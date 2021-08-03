<?php

namespace Rawaby88\Portal;

class Portal
{
	
	public static $runsMigrations = true;
	
	public static
	function actingAs ( $user )
	{
		$guard = 'portal';
		
		app( 'auth' )->guard( $guard )->setUser( $user );
		
		app( 'auth' )->shouldUse( $guard );
		
		return $user;
	}
	
	public static
	function shouldRunMigrations ()
	: bool
	{
		return static::$runsMigrations;
	}
	
	public static
	function ignoreMigrations ()
	: Portal
	{
		static::$runsMigrations = false;
		
		return new static;
	}
}
