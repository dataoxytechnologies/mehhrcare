<?php
defined('BASEPATH') or exit('No direct script access allowed');
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
?>
<style>
/* Timeline Accordion Styles */
.accordion-item {
    border: none;
    border-bottom: 1px solid rgba(0,0,0,0.05);
    background: transparent;
}
.accordion-item-last {
    border-bottom: none;
}
.accordion-button {
    background: #f8f9fa;
    padding: 12px 16px;
    border-radius: 4px !important;
    font-size: 0.95rem;
}
.accordion-button:not(.collapsed) {
    background: #e9ecef;
    color: #1a1a1a;
    box-shadow: none;
}
.accordion-button:focus {
    box-shadow: none;
    border-color: #dee2e6;
}
.accordion-button .badge {
    font-weight: 500;
}
.accordion-body {
    background: #ffffff;
    padding: 16px 20px;
    border-top: 1px solid rgba(0,0,0,0.05);
}
.timeline-marker-sm {
    display: inline-block;
    width: 10px;
    height: 10px;
    border-radius: 50%;
    flex-shrink: 0;
}
.bg-primary-subtle {
    background-color: #cfe2ff !important;
    color: #084298 !important;
}
.bg-success-subtle {
    background-color: #d1e7dd !important;
    color: #0a3622 !important;
}
.bg-secondary-subtle {
    background-color: #e9ecef !important;
    color: #1a1a1a !important;
}
.accordion-button .d-flex {
    gap: 0.5rem;
}
.table-borderless td {
    padding: 0.3rem 0;
}
@media (max-width: 768px) {
    .accordion-button .d-flex {
        flex-wrap: wrap;
    }
    .accordion-button .text-muted.small {
        margin-top: 4px;
    }
}
</style>
<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
        <div class="d-flex align-items-center gap-2">
            <i class="fa fa-eye sh-icon-soft"></i>
            <h3 class="card-title titlefix mb-0">
                Patient Flow Details - <?php echo $this->lang->line('visit'); ?> #<?php echo isset($visit->visit_no) ? $visit->visit_no : 'N/A'; ?>
            </h3>
        </div>
        <div class="d-flex gap-2 align-items-center flex-wrap">
            <a href="<?php echo site_url('admin/patientflow'); ?>" class="btn btn-sm btn-outline-secondary">
                <i class="fa fa-arrow-left"></i> <?php echo $this->lang->line('back'); ?>
            </a>
            <?php if ($can_forward && isset($visit->next_level_id) && $visit->next_level_id): ?>
            <button type="button" class="btn btn-sm btn-primary" onclick="advanceVisit(<?php echo $visit->id; ?>)">
                <i class="fa fa-arrow-right"></i> Forward to <?php echo isset($visit->next_title) ? $visit->next_title : 'Next'; ?>
            </button>
            <?php endif; ?>
            <?php if ($can_backward): ?>
            <button type="button" class="btn btn-sm btn-warning" onclick="backwardVisit(<?php echo $visit->id; ?>)">
                <i class="fa fa-arrow-left"></i> Back to <?php echo isset($visit->prev_title) ? $visit->prev_title : 'Previous'; ?>
            </button>
            <?php endif; ?>
            <?php if ($can_approve && isset($visit->approve_status) && $visit->approve_status != 'approved'): ?>
            <button type="button" class="btn btn-sm btn-success" onclick="approveVisit(<?php echo $visit->id; ?>)">
                <i class="fa fa-check"></i> Approve
            </button>
            <?php endif; ?>
        </div>
    </div>

    <div class="card-body">
        <!-- Status Badges -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex flex-wrap gap-3 align-items-center">
                    <?php if (isset($visit->level_phase)): ?>
                    <span class="badge badge-lg bg-info-subtle text-info-emphasis"><?php echo $this->lang->line('phase'); ?>: <?php echo $visit->level_phase; ?></span>
                    <?php endif; ?>
                    <span class="badge badge-lg bg-primary-subtle text-primary-emphasis"><?php echo $this->lang->line('current_level'); ?>: <?php echo isset($visit->level_title) ? $visit->level_title : 'N/A'; ?></span>
                    <span class="badge badge-lg <?php echo (isset($visit->payment_status) && $visit->payment_status == 'paid') ? 'bg-success-subtle text-success-emphasis' : 'bg-warning-subtle text-warning-emphasis'; ?>">
                        <?php echo $this->lang->line('payment'); ?>: <?php echo isset($visit->payment_status) ? ucfirst($visit->payment_status) : 'Pending'; ?>
                    </span>
                    <span class="badge badge-lg <?php echo (isset($visit->approve_status) && $visit->approve_status == 'approved') ? 'bg-success-subtle text-success-emphasis' : 'bg-secondary-subtle text-secondary-emphasis'; ?>">
                        <?php echo $this->lang->line('approval'); ?>: <?php echo isset($visit->approve_status) ? ucfirst($visit->approve_status) : 'Pending'; ?>
                    </span>
                    <span class="badge badge-lg <?php 
                        echo (isset($visit->billing_status) && $visit->billing_status == 'paid') ? 'bg-success-subtle text-success-emphasis' : 
                             ((isset($visit->billing_status) && $visit->billing_status == 'billed') ? 'bg-info-subtle text-info-emphasis' : 
                             ((isset($visit->billing_status) && $visit->billing_status == 'cancelled') ? 'bg-danger-subtle text-danger-emphasis' : 'bg-warning-subtle text-warning-emphasis')); 
                    ?>">
                        <?php echo $this->lang->line('billing'); ?>: <?php echo isset($visit->billing_status) ? ucfirst($visit->billing_status) : 'Pending'; ?>
                    </span>
                    <?php if (isset($visit->billing_reference) && $visit->billing_reference): ?>
                    <span class="badge badge-lg bg-secondary-subtle text-secondary-emphasis">
                        <?php echo $this->lang->line('billing_ref'); ?>: <?php echo $visit->billing_reference; ?>
                    </span>
                    <?php endif; ?>
                    <?php if (isset($visit->token_no) && $visit->token_no): ?>
                    <span class="badge badge-lg bg-dark-subtle text-dark-emphasis">
                        <i class="fa fa-ticket"></i> <?php echo $this->lang->line('token'); ?>: <?php echo $visit->token_no; ?>
                        <?php if (!empty($visit->token_status)): ?>
                        · <?php echo htmlspecialchars($visit->token_status); ?>
                        <?php endif; ?>
                    </span>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Patient Information -->
        <div class="row">
            <div class="col-md-6">
                <div class="card card-outline card-info">
                    <div class="card-header">
                        <h5 class="card-title mb-0"><i class="fa fa-user"></i> Patient Information</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered table-striped table-sm">
                            <tr>
                                <th width="35%">Patient Name</th>
                                <td><?php echo isset($visit->patient_name) ? $visit->patient_name : 'N/A'; ?></td>
                            </tr>
                            <tr>
                                <th>Patient UID</th>
                                <td><code><?php echo isset($visit->patient_uuid) ? $visit->patient_uuid : 'N/A'; ?></code></td>
                            </tr>
                            <tr>
                                <th>Gender</th>
                                <td><?php echo isset($visit->gender) ? ucfirst($visit->gender) : 'N/A'; ?></td>
                            </tr>
                            <tr>
                                <th>Age</th>
                                <td><?php echo isset($visit->age) ? $visit->age : 'N/A'; ?></td>
                            </tr>
                            <tr>
                                <th>Mobile</th>
                                <td><?php echo isset($visit->mobileno) ? $visit->mobileno : 'N/A'; ?></td>
                            </tr>
                            <tr>
                                <th>Guardian</th>
                                <td><?php echo isset($visit->guardian_name) ? $visit->guardian_name : 'N/A'; ?></td>
                            </tr>
                            <tr>
                                <th>Address</th>
                                <td><?php echo isset($visit->address) ? $visit->address : 'N/A'; ?></td>
                            </tr>
                            <tr>
                                <th>Registration Date</th>
                                <td><?php echo isset($visit->created_at) ? date('d-m-Y H:i', strtotime($visit->created_at)) : 'N/A'; ?></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Current Visit Information -->
            <div class="col-md-6">
                <div class="card card-outline card-primary">
                    <div class="card-header">
                        <h5 class="card-title mb-0"><i class="fa fa-clock-o"></i> Current Visit Details</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered table-striped table-sm">
                            <tr>
                                <th width="35%">Visit Number</th>
                                <td><strong><?php echo isset($visit->visit_no) ? $visit->visit_no : 'N/A'; ?></strong></td>
                            </tr>
                            <tr>
                                <th>Current Level</th>
                                <td><span class="badge bg-primary-subtle text-primary-emphasis"><?php echo isset($visit->level_code) ? $visit->level_code : 'N/A'; ?></span> <?php echo isset($visit->level_title) ? $visit->level_title : 'N/A'; ?></td>
                            </tr>
                            <tr>
                                <th>Counter Type</th>
                                <td><?php echo isset($visit->level_counter) ? ucfirst($visit->level_counter) : 'N/A'; ?></td>
                            </tr>
                            <tr>
                                <th>Level Started</th>
                                <td><?php echo isset($visit->level_started_at) ? date('d-m-Y H:i', strtotime($visit->level_started_at)) : 'N/A'; ?></td>
                            </tr>
                            <!-- <tr>
                                <th>Amount</th>
                                <td><strong><?php echo $currency_symbol . ' ' . (isset($visit->amount) ? number_format($visit->amount, 2) : '0.00'); ?></strong></td>
                            </tr> 
                            <tr>
                                <th>Payment Status</th>
                                <td>
                                    <span class="badge <?php echo (isset($visit->payment_status) && $visit->payment_status == 'paid') ? 'bg-success-subtle text-success-emphasis' : 'bg-warning-subtle text-warning-emphasis'; ?>">
                                        <?php echo isset($visit->payment_status) ? ucfirst($visit->payment_status) : 'Pending'; ?>
                                    </span>
                                    <?php if (isset($visit->token_no) && $visit->token_no): ?>
                                    <span class="badge bg-dark-subtle text-dark-emphasis">Token: <?php echo $visit->token_no; ?></span>
                                    <?php endif; ?>
                                </td>
                            </tr> -->
                            <tr>
                                <th>Approval Status</th>
                                <td>
                                    <span class="badge <?php echo (isset($visit->approve_status) && $visit->approve_status == 'approved') ? 'bg-success-subtle text-success-emphasis' : 'bg-secondary-subtle text-secondary-emphasis'; ?>">
                                        <?php echo isset($visit->approve_status) ? ucfirst($visit->approve_status) : 'Pending'; ?>
                                    </span>
                                    <?php if (isset($visit->approved_at) && $visit->approved_at): ?>
                                    <small class="text-muted">on <?php echo date('d-m-Y H:i', strtotime($visit->approved_at)); ?></small>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <tr>
                                <th>Created By</th>
                                <td><?php echo isset($visit->staff_name) ? $visit->staff_name . ' ' . (isset($visit->staff_surname) ? $visit->staff_surname : '') : 'N/A'; ?></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

<!-- OPD Details -->
        <?php if (isset($opd_details) && !empty($opd_details)): ?>
        <?php $cs = !empty($opd_details->currency_symbol) ? $opd_details->currency_symbol : $currency_symbol; ?>
        <div class="row mt-3">
            <div class="col-12">
                <div class="card card-outline card-success">
                    <div class="card-header">
                        <h5 class="card-title mb-0"><i class="fa fa-stethoscope"></i> OPD Details</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-bordered table-striped table-sm">
                                    <tr>
                                        <th width="35%">OPD Number</th>
                                        <td><?php echo $this->customlib->getSessionPrefixByType('opd_no') . $opd_details->id; ?></td>
                                    </tr>
                                    <tr>
                                        <th>Checkup ID</th>
                                        <td><?php echo isset($opd_details->visit_details_id) ? $this->customlib->getSessionPrefixByType('checkup_id') . $opd_details->visit_details_id : 'N/A'; ?></td>
                                    </tr>
                                    <tr>
                                        <th>Case Reference</th>
                                        <td><?php echo !empty($opd_details->case_reference_id) ? $opd_details->case_reference_id : 'N/A'; ?></td>
                                    </tr>
                                    <tr>
                                        <th>Doctor</th>
                                        <td><?php echo isset($opd_details->doctor_name) ? composeStaffNameByString($opd_details->doctor_name, $opd_details->doctor_surname, $opd_details->doctor_employee_id) : 'N/A'; ?></td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-bordered table-striped table-sm">
                                    <tr>
                                        <th width="35%">Consultation Date</th>
                                        <td><?php echo !empty($opd_details->appointment_date) ? date('d-m-Y H:i', strtotime($opd_details->appointment_date)) : 'N/A'; ?></td>
                                    </tr>
                                    <tr>
                                        <th>Reference</th>
                                        <td><?php echo !empty($opd_details->refference) ? $opd_details->refference : 'N/A'; ?></td>
                                    </tr>
                                    <tr>
                                        <th>Payment Mode</th>
                                        <td><?php echo !empty($opd_details->payment_mode) ? ucfirst($opd_details->payment_mode) : 'N/A'; ?></td>
                                    </tr>
                                    <tr>
                                        <th>Payment Status</th>
                                        <td>
                                            <span class="badge <?php echo (isset($visit->payment_status) && $visit->payment_status == 'paid') ? 'bg-success-subtle text-success-emphasis' : 'bg-warning-subtle text-warning-emphasis'; ?>">
                                                <?php echo isset($visit->payment_status) ? ucfirst($visit->payment_status) : 'Pending'; ?>
                                            </span>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
<?php if (!empty($opd_details->symptoms)): ?>
                        <div class="row mt-2">
                            <div class="col-12">
                                <table class="table table-bordered table-striped table-sm">
                                    <tr>
                                        <th width="18%">Symptoms</th>
                                        <td><?php echo nl2br(html_escape($opd_details->symptoms)); ?></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <?php endif; ?>

                        <?php if (!empty($opd_details->note_remark) || !empty($opd_details->note)): ?>
                        <div class="row mt-2">
                            <div class="col-12">
                                <table class="table table-bordered table-striped table-sm">
                                    <tr>
                                        <th width="18%">Note</th>
                                        <td><?php echo nl2br(html_escape(!empty($opd_details->note_remark) ? $opd_details->note_remark : $opd_details->note)); ?></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <?php endif; ?>

                        <div class="row mt-2">
                            <div class="col-12">
                                <table class="table table-bordered table-sm">
                                    <tr>
                                        <th>Net Amount</th>
                                        <td><strong><?php echo $cs . ' ' . number_format((float) $opd_details->net_amount, 2); ?></strong></td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <?php if (isset($opd_details->charges) && !empty($opd_details->charges)): ?>
                        <div class="mt-3">
                            <h6>OPD Charges (from patient_charges)</h6>
                            <table class="table table-bordered table-sm">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Service/Item</th>
                                        <th>Qty</th>
                                        <th>Rate</th>
                                        <th>Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $counter = 1; ?>
                                    <?php foreach ($opd_details->charges as $charge): ?>
                                    <tr>
                                        <td><?php echo $counter++; ?></td>
                                        <td><?php echo isset($charge['name']) ? html_escape($charge['name']) : 'N/A'; ?></td>
                                        <td><?php echo isset($charge['qty']) ? (int) $charge['qty'] : '0'; ?></td>
                                        <td><?php echo $cs . ' ' . number_format((float) (!empty($charge['org_charge']) ? $charge['org_charge'] : $charge['standard_charge']), 2); ?></td>
                                        <td><?php echo $cs . ' ' . number_format((float) $charge['amount'], 2); ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="4" class="text-right">Total</th>
                                        <th><?php echo $cs . ' ' . number_format((float) $opd_details->net_amount, 2); ?></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Flow Timeline / Logs -->
        <div class="row mt-3">
            <div class="col-12">
                <div class="card card-outline card-secondary">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fa fa-history"></i> Flow Timeline
                            <?php if (isset($logs) && !empty($logs)): ?>
                            <span class="badge bg-secondary ms-2"><?php echo count($logs); ?> events</span>
                            <?php endif; ?>
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php if (isset($logs) && !empty($logs)): ?>
                        <!-- Accordion -->
                        <div class="accordion" id="flowTimelineAccordion">
                            <?php 
                            $event_count = count($logs);
                            $counter = 0;
                            foreach ($logs as $log): 
                                $counter++;
                                $is_last = ($counter == $event_count);
                                $accordion_id = 'timeline-' . $log->visit_id . '-' . $counter;
                            ?>
                            <div class="accordion-item <?php echo $is_last ? 'accordion-item-last' : ''; ?>">
                                <h2 class="accordion-header" id="heading-<?php echo $accordion_id; ?>">
                                    <button class="accordion-button <?php echo $is_last ? '' : 'collapsed'; ?>" 
                                            type="button" 
                                            data-bs-toggle="collapse" 
                                            data-bs-target="#collapse-<?php echo $accordion_id; ?>" 
                                            aria-expanded="<?php echo $is_last ? 'true' : 'false'; ?>" 
                                            aria-controls="collapse-<?php echo $accordion_id; ?>">
                                        <div class="d-flex justify-content-between align-items-center w-100 me-3">
                                            <div class="d-flex align-items-center gap-3">
                                                <span class="timeline-marker-sm <?php 
                                                    echo (isset($log->action) && $log->action == 'forward') ? 'bg-primary' : 
                                                        ((isset($log->action) && $log->action == 'backward') ? 'bg-warning' : 
                                                        ((isset($log->action) && $log->action == 'approve') ? 'bg-success' : 
                                                        'bg-info'));
                                                ?>"></span>
                                                <strong><?php echo isset($log->action) ? ucfirst($log->action) : 'N/A'; ?></strong>
                                                <?php if (isset($log->from_level_id) && isset($log->to_level_id) && $log->from_level_id != $log->to_level_id): ?>
                                                <span class="text-muted">from</span>
                                                <span class="badge bg-primary-subtle text-primary-emphasis"><?php echo isset($log->from_code) ? $log->from_code : 'N/A'; ?></span>
                                                <span class="text-muted">to</span>
                                                <span class="badge bg-success-subtle text-success-emphasis"><?php echo isset($log->to_code) ? $log->to_code : 'N/A'; ?></span>
                                                <?php elseif (isset($log->from_level_id)): ?>
                                                <span class="text-muted">at</span>
                                                <span class="badge bg-secondary-subtle text-secondary-emphasis"><?php echo isset($log->from_code) ? $log->from_code : 'N/A'; ?></span>
                                                <?php endif; ?>
                                            </div>
                                            <div class="text-muted small">
                                                <i class="fa fa-clock-o"></i> <?php echo isset($log->created_at) ? date('d-m-Y H:i', strtotime($log->created_at)) : 'N/A'; ?>
                                            </div>
                                        </div>
                                    </button>
                                </h2>
                                <div id="collapse-<?php echo $accordion_id; ?>" 
                                    class="accordion-collapse collapse <?php echo $is_last ? 'show' : ''; ?>" 
                                    aria-labelledby="heading-<?php echo $accordion_id; ?>" 
                                    data-bs-parent="#flowTimelineAccordion">
                                    <div class="accordion-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <table class="table table-sm table-borderless mb-0">
                                                    <tr>
                                                        <td width="30%"><strong>Action</strong></td>
                                                        <td><?php echo isset($log->action) ? ucfirst($log->action) : 'N/A'; ?></td>
                                                    </tr>
                                                    <?php if (isset($log->from_level_id) && isset($log->to_level_id) && $log->from_level_id != $log->to_level_id): ?>
                                                    <tr>
                                                        <td><strong>From Level</strong></td>
                                                        <td>
                                                            <span class="badge bg-primary-subtle text-primary-emphasis">
                                                                <?php echo isset($log->from_code) ? $log->from_code : 'N/A'; ?>
                                                                - <?php echo isset($log->from_title) ? $log->from_title : 'N/A'; ?>
                                                            </span>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td><strong>To Level</strong></td>
                                                        <td>
                                                            <span class="badge bg-success-subtle text-success-emphasis">
                                                                <?php echo isset($log->to_code) ? $log->to_code : 'N/A'; ?>
                                                                - <?php echo isset($log->to_title) ? $log->to_title : 'N/A'; ?>
                                                            </span>
                                                        </td>
                                                    </tr>
                                                    <?php elseif (isset($log->from_level_id)): ?>
                                                    <tr>
                                                        <td><strong>Level</strong></td>
                                                        <td>
                                                            <span class="badge bg-secondary-subtle text-secondary-emphasis">
                                                                <?php echo isset($log->from_code) ? $log->from_code : 'N/A'; ?>
                                                                - <?php echo isset($log->from_title) ? $log->from_title : 'N/A'; ?>
                                                            </span>
                                                        </td>
                                                    </tr>
                                                    <?php endif; ?>
                                                </table>
                                            </div>
                                            <div class="col-md-6">
                                                <table class="table table-sm table-borderless mb-0">
                                                    <tr>
                                                        <td width="30%"><strong>Performed By</strong></td>
                                                        <td>
                                                            <i class="fa fa-user"></i> 
                                                            <?php echo isset($log->acted_by_name) ? $log->acted_by_name : 'N/A'; ?>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td><strong>Date & Time</strong></td>
                                                        <td>
                                                            <i class="fa fa-clock-o"></i> 
                                                            <?php echo isset($log->created_at) ? date('d-m-Y H:i:s', strtotime($log->created_at)) : 'N/A'; ?>
                                                        </td>
                                                    </tr>
                                                    <?php if (isset($log->remarks) && $log->remarks): ?>
                                                    <tr>
                                                        <td><strong>Remarks</strong></td>
                                                        <td><?php echo $log->remarks; ?></td>
                                                    </tr>
                                                    <?php endif; ?>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <?php else: ?>
                        <div class="text-center text-muted py-3">
                            <i class="fa fa-info-circle"></i> No flow history available for this visit.
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Patient Visit History -->
        <?php if (isset($history) && !empty($history)): ?>
        <div class="row mt-3">
            <div class="col-12">
                <div class="card card-outline card-light">
                    <div class="card-header">
                        <h5 class="card-title mb-0"><i class="fa fa-list"></i> Patient's Visit History</h5>
                    </div>
                    <div class="card-body table-responsive">
                        <table class="table table-bordered table-striped table-hover table-sm">
                            <thead>
                                <tr>
                                    <th>Visit No</th>
                                    <th>Level</th>
                                    <th>Date</th>
                                    <th>Amount</th>
                                    <th>Payment</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($history as $h): ?>
                                <?php if ($h->id != $visit->id): ?>
                                <tr>
                                    <td><?php echo isset($h->visit_no) ? $h->visit_no : 'N/A'; ?></td>
                                    <td><span class="badge bg-info-subtle text-info-emphasis"><?php echo isset($h->level_code) ? $h->level_code : 'N/A'; ?></span> <?php echo isset($h->level_title) ? $h->level_title : 'N/A'; ?></td>
                                    <td><?php echo isset($h->created_at) ? date('d-m-Y', strtotime($h->created_at)) : 'N/A'; ?></td>
                                    <td><?php echo $currency_symbol . ' ' . (isset($h->amount) ? number_format($h->amount, 2) : '0.00'); ?></td>
                                    <td>
                                        <span class="badge <?php echo (isset($h->payment_status) && $h->payment_status == 'paid') ? 'bg-success-subtle text-success-emphasis' : 'bg-warning-subtle text-warning-emphasis'; ?>">
                                            <?php echo isset($h->payment_status) ? ucfirst($h->payment_status) : 'Pending'; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge <?php echo (isset($h->approve_status) && $h->approve_status == 'approved') ? 'bg-success-subtle text-success-emphasis' : 'bg-secondary-subtle text-secondary-emphasis'; ?>">
                                            <?php echo isset($h->approve_status) ? ucfirst($h->approve_status) : 'Pending'; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="<?php echo site_url('admin/patientflow/view/' . $h->id); ?>" class="btn btn-sm btn-outline-primary">
                                            <i class="fa fa-eye"></i> View
                                        </a>
                                    </td>
                                </tr>
                                <?php endif; ?>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Billing Information (if R2 level) -->
        <?php if ($can_manage_billing): ?>
        <!-- <div class="row mt-3">
            <div class="col-12">
                <div class="card card-outline card-warning">
                    <div class="card-header">
                        <h5 class="card-title mb-0"><i class="fa fa-credit-card"></i> Billing Management</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Billing Reference</label>
                                    <div class="input-group">
                                        <input type="text" id="billing_ref" class="form-control" 
                                               value="<?php echo isset($visit->billing_reference) ? $visit->billing_reference : ''; ?>" 
                                               placeholder="Generate billing reference" readonly>
                                        <?php if (empty($visit->billing_reference)): ?>
                                        <div class="input-group-append">
                                            <button class="btn btn-primary" onclick="generateBillingRef(<?php echo $visit->id; ?>)">
                                                <i class="fa fa-refresh"></i> Generate
                                            </button>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Billing Status</label>
                                    <div class="input-group">
                                        <select id="billing_status" class="form-control">
                                            <?php foreach ($billing_statuses as $status): ?>
                                            <option value="<?php echo $status; ?>" 
                                                <?php echo (isset($visit->billing_status) && $visit->billing_status == $status) ? 'selected' : ''; ?>>
                                                <?php echo ucfirst($status); ?>
                                            </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <div class="input-group-append">
                                            <button class="btn btn-primary" onclick="updateBillingStatus(<?php echo $visit->id; ?>)">
                                                <i class="fa fa-save"></i> Update
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div> -->
        <?php endif; ?>

    </div>
</div>

<!-- JavaScript for actions -->
<script>
$(document).ready(function() {
    // Initialize any tooltips or date pickers if needed
});

function advanceVisit(visitId) {
    if (!confirm('Are you sure you want to advance this patient to the next level?')) {
        return false;
    }
    
    var remarks = prompt('Enter remarks (optional):', 'Moving to next level');
    
    $.ajax({
        url: '<?php echo site_url("admin/patientflow/advance"); ?>',
        type: 'POST',
        data: {
            visit_id: visitId,
            remarks: remarks
        },
        dataType: 'json',
        success: function(response) {
            if (response.status) {
                toastr.success(response.message);
                setTimeout(function() {
                    window.location.reload();
                }, 1000);
            } else {
                toastr.error(response.message);
            }
        },
        error: function() {
            toastr.error('An error occurred while advancing the visit.');
        }
    });
}

function backwardVisit(visitId) {
    if (!confirm('Are you sure you want to send this patient back to the previous level?')) {
        return false;
    }
    
    var remarks = prompt('Enter remarks for moving backward (optional):', 'Returning to previous level');
    
    $.ajax({
        url: '<?php echo site_url("admin/patientflow/backward"); ?>',
        type: 'POST',
        data: {
            visit_id: visitId,
            remarks: remarks
        },
        dataType: 'json',
        success: function(response) {
            if (response.status) {
                toastr.success(response.message);
                setTimeout(function() {
                    window.location.reload();
                }, 1000);
            } else {
                toastr.error(response.message);
            }
        },
        error: function() {
            toastr.error('An error occurred while moving the visit backward.');
        }
    });
}

function approveVisit(visitId) {
    if (!confirm('Are you sure you want to approve this visit?')) {
        return false;
    }
    
    var remarks = prompt('Enter approval remarks (optional):', 'Approved at this level');
    
    $.ajax({
        url: '<?php echo site_url("admin/patientflow/approve"); ?>',
        type: 'POST',
        data: {
            visit_id: visitId,
            remarks: remarks
        },
        dataType: 'json',
        success: function(response) {
            if (response.status) {
                toastr.success(response.message);
                setTimeout(function() {
                    window.location.reload();
                }, 1000);
            } else {
                toastr.error(response.message);
            }
        },
        error: function() {
            toastr.error('An error occurred while approving the visit.');
        }
    });
}

function generateBillingRef(visitId) {
    if (!confirm('Generate billing reference for this visit?')) {
        return false;
    }
    
    $.ajax({
        url: '<?php echo site_url("admin/patientflow/generate_billing_ref"); ?>',
        type: 'POST',
        data: { visit_id: visitId },
        dataType: 'json',
        success: function(response) {
            if (response.status) {
                toastr.success(response.message);
                $('#billing_ref').val(response.reference);
                setTimeout(function() {
                    window.location.reload();
                }, 1500);
            } else {
                toastr.error(response.message);
            }
        },
        error: function() {
            toastr.error('An error occurred while generating billing reference.');
        }
    });
}

function updateBillingStatus(visitId) {
    var status = $('#billing_status').val();
    if (!confirm('Change billing status to "' + status + '"?')) {
        return false;
    }
    
    var remarks = prompt('Enter remarks (optional):', 'Billing status updated');
    
    $.ajax({
        url: '<?php echo site_url("admin/patientflow/update_billing_status"); ?>',
        type: 'POST',
        data: {
            visit_id: visitId,
            status: status,
            remarks: remarks
        },
        dataType: 'json',
        success: function(response) {
            if (response.status) {
                toastr.success(response.message);
                setTimeout(function() {
                    window.location.reload();
                }, 1000);
            } else {
                toastr.error(response.message);
            }
        },
        error: function() {
            toastr.error('An error occurred while updating billing status.');
        }
    });
}
</script>

<!-- Custom CSS for Timeline -->
<style>
.timeline {
    position: relative;
    padding: 10px 0;
}
.timeline-item {
    padding: 10px 0 10px 30px;
    position: relative;
    border-left: 2px solid #e9ecef;
    margin-left: 10px;
}
.timeline-marker {
    position: absolute;
    left: -8px;
    top: 15px;
    width: 14px;
    height: 14px;
    border-radius: 50%;
    background: #6c757d;
}
.timeline-marker-primary { background: #007bff; }
.timeline-marker-success { background: #28a745; }
.timeline-marker-warning { background: #ffc107; }
.timeline-marker-info { background: #17a2b8; }
.timeline-marker-danger { background: #dc3545; }
.timeline-content {
    background: #f8f9fa;
    padding: 12px 15px;
    border-radius: 4px;
}
.badge-lg {
    font-size: 0.9rem;
    padding: 0.5rem 0.75rem;
}
</style>