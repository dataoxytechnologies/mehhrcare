<?php $currency_symbol = $this->customlib->getHospitalCurrencyFormat(); ?>
<!-- Content Wrapper. Contains page content -->
<div class="row">
            <?php
            if ($this->rbac->hasPrivilege('custom_fields', 'can_view')) {
                ?>
                <div class="col-md-4">
                    <!-- Horizontal Form -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title"><?php echo $this->lang->line('add_custom_field'); ?></h3>
                        </div><!-- /.card-header -->
                        <form id="form1" action="<?php echo site_url('admin/customfield') ?>"  id="employeeform" name="employeeform" method="post" accept-charset="utf-8">
                            <div class="card-body">
                                <?php if ($this->session->flashdata('msg')) { ?>
                                    <?php echo $this->session->flashdata('msg');
                                        $this->session->unset_userdata('msg');
                                    ?>
                                <?php } ?>
                                <?php
                                if (isset($error_message)) {
                                    echo "<div class='alert alert-danger mb0'>" . $error_message . "</div>";
                                }
                                ?>
                                <?php echo $this->customlib->getCSRF(); ?>

                                <div class="mb-3">
                                    <label for="exampleInputEmail1"><?php echo $this->lang->line('field_belongs_to'); ?></label> <small class="req">*</small>
                                    <select autofocus="" id="belong_to" name="belong_to" class="form-control select2" >
                                        <option value=""><?php echo $this->lang->line('select'); ?></option>
                                        <?php
                                        $count = 0; foreach ($custom_field_table as $custom_field_table_key => $custom_field_table_value) {
                                            ?>
                                            <option value="<?php echo $custom_field_table_key; ?>" <?php echo set_select('belong_to', $custom_field_table_key, set_value('belong_to')); ?>><?php echo $custom_field_table_value; ?></option>

                                            <?php
                                            $count++;
                                        }
                                        ?>
                                    </select>
                                    <span class="text-danger"><?php echo form_error('belong_to'); ?></span>
                                </div>
                                <div class="mb-3">
                                    <label for="exampleInputEmail1"><?php echo $this->lang->line('field_type'); ?></label> <small class="req">*</small>
                                    <select autofocus="" id="type" name="type" class="form-control select2" >
                                        <option value=""><?php echo $this->lang->line('select'); ?></option>
                                        <?php
                                        foreach ($custom_fields_list as $custom_fields_list_key => $custom_fields_list_value) {
                                            ?>
                                            <option value="<?php echo $custom_fields_list_key; ?>" <?php echo set_select('type', $custom_fields_list_key, set_value('type')); ?>><?php echo $custom_fields_list_value; ?></option>

                                            <?php
                                            $count++;
                                        }
                                        ?>
                                    </select>
                                    <span class="text-danger"><?php echo form_error('type'); ?></span>
                                </div>
                                <div class="mb-3">
                                    <label for="exampleInputEmail1"><?php echo $this->lang->line('field_name'); ?></label> <small class="req">*</small>
                                    <input id="name" name="name" placeholder="" type="text" class="form-control" value="<?php echo set_value('name'); ?>" />
                                    <span class="text-danger"><?php echo form_error('name'); ?></span>
                                </div>
                                <div class="mb-3">
                                    <label for="exampleInputEmail1"><?php echo $this->lang->line('grid_bootstrap'); ?></label>
                                    <div class="input-group">
                                        <span class="input-group-text">col-md-</span>
                                        <input type="number" min="1" max="12" class="form-control" name="column" id="column" value="12" aria-invalid="false">
                                    </div>
                                    <span class="text-danger"><?php echo form_error('column'); ?></span>
                                </div>
                                <div class="mb-3">
                                    <label for="exampleInputEmail1"><?php echo $this->lang->line('field_values_separate_by_comma'); ?></label>
                                    <textarea class="form-control" name="field_values"><?php echo set_value('field_values') ?></textarea>
                                    <span class="text-danger"><?php echo form_error('field_values'); ?></span>
                                </div>                                
                            <div class="row">                            
                                <div class="col-md-6">
                                    <label><?php echo $this->lang->line('validation'); ?></label>                            
                                </div>
                                <div class="col-md-6">                                    
                                    <label class="form-check form-check-inline">
                                        <input type="checkbox" class="content_available" name="validation" value="1" <?php echo set_checkbox('display_tbl', '1', (set_value('validation') == 1) ? true : false); ?>>
                                        <?php echo $this->lang->line('required'); ?>
                                    </label>                                        
                                </div>
                            </div>
                             <br/>
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="form-label"><?php echo $this->lang->line('visibility') ?></label>
                                </div>                               
                            </div>
                            <div class="row">                           
                                <div class="col-md-6">                                      
                                    <label class="form-check form-check-inline">
                                        <input type="checkbox" class="content_available" name="display_tbl" value="1" <?php echo set_checkbox('display_tbl', '1', (set_value('display_tbl') == 1) ? true : false); ?>>
                                        <?php echo $this->lang->line('on_table'); ?>
                                    </label> 
                                </div> 
                                <div class="col-md-6"> 
                                    <label class="form-check form-check-inline">
                                        <input type="checkbox" class="content_available" name="display_print" value="1" <?php echo set_checkbox('display_print', '1', (set_value('display_print') == 1) ? true : false); ?>>
                                        <?php echo $this->lang->line('on_print'); ?>
                                    </label> 
                                </div>                                  
                                <div class="col-md-6">     
                                    <label class="form-check form-check-inline">
                                        <input type="checkbox" class="content_available" name="display_report" value="1" <?php echo set_checkbox('display_report', '1', (set_value('display_report') == 1) ? true : false); ?>>
                                        <?php echo $this->lang->line('on_report'); ?>
                                    </label>    
                                </div>    
                                <div class="col-md-6">   
                                    <label class="form-check form-check-inline">
                                        <input type="checkbox" class="content_available" name="display_patient" value="1" <?php echo set_checkbox('display_patient', '1', (set_value('display_patient_panel') == 1) ? true : false); ?>>
                                        <?php echo $this->lang->line('on_patient_panel'); ?>
                                    </label> 
                                </div>                               
                            </div>                    
                           
                        </div><!-- /.card-body -->
                            <div class="card-footer">
                                <button type="submit" class="btn btn-info float-end"><?php echo $this->lang->line('save'); ?></button>
                            </div>
                        </form>
                    </div>

                </div><!--/.col (right) -->
                <!-- left column -->
            <?php } ?>
            <div class="col-md-<?php
            if ($this->rbac->hasPrivilege('custom_fields', 'can_view')) {
                echo "8";
            } else {
                echo "12";
            }
            ?>">
                <!-- general form elements -->
                <div class="card">
                    <div class="card-header ptbnull">
                        <h3 class="card-title titlefix"><?php echo $this->lang->line('custom_field_list'); ?></h3>
                    </div><!-- /.card-header -->
                    <div class="card-body">
                        <div id="fade"></div>
                        <div id="modal">
                        </div>
                        <?php
                        if (!empty($customfields)) {
                            ?>
                            <div id="accordion" class="panel-group">
                                <?php
                                foreach ($custom_field_table as $custom_field_table_key => $custom_field_table_value) {
                                    ?>
                                    <div class="card">
                                        <div class="card-header">
                                            <h4 class="panel-title">
                                                <a class="collapsed" role="button" data-bs-toggle="collapse" data-parent="#accordion" href="#collapse<?php echo $custom_field_table_key ?>" aria-expanded="false" aria-controls="collapse<?php echo $custom_field_table_key ?>">
                                                    <i class="more-less fa fa-plus"></i>
                                                    <?php echo $custom_field_table_value; ?>
                                                </a>
                                            </h4>
                                        </div>
                                        <div id="collapse<?php echo $custom_field_table_key ?>" class="panel-collapse collapse">
                                            <div class="card-body">
                                                <?php
                                                $records_fields = isset($customfields[$custom_field_table_key]) ? $customfields[$custom_field_table_key] : array();
                                                if (!empty($records_fields)) {
                                                    ?>
                                                    <ul class="sortable-item ui-sortable list-group" data-record_name="<?php echo $custom_field_table_key; ?>">
                                                        <?php
                                                        foreach ($records_fields as $records_fields_key => $records_fields_value) {
                                                            ?>
                                                            <li id="<?php echo $records_fields_value['id']; ?>" class="list-group-item-sort text-start">
                                                                <span class="sort-action sort-float-end">
                                                                    <a href="<?php echo site_url('admin/customfield/edit/' . $records_fields_value['id']); ?>" class="btn btn-sm" data-bs-toggle="tooltip"
                                                                       title="<?php echo $this->lang->line('edit'); ?>"><i class="fa fa-pencil"></i></a>
                                                                    <a href="#" data-href="<?php echo site_url('admin/customfield/delete/' . $records_fields_value['id']); ?>" class="btn btn-sm cf-delete-btn" data-bs-toggle="tooltip"
                                                                       title="<?php echo $this->lang->line('delete'); ?>"><i class="fa fa-remove"></i></a>
                                                                </span> <i class="fa fa-arrows"></i> <?php
                                                                echo ($records_fields_value['name']);
                                                                ?>
                                                            </li>
                                                            <?php
                                                        }
                                                        ?>

                                                        </ul>
                                                        <?php
                                                    } else {
                                                        ?>
                                                        <div class="alert alert-danger mb0">
                                                            <?php echo $this->lang->line('no_record_found') ?>
                                                        </div>
                                                        <?php
                                                    }
                                                    ?>
                                            </div>
                                        </div>
                                    </div>
                                    <?php
                                }
                                ?>
                            </div>
                            <?php
                        }
                        ?>
                    </div><!-- /.card-body -->
                </div>
            </div><!--/.col (left) -->
        </div>
    

 
<script type="text/javascript">
     $(function () {
        $('.select2').select2();

        $(document).on('click', '.cf-delete-btn', function (e) {
            e.preventDefault();
            var deleteUrl = $(this).data('href');
            Swal.fire({
                title: '<?php echo $this->lang->line('delete_confirm'); ?>',
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<?php echo $this->lang->line('delete'); ?>',
                cancelButtonText: '<?php echo $this->lang->line('cancel'); ?>'
            }).then(function (result) {
                if (result.value) {
                    window.location.href = deleteUrl;
                }
            });
        });
    });
    function toggleIcon(e) {
        $(e.target)
                .prev('.card-header')
                .find(".more-less")
                .toggleClass('fa-plus fa-minus');
    }
    $('.panel-group').on('hidden.bs.collapse', toggleIcon);
    $('.panel-group').on('shown.bs.collapse', toggleIcon);

    $('.sortable-item').sortable({
        connectWith: '.sortable-item',
        update: function (event, ui) {
            $(this).closest('div.card-body').addClass("sdfdsfs");
            var record_name = $(this).data('record_name');
            var data = $(this).sortable('toArray');
            $.ajax({
                type: "POST",
                url: base_url + "admin/customfield/updateorder",
                data: {"items": data, "belong_to": record_name},
                dataType: "json",
                beforeSend: function () {
                    $('#fade,#modal').css({'display': 'block'});
                },
                success: function (data) {
                    if (data.status) {
                        successMsg(data.msg);
                    } else {
                        errorMsg(data.msg);
                    }
                    $('#fade,#modal').css({'display': 'none'});
                },
                error: function (xhr) { // if error occured
                    alert("Error occured.please try again");
                    $('#fade,#modal').css({'display': 'none'});
                },
                complete: function () {
                    $('#fade,#modal').css({'display': 'none'});
                }
            });
        }
    });
</script>