<?php

	namespace VLW\API\Databases\VLWdb\Models\Battlestation\Config;

	use victorwesterlund\xEnum;

	enum MbPsuModel: string {
		use xEnum;
		
		const TABLE = "config_mb_psu";

		case REF_MB_ID  = "ref_mb_id";
		case REF_PSU_ID = "ref_psu_id";
	}