<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
?>
<div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><?php echo $this->lang->line('import_staff'); ?></h3>
                        <div class="float-end ms-auto d-flex gap-1">                            
                            <a href="<?php echo site_url('admin/staff/exportformat') ?>">
                                <button class="btn btn-primary btn-sm"><i class="fa fa-download"></i> <?php echo $this->lang->line('download_sample_import_file'); ?></button>
                            </a>
                        </div>
                    </div>
                    <div class="card-body">      
                        <?php  if ($this->session->flashdata('msg')) { ?> <div>  <?php echo $this->session->flashdata('msg') ?> </div> <?php $this->session->unset_userdata('msg'); }   ?>
                        <br/>           
                        1. <?php echo $this->lang->line('import_staff_step1'); ?><br/>
                        2. <?php echo $this->lang->line('import_staff_step2'); ?><br/>

                        <hr/></div>
                    <div class="card-body table-responsive" style="overflow-x:auto;">
                        <table class="table table-striped table-bordered table-hover" id="sampledata">
                            <thead>
                                <tr>
                                    <?php
                                    foreach ($field as $key => $value) {
                                        if ($value == 'staff_id' || $value == 'first_name' || $value == 'email' || $value == 'gender' || $value == 'date_of_birth') {
                                            $req = "<span class='text-red'>*</span>";
                                        } else {
                                            $req = "";
                                        }
                                        ?>    
                                        <th><?php if ($value == 'first_name' || $value == 'last_name' || $value=='emergency_contact_number' || $value=='permanent_address' || $value=='qualification' || $value=='note' || $value=='work_experience') {
                                           echo "<span>" . $this->lang->line('staff_'.$value) . "</span>" . $req;
                                        } else {
                                           echo "<span>" . $this->lang->line($value) . "</span>" . $req;
                                        }  ?></th>
                                    <?php } ?>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <?php foreach ($field as $key => $value) {
                                        ?>    
                                        <td><?php echo "XYZ" ?></td>
                                    <?php } ?>
                                </tr>
                            </tbody>
                        </table>        
                    </div>
                    <hr/>
                    <form action="<?php echo site_url('admin/staff/import') ?>" id="employeeform" name="employeeform" method="post" enctype="multipart/form-data">
                        <div class="card-body">
                            <?php echo $this->customlib->getCSRF(); ?>
                            <div class="row">                                     
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="exampleInputEmail1"><?php echo $this->lang->line('role'); ?></label><small class="req"> *</small>
                                        <select  id="role" name="role" class="form-control" >
                                            <option value=""   ><?php echo $this->lang->line('select'); ?></option>
                                            <?php
                                            foreach ($roles as $key => $role) {
                                                ?>
                                                <option value="<?php echo $role['id'] ?>" <?php echo set_select('role', $role['id'], set_value('role')); ?>><?php echo $role["name"] ?></option>
                                            <?php }
                                            ?>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('role'); ?></span>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="exampleInputEmail1"><?php echo $this->lang->line('designation'); ?></label>
                                        <select id="designation" name="designation" placeholder="" type="text" class="form-control">
                                            <option value="select"><?php echo $this->lang->line('select') ?></option>
                                            <?php foreach ($designation as $key => $value) {
                                                ?>
                                                <option value="<?php echo $value["id"] ?>" <?php echo set_select('designation', $value['id'], set_value('designation')); ?> ><?php echo $value["designation"] ?></option>
                                            <?php }
                                            ?>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('designation'); ?></span>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="exampleInputEmail1"><?php echo $this->lang->line('department'); ?></label>
                                        <select id="department" name="department" placeholder="" type="text" class="form-control">
                                            <option value="select"><?php echo $this->lang->line('select') ?></option>
                                            <?php foreach ($department as $key => $value) {
                                                ?>
                                                <option value="<?php echo $value["id"] ?>" <?php echo set_select('department', $value['id'], set_value('department')); ?>><?php echo $value["department_name"] ?></option>
                                            <?php }
                                            ?>
                                        </select> 
                                        <span class="text-danger"><?php echo form_error('department'); ?></span>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="exampleInputFile"><?php echo $this->lang->line('select_csv_file'); ?></label><small class="req"> *</small>
                                        <div><input class="filestyle form-control" type='file' name='file' id="file" size='20' />
                                            <span class="text-danger"><?php echo form_error('file'); ?></span></div>
                                    </div></div>
                                <div class="col-md-6 pt20">
                                    <button type="submit" class="btn btn-info float-end"><?php echo $this->lang->line('import_staff'); ?></button>
                                </div>
							</div>
                        </div>
                    </form>
                    <div>
                    </div>
                </div>