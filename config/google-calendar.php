<?php

return [

	'user_class' => 'App\Models\User',

	'excel_view' => 'innoboxrrgooglecalendar::excel.',

	'notification_via' => ['mail', 'database'],

	'export_disk' => 's3',

	'client_id' => env('GOOGLE_CLIENT_ID'),

	'client_secret' => env('GOOGLE_SECRET'),
	
];