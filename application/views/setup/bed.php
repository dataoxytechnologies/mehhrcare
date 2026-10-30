<div class="row">
    <div class="col-md-2">
        <?php $this->load->view('setup/bedsidebar'); ?>
    </div>
    <div class="col-md-10">
        <!-- general form elements -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title titlefix"><?php echo $this->lang->line('bed_list'); ?></h3>
                <div class="d-flex gap-2 align-items-center flex-wrap float-end">
                    <?php if ($this->rbac->hasPrivilege('bed', 'can_add')) { ?>
                        <a data-bs-toggle="modal" data-bs-target="#myModal" class="btn btn-primary btn-sm addbed"><i class="fa fa-plus"></i> <?php echo $this->lang->line('add_bed'); ?></a>
                    <?php } ?>
                </div>
            </div>
            <div class="card-body">
                <div class="download_label"><?php echo $this->lang->line('bed_list'); ?></div>
                <div class="table-responsive mailbox-messages overflow-visible">
                    <table class="table table-hover table-striped table-bordered example">
                        <thead>
                            <tr>
                                <th><?php echo $this->lang->line('name'); ?></th>
                                <th><?php echo $this->lang->line('bed_type'); ?></th>
                                <th><?php echo $this->lang->line('bed_group'); ?></th>
                                <th><?php echo $this->lang->line('used'); ?></th>
                                <th class="text-end noExport sh-th-140"><?php echo $this->lang->line('action'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if (empty($bed_list)) {
                            } else {
                                foreach ($bed_list as $key => $value) {
                                    ?>
                                    <tr>
                                        <td class="mailbox-name">
                                            <a href="#" data-bs-toggle="popover" class="detail_popover"><?php echo $value['name'] ?></a>
                                        </td>
                                        <td><?php echo $value['bed_type_name']; ?></td>
                                        <td><?php echo $value['bedgroup'] . " - " . $value['floor_name']; ?></td>
                                        <td>
                                            <?php if ($value['is_active'] != "unused") { ?>
                                                <i class="fa fa-check-square-o"></i><span class="d-none"><?php echo $this->lang->line('used'); ?></span>
                                            <?php } ?>
                                        </td>
                                        <td class="text-end">
                                            <div class="rowoptionview mt-mius0">
                                                <?php if ($this->rbac->hasPrivilege('bed', 'can_edit')) { ?>
                                                    <a href="javascript:void(0)" onclick="getRecord('<?php echo $value['id'] ?>')" class="btn btn-default btn-sm" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('edit'); ?>">
                                                        <i class="fa fa-pencil"></i>
                                                    </a>
                                                <?php } ?>
                                                <?php if ($this->rbac->hasPrivilege('bed', 'can_delete')) { ?>
                                                    <a class="btn btn-default btn-sm" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('delete') ?>" onclick="delete_recordByIdReload('admin/setup/bed/delete/<?php echo $value['id']; ?>', '')">
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

<div class="modal fade sh-modal sh-modal-accent" id="myModal" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm400 modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myModalLabel"><?php echo $this->lang->line('add_bed'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addbed" method="post" accept-charset="utf-8" enctype="multipart/form-data">
                <div class="pup-scroll-area">
                    <div class="modal-body">
                        <div class="sh-form-card">
                            <div class="sh-card-header">
                                <span class="sh-card-header-title"><?php echo $this->lang->line('details'); ?></span>
                            </div>
                            <div class="p-3">
                                <div class="row" id="edit_bedtypedata">
                                    <div class="mb-3 col-12">
                                        <label class="form-label"><?php echo $this->lang->line('name'); ?></label><small class="req"> *</small>
                                        <input name="name" placeholder="" type="text" class="form-control" value="<?php echo set_value('name'); ?>" />
                                    </div>
                                    <div class="mb-3 col-12">
                                        <label class="form-label"><?php echo $this->lang->line('bed_type'); ?></label><small class="req"> *</small>
                                        <select name="bed_type" id="bed_type" class="form-control">
                                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                                            <?php foreach ($bedtype_list as $value) { ?>
                                                <option value="<?php echo $value['id']; ?>"><?php echo $value['name']; ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    <div class="mb-3 col-12">
                                        <label class="form-label"><?php echo $this->lang->line('bed_group'); ?></label><small class="req"> *</small>
                                        <select name="bed_group" id="bed_group" class="form-control">
                                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                                            <?php foreach ($bedgroup_list as $bedg) { ?>
                                                <option value="<?php echo $bedg['id']; ?>"><?php echo $bedg['name'] . " - " . $bedg['floor_name']; ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="mark_as_unused">
                                            <label class="form-check-label"><?php echo $this->lang->line('not_available_for_use'); ?></label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                    <button type="submit" id="addbedbtn" data-loading-text="<?php echo $this->lang->line('processing'); ?>" class="btn btn-info"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade sh-modal sh-modal-accent" id="myModalEdit" tabindex="-1" aria-labelledby="myModalEditLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm400 modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myModalEditLabel"><?php echo $this->lang->line('edit_bed'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editbed" method="post" accept-charset="utf-8" enctype="multipart/form-data">
                <div class="pup-scroll-area">
                    <div class="modal-body">
                        <div class="sh-form-card">
                            <div class="sh-card-header">
                                <span class="sh-card-header-title"><?php echo $this->lang->line('details'); ?></span>
                            </div>
                            <div class="p-3">
                                <div class="row" id="edit_bedtypedata">
                                    <div class="mb-3 col-12">
                                        <label class="form-label"><?php echo $this->lang->line('name'); ?></label><small class="req"> *</small>
                                        <input id="name" name="name" placeholder="" type="text" class="form-control" value="<?php echo set_value('name'); ?>" />
                                        <input id="bedid" name="bedid" placeholder="" type="hidden" />
                                        <input id="bedstatus" name="bedstatus" placeholder="" type="hidden" />
                                    </div>
                                    <div class="mb-3 col-12">
                                        <label class="form-label"><?php echo $this->lang->line('bed_type'); ?></label><small class="req"> *</small>
                                        <select name="bed_type" id="bedtype" class="form-control">
                                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                                            <?php foreach ($bedtype_list as $value) { ?>
                                                <option value="<?php echo $value['id']; ?>"><?php echo $value['name']; ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    <div class="mb-3 col-12">
                                        <label class="form-label"><?php echo $this->lang->line('bed_group'); ?></label><small class="req"> *</small>
                                        <select name="bed_group" id="bedgroup" class="form-control">
                                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                                            <?php foreach ($bedgroup_list as $value) { ?>
                                                <option value="<?php echo $value['id']; ?>"><?php echo $value['name'] . " - " . $value['floor_name']; ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-check">
                                            <input id="mark_as_unused" class="form-check-input" type="checkbox" name="mark_as_unused">
                                            <label class="form-check-label" for="mark_as_unused"><?php echo $this->lang->line('not_available_for_use'); ?></label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                    <button type="submit" data-loading-text="<?php echo $this->lang->line('processing'); ?>" id="editbedbtn" class="btn btn-info"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

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

    function getbedcat(cate) {
        $.ajax({
            url: '<?php echo base_url() ?>admin/setup/bed/getbed_categore_type/' + cate,
            success: function (data) {
                $('#bed_categore_type').html(data);
            }
        });
    }

    $(document).ready(function (e) {
        $('#addbed').on('submit', (function (e) {
            $("#addbedbtn").button('loading');
            e.preventDefault();
            $.ajax({
                url: '<?php echo base_url(); ?>admin/setup/bed/add',
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
                    $("#addbedbtn").button('reset');
                },
                error: function () {
                    alert("<?php echo $this->lang->line('fail'); ?>")
                }
            });
        }));
    });

    $(document).ready(function (e) {
        $('#editbed').on('submit', (function (e) {
            $("#editbedbtn").button('loading');
            e.preventDefault();
            $.ajax({
                url: '<?php echo base_url(); ?>admin/setup/bed/update',
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
                    $("#editbedbtn").button('reset');
                },
                error: function () {
                    alert("Fail")
                }
            });
        }));
    });

    function getRecord(id) {
        var myModalEdit = new bootstrap.Modal(document.getElementById('myModalEdit'));
        myModalEdit.show();
        $.ajax({
            url: '<?php echo base_url(); ?>admin/setup/bed/get/' + id,
            type: "POST",
            dataType: "json",
            success: function (data) {
                $("#name").val(data.name);
                $("#bedid").val(id);
                $("#bedstatus").val(data.is_active);
                $("#bedtype").val(data.bed_type_id);
                $("#bedgroup").val(data.bed_group_id);
                if (data.is_active == 'unused') {
                    $('#mark_as_unused').prop('checked', true);
                }
            },
            error: function () {
                alert("Fail")
            }
        })
    }

    $(".addbed").click(function () {
        $('#addbed').trigger("reset");
    });
</script>
