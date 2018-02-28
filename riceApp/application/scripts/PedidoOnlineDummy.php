<?php
//inicializo el script
incialiceScript();

//Display Errors Runtime
error_reporting(E_ALL);
ini_set('error_reporting', E_ALL);
ini_set('display_errors',1);

//Start Codigo del la aplicación

//obtengo data posible
$users = UsuarioPerfil::where("id_perfil", Perfil::CLIENTE)->with("usuario")->get()->toArray();
$productos = Producto::with("especificacion")->get()->toArray();
$extras = Extras::all()->toArray();

$DB = Zend_Registry::get("DB");

try {
	$DB::beginTransaction();
	//Desde la data que tengo en memoria, tomo datos random.

	$u = $users[rand(0, count($users)-1)];
	$direccion = $u["usuario"]["persona"]["direccion"][ rand(0, count($u["usuario"]["persona"]["direccion"]) - 1) ];

	$fecha_creacion = Zend_Date::NOW()->get("yyyy-MM-dd HH:mm-ss");
	
	//generar el pedido
	$pedido = new Pedido();
	$pedido->observacion = "Esta es la observación de un pedido".rand();
	$pedido->estado = Params::TIPO_1_RECIBIDO;
	$pedido->total = $direccion["sector"]["valor"];
	$pedido->id_direccion = $direccion["id"];
	$pedido->creador = $u["usuario"]["persona"]["id"];
	$pedido->fecha_creacion = $fecha_creacion;
	$pedido->save();

	//asociar los productos
	$cantProductos = rand(1,count($productos)-1);
	$productosAsociar = array();

	$i = 0;
	while ( $i <= $cantProductos) {
		$key = rand(0, $cantProductos);
		$productosAsociar[$key] = $productos[$key];
		$i++;
	}

	foreach ($productosAsociar as $key => $pro) {
		$pp = new PedidoProducto();
		$pp->id_producto = $pro["id"];
		$pp->id_pedido = $pedido->id;
		
		if(count($pro["especificacion"])){
			$especificacion = $pro["especificacion"][rand(0, count($pro["especificacion"])-1 )];
			$pp->id_especificacion = $especificacion["id"] ;
			$pedido->total = $pedido->total + ($especificacion["valor"]*$pp->cantidad);
		}
		$pp->cantidad = rand(1,5);
		$pp->creador = $pedido->creador;
		$pp->fecha_creacion = $pedido->fecha_creacion;
		$pp->save();
		$pedido->total = $pedido->total + ($pro["valor"]*$pp->cantidad);
	}


	//asociar los extras
	$cantidadExtras = rand(0,count($extras)-1);
	$extrasAsociar = array();
	$i = 0;
	while ($i <= $cantidadExtras) {
		$k = rand(0, $cantidadExtras);
		$extrasAsociar[$k] = $extras[$k];
		$i++;		
	}

	if(rand(false,true)){
		foreach ($extrasAsociar as $key => $ex) {
			$c = rand(1,10);
			$pe = new PedidoExtra();
			$pe->id_extra = $ex["id"];
			$pe->id_pedido = $pedido->id;
			$pe->cantidad = $c;
			$pe->creador = $pedido->creador;
			$pe->fecha_creacion = $pedido->fecha_creacion;
			$pe->save();
			$pedido->total = $pedido->total + ($ex["valor"]*$c);
		}
	}


	$pedido->save();

	$DB::commit();
} catch (Exception $e) {
	$DB::rollBack();
	Zend_Debug::Dump($e->getMessage());
}


//End Codigo del la aplicación

echo PHP_EOL.".::Fin del Script::.".PHP_EOL;

function incialiceScript(){


	try {
		$appPath =  realpath(dirname(__FILE__) . '/..');
		defined('APPLICATION_PATH')	|| define('APPLICATION_PATH',$appPath);
		
		require realpath(APPLICATION_PATH . '/../vendor/autoload.php');

		require_once 'Zend/Loader/Autoloader.php';
		$autoloader = Zend_Loader_Autoloader::getInstance();

		$opts = new Zend_Console_Getopt(
			array('env|e=w' => 'Establece el enviroment ej: development')
		);
		$opts->parse();
		if(!isset($opts->env)){
			print $opts->getUsageMessage();
	    	exit;
		}
	}catch(Exception $e){
		print $e->getMessage();
		exit;
	}

	define("RUN_APPLICATION",false);
	require_once APPLICATION_PATH."/../public/index.php";

}


