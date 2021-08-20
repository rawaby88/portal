<?php

namespace Rawaby88\Portal\Exceptions;

use Exception;

class BadKey extends Exception
{
	public static
	function make ( string $type )
	: self
	{
		return new self( "This does not seem to be a valid `{$type}` key." );
	}
}