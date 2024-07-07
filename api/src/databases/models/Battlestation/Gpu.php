<?php

	namespace VLW\API\Databases\VLWdb\Models\Battlestation;

	use victorwesterlund\xEnum;

	enum GpuModel: string {
		use xEnum;
		
		const TABLE = "gpu";

		case ID                = "id";
		case MEMORY            = "memory";
		case VENDOR_NAME       = "vendor_name";
		case VENDOR_MODEL      = "vendor_model";
		case VENDOR_CHIP_NAME  = "vendor_chip_name";
		case VENDOR_CHIP_MODEL = "vendor_chip_model";
		case DATE_AQUIRED      = "date_aquired";
		case IS_RETIRED        = "is_retired";
	}