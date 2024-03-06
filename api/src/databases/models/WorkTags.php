<?php

	namespace VLW\API\Databases\VLWdb\Models\Work;

	use victorwesterlund\xEnum;

	enum WorkTagsNameEnum: string {
		use xEnum;

		case VLW     = "VLW";
		case RELEASE = "RELEASE";
		case WEBSITE = "WEBSITE";
	}

	enum WorkTagsModel: string {
		const TABLE = "work_tags";

		case ANCHOR = "anchor";
		case NAME   = "name";
	}