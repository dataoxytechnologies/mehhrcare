<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
$genderList      = $this->customlib->getGender();
?>
<div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title titlefix"><?php echo $this->lang->line('ambulance_call_list'); ?></h3>
                        <div class="d-flex gap-2 align-items-center flex-wrap float-end">
                            <?php if ($this->rbac->hasPrivilege('ambulance_call', 'can_add')) {?>
                                <a data-bs-toggle="modal" onclick="holdModal('callModal')" class="btn btn-primary btn-sm"><i class="fa fa-plus"></i> <?php echo $this->lang->line('add') . " " . $this->lang->line('ambulance_call'); ?></a>
                            <?php }?>
                        </div>
                    </div><!-- /.card-header -->
                    <div class="card-body">
                        <div class="download_label"><?php echo $this->lang->line('ambulance_call_list'); ?></div>
                        <table class="table table-striped table-bordered table-hover example" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <th><?php echo $this->lang->line('bill') . " " . $this->lang->line('no'); ?></th>
                                    <th><?php echo $this->lang->line('patient') . " " . $this->lang->line('name'); ?></th>
                                    <th><?php echo $this->lang->line('contact') . " " . $this->lang->line('no'); ?></th>
                                    <th><?php echo $this->lang->line('vehicle_no'); ?></th>
                                    <th><?php echo $this->lang->line('vehicle_model'); ?></th>
                                    <th><?php echo $this->lang->line('driver_name'); ?></th>
                                    <th><?php echo $this->lang->line('address'); ?></th>
                                    <th><?php echo $this->lang->line('date'); ?></th>
                                    <th class="text-end"><?php echo $this->lang->line('amount') . " (" . $currency_symbol . ")"; ?></th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php if (empty($listCall)) { ?>
                            <?php
} else {
    $count = 1;
    foreach ($listCall as $data) {
        ?>
                                        <tr>
                                            <td>
                                                <?php if ($this->rbac->hasPrivilege('ambulance bill', 'can_view')) {?>
                                                    <a href="#" onclick="viewDetailBill('<?php echo $data['id']; ?>')"
                                                       data-bs-toggle="tooltip"  title="<?php echo $this->lang->line('show'); ?>" ><?php echo $data['bill_no']; ?></a>
                                                   <?php }?>
                                            </td>
                                            <td>
                                                <?php echo $data['patient_name'] ?>
                                                <div class="rowoptionview">
                                                    <?php
if ($this->rbac->hasPrivilege('ambulance_call', 'can_edit')) {
            ?>
                                                        <a href="#" onclick="getRecord('<?php echo $data['id'] ?>')" class="btn btn-secondary btn-sm" data-bs-target="#editModal" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('edit'); ?>">
                                                            <i class="fa fa-pencil"></i>
                                                        </a>
                                                    <?php }?>
                                                    <?php if ($this->rbac->hasPrivilege('ambulance_call', 'can_delete')) {?>
                                                        <a class="btn btn-secondary btn-sm"  data-bs-toggle="tooltip" title="<?php echo $this->lang->line('delete'); ?>" onclick="delete_recordById('<?php echo base_url(); ?>admin/vehicle/deletecallambulance/<?php echo $data['id'] ?>', '<?php echo $this->lang->line('delete_message') ?>')">
                                                            <i class="fa fa-trash"></i>
                                                        </a>
                                                    <?php }?>
                                                </div>
                                            </td>
                                            <td><?php echo $data['contact_no'] ?></td>
                                            <td><?php echo $data['vehicle_no'] ?></td>
                                            <td><?php echo $data['vehicle_model']; ?></td>
                                            <td><?php echo $data['driver']; ?></td>
                                            <td><?php echo $data['address']; ?></td>
                                            <td><?php echo $this->customlib->YYYYMMDDHisTodateFormat($dat['date']);?></td>
                                            <td class="text-end"><?php echo $data['amount']; ?></td>
                                        </tr>
                                        <?php
$count++;
    }
}
?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
</div>

<div class="modal fade sh-modal sh-modal-branded" id="callModal" tabindex="-1" aria-labelledby="callModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="callModalLabel"><?php echo $this->lang->line('ambulance_call'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formcall" method="post" accept-charset="utf-8">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-sm-3">
                            <div class="mb-3">
                                <label class="form-label"><?php echo $this->lang->line('vehicle_model'); ?></label><small class="req"> *</small>
                                <select name="vehicle_no" class="form-control" onchange="getVehicleDetail(this.value)">
                                    <option value=""><?php echo $this->lang->line('select') ?></option>
                                    <?php foreach ($vehiclelist as $key => $vehicle) { ?>
                                        <option value="<?php echo $vehicle["id"] ?>"><?php echo $vehicle["vehicle_model"] . " - " . $vehicle["vehicle_no"] ?></option>
                                    <?php }?>
                                </select>
                                <span class="text-danger"><?php echo form_error('vehicle_no'); ?></span>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="mb-3">
                                <label class="form-label"><?php echo $this->lang->line('driver_name'); ?></label>
                                <input name="driver" id="driver_search" type="text" class="form-control" />
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="mb-3">
                                <label class="form-label"><?php echo $this->lang->line('date'); ?></label><small class="req"> *</small>
                                <input name="date" type="text" class="form-control datetime" />
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="mb-3">
                                <label class="form-label"><?php echo $this->lang->line('amount') . " (" . $currency_symbol . ")"; ?></label><small class="req"> *</small>
                                <input name="amount" type="text" class="form-control" />
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="mb-3">
                                <label class="form-label"><?php echo $this->lang->line('patient') . " " . $this->lang->line('name'); ?></label><small class="req"> *</small>
                                <input name="patient_name" type="text" class="form-control"/>
                                <span class="text-danger"><?php echo form_error('patient_name'); ?></span>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="mb-3">
                                <label class="form-label"><?php echo $this->lang->line('contact') . " " . $this->lang->line('no'); ?></label>
                                <input name="contact_no" type="text" class="form-control" />
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="mb-3">
                                <label class="form-label"><?php echo $this->lang->line('address'); ?></label>
                                <textarea class="form-control" name="address" rows="2"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                    <button type="submit" data-loading-text="<?php echo $this->lang->line('processing') ?>" id="formcallbtn" class="btn btn-info"><?php echo $this->lang->line('save'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade sh-modal sh-modal-branded" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel"><?php echo $this->lang->line('ambulance_call'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formedit" method="post" accept-charset="utf-8">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-sm-3">
                            <div class="mb-3">
                                <label class="form-label"><?php echo $this->lang->line('vehicle_no'); ?></label><small class="req"> *</small>
                                <select name="vehicle_no" id="vehicle_no" class="form-control" onchange="getVehicleDetail(this.value, 'vehicle_model', 'driver_name')">
                                    <option value=""><?php echo $this->lang->line('select') ?></option>
                                    <?php foreach ($vehiclelist as $key => $vehicle) { ?>
                                        <option value="<?php echo $vehicle["id"] ?>"><?php echo $vehicle["vehicle_model"] . " - " . $vehicle["vehicle_no"] ?></option>
                                    <?php }?>
                                </select>
                                <span class="text-danger"><?php echo form_error('vehicle_model'); ?></span>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="mb-3">
                                <label class="form-label"><?php echo $this->lang->line('driver_name'); ?></label>
                                <input name="driver_name" id="driver_name" type="text" class="form-control" value="<?php echo set_value('vehicle_model'); ?>"/>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="mb-3">
                                <label class="form-label"><?php echo $this->lang->line('date'); ?></label><small class="req"> *</small>
                                <input name="date" id="edit_date" type="text" class="form-control datetime" value="<?php echo set_value('amount'); ?>"/>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="mb-3">
                                <label class="form-label"><?php echo $this->lang->line('amount') . " (" . $currency_symbol . ")"; ?></label><small class="req"> *</small>
                                <input name="amount" id="amount" type="text" class="form-control" value="<?php echo set_value('amount'); ?>"/>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="mb-3">
                                <input name="id" id="id" type="hidden" value="<?php echo set_value('id'); ?>" />
                                <label class="form-label"><?php echo $this->lang->line('patient') . " " . $this->lang->line('name'); ?></label><small class="req"> *</small>
                                <input name="patient_name" id="patient_name" type="text" class="form-control" value="<?php echo set_value('patient_name'); ?>"/>
                                <span class="text-danger"><?php echo form_error('patient_name'); ?></span>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="mb-3">
                                <label class="form-label"><?php echo $this->lang->line('contact') . " " . $this->lang->line('no'); ?></label>
                                <input name="contact_no" id="contact_no" type="text" class="form-control" value="<?php echo set_value('contact_no'); ?>" />
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="mb-3">
                                <label class="form-label"><?php echo $this->lang->line('address'); ?></label>
                                <textarea class="form-control" name="address" id="address" rows="2"><?php echo set_value('address'); ?></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                    <button type="submit" data-loading-text="<?php echo $this->lang->line('processing') ?>" id="formeditbtn" class="btn btn-info"><?php echo $this->lang->line('save'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade sh-modal sh-modal-branded" id="viewModalBill" tabindex="-1" aria-labelledby="viewModalBillLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewModalBillLabel"><?php echo $this->lang->line('bill') . " " . $this->lang->line('details'); ?></h5>
                <div class="d-flex align-items-center gap-2" id="edit_deletebill"></div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="reportdata"></div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(function () {
        $('#easySelectable').easySelectable();

    })
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
                $("#formcall").on('submit', (function (e) {
                    $("#formcallbtn").btnLoading();
                    e.preventDefault();
                    $.ajax({
                        url: '<?php echo base_url(); ?>admin/vehicle/addCallAmbulance',
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
                            $("#formcallbtn").btnReset();
                        },
                        error: function () {
                            
                        }
                    });
                }));
            });

            function viewDetailBill(id) {
                $.ajax({
                    url: '<?php echo base_url() ?>admin/vehicle/getBillDetails/' + id,
                    type: "GET",
                    data: {id: id},
                    success: function (data) {
                        $('#reportdata').html(data);
                        $('#edit_deletebill').html("<?php if ($this->rbac->hasPrivilege('ambulance bill', 'can_view')) {?><a href='#' class='btn btn-sm btn-light' data-bs-toggle='tooltip' onclick='printData(" + id + ")'   title='<?php echo $this->lang->line('print'); ?>'><i class='fa fa-print'></i></a><?php }?><?php if ($this->rbac->hasPrivilege('ambulance bill', 'can_edit')) {?><a href='#' class='btn btn-sm btn-light' onclick='edit_bill(" + id + ")' data-bs-toggle='tooltip'  title='<?php echo $this->lang->line('edit'); ?>'><i class='fa fa-pencil'></i></a><?php }?><?php if ($this->rbac->hasPrivilege('ambulance bill', 'can_edit')) {?><a onclick='delete_bill(" + id + ")'  href='#'  class='btn btn-sm btn-light' data-bs-toggle='tooltip'  title='<?php echo $this->lang->line('delete'); ?>'><i class='fa fa-trash'></i></a><?php }?>");
                        holdModal('viewModalBill');
                    },
                });
            }
            function getRecord(id) {
                shModal('editModal').show();
                $.ajax({
                    url: '<?php echo base_url(); ?>admin/vehicle/editCall',
                    type: "POST",
                    data: {id: id},
                    dataType: 'json',
                    success: function (data) {
                        $("#id").val(data.id);
                        $("#vehicle_no").val(data.id);
                        $("#vehicle_model").val(data.vehicle_model);
                        $("#driver_name").val(data.driver);
                        $("#patient_name").val(data.patient_name);
                        $("#contact_no").val(data.contact_no);
                        $("#edit_date").val(data.date);
                        $("#address").val(data.address);
                        $("#amount").val(data.amount);
                    },
                });
            }

            function getVehicleDetail(id, vh = 'vehicle_model_search', dr = 'driver_search') {
                $.ajax({
                    url: '<?php echo base_url(); ?>admin/vehicle/getVehicleDetail',
                    type: "POST",
                    data: {id: id},
                    dataType: 'json',
                    success: function (data) {
                        console.log(data)
                        $("#" + dr).val(data.driver_name);
                        $("#" + vh).val(data.vehicle_model);
                    },
                });
            }

            $(document).ready(function (e) {
                $("#formedit").on('submit', (function (e) {
                    $("#formeditbtn").btnLoading();
                    e.preventDefault();
                    $.ajax({
                        url: '<?php echo base_url(); ?>admin/vehicle/updatecallambulance',
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

            function holdModal(modalId) {

                (function(){var _el=document.getElementById(modalId);if(_el)bootstrap.Modal.getOrCreateInstance(_el,{backdrop:'static',keyboard:false}).show();})();;
            }
</script>

