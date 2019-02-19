<?php
class minprod_validator{
    private $mysql;

    public function __construct(){
    	$this->set_mysql();
    }

    private function set_mysql(){
    	$this->mysql = new mysqli("localhost", "root");
    }

    public function validate_login($login){
    	return "MySQL Server info: ". $this->mysql->host_info. " cuit:". $login['id_login']. " password:". $login['password_login'];
    }

}