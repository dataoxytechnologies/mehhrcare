<?php if (!empty($result)) {
    ?>
    <ul class="timeline timeline-inverse">
        <?php 
        foreach ($result as $key => $value) {
            ?>      
            <li class="time-label">
                <span class="bg-blue">    <?php
                    echo $this->customlib->YYYYMMDDTodateFormat($value['timeline_date']);
                    ?></span>
            </li> 
            <li>
                <i class="fa fa-list-alt bg-blue"></i>
                <div class="timeline-item">
                    <?php if ($this->rbac->hasPrivilege('staff_timeline', 'can_delete')) { ?>
                        <span class="time"><a class="defaults-c text-end" data-bs-toggle="tooltip" title="" onclick="delete_timeline('<?php echo $value['id']; ?>')" title="<?= $this->lang->line('delete') ?>"><i class="fa fa-trash"></i></a></span>
                    <?php } ?>
                    <?php if (!empty($value["document"])) { ?>
                        <span class="time"><a class="defaults-c text-end" data-bs-toggle="tooltip" title="" href="<?php echo base_url() . "admin/timeline/download_patient_timeline/" . $value["id"] . "/" . $value["document"] ?>" title="<?= $this->lang->line('download') ?>"><i class="fa fa-download"></i></a></span>
                    <?php } ?>
                    <h3 class="timeline-header text-aqua"> <?php echo $value['title']; ?> </h3>
                    <div class="timeline-body">
                        <?php echo $value['description']; ?> 
                    </div>
                </div>
            </li>
        <?php } ?>      
        <li><i class="fa fa-clock-o bg-gray"></i></li> 
    </ul>
<?php } else {
    ?>
    <div align="center" class="dataTables_empty">No data available in table <br /><br /><img src="https://smart-hospital.in/shappresource/images/addnewitem.svg" width="150"><br /><br /><span class="text-success bolds"><i class="fa fa-arrow-left"></i> Add new record or search with different criteria.</span></div>
<?php }
?>