<?php

	namespace VLW\API\Databases\VLWdb\Models\Work;
	
	enum WorkActionsModel: string {
		const TABLE = "work_actions";
		
		case REF_WORK_ID  = "ref_work_id";
		case DISPLAY_TEXT = "display_text";
		case HREF         = "href";
		case CLASS_LIST   = "class_list";
		case EXTERNAL     = "external";
	}