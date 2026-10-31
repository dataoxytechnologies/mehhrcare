<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <h3 class="card-title mb-0">Schedules</h3>
    </div>
    <div class="card-body table-responsive">
        <table class="table table-striped table-bordered table-hover example">
            <thead>
                <tr>
                    <th>Account ID</th>
                    <th>Quarter No</th>
                    <th>Stage From</th>
                    <th>Stage To</th>
                    <th>Label</th>
                    <th>Standard Amount</th>
                    <th>Discount %</th>
                    <th>Payable Amount</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($schedules)): ?>
                <tr><td colspan="9" class="text-center text-muted">No pricing schedule yet.</td></tr>
                <?php else: foreach ($schedules as $s): ?>
                <tr>
                    <td><?php echo htmlspecialchars($s->account_id); ?></td>
                    <td><?php echo $s->quarter_no; ?></td>
                    <td><?php echo $s->stage_from; ?></td>
                    <td><?php echo $s->stage_to; ?></td>
                    <td><?php echo htmlspecialchars($s->label); ?></td>
                    <td><?php echo $s->standard_amount ? '$' . number_format($s->standard_amount, 2) : '-'; ?></td>
                    <td><?php echo $s->discount_percent ? number_format($s->discount_percent, 2) . '%' : '-'; ?></td>
                    <td><?php echo $s->payable_amount ? '$' . number_format($s->payable_amount, 2) : '-'; ?></td>
                    <td><?php echo htmlspecialchars($s->status); ?></td>
                </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</div>