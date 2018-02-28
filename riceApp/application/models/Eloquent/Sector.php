<?php
class Sector extends Illuminate\Database\Eloquent\Model 
{

	protected $table = 'sector';
	
	public $timestamps = false;
	
	protected $casts = array(
		'activo' => 'boolean',
		'valor'  => 'integer',
	);

}