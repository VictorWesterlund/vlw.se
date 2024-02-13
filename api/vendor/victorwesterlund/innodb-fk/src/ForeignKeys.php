<?php

    namespace victorwesterlund;

    use \victorwesterlund\xEnum;
    use \libmysqldriver\MySQL as MySQLDriver;

	require_once "../vendor/autoload.php";

    enum InnoDB_ForeignModel: string {
        use xEnum;

        const TABLE = "INNODB_SYS_FOREIGN";

        case ID       = "ID";
        case FOR_NAME = "FOR_NAME";
        case REF_NAME = "REF_NAME";
        case N_COLS   = "N_COLS";
        case TYPE     = "TYPE";
    }

    enum InnoDB_ForeignColsModel: string {
        use xEnum;

        const TABLE = "INNODB_SYS_FOREIGN_COLS";

        case ID           = "ID";
        case FOR_COL_NAME = "FOR_COL_NAME";
        case REF_COL_NAME = "REF_COL_NAME";
        case POS          = "POS";
    }

    class ForeignKeys {
        // Database containing InnoDB foreign key tables
        const DATABASE_NAME = "information_schema";

		private MySQLDriver $db;
        // Array will contain all columns in table that have a foreign key constraint
        private array $constraints = [];

        public function __construct() {
            // Initialize MySQL driver
			$this->db = new MySQLDriver(...func_get_args());
        }

        // Return foreign key column constraints from table relationships
        private function resolve_constraints(array $relationships): array {
            $constraints = [];

            $relationship_columns = $this->db->for(InnoDB_ForeignColsModel::TABLE)
                ->with(InnoDB_ForeignColsModel::values());

            // Get referenced key database and column from table relationship
            foreach ($relationships as $relationship) {
                $constraint = $relationship_columns
                    ->limit(1)
                    ->flatten()
                    ->where([
                        // Use relationship id as anchor for resolving a column pair
                        InnoDB_ForeignColsModel::ID->value => $relationship[InnoDB_ForeignModel::ID->value]
                    ])
                    ->select([
                        // Select foreign column and referenced column
                        InnoDB_ForeignColsModel::FOR_COL_NAME->value,
                        InnoDB_ForeignColsModel::REF_COL_NAME->value
                    ]);

                // Convert slash to dot for easier use in further queries
                $database = str_replace("/", ".", $relationship[InnoDB_ForeignModel::REF_NAME->value]);
                $column = $constraint[InnoDB_ForeignColsModel::REF_COL_NAME->value];

                // Use foreign key column as index and referenced database and column as assoc array
                $constraints[$constraint[InnoDB_ForeignColsModel::FOR_COL_NAME->value]] = [$database => $column];
            }

            return $constraints;
        }

		// Get relationships for a database table
		private function resolve_relationships(string $database, string $table): self {
			// Create database/table name string for InnoDB FK id
			$for_name = implode("/", [$database, $table]);

			// Get all key constraint tables for table
			$relationships = $this->db->for(InnoDB_ForeignModel::TABLE)
				->with(InnoDB_ForeignModel::values())
				->where([
					InnoDB_ForeignModel::FOR_NAME->value => $for_name
				])
				->select([
					InnoDB_ForeignModel::ID->value,
					InnoDB_ForeignModel::REF_NAME->value
				]);

			// Resolve table column constraints for relationships
			$this->constraints = $this->resolve_constraints($relationships);
			return $this;
		}

		// Resolve foreign key constraint columns in entity
        private function resolve(string $col, mixed $value): mixed {
            // Return value if there is no foreign key constraint to resolve on column
            if (!array_key_exists($col, $this->constraints)) {
                return $value;
            }

            $fk = $this->constraints[$col];

            // Get database and table name from key
            $database = array_keys($fk)[0];
            // Get database column from value
            $column = array_values($fk)[0];

            // Return all columns for referenced key entity
            return $this->db->for(array_keys($fk)[0])
				->with(null)
                ->where([
                    $column => $value
                ])
                ->limit(1)
                ->flatten()
                ->select("*");
        }

        /* ---- */

        public function get_constraints(): array {
            return $this->constraints;
        }

		// Resolve foreign keys for entities
        public function resolve_all(array $entities): array {
            foreach ($entities as &$entity) {
                foreach ($entity as $col => $value) {
                    $entity[$col] = $this->resolve($col, $value);
                }
            }

            return $entities;
        }

		public function for(string $database, ?string $table = null): self {
			// Table argument was not specified, try to extract it from $database
			if (!$table) {
				// $database string does not contain a db, table spearator
				if (!strpos($database, ".")) {
					throw new Exception("Database and table must be specified `for('db.table')` or call or `for('db', 'table')`");
				}

				// Expand $database and $table from string
				[$database, $table] = explode(".", $database, 2);
			}

			// Get relationships and constraints for table
			$this->resolve_relationships($database, $table);
            return $this;
        }
    }