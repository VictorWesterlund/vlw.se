<?php

	use Reflect\Path;
	use Reflect\Response;
	use ReflectRules\Type;
	use ReflectRules\Rules;
	use ReflectRules\Ruleset;

	use Reflect\Method;
	use function Reflect\Call;

	use VLW\API\Databases\VLWdb\VLWdb;
	use VLW\API\Databases\VLWdb\Models\Media\MediaModel;
	use VLW\API\Databases\VLWdb\Models\Media\MediaTypeEnum;

	require_once Path::root("src/databases/VLWdb.php");
	require_once Path::root("src/databases/models/Media.php");

	class POST_Media extends VLWdb {
		protected Ruleset $ruleset;

		public function __construct() {
			parent::__construct();
			$this->ruleset = new Ruleset(strict: true);

			$this->ruleset->POST([
				(new Rules(MediaModel::ID->value))
					->type(Type::STRING)
					->min(1)
					->max(parent::MYSQL_VARCHAR_MAX_LENGTH)
					->default(parent::gen_uuid4()),

				(new Rules(MediaModel::NAME->value))
					->type(Type::STRING)
					->min(1)
					->max(parent::MYSQL_VARCHAR_MAX_LENGTH)
					->default(null),

				(new Rules(MediaModel::TYPE->value))
					->type(Type::ENUM, MediaTypeEnum::values())
					->default(null),

				(new Rules(MediaModel::EXTENSION->value))
					->type(Type::STRING)
					->min(3)
					->max(parent::MYSQL_VARCHAR_MAX_LENGTH)
					->default(null),

				(new Rules(MediaModel::MIME->value))
					->type(Type::STRING)
					->min(3)
					->max(parent::MYSQL_VARCHAR_MAX_LENGTH)
					->default(null),

				(new Rules(MediaModel::SRCSET->value))
					->type(Type::STRING)
					->min(1)
					->max(parent::MYSQL_VARCHAR_MAX_LENGTH)
					->default(null)
			]);
		}

		// # Helper methods

		// Returns true if an srcset exists for provided key
		private static function media_srcset_exists(): bool {
			// No srcet get parameter has been set
			if (empty($_POST[MediaModel::SRCSET->value])) {
				return true;
			}

			// Check if the provided srcset exists by calling the srcset endpoint
			return Call("media/srcset?id={$_POST[MediaModel::SRCSET->value]}", Method::GET)->ok;
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

			// Bail out if an srcset doesn't exist
			if (!self::media_srcset_exists()) {
				return new Response("No media srcset exists with id '{$_POST[MediaModel::SRCSET->value]}'", 404);
			}

			$insert = $this->db->for(MediaModel::TABLE)
				->insert([
					MediaModel::ID->value                     => $_POST[MediaModel::ID->value],
					MediaModel::NAME->value                   => $_POST[MediaModel::NAME->value],
					MediaModel::MIME->value                   => $_POST[MediaModel::MIME->value],
					// Strip dots from extension string if set
					MediaModel::EXTENSION->value              => $_POST[MediaModel::EXTENSION->value]
																	? str_replace(".", "", $_POST[MediaModel::EXTENSION->value])
																	: null,
					MediaModel::SRCSET->value                 => $_POST[MediaModel::SRCSET->value],
					MediaModel::DATE_TIMESTAMP_CREATED->value => time()
				]);

			// Return media id if insert was successful
			return $insert
				? new Response($_POST[MediaModel::ID->value], 201)
				: $this->resp_database_error();
		}
	}