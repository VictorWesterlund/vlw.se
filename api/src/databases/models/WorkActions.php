<?php

	namespace VLW\API\Databases\VLWdb\Models\Work;
	
	enum WorkActionsModel: string {
		const TABLE = "work_actions";
		
		case ID           = "id";
		case ANCHOR       = "anchor";
		case DISPLAY_TEXT = "display_text";
		case HREF         = "href";
		case CLASS_LIST   = "class_list";
		case EXTERNAL     = "external";
	}