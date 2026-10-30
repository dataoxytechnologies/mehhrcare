<div class="row">
            <div class="col-md-12">
                <div class="card" >
                    <div class="card-header">
                        <h3 class="card-title"><?php echo $this->lang->line('assign_permission'); ?> (<?php echo $staff['name'] ?>) </h3>
                    </div>
                    <form id="form1" action="<?php echo site_url('admin/staff/permission/' . $staff['id']) ?>"  id="employeeform" name="employeeform" method="post" accept-charset="utf-8">
                        <div class="card-body">
                            <?php if ($this->session->flashdata('msg')) { ?>
                                <?php echo $this->session->flashdata('msg') ?>
                            <?php } ?>
                            <?php
                            if (isset($error_message)) {
                                echo "<div class='alert alert-danger'>" . $error_message . "</div>";
                            }
                            ?>      
                            <?php echo $this->customlib->getCSRF(); ?>  
                            <input type="hidden" name="staff_id" value="<?php echo $staff['id'] ?>"/>
                            <div class="mb-3">
                                <label class="col-lg-2 form-label"><?php echo $this->lang->line('permisssion'); ?></label>
                                <div class="col-lg-10">
                                    <?php
                                    foreach ($userpermission as $userpermission_key => $userpermission_value) {
                                        if ($userpermission_value->user_permissions_id == 1) {
                                            ?>
                                            <input type="hidden" name="prev_array[]" value="<?php echo $userpermission_value->id ?>">
                                            <?php
                                        }
                                        ?>
                                        <label class="form-check form-check-inline">
                                            <input type="checkbox" name="module_perm[]" value="<?php echo $userpermission_value->id ?>" <?php echo set_checkbox('module_perm[]', $userpermission_value->id, ($userpermission_value->user_permissions_id == 1) ? TRUE : FALSE) ?>> <?php echo $userpermission_value->name; ?>
                                        </label>
                                        <?php
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-info float-end"><?php echo $this->lang->line('save'); ?></button>
                        </div>
                    </form>
                </div>
            </div>        
		</div>