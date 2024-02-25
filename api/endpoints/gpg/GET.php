<?php


	use Reflect\Path;
	use Reflect\Response;
	use ReflectRules\Type;
	use ReflectRules\Rules;
	use ReflectRules\Ruleset;

	use VLW\API\Databases\VLWdb\VLWdb;
	use VLW\API\Databases\VLWdb\Models\Gpg\GpgModel;

	require_once Path::root("src/databases/VLWdb.php");
	require_once Path::root("src/databases/models/Gpg.php");

	class GET_Gpg extends VLWdb {
		private const GPG_FILE_NAME = "gpg_vlw.txt";

		protected Ruleset $ruleset;

		public function __construct() {
			parent::__construct();
			$this->ruleset = new Ruleset(strict: true);

			$this->ruleset->GET([
				(new Rules("download"))
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

			// Get GPG key as plaintext
			$resp = $this->db->for(GpgModel::TABLE)
				->limit(1)
				->select(GpgModel::TEXT->value);

			// Bail out if something went wrong retrieving rows from the database
			if (!parent::is_mysqli_result($resp)) {
				return $this->resp_database_error();
			}

			// Download GPG key as text binary
			if ($_GET["download"]) {
				header("Content-Disposition: attachment; filename=" . self::GPG_FILE_NAME);
			}

			// Return GPG key
			return new Response($resp->fetch_assoc()[GpgModel::TEXT->value], 200, "text/plain");
		}
	}