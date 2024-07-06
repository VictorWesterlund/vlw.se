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
		MbModel,
		MbFormfactorEnum
	};
	use VLW\API\Databases\VLWdb\Models\Battlestation\Config\{
		MbGpuModel,
		MbPsuModel,
		MbDramModel,
		MbStorageModel,
		ChassisMbModel,
		MbCpuCoolerModel
	};

	require_once Path::root("src/databases/VLWdb.php");
	require_once Path::root("src/databases/models/Battlestation/Mb.php");
	require_once Path::root("src/databases/models/Battlestation/Config/MbPsu.php");
	require_once Path::root("src/databases/models/Battlestation/Config/MbGpu.php");
	require_once Path::root("src/databases/models/Battlestation/Config/MbDram.php");
	require_once Path::root("src/databases/models/Battlestation/Config/MbStorage.php");
	require_once Path::root("src/databases/models/Battlestation/Config/ChassisMb.php");
	require_once Path::root("src/databases/models/Battlestation/Config/MbCpuCooler.php");

	class GET_BattlestationMb extends VLWdb {
		private const REL_CPU     = "cpus";
		private const REL_PSU     = "psus";
		private const REL_GPU     = "gpus";
		private const REL_DRAM    = "dram";
		private const REL_STORAGE = "storage";
		private const REL_CHASSIS = "chassis";

		protected Ruleset $ruleset;

		private array $query;
		private array $results = [];

		public function __construct() {
			$this->ruleset = new Ruleset(strict: true);

			$this->ruleset->GET([
				(new Rules(MbModel::ID->value))
					->type(Type::STRING)
					->min(parent::UUID_LENGTH)
					->max(parent::UUID_LENGTH),

				(new Rules(MbModel::FORMFACTOR->value))
					->type(Type::ENUM, MbFormfactorEnum::names()),

				(new Rules(MbModel::VENDOR_NAME->value))
					->type(Type::STRING)
					->min(1)
					->max(parent::MYSQL_VARCHAR_MAX_LENGTH),

				(new Rules(MbModel::VENDOR_MODEL->value))
					->type(Type::STRING)
					->min(1)
					->max(parent::MYSQL_VARCHAR_MAX_LENGTH),

				(new Rules(MbModel::NETWORK_ETHERNET->value))
					->type(Type::NULL)
					->type(Type::STRING)
					->min(1)
					->max(parent::MYSQL_VARCHAR_MAX_LENGTH),

				(new Rules(MbModel::NETWORK_WLAN->value))
					->type(Type::NULL)
					->type(Type::STRING)
					->min(1)
					->max(parent::MYSQL_VARCHAR_MAX_LENGTH),

				(new Rules(MbModel::NETWORK_BLUETOOTH->value))
					->type(Type::NULL)
					->type(Type::STRING)
					->min(1)
					->max(parent::MYSQL_VARCHAR_MAX_LENGTH),

				(new Rules(MbModel::IS_RETIRED->value))
					->type(Type::BOOLEAN)
			]);

			parent::__construct(Databases::BATTLESTATION, $this->ruleset);

			// Use a copy of search parameters
			$this->query = $_GET;
		}

		private function get_chassis(): void {
			foreach ($this->results as &$result) {
				// Get motherboard id from relationship by chassis id
				$result[self::REL_CHASSIS] = $this->db
					->for(ChassisMbModel::TABLE)
					->where([ChassisMbModel::REF_MB_ID->value => $result[MbModel::ID->value]])
					->select(ChassisMbModel::values())
					->fetch_all(MYSQLI_ASSOC);
			}
		}

		private function get_psu(): void {
			foreach ($this->results as &$result) {
				// Get motherboard id from relationship by chassis id
				$result[self::REL_PSU] = $this->db
					->for(MbPsuModel::TABLE)
					->where([MbPsuModel::REF_MB_ID->value => $result[MbModel::ID->value]])
					->select(MbPsuModel::values())
					->fetch_all(MYSQLI_ASSOC);
			}
		}

		private function get_cpu(): void {
			foreach ($this->results as &$result) {
				// Get motherboard id from relationship by chassis id
				$result[self::REL_CPU] = $this->db
					->for(MbCpuCoolerModel::TABLE)
					->where([MbCpuCoolerModel::REF_MB_ID->value => $result[MbModel::ID->value]])
					->select(MbCpuCoolerModel::values())
					->fetch_all(MYSQLI_ASSOC);
			}
		}

		private function get_gpu(): void {
			foreach ($this->results as &$result) {
				// Get motherboard id from relationship by chassis id
				$result[self::REL_GPU] = $this->db
					->for(MbGpuModel::TABLE)
					->where([MbGpuModel::REF_MB_ID->value => $result[MbModel::ID->value]])
					->select(MbGpuModel::values())
					->fetch_all(MYSQLI_ASSOC);
			}
		}

		private function get_dram(): void {
			foreach ($this->results as &$result) {
				// Get motherboard id from relationship by chassis id
				$result[self::REL_DRAM] = $this->db
					->for(MbDramModel::TABLE)
					->where([MbDramModel::REF_MB_ID->value => $result[MbModel::ID->value]])
					->select(MbDramModel::values())
					->fetch_all(MYSQLI_ASSOC);
			}
		}

		private function get_storage(): void {
			foreach ($this->results as &$result) {
				// Get motherboard id from relationship by chassis id
				$result[self::REL_STORAGE] = $this->db
					->for(MbStorageModel::TABLE)
					->where([MbStorageModel::REF_MB_ID->value => $result[MbModel::ID->value]])
					->select(MbStorageModel::values())
					->fetch_all(MYSQLI_ASSOC);
			}
		}

		// ----

		private function get_motherboards(): array {
			return $this->results = $this->db
				->for(MbModel::TABLE)
				->where($this->query)
				->order([MbModel::DATE_AQUIRED->value => "DESC"])
				->select(MbModel::values())
				->fetch_all(MYSQLI_ASSOC);
		}

		public function main(): Response {
			// Set properties as "searchable"
			parent::make_wildcard_search(MbModel::VENDOR_NAME->value, $this->query);
			parent::make_wildcard_search(MbModel::VENDOR_MODEL->value, $this->query);

			// Get hardware
			$this->get_motherboards();
			
			// Resolve hardware relationships
			$this->get_chassis();
			$this->get_cpu();
			$this->get_psu();
			$this->get_gpu();
			$this->get_dram();
			$this->get_storage();

			// Return 404 Not Found if response array is empty
			return new Response($this->results, $this->results ? 200 : 404);
		}
	}