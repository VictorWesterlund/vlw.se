<?php

	namespace VLW\API\Databases\VLWdb\Models\Work;

	enum WorkMediaModel: string {
		const TABLE = "work_media";

		case ANCHOR = "anchor";
		case MEDIA  = "media";
	}