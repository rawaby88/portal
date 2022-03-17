<?php

namespace Rawaby88\Portal\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;

class ResponseException extends Exception
{
	public $data = [];
	public $status;
	public $service;
	
	public
	function __construct ( $message, $status, $service )
	{
		parent::__construct( $message );
		
		$this->status  = $status;
		$this->service = $service;
	}
	
	public
	function render ( $request ): JsonResponse
	{
		return $this->handleAjax();
	}
	
	/**
	 * Handle an ajax response.
	 */
	private
	function handleAjax (): JsonResponse
	{
		return response()->json( [
			                         'error'   => TRUE,
			                         'service' => $this->service,
			                         'message' => $this->getMessage(),
			                         'data'    => $this->data,
		                         ], $this->status );
	}
	
	public
	function withData ( array $data ): ResponseException
	{
		$this->data = $data;
		
		return $this;
	}
	
	public
	function withStatus ( $status ): ResponseException
	{
		$this->status = $status;
		
		return $this;
	}
}
