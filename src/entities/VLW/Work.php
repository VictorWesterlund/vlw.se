<?php

	namespace VLW\Entities\Work;

	use Vegvisir\Path;

	require_once Path::root("src/entities/Entity.php");

	class WorkEntity extends Entity {
		private array $individuals = [];
		private array $signatures = [];
		private array $documents = [];
		private array $studies = [];
		private array $notes = [];

		public function __construct(string $id) {
			parent::__construct(Endpoints::WORK, $id);
		}

		public function signatures(): array {
			if ($this->signatures) {
				return $this->signatures;
			}

			foreach ($this->resolve(Endpoints::ICELDB_ANALYSES_SIGNATURES, ["ref_analysis_id" => $this->id]) as $rel) {
				$this->signatures[$rel["id"]] = $rel;
				$this->signatures[$rel["id"]]["user"] = $this->resolve(Endpoints::ICELDB_USERS, ["ref_user_id" => $rel["ref_user_id"]])[0];
			}

			return $this->signatures;
		}

		public function documents(): array {
			if ($this->documents) {
				return $this->documents;
			}

			$this->documents = $this->resolve(Endpoints::ICELDB_ANALYSES_DOCUMENTS, ["ref_analysis_id" => $this->id]);
			return $this->documents;
		}
 
		public function studies(): array {
			if ($this->studies) {
				return $this->studies;
			}

			foreach ($this->resolve(Endpoints::ICELDB_STUDIES_ANALYSES, ["ref_analysis_id" => $this->id]) as $rel) {
				$this->studies[] = $this->resolve(Endpoints::ICELDB_STUDIES, ["id" => $rel["ref_study_id"]]);
			}

			return $this->studies;
		}

		public function notes(): array {
			if ($this->notes) {
				return $this->notes;
			}

			$this->notes = $this->resolve(Endpoints::ICELDB_ANALYSES_NOTES, ["ref_analysis_id" => $this->id]);
			return $this->notes;
		}

		public function individuals(): array {
			if ($this->individuals) {
				return $this->individuals;
			}

			foreach ($this->resolve(Endpoints::ICELDB_ANALYSES_INDIVIDUALS, ["ref_analysis_id" => $this->id]) as $rel) {
				$this->individuals[] = $this->resolve(Endpoints::ICELDB_INDIVIDUALS, ["id" => $rel["ref_individual_id"]])[0];
			}

			return $this->individuals;
		}
	}