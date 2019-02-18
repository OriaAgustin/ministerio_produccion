<?php

require_once($_SERVER['DOCUMENT_ROOT']. '/view.php');

$view = new view();

switch($_REQUEST['ajax']){
	default:
		print $view->get_initial_html();
}