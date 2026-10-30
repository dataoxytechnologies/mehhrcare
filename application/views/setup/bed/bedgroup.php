<div class="row">
    <div class="col-md-2">
        <?php $this->load->view('setup/bedsidebar'); ?>
    </div>
    <div class="col-md-10">
        <!-- general form elements -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title titlefix"><?php echo $this->lang->line('bed_group_list'); ?></h3>
                <div class="d-flex gap-2 align-items-center flex-wrap float-end">
                    <?php if ($this->rbac->hasPrivilege('bed_group', 'can_add')) { ?>
                        <a data-bs-toggle="modal" data-bs-target="#myModal" class="btn btn-primary btn-sm bedgroup"><i class="fa fa-plus"></i> <?php echo $this->lang->line('add_bed_group'); ?></a>
                    <?php } ?>
                </div>
            </div>

            <div class="card-body">
                <div class="download_label"><?php echo $this->lang->line('bed_group_list'); ?></div>
                <div class="table-responsive mailbox-messages overflow-visible-lg">
                    <table class="table table-hover table-striped table-bordered example">
                        <thead>
                            <tr>
                                <th><?php echo $this->lang->line('name'); ?></th>
                                <th><?php echo $this->lang->line('floor'); ?></th>
                                <th><?php echo $this->lang->line('description'); ?></th>
                                <th class="text-end noExport sh-th-140"><?php echo $this->lang->line('action'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if (empty($bedgroup_list)) {
                            } else {
                                foreach ($bedgroup_list as $key => $value) {
                                    ?>
                                    <tr>
                                        <td class="mailbox-name">
                                            <a href="#" data-bs-toggle="popover" class="detail_popover" title=""><?php echo $value['name'] ?></a>
                                        </td>
                                        <td class="mailbox-date">
                                            <?php echo $value["floor_name"] ?>
                                        </td>
                                        <td class="mailbox-date">
                                            <?php echo $value["description"] ?>
                                        </td>
                                        <td class="text-end">
                                            <div class="rowoptionview mt-mius0">
                                                <?php if ($this->rbac->hasPrivilege('bed_group', 'can_edit')) { ?>
                                                    <a data-bs-target="#myeditModal" onclick="edit(<?php echo $value['id']; ?>)" class="btn btn-default btn-sm" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('edit'); ?>">
                                                        <i class="fa fa-pencil"></i>
                                                    </a>
                                                <?php } ?>
                                                <?php if ($this->rbac->hasPrivilege('bed_group', 'can_delete')) { ?>
                                                    <a class="btn btn-default btn-sm" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('delete') ?>" onclick="delete_recordByIdReload('admin/setup/bedgroup/delete_bedgroup/<?php echo $value['id']; ?>', '<?php echo $this->lang->line('delete_confirm') ?>')">
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
                <h5 class="modal-title" id="myModalLabel"><?php echo $this->lang->line('add_bed_group'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addward" method="post" accept-charset="utf-8" enctype="multipart/form-data">
                <div class="pup-scroll-area">
                    <div class="modal-body">
                        <div class="sh-form-card">
                            <div class="sh-card-header">
                                <span class="sh-card-header-title"><?php echo $this->lang->line('details'); ?></span>
                            </div>
                            <div class="p-3">
                                <div class="row">
                                    <div class="mb-3 col-12">
                                        <label class="form-label"><?php echo $this->lang->line('name'); ?></label><small class="req"> *</small>
                                        <input name="name" placeholder="" type="text" class="form-control" value="<?php echo set_value('invoice_no'); ?>" />
                                        <span class="text-danger name"></span>
                                    </div>
                                    <div class="mb-3 col-12">
                                        <label class="form-label"><?php echo $this->lang->line('floor'); ?></label><small class="req"> *</small>
                                        <select name="floor" class="form-control">
                                            <option value=""><?php echo $this->lang->line('select') ?></option>
                                            <?php foreach ($floor as $key => $floorvalue) { ?>
                                                <option value="<?php echo $floorvalue["id"] ?>"><?php echo $floorvalue["name"] ?></option>
                                            <?php } ?>
                                        </select>
                                        <span class="text-danger floor"></span>
                                    </div>
                                    <div class="mb-3 col-12">
                                        <label class="form-label"><?php echo $this->lang->line('color'); ?></label>
                                        <input name="color" value="#f4f4f4" placeholder="" type="color" class="form-control" />
                                    </div>
                                    <div class="mb-3 col-12">
                                        <label class="form-label"><?php echo $this->lang->line('description'); ?></label>
                                        <textarea class="form-control sh-no-resize" name="description" placeholder="" rows="2"><?php echo set_value('description'); ?></textarea>
                                        <span class="text-danger description"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                    <button type="submit" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i>" id="addwardbtn" class="btn btn-info"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade sh-modal sh-modal-accent" id="myeditModal" tabindex="-1" aria-labelledby="myeditModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm400 modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myeditModalLabel"><?php echo $this->lang->line('edit_bed_group'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editbedgroup" method="post" accept-charset="utf-8" enctype="multipart/form-data">
                <div class="pup-scroll-area">
                    <div class="modal-body">
                        <div class="sh-form-card">
                            <div class="sh-card-header">
                                <span class="sh-card-header-title"><?php echo $this->lang->line('details'); ?></span>
                            </div>
                            <div class="p-3">
                                <div class="row">
                                    <div class="mb-3 col-12">
                                        <label class="form-label"><?php echo $this->lang->line('name'); ?></label><small class="req"> *</small>
                                        <input id="name" name="name" placeholder="" type="text" class="form-control" value="<?php echo set_value('name'); ?>" />
                                        <input type="hidden" id="id" name="id">
                                    </div>
                                    <div class="mb-3 col-12">
                                        <label class="form-label"><?php echo $this->lang->line('floor'); ?></label><small class="req"> *</small>
                                        <select name="floor" id="floor" class="form-control">
                                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                                            <?php foreach ($floor as $key => $floorvalue) { ?>
                                                <option value="<?php echo $floorvalue["id"] ?>"><?php echo $floorvalue["name"] ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    <div class="mb-3 col-12">
                                        <label class="form-label"><?php echo $this->lang->line('color'); ?></label>
                                        <input name="color" id="color" placeholder="" type="color" class="form-control" value="" />
                                    </div>
                                    <div class="mb-3 col-12">
                                        <label class="form-label"><?php echo $this->lang->line('description'); ?></label>
                                        <textarea class="form-control sh-no-resize" id="description" name="description" placeholder="" rows="2"><?php echo set_value('description'); ?></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                    <button type="submit" id="editbedgroupbtn" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i>" class="btn btn-info"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $(document).ready(function (e) {
        $("#addward").on('submit', (function (e) {
            e.preventDefault();
            $("#addwardbtn").button('loading');
            $.ajax({
                url: '<?php echo base_url(); ?>admin/setup/bedgroup/add_bed_group',
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
                    $("#addwardbtn").button('reset');
                },
                error: function () {
                    alert("Fail")
                }
            });
        }));
    });

    function edit(id) {
        var myeditModal = new bootstrap.Modal(document.getElementById('myeditModal'));
        myeditModal.show();
        $.ajax({
            url: '<?php echo base_url(); ?>admin/setup/bedgroup/getbedgroupdata/' + id,
            dataType: 'json',
            success: function (data) {
                console.log(data);
                $('#id').val(data.id);
                $('#name').val(data.name);
                $('#floor').val(data.floor);
                $('#color').val(data.color);
                $('#description').val(data.description);
            }
        });
    }

    $(document).ready(function (e) {
        $("#editbedgroup").on('submit', (function (e) {
            $("#editbedgroupbtn").button('loading');
            e.preventDefault();
            $.ajax({
                url: '<?php echo base_url(); ?>admin/setup/bedgroup/update_bedgroup',
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
                    $("#editbedgroupbtn").button('reset');
                },
                error: function () {
                    alert("Fail")
                }
            });
        }));
    });

    $(".bedgroup").click(function () {
        $('#addward').trigger("reset");
    });
</script>
