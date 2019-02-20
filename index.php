<?php

require_once($_SERVER['DOCUMENT_ROOT']. '/view.php');
require_once($_SERVER['DOCUMENT_ROOT']. '/minprod_validator.php');

$view = new view();

switch(true){
	case $_POST['login']:
		$minprod_validator = new minprod_validator();
		if($minprod_validator->validate_login($_POST['login'])){
			//print $view->get_main_view();
		}else{
			//print $view->get_login_error();
		}
		break;
	default:
		print $view->get_initial_html();
}
