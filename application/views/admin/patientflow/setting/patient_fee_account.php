<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <h3 class="card-title mb-0">Patient Fee Accounts</h3>
    </div>
    <div class="card-body table-responsive">
        <table class="table table-striped table-bordered table-hover example">
            <thead>
                <tr>
                    <th>UID</th>
                    <th>Patient ID</th>
                    <th>Programme Code</th>
                    <th>Coverage</th>
                    <th>Enrolled On</th>
                    <th>Benefit Discount</th>
                    <th>Benefit %</th>
                    <th>Benefit Status</th>
                    <th>Payment Mode</th>
                    <th>Requested By</th>
                    <th>Approved By</th>
                    <th>Approved At</th>
                    <th>Stagewise Approved By</th>
                    <th>Created At</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($accounts)): ?>
                <tr><td colspan="14" class="text-center text-muted">No patient fee accounts yet.</td></tr>
                <?php else: foreach ($accounts as $a): ?>
                <tr>
                    <td><?php echo htmlspecialchars($a->uid); ?></td>
                    <td><?php echo $a->patient_id; ?></td>
                    <td><?php echo htmlspecialchars($a->programme_code); ?></td>
                    <td><?php echo htmlspecialchars($a->coverage); ?></td>
                    <td><?php echo $a->enrolled_on; ?></td>
                    <td><?php echo htmlspecialchars($a->benefit_discount_id ?? '-'); ?></td>
                    <td><?php echo $a->benefit_percent; ?></td>
                    <td><?php echo htmlspecialchars($a->benefit_status); ?></td>
                    <td><?php echo htmlspecialchars($a->benefit_payment_mode); ?></td>
                    <td><?php echo htmlspecialchars($a->benefit_requested_by ?? '-'); ?></td>
                    <td><?php echo htmlspecialchars($a->benefit_approved_by ?? '-'); ?></td>
                    <td><?php echo $a->benefit_approved_at ? $a->benefit_approved_at : '-'; ?></td>
                    <td><?php echo $a->stagewise_approved_by ?? '-'; ?></td>
                    <td><?php echo $a->created_at; ?></td>
                </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</div>