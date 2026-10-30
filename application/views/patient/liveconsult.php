<div class="container-fluid px-1 py-1">
    <div class="card sh-patient-card">
        <div class="card-header">
            <h3 class="card-title mb-0"><?php echo $this->lang->line('live_consultation'); ?></h3>
        </div>
        <div class="card-body p-3">
            <div class="download_label"><?php echo $this->lang->line('live_consultation'); ?></div>
            <div class="table-responsive">
                <table class="table table-striped table-bordered table-hover example">
                    <thead>
                        <tr>
                            <th><?php echo $this->lang->line('consultation_title'); ?></th>
                            <th><?php echo $this->lang->line('description'); ?></th>
                            <th><?php echo $this->lang->line('date'); ?></th>
                            <th><?php echo $this->lang->line('created_by'); ?></th>
                            <th><?php echo $this->lang->line('created_for'); ?></th>
                            <th><?php echo $this->lang->line('patient'); ?></th>
                            <th><?php echo $this->lang->line('status'); ?></th>
                            <th class="text-end noExport"><?php echo $this->lang->line('action'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($conferences)) {
                            foreach ($conferences as $conference_value) {
                                $name_by  = trim($conference_value->create_by_name . ' ' . $conference_value->create_by_surname);
                                $name_for = trim($conference_value->create_for_name . ' ' . $conference_value->create_for_surname);
                                $name_pat = $conference_value->patient_name;
                        ?>
                        <tr>
                            <td><?php echo html_escape($conference_value->title); ?></td>
                            <td><?php echo html_escape($conference_value->description); ?></td>
                            <td><?php echo date($this->customlib->getHospitalDateFormat(true, true), strtotime($conference_value->date)); ?></td>
                            <td>
                                <?php if ($name_by == 'Super Admin') {
                                    echo composeStaffNameByString($name_by, '', $conference_value->create_by_employee_id);
                                } else {
                                    echo html_escape($name_by) . ' (' . html_escape($conference_value->create_by_role_name) . ': ' . html_escape($conference_value->create_by_employee_id) . ')';
                                } ?>
                            </td>
                            <td><?php echo html_escape($name_for) . ' (' . html_escape($conference_value->create_for_role_name) . ': ' . html_escape($conference_value->create_for_employee_id) . ')'; ?></td>
                            <td><?php echo html_escape($name_pat) . ' (' . html_escape($conference_value->patient_unique_id) . ')'; ?></td>
                            <td>
                                <?php if ($conference_value->status == 0) { ?>
                                    <span class="badge text-bg-warning"><?php echo $this->lang->line('awaited'); ?></span>
                                <?php } elseif ($conference_value->status == 1) { ?>
                                    <span class="badge text-bg-danger"><?php echo $this->lang->line('cancelled'); ?></span>
                                <?php } elseif ($conference_value->status == 2) { ?>
                                    <span class="badge text-bg-success"><?php echo $this->lang->line('finished'); ?></span>
                                <?php } ?>
                            </td>
                            <td class="text-end">
                                <?php if ($conference_value->status == 0) { ?>
                                <a href="#" class="btn btn-info sh-btn-start js-open-livestatus"
                                   data-id="<?php echo (int)$conference_value->id; ?>">
                                    <i class="fa fa-video-camera me-1"></i><?php echo $this->lang->line('join'); ?>
                                </a>
                                <?php } ?>
                            </td>
                        </tr>
                        <?php } } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Join Consultation Modal -->
<div id="modal-chkstatus" class="modal fade sh-modal sh-modal-branded" tabindex="-1" aria-labelledby="chkstatusLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="chkstatusLabel"><?php echo $this->lang->line('live_consultation'); ?></h5>
                <div class="d-flex align-items-center gap-2 ms-auto">
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
            </div>
            <form id="form-chkstatus" action="" method="POST">
                <div class="modal-body min-h-3" id="zoom_details"></div>
            </form>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('close'); ?></button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        modal_click_disabled('modal-chkstatus');

        $(document).on('click', 'a.join-btn', function (e) {
            e.preventDefault();
            var id = $(this).data('id');
            var url = $(this).attr('href');
            $.ajax({
                url: '<?php echo site_url("patient/dashboard/add_history"); ?>',
                type: 'POST',
                data: {'id': id},
                dataType: 'json',
                success: function (res) {
                    if (res.status == 1) { window.open(url, '_blank'); }
                },
                error: function () {
                    alert('<?php echo $this->lang->line('error_occurred_please_try_again'); ?>');
                }
            });
        });

        // Fetch the status content FIRST, then open the modal once #zoom_details is populated.
        $(document).on('click', '.js-open-livestatus', function (e) {
            e.preventDefault();
            var $btn = $(this);
            if ($btn.hasClass('disabled')) { return; }
            var id = $btn.data('id');
            $btn.addClass('disabled');
            $('#zoom_details').html('');
            $.ajax({
                type: 'POST',
                url: '<?php echo site_url("patient/dashboard/getlivestatus"); ?>',
                data: {'id': id},
                dataType: 'JSON',
                success: function (data) {
                    $('#zoom_details').html(data.page);
                    bootstrap.Modal.getOrCreateInstance(document.getElementById('modal-chkstatus')).show();
                },
                error: function () {
                    alert('<?php echo $this->lang->line('error_occurred_please_try_again'); ?>');
                },
                complete: function () { $btn.removeClass('disabled'); }
            });
        });
    });
</script>
