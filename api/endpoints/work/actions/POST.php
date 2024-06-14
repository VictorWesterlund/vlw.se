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
		WorkActionsModel
	};

	require_once Path::root("src/Endpoints.php");
	require_once Path::root("src/databases/VLWdb.php");
	require_once Path::root("src/databases/models/Work/Work.php");
	require_once Path::root("src/databases/models/Work/WorkActions.php");

	class POST_WorkActions extends VLWdb {
		protected Ruleset $ruleset;

		public function __construct() {
			$this->ruleset = new Ruleset(strict: true);

			$this->ruleset->POST([
				(new Rules(WorkActionsModel::REF_WORK_ID->value))
					->required()
					->min(1)
					->max(parent::MYSQL_VARCHAR_MAX_LENGTH),

				(new Rules(WorkActionsModel::DISPLAY_TEXT->value))
					->required()
					->type(Type::STRING)
					->min(1)
					->max(parent::MYSQL_VARCHAR_MAX_LENGTH),

				(new Rules(WorkActionsModel::HREF->value))
					->required()
					->type(Type::STRING)
					->type(Type::NULL)
					->min(1)
					->max(parent::MYSQL_VARCHAR_MAX_LENGTH),

				(new Rules(WorkActionsModel::CLASS_LIST->value))
					->type(Type::ARRAY)
					->min(1)
					->default([]),

				(new Rules(WorkActionsModel::EXTERNAL->value))
					->type(Type::BOOLEAN)
					->default(false)
			]);

			parent::__construct($this->ruleset);
		}

		private static function get_entity(): Response {
			return (new Call(Endpoints::WORK->value))->params([
				WorkModel::ID->value => $_POST[WorkActionsModel::REF_WORK_ID->value]
			])->get();
		}

		public function main(): Response {
			// Bail out if work entity could not be fetched
			$entity = self::get_entity();
			if (!$entity->ok) {
				return $entity;
			}

			return $this->db->for(WorkActionsModel::TABLE)->insert($_POST) === true
				? new Response($_POST[WorkActionsModel::REF_WORK_ID->value], 201)
				: new Response("Failed to add action to work entity", 500);
		}
	}