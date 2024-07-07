<?php

	namespace VLW\API\Databases\VLWdb\Models\Battlestation;

	use victorwesterlund\xEnum;

	enum ClassEnum {
		use xEnum;

		case DESKTOP;
		case LAPTOP;
		case SERVER;
	}

	enum CpuModel: string {
		use xEnum;
		
		const TABLE = "cpu";

		case ID                     = "id";
		case CPU_CLASS              = "class";
		case CLOCK_BASE             = "clock_base";
		case CLOCK_TURBO            = "clock_turbo";
		case CORE_COUNT_PERFORMANCE = "core_count_performance";
		case CORE_COUNT_EFFICIENCY  = "core_count_efficiency";
		case CORE_THREADS           = "core_threads";
		case VENDOR_NAME            = "vendor_name";
		case VENDOR_MODEL           = "vendor_model";
		case DATE_AQUIRED           = "date_aquired";
		case IS_RETIRED             = "is_retired";
	}