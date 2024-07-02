<?php

	namespace VLW\API\Databases\VLWdb\Models\Battlestation\Config;

	use victorwesterlund\xEnum;

	enum SocketTypeModel {
		use xEnum;

		case SLOTTED;
		case INTEGRATED;
	}

	enum MbDramModel: string {
		use xEnum;

		const TABLE = "config_mb_dram";

		case REF_MB_ID   = "ref_mb_id";
		case REF_DRAM_ID = "ref_dram_id";
		case SOCKET      = "socket";
		case SOCKET_TYPE = "socket_type";
	}