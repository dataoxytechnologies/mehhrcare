<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Programme Invoice</title>
    <link rel="stylesheet" href="<?php echo base_url('backend/dist/bootstrap5/css/bootstrap.min.css'); ?>">
    <style>
        body {
            background: #fff;
            color: #212529;
            font-family: Arial, Helvetica, sans-serif;
            padding: 24px;
        }
        .invoice-box {
            max-width: 820px;
            margin: 0 auto;
            border: 1px solid #dee2e6;
            border-radius: 10px;
            padding: 24px;
        }
        .topbar {
            text-align: right;
            margin-bottom: 16px;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 2px solid #212529;
            padding-bottom: 12px;
            margin-bottom: 18px;
        }
        .header h2 {
            margin: 0;
            font-size: 26px;
        }
        .subtext {
            color: #495057;
            font-size: 13px;
            margin-top: 6px;
        }
        .brand img {
            max-height: 60px;
            max-width: 180px;
            object-fit: contain;
        }
        .title {
            text-align: center;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
            margin-bottom: 10px;
        }
        .ref {
            text-align: center;
            background: #f8f9fa;
            border: 1px dashed #adb5bd;
            border-radius: 6px;
            font-weight: 700;
            font-family: monospace;
            font-size: 22px;
            padding: 10px;
            margin-bottom: 18px;
        }
        .row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 18px;
            margin-bottom: 18px;
        }
        .card {
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 12px 14px;
            background: #fff;
        }
        .label {
            display: inline-block;
            width: 42%;
            color: #495057;
            font-weight: 600;
            vertical-align: top;
        }
        .value {
            display: inline-block;
            width: 56%;
            vertical-align: top;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
        }
        th, td {
            border-top: 1px solid #dee2e6;
            padding: 8px 6px;
            text-align: left;
        }
        th {
            text-transform: uppercase;
            font-size: 12px;
            color: #495057;
        }
        .totals {
            margin-top: 16px;
            display: flex;
            justify-content: flex-end;
            font-size: 18px;
            font-weight: 700;
        }
        .sig {
            margin-top: 36px;
            display: flex;
            justify-content: space-between;
        }
        .sig div {
            width: 220px;
            border-top: 1px solid #212529;
            text-align: center;
            padding-top: 6px;
            color: #495057;
            font-size: 12px;
        }
        @media print {
            .no-print { display: none !important; }
            body { padding: 0; }
            .invoice-box {
                border: none;
                border-radius: 0;
                margin: 0;
                max-width: 100%;
                padding: 0;
            }
            @page { margin: 8mm; }
        }
    </style>
</head>
<body>
    <div class="no-print topbar">
        <button type="button" class="btn btn-sm btn-primary" onclick="window.print();">
            <i class="fa fa-print"></i> Print
        </button>
    </div>

    <div class="invoice-box">
        <div class="header">
            <div>
                <h2><?php echo isset($setting['name']) ? htmlspecialchars($setting['name']) : 'Hospital'; ?></h2>
                <div class="subtext">
                    <?php echo isset($setting['address']) ? htmlspecialchars($setting['address']) : ''; ?>
                    <?php if (!empty($setting['phone'])) { echo '<br>' . htmlspecialchars($setting['phone']); } ?>
                    <?php if (!empty($setting['email'])) { echo ' · ' . htmlspecialchars($setting['email']); } ?>
                </div>
            </div>
            <div class="brand">
                <?php if (!empty($setting['image'])) { ?>
                    <img src="<?php echo base_url('uploads/hospital_content/logo/' . $setting['image']); ?>" alt="logo">
                <?php } elseif (!empty($setting['mini_logo'])) { ?>
                    <img src="<?php echo base_url('uploads/hospital_content/logo/' . $setting['mini_logo']); ?>" alt="logo">
                <?php } ?>
            </div>
        </div>

        <div class="title">Programme Invoice</div>
        <div class="ref"><?php echo htmlspecialchars((string) $invoice->invoice_no); ?></div>

        <div class="row">
            <div class="card">
                <div><span class="label">Patient</span><span class="value"><strong><?php echo htmlspecialchars((string) ($visit->patient_name ?? 'N/A')); ?></strong></span></div>
                <div><span class="label">Phone</span><span class="value"><?php echo htmlspecialchars((string) ($visit->mobileno ?? 'N/A')); ?></span></div>
                <div><span class="label">Patient UID</span><span class="value"><?php echo htmlspecialchars((string) ($visit->patient_uuid ?? 'N/A')); ?></span></div>
            </div>
            <div class="card">
                <div><span class="label">Visit No</span><span class="value"><?php echo htmlspecialchars((string) ($visit->visit_no ?? 'N/A')); ?></span></div>
                <div><span class="label">Invoice Date</span><span class="value"><?php echo !empty($invoice->invoice_date) ? htmlspecialchars(date('d M Y', strtotime($invoice->invoice_date))) : 'N/A'; ?></span></div>
                <div><span class="label">Billing Status</span><span class="value"><?php echo htmlspecialchars((string) ($invoice->status ?? 'PAID')); ?></span></div>
            </div>
        </div>

        <div class="card" style="margin-bottom:18px;">
            <div class="row" style="margin-bottom:0; gap:10px;">
                <div class="col-md-6"><strong>Programme:</strong> <?php echo htmlspecialchars((string) $programme_name); ?></div>
                <div class="col-md-6"><strong>Programme Group:</strong> <?php echo htmlspecialchars((string) $programme_group_name); ?></div>
                <div class="col-md-6"><strong>Programme Source:</strong> <?php echo htmlspecialchars((string) (($benefit_meta['source'] ?? 'NONE') === 'ASSISTANCE' ? 'Fee Assistance' : (($benefit_meta['source'] ?? 'NONE') === 'CAMPAIGN' ? 'Discount Campaign' : 'Direct Billing'))); ?></div>
                <!-- <div class="col-md-6"><strong>Source Code:</strong> <?php echo htmlspecialchars((string) ($benefit_meta['source_code'] ?? '—')); ?></div> -->
                <div class="col-md-6"><strong>Schedule:</strong> Q<?php echo htmlspecialchars((string) ($schedule->quarter_no ?? 1)); ?> · Seq <?php echo htmlspecialchars((string) ($schedule->seq_no ?? 1)); ?></div>
                <div class="col-md-6"><strong>Payment Mode:</strong> <?php echo htmlspecialchars((string) (($benefit_meta['payment_mode'] ?? '') ?: ($schedule->unit_type ?? 'MILESTONE'))); ?></div>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Breakdown</th>
                    <th>Value</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Standard Amount</td>
                    <td><?php echo $currency_symbol . ' ' . number_format((float) ($schedule->standard_amount ?? ($invoice->subtotal ?? 0)), 2); ?></td>
                </tr>
                <tr>
                    <td>Selected Discount</td>
                    <td><?php echo number_format((float) (($schedule->discount_percent ?? $benefit_meta['discount_percent'] ?? 0)), 2) . '%'; ?></td>
                </tr>
                <tr>
                    <td>Discount Amount</td>
                    <td><?php echo $currency_symbol . ' ' . number_format((float) ($schedule->discount_amount ?? (($schedule->standard_amount ?? ($invoice->subtotal ?? 0)) * ((float) (($schedule->discount_percent ?? $benefit_meta['discount_percent'] ?? 0)) / 100))), 2); ?></td>
                </tr>
                <tr>
                    <td>Fee Assistance / Campaign</td>
                    <td><?php echo htmlspecialchars((string) ($benefit_meta['source_name'] ?? '—')); ?> (<?php echo htmlspecialchars((string) ($benefit_meta['source_code'] ?? '—')); ?>)</td>
                </tr>
                <tr>
                    <td>Payable Amount</td>
                    <td><?php echo $currency_symbol . ' ' . number_format((float) ($schedule->payable_amount ?? ($invoice->grand_total ?? $invoice->subtotal ?? 0)), 2); ?></td>
                </tr>
                <tr>
                    <td>Invoice Total</td>
                    <td><?php echo $currency_symbol . ' ' . number_format((float) ($invoice->grand_total ?? $invoice->subtotal ?? 0), 2); ?></td>
                </tr>
            </tbody>
        </table>

        <table style="margin-top:18px;">
            <thead>
                <tr>
                    <th>Description</th>
                    <th>Qty</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($lines as $line): ?>
                    <tr>
                        <td><?php echo htmlspecialchars((string) ($line->description ?: 'Programme Billing')); ?></td>
                        <td><?php echo htmlspecialchars((string) ($line->qty ?: '1')); ?></td>
                        <td><?php echo $currency_symbol . ' ' . number_format((float) ($line->taxable_amount ?: $line->unit_amount), 2); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="totals">
            Total: <?php echo $currency_symbol . ' ' . number_format((float) ($invoice->grand_total ?: $invoice->subtotal), 2); ?>
        </div>

        <div class="sig">
            <div>Patient Signature</div>
            <div>Authorized Signature</div>
        </div>
    </div>

    <script>
        window.onload = function () {
            window.print();

            setTimeout(function () {
                window.close();
            }, 1000);
        };
    </script>
</body>
</html>
