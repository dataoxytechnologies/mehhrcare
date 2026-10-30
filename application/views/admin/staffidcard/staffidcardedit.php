     <!-- Content Wrapper. Contains page content -->
<div class="row">
            <?php
            if ($this->rbac->hasPrivilege('staff_id_card', 'can_add') || $this->rbac->hasPrivilege('staff_id_card', 'can_edit')) {
                ?>
                <div class="col-md-4">
                    <!-- Horizontal Form -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title"><?php echo $this->lang->line('edit_staff_id_card'); ?></h3>
                        </div><!-- /.card-header -->
                        <!-- form start -->
                        <form id="form1" enctype="multipart/form-data" action="<?php echo site_url('admin/staffidcard/edit/') . $editstaffidcard[0]->id ?>"  id="certificateform" name="certificateform" method="post" accept-charset="utf-8">
                            <div class="card-body">
                                <?php if ($this->session->flashdata('msg')) { ?>
                                    <?php echo $this->session->flashdata('msg');
                                    $this->session->unset_userdata('msg');
                                     ?>
                                <?php } ?>
                                <?php
                                if (isset($error_message)) {
                                    echo "<div class='alert alert-danger'>" . $error_message . "</div>";
                                }
                                ?>
                                <?php echo $this->customlib->getCSRF(); ?>
                                <input type="hidden" name="id" value="<?php echo set_value('id', $editstaffidcard[0]->id); ?>" >
                                <div class="mb-3">
                                    <label for="exampleInputEmail1"><?php echo $this->lang->line('background_image'); ?></label>
                                    <input id="documents" value="<?php echo $editstaffidcard[0]->background; ?>" placeholder="" type="file" class="filestyle form-control" data-height="40"  name="background_image">
                                    <span class="text-danger"><?php echo form_error('background_image'); ?></span>
                                    <?php if (!empty($editstaffidcard[0]->background)) { ?>
                                        <div class="remove_background_image d-flex pt5"> 
                                            <a class="uploadclosebtn" title="<?php echo $this->lang->line('delete'); ?>"><i class="fa fa-trash-o pe5 cursor-pointer" onclick="remove_background_image()"></i></a><?php echo $editstaffidcard[0]->background; ?>
                                        </div>     
                                    <?php }?>
                                </div>
                                <div class="mb-3">
                                    <label for="exampleInputEmail1"><?php echo $this->lang->line('logo'); ?></label>
                                    <input id="logo_img" placeholder="" value="<?php echo $editstaffidcard[0]->logo; ?>" type="file" class="filestyle form-control" data-height="40"  name="logo_img">
                                    <span class="text-danger"><?php echo form_error('logo_img'); ?></span>
                                    <?php if (!empty($editstaffidcard[0]->logo)) { ?>
                                        <div class="remove_logo d-flex pt5"> 
                                            <a class="uploadclosebtn" title="<?php echo $this->lang->line('delete'); ?>"><i class="fa fa-trash-o pe5 cursor-pointer" onclick="remove_logo()"></i></a><?php echo $editstaffidcard[0]->logo; ?>
                                        </div>     
                                    <?php }?>
                                </div>
                                <div class="mb-3">
                                    <label for="exampleInputEmail1"><?php echo $this->lang->line('signature'); ?></label>
                                    <input id="sign_image" placeholder="" value="<?php echo $editstaffidcard[0]->sign_image; ?>" type="file" class="filestyle form-control" data-height="40"  name="sign_image">
                                    <span class="text-danger"><?php echo form_error('sign_image'); ?></span>
                                    <?php if (!empty($editstaffidcard[0]->sign_image)) { ?>
                                        <div class="remove_sign_image d-flex pt5"> 
                                            <a class="uploadclosebtn" title="<?php echo $this->lang->line('delete'); ?>"><i class="fa fa-trash-o pe5 cursor-pointer" onclick="remove_sign_image()"></i></a><?php echo $editstaffidcard[0]->sign_image; ?>
                                        </div>     
                                    <?php }?>
                                </div>
                                <div class="mb-3">
                                    <label for="exampleInputEmail1"><?php echo $this->lang->line('hospital_name'); ?></label><small class="req"> *</small>
                                    <input autofocus="" id="hospital_name" name="hospital_name" placeholder="" type="text" class="form-control" value="<?php echo set_value('hospital_name', $editstaffidcard[0]->hospital_name); ?>" />
                                    <span class="text-danger"><?php echo form_error('hospital_name'); ?></span>
                                </div>
                                <div class="mb-3">
                                    <label for="exampleInputEmail1"><?php echo $this->lang->line('address_phone_email'); ?></label>
                                    <textarea class="form-control" id="address" name="address" placeholder="" rows="3" placeholder=""><?php echo set_value('address', $editstaffidcard[0]->hospital_address); ?></textarea>
                                    <span class="text-danger"><?php echo form_error('address'); ?></span>
                                </div>
                                <div class="mb-3">
                                    <label for="exampleInputEmail1"><?php echo $this->lang->line('id_card_title'); ?></label><small class="req"> *</small>
                                    <input id="title" name="title" placeholder="" type="text" class="form-control" value="<?php echo set_value('title', $editstaffidcard[0]->title); ?>" />
                                    <span class="text-danger"><?php echo form_error('title'); ?></span>
                                </div>
                                <div class="mb-3">
                                    <label for="exampleInputEmail1"><?php echo $this->lang->line('header_color'); ?></label>
                                    <input id="header_color" name="header_color" placeholder="" type="text" class="form-control my-colorpicker1" value="<?php echo set_value('header_color', $editstaffidcard[0]->header_color); ?>" />
                                </div>
                                <div class="mb-3 d-flex align-items-center justify-content-between">
                                    <label class="form-label"><?php echo $this->lang->line('staff_name'); ?></label>
                                    <div class="form-check form-switch d-inline-flex m-0">
                                        <input id="enable_name" name="is_active_staff_name" type="checkbox" role="switch" class="form-check-input chk" value="1" <?php echo set_checkbox('is_active_staff_name', '1', (set_value('is_active_staff_name', $editstaffidcard[0]->enable_name) == 1) ? TRUE : FALSE); ?>>
                                    </div>
                                </div>
                                <div class="mb-3 d-flex align-items-center justify-content-between">
                                    <label class="form-label"><?php echo $this->lang->line('staff_id'); ?></label>
                                    <div class="form-check form-switch d-inline-flex m-0">
                                        <input id="enable_staff_id" name="is_active_staff_id" type="checkbox" role="switch" class="form-check-input chk" value="1" <?php echo set_checkbox('is_active_staff_id', '1', (set_value('is_active_staff_id', $editstaffidcard[0]->enable_staff_id) == 1) ? TRUE : FALSE); ?>>
                                    </div>
                                </div>
                                <div class="mb-3 d-flex align-items-center justify-content-between">
                                    <label class="form-label"><?php echo $this->lang->line('designation'); ?></label>
                                    <div class="form-check form-switch d-inline-flex m-0">
                                        <input id="enable_designation" name="is_active_designation" type="checkbox" role="switch" class="form-check-input chk" value="1" <?php echo set_checkbox('is_active_staff_id', '1', (set_value('is_active_designation', $editstaffidcard[0]->enable_designation) == 1) ? TRUE : FALSE); ?>>
                                    </div>
                                </div>
                                <div class="mb-3 d-flex align-items-center justify-content-between">
                                    <label class="form-label"><?php echo $this->lang->line('department'); ?></label>
                                    <div class="form-check form-switch d-inline-flex m-0">
                                        <input id="enable_department" name="is_active_department" type="checkbox" role="switch" class="form-check-input chk" value="1" <?php echo set_checkbox('is_active_department', '1', (set_value('is_active_department', $editstaffidcard[0]->enable_staff_department) == 1) ? TRUE : FALSE); ?>>
                                    </div>
                                </div>
                                <div class="mb-3 d-flex align-items-center justify-content-between">
                                    <label class="form-label"><?php echo $this->lang->line('father_name'); ?></label>
                                    <div class="form-check form-switch d-inline-flex m-0">
                                        <input id="enable_fathers_name" name="is_active_staff_father_name" type="checkbox" role="switch" class="form-check-input chk" value="1" <?php echo set_checkbox('is_active_staff_father_name', '1', (set_value('is_active_staff_father_name', $editstaffidcard[0]->enable_fathers_name) == 1) ? TRUE : FALSE); ?>>
                                    </div>
                                </div>
                                <div class="mb-3 d-flex align-items-center justify-content-between">
                                    <label class="form-label"><?php echo $this->lang->line('mother_name'); ?></label>
                                    <div class="form-check form-switch d-inline-flex m-0">
                                        <input id="enable_mother_name" name="is_active_staff_mother_name" type="checkbox" role="switch" class="form-check-input chk" value="1" <?php echo set_checkbox('is_active_staff_mother_name', '1', (set_value('is_active_staff_mother_name', $editstaffidcard[0]->enable_mothers_name) == 1) ? TRUE : FALSE); ?>>
                                    </div>
                                </div>
                                <div class="mb-3 d-flex align-items-center justify-content-between">
                                    <label class="form-label"><?php echo $this->lang->line('date_of_joining'); ?></label>
                                    <div class="form-check form-switch d-inline-flex m-0">
                                        <input id="enable_date_of_joining" name="is_active_date_of_joining" type="checkbox" role="switch" class="form-check-input chk" value="1" <?php echo set_checkbox('is_active_date_of_joining', '1', (set_value('is_active_date_of_joining', $editstaffidcard[0]->enable_date_of_joining) == 1) ? TRUE : FALSE); ?>>
                                    </div>
                                </div>
                                <div class="mb-3 d-flex align-items-center justify-content-between">
                                    <label class="form-label"><?php echo $this->lang->line('current_address'); ?></label>
                                    <div class="form-check form-switch d-inline-flex m-0">
                                        <input id="enable_staff_permanent_address" name="is_active_staff_permanent_address" type="checkbox" role="switch" class="form-check-input chk" value="1" <?php echo set_checkbox('is_active_staff_permanent_address', '1', (set_value('is_active_staff_permanent_address', $editstaffidcard[0]->enable_permanent_address) == 1) ? TRUE : FALSE); ?>>
                                    </div>
                                </div>
                                <div class="mb-3 d-flex align-items-center justify-content-between">
                                    <label class="form-label"><?php echo $this->lang->line('phone'); ?></label>
                                    <div class="form-check form-switch d-inline-flex m-0">
                                        <input id="enable_staff_phone" name="is_active_staff_phone" type="checkbox" role="switch" class="form-check-input chk" value="1" <?php echo set_checkbox('is_active_staff_phone', '1', (set_value('is_active_staff_phone', $editstaffidcard[0]->enable_staff_phone) == 1) ? TRUE : FALSE); ?>>
                                    </div>
                                </div>
                                <div class="mb-3 d-flex align-items-center justify-content-between">
                                    <label class="form-label"><?php echo $this->lang->line('date_of_birth'); ?></label>
                                    <div class="form-check form-switch d-inline-flex m-0">
                                        <input id="enable_staff_dob" name="is_active_staff_dob" type="checkbox" role="switch" class="form-check-input chk" value="1" <?php echo set_checkbox('is_active_staff_dob', '1', (set_value('is_active_staff_dob', $editstaffidcard[0]->enable_staff_dob) == 1) ? TRUE : FALSE); ?>>
                                    </div>
                                </div>
                                <div class="mb-3 d-flex align-items-center justify-content-between">
                                    <label class="form-label"><?php echo $this->lang->line('barcode_qrcode'); ?></label>
                                    <div class="form-check form-switch d-inline-flex m-0">
                                        <input id="enable_staff_barcode" name="is_active_staff_barcode" type="checkbox" role="switch" class="form-check-input chk" value="1" <?php echo set_checkbox('enable_staff_barcode', '1', (set_value('enable_staff_barcode', $editstaffidcard[0]->enable_staff_barcode) == 1) ? TRUE : FALSE); ?>>
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
            if ($this->rbac->hasPrivilege('staff_id_card', 'can_add') || $this->rbac->hasPrivilege('staff_id_card', 'can_edit')) {
                echo "8";
            } else {
                echo "12";
            }
            ?>">
                <!-- general form elements -->
                <div class="card" id="hroom">
                    <div class="card-header ptbnull">
                        <h3 class="card-title titlefix"><?php echo $this->lang->line('staff_id_card_list'); ?></h3>
                    </div><!-- /.card-header -->
                    <div class="card-body">
                        <div class="mailbox-messages">
                            <div class="download_label"><?php echo $this->lang->line('staff_id_card_list'); ?></div>
                            <div class="table-responsive">
                            <table class="table table-striped table-bordered table-hover example">
                                <thead>
                                    <tr>
                                        <th><?php echo $this->lang->line('id_card_title'); ?></th>
                                        <!-- <th>Certificate Text</th> -->
                                        <th class="noExport"><?php echo $this->lang->line('background_image'); ?></th>
                                        <th class="text-end noExport"><?php echo $this->lang->line('action'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($staffidcardlist)) {
                                        ?>

                                        <?php
                                    } else {
                                        $count = 1;
                                        foreach ($staffidcardlist as $staffidcards_value) {
                                            ?>
                                            <tr>
                                                <td class="mailbox-name">
                                                    <a href="#" data-bs-toggle="popover" class="detail_popover" ><?php echo $staffidcards_value->title; ?></a>
                                                </td>
                                                <td class="mailbox-name">
                                                    <?php if ($staffidcards_value->background != '' && !is_null($staffidcards_value->background)) { ?>
                                                        <img src="<?php echo $this->media_storage->getImageURL('uploads/staff_id_card/background/'.$staffidcards_value->background) ?>" width="40">
                                                    <?php } ?>
                                                       

                                                </td>
                                                <td class="mailbox-date float-end no-print">
                                                    <a id="<?php echo $staffidcards_value->id ?>" class="btn btn-secondary btn-sm view_data" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('view'); ?>">
                                                        <i class="fa fa-reorder"></i>
                                                    </a>
                                                    <?php if ($this->rbac->hasPrivilege('staff_id_card', 'can_edit')) { ?>
                                                        <a href="<?php echo base_url(); ?>admin/staffidcard/edit/<?php echo $staffidcards_value->id ?>" class="btn btn-secondary btn-sm"  data-bs-toggle="tooltip" title="<?php echo $this->lang->line('edit'); ?>">
                                                            <i class="fa fa-pencil"></i>
                                                        </a>
                                                        <?php
                                                    }
                                                    if ($this->rbac->hasPrivilege('staff_id_card', 'can_delete')) {
                                                        ?>
                                                        <a href="<?php echo base_url(); ?>admin/staffidcard/delete/<?php echo $staffidcards_value->id ?>" class="btn btn-secondary btn-sm"  data-bs-toggle="tooltip" title="<?php echo $this->lang->line('delete'); ?>" onclick="return confirm('<?php echo $this->lang->line('delete_confirm') ?>');">
                                                            <i class="fa fa-remove"></i>
                                                        </a>
                                                    <?php } ?>
                                                </td>
                                            </tr>
                                            <?php
                                        }
                                        $count++;
                                    }
                                    ?>
                                </tbody>
                            </table><!-- /.table -->
                          </div>  
                        </div><!-- /.mail-box-messages -->
                    </div><!-- /.card-body -->
                </div>
            </div><!--/.col (left) -->
            <!-- right column -->
        </div>
        <div class="row">
            <div class="col-md-12">
            </div><!--/.col (right) -->
        </div>   <!-- /.row -->
    

<!-- Modal -->
<div class="modal fade sh-modal sh-modal-accent" id="myModal" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myModalLabel"><?php echo $this->lang->line('view_id_card'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="pup-scroll-area">
                <div class="modal-body" id="certificate_detail">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('close'); ?></button>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    var base_url = '<?php echo base_url() ?>';
    function printDiv(elem) {
        popup(jQuery(elem).html());
    }   
</script>

<script type="text/javascript">
    $(document).ready(function () {
        $('.view_data').click(function () {
            var certificateid = $(this).attr("id");
            $.ajax({
                url: "<?php echo base_url('admin/staffidcard/view') ?>",
                method: "post",
                data: {certificateid: certificateid},
                success: function (data) {
                    $('#certificate_detail').html(data);
                    shModal('myModal').show();
                }
            });
        });
    });
</script>

<script>
    $(document).ready(function () {
        $('.detail_popover').popover({
            placement: 'right',
            trigger: 'hover',
            container: 'body',
            html: true,
            content: function () {
                return $(this).closest('td').find('.fee_detail_popover').html();
            }
        });
        $("#header_color").colorpicker();
    });
    
    $(document).ready(function (e) {
        modal_click_disabled('myModal');
    }); 
    
    function remove_background_image(){
        var result = confirm("<?php echo $this->lang->line('delete_confirm') ?>");
        if (result) {
            $('.remove_background_image').html('<input type="hidden" name="remove_background_image" value="1">');         
        }
    }
    
    function remove_logo(){
        var result = confirm("<?php echo $this->lang->line('delete_confirm') ?>");
        if (result) {
            $('.remove_logo').html('<input type="hidden" name="remove_logo" value="1">');         
        }
    }
    
    function remove_sign_image(){
        var result = confirm("<?php echo $this->lang->line('delete_confirm') ?>");
        if (result) {
            $('.remove_sign_image').html('<input type="hidden" name="remove_sign_image" value="1">');         
        }
    }
</script>