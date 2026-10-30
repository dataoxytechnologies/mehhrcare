<?php $currency_symbol = $this->customlib->getHospitalCurrencyFormat() ;?>
<div class="row">
            <div class="col-md-12">
                <!-- general form elements -->
                <div class="card">
                    <div class="card-header ptbnull">
                        <h3 class="card-title titlefix"><?php echo $this->lang->line("referral_person_list"); ?></h3>
                        <div class="d-flex gap-2 align-items-center flex-wrap float-end">
                        <?php if ($this->rbac->hasPrivilege('referral_person', 'can_add')) { ?>
                                <a onclick="addPersonModal()" class="btn btn-primary btn-sm addperson"><i class="fa fa-plus"></i> <?php echo $this->lang->line('add_referral_person'); ?></a>
                        <?php } ?>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="download_label"><?php echo $this->lang->line('referral_person_list'); ?></div>
                        <div class="table-responsive mailbox-messages overflow-visible-lg">
                            <table class="table table-hover table-striped table-bordered example">
                                <thead>
                                    <tr> 
                                        <th><?php echo $this->lang->line('referrer_name'); ?></th>
                                        <th><?php echo $this->lang->line('category'); ?></th>
                                        <th><?php echo $this->lang->line('commission'); ?></th>
                                        <th><?php echo $this->lang->line('referrer_contact'); ?></th>
                                        <th><?php echo $this->lang->line('contact_person_name'); ?></th>
                                        <th><?php echo $this->lang->line('contact_person_phone'); ?></th>
                                        <th><?php echo $this->lang->line('address'); ?></th>
                                        <?php if ($this->rbac->hasPrivilege('referral_person', 'can_edit') || $this->rbac->hasPrivilege('referral_person', 'can_delete')) { ?>
                                            <th class="text-end noExport"><?php echo $this->lang->line('action'); ?></th>
                                        <?php } ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
if (empty($person)) {

} else {
    foreach ($person as $person_key => $person_value) {
        ?>
                                            <tr>
                                                <td class="mailbox-name">
                                                    <a href="#" data-bs-toggle="popover" class="detail_popover"><?php echo html_escape($person_value->name); ?></a>
                                                </td>
                                                <td>
                                                    <?php echo html_escape($person_value->category_name); ?>
                                                </td>
                                                <td>
                                                    <?php
foreach ($person_value->commission as $value) {?>
                                                            <div><?php echo $this->lang->line($value->name) . " - " . $value->commission . "%"; ?></div>
                                                    <?php }
        ?>
                                                </td>
                                                <td><?php echo html_escape($person_value->contact); ?></td>
                                                <td><?php echo html_escape($person_value->person_name); ?></td>
                                                <td><?php echo html_escape($person_value->person_phone); ?></td>
                                                <td><?php echo html_escape($person_value->address); ?></td>
                                                <td class="text-end noExport">
                                                    <div class="d-inline-flex gap-1">
                                                    <?php if ($this->rbac->hasPrivilege('referral_person', 'can_edit')) { ?>
                                                        <a href="#" onclick="getRecord('<?php echo (int)$person_value->person_id; ?>')" class="btn btn-sm btn-outline-primary" data-bs-target="#myModalEdit" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('edit'); ?>">
                                                            <i class="fa fa-pencil"></i>
                                                        </a>
                                                    <?php } if ($this->rbac->hasPrivilege('referral_person', 'can_delete')) { ?>
                                                        <a class="btn btn-sm btn-outline-danger sh-cursor-pointer" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('delete') ?>" onclick="delete_personById('<?php echo (int)$person_value->person_id; ?>')">
                                                            <i class="fa fa-trash"></i>
                                                        </a>
                                                    <?php } ?>
                                                    </div>
                                                </td>
                                            </tr>
                                            <?php
}
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
    


<div class="modal fade sh-modal sh-modal-accent" id="myModal" tabindex="-1" aria-labelledby="addPersonLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addPersonLabel"><i class="fa fa-plus me-1"></i> <?php echo $this->lang->line("add_person"); ?></h5>
                <div class="d-flex align-items-center gap-2 ms-auto">
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            </div>
            <form id="addperson" method="post" accept-charset="utf-8" enctype="multipart/form-data">
                <div class="pup-scroll-area">
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-7">
                                <div class="sh-form-card h-100">
                                    <div class="sh-card-header">
                                        <span class="sh-card-header-title"><?php echo $this->lang->line('details'); ?></span>
                                    </div>
                                    <div class="p-2">
                                        <div class="row g-3">
                                            <div class="col-sm-6">
                                                <label class="form-label form-label-sm"><?php echo $this->lang->line("referrer_name"); ?> <small class="req">*</small></label>
                                                <input name="name" type="text" class="form-control form-control-sm" value="<?php echo set_value('name'); ?>">
                                            </div>
                                            <div class="col-sm-6">
                                                <label class="form-label form-label-sm"><?php echo $this->lang->line("referrer_contact"); ?></label>
                                                <input name="referrer_contact" type="text" class="form-control form-control-sm" value="<?php echo set_value('referrer_contact'); ?>">
                                            </div>
                                            <div class="col-sm-6">
                                                <label class="form-label form-label-sm"><?php echo $this->lang->line("contact_person_name"); ?></label>
                                                <input name="person_name" type="text" class="form-control form-control-sm" value="<?php echo set_value('person_name'); ?>">
                                            </div>
                                            <div class="col-sm-6">
                                                <label class="form-label form-label-sm"><?php echo $this->lang->line("contact_person_phone"); ?></label>
                                                <input name="person_phone" type="text" class="form-control form-control-sm" value="<?php echo set_value('person_phone'); ?>">
                                            </div>
                                            <div class="col-sm-6">
                                                <label class="form-label form-label-sm"><?php echo $this->lang->line('category'); ?> <small class="req">*</small></label>
                                                <select onchange="getDefaultCommission(this.value)" name="category" class="form-select form-select-sm">
                                                    <option value=""><?php echo $this->lang->line('select_category'); ?></option>
                                                    <?php foreach ($category as $value) { ?>
                                                    <option value="<?php echo $value['id']; ?>"><?php echo $value['name']; ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                            <div class="col-sm-6">
                                                <label class="form-label form-label-sm"><?php echo $this->lang->line("standard_commission") . ' (%)'; ?></label>
                                                <input type="text" class="form-control form-control-sm" name="commission" id="commission">
                                            </div>
                                            <div class="col-sm-12">
                                                <label class="form-label form-label-sm"><?php echo $this->lang->line("address"); ?></label>
                                                <input name="address" type="text" class="form-control form-control-sm" value="<?php echo set_value('address'); ?>">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="sh-form-card h-100">
                                    <div class="sh-card-header">
                                        <span class="sh-card-header-title"><?php echo $this->lang->line("commission_for_modules") . ' (%)'; ?> <small class="req">*</small></span>
                                        <button type="button" class="btn btn-sm btn-primary ms-auto" onclick="apply_to_all()" autocomplete="off">
                                            <?php echo $this->lang->line("apply_to_all"); ?>
                                        </button>
                                    </div>
                                    <div class="p-2">
                                        <?php foreach ($type as $key => $value) { ?>
                                        <div class="row align-items-center mb-2">
                                            <input type="hidden" name="referral_type_id[]" value="<?php echo $value['id']; ?>">
                                            <div class="col-7">
                                                <label class="form-label form-label-sm"><?php echo $this->lang->line($value['name']); ?></label>
                                            </div>
                                            <div class="col-5">
                                                <input class="commissionInput form-control form-control-sm text-end" type="text" name="module_commission[]" id="type_id_<?php echo $value['id']; ?>" autocomplete="off">
                                            </div>
                                        </div>
                                        <?php } ?>
                                        <span class="text-danger"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                    <button type="submit" id="addpersonbtn" data-loading-text="<?php echo $this->lang->line('processing'); ?>" class="btn btn-info"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade sh-modal sh-modal-accent" id="myModalEdit" tabindex="-1" aria-labelledby="editPersonLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editPersonLabel"><i class="fa fa-pencil me-1"></i> <?php echo $this->lang->line("edit_person"); ?></h5>
                <div class="d-flex align-items-center gap-2 ms-auto">
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            </div>
            <form id="editperson" method="post" accept-charset="utf-8" enctype="multipart/form-data">
                <input type="hidden" name="person_id" id="person_id">
                <div class="pup-scroll-area">
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-7">
                                <div class="sh-form-card h-100">
                                    <div class="sh-card-header">
                                        <span class="sh-card-header-title"><?php echo $this->lang->line('details'); ?></span>
                                    </div>
                                    <div class="p-2">
                                        <div class="row g-3">
                                            <div class="col-sm-6">
                                                <label class="form-label form-label-sm"><?php echo $this->lang->line("referrer_name"); ?> <small class="req">*</small></label>
                                                <input name="name" id="referrer_name" type="text" class="form-control form-control-sm">
                                            </div>
                                            <div class="col-sm-6">
                                                <label class="form-label form-label-sm"><?php echo $this->lang->line("referrer_contact"); ?></label>
                                                <input name="referrer_contact" id="referrer_contact" type="text" class="form-control form-control-sm">
                                            </div>
                                            <div class="col-sm-6">
                                                <label class="form-label form-label-sm"><?php echo $this->lang->line("contact_person_name"); ?></label>
                                                <input name="person_name" id="person_name" type="text" class="form-control form-control-sm">
                                            </div>
                                            <div class="col-sm-6">
                                                <label class="form-label form-label-sm"><?php echo $this->lang->line("contact_person_phone"); ?></label>
                                                <input name="person_phone" id="person_phone" type="text" class="form-control form-control-sm">
                                            </div>
                                            <div class="col-sm-6">
                                                <label class="form-label form-label-sm"><?php echo $this->lang->line('category'); ?> <small class="req">*</small></label>
                                                <select name="category" id="referrer_category" class="form-select form-select-sm">
                                                    <option value=""><?php echo $this->lang->line('select_category'); ?></option>
                                                    <?php foreach ($category as $value) { ?>
                                                    <option value="<?php echo $value['id']; ?>"><?php echo $value['name']; ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                            <div class="col-sm-6">
                                                <label class="form-label form-label-sm"><?php echo $this->lang->line('standard_commission') . ' (%)'; ?></label>
                                                <input type="text" class="form-control form-control-sm" name="commission" id="commissionEdit">
                                            </div>
                                            <div class="col-sm-12">
                                                <label class="form-label form-label-sm"><?php echo $this->lang->line("address"); ?></label>
                                                <input name="address" id="address" type="text" class="form-control form-control-sm">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="sh-form-card h-100">
                                    <div class="sh-card-header">
                                        <span class="sh-card-header-title"><?php echo $this->lang->line("commission_for_modules") . ' (%)'; ?> <small class="req">*</small></span>
                                        <button type="button" class="btn btn-sm btn-primary ms-auto" onclick="apply_to_all_edit()" autocomplete="off">
                                            <?php echo $this->lang->line("apply_to_all"); ?>
                                        </button>
                                    </div>
                                    <div class="p-2">
                                        <?php foreach ($type as $key => $value) { ?>
                                        <div class="row align-items-center mb-2">
                                            <input type="hidden" name="referral_type_id[]" value="<?php echo $value['id']; ?>">
                                            <div class="col-7">
                                                <label class="form-label form-label-sm"><?php echo $this->lang->line($value['name']); ?></label>
                                            </div>
                                            <div class="col-5">
                                                <input class="commissionInputEdit form-control form-control-sm text-end" type="text" name="module_commission[]" id="type_edit_id_<?php echo $value['id']; ?>" autocomplete="off">
                                            </div>
                                        </div>
                                        <?php } ?>
                                        <span class="text-danger"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                    <button type="submit" id="editpersonbtn" data-loading-text="<?php echo $this->lang->line('processing'); ?>" class="btn btn-info"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>

    $(document).ready(function (e) {
        $('#addperson').on('submit', (function (e) {
            $("#addpersonbtn").btnLoading();
            e.preventDefault();
            $.ajax({
                url: '<?php echo base_url(); ?>admin/referralperson/add',
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
                            $('.' + index).html(value);
                            message += value;
                        });
                        errorMsg(message);
                    } else {
                        successMsg(data.message);
                        window.location.reload(true);
                    }
                    $("#addpersonbtn").btnReset();
                },
                error: function () {
                    alert("<?php echo $this->lang->line('fail'); ?>")
                }
            });
        }));
    });

    function getRecord(id) {
        shModal('myModalEdit').show();
        $.ajax({
            url: '<?php echo base_url(); ?>admin/referralperson/get/' + id,
            type: "POST",
            dataType: "json",
            success: function (data) {
                $("#referrer_name").val(data.name);
                $("#person_id").val(id);
                $("#referrer_category").val(data.category_id);
                $("#referrer_contact").val(data.contact);
                $("#person_name").val(data.person_name);
                $("#person_phone").val(data.person_phone);
                $("#address").val(data.address);
                $("#commissionEdit").val(data.standard_commission);
                $.each(data.commission, function(i,item){
                    $("#type_edit_id_" + item.referral_type_id).val(item.commission);
                });
            },
            error: function () {
                alert("Fail")
            }
        });
    }

    $(document).ready(function (e) {
        $('#editperson').on('submit', (function (e) {
            $("#editpersonbtn").btnLoading();
            e.preventDefault();
            $.ajax({
                url: '<?php echo base_url(); ?>admin/referralperson/update',
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
                    $("#editpersonbtn").btnReset();
                },
                error: function () {
                    alert("Fail")
                }
            });
        }));
    });
 
</script>

<script>
    function apply_to_all(){
        commission = $("#commission").val();
        $(".commissionInput").val(commission);
    }
	
    function apply_to_all_edit(){
        commission = $("#commissionEdit").val();
        $(".commissionInputEdit").val(commission);
    }
</script>

<script type="text/javascript">
    function getDefaultCommission(id){
        if(id != ""){
            $.ajax({
                url: '<?php echo base_url(); ?>admin/referralcommission/get_by_category/' + id,
                type: "POST",
                dataType: "json",
                success: function (data) {
                    if(data.length === 0){
                        $(".commissionInput").val("");
                    }else{
                        $.each(data, function(i,item){
                            $("#type_id_" + item.referral_type_id).val(item.commission);
                        });
                    }
                },
                error: function () {
                    alert("Fail")
                }

            });
        }else{
            $(".commissionInput").val("");
        }
    }

    function addPersonModal(){
        $('#myModal form')[0].reset();
        shModal("myModal").show();
    }
    
    $(document).ready(function (e) {
        modal_click_disabled('myModal', 'myModalEdit');
    });
 
    function delete_personById(id){
        var conf = confirm("<?php echo $this->lang->line('delete_confirm');?>");
		if(conf == true){
			$.ajax({
				url: '<?php echo base_url(); ?>admin/referralperson/delete/' + id,
				type: "POST",
				dataType: "json",
				success: function (data) {
				successMsg(data.msg);
				window.location.reload(true); 
				},
				error: function () {
					alert("Fail")
				}
			});
		}
    }
</script>