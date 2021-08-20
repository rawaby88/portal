<?php

namespace Rawaby88\Portal\Exceptions;

use Exception;

class KeyFileDoesNotExist extends Exception
{
	public static
	function make ( string $path )
	: self
	{
		return new self( "There is no file at path: `{$path}`. or add service public_key in portal config" );
	}
}