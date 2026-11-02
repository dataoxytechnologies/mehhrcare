<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $this->lang->line('qrcode') ?: 'QR Code'; ?> — <?php echo !empty($setting['name']) ? htmlspecialchars($setting['name']) : ''; ?></title>
    <link rel="stylesheet" href="<?php echo base_url('backend/dist/bootstrap5/css/bootstrap.min.css'); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        :root {
            --qr-accent: #0d6efd;
            --qr-accent-soft: #e7f0ff;
            --qr-border: #e5e9f2;
        }
        body {
            background: #f4f6fb;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px 12px;
            font-family: -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        }
        .qr-card {
            width: 100%;
            max-width: 460px;
            border: 1px solid var(--qr-border);
            border-radius: 16px;
            background: #fff;
            box-shadow: 0 10px 30px rgba(16, 24, 40, .08);
            overflow: hidden;
        }
        .qr-card-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 16px 20px;
            border-bottom: 1px solid var(--qr-border);
            background: #fbfcfe;
        }
        .qr-card-head h5 {
            margin: 0;
            font-size: 1rem;
            font-weight: 600;
            color: #1f2937;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .qr-card-head .icon-badge {
            width: 30px;
            height: 30px;
            border-radius: 8px;
            background: var(--qr-accent-soft);
            color: var(--qr-accent);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
        }
        .qr-body {
            padding: 24px 22px 26px;
            text-align: center;
        }
        .qr-frame {
            position: relative;
            display: inline-block;
            padding: 16px;
            border-radius: 20px;
            background:
                linear-gradient(#fff, #fff) padding-box,
                linear-gradient(135deg, #0d6efd, #6ea8fe) border-box;
            border: 2px solid transparent;
            box-shadow: 0 8px 22px rgba(13, 110, 253, .12);
        }
        .qr-frame img {
            width: 100%;
            max-width: 240px;
            height: auto;
            display: block;
            border-radius: 10px;
        }
        /* corner brackets around the QR for a "scan me" feel */
        .qr-frame::before,
        .qr-frame::after {
            content: "";
            position: absolute;
            width: 22px;
            height: 22px;
            border: 3px solid var(--qr-accent);
            border-radius: 6px;
            pointer-events: none;
        }
        .qr-frame::before { top: -6px; left: -6px; border-right: none; border-bottom: none; }
        .qr-frame::after  { bottom: -6px; right: -6px; border-left: none; border-top: none; }

        .scan-hint {
            margin-top: 14px;
            font-size: .82rem;
            color: #6b7280;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .scan-hint i { color: var(--qr-accent); }

        .info-grid {
            margin-top: 22px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            text-align: left;
        }
        .info-cell {
            background: #f8fafc;
            border: 1px solid var(--qr-border);
            border-radius: 10px;
            padding: 10px 12px;
        }
        .info-cell .label {
            font-size: .72rem;
            text-transform: uppercase;
            letter-spacing: .04em;
            color: #8892a0;
            margin-bottom: 2px;
        }
        .info-cell .value {
            font-size: .92rem;
            font-weight: 600;
            color: #1f2937;
            word-break: break-word;
        }
        .info-cell.full { grid-column: 1 / -1; }

        .actions {
            margin-top: 22px;
            display: flex;
            gap: 10px;
            justify-content: center;
            flex-wrap: wrap;
        }
        .actions .btn {
            border-radius: 9px;
            padding: 9px 16px;
            font-size: .875rem;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .ref-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: var(--qr-accent-soft);
            color: #0b5ed7;
            border: 1px solid #cfe2ff;
            border-radius: 999px;
            padding: 4px 12px;
            font-size: .78rem;
            font-weight: 600;
            max-width: 100%;
        }
        .ref-pill span {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        @media (max-width: 400px) {
            .info-grid { grid-template-columns: 1fr; }
        }
        @media print {
            body { background: #fff; }
            .qr-card { box-shadow: none; border-color: #ddd; }
            .actions, .qr-card-head .btn { display: none !important; }
            .qr-frame { box-shadow: none; }
        }
    </style>
</head>
<body>
    <div class="qr-card">
        <div class="qr-card-head">
            <h5>
                <span class="icon-badge"><i class="fa-solid fa-qrcode"></i></span>
                <?php echo $this->lang->line('qrcode') ?: 'QR Code'; ?>
            </h5>
            <a href="<?php echo base_url('admin/patientflow'); ?>" class="btn btn-sm btn-outline-secondary">
                <i class="fa-solid fa-arrow-left me-1"></i><?php echo $this->lang->line('back') ?: 'Back'; ?>
            </a>
        </div>

        <div class="qr-body">
            <div class="qr-frame">
                <img src="<?php echo $this->media_storage->getImageURL($qr_image); ?>" alt="QR Code">
            </div>

            <div class="scan-hint">
                <i class="fa-solid fa-mobile-screen-button"></i>
                <?php echo $this->lang->line('scan_qr_to_pay') ?: 'Scan this QR code to open the payment page.'; ?>
            </div>

            <div class="info-grid">
                <div class="info-cell">
                    <div class="label"><?php echo $this->lang->line('visit_no') ?: 'Visit No'; ?></div>
                    <div class="value"><?php echo htmlspecialchars(!empty($visit->visit_no) ? $visit->visit_no : '-'); ?></div>
                </div>
                <div class="info-cell">
                    <div class="label"><?php echo $this->lang->line('patient_name') ?: 'Patient'; ?></div>
                    <div class="value"><?php echo htmlspecialchars(!empty($visit->patient_name) ? $visit->patient_name : '-'); ?></div>
                </div>

                <?php if (!empty($visit->billing_reference)) { ?>
                <div class="info-cell full" style="text-align:center;">
                    <div class="label mb-2"><?php echo $this->lang->line('billing_reference') ?: 'Billing Ref'; ?></div>
                    <span class="ref-pill">
                        <i class="fa-solid fa-hashtag"></i>
                        <span><?php echo htmlspecialchars($visit->billing_reference); ?></span>
                    </span>
                </div>
                <?php } ?>
            </div>

            <div class="actions">
                <a href="<?php echo base_url('admin/patientflow/pay?ref=' . urlencode($payment_ref ?? ($visit->billing_reference ?: $visit->visit_no))); ?>"
                   class="btn btn-primary" target="_blank" rel="noopener">
                    <i class="fa-solid fa-arrow-up-right-from-square"></i>
                    <?php echo $this->lang->line('open_payment_page') ?: 'Open Payment Page'; ?>
                </a>
                <button type="button" class="btn btn-outline-secondary" onclick="window.print()">
                    <i class="fa-solid fa-print"></i> Print
                </button>
            </div>
        </div>
    </div>
</body>
</html>