<?php $currency_symbol = $this->customlib->getHospitalCurrencyFormat();  ?>
<div class="row">
            <div class="col-md-12">
                <!-- general form elements -->
                <div class="card">
                    <div class="card-header ptbnull">
                        <h3 class="card-title titlefix"><?php echo $this->lang->line('expense_list'); ?></h3>
                        <div class="d-flex gap-2 align-items-center flex-wrap float-end">
                            <?php if ($this->rbac->hasPrivilege('expense', 'can_add')) {?>
                                <a data-bs-toggle="modal" data-bs-target="#myModal" class="btn btn-primary btn-sm addexpense"><i class="fa fa-plus"></i>  <?php echo $this->lang->line('add_expense'); ?></a>
                            <?php }?>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive mailbox-messages overflow-visible-lg">
                         <table class="table table-hover table-striped table-bordered dt-list" data-export-title="<?php echo $this->lang->line('expense_list'); ?>">
                                <thead>
                                    <tr>
                                        <th><?php echo $this->lang->line('name'); ?></th>
                                        <th><?php echo $this->lang->line('invoice_number'); ?></th>
                                        <th><?php echo $this->lang->line('date'); ?></th>
                                        <th><?php echo $this->lang->line('description'); ?></th>
                                        <th><?php echo $this->lang->line('expense_head'); ?></th> 
                                        <th><?php echo $this->lang->line('generated_by'); ?></th> 
                                        <?php 
                                         if (!empty($fields)) {

                                                foreach ($fields as $fields_key => $fields_value) {
                                                    ?>
                                                    <th><?php echo $fields_value->name; ?></th>
                                                    <?php
                                                } 
                                            }
                                         ?> 
                                        <th class="text-end"><?php echo $this->lang->line('amount') . " (" . $currency_symbol . ")"; ?></th>
                                        <th class="noExport text-end"><?php echo $this->lang->line('action'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table><!-- /.table -->
                        </div><!-- /.mail-box-messages -->
                    </div><!-- /.card-body -->
                </div>
            </div><!--/.col (left) -->
        </div>
    

<div class="modal fade sh-modal sh-modal-accent" id="myModal" tabindex="-1" aria-labelledby="addExpenseLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addExpenseLabel"><?php echo $this->lang->line('add_expense'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addexpense" method="post" accept-charset="utf-8" enctype="multipart/form-data">
                <div class="pup-scroll-area">
                    <div class="modal-body">
                        <?php if ($this->session->flashdata('msg')) { echo $this->session->flashdata('msg'); $this->session->unset_userdata('msg'); } ?>
                        <?php if (isset($error_message)) { echo "<div class='alert alert-danger'>" . $error_message . "</div>"; } ?>
                        <?php echo $this->customlib->getCSRF(); ?>
                        <div class="sh-form-card">
                            <div class="sh-card-header">
                                <span class="sh-card-header-title"><?php echo $this->lang->line('details'); ?></span>
                            </div>
                            <div class="p-2">
                                <div class="row g-3">
                                    <div class="col-sm-6">
                                        <label class="form-label form-label-sm"><?php echo $this->lang->line('expense_head'); ?> <small class="req">*</small></label>
                                        <select autofocus id="exp_head_id" name="exp_head_id" class="form-select form-select-sm">
                                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                                            <?php foreach ($expheadlist as $exphead) { ?>
                                            <option value="<?php echo $exphead['id'] ?>"<?php if (set_value('exp_head_id') == $exphead['id']) echo ' selected'; ?>><?php echo html_escape($exphead['exp_category']) ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="form-label form-label-sm"><?php echo $this->lang->line('name'); ?> <small class="req">*</small></label>
                                        <input id="name" name="name" type="text" class="form-control form-control-sm" value="<?php echo set_value('name'); ?>">
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="form-label form-label-sm"><?php echo $this->lang->line('invoice_number'); ?></label>
                                        <input id="invoice_no" name="invoice_no" type="text" class="form-control form-control-sm" value="<?php echo set_value('invoice_no'); ?>">
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="form-label form-label-sm"><?php echo $this->lang->line('date'); ?> <small class="req">*</small></label>
                                        <input id="date" name="exdate" type="text" class="form-control form-control-sm date" value="<?php echo set_value('date', date($this->customlib->getHospitalDateFormat())); ?>" readonly autocomplete="off">
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="form-label form-label-sm"><?php echo $this->lang->line('amount') . " (" . $currency_symbol . ")"; ?> <small class="req">*</small></label>
                                        <input id="amount" name="amount" type="text" class="form-control form-control-sm" value="<?php echo set_value('amount'); ?>">
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="form-label form-label-sm"><?php echo $this->lang->line('attach_document'); ?></label>
                                        <input id="documents" name="documents" type="file" class="filestyle form-control form-control-sm" value="<?php echo set_value('documents'); ?>">
                                    </div>
                                    <div class="col-sm-12">
                                        <label class="form-label form-label-sm"><?php echo $this->lang->line('description'); ?></label>
                                        <textarea class="form-control form-control-sm" id="description" name="description" rows="3"><?php echo set_value('description'); ?></textarea>
                                    </div>
                                    <?php echo display_custom_fields('expenses'); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                    <button type="submit" data-loading-text="<?php echo $this->lang->line('processing'); ?>" id="addexpensebtn" class="btn btn-info"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade sh-modal sh-modal-accent" id="myModaledit" tabindex="-1" aria-labelledby="editExpenseLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editExpenseLabel"><?php echo $this->lang->line('edit_expense'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="pup-scroll-area">
                <div class="modal-body" id="edit_expensedata">
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        // #date auto-initialized via .date class + event delegation. Apply max=today restriction:
        var dateEl = document.getElementById('date');
        if (dateEl) {
            dateEl.addEventListener('focus', function() {
                if (dateEl._pickerInit) {
                    dateEl._pickerInit.updateOptions({
                        restrictions: { maxDate: new tempusDominus.DateTime() }
                    });
                }
            }, { once: true });
        }

        $("#btnreset").click(function () {
            $("#form1")[0].reset();
        });

    });
</script>
<script>
    $(document).ready(function () {
        document.querySelectorAll('.detail_popover').forEach(function (el) {
            new bootstrap.Popover(el, {
                placement: 'right',
                trigger: 'hover',
                container: 'body',
                html: true,
                content: function () {
                    var td = el.closest('td');
                    var inner = td ? td.querySelector('.fee_detail_popover') : null;
                    return inner ? inner.innerHTML : '';
                }
            });
        });
    });
    
    function delete_record(id) {
        if (confirm('<?php echo $this->lang->line('delete_confirm') ?>')) {
            $.ajax({
                url: '<?php echo base_url(); ?>admin/expense/delete/' + id,
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
    
    $(document).ready(function (e) {
        $("#addexpense").on('submit', (function (e) {
            e.preventDefault();
            $("#addexpensebtn").btnLoading();
            $.ajax({
                url: '<?php echo base_url(); ?>admin/expense/add',
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
                        $('.dt-list').DataTable().ajax.reload(); 						
						shModal('myModal').hide();
                    }
                    $("#addexpensebtn").btnReset();
                },
                error: function () {
                    alert("Fail");
                    $("#addexpensebtn").btnReset();
                },
                complete: function() {
                $("#addexpensebtn").btnReset();
               }
            });
        }));
    });

    function edit(id) {
        shModal('myModaledit').show();
        $.ajax({
            url: '<?php echo base_url(); ?>admin/expense/getDataByid/' + id,
            success: function (data) {
                $('#edit_expensedata').html(data);
            }
        });
    }

    $(".addexpense").click(function(){
        $('#addexpense').trigger("reset");  
        $(".dropify-clear").trigger("click");
    });

    $(document).ready(function (e) {
        modal_click_disabled('myModal', 'myModaledit');
    });
</script>

<!-- //========datatable start===== -->
<script type="text/javascript">
( function ( $ ) {
    'use strict';
    $(document).ready(function () {
        initDatatable('dt-list','admin/expense/getDatatable',[],[],100);
    });
} ( jQuery ) )
</script>
<!-- //========datatable end===== -->

<?php if ($this->input->get('action') === 'add'): ?>
<script>$(function(){ shModal('myModal').show(); shCleanUrlParam('action'); });</script>
<?php endif; ?>