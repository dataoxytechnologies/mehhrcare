<div class="row">
            <?php $this->load->view('admin/pharmacy/pharmacyMasters') ?>
            <div class="col-md-10">
                <div class="card" id="tachelist">
                    <div class="card-header ptbnull">
                        <h3 class="card-title titlefix"><?php echo $this->lang->line('medicine_dosage_list'); ?></h3>
                        <div class="d-flex gap-2 align-items-center flex-wrap float-end">
                            <?php if ($this->rbac->hasPrivilege('medicine_dosage', 'can_add')) { ?>
                                <a class="btn btn-primary btn-sm medicine"><i class="fa fa-plus"></i> <?php echo $this->lang->line('add_medicine_dosage'); ?></a>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="mailbox-controls">
                        </div>
                        <div class="table-responsive mailbox-messages overflow-visible">
                            <div class="download_label"><?php echo $this->lang->line('medicine_dosage_list'); ?></div>
                            <table class="table table-striped table-bordered table-hover example" >
                                <thead>
                                    <tr>
                                        <th><?php echo $this->lang->line('category_name'); ?></th>
                                        <th><?php echo $this->lang->line('dosage'); ?></th>
                                        <th><?php echo $this->lang->line('unit'); ?></th>
                                        <th class="text-end noExport sh-th-140"><?php echo $this->lang->line('action'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $count = 1;
                                    if (!empty($medicineDosage)) {
                                        foreach ($medicineDosage as $dosage) {
                                           $subcount = 1;
                                            foreach ($dosage as $key => $value) {

                                            ?>
                                            <tr>
                                                <td><?php if($subcount==1){ echo $value['medicine_category']; } ?></td>
                                                <td><?php echo $value['dosage']; ?></td>
                                                <td><?php echo $value['unit']; ?></td>
                                                <td class="text-end">
                                                    <div class="rowoptionview mt-mius0">
                                                        <?php if ($this->rbac->hasPrivilege('medicine_dosage', 'can_edit')) { ?>
                                                            <a href="javascript:void(0)" onclick="get(<?php echo $value['id'] ?>)" class="btn btn-default btn-sm" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('edit'); ?>">
                                                                <i class="fa fa-pencil"></i>
                                                            </a>
                                                        <?php } if ($this->rbac->hasPrivilege('medicine_dosage', 'can_delete')) { ?>
                                                            <a href="javascript:void(0)" class="btn btn-default btn-sm" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('delete'); ?>" onclick="delete_medicine_dosage('<?php echo $value['id'] ?>')">
                                                                <i class="fa fa-trash"></i>
                                                            </a>
                                                        <?php } } ?>
                                                    </div>
                                                </td>
                                            </tr>
                                            <?php
                                            $subcount++;
                                        }
                                        $count++;
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="mailbox-controls"></div>
                </div>
            </div>
        </div>

<div class="modal fade sh-modal sh-modal-accent" id="myModal" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myModalLabel"><?php echo $this->lang->line('add_medicine_dosage'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formadd" action="<?php echo site_url('admin/medicinedosage/add') ?>" name="employeeform" method="post" accept-charset="utf-8">
                <div class="pup-scroll-area">
                    <div class="modal-body">
                        <div class="sh-form-card">
                            <div class="sh-card-header">
                                <span class="sh-card-header-title"><?php echo $this->lang->line('medicine_dosage'); ?></span>
                            </div>
                            <div class="p-3">
                                <div class="row">
                                    <div class="mb-3 col-12">
                                        <label class="form-label"><?php echo $this->lang->line('medicine_category'); ?></label><small class="req"> *</small>
                                        <select name="medicine_category" class="form-control">
                                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                                            <?php foreach ($medicineCategory as $key => $catvalue) { ?>
                                                <option value="<?php echo $catvalue["id"] ?>"><?php echo $catvalue["medicine_category"] ?></option>
                                            <?php } ?>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('medicine_category'); ?></span>
                                    </div>
                                </div>
                                <div class="row mb-1">
                                    <div class="col-sm-5">
                                        <label class="form-label"><?php echo $this->lang->line('dosage'); ?></label><small class="req"> *</small>
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="form-label"><?php echo $this->lang->line('unit'); ?></label><small class="req"> *</small>
                                    </div>
                                </div>
                                <div id="dose_fields"></div>
                                <div class="mt-2 text-end">
                                    <a class="btn btn-primary btn-sm add-record" data-added="0"><i class="fa fa-plus"></i> <?php echo $this->lang->line('add'); ?></a>
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

<div class="modal fade sh-modal sh-modal-accent" id="editmyModal" tabindex="-1" aria-labelledby="editmyModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm400 modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editmyModalLabel"><?php echo $this->lang->line('edit_medicine_dosage'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editformadd" action="<?php echo site_url('admin/medicinedosage/add') ?>" name="employeeform" method="post" accept-charset="utf-8">
                <input type="hidden" id="id" name="medicinecategoryid">
                <input type="hidden" name="dosageid" id="dosageid">
                <div class="pup-scroll-area">
                    <div class="modal-body">
                        <div class="sh-form-card">
                            <div class="sh-card-header">
                                <span class="sh-card-header-title"><?php echo $this->lang->line('medicine_dosage'); ?></span>
                            </div>
                            <div class="p-3">
                                <div class="row">
                                    <div class="mb-3 col-12">
                                        <label class="form-label"><?php echo $this->lang->line('category_name'); ?></label><small class="req"> *</small>
                                        <select name="medicine_category" id="medicine_category" onchange="editMedicineName(this.value)" class="form-control">
                                            <option value=""><?php echo $this->lang->line('select') ?></option>
                                            <?php foreach ($medicineCategory as $key => $catvalue) { ?>
                                                <option value="<?php echo $catvalue["id"] ?>"><?php echo $catvalue["medicine_category"] ?></option>
                                            <?php } ?>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('medicine_category'); ?></span>
                                    </div>
                                    <div class="mb-3 col-6">
                                        <label class="form-label"><?php echo $this->lang->line('dosage'); ?></label><small class="req"> *</small>
                                        <input name="dosage[]" id="dosage" type="text" class="form-control">
                                    </div>
                                    <div class="mb-0 col-6">
                                        <label class="form-label"><?php echo $this->lang->line('unit'); ?></label><small class="req"> *</small>
                                        <select name="unit[]" id="unit" class="form-control">
                                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                                            <?php foreach ($unitname as $key => $value) { ?>
                                                <option value="<?php echo $value['id']; ?>"><?php echo $value['unit_name']; ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                    <button type="submit" id="editformaddbtn" data-loading-text="<?php echo $this->lang->line('processing'); ?>" class="btn btn-info"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    <?php
    // Build unit options HTML safely and encode for use in JavaScript
    $unit_listval = '<option value="">' . $this->lang->line('select') . '</option>';
    foreach ($unitname as $key => $value) {
        $unit_listval .= '<option value="' . $value['id'] . '" >' . htmlspecialchars($value['unit_name'], ENT_QUOTES) . '</option>';
    }
    ?>
    // Safe JS variable — json_encode handles all special characters (quotes, backslashes, etc.)
    var unitOptionsHtml = <?php echo json_encode($unit_listval); ?>;

    function add_more() {
        var data_id = makeid(8);
        var row = '<div class="row dosage_row" id="fields_data' + data_id + '">'
                +   '<div class="col-sm-5">'
                +     '<div class="mb-3">'
                +       '<input autofocus="" name="dosage[]" placeholder="" type="text" class="form-control"/>'
                +     '</div>'
                +   '</div>'
                +   '<div class="col-sm-6">'
                +     '<div class="mb-3">'
                +       '<select autofocus="" name="unit[]" class="form-control">' + unitOptionsHtml + '</select>'
                +     '</div>'
                +   '</div>'
                +   '<div class="col-sm-1">'
                +     '<div class="mb-3">'
                +       '<button type="button" class="btn btn-sm btn-outline-secondary closebtn delete_row" data-row-id="' + data_id + '" autocomplete="off"><i class="fa fa-remove"></i></button>'
                +     '</div>'
                +   '</div>'
                + '</div>';
        $('#dose_fields').append(row);
    }

    function makeid(length) {
        var result = '';
        var characters = '0123456789';
        var charactersLength = characters.length;
        for (var i = 0; i < length; i++) {
            result += characters.charAt(Math.floor(Math.random() * charactersLength));
        }
        return result;
    }

    function addModal() {
        $('#formadd').trigger("reset");
        $(".dosage_row").remove();
        add_more();
        shModal("myModal").show();
    }

    $(document).on('click', '.add-record', function () {
        add_more();
    });

    $(document).on('click', '.delete_row', function () {
        if (confirm("<?php echo $this->lang->line('are_you_sure_to_delete_this'); ?>")) {
            var record_id = $(this).data('row-id');
            $('#fields_data' + record_id).remove();
        }
    });

    $(".medicine").click(function () {
        addModal();
    });

    $(document).ready(function (e) {
        $(".select2").select2();
    });

    $(document).ready(function () {
        modal_click_disabled('myModal');
        modal_click_disabled('editmyModal');
    });

    $(document).ready(function (e) {
        $('#formadd').on('submit', (function (e) {
            $("#formaddbtn").btnLoading();
            e.preventDefault();
            $.ajax({
                url: $(this).attr('action'),
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
                    } else if (data.status == 2) {
                        errorMsg(data.error);
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

    function get(id) {
        $.ajax({
            dataType: 'json',
            url: '<?php echo base_url(); ?>admin/medicinedosage/get_data/' + id,
            success: function (result) {
                console.log(result);
                $('#dosageid').val(result.id);
                $('#dosage').val(result.dosage);
                $('#unit option[value="' + result.units_id + '"]').prop('selected', true);
                $('#medicine_category').val(result.medicine_category_id);
                shModal('editmyModal').show();
            },
            error: function (xhr) {
                alert("Error occured.please try again");
            }
        });
    }

    $(document).ready(function (e) {
        $('#editformadd').on('submit', (function (e) {
            $("#editformaddbtn").btnLoading();
            e.preventDefault();
            $.ajax({
                url: $(this).attr('action'),
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
                    $("#editformaddbtn").btnReset();
                },
                error: function () {}
            });
        }));
    });

    function delete_medicine_dosage(id) {
        delete_recordByIdReload('admin/medicinedosage/delete/' + id, '<?php echo $this->lang->line('delete_confirm'); ?>');
    }
</script>
