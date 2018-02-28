<?php
class Perfil extends Illuminate\Database\Eloquent\Model 
{

	const USUARIO = 1;
	
	const CLIENTE = 2;

	protected $table = 'perfil';
	
	public $timestamps = false;

}