<?php

namespace Rawaby88\Portal;

use Illuminate\Contracts\Auth\Factory as AuthFactory;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;

class Guard
{
	protected $auth;
	protected $expiration;
	protected $provider;
	protected $tokenResponse;
	protected $token;
	protected $userModel;
	
	public
	function __construct ( AuthFactory $auth, $expiration = NULL, $provider = NULL )
	{
		$this->auth       = $auth;
		$this->expiration = $expiration;
		$this->provider   = $provider;
		$this->userModel  = config( 'portal.user_model', 'App\Models\User' );
	}
	
	public
	function __invoke ()
	{
		if ( $service = request()->header( 'service' ) )
		{
			if ( Decrypt::valid( $service ) )
			{
				return 'auth:machine';
			}
			
			return;
		}
		elseif ( $bearerToken = request()->bearerToken() )
		{
			$this->findTokenString( $bearerToken );
			
			if ( !$this->isValidAccessToken() )
			{
				return;
			}
			
			return $this->findOrCreateUser();
		}
		
		return;
	}
	
	public
	function findTokenString ( $bearerToken )
	{
		if ( strpos( $bearerToken, '|' ) === FALSE )
		{
			$this->token = $bearerToken;
		}
		else
		{
			[
				$id,
				$this->token,
			] = explode( '|', $bearerToken, 2 );
		}
	}
	
	/**
	 * Check token is valid and has permission
	 */
	protected
	function isValidAccessToken (): bool
	{
		$this->tokenResponse();
		
		if ( $this->tokenResponse->status() !== Response::HTTP_OK )
		{
			return FALSE;
		}
		
		return TRUE;
	}
	
	protected
	function tokenResponse ()
	{
		$route = Route::getRoutes()->match( request() );
		
		$this->tokenResponse = Http::post( config( 'portal.auth_endpoint' ), [
			'token'        => $this->token,
			'route_name'   => $route->getName(),
			'service'      => config('portal.current_service')
		] );
	}
	
	/**
	 * Find or create user to current service;
	 */
	protected
	function findOrCreateUser ()
	{
		$response = $this->tokenResponse->object()->data;
		
		$user = $this->userModel::find( $response->id );
		
		if ( !$user )
		{
			$user = $this->userModel::create( $this->userFields( $response ) );
		}
		
		$user->setAppliance( request()->header( 'appliance' ) ?? NULL );
		$user->setToken( $this->token );
		$user->setData( $response );
		
		return $user;
	}
	
	protected
	function userFields ( $response ): array
	{
		$data = [];
		
		foreach ( config( 'portal.db_user_fields' ) as $res => $field )
		{
			$data[ $field ] = $response->$res;
		}
		
		return $data;
	}
	
}
