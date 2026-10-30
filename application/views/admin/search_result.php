<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
$genderList = $this->customlib->getGender();
$marital_status = $this->config->item('marital_status');
$bloodgroup = $this->config->item('bloodgroup');
?>
<div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header ptbnull">
                            <h3 class="card-title titlefix"> <?php echo form_error('Opd'); ?> 
                                <?php
                                echo $this->lang->line('patient_list');
                                ?>
                            </h3>                        
                        <div class="d-flex gap-2 align-items-center flex-wrap float-end">
                            <?php if ($this->rbac->hasPrivilege('patient', 'can_add')) { ?>
                                <a data-bs-toggle="modal" onclick="holdModal('myModalpa')" id="addp" class="btn btn-primary btn-sm newpatient"><i class="fa fa-plus"></i>  <?php echo $this->lang->line('add_new_patient'); ?></a> 
                                <?php
                            }
                            if ($this->rbac->hasPrivilege('patient_import', 'can_view')) {
                                ?>
                                <a data-bs-toggle="" href="<?php echo base_url() ?>admin/patient/import" id="addp" class="btn btn-primary btn-sm"><i class="fa fa-upload"></i>  <?php echo $this->lang->line('import_patient'); ?></a> 
                            <?php } ?>
                            <a  href="<?php echo base_url() ?>admin/admin/disablepatient" class="btn btn-primary btn-sm"><i class="fa fa-reorder"></i> <?php echo $this->lang->line('disabled_patient_list'); ?></a> 
                        </div>     
                    </div>
                    <div class="card-body">
                        <div class="download_label"><?php echo $this->lang->line('patient') . " " . $this->lang->line('list'); ?></div>
                        <table class="table table-striped table-bordered table-hover example"  >
                            <thead>
                                <tr>
                                    <th><?php echo $this->lang->line('patient_id'); ?></th>
                                    <th><?php echo $this->lang->line('patient_name'); ?></th>
                                    <th><?php echo $this->lang->line('age'); ?></th>
                                    <th><?php echo $this->lang->line('gender'); ?></th>
                                    <th><?php echo $this->lang->line('phone'); ?></th>
                                    <th><?php echo $this->lang->line('guardian_name'); ?></th>
                                    <th><?php echo $this->lang->line('address'); ?></th>
                                    <?php if (!empty($fields)) {
                                        foreach ($fields as $fields_key => $fields_value) {
                                         ?>
                                        <th><?php echo ucfirst($fields_value->name); ?></th>
                                    <?php } } ?>
                                    <th class=""><?php echo $this->lang->line('action'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                 <?php
                                if (empty($resultlist)) {
                                    ?>
                                    <?php
                                } else {
                                    foreach ($resultlist as $report) {
                                        $url = '#';
                                        $patient_type = $report['id'];
                                        if ($report['age']) {
                                            $age = $report['age'] . " ".$this->lang->line("years");
                                        } else {
                                            $age =  " 0 ".$this->lang->line("years");
                                        }

                                        if ($report['month']) {
                                            $month = ", ".$report['month'] . " ".$this->lang->line("month");
                                        } else {
                                            $month =", ". " 0 ".$this->lang->line("month");;
                                        }

                                        if ($patient_type) {
                                            ?>      
                                            <tr>
                                                <td><?php echo $report['id']; ?></td>
                                                <td>
                                                    <a href="#" data-bs-toggle="modal"  onclick="getpatientData('<?php echo $report['id'] ?>')" data-bs-target="" class="" title="" ><?php echo $report['patient_name'] ?>
                                                    </a>
                                                </td>
                                                <td><?php echo $age . " " . $month; ?></td>
                                                <td><?php echo $report['gender']; ?></td>
                                                <td><?php echo $report['mobileno']; ?></td>
                                                <td><?php echo $report['guardian_name']; ?></td>
                                                <td><?php echo $report['address']; ?></td>
                                                <?php 
                                                if (!empty($fields)) {
                                                    
                                                    foreach ($fields as $fields_key => $fields_value) {
                                                        $display_field = $report[$fields_value->name];
                                                      if($fields_value->type == "link"){
                                                        $display_field= "<a href=".$report[$fields_value->name]." target='_blank'>".$report[$fields_value->name]."</a>";
                                                         }
                                                            ?>
                                                        <td>
                                                            <?php echo $display_field; ?>
                                                                    
                                                        </td>
                                                        <?php
                                                    }
                                                }
                                                ?>
                                                <td class="">
                                                    <a href="#" data-bs-toggle="modal"  onclick="getpatientData('<?php echo $report['id'] ?>')" data-bs-target="" class="btn btn-secondary btn-sm" 
                                                       title="<?php echo $this->lang->line('show'); ?>" >
                                                        <i class="fa fa-reorder"></i>
                                                    </a>                                                   
                                                    <div class="btn-group">
                                                        <?php if (!empty($report['info'])) { ?>
                                                            <a href="#" class="btn btn-secondary btn-sm" data-bs-toggle="dropdown">
                                                                <i class="fa fa-ellipsis-v"></i>
                                                            </a>
                                                            <ul class="dropdown-menu dropdown-menu2" role="menu">
                                                                <?php
                                                                foreach ($report['info'] as $key => $value) {
                                                                    ?>
                                                                    <li><a href="<?php echo $report['url'][$key] ?>" >
                                                                            <?php echo $value; ?>
                                                                        </a>
                                                                    </li>
                                                                <?php } ?>
                                                            </ul>
                                                        <?php } ?>
                                                    </div>
                                                </td>
                                            </tr>
                                            <?php
                                        }
                                    }
                                    
                                    ?>
                                </tbody>
                            <?php } ?>
                        </table>
                    </div>
                </div>
            </div>  
        </div>

<div class="modal fade" id="myModal"  role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content modal-media-content">
            <div class="modal-header modal-media-header">
                <button type="button" class="close pt4" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('close'); ?>" data-bs-dismiss="modal" aria-label="Close"></button>
                <div class="modalicon"> 
                    <div id='edit_delete' class="pt4">
                        <?php if ($this->rbac->hasPrivilege('revisit', 'can_edit')) { ?>
                            <a href="#"  data-bs-toggle="tooltip" title="<?php echo $this->lang->line('edit'); ?>" ><i class="fa fa-pencil"></i></a>
                            <?php
                        }
                        if ($this->rbac->hasPrivilege('revisit', 'can_delete')) {
                            ?>
                            <a href="#" data-bs-toggle="tooltip" title="" title="<?php echo $this->lang->line('delete'); ?>"><i class="fa fa-trash"></i></a>
                        <?php } ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-4 col-3">
                        <div class="form-group15">
                            <?php if ($this->rbac->hasPrivilege('patient', 'can_add')) { ?>
                                <a data-bs-toggle="modal" id="add" onclick="holdModal('myModalpa')" class="modalbtnpatient"><i class="fa fa-plus"></i>  <span><?php echo $this->lang->line('new_patient'); ?></span></a> 

                            <?php } ?> 
                        </div>
                    </div><!--./col-sm-4--> 
                </div><!-- ./row -->                
            </div><!--./modal-header-->
            <div class="modal-body pt0 pb0">
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12">
                        <form id="formadd" accept-charset="utf-8" action="<?php echo base_url() . "admin/patient" ?>" enctype="multipart/form-data" method="post">
                            <input class="" name="id" type="hidden" id="patientid">
                            <div class="row row-eq">
                                <div class="col-lg-12 col-md-12 col-sm-12">
                                    <div class="row ptt10">
                                        <div class="col-md-9 col-sm-9 col-9" id="Myinfo">
                                            <ul class="singlelist">
                                                <li class="singlelist24bold">
                                                    <span id="patient_name"></span></li>
                                                <li>
                                                    <i class="fas fa-user-secret" data-bs-toggle="tooltip" data-placement="top" title="<?= $this->lang->line('patient') ?>"></i>
                                                    <span id="guardian"></span>
                                                </li>
                                            </ul>   
                                            <ul class="multilinelist">   
                                                <li>
                                                    <i class="fas fa-venus-mars" data-bs-toggle="tooltip" data-placement="top" title="<?= $this->lang->line('gender') ?>"></i>
                                                    <span id="genders" ></span>
                                                </li>
                                                <li>
                                                    <i class="fas fa-tint" data-bs-toggle="tooltip" data-placement="top" title="<?= $this->lang->line('blood_group') ?>"></i>
                                                    <span id="blood_group"></span>
                                                </li>
                                                <li>
                                                    <i class="fas fa-ring" data-bs-toggle="tooltip" data-placement="top" title="<?= $this->lang->line('marital_status') ?>"></i>
                                                    <span id="marital_status"></span>
                                                </li> 
                                            </ul>  
                                            <ul class="singlelist">  
                                                <li>
                                                    <i class="fas fa-hourglass-half" data-bs-toggle="tooltip" data-placement="top" title="<?= $this->lang->line('age') ?>"></i>
                                                    <span id="age"></span>
                                                </li>  
                                                <li>
                                                    <i class="fa fa-phone-square" data-bs-toggle="tooltip" data-placement="top" title="<?= $this->lang->line('phone') ?>"></i> 
                                                    <span id="contact"></span>
                                                </li>
                                                <li>
                                                    <i class="fa fa-envelope" data-bs-toggle="tooltip" data-placement="top" title="<?= $this->lang->line('email') ?>"></i>
                                                    <span id="email"></span>
                                                </li>
                                                <li>
                                                    <i class="fas fa-street-view" data-bs-toggle="tooltip" data-placement="top" title="<?= $this->lang->line('address') ?>"></i>
                                                    <span id="address" ></span>
                                                </li>
                                                <li>
                                                    <b><?php echo $this->lang->line('any_known_allergies') ?> </b> 
                                                    <span id="allergies" ></span>
                                                </li>
                                                <li>
                                                    <b><?php echo $this->lang->line('remarks') ?> </b> 
                                                    <span id="note"></span>
                                                </li>
                                                <li>
                                                    <b><?php echo $this->lang->line('insurance_id'); ?> </b> 
                                                    <span id="insurance_id" ></span>
                                                </li>
                                                <li>
                                                    <b><?php echo $this->lang->line('validity'); ?> </b> 
                                                    <span id="validity"></span>
                                                </li> 
                                                 <li id="field_data">
                                                    <b><span id="vcustom_name"></span></b>
                                                    <span id="vcustom_value"></span>
                                                </li>    
                                            </ul>                               
                                        </div><!-- ./col-md-9 -->
                                        <div class="col-md-3 col-sm-3 col-3"> 
                                            <div class="float-end">  
                                                <?php $file = "uploads/patient_images/no_image.png"; ?>        
                                                <img class="profile-user-img img-fluid" src="<?php echo $this->media_storage->getImageURL($file) ?>" id="image" alt="User profile picture">
                                            </div>           
                                        </div><!-- ./col-md-3 --> 
                                    </div>
                                </div><!--./col-md-8--> 
                            </div><!--./row-->  
                        </form>                       
                    </div><!--./col-md-12-->       
                </div><!--./row--> 
            </div>
        </div>
    </div>    
</div>

<div class="modal fade" id="editModal"  role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content modal-media-content">
            <div class="modal-header modal-media-header">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                <h4 class="card-title"> <?php echo $this->lang->line('patient_details'); ?></h4> 
            </div><!--./modal-header-->
            <div class="modal-body pt0 pb0">
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12">
                        <form id="formeditpa" accept-charset="utf-8" action="" enctype="multipart/form-data" method="post">
                            <input id="eupdateid" name="updateid" placeholder="" type="hidden" class="form-control"  value="" />
                            <div class="row row-eq h-vh-lg-100-100">
                                <div class="col-lg-12 col-md-12 col-sm-12">
                                    <div class="row ptt10">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label><?php echo $this->lang->line('name'); ?></label><small class="req"> *</small> 
                                                <input id="ename" name="name" placeholder="" type="text" class="form-control"  value="<?php echo set_value('name'); ?>" />
                                                <span class="text-danger"><?php echo form_error('name'); ?></span>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="mb-3">
                                                <label><?php echo $this->lang->line('guardian_name') ?></label>
                                                <input type="text" name="guardian_name"  id="eguardian_name"placeholder="" value="" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-sm-12">  
                                            <div class="row">  
                                                <div class="col-sm-3">
                                                    <div class="mb-3">
                                                        <label> <?php echo $this->lang->line('gender'); ?></label>
                                                        <select class="form-control" name="gender" id="egenders">
                                                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                                                            <?php
                                                            foreach ($genderList as $key => $value) {
                                                                ?>
                                                                <option value="<?php echo $key; ?>" <?php if (set_value('gender') == $key) echo "selected"; ?>><?php echo $value; ?></option>
                                                                <?php
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-sm-4">
                                                    <div class="mb-3">
                                                        <label for="dob"><?php echo $this->lang->line('date_of_birth'); ?></label> 
                                                        <input type="text" name="dob" id="ebirth_date" placeholder="" class="form-control date" /><?php echo set_value('dob'); ?>
                                                    </div>
                                                </div>
                                                <div class="col-sm-5" id="calculate">
                                                    <div class="mb-3">
                                                        <label><?php echo $this->lang->line('age') ?></label>
                                                        <div class="sh-clear">
                                                            <input type="text" placeholder="<?php echo $this->lang->line('year') ?>" name="age" id="eage_year" value="" class="form-control sh-print-left-40" >
                                                            <input type="text" id="eage_month" placeholder="<?php echo $this->lang->line('month') ?>" name="month" value="" class="form-control sh-print-left-56" >
                                                        </div>
                                                    </div>
                                                </div> 
                                            </div>  
                                        </div><!--./col-md-6-->  
                                        <div class="col-md-6 col-sm-12"> 
                                            <div class="row"> 
                                                <div class="col-md-3">
                                                    <div class="mb-3">
                                                        <label> <?php echo $this->lang->line('blood_group'); ?></label>
                                                        <select class="form-control" id="blood_groups" name="blood_group">
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
                                                <div class="col-sm-3">
                                                    <div class="mb-3">
                                                        <label for="pwd"><?php echo $this->lang->line('marital_status'); ?></label>
                                                        <select name="marital_status" id="marital_statuss" class="form-control">
                                                            <option value=""><?php echo $this->lang->line('select') ?></option>
                                                            <?php foreach ($marital_status as $key => $value) {
                                                                ?>
                                                                <option value="<?php echo $value; ?>" <?php if (set_value('marital_status') == $key) echo "selected"; ?>><?php echo $value; ?></option>
                                                            <?php } ?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="mb-3">
                                                        <label for="exampleInputFile">
                                                            <?php echo $this->lang->line('patient_photo'); ?>
                                                        </label>
                                                        <div>
                                                            <input class="filestyle form-control-file" type='file' name='file' id="exampleInputFile" size='20' data-height="26" data-default-file="<?php echo base_url() ?>uploads/patient_images/no_image.png" >
                                                        </div>
                                                        <span class="text-danger"><?php echo form_error('file'); ?></span>
                                                    </div>
                                                </div>
                                            </div> 
                                        </div><!--./col-md-6--> 
                                        <div class="col-sm-3">
                                            <div class="mb-3">
                                                <label for="pwd"><?php echo $this->lang->line('phone'); ?></label>
                                                <input id="emobileno" autocomplete="off" name="contact"  type="text" placeholder="" class="form-control"  value="<?php echo set_value('mobileno'); ?>" />
                                            </div>
                                        </div> 
                                        <div class="col-sm-3">
                                            <div class="mb-3">
                                                <label><?php echo $this->lang->line('email'); ?></label>
                                                <input type="text" placeholder="" id="eemail" value="<?php echo set_value('email'); ?>" name="email" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="mb-3">
                                                <label for="address"><?php echo $this->lang->line('address'); ?></label> 
                                                <input name="address" id="eaddress" placeholder="" class="form-control" /><?php echo set_value('address'); ?>
                                            </div> 
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="mb-3">
                                                <label for="pwd"><?php echo $this->lang->line('remarks'); ?></label> 
                                                <textarea name="note" id="enote" class="form-control" ><?php echo set_value('note'); ?></textarea>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="mb-3">
                                                <label for="email"><?php echo $this->lang->line('any_known_allergies'); ?></label> 
                                                <textarea name="known_allergies" id="eknown_allergies" placeholder="" class="form-control" ><?php echo set_value('address'); ?></textarea>
                                            </div> 
                                        </div>  
                                        <div class="col-sm-6">
                                            <div class="mb-3">
                                                <label for="insurance_id"><?php echo $this->lang->line('insurance_id'); ?></label> 
                                                <input name="insurance_id" placeholder="" class="form-control" /><?php echo set_value('insurance_id'); ?>
                                            </div> 
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="mb-3">
                                                <label for="validity"><?php echo $this->lang->line('validity'); ?></label> 
                                                <input name="validity" placeholder="" class="form-control date" /><?php echo set_value('validity'); ?>
                                            </div> 
                                        </div> 
                                        <div class="" id="customfield" >
                                        
                                        </div> 
                                    </div><!--./row--> 
                                </div><!--./col-md-8--> 
                            </div><!--./row--> 
                            <div class="row">            
                                <div class="card-footer">
                                    <div class="float-end">
                                        <button type="submit" id="formeditpabtn" data-loading-text="<?php echo $this->lang->line('processing') ?>" class="btn btn-info float-end"><?php echo $this->lang->line('save'); ?></button>
                                    </div>
                                </div>
                            </div><!--./row-->  
                        </form>                       
                    </div><!--./col-md-12-->       
                </div><!--./row--> 
            </div>
        </div>
    </div>    
</div>

<script type="text/javascript">   
    function showdate(value) {
        if (value == 'period') {
            $('#fromdate').show();
            $('#todate').show();
        } else {
            $('#fromdate').hide();
            $('#todate').hide();
        }
    }

    function holdModal(modalId) {
        (function(){var _el=document.getElementById(modalId);if(_el)bootstrap.Modal.getOrCreateInstance(_el,{backdrop:'static',keyboard:false}).show();})();;
    }

    function capitalizeFirstLetter(string) {
      return string.charAt(0).toUpperCase() + string.slice(1);
    }
    
    function getpatientData(id) {
        $.ajax({
            url: '<?php echo base_url(); ?>admin/patient/getpatientDetails',
            type: "POST",
            data: {id: id},
            dataType: 'json',
            success: function (data) {
                if (data.is_active == 'yes') {
                    var link = "<?php if ($this->rbac->hasPrivilege('enabled_disabled', 'can_view')) { ?><a href='#' data-bs-toggle='tooltip'  onclick='patient_deactive(" + id + ")' title='<?php echo $this->lang->line('disable'); ?>'><i class='fa fa-thumbs-o-down'></i></a><?php } ?>";
                } else {
                    var link = "<?php if ($this->rbac->hasPrivilege('enabled_disabled', 'can_view')) { ?><a href='#' data-bs-toggle='tooltip'  onclick='patient_active(" + id + ")' title='<?php echo $this->lang->line('enable'); ?>'><i class='fa fa-thumbs-o-up'></i></a> <?php } if ($this->rbac->hasPrivilege('patient', 'can_delete')) { ?><a href='#' data-bs-toggle='tooltip'  onclick='delete_record(" + id + ")' title='<?php echo $this->lang->line('delete'); ?>'><i class='fa fa-trash'></i></a> <?php } ?>";
                }
                var table_html = '';
                $.each(data.field_data, function (i, obj)
                {
                    if (obj.field_value == null) {
                        var field_value = "";
                    } else {
                        var field_value = obj.field_value;
                    }
                    var name = obj.name ;
                    table_html += "<b><span id='vcustom_name'>" + capitalizeFirstLetter(name) + "</span></b> <span id='vcustom_value'>" + field_value + "</span><br>";
                });

                $("#field_data").html(table_html);
                $("patientid").val(data.id);
                $("#patient_name").html(data.patient_name);
                $("#guardian").html(data.guardian_name);
                $("#patients_id").html(data.patient_unique_id);
                $("#genders").html(data.gender);
                $("#marital_status").html(data.marital_status);
                $("#contact").html(data.mobileno);
                $("#email").html(data.email);
                $("#address").html(data.address);
                $("#is_active").html(data.is_active);
                $("#blood_group").html(data.blood_group);
                if (data.age == "") {
                    $("#age").html("");
                } else {
                    if (data.age) {
                        var age = data.age + " " + "Years";
                    } else {
                        var age = '';
                    }
                    if (data.month) {
                        var month = data.month + " " + "Month";
                    } else {
                        var month = '';
                    }
                    if (data.dob) {
                        var dob = "(" + data.dob + ")";
                    } else {
                        var dob = '';
                    }

                    $("#age").html(age + "," + month + " " + dob);                    
                }

                $("#allergies").html(data.known_allergies);
                $("#insurance_id").html(data.insurance_id);
                $("#validity").html(data.insurance_validity);
                $("#note").html(data.note);
                $("#image").attr("src", '<?php echo base_url() ?>' + data.image);
                $('#edit_delete').html("<?php if ($this->rbac->hasPrivilege('patient', 'can_edit')) { ?><a href='#' onclick='editRecord(" + id + ")' data-bs-toggle='tooltip' title='<?php echo $this->lang->line('edit'); ?>' data-bs-target='' data-bs-toggle='modal'  title='<?php echo $this->lang->line('edit'); ?>'><i class='fa fa-pencil'></i></a><?php } ?> " + link + "");
                holdModal('myModal');
            },
        });
    }

    function editRecord(id) {
        $.ajax({
            url: '<?php echo base_url(); ?>admin/patient/getpatientDetails',
            type: "POST",
            data: {id: id},
            dataType: 'json',
            success: function (data) {
                $("#eupdateid").val(data.id);
                $('#customfield').html(data.custom_fields_value);
                $("#ename").val(data.patient_name);
                $("#eguardian_name").val(data.guardian_name);
                $("#emobileno").val(data.mobileno);
                $("#eemail").val(data.email);
                $("#eaddress").val(data.address);
                $("#eage_year").val(data.age);
                $("#eage_month").val(data.month);
                $("#ebirth_date").val(data.dob);
                $("#enote").val(data.note);
                $("#exampleInputFile").attr("data-default-file", '<?php echo base_url() ?>' + data.image);
                $(".dropify-render").find("img").attr("src", '<?php echo base_url() ?>' + data.image);
                $("#eknown_allergies").val(data.known_allergies);
                $('select[id="blood_groups"] option[value="' + data.blood_group + '"]').attr("selected", "selected");
                $('select[id="egenders"] option[value="' + data.gender + '"]').attr("selected", "selected");
                $('select[id="marital_statuss"] option[value="' + data.marital_status + '"]').attr("selected", "selected");
                $("#insurance_id").val(data.insurance_id);
                $("#insurance_validity").val(data.insurance_validity);
                shModal('myModal').hide();
                holdModal('editModal');
            },
        });
    }

    $(document).ready(function (e) {
        $("#formeditpa").on('submit', (function (e) {
            $("#formeditpabtn").btnLoading();
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
                    $("#formeditpabtn").btnReset();
                },
                error: function () {
                }
            });
        }));
    });
	
    function delete_record(id) {
        if (confirm(<?php echo "'" . $this->lang->line('delete_confirm') . "'"; ?>)) {
            $.ajax({
                url: '<?php echo base_url(); ?>admin/patient/deletePatient',
                type: "POST",
                data: {delid: id},
                dataType: 'json',
                success: function (data) {
                    successMsg(<?php echo "'" . $this->lang->line('delete_message') . "'"; ?>);
                    window.location.reload(true);
                }
            })
        }
    }

    function patient_deactive(id) {
        if (confirm(<?php echo "'" . $this->lang->line('are_you_sure_to_deactivate_account') . "'"; ?>)) {
            $.ajax({
                url: '<?php echo base_url(); ?>admin/patient/deactivePatient',
                type: "POST",
                data: {id: id},
                dataType: 'json',
                success: function (data) {
                    successMsg(<?php echo "'" . $this->lang->line('record_deactivate') . "'"; ?>);                    
                    window.getpatientData(id);
                }
            })
        }
    }

    function CalculateAgeInQCe(DOB, txtAge, Txndate) {
        if (DOB.value != '') {
            now = new Date(Txndate)
            var txtValue = DOB;
            if (txtValue != null)
                dob = txtValue.split('/');
            if (dob.length === 3) {
                born = new Date(dob[2], dob[1] * 1 - 1, dob[0]);
                if (now.getMonth() == born.getMonth() && now.getDate() == born.getDate()) {
                    age = now.getFullYear() - born.getFullYear();
                } else {
                    age = Math.floor((now.getTime() - born.getTime()) / (365.25 * 24 * 60 * 60 * 1000));
                }
                if (isNaN(age) || age < 0) {
                    
                    
                } else {
                    if (now.getMonth() > born.getMonth()) {
                        var calmonth = now.getMonth() - born.getMonth();
                    } else {
                        var calmonth = born.getMonth() - now.getMonth();
                    }                    
                    $("#eage_year").val(age);
                    $("#eage_month").val(calmonth);
                    return age;
                    
                }
            }
        }
    }
	
    $(document).ready(function () {
        $("#ebirth_date").change(function () {
            var mdate = $("#ebirth_date").val().toString();
            var yearThen = parseInt(mdate.substring(6, 10), 10);
            var dayThen = parseInt(mdate.substring(0, 2), 10);
            var monthThen = parseInt(mdate.substring(3, 5), 10);
            var DOB = dayThen + "/" + monthThen + "/" + yearThen;          
            CalculateAgeInQCe(DOB, '', new Date());
        });
    });

    function patient_active(id) {
        if (confirm(<?php echo "'" . $this->lang->line('are_you_sure_to_active_account') . "'"; ?>)) {
            $.ajax({
                url: '<?php echo base_url(); ?>admin/patient/activePatient',
                type: "POST",
                data: {activeid: id},
                dataType: 'json',
                success: function (data) {
                    successMsg(<?php echo "'" . $this->lang->line('active_message') . "'"; ?>);
                    window.getpatientData(id);
                }
            })
        }
    }
</script>
<script type="text/javascript">
$(".newpatient").click(function(){	
	$('#formaddpa').trigger("reset");
	$(".dropify-clear").trigger("click");
});	

$(".modalbtnpatient").click(function(){	
	$('#formaddpa').trigger("reset");
	$(".dropify-clear").trigger("click");
});
</script>
<?php $this->load->view('admin/patient/patientaddmodal') ?>