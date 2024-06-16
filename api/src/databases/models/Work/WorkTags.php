<?php

	namespace VLW\API\Databases\VLWdb\Models\Work;

	use victorwesterlund\xEnum;

	enum WorkTagsNameEnum {
		use xEnum;

		case VLW;
		case RELEASE;
		case WEBSITE;
	}

	enum WorkTagsModel: string {
		const TABLE = "work_tags";

		case REF_WORK_ID = "ref_work_id";
		case NAME        = "name";
	}