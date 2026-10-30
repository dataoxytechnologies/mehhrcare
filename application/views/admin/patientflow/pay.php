<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $this->lang->line('patient_flow_payment') ?: 'Patient Flow Payment'; ?> — <?php echo !empty($setting[0]['name']) ? htmlspecialchars($setting[0]['name']) : ''; ?></title>
    <link rel="stylesheet" href="<?php echo base_url('backend/dist/bootstrap5/css/bootstrap.min.css'); ?>">
    <link rel="stylesheet" href="<?php echo base_url('backend/dist/css/style-main.css'); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f8f9fa; }
        .payment-card { max-width: 560px; margin: 0 auto; }
        .patient-avatar { width: 60px; height: 60px; object-fit: cover; border-radius: 50%; }
        .amount-box { background: #e7f4fe; border-radius: 8px; padding: 12px 20px; }
    </style>
</head>
<body>
<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
?>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow-sm payment-card">
                <div class="card-header bg-white border-bottom py-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <h5 class="mb-0">
                            <i class="fas fa-file-invoice-dollar text-primary me-2"></i>
                            <?php echo $this->lang->line('patient_flow_payment') ?: 'Patient Flow Payment'; ?>
                        </h5>
                        <a href="<?php echo base_url('admin/patientflow'); ?>" class="btn btn-sm btn-outline-secondary">
                            <i class="fa fa-arrow-left"></i> <?php echo $this->lang->line('back') ?: 'Back'; ?>
                        </a>
                    </div>
                </div>
                <div class="card-body">

                    <!-- Patient Information -->
                    <div class="d-flex align-items-center gap-3 mb-4">
                        <?php
                        $photo = !empty($patient['image']) ? $patient['image'] : 'uploads/patient_images/no_image.png';
                        ?>
                        <img src="<?php echo $this->media_storage->getImageURL($photo); ?>" alt="Patient" class="patient-avatar">
                        <div class="flex-grow-1">
                            <h6 class="mb-1 fw-bold"><?php echo htmlspecialchars($patient_name); ?></h6>
                            <small class="text-muted d-block">
                                <?php echo $this->lang->line('visit_no') ?: 'Visit No'; ?>: <strong><?php echo htmlspecialchars($visit_no); ?></strong>
                            </small>
                            <small class="text-muted d-block">
                                <?php echo $this->lang->line('billing_reference') ?: 'Billing Ref'; ?>: <strong><?php echo htmlspecialchars($billing_reference); ?></strong>
                            </small>
                        </div>
                    </div>

                    <!-- Visit Summary -->
                    <div class="list-group list-group-flush border mb-4">
                        <div class="list-group-item py-2 px-3 bg-light d-flex justify-content-between">
                            <span class="small fw-medium"><?php echo $this->lang->line('visit_no') ?: 'Visit No'; ?></span>
                            <span class="small"><?php echo htmlspecialchars($visit->visit_no); ?></span>
                        </div>
                        <?php if (!empty($visit->billing_reference)) { ?>
                        <div class="list-group-item py-2 px-3 d-flex justify-content-between">
                            <span class="small fw-medium"><?php echo $this->lang->line('billing_reference') ?: 'Billing Ref'; ?></span>
                            <span class="small text-truncate"><?php echo htmlspecialchars($visit->billing_reference); ?></span>
                        </div>
                        <?php } ?>
                        <?php if (!empty($visit->token_no)) { ?>
                        <div class="list-group-item py-2 px-3 bg-light d-flex justify-content-between">
                            <span class="small fw-medium"><?php echo $this->lang->line('token_no') ?: 'Token No'; ?></span>
                            <span class="small"><?php echo htmlspecialchars($visit->token_no); ?></span>
                        </div>
                        <?php } ?>
                        <?php if (!empty($visit->pbhp_id)) { ?>
                        <div class="list-group-item py-2 px-3 d-flex justify-content-between">
                            <span class="small fw-medium"><?php echo $this->lang->line('pbhp') ?: 'PBHP ID'; ?></span>
                            <span class="small"><?php echo htmlspecialchars($visit->pbhp_id); ?></span>
                        </div>
                        <?php } ?>
                        <?php if (!empty($visit->mobileno)) { ?>
                        <div class="list-group-item py-2 px-3 bg-light d-flex justify-content-between">
                            <span class="small fw-medium"><?php echo $this->lang->line('mobile_no') ?: 'Mobile No'; ?></span>
                            <span class="small"><?php echo htmlspecialchars($visit->mobileno); ?></span>
                        </div>
                        <?php } ?>
                        <?php if (!empty($visit->level_code)) { ?>
                        <div class="list-group-item py-2 px-3 d-flex justify-content-between">
                            <span class="small fw-medium"><?php echo $this->lang->line('current_level') ?: 'Current Level'; ?></span>
                            <span class="small"><?php echo htmlspecialchars($visit->level_code . ' — ' . $visit->level_title); ?></span>
                        </div>
                        <?php } ?>
                    </div>

                    <?php
                    // For now, total amount defaults to 0 until a billing calculation
                    // is wired into the model. This keeps the page functional.
                    $total_amount = isset($total_amount) ? (float) $total_amount : 0.00;
                    ?>

                    <!-- Amount Summary -->
                    <div class="amount-box mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="small text-muted"><?php echo $this->lang->line('total_amount') ?: 'Total Amount'; ?></span>
                            <span class="h5 fw-bold mb-0"><?php echo $currency_symbol . ' ' . number_format($total_amount, 2, '.', ','); ?></span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="small text-muted"><?php echo $this->lang->line('paid_amount') ?: 'Paid Amount'; ?></span>
                            <span class="text-success fw-medium"><?php echo $currency_symbol . ' 0.00'; ?></span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mt-2 pt-2 border-top">
                            <span class="small fw-medium"><?php echo $this->lang->line('balance') ?: 'Balance Due'; ?></span>
                            <span class="h5 fw-bold text-primary"><?php echo $currency_symbol . ' ' . number_format($total_amount, 2, '.', ','); ?></span>
                        </div>
                    </div>

                    <?php if ($total_amount > 0): ?>
                    <!-- Payment Form -->
                    <form id="pf_payment_form" action="<?php echo site_url('admin/patientflow/process_payment'); ?>" method="post">
                        <input type="hidden" name="visit_id" value="<?php echo (int) $visit->id; ?>">
                        <input type="hidden" name="payment_ref" value="<?php echo htmlspecialchars($payment_ref); ?>">
                        <div class="mb-3">
                            <label class="form-label small fw-medium"><?php echo $this->lang->line('payment_mode') ?: 'Payment Mode'; ?> *</label>
                            <select class="form-select form-select-sm" name="payment_mode" id="payment_mode" required>
                                <option value=""><?php echo $this->lang->line('select') ?: 'Select'; ?></option>
                                <?php if (!empty($payment_mode) && is_array($payment_mode)) {
                                    foreach ($payment_mode as $key => $value) { ?>
                                        <option value="<?php echo $key; ?>"><?php echo $value; ?></option>
                                    <?php }
                                } ?>
                            </select>
                        </div>

                        <div class="mb-3 cheque_div d-none">
                            <label class="form-label small fw-medium"><?php echo $this->lang->line('cheque_no') ?: 'Cheque No'; ?> *</label>
                            <input type="text" class="form-control form-control-sm" name="cheque_no" id="cheque_no">
                        </div>

                        <div class="mb-3 cheque_div d-none">
                            <label class="form-label small fw-medium"><?php echo $this->lang->line('cheque_date') ?: 'Cheque Date'; ?> *</label>
                            <input type="text" class="form-control form-control-sm datepicker" name="cheque_date" id="cheque_date">
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-medium"><?php echo $this->lang->line('amount') ?: 'Amount'; ?> *</label>
                            <input type="text" class="form-control form-control-sm text-end" name="amount" id="amount" value="<?php echo $total_amount; ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-medium"><?php echo $this->lang->line('note') ?: 'Note'; ?></label>
                            <textarea class="form-control form-control-sm" name="note" rows="2" placeholder="<?php echo $this->lang->line('enter_note') ?: 'Enter a note'; ?>"></textarea>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="fa fa-check me-1"></i> <?php echo $this->lang->line('pay_now') ?: 'Pay Now'; ?>
                            </button>
                        </div>
                    </form>
                    <?php else: ?>
                    <div class="alert alert-warning mb-4">
                        <i class="fa fa-info-circle me-1"></i>
                        <?php echo $this->lang->line('no_amount_due') ?: 'No amount due for this visit.'; ?>
                    </div>
                    <?php endif; ?>

                </div>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo base_url('backend/dist/bootstrap5/js/bootstrap.bundle.min.js'); ?>"></script>
<script>
$(document).ready(function() {
    $('#payment_mode').on('change', function() {
        if ($(this).val() === 'Cheque') {
            $('.cheque_div').removeClass('d-none');
        } else {
            $('.cheque_div').addClass('d-none');
        }
    });

    $('#pf_payment_form').on('submit', function(e) {
        e.preventDefault();
        // Form submission will be wired in step 2
        alert('<?php echo $this->lang->line('payment_processing') ?: 'Payment processing...'; ?>');
    });
});
</script>
</body>
</html>