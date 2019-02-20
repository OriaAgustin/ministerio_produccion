<?php
/*Separar en clases hijas y padre?*/
class ministerio_produccion_usuario_interno_view extends ministerio_produccion_view{
    protected $table_to_show;
    protected $fields_to_show;

    public function __construct(){
    	parent::__construct();
        $this->set_table_to_show();
        $this->set_fields_to_show();
    }

    private function set_table_to_show(){
        $this->table_to_show = "empresas";
    }

    private function set_fields_to_show(){
        //[!] Deberia sacarse de un archivo de configuracion.
        $this->fields_to_show = array('cuit', 'nombre');
    }
}