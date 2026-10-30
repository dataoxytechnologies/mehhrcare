    <!-- Content Wrapper. Contains page content -->
<div class="row">
            <?php
            if ($this->rbac->hasPrivilege('staff_id_card', 'can_add')) {
                ?>
                <div class="col-md-4">
                    <!-- Horizontal Form -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title"><?php echo $this->lang->line('add_staff_id_card'); ?></h3>
                        </div><!-- /.card-header -->
                        <!-- form start -->
                        <form id="form1" enctype="multipart/form-data" action="<?php echo site_url('admin/staffidcard/create') ?>"  id="certificateform" name="certificateform" method="post" accept-charset="utf-8">
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
                                <div class="mb-3">
                                    <label><?php echo $this->lang->line('background_image'); ?></label>
                                    <input id="documents" placeholder="" type="file" class="filestyle form-control" data-height="40" name="background_image"><span class="text-danger"><?php echo form_error('background_image'); ?></span>
                                </div>
                                <div class="mb-3">
                                    <label><?php echo $this->lang->line('logo'); ?></label>
                                    <input id="logo_img" placeholder="" type="file" class="filestyle form-control" data-height="40" name="logo_img"><span class="text-danger"><?php echo form_error('logo_img'); ?></span>
                                </div>
                                <div class="mb-3">
                                    <label><?php echo $this->lang->line('signature'); ?></label>
                                    <input id="sign_image" placeholder="" type="file" class="filestyle form-control" data-height="40" name="sign_image">
                                    <span class="text-danger"><?php echo form_error('sign_image'); ?></span>
                                </div>
                                <div class="mb-3">
                                    <label><?php echo $this->lang->line('hospital_name'); ?></label><small class="req"> *</small>
                                    <input autofocus="" id="hospital_name" name="hospital_name" placeholder="" type="text" class="form-control" value="<?php echo set_value('hospital_name'); ?>" />
                                    <span class="text-danger"><?php echo form_error('hospital_name'); ?></span>
                                </div>
                                <div class="mb-3">
                                    <label><?php echo $this->lang->line('address_phone_email'); ?></label><small class="req"> *</small>
                                    <textarea class="form-control" id="address" name="address" placeholder="" rows="3" placeholder=""><?php echo set_value('address'); ?></textarea>
                                    <span class="text-danger"><?php echo form_error('address'); ?></span>
                                </div>
                                <div class="mb-3">
                                    <label><?php echo $this->lang->line('id_card_title'); ?></label><small class="req"> *</small>
                                    <input id="title" name="title" placeholder="" type="text" class="form-control" value="<?php echo set_value('title'); ?>" />
                                    <span class="text-danger"><?php echo form_error('title'); ?></span>
                                </div>
                                <div class="mb-3">
                                    <label><?php echo $this->lang->line('header_color'); ?></label>
                                    <input id="header_color" name="header_color" placeholder="" type="text" class="form-control my-colorpicker1" value="<?php echo set_value('header_color'); ?>" />
                                </div>
                                <div class="mb-3 d-flex align-items-center justify-content-between">
                                    <label class="form-label"><?php echo $this->lang->line('staff_name'); ?></label>
                                    <div class="form-check form-switch d-inline-flex m-0">
                                        <input id="enable_name" name="is_active_staff_name" type="checkbox" role="switch" class="form-check-input chk" value="1">
                                    </div>
                                </div>
                                <div class="mb-3 d-flex align-items-center justify-content-between">
                                    <label class="form-label"><?php echo $this->lang->line('staff_id'); ?></label>
                                    <div class="form-check form-switch d-inline-flex m-0">
                                        <input id="enable_staff_id" name="is_active_staff_id" type="checkbox" role="switch" class="form-check-input chk" value="1">
                                    </div>
                                </div>
                                <div class="mb-3 d-flex align-items-center justify-content-between">
                                    <label class="form-label"><?php echo $this->lang->line('designation'); ?></label>
                                    <div class="form-check form-switch d-inline-flex m-0">
                                        <input id="enable_designation" name="is_active_designation" type="checkbox" role="switch" class="form-check-input chk" value="1">
                                    </div>
                                </div>
                                <div class="mb-3 d-flex align-items-center justify-content-between">
                                    <label class="form-label"><?php echo $this->lang->line('department'); ?></label>
                                    <div class="form-check form-switch d-inline-flex m-0">
                                        <input id="enable_department" name="is_active_department" type="checkbox" role="switch" class="form-check-input chk" value="1">
                                    </div>
                                </div>
                                <div class="mb-3 d-flex align-items-center justify-content-between">
                                    <label class="form-label"><?php echo $this->lang->line('father_name'); ?></label>
                                    <div class="form-check form-switch d-inline-flex m-0">
                                        <input id="enable_fathers_name" name="is_active_staff_father_name" type="checkbox" role="switch" class="form-check-input chk" value="1">
                                    </div>
                                </div>
                                <div class="mb-3 d-flex align-items-center justify-content-between">
                                    <label class="form-label"><?php echo $this->lang->line('mother_name'); ?></label>
                                    <div class="form-check form-switch d-inline-flex m-0">
                                        <input id="enable_staff_mother_name" name="is_active_staff_mother_name" type="checkbox" role="switch" class="form-check-input chk" value="1">
                                    </div>
                                </div>
                                <div class="mb-3 d-flex align-items-center justify-content-between">
                                    <label class="form-label"><?php echo $this->lang->line('date_of_joining'); ?></label>
                                    <div class="form-check form-switch d-inline-flex m-0">
                                        <input id="enable_date_of_joining" name="is_active_date_of_joining" type="checkbox" role="switch" class="form-check-input chk" value="1">
                                    </div>
                                </div>
                                <div class="mb-3 d-flex align-items-center justify-content-between">
                                    <label class="form-label"><?php echo $this->lang->line('current_address'); ?></label>
                                    <div class="form-check form-switch d-inline-flex m-0">
                                        <input id="enable_staff_permanent_address" name="is_active_staff_permanent_address" type="checkbox" role="switch" class="form-check-input chk" value="1">
                                    </div>
                                </div>
                                <div class="mb-3 d-flex align-items-center justify-content-between">
                                    <label class="form-label"><?php echo $this->lang->line('phone'); ?></label>
                                    <div class="form-check form-switch d-inline-flex m-0">
                                        <input id="enable_staff_phone" name="is_active_staff_phone" type="checkbox" role="switch" class="form-check-input chk" value="1">
                                    </div>
                                </div>
                                <div class="mb-3 d-flex align-items-center justify-content-between">
                                    <label class="form-label"><?php echo $this->lang->line('date_of_birth'); ?></label>
                                    <div class="form-check form-switch d-inline-flex m-0">
                                        <input id="enable_staff_dob" name="is_active_staff_dob" type="checkbox" role="switch" class="form-check-input chk" value="1">
                                    </div>
                                </div>
                                <div class="mb-3 d-flex align-items-center justify-content-between">
                                    <label class="form-label"><?php echo $this->lang->line('barcode_qrcode'); ?></label>
                                    <div class="form-check form-switch d-inline-flex m-0">
                                        <input id="enable_staff_barcode" name="is_active_staff_barcode" type="checkbox" role="switch" class="form-check-input chk" value="1">
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
            if ($this->rbac->hasPrivilege('staff_id_card', 'can_add')) {
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
                            <table class="table table-striped table-bordered table-hover example">
                                <thead>
                                    <tr>
                                        <th><?php echo $this->lang->line('id_card_title'); ?></th>
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
                                        foreach ($staffidcardlist as $staffidcard_value) {
                                            ?>
                                            <tr>
                                                <td class="mailbox-name">
                                                    <a id="<?php echo $staffidcard_value->id ?>" data-bs-toggle="popover" class="detail_popover view_data sh-cursor-pointer" ><?php echo $staffidcard_value->title; ?></a>
                                                </td>
                                                <td class="mailbox-name">
                                                    <?php if ($staffidcard_value->background != '' && !is_null($staffidcard_value->background)) { ?>
                     <img src="<?php echo $this->media_storage->getImageURL('uploads/staff_id_card/background/'.$staffidcard_value->background) ?>" width="40">
                                                    <?php }  ?>
                                                         
                                                </td>
                                                <td class="mailbox-date float-end no-print">
                                                    <a id="<?php echo $staffidcard_value->id ?>" class="btn btn-secondary btn-sm view_data"  data-bs-toggle="tooltip" title="<?php echo $this->lang->line('view'); ?>">    
                                                        <i class="fa fa-reorder"></i>
                                                    </a>
                                                    <?php
                                                    if ($this->rbac->hasPrivilege('staff_id_card', 'can_edit')) {
                                                        ?>
                                                        <a href="<?php echo base_url(); ?>admin/staffidcard/edit/<?php echo $staffidcard_value->id ?>" class="btn btn-secondary btn-sm"  data-bs-toggle="tooltip" title="<?php echo $this->lang->line('edit'); ?>">
                                                            <i class="fa fa-pencil"></i>
                                                        </a>
                                                        <?php
                                                    }
                                                    if ($this->rbac->hasPrivilege('staff_id_card', 'can_delete')) {
                                                        ?>
                                                        <a href="<?php echo base_url(); ?>admin/staffidcard/delete/<?php echo $staffidcard_value->id ?>" class="btn btn-secondary btn-sm"  data-bs-toggle="tooltip" title="<?php echo $this->lang->line('delete'); ?>" onclick="return confirm('<?php echo $this->lang->line('delete_confirm') ?>');">
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
</script>