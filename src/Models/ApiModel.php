<?php

namespace Rawaby88\Portal\Models;

use Exception;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;
use Rawaby88\Portal\Encrypt;
use Rawaby88\Portal\Exceptions\BadKey;
use Rawaby88\Portal\Exceptions\InvalidData;
use Rawaby88\Portal\Exceptions\KeyFileDoesNotExist;
use Rawaby88\Portal\Exceptions\ResponseException;

class ApiModel
{
	
	public static $baseUrl;
	public static $service;
	
	public
	function __construct ()
	{
	}
	
	/**
	 * @throws KeyFileDoesNotExist
	 * @throws InvalidData
	 * @throws BadKey
	 * @throws Exception
	 */
	public static
	function callApi ( $link, $method, $params = [], $attachment = NULL )
	{
		$response = Http::accept( 'application/json' )
		                ->withHeaders( [
			                               'appliance' => auth()->user()->appliance ??
			                                              config( 'portal.main_appliance' ),
			                               'service'   => Encrypt::data( static::$service ),
		
		                               ] );
		
		if ( auth()->user() instanceof Authenticatable && auth()->user()->token )
		{
			$response = $response->withToken( auth()->user()->token );
		}
		
		if ( $attachment )
		{
			$response = $response->asMultipart();
			$response = $response->attach( 'file', file_get_contents( $attachment ), 'file' );
		}
		else
		{
			$response = $response->contentType( 'application/json' );
		}
		
		$response = $response->$method( $link, $params );
		
		$responseObject = $response->object();
		
		if ( $response->status() == 404 )
		{
			throw new ResponseException( 'not found', 404, static::$service );
		}
		
		if ( $response->status() == 500 )
		{
			$ex = new ResponseException( 'Internal Server Error', 500, static::$service );
			
			$ex->withData( $response->json() );
			
			throw $ex;
		}
		
		if ( $response->status() == Response::HTTP_FAILED_DEPENDENCY )
		{
			throw new ResponseException( $responseObject->message, Response::HTTP_FAILED_DEPENDENCY, static::$service );
		}
		
		if ( !$responseObject->success )
		{
			$ex = new ResponseException( $responseObject->error->code, $response->status(), static::$service );
			$ex->withData( $response->json() );
			
			throw $ex;
		}
		
		return $response;
	}
	
	
	public static
	function serviceBaseUrl (): string
	{
		return config( 'portal.service.' . static::$service . '.url' ) . '/' . static::$baseUrl;
	}
	
	
	
	/**
	 * @throws KeyFileDoesNotExist
	 * @throws InvalidData
	 * @throws BadKey
	 */
	public static
	function find ( $id, $params = [], $prefix = '' )
	{
		$link = static::serviceBaseUrl() . '/' . $id;
		if($prefix)
		{
			$link = static::serviceBaseUrl() .  '/' . $prefix . '/' . $id;
		}
		
		$apiResponse = static::callApi( $link, 'get', $params );
		
		return $apiResponse->object()->data;
	}
	
	
	
	/**
	 * @throws KeyFileDoesNotExist
	 * @throws InvalidData
	 * @throws BadKey
	 */
	public static
	function all ($params = [], $prefix = '')
	{
		$apiResponse = static::callApi( static::serviceBaseUrl().'/'.$prefix, 'get', $params );
		
		return $apiResponse->object()->data;
	}
	
	/**
	 * @throws KeyFileDoesNotExist
	 * @throws InvalidData
	 * @throws BadKey
	 */
	public static
	function create ( array $params, $attachment = NULL, $prefix = '' )
	{
		$apiResponse = static::callApi( static::serviceBaseUrl().  '/' . $prefix, 'post', $params, $attachment );
		
		return $apiResponse->object()->data;
	}
	
	/**
	 * @throws KeyFileDoesNotExist
	 * @throws InvalidData
	 * @throws BadKey
	 */
	public static
	function delete ( int $id, $params = [], $prefix = '' )
	{
		$link = static::serviceBaseUrl() . '/' . $id;
		if($prefix)
		{
			$link = static::serviceBaseUrl() .  '/' . $prefix . '/' . $id;
		}
		
		$apiResponse = static::callApi( $link, 'delete', $params );
		
		return $apiResponse->object()->data;
	}
	
	/**
	 * @throws KeyFileDoesNotExist
	 * @throws InvalidData
	 * @throws BadKey
	 */
	public static
	function get ( string $prefix = '', array $params = [] )
	{
		$apiResponse = static::callApi( static::serviceBaseUrl() . '/' . $prefix, 'get', $params );
		
		return $apiResponse->object()->data;
	}
	
	/**
	 * @throws KeyFileDoesNotExist
	 * @throws InvalidData
	 * @throws BadKey
	 */
	public static
	function post ( string $prefix, array $params )
	{
		$apiResponse = static::callApi( static::serviceBaseUrl() . '/' . $prefix, 'post', $params );
		
		return $apiResponse->object()->data;
	}
}
