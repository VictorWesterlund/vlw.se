<?php

	namespace VLW\API\Databases\VLWdb\Models\Work;

	enum WorkModel: string {
		const TABLE = "work";

		case ID            = "id";
		case TITLE         = "title";
		case SUMMARY       = "summary";
		case COVER_SRCSET  = "cover_srcset";
		case IS_LISTABLE   = "is_listable";
		case IS_READABLE   = "is_readable";
		case DATE_YEAR     = "date_year";
		case DATE_MONTH    = "date_month";
		case DATE_DAY      = "date_day";
		case DATE_MODIFIED = "date_modified";
		case DATE_CREATED  = "date_created";
	}