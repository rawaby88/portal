<?php

namespace Rawaby88\Portal;

class Portal
{
	
	public static $runsMigrations = TRUE;
	
	public static
	function actingAs ( $user )
	{
		$guard = 'portal';
		
		app( 'auth' )
			->guard( $guard )
			->setUser( $user );
		
		app( 'auth' )->shouldUse( $guard );
		
		return $user;
	}
	
	public static
	function service ( $service ): string
	{
		return Encrypt::data( $service );
	}
	
	public static
	function shouldRunMigrations (): bool
	{
		return static::$runsMigrations;
	}
	
	public static
	function ignoreMigrations (): Portal
	{
		static::$runsMigrations = FALSE;
		
		return new static;
	}
}
