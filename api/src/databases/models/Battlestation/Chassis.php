<?php

	namespace VLW\API\Databases\VLWdb\Models\Battlestation;

	use victorwesterlund\xEnum;

	enum ChassisModel: string {
		use xEnum;
		
		const TABLE = "chassis";

		case ID                    = "id";
		case VENDOR_NAME           = "vendor_name";
		case VENDOR_MODEL          = "vendor_model";
		case STORAGE_TWOINCHFIVE   = "storage_2i5hi";
		case STORAGE_THREEINCHFIVE = "storage_3i5hi";
		case DATE_AQUIRED          = "date_aquired";
		case IS_RETIRED            = "is_retired";
	}