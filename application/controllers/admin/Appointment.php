<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Appointment extends Admin_Controller
{
	public $appointment_status;
	public $notification;
	public $notificationurl;
	public $patient_notificationurl;
	public $payment_mode;
	public $search_type;
	public $time_format;
	public $yesno_condition;


    public function __construct()
    {
        parent::__construct();
        $this->config->load("payroll");
        $this->config->load("mailsms");
        $this->notification            = $this->config->item('notification');
        $this->notificationurl         = $this->config->item('notification_url');
        $this->yesno_condition         = $this->config->item('yesno_condition');
        $this->patient_notificationurl = $this->config->item('patient_notification_url');
        $this->search_type             = $this->config->item('search_type');
        $this->load->library('mailsmsconf');
        $this->load->library('Enc_lib');
        $this->load->library('datatables');
        $this->load->library('system_notification');
        $this->load->library('SaasValidation');
        $this->load->model(array('appoint_priority_model', 'onlineappointment_model', 'transaction_model', 'conference_model'));
        $this->appointment_status = $this->config->item('appointment_status');
        $this->load->helper('customfield_helper');
        $this->time_format = $this->customlib->getHospitalTimeFormat();
        $this->config->load('image_valid');
        $this->payment_mode = $this->config->item('payment_mode');
    }

    public function unauthorized()
    {
        $data = array();
        $this->load->view('layout/header', $data);
        $this->load->view('unauthorized', $data);
        $this->load->view('layout/footer', $data);
    }

    public function index()
    {
        if (!$this->rbac->hasPrivilege('appointment', 'can_view')) {
            access_denied();
        }

        $this->session->set_userdata('top_menu', 'appointment');
        $app_data                      = $this->session->flashdata('app_data');
        $data['app_data']              = $app_data;
        $doctors                       = $this->staff_model->getStaffbyrole(3);
        $data["doctors"]               = $doctors;
        $data["appointment_status"]    = $this->appointment_status;
        $data["yesno_condition"]       = $this->yesno_condition;
        $userdata                      = $this->customlib->getUserData();
        $role_id                       = $userdata['role_id'];
        $data["bloodgroup"]            = $this->bloodbankstatus_model->get_product(null, 1);
        $doctorid                      = "";
        $data['appoint_priority_list'] = $this->appoint_priority_model->appoint_priority_list();
        $doctor_restriction            = $this->session->userdata['hospitaladmin']['doctor_restriction'];
        $disable_option                = false;

        if ($doctor_restriction == 'enabled') {
            if ($role_id == 3) {
                $disable_option = true;
                $doctorid       = $userdata['id'];
            }
        }

        $data["doctor_select"]  = $doctorid;
        $data["disable_option"] = $disable_option;
        $data['fields']         = $this->customfield_model->get_custom_fields('appointment', 1);
        $data['payment_mode']   = $this->payment_mode;
        $data['organisation']    = $this->organisation_model->get();
        $data['module']          = 'appointment';
        $this->load->view('layout/header', $data);
        $this->load->view('admin/appointment/index', $data);
        $this->load->view('layout/footer', $data);
    }

    public function add()
    {
		if (!$this->rbac->hasPrivilege('appointment', 'can_add')) {
            access_denied();
        }
		
        $custom_fields = $this->customfield_model->getByBelong('appointment');
        foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
            if ($custom_fields_value['validation']) {
                $custom_fields_id   = $custom_fields_value['id'];
                $custom_fields_name = $custom_fields_value['name'];
                $this->form_validation->set_rules("custom_fields[appointment][" . $custom_fields_id . "]", $custom_fields_name, 'trim|required|xss_clean');
            }
        }

        $this->form_validation->set_rules('date', $this->lang->line('appointment_date'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('doctorid', $this->lang->line('doctor'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('amount', $this->lang->line('doctor_fees'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('patient_id', $this->lang->line('patient'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('global_shift', $this->lang->line('shift'), 'trim|required');
        $this->form_validation->set_rules('slot', $this->lang->line('slot'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('priority', $this->lang->line('priority'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('appointment_status', $this->lang->line('appointment_status'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('validate_resource', $this->lang->line('appointment'), 'callback_validateCanAddNewResource[no_of_appointment,1]');

        if ($this->input->post("payment_mode", TRUE) == "Cheque") {
            $this->form_validation->set_rules('cheque_no', $this->lang->line('cheque_no'), 'trim|required');
            $this->form_validation->set_rules('cheque_date', $this->lang->line('cheque_date'), 'trim|required');
            $this->form_validation->set_rules('document', $this->lang->line("document"), 'callback_handle_doc_upload[document]|callback_validateCanUploadFile[document]');
        }

        $consult      = $this->input->post('live_consult', TRUE);
        if ($consult == 'yes') {
            if (!$this->session->has_userdata('zoom_access_token')) {
                $this->form_validation->set_rules('zoom_live', $this->lang->line('zoom_access_token_is_invalid_or_not_generated_from_zoom_setting'), 'trim|required|xss_clean', array('required' => $this->lang->line('zoom_access_token_is_invalid_or_not_generated_from_zoom_setting')));
            }
        }

        if ($this->form_validation->run() == false) {
            $msg = array(
                'patient_id'         => form_error('patient_id'),
                'doctor'             => form_error('doctorid'),
                'amount'             => form_error('amount'),
                'global_shift'       => form_error('global_shift'),
                'date'               => form_error('date'),
                'slot'               => form_error('slot'),
                'appointment_status' => form_error('appointment_status'),
                'cheque_no'          => form_error('cheque_no'),
                'cheque_date'        => form_error('cheque_date'),
                'document'           => form_error('document'),
                'priority'           => form_error('priority'),
                'zoom_live'           => form_error('zoom_live'),
                // SaaS count-limit (no_of_appointment) error. The virtual validate_resource
                // field has no input of its own; including it here lets the existing AJAX
                // error toast (which concatenates all error values) surface the message.
                'validate_resource'  => form_error('validate_resource'),
            );

            if (!empty($custom_fields)) {
                foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
                    if ($custom_fields_value['validation']) {
                        $custom_fields_id                                                    = $custom_fields_value['id'];
                        $custom_fields_name                                                  = $custom_fields_value['name'];
                        $error_msg2["custom_fields[appointment][" . $custom_fields_id . "]"] = form_error("custom_fields[appointment][" . $custom_fields_id . "]");
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
            $staff_id     = $this->customlib->getLoggedInUserID();
            $date         = $this->input->post('date', TRUE);
            $date_appoint = $this->customlib->dateFormatToYYYYMMDDHis($date, $this->time_format);
            $patient_id   = $this->input->post('patient_id', TRUE);

            if ($consult == '') {
                $consult = 'no';
            }
            $cheque_date = $this->customlib->dateFormatToYYYYMMDD($this->input->post("cheque_date", TRUE));

            $doctor = $this->input->post('doctorid', TRUE);

            $getstaffdetails = $this->staff_model->getstaff($doctor);

            if ($getstaffdetails['specialist']) {
                $specialist =  $getstaffdetails['specialist'];
            } else {
                $specialist =  '';
            }

            $date   =   date("Y-M-d ", strtotime($date_appoint));
            $day    =   date("l", strtotime($date));

            $getDoctorShiftTimeId = $this->onlineappointment_model->getDoctorShiftTimeId($doctor, $this->input->post('global_shift', TRUE), $day);

            $appointment = array(
                'patient_id'             => $patient_id,
                'date'                   => $date_appoint,
                'priority'               => $this->input->post('priority', TRUE),
                'doctor'                 => $doctor,
                'message'                => $this->input->post('message', TRUE),
                'doctor_shift_time_id'   => $getDoctorShiftTimeId->id,
                'is_queue'               => 0,
                'live_consult'           => $consult,
                'source'                 => 'Offline',
                'appointment_status'     => $this->input->post('appointment_status', TRUE),
                'specialist'             => $specialist,
                'doctor_global_shift_id' => $this->input->post('global_shift', TRUE),
                'created_by'             => $this->customlib->getStaffID(),
            );

            $insert_id = $this->appointment_model->add($appointment);

            // SaaS: increment the appointment count usage by 1 (count-based resource:
            // no_of_appointment). The pre-check only blocks when over limit; this raises the
            // usage so the limit stays meaningful. Own try/catch so a quota-API hiccup does
            // not abort the already-created appointment's flow (updateResouceQuota throws).
            if ($insert_id) {
                try {
                    $this->saasvalidation->updateResouceQuota('no_of_appointment', 1);
                } catch (Exception $e) {
                    log_message('error', 'SaaS no_of_appointment quota increment failed (appointment add): ' . $e->getMessage());
                }
            }

	            if ($this->input->post('amount', TRUE) == 0) {
                $discount_percentage = 0;
            } else {
                if (empty($this->input->post('discount_percentage', TRUE))) {
                    $discount_percentage = 0;
                } else {
                    $discount_percentage = $this->input->post('discount_percentage', TRUE);
                }
            }			
			
			$shift_details  = $this->onlineappointment_model->getShiftDetails($doctor);		
        
            $charge_details = $this->charge_model->getChargeDetailsById($shift_details['charge_id']);
            
            $standard_amount = isset($charge_details->standard_charge) ? amountFormat($charge_details->standard_charge + ($charge_details->standard_charge * $charge_details->percentage / 100)) : "";
			
            $charge_id = $shift_details['charge_id'];			
			
            $amount_paid1 = $this->input->post('amount', TRUE) - calculatePercent($this->input->post('amount', TRUE), $discount_percentage);


			$amount_paid =  $amount_paid1 + calculatePercent($amount_paid1, $charge_details->percentage);				
				
			
            $payment_data = array(
                'appointment_id' => $insert_id,
                'standard_amount'    => $standard_amount,
                'paid_amount'    => $amount_paid,
                'charge_id'      => $charge_id,
                'discount_percentage' => $discount_percentage,
                'tax' => $charge_details->percentage,
                'payment_type'   => 'Offline',
                'date'           => date("Y-m-d H:i:s"),
            );
            $payment_section   = $this->config->item('payment_section');
            $transaction_array = array(
                'amount'         => $amount_paid,
                'patient_id'     => $patient_id,
                'section'        => $payment_section['appointment'],
                'type'           => 'payment',
                'appointment_id' => $insert_id,
                'payment_mode'   => $this->input->post("payment_mode", TRUE),
                'payment_date'   => date('Y-m-d H:i:s'),
                'received_by'    => $staff_id,
            );

            if ($this->input->post('payment_mode', TRUE) == "Cheque") {
                $transaction_array['cheque_date']     = $cheque_date;
                $transaction_array['cheque_no']       = $this->input->post('cheque_no', TRUE);
                if (isset($_FILES["document"]) && !empty($_FILES['document']['name'])) {
                    $file_name = $this->media_storage->fileupload("document",'./uploads/payment_document/');
                    $transaction_array['attachment']      = $file_name;
                    $transaction_array['attachment_name'] = $_FILES["document"]["name"];

                    // SaaS: add the uploaded document's size to the storage quota usage.
                    try {
                        $saas_quota_result = $this->saasvalidation->updateStorageLimit('storage', ['document']);
                        // Capture silent API failures (lib returns JSON status:false instead of throwing).
                        if (is_string($saas_quota_result)) {
                            $saas_quota_decoded = json_decode($saas_quota_result);
                            if (isset($saas_quota_decoded->status) && $saas_quota_decoded->status === false) {
                                log_message('error', 'SaaS storage quota update returned failure (appointment add): ' . ($saas_quota_decoded->message ?? 'unknown'));
                            }
                        }
                    } catch (Exception $e) {
                        log_message('error', 'SaaS storage quota update failed (appointment add): ' . $e->getMessage());
                    }
                }
            }

            $this->appointment_model->saveAppointmentPayment($payment_data, $transaction_array);

            /* OPD Insert Code*/
            $appointment_id      = $insert_id;
            $appointment_details = $this->appointment_model->getDetails($appointment_id);
            $transaction_data    = $this->transaction_model->getTransactionByAppointmentId($appointment_id);
            $appointment_payment = $this->appointment_model->getPaymentByAppointmentId($appointment_id);
            $charges             = $this->charge_model->getChargeByChargeId($charge_id);
            $opd_details         = array(
                'patient_id'   => $appointment_details['patient_id'],
                'generated_by' => $this->customlib->getStaffID(),
            );
            $visit_details = array(
                'appointment_date'  => $appointment_details['date'],
                'opd_details_id'    => 0,
                'cons_doctor'       => $appointment_details['doctor'],
                'generated_by'      => $this->customlib->getLoggedInUserID(),
                'patient_charge_id' => null,
                'transaction_id'    => $transaction_data->id,
                'can_delete'        => 'no',
                'live_consult'      => $consult,
            );
            $staff_data = $this->staff_model->getStaffByID($appointment_details['doctor']);
            $staff_name = composeStaffName($staff_data);
            $charge     = array(
                'opd_id'          => 0,
                'date'            => date('Y-m-d H:i:s'),
                'charge_id'       => $charge_id,
                'qty'             => 1,
                'apply_charge'    => $standard_amount,
                'standard_charge' => $standard_amount,
                'discount_percentage' => $appointment_payment->discount_percentage,
                'amount'          => $amount_paid,
                'created_at'      => date('Y-m-d H:i:s'),
                'note'            => null,
                'tax'             => $charges['percentage'],
            );
            $opd_visit_details = $this->appointment_model->moveToOpd($opd_details, $visit_details, $charge, $appointment_id);

            // SaaS: count the OPD only when the appointment is created as 'approved'.
            // The process (moveToOpd above) is left unchanged for every status; only the
            // no_of_opd usage is gated here so pending/cancel appointments don't consume
            // OPD quota. Mirrors the lowercase 'approved' check used for notifications below.
            if ($this->input->post('appointment_status', TRUE) == 'approved') {
                // increment the OPD count usage by 1 (count-based resource: no_of_opd).
                // Own try/catch so a quota-API hiccup does not abort the already-created flow.
                try {
                    $this->saasvalidation->updateResouceQuota('no_of_opd', 1);
                } catch (Exception $e) {
                    log_message('error', 'SaaS no_of_opd quota increment failed (appointment add->opd): ' . $e->getMessage());
                }
            }

            /* OPD Insert Code*/
            $visit_detail = $this->patient_model->getVisitDetailByid($opd_visit_details['visit_details_id']);
            $setting_result   = $this->setting_model->getzoomsetting();
            $opdduration      = $setting_result->opd_duration;

            if ($consult == 'yes') {
                $api_type = 'global';
                $params   = array(
                    'zoom_api_key'    => "",
                    'zoom_api_secret' => "",
                );

                $title = 'Online consult for ' . $this->customlib->getSessionPrefixByType('opd_no') . $visit_detail->opd_details_id . " Checkup ID " . $visit_detail->id;
                $this->load->library('zoom_api', $params);
                $insert_array = array(
                    'staff_id'         => $doctor,
                    'visit_details_id' => $visit_detail->id,
                    'title'            => $title,
                    'date'             => $date_appoint,
                    'duration'         => $opdduration,
                    'created_id'       => $this->customlib->getStaffID(),
                    'password'         => random_string(),
                    'api_type'         => $api_type,
                    'host_video'       => 1,
                    'client_video'     => 1,
                    'purpose'          => 'consult',
                    'timezone'         => $this->customlib->getTimeZone(),
                    'patient_id'         => $patient_id,
                );

                $response = $this->zoom_api->createAMeeting($insert_array);

                if ($response['status']) {

                    if (isset($response['data'])) {
                        $insert_array['return_response'] = json_encode($response['data']);
                        $this->conference_model->add($insert_array);
                    }
                }
            }

            $custom_field_post  = $this->input->post("custom_fields[appointment]");
            $custom_value_array = array();
            if (!empty($custom_field_post)) {
                foreach ($custom_field_post as $key => $value) {
                    $check_field_type = $this->input->post("custom_fields[appointment][" . $key . "]", TRUE);
                    $field_value      = is_array($check_field_type) ? implode(",", $check_field_type) : $check_field_type;
                    $array_custom     = array(
                        'belong_table_id' => 0,
                        'custom_field_id' => $key,
                        'field_value'     => $field_value,
                    );
                    $custom_value_array[] = $array_custom;
                }
            }

            if (!empty($custom_value_array)) {
                $this->customfield_model->insertRecord($custom_value_array, $insert_id);
            }

            $doctor_details = $this->notificationsetting_model->getstaffDetails($doctor);
            $event_data     = array(
                'appointment_date'   => $this->customlib->YYYYMMDDHisTodateFormat($date_appoint, $this->customlib->getHospitalTimeFormat()),
                'patient_id'         => $patient_id,
                'doctor_id'          => $doctor,
                'doctor_name'        => composeStaffNameByString($doctor_details['name'], $doctor_details['surname'], $doctor_details['employee_id']),
                'message'            => $this->input->post('message', TRUE),
                'appointment_status' => $this->input->post('appointment_status', TRUE),
            );

            $sender_details = array('patient_id' => $appointment_details["patient_id"], 'appointment_id' => $appointment_id);

            if ($this->input->post('appointment_status', TRUE) == 'approved') {
                $this->mailsmsconf->mailsms('appointment_approved', $sender_details);
                $this->system_notification->send_system_notification('notification_appointment_created', $event_data);
                $this->system_notification->send_system_notification('appointment_approved', $event_data);
            }

            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'), 'patient_id' => $appointment_details['patient_id'], 'appointment_id' => $appointment_id);
        }
        echo json_encode($array);
    }

    public function printAppointmentBill()
    {
        $print_details         = $this->printing_model->get('', 'appointment');
        $data["print_details"] = $print_details;
        $id     = $this->input->post("appointment_id", TRUE);
        $result = $this->appointment_model->getDetailsAppointment($id);
	
		if ($result['appointment_status'] == 'approved') {
			$result['appointment_no'] = $this->customlib->getSessionPrefixByType('appointment') . $id;
		}else{
			$result['appointment_no'] ="";
		}

        if ($result['start_time']) {
            if ($this->time_format) {
                $result["doctor_shift_name"]       = date('G:i:s', strtotime($result['start_time'])) . " - " . date('G:i:s', strtotime($result['end_time']));
            } else {
                $result["doctor_shift_name"]       = date('g:i A', strtotime($result['start_time'])) . " - " . date('g:i A', strtotime($result['end_time']));
            }
        } else {
            $result["doctor_shift_name"]       = '';
        }

        $result["patients_name"]       = composePatientName($result['patients_name'], $result['patient_id']);
        $result["edit_live_consult"]   = $this->lang->line($result['live_consult']);
        $result["live_consult"]        = $result['live_consult'];
        $result["date"]                = $this->customlib->YYYYMMDDHisTodateFormat($result['date'], $this->time_format);
        $result['custom_fields_value'] = display_custom_fields('appointment', $id);
        $cutom_fields_data             = get_custom_table_values($id, 'appointment');
        $data['field_data']          = $cutom_fields_data;
        $result['patients_gender']     = $result['patients_gender'];
        $result['transaction_id']      = $this->customlib->getSessionPrefixByType('transaction_id') . $result['transaction_id'];
        $data['appointment_id']        = $id;
        $data['fields']                = $this->customfield_model->get_custom_fields('appointment');
        $data['result']                = $result;
        $page = $this->load->view('patient/printAppointment', $data, true);
        echo json_encode(array('status' => 1, 'page' => $page));
    }
	
    /*
    This Function is Used to Update Records
     */
    public function update()
    {
        $custom_fields = $this->customfield_model->getByBelong('appointment');
        if (!empty($custom_fields)) {
            foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
                if ($custom_fields_value['validation']) {
                    $custom_fields_id   = $custom_fields_value['id'];
                    $custom_fields_name = $custom_fields_value['name'];

                    $this->form_validation->set_rules("custom_fields[appointment][" . $custom_fields_id . "]", $custom_fields_name, 'trim|required|xss_clean');
                }
            }
        }
        $this->form_validation->set_rules('date', $this->lang->line('appointment_date'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('doctor', $this->lang->line('doctor'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('amount', $this->lang->line('doctor_fees'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('message', $this->lang->line('message'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('patient_id', $this->lang->line('patient'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('global_shift', $this->lang->line('shift'), 'trim|required');
        $this->form_validation->set_rules('slot', $this->lang->line('slot'), 'trim|required|xss_clean');
        if ($this->form_validation->run() == false) {
            $msg = array(
                'patient_id'         => form_error('patient_id'),
                'doctor'             => form_error('doctorid'),
                'amount'             => form_error('amount'),
                'global_shift'       => form_error('global_shift'),
                'date'               => form_error('date'),
                'slot'               => form_error('slot'),
                'message'            => form_error('message'),
                'appointment_status' => form_error('appointment_status'),
            );

            if (!empty($custom_fields)) {
                foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
                    if ($custom_fields_value['validation']) {
                        $custom_fields_id                                                    = $custom_fields_value['id'];
                        $custom_fields_name                                                  = $custom_fields_value['name'];
                        $error_msg2["custom_fields[appointment][" . $custom_fields_id . "]"] = form_error("custom_fields[appointment][" . $custom_fields_id . "]");
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
            $id                  = $this->input->post('id', TRUE);
            $appointment_details = $this->appointment_model->getDetails($id);
            $date                = $this->input->post('date', TRUE);
            $custom_field_post   = $this->input->post("custom_fields[appointment]");
            $consult             = $this->input->post('live_consult', TRUE);
            $appointment_payment = $this->appointment_model->getPaymentByAppointmentId($id);
            $charges             = $this->charge_model->getChargeByChargeId($appointment_payment->charge_id);
            $apply_charge        = $charges['standard_charge'] + ($charges['standard_charge'] * ($charges['percentage'] / 100));
            $patient_id_update   = $this->input->post('patient_id', TRUE);
            $amount_update       = $this->input->post('amount', TRUE);

            $appointment = array(
                'id'              => $id,
                'patient_id'      => $patient_id_update,
                'date'            => $this->customlib->dateFormatToYYYYMMDDHis($date, $this->time_format),
                'priority'        => $this->input->post('priority', TRUE),
                'doctor'          => $this->input->post('doctor', TRUE),
                'message'         => $this->input->post('message', TRUE),
                'global_shift_id' => $this->input->post('global_shift', TRUE),
                'shift_id'        => $this->input->post('slot', TRUE),
                'is_queue'        => 0,
                'live_consult'    => $consult,
            );
            $payment_data = array(
                'appointment_id' => $id,
                'paid_amount'    => $amount_update,
                'charge_id'      => $this->input->post('charge_id', TRUE),
                'payment_type'   => 'Offline',
                'date'           => date("Y-m-d H:i:s"),
            );
            $payment_section   = $this->config->item('payment_section');
            $transaction_array = array(
                'amount'         => $amount_update,
                'patient_id'     => $patient_id_update,
                'section'        => $payment_section['appointment'],
                'type'           => 'payment',
                'appointment_id' => $id,
                'payment_mode'   => "Offline",
                'payment_date'   => date('Y-m-d H:i:s'),
                'received_by'    => $this->customlib->getLoggedInUserID(),
            );
            $visit_data  = $this->patient_model->getVisitdataDetails($appointment_details['visit_details_id']);
            $opd_details = array(
                'id'           => $visit_data['opdid'],
                'patient_id'   => $appointment_details['patient_id'],
                'generated_by' => $this->customlib->getStaffID(),
            );
            $visit_details = array(
                'id'               => $appointment_details['visit_details_id'],
                'appointment_date' => date("Y-m-d H:i:s"),
                'opd_details_id'   => $visit_data['opdid'],
                'cons_doctor'      => $appointment_details['doctor'],
                'generated_by'     => $this->customlib->getLoggedInUserID(),
                'can_delete'       => 'no',
            );
            $staff_data = $this->staff_model->getStaffByID($appointment_details['doctor']);
            $staff_name = composeStaffName($staff_data);
            $charge     = array(
                'date'            => date('Y-m-d'),
                'charge_id'       => $appointment_payment->charge_id,
                'qty'             => 1,
                'apply_charge'    => $apply_charge,
                'standard_charge' => $charges['standard_charge'],
                'amount'          => $appointment_payment->paid_amount,
                'created_at'      => date('Y-m-d H:i:s'),
                'note'            => $staff_name,
                'tax'             => $charges['percentage'],
            );

            $this->appointment_model->updateAppointment($appointment, $payment_data, $transaction_array, $opd_details, $visit_details, $charge);
            if (!empty($custom_fields)) {
                foreach ($custom_field_post as $key => $value) {
                    $check_field_type = $this->input->post("custom_fields[appointment][" . $key . "]", TRUE);
                    $field_value      = is_array($check_field_type) ? implode(",", $check_field_type) : $check_field_type;
                    $array_custom     = array(
                        'belong_table_id' => $id,
                        'custom_field_id' => $key,
                        'field_value'     => $field_value,
                    );
                    $custom_value_array[] = $array_custom;
                }
                $this->customfield_model->updateRecord($custom_value_array, $id, 'appointment');
            }
            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
        }
        echo json_encode($array);
    }

    public function status($id)
    {
        $data = array('appointment_status' => 'approved');
        $this->appointment_model->status($id, $data);
        $appointment_details = $this->appointment_model->getDetails($id);
        $date_appoint        = $appointment_details['date'];

        $doctor_details = $this->notificationsetting_model->getstaffDetails($appointment_details["doctor"]);

        $event_data = array(
            'appointment_date'   => $this->customlib->YYYYMMDDHisTodateFormat($date_appoint, $this->time_format),
            'patient_id'         => $appointment_details["patient_id"],
            'doctor_id'          => $appointment_details["doctor"],
            'doctor_name'        => composeStaffNameByString($doctor_details['name'], $doctor_details['surname'], $doctor_details['employee_id']),
            'message'            => $appointment_details["message"],
            'appointment_status' => $this->lang->line($appointment_details["appointment_status"]),
        );

        $this->system_notification->send_system_notification('appointment_approved', $event_data);
        $sender_details = array('patient_id' => $appointment_details["patient_id"], 'appointment_id' => $id, 'contact_no' => $appointment_details["mobileno"], 'email' => $appointment_details["email"]);
        $this->mailsmsconf->mailsms('appointment_approved', $sender_details);
        redirect('admin/appointment/index');
    }

    public function search()
    {
        $this->session->set_userdata('top_menu', 'front_office');
        $app_data                      = $this->session->flashdata('app_data');
        $data['app_data']              = $app_data;
        $doctors                       = $this->staff_model->getStaffbyrole(3);
        $data["doctors"]               = $doctors;
        $patients                      = $this->patient_model->getPatientListall();
        $data["patients"]              = $patients;
        $data["appointment_status"]    = $this->appointment_status;
        $userdata                      = $this->customlib->getUserData();
        $role_id                       = $userdata['role_id'];
        $data["yesno_condition"]       = $this->yesno_condition;
        $doctorid                      = "";
        $data['appoint_priority_list'] = $this->appoint_priority_model->appoint_priority_list();
        $doctor_restriction            = $this->session->userdata['hospitaladmin']['doctor_restriction'];
        $disable_option                = false;
        if ($doctor_restriction == 'enabled') {
            if ($role_id == 3) {
                $disable_option = true;
                $doctorid       = $userdata['id'];
            }
        }
        $data["doctor_select"]  = $doctorid;
        $data["disable_option"] = $disable_option;
        $data['fields']         = $this->customfield_model->get_custom_fields('appointment', 1);
        $data['module']         = 'appointment';
        $this->load->view('layout/header', $data);
        $this->load->view('admin/appointment/search', $data);
        $this->load->view('layout/footer', $data);
    }

    /*
    This Function is Used to get appointment records for datatable
     */
    public function getappointmentdatatabletoday()
    {
        $dt_response = $this->appointment_model->getAllappointmentRecord(1);

        $fields      = $this->customfield_model->get_custom_fields('appointment', 1);
        $dt_response = json_decode($dt_response);
        $dt_data     = array();
        if (!empty($dt_response->data)) {
            foreach ($dt_response->data as $key => $value) {

                $row = array();
                //====================================
                $label = "";
                if ($value->appointment_status == "approved") {
                    $label  = "class='badge bg-success text-white'";
                    $status = $this->customlib->getSessionPrefixByType('appointment') . $value->id;
                } else if ($value->appointment_status == "pending") {
                    $label  = "class='badge bg-warning text-dark'";
                    $status = $this->lang->line($value->appointment_status);
                } else if ($value->appointment_status == "cancel") {
                    $label  = "class='badge bg-danger text-white'";
                    $status = $this->lang->line($value->appointment_status);
                }
		
				$action = '';
			 
				// if($value->source != 'Online'){
					$action = "<div class='rowoptionview rowview-btn-top'>";
					$action .= "<a href='#' data-bs-toggle='tooltip' title='" . $this->lang->line('show') . "' class='btn btn-secondary btn-sm'   data-bs-target='#viewModal' onclick='viewDetail(" . $value->id . ")'>  <i class='fa fa-reorder'></i></a>";
					$action .= "<a href='#'  class='btn btn-secondary btn-sm' data-bs-toggle='tooltip'  onclick='printAppointment(" . $value->id . ")' title='" . $this->lang->line('print') . "'><i class='fa fa-print'></i></a>";
					if ($this->rbac->hasPrivilege('reschedule', 'can_view')) {
						$action .= " <a href='#' data-bs-toggle='tooltip' title='" . $this->lang->line('reschedule') . "' class='btn btn-secondary btn-sm'   data-bs-target='#rescheduleModal' onclick='viewreschedule(" . $value->id . ",1)'>  <i class='fa fa-calendar'></i> </a>";
					}
				
					if ($value->appointment_status == 'pending') {
                     
                        if ($this->rbac->hasPrivilege('appointment_approve', 'can_view')) {
                            $action .= "<span class='large-tooltip'><a href='#' class='btn btn-secondary btn-sm'  data-bs-toggle='tooltip' data-bs-target='#rescheduleModal' onclick='viewreschedule(" . $value->id . ",2)' title='" . $this->lang->line('approve_appointment') . "'><i class='fa fa-check' aria-hidden='true'></i></a></span>";
                        }
                     
					}

					$action .= "</div>";
				// }	
				
                $first_action = "<a  href='" . base_url() . 'admin/patient/profile/' . $value->pid . "'  title=''>";
                $appoint_no = "<a  href='" . base_url() . 'admin/patient/profile/' . $value->pid . "'  title=''>" . $status . "</a>";
				
                if (!empty($value->live_consult)) {
                    $live_consult = $this->lang->line(strtolower($value->live_consult));
                } else {
                    $live_consult = '';
                };

                if ($value->gender) {
                    $gender = $this->lang->line(strtolower($value->gender));
                } else {
                    $gender = '';
                }

                //==============================
                $row[] = $first_action . composePatientName($value->patient_name, $value->pid) . "</a>";
                $row[] =  $appoint_no;
                $row[] = $this->customlib->YYYYMMDDHisTodateFormat($value->date, $this->time_format);
                $row[] = $value->mobileno;

                $row[] = $gender;
                $row[] = composeStaffNameByString($value->name, $value->surname, $value->employee_id);
                $row[] = $this->lang->line(strtolower($value->source));
                $row[] = $value->priorityname;
                if ($this->module_lib->hasActive('live_consultation')) {
                    $row[] = $live_consult;
                }
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
				$row[] = composeStaffNameByString($value->created_by_name, $value->created_by_surname, $value->created_by_employee_id);
                $row[]     = "<small " . $label . ">" . $this->lang->line($value->appointment_status) . "</small>";
                $dicount_amt=0;
                $dicount_amt=(($value->standard_amount*$value->discount_percentage)/100);
                $row[]     = amountFormat($value->standard_amount);
                $row[]     = amountFormat($dicount_amt)." (".$value->discount_percentage." %)";
                $row[]     = amountFormat($value->paid_amount) . $action;
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
	
	public function getappointmentdatatableupcoming()
    {
        $dt_response = $this->appointment_model->getAllappointmentRecord(2);

        $fields      = $this->customfield_model->get_custom_fields('appointment', 1);
        $dt_response = json_decode($dt_response);
        $dt_data     = array();
        if (!empty($dt_response->data)) {
            foreach ($dt_response->data as $key => $value) {

                $row = array();
                //====================================
                $label = "";
                if ($value->appointment_status == "approved") {
                    $label  = "class='badge bg-success text-white'";
                    $status = $this->customlib->getSessionPrefixByType('appointment') . $value->id;
                } else if ($value->appointment_status == "pending") {
                    $label  = "class='badge bg-warning text-dark'";
                    $status = $this->lang->line($value->appointment_status);
                } else if ($value->appointment_status == "cancel") {
                    $label  = "class='badge bg-danger text-white'";
                    $status = $this->lang->line($value->appointment_status);
                }
				
				$action = '';
				
				// if($value->source != 'Online'){
					
					$action = "<div class='rowoptionview rowview-btn-top'>";
					$action .= "<a href='#' data-bs-toggle='tooltip' title='" . $this->lang->line('show') . "' class='btn btn-secondary btn-sm'   data-bs-target='#viewModal' onclick='viewDetail(" . $value->id . ")'>  <i class='fa fa-reorder'></i> </a>";
					$action .= "<a href='#'  class='btn btn-secondary btn-sm' data-bs-toggle='tooltip'  onclick='printAppointment(" . $value->id . ")' title='" . $this->lang->line('print') . "'><i class='fa fa-print'></i></a>";
					
					if ($this->rbac->hasPrivilege('reschedule', 'can_view')) {
						$action .= " <a href='#' data-bs-toggle='tooltip' title='" . $this->lang->line('reschedule') . "' class='btn btn-secondary btn-sm'   data-bs-target='#rescheduleModal' onclick='viewreschedule(" . $value->id . ",1)'>  <i class='fa fa-calendar'></i> </a>";
					}
					
					if ($value->appointment_status == 'pending') {
						 
						if ($this->rbac->hasPrivilege('appointment_approve', 'can_view')) {
							$action .= "<span class='large-tooltip'><a href='#' class='btn btn-secondary btn-sm'  data-bs-toggle='tooltip' data-bs-target='#rescheduleModal' onclick='viewreschedule(" . $value->id . ",2)' title='" . $this->lang->line('approve_appointment') . "'><i class='fa fa-check' aria-hidden='true'></i></a></span>";
						}
						 
					}
	
					$action .= "</div>";
				
				// }
			
                $first_action = "<a  href='" . base_url() . 'admin/patient/profile/' . $value->pid . "'  title=''>";
                $appoint_no = "<a  href='" . base_url() . 'admin/patient/profile/' . $value->pid . "'  title=''>" . $status . "</a>";
				
                if (!empty($value->live_consult)) {
                    $live_consult = $this->lang->line(strtolower($value->live_consult));
                } else {
                    $live_consult = '';
                };

                if ($value->gender) {
                    $gender = $this->lang->line(strtolower($value->gender));
                } else {
                    $gender = '';
                }

                //==============================
                $row[] = $first_action . composePatientName($value->patient_name, $value->pid) . "</a>";
                $row[] =  $appoint_no;
                $row[] = $this->customlib->YYYYMMDDHisTodateFormat($value->date, $this->time_format);
                $row[] = $value->mobileno;

                $row[] = $gender;
                $row[] = composeStaffNameByString($value->name, $value->surname, $value->employee_id);
                $row[] = $this->lang->line(strtolower($value->source));
                $row[] = $value->priorityname;
                if ($this->module_lib->hasActive('live_consultation')) {
                    $row[] = $live_consult;
                }
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
				$row[] = composeStaffNameByString($value->created_by_name, $value->created_by_surname, $value->created_by_employee_id);

                $row[]     = "<small " . $label . ">" . $this->lang->line($value->appointment_status) . "</small>";
                $dicount_amt=0;
                $dicount_amt=(($value->standard_amount*$value->discount_percentage)/100);
                $row[]     = amountFormat($value->standard_amount);
                $row[]     = amountFormat($dicount_amt)." (".$value->discount_percentage." %)";
                $row[]     = amountFormat($value->paid_amount) . $action;
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
	
	public function getappointmentdatatableold()
    {
        $dt_response = $this->appointment_model->getAllappointmentRecord(3);
	 

        $fields      = $this->customfield_model->get_custom_fields('appointment', 1);
        $dt_response = json_decode($dt_response);
        $dt_data     = array();
        if (!empty($dt_response->data)) {
            foreach ($dt_response->data as $key => $value) {

                $row = array();
                //====================================
                $label = "";
                if ($value->appointment_status == "approved") {
                    $label  = "class='badge bg-success text-white'";
                    $status = $this->customlib->getSessionPrefixByType('appointment') . $value->id;
                } else if ($value->appointment_status == "pending") {
                    $label  = "class='badge bg-warning text-dark'";
                    $status = $this->lang->line($value->appointment_status);
                } else if ($value->appointment_status == "cancel") {
                    $label  = "class='badge bg-danger text-white'";
                    $status = $this->lang->line($value->appointment_status);
                }

				$action = "";
				// if($value->source != 'Online'){
				
					$action = "<div class='rowoptionview rowview-btn-top'>";
					$action .= "<a href='#' data-bs-toggle='tooltip' title='" . $this->lang->line('show') . "' class='btn btn-secondary btn-sm'   data-bs-target='#viewModal' onclick='viewDetail(" . $value->id . ")'>  <i class='fa fa-reorder'></i> </a>";
					$action .= "<a href='#'  class='btn btn-secondary btn-sm' data-bs-toggle='tooltip'  onclick='printAppointment(" . $value->id . ")' title='" . $this->lang->line('print') . "'><i class='fa fa-print'></i></a>";
					if ($this->rbac->hasPrivilege('reschedule', 'can_view')) {
						$action .= " <a href='#' data-bs-toggle='tooltip' title='" . $this->lang->line('reschedule') . "' class='btn btn-secondary btn-sm'   data-bs-target='#rescheduleModal' onclick='viewreschedule(" . $value->id . ",1)'>  <i class='fa fa-calendar'></i> </a>";
					}
					if ($value->appointment_status == 'pending') {
                     
                        if ($this->rbac->hasPrivilege('appointment_approve', 'can_view')) {
                            $action .= "<span class='large-tooltip'><a href='#' class='btn btn-secondary btn-sm'  data-bs-toggle='tooltip' data-bs-target='#rescheduleModal' onclick='viewreschedule(" . $value->id . ",2)' title='" . $this->lang->line('approve_appointment') . "'><i class='fa fa-check' aria-hidden='true'></i></a></span>";
                        }
                     
					}

					$action .= "</div>";
				
				// }
			
                $first_action = "<a  href='" . base_url() . 'admin/patient/profile/' . $value->pid . "'  title=''>";
                $appoint_no = "<a  href='" . base_url() . 'admin/patient/profile/' . $value->pid . "'  title=''>" . $status . "</a>";
				
                if (!empty($value->live_consult)) {
                    $live_consult = $this->lang->line(strtolower($value->live_consult));
                } else {
                    $live_consult = '';
                };

                if ($value->gender) {
                    $gender = $this->lang->line(strtolower($value->gender));
                } else {
                    $gender = '';
                }

                //==============================
                $row[] = $first_action . composePatientName($value->patient_name, $value->pid) . "</a>";
                $row[] =  $appoint_no;
                $row[] = $this->customlib->YYYYMMDDHisTodateFormat($value->date, $this->time_format);
                $row[] = $value->mobileno;

                $row[] = $gender;
                $row[] = composeStaffNameByString($value->name, $value->surname, $value->employee_id);
                $row[] = $this->lang->line(strtolower($value->source));
                $row[] = $value->priorityname;
                if ($this->module_lib->hasActive('live_consultation')) {
                    $row[] = $live_consult;
                }
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
			    $row[] = composeStaffNameByString($value->created_by_name, $value->created_by_surname, $value->created_by_employee_id);

                $row[]     = "<small " . $label . ">" . $this->lang->line($value->appointment_status) . "</small>";
                $dicount_amt=0;
                $dicount_amt=(($value->standard_amount*$value->discount_percentage)/100);
                $row[]     = amountFormat($value->standard_amount);
                $row[]     = amountFormat($dicount_amt)." (".$value->discount_percentage." %)";
                $row[]     = amountFormat($value->paid_amount) . $action;
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

    public function getDetails()
    {
        $id             = $this->input->post("appointment_id", TRUE);
        $result         = $this->appointment_model->getDetails($id);
        $result["date"] = $this->customlib->YYYYMMDDHisTodateFormat($result['date'], $this->time_format);
        echo json_encode($result);
    }

    public function getDetailsAppointment_old()
    {
        $id     = $this->input->get("appointment_id");
        $result = $this->appointment_model->getDetailsAppointment($id);

        $result['appointment_no'] = $this->customlib->getSessionPrefixByType('appointment') . $id;

        if ($result['start_time']) {
            if ($this->time_format) {
                $result["doctor_shift_name"]       = date('G:i:s', strtotime($result['start_time'])) . " - " . date('G:i:s', strtotime($result['end_time']));
            } else {
                $result["doctor_shift_name"]       = date('g:i A', strtotime($result['start_time'])) . " - " . date('g:i A', strtotime($result['end_time']));
            }
        } else {
            $result["doctor_shift_name"]       = '';
        }

        $result["patients_name"]       = composePatientName($result['patients_name'], $result['patient_id']);
        $result["edit_live_consult"]   = $this->lang->line($result['live_consult']);
        $result["live_consult"]        = $result['live_consult'];
        $result["date"]                = $this->customlib->YYYYMMDDHisTodateFormat($result['date'], $this->time_format);
        $result['custom_fields_value'] = display_custom_fields('appointment', $id);
        $cutom_fields_data             = get_custom_table_values($id, 'appointment');
        $result['field_data']          = $cutom_fields_data;
        $result['patients_gender']     = (isset($result['patients_gender'])) ? $this->lang->line(strtolower($result['patients_gender'])) : "";
        $result['amount']              = $result['paid_amount'];
        $result['discount_percentage']              = $result['discount_percentage'];

        if ($result['payment_mode']) {
            $result['payment_mode']        = $this->lang->line(strtolower($result['payment_mode']));
        } else {
            $result['payment_mode']        = '';
        }

        $result['cheque_no']           = $result['cheque_no'];
        if ($result['cheque_date']) {
            $result['cheque_date']         = $this->customlib->YYYYMMDDTodateFormat($result['cheque_date']);
        } else {
            $result['cheque_date']         =  '';
        }

        $result['attachment']          = $result['attachment'];
        $transaction_id                = $result['transaction_id'];
        if ($result['transaction_id'] != "") {
            $result['transaction_id']      =  $this->customlib->getSessionPrefixByType('transaction_id') . $result['transaction_id'];
        } else {
            $result['transaction_id'] = "";
        }

        $result['department_name']     = $result['department_name'];
        $result['age']                 = $result['age'];
        $result['day']                 = $result['day'];
        $result['month']               = $result['month'];

        if ($result['appointment_status']) {
            $result['appointmentstatus']  = $this->lang->line(strtolower($result['appointment_status']));
        } else {
            $result['appointmentstatus']  =  '';
        }

        if ($result['source']) {
            $result['source']  = $this->lang->line(strtolower($result['source']));
        } else {
            $result['source']  = '';
        }

        $result['patient_age']         = $this->customlib->get_patient_current_age($result['patient_id']);

        if ($result['attachment'] != "") {
            $result["doc"] = "<a href='" . site_url('admin/transaction/download/') . $transaction_id . "' class='btn btn-secondary btn-sm'  title=" . $this->lang->line('download') . "><i class='fa fa-download'></i></a>";
        } else {
            $result["doc"] = "";
        }

        if($result['received_by']){
			$staff_data = $this->staff_model->getstaff($result['received_by']);
			$staff_name = $staff_data["name"] . " " . $staff_data["surname"] . " (" . $staff_data["employee_id"] . ")";
			$result['received_by']  = $staff_name;
		}else{
			$result['received_by']  = '';
		}
        echo json_encode($result);
    }

    public function getDetailsAppointment()
    {
        $id     = $this->input->get("appointment_id");
        $result = $this->appointment_model->getDetailsAppointment($id);

		if ($result['appointment_status'] == 'approved') {
			$result['appointment_no'] = $this->customlib->getSessionPrefixByType('appointment') . $id;
		}else{
			$result['appointment_no'] ="";
		}

        if ($result['start_time']) {
            if ($this->time_format) {
                $result["doctor_shift_name"]       = date('G:i:s', strtotime($result['start_time'])) . " - " . date('G:i:s', strtotime($result['end_time']));
            } else {
                $result["doctor_shift_name"]       = date('g:i A', strtotime($result['start_time'])) . " - " . date('g:i A', strtotime($result['end_time']));
            }
        } else {
            $result["doctor_shift_name"]           = '';
        }


        $result["patients_name"]       = composePatientName($result['patients_name'], $result['patient_id']);
        $result["edit_live_consult"]   = $this->lang->line($result['live_consult']);
        $result["live_consult"]        = $result['live_consult'];
        $result["date"]                = $this->customlib->YYYYMMDDHisTodateFormat($result['date'], $this->time_format);
        $result['custom_fields_value'] = display_custom_fields('appointment', $id);
        $cutom_fields_data             = get_custom_table_values($id, 'appointment');
        $result['field_data']          = $cutom_fields_data;
        $result['patients_gender']     = (isset($result['patients_gender'])) ? $this->lang->line(strtolower($result['patients_gender'])) : "";
        $result['amount']              = $result['paid_amount'];
        $result['discount_percentage'] = $result['discount_percentage'];
        $result['payment_mode_type']   = $result['payment_mode'];//added

        if ($result['payment_mode']) {
            $result['payment_mode']   = $this->lang->line(strtolower($result['payment_mode']));
        } else {
            $result['payment_mode']   = '';
        }

        $result['cheque_no'] = $result['cheque_no'];
        if ($result['cheque_date']) {
            $result['cheque_date']  = $this->customlib->YYYYMMDDTodateFormat($result['cheque_date']);
        } else {
            $result['cheque_date']  =  '';
        }

        $result['attachment']          = $result['attachment'];
        $transaction_id                = $result['transaction_id'];
        if ($result['transaction_id'] != "") {
            $result['transaction_id']      =  $this->customlib->getSessionPrefixByType('transaction_id') . $result['transaction_id'];
        } else {
            $result['transaction_id'] = "";
        }

        $result['department_name']     = $result['department_name'];
        $result['age']                 = $result['age'];
        $result['day']                 = $result['day'];
        $result['month']               = $result['month'];

        if ($result['appointment_status']) {
            $result['appointmentstatus']  = $this->lang->line(strtolower($result['appointment_status']));
        } else {
            $result['appointmentstatus']  =  '';
        }

        if ($result['source']) {
            $result['source']  = $this->lang->line(strtolower($result['source']));
        } else {
            $result['source']  = '';
        }

        $result['patient_age']         = $this->customlib->get_patient_current_age($result['patient_id']);

        if ($result['attachment'] != "") {
            $result["doc"] = "<a href='" . site_url('admin/transaction/download/') . $transaction_id . "' class='btn btn-secondary btn-sm'  title=" . $this->lang->line('download') . "><i class='fa fa-download'></i></a>";
        } else {
            $result["doc"] = "";
        }

        if($result['received_by']){
            $staff_data = $this->staff_model->getstaff($result['received_by']);
            $staff_name = $staff_data["name"] . " " . $staff_data["surname"] . " (" . $staff_data["employee_id"] . ")";
            $result['received_by']  = $staff_name;
        }else{
            $result['received_by']  = '';
        }

        $userdata                = $this->customlib->getUserData();//added
        $result['role_id']       = $userdata['role_id'];//added

  
        echo json_encode($result);
    }


    public function getappDetails($id)
    {
        $result         = $this->appointment_model->getDetails($id);
        $result["date"] = $this->customlib->YYYYMMDDHisTodateFormat($result['date'], $this->time_format);
        echo json_encode($result);
    }

    /*
	This Function is Used to Delete created Appointment patient
	*/
    public function delete($id)
    {
        if (!empty($id)) {
            // SaaS: release the appointment's payment attachment from storage quota before deletion.
            $saas_transaction = $this->transaction_model->getTransactionByAppointmentId($id);
            if (!empty($saas_transaction) && !empty($saas_transaction->attachment)) {
                $doc_path = $saas_transaction->attachment;
                $dir      = $this->media_storage->resolveAttachmentDir($doc_path);
                $kb       = $this->media_storage->getUploadedFileSize($doc_path, $dir);
                if ($kb > 0) {
                    try {
                        $this->saasvalidation->deleteResouceQuota('storage', $kb);
                    } catch (Exception $e) {
                        log_message('error', 'SaaS storage quota release failed (appointment delete): ' . $e->getMessage());
                    }
                }
                $this->media_storage->filedelete($doc_path, $dir);
            }

            $appointment_details = $this->appointment_model->getDetails($id);
            $visit_details_id    = $appointment_details['visit_details_id'] ?? null;
            $visit_data          = $visit_details_id ? $this->patient_model->getvisitDetailsByVisitId($visit_details_id) : null;
            $opd_id              = $visit_data['opdid'] ?? null;

            $this->appointment_model->delete($id, $visit_details_id, $opd_id);

            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('delete_message'));
        } else {
            $array = array('status' => 'fail', 'error' => '', 'message' => '');
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
     * SaaS count-limit pre-check (form_validation callback).
     * Returns false (blocking the save) when adding one more record would push
     * the tenant over its count quota (e.g. no_of_appointment). Param string is
     * "resource_name,quantity"; SaasValidation sets the error message.
     */
    public function validateCanAddNewResource($input, $resource_name)
    {
        list($resource_name, $quantity) = explode(',', $resource_name);
        return $this->saasvalidation->validateCanAddNewResource($input, $resource_name, $quantity);
    }
	
    /*
	This Function is Used to move patient from appointment to other module
	*/
    public function move($id)
    {
        $appointment_details = $this->appointment_model->getDetails($id);
        $patient_name        = $appointment_details['patient_name'];
        $gender              = $appointment_details['gender'];
        $email               = $appointment_details['email'];
        $phone               = $appointment_details['mobileno'];
        $doctor              = $appointment_details['doctor'];
        $note                = $appointment_details['message'];
        $appointment_date    = $appointment_details['date'];
        $amount              = $appointment_details['amount'];
        $live_consult        = $appointment_details['live_consult'];

        $check_patient_id = $this->patient_model->getMaxId();
        if (empty($check_patient_id)) {
            $check_patient_id = 1000;
        }
        $patient_id   = $check_patient_id + 1;
        $patient_data = array(
            'patient_name'      => $patient_name,
            'mobileno'          => $phone,
            'email'             => $email,
            'gender'            => $gender,
            'patient_unique_id' => $patient_id,
            'note'              => $note,
            'is_active'         => 'yes',
        );

        $insert_id          = $this->patient_model->add_patient($patient_data);
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
            move_uploaded_file($_FILES["file"]["tmp_name"], "./uploads/patient_images/" . $img_name);
            $data_img = array('id' => $insert_id, 'image' => 'uploads/patient_images/' . $img_name);
            $this->patient_model->add($data_img);
        }
        if (isset($insert_id)) {
            $check_opd_id = $this->patient_model->getMaxOPDId();
            $opdnoid      = $check_opd_id + 1;
            $opd_data = array(
                'appointment_date' => $appointment_date,
                'opd_no'           => 'OPDN' . $opdnoid,
                'cons_doctor'      => $doctor,
                'patient_id'       => $insert_id,
                'amount'           => $amount,
                'live_consult'     => $live_consult,
            );
            $opd_id = $this->patient_model->add_opd($opd_data);

            if (isset($opd_id)) {
                $this->appointment_model->deleteAppointment($id);
            }
        }

        redirect('admin/appointment/search');
    }

    public function moveipd()
    {
        $custom_fields = $this->customfield_model->getByBelong('ipd');

        foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
            if ($custom_fields_value['validation']) {
                $custom_fields_id   = $custom_fields_value['id'];
                $custom_fields_name = $custom_fields_value['name'];
                $this->form_validation->set_rules("custom_fields[ipd][" . $custom_fields_id . "]", $custom_fields_name, 'trim|required');
            }
        }
        $this->form_validation->set_rules('bed_no', $this->lang->line('bed_no'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('appointment_date', $this->lang->line('appointment_date'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('consultant_doctor', $this->lang->line('consultant_doctor'), 'trim|required|xss_clean');
        if ($this->form_validation->run() == false) {
            $msg = array(
                'appointment_date'  => form_error('appointment_date'),
                'bed_no'            => form_error('bed_no'),
                'consultant_doctor' => form_error('consultant_doctor'),
                'opd_id'            => form_error('opd_id'),

            );
            if (!empty($custom_fields)) {
                foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
                    if ($custom_fields_value['validation']) {
                        $custom_fields_id                                            = $custom_fields_value['id'];
                        $custom_fields_name                                          = $custom_fields_value['name'];
                        $error_msg2["custom_fields[ipd][" . $custom_fields_id . "]"] = form_error("custom_fields[ipd][" . $custom_fields_id . "]");
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

            $appointment_id      = $this->input->post('appointment_id', TRUE);
            $appointment_details = $this->appointment_model->getDetails($appointment_id);
            $bed_no              = $this->input->post('bed_no', TRUE);
            $bed_group_id        = $this->input->post('bed_group_id', TRUE);
            $ipd_details         = array(
                'patient_id'      => $appointment_details['patient_id'],
                'bed'             => $bed_no,
                'bed_group_id'    => $bed_group_id,
                'height'          => $this->input->post('height', TRUE),
                'weight'          => $this->input->post('weight', TRUE),
                'pulse'           => $this->input->post('pulse', TRUE),
                'temperature'     => $this->input->post('temperature', TRUE),
                'respiration'     => $this->input->post('respiration', TRUE),
                'bp'              => $this->input->post('bp', TRUE),
                'case_type'       => $this->input->post('case', TRUE),
                'casualty'        => $this->input->post('casualty', TRUE),
                'symptoms'        => $this->input->post('symptoms', TRUE),
                'known_allergies' => $this->input->post('symptoms', TRUE),
                'date'            => $this->customlib->dateFormatToYYYYMMDDHis($this->input->post('appointment_date', TRUE), $this->time_format),
                'refference'      => $this->input->post('refference', TRUE),
                'cons_doctor'     => $this->input->post('consultant_doctor', TRUE),
                'live_consult'    => $this->input->post('live_consult', TRUE),
                'discharged'      => 'no',
            );

            $bed_history = array(
                "bed_group_id" => $bed_group_id,
                "bed_id"       => $bed_no,
                "from_date"    => date("Y-m-d H:i:s"),
                "is_active"    => "yes",
            );

            $ipd_id = $this->appointment_model->moveToIpd($ipd_details, $bed_history, $appointment_id);
            if ($ipd_id) {
                $array = array('status' => 'success', 'message' => $this->lang->line('success_message'), 'ipd_id' => $ipd_id);
            } else {
                $msg   = array('no_insert' => $this->lang->line('something_went_wrong'));
                $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
            }
        }
        echo json_encode($array);
    }

    public function getpatientDetails()
    {
        $id     = $this->input->post("patient_id", TRUE);
        $result = $this->appointment_model->getpatientDetails($id);
        echo json_encode($result);
    }

    public function checkvalidation()
    {
        $search = $this->input->post('search');
        $this->form_validation->set_rules('search_type', $this->lang->line('search_type'), 'trim|required|xss_clean');

        if ($this->input->post('search_type') == 'period') {
            $this->form_validation->set_rules('date_from', $this->lang->line('date_from'), 'trim|required|xss_clean');
            $this->form_validation->set_rules('date_to', $this->lang->line('date_to'), 'trim|required|xss_clean');
        }

        if ($this->form_validation->run() == false) {
            if ($this->input->post('search_type') == 'period') {
                $msg1 = array(
                    'date_from' => form_error('date_from'),
                    'date_to' => form_error('date_to'),
                );
            }

            $msg2 = array(
                'search_type' => form_error('search_type'),
            );

            if ($this->input->post('search_type') == 'period') {
                $msg = array_merge($msg1, $msg2);
            } else {
                $msg = $msg2;
            }

            $json_array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $param = array(
                'search_type'      => $this->input->post('search_type'),
                'collect_staff'    => $this->input->post('collect_staff'),
                'date_from'        => $this->input->post('date_from'),
                'date_to'          => $this->input->post('date_to'),
                'shift'            => $this->input->post('shift'),
                'priority'         => $this->input->post('priority'),
                'appointment_type' => $this->input->post('appointment_type'),
                'appointment_status' => $this->input->post('appointment_status'),
            );

            $json_array = array('status' => 'success', 'error' => '', 'param' => $param, 'message' => $this->lang->line('success_message'));
        }
        echo json_encode($json_array);
    }

    public function appointmentreport()
    {
        $this->session->set_userdata('top_menu', 'Reports');
        $this->session->set_userdata('sub_menu', 'reports/appointment');
        $this->session->set_userdata('subsub_menu', 'reports/appointment/appointmentreport');

        $doctorlist                    = $this->staff_model->getEmployeeByRoleID(3);
        $data['doctorlist']            = $doctorlist;
        $custom_fields                 = $this->customfield_model->get_custom_fields('appointment', '', '', 1);
        $data['fields']                = $custom_fields;
        $data['appoint_priority_list'] = $this->appoint_priority_model->appoint_priority_list();
        $data['appointment_type']      = $this->config->item('appointment_type');
        $data["searchlist"]            = $this->search_type;
        $data["appointment_status"]    = $this->appointment_status;

        $data['module'] = 'reports';
        $this->load->view('layout/header', $data);
        $this->load->view('admin/appointment/appointmentReport', $data);
        $this->load->view('layout/footer', $data);
    }

    public function appointmentreports()
    {
        $search['search_type']   = $this->input->post('search_type');
        $search['collect_staff'] = $this->input->post('collect_staff');
        $search['date_from']     = $this->input->post('date_from');
        $search['date_to']       = $this->input->post('date_to');
        $shift                   = $this->input->post('shift');
        $priority                = $this->input->post('priority');
        $appointment_type        = $this->input->post('appointment_type');
        $appointment_status      = $this->input->post('appointment_status');
        $start_date              = '';
        $end_date                = '';
        $fields                  = $this->customfield_model->get_custom_fields('appointment', '', '', 1);
        if ($search['search_type'] == 'period') {

            $start_date = $this->customlib->dateFormatToYYYYMMDD($search['date_from']);
            $end_date   = $this->customlib->dateFormatToYYYYMMDD($search['date_to']);
        } else {

            if (isset($search['search_type']) && $search['search_type'] != '') {
                $dates               = $this->customlib->get_betweendate($search['search_type']);
                $data['search_type'] = $search['search_type'];
            } else {
                $dates               = $this->customlib->get_betweendate('this_year');
                $data['search_type'] = '';
            }

            $start_date = $dates['from_date'];
            $end_date   = $dates['to_date'];
        }

        $reportdata  = $this->report_model->appointmentRecord($start_date, $end_date, $search['collect_staff'], $shift, $priority, $appointment_type, $appointment_status);
        $reportdata  = json_decode($reportdata);
        $dt_data     = array();
        $paid_amount = 0;
        $currency_symbol = $this->customlib->getHospitalCurrencyFormat();
        if (!empty($reportdata->data)) {
            foreach ($reportdata->data as $key => $value) {
                $paid_amount += $value->paid_amount;

                if ($value->appointment_status == "approved") {
                    $label = "class='label label-success'";
                } else if ($value->appointment_status == "pending") {
                    $label = "class='label label-warning'";
                } else if ($value->appointment_status == "cancel") {
                    $label = "class='label label-danger'";
                } else {
                    $label = "class=' '";
                }
                $row = array();

                $row[] = composePatientName($value->patient_name, $value->patient_id);
                $row[] = $this->customlib->YYYYMMDDHisTodateFormat($value->date, $this->time_format);
                $row[] = $value->mobileno;
                $row[] = ($value->gender) ? $this->lang->line(strtolower($value->gender)):"";
                $row[] = composeStaffNameByString($value->name, $value->surname, $value->employee_id);
                $row[] = $this->lang->line(strtolower($value->source));
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
                $row[]     = $value->discount_percentage;
                if ($value->paid_amount) {
                    $row[]     = $value->paid_amount;
                } else {
                    $row[]     = '0.00';
                }

                $row[]     = "<small " . $label . " >" . $this->lang->line($value->appointment_status) . "</small>";
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
            $footer_row[] = "<b>" . $currency_symbol . (number_format($paid_amount, 2, '.', '')) . "<br/>";
            $footer_row[] = "";
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

    public function getDoctorFees()
    {
        $doctor_id      = $this->input->post("doctor_id");
        $shift_details  = $this->onlineappointment_model->getShiftDetails($doctor_id);
		
        if (!empty($shift_details)) {
            $charge_details = $this->charge_model->getChargeDetailsById($shift_details['charge_id']);
            echo json_encode(
                array(
                    'status' => 1,
                    "fees"      => isset($charge_details->standard_charge) ? amountFormat($charge_details->standard_charge + ($charge_details->standard_charge * $charge_details->percentage / 100)) : "",
                    "charge_id" => $shift_details['charge_id']
                )
            );
        } else {
            echo  json_encode(['status' => 0]);
        }
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

    public function reschedule_old()
    {
        $custom_fields = $this->customfield_model->getByBelong('appointment');
        if (!empty($custom_fields)) {
            foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
                if ($custom_fields_value['validation']) {
                    $custom_fields_id   = $custom_fields_value['id'];
                    $custom_fields_name = $custom_fields_value['name'];
                    $this->form_validation->set_rules("custom_fields[appointment][" . $custom_fields_id . "]", $custom_fields_name, 'trim|required');
                }
            }
        }
        $this->form_validation->set_rules('appointment_date', $this->lang->line('appointment_date'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('rglobal_shift', $this->lang->line('shift'), 'trim|required');
        $this->form_validation->set_rules('rslot', $this->lang->line('slot'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('edit_appointment_status', $this->lang->line('status'), 'trim|required|xss_clean');

        $consult      = $this->input->post('live_consult', TRUE);
        if ($consult == 'yes') {
            if (!$this->session->has_userdata('zoom_access_token')) {
                $this->form_validation->set_rules('zoom_live', $this->lang->line('zoom_access_token_is_invalid_or_not_generated_from_zoom_setting'), 'trim|required|xss_clean', array('required' => $this->lang->line('zoom_access_token_is_invalid_or_not_generated_from_zoom_setting')));
            }
        }

        if ($this->form_validation->run() == false) {
            $msg = array(
                'appointment_date' => form_error('appointment_date'),
                'rglobal_shift'    => form_error('rglobal_shift'),
                'rslot'            => form_error('rslot'),
                'edit_appointment_status'            => form_error('edit_appointment_status'),
                'zoom_live'            => form_error('zoom_live'),
            );
            if (!empty($custom_fields)) {
                foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
                    if ($custom_fields_value['validation']) {
                        $custom_fields_id                                                    = $custom_fields_value['id'];
                        $custom_fields_name                                                  = $custom_fields_value['name'];
                        $error_msg2["custom_fields[appointment][" . $custom_fields_id . "]"] = form_error("custom_fields[appointment][" . $custom_fields_id . "]");
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
            $appointment_id  = $this->input->post('appointment_id', TRUE);
            $date            = $this->input->post('appointment_date', TRUE);
            $day             = date("l", strtotime($date));
            $rdoctor_id      = $this->input->post('rdoctor_id', TRUE);
            $rglobal_shift   = $this->input->post('rglobal_shift', TRUE);

            $getDoctorShiftTimeId = $this->onlineappointment_model->getDoctorShiftTimeId($rdoctor_id, $rglobal_shift, $day);

            $appointment = array(
                'id'                     => $appointment_id,
                'date'                   => $this->customlib->dateFormatToYYYYMMDDHis($date, $this->time_format),
                'priority'               => $this->input->post('priority', TRUE),
                'doctor_shift_time_id'   => $this->input->post('rslot', TRUE),
                'message'                => $this->input->post('message', TRUE),
                'live_consult'           => $this->input->post('live_consult', TRUE),
                'appointment_status'     => $this->input->post('edit_appointment_status', TRUE),
                'doctor_global_shift_id' => $rglobal_shift,
            );

            $this->appointment_model->update($appointment);

            /*status code */
            $appointment_details = $this->appointment_model->getDetails($appointment_id);
            $transaction_data    = $this->transaction_model->getTransactionByAppointmentId($appointment_id);
            $appointment_payment = $this->appointment_model->getPaymentByAppointmentId($appointment_id);
          
            $charges             = $this->charge_model->getChargeByChargeId($appointment_payment->charge_id);
			
                $opd_details         = array(
                    'patient_id'   => $appointment_details['patient_id'],
                    'generated_by' => $this->customlib->getStaffID(),
                ); 
                $consult      = $this->input->post('live_consult', TRUE);
                $visit_details = array(
                    'appointment_date'  => $appointment_details['date'],
                    'opd_details_id'    => 0,
                    'cons_doctor'       => $appointment_details['doctor'],
                    'generated_by'      => $this->customlib->getLoggedInUserID(),
                    'patient_charge_id' => null,
                    'transaction_id'    => $transaction_data->id,
                    'can_delete'        => 'no',
                    'live_consult'      => $consult,
                );
                $staff_data = $this->staff_model->getStaffByID($appointment_details['doctor']);
                $staff_name = composeStaffName($staff_data);
                $charge     = array(
                    'opd_id'          => 0,
                    'date'            => date('Y-m-d H:i:s'),
                    'charge_id'       => $appointment_payment->charge_id,
                    'qty'             => 1,
                    'apply_charge'    => $charges['standard_charge'],
                    'standard_charge' => $charges['standard_charge'],
                    'amount'          => $appointment_payment->paid_amount,
                    'created_at'      => date('Y-m-d H:i:s'),
                    'note'            => null,
                    'tax'             => $charges['percentage'],
                );

                $doctor_fees = $this->input->post('doctor_fees', TRUE);
                if ($doctor_fees == 0) {
                    $discount_percentage = 0;
                } else {
                    $discount_percentage = $this->input->post('discount_percentage', TRUE);
                }

                $amount_paid1 = $doctor_fees - calculatePercent($doctor_fees, $discount_percentage);
                $amount_paid  = $amount_paid1 + calculatePercent($amount_paid1, $charges['percentage']);

                $appointment_fees = array(
                    'standard_amount'     => $doctor_fees,
                    'paid_amount'         => $amount_paid,
                    'discount_percentage' => $discount_percentage,
                    'tax'                 => $charges['percentage'],
                );
                $this->appointment_model->updateappointmentpayment($appointment_id, $appointment_fees);

                if ($this->input->post('edit_appointment_status', TRUE) == "approved") {

                    $opdidbyappointmentid	=	$this->appointment_model->getopdidbyappointmentid($appointment_id);

					$appointment_opd_details=[
						'standard_charge'    => $doctor_fees,
						'apply_charge'    => $doctor_fees,
						'amount'    => $amount_paid,
						'discount_percentage' => $discount_percentage,
					];

					if (!empty($opdidbyappointmentid) && !empty($opdidbyappointmentid->opd_id)) {
						$this->appointment_model->updateappointmentpatientcharges($appointment_opd_details, $opdidbyappointmentid->opd_id);
					}

				}
				 
                if ($appointment_details['visit_details_id'] == '') {					
					 
					$opd_visit_id = $this->appointment_model->moveToOpd($opd_details, $visit_details, $charge, $appointment_id);                   
 
					$visit_detail = $this->patient_model->getVisitDetailByid($opd_visit_id);

					// SaaS: increment the OPD count usage by 1 (count-based resource: no_of_opd).
					// moveToOpd() created a new OPD visit from the approved appointment; mirror the
					// +1 done in normal OPD registration. Own try/catch so a quota-API hiccup does not abort.
					try {
						$this->saasvalidation->updateResouceQuota('no_of_opd', 1);
					} catch (Exception $e) {
						log_message('error', 'SaaS no_of_opd quota increment failed (appointment reschedule->opd): ' . $e->getMessage());
					}

                    $setting_result   = $this->setting_model->getzoomsetting();
                    $opdduration      = $setting_result->opd_duration;
                    if ($consult == 'yes') {
                        $api_type = 'global';
                        $params   = array(
                            'zoom_api_key'    => "",
                            'zoom_api_secret' => "",
                        );

                        $title = 'Online consult for ' . $this->customlib->getSessionPrefixByType('opd_no') . $visit_detail->opd_details_id . " Checkup ID " . $visit_detail->id;
                        $this->load->library('zoom_api', $params);
                        $insert_array = array(
                            'staff_id'         => $appointment_details['doctor'],
                            'visit_details_id' => $visit_detail->id,
                            'title'            => $title,
                            'date'             => $this->customlib->dateFormatToYYYYMMDDHis($this->input->post('appointment_date'), $this->time_format),
                            'duration'         => $opdduration,
                            'created_id'       => $this->customlib->getStaffID(),
                            'password'         => random_string(),
                            'api_type'         => $api_type,
                            'host_video'       => 1,
                            'client_video'     => 1,
                            'purpose'          => 'consult',
                            'timezone'         => $this->customlib->getTimeZone(),
                        );

                        $response = $this->zoom_api->createAMeeting($insert_array);

                        if (!empty($response)) {
                            if (isset($response->id)) {
                                $insert_array['return_response'] = json_encode($response);
                                $this->conference_model->add($insert_array);
                            }
                        }
                    }
                }			 

            /* end status code */
            $custom_field_post = $this->input->post("custom_fields[appointment]");
            if (!empty($custom_fields)) {
                foreach ($custom_field_post as $key => $value) {
                    $check_field_type = $this->input->post("custom_fields[appointment][" . $key . "]", TRUE);
                    $field_value      = is_array($check_field_type) ? implode(",", $check_field_type) : $check_field_type;
                    $array_custom     = array(
                        'belong_table_id' => $appointment_id,
                        'custom_field_id' => $key,
                        'field_value'     => $field_value,
                    );
                    $custom_value_array[] = $array_custom;
                }
                $this->customfield_model->updateRecord($custom_value_array, $appointment_id, 'appointment');
            }

            $sender_details = array('patient_id' => $appointment_details["patient_id"], 'appointment_id' => $appointment_id);
            $doctor_details = $this->notificationsetting_model->getstaffDetails($this->input->post('rdoctor_id'));
            $appointment_date = $this->input->post('appointment_date');
            $date             = $this->customlib->dateFormatToYYYYMMDDHis($appointment_date, $this->time_format);

            $event_data     = array(
                'appointment_date' => $this->customlib->YYYYMMDDHisTodateFormat($date, $this->time_format),
                'patient_id'       => $appointment_details["patient_id"],
                'doctor_id'        => $this->input->post('rdoctor_id'),
                'doctor_name'      => composeStaffNameByString($doctor_details['name'], $doctor_details['surname'], $doctor_details['employee_id']),
                'message'          => $this->input->post('message'),
                'appointment_status' =>  $this->input->post('edit_appointment_status'),
            );

            $sender_details = array('patient_id' => $appointment_details["patient_id"], 'appointment_id' => $appointment_id);

            if ($this->input->post('edit_appointment_status') == 'approved') {
                $this->mailsmsconf->mailsms('appointment_approved', $sender_details);
                $this->system_notification->send_system_notification('notification_appointment_created', $event_data);
                $this->system_notification->send_system_notification('appointment_approved', $event_data);
            }

            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
        }
        echo json_encode($array);
    }



     public function reschedule()
    {
        $custom_fields = $this->customfield_model->getByBelong('appointment');
        if (!empty($custom_fields)) {
            foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
                if ($custom_fields_value['validation']) {
                    $custom_fields_id   = $custom_fields_value['id'];
                    $custom_fields_name = $custom_fields_value['name'];
                    $this->form_validation->set_rules("custom_fields[appointment][" . $custom_fields_id . "]", $custom_fields_name, 'trim|required');
                }
            }
        }
        $this->form_validation->set_rules('appointment_date', $this->lang->line('appointment_date'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('rglobal_shift', $this->lang->line('shift'), 'trim|required');
        $this->form_validation->set_rules('rslot', $this->lang->line('slot'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('edit_appointment_status', $this->lang->line('status'), 'trim|required|xss_clean');

        $consult      = $this->input->post('live_consult', TRUE);
        if ($consult == 'yes') {
            if (!$this->session->has_userdata('zoom_access_token')) {
                $this->form_validation->set_rules('zoom_live', $this->lang->line('zoom_access_token_is_invalid_or_not_generated_from_zoom_setting'), 'trim|required|xss_clean', array('required' => $this->lang->line('zoom_access_token_is_invalid_or_not_generated_from_zoom_setting')));
            }
        }

        if ($this->form_validation->run() == false) {
            $msg = array(
                'appointment_date'          => form_error('appointment_date'),
                'rglobal_shift'             => form_error('rglobal_shift'),
                'rslot'                     => form_error('rslot'),
                'edit_appointment_status'   => form_error('edit_appointment_status'),
                'zoom_live'                 => form_error('zoom_live'),
            );
            if (!empty($custom_fields)) {
                foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
                    if ($custom_fields_value['validation']) {
                        $custom_fields_id                                                    = $custom_fields_value['id'];
                        $custom_fields_name                                                  = $custom_fields_value['name'];
                        $error_msg2["custom_fields[appointment][" . $custom_fields_id . "]"] = form_error("custom_fields[appointment][" . $custom_fields_id . "]");
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
            $appointment_id  = $this->input->post('appointment_id', TRUE);
            $date            = $this->input->post('appointment_date', TRUE);
            $day             = date("l", strtotime($date));
            $rdoctor_id_r    = $this->input->post('rdoctor_id', TRUE);
            $rglobal_shift_r = $this->input->post('rglobal_shift', TRUE);

            $getDoctorShiftTimeId = $this->onlineappointment_model->getDoctorShiftTimeId($rdoctor_id_r, $rglobal_shift_r, $day);

            $appointment = array(
                'id'                     => $appointment_id,
                'date'                   => $this->customlib->dateFormatToYYYYMMDDHis($date, $this->time_format),
                'priority'               => $this->input->post('priority', TRUE),
                'doctor_shift_time_id'   => $this->input->post('rslot', TRUE),
                'message'                => $this->input->post('message', TRUE),
                'live_consult'           => $this->input->post('live_consult', TRUE),
                'appointment_status'     => $this->input->post('edit_appointment_status', TRUE),
                'doctor_global_shift_id' => $rglobal_shift_r,
            );

            // SaaS: capture the appointment status BEFORE the update so we can detect a
            // transition into 'approved' below. The OPD is counted only on the first
            // approval — never when re-saving an already-approved appointment.
            $saas_old_appointment = $this->appointment_model->getDetails($appointment_id);
            $saas_old_status      = isset($saas_old_appointment['appointment_status']) ? $saas_old_appointment['appointment_status'] : '';

            $this->appointment_model->update($appointment);

        //=============================================customization code added=============================================//
        $appointment_result = $this->appointment_model->getDetailsAppointment($appointment_id);//added

        if($appointment_result['source']=='Online' && $appointment_result['transaction_id']=="" && $appointment_result['paid_amount'] === null){
            //when appointment is online and pending
            //appointment is pending means payment not done so that record is not saved in appointment_payment and transaction table

            $doctor_fees_r = $this->input->post('doctor_fees', TRUE);
            if ($doctor_fees_r == 0) {
                $discount_percentage = 0;
            } else {
                if (empty($this->input->post('discount_percentage', TRUE))) {
                    $discount_percentage = 0;
                } else {
                    $discount_percentage = $this->input->post('discount_percentage', TRUE);
                }
            }

            $doctor_fees = (float) $doctor_fees_r;
            $amount_paid = $doctor_fees - calculatePercent($doctor_fees, $discount_percentage);

            $payment_data = array(
                'appointment_id'      => $appointment_id,
                'standard_amount'     => $doctor_fees,
                'paid_amount'         => $amount_paid,
                'charge_id'           => $this->input->post('charge_id_edit', TRUE),
                'discount_percentage' => $discount_percentage,
                'payment_type'        => 'Offline',
                'date'                => date("Y-m-d H:i:s"),
            );
            $payment_section = $this->config->item('payment_section');
            $staff_id        = $this->customlib->getLoggedInUserID();
            $edit_pay_mode   = $this->input->post("edit_payment_mode", TRUE);

            $transaction_array = array(
                'amount'         => $amount_paid,
                'patient_id'     => $appointment_result['patient_id'],
                'section'        => $payment_section['appointment'],
                'type'           => 'payment',
                'appointment_id' => $appointment_id,
                'payment_mode'   => $edit_pay_mode,
                'payment_date'   => date('Y-m-d H:i:s'),
                'received_by'    => $staff_id,
            );

            if ($edit_pay_mode == "Cheque") {
                $transaction_array['cheque_date']     = $cheque_date;
                $transaction_array['cheque_no']       = $this->input->post('cheque_no', TRUE);
                if (isset($_FILES["document"]) && !empty($_FILES['document']['name'])) {
                    $file_name = $this->media_storage->fileupload("document",'./uploads/payment_document/');
                    $transaction_array['attachment']      = $file_name;
                    $transaction_array['attachment_name'] = $_FILES["document"]["name"];

                    // SaaS: add the uploaded document's size to the storage quota usage.
                    try {
                        $saas_quota_result = $this->saasvalidation->updateStorageLimit('storage', ['document']);
                        // Capture silent API failures (lib returns JSON status:false instead of throwing).
                        if (is_string($saas_quota_result)) {
                            $saas_quota_decoded = json_decode($saas_quota_result);
                            if (isset($saas_quota_decoded->status) && $saas_quota_decoded->status === false) {
                                log_message('error', 'SaaS storage quota update returned failure (appointment reschedule): ' . ($saas_quota_decoded->message ?? 'unknown'));
                            }
                        }
                    } catch (Exception $e) {
                        log_message('error', 'SaaS storage quota update failed (appointment reschedule): ' . $e->getMessage());
                    }
                }
            }

            $this->appointment_model->saveAppointmentPayment($payment_data, $transaction_array);
        }
        //=============================================customization code added=============================================//

            /*status code */
            $appointment_details = $this->appointment_model->getDetails($appointment_id); //ok
            $transaction_data    = $this->transaction_model->getTransactionByAppointmentId($appointment_id);
            $appointment_payment = $this->appointment_model->getPaymentByAppointmentId($appointment_id);
            $charges             = !empty($appointment_payment) ? $this->charge_model->getChargeByChargeId($appointment_payment->charge_id) : null;

            if (!empty($appointment_payment) && !empty($charges)):
            $apply_charge        = $charges['standard_charge'] + ($charges['standard_charge'] * ($charges['percentage'] / 100));
            
            $opd_details         = array(
                'patient_id'   => $appointment_details['patient_id'],
                'generated_by' => $this->customlib->getStaffID(),
            ); 
                $consult      = $this->input->post('live_consult', TRUE);
                //added
                if ($consult == '') { 
                    $consult = 'no';
                }
                //added
                $visit_details = array(
                    'appointment_date'  => $appointment_details['date'],
                    'opd_details_id'    => 0,
                    'cons_doctor'       => $appointment_details['doctor'],
                    'generated_by'      => $this->customlib->getLoggedInUserID(),
                    'patient_charge_id' => null,
                    'transaction_id'    => $transaction_data->id,
                    'can_delete'        => 'no',
                    'live_consult'      => $consult,

                );
                $staff_data = $this->staff_model->getStaffByID($appointment_details['doctor']);
                $staff_name = composeStaffName($staff_data);
                $charge     = array(
                    'opd_id'          => 0,
                    'date'            => date('Y-m-d H:i:s'),
                    'charge_id'       => $appointment_payment->charge_id,
                    'qty'             => 1,
                    'apply_charge'    => $charges['standard_charge'],
                    'standard_charge' => $charges['standard_charge'],
                    'amount'          => $appointment_payment->paid_amount,
                    'created_at'      => date('Y-m-d H:i:s'),
                    'note'            => null,
                    'tax'             => $charges['percentage'],
                );

                $doctor_fees = $this->input->post('doctor_fees', TRUE);
                if ($doctor_fees == 0) {
                    $discount_percentage = 0;
                } else {
                    $discount_percentage = $this->input->post('discount_percentage', TRUE);
                }

                $amount_paid      = $doctor_fees - calculatePercent($doctor_fees, $discount_percentage);
                $appointment_fees = array(
                    'standard_amount'     => $doctor_fees,
                    'paid_amount'         => $amount_paid,
                    'discount_percentage' => $discount_percentage,
                );
                $this->appointment_model->updateappointmentpayment($appointment_id, $appointment_fees);

                if ($this->input->post('edit_appointment_status', TRUE) == "approved") {

                    $opdidbyappointmentid   =   $this->appointment_model->getopdidbyappointmentid($appointment_id);

                    $appointment_opd_details=[
                        'standard_charge'       => $doctor_fees,
                        'apply_charge'          => $doctor_fees,
                        'amount'                => $amount_paid,
                        'discount_percentage'   => $discount_percentage,
                    ];

                    if (!empty($opdidbyappointmentid) && !empty($opdidbyappointmentid->opd_id)) {
                        $this->appointment_model->updateappointmentpatientcharges($appointment_opd_details, $opdidbyappointmentid->opd_id);
                    }

                }
                 
                if ($appointment_details['visit_details_id'] == '') {   
                     
                    $opd_visit_id = $this->appointment_model->moveToOpd($opd_details, $visit_details, $charge, $appointment_id);

                    // SaaS: increment the OPD count usage by 1 (count-based resource: no_of_opd).
                    // moveToOpd() created a new OPD visit from the approved appointment; mirror the
                    // +1 done in normal OPD registration. Own try/catch so a quota-API hiccup does not abort.
                    try {
                        $this->saasvalidation->updateResouceQuota('no_of_opd', 1);
                    } catch (Exception $e) {
                        log_message('error', 'SaaS no_of_opd quota increment failed (appointment reschedule->opd): ' . $e->getMessage());
                    }

                    $visit_detail = $this->patient_model->getVisitDetailByid($opd_visit_id['visit_details_id']);
                    $setting_result   = $this->setting_model->getzoomsetting();
                    $opdduration      = $setting_result->opd_duration;
                    if ($consult == 'yes') {
                        $api_type = 'global';
                        $params   = array(
                            'zoom_api_key'    => "",
                            'zoom_api_secret' => "",
                        );

                        $title = 'Online consult for ' . $this->customlib->getSessionPrefixByType('opd_no') . $visit_detail->opd_details_id . " Checkup ID " . $visit_detail->id;
                        $this->load->library('zoom_api', $params);
                        $insert_array = array(
                            'staff_id'         => $appointment_details['doctor'],
                            'visit_details_id' => $visit_detail->id,
                            'title'            => $title,
                            'date'             => $this->customlib->dateFormatToYYYYMMDDHis($this->input->post('appointment_date'), $this->time_format),
                            'duration'         => $opdduration,
                            'created_id'       => $this->customlib->getStaffID(),
                            'password'         => random_string(),
                            'api_type'         => $api_type,
                            'host_video'       => 1,
                            'client_video'     => 1,
                            'purpose'          => 'consult',
                            'timezone'         => $this->customlib->getTimeZone(),
                        );

                        $response = $this->zoom_api->createAMeeting($insert_array);

                        if (!empty($response)) {
                            if (isset($response->id)) {
                                $insert_array['return_response'] = json_encode($response);
                                $this->conference_model->add($insert_array);
                            }
                        }
                    }
                }
                else if ($this->input->post('edit_appointment_status', TRUE) == 'approved' && $saas_old_status != 'approved' && !empty($appointment_details['visit_details_id'])) {
                    // SaaS: OPD already existed (e.g. created at add() time) so the moveToOpd
                    // branch above didn't run/count it. Count once now on the first approval;
                    // the old-status guard blocks double-counting on later re-saves.
                    try {
                        $this->saasvalidation->updateResouceQuota('no_of_opd', 1);
                    } catch (Exception $e) {
                        log_message('error', 'SaaS no_of_opd quota increment failed (appointment reschedule approve, existing opd): ' . $e->getMessage());
                    }
                }

            endif; /* end null guard: appointment_payment && charges */

            /* end status code */
            $custom_field_post = $this->input->post("custom_fields[appointment]");
            if (!empty($custom_fields)) {
                foreach ($custom_field_post as $key => $value) {
                    $check_field_type = $this->input->post("custom_fields[appointment][" . $key . "]", TRUE);
                    $field_value      = is_array($check_field_type) ? implode(",", $check_field_type) : $check_field_type;
                    $array_custom     = array(
                        'belong_table_id' => $appointment_id,
                        'custom_field_id' => $key,
                        'field_value'     => $field_value,
                    );
                    $custom_value_array[] = $array_custom;
                }
                $this->customfield_model->updateRecord($custom_value_array, $appointment_id, 'appointment');
            }

            $sender_details = array('patient_id' => $appointment_details["patient_id"], 'appointment_id' => $appointment_id);
            $doctor_details = $this->notificationsetting_model->getstaffDetails($this->input->post('rdoctor_id'));
            $appointment_date = $this->input->post('appointment_date');
            $date             = $this->customlib->dateFormatToYYYYMMDDHis($appointment_date, $this->time_format);

            $event_data     = array(
                'appointment_date' => $this->customlib->YYYYMMDDHisTodateFormat($date, $this->time_format),
                'patient_id'       => $appointment_details["patient_id"],
                'doctor_id'        => $this->input->post('rdoctor_id'),
                'doctor_name'      => composeStaffNameByString($doctor_details['name'], $doctor_details['surname'], $doctor_details['employee_id']),
                'message'          => $this->input->post('message'),
                'appointment_status' =>  $this->input->post('edit_appointment_status'),
            );

            $sender_details = array('patient_id' => $appointment_details["patient_id"], 'appointment_id' => $appointment_id);

            if ($this->input->post('edit_appointment_status') == 'approved') {
                $this->mailsmsconf->mailsms('appointment_approved', $sender_details);
                $this->system_notification->send_system_notification('notification_appointment_created', $event_data);
                $this->system_notification->send_system_notification('appointment_approved', $event_data);
            }

            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
        }
        echo json_encode($array);
    }

















}
