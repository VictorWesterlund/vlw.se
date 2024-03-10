<?php

	namespace VLW\API\Databases\VLWdb\Models\MediaSrcset;

	enum MediaSrcsetModel: string {
		const TABLE = "media_srcset";

		case ID             = "id";
		case ANCHOR_DEFAULT = "anchor_default";
	}