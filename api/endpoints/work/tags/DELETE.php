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
	use VLW\API\Databases\VLWdb\Models\Work\WorkTagsModel;

	require_once Path::root("src/databases/VLWdb.php");
	require_once Path::root("src/databases/models/Work/WorkTags.php");

	class DELETE_WorkTags extends VLWdb {
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

			parent::__construct(Databases::VLW, $this->ruleset);
		}

		public function main(): Response {
			return $this->db->for(WorkTagsModel::TABLE)->delete($_POST) === true
				? new Response(RESP_DELETE_OK)
				: new Response("Failed to delete value from document", 500);
		}
	}