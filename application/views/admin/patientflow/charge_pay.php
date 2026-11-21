<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $this->lang->line('patient_flow_charge') ?: 'Charge Collection'; ?> — <?php echo !empty($setting[0]['name']) ? htmlspecialchars($setting[0]['name']) : ''; ?></title>
    <link rel="stylesheet" href="<?php echo base_url('backend/dist/bootstrap5/css/bootstrap.min.css'); ?>">
    <link rel="stylesheet" href="<?php echo base_url('backend/dist/css/style-main.css'); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --pay-accent: #0d6efd;
            --pay-accent-soft: #e7f0ff;
            --pay-border: #e5e9f2;
            --pay-ink: #1f2937;
            --pay-muted: #6b7280;
        }
        body {
            background: #f4f6fb;
            min-height: 100vh;
            font-family: -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            color: var(--pay-ink);
        }
        .payment-card {
            max-width: 560px;
            margin: 0 auto;
            border: 1px solid var(--pay-border);
            border-radius: 16px;
            background: #fff;
            box-shadow: 0 10px 30px rgba(16, 24, 40, .08);
            overflow: hidden;
        }
        .payment-card .card-header {
            background: #fbfcfe !important;
            border-bottom: 1px solid var(--pay-border) !important;
            padding: 16px 20px;
        }
        .payment-card .card-header h5 {
            font-size: 1rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .icon-badge {
            width: 30px;
            height: 30px;
            border-radius: 8px;
            background: var(--pay-accent-soft);
            color: var(--pay-accent);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
        }
        .card-body { padding: 22px 22px 26px; }

        /* Patient block */
        .patient-block {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 14px;
            border: 1px solid var(--pay-border);
            border-radius: 12px;
            background: #f8fafc;
            margin-bottom: 20px;
        }
        .patient-avatar {
            width: 56px;
            height: 56px;
            object-fit: cover;
            border-radius: 50%;
            border: 3px solid #fff;
            box-shadow: 0 2px 8px rgba(16, 24, 40, .10);
        }
        .patient-block h6 {
            margin: 0 0 4px;
            font-size: .98rem;
            font-weight: 600;
        }
        .patient-meta {
            font-size: .78rem;
            color: var(--pay-muted);
            line-height: 1.5;
        }
        .patient-meta strong { color: var(--pay-ink); font-weight: 600; }

        /* Visit summary list */
        .summary-list {
            border: 1px solid var(--pay-border);
            border-radius: 12px;
            overflow: hidden;
            margin-bottom: 20px;
        }
        .summary-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 9px 14px;
            font-size: .82rem;
            border-bottom: 1px solid var(--pay-border);
        }
        .summary-row:last-child { border-bottom: none; }
        .summary-row:nth-child(odd) { background: #f8fafc; }
        .summary-row .k { color: var(--pay-muted); font-weight: 500; }
        .summary-row .v {
            color: var(--pay-ink);
            font-weight: 600;
            max-width: 60%;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            text-align: right;
        }

        /* Amount block */
        .amount-box {
            border-radius: 14px;
            padding: 16px 18px;
            background: linear-gradient(135deg, #eaf2ff 0%, #f5f9ff 100%);
            border: 1px solid #d4e4ff;
            margin-bottom: 20px;
        }
        .amount-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: .85rem;
        }
        .amount-row + .amount-row { margin-top: 8px; }
        .amount-row .label { color: var(--pay-muted); }
        .amount-row .value { font-weight: 600; }
        .amount-row.total {
            border-top: 1px dashed #c3d9f7;
            margin-top: 10px;
            padding-top: 10px;
            font-size: .95rem;
        }
        .amount-row.total .label { color: var(--pay-ink); font-weight: 600; }
        .amount-row.total .value {
            color: var(--pay-accent);
            font-size: 1.35rem;
            font-weight: 700;
        }
        .amount-row .value.paid { color: #198754; }

        /* Form */
        .form-label {
            font-size: .8rem;
            font-weight: 600;
            color: #374151;
            margin-bottom: 4px;
        }
        .form-control-sm, .form-select-sm {
            border-radius: 8px;
            border-color: var(--pay-border);
            padding: 8px 12px;
            font-size: .875rem;
        }
        .form-control-sm:focus, .form-select-sm:focus {
            border-color: var(--pay-accent);
            box-shadow: 0 0 0 3px rgba(13, 110, 253, .12);
        }
        .btn-pay {
            border-radius: 10px;
            padding: 11px 18px;
            font-weight: 600;
            font-size: .95rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            background: linear-gradient(135deg, #198754, #20c997);
            border: none;
            color: #fff;
            box-shadow: 0 6px 18px rgba(25, 135, 84, .25);
            transition: transform .12s ease, box-shadow .12s ease;
        }
        .btn-pay:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 22px rgba(25, 135, 84, .32);
            color: #fff;
        }
        .btn-pay:active { transform: translateY(0); }

        .no-due-alert {
            background: #fff8e6;
            border: 1px solid #ffe4a3;
            color: #8a6d00;
            border-radius: 10px;
            padding: 12px 14px;
            font-size: .85rem;
        }

        @media (max-width: 480px) {
            .summary-row .v { max-width: 55%; }
            .amount-row.total .value { font-size: 1.2rem; }
        }
    </style>
</head>
<body>
<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
?>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card payment-card">
                <div class="card-header">
                    <div class="d-flex align-items-center justify-content-between">
                        <h5 class="mb-0">
                            <span class="icon-badge"><i class="fas fa-money-bill-wave"></i></span>
                            <?php echo $this->lang->line('patient_flow_charge') ?: 'Charge Collection'; ?>
                        </h5>
                        <a href="<?php echo base_url('admin/patientflow'); ?>" class="btn btn-sm btn-outline-secondary" style="border-radius:8px;">
                            <i class="fa fa-arrow-left me-1"></i> <?php echo $this->lang->line('back') ?: 'Back'; ?>
                        </a>
                    </div>
                </div>
                <div class="card-body">

                    <!-- Patient Information -->
                    <div class="patient-block">
                        <?php
                        $photo = !empty($patient['image']) ? $patient['image'] : 'uploads/patient_images/no_image.png';
                        ?>
                        <img src="<?php echo $this->media_storage->getImageURL($photo); ?>" alt="Patient" class="patient-avatar">
                        <div class="flex-grow-1">
                            <h6><?php echo htmlspecialchars($patient_name); ?></h6>
                            <div class="patient-meta">
                                <?php echo $this->lang->line('visit_no') ?: 'Visit No'; ?>: <strong><?php echo htmlspecialchars($visit_no); ?></strong><br>
                                <?php if (!empty($visit->level_code)) { ?>
                                <?php echo $this->lang->line('current_level') ?: 'Current Level'; ?>: <strong><?php echo htmlspecialchars($visit->level_code . ' — ' . $visit->level_title); ?></strong><br>
                                <?php } ?>
                                <?php if (!empty($visit->mobileno)) { ?>
                                <?php echo $this->lang->line('mobile_no') ?: 'Mobile No'; ?>: <strong><?php echo htmlspecialchars($visit->mobileno); ?></strong>
                                <?php } ?>
                            </div>
                        </div>
                    </div>

                    <!-- Visit Summary -->
                    <div class="summary-list">
                        <div class="summary-row">
                            <span class="k"><?php echo $this->lang->line('visit_no') ?: 'Visit No'; ?></span>
                            <span class="v"><?php echo htmlspecialchars($visit->visit_no); ?></span>
                        </div>
                        <?php if (!empty($visit->token_no)) { ?>
                        <div class="summary-row">
                            <span class="k"><?php echo $this->lang->line('token_no') ?: 'Token No'; ?></span>
                            <span class="v"><?php echo htmlspecialchars($visit->token_no); ?></span>
                        </div>
                        <?php } ?>
                        <?php if (!empty($visit->pbhp_id)) { ?>
                        <div class="summary-row">
                            <span class="k"><?php echo $this->lang->line('pbhp') ?: 'PBHP ID'; ?></span>
                            <span class="v"><?php echo htmlspecialchars($visit->pbhp_id); ?></span>
                        </div>
                        <?php } ?>
                        <?php if (!empty($visit->mobileno)) { ?>
                        <div class="summary-row">
                            <span class="k"><?php echo $this->lang->line('mobile_no') ?: 'Mobile No'; ?></span>
                            <span class="v"><?php echo htmlspecialchars($visit->mobileno); ?></span>
                        </div>
                        <?php } ?>
                        <?php if (!empty($visit->level_code)) { ?>
                        <div class="summary-row">
                            <span class="k"><?php echo $this->lang->line('current_level') ?: 'Current Level'; ?></span>
                            <span class="v"><?php echo htmlspecialchars($visit->level_code . ' — ' . $visit->level_title); ?></span>
                        </div>
                        <?php } ?>
                    </div>

                    <!-- Charge Amount Summary -->
                    <div class="amount-box">
                        <div class="amount-row">
                            <span class="label"><?php echo $this->lang->line('charge_amount') ?: 'Charge Amount'; ?></span>
                            <span class="value"><?php echo $currency_symbol . ' ' . number_format($charge_amount, 2, '.', ','); ?></span>
                        </div>
                        <div class="amount-row">
                            <span class="label"><?php echo $this->lang->line('paid_amount') ?: 'Paid Amount'; ?></span>
                            <span class="value paid"><?php echo $currency_symbol . ' 0.00'; ?></span>
                        </div>
                        <div class="amount-row total">
                            <span class="label"><?php echo $this->lang->line('balance') ?: 'Balance Due'; ?></span>
                            <span class="value"><?php echo $currency_symbol . ' ' . number_format($charge_amount, 2, '.', ','); ?></span>
                        </div>
                    </div>

                    <!-- Payment Form -->
                    <form id="pf_charge_form" action="<?php echo site_url('admin/patientflow/collect_charge/') . (int) $visit->id; ?>" method="post">
                        <input type="hidden" name="visit_id" value="<?php echo (int) $visit->id; ?>">
                        <div class="mb-3">
                            <label class="form-label"><?php echo $this->lang->line('payment_mode') ?: 'Payment Mode'; ?> *</label>
                            <select class="form-select form-select-sm" name="payment_mode" id="payment_mode" required>
                                <option value=""><?php echo $this->lang->line('select') ?: 'Select'; ?></option>
                                <option value="Cash"><?php echo $this->lang->line('cash') ?: 'Cash'; ?></option>
                                <option value="Online"><?php echo $this->lang->line('online') ?: 'Online'; ?></option>
                                <option value="Razorpay"><?php echo $this->lang->line('razorpay') ?: 'Razorpay'; ?></option>
                            </select>
                        </div>

                        <div class="mb-3 cheque_div d-none">
                            <label class="form-label"><?php echo $this->lang->line('cheque_no') ?: 'Cheque No'; ?> *</label>
                            <input type="text" class="form-control form-control-sm" name="cheque_no" id="cheque_no">
                        </div>

                        <div class="mb-3 cheque_div d-none">
                            <label class="form-label"><?php echo $this->lang->line('cheque_date') ?: 'Cheque Date'; ?> *</label>
                            <input type="text" class="form-control form-control-sm datepicker" name="cheque_date" id="cheque_date">
                        </div>

                        <div class="mb-3">
                            <label class="form-label"><?php echo $this->lang->line('amount') ?: 'Amount'; ?> *</label>
                            <input type="text" class="form-control form-control-sm text-end fw-semibold" name="amount" id="amount" value="<?php echo $charge_amount; ?>" required readonly>
                        </div>

                        <div class="mb-3">
                            <label class="form-label"><?php echo $this->lang->line('note') ?: 'Note'; ?></label>
                            <textarea class="form-control form-control-sm" name="note" rows="2" placeholder="<?php echo $this->lang->line('enter_note') ?: 'Enter a note'; ?>"></textarea>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-pay">
                                <i class="fa fa-check"></i> <?php echo $this->lang->line('collect_charge') ?: 'Collect Charge'; ?>
                            </button>
                            <button type="button" class="btn btn-success" onclick="payCash(<?php echo (int) $visit->id; ?>)">
                                <i class="fa fa-money-bill-wave me-1"></i><?php echo $this->lang->line('cash_payment') ?: 'Cash Payment'; ?>
                            </button>
                        </div>
                    </form>

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

    // If Razorpay is selected, redirect to Razorpay checkout instead of submitting form.
    $('#pf_charge_form').on('submit', function(e) {
        var mode = $('#payment_mode').val();
        if (mode === 'Razorpay') {
            e.preventDefault();
            window.location.href = '<?php echo site_url('admin/patientflow/charge_gateway'); ?>';
        }
    });

    // Cash payment: generate charge reference and return to board.
    function payCash(visitId) {
        if (!confirm('Generate charge reference and mark as paid via Cash?')) {
            return;
        }
        $.post('<?php echo site_url('admin/patientflow/collect_charge/'); ?>' + visitId, {
            action: 'generate_reference',
            payment_mode: 'Cash'
        }, function (res) {
            if (res && res.status) {
                window.location.href = '<?php echo site_url('admin/patientflow'); ?>?cash_paid=1';
            } else {
                alert(res && res.message ? res.message : 'Failed to generate charge reference.');
            }
        }, 'json').fail(function () {
            alert('Server error while processing cash payment.');
        });
    }
});
</script>
</body>
</html>
