<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class PurchaseImport extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('pharmacy_model');
        $this->load->library('system_notification');
    }

    /**
     * Purchase CSV import form view.
     */
    public function index()
    {
        if (!$this->rbac->hasPrivilege('import_purchase', 'can_view')) {
            access_denied();
        }

        $data['title'] = 'Purchase CSV Import';
        $this->load->view('layout/header', $data);
        $this->load->view('admin/purchase_import/import', $data);
        $this->load->view('layout/footer');
    }

    /**
     * Download sample CSV file for purchase import.
     */
    public function export_sample()
    {
        if (!$this->rbac->hasPrivilege('import_purchase', 'can_view')) {
            access_denied();
        }

        $this->load->helper('download');
        $filepath = './backend/import/import_purchase_sample_file.csv';

        if (!is_file($filepath)) {
            show_error('Sample CSV file not found.', 404);
            return;
        }

        $data = file_get_contents($filepath);
        $name   = 'import_purchase_sample_file.csv';
        force_download($name, $data);
    }

    /**
     * Process uploaded purchase CSV file and import bills + medicine batches.
     */
    public function do_import()
    {
        if (!$this->rbac->hasPrivilege('import_purchase', 'can_view')) {
            access_denied();
        }

        $staff_id = (int) $this->customlib->getStaffID();

        if ($staff_id <= 0) {
            show_error('Please log in to the ERP before running this import.', 403);
            return;
        }

        // Validate file upload
        if (empty($_FILES['file']) || !isset($_FILES['file']['tmp_name']) || $_FILES['file']['tmp_name'] === '') {
            show_error('No CSV file uploaded.', 400);
            return;
        }

        $file_tmp = $_FILES['file']['tmp_name'];
        $file_name = $_FILES['file']['name'];

        $ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        if ($ext !== 'csv') {
            show_error('Invalid file type. Only CSV files are allowed.', 400);
            return;
        }

        $file_size = filesize($file_tmp);
        if ($file_size <= 0) {
            show_error('The uploaded CSV file is empty.', 400);
            return;
        }

        // Max 2MB upload
        $max_size = 2 * 1024 * 1024;
        if ($file_size > $max_size) {
            show_error('The uploaded CSV file is too large (max 2MB).', 400);
            return;
        }

        $csv_path = $file_tmp;

        $result = array(
            'status' => true,
            'ok'     => true,
            'bills'  => 0,
            'lines'  => 0,
            'skipped' => array()
        );

        if (!is_file($csv_path) || !is_readable($csv_path)) {
            show_error('CSV file not found or not readable: ' . $csv_path, 404);
            return;
        }

        // ---------- 1. Read CSV ----------
        $raw = array();
        $fh = fopen($csv_path, 'r');

        if ($fh === false) {
            show_error('Unable to open uploaded CSV file.', 500);
            return;
        }

        $header = null;
        $expected_columns = array(
            'invoice_no', 'date', 'supplier', 'payment_mode', 'cheque_no',
            'cheque_date', 'payment_note', 'note', 'medicine_category',
            'medicine_name', 'batch_no', 'expiry', 'mrp', 'batch_amount',
            'sale_rate', 'packing_qty', 'quantity', 'purchase_price', 'tax', 'amount'
        );

        while (($row = fgetcsv($fh, 0, ',', '"')) !== false) {
            if ($header === null) {
                $header = $this->pi_normalise_header($row);
                $missing = array_diff($expected_columns, $header);

                if (!empty($missing)) {
                    fclose($fh);
                    show_error('CSV is missing required columns: ' . implode(', ', $missing), 400);
                    return;
                }
                continue;
            }

            if (count($row) === 1 && trim($row[0]) === '') {
                continue;
            }

            $row = array_slice(array_pad($row, count($header), ''), 0, count($header));
            $combined = array_combine($header, $row);

            if ($combined !== false) {
                $raw[] = $combined;
            }
        }
        fclose($fh);

        if (empty($raw)) {
            show_error('The CSV contains no data rows.', 400);
            return;
        }

        // ---------- 2. Build reference maps ----------
        $suppliers = array();
        foreach ($this->db->select('id, supplier')->get('medicine_supplier')->result_array() as $r) {
            $suppliers[strtolower(trim($r['supplier']))] = (int) $r['id'];
        }

        $categories = array();
        foreach ($this->db->select('id, medicine_category')->get('medicine_category')->result_array() as $r) {
            $categories[strtolower(trim($r['medicine_category']))] = (int) $r['id'];
        }

        $medicines = array();
        foreach ($this->db->select('id, medicine_name, medicine_category_id')->get('pharmacy')->result_array() as $r) {
            $cat_id = (int) $r['medicine_category_id'];
            $medicines[$cat_id][strtolower(trim($r['medicine_name']))] = (int) $r['id'];
        }

        $valid_modes = array('cash', 'cheque', 'transfer_to_bank_account', 'upi', 'online', 'other');

        // ---------- 3. Group rows by invoice number ----------
        $bills = array();
        foreach ($raw as $i => $r) {
            $line_no = $i + 2; // account for CSV header
            $inv = trim($r['invoice_no']);

            if ($inv === '') {
                $result['skipped'][] = "CSV row {$line_no}: Purchase Invoice No is blank.";
                continue;
            }

            if (!isset($bills[$inv])) {
                $bills[$inv] = array(
                    'invoice_no'   => $inv,
                    'date'         => trim($r['date']),
                    'supplier'     => trim($r['supplier']),
                    'payment_mode' => trim($r['payment_mode']),
                    'cheque_no'    => trim($r['cheque_no']),
                    'cheque_date'  => trim($r['cheque_date']),
                    'payment_note' => trim($r['payment_note']),
                    'note'         => trim($r['note']),
                    'lines'        => array()
                );
            }

            $bills[$inv]['lines'][] = array(
                'row'            => $line_no,
                'category'       => trim($r['medicine_category']),
                'medicine'       => trim($r['medicine_name']),
                'batch_no'       => trim($r['batch_no']),
                'expiry'         => trim($r['expiry']),
                'mrp'            => trim($r['mrp']),
                'batch_amount'   => trim($r['batch_amount']),
                'sale_rate'      => trim($r['sale_rate']),
                'packing_qty'    => trim($r['packing_qty']),
                'quantity'       => trim($r['quantity']),
                'purchase_price' => trim($r['purchase_price']),
                'tax'            => trim($r['tax']),
                'amount'         => trim($r['amount'])
            );
        }

        // ---------- 4. Load existing invoice numbers and their IDs ----------
        $existing_invoices = array();
        foreach ($this->db->select('id, invoice_no')->get('supplier_bill_basic')->result_array() as $r) {
            $existing_invoices[strtolower(trim($r['invoice_no']))] = (int) $r['id'];
        }

        // ---------- 5. Validate bills before inserting ----------
        $prepared = array();

        foreach ($bills as $inv => $b) {
            $errs = array();
            $supplier_key = strtolower(trim($b['supplier']));

            if ($b['supplier'] === '') {
                $errs[] = "Invoice {$inv}: Supplier Name is blank.";
            } elseif (!isset($suppliers[$supplier_key])) {
                $errs[] = 'Invoice ' . $inv . ': Supplier "' . $b['supplier'] . '" was not found.';
            }

            $supplier_id = isset($suppliers[$supplier_key]) ? $suppliers[$supplier_key] : 0;
            $date_mysql = $this->pi_parse_date($b['date']);

            if ($date_mysql === false) {
                $errs[] = 'Invoice ' . $inv . ': invalid Purchase Date "' . $b['date'] . '".';
            }

            $mode = strtolower(trim($b['payment_mode']));

            if ($mode === '' || !in_array($mode, $valid_modes, true)) {
                $errs[] = 'Invoice ' . $inv . ': invalid Payment Mode "' . $b['payment_mode'] . '".';
            }

            if ($mode === 'cheque') {
                if ($b['cheque_no'] === '' || $this->pi_parse_date($b['cheque_date']) === false) {
                    $errs[] = 'Invoice ' . $inv . ': Cheque No and valid Cheque Date are required.';
                }
            }

            // Check if invoice exists
            $is_existing_invoice = isset($existing_invoices[strtolower($inv)]);
            $existing_bill_id = $is_existing_invoice ? $existing_invoices[strtolower($inv)] : 0;

            // If existing, fetch already imported batch combinations to prevent duplicates
            $existing_batches = array();
            if ($is_existing_invoice) {
                $this->db->select('pharmacy_id, batch_no, expiry');
                $this->db->where('supplier_bill_basic_id', $existing_bill_id);
                $query = $this->db->get('medicine_batch_details');

                if ($query) {
                    foreach ($query->result_array() as $eb) {
                        $key = $eb['pharmacy_id'] . '|' . strtolower(trim($eb['batch_no'])) . '|' . $eb['expiry'];
                        $existing_batches[$key] = true;
                    }
                }
            }

            $line_rows = array();

            foreach ($b['lines'] as $ln) {
                $tag = 'Invoice ' . $inv . ', CSV row ' . $ln['row'];
                $category_key = strtolower(trim($ln['category']));
                $medicine_key = strtolower(trim($ln['medicine']));

                if (!isset($categories[$category_key])) {
                    $errs[] = $tag . ': category "' . $ln['category'] . '" was not found.';
                    continue;
                }

                $cat_id = $categories[$category_key];

                if (!isset($medicines[$cat_id][$medicine_key])) {
                    $errs[] = $tag . ': medicine "' . $ln['medicine'] . '" was not found in that category.';
                    continue;
                }

                $med_id = $medicines[$cat_id][$medicine_key];

                /**
                 * Supported expiry formats:
                 * MM/YYYY, MM-YYYY, DD-MM-YYYY, DD/MM/YYYY,
                 * YYYY-MM-DD, Jul-28, Jun-29, etc.
                 * Store expiry as the last day of the month.
                 */
                $expiry_raw = trim((string) $ln['expiry']);
                $expiry_raw = preg_replace('/\s+/', '', $expiry_raw);

                $month = 0;
                $year  = 0;

                // Format: Jul-28, June-2028, Sep-29
                if (preg_match('/^([A-Za-z]{3,9})[-\/](\d{2}|\d{4})$/', $expiry_raw, $m)) {
                    $month_names = array(
                        'jan' => 1, 'feb' => 2, 'mar' => 3, 'apr' => 4, 'may' => 5, 'jun' => 6,
                        'jul' => 7, 'aug' => 8, 'sep' => 9, 'oct' => 10, 'nov' => 11, 'dec' => 12
                    );
                    $month_key = strtolower(substr($m[1], 0, 3));
                    if (isset($month_names[$month_key])) {
                        $month = $month_names[$month_key];
                        $year  = (int) $m[2];
                        if (strlen($m[2]) === 2) {
                            $year += ($year >= 70) ? 1900 : 2000;
                        }
                    }
                }
                // Format: MM/YYYY or MM-YYYY
                elseif (preg_match('/^(\d{1,2})[-\/](\d{4})$/', $expiry_raw, $m)) {
                    $month = (int) $m[1];
                    $year  = (int) $m[2];
                }
                // Format: DD-MM-YYYY or DD/MM/YYYY
                elseif (preg_match('/^(\d{1,2})[-\/](\d{1,2})[-\/](\d{4})$/', $expiry_raw, $m)) {
                    $day   = (int) $m[1];
                    $month = (int) $m[2];
                    $year  = (int) $m[3];
                    if (!checkdate($month, $day, $year)) {
                        $month = 0;
                        $year  = 0;
                    }
                }
                // Format: YYYY-MM-DD
                elseif (preg_match('/^(\d{4})-(\d{1,2})-(\d{1,2})$/', $expiry_raw, $m)) {
                    $year  = (int) $m[1];
                    $month = (int) $m[2];
                    $day   = (int) $m[3];
                    if (!checkdate($month, $day, $year)) {
                        $month = 0;
                        $year  = 0;
                    }
                }

                if ($month < 1 || $month > 12 || $year < 1900 || $year > 2200) {
                    $errs[] = $tag . ': Expiry "' . $ln['expiry'] . '" is invalid. Expected MM/YYYY or a valid month-year.';
                    continue;
                }

                $expiry = date('Y-m-t', mktime(0, 0, 0, $month, 1, $year));

                if ($ln['batch_no'] === '') {
                    $errs[] = $tag . ': Batch No is blank.';
                    continue;
                }

                // Check for existing duplicate batch in the SAME invoice
                if ($is_existing_invoice) {
                    $batch_key = $med_id . '|' . strtolower(trim($ln['batch_no'])) . '|' . $expiry;
                    if (isset($existing_batches[$batch_key])) {
                        $errs[] = $tag . ': This medicine, batch, and expiry already exist in this invoice. Skipped to prevent duplicate stock.';
                        continue;
                    }
                }

                if (!is_numeric($ln['quantity']) || (float) $ln['quantity'] <= 0) {
                    $errs[] = $tag . ': Quantity must be positive.';
                    continue;
                }

                if (!is_numeric($ln['purchase_price']) || !is_numeric($ln['mrp']) || !is_numeric($ln['sale_rate'])) {
                    $errs[] = $tag . ': Purchase Price, MRP and Sale Rate must be numeric.';
                    continue;
                }

                $tax_pct = is_numeric($ln['tax']) ? (float) $ln['tax'] : 0;

                if (is_numeric($ln['amount']) && (float) $ln['amount'] != 0) {
                    $amount = (float) $ln['amount'];
                } else {
                    $base_amount = (float) $ln['purchase_price'] * (float) $ln['quantity'];
                    $amount = round($base_amount, 2) + round($base_amount * $tax_pct / 100, 2);
                }

                $line_rows[] = array(
                    'pharmacy_id'          => $med_id,
                    'batch_no'             => $ln['batch_no'],
                    'expiry'               => $expiry,
                    'mrp'                  => $ln['mrp'],
                    'batch_amount'         => $ln['batch_amount'] === '' ? $ln['mrp'] : $ln['batch_amount'],
                    'sale_rate'            => $ln['sale_rate'],
                    'packing_qty'          => $ln['packing_qty'] === '' ? '1' : $ln['packing_qty'],
                    'quantity'             => $ln['quantity'],
                    'purchase_price'       => $ln['purchase_price'],
                    'available_quantity'   => $ln['quantity'],
                    'tax'                  => $tax_pct,
                    'amount'               => $amount,
                    'purchase_rate_packing'=> $ln['purchase_price']
                );
            }

            if (empty($line_rows)) {
                $errs[] = 'Invoice ' . $inv . ': no valid new medicine lines were found.';
            }

            if (!empty($errs)) {
                $result['skipped'] = array_merge($result['skipped'], $errs);
                continue; // Skip this invoice entirely if there are validation errors
            }

            $total = 0;
            foreach ($line_rows as $lr) {
                $total += (float) $lr['amount'];
            }

            $prepared[$inv] = array(
                'is_existing' => $is_existing_invoice,
                'existing_id' => $existing_bill_id,
                'header' => array(
                    'invoice_no'   => $inv,
                    'date'         => $date_mysql,
                    'supplier_id'  => $supplier_id,
                    'total'        => round($total, 2),
                    'discount'     => 0,
                    'tax'          => 0,
                    'net_amount'   => round($total, 2),
                    'note'         => $b['note'],
                    'payment_mode' => $b['payment_mode'],
                    'payment_date' => date('Y-m-d H:i:s'),
                    'payment_note' => $b['payment_note'],
                    'received_by'  => $staff_id,
                    'file'         => ''
                ),
                'lines' => $line_rows,
                'cheque_no' => $b['cheque_no'],
                'cheque_date' => $b['cheque_date']
            );
        }

        // ---------- 6. Insert/Update each bill in its own transaction ----------
        foreach ($prepared as $inv => $bill) {
            $this->db->trans_start();

            try {
                if ($bill['is_existing']) {
                    // Append to existing invoice
                    $bill_id = $bill['existing_id'];

                    if (strtolower($bill['header']['payment_mode']) === 'cheque') {
                        $this->pharmacy_model->addBillSupplier(array(
                            'id' => $bill_id,
                            'cheque_no' => $bill['cheque_no'],
                            'cheque_date' => $this->pi_parse_date($bill['cheque_date'])
                        ));
                    }
                } else {
                    // Create new invoice
                    $bill_id = $this->pharmacy_model->addBillSupplier($bill['header']);

                    if (empty($bill_id)) {
                        throw new Exception("Invoice {$inv}: supplier bill header insert failed.");
                    }

                    if (strtolower($bill['header']['payment_mode']) === 'cheque') {
                        $this->pharmacy_model->addBillSupplier(array(
                            'id' => $bill_id,
                            'cheque_no' => $bill['cheque_no'],
                            'cheque_date' => $this->pi_parse_date($bill['cheque_date'])
                        ));
                    }
                }

                $batch = array();
                foreach ($bill['lines'] as $lr) {
                    $lr['inward_date'] = $bill['header']['date'];
                    $lr['supplier_bill_basic_id'] = $bill_id;
                    $batch[] = $lr;
                }

                $this->pharmacy_model->addBillMedicineBatchSupplier($batch);

                if ($this->db->trans_status() === false) {
                    throw new Exception("Invoice {$inv}: medicine batch insert failed.");
                }

                $this->db->trans_complete();

                $result['bills']++;
                $result['lines'] += count($batch);

                // Protect against duplicate invoice numbers on later requests.
                if (!$bill['is_existing']) {
                    $existing_invoices[strtolower($inv)] = $bill_id;
                }

            } catch (Exception $e) {
                $this->db->trans_rollback();
                $result['ok'] = false;
                $result['skipped'][] = $e->getMessage();
                log_message('error', 'Purchase CSV import: ' . $e->getMessage());
            }
        }

        // ---------- 7. Display result in browser ----------
        header('Content-Type: application/json');

        // If no bills were imported but there were skipped items, mark as failure
        if ($result['bills'] === 0 && !empty($result['skipped'])) {
            $result['ok'] = false;
        }

        echo json_encode(array(
            'status'  => $result['ok'] ? true : false,
            'bills'   => $result['bills'],
            'lines'   => $result['lines'],
            'skipped' => $result['skipped'],
            'message' => ($result['ok'] ? 'Purchase CSV imported successfully.' : 'Purchase CSV import completed with errors.')
        ));
    }

    /**
     * Normalise CSV header row: strip BOM, parentheses, asterisks,
     * normalize punctuation/spaces, and map known aliases.
     *
     * @param array $row Raw header row from fgetcsv
     * @return array Normalized header column names
     */
    private function pi_normalise_header($row)
    {
        $headers = [];

        foreach ($row as $value) {
            $value = preg_replace('/^\xEF\xBB\xBF/', '', (string) $value);
            $value = strtolower(trim($value));

            // Remove explanatory text in parentheses and asterisks.
            $value = preg_replace('/\([^)]*\)/', '', $value);
            $value = str_replace('*', '', $value);

            // Normalize punctuation and spaces.
            $value = preg_replace('/[^a-z0-9]+/', '_', $value);
            $value = trim($value, '_');

            // Map CSV headings to the column names expected by the importer.
            $aliases = [
                'purchase_invoice_no' => 'invoice_no',
                'purchase_date'       => 'date',
                'supplier_name'       => 'supplier',
                'cheque_date'         => 'cheque_date',
                'bill_note'           => 'note',
                'expiry'              => 'expiry',
                'purchase_tax'        => 'tax',
            ];

            if (isset($aliases[$value])) {
                $value = $aliases[$value];
            }

            $headers[] = $value;
        }

        return $headers;
    }

    /**
     * Parse various date formats into YYYY-MM-DD.
     * Returns null if the value cannot be parsed.
     *
     * @param string $value Raw date string
     * @return string|null
     */
    private function pi_parse_date($value)
    {
        $value = trim((string) $value);

        if ($value === '') {
            return null;
        }

        // YYYY-MM-DD
        if (preg_match('/^(\d{4})-(\d{1,2})-(\d{1,2})$/', $value, $m)) {
            return checkdate((int) $m[2], (int) $m[3], (int) $m[1])
                ? sprintf('%04d-%02d-%02d', $m[1], $m[2], $m[3])
                : null;
        }

        // DD-MM-YYYY, DD/MM/YYYY, DD-MM-YY, or DD/MM/YY
        if (preg_match('/^(\d{1,2})[\/-](\d{1,2})[\/-](\d{2}|\d{4})$/', $value, $m)) {
            $day   = (int) $m[1];
            $month = (int) $m[2];
            $year  = (int) $m[3];

            if ($year < 100) {
                $year += ($year <= 69) ? 2000 : 1900;
            }

            return checkdate($month, $day, $year)
                ? sprintf('%04d-%02d-%02d', $year, $month, $day)
                : null;
        }

        // Expiry formats: MM/YYYY or MMM-YY, e.g. 07/2028 or Jul-28.
        if (preg_match('/^(\d{1,2})\/(\d{4})$/', $value, $m)) {
            $month = (int) $m[1];
            $year  = (int) $m[2];
        } elseif (preg_match('/^([A-Za-z]{3,})[-\/ ](\d{2}|\d{4})$/', $value, $m)) {
            $month = (int) date('n', strtotime($m[1] . ' 1'));
            $year  = (int) $m[2];

            if ($year < 100) {
                $year += ($year <= 69) ? 2000 : 1900;
            }
        } else {
            return null;
        }

        if ($month < 1 || $month > 12 || $year < 1) {
            return null;
        }

        // Return the last day of the expiry month.
        return date('Y-m-t', strtotime(sprintf('%04d-%02d-01', $year, $month)));
    }
}
