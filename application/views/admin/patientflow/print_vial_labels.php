<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Vial Label Printing</title>

    <style>
        * { box-sizing: border-box; }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 12px;
            color: #000;
            background: #fff;
            margin: 0;
            padding: 0;
            line-height: 1.0;
        }

        @page {
            size: 73mm 25mm;
            margin: 0mm;
        }

        .label {
            width: 73mm;
            height: 25mm;
            margin: 0 auto 1mm auto;
            padding: 0;
            page-break-inside: avoid;
            overflow: hidden;
        }

        .label-table {
            width: 100%;
            height: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            border: 1px solid #000;
        }

        .label-table td {
            padding: 0.8mm 0.8mm;
            vertical-align: middle;
            font-size: 12px;
            border: none;
        }

        .label-table .label-key {
            font-weight: bold;
            font-size: 12px;
            white-space: nowrap;
        }

        .label-table .label-value {
            font-weight: normal;
            font-size: 12px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .label-table .divider-row td {
            padding: 0;
            height: 1px;
            background: #000;
            line-height: 0;
            font-size: 0;
        }

        .text-center { text-align: center; }

        .print-only { display: none; }

        @media screen {
            body {
                background: #f2f2f2;
                padding: 10px;
            }
            .print-only {
                display: block;
                background: #fff;
                border: 1px dashed #aaa;
                padding: 8px 12px;
                margin: 0 auto 15px auto;
                max-width: 600px;
                text-align: center;
            }
            .label {
                background: #fff;
                box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            }
        }

        @media print {
            body { margin: 0; padding: 0; background: #fff; }
            .print-only { display: none !important; }
            .no-print { display: none !important; }
            .label {
                margin: 0 auto;
                page-break-after: avoid;
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body>

<!-- screen-only preview (hidden in print) -->
<div class="print-only text-center">
    <h3 style="margin: 0 0 6px 0; font-size: 16px;">Vial Label Printing</h3>
    <div style="color: #555; font-size: 12px;">
        Visit: <?php echo htmlspecialchars((string) $visit->visit_no); ?> |
        Patient: <?php echo htmlspecialchars((string) $visit->patient_name); ?> |
        UID: <?php echo htmlspecialchars((string) $visit->patient_uuid); ?>
    </div>
</div>

<?php
/**
 * Helper: determine the vial number based on series + timing.
 *
 * Rules:
 *  - Series FX             => V4 or V5 (V4 for morning / first, V5 for evening / second)
 *  - Series F, FA, FB, FC, FD:
 *        - timing containing "morning" (case-insensitive) => V2
 *        - timing containing "evening" (case-insensitive) => V3
 *        - otherwise (constitutional / symptom onset etc.) => V1
 *  - Anything else falls back to V1.
 */
if (!function_exists('resolveVialNumber')) {
    function resolveVialNumber($series, $timing) {
        $series  = strtoupper(trim((string) $series));
        $timingL = strtolower(trim((string) $timing));

        // FX series -> V4 / V5
        if ($series === 'FX') {
            if (strpos($timingL, 'evening') !== false) {
                return 'V5';
            }
            if (strpos($timingL, 'morning') !== false) {
                return 'V4';
            }
            // If timing is ambiguous for FX, default to V4
            return 'V4';
        }

        // F / FA / FB / FC / FD series -> V1 / V2 / V3
        $fSeries = ['F', 'FA', 'FB', 'FC', 'FD'];
        if (in_array($series, $fSeries, true)) {
            if (strpos($timingL, 'morning') !== false) {
                return 'V2';
            }
            if (strpos($timingL, 'evening') !== false) {
                return 'V3';
            }
            // constitutional / symptom onset / other -> V1
            return 'V1';
        }

        // fallback
        return 'V1';
    }
}

if (!empty($medicines)): ?>
    <?php foreach ($medicines as $med): ?>
    <?php
        // Resolve the vial label (V1, V2, V3, V4, V5)
        $vialNumber = resolveVialNumber(
            isset($med['series'])  ? $med['series']  : '',
            isset($med['timing'])  ? $med['timing']  : ''
        );
    ?>
    <div class="label">
        <table class="label-table" cellspacing="0" cellpadding="0">
            <!-- row 1: UID, Stage, Vial -->
            <tr>
                <td style="width: 55%; height:10px;">
                    <span class="label-key">UID:</span>
                    <span class="label-value"><?php echo htmlspecialchars((string) $visit->patient_uuid); ?></span>
                </td>
                <td style="width: 23%; text-align: center;">
                    <span class="label-key">Stage:</span>
                    <span class="label-value"><?php echo htmlspecialchars((string) $med['series']); ?></span>
                </td>
                <td style="width: 22%; text-align: right;">
                    <span class="label-key">Vial:</span>
                    <span class="label-value"><?php echo htmlspecialchars($vialNumber); ?></span>
                </td>
            </tr>

            <!-- divider row -->
            <tr class="divider-row">
                <td colspan="3" style="height: 1px; background: #000; padding: 0;"></td>
            </tr>

            <!-- row 2: Code, Date -->
            <tr>
                <td style="width: 50%; height:10px;">
                    <span class="label-key">Code:</span>
                    <span class="label-value"><?php echo htmlspecialchars((string) $med['code']); ?></span>
                </td>
                <td colspan="2" style="width: 50%; text-align: right;">
                    <span class="label-key">Date:</span>
                    <span class="label-value"><?php echo date('d-M-y', strtotime($med['prescribed_at'])); ?></span>
                </td>
            </tr>
        </table>
    </div>
    <?php endforeach; ?>
<?php endif; ?>

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