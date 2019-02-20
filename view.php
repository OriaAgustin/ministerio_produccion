<?php
/*Separar en clases hijas y padre?*/
class view{
    private $initial_html;
    private $main_view;
    private $login_error;

    public function __construct(){
    	$this->set_initial_html();
    }

    private function set_initial_html(){
        $head = $this->generate_head();

        $login_div = $this->generate_login_div();
        $body = $this->generate_body($login_div);

        $html = $this->generate_html($head. $body);

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

    private function generate_body($inner_html){
        $body = '<body>'. $inner_html. '</body>';

        return $body;
    }

    private function generate_html($inner_html){
    	$html = '<!DOCTYPE html><html lang="es">'. $inner_html. '</html>';

    	return $html;
    }

   	private function generate_login_div(){
   		$login_select =	'<select id="login_select" onchange="change_profile_type(event);" name="login[type_login]">
    						<option value="usuario_interno">Usuario interno</option>	
    						<option value="empresa">Empresa</option>
    					</select>';
    	$login_inputs = '<input id="id_login_input" class="login_input" type="text" placeholder="Email" name="login[id_login]">
   						<input id="password_login_input" class="login_input" type="password" placeholder="Contraseña" name="login[password_login]">';
    	$login_button = '<input type="submit" value="Ingresar">';

        $login_form = '<div class="login_wrapper"><form id="login_form" action="index.php" method="post">'. $login_select. $login_inputs. $login_button. '</form></div>';

   		return $login_form;
   	}

   	public function get_login(){
        return $this->initial_html;
    }

    public function get_main_view(){
    	$this->set_main_view();

    	return $this->main_view;
    }

    private function set_main_view(){
    	$head = $this->generate_head();

    	$main_view = '<p>Prueba</p>';

    	$body = $this->generate_body($error_form);

        $html = $this->generate_html($head. $body);

    	$this->login_error = $html;
    }

    public function get_login_error(){
    	$this->set_login_error();
    	
    	return $this->login_error;
    }

    private function set_login_error(){
        $head = $this->generate_head();

    	$message =	'<p>Usuario o contrase&nacute;a no validos.</p>';
    	$retry_button = '<input type="submit" value="Volver a intentar">';
        $error_form = '<div class="login_wrapper"><form id="error_form" action="index.php" method="post">'. $message. $retry_button. '</form></div>';
        $body = $this->generate_body($error_form);

        $html = $this->generate_html($head. $body);

    	$this->login_error = $html;
    }
}