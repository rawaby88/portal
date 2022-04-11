<?php

namespace Rawaby88\Portal;

use Exception;
use Illuminate\Contracts\Auth\Factory as AuthFactory;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;
use Prophecy\Exception\Doubler\MethodNotFoundException;
use Rawaby88\Portal\Models\User;
use Symfony\Component\ErrorHandler\Error\ClassNotFoundError;

class Guard
{
	protected $auth;
	protected $provider;
	protected $tokenResponse;
	protected $token;
	protected $userModel;
	
	public
	function __construct ( AuthFactory $auth, $provider = NULL )
	{
		$this->auth      = $auth;
		$this->provider  = $provider;
		$this->userModel = config( 'portal.user_model', 'App\Models\User' );
	}
	
	public
	function __invoke ()
	{
		if ( $this->token = request()->bearerToken() )
		{
			//if bearerToken validation in auth service
			if ( config( 'portal.current_service' ) === 'auth' )
			{
				$accessToken = \Laravel\Sanctum\PersonalAccessToken::findToken( $this->token );
				
				if ( !$accessToken || !$this->isValidAccessTokenAuthService( $accessToken ) )
				{
					return;
				}
				
				return $accessToken->tokenable->withAccessToken( $accessToken );
			}
			
			//if bearerToken validation in other service
			if ( !$this->isValidAccessToken() )
			{
				return;
			}
			
			return $this->authenticatedUser();
		}
		elseif ( $service = request()->header( 'service' ) )
		{
			if ( Decrypt::valid( $service ) )
			{
				if( config( 'portal.mock_user' ) )
				{
					$user = Portal::actingAs( new User() );
					
					if(request()->user_id)
					{
						$user->findAndActAs(request()->user_id);
					}
					return $user;
				}
				return Portal::actingAs( new $this->userModel() );
			}
		}
		
		return;
	}
	
	/**
	 * Check token is valid while in auth service
	 */
	protected
	function isValidAccessTokenAuthService ( $accessToken )
	{
		if ( $accessToken->updated_at->lte( now()->subMinutes( config( 'sanctum.expiration' ) ) ) )
		{
			$accessToken->delete();
			
			return FALSE;
		}
		
		$accessToken->touch();
		
		return TRUE;
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
		$this->tokenResponse = Http::post( config( 'portal.auth_endpoint' ), [
			'token'   => $this->token,
			'service' => config( 'portal.current_service' ),
		] );
	}
	
	/**
	 * @throws Exception
	 */
	protected
	function authenticatedUser ()
	{
		$response = $this->tokenResponse->object()->data;
		
		if( config( 'portal.mock_user' ) )
		{
			$user = new User();
		}
		elseif ( class_exists( $this->userModel ) )
		{
			$user = $this->userModel::find( $response->id );
			
			if ( !$user )
			{
				$user = $this->createUser( $response );
			}
		}else
		{
			throw new ClassNotFoundError(printf('Class not found %s', $this->userModel), 404);
		}
		
		if (! in_array( Portable::class, class_uses($user)))
		{
			throw new Exception(printf('Portable trait is not use in %s', $this->userModel)  );
		}
		
		$user->setData ( $response );
		$user->setToken( $this->token );
		
		return $user;
	}
	
	/**
	 * Find or create user to current service;
	 */
	protected
	function createUser ( $response )
	{
		$data = [];
		
		foreach ( config( 'portal.db_user_fields' ) as $res => $field )
		{
			$data[ $field ] = $response->$res;
		}
		
		return $this->userModel::create(  $data );
		
	}
	
	protected
	function userFields ( $user )
	{
		foreach ( config( 'portal.db_user_fields' ) as $res => $field )
		{
			$user->fillable[] = $field;
		}
	}
	
}
