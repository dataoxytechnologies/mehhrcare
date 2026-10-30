<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $this->lang->line('qrcode') ?: 'QR Code'; ?> — <?php echo !empty($setting['name']) ? htmlspecialchars($setting['name']) : ''; ?></title>
    <link rel="stylesheet" href="<?php echo base_url('backend/dist/bootstrap5/css/bootstrap.min.css'); ?>">
</head>
<body>
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fa fa-qrcode me-2 text-primary"></i><?php echo $this->lang->line('qrcode') ?: 'QR Code'; ?></h5>
                    <a href="<?php echo base_url('admin/patientflow'); ?>" class="btn btn-sm btn-outline-secondary"><?php echo $this->lang->line('back') ?: 'Back'; ?></a>
                </div>
                <div class="card-body text-center pb-4">
                    <div class="mb-3">
                        <img src="<?php echo $this->media_storage->getImageURL($qr_image); ?>" alt="QR Code"
                             class="img-fluid" style="max-width: 260px; border: 4px solid #fff; box-shadow: 0 0 0 1px rgba(0,0,0,.10);">
                    </div>

                    <div class="row text-center g-3 mt-3">
                        <div class="col-6">
                            <div class="small text-muted d-block"><?php echo $this->lang->line('visit_no') ?: 'Visit No'; ?></div>
                            <div class="fw-bold"><?php echo htmlspecialchars(!empty($visit->visit_no) ? $visit->visit_no : '-'); ?></div>
                        </div>
                        <div class="col-6">
                            <div class="small text-muted d-block"><?php echo $this->lang->line('patient_name') ?: 'Patient'; ?></div>
                            <div class="fw-bold"><?php echo htmlspecialchars(!empty($visit->patient_name) ? $visit->patient_name : '-'); ?></div>
                        </div>
                    </div>

                    <?php if (!empty($visit->billing_reference)) { ?>
                    <div class="row text-center g-3 mt-2">
                        <div class="col-12">
                            <div class="small text-muted d-block"><?php echo $this->lang->line('billing_reference') ?: 'Billing Ref'; ?></div>
                            <div class="fw-bold text-truncate" style="max-width: 100%; margin: 0 auto;"><?php echo htmlspecialchars($visit->billing_reference); ?></div>
                        </div>
                    </div>
                    <?php } ?>

                    <div class="mt-4">
                        <a href="<?php echo base_url('admin/patientflow/pay?ref=' . urlencode($payment_ref ?? ($visit->billing_reference ?: $visit->visit_no))); ?>" class="btn btn-sm btn-primary" target="_blank" rel="noopener">
                            <i class="fa fa-external-link-alt me-1"></i><?php echo $this->lang->line('open_payment_page') ?: 'Open Payment Page'; ?>
                        </a>
                    </div>

                    <div class="mt-3">
                        <small class="text-muted"><?php echo $this->lang->line('scan_qr_to_pay') ?: 'Scan this QR code to open the payment page.'; ?></small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
