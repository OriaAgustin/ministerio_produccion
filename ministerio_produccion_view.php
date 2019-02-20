<?php
/*Separar en clases hijas y padre?*/
class ministerio_produccion_view{
    private $login;
    private $main_view;
    private $login_error;

    public function __construct(){
    	
    }

    public function get_login(){
        $this->set_login();

        return $this->login;
    }

    private function set_login(){
        $head = $this->generate_head();

        $login_div = $this->generate_login_div();
        $body = $this->generate_body($login_div);

        $html = $this->generate_html($head. $body);

        $this->login = $html;
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
   		$login_select =	'<select id="login_select" onchange="change_profile_type(event);" name="login[login_type]">
    						<option value="usuario_interno">Usuario interno</option>	
    						<option value="empresa">Empresa</option>
    					</select>';
    	$login_inputs = '<input id="login_id_input" class="login_input" type="text" placeholder="Email" name="login[login_id]">
   						<input id="login_password_input" class="login_input" type="password" placeholder="Contraseña" name="login[login_password]">';
    	$login_button = '<input type="submit" value="Ingresar">';

        $login_form = '<div class="login_wrapper"><form id="login_form" action="index.php" method="post">'. $login_select. $login_inputs. $login_button. '</form></div>';

   		return $login_form;
   	}

    public function get_main_view(){
    	$this->set_main_view();

    	return $this->main_view;
    }

    private function set_main_view(){
    	$head = $this->generate_head();

        $data = $this->obtain_data();
    	$table = $this->generate_table($data);

    	$body = $this->generate_body($table);

        $html = $this->generate_html($head. $body);

    	$this->main_view = $html;
    }

    private function obtain_data(){
        $this->set_mysql();

        $query = 'SELECT '. join(", ", $this->fields_to_show). ' FROM '. $this->table_to_show;
        $data = $this->mysql->query($query);

        return $data;
    }

    private function generate_table($data){
        $table_head = $this->generate_table_head();
        $table_body = $this->generate_table_body($data);
        $table_foot = $this->generate_table_foot($data);

        $table = '<table>'. $table_head. $table_body. $table_foot. '</table>';

        return $table;
    }

    private function generate_table_head(){
        $colspan = count($this->fields_to_show) + 2;
        $trs[] = '<tr><th colspan='. $colspan. '>'. strtoupper($this->table_to_show). '</th></tr>';
        foreach($this->fields_to_show as $field_to_show){
            $ths[] = '<th>'. ucfirst($field_to_show). '</th>';
        }
        $ths[] = '<th>Editar</th>';
        $ths[] = '<th>Borrar</th>';
        $trs[] = '<tr>'. join("", $ths). '</tr>';
        $table_head = '<thead>'. join("", $trs). '</thead>';

        return $table_head;
    }

    private function generate_table_body($data){
        while($register = $data->fetch_assoc()){
            $tds = array();
            foreach($register as $register_field){
                $tds[] = '<td>'. $register_field. '</td>';
            }
            $tds[] = '<td><input type="submit" value="Editar"></td>';
            $tds[] = '<td><input type="submit" value="Borrar"></td>';
            $trs[] = '<tr>'. join("", $tds). '</tr>';
        }
        $table_body = '<tbody>'. join("", $trs). '</tbody>';

        return $table_body;
    }

    private function generate_table_foot($data){
        $colspan = count($this->fields_to_show) + 2;
        $table_foot = '<tfoot><tr><td colspan="'. $colspan. '">Total: '. $data->num_rows. " ". $this->table_to_show. '</td></tr></tbody>';

        return $table_foot;
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

    private function set_mysql(){
        //No deberia usarse el usuario root y menos sin contrasena.
        $this->mysql = new mysqli('localhost', 'root', '', 'ministerio_produccion');
    }
}