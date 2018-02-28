<?php
//inicializo el script
incialiceScript();

//Display Errors Runtime
error_reporting(E_ALL);
ini_set('error_reporting', E_ALL);
ini_set('display_errors',1);

//Start Codigo del la aplicación

$DB = Zend_Registry::get("DB");

//limpio la Base de datos
$DB::statement("SET FOREIGN_KEY_CHECKS=0");
Persona::truncate();
Perfil::truncate();
Categoria::truncate();
Especificacion::truncate();
Extras::truncate();
Params::truncate();
Producto::truncate();
Sector::truncate();
Usuario::truncate();
UsuarioPerfil::truncate();
Direccion::truncate();
ProductoEspecificacion::truncate();
Pedido::truncate();
PedidoProducto::truncate();
PedidoExtra::truncate();
$DB::statement("SET FOREIGN_KEY_CHECKS=1");

$poblacion = new Zend_Config_Ini(__DIR__."/poblacion.ini");
$poblacion = $poblacion->toArray();

//comienzo la inserción de elementos
Persona::insert($poblacion["Persona"]);
Params::insert($poblacion["Params"]);
Perfil::insert($poblacion["Perfil"]);
Categoria::insert($poblacion["Categoria"]);
Especificacion::insert($poblacion["Especificacion"]);
Extras::insert($poblacion["Extras"]);
Producto::insert($poblacion["Producto"]);
Sector::insert($poblacion["Sector"]);
Usuario::insert($poblacion["Usuario"]);
UsuarioPerfil::insert($poblacion["UsuarioPerfil"]);
Direccion::insert($poblacion["Direccion"]);
ProductoEspecificacion::insert($poblacion["ProductoEspecificacion"]);

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


