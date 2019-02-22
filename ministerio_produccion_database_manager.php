<?php

require_once($_SERVER['DOCUMENT_ROOT']. '/kint.phar');

class ministerio_produccion_database_manager{
    private $mysql;

    public function __construct(){
    	$this->set_mysql();
    }

    private function set_mysql(){
    	//No deberia usarse el usuario root y menos sin contrasena.
    	$this->mysql = new mysqli('localhost', 'root', '', 'ministerio_produccion');
    }

    public function validate_login($login, $meta_data){
        $this->escape_data($login);
        $query =    'SELECT * FROM '. $meta_data['table']. 
                    ' WHERE '. $login['login_id_field']. '="'. $login['login_id']. 
                    '" AND password=PASSWORD("'. $login['login_password']. '")';
        
        $result = $this->mysql->query($query);
        $user = $result->fetch_assoc();
        
        if($user){ 
            return true;
        }

        return false;
    }

    public function escape_data(&$data){
        if(is_array($data)){
            foreach($data as $key => $value){
                $data[$key] = $this->mysql->real_escape_string($value);
            }
        }else{
            $data = $this->mysql->real_escape_string($data);
        }
    }

    public function define_main_view_data($login, $meta_data){
        switch($login['login_type']){
            case "usuario_interno":
                $query = 'SELECT '. join(", ", $meta_data['fields_to_show']). ' FROM '. $meta_data['table_to_show'];
                break;
            case "empresa":
                $query =    'SELECT '. join(", ", $meta_data['fields_to_show']). ' FROM '. $meta_data['table_to_show']. 
                            ' WHERE '. $login['login_id_field']. '="'. $login['login_id']. '"';
        }

        $data = $this->mysql->query($query);

        return $data;
    }

    public function define_fields_to_edit($pk_value, $meta_data){
        $this->escape_data($item);
        $query =    'SELECT '. join(", ", $meta_data['fields_to_edit']). ' FROM '. $meta_data['table_to_show']. 
                    ' WHERE '. $meta_data['table_to_show_pk']. '="'. $pk_value. '"';

        $data = $this->mysql->query($query);
        $data = $data->fetch_assoc();

        return $data;
    }

    public function save_edited_item($data, $meta_data){
        $data = json_decode($data['data'], true);
        $pk_value = $data['pk_value'];
        unset($data['pk_value']);
        $modifications = $data;
        
        if($modifications){
            foreach($modifications as $field => $value){
                $this->escape_data($field);
                $this->escape_data($value);
                if($field != 'password'){
                    $set_statements[] = $field. '="'. $value. '"'; 
                }else{
                    $set_statements[] = $field. '=PASSWORD("'. $value. '")'; 
                }
            }
            $set = ' SET '. join(", ", $set_statements);
            $query = 'UPDATE '. $meta_data['table_to_show']. $set. ' WHERE '. $meta_data['table_to_show_pk']. '="'. $pk_value. '"';
            
            $result = $this->mysql->query($query);
            if(!$result){
                return false;
            }
        }

        return true;
    }

    public function delete_item($data, $meta_data){
        $query = 'DELETE FROM '. $meta_data['table_to_show']. ' WHERE '. $meta_data['table_to_show_pk']. '="'. $data['pk_value']. '"';
        
        $result = $this->mysql->query($query);
        if(!$result){
            return false;
        }

        return true;
    }

    public function save_added_item($params, $meta_data){
        $added_data = json_decode($params['data'], true);

        foreach($added_data as $field => $value){
            $this->escape_data($field);
            $this->escape_data($value);
            if($field != 'password'){
                $values_statements[] = '"'. $value. '"'; 
            }else{
                $values_statements[] = 'PASSWORD("'. $value. '")'; 
            }
            $fields_statements[] = $field;
        }
        $values = ' VALUES('. join(", ", $values_statements). ')';
        $fields = ' ('. join(", ", $fields_statements). ')';
        $query = 'INSERT INTO '. $meta_data['table_to_show']. $fields. $values;
        
        $result = $this->mysql->query($query);
        if(!$result){
            return false;
        }

        return true;
    }

    public function define_pdf_html_data(){
        $query = 'SELECT cuit, nombre FROM empresas';
        $data = $this->mysql->query($query);
        while($register = $data->fetch_assoc()){
            $empresas[$register['cuit']] = $register['nombre'];
        }

        $query = 'SELECT cuit, nombre_completo, dni FROM empleados';
        $data = $this->mysql->query($query);
        while($register = $data->fetch_assoc()){
            $empleados[$register['cuit']][] = $register['nombre_completo']. " - DNI:". $register['dni'];
        }

        return array('empresas' => $empresas, 'empleados' => $empleados);
    }
}