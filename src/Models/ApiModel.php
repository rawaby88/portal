<?php

namespace Rawaby88\Portal\Models;

use Illuminate\Support\Facades\Http;

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
	
	public static
	function callApi ( $link, $method, $params = [], array $attachments = [] )
	{
		$response = Http::contentType( 'application/json' )->withToken( auth()->user()->token )
		                ->accept( 'application/json' )->withHeaders( [
			                                                             'appliance' => auth()->user()->appliance,
		                                                             ] );
		
		
		if (count($attachments)) {
			foreach ($attachments as $attachment) {
				$response = $response->attach(
					$attachment['name'], file_get_contents($attachment['file']), $attachment['filename']
				);
			}
		}
		
		
		$response = $response->$method( $link, $params );
		
		$responseObject = $response->object();
		
		if ( !$responseObject->success )
		{
			throw new \Exception(  $responseObject->error->code . ' ' . $responseObject->error->message ,
			                       $response->status());
		}
		
		return $response;
	}
	
	public static
	function find ( $id )
	{
		$apiResponse = static::callApi( static::serviceBaseUrl() . '/' . $id, 'get', [] );
		
		return $apiResponse->object()->data;
	}
	
	public static
	function all ()
	{
		$apiResponse = static::callApi( static::serviceBaseUrl(), 'get', [] );
		
		return $apiResponse->object()->data;
	}
	
	
	public static
	function create(array $params, array $attachments = [])
	{
		$apiResponse = static::callApi( static::serviceBaseUrl(), 'post', $params, $attachments );
		
		return $apiResponse->object()->data;
	}
	
	public
	static function delete(int $id)
	{
		$apiResponse = static::callApi( static::serviceBaseUrl() . '/' . $id, 'delete' );
		
		return $apiResponse->object()->data;
	}
	
	public static
	function serviceBaseUrl ()
	: string
	{
		return config( 'portal.service.' . static::$service . '.url' ) . '/' . static::$baseUrl;
	}
}
