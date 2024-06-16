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
		WorkTagsModel,
		WorkTagsNameEnum
	};

	require_once Path::root("src/Endpoints.php");
	require_once Path::root("src/databases/VLWdb.php");
	require_once Path::root("src/databases/models/Work/Work.php");
	require_once Path::root("src/databases/models/Work/WorkTags.php");

	class POST_WorkTags extends VLWdb {
		protected Ruleset $ruleset;

		public function __construct() {
			$this->ruleset = new Ruleset(strict: true);

			$this->ruleset->POST([
				(new Rules(WorkTagsModel::REF_WORK_ID->value))
					->required()
					->min(1)
					->max(parent::MYSQL_VARCHAR_MAX_LENGTH),
				
				(new Rules(WorkTagsModel::NAME->value))
					->required()
					->type(Type::ENUM, WorkTagsNameEnum::names())
			]);

			parent::__construct($this->ruleset);
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

			return $this->db->for(WorkTagsModel::TABLE)->insert($_POST) === true
				? new Response($_POST[WorkTagsModel::REF_WORK_ID->value], 201)
				: new Response("Failed to add tag to work entity", 500);
		}
	}