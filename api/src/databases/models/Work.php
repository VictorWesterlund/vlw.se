<?php

	namespace VLW\API\Databases\VLWdb\Models\Work;

	enum WorkModel: string {
		const TABLE = "work";

		case ID                      = "id";
		case TITLE                   = "title";
		case SUMMARY                 = "summary";
		case DATE_YEAR               = "date_year";
		case DATE_MONTH              = "date_month";
		case DATE_DAY                = "date_day";
		case DATE_TIMESTAMP_MODIFIED = "date_timestamp_modified";
		case DATE_TIMESTAMP_CREATED  = "date_timestamp_created";
	}

	enum WorkTagsModel: string {
		const TABLE = "work_tags";

		case ID   = "anchor";
		case NAME = "name";
	}

	enum WorkActionsModel: string {
		const TABLE = "work_actions";
		
		case ID           = "anchor";
		case DISPLAY_TEXT = "display_text";
		case HREF         = "href";
		case EXTERNAL     = "external";
	}