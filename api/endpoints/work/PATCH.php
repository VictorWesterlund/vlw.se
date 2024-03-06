<?php

	use Reflect\Path;
	use Reflect\Response;
	use ReflectRules\Type;
	use ReflectRules\Rules;
	use ReflectRules\Ruleset;

	use function Reflect\Call;
	use Reflect\Request\Method;

	use VLW\API\Databases\VLWdb\VLWdb;
	use VLW\API\Databases\VLWdb\Models\Work\WorkModel;
	use VLW\API\Databases\VLWdb\Models\WorkPermalinks\WorkPermalinksModel;

	require_once Path::root("src/databases/VLWdb.php");
	require_once Path::root("src/databases/models/Work.php");
	require_once Path::root("src/databases/models/WorkPermalinks.php");

	class PATCH_Work extends VLWdb {
		const MYSQL_TEXT_MAX_LENGTH = 65538;
		const MYSQL_INT_MAX_LENGHT = 2147483647;

		protected Ruleset $ruleset;

		protected Response $current_entity;
		protected array $updated_entity;

		public function __construct() {
			parent::__construct();
			$this->ruleset = new Ruleset(strict: true);

			$this->ruleset->GET([
				(new Rules(WorkModel::ID->value))
					->required()
					->type(Type::STRING)
					->min(1)
					->max(255)
			]);

			$this->ruleset->POST([
				(new Rules(WorkModel::TITLE->value))
					->type(Type::STRING)
					->min(3)
					->max(255),

				(new Rules(WorkModel::SUMMARY->value))
					->type(Type::STRING)
					->min(1)
					->max(self::MYSQL_TEXT_MAX_LENGTH),

				(new Rules(WorkModel::IS_LISTABLE->value))
					->type(Type::BOOLEAN),

				(new Rules(WorkModel::IS_READABLE->value))
					->type(Type::BOOLEAN),

				(new Rules(WorkModel::DATE_TIMESTAMP_CREATED->value))
					->type(Type::NUMBER)
					->min(0)
					->max(self::MYSQL_INT_MAX_LENGHT)
			]);

			$this->get_existing_entity();

			// Copy all provided post data into a new array
			$this->updated_entity = $_POST;

			// Set date modified timestamp
			$this->updated_entity[WorkModel::DATE_TIMESTAMP_MODIFIED->value] = time();
		}

		// Generate a slug URL from string
		private static function gen_slug(string $input): string {
			return strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $input)));
		}

		// # Helper methods

		private function get_existing_entity(): Response {
			// Check if an entity already exists with slugified title from GET endpoint
			$this->current_entity = Call("work?id={$_GET["id"]}", Method::GET);

			// Response is not 404 (Not found) so we can't create the entity
			if ($this->current_entity->code !== 200) {
				// Response is not a valid entity, something went wrong
				if ($this->current_entity->code !== 404) {
					return $this->resp_database_error();
				}

				// Return 402 Conflict
				return new Response("No entity with id '{$_GET["id"]}' was found", 404);
			}

			return $this->current_entity;
		}

		// Create new permalink for entity slug
		private function create_permalink(string $slug): bool {
			$create = Call("work/permalinks", Method::POST, [
				WorkPermalinksModel::SLUG->value   => $slug,
				WorkPermalinksModel::ANCHOR->value => $slug
			]);

			return $create->ok;
		}

		// ## Updated entity

		private function change_slug(): bool {
			if (!array_key_exists(WorkModel::ID->value, $this->updated_entity)) {
				return true;
			}

			// Generate new permalink for entity id
			return $this->create_permalink($this->updated_entity[WorkModel::ID->value]);
		}

		private function timestamp_to_dates(): void {
			if (!array_key_exists(WorkModel::DATE_TIMESTAMP_CREATED->value, $this->updated_entity)) {
				return;
			}

			// Get timestamp from post data
			$timestamp = $this->updated_entity[WorkModel::DATE_TIMESTAMP_CREATED->value];

			// Update fractured dates from timestamp
			$this->updated_entity[WorkModel::DATE_YEAR->value] = date("Y", $timestamp);
			$this->updated_entity[WorkModel::DATE_MONTH ->value] = date("n", $timestamp);
			$this->updated_entity[WorkModel::DATE_DAY->value] = date("j", $timestamp);
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

		// Return a 422 Unprocessable Entity if there is nothing to change
		private function resp_no_changes(): Response {
			return new Response("No columns to update", 422);
		}

		// Rollback changes and return error response
		private function resp_permalink_error_rollback(): Response {
			$update = $this->db->for(WorkModel::TABLE)
				->where([WorkModel::ID->value => $_GET["id"]])
				->update($this->current_entity->output());

			return $update
				? new Response("Failed to create new permalink for updated entity. Changes have been rolled back", 500)
				: new Reponse("Failed to create new permalink for updated entity. Changes failed to rollback, this is bad.", 500);
		}

		public function main(): Response {
			// Bail out if request validation failed
			if (!$this->ruleset->is_valid()) {
				return $this->resp_rules_invalid();
			}

			// Empty payload, nothing to do
			if (empty($_POST)) {
				return $this->resp_no_changes();
			}

			// Generate new slug for entity if title is updated
			if (array_key_exists(WorkModel::TITLE->value, $_POST)) {
				// Generate URL slug from title text or UUID if undefined
				$slug = self::gen_slug($_POST["title"]);

				// Save generated slug from title if it's different from existing slug
				if ($slug !== $this->current_entity->output()[WorkModel::ID->value]) {
					$this->updated_entity[WorkModel::ID->value] = $slug;
				}
			}

			// Update fractured dates from timestamp
			$this->timestamp_to_dates();

			// Attempt to update the entity
			$update = $this->db->for(WorkModel::TABLE)
				->where([WorkModel::ID->value => $_GET["id"]])
				->update($this->updated_entity);

			// Bail out if update failed
			if (!$update) {
				return $this->resp_database_error();
			}

			// Create new slug for entity if title was changed
			if (!$this->change_slug()) {
				return $this->resp_permalink_error_rollback();
			}
			
			// Return 200 OK and new or existing entity slug as body
			return new Response($this->current_entity->output()[WorkModel::ID->value]);
		}
	}