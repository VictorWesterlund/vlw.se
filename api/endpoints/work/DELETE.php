<?php

	use Reflect\Path;
	use Reflect\Response;
	use ReflectRules\Type;
	use ReflectRules\Rules;
	use ReflectRules\Ruleset;

	use const VLW\API\RESP_DELETE_OK;
	use VLW\API\Databases\VLWdb\{
		VLWdb,
		Databases
	};
	use VLW\API\Databases\VLWdb\Models\Work\WorkModel;

	require_once Path::root("src/Endpoints.php");
	require_once Path::root("src/databases/VLWdb.php");
	require_once Path::root("src/databases/models/Work.php");

	class DELETE_Work extends VLWdb {
		protected Ruleset $ruleset;

		public function __construct() {
			$this->ruleset = new Ruleset(strict: true);

			$this->ruleset->POST([
				(new Rules(WorkModel::ID->value))
					->type(Type::STRING)
					->min(1)
					->max(parent::MYSQL_VARCHAR_MAX_LENGTH),

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
					->max(parent::MYSQL_INT_MAX_LENGTH),

				(new Rules(WorkModel::DATE_CREATED->value))
					->type(Type::NUMBER)
					->min(1)
					->max(parent::MYSQL_INT_MAX_LENGTH)
			]);

			parent::__construct(Databases::VLW, $this->ruleset);
		}

		public function main(): Response {
			return $this->db->for(FieldsEnumsModel::TABLE)->delete($_POST) === true
				? new Response(RESP_DELETE_OK)
				: new Response("Failed to delete work entity", 500);
		}
	}