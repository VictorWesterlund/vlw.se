<?php

	namespace VLW\API\Databases\VLWdb\Models\Battlestation\Config;

	use victorwesterlund\xEnum;

	enum SocketTypeEnum {
		use xEnum;

		case SLOTTED;
		case INTEGRATED;
	}

	enum MbCpuCoolerModel: string {
		use xEnum;
		
		const TABLE = "config_mb_cpu_cooler";

		case REF_MB_ID     = "ref_mb_id";
		case REF_CPU_ID    = "ref_cpu_id";
		case REF_COOLER_ID = "ref_cooler_id";
		case SOCKET        = "socket";
		case SOCKET_TYPE   = "socket_type";
	}