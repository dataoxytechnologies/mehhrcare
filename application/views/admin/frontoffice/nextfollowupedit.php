<div class="container-fluid py-3">
<h4 class="mb-3">
            <i class="fa fa-phone"></i> <?php echo $this->lang->line('enquiry'); ?>
    </section>
    <!-- Main content -->

<?php
$response     = $this->customlib->getResponse();
$enquiry_type = $this->customlib->getenquiryType();
$Source       = $this->customlib->getComplaintSource();
$Reference    = $this->customlib->getReference();
$admin        = $this->customlib->getLoggedInUserData();
?>
    <!-- New Desgine start -->
    <!-- Main content -->
    <div class="row">
            <div class="col-md-4">
                <!-- Horizontal Form -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><?php echo $this->lang->line('follow_ups_of'); ?>(<?php echo $enquiry_data['name']; ?>)</h3>
                    </div><!-- /.card-header -->
                    <form id="form1" action="<?php echo site_url('admin/enquiry/follow_up_edit/' . $id . "/" . $follow_up_data['id']) ?>"   method="post" >
                        <div class="card-body">
                            <div class="mb-3">
                                <label><?php echo $this->lang->line('follow_up_date'); ?></label>
                                <input type="text" id="date" name="date" class="form-control date" value="<?php echo set_value('date', $follow_up_data['date']); ?>" readonly="">
                                <span class="text-danger"><?php echo form_error('date'); ?></span>
                            </div>
                            <div class="mb-3">
                                <label for="pwd"><?php echo $this->lang->line('next_follow_up_date'); ?></label>
                                <input type="text" id="date_of_call" name="follow_up_date"class="form-control date" value="<?php echo set_value('follow_up_date', $follow_up_data['next_date']); ?>" readonly="">
                            </div>
                            <div class="mb-3">
                                <label for="pwd"><?php echo $this->lang->line('response'); ?></label>
                                <textarea name="response" class="form-control" ><?php echo set_value('response', $follow_up_data['response']); ?></textarea>
                                <span class="text-danger"><?php echo form_error('response'); ?></span>
                            </div>
                            <div class="mb-3">
                                <label for="pwd"><?php echo $this->lang->line('note'); ?></label>
                                <textarea name="note" class="form-control" ><?php echo set_value('note', $follow_up_data['note']); ?></textarea>
                            </div>
                        </div><!-- /.card-body -->
                        <div class="card-footer">
                            <button type="submit" class="btn btn-info float-end"><?php echo $this->lang->line('save'); ?></button>
                        </div>
                    </form>
                </div>
            </div><!--/.col (right) -->
            <!-- left column -->
            <div class="col-md-8">
                <!-- general form elements -->
                <div class="card">
                    <div class="card-header ptbnull">
                        <h3 class="card-title titlefix"> <?php echo $this->lang->line('follow_ups_list_of'); ?> (<?php echo $enquiry_data['name']; ?>)</h3>
                        <div class="d-flex gap-2 align-items-center flex-wrap float-end">
                        </div><!-- /.ms-auto d-flex gap-1 -->
                    </div><!-- /.card-header -->
                    <div class="card-body">
                        <div class="tab-pane active" id="timeline">
                            <!-- The timeline -->
                            <ul class="timeline timeline-inverse">
                                <!-- timeline time label -->
                                <?php
if (empty($follow_up_list)) {
    ?>

                                    <?php
} else {
    foreach ($follow_up_list as $key => $value) {
        ?>
                                        <li class="time-label">
                                            <span class="bg-blue">
                                                <?php echo $value['date']; ?>
                                            </span>
                                        </li>
                                        <!-- /.timeline-label -->
                                        <!-- timeline item -->
                                        <li>
                                            <i class="fa fa-phone bg-blue"></i>
                                            <div class="timeline-item">
                                                <span class="time"><i class="fa fa-clock-o"></i> <?php echo $value['next_date']; ?></span>
                                                <h3 class="timeline-header"><a href="#"><?php echo $value['followup_by']; ?></a> </h3>
                                                <div class="timeline-body">
                                                    <?php echo $value['response']; ?>
                                                    <hr>
                                                    <?php echo $value['note']; ?>
                                                </div>
                                                <div class="timeline-footer">
                                                    <a href="<?php echo base_url(); ?>admin/enquiry/follow_up_edit/<?php echo $id; ?>/<?php echo $value['id']; ?>" class="btn btn-primary btn-sm" data-bs-toggle="tooltip" title="" title="<?= $this->lang->line('edit') ?>">
                                                        Edit
                                                    </a>

                                                    <a href="<?php echo base_url(); ?>admin/enquiry/follow_up_delete/<?php echo $id; ?>/<?php echo $value['id']; ?>" class="btn btn-danger btn-sm" data-bs-toggle="tooltip" title="" onclick="return confirm('<?php echo $this->lang->line('delete_confirm') ?>');" title="<?= $this->lang->line('delete') ?>">

                                                        Delete</a>
                                                </div>
                                            </div>
                                        </li>
                                        <?php
}
}
?>
                            </ul>
                        </div>
                    </div><!-- /.card-body -->
                </div>
            </div><!--/.col (left) -->
            <!-- right column -->
        </div>
    

<!-- new END -->


<script type="text/javascript">
    $(document).ready(function () {
        var date_format = '<?php echo $result = strtr($this->customlib->getHospitalDateFormat(), ['d' => 'dd', 'm' => 'mm', 'Y' => 'yyyy']) ?>';

        /* date init removed - .date class triggers TD 6 via event delegation */

    });

    $(document).ready(function () {
        var date_format = '<?php echo $result = strtr($this->customlib->getHospitalDateFormat(), ['d' => 'dd', 'm' => 'mm', 'Y' => 'yyyy']) ?>';

        /* date_of_call init removed - .date class triggers TD 6 via event delegation */
    });
</script>
