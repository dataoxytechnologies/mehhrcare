<!-- Main content -->
    <div class="row">
            <div class="col-md-2">
                <div class="card border0">
                    <?php $this->load->view("admin/charges/sidebar");?>
                </div>
            </div>
            <div class="col-md-10">
                <div class="card" id="tachelist">
                    <div class="card-header ptbnull">
                        <h3 class="card-title titlefix"><?php echo $this->lang->line('charge_type_list'); ?></h3>
                        <div class="d-flex gap-2 align-items-center flex-wrap float-end">
                            <?php if ($this->rbac->hasPrivilege('charge_type', 'can_add')) {?>
                                <a data-bs-toggle="modal" data-bs-target="#myModal" class="btn btn-primary btn-sm charge_type"><i class="fa fa-plus"></i> <?php echo $this->lang->line('add_charge_type'); ?></a>
                            <?php }?>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="mailbox-controls">
                        </div>
                        <div class="table-responsive mailbox-messages overflow-visible-lg">
                            <div class="download_label"><?php echo $this->lang->line('charge_category_list'); ?></div>
                            <table class="table table-striped table-bordered table-hover example" >
                                <thead>
                                    <tr>
                                        <th><?php echo $this->lang->line('charge_type'); ?></th>
                                        <?php foreach ($charge_type_modules as $module_shortcode => $module_name) {?>
                                            <th class="sh-th-60 text-center text-wrap text-break sh-fs-11"><?=$module_name;?></th>
                                        <?php }?>
                                        <th class="text-end noExport sh-th-90"><?php echo $this->lang->line('action'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $count = 1;
                                    foreach ($resultlist as $key => $chargetype) {
                                        ?>
                                         <tr>
                                            <td><?php  echo $chargetype['charge_type']; ?></td>                                            
                                            <?php foreach ($charge_type_modules as $module_shortcode => $module_name) {   ?>
                                            <td class="text-center align-middle"><input type="checkbox" <?php echo "onclick=updateChargeTypeModule(" . $chargetype['id'] . ",'" . $module_shortcode . "') ";
        if (in_array($chargetype['id'], $module_data[$module_shortcode])) {
            echo "checked ";
        }
        ?> /></td>
                                            <?php }?>                                            
                                            <td class="text-end">
										<?php if ($this->rbac->hasPrivilege('charge_type', 'can_edit')) {?>
                                                <a  class="btn btn-secondary btn-sm editcharge" data-record-id='<?php echo $chargetype['id'] ?>' data-bs-toggle="tooltip" title="<?php echo $this->lang->line('edit'); ?>" ;>
                                                        <i class="fa fa-pencil"></i>
                                                    </a>
										<?php } ?>
                                                <?php
if ($chargetype['is_default'] != 'yes') {
        if ($this->rbac->hasPrivilege('charge_type', 'can_delete')) {?>
                                                    <a  class="btn btn-secondary btn-sm" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('delete'); ?>" onclick="deleteChargeType('<?php echo $chargetype['id'] ?>')";>
                                                        <i class="fa fa-trash"></i>
                                                    </a>
                                                <?php }}?>
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
                    <div class="">
                        <div class="mailbox-controls">
                        </div>
                    </div>
                </div>
            </div>
        </div>

<div class="modal fade sh-modal sh-modal-accent" id="myModal" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm400 modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myModalLabel"><?php echo $this->lang->line('add_charge_type'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formadd" action="<?php echo site_url('admin/chargetype/add') ?>" name="employeeform" method="post" accept-charset="utf-8">
                <div class="pup-scroll-area">
                    <div class="modal-body">
                        <div class="sh-form-card">
                            <div class="sh-card-header">
                                <span class="sh-card-header-title"><?php echo $this->lang->line('charge_type'); ?></span>
                            </div>
                            <div class="p-3">
                                <div class="row">
                                    <div class="mb-3 col-12">
                                        <label class="form-label"><?php echo $this->lang->line('charge_type'); ?></label><small class="req"> *</small>
                                        <input autofocus="" id="type" name="charge_type" type="text" class="form-control" value="<?php if (isset($result)) { echo $result["name"]; } ?>" />
                                        <span class="text-danger"><?php echo form_error('name'); ?></span>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label"><?php echo $this->lang->line('module'); ?></label><small class="req"> *</small>
                                        <div class="mt-1">
                                            <?php foreach ($charge_type_modules as $module_shortcode => $module_name) { ?>
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" name="charge_module[]" value="<?= $module_shortcode; ?>" id="module_<?= $module_shortcode; ?>">
                                                <label class="form-check-label" for="module_<?= $module_shortcode; ?>"><?php echo $module_name; ?></label>
                                            </div>
                                            <?php } ?>
                                        </div>
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

<div class="modal fade sh-modal sh-modal-accent" id="editmyModal" tabindex="-1" aria-labelledby="editmyModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editmyModalLabel"><?php echo $this->lang->line('edit_charge_category'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editformadd" action="<?php echo site_url('admin/chargecategory/add') ?>" name="employeeform" method="post" accept-charset="utf-8"  enctype="multipart/form-data">
                <div class="modal-body">
                        <div class="mb-3">
                            <label><?php echo $this->lang->line('name'); ?></label><small class="req"> *</small>
                            <input  id="type1"  name="name"  type="text" class="form-control" value="<?php
if (isset($result)) {
    echo $result["name"];
}
?>" />
                            <span class="text-danger"><?php echo form_error('name'); ?></span>
                        </div>
                        <div class="mb-3">
                            <label><?php echo $this->lang->line('description'); ?></label>
                            <small class="req"> *</small>
                            <textarea name="description" id="description1" class="form-control"><?php
if (isset($result)) {
    echo $result["description"];
}
?></textarea>
                            <span class="text-danger"><?php echo form_error('description'); ?></span>
                        </div>
                        <div class="mb-3">
                            <label for="pwd"><?php echo $this->lang->line('charge_type'); ?></label>
                            <small class="req"> *</small>
                            <select name="charge_type" id="charge_type1" class="form-control">
                                <option value=""><?php echo $this->lang->line('select') ?></option>
                                <?php foreach ($charge_type as $charge_key => $charge_value) {  ?>
                                    <option value="<?php echo $charge_key; ?>" <?php if ((isset($result['charge_type'])) && ($result['charge_type'] == $charge_key)) {
                                        echo "selected";
                                        }
                                        ?>><?php echo $charge_value; ?></option>
                                <?php }?>
                            </select>
                            <input type="hidden" id="chargecategory_id" name="id" >
                            <span class="text-danger"><?php echo form_error('charge_type'); ?></span>
                        </div>
                    </div>   
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                            <button type="submit" data-loading-text="<?php echo $this->lang->line('processing') ?>" id="editformaddbtn" class="btn btn-info float-end"><i class="fa fa-check-circle"></i>  <?php echo $this->lang->line('save'); ?></button>
                        </div>
                </form>
        </div>
    </div>
</div>

<div class="modal fade sh-modal sh-modal-accent" id="editchargeModal" tabindex="-1" aria-labelledby="editchargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm400 modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editchargeModalLabel"><?php echo $this->lang->line('edit_charge_type'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editform" action="<?php echo site_url('admin/chargetype/edit') ?>" name="employeeform" method="post" accept-charset="utf-8">
                <input type="hidden" id="editchargeid" name="editchargeid" value="">
                <div class="pup-scroll-area">
                    <div class="modal-body">
                        <div class="sh-form-card">
                            <div class="sh-card-header">
                                <span class="sh-card-header-title"><?php echo $this->lang->line('charge_type'); ?></span>
                            </div>
                            <div class="p-3">
                                <div class="row">
                                    <div class="mb-0 col-12">
                                        <label class="form-label"><?php echo $this->lang->line('name'); ?></label><small class="req"> *</small>
                                        <input id="editchargetype" name="editchargetype" type="text" class="form-control" value="">
                                        <span class="text-danger"><?php echo form_error('name'); ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                    <button type="submit" id="editformbtn" data-loading-text="<?php echo $this->lang->line('processing') ?>" class="btn btn-info"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
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

    function deleteChargeType(id) {
        var msg = '<?php echo $this->lang->line("delete_charge_category_message"); ?>';
        if(confirm(msg)){
             var url = 'admin/chargetype/delete/'+id;
              $.ajax({
                 url: baseurl+url,
                 dataType: 'json',
                 beforeSend: function() {

                },
                 success: function (res) {
                    successMsg(res.msg);

                    window.location.reload(true);
                        },
                        error: function(xhr) { // if error occured
                   alert("Something went wrong");

            },
            complete: function() {


            }
            })
        }
    }

    $(document).ready(function (e) {
        $('#editformadd').on('submit', (function (e) {
            e.preventDefault();
            $("#editformaddbtn").btnLoading();
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

    $(".charge_type").click(function(){
        $('#formadd').trigger("reset");
    });

    function updateChargeTypeModule(charge_type, module_shortcode){
        $.ajax({
            url: "<?=base_url('admin/chargetype/updateChargeTypeModule');?>",
            type: "POST",
            data: {charge_type:charge_type,module_shortcode:module_shortcode},
            dataType: 'json',
            success: function (data) {
                if(data.status=="success"){
                    successMsg(data.message)
                }
            },
        });
    }

    $(document).ready(function (e) {
        modal_click_disabled('myModal');
    });
</script>
<script>
    $(document).on('click','.editcharge',function(){    
            
            var $this = $(this);
            var recordId = $this.data('recordId');          
          
            $.ajax({
                url: base_url+'admin/chargetype/getchargetype',
                type: "POST",
                data: {'id':recordId},
                dataType: 'json',
                 beforeSend: function() {
                    $this.btnLoading();
                    
                },
                success: function(res) {   
                    
                        shModal('editchargeModal').show();
                        $("#editchargetype").val(res.result.charge_type);
                        $("#editchargeid").val(res.result.id);
                  
                  $this.btnReset();
                },
                   error: function(xhr) { // if error occured
                   alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");
                 
            },
            complete: function() {
                  $this.btnReset();
            
            }
            });
            
        });
</script>
<script>
    $(document).ready(function (e) {
        $('#editform').on('submit', (function (e) {
            $("#editformbtn").btnLoading();
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
                    $("#editformbtn").btnReset();
                },
                error: function () {

                }
            });
        }));
    });
    </script>