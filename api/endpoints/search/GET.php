<?php

	use Reflect\Call;
	use Reflect\Path;
	use Reflect\Response;
	use ReflectRules\Type;
	use ReflectRules\Rules;
	use ReflectRules\Ruleset;

	use VLW\API\Endpoints;

	use VLW\API\Databases\VLWdb\VLWdb;
	use VLW\API\Databases\VLWdb\Models\Work\WorkModel;
	use VLW\API\Databases\VLWdb\Models\Work\WorkActionsModel;

	require_once Path::root("src/databases/VLWdb.php");
	require_once Path::root("src/databases/models/Work.php");
	require_once Path::root("src/databases/models/WorkActions.php");

	class GET_Search extends VLWdb {
		const GET_QUERY = "q";

		protected Ruleset $ruleset;

		public function __construct() {
			parent::__construct();
			$this->ruleset = new Ruleset(strict: true);

			$this->ruleset->GET([
				(new Rules(self::GET_QUERY))
					->required()
					->type(Type::STRING)
					->min(2)
					->max(parent::MYSQL_VARCHAR_MAX_LENGTH)
			]);
		}

		// Return an SQL string from array for use in prepared statements
		private static function array_to_wildcard_sql(array $columns): string {
			$sql = array_map(fn(string $column): string => "{$column} LIKE CONCAT('%', ?, '%')", $columns);
			
			return implode(" OR ", $sql);
		}

		// Return chained AND statements from array for use in prepared statements
		private static function array_to_and_statement(array $keys): string {
			$sql = array_map(fn(string $k): string => "{$k} = ?", $keys);

			return implode(" AND ", $sql);
		}

		// Wildcard search columns in table with query string from query string
		// This has to be implemented manually until "libmysqldriver/MySQL" supports wildcard SELECT 
		private function search(string $table, array $columns, array $conditions = null): array {
			// Create CSV from columns array
			$columns_concat = implode(",", $columns);

			// Create SQL LIKE wildcard statement for each column.
			$where = self::array_to_wildcard_sql($columns);

			// Create array of values from query string for each colum
			$values = array_fill(0, count($columns), $_GET[self::GET_QUERY]);

			if ($conditions) {
				$conditions_sql = self::array_to_and_statement(array_keys($conditions));

				// Wrap positive where statements and prepare new group of conditions
				// WHERE (<search_terms>) AND (<conditions>)
				$where = "({$where}) AND ({$conditions_sql})";

				// Append values from conditions statements to prepared statement
				array_push($values, ...array_values($conditions));
			}

			// Order the rows by the array index of $colums received
			$rows = $this->db->exec("SELECT {$columns_concat} FROM {$table} WHERE {$where} ORDER BY {$columns_concat}", $values);
			// Return results as assoc or empty array
			return parent::is_mysqli_result($rows) ? $rows->fetch_all(MYSQLI_ASSOC) : [];
		}

		// Search work table
		private function search_work(): array {
			$search = [
				WorkModel::TITLE->value,
				WorkModel::SUMMARY->value,
				WorkModel::DATE_TIMESTAMP_CREATED->value,
				WorkModel::ID->value
			];

			$conditions = [
				WorkModel::IS_LISTABLE->value => true
			];

			$results = $this->search(WorkModel::TABLE, $search, $conditions);

			foreach ($results as &$result) {
				$result["actions"] = (new Call(Endpoints::WORK_ACTIONS->value))
					->params([WorkActionsModel::ANCHOR->value => $result[WorkModel::ID->value]])
					->get()->output();
			}

			return $results;
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

			// Get search results for each category
			$categories = [
				WorkModel::TABLE => $this->search_work()
			];

			// Count total number of results from all categories
			$total_num_results = 0;
			foreach (array_values($categories) as $results) {
				$total_num_results += count($results);
			}

			return new Response([
				"query"             => $_GET[self::GET_QUERY],
				"results"           => $categories,
				"total_num_results" => $total_num_results
			]);
		}
	}