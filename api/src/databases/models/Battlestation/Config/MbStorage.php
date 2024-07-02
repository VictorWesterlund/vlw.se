<?php

	namespace VLW\API\Databases\VLWdb\Models\Battlestation\Config;

	use victorwesterlund\xEnum;

	enum MbStorageSlotFormfactorEnum: string {
		use xEnum;

		case TWODOTFIVE   = "2.5";
		case THREEDOTFIVE = "3.5";
		case MDOTTWO      = "M.2";
		case EXTERNAL     = "EXTERNAL";
	}

	enum MbStorageModel: string {
		use xEnum;
		
		const TABLE = "config_mb_storage";

		case REF_MB_ID       = "ref_mb_id";
		case REF_STORAGE_ID  = "ref_storage_id";
		case INTERFACE       = "interface";
		case SLOT_FORMFACTOR = "slot_formfactor";
	}