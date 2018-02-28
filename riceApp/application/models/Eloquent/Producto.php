<?php
class Producto extends Illuminate\Database\Eloquent\Model 
{

	protected $table = 'producto';
	
	public $timestamps = false;

	protected $casts = array(
		'activo' => 'boolean',
		'valor'  => 'integer',
	);

	public function especificacion(){
		return $this->belongsToMany('Especificacion', 'producto_especificacion', 'id_producto', 'id_especificacion');
	}

	public function categoria(){
		return $this->belongsTo('Categoria','id_categoria','id');
	}

}