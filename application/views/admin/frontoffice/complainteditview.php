<div class="container-fluid py-3">
<h4 class="mb-3">
            <i class="fa fa-ioxhost"></i> <?php echo $this->lang->line('front_office'); ?></h4>
    <div class="row">
            <?php if ($this->rbac->hasPrivilege('complaint', 'can_add') || $this->rbac->hasPrivilege('complaint', 'can_edit')) {
    ?>
                <div class="col-md-4">
                    <!-- Horizontal Form -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title"><?php echo $this->lang->line('edit'); ?> <?php echo $this->lang->line('complain'); ?></h3>
                        </div><!-- /.card-header -->
                        <form id="form1" action="<?php echo site_url('admin/complaint/edit/' . $complaint_data['id']) ?>"   method="post" accept-charset="utf-8" enctype="multipart/form-data" >
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="exampleInputEmail1"><?php echo $this->lang->line('complain_type'); ?></label>
                                    <select name="complaint" class="form-control">
                                        <option value=""><?php echo $this->lang->line('select'); ?></option>
                                        <?php
foreach ($complaint_type as $key => $value) {
        ?>
                                            <option value="<?php echo $value['complaint_type']; ?>" <?php if (set_value('complaint', $complaint_data['complaint_type']) == $value['complaint_type']) {
            echo "selected";
        }
        ?>><?php print_r($value['complaint_type']);?></option>
                                        <?php }?>
                                    </select>
                                    <span class="text-danger"><?php echo form_error('complaint'); ?></span>
                                </div>
                                <div class="mb-3">
                                    <label for="pwd"><?php echo $this->lang->line('source'); ?></label>
                                    <select name="source" class="form-control">
                                        <option value=""><?php echo $this->lang->line('select'); ?></option>
                                        <?php foreach ($complaintsource as $key => $value) {
        ?>
                                            <option value="<?php echo $value['source']; ?>"<?php if (set_value('source', $complaint_data['source']) == $value['source']) {
            echo "selected";
        }
        ?>><?php echo $value['source']; ?></option>
                                        <?php }
    ?>
                                    </select>
                                    <span class="text-danger"><?php echo form_error('source'); ?></span>
                                </div>
                                <div class="mb-3">
                                    <label for="pwd"><?php echo $this->lang->line('complain_by'); ?></label> <small class="req"> *</small>
                                    <input type="text" class="form-control" value="<?php echo set_value('name', $complaint_data['name']); ?>"  name="name">
                                    <span class="text-danger"><?php echo form_error('name'); ?></span>
                                </div>
                                <div class="mb-3">
                                    <label for="email"><?php echo $this->lang->line('phone'); ?></label>
                                    <input type="text" class="form-control" value="<?php echo set_value('contact', $complaint_data['contact']); ?>"  name="contact">
                                </div>
                                <div class="mb-3">
                                    <div class="mb-3">
                                        <label for="pwd"><?php echo $this->lang->line('date'); ?></label>
                                        <input type="text" class="form-control" value="<?php echo set_value('date', date($this->customlib->getHospitalDateFormat(), $this->customlib->dateyyyymmddTodateformat($complaint_data['date']))); ?>"  name="date" id="date" readonly>
                                        <span class="text-danger"><?php echo form_error('date'); ?></span>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="pwd"><?php echo $this->lang->line('description'); ?></label>
                                    <textarea class="form-control" id="description" name="description"rows="3"><?php echo set_value('description', $complaint_data['description']); ?></textarea>
                                </div>
                                <div class="mb-3">
                                    <label for="pwd"><?php echo $this->lang->line('action_taken'); ?></label>
                                    <input type="text" class="form-control" value="<?php echo set_value('action_taken', $complaint_data['action_taken']); ?>"  name="action_taken">
                                    <span class="text-danger"><?php echo form_error('action_taken'); ?></span>
                                </div>
                                <div class="mb-3">
                                    <label for="pwd"><?php echo $this->lang->line('assigned'); ?></label>
                                    <input type="text" class="form-control" value="<?php echo set_value('assigned', $complaint_data['assigned']); ?>"  name="assigned">
                                    <span class="text-danger"><?php echo form_error('assigned'); ?></span>
                                </div>
                                <div class="mb-3">
                                    <label for="pwd"><?php echo $this->lang->line('note'); ?></label>
                                    <textarea class="form-control" id="description" name="note" name="note" rows="3"><?php echo set_value('note', $complaint_data['note']); ?></textarea>
                                    <span class="text-danger"><?php echo form_error('note'); ?></span>
                                </div>
                                <div class="mb-3">
                                    <label for="exampleInputFile"><?php echo $this->lang->line('attach_document'); ?></label>
                                    <div><input class="filestyle form-control" type='file' name='file'  />
                                    </div>
                                    <span class="text-danger"><?php echo form_error('file'); ?></span></div>
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
if ($this->rbac->hasPrivilege('complaint', 'can_add') || $this->rbac->hasPrivilege('complaint', 'can_edit')) {
    echo "8";
} else {
    echo "12";
}
?>">
                <!-- general form elements -->
                <div class="card">
                    <div class="card-header ptbnull">
                        <h3 class="card-title titlefix"><?php echo $this->lang->line('complain'); ?> <?php echo $this->lang->line('list'); ?></h3>
                        <div class="d-flex gap-2 align-items-center flex-wrap float-end">
                        </div><!-- /.ms-auto d-flex gap-1 -->
                    </div><!-- /.card-header -->
                    <div class="card-body">
                        <div class="download_label"></div>
                        <div class="table-responsive mailbox-messages">
                            <table class="table table-hover table-striped table-bordered example">
                                <thead>
                                    <tr>
                                        <th><?php echo $this->lang->line('complain'); ?> #  </th>
                                        <th><?php echo $this->lang->line('complain_type'); ?></th>
                                        <th><?php echo $this->lang->line('name'); ?></th>
                                        <th><?php echo $this->lang->line('phone'); ?></th>
                                        <th><?php echo $this->lang->line('date'); ?></th>
                                        <th class="text-end"><?php echo $this->lang->line('action'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php
                                if (empty($complaint_list)) { ?>
                                <?php   } else {
                                    foreach ($complaint_list as $key => $value) { ?>
                                            <tr>
                                                <td class="mailbox-name"><?php echo $value['id']; ?></td>
                                                <td class="mailbox-name"><?php echo $value['complaint_type']; ?></td>
                                                <td class="mailbox-name"><?php echo $value['name']; ?> </td>
                                                <td class="mailbox-name"> <?php echo $value['contact']; ?></td>
                                                <td class="mailbox-name"> <?php echo date($this->customlib->getHospitalDateFormat(), $this->customlib->dateyyyymmddTodateformat($value['date'])); ?></td>
                                                <td class="mailbox-date float-end" "="">
                                                    <a onclick="getRecord(<?php echo $value['id']; ?>)" class="btn btn-secondary btn-sm" data-bs-target="#complaintdetails" data-bs-toggle="modal" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i> Processing" title="<?= $this->lang->line('view') ?>"><i class="fa fa-reorder"></i></a>
                                                    <?php if ($value['image'] !== "") {?><a href="<?php echo base_url(); ?>admin/complaint/download/<?php echo $value['image']; ?>" class="btn btn-secondary btn-sm" data-bs-toggle="tooltip" title="" title="<?php echo $this->lang->line('download'); ?>">
                                                            <i class="fa fa-download"></i>
                                                        </a>  <?php }?>
                                                    <?php if ($this->rbac->hasPrivilege('complaint', 'can_edit')) {?>
                                                        <a href="<?php echo base_url(); ?>admin/complaint/edit/<?php echo $value['id']; ?>" class="btn btn-secondary btn-sm" data-bs-toggle="tooltip" title="" title="<?php echo $this->lang->line('edit'); ?>">
                                                            <i class="fa fa-pencil"></i>
                                                        </a>
                                                    <?php }?>
                                                    <?php if ($this->rbac->hasPrivilege('complaint', 'can_delete')) {
            ?>
                                                        <?php if ($value['image'] !== "") {?><a href="<?php echo base_url(); ?>admin/complaint/imagedelete/<?php echo $value['id']; ?>/<?php echo $value['image']; ?>" class="btn btn-secondary btn-sm" data-bs-toggle="tooltip" title="" onclick="return confirm('<?php echo $this->lang->line('delete_confirm') ?>');" title="<?php echo $this->lang->line('delete'); ?>">
                                                                <i class="fa fa-remove"></i>
                                                            </a>
                                                        <?php } else {?>
                                                            <a href="<?php echo base_url(); ?>admin/complaint/delete/<?php echo $value['id']; ?>" class="btn btn-secondary btn-sm" data-bs-toggle="tooltip" title="" onclick="return confirm('<?php echo $this->lang->line('delete_confirm') ?>');" title="<?php echo $this->lang->line('delete'); ?>">
                                                                <i class="fa fa-remove"></i>
                                                            </a>
                                                            <?php
}
        }
        ?>
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
    


<!-- new END -->
<div id="complaintdetails" class="modal fade" role="dialog">
    <div class="modal-dialog modal-dialog2 modal-lg">
        <div class="modal-content mx-2">
            <div class="modal-header">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                <h5 class="modal-title"><?php echo $this->lang->line('details'); ?></h5>
            </div>
            <div class="modal-body" id="getdetails">

            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        var date_format = '<?php echo $result = strtr($this->customlib->getHospitalDateFormat(), ['d' => 'dd', 'm' => 'mm', 'Y' => 'yyyy']) ?>';
        /* date init removed - .date class triggers TD 6 via event delegation */
    });

    function getRecord(id) {
        $.ajax({
            url: '<?php echo base_url(); ?>admin/complaint/details/' + id,
            success: function (result) {
                $('#getdetails').html(result);
            }
        });
    }
</script>
