<?php

namespace Rawaby88\Portal;

use Rawaby88\Portal\Exceptions\BadKey;
use Rawaby88\Portal\Exceptions\KeyFileDoesNotExist;

class Decrypt
{
	
	/**
	 * @throws KeyFileDoesNotExist
	 * @throws BadKey
	 */
	public static
	function valid ( $service )
	: bool
	{
		$serviceFilePrivateKey = config( 'portal.private_key' );
		$passKey = config( 'portal.pass_key' );
		
		if ( !file_exists( $serviceFilePrivateKey ) )
		{
			throw KeyFileDoesNotExist::make( $serviceFilePrivateKey );
		}
		
		$servicePrivateKey = openssl_pkey_get_private(file_get_contents($serviceFilePrivateKey), $passKey);
		
		if ($servicePrivateKey === false) {
			throw BadKey::make('private');
		}
		
		openssl_public_decrypt( base64_decode($service), $decrypted, $servicePrivateKey, OPENSSL_PKCS1_PADDING );
		
		
		return ! is_null($decrypted);
	}
	
}

