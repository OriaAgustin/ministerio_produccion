<?php
class view{
    private $initial_html;

    public function __construct(){
    	$this->set_initial_html();
    }

    private function set_initial_html(){
    	$head = $this->generate_head();
		$body = $this->generate_body();

		$html = '<!DOCTYPE html><html lang="es">'. $head. $body. '</html>';

    	$this->initial_html = $html;
    }

   	private function generate_head(){
   		$links = '<script src="js/ejercicio.js"></script>';
   		$title = '<title>Esto es una prueba</title>';
   		$head = '<head><meta charset="utf-8"/>'. $links. $title. '</head>';

   		return $head;
   	}

   	private function generate_body(){

   		$select = 	'<select onchange="change_profile_type();">
  						<option value="empresa">Empresa</option>
  						<option value="usuario_interno">Usuario interno</option>
					</select>';


		$login_div = '<div id="login_div">'. $select. '</div>';
		
   		$body = '<body>'. $login_div. '</body>';

   		return $body;
   	}

    public function get_initial_html(){
    	return $this->initial_html;
    }
}