<?php

	namespace VLW\API\Databases\VLWdb\Models\Battlestation;

	use victorwesterlund\xEnum;

	enum MbFormfactorEnum {
		use xEnum;

		case ATX;
		case MTX;
		case ITX;
		case LAPTOP;
	}

	enum MbModel: string {
		use xEnum;

		const TABLE = "mb";

		case ID                = "id";
		case FORMFACTOR        = "formfactor";
		case VENDOR_NAME       = "vendor_name";
		case VENDOR_MODEL      = "vendor_model";
		case NETWORK_ETHERNET  = "network_ethernet";
		case NETWORK_WLAN      = "network_wlan";
		case NETWORK_BLUETOOTH = "network_bluetooth";
		case DATE_AQUIRED      = "date_aquired";
		case IS_RETIRED        = "is_retired";
	}