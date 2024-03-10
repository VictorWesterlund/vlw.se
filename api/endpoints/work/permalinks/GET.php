<?php


	use Reflect\Path;
	use Reflect\Response;
	use ReflectRules\Type;
	use ReflectRules\Rules;
	use ReflectRules\Ruleset;

	use VLW\API\Databases\VLWdb\VLWdb;
	use VLW\API\Databases\VLWdb\Models\WorkPermalinks\WorkPermalinksModel;

	require_once Path::root("src/databases/VLWdb.php");
	require_once Path::root("src/databases/models/WorkPermalinks.php");

	class GET_WorkPermalinks extends VLWdb {
		protected Ruleset $ruleset;

		public function __construct() {
			parent::__construct();
			$this->ruleset = new Ruleset(strict: true);

			$this->ruleset->GET([
				(new Rules("id"))
					->required()
					->type(Type::STRING)
					->min(1)
					->max(parent::MYSQL_VARCHAR_MAX_LENGTH)
			]);
		}

		// # Responses

		// Return 422 Unprocessable Content error if request validation failed 
		private function resp_rules_invalid(): Response {
			return new Response($this->ruleset->get_errors(), 422);
		}

		// Return a 503 Service Unavailable error if something went wrong with the database call
		private function resp_database_error(): Response {
			return new Response("Failed to resolve permalink, please try again later", 503);
		}

		public function main(): Response {
			// Bail out if request validation failed
			if (!$this->ruleset->is_valid()) {
				return $this->resp_rules_invalid();
			}

			// Get all anchors that match the requested slug
			$resolve = $this->db->for(WorkPermalinksModel::TABLE)
				->where([WorkPermalinksModel::SLUG->value => $_GET["id"]])
				->select(WorkPermalinksModel::ANCHOR->value);

			// Return array of all matched work table ids. Or empty array if none found
			return parent::is_mysqli_result($resolve)
				? new Response(array_column($resolve->fetch_all(MYSQLI_ASSOC), WorkPermalinksModel::ANCHOR->value))
				: $this->resp_database_error();
		}
	}