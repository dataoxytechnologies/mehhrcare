<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <h3 class="card-title mb-0">Consultation & Vial Fees</h3>
    </div>
    <div class="card-body table-responsive">
        <table class="table table-striped table-bordered table-hover example">
            <thead>
                <tr>
                    <th>Fee Code</th>
                    <th>Fee Type</th>
                    <th>Name</th>
                    <th>Group Code</th>
                    <th>Coverage</th>
                    <th>Amount</th>
                    <th>GST Rate</th>
                    <th>Description</th>
                    <th>Vial Type</th>
                    <th>Fill Amount</th>
                    <th class="text-end noExport">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($fees)): ?>
                <tr><td colspan="11" class="text-center text-muted">No consultation or vial fees yet.</td></tr>
                <?php else: foreach ($fees as $f): ?>
                <tr>
                    <td><strong><?php echo htmlspecialchars($f->fee_code); ?></strong></td>
                    <td><?php echo htmlspecialchars($f->fee_type); ?></td>
                    <td><?php echo htmlspecialchars($f->name); ?></td>
                    <td><?php echo htmlspecialchars($f->group_code); ?></td>
                    <td><?php echo htmlspecialchars($f->coverage); ?></td>
                    <td><?php echo $f->amount ? '$' . number_format($f->amount, 2) : '-'; ?></td>
                    <td><?php echo $f->gst_rate ? number_format($f->gst_rate, 2) . '%' : '-'; ?></td>
                    <td><?php echo htmlspecialchars($f->description ?? '-'); ?></td>
                    <td><?php echo htmlspecialchars($f->vial_type ?? '-'); ?></td>
                    <td><?php echo $f->fill_amount ? '$' . number_format($f->fill_amount, 2) : '-'; ?></td>
                    <td class="text-end text-nowrap">
                        <button type="button" class="btn btn-sm btn-outline-primary"><i class="fa fa-pencil"></i></button>
                        <a href="#" class="btn btn-sm btn-outline-danger"><i class="fa fa-trash"></i></a>
                    </td>
                </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</div>