<?php

	use Reflect\Path;
	use Reflect\Response;
	use ReflectRules\Type;
	use ReflectRules\Rules;
	use ReflectRules\Ruleset;

	use Reflect\Method;
	use function Reflect\Call;

	use VLW\API\Databases\VLWdb\VLWdb;
	use VLW\API\Databases\VLWdb\Models\Work\WorkActionsModel;

	require_once Path::root("src/databases/VLWdb.php");
	require_once Path::root("src/databases/models/WorkActions.php");

	class DELETE_WorkActions extends VLWdb {
		protected Ruleset $ruleset;

		public function __construct() {
			parent::__construct();
			$this->ruleset = new Ruleset(strict: true);

			$this->ruleset->POST([
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
			return new Response("Failed to get work data, please try again later", 503);
		}

		public function main(): Response {
			// Bail out if request validation failed
			if (!$this->ruleset->is_valid()) {
				return $this->resp_rules_invalid();
			}

			// Ensure the action exists by id
			$existing_action = $this->db->for(WorkActionsModel::TABLE)
				->where([
					WorkActionsModel::ID->value => $_POST["id"]
				])
				->select(null);

			// Return idempotent deletion if the action does not exist
			if ($existing_action->num_rows === 0) {
				return new Response($_POST["id"]);
			}

			// Attempt to delete action by id
			$delete = $this->db->for(WorkActionsModel::TABLE)
				->delete([
					WorkActionsModel::ID->value => $_POST["id"]
				]);

			// Return 201 Created and entity id as body if insert was successful
			return $delete === true ? new Response($_POST["id"], 201) : $this->resp_database_error();
		}
	}