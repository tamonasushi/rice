<?php
class Params extends Illuminate\Database\Eloquent\Model 
{

	const TIPO_1_RECIBIDO = 1;
	
	const TIPO_1_ACEPTADO = 2;
	
	const TIPO_1_DESPACHADO = 3;

	protected $table = 'params';
	
	public $timestamps = false;

}