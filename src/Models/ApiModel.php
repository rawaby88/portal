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
	function callApi ( $link, $method, $params = [] )
	{
		$response = Http::contentType( 'application/json' )->withToken( auth()->user()->token )
		                ->accept( 'application/json' )->withHeaders( [
			                                                             'appliance' => auth()->user()->appliance,
		                                                             ] )->$method( $link, $params );
		
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
	function serviceBaseUrl ()
	: string
	{
		return config( 'portal.service.' . static::$service . '.url' ) . '/' . static::$baseUrl;
	}
}
