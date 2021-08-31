<?php

return [
	'auth_endpoint'       => env( 'PORTAL_AUTH_ENDPOINT', '172.17.0.1/api/auth/token/check/' ),
	'expiration'          => null,
	'main_appliance'      => 'd9f605f0-0669-4d05-8845-e4517acb6557',
	'user_model'          => env( 'PORTAL_USER_MODEL', 'App\Models\User' ),
	'db_user_fields'      => [
		'id'    => 'user_id',
		'name'  => 'name',
		'email' => 'email'
	],
	'user_model_key'      => 'user_id',
	'user_model_key_type' => 'string',
	
	'service'         => [
		'auth'  => [
			'url'        => env('SERVICE_AUTH_URL','http://172.17.0.1'),
			'public_key' => base_path( 'keys/auth.key' ),
		],
		'media' => [
			'url'        => env('SERVICE_MEDIA_URL','http://172.17.0.1:8002'),
			'public_key' => base_path( 'keys/media.key' ),
		],
		'mbc'   => [
			'url'        => env('SERVICE_MBC_URL','http://172.17.0.1:8001'),
			'public_key' => base_path( 'keys/mbc.key' ),
		],
		'link'   => [
			'url'        => env('SERVICE_MBC_URL','http://172.17.0.1:8003'),
			'public_key' => base_path( 'keys/link.key' ),
		],
	],
	
	//change this to service name ex. auth , media or mbc
	'current_service' => env('SERVICE_NAME','service_name'),
	'private_key'     => base_path( 'keys/private.key' ),
	'pass_key'        => env('SERVICE_PASS_KEY','123456'),

];