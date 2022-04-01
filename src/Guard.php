<?php

namespace Rawaby88\Portal;

use Illuminate\Contracts\Auth\Factory as AuthFactory;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;


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
		elseif ( $this->token = request()->bearerToken() )
		{
			if(config('portal.current_service') === 'auth')
			{
				return $this->tokenValidationOnAuthService();
			}
			
			
			if ( !$this->isValidAccessToken() )
			{
				return;
			}
			
			return $this->findOrCreateUser();
		}
		
		return;
	}
	
	/**
	 * Check token is valid while in auth service
	 */
	protected
	function tokenValidationOnAuthService()
	{
		$accessToken = \Laravel\Sanctum\PersonalAccessToken::findToken($this->token);
		
		if ( !$accessToken )
		{
			return;
		}
		
		if ( $accessToken->updated_at->lte( now()->subMinutes( config( 'sanctum.expiration' ) ) ) )
		{
			$accessToken->delete();
			
			return;
		}
		
		$accessToken->touch();
		
		return 'auth:user';
	}
	
	
	/**
	 * Check token is valid by sending token to auth service
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
		//		$route = Route::getRoutes()->match( request() );
		
		$this->tokenResponse = Http::post( config( 'portal.auth_endpoint' ), [
			'token'        => $this->token,
			//			'route_name'   => $route->getName(),
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
			$user->setData( $response );
		}
		
		$user->setToken( $this->token );
		
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
