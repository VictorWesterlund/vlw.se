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
		DramModel,
		DramFormfactorEnum,
		DramTechnologyEnum
	};
	use VLW\API\Databases\VLWdb\Models\Battlestation\Config\MbDramModel;

	require_once Path::root("src/databases/VLWdb.php");
	require_once Path::root("src/databases/models/Battlestation/Dram.php");
	require_once Path::root("src/databases/models/Battlestation/Config/MbDram.php");

	class GET_BattlestationDram extends VLWdb {
		private const REL_MOTHERBOARDS = "motherboards";

		protected Ruleset $ruleset;

		private array $query;
		private array $results = [];

		public function __construct() {
			$this->ruleset = new Ruleset(strict: true);

			$this->ruleset->GET([
				(new Rules(DramModel::ID->value))
					->type(Type::STRING)
					->min(parent::UUID_LENGTH)
					->max(parent::UUID_LENGTH),

				(new Rules(DramModel::CAPACITY->value))
					->type(Type::NUMBER)
					->min(1),

				(new Rules(DramModel::SPEED->value))
					->type(Type::NUMBER)
					->min(1),

				(new Rules(DramModel::FORMFACTOR->value))
					->type(Type::ENUM, DramFormfactorEnum::names()),

				(new Rules(DramModel::TECHNOLOGY->value))
					->type(Type::ENUM, DramTechnologyEnum::names()),

				(new Rules(DramModel::ECC->value))
					->type(Type::BOOLEAN),

				(new Rules(DramModel::BUFFERED->value))
					->type(Type::BOOLEAN),

				(new Rules(DramModel::VENDOR_NAME->value))
					->type(Type::STRING)
					->min(1)
					->max(parent::MYSQL_VARCHAR_MAX_LENGTH),

				(new Rules(DramModel::VENDOR_MODEL->value))
					->type(Type::STRING)
					->min(1)
					->max(parent::MYSQL_VARCHAR_MAX_LENGTH),

				(new Rules(DramModel::IS_RETIRED->value))
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
					->for(MbDramModel::TABLE)
					->where([MbDramModel::REF_DRAM_ID->value => $result[DramModel::ID->value]])
					->select(MbDramModel::values())
					->fetch_all(MYSQLI_ASSOC);
			}
		}

		private function get_dram(): array {
			return $this->results = $this->db
				->for(DramModel::TABLE)
				->where($this->query)
				->order([DramModel::DATE_AQUIRED->value => "DESC"])
				->select(DramModel::values())
				->fetch_all(MYSQLI_ASSOC);
		}

		public function main(): Response {
			// Set properties as "searchable"
			parent::make_wildcard_search(DramModel::VENDOR_NAME->value, $this->query);
			parent::make_wildcard_search(DramModel::VENDOR_MODEL->value, $this->query);

			// Get hardware
			$this->get_dram();

			// Resolve hardware relationships
			$this->get_motherboards();

			// Return 404 Not Found if response array is empty
			return new Response($this->results, $this->results ? 200 : 404);
		}
	}