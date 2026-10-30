<div class="row">  
            <?php $this->load->view('admin/pharmacy/pharmacyMasters') ?>
            <div class="col-md-10">              
                <div class="card" id="tachelist">
                    <div class="card-header ptbnull">
                        <h3 class="card-title titlefix"><?php echo $this->lang->line('dosage_interval_list'); ?></h3>
                        <div class="d-flex gap-2 align-items-center flex-wrap float-end">
                            <?php if ($this->rbac->hasPrivilege('dosage_interval', 'can_add')) { ?>
                                <a onclick="add()" class="btn btn-primary btn-sm medicine"><i class="fa fa-plus"></i> <?php echo $this->lang->line('add_dosage_interval'); ?></a> 
                            <?php } ?>    
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="mailbox-controls">
                        </div>
                        <div class="table-responsive mailbox-messages overflow-visible">
                            <div class="download_label"><?php echo $this->lang->line('dosage_interval_list'); ?></div>
                            <table class="table table-striped table-bordered table-hover ajaxlist" data-export-title="<?php echo $this->lang->line('dosage_interval_list'); ?>">
                                <thead>
                                    <tr>
                                        <th><?php echo $this->lang->line('name'); ?></th>
                                        <th class="text-end noExport sh-th-140"><?php echo $this->lang->line('action'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                   
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

<!-- add multiple data modal -->
<div class="modal fade sh-modal sh-modal-accent" id="addmultiplerow" tabindex="-1" aria-labelledby="addmultiplerowLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addmultiplerowLabel"><?php echo $this->lang->line('add_dosage_interval') ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="form_add_multiple" action="<?php echo site_url('admin/medicinedosage/add_multiple_interval') ?>" method="post" accept-charset="utf-8">
                <div class="pup-scroll-area">
                    <div class="modal-body">
                        <div class="sh-form-card">
                            <div class="sh-card-header">
                                <span class="sh-card-header-title"><?php echo $this->lang->line('add_dosage_interval') ?></span>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-hover mb-0" id="tableID_vitals">
                                    <thead>
                                        <tr>
                                            <th><?php echo $this->lang->line('interval'); ?><small class="req"> *</small></th>
                                            <th class="sh-w-46"></th>
                                        </tr>
                                    </thead>
                                    <tbody id="set_row"></tbody>
                                </table>
                            </div>
                            <div class="p-3 pt-2 text-end">
                                <a class="btn btn-primary btn-sm" onclick="addrow()"><i class="fa fa-plus"></i> <?php echo $this->lang->line('add'); ?></a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                    <button type="submit" id="formadd_multiple_btn" data-loading-text="<?php echo $this->lang->line('processing'); ?>" class="btn btn-info"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- add multiple data modal -->

<!-- edit single row data modal -->
<div class="modal fade sh-modal sh-modal-accent" id="myModal" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm400 modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myModalLabel"><?php echo $this->lang->line('add_dosage_interval') ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formadd" action="<?php echo site_url('admin/medicinedosage/add_interval') ?>" method="post" accept-charset="utf-8">
                <input type="hidden" name="id" id="id">
                <div class="pup-scroll-area">
                    <div class="modal-body">
                        <div class="sh-form-card">
                            <div class="sh-card-header">
                                <span class="sh-card-header-title"><?php echo $this->lang->line('dosage_interval_list') ?></span>
                            </div>
                            <div class="p-3">
                                <div class="row">
                                    <div class="mb-0 col-12">
                                        <label class="form-label"><?php echo $this->lang->line('interval'); ?></label><small class="req"> *</small>
                                        <input name="name" id="name" type="text" class="form-control">
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
<!-- edit single row data modal -->

<script type="text/javascript">
( function ( $ ) { 
    'use strict';
    $(document).ready(function () {
        initDatatable('ajaxlist','admin/medicinedosage/get_doseIntervallist');
        $(document).on('draw.dt', '.ajaxlist', function() {
            $('[data-bs-toggle="tooltip"]').each(function() {
                bootstrap.Tooltip.getOrCreateInstance(this);
            });
        });
    });
} ( jQuery ) )
</script>

<script> 
$('#myModal').on('hidden.bs.modal', function (e) {
$('#formadd').trigger("reset");
$('#myModal .modal-title').html('<?php echo $this->lang->line('add_dosage_interval') ?>');
})

function add(){
    shModal('addmultiplerow').show();
    $('#form_add_multiple').trigger("reset");
    $('#set_row').html('');
    total_rows = 0;
    addrow();
}

$(document).ready(function (e) {
modal_click_disabled('myModal');

$(".select2").select2();
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
        $.ajax({
            dataType: 'JSON',
            url: base_url+'admin/medicinedosage/get_doseintervalbyid/' + id,
            beforeSend: function() {               
            },
            success: function(result) {
					$('#id').val(result.id);
					$('#name').val(result.name);                        
					$('#myModal .modal-title').html('<?php echo $this->lang->line('edit_dosage_interval') ?>');
					shModal('myModal').show();
            },
            error: function(xhr) { // if error occured
                alert("Error occured.please try again");               
            },
            complete: function() {
             
            }
        });
    }

function delete_intervalById(id){  
    if (confirm('<?php echo $this->lang->line("delete_confirm"); ?>')) {
                    $.ajax({
                        url: "<?php echo base_url();?>admin/medicinedosage/delete_doseInterval/"+id,
                        success: function (res) {
                            successMsg('<?php echo $this->lang->line('delete_confirm')?>');
                            window.location.reload(true);
                        }
                    });
                }
}

$(".medicine").click(function(){
	$('#formadd').trigger("reset");
});
</script>

<script>
var total_rows=0;
addrow();
function addrow(){
    var id = total_rows+1;   
    var div = "<tr id='name_row_"+id+"'><td><input type='hidden' name='total_rows[]' value='" + id + "'><input name='name_"+id+"' id='name_"+id+"' type='text' class='form-control' placeholder='<?php echo $this->lang->line('interval'); ?>' /></td><td class='text-center align-middle sh-w-46'><button type='button' data-rowid='"+id+"' class='closebtn delete_row btn btn-danger btn-sm'><i class='fa fa-remove'></i></button></td></tr>";
    $('#set_row').append(div);   
    total_rows++;      
}
    
$(document).on('click', '.delete_row', function (e) {
    if (confirm("<?php echo $this->lang->line('are_you_sure_to_delete_this'); ?>")) {
        var del_row_id = $(this).data('rowid');
        $("#name_row_" + del_row_id).remove();
    }
});

$(document).ready(function (e) {
        $('#form_add_multiple').on('submit', (function (e) {
            $("#formadd_multiple_btn").btnLoading();
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
                    }else if(data.status==2){

                        errorMsg(data.error);
                    } else {
                        successMsg(data.message);
                        window.location.reload(true);
                    }
                    $("#formadd_multiple_btn").btnReset();
                },
                error: function () {

                }
            });
        }));
    });
	
$(document).ready(function () {
    modal_click_disabled('addmultiplerow');
});
</script>