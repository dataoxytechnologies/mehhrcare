<div class="row">
            <div class="col-md-2">
                <?php $this->load->view('admin/referral/referralSidebar'); ?>
            </div>			
			<?php if ($this->rbac->hasPrivilege('referral_commission', 'can_view')) { ?>
            <div class="col-md-10">
                <!-- general form elements -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title titlefix"><?php echo $this->lang->line("referral_commission_list"); ?></h3>
                        <div class="d-flex gap-2 align-items-center flex-wrap float-end">
                        <?php if ($this->rbac->hasPrivilege('referral_commission', 'can_add')) { ?>
                                <a onclick="addCommissionModal()" class="btn btn-primary btn-sm addcommission"><i class="fa fa-plus"></i> <?php echo $this->lang->line('add_referral_commission'); ?></a>
                        <?php } ?>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive mailbox-messages overflow-visible-lg overflow-x-hidden">
                             <div class="download_label"><?php echo $this->lang->line('referral_commission_list'); ?></div>
                            <table class="table table-hover table-striped table-bordered example setup-referral-commission-fixed">
                                <thead>
                                    <tr>
                                        <th><?php echo $this->lang->line('category'); ?></th>
                                        <th><?php echo $this->lang->line('module_commission'); ?></th>
                                        <th class="text-end noExport"><?php echo $this->lang->line('action'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
if (empty($commission)) {

} else {
    foreach ($commission as $commission_value) {
        if (empty($commission_value->referral_commission)) {
            continue;
        }
        ?>
                                            <tr>
                                                <td><?php echo html_escape($commission_value->name); ?></td>
                                                <td>
                                                    <div>
                                                    <?php foreach ($commission_value->referral_commission as $key => $value) {?>
                                                       <div><?php echo $this->lang->line($value->name) . " - " . $value->commission . "%"; ?></div>
                                                    <?php }?>
                                                    </div>
                                                </td>
                                                <td class="text-end">
                                                    <div class="rowoptionview mt-mius0">
                                                        <?php if ($this->rbac->hasPrivilege('referral_commission', 'can_edit')) { ?>
                                                            <a href="javascript:void(0)" onclick="getRecord('<?php echo (int)$commission_value->id; ?>')" class="btn btn-default btn-sm" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('edit'); ?>">
                                                                <i class="fa fa-pencil"></i>
                                                            </a>
                                                        <?php } if ($this->rbac->hasPrivilege('referral_commission', 'can_delete')) { ?>
                                                            <a href="javascript:void(0)" class="btn btn-default btn-sm" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('delete'); ?>" onclick="delete_recordByIdReload('admin/referralcommission/delete/<?php echo (int)$commission_value->id; ?>', '<?php echo $this->lang->line('delete_confirm') ?>');">
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
			<?php } ?>			
            <!-- right column -->
        </div>
    


<div class="modal fade sh-modal sh-modal-accent" id="myModal" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myModalLabel"><?php echo $this->lang->line("add_commission"); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addcommission" method="post" accept-charset="utf-8">
                <div class="pup-scroll-area">
                    <div class="modal-body">
                        <div class="sh-form-card">
                            <div class="sh-card-header">
                                <span class="sh-card-header-title"><?php echo $this->lang->line('referral_commission'); ?></span>
                            </div>
                            <div class="p-3">
                                <div class="row">
                                    <div class="mb-3 col-12">
                                        <label class="form-label"><?php echo $this->lang->line('category'); ?></label><span class="req"> *</span>
                                        <select name="category" id="category" onchange="getRecordByCategory()" class="form-control">
                                            <option value=""><?php echo $this->lang->line("select_category"); ?></option>
                                            <?php foreach ($category as $value) {?>
                                                <option value="<?php echo $value['id']; ?>"><?php echo $value['name']; ?></option>
                                            <?php }?>
                                        </select>
                                    </div>
                                    <div class="mb-3 col-12">
                                        <label class="form-label"><?php echo $this->lang->line("standard_commission"); ?> (%)</label>
                                        <input type="text" class="form-control" name='commission' id="commission">
                                    </div>
                                    <div class="mb-0 col-12">
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <div><label class="form-label mb-0"><?php echo $this->lang->line("commission_for_modules"); ?></label> <span class="req">*</span></div>
                                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="apply_to_all()" autocomplete="off"><?php echo $this->lang->line("apply_to_all"); ?></button>
                                        </div>
                                        <?php foreach ($type as $key => $value) {?>
                                        <div class="row mb-1 align-items-center">
                                            <input type="hidden" name="referral_type_id[]" value="<?php echo $value['id']; ?>">
                                            <div class="col-7"><?php echo $this->lang->line($value['name']); ?></div>
                                            <div class="col-5"><input class="commissionInput form-control form-control-sm text-end" type="text" name="module_commission[]" id="type_id_<?php echo $value['id']; ?>" autocomplete="off"></div>
                                        </div>
                                        <?php }?>
                                        <span class="text-danger"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                    <button type="submit" id="addcommissionbtn" data-loading-text="<?php echo $this->lang->line('processing'); ?>" class="btn btn-info"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade sh-modal sh-modal-accent" id="myModalEdit" tabindex="-1" aria-labelledby="myModalEditLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myModalEditLabel"><?php echo $this->lang->line("edit_commission"); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editcommission" method="post" accept-charset="utf-8">
                <input type="hidden" name='category' id="categoryEdit">
                <div class="pup-scroll-area">
                    <div class="modal-body">
                        <div class="sh-form-card">
                            <div class="sh-card-header">
                                <span class="sh-card-header-title"><?php echo $this->lang->line('referral_commission'); ?></span>
                            </div>
                            <div class="p-3">
                                <div class="row">
                                    <div class="mb-3 col-12">
                                        <label class="form-label"><?php echo $this->lang->line('category'); ?></label>
                                        <select id="category_edit" class="form-control" disabled="disabled">
                                            <?php foreach ($category as $value) {?>
                                                <option value="<?php echo $value['id']; ?>"><?php echo $value['name']; ?></option>
                                            <?php }?>
                                        </select>
                                    </div>
                                    <div class="mb-3 col-12">
                                        <label class="form-label"><?php echo $this->lang->line("standard_commission"); ?> (%)</label>
                                        <input type="text" class="form-control" name='commission' id="commissionEdit">
                                    </div>
                                    <div class="mb-0 col-12">
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <div><label class="form-label mb-0"><?php echo $this->lang->line("commission_for_modules"); ?></label> <span class="req">*</span></div>
                                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="apply_to_all_edit()" autocomplete="off"><?php echo $this->lang->line("apply_to_all"); ?></button>
                                        </div>
                                        <?php foreach ($type as $key => $value) {?>
                                        <div class="row mb-1 align-items-center">
                                            <input type="hidden" name="referral_type_id[]" value="<?php echo $value['id']; ?>">
                                            <div class="col-7"><?php echo $this->lang->line($value['name']); ?></div>
                                            <div class="col-5"><input class="commissionInputEdit form-control form-control-sm text-end" type="text" name="module_commission[]" id="type_edit_id_<?php echo $value['id']; ?>" autocomplete="off"></div>
                                        </div>
                                        <?php }?>
                                        <span class="text-danger"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                    <button type="submit" data-loading-text="<?php echo $this->lang->line('processing'); ?>" id="editcommissionbtn" class="btn btn-info"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $(document).ready(function (e) {
        $('#addcommission').on('submit', (function (e) {
            $("#addcommissionbtn").btnLoading();
            e.preventDefault();
            $.ajax({
                url: '<?php echo base_url(); ?>admin/referralcommission/add',
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
                    $("#addcommissionbtn").btnReset();
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
            url: '<?php echo base_url(); ?>admin/referralcommission/get_by_category/' + id,
            type: "POST",
            dataType: "json",
            success: function (data) {
                if(data != ""){
                    $.each(data, function(i,item){4
                        $("#type_edit_id_"+item.referral_type_id).val(item.commission);
                        $("#categoryEdit").val(item.referral_category_id);
                        $("#category_edit").val(item.referral_category_id);
                    });
                }else{
                    $(".commissionInputEdit").val("");
                }
                }
        });
    }

    function getRecordByCategory() {
        id = $("#category").val();
        if(id!=''){
            $.ajax({
                url: '<?php echo base_url(); ?>admin/referralcommission/get_by_category/' + id,
                type: "POST",
                dataType: "json",
                success: function (data) {
                    if(data != ""){
                        $.each(data, function(i,item){
                            $("#type_id_"+item.referral_type_id).val(item.commission);
                        });
                    }else{
                        $(".commissionInput").val("");
                    }
                }
            });
        }else{
             $(".commissionInput").val("");
        }
    }

    $(document).ready(function (e) {
        $('#editcommission').on('submit', (function (e) {
            $("#editcommissionbtn").btnLoading();
            e.preventDefault();
            $.ajax({
                url: '<?php echo base_url(); ?>admin/referralcommission/add',
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
                    $("#editcommissionbtn").btnReset();
                },
                error: function () {
                    alert("Fail")
                }
            });
        }));
    });

    function apply_to_all(){
        commission = $("#commission").val();
        $(".commissionInput").val(commission);
    }

    function apply_to_all_edit(){
        commission = $("#commissionEdit").val();
        $(".commissionInputEdit").val(commission);
    }

    function addCommissionModal(){
        $('#myModal form')[0].reset();
        shModal("myModal").show();
    }
    
    $(document).ready(function (e) {
        modal_click_disabled('myModal');
        modal_click_disabled('myModalEdit');
    });
</script>