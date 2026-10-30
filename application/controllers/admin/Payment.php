<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Payment extends Admin_Controller
{
	public $blood_group;
	public $charge_type;
	public $marital_status;
	public $opd_prefix;
	public $payment_mode;
	public $time_format;


    public function __construct()
    {
        parent::__construct();
        $this->config->load("payroll");
        $this->load->library('Enc_lib');
        $this->marital_status = $this->config->item('marital_status');
        $this->payment_mode   = $this->config->item('payment_mode');
        $this->blood_group    = $this->config->item('bloodgroup');
        $this->load->library('mailsmsconf');
        $this->load->library('system_notification');
        $this->load->library('SaasValidation');
        $this->charge_type   = $this->customlib->getChargeMaster();
        $data["charge_type"] = $this->charge_type;
        $this->load->model("transaction_model");
        $this->opd_prefix = $this->customlib->getSessionPrefixByType('opd_no');
        $this->config->load("image_valid");
        $this->time_format = $this->customlib->getHospitalTimeFormat();
    }

    public function create()
    {
        $this->form_validation->set_rules('amount', $this->lang->line("amount"), array('required', 'valid_amount',
            array('check_exists', array($this->transaction_model, 'validate_paymentamount')),
        )
        );

        $this->form_validation->set_rules('payment_date', $this->lang->line('payment_date'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('payment_mode', $this->lang->line('payment_mode'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('cheque_no', $this->lang->line('cheque_no'), 'callback_validate_cheque_no');
        $this->form_validation->set_rules('cheque_date', $this->lang->line('cheque_date'), 'callback_validate_cheque_date');
        $this->form_validation->set_rules('document', $this->lang->line('document'), 'callback_handle_doc_upload[document]|callback_validateCanUploadFile[document]');

        if ($this->form_validation->run() == false) {
            $msg = array(
                'amount'       => form_error('amount'),
                'payment_date' => form_error('payment_date'),
                'payment_mode' => form_error('payment_mode'),
                'cheque_no'    => form_error('cheque_no'),
                'cheque_date'  => form_error('cheque_date'),
                'document'     => form_error('document'),
            );

            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {

            $patient_id = $this->input->post("patient_id", TRUE);
            $ipd_id     = $this->input->post("ipdid", TRUE);

            $payment_date    = $this->customlib->dateFormatToYYYYMMDDHis($this->input->post("payment_date", TRUE), $this->time_format);
            $cheque_date     = $this->input->post("cheque_date", TRUE);
            $amount          = $this->input->post('amount', TRUE);
            $payment_section = $this->config->item('payment_section');

            $data = array(
                'case_reference_id' => $this->input->post('case_reference_id', TRUE),
                'section'           => $payment_section['ipd'],
                'patient_id'        => $patient_id,
                'amount'            => $amount,
                'type'              => 'payment',
                'ipd_id'            => $ipd_id,
                'payment_mode'      => $this->input->post('payment_mode', TRUE),
                'note'              => $this->input->post('note', TRUE),
                'payment_date'      => $payment_date,
                'received_by'       => $this->customlib->getLoggedInUserID(),
            );

            $attachment      = "";
            $attachment_name = "";
            $img_name = "";
            
          
            if (isset($_FILES["document"]) && !empty($_FILES["document"]['name'])) {
                $img_name = $this->media_storage->fileupload("document", "./uploads/payment_document/");

                // SaaS: add the uploaded document's size to the storage quota usage.
                try {
                    $saas_quota_result = $this->saasvalidation->updateStorageLimit('storage', ['document']);
                    // Capture silent API failures (lib returns JSON status:false instead of throwing).
                    if (is_string($saas_quota_result)) {
                        $saas_quota_decoded = json_decode($saas_quota_result);
                        if (isset($saas_quota_decoded->status) && $saas_quota_decoded->status === false) {
                            log_message('error', 'SaaS storage quota update returned failure (payment create): ' . ($saas_quota_decoded->message ?? 'unknown'));
                        }
                    }
                } catch (Exception $e) {
                    log_message('error', 'SaaS storage quota update failed (payment create): ' . $e->getMessage());
                }
            }

            if ($this->input->post('payment_mode', TRUE) == "Cheque") {

                $data['cheque_date']     = $this->customlib->dateFormatToYYYYMMDD($cheque_date);
                $data['cheque_no']       = $this->input->post('cheque_no', TRUE);
                $data['attachment']      = $img_name;
                $data['attachment_name'] = $img_name;
            }

            $insert_id = $this->transaction_model->add($data);

            $doctor_list       = $this->patient_model->getDoctorsipd($ipd_id);
            $consultant_doctor = $this->patient_model->get_patientidbyIpdId($ipd_id);

            $consultant_doctorarray[] = array('consult_doctor' => $consultant_doctor['cons_doctor'], 'name' => $consultant_doctor['doctor_name'] . " " . $consultant_doctor['doctor_surname'] . "(" . $consultant_doctor['doctor_employee_id'] . ")");
            foreach ($doctor_list as $key => $value) {
                $consultant_doctorarray[] = array('consult_doctor' => $value['consult_doctor'], 'name' => $value['ipd_doctorname'] . " " . $value['ipd_doctorsurname'] . "(" . $value['employee_id'] . ")");
            }

            $event_data = array(
                'patient_id'     => $patient_id,
                'ipd_no'         => $this->customlib->getSessionPrefixByType('ipd_no') . $ipd_id,
                'date'           => $this->customlib->YYYYMMDDHisTodateFormat($payment_date, $this->time_format),
                'amount'         => number_format((float) $amount, 2, '.', ''),
                'payment_mode'   => $this->lang->line(strtolower($this->input->post('payment_mode', TRUE))),
                'transaction_id' => $this->customlib->getSessionPrefixByType('transaction_id') . $insert_id,
            );
            $this->system_notification->send_system_notification('add_ipd_payment', $event_data, $consultant_doctorarray);
            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('record_saved_successfully'));
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

    public function validate_cheque_no()
    {
        if (isset($_POST['payment_mode']) and ($_POST['payment_mode'] == "Cheque")) {
            if ($this->input->post('cheque_no') == "") {
                $this->form_validation->set_message('validate_cheque_no', $this->lang->line('cheque_no_required'));
                return false;
            }
        }
        return true;
    }

    public function validate_cheque_date()
    {
        if (isset($_POST['payment_mode']) and ($_POST['payment_mode'] == "Cheque")) {
            if ($this->input->post('cheque_date') == "") {
                $this->form_validation->set_message('validate_cheque_date', $this->lang->line('cheque_date_required'));
                return false;
            }
        }
        return true;
    }

    public function addOPDPayment()
    {        
        $this->form_validation->set_rules(
            'amount', $this->lang->line('amount'), array('required', 'numeric', 'xss_clean', 'valid_amount',
                array('check_exists', array($this->transaction_model, 'validate_paymentamount')),
            )
        );
        $this->form_validation->set_rules('payment_date', $this->lang->line('payment_date'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('payment_mode', $this->lang->line('payment_mode'), 'trim|required|xss_clean');
        if ($_POST['payment_mode'] == "Cheque") {
            $this->form_validation->set_rules('cheque_no', $this->lang->line('cheque_no'), 'trim|required|xss_clean');
            $this->form_validation->set_rules('cheque_date', $this->lang->line('cheque_date'), 'trim|required|xss_clean');
            $this->form_validation->set_rules('document', $this->lang->line("document"), 'callback_handle_doc_upload[document]|callback_validateCanUploadFile[document]');
        }

        if ($this->form_validation->run() == false) {
            $msg = array(
                'amount'       => form_error('amount'),
                'payment_mode' => form_error('payment_mode'),
                'payment_date' => form_error('payment_date'),
                'cheque_date'  => form_error('cheque_date'),
                'cheque_no'    => form_error('cheque_no'),
                'document'     => form_error('document'),
            );

            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {

            $payment_date    = $this->customlib->dateFormatToYYYYMMDDHis($this->input->post("payment_date", TRUE), $this->time_format);
            $cheque_date     = $this->customlib->dateFormatToYYYYMMDD($this->input->post("cheque_date", TRUE));
            $amount          = $this->input->post('amount', TRUE);
            $payment_section = $this->config->item('payment_section');
            $data            = array(
                'case_reference_id' => $this->input->post('case_reference_id', TRUE),
                'patient_id'        => $this->input->post('patient_id', TRUE),
                'section'           => $payment_section['opd'],
                'amount'            => $amount,
                'type'              => 'payment',
                'opd_id'            => $this->input->post('opd_id', TRUE),
                'payment_mode'      => $this->input->post('payment_mode', TRUE),
                'note'              => $this->input->post('note', TRUE),
                'payment_date'      => $payment_date,
                'received_by'       => $this->customlib->getLoggedInUserID(),
            );

            $cheque_date = $this->input->post("cheque_date", TRUE);

            $insert_id       = $this->transaction_model->add($data);
            $attachment      = "";
            $attachment_name = "";
            $img_name        = "";

            if (isset($_FILES["document"]) && !empty($_FILES["document"]['name'])) {
                $img_name = $this->media_storage->fileupload("document", "./uploads/payment_document/");

                // SaaS: add the uploaded document's size to the storage quota usage.
                try {
                    $saas_quota_result = $this->saasvalidation->updateStorageLimit('storage', ['document']);
                    // Capture silent API failures (lib returns JSON status:false instead of throwing).
                    if (is_string($saas_quota_result)) {
                        $saas_quota_decoded = json_decode($saas_quota_result);
                        if (isset($saas_quota_decoded->status) && $saas_quota_decoded->status === false) {
                            log_message('error', 'SaaS storage quota update returned failure (payment addOPDPayment): ' . ($saas_quota_decoded->message ?? 'unknown'));
                        }
                    }
                } catch (Exception $e) {
                    log_message('error', 'SaaS storage quota update failed (payment addOPDPayment): ' . $e->getMessage());
                }
            }

            if ($this->input->post('payment_mode', TRUE) == "Cheque") {
                $data['id']              = $insert_id;
                $data['cheque_date']     = $this->customlib->dateFormatToYYYYMMDD($cheque_date);
                $data['cheque_no']       = $this->input->post('cheque_no', TRUE);
                $data['attachment']      = $img_name;
                $data['attachment_name'] = $img_name;
                $this->transaction_model->add($data);
            }

            $consultant_doctor = $this->patient_model->get_patientidbyopdid($this->input->post('opd_id', TRUE));

            $event_data = array(
                'patient_id'     => $this->input->post('patient_id', TRUE),
                'opd_no'         => $this->customlib->getSessionPrefixByType('opd_no') . $this->input->post('opd_id', TRUE),
                'date'           => $this->customlib->YYYYMMDDHisTodateFormat($payment_date, $this->time_format),
                'amount'         => number_format((float) $amount, 2, '.', ''),
                'payment_mode'   => $this->lang->line(strtolower($this->input->post('payment_mode', TRUE))),
                'doctor_id'      => $consultant_doctor['doctor_id'],
                'transaction_id' => $this->customlib->getSessionPrefixByType('transaction_id') . $insert_id,
            );

            $this->system_notification->send_system_notification('add_opd_payment', $event_data);
            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('record_saved_successfully'));
        }
        echo json_encode($array);
    }

    public function addambulancePayment()
    {
        $this->form_validation->set_rules('amount', $this->lang->line('amount'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('payment_date', $this->lang->line('payment_date'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('document', $this->lang->line('document'), 'callback_validateCanUploadFile[document]');
        if ($this->form_validation->run() == false) {
            $msg = array(
                'amount'       => form_error('amount'),
                'payment_date' => form_error('payment_date'),
                'document'     => form_error('document'),
            );

            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {

            $ambulancecall_id = $this->input->post("ambulancecall_id");
            $patient_id       = $this->input->post("patient_id");
            $date             = $this->input->post("payment_date");
            $payment_date     = $this->customlib->dateFormatToYYYYMMDD($date);
            $total_amount     = $this->input->post('total_amount');
            $paid_amount      = $this->input->post('amount');
            $paid_total       = $this->payment_model->getambulancepaidtotal($ambulancecall_id);
            $totalPaidamount  = $paid_total["paid_amount"] + $paid_amount;
            $balance_amount   = $total_amount - $totalPaidamount;
            if ($balance_amount <= 0) {
                $paidstatus = 'paid';
            } else {
                $paidstatus = 'unpaid';
            }
            $data = array(
                'ambulancecall_id' => $ambulancecall_id,
                'bill_no'          => $this->input->post('bill_no'),
                'amount'           => $total_amount,
                'paid_date'        => $payment_date,
                'paid'             => $paid_amount,
                'payment_mode'     => $this->input->post('payment_mode'),
                'balance'          => $balance_amount,
                'status'           => 'paid',
            );

            $insert_id        = $this->vehicle_model->addCallAmbulancebilling($data);
            $update_ambulance = array(
                'id'     => $ambulancecall_id,
                'status' => $paidstatus,
            );

            $this->vehicle_model->addCallAmbulance($update_ambulance);

            if (isset($_FILES["document"]) && !empty($_FILES['document']['name'])) {
                $fileInfo = pathinfo($_FILES["document"]["name"]);
                $img_name = $insert_id . '.' . $fileInfo['extension'];
                if (move_uploaded_file($_FILES["document"]["tmp_name"], "./uploads/payment_document/" . $img_name)) {
                    $data_img = array('id' => $insert_id, 'document' => $img_name);
                    $this->payment_model->addOPDPayment($data_img);

                    // SaaS: add the uploaded document's size to the storage quota usage.
                    try {
                        $saas_quota_result = $this->saasvalidation->updateStorageLimit('storage', ['document']);
                        // Capture silent API failures (lib returns JSON status:false instead of throwing).
                        if (is_string($saas_quota_result)) {
                            $saas_quota_decoded = json_decode($saas_quota_result);
                            if (isset($saas_quota_decoded->status) && $saas_quota_decoded->status === false) {
                                log_message('error', 'SaaS storage quota update returned failure (payment addambulancePayment): ' . ($saas_quota_decoded->message ?? 'unknown'));
                            }
                        }
                    } catch (Exception $e) {
                        log_message('error', 'SaaS storage quota update failed (payment addambulancePayment): ' . $e->getMessage());
                    }
                }
            }
           
            $array = array('status' => 'success', 'id' => $insert_id, 'error' => '', 'message' => $this->lang->line('success_message'));
        }

        echo json_encode($array);
    }

    public function addbloodissuePayment()
    {
        $this->form_validation->set_rules('amount', $this->lang->line('amount'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('payment_date', $this->lang->line('payment_date'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('document', $this->lang->line('document'), 'callback_validateCanUploadFile[document]');

        if ($this->form_validation->run() == false) {
            $msg = array(
                'amount'       => form_error('amount'),
                'payment_date' => form_error('payment_date'),
                'document'     => form_error('document'),
            );

            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {

            $bloodissue_id   = $this->input->post("bloodissue_id");
            $patient_id      = $this->input->post("patient_id");
            $date            = $this->input->post("payment_date");
            $payment_date    = $this->customlib->dateFormatToYYYYMMDD($date);
            $total_amount    = $this->input->post('total_amount');
            $paid_amount     = $this->input->post('amount');
            $paid_total      = $this->payment_model->getbloodissuepaidtotal($bloodissue_id);
            $totalPaidamount = $paid_total["paid_amount"] + $paid_amount;
            $balance_amount  = $total_amount - $totalPaidamount;
            if ($balance_amount <= 0) {
                $paidstatus = 'paid';
            } else {
                $paidstatus = 'unpaid';
            }
            $data = array(
                'bloodissue_id' => $bloodissue_id,
                'bill_no'       => $this->input->post('bill_no'),
                'amount'        => $total_amount,
                'paid_date'     => $payment_date,
                'paid'          => $paid_amount,
                'payment_mode'  => $this->input->post('payment_mode'),
                'balance'       => $balance_amount,
                'status'        => 'paid',

            );

            $insert_id         = $this->bloodissue_model->add_billing($data);
            $update_bloodissue = array(
                'id'     => $bloodissue_id,
                'status' => $paidstatus,
            );

            $this->bloodissue_model->add($update_bloodissue);

            if (isset($_FILES["document"]) && !empty($_FILES['document']['name'])) {
                $fileInfo = pathinfo($_FILES["document"]["name"]);
                $img_name = $insert_id . '.' . $fileInfo['extension'];
                if (move_uploaded_file($_FILES["document"]["tmp_name"], "./uploads/payment_document/" . $img_name)) {
                    $data_img = array('id' => $insert_id, 'document' => $img_name);
                    $this->payment_model->addOPDPayment($data_img);

                    // SaaS: add the uploaded document's size to the storage quota usage.
                    try {
                        $saas_quota_result = $this->saasvalidation->updateStorageLimit('storage', ['document']);
                        // Capture silent API failures (lib returns JSON status:false instead of throwing).
                        if (is_string($saas_quota_result)) {
                            $saas_quota_decoded = json_decode($saas_quota_result);
                            if (isset($saas_quota_decoded->status) && $saas_quota_decoded->status === false) {
                                log_message('error', 'SaaS storage quota update returned failure (payment addbloodissuePayment): ' . ($saas_quota_decoded->message ?? 'unknown'));
                            }
                        }
                    } catch (Exception $e) {
                        log_message('error', 'SaaS storage quota update failed (payment addbloodissuePayment): ' . $e->getMessage());
                    }
                }
            }

            $array = array('status' => 'success', 'id' => $insert_id, 'error' => '', 'message' => $this->lang->line('success_message'));
        }

        echo json_encode($array);
    }

    public function addotPayment()
    {
        $this->form_validation->set_rules('amount', $this->lang->line('amount'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('payment_date', $this->lang->line('payment_date'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('document', $this->lang->line('document'), 'callback_validateCanUploadFile[document]');

        if ($this->form_validation->run() == false) {
            $msg = array(
                'amount'       => form_error('amount'),
                'payment_date' => form_error('payment_date'),
                'document'     => form_error('document'),
            );

            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {

            $operation_id    = $this->input->post("operation_id");
            $patient_id      = $this->input->post("patient_id");
            $date            = $this->input->post("payment_date");
            $payment_date    = $this->customlib->dateFormatToYYYYMMDD($date);
            $total_amount    = $this->input->post('total_amount');
            $paid_amount     = $this->input->post('amount');
            $paid_total      = $this->payment_model->getotpaidtotal($operation_id);
            $totalPaidamount = $paid_total["paid_amount"] + $paid_amount;
            $balance_amount  = $total_amount - $totalPaidamount;
            if ($balance_amount <= 0) {
                $paidstatus = 'paid';
            } else {
                $paidstatus = 'unpaid';
            }
            $data = array(
                'operation_id' => $operation_id,
                'bill_no'      => $this->input->post('bill_no'),
                'amount'       => $total_amount,
                'paid_date'    => $payment_date,
                'paid'         => $paid_amount,
                'payment_mode' => $this->input->post('payment_mode'),
                'balance'      => $balance_amount,
                'status'       => 'paid',
            );

            $insert_id = $this->operationtheatre_model->addoperation_billing($data);
            $update_ot = array(
                'id'     => $operation_id,
                'status' => $paidstatus,
            );

            $this->operationtheatre_model->operation_detail($update_ot);

            if (isset($_FILES["document"]) && !empty($_FILES['document']['name'])) {
                $fileInfo = pathinfo($_FILES["document"]["name"]);
                $img_name = $insert_id . '.' . $fileInfo['extension'];
                if (move_uploaded_file($_FILES["document"]["tmp_name"], "./uploads/payment_document/" . $img_name)) {
                    $data_img = array('id' => $insert_id, 'document' => $img_name);
                    $this->payment_model->addOPDPayment($data_img);

                    // SaaS: add the uploaded document's size to the storage quota usage.
                    try {
                        $saas_quota_result = $this->saasvalidation->updateStorageLimit('storage', ['document']);
                        // Capture silent API failures (lib returns JSON status:false instead of throwing).
                        if (is_string($saas_quota_result)) {
                            $saas_quota_decoded = json_decode($saas_quota_result);
                            if (isset($saas_quota_decoded->status) && $saas_quota_decoded->status === false) {
                                log_message('error', 'SaaS storage quota update returned failure (payment addotPayment): ' . ($saas_quota_decoded->message ?? 'unknown'));
                            }
                        }
                    } catch (Exception $e) {
                        log_message('error', 'SaaS storage quota update failed (payment addotPayment): ' . $e->getMessage());
                    }
                }
            }

            $array = array('status' => 'success', 'id' => $insert_id, 'error' => '', 'message' => $this->lang->line('success_message'));
        }
        echo json_encode($array);
    }

    public function download($doc)
    {
        $this->load->helper('download');
        $filepath = "./uploads/payment_document/" . $doc;
        $data     = file_get_contents($filepath);
        force_download($doc, $data);
    }

    public function getBill()
    {
        $id                      = $this->input->post("patient_id");
        $ipdid                   = $this->input->post("ipdid");
        $data['total_amount']    = $this->input->post("total_amount");
        $data['discount']        = $this->input->post("discount");
        $data['other_charge']    = $this->input->post("other_charge");
        $data['gross_total']     = $this->input->post("gross_total");
        $data['tax']             = $this->input->post("tax");
        $data['net_amount']      = $this->input->post("net_amount");
        $data["print_details"]   = $this->printing_model->get('', 'ipd');
        $status                  = $this->input->post("status");
        $result                  = $this->patient_model->getIpdDetails($id, $ipdid, $status);
        $charges                 = $this->charge_model->getCharges($id, $ipdid);
        $paymentDetails          = $this->payment_model->paymentDetails($id, $ipdid);
        $paid_amount             = $this->payment_model->getPaidTotal($id, $ipdid);
        $balance_amount          = $this->payment_model->getBalanceTotal($id, $ipdid);
        $data["paid_amount"]     = $paid_amount["paid_amount"];
        $data["balance_amount"]  = $balance_amount["balance_amount"];
        $data["payment_details"] = $paymentDetails;
        $data["charges"]         = $charges;
        $data["result"]          = $result;
        $this->load->view("admin/patient/ipdBill", $data);
    }

    public function getOPDBill()
    {
        $id                    = $this->input->post("patient_id");
        $opdid                 = $this->input->post("opdid");
        $data['total_amount']  = $this->input->post("total_amount");
        $data['discount']      = $this->input->post("discount");
        $data['other_charge']  = $this->input->post("other_charge");
        $data['gross_total']   = $this->input->post("gross_total");
        $data['tax']           = $this->input->post("tax");
        $data['net_amount']    = $this->input->post("net_amount");
        $data["print_details"] = $this->printing_model->get('', 'opd');
        $status                = $this->input->post("status");
        $result                = $this->patient_model->getDetails($id, $opdid);
        $charges               = $this->charge_model->getOPDCharges($id, $opdid);
        $paymentDetails = $this->payment_model->opdPaymentDetails($id, $opdid);
        $paid_amount    = $this->payment_model->getOPDPaidTotal($id, $opdid);
        $balance_amount = $this->payment_model->getOPDBalanceTotal($id);
        $billstatus         = $this->patient_model->getBillstatus($id, $opdid);
        $data["billstatus"] = $billstatus;

        $data["paid_amount"]     = $paid_amount["paid_amount"];
        $data["balance_amount"]  = $balance_amount["balance_amount"];
        $data["payment_details"] = $paymentDetails;
        $data["charges"]         = $charges;
        $data["result"]          = $result;

        $this->load->view("admin/patient/opdBill", $data);
    }

    public function getVisitBill()
    {
        $visit_id = $this->input->post("visit_id");
        $data["print_details"] = $this->printing_model->get('', 'opd');
        $status                = $this->input->post("status");
        $result                = $this->patient_model->printVisitDetails($visit_id);
        $data["result"]     = $result;
        $data["opd_prefix"] = $this->opd_prefix;
        $this->load->view("admin/patient/visitBill", $data);
    }

    public function addbill()
    {
        $this->form_validation->set_rules('net_amount', $this->lang->line('net_amount'), 'trim|required|xss_clean');
        if ($this->form_validation->run() == false) {
            $msg = array(
                'net_amount' => form_error('net_amount'),
            );
            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $patient_id = $this->input->post('patient_id');
            $ipdid      = $this->input->post('ipdid');
            $data       = array('patient_id' => $this->input->post('patient_id'),
                'ipd_id'                         => $this->input->post('ipdid'),
                'discount'                       => $this->input->post('discount'),
                'other_charge'                   => $this->input->post('other_charge'),
                'total_amount'                   => $this->input->post('gross_total'),
                'gross_total'                    => $this->input->post('gross_total'),
                'tax'                            => $this->input->post('tax'),
                'net_amount'                     => $this->input->post('net_amount'),
                'date'                           => date("Y-m-d"),
                'generated_by'                   => $this->customlib->getLoggedInUserID(),
                'status'                         => 'paid',
            );
            $this->payment_model->add_bill($data);
            $patient  = $this->patient_model->patientProfileDetails($patient_id);
            $bed_no   = $this->input->post('bed_no');
            $bed_data = array('id' => $bed_no, 'is_active' => 'yes');
            $this->bed_model->savebed($bed_data);
            $ipd_data = array('id' => $ipdid, 'discharged' => 'yes', 'discharged_date' => date("Y-m-d"));
            $this->patient_model->add_ipd($ipd_data);

            $patient_data = array('id' => $patient_id, 'discharged' => 'yes');
            $this->patient_model->add($patient_data);
            $array          = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('record_saved_successfully'));
            $sender_details = array('patient_id' => $patient_id, 'ipd_id' => $ipdid, 'contact_no' => $patient['mobileno'], 'email' => $patient['email']);
            $this->mailsmsconf->mailsms('ipd_patient_discharged', $sender_details);
        }

        echo json_encode($array);
    }

    public function addopdbill()
    {
        $this->form_validation->set_rules('total_amount', $this->lang->line('total_amount'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('net_amount', $this->lang->line('net_amount'), 'trim|required|xss_clean');
        if ($this->form_validation->run() == false) {
            $msg = array(
                'total_amount' => form_error('total_amount'),
                'net_amount'   => form_error('net_amount'),
            );
            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {

            $patient_id = $this->input->post('patient_id', TRUE);
            $patient    = $this->patient_model->patientDetails($patient_id);
            $opd_id     = $this->input->post('opd_id', TRUE);
            $data       = array(
                'opd_details_id' => $opd_id,
                'discount'       => $this->input->post('discount', TRUE),
                'other_charge'   => $this->input->post('other_charge', TRUE),
                'total_amount'   => $this->input->post('total_amount', TRUE),
                'gross_total'    => $this->input->post('gross_total', TRUE),
                'tax'            => $this->input->post('tax', TRUE),
                'net_amount'     => $this->input->post('net_amount', TRUE),
                'date'           => date("Y-m-d"),
                'generated_by'   => $this->customlib->getLoggedInUserID(),
                'status'         => 'paid',
                'paymode'        => $this->input->post('bill_paymode', TRUE),
            );
            $opd_data = array('patient_id' => $patient_id,
                'id'                           => $opd_id,
                'discharged'                   => 'yes',
            );

            $sender_details = array('patient_id' => $patient_id, 'opd_id' => $opd_id, 'contact_no' => $patient['mobileno'], 'email' => $patient['email']);
            $this->payment_model->add_opdbill($data);
            $this->patient_model->add_opd($opd_data);
            $this->mailsmsconf->mailsms('opd_patient_discharged', $sender_details);
            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('record_saved_successfully'));
        }

        echo json_encode($array);
    }

    public function payment_attachment()
    {
        $this->load->helper('download');
        $filepath = "./" . $this->uri->segment(4) . "/" . $this->uri->segment(5) . "/" . $this->uri->segment(6);
        $data     = file_get_contents($filepath);
        $name     = $this->uri->segment(6);
        force_download($name, $data);
    }

}
