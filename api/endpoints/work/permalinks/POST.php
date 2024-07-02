<?php

	use Reflect\Call;
	use Reflect\Path;
	use Reflect\Response;
	use ReflectRules\Type;
	use ReflectRules\Rules;
	use ReflectRules\Ruleset;

	use VLW\API\Databases\VLWdb\{
		VLWdb,
		Databases
	};
	use VLW\API\Databases\VLWdb\Models\Work\WorkPermalinksModel;

	require_once Path::root("src/databases/VLWdb.php");
	require_once Path::root("src/databases/models/Work/WorkPermalinks.php");

	class POST_WorkPermalinks extends VLWdb {
		protected Ruleset $ruleset;

		public function __construct() {
			$this->ruleset = new Ruleset(strict: true);

			$this->ruleset->POST([
				(new Rules(WorkPermalinksModel::ID->value))
					->required()
					->type(Type::STRING)
					->min(1)
					->max(parent::MYSQL_VARCHAR_MAX_LENGTH),

				(new Rules(WorkPermalinksModel::REF_WORK_ID->value))
					->required()
					->type(Type::STRING)
					->min(1)
					->max(parent::MYSQL_VARCHAR_MAX_LENGTH),

				(new Rules(WorkPermalinksModel::DATE_CREATED->value))
					->type(Type::NUMBER)
					->min(1)
					->max(parent::MYSQL_INT_MAX_LENGTH)
					->default(time())
			]);

			parent::__construct(Databases::VLW, $this->ruleset);
		}

		private static function get_entity(): Response {
			return (new Call(Endpoints::WORK->value))->params([
				WorkModel::ID->value => $_POST[WorkTagsModel::REF_WORK_ID->value]
			])->get();
		}

		public function main(): Response {
			// Bail out if work entity could not be fetched
			$entity = self::get_entity();
			if (!$entity->ok) {
				return $entity;
			}

			return $this->db->for(WorkPermalinksModel::TABLE)->insert($_POST) === true
				? new Response($_POST[WorkPermalinksModel::ID->value], 201)
				: new Response("Failed to add permalink to work entity", 500);
		}
	}