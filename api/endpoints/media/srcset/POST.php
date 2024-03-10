<?php

	use Reflect\Path;
	use Reflect\Response;

	use function Reflect\Call;
	use Reflect\Request\Method;

	use VLW\API\Databases\VLWdb\VLWdb;
	use VLW\API\Databases\VLWdb\Models\MediaSrcset\MediaSrcsetModel;

	require_once Path::root("src/databases/VLWdb.php");
	require_once Path::root("src/databases/models/Media.php");
	require_once Path::root("src/databases/models/MediaSrcset.php");

	class POST_MediaSrcset extends VLWdb {
		public function __construct() {
			parent::__construct();
		}

		// # Responses

		// Return a 503 Service Unavailable error if something went wrong with the database call
		private function resp_database_error(): Response {
			return new Response("Failed to get work data, please try again later", 503);
		}

		public function main(): Response {
			// Generate a random UUID for this srcset
			$id = parent::gen_uuid4();

			// Ensure an srcset with the generated id doesn't exist, although it shouldn't realistically ever happen
			$srcset_existing = Call("media/srcset?id={$id}", Method::GET);
			if ($srcset_existing->code !== 404) {
				// Wow a UUID4 collision... buy a lottery ticket
				if ($srcset_existing->code === 200) {
					return $this->main();
				}

				// Failed to get srcset
				return new Response("Something went wrong when checking if the srcset exists", 500);
			}

			// Create new srcset entity
			$insert = $this->db->for(MediaSrcsetModel::TABLE)
				->insert([
					MediaSrcsetModel::ID->value => $id
				]);
			
			// Return created srcset id if successful
			return $insert
				? new Response($id, 201)
				: $this->resp_database_error();
		}
	}