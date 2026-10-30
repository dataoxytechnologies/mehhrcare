<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Pathology extends Admin_Controller
{
	public $agerange;
	public $blood_group;
	public $charge_type;
	public $marital_status;
	public $patient_login_prefix;
	public $payment_mode;
	public $search_type;
	public $time_format;


    public function __construct()
    {
        parent::__construct();
        $this->config->load("payroll");
        $this->load->library('Enc_lib');
        $this->load->library('mailsmsconf');
        $this->load->library('datatables');
        $this->load->library('system_notification');
        $this->load->library('SaasValidation');
        $this->load->config('image_valid');
        $this->marital_status       = $this->config->item('marital_status');
        $this->payment_mode         = $this->config->item('payment_mode');
        $this->search_type          = $this->config->item('search_type');
        $this->blood_group          = $this->config->item('bloodgroup');
        $this->charge_type          = $this->customlib->getChargeMaster();
        $this->payment_mode         = $this->config->item('payment_mode');
        $data["charge_type"]        = $this->charge_type;
        $this->patient_login_prefix = "pat";
        $this->load->model(array('prefix_model', 'transaction_model'));
        $this->load->helper('customfield_helper');
        $this->load->helper('custom');
        $this->time_format = $this->customlib->getHospitalTimeFormat();
		$this->agerange             = $this->config->item('agerange');
    }

    public function unauthorized()
    {
        $data = array();
        $this->load->view('layout/header', $data);
        $this->load->view('unauthorized', $data);
        $this->load->view('layout/footer', $data);
    }

    public function search()
    {
        if (!$this->rbac->hasPrivilege('pathology_test', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'pathology');
        $parametername         = $this->pathology_category_model->getpathoparameter();
        $data["parametername"] = $parametername;

        $data["title"]    = $this->lang->line('pathology');
        $doctors          = $this->staff_model->getStaffbyrole(3);
        $data["doctors"]  = $doctors;
        $patients         = $this->patient_model->getPatientListall();
        $data["patients"] = $patients;
        $data['fields']   = $this->customfield_model->get_custom_fields('pathologytest', 1);

        $data['module'] = 'pathology';
        $this->load->view('layout/header', $data);
        $this->load->view('admin/pathology/search', $data);
        $this->load->view('layout/footer', $data);
    }

    public function createPathologyTest()
    {
        $data                    = array();
        $data['charge_category'] = $this->charge_category_model->getCategoryByModule("pathology");
        $categoryName            = $this->pathology_category_model->getcategoryName();
        $data["categoryName"]    = $categoryName;
        $parametername           = $this->pathology_category_model->getpathoparameter();
        $data["parametername"]   = $parametername;
        $page                    = $this->load->view("admin/pathology/_createPathologyTest", $data, true);

        echo json_encode(array('status' => 1, 'page' => $page));
    }

    public function editPathologyTest()
    {
        $data           = array();
        $total_rows     = "";
        $id             = $this->input->get("id");
        $result         = $this->pathology_model->getDetails($id);
        $data['result'] = $result;
 
        $data['charge_category'] = $this->charge_category_model->getCategoryByModule("pathology");
        $categoryName            = $this->pathology_category_model->getcategoryName();
        $data["categoryName"]    = $categoryName;
        $parametername           = $this->pathology_category_model->getpathoparameter();
        $data["parametername"]   = $parametername;
        if (!empty($result->pathology_parameter)) {
            $total_rows = count($result->pathology_parameter);
        }
     
        $page = $this->load->view("admin/pathology/_editPathologyTest", $data, true);
      
        echo json_encode(array('status' => 1, 'page' => $page, 'total_rows' => $total_rows));
    }

    public function add()
    {
        if (!$this->rbac->hasPrivilege('pathology_test', 'can_add')) {
            access_denied();
        }
        $custom_fields = $this->customfield_model->getByBelong('pathologytest');

        foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
            if ($custom_fields_value['validation']) {
                $custom_fields_id   = $custom_fields_value['id'];
                $custom_fields_name = $custom_fields_value['name'];
                $this->form_validation->set_rules("custom_fields[pathologytest][" . $custom_fields_id . "]", $custom_fields_name, 'trim|required|xss_clean');
            }
        }
        $check_duplicate_test = array();

        $total_rows = $this->input->post('total_rows', TRUE);
        if (!isset($total_rows)) {
            $this->form_validation->set_rules('no_records', $this->lang->line('no_records'), 'trim|required|xss_clean',
                array('required' => $this->lang->line('no_parameter_selected')));
        }

        if (isset($total_rows) && !empty($total_rows)) {
            foreach ($total_rows as $row_key => $row_value) {
                $parameter_name  = $this->input->post('parameter_name_' . $row_value, TRUE);
                $reference_range = $this->input->post('reference_range_' . $row_value, TRUE);
                $patho_unit      = $this->input->post('patho_unit_' . $row_value, TRUE);

                if ($reference_range == "") {
                    $this->form_validation->set_rules('reference_range', $this->lang->line('reference_range'), 'trim|required|xss_clean');
                }
				
                if ($patho_unit == "") {
                    $this->form_validation->set_rules('unit', $this->lang->line('unit'), 'trim|required|xss_clean');
                }

                if ($parameter_name == "") {
                    $this->form_validation->set_rules('parameter_name', $this->lang->line('test_parameter_name'), 'trim|required|xss_clean');
                } else {
                    $check_duplicate_test[] = $parameter_name;
                }
            }
        }

        if (!empty($check_duplicate_test)) {

            if (has_duplicate_array($check_duplicate_test)) {
                $this->form_validation->set_rules('duplicate_test', $this->lang->line("duplicate_parameter_name"), 'trim|required|xss_clean', array('required' => 'The %s not allowed.'));
            }
        }

        $this->form_validation->set_rules('test_name', $this->lang->line('test_name'), 'callback_pathology_test|xss_clean');
        $this->form_validation->set_rules('pathology_category_id', $this->lang->line('category_name'), 'required|xss_clean');
        $this->form_validation->set_rules('code', $this->lang->line('charge_name'), 'required|xss_clean');
        $this->form_validation->set_rules('standard_charge', $this->lang->line('standard_charge'), 'required|xss_clean');
        $this->form_validation->set_rules('amount', $this->lang->line('amount'), 'required|xss_clean');
        $this->form_validation->set_rules('charge_category_id', $this->lang->line('charge_category'), 'required|xss_clean');
        $this->form_validation->set_rules('report_days', $this->lang->line('report_days'), 'required|xss_clean');
        if ($this->form_validation->run() == false) {
            $msg = array(
                'no_records'            => form_error('no_records'),
                'test_name'             => form_error('test_name'),
                'short_name'            => form_error('short_name'),
                'pathology_category_id' => form_error('pathology_category_id'),
                'parameter_name'        => form_error('parameter_name'),
                'charge_category_id'    => form_error('charge_category_id'),
                'code'                  => form_error('code'),
                'reference_range'       => form_error('reference_range'),
                'unit'                  => form_error('unit'),
                'standard_charge'       => form_error('standard_charge'),
                'amount'                => form_error('amount'),
                'duplicate_test'        => form_error('duplicate_test'),
                'report_days'        	=> form_error('report_days'),
            );
			
            if (!empty($custom_fields)) {
                foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
                    if ($custom_fields_value['validation']) {
                        $custom_fields_id                                                      = $custom_fields_value['id'];
                        $custom_fields_name                                                    = $custom_fields_value['name'];
                        $error_msg2["custom_fields[pathologytest][" . $custom_fields_id . "]"] = form_error("custom_fields[pathologytest][" . $custom_fields_id . "]");
                    }
                }
            }
			
            if (!empty($error_msg2)) {
                $error_msg = array_merge($msg, $error_msg2);
            } else {
                $error_msg = $msg;
            }
			
            $array = array('status' => 'fail', 'error' => $error_msg, 'message' => '');
        } else {

            $id                     = $this->input->post('id', TRUE);
            $insert_parameter_array = array();
            $update_parameter_array = array();
            $prev_inserted_array    = $this->input->post('prev_inserted', TRUE);
            if (!isset($prev_inserted_array)) {
                $prev_inserted_array = array();
            }
            $update_inserted_array = array();
            $total_rows            = $this->input->post('total_rows', TRUE);
            foreach ($total_rows as $row_key => $row_value) {
                $chk_new = $this->input->post('inserted_id_' . $row_value, TRUE);
                if ($chk_new == 0) {
                    $insert_parameter_array[] = array(
                        'pathology_id'           => 0,
                        'pathology_parameter_id' => $this->input->post('parameter_name_' . $row_value, TRUE),
                    );
                } else {
                    $update_inserted_array[]  = $chk_new;
                    $update_parameter_array[] = array(
                        'id'                     => $chk_new,
                        'pathology_id'           => $id,
                        'pathology_parameter_id' => $this->input->post('parameter_name_' . $row_value, TRUE),
                    );
                }
            }

            $deleted_parameter_array = array_diff($prev_inserted_array, $update_inserted_array);

            $parameter_id = $this->input->post('parameter_name', TRUE);
            $pathology    = array(
                'test_name'             => $this->input->post('test_name', TRUE),
                'short_name'            => $this->input->post('short_name', TRUE),
                'test_type'             => $this->input->post('test_type', TRUE),
                'pathology_category_id' => $this->input->post('pathology_category_id', TRUE),
                'sub_category'          => $this->input->post('sub_category', TRUE),
                'report_days'           => $this->input->post('report_days', TRUE),
                'method'                => $this->input->post('method', TRUE),
                'charge_id'             => $this->input->post('code', TRUE),

            );
            if ($id > 0) {
                $pathology['id'] = $id;
            }
            $insert_id = $this->pathology_model->add($pathology, $insert_parameter_array, $update_parameter_array, $deleted_parameter_array);

            $custom_field_post  = $this->input->post("custom_fields[pathologytest]", TRUE);
            $custom_value_array = array();
            if (!empty($custom_field_post)) {
                foreach ($custom_field_post as $key => $value) {
                    $check_field_type = $this->input->post("custom_fields[pathologytest][" . $key . "]", TRUE);
                    $field_value      = is_array($check_field_type) ? implode(",", $check_field_type) : $check_field_type;
                    $array_custom     = array(
                        'belong_table_id' => $insert_id,
                        'custom_field_id' => $key,
                        'field_value'     => $field_value,
                    );
                    $custom_value_array[] = $array_custom;
                }
            }

            if (!empty($custom_value_array)) {
                if ($id > 0) {
                    $this->customfield_model->updateRecord($custom_value_array, $id, 'pathologytest');
                } else {
                    $this->customfield_model->insertRecord($custom_value_array, $insert_id);
                }
            }

            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
        }

        echo json_encode($array);
    }

    public function pathology_test()
    {
        $id         = $this->input->post('id', TRUE);
        $test_name  = $this->input->post('test_name', TRUE);
        $short_name = $this->input->post('short_name', TRUE);

        if ($test_name == "" && $short_name != "") {
            $this->form_validation->set_message('pathology_test', $this->lang->line('the_test_name_field_is_required'));
            return false;
        }
        if ($short_name == "" && $test_name != "") {
            $this->form_validation->set_message('pathology_test', $this->lang->line('the_short_name_field_is_required'));
            return false;
        }
        if ($short_name == "" && $test_name == "") {
            $this->form_validation->set_message('pathology_test', $this->lang->line('the_test_name_and_short_name_required'));
            return false;
        }
        if ($test_name != '' && $short_name != '') {
            $count = $this->pathology_model->test_uniqe($test_name, $short_name, $id);
            if ($count > 0) {
                $this->form_validation->set_message('pathology_test', $this->lang->line('test_name_and_short_name_already_exist'));
                return false;
            }
        } else {
            return true;
        }
    }
	
    public function addBill()
    {
        $custom_fields = $this->customfield_model->getByBelong('pathology');
        foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
            if ($custom_fields_value['validation']) {
                $custom_fields_id   = $custom_fields_value['id'];
                $custom_fields_name = $custom_fields_value['name'];
                $this->form_validation->set_rules("custom_fields[pathology][" . $custom_fields_id . "]", $custom_fields_name, 'trim|required|xss_clean');
            }
        }
        $transaction_data     = array();
        $pathology_billing_id = $this->input->post('pathology_billing_id', TRUE);
        $prescription_no      = $this->input->post('is_prescription_no_exist', TRUE);


        $this->form_validation->set_rules('net_amount', $this->lang->line('net_amount'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('total', $this->lang->line('total'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('patientid', $this->lang->line('patient'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('discount', $this->lang->line('discount'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('tax', $this->lang->line('tax'), 'trim|required|xss_clean');
        if (empty($pathology_billing_id)) {

            $this->form_validation->set_rules(
                'amount', $this->lang->line('amount'), array('trim', 'required', 'xss_clean', 'valid_amount',
                    array('check_exists', array($this->pathology_model, 'validate_paymentamount')),
                )
            );
            if ($this->input->post("payment_mode", TRUE) == "Cheque") {
                $this->form_validation->set_rules('cheque_no', $this->lang->line('cheque_no'), 'trim|required|xss_clean');
                $this->form_validation->set_rules('cheque_date', $this->lang->line('cheque_date'), 'trim|required|xss_clean');
                $this->form_validation->set_rules('document', $this->lang->line('documents'), 'callback_handle_doc_upload[document]|callback_validateCanUploadFile[document]');
            }
            // SaaS: count pre-check — block a NEW pathology bill once the tenant hits
            // its no_of_pathology limit. Create-only (this block runs only when pathology_billing_id is empty).
            $this->form_validation->set_rules('validate_resource', $this->lang->line('pathology'), 'callback_validateCanAddNewResource[no_of_pathology,1]');
        }

        $this->form_validation->set_rules('date', $this->lang->line('date'), 'trim|required|xss_clean');

        $total_rows = $this->input->post('total_rows', TRUE);
        if (!isset($total_rows) && !isset($pathology) && !isset($radiology)) {
            $this->form_validation->set_rules('no_records', $this->lang->line('no_records'), 'trim|required|xss_clean',
                array('required' => $this->lang->line('no_test_selected')));
        }
        $check_duplicate_test = array();
        if (isset($total_rows) && !empty($total_rows)) {
            foreach ($total_rows as $row_key => $row_value) {

                $test_name              = $this->input->post('test_name_' . $row_value, TRUE);
                $reportdate             = $this->input->post('reportdate_' . $row_value, TRUE);
                $check_duplicate_test[] = $test_name;

                if ($test_name == "") {
                    $this->form_validation->set_rules('test_name', $this->lang->line('test_name'), 'trim|required|xss_clean');
                }
                if ($reportdate == "") {
                    $this->form_validation->set_rules('reportdate', $this->lang->line('report_date'), 'trim|required|xss_clean');
                }
            }
        }
		
        if (!empty($check_duplicate_test)) {
            if (has_duplicate_array($check_duplicate_test)) {
                $this->form_validation->set_rules('duplicate_test', $this->lang->line("duplicate_test"), 'trim|required|xss_clean', array('required' => 'The %s not allowed.'));
            }
        }

        if ($this->form_validation->run() == false) {
            $msg = array(
                'no_records'     => form_error('no_records'),
                'patientid'      => form_error('patientid'),
                'discount'       => form_error('discount'),
                'tax'            => form_error('tax'),
                'test_name'      => form_error('test_name'),
                'reportdate'     => form_error('reportdate'),
                'amount'         => form_error('amount'),
                'duplicate_test' => form_error('duplicate_test'),
                'document'       => form_error('document'),
                'date'           => form_error('date'),
                'net_amount'     => form_error('net_amount'),
                'total'          => form_error('total'),
                'validate_resource' => form_error('validate_resource'),
            );

            if (empty($pathology_billing_id)) {
                if ($this->input->post("payment_mode", TRUE) == "Cheque") {
                    $msg['cheque_no']   = form_error('cheque_no');
                    $msg['cheque_date'] = form_error('cheque_date');
                }
            }

            if (!empty($custom_fields)) {
                foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
                    if ($custom_fields_value['validation']) {
                        $custom_fields_id                                                  = $custom_fields_value['id'];
                        $custom_fields_name                                                = $custom_fields_value['name'];
                        $error_msg2["custom_fields[pathology][" . $custom_fields_id . "]"] = form_error("custom_fields[pathology][" . $custom_fields_id . "]");
                    }
                }
            }
            if (!empty($error_msg2)) {
                $error_msg = array_merge($msg, $error_msg2);
            } else {
                $error_msg = $msg;
            }
            $array = array('status' => 'fail', 'error' => $error_msg, 'message' => '');
        } else {
          
            $patient_id        = $this->input->post('patientid', TRUE);
            $bill_date         = $this->customlib->dateFormatToYYYYMMDDHis($this->input->post('date', TRUE));
            $doctor_name       = $this->input->post('doctor_name', TRUE);
            $doctor_id         = $this->input->post('consultant_doctor', TRUE);
            $case_reference_id = $this->input->post('case_reference_id', TRUE);
            if (empty($doctor_id)) {
                $doctor_id = null;
            }
			
            if (empty($case_reference_id)) {
                $case_reference_id = null;
            }

            if ($prescription_no != "") {
                $prescription_prefix = splitPrefixType($prescription_no);
                $prescription_no     = splitPrefixID($prescription_no);
            } else {
                $prescription_no = null;
            }

            $is_tpa = $this->input->post('is_tpa', TRUE);
            if($is_tpa=="" || $is_tpa==0){
                $organisation_id = null;
                $insurance_id = null;
                $insurance_validity = null;
            }else{
                $organisation_id    = $this->input->post('organisation_id', TRUE);
                $insurance_id       = $this->input->post('insurance_id', TRUE);
                $insurance_validity = $this->customlib->dateFormatToYYYYMMDD($this->input->post('insurance_validity', TRUE));
            }
			
            if (empty($organisation_id)) {
                $organisation_id = null;
            }

            $data = array(
                'date'                      => $bill_date,
                'patient_id'                => $patient_id,
                'doctor_name'               => $doctor_name,
                'doctor_id'                 => $doctor_id,
                'case_reference_id'         => $case_reference_id,
                'ipd_prescription_basic_id' => $prescription_no,
                'total'                     => $this->input->post('total', TRUE),
                'discount'                  => $this->input->post('discount', TRUE),
                'discount_percentage'       => $this->input->post('discount_percent', TRUE),
                'tax'                       => $this->input->post('tax', TRUE),
                'net_amount'                => $this->input->post('net_amount', TRUE),
                'note'                      => $this->input->post('note', TRUE),
                'generated_by'              => $this->customlib->getLoggedInUserID(),
                'organisation_id'           => $organisation_id,
                'insurance_validity'        => $insurance_validity,
                'insurance_id'              => $insurance_id,
            );

            $custom_field_post  = $this->input->post("custom_fields[pathology]", TRUE);
            $custom_value_array = array();
            if (!empty($custom_field_post)) {
                foreach ($custom_field_post as $key => $value) {
                    $check_field_type = $this->input->post("custom_fields[pathology][" . $key . "]", TRUE);
                    $field_value      = is_array($check_field_type) ? implode(",", $check_field_type) : $check_field_type;
                    $array_custom     = array(
                        'belong_table_id' => 0,
                        'custom_field_id' => $key,
                        'field_value'     => $field_value,
                    );
                    $custom_value_array[] = $array_custom;
                }
            }

            if ($pathology_billing_id > 0) {
                $data['id'] = $pathology_billing_id;
            }

            $total_rows   = $this->input->post('total_rows', TRUE);
            $prev_reports = $this->input->post('prev_reports', TRUE);

            $insert_array              = array();
            $update_array              = array();
            $prev_reports_array        = array();
            $prev_reports_update_array = array();
			
            if (isset($prev_reports)) {
                $prev_reports_array = $prev_reports;
            }

            foreach ($total_rows as $row_key => $row_value) {
                $test_report_id = $this->input->post('inserted_id_' . $row_value, TRUE);
                if ($test_report_id == 0) {
                    $report = array(
                        'pathology_bill_id' => 0,
                        'patient_id'        => $patient_id,
                        'pathology_id'      => $this->input->post('test_name_' . $row_value, TRUE),
                        'tax_percentage'    => $this->input->post('taxpercent_' . $row_value, TRUE),
                        'reporting_date'    => $this->customlib->dateFormatToYYYYMMDD($this->input->post('reportdate_' . $row_value, TRUE)),
                        'apply_charge'      => $this->input->post('amount_' . $row_value, TRUE),
                    );
                    $insert_array[] = $report;
                } else if ($test_report_id > 0) {
                    $report = array(
                        'id'             => $test_report_id,
                        'patient_id'     => $patient_id,
                        'pathology_id'   => $this->input->post('test_name_' . $row_value, TRUE),
                        'tax_percentage' => $this->input->post('taxpercent_' . $row_value, TRUE),
                        'reporting_date' => $this->customlib->dateFormatToYYYYMMDD($this->input->post('reportdate_' . $row_value, TRUE)),
                        'apply_charge'   => $this->input->post('amount_' . $row_value, TRUE),
                    );
                    $prev_reports_update_array[] = $test_report_id;
                    $update_array[]              = $report;
                }
            }

            if (empty($pathology_billing_id)) {

                $cheque_date     = $this->customlib->dateFormatToYYYYMMDD($this->input->post("cheque_date", TRUE));
                $payment_section = $this->config->item('payment_section');

                $transaction_data = array(
                    'pathology_billing_id' => 0,
                    'patient_id'           => $patient_id,
                    'case_reference_id'    => $case_reference_id,
                    'section'              => $payment_section['pathology'],
                    'amount'               => $this->input->post('amount', TRUE),
                    'type'                 => 'payment',
                    'ipd_id'               => $this->input->post('ipdid', TRUE),
                    'payment_mode'         => $this->input->post('payment_mode', TRUE),
                    'payment_date'         => $bill_date,
                    'received_by'          => $this->customlib->getLoggedInUserID(),
                );

                if ($this->input->post('payment_mode', TRUE) == "Cheque") {
                    $transaction_data['cheque_date'] = $cheque_date;
                    $transaction_data['cheque_no']   = $this->input->post('cheque_no', TRUE);

                    if (isset($_FILES["document"]) && !empty($_FILES['document']['name'])) {
                        $file_name = $this->media_storage->fileupload("document", './uploads/payment_document/');
                        $transaction_data['attachment']      = $file_name;
                        $transaction_data['attachment_name'] = $_FILES["document"]["name"];

                        // SaaS: add the uploaded document's size to the storage quota usage.
                        try {
                            $this->saasvalidation->updateStorageLimit('storage', ['document']);
                        } catch (Exception $e) {
                            log_message('error', 'SaaS storage quota update failed (pathology addBill): ' . $e->getMessage());
                        }
                    }
                }
            }

            $array_delete = array_diff($prev_reports_array, $prev_reports_update_array);
            $inserted= $this->pathology_model->addBill($data, $insert_array, $update_array, $array_delete, $pathology_billing_id, $transaction_data);

            if ($pathology_billing_id > 0) {
                if (!empty($custom_fields)) {
                    foreach ($custom_field_post as $key => $value) {
                        $check_field_type = $this->input->post("custom_fields[pathology][" . $key . "]", TRUE);
                        $field_value      = is_array($check_field_type) ? implode(",", $check_field_type) : $check_field_type;
                        $array_custom     = array(
                            'belong_table_id' => $inserted,
                            'custom_field_id' => $key,
                            'field_value'     => $field_value,
                        );
                        $custom_value_array[] = $array_custom;
                    }
                    $this->customfield_model->updateRecord($custom_value_array, $inserted, 'pathology');
                }

            } else {
                if (!empty($custom_value_array)) {
                    $this->customfield_model->insertRecord($custom_value_array, $inserted);
                }

                // SaaS: a NEW pathology bill was created — increment the no_of_pathology count
                // usage. Create-only branch ($pathology_billing_id empty); wrapped so an API hiccup never blocks the save.
                if ($inserted) {
                    try {
                        $this->saasvalidation->updateResouceQuota('no_of_pathology', 1);
                    } catch (Exception $e) {
                        log_message('error', 'SaaS no_of_pathology count increment failed (pathology addBill insert): ' . $e->getMessage());
                    }
                }
            }

            if ($inserted) {
                $patientlist = $this->notificationsetting_model->getpatientDetails($patient_id);

                $event_data = array(
                    'patient_id'  => $patient_id,
                    'case_id'     => $this->input->post('case_reference_id', TRUE),
                    'bill_no'     => $this->input->post('bill_no', TRUE),
                    'date'        => $this->customlib->YYYYMMDDTodateFormat($this->input->post('date', TRUE)),
                    'total'       => $this->input->post('total', TRUE),
                    'discount'    => $this->input->post('discount', TRUE),
                    'tax'         => $this->input->post('tax', TRUE),
                    'net_amount'  => $this->input->post('net_amount', TRUE),
                    'paid_amount' => $this->input->post('amount', TRUE),
                );

                if ($doctor_id != "") {
                    $doctor_details            = $this->notificationsetting_model->getstaffDetails($doctor_id);
                    $event_data['doctor_name'] = composeStaffNameByString($doctor_details['name'], $doctor_details['surname'], $doctor_details['employee_id']);
                    $event_data['doctor_id']   = $doctor_id;
                } else {
                    $event_data['doctor_name'] = $doctor_name;
                    $event_data['doctor_id']   = $doctor_id;
                }

                $this->system_notification->send_system_notification('pathology_investigation', $event_data);

                $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'), 'insert_id' => $inserted);
            } else {
                $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('something_went_wrong'), 'insert_id' => $inserted);
            }

        }
        echo json_encode($array);
    }

    public function addpatient()
    {
        if (!$this->rbac->hasPrivilege('pathology_test', 'can_add')) {
            access_denied();
        }

        $this->form_validation->set_rules('name', $this->lang->line('name'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('file', $this->lang->line('file'), 'callback_validateCanUploadFile[file]');
        if ($this->form_validation->run() == false) {
            $msg = array(
                'name' => form_error('name'),
                'file' => form_error('file'),
            );
            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $check_patient_id = $this->patient_model->getMaxId();
            if (empty($check_patient_id)) {
                $check_patient_id = 1000;
            }

            $patient_id = $check_patient_id + 1;

            $patient_data = array(
                'patient_name'      => $this->input->post('name', TRUE),
                'mobileno'          => $this->input->post('contact', TRUE),
                'marital_status'    => $this->input->post('marital_status', TRUE),
                'email'             => $this->input->post('email', TRUE),
                'gender'            => $this->input->post('gender', TRUE),
                'guardian_name'     => $this->input->post('guardian_name', TRUE),
                'blood_group'       => $this->input->post('blood_group', TRUE),
                'address'           => $this->input->post('address', TRUE),
                'known_allergies'   => $this->input->post('known_allergies', TRUE),
                'patient_unique_id' => $patient_id,
                'note'              => $this->input->post('note', TRUE),
                'age'               => $this->input->post('age', TRUE),
                'month'             => $this->input->post('month', TRUE),
                'is_active'         => 'yes',
            );
            $insert_id = $this->patient_model->add_patient($patient_data);

            $user_password      = $this->role->get_random_password($chars_min = 6, $chars_max = 6, $use_upper_case = false, $include_numbers = true, $include_special_chars = false);
            $data_patient_login = array(
                'username' => $this->patient_login_prefix . $insert_id,
                'password' => $user_password,
                'user_id'  => $insert_id,
                'role'     => 'patient',
            );
            $this->user_model->add($data_patient_login);
            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
            if (isset($_FILES["file"]) && !empty($_FILES['file']['name'])) {
                $fileInfo = pathinfo($_FILES["file"]["name"]);
                $img_name = $insert_id . '.' . $fileInfo['extension'];
                if (move_uploaded_file($_FILES["file"]["tmp_name"], "./uploads/patient_images/" . $img_name)) {
                    $data_img = array('id' => $insert_id, 'image' => 'uploads/patient_images/' . $img_name);
                    $this->patient_model->add($data_img);

                    // SaaS: add the uploaded patient image size to the storage quota usage.
                    try {
                        $this->saasvalidation->updateStorageLimit('storage', ['file']);
                    } catch (Exception $e) {
                        log_message('error', 'SaaS storage quota update failed (pathology addpatient): ' . $e->getMessage());
                    }
                }
            }
        }
        echo json_encode($array);
    }

    public function prescriptionBill()
    {
        $this->form_validation->set_rules('prescription_no', $this->lang->line('prescription_no'), 'required|xss_clean');
        if ($this->form_validation->run() == false) {     
            $msg = array(
                'prescription_no'            => form_error('prescription_no'),               
            );
         
            $array = array('status' => 0, 'error' =>$msg, 'message' => '');
            echo json_encode($array);
        } else {
            $prescription_no = $this->input->post('prescription_no', TRUE);
            $date            = $this->input->post('date', TRUE);
            $pathology_tests = $this->pathology_model->getpathotestDetails();    
            $data["pathology_tests"] = $pathology_tests;
            $data["date"]            = $this->customlib->dateFormatToStrtotime($date);
            $data["payment_mode"]    = $this->payment_mode;
            $doctors                 = $this->staff_model->getStaffbyrole(3);
            $data["doctors"]         = $doctors;
            $prefixes                = $this->prefix_model->getByCategory(array('ipd_prescription', 'opd_prescription'));
            $total_rows              = 1;
            $patient_id              = 0;
            $patient_name            = "";
            $total_tests_rows        = 0;
            $prefix_type             = "";
            $case_reference_id       = "";
            $prescription_prefix     = splitPrefixType($prescription_no);
            $prescription_no         = splitPrefixID($prescription_no);
            if (!empty($prefixes)) {
                $prefix_type = findPrefixType($prefixes, $prescription_prefix);
            }
    
            $prescription_test = $this->prescription_model->getPrescriptionTestsByCategory($prescription_no, $prefix_type, 'pathology_id');    
    
            if (!empty($prescription_test)) {
                $id      = $prescription_test->id;
                $patient_name      = $prescription_test->patient_name;
                $patient_id        = $prescription_test->patient_id;
                $total_rows        = count($prescription_test->tests);
                $case_reference_id = $prescription_test->case_reference_id;     
				$is_tpa= $this->input->post('is_tpa', TRUE);
    
				if($patient_id != 0){
					
					$result            = $this->patient_model->getpatientDetails($patient_id);
					if(empty($result['organisation_id'])){
						$data['organisation_id']        =  null;
					}else{
						$data['organisation_id']        =  $result['organisation_id'] ;
					}
					$data['insurance_id']           =  $result['insurance_id'] ;
					$data['insurance_validity']     =  $result['insurance_validity'];
					$tpa_status=0;
					if(!empty($result['organisation_id'])){
						if($is_tpa==1){
							if(strtotime($result['insurance_validity']) < strtotime(date('Y-m-d'))){
								$tpa_status=1;
							}else{
								$tpa_status=0;
							}
						}
					}
				}            
    
				$data['prescription_test']      =  $prescription_test;
				$data['is_tpa']                 =   $is_tpa;            
    
				$page = $this->load->view("admin/pathology/_prescriptionBill", $data, true);  
    
				echo json_encode(array('status' => 1, 'page' => $page, 'patient_id' => $patient_id, 'total_rows' => $total_rows, 'patient_name' => $patient_name, 'case_reference_id' => $case_reference_id,'tpa_status'=>$tpa_status, 'msg'=> $this->lang->line('your_tpa_validity_has_expired_on').' '.$this->customlib->YYYYMMDDTodateFormat($result['insurance_validity']).', '. $this->lang->line('so_standard_charges_has_been_applied')));
				}else{
				echo json_encode(array('status' => 1, 'patient_id' => 0 ));
				}
			}   
    }

    public function editparameter($id)
    {
        $parametername         = $this->pathology_category_model->getpathoparameter();
        $data["parametername"] = $parametername;
        $detail                = $this->pathology_category_model->getparameterDetails($id);
        $data['detail']        = $detail;
        $this->load->view("admin/pathology/editparameter", $data);
    }

    public function parameterview($id, $value_id = '')
    {
        $parametername         = $this->pathology_category_model->getpathoparameter();
        $data["parametername"] = $parametername;
        $detail                = $this->pathology_category_model->getparameterDetails($id, $value_id);
        $data['detail']        = $detail;
        $this->load->view("admin/pathology/parameterview", $data);
    }

    public function parameterdetails($id, $value_id = '')
    {
        $parametername         = $this->pathology_category_model->getpathoparameter();
        $data["parametername"] = $parametername;
        $detail                = $this->pathology_category_model->getparameterDetailsforpatient($value_id);
        $data['detail'] = $detail;
        $this->load->view("admin/pathology/parameterdetails", $data);
    }

    public function getparameterdetails()
    {
        $id     = $this->input->get_post('id');
        $result = $this->pathology_category_model->getpathoparameter($id);
        echo json_encode($result);
    }

    public function gettestpathodetails()
    {
        $id     = $this->input->post('id', TRUE);
        $patient_id=$this->input->post('patient_id', TRUE);
        $is_tpa_valid=true;
        if($this->input->post('is_tpa', TRUE) == 1 && $patient_id != ""){          
            $result = $this->pathology_model->getPathologyChargeByPatient($id,$patient_id);            
            if($result && !IsNullOrEmptyString($result->insurance_validity)){
                if(strtotime($result->insurance_validity) >= strtotime(date('Y-m-d'))){
                    $result->standard_charge= $result->org_charge;
                }else{
                    $is_tpa_valid=false;  
                }              
            }
        }else{
            $result = $this->pathology_model->getPathologyChargeByPatient($id);
        }

        if(!$result){
            echo json_encode(array('status' => 0, 'msg' => $this->lang->line('no_charge_has_configured_for_selected_category')));
        }elseif($result && !$is_tpa_valid){
            echo json_encode(array('status' => 2, 'result' => $result,'msg'=> $result,'msg'=> $this->lang->line('your_tpa_validity_has_expired_on').' '.$this->customlib->YYYYMMDDTodateFormat($result->insurance_validity).', '. $this->lang->line('so_standard_charges_has_been_applied')));
        }else{
            echo json_encode(array('status' => 1, 'result' => $result));
        }       
    }

    public function pathologyDetails()
    {
        if (!$this->rbac->hasPrivilege('pathology_test', 'can_view')) {
            access_denied();
        }
        $id             = $this->input->post("pathology_id", TRUE);
        $result         = $this->pathology_model->getDetails($id);
        $data['result'] = $result;
        $page = $this->load->view("admin/pathology/_pathologyDetails", $data, true);
        echo json_encode(array('status' => 1, 'page' => $page));
    }

    public function update()
    {
        if (!$this->rbac->hasPrivilege('pathology_test', 'can_edit')) {
            access_denied();
        }
        $custom_fields = $this->customfield_model->getByBelong('pathology');
        if (!empty($custom_fields)) {
            foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
                if ($custom_fields_value['validation']) {
                    $custom_fields_id   = $custom_fields_value['id'];
                    $custom_fields_name = $custom_fields_value['name'];
                    $this->form_validation->set_rules("custom_fields[pathology][" . $custom_fields_id . "]", $custom_fields_name, 'trim|required|xss_clean');
                }
            }
        }
        $this->form_validation->set_rules('test_name', $this->lang->line('test_name'), 'required|xss_clean');
        $this->form_validation->set_rules('short_name', $this->lang->line('short_name'), 'required|xss_clean');
        $this->form_validation->set_rules('test_type', $this->lang->line('test_type'), 'required|xss_clean');
        $this->form_validation->set_rules('pathology_category_id', $this->lang->line('category_name'), 'required|xss_clean');
        $this->form_validation->set_rules('code', $this->lang->line('code'), 'required|xss_clean');
        $this->form_validation->set_rules('charge_category_id', $this->lang->line('charge_category'), 'required|xss_clean');
        if ($this->form_validation->run() == false) {
            $msg = array(
                'test_name'             => form_error('test_name'),
                'short_name'            => form_error('short_name'),
                'test_type'             => form_error('test_type'),
                'pathology_category_id' => form_error('pathology_category_id'),
                'code'                  => form_error('code'),
                'charge_category_id'    => form_error('charge_category_id'),
            );
            if (!empty($custom_fields)) {
                foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
                    if ($custom_fields_value['validation']) {
                        $custom_fields_id                                                  = $custom_fields_value['id'];
                        $custom_fields_name                                                = $custom_fields_value['name'];
                        $error_msg2["custom_fields[pathology][" . $custom_fields_id . "]"] = form_error("custom_fields[pathology][" . $custom_fields_id . "]");
                    }
                }
            }
            if (!empty($error_msg2)) {
                $error_msg = array_merge($msg, $error_msg2);
            } else {
                $error_msg = $msg;
            }
            $array = array('status' => 'fail', 'error' => $error_msg, 'message' => '');
        } else {

            $id                         = $this->input->post('id', TRUE);
            $charge_category_id         = $this->input->post('charge_category_id', TRUE);
            $pre_pathology_parameter_id = $this->input->post("previous_pathology_parameter_id[]", TRUE);
            $pre_pathology_id           = $this->input->post("previous_pathology_id", TRUE);
            $pre_parameter_id           = $this->input->post("previous_parameter_id[]", TRUE);
            $new_parameter_id           = $this->input->post("new_parameter_id[]", TRUE);
            $parameter_id               = $this->input->post("parameter_name[]", TRUE);
            $custom_field_post          = $this->input->post("custom_fields[pathology]", TRUE);
            $insert_data                = array();
            $pathology                  = array(
                'id'                    => $id,
                'test_name'             => $this->input->post('test_name', TRUE),
                'short_name'            => $this->input->post('short_name', TRUE),
                'test_type'             => $this->input->post('test_type', TRUE),
                'pathology_category_id' => $this->input->post('pathology_category_id', TRUE),
                'sub_category'          => $this->input->post('sub_category', TRUE),
                'report_days'           => $this->input->post('report_days', TRUE),
                'method'                => $this->input->post('method', TRUE),
                'charge_id'             => $this->input->post('code', TRUE),
            );
            if (!empty($custom_fields)) {
                foreach ($custom_field_post as $key => $value) {
                    $check_field_type = $this->input->post("custom_fields[pathology][" . $key . "]", TRUE);
                    $field_value      = is_array($check_field_type) ? implode(",", $check_field_type) : $check_field_type;
                    $array_custom     = array(
                        'belong_table_id' => $id,
                        'custom_field_id' => $key,
                        'field_value'     => $field_value,
                    );
                    $custom_value_array[] = $array_custom;
                }
                $this->customfield_model->updateRecord($custom_value_array, $id, 'pathology');
            }

            $i = 0;
            $j = 0;
            foreach ($parameter_id as $key => $value) {
                if (array_key_exists($i, $pre_pathology_parameter_id)) {
                    $detail = array(
                        'parameter_id' => $parameter_id[$i],
                        'id'           => $pre_pathology_parameter_id[$i],
                    );
                    $data[] = $detail;
                } else {
                    $j++;
                    $insert_detail = array(
                        'pathology_id' => $id,
                        'parameter_id' => $parameter_id[$i],
                    );
                    $insert_data[] = $insert_detail;
                }
                $i++;
            }

            $k         = $i - $j;
            $s         = 1;
            $condition = "";
            foreach ($data as $key => $value) {
                if ($s == $k) {
                    $coma = '';
                } else {
                    $coma = ',';
                }
                $condition .= "(" . (int)$value['parameter_id'] . "," . (int)$value['id'] . ")" . $coma;
                $s++;
            }

            $delete_arr = array();
            foreach ($pre_parameter_id as $pkey => $pvalue) {
                if (in_array($pvalue, $new_parameter_id)) {

                } else {
                    $delete_arr[] = array('id' => $pvalue);
                }
            }

            $this->pathology_model->updateparameter($condition);

            if (!empty($insert_data)) {
                $this->pathology_model->addparameter($insert_data);
            }

            if (!empty($delete_arr)) {
                $this->pathology_model->delete_parameter($delete_arr);
            }

            $this->pathology_model->update($pathology);
            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
            echo json_encode($array);
        }
    }

    public function delete()
    {
        $id = $this->input->post('id', TRUE);
        if (!$this->rbac->hasPrivilege('pathology_test', 'can_delete')) {
            access_denied();
        }
        if (!empty($id)) {
            $this->pathology_model->delete($id);
            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('delete_message'));
        } else {
            $array = array('status' => 'fail', 'error' => '', 'message' => $this->lang->line('something_went_wrong'));
        }
        echo json_encode($array);
    }

    public function deletebill()
    {
        $id = $this->input->post('id', TRUE);
        if (!$this->rbac->hasPrivilege('pathology_test', 'can_delete')) {
            access_denied();
        }
        if (!empty($id)) {
            // SaaS: release every linked transaction's attachment from storage quota before deletion.
            $saas_transactions = $this->transaction_model->pathologyPayments($id);
            if (!empty($saas_transactions)) {
                foreach ($saas_transactions as $saas_transaction) {
                    if (!empty($saas_transaction->attachment)) {
                        $doc_path = $saas_transaction->attachment;
                        $dir      = $this->media_storage->resolveAttachmentDir($doc_path);
                        $kb       = $this->media_storage->getUploadedFileSize($doc_path, $dir);
                        if ($kb > 0) {
                            try {
                                $this->saasvalidation->deleteResouceQuota('storage', $kb);
                            } catch (Exception $e) {
                                log_message('error', 'SaaS storage quota release failed (pathology deletebill transaction_id=' . $saas_transaction->id . '): ' . $e->getMessage());
                            }
                        }
                        $this->media_storage->filedelete($doc_path, $dir);
                    }
                }
            }

            // SaaS: release every linked test-report document (uploads/pathology_report/) before deletion.
            $saas_reports = $this->pathology_model->getReportByBillId($id);
            if (!empty($saas_reports)) {
                foreach ($saas_reports as $saas_report) {
                    if (!empty($saas_report->pathology_report)) {
                        $report_doc = $saas_report->pathology_report;
                        $report_dir = (strpos($report_doc, '/') !== false) ? '' : 'uploads/pathology_report';
                        $report_kb  = $this->media_storage->getUploadedFileSize($report_doc, $report_dir);
                        if ($report_kb > 0) {
                            try {
                                $this->saasvalidation->deleteResouceQuota('storage', $report_kb);
                            } catch (Exception $e) {
                                log_message('error', 'SaaS storage quota release failed (pathology deletebill report_id=' . $saas_report->id . '): ' . $e->getMessage());
                            }
                        }
                        $this->media_storage->filedelete($report_doc, $report_dir);
                    }
                }
            }

            $this->pathology_model->deletePathologyBill($id);
            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('delete_message'));
        } else {
            $array = array('status' => 'fail', 'error' => '', 'message' => $this->lang->line('something_went_wrong'));
        }
        echo json_encode($array);
    }

    public function getPathology()
    {
        if (!$this->rbac->hasPrivilege('pathology_test', 'can_view')) {
            access_denied();
        }

        $id     = $this->input->post('pathology_id', TRUE);
        $result = $this->pathology_model->getPathology($id);
        echo json_encode($result);
    }

    public function getPathologyReport()
    {
        if (!$this->rbac->hasPrivilege('pathology_test', 'can_view')) {
            access_denied();
        }
        $id                       = $this->input->post('id', TRUE);
        $result                   = $this->pathology_model->getPathologyReport($id);
        $result['reporting_date'] = date($this->customlib->getHospitalDateFormat(), strtotime($result['reporting_date']));
        echo json_encode($result);
    }

    public function getPathologyparameterReport()
    {
        if (!$this->rbac->hasPrivilege('pathology_test', 'can_view')) {
            access_denied();
        }
        $id                       = $this->input->post('id', TRUE);
        $result                   = $this->pathology_model->getPathologyparameterReport($id);
        $result['reporting_date'] = $this->customlib->YYYYMMDDTodateFormat($result['reporting_date']);
        echo json_encode($result);
    }

    public function updateTestReport()
    {
        if (!$this->rbac->hasPrivilege('pathology_test', 'can_edit')) {
            access_denied();
        }

        $this->form_validation->set_rules('id', $this->lang->line('id'), 'required|xss_clean');
        $this->form_validation->set_rules('apply_charge', $this->lang->line('applied_charge'), 'required|xss_clean');
        $this->form_validation->set_rules('pathology_report', $this->lang->line('file'), 'callback_handle_upload|callback_validateCanUploadFile[pathology_report]');

        if ($this->form_validation->run() == false) {
            $msg = array(
                'id'           => form_error('id'),
                'patient_name' => form_error('patient_name'),
                'apply_charge' => form_error('apply_charge'),
            );
            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $reporting_date = $this->input->post("reporting_date", TRUE);

            $id           = $this->input->post('id', TRUE);
            $report_batch = array(
                'id'                => $id,
                'patient_name'      => $this->input->post('patient_name', TRUE),
                'patient_id'        => $this->input->post('patient_id_patho', TRUE),
                'consultant_doctor' => $this->input->post('consultant_doctor', TRUE),
                'reporting_date'    => $this->customlib->dateFormatToYYYYMMDD($reporting_date),
                'description'       => $this->input->post('description', TRUE),
                'apply_charge'      => $this->input->post('apply_charge', TRUE),
            );

            $this->pathology_model->updateTestReport($report_batch);

            if (!empty($_FILES['pathology_report']['name'])) {
                // SaaS: capture the existing report file for quota diff + cleanup.
                $saas_existing   = $this->pathology_model->getPathologyReportByID($id);
                $saas_old_report = (!empty($saas_existing['pathology_report'])) ? $saas_existing['pathology_report'] : '';
                $saas_old_dir    = (strpos($saas_old_report, '/') !== false) ? '' : 'uploads/pathology_report';
                $saas_old_kb     = (!empty($saas_old_report)) ? $this->media_storage->getUploadedFileSize($saas_old_report, $saas_old_dir) : 0;

                $config['upload_path']   = 'uploads/pathology_report/';
                $config['allowed_types'] = 'jpg|jpeg|png|pdf|doc|docx|xls|xlsx';
                $config['file_name']     = $_FILES['pathology_report']['name'];

                $this->load->library('upload', $config);
                $this->upload->initialize($config);
                if ($this->upload->do_upload('pathology_report')) {
                    $uploadData = $this->upload->data();
                    $picture    = $uploadData['file_name'];

                    // SaaS: delete the old file (now replaced) and adjust quota by diff.
                    if (!empty($saas_old_report) && $saas_old_report !== $picture) {
                        $this->media_storage->filedelete($saas_old_report, $saas_old_dir);
                    }
                    try {
                        $saas_new_kb = $this->media_storage->getTmpFileSize('pathology_report');
                        if ($saas_old_kb > $saas_new_kb) {
                            $this->saasvalidation->deleteResouceQuota('storage', $saas_old_kb - $saas_new_kb);
                        } elseif ($saas_new_kb > $saas_old_kb) {
                            $this->saasvalidation->updateResouceQuota('storage', $saas_new_kb - $saas_old_kb);
                        }
                    } catch (Exception $e) {
                        log_message('error', 'SaaS storage quota update failed (pathology updateTestReport): ' . $e->getMessage());
                    }
                } else {
                    $picture = "";
                }

                $data_img = array('id' => $id, 'pathology_report' => $picture);
                $this->pathology_model->updateTestReport($data_img);
            }
            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
        }
        echo json_encode($array);
    }

    public function parameteraddvalue()
    {
        if (!$this->rbac->hasPrivilege('pathology_test', 'can_edit')) {
            access_denied();
        }

        $this->form_validation->set_rules('id', $this->lang->line('id'), 'required|xss_clean');
        $this->form_validation->set_rules('pathology_report', $this->lang->line('file'), 'callback_validateCanUploadFile[pathology_report]');

        if ($this->form_validation->run() == false) {
            $msg = array(
                'id' => form_error('id'),
                'pathology_report' => form_error('pathology_report'),
            );
            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {

            $id             = $this->input->post('id', TRUE);
            $reporting_date = $this->input->post("reporting_date", TRUE);
            $report_batch   = array(
                'id'                => $id,
                'patient_id'        => $this->input->post('patient_id_patho', TRUE),
                'consultant_doctor' => $this->input->post('consultant_doctor', TRUE),
                'reporting_date'    => $this->customlib->dateFormatToYYYYMMDD($reporting_date),
                'description'       => $this->input->post('description', TRUE),
                'apply_charge'      => $this->input->post('apply_charge', TRUE),
            );

            $parameter_id    = $this->input->post('parameter_id[]', TRUE);
            $parameter_value = $this->input->post('parameter_value[]', TRUE);
            $par_id          = $this->input->post('parid[]', TRUE);
            $pathology_id    = $this->input->post('pathologyid', TRUE);
            $update_id       = $this->input->post('update_id[]', TRUE);
            $preport_id      = $this->input->post('preport_id[]', TRUE);

            $i               = 0;
            $parameter_array = array();
            foreach ($update_id as $pkey => $pvalue) {
                $parameter_value_arr = array(
                    'id'                     => $pvalue,
                    'pathology_report_id'    => $preport_id[$i],
                    'pathology_report_value' => $parameter_value[$i],
                );

                $this->pathology_model->addparametervalue($parameter_value_arr);
                $i++;
            }

            if (!empty($_FILES['pathology_report']['name'])) {
                // SaaS: capture the existing report file for quota diff + cleanup.
                $saas_existing   = $this->pathology_model->getPathologyReportByID($id);
                $saas_old_report = (!empty($saas_existing['pathology_report'])) ? $saas_existing['pathology_report'] : '';
                $saas_old_dir    = (strpos($saas_old_report, '/') !== false) ? '' : 'uploads/pathology_report';
                $saas_old_kb     = (!empty($saas_old_report)) ? $this->media_storage->getUploadedFileSize($saas_old_report, $saas_old_dir) : 0;

                $config['upload_path']   = 'uploads/pathology_report/';
                $config['allowed_types'] = 'jpg|jpeg|png|pdf|doc|docx|xls|xlsx';
                $config['file_name']     = $_FILES['pathology_report']['name'];
                $fileInfo                = pathinfo($_FILES["pathology_report"]["name"]);
                $img_name                = $id . '.' . $fileInfo['extension'];

                $this->load->library('upload', $config);
                $this->upload->initialize($config);
                if (move_uploaded_file($_FILES["pathology_report"]["tmp_name"], "./uploads/pathology_report/" . $img_name)) {
                    // SaaS: delete the old file (now replaced) and adjust quota by diff.
                    if (!empty($saas_old_report) && $saas_old_report !== $img_name) {
                        $this->media_storage->filedelete($saas_old_report, $saas_old_dir);
                    }
                    try {
                        $saas_new_kb = $this->media_storage->getTmpFileSize('pathology_report');
                        if ($saas_old_kb > $saas_new_kb) {
                            $this->saasvalidation->deleteResouceQuota('storage', $saas_old_kb - $saas_new_kb);
                        } elseif ($saas_new_kb > $saas_old_kb) {
                            $this->saasvalidation->updateResouceQuota('storage', $saas_new_kb - $saas_old_kb);
                        }
                    } catch (Exception $e) {
                        log_message('error', 'SaaS storage quota update failed (pathology parameteraddvalue): ' . $e->getMessage());
                    }
                }

                $data_img = array('id' => $id, 'pathology_report' => $img_name);
                $this->pathology_model->updateTestReport($data_img);
            }

            $this->pathology_model->updateTestReport($report_batch);

            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
        }
        echo json_encode($array);
    }

    public function testReportBatch()
    {
        if (!$this->rbac->hasPrivilege('pathology_test', 'can_add')) {
            access_denied();
        }

        $this->form_validation->set_rules('patient_id', $this->lang->line('patient'), 'required|xss_clean');
        $this->form_validation->set_rules('pathology_id', $this->lang->line('pathology_id'), 'required|xss_clean');
        $this->form_validation->set_rules('apply_charge', $this->lang->line('applied_charge'), 'required|xss_clean');
        $this->form_validation->set_rules('pathology_report', $this->lang->line('file'), 'callback_handle_upload|callback_validateCanUploadFile[pathology_report]');
        $this->form_validation->set_rules('reporting_date', $this->lang->line('reporting_date'), 'required|xss_clean');
        if ($this->form_validation->run() == false) {
            $msg = array(
                'patient_id'       => form_error('patient_id'),
                'pathology_id'     => form_error('pathology_id'),
                'apply_charge'     => form_error('apply_charge'),
                'reporting_date'   => form_error('reporting_date'),
                'pathology_report' => form_error('pathology_report'),
            );
            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $id             = $this->input->post('pathology_id', TRUE);
            $patient_id     = $this->input->post('patient_id', TRUE);
            $reporting_date = $this->input->post("reporting_date", TRUE);

            $report_batch = array(
                'pathology_id'      => $id,
                'patient_id'        => $patient_id,
                'customer_type'     => $this->input->post('customer_type', TRUE),
                'patient_name'      => $this->input->post('patient_name', TRUE),
                'consultant_doctor' => $this->input->post('consultant_doctor', TRUE),
                'reporting_date'    => $this->customlib->dateFormatToYYYYMMDD($reporting_date),
                'description'       => $this->input->post('description', TRUE),
                'apply_charge'      => $this->input->post('apply_charge', TRUE),
                'generated_by'      => $this->customlib->getLoggedInUserID(),
                'pathology_report'  => '',
            );

            $insert_id = $this->pathology_model->testReportBatch($report_batch);

            if (isset($_FILES["pathology_report"]) && !empty($_FILES['pathology_report']['name'])) {
                $fileInfo = pathinfo($_FILES["pathology_report"]["name"]);
                $img_name = $insert_id . '.' . $fileInfo['extension'];
                if (move_uploaded_file($_FILES["pathology_report"]["tmp_name"], "./uploads/pathology_report/" . $img_name)) {
                    $data_img = array('id' => $insert_id, 'pathology_report' => $img_name);
                    $this->pathology_model->testReportBatch($data_img);

                    // SaaS: add the uploaded report's size to the storage quota usage.
                    try {
                        $this->saasvalidation->updateStorageLimit('storage', ['pathology_report']);
                    } catch (Exception $e) {
                        log_message('error', 'SaaS storage quota update failed (pathology testReportBatch): ' . $e->getMessage());
                    }
                }
            }

            $array = array('status' => 'success', 'id' => $insert_id, 'error' => '', 'message' => $this->lang->line('success_message'));
        }
        echo json_encode($array);
    }

    public function handle_upload_document()
    {
        $image_validate = $this->config->item('file_validate');

        if (isset($_FILES["document"]) && !empty($_FILES["document"]['name'])) {

            $file_type         = $_FILES["document"]['type'];
            $file_size         = $_FILES["document"]["size"];
            $file_name         = $_FILES["document"]["name"];
            $allowed_extension = $image_validate['allowed_extension'];
            $ext               = pathinfo($file_name, PATHINFO_EXTENSION);

            $allowed_mime_type = $image_validate['allowed_mime_type'];

            if ($files = @getimagesize($_FILES['document']['tmp_name'])) {
                if (!in_array($files['mime'], $allowed_mime_type)) {
                    $this->form_validation->set_message('handle_upload_document', $this->lang->line('file_type_not_allowed'));
                    return false;
                }
                if (!in_array(strtolower($ext), $allowed_extension) || !in_array($file_type, $allowed_mime_type)) {
                    $this->form_validation->set_message('handle_upload_document', $this->lang->line('file_extension_not_allowed'));
                    return false;
                }
                if ($file_size > $image_validate['upload_size']) {
                    $this->form_validation->set_message('handle_upload_document', $this->lang->line('file_size_shoud_be_less_than') . number_format($image_validate['upload_size'] / 1048576, 2) . " MB");
                    return false;
                }
            } else {
                $this->form_validation->set_message('handle_upload_document', $this->lang->line('file_type_extension_not_allowed'));
                return false;
            }

            return true;
        }
        return true;
    }

    public function handle_upload( )
    {
        $image_validate = $this->config->item('file_validate');       
        if (isset($_FILES["file"]) && !empty($_FILES["file"]['name'])) {           

            $file_type = $_FILES["file"]['type'];
            $file_size = $_FILES["file"]["size"];
            $file_name = $_FILES["file"]["name"];
            
            $allowed_extension = $image_validate['allowed_extension'];
            $ext               = pathinfo($file_name, PATHINFO_EXTENSION);
            $allowed_mime_type = $image_validate['allowed_mime_type'];

            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mtype = finfo_file($finfo, $_FILES['file']['tmp_name']);
            finfo_close($finfo);

            if (!in_array($mtype, $allowed_mime_type)) {
                $this->form_validation->set_message('handle_upload', $this->lang->line('file_type_not_allowed'));
                return false;
            }
            if (!in_array($ext, $allowed_extension) || !in_array($file_type, $allowed_mime_type)) {
                $this->form_validation->set_message('handle_upload', $this->lang->line('extension_not_allowed'));
                return false;
            }
            if ($file_size > $image_validate['upload_size']) {
                $this->form_validation->set_message('handle_upload', $this->lang->line('file_size_shoud_be_less_than') . number_format($image_validate['upload_size'] / 1048576, 2) . " MB");
                return false;
            }

            return true;
        } 
    } 

    public function gettestreportbatch()
    {
        if (!$this->rbac->hasPrivilege('pathology_bill', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'pathology');
        $id                  = $this->input->post("id");
        $patients            = $this->patient_model->getPatientListall();
        $data["patients"]    = $patients;
        $pathologist         = $this->staff_model->getStaffbyrole(5);
        $data["pathologist"] = $pathologist;
        $testlist            = $this->pathology_model->getpathotestDetails();
        $data["testlist"]    = $testlist;
        $data["bloodgroup"]  = $this->bloodbankstatus_model->get_product(null, 1);
        $data['fields']      = $this->customfield_model->get_custom_fields('pathology', 1);
        $data['organisation']   = $this->organisation_model->get();
        $data['module'] = 'pathology';
        $this->load->view('layout/header', $data);
        $this->load->view('admin/pathology/reportDetail', $data);
        $this->load->view('layout/footer', $data);
    }

    public function getpathologybillDatatable()
    {
        $dt_response = $this->pathology_model->getAllpathologybillRecord();
        $fields      = $this->customfield_model->get_custom_fields('pathology', 1);
        $dt_response = json_decode($dt_response);
        $dt_data     = array();
        if (!empty($dt_response->data)) {   
            foreach ($dt_response->data as $key => $value) {

                $row            = array();
                $balance_amount = ($value->net_amount) - ($value->paid_amount);
                //====================================

                $action = "<div class='rowoptionview rowview-mt-19'>";
                $action .= "<a href='javascript:void(0)'  data-loading-text='" . $this->lang->line('please_wait') . "' data-record-id='" . $value->id . "' class='btn btn-default btn-xs view_detail' data-bs-toggle='tooltip' title='" . $this->lang->line('view_reports') . "' ><i class='fa fa-reorder'></i></a>";
                if ($this->rbac->hasPrivilege('pathology_partial_payment', 'can_view')) {
                    $action .= " <a href='javascript:void(0)'  data-loading-text='" . $this->lang->line('please_wait') . "' data-record-id='" . $value->id . "' data-record-caseid='" . $value->case_reference_id . "' class='btn btn-default btn-xs add_payment' data-bs-toggle='tooltip' title='" . $this->lang->line('add_view_payments') . "' ><i class='fa fa-money'></i></a>";
                }
                if ($value->case_reference_id > 0) {
                    $case_id = $value->case_reference_id;
                } else {
                    $case_id = '';
                }

                $action .= "</div>";

                //==============================
                $row[] = $this->customlib->getSessionPrefixByType('pathology_billing') . $value->id;
                $row[] = $case_id;
                $row[] = $this->customlib->YYYYMMDDHisTodateFormat($value->date, $this->time_format);
                $row[] = $value->patient_name . " (" . $value->pid . ")";
                $row[] = composeStaffNameByString($value->generated_byname, $value->generated_bysurname, $value->generated_byemployee_id);
                $row[] = composeStaffNameByString($value->name, $value->surname, $value->employee_id);

                //====================
                if (!empty($fields)) {
                    foreach ($fields as $fields_key => $fields_value) {
                        $display_field = $value->{"$fields_value->name"};
                        if ($fields_value->type == "link") {
                            $display_field = "<a href=" . $value->{"$fields_value->name"} . " target='_blank'>" . $value->{"$fields_value->name"} . "</a>";

                        }
                        $row[] = $display_field;
                    }
                }
                //====================
				
				$row[]     = amountFormat($value->total);
				$discount_amt = ( $value->total * $value->discount_percentage ) / 100 ;	
                $row[]     = amountFormat($discount_amt) ." (". $value->discount_percentage ."%)"  ;		
				$tax_amt = (($value->total - $discount_amt) * $value->tax_percentage ) / 100;	

                if(($value->total - $discount_amt)==0){
                     $row[]     = $value->tax."(0.00%)";     
                }else{
                     $row[]     =   $value->tax ." (".amountFormat(($value->tax * 100) / ($value->total - $discount_amt), 2)."%)";  
                }
				$row[]     = amountFormat($value->net_amount, 2);
                $row[]     = amountFormat($value->paid_amount, 2);
                $row[]     = amountFormat($balance_amount, 2) . $action;
                $dt_data[] = $row;
            }
        }
        $json_data = array(
            "draw"            => intval($dt_response->draw),
            "recordsTotal"    => intval($dt_response->recordsTotal),
            "recordsFiltered" => intval($dt_response->recordsFiltered),
            "data"            => $dt_data,
        );
        echo json_encode($json_data);
    }

    public function getpathologytestDatatable()
    {
        $dt_response = $this->pathology_model->getAllpathologytest();
        $fields      = $this->customfield_model->get_custom_fields('pathologytest', 1);
        $dt_response = json_decode($dt_response);
        $dt_data     = array();
        if (!empty($dt_response->data)) {
            foreach ($dt_response->data as $key => $value) {

                $row = array();
                //====================================

                $action = "<div class='rowoptionview rowview-mt-19'>";

                if ($this->rbac->hasPrivilege('pathology_test', 'can_view')) {
                    $action .= "<a href='#' data-bs-toggle='tooltip' onclick='viewDetail(" . $value->id . ")'  class='btn btn-default btn-xs' title='" . $this->lang->line('show') . "' ><i class='fa fa-reorder'></i></a>";
                }
                $action .= "</div'>";

                //==============================
                $row[] = $value->test_name . "</a> ";
                $row[] = $value->short_name;
                $row[] = $value->test_type;
                $row[] = $value->category_name;
                $row[] = $value->sub_category;
                $row[] = $value->method;
                $row[] = $value->report_days;

                //====================
                if (!empty($fields)) {
                    foreach ($fields as $fields_key => $fields_value) {
                        $display_field = $value->{"$fields_value->name"};
                        if ($fields_value->type == "link") {
                            $display_field = "<a href=" . $value->{"$fields_value->name"} . " target='_blank'>" . $value->{"$fields_value->name"} . "</a>";
                        }
                        $row[] = $display_field;
                    }
                }
                //====================
                $row[]     = $value->percentage;
                $row[]     = $value->standard_charge;
                $row[]     = amountFormat($value->standard_charge + calculatePercent($value->standard_charge, $value->percentage));
                $row[]     = $action;
                $dt_data[] = $row;
            }
        }
        $json_data = array(
            "draw"            => intval($dt_response->draw),
            "recordsTotal"    => intval($dt_response->recordsTotal),
            "recordsFiltered" => intval($dt_response->recordsFiltered),
            "data"            => $dt_data,
        );
        echo json_encode($json_data);
    }

    public function getPatientPathologyDetails()
    {
        $is_bill    = $this->input->post('is_bill', TRUE);
        $id         = $this->input->post('id', TRUE);
        $data['id'] = $id;
        $result     = $this->pathology_model->getPathologyBillByID($id);
  
        if($result->ipd_prescription_basic_id != ''){
            $ipd_opd = $this->pharmacy_model->getIpdPrescriptionBasic($result->ipd_prescription_basic_id);            
            if(!empty($ipd_opd)){
                if($ipd_opd->ipd_id != ''){             
                $data['prescription']   =   $this->customlib->getSessionPrefixByType('ipd_prescription').$ipd_opd->id;  
            }else{
                $data['prescription']   =   $this->customlib->getSessionPrefixByType('opd_prescription').$ipd_opd->id ; 
            } 
            }else{
                $data['prescription']   = '';
            }       
                   
        }else{
            $data['prescription']   = '';
        }
        
        $data['result'] = $result;
        if (isset($is_bill)) {
            $data['is_bill'] = false;
        } else {
            $data['is_bill'] = true;
        }
        $data['bill_prefix']           = $this->customlib->getSessionPrefixByType('pathology_billing');
        $data['fields']                = $this->customfield_model->get_custom_fields('pathology');
        $data['pathology_test_fields'] = $this->customfield_model->get_custom_fields('pathologytest');
       
        $page                          = $this->load->view('admin/pathology/_getPatientPathologyDetails', $data, true);
        $actions                       = "";

        if (isset($is_bill)) {
            $actions .= "<a href='javascript:void(0)' data-loading-text='<i class=\"fa fa-circle-o-notch fa-spin\"></i>' class='print_pathology_bill btn btn-sm btn-light' data-bs-toggle='tooltip' data-bs-placement='bottom'  data-record-id=\"" . $id . "\"   title='" . $this->lang->line('print_bill') . "'><i class='fa fa-print'></i></a>";
        } else {
            $actions .= "<a href='javascript:void(0)' data-loading-text='<i class=\"fa fa-circle-o-notch fa-spin\"></i>' class='print_parameter btn btn-sm btn-light' data-bs-toggle='tooltip' data-bs-placement='bottom'  data-record-id=\"" . $id . "\"   title='" . $this->lang->line('print_test_report') . "'><i class='fa fa-reorder'></i></a>";
            $actions .= "<a href='javascript:void(0)' data-loading-text='<i class=\"fa fa-circle-o-notch fa-spin\"></i>' class='print_bill btn btn-sm btn-light' data-bs-toggle='tooltip' data-record-id=\"" . $id . "\"  data-bs-placement='bottom'  title='" . $this->lang->line('print_bill') . "'><i class='fa fa-print'></i></a>";
            if ($this->rbac->hasPrivilege('pathology_bill', 'can_edit')) {
                $actions .= "<a href='javascript:void(0)' data-loading-text='<i class=\"fa fa-circle-o-notch fa-spin\"></i>' class='edit_pathology btn btn-sm btn-light' data-bs-toggle='tooltip' data-bs-placement='bottom' data-record-id=\"" . $id . "\"   title='" . $this->lang->line('edit_pathology') . "'><i class='fa fa-pencil'></i></a>";
            }if ($this->rbac->hasPrivilege('pathology_bill', 'can_delete')) {
                $actions .= "<a href='javascript:void(0)' data-loading-text='<i class=\"fa fa-circle-o-notch fa-spin\"></i>' class='delete_pathology btn btn-sm btn-light' data-bs-toggle='tooltip' data-bs-placement='bottom' data-record-id=\"" . $id . "\"   title='" . $this->lang->line('delete_pathology') . "'><i class='fa fa-trash'></i></a>";
            }
        }

        echo json_encode(array('status' => 1, 'page' => $page, 'actions' => $actions));
    }

    public function getBillDetails()
    {
        $print_details         = $this->printing_model->get('', 'pathology');
        $data['print_details'] = $print_details;
        $id                    = $this->input->post('id', TRUE);
		$print   				= $this->input->get('print');
		 
        $data['id']            = $id;		
		
        if (isset($print)) {
            $data["print"] = 'yes';
        } else {
            $data["print"] = 'no';
        }

        $result = $this->pathology_model->getPathologyBillByID($id);
     
        if($result->ipd_prescription_basic_id != ''){
            $ipd_opd = $this->pharmacy_model->getIpdPrescriptionBasic($result->ipd_prescription_basic_id);          
           
            if(!empty($ipd_opd)){
                if($ipd_opd->ipd_id != ''){             
                    $data['prescription']   =   $this->customlib->getSessionPrefixByType('ipd_no').$ipd_opd->ipd_id;  
                }else{
                    $data['prescription']   =   $this->customlib->getSessionPrefixByType('opd_no').$ipd_opd->visit_details_id ; 
                } 
            }else{
                $data['prescription']   = '';
            }
            
        }else{
            $data['prescription']   = '';
        }

        $data['fields']      = $this->customfield_model->get_custom_fields('pathology', '', 1);
        $data['bill_prefix'] = $this->customlib->getSessionPrefixByType('pathology_billing');
        $data['result'] = $result;
        $page = $this->load->view('admin/pathology/_getBillDetails', $data, true);
        echo json_encode(array('status' => 1, 'page' => $page));
    }

    public function getPathologyReportDetail()
    {
        $id             = $this->input->post('id', TRUE);
        $data['id']     = $id;
        $staff          = $this->staff_model->getStaffbyrole(5);
        $data["staff"]  = $staff;
        $result         = $this->pathology_model->getPatientPathologyReportDetails($id);
        $data['result'] = $result;
        $page = $this->load->view('admin/pathology/_getPathologyReportDetails', $data, true);
        echo json_encode(array('status' => 1, 'page' => $page));
    }

    public function assigntestpatho()
    {
        $id                   = $this->input->post('id', TRUE);
        $testlist             = $this->pathology_model->getpathotestDetails();
        $data["testlist"]     = $testlist;
        $patients             = $this->patient_model->getPatientListall();
        $data["patients"]     = $patients;
        $doctors              = $this->staff_model->getStaffbyrole(3);
        $data["doctors"]      = $doctors;
        $data["payment_mode"] = $this->payment_mode;
        $page                 = $this->load->view("admin/pathology/_assigntestpatho", $data, true);
        $result               = $this->pathology_model->getBillNo();
        $id                   = $result["id"];
        if (!empty($result["id"])) {
            $bill_no = $id + 1;
        } else {
            $bill_no = 1;
        }

        echo json_encode(array('status' => 1, 'page' => $page, 'bill_no' => $this->customlib->getSessionPrefixByType('pathology_billing') . $bill_no, 'total_rows' => 1));
    }

    public function editpathology()
    {
        $id                     = $this->input->post('id', TRUE);
        $pathology_data         = $this->pathology_model->getPathologyBillByID($id);		
        $data["pathology_data"] = $pathology_data;
        $testlist                    = $this->pathology_model->getpathotestDetails();
        $data["testlist"]            = $testlist;
        $patients                    = $this->patient_model->getPatientListall();
        $data["patients"]            = $patients;
        $patient_names               = array_column($patients, 'patient_name', 'id');
        $doctors                     = $this->staff_model->getStaffbyrole(3);
        $data['custom_fields_value'] = display_custom_fields('pathology', $id);
        $data["doctors"]             = $doctors;
        $data["payment_mode"]        = $this->payment_mode;               
        $page                        = $this->load->view("admin/pathology/_editpathology", $data, true);        
        $total_rows                  = count($pathology_data->pathology_report);
        $case_reference_id           = $pathology_data->case_reference_id;
        $patient_id                  = $pathology_data->patient_id;
        $date                        = $pathology_data->date;
        $bill_no                     = $pathology_data->id;		
        $organisation_id             = $pathology_data->organisation_id;

        if($pathology_data->ipd_prescription_basic_id != ''){
            $ipd_opd = $this->pharmacy_model->getIpdPrescriptionBasic($pathology_data->ipd_prescription_basic_id);            
            if(!empty($ipd_opd)){
                if($ipd_opd->ipd_id != ''){             
                $ipd_prescription_basic_id   =   $this->customlib->getSessionPrefixByType('ipd_prescription').$ipd_opd->id;  
            }else{
                $ipd_prescription_basic_id   =   $this->customlib->getSessionPrefixByType('opd_prescription').$ipd_opd->id ; 
            } 
            }else{
                $ipd_prescription_basic_id   = '';
            }       
                   
        }else{
            $ipd_prescription_basic_id   = '';
        }

        if($organisation_id!=null || $organisation_id!=0 || $organisation_id!=""){
            $tpa_apply_status=1;
        }else{
            $tpa_apply_status=0;
        }

        echo json_encode(array('status' => 1, 'page' => $page, 'bill_prefix' => $this->customlib->getSessionPrefixByType('pathology_billing'), 'bill_no' => $bill_no, 'pathology_date' => $date, 'total_rows' => $total_rows, 'case_reference_id' => $case_reference_id, 'patient_id' => $patient_id, 'patient_name' => ($patient_names[$patient_id] ?? '') . " (" . $patient_id . ")",'tpa_apply_status'=>$tpa_apply_status,'ipd_prescription_basic_id'=>$ipd_prescription_basic_id));
    }

    public function getReportDetails($id, $parameter_id)
    {
        $print_details         = $this->printing_model->get('', 'pathology');
        $data['print_details'] = $print_details;
        $data['id']            = $id;
        if (isset($_POST['print'])) {
            $data["print"] = 'yes';
        } else {
            $data["print"] = 'no';
        }

        $result                   = $this->pathology_model->getBillDetails($id);
        $data['result']           = $result;
        $detail                   = $this->pathology_model->getAllBillDetails($id);
        $data['detail']           = $detail;
        $parametername            = $this->pathology_category_model->getpathoparameter();
        $data["parametername"]    = $parametername;
        $parameterdetails         = $this->pathology_category_model->getparameterDetailsforpatient($id);
        $data['parameterdetails'] = $parameterdetails;

        $this->load->view('admin/pathology/printReport', $data);
    }

    public function deleteTestReport($id)
    {
        if (!$this->rbac->hasPrivilege('pathology_test', 'can_delete')) {
            access_denied();
        }
        // SaaS: release the test report document's storage from the quota and remove the file
        // before the report row is deleted. The `pathology_report` column may hold either a bare
        // filename (testReportBatch add) or a full relative path (savetestreport edit) — the dir
        // resolution below handles both.
        $saas_report = $this->pathology_model->getPathologyReportByID($id);
        if (!empty($saas_report) && !empty($saas_report['pathology_report'])) {
            $report_doc = $saas_report['pathology_report'];
            $report_dir = (strpos($report_doc, '/') !== false) ? '' : 'uploads/pathology_report';
            $report_kb  = $this->media_storage->getUploadedFileSize($report_doc, $report_dir);
            if ($report_kb > 0) {
                try {
                    $this->saasvalidation->deleteResouceQuota('storage', $report_kb);
                } catch (Exception $e) {
                    log_message('error', 'SaaS storage quota release failed (pathology deleteTestReport id=' . $id . '): ' . $e->getMessage());
                }
            }
            $this->media_storage->filedelete($report_doc, $report_dir);
        }

        $this->pathology_model->deleteTestReport($id);
    }

    public function pathologyReport()
    {
        if (!$this->rbac->hasPrivilege('pathology_test', 'can_view')) {
            access_denied();
        }

        $this->session->set_userdata('top_menu', 'Reports');
        $this->session->set_userdata('sub_menu', 'reports/pathology');
        $this->session->set_userdata('subsub_menu', 'reports/pathology/pathologyreport');

        $custom_fields = $this->customfield_model->get_custom_fields('pathology', '', '', 1);
        $staffsearch          = $this->patient_model->getstaffbytransactionbill();
        $data['staffsearch']  = $staffsearch;
        $data["searchlist"]   = $this->search_type;
        $data['fields']       = $custom_fields;
        $data["testlist"]     = $this->pathology_model->getpathotestDetails();
        $pathologist          = $this->staff_model->getStaffbyrole(5);
        $data["pathologist"]  = $pathologist;
        $categoryName         = $this->pathology_category_model->getcategoryName();
        $data["categoryName"] = $categoryName;
        $data['module'] = 'pathology';
        $this->load->view('layout/header', $data);
        $this->load->view('admin/pathology/pathologyReport', $data);
        $this->load->view('layout/footer', $data);
    }

    public function checkvalidation()
    {
        $search = $this->input->post('search', TRUE);
        $this->form_validation->set_rules('search_type', $this->lang->line('search_type'), 'trim|required|xss_clean');

        if ($this->form_validation->run() == false) {
            $msg = array(
                'search_type' => form_error('search_type'),
            );
            $json_array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $param = array(
                'search_type'           => $this->input->post('search_type', TRUE),
                'collect_staff'         => $this->input->post('collect_staff', TRUE),
                'date_from'             => $this->input->post('date_from', TRUE),
                'date_to'               => $this->input->post('date_to', TRUE),
                'test_name'             => $this->input->post('test_name', TRUE),
                'pathology_category_id' => $this->input->post('pathology_category_id', TRUE),
            );

            $json_array = array('status' => 'success', 'error' => '', 'param' => $param, 'message' => $this->lang->line('success_message'));
        }
        echo json_encode($json_array);
    }

    public function pathologyreports()
    {
        $search['search_type']   = $this->input->post('search_type', TRUE);
        $search['collect_staff'] = $this->input->post('collect_staff', TRUE);
        $search['date_from']     = $this->input->post('date_from', TRUE);
        $search['date_to']       = $this->input->post('date_to', TRUE);
        $start_date              = '';
        $end_date                = '';

        $currency_symbol = $this->customlib->getHospitalCurrencyFormat();
        $condition['pathology_category_id'] = $this->input->post('pathology_category_id', TRUE);
        $condition['collect_staff'] = $this->input->post('collect_staff', TRUE);
        $condition['test_name']     = $this->input->post('test_name', TRUE);
        $fields = $this->customfield_model->get_custom_fields('pathology', '', '', 1);
        if ($search['search_type'] == 'period') {

            $condition['start_date'] = $this->customlib->dateFormatToYYYYMMDD($search['date_from']);
            $condition['end_date']   = $this->customlib->dateFormatToYYYYMMDD($search['date_to']);

        } else {
            if (isset($search['search_type']) && $search['search_type'] != '') {
                $dates               = $this->customlib->get_betweendate($search['search_type']);
                $data['search_type'] = $search['search_type'];
            } else {
                $dates               = $this->customlib->get_betweendate('this_year');
                $data['search_type'] = '';
            }
            $condition['start_date'] = $dates['from_date'];
            $condition['end_date']   = $dates['to_date'];
        }

        $reportdata = $this->transaction_model->pathologybillreportsRecord($condition);
       
        $reportdata    = json_decode($reportdata);
        $dt_data       = array();
        $total_balance = 0;
        $total_charge  = 0;
        $total_net_amount=0;
        $total_amount=0;

        if (!empty($reportdata->data)) {
             
            foreach ($reportdata->data as $key => $value) {               
                
				$apply_discount = ($value->apply_charge * $value->discount_percentage ) /100 ;
				$apply_amt = $value->apply_charge - $apply_discount;
				$apply_tax	=	(( $apply_amt * $value->tax_percentage ) / 100);				
                $apply_charge = $apply_tax + $apply_amt  ;
                $total_charge += $apply_charge;
                
                if (!empty($value->patient_id)) {
                    $patient_id = " (" . $value->patient_id . ")";
                } else {
                    $patient_id = "";
                }                

                $row   = array();
                $row[] = $this->customlib->getSessionPrefixByType($value->module_no) . $value->module_id;
                
                if($value->payment_date){
                    $row[] = $this->customlib->YYYYMMDDTodateFormat($value->payment_date);
                }else{
                    $row[] = '';
                }
                
                $row[] = composePatientName($value->patient_name, $value->patient_id);
                $row[] = $value->category_name;
                $row[] = $value->test_name . " (" . $value->short_name . ")";
                $row[] = $value->doctor_name;
                $row[] = composeStaffNameByString($value->name, $value->surname, $value->employee_id);
                //====================
                if (!empty($fields)) {
                    foreach ($fields as $fields_key => $fields_value) {
                        $display_field = $value->{"$fields_value->name"};
                        if ($fields_value->type == "link") {
                            $display_field = "<a href=" . $value->{"$fields_value->name"} . " target='_blank'>" . $value->{"$fields_value->name"} . "</a>";
                        }
                        $row[] = $display_field;
                    }
                }
                //====================
                $row[] = amountFormat($apply_charge);
                
                $dt_data[] = $row;
            }

            $footer_row   = array();
            $footer_row[] = "";
            $footer_row[] = "";
            $footer_row[] = "";
            $footer_row[] = "";
            $footer_row[] = "";
            $footer_row[] = "";
            
            if (!empty($fields)) {
                    foreach ($fields as $fields_key => $fields_value) {
                         
                        $footer_row[] = "";
                    }
                }
                
            $footer_row[] = "<b>" . $this->lang->line('total_amount') . "</b>" . ':';
            $footer_row[] = "<b>" . $currency_symbol . (number_format($total_charge, 2, '.', '')) . "<br/>";  
            $dt_data[]    = $footer_row;
        }

        $json_data = array(
            "draw"            => intval($reportdata->draw),
            "recordsTotal"    => intval($reportdata->recordsTotal),
            "recordsFiltered" => intval($reportdata->recordsFiltered),
            "data"            => $dt_data,
        );
        echo json_encode($json_data);
    }

    public function getPathologyTransaction()
    {
        $pathology_billing_id  = $this->input->post('id', TRUE);
        $pathology_transaction = $this->transaction_model->pathologyPayments($pathology_billing_id);
        $is_bill               = $this->input->post('is_bill', TRUE);
        if (isset($is_bill)) {
            $data['is_bill'] = true;
            $data['form_id'] = "add_pathopartial_payment";
        } else {
            $data['is_bill'] = false;
            $data['form_id'] = "add_partial_payment";
        }
        $data["pathology_billing_id"]    = $pathology_billing_id;
        $data["payment_mode"]            = $this->payment_mode;
        $data['pathology_transaction']   = $pathology_transaction;
        $pathology_billing               = $this->pathology_model->getPathologyBillByID($pathology_billing_id);
		 
        $data['pathology_billing']       = $pathology_billing;
        $data['pathology_total_payment'] = $this->transaction_model->pathologyTotalPayments($pathology_billing_id)->total_paid;
 
        $page = $this->load->view("admin/pathology/_getPathologyTransaction", $data, true);
        echo json_encode(array('status' => 1, 'page' => $page));
    }

    public function partialbill()
    {
        if (!$this->rbac->hasPrivilege('pathology_partial_payment', 'can_add')) {
            access_denied();
        }

        $this->form_validation->set_rules('payment_date', $this->lang->line('date'), 'required|xss_clean');
        $this->form_validation->set_rules('amount', $this->lang->line('amount'), 'required|valid_amount|xss_clean');
        $this->form_validation->set_rules('payment_mode', $this->lang->line('payment_mode'), 'required|xss_clean');
        if ($this->input->post('payment_mode', TRUE) == "Cheque") {
            $this->form_validation->set_rules('cheque_no', $this->lang->line('cheque_no'), 'required|xss_clean');
            $this->form_validation->set_rules('cheque_date', $this->lang->line('cheque_date'), 'required|xss_clean');
            $this->form_validation->set_rules('document', $this->lang->line('document'), 'callback_handle_upload_document|callback_validateCanUploadFile[document]');
        }

        if ($this->form_validation->run() == false) {
            $msg = array(
                'payment_date' => form_error('payment_date'),
                'amount'       => form_error('amount'),
                'payment_mode' => form_error('payment_mode'),
                'cheque_no'    => form_error('cheque_no'),
                'cheque_date'  => form_error('cheque_date'),
                'document'     => form_error('document'),
            );

            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $pathology_billing_id     = $this->input->post('pathology_billing_id', TRUE);
            $pathology_billing_detail = $this->transaction_model->pathologyTotalPayments($pathology_billing_id);
            $net_amount               = $pathology_billing_detail->net_amount;
            $amount_paying            = $this->input->post('amount', TRUE);
            $total_paid               = $pathology_billing_detail->total_paid;

            if ($net_amount >= ($total_paid + $amount_paying)) {
                $picture         = "";
                $bill_date       = $this->input->post("payment_date", TRUE);
                $payment_section = $this->config->item('payment_section');
                $payment_array   = array(
                    'amount'               => $this->input->post('amount', TRUE),
                    'type'                 => 'payment',
                    'patient_id'           => $this->input->post('patient_id', TRUE),
                    'section'              => $payment_section['pathology'],
                    'pathology_billing_id' => $this->input->post('pathology_billing_id', TRUE),
                    'payment_mode'         => $this->input->post('payment_mode', TRUE),
                    'note'                 => $this->input->post('note', TRUE),
                    'payment_date'         => $this->customlib->dateFormatToYYYYMMDDHis($bill_date, $this->customlib->getHospitalTimeFormat()),
                    'received_by'          => $this->customlib->getLoggedInUserID(),
                );

                if (!empty($this->input->post('case_reference_id', TRUE)) && $this->input->post('case_reference_id', TRUE) != "") {
                    $payment_array['case_reference_id'] = $this->input->post('case_reference_id', TRUE);
                }

                $attachment      = "";
                $attachment_name = "";

                $cheque_date = $this->input->post("cheque_date", TRUE);
                if ($this->input->post('payment_mode', TRUE) == "Cheque") {

                    $payment_array['cheque_date'] = $this->customlib->dateFormatToYYYYMMDD($cheque_date);
                    $payment_array['cheque_no']   = $this->input->post('cheque_no', TRUE);

                    if (isset($_FILES["document"]) && !empty($_FILES['document']['name'])) {
                        $file_name = $this->media_storage->fileupload("document",'./uploads/payment_document/');
                        $payment_array['attachment']      = $file_name;
                        $payment_array['attachment_name'] = $_FILES["document"]["name"];

                        // SaaS: add the uploaded document's size to the storage quota usage.
                        try {
                            $this->saasvalidation->updateStorageLimit('storage', ['document']);
                        } catch (Exception $e) {
                            log_message('error', 'SaaS storage quota update failed (pathology partialbill): ' . $e->getMessage());
                        }
                    }
                }

                $this->transaction_model->add($payment_array);
                $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
            } else {
                $array = array('status' => 'fail', 'error' => array('amount_invalid' => $this->lang->line('amount_should_not_be_greater_than_balance') . ' ' . amountFormat($net_amount - $total_paid)), 'message' => '');
            }
        }
        echo json_encode($array);
    }

    public function printTransaction()
    {
        $print_details         = $this->printing_model->get('', 'paymentreceipt');
        $id                    = $this->input->post('id', TRUE);
        $charge                = array();
        $transaction           = $this->transaction_model->pathologyPaymentByTransactionId($id);
        $data['transaction']   = $transaction;
        $data['print_details'] = $print_details;
        $page                  = $this->load->view('admin/pathology/_printTransaction', $data, true);
        echo json_encode(array('status' => 1, 'page' => $page));
    }

    public function printPatientReportDetail()
    {
        $print_details         = $this->printing_model->get('', 'pathology');
        $data['print_details'] = $print_details;
        $id                    = $this->input->post('id', TRUE);
        $data['id']            = $id;
        $result                = $this->pathology_model->getPatientPathologyReportDetails($id);
        $data['result']        = $result;
        $page                  = $this->load->view('admin/pathology/_printPatientReportDetail', $data, true);
        echo json_encode(array('status' => 1, 'page' => $page));
    }

    public function getReportCollectionDetail()
    {
        $id     = $this->input->post('id', TRUE);
        $charge = array();
        $pathology_center = $this->lang->line('in_house_pathology_lab');
        $report           = $this->pathology_model->getPathologyReportByID($id);
        if (!empty($report)) {
            if ($report['pathology_center'] == "") {
                $report['pathology_center'] = $pathology_center;
            }
            if ($report['collection_date'] == "") {
                $report['collection_date'] = date('Y-m-d');
            }
        }
        echo json_encode(array('status' => 1, 'report' => $report));
    }

    public function updatecollection()
    {
        $this->form_validation->set_rules('pathology_report_id', $this->lang->line('report_id'), 'required|xss_clean');
        $this->form_validation->set_rules('collected_by', $this->lang->line('sample_collected_person_name'), 'required|xss_clean');
        $this->form_validation->set_rules('collected_date', $this->lang->line('collected_date'), 'required|xss_clean');
        $this->form_validation->set_rules('pathology_center', $this->lang->line('pathology_center'), 'required|xss_clean');

        if ($this->form_validation->run() == false) {
            $msg = array(
                'pathology_report_id' => form_error('pathology_report_id'),
                'collected_by'        => form_error('collected_by'),
                'collected_date'      => form_error('collected_date'),
                'pathology_center'    => form_error('pathology_center'),
            );
            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $doctor_id     = "";
            $doctor_name   = "";
            $payment_array = array(
                'id'                    => $this->input->post('pathology_report_id', TRUE),
                'collection_specialist' => $this->input->post('collected_by', TRUE),
                'pathology_center'      => $this->input->post('pathology_center', TRUE),
                'collection_date'       => $this->customlib->dateFormatToYYYYMMDD($this->input->post('collected_date', TRUE)),
            );
            $this->pathology_model->updateTestReport($payment_array);
            $reportdetails = $this->pathology_model->getPathologyReportByID($this->input->post('pathology_report_id', TRUE));

            $test_detail                  = $this->notificationsetting_model->getPathologyBillReportByID($this->input->post('pathology_bill_id', TRUE));
            $sample_collected_person_name = $this->notificationsetting_model->getstaffDetails($this->input->post('collected_by', TRUE));
            
            $event_data = array(
                'patient_id'                   => $reportdetails['patient_id'],
                'case_id'                      => $test_detail['case_reference_id'],
                'bill_no'                      => $this->customlib->getSessionPrefixByType('pathology_billing') . $reportdetails['pathology_bill_id'],
                'collected_date'               => $this->customlib->YYYYMMDDTodateFormat($this->input->post('collected_date', TRUE)),
                'test_name'                    => $test_detail['test_name'],
                'sample_collected_person_name' => composeStaffNameByString($sample_collected_person_name['name'], $sample_collected_person_name['surname'], $sample_collected_person_name['employee_id']),
                'pathology_center'             => $this->input->post('pathology_center', TRUE),
                'expected_date'                => $this->customlib->YYYYMMDDTodateFormat($test_detail['reporting_date']),
            );
            if ($this->input->post('collected_by', TRUE) != "") {

                $event_data['doctor_id']   = $this->input->post('collected_by', TRUE);
                $event_data['doctor_name'] = composeStaffNameByString($sample_collected_person_name['name'], $sample_collected_person_name['surname'], $sample_collected_person_name['employee_id']);
                $event_data['role_id']     = $sample_collected_person_name['role_id'];
            }

            $this->system_notification->send_system_notification('pathology_sample_collection', $event_data);

            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
        }
        echo json_encode($array);
    }

    public function updatereportparam()
    {
        $this->form_validation->set_rules('pathology_report_id', $this->lang->line('report_id'), 'required|xss_clean');
        $this->form_validation->set_rules('approved_by', $this->lang->line('approved_by'), 'required|xss_clean');
        $this->form_validation->set_rules('approve_date', $this->lang->line('approve_date'), 'required|xss_clean');
        $this->form_validation->set_rules('file', $this->lang->line('documents'), 'callback_handle_upload|callback_validateCanUploadFile[file]');

        $rows = $this->input->post('pathology_parameterdetails', TRUE);
        if (isset($rows) && !empty($rows)) {
            foreach ($rows as $row_key => $row_value) {
                $input_fields              = $this->input->post('pathology_parameter_' . $row_value, TRUE);
                $pathology_reference_range = $this->input->post('pathology_reference_range_' . $row_value, TRUE);
                if ($input_fields == "") {
                    $this->form_validation->set_rules('pathology_parameter', $this->lang->line('pathology_parameter'), 'required|xss_clean');
                }               
            }
        }

        if ($this->form_validation->run() == false) {
            $msg = array(
                'pathology_report_id'         => form_error('pathology_report_id'),
                'approved_by'                 => form_error('approved_by'),
                'approve_date'                => form_error('approve_date'),
                'file'                        => form_error('file'),
                'pathology_parameter'         => form_error('pathology_parameter'),
                'pathology_parameter_invalid' => form_error('pathology_parameter_invalid'),
            );
            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {

            $insert_array        = array();
            $update_array        = array();
            $approved_by         = $this->input->post('approved_by', TRUE);
            $rows                = $this->input->post('pathology_parameterdetails', TRUE);
            $pathology_report_id = $this->input->post('pathology_report_id', TRUE);
            $test_result         = $this->input->post('test_result', TRUE);

            $approve_date = $this->customlib->dateFormatToYYYYMMDD($this->input->post('approve_date', TRUE));
            if (!empty($rows)) {
                foreach ($rows as $row_key => $row_value) {
                    $prev_id = $this->input->post('prev_id_' . $row_value, TRUE);
                    if ($prev_id == 0) {
                        $insert_array[] = array(
                            'pathology_report_id'          => $pathology_report_id,
                            'pathology_parameterdetail_id' => $row_value,
                            'pathology_report_value'       => $this->input->post('pathology_parameter_' . $row_value, TRUE),
                        );
                    } else {
                        $update_array[] = array(
                            'id'                           => $prev_id,
                            'pathology_report_id'          => $pathology_report_id,
                            'pathology_parameterdetail_id' => $row_value,
                            'pathology_report_value'       => $this->input->post('pathology_parameter_' . $row_value, TRUE),
                        );
                    }
                }
                $delete_array = array();
                $this->pathology_model->addParameterforPatient($pathology_report_id, $approved_by, $approve_date, $insert_array, $update_array, $delete_array, $test_result);
            }

            //==========
            if (isset($_FILES["file"]) && !empty($_FILES['file']['name'])) {
                // SaaS: capture the existing report file for quota diff + cleanup.
                $saas_existing   = $this->pathology_model->getPathologyReportByID($pathology_report_id);
                $saas_old_report = (!empty($saas_existing['pathology_report'])) ? $saas_existing['pathology_report'] : '';
                $saas_old_dir    = (strpos($saas_old_report, '/') !== false) ? '' : 'uploads/pathology_report';
                $saas_old_kb     = (!empty($saas_old_report)) ? $this->media_storage->getUploadedFileSize($saas_old_report, $saas_old_dir) : 0;

                $file_name = $this->media_storage->fileupload("file",'./uploads/pathology_report/');
                if (!IsNullOrEmptyString($file_name)) {
                    if (!empty($saas_old_report)) {
                        $this->media_storage->filedelete($saas_old_report, $saas_old_dir);
                    }
                    $data_img = array('id' => $pathology_report_id, 'pathology_report' => 'uploads/pathology_report/' . $file_name, 'report_name' =>$_FILES["file"]['name']);
                    $this->pathology_model->updateTestReport($data_img);

                    // SaaS: adjust storage quota by the size difference (new vs replaced).
                    try {
                        $saas_new_kb = $this->media_storage->getTmpFileSize('file');
                        if ($saas_old_kb > $saas_new_kb) {
                            $this->saasvalidation->deleteResouceQuota('storage', $saas_old_kb - $saas_new_kb);
                        } elseif ($saas_new_kb > $saas_old_kb) {
                            $this->saasvalidation->updateResouceQuota('storage', $saas_new_kb - $saas_old_kb);
                        }
                    } catch (Exception $e) {
                        log_message('error', 'SaaS storage quota update failed (pathology report): ' . $e->getMessage());
                    }
                }
            }
            //==========

            $reportdetails                = $this->pathology_model->getPathologyReportByID($this->input->post('pathology_report_id', TRUE));
            $test_detail                  = $this->notificationsetting_model->getPathologyBillReportByID($this->input->post('pathology_bill_id', TRUE));
            $approved_by                  = $this->notificationsetting_model->getstaffDetails($this->input->post('approved_by', TRUE));            
            $sample_collected_person_name = $this->notificationsetting_model->getstaffDetails($this->input->post('collected_id', TRUE));

            $event_data = array(
                'patient_id'                   => $reportdetails['patient_id'],
                'case_id'                      => $test_detail['case_reference_id'],
                'bill_no'                      => $this->customlib->getSessionPrefixByType('pathology_billing') . $reportdetails['pathology_bill_id'],
                'collected_date'               => $this->input->post('collected_date', TRUE),
                'test_name'                    => $test_detail['test_name'],
                'doctor_id'                    => $this->input->post('approved_by', TRUE),
                'doctor_name'                  => $this->input->post('collected_by', TRUE),
                'sample_collected_person_name' => $this->input->post('collected_by', TRUE),
                'pathology_center'             => $this->input->post('pathalogy_center', TRUE),
                'expected_date'                => $this->customlib->YYYYMMDDTodateFormat($test_detail['reporting_date']),
                'approved_by'                  => composeStaffNameByString($approved_by['name'], $approved_by['surname'], $approved_by['employee_id']),
                'approve_date'                 => $this->customlib->YYYYMMDDTodateFormat($approve_date),
                'role_id'                      => $sample_collected_person_name['role_id'],
            );

            $this->system_notification->send_system_notification('pathology_test_report', $event_data);
            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
        }
        echo json_encode($array);
    }

    /**
     * SaaS storage pre-check (form_validation callback).
     * Returns false (blocking the save) when the upload would push the
     * tenant over its storage quota. SaasValidation sets the error message.
     */
    public function validateCanUploadFile($str, $params_string)
    {
        $storage_array = array_map('trim', explode(',', $params_string));
        return $this->saasvalidation->validateCanUploadFile($str, $storage_array);
    }

    /**
     * SaaS count pre-check (form_validation callback).
     * Param format "resource_name,quantity" (e.g. "no_of_pathology,1").
     * Returns false (blocking the save) when adding the records would push
     * the tenant over its allowed count. SaasValidation sets the error message.
     */
    public function validateCanAddNewResource($input, $resource_name)
    {
        list($resource_name, $quantity) = explode(',', $resource_name);
        return $this->saasvalidation->validateCanAddNewResource($input, $resource_name, $quantity);
    }

    public function downloadReport($report_id)
    {
        $report = $this->pathology_model->getPatientPathologyReportDetails($report_id);
        $this->media_storage->filedownload($report->pathology_report,'./');
    }

    /**
     * This function is used to validate document for upload
     **/
    public function handle_doc_upload($str, $var)
    {
        $image_validate = $this->config->item('file_validate');
        if (isset($_FILES[$var]) && !empty($_FILES[$var]['name'])) {

            $file_type = $_FILES[$var]['type'];
            $file_size = $_FILES[$var]["size"];
            $file_name = $_FILES[$var]["name"];

            $allowed_extension = $image_validate["allowed_extension"];
            $allowed_mime_type = $image_validate["allowed_mime_type"];
            $ext               = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

            if ($files = filesize($_FILES[$var]['tmp_name'])) {
                if (!in_array($file_type, $allowed_mime_type)) {
                    $this->form_validation->set_message('handle_doc_upload', $this->lang->line('file_type_extension_error_uploading_document'));
                    return false;
                }

                if (!in_array($ext, $allowed_extension) || !in_array($file_type, $allowed_mime_type)) {
                    $this->form_validation->set_message('handle_doc_upload', $this->lang->line('extension_error_while_uploading_document'));
                    return false;
                }
				
                if ($file_size > 2097152) {
                    $this->form_validation->set_message('handle_doc_upload', $this->lang->line('file_size_shoud_be_less_than') . "2MB");
                    return false;
                }
				
            } else {
                $this->form_validation->set_message('handle_doc_upload', $this->lang->line('error_while_uploading_document'));
                return false;
            }
            return true;
        }
        return true;
    }

    public function printtestparameterdetail()
    {
        $print_details         = $this->printing_model->get('', 'pathology');
        $data['print_details'] = $print_details;
        $id                    = $this->input->post('id', TRUE);
        $data['id']            = $id;
        $result                = $this->pathology_model->gettestparameterdetails($id);
        $data['head_result']   = $this->pathology_model->getPathologyBillByID($id);
        $data['result']        = $result;
        $page                  = $this->load->view('admin/pathology/_printtestparameterdetail', $data, true);
        echo json_encode(array('status' => 1, 'page' => $page));
    }
	
	public function pathologybalancereport()
    {
        $this->session->set_userdata('top_menu', 'Reports');
        $this->session->set_userdata('sub_menu', 'reports/pathology');
        $this->session->set_userdata('subsub_menu', 'reports/pathology/pathologybalancereport');

        $custom_fields = $this->customfield_model->get_custom_fields('pathology', '', '', 1);
        $staffsearch          = $this->patient_model->getstaffbytransactionbill();
        $data['staffsearch']  = $staffsearch;
        $data["searchlist"]   = $this->search_type;
        $data['fields']       = $custom_fields;       
		$data['agerange']       = $this->agerange;
		$data['gender']         = $this->customlib->getGender_Patient();
        
        $data['module'] = 'pathology';
        $this->load->view('layout/header', $data);
        $this->load->view('admin/pathology/pathologybalancereport', $data);
        $this->load->view('layout/footer', $data);
    }

    public function checkPathologyBalanceReportValidation()
    {
        $search = $this->input->post('search', TRUE);
        $this->form_validation->set_rules('search_type', $this->lang->line('search_type'), 'trim|required|xss_clean');

        if ($this->form_validation->run() == false) {
            $msg = array(
                'search_type' => form_error('search_type'),
            );
            $json_array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $param = array(
                'search_type'   => $this->input->post('search_type', TRUE),
                'date_from'     => $this->input->post('date_from', TRUE),
                'date_to'       => $this->input->post('date_to', TRUE),
                'from_age'      => $this->input->post('from_age', TRUE),
                'to_age'        => $this->input->post('to_age', TRUE),
                'gender'        => $this->input->post('gender', TRUE),
                'miscellaneous' => $this->input->post('miscellaneous', TRUE),
            );

            $json_array = array('status' => 'success', 'error' => '', 'param' => $param, 'message' => $this->lang->line('success_message'));
        }
        echo json_encode($json_array);
    }

    public function dtpathologybalancereports()
    {
        $search['search_type']   = $this->input->post('search_type', TRUE);
        $search['date_from']     = $this->input->post('date_from', TRUE);
        $search['date_to']       = $this->input->post('date_to', TRUE);
        $start_date              = '';
        $end_date                = '';

        $currency_symbol = $this->customlib->getHospitalCurrencyFormat();

        $condition['patient_status'] = $this->input->post('patient_status', TRUE);
        $condition['from_age']       = $this->input->post('from_age', TRUE);
        $condition['to_age']         = $this->input->post('to_age', TRUE);
        $condition['gender']         = $this->input->post('gender', TRUE);
        $condition['miscellaneous']  = $this->input->post('miscellaneous', TRUE);         
		
        $fields = $this->customfield_model->get_custom_fields('pathology', '', '', 1);
        if ($search['search_type'] == 'period') {

            $condition['start_date'] = $this->customlib->dateFormatToYYYYMMDD($search['date_from']);
            $condition['end_date']   = $this->customlib->dateFormatToYYYYMMDD($search['date_to']);

        } else {
            if (isset($search['search_type']) && $search['search_type'] != '') {
                $dates               = $this->customlib->get_betweendate($search['search_type']);
                $data['search_type'] = $search['search_type'];
            } else {
                $dates               = $this->customlib->get_betweendate('this_year');
                $data['search_type'] = '';
            }
            $condition['start_date'] = $dates['from_date'];
            $condition['end_date']   = $dates['to_date'];
        }

        $reportdata = $this->transaction_model->dtpathologybalancereports($condition);
       
        $reportdata    = json_decode($reportdata);
        $dt_data       = array();
        $total_balance = 0;       
        $total_charge  = 0;
        $total_net_amount  = 0;
        $total_paid_amount  = 0;
        $total_balance_amount  = 0;
        $total_amount=0;
        $is_antenatal  = "";
        $total_discount_amt  = 0;
        $total_tax_amt  = 0;
        $discount_amt  = 0;
        if (!empty($reportdata->data)) {
             
            foreach ($reportdata->data as $key => $value) {
                if(($value->net_amount - $value->paid_amount) == 0){
                    continue;
                }
                
                $apply_charge = ( ( $value->apply_charge * $value->tax_percentage ) / 100 + $value->apply_charge ) ;
                $total_charge += $apply_charge;
                $total_net_amount += $value->net_amount;
                $total_paid_amount += $value->paid_amount;
                $total_amount += $value->total;			
                
                if($value->is_antenatal==1){
                    $is_antenatal=$this->lang->line("yes");
                }else{
                    $is_antenatal=$this->lang->line("no");
                }
                
                if (!empty($value->patient_id)) {
                    $patient_id = " (" . $value->patient_id . ")";
                } else {
                    $patient_id = "";
                }                

                $row   = array();
                $row[] = $this->customlib->getSessionPrefixByType($value->module_no) . $value->module_id; 
                $row[] = $this->customlib->YYYYMMDDHisTodateFormat($value->date, $this->time_format);
                $row[] = composePatientName($value->patient_name, $value->patient_id);
                $row[] = $value->organisation_name;
                $row[] = $value->doctor_name;
                $row[] = composeStaffNameByString($value->name, $value->surname, $value->employee_id);
                $row[] = $is_antenatal;
               
                //====================
                if (!empty($fields)) {
                    foreach ($fields as $fields_key => $fields_value) {
                        $display_field = $value->{"$fields_value->name"};
                        if ($fields_value->type == "link") {
                            $display_field = "<a href=" . $value->{"$fields_value->name"} . " target='_blank'>" . $value->{"$fields_value->name"} . "</a>";
                        }
                        $row[] = $display_field;
                    }
                }
                //====================
                $balance_amount = ($value->net_amount) - ($value->paid_amount);	
				$total_balance_amount += $balance_amount;
                
                $row[]     = $value->total;
				
				$discount_amt = ( $value->total * $value->discount_percentage ) / 100 ;	
				
				$total_discount_amt += $discount_amt;
				
                $row[]     = number_format($discount_amt, 2) .'('. $value->discount_percentage.'%)';				
				
				$tax_amt = (($value->total - $discount_amt) * $value->tax_percentage ) / 100;
				$total_tax_amt += $tax_amt;				
				
                $row[]     = number_format($tax_amt, 2) .'('. $value->tax_percentage.'%)'; 				
                $row[]     = number_format((float) $value->net_amount, 2);
                $row[]     = number_format((float) $value->paid_amount, 2);
                $row[]     = number_format($balance_amount, 2);                 
                
                $dt_data[] = $row;
            }

            $footer_row   = array();
            $footer_row[] = "";
            $footer_row[] = "";
            $footer_row[] = "";
            $footer_row[] = "";
            $footer_row[] = "";
            $footer_row[] = "";            
            
				if (!empty($fields)) {
                    foreach ($fields as $fields_key => $fields_value) {
                         
                        $footer_row[] = "";
                    }
                }
                
            $footer_row[] = "<b>" . $this->lang->line('total_amount') . "</b>" . ':';
            $footer_row[] = "<b>" . $currency_symbol . (number_format($total_amount, 2, '.', '')) . "<br/>"; 
            $footer_row[] = "<b>" . $currency_symbol . (number_format($total_discount_amt, 2, '.', '')) . "<br/>"; 			
            $footer_row[] = "<b>" . $currency_symbol . (number_format($total_tax_amt, 2, '.', '')) . "<br/>"; 			
            $footer_row[] = "<b>" . $currency_symbol . (number_format($total_net_amount, 2, '.', '')) . "<br/>"; 			
            $footer_row[] = "<b>" . $currency_symbol . (number_format($total_paid_amount, 2, '.', '')) . "<br/>";			
            $footer_row[] = "<b>" . $currency_symbol . (number_format($total_balance_amount, 2, '.', '')) . "<br/>"; 
            $dt_data[]    = $footer_row;
        }

        $json_data = array(
            "draw"            => intval($reportdata->draw),
            "recordsTotal"    => intval($reportdata->recordsTotal),
            "recordsFiltered" => intval($reportdata->recordsFiltered),
            "data"            => $dt_data,
        );
        echo json_encode($json_data);
    }	

}
