<!-- Content Wrapper. Contains page content -->
<!-- Content Header (Page header) -->
    <!-- Main content -->
    <div class="row">
            <div class="col-md-12">
                <!-- Horizontal Form -->
                <div class="card">
                    <div class="card-header ptbnull">
                        <h3 class="card-title titlefix"><?php echo $this->lang->line('issue_item_list'); ?></h3>
                        <div class="d-flex gap-2 align-items-center flex-wrap float-end">
                            <?php if ($this->rbac->hasPrivilege('issue_item', 'can_add')) {?>
                                <a href="" data-bs-toggle="modal" data-bs-target="#myModal" class="btn btn-primary btn-sm addissueitem"><i class="fa fa-plus"></i> <?php echo $this->lang->line('add_issue_item'); ?></a>
                            <?php }?>
                        </div>
                    </div><!-- /.card-header -->
                    <div class="card-body">
                        <?php $logUser = $this->customlib->getLoggedInUserData(); ?>
                        <div class="table-responsive mailbox-messages">
                            <div class="download_label"><?php echo $this->lang->line('issue_item_list'); ?></div>
                            <table id="issueitem-list-table" class="table table-hover table-striped table-bordered ajaxlist" data-export-title="<?php echo $this->lang->line('issue_item_list'); ?>">
                                <thead>
                                    <tr>
                                        <th><?php echo $this->lang->line('item'); ?></th>
                                        <th><?php echo $this->lang->line('item_category'); ?></th>
                                        <th><?php echo $this->lang->line('issue_return'); ?></th>
                                        <th><?php echo $this->lang->line('issue_to'); ?></th>
                                        <th><?php echo $this->lang->line('issued_by'); ?></th>
                                        <th><?php echo $this->lang->line('quantity'); ?></th>
                                        <th><?php echo $this->lang->line('note'); ?></th>
                                        <th class="text-center noExport"><?php echo $this->lang->line('status'); ?></th>
                                        <th class="text-end noExport"><?php echo $this->lang->line('action'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                   
                                </tbody>
                            </table><!-- /.table -->
                        </div><!-- /.mail-box-messages -->
                    </div><!-- /.card-body -->
                </div>
            </div><!--/.col (right) -->
        </div>
    


<div class="modal fade sh-modal sh-modal-accent" id="confirm-delete" tabindex="-1" aria-labelledby="confirmReturnLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmReturnLabel"><?php echo $this->lang->line('confirm_return'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="item_issue_id" name="item_issue_id" value="">
                <div class="d-flex align-items-start gap-2 mb-3">
                    <i class="fa fa-exclamation-circle text-warning mt-1"></i>
                    <p class="mb-0 small text-muted"><?php echo $this->lang->line('are_you_sure_to_return_this_item'); ?></p>
                </div>
                <div class="border rounded overflow-hidden">
                    <div class="d-flex align-items-center gap-2 px-3 py-2 border-bottom">
                        <i class="fa fa-fw fa-cube text-muted"></i>
                        <span class="text-muted small flex-grow-1"><?php echo $this->lang->line('item'); ?></span>
                        <span class="fw-semibold small" id="modal_item"></span>
                    </div>
                    <div class="d-flex align-items-center gap-2 px-3 py-2 border-bottom">
                        <i class="fa fa-fw fa-tag text-muted"></i>
                        <span class="text-muted small flex-grow-1"><?php echo $this->lang->line('item_category'); ?></span>
                        <span class="fw-semibold small" id="modal_item_cat"></span>
                    </div>
                    <div class="d-flex align-items-center gap-2 px-3 py-2">
                        <i class="fa fa-fw fa-sort-numeric-asc text-muted"></i>
                        <span class="text-muted small flex-grow-1"><?php echo $this->lang->line('quantity'); ?></span>
                        <span class="fw-semibold small" id="modal_item_quantity"></span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                <a class="btn btn-info cfees btn-ok" data-loading-text="<i class='fa fa-spinner fa-spin '></i> <?php echo $this->lang->line('return'); ?>"><?php echo $this->lang->line('return'); ?></a>
            </div>
        </div>
    </div>
</div>

<div class="modal fade sh-modal sh-modal-accent" id="myModal" tabindex="-1" aria-labelledby="addIssueItemLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addIssueItemLabel"><?php echo $this->lang->line('add_issue_item'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="form1" action="<?php echo base_url() ?>admin/issueitem/add" name="itemstockform" method="post" accept-charset="utf-8" enctype="multipart/form-data">
                <div class="pup-scroll-area">
                    <div class="modal-body">
                        <?php echo $this->customlib->getCSRF(); ?>
                        <div class="sh-form-card mb-3">
                            <div class="sh-card-header">
                                <span class="sh-card-header-title"><?php echo $this->lang->line('details'); ?></span>
                            </div>
                            <div class="p-2">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label form-label-sm"><?php echo $this->lang->line('user_type'); ?> <small class="req">*</small></label>
                                        <select name="account_type" onchange="getIssueUser(this.value)" class="form-select form-select-sm ac_type">
                                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                                            <?php foreach ($roles as $role_key => $role_value) { ?>
                                                <option value="<?php echo $role_value['id']; ?>"><?php echo $role_value['name'] ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label form-label-sm"><?php echo $this->lang->line('issue_to'); ?> <small class="req">*</small></label>
                                        <select id="issue_to" name="issue_to" class="form-select form-select-sm">
                                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label form-label-sm"><?php echo $this->lang->line('issued_by'); ?> <small class="req">*</small></label>
                                        <input id="issue_by" name="issue_by" type="text" class="form-control form-control-sm" value="<?php echo $logUser['username']; ?>">
                                        <span class="text-danger"><?php echo form_error('issue_by'); ?></span>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label form-label-sm"><?php echo $this->lang->line('issue_date'); ?> <small class="req">*</small></label>
                                        <input id="issue_date" name="issue_date" type="text" class="form-control form-control-sm date" value="<?php echo set_value('issue_date'); ?>" readonly>
                                        <span class="text-danger"><?php echo form_error('issue_date'); ?></span>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label form-label-sm"><?php echo $this->lang->line('return_date'); ?></label>
                                        <input id="return_date" name="return_date" type="text" class="form-control form-control-sm date" value="<?php echo set_value('return_date'); ?>" readonly>
                                        <span class="text-danger"><?php echo form_error('return_date'); ?></span>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label form-label-sm"><?php echo $this->lang->line('note'); ?></label>
                                        <textarea name="note" class="form-control form-control-sm" id="note" rows="1"><?php echo set_value('note'); ?></textarea>
                                        <span class="text-danger"><?php echo form_error('note'); ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="sh-form-card">
                            <div class="sh-card-header">
                                <span class="sh-card-header-title"><?php echo $this->lang->line('item'); ?></span>
                            </div>
                            <div class="p-2">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label form-label-sm"><?php echo $this->lang->line('item_category'); ?> <small class="req">*</small></label>
                                        <select id="item_category_id" name="item_category_id" class="form-select form-select-sm">
                                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                                            <?php foreach ($itemcatlist as $item_category) { ?>
                                                <option value="<?php echo $item_category['id'] ?>"<?php if (set_value('item_category_id') == $item_category['id']) echo ' selected'; ?>><?php echo $item_category['item_category'] ?></option>
                                            <?php } ?>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('item_category_id'); ?></span>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label form-label-sm"><?php echo $this->lang->line('item'); ?> <small class="req">*</small></label>
                                        <select id="item_id" name="item_id" class="form-select form-select-sm">
                                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('item_id'); ?></span>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label form-label-sm"><?php echo $this->lang->line('quantity'); ?> <small class="req">*</small></label>
                                        <input class="form-control form-control-sm" name="quantity">
                                        <div id="div_avail" class="small text-muted mt-1">
                                            <span><?php echo $this->lang->line('available_quantity'); ?>: </span>
                                            <span id="item_available_quantity">0</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                    <button type="submit" id="form1btn" data-loading-text="<?php echo $this->lang->line('processing'); ?>" class="btn btn-info"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.querySelectorAll('.detail_popover').forEach(function(el) {
        new bootstrap.Popover(el, {
            placement: 'right',
            trigger: 'hover',
            container: 'body',
            html: true,
            content: function() {
                var td = el.closest('td');
                var inner = td ? td.querySelector('.fee_detail_popover') : null;
                return inner ? inner.innerHTML : '';
            }
        });
    });
</script>

<script type="text/javascript">
    $(document).ready(function () {
        $('#confirm-delete').on('show.bs.modal', function (e) {
            $('#item_issue_id').val("");
            $('#modal_item_quantity,#modal_item,#modal_item_cat').text("");
            var item_issue_id = $(e.relatedTarget).data('item');
            var item_category = $(e.relatedTarget).data('category');
            var quantity = $(e.relatedTarget).data('quantity');
            var item_name = $(e.relatedTarget).data('item_name');
            $('#item_issue_id').val(item_issue_id);
            $('#modal_item_cat').text(item_category);
            $('#modal_item').text(item_name);
            $('#modal_item_quantity').text(quantity);
        });
		
        modal_click_disabled('confirm-delete');
		
    });

    $(document).on('change', '#item_category_id', function (e) {
        var item_category_id = $(this).val();
        populateItem(0, item_category_id);
    });

    $(document).on('click', '.btn-ok', function () {
        var $this = $('.btn-ok');
        $this.btnLoading();
        var item_issue_id = $('#item_issue_id').val();

        $.ajax(
                {
                    url: "<?php echo site_url('admin/issueitem/returnItem') ?>",
                    type: "POST",
                    data: {'item_issue_id': item_issue_id},
                    dataType: 'Json',
                    success: function (data, textStatus, jqXHR)
                    {
                        if (data.status == "fail") {
                            errorMsg(data.message);
                        } else {
                            successMsg(data.message);
                            shModal('confirm-delete').hide();
                            location.reload();
                        }

                        $this.btnReset();
                    },
                    error: function (jqXHR, textStatus, errorThrown)
                    {
                        $this.btnReset();
                    }
                });
    });
</script>

<script type="text/javascript">
    $(document).ready(function (e) {

        $('#form1').on('submit', (function (e) {
            $("#form1btn").btnLoading();
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
                    $("#form1btn").btnReset();
                },
                error: function () {
                    alert("Fail")
                }
            });
        }));
    });

    var base_url = '<?php echo base_url() ?>';
    function populateItem(item_id_post, item_category_id_post) {
        if (item_category_id_post != "") {
            $('#item_id').html("");
            var div_data = '<option value=""><?php echo $this->lang->line('select'); ?></option>';
            $.ajax({
                type: "GET",
                url: base_url + "admin/itemstock/getItemByCategory",
                data: {'item_category_id': item_category_id_post},
                dataType: "json",
                success: function (data) {
                    $.each(data, function (i, obj)
                    {
                        var select = "";
                        if (item_id_post == obj.id) {
                            var select = "selected=selected";
                        }
                        div_data += "<option value=" + obj.id + " " + select + ">" + obj.name + "</option>";
                    });
                    $('#item_id').append(div_data);
                }
            });
        }
    }

    $(document).on('change', '#item_id', function (e) {
        $('#div_avail').hide();
        var item_id = $(this).val();
        availableQuantity(item_id);
    });

    function availableQuantity(item_id) {
        if (item_id != "") {
            $('#item_available_quantity').html("");
            var div_data = '';
            $.ajax({
                type: "GET",
                url: base_url + "admin/item/getAvailQuantity",
                data: {'item_id': item_id},
                dataType: "json",
                success: function (data) {

                    $('#item_available_quantity').html(data.available);
                    $('#div_avail').show();
                }
            });
        }
    }

    $("input[name=account_type]:radio").change(function () {
        var user = $('input[name=account_type]:checked').val();
        getIssueUser(user);
    });

    function getIssueUser(usertype) {
        $('#issue_to').html("");
        var div_data = "";
        $.ajax({
            type: "POST",
            url: base_url + "admin/issueitem/getUser",
            data: {'usertype': usertype},
            dataType: "json",
            success: function (data) {
                $.each(data.result, function (i, obj)
                {
                    if (data.usertype == "admin") {
                        name = obj.username;
                    } else {
                        name = obj.name + " " + obj.surname;
                    }
                    div_data += "<option value=" + obj.id + ">" + name + " (" + obj.employee_id +")</option>";
                });
                $('#issue_to').append(div_data);
            }
        });
    }

    function delete_record(id) {
            if (confirm('<?php echo $this->lang->line('delete_confirm') ?>')) {
                $.ajax({
                    url: '<?php echo base_url(); ?>admin/issueitem/delete/' + id,
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

$(".addissueitem").click(function(){
    $('#form1').trigger("reset");
    $('#issue_to').val("");
});
    
    $(document).ready(function (e) {
        modal_click_disabled('myModal');
    });
</script>

<!-- //========datatable start===== -->
<script type="text/javascript">
( function ( $ ) {
    'use strict';
    $(document).ready(function () {
        initDatatable('ajaxlist','admin/issueitem/getissueitemdatatable',[],[],100,[
            { "bSortable": false, "aTargets": [-1], "sClass": "text-end" },
            { "bSortable": false, "aTargets": [-2], "sClass": "text-center" },
            { "aTargets": [0], "sWidth": "200px" },
            { "aTargets": [1], "sWidth": "180px" },
            { "aTargets": [2], "sWidth": "250px", "sClass": "text-nowrap" },
            { "aTargets": [3], "sWidth": "120px" },
            { "aTargets": [4], "sWidth": "120px" },
            { "aTargets": [5], "sWidth": "100px", "sClass": "text-center" },
            { "aTargets": [6], "sWidth": "150px" }
        ]);
        $('#ajaxlist').on('draw.dt', function () {
            $('[data-bs-toggle="tooltip"]').tooltip();
        });
    });
} ( jQuery ) )
</script>
<!-- //========datatable end===== -->