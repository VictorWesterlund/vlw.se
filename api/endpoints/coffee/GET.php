<?php


	use Reflect\Path;
	use Reflect\Response;

	use VLW\API\Databases\VLWdb\VLWdb;
	use VLW\API\Databases\VLWdb\Models\Coffee\CoffeeModel;

	require_once Path::root("src/databases/VLWdb.php");
	require_once Path::root("src/databases/models/Coffee.php");

	class GET_Coffee extends VLWdb {
		const LIST_LIMIT = 20;

		public function __construct() {
			parent::__construct();
		}

		// Return a 503 Service Unavailable error if something went wrong with the database call
		private function resp_database_error(): Response {
			return new Response("Failed to get work data, please try again later", 503);
		}

		public function main(): Response {
			// Get the last LIST_LIMIT coffees from the database
			$resp = $this->db->for(CoffeeModel::TABLE)
				->order([CoffeeModel::DATE_TIMESTAMP_CREATED->value => "DESC"])
				->limit(self::LIST_LIMIT)
				->select([
					CoffeeModel::ID->value,
					CoffeeModel::DATE_TIMESTAMP_CREATED->value
				]);

			return parent::is_mysqli_result($resp)
				? new Response($resp->fetch_all(MYSQLI_ASSOC))
				: $this->resp_database_error();
		}
	}