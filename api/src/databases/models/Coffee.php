<?php

	namespace VLW\API\Databases\VLWdb\Models\Coffee;

	enum CoffeeModel: string {
		const TABLE = "coffee";

		case ID                      = "id";
		case DATE_TIMESTAMP_CREATED  = "date_timestamp_created";
	}