<?php

	namespace VLW\API;

	enum Endpoints: string {
		case WORK         = "/work";
		case SEARCH       = "/search";
		case MESSAGES     = "/messages";
		case WORK_TAGS    = "/work/tags";
		case WORK_ACTIONS = "/work/actions";
	}