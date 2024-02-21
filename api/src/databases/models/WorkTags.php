<?php

	namespace VLW\API\Databases\VLWdb\Models\Work;

	use victorwesterlund\xEnum;

	enum WorkTagsNameEnum {
		use xEnum;

		case RELEASE;
	}

	enum WorkTagsModel: string {
		const TABLE = "work_tags";

		case ANCHOR = "anchor";
		case NAME   = "name";
	}