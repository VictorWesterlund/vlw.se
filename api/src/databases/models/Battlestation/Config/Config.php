<?php

	namespace VLW\API\Databases\VLWdb\Models\Battlestation\Config;

	use victorwesterlund\xEnum;

	enum ConfigModel: string {
		use xEnum;
		
		const TABLE = "config";

		case REF_MB_ID     = "ref_mb_id";
		case FRIENDLY_NAME = "friendly_name";
		case DATE_BUILT    = "date_built";
	}