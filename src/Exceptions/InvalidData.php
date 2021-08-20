<?php

namespace Rawaby88\Portal\Exceptions;

use Exception;

class InvalidData extends Exception
{
	public static function make(string $type ): self
	{
		return new self("Empty data, couldn't `{$type}` data.");
	}
}