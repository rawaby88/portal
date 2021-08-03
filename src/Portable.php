<?php

namespace Rawaby88\Portal;

trait Portable
{
	public $token     = null;
	public $appliance = null;
	public $data      = null;
	
	public
	function initializePortable ()
	{
		$this->primaryKey = config('portal.user_model_key');
		$this->keyType    = config('portal.user_model_key_type');
		
		foreach (config('portal.db_user_fields') as $res => $field)
		{
			$this->fillable[] = $field;
		}
	}
	
	public
	function setToken ( $token )
	: void
	{
		$this->token = $token;
	}
	
	public
	function setAppliance ( $appliance )
	: void
	{
		$this->appliance = $appliance;
	}
	
	public
	function setData ( $data )
	: void
	{
		$this->data = $data;
	}
	
	public
	function __get ( $name )
	{
		return $this->data->$name ?? null;
	}
}