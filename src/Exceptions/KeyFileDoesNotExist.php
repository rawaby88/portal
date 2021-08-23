<?php

namespace Rawaby88\Portal\Exceptions;

use Exception;

class KeyFileDoesNotExist extends Exception
{
	public static
	function make ( string $path )
	: self
	{
		return new self( "There is no public key file at path: `{$path}` or there is no configuration for this service in portal config" );
	}
}