<?php

	use Reflect\Call;
	use Reflect\Path;
	use Reflect\Response;
	use ReflectRules\Type;
	use ReflectRules\Rules;
	use ReflectRules\Ruleset;

	use VLW\API\Endpoints;

	use VLW\API\Databases\VLWdb\{
		VLWdb,
		Databases
	};
	use VLW\API\Databases\VLWdb\Models\Work\WorkModel;

	require_once Path::root("src/Endpoints.php");
	require_once Path::root("src/databases/VLWdb.php");
	require_once Path::root("src/databases/models/Work/Work.php");

	class GET_Search extends VLWdb {
		const GET_QUERY = "q";

		protected Ruleset $ruleset;

		public function __construct() {
			$this->ruleset = new Ruleset(strict: true);

			$this->ruleset->GET([
				(new Rules(self::GET_QUERY))
					->required()
					->type(Type::STRING)
					->min(1)
					->max(parent::MYSQL_VARCHAR_MAX_LENGTH)
			]);

			parent::__construct(Databases::VLW, $this->ruleset);
		}

		private function search_work(): Response {
			return (new Call(Endpoints::WORK->value))->params([
				WorkModel::TITLE->value      => $_GET[self::GET_QUERY],
				WorkModel::SUMMARY->value    => $_GET[self::GET_QUERY]
			])->get();
		}

		public function main(): Response {
			$results = [
				Endpoints::WORK->value => $this->search_work()->output()
			];

			// Calculate the total number of results from all searched endpoints
			$num_results = array_sum(array_map(fn(array $result): int => count($result), array_values($results)));

			// Return 404 if no search results
			return new Response($results, $num_results > 0 ? 200 : 404);
		}
	}