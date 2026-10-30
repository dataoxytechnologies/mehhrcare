<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
$genderList      = $this->customlib->getGender_Patient();
?>

<div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                            
                        <h3 class="card-title titlefix"><?php echo $this->lang->line('birth_record'); ?></h3>
                        <div class="d-flex gap-2 align-items-center flex-wrap float-end ms-auto mt-md-0">
                            <?php
                            if ($this->rbac->hasPrivilege('birth_record', 'can_add')) {
                                ?>
                                <a href="javascript:void(0)" onclick="shModal('myModal').show(); $('.filestyle','#myModal').dropify({height:80});" class="btn btn-primary btn-sm birthrecord"><i class="fa fa-plus"></i> <?php echo $this->lang->line('add_birth_record'); ?></a>
                            <?php }?>
                        </div>
                    </div><!-- /.card-header -->
                    <div class="card-body">
                        <div class="download_label"><?php echo $this->lang->line('birth_record'); ?></div>
                        <table id="birthTable" class="table table-striped table-bordered table-hover ajaxlist"  cellspacing="0" width="100%" data-export-title="<?php echo $this->lang->line('birth_record'); ?>">
                            <thead>
                            <tr>
                                <th><?php echo $this->lang->line('reference_no'); ?></th>
                                <th><?php echo $this->lang->line('case_id'); ?></th>
                                <th><?php echo $this->lang->line('generated_by'); ?></th>
                                <th><?php echo $this->lang->line('child_name'); ?></th>
                                <th><?php echo $this->lang->line('gender'); ?></th>
                                <th><?php echo $this->lang->line('birth_date'); ?></th>
                                <th><?php echo $this->lang->line('mother_name'); ?></th>
                                <th><?php echo $this->lang->line('father_name'); ?></th>
                                    <?php 
                                    if (!empty($fields)) {
                                        foreach ($fields as $fields_key => $fields_value) {
                                            ?>
                                            <th><?php echo $fields_value->name; ?></th>
                                            <?php
                                            } 
                                        }
                                    ?>
                                <th class="text-end"><?php echo $this->lang->line('report'); ?></th>
                                <th class="noExport text-end"><?php echo $this->lang->line('action'); ?></th>
                            </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

<div class="modal fade sh-modal sh-modal-branded" id="myModal" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered sh-modal-autoheight modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myModalLabel"><?php echo $this->lang->line('add_birth_record'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formadd" accept-charset="utf-8" method="post">
                <div class="modal-body">
                    <div class="card mb-3">
                        <div class="card-header">
                            <h5 class="card-title mb-0 titlefix"><?php echo $this->lang->line('birth_record_details'); ?></h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <label class="form-label"><?php echo $this->lang->line('child_name'); ?></label><small class="req"> *</small>
                                    <input type="text" name="child_name" id="child_name" class="form-control">
                                    <span class="text-danger"><?php echo form_error('code'); ?></span>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label"><?php echo $this->lang->line('gender'); ?></label><small class="req"> *</small>
                                    <select class="form-control" name="gender">
                                        <option value=""><?php echo $this->lang->line('select'); ?></option>
                                        <?php foreach ($genderList as $key => $value) { ?>
                                        <option value="<?php echo $key; ?>" <?php if (set_value('gender') == $key) echo "selected"; ?>><?php echo $value; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label"><?php echo $this->lang->line('weight'); ?></label><small class="req"> *</small>
                                    <input type="text" name="weight" id="weight" class="form-control">
                                    <span class="text-danger"><?php echo form_error('weight'); ?></span>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label"><?php echo $this->lang->line('birth_date'); ?></label><small class="req"> *</small>
                                    <input id="datetimepicker" name="birth_date" type="text" class="form-control datetime" />
                                    <span class="text-danger"><?php echo form_error('birth_date'); ?></span>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label"><?php echo $this->lang->line('phone'); ?></label>
                                    <input type="text" name="contact" id="contact" class="form-control">
                                    <span class="text-danger"><?php echo form_error('contact'); ?></span>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label"><?php echo $this->lang->line('case_id'); ?></label>
                                    <input type="text" name="case_id" id="case_id" class="form-control">
                                    <span class="text-danger"><?php echo form_error('case_id'); ?></span>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label"><?php echo $this->lang->line('mother_name'); ?></label><small class="req"> *</small>
                                    <input type="text" class="form-control" name="mother" id="mother" readonly="">
                                    <input type="hidden" id="mother_name" name="mother_name" value="" class="form-control">
                                    <span class="text-danger"><?php echo form_error('mother'); ?></span>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label"><?php echo $this->lang->line('father_name'); ?></label>
                                    <input type="text" name="father_name" id="father_name" class="form-control">
                                    <span class="text-danger"><?php echo form_error('father_name'); ?></span>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label"><?php echo $this->lang->line('address'); ?></label>
                                    <input type="text" name="address" id="address" class="form-control">
                                    <span class="text-danger"><?php echo form_error('address'); ?></span>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label"><?php echo $this->lang->line('report'); ?></label>
                                    <textarea name="birth_report" id="birth_report" class="form-control"><?php echo set_value('birth_report'); ?></textarea>
                                </div>
                            </div>
                            <div>
                                <?php echo display_custom_fields('birth_report'); ?>
                            </div>
                        </div>
                    </div>
                    <div class="card mb-3">
                        <div class="card-header">
                            <h5 class="card-title mb-0 titlefix"><?php echo $this->lang->line('attachments'); ?></h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <label class="form-label"><?php echo $this->lang->line('child_photo'); ?></label>
                                    <input class="filestyle" type='file' name='child_img' id="child_img" />
                                    <span class="text-danger"><?php echo form_error('file'); ?></span>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label"><?php echo $this->lang->line('mother_photo'); ?></label>
                                    <input class="filestyle" type='file' name='first_img' id="first_img" />
                                    <span class="text-danger"><?php echo form_error('file'); ?></span>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label"><?php echo $this->lang->line('father_photo'); ?></label>
                                    <input class="filestyle" type='file' name='second_img' id="second_img" />
                                    <span class="text-danger"><?php echo form_error('file'); ?></span>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label"><?php echo $this->lang->line('attach_document_photo'); ?></label>
                                    <input class="filestyle" type='file' name='document' id="document" />
                                    <span class="text-danger"><?php echo form_error('file'); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                    <button type="submit" id="formaddbtn" data-loading-text="<?php echo $this->lang->line('processing'); ?>" class="btn btn-info"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- dd -->
<div class="modal fade sh-modal sh-modal-accent" id="viewModal" tabindex="-1" aria-labelledby="viewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered sh-modal-autoheight modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewModalLabel"><?php echo $this->lang->line('birth_record_details'); ?></h5>
                <div class="d-flex align-items-center gap-2 ms-auto me-2"><div id="edit_delete"></div></div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="view" accept-charset="utf-8" method="get">
                <div class="pup-scroll-area">
                    <div class="modal-body">
                        <?php $file = "uploads/patient_images/no_image.png"; ?>

                        <div class="sh-form-card">
                            <div class="sh-card-header">
                                <span class="sh-card-header-title"><?php echo $this->lang->line('birth_record_details'); ?></span>
                            </div>
                            <div class="sh-info-grid">
                                <div class="row g-0">
                                    <div class="col-6 col-md-3 sh-info-item">
                                        <span class="sh-info-label"><?php echo $this->lang->line('case_id'); ?></span>
                                        <span class="sh-info-value" id="vcase_id">—</span>
                                    </div>
                                    <div class="col-6 col-md-3 sh-info-item">
                                        <span class="sh-info-label"><?php echo $this->lang->line('birth_date'); ?></span>
                                        <span class="sh-info-value" id="vbirth_date">—</span>
                                    </div>
                                    <div class="col-6 col-md-3 sh-info-item">
                                        <span class="sh-info-label"><?php echo $this->lang->line('weight'); ?></span>
                                        <span class="sh-info-value" id="vweight">—</span>
                                    </div>
                                    <div class="col-6 col-md-3 sh-info-item">
                                        <span class="sh-info-label"><?php echo $this->lang->line('gender'); ?></span>
                                        <span class="sh-info-value" id="vgender">—</span>
                                    </div>
                                </div>
                                <div class="row g-0 sh-row-divider">
                                    <div class="col-6 col-md-3 sh-info-item">
                                        <span class="sh-info-label"><?php echo $this->lang->line('phone'); ?></span>
                                        <span class="sh-info-value" id="vcontact">—</span>
                                    </div>
                                    <div class="col-12 col-md-6 sh-info-item">
                                        <span class="sh-info-label"><?php echo $this->lang->line('address'); ?></span>
                                        <span class="sh-info-value" id="vaddress">—</span>
                                    </div>
                                    <div class="col-6 col-md-3 sh-info-item">
                                        <span class="sh-info-label"><?php echo $this->lang->line('document'); ?></span>
                                        <span class="sh-info-value" id="download_document">—</span>
                                    </div>
                                </div>
                                <div class="row g-0 sh-row-divider">
                                    <div class="col-12 sh-info-item">
                                        <span class="sh-info-label"><?php echo $this->lang->line('report'); ?></span>
                                        <span class="sh-info-value" id="vreport">—</span>
                                    </div>
                                </div>
                            </div>
                            <div id="field_data"></div>
                        </div>

                        <div class="sh-form-card">
                            <div class="sh-card-header">
                                <span class="sh-card-header-title"><?php echo $this->lang->line('attachments'); ?></span>
                            </div>
                            <div class="sh-card-body">
                                <div class="row g-3 text-center">
                                    <div class="col-md-4">
                                        <div class="d-flex flex-column align-items-center gap-2">
                                            <img src="" id="image" class="img-thumbnail sh-view-photo d-none" alt="Child photo">
                                            <div class="sh-view-initials d-none" id="image_i"></div>
                                            <div>
                                                <div class="text-muted small"><?php echo $this->lang->line('child_name'); ?></div>
                                                <div class="fw-semibold" id="vchild_name"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="d-flex flex-column align-items-center gap-2">
                                            <img src="" id="imagem" class="img-thumbnail sh-view-photo d-none" alt="Mother photo">
                                            <div class="sh-view-initials d-none" id="imagem_i"></div>
                                            <div>
                                                <div class="text-muted small"><?php echo $this->lang->line('mother_name'); ?></div>
                                                <div class="fw-semibold" id="vmother_name"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="d-flex flex-column align-items-center gap-2">
                                            <img src="" id="imagef" class="img-thumbnail sh-view-photo d-none" alt="Father photo">
                                            <div class="sh-view-initials d-none" id="imagef_i"></div>
                                            <div>
                                                <div class="text-muted small"><?php echo $this->lang->line('father_name'); ?></div>
                                                <div class="fw-semibold" id="vfather_name"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div id="tabledata"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade sh-modal sh-modal-branded" id="myModaledit" tabindex="-1" aria-labelledby="myModaleditLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered sh-modal-autoheight modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myModaleditLabel"><?php echo $this->lang->line('edit_birth_record'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
             <form id="formedit" accept-charset="utf-8" enctype="multipart/form-data" method="post">
                   <div class="modal-body">
                        <div class="card mb-3">
                            <div class="card-header">
                                <h5 class="card-title mb-0 titlefix"><?php echo $this->lang->line('birth_record_details'); ?></h5>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <input type="hidden" name="id" id="eid" value="<?php echo set_value('id'); ?>">
                                    <div class="col-md-3">
                                        <label class="form-label"><?php echo $this->lang->line('child_name'); ?></label><small class="req"> *</small>
                                        <input type="text" value="<?php echo set_value('child_name'); ?>" name="child_name" id="echild_name" class="form-control">
                                        <span class="text-danger"><?php echo form_error('child_name'); ?></span>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label"><?php echo $this->lang->line('gender'); ?></label><small class="req"> *</small>
                                        <select class="form-control" name="gender" id="gender">
                                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                                            <?php
                                            foreach ($genderList as $key => $value) {
                                                ?>
                                                <option value="<?php echo $key; ?>" <?php if (set_value('gender') == $key) {
                                                    echo "selected";
                                                }
                                                ?>><?php echo $value; ?></option>
                                                <?php
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label"><?php echo $this->lang->line('weight'); ?></label><small class="req"> *</small>
                                        <input type="text" value="<?php echo set_value('weight'); ?>" name="weight" id="eweight" class="form-control">
                                        <span class="text-danger"><?php echo form_error('weight'); ?></span>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label"><?php echo $this->lang->line('birth_date'); ?></label><small class="req">*</small>
                                        <input value="<?php echo set_value('birth_date'); ?>" id="ebirth_date" name="birth_date" placeholder="" type="text" class="form-control datetime" />
                                        <span class="text-danger"><?php echo form_error('birth_date'); ?></span>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label"><?php echo $this->lang->line('phone'); ?></label>
                                        <input type="text" value="<?php echo set_value('contact'); ?>" name="contact" id="econtact" class="form-control">
                                        <span class="text-danger"><?php echo form_error('contact'); ?></span>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label"><?php echo $this->lang->line("case_id"); ?></label>
                                        <input type="text" onchange="case_reference()" name="case_id" value="<?php echo set_value('case_id'); ?>" id="ecase_id" class="form-control">
                                        <span class="text-danger"><?php echo form_error('ipd'); ?></span>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label"><?php echo $this->lang->line('mother_name'); ?></label><small class="req"> *</small>
                                        <input type="text" class="form-control" name="mother" id="mother">
                                        <input type="hidden" id="mother_name" name="mother_name" value="" class="form-control">
                                        <span class="text-danger"><?php echo form_error('mother'); ?></span>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label"><?php echo $this->lang->line('father_name'); ?></label>
                                        <input type="text" value="<?php echo set_value('father_name'); ?>" name="father_name" id="efather_name" class="form-control">
                                        <span class="text-danger"><?php echo form_error('father_name'); ?></span>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label"><?php echo $this->lang->line('address'); ?></label>
                                        <input type="text" value="<?php echo set_value('address'); ?>" name="address" id="eaddress" class="form-control">
                                        <span class="text-danger"><?php echo form_error('address'); ?></span>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label"><?php echo $this->lang->line('report'); ?></label>
                                        <textarea name="birth_report" id="ebirth_report" class="form-control"><?php echo set_value('birth_report'); ?></textarea>
                                    </div>
                                </div>
                                <div id="customfield"></div>
                            </div>
                        </div>
                        <div class="card mb-3">
                            <div class="card-header">
                                <h5 class="card-title mb-0 titlefix"><?php echo $this->lang->line('attachments'); ?></h5>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-3">
                                        <label class="form-label"><?php echo $this->lang->line('child_photo'); ?></label>
                                        <input class="filestyle" type="file" name="child_img" id="echild_img" />
                                        <span class="text-danger"><?php echo form_error('file'); ?></span>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label"><?php echo $this->lang->line('mother_photo'); ?></label>
                                        <input class="filestyle" type="file" name="mother_pic" id="emother_pic" />
                                        <span class="text-danger"><?php echo form_error('file'); ?></span>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label"><?php echo $this->lang->line('father_photo'); ?></label>
                                        <input class="filestyle" type="file" name="father_pic" id="efather_pic" />
                                        <span class="text-danger"><?php echo form_error('file'); ?></span>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label"><?php echo $this->lang->line('attach_document_photo'); ?></label>
                                        <input class="filestyle" type="file" name="document" id="edocument" />
                                        <span class="text-danger"><?php echo form_error('file'); ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                    <button type="submit" id="formeditbtn" data-loading-text="<?php echo $this->lang->line('processing'); ?>" class="btn btn-info"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<script type="text/javascript">
    $('#myModal').on('hidden.bs.modal', function (e) {
        $(this).find('#formadd')[0].reset();
        $(this).find('.dropify-clear').trigger('click');
    });

    $(function () {
        //Initialize Select2 Elements
        $('.select2').select2()
    });
    $(function () {
        $('#easySelectable').easySelectable();
    });
    function apply_to_all() {
        var standard_charge = $("#standard_charge").val();
        $('input name=schedule_charge_id').val(standard_charge);
    }
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
        $("#formadd").on('submit', (function (e) {
           
            e.preventDefault();
            $.ajax({
                url: baseurl+'admin/birthordeath/addBirthdata',
                type: "POST",
                data: new FormData(this),
                dataType: 'json',
                contentType: false,
                cache: false,
                processData: false,
                 beforeSend: function() {
        $("#formaddbtn").btnLoading();
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
                        window.location.reload(true);
                    }
                    $("#formaddbtn").btnReset();
                },
    error: function(xhr) { // if error occured
      $("#formaddbtn").btnReset();
        alert("Error occured.please try again");
    },
    complete: function() {
       $("#formaddbtn").btnReset();
    }
            });
        }));
    });

    $(document).ready(function (e) {
        $("#formedit").on('submit', (function (e) {
            $("#formeditbtn").btnLoading();
            e.preventDefault();
            $.ajax({
                url: baseurl+'admin/birthordeath/update_birth',
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
                    $("#formeditbtn").btnReset();
                },
                error: function () {

                }
            });
        }));
    });

    $(document).ready(function (e) {
        var date_format = '<?php echo $result = strtr($this->customlib->getHospitalDateFormat(), ['d' => 'DD', 'm' => 'MM', 'Y' => 'YYYY']) ?>';
        /* picker init removed - auto-init via class + event delegation in footer.php */
    });

    function get_PatientDetails(id) {
        $.ajax({
            url: baseurl+'admin/pharmacy/patientDetails',
            type: "POST",
            data: {id: id},
            dataType: 'json',
            success: function (res) {
                if (res) {
                    $('#patient_name').val(res.patient_name);
                    $('#patient_id').val(res.id);
                } else {
                    $('#patient_name').val('Null');
                }
            }
        });
    }
$(".mother_name").on("select2:select", function (e) { 
  var select_val = $(e.currentTarget).val();

});

    function escHtml(str) {
        var d = document.createElement('div');
        d.appendChild(document.createTextNode(str || ''));
        return d.innerHTML;
    }

    function renderBirthAvatar(imgId, initId, imageUrl, personName) {
        var name = (personName || '').replace(/\s*\([^)]*\)\s*/g, ' ').trim();
        var hasRealImage = imageUrl && imageUrl.indexOf('no_image') === -1;
        if (hasRealImage) {
            $('#' + imgId).attr('src', imageUrl + '<?php echo img_time(); ?>').removeClass('d-none');
            $('#' + initId).text('').addClass('d-none');
        } else {
            var parts = name.split(/\s+/).filter(Boolean);
            var initials = parts.length === 0 ? '?' : parts.length === 1 ? parts[0].charAt(0) : parts[0].charAt(0) + parts[parts.length - 1].charAt(0);
            $('#' + imgId).addClass('d-none').removeAttr('src');
            $('#' + initId).text(initials.toUpperCase()).removeClass('d-none');
        }
    }

    function viewDetail(id) {
        shModal('viewModal').show();
        $.ajax({
            url: baseurl+'admin/birthordeath/getBirthdata',
            type: "POST",
            data: {id: id},
            dataType: 'json',
            success: function (data) {
                $("#vid").text(data.id);
                $("#vchild_name").text(data.child_name);
                $("#vbirth_date").text(data.birth_date);
                $("#vweight").text(data.weight);
                $("#vmother_name").text(data.patient_name+' ('+data.patient_id+')');
                $("#vcontact").text(data.contact);
                $("#vaddress").text(data.address);
                $("#vreport").text(data.birth_report);
                $("#vcase_id").text(data.case_reference_id);
                $("#vgender").text(data.gender);
                $("#vfather_name").text(data.father_name);
                $("#vbirth_report").text(data.birth_report);

                renderBirthAvatar('image',  'image_i',  data.child_pic,  data.child_name);
                renderBirthAvatar('imagem', 'imagem_i', data.mother_pic, data.patient_name + ' (' + data.patient_id + ')');
                renderBirthAvatar('imagef', 'imagef_i', data.father_pic, data.father_name);

                var downloadid = data.document;
                var table_html = '';

                $.each(data.field_data, function (i, obj)
                {
                    if (obj.field_value == null) {
                        var field_value = "";
                    } else {
                        var field_value = obj.field_value;
                    }
                     var name = obj.name ;
                    table_html += "<div class='col-6 col-md-3 sh-info-item'><span class='sh-info-label'>" + escHtml(capitalizeFirstLetter(name)) + "</span><span class='sh-info-value'>" + escHtml(field_value) + "</span></div>";
                });
                $("#field_data").html(table_html ? "<div class='sh-info-grid'><div class='row g-0 sh-row-divider'>" + table_html + "</div></div>" : "");
                $('#edit_delete').html("<?php if ($this->rbac->hasPrivilege('birth_record', 'can_view')) {?><a href='#' class='btn btn-sm btn-light' data-bs-toggle='tooltip' onclick='printData(" + id + ")' title='<?php echo $this->lang->line('print'); ?>'><i class='fa fa-print'></i></a><?php }?><?php if ($this->rbac->hasPrivilege('birth_record', 'can_edit')) {?><a href='#' class='btn btn-sm btn-light' onclick='getRecord(" + id + ")' data-bs-toggle='tooltip' title='<?php echo $this->lang->line('edit'); ?>'><i class='fa fa-pencil'></i></a><?php }?><?php if ($this->rbac->hasPrivilege('birth_record', 'can_delete')) {?><a class='btn btn-sm btn-light' onclick='delete_bill(" + id + ")' href='#' data-bs-toggle='tooltip' title='<?php echo $this->lang->line('delete'); ?>'><i class='fa fa-trash'></i></a><?php }?>");
                if (data.document) {
                    $('#download_document').html("<a href='<?php echo base_url(); ?>admin/birthordeath/download/" + id + "' data-bs-toggle='tooltip'  title='<?php echo $this->lang->line('download'); ?>'><i class='fa fa-download'></i></a>");
                }
            },
        });
    }

    function getRecord(id) {
        shModal('myModaledit').show();
        shModal('viewModal').hide();
        $('.filestyle','#myModaledit').dropify({ height: 80 });
        $.ajax({
            url: baseurl+'admin/birthordeath/edit',
            type: "GET",
            data: {id: id},
            dataType: 'json',
            success: function (data) {
                $("#eid").val(data.id);
                $('#customfield').html(data.custom_fields_value);
                $("#ecase_id").val(data.case_reference_id);
                $("#echild_name").val(data.child_name);
                $("#ebirth_date").val(data.birth_date);
                $("#eweight").val(data.weight);
                $("#gender").val(data.gender);
                $("#econtact").val(data.contact);
                $("#eaddress").val(data.address);
                $("#efather_name").val(data.father_name);
                $("#ebirth_report").val(data.birth_report);
                getmothernamebycaseid(data.case_reference_id);
            },
        });
    }

    function delete_bill(id) {
        if (confirm('<?php echo $this->lang->line('delete_confirm'); ?>')) {
            $.ajax({
                url: baseurl+'admin/birthordeath/delete/' + id,
                success: function (res) {
                    successMsg('<?php echo $this->lang->line('delete_message'); ?>');
                    window.location.reload(true);
                },
                error: function () {
                    alert("Fail")
                }
            });
        }
    }

    function printData(id) {
        var base_url = '<?php echo base_url() ?>';
        $.ajax({
            url: base_url + 'admin/birthordeath/getBirthprintDetails/' + id,
            type: 'POST',
            data: {id: id, print: 'yes'},
            success: function (result) {
                popup(result);
            }
        });
    }     

    function apply_to_all() {
        var total = 0;
        var standard_charge = $("#standard_charge").val();
        var schedule_charge = document.getElementsByName('schedule_charge[]');
        for (var i = 0; i < schedule_charge.length; i++) {
            var inp = schedule_charge[i];
            inp.value = standard_charge;
        }
    }

    function holdModal(modalId) {
        (function(){var _el=document.getElementById(modalId);if(_el)bootstrap.Modal.getOrCreateInstance(_el,{backdrop:'static',keyboard:false}).show();})();;
        $('.filestyle','#' + modalId).dropify({ height: 80 });
    }
</script>
<script type="text/javascript">
$(".birthrecord").click(function(){
        $('#formadd').trigger("reset");
        $('#select2-14fy-container').html('');
        $(".dropify-clear").trigger("click");
});
</script>

<!-- //========datatable start===== -->
<script type="text/javascript">
( function ( $ ) {
    'use strict';
    $(document).ready(function () {
        window.table = initDatatable('ajaxlist','admin/birthordeath/getbirthDatatable',[],[],100,[
            { "bSortable": false, "sWidth": "110px", "aTargets": [ -1 ], 'sClass': 'dt-body-right' },
            { "sWidth": "120px", "aTargets": [ -2, -3, -4, -5 ] }
        ]);
        window.table.on('draw.dt', function () {
            var lastCol = document.querySelector('#birthTable colgroup col:last-child');
            if (lastCol) lastCol.style.setProperty('width', '110px', 'important');
            $('#birthTable th:last-child, #birthTable td:last-child').each(function () {
                this.style.setProperty('width',     '110px', 'important');
                this.style.setProperty('min-width', '110px', 'important');
                this.style.setProperty('max-width', '110px', 'important');
            });
        });
    });
} ( jQuery ) )
</script>
<script type="text/javascript">
    function delay(callback, ms) {
  var timer = 0;
  return function() {
    var context = this, args = arguments;
    clearTimeout(timer);
    timer = setTimeout(function () {
      callback.apply(context, args);
    }, ms || 0);
  };
}

// Example usage:

$('input[name=case_id]').keyup(delay(function (e) {
    $("#formadd #mother").val('');
    $("#formedit #mother").val('');
    $("#formadd #mother_name").val('');
    $("#formedit #mother_name").val('');
    
    var case_reference_id=$("input[name=case_id]").val();
    var case_reference_id= this.value ;
    if (isNaN(case_reference_id)) {

        errorMsg('<?php echo $this->lang->line("case_id_not_valid"); ?>');
         
        }else{
          getmothernamebycaseid(case_reference_id);  
        }   

}, 500)); 

function getmothernamebycaseid(case_id) {
    if(case_id!=''){
       $.ajax({
            url: baseurl+'admin/birthordeath/getpatientBycaseId/'+case_id,
            type: "POST",
            data: {case_reference_id: case_id},
            dataType: 'json',
            success: function (res) {
                if(res.status==1){

                if(res.gender == 'Male'){
                        errorMsg(res.message);
                    }else{
                        $("#formadd #mother").val(res.patient_name).prop('readonly',true);
                        $("#formedit #mother").val(res.patient_name).prop('readonly',true);
                        $("#formadd #mother_name").val(res.patient_id);
                        $("#formedit #mother_name").val(res.patient_id);
                    }

               }else{
                errorMsg(res.message);
               }
            }
        }); 
   }else{
 
   }
}
</script>
<script type="text/javascript">  
    
    $(document).ready(function (e) {
        modal_click_disabled('myModal', 'viewModal', 'myModaledit');
    });
</script>
<!-- //========datatable end===== -->