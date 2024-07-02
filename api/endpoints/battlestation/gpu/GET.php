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
	use VLW\API\Databases\VLWdb\Models\Battlestation\GpuModel;
	use VLW\API\Databases\VLWdb\Models\Battlestation\Config\MbGpuModel;

	require_once Path::root("src/databases/VLWdb.php");
	require_once Path::root("src/databases/models/Battlestation/Gpu.php");
	require_once Path::root("src/databases/models/Battlestation/Config/MbGpu.php");

	class GET_BattlestationGpu extends VLWdb {
		private const REL_MOTHERBOARDS = "motherboards";

		protected Ruleset $ruleset;

		private array $query;
		private array $results = [];

		public function __construct() {
			$this->ruleset = new Ruleset(strict: true);

			$this->ruleset->GET([
				(new Rules(GpuModel::ID->value))
					->type(Type::STRING)
					->min(parent::UUID_LENGTH)
					->max(parent::UUID_LENGTH),

				(new Rules(GpuModel::MEMORY->value))
					->type(Type::NUMBER)
					->min(1),

				(new Rules(GpuModel::VENDOR_NAME->value))
					->type(Type::STRING)
					->min(1)
					->max(parent::MYSQL_VARCHAR_MAX_LENGTH),

				(new Rules(GpuModel::VENDOR_MODEL->value))
					->type(Type::STRING)
					->min(1)
					->max(parent::MYSQL_VARCHAR_MAX_LENGTH),

				(new Rules(GpuModel::VENDOR_CHIP_NAME->value))
					->type(Type::STRING)
					->min(1)
					->max(parent::MYSQL_VARCHAR_MAX_LENGTH),

				(new Rules(GpuModel::VENDOR_CHIP_MODEL->value))
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
					->for(MbGpuModel::TABLE)
					->where([MbGpuModel::REF_GPU_ID->value => $result[GpuModel::ID->value]])
					->select(MbGpuModel::values())
					->fetch_all(MYSQLI_ASSOC);
			}
		}

		private function get_gpu(): array {
			return $this->results = $this->db
				->for(GpuModel::TABLE)
				->where($this->query)
				->order([GpuModel::DATE_AQUIRED->value => "DESC"])
				->select(GpuModel::values())
				->fetch_all(MYSQLI_ASSOC);
		}

		public function main(): Response {
			// Set properties as "searchable"
			parent::make_wildcard_search(GpuModel::VENDOR_NAME->value, $this->query);
			parent::make_wildcard_search(GpuModel::VENDOR_MODEL->value, $this->query);
			parent::make_wildcard_search(GpuModel::VENDOR_CHIP_NAME->value, $this->query);
			parent::make_wildcard_search(GpuModel::VENDOR_CHIP_MODEL->value, $this->query);

			// Get hardware
			$this->get_gpu();

			// Resolve hardware relationships
			$this->get_motherboards();

			// Return 404 Not Found if response array is empty
			return new Response($this->results, $this->results ? 200 : 404);
		}
	}