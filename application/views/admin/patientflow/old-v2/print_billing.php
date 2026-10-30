<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo !empty($visit->billing_reference) ? htmlspecialchars($visit->billing_reference) : 'Billing Slip'; ?> — <?php echo isset($setting['name']) ? htmlspecialchars($setting['name']) : ''; ?></title>

    <link rel="stylesheet" href="<?php echo base_url('backend/dist/bootstrap5/css/bootstrap.min.css'); ?>">

    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            color: #212529;
            background: #fff;
        }

        .pf-slip {
            max-width: 800px;
            margin: 0 auto 10px auto;
            padding: 10px 24px;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            page-break-inside: avoid;
            break-inside: avoid;
        }

        .pf-copy-badge {
            text-align: right;
            margin-bottom: 4px;
        }

        .pf-copy-badge span {
            display: inline-block;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #6c757d;
            border: 1px solid #adb5bd;
            border-radius: 10px;
            padding: 1px 10px;
        }

        .pf-cut-line {
            position: relative;
            max-width: 800px;
            margin: 8px auto;
            border-top: 1px dashed #212529;
            text-align: center;
        }

        .pf-cut-line span {
            position: relative;
            top: -11px;
            background: #fff;
            padding: 0 10px;
            color: #6c757d;
            font-size: 13px;
        }

        .pf-slip-hospital {
            border-bottom: 2px solid #212529;
            padding-bottom: 6px;
            margin-bottom: 10px;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 16px;
        }

        .pf-slip-hospital h2 {
            margin: 0 0 2px 0;
            font-size: 19px;
        }

        .pf-slip-hospital .small {
            color: #495057;
            line-height: 1.35;
        }

        .pf-slip-hospital img {
            max-height: 52px;
            max-width: 150px;
            width: auto;
            height: auto;
            object-fit: contain;
        }

        .pf-slip-title {
            text-align: center;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
            margin-bottom: 6px;
        }

        .pf-slip-ref {
            text-align: center;
            font-family: monospace;
            font-size: 22px;
            font-weight: 700;
            background: #f8f9fa;
            border: 1px dashed #adb5bd;
            border-radius: 6px;
            padding: 5px 10px;
            margin-bottom: 8px;
        }

        /*
         * Main wrapper for the left/right information.
         * Each side has its own independent table.
         */
        .pf-two-column {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .pf-two-column > tbody > tr > td {
            width: 50%;
            vertical-align: top;
            padding: 0 12px 0 0;
        }

        .pf-two-column > tbody > tr > td:last-child {
            padding: 0 0 0 12px;
        }

        /*
         * Individual left/right tables.
         */
        .pf-side-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .pf-side-table td {
            padding: 2px 4px;
            vertical-align: top;
        }

        /*
         * Fixed label and value columns.
         */
        .pf-side-table .pf-slip-label {
            width: 42%;
            color: #495057;
            font-weight: 600;
            white-space: nowrap;
        }

        .pf-side-table .pf-slip-value {
            width: 58%;
            word-break: break-word;
        }

        .pf-slip-section {
            font-weight: 700;
            border-bottom: 1px solid #dee2e6;
            margin: 8px 0 4px 0;
            padding-bottom: 2px;
            text-transform: uppercase;
            font-size: 11px;
            color: #6c757d;
        }

        .pf-charges-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            margin-bottom: 4px;
        }

        .pf-charges-table th,
        .pf-charges-table td {
            padding: 2px 4px;
            vertical-align: top;
            text-align: left;
        }

        .pf-charges-table .pf-charges-num {
            width: 20%;
            text-align: right;
            white-space: nowrap;
        }

        .pf-charges-table thead th,
        .pf-charges-table tfoot th {
            border-bottom: 1px solid #dee2e6;
            font-size: 11px;
            color: #495057;
            text-transform: uppercase;
        }

        .pf-charges-table tfoot th {
            border-top: 1px solid #adb5bd;
            border-bottom: none;
        }

        .pf-slip-sign {
            margin-top: 58px;
            display: flex;
            justify-content: space-between;
        }

        .pf-slip-sign div {
            width: 220px;
            border-top: 1px solid #212529;
            text-align: center;
            padding-top: 3px;
            font-size: 12px;
            color: #495057;
        }

        .pf-slip-footer {
            margin-top: 8px;
            text-align: center;
            font-size: 10px;
            color: #6c757d;
        }

        @media print {
            .pf-no-print {
                display: none !important;
            }

            body {
                background: #fff;
            }

            .pf-slip {
                border: none;
                margin: 0 auto;
                max-width: 100%;
                padding: 6px 10px;
            }

            .pf-slip-ref {
                font-size: 20px;
            }

            .pf-slip-sign {
                margin-top: 24px;
            }

            @page {
                margin: 7mm;
            }
        }
    </style>
</head>

<body>

    <div class="pf-no-print text-end m-3">
        <button type="button" class="btn btn-sm btn-primary" onclick="window.print();">
            <i class="fa fa-print"></i> Print
        </button>
    </div>

    <?php
    // Two copies on one page:
    // Top slip = Customer Copy
    // Bottom slip = Counter Copy

    $pf_copies = array('Customer Copy', 'Counter Copy');

    foreach ($pf_copies as $pf_copy_idx => $pf_copy_label) {
    ?>

    <div class="pf-slip">

        <div class="pf-copy-badge">
            <span><?php echo htmlspecialchars($pf_copy_label); ?></span>
        </div>

        <!-- Hospital Header -->
        <div class="pf-slip-hospital">

            <div>
                <h2>
                    <?php echo isset($setting['name']) ? htmlspecialchars($setting['name']) : ''; ?>
                </h2>

                <div class="small">
                    <?php echo isset($setting['address']) ? htmlspecialchars($setting['address']) : ''; ?>

                    <?php
                    if (!empty($setting['phone'])) {
                        echo '<br>' . htmlspecialchars($setting['phone']);
                    }

                    if (!empty($setting['email'])) {
                        echo ' · ' . htmlspecialchars($setting['email']);
                    }
                    ?>
                </div>
            </div>

            <div class="text-end">
                <?php if (!empty($setting['image'])) { ?>

                    <img
                        src="<?php echo base_url('uploads/hospital_content/logo/' . $setting['image']); ?>"
                        alt="logo"
                    >

                <?php } elseif (!empty($setting['mini_logo'])) { ?>

                    <img
                        src="<?php echo base_url('uploads/hospital_content/logo/' . $setting['mini_logo']); ?>"
                        alt="logo"
                    >

                <?php } ?>
            </div>

        </div>


        <!-- Billing Reference / Billing Invoice -->
        <div class="pf-slip-title">
            <?php echo $hide_billing_ref
                ? ($this->lang->line('billing_invoice') ?: 'Billing Invoice')
                : ($this->lang->line('billing_reference') ?: 'Billing Reference'); ?>
        </div>

        <?php if (!empty($visit->invoice_no)) { ?>

            <!-- Invoice No takes precedence when it exists -->
            <div class="pf-slip-title">
                <?php echo $this->lang->line('invoice_no') ?: 'Invoice No'; ?>
            </div>
            <div class="pf-slip-ref">
                <?php echo htmlspecialchars($visit->invoice_no); ?>
            </div>

        <?php } elseif (!empty($visit->billing_reference)) { ?>

            <div class="pf-slip-ref">
                <?php echo htmlspecialchars($visit->billing_reference); ?>
            </div>

        <?php } elseif (empty($hide_billing_ref)) { ?>

            <div class="pf-slip-ref text-danger">
                <?php echo $this->lang->line('not_generated') ?: 'Not generated'; ?>
            </div>

        <?php } ?>


        <!-- ========================================================= -->
        <!-- PATIENT SECTION -->
        <!-- ========================================================= -->

        <div class="pf-slip-section">
            <?php echo $this->lang->line('patient') ?: 'Patient'; ?>
        </div>

        <table class="pf-two-column">
            <tr>

                <!-- LEFT SIDE -->
                <td>
                    <table class="pf-side-table">

                        <tr>
                            <td class="pf-slip-label">
                                <?php echo $this->lang->line('name') ?: 'Name'; ?>
                            </td>

                            <td class="pf-slip-value">
                                <strong>
                                    <?php echo htmlspecialchars((string) $visit->patient_name); ?>
                                </strong>
                            </td>
                        </tr>

                        <tr>
                            <td class="pf-slip-label">
                                <?php echo $this->lang->line('phone') ?: 'Phone'; ?>
                            </td>

                            <td class="pf-slip-value">
                                <?php echo htmlspecialchars((string) $visit->mobileno); ?>
                            </td>
                        </tr>

                    </table>
                </td>


                <!-- RIGHT SIDE -->
                <td>
                    <table class="pf-side-table">

                        <tr>
                            <td class="pf-slip-label">
                                <?php echo $this->lang->line('patient_uuid') ?: 'Patient UID'; ?>
                            </td>

                            <td class="pf-slip-value" style="font-family: monospace;">
                                <?php echo htmlspecialchars((string) $visit->patient_uuid); ?>
                            </td>
                        </tr>

                        <tr>
                            <td class="pf-slip-label">
                                <?php echo $this->lang->line('gender') . ' / ' . $this->lang->line('age'); ?>
                            </td>

                            <td class="pf-slip-value">
                                <?php echo htmlspecialchars((string) $visit->gender); ?>
                                /
                                <?php echo htmlspecialchars((string) $visit->age); ?>Y
                            </td>
                        </tr>

                    </table>
                </td>

            </tr>
        </table>


        <!-- ========================================================= -->
        <!-- VISIT SECTION -->
        <!-- ========================================================= -->

        <div class="pf-slip-section">
            <?php echo $this->lang->line('visit_no') ?: 'Visit'; ?>
        </div>

        <table class="pf-two-column">
            <tr>

                <!-- LEFT SIDE -->
                <td>
                    <table class="pf-side-table">

                        <tr>
                            <td class="pf-slip-label">
                                <?php echo $this->lang->line('visit_no'); ?>
                            </td>

                            <td class="pf-slip-value">
                                <strong>
                                    <?php echo htmlspecialchars((string) $visit->visit_no); ?>
                                </strong>
                            </td>
                        </tr>

                        <tr>
                            <td class="pf-slip-label">
                                <?php echo $this->lang->line('charge') ?: 'Charge'; ?>
                            </td>

                            <td class="pf-slip-value">
                                <?php
                                // Charge amount is fetched from the reference
                                // OPD (patient_charges), not from the visit table.
                                $pf_charge = (float) $opd_net_amount;
                                echo $currency_symbol . ' ' .
                                    number_format($pf_charge, 2);
                                ?>
                            </td>
                        </tr>

                    </table>
                </td>


                <!-- RIGHT SIDE -->
                <td>
                    <table class="pf-side-table">

                        <tr>
                            <td class="pf-slip-label">
                                <?php echo $this->lang->line('token'); ?>
                            </td>

                            <td class="pf-slip-value">
                                <?php echo htmlspecialchars((string) $visit->token_no); ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="pf-slip-label">
                                <?php echo $this->lang->line('payment_status'); ?>
                            </td>

                            <td class="pf-slip-value">
                                <?php echo htmlspecialchars((string) $visit->payment_status); ?>
                            </td>
                        </tr>

                    </table>
                </td>

            </tr>
        </table>


        <!-- ========================================================= -->
        <!-- OPD CHARGES (from the reference OPD's patient_charges) -->
        <!-- ========================================================= -->

        <?php if (!empty($opd_charges)) { ?>

        <div class="pf-slip-section">
            <?php echo $this->lang->line('opd_charges') ?: 'OPD Charges'; ?>
        </div>

        <table class="pf-charges-table">
            <thead>
                <tr>
                    <th><?php echo $this->lang->line('charge') ?: 'Charge'; ?></th>
                    <th class="pf-charges-num"><?php echo $this->lang->line('qty') ?: 'Qty'; ?></th>
                    <th class="pf-charges-num"><?php echo $this->lang->line('amount') ?: 'Amount'; ?></th>
                </tr>
            </thead>
            <tbody>
                <?php
                $pf_charge_total = 0.0;
                foreach ($opd_charges as $pf_charge_row) {
                    $pf_charge_total += (float) $pf_charge_row['amount'];
                ?>
                <tr>
                    <td><?php echo htmlspecialchars((string) $pf_charge_row['name']); ?></td>
                    <td class="pf-charges-num"><?php echo (int) $pf_charge_row['qty']; ?></td>
                    <td class="pf-charges-num"><?php echo $currency_symbol . ' ' . number_format((float) $pf_charge_row['amount'], 2); ?></td>
                </tr>
                <?php } ?>
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="2"><?php echo $this->lang->line('total') ?: 'Total'; ?></th>
                    <th class="pf-charges-num"><?php echo $currency_symbol . ' ' . number_format((float) $opd_net_amount, 2); ?></th>
                </tr>
            </tfoot>
        </table>

        <?php } ?>


        <!-- ========================================================= -->
        <!-- BILLING SECTION -->
        <!-- ========================================================= -->

        <div class="pf-slip-section">
            <?php echo $this->lang->line('billing_status') ?: 'Billing'; ?>
        </div>

        <table class="pf-two-column">
            <tr>

                <!-- LEFT SIDE -->
                <td>
                    <table class="pf-side-table">

                        <tr>
                            <td class="pf-slip-label">
                                <?php echo $this->lang->line('billing_status') ?: 'Billing status'; ?>
                            </td>

                            <td class="pf-slip-value">
                                <strong>
                                    <?php
                                    echo ucfirst(
                                        htmlspecialchars(
                                            (string) (
                                                !empty($visit->billing_status)
                                                    ? $visit->billing_status
                                                    : 'pending'
                                            )
                                        )
                                    );
                                    ?>
                                </strong>
                            </td>
                        </tr>

                    </table>
                </td>


                <!-- RIGHT SIDE -->
                <td>
                    <table class="pf-side-table">

                        <tr>
                            <td class="pf-slip-label">
                                <?php echo $this->lang->line('generated_at') ?: 'Generated at'; ?>
                            </td>

                            <td class="pf-slip-value">
                                <?php
                                echo !empty($visit->billing_reference_at)
                                    ? date(
                                        'd-M Y H:i',
                                        strtotime($visit->billing_reference_at)
                                    )
                                    : '—';
                                ?>
                            </td>
                        </tr>

                    </table>
                </td>

            </tr>
        </table>


        <!-- Signatures -->
        <div class="pf-slip-sign">
            <div>
                <?php echo $this->lang->line('patient') ?: 'Patient'; ?>
            </div>

            <div>
                <?php echo $this->lang->line('reception') ?: 'Counter'; ?>
            </div>
        </div>


        <!-- Footer -->
        <div class="pf-slip-footer">

            <?php echo isset($setting['name']) ? htmlspecialchars($setting['name']) : ''; ?>

            ·

            <?php echo date('d-M Y H:i'); ?>

        </div>

    </div>


    <?php if ($pf_copy_idx === 0) { ?>

        <div class="pf-cut-line">
            <span>✂</span>
        </div>

    <?php } ?>

    <?php } ?>


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