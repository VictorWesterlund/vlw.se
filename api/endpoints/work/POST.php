<?php

	use Reflect\Call;
	use Reflect\Path;
	use Reflect\Response;
	use ReflectRules\Type;
	use ReflectRules\Rules;
	use ReflectRules\Ruleset;

	use VLW\API\Endpoints;
	use VLW\API\Databases\VLWdb\VLWdb;
	use VLW\API\Databases\VLWdb\Models\Work\{
		WorkModel,
		WorkPermalinksModel
	};

	require_once Path::root("src/Endpoints.php");
	require_once Path::root("src/databases/VLWdb.php");
	require_once Path::root("src/databases/models/Work/Work.php");
	require_once Path::root("src/databases/models/Work/WorkPermalinks.php");

	class POST_Work extends VLWdb {
		protected Ruleset $ruleset;

		public function __construct() {
			$this->ruleset = new Ruleset(strict: true);

			$this->ruleset->POST([
				(new Rules(WorkModel::TITLE->value))
					->type(Type::STRING)
					->min(3)
					->max(parent::MYSQL_VARCHAR_MAX_LENGTH)
					->default(null),

				(new Rules(WorkModel::SUMMARY->value))
					->type(Type::STRING)
					->min(1)
					->max(parent::MYSQL_TEXT_MAX_LENGTH)
					->default(null),

				(new Rules(WorkModel::IS_LISTABLE->value))
					->type(Type::BOOLEAN)
					->default(false),

				(new Rules(WorkModel::IS_READABLE->value))
					->type(Type::BOOLEAN)
					->default(false),

				(new Rules(WorkModel::DATE_CREATED->value))
					->type(Type::NUMBER)
					->min(1)
					->max(parent::MYSQL_INT_MAX_LENGHT)
					->default(time())
			]);

			parent::__construct($this->ruleset);
		}

		// Generate a slug URL from string
		private static function gen_slug(string $input): string {
			return strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $input)));
		}

		// Compute and return modeled year, month, and day from a Unix timestamp
		private static function gen_date_created(): array {
			// Use provided timestamp in request
			$date_created = $_POST[WorkModel::DATE_CREATED->value];

			return [
				WorkModel::DATE_YEAR->value   => date("Y", $date_created),
				WorkModel::DATE_MONTH ->value => date("n", $date_created),
				WorkModel::DATE_DAY->value    => date("j", $date_created)
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

			// Generate URL slug from title text or UUID if undefined
			$entity[WorkModel::ID->value] = $_POST[WorkModel::TITLE->value] 
				? self::gen_slug($_POST[WorkModel::TITLE->value]) 
				: parent::gen_uuid4();

			// Bail out here if a work entry with id had been created already
			if ($this->get_entity_by_id($entity[WorkModel::ID->value])->ok) {
				return new Response("An entity with id '{$slug}' already exist", 409);
			}

			// Generate the necessary date fields
			array_merge($entity, self::gen_date_created());

			// Let's try to insert the new entity
			if (!$this->db->for(WorkModel::TABLE)->insert($entity)) {
				return new Response("Failed to insert work entry", 500);
			}

			// Generate permalink for new entity
			return (new Call(Endpoints::WORK_PERMALINKS->value))->post([
				WorkPermalinksModel::ID           => $entity[WorkModel::ID->value],
				WorkPermalinksModel::REF_WORK_ID  => $entity[WorkModel::ID->value],
				WorkPermalinksModel::DATE_CREATED => time()
			]);
		}
	}