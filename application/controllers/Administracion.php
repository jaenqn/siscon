<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Administracion extends CI_Controller {

    function __construct() {
        parent::__construct();
    }

    public function index()
    {

        redirect('Dashboard');
    }
    public function editar_solicitud($id_proceso){
        if($this->session->userdata('Login') == true && ent_usuarios::permitir_area($this->session->userdata('usuario_area'), 'catalogacion') ){


            $data['titulo_header'] = '';
            $this->load->model('Usuarios_mod', 'usuarios');
            $this->load->model('Procesos_mod', 'proceso');

            $objP = $this->proceso->getsimpledata($id_proceso);
            if(isset($_POST['btn_asginar']) && $_POST['btn_asginar'] == 'submit'){

                if(isset($_SESSION['success_edit_proceso']) && $_SESSION['success_edit_proceso'] == true){
                    unset($_SESSION['success_edit_proceso']);
                    redirect('administracion/registro-solicitudes');

                }
                $p = $this->input->post();
                if(md5($objP->pro_id) == $_POST['txt_id_proceso']){
                    $this->load->model('Solped_mod', 'solped');
                        $reg_val_sol['sol_numero'] = $p['txt_req_nrosolped'];
                        $existe_solped = false;
                        if(trim($reg_val_sol['sol_numero']) != $objP->sol_numero)
                            $existe_solped = $this->proceso->existe_solped($reg_val_sol['sol_numero']);

                        if(!$existe_solped)
                        {
                            // print_f($objP);
                            // print_f($_POST);exit;
                            $val_proceso['pro_area'] = $p['txt_area'];
                            $val_proceso['dep_idAreaSolicita'] = $p['txt_id_area'];
                            $val_proceso['pro_encargado'] = $p['txt_sol_encargado'];
                            $val_proceso['emp_idEncargadoSolicita'] = $p['txt_id_encargado'];
                            $val_proceso['pro_presupuesto_tipo'] = $p['sel_presup_tipo'];
                            $val_proceso['pro_moneda'] = $p['sel_presup_moneda'];
                            $val_proceso['pro_reservado'] = +$p['rad_presup_reservado'];
                            if(isset($p['txt_presup_mer']))
                                $val_proceso['pro_mer'] = +$p['txt_presup_mer'] > 0 ? +$p['txt_presup_mer'] : 0;
                            if(trim($p['txt_pac']) == '')
                                $val_proceso['pro_pac'] = 'NO';
                            else $val_proceso['pro_pac'] =  $p['txt_pac'];

                            $val_proceso['pro_modalidad'] = $p['sel_modalidad'];
                            $val_proceso['pro_fechaEntrada'] = format_date($p['txt_fecha_area_entrada']);
                            $val_proceso['pro_fechaSalida'] = format_date($p['txt_fecha_area_salida']);
                            $val_proceso['pro_supervisor'] = $p['sel_supervisor_catcon'];

                            $val_proceso['pro_id'] = $objP->pro_id;
                            $this->proceso->update($val_proceso);

                            // $val_solped['sol_tipo'] = +$p['rad_req_tiposolped'];
                            $val_solped['sol_id'] = $objP->sol_id_solped;
                            $val_solped['sol_descripcion'] = mb_strtoupper($p['txt_req_solped_descripcion']);
                            $this->proceso->update_solped($val_solped);
                            $_SESSION['success_edit_proceso'] = true;

                            $data['callout_type'] = 'success';
                            $data['callout_title'] = 'Actualizado';
                            $data['callout_content'] = 'Cambios realizados con éxito';
                            $data['content_view'] = 'recursos/callouts';
                        }else{
                            $data['callout_type'] = 'danger';
                            $data['callout_title'] = 'Error de SOLPED';
                            $data['callout_content'] = 'La SOLPED ya se encuentra registrada';
                            $data['content_view'] = 'recursos/callouts';
                        }

                }else{

                    $data['callout_type'] = 'danger';
                    $data['callout_title'] = 'Error PROCESO';
                    $data['callout_content'] = 'Los identificadores son diferentes';
                    $data['content_view'] = 'recursos/callouts';
                }
            }else{
                unset($_SESSION['success_edit_proceso']);

                if($objP){
                    if($objP->pro_estado == 1){
                        $data['editar'] = true;
                        $part_exp = explode('-', $objP->pro_expediente);
                        unset($part_exp[0]);
                        $objP->pro_expediente = join('-', $part_exp);
                        $data['obj_proceso'] = $objP;
                        $data['content_view'] = 'administracion/adm_regsolicitudes';
                        $data['titulo_header'] = 'Registro y asignación las solicitudes de pedidos';
                        $data['script'] = array(
                            'assets/plugins/typeahead/typeahead.bundle.js',
                            'assets/views/catalogacion/cat_registro.js'
                            );
                        $data['css'] = array(
                            'assets/views/catalogacion/cat_registro.css'
                            );
                        $data['ref_form'] = $_SESSION['ref_form'] = uniqid(mt_rand());
                    }else{
                        $data['callout_type'] = 'danger';
                        $data['callout_title'] = 'Error PROCESO';
                        $data['callout_content'] = 'El proceso debe estar en estado ELABORADO';
                        $data['content_view'] = 'recursos/callouts';
                    }
                }else{
                    $data['callout_type'] = 'danger';
                    $data['callout_title'] = 'Error PROCESO';
                    $data['callout_content'] = 'El proceso no existe';
                    $data['content_view'] = 'recursos/callouts';
                }
            }

            $this->load->view('master/master_page', $data);

        }else{
            redirect('login/cerrar_sesion');
        }
    }
    public function get_correlativo($tipo){
        $res['success'] = false;
        $this->load->model('Correlativo_mod', 'correlativo');
        $tipos = '';
        switch (+$tipo) {
            case 1: $tipos = 'servicios'; break;
            case 2: $tipos = 'bienes'; break;
        }
        $res['data']  = $this->correlativo->get_correlativo($tipos);
        if($res['data'])
            $res['success'] = true;
        echo_json($res);
    }
    public function formato($id_proceso){
        //mostrar formato de proceso imprmible
        $this->load->library('php_word');
        require_once APPPATH.'libraries/themoment.php';
        \Moment\Moment::setLocale('es_ES');


        $this->load->model('Procesos_mod', 'pro');
        $this->load->model('Dependencia_mod', 'dep');
        // $this->load->model('Usuarios_mod', 'usu');
        $this->load->model('', 'dep');
        $objPro = $this->pro->getsimpledata($id_proceso);

        // print_f($objPro);exit;
        if($objPro){
            $objAreaSol = $this->dep->getsimpledata($objPro->dep_idAreaSolicita);
            $objAreaContra = $this->dep->getsimpledata(10);
            // $objEnca = $this->usu->getsimpledata($objPro->emp_idEncargadoSolicita);
            $phpWord = new \PhpOffice\PhpWord\PhpWord();
            $testWord = $phpWord->loadTemplate(DOCSPATH.'templates/cdx_tpl.docx');
            $vars  = $testWord->getVariables();

            $titulo = '';
            $supervisor = '';
            $objeto = '';
            switch($objPro->sol_tipo){
                case 1:
                case 3:
                    $titulo = 'CONTRATACIÓN SERVICIOS';
                    $supervisor = 'Supervisor Contrataciones de Servicios y Obras';
                    $objeto = 'Servicios';
                    break;
                case 2: $titulo = 'COMPRA DE BIENES';
                    $supervisor = 'Supervisor Compra de Bienes';
                    $objeto = 'Bienes';
                    break;
            }
            $setValA['modo'] = $titulo;
            $setValA['expediente'] = $objPro->pro_expediente;
            $m = new \Moment\Moment($objPro->pro_fechaEntrada);
            $customformat = new \Moment\CustomFormats\MomentJs();
            $setValA['fecha'] = $m->format('DD [de] MMMM [de] YYYY',$customformat);
            $setValA['unidad_genera'] = mb_strtoupper($objAreaContra->descripcion);
            $setValA['area_destino'] = $supervisor;
            $setValA['nro_solped'] = $objPro->sol_numero;
            $setValA['objeto_tipo'] = $objeto;
            $setValA['descripcion'] = $objPro->sol_descripcion;
            $setValA['modalidad'] = $objPro->mod_descripcion;
            $setValA['solicitante'] = mb_strtoupper($objAreaSol->descripcion);
            $setValA['monto_estimado'] = number_format($objPro->pro_mer, 2 , '.' , ',');
            if($objPro->pro_reservado == 1) $setValA['monto_estimado'] .= ' RESERVADO';
            $setValA['pac'] = $objPro->pro_pac;

            $testWord->setValue(array_keys($setValA), array_values($setValA));
            $temp_name = uniqid($id_proceso);
            $temp_file = DOCSPATH.'temp/'.$temp_name.'.docx';
            $testWord->saveAs($temp_file);
             // header('Content-Type: application/docx');
            header('Content-Disposition: attachment;filename="CDX '.$objPro->pro_nro_proceso.' [TITULO].docx"');
            header('Cache-Control: max-age=0');
            ob_end_clean();
            readfile($temp_file);
            unlink($temp_file);
        }else echo_json(array('msg' => 'Error al generar fichero'));
    }
    public function registro_solicitudes(){
        // uniqid(mt_rand())
        //punto 1
        if($this->session->userdata('Login') == true && ent_usuarios::permitir_area($this->session->userdata('usuario_area'), 'catalogacion') )
        {
            $data['content_view'] = 'administracion/adm_regsolicitudes';
            $data['titulo_header'] = 'Registro y asignación las solicitudes de pedidos';
            $this->load->model('Usuarios_mod', 'usuarios');

            // print_r($_POST);
            if(isset($_POST['btn_asginar']) && $_POST['btn_asginar'] == 'submit'){

                if(isset($_SESSION['success_reg_proceso']) && $_SESSION['success_reg_proceso'] == true){
                    unset($_SESSION['success_reg_proceso']);
                    redirect('administracion/registro-solicitudes');

                }
                // print_f($_POST);exit;
                $this->load->model('Solped_mod', 'solped');
                $this->load->model('Procesos_mod', 'proceso');
                $this->load->model('Correlativo_mod', 'correlativo');
                $p = $this->input->post();
                $tipo_correlativo = '';
                $objCor = null;
                switch (+$p['rad_req_tiposolped']) {
                    case 1:
                    case 3:
                        $tipo_correlativo = 'servicios';
                        break;
                    case 2: $tipo_correlativo = 'bienes'; break;
                }
                $objCor = $this->correlativo->get_correlativo($tipo_correlativo);

                $pre_correlativo = str_pad($p['txt_expediente'],4,'0',STR_PAD_LEFT).'-'.$p['txt_expediente_correlativo'];

                if($objCor && $pre_correlativo == $objCor->correlativo){

                    $nro_expediente = $objCor->correlativo;
                    if(!$this->proceso->existe_expediente($nro_expediente)){
                        //registrar si no existe el expediente figuado, que se obtiene del form del cliente
                        // print_f($_POST);exit;
                        //SOLPED
                        $reg_val_sol['sol_numero'] = $p['txt_req_nrosolped'];
                        if(!$this->proceso->existe_solped($reg_val_sol['sol_numero']))
                        {
                            $reg_val_sol['sol_tipo'] = +$p['rad_req_tiposolped'];
                            $reg_val_sol['sol_descripcion'] = mb_strtoupper($p['txt_req_solped_descripcion']);

                            // $res_solped = $this->solped->insertar($reg_val_sol);
                            // if(true){
                            // if($res_solped['success']){
                            // $reg_proceso['sol_id_solped'] = $res_solped['ref_id'];
                            $reg_proceso['pro_area'] = $p['txt_area'];
                            $reg_proceso['dep_idAreaSolicita'] = $p['txt_id_area'];
                            // $reg_proceso['pro_expediente'] = $nro_expediente;


                            $reg_proceso['pro_encargado'] = $p['txt_sol_encargado'];
                            $reg_proceso['emp_idEncargadoSolicita'] = $p['txt_id_encargado'];
                            $reg_proceso['pro_presupuesto_tipo'] = $p['sel_presup_tipo'];
                            $reg_proceso['pro_moneda'] = $p['sel_presup_moneda'];
                            $reg_proceso['pro_reservado'] = +$p['rad_presup_reservado'];
                            if(isset($p['txt_presup_mer']))
                                $reg_proceso['pro_mer'] = +$p['txt_presup_mer'] > 0 ? +$p['txt_presup_mer'] : 0;
                            if(trim($p['txt_pac']) == '')
                                $reg_proceso['pro_pac'] = 'NO';
                            else $reg_proceso['pro_pac'] =  $p['txt_pac'];
                            $reg_proceso['pro_modalidad'] = $p['sel_modalidad'];
                            $reg_proceso['pro_fechaEntrada'] = format_date($p['txt_fecha_area_entrada']);
                            $reg_proceso['pro_fechaSalida'] = format_date($p['txt_fecha_area_salida']);
                            $reg_proceso['pro_supervisor'] = $p['sel_supervisor_catcon'];
                                // $reg_val['pro_'] = $p['btn_asginar'];

                                // if($this->proceso->insertar($reg_val)){
                                //     $this->correlativo->get_correlativo($tipo_correlativo, true);
                                // }
                            // }
                            $res_insert = $this->proceso->insertar_solped($reg_val_sol, $reg_proceso, $tipo_correlativo);
                            $_SESSION['success_reg_proceso'] = true;
                            if($res_insert['success']){

                                $data['callout_title'] = 'Registrado';
                                $data['callout_content'] = 'El proceso ha sido registrado con éxito «<a href="'.base_url('administracion/formato/'.$res_insert['id_proceso']).'" class="">DESCARGAR FORMATO</a>»';
                                $data['content_view'] = 'recursos/callouts';
                            }else{
                                $data['callout_type'] = 'danger';
                                $data['callout_title'] = 'Error en Registro';
                                $data['callout_content'] = 'Se producjo un error mientras se registraban los datos';
                                $data['content_view'] = 'recursos/callouts';
                                $data['script'] = array(
                                    // array(redirect_seTimeOut('administracion/registro-solicitudes'))
                                );
                            }
                        }else{
                            $data['callout_type'] = 'danger';
                            $data['callout_title'] = 'Error de SOLPED';
                            $data['callout_content'] = 'La SOLPED ya se encuentra registrada';
                            $data['content_view'] = 'recursos/callouts';
                        }
                    }else{
                        $data['callout_type'] = 'danger';
                        $data['callout_title'] = 'Error de expediente';
                        $data['callout_content'] = 'El expediente ya se encuentra registrado';
                        $data['content_view'] = 'recursos/callouts';
                    }
                }
                // else{
                //         $data['callout_type'] = 'danger';
                //         $data['callout_title'] = 'Error de expediente';
                //         $data['callout_content'] = 'El expediente ya se encuentra registrado';
                //         $data['content_view'] = 'recursos/callouts';
                //     }
            }else{
                $data['editar'] = false;
                $data['script'] = array(
                    'assets/plugins/typeahead/typeahead.bundle.js',
                    'assets/views/catalogacion/cat_registro.js'
                    );
                $data['css'] = array(
                    'assets/views/catalogacion/cat_registro.css'
                    );
                $data['ref_form'] = $_SESSION['ref_form'] = uniqid(mt_rand());
            }




            $this->load->view('master/master_page', $data);
        }
        else
        {
            redirect('Login/cerrar_sesion');
        }
    }

    public function lista_solicitudes(){

        if($this->session->userdata('Login') == true && ent_usuarios::permitir_area(+$this->session->userdata('usuario_area'), 'servicios') )
        {
            $data =  array
            (
                'content_view' => 'administracion/adm_lstsolicitudes.php',
                'titulo_header' => 'Lista de Solicitudes ',
                'sub_titulo_header' => 'derivados al área de contratación de serviciosy obras',
                'script' => array(
                    'assets/plugins/datatables/jquery.dataTables.min.js',
                    'assets/plugins/datatables/dataTables.bootstrap.min.js',
                    'assets/views/administracion/adm_lista_solicitudes.js'
                    ),
                'css' => array(
                    'assets/plugins/datatables/dataTables.bootstrap.css'
                    )
            );
            $this->load->view('master/master_page', $data);
        }
        else
        {
            redirect('Login/cerrar_sesion');
        }
    }
    public function detalles(){

        if($this->session->userdata('Login') == true && $this->session->userdata('logistica') == 1 )
        {
            $data =  array
            (
                'content_view' => 'administracion/adm_regdetalles.php',
                'titulo_header' => 'Registro detalles de la SOLPED',
                'script' => array(),
                'css' => array()
            );
            $this->load->view('master/master_page', $data);
        }
        else
        {
            redirect('Login/cerrar_sesion');
        }
    }
    public function registro_detalles_solped($id_proceso){

        if($this->session->userdata('Login') == true && ent_usuarios::permitir_area($this->session->userdata('usuario_area'), 'servicios') )
        {

            if(isset($_SESSION['success_reg_detalles_soled_serobr']) && $_SESSION['success_reg_detalles_soled_serobr'] == true){
                unset($_SESSION['success_reg_detalles_soled_serobr']);
                redirect('administracion/lista-solicitudes');

            }
            $this->load->model('Procesos_mod', 'proceso');
            $objP = $this->proceso->getalldata($id_proceso);

            $data =  array
            (
                'content_view' => 'administracion/adm_regdetalles.php',
                'titulo_header' => 'Registro detalles de la SOLPED',
                'script' => array(),
                'css' => array()
            );
            if(isset($_POST['btn_asginar']) && $_POST['btn_asginar'] == 'submit'){
                $p = $this->input->post();
                // print_f($p);
                if(isset($_POST[md5('txt_id_proceso')]) && +$_POST[md5('txt_id_proceso')] == +$id_proceso){

                    $_SESSION['success_reg_detalles_soled_serobr'] = true;
                    $data['callout_title'] = 'Registrado';
                    $data['callout_content'] = 'No tengo mensaje';
                    $data['content_view'] = 'recursos/callouts';

                    $reg_val['pro_id'] = $p[md5('txt_id_proceso')];
                    $reg_val['pro_nro_licitacion'] = $p['txt_licitacion'];
                    $reg_val['pro_numero'] = $p['txt_nro_proceso'];
                    $reg_val['pro_igv_convocatoria'] = $p['rad_igv'];
                    $reg_val['pro_estado'] = 2;
                    $reg_val['pro_pep'] = $p['txt_detalles_pep'];
                    $reg_val['pro_fecha_area_ingreso_serobr'] = $p['txt_fecha_ingreso_area_ser'];
                    $reg_val['pro_supervisor_proceso'] = $p['sel_encargado_proceso'];

                    $this->proceso->update($reg_val);


                }else{
                    $data['callout_type'] = 'danger';
                    $data['callout_title'] = 'Error';
                    $data['callout_content'] = 'Los identificadores no son idénticos';
                    $data['content_view'] = 'recursos/callouts';
                }
            }else{

                // print_f($objP);
                $data['ref_form'] = $_SESSION['ref_form'] = uniqid(mt_rand());
                if($objP){
                    //determinar el modo de proceso

                    if(!(+$objP->sol_tipo == 1 || +$objP->sol_tipo == 3)){

                        $data['callout_type'] = 'danger';
                        $data['callout_title'] = 'No autorizado';
                        $data['callout_content'] = 'El proceso no puede ser registrado en esta área';
                        $data['content_view'] = 'recursos/callouts';
                    }else{
                        $this->load->model('Usuarios_mod', 'usuarios');

                        $data['obj_proceso'] = $objP;
                        //1CMA, 2CME, 3DIR, 4NSR, 5NSRDIR
                        switch (+$objP->pro_modalidad) {
                            case 1:
                                $data['titulo_header'] .= ' | CMA';
                                $objP->pro_nro_proceso = 'CMA-'.sprintf("%'.05d\n", $objP->pro_id).'-2017 OPS/PETROPERU';
                                $data['content_view'] = 'administracion/adm_regdetalles.php';
                                break;
                            case 2:
                                $data['content_view'] = 'asdas';
                                break;
                            case 3:
                                $data['content_view'] = 'asdas';
                                break;
                            case 4:
                            case 5:
                                $data['content_view'] = 'asdas';
                                break;
                        }
                    }

                }else{
                    $data['callout_type'] = 'danger';
                    $data['callout_title'] = 'Proceso no encontrado';
                    $data['callout_content'] = 'La referencia al proceso no se encuentra registrado en el sistema';
                    $data['content_view'] = 'recursos/callouts';
                }
            }



            $this->load->view('master/master_page', $data);
        }
        else
        {
            redirect('Login/cerrar_sesion');
        }
    }

    public function list_datatable($mode = false){
        $this->load->model('Datatables_mod');
        $order = false;
        $selects = array(
            'sp.*'
                 // "urm.desDepend as uni_rem_nombre",
                 // "urc.desDepend as uni_rec_nombre",
                // "dd.desDepend as dep_nombre",
                // "dd.idDepend as dep_id_departamento"
            // "CONCAT(ins_attr_direccion,' ',ins_direccion) AS dir_total",
            // "CONCAT(ins_razon_social,' - ',ins_rz_siglas) AS rz_total"
            );
        if(isset($_POST['order'])){

            switch ($_POST['columns'][$_POST['order']['0']['column']]['data']) {
                // case 'get_estado':
                //     $order = array('uni_estado' => $_POST['order']['0']['dir']);
                //     break;

            }

        }

        $filter = array();
        if($mode && is_numeric($mode)){
            $filter[] = array(
                                'column' => 'pro_modalidad',
                                'filter' => +$mode,
                                'default' => true
                                );
        }
        $likes = array();
        if($this->input->post('filters')){
            $p = $this->input->post('filters');
            $i = 0;
            foreach ($p as $key => $value) {
                switch ($value['column']) {
                    // case 'usu_nombre':
                    //     $likes[] = $p[$i];
                    //     break;
                    // case 'dep_id_departamento':
                    //     $filter[] = array('column' => 'dd.idDepend', 'filter' => $value['filter']);
                    //     break;
                    // case 'uni_id_unidad':
                    //     $filter[] = $p[$i];
                    //     break;
                    // case 'usu_estado':
                    // case 'usu_tipo_usuario':
                    //     $filter[] = $p[$i];
                    //     break;
                }
                $i++;
            }
        }
        $_POST['f'] = $filter;
        $_POST['l'] = $likes;
        $joins = array(
                array('tbl_solicitudes_pedido sp','sp.sol_id = tbl_proceso.sol_id_solped')
                // array('dependencia dd','dd.idDepend = d.reportaDpend')
            );
        $datas = $this->Datatables_mod->get_datatables('tbl_proceso','object',$likes, $filter,$selects, $order,$joins,$_POST['length'],$_POST['start']);

        // foreach ($datas['list'] as $key => $value) {
        //     $value->uni_activa = AppSession::get('id_unidad');
        //     $value->tipo_usuario = Hash::getHash('sha1', AppSession::get('tipo_usuario'), HASH_KEY);
        // }
        // $list = get_object_vars($list);
        $data = array();
        $no = $_POST['start'];


        $output = array(
                        "draw" => $_POST['draw'],
                        "recordsTotal" => $datas['count_all'],
                        "recordsFiltered" => $datas['count_filtered'],
                        "data" => $datas['list'],
                        "post" => $_POST
                );
        //output to json format
        echo json_encode($output);
    }

}