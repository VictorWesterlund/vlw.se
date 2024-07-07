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

		case BATTLESTATION         = "/battlestation";
		case BATTLESTATION_MB      = "/battlestation/mb";
		case BATTLESTATION_CPU     = "/battlestation/cpu";
		case BATTLESTATION_GPU     = "/battlestation/gpu";
		case BATTLESTATION_PSU     = "/battlestation/psu";
		case BATTLESTATION_DRAM    = "/battlestation/dram";
		case BATTLESTATION_STORAGE = "/battlestation/storage";
		case BATTLESTATION_COOLERS = "/battlestation/coolers";
		case BATTLESTATION_CHASSIS = "/battlestation/chassis";
	}