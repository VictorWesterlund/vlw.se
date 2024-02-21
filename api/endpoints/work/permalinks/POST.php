<?php


	use Reflect\Path;
	use Reflect\Response;
	use ReflectRules\Type;
	use ReflectRules\Rules;
	use ReflectRules\Ruleset;

	use function Reflect\Call;
	use Reflect\Request\Method;

	use VLW\API\Databases\VLWdb\VLWdb;
	use VLW\API\Databases\VLWdb\Models\WorkPermalinks\WorkPermalinksModel;

	require_once Path::root("src/databases/VLWdb.php");
	require_once Path::root("src/databases/models/WorkPermalinks.php");

	class POST_WorkPermalinks extends VLWdb {
		protected Ruleset $ruleset;

		public function __construct() {
			parent::__construct();
			$this->ruleset = new Ruleset(strict: true);

			$this->ruleset->POST([
				(new Rules("slug"))
					->required()
					->type(Type::STRING)
					->min(1)
					->max(255),

				(new Rules("anchor"))
					->required()
					->type(Type::STRING)
					->min(1)
					->max(255)
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

			// Check if an entity exists with slug
			$existing_entity = Call("work?id={$_POST["slug"]}", Method::GET);
			// Response is not 404 (Not found) so we can't create the entity
			if ($existing_entity->code !== 200) {
				// Response is not a valid entity, something went wrong
				if ($existing_entity->code !== 404) {
					return $this->resp_database_error();
				}

				// Return 402 Conflict
				return new Response("No work entity with id '{$_POST["slug"]}' was found to permalink", 404);
			}

			// Attempt to create new entity
			$insert = $this->db->for(WorkPermalinksModel::TABLE)
				->insert([
					WorkPermalinksModel::SLUG->value                   => $_POST["slug"],
					WorkPermalinksModel::ANCHOR->value                 => $_POST["anchor"],
					WorkPermalinksModel::DATE_TIMESTAMP_CREATED->value => time(),
				]);

			// Return 201 Created and entity slug as body if insert was successful
			return $insert === true ? new Response($_POST["slug"], 201) : $this->resp_database_error();
		}
	}