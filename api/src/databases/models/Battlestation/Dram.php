<?php

	namespace VLW\API\Databases\VLWdb\Models\Battlestation;

	use victorwesterlund\xEnum;

	enum DramFormfactorEnum {
		use xEnum;

		case DIMM;
		case SODIMM;
	}

	enum DramTechnologyEnum {
		use xEnum;

		case DDR4;
		case DDR5;
	}

	enum DramModel: string {
		use xEnum;
		
		const TABLE = "dram";

		case ID           = "id";
		case CAPACITY     = "capacity";
		case SPEED        = "speed";
		case FORMFACTOR   = "formfactor";
		case TECHNOLOGY   = "technology";
		case ECC          = "ecc";
		case BUFFERED     = "buffered";
		case VENDOR_NAME  = "vendor_name";
		case VENDOR_MODEL = "vendor_model";
		case DATE_AQUIRED = "date_aquired";
		case DATE_RETIRED = "date_retired";
	}