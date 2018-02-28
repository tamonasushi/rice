<?php
/**
 * Descrición del Controlador
 */
class AdminController extends Rice_Controller_Action 
{

######################### BASE #############################################	
	/**
	 * [init description]
	 * @return [type] [description]
	 */
	public function init() {

		parent::init();
				 
		$this->_helper->_layout->setLayout('admin') ;

		$this->addScriptJS("/js/modulos/directives.js");
		$this->addScriptJS("/js/modulos/factory.js");
		$this->addScriptJS("/js/modulos/filters.js");
		$this->addScriptJS("/js/modulos/resources.js");

		$this->addScriptJS("/js/modulos/admin/controllers.js");
		$this->addScriptJS("/js/modulos/admin/module.js");

		try{
			 $this->dataPost = Zend_Json::decode($this->getRequest()->getRawBody());
		}catch(Exception $e){
			$this->dataPost = array();
		}

		$this->usuario =  Zend_Auth::getInstance()->getIdentity();

	}

	public function logoutAction(){
		Zend_Auth::getInstance()->clearIdentity();
        Zend_Session::destroy();
		$this->_redirect("/admin/login");
	}

	public function loginAction(){
		$this->_helper->_layout->setLayout('admin_login') ;
	}

	public function setLoginAction(){

		$resp = array("success"=> false,"code" => -1,"data"=>array());
		$elqUsuario = new Usuario();

		$username = $this->getParam("username");
		$password = $elqUsuario->encriptPassword($this->getParam("password"));
		
		try{
			$user = Usuario::where("username", $username)
							->where("password", $password)
							->first();
			if(count($user)){
				$this->inicializeAdapter($user);
				$resp = array("success"=> true,"code" => -1,"data"=>array());
			}
		}catch(Esception $e){

		}
		$this->_helper->json($resp);
	}

    public function inicializeAdapter($mdUsuarios){
        $adapter = new Rice_Auth_Adapter($mdUsuarios);
        $result = Zend_Auth::getInstance()->authenticate($adapter);
        Zend_Auth::getInstance()->getIdentity()->setRole("Usuario");
    }

	/**
	 * [indexAction description]
	 * @return [type] [description]
	 */
	public function indexAction() {
		 //Index de la aplicación
		 $this->_redirect("/admin/estadistica");
	}

######################### REPORTES #############################################
	
	/**
	 * [Action description]
	 * @return [type] [description]
	 */
	public function reportesAction(){
	    //ActionCode
	}
	/**
	 * [Action description]
	 * @return [type] [description]
	 */
	public function getReportesAction(){
	    //ActionCode
	    $resp = array("success"=> false,"code" => -1,"data"=>array());
	    try {

			$ini = $fin = false;

			if(isset($this->dataPost["iniDate"]))
				$ini = date('Y-m-d', strtotime($this->dataPost["iniDate"]));
			if(isset($this->dataPost["endDate"]))
 				$fin = date('Y-m-d', strtotime($this->dataPost["endDate"]));

			$reportes = new Pedido();
			$reportes = $reportes->getTotalPedidosFecha($ini, $fin);
	    	$resp = array("success"=> true,"code" => 0,"data"=>$reportes);

	    } catch (Exception $e) {

	    }
	    $this->_helper->json($resp);
	}

########################## ORDENES ###############################################
	/**
	 * [Action description]
	 * @return [type] [description]
	 */
	public function ordenesAction(){

	    //ActionCode
	}

	/**
	 * [Action description]
	 * @return [type] [description]
	 */
	public function getOrdenesAction(){
	    //ActionCode
	    $resp = array("success"=> false,"code" => -1,"data"=>array());
	    try {
	    	if(isset($this->dataPost["id"])){
	    		$pedido = Pedido::where("id", $this->dataPost["id"])
	    					->with("productos")
	    					->with("cliente")
	    					->with("direccion")
							->orderBy('fecha_creacion', 'desc')
							->get();	
	    	}else{
	    		$pedido = Pedido::where("fecha_creacion",">=", date('Y-m-d 00:00:00', strtotime($this->dataPost["iniDate"]) ))
							->where("fecha_creacion","<=", date('Y-m-d 23:59:59', strtotime( $this->dataPost["endDate"] ) ) )
							->orderBy('fecha_creacion', 'desc')
							->get();
	    	}
	    	$resp = array("success"=> true,"code" => 0,"data"=>$pedido);
	    } catch (Exception $e) {

	    }
	    $this->_helper->json($resp);
	}

	public function ordenesStatusChangeAction(){
	    $resp = array("success"=> false,"code" => 0,"data"=>array());

	    try {
	    	$pedido = Pedido::find($this->dataPost["id"]);
	    	if(count($pedido)){

				switch ($this->dataPost["estado"]) {
					case 1: // acepto
						$estado = 2;
						break;
					case 4: //despacho
						$estado = 4;
						break;
					case 5: //rechazo
						$estado = 5;
						break;
				}

    			$pedido->estado = $estado;
				$pedido->modificador = $this->usuario->id;
				$pedido->fecha_modificacion = Zend_Date::NOW()->get("yyyy-MM-dd HH:mm:ss");
    			$pedido->save();
    			$resp = array("success"=> true,"code" => 0,"data"=>array("estado" => $estado));
	    	}
	    } catch (Exception $e) {
	    	$resp = array("success"=> false,"code" => 0,"data"=>$e->getMessage());
	    }

	    $this->_helper->json($resp);
	}
	
########################## ESTADISTICAS #########################################

	/**
	 * [Action description]
	 * @return [type] [description]
	 */
	public function estadisticaAction(){
	    //ActionCode
	}
	/**
	 * [Action description]
	 * @return [type] [description]
	 */
	public function getEstadisticasAction(){
	    //ActionCode
	    
	    $resp = array("success"=> false,"code" => -1,"data"=>array());
	    try {

			$ini = $fin = false;

			if(isset($this->dataPost["iniDate"]))
				$ini = date('Y-m-d', strtotime($this->dataPost["iniDate"]));
			if(isset($this->dataPost["endDate"]))
 				$fin = date('Y-m-d', strtotime($this->dataPost["endDate"]));

			$pedido = new Pedido();
			$ventasSemana = $pedido->getTotalPedidosFecha($ini, $fin, "ORDER BY `fecha_creacion` ASC");
	    	$resp = array("success"=> true,"code" => 0,
				"data"=>array(
					"ventasSemana"=>$ventasSemana
					)
				);

	    } catch (Exception $e) {

	    }
	    $this->_helper->json($resp);
	}

	
########################### CLIENTES #############################################
	/**
	 * [Action description]
	 * @return [type] [description]
	 */
	public function clientesAction(){
	    //ActionCode
	}

	/**
	 * [Action description]
	 * @return [type] [description]
	 */
	public function getClientesAction(){
	    //ActionCode
	    $resp = array("success"=> false,"code" => -1,"data"=>array());

	    try {
	    	$clientes = UsuarioPerfil::where("id_perfil", Perfil::CLIENTE)
				->with("usuario")
				->limit(10)->offset(0)
				->get();	
	    	$count =  UsuarioPerfil::where("id_perfil", Perfil::CLIENTE)->count();
	    	$resp = array("success"=> true,"code" => 0,"data"=>$clientes, "nrows"=>$count);

	    } catch (Exception $e) {

	    }
	    $this->_helper->json($resp);
	}
			
####################### CATEGORIAS ###############################################
	
	/**
	 * [Action description]
	 * @return [type] [description]
	 */
	public function categoriasAction(){
	    //ActionCode
	}

	/**
	 * [Action description]
	 * @return [type] [description]
	 */
	public function getCategoriasAction(){
	    //ActionCode
	    $resp = array("success"=> false,"code" => -1,"data"=>array());
	    try {
	    	if(isset($this->dataPost["id"])){
	    		$categorias = Categoria::find($this->dataPost["id"]);
	    	}else{
	    		$categorias = Categoria::all();	
	    	}
	    	$resp = array("success"=> true,"code" => 0,"data"=>$categorias);
	    } catch (Exception $e) {

	    }
	    $this->_helper->json($resp);
	}
	

	public function categoriasStatusChangeAction(){
	    $resp = array("success"=> false,"code" => 0,"data"=>array());

	    try {
	    	$categoria = Categoria::find($this->dataPost["id"]);
	    	if(count($categoria)){

	    		switch ($this->dataPost["mov"]) {
	    			case 'Activar':
	    				$activo = true;
	    				break;
	    			case 'Desactivar':
	    				$activo = false;
	    				break;
	    		}

	    		if(isset($activo)){
	    			$categoria->activo = $activo;
	    			$categoria->modificador = $this->usuario->id;
					$categoria->fecha_modificacion = Zend_Date::NOW()->get("yyyy-MM-dd HH:mm:ss");
	    			$categoria->save();
	    			$resp = array("success"=> true,"code" => 0,"data"=>array("activo" => $activo));
	    		}
	    	}
	    } catch (Exception $e) {
	    	$resp = array("success"=> false,"code" => 0,"data"=>$e->getMessage());
	    }

	    $this->_helper->json($resp);
	}

	/**
	 * [Action description]
	 * @return [type] [description]
	 */
	public function categoriasEditAction(){
		$resp = array("success"=> false,"code" => 0,"data"=>array());

		try {

			if(isset($this->dataPost["categoria"]["id"])){
				//editar
				$categoria = Categoria::find($this->dataPost["categoria"]["id"]);
			}else{
				//crear
				$categoria = new Categoria();
				$categoria->fecha_creacion = Zend_Date::NOW()->get("yyyy-MM-dd HH:mm:ss");
				$categoria->creador = $this->usuario->id;
			}
			
			$categoria->activo = @$this->dataPost["categoria"]["activo"];
			$categoria->descripcion = @$this->dataPost["categoria"]["descripcion"];
			$categoria->fecha_modificacion = Zend_Date::NOW()->get("yyyy-MM-dd HH:mm:ss");
			$categoria->modificador = $this->usuario->id;
			$categoria->save();
			
			$resp = array("success"=> true,"code" => 0,"data"=>$categoria);

		} catch (Exception $e) {
			$resp = array("success"=> false,"code" => -1,"data"=>$e->getMessage());
		}
		$this->_helper->json($resp);
	}

######################## PRODUCTOS ###############################################
	/**
	 * [Action description]
	 * @return [type] [description]
	 */
	public function productosAction(){
	    //ActionCode
	}

	public function uploadImgProductoAction(){
	    //ActionCode 

		$body = $this->getRequest()->getRawBody();
        $data = Zend_Json::decode($body);
        $file = @$_FILES["file"];

		$pathImg = "/img/productos/".$this->getParam("id_producto")."_".$file["name"];
    	if($file && move_uploaded_file($file["tmp_name"], PUBLIC_PATH. $pathImg)){
			
			if((@$this->getParam("id_producto"))){
				$producto = Producto::find($this->getParam("id_producto"));
				$producto->img = $pathImg;
				$producto->fecha_modificacion = Zend_Date::NOW()->get("yyyy-MM-dd HH:mm:ss");
				$producto->modificador = $this->usuario->id;
				$producto->save();
			}

    		$this->_helper->json(array("success" => true, "file" => $file, "path" => $pathImg )); 
    	}
	    $this->_helper->json(array("success" => false, "msj" => "Error al interactuar con la orden 1")); 

	}

	/**
	 * [Action description]
	 * @return [type] [description]
	 */
	public function getProductosAction(){
	    //ActionCode
	    $resp = array("success"=> false,"code" => -1,"data"=>array());
	    try {
	    	if(isset($this->dataPost["id"])){
	    		$productos = Producto::with("especificacion")->find($this->dataPost["id"]);
	    	}else{
	    		$productos = Producto::with("categoria")->get();	
	    	}
	    	$resp = array("success"=> true,"code" => 0,"data"=>$productos);
	    } catch (Exception $e) {

	    }
	    $this->_helper->json($resp);
	}

	public function productosStatusChangeAction(){
	    $resp = array("success"=> false,"code" => 0,"data"=>array());

	    try {
	    	$producto = Producto::find($this->dataPost["id"]);
	    	if(count($producto)){

	    		switch ($this->dataPost["mov"]) {
	    			case 'Activar':
	    				$activo = true;
	    				break;
	    			case 'Desactivar':
	    				$activo = false;
	    				break;
	    		}

	    		if(isset($activo)){
	    			$producto->activo = $activo;
					$producto->fecha_modificacion = Zend_Date::NOW()->get("yyyy-MM-dd HH:mm:ss");
					$producto->modificador = $this->usuario->id;
	    			$producto->save();
	    			$resp = array("success"=> true,"code" => 0,"data"=>array("activo" => $activo));
	    		}
	    	}
	    } catch (Exception $e) {
	    	$resp = array("success"=> false,"code" => 0,"data"=>$e->getMessage());
	    }

	    $this->_helper->json($resp);
	}

	/**
	 * [Action description]
	 * @return [type] [description]
	 */
	public function productosEditAction(){
	    $resp = array("success"=> false,"code" => 0,"data"=>array());
	    $fechaProceso = Zend_Date::NOW()->get("yyyy-MM-dd HH:mm:ss");
	    try {

	    	if(isset($this->dataPost["id"])){
	    		$Producto = Producto::find($this->dataPost["id"]);
		    	if($Producto){
					$Producto->nombre = $this->dataPost["nombre"];
					$Producto->descripcion = $this->dataPost["descripcion"];
					$Producto->valor = $this->dataPost["valor"];
					$Producto->id_categoria = $this->dataPost["id_categoria"];
					$Producto->img = $this->dataPost["img"];
					$Producto->activo = @$this->dataPost["activo"];
					$Producto->modificador = $this->usuario->id;
					$Producto->fecha_modificacion = $fechaProceso;
				}else{
					throw new Exception("No se encuentra el producto", -1);
				}
	    		
	    	}else{
		    	$Producto = new Producto();
				$Producto->nombre = $this->dataPost["nombre"];
				$Producto->descripcion = $this->dataPost["descripcion"];
				$Producto->valor = $this->dataPost["valor"];
				$Producto->id_categoria = $this->dataPost["id_categoria"];
				$Producto->img = $this->dataPost["img"];
				$Producto->activo = @$this->dataPost["activo"];
				$Producto->creador = $this->usuario->id;
				$Producto->fecha_creacion = $fechaProceso;
	    	}
	    	
			$Producto->save();
			if(isset($this->dataPost["id"])){
				ProductoEspecificacion::where("id_producto", $this->dataPost["id"])->delete();
			}

	    	if(isset($this->dataPost["especificacionesSeleccionadas"])){
	    		foreach ($this->dataPost["especificacionesSeleccionadas"] as $idEspecificacion => $boolean) {
	    			if($boolean){
	    				$pe = new ProductoEspecificacion();
						$pe->id_especificacion = $idEspecificacion;
						$pe->id_producto = $Producto->id;
						$pe->creador =$this->usuario->id;
						$pe->fecha_creacion = $fechaProceso;
						$pe->save();
	    			}
	    		}
	    	}
	    	
	    	$resp = array("success"=> true,"code" => 0,"data"=> Producto::with("especificacion")->find($Producto->id));
	    	
	    } catch (Exception $e) {
	    	$resp = array("success"=> false,"code" => 0,"data"=>$e->getMessage());
	    }
	    $this->_helper->json($resp);
	}
	
######################## ESPECIFICACIONES ########################################

	/**
	 * [Action description]
	 * @return [type] [description]
	 */
	public function especificacionesAction(){
	    //ActionCode
	}


	/**
	 * [Action description]
	 * @return [type] [description]
	 */
	public function getEspecificacionAction(){
	    //ActionCode
	    $resp = array("success"=> false,"code" => -1,"data"=>array());
	    try {
	    	if(isset($this->dataPost["id"])){
	    		$especificacion = Especificacion::find($this->dataPost["id"]);	
	    	}else{
	    		$especificacion = Especificacion::all();	
	    	}
	    	$resp = array("success"=> true,"code" => 0,"data"=>$especificacion);
	    } catch (Exception $e) {

	    }
	    $this->_helper->json($resp);
	}
	

	public function especificacionStatusChangeAction(){
	    $resp = array("success"=> false,"code" => 0,"data"=>array());

	    try {
	    	$especificacion = Especificacion::find($this->dataPost["id"]);
	    	if(count($especificacion)){

	    		switch ($this->dataPost["mov"]) {
	    			case 'Activar':
	    				$activo = true;
	    				break;
	    			case 'Desactivar':
	    				$activo = false;
	    				break;
	    		}

	    		if(isset($activo)){
	    			$especificacion->activo = $activo;					
					$especificacion->fecha_modificacion = Zend_Date::NOW()->get("yyyy-MM-dd HH:mm:ss");
					$especificacion->modificador = $this->usuario->id;
	    			$especificacion->save();
	    			$resp = array("success"=> true,"code" => 0,"data"=>array("activo" => $activo));
	    		}
	    	}
	    } catch (Exception $e) {
	    	$resp = array("success"=> false,"code" => 0,"data"=>$e->getMessage());
	    }

	    $this->_helper->json($resp);
	}

	/**
	 * [Action description]
	 * @return [type] [description]
	 */
	public function especificacionEditAction(){
		$resp = array("success"=> false,"code" => 0,"data"=>array());

		try {

			if(isset($this->dataPost["especificacion"]["id"])){
				//editar
				$especificacion = Especificacion::find($this->dataPost["especificacion"]["id"]);
			}else{
				//crear
				$especificacion = new Especificacion();
				$especificacion->fecha_creacion = Zend_Date::NOW()->get("yyyy-MM-dd HH:mm:ss");
				$especificacion->creador = $this->usuario->id;
			}
			
			$especificacion->activo = @$this->dataPost["especificacion"]["activo"];
			$especificacion->descripcion = @$this->dataPost["especificacion"]["descripcion"];
			$especificacion->valor = @$this->dataPost["especificacion"]["valor"];
			$especificacion->fecha_modificacion = Zend_Date::NOW()->get("yyyy-MM-dd HH:mm:ss");
			$especificacion->modificador = $this->usuario->id;
			$especificacion->save();
			
			$resp = array("success"=> true,"code" => 0,"data"=>$especificacion);

		} catch (Exception $e) {
			$resp = array("success"=> false,"code" => -1,"data"=>$e->getMessage());
		}
		$this->_helper->json($resp);
	}


######################### SECTORES ###############################################
	/**
	 * [Action description]
	 * @return [type] [description]
	 */
	public function sectoresAction(){
	    //ActionCode
	}

	/**
	 * [Action description]
	 * @return [type] [description]
	 */
	public function getSectoresAction(){
	    //ActionCode
	    $resp = array("success"=> false,"code" => -1,"data"=>array());
	    try {
	    	if(isset($this->dataPost["id"])){
	    		$sectores = Sector::find($this->dataPost["id"]);	
	    	}else{
	    		$sectores = Sector::all();	
	    	}
	    	$resp = array("success"=> true,"code" => 0,"data"=>$sectores);
	    } catch (Exception $e) {

	    }
	    $this->_helper->json($resp);
	}

	/**
	 * [Action description]
	 * @return [type] [description]
	 */
	public function sectoresStatusChangeAction(){
	    $resp = array("success"=> false,"code" => 0,"data"=>array());

	    try {
	    	$sector = Sector::find($this->dataPost["id"]);
	    	if(count($sector)){

	    		switch ($this->dataPost["mov"]) {
	    			case 'Activar':
	    				$activo = true;
	    				break;
	    			case 'Desactivar':
	    				$activo = false;
	    				break;
	    		}

	    		if(isset($activo)){
	    			$sector->activo = $activo;					
					$sector->fecha_modificacion = Zend_Date::NOW()->get("yyyy-MM-dd HH:mm:ss");
					$sector->modificador = $this->usuario->id;
	    			$sector->save();
	    			$resp = array("success"=> true,"code" => 0,"data"=>array("activo" => $activo));
	    		}
	    	}
	    } catch (Exception $e) {
	    	$resp = array("success"=> false,"code" => 0,"data"=>$e->getMessage());
	    }

	    $this->_helper->json($resp);
	}

	/**
	 * [Action description]
	 * @return [type] [description]
	 */
	public function sectoresEditAction(){
		$resp = array("success"=> false,"code" => 0,"data"=>array());

		try {

			if(isset($this->dataPost["sector"]["id"])){
				//editar
				$sector = Sector::find($this->dataPost["sector"]["id"]);
			}else{
				//crear
				$sector = new Sector();
				$sector->fecha_creacion = Zend_Date::NOW()->get("yyyy-MM-dd HH:mm:ss");
				$sector->creador = $this->usuario->id;
			}
			
			$sector->activo = @$this->dataPost["sector"]["activo"];
			$sector->valor = @$this->dataPost["sector"]["valor"];
			$sector->descripcion = @$this->dataPost["sector"]["descripcion"];
			$sector->fecha_modificacion = Zend_Date::NOW()->get("yyyy-MM-dd HH:mm:ss");
			$sector->modificador = $this->usuario->id ;
			$sector->save();
			
			$resp = array("success"=> true,"code" => 0,"data"=>$sector);

		} catch (Exception $e) {
			
		}
		$this->_helper->json($resp);
	}
	
########################## USUARIOS ##############################################
	/**
	 * [Action description]
	 * @return [type] [description]
	 */
	public function usuariosAction(){
	    //ActionCode
	}

	/**
	 * [Action description]
	 * @return [type] [description]
	 */
	public function getUsuariosAction(){
	    //ActionCode
	    $resp = array("success"=> false,"code" => -1,"data"=>array());

	    try {
	    	$clientes = UsuarioPerfil::where("id_perfil", Perfil::USUARIO)
				->with("usuario")
				->limit(10)->offset(0)
				->get();	
	    	$count =  UsuarioPerfil::where("id_perfil", Perfil::USUARIO)->count();
	    	$resp = array("success"=> true,"code" => 0,"data"=>$clientes, "nrows"=>$count);

	    } catch (Exception $e) {

	    }
	    $this->_helper->json($resp);
	}
	
######################### PERFIL #################################################
	/**
	 * [Action description]
	 * @return [type] [description]
	 */
	public function profileAction(){
	    //ActionCode
	}

	public function getProfileAction(){
	    $resp = array("success"=> false,"code" => 0,"data"=>array());
	    $this->_helper->json($resp);
	}

	/**
     * [Action description]
     * @return [type] [description]
     */
	 public function modalConfirmarAction(){
		//ActionCode
		$this->_helper->layout()->disableLayout(); 
    }

}
