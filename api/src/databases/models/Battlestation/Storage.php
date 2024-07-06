<?php

	namespace VLW\API\Databases\VLWdb\Models\Battlestation;

	use victorwesterlund\xEnum;

	enum StorageDiskTypeEnum {
		use xEnum;

		case SSD;
		case HDD;
	}

	enum StorageDiskInterfaceEnum {
		use xEnum;

		case SATA;
		case NVME;
		case USB;
	}

	enum StorageDiskFormfactorEnum{
		use xEnum;

		case TWODOTFIVE;
		case THREEDOTFIVE;
		case MDOTTWO;
	}

	enum StorageModel: string {
		use xEnum;
		
		const TABLE = "storage";

		case ID              = "id";
		case DISK_TYPE       = "disk_type";
		case DISK_SIZE       = "disk_size";
		case DISK_SECTORS    = "disk_sectors";
		case DISK_INTERFACE  = "disk_interface";
		case DISK_FORMFACTOR = "disk_formfactor";
		case VENDOR_NAME     = "vendor_name";
		case VENDOR_MODEL    = "vendor_model";
		case DATE_AQUIRED    = "date_aquired";
		case IS_RETIRED      = "is_retired";
	}