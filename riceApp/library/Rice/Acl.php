<?php

class Rice_Acl extends Zend_Acl {

	public function __construct($role = 'invitado',$iniFile = 'acl.ini', $objEstablecimiento = null) {		
		/**
		 * Sólo el inicial acl.ini
		 */
		$ini = new Zend_Config_Ini(APPLICATION_PATH . '/configs/navegacion/'.$iniFile);
		$confIni = $ini->toArray();

		$roleTmp = $role;
		$arrAcl = array();
		while(true){
			$rolAux   = new Zend_Config_Ini(APPLICATION_PATH . '/configs/navegacion/roles/'.$roleTmp.'.ini');
			$arrAcl = array_merge($arrAcl,$rolAux->toArray());
			$roleTmp = $confIni['roles']['roles'][$roleTmp]["parent"];
			if(!$roleTmp){
				break;
			}
		}
		
		/**
		 * Recorrido de Roles
		 */
		foreach ($confIni['roles']['roles'] as $rol => $parents) {
			if ($parents['parent'] == '')
				$parents['parent'] = null;

			$this->addRole(new Zend_Acl_Role($rol), $parents['parent']);

		}
		/**
		 * Recorrido de resources
		 */
		foreach ($confIni['resources']['resources'] as $resource) {
			$this->add(new Zend_Acl_Resource($resource));
		}
		/**
		 * Recorrido de Roles para cada etiqueta
		 */
		foreach($arrAcl as $rolFinal => $data){
			if(isset($data['acls']))
			foreach ($data['acls'] as $tipo => $permisos) {
				if($tipo == "allow"){
					foreach ($permisos as $resource) {
						$this->allow($rolFinal, $resource);
					}
				}elseif($tipo == "deny"){
					$this->deny($rolFinal, $resource);
				}
			}
		}
	}

}