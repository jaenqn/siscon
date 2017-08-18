<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Proceso extends CI_Controller {

    function __construct() {
        parent::__construct();
    }

    public function finalizado($id_proceso){
        $this->load->model('Procesos_mod', 'proceso');
        $objP = $this->proceso->getalldata($id_proceso);
        print_r($objP);
    }

    public function datajavascript_proceso($id_proceso){
        $this->load->model('Procesos_mod', 'proceso');
        $obj_proceso = $this->proceso->getalldata($id_proceso);

        if($obj_proceso){
            header( "Content-Type: application / javascript");
            include_once APPPATH.'models/javascript/ent_proceso.php';
        }


    }
    public function index(){}
    public function get_postores($id_proceso){

        $res['success'] = false;
        $res['data'] = array();
        $this->load->model('Procesos_mod','proceso');
        $res['data'] = $this->proceso->get_postores($id_proceso);
        if(count($res['data']) > 0)
        echo_json($res['data']);

    }

    private function data_regdetsolpe_bie_cma($p){
        // $p = $this->input->post();
        // $v[''] = $p['txt_nro_proceso'];
        // $v[''] = $p['txt_licitacion'];
        // $v[''] = $p['txt_aprobado_por'];
        // $v[''] = $p['rad_reservado'];
        // $v[''] = $p['txt_mer'];
        // $v[''] = $p['rad_igv'];
        // $v[''] = $p['txt_pac'];
        // $v[''] = $p['txt_pep'];
        // $v[''] = $p['sel_sistema_contratacion'];
        // $v[''] = $p['txt_fecha_ingreso'];
        // return $v;
    }
    public function registro_detalles_solped($id_proceso){
        if(!$id_proceso) redirect('solicitudes/listar');
        if($this->session->userdata('Login') == true && ent_usuarios::permitir_area($this->session->userdata('usuario_area'), array('servicios', 'bienes')) )
        {

            $this->load->model('Procesos_mod', 'proceso');


            $data =  array
            (
                'content_view' => '',
                'titulo_header' => 'Registro detalles de la SOLPED',
                'script' => array(),
                'css' => array()
            );
            $objP = $this->proceso->getalldata($id_proceso);
            if(isset($_POST['btn_asginar']) && $_POST['btn_asginar'] == 'submit'){

                if(isset($_SESSION['success_reg_detalles_soled_serobr']) && $_SESSION['success_reg_detalles_soled_serobr'] == true){
                    unset($_SESSION['success_reg_detalles_soled_serobr']);
                    redirect('solicitudes/listar');

                }
                // $_SESSION['success_reg_detalles_soled_serobr'] = true;
                // exit;
                $p = $this->input->post();
                // print_f($p);

                if(isset($_POST[md5('txt_id_proceso')]) && $_POST[md5('txt_id_proceso')] == md5(+$id_proceso)){

                    //verficar que no exista el proceso
                    $nro_proceso = '';
                    if(isset($p['txt_nro_proceso']))
                        $nro_proceso = $p['txt_nro_proceso'];


                    $pase_proceso = false;
                    if($this->proceso->existe_proceso($nro_proceso))
                        $pase_proceso = false;
                    else $pase_proceso = true;

                    if($objP->pro_modalidad == 4 || $objP->pro_modalidad == 5)
                    // if($objP->sol_tipo == 2 && ($objP->pro_modalidad == 4 || $objP->pro_modalidad == 5))
                        $pase_proceso = true;
                        // $pase_proceso == true;
                    if($pase_proceso){
                        // print_f($_POST);




                        $reg_val['pro_id'] = $objP->pro_id;
                        if(isset($p['txt_licitacion']))
                            $reg_val['pro_nro_licitacion'] = $p['txt_licitacion'];
                        if(isset($p['txt_nro_proceso']))
                            $reg_val['pro_numero'] = $p['txt_nro_proceso'];
                        $reg_val['pro_igv'] = $p['rad_igv'];
                        $reg_val['pro_estado'] = 2;
                        $reg_val['pro_pep'] = $p['txt_pep'];


                        // $reg_val['pro_fecha_area_ingreso_serobr'] = $p['txt_fecha_ingreso_area_ser'];
                        // $reg_val['pro_supervisor_proceso'] = $p['sel_encargado_proceso'];

                        if(isset($p[md5('txt_sol_tipo')]) && $p[md5('txt_sol_tipo')] == md5($objP->sol_tipo) && isset($p[md5('txt_modalidad')]) && $p[md5('txt_modalidad')] == md5($objP->pro_modalidad)){

                            switch (+$objP->sol_tipo) {

                                case 1:
                                case 3://SERVICIOS
                                    if(ent_usuarios::permitir_area($_SESSION['usuario_area'], 'servicios')){
                                            //1CMA, 2CME, 3DIR, 4NSR, 5NSRDIR
                                        switch (+$objP->pro_modalidad) {
                                            case 1://COM
                                                $reg_val['pro_fechaIngresoServicios'] = format_date($p['txt_fecha_ingreso_area_ser']);
                                                // $reg_val['pro_mer'] = $p['txt_mer'];
                                                // $reg_val['pro_sistemaContratacion'] = $p['sel_sistema_contratacion'];
                                                $reg_val['pro_supervisor'] = $p['sel_encargado_proceso'];
                                                // print_f($_POST);
                                                $this->proceso->update($reg_val);
                                                break;
                                            case 2://SEL
                                                // exit;
                                                $reg_val['pro_fechaIngresoServicios'] = format_date($p['txt_fecha_ingreso_area_ser']);
                                                // if(isset($_POST['txt_mer']))
                                                //     $reg_val['pro_mer'] = $p['txt_mer'];
                                                // $reg_val['pro_reservado'] = $p['rad_reservado'];
                                                $reg_val['pro_supervisor'] = $p['sel_encargado_proceso'];
                                                $this->proceso->update($reg_val);
                                                break;
                                            case 3:
                                                // print_f($objP);exit;
                                                $reg_val['pro_fechaIngresoServicios'] = format_date($p['txt_fecha_ingreso_area_ser']);
                                                // if(isset($_POST['txt_mer']))
                                                //     $reg_val['pro_mer'] = $p['txt_mer'];
                                                // $reg_val['pro_reservado'] = $p['rad_reservado'];
                                                $reg_val['pro_supervisor'] = $p['sel_encargado_proceso'];
                                                $this->proceso->update($reg_val);
                                                break;
                                            case 4:
                                            case 5:
                                                $reg_val['pro_estado'] = 3;
                                                $reg_val['pro_fechaIngresoServicios'] = format_date($p['txt_fecha_ingreso_area_ser']);
                                                // $reg_val['pro_reservado'] = $p['rad_reservado'];
                                                $reg_val['pro_supervisor'] = $p['sel_encargado_proceso'];
                                                // $reg_val['pro_informeTecnico'] = $p['txt_informe'];
                                                // print_f($_POST);
                                                $this->proceso->update($reg_val);
                                                break;
                                        }
                                        $data['callout_title'] = 'Registrado';
                                        $data['callout_content'] = 'Registro completado';
                                        $data['content_view'] = 'recursos/callouts';
                                        $data['script'] = array(
                                                array(redirect_seTimeOut('solicitudes/listar'))
                                            );
                                    }else{
                                        $data['callout_type'] = 'danger';
                                        $data['callout_title'] = 'No autorizado';
                                        $data['callout_content'] = 'El proceso no puede ser registrado en esta área';
                                        $data['content_view'] = 'recursos/callouts';
                                    }
                                    break;
                                case 2://BIENES
                                    if(ent_usuarios::permitir_area($_SESSION['usuario_area'], 'bienes')){
                                            //1CMA, 2CME, 3DIR, 4NSR, 5NSRDIR
                                        switch (+$objP->pro_modalidad) {
                                            case 1:
                                            case 2:
                                            case 3:
                                            // print_f($_POST);exit;

                                            // [txt_nro_proceso] => COM-0000-2017-OPS/PETROPERU
                                            // [txt_licitacion] => 123123
                                            // [txt_cantidad] => 1
                                            // [txt_aprobado_por] => Pepito
                                            // [txt_mer] => 14000.00
                                            // [rad_igv] => 1
                                            // [txt_pac] =>
                                            // [txt_pep] =>
                                                $reg_val['pro_numero'] = $p['txt_nro_proceso'];
                                                $reg_val['pro_nro_licitacion'] = $p['txt_licitacion'];
                                                // $reg_val['pro_fechaIngresoServicios'] = format_date($p['txt_fecha_ingreso_area_ser']);
                                                $reg_val['pro_cantidadMaterial'] = $p['txt_cantidad'];
                                                $reg_val['pro_aprobadorSolicitud'] = $p['txt_aprobado_por'];
                                                // $reg_val['pro_reservado'] = $p['rad_reservado'];
                                                // if(isset($p['txt_mer']))
                                                //     $reg_val['pro_mer'] =  $p['txt_mer'];

                                                // $reg_val['pro_moneda'] = $p['sel_tipo_moneda'];
                                                $reg_val['pro_pac'] = trim($p['txt_pac']) == '' ? 'NO' : $p['txt_pac'];

                                                $reg_val['pro_sistemaContratacion'] = $p['sel_sistema_contratacion'];
                                                $reg_val['pro_fechaIngresoBienes'] = format_date($p['txt_fecha_ingreso_area']);

                                                // $reg_val['pro_encargadoProceso'] = $p['sel_encargado_proceso'];
                                                // print_f($_POST);
                                                // print_f($reg_val);
                                                $this->proceso->update($reg_val);
                                                break;

                                            case 4:
                                            case 5:
                                                // print_f($_POST);
                                                $reg_val['pro_estado'] = 3;
                                                $reg_val['pro_cantidadMaterial'] = $p['txt_cantidad'];
                                                $reg_val['pro_aprobadorSolicitud'] = $p['txt_aprobado_por'];
                                                if(isset($_POST['txt_mer']))
                                                    $reg_val['pro_mer'] = $p['txt_mer'];
                                                $reg_val['pro_moneda'] = $p['sel_tipo_moneda'];
                                                $reg_val['pro_sistemaContratacion'] = $p['sel_sistema_contratacion'];
                                                $reg_val['pro_fechaIngresoBienes'] = format_date($p['txt_fecha_ingreso_area']);
                                                // print_f($_POST);
                                                $this->proceso->update($reg_val);
                                                break;
                                        }
                                        $data['callout_title'] = 'Registrado';
                                        $data['callout_content'] = 'Registro completado';
                                        $data['content_view'] = 'recursos/callouts';
                                    }else{
                                        $data['callout_type'] = 'danger';
                                        $data['callout_title'] = 'No autorizado';
                                        $data['callout_content'] = 'El proceso no puede ser registrado en esta área';
                                        $data['content_view'] = 'recursos/callouts';
                                    }
                                    break;
                            }
                            $_SESSION['success_reg_detalles_soled_serobr'] = true;
                            // $this->proceso->update($reg_val);
                        }else{
                            $data['callout_type'] = 'danger';
                            $data['callout_title'] = 'Error';
                            $data['callout_content'] = 'Los identificadores de tipo no son válidos';
                            $data['content_view'] = 'recursos/callouts';
                        }
                    }else{
                        $data['callout_type'] = 'danger';
                        $data['callout_title'] = 'Error PROCESO';
                        $data['callout_content'] = 'El número de proceso se encuentra registrado';
                        $data['content_view'] = 'recursos/callouts';
                    }



                }else{
                    $data['callout_type'] = 'danger';
                    $data['callout_title'] = 'Error';
                    $data['callout_content'] = 'Los identificadores no son idénticos';
                    $data['content_view'] = 'recursos/callouts';
                }
            }else{
                unset($_SESSION['success_reg_detalles_soled_serobr']);
                // print_f($objP);
                $data['ref_form'] = $_SESSION['ref_form'] = uniqid(mt_rand());
                if($objP){
                    if($this->proceso->verificar_modalidad($objP->pro_modalidad)){
                        if($objP->pro_estado == 1){

                            $date = getdate();
                            //determinar el modo de proceso
                            $data['rad_reservado_si'] = '';
                            $data['rad_reservado_no'] = '';
                             if($objP->pro_reservado == 1)
                                $data['rad_reservado_si'] = 'checked';
                            else $data['rad_reservado_no'] = 'checked';
                            switch (+$objP->sol_tipo) {
                                case 1:
                                case 3:
                                    if(ent_usuarios::permitir_area($_SESSION['usuario_area'], 'servicios')){

                                        switch (+$objP->pro_modalidad) {
                                            case 1: $data['titulo_header'] .= ' | COM'; break;
                                            case 2: $data['titulo_header'] .= ' | SEL'; break;
                                            case 3: $data['titulo_header'] .= ' | ABR'; break;
                                            case 4: $data['titulo_header'] .= ' | NSR'; break;
                                            case 5: $data['titulo_header'] .= ' | NSR-ABR'; break;

                                        }

                                        $data['correlativo_temp'] = '';


                                        $this->load->model('Usuarios_mod', 'usuarios');
                                            //1CMA, 2CME, 3DIR, 4NSR, 5NSRDIR@@
                                        switch (+$objP->pro_modalidad) {
                                            case 1:
                                                $data['correlativo_temp'] = 'COM-0000-'.$date['year'].'-OPS/PETROPERU';
                                                // $this->load->model('Usuarios_mod', 'usuarios');
                                                $data['content_view'] = 'proceso/servicios/pro_ser_registro_detalles_solped_cma';
                                                break;
                                            case 2:
                                                $data['correlativo_temp'] = 'SEL-0000-'.$date['year'].'-OPS/PETROPERU';
                                                // $this->load->model('Usuarios_mod', 'usuarios');
                                                $data['content_view'] = 'proceso/servicios/pro_ser_registro_detalles_solped_cme';
                                                // $data['script'] = array('assets/views/proceso/pro_registro_detalles.js');
                                                break;
                                            case 3:
                                                $data['correlativo_temp'] = 'ABR-0000-'.$date['year'].'-OPS/PETROPERU';
                                                // $this->load->model('Usuarios_mod', 'usuarios');
                                                $data['content_view'] = 'proceso/servicios/pro_ser_registro_detalles_solped_dir';
                                                $data['script'] = array('assets/plugins/typeahead/typeahead.bundle.js','assets/views/proceso/pro_registro_detalles.js');
                                                break;
                                            case 4:
                                            case 5:
                                                if($objP->pro_modalidad == 4)
                                                    $data['correlativo_temp'] = 'NSR-0000-'.$date['year'].'-OPS/PETROPERU';
                                                if($objP->pro_modalidad == 5)
                                                    $data['correlativo_temp'] = 'NSR-ABR-0000-'.$date['year'].'-OPS/PETROPERU';
                                                // $this->load->model('Usuarios_mod', 'usuarios');
                                                $data['content_view'] = 'proceso/servicios/pro_ser_registro_detalles_solped_nsr';
                                                $data['script'] = array('assets/plugins/typeahead/typeahead.bundle.js','assets/views/proceso/pro_registro_detalles.js');
                                                break;
                                        }

                                    }else{
                                        $data['callout_type'] = 'danger';
                                        $data['callout_title'] = 'No autorizado';
                                        $data['callout_content'] = 'El proceso no puede ser registrado en esta área';
                                        $data['content_view'] = 'recursos/callouts';
                                    }
                                    break;
                                case 2://BIENES

                                    if(ent_usuarios::permitir_area($_SESSION['usuario_area'], 'bienes')){
                                            //1CMA, 2CME, 3DIR, 4NSR, 5NSRDIR

                                        switch (+$objP->pro_modalidad) {
                                            case 1: $data['titulo_header'] .= ' | COM'; $data['correlativo_temp'] = 'COM-0000-'.$date['year'].'-OPS/PETROPERU'; break;
                                            case 2: $data['titulo_header'] .= ' | SEL'; $data['correlativo_temp'] = 'SEL-0000-'.$date['year'].'-OPS/PETROPERU'; break;
                                            case 3: $data['titulo_header'] .= ' | ABR'; $data['correlativo_temp'] = 'ABR-0000-'.$date['year'].'-OPS/PETROPERU'; break;
                                            case 4: $data['titulo_header'] .= ' | NSR'; $data['correlativo_temp'] = 'NSR-0000-'.$date['year'].'-OPS/PETROPERU'; break;
                                            case 5: $data['titulo_header'] .= ' | NSR-ABR'; $data['correlativo_temp'] = 'NSR-ABR-0000-'.$date['year'].'-OPS/PETROPERU';break;

                                        }

                                        switch (+$objP->pro_modalidad) {
                                            case 1:
                                            case 2:
                                            case 3:

                                                $data['content_view'] = 'proceso/bienes/pro_bie_registro_detalles_solped';
                                                $data['script'] = array('assets/plugins/typeahead/typeahead.bundle.js', 'assets/views/proceso/pro_registro_detalles.js');
                                                break;
                                            case 4:
                                            case 5:
                                                $data['content_view'] = 'proceso/bienes/pro_bie_registro_detalles_solped_nsr';
                                                $data['script'] = array('assets/plugins/typeahead/typeahead.bundle.js','assets/views/proceso/pro_registro_detalles.js');
                                                break;
                                        }
                                    }else{
                                        $data['callout_type'] = 'danger';
                                        $data['callout_title'] = 'No autorizado';
                                        $data['callout_content'] = 'El proceso no puede ser registrado en esta área';
                                        $data['content_view'] = 'recursos/callouts';
                                    }
                                    break;

                                default:

                                    break;
                            }
                            $data['obj_proceso'] = $objP;
                        }else{
                            $data['callout_type'] = 'danger';
                            $data['callout_title'] = 'Error PROCESO';
                            $data['callout_content'] = 'El proceso debe estar en estado Elaborado';
                            $data['content_view'] = 'recursos/callouts';
                        }
                    }else{
                        $data['callout_type'] = 'danger';
                        $data['callout_title'] = 'Modalidad no registrada';
                        $data['callout_content'] = 'Modalidad no registrada';
                        $data['content_view'] = 'recursos/callouts';
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

}