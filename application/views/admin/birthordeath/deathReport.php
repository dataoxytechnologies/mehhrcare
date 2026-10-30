<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
$genderList      = $this->customlib->getGender();
?>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header ptbnull">
                <h3 class="card-title titlefix"><?php echo $this->lang->line('death_record'); ?></h3>
                <div class="d-flex gap-2 align-items-center flex-wrap float-end ms-auto mt-md-0">
                    <?php if ($this->rbac->hasPrivilege('death_record', 'can_add')) { ?>
                    <a href="javascript:void(0)" onclick="shModal('myModal').show(); $('.filestyle','#myModal').dropify();" class="btn btn-primary btn-sm deathrecord"><i class="fa fa-plus"></i> <?php echo $this->lang->line('add_death_record'); ?></a>
                    <?php } ?>
                </div>
            </div><!-- /.card-header -->
            <div class="card-body">
                <div class="download_label"><?php echo $this->lang->line('death_record'); ?></div>
                <div class="table-responsive overflow-visible-lg">
                    <table id="deathTable" class="table table-striped table-bordered table-hover ajaxlist" cellspacing="0" width="100%" data-export-title="<?php echo $this->lang->line('death_record'); ?>">
                        <thead>
                            <tr>
                                <th><?php echo $this->lang->line('reference_no'); ?></th>
                                <th><?php echo $this->lang->line('case_id'); ?></th>
                                <th><?php echo $this->lang->line('generated_by'); ?></th>
                                <th><?php echo $this->lang->line('patient_name'); ?></th>
                                <th><?php echo $this->lang->line('guardian_name'); ?></th>
                                <th><?php echo $this->lang->line('gender'); ?></th>
                                <th><?php echo $this->lang->line('death_date'); ?></th>
                                <?php if (!empty($fields)) {
                                    foreach ($fields as $fields_key => $fields_value) { ?>
                                    <th class="white-space-nowrap"><?php echo $fields_value->name; ?></th>
                                <?php } } ?>
                                <th class="text-end"><?php echo $this->lang->line('report'); ?></th>
                                <th class="noExport text-end"><?php echo $this->lang->line('action'); ?></th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Modal -->
<div class="modal fade sh-modal sh-modal-accent" id="myModal" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered sh-modal-autoheight modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myModalLabel"><i class="fa fa-plus me-2 sh-icon-soft"></i><?php echo $this->lang->line('add_death_record'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formadd" accept-charset="utf-8" method="post" enctype="multipart/form-data">
                <div class="pup-scroll-area">
                    <div class="modal-body">
                        <div class="sh-form-card">
                            <div class="sh-card-header">
                                <span class="sh-card-header-title"><?php echo $this->lang->line('death_record_details'); ?></span>
                            </div>
                            <div class="p-2">
                                <div class="row g-3">
                                    <div class="col-md-3">
                                        <label class="form-label form-label-sm"><?php echo $this->lang->line("case_id"); ?> <small class="req">*</small></label>
                                        <input type="text" name="case_id" id="case_id" class="form-control form-control-sm">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label form-label-sm"><?php echo $this->lang->line('patient_name'); ?> <small class="req">*</small></label>
                                        <input type="text" id="patient_name" readonly name="patient_name" class="form-control form-control-sm">
                                        <input type="hidden" id="patient" name="patient">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label form-label-sm"><?php echo $this->lang->line('death_date'); ?> <small class="req">*</small></label>
                                        <input id="datetimepicker" name="death_date" type="text" class="form-control form-control-sm datetime" />
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label form-label-sm"><?php echo $this->lang->line('guardian_name'); ?> <small class="req">*</small></label>
                                        <input type="text" name="guardian_name" id="guardian_name" class="form-control form-control-sm">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label form-label-sm"><?php echo $this->lang->line('attachment'); ?></label>
                                        <input type="file" name="document" id="document" class="form-control filestyle">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label form-label-sm"><?php echo $this->lang->line('report'); ?></label>
                                        <textarea name="death_report" id="death_report" class="form-control form-control-sm"><?php echo set_value('death_report'); ?></textarea>
                                    </div>
                                    <div>
                                        <?php echo display_custom_fields('death_report'); ?>
                                    </div>
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

<!-- View Modal -->
<div class="modal fade sh-modal sh-modal-accent" id="viewModal" tabindex="-1" aria-labelledby="viewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered sh-modal-autoheight modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewModalLabel"><?php echo $this->lang->line('death_record_details'); ?></h5>
                <div class="d-flex align-items-center gap-2 ms-auto me-2"><div id="edit_delete"></div></div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="view" accept-charset="utf-8" method="get">
                <div class="pup-scroll-area">
                    <div class="modal-body">
                        <div class="sh-form-card">
                            <div class="sh-card-header">
                                <span class="sh-card-header-title"><?php echo $this->lang->line('death_record_details'); ?></span>
                            </div>
                            <div class="sh-info-grid">
                                <div class="row g-0">
                                    <div class="col-6 col-md-3 sh-info-item">
                                        <span class="sh-info-label"><?php echo $this->lang->line('reference_no'); ?></span>
                                        <span class="sh-info-value" id="vreference_no">—</span>
                                    </div>
                                    <div class="col-6 col-md-3 sh-info-item">
                                        <span class="sh-info-label"><?php echo $this->lang->line('case_id'); ?></span>
                                        <span class="sh-info-value" id="vcase_id">—</span>
                                    </div>
                                    <div class="col-6 col-md-3 sh-info-item">
                                        <span class="sh-info-label"><?php echo $this->lang->line('death_date'); ?></span>
                                        <span class="sh-info-value" id="vdeath_date">—</span>
                                    </div>
                                    <div class="col-6 col-md-3 sh-info-item">
                                        <span class="sh-info-label"><?php echo $this->lang->line('gender'); ?></span>
                                        <span class="sh-info-value" id="vgender">—</span>
                                    </div>
                                </div>
                                <div class="row g-0 sh-row-divider">
                                    <div class="col-6 col-md-3 sh-info-item">
                                        <span class="sh-info-label"><?php echo $this->lang->line('patient_name'); ?></span>
                                        <span class="sh-info-value" id="vpatient">—</span>
                                    </div>
                                    <div class="col-6 col-md-3 sh-info-item">
                                        <span class="sh-info-label"><?php echo $this->lang->line('age'); ?></span>
                                        <span class="sh-info-value" id="vopdipd_age">—</span>
                                    </div>
                                    <div class="col-6 col-md-3 sh-info-item">
                                        <span class="sh-info-label"><?php echo $this->lang->line('guardian_name'); ?></span>
                                        <span class="sh-info-value" id="vguardian_name">—</span>
                                    </div>
                                    <div class="col-6 col-md-3 sh-info-item">
                                        <span class="sh-info-label"><?php echo $this->lang->line('address'); ?></span>
                                        <span class="sh-info-value" id="vaddress">—</span>
                                    </div>
                                </div>
                                <div class="row g-0 sh-row-divider">
                                    <div class="col-12 sh-info-item">
                                        <span class="sh-info-label"><?php echo $this->lang->line('death_report'); ?></span>
                                        <span class="sh-info-value" id="vdeath_report">—</span>
                                    </div>
                                </div>
                            </div>
                            <div id="field_data"></div>
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

<!-- Edit Modal -->
<div class="modal fade sh-modal sh-modal-accent" id="myModaledit" tabindex="-1" aria-labelledby="myModaleditLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered sh-modal-autoheight modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myModaleditLabel"><i class="fa fa-pencil me-2 sh-icon-soft"></i><?php echo $this->lang->line('edit_death_record'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formedit" accept-charset="utf-8" method="post" enctype="multipart/form-data">
                <input type="hidden" name="id" id="eid" value="<?php echo set_value('id'); ?>">
                <div class="pup-scroll-area">
                    <div class="modal-body">
                        <div class="sh-form-card">
                            <div class="sh-card-header">
                                <span class="sh-card-header-title"><?php echo $this->lang->line('death_record_details'); ?></span>
                            </div>
                            <div class="p-2">
                                <div class="row g-3">
                                    <div class="col-md-3">
                                        <label class="form-label form-label-sm"><?php echo $this->lang->line("case_id"); ?> <small class="req">*</small></label>
                                        <input id="ecase_id" value="<?php echo set_value('case_id'); ?>" name="case_id" type="text" class="form-control form-control-sm" />
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label form-label-sm"><?php echo $this->lang->line('patient_name'); ?> <small class="req">*</small></label>
                                        <input type="text" id="epatient_name" name="epatient_name" class="form-control form-control-sm" readonly="">
                                        <input type="hidden" id="epatient" name="epatient">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label form-label-sm"><?php echo $this->lang->line('death_date'); ?> <small class="req">*</small></label>
                                        <input id="edeath_date" value="<?php echo set_value('death_date'); ?>" name="death_date" type="text" class="form-control form-control-sm datetime" />
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label form-label-sm"><?php echo $this->lang->line('guardian_name'); ?> <small class="req">*</small></label>
                                        <input type="text" value="<?php echo set_value('guardian_name'); ?>" name="guardian_name" id="eguardian_name" class="form-control form-control-sm">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label form-label-sm"><?php echo $this->lang->line('attachment'); ?></label>
                                        <input type="file" name="document" id="edocument" class="form-control filestyle">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label form-label-sm"><?php echo $this->lang->line('report'); ?></label>
                                        <textarea name="death_report" id="edeath_report" class="form-control form-control-sm"><?php echo set_value('death_report'); ?></textarea>
                                    </div>
                                    <div id="customfield"></div>
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
        $(".dropify-clear").trigger("click");
        $("#selectdata").val("").change();
    });

    $(function () {
        $('#easySelectable').easySelectable();
        $('.select2').select2()
    })
</script>
<script type="text/javascript">
    (function ($) {
        $.fn.easySelectable = function (options) {
            var el = $(this);
            var options = $.extend({
                'item': 'li',
                'state': true,
                onSelecting: function (el) {},
                onSelected: function (el) {},
                onUnSelected: function (el) {}
            }, options);
            el.on('dragstart', function (event) { event.preventDefault(); });
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
                        if (prev_el == $(this).index()) return true;
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
                $(document).on('mouseup', function () { el.off('mouseover'); });
            } else {
                el.off('mousedown');
            }
        };
    })(jQuery);
</script>
<script type="text/javascript">
    $(document).ready(function (e) {
        $("#formadd").on('submit', (function (e) {
            $("#formaddbtn").btnLoading();
            e.preventDefault();
            $.ajax({
                url: '<?php echo base_url(); ?>admin/birthordeath/addDeathdata',
                type: "POST",
                data: new FormData(this),
                dataType: 'json',
                contentType: false,
                cache: false,
                processData: false,
                success: function (data) {
                    if (data.status == "fail") {
                        var message = "";
                        $.each(data.error, function (index, value) { message += value; });
                        errorMsg(message);
                    } else {
                        successMsg(data.message);
                        window.location.reload(true);
                    }
                    $("#formaddbtn").btnReset();
                },
                error: function () {}
            });
        }));
    });

    $(document).ready(function (e) {
        $("#formedit").on('submit', (function (e) {
            $("#formeditbtn").btnLoading();
            e.preventDefault();
            $.ajax({
                url: '<?php echo base_url(); ?>admin/birthordeath/update_death',
                type: "POST",
                data: new FormData(this),
                dataType: 'json',
                contentType: false,
                cache: false,
                processData: false,
                success: function (data) {
                    if (data.status == "fail") {
                        var message = "";
                        $.each(data.error, function (index, value) { message += value; });
                        errorMsg(message);
                    } else {
                        successMsg(data.message);
                        window.location.reload(true);
                    }
                    $("#formeditbtn").btnReset();
                },
                error: function () {}
            });
        }));
    });

    $(document).ready(function (e) {
        var date_format = '<?php echo $result = strtr($this->customlib->getHospitalDateFormat(), ['d' => 'DD', 'm' => 'MM', 'Y' => 'YYYY']) ?>';
        /* picker init removed - auto-init via class + event delegation in footer.php */
    });

    function escHtml(str) {
        var d = document.createElement('div');
        d.appendChild(document.createTextNode(str || ''));
        return d.innerHTML;
    }

    function viewDetail(id) {
        shModal('viewModal').show();
        $.ajax({
            url: '<?php echo base_url(); ?>admin/birthordeath/getDeathdata',
            type: "POST",
            data: {id: id},
            dataType: 'json',
            success: function (data) {
                $("#vreference_no").text(data.prefix + data.id);
                $("#vopdipd_age").text(data.age);
                $("#vpatient").text(data.patient_name + " (" + data.patient_id + ")");
                $("#vgender").text(data.gender);
                $("#vcase_id").text(data.case_reference_id);
                $("#vdeath_date").text(data.death_date);
                $("#vguardian_name").text(data.guardian_name);
                $("#vaddress").text(data.address);
                $("#vdeath_report").text(data.death_report);

                var table_html = '';
                $.each(data.field_data, function (i, obj) {
                    var field_value = obj.field_value || "";
                    var name = obj.name;
                    table_html += "<div class='col-6 col-md-3 sh-info-item'><span class='sh-info-label'>" + escHtml(capitalizeFirstLetter(name)) + "</span><span class='sh-info-value'>" + escHtml(field_value) + "</span></div>";
                });
                $("#field_data").html(table_html ? "<div class='sh-info-grid'><div class='row g-0 sh-row-divider'>" + table_html + "</div></div>" : "");

                $('#edit_delete').html("<?php if ($this->rbac->hasPrivilege('death_record', 'can_view')) {?><a href='#' class='btn btn-sm btn-light' data-bs-toggle='tooltip' onclick='printData(" + id + ")' title='<?php echo $this->lang->line('print'); ?>'><i class='fa fa-print'></i></a><?php }?><?php if ($this->rbac->hasPrivilege('death_record', 'can_edit')) {?><a href='#' class='btn btn-sm btn-light' onclick='getRecord(" + id + ")' data-bs-toggle='tooltip' title='<?php echo $this->lang->line('edit'); ?>'><i class='fa fa-pencil'></i></a><?php }?><?php if ($this->rbac->hasPrivilege('death_record', 'can_delete')) {?><a href='#' class='btn btn-sm btn-light' onclick='delete_bill(" + id + ")' data-bs-toggle='tooltip' title='<?php echo $this->lang->line('delete'); ?>'><i class='fa fa-trash'></i></a><?php }?>");
            },
        });
    }

    function getRecord(id) {
        shModal('myModaledit').show();
        $('.filestyle', '#myModaledit').dropify();
        $.ajax({
            url: '<?php echo base_url(); ?>admin/birthordeath/editDeath',
            type: "POST",
            data: {id: id},
            dataType: 'json',
            success: function (data) {
                $("#eid").val(data.id);
                $('#customfield').html(data.custom_fields_value);
                $("#edeath_date").val(data.death_date);
                $("#ecase_id").val(data.case_reference_id);
                $("#eguardian_name").val(data.guardian_name);
                $("#edeath_report").val(data.death_report);
                $("#formedit #epatient_name").val(data.patient_name + " (" + data.patient_id + ")").prop("readonly", true);
                $("#formedit #epatient").val(data.patient_id);
            },
        });
    }

    function delete_bill(id) {
        if (confirm('<?php echo $this->lang->line('delete_confirm'); ?>')) {
            $.ajax({
                url: '<?php echo base_url(); ?>admin/birthordeath/deletedeath/' + id,
                success: function (res) {
                    successMsg('<?php echo $this->lang->line('delete_message'); ?>');
                    window.location.reload(true);
                },
                error: function () { alert("Fail") }
            });
        }
    }

    function printData(id) {
        var base_url = '<?php echo base_url() ?>';
        $.ajax({
            url: base_url + 'admin/birthordeath/getDeathprintDetails/' + id,
            type: 'POST',
            data: {id: id, print: 'yes'},
            success: function (result) { popup(result); }
        });
    }
</script>
<script type="text/javascript">
    function delay(callback, ms) {
        var timer = 0;
        return function() {
            var context = this, args = arguments;
            clearTimeout(timer);
            timer = setTimeout(function () { callback.apply(context, args); }, ms || 0);
        };
    }

    $(document).on('input', '#case_id', function () {
        $('#patient_name').val('');
        $('#guardian_name').val('');
        $("#patient").val('');
        var case_reference_id = $(this).val();
        if (isNaN(case_reference_id)) {
            errorMsg('<?php echo $this->lang->line("case_id_not_valid"); ?>');
        } else {
            getmothernamebycaseid(case_reference_id);
        }
    });

    $(document).on('input', '#ecase_id', function () {
        $('#epatient_name').val('');
        $('#eguardian_name').val('');
        var case_reference_id = $(this).val();
        if (isNaN(case_reference_id)) {
            errorMsg('<?php echo $this->lang->line("case_id_not_valid"); ?>');
        } else {
            getmothernamebycaseid(case_reference_id);
        }
    });

    function getmothernamebycaseid(case_id) {
        if (case_id != '') {
            $.ajax({
                url: '<?php echo base_url(); ?>admin/birthordeath/getdeathpatientBycaseId/' + case_id,
                type: "POST",
                data: {case_reference_id: case_id},
                dataType: 'json',
                success: function (res) {
                    if (res.status == 1) {
                        $("#formadd #patient_name").val(res.patient_name).prop('readonly', true);
                        $("#formadd #patient").val(res.patient_id);
                        $("#formadd #guardian_name").val(res.guardian_name);
                        $("#formedit #epatient").val(res.patient_id);
                        $("#formedit #epatient_name").val(res.patient_name).prop('readonly', true);
                        $("#formedit #eguardian_name").val(res.guardian_name);
                    } else {
                        errorMsg(res.message);
                    }
                }
            });
        }
    }

    $(document).ready(function (e) {
        modal_click_disabled('viewModal', 'myModaledit');
    });
</script>
<!-- //========datatable start===== -->
<script type="text/javascript">
( function ( $ ) {
    'use strict';
    $(document).ready(function () {
        window.table = initDatatable('ajaxlist','admin/birthordeath/getdeathDatatable',[],[],100,[
            { "bSortable": false, "sWidth": "110px", "aTargets": [ -1 ], 'sClass': 'dt-body-right' },
            { "sWidth": "120px", "aTargets": [ -2, -3, -4, -5 ] }
        ]);
        window.table.on('draw.dt', function () {
            var lastCol = document.querySelector('#deathTable colgroup col:last-child');
            if (lastCol) lastCol.style.setProperty('width', '110px', 'important');
            $('#deathTable th:last-child, #deathTable td:last-child').each(function () {
                this.style.setProperty('width',     '110px', 'important');
                this.style.setProperty('min-width', '110px', 'important');
                this.style.setProperty('max-width', '110px', 'important');
            });
        });
    });
} ( jQuery ) )
</script>
<!-- //========datatable end===== -->
