<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Pharmacy extends Admin_Controller
{
	public $agerange;
	public $blood_group;
	public $charge_type;
	public $marital_status;
	public $opd_prefix;
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
        $this->load->library('encoding_lib');
        $this->load->library('CSVReader');
        $this->load->library('datatables');
        $this->load->library('system_notification');
        $this->load->library('SaasValidation');
        $this->load->model(array('pharmacy_model', 'prefix_model', 'transaction_model'));
        $this->marital_status       = $this->config->item('marital_status');
        $this->payment_mode         = $this->config->item('payment_mode');
        $this->search_type          = $this->config->item('search_type');
        $this->blood_group          = $this->config->item('bloodgroup');
        $this->charge_type          = $this->customlib->getChargeMaster();
        $data["charge_type"]        = $this->charge_type;
        $this->patient_login_prefix = "pat";
        $this->config->load("image_valid");
        $this->load->helper('customfield_helper');
        $this->load->helper('custom');
        $this->time_format = $this->customlib->getHospitalTimeFormat();
        $this->opd_prefix  = $this->prefix_model->getByCategory(array('opd_no'))[0]->prefix;
        $this->agerange    = $this->config->item('agerange');
    }

    public function unauthorized()
    {
        $data = array();
        $this->load->view('layout/header', $data);
        $this->load->view('unauthorized', $data);
        $this->load->view('layout/footer', $data);
    }

    public function partialbill()
    {
        if (!$this->rbac->hasPrivilege('pharmacy_partial_payment', 'can_add')) {
            access_denied();
        }

        $this->form_validation->set_rules('payment_date', $this->lang->line('date'), 'required');
        $this->form_validation->set_rules('amount', $this->lang->line('amount'), 'required|valid_amount');
        $this->form_validation->set_rules('payment_mode', $this->lang->line('payment_mode'), 'required|xss_clean');

        if ($this->input->post('payment_mode', TRUE) == "Cheque") {
            $this->form_validation->set_rules('cheque_no', $this->lang->line('cheque_no'), 'required|xss_clean');
            $this->form_validation->set_rules('cheque_date', $this->lang->line('cheque_date'), 'required');
            $this->form_validation->set_rules('document', $this->lang->line('document'), 'callback_handle_doc_upload[document]|callback_validateCanUploadFile[document]');
        }

        if ($this->form_validation->run() == false) {
            $msg = array(
                'payment_date' => form_error('payment_date'),
                'amount'       => form_error('amount'),
                'payment_mode' => form_error('payment_mode'),
                'chekque_no'   => form_error('cheque_no'),
                'cheque_date'  => form_error('cheque_date'),
                'document'     => form_error('document'),
            );
            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {

            $pharmacy_bill_basic_id = $this->input->post('pharmacy_bill_basic_id', TRUE);
            $pharmacy_bill_basic    = $this->transaction_model->pharmacyTotalPayments($pharmacy_bill_basic_id);

            if (!empty($pharmacy_bill_basic)) {
                $net_amount = $pharmacy_bill_basic->net_amount;
                $total_paid = $pharmacy_bill_basic->total_paid;
            } else {
                $net_amount = 0;
                $total_paid = 0;
            }
            $amount_paying = $this->input->post('amount', TRUE);
            $refund_amount = $this->input->post('refund_amount', TRUE);

            if (($net_amount + $refund_amount) >= ($total_paid + $amount_paying)) {

                $picture         = "";
                $bill_date       = $this->input->post("payment_date", TRUE);
                $payment_section = $this->config->item('payment_section');
                $payment_array   = array(
                    'amount'                 => $this->input->post('amount', TRUE),
                    'patient_id'             => $this->input->post('patient_id', TRUE),
                    'section'                => $payment_section['pharmacy'],
                    'type'                   => 'payment',
                    'pharmacy_bill_basic_id' => $this->input->post('pharmacy_bill_basic_id', TRUE),
                    'payment_mode'           => $this->input->post('payment_mode', TRUE),
                    'note'                   => $this->input->post('note', TRUE),
                    'payment_date'           => $this->customlib->dateFormatToYYYYMMDDHis($bill_date, $this->customlib->getHospitalTimeFormat()),
                    'received_by'            => $this->customlib->getLoggedInUserID(),
                );
                if (!empty($this->input->post('case_reference_id', TRUE)) && $this->input->post('case_reference_id', TRUE) != "") {
                    $payment_array['case_reference_id'] = $this->input->post('case_reference_id', TRUE);
                }

                $cheque_date = $this->input->post("cheque_date", TRUE);
                if ($this->input->post('payment_mode', TRUE) == "Cheque") {
                    $payment_array['cheque_date']     = $this->customlib->dateFormatToYYYYMMDD($cheque_date);
                    $payment_array['cheque_no']       = $this->input->post('cheque_no', TRUE);

                    if (isset($_FILES["document"]) && !empty($_FILES['document']['name'])) {
                        $file_name = $this->media_storage->fileupload("document",'./uploads/payment_document/');
                        $payment_array['attachment']      = $file_name;
                        $payment_array['attachment_name'] = $_FILES["document"]["name"];

                        // SaaS: add the uploaded document's size to the storage quota usage.
                        try {
                            $this->saasvalidation->updateStorageLimit('storage', ['document']);
                        } catch (Exception $e) {
                            log_message('error', 'SaaS storage quota update failed (pharmacy partialbill): ' . $e->getMessage());
                        }
                    }
                }

                $this->transaction_model->add($payment_array);
                $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));

            } else {
                $array = array('status' => 'fail', 'error' => array('amount_invalid' => $this->lang->line('amount_should_not_be_greater_than_balance') . ' ' . amountFormat(($net_amount + $refund_amount) - $total_paid)), 'message' => '');
            }
        }
        echo json_encode($array);
    }

    public function add()
    {
        if (!$this->rbac->hasPrivilege('medicine', 'can_add')) {
            access_denied();
        }

        $this->form_validation->set_rules(
            'medicine_name', $this->lang->line('medicine_name'), array('required',
                array('check_exists', array($this->medicine_category_model, 'valid_medicine_name')),
            )
        );
		
        $this->form_validation->set_rules('medicine_category_id', $this->lang->line('medicine_category'), 'required');       
        $this->form_validation->set_rules('unit', $this->lang->line('unit'), 'required|xss_clean');
        $this->form_validation->set_rules('unit_packing', $this->lang->line('unit_packing'), 'required|xss_clean');
        $this->form_validation->set_rules('file', $this->lang->line('image'), 'callback_handle_upload|callback_validateCanUploadFile[file]', 'required');
        if ($this->form_validation->run() == false) {

            $msg = array(
                'medicine_name'        => form_error('medicine_name'),
                'medicine_category_id' => form_error('medicine_category_id'),
                'unit'                 => form_error('unit'),
                'unit_packing'         => form_error('unit_packing'),
                'file'                 => form_error('file'),
            );

            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');

        } else {

            $pharmacy = array(
                'medicine_name'        => $this->input->post('medicine_name', TRUE),
                'medicine_category_id' => $this->input->post('medicine_category_id', TRUE),
                'medicine_company'     => $this->input->post('medicine_company', TRUE),
                'medicine_composition' => $this->input->post('medicine_composition', TRUE),
                'medicine_group'       => $this->input->post('medicine_group', TRUE),
                'unit'                 => $this->input->post('unit', TRUE),
                'min_level'            => $this->input->post('min_level', TRUE),
                'reorder_level'        => $this->input->post('reorder_level', TRUE),
                'vat'                  => $this->input->post('vat', TRUE),
                'unit_packing'         => $this->input->post('unit_packing', TRUE),
                'note'                 => $this->input->post('note', TRUE),
                'vat_ac'               => $this->input->post('vat_ac', TRUE),
                'rack_number'          => $this->input->post('rack_number', TRUE),
            );

            $insert_id = $this->pharmacy_model->add($pharmacy);

            if (isset($_FILES["file"]) && !empty($_FILES['file']['name'])) {
                $file_name = $this->media_storage->fileupload("file","./uploads/medicine_images/");
                $data_img = array('id' => $insert_id, 'medicine_image' => 'uploads/medicine_images/' . $file_name);
                $this->pharmacy_model->update($data_img);

                // SaaS: add the uploaded medicine image size to the storage quota usage.
                try {
                    $this->saasvalidation->updateStorageLimit('storage', ['file']);
                } catch (Exception $e) {
                    log_message('error', 'SaaS storage quota update failed (pharmacy add): ' . $e->getMessage());
                }
            }

            $category_name = $this->notificationsetting_model->getmedicinecategoryDetails($this->input->post('medicine_category_id', TRUE));

            $event_data = array(
                'medicine_name'        => $this->input->post('medicine_name', TRUE),
                'medicine_category'    => $category_name['medicine_category'],
                'medicine_company'     => $this->input->post('medicine_company', TRUE),
                'medicine_composition' => $this->input->post('medicine_composition', TRUE),
                'medicine_group'       => $this->input->post('medicine_group', TRUE),
                'unit'                 => $this->input->post('unit', TRUE),
                'unit_packing'         => $this->input->post('unit_packing', TRUE),
            );

            $this->system_notification->send_system_notification('add_medicine', $event_data);
            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
        }

        echo json_encode($array);
    }

    public function search()
    {
        if (!$this->rbac->hasPrivilege('medicine', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'pharmacy');
        $medicineCategory         = $this->medicine_category_model->getMedicineCategory();
        $data["medicineCategory"] = $medicineCategory;
        $resultlist               = $this->pharmacy_model->searchFullText();

        $i = 0;
        foreach ($resultlist as $value) {
            $pharmacy_id                 = $value['id'];
            $available_qty               = $this->pharmacy_model->totalQuantity($pharmacy_id);
            $totalAvailableQty           = $available_qty['total_qty'];
            $resultlist[$i]["total_qty"] = $totalAvailableQty;
            $i++;
        }

        $result             = $this->pharmacy_model->getPharmacy();
        $data['resultlist'] = $resultlist;
        $data['result']     = $result;
        $data['unitname']   = $this->pharmacy_model->getpharmacyunit();
        $data['company']    = $this->pharmacy_model->getcomapnyname();
        $data['get_medicine_group'] = $this->pharmacy_model->get_medicine_group();
        $data['module'] = 'pharmacy';
        $this->load->view('layout/header', $data);
        $this->load->view('admin/pharmacy/search', $data);
        $this->load->view('layout/footer', $data);
    }

    public function bulk_delete()
    {
        $this->form_validation->set_error_delimiters('', '');
        $this->form_validation->set_rules('delete_id[]', 'delete_id', 'trim|required|xss_clean', array('required' => $this->lang->line('no_record_selected')));

        if ($this->form_validation->run() == false) {
            $msg = array(
                'delete_id' => form_error('delete_id[]'),
            );
            $return_array = array('status' => 0, 'error' => $msg);
        } else {
            $pharmacy = $this->input->post('delete_id', TRUE);
            if(!empty($pharmacy)){
                foreach($pharmacy as $pharmacy_value){
                    // SaaS: release the medicine image's storage before the medicine row is deleted
                    // (mirrors the single delete() release; bulk delete was leaking the image quota).
                    $saas_row = $this->pharmacy_model->getMedicineById($pharmacy_value);
                    if (!empty($saas_row) && !empty($saas_row['medicine_image'])) {
                        $saas_image = $saas_row['medicine_image'];
                        $saas_dir   = (strpos($saas_image, '/') !== false) ? '' : 'uploads/medicine_images';
                        $saas_kb    = $this->media_storage->getUploadedFileSize($saas_image, $saas_dir);
                        if ($saas_kb > 0) {
                            try {
                                $this->saasvalidation->deleteResouceQuota('storage', $saas_kb);
                            } catch (Exception $e) {
                                log_message('error', 'SaaS storage quota release failed (pharmacy bulk_delete id=' . $pharmacy_value . '): ' . $e->getMessage());
                            }
                        }
                        $this->media_storage->filedelete($saas_image, $saas_dir);
                    }

                    $this->pharmacy_model->bulkdelete($pharmacy_value);
                }
                $return_array = array('status' => 1, 'error' => '', 'message' => $this->lang->line('delete_message'));
            }else{
                $return_array = array('status' => 0, 'error' => $this->lang->line('something_went_wrong') , 'message' => '');
            }            
        }

        echo json_encode($return_array);

    }

    public function getPrescriptionById()
    {
        $prescription_no = $this->input->post('prescription_no', TRUE);
        if ($prescription_no != "") {

            $medicineCategory         = $this->medicine_category_model->getMedicineCategory();
            $data["medicineCategory"] = $medicineCategory;
            $doctors                  = $this->staff_model->getStaffbyrole(3);
            $data["doctors"]          = $doctors;
            $prefixes                 = $this->prefix_model->getByCategory(array('ipd_prescription', 'opd_prescription'));
            $data["payment_mode"]     = $this->payment_mode;
            $total_rows               = 0;
            $patient_id               = "";
            $prefix_type              = "";
            $case_reference_id        = "";

            $prescription_prefix = splitPrefixType($prescription_no);
            $prescription_no     = splitPrefixID($prescription_no);

            if (!empty($prefixes)) {
                $prefix_type = findPrefixType($prefixes, $prescription_prefix);
            }

            $prescription_data = $this->prescription_model->getPrescriptionByTable($prescription_no, $prefix_type);
            $data['prescription_data'] = $prescription_data;

            $page = $this->load->view("admin/pharmacy/_prescriptionBill", $data, true);
            if (!empty($prescription_data)) {
                $case_reference_id = $prescription_data->case_reference_id;
                $patient_id        = $prescription_data->patient_id;
                $patient_name      = $prescription_data->patient_name;
                $total_rows        = count($prescription_data->medicines);
                $return_array      = array('status' => 1, 'page' => $page, 'patient_id' => $patient_id, 'patient_name' => $patient_name, 'total_rows' => $total_rows, 'case_reference_id' => $case_reference_id);
            } else {
                $return_array = array('status' => 0, 'msg' => $this->lang->line('no_prescription_found'));
            }

            echo json_encode($return_array);
        } else {
            echo json_encode(array('status' => 0, 'msg' => $this->lang->line('no_prescription_found')));
        }
    }

    public function getpharmacyDatatable()
    {
        $medicine_name = $this->input->post('medicine_name_filter', TRUE);
        $dt_response = $this->pharmacy_model->getAllpharmacyRecord($medicine_name);
        $dt_response = json_decode($dt_response);
        
        $dt_data     = array();
        if (!empty($dt_response->data)) {
            foreach ($dt_response->data as $key => $value) {
                
                $result   =   $this->pharmacy_model->getAvailableQuantity($value->id);
                
                if(!empty($result['used_quantity'])){
                    $used_quantity  =   $result['used_quantity'];
                }else{
                    $used_quantity  =  0 ;
                }                   
                $row = array();
                $available_qty = ($value->total_qty - $used_quantity);
                //====================================
                $status = "";
                $status1 = "";
                if ($available_qty <= 0) {
                    $status = " <span class='text text-danger'> (" . $this->lang->line('out_of_stock') . ")</span>";
                } elseif ($available_qty > 0 && $available_qty < $value->min_level ) {
                    $status = " <span class='text text-warning'> (" . $this->lang->line('low_stock') . ")</span>"; 
                } 
                
                if ($available_qty <= $value->reorder_level ) {                   
                    $status1 = " <span class='text text-info'> (" . $this->lang->line('reorder') . ")</span>";
                }  
                
                $action = "<div class='rowoptionview rowview-mt-19'>";
                $action .= "<a href='#' onclick='viewDetail(" . $value->id . ")' class='btn btn-default btn-xs' data-bs-toggle='tooltip' title='" . $this->lang->line('show') . "'><i class='fa fa-reorder'></i></a>";
                if ($this->rbac->hasPrivilege('medicine_bad_stock', 'can_add')) {
                    $action .= "<a href='#' class='btn btn-default btn-xs' onclick='addbadstock(" . $value->id . ")' data-bs-toggle='tooltip' title='" . $this->lang->line('add_bad_stock') . "'><i class='fas fa-minus-square'></i></a>";
                }
                
                $action .= "<div'>";
                $checkbox = "<input id='pharmacy' href='#' class='enable_delete'  type='checkbox' name='pharmacy[]' value='" . $value->id . "'>";
                //==============================
                $row[]     = $checkbox;
                $row[]     = $value->medicine_name;
                $row[]     = $value->company_name;
                $row[]     = $value->medicine_composition;
                $row[]     = $value->medicine_category;
                $row[]     = $value->group_name;
                $row[]     = $value->unit_name;
                $row[]     = $available_qty . $status . $status1 ;

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

    public function bill_search()
    {
        $draw            = $_POST['draw'];
        $row             = $_POST['start'];
        $rowperpage      = $_POST['length']; // Rows display per page
        $columnIndex     = $_POST['order'][0]['column']; // Column index
        $columnName      = $_POST['columns'][$columnIndex]['data']; // Column name
        $columnSortOrder = $_POST['order'][0]['dir']; // asc or desc
        $where_condition = array();
        if (!empty($_POST['search']['value'])) {
            $where_condition = array('search' => $_POST['search']['value']);
        }
        $resultlist   = $this->pharmacy_model->searchbill_datatable($where_condition);
        $total_result = $this->pharmacy_model->searchbill_datatable_count($where_condition);
        $data         = array();
        foreach ($resultlist as $result_key => $result_value) {

            $nestedData = array();
            $action     = "<div class='rowoptionview rowview-mt-19'>";
            $action .= "<a href='#'  data-loading-text='<i class=\"fa fa-circle-o-notch fa-spin\"></i>' data-record-id='" . $result_value->id . "' class='btn btn-default btn-xs add_payment' data-bs-toggle='tooltip' title='" . $this->lang->line('add_view_payments') . "' ><i class='fa fa-money'></i></a>";
            $action .= "<a href='#' onclick='viewDetail(" . $result_value->id . ")' class='btn btn-default btn-xs' data-bs-toggle='tooltip' title='" . $this->lang->line('show') . "' ><i class='fa fa-reorder'></i></a>";
            $action .= "<a href='#'  data-loading-text='<i class=\"fa fa-circle-o-notch fa-spin\"></i>' data-record-id='" . $result_value->id . "' class='btn btn-default btn-xs print_bill' data-bs-toggle='tooltip' title='" . $this->lang->line('print') . "' ><i class='fa fa-print'></i></a>";
            if ($this->pharmacy_model->getSaleReturnCountForBill($result_value->id) > 0) {
                $action .= "<a href='#' onclick='saleReturnHistory(" . $result_value->id . ")' class='btn btn-default btn-xs' data-bs-toggle='tooltip' title='" . $this->lang->line('return_history') . "'><i class='fa fa-history'></i></a>";
            }
            $action .= "<div>";

            $nestedData[] = $this->customlib->getSessionPrefixByType('pharmacy_billing') . $result_value->id;
            $nestedData[] = $result_value->case_reference_id;
            $nestedData[] = $result_value->date;
            $nestedData[] = $result_value->patient_name . $action;
            $nestedData[] = $result_value->doctor_name;
            $nestedData[] = $result_value->total;
            $data[]       = $nestedData;
        }

        $json_data = array(
            "draw"            => intval($draw), // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw.
            "recordsTotal"    => intval($total_result), // total number of records
            "recordsFiltered" => intval($total_result), // total number of records after searching, if there is no searching then totalFiltered = totalData
            "data"            => $data, // total data array
        );

        echo json_encode($json_data); // send data as json format

    }

    public function getpharmacybillDatatable()
    {
        $dt_response = $this->pharmacy_model->getAllpharmacybillRecord();
        $fields      = $this->customfield_model->get_custom_fields('pharmacy', 1);
        $dt_response = json_decode($dt_response);

        $dt_data = array();
        if (!empty($dt_response->data)) {
            foreach ($dt_response->data as $key => $value) {
              
                $row            = array();
                $balance_amount = ($value->net_amount-($value->paid_amount-$value->refund_amount));
                // ====================================
                $action = "<div class='rowoptionview rowview-mt-19'>";
                if ($this->rbac->hasPrivilege('pharmacy_partial_payment', 'can_view')) {
                    $action .= "<span class='medium-lr-tooltip'><a href='#' data-loading-text='" . $this->lang->line('please_wait') . "' data-record-id='" . $value->id . "' class='btn btn-default btn-xs add_payment tooltip-inner-2' data-bs-toggle='tooltip' title='" . $this->lang->line('add_view_payments') . "' ><i class='fa fa-money'></i></a></span>";
                }
                $action .= "<a href='#' onclick='viewDetail(" . $value->id . ")' class='btn btn-default btn-xs' data-bs-toggle='tooltip' title='" . $this->lang->line('show') . "' ><i class='fa fa-reorder'></i></a>";
                $action .= "<a href='#'  data-loading-text='" . $this->lang->line('please_wait') . "' data-record-id='" . $value->id . "' class='btn btn-default btn-xs print_bill' data-bs-toggle='tooltip' title='" . $this->lang->line('print') . "' ><i class='fa fa-print'></i></a>";
                if ($this->pharmacy_model->getSaleReturnCountForBill($value->id) > 0) {
                    $action .= "<a href='#' onclick='saleReturnHistory(" . $value->id . ")' class='btn btn-default btn-xs' data-bs-toggle='tooltip' title='" . $this->lang->line('return_history') . "'><i class='fa fa-history'></i></a>";
                }

                if ($value->case_reference_id > 0) {
                    $case_id = $value->case_reference_id;
                } else {
                    $case_id = '';
                }
                $action .= "</div>";

                //==============================
                $row[] = $this->customlib->getSessionPrefixByType('pharmacy_billing') . $value->id;
                $row[] = $case_id;
                $row[] = $this->customlib->YYYYMMDDHisTodateFormat($value->date, $this->time_format);
                $row[] = $value->patient_name . " (" . $value->pid . ")";
                $row[] = composeStaffNameByString($value->generated_byname, $value->generated_bysurname, $value->generated_byemployee_id);
                $row[] = $value->doctor_name;
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

                $row[]     = $value->total;
                $row[]     = $value->discount . " (" . $value->discount_percentage . "%)";
                if (( $value->total * $value->discount_percentage ) != 0) {
                    $discount_amt = ( $value->total * $value->discount_percentage ) / 100 ; 
                } else {
                    $discount_amt = 0; // or handle it however your logic requires
                }

                if (amountFormat($value->tax * 100)  != 0) {
                    $row[]     =  $value->tax ." (".amountFormat(($value->tax * 100) / ($value->total - $discount_amt), 2)."%)";   
                } else {
                     $row[] =0;
                } 
                $row[]     = $value->net_amount;
                $row[]     = number_format((float) $value->paid_amount, 2, '.', '');
                $row[]     = number_format((float) $value->refund_amount, 2, '.', '');
                $row[]     = number_format((float) $balance_amount, 2, '.', '') . $action;
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
	
//======================================================================================
    public function handle_upload()
    {
        $image_validate = $this->config->item('image_validate');
        if (isset($_FILES["file"]) && !empty($_FILES['file']['name'])) {

            $file_type         = $_FILES["file"]['type'];
            $file_size         = $_FILES["file"]["size"];
            $file_name         = $_FILES["file"]["name"];
            $allowed_extension = $image_validate['allowed_extension'];
            $ext               = pathinfo($file_name, PATHINFO_EXTENSION);
            $allowed_mime_type = $image_validate['allowed_mime_type'];
            if ($files = @getimagesize($_FILES['file']['tmp_name'])) {

                if (!in_array($files['mime'], $allowed_mime_type)) {
                    $this->form_validation->set_message('handle_upload', $this->lang->line('file_type_not_allowed'));
                    return false;
                }
                if (!in_array(strtolower($ext), $allowed_extension) || !in_array($file_type, $allowed_mime_type)) {
                    $this->form_validation->set_message('handle_upload', $this->lang->line('file_type_not_allowed'));
                    return false;
                }
                if ($file_size > $image_validate['upload_size']) {
                    $this->form_validation->set_message('handle_upload', $this->lang->line('file_size_shoud_be_less_than') . number_format($image_validate['upload_size'] / 1048576, 2) . " MB");
                    return false;
                }
            } else {
                $this->form_validation->set_message('handle_upload', $this->lang->line('file_type_not_allowed'));
                return false;
            }

            return true;
        }
        return true;
    }

    public function check_upload()
    {
        $image_validate = $this->config->item('image_validate');
        if (isset($_FILES["medicine_image"]) && !empty($_FILES['medicine_image']['name'])) {

            $file_type         = $_FILES["medicine_image"]['type'];
            $file_size         = $_FILES["medicine_image"]["size"];
            $file_name         = $_FILES["medicine_image"]["name"];
            $allowed_extension = $image_validate['allowed_extension'];
            $ext               = pathinfo($file_name, PATHINFO_EXTENSION);
            $allowed_mime_type = $image_validate['allowed_mime_type'];
            if ($files = @getimagesize($_FILES['medicine_image']['tmp_name'])) {

                if (!in_array($files['mime'], $allowed_mime_type)) {
                    $this->form_validation->set_message('check_upload', $this->lang->line('file_type_not_allowed'));
                    return false;
                }
                if (!in_array($ext, $allowed_extension) || !in_array($file_type, $allowed_mime_type)) {
                    $this->form_validation->set_message('check_upload', $this->lang->line('file_type_not_allowed'));
                    return false;
                }
                if ($file_size > $image_validate['upload_size']) {
                    $this->form_validation->set_message('check_upload', $this->lang->line('file_size_shoud_be_less_than') . number_format($image_validate['upload_size'] / 1048576, 2) . " MB");
                    return false;
                }
            } else {
                $this->form_validation->set_message('check_upload', $this->lang->line('file_type_not_allowed'));
                return false;
            }

            return true;
        }
        return true;
    }

    public function getindate()
    {
        $purchase_id           = $this->input->post("purchase_id");
        $result                = $this->pharmacy_model->getindate($purchase_id);
        $result['purchase_no'] = $this->customlib->getSessionPrefixByType('purchase_no') . $result['id'];

        echo json_encode($result);
    }

    public function getdate()
    {
        $id             = $this->input->post("id");
        $result         = $this->pharmacy_model->getdate($id);
        $result["date"] = $this->customlib->YYYYMMDDHisTodateFormat($result['date'], $this->time_format);
        echo json_encode($result);
    }

    public function purchase()
    {
        if (!$this->rbac->hasPrivilege('medicine_purchase', 'can_view')) {
            access_denied();
        }
        $medicineCategory         = $this->medicine_category_model->getMedicineCategory();
        $data["medicineCategory"] = $medicineCategory;
        $supplierCategory         = $this->medicine_category_model->getSupplierCategory();
        $data["supplierCategory"] = $supplierCategory;
        $result                   = $this->pharmacy_model->getPharmacy();
        $data['result']           = $result;
        $data["payment_mode"]     = $this->payment_mode;
        $data['module'] = 'pharmacy';
        $this->load->view('layout/header', $data);
        $this->load->view('admin/pharmacy/purchase', $data);
        $this->load->view('layout/footer', $data);
    }

    public function getpharmacypurchaseDatatable()
    {
        $dt_response = $this->pharmacy_model->getAllpharmacypurchaseRecord();
        $dt_response = json_decode($dt_response);
        $dt_data     = array();
        $discount_percentage=0;
        $tax_percentage=0;
        if (!empty($dt_response->data)) {
            foreach ($dt_response->data as $key => $value) {
                $row = array();

                //====================================
                $action = "<div class='rowoptionview rowview-mt-19'>";
                $action .= "<a href='#' onclick='viewDetail(" . $value->id . ")' class='btn btn-default btn-xs' data-bs-toggle='tooltip' title='" . $this->lang->line('show') . "'><i class='fa fa-reorder'></i></a>";

                if ($this->rbac->hasPrivilege('medicine_purchase', 'can_edit')) {
                    $action .= "<a href='#' onclick='editPurchase(" . $value->id . ")' class='btn btn-default btn-xs' data-bs-toggle='tooltip' title='" . $this->lang->line('edit') . "'><i class='fa fa-pencil'></i></a>";
                }

                if ($this->rbac->hasPrivilege('medicine_purchase', 'can_add')) {
                    $action .= "<a href='#' onclick='purchaseReturn(" . $value->id . ")' class='btn btn-default btn-xs' data-bs-toggle='tooltip' title='" . $this->lang->line('return') . "'><i class='fa fa-undo'></i></a>";
                }
                $has_return_history = $this->pharmacy_model->getPurchaseReturnHistoryCount($value->id) > 0;
                if ($this->rbac->hasPrivilege('medicine_purchase', 'can_view') && $has_return_history) {
                    $action .= "<a href='#' onclick='purchaseReturnHistory(" . $value->id . ")' class='btn btn-default btn-xs' data-bs-toggle='tooltip' title='" . $this->lang->line('history') . "'><i class='fa fa-history'></i></a>";
                }

                if ($this->rbac->hasPrivilege('medicine_tpa_charges', 'can_view')) {
                $action .= "<a href='#' onclick='view_tpa_charge_model(" . $value->id . ")' class='btn btn-default btn-xs' data-bs-toggle='tooltip' title='" . $this->lang->line('tpa_charges') . "'><i class='fas fa-umbrella'></i></a>";
                }

                if (!empty($value->file)) {
                    $action .= "<a href=" . base_url() . 'admin/pharmacy/download/' . $value->file . " class='btn btn-default btn-xs' data-bs-toggle='tooltip' title='" . $this->lang->line('download') . "'><i class='fa fa-download'></i></a>";
                }
                $action .= "<div>";
                //==============================
                $return_badge = $has_return_history ? ' <span class="label label-warning">R</span>' : '';
                $row[] = $this->customlib->getSessionPrefixByType('purchase_no') . $value->id . $return_badge ;
                $row[] = $this->customlib->YYYYMMDDHisTodateFormat($value->date);
                $row[] = $value->invoice_no;
                $row[] = $value->supplier;
                $row[] = $value->total;

                $total = (float)$value->total;
                $discount = (float)$value->discount;
                $tax = (float)$value->tax;
                
                if ($total != 0) {
                    $discount_percentage = ($discount * 100) / $total;
                    $tax_percentage = ($tax * 100) / ($total - $discount); // assuming tax is based on amount after discount
                } else {
                    $discount_percentage = 0;
                    $tax_percentage = 0;
                }

                $row[] = $value->discount." (".amountFormat($discount_percentage)."%)";
                $row[] = $value->tax." (".amountFormat($tax_percentage)."%)";
                $row[] = $value->net_amount . $action;
                //====================

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

    public function exportformat()
    {
        $this->load->helper('download');
        $filepath = "./backend/import/import_medicine_sample_file.csv";
        $data     = file_get_contents($filepath);
        $name     = 'import_medicine_sample_file.csv';
        force_download($name, $data);
    }
 
    public function import()
    {
        if (!$this->rbac->hasPrivilege('import_medicine', 'can_view')) {
            access_denied();
        }
        $this->form_validation->set_rules('medicine_category_id', $this->lang->line('medicine_category'), 'required|trim|xss_clean');
        $this->form_validation->set_rules('file', $this->lang->line('file'), 'callback_handle_csv_upload');
        $medicineCategory         = $this->medicine_category_model->getMedicineCategory();
        $data["medicineCategory"] = $medicineCategory;
        $fields                   = array('medicine_name', 'medicine_company', 'medicine_composition', 'medicine_group', 'unit', 'min_level', 'reorder_level', 'vat', 'unit_packing', 'note');
        $data["fields"]           = $fields;

        if ($this->form_validation->run() == false) {
            $msg = array(
                'medicine_category_id' => form_error('medicine_category_id'),
                'file'                 => form_error('file'),
            );

            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
            $data['module'] = 'pharmacy';
            $this->load->view('layout/header', $data);
            $this->load->view('admin/pharmacy/import', $data);
            $this->load->view('layout/footer', $data);
        } else {
            $medicine_category_id = $this->input->post('medicine_category_id', TRUE);
            if (isset($_FILES["file"]) && !empty($_FILES['file']['name'])) {
                $ext = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);

                if ($ext == 'csv') {
                    $file = $_FILES['file']['tmp_name'];
                    $result = $this->csvreader->parse_file($file);
                    if (!empty($result)) {
                        $count = 0;
                        for ($i = 1; $i <= count($result); $i++) {

                            $medicine_data[$i] = array();
                            $n                 = 0;
                            foreach ($result[$i] as $key => $value) {

                                $medicine_data[$i][$fields[$n]]            = $this->encoding_lib->toUTF8($result[$i][$key]);
                                $medicine_data[$i]['is_active']            = 'yes';
                                $medicine_data[$i]['medicine_category_id'] = $medicine_category_id;

                                $n++;
                            }

                            $medicine_name = $medicine_data[$i]["medicine_name"];
                            if (!empty($medicine_name)) {
                                if ($this->pharmacy_model->check_medicine_exists($medicine_name, $medicine_category_id)) {
                                    $this->session->set_flashdata('import_msg', '<div class="alert alert-danger text-center">' . $this->lang->line('record_already_exists') . '</div>');

                                    $insert_id = "";
                                } else {
                                    $insert_id = $this->pharmacy_model->addImport($medicine_data[$i]);
                                }
                            }

                            if (!empty($insert_id)) {
                                $data['csvData'] = $result;
                                $this->session->set_flashdata('import_msg', '<div class="alert alert-success text-center">' . $this->lang->line('records_imported_successfully') . '</div>');
                                $count++;
                                $this->session->set_flashdata('import_msg', '<div class="alert alert-success text-center">Total ' . count($result) . ' ' . $this->lang->line('records_found_in_csv_file_total') . ' ' . $count . $this->lang->line('records_imported_successfully') . '</div>');
                            } else {
                                $this->session->set_flashdata('import_msg', '<div class="alert alert-danger text-center">' . $this->lang->line('record_already_exists') . '</div>');
                            }
                        }
                    }
                }
                redirect('admin/pharmacy/import');
            }
        }
    }

    public function handle_csv_upload()
    {
        $image_validate = $this->config->item('filecsv_validate');
        if (isset($_FILES["file"]) && !empty($_FILES['file']['name'])) {

            $file_type         = $_FILES["file"]['type'];
            $file_size         = $_FILES["file"]["size"];
            $file_name         = $_FILES["file"]["name"];
            $allowed_extension = $image_validate['allowed_extension'];
            $ext               = pathinfo($file_name, PATHINFO_EXTENSION);
            $allowed_mime_type = $image_validate['allowed_mime_type'];
            if ($files = filesize($_FILES['file']['tmp_name'])) {
                if (!in_array($file_type, $allowed_mime_type)) {
                    $this->form_validation->set_message('handle_csv_upload', $this->lang->line('file_type_not_allowed'));
                    return false;
                }
                if (!in_array($ext, $allowed_extension) || !in_array($file_type, $allowed_mime_type)) {
                    $this->form_validation->set_message('handle_csv_upload', $this->lang->line('file_extension_not_allowed'), 'Extension Not Allowed');
                    return false;
                }
                if ($file_size > $image_validate['upload_size']) {
                    $this->form_validation->set_message('handle_csv_upload', $this->lang->line('file_size_shoud_be_less_than') . number_format($image_validate['upload_size'] / 1048576, 2) . " MB");
                    return false;
                }
            } else {
                $this->form_validation->set_message('handle_csv_upload', $this->lang->line('file_type_extension_not_allowed'));
                return false;
            }

            return true;
        } else {
            $this->form_validation->set_message('handle_csv_upload', $this->lang->line('the_file_field_is_required'));
            return false;
        }
        return true;
    }

    public function getDetails()
    {
        if (!$this->rbac->hasPrivilege('medicine', 'can_view')) {
            access_denied();
        }
        $id     = $this->input->post("pharmacy_id");
        $result = $this->pharmacy_model->getDetails($id);

        if(!empty($result['medicine_image'])){        
            $result['medicine_image']  = $this->media_storage->getImageURL($result['medicine_image']);
        }else{
            $result['medicine_image']  = $this->media_storage->getImageURL("./uploads/patient_images/no_image.png") ;
        }
        echo json_encode($result);
    }

    public function update()
    {
        if (!$this->rbac->hasPrivilege('medicine', 'can_edit')) {
            access_denied();
        }
        $this->form_validation->set_rules('medicine_name', $this->lang->line('medicine_name'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('medicine_category_id', $this->lang->line('medicine_category_id'), 'required');
        $this->form_validation->set_rules('unit', $this->lang->line('unit'), 'required');
        $this->form_validation->set_rules('unit_packing', $this->lang->line('unit_packing'), 'required');
        $this->form_validation->set_rules('medicine_image', $this->lang->line('image'), 'callback_check_upload|callback_validateCanUploadFile[medicine_image]');
        if ($this->form_validation->run() == false) {
            $msg = array(
                'medicine_name'        => form_error('medicine_name'),
                'medicine_category_id' => form_error('medicine_category_id'),
                'unit'                 => form_error('unit'),
                'unit_packing'         => form_error('unit_packing'),
                'medicine_image'       => form_error('medicine_image'),
            );
            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $id       = $this->input->post('id', TRUE);
            $pharmacy = array(
                'id'                   => $id,
                'medicine_name'        => $this->input->post('medicine_name', TRUE),
                'medicine_category_id' => $this->input->post('medicine_category_id', TRUE),
                'medicine_company'     => $this->input->post('medicine_company', TRUE),
                'medicine_composition' => $this->input->post('medicine_composition', TRUE),
                'medicine_group'       => $this->input->post('medicine_group', TRUE),
                'unit'                 => $this->input->post('unit', TRUE),
                'min_level'            => $this->input->post('min_level', TRUE),
                'reorder_level'        => $this->input->post('reorder_level', TRUE),
                'vat'                  => $this->input->post('vat', TRUE),
                'unit_packing'         => $this->input->post('unit_packing', TRUE),
                'note'                 => $this->input->post('edit_note', TRUE),
                'vat_ac'               => $this->input->post('vat_ac', TRUE),
            );
            $this->pharmacy_model->update($pharmacy);
           
            if (isset($_FILES["medicine_image"]) && !empty($_FILES['medicine_image']['name'])) {
                // SaaS: capture the existing medicine image for quota diff + cleanup.
                $saas_existing  = $this->pharmacy_model->getMedicineById($id);
                $saas_old_image = (!empty($saas_existing['medicine_image'])) ? $saas_existing['medicine_image'] : '';
                $saas_old_dir   = (strpos($saas_old_image, '/') !== false) ? '' : 'uploads/medicine_images';
                $saas_old_kb    = (!empty($saas_old_image)) ? $this->media_storage->getUploadedFileSize($saas_old_image, $saas_old_dir) : 0;

                $file_name = $this->media_storage->fileupload("medicine_image","./uploads/medicine_images/");
                if (!IsNullOrEmptyString($file_name)) {
                    if (!empty($saas_old_image)) {
                        $this->media_storage->filedelete($saas_old_image, $saas_old_dir);
                    }
                    $data_img = array('id' => $id, 'medicine_image' => 'uploads/medicine_images/' . $file_name);
                    $this->pharmacy_model->update($data_img);

                    // SaaS: adjust storage quota by the size difference (new vs replaced).
                    try {
                        $saas_new_kb = $this->media_storage->getTmpFileSize('medicine_image');
                        if ($saas_old_kb > $saas_new_kb) {
                            $this->saasvalidation->deleteResouceQuota('storage', $saas_old_kb - $saas_new_kb);
                        } elseif ($saas_new_kb > $saas_old_kb) {
                            $this->saasvalidation->updateResouceQuota('storage', $saas_new_kb - $saas_old_kb);
                        }
                    } catch (Exception $e) {
                        log_message('error', 'SaaS storage quota update failed (pharmacy update): ' . $e->getMessage());
                    }
                }
            }
            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
        }
        echo json_encode($array);
    }

    public function delete($id)
    {
        if (!$this->rbac->hasPrivilege('medicine', 'can_delete')) {
            access_denied();
        }
        if (!empty($id)) {
            // SaaS: release the medicine image's storage from the quota and remove the file.
            $saas_row = $this->pharmacy_model->getMedicineById($id);
            if (!empty($saas_row) && !empty($saas_row['medicine_image'])) {
                $saas_image = $saas_row['medicine_image'];
                $saas_dir   = (strpos($saas_image, '/') !== false) ? '' : 'uploads/medicine_images';
                $saas_kb    = $this->media_storage->getUploadedFileSize($saas_image, $saas_dir);
                if ($saas_kb > 0) {
                    try {
                        $this->saasvalidation->deleteResouceQuota('storage', $saas_kb);
                    } catch (Exception $e) {
                        log_message('error', 'SaaS storage quota release failed (pharmacy delete): ' . $e->getMessage());
                    }
                }
                $this->media_storage->filedelete($saas_image, $saas_dir);
            }

            $this->pharmacy_model->delete($id);
            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('delete_message'));
        } else {
            $array = array('status' => 'fail', 'error' => '', 'message' => '');
        }
        echo json_encode($array);
    }

    public function getPharmacy()
    {
        if (!$this->rbac->hasPrivilege('medicine', 'can_view')) {
            access_denied();
        }
        $id     = $this->input->post('pharmacy_id');
        $result = $this->pharmacy_model->getPharmacy($id);
        echo json_encode($result);
    }

    public function convertMonthToNumber($monthName)
    {
        return date('m', strtotime($monthName));
    }

    public function medicineBatch()
    {
        if (!$this->rbac->hasPrivilege('medicine batch details', 'can_edit')) {
            access_denied();
        }
        $this->form_validation->set_rules('pharmacy_id', $this->lang->line('pharmacy_id'), 'required');
        $this->form_validation->set_rules('expiry_date', $this->lang->line('expiry_date'), 'required');
        $this->form_validation->set_rules('batch_no', $this->lang->line('batch_no'), 'required');
        $this->form_validation->set_rules('packing_qty', $this->lang->line('packing_qty'), 'required|numeric');
        $this->form_validation->set_rules('quantity', $this->lang->line('quantity'), 'required|numeric');
        $this->form_validation->set_rules('mrp', $this->lang->line('mrp'), 'required|numeric');
        $this->form_validation->set_rules('sale_rate', $this->lang->line('sale_rate'), 'required|numeric');

        if ($this->form_validation->run() == false) {
            $msg = array(
                'pharmacy_id'        => form_error('pharmacy_id'),
                'expiry_date'        => form_error('expiry_date'),
                'expiry_date_format' => form_error('expiry_date_format'),
                'batch_no'           => form_error('batch_no'),
                'packing_qty'        => form_error('packing_qty'),
                'quantity'           => form_error('quantity'),
                'mrp'                => form_error('mrp'),
                'sale_rate'          => form_error('sale_rate'),
            );
            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $id          = $this->input->post('pharmacy_id', TRUE);
            $inward_date = $this->input->post('inward_date', TRUE);
            $expdate     = $this->input->post('expiry_date', TRUE);

            $explore = explode("/", $expdate);
            $monthary     = $explore[0];
            $yearary      = $explore[1];
            $month        = $monthary;
            $month_number = $this->convertMonthToNumber($month);
            $insert_date  = $yearary . "-" . $month_number . "-01";

            $medicine_batch = array(
                'pharmacy_id'           => $id,
                'expiry_date'           => $this->input->post('expiry_date', TRUE),
                'expiry_date_format'    => $insert_date,
                'inward_date'           => $this->customlib->dateFormatToYYYYMMDD($inward_date),
                'batch_no'              => $this->input->post('batch_no', TRUE),
                'packing_qty'           => $this->input->post('packing_qty', TRUE),
                'purchase_rate_packing' => $this->input->post('purchase_rate_packing', TRUE),
                'quantity'              => $this->input->post('quantity', TRUE),
                'mrp'                   => $this->input->post('mrp', TRUE),
                'sale_rate'             => $this->input->post('sale_rate', TRUE),
                'amount'                => $this->input->post('amount', TRUE),
                'available_quantity'    => $this->input->post('quantity', TRUE),
            );
            $this->pharmacy_model->medicineDetail($medicine_batch);
            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
        }
        echo json_encode($array);
    }

    public function getMedicineBatch()
    {
        if (!$this->rbac->hasPrivilege('medicine', 'can_view')) {
            access_denied();
        }
        $id                     = $this->input->post("pharmacy_id");
        $result                 = $this->pharmacy_model->getMedicineBatch($id);
        $data["result"]         = $result;
        $badstockresult         = $this->pharmacy_model->getMedicineBadStock($id);
        $data["badstockresult"] = $badstockresult;
        $this->load->view('admin/pharmacy/medicineDetail', $data);
    }

    public function addpatient()
    {
        if (!$this->rbac->hasPrivilege('patient', 'can_add')) {
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
                        log_message('error', 'SaaS storage quota update failed (pharmacy addpatient): ' . $e->getMessage());
                    }
                }
            }
        }
        echo json_encode($array);
    }

    public function patientDetails()
    {
        if (!$this->rbac->hasPrivilege('patient', 'can_view')) {
            access_denied();
        }
        $id   = $this->input->post("id", TRUE);
        $data = $this->patient_model->patientDetails($id);
        echo json_encode($data);
    }

    public function supplierDetails()
    {
        if (!$this->rbac->hasPrivilege('medicine_supplier', 'can_view')) {
            access_denied();
        }
        $id   = $this->input->post("id");
        $data = $this->patient_model->supplierDetails($id);
        echo json_encode($data);
    }

    public function bill()
    {
        if (!$this->rbac->hasPrivilege('pharmacy_bill', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'pharmacy');
        $doctors                  = $this->staff_model->getStaffbyrole(3);
        $data["doctors"]          = $doctors;
        $data['medicineCategory'] = $this->medicine_category_model->getMedicineCategory();
        $data['medicineName']     = $this->pharmacy_model->getMedicineName();
        $patients                 = $this->patient_model->getPatientListall();
        $data['fields']           = $this->customfield_model->get_custom_fields('pharmacy', 1);
        $data["patients"]         = $patients;
        $data["marital_status"]   = $this->marital_status;
        $data["bloodgroup"]       = $this->blood_group;
        $data["payment_mode"]     = $this->payment_mode;
        $data['organisation']   = $this->organisation_model->get();
        $data['module'] = 'pharmacy';
        $this->load->view('layout/header', $data);
        $this->load->view('admin/pharmacy/pharmacyBill', $data);
        $this->load->view('layout/footer', $data);
    }

    public function get_medicine_name()
    {
        $medicine_category_id = $this->input->post("medicine_category_id");
        $data                 = $this->pharmacy_model->get_medicine_name($medicine_category_id);
        echo json_encode($data);
    }

    public function get_medicine_stockinfo()
    {
        $pharmacy_id = $this->input->post('pharmacy_id');
        $notic_data  = $this->pharmacy_model->get_medicine_stockinfo($pharmacy_id);

        if(empty($notic_data['total_qty'])){
            $available_quantity =  0;    
        }else{
             $available_quantity =  $notic_data['total_qty']-$notic_data['used_quantity'];    
        }
        $msg  = "";         
        
        if ($available_quantity <= 0) {
            $msg .= " <span class='dataTables_info text-danger'> " . $this->lang->line('out_of_stock') . "</span>";
        } elseif ($notic_data['total_qty'] <= $notic_data['min_level']) {
            $msg .= " <span class='dataTables_info text-danger'> " . $this->lang->line('low_stock') . "</span>";
        } else {
            $msg .= "<div style='font-size:12px' class='text-danger'>" . $this->lang->line('avl_qty') . ": " . $available_quantity. "</div>";            
        }        
        
        echo json_encode($msg);
    }

    public function get_medicine_dosage()
    {
        $medicine_category_id = $this->input->post("medicine_category_id");
        $data                 = $this->pharmacy_model->get_medicine_dosage($medicine_category_id);
        echo json_encode($data);
    }

    public function get_dosagename()
    {
        $dosage_id           = $this->input->post("dosage_id");
        $data                = $this->pharmacy_model->get_dosagename($dosage_id);
        $data['dosage_unit'] = $data['dosage'] . " " . $data['unit'];
        echo json_encode($data);
    }

    public function get_supplier_name()
    {
        if (!$this->rbac->hasPrivilege('supplier_category', 'can_view')) {
            access_denied();
        }
        $supplier_category_id = $this->input->post("supplier_category_id");
        $data                 = $this->pharmacy_model->get_supplier_name($supplier_category_id);
        echo json_encode($data);
    }

    public function addBill()
    {
        if (!$this->rbac->hasPrivilege('pharmacy_bill', 'can_add')) {
            access_denied();
        }
        $duplicate_medicine = false;
        $medicines          = array();
       
        $prescription_no    = $this->input->post('is_prescription_no', TRUE);

        $custom_fields = $this->customfield_model->getByBelong('pharmacy');
        $action_type   = $this->input->post('action_type', TRUE);
        foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
            if ($custom_fields_value['validation']) {
                $custom_fields_id   = $custom_fields_value['id'];
                $custom_fields_name = $custom_fields_value['name'];
                $this->form_validation->set_rules("custom_fields[pharmacy][" . $custom_fields_id . "]", $custom_fields_name, 'trim|required|xss_clean');
            }
        }
        $this->form_validation->set_rules('bill_no', $this->lang->line('bill_no'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('net_amount', $this->lang->line('net_amount'), 'trim|required|xss_clean');
        if ($this->input->post('payment_mode', TRUE) == "Cheque") {
            $this->form_validation->set_rules('cheque_no', $this->lang->line('cheque_no'), 'required');
            $this->form_validation->set_rules('cheque_date', $this->lang->line('cheque_date'), 'required');
            $this->form_validation->set_rules('document', $this->lang->line('document'), 'callback_handle_doc_upload[document]|callback_validateCanUploadFile[document]');
        }

        if ($action_type !== 'update') {
            $this->form_validation->set_rules(
                'payment_amount', $this->lang->line('payment_amount'), array('required', 'xss_clean',
                    array('check_exists', array($this->pharmacy_model, 'validate_paymentamount')),
                )
            );
            // SaaS: count pre-check — block a NEW pharmacy bill once the tenant
            // hits its no_of_pharmacy limit. Create-only (never on update).
            $this->form_validation->set_rules('validate_resource', $this->lang->line('pharmacy'), 'callback_validateCanAddNewResource[no_of_pharmacy,1]');
        }

        $total_rows = $this->input->post('total_rows', TRUE);
        if (!isset($total_rows) && !isset($pathology) && !isset($radiology)) {
            $this->form_validation->set_rules('no_records', $this->lang->line('no_records'), 'trim|required|xss_clean',
                array('required' => $this->lang->line('no_medicine_selected')));
        }

        $medication_details = array();

        if (isset($total_rows) && !empty($total_rows)) {
            foreach ($total_rows as $row_key => $row_value) {

                $medicine_name      = $this->input->post('medicine_name_id_' . $row_value, TRUE);
                $batch_no           = $this->input->post('batch_no_id_' . $row_value, TRUE);
                $expire_date        = $this->input->post('expire_date_' . $row_value, TRUE);
                $quantity           = $this->input->post('quantity_' . $row_value, TRUE);
                $available_quantity = $this->input->post('available_quantity_' . $row_value, TRUE);
                $sale_price         = $this->input->post('sale_price_' . $row_value, TRUE);

                $expiry        = $this->input->post('expiry_' . $row_value, TRUE);

                $get_medicine_name = $this->notificationsetting_model->getmedicineDetails($medicine_name);
                if (!empty($get_medicine_name)) {
                    $medication_details[] = $get_medicine_name['medicine_name'] . ' (' . $batch_no . ')';
                }
                if ($quantity != "" && ($available_quantity < $quantity)) {
                    $this->form_validation->set_rules('over_quantity_demand', $this->lang->line('order_quantity'), 'required', array('required' => $this->lang->line('order_quantity_should_not_be_greater_than_available_quantity')));
                }
                if ($medicine_name == "") {
                    $this->form_validation->set_rules('medicine_name', $this->lang->line('medicine_name'), 'trim|required|xss_clean');
                }
                if ($batch_no == "") {
                    $this->form_validation->set_rules('batch_no', $this->lang->line('batch_no'), 'required');
                } else {
                    $medicines[] = $batch_no;
                }
                if ($expire_date == "") {
                }
                if ($quantity == "") {
                    $this->form_validation->set_rules('quantity', $this->lang->line('quantity'), 'required|numeric');
                }
                if ($sale_price == "") {
                    $this->form_validation->set_rules('sale_price', $this->lang->line('sale_price'), 'required|numeric');
                }
                if ($sale_price == "") {
                    $this->form_validation->set_rules('amount', $this->lang->line('amount'), 'required|numeric');
                }

                $exp_date=date('Y-m-d',strtotime("$expiry"));
                $cur_date=date('Y-m-d');;
                if (strtotime("$exp_date") < strtotime("$cur_date")) {
                     $this->form_validation->set_rules('expire_date', $this->lang->line('expiry_date'), 'required', array('required' => ('Medicine Expired')));
                }
            }
        }
	
        if (!empty($medicines)) {
            $duplicate = chkDuplicate($medicines);
            if (!empty($duplicate)) {
                $this->form_validation->set_rules('duplicate_medicine', $this->lang->line('duplicate_medicine'), 'required', array('required' => $this->lang->line('duplicate_medicines_found')));
            }
        }

        $this->form_validation->set_rules('patient_id', $this->lang->line('patient'), 'trim|required|xss_clean');
        if ($this->form_validation->run() == false) {
            $msg = array(
                'bill_no'              => form_error('bill_no'),
                'no_records'           => form_error('no_records'),
                'duplicate_medicine'   => form_error('duplicate_medicine'),
                'over_quantity_demand' => form_error('over_quantity_demand'),
                'medicine_name'        => form_error('medicine_name'),
                'batch_no'             => form_error('batch_no'),
                'expire_date'          => form_error('expire_date'),
                'quantity'             => form_error('quantity'),
                'sale_price'           => form_error('sale_price'),
                'amount'               => form_error('amount'),
                'patient_id'           => form_error('patient_id'),
                'net_amount'           => form_error('net_amount'),
                'payment_amount'       => form_error('payment_amount'),
                'cheque_no'            => form_error('cheque_no'),
                'cheque_date'          => form_error('cheque_date'),
                'document'             => form_error('document'),
                'validate_resource'    => form_error('validate_resource'),
            );
            if (!empty($custom_fields)) {
                foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
                    if ($custom_fields_value['validation']) {
                        $custom_fields_id                                                 = $custom_fields_value['id'];
                        $custom_fields_name                                               = $custom_fields_value['name'];
                        $error_msg2["custom_fields[pharmacy][" . $custom_fields_id . "]"] = form_error("custom_fields[pharmacy][" . $custom_fields_id . "]");
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
            $payment_section        = $this->config->item('payment_section');
            $patient_id             = $this->input->post('patient_id', TRUE);
            $bill_date              = $this->input->post("date", TRUE);
            $bill_no                = $this->input->post('bill_no', TRUE);
            $pharmacy_bill_basic_id = $this->input->post('pharmacy_bill_basic_id', TRUE);
            $case_reference_id      = $this->input->post('case_reference_id', TRUE);

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
                $organisation_id    = null;
                $insurance_id       = null;
                $insurance_validity = null;
            }else{
                $organisation_id    = $this->input->post('organisation_id', TRUE);
                $insurance_id       = $this->input->post('insurance_id', TRUE);
                $insurance_validity = $this->customlib->dateFormatToYYYYMMDD($this->input->post('insurance_validity', TRUE));
            }
            if (empty($organisation_id)) {
                $organisation_id    = null;
                $insurance_validity = null;
                $insurance_id       = null;
            }

            $data['opd_prefix'] = $this->opd_prefix;
            $bill_detail        = array(
                'id'                        => $pharmacy_bill_basic_id,
                'case_reference_id'         => $case_reference_id,
                'date'                      => $this->customlib->dateFormatToYYYYMMDDHis($bill_date, $this->time_format),
                'patient_id'                => $patient_id,
                'customer_name'             => $this->input->post('customer_name', TRUE),
                'ipd_prescription_basic_id' => $prescription_no,
                'doctor_name'               => $this->input->post('doctor_name', TRUE),
                'total'                     => $this->input->post('total', TRUE),
                'discount'                  => $this->input->post('discount', TRUE),
                'tax'                       => $this->input->post('tax', TRUE),
                'net_amount'                => $this->input->post('net_amount', TRUE),
                'note'                      => $this->input->post('note', TRUE),
                'discount_percentage'       => $this->input->post('discount_percent', TRUE),
                'tax_percentage'            => $this->input->post('tax_percent', TRUE),
                'generated_by'              => $this->customlib->getLoggedInUserID(),
                'organisation_id'           => $organisation_id,
                'insurance_id'              => $insurance_id,
                'insurance_validity'        => $insurance_validity,
            );

            $custom_field_post  = $this->input->post("custom_fields[pharmacy]", TRUE);
            $custom_value_array = array();
            if (!empty($custom_field_post)) {
                foreach ($custom_field_post as $key => $value) {
                    $check_field_type = $this->input->post("custom_fields[pharmacy][" . $key . "]", TRUE);
                    $field_value      = is_array($check_field_type) ? implode(",", $check_field_type) : $check_field_type;
                    $array_custom     = array(
                        'belong_table_id' => 0,
                        'custom_field_id' => $key,
                        'field_value'     => $field_value,
                    );
                    $custom_value_array[] = $array_custom;
                }
            }

            if ($action_type == 'update') {

                $insert_medicines = array();
                $update_medicines = array();

                $prev_array   = $this->input->post('previous_ids', TRUE);
                $update_array = array();
                $total_rows   = $this->input->post('total_rows', TRUE);
                if (isset($total_rows) && !empty($total_rows)) {
                    foreach ($total_rows as $row_key => $row_value) {
                        $inserted_id = $this->input->post('insert_id_' . $row_value, TRUE);
                        if ($inserted_id == 0) {

                            $insert_medicines[] = array(
                                'pharmacy_bill_basic_id'   => 0,
                                'medicine_batch_detail_id' => $this->input->post('batch_no_id_' . $row_value, TRUE),
                                'quantity'                 => $this->input->post('quantity_' . $row_value, TRUE),
                                'sale_price'               => $this->input->post('sale_price_' . $row_value, TRUE),
                                'discount'                 => $this->input->post('mdiscount_' . $row_value, TRUE),
                            );

                        } elseif ($inserted_id != 0) {
                            $update_array[]     = $inserted_id;
                            $update_medicines[] = array(
                                'id'                       => $inserted_id,
                                'pharmacy_bill_basic_id'   => $pharmacy_bill_basic_id,
                                'medicine_batch_detail_id' => $this->input->post('batch_no_id_' . $row_value, TRUE),
                                'quantity'                 => $this->input->post('quantity_' . $row_value, TRUE),
                                'sale_price'               => $this->input->post('sale_price_' . $row_value, TRUE),
                                'discount'                 => $this->input->post('mdiscount_' . $row_value, TRUE),
                            );
                        }
                    }
                }

                $payment_amount = $this->input->post('payment_amount', TRUE);
                $cheque_date    = $this->input->post('cheque_date', TRUE);
                if (!empty($payment_amount)) {
                    $payment_array = array(
                        'amount'                 => $this->input->post('payment_amount', TRUE),
                        'type'                   => 'refund',
                        'case_reference_id'      => $case_reference_id,
                        'patient_id'             => $patient_id,
                        'section'                => $payment_section['pharmacy'],
                        'pharmacy_bill_basic_id' => $pharmacy_bill_basic_id,
                        'payment_mode'           => $this->input->post('payment_mode', TRUE),
                        'note'                   => $this->input->post('note', TRUE),
                        'payment_date'           => date('Y-m-d H:i:s'),
                        'received_by'            => $this->customlib->getLoggedInUserID(),
                    );

                    $cheque_date = $this->input->post("cheque_date", TRUE);
                    if ($this->input->post('payment_mode', TRUE) == "Cheque") {
                        $payment_array['cheque_date']     = $this->customlib->dateFormatToYYYYMMDD($cheque_date);
                        $payment_array['cheque_no']       = $this->input->post('cheque_no', TRUE);
                           
                           if (isset($_FILES["document"]) && !empty($_FILES['document']['name'])) {
                            $file_name = $this->media_storage->fileupload("document",'./uploads/payment_document/');
                            $payment_array['attachment']        = $file_name;
                            $payment_array['attachment_name']   = $_FILES["document"]["name"];

                            // SaaS: add the uploaded document's size to the storage quota usage.
                            try {
                                $saas_quota_result = $this->saasvalidation->updateStorageLimit('storage', ['document']);
                                // Capture silent API failures (lib returns JSON status:false instead of throwing).
                                if (is_string($saas_quota_result)) {
                                    $saas_quota_decoded = json_decode($saas_quota_result);
                                    if (isset($saas_quota_decoded->status) && $saas_quota_decoded->status === false) {
                                        log_message('error', 'SaaS storage quota update returned failure (pharmacy addBill update): ' . ($saas_quota_decoded->message ?? 'unknown'));
                                    }
                                }
                            } catch (Exception $e) {
                                log_message('error', 'SaaS storage quota update failed (pharmacy addBill update): ' . $e->getMessage());
                            }
                        }
                    }

                } else {
                    $payment_array = array();
                }

                if(!empty($prev_array)){
					$delete_result = array_diff($prev_array, $update_array);
				}else{
					$delete_result	= array();
				}

                $is_inserted = $this->pharmacy_model->addBill($bill_detail, $insert_medicines, $update_medicines, $delete_result, $payment_array);

                if (!empty($custom_fields)) {
                    foreach ($custom_field_post as $key => $value) {
                        $check_field_type = $this->input->post("custom_fields[pharmacy][" . $key . "]", TRUE);
                        $field_value      = is_array($check_field_type) ? implode(",", $check_field_type) : $check_field_type;
                        $array_custom     = array(
                            'belong_table_id' => $is_inserted,
                            'custom_field_id' => $key,
                            'field_value'     => $field_value,
                        );
                        $custom_value_array[] = $array_custom;
                    }
                    $this->customfield_model->updateRecord($custom_value_array, $is_inserted, 'pharmacy');
                }

                //====================
            } else {

                $payment_amount   = $this->input->post('payment_amount', TRUE);
                $cheque_date      = $this->input->post('cheque_date', TRUE);
                $insert_medicines = array();
                $total_rows       = $this->input->post('total_rows', TRUE);
                if (isset($total_rows) && !empty($total_rows)) {
                    foreach ($total_rows as $row_key => $row_value) {
                        $insert_medicines[] = array(
                            'medicine_batch_detail_id' => $this->input->post('batch_no_id_' . $row_value, TRUE),
                            'quantity'                 => $this->input->post('quantity_' . $row_value, TRUE),
                            'sale_price'               => $this->input->post('sale_price_' . $row_value, TRUE),
                            'discount'                 => $this->input->post('mdiscount_' . $row_value, TRUE),
                        );
                    }
                }

                if ($payment_amount >= 0) {

                    $payment_array = array(
                        'amount'       => $this->input->post('payment_amount', TRUE),
                        'type'         => 'payment',
                        'patient_id'   => $patient_id,
                        'section'      => $payment_section['pharmacy'],
                        'payment_mode' => $this->input->post('payment_mode', TRUE),
                        'note'         => $this->input->post('note', TRUE),
                        'payment_date' => $this->customlib->dateFormatToYYYYMMDDHis($bill_date, $this->time_format),
                        'received_by'  => $this->customlib->getLoggedInUserID(),
                    );

                    if ($this->input->post('payment_mode', TRUE) == "Cheque") {
                        $payment_array['cheque_date'] = $this->customlib->dateFormatToYYYYMMDD($cheque_date);
                        $payment_array['cheque_no']   = $this->input->post('cheque_no', TRUE);
                       
                        if (isset($_FILES["document"]) && !empty($_FILES['document']['name'])) {
                            $file_name = $this->media_storage->fileupload("document",'./uploads/payment_document/');
                            $payment_array['attachment']        = $file_name;
                            $payment_array['attachment_name']   = $_FILES["document"]["name"];

                            // SaaS: add the uploaded document's size to the storage quota usage.
                            try {
                                $saas_quota_result = $this->saasvalidation->updateStorageLimit('storage', ['document']);
                                // Capture silent API failures (lib returns JSON status:false instead of throwing).
                                if (is_string($saas_quota_result)) {
                                    $saas_quota_decoded = json_decode($saas_quota_result);
                                    if (isset($saas_quota_decoded->status) && $saas_quota_decoded->status === false) {
                                        log_message('error', 'SaaS storage quota update returned failure (pharmacy addBill insert): ' . ($saas_quota_decoded->message ?? 'unknown'));
                                    }
                                }
                            } catch (Exception $e) {
                                log_message('error', 'SaaS storage quota update failed (pharmacy addBill insert): ' . $e->getMessage());
                            }
                        }
                    }

                } else {
                    $payment_array = array();
                }

                $is_inserted = $this->pharmacy_model->addBill($bill_detail, $insert_medicines, array(), array(), $payment_array);

                if (!empty($custom_value_array)) {
                    $this->customfield_model->insertRecord($custom_value_array, $is_inserted);
                }

                // SaaS: a NEW pharmacy bill was created — increment the no_of_pharmacy
                // count usage. Create-only branch; wrapped so an API hiccup never blocks the save.
                if ($is_inserted) {
                    try {
                        $this->saasvalidation->updateResouceQuota('no_of_pharmacy', 1);
                    } catch (Exception $e) {
                        log_message('error', 'SaaS no_of_pharmacy count increment failed (pharmacy addBill insert): ' . $e->getMessage());
                    }
                }
            }

            if ($is_inserted) {
                $array = array('status' => 1, 'error' => '', 'message' => $this->lang->line('success_message'), 'insert_id' => $is_inserted);

                $medication_details = implode(',', $medication_details);
                $due_amount         = $this->input->post('net_amount', TRUE) - $this->input->post('payment_amount', TRUE);

                $event_data = array(
                    'patient_id'       => $patient_id,
                    'case_id'          => $case_reference_id,
                    'bill_no'          => $this->customlib->getSessionPrefixByType('pharmacy_billing') . $bill_no,
                    'medicine_details' => $medication_details,
                    'doctor_name'      => $this->input->post('doctor_name', TRUE),
                    'total'            => $this->input->post('total', TRUE),
                    'discount'         => $this->input->post('discount', TRUE),
                    'tax'              => $this->input->post('tax', TRUE),
                    'net_amount'       => $this->input->post('net_amount', TRUE),
                    'paid'             => $this->input->post('payment_amount', TRUE),
                    'due_amount'       => number_format((float) $due_amount, 2, '.', ''),
                    'date'             => $this->customlib->YYYYMMDDHisTodateFormat($bill_date, $this->customlib->getHospitalTimeFormat()),
                );

                $this->system_notification->send_system_notification('pharmacy_generate_bill', $event_data);
            } else {
                $array = array('status' => 0, 'message' => $this->lang->line('something_went_wrong'));
            }
        }
        echo json_encode($array);
    }

    public function getBillDetails()
    {
        if (!$this->rbac->hasPrivilege('pharmacy_bill', 'can_view')) {
            access_denied();
        }
        $id      = $this->input->get('id');
        $print   = $this->input->get('print');
        $is_bill = $this->input->get('is_bill');

        $print_details         = $this->printing_model->get('', 'pharmacy');
        $data["print_details"] = $print_details;
        $data['id']            = $id;
        if (isset($print)) {
            $data["print"] = true;
            $check_print   = 'print';
        } else {
            $data["print"] = false;
            $check_print   = '';
        }
        if (isset($is_bill)) {
            $data["is_bill"] = false;
            $bill_print      = "print_pharmacy_bill";
        } else {
            $data["is_bill"] = true;
            $bill_print      = "print_bill";
        }
        if ($check_print == 'print') {
            $data['fields']      = $this->customfield_model->get_custom_fields('pharmacy', '', 1);
            $data['check_print'] = $check_print;
        } else {
            $data['fields']      = $this->customfield_model->get_custom_fields('pharmacy');
            $data['check_print'] = $check_print;
        }
        $result = $this->pharmacy_model->getBillDetails($id, $data['check_print']);
        $data['result'] = $result;
        $bill_no    = $result['id'];
        $patient_id = $result['patient_id'];
        $ipd_prescription_basic_id = $result['ipd_prescription_basic_id'];  
        
        $ipd_opd = $this->pharmacy_model->getIpdPrescriptionBasic($ipd_prescription_basic_id);   
        if($ipd_prescription_basic_id!=""){
            if($ipd_opd->ipd_id != ''){             
              $data['prescription']   =   $this->customlib->getSessionPrefixByType('ipd_prescription').$ipd_opd->id ;   
            }else{
                $data['prescription']   =   $this->customlib->getSessionPrefixByType('opd_prescription').$ipd_opd->id ; 
            }   
        }else{
            $data['prescription']   ="" ;
        }           
        
        $detail = $this->pharmacy_model->getAllBillDetails($id);
      
        $data['detail'] = $detail;
        $action_details = "";
        if ($this->rbac->hasPrivilege('pharmacy_bill', 'can_view')) {
            $action_details .= "<a href='#' class='btn btn-sm btn-light " . $bill_print . "' data-loading-text='<i class=\"fa fa-circle-o-notch fa-spin\"></i>' data-bs-toggle='tooltip' title='" . $this->lang->line('print') . "' data-record-id='" . $id . "'><i class='fa fa-print'></i></a>";
        }

        if ($this->rbac->hasPrivilege('pharmacy_bill', 'can_edit')) {
            if ($data["is_bill"]) {
                $action_details .= "<a href='#' class='btn btn-sm btn-light edit_bill' data-record-id='" . $id . "' data-prescription-id='" . $data['prescription'] . "' data-bs-toggle='tooltip' title='" . $this->lang->line('edit') . "'><i class='fa fa-pencil'></i></a>";
            }
        }

        if ($this->rbac->hasPrivilege('pharmacy_bill', 'can_delete')) {
            if ($data["is_bill"]) {
                $action_details .= "<a href='#' class='btn btn-sm btn-light delete-record' data-record-id='" . $id . "' data-bs-toggle='tooltip' title='" . $this->lang->line('delete') . "'><i class='fa fa-trash'></i></a>";
            }
        }

        $page = $this->load->view('admin/pharmacy/_getBillDetails', $data, true);
        echo json_encode(array('status' => 1, 'page' => $page, 'actions' => $action_details));
    }

    public function getPharmacyTransaction()
    {
        $pharmacy_bill_basic_id         = $this->input->post('id');
        $pharmacy_bill_detail           = $this->pharmacy_model->getBillDetails($pharmacy_bill_basic_id);
      
        $balance_amount                 = (($pharmacy_bill_detail['net_amount']+$pharmacy_bill_detail['refund_amount']) - ($pharmacy_bill_detail['paid_amount']));
        $pharmacy_transaction           = $this->transaction_model->pharmacyPayments($pharmacy_bill_basic_id);
        $data["balance_amount"]         = amountFormat($balance_amount);
        $data["pharmacy_bill_basic_id"] = $pharmacy_bill_basic_id;
        $data["payment_mode"]           = $this->payment_mode;
        $data['pharmacy_transaction']   = $pharmacy_transaction;
        $data['pharmacy_bill_detail']   = $pharmacy_bill_detail;
        $is_bill                        = $this->input->post('is_bill');
        if (isset($is_bill)) {
            $data['view_delete'] = false;
        } else {
            $data['view_delete'] = true;
        }

        $page = $this->load->view("admin/pharmacy/_getPharmacyTransaction", $data, true);
        echo json_encode(array('status' => 1, 'page' => $page));
    }

    public function createBill()
    {
        if (!$this->rbac->hasPrivilege('pharmacy_bill', 'can_view')) {
            access_denied();
        }
        $id                       = $this->input->post('id');
        $medicineCategory         = $this->medicine_category_model->getMedicineCategory();
        $data["medicineCategory"] = $medicineCategory;
        $data["medicineName"]     = $this->pharmacy_model->getMedicineName();
        $patients                 = $this->patient_model->getPatientListall();
        $data["patients"]         = $patients;
        $doctors                  = $this->staff_model->getStaffbyrole(3);
        $data["doctors"]          = $doctors;
        $data["payment_mode"]     = $this->payment_mode;
        $result                   = $this->pharmacy_model->getBillNo();
        $id                       = $result["id"];
        if (!empty($result["id"])) {
            $bill_no = $id + 1;
        } else {
            $bill_no = 1;
        }

        $page = $this->load->view("admin/pharmacy/_createBill", $data, true);
        echo json_encode(array('status' => 1, 'page' => $page, 'bill_no' => $bill_no));
    }

    public function editBill()
    {
        if (!$this->rbac->hasPrivilege('pharmacy_bill', 'can_view')) {
            access_denied();
        }
        $id                          = $this->input->get('id');
        $medicineCategory            = $this->medicine_category_model->getMedicineCategory();
        $data["medicineCategory"]    = $medicineCategory;
        $patients                    = $this->patient_model->getPatientListall();
        $data["patients"]            = $patients;
        $doctors                     = $this->staff_model->getStaffbyrole(3);
        $data["doctors"]             = $doctors;
        $bill                        = $this->pharmacy_model->getBillDetails($id);       
        $data['bill']                = $bill;
        $detail                      = $this->pharmacy_model->getAllBillDetails($id);
        $data['detail']              = $detail;
        $data["payment_mode"]        = $this->payment_mode;
        $data['custom_fields_value'] = display_custom_fields('pharmacy', $id);       
        $page = $this->load->view("admin/pharmacy/_editBill", $data, true);
        $total_rows = count($detail); 
        echo json_encode(array('status' => 1, 'page' => $page, 'paid_amount' => $bill['paid_amount'],'refund_amount' => $bill['refund_amount'], 'patient_id' => $bill['patient_id'], 'patient_name' => $bill['patient_name'], 'bill_no' => $this->customlib->getSessionPrefixByType('pharmacy_billing').$bill['id'], 'date' => $bill['date'], 'case_reference_id' => $bill['case_reference_id'], 'total_rows' => $total_rows,'organisation_id'=>$bill['organisation_id']));
    }
 
    public function getSupplierDetails($id)
    {
        if (!$this->rbac->hasPrivilege('medicine_purchase', 'can_view')) {
            access_denied();
        }
        $data['id'] = $id;
        if (isset($_POST['print'])) {
            $data["print"] = 'yes';
        } else {
            $data["print"] = 'no';
        }

        $result         = $this->pharmacy_model->getSupplierDetails($id);
        $data['result'] = $result;
        $detail         = $this->pharmacy_model->getAllSupplierDetails($id);
        $data['detail'] = $detail;
        $return_history = $this->pharmacy_model->getPurchaseReturnHistory($id);
        foreach ($return_history as $key => $row) {
            $return_history[$key]['items'] = $this->pharmacy_model->getPurchaseReturnDetail($row['id']);
        }
        $data['return_history'] = $return_history;
        $this->load->view('admin/pharmacy/printPurchase', $data);
    }

    public function printSupplierDetails($id)
    {
        if (!$this->rbac->hasPrivilege('medicine_purchase', 'can_view')) {
            access_denied();
        }
        $data['id']            = $id;
        $data['print_details'] = $this->printing_model->get('', 'pharmacy');
        $data['result']        = $this->pharmacy_model->getSupplierDetails($id);
        $data['detail']        = $this->pharmacy_model->getAllSupplierDetails($id);
        $return_history        = $this->pharmacy_model->getPurchaseReturnHistory($id);
        foreach ($return_history as $key => $row) {
            $return_history[$key]['items'] = $this->pharmacy_model->getPurchaseReturnDetail($row['id']);
        }
        $data['return_history'] = $return_history;
        $this->load->view('admin/pharmacy/printSupplierBill', $data);
    }

    public function download($file)
    {
        $this->load->helper('download');
        $filepath = "./uploads/medicine_images/" . $this->uri->segment(6);
        $data     = file_get_contents($filepath);
        $name     = $this->uri->segment(6);
        force_download($name, $data);
    }

    public function getQuantity()
    {
        if (!$this->rbac->hasPrivilege('medicine', 'can_view')) {
            access_denied();
        }
        $batch_no = $this->input->get('batch_no');
        $med_id   = $this->input->get('med_id');
        $data     = $this->pharmacy_model->getQuantity($batch_no, $med_id);
        echo json_encode($data);
    }

    public function getQuantityedit()
    {
        if (!$this->rbac->hasPrivilege('medicine', 'can_view')) {
            access_denied();
        }
        $batch_no = $this->input->get('batch_no');
        $data     = $this->pharmacy_model->getQuantityedit($batch_no);
        echo json_encode($data);
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
                'search_type'   => $this->input->post('search_type', TRUE),
                'collect_staff' => $this->input->post('collect_staff', TRUE),
                'gender'        => $this->input->post('gender', TRUE),
                'from_age'      => $this->input->post('from_age', TRUE),
                'to_age'        => $this->input->post('to_age', TRUE),
                'date_from'     => $this->input->post('date_from', TRUE),
                'date_to'       => $this->input->post('date_to', TRUE),
                'payment_mode'  => $this->input->post('payment_mode', TRUE),
                'doctor_name'   => $this->input->post('doctor_name', TRUE),
            );

            $json_array = array('status' => 'success', 'error' => '', 'param' => $param, 'message' => $this->lang->line('success_message'));
        }
        echo json_encode($json_array);
    }

    public function billreport()
    {
        if (!$this->rbac->hasPrivilege('pharmacy_bill', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'Reports');
        $this->session->set_userdata('sub_menu', 'reports/pharmacy');
        $this->session->set_userdata('subsub_menu', 'reports/pharmacy/billreport');

        $custom_fields = $this->customfield_model->get_custom_fields('pharmacy', '', '', 1);
        $data['fields']       = $custom_fields;
        $staffsearch          = $this->patient_model->getstaffpharmacybill();
        $data['staffsearch']  = $staffsearch;
        $search_type          = "this_month";
        $data["searchlist"]   = $this->search_type;
        $data['agerange']     = $this->agerange;
        $data['gender']       = $this->customlib->getGender_Patient();
        $this->payment_mode   = $this->config->item('payment_mode');
        $data['payment_mode'] = $this->payment_mode;
        $data['pharmacydoctor'] = $this->pharmacy_model->getpharmacydoctor();       
        
        $data['module'] = 'pharmacy';
        $this->load->view('layout/header', $data);
        $this->load->view('admin/pharmacy/billReport', $data);
        $this->load->view('layout/footer', $data);
    }

    public function pharmacyreports()
    {
        $search['search_type']   = $this->input->post('search_type');
        $search['collect_staff'] = $this->input->post('collect_staff');
        $search['date_from']     = $this->input->post('date_from');
        $search['date_to']       = $this->input->post('date_to');
        $search['from_age']      = $this->input->post('from_age');
        $search['to_age']        = $this->input->post('to_age');
        $search['gender']        = $this->input->post('gender');
        $search['payment_mode']  = $this->input->post('payment_mode');
        $search['doctor_name']   = $this->input->post('doctor_name');
        
        $currency_symbol = $this->customlib->getHospitalCurrencyFormat();
        
        $start_date = '';
        $end_date   = '';
        $fields     = $this->customfield_model->get_custom_fields('pharmacy', '', '', 1);
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

        $reportdata = $this->transaction_model->pharmacybillreportsRecord($start_date, $end_date, $search);
 
        $reportdata = json_decode($reportdata);
        $dt_data    = array();

        $total_amount = 0;
        $total_discount = 0;
        $total_tax = 0;
        $total_net  = 0;
        $total_paid  = 0;
        $total_refund  = 0;
        $total_balance  = 0;
		
        if (!empty($reportdata->data)) {	 
            foreach ($reportdata->data as $key => $value) {

                $total_amount += $value->total;
                $total_discount += $value->discount;
                $total_tax += $value->tax;
                $total_paid += $value->paid_amount;
                $total_net += $value->net_amount;
                $total_refund += $value->refund_amount;                
				
				$balance_amount = ($value->net_amount-($value->paid_amount-$value->refund_amount));
				$balance = number_format($balance_amount, 2, '.', '');
                $total_balance+= $balance ; 
				
                $prescription_no = "";				
				
                if ($value->ipd_id != "") {
                    $prescription_no = $this->customlib->getSessionPrefixByType('ipd_prescription') . $value->ipd_prescription_basic_id;
                } elseif ($value->visit_details_id != "") {
                    // code...
                    $prescription_no = $this->customlib->getSessionPrefixByType('opd_prescription') . $value->ipd_prescription_basic_id;
                }
                
                $action1 = "<a href='#' onclick='viewDetail(" . $value->id . ")'>";
                $action2 = "</a>";                
                $row   = array();              
                
                $row[] = $action1.$this->customlib->getSessionPrefixByType('pharmacy_billing') . $value->id.$action2;
                $row[] = $this->customlib->YYYYMMDDHisTodateFormat($value->date);
                $row[] = composePatientName($value->patient_name, $value->patient_id);
                $row[] = $this->customlib->get_patient_current_age($value->patient_id);
                $row[] = ($value->gender) ? $this->lang->line(strtolower($value->gender)):"";
                $row[] = $prescription_no;
                $row[] = $value->doctor_name;
                $row[] = $value->name . " " . $value->surname . "(" . $value->employee_id . ")";
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
				
                $row[]     = $value->total;
                $row[]     = $value->discount.' ('.$value->discount_percentage.'%)';

                $discount_amt = ( $value->total * $value->discount_percentage ) / 100 ; 
                $tax_amt = (($value->total - $discount_amt) * $value->tax_percentage ) / 100;
                $taxable_amt = $value->total - $discount_amt;
                $tax_pct     = $taxable_amt != 0 ? ($value->tax * 100) / $taxable_amt : 0;
                $row[]       = $value->tax . " (" . amountFormat($tax_pct, 2) . "%)";
                       
                $row[]     = $value->net_amount;				
                $row[]     = $value->paid_amount;
                $row[]     = $value->refund_amount;
                $row[]     = $balance;				
				
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
                        
                        $footer_row[] = '';
                        
                    }
                }
                
            $footer_row[] = "<b>" . $this->lang->line('total_amount') . "</b>" . ':';
            $footer_row[] = "<b>" . $currency_symbol . (number_format($total_amount, 2, '.', '')) . "<br/>";
            $footer_row[] = "<b>" . $currency_symbol . (number_format($total_discount, 2, '.', '')) . "<br/>";
            $footer_row[] = "<b>" . $currency_symbol . (number_format($total_tax, 2, '.', '')) . "<br/>";			
            $footer_row[] = "<b>" . $currency_symbol . (number_format($total_net, 2, '.', '')) . "<br/>";		
            $footer_row[] = "<b>" . $currency_symbol . (number_format($total_paid, 2, '.', '')) . "<br/>";
            $footer_row[] = "<b>" . $currency_symbol . (number_format($total_refund, 2, '.', '')) . "<br/>";
            $footer_row[] = "<b>" . $currency_symbol . (number_format($total_balance, 2, '.', '')) . "<br/>";

            $dt_data[] = $footer_row;
        }

        $json_data = array(
            "draw"            => intval($reportdata->draw),
            "recordsTotal"    => intval($reportdata->recordsTotal),
            "recordsFiltered" => intval($reportdata->recordsFiltered),
            "data"            => $dt_data,
        );
        echo json_encode($json_data);
    }

    public function editPharmaBill($id)
    {
        if (!$this->rbac->hasPrivilege('pharmacy_bill', 'can_view')) {
            access_denied();
        }

        $id               = $this->input->post("id");
        $patients         = $this->patient_model->getPatientListall();
        $data["patients"] = $patients;
        $result           = $this->pharmacy_model->getBillDetails($id);
        $data['result']   = $result;
        echo json_encode($result);
    }

    public function editSupplierBill($id)
    {
        if (!$this->rbac->hasPrivilege('medicine_purchase', 'can_view')) {
            access_denied();
        }
        $medicineCategory             = $this->medicine_category_model->getMedicineCategory();
        $data["medicineCategory"]     = $medicineCategory;
        $medicine_category_id         = $this->input->post("medicine_category_id");
        $data['medicine_category_id'] = $this->pharmacy_model->get_medicine_name($medicine_category_id);
        $data['medicine_category_id'] = $medicine_category_id;
        $supplierCategory             = $this->medicine_category_model->getSupplierCategory();
        $data["supplierCategory"]     = $supplierCategory;
        $supplier_category_id         = $this->input->post("supplier_category_id");
        $data['supplier_category_id'] = $this->pharmacy_model->get_supplier_name($supplier_category_id);
        $data['supplier_category_id'] = $supplier_category_id;

        $result         = $this->pharmacy_model->getSupplierDetails($id);
        $data['result'] = $result;
        $detail         = $this->pharmacy_model->getAllSupplierDetails($id);
        $data['detail'] = $detail;
        $this->load->view("admin/pharmacy/editSupplierBill", $data);
    }

    public function updateBill()
    {
        if (!$this->rbac->hasPrivilege('pharmacy_bill', 'can_edit')) {
            access_denied();
        }

        $this->form_validation->set_rules('bill_no', $this->lang->line('bill_no'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('date', $this->lang->line('date'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('patient_id', $this->lang->line('patient'), 'trim|required');
        $this->form_validation->set_rules('medicine_category_id[]', $this->lang->line('medicine_category'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('medicine_name[]', $this->lang->line('medicine_name'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('expire_date[]', $this->lang->line('expiry_date'), 'required');
        $this->form_validation->set_rules('batch_no[]', $this->lang->line('batch_no'), 'required');
        $this->form_validation->set_rules('quantity[]', $this->lang->line('quantity'), 'required|numeric');
        $this->form_validation->set_rules('sale_price[]', $this->lang->line('sale_price'), 'required|numeric');
        $this->form_validation->set_rules('amount[]', $this->lang->line('amount'), 'required|numeric');
        $this->form_validation->set_rules('total', $this->lang->line('total'), 'required|numeric');
        $this->form_validation->set_rules('file', $this->lang->line('file'), 'callback_validateCanUploadFile[file]');
        if ($this->form_validation->run() == false) {

            $msg = array(
                'bill_no'              => form_error('bill_no'),
                'file'                 => form_error('file'),
                'date'                 => form_error('date'),
                'customer_name'        => form_error('customer_name'),
                'patient_id'           => form_error('patient_id'),
                'medicine_category_id' => form_error('medicine_category_id[]'),
                'medicine_name'        => form_error('medicine_name[]'),
                'expire_date'          => form_error('expire_date[]'),
                'batch_no'             => form_error('batch_no[]'),
                'quantity'             => form_error('quantity[]'),
                'sale_price'           => form_error('sale_price[]'),
                'total'                => form_error('total'),
                'amount'               => form_error('amount[]'),
            );
            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {

            $id               = $this->input->post('bill_basic_id', TRUE);
            $bill_id          = $this->input->post("bill_detail_id[]", TRUE);
            $previous_bill_id = $this->input->post("previous_bill_id[]", TRUE);
            $bill_date        = $this->input->post("date", TRUE);
            $data_array       = array();
            $delete_arr       = array();
            foreach ($previous_bill_id as $pkey => $pvalue) {
                if (in_array($pvalue, $bill_id)) {

                } else {
                    $delete_arr[] = array('id' => $pvalue);
                }
            }

            $data = array(
                'id'            => $id,
                'bill_no'       => $this->input->post('bill_no', TRUE),
                'patient_id'    => $this->input->post('patient_id', TRUE),
                'date'          => $this->customlib->dateFormatToYYYYMMDDHis($bill_date, $this->time_format),
                'customer_name' => $this->input->post('customer_name', TRUE),
                'customer_type' => $this->input->post('customer_type', TRUE),
                'doctor_name'   => $this->input->post('doctor_name', TRUE),
                'opd_ipd_no'    => $this->input->post('opd_ipd_no', TRUE),
                'total'         => $this->input->post('total', TRUE),
                'discount'      => $this->input->post('discount', TRUE),
                'note'          => $this->input->post('note', TRUE),
                'tax'           => $this->input->post('tax', TRUE),
                'net_amount'    => $this->input->post('net_amount', TRUE),
            );

            $this->pharmacy_model->addBill($data);

            if (isset($_FILES["file"]) && !empty($_FILES['file']['name'])) {
                // SaaS: capture the existing prescription image + its size BEFORE the move — a
                // same-extension upload overwrites the old file in place, so read the old size first.
                $saas_existing = $this->pharmacy_model->getdate($id);
                $saas_old_file = (!empty($saas_existing['file'])) ? $saas_existing['file'] : '';
                $saas_old_dir  = (strpos($saas_old_file, '/') !== false) ? '' : 'uploads/pres_images';
                $saas_old_kb   = (!empty($saas_old_file)) ? $this->media_storage->getUploadedFileSize($saas_old_file, $saas_old_dir) : 0;
                $saas_new_kb   = $this->media_storage->getTmpFileSize('file');

                $fileInfo = pathinfo($_FILES["file"]["name"]);
                $img_name = $id . '.' . $fileInfo['extension'];
                if (move_uploaded_file($_FILES["file"]["tmp_name"], "./uploads/pres_images/" . $img_name)) {
                    $new_file_path = 'uploads/pres_images/' . $img_name;
                    $data_img = array('id' => $id, 'file' => $new_file_path);
                    $this->pharmacy_model->addBill($data_img);

                    // SaaS: adjust storage quota by the size DIFFERENCE (new vs replaced) instead of a
                    // blind add, so editing the prescription image keeps usage accurate (was add-only).
                    try {
                        if ($saas_old_kb > $saas_new_kb) {
                            $this->saasvalidation->deleteResouceQuota('storage', $saas_old_kb - $saas_new_kb);
                        } elseif ($saas_new_kb > $saas_old_kb) {
                            $this->saasvalidation->updateResouceQuota('storage', $saas_new_kb - $saas_old_kb);
                        }
                    } catch (Exception $e) {
                        log_message('error', 'SaaS storage quota update failed (pharmacy updateBill): ' . $e->getMessage());
                    }

                    // SaaS: a different extension leaves the old file orphaned alongside the new one — remove it.
                    if (!empty($saas_old_file) && $saas_old_file !== $new_file_path) {
                        $this->media_storage->filedelete($saas_old_file, $saas_old_dir);
                    }
                }
            }

            if (!empty($id)) {
                $pharmacy_bill_basic_id = $id;
                $bill_detail_id         = $this->input->post('bill_detail_id', TRUE);
                $medicine_batch_id      = $this->input->post('medicine_batch_id', TRUE);
                $medicine_category_id   = $this->input->post('medicine_category_id', TRUE);
                $medicine_name          = $this->input->post('medicine_name', TRUE);
                $expiry_date            = $this->input->post('expire_date', TRUE);
                $batch_no               = $this->input->post('batch_no', TRUE);
                $quantity               = $this->input->post('quantity', TRUE);
                $total_quantity         = $this->input->post('available_quantity', TRUE);
                $amount                 = $this->input->post('amount', TRUE);
                $sale_price             = $this->input->post('sale_price', TRUE);
                $data                   = array();
                $i                      = 0;
                foreach ($medicine_category_id as $key => $value) {
                    if ($bill_id[$i] == 0) {
                        $add_data = array(
                            'pharmacy_bill_basic_id' => $id,
                            'medicine_category_id'   => $medicine_category_id[$i],
                            'medicine_name'          => $medicine_name[$i],
                            'expire_date'            => $expiry_date[$i],
                            'batch_no'               => $batch_no[$i],
                            'quantity'               => $quantity[$i],
                            'sale_price'             => $sale_price[$i],
                            'amount'                 => $amount[$i],
                        );
                        $data_array[]           = $add_data;
                        $available_quantity[$i] = $total_quantity[$i] - $quantity[$i];
                        $add_quantity           = array(
                            'id'                 => $medicine_batch_id[$i],
                            'available_quantity' => $available_quantity[$i],
                        );
                        $this->pharmacy_model->availableQty($add_quantity);
                    } else {
                        $detail = array(
                            'id'                     => $bill_detail_id[$i],
                            'pharmacy_bill_basic_id' => $id,
                            'medicine_category_id'   => $medicine_category_id[$i],
                            'medicine_name'          => $medicine_name[$i],
                            'expire_date'            => $expiry_date[$i],
                            'batch_no'               => $batch_no[$i],
                            'quantity'               => $quantity[$i],
                            'sale_price'             => $sale_price[$i],
                            'amount'                 => $amount[$i],
                        );
                        $this->pharmacy_model->updateBillDetail($detail);
                        $available_quantity[$i] = $total_quantity[$i] - $quantity[$i];
                        $update_quantity        = array(
                            'id'                 => $medicine_batch_id[$i],
                            'available_quantity' => $available_quantity[$i],
                        );
                        $this->pharmacy_model->availableQty($update_quantity);
                    }
                    $i++;
                }
            } else {

            }
            if (!empty($data_array)) {
                $this->pharmacy_model->addBillBatch($data_array);
            }
            if (!empty($delete_arr)) {
                $this->pharmacy_model->delete_bill_detail($delete_arr);
            }
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
     * Param format "resource_name,quantity" (e.g. "no_of_pharmacy,1").
     * Returns false (blocking the save) when adding the records would push
     * the tenant over its allowed count. SaasValidation sets the error message.
     */
    public function validateCanAddNewResource($input, $resource_name)
    {
        list($resource_name, $quantity) = explode(',', $resource_name);
        return $this->saasvalidation->validateCanAddNewResource($input, $resource_name, $quantity);
    }

    public function addBillSupplier()
    {
        $this->form_validation->set_rules('date', $this->lang->line('date'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('discount', $this->lang->line('discount'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('supplier_id', $this->lang->line('supplier'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('medicine_category_id[]', $this->lang->line('medicine_category'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('medicine_name[]', $this->lang->line('medicine_name'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('expiry_date[]', $this->lang->line('expiry_month'), 'required');
        $this->form_validation->set_rules('batch_no[]', $this->lang->line('batch_no'), 'required');
        $this->form_validation->set_rules('mrp[]', $this->lang->line('mrp'), 'required');
        $this->form_validation->set_rules('sale_rate[]', $this->lang->line('sale_price'), 'required');
        $this->form_validation->set_rules('quantity[]', $this->lang->line('quantity'), 'required|numeric');
        $this->form_validation->set_rules('purchase_price[]', $this->lang->line('purchase_price'), 'required|numeric');
        $this->form_validation->set_rules('amount[]', $this->lang->line('amount'), 'required|numeric');
        $this->form_validation->set_rules('total', $this->lang->line('total'), 'required|numeric');
        $this->form_validation->set_rules('payment_mode', $this->lang->line('payment_mode'), 'required|xss_clean|trim');
        $this->form_validation->set_rules('tax', $this->lang->line('tax'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('file', $this->lang->line("document"), 'callback_handle_doc_upload[file]|callback_validateCanUploadFile[file]');
        if ($this->input->post('payment_mode', TRUE) == "Cheque") {
            $this->form_validation->set_rules('cheque_no', $this->lang->line('cheque_no'), 'required');
            $this->form_validation->set_rules('cheque_date', $this->lang->line('cheque_date'), 'required');
            $this->form_validation->set_rules('document', $this->lang->line('document'), 'callback_handle_doc_upload[document]|callback_validateCanUploadFile[document]');
        }
        $this->form_validation->set_rules('file', '', 'callback_handle_doc_upload[file]');

        if ($this->form_validation->run() == false) {
            $msg = array(
                'date'                 => form_error('date'),
                'supplier_id'          => form_error('supplier_id'),
                'medicine_category_id' => form_error('medicine_category_id[]'),
                'medicine_name'        => form_error('medicine_name[]'),
                'batch_no'             => form_error('batch_no[]'),
                'mrp'                  => form_error('mrp[]'),
                'sale_rate'            => form_error('sale_rate[]'),
                'expiry_date'          => form_error('expiry_date[]'),
                'quantity'             => form_error('quantity[]'),
                'purchase_price'       => form_error('purchase_price[]'),
                'tax'                  => form_error('tax'),
                'discount'             => form_error('discount'),
                'total'                => form_error('total'),
                'amount'               => form_error('amount[]'),
                'document'             => form_error('file'),
                'payment_mode'         => form_error('payment_mode'),
                'cheque_no'            => form_error('cheque_no'),
                'cheque_date'          => form_error('cheque_date'),
                'file'                 => form_error('file'),
                'document'             => form_error('document'),
            );
            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $supplier_id = $this->input->post('supplier_id', TRUE);
            $bill_date   = $this->input->post("date", TRUE);

            $data = array(
                'date'         => $this->customlib->dateFormatToYYYYMMDDHis($bill_date, $this->time_format),
                'supplier_id'  => $supplier_id,
                'invoice_no'   => $this->input->post('invoice_no', TRUE),
                'total'        => $this->input->post('total', TRUE),
                'discount'     => $this->input->post('discount', TRUE),
                'tax'          => $this->input->post('tax', TRUE),
                'net_amount'   => $this->input->post('net_amount', TRUE),
                'note'         => $this->input->post('note', TRUE),
                'payment_mode' => $this->input->post('payment_mode', TRUE),
                'payment_date' => date('Y-m-d H:i:s'),
                'payment_note' => $this->input->post('payment_note', TRUE),
                'received_by'  => $this->customlib->getStaffID(),
            );

            $cheque_date = $this->input->post("cheque_date", TRUE);

            if ($this->input->post('payment_mode', TRUE) == "Cheque") {
                $data['cheque_date']     = $this->customlib->dateFormatToYYYYMMDD($cheque_date);
                $data['cheque_no']       = $this->input->post('cheque_no', TRUE);
            }

            // SaaS: the supplier bill can carry TWO documents — a general 'file' attachment and a
            // cheque 'document'. Meter BOTH independently. The general 'file' is saved to attachment
            // here; the cheque 'document' filename is persisted to cheque_attachment after the insert.
            if (isset($_FILES["file"]) && !empty($_FILES['file']['name'])) {
                $file_name = $this->media_storage->fileupload("file", './uploads/payment_document/');
                $data['attachment']      = "uploads/payment_document/" . $file_name;
                $data['attachment_name'] = $_FILES["file"]["name"];

                try {
                    $saas_quota_result = $this->saasvalidation->updateStorageLimit('storage', ['file']);
                    if (is_string($saas_quota_result)) {
                        $saas_quota_decoded = json_decode($saas_quota_result);
                        if (isset($saas_quota_decoded->status) && $saas_quota_decoded->status === false) {
                            log_message('error', 'SaaS storage quota update returned failure (pharmacy addBillSupplier file): ' . ($saas_quota_decoded->message ?? 'unknown'));
                        }
                    }
                } catch (Exception $e) {
                    log_message('error', 'SaaS storage quota update failed (pharmacy addBillSupplier file): ' . $e->getMessage());
                }
            }

            $saas_cheque_doc      = '';
            $saas_cheque_doc_name = '';
            if (isset($_FILES["document"]) && !empty($_FILES['document']['name'])) {
                $saas_cheque_doc      = $this->media_storage->fileupload("document", './uploads/payment_document/');
                $saas_cheque_doc_name = $_FILES["document"]["name"];

                try {
                    $saas_quota_result = $this->saasvalidation->updateStorageLimit('storage', ['document']);
                    if (is_string($saas_quota_result)) {
                        $saas_quota_decoded = json_decode($saas_quota_result);
                        if (isset($saas_quota_decoded->status) && $saas_quota_decoded->status === false) {
                            log_message('error', 'SaaS storage quota update returned failure (pharmacy addBillSupplier document): ' . ($saas_quota_decoded->message ?? 'unknown'));
                        }
                    }
                } catch (Exception $e) {
                    log_message('error', 'SaaS storage quota update failed (pharmacy addBillSupplier document): ' . $e->getMessage());
                }
            }

            $insert_id = $this->pharmacy_model->addBillSupplier($data);

            // SaaS: persist the cheque document reference in its own column so deleteSupplierBill can
            // release it later. Guarded with field_exists so a not-yet-migrated DB never breaks the save.
            if (!IsNullOrEmptyString($saas_cheque_doc) && !empty($insert_id)
                && $this->db->field_exists('cheque_attachment', 'supplier_bill_basic')) {
                $this->pharmacy_model->addBillSupplier(array(
                    'id'                     => $insert_id,
                    'cheque_attachment'      => "uploads/payment_document/" . $saas_cheque_doc,
                    'cheque_attachment_name' => $saas_cheque_doc_name,
                ));
            }

            if ($insert_id) {
                $medicine_category_id = $this->input->post('medicine_category_id', TRUE);
                $medicine_name        = $this->input->post('medicine_name', TRUE);
                $expiry_date          = $this->input->post('expiry_date', TRUE);
                $batch_no             = $this->input->post('batch_no', TRUE);
                $batch_amount         = $this->input->post('batch_amount', TRUE);
                $mrp                  = $this->input->post('mrp', TRUE);
                $sale_rate            = $this->input->post('sale_rate', TRUE);
                $packing_qty          = $this->input->post('packing_qty', TRUE);
                $quantity             = $this->input->post('quantity', TRUE);
                $purchase_price       = $this->input->post('purchase_price', TRUE);
                $amount               = $this->input->post('amount', TRUE);
                $tax                  = $this->input->post('purchase_tax', TRUE);

                $data1 = array();
                $j     = 0;

                foreach ($medicine_name as $key => $mvalue) {

                    $expdate = $expiry_date[$j];
                    $explore = explode("/", $expdate);

                    $monthary = $explore[0];
                    $yearary  = $explore[1];
                    $month    = $monthary;

                    $month_number       = $this->convertMonthToNumber($month);
                    $last_date_of_month = date("Y-m-t", strtotime($yearary . "-" . $month_number . "-01"));
                    $insert_date        = $last_date_of_month;

                    $details = array(
                        'inward_date'            => $this->customlib->dateFormatToYYYYMMDDHis($bill_date, $this->time_format),
                        'pharmacy_id'            => $medicine_name[$j],
                        'supplier_bill_basic_id' => $insert_id,
                        'expiry'                 => $insert_date,
                        'batch_no'               => $batch_no[$j],
                        'batch_amount'           => $batch_amount[$j],
                        'mrp'                    => $mrp[$j],
                        'sale_rate'              => $sale_rate[$j],
                        'packing_qty'            => $packing_qty[$j],
                        'quantity'               => $quantity[$j],
                        'purchase_price'         => $purchase_price[$j],
                        'available_quantity'     => $quantity[$j],
                        'tax'                    => $tax[$j],
                        'amount'                 => $amount[$j],
                    );
                    $data1[] = $details;

                    $medicine_data         = $this->notificationsetting_model->getmedicineDetails($medicine_name[$j]);
                    $medicine_name_array[] = $medicine_data['medicine_name'] . ' (' . $batch_no[$j] . ')';

                    $j++;

                }
                $this->pharmacy_model->addBillMedicineBatchSupplier($data1);
            }

            if (!empty($medicine_name_array)) {
                $medicine_var = implode(",", $medicine_name_array);
            }

            $supplier_name = $this->patient_model->supplierDetails($supplier_id);
            $event_data = array(
                'supplier_name'    => $supplier_name['supplier'],
                'medicine_details' => $medicine_var,
                'purchase_date'    => $this->customlib->dateFormatToYYYYMMDDHis($bill_date, $this->time_format),
                'invoice_number'   => $this->input->post('invoiceno', TRUE),
                'total'            => $this->input->post('total', TRUE),
                'discount'         => number_format((float) $this->input->post('discount', TRUE), 2, '.', ''),
                'tax'              => $this->input->post('tax', TRUE),
                'net_amount'       => $this->input->post('net_amount', TRUE),
            );

            $this->system_notification->send_system_notification('purchase_medicine', $event_data);
            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'), 'insert_id' => $insert_id);
        }
        echo json_encode($array);
    }

    public function updateSupplierBill()
    {
        if (!$this->rbac->hasPrivilege('medicine_purchase', 'can_edit')) {
            access_denied();
        }

        $this->form_validation->set_rules('date', $this->lang->line('date'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('supplier_id', $this->lang->line('supplier'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('medicine_category_id[]', $this->lang->line('medicine_category'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('medicine_name[]', $this->lang->line('medicine_name'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('expiry_date[]', $this->lang->line('expiry_date'), 'required');
        $this->form_validation->set_rules('batch_no[]', $this->lang->line('batch_no'), 'required');
        $this->form_validation->set_rules('mrp[]', $this->lang->line('mrp'), 'required');
        $this->form_validation->set_rules('sale_rate[]', $this->lang->line('sale_price'), 'required');
        $this->form_validation->set_rules('quantity[]', $this->lang->line('quantity'), 'required|numeric');
        $this->form_validation->set_rules('purchase_price[]', $this->lang->line('purchase_price'), 'required|numeric');
        $this->form_validation->set_rules('total', $this->lang->line('total'), 'required|numeric');
        $this->form_validation->set_rules('file', $this->lang->line('file'), 'callback_validateCanUploadFile[file]');
        if ($this->form_validation->run() == false) {
            $msg = array(
                'date'                 => form_error('date'),
                'supplier_id'          => form_error('supplier_id'),
                'file'                 => form_error('file'),
                'medicine_category_id' => form_error('medicine_category_id[]'),
                'medicine_name'        => form_error('medicine_name[]'),
                'expiry_date'          => form_error('expiry_date[]'),
                'batch_no'             => form_error('batch_no[]'),
                'mrp'                  => form_error('mrp[]'),
                'sale_rate'            => form_error('sale_rate[]'),
                'quantity'             => form_error('quantity[]'),
                'purchase_price'       => form_error('purchase_price[]'),
                'total'                => form_error('total'),
            );
            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $id               = $this->input->post('bill_basic_id', TRUE);
            $bill_id          = $this->input->post("bill_detail_id[]", TRUE);
            $previous_bill_id = $this->input->post("previous_bill_id[]", TRUE);
            $supplier_id      = $this->input->post('supplier_id', TRUE);
            $purchase_no      = $this->input->post('purchase_no', TRUE);
            $data_array       = array();
            $delete_arr       = array();

            $bill_date = $this->input->post("date", TRUE);
            $data      = array(
                'id'          => $id,
                'supplier_id' => $supplier_id,
                'date'        => $this->customlib->dateFormatToYYYYMMDDHis($bill_date, $this->time_format),
                'invoice_no'  => $this->input->post('invoice_no', TRUE),
                'total'       => $this->input->post('total', TRUE),
                'discount'    => $this->input->post('discount', TRUE),
                'tax'         => $this->input->post('tax', TRUE),
                'note'        => $this->input->post('note', TRUE),
                'net_amount'  => $this->input->post('net_amount', TRUE),
            );

            if (isset($_FILES["file"]) && !empty($_FILES['file']['name'])) {
                // SaaS: capture the existing attachment + its size BEFORE the move — a same-extension
                // upload overwrites the old file in place, so the old size must be read first.
                $saas_existing = $this->pharmacy_model->getSupplierDetails($id);
                $saas_old_file = (!empty($saas_existing['file'])) ? $saas_existing['file'] : '';
                $saas_old_dir  = (strpos($saas_old_file, '/') !== false) ? '' : 'uploads/medicine_images';
                $saas_old_kb   = (!empty($saas_old_file)) ? $this->media_storage->getUploadedFileSize($saas_old_file, $saas_old_dir) : 0;
                $saas_new_kb   = $this->media_storage->getTmpFileSize('file');

                $fileInfo = pathinfo($_FILES["file"]["name"]);
                $img_name = $id . '.' . $fileInfo['extension'];
                if (move_uploaded_file($_FILES["file"]["tmp_name"], "./uploads/medicine_images/" . $img_name)) {
                    $new_file_path = 'uploads/medicine_images/' . $img_name;
                    $data_img      = array('id' => $id, 'file' => $new_file_path);
                    $this->pharmacy_model->addBillSupplier($data_img);

                    // SaaS: adjust storage quota by the size DIFFERENCE (new vs replaced) instead of a
                    // blind add, so editing the attachment keeps usage accurate.
                    try {
                        if ($saas_old_kb > $saas_new_kb) {
                            $this->saasvalidation->deleteResouceQuota('storage', $saas_old_kb - $saas_new_kb);
                        } elseif ($saas_new_kb > $saas_old_kb) {
                            $this->saasvalidation->updateResouceQuota('storage', $saas_new_kb - $saas_old_kb);
                        }
                    } catch (Exception $e) {
                        log_message('error', 'SaaS storage quota update failed (pharmacy updateSupplierBill): ' . $e->getMessage());
                    }

                    // SaaS: a different extension leaves the old file orphaned alongside the new one — remove it.
                    if (!empty($saas_old_file) && $saas_old_file !== $new_file_path) {
                        $this->media_storage->filedelete($saas_old_file, $saas_old_dir);
                    }
                }
            }
            $this->pharmacy_model->addBillSupplier($data);

            if (!empty($id)) {

                $bill_detail_id       = $this->input->post('bill_detail_id', TRUE);
                $medicine_batch_id    = $this->input->post('medicine_batch_id', TRUE);
                $medicine_category_id = $this->input->post('medicine_category_id', TRUE);
                $medicine_name        = $this->input->post('medicine_name', TRUE);
                $expiry_date          = $this->input->post('expiry_date', TRUE);
                $batch_no             = $this->input->post('batch_no', TRUE);
                $batch_amount         = $this->input->post('batch_amount', TRUE);
                $mrp                  = $this->input->post('mrp', TRUE);
                $sale_rate            = $this->input->post('sale_rate', TRUE);
                $packing_qty          = $this->input->post('packing_qty', TRUE);
                $quantity             = $this->input->post('quantity', TRUE);
                $total_quantity       = $this->input->post('available_quantity', TRUE);
                $amount               = $this->input->post('amount', TRUE);
                $purchase_price       = $this->input->post('purchase_price', TRUE);
                $data_array1          = array();
                $bill_date1           = $this->input->post("date", TRUE);
                $tax                  = $this->input->post("purchase_tax", TRUE);
                $j                    = 0;
                foreach ($medicine_category_id as $key => $value) {
                    $expdate = $expiry_date[$j];
                    $explore = explode("/", $expdate);
                    $monthary = $explore[0];
                    $yearary  = $explore[1];
                    $month    = $monthary;

                    $month_number = $this->convertMonthToNumber($month);
                    $insert_date  = $yearary . "-" . $month_number . "-01";

                    if ($bill_id[$j] == 0) {
                        $add_data = array(
                            'supplier_bill_basic_id' => $id,
                            'pharmacy_id'            => $medicine_name[$j],
                            'inward_date'            => $this->customlib->dateFormatToYYYYMMDDHis($bill_date1, $this->time_format),
                            'expiry'                 => $insert_date,
                            'batch_no'               => $batch_no[$j],
                            'batch_amount'           => $batch_amount[$j],
                            'mrp'                    => $mrp[$j],
                            'sale_rate'              => $sale_rate[$j],
                            'packing_qty'            => $packing_qty[$j],
                            'quantity'               => $quantity[$j],
                            'available_quantity'     => $quantity[$j],
                            'purchase_price'         => $purchase_price[$j],
                            'amount'                 => $amount[$j],
                            'tax'                    => $tax[$j],
                        );
                        $data_array[] = $add_data;
                    } else {

                        $detail = array(
                            'id'                     => $bill_detail_id[$j],
                            'supplier_bill_basic_id' => $id,
                            'pharmacy_id'            => $medicine_name[$j],
                            'inward_date'            => $this->customlib->dateFormatToYYYYMMDDHis($bill_date1, $this->time_format),
                            'expiry'                 => $insert_date,
                            'batch_no'               => $batch_no[$j],
                            'batch_amount'           => $batch_amount[$j],
                            'mrp'                    => $mrp[$j],
                            'sale_rate'              => $sale_rate[$j],
                            'packing_qty'            => $packing_qty[$j],
                            'quantity'               => $quantity[$j],
                            'available_quantity'     => $quantity[$j],
                            'purchase_price'         => $purchase_price[$j],
                            'amount'                 => $amount[$j],
                            'tax'                    => $tax[$j],
                        );

                        $this->pharmacy_model->updateMedicineBatchDetail($detail);
                    }

                    $j++;
                }
            } else {

            }
            if (!empty($data_array)) {
                $this->pharmacy_model->addBillMedicineBatchSupplier($data_array);
            }

            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
        }
        echo json_encode($array);
    }

    public function deletePharmacyBill()
    {
        $id = $this->input->post('id', TRUE);
        if (!$this->rbac->hasPrivilege('pharmacy_bill', 'can_delete')) {
            access_denied();
        }
        if (!empty($id)) {
            // SaaS: release every linked transaction's attachment from storage quota before deletion.
            $saas_transactions = $this->transaction_model->pharmacyPayments($id);
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
                                log_message('error', 'SaaS storage quota release failed (pharmacy deletePharmacyBill transaction_id=' . $saas_transaction->id . '): ' . $e->getMessage());
                            }
                        }
                        $this->media_storage->filedelete($doc_path, $dir);
                    }
                }
            }

            // SaaS: release the bill's own prescription image (pres_images, field `file`) — it is
            // set/diffed in updateBill, so it must be released here too (the loop above only releases
            // transaction/payment attachments, not the bill image).
            $saas_bill = $this->pharmacy_model->getdate($id);
            if (!empty($saas_bill['file'])) {
                $bill_doc = $saas_bill['file'];
                $bill_dir = (strpos($bill_doc, '/') !== false) ? '' : 'uploads/pres_images';
                $bill_kb  = $this->media_storage->getUploadedFileSize($bill_doc, $bill_dir);
                if ($bill_kb > 0) {
                    try {
                        $this->saasvalidation->deleteResouceQuota('storage', $bill_kb);
                    } catch (Exception $e) {
                        log_message('error', 'SaaS storage quota release failed (pharmacy deletePharmacyBill file id=' . $id . '): ' . $e->getMessage());
                    }
                }
                $this->media_storage->filedelete($bill_doc, $bill_dir);
            }

            $this->pharmacy_model->deletePharmacyBill($id);
            $array = array('status' => 1, 'error' => '', 'message' => $this->lang->line('delete_message'));
        } else {
            $array = array('status' => 0, 'error' => '', 'message' => $this->lang->line('something_went_wrong'));
        }
        echo json_encode($array);
    }

    public function deleteSupplierBill($id)
    {
        if (!$this->rbac->hasPrivilege('medicine_purchase', 'can_delete')) {
            access_denied();
        }
        if (!empty($id)) {
            // SaaS: release the supplier bill's attachment(s) from storage quota before deletion —
            // both the general 'attachment' and the cheque 'cheque_attachment' (if present).
            $saas_supplier = $this->pharmacy_model->getSupplierDetails($id);
            if (!empty($saas_supplier)) {
                $saas_docs = array();
                if (!empty($saas_supplier['attachment'])) {
                    $saas_docs[] = $saas_supplier['attachment'];
                }
                if (!empty($saas_supplier['cheque_attachment'])) {
                    $saas_docs[] = $saas_supplier['cheque_attachment'];
                }
                // SaaS: the 'file' column is written by the EDIT path (updateSupplierBill) — release it
                // too, otherwise attachments uploaded via edit would leak on delete.
                if (!empty($saas_supplier['file'])) {
                    $saas_docs[] = $saas_supplier['file'];
                }
                foreach ($saas_docs as $doc_path) {
                    $dir = $this->media_storage->resolveAttachmentDir($doc_path);
                    $kb  = $this->media_storage->getUploadedFileSize($doc_path, $dir);
                    if ($kb > 0) {
                        try {
                            $this->saasvalidation->deleteResouceQuota('storage', $kb);
                        } catch (Exception $e) {
                            log_message('error', 'SaaS storage quota release failed (pharmacy deleteSupplierBill): ' . $e->getMessage());
                        }
                    }
                    $this->media_storage->filedelete($doc_path, $dir);
                }
            }

            $this->pharmacy_model->deleteSupplierBill($id);
            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('delete_message'));
        } else {
            $array = array('status' => 'fail', 'error' => '', 'message' => '');
        }
        echo json_encode($array);
    }

    // ================================================================
    // PURCHASE RETURN METHODS
    // ================================================================

    public function purchaseReturn($id)
    {
        if (!$this->rbac->hasPrivilege('medicine_purchase', 'can_add')) {
            access_denied();
        }
        $purchase                  = $this->pharmacy_model->getSupplierDetails($id);
        $batches                   = $this->pharmacy_model->getMedicineBatchesForPurchase($id);
        $data['purchase']          = $purchase;
        $data['batches']           = $batches;
        $data['returned_amount']   = $this->pharmacy_model->getPurchaseReturnedAmount($id);
        $this->load->view('admin/pharmacy/purchase_return_form', $data);
    }

    public function savePurchaseReturn()
    {
        if (!$this->rbac->hasPrivilege('medicine_purchase', 'can_add')) {
            access_denied();
        }
        $this->form_validation->set_rules('supplier_bill_basic_id', $this->lang->line('purchase_no'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('return_date',            $this->lang->line('date'),        'trim|required|xss_clean');
        $this->form_validation->set_rules('reason',                 $this->lang->line('reason'),      'trim|required|xss_clean');
        if ($this->form_validation->run() == false) {
            $array = array('status' => 'fail', 'error' => $this->form_validation->error_array(), 'message' => '');
            echo json_encode($array);
            return;
        }
        $supplier_bill_basic_id = $this->input->post('supplier_bill_basic_id', TRUE);
        $batch_ids              = $this->input->post('batch_id',       TRUE);
        $pharmacy_ids           = $this->input->post('pharmacy_id',    TRUE);
        $batch_nos              = $this->input->post('batch_no',       TRUE);
        $return_qtys            = $this->input->post('return_qty',     TRUE);
        $purchase_prices        = $this->input->post('purchase_price', TRUE);
        $available_qtys         = $this->input->post('available_qty',  TRUE);

        // Validate at least one medicine has return qty > 0
        $has_return = false;
        foreach ($return_qtys as $qty) {
            if ((int)$qty > 0) {
                $has_return = true;
                break;
            }
        }
        if (!$has_return) {
            $array = array('status' => 'fail', 'error' => array(), 'message' => $this->lang->line('please_enter_return_quantity'));
            echo json_encode($array);
            return;
        }

        // Validate each return_qty <= available_qty
        $detail_data  = array();
        $total_amount = 0;
        foreach ($return_qtys as $j => $qty) {
            $qty = (int)$qty;
            if ($qty <= 0) {
                continue;
            }
            $avail = (int)$available_qtys[$j];
            if ($qty > $avail) {
                $array = array('status' => 'fail', 'error' => array(), 'message' => $this->lang->line('return_qty_exceeds_available'));
                echo json_encode($array);
                return;
            }
            $price  = (float)$purchase_prices[$j];
            $amount = $qty * $price;
            $total_amount += $amount;
            $detail_data[] = array(
                'purchase_return_basic_id'  => 0, // will update after header insert
                'medicine_batch_details_id' => (int)$batch_ids[$j],
                'pharmacy_id'               => (int)$pharmacy_ids[$j],
                'batch_no'                  => $batch_nos[$j],
                'quantity'                  => $qty,
                'purchase_price'            => $price,
                'amount'                    => $amount,
            );
        }

        $return_date = $this->input->post('return_date', TRUE);
        $header = array(
            'supplier_bill_basic_id' => $supplier_bill_basic_id,
            'supplier_id'            => $this->input->post('supplier_id', TRUE),
            'return_date'            => $this->customlib->dateFormatToYYYYMMDDHis($return_date, $this->time_format),
            'total_amount'           => $total_amount,
            'reason'                 => $this->input->post('reason', TRUE),
            'note'                   => $this->input->post('note', TRUE),
            'returned_by'            => $this->customlib->getStaffID(),
        );

        $fail_response = array('status' => 'fail', 'error' => array(), 'message' => $this->lang->line('something_went_wrong'));

        $return_id = $this->pharmacy_model->addPurchaseReturn($header);
        if (!$return_id) {
            echo json_encode($fail_response);
            return;
        }

        // Set return id in detail rows
        foreach ($detail_data as &$row) {
            $row['purchase_return_basic_id'] = $return_id;
        }
        unset($row);

        // Insert detail rows first — only decrement inventory if insert succeeds
        $detail_saved = $this->pharmacy_model->addPurchaseReturnDetail($detail_data);
        if (!$detail_saved) {
            echo json_encode($fail_response);
            return;
        }

        $this->pharmacy_model->updateBatchAvailableQtyBulk($detail_data);

        $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
        echo json_encode($array);
    }

    public function getPurchaseReturnHistory($id)
    {
        if (!$this->rbac->hasPrivilege('medicine_purchase', 'can_view')) {
            access_denied();
        }
        $history          = $this->pharmacy_model->getPurchaseReturnHistory($id);
        $data['history']  = $history;
        $data['purchase_id'] = $id;
        foreach ($history as $key => $row) {
            $history[$key]['items'] = $this->pharmacy_model->getPurchaseReturnDetail($row['id']);
        }
        $data['history'] = $history;
        $this->load->view('admin/pharmacy/purchase_return_history', $data);
    }

    public function delete_medicine_batch($id)
    {       
        if (!empty($id)) {
            $this->pharmacy_model->delete_medicine_batch($id);
            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('delete_message'));
        } else {
            $array = array('status' => 'fail', 'error' => '', 'message' => '');
        }
        echo json_encode($array);
    }

    public function getBillNo()
    {
        $result = $this->pharmacy_model->getBillNo();

        $id = $result["id"];
        if (!empty($result["id"])) {
            $bill_no = $id + 1;
        } else {
            $bill_no = 1;
        }
        echo json_encode($bill_no);
    }

    // =====================================================================
    // SALE RETURN METHODS
    // =====================================================================

    public function getSaleBillForReturn()
    {
        if (!$this->rbac->hasPrivilege('pharmacy_bill', 'can_add')) {
            access_denied();
        }
        $bill_no = $this->input->post('bill_no', TRUE);
        if (empty($bill_no)) {
            echo json_encode(array('status' => 0, 'msg' => 'Bill number required'));
            return;
        }
        $bill = $this->pharmacy_model->getBillDetailsPharma($bill_no);
        if (empty($bill)) {
            echo json_encode(array('status' => 0, 'msg' => $this->lang->line('no_record_found')));
            return;
        }
        $items            = $this->pharmacy_model->getAllBillDetails($bill_no);
        $previous_returns = $this->pharmacy_model->getPreviousReturnsForBill($bill['id']);
        foreach ($previous_returns as &$ret) {
            $ret['items'] = $this->pharmacy_model->getSaleReturnItems($ret['id']);
            $ret['date']  = $this->customlib->YYYYMMDDTodateFormat($ret['date']);
        }
        unset($ret);
        echo json_encode(array('status' => 1, 'bill' => $bill, 'items' => $items, 'previous_returns' => $previous_returns));
    }

    public function salereturnbills()
    {
        if (!$this->rbac->hasPrivilege('pharmacy_bill', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'pharmacy');
        $data['title']           = 'Sale Return Bills';
        $data['saleReturnBills'] = $this->pharmacy_model->getSaleReturnBillsList();
        $data['prefix_bill']     = $this->customlib->getSessionPrefixByType('pharmacy_billing');
        $prefix_array            = $this->session->userdata('hospitaladmin')['prefix'];
        $data['prefix_return']   = isset($prefix_array['pharmacy_return']) ? $prefix_array['pharmacy_return'] : 'RET';
        $data['module'] = 'pharmacy';
        $this->load->view('layout/header', $data);
        $this->load->view('admin/pharmacy/saleReturnBills', $data);
        $this->load->view('layout/footer', $data);
    }

    public function getSaleReturnDetails($return_id)
    {
        if (!$this->rbac->hasPrivilege('pharmacy_bill', 'can_view')) {
            access_denied();
        }
        $data['items']     = $this->pharmacy_model->getSaleReturnItems($return_id);
        $data['return_id'] = $return_id;
        $this->load->view('admin/pharmacy/sale_return_details', $data);
    }

    public function getSaleReturnHistoryForBill($bill_id)
    {
        if (!$this->rbac->hasPrivilege('pharmacy_bill', 'can_view')) {
            access_denied();
        }
        $history = $this->pharmacy_model->getSaleReturnHistoryForBill($bill_id);
        foreach ($history as $key => $row) {
            $history[$key]['items'] = $this->pharmacy_model->getSaleReturnItems($row['id']);
        }
        $data['history']  = $history;
        $data['bill_id']  = $bill_id;
        $this->load->view('admin/pharmacy/sale_return_history', $data);
    }

    public function createSaleReturn()
    {
        if (!$this->rbac->hasPrivilege('pharmacy_bill', 'can_add')) {
            access_denied();
        }
        $medicineCategory         = $this->medicine_category_model->getMedicineCategory();
        $data['medicineCategory'] = $medicineCategory;
        $doctors                  = $this->staff_model->getStaffbyrole(3);
        $data['doctors']          = $doctors;
        $data['payment_mode']     = $this->payment_mode;
        $data['disable_option']   = false;

        $result    = $this->pharmacy_model->getSaleReturnNo();
        $return_no = !empty($result['id']) ? $result['id'] + 1 : 1;

        $page = $this->load->view('admin/pharmacy/_createSaleReturn', $data, true);
        echo json_encode(array('status' => 1, 'page' => $page, 'return_no' => $return_no));
    }

    public function addSaleReturn()
    {
        if (!$this->rbac->hasPrivilege('pharmacy_bill', 'can_add')) {
            access_denied();
        }

        $this->form_validation->set_rules('sr_return_no', $this->lang->line('bill_no'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('sr_net_amount', $this->lang->line('net_amount'), 'trim|required|xss_clean');

        if ($this->input->post('sr_payment_mode', TRUE) == 'Cheque') {
            $this->form_validation->set_rules('sr_cheque_no', $this->lang->line('cheque_no'), 'trim|required|xss_clean');
            $this->form_validation->set_rules('sr_cheque_date', $this->lang->line('cheque_date'), 'trim|required|xss_clean');
            $this->form_validation->set_rules('sr_document', $this->lang->line('document'), 'callback_validateCanUploadFile[sr_document]');
        }

        $total_rows = $this->input->post('sr_total_rows', TRUE);
        if (!isset($total_rows) || empty($total_rows)) {
            $this->form_validation->set_rules('no_records', $this->lang->line('no_records'), 'trim|required|xss_clean',
                array('required' => 'Return Qty required'));
        }

        if (isset($total_rows) && !empty($total_rows)) {
            foreach ($total_rows as $row_value) {
                $return_qty = $this->input->post('sr_quantity_' . $row_value, TRUE);
                $sale_qty   = $this->input->post('sr_sale_qty_' . $row_value, TRUE);

                // Must be numeric (integers only)
                if ($return_qty !== '' && (!ctype_digit((string)$return_qty) || (int)$return_qty < 0)) {
                    $this->form_validation->set_rules('invalid_return_qty', $this->lang->line('return_qty'), 'required',
                        array('required' => 'Return Qty must be a valid positive number'));
                    break;
                }

                // Must not exceed sale qty
                if ($sale_qty !== '' && $return_qty !== '' && (int)$return_qty > (int)$sale_qty) {
                    $this->form_validation->set_rules('over_return_qty', $this->lang->line('return_qty'), 'required',
                        array('required' => 'Return Qty cannot be greater than Sale Qty'));
                    break;
                }
            }
        }

        if ($this->form_validation->run() == false) {
            $error = $this->form_validation->error_array();
            echo json_encode(array('status' => 'fail', 'error' => $error, 'message' => ''));
            return;
        }

        $return_date = $this->customlib->dateFormatToYYYYMMDDHis(
            $this->input->post('sr_date', TRUE), $this->time_format
        );
        $patient_id  = $this->input->post('sr_patient_id', TRUE);

        $basic_data = array(
            'return_no'              => $this->input->post('sr_return_no', TRUE),
            'pharmacy_bill_basic_id' => $this->input->post('sr_pharmacy_bill_basic_id', TRUE) ?: null,
            'date'                   => $return_date,
            'patient_id'             => !empty($patient_id) ? $patient_id : null,
            'customer_name'          => $this->input->post('sr_customer_name', TRUE),
            'customer_type'          => $this->input->post('sr_customer_type', TRUE),
            'doctor_name'            => $this->input->post('sr_doctor_name', TRUE),
            'total'                  => $this->input->post('sr_total', TRUE),
            'discount_percentage'    => $this->input->post('sr_discount_percent', TRUE) ?: 0,
            'discount'               => $this->input->post('sr_discount', TRUE) ?: 0,
            'tax_percentage'         => 0,
            'tax'                    => $this->input->post('sr_tax', TRUE) ?: 0,
            'net_amount'             => $this->input->post('sr_net_amount', TRUE),
            'note'                   => $this->input->post('sr_note', TRUE),
            'returned_by'            => $this->customlib->getLoggedInUserID(),
        );

        $detail_array = array();
        if (isset($total_rows) && !empty($total_rows)) {
            foreach ($total_rows as $row_value) {
                $quantity = $this->input->post('sr_quantity_' . $row_value, TRUE);
                $batch_id = $this->input->post('sr_batch_no_id_' . $row_value, TRUE);
                $price    = $this->input->post('sr_sale_price_' . $row_value, TRUE);
                $discount = $this->input->post('sr_mdiscount_' . $row_value, TRUE) ?: 0;
                $amount   = $this->input->post('sr_amount_' . $row_value, TRUE);

                if (!empty($batch_id) && !empty($quantity)) {
                    $detail_array[] = array(
                        'medicine_batch_detail_id' => $batch_id,
                        'quantity'                 => $quantity,
                        'sale_price'               => $price,
                        'discount'                 => $discount,
                        'amount'                   => $amount,
                    );
                }
            }
        }

        if (empty($detail_array)) {
            echo json_encode(array('status' => 'fail', 'error' => array(), 'message' => $this->lang->line('return_qty_required')));
            return;
        }

        // Build refund payment entry (mirrors addBill update-path payment_array)
        $payment_section  = $this->config->item('payment_section');
        $payment_amount   = $this->input->post('sr_payment_amount', TRUE);
        if ($payment_amount !== '' && $payment_amount >= 0) {
            $payment_array = array(
                'amount'                 => $payment_amount,
                'type'                   => 'refund',
                'patient_id'             => !empty($patient_id) ? $patient_id : null,
                'section'                => $payment_section['pharmacy'],
                'pharmacy_bill_basic_id' => $this->input->post('sr_pharmacy_bill_basic_id', TRUE) ?: null,
                'payment_mode'           => $this->input->post('sr_payment_mode', TRUE),
                'note'                   => $this->input->post('sr_note', TRUE),
                'payment_date'           => date('Y-m-d H:i:s'),
                'received_by'            => $this->customlib->getLoggedInUserID(),
            );
            if ($this->input->post('sr_payment_mode', TRUE) == 'Cheque') {
                $payment_array['cheque_date'] = $this->customlib->dateFormatToYYYYMMDD($this->input->post('sr_cheque_date', TRUE));
                $payment_array['cheque_no']   = $this->input->post('sr_cheque_no', TRUE);
                if (isset($_FILES['sr_document']) && !empty($_FILES['sr_document']['name'])) {
                    $file_name = $this->media_storage->fileupload('sr_document', './uploads/payment_document/');
                    $payment_array['attachment']      = $file_name;
                    $payment_array['attachment_name'] = $_FILES['sr_document']['name'];

                    // SaaS: add the uploaded sale-return document size to the storage quota usage.
                    try {
                        $this->saasvalidation->updateStorageLimit('storage', ['sr_document']);
                    } catch (Exception $e) {
                        log_message('error', 'SaaS storage quota update failed (pharmacy addSaleReturn): ' . $e->getMessage());
                    }
                }
            }
        } else {
            $payment_array = array();
        }

        $result = $this->pharmacy_model->addSaleReturn($basic_data, $detail_array, $payment_array);
        if ($result) {
            echo json_encode(array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message')));
        } else {
            echo json_encode(array('status' => 'fail', 'error' => '', 'message' => $this->lang->line('error_message')));
        }
    }

    public function getExpiryDate()
    {
        $medicine_batch_detail_id   =   $this->input->get_post('medicine_batch_detail_id');
        $id                         =   $this->input->post('id');
        $is_tpa                     =   $this->input->post('is_tpa');
        $patient_id                 =   $this->input->post('patient_id');
        $is_tpa_valid               =   true;
        $organisation_id            =   "";

        if($is_tpa == 1 && $patient_id != ""){   
            $patient_result  = $this->patient_model->getpatientDetails($patient_id);
            $organisation_id=$this->organisation_model->get($patient_result['organisation_id']);
            if($patient_result && !IsNullOrEmptyString($patient_result['insurance_validity'])  &&  isset($organisation_id) ){
                if(strtotime($patient_result['insurance_validity']) >= strtotime(date('Y-m-d'))){
                    $result  = $this->pharmacy_model->getMedicineBatchByID($medicine_batch_detail_id,$patient_result['organisation_id']);
                    $result->expiry_date = $this->customlib->getMedicine_expire_month($result->expiry);
                    $sale_rate=$result->sale_rate= $result->org_charge;
                }else{
                    $result = $this->pharmacy_model->getMedicineBatchByID($medicine_batch_detail_id);
                    $result->expiry_date = $this->customlib->getMedicine_expire_month($result->expiry);
                    $sale_rate=$result->sale_rate;
                    $is_tpa_valid=false;  
                }              
            }else{
                $result  = $this->pharmacy_model->getMedicineBatchByID($medicine_batch_detail_id);
                $result->expiry_date = $this->customlib->getMedicine_expire_month($result->expiry);
                $sale_rate=$result->sale_rate;
            }

        }else{
            $result  = $this->pharmacy_model->getMedicineBatchByID($medicine_batch_detail_id);
            $result->expiry_date = $this->customlib->getMedicine_expire_month($result->expiry);
            $sale_rate=$result->sale_rate;
        }  
        if($is_tpa > 0 &&  isset($organisation_id) && ($sale_rate == 0 || $sale_rate == "")){
            echo json_encode(array('status' => 0, 'msg' => $this->lang->line('no_charge_has_configured_for_selected_category')));
        }elseif($result && !$is_tpa_valid){
            echo json_encode(array('status' => 2, 'result' => $result,'medicine_batch_detail_id'=>$medicine_batch_detail_id,'msg'=> $this->lang->line('your_tpa_validity_has_expired_on').' '.$this->customlib->YYYYMMDDTodateFormat($patient_result['insurance_validity']).', '. $this->lang->line('so_standard_charges_has_been_applied')));
        }else{
            echo json_encode(array('status' => 1, 'result' => $result,'medicine_batch_detail_id'=>$medicine_batch_detail_id));
        }  
    }

    public function getExpireDate()
    {        
        $batch_no         = $this->input->get_post('batch_no');
        $result           = $this->pharmacy_model->getExpireDate($batch_no);
        $result['expiry'] = $this->customlib->getMedicine_expire_month($result['expiry']);
        echo json_encode($result);
    }

    public function getBatchNoList()
    {
        $pharmacy_id = $this->input->get_post('pharmacy_id');
        $batch_id = $this->input->get_post('batch_id');
        $result      = $this->pharmacy_model->getBatchNoList($pharmacy_id,$batch_id);
        echo json_encode($result);
    }

    public function getmedicinedetails()
    {
        $pharmacy_id = $this->input->get_post('pharmacy_id');
        $result      = $this->pharmacy_model->getmedicinedetailsbyid($pharmacy_id);
        echo json_encode($result);
    }

    public function addBadStock()
    {
        if (!$this->rbac->hasPrivilege('medicine_bad_stock', 'can_view')) {
            access_denied();
        }
        $this->form_validation->set_rules('pharmacy_id', $this->lang->line('pharmacy_id'), 'required');
        $this->form_validation->set_rules('expiry_date', $this->lang->line('expiry_date'), 'required');
        $this->form_validation->set_rules('batch_no', $this->lang->line('batch_no'), 'required');
        $this->form_validation->set_rules('packing_qty', $this->lang->line('qty'), 'required|numeric');
        $this->form_validation->set_rules('inward_date', $this->lang->line('outward_date'), 'required');

        if ($this->form_validation->run() == false) {
            $msg = array(
                'pharmacy_id' => form_error('pharmacy_id'),
                'expiry_date' => form_error('expiry_date'),
                'batch_no'    => form_error('batch_no'),
                'packing_qty' => form_error('packing_qty'),
                'inward_date' => form_error('inward_date'),
            );
            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $id          = $this->input->post('pharmacy_id', TRUE);
            $inward_date = $this->input->post('inward_date', TRUE);
            $expiry_date = $this->input->post('expiry_date', TRUE);

            $explore = explode("/", $expiry_date);

            $monthary = $explore[0];
            $yearary  = $explore[1];
            $month    = $monthary;

            $month_number       = $this->convertMonthToNumber($month);
            $last_date_of_month = date("Y-m-t", strtotime($yearary . "-" . $month_number . "-01"));
            $insert_date        = $last_date_of_month;
            $medicine_batch     = array(
                'pharmacy_id'               => $id,
                'medicine_batch_details_id' => $this->input->post('medicine_batch_id', TRUE),
                'expiry_date'               => $insert_date,
                'outward_date'              => $this->customlib->dateFormatToYYYYMMDD($inward_date),
                'batch_no'                  => $this->input->post('batch_no', TRUE),
                'quantity'                  => $this->input->post('packing_qty', TRUE),
                'note'                      => $this->input->post('note', TRUE),
            );

            $batch_qty   = $this->input->post('available_quantity', TRUE);
            $packing_qty = $this->input->post('packing_qty', TRUE);

            if (!empty($batch_qty)) {
                $available_quantity = $batch_qty - $packing_qty;
            } else {
                $available_quantity = 0;
            }

            $update_data = array('id' => $this->input->post('medicine_batch_id', TRUE), 'available_quantity' => $available_quantity);

            $this->pharmacy_model->addBadStock($medicine_batch);
            $this->pharmacy_model->updateMedicineBatch($update_data);

            $event_data = array(
                'batch_no'     => $this->input->post('batch_no', TRUE),
                'expiry_date'  => $this->customlib->YYYYMMDDTodateFormat($insert_date),
                'outward_date' => $this->customlib->YYYYMMDDTodateFormat($inward_date),
                'qty'          => $this->input->post('packing_qty', TRUE),
            );

            $this->system_notification->send_system_notification('add_bad_stock', $event_data);
            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
        }
        echo json_encode($array);
    }

    public function deleteBadStock($id, $medicine_batch_details_id)
    {
        if (!$this->rbac->hasPrivilege('medicine', 'can_view')) {
            access_denied();
        }
        if (!empty($id)) {
            $medicine_available_quantity               = $this->pharmacy_model->getsingleMedicineBatchdetails($medicine_batch_details_id);
            $bad_stock_quantity                        = $this->pharmacy_model->getsingleMedicineBadStock($id);
            $medicine_batch_data['id']                 = $medicine_batch_details_id;
            $medicine_batch_data['available_quantity'] = $medicine_available_quantity['available_quantity'] + $bad_stock_quantity['quantity'];
            $this->pharmacy_model->availableQty($medicine_batch_data);

            $this->pharmacy_model->deleteBadStock($id);
            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('delete_message'));
        } else {
            $array = array('status' => 'fail', 'error' => '', 'message' => '');
        }
        echo json_encode($array);
    }

    public function printTransaction()
    {
        $print_details         = $this->printing_model->get('', 'paymentreceipt');
        $id                    = $this->input->post('id');
        $charge                = array();
        $transaction           = $this->transaction_model->pharmacyPaymentByTransactionId($id);
        $data['print_details'] = $print_details;
        $data['transaction']   = $transaction;
        $page = $this->load->view('admin/pharmacy/_printTransaction', $data, true);
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
                    $this->form_validation->set_message('handle_doc_upload', $this->lang->line('file_size_shoud_be_less_than') . " 2MB");
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

    public function downloadcheque($bill_id)
    {
        $result = $this->pharmacy_model->getSupplierDetails($bill_id);
        $this->load->helper('download');
        $filepath  = $result["attachment"];
        $file_name = $result["attachment_name"];
        $data      = file_get_contents($filepath);
        force_download($file_name, $data);
    }
	
    public function download_attachment($bill_id)
    {
        $result = $this->pharmacy_model->getSupplierDetails($bill_id);
        $this->media_storage->filedownload($result["attachment"],'./');
    }

    public function update_sale_rate(){
        $this->form_validation->set_rules('salerate[]', $this->lang->line('sale_price'), 'trim|required|xss_clean');       
        if ($this->form_validation->run() == false) {
            $msg   = array('sale_price' => form_error('salerate[]'));
            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $id         =   $this->input->post('id[]', TRUE);
            $salerate   =   $this->input->post('salerate[]', TRUE);
            $data1      =   array();
            $j          =   0;

            foreach ($id as $key => $value) {
                $details = array(
                'id'            => $id[$j],
                'sale_rate'     => $salerate[$j],
            );

            $data1[] = $details;
            $j++;
        }
        $this->pharmacy_model->update_sale_rate($data1);
        $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
        }     
        echo json_encode($array);
    }

    //****pharmacy TPA feature work start****
    public function view_tpa_charge_model(){
        $id=$this->input->post('id', TRUE);
        $result         = $this->pharmacy_model->getSupplierDetails($id);
        $data['result'] = $result;
        $detail         = $this->pharmacy_model->getAllSupplierDetails($id);
        $data['detail'] = $detail;
        $data['schedule']    = $this->organisation_model->get();
        $this->load->view('admin/pharmacy/_medicine_tpa_charges', $data);
    }

     public function update_tpa_rate(){

        $this->form_validation->set_rules('id[]', ('tpa_price--r'), 'trim|required|xss_clean');       
        if ($this->form_validation->run() == false) {
            $msg   = array('id' => form_error('id[]'));
            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {

            $id       =   $this->input->post('id[]', TRUE);
            $data1    =   array();

            foreach($id as $value) {
                $schedule_charge_id   =   $this->input->post("schedule_charge_id_".$value."[]", TRUE);
                foreach($schedule_charge_id as $svalue) {
                    $org_charge                         =  $this->input->post("schedule_charge_".$svalue."_".$value, TRUE);
                    $orgnization_medicine_charge_id     =  $this->input->post("orgnization_medicine_charge_id_".$svalue."_".$value, TRUE);

                    $details = array(
                        'id'                        => $orgnization_medicine_charge_id,
                        'org_charge'                => $org_charge,
                        'org_id'                    => $svalue,
                        'medicine_batch_details_id' => $value,
                    );
                    $this->pharmacy_model->addtpacharge($details);
            }
        }

        $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
        }     
        echo json_encode($array);

    }
    //***pharmacy TPA feature work end***













 

   

     

}