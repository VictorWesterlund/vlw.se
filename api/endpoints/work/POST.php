<?php

	use Reflect\Path;
	use Reflect\Response;
	use ReflectRules\Type;
	use ReflectRules\Rules;
	use ReflectRules\Ruleset;

	use function Reflect\Call;
	use Reflect\Request\Method;

	use VLW\API\Databases\VLWdb\VLWdb;
	use VLW\API\Databases\VLWdb\Models\Work\WorkModel;
	use VLW\API\Databases\VLWdb\Models\WorkPermalinks\WorkPermalinksModel;

	require_once Path::root("src/databases/VLWdb.php");
	require_once Path::root("src/databases/models/Work.php");
	require_once Path::root("src/databases/models/WorkPermalinks.php");

	class POST_Work extends VLWdb {
		const MYSQL_TEXT_MAX_LENGTH = 65538;
		const MYSQL_INT_MAX_LENGHT = 2147483647;

		protected Ruleset $ruleset;

		public function __construct() {
			parent::__construct();
			$this->ruleset = new Ruleset(strict: true);

			$this->ruleset->POST([
				(new Rules(WorkModel::TITLE->value))
					->type(Type::STRING)
					->min(3)
					->max(255)
					->default(null),

				(new Rules(WorkModel::SUMMARY->value))
					->type(Type::STRING)
					->min(1)
					->max(self::MYSQL_TEXT_MAX_LENGTH)
					->default(null),

				(new Rules(WorkModel::DATE_TIMESTAMP_CREATED->value))
					->type(Type::NUMBER)
					->min(1)
					->max(self::MYSQL_INT_MAX_LENGHT)
					->default(null)
			]);
		}

		// Generate a slug URL from string
		private static function gen_slug(string $input): string {
			return strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $input)));
		}

		// Create permalink for entity slug
		private function create_permalink(string $slug): bool {
			$create = Call("work/permalinks", Method::POST, [
				WorkPermalinksModel::SLUG->value   => $slug,
				WorkPermalinksModel::ANCHOR->value => $slug
			]);

			return $create->ok;
		}

		// # Responses

		// Return 422 Unprocessable Content error if request validation failed 
		private function resp_rules_invalid(): Response {
			return new Response($this->ruleset->get_errors(), 422);
		}

		// Return a 503 Service Unavailable error if something went wrong with the database call
		private function resp_database_error(): Response {
			return new Response("Failed to get work data, please try again later", 503);
		}

		public function main(): Response {
			// Bail out if request validation failed
			if (!$this->ruleset->is_valid()) {
				return $this->resp_rules_invalid();
			}

			// Generate URL slug from title text or UUID if undefined
			$slug = !empty($_POST["title"]) ? self::gen_slug($_POST["title"]) : parent::gen_uuid4();

			// Check if an entity already exists with slugified title from GET endpoint
			$existing_entity = Call("work?id={$slug}", Method::GET);
			// Response is not 404 (Not found) so we can't create the entity
			if ($existing_entity->code !== 404) {
				// Response is not a valid entity, something went wrong
				if ($existing_entity->code !== 200) {
					return $this->resp_database_error();
				}

				// Return 402 Conflict
				return new Response("Entity with id '{$slug}' already exists", 402);
			}

			// Get created timestamp from payload or use current time if not specified
			$created_timestamp = $_POST[WorkModel::DATE_TIMESTAMP_CREATED->value] 
				? $_POST[WorkModel::DATE_TIMESTAMP_CREATED->value]
				: time();

			// Attempt to create new entity
			$insert = $this->db->for(WorkModel::TABLE)
				->insert([
					WorkModel::ID->value                      => $slug,
					WorkModel::TITLE->value                   => $_POST["title"],
					WorkModel::SUMMARY->value                 => $_POST["summary"],
					WorkModel::IS_LISTABLE->value             => true,
					WorkModel::IS_READABLE->value             => true,
					WorkModel::DATE_YEAR->value               => date("Y", $created_timestamp),
					WorkModel::DATE_MONTH ->value             => date("n", $created_timestamp),
					WorkModel::DATE_DAY->value                => date("j", $created_timestamp),
					WorkModel::DATE_TIMESTAMP_MODIFIED->value => null,
					WorkModel::DATE_TIMESTAMP_CREATED->value  => $created_timestamp,
				]);

			// Bail out if insert failed
			if (!$insert) {
				return $this->resp_database_error();
			}

			// Create permalink for new entity
			if (!$this->create_permalink($slug)) {
				// Rollback created entity if permalink creation failed
				Call("work", Method::DELETE, [WorkModel::ID->value => $slug]);

				return new Response("Failed to create permalink", 500);
			}

			// Return 201 Created and entity slug as body
			return new Response($slug, 201);
		}
	}