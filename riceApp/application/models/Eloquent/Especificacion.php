<?php
class Especificacion extends Illuminate\Database\Eloquent\Model 
{

	protected $table = 'especificacion';
	
	public $timestamps = false;

	protected $casts = array(
		'activo' => 'boolean',
		'valor'  => 'integer',
	);

	public function producto(){
		 return $this->belongsToMany('Producto', 'producto_especificacion', 'id_especificacion', 'id_producto');
	}
}