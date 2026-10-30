<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
$title="";
?>
<script src="<?php echo base_url(); ?>backend/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js"></script>
<div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                            <h3 class="card-title titlefix"><?php echo $this->lang->line('discharged_patient'); ?></h3>
                        <div class="d-flex gap-2 align-items-center flex-wrap addmeeting">
                        </div>    
                    </div><!-- /.card-header -->
                        <div class="card-body">
                            <div class="download_label"><?php
                                if ($title == 'old_patient') {
                                    echo $this->lang->line('opd_old_patient');
                                    ?>
                                    <?php
                                } else {
                                    echo $this->lang->line('opd_patient');
                                    ?>
                                <?php } ?>
                            </div>
                            <table class="table table-striped table-bordered table-hover ajaxlist" data-export-title="<?php echo $this->lang->line('discharged_patient'); ?>">
                                <thead>
                                    <tr>
                                    <th><?php echo $this->lang->line('name') ?></th>
                                    <th><?php echo $this->lang->line('patient_id'); ?></th>
                                    <th><?php echo $this->lang->line('guardian_name') ?></th>
                                    <th><?php echo $this->lang->line('gender'); ?></th>
                                    <th><?php echo $this->lang->line('phone'); ?></th>
                                    <th><?php echo $this->lang->line('consultant'); ?></th>
                                    <th><?php echo $this->lang->line('last_visit'); ?></th>
                                    <?php                                   
                                        if (!empty($fields)) {
                                            foreach ($fields as $fields_key => $fields_value) {
                                                ?>
                                                <th><?php echo $fields_value->name; ?></th>
                                                <?php
                                            } 
                                        }
                                    ?> 
                                    <th class="text-end"><?php echo $this->lang->line('total_visit'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                </div>  
            </div>
        </div>

<div class="modal fade sh-modal sh-modal-accent" id="myModal" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered pup100">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myModalLabel"><?php echo $this->lang->line('add_patient'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
        <form id="formadd" accept-charset="utf-8" action="<?php echo base_url() . "admin/patient" ?>" enctype="multipart/form-data" method="post">
            <div class="scroll-area">
                <div class="modal-body">
                    <div class="row mb-2">
                        <div class="col-sm-6 col-6">
                            <div class="form-group15">
                                <div>
                                    <select onchange="get_PatientDetails(this.value)"  class="form-control patient_list_ajax" <?php
                                    if ($disable_option == true) {

                                    }
                                    ?> class="w-100" name='' id="addpatient_id">
                                    </select>
                                </div>
                                <span class="text-danger"><?php echo form_error('refference'); ?></span>
                            </div>
                        </div><!--./col-sm-8-->
                        <div class="col-sm-4 col-5">
                            <div class="form-group15">
                                <?php if ($this->rbac->hasPrivilege('patient', 'can_add')) { ?>
                                    <a data-bs-toggle="modal" id="add" onclick="holdModal('myModalpa')" class="modalbtnpatient"><i class="fa fa-plus"></i>  <span><?php echo $this->lang->line('new_patient'); ?></span></a>
                                <?php } ?>

                                <?php if ($this->rbac->hasPrivilege('discharged_patients', 'can_view')) { ?>
                                    <a  href="<?php echo base_url() ?>admin/patient/discharged_patients" class="btn btn-primary btn-sm"><i class="fa fa-reorder"></i> <?php echo $this->lang->line('discharged_patient'); ?></a>
                                <?php } ?>
                            </div>
                        </div><!--./col-sm-4-->
                    </div><!-- ./row -->
                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12">                            
                                <input name="patient_id" id="patient_id" type="hidden" class="form-control" />
                                <input name="email" id="pemail" type="hidden" class="form-control" />
                                <input name="mobileno" id="mobnumber" type="hidden" class="form-control" />
                                <input name="patient_name" id="patientname" type="hidden" class="form-control" />
                                <input name="password" id="password" type="hidden" class="form-control" />                                
                                <div class="row row-eq">
                                    <div class="col-lg-8 col-md-8 col-sm-8">
                                        <div id="ajax_load"></div>
                                        <div class="row ptt10" id="patientDetails" class="d-none">
                                            <div class="col-md-9 col-sm-9 col-9">
                                                <ul class="singlelist">
                                                    <li class="singlelist24bold">
                                                        <span id="listname"></span></li>
                                                    <li>
                                                        <i class="fas fa-user-secret" data-bs-toggle="tooltip" data-placement="top" title="<?php echo $this->lang->line('guardian'); ?>"></i>
                                                        <span id="guardian"></span>
                                                    </li>
                                                </ul>   
                                                <ul class="multilinelist">   
                                                    <li>
                                                        <i class="fas fa-venus-mars" data-bs-toggle="tooltip" data-placement="top" title="<?php echo $this->lang->line('gender'); ?>"></i>
                                                        <span id="genders"></span>
                                                    </li>
                                                    <li>
                                                        <i class="fas fa-tint" data-bs-toggle="tooltip" data-placement="top" title="<?php echo $this->lang->line('blood_group'); ?>"></i>
                                                        <span id="blood_group"></span>
                                                    </li>
                                                    <li>
                                                        <i class="fas fa-ring" data-bs-toggle="tooltip" data-placement="top" title="<?php echo $this->lang->line('marital_status'); ?>"></i>
                                                        <span id="marital_status"></span>
                                                    </li> 
                                                </ul>  
                                                <ul class="singlelist">  
                                                    <li>
                                                        <i class="fas fa-hourglass-half" data-bs-toggle="tooltip" data-placement="top" title="<?php echo $this->lang->line('age'); ?>"></i>
                                                        <span id="age"></span>
                                                    </li>
                                                    <li>
                                                        <i class="fa fa-phone-square" data-bs-toggle="tooltip" data-placement="top" title="<?php echo $this->lang->line('phone'); ?>"></i> 
                                                        <span id="listnumber"></span>
                                                    </li>
                                                    <li>
                                                        <i class="fa fa-envelope" data-bs-toggle="tooltip" data-placement="top" title="<?php echo $this->lang->line('email'); ?>"></i>
                                                        <span id="email"></span>
                                                    </li>
                                                    <li>
                                                        <i class="fas fa-street-view" data-bs-toggle="tooltip" data-placement="top" title="<?php echo $this->lang->line('address'); ?>"></i>
                                                        <span id="address"></span>
                                                    </li>
                                                    <li>
                                                        <b><?php echo $this->lang->line('any_known_allergies') ?> </b> 
                                                        <span id="allergies"></span>
                                                    </li>
                                                    <li>
                                                        <b><?php echo $this->lang->line('remarks') ?> </b> 
                                                        <span id="note"></span>
                                                    </li>  
                                                    <li>
                                                        <b><?php echo $this->lang->line("insurance_id"); ?> </b> 
                                                        <span id="insurance_id" ></span>
                                                    </li>
                                                    <li>
                                                        <b><?php echo $this->lang->line("validity"); ?> </b> 
                                                        <span id="validity"></span>
                                                    </li>   
                                                </ul>                               
                                            </div><!-- ./col-md-9 -->
                                            <div class="col-md-3 col-sm-3 col-3"> 
                                                <div class="float-end">  
                                                
                                                    <?php
                                                    $file = "uploads/patient_images/no_image.png";
                                                    ?>        
                                                    <img class="modal-profile-user-img img-fluid" src="<?php echo $this->media_storage->getImageURL($file); ?>" id="image" alt="User profile picture">
                                                </div>           
                                            </div><!-- ./col-md-3 --> 
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12"> 
                                                <div class="dividerhr"></div>
                                            </div><!--./col-md-12-->
                                            <div class="col-sm-2 col-4">
                                                <div class="mb-3">
                                                    <label for="pwd"><?php echo $this->lang->line('height'); ?></label> 
                                                    <input name="height" type="text" class="form-control" />
                                                </div>
                                            </div>
                                            <div class="col-sm-2 col-4">
                                                <div class="mb-3">
                                                    <label for="pwd"><?php echo $this->lang->line('weight'); ?></label> 
                                                    <input name="weight" type="text" class="form-control" />
                                                </div>
                                            </div>
                                            <div class="col-sm-2 col-4">
                                                <div class="mb-3">
                                                    <label for="pwd"><?php echo $this->lang->line('bp'); ?></label> 
                                                    <input name="bp" type="text" class="form-control" />
                                                </div>
                                            </div>
                                            <div class="col-sm-2 col-4">
                                                <div class="mb-3">
                                                    <label for="pwd"><?php echo $this->lang->line('pulse'); ?></label> 
                                                    <input name="pulse" type="text" class="form-control" />
                                                </div>
                                            </div>
                                            <div class="col-sm-2 col-4">
                                                <div class="mb-3">
                                                    <label for="pwd"><?php echo $this->lang->line('temperature'); ?></label> 
                                                    <input name="temperature" type="text" class="form-control" />
                                                </div>
                                            </div>
                                            <div class="col-sm-2 col-4">
                                                <div class="mb-3">
                                                    <label for="pwd"><?php echo $this->lang->line('respiration'); ?></label> 
                                                    <input name="respiration" type="text" class="form-control" />
                                                </div>
                                            </div>
                                            <div class="col-sm-3 col-6">
                                                <div class="mb-3">
                                                    <label for="exampleInputFile">
                                                        <?php echo $this->lang->line('symptoms_type') ; ?></label>
                                                    <div><select name='symptoms_type'  id="act"  class="form-control select2 act w-100" >
                                                            <option value=""><?php echo $this->lang->line('select') ?></option>
                                                            <?php foreach ($symptomsresulttype as $dkey => $dvalue) {
                                                                ?>
                                                            <option value="<?php echo $dvalue["id"]; ?>"><?php echo $dvalue["symptoms_type"] ;?></option>

                                                        <?php } ?>
                                                        </select>
                                                    </div>
                                                    <span class="text-danger"><?php echo form_error('symptoms_type'); ?></span>
                                                </div>
                                            </div>                                            
                                            <input name="rows[]" type="hidden" value="1">
                                                <div class="col-sm-3 col-6">
                                                    <label for="exampleInputFile"> 
                                                        <?php echo $this->lang->line('symptoms_title'); ?></label>
                                                    <div id="dd" class="wrapper-dropdown-3">
                                                        <input class="form-control filterinput" type="text">
                                                        <ul class="dropdown scroll150 section_ul">
                                                            <li><label class="checkbox"><?php echo $this->lang->line('select') ; ?></label></li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            <div class="col-sm-6 col-12">
                                                <div class="mb-3">
                                                    <label><?php echo $this->lang->line('symptoms_description'); ?></label>
                                                    <textarea class="form-control" id="symptoms_description" name="symptoms" ></textarea> 
                                                </div> 
                                            </div>                                        
                                            <div class="col-sm-12">
                                                <div class="mb-3">
                                                    <label for="pwd"><?php echo $this->lang->line('note'); ?></label> 
                                                    <textarea name="note" rows="3" class="form-control" ><?php echo set_value('note'); ?></textarea>
                                                </div>
                                            </div>
                                            <div>
                                            <?php
                                            echo display_custom_fields('opd');
                                            ?>
                                            </div>       
                                        </div><!--./row--> 
                                    </div><!--./col-md-8--> 
                                    <div class="col-lg-4 col-md-4 col-sm-4 col-eq ptt10">
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <div class="mb-3">
                                                    <label><?php echo $this->lang->line('appointment_date'); ?></label>
                                                    <small class="req"> *</small>
                                                    <input id="admission_date" name="appointment_date" placeholder="" type="text" class="form-control datetime" />
                                                    <span class="text-danger"><?php echo form_error('appointment_date'); ?></span>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="mb-3">
                                                    <label for="exampleInputFile">
                                                    <?php echo $this->lang->line('case'); ?></label>
                                                    <div><input class="form-control" type='text' name='case' />
                                                    </div>
                                                    <span class="text-danger"><?php echo form_error('case'); ?></span>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="mb-3">
                                                    <label for="exampleInputFile">
                                                    <?php echo $this->lang->line('casualty'); ?></label>
                                                    <div>
                                                    <select name="casualty" id="casualty" class="form-control">
                                                        <?php foreach ($yesno_condition as $yesno_key => $yesno_value) {
                                                            ?>
                                                            <option value="<?php echo $yesno_key ?>" <?php
                                                                    if ($yesno_key == 'no') {
                                                                        echo "selected";
                                                                    }
                                                                    ?> ><?php echo $yesno_value ?>
                                                            </option>
                                                            <?php } ?>
                                                    </select>                                                        
                                                    </div>
                                                    <span class="text-danger"><?php echo form_error('case'); ?></span>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="mb-3">
                                                    <label for="exampleInputFile">
                                                    <?php echo $this->lang->line('old_patient'); ?></label>
                                                    <div>
                                                        <select name="old_patient" id="" class="form-control">
                                                        <?php foreach ($yesno_condition as $yesno_key => $yesno_value) {
                                                            ?>
                                                            <option value="<?php echo $yesno_key ?>" <?php
                                                                    if ($yesno_key == 'no') {
                                                                        echo "selected";
                                                                    }
                                                                    ?> ><?php echo $yesno_value ?>
                                                            </option>
                                                            <?php } ?>
                                                        </select>
                                                    </div>
                                                    <span class="text-danger"><?php echo form_error('case'); ?></span>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="mb-3">
                                                    <label for="exampleInputFile">
                                                            <?php echo $this->lang->line('tpa'); ?></label>
                                                    <div><select class="form-control" onchange="get_Charges(this.value)" id="organisation" name='organisation' >
                                                            <option value="0"><?php echo $this->lang->line('select'); ?></option>
                                                            <?php foreach ($organisation as $orgkey => $orgvalue) {
                                                                ?>
                                                                <option value="<?php echo $orgvalue["id"]; ?>"><?php echo $orgvalue["organisation_name"] ?></option>   
                                                            <?php } ?>
                                                        </select>
                                                    </div>
                                                    <span class="text-danger"><?php echo form_error('refference'); ?></span>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="mb-3">
                                                    <label for="exampleInputFile">
                                                    <?php echo $this->lang->line('reference'); ?></label>
                                                    <div><input class="form-control" type='text' name='refference' />
                                                    </div>
                                                    <span class="text-danger"><?php echo form_error('refference'); ?></span>
                                                </div>
                                            </div>
                                            <div class="col-sm-12">
                                                <div class="mb-3">
                                                    <label for="exampleInputFile">
                                                        <?php echo $this->lang->line('consultant_doctor'); ?></label><small class="req"> *</small>
                                                    <div><select name='consultant_doctor' id="consultant_doctor" class="form-control select2" <?php
                                                        if ($disable_option == true) {
                                                            echo "disabled";
                                                        }
                                                        ?> class="w-100">
                                                            <option value=""><?php echo $this->lang->line('select') ?></option>
                                                            <?php foreach ($doctors as $dkey => $dvalue) {
                                                                ?>
                                                                <option value="<?php echo $dvalue["id"]; ?>" <?php
                                                                        if ((isset($doctor_select)) && ($doctor_select == $dvalue["id"])) {
                                                                            echo "selected";
                                                                        }
                                                                        ?>><?php echo $dvalue["name"] . " " . $dvalue["surname"]." (".$dvalue["employee_id"].")" ?></option>   
                                                        <?php } ?>
                                                        </select>
                                                        <?php if ($disable_option == true) { ?>
                                                            <input type="hidden" name="consultant_doctor"  value="<?php echo $doctor_select ?>">
                                                        <?php } ?>
                                                    </div>
                                                    <span class="text-danger"><?php echo form_error('refference'); ?></span>
                                                </div>
                                            </div>
                                    <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label><?php echo $this->lang->line('charge_category'); ?></label>
                        <select name="charge_category" class="w-100 form-control charge_category select2">
                                                <option value=""><?php echo $this->lang->line('select'); ?></option>
                                                <?php foreach ($charge_category as $key => $value) {
                                                    ?>
                                                    <option value="<?php echo $value['id']; ?>">
                                                    <?php echo $value['name']; ?>
                                                    </option>
                                                <?php } ?>
                                            </select>
                                                    <span class="text-danger"><?php echo form_error('charge_category'); ?></span>
                                                </div>
                                            </div>     
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                            <label><?php echo $this->lang->line('charge'); ?></label><small class="req"> *</small>
                                            <select name="charge_id" class="w-100 form-control charge select2">
                                    <option value=""><?php echo $this->lang->line('select')?></option>
                                            </select>
                                                    <span class="text-danger"><?php echo form_error('apply_charge'); ?></span>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                            <div class="mb-3"> 
                                                    <label for="exampleInputEmail1"><?php echo $this->lang->line('tax'); ?></label>
                                                    <div class="input-group">
                                                        <input type="text" class="form-control right-border-none" name="percentage" id="percentage" readonly autocomplete="off">
                                                        <span class="input-group-text "> %</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label><?php echo $this->lang->line('standard_charge') . " (" . $currency_symbol . ")" ?></label>
                                                    <input type="text" readonly name="standard_charge" id="standard_charge" class="form-control" value="<?php echo set_value('standard_charge'); ?>"> 
                                                    <span class="text-danger"><?php echo form_error('standard_charge'); ?></span>
                                                </div>
                                            </div> 
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label><?php echo $this->lang->line('applied_charge') . " (" . $currency_symbol . ")" ?></label><small class="req"> *</small><input type="text" name="amount" id="apply_charge" onkeyup="update_amount(this.value)" class="form-control">    
                                                    <span class="text-danger"><?php echo form_error('amount'); ?></span>
                                                </div>
                                            </div>
                                        <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label><?php echo $this->lang->line('amount'). " (" . $currency_symbol . ")" ?></label><small class="req"> *</small><input type="text" name="apply_amount" readonly id="apply_amount" class="form-control">
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="mb-3">
                                                    <label for="pwd"><?php echo $this->lang->line('payment_mode'); ?></label>
                                                    <select name="payment_mode" class="form-control payment_mode">
                                                        <?php foreach ($payment_mode as $payment_key => $payment_value) {
                                                            ?>
                                                            <option value="<?php echo $payment_key ?>" <?php
                                                                    if ($payment_key == 'cash') {
                                                                        echo "selected";
                                                                    }
                                                                    ?> ><?php echo $payment_value ?></option>
                                                            <?php } ?>
                                                    </select>
                                                </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="mb-3">
                                                <label for="pwd"><?php echo $this->lang->line('paid_amount') . " (" . $currency_symbol . ")" ; ?></label><small class="req"> *</small>
                                                <input type="text" name="paid_amount" id="paid_amount" class="form-control">    
                                                <span class="text-danger"><?php echo form_error('paid_amount'); ?></span>
                                            </div>
                                        </div>
                                        <div class="cheque_div d-none" >
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label><?php echo $this->lang->line('cheque_no'); ?></label><small class="req"> *</small> 
                                                    <input type="text" name="cheque_no" id="cheque_no" class="form-control">
                                                    <span class="text-danger"></span>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label><?php echo $this->lang->line('cheque_date'); ?></label><small class="req"> *</small> 
                                                    <input type="text" name="cheque_date" id="cheque_date" class="form-control date">
                                                    <span class="text-danger"></span>
                                                </div>
                                            </div>
                                            <div class="col-sm-12">
                                            <div class="mb-3">
                                                <label><?php echo $this->lang->line('attach_document'); ?></label>
                                                <input type="file" class="filestyle form-control" name="document">
                                                <span class="text-danger"><?php echo form_error('document'); ?></span> 
                                            </div>
                                    </div>
                                    </div>
                                        <?php if ($this->module_lib->hasActive('live_consultation')) { ?>
                                            <div class="col-sm-6">
                                                <div class="mb-3">
                                                    <label>
                                                    <?php echo $this->lang->line('live_consultation'); ?></label>
                                                    <select name="live_consult"  class="form-control">
                                                        <?php
                                                         foreach ($yesno_condition as $yesno_key => $yesno_value) {
                                                            ?>
                                                            <option value="<?php echo $yesno_key ?>" <?php
                                                                    if ($yesno_key == 'no') {
                                                                        echo "selected";
                                                                    }
                                                                    ?> ><?php echo $yesno_value; ?>
                                                            </option>
                                                            <?php } ?>
                                                    </select> 
                                                    <span class="text-danger"><?php echo form_error('live_consult'); ?></span>
                                                </div>
                                            </div> 
                                        <?php  } ?>
                                            
                                        </div><!--./row-->    
                                    </div><!--./col-md-4-->
                                </div><!--./row-->        
                        </div><!--./col-md-12-->       
                    </div><!--./row--> 
                </div>
            </div>  

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                    <button type="submit" id="formaddbtn" name="save" data-loading-text="<?php echo $this->lang->line('processing') ?>" class="btn btn-info"><i class="fa fa-check-circle"></i> <span><?php echo $this->lang->line('save'); ?></span></button>
                    <button type="submit"  data-loading-text="<?php echo $this->lang->line('processing') ?>" name="save_print" class="btn btn-info printsavebtn"><?php echo $this->lang->line('save_print'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade sh-modal sh-modal-accent" id="myModaledit" tabindex="-1" aria-labelledby="myModaleditLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myModaleditLabel"><?php echo $this->lang->line('patient_details'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12 paddlr">
                        <form id="formedit" accept-charset="utf-8" enctype="multipart/form-data" method="post" class="ptt10">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label><?php echo $this->lang->line('name'); ?></label><small class="req"> *</small> 
                                        <input id="patient_name" name="name" placeholder="" type="text" class="form-control"  value="<?php echo set_value('name'); ?>" />
                                        <input type="hidden" id="updateid" name="updateid">
                                        <input type="hidden" id="opdid" name="opdid">
                                        <span class="text-danger"><?php echo form_error('name'); ?></span>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="mb-3">
                                        <label><?php echo $this->lang->line('guardian_name'); ?></label>
                                        <input type="text" id="guardian_name" name="guardian_name" value="<?php echo set_value('guardian_name'); ?>" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label><?php echo $this->lang->line('gender'); ?></label><small class="req"> *</small> 
                                        <select class="form-control" id="gender" name="gender">
                                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                                            <?php
                                            foreach ($genderList as $key => $value) {
                                                ?>
                                                <option value="<?php echo $key; ?>" <?php if (set_value('gender') == $key) echo "selected"; ?>><?php echo $value; ?></option>
    <?php
}
?>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('gender'); ?></span>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="mb-3">
                                        <label for="pwd"><?php echo $this->lang->line('marital_status'); ?></label>
                                        <select name="marital_status" id="marital_status" class="form-control">
                                            <option value=""><?php echo $this->lang->line('select') ?></option>
<?php foreach ($marital_status as $mkey => $mvalue) {
    ?>
                                                <option value="<?php echo $mkey ?>"><?php echo $mvalue ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="mb-3">
                                        <label for="exampleInputFile">
<?php echo $this->lang->line('patient_photo'); ?></label>
                                        <div><input class="filestyle form-control" type='file' name='file' id="file" size='20' />
                                            <input type="hidden" name="patient_photo" id="patient_photo">
                                        </div>
                                        <span class="text-danger"><?php echo form_error('file'); ?></span>
                                    </div>
                                </div>  
                                <div class="col-sm-3">
                                    <div class="mb-3">
                                        <label><?php echo $this->lang->line('email'); ?></label>
                                        <input type="text" id="email" value="<?php echo set_value('email'); ?>" name="email" class="form-control">
                                    </div>
                                </div> 
                                <div class="col-sm-2">
                                    <div class="mb-3">
                                        <label for="pwd"><?php echo $this->lang->line('phone'); ?></label>
                                        <input id="contact" autocomplete="off" name="contact" placeholder="" type="text" class="form-control"  value="<?php echo set_value('contact'); ?>" />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="mb-3">
                                        <label> <?php echo $this->lang->line('blood_group'); ?></label><small class="req"> *</small> 
                                        <select class="form-control" id="blood_group" name="blood_group">
                                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                                            <?php
                                            foreach ($bloodgroup as $key => $value) {
                                                ?>
                                                <option value="<?php echo $value; ?>" <?php if (set_value('blood_group') == $key) echo "selected"; ?>><?php echo $value; ?></option>
                                                        <?php
                                                    }
                                                    ?>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('blood_group'); ?></span>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="mb-3">
                                        <label><?php echo $this->lang->line('age'); ?></label>
                                        <div class="sh-clear">
                                            <input type="text" placeholder="<?= $this->lang->line('age') ?>" id="age" name="age" value="<?php echo set_value('age'); ?>" class="form-control sh-print-left-40" >
                                            <input type="text" placeholder="<?= $this->lang->line('month') ?>" id="month" name="month" value="<?php echo set_value('month'); ?>" class="form-control sh-print-left-56" >
                                        </div>
                                    </div>
                                </div> 

                            </div><!--./row-->
                            <button type="submit" class="btn btn-info float-end"><?php echo $this->lang->line('save'); ?></button>
                    </div><!--./col-md-12-->
                </div><!--./row-->
            </div>
            </form>
        </div>
    </div>
</div>

<script type="text/javascript">
( function ( $ ) {
    'use strict';
    $(document).ready(function () {      
        initDatatable('ajaxlist','admin/patient/getopddischargepatient',[],[],100);
    });
} ( jQuery ) )
</script>

<script>
    $(document).on('change', '.act', function () {
        $this = $(this);
        var sys_val = $(this).val();
        var section_ul = $(this).closest('div.row').find('ul.section_ul');
        $.ajax({
            type: 'POST',
            url: base_url + 'admin/patient/getPartialsymptoms',
            data: {'sys_id': sys_val},  
            dataType: 'JSON',
            beforeSend: function () {             
                $('ul.section_ul').find('li:not(:first-child)').remove();                
            },
            success: function (data) {
                section_ul.append(data.record);
            },
            error: function (xhr) { // if error occured
                alert("Error occured.please try again");
            },
            complete: function () {
            }
        });
    });
</script>
<script type="text/javascript">    

    $(document).on('click', '.remove_row', function () {
        $this = $(this);
        $this.closest('.row').remove();
    });
	
    $(document).mouseup(function (e)
    {
        var container = $(".wrapper-dropdown-3"); // YOUR CONTAINER SELECTOR
        if (!container.is(e.target) // if the target of the click isn't the container...
                && container.has(e.target).length === 0) // ... nor a descendant of the container
        {
            $("div.wrapper-dropdown-3").removeClass('active');
        }
    });

    $(document).on('click', '.filterinput', function () {
        if (!$(this).closest('.wrapper-dropdown-3').hasClass("active")) {
            $(".wrapper-dropdown-3").not($(this)).removeClass('active');
            $(this).closest("div.wrapper-dropdown-3").addClass('active');
        }
    });

    $(document).on('click', 'input[name="section[]"]', function () {
        $(this).closest('label').toggleClass('active_section');
    });

    $(document).on('keyup', '.filterinput', function () {

        var valThis = $(this).val().toLowerCase();
        var closer_section = $(this).closest('div').find('.section_ul > li');
        var noresult = 0;
        if (valThis == "") {
            closer_section.show();
            noresult = 1;
            $('.no-results-found').remove();
        } else {
            closer_section.each(function () {
                var text = $(this).text().toLowerCase();
                var match = text.indexOf(valThis);
                if (match >= 0) {
                    $(this).removeClass('d-none');
                    noresult = 1;
                    $('.no-results-found').remove();
                } else {
                    $(this).addClass('d-none');
                }
            });
        }
        ;
        if (noresult == 0) {
            closer_section.append('<li class="no-results-found">No results found.</li>');
        }
    });
</script>

<script type="text/javascript">
   
    $('#myModal').on('hidden.bs.modal', function (e) {
        $(this).find('#formadd')[0].reset();
    });

    $('#myModalpa').on('hidden.bs.modal', function (e) {
        $(this).find('#formaddpa')[0].reset();
    });

    $(function () {
        $('#easySelectable').easySelectable();
        $('.select2').select2()    
    })

    function makeid(length) {
        var result = '';
        var characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        var charactersLength = characters.length;
        for (var i = 0; i < length; i++) {
            result += characters.charAt(Math.floor(Math.random() * charactersLength));
        }
        return result;
    }

    function get_PatientDetails(id) {      
        
        var base_url = "<?php echo $this->media_storage->getImageURL('backend/images/loading.gif') ?>";

        $("#ajax_load").html("<center><img src='" + base_url + "'/>");
        var password = makeid(5)
       if(id==''){
            $("#ajax_load").html("");
            $("#patientDetails").addClass('d-none');
       }else{
             
            $.ajax({
            url: '<?php echo base_url(); ?>admin/patient/patientDetails',
            type: "POST",
            data: {id: id},
            dataType: 'json',
            success: function (res) {
                
                if (res) {
                    $("#ajax_load").html("");
                    $("#patientDetails").removeClass('d-none');
                    $('#patient_unique_id').html(res.id);
                    $('#patient_id').val(res.id);
                    $('#password').val(password);
                    $('#revisit_password').val(password);
                    $('#listname').html(res.patient_name);
                    $('#guardian').html(res.guardian_name);
                    $('#listnumber').html(res.mobileno);
                    $('#email').html(res.email);
                    $('#mobnumber').val(res.mobileno);
                    $('#pemail').val(res.email);
                    $('#patientname').val(res.patient_name);
                    if (res.age == "") {
                        $("#age").html("");
                    } else {
                      
                    $("#age").html(res.age);
                    }
                    $('#doctname').val(res.name + " " + res.surname);
                    $("#bp").html(res.bp);
                    $("#symptoms").html(res.symptoms);
                    $("#known_allergies").html(res.known_allergies);
                    $("#insurance_id").html(res.insurance_id);
                    $("#validity").html(res.insurance_validity);
                    $("#address").html(res.address);
                    $("#note").html(res.note);
                    $("#height").html(res.height);
                    $("#weight").html(res.weight);
                    $("#genders").html(res.gender);
                    $("#marital_status").html(res.marital_status);
                    $("#blood_group").html(res.blood_group);
                    $("#allergies").html(res.known_allergies);
                    $("#image").attr("src", '<?php echo base_url() ?>' + res.image);
                } else {
                    $("#ajax_load").html("");
                    $("#patientDetails").addClass('d-none');
                }
            }
        });
       }
    }

    function get_Charges(orgid) {
        var charge =$('.charge').val();
        var apply_amount=0;
        $.ajax({
            url: '<?php echo base_url(); ?>admin/patient/getChargeById',
            type: "POST",
            data: {charge_id: charge, organisation_id: orgid},
            dataType: 'json',
            success: function (res) {
                if (res) {
                    $('#percentage').val(res.percentage);
                    if (orgid) {
                         if(res.percentage ==null){
                            apply_amount=parseFloat(res.org_charge);
                        }else{
                            apply_amount=(parseFloat(res.org_charge) * res.percentage/100)+(parseFloat(res.org_charge));
                        }
                          
                        $('#apply_charge').val(res.org_charge);
                        $('#apply_amount').val(apply_amount);
                        $('#standard_charge').val(res.standard_charge);
                        $('#paid_amount').val(res.apply_amount);
                    } else {
                        if(res.percentage ==null){
                            apply_amount=parseFloat(res.standard_charge);
                        }else{
                            apply_amount=(parseFloat(res.standard_charge) * res.percentage/100)+(parseFloat(res.standard_charge));
                        }
                     
                        $('#standard_charge').val(res.standard_charge);
                        $('#apply_charge').val(res.standard_charge);
                        $('#apply_amount').val(apply_amount);
                        $('#paid_amount').val(res.apply_amount);
                    }
                } else {
                    $('#standard_charge').val('');
                    $('#apply_charge').val('');
                }
            }
        });
    }

    function get_Chargesrevisit(id) {
        $("#standard_chargerevisit").html("standard_charge");
        var orgid = $("#revisit_organisation").val();
        if (id == '') {
            id = $("#revisit_doctor").val();
        }

        $.ajax({
            url: '<?php echo base_url(); ?>admin/patient/doctCharge',
            type: "POST",
            data: {doctor: id, organisation: orgid},
            dataType: 'json',
            success: function (res) {             
                if (res) {
                    if (orgid) {
                        $('#revisit_amount').val(res.org_charge);
                        $('#standard_chargerevisit').val(res.standard_charge);
                    } else {
                        $('#standard_chargerevisit').val(res.standard_charge);
                        $('#revisit_amount').val(res.standard_charge);
                    }                 
                } else {
                    $('#standard_chargerevisit').val('');
                    $('#revisit_amount').val('');
                }
            }
        });
    }
   
	$(document).on('select2:select','.charge_category',function(){
		var charge_category=$(this).val();      
		$('.charge').html("<option value=''><?php echo $this->lang->line('loading') ?></option>");
		getchargecode(charge_category,"");
	});

    function getchargecode(charge_category,charge_id) {
      var div_data = "<option value=''><?= $this->lang->line('select') ?></option>";
      if(charge_category != ""){
          $.ajax({
            url: base_url+'admin/charges/getchargeDetails',
            type: "POST",
            data: {charge_category: charge_category},
            dataType: 'json',
            success: function (res) {              
                $.each(res, function (i, obj)
                {
                    var sel = "";
                    div_data += "<option value='" + obj.id + "'>" + obj.name + "</option>";

                });
                $('.charge').html(div_data);
                $(".charge").select2("val", charge_id);             
            }
        });
      }
    }

    function update_amount(apply_charge){       
        var apply_amount=apply_charge;
        var tax_percentage=$('#percentage').val();
        if(tax_percentage !='' && tax_percentage !=0){
            apply_amount=(parseFloat(apply_charge) * tax_percentage/100)+(parseFloat(apply_charge));            
            $('#apply_amount').val(apply_amount);
        }else{
            $('#apply_amount').val(apply_amount);
        }
    }

    $(document).on('select2:select','.charge',function(){
        var charge=$(this).val();
        var orgid = $("#organisation").val();
        var apply_amount=0;

		$.ajax({
            url: '<?php echo base_url(); ?>admin/patient/getChargeById',
            type: "POST",
            data: {charge_id: charge, organisation_id: orgid},
            dataType: 'json',
            success: function (res) {
              
                if (res) { 
                    var tax=res.percentage;
                    var quantity=$('#qty').val();
                    $('#percentage').val(tax);
                    $('#apply_charge').val(parseFloat(res.standard_charge) * quantity);
                    $('#standard_charge').val(res.standard_charge);
                    $('#schedule_charge').val(res.org_charge);                
                    $('#org_id').val(res.org_charge_id);

                    if (res.org_charge == null) {
                        if(res.percentage ==null){
                            apply_amount=parseFloat(res.standard_charge);
                        }else{
                            apply_amount=(parseFloat(res.standard_charge) * res.percentage/100)+(parseFloat(res.standard_charge));
                        }
                        
                        $('#apply_charge').val(res.standard_charge);
                        $('#apply_amount').val(apply_amount);
                       
                    } else {
                         if(res.percentage ==null){
                            apply_amount=parseFloat(res.org_charge);
                        }else{
                            apply_amount=(parseFloat(res.org_charge) * res.percentage/100)+(parseFloat(res.org_charge));
                        }
                       
                        $('#apply_charge').val(res.org_charge);
                        $('#apply_amount').val(apply_amount);
                      
                    }
                } else {
                   
                }
            }
        });
 });
</script>

<script type="text/javascript">
     
    (function ($) {
        //selectable html elements
        $.fn.easySelectable = function (options) {
            var el = $(this);
            var options = $.extend({
                'item': 'li',
                'state': true,
                onSelecting: function (el) {

                },
                onSelected: function (el) {

                },
                onUnSelected: function (el) {

                }
            }, options);
            el.on('dragstart', function (event) {
                event.preventDefault();
            });
            el.off('mouseover');
            el.addClass('easySelectable');
            if (options.state) {
                el.find(options.item).addClass('es-selectable');
                el.on('mousedown', options.item, function (e) {
                    $(this).trigger('start_select');
                    var offset = $(this).offset();
                    var hasClass = $(this).hasClass('es-selected');
                    var prev_el = false;
                    el.on('mouseover', options.item, function (e) {
                        if (prev_el == $(this).index())
                            return true;
                        prev_el = $(this).index();
                        var hasClass2 = $(this).hasClass('es-selected');
                        if (!hasClass2) {
                            $(this).addClass('es-selected').trigger('selected');
                            el.trigger('selected');
                            options.onSelecting($(this));
                            options.onSelected($(this));
                        } else {
                            $(this).removeClass('es-selected').trigger('unselected');
                            el.trigger('unselected');
                            options.onSelecting($(this))
                            options.onUnSelected($(this));
                        }
                    });
                    if (!hasClass) {
                        $(this).addClass('es-selected').trigger('selected');
                        el.trigger('selected');
                        options.onSelecting($(this));
                        options.onSelected($(this));
                    } else {
                        $(this).removeClass('es-selected').trigger('unselected');
                        el.trigger('unselected');
                        options.onSelecting($(this));
                        options.onUnSelected($(this));
                    }
                    var relativeX = (e.pageX - offset.left);
                    var relativeY = (e.pageY - offset.top);
                });
                $(document).on('mouseup', function () {
                    el.off('mouseover');
                });
            } else {
                el.off('mousedown');
            }
        };
    })(jQuery);

</script>

<script type="text/javascript">
    $(document).ready(function (e) {
        $("form#formadd button[type=submit]").click(function() {            
         $("button[type=submit]", $(this).parents("form")).removeAttr("clicked");
        $(this).attr("clicked", "true");
    });

    $("#formadd").on('submit', (function (e) {
			var form_valid=true;
             var amount_to_be_paid=parseInt($("form#formadd #apply_amount").val());
             var amount_paying= parseInt($("form#formadd #paid_amount").val());
             if(amount_to_be_paid < amount_paying){
                 errorMsg("Invalid Amount");
                return false;
             }

             var sub_btn_clicked = $("button[type=submit][clicked=true]");           
             var sub_btn_clicked_name=sub_btn_clicked.attr('name');
            e.preventDefault();
            $.ajax({
                url: '<?php echo base_url(); ?>admin/patient',
                type: "POST",
                data: new FormData(this),
                dataType: 'json',
                contentType: false,
                cache: false,
                processData: false,
                beforeSend: function() {
                 sub_btn_clicked.btnLoading() ; 
                 },
                success: function (data) {
                    if (data.status == "fail") {
                        var message = "";
                        $.each(data.error, function (index, value) {
                            message += value;
                        });
                        errorMsg(message);
                    } else {
                        successMsg(data.message);
                        if(sub_btn_clicked_name === "save_print") {                            
                           printVisitBill(data.opd_id);
                        }  
                        table.ajax.reload( null, false );
                        shModal('myModal').hide();
                    }
                      sub_btn_clicked.btnReset() ; 

                },
                 error: function(xhr) { // if error occured
        alert("Error occured.please try again");
       sub_btn_clicked.btnReset()  ;
    },
    complete: function() {
        sub_btn_clicked.btnReset();  
    }
            }); 
        }));
    });    
    
    $(document).ready(function (e) {
        $(".printsavedata").on('click', (function (e) {            
            var form = $(this).parents('form').attr('id');
            var str = $("#" + form).serializeArray();
            var postData = new FormData();
            $.each(str, function (i, val) {
                postData.append(val.name, val.value);
            });           

            $.ajax({
                url: '<?php echo base_url(); ?>admin/patient/add_revisit',
                type: "POST",
                data: postData,
                dataType: 'json',
                contentType: false,
                cache: false,
                processData: false,
                success: function (data) {
                    if (data.status == "fail") {
                        var message = "";
                        $.each(data.error, function (index, value) {
                            message += value;
                        });
                        errorMsg(message);
                    } else {
                        successMsg(data.message);
                        patientid = $("#pid").val();
                        printVisitBill(patientid, data.id);
                    }
                    $("#formrevisitbtn").btnReset();
                },
                error: function () {
                    
                }
            });            
        }));
    });

    function printVisitBill(opdid) {
        var base_url = '<?php echo base_url() ?>';
        $.ajax({
            url: base_url + 'admin/payment/getVisitBill',
            type: 'POST',
            data: {visit_id:opdid},
            success: function (result) {
                $("#testdata").html(result);
                popup(result);
            }
        });
    }

    $(document).ready(function (e) {
        $("#formedit").on('submit', (function (e) {
            e.preventDefault();
            $.ajax({
                url: '<?php echo base_url(); ?>admin/patient/update',
                type: "POST",
                data: new FormData(this),
                dataType: 'json',
                contentType: false,
                cache: false,
                processData: false,
                success: function (data) {
                    if (data.status == "fail") {
                        var message = "";
                        $.each(data.error, function (index, value) {
                            message += value;
                        });
                        errorMsg(message);
                    } else {
                        successMsg(data.message);
                        window.location.reload(true);
                    }
                },
                error: function () {
                   
                }
            });
        }));
    });

    /**/
    function getRecord(id) {
        $.ajax({
            url: '<?php echo base_url(); ?>admin/patient/getDetails',
            type: "POST",
            data: {recordid: id},
            dataType: 'json',
            success: function (data) {
                $("#patientid").val(data.id);
                $("#patient_name").val(data.patient_name);
                $("#contact").val(data.mobileno);
                $("#email").val(data.email);
                $("#age").val(data.age);
                $("#bp").val(data.bp);
                $("#month").val(data.month);
                $("#guardian_name").val(data.guardian_name);
                $("#appointment_date").val(data.appointment_date);
                $("#case").val(data.case_type);
                $("#symptoms").val(data.symptoms);
                $("#known_allergies").val(data.known_allergies);
                $("#refference").val(data.refference);
                $("#amount").val(data.amount);
                $("#tax").val(data.tax);
                $("#opdid").val(data.opdid);
                $("#address").val(data.address);
                $("#note").val(data.note);
                $("#height").val(data.height);
                $("#weight").val(data.weight);
                $("#updateid").val(id);
                $('select[id="blood_group"] option[value="' + data.blood_group + '"]').attr("selected", "selected");
                $('select[id="gender"] option[value="' + data.gender + '"]').attr("selected", "selected");
                $('select[id="marital_status"] option[value="' + data.marital_status + '"]').attr("selected", "selected");
                $('select[id="consultant_doctor"] option[value="' + data.cons_doctor + '"]').attr("selected", "selected");
                $('select[id="payment_mode"] option[value="' + data.payment_mode + '"]').attr("selected", "selected");
                $('select[id="casualty"] option[value="' + data.casualty + '"]').attr("selected", "selected");
            },
        })
    }

    function holdModal(modalId) {
        (function(){var _el=document.getElementById(modalId); if(_el) bootstrap.Modal.getOrCreateInstance(_el,{backdrop:'static',keyboard:false}).show();})();;
    }

</script>

<script type="text/javascript"> 

$(".addpatient").click(function(){	
    $('.select2-selection__rendered').html("");
	$('#formadd').trigger("reset");
	$("#patientDetails").addClass('d-none');
});

$(".modalbtnpatient").click(function(){		
	$('#formaddpa').trigger("reset");
	$(".dropify-clear").trigger("click");
});

	$(document).on('change','.payment_mode',function(){
		var mode=$(this).val();
		if(mode == "Cheque"){
			$('.cheque_div').removeClass('d-none');
		}else{
			$('.cheque_div').addClass('d-none');
		}
	});

</script>

 <?php $this->load->view('admin/patient/patientaddmodal'); ?>