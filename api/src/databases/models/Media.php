<?php

	namespace VLW\API\Databases\VLWdb\Models\Media;

	use victorwesterlund\xEnum;

	enum MediaTypeEnum {
		use xEnum;

		case IMAGE;
	}

	enum MediaModel: string {
		const TABLE = "media";

		case ID   = "id";
		case TYPE = "type";
		case MIME = "mime";
	}