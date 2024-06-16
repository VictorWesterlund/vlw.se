<?php

	namespace VLW\Client;
	
	use Reflect\Client;	

	class API extends Client {
		// ISO 8601: YYYY-MM-DD
		public const DATE_FORMAT = "Y-m-d";

		public function __construct() {
			parent::__construct(
				$_ENV["api"]["base_url"],
				$_ENV["api"]["api_key"],
				$_ENV["api"]["verify_peer"]
			);
		}
	}