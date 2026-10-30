<?php $call_type = $this->customlib->getCalltype();?>
    <div class="row">
            <?php if ($this->rbac->hasPrivilege('phone_call_log', 'can_add') || $this->rbac->hasPrivilege('phone_call_log', 'can_edit')) {?>
                <div class="col-md-4">
                    <!-- Horizontal Form -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title"><?php echo $this->lang->line('edit'); ?> <?php echo $this->lang->line('phone_call_log'); ?></h3>
                        </div><!-- /.card-header -->

                        <form id="form1" action="<?php echo site_url('admin/generalcall/edit/' . $Call_data['id']) ?>"   method="post" accept-charset="utf-8" enctype="multipart/form-data" >
                            <div class="card-body">

                                <?php echo $this->session->flashdata('msg') ?>
                                <div class="mb-3">
                                    <label><?php echo $this->lang->line('name'); ?></label>
                                    <input type="text" class="form-control" value="<?php echo set_value('name', $Call_data['name']); ?>" name="name">
                                    <span class="text-danger"><?php echo form_error('name'); ?></span>
                                </div>
                                <div class="mb-3">
                                    <label for="pwd"><?php echo $this->lang->line('phone'); ?></label> <small class="req"> *</small>
                                    <input type="text" class="form-control" value="<?php echo set_value('contact', $Call_data['contact']); ?>" name="contact">
                                    <span class="text-danger"><?php echo form_error('contact'); ?></span>
                                </div>
                                <div class="mb-3">
                                    <label for="pwd"><?php echo $this->lang->line('date'); ?></label>
                                    <input id="date" name="date" placeholder="" type="text" class="form-control date" value="<?php echo set_value('date', date($this->customlib->getHospitalDateFormat(), $this->customlib->dateyyyymmddTodateformat($Call_data['date']))); ?>" readonly="readonly" />
                                </div>
                                <div class="mb-3">
                                    <label for="email"><?php echo $this->lang->line('description'); ?></label>
                                    <input type="text" class="form-control" value="<?php echo set_value('description', $Call_data['description']); ?>" name="description">
                                </div>
                                <div class="mb-3">
                                    <div class="mb-3">
                                        <label for="pwd"><?php echo $this->lang->line('next_follow_up_date'); ?></label>
                                        <input id="follow_up_date" name="follow_up_date" placeholder="" type="text" class="form-control date"  value="<?php echo set_value('follow_up_date', date($this->customlib->getHospitalDateFormat(), $this->customlib->dateyyyymmddTodateformat($Call_data['follow_up_date']))); ?>" readonly="readonly" />
                                        <span class="text-danger"><?php echo form_error('follow_up_date'); ?></span>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="pwd"><?php echo $this->lang->line('call_duration'); ?></label>
                                    <input type="text" class="form-control" value="<?php echo set_value('call_dureation', $Call_data['call_dureation']); ?>" name="call_dureation">
                                    <span class="text-danger"><?php echo form_error('call_dureation'); ?></span>
                                </div>
                                <div class="mb-3">
                                    <label for="pwd"><?php echo $this->lang->line('note'); ?></label>
                                    <textarea class="form-control" id="description" name="note"  rows="3"><?php echo set_value('note', $Call_data['note']); ?></textarea>
                                    <span class="text-danger"><?php echo form_error('note'); ?></span>
                                </div>

                                <div class="mb-3">
                                    <label for="pwd"><?php echo $this->lang->line('call_type'); ?></label><small class="req"> *</small>
                                    <?php foreach ($call_type as $key => $value) {?>
                                        <label class="form-check form-check-inline"><input type="radio" name="call_type" value="<?php echo $key; ?>" <?php if (set_value('call_type', $Call_data['call_type']) == $key) {?> checked=""<?php }?>> <?php echo $value; ?></label>

                                    <?php }?>
                                    <span class="text-danger"><?php echo form_error('call_type'); ?></span>
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
if ($this->rbac->hasPrivilege('phone_call_log', 'can_add') || $this->rbac->hasPrivilege('phone_call_log', 'can_edit')) {
    echo "8";
} else {
    echo "12";
}
?>">
                <!-- general form elements -->
                <div class="card">
                    <div class="card-header ptbnull">
                        <h3 class="card-title titlefix"><?php echo $this->lang->line('phone_call_log'); ?> <?php echo $this->lang->line('list'); ?></h3>
                        <div class="d-flex gap-2 align-items-center flex-wrap float-end">
                        </div><!-- /.ms-auto d-flex gap-1 -->
                    </div><!-- /.card-header -->
                    <div class="card-body">
                        <div class="download_label"></div>
                        <div class="table-responsive mailbox-messages">
                            <table class="table table-hover table-striped table-bordered example">
                                <thead>
                                    <tr>
                                        <th><?php echo $this->lang->line('name'); ?>
                                        </th>
                                        <th><?php echo $this->lang->line('phone'); ?>
                                        </th>
                                        <th><?php echo $this->lang->line('date'); ?>
                                        </th>
                                        <th><?php echo $this->lang->line('next_follow_up_date'); ?></th>
                                        <th><?php echo $this->lang->line('call_type'); ?>
                                        </th>
                                        <th class="text-end"><?php echo $this->lang->line('action'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
if (empty($CallList)) {
    ?>

                                        <?php
} else {
    foreach ($CallList as $key => $value) {
        ?>
                                            <tr>
                                                <td class="mailbox-name"><?php echo $value['name']; ?></td>
                                                <td class="mailbox-name"><?php echo $value['contact']; ?></td>
                                                <td class="mailbox-name"><?php echo date($this->customlib->getHospitalDateFormat(), $this->customlib->dateyyyymmddTodateformat($value['date'])); ?> </td>
                                                <td class="mailbox-name"> <?php echo date($this->customlib->getHospitalDateFormat(), $this->customlib->dateyyyymmddTodateformat($value['follow_up_date'])); ?></td>
                                                <td class="mailbox-name"> <?php echo $value['call_type']; ?></td>
                                                <td class="mailbox-date float-end">
                                                    <a  onclick="getRecord(<?php echo $value['id']; ?>)" class="btn btn-secondary btn-sm" data-bs-target="#calldetails" data-bs-toggle="modal" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i> Processing" title="<?= $this->lang->line('view') ?>"><i class="fa fa-reorder"></i></a>
                                                    <?php if ($this->rbac->hasPrivilege('phone_call_log', 'can_edit')) {?>
                                                        <a href="<?php echo base_url('admin/generalcall/edit/' . $value['id']) ?>" class="btn btn-secondary btn-sm" data-bs-toggle="tooltip" title="" title="<?php echo $this->lang->line('edit'); ?>">
                                                            <i class="fa fa-pencil"></i>
                                                        </a>
                                                    <?php }?>
                                                    <?php if ($this->rbac->hasPrivilege('phone_call_log', 'can_delete')) {?>
                                                        <a href="<?php echo base_url('admin/generalcall/delete/' . $value['id']) ?>" class="btn btn-secondary btn-sm" data-bs-toggle="tooltip" title="" onclick="return confirm('<?php echo $this->lang->line('delete_confirm') ?>');" title="<?php echo $this->lang->line('delete'); ?>">
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
        </div>
    

<!-- new END -->
<div id="calldetails" class="modal fade" role="dialog">
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

    $(document).ready(function () {
        var date_format = '<?php echo $result = strtr($this->customlib->getHospitalDateFormat(), ['d' => 'dd', 'm' => 'mm', 'Y' => 'yyyy']) ?>';

        /* follow_up_date init removed - .date class triggers TD 6 via event delegation */
    });

    function getRecord(id) {

        $.ajax({
            url: '<?php echo base_url(); ?>admin/generalcall/details/' + id,
            success: function (result) {
                //alert(result);
                $('#getdetails').html(result);
            }

        });
    }
</script>
