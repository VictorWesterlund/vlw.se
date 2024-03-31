<?php

	use Reflect\Path;
	use Reflect\Response;
	use ReflectRules\Type;
	use ReflectRules\Rules;
	use ReflectRules\Ruleset;

	use VLW\API\Databases\VLWdb\VLWdb;
	use VLW\API\Databases\VLWdb\Models\Media\MediaModel;
	use VLW\API\Databases\VLWdb\Models\MediaSrcset\MediaSrcsetModel;

	require_once Path::root("src/databases/VLWdb.php");
	require_once Path::root("src/databases/models/Media.php");
	require_once Path::root("src/databases/models/MediaSrcset.php");

	class GET_MediaSrcset extends VLWdb {
		protected Ruleset $ruleset;

		public function __construct() {
			parent::__construct();
			$this->ruleset = new Ruleset(strict: true);

			$this->ruleset->GET([
				(new Rules(MediaSrcsetModel::ID->value))
					->required()
					->type(Type::STRING)
					->min(1)
					->max(parent::MYSQL_VARCHAR_MAX_LENGTH)
			]);
		}

		// # Helper methods

		// Get metadata for the requested srcset
		private function get_srcset(): array|false {
			$srcset = $this->db->for(MediaSrcsetModel::TABLE)
				->where([MediaSrcsetModel::ID->value => $_GET[MediaSrcsetModel::ID->value]])
				->select([MediaSrcsetModel::ANCHOR_DEFAULT->value]);

			// Something went wrong retrieving rows from the database
			if (!parent::is_mysqli_result($srcset)) {
				return false;
			}

			// Return assoc array of srcset data if it exists
			return $srcset->num_rows === 1 ? $srcset->fetch_assoc() : false;
		}

		// Get all media entities that are part of the requested srcset
		private function get_srcset_media(): mysqli_result|false {
			$media = $this->db->for(MediaModel::TABLE)
				->where([MediaModel::SRCSET->value => $_GET[MediaSrcsetModel::ID->value]])
				->select([
					MediaModel::ID->value,
					MediaModel::TYPE->value,
					MediaModel::MIME->value,
					MediaModel::EXTENSION->value
				]);

			return parent::is_mysqli_result($media) ? $media : false;
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

			// Get srcset data
			$srcset = $this->get_srcset();
			if (!$srcset) {
				return new Response("No media srcset exist with id '{$_GET[MediaSrcsetModel::ID->value]}'", 404);
			}

			$media = $this->get_srcset_media();
			if (!$media) {
				return new Response("Failed to fetch srcset media", 500);
			}

			$media_entities = $media->fetch_all(MYSQLI_ASSOC);

			// This is the id of the media entity that is considered the default or "fallback"
			$srcet_default_media_id = $srcset[MediaSrcsetModel::ANCHOR_DEFAULT->value];
			
			// Return assoc array of all media entities that are in this srcset
			return new Response([
				// Return default media entity separately from the rest of the srcset as an assoc array
				"default" => array_filter($media_entities, fn(array $entity) => $entity[MediaModel::ID->value] === $srcet_default_media_id)[0],
				// Return all media that isn't default as array of assoc arrays
				"srcset"  => array_filter($media_entities, fn(array $entity) => $entity[MediaModel::ID->value] !== $srcet_default_media_id)
			]);
		}
	}