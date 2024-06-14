<?php

	namespace VLW\API\Databases\VLWdb;

	use Reflect\ENV;
	use Reflect\Path;
	use Reflect\Request;
	use Reflect\Response;
	use ReflectRules\Ruleset;

	use libmysqldriver\MySQL;

	class VLWdb {
		const UUID_LENGTH = 36;

		const MYSQL_TEXT_MAX_LENGTH    = 65538;
		const MYSQL_VARCHAR_MAX_LENGTH = 255;
		const MYSQL_INT_MAX_LENGHT     = 2147483647;

		protected readonly MySQL $db;

		public function __construct(Ruleset $ruleset) {
			// Validate provided Ruleset before attempting to connect to the database
			self::eval_ruleset_or_exit($ruleset);

			// Create new MariaDB connection
			$this->db = new MySQL(
				$_ENV["vlwdb"]["mariadb_host"],
				$_ENV["vlwdb"]["mariadb_user"],
				$_ENV["vlwdb"]["mariadb_pass"],
				$_ENV["vlwdb"]["mariadb_db"],
			);
		}

		// Generate and return UUID4 string
		public static function gen_uuid4(): string {
			return sprintf("%04x%04x-%04x-%04x-%04x-%04x%04x%04x",
				// 32 bits for "time_low"
				mt_rand(0, 0xffff), mt_rand(0, 0xffff),
		
				// 16 bits for "time_mid"
				mt_rand(0, 0xffff),
		
				// 16 bits for "time_hi_and_version",
				// four most significant bits holds version number 4
				mt_rand(0, 0x0fff) | 0x4000,
		
				// 16 bits, 8 bits for "clk_seq_hi_res",
				// 8 bits for "clk_seq_low",
				// two most significant bits holds zero and one for variant DCE1.1
				mt_rand(0, 0x3fff) | 0x8000,
		
				// 48 bits for "node"
				mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
			);
		}

		// Bail out if provided ReflectRules\Ruleset is invalid
		private static function eval_ruleset_or_exit(Ruleset $ruleset): ?Response {
			return !$ruleset->is_valid() ? new Response($ruleset->get_errors(), 422) : null;
		}
	}