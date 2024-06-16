<?php

	use Reflect\Path;
	use Reflect\Response;
	use ReflectRules\Type;
	use ReflectRules\Rules;
	use ReflectRules\Ruleset;

	use VLW\API\Databases\VLWdb\VLWdb;
	use VLW\API\Databases\VLWdb\Models\Work\WorkModel;

	require_once Path::root("src/databases/VLWdb.php");
	require_once Path::root("src/databases/models/Work/Work.php");

	class GET_Work extends VLWdb {
		protected Ruleset $ruleset;

		public function __construct() {
			$this->ruleset = new Ruleset(strict: true);

			$this->ruleset->GET([
				(new Rules(WorkModel::ID->value))
					->type(Type::STRING)
					->min(1)
					->max(parent::MYSQL_VARCHAR_MAX_LENGTH),

				(new Rules(WorkModel::TITLE->value))
					->type(Type::STRING)
					->max(parent::MYSQL_VARCHAR_MAX_LENGTH),

				(new Rules(WorkModel::SUMMARY->value))
					->type(Type::STRING)
					->max(parent::MYSQL_TEXT_MAX_LENGTH),

				(new Rules(WorkModel::IS_LISTABLE->value))
					->type(Type::BOOLEAN)
					->default(true),

				(new Rules(WorkModel::IS_READABLE->value))
					->type(Type::BOOLEAN)
					->default(true),

				(new Rules(WorkModel::DATE_MODIFIED->value))
					->type(Type::NUMBER)
					->min(1)
					->max(parent::MYSQL_INT_MAX_LENGHT),

				(new Rules(WorkModel::DATE_CREATED->value))
					->type(Type::NUMBER)
					->min(1)
					->max(parent::MYSQL_INT_MAX_LENGHT)
			]);

			parent::__construct($this->ruleset);
		}

		public function main(): Response {
			// Use copy of search paramters as filters
			$filters = $_GET;

			// Do a wildcard search on the title column if provided
			if (array_key_exists(WorkModel::TITLE->value, $_GET)) {
				$filters[WorkModel::TITLE->value] = [
					"LIKE" => "%{$_GET[WorkModel::TITLE->value]}%"
				];
			}

			// Do a wildcard search on the summary column if provided
			if (array_key_exists(WorkModel::SUMMARY->value, $_GET)) {
				$filters[WorkModel::SUMMARY->value] = [
					"LIKE" => "%{$_GET[WorkModel::SUMMARY->value]}%"
				];
			}

			$response = $this->db->for(WorkModel::TABLE)
				->where($filters)
				->select([
					WorkModel::ID->value,
					WorkModel::TITLE->value,
					WorkModel::SUMMARY->value,
					WorkModel::IS_LISTABLE->value,
					WorkModel::IS_READABLE->value,
					WorkModel::DATE_YEAR->value,
					WorkModel::DATE_MONTH->value,
					WorkModel::DATE_DAY->value,
					WorkModel::DATE_MODIFIED->value,
					WorkModel::DATE_CREATED->value
				]);

			return $response->num_rows > 0
				? new Response($response->fetch_all(MYSQLI_ASSOC))
				: new Response([], 404);
		}
	}