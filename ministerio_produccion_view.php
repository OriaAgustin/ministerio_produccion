<?php

require_once($_SERVER['DOCUMENT_ROOT']. '/ministerio_produccion_database_manager.php');
require_once($_SERVER['DOCUMENT_ROOT']. '/libraries/phpToPDF.php');
require_once($_SERVER['DOCUMENT_ROOT']. '/kint.phar');

class ministerio_produccion_view{
    private $database_manager;
    private $login;
    private $main_view;
    private $login_error;
    private $edit_item;
    private $add_item;
    private $pdf_html;
    private $generate_report;
    private $show_result;

    public function __construct(){
    	$this->set_database_manager();
    }

    private function set_database_manager(){
        $this->database_manager = new ministerio_produccion_database_manager();
    }

    private function generate_head(){
        $links = '<script src="ministerio_produccion.js"></script>';
        $links .= '<script src="/libraries/minAjax.js"></script>';
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
        $login_select = '<select id="login_type_select" onchange="change_profile_type(event);">
                            <option value="usuario_interno">Usuario interno</option>    
                            <option value="empresa">Empresa</option>
                        </select>';
        $login_inputs = '<input id="login_id_input" class="login_input" type="text" placeholder="Email" value="agustin.oria@gmail.com">
                        <input id="login_password_input" class="login_input" type="password" placeholder="Contraseña" value="a">';
        $login_button = '<input type="button" value="Ingresar" onclick="login();">';

        $login = '<div id="login_wrapper" class="wrapper">'. $login_select. $login_inputs. $login_button. '</div>';

        $html = $this->generate_html($login);
        $this->login = $html;
    }

    public function get_main_view(){
    	$this->set_main_view();

    	return $this->main_view;
    }

    private function set_main_view(){
        $data = $this->database_manager->define_main_view_data($_SESSION['login'], $_SESSION['meta_data']);
    	$table = $this->generate_table($data);
        $add_button = '<input type="button" value="Agregar" onclick="add_item();">';
        $special_action_button = $this->generate_special_action_button();
        $logout_button = '<input type="button" value="Salir" onclick="logout();">';
        $main_view = $this->generate_body('<div id="main_view_wrapper" class="wrapper">'. $table. $add_button. $special_action_button. $logout_button. '</div>');
    	
        $this->main_view = $main_view;
    }

    private function generate_table($data){
        $table_head = $this->generate_table_head($_SESSION['meta_data']);
        $table_body = $this->generate_table_body($data, $_SESSION['meta_data']);
        $table_foot = $this->generate_table_foot($data, $_SESSION['meta_data']);

        $table = '<table class="table">'. $table_head. $table_body. $table_foot. '</table>';

        return $table;
    }

    private function generate_table_head($meta_data){
        $colspan = count($meta_data['fields_to_show']) + 2;
        $trs[] = '<tr><th colspan='. $colspan. '>'. strtoupper($meta_data['table_to_show']). '</th></tr>';
        foreach($meta_data['fields_to_show'] as $field_to_show){
            $ths[] = '<th>'. ucfirst($field_to_show). '</th>';
        }
        $ths[] = '<th>Editar</th>';
        $ths[] = '<th>Borrar</th>';
        $trs[] = '<tr>'. join("", $ths). '</tr>';
        $table_head = '<thead>'. join("", $trs). '</thead>';

        return $table_head;
    }

    private function generate_table_body($data, $meta_data){
        if($data){
            $index = 0;
            while($register = $data->fetch_assoc()){
                $tds = array();
                foreach($register as $field => $value){
                    if($field == $meta_data['table_to_show_pk']){
                        $tds[] = '<td id="pk_item_'. $index. '" field="'. $field. '">'. $value. '</td>';
                    }else{
                        $tds[] = '<td>'. $value. '</td>';
                    }
                }
                $tds[] = '<td><input type="button" value="Editar" onclick=\'edit_item("'. $index. '");\'></td>';
                $tds[] = '<td><input type="button" value="Borrar" onclick=\'delete_item("'. $index. '");\'></td>';
                $trs[] = '<tr>'. join("", $tds). '</tr>';
                $index++;
            }
        }
        $table_body = '<tbody>'. join("", $trs). '</tbody>';

        return $table_body;
    }

    private function generate_table_foot($data, $meta_data){
        $num_items = $data ? $data->num_rows : 0;
        $colspan = count($meta_data['fields_to_show']) + 2;
        $table_foot = '<tfoot><tr><td colspan="'. $colspan. '">Total: '. $num_items. " ". $meta_data['table_to_show']. '</td></tr></tbody>';

        return $table_foot;
    }

    private function generate_special_action_button(){
        if($_SESSION['login']['login_type'] == "usuario_interno"){
            $special_action_button = '<input type="button" value="Generar reporte" onclick="generate_report();">';
        }else{
            $special_action_button = '<input type="button" value="Carga masiva" onclick="masive_load();">';
        }

        return $special_action_button;
    }

    public function get_login_error(){
    	$this->set_login_error();
    	
    	return $this->login_error;
    }

    private function set_login_error(){
        $message ='<p>Usuario o contrase&ntilde;a no validos.</p>';
    	$main_view_button = '<input type="button" value="Volver" onclick="go_to_main_view();">';
        $login_error = '<div id="login_wrapper" class="wrapper">'. $message. $main_view_button. '</div>';

    	$this->login_error = $login_error;
    }

    public function get_edit_item($item){
        $this->set_edit_item($item);

        return $this->edit_item;
    }

    private function set_edit_item($pk_value){
        $data = $this->database_manager->define_fields_to_edit($pk_value, $_SESSION['meta_data']);

        $edit_table = $this->generate_edit_table($data);
        $save_button = '<input type="button" onclick="save_edited_item();" value="Guardar">';
        $cancel_button = '<input type="button" onclick="cancel();" value="Cancelar">';
        $edit_item = '<div id="edit_item_wrapper" class="wrapper">'. $edit_table. $save_button. $cancel_button. '</div>';

        $this->edit_item = $edit_item;
    }

    private function generate_edit_table($data){
        $table_head = $this->generate_edit_table_head();
        $table_body = $this->generate_edit_table_body($data);

        $edit_table = '<table class="table">'. $table_head. $table_body. '</table>';

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
            $td_field = '<td><p>'. ucfirst($field). '</p></td>';
            if($field == $_SESSION['meta_data']['table_to_show_pk']){
                $td_value = '<td id="pk_value"><p>'. $value. '</p></td>';
            }else{
                $td_value = '<td><p>'. $value. '</p></td>';
            }
            $input_type = $field == 'password' ? 'password' : 'text';
            $td_new_value = '<td class="td_containning_input"><input type="'. $input_type. '" class="new_values" field="'. $field. '"></td>'; 
            $trs[] = '<tr>'. $td_field. $td_value. $td_new_value. '</tr>';
        }
        $table_body = '<tbody>'. join("", $trs). '</tbody>';

        return $table_body;
    }

    public function get_add_item($meta_data){
        $this->set_add_item($meta_data);

        return $this->add_item;
    }

    private function set_add_item($meta_data){
        //$fields = $this->database_manager->get_fields_from_table($meta_data['table_to_show']);
        $fields = $_SESSION['meta_data']['fields_to_edit'];
        $add_table = $this->generate_add_table($fields);
        $save_button = '<input type="button" onclick="save_added_item();" value="Guardar">';
        $cancel_button = '<input type="button" onclick="cancel();" value="Cancelar">';
        $add_item = '<div id="add_item_wrapper" class="wrapper">'. $add_table. $save_button. $cancel_button. '</div>';

        $this->add_item = $add_item;
    }

    private function generate_add_table($fields){
        $table_head = $this->generate_add_table_head();
        $table_body = $this->generate_add_table_body($fields);

        $add_table = '<table class="table">'. $table_head. $table_body. '</table>';

        return $add_table;
    }

    private function generate_add_table_head(){
        $ths[] = '<th>Campo</th>';
        $ths[] = '<th>Valor</th>';
        $tr = '<tr>'. join("", $ths). '</tr>';
        $table_head = '<thead>'. $tr. '</thead>';

        return $table_head;
    }

    private function generate_add_table_body($fields){
        foreach($fields as $field){
           $td_field = '<td><p>'. ucfirst($field). '</p></td>';
           $input_type = $field == 'password' ? 'password' : 'text';
           $td_value = '<td class="td_containning_input"><input type="'. $input_type. '" class="added_values" field="'. $field. '"></td>'; 
           $trs[] = '<tr>'. $td_field. $td_value. '</tr>';
        }
        if($_SESSION['login']['login_type'] == 'empresa'){
            $td_field = '<td><p>Cuit</p></td>';
            $td_value = '<td><input type="text" class="added_values" field="cuit" value="'. $_SESSION['login']['login_id']. '" readonly></td>'; 
            $trs[] = '<tr>'. $td_field. $td_value. '</tr>';
        }
        $table_body = '<tbody>'. join("", $trs). '</tbody>';

        return $table_body;
    }

    public function generate_report(){
        $data = $this->database_manager->define_pdf_html_data();
        
        $empresas = $data['empresas'];
        $empleados = $data['empleados'];
        $count_empleados = 0;
        foreach($empresas as $cuit => $nombre){
            $empleados_empresa = $empleados[$cuit];
            $lis_empleados = array();
            foreach($empleados_empresa as $empleado){
                $lis_empleados[] = '<li>'. $empleado. '</li>';
            }
            $lis_empresas[] = '<li>'. $nombre. " | ". count($lis_empleados). " empleados:". '<ul>'. join("", $lis_empleados). '</ul></li>';
            $count_empleados += count($lis_empleados);
        }
        $lis_empresas[] = '<li>Total de empleados cargados en el sistema: '. $count_empleados. '</li>';
        $pdf_html = '<ul>'. join("", $lis_empresas). '</ul>';

        $pdf_options = array(   "source_type" => 'html',
                                "source" => $pdf_html,
                                "action" => 'save',
                                "save_directory" => 'files_download',
                                "file_name" => 'reporte_empresas_'. date('Ymd'). '.pdf');

        phptopdf($pdf_options);
    }

    public function get_generate_report(){
        $this->set_generate_report();

        return $this->generate_report;
    }

    private function set_generate_report(){
        $message = '<p>Reporte generado en archivo PDF. Revisar la carpeta del proyecto.</p>';
        $main_view_button = '<input type="button" value="Volver" onclick="go_to_main_view();">';
        $generate_report = '<div id="generate_report_wrapper" class="wrapper">'. $message. $main_view_button. '</div>';

        $this->generate_report = $generate_report;
    }

    public function get_show_result($result, $params){
        $this->set_show_result($result, $params);

        return $this->show_result;
    }

    private function set_show_result($result, $ajax){
        $meta_data = json_decode($params['meta_data'], true);
        $messages = $this->define_messages();
        
        $result = $result ? 'success' : 'error';
        $message = '<p>'. $messages[$ajax][$result]. '</p>';

        $main_view_button = '<input type="button" value="Volver" onclick="go_to_main_view();">';
        $show_result = '<div id="save_edition_result_wrapper" class="wrapper">'. $message. $main_view_button. '</div>';

        $show_result = $this->generate_body($show_result);

        $this->show_result = $show_result;
    }

    //[!] Esto deberia sacarse de un archivo de configuracion.
    private function define_messages(){
        $messages['save_edited_item']['success'] = 'Las modificaciones se realizaron exitosamente.';
        $messages['save_edited_item']['error'] = 'Error. No se pudieron realizar las modificaciones.';
        $messages['delete_item']['success'] = 'Item borrado exitosamente.';
        $messages['delete_item']['error'] = 'Error. No se pudo borrar el item.';
        $messages['save_added_item']['success'] = 'Item guardado exitosamente.';
        $messages['save_added_item']['error'] = 'Error. No se pudo guardar el item.';

        return $messages;
    }
}