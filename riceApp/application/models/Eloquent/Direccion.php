<?php
class Direccion extends Illuminate\Database\Eloquent\Model 
{

	protected $table = 'direccion';
	
	public $timestamps = false;
	const ACTIVA = 1;

	public function sector(){
		return $this->belongsTo('Sector','id_sector','id');
	}

}