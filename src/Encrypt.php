<?php

namespace Rawaby88\Portal;

use Rawaby88\Portal\Exceptions\BadKey;
use Rawaby88\Portal\Exceptions\InvalidData;
use Rawaby88\Portal\Exceptions\KeyFileDoesNotExist;

class Encrypt
{
	
	/**
	 * @throws KeyFileDoesNotExist
	 * @throws InvalidData
	 * @throws BadKey
	 */
	public static
	function data ( $service )
	: string
	{
		$serviceFileKey = config( 'portal.service.'. $service.'.public_key' ) ?? $service.'.key';
		
		if ( !file_exists( $serviceFileKey ) )
		{
			throw KeyFileDoesNotExist::make( $serviceFileKey );
		}
		
		$serviceKey = openssl_pkey_get_public(file_get_contents($serviceFileKey));
		
		if ($serviceKey === false) {
			throw BadKey::make('public');
		}
		
		openssl_public_encrypt( $service, $encrypted, $serviceKey, OPENSSL_PKCS1_PADDING );

		if (is_null($encrypted)) {
			throw InvalidData::make('encrypt');
		}
		
		return base64_encode($encrypted);
	}
}