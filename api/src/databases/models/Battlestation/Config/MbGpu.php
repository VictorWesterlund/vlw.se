<?php

	namespace VLW\API\Databases\VLWdb\Models\Battlestation\Config;

	use victorwesterlund\xEnum;

	enum MbGpuModel: string {
		use xEnum;
		
		const TABLE = "config_mb_gpu";

		case REF_MB_ID  = "ref_mb_id";
		case REF_GPU_ID = "ref_gpu_id";
	}