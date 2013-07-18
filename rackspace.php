<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

define('RAXSDK_TIMEOUT', 120);

class CI_Rackspace {

	function __construct($config = null){
		if(is_null($config)){
			error_log('No Rackspace Config file found');
			return;
		}
		$this->config = $config;
	}

	public function getConnection(){
		return new \OpenCloud\Rackspace($this->config['AUTHURL'], array( 'username' => $this->config['USERNAME'], 'apiKey' => $this->config['APIKEY'] ));
	}

	public function getObjStore(){
		return $this->getConnection()->ObjectStore('cloudFiles', 'LON');
	}

	public function createContainer($name){
		// create a new container
		$container = $this->getObjStore()->Container();
		$container->Create(array('name' => $name ));

		// publish it to the CDN
		$container->PublishToCDN();

		return $container;
	}

	public function upload($file, $name = null){
		if(is_null($name)){
			$name = $file;
		}

		$container = $this->createContainer($this->config['CONTAINER']);

		$object = $container->DataObject();
		$object->Create(array('name'=> $name), $file);

		return $object;
	}
}
