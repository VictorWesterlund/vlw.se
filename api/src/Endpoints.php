<?php

	namespace VLW\API;

	// Default string to return when a DELETE request is successful
	const RESP_DELETE_OK = "OK";

	// Enum of all available VLW endpoints grouped by category
	enum Endpoints: string {
		case SEARCH = "/search";

		case MESSAGES = "/messages";

		case WORK         = "/work";
		case WORK_TAGS    = "/work/tags";
		case WORK_ACTIONS = "/work/actions";
	}