<?php
class Rice_Controller_Action extends Zend_Controller_Action
{

    public function addScriptJS($script, $includeTime = true)
    {
    	if (APPLICATION_ENV == "development" && $includeTime)
    		$script .= "?time=" . time();
    	$this->view->headScript()->appendFile($script);
    	return $this;
    }

    public function init()
    {
        Zend_Registry::set('responseObject', $this->getResponse());
        Zend_Registry::set('viewObject', $this->view);
        $user = Zend_Auth::getInstance()->getIdentity();
        $action = '/' . $this->getRequest()->getModuleName() . '/' . $this->getRequest()->getControllerName() . '/' . $this->getRequest()->getActionName();
        
        if ($user instanceof Usuario) {
            $this->session = new Zend_Session_Namespace("Rice");
            $this->user = Zend_Auth::getInstance()->getIdentity();
            $this->view->user = Zend_Auth::getInstance()->getIdentity();

            $acl = $user->getAclNavigation();
            $conf = new Zend_Config_Ini(APPLICATION_PATH . "/configs/navegacion/roles/" . Zend_Auth::getInstance()->getIdentity()->getRole() . ".ini", Zend_Auth::getInstance()->getIdentity()->getRole());
            $this->view->uriHomePerfil = $conf->paginainicio;
            if ($acl->isAllowed(($user->getRole() === null) ? "invitado" : $user->getRole(), $action)) {
                
                if (! $this->session->navegando) {
                    
                    $this->session->navegando = true;
                    if (isset($conf->paginainicio)) {
                        return $this->_redirect($conf->paginainicio);
                    }
                }
                
                return;
            }
        } else {
            $acl = new Rice_Acl();
            if ($acl->isAllowed("invitado", $action)) {
                return;
            }
        }
        
        if ($this->getRequest()->isXmlHttpRequest())
            return $this->_helper->json("noautorizado");
        return $this->_redirect("/admin/login");
    }

    protected function _isAjax()
    {
        return $this->_request->isXmlHttpRequest();
    }

    public function preDispatch()
    {
        if (Zend_Auth::getInstance()->hasIdentity()) {
            $user = Zend_Auth::getInstance()->getIdentity();
            if ($user instanceof Model_Usuarios) {}
        }
    }

    public function postDispatch()
    {
        if (! $this->_isAjax()) {
            if (Zend_Auth::getInstance()->hasIdentity()) {
                // INICIA LA NAVEGACION
                $config = new Zend_Config_Xml(APPLICATION_PATH . '/configs/navegacion/navigation.xml', 'nav');
                $container = new Zend_Navigation($config);

                $resource = '/' . $this->getRequest()->getModuleName() . '/' . 
                                $this->getRequest()->getControllerName() . '/' . 
                                $this->getRequest()->getActionName();

                $activas = $container->findAllBy("resource", $resource);

                foreach ($activas as $paginaActiva) {
                    if ($paginaActiva instanceof Zend_Navigation_Page) {
                        $paginaActiva->setActive(true);
                        //Zend_Debug::Dump($paginaActiva);
                    }
                }
                $var = $this->view->navigation($container);

                if ($var instanceof Zend_View_Helper_Navigation) {
                    $var->setAcl(Zend_Auth::getInstance()->getIdentity()
                        ->getAclNavigation())
                        ->setRole(Zend_Auth::getInstance()->getIdentity()
                        ->getRole());
                }
               
            }
        } else {
            $this->_helper->layout()->setLayout("ajax");
        }
    }

}