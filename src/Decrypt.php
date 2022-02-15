<?php

namespace Rawaby88\Portal;

use Illuminate\Http\Response;

class Decrypt
{
	
	public static
	function valid ( $service )
	: bool
	{
		$serviceFilePrivateKey = config( 'portal.private_key' ) ?? 'private_key';
		$passKey               = config( 'portal.pass_key' );
		$currentService        = config( 'portal.service_name' );
		
		if ( !file_exists( $serviceFilePrivateKey ) )
		{
			abort( Response::HTTP_FAILED_DEPENDENCY,
			       "Error with `{$currentService}` service: file: `{$serviceFilePrivateKey}` doesnt exists in `{$currentService}` service." );
		}
		
		$servicePrivateKey = openssl_pkey_get_private( file_get_contents( $serviceFilePrivateKey ), $passKey );
		
		if ( $servicePrivateKey === false )
		{
			abort( Response::HTTP_FAILED_DEPENDENCY,
			       "Error with `{$currentService}` service: - Does not seem to be a valid privte key in `{$currentService}` service." );
		}
		
		openssl_private_decrypt( base64_decode( $service ), $decrypted, $servicePrivateKey, OPENSSL_PKCS1_PADDING );
		
		if(is_null( $decrypted ))
		{
			return false;
		}
		
		[$ser, $sentTime] = explode('|', $decrypted);
		
		
		if( time() -  $sentTime > config( 'portal.ttl' ) )
		{
			return false;
		}
		
		return true;
	}
	
}

