<!-- para cma -->
<?php
$a = $this->usuarios->get_supervisor_area($_SESSION['usuario_area']);
$b = $this->usuarios->get_auxiliar_area($_SESSION['usuario_area']);
$lst_encargados = array_merge($b, $a);
 ?>
<div class="row">
    <div class="col-sm-8">
    <div class="box box-primary">
   <!--      <div class="box-header with-border">
            <h3 class="box-title">Registro</h3>
        </div> -->
        <!-- /.box-header -->
        <!-- form start -->
        <form class="form-horizontal" action="<?= $_SERVER['REQUEST_URI'] ?>" method="post">
            <div class="box-body">

                <div class="col-sm-12">
                    <h3 class="text-center"><?= $obj_proceso->pro_expediente ?>
                </div>
            <div class="col-sm-12">


                <input type="hidden" name="<?= md5('txt_id_proceso') ?>" value="<?= md5($obj_proceso->pro_id) ?>">
                <input type="hidden" name="<?= md5('txt_modalidad') ?>" value="<?= md5($obj_proceso->pro_modalidad) ?>">
                <input type="hidden" name="<?= md5('txt_sol_tipo') ?>" value="<?= md5($obj_proceso->sol_tipo) ?>">
               <!--  <div class="form-group">
                    <label for="txt_nro_proceso" class="col-sm-2 control-label-left">N° de proceso</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control text-no-submit" id="" name="txt_nro_proceso" id="txt_nro_proceso" placeholder="" value="<?= $correlativo_temp ?>" >
                    </div>
                </div> -->
            </div>

                <div class="col-sm-6">

                    <div class="form-group">
                        <label for="" class="col-sm-4 control-label-left">N° SOLPED</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control text-no-submit" id="" placeholder="" name="" value="<?= $obj_proceso->sol_numero ?>" readonly="">
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">

                   <!--  <div class="form-group">
                        <label for="txt_licitacion" class="col-sm-4 control-label-left">N° Licitación</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control text-no-submit" id="txt_licitacion" placeholder="" name="txt_licitacion" required>
                        </div>

                    </div> -->
                </div>
                <div class="col-sm-12">

                    <div class="form-group">
                        <label for="" class="col-sm-2 control-label-left">Descripción</label>
                        <div class="col-sm-10">
                            <textarea class="form-control" rows="3" placeholder="" readonly=""><?= $obj_proceso->sol_descripcion ?></textarea>
                        </div>
                    </div>
                </div>
                <div class="clearfix"></div>
                <fieldset>
                    <legend>SOLICITANTE</legend>
                    <div class="col-sm-12">

                        <div class="form-group">
                            <label for="" class="col-sm-1 control-label-left">Área</label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control text-no-submit" id="" placeholder="" readonly="" value="<?= $obj_proceso->pro_area ?>">
                            </div>
                            <label for="" class="col-sm-2 control-label-left">Encargado</label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control text-no-submit" id="" placeholder="" readonly="" value="<?= $obj_proceso->pro_encargado ?>">
                            </div>
                        </div>
                    </div>

                </fieldset>

                <fieldset>
                    <legend>DETALLES</legend>

                    <div class="col-sm-4">

                        <div class="form-group">
                            <label for="rad_reservado_si" class="col-sm-4 control-label-left">Reservado</label>
                            <div class="col-sm-8">


                              <div class="radio">
                                <label>
                                  <input type="radio" id="rad_reservado_si" class="rad_reservado" name="" value="1" <?= $rad_reservado_si ?> disabled>
                                  SI
                                </label>
                                <label>
                                  <input type="radio" id="rad_reservado_no" class="rad_reservado" name="" value="0"  <?= $rad_reservado_no ?> disabled>
                                    NO
                                </label>
                              </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4">

                        <div class="form-group">
                            <label for="txt_mer" class="col-sm-3 control-label-left">MER</label>

                            <div class="col-sm-9">
                                <input type="text" class="form-control text-no-submit" name="txt_mer" id="txt_mer" placeholder=""  value="<?= $obj_proceso->pro_mer ?>" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label for="inputPassword3" class="col-sm-3 control-label-left">Moneda</label>

                            <div class="col-sm-9">
                                <select class="form-control" disabled="">
                                    <option value="1" <?= $obj_proceso->pro_moneda == 1 ? 'selected=""' : '' ?>>SOLES</option>
                                    <option value="2" <?= $obj_proceso->pro_moneda == 2 ? 'selected=""' : '' ?>>DÓLARES</option>

                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <div class="col-sm-4">

                        <div class="form-group">
                            <label for="inputPassword3" class="col-sm-2 control-label-left">IGV</label>
                            <div class="col-sm-10">


                              <div class="radio">
                                <label>
                                  <input type="radio" id="rad_igv_si" name="rad_igv" value="1">
                                  SI
                                </label>
                                <label>
                                  <input type="radio" id="rad_igv_no" name="rad_igv" value="0" checked>
                                    NO
                                </label>
                              </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-4">

                        <div class="form-group">
                            <label for="" class="col-sm-3 control-label-left">PAC</label>

                            <div class="col-sm-9">
                                <input type="text" class="form-control text-no-submit" id="" placeholder="" readonly="" value="<?= $obj_proceso->pro_pac ?>">
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-4">

                        <div class="form-group">
                            <label for="txt_pep" class="col-sm-3 control-label-left">PEP</label>

                            <div class="col-sm-9">
                                <input type="text" class="form-control text-no-submit" id="txt_pep" placeholder="" name="txt_pep">
                            </div>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <!-- <div class="col-sm-12">

                        <div class="form-group">
                            <label for="txt_informe" class="col-sm-3 control-label-left">N° Informe técnico</label>

                            <div class="col-sm-6">
                                <input type="text" class="form-control text-no-submit" id="txt_informe" placeholder="" name="txt_informe" required="">
                            </div>
                        </div>
                    </div> -->
                </fieldset>
                <hr>


                    <div class="clearfix"></div>
                    <div class="col-sm-8">


                        <div class="form-group">
                            <label for="txt_fecha_ingreso_area_ser" class="col-sm-5 control-label-left">Fecha de ingreso al área</label>

                            <div class="col-sm-7">
                             <div class="input-group">
                                <input type="text" class="form-control daterange-default text-no-submit" name="txt_fecha_ingreso_area_ser" id="txt_fecha_ingreso_area_ser">
                                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                              </div>
                            </div>
                        </div>


                    </div>

                    <div class="col-sm-12">
                        <div class="form-group">
                            <label for="sel_encargado_proceso" class="col-sm-4 control-label-left">Asginar encargado de proceso</label>

                            <div class="col-sm-8">
                                <select class="form-control" name="sel_encargado_proceso" id="sel_encargado_proceso">
                                    <?php foreach ($lst_encargados as $key => $value): ?>
                                        <option value="<?= $value->idEmployee ?>" <?= +$obj_proceso->pro_supervisor == +$value->idEmployee ? 'selected' : '' ?>><?= $value->nombres.' '.$value->apellidos ?></option>
                                    <?php endforeach ?>

                                </select>
                            </div>
                        </div></div>


            </div>
            <!-- /.box-body -->
            <div class="box-footer">

                <div class="col-sm-12">
                    <input type="hidden" name="ref_form" value="<?= $ref_form ?>">
                    <button type="submit" class="btn btn-info center-block" name="btn_asginar" value="submit">Guardar</button>
                </div>
            </div>
            <!-- /.box-footer -->
        </form>
    </div>
</div>
</div>