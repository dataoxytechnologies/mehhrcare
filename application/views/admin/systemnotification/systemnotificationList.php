<link rel="stylesheet" href="<?php echo base_url(); ?>backend/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css">
<script src="<?php echo base_url(); ?>backend/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js"></script>
<!-- Content Wrapper. Contains page content -->
<div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><?php echo $this->lang->line('notice_board'); ?></h3>
                        <?php if ($this->rbac->hasPrivilege('notice_board', 'can_add')) { ?>
                            <div class="d-flex gap-2 align-items-center flex-wrap float-end">
                                <a href="<?php echo base_url() ?>admin/notification/add" class="btn btn-primary btn-sm"><i class="fa fa-plus"></i> <?php echo $this->lang->line('post_new_message'); ?></a>
                                <?php
                            }
                            if ($this->rbac->hasPrivilege('email_sms', 'can_view')) {
                                ?>
                                <a href="<?php echo base_url(); ?>admin/mailsms/compose" class="btn btn-primary btn-sm"><i class="fa fa-reorder"></i> <?php echo $this->lang->line('send_email_/_sms'); ?></a>
                            <?php } ?>
                        </div>
                    </div>                 
                    <div class="card-body">
                        <div class="accordion" id="accordion">                          
                            <?php if (empty($notificationlist)) {
                                ?>
                                <div class="alert alert-info"><?php echo $this->lang->line('no_record_found'); ?></div>
                                <?php
                            } else {
                                foreach ($notificationlist as $key => $notification) {
                                    $role_name = $notification["role_name"];
                                    ?>
                                    <div class="card mb-2">
                                        <div class="card-header">
                                            <h4 class="card-title">
                                                <a data-bs-toggle="collapse" data-bs-parent="#accordion" href="#collapse<?php echo $notification['id']; ?>" aria-expanded="false" class="collapsed">
                                                    <?php echo $notification['title']; ?>
                                                </a>
                                            </h4>
                                            <div class="float-end">
                                                <?php if (($this->rbac->hasPrivilege('notice_board', 'can_edit')) || ($notification["created_id"] == $user_id)) { ?>
                                                    <a href="<?php echo base_url() ?>admin/notification/edit/<?php echo $notification['id'] ?>" class="btn btn-secondary btn-sm" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('edit'); ?>" title="<?php echo $this->lang->line('add'); ?>">
                                                        <i class="fa fa-pencil"></i>
                                                    </a>
                                                    &nbsp; <?php } if (($this->rbac->hasPrivilege('notice_board', 'can_delete')) || ($notification["created_id"] == $user_id)) { ?>                                         
                                                    <a onclick="delete_recordById('<?php echo base_url() ?>admin/notification/delete/<?php echo $notification['id'] ?>', '<?php echo $this->lang->line('delete_message'); ?>')"  class="btn btn-secondary btn-sm" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('delete'); ?>" title="<?php echo $this->lang->line('add'); ?>">
                                                        <i class="fa fa-trash"></i>
                                                    </a>
                                                <?php } ?>
                                            </div>
                                        </div>
                                        <div id="collapse<?php echo $notification['id']; ?>" class="collapse" aria-expanded="false">
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-md-9">
                                                        <?php echo $notification['message']; ?>
                                                    </div><!-- /.col -->
                                                    <div class="col-md-3">
                                                        <div class="">
                                                            <div class="card-body">
                                                                <ul class="nav nav-pills">
                                                                    <li><i class="fa fa-calendar-check-o"></i> <?php echo $this->lang->line('publish_date'); ?> : <?php echo date($this->customlib->getHospitalDateFormat(), $this->customlib->dateyyyymmddTodateformat($notification['publish_date'])); ?> </li>
                                                                    <li><i class="fa fa-calendar"></i> <?php echo $this->lang->line('notice_date'); ?> : <?php echo date($this->customlib->getHospitalDateFormat(), $this->customlib->dateyyyymmddTodateformat($notification['date'])); ?> </li>
                                                                </ul>
                                                                <h4 class="text text-primary ptt10"> <?php echo $this->lang->line('message_to'); ?></h4>
                                                                <ul class="nav nav-pills nav-stacked">
                                                                    <?php foreach ($role_name as $key => $role_value) {
                                                                        ?>
                                                                        <li>
                                                                            <i class="fa fa-user" aria-hidden="true"></i>
                                                                            <?php echo $role_value['name']; ?>
                                                                        </li>
                                                                    <?php } ?>
                                                                    <?php ?> 
                                                                </ul>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php
                                }
                            }
                            ?>
                        </div>
                    </div>                   
                </div>
            </div>           
        </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        var date_format = '<?php echo $result = strtr($this->customlib->getHospitalDateFormat(), ['d' => 'dd', 'm' => 'mm', 'Y' => 'yyyy',]) ?>';
        /* .date init removed - auto-init via event delegation */

        $("#btnreset").click(function () {
            $("#form1")[0].reset();
        });
    });
</script>
<script>
    $(function () {
        $("#compose-textarea").wysihtml5();
    });
</script>