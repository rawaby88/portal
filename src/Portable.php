<?php

namespace Rawaby88\Portal;

trait Portable
{
	public $token     = null;
	public $appliance = null;
	public $workspace = null;
	
	public
	function initializePortable ()
	{
		$this->fillable[] = 'user_id';
		$this->primaryKey = 'user_id';
		$this->keyType    = 'string';
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
	function setWorkspace ( $workspace )
	: void
	{
		$this->workspace = $workspace;
	}
}