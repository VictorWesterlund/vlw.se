<?php

	use Reflect\Path;
	use Reflect\Response;
	use ReflectRules\Type;
	use ReflectRules\Rules;
	use ReflectRules\Ruleset;

	use VLW\API\Databases\VLWdb\{
		VLWdb,
		Databases
	};
	use VLW\API\Databases\VLWdb\Models\Work\WorkActionsModel;

	require_once Path::root("src/databases/VLWdb.php");
	require_once Path::root("src/databases/models/Work/WorkActions.php");

	class GET_WorkActions extends VLWdb {
		protected Ruleset $ruleset;

		public function __construct() {
			$this->ruleset = new Ruleset(strict: true);

			$this->ruleset->GET([
				(new Rules(WorkActionsModel::REF_WORK_ID->value))
					->type(Type::STRING)
					->min(1)
					->max(parent::MYSQL_VARCHAR_MAX_LENGTH)
			]);

			parent::__construct(Databases::VLW, $this->ruleset);
		}

		public function main(): Response {
			$response = $this->db->for(WorkActionsModel::TABLE)
				->where($_GET)
				->select([
					WorkActionsModel::REF_WORK_ID->value,
					WorkActionsModel::DISPLAY_TEXT->value,
					WorkActionsModel::HREF->value,
					WorkActionsModel::CLASS_LIST->value,
					WorkActionsModel::EXTERNAL->value
				]);

			return $response->num_rows > 0
				? new Response($response->fetch_all(MYSQLI_ASSOC))
				: new Response([], 404);
		}
	}