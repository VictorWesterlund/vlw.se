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
		PsuModel,
		EightyplusRatingEnum
	};
	use VLW\API\Databases\VLWdb\Models\Battlestation\Config\MbPsuModel;

	require_once Path::root("src/databases/VLWdb.php");
	require_once Path::root("src/databases/models/Battlestation/Psu.php");
	require_once Path::root("src/databases/models/Battlestation/Config/MbPsu.php");

	class GET_BattlestationPsu extends VLWdb {
		private const REL_MOTHERBOARDS = "motherboards";

		protected Ruleset $ruleset;

		private array $query;
		private array $results = [];

		public function __construct() {
			$this->ruleset = new Ruleset(strict: true);

			$this->ruleset->GET([
				(new Rules(PsuModel::ID->value))
					->type(Type::STRING)
					->min(parent::UUID_LENGTH)
					->max(parent::UUID_LENGTH),

				(new Rules(PsuModel::POWER->value))
					->type(Type::NUMBER)
					->min(1)
					->max(parent::MYSQL_INT_MAX_LENGTH),

				(new Rules(PsuModel::EIGHTYPLUS_RATING->value))
					->type(Type::ENUM, EightyplusRatingEnum::names()),

				(new Rules(PsuModel::VENDOR_NAME->value))
					->type(Type::STRING)
					->min(1)
					->max(parent::MYSQL_VARCHAR_MAX_LENGTH),

				(new Rules(PsuModel::VENDOR_MODEL->value))
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
					->for(MbPsuModel::TABLE)
					->where([MbPsuModel::REF_PSU_ID->value => $result[PsuModel::ID->value]])
					->select(MbPsuModel::values())
					->fetch_all(MYSQLI_ASSOC);
			}
		}

		private function get_psu(): array {
			return $this->results = $this->db
				->for(PsuModel::TABLE)
				->where($this->query)
				->order([PsuModel::DATE_AQUIRED->value => "DESC"])
				->select(PsuModel::values())
				->fetch_all(MYSQLI_ASSOC);
		}

		public function main(): Response {
			// Set properties as "searchable"
			parent::make_wildcard_search(PsuModel::VENDOR_NAME->value, $this->query);
			parent::make_wildcard_search(PsuModel::VENDOR_MODEL->value, $this->query);

			// Get hardware
			$this->get_psu();

			// Resolve hardware relationships
			$this->get_motherboards();

			// Return 404 Not Found if response array is empty
			return new Response($this->results, $this->results ? 200 : 404);
		}
	}