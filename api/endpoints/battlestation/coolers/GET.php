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
	use VLW\API\Databases\VLWdb\Models\Battlestation\{
		CoolerModel
	};
	use VLW\API\Databases\VLWdb\Models\Battlestation\Config\MbCpuCoolerModel;

	require_once Path::root("src/databases/VLWdb.php");
	require_once Path::root("src/databases/models/Battlestation/Coolers.php");
	require_once Path::root("src/databases/models/Battlestation/Config/MbCpuCooler.php");

	class GET_BattlestationCoolers extends VLWdb {
		private const REL_MOTHERBOARDS = "motherboards";

		protected Ruleset $ruleset;

		private array $query;
		private array $results = [];

		public function __construct() {
			$this->ruleset = new Ruleset(strict: true);

			$this->ruleset->GET([
				(new Rules(CoolerModel::ID->value))
					->type(Type::STRING)
					->min(parent::UUID_LENGTH)
					->max(parent::UUID_LENGTH),

				(new Rules(CoolerModel::TYPE_LIQUID->value))
					->type(Type::BOOLEAN),

				(new Rules(CoolerModel::SIZE_FAN->value))
					->type(Type::NULL)
					->type(Type::NUMBER)
					->min(1)
					->max(parent::MYSQL_INTR_MAX_LENGTH),

				(new Rules(CoolerModel::SIZE_RADIATOR->value))
					->type(Type::NULL)
					->type(Type::NUMBER)
					->min(1)
					->max(parent::MYSQL_INTR_MAX_LENGTH),

				(new Rules(CoolerModel::VENDOR_NAME->value))
					->type(Type::STRING)
					->min(1)
					->max(parent::MYSQL_VARCHAR_MAX_LENGTH),

				(new Rules(CoolerModel::VENDOR_MODEL->value))
					->type(Type::STRING)
					->min(1)
					->max(parent::MYSQL_VARCHAR_MAX_LENGTH),

				(new Rules(CoolerModel::IS_RETIRED->value))
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
					->for(MbCoolerModel::TABLE)
					->where([MbCoolerModel::REF_COOLER_ID->value => $result[CoolerModel::ID->value]])
					->select(MbCoolerModel::values())
					->fetch_all(MYSQLI_ASSOC);
			}
		}

		private function get_coolers(): array {
			return $this->results = $this->db
				->for(CoolerModel::TABLE)
				->where($this->query)
				->order([CoolerModel::DATE_AQUIRED->value => "DESC"])
				->select(CoolerModel::values())
				->fetch_all(MYSQLI_ASSOC);
		}

		public function main(): Response {
			// Set properties as "searchable"
			parent::make_wildcard_search(CoolerModel::VENDOR_NAME->value, $this->query);
			parent::make_wildcard_search(CoolerModel::VENDOR_MODEL->value, $this->query);

			// Get hardware
			$this->get_coolers();

			// Resolve hardware relationships
			$this->get_motherboards();

			// Return 404 Not Found if response array is empty
			return new Response($this->results, $this->results ? 200 : 404);
		}
	}