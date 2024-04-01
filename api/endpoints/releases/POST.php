<?php

	use Reflect\Path;
	use Reflect\Response;
	use ReflectRules\Type;
	use ReflectRules\Rules;
	use ReflectRules\Ruleset;

	use Reflect\Method;
	use function Reflect\Call;

	use VLW\API\Databases\VLWdb\Models\Work\WorkModel;
	use VLW\API\Databases\VLWdb\Models\Work\WorkTagsModel;
	use VLW\API\Databases\VLWdb\Models\Work\WorkTagsNameEnum;
	use VLW\API\Databases\VLWdb\Models\Work\WorkActionsModel;

	require_once Path::root("src/databases/models/Work.php");
	require_once Path::root("src/databases/models/WorkTags.php");
	require_once Path::root("src/databases/models/WorkActions.php");

	// "Virtual" database model for the POST request body since we're not writing to a db directly
	enum ReleasesPostModel: string {
		case GITHUB_USER = "user";
		case GITHUB_REPO = "repo";
		case GITHUB_TAG  = "tag";
	}

	class POST_Releases {
		// Base URL of the GitHub API (no tailing slash)
		const GITHUB_API = "https://api.github.com";

		const REGEX_HANDLE = "/@[\w]+/";
		const REGEX_URL    = "/\b(?:https?):\/\/\S+\b/";

		protected Ruleset $ruleset;

		protected CurlHandle $curl;

		public function __construct() {
			$this->ruleset = new Ruleset(strict: true);

			$this->ruleset->POST([
				(new Rules(ReleasesPostModel::GITHUB_USER->value))
					->required()
					->type(Type::STRING)
					->min(1),

				(new Rules(ReleasesPostModel::GITHUB_REPO->value))
					->required()
					->type(Type::STRING)
					->min(1),

				(new Rules(ReleasesPostModel::GITHUB_TAG->value))
					->required()
					->type(Type::STRING)
					->type(Type::NUMBER)
					->min(1)
			]);

			$this->curl = curl_init();

			curl_setopt($this->curl, CURLOPT_USERAGENT, $_ENV["github"]["user_agent"]);
			curl_setopt($this->curl, CURLOPT_HEADER, true);
			curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($this->curl, CURLOPT_HTTPHEADER, [
				"Accept"               => "application/vnd.github+json",
				"Authorization"        => "token {$_ENV["github"]["api_key"]}",
				"X-GitHub-Api-Version" => "2022-11-28"
			]);
		}

		// # GitHub

		// Generate HTML from a GitHub "auto-generate" release body
		protected static function gh_auto_release_md_to_html(string $md): string {
			$output = "";

			// Parse each line of markdown
			$lines = explode(PHP_EOL, $md);

			foreach ($lines as $i => $line) {
				// Ignore header line from releases
				if ($i < 1) continue;

				// Replace all URLs with HTMLAnchor tags, they will be PRs
				$links = [];
				preg_match_all(self::REGEX_URL, $line, $links, PREG_UNMATCHED_AS_NULL);
				foreach ($links as $i => $link) {
					if (empty($link)) continue;

					// Last crumb from link pathname will be the PR id
					$pr_id = explode("/", $link[$i]);
					$pr_id = end($pr_id);

					$line = str_replace($link, "<a href='{$link[$i]}'>{$pr_id}</a>", $line);
				}

				// Replace all at-handles with links to GitHub user profiles
				$handles = [];
				preg_match_all(self::REGEX_HANDLE, $line, $handles, PREG_UNMATCHED_AS_NULL);
				foreach ($handles as $i => $handle) {
					if (empty($handle)) continue;

					// GitHub user URL without the "@"
					$url = "https://github.com/" . substr($handle[$i], 1);

					$line = str_replace($handle, "<a href='{$url}'>{$handle[$i]}</a>", $line);
				}

				$output .= "<p>{$line}</p>";
			}

			return $output;
		}

		// Return fully qualified URL to GitHub API releases endpoint
		private static function get_url(): string {
			return implode("/", [
				self::GITHUB_API,
				"repos",
				$_POST[ReleasesPostModel::GITHUB_USER->value],
				$_POST[ReleasesPostModel::GITHUB_REPO->value],
				"releases",
				"tags",
				$_POST[ReleasesPostModel::GITHUB_TAG->value],
			]);
		}

		// Fetch release information from GitHub API
		private function fetch_release_data(): array {
			$url = self::get_url();
			curl_setopt($this->curl, CURLOPT_URL, self::get_url());

			$resp = curl_exec($this->curl);

			$header_size = curl_getinfo($this->curl, CURLINFO_HEADER_SIZE);
			$header = substr($resp, 0, $header_size);
			$body = substr($resp, $header_size);

			return json_decode($body, true);
		}

		// # Sup

		private function create_link_to_release_page(string $id, string $href): Response {
			return Call("work/actions?id={$id}", Method::POST, [
				WorkActionsModel::DISPLAY_TEXT->value => "Release details",
				WorkActionsModel::HREF->value         => $href,
				WorkActionsModel::EXTERNAL->value     => true
			]);
		}

		// Create a tag for entity
		private function create_tag(string $id, WorkTagsNameEnum $tag): Response {
			return Call("work/tags?id={$id}", Method::POST, [
				// Set "RELEASE" tag on new entity
				WorkTagsModel::NAME->value => $tag->value
			]);
		}

		// # Responses

		// Return 422 Unprocessable Content error if request validation failed 
		private function resp_rules_invalid(): Response {
			return new Response($this->ruleset->get_errors(), 422);
		}

		public function main(): Response {
			// Bail out if request validation failed
			if (!$this->ruleset->is_valid()) {
				return $this->resp_rules_invalid();
			}

			$data = $this->fetch_release_data();
			if (!$data) {
				return new Response("Failed to fetch release data", 500);
			}

			
			// Transform repo name to lowercase for summary title
			$title = strtolower($_POST["repo"]);

			// Use repo name and tag name as heading for summary
			$summary = "<h3>Release {$title}@{$data["name"]}</h3>";
			// Append HTML-ified release notes from GitHub to summary
			$summary .= self::gh_auto_release_md_to_html($data["body"]);

			$date_published = new \DateTime($data["published_at"], new \DateTimeZone("UTC"));

			// Create work entity
			$work_entity = Call("work", Method::POST, [
				WorkModel::SUMMARY->value                => $summary,
				// Convert time created to Unix timestamp for work endpoint
				WorkModel::DATE_TIMESTAMP_CREATED->value => $date_published->format("U"),
			]);

			// Bail out if creating the work entity failed
			if (!$work_entity->ok) {
				return new Response("Failed to create work entity for release", 500);
			}

			$work_entity_id = $work_entity->output();

			// Create entity tags for release
			$tags = [
				WorkTagsNameEnum::VLW,
				WorkTagsNameEnum::RELEASE
			];
			foreach ($tags as $tag) {
				// Create entity tag for release or exit if failed to create
				if (!$this->create_tag($work_entity_id, $tag)->ok) {
					return new Response("Failed to create {$tag->name} tag for release entity", 500);
				}
			}

			// Create link to release page on GitHub
			if (!$this->create_link_to_release_page($work_entity_id, $data["html_url"])) {
				return new Response("Failed to create link to release page on GitHub", 500);
			}

			return new Response($work_entity_id, 201);
		}
	}