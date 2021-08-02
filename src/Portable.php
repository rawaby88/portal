<?php

namespace Rawaby88\Portal;

trait Portable
{
	protected $primaryKey= 'user_id';
	protected $keyType = 'string';
	
	
	public function initializePortable()
	{
		$this->fillable[] = 'user_id';
	}
}