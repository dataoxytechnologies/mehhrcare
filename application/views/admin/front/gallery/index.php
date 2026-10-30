<!-- Content Wrapper. Contains page content -->
<!-- Content Header (Page header) -->
    <!-- Main content -->
    <div class="row">
            <!-- left column -->
            <div class="col-md-12">
                <!-- general form elements -->
                <div class="card" id="holist">
                    <div class="card-header ptbnull d-flex align-items-center">
                        <h3 class="card-title titlefix"><?php echo $this->lang->line('gallery_list'); ?></h3>
                        <?php
if ($this->rbac->hasPrivilege('gallery', 'can_add')) {
    ?>
                            <div class="ms-auto d-flex gap-1">
                                <a href="<?php echo site_url('admin/front/gallery/create'); ?>" class="btn btn-sm btn-primary"><i class="fa fa-plus"></i> <?php echo $this->lang->line('add'); ?></a>

                            </div>
                        <?php }?>
                    </div><!-- /.card-header -->
                    <div class="card-body">
                        <?php if ($this->session->flashdata('msg')) {?>
                            <?php echo $this->session->flashdata('msg');
                                $this->session->unset_userdata('msg');
                            ?>
                        <?php }?>
                        <div class="mailbox-controls">
                                <div class="float-end">
                                </div><!-- /.float-end -->
                            </div>
                              <div class="download_label"><?php echo $this->lang->line('gallery_list'); ?></div>
                            <div class="table-responsive mailbox-messages">
                                <table class="table table-striped table-bordered table-hover example">
                                    <thead>
                                        <tr>
                                            <th><?php echo $this->lang->line('title'); ?></th>
                                            <th><?php echo $this->lang->line('url'); ?></th>
                                            <th class="text-end no-print noExport"><?php echo $this->lang->line('action'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($listResult)) {
    ?>

                                            <?php
} else {
    $count = 1;
    foreach ($listResult as $page) {
        ?>
                                                <tr id="<?php echo (int)$page["id"]; ?>">
                                                    <td class="mailbox-name">
                                                        <a href="#" ><?php echo html_escape($page['title']) ?></a>
                                                    </td>
                                                    <td class="mailbox-name"> <a href="<?php echo html_escape(base_url() . $page['url']) ?>" target="_blank"><?php echo html_escape(base_url() . $page['url']) ?></a></td>
                                                    <td class="mailbox-date text-end no-print">
                                                        <?php
if ($this->rbac->hasPrivilege('gallery', 'can_edit')) {
            ?>
                                                            <a href="<?php echo site_url('admin/front/gallery/edit/' . $page['slug']); ?>" class="btn btn-secondary btn-sm"  data-bs-toggle="tooltip" title="<?php echo $this->lang->line('edit'); ?>">
                                                                <i class="fa fa-pencil"></i>
                                                            </a>
                                                            <?php
}
        if ($this->rbac->hasPrivilege('gallery', 'can_delete')) {
            ?>
                                                            <a  class="btn btn-secondary btn-sm"  data-bs-toggle="tooltip" title="<?php echo $this->lang->line('delete'); ?>" onclick="delete_recordByIdReload('admin/front/gallery/delete/<?php echo (int)$page['id']; ?>', '<?php echo $this->lang->line('delete_message'); ?>')">
                                                                <i class="fa fa-remove"></i>
                                                            </a>
                                                        <?php }?>
                                                    </td>
                                                </tr>
                                                <?php
}
    $count++;
}
?>
                                    </tbody>
                                </table><!-- /.table -->
                            </div><!-- /.mail-box-messages -->
                    </div><!-- /.card-body -->
                </div>
            </div><!--/.col (left) -->
        </div>

    
