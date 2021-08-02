<?php

namespace Rawaby88\Portal;

use Illuminate\Auth\RequestGuard;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;

class PortalServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        /*
         * Optional methods to load your package assets
         */
        // $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'portal');
        // $this->loadViewsFrom(__DIR__.'/../resources/views', 'portal');
        // $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        // $this->loadRoutesFrom(__DIR__.'/routes.php');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/portal.php' => config_path('portal.php'),
            ], 'config');

            // Publishing the views.
            /*$this->publishes([
                __DIR__.'/../resources/views' => resource_path('views/vendor/portal'),
            ], 'views');*/

            // Publishing assets.
            /*$this->publishes([
                __DIR__.'/../resources/assets' => public_path('vendor/portal'),
            ], 'assets');*/

            // Publishing the translation files.
            /*$this->publishes([
                __DIR__.'/../resources/lang' => resource_path('lang/vendor/portal'),
            ], 'lang');*/

            // Registering package commands.
            // $this->commands([]);
        }
	
	    $this->configureGuard();
    }

    /**
     * Register the application services.
     */
    public function register()
    {
	    config( [
		            'auth.guards.portal' => array_merge( [
			                                                 'driver'   => 'portal',
			                                                 'provider' => null,
		                                                 ], config( 'auth.guards.portal', [] ) ),
	            ] );
	
	    if ( !app()->configurationIsCached() )
	    {
		    $this->mergeConfigFrom( __DIR__ . '/../config/portal.php', 'portal' );
	    }

        // Register the main class to use with the facade
        $this->app->singleton('portal', function () {
            return new Portal;
        });
    }
	
	
	protected
	function configureGuard ()
	{
		Auth::resolved( function ( $auth ) {
			$auth->extend( 'portal', function ( $app, $name, array $config ) use ( $auth ) {
				return tap( $this->createGuard( $auth, $config ), function ( $guard ) {
					app()->refresh( 'request', $guard, 'setRequest' );
				} );
			} );
		} );
	}
	
	protected
	function createGuard ( $auth, $config )
	{
		return new RequestGuard( new Guard( $auth, config( 'portal.expiration' ), $config[ 'provider' ] ), request(),
		                         $auth->createUserProvider( $config[ 'provider' ] ?? null ) );
	}
}
