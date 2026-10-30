<div class="downloadlabel hide" id="downloadlabel"><?php echo $this->lang->line('join_list'); ?></div>
<?php
if (!empty($viewerDetail)) {
    if ($type == "staff") { ?>

    <table class="table table-hover table-striped table-bordered viewer-list-datatable">
                        <thead>
                            <tr>
                             <th><?php echo $this->lang->line('staff'); ?></th>
                             <th class="text-end"><?php echo $this->lang->line('last_join'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
              <?php
                  foreach ($viewerDetail as $viewer_key => $viewer_value) { ?>
                  <tr>
                      <td> <?php
                              echo html_escape($viewer_value->create_for_name) . " " . html_escape($viewer_value->create_for_surname) . " (" . html_escape($viewer_value->role_name) . ": " . html_escape($viewer_value->employee_id) . ")";
                              ?></td>
                      <td class="float-end">
                          <?php echo date($this->customlib->getHospitalDateFormat(true, true), strtotime($viewer_value->created_at)) ?>
                      </td>
                  </tr>
                  <?php
                  }
              ?>
            </tbody>
</table>

<?php
} elseif ($type == "patient") {
        ?>
  <table class="table table-hover table-striped table-bordered viewer-list-datatable">
                        <thead>
                          <tr>
                            <th><?php echo $this->lang->line('patient_id'); ?></th>
                            <th><?php echo $this->lang->line('patient_name'); ?></th>
                            <th><?php echo $this->lang->line('mobile_number'); ?></th>
                            <th><?php echo $this->lang->line('last_join'); ?></th>
                          </tr>
                        </thead>
                        <tbody>
                            <?php
foreach ($viewerDetail as $viewer_key => $viewer_value) {
            ?>
              <tr>
                  <td><?php echo html_escape($viewer_value->patient_unique_id); ?></td>
                    <td><?php echo html_escape($viewer_value->patient_name); ?></td>
                    <td><?php echo html_escape($viewer_value->mobileno); ?></td>
                    <td><?php echo date($this->customlib->getHospitalDateFormat(true, true), strtotime($viewer_value->created_at)); ?></td>
              </tr>
<?php
}
        ?>
</tbody>
</table>
   <?php
}

} else {
    ?>
 <div class="alert alert-info"><?php echo $this->lang->line('no_record_found'); ?></div>
                                    <?php
}

?>