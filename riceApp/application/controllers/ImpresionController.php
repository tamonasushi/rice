<?php
class ImpresionController extends Zend_Controller_Action
{

    public function init()
    {
        $key = $this->getRequest()->getParam("key");
        if($key != 'd6e4ad5462e40465d915bfc2afb3504a'){
            $this->_redirect("/");
        }
        $this->app = Zend_Registry::get("appData");
        $this->_helper->layout()->disableLayout(); 
        $this->_helper->viewRenderer->setNoRender(true);
    }

    /**
     * [Action description]
    * @return [type] [description]
    */
    public function indexAction(){
    
        //busco todos los pedidos aceptados
        $resp = array("success"=> false,"code" => 0,"data"=>array());
        try {

            $pedidos = Pedido::where("estado","=", Pedido::ESTADO_2_ACEPTADO )
                ->with("productos")
                ->with("cliente")
                ->with("direccion")
                ->orderBy('fecha_creacion', 'desc')
                ->get();

            if(count($pedidos) > 0 ){
                $resp["success"] = true;
                $resp["data"] = $pedidos;
                $resp["cabecera"] = $this->app["name"];

                foreach ($pedidos as $key => $pedido) {
                    $pedido->estado = Pedido::ESTADO_3_EN_PREPARACION;
                    $pedido->save();
                }
            }
        } catch (Exception $e){

        }
        
	    $this->_helper->json($resp);
    }
}