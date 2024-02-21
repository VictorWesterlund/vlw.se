<?php

	namespace VLW\API\Databases\VLWdb\Models\WorkPermalinks;
	
	enum WorkPermalinksModel: string {
		const TABLE = "work_permalinks";
		
		case SLUG                   = "slug";
		case ANCHOR                 = "anchor";
		case DATE_TIMESTAMP_CREATED = "date_timestamp_created";
	}