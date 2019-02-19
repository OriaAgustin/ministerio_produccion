<?php

require_once($_SERVER['DOCUMENT_ROOT']. '/view.php');
require_once($_SERVER['DOCUMENT_ROOT']. '/minprod_validator.php');

$view = new view();

switch(true){
	case $_POST['login']:
		$minprod_validator = new minprod_validator();
		print $minprod_validator->validate_login($_POST['login']);
		break;
	default:
		print $view->get_initial_html();
}
