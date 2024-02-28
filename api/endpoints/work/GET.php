<?php


	use Reflect\Path;
	use Reflect\Response;
	use ReflectRules\Type;
	use ReflectRules\Rules;
	use ReflectRules\Ruleset;

	use VLW\API\Databases\VLWdb\VLWdb;
	use VLW\API\Databases\VLWdb\Models\Work\WorkModel;
	use VLW\API\Databases\VLWdb\Models\Work\WorkTagsModel;
	use VLW\API\Databases\VLWdb\Models\Work\WorkActionsModel;

	require_once Path::root("src/databases/VLWdb.php");
	require_once Path::root("src/databases/models/Work.php");
	require_once Path::root("src/databases/models/WorkTags.php");
	require_once Path::root("src/databases/models/WorkActions.php");

	class GET_Work extends VLWdb {
		protected Ruleset $ruleset;

		public function __construct() {
			parent::__construct();
			$this->ruleset = new Ruleset(strict: true);

			$this->ruleset->GET([
				(new Rules("id"))
					->type(Type::STRING)
					->min(1)
					->max(255)
					->default(null)
			]);
		}

		// # Helper methods

		private function fetch_row_tags(string $id): array {
			$resp = $this->db->for(WorkTagsModel::TABLE)
				->where([WorkTagsModel::ANCHOR->value => $id])
				->select(WorkTagsModel::NAME->value);

			return parent::is_mysqli_result($resp) ? $resp->fetch_all(MYSQLI_ASSOC) : [];
		}

		private function fetch_row_actions(string $id): array {
			$resp = $this->db->for(WorkActionsModel::TABLE)
				->where([WorkActionsModel::ANCHOR->value => $id])
				->select([
					WorkActionsModel::DISPLAY_TEXT->value,
					WorkActionsModel::HREF->value,
					WorkActionsModel::CLASS_LIST->value,
					WorkActionsModel::EXTERNAL->value
				]);

			return parent::is_mysqli_result($resp) ? $resp->fetch_all(MYSQLI_ASSOC) : [];
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

		private function resp_item_details(string $id): Response {
			$resp = $this->db->for(WorkModel::TABLE)
				->where([WorkModel::ID->value => $id])
				->limit(1)
				->select("*");

			// Bail out if something went wrong retrieving rows from the database
			if (!parent::is_mysqli_result($resp)) {
				return $this->resp_database_error();
			}

			return $resp->num_rows === 1
				? new Response($resp->fetch_assoc())
				: new Response("No entity with id '{$id}' was found", 404);
		}

		public function main(): Response {
			// Bail out if request validation failed
			if (!$this->ruleset->is_valid()) {
				return $this->resp_rules_invalid();
			}

			// Return details about a specific item by id
			if (!empty($_GET["id"])) {
				return $this->resp_item_details($_GET["id"]);
			}

			$resp = $this->db->for(WorkModel::TABLE)
				->order([WorkModel::DATE_TIMESTAMP_CREATED->value => "DESC"])
				->select("*");

			// Bail out if something went wrong retrieving rows from the database
			if (!parent::is_mysqli_result($resp)) {
				return $this->resp_database_error();
			}

			// Resolve tags and actions for each row
			$rows = [];
			while ($row = $resp->fetch_assoc()) {
				$row["tags"] = $this->fetch_row_tags($row["id"]);
				$row["actions"] = $this->fetch_row_actions($row["id"]);

				$rows[] = $row;
			}

			return new Response($rows);
		}
	}