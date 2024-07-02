<?php

	use Reflect\Call;
	use Reflect\Path;
	use Reflect\Response;
	use ReflectRules\Type;
	use ReflectRules\Rules;
	use ReflectRules\Ruleset;

	use VLW\API\Endpoints;
	use VLW\API\Databases\VLWdb\{
		VLWdb,
		Databases
	};
	use VLW\API\Databases\VLWdb\Models\Work\{
		WorkModel,
		WorkPermalinksModel
	};

	require_once Path::root("src/Endpoints.php");
	require_once Path::root("src/databases/VLWdb.php");
	require_once Path::root("src/databases/models/Work/Work.php");
	require_once Path::root("src/databases/models/Work/WorkPermalinks.php");

	class PATCH_Work extends VLWdb {
		protected Ruleset $ruleset;

		public function __construct() {
			$this->ruleset = new Ruleset(strict: true);

			$this->ruleset->GET([
				(new Rules(WorkModel::ID->value))
					->required()
					->type(Type::STRING)
					->min(1)
					->max(parent::MYSQL_VARCHAR_MAX_LENGTH)
			]);

			$this->ruleset->POST([
				(new Rules(WorkModel::TITLE->value))
					->type(Type::STRING)
					->min(3)
					->max(parent::MYSQL_VARCHAR_MAX_LENGTH),

				(new Rules(WorkModel::SUMMARY->value))
					->type(Type::STRING)
					->min(1)
					->max(parent::MYSQL_TEXT_MAX_LENGTH),

				(new Rules(WorkModel::IS_LISTABLE->value))
					->type(Type::BOOLEAN),

				(new Rules(WorkModel::IS_READABLE->value))
					->type(Type::BOOLEAN),

				(new Rules(WorkModel::DATE_MODIFIED->value))
					->type(Type::NUMBER)
					->min(1)
					->max(parent::MYSQL_INT_MAX_LENGTH)
					->default(time()),

				(new Rules(WorkModel::DATE_CREATED->value))
					->type(Type::NUMBER)
					->min(1)
					->max(parent::MYSQL_INT_MAX_LENGTH)
			]);

			parent::__construct();
		}

		// Generate a slug URL from string
		private static function gen_slug(string $input): string {
			return strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $input)));
		}

		// Compute and return modeled year, month, and day from Unix timestamp in request body
		private static function gen_date_created(): array {
			return [
				WorkModel::DATE_YEAR->value   => date("Y", $_POST[WorkModel::DATE_CREATED->value]),
				WorkModel::DATE_MONTH ->value => date("n", $_POST[WorkModel::DATE_CREATED->value]),
				WorkModel::DATE_DAY->value    => date("j", $_POST[WorkModel::DATE_CREATED->value])
			];
		}

		private function get_entity_by_id(string $id): Response {
			return (new Call(Endpoints::WORK->value))->params([
				WorkModel::ID->value => $id
			])->get();
		}

		public function main(): Response {
			// Use copy of request body as entity
			$entity = $_POST;

			// Generate a new slug id from title if changed
			if ($_POST[WorkModel::TITLE->value]) {
				$slug = $_POST[WorkModel::TITLE->value];

				// Bail out if the slug generated from the new tite already exist
				if ($this->get_entity_by_id($slug)) {
					return new Response("An entity with this title already exist", 409);
				}

				// Add the new slug to update entity
				$entity[WorkModel::ID] = $slug;
			}

			// Generate new work date fields from timestamp
			if ($_POST[WorkModel::DATE_CREATED->value]) {
				array_merge($entity, self::gen_date_created());
			}
			
			// Update entity by existing id
			return $this->db->for(WorkModel::TABLE)->where([WorkModel::ID->value => $_GET[WorkModel::ID->value]])->update($entity) === true
				? new Response($_GET[WorkModel::ID->value])
				: new Response("Failed to update entity", 500);
		}
	}