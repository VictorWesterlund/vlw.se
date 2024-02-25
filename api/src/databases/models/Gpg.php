<?php

	namespace VLW\API\Databases\VLWdb\Models\Gpg;

	enum GpgModel: string {
		const TABLE = "gpg";

		case TEXT = "text";
		case BIN  = "bin";
	}