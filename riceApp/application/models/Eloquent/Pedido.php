<?php
class Pedido extends Illuminate\Database\Eloquent\Model 
{

	const ESTADO_1_PENDIENTE = 1;
	const ESTADO_2_ACEPTADO = 2;
	const ESTADO_3_EN_PREPARACION = 3;
	const ESTADO_4_DESPACHADO = 4;
	const ESTADO_5_RECHAZADO = 5;

	protected $table = 'pedido';
	
	public $timestamps = false;

	protected $casts = array(
		'estado' => 'integer',
		'valor'  => 'integer',
	);

	public function productos(){
		return $this->hasMany('PedidoProducto','id_pedido', 'id')
			->with("especificacion")
			->with("producto")
			;
	}

	public function cliente(){
		return $this->belongsTo('Usuario','creador','id')->with("persona");
	}

	public function direccion(){
		return $this->belongsTo('Direccion','id_direccion','id')->with("sector");
	}

	public function getTotalPedidosFecha($fIni = false, $fFin = false, $order = 'ORDER BY `fecha_creacion` DESC'){
		$DB = Zend_Registry::get("DB");

		$q = "SELECT date_format(fecha_creacion,'%Y-%m-%d') fecha, SUM(total) total
				FROM `pedido`";
		
		$wheres = array();
		if($fIni)
			$wheres[] = "date_format(fecha_creacion,'%Y-%m-%d') >= '$fIni'";
		if($fFin)
			$wheres[] = " AND date_format(fecha_creacion,'%Y-%m-%d') <= '$fFin'";
		
		if(count($wheres))
			$q .= "WHERE ".implode(" ", $wheres);

		$q .=" AND estado = 2 ";
		$q	.= "GROUP BY date_format(fecha_creacion,'%Y%m%d')";
		$q	.= $order;

		return $DB::select($q);
	}

}