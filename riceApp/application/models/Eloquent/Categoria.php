<?php
class Categoria extends Illuminate\Database\Eloquent\Model 
{

	protected $table = 'categoria';
	
	public $timestamps = false;

	protected $casts = array(
		'activo' => 'boolean',
	);

}