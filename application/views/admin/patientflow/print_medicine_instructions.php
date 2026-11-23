<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Medicine Instructions — <?php echo !empty($visit->visit_no) ? htmlspecialchars($visit->visit_no) : ''; ?></title>
    <link rel="stylesheet" href="<?php echo base_url('backend/dist/bootstrap5/css/bootstrap.min.css'); ?>">
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 12px;
            color: #212529;
            background: #fff;
            padding: 18px;
            line-height: 1.4;
        }
        .print-header {
            border-bottom: 2px solid #212529;
            padding-bottom: 12px;
            margin-bottom: 18px;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }
        .hospital-name {
            font-size: 20px;
            font-weight: 700;
            margin: 0;
        }
        .hospital-sub {
            font-size: 11px;
            color: #495057;
            margin: 2px 0 0 0;
        }
        .doc-title {
            text-align: center;
            font-size: 16px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 6px;
        }
        .patient-info {
            display: flex;
            gap: 20px;
            margin-bottom: 14px;
            font-size: 12px;
        }
        .patient-info .info-block {
            flex: 1;
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            padding: 8px 10px;
        }
        .patient-info .info-label {
            font-size: 10px;
            text-transform: uppercase;
            color: #6c757d;
            font-weight: 600;
            margin-bottom: 2px;
        }
        .patient-info .info-value {
            font-weight: 600;
            color: #212529;
        }
        table.medicine-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
        }
        table.medicine-table th {
            background: #e9ecef;
            color: #495057;
            font-size: 10px;
            text-transform: uppercase;
            font-weight: 700;
            border: 1px solid #dee2e6;
            padding: 6px 8px;
            text-align: left;
        }
        table.medicine-table td {
            border: 1px solid #dee2e6;
            padding: 6px 8px;
            font-size: 11px;
            vertical-align: top;
        }
        table.medicine-table tr:nth-child(even) td {
            background: #f8f9fa;
        }
        .med-name {
            font-weight: 700;
            color: #212529;
        }
        .med-code {
            font-family: monospace;
            font-size: 10px;
            color: #6c757d;
        }
        .instr-cell {
            font-size: 11px;
        }
        .hindi-text {
            font-size: 10px;
            color: #6c757d;
            font-style: italic;
        }
        .empty-state {
            text-align: center;
            padding: 30px;
            color: #6c757d;
            border: 2px dashed #dee2e6;
            border-radius: 8px;
        }
        .no-print {
            margin-top: 14px;
            text-align: center;
        }
        @media print {
            body { padding: 0; background: #fff; }
            .no-print { display: none !important; }
            .print-header { border-bottom: 2px solid #000; }
        }
    </style>
</head>
<body>

<div class="print-header">
    <div>
        <?php $hospital_name = !empty($setting['name']) ? htmlspecialchars($setting['name']) : 'Hospital'; ?>
        <p class="hospital-name m-0"><?php echo $hospital_name; ?></p>
        <p class="hospital-sub m-0">Medicine Instructions</p>
    </div>
    <div class="text-end">
        <p class="m-0"><strong>Visit No:</strong> <?php echo htmlspecialchars((string) ($visit->visit_no ?: '—')); ?></p>
        <p class="m-0"><strong>Patient:</strong> <?php echo htmlspecialchars((string) ($visit->patient_name ?: '—')); ?></p>
        <p class="m-0"><strong>UID:</strong> <?php echo htmlspecialchars((string) ($visit->patient_uuid ?: '—')); ?></p>
        <p class="m-0"><strong>Date:</strong> <?php echo date('d-M-Y'); ?></p>
    </div>
</div>

<!-- <div class="patient-info">
    <div class="info-block">
        <span class="info-label">Visit No</span>
        <div class="info-value"><?php echo htmlspecialchars((string) ($visit->visit_no ?: '—')); ?></div>
    </div>
    <div class="info-block">
        <span class="info-label">Patient Name</span>
        <div class="info-value"><?php echo htmlspecialchars((string) ($visit->patient_name ?: '—')); ?></div>
    </div>
    <div class="info-block">
        <span class="info-label">UID</span>
        <div class="info-value"><?php echo htmlspecialchars((string) ($visit->patient_uuid ?: '—')); ?></div>
    </div>
</div> -->

<h2 class="doc-title">Medicine Instructions</h2>

<?php if (!empty($medicines) && is_array($medicines)): ?>
<table class="medicine-table">
    <thead>
    <tr>
        <th style="width: 12%;">Code</th>
        <!-- <th style="width: 28%;">Medicine</th> -->
        <th style="width: 14%;">Dose</th>
        <th style="width: 14%;">Route</th>
        <th style="width: 12%;">Timing</th>
        <th style="width: 10%;">Frequency</th>
        <th style="width: 10%;">Duration</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($medicines as $med): ?>
    <tr>
        <td class="med-code"><?php echo htmlspecialchars((string) ($med['code'] ?: '—')); ?></td>
        <!-- <td>
            <span class="med-name"><?php echo htmlspecialchars((string) ($med['medicine_identity'] ?: $med['code'] ?: '—')); ?></span>
        </td> -->
        <td class="instr-cell"><?php echo htmlspecialchars((string) ($med['patient_dose'] ?: '—')); ?></td>
        <td class="instr-cell"><?php echo htmlspecialchars((string) ($med['route_method'] ?: '—')); ?></td>
        <td class="instr-cell"><?php echo htmlspecialchars((string) ($med['timing'] ?: '—')); ?></td>
        <td class="instr-cell"><?php echo htmlspecialchars((string) ($med['frequency'] ?: '—')); ?></td>
        <td class="instr-cell"><?php echo htmlspecialchars((string) ($med['duration_text'] ?: '—')); ?></td>
    </tr>
    <?php if (!empty($med['hindi_patient_dose']) || !empty($med['hindi_route_method']) || !empty($med['hindi_timing']) || !empty($med['hindi_frequency']) || !empty($med['hindi_duration_text'])): ?>
    <tr>
        <td colspan="7">
            <span class="hindi-text">हिन्दी निर्देश: </span>
            <?php if (!empty($med['hindi_patient_dose'])): ?>डोज़: <?php echo htmlspecialchars($med['hindi_patient_dose']); ?> | <?php endif; ?>
            <?php if (!empty($med['hindi_route_method'])): ?>मार्ग: <?php echo htmlspecialchars($med['hindi_route_method']); ?> | <?php endif; ?>
            <?php if (!empty($med['hindi_timing'])): ?>समय: <?php echo htmlspecialchars($med['hindi_timing']); ?> | <?php endif; ?>
            <?php if (!empty($med['hindi_frequency'])): ?>आवृत्ति: <?php echo htmlspecialchars($med['hindi_frequency']); ?> | <?php endif; ?>
            <?php if (!empty($med['hindi_duration_text'])): ?>अवधि: <?php echo htmlspecialchars($med['hindi_duration_text']); ?> | <?php endif; ?>
        </td>
    </tr>
    <?php endif; ?>
    <?php endforeach; ?>
    </tbody>
</table>
<?php else: ?>
<div class="empty-state">
    <p><i class="fa fa-info-circle me-1"></i> No medicines prescribed for this visit.</p>
</div>
<?php endif; ?>

<div class="no-print text-center mt-4">
    <button class="btn btn-primary" onclick="window.print();"><i class="fa fa-print me-1"></i> Print</button>
    <button class="btn btn-outline-secondary ms-2" onclick="window.close();"><i class="fa fa-times me-1"></i> Close</button>
</div>

<script>
    window.onload = function () {
        window.print();
        setTimeout(function () {
            window.close();
        }, 1500);
    };
</script>

</body>
</html>
