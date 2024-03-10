<?php

	use Reflect\Path;
	use Reflect\Response;
	use ReflectRules\Type;
	use ReflectRules\Rules;
	use ReflectRules\Ruleset;

	use function Reflect\Call;
	use Reflect\Request\Method;

	use VLW\API\Databases\VLWdb\VLWdb;
	use VLW\API\Databases\VLWdb\Models\Work\WorkActionsModel;

	require_once Path::root("src/databases/VLWdb.php");
	require_once Path::root("src/databases/models/WorkActions.php");

	class POST_WorkActions extends VLWdb {
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
				(new Rules(WorkActionsModel::DISPLAY_TEXT->value))
					->required()
					->type(Type::STRING)
					->min(1)
					->max(parent::MYSQL_VARCHAR_MAX_LENGTH),

				(new Rules(WorkActionsModel::HREF->value))
					->required()
					->type(Type::STRING)
					->type(Type::NULL)
					->min(1)
					->max(parent::MYSQL_VARCHAR_MAX_LENGTH),

				(new Rules(WorkActionsModel::CLASS_LIST->value))
					->type(Type::ARRAY)
					->min(1)
					->max(4)
					->default([]),

				(new Rules(WorkActionsModel::EXTERNAL->value))
					->type(Type::BOOLEAN)
					->default(false)
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

			// Attempt to create action for entity
			$insert = $this->db->for(WorkActionsModel::TABLE)
				->insert([
					WorkActionsModel::ID->value           => parent::gen_uuid4(),
					WorkActionsModel::ANCHOR->value       => $_GET["id"],
					WorkActionsModel::DISPLAY_TEXT->value => $_POST[WorkActionsModel::DISPLAY_TEXT->value],
					WorkActionsModel::HREF->value         => $_POST[WorkActionsModel::HREF->value],
					WorkActionsModel::CLASS_LIST->value   => implode(",", $_POST[WorkActionsModel::CLASS_LIST->value]),
					WorkActionsModel::EXTERNAL->value     => $_POST[WorkActionsModel::EXTERNAL->value],
				]);

			// Return 201 Created and entity id as body if insert was successful
			return $insert === true ? new Response($_GET["id"], 201) : $this->resp_database_error();
		}
	}