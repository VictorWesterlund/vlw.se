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
	use VLW\API\Databases\VLWdb\Models\Battlestation\ChassisModel;
	use VLW\API\Databases\VLWdb\Models\Battlestation\Config\ChassisMbModel;

	require_once Path::root("src/databases/VLWdb.php");
	require_once Path::root("src/databases/models/Battlestation/Chassis.php");
	require_once Path::root("src/databases/models/Battlestation/Config/ChassisMb.php");

	class GET_BattlestationChassis extends VLWdb {
		private const REL_MOTHERBOARDS = "motherboards";

		protected Ruleset $ruleset;

		private array $query;
		private array $results = [];

		public function __construct() {
			$this->ruleset = new Ruleset(strict: true);

			$this->ruleset->GET([
				(new Rules(ChassisModel::ID->value))
					->type(Type::STRING)
					->min(parent::UUID_LENGTH)
					->max(parent::UUID_LENGTH),

				(new Rules(ChassisModel::VENDOR_NAME->value))
					->type(Type::STRING)
					->min(1)
					->max(parent::MYSQL_VARCHAR_MAX_LENGTH),

				(new Rules(ChassisModel::VENDOR_MODEL->value))
					->type(Type::STRING)
					->min(1)
					->max(parent::MYSQL_VARCHAR_MAX_LENGTH),

				(new Rules(ChassisModel::STORAGE_TWOINCHFIVE->value))
					->type(Type::NUMBER)
					->type(Type::NULL)
					->min(0)
					->max(parent::MYSQL_TINYINT_MAX_LENGTH),

				(new Rules(ChassisModel::STORAGE_THREEINCHFIVE->value))
					->type(Type::NUMBER)
					->type(Type::NULL)
					->min(0)
					->max(parent::MYSQL_TINYINT_MAX_LENGTH),

				(new Rules(ChassisModel::IS_RETIRED->value))
					->type(Type::BOOLEAN)
			]);

			parent::__construct(Databases::BATTLESTATION, $this->ruleset);

			// Use a copy of search parameters
			$this->query = $_GET;
		}

		private function get_motherboards(): void {
			foreach ($this->results as &$result) {
				// Get motherboard id from relationship by chassis id
				$result[self::REL_MOTHERBOARDS] = $this->db
					->for(ChassisMbModel::TABLE)
					->where([ChassisMbModel::REF_CHASSIS_ID->value => $result[ChassisModel::ID->value]])
					->select(ChassisMbModel::values())
					->fetch_all(MYSQLI_ASSOC);
			}
		}

		private function get_chassis(): array {
			return $this->results = $this->db
				->for(ChassisModel::TABLE)
				->where($this->query)
				->order([ChassisModel::DATE_AQUIRED->value => "DESC"])
				->select(ChassisModel::values())
				->fetch_all(MYSQLI_ASSOC);
		}

		public function main(): Response {
			// Set properties as "searchable"
			parent::make_wildcard_search(ChassisModel::VENDOR_NAME->value, $this->query);
			parent::make_wildcard_search(ChassisModel::VENDOR_MODEL->value, $this->query);

			// Get hardware
			$this->get_chassis();

			// Resolve hardware relationships
			$this->get_motherboards();

			// Return 404 Not Found if response array is empty
			return new Response($this->results, $this->results ? 200 : 404);
		}
	}