<?php

	namespace VLW\API\Databases\VLWdb;

	use Reflect\Path;
	use Reflect\Request;
	use Reflect\Response;
	use ReflectRules\Ruleset;

	use libmysqldriver\MySQL;

	enum Databases: string {
		case VLW           = "vlw";
		case BATTLESTATION = "battlestation";
	}

	class VLWdb {
		const UUID_LENGTH = 36;

		const MYSQL_INT_MAX_LENGTH     = 2147483647;
		const MYSQL_TEXT_MAX_LENGTH    = 65538;
		const MYSQL_VARCHAR_MAX_LENGTH = 255;
		const MYSQL_TINYINT_MAX_LENGTH = 255;

		protected readonly MySQL $db;

		public function __construct(Databases $database, Ruleset $ruleset) {
			// Validate provided Ruleset before attempting to connect to the database
			self::eval_ruleset_or_exit($ruleset);

			// Create new MariaDB connection
			$this->db = new MySQL(
				$_ENV["connect"]["host"],
				$_ENV["connect"]["user"],
				$_ENV["connect"]["pass"],
				$_ENV["databases"][$database->value],
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

		// Mutate the value by array key $property_name into a libmysqldriver\MySQL custom operator
		// https://github.com/VictorWesterlund/php-libmysqldriver?tab=readme-ov-file#define-custom-operators
		public static function make_wildcard_search(string $property_name, array &$filters): array {
			// Bail out if property name is not set in filters array or if its value is null
			if (!array_key_exists($property_name, $filters) || $filers[$property_name] !== null) {
				return $filters;
			}

			// Mutate filter value into a custom operator array
			$filters[$property_name] = [
				"LIKE" => "%{$filters[$property_name]}%"
			];

			return $filters;
		}

		// Bail out if provided ReflectRules\Ruleset is invalid
		private static function eval_ruleset_or_exit(Ruleset $ruleset): ?Response {
			return !$ruleset->is_valid() ? new Response($ruleset->get_errors(), 422) : null;
		}
	}