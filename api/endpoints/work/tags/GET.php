<?php

	use Reflect\Path;
	use Reflect\Response;
	use ReflectRules\Type;
	use ReflectRules\Rules;
	use ReflectRules\Ruleset;

	use VLW\API\Databases\VLWdb\VLWdb;
	use VLW\API\Databases\VLWdb\Models\Work\{
		WorkTagsModel,
		WorkTagsNameEnum
	};

	require_once Path::root("src/databases/VLWdb.php");
	require_once Path::root("src/databases/models/Work/WorkTags.php");

	class GET_WorkTags extends VLWdb {
		private Ruleset $ruleset;

		public function __construct() {
			$this->ruleset = new Ruleset(strict: true);

			$this->ruleset->GET([
				(new Rules(WorkTagsModel::REF_WORK_ID->value))
					->min(1)
					->max(parent::MYSQL_VARCHAR_MAX_LENGTH),
				
				(new Rules(WorkTagsModel::NAME->value))
					->type(Type::ENUM, WorkTagsNameEnum::names())
			]);

			parent::__construct($this->ruleset);
		}

		public function main(): Response {
			$response = $this->db->for(WorkTagsModel::TABLE)
				->where($_GET)
				->select([
					WorkTagsModel::REF_WORK_ID->value,
					WorkTagsModel::NAME->value
				]);

			return $response->num_rows > 0
				? new Response($response->fetch_all(MYSQLI_ASSOC))
				: new Response([], 404);
		}
	}