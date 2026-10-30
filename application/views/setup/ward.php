<div class="row">
    <div class="col-md-2">
        <?php $this->load->view('setup/bedsidebar'); ?>
    </div>
    <div class="col-md-10">
        <!-- general form elements -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title titlefix"><?php echo $this->lang->line('ward') . " " . $this->lang->line('list'); ?></h3>
                <div class="d-flex gap-2 align-items-center flex-wrap float-end">
                    <a data-bs-toggle="modal" data-bs-target="#myModal" class="btn btn-primary btn-sm"><i class="fa fa-plus"></i> <?php echo $this->lang->line('add'); ?></a>
                </div>
            </div>
            <div class="card-body">
                <div class="download_label"><?php echo $this->lang->line('ward') . " " . $this->lang->line('list'); ?></div>
                <div class="table-responsive mailbox-messages">
                    <table class="table table-hover table-striped table-bordered example">
                        <thead>
                            <tr>
                                <th><?php echo $this->lang->line('name'); ?></th>
                                <th><?php echo $this->lang->line('floor'); ?></th>
                                <th><?php echo $this->lang->line('department'); ?></th>
                                <th class="text-end"><?php echo $this->lang->line('action'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if (empty($ward_list)) {
                            } else {
                                foreach ($ward_list as $key => $value) {
                                    ?>
                                    <tr>
                                        <td class="mailbox-name">
                                            <a href="#" data-bs-toggle="popover" class="detail_popover"><?php echo $value['ward_name'] ?></a>
                                            <div class="fee_detail_popover d-none">
                                                <?php
                                                if ($value['description'] == "") {
                                                    ?>
                                                    <p class="text text-danger"><?php echo $this->lang->line('no_description'); ?></p>
                                                    <?php
                                                } else {
                                                    ?>
                                                    <p class="text text-info"><?php echo $value['description']; ?></p>
                                                    <?php
                                                }
                                                ?>
                                            </div>
                                        </td>
                                        <td class="mailbox-name">
                                            <?php echo $value['floor_name']; ?>
                                        </td>
                                        <td class="mailbox-name">
                                            <?php echo $value['department_name']; ?>
                                        </td>
                                        <td class="mailbox-date float-end">
                                            <?php if ($this->rbac->hasPrivilege('setup_font_office', 'can_edit')) { ?>
                                                <a data-bs-toggle="modal" data-bs-target="#myeditModal" onclick="edit(<?php echo $value['id']; ?>)" class="btn btn-secondary btn-sm" title="<?php echo $this->lang->line('edit'); ?>">
                                                    <i class="fa fa-pencil"></i>
                                                </a>
                                            <?php } ?>
                                            <?php if ($this->rbac->hasPrivilege('setup_font_office', 'can_delete')) { ?>
                                                <a href="<?php echo base_url(); ?>admin/setup/ward/delete/<?php echo $value['id']; ?>" class="btn btn-secondary btn-sm" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('delete'); ?>" onclick="return confirm('<?php echo $this->lang->line('delete_confirm') ?>');">
                                                    <i class="fa fa-trash"></i>
                                                </a>
                                            <?php } ?>
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
                <h5 class="modal-title" id="myModalLabel"><?php echo $this->lang->line('add') . " " . $this->lang->line('ward'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addward" method="post" accept-charset="utf-8" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label class="form-label"><?php echo $this->lang->line('name'); ?></label>
                                <input id="invoice_no" name="name" placeholder="" type="text" class="form-control" value="<?php echo set_value('invoice_no'); ?>" />
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label class="form-label"><?php echo $this->lang->line('floor'); ?></label>
                                <select autofocus="" id="fee_groups_id" name="floor_id" class="form-control">
                                    <option value=""><?php echo $this->lang->line('select'); ?></option>
                                    <?php
                                    foreach ($floor_list as $floors) {
                                        ?>
                                        <option value="<?php echo $floors['id'] ?>"<?php
                                        if (set_value('fee_groups_id') == $floors['id']) {
                                            echo "selected=selected";
                                        }
                                        ?>><?php echo $floors['name'] ?></option>
                                        <?php
                                        $count++;
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label class="form-label"><?php echo $this->lang->line('department'); ?></label>
                                <select autofocus="" name="department_id" class="form-control">
                                    <option value=""><?php echo $this->lang->line('select'); ?></option>
                                    <?php
                                    foreach ($dept_list as $value) {
                                        ?>
                                        <option value="<?php echo $value['id'] ?>"<?php
                                        if (set_value('fee_groups_id') == $value['id']) {
                                            echo "selected=selected";
                                        }
                                        ?>><?php echo $value['department_name'] ?></option>
                                        <?php
                                        $count++;
                                    }
                                    ?>
                                </select>
                                <span class="text-danger"><?php echo form_error('fee_groups_id'); ?></span>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label class="form-label"><?php echo $this->lang->line('description'); ?></label>
                                <textarea class="form-control" id="description" name="description" placeholder="" rows="2"><?php echo set_value('description'); ?><?php echo set_value('description') ?></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                    <button type="submit" class="btn btn-info"><?php echo $this->lang->line('save'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade sh-modal sh-modal-accent" id="myeditModal" tabindex="-1" aria-labelledby="myeditModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm400 modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myeditModalLabel"><?php echo $this->lang->line('edit') . " " . $this->lang->line('ward'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12" id="edit_warddata">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function (e) {
        $("#addward").on('submit', (function (e) {
            e.preventDefault();
            $.ajax({
                url: '<?php echo base_url(); ?>admin/setup/ward/add',
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
                },
                error: function () {
                    alert("Fail")
                }
            });
        }));
    });

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

    function edit(id) {
        $.ajax({
            url: '<?php echo base_url(); ?>admin/setup/ward/getdata/' + id,
            success: function (data) {
                $('#edit_warddata').html(data);
            }
        });
    }
</script>
