<div class="row">
    <?php $this->load->view('setting/sidebar'); ?>
    <div class="col-md-10">
        <div class="card">
            <div class="card-header ptbnull d-flex align-items-center justify-content-between flex-wrap gap-2">
                <h5 class="card-title mb-0"><?php echo $this->lang->line('users'); ?></h5>
                <ul class="nav nav-pills sh-segmented-tabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="tab_patients_btn" data-bs-toggle="tab" data-bs-target="#tab_patients" type="button" role="tab"><i class="fa fa-user"></i> <?php echo $this->lang->line('patient') ?></button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="tab_staff_btn" data-bs-toggle="tab" data-bs-target="#tab_staff" type="button" role="tab"><i class="fa fa-users"></i> <?php echo $this->lang->line('staff') ?></button>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content">
                    <div class="tab-pane fade show active" id="tab_patients" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover align-middle ajaxlist mb-0" data-export-title="<?php echo $this->lang->line('patient'); ?>">
                                <thead>
                                    <tr>
                                        <th class="sh-th-120"><?php echo $this->lang->line('patient_id'); ?></th>
                                        <th class="sh-th-220"><?php echo $this->lang->line('name'); ?></th>
                                        <th class="sh-th-180"><?php echo $this->lang->line('username'); ?></th>
                                        <th class="sh-th-160"><?php echo $this->lang->line('mobile_number'); ?></th>
                                        <th class="text-end noExport sh-th-140"><?php echo $this->lang->line('action'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if (!empty($patientList)) {
                                        foreach ($patientList as $patient) {
                                            ?>
                                            <tr>
                                                <td><?php echo $patient['id']; ?></td>
                                                <td><a href="#" target="_blank"><?php echo $patient['patient_name']; ?></a></td>
                                                <td><?php echo $patient['username']; ?></td>
                                                <td><?php echo $patient['mobileno']; ?></td>
                                                <td class="text-end noExport">
                                                    <div class="form-check form-switch d-inline-flex m-0">
                                                        <input class="form-check-input chk" type="checkbox" role="switch"
                                                               id="patient<?php echo $patient['user_tbl_id'] ?>"
                                                               data-role="patient"
                                                               data-rowid="<?php echo $patient['user_tbl_id'] ?>"
                                                               <?php if ($patient['user_tbl_active'] == "yes") echo 'checked'; ?> />
                                                    </div>
                                                </td>
                                            </tr>
                                            <?php
                                        }
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <!-- /.tab-pane -->
                    <div class="tab-pane fade" id="tab_staff" role="tabpanel">
                        <div class="download_label"><?php echo $this->lang->line('staff'); ?></div>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover align-middle example mb-0">
                                <thead>
                                    <tr>
                                        <th class="sh-th-120"><?php echo $this->lang->line('staff_id'); ?></th>
                                        <th class="sh-th-180"><?php echo $this->lang->line('name'); ?></th>
                                        <th class="sh-th-220"><?php echo $this->lang->line('email'); ?></th>
                                        <th class="sh-th-140"><?php echo $this->lang->line('role'); ?></th>
                                        <th class="sh-th-160"><?php echo $this->lang->line('designation'); ?></th>
                                        <th class="sh-th-160"><?php echo $this->lang->line('department'); ?></th>
                                        <th class="sh-th-150"><?php echo $this->lang->line('phone'); ?></th>
                                        <th class="text-end noExport sh-th-120"><?php echo $this->lang->line('action'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if (!empty($staffList)) {
                                        foreach ($staffList as $staff) {
                                            if ($staff["role_id"] != 7) {
                                                ?>
                                                <tr>
                                                    <td><?php echo $staff['employee_id'] ?></td>
                                                    <td><a href="<?php echo base_url(); ?>admin/staff/profile/<?php echo $staff['id']; ?>"><?php echo $staff['name'] ?></a></td>
                                                    <td><?php echo $staff['email'] ?></td>
                                                    <td><?php echo $staff['role'] ?></td>
                                                    <td><?php echo $staff['designation'] ?></td>
                                                    <td><?php echo $staff['department'] ?></td>
                                                    <td><?php echo $staff['contact_no'] ?></td>
                                                    <td class="text-end noExport">
                                                        <div class="form-check form-switch d-inline-flex m-0">
                                                            <input class="form-check-input chk" type="checkbox" role="switch"
                                                                   id="staff<?php echo $staff['id'] ?>"
                                                                   data-role="staff"
                                                                   data-rowid="<?php echo $staff['id'] ?>"
                                                                   <?php if ($staff['is_active'] == 1) echo 'checked'; ?> />
                                                        </div>
                                                    </td>
                                                </tr>
                                                <?php
                                            }
                                        }
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <!-- /.tab-pane -->
                </div>
                <!-- /.tab-content -->
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
( function ( $ ) {
    'use strict';
    $(document).ready(function () {
        initDatatable('ajaxlist','admin/users/getUsersDatatable');
    });
} ( jQuery ) )
</script>

<script type="text/javascript">  
    
        $(document).on('click', '.chk', function () {    
            var checked = $(this).is(':checked');
            var rowid = $(this).data('rowid');
            var role = $(this).data('role');
            if (checked) {
                if (!confirm('<?php echo $this->lang->line('are_you_sure_to_active_account') ?>')) {
                    $(this).removeAttr('checked');
                } else {
                    var status = "yes";
                    changeStatus(rowid, status, role);
                }
            } else if (!confirm('<?php echo $this->lang->line('are_you_sure_to_deactivate_account') ?>')) {
                $(this).prop("checked", true);
            } else {
                var status = "no";
                changeStatus(rowid, status, role);
            }
        });
        
    function changeStatus(rowid, status, role) {
        var base_url = '<?php echo base_url() ?>';
        $.ajax({
            type: "POST",
            url: base_url + "admin/users/changeStatus",
            data: {'id': rowid, 'status': status, 'role': role},
            dataType: "json",
            success: function (data) {
                successMsg(data.msg);
            }
        });
    }
</script>