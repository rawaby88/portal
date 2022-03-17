<?php

namespace Rawaby88\Portal;

use Illuminate\Support\Facades\Facade;

class PortalFacade extends Facade
{
	/**
	 * Get the registered name of the component.
	 *
	 * @return string
	 */
	protected static
	function getFacadeAccessor ()
	{
		return 'portal';
	}
}
