<?php

	namespace VLW\API\Databases\VLWdb\Models\Battlestation;

	enum CoolersModel: string {
		const TABLE = "coolers";

		case ID            = "id";
		case TYPE_LIQUID   = "type_liquid";
		case SIZE_FAN      = "size_fan";
		case SIZE_RADIATOR = "size_radiator";
		case VENDOR_NAME   = "vendor_name";
		case VENDOR_MODEL  = "vendor_model";
		case DATE_AQUIRED  = "date_aquired";
		case DATE_RETIRED  = "date_retired";
	}