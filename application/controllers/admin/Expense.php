<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Expense extends Admin_Controller
{
	public $search_type;


    public function __construct()
    {
        parent::__construct();
        $this->load->library('Customlib');
        $this->load->library("datatables");
        $this->load->library("SaasValidation");
        $this->config->load("payroll");
        $this->config->load("image_valid");
        $this->load->model("transaction_model");
        $this->search_type = $this->config->item('search_type');
        $this->load->helper('customfield_helper');
    }

    public function index()
    {
        if (!$this->module_lib->hasActive('expense')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'finance');
        $this->session->set_userdata('sub_menu', 'expense/index');
        $data['title']      = $this->lang->line('add_expense');
        $data['title_list'] = $this->lang->line('recent_expenses');
        $this->form_validation->set_rules('exp_head_id', $this->lang->line('expense_head'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('amount', $this->lang->line('amount'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('name', $this->lang->line('name'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('date', $this->lang->line('date'), 'trim|required|xss_clean');
        $expense_result      = $this->expense_model->get();
        $data['expenselist'] = $expense_result;
        $data['fields']      = $this->customfield_model->get_custom_fields('expenses', 1);
        $expnseHead          = $this->expensehead_model->get();
        $data['expheadlist'] = $expnseHead;
        $data['module'] = 'finance';
        $this->load->view('layout/header', $data);
        $this->load->view('admin/expense/expenseList', $data);
        $this->load->view('layout/footer', $data);
    }

    public function getDatatable()
    {
        if (!$this->rbac->hasPrivilege('expense', 'can_view')) {
            access_denied();
        }
        $dt_response = $this->expense_model->getAllRecord();
        $fields      = $this->customfield_model->get_custom_fields('expenses', 1);
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
                    $action .= '<a href="' . base_url() . 'admin/expense/download/' . $value->id . '" class="btn btn-sm btn-outline-secondary" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $this->lang->line('download') . '"><i class="fa fa-download"></i></a>';
                }

                if ($this->rbac->hasPrivilege('expense', 'can_edit')) {
                    $action .= '<a href="#" onclick="edit(' . $value->id . ')" class="btn btn-sm btn-outline-primary" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $this->lang->line('edit') . '"><i class="fa fa-pencil"></i></a>';
                }

                if ($this->rbac->hasPrivilege('expense', 'can_delete')) {
                    $action .= '<a href="#" onclick="delete_record(' . $value->id . ')" class="btn btn-sm btn-outline-danger" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $this->lang->line('delete') . '"><i class="fa fa-trash"></i></a>';
                }
                //==============================

                $row[] = $column_first;
                $row[] = $value->invoice_no;
                $row[] = $this->customlib->YYYYMMDDTodateFormat($value->date);
                $row[] = html_escape($value->note);
                $row[] = $value->exp_category;
                $row[] = composeStaffNameByString($value->generated_byname, $value->generated_bysurname, $value->generated_byemployee_id);
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
                $row[]     = $value->amount;
                $row[]     = "<div class='white-space-nowrap'>" . $action . "</div>";
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
        $data['title']      = $this->lang->line('add_expense');
        $data['title_list'] = $this->lang->line('recent_expenses');
        $custom_fields      = $this->customfield_model->getByBelong('expenses');
        foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
            if ($custom_fields_value['validation']) {
                $custom_fields_id   = $custom_fields_value['id'];
                $custom_fields_name = $custom_fields_value['name'];
                $this->form_validation->set_rules("custom_fields[expenses][" . $custom_fields_id . "]", $custom_fields_name, 'trim|required|xss_clean');
            }
        }
        $this->form_validation->set_rules('exp_head_id', $this->lang->line('expense_head'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('amount', $this->lang->line('amount'), 'trim|required|xss_clean|valid_amount');
        $this->form_validation->set_rules('name', $this->lang->line('name'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('exdate', $this->lang->line('date'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('documents', $this->lang->line('documents'), 'callback_handle_upload|callback_validateCanUploadFile[documents]');
        if ($this->form_validation->run() == false) {
            $msg = array(
                'exp_head_id' => form_error('exp_head_id'),
                'name'        => form_error('name'),
                'exdate'        => form_error('exdate'),
                'amount'      => form_error('amount'),
                'documents'   => form_error('documents'),
            );
            if (!empty($custom_fields)) {
                foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
                    if ($custom_fields_value['validation']) {
                        $custom_fields_id                                                 = $custom_fields_value['id'];
                        $custom_fields_name                                               = $custom_fields_value['name'];
                        $error_msg2["custom_fields[expenses][" . $custom_fields_id . "]"] = form_error("custom_fields[expenses][" . $custom_fields_id . "]");
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
            $exdate = $this->input->post('exdate');
            $data   = array(
                'exp_head_id'  => $this->input->post('exp_head_id', TRUE),
                'name'         => $this->input->post('name', TRUE),
                'date'         => $this->customlib->dateFormatToYYYYMMDD($exdate),
                'amount'       => $this->input->post('amount', TRUE),
                'invoice_no'   => $this->input->post('invoice_no', TRUE),
                'note'         => $this->input->post('description', TRUE),
                'documents'    => $this->input->post('documents', TRUE),
                'generated_by' => $this->customlib->getLoggedInUserID(),
            );
            $custom_field_post  = $this->input->post("custom_fields[expenses]");
            $custom_value_array = array();
            if (!empty($custom_field_post)) {
                foreach ($custom_field_post as $key => $value) {
                    $check_field_type = $this->input->post("custom_fields[expenses][" . $key . "]");
                    $field_value      = is_array($check_field_type) ? implode(",", $check_field_type) : $check_field_type;
                    $array_custom     = array(
                        'belong_table_id' => 0,
                        'custom_field_id' => $key,
                        'field_value'     => $field_value,
                    );
                    $custom_value_array[] = $array_custom;
                }
            }
            $insert_id = $this->expense_model->add($data);
            if (!empty($custom_value_array)) {
                $this->customfield_model->insertRecord($custom_value_array, $insert_id);
            }

            if (isset($_FILES["documents"]) && !empty($_FILES['documents']['name'])) {
                $file_name = $this->media_storage->fileupload("documents",'./uploads/hospital_expense/');
                if (!IsNullOrEmptyString($file_name)) {
                    $data_img = array('id' => $insert_id, 'documents' => 'uploads/hospital_expense/'.$file_name);
                    $this->expense_model->add($data_img);

                    // SaaS: add the uploaded document's size to the storage quota usage.
                    try {
                        $this->saasvalidation->updateStorageLimit('storage', ['documents']);
                    } catch (Exception $e) {
                        log_message('error', 'SaaS storage quota update failed (expense add): ' . $e->getMessage());
                    }
                }
            }

            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
        }
        echo json_encode($array);
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

    public function download($id)
    {
        $expense_data=$this->expense_model->get($id);
        $this->media_storage->filedownload($expense_data['documents'],'./');
    }

    public function view($id)
    {
        if (!$this->rbac->hasPrivilege('expense', 'can_view')) {
            access_denied();
        }
        $data['title']   = $this->lang->line('fees_master_list');
        $expense         = $this->expense_model->get($id);
        $data['expense'] = $expense;
        $data['module'] = 'finance';
        $this->load->view('layout/header', $data);
        $this->load->view('expense/expenseShow', $data);
        $this->load->view('layout/footer', $data);
    }

    public function delete($id)
    {
        if (!$this->rbac->hasPrivilege('expense', 'can_delete')) {
            access_denied();
        }
        $data['title'] = $this->lang->line('fees_master_list');

        // SaaS: release the document's storage from the quota and remove the file.
        $row = $this->expense_model->get($id);
        if (!empty($row['documents'])) {
            $file_size_kb = $this->media_storage->getUploadedFileSize($row['documents']);
            if ($file_size_kb > 0) {
                try {
                    $this->saasvalidation->deleteResouceQuota('storage', $file_size_kb);
                } catch (Exception $e) {
                    log_message('error', 'SaaS storage quota release failed (expense delete): ' . $e->getMessage());
                }
            }
            $this->media_storage->filedelete($row['documents'], '');
        }

        $this->expense_model->remove($id);
        redirect('admin/expense/index');
    }

    public function create()
    {
        if (!$this->rbac->hasPrivilege('expense', 'can_add')) {
            access_denied();
        }
        $data['title'] = $this->lang->line('add_fees_master');
        $this->form_validation->set_rules('expense', $this->lang->line('expense'), 'trim|required|xss_clean');
        if ($this->form_validation->run() == false) {
            $data['module'] = 'finance';
            $this->load->view('layout/header', $data);
            $this->load->view('expense/expenseCreate', $data);
            $this->load->view('layout/footer', $data);
        } else {
            $data = array(
                'expense' => $this->input->post('expense'),
            );
            $this->expense_model->add($data);
            $this->session->set_flashdata('msg', '<div class="alert alert-success text-left">' . $this->lang->line('expense_added_successfully') . '</div>');
            redirect('expense/index');
        }
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
        if (!$this->rbac->hasPrivilege('expense', 'can_view')) {
            access_denied();
        }
        $data['title']       = $this->lang->line('edit_fees_master');
        $data['id']          = $id;
        $expense             = $this->expense_model->get($id);
        $data['expense']     = $expense;
        $data['title_list']  = $this->lang->line('fees_master_list');
        $expnseHead          = $this->expensehead_model->get();
        $data['expheadlist'] = $expnseHead;
        $this->load->view('admin/expense/editexpenseModal', $data);
    }

    public function edit($id)
    {
        if (!$this->rbac->hasPrivilege('expense', 'can_edit')) {
            access_denied();
        }
        $data['title']       = $this->lang->line('edit_fees_master');
        $data['id']          = $id;
        $expense             = $this->expense_model->get($id);
        $data['expense']     = $expense;
        $data['title_list']  = $this->lang->line('fees_master_list');
        $expense_result      = $this->expense_model->get();
        $data['expenselist'] = $expense_result;
        $expnseHead          = $this->expensehead_model->get();
        $data['expheadlist'] = $expnseHead;

        $custom_fields = $this->customfield_model->getByBelong('expenses');
        if (!empty($custom_fields)) {
            foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
                if ($custom_fields_value['validation']) {
                    $custom_fields_id   = $custom_fields_value['id'];
                    $custom_fields_name = $custom_fields_value['name'];

                    $this->form_validation->set_rules("custom_fields[expenses][" . $custom_fields_id . "]", $custom_fields_name, 'trim|required|xss_clean');

                }
            }
        }
        $this->form_validation->set_rules('exp_head_id', $this->lang->line('expense_head'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('documents', $this->lang->line('documents'), 'callback_handle_upload|callback_validateCanUploadFile[documents]');
        $this->form_validation->set_rules('amount', $this->lang->line('amount'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('name', $this->lang->line('name'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('date', $this->lang->line('date'), 'trim|required|xss_clean');
        if ($this->form_validation->run() == false) {
            $msg = array(
                'exp_head_id' => form_error('exp_head_id'),
                'amount'      => form_error('amount'),
                'name'        => form_error('name'),
                'date'        => form_error('date'),
                'documents'   => form_error('documents'),
            );

            if (!empty($custom_fields)) {
                foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
                    if ($custom_fields_value['validation']) {
                        $custom_fields_id                                                 = $custom_fields_value['id'];
                        $custom_fields_name                                               = $custom_fields_value['name'];
                        $error_msg2["custom_fields[expenses][" . $custom_fields_id . "]"] = form_error("custom_fields[expenses][" . $custom_fields_id . "]");
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
            $exdate            = $this->input->post('date');
            $custom_field_post = $this->input->post("custom_fields[expenses]");
            $data              = array(
                'id'           => $id,
                'exp_head_id'  => $this->input->post('exp_head_id', TRUE),
                'name'         => $this->input->post('name', TRUE),
                'invoice_no'   => $this->input->post('invoice_no', TRUE),
                'date'         => $this->customlib->dateFormatToYYYYMMDD($exdate),
                'amount'       => $this->input->post('amount', TRUE),
                'note'         => $this->input->post('description', TRUE),
                'generated_by' => $this->customlib->getLoggedInUserID(),
            );
            $insert_id = $this->expense_model->add($data);
            if (!empty($custom_fields)) {
                foreach ($custom_field_post as $key => $value) {
                    $check_field_type = $this->input->post("custom_fields[expenses][" . $key . "]");
                    $field_value      = is_array($check_field_type) ? implode(",", $check_field_type) : $check_field_type;
                    $array_custom     = array(
                        'belong_table_id' => $id,
                        'custom_field_id' => $key,
                        'field_value'     => $field_value,
                    );
                    $custom_value_array[] = $array_custom;
                }
                $this->customfield_model->updateRecord($custom_value_array, $id, 'expenses');
            }
            
            if (isset($_FILES["documents"]) && !empty($_FILES['documents']['name'])) {
                // Size of the document being replaced, for quota diff calculation.
                $existing     = $this->expense_model->get($id);
                $prev_size_kb = (!empty($existing['documents'])) ? $this->media_storage->getUploadedFileSize($existing['documents']) : 0;

                $file_name = $this->media_storage->fileupload("documents",'./uploads/hospital_expense/');
                if (!IsNullOrEmptyString($file_name)) {
                    // Remove the old physical file that is being replaced.
                    if (!empty($existing['documents'])) {
                        $this->media_storage->filedelete($existing['documents'], '');
                    }

                    $data_img = array('id' => $insert_id, 'documents' => 'uploads/hospital_expense/'.$file_name);
                    $this->expense_model->add($data_img);

                    // SaaS: adjust storage quota by the size difference (new vs replaced).
                    try {
                        $new_size_kb = $this->media_storage->getTmpFileSize('documents');
                        if ($prev_size_kb > $new_size_kb) {
                            $this->saasvalidation->deleteResouceQuota('storage', $prev_size_kb - $new_size_kb);
                        } elseif ($new_size_kb > $prev_size_kb) {
                            $this->saasvalidation->updateResouceQuota('storage', $new_size_kb - $prev_size_kb);
                        }
                    } catch (Exception $e) {
                        log_message('error', 'SaaS storage quota update failed (expense edit): ' . $e->getMessage());
                    }
                }
            }

            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('expense_edit_successfully'));
        }
        echo json_encode($array);
    }

    public function expenseSearch()
    {
        if (!$this->rbac->hasPrivilege('expense_report', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'Reports');
        $this->session->set_userdata('sub_menu', 'reports/finance');
        $this->session->set_userdata('subsub_menu', 'reports/expense/expensesearch');

        $data['title'] = 'Search Expense';
        $select        = 'expenses.id,expenses.date,expenses.invoice_no,expenses.name,expenses.amount,expenses.documents,expenses.note,expense_head.exp_category,expenses.exp_head_id';
        $join          = array('JOIN expense_head ON expenses.exp_head_id = expense_head.id');
        $table_name    = "expenses";

        $search_type = $this->input->post("search_type");
        if (isset($search_type)) {
            $search_type = $this->input->post("search_type");
        } else {
            $search_type = "this_month";
        }
        if (empty($search_type)) {
            $search_type = "";
            $listMessage = $this->report_model->getReport($table_name,$select, $join);
        } else {
            $search_table     = "expenses";
            $search_column    = "date";
            $additional       = array();
            $additional_where = array();
            $listMessage      = $this->report_model->searchReport($table_name,$select, $join, $search_type, $search_table, $search_column);
        }
        $data['resultList']  = $listMessage;
        $data["searchlist"]  = $this->search_type;
        $data["search_type"] = $search_type;
        $data['fields']      = $this->customfield_model->get_custom_fields('expenses', '', '', 1);
        $data['module'] = 'finance';
        $this->load->view('layout/header', $data);
        $this->load->view('admin/expense/expenseSearch', $data);
        $this->load->view('layout/footer', $data);
    }

    public function checkvalidationexpense()
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
                'search_type' => $this->input->post('search_type'),

                'date_from'   => $this->input->post('date_from'),
                'date_to'     => $this->input->post('date_to'),
            );

            $json_array = array('status' => 'success', 'error' => '', 'param' => $param, 'message' => $this->lang->line('success_message'));
        }
        echo json_encode($json_array);
    }

    public function expensereports()
    {
        if (!$this->rbac->hasPrivilege('expense_report', 'can_view')) {
            access_denied();
        }

        $search['search_type'] = $this->input->post('search_type');
        $search['date_from']   = $this->input->post('date_from');
        $search['date_to']     = $this->input->post('date_to');
        $start_date            = '';
        $end_date              = '';
        $fields                = $this->customfield_model->get_custom_fields('expenses', '', '', 1);
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

        $reportdata   = $this->transaction_model->expensereportRecord($start_date, $end_date);
        $reportdata   = json_decode($reportdata);
        $dt_data      = array();
        $total_amount = 0;
        if (!empty($reportdata->data)) {
            foreach ($reportdata->data as $key => $value) {

                $total_amount += $value->amount;
                $row   = array();
                $row[] = $value->expense_name;
                $row[] = $value->invoice_no;
                $row[] = $value->exp_category;
                $row[] = $this->customlib->YYYYMMDDTodateFormat($value->payment_date);
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
                $row[]     = $value->amount;
                $dt_data[] = $row;
            }
            $footer_row   = array();
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
    
    public function expensegroup()
    {
        $this->session->set_userdata('top_menu', 'Reports');
        $this->session->set_userdata('sub_menu', 'reports/finance');
        $this->session->set_userdata('subsub_menu', 'reports/expense/expensegroup');
        
        if (isset($_POST['search_type'])) {
            $search_type = $this->input->post("search_type");
        } else {
            $search_type = "";
        }

        $data["searchlist"]  = $this->search_type;
        $data["search_type"] = $search_type;
        $data['head_id']     = $head_id     = "";
        if (isset($_POST['head']) && $_POST['head'] != '') {
            $data['head_id'] = $head_id = $_POST['head'];
        }
        $data['fields'] = $this->customfield_model->get_custom_fields('expenses', '', '', 1);

        $result              = $this->expense_model->searchexpensegroup($search_type, $head_id);
        $data['headlist']    = $this->expensehead_model->get();
        $data['expenselist'] = $result;
        $data['module'] = 'finance';
        $this->load->view('layout/header', $data);
        $this->load->view('admin/expense/groupexpenseReport', $data);
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

    /* this function is used to get expense group report by using datatable */

    public function dtexpensegroupreport()
    {
        $search_type = $this->input->post('search_type');
        $date_from   = $this->input->post('date_from');
        $date_to     = $this->input->post('date_to');
        $head        = $this->input->post('head');

        $fields = $this->customfield_model->get_custom_fields('expenses', '', '', 1);
        if (isset($_POST['search_type']) && $_POST['search_type'] != '') {
            $dates               = $this->customlib->get_betweendate($_POST['search_type']);
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

        $result = $this->expense_model->getexpensereport($start_date, $end_date, $head_id);

        $m               = json_decode($result);
        $currency_symbol = $this->customlib->getHospitalCurrencyFormat();
        $dt_data         = array();
        $grand_total     = 0;
        if (!empty($m->data)) {
            foreach ($m->data as $key => $value) {
                $expense_head[$value->exp_head_id][] = $value;
            }

            $grd_total  = 0;
            $exphead_id = 0;
            $count      = 0;
            foreach ($m->data as $key => $value) {

                $exp_head_id  = $value->exp_head_id;
                $total_amount = "<b>" . $value->amount . "</b>";
                $grd_total += $value->amount;
                $row = array();

                if ($exphead_id == $exp_head_id) {
                    $row[] = "";
                    $count++;
                } else {
                    $row[] = $value->exp_category;
                    $count = 0;
                }

                $row[] = $value->id;
                $row[] = $value->name;
                $row[] = $this->customlib->YYYYMMDDTodateFormat($value->date);
                $row[] = $value->invoice_no;

                if (!empty($fields)) {
                    foreach ($fields as $fields_key => $fields_value) {

                        $display_field = $value->{"$fields_value->name"};
                        if ($fields_value->type == "link") {
                            $display_field = "<a href=" . $value->{"$fields_value->name"} . " target='_blank'>" . $value->{"$fields_value->name"} . "</a>";

                        }
                        $row[] = $display_field;
                    }
                }

                $row[]      = $value->amount;
                $dt_data[]  = $row;
                $exphead_id = $value->exp_head_id;
                $sub_total  = 0;
                if ($count == (count($expense_head[$value->exp_head_id]) - 1)) {
                    foreach ($expense_head[$value->exp_head_id] as $exp_headkey => $exp_headvalue) {
                        $sub_total += $exp_headvalue->amount;
                    }
                    $amount_row   = array();
                    $amount_row[] = "";
                    $amount_row[] = "";
                    $amount_row[] = "";
                    $amount_row[] = "";
                    if (!empty($fields)) {
                foreach ($fields as $fields_key => $fields_value) {

                    $amount_row[] = "";
                }
            }
                    $amount_row[] = "";
                    $amount_row[] = "<b>" . $this->lang->line('subtotal') .': '.$currency_symbol . $sub_total . "</b>";
                    $dt_data[]    = $amount_row;
                }

            }

            $grand_total  = "<b>" . $currency_symbol . $grd_total . "</b>";
            $footer_row   = array();
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
            $footer_row[] = "<b>" . $this->lang->line('total') .": ". $grand_total ."</b>";
           
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
