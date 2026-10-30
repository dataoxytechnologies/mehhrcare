<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Vehicle extends Admin_Controller
{
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
        $this->marital_status = $this->config->item('marital_status');
        $this->payment_mode   = $this->config->item('payment_mode');
        $this->search_type    = $this->config->item('search_type');
        $this->blood_group    = $this->config->item('bloodgroup');
        $this->charge_type    = $this->customlib->getChargeMaster();
        $this->load->model("transaction_model");
        $this->load->library('SaasValidation');
        $this->config->load("image_valid");
        $data["charge_type"]        = $this->charge_type;
        $this->patient_login_prefix = "pat";
        $this->load->helper('customfield_helper');
        $this->time_format = $this->customlib->getHospitalTimeFormat();
    }

    public function search()
    {
        if (!$this->rbac->hasPrivilege('ambulance', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'Transport');
        $this->session->set_userdata('sub_menu', 'vehicle/index');
        $data['title'] = $this->lang->line('add_vehicle');
        $data['module'] = 'ambulance';
        $this->load->view('layout/header', $data);
        $this->load->view('admin/vehicle/search', $data);
        $this->load->view('layout/footer', $data);
    }

    public function add()
    {
        if (!$this->rbac->hasPrivilege('ambulance', 'can_add')) {
            access_denied();
        }
        $this->form_validation->set_rules('vehicle_no', $this->lang->line('vehicle_number'), 'required|xss_clean');
        $this->form_validation->set_rules('vehicle_model', $this->lang->line('vehicle_model'), 'required|xss_clean');
        $this->form_validation->set_rules('vehicle_type', $this->lang->line('vehicle_type'), 'required|xss_clean');

        if ($this->form_validation->run() == false) {

            $msg = array(
                'vehicle_no'    => form_error('vehicle_no'),
                'vehicle_model' => form_error('vehicle_model'),
                'vehicle_type'  => form_error('vehicle_type'),
            );

            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $manufacture_year = $this->input->post('manufacture_year', TRUE);
            $data             = array(
                'vehicle_no'     => $this->input->post('vehicle_no', TRUE),
                'vehicle_model'  => $this->input->post('vehicle_model', TRUE),
                'driver_name'    => $this->input->post('driver_name', TRUE),
                'driver_licence' => $this->input->post('driver_licence', TRUE),
                'driver_contact' => $this->input->post('driver_contact', TRUE),
                'vehicle_type'   => $this->input->post('vehicle_type', TRUE),
                'note'           => $this->input->post('note', TRUE),
            );
            ($manufacture_year != "") ? $data['manufacture_year'] = $manufacture_year : '';
            $this->vehicle_model->add($data);
            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
        }
        echo json_encode($array);
    }

    public function edit()
    {
        if (!$this->rbac->hasPrivilege('ambulance', 'can_view')) {
            access_denied();
        }
        $id          = $this->input->post("id", TRUE);
        $listVehicle = $this->vehicle_model->getDetails($id);
        echo json_encode($listVehicle);
    }

    public function update()
    {
        if (!$this->rbac->hasPrivilege('ambulance', 'can_edit')) {
            access_denied();
        }

        $this->form_validation->set_rules('vehicle_no', $this->lang->line('vehicle_number'), 'required|xss_clean');
        $this->form_validation->set_rules('vehicle_model', $this->lang->line('vehicle_model'), 'required|xss_clean');
        $this->form_validation->set_rules('vehicle_type', $this->lang->line('vehicle_type'), 'required|xss_clean');
        if ($this->form_validation->run() == false) {
            $msg = array(
                'vehicle_no'    => form_error('vehicle_no'),
                'vehicle_model' => form_error('vehicle_model'),
                'vehicle_type'  => form_error('vehicle_type'),
            );
            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {

            $id               = $this->input->post('id', TRUE);
            $manufacture_year = $this->input->post('manufacture_year', TRUE);

            $data = array(
                'id'             => $id,
                'vehicle_no'     => $this->input->post('vehicle_no', TRUE),
                'vehicle_model'  => $this->input->post('vehicle_model', TRUE),
                'driver_name'    => $this->input->post('driver_name', TRUE),
                'driver_licence' => $this->input->post('driver_licence', TRUE),
                'driver_contact' => $this->input->post('driver_contact', TRUE),
                'vehicle_type'   => $this->input->post('vehicle_type', TRUE),
                'note'           => $this->input->post('note', TRUE),
            );

            ($manufacture_year != "") ? $data['manufacture_year'] = $manufacture_year : '';

            $this->vehicle_model->add($data);
            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
        }

        echo json_encode($array);
    }

    public function delete($id)
    {
        if (!$this->rbac->hasPrivilege('ambulance', 'can_delete')) {
            access_denied();
        }
        $this->vehicle_model->remove($id);
        $array = array('status' => 'success', 'error' => '', 'msg' => $this->lang->line('delete_message'));
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

    public function validateCanAddNewResource($input, $resource_name)
    {
        list($resource_name, $quantity) = explode(',', $resource_name);
        return $this->saasvalidation->validateCanAddNewResource($input, $resource_name, $quantity);
    }

    /**
     * SaaS: release a cheque-document attachment from the storage quota and
     * remove the physical file (attachments live in the transactions table;
     * dir is uploads/payment_document/).
     */
    private function releaseAmbulanceAttachment($attachment)
    {
        if (empty($attachment)) {
            return;
        }
        $dir = $this->media_storage->resolveAttachmentDir($attachment);
        $kb  = $this->media_storage->getUploadedFileSize($attachment, $dir);
        if ($kb > 0) {
            try {
                $this->saasvalidation->deleteResouceQuota('storage', $kb);
            } catch (Exception $e) {
                log_message('error', 'SaaS storage quota release failed (ambulance): ' . $e->getMessage());
            }
        }
        $this->media_storage->filedelete($attachment, $dir);
    }

    public function addCallAmbulance()
    {
        if (!$this->rbac->hasPrivilege('ambulance_call', 'can_add')) {
            access_denied();
        }
        $custom_fields = $this->customfield_model->getByBelong('ambulance_call');

        foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
            if ($custom_fields_value['validation']) {
                $custom_fields_id   = $custom_fields_value['id'];
                $custom_fields_name = $custom_fields_value['name'];
                $this->form_validation->set_rules("custom_fields[ambulance_call][" . $custom_fields_id . "]", $custom_fields_name, 'trim|required|xss_clean');
            }
        }

        $this->form_validation->set_rules('patient_id', $this->lang->line('patient_name'), 'required|xss_clean');
        $this->form_validation->set_rules('vehicle_no', $this->lang->line('vehicle_model'), 'required|xss_clean');
        $this->form_validation->set_rules('date', $this->lang->line('date'), 'required|xss_clean');
        $this->form_validation->set_rules('charge_category_id', $this->lang->line("charge_category"), 'trim|required|xss_clean');
        $this->form_validation->set_rules('code', $this->lang->line("charge_name"), 'trim|required|xss_clean');
        $this->form_validation->set_rules('standard_charge', $this->lang->line('standard_charge'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('net_amount', $this->lang->line('net_amount'), 'trim|required|xss_clean');
        $this->form_validation->set_rules(
            'payment_amount',
            $this->lang->line('payment_amount'),
            array(
                'required', 'xss_clean', 'valid_amount',
                array('check_exists', array($this->vehicle_model, 'validate_paymentamount')),
            )
        );

        if ($this->input->post('payment_mode', TRUE) == "Cheque") {
            $this->form_validation->set_rules('cheque_no', $this->lang->line('cheque_no'), 'required|xss_clean');
            $this->form_validation->set_rules('cheque_date', $this->lang->line('cheque_date'), 'required|xss_clean');
            $this->form_validation->set_rules('document', $this->lang->line('document'), 'callback_handle_doc_upload[document]|callback_validateCanUploadFile[document]');
        }
        // SaaS: block a NEW ambulance call once the tenant hits its no_of_ambulance limit (create-only).
        $this->form_validation->set_rules('validate_resource', $this->lang->line('ambulance'), 'callback_validateCanAddNewResource[no_of_ambulance,1]');

        if ($this->form_validation->run() == false) {
            $msg = array(
                'vehicle_no'         => form_error('vehicle_no'),
                'validate_resource'  => form_error('validate_resource'),
                'date'               => form_error('date'),
                'payment_amount'     => form_error('payment_amount'),
                'patient_id'         => form_error('patient_id'),
                'charge_category_id' => form_error('charge_category_id'),
                'code'               => form_error('code'),
                'standard_charge'    => form_error('standard_charge'),
                'net_amount'         => form_error('net_amount'),
                'chekque_no'         => form_error('cheque_no'),
                'cheque_date'        => form_error('cheque_date'),
                'document'           => form_error('document'),
            );
            if (!empty($custom_fields)) {
                foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
                    if ($custom_fields_value['validation']) {
                        $custom_fields_id                                                       = $custom_fields_value['id'];
                        $custom_fields_name                                                     = $custom_fields_value['name'];
                        $error_msg2["custom_fields[ambulance_call][" . $custom_fields_id . "]"] = form_error("custom_fields[ambulance_call][" . $custom_fields_id . "]");
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

            $date              = $this->input->post("date", TRUE);
            $patient_id        = $this->input->post('patient_id', TRUE);
            $case_reference_id = $this->input->post('case_reference_id_add', TRUE);
            $data              = array(
                'patient_id'          => $patient_id,
                'vehicle_id'          => $this->input->post('vehicle_no', TRUE),
                'driver'              => $this->input->post('driver', TRUE),
                'amount'              => $this->input->post('total', TRUE),
                'net_amount'          => $this->input->post('net_amount', TRUE),
                'charge_id'           => $this->input->post('code', TRUE),
                'standard_charge'     => $this->input->post("standard_charge", TRUE),
                'tax_percentage'      => $this->input->post("tax_percentage", TRUE),
                'note'                => $this->input->post('note', TRUE),
                'discount_percentage' => $this->input->post("discount_percent", TRUE),
                'discount'            => $this->input->post("discount", TRUE),
                'generated_by'        => $this->customlib->getLoggedInUserID(),
                'date'                => $this->customlib->dateFormatToYYYYMMDDHis($date, $this->time_format),
            );
            if ($case_reference_id != '') {
                $data['case_reference_id'] = $case_reference_id;
            } else {
                $data['case_reference_id'] = null;
            }
            $chequedate      = $this->input->post('cheque_date', TRUE);
            $cheque_date     = $this->customlib->dateFormatToYYYYMMDD($chequedate);
            $payment_section = $this->config->item('payment_section');

            $transaction_data = array(
                'patient_id'        => $patient_id,
                'section'           => $payment_section['ambulance'],
                'amount'            => $this->input->post('payment_amount', TRUE),
                'type'              => 'payment',
                'case_reference_id' => $data['case_reference_id'],
                'payment_mode'      => $this->input->post('payment_mode', TRUE),
                'payment_date'      => $this->customlib->dateFormatToYYYYMMDDHis($date, $this->time_format),
                'received_by'       => $this->customlib->getLoggedInUserID(),
            );

            if ($this->input->post('payment_mode', TRUE) == "Cheque") {
                $transaction_data['cheque_date']     = $cheque_date;
                $transaction_data['cheque_no']       = $this->input->post('cheque_no', TRUE);

                if (isset($_FILES["document"]) && !empty($_FILES['document']['name'])) {
                    $file_name = $this->media_storage->fileupload("document",'./uploads/payment_document/');
                    $transaction_data['attachment']      = $file_name;
                    $transaction_data['attachment_name'] = $_FILES["document"]["name"];

                    // SaaS: add uploaded cheque document size to the storage quota usage.
                    try {
                        $this->saasvalidation->updateStorageLimit('storage', ['document']);
                    } catch (Exception $e) {
                        log_message('error', 'SaaS storage quota update failed (ambulance add): ' . $e->getMessage());
                    }
                }
            }

            $insert_id = $this->vehicle_model->addCallAmbulance($data, $transaction_data);

            // SaaS: a NEW ambulance call was created — increment no_of_ambulance by 1.
            // Monthly-reset usage counter: add-only (+1), NO edit/delete decrement by design.
            // Own try/catch so a quota-API hiccup never aborts the already-saved call.
            if ($insert_id) {
                try {
                    $this->saasvalidation->updateResouceQuota('no_of_ambulance', 1);
                } catch (Exception $e) {
                    log_message('error', 'SaaS no_of_ambulance count increment failed (vehicle addCallAmbulance): ' . $e->getMessage());
                }
            }

            $custom_field_post  = $this->input->post("custom_fields[ambulance_call]", TRUE);
            $custom_value_array = array();

            if (!empty($custom_field_post)) {
                foreach ($custom_field_post as $key => $value) {
                    $check_field_type = $this->input->post("custom_fields[ambulance_call][" . $key . "]", TRUE);
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
            $array = array('status' => 'success', 'id' => $insert_id, 'error' => '', 'message' => $this->lang->line('success_message'));

            $charge_details  = $this->notificationsetting_model->getchargeDetails($this->input->post('code', TRUE));
            $vehicle_details = $this->Notificationsetting_model->getvehiclemodelnoDetails($this->input->post('vehicle_no', TRUE));

            $event_data = array(
                'patient_id'    => $patient_id,
                'vehicle_model' => $vehicle_details['vehicle_model'],
                'driver_name'   => $this->input->post('driver', TRUE),
                'date'          => $date,
                'charge_name'   => $charge_details['name'],
                'tax'           => $this->input->post('tax', TRUE),
                'net_amount'    => $this->input->post('net_amount', TRUE),
                'paid_amount'   => $this->input->post('payment_amount', TRUE),
            );

            $this->system_notification->send_system_notification('create_ambulance_call', $event_data);
        }
        echo json_encode($array);
    }

    public function getBillDetails($id)
    {
        if (!$this->rbac->hasPrivilege('ambulance_call', 'can_view')) {
            access_denied();
        }
        $data['id'] = $id;
        if (isset($_GET['print'])) {
            $data["print"] = 'yes';
        } else {
            $data["print"] = 'no';
        }
        $print_details         = $this->printing_model->get('', 'ambulance');
        $data['print_details'] = $print_details;
        $result                = $this->vehicle_model->getBillDetails($id);
        $data['result']        = $result;
        $data['fields']        = $this->customfield_model->get_custom_fields('ambulance_call');
        $data['print_fields']  = $this->customfield_model->get_custom_fields('ambulance_call', '', 1);
        $this->load->view('admin/vehicle/printBill', $data);
    }

    public function getAmbulanceBillView()
    {
        if (!$this->rbac->hasPrivilege('ambulance_call', 'can_view')) {
            access_denied();
        }
        $id             = $this->input->post('id', TRUE);
        $data['result'] = $this->vehicle_model->getBillDetails($id);
        $data['fields'] = $this->customfield_model->get_custom_fields('ambulance_call');
        $page           = $this->load->view('admin/vehicle/_viewBillDetails', $data, true);
        echo json_encode(['status' => 1, 'page' => $page]);
    }

    public function getcallambulance()
    {
        if (!$this->rbac->hasPrivilege('ambulance_call', 'can_view')) {
            access_denied();
        }

        $this->session->set_userdata('top_menu', 'Transport');
        $this->session->set_userdata('sub_menu', 'vehicle/getcallambulance');
        $data['title']           = $this->lang->line('add_vehicle');
        $data['fields']          = $this->customfield_model->get_custom_fields('ambulance_call', 1);
        $patients                = $this->patient_model->getPatientListall();
        $data["patients"]        = $patients;
        $vehiclelist             = $this->vehicle_model->get();
        $data['vehiclelist']     = $vehiclelist;
        $data["payment_mode"]    = $this->payment_mode;
        $data['charge_category'] = $this->charge_category_model->getCategoryByModule("ambulance");
        $data["bloodgroup"]      = $this->bloodbankstatus_model->get_product(null, 1);
        $categoryName            = $this->pathology_category_model->getcategoryName();
        $data["categoryName"]    = $categoryName;
		$data['organisation']    = $this->organisation_model->get();
        $data['module'] = 'ambulance';
        $this->load->view('layout/header', $data);
        $this->load->view('admin/vehicle/ambulance_call', $data);
        $this->load->view('layout/footer', $data);
    }

    public function getambulancecallDatatable()
    {
        if (!$this->rbac->hasPrivilege('ambulance_call', 'can_view')) {
            access_denied();
        }
        $dt_response = $this->vehicle_model->getAllambulancecallRecord();		
        $fields      = $this->customfield_model->get_custom_fields('ambulance_call', 1);
        $dt_response = json_decode($dt_response);
        $dt_data     = array();
        if (!empty($dt_response->data)) {
			 
            foreach ($dt_response->data as $key => $value) {
                $balance = $value->net_amount - $value->paid_amount;
                $row     = array();
                //====================================
                $action = "<div class='rowoptionview rowview-btn-top'>";

                if ($this->rbac->hasPrivilege('ambulance_call', 'can_view')) {
                    $action .= "<a href='#'  onclick='viewDetailBill(" . $value->id . ")' class='btn btn-default btn-xs'  data-bs-toggle='tooltip'  title='" . $this->lang->line('show') . "' ><i class='fa fa-reorder'></i></a>";
                }

                if ($this->rbac->hasPrivilege('ambulance_partial_payment', 'can_add') || $this->rbac->hasPrivilege('ambulance_partial_payment', 'can_view') || $this->rbac->hasPrivilege('ambulance_partial_payment', 'can_delete')) {
                    $action .= "<a href='javascript:void(0)'  data-caseid='' data-module='ambulance'  data-record-id='" . $value->id . "' data-case-id='" . $value->case_reference_id . "' data-patient-id='" . $value->patient_id . "' data-balance-amount='" . $balance . "'class='btn btn-default btn-xs add_payment' data-bs-toggle='tooltip' title='" . $this->lang->line('add_payment') . "' ><i class='fa fa-plus'></i></a>";
                }

                if ($this->rbac->hasPrivilege('ambulance_call', 'can_delete')) {
                    $action .= "<a href='#' onclick='delete_bill(" . $value->id . ")' class='btn btn-default btn-xs'  data-bs-toggle='tooltip'  title='" . $this->lang->line('delete') . "'><i class='fa fa-trash'></i></a>";
                }

                $action .= "</div'>";

                //==============================
                $row[] = $this->customlib->getSessionPrefixByType('ambulance_call_billing') . $value->id;
                $row[] = $value->case_reference_id;
                $row[] = $value->patient . " (" . $value->patient_id . ")";
                $row[] = composeStaffNameByString($value->name, $value->surname, $value->employee_id);
                $row[] = $value->vehicle_no;
                $row[] = $value->vehicle_model;
                $row[] = $value->driver;
                $row[] = $value->mobileno;
                $row[] = $value->patient_address;
                $row[] = $this->customlib->YYYYMMDDHisTodateFormat($value->date, $this->time_format);

                //====================

                if (!empty($fields)) {
                    foreach ($fields as $fields_key => $fields_value) {
                        $display_field = $value->{"$fields_value->name"};
                        if ($fields_value->type == "link") {
                            $display_field = "<a href=" . $value->{"$fields_value->name"} . " target='_blank'>" . $value->{"$fields_value->name"} . "</a>";
                        } else {
                            $display_field = html_escape($display_field);
                        }
                        $row[] = $display_field;
                    }
                }
                //====================
				
                $row[]     = $value->amount;
                
				if($value->discount){
					$row[]     = $value->discount." (".$value->discount_percentage." %)";
				}else{
					$row[]     = '';
				}
				
				$tax_amt = (($value->amount - $value->discount) * $value->tax_percentage ) / 100;	
				
                $row[]     = amountFormat($tax_amt)." (".$value->tax_percentage." %)";
                $row[]     = $value->net_amount;
                $row[]     = $value->paid_amount;
                $row[]     = amountFormat($balance) . $action;
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

    public function getvehicleDatatable()
    {
        if (!$this->rbac->hasPrivilege('ambulance', 'can_view')) {
            access_denied();
        }
        $dt_response = $this->vehicle_model->getAllvehicleRecord();
        $dt_response = json_decode($dt_response);
        $dt_data     = array();
        if (!empty($dt_response->data)) {
            foreach ($dt_response->data as $key => $value) {

                $row = array();
                //====================================
                $note_value = $value->note;

                $action = "<div class='rowoptionview rowview-btn-top'>";

                if ($this->rbac->hasPrivilege('ambulance', 'can_edit')) {
                    $action .= "<span class='medium-tooltip'><a href='#' onclick='getRecord(" . $value->id . "),refreshmodal()' class='btn btn-default btn-xs'  data-bs-toggle='tooltip' data-placement='top' title='" . $this->lang->line('edit_ambulance') . "'><i class='fa fa-pencil'></i></a></span>";
                }
                if ($this->rbac->hasPrivilege('ambulance', 'can_delete')) {
                    $action .= "<a href='#' onclick='delete_bill(" . $value->id . ")'
                   class='btn btn-default btn-xs' data-placement='top' data-bs-toggle='tooltip'  title='" . $this->lang->line('delete') . "'><i class='fa fa-trash'></i></a>";
                }

                $action .= "</div>";

                $first_action = "<a href='#' class='detail_popover'  data-bs-toggle='popover' title=''>";
                $firstaction  = "<div class='fee_detail_popover' style='display:none'>";
                if ($value->note == "") {
                    $firstaction = "<p href='#'  class='text text-danger'   title=''>" . $this->lang->line('no_description') . "</p>";
                } else {
                    $firstaction = "<p href='#'  class='text text-danger'   title=''>" . $this->lang->line('note') . "</p>";
                }
                $firstaction = "</div>";
                // =============================

                $row[]     = $first_action . $value->vehicle_no . "</a>" . $firstaction;
                $row[]     = $value->vehicle_model;
                $row[]     = $value->manufacture_year;
                $row[]     = $value->driver_name;
                $row[]     = $value->driver_licence;
                $row[]     = $value->driver_contact;
                $row[]     = $note_value;
                $row[]     = $value->vehicle_type;
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

    public function editCall()
    {
        if (!$this->rbac->hasPrivilege('ambulance_call', 'can_view')) {
            access_denied();
        }

        $id                              = $this->input->get("id", TRUE);
        $listCall                        = $this->vehicle_model->getCallDetails($id);
        $date                            = $this->customlib->YYYYMMDDHisTodateFormat($listCall['date'], $this->time_format);
        $listCall["date"]                = $date;
        $listCall['custom_fields_value'] = display_custom_fields('ambulance_call', $id);
        echo json_encode($listCall);
    }

    public function updatecallambulance()
    {
        if (!$this->rbac->hasPrivilege('ambulance_call', 'can_edit')) {
            access_denied();
        }
        $custom_fields = $this->customfield_model->getByBelong('ambulance_call');
        if (!empty($custom_fields)) {
            foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
                if ($custom_fields_value['validation']) {
                    $custom_fields_id   = $custom_fields_value['id'];
                    $custom_fields_name = $custom_fields_value['name'];
                    $this->form_validation->set_rules("custom_fields[ambulance_call][" . $custom_fields_id . "]", $custom_fields_name, 'trim|required|xss_clean');
                }
            }
        }
        $this->form_validation->set_rules('patient_id', $this->lang->line('patient_name'), 'required|xss_clean');
        $this->form_validation->set_rules('vehicle_no', $this->lang->line('vehicle_number'), 'required|xss_clean');
        $this->form_validation->set_rules('date', $this->lang->line('date'), 'required|xss_clean');
        $this->form_validation->set_rules('charge_category_id', $this->lang->line("charge_category"), 'trim|required|xss_clean');
        $this->form_validation->set_rules('code', $this->lang->line("charge_name"), 'trim|required|xss_clean');
        $this->form_validation->set_rules('standard_charge', $this->lang->line('standard_charge'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('payment_amount', $this->lang->line('payment_amount'), 'trim|valid_amount|xss_clean');
        $this->form_validation->set_rules('total', $this->lang->line('total'), 'trim|required|xss_clean');
        if ($this->input->post('payment_mode', TRUE) == "Cheque") {
            $this->form_validation->set_rules('cheque_no', $this->lang->line('cheque_no'), 'required|xss_clean');
            $this->form_validation->set_rules('cheque_date', $this->lang->line('cheque_date'), 'required|xss_clean');
            $this->form_validation->set_rules('document', $this->lang->line('document'), 'callback_handle_doc_upload[document]|callback_validateCanUploadFile[document]');
        }

        if ($this->form_validation->run() == false) {
            $msg = array(
                'patient_id'         => form_error('patient_id'),
                'vehicle_no'         => form_error('vehicle_no'),
                'date'               => form_error('date'),
                'amount'             => form_error('amount'),
                'charge_category_id' => form_error('charge_category_id'),
                'code'               => form_error('code'),
                'standard_charge'    => form_error('standard_charge'),
                'chekque_no'         => form_error('cheque_no'),
                'cheque_date'        => form_error('cheque_date'),
                'document'           => form_error('document'),
                'total'              => form_error('total'),
            );
			
            if (!empty($custom_fields)) {
                foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
                    if ($custom_fields_value['validation']) {
                        $custom_fields_id                                                = $custom_fields_value['id'];
                        $custom_fields_name                                              = $custom_fields_value['name'];
                        $error_msg2["custom_fields[ambulance_call][" . $custom_fields_id . "]"] = form_error("custom_fields[ambulance_call][" . $custom_fields_id . "]");
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
            $id                = $this->input->post('id', TRUE);
            $date              = $this->input->post('date', TRUE);
            $patient_id        = $this->input->post("patient_id", TRUE);
            $custom_field_post = $this->input->post("custom_fields[ambulance_call]", TRUE);
            $data              = array(
                'id'                  => $id,
                'patient_id'          => $patient_id,
                'address'             => $this->input->post('address', TRUE),
                'driver'              => $this->input->post('driver_name', TRUE),
                'amount'              => $this->input->post('total', TRUE),
                'charge_category_id'  => $this->input->post("charge_category_id", TRUE),
                'charge_id'           => $this->input->post('code', TRUE),
                'net_amount'          => $this->input->post('net_amount', TRUE),
                'note'                => $this->input->post('note', TRUE),
                'standard_charge'     => $this->input->post("standard_charge", TRUE),
                'tax_percentage'      => $this->input->post("tax_percentage", TRUE),
                'discount_percentage' => $this->input->post("discount_percent_edit", TRUE),
                'discount'            => $this->input->post("discount_edit", TRUE),
                'date'                => $this->customlib->dateFormatToYYYYMMDDHis($date, $this->time_format),
            );

            if ($this->input->post("case_reference_id", TRUE) != "") {
                $data['case_reference_id'] = $this->input->post("case_reference_id", TRUE);
                $transaction_data          = array('case_reference_id' => $this->input->post("case_reference_id", TRUE));
            }

            $chequedate       = $this->input->post('cheque_date', TRUE);
            $cheque_date      = $this->customlib->dateFormatToYYYYMMDD($chequedate);
            $payment_section  = $this->config->item('payment_section');
            $transaction_data = array(
                'patient_id'   => $patient_id,
                'section'      => $payment_section['ambulance'],
                'amount'       => $this->input->post('payment_amount', TRUE),
                'type'         => 'payment',
                'payment_mode' => $this->input->post('payment_mode', TRUE),
                'payment_date' => $this->customlib->dateFormatToYYYYMMDDHis($date, $this->time_format),
                'received_by'  => $this->customlib->getLoggedInUserID(),
            );

            if ($this->input->post("case_reference_id", TRUE) != "") {
                $transaction_data['case_reference_id'] = $this->input->post("case_reference_id", TRUE);
            }

            if ($this->input->post('payment_mode', TRUE) == "Cheque") {
                $transaction_data['cheque_date']     = $cheque_date;
                $transaction_data['cheque_no']       = $this->input->post('cheque_no', TRUE);
               
                if (isset($_FILES["document"]) && !empty($_FILES['document']['name'])) {
                    $file_name = $this->media_storage->fileupload("document",'./uploads/payment_document/');
                    $transaction_data['attachment']      = $file_name;
                    $transaction_data['attachment_name'] = $_FILES["document"]["name"];

                    // SaaS: release the replaced cheque document (if any) + add the new one's size.
                    $saas_old_attachment = '';
                    $saas_existing_txns  = $this->transaction_model->ambulanceCallPayments($id);
                    if (!empty($saas_existing_txns)) {
                        foreach ($saas_existing_txns as $saas_txn) {
                            if ((string)$saas_txn->patient_id === (string)$patient_id && !empty($saas_txn->attachment)) {
                                $saas_old_attachment = $saas_txn->attachment;
                                break;
                            }
                        }
                    }
                    $this->releaseAmbulanceAttachment($saas_old_attachment);
                    try {
                        $this->saasvalidation->updateStorageLimit('storage', ['document']);
                    } catch (Exception $e) {
                        log_message('error', 'SaaS storage quota update failed (ambulance update): ' . $e->getMessage());
                    }
                }
            }

            $this->vehicle_model->addCallAmbulance($data, $transaction_data);
            $custom_field_post = $this->input->post("custom_fields[ambulance_call]", TRUE);

            if (!empty($custom_field_post)) {
                $custom_value_array = [];
              
                foreach ($custom_field_post as $key => $value) {
                    $check_field_type = $this->input->post("custom_fields[ambulance_call][" . $key . "]", TRUE);
                    $field_value      = is_array($check_field_type) ? implode(",", $check_field_type) : $check_field_type;
                    $array_custom     = array(
                        'belong_table_id' => $id,
                        'custom_field_id' => $key,
                        'field_value'     => $field_value,
                    );
                    $custom_value_array[] = $array_custom;
                }

                $this->customfield_model->updateRecord($custom_value_array, $id, 'ambulance_call');
            }
            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('update_message'), 'id' => $id);
        }
        echo json_encode($array);
    }

    public function deleteCallAmbulance($id)
    {
        if (!$this->rbac->hasPrivilege('ambulance_call', 'can_delete')) {
            access_denied();
        }

        // SaaS: release + remove the cheque document files of this call's transactions
        // (model->delete cascades the transaction rows but leaves the files on disk).
        $saas_txns = $this->transaction_model->ambulanceCallPayments($id);
        if (!empty($saas_txns)) {
            foreach ($saas_txns as $saas_txn) {
                $this->releaseAmbulanceAttachment(isset($saas_txn->attachment) ? $saas_txn->attachment : '');
            }
        }

        $this->vehicle_model->delete($id);
        redirect('admin/Vehicle/getcallambulance');
    }

    public function getVehicleDetail()
    {
        if (!$this->rbac->hasPrivilege('ambulance', 'can_view')) {
            access_denied();
        }
        $id     = $this->input->post('id', TRUE);
        $result = $this->vehicle_model->getDetails($id);
        echo json_encode($result);
    }

    public function ambulancereport()
    {
        if (!$this->rbac->hasPrivilege('ambulance_call', 'can_view')) {
            access_denied();
        }

        $this->session->set_userdata('top_menu', 'Reports');
        $this->session->set_userdata('sub_menu', 'reports/ambulance');
        $this->session->set_userdata('subsub_menu', 'reports/ambulance/ambulancereport');
        $custom_fields       = $this->customfield_model->get_custom_fields('ambulance_call', '', '', 1);
        $staffsearch         = $this->patient_model->getstaffAmbulancebill();
        $data['staffsearch'] = $staffsearch;
        $data["searchlist"]  = $this->search_type;
        $data["fields"]      = $custom_fields;
        $data['vehiclelist'] = $this->vehicle_model->get();
        $data['module'] = 'reports';
        $this->load->view('layout/header', $data);
        $this->load->view('admin/vehicle/ambulancereport', $data);
        $this->load->view('layout/footer', $data);
    }

    public function checkvalidation()
    {
        if (!$this->rbac->hasPrivilege('ambulance_call', 'can_view')) {
            access_denied();
        }
        $search = $this->input->post('search', TRUE);
        $this->form_validation->set_rules('search_type', $this->lang->line('search_type'), 'trim|required|xss_clean');
        if ($this->form_validation->run() == false) {
            $msg = array(
                'search_type' => form_error('search_type'),
            );
            $json_array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $param = array(
                'search_type'    => $this->input->post('search_type', TRUE),
                'collect_staff'  => $this->input->post('collect_staff', TRUE),
                'vehicle_number' => $this->input->post('vehicle_number', TRUE),
                'date_from'      => $this->input->post('date_from', TRUE),
                'date_to'        => $this->input->post('date_to', TRUE),
            );

            $json_array = array('status' => 'success', 'error' => '', 'param' => $param, 'message' => $this->lang->line('success_message'));
        }
        echo json_encode($json_array);
    }

    public function ambulancereports()
    {
        $search['search_type']   = $this->input->post('search_type', TRUE);
        $search['collect_staff'] = $this->input->post('collect_staff', TRUE);
        $search['date_from']     = $this->input->post('date_from', TRUE);
        $search['date_to']       = $this->input->post('date_to', TRUE);
        $start_date              = '';
        $end_date                = '';
        $currency_symbol = $this->customlib->getHospitalCurrencyFormat();

        $condition['vehicle_number'] = $this->input->post('vehicle_number', TRUE);
        $fields                      = $this->customfield_model->get_custom_fields('ambulance_call', '', '', 1);
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

        $condition['generated_staff'] = $this->input->post('collect_staff', TRUE);

        $reportdata   = $this->transaction_model->ambulancecallRecordReport($condition);
        $reportdata   = json_decode($reportdata);
        $dt_data      = array();
        $total_amount = 0;
        $total_discount = 0;
        $total_tax_amt = 0;
        $total_net = 0;
        $total_paid = 0;
        $total_balance = 0;       
	   
        if (!empty($reportdata->data)) { 
            foreach ($reportdata->data as $key => $value) {
                $row = array();
                $balance_amount = $value->net_amount - $value->paid_amount;
                $total_balance += $balance_amount;
                $total_net += $value->net_amount;
                $total_paid += $value->paid_amount;
                $total_amount += $value->amount;
                $total_discount += $value->discount;
				
				$tax_amt = (($value->amount - $value->discount) * $value->tax_percentage ) / 100;
				
				$total_tax_amt += $tax_amt;

                $row[] = $this->customlib->getSessionPrefixByType('ambulance_call_billing') . $value->id;
                $row[] = $value->patient_name . " (" . $value->patient_id . ")";
                $row[] = $this->customlib->YYYYMMDDHisTodateFormat($value->date, $this->time_format);
                $row[] = $value->mobileno;
                $row[] = $value->vehicle_no;
                $row[] = $value->vehicle_model;
                $row[] = $value->driver;
                $row[] = $value->address;

                //====================
                if (!empty($fields)) {
                    foreach ($fields as $fields_key => $fields_value) {
                        $display_field = $value->{"$fields_value->name"};
                        if ($fields_value->type == "link") {
                            $display_field = "<a href=" . $value->{"$fields_value->name"} . " target='_blank'>" . $value->{"$fields_value->name"} . "</a>";
                        } else {
                            $display_field = html_escape($display_field);
                        }
                        $row[] = $display_field;
                    }
                }
                //====================
                $row[]     = $value->amount;			
				
				if($value->discount){
					$row[]     = $value->discount." (".$value->discount_percentage." %)";
				}else{
					$row[]     = '';
				}				
				
                $row[]     = (number_format($tax_amt, 2, '.', '')) ." (".$value->tax_percentage." %)";
                $row[]     = $value->net_amount;				
                $row[]     = number_format((int) $value->paid_amount, 2, '.', '');
                $row[]     = number_format($value->net_amount - $value->paid_amount, 2, '.', '');
                $dt_data[] = $row;
            }

            $footer_row   = array();
            $footer_row[] = "";
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

            $footer_row[] = "<b>" .$this->lang->line('total') . "</b>" . ':';			
            $footer_row[] = "<b>" .$currency_symbol . (number_format($total_amount, 2, '.', '')) . "<br/>";
            $footer_row[] = "<b>" .$currency_symbol . (number_format($total_discount, 2, '.', '')) . "<br/>";
            $footer_row[] = "<b>" .$currency_symbol . (number_format($total_tax_amt, 2, '.', '')) . "<br/>";
            $footer_row[] = "<b>" .$currency_symbol . (number_format($total_net, 2, '.', '')) . "<br/>";            
            $footer_row[] = "<b>" .$currency_symbol . (number_format($total_paid, 2, '.', '')) . "<br/>";
            $footer_row[] = "<b>" .$currency_symbol . (number_format($total_balance, 2, '.', '')) . "<br/>";

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

    public function getAmbulanceCallTransaction()
    {
        $billing_id                     = $this->input->post('id', TRUE);
        $data['balance_amount']         = $this->input->post('balance_amount', TRUE);
        $data['case_id']                = $this->input->post('case_id', TRUE);
        $data['patient_id']             = $this->input->post('patient_id', TRUE);
        $data['ambullance_call_detail'] = $this->vehicle_model->getBillDetailsAmbulance($billing_id);
        $transaction                    = $this->transaction_model->ambulanceCallPayments($billing_id);
        $data["billing_id"]             = $billing_id;
        $data["payment_mode"]           = $this->payment_mode;
        $data['transaction']            = $transaction;
        $page                           = $this->load->view("admin/vehicle/_getAmbulanceCallTransactions", $data, true);
        echo json_encode(array('status' => 1, 'page' => $page));
    }

    public function partialbill()
    {
        if (!$this->rbac->hasPrivilege('ambulance_billing_payment', 'can_add')) {
            access_denied();
        }

        $this->form_validation->set_rules('payment_date', $this->lang->line('date'), 'required|xss_clean');
        $this->form_validation->set_rules(
            'payment_amount',
            $this->lang->line('payment_amount'),'required|xss_clean|valid_amount|callback_check_amount_exceed',
        );

        $this->form_validation->set_rules('payment_mode', $this->lang->line('payment_mode'), 'required|xss_clean');
        if ($this->input->post('payment_mode', TRUE) == "Cheque") {
            $this->form_validation->set_rules('cheque_no', $this->lang->line('cheque_no'), 'trim|required|xss_clean');
            $this->form_validation->set_rules('cheque_date', $this->lang->line('cheque_date'), 'trim|required|xss_clean');
            $this->form_validation->set_rules('document', $this->lang->line('document'), 'callback_handle_doc_upload[document]|callback_validateCanUploadFile[document]');
        }

        $case_id=$this->input->post('patient_referance_case_id', TRUE);

        if ($this->form_validation->run() == false) {
            $msg = array(
                'amount'       => form_error('payment_amount'),
                'payment_mode' => form_error('payment_mode'),
                'payment_date' => form_error('payment_date'),
                'cheque_date'  => form_error('cheque_date'),
                'cheque_no'    => form_error('cheque_no'),
                'document'     => form_error('document'),
            );
            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $picture       = "";
            $bill_date     = $this->input->post("payment_date", TRUE);
            $payment_array = array(
                'amount'            => $this->input->post('payment_amount', TRUE),
                'type'              => 'payment',
                'patient_id'        => $this->input->post('patient_id', TRUE),
                'ambulance_call_id' => $this->input->post('billing_id', TRUE),
                'payment_mode'      => $this->input->post('payment_mode', TRUE),
                'note'              => $this->input->post('note', TRUE),
                'payment_date'      => $this->customlib->dateFormatToYYYYMMDDHis($bill_date, $this->customlib->getHospitalTimeFormat()),
                'received_by'       => $this->customlib->getLoggedInUserID(),
            );

            if($case_id != "") {
                $payment_array['case_reference_id'] = $case_id;
            }

            $insert_id = $this->transaction_model->add($payment_array);

            $cheque_date = $this->input->post("cheque_date", TRUE);
            if ($this->input->post('payment_mode', TRUE) == "Cheque") {
                $data['id']              = $insert_id;
                $data['cheque_date']     = $this->customlib->dateFormatToYYYYMMDD($cheque_date);
                $data['cheque_no']       = $this->input->post('cheque_no', TRUE);
                
                if (isset($_FILES["document"]) && !empty($_FILES['document']['name'])) {
                    $file_name = $this->media_storage->fileupload("document",'./uploads/payment_document/');
                    $data['attachment']      = $file_name;
                    $data['attachment_name'] = $_FILES["document"]["name"];

                    // SaaS: add uploaded cheque document size to the storage quota usage.
                    try {
                        $this->saasvalidation->updateStorageLimit('storage', ['document']);
                    } catch (Exception $e) {
                        log_message('error', 'SaaS storage quota update failed (ambulance partialbill): ' . $e->getMessage());
                    }
                }
                $this->transaction_model->add($data);
            }

            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
        }
        echo json_encode($array);
    }

    public function check_amount_exceed(){
        $billing_id=$this->input->post('billing_id', TRUE);
        $ambullance_call_detail = $this->vehicle_model->getBillDetailsAmbulance($billing_id);

        if($this->input->post('payment_amount', TRUE) !="" && (float)$ambullance_call_detail['net_amount'] < ((float)$ambullance_call_detail['paid_amount'] + (float)$this->input->post('payment_amount', TRUE)))
        {
            $this->form_validation->set_message('check_amount_exceed', $this->lang->line('amount_should_not_be_greater_than_balance') . ' ' . ($ambullance_call_detail['net_amount']-$ambullance_call_detail['paid_amount']));
            return FALSE;
        }else{
            return TRUE;
        }
    }

    public function printTransaction()
    {
        if (!$this->rbac->hasPrivilege('ambulance_billing_payment', 'can_view')) {
            access_denied();
        }
        $print_details         = $this->printing_model->get('', 'paymentreceipt');
        $id                    = $this->input->post('id', TRUE);
        $charge                = array();
        $transaction           = $this->transaction_model->ambulanceCallPaymentByTransactionId($id);
        $data['print_details'] = $print_details;
        $data['transaction']   = $transaction;
        $page = $this->load->view('admin/vehicle/_printTransaction', $data, true);
        echo json_encode(array('status' => 1, 'page' => $page));
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
}
