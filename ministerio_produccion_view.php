<?php

require_once($_SERVER['DOCUMENT_ROOT']. '/kint.phar');
/*Separar en clases hijas y padre?*/
class ministerio_produccion_view{
    private $login;
    private $main_view;
    private $login_error;
    private $edit_item;
    private $delete_item;

    public function __construct(){
    	
    }

    private function generate_head(){
        $links = '<script src="ministerio_produccion.js"></script>';
        $links .= '<script src="minAjax.js"></script>';
        $links .= '<link rel="stylesheet" type="text/css" href="ministerio_produccion.css">';
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
        $head = $this->generate_head();
        $body = $this->generate_body($inner_html);
    	$html = '<!DOCTYPE html><html lang="es">'. $head. $body. '</html>';

    	return $html;
    }

    public function get_login(){
        $this->set_login();

        return $this->login;
    }

    private function set_login(){
        //$head = $this->generate_head();
        $login_select = '<select id="login_type_select" onchange="change_profile_type(event);">
                            <option value="usuarios_internos">Usuario interno</option>    
                            <option value="empresas">Empresa</option>
                        </select>';
        $login_inputs = '<input id="login_id_input" class="login_input" type="text" placeholder="Email">
                        <input id="login_password_input" class="login_input" type="password" placeholder="Contraseña">';
        $login_button = '<input type="button" value="Ingresar" onclick="login();">';

        $login = '<div class="login_wrapper">'. $login_select. $login_inputs. $login_button. '</div>';

        //$body = $this->generate_body($login_div);

        //$html = $this->generate_html($head. $body);
        $html = $this->generate_html($login);
        $this->login = $html;
    }

    public function get_main_view(){
    	$this->set_main_view();

    	return $this->main_view;
    }

    private function set_main_view(){
        $data = $this->obtain_data();
    	$table = $this->generate_table($data);

        $html = $this->generate_html('<div class="main_view_wrapper">'. $table. '</div>');
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
        if($data){
            $meta_data = $data->fetch_field();
            if($meta_data->flags & MYSQLI_PRI_KEY_FLAG) { 
                $ajax_data['meta_data']['primary_key'] = $meta_data->name;
                $ajax_data['meta_data']['table'] = $meta_data->table;
            }
            while($register = $data->fetch_assoc()){
                $tds = array();
                foreach($register as $field => $value){
                    $ajax_data['data'][$field] = $value;
                    $tds[] = '<td>'. $value. '</td>';
                }
                $tds[] = '<td><input type="button" value="Editar" onclick=\'edit_item('. json_encode($ajax_data). ');\'></td>';
                $tds[] = '<td><input type="button" value="Borrar" onclick=\'delete_item('. json_encode($ajax_data). ');\'></td>';
                $trs[] = '<tr>'. join("", $tds). '</tr>';
            }
        }
        $table_body = '<tbody>'. join("", $trs). '</tbody>';

        return $table_body;
    }

    private function generate_table_foot($data){
        $num_items = $data ? $data->num_rows : 0;
        $colspan = count($this->fields_to_show) + 2;
        $table_foot = '<tfoot><tr><td colspan="'. $colspan. '">Total: '. $num_items. " ". $this->table_to_show. '</td></tr></tbody>';

        return $table_foot;
    }

    public function get_login_error(){
    	$this->set_login_error();
    	
    	return $this->login_error;
    }

    private function set_login_error(){
        $message =	'<p>Usuario o contrase&nacute;a no validos.</p>';
    	$retry_button = '<input type="submit" value="Volver a intentar">';
        $error_form = '<div class="login_wrapper"><form id="error_form" action="index.php" method="post">'. $message. $retry_button. '</form></div>';

        $html = $this->generate_html($error_form);

    	$this->login_error = $html;
    }

    public function get_edit_item($item){
        $this->set_edit_item($item);

        return $this->edit_item;
    }

    private function set_edit_item($item){
        $item = json_decode($item, true);

        $primary_key = $item['meta_data']['primary_key'];
        $table = $item['meta_data']['table'];

        $edit_table = $this->generate_edit_table($item['data']);
        $save_button = '<input type="button" onclick="save_edition();" value="Guardar">';
        $cancel_button = '<input type="button" onclick="cancel_edition();" value="Cancelar">';
        $edit_item = '<div class="edit_item_wrapper">'. $edit_table. $save_button. $cancel_button. '</div>';

        $this->edit_item = $edit_item;
    }

    private function generate_edit_table($data){
        $table_head = $this->generate_edit_table_head();
        $table_body = $this->generate_edit_table_body($data);
        //$table_foot = $this->generate_edit_table_foot($data);

        $edit_table = '<table>'. $table_head. $table_body. '</table>';

        return $edit_table;
    }

    private function generate_edit_table_head(){
        $ths[] = '<th>Campo</th>';
        $ths[] = '<th>Valor</th>';
        $ths[] = '<th>Valor nuevo</th>';
        $tr = '<tr>'. join("", $ths). '</tr>';
        $table_head = '<thead>'. $tr. '</thead>';

        return $table_head;
    }

    private function generate_edit_table_body($data){
        foreach($data as $field => $value){
           $td_field = '<td><p>'. $field. '</p></td>';
           $td_value = '<td><p>'. $value. '</p></td>';
           $td_new_value = '<td><input type="text" class="edit_modifications" field="'. $field. '"></td>'; 
           $trs[] = '<tr>'. $td_field. $td_value. $td_new_value. '</tr>';
        }
        $table_body = '<tbody>'. join("", $trs). '</tbody>';

        return $table_body;
    }

    public function get_delete_item(){
        $this->set_delete_item();

        return $this->delete_item;
    }

    private function set_delete_item(){
        $html = $this->generate_html();

        $this->delete_item = $html;
    }

    private function set_mysql(){
        //No deberia usarse el usuario root y menos sin contrasena.
        $this->mysql = new mysqli('localhost', 'root', '', 'ministerio_produccion');
    }
}