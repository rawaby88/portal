<?php

namespace Rawaby88\Portal\Models;

use Illuminate\Auth\Authenticatable;

use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Rawaby88\Portal\Exceptions\BadKey;
use Rawaby88\Portal\Exceptions\InvalidData;
use Rawaby88\Portal\Exceptions\KeyFileDoesNotExist;
use Rawaby88\Portal\Portable;

class User extends ApiModel implements AuthenticatableContract, AuthorizableContract
{
	use Authenticatable, Authorizable, Portable;
	
	public static $baseUrl = 'api/users';
	public static $service = 'auth';
	
	
	/**
	 * @throws KeyFileDoesNotExist
	 * @throws InvalidData
	 * @throws BadKey
	 */
	public
	function findAndActAs ( $id, $params = [] )
	{
		$apiResponse = static::callApi( static::serviceBaseUrl() . '/profile/' . $id, 'get', $params );
		
		$data = $apiResponse->object()->data;
		
		foreach ( $data as $key => $value )
		{
			$this->{$key} = $value;
		}
	}
	
	public
	function getKeyName (): string
	{
		return 'id';
	}
}
