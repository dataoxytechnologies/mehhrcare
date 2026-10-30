<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Income extends Admin_Controller
{
	public $modules;
	public $search_type;


    public function __construct()
    {
        parent::__construct();
        $this->load->helper('form');
        $this->load->library("datatables");
        $this->load->library("SaasValidation");
        $this->load->model("transaction_model");
        $this->modules = $this->config->item('modules');
        $this->config->load("payroll");
        $this->config->load("image_valid");
        $this->search_type = $this->config->item('search_type');
        $this->load->helper('customfield_helper');
        $this->config->item('search_type');
    }

    public function index()
    {
        if (!$this->module_lib->hasActive('income')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'finance');
        $this->session->set_userdata('sub_menu', 'income/index');
        $data['title']       = $this->lang->line('add_income');
        $data['title_list']  = $this->lang->line('recent_income');
        $data['fields']      = $this->customfield_model->get_custom_fields('income', 1);
        $incomeHead          = $this->incomehead_model->get();
        $data['incheadlist'] = $incomeHead;
        $data['module'] = 'finance';
        $this->load->view('layout/header', $data);
        $this->load->view('admin/income/incomeList', $data);
        $this->load->view('layout/footer', $data);
    }

    public function getDatatable()
    {
        if (!$this->rbac->hasPrivilege('income', 'can_view')) {
            access_denied();
        }
        $dt_response = $this->income_model->getAllRecord();
        $fields      = $this->customfield_model->get_custom_fields('income', 1);
        $dt_response = json_decode($dt_response);

        $dt_data = array();
        if (!empty($dt_response->data)) {
            foreach ($dt_response->data as $key => $value) {

                $row = array();
                //====================================
                $column_first  = '<a href="#" data-bs-toggle="popover" class="detail_popover">' . html_escape($value->name) . '</a>';
                $column_first .= '<div class="fee_detail_popover" style="display: none">';
                if ($value->note == "") {
                    $column_first .= '<p class="text text-danger">' . $this->lang->line('no_description') . '</p>';
                } else {
                    $column_first .= '<p class="text text-info">' . html_escape($value->note) . '</p>';
                }
                $column_first .= '</div>';

                $action = '';
                if ($value->documents) {
                    $action .= '<a href="' . base_url() . 'admin/income/downloadincome/' . $value->id . '" class="btn btn-sm btn-outline-secondary" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $this->lang->line('download') . '"><i class="fa fa-download"></i></a>';
                }

                if ($this->rbac->hasPrivilege('income', 'can_edit')) {
                    $action .= '<a href="#" onclick="edit(' . $value->id . ')" class="btn btn-sm btn-outline-primary" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $this->lang->line('edit') . '"><i class="fa fa-pencil"></i></a>';
                }

                if ($this->rbac->hasPrivilege('income', 'can_delete')) {
                    $action .= '<a href="#" onclick="delete_record(' . $value->id . ')" class="btn btn-sm btn-outline-danger" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $this->lang->line('delete') . '"><i class="fa fa-trash"></i></a>';
                }
                //==============================

                $row[] = $column_first;
                $row[] = html_escape($value->invoice_no);
                $row[] = $this->customlib->YYYYMMDDTodateFormat($value->date);
                $row[] = html_escape($value->note);
                $row[] = html_escape($value->income_category);
                $row[] = composeStaffNameByString($value->generated_byname, $value->generated_bysurname, $value->generated_byemployee_id);
                //====================
                if (!empty($fields)) {

                    foreach ($fields as $fields_key => $fields_value) {
                        $display_field = $value->{"$fields_value->name"};
                        if ($fields_value->type == "link") {
                            $display_field = "<a href=" . html_escape($value->{"$fields_value->name"}) . " target='_blank'>" . html_escape($value->{"$fields_value->name"}) . "</a>";
                        } else {
                            $display_field = html_escape($display_field);
                        }
                        $row[] = $display_field;
                    }
                }
                //====================
                $row[] = $value->amount;
                $row[] = "<div class='white-space-nowrap'>" . $action . "</div>";

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

    public function add()
    {
        $this->session->set_userdata('top_menu', 'Income');
        $this->session->set_userdata('sub_menu', 'income/index');
        $data['title']      = $this->lang->line('add_income');
        $data['title_list'] = $this->lang->line('recent_income');
        $custom_fields      = $this->customfield_model->getByBelong('income');

        foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
            if ($custom_fields_value['validation']) {
                $custom_fields_id   = $custom_fields_value['id'];
                $custom_fields_name = $custom_fields_value['name'];
                $this->form_validation->set_rules("custom_fields[income][" . $custom_fields_id . "]", $custom_fields_name, 'trim|required|xss_clean');
            }
        }

        $this->form_validation->set_rules('inc_head_id[]', $this->lang->line('income_head'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('amount', $this->lang->line('amount'), 'trim|required|xss_clean|valid_amount');
        $this->form_validation->set_rules('name', $this->lang->line('name'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('date', $this->lang->line('date'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('description', $this->lang->line('description'), 'trim|xss_clean');
        $this->form_validation->set_rules('documents', $this->lang->line('documents'), 'callback_handle_upload|callback_validateCanUploadFile[documents]');
        if ($this->form_validation->run() == false) {
            $msg = array(
                'inc_head_id[]' => form_error('inc_head_id[]'),
                'name'          => form_error('name'),
                'date'          => form_error('date'),
                'amount'        => form_error('amount'),
                'documents'     => form_error('documents'),
            );

            if (!empty($custom_fields)) {
                foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
                    if ($custom_fields_value['validation']) {
                        $custom_fields_id                                               = $custom_fields_value['id'];
                        $custom_fields_name                                             = $custom_fields_value['name'];
                        $error_msg2["custom_fields[income][" . $custom_fields_id . "]"] = form_error("custom_fields[income][" . $custom_fields_id . "]");
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
            $date = $this->input->post('date', TRUE);
            $data = array(
                'inc_head_id'  => $this->input->post('inc_head_id', TRUE),
                'name'         => $this->input->post('name', TRUE),
                'date'         => $this->customlib->dateFormatToYYYYMMDD($date),
                'amount'       => $this->input->post('amount', TRUE),
                'invoice_no'   => $this->input->post('invoice_no', TRUE),
                'note'         => $this->input->post('description', TRUE),
                'documents'    => $this->input->post('documents', TRUE),
                'generated_by' => $this->customlib->getLoggedInUserID(),
            );
			
            $custom_field_post  = $this->input->post("custom_fields[income]");
            $custom_value_array = array();
            if (!empty($custom_field_post)) {
                foreach ($custom_field_post as $key => $value) {
                    $check_field_type = $this->input->post("custom_fields[income][" . $key . "]");
                    $field_value      = is_array($check_field_type) ? implode(",", $check_field_type) : $check_field_type;
                    $array_custom     = array(
                        'belong_table_id' => 0,
                        'custom_field_id' => $key,
                        'field_value'     => $field_value,
                    );
                    $custom_value_array[] = $array_custom;
                }
            }
            $insert_id = $this->income_model->add($data);

            if (!empty($custom_value_array)) {
                $this->customfield_model->insertRecord($custom_value_array, $insert_id);
            }

            if (isset($_FILES["documents"]) && !empty($_FILES['documents']['name'])) {
                $file_name = $this->media_storage->fileupload("documents", './uploads/hospital_income/');
                if (!IsNullOrEmptyString($file_name)) {
                    $data_img = array('id' => $insert_id, 'documents' => 'uploads/hospital_income/' . $file_name);
                    $this->income_model->add($data_img);

                    // SaaS: add the uploaded document's size to the storage quota usage.
                    try {
                        $this->saasvalidation->updateStorageLimit('storage', ['documents']);
                    } catch (Exception $e) {
                        log_message('error', 'SaaS storage quota update failed (income add): ' . $e->getMessage());
                    }
                }
            }

            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
        }
        echo json_encode($array);
    }

    public function downloadincome($id){
        $get_income  = $this->income_model->get($id);
        $this->media_storage->filedownload($get_income['documents'],'/');
    }

    public function view($id)
    {
        if (!$this->rbac->hasPrivilege('income', 'can_view')) {
            access_denied();
        }
        $data['title']  = $this->lang->line('fees_master_list');
        $income         = $this->income_model->get($id);
        $data['income'] = $income;
        $data['module'] = 'finance';
        $this->load->view('layout/header', $data);
        $this->load->view('income/incomeShow', $data);
        $this->load->view('layout/footer', $data);
    }

    public function delete($id)
    {
        if (!$this->rbac->hasPrivilege('income', 'can_delete')) {
            access_denied();
        }
        $data['title'] = $this->lang->line('fees_master_list');

        // SaaS: release the document's storage from the quota and remove the file.
        $row = $this->income_model->get($id);
        if (!empty($row['documents'])) {
            $file_size_kb = $this->media_storage->getUploadedFileSize($row['documents']);
            if ($file_size_kb > 0) {
                try {
                    $this->saasvalidation->deleteResouceQuota('storage', $file_size_kb);
                } catch (Exception $e) {
                    log_message('error', 'SaaS storage quota release failed (income delete): ' . $e->getMessage());
                }
            }
            $this->media_storage->filedelete($row['documents'], '');
        }

        $this->income_model->remove($id);
        redirect('admin/income/index');
    }

    public function create()
    {
        $data['title'] = $this->lang->line('add_fees_master');
        $this->form_validation->set_rules('income', $this->lang->line('fees_master'), 'trim|required|xss_clean');
        if ($this->form_validation->run() == false) {
            $data['module'] = 'finance';
            $this->load->view('layout/header', $data);
            $this->load->view('income/incomeCreate', $data);
            $this->load->view('layout/footer', $data);
        } else {
            $data = array(
                'income' => $this->input->post('income', TRUE),
            );
            $this->income_model->add($data);
            $this->session->set_flashdata('msg', '<div class="alert alert-success text-left">' . $this->lang->line('income_added_successfully') . '</div>');
            redirect('income/index');
        }
    }

    public function handle_upload()
    {
        $image_validate = $this->config->item('file_validate');
        if (isset($_FILES["documents"]) && !empty($_FILES['documents']['name'])) {
            $file_type         = $_FILES["documents"]['type'];
            $file_size         = $_FILES["documents"]["size"];
            $file_name         = $_FILES["documents"]["name"];
            $allowed_extension = $image_validate['allowed_extension'];
            $ext               = pathinfo($file_name, PATHINFO_EXTENSION);
            $allowed_mime_type = $image_validate['allowed_mime_type'];
            if ($files = @filesize($_FILES['documents']['tmp_name'])) {
                if (!in_array($file_type, $allowed_mime_type)) {
                    $this->form_validation->set_message('handle_upload', $this->lang->line('file_type_not_allowed'));
                    return false;
                }

                if (!in_array(strtolower($ext), $allowed_extension) || !in_array($file_type, $allowed_mime_type)) {
                    $this->form_validation->set_message('handle_upload', $this->lang->line('file_extension_not_allowed'));
                    return false;
                }
				
                if ($file_size > $image_validate['upload_size']) {
                    $this->form_validation->set_message('handle_upload', $this->lang->line('file_size_shoud_be_less_than') . number_format($image_validate['upload_size'] / 1048576, 2) . " MB");
                    return false;
                }
            } else {
                $this->form_validation->set_message('handle_upload', $this->lang->line('error_file_uploading'));
                return false;
            }
            return true;
        }
        return true;
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

    public function getDataByid($id)
    {
        if (!$this->rbac->hasPrivilege('income', 'can_view')) {
            access_denied();
        }
        $data['title']       = $this->lang->line('edit_fees_master');
        $data['id']          = $id;
        $income              = $this->income_model->get($id);
        $data['income']      = $income;
        $incomeHead          = $this->incomehead_model->get();
        $data['incheadlist'] = $incomeHead;
        $this->load->view('admin/income/editModal', $data);
    }

    public function edit($id)
    {
        $data['title']       = $this->lang->line('edit_fees_master');
        $data['id']          = $id;
        $income              = $this->income_model->get($id);
        $data['income']      = $income;
        $data['title_list']  = 'Fees Master List';
        $income_result       = $this->income_model->get();
        $data['incomelist']  = $income_result;
        $expnseHead          = $this->incomehead_model->get();
        $data['incheadlist'] = $expnseHead;
        $custom_fields       = $this->customfield_model->getByBelong('income');
        if (!empty($custom_fields)) {
            foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
                if ($custom_fields_value['validation']) {
                    $custom_fields_id   = $custom_fields_value['id'];
                    $custom_fields_name = $custom_fields_value['name'];
                    $this->form_validation->set_rules("custom_fields[income][" . $custom_fields_id . "]", $custom_fields_name, 'trim|required|xss_clean');
                }
            }
        }
        $this->form_validation->set_rules('inc_head_id', $this->lang->line('income_head'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('inc_head_id[]', $this->lang->line('income_head'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('amount', $this->lang->line('amount'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('name', $this->lang->line('name'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('date', $this->lang->line('date'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('description', $this->lang->line('description'), 'trim|xss_clean');
        $this->form_validation->set_rules('documents', $this->lang->line('documents'), 'callback_handle_upload|callback_validateCanUploadFile[documents]');
        if ($this->form_validation->run() == false) {
            $msg = array(
                'inc_head_id[]' => form_error('inc_head_id[]'),
                'amount'        => form_error('amount'),
                'name'          => form_error('name'),
                'date'          => form_error('date'),
                'documents'     => form_error('documents'),
            );

            if (!empty($custom_fields)) {
                foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
                    if ($custom_fields_value['validation']) {
                        $custom_fields_id                                               = $custom_fields_value['id'];
                        $custom_fields_name                                             = $custom_fields_value['name'];
                        $error_msg2["custom_fields[income][" . $custom_fields_id . "]"] = form_error("custom_fields[income][" . $custom_fields_id . "]");
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
            $custom_field_post = $this->input->post("custom_fields[income]");
            $date              = $this->input->post('date', TRUE);
            $data              = array(
                'id'           => $id,
                'inc_head_id'  => $this->input->post('inc_head_id', TRUE),
                'name'         => $this->input->post('name', TRUE),
                'date'         => $this->customlib->dateFormatToYYYYMMDD($date),
                'amount'       => $this->input->post('amount', TRUE),
                'invoice_no'   => $this->input->post('invoice_no', TRUE),
                'note'         => $this->input->post('description', TRUE),
                'generated_by' => $this->customlib->getLoggedInUserID(),
            );
            $insert_id = $this->income_model->add($data);
            if (!empty($custom_fields)) {
                foreach ($custom_field_post as $key => $value) {
                    $check_field_type = $this->input->post("custom_fields[income][" . $key . "]");
                    $field_value      = is_array($check_field_type) ? implode(",", $check_field_type) : $check_field_type;
                    $array_custom     = array(
                        'belong_table_id' => $id,
                        'custom_field_id' => $key,
                        'field_value'     => $field_value,
                    );
                    $custom_value_array[] = $array_custom;
                }
                $this->customfield_model->updateRecord($custom_value_array, $id, 'income');
            }
           
            if (isset($_FILES["documents"]) && !empty($_FILES['documents']['name'])) {
                // Size of the document being replaced, for quota diff calculation.
                $existing     = $this->income_model->get($id);
                $prev_size_kb = (!empty($existing['documents'])) ? $this->media_storage->getUploadedFileSize($existing['documents']) : 0;

                $file_name = $this->media_storage->fileupload("documents", './uploads/hospital_income/');
                if (!IsNullOrEmptyString($file_name)) {
                    // Remove the old physical file that is being replaced.
                    if (!empty($existing['documents'])) {
                        $this->media_storage->filedelete($existing['documents'], '');
                    }

                    $data_img = array('id' => $insert_id, 'documents' => 'uploads/hospital_income/' . $file_name);
                    $this->income_model->add($data_img);

                    // SaaS: adjust storage quota by the size difference (new vs replaced).
                    try {
                        $new_size_kb = $this->media_storage->getTmpFileSize('documents');
                        if ($prev_size_kb > $new_size_kb) {
                            $this->saasvalidation->deleteResouceQuota('storage', $prev_size_kb - $new_size_kb);
                        } elseif ($new_size_kb > $prev_size_kb) {
                            $this->saasvalidation->updateResouceQuota('storage', $new_size_kb - $prev_size_kb);
                        }
                    } catch (Exception $e) {
                        log_message('error', 'SaaS storage quota update failed (income edit): ' . $e->getMessage());
                    }
                }
            }

            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('update_message'));
        }
        echo json_encode($array);
    }

    public function incomeSearch()
    {
        if (!$this->rbac->hasPrivilege('income_report', 'can_view')) {
            access_denied();
        }

        $this->session->set_userdata('top_menu', 'Reports');
        $this->session->set_userdata('sub_menu', 'reports/finance');
        $this->session->set_userdata('subsub_menu', 'reports/income/incomesearch');

        $custom_fields      = $this->customfield_model->get_custom_fields('income', '', '', 1);
        $data["searchlist"] = $this->search_type;
        $data['fields']     = $custom_fields;
        $data['module'] = 'finance';
        $this->load->view('layout/header', $data);
        $this->load->view('admin/income/incomeSearch', $data);
        $this->load->view('layout/footer', $data);
    }    

    public function checkvalidation()
    {
        if (!$this->rbac->hasPrivilege('income_report', 'can_view')) {
            access_denied();
        }
        $search = $this->input->post('search');
        $this->form_validation->set_rules('search_type', $this->lang->line('search_type'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('modules_select', $this->lang->line('modules'), 'trim|required|xss_clean');
        if ($this->form_validation->run() == false) {
            $msg = array(
                'search_type'    => form_error('search_type'),
                'modules_select' => form_error('modules_select'),
            );
			
            $json_array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $param = array(
                'search_type'    => $this->input->post('search_type', TRUE),
                'collect_staff'  => $this->input->post('collect_staff', TRUE),
                'modules_select' => $this->input->post('modules_select', TRUE),
                'date_from'      => $this->input->post('date_from', TRUE),
                'date_to'        => $this->input->post('date_to', TRUE),
            );

            $json_array = array('status' => 'success', 'error' => '', 'param' => $param, 'message' => $this->lang->line('success_message'));
        }
        echo json_encode($json_array);
    }

    public function checkvalidationincome()
    {
        $search = $this->input->post('search');
        $this->form_validation->set_rules('search_type', $this->lang->line('search_type'), 'trim|required|xss_clean');
        if ($this->form_validation->run() == false) {
            $msg = array(
                'search_type' => form_error('search_type'),
            );
            $json_array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $param = array(
                'search_type' => $this->input->post('search_type', TRUE),
                'date_from'   => $this->input->post('date_from', TRUE),
                'date_to'     => $this->input->post('date_to', TRUE),
            );

            $json_array = array('status' => 'success', 'error' => '', 'param' => $param, 'message' => $this->lang->line('success_message'));
        }
        echo json_encode($json_array);
    }

    public function alltransactionreport($value = '')
    {
        if (!$this->rbac->hasPrivilege('all_transaction_report', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'Reports');
        $this->session->set_userdata('sub_menu', 'reports/finance');
        $this->session->set_userdata('subsub_menu', 'reports/income/alltransactionreport');

        $data['title']          = 'title';
        $resultList_staffsearch = $this->patient_model->getstaffsearch();
        $data['staffsearch']    = $resultList_staffsearch;
        $data["modules"]        = $this->customlib->get_modules();
        $data["searchlist"]     = $this->search_type;
        $data['search_data']    = '';
        $data['module'] = 'finance';
        $this->load->view('layout/header', $data);
        $this->load->view('admin/income/alltransactionReport', $data);
        $this->load->view('layout/footer', $data);
    }

    public function transactionreports()
    {
        if (!$this->rbac->hasPrivilege('all_transaction_report', 'can_view')) {
            access_denied();
        }
        $search['search_type']    = $this->input->post('search_type', TRUE);
        $search['collect_staff']  = $this->input->post('collect_staff', TRUE);
        $search['modules_select'] = $this->input->post('modules_select', TRUE);
        $search['date_from']      = $this->input->post('date_from', TRUE);
        $search['date_to']        = $this->input->post('date_to', TRUE);
        $start_date               = '';
        $end_date                 = '';
        $currency_symbol           = $this->customlib->getHospitalCurrencyFormat();
        
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

        $search['start_date'] = $start_date;
        $search['end_date']   = $end_date;

        if ($search['modules_select'] == 'all') {
            $transactiondata = $this->transaction_model->allTransactionRecord($start_date, $end_date, $search['collect_staff']);
        } elseif ($search['modules_select'] == 'appointment') {
            $transactiondata = $this->transaction_model->appointmentpatientRecord($start_date, $end_date, $search['collect_staff']);            
        } elseif ($search['modules_select'] == 'opd_patient') {
            $transactiondata = $this->transaction_model->opdpatientRecord($start_date, $end_date, $search['collect_staff']);            
        } elseif ($search['modules_select'] == 'ipd_patient') {
            $transactiondata = $this->transaction_model->ipdpatientRecord($start_date, $end_date, $search['collect_staff']);
        } elseif ($search['modules_select'] == 'pharmacy_bill') {
            $transactiondata = $this->transaction_model->pharmacybillRecord($start_date, $end_date, $search['collect_staff']);
        } elseif ($search['modules_select'] == 'pathology_test') {
            $transactiondata = $this->transaction_model->pathologybillRecord($start_date, $end_date, $search['collect_staff']);
        } elseif ($search['modules_select'] == 'radiology_test') {
            $transactiondata = $this->transaction_model->radiologybillRecord($start_date, $end_date, $search['collect_staff']);
        } elseif ($search['modules_select'] == 'blood_bank') {
            $transactiondata = $this->transaction_model->bloodissuebillRecordReport($search);			
        } elseif ($search['modules_select'] == 'ambulance_call') {
            $transactiondata = $this->transaction_model->ambulancecallRecord($search);			
        } elseif ($search['modules_select'] == 'income') {
            $transactiondata = $this->transaction_model->incomeRecord($start_date, $end_date, $search['collect_staff']);
        } elseif ($search['modules_select'] == 'expense') {
            $transactiondata = $this->transaction_model->expensesRecord($start_date, $end_date, $search['collect_staff']);
        } elseif ($search['modules_select'] == 'payroll_report') {
            $transactiondata = $this->transaction_model->payrollRecord($start_date, $end_date, $search['collect_staff']);
        }      

        $transactiondata = json_decode($transactiondata);
        $dt_data         = array();
        $total_amount    = 0;

        if (!empty($transactiondata->data)) {
            foreach ($transactiondata->data as $key => $value) {
				if($value->type != 'refund'){
					$total_amount += $value->amount;
				}else{
					$total_amount -= $value->amount;
				}

                if (!empty($value->ward) && !in_array($value->ward, ['income', 'expenses'])) {
                    $ward = $this->customlib->getSessionPrefixbyType($value->ward);
                } else {
                    $ward = '';
                }
                if (!empty($value->reference)) {
                    $reference = $value->reference;
                } else {
                    $reference = '';
                }
                if ($value->section != null) {
                    if($value->section == "Appointment"){
                        $section = "OPD / Appointment";
                    }else{
                        $section = $value->section;
                    }
                } else {
                    $section = '';
                }
                if ($value->type != null) {
                    $type = $this->lang->line($value->type);
                } else {
                    $type = '';
                }
                if ($value->payment_mode != null) {
                    $payment_mode = $this->lang->line(strtolower($value->payment_mode));
                } else {
                    $payment_mode = '';
                }
                if (!empty($value->amount)) {
					if($value->type != 'refund'){
						$amount = $value->amount;
					}else{
						$amount = '-'.$value->amount;
					}
                } else {
                    $amount = '';
                }
                if (!empty($value->patient_id)) {
                    $patient_id = " (" . $value->patient_id . ")";
                } else {
                    $patient_id = '';
                }
                if (($search['modules_select'] == 'income') || ($search['modules_select'] == 'expense') || ($search['modules_select'] == 'payroll_report')) {
                    $date = $this->customlib->YYYYMMDDTodateFormat($value->payment_date);
                } else {
                    $date = $this->customlib->YYYYMMDDHisTodateFormat($value->payment_date, $this->customlib->getHospitalTimeFormat());
                }

                $row                = array();
                $transaction_prefix = $this->customlib->getSessionPrefixByType('transaction_id');
               
                $row[]     = $transaction_prefix . $value->id;
                $row[]     = $date;
                if(!empty($value->patient_id)){
                     $row[]     = composePatientName($value->patient_name,$value->patient_id);
                 }else{
                    $row[]="";
                 }
               
                $row[]     = html_escape($ward) . html_escape($reference);
                $row[]     = html_escape($section);
                $row[]     = composeStaffNameByString($value->name, $value->surname, $value->employee_id);
                $row[]     = $type;
                $row[]     = $payment_mode;
                $row[]     = $amount;
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
            $footer_row[] = "<b>" . $this->lang->line('total_amount') . "</b>" . ':';
            $footer_row[] = "<b>" . $currency_symbol . (number_format($total_amount, 2, '.', '')) . "<br/>";
            $dt_data[]    = $footer_row;
        }

        $json_data = array(
            "draw"            => intval($transactiondata->draw),
            "recordsTotal"    => intval($transactiondata->recordsTotal),
            "recordsFiltered" => intval($transactiondata->recordsFiltered),
            "data"            => $dt_data,
        );
        echo json_encode($json_data);
    }

    public function incomereports()
    {
        if (!$this->rbac->hasPrivilege('income_report', 'can_view')) {
            access_denied();
        }
        $search['search_type'] = $this->input->post('search_type', TRUE);
        $search['date_from']   = $this->input->post('date_from', TRUE);
        $search['date_to']     = $this->input->post('date_to', TRUE);
        $start_date            = '';
        $end_date              = '';
        $fields                = $this->customfield_model->get_custom_fields('income', '', '', 1);
        $currency_symbol = $this->customlib->getHospitalCurrencyFormat();
        
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

        $reportdata   = $this->transaction_model->incomereportRecord($start_date, $end_date);
        $reportdata   = json_decode($reportdata);
        $dt_data      = array();
        $total_amount = 0;
        if (!empty($reportdata->data)) {
            foreach ($reportdata->data as $key => $value) {

                $total_amount += $value->amount;
                $row   = array();
                $row[] = html_escape($value->invoice_name);
                $row[] = html_escape($value->invoice_no);
                $row[] = html_escape($value->income_category);
                $row[] = $this->customlib->YYYYMMDDTodateFormat($value->payment_date);
                if (!empty($fields)) {
                    foreach ($fields as $fields_key => $fields_value) {
                        $display_field = $value->{"$fields_value->name"};
                        if ($fields_value->type == "link") {
                            $display_field = "<a href=" . html_escape($value->{"$fields_value->name"}) . " target='_blank'>" . html_escape($value->{"$fields_value->name"}) . "</a>";
                        } else {
                            $display_field = html_escape($display_field);
                        }
                        $row[] = $display_field;
                    }
                }

                $row[]     = $value->amount;
                $dt_data[] = $row;
            }

            $footer_row   = array();
            $footer_row[] = "";
            $footer_row[] = "";
            $footer_row[] = "";
            if (!empty($fields)) {
                foreach ($fields as $fields_key => $fields_value) {
                    $display_field = '';                   
                    $footer_row[] = $display_field;
                }
            }
            $footer_row[] = "<b>" . $this->lang->line('total_amount') . "</b>" . ':';
            $footer_row[] = "<b>" . $currency_symbol . (number_format($total_amount, 2, '.', '')) . "<br/>";
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

    public function incomegroup()
    {
        $this->session->set_userdata('top_menu', 'Reports');
        $this->session->set_userdata('sub_menu', 'reports/finance');
        $this->session->set_userdata('subsub_menu', 'reports/income/incomegroup');

        $search_type = $this->input->post('search_type', TRUE) ?: '';
        $data['head_id'] = $head_id = "";
        $head_post = $this->input->post('head', TRUE);
        if ($head_post != '') {
            $data['head_id'] = $head_id = $head_post;
        }
        $data['fields']      = $this->customfield_model->get_custom_fields('income', '', '', 1);
        $data["searchlist"]  = $this->search_type;
        $data["search_type"] = $search_type;
        $incomeList          = $this->income_model->searchincomegroup($search_type, $head_id);
        $data['headlist']    = $this->incomehead_model->get();
        $data['incomeList']  = $incomeList;
        $data['module'] = 'finance';
        $this->load->view('layout/header', $data);
        $this->load->view('admin/income/groupincomeReport', $data);
        $this->load->view('layout/footer', $data);
    }

    /* this function is used to get and return income group report parameter without applying any validation */
    public function getgroupreportparam()
    {
        $search_type = $this->input->post('search_type');
        $head        = $this->input->post('head');

        $date_from = "";
        $date_to   = "";
        if ($search_type == 'period') {
            $date_from = $this->input->post('date_from');
            $date_to   = $this->input->post('date_to');
        }

        $params = array('search_type' => $search_type, 'head' => $head, 'date_from' => $date_from, 'date_to' => $date_to);
        $array  = array('status' => 1, 'error' => '', 'params' => $params);
        echo json_encode($array);
    }
    /* this function is used to get income group report by using datatable */

    public function dtincomegroupreport()
    {
        $search_type = $this->input->post('search_type');
        $date_from   = $this->input->post('date_from');
        $date_to     = $this->input->post('date_to');
        $head        = $this->input->post('head');
        $fields      = $this->customfield_model->get_custom_fields('income', '', '', 1);
        $currency_symbol = $this->customlib->getHospitalCurrencyFormat();
        
        if (isset($search_type) && $search_type != '') {

            $dates               = $this->customlib->get_betweendate($search_type);
            $data['search_type'] = $_POST['search_type'];

        } else {

            $dates               = $this->customlib->get_betweendate('this_year');
            $data['search_type'] = '';

        }
        $data['head_id'] = $head_id = "";
        if (isset($_POST['head']) && $_POST['head'] != '') {
            $data['head_id'] = $head_id = $_POST['head'];
        }

        $start_date = date('Y-m-d', strtotime($dates['from_date']));
        $end_date   = date('Y-m-d', strtotime($dates['to_date']));

        $data['label'] = date($this->customlib->getHospitalDateFormat(), strtotime($start_date)) . " " . $this->lang->line('to') . " " . date($this->customlib->getHospitalDateFormat(), strtotime($end_date));
        $incomeList    = $this->report_model->searchincomegroup($start_date, $end_date, $head_id);
       
        $m               = json_decode($incomeList);
        $currency_symbol = $this->customlib->getHospitalCurrencyFormat();
        $dt_data         = array();
        $grand_total     = 0;

        if (!empty($m->data)) {
            $grd_total  = 0;
            $inchead_id = 0;
            $count      = 0;
            foreach ($m->data as $key => $value) {
                $income_head[$value->head_id][] = $value;
            }
            foreach ($m->data as $key => $value) {
                $inc_head_id  = $value->head_id;
                $total_amount = "<b>" . $value->amount . "</b>";
                $grd_total += $value->amount;
                $row = array();
                if ($inchead_id == $inc_head_id) {
                    $row[] = "";
                    $count++;
                } else {
                    $row[] = html_escape($value->income_category);
                    $count = 0;
                }
                $row[] = $value->id;
                $row[] = html_escape($value->name);
                $row[] = $this->customlib->YYYYMMDDTodateFormat($value->date);
                $row[] = html_escape($value->invoice_no);

                if (!empty($fields)) {
                    foreach ($fields as $fields_key => $fields_value) {
                        $display_field = $value->{"$fields_value->name"};
                        if ($fields_value->type == "link") {
                            $display_field = "<a href=" . html_escape($value->{"$fields_value->name"}) . " target='_blank'>" . html_escape($value->{"$fields_value->name"}) . "</a>";
                        } else {
                            $display_field = html_escape($display_field);
                        }
                        $row[] = $display_field;
                    }
                }
                
                $row[]      = $value->amount;
                $dt_data[]  = $row;
                $inchead_id = $value->head_id;
                $sub_total  = 0;
                if ($count == (count($income_head[$value->head_id]) - 1)) {
                    foreach ($income_head[$value->head_id] as $inc_headkey => $inc_headvalue) {
                        $sub_total += $inc_headvalue->amount;
                    }
                    $amount_row   = array();
                    $amount_row[] = "";
                    $amount_row[] = "";
                    $amount_row[] = "";
                    $amount_row[] = "";
                    $amount_row[] = "";
                    if (!empty($fields)) {
                        foreach ($fields as $fields_key => $fields_value) {

                            $display_field = '';                             
                            $amount_row[] = $display_field;
                            
                        }
                    }
                
                    $amount_row[] = "<b>" . $this->lang->line('subtotal') .': ' .$currency_symbol . amountFormat($sub_total) . "</b>";
                    $dt_data[]    = $amount_row;
                }
            }

            $grand_total  = "<b>" . $currency_symbol . amountFormat($grd_total) . "</b>";
            $footer_row   = array();
            $footer_row[] = "";
            $footer_row[] = "";
            $footer_row[] = "";
            $footer_row[] = "";
            $footer_row[] = "";
            if (!empty($fields)) {
                foreach ($fields as $fields_key => $fields_value) {

                    $display_field = '';                        
                    $footer_row[] = $display_field;
                    
                }
            }
            $footer_row[] = "<b>" . $this->lang->line('total').': ' .$grand_total. "</b>";
            $dt_data[]    = $footer_row;
        }

        $json_data = array(
            "draw"            => intval($m->draw),
            "recordsTotal"    => intval($m->recordsTotal),
            "recordsFiltered" => intval($m->recordsFiltered),
            "data"            => $dt_data,
        );
        echo json_encode($json_data);
    }

}
