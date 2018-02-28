<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{

	protected function _initResources(){
        $resourceLoader = new Zend_Loader_Autoloader_Resource(array(
            'basePath'  => APPLICATION_PATH,
            'namespace' => ''
        ));
        $resourceLoader->addResourceTypes(array(
            'model' => array(
                'namespace' => 'Model',
                'path'      => 'models'
            ),
            'form' => array(
                'path'      => 'forms',
                'namespace' => 'Form',
            ),
            'mapper' => array(
                'path'      => 'models/mappers',
                'namespace' => 'Model_Mapper',
            ),
        ));
    }

	protected function _initDataBase(){

		$capsule = new Illuminate\Database\Capsule\Manager;

		$resources = $this->getOption("resources");
		$configuracion = array(
			'driver'    => 'mysql',
			'host'      => $resources["db"]["params"]["host"],
			'database'  => $resources["db"]["params"]["dbname"],
			'username'  => $resources["db"]["params"]["username"],
			'password'  => $resources["db"]["params"]["password"],
			'charset'   => 'utf8',
			'collation' => 'utf8_unicode_ci',
			'prefix'    => '',
			);
		$capsule->addConnection($configuracion);
		$capsule->setAsGlobal();
		$capsule->bootEloquent();
		Zend_Registry::set("DB", $capsule);

		$resource = $this->getPluginResource('db');
		$db = $resource->getDbAdapter();
        Zend_Registry::set("db", $db);

	}

	protected function _initAppData(){
		$app = $this->getOption("app");
		Zend_Registry::set("appData", $app);
	}

}

