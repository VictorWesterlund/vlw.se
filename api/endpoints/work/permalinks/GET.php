<?php

	use Reflect\Path;
	use Reflect\Response;
	use ReflectRules\Type;
	use ReflectRules\Rules;
	use ReflectRules\Ruleset;

	use VLW\API\Databases\VLWdb\VLWdb;
	use VLW\API\Databases\VLWdb\Models\Work\WorkPermalinksModel;

	require_once Path::root("src/databases/VLWdb.php");
	require_once Path::root("src/databases/models/Work/WorkPermalinks.php");

	class GET_WorkPermalinks extends VLWdb {
		protected Ruleset $ruleset;

		public function __construct() {
			$this->ruleset = new Ruleset(strict: true);

			$this->ruleset->GET([
				(new Rules(WorkPermalinksModel::ID->value))
					->type(Type::STRING)
					->min(1)
					->max(parent::MYSQL_VARCHAR_MAX_LENGTH),

				(new Rules(WorkPermalinksModel::REF_WORK_ID->value))
					->type(Type::STRING)
					->min(1)
					->max(parent::MYSQL_VARCHAR_MAX_LENGTH)
			]);

			parent::__construct($this->ruleset);
		}

		public function main(): Response {
			$response = $this->db->for(WorkPermalinksModel::TABLE)
				->where($_GET)
				->select([
					WorkPermalinksModel::ID->value,
					WorkPermalinksModel::REF_WORK_ID->value,
					WorkPermalinksModel::DATE_CREATED->value
				]);

			return $response->num_rows > 0
				? new Response($response->fetch_all(MYSQLI_ASSOC))
				: new Response([], 404);
		}
	}