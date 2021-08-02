<?php

namespace Rawaby88\Portal;

use App\Models\User;
use Illuminate\Contracts\Auth\Factory as AuthFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;

class Guard
{
	
	protected $auth;
	
	protected $expiration;
	
	protected $provider;
	protected $tokenResponse;
	
	public function __construct(AuthFactory $auth, $expiration = null, $provider = null)
	{
		$this->auth = $auth;
		$this->expiration = $expiration;
		$this->provider = $provider;
	}
	
	/**
	 * Retrieve the authenticated user for the incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return mixed
	 */
	public function __invoke(Request $request)
	{
		if ($bearerToken = $request->bearerToken())
		{
			$token = $this->findTokenString($bearerToken);
			
			if (! $this->isValidAccessToken($token) )
			{
				return;
			}
			
			return $this->findOrCreateUser();
			
		}
		
		return;
	}
	
	
	public
	function findTokenString($bearerToken)
	{
		if (strpos($bearerToken, '|') === false)
		{
			return $bearerToken;
		}
		
		[$id, $token] = explode('|', $bearerToken, 2);
		
		return $token;
	}
	
	protected
	function tokenResponse ($token)
	{
		$this->tokenResponse = Http::post( config( 'portal.auth_endpoint' ), [
			'token'        => $token,
			'workspace_id' => request()->workspace_id,
			'appliance_id' => request()->appliance_id,
			'route_name'   => request()->route_name,
		] );
	}
	
	
	/**
	 * Check token is valid and has permission
	 */
	protected
	function isValidAccessToken ($token)
	: bool
	{
		$this->tokenResponse ($token);
		if ( $this->tokenResponse->status() !== Response::HTTP_OK )
		{
			return false;
		}
		
		return true;
	}
	
	/**
	 * Find or create user to current service;
	 */
	protected
	function findOrCreateUser ()
	{
		$response = $this->tokenResponse->object()->data;
		
		$user = User::find( $response->id );
		
		if ( !$user )
		{
			$user = User::create( [
				                      'user_id' => $response->id,
				                      'name'    => $response->name,
				                      'email'   => $response->email,
			                      ] );
		}
		
		return $user;
	}

   
}
