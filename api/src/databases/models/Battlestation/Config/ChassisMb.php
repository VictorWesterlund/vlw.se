<?php

	namespace VLW\API\Databases\VLWdb\Models\Battlestation\Config;

	use victorwesterlund\xEnum;

	enum ChassisMbModel: string {
		use xEnum;
		
		const TABLE = "config_chassis_mb";

		case REF_CHASSIS_ID = "ref_chassis_id";
		case REF_MB_ID      = "ref_mb_id";
	}