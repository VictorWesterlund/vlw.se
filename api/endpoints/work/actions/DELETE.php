<?php

	use Reflect\Path;
	use Reflect\Response;
	use ReflectRules\Type;
	use ReflectRules\Rules;
	use ReflectRules\Ruleset;

	use const VLW\API\RESP_DELETE_OK;
	use VLW\API\Databases\VLWdb\VLWdb;
	use VLW\API\Databases\VLWdb\Models\Work\WorkActionsModel;

	require_once Path::root("src/databases/VLWdb.php");
	require_once Path::root("src/databases/models/WorkActions.php");

	class DELETE_WorkActions extends VLWdb {
		protected Ruleset $ruleset;

		public function __construct() {
			parent::__construct();
			$this->ruleset = new Ruleset(strict: true);

			$this->ruleset->POST([
				(new Rules(WorkActionsModel::REF_WORK_ID->value))
					->min(1)
					->max(parent::MYSQL_VARCHAR_MAX_LENGTH)
			]);
		}

		public function main(): Response {
			return $this->db->for(WorkActionsModel::TABLE)->delete($_POST) === true
				? new Response(RESP_DELETE_OK)
				: new Response("Failed to delete action for work entity", 500);
		}
	}