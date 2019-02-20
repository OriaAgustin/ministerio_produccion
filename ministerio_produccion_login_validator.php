<?php

require_once($_SERVER['DOCUMENT_ROOT']. '/kint.phar');

class ministerio_produccion_login_validator{
    private $mysql;
    private $tables = array();
    private $idfields = array();

    public function __construct(){
    	$this->set_mysql();
    	$this->set_tables();
    	$this->set_id_fields();
    }

    private function set_mysql(){
    	//No deberia usarse el usuario root y menos sin contrasena.
    	$this->mysql = new mysqli('localhost', 'root', '', 'ministerio_produccion');
    }

    private function set_tables(){
    	$this->tables = array('usuario_interno' => 'usuarios_internos', 'empresa' => 'empresas');
    }

    private function set_id_fields(){
    	$this->id_fields = array('usuario_interno' => 'email', 'empresa' => 'cuit');
    }
    /*
		Meter lo de la validacion del cuit y lo de la validacion del mail?
		ESCAPAR ARGUMENTOS
    */
    public function validate_login($login){
    	$query =	'SELECT * FROM '. $this->tables[$login['login_type']]. 
    				' WHERE '. $this->id_fields[$login['login_type']]. '="'. $login['login_id']. 
    				'" AND password=PASSWORD("'. $login['login_password']. '")';
    	$result = $this->mysql->query($query);
		$user = $result->fetch_assoc();
    	
    	if($user){
			return true;
		}

    	return false;
    }
}