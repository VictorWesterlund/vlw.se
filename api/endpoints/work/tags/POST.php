<?php

	use Reflect\Path;
	use Reflect\Response;
	use ReflectRules\Type;
	use ReflectRules\Rules;
	use ReflectRules\Ruleset;

	use Reflect\Method;
	use function Reflect\Call;

	use VLW\API\Databases\VLWdb\VLWdb;
	use VLW\API\Databases\VLWdb\Models\Work\WorkTagsModel;
	use VLW\API\Databases\VLWdb\Models\Work\WorkTagsNameEnum;

	require_once Path::root("src/databases/VLWdb.php");
	require_once Path::root("src/databases/models/WorkTags.php");

	class POST_WorkTags extends VLWdb {
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

			$this->ruleset->POST([
				(new Rules(WorkTagsModel::NAME->value))
					->required()
					->type(Type::ENUM, WorkTagsNameEnum::names())
			]);
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

			// Ensure an entity with the provided id exists
			$entity = Call("work?id={$_GET["id"]}", Method::GET);
			if ($entity->code !== 200) {
				// Response from endpoint is not 404, something went wrong
				if ($entity->code !== 404) {
					return $this->resp_database_error();
				}

				return new Response("No entity with id '{$_GET["id"]}' was found", 404);
			}

			// Ensure the tag does not already exist for entity
			$existing_tag = $this->db->for(WorkTagsModel::TABLE)
				->where([
					WorkTagsModel::ANCHOR->value => $_GET["id"],
					WorkTagsModel::NAME->value   => $_POST["name"]
				])
				->select(null);

			// Bail out if this tag already exists
			if ($existing_tag->num_rows !== 0) {
				return new Response("Tag '{$_POST["name"]}' is already set on entity id '{$_GET["id"]}'", 402);
			}

			// Attempt to create tag for entity
			$insert = $this->db->for(WorkTagsModel::TABLE)
				->insert([
					WorkTagsModel::ANCHOR->value => $_GET["id"],
					WorkTagsModel::NAME->value   => $_POST["name"]
				]);

			// Return 201 Created and entity id as body if insert was successful
			return $insert === true ? new Response($_GET["id"], 201) : $this->resp_database_error();
		}
	}