<?php

	namespace VLW\API\Databases\VLWdb\Models\Messages;

	enum MessagesModel: string {
		const TABLE = "messages";

		case ID                      = "id";
		case EMAIL                   = "email";
		case MESSAGE                 = "message";
		case IS_READ                 = "is_read";
		case IS_SPAM                 = "is_spam";
		case IS_SAVED                = "is_saved";
		case DATE_TIMESTAMP_CREATED  = "date_timestamp_created";
	}