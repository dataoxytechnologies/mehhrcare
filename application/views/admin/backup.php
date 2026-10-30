<div class="row">
    <?php $this->load->view('setting/sidebar'); ?>
    <?php $hasRestore = $this->rbac->hasPrivilege('restore', 'can_view'); ?>
    <div class="<?php echo $hasRestore ? 'col-md-6' : 'col-md-10'; ?>">
        <div class="card">
            <div class="card-header ptbnull d-flex align-items-center justify-content-between flex-wrap gap-2">
                <h5 class="card-title mb-0"><?php echo $this->lang->line('backup_history'); ?></h5>
                <?php if ($this->rbac->hasPrivilege('backup', 'can_add')) { ?>
                    <form id="form1" action="<?php echo site_url('admin/admin/backup') ?>" method="post" accept-charset="utf-8" role="form" class="mb-0">
                        <?php echo $this->customlib->getCSRF(); ?>
                        <button class="btn btn-primary btn-sm" type="submit" name="backup" value="backup"><i class="fa fa-plus-square-o"></i> <?php echo $this->lang->line('create_backup'); ?></button>
                    </form>
                <?php } ?>
            </div>
            <div class="card-body">
                <?php if ($this->session->flashdata('msg')) {
                    echo $this->session->flashdata('msg');
                    $this->session->unset_userdata('msg');
                } ?>
                <div class="table-responsive">
                    <table class="table table-hover table-striped align-middle mb-0">
                        <thead>
                            <tr>
                                <th><?php echo $this->lang->line('backup_files'); ?></th>
                                <th class="text-end sh-th-260 text-nowrap"><?php echo $this->lang->line('action'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($dbfileList as $data) { ?>
                                <tr>
                                    <td><a href="#"><?php echo $data; ?></a></td>
                                    <td class="text-end">
                                        <div class="d-flex flex-nowrap justify-content-end gap-1">
                                            <a href="<?php echo site_url('admin/admin/downloadbackup/' . $data) ?>" class="btn btn-success btn-sm d-inline-flex align-items-center justify-content-center sh-btn-download-icon" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('download'); ?>"><i class="fa fa-download"></i></a>
                                            <?php if ($hasRestore) { ?>
                                                <form class="formrestore m-0 p-0" action="<?php echo site_url('admin/admin/backup') ?>" method="post" accept-charset="utf-8" role="form">
                                                    <?php echo $this->customlib->getCSRF(); ?>
                                                    <input type="hidden" name="filename" value="<?php echo $data; ?>">
                                                    <button class="btn btn-warning btn-sm" type="submit" name="backup" value="restore" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('restore'); ?>"><i class="fa fa-undo"></i></button>
                                                </form>
                                            <?php } ?>
                                            <?php if ($this->rbac->hasPrivilege('backup', 'can_delete')) { ?>
                                                <form class="formdelete m-0 p-0" method="post" role="form" accept-charset="utf-8" action="<?php echo site_url('admin/admin/dropbackup/' . $data); ?>">
                                                    <?php echo $this->customlib->getCSRF(); ?>
                                                    <button class="btn btn-danger btn-sm" type="submit" name="backup" value="delete" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('delete'); ?>"><i class="fa fa-trash"></i></button>
                                                </form>
                                            <?php } ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <?php if ($hasRestore) { ?>
        <div class="col-md-4 col-sm-4">
            <div class="card">
                <div class="card-header ptbnull">
                    <h5 class="card-title mb-0"><?php echo $this->lang->line('upload_from_local_directory'); ?></h5>
                </div>
                <form role="form" action="<?php echo site_url('admin/admin/backup') ?>" method="post" enctype="multipart/form-data" class="mb-0">
                    <?php echo $this->customlib->getCSRF(); ?>
                    <div class="card-body">
                        <input class="filestyle form-control" data-height="30" type="file" name="file" id="exampleInputFile">
                        <span class="text-danger"><?php echo form_error('file'); ?></span>
                    </div>
                    <div class="card-footer text-end">
                        <button class="btn btn-primary btn-sm" type="submit" name="backup" value="upload"><i class="fa fa-upload"></i> <?php echo $this->lang->line('upload'); ?></button>
                    </div>
                </form>
            </div>
            <div class="card">
                <div class="card-header ptbnull d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <h5 class="card-title mb-0"><?php echo $this->lang->line('cron_secret_key') ?></h5>
                    <a class="btn btn-primary btn-sm" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('cron_secret_key') ?>" href="<?php echo base_url() . "admin/admin/addCronsecretkey/" . $settinglist[0]['id'] ?>">
                        <?php echo !empty($settinglist[0]['cron_secret_key']) ? $this->lang->line('regenerate') : $this->lang->line('generate'); ?>
                    </a>
                </div>
                <div class="card-body cronkeyheight">
                    <div class="d-none" id="cronkey">
                        <p class="hideeyep"><?php print_r($settinglist[0]['cron_secret_key']); ?></p>
                    </div>
                    <a class="hideeye" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('cron_secret_key') ?>" id="showbtn" onclick="showkey()" href="#"><i class="fa fa-eye"></i></a>
                </div>
            </div>
        </div>
    <?php } ?>
</div>
<script type="text/javascript">
    $('#form1').submit(function () {
        var c = confirm('<?php echo $this->lang->line('are_you_sure_want_to_make_current_backup'); ?>');
        return c;
    });
    $('.formdelete').submit(function () {
        var c = confirm('<?php echo $this->lang->line('are_you_sure_want_to_delete_backup'); ?>');
        return c;
    });
    $('.formrestore').submit(function () {
        var c = confirm('<?php echo $this->lang->line('are_you_sure_want_to_restore_backup'); ?>');
        return c;
    });

    function showkey() {
        $("#cronkey").removeClass('d-none');
        $("#showbtn").html("<i class='fa fa-eye-slash'></i>");
        $("#showbtn").attr("onclick", "hidekey()");
    }

    function hidekey() {
        $("#cronkey").addClass('d-none');
        $("#showbtn").html("<i class='fa fa-eye'></i>");
        $("#showbtn").attr("onclick", "showkey()");
    }
</script>