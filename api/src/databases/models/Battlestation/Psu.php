<?php

	namespace VLW\API\Databases\VLWdb\Models\Battlestation;

	use victorwesterlund\xEnum;

	enum EightyplusRatingEnum {
		use xEnum;

		case BASE;
		case BRONZE;
		case SILVER;
		case GOLD;
		case PLATINUM;
		case TITANIUM;
	}

	enum PsuModel: string {
		use xEnum;
		
		const TABLE = "psu";

		case ID                = "id";
		case POWER             = "power";
		case VENDOR_NAME       = "vendor_name";
		case VENDOR_MODEL      = "vendor_model";
		case TYPE_MODULAR      = "type_modular";
		case EIGHTYPLUS_RATING = "80plus_rating";
		case DATE_AQUIRED      = "date_aquired";
		case DATE_RETIRED      = "date_retired";
	}