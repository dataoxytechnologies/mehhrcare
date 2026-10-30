<div class="row">
            <div class="col-md-2">
                <div class="card border0">
                    <ul class="tablists">
                        <li><a href="<?php echo site_url('admin/visitorspurpose') ?>" class="active"><?php echo $this->lang->line('purpose'); ?></a></li>
                        <li><a href="<?php echo site_url('admin/complainttype') ?>"><?php echo $this->lang->line('complain_type'); ?></a></li>
                        <li><a href="<?php echo site_url('admin/source') ?>"><?php echo $this->lang->line('source'); ?></a></li>
                        <li><a href="<?php echo site_url('admin/reference') ?>" ><?php echo $this->lang->line('reference'); ?></a></li>
                    </ul>
                </div>
            </div><!--./col-md-3-->
            <?php if ($this->rbac->hasPrivilege('setup_font_office', 'can_add') || $this->rbac->hasPrivilege('setup_font_office', 'can_edit')) {?>
                <div class="col-md-4">
                    <!-- Horizontal Form -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title"><?php echo $this->lang->line('edit'); ?> <?php echo $this->lang->line('purpose'); ?></h3>
                        </div><!-- /.card-header -->
                        <form id="form1" action="<?php echo site_url('admin/visitorspurpose/edit/' . $visitors_purpose_data['id']) ?>" method="post">
                            <div class="card-body">
                                <?php echo $this->session->flashdata('msg') ?>
                                <div class="mb-3">
                                    <label for="pwd"><?php echo $this->lang->line('purpose'); ?></label> <small class="req"> *</small>
                                    <input class="form-control" id="description" name="visitors_purpose" value="<?php echo set_value('visitors_purpose', $visitors_purpose_data['visitors_purpose']); ?>"/>
                                    <span class="text-danger"><?php echo form_error('visitors_purpose'); ?></span>
                                </div>
                                <div class="mb-3">
                                    <label for="pwd"><?php echo $this->lang->line('description'); ?></label>
                                    <textarea class="form-control" id="description" name="description"rows="3"><?php echo set_value('description', $visitors_purpose_data['description']); ?></textarea>
                                </div>
                            </div><!-- /.card-body -->
                            <div class="card-footer">
                                <button type="submit" class="btn btn-info float-end"><?php echo $this->lang->line('save'); ?></button>
                            </div>
                        </form>
                    </div>
                </div><!--/.col (right) -->
                <!-- left column -->
            <?php }?>
            <div class="col-md-<?php
if ($this->rbac->hasPrivilege('setup_font_office', 'can_add') || $this->rbac->hasPrivilege('setup_font_office', 'can_edit')) {
    echo "6";
} else {
    echo "10";
}
?>">
                <!-- general form elements -->
                <div class="card">
                    <div class="card-header ptbnull">
                        <h3 class="card-title titlefix"><?php echo $this->lang->line('purpose'); ?> <?php echo $this->lang->line('list'); ?></h3>
                        <div class="d-flex gap-2 align-items-center flex-wrap float-end">
                        </div><!-- /.ms-auto d-flex gap-1 -->
                    </div><!-- /.card-header -->
                    <div class="card-body">
                        <div class="download_label"></div>
                        <div class="table-responsive mailbox-messages overflow-visible">
                            <table class="table table-hover table-striped table-bordered example">
                                <thead>
                                    <tr>
                                        <th><?php echo $this->lang->line('purpose'); ?></th>
                                        <th class="text-end"><?php echo $this->lang->line('action'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php
                                if (empty($visitors_purpose_list)) { ?>
                                <?php
                                } else {
                                    foreach ($visitors_purpose_list as $key => $value) { ?>
                                            <tr>
                                                <td class="mailbox-name">
                                                    <a href="#" data-bs-toggle="popover" class="detail_popover"><?php echo $value['visitors_purpose'] ?></a>

                                                    <div class="fee_detail_popover displaynone">
                                                    <?php
                                                        if ($value['description'] == "") {  ?>
                                                            <p class="text text-danger"><?php echo $this->lang->line('no_description'); ?></p>
                                                            <?php
                                                        } else { ?>
                                                            <p class="text text-info"><?php echo $value['description']; ?></p>
                                                        <?php
                                                        }
                                                        ?>
                                                    </div>
                                                </td>
                                                
                                                <td class="mailbox-date float-end">
                                                     <?php if ($this->rbac->hasPrivilege('setup_font_office', 'can_edit')) {?>
                                                    <a href="<?php echo base_url(); ?>admin/visitorspurpose/edit/<?php echo $value['id']; ?>"  class="btn btn-secondary btn-sm" data-bs-toggle="tooltip" title="" title="<?= $this->lang->line('edit') ?>">
                                                        <i class="fa fa-pencil"></i>
                                                    </a>
                                                    <?php }if ($this->rbac->hasPrivilege('setup_font_office', 'can_delete')) {?>
                                                    <a href="<?php echo base_url(); ?>admin/visitorspurpose/delete/<?php echo $value['id']; ?>" class="btn btn-secondary btn-sm" data-bs-toggle="tooltip" title="" onclick="return confirm('<?php echo $this->lang->line('delete_confirm') ?>');" title="<?= $this->lang->line('delete') ?>">
                                                        <i class="fa fa-remove"></i>
                                                    </a>
                                                    <?php }?>


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
    


<script>
    $(document).ready(function () {
        $('.detail_popover').popover({
            placement: 'right',
            trigger: 'hover',
            container: 'body',
            html: true,
            content: function () {
                return $(this).closest('td').find('.fee_detail_popover').html();
            }
        });
    });
</script>