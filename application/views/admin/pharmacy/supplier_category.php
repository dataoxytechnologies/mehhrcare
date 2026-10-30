<div class="row">
            <?php $this->load->view('admin/pharmacy/pharmacyMasters') ?>
            <div class="col-md-10">              
                <div class="card" id="tachelist">
                    <div class="card-header ptbnull">
                        <h3 class="card-title titlefix"><?php echo $this->lang->line('supplier_list'); ?></h3>
                        <div class="d-flex gap-2 align-items-center flex-wrap float-end">
                            <?php if ($this->rbac->hasPrivilege('medicine_supplier', 'can_add')) { ?>
                                <a data-bs-toggle="modal" data-bs-target="#myModal" class="btn btn-primary btn-sm supplier"><i class="fa fa-plus"></i> <?php echo $this->lang->line('add_supplier'); ?></a> 
                            <?php } ?>    
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="mailbox-controls">
                        </div>
                        <div class="table-responsive mailbox-messages overflow-visible">
                            <div class="download_label"><?php echo $this->lang->line('supplier_list'); ?></div>
                            <table class="table table-striped table-bordered table-hover example">
                                <thead>
                                    <tr>
                                        <th><?php echo $this->lang->line('supplier_name'); ?></th>
                                        <th><?php echo $this->lang->line('supplier_contact'); ?></th>
                                        <th><?php echo $this->lang->line('contact_person_name'); ?></th>
                                        <th><?php echo $this->lang->line('contact_person_phone'); ?></th>
                                        <th><?php echo $this->lang->line("drug_license_number"); ?></th>
                                        <th><?php echo $this->lang->line('address'); ?></th>
                                        <th class="text-end noExport sh-th-140"><?php echo $this->lang->line('action'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $count = 1;
                                    foreach ($supplierCategory as $supplier) {
                                        ?>
                                        <tr>
                                            <td><?php echo $supplier['supplier']; ?></td>
                                            <td><?php echo $supplier['contact']; ?></td>
                                            <td><?php echo $supplier['supplier_person']; ?></td>
                                            <td><?php echo $supplier['supplier_person_contact']; ?></td>
                                            <td><?php echo $supplier['supplier_drug_licence']; ?></td>
                                            <td><?php echo $supplier['address']; ?></td>
                                            <td class="text-end">
                                                <div class="rowoptionview mt-mius0">
                                                    <?php if ($this->rbac->hasPrivilege('medicine_supplier', 'can_edit')) { ?>
                                                        <a href="javascript:void(0)" onclick="get(<?php echo $supplier['id'] ?>)" class="btn btn-default btn-sm" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('edit'); ?>">
                                                            <i class="fa fa-pencil"></i>
                                                        </a>
                                                    <?php } if ($this->rbac->hasPrivilege('medicine_supplier', 'can_delete')) { ?>
                                                        <a href="javascript:void(0)" class="btn btn-default btn-sm" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('delete'); ?>" onclick="delete_recordByIdReload('admin/medicinecategory/deletesupplier/<?php echo $supplier['id'] ?>', '<?php echo $this->lang->line('delete_confirm'); ?>')">
                                                            <i class="fa fa-trash"></i>
                                                        </a>
                                                    <?php } ?>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php
                                        $count++;
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div>
                        <div class="mailbox-controls">
                        </div>
                    </div>
                </div>
            </div> 
        </div>

<div class="modal fade sh-modal sh-modal-accent" id="myModal" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myModalLabel"><?php echo $this->lang->line('add_supplier'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formadd" action="<?php echo site_url('admin/medicinecategory/addsupplier') ?>" name="employeeform" method="post" accept-charset="utf-8">
                <div class="pup-scroll-area">
                    <div class="modal-body">
                        <div class="sh-form-card">
                            <div class="sh-card-header">
                                <span class="sh-card-header-title"><?php echo $this->lang->line('supplier'); ?></span>
                            </div>
                            <div class="p-3">
                                <div class="row">
                                    <div class="mb-3 col-md-6">
                                        <label class="form-label"><?php echo $this->lang->line('supplier_name'); ?></label><small class="req"> *</small>
                                        <input autofocus="" name="supplier_category" type="text" class="form-control" value="<?php if (isset($result)) { echo $result["supplier_category"]; } ?>">
                                        <span class="text-danger"><?php echo form_error('supplier_category'); ?></span>
                                    </div>
                                    <div class="mb-3 col-md-6">
                                        <label class="form-label"><?php echo $this->lang->line('supplier_contact'); ?></label>
                                        <input name="contact" type="text" class="form-control" value="<?php if (isset($result)) { echo $result["contact"]; } ?>">
                                        <span class="text-danger"><?php echo form_error('contact'); ?></span>
                                    </div>
                                    <div class="mb-3 col-md-6">
                                        <label class="form-label"><?php echo $this->lang->line('contact_person_name'); ?></label>
                                        <input name="supplier_person" type="text" class="form-control" value="<?php if (isset($result)) { echo $result["supplier_person"]; } ?>">
                                        <span class="text-danger"><?php echo form_error('supplier_person'); ?></span>
                                    </div>
                                    <div class="mb-3 col-md-6">
                                        <label class="form-label"><?php echo $this->lang->line('contact_person_phone'); ?></label>
                                        <input name="supplier_person_contact" type="text" class="form-control" value="<?php if (isset($result)) { echo $result["supplier_person_contact"]; } ?>">
                                        <span class="text-danger"><?php echo form_error('supplier_person_contact'); ?></span>
                                    </div>
                                    <div class="mb-3 col-md-6">
                                        <label class="form-label"><?php echo $this->lang->line('drug_license_number'); ?></label>
                                        <input name="supplier_drug_licence" type="text" class="form-control" value="<?php if (isset($result)) { echo $result["supplier_drug_licence"]; } ?>">
                                        <span class="text-danger"><?php echo form_error('supplier_drug_licence'); ?></span>
                                    </div>
                                    <div class="mb-0 col-md-6">
                                        <label class="form-label"><?php echo $this->lang->line('address'); ?></label>
                                        <input name="address" type="text" class="form-control" value="<?php if (isset($result)) { echo $result["address"]; } ?>">
                                        <span class="text-danger"><?php echo form_error('address'); ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                    <button type="submit" id="formaddbtn" data-loading-text="<?php echo $this->lang->line('processing') ?>" class="btn btn-info"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade sh-modal sh-modal-accent" id="editmyModal" tabindex="-1" aria-labelledby="editmyModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editmyModalLabel"><?php echo $this->lang->line('edit_supplier'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editformadd" action="<?php echo site_url('admin/medicinecategory/addsupplier') ?>" name="employeeform" method="post" accept-charset="utf-8">
                <input type="hidden" id="id" name="suppliercategoryid">
                <div class="pup-scroll-area">
                    <div class="modal-body">
                        <div class="sh-form-card">
                            <div class="sh-card-header">
                                <span class="sh-card-header-title"><?php echo $this->lang->line('supplier'); ?></span>
                            </div>
                            <div class="p-3">
                                <div class="row">
                                    <div class="mb-3 col-md-6">
                                        <label class="form-label"><?php echo $this->lang->line('supplier_name'); ?></label><small class="req"> *</small>
                                        <input autofocus="" id="supplier_category" name="supplier_category" type="text" class="form-control" value="<?php if (isset($result)) { echo $result["supplier_category"]; } ?>">
                                        <span class="text-danger"><?php echo form_error('supplier_category'); ?></span>
                                    </div>
                                    <div class="mb-3 col-md-6">
                                        <label class="form-label"><?php echo $this->lang->line('supplier_contact'); ?></label>
                                        <input id="contact" name="contact" type="text" class="form-control" value="<?php if (isset($result)) { echo $result["contact"]; } ?>">
                                        <span class="text-danger"><?php echo form_error('contact'); ?></span>
                                    </div>
                                    <div class="mb-3 col-md-6">
                                        <label class="form-label"><?php echo $this->lang->line('contact_person_name'); ?></label>
                                        <input id="supplier_person" name="supplier_person" type="text" class="form-control" value="<?php if (isset($result)) { echo $result["supplier_person"]; } ?>">
                                        <span class="text-danger"><?php echo form_error('supplier_person'); ?></span>
                                    </div>
                                    <div class="mb-3 col-md-6">
                                        <label class="form-label"><?php echo $this->lang->line('contact_person_phone'); ?></label>
                                        <input id="supplier_person_contact" name="supplier_person_contact" type="text" class="form-control" value="<?php if (isset($result)) { echo $result["supplier_person_contact"]; } ?>">
                                        <span class="text-danger"><?php echo form_error('supplier_person_contact'); ?></span>
                                    </div>
                                    <div class="mb-3 col-md-6">
                                        <label class="form-label"><?php echo $this->lang->line('drug_license_number'); ?></label>
                                        <input id="supplier_drug_licence" name="supplier_drug_licence" type="text" class="form-control" value="<?php if (isset($result)) { echo $result["supplier_drug_licence"]; } ?>">
                                        <span class="text-danger"><?php echo form_error('supplier_drug_licence'); ?></span>
                                    </div>
                                    <div class="mb-0 col-md-6">
                                        <label class="form-label"><?php echo $this->lang->line('address'); ?></label>
                                        <input id="address" name="address" type="text" class="form-control" value="<?php if (isset($result)) { echo $result["address"]; } ?>">
                                        <span class="text-danger"><?php echo form_error('address'); ?></span>
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
                    } else {
                        successMsg(data.message);
                        window.location.reload(true);
                    }
                    $("#formaddbtn").btnReset();
                },
                error: function () {

                }
            });
        }));
    });

    function get(id) {
        shModal('editmyModal').show();
        $.ajax({
            dataType: 'json',
            url: '<?php echo base_url(); ?>admin/medicinecategory/get_datasupplier/' + id,
            success: function (result) {
                $('#id').val(result.id);
                $('#supplier_category').val(result.supplier);
                $('#supplier_person').val(result.supplier_person);
                $('#supplier_person_contact').val(result.supplier_person_contact);
                $('#supplier_drug_licence').val(result.supplier_drug_licence);
                $('#contact').val(result.contact);
                $('#address').val(result.address);
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
                error: function () {

                }
            });
        }));
	});

$(".supplier").click(function(){
	$('#formadd').trigger("reset");
});

    $(document).ready(function () {
        modal_click_disabled('myModal');
        modal_click_disabled('editmyModal');
    });
</script>