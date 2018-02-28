<?php
class Persona extends Illuminate\Database\Eloquent\Model 
{

	protected $table = 'persona';
	
	public $timestamps = false;

	public function direccion(){
        return $this->hasMany('Direccion','id_persona', 'id')->with("sector");
    }

}