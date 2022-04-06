<?php

namespace Rawaby88\Portal\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;

class PermissionMiddleware
{
	/**
	 * Handle an incoming request.
	 *
	 * @param Request $request
	 * @param Closure $next
	 * @return mixed
	 */
	public
	function handle ( Request $request, Closure $next )
	{
		if( ! auth()->user()->token )
		{
			return response()->json( [
				                         'error'   => TRUE,
				                         'message' => 'Token is not included in the auth user',
				                         'data'    => [],
			                         ],  Response::HTTP_UNPROCESSABLE_ENTITY );
		}
		$routeName = $this->routeName();
		
		if( !$routeName)
		{
			return response()->json( [
				                         'error'   => TRUE,
				                         'message' => 'Route name is missing!',
				                         'data'    => [],
			                         ],  Response::HTTP_UNPROCESSABLE_ENTITY );
		}
		
		$permission = Http::get( config( 'portal.service.auth.url' ) .'/api/auth/token/can', [
			'token' => auth()->user()->token,
			'route_name'   => $routeName,
			'service'      => config('portal.current_service')
		] );
		
		
		if ( $permission->status() === Response::HTTP_OK )
		{
			if($permission->object()->data->can)
			{
				return $next( $request );
			}
		}
		
		return response()->json( [
			                         'error'   => TRUE,
			                         'service' => 'auth',
			                         'message' => 'access to the requested resource is forbidden!',
			                         'data'    => ['route' => $routeName],
		                         ],  Response::HTTP_FORBIDDEN );
	}
	
	private
	function routeName (): ?string
	{
		$route = Route::getRoutes()->match( request() );
		
		return $route->getName();
	}
}
