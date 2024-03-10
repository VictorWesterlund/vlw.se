<?php

	use Reflect\Path;
	use Reflect\Response;
	use ReflectRules\Type;
	use ReflectRules\Rules;
	use ReflectRules\Ruleset;

	use function Reflect\Call;
	use Reflect\Request\Method;

	use VLW\API\Databases\VLWdb\VLWdb;
	use VLW\API\Databases\VLWdb\Models\Messages\MessagesModel;

	require_once Path::root("src/databases/VLWdb.php");
	require_once Path::root("src/databases/models/Messages.php");

	class POST_Messages extends VLWdb {
		protected Ruleset $ruleset;

		public function __construct() {
			parent::__construct();
			$this->ruleset = new Ruleset(strict: true);

			$this->ruleset->POST([
				(new Rules(MessagesModel::EMAIL->value))
					->type(Type::STRING)
					->max(255)
					->default(null),

				(new Rules(MessagesModel::MESSAGE->value))
					->required()
					->type(Type::STRING)
					->min(1)
					->max(parent::MYSQL_TEXT_MAX_LENGTH)
			]);
		}

		// # Responses

		// Return 422 Unprocessable Content error if request validation failed 
		private function resp_rules_invalid(): Response {
			return new Response($this->ruleset->get_errors(), 422);
		}

		// Return a 503 Service Unavailable error if something went wrong with the database call
		private function resp_database_error(): Response {
			return new Response("Failed to get work data, please try again later", 503);
		}

		public function main(): Response {
			//return new Response(["hello" => "maybe"], 500);

			// Bail out if request validation failed
			if (!$this->ruleset->is_valid()) {
				return $this->resp_rules_invalid();
			}

			// Generate UUID for entity
			$id = parent::gen_uuid4();

			// Attempt to create new entity
			$insert = $this->db->for(MessagesModel::TABLE)
				->insert([
					MessagesModel::ID->value                      => $id,
					MessagesModel::EMAIL->value                   => $_POST["email"],
					MessagesModel::MESSAGE->value                 => $_POST["message"],
					MessagesModel::DATE_TIMESTAMP_CREATED->value  => time(),
				]);

			// Bail out if insert failed
			if (!$insert) {
				return $this->resp_database_error();
			}

			// Return 201 Created and entity id
			return new Response($id, 201);
		}
	}