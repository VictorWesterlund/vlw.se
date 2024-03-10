<?php

	namespace VLW\API\Databases\VLWdb\Models\Media;

	use victorwesterlund\xEnum;

	enum MediaTypeEnum: string {
		use xEnum;

		case BLOB  = "BLOB";
		case IMAGE = "IMAGE";
	}

	enum MediaModel: string {
		const TABLE = "media";

		case ID                     = "id";
		case NAME                   = "name";
		case TYPE                   = "type";
		case MIME                   = "mime";
		case EXTENSION              = "extension";
		case SRCSET                 = "srcset";
		case DATE_TIMESTAMP_CREATED = "date_timestamp_created";
	}