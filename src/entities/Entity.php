<?php

	namespace VLW\Entities;

	use Vegvisir\Path;
	use Reflect\Response;

	use VLW\Client\API;
	use VLW\API\Endpoints;

	require_once Path::root("src/client/API.php");
	require_once Path::root("api/src/Endpoints.php");

	interface EntityInterface {}

	abstract class Entity implements EntityInterface {
		public const ENTITY_ID = "id";

		public readonly ?string $id;
		public readonly object $entity;
		public readonly Response $response;
		
		protected readonly Api $api;
		protected readonly Endpoints $endpoint;

		public function __construct(Endpoints $endpoint, ?string $id) {
			$this->id = $id;
			$this->api = new Api();
			$this->endpoint = $endpoint;

			$this->resolve_entity_by_id();
		}

		private function resolve_entity_by_id() {
			// Bail out wit a dummy Response if no id was provided
			if (!$this->id) {
				$this->response = new Response("", 404);
				return;
			}

			$this->response = $this->api
				->call($this->endpoint->value)
				->params([self::ENTITY_ID => $this->id])
				->get();

			// Load response into entity object if successful
			if ($this->response->ok) {
				$this->entity = (object) $this->response->json()[0];
			}
		}

		public function resolve(Endpoints $endpoint, array $params): array {
			$response = $this->api->call($endpoint->value)->params($params)->get();

			return $response->ok ? $response->json() : [];
		}
	}