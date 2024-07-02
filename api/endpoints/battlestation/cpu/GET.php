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
		CpuModel,
		ClassEnum
	};
	use VLW\API\Databases\VLWdb\Models\Battlestation\Config\MbCpuCoolerModel;

	require_once Path::root("src/databases/VLWdb.php");
	require_once Path::root("src/databases/models/Battlestation/Cpu.php");
	require_once Path::root("src/databases/models/Battlestation/Config/MbCpuCooler.php");

	class GET_BattlestationCpu extends VLWdb {
		private const REL_MOTHERBOARDS = "motherboards";

		protected Ruleset $ruleset;

		private array $query;
		private array $results = [];

		public function __construct() {
			$this->ruleset = new Ruleset(strict: true);

			$this->ruleset->GET([
				(new Rules(CpuModel::ID->value))
					->type(Type::STRING)
					->min(parent::UUID_LENGTH)
					->max(parent::UUID_LENGTH),

				(new Rules(CpuModel::CLOCK_BASE->value))
					->type(Type::NUMBER)
					->min(1),

				(new Rules(CpuModel::CLOCK_TURBO->value))
					->type(Type::NUMBER)
					->min(1),

				(new Rules(CpuModel::CORE_COUNT_PERFORMANCE->value))
					->type(Type::NUMBER)
					->min(1)
					->max(parent::MYSQL_TINYINT_MAX_LENGTH),

				(new Rules(CpuModel::CORE_COUNT_EFFICIENCY->value))
					->type(Type::NUMBER)
					->min(1)
					->max(parent::MYSQL_TINYINT_MAX_LENGTH),

				(new Rules(CpuModel::CORE_THREADS->value))
					->type(Type::NUMBER)
					->min(1)
					->max(parent::MYSQL_TINYINT_MAX_LENGTH),

				(new Rules(CpuModel::VENDOR_NAME->value))
					->type(Type::STRING)
					->min(1)
					->max(parent::MYSQL_VARCHAR_MAX_LENGTH),

				(new Rules(CpuModel::VENDOR_MODEL->value))
					->type(Type::STRING)
					->min(1)
					->max(parent::MYSQL_VARCHAR_MAX_LENGTH)
			]);

			parent::__construct(Databases::BATTLESTATION, $this->ruleset);

			// Use a copy of search parameters
			$this->query = $_GET;
		}

		private function get_motherboards(): void {
			foreach ($this->results as &$result) {
				// Get motherboard id from relationship by chassis id
				$result[self::REL_MOTHERBOARDS] = $this->db
					->for(MbCpuCoolerModel::TABLE)
					->where([MbCpuCoolerModel::REF_CPU_ID->value => $result[CpuModel::ID->value]])
					->select(MbCpuCoolerModel::values())
					->fetch_all(MYSQLI_ASSOC);
			}
		}

		private function get_cpu(): array {
			return $this->results = $this->db
				->for(CpuModel::TABLE)
				->where($this->query)
				->order([CpuModel::DATE_AQUIRED->value => "DESC"])
				->select(CpuModel::values())
				->fetch_all(MYSQLI_ASSOC);
		}

		public function main(): Response {
			// Set properties as "searchable"
			parent::make_wildcard_search(CpuModel::VENDOR_NAME->value, $this->query);
			parent::make_wildcard_search(CpuModel::VENDOR_MODEL->value, $this->query);

			// Get hardware
			$this->get_cpu();

			// Resolve hardware relationships
			$this->get_motherboards();

			// Return 404 Not Found if response array is empty
			return new Response($this->results, $this->results ? 200 : 404);
		}
	}