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
        $links = '<script src="minprod.js"></script>';
        $links .= '<link rel="stylesheet" type="text/css" href="minprod.css">';
        $title = '<title>Ministerio de Produccion</title>';
        $meta = '<meta charset="utf-8"/>';
        $head = '<head>'. $meta. $links. $title. '</head>';

        return $head;
    }

    private function generate_body(){

    	$login_div = $this->generate_login_div();

        $body = '<body>'. $login_div. '</body>';

        return $body;
    }

   	private function generate_login_div(){
   		$login_select =	'<select id="login_select" onchange="change_profile_type(event);">
    						<option value="empresa">Empresa</option>
    						<option value="usuario_interno">Usuario interno</option>	
    					</select>';
    	$login_inputs = '<input id="id_login_input" class="login_input" type="text" placeholder="Cuit" name="login[id_login]">
   						<input id="password_login_input" class="login_input" type="password" placeholder="Contraseña" name="login[password_login]">';
    	//$login_data_div = '<div id="login_data_div">'. $login_inputs. '</div>';

    	$login_button = '<input type="submit" value="Ingresar">';

        $login_form = '<form id="login_form" action="index.php" method="post">'. $login_select. $login_inputs. $login_button. '</form>';

   		return $login_form;
   	}

    public function get_initial_html(){
        return $this->initial_html;
    }

    public function validate_login($post){
    	foreach($post as $key => $value){
    		echo $key;
    		echo '<br>';
    		echo $value;
    		echo '<br>';
    	}
    }
}