<?php

namespace Rawaby88\Portal\Models;

use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;
use Rawaby88\Portal\Encrypt;
use Rawaby88\Portal\Exceptions\BadKey;
use Rawaby88\Portal\Exceptions\InvalidData;
use Rawaby88\Portal\Exceptions\KeyFileDoesNotExist;

class ApiModel
{
	
	public static $baseUrl;
	public static $service;
	
	public
	function __construct ()
	{
		//		foreach ( $this->fillable as $field )
		//			$this->$field = null;
	}
	
	/**
	 * @throws KeyFileDoesNotExist
	 * @throws InvalidData
	 * @throws BadKey
	 * @throws Exception
	 */
	public static
	function callApi ( $link, $method, $params = [], array $attachments = [] )
	{
		$response = Http::contentType( 'application/json' )
		                ->accept( 'application/json' )->withHeaders( [
			                                                             'appliance' => auth()->user()->appliance ??
			                                                                            config('portal.main_appliance'),
			                                                             'service' => Encrypt::data(static::$service)
			                                                                          .time()
		                                                             ] );
		if(auth()->user() && auth()->user()->token)
		{
			$response = $response->withToken( auth()->user()->token );
		}
		
		
		if ( count( $attachments ) )
		{
			foreach ( $attachments as $attachment )
			{
				$response = $response->attach( $attachment[ 'name' ], file_get_contents( $attachment[ 'file' ] ),
				                               $attachment[ 'filename' ] );
			}
		}
		
		$response = $response->$method( $link, $params );
		
		$responseObject = $response->object();
		
		if ( $response->status() == 404 )
		{
			return null;
		}
		
		if($response->status() == Response::HTTP_FAILED_DEPENDENCY)
		{
			abort( Response::HTTP_FAILED_DEPENDENCY,  $responseObject->message );
		}
		
		
		if ( !$responseObject->success )
		{
			abort( $response->status() ,  $responseObject->error->code . ' ' . $responseObject->error->message );
			//			throw new Exception( $responseObject->error->code . ' ' . $responseObject->error->message,
			//			                      $response->status() );
		}
		
		return $response;
	}
	
	public static
	function find ( $id )
	{
		$apiResponse = static::callApi( static::serviceBaseUrl() . '/' . $id, 'get', [] );
		
		return $apiResponse ? $apiResponse->object()->data : null;
	}
	
	public static
	function all ()
	{
		$apiResponse = static::callApi( static::serviceBaseUrl(), 'get', [] );
		
		return $apiResponse ? $apiResponse->object()->data : null;
	}
	
	public static
	function create ( array $params, array $attachments = [] )
	{
		$apiResponse = static::callApi( static::serviceBaseUrl(), 'post', $params, $attachments );
		
		return $apiResponse ? $apiResponse->object()->data : null;
	}
	
	/**
	 * @throws KeyFileDoesNotExist
	 * @throws InvalidData
	 * @throws BadKey
	 */
	public static
	function delete ( int $id )
	{
		$apiResponse = static::callApi( static::serviceBaseUrl() . '/' . $id, 'delete' );
		
		return $apiResponse ? $apiResponse->object()->data : null;
	}
	
	/**
	 * @throws KeyFileDoesNotExist
	 * @throws InvalidData
	 * @throws BadKey
	 */
	public static
	function get ( string $link )
	{
		$apiResponse = static::callApi( static::serviceBaseUrl() . '/' . $link, 'get', [] );
		
		return $apiResponse ? $apiResponse->object()->data : null;
	}
	
	/**
	 * @throws KeyFileDoesNotExist
	 * @throws InvalidData
	 * @throws BadKey
	 */
	public static
	function post ( string $link, array $params )
	{
		$apiResponse = static::callApi( static::serviceBaseUrl() . '/' . $link, 'post', $params );
		
		return $apiResponse ? $apiResponse->object()->data : null;
	}
	
	
	public static
	function serviceBaseUrl ()
	: string
	{
		return config( 'portal.service.' . static::$service . '.url' ) . '/' . static::$baseUrl;
	}
}
