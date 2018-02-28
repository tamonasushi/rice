<?php

class IndexController extends Rice_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */

        $this->addScriptJS("/js/modulos/directives.js");
		$this->addScriptJS("/js/modulos/factory.js");
		$this->addScriptJS("/js/modulos/filters.js");
		$this->addScriptJS("/js/modulos/resources.js");
		$this->addScriptJS("/js/modulos/client/controllers.js");
        $this->addScriptJS("/js/modulos/client/module.js");

        try{
            $this->dataPost = Zend_Json::decode($this->getRequest()->getRawBody());
        }catch(Exception $e){
            $this->dataPost = array();
        }
        
    }

    public function indexAction()
    {
       $this->_redirect("/index/menu");
    }

    public function inicioAction()
    {
         
    }

    /**
     * [Action description]
    * @return [type] [description]
    */
    public function getCategoriasAction(){

        $resp = array("success"=> false,"code" => -1,"data"=>array());
		try{
			$resp = array("success"=> true,"code" => -1,"data"=>Categoria::where("activo",true)->get());
		}catch(Esception $e){
		}
		$this->_helper->json($resp);
    
    }
    
    /**
     * [Action description]
    * @return [type] [description]
    */
    public function getProductosAction(){
        
        $resp = array("success"=> false,"code" => -1,"data"=>array());
        try{
            $productos = array();
            $p = Producto::with(array(
                "especificacion" => function($query){
                    $query->where('especificacion.activo', true);
                    }
                ))->where("activo",true)->get();

            foreach ($p as $key => $pr) {
                $productos[$pr->id_categoria][$pr->id] = $pr;
            }

            $resp = array("success"=> true,"code" => -1,"data"=>$productos);
        } catch(Esception $e){}
        $this->_helper->json($resp);
    
    }


    public function getSectoresAction(){
        $resp = array("success"=> false,"code" => -1,"data"=>array());
        try {
            if( isset($this->dataPost["id"]) ) {
                $sectores = Sector::find($this->dataPost["id"]);    
            } else {
                $sectores = Sector::all();  
            }
            $resp = array("success"=> true,"code" => 0,"data"=>$sectores);
        } catch (Exception $e) {
            $resp["error"] = $e;
        }
        $this->_helper->json($resp);
    }


    public function getEspecificacionesAction(){
        
        $resp = array("success"=> false,"code" => -1,"data"=>array());
        try{
            $especificaciones = Especificacion::where('activo',true)->get();
            $data = array();
            foreach ($especificaciones as $especificacion) {
                $data[$especificacion->id] = $especificacion->toArray();
            }
            $resp = array("success"=> true,"code" => -1,"data"=>$data);
        } catch(Esception $e){}
        $this->_helper->json($resp);
    
    }

    /**
     * [Action description]
    * @return [type] [description]
    */
    public function menuAction(){
    
    }

    public function ingresoAction() {
    
    }

    
    public function miPerfilAction() {
        
    }

    public function misComprasAction() {
        
    }

    public function getalgoAction(){
        $this->_helper->json(array("success"=>"algo"));
    }



####################### USUARIO ###############################################
    

    /**
     * [Action description]
     * @return [type] [description]
     */
    public function getDataUsuarioAction(){
        $success = false;
        $mensaje = "";
        $code = -1;
        $data = [];
        try {
            if(isset($this->dataPost["correo"]) && isset($this->dataPost["password"])){
                $salt =  Usuario::SALT;
                $resultEncriptacion = $this->encriptacionContraseniaEloquent($this->dataPost["password"], $salt);
                $passwordEncriptado = $resultEncriptacion["contrasenaEncriptada"];
                $existeUsuario = Usuario::where('username',$this->dataPost["correo"])
                                    ->where('password',$passwordEncriptado)
                                    ->exists();
                if( $existeUsuario ){
                    $usuario = Usuario::where('username',$this->dataPost["correo"])
                                    ->where('password',$passwordEncriptado)
                                    ->with('persona')->get()->last();
                    $numeroDirecciones = $usuario->persona->direccion->count();
                    $data = [
                        "id_usuario"=>$usuario->id,
                        "id_persona"=>$usuario->id_persona,
                        "username"=>$usuario->username,
                        "nombre"=>$usuario->persona->nombres,
                        "apPaterno"=>$usuario->persona->ap_pat,
                        "apMaterno"=>$usuario->persona->ap_mat,
                        "celular"=>$usuario->persona->celular,
                        "direccion"=>$usuario->persona->direccion,
                        "numero_direcciones"=>$numeroDirecciones
                        ];
                    $mensaje = "Acción realizada con éxito";
                    $success = true;
                } else {
                    $mensaje = "Datos de acceso no válidos";
                }
            }else{
                $mensaje = "Faltan parámetros";
            }
        } catch (Exception $e) {
            $mensaje = $e;
        }
        $resp = ["success"=>$success, "code"=>$code, "mensaje"=>$mensaje, "data"=>$data];
        $this->_helper->json($resp);
    }
    

    public function  registrarClienteAction() {
        $success = $success = false;
        $mensaje = "";
        $code = -1;
        $data = [];
        try {
            if( isset($this->dataPost["correo"]) && isset($this->dataPost["password"]) && isset($this->dataPost["nombres"]) && isset($this->dataPost["apellidos"]) && isset($this->dataPost["telefono"]) && isset($this->dataPost["sector"]) && isset($this->dataPost["dir_calle"]) && isset($this->dataPost["dir_numero"]) ) {
                if( $this->validaCorreo($this->dataPost["correo"]) ) {
                    $existeUsuario = Usuario::where("username",$this->dataPost["correo"])->exists();
                    if( $existeUsuario ) {
                        $mensaje = "El correo ya está en uso";
                    } else {
                        $contrasenia = $this->dataPost["password"];
                        $salt =  Usuario::SALT;
                        $resultEncriptacion = $this->encriptacionContraseniaEloquent($contrasenia, $salt);
                        if( $resultEncriptacion['success'] ) {
                            /** Crear persona **/
                            $persona = new Persona;
                            $persona->nombres = $this->dataPost["nombres"];
                            $persona->ap_pat = $this->dataPost["apellidos"];
                            $persona->email = $this->dataPost["correo"];
                            $persona->celular = $this->dataPost["telefono"];
                            $persona->save();

                            $descripcionDir = $this->dataPost["dir_calle"]." ".$this->dataPost["dir_numero"]." ".$this->dataPost["dir_detalle"];
                            $direccion = new Direccion;
                            $direccion->id_persona = $persona->id;
                            $direccion->id_sector = $this->dataPost["sector"];
                            $direccion->descripcion = $descripcionDir;
                            $direccion->activo = Direccion::ACTIVA ;
                            $direccion->fecha_creacion = date('Y-m-d H:i:s');
                            $direccion->save();

                            /** Crear usuario **/
                            $usuario = new Usuario;
                            $usuario->id_persona = $persona->id;
                            $usuario->username = $persona->email;
                            $usuario->password = $resultEncriptacion["contrasenaEncriptada"];
                            $usuario->fecha_creacion = date('Y-m-d H:i:s');
                            $usuario->save();

                            $numeroDirecciones = $persona->direccion->count();
                            $data = [
                                "id_usuario"=>$usuario->id,
                                "id_persona"=>$persona->id,
                                "username"=>$usuario->username,
                                "nombre"=>$persona->nombres,
                                "apPaterno"=>$persona->ap_pat,
                                "apMaterno"=>$persona->ap_mat,
                                "celular"=>$persona->celular,
                                "direccion"=>[$direccion->id =>$direccion],
                                "numero_direcciones"=>$numeroDirecciones
                            ];
                            $mensaje = "Acción realizada con éxito";
                            $success = true;
                        } else {
                            $mensaje = "No se pudo generar la contraseña. Inténtelo más tarde.";
                        }
                    }
                } else {
                    $mensaje = "El correo ingresado no tiene un formato válido";
                }
            } else{
                $mensaje = "Faltan parámetros";
            }
        } catch (Exception $e) {
            $mensaje = $e;
        }
        $resp = ["success"=>$success, "code"=>$code, "mensaje"=>$mensaje, "data"=>$data];
        $this->_helper->json($resp);
    }

    

    /**
     * [Action description]
     * @return [type] [description]
     */
    public function personaEditAction(){
        $success = false;
        $mensaje = "";
        $code = -1;
        $data = [];
        try {
            if(isset($this->dataPost["correo"]) && isset($this->dataPost["nombres"]) && isset($this->dataPost["apellidos"]) && isset($this->dataPost["telefono"]) ){
                $existeUsuario = Usuario::where('username',$this->dataPost["correo"])->exists();
                if( !$existeUsuario ) {
                    $mensaje = "Este correo no está registrado en el sistema.";
                } else {
                    $usuario = Usuario::where('username',$this->dataPost["correo"])
                                    ->with('persona')->get()->last();
                    $persona = $usuario->persona;
                    $persona->nombres = $this->dataPost["nombres"];
                    $persona->ap_pat = $this->dataPost["apellidos"];
                    $persona->celular = $this->dataPost["telefono"];
                    $persona->save();
                    $numeroDirecciones = $persona->direccion->count();
                    $data = [
                        "id_usuario"=>$usuario->id,
                        "id_persona"=>$usuario->id_persona,
                        "username"=>$usuario->username,
                        "nombre"=>$persona->nombres,
                        "apPaterno"=>$persona->ap_pat,
                        "apMaterno"=>$persona->ap_mat,
                        "celular"=>$persona->celular,
                        "direccion"=>$persona->direccion,
                        "numero_direcciones"=>$numeroDirecciones
                        ];
                    $mensaje = "Acción realizada con éxito";
                    $success = true;
                }
            }else{
                $mensaje = "Faltan parámetros";
            }
        } catch (Exception $e) {
            $mensaje = $e;
        }
        $resp = ["success"=>$success, "code"=>$code, "mensaje"=>$mensaje, "data"=>$data];
        $this->_helper->json($resp);
    }

    public function crearDireccionAction(){
        $success = false;
        $mensaje = "";
        $code = -1;
        $data = [];
        try {
            if(isset($this->dataPost["sector"]) && isset($this->dataPost["calle"]) && isset($this->dataPost["numero"]) && isset($this->dataPost["datosUsuario"]) ) {
                
                $datosUsuario = json_decode($this->dataPost["datosUsuario"]);
                $id_persona = $datosUsuario->id_persona;
                $id_usuario = $datosUsuario->id_usuario;
                $existePersona = Persona::find($id_persona);
                
                if( !$existePersona ) {
                    $mensaje = "No se encontró a la persona en el sistema.";
                } else {
                    $descripcionDir = $this->dataPost["calle"]." ".$this->dataPost["numero"]." ".$this->dataPost["detalle"];
                    $direccion = new Direccion;
                    $direccion->id_persona = $id_persona;
                    $direccion->id_sector = $this->dataPost["sector"];
                    $direccion->descripcion = $descripcionDir;
                    $direccion->activo = Direccion::ACTIVA ;
                    $direccion->fecha_creacion = date('Y-m-d H:i:s');
                    $direccion->save();

                    $usuario = Usuario::where('id',$id_usuario)->with('persona')->get()->last();
                    $persona = $usuario->persona;
                    $numeroDirecciones = $persona->direccion->count();
                    $data = [
                        "id_usuario"=>$usuario->id,
                        "id_persona"=>$usuario->id_persona,
                        "username"=>$usuario->username,
                        "nombre"=>$persona->nombres,
                        "apPaterno"=>$persona->ap_pat,
                        "apMaterno"=>$persona->ap_mat,
                        "celular"=>$persona->celular,
                        "direccion"=>$persona->direccion,
                        "numero_direcciones"=>$numeroDirecciones
                        ];
                    $mensaje = "Acción realizada con éxito";
                    $success = true;
                }
            } else {
                $mensaje = "Faltan parámetros";
            }
        } catch (Exception $e) {
            $mensaje = $e;
        }
        $resp = ["success"=>$success, "code"=>$code, "mensaje"=>$mensaje, "data"=>$data];
        $this->_helper->json($resp);
    }

    public function eliminarDireccionAction() {
        $success = false;
        $mensaje = "";
        $code = -1;
        $data = [];
        try {
            if( $this->dataPost["id_direccion"] != "" &&$this->dataPost["datosUsuario"] != "" ) {
                $direccion = Direccion::find($this->dataPost["id_direccion"]);
                if( !$direccion ) {
                    $mensaje = "No se encontró la dirección en el sistema.";
                } else {
                    $datosUsuario = json_decode($this->dataPost["datosUsuario"]);
                    
                    $id_usuario = $datosUsuario->id_usuario;
                    $direccion->delete();
                    $usuario = Usuario::where('id',$id_usuario)->with('persona')->get()->last();
                    $persona = $usuario->persona;
                    $numeroDirecciones = $persona->direccion->count();
                    $data = [
                        "id_usuario"=>$usuario->id,
                        "id_persona"=>$usuario->id_persona,
                        "username"=>$usuario->username,
                        "nombre"=>$persona->nombres,
                        "apPaterno"=>$persona->ap_pat,
                        "apMaterno"=>$persona->ap_mat,
                        "celular"=>$persona->celular,
                        "direccion"=>$persona->direccion,
                        "numero_direcciones"=>$numeroDirecciones
                        ];
                    $mensaje = "Acción realizada con éxito";
                    $success = true;
                }
            }

        } catch (Exception $e) {
            $mensaje = $e;
        }
        $resp = ["success"=>$success, "code"=>$code, "mensaje"=>$mensaje, "data"=>$data];
        $this->_helper->json($resp);
            
        
    }
    ##################################### COMPRAS ############################################
    
    public function getComprasAction() {
        $resp = array("success"=> false,"code" => -1,"data"=>array());
        try {
            if( isset($this->dataPost["id_persona"]) ) {
                $pedido = Pedido::where("creador", $this->dataPost["id_persona"])
                            ->with("productos")
                            ->with("cliente")
                            ->with("direccion")
                            ->orderBy('fecha_creacion', 'desc')
                            ->get();
            }
            $resp = array("success"=> true,"code" => 0,"data"=>$pedido,"nRegistros"=>$pedido->count());
        } catch (Exception $e) { }
        $this->_helper->json($resp);
    }



        
    public function ingresarCompraAction() {
        $success = $success = false;
        $mensaje = "";
        $code = -1;
        $data = [];
        try {
            if( isset($this->dataPost["data"]) ) {
                $opcNuevaDireccion = ( isset($this->dataPost["opc_nueva"]) ) ? $this->dataPost["opc_nueva"] : 0 ;
                $objEnvio = json_decode($this->dataPost["data"],true);
                if( $opcNuevaDireccion > 0 ) {
                    if( isset($this->dataPost["sector"]) && isset($this->dataPost["dir_calle"]) && isset($this->dataPost["dir_numero"]) ) {
                        $datosUsuario = json_decode($this->dataPost["datosUsuario"]);
                        $id_persona = $datosUsuario->id_persona;
                        $id_usuario = $datosUsuario->id_usuario;
                        $descripcionDir = $this->dataPost["dir_calle"]." ".$this->dataPost["dir_numero"];
                        if( isset($this->dataPost["dir_detalle"]) ) {
                            $descripcionDir .= " ".$this->dataPost["dir_detalle"];
                        }
                        $direccion = new Direccion;
                        $direccion->id_persona = $id_persona;
                        $direccion->id_sector = $this->dataPost["sector"];
                        $direccion->descripcion = $descripcionDir;
                        $direccion->activo = Direccion::ACTIVA ;
                        $direccion->fecha_creacion = date('Y-m-d H:i:s');
                        $direccion->save();

                        $id_direccion = $direccion->id;
                        array_push($objEnvio["comprador"]["direccion"], $direccion->toArray());
                        if( $id_direccion ) {
                            $montoTotal = 0;
                            $pedido = new Pedido;
                            $pedido->observacion = "Sin observación";
                            $pedido->estado = Pedido::ESTADO_1_PENDIENTE;
                            $pedido->total = $montoTotal;
                            $pedido->id_direccion = $id_direccion; 
                            $pedido->creador = $id_usuario; 
                            $pedido->fecha_creacion = date('Y-m-d H:i:s');
                            $pedido->save();

                            $montoPedido = $this->calcularCostoPedido($objEnvio["resumen"],$pedido->id);
                            if( !$montoPedido ) {
                                $pedido->delete();
                                $mensaje = "No se pudo guardar su pedido. Por favor inténtelo de nuevo";   
                            } else {
                                $pedido->total = $montoPedido;
                                $pedido->save();
                                $success = true;
                                $mensaje = "Acción realizada con éxito";
                                $data = $objEnvio["comprador"];
                            }
                        } else {
                            $mensaje = "Ocurrió un error al guardar la dirección. Por favor inténtelo más tarde.";    
                        }
                    } else {
                        $mensaje = "Faltan parámetros. Por favor vuelva a intentarlo.";
                    }
                } else {
                    if( $this->dataPost["id_direccion"] != ""  ) {
                        $datosUsuario = json_decode($this->dataPost["datosUsuario"]);
                        $id_persona = $datosUsuario->id_persona;
                        $montoTotal = 0;
                        $id_direccion = $this->dataPost["id_direccion"];
                        $pedido = new Pedido;
                        $pedido->observacion = "Sin observación";
                        $pedido->estado = Pedido::ESTADO_1_PENDIENTE;
                        $pedido->total = $montoTotal;
                        $pedido->id_direccion = $id_direccion; 
                        $pedido->creador = $id_persona;
                        $pedido->fecha_creacion = date('Y-m-d H:i:s');
                        $pedido->save();

                        $montoPedido = $this->calcularCostoPedido($objEnvio["resumen"],$pedido->id);
                        if( !$montoPedido ) {
                            $pedido->delete();
                            $mensaje = "No se pudo guardar su pedido. Por favor inténtelo de nuevo";   
                        } else {
                            $pedido->total = $montoPedido;
                            $pedido->save();
                            $success = true;
                            $mensaje = "Acción realizada con éxito";
                            $data = $objEnvio["comprador"];
                        }
                    } else {
                        $mensaje = "Faltan parámetros. Por favor vuelva a intentarlo.";   
                    }
                }
            } else{
                $mensaje = "Faltan parámetros";
            }
        } catch (Exception $e) {
            $mensaje = $e;
        }
        $resp = ["success"=>$success, "code"=>$code, "mensaje"=>$mensaje, "data"=>$data];
        $this->_helper->json($resp);
    }
    //protected

    public function calcularCostoPedido($resumen, $id_pedido) {
        try {    
            $precioEspecificaciones = $this->getPreciosEspecificaciones();
            $precioProductos = $this->getPreciosProductos();
            $total = 0;
            $dataPedido = [];
            foreach ($resumen as $item) {
                $id_producto_item = $item["id"];
                $cantidad = intval($item["cantidad"]);
                if( $item["especificacionSeleccionada"] != "" ) {
                    $id_especificacion = intval($item["especificacionSeleccionada"]);
                    $precio_item =  ($cantidad * $precioProductos[$id_producto_item]);
                    $precio_item = $precio_item + ($cantidad * $precioEspecificaciones[$id_especificacion]);
                    $precio_item = round($precio_item);
                } else {
                    $precio_item = round( $cantidad * $precioProductos[$id_producto_item]);
                    $id_especificacion = null;
                }
                $dataPedido[] = [
                                "id_producto" => $id_producto_item,
                                "id_pedido" => $id_pedido,
                                "id_especificacion" => $id_especificacion,
                                "cantidad" => $cantidad,
                                "fecha_creacion" => date("Y-m-d H:i:s")
                            ];

                $total = $total + $precio_item;
            }

            PedidoProducto::insert($dataPedido);
            return $total;
        } catch (Exception $e) {
            var_dump($e);
            return null;
        }
    }

    protected function getPreciosEspecificaciones() {
        try {
            $especificaciones = Especificacion::all();
            $precios = array();
            foreach ( $especificaciones as $especificacion ) {
                $precios[$especificacion->id] = $especificacion->valor;
            }
            return $precios;
        } catch (Exception $e) {
            return [];
        }
    }

    protected function getPreciosProductos() {
        try {
            $productos = Producto::all();
            $precios = array();
            foreach ( $productos as $producto ) {
                $precios[$producto->id] = $producto->valor;
            }
            return $precios;
        } catch (Exception $e) {
            return [];
        }
    }
    ################################## UTILIDADES ############################################
    public function validaCorreo($email) {
       $sintaxis = '#^[\w.-]+@[\w.-]+\.[a-zA-Z]{2,6}$#';
       if(preg_match($sintaxis,$email))
            return true;
       else
            return false;
    }

    public function encriptacionContraseniaEloquent($contrasenia, $salt = false) {
        $success = false;
        $mensaje = ""; 
        $log = "";
        $contrasenaEncriptada = "";
        try {
            if( !$salt )
                $salt =  Usuario::SALT;
            $bd = Zend_Registry::get("DB");
            $resultEncriptacion = $bd::select("select SHA2(AES_ENCRYPT(:contrasenia , :salt), 256)", 
                                                            ['contrasenia' => $contrasenia, 'salt' => $salt]);
            foreach ($resultEncriptacion[0] as $key => $value) {
                $contrasenaEncriptada = $value;
                break;
            }
            $success = true;
        } catch (Exception $e) {
            $mensaje = "No se pudo encriptar la contraseña. Inténtelo más tarde";
            $log = $e;
        }

        return array("success" => $success, "mensaje" => $mensaje, "contrasenaEncriptada" => $contrasenaEncriptada, "salt" => $salt,"log" => $log, );
    }

    public function validaRun ( $runCompleto ) {
        $runCompleto = str_replace(".", "", $runCompleto);
        if ( !preg_match("/^[0-9]+-[0-9kK]{1}/",$runCompleto)) return false;
            $rut = explode('-', $runCompleto);
            return strtolower($rut[1]) == self::dv($rut[0]);
    }

    public function modalConfirmarAction(){
        //ActionCode
        $this->_helper->layout()->disableLayout(); 
    }

}

