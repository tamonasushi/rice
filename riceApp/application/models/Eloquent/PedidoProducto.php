<?php
class PedidoProducto extends Illuminate\Database\Eloquent\Model 
{

	protected $table = 'pedido_producto';
	
	public $timestamps = false;

	public function especificacion(){
		return $this->belongsTo('Especificacion','id_especificacion','id');
	}
	public function producto(){
		return $this->belongsTo('Producto','id_producto','id');
	}

}