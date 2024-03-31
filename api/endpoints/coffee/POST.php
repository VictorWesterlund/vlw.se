<?php

	use Reflect\Path;
	use Reflect\Response;

	use VLW\API\Databases\VLWdb\VLWdb;
	use VLW\API\Databases\VLWdb\Models\Coffee\CoffeeModel;

	require_once Path::root("src/databases/VLWdb.php");
	require_once Path::root("src/databases/models/Coffee.php");

	class POST_Coffee extends VLWdb {
		public function __construct() {
			parent::__construct();
		}

		// Return a 503 Service Unavailable error if something went wrong with the database call
		private function resp_database_error(): Response {
			return new Response("Failed to record coffee! Ugh please take a note somewhere else", 503);
		}

		public function main(): Response {
			// Generate UUID for entity
			$id = parent::gen_uuid4();

			// Attempt to create new entity
			$insert = $this->db->for(CoffeeModel::TABLE)
				->insert([
					CoffeeModel::ID->value                      => $id,
					CoffeeModel::DATE_TIMESTAMP_CREATED->value  => time(),
				]);

			// Return 201 Created and entity id if successful
			return $insert ? new Response($id, 201) : $this->resp_database_error();
		}
	}