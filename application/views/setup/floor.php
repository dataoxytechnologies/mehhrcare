<div class="row">
    <div class="col-md-2">
        <?php $this->load->view('setup/bedsidebar'); ?>
    </div>
    <div class="col-md-10">
        <!-- general form elements -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title titlefix"><?php echo $this->lang->line('floor_list'); ?></h3>
                <div class="d-flex gap-2 align-items-center flex-wrap float-end">
                    <?php if ($this->rbac->hasPrivilege('floor', 'can_add')) { ?>
                        <a data-bs-toggle="modal" data-bs-target="#myModal" class="btn btn-primary btn-sm floor"><i class="fa fa-plus"></i> <?php echo $this->lang->line('add_floor'); ?></a>
                    <?php } ?>
                </div>
            </div>
            <div class="card-body">
                <div class="download_label"><?php echo $this->lang->line('floor_list'); ?></div>
                <div class="table-responsive mailbox-messages overflow-visible">
                    <table class="table table-hover table-striped table-bordered example setup-floor-fixed">
                        <thead>
                            <tr>
                                <th><?php echo $this->lang->line('name'); ?></th>
                                <th><?php echo $this->lang->line('description'); ?></th>
                                <th class="text-end noExport"><?php echo $this->lang->line('action'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if (empty($floor)) {
                            } else {
                                foreach ($floor as $key => $value) {
                                    ?>
                                    <tr>
                                        <td class="mailbox-name"><a href="#"><?php echo $value['name'] ?></a></td>
                                        <td class="mailbox-name"><?php echo $value['description'] ?></td>
                                        <td class="text-end">
                                            <div class="rowoptionview mt-mius0">
                                                <?php if ($this->rbac->hasPrivilege('floor', 'can_edit')) { ?>
                                                    <a data-bs-target="#myModaledit" onclick="edit('<?php echo $value['id']; ?>')" class="btn btn-default btn-sm" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('edit'); ?>">
                                                        <i class="fa fa-pencil"></i>
                                                    </a>
                                                <?php } ?>
                                                <?php if ($this->rbac->hasPrivilege('floor', 'can_delete')) { ?>
                                                    <a class="btn btn-default btn-sm" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('delete') ?>" onclick="delete_recordByIdReload('admin/setup/floor/delete/<?php echo $value['id']; ?>', '<?php echo $this->lang->line('delete_confirm') ?>')">
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
                <h5 class="modal-title" id="myModalLabel"><?php echo $this->lang->line('add_floor'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addfloor" method="post" accept-charset="utf-8" enctype="multipart/form-data">
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
                                        <input id="invoice_no" name="name" placeholder="" type="text" class="form-control" value="<?php echo set_value('invoice_no'); ?>" />
                                    </div>
                                    <div class="mb-3 col-12">
                                        <label class="form-label"><?php echo $this->lang->line('description'); ?></label>
                                        <textarea class="form-control sh-no-resize" id="description" name="description" placeholder="" rows="2"><?php echo set_value('description'); ?></textarea>
                                        <span class="text-danger description"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                    <button type="submit" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i>" id="addfloorbtn" class="btn btn-info"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade sh-modal sh-modal-accent" id="myModaledit" tabindex="-1" aria-labelledby="myModaleditLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm400 modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myModaleditLabel"><?php echo $this->lang->line('edit_floor'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="pup-scroll-area">
                <div class="modal-body">
                    <div id="edit_floor"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                <button type="submit" form="editfloor_data" id="editfloor_databtn" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i>" class="btn btn-info"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
            </div>
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

    $(document).ready(function (e) {
        $("#addfloor").on('submit', (function (e) {
            $("#addfloorbtn").button('loading');
            e.preventDefault();
            $.ajax({
                url: '<?php echo base_url(); ?>admin/setup/floor/add',
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
                            $("." + index).html(value);
                            message += value;
                        });
                        errorMsg(message);
                    } else {
                        successMsg(data.message);
                        window.location.reload(true);
                    }
                    $("#addfloorbtn").button('reset');
                },
                error: function () {
                    alert("Fail")
                }
            });
        }));
    });

    function edit(id) {
        var myModaledit = new bootstrap.Modal(document.getElementById('myModaledit'));
        myModaledit.show();
        $.ajax({
            url: '<?php echo base_url(); ?>admin/setup/floor/getDataByid/' + id,
            success: function (data) {
                $('#edit_floor').html(data);
            }
        });
    }

    $(".floor").click(function () {
        $('#addfloor').trigger("reset");
    });
</script>
