<?php

namespace Rawaby88\Portal;

trait Portable
{
	public $token     = NULL;
	public $appliance = NULL;
	public $data      = NULL;
	
	public
	function initializePortable ()
	{
		$this->primaryKey   = config( 'portal.user_model_key' );
		$this->keyType      = config( 'portal.user_model_key_type' );
		$this->incrementing = FALSE;
		
		foreach ( config( 'portal.db_user_fields' ) as $res => $field )
		{
			if(is_array($field))
			{
				foreach ( $field as $val => $key )
				{
					$this->fillable[] = $key;
				}
			}else
			{
				$this->fillable[] = $field;
			}
		}
	}
	
	public
	function setToken ( $token ): void
	{
		$this->token = $token;
	}
	
	public
	function setAppliance ( $appliance ): void
	{
		$this->appliance = $appliance;
	}
	
	public
	function getData ( $name )
	{
		return $this->data->$name ?? NULL;
	}
	
	public
	function setData ( $data ): void
	{
		$this->data = $data;
		
		
		foreach ( config( 'portal.db_user_fields' ) as $res => $field )
		{
			if(is_array($field))
			{
				foreach ( $field as $val => $key )
				{
					$this->{$key} = $data->{$res}->{$val};
				}
			}else
			{
				$this->{$field} = $data->{$res};
			}
		}
		
		
	}
	
}