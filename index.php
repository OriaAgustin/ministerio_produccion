<?php
require_once($_SERVER['DOCUMENT_ROOT']. '/ministerio_produccion_view.php');
require_once($_SERVER['DOCUMENT_ROOT']. '/ministerio_produccion_login_validator.php');

switch(true){
	case $_POST['login']:
		$ministerio_produccion_login_validator = new ministerio_produccion_login_validator();
		if($ministerio_produccion_login_validator->validate_login($_POST['login'])){
			$class = 'ministerio_produccion_'. $_POST['login']['login_type']. '_view';
			require_once($_SERVER['DOCUMENT_ROOT']. '/'. $class. '.php');
			$ministerio_produccion_view = new $class;
			print $ministerio_produccion_view->get_main_view();
		}else{
			$ministerio_produccion_view = new ministerio_produccion_view();
			print $ministerio_produccion_view->get_login_error();
		}
		break;
	default:
		$ministerio_produccion_view = new ministerio_produccion_view();
		print $ministerio_produccion_view->get_login();
}
