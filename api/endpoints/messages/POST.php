<?php

	use Reflect\Path;
	use Reflect\Response;
	use ReflectRules\Type;
	use ReflectRules\Rules;
	use ReflectRules\Ruleset;

	use VLW\API\Databases\VLWdb\VLWdb;
	use VLW\API\Databases\VLWdb\Models\Messages\MessagesModel;

	require_once Path::root("src/databases/VLWdb.php");
	require_once Path::root("src/databases/models/Messages/Messages.php");

	class POST_Messages extends VLWdb {
		protected Ruleset $ruleset;

		public function __construct() {
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

			parent::__construct($this->ruleset);
		}

		public function main(): Response {
			// Use copy of request body as entity
			$entity = $_POST;

			$entity[MessagesModel::ID->value] = parent::gen_uuid4();
			$entity[MessagesModel::DATE_CREATED->value] = time();

			return $this->db->for(MessagesModel::TABLE)->insert($entity) === true
				? new Response($entity[MessagesModel::ID->value], 201)
				: new Response("Failed to create message", 500);
		}
	}