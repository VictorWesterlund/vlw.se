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

		protected Ruleset $ruleset;
		protected Response $entity;

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
					->max(self::MYSQL_TEXT_MAX_LENGTH)
			]);

			$this->get_entity();
		}

		// Generate a slug URL from string
		private static function gen_slug(string $input): string {
			return strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $input)));
		}

		// Create new permalink for entity slug
		private function create_permalink(string $slug): bool {
			$create = Call("work/permalinks", Method::POST, [
				WorkPermalinksModel::SLUG->value   => $slug,
				WorkPermalinksModel::ANCHOR->value => $slug
			]);

			return $create->ok;
		}

		private function get_entity(): Response {
			// Check if an entity already exists with slugified title from GET endpoint
			$this->entity = Call("work?id={$_GET["id"]}", Method::GET);

			// Response is not 404 (Not found) so we can't create the entity
			if ($this->entity->code !== 200) {
				// Response is not a valid entity, something went wrong
				if ($this->entity->code !== 404) {
					return $this->resp_database_error();
				}

				// Return 402 Conflict
				return new Response("No entity with id '{$_GET["id"]}' was found", 404);
			}

			return $this->entity;
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
				->update($this->entity->output());

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

			// Assoc array of columns and values to update
			$update_entity = $_POST;

			// Generate new slug for entity if title is updated
			if (array_key_exists(WorkModel::TITLE->value, $_POST)) {
				// Generate URL slug from title text or UUID if undefined
				$slug = self::gen_slug($_POST["title"]);

				// Save generated slug from title if it's different from existing slug
				if ($slug !== $this->entity->output()[WorkModel::ID->value]) {
					$update_entity[WorkModel::ID->value] = $slug;
				}
			}

			// Attempt to update the entity
			$update = $this->db->for(WorkModel::TABLE)
				->where([WorkModel::ID->value => $_GET["id"]])
				->update($update_entity);

			// Bail out if update failed
			if (!$update) {
				return $this->resp_database_error();
			}

			// Slug has been updated
			if (array_key_exists(WorkModel::ID->value, $update_entity)) {
				// Create new permalink for changed slug
				if (!$this->create_permalink($update_entity[WorkModel::ID->value])) {
					return $this->resp_permalink_error_rollback();
				}

				return new Response($update_entity[WorkModel::ID->value]);
			}
			
			// Return 200 OK and new or existing entity slug as body
			return new Response($this->entity->output()[WorkModel::ID->value]);
		}
	}