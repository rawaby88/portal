<?php

namespace Rawaby88\Portal\Models;

use Illuminate\Database\Eloquent\Model;

class DummyUser extends Model
{
	public
	function setData ( $response )
	{
		foreach ( config( 'portal.db_user_fields' ) as $res => $field )
		{
			$this->{$field} = $response->$res;
		}
	}
}