<?php

	namespace VLW\API\Databases\VLWdb;

	use libmysqldriver\MySQL;

	class VLWdb {
		const UUID_LENGTH = 36;

		const MYSQL_TEXT_MAX_LENGTH = 65538;
		const MYSQL_VARCHAR_MAX_LENGTH = 255;
		const MYSQL_INT_MAX_LENGHT = 2147483647;

		protected MySQL $db;

		public function __construct() {
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

		public static function is_mysqli_result(\mysqli_result|bool $resp): bool {
			return $resp instanceof \mysqli_result;
		}
	}