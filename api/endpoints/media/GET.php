<?php

	use Reflect\Path;
	use Reflect\Response;
	use ReflectRules\Type;
	use ReflectRules\Rules;
	use ReflectRules\Ruleset;

	use VLW\API\Databases\VLWdb\VLWdb;
	use VLW\API\Databases\VLWdb\Models\Media\MediaModel;

	use victorwesterlund\xEnum;

	require_once Path::root("src/databases/VLWdb.php");
	require_once Path::root("src/databases/models/Media.php");

	enum MediaDispositionEnum: string {
		use xEnum;

		case METADATA  = "metadata";
		case INLINE    = "inline";
		case DOWNLOAD  = "download";
	}

	class GET_Media extends VLWdb {
		const GET_DISPOSITION_KEY = "disposition";

		protected Ruleset $ruleset;

		public function __construct() {
			parent::__construct();
			$this->ruleset = new Ruleset(strict: true);

			$this->ruleset->GET([
				(new Rules(MediaModel::ID->value))
					->required()
					->type(Type::STRING)
					->min(1)
					->max(parent::MYSQL_VARCHAR_MAX_LENGTH),

				(new Rules(self::GET_DISPOSITION_KEY))
					->type(Type::ENUM, MediaDispositionEnum::values())
					->default(MediaDispositionEnum::METADATA->value)
			]);
		}

		// # Helper methods

		private function fetch_srcset(string $id): array {
			$resp = $this->db->for(WorkTagsModel::TABLE)
				->where([WorkTagsModel::ANCHOR->value => $id])
				->select(WorkTagsModel::NAME->value);

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

		public function main(): Response {
			// Bail out if request validation failed
			if (!$this->ruleset->is_valid()) {
				return $this->resp_rules_invalid();
			}

			$resp = $this->db->for(MediaModel::TABLE)
				->where([MediaModel::ID->value => $_GET[MediaModel::ID->value]])
				->select([
					MediaModel::ID->value,
					MediaModel::NAME->value,
					MediaModel::TYPE->value,
					MediaModel::MIME->value,
					MediaModel::EXTENSION->value,
					MediaModel::SRCSET->value,
					MediaModel::DATE_TIMESTAMP_CREATED->value,
				]);

			// Bail out if something went wrong retrieving rows from the database
			if (!parent::is_mysqli_result($resp)) {
				return $this->resp_database_error();
			}

			$media = $resp->fetch_assoc();
			$test = true;
		}
	}