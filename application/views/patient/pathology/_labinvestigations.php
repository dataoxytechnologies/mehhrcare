<?php if (!empty($result->pathology_parameter)): ?>
<div class="sh-form-card">
    <div class="sh-card-header">
        <span class="sh-card-header-title"><?php echo html_escape($result->test_name . ' (' . $result->short_name . ')'); ?></span>
    </div>
    <div class="table-responsive">
        <table class="table table-sm table-hover mb-0">
            <thead>
                <tr>
                    <th class="sh-lab-th-num">#</th>
                    <th><?php echo $this->lang->line('test_parameter_name'); ?></th>
                    <th><?php echo $this->lang->line('reference_range'); ?></th>
                    <th><?php echo $this->lang->line('report_value'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php $row_counter = 1; foreach ($result->pathology_parameter as $parameter_value): ?>
                <tr>
                    <td><?php echo $row_counter++; ?></td>
                    <td>
                        <?php echo html_escape($parameter_value->parameter_name); ?>
                        <?php if ($parameter_value->description != ''): ?>
                        <div class="text-muted sh-lab-desc-text"><?php echo $this->lang->line('description'); ?>: <?php echo html_escape($parameter_value->description); ?></div>
                        <?php endif; ?>
                    </td>
                    <td><?php echo html_escape($parameter_value->reference_range . ' ' . $parameter_value->unit_name); ?></td>
                    <td><?php if ($parameter_value->pathology_report_value != '') echo html_escape($parameter_value->pathology_report_value . ' ' . $parameter_value->unit_name); ?></td>
                </tr>
                <?php endforeach; ?>
                <?php if (!empty($parameter_value->pathology_result)): ?>
                <tr>
                    <td colspan="4"><strong><?php echo $this->lang->line('result'); ?>:</strong> <?php echo nl2br(html_escape($parameter_value->pathology_result)); ?></td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php else: ?>
<div class="alert alert-info mb-0"><?php echo $this->lang->line('no_record_found'); ?></div>
<?php endif; ?>
