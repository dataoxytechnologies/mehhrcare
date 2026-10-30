<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
?>
<div class="container-fluid px-1 py-1">
    <div class="card sh-patient-card">
        <div class="card-header">
            <h3 class="card-title mb-0"><?php echo $this->lang->line('content_list'); ?></h3>
        </div>
        <div class="card-body p-3">
            <div class="download_label"><?php echo $this->lang->line('content_list'); ?></div>
            <div class="table-responsive">
                <table class="table table-striped table-bordered table-hover example">
                    <thead>
                        <tr>
                            <th><?php echo $this->lang->line('title'); ?></th>
                            <th><?php echo $this->lang->line('share_date'); ?></th>
                            <th><?php echo $this->lang->line('valid_upto'); ?></th>
                            <th><?php echo $this->lang->line('shared_by'); ?></th>
                            <th class="text-end noExport"><?php echo $this->lang->line('action'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($contentlist)) {
                            foreach ($contentlist as $contentlist_value) { ?>
                        <tr>
                            <td><?php echo html_escape($contentlist_value->title); ?></td>
                            <td><?php echo $this->customlib->YYYYMMDDTodateFormat($contentlist_value->share_date, $timeformat); ?></td>
                            <td><?php echo $this->customlib->YYYYMMDDTodateFormat($contentlist_value->valid_upto, $timeformat); ?></td>
                            <td>
                                <?php if ($superadmin_restriction != 'disabled') {
                                    echo html_escape($contentlist_value->name) . ' ' . html_escape($contentlist_value->surname) . ' (' . html_escape($contentlist_value->employee_id) . ')';
                                } ?>
                            </td>
                            <td class="text-end">
                                <a href="<?php echo site_url('patient/dashboard/viewcontent/') . (int)$contentlist_value->id; ?>"
                                   class="btn btn-sm btn-outline-secondary"
                                   data-bs-toggle="tooltip" title="<?php echo $this->lang->line('view'); ?>">
                                    <i class="fa fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        <?php } } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
