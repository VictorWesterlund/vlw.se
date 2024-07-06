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
		StorageModel,
		StorageDiskTypeEnum,
		StorageDiskInterfaceEnum,
		StorageDiskFormfactorEnum
	};
	use VLW\API\Databases\VLWdb\Models\Battlestation\Config\MbStorageModel;

	require_once Path::root("src/databases/VLWdb.php");
	require_once Path::root("src/databases/models/Battlestation/Storage.php");
	require_once Path::root("src/databases/models/Battlestation/Config/MbStorage.php");

	class GET_BattlestationStorage extends VLWdb {
		private const REL_MOTHERBOARDS = "motherboards";

		protected Ruleset $ruleset;

		private array $query;
		private array $results = [];

		public function __construct() {
			$this->ruleset = new Ruleset(strict: true);

			$this->ruleset->GET([
				(new Rules(StorageModel::ID->value))
					->type(Type::STRING)
					->min(parent::UUID_LENGTH)
					->max(parent::UUID_LENGTH),

				(new Rules(StorageModel::DISK_TYPE->value))
					->type(Type::ENUM, StorageDiskTypeEnum::names()),

				(new Rules(StorageModel::DISK_SIZE->value))
					->type(Type::NUMBER)
					->min(1)
					->max(parent::MYSQL_INT_MAX_LENGTH),

				(new Rules(StorageModel::DISK_INTERFACE->value))
					->type(Type::ENUM, StorageDiskInterfaceEnum::names()),

				(new Rules(StorageModel::DISK_FORMFACTOR->value))
					->type(Type::ENUM, StorageDiskFormfactorEnum::names()),

				(new Rules(StorageModel::VENDOR_NAME->value))
					->type(Type::STRING)
					->min(1)
					->max(parent::MYSQL_VARCHAR_MAX_LENGTH),

				(new Rules(StorageModel::VENDOR_MODEL->value))
					->type(Type::STRING)
					->min(1)
					->max(parent::MYSQL_VARCHAR_MAX_LENGTH),

				(new Rules(StorageModel::IS_RETIRED->value))
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
					->for(MbStorageModel::TABLE)
					->where([MbStorageModel::REF_STORAGE_ID->value => $result[StorageModel::ID->value]])
					->select(MbStorageModel::values())
					->fetch_all(MYSQLI_ASSOC);
			}
		}

		private function get_storage(): array {
			return $this->results = $this->db
				->for(StorageModel::TABLE)
				->where($this->query)
				->order([StorageModel::DATE_AQUIRED->value => "DESC"])
				->select(StorageModel::values())
				->fetch_all(MYSQLI_ASSOC);
		}

		public function main(): Response {
			// Set properties as "searchable"
			parent::make_wildcard_search(StorageModel::VENDOR_NAME->value, $this->query);
			parent::make_wildcard_search(StorageModel::VENDOR_MODEL->value, $this->query);

			// Get hardware
			$this->get_storage();

			// Resolve hardware relationships
			$this->get_motherboards();

			// Return 404 Not Found if response array is empty
			return new Response($this->results, $this->results ? 200 : 404);
		}
	}