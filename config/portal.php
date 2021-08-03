<?php

return [
	'auth_endpoint'       => env( 'PORTAL_AUTH_ENDPOINT', '172.17.0.1/api/auth/token/check/' ),
	'expiration'          => null,
	'user_model'          => env( 'PORTAL_USER_MODEL', 'App\Models\User' ),
	'db_user_fields'      => [
		'id'    => 'user_id',
		'name'  => 'name',
		'email' => 'email'
	],
	'user_model_key'      => 'user_id',
	'user_model_key_type' => 'string',
	
	'service' => [
		'auth' => [
			'url' => 'http://172.17.0.1'
		],
	]

];