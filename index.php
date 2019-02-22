<?php

require_once($_SERVER['DOCUMENT_ROOT']. '/ministerio_produccion_view.php');
require_once($_SERVER['DOCUMENT_ROOT']. '/ministerio_produccion_database_manager.php');
require_once($_SERVER['DOCUMENT_ROOT']. '/libraries/kint.phar');

session_start();

$ministerio_produccion_database_manager = new ministerio_produccion_database_manager();
$ministerio_produccion_view = new ministerio_produccion_view;

if(!isset($_SESSION['login'])){
	switch($_REQUEST['ajax']){
		case 'login':
			$login = json_decode($_REQUEST['login'], true);
			$meta_data = array();
			//[!] $meta_data deberia sacarse de un archivo de configuracion.
			if($login['login_type'] == 'usuario_interno'){ 
                $login['login_id_field'] = 'email';
                $meta_data['table'] = 'usuarios_internos';
                $meta_data['table_to_show'] = 'empresas';
                $meta_data['fields_to_show'] = array('cuit', 'nombre');
                $meta_data['fields_to_edit'] = array('cuit', 'nombre', 'password');
                $meta_data['table_to_show_pk'] = 'cuit';
	        }else{
            	$login['login_id_field'] = 'cuit';
                $meta_data['table'] = 'empresas';
                $meta_data['table_to_show'] = 'empleados';
                $meta_data['fields_to_show'] = array('nombre_completo', 'dni');
                $meta_data['fields_to_edit'] = array('nombre_completo', 'dni');
                $meta_data['table_to_show_pk'] = 'dni';
        	}
			if($ministerio_produccion_database_manager->validate_login($login, $meta_data)){
				$_SESSION['login'] = $login;
				$_SESSION['meta_data'] = $meta_data;
				print $ministerio_produccion_view->get_main_view();
			}else{
				print $ministerio_produccion_view->get_login_error();
			}
			break;
		default:
			$ministerio_produccion_view = new ministerio_produccion_view();
			print $ministerio_produccion_view->get_login();
	}
}else{
	switch($_REQUEST['ajax']){
		case 'edit_item':
			print $ministerio_produccion_view->get_edit_item($_REQUEST['pk_value']);	
			break;
		case 'main_view':
			print $ministerio_produccion_view->get_main_view();
			break;
		case 'add_item':
			print $ministerio_produccion_view->get_add_item($_SESSION['meta_data']);
			break;
		case 'save_edited_item':
		case 'delete_item':
		case 'save_added_item':
			$result = $ministerio_produccion_database_manager->$_REQUEST['ajax']($_REQUEST, $_SESSION['meta_data']);
			print $ministerio_produccion_view->get_show_result($result, $_REQUEST['ajax']);
			break;
		case 'generate_report':
			$ministerio_produccion_view->generate_report();
			print $ministerio_produccion_view->get_generate_report();
			break;
		case 'logout':
			session_unset();
			print $ministerio_produccion_view->get_login();
		default:
			session_unset();
	}
}