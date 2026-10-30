<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Birthordeath extends Admin_Controller
{
	public $search_type;
	public $time_format;


    public function __construct()
    {
        parent::__construct();
        $this->config->load("payroll");
        $this->config->load("image_valid");
        $this->load->library("datatables");
        $this->load->library('form_validation');
        $this->load->library('Customlib');
        $this->load->library('system_notification');
        $this->load->library('SaasValidation');
        $this->load->helper('customfield_helper');
        $this->search_type = $this->config->item('search_type');
        $this->time_format = $this->customlib->getHospitalTimeFormat();
    }

    public function index()
    {
        if (!$this->rbac->hasPrivilege('birth_record', 'can_view')) {
            access_denied();
        }

        $this->session->set_userdata('top_menu', 'birthordeath');
        $this->session->set_userdata('sub_menu', 'birthordeath/index');
        $this->session->set_userdata('subsub_menu', ''); 
        
        $this->load->helper('customfield_helper');
        $data['fields']         = $this->customfield_model->get_custom_fields('birth_report', 1);
        $patients               = $this->patient_model->getchildMother();
        $data["patients"]       = $patients;
        $data["disable_option"] = false;
        $data['module'] = 'birth_death_record';
        $this->load->view("layout/header", $data);
        $this->load->view("admin/birthordeath/birthReport", $data);
        $this->load->view("layout/footer", $data);
    }

    public function getpatientBycaseId($case_reference_id)
    {
        $patient = $this->patient_model->getDetailsByCaseId($case_reference_id);
        $status = 0;

        if (!empty($patient['patient_id'])) {
            if ($patient['gender'] == 'Female') {
                $status        = 1;
                $patient_id    = $patient['patient_id'];
                $patient_name  = composePatientName($patient['patient_name'], $patient_id);
                $guardian_name = $patient['guardian_name'];
                $message       = $this->lang->line("patient_is_female");
            } else {
                $status        = 1;
                $patient_id    = $patient['patient_id'];
                $patient_name  = composePatientName($patient['patient_name'], $patient_id);
                $guardian_name = $patient['guardian_name'];
                $message       = $this->lang->line("patient_is_male");
            }
        } else {
            $status        = 0;
            $patient_id    = 0;
            $patient_name  = "";
            $guardian_name = "";
            $message       = $this->lang->line("patient_not_found");
        }

        echo json_encode(array('status' => $status, 'patient_id' => $patient_id, 'patient_name' => $patient_name, 'gender' => $patient['gender'], 'message' => $message, 'guardian_name' => $guardian_name));
    }

    public function getdeathpatientBycaseId($case_reference_id)
    {
        $patient = $this->patient_model->getDetailsByCaseId($case_reference_id);
        $status  = 0;

        if (!empty($patient['patient_id'])) {
            $status        = 1;
            $patient_id    = $patient['patient_id'];
            $patient_name  = composePatientName($patient['patient_name'], $patient_id);
            $guardian_name = $patient['guardian_name'];
            $message       = "";
        } else {
            $status        = 0;
            $patient_id    = 0;
            $patient_name  = "";
            $guardian_name = "";
            $message       = $this->lang->line("patient_not_found");
        }

        echo json_encode(array('status' => $status, 'patient_id' => $patient_id, 'patient_name' => $patient_name, 'gender' => $patient['gender'], 'message' => $message, 'guardian_name' => $guardian_name));
    }

    public function getbirthDatatable()
    {
        $dt_response = $this->birthordeath_model->getAllbirthRecord();
        $fields      = $this->customfield_model->get_custom_fields('birth_report', 1);
        $dt_response = json_decode($dt_response);
        $dt_data     = array();
        if (!empty($dt_response->data)) {
            foreach ($dt_response->data as $key => $value) {

                $row    = array();
                $action = '';
                //====================================

                $action .= "<a href='#' onclick='viewDetail(" . $value->id . ")' class='btn btn-sm btn-outline-secondary' data-bs-toggle='tooltip' data-bs-placement='top' title='" . $this->lang->line('show') . "'><i class='fa fa-reorder'></i></a>";

                if ($this->rbac->hasPrivilege('birth_record', 'can_edit')) {
                    $action .= "<a href='#' onclick='getRecord(" . $value->id . ")' class='btn btn-sm btn-outline-primary' data-bs-toggle='tooltip' data-bs-placement='top' title='" . $this->lang->line('edit') . "'><i class='fa fa-pencil'></i></a>";
                }

                if ($this->rbac->hasPrivilege('birth_record', 'can_delete')) {
                    $action .= "<a href='#' onclick='delete_bill(" . $value->id . ")' class='btn btn-sm btn-outline-danger' data-bs-toggle='tooltip' data-bs-placement='top' title='" . $this->lang->line('delete') . "'><i class='fa fa-trash'></i></a>";
                }

                if($value->gender){
                    $gender = $this->lang->line(strtolower($value->gender));
                }else{
                    $gender = '';
                }

                //==============================
                $row[] = $this->customlib->getSessionPrefixByType('birth_record_reference_no') . $value->id;
                $row[] = html_escape($value->case_reference_id);
                $row[] = composeStaffNameByString($value->generated_byname, $value->generated_bysurname, $value->generated_byemployee_id);
                $row[] = html_escape($value->child_name);
                $row[] = $gender;
                $row[] = $this->customlib->YYYYMMDDHisTodateFormat($value->birth_date, $this->time_format);
                $row[] = ($value->patient_name != "") ? html_escape($value->patient_name) . " (" . html_escape($value->mother_id) . ")" : "";
                $row[] = html_escape($value->father_name);

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
                $row[]     = html_escape($value->birth_report);
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

    public function getdeathDatatable()
    {
        $dt_response = $this->birthordeath_model->getAlldeathRecord();
        $fields      = $this->customfield_model->get_custom_fields('death_report', 1);
        $dt_response = json_decode($dt_response);
        $dt_data     = array();
        if (!empty($dt_response->data)) {
            foreach ($dt_response->data as $key => $value) {

                $row    = array();
                $action = '';
                //====================================

                $action .= "<a href='#' onclick='viewDetail(" . $value->id . ")' class='btn btn-sm btn-outline-secondary' data-bs-toggle='tooltip' data-bs-placement='top' title='" . $this->lang->line('show') . "'><i class='fa fa-reorder'></i></a>";

                if ($value->attachment != "") {
                    $action .= "<a href='" . site_url('admin/birthordeath/download_deathrecord/' . $value->id) . "' class='btn btn-sm btn-outline-secondary' data-bs-toggle='tooltip' data-bs-placement='top' title='" . $this->lang->line('attachment') . "'><i class='fa fa-download'></i></a>";
                }

                if ($this->rbac->hasPrivilege('death_record', 'can_edit')) {
                    $action .= "<a href='#' onclick='getRecord(" . $value->id . ")' class='btn btn-sm btn-outline-primary' data-bs-toggle='tooltip' data-bs-placement='top' title='" . $this->lang->line('edit') . "'><i class='fa fa-pencil'></i></a>";
                }

                if ($this->rbac->hasPrivilege('death_record', 'can_delete')) {
                    $action .= "<a href='#' onclick='delete_bill(" . $value->id . ")' class='btn btn-sm btn-outline-danger' data-bs-toggle='tooltip' data-bs-placement='top' title='" . $this->lang->line('delete') . "'><i class='fa fa-trash'></i></a>";
                }

                if($value->gender){
                    $gender =   $this->lang->line(strtolower($value->gender));
                }else{
                    $gender =   '';
                }

                //==============================
                $row[] = $this->customlib->getSessionPrefixByType('death_record_reference_no') . $value->id;
                $row[] = html_escape($value->case_reference_id);
                $row[] = composeStaffNameByString($value->generated_byname, $value->generated_bysurname, $value->generated_byemployee_id);
                $row[] = html_escape($value->patient_name) . " (" . html_escape($value->patientid) . ")";
                $row[] = html_escape($value->guardian_name);
                $row[] = $gender;
                $row[] = $this->customlib->YYYYMMDDHisTodateFormat($value->death_date, $this->time_format);

                //==============================

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

                $row[]     = html_escape($value->death_report);
                $row[]     = "<div class='d-flex flex-wrap justify-content-end gap-1'>" . $action . "</div>";
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

    public function edit()
    {
        if (!$this->rbac->hasPrivilege('birth_record', 'can_edit')) {
            access_denied();
        }
        $id = $this->input->get("id", TRUE);
        $this->load->helper('customfield_helper');
        $birthrecord                        = $this->birthordeath_model->getDetails($id);
        $birthrecord["birth_date"]          = $this->customlib->YYYYMMDDHisTodateFormat($birthrecord['birth_date'], $this->time_format);        
        $birthrecord['custom_fields_value'] = display_custom_fields('birth_report', $id);        
        $cutom_fields_data                  = get_custom_table_values($id, 'birth_report');
        $birthrecord['field_data']          = $cutom_fields_data;
        echo json_encode($birthrecord);
    }

    public function getBirthdata()
    {
        $id = $this->input->post("id", TRUE);
        $this->load->helper('customfield_helper');
        $custom_fields_data        = get_custom_table_values($id, 'birth_report');
        $birthrecord               = $this->birthordeath_model->getDetails($id);	 
        $birthrecord["birth_date"] = $this->customlib->YYYYMMDDHisTodateFormat($birthrecord['birth_date'], $this->time_format);
        $birthrecord["gender"]      =  $this->lang->line(strtolower($birthrecord['gender']));        
        $birthrecord['field_data'] = $custom_fields_data;
        $birthrecord['child_pic'] = $this->media_storage->getImageURL($birthrecord['child_pic']);
        $birthrecord['mother_pic'] = $this->media_storage->getImageURL($birthrecord['mother_pic']);
        $birthrecord['father_pic'] = $this->media_storage->getImageURL($birthrecord['father_pic']);
        echo json_encode($birthrecord);
    }

    public function getBirthprintDetails($id)
    {
        if (!$this->rbac->hasPrivilege('birth_record', 'can_view')) {
            access_denied();
        }
        $print_details         = $this->printing_model->get('', 'birth');
        $data["print_details"] = $print_details;
        $data['id']            = $id;
        if ($this->input->post('print')) {
            $data["print"] = 'yes';
        } else {
            $data["print"] = 'no';
        }
        $data['fields'] = $this->customfield_model->get_custom_fields('birth_report', '', 1, '');
        $result         = $this->birthordeath_model->getDetails($id);
        $data['result'] = $result;

        $this->load->view('admin/birthordeath/printBirth', $data);
    }

    public function getDeathprintDetails($id)
    {
        if (!$this->rbac->hasPrivilege('death_record', 'can_view')) {
            access_denied();
        }

        $print_details         = $this->printing_model->get('', 'death');
        $data["print_details"] = $print_details;
        $data['id']            = $id;
        if ($this->input->post('print')) {
            $data["print"] = 'yes';
        } else {
            $data["print"] = 'no';
        }
        $data["prefix"] = $this->customlib->getSessionPrefixByType('death_record_reference_no');
        $data['fields'] = $this->customfield_model->get_custom_fields('death_report', '', 1);
        $result         = $this->birthordeath_model->getDeDetails($id);
        $data['result'] = $result;
        $this->load->view('admin/birthordeath/printDeath', $data);
    }

    public function deathreport()
    {
        if (!$this->rbac->hasPrivilege('death_record', 'can_view')) {
            access_denied();
        }

        $this->session->set_userdata('top_menu', 'Reports');
        $this->session->set_userdata('sub_menu', 'reports/birth_death');
        $this->session->set_userdata('subsub_menu', 'admin/birthordeath/deathreport');        
        $custom_fields = $this->customfield_model->get_custom_fields('death_report', '', '', 1);
        $data["searchlist"] = $this->search_type;
        $data["fields"]     = $custom_fields;
        $data['gender']     = $this->customlib->getGender_Patient();
        $data['module'] = 'reports';
        $this->load->view('layout/header', $data);
        $this->load->view('admin/deathreport/deathreport', $data);
        $this->load->view('layout/footer', $data);
    }

    public function birthreport()
    {
        if (!$this->rbac->hasPrivilege('birth_record', 'can_view')) {
            access_denied();
        }

        $this->session->set_userdata('top_menu', 'Reports');
        $this->session->set_userdata('sub_menu', 'reports/birth_death');
        $this->session->set_userdata('subsub_menu', 'admin/birthordeath/birthreport');        
        $custom_fields      = $this->customfield_model->get_custom_fields('birth_report', '', '', 1);
        $data["searchlist"] = $this->search_type;
        $data["fields"]     = $custom_fields;
        $data["genderlist"] = $this->customlib->getGender_Patient();
        $data['module'] = 'reports';
        $this->load->view('layout/header', $data);
        $this->load->view('admin/birthreport/birthreport', $data);
        $this->load->view('layout/footer', $data);
    }

    public function checkvalidation()
    {         
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
                'gender'      => $this->input->post('gender', TRUE),
            );

            $json_array = array('status' => 'success', 'error' => '', 'param' => $param, 'message' => $this->lang->line('success_message'));
        }
        echo json_encode($json_array);
    }

    public function birthreports()
    {
        $search['search_type'] = $this->input->post('search_type', TRUE);
        $search['date_from']   = $this->input->post('date_from', TRUE);
        $search['date_to']     = $this->input->post('date_to', TRUE);
        $gender                = $this->input->post('gender', TRUE);
        $start_date            = '';
        $end_date              = '';
        $fields                = $this->customfield_model->get_custom_fields('birth_report', '', '', 1);
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

        $reportdata = $this->report_model->birthRecord($start_date, $end_date, $gender);

        $reportdata = json_decode($reportdata);
        $dt_data    = array();
        if (!empty($reportdata->data)) {
            foreach ($reportdata->data as $key => $value) {
                $row   = array();
                $row[] = $this->customlib->getSessionPrefixByType('birth_record_reference_no') . $value->id;
                $row[] = html_escape($value->case_reference_id);
                $row[] = html_escape($value->child_name);
                $row[] = $this->lang->line(strtolower($value->gender));
                $row[] = $this->customlib->YYYYMMDDHisTodateFormat($value->birth_date, $this->time_format);
                $row[] = html_escape($value->weight);
                $row[] = composePatientName($value->patient_name, $value->mother_id);
                $row[] = html_escape($value->father_name);
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
                $row[]     = html_escape($value->birth_report);
                $dt_data[] = $row;
            }
        }

        $json_data = array(
            "draw"            => intval($reportdata->draw),
            "recordsTotal"    => intval($reportdata->recordsTotal),
            "recordsFiltered" => intval($reportdata->recordsFiltered),
            "data"            => $dt_data,
        );
        echo json_encode($json_data);
    }

    public function deathreports()
    {
        $search['search_type'] = $this->input->post('search_type', TRUE);
        $search['date_from']   = $this->input->post('date_from', TRUE);
        $search['date_to']     = $this->input->post('date_to', TRUE);
        $start_date            = '';
        $end_date              = '';
        $gender                = $this->input->post('gender', TRUE);
        $fields                = $this->customfield_model->get_custom_fields('death_report', '', '', 1);
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

        $reportdata = $this->report_model->deathRecord($start_date, $end_date, $gender);
        $reportdata = json_decode($reportdata);
        $dt_data    = array();
        if (!empty($reportdata->data)) {
            foreach ($reportdata->data as $key => $value) {
                $row = array();

                $row[] = $this->customlib->getSessionPrefixByType('death_record_reference_no') . $value->id;
                $row[] = html_escape($value->case_reference_id);
                $row[] = html_escape($value->guardian_name);
                $row[] = $this->customlib->YYYYMMDDHisTodateFormat($value->death_date, $this->time_format);
                $row[] = composePatientName($value->patient_name, $value->patient_id);
                $row[] = ($value->gender) ? $this->lang->line(strtolower($value->gender)):"";
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
                $row[] = html_escape($value->death_report);
                $dt_data[] = $row;
            }
        }

        $json_data = array(
            "draw"            => intval($reportdata->draw),
            "recordsTotal"    => intval($reportdata->recordsTotal),
            "recordsFiltered" => intval($reportdata->recordsFiltered),
            "data"            => $dt_data,
        );
        echo json_encode($json_data);
    }

    public function getDeathdata()
    {
        if (!$this->rbac->hasPrivilege('death_record', 'can_view')) {
            access_denied();
        }
        $id = $this->input->post("id", TRUE);
        $this->load->helper('customfield_helper');
        $cutom_fields_data         = get_custom_table_values($id, 'death_report');
        $deathrecord               = $this->birthordeath_model->getDeDetails($id);		
		$deathrecord["age"] 		= $this->customlib->get_patient_current_age($deathrecord['patient_id']);		 
        $deathrecord["prefix"]     = $this->customlib->getSessionPrefixByType('death_record_reference_no');
        $deathrecord["death_date"] = $this->customlib->YYYYMMDDHisTodateFormat($deathrecord['death_date'], $this->time_format);        
        $deathrecord["gender"] = $this->lang->line(strtolower($deathrecord['gender']));        
        $deathrecord['field_data'] = $cutom_fields_data;
        echo json_encode($deathrecord);
    }

    public function editDeath()
    {
        if (!$this->rbac->hasPrivilege('death_record', 'can_view')) {
            access_denied();
        }
        $id = $this->input->post("id", TRUE);
        $this->load->helper('customfield_helper');
        $deathrecord = $this->birthordeath_model->getDeDetails($id);
        $deathrecord["death_date"]          = $this->customlib->YYYYMMDDHisTodateFormat($deathrecord['death_date'], $this->time_format);
        $deathrecord['custom_fields_value'] = display_custom_fields('death_report', $id);
        $cutom_fields_data                  = get_custom_table_values($id, 'death_report');
        $deathrecord['field_data']          = $cutom_fields_data;

        echo json_encode($deathrecord);
    }

    public function death()
    {
        if (!$this->rbac->hasPrivilege('death_record', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'birthordeath');
        $this->session->set_userdata('sub_menu', 'birthordeath/death');
        $this->session->set_userdata('subsub_menu', '');        

        $patients         = $this->patient_model->getPatientListall();
        $data["patients"] = $patients;
        $data['fields']   = $this->customfield_model->get_custom_fields('death_report', 1);
        $data['module'] = 'birth_death_record';
        $this->load->view("layout/header", $data);
        $this->load->view("admin/birthordeath/deathReport", $data);
        $this->load->view("layout/footer", $data);
    }

    public function addDeathdata()
    {
        if (!$this->rbac->hasPrivilege('death_record', 'can_add')) {
            access_denied();
        }

        $this->form_validation->set_rules('patient', $this->lang->line('patient_name'), 'trim|required|xss_clean');			
		$this->form_validation->set_rules('case_id', $this->lang->line('case_id'), array('required', 'xss_clean',
            array('check_exists', array($this->birthordeath_model, 'valid_case_id')),
        )
        );

        $this->form_validation->set_rules('death_date', $this->lang->line('death_date'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('guardian_name', $this->lang->line('guardian_name'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('document', $this->lang->line('document'), 'callback_handle_doc_upload[document]|callback_validateCanUploadFile[document]');
        $custom_fields = $this->customfield_model->getByBelong('death_report');
        foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
            if ($custom_fields_value['validation']) {
                $custom_fields_id   = $custom_fields_value['id'];
                $custom_fields_name = $custom_fields_value['name'];
                $this->form_validation->set_rules("custom_fields[death_report][" . $custom_fields_id . "]", $custom_fields_name, 'trim|required|xss_clean');
            }
        }
        if ($this->form_validation->run() == false) {
            $msg = array(
                'patient'       => form_error('patient'),
                'case_id'       => form_error('case_id'),
                'death_date'    => form_error('death_date'),
                'guardian_name' => form_error('guardian_name'),
                'document'      => form_error('document'),
            );

            if (!empty($custom_fields)) {
                foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
                    if ($custom_fields_value['validation']) {
                        $custom_fields_id                                                     = $custom_fields_value['id'];
                        $custom_fields_name                                                   = $custom_fields_value['name'];
                        $error_msg2["custom_fields[death_report][" . $custom_fields_id . "]"] = form_error("custom_fields[death_report][" . $custom_fields_id . "]");
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

            $custom_field_post  = $this->input->post("custom_fields[death_report]", TRUE);
            $custom_value_array = array();
            if (!empty($custom_field_post)) {
                foreach ($custom_field_post as $key => $value) {
                    $check_field_type = $this->input->post("custom_fields[death_report][" . $key . "]", TRUE);
                    $field_value      = is_array($check_field_type) ? implode(",", $check_field_type) : $check_field_type;
                    $array_custom     = array(
                        'belong_table_id' => 0,
                        'custom_field_id' => $key,
                        'field_value'     => $field_value,
                    );
                    $custom_value_array[] = $array_custom;
                }
            }
            $deathdate         = $this->input->post('death_date', TRUE);
            $death_date        = $this->customlib->dateFormatToYYYYMMDDHis($deathdate, $this->time_format);
            $case_reference_id = $this->input->post('case_id', TRUE);
            if (empty($case_reference_id)) {
                $case_reference_id = null;
            }
            $death_data = array(
                'patient_id'        => $this->input->post('patient', TRUE),
                'guardian_name'     => $this->input->post('guardian_name', TRUE),
                'case_reference_id' => $case_reference_id,
                'death_date'        => $death_date,
                'death_report'      => $this->input->post('death_report', TRUE),
                'generated_by'           => $this->customlib->getStaffID(),
                'is_active'         => 'yes',
            );
            $insert_id = $this->birthordeath_model->addDeathdata($death_data);
            if (!empty($custom_value_array)) {
                $this->customfield_model->insertRecord($custom_value_array, $insert_id);
            }
            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));

            if (isset($_FILES["document"]) && !empty($_FILES['document']['name'])) {
                $file_name = $this->media_storage->fileupload("document",'./uploads/death_image/');
                $data_img = array('id' => $insert_id, 'attachment' => $file_name, 'attachment_name' => $_FILES["document"]["name"]);
                $this->birthordeath_model->addDeathdata($data_img);

                // SaaS: add the uploaded document size to the storage quota usage.
                try {
                    $saas_quota_result = $this->saasvalidation->updateStorageLimit('storage', ['document']);
                    // Silent-failure capture: updateStorageLimit does not throw on API
                    // rejection — it returns a JSON string with status:false instead.
                    if (is_string($saas_quota_result)) {
                        $saas_decoded = json_decode($saas_quota_result, true);
                        if (isset($saas_decoded['status']) && $saas_decoded['status'] === false) {
                            log_message('error', 'SaaS storage quota update returned failure (birthordeath addDeathdata): ' . $saas_quota_result);
                        }
                    }
                } catch (Exception $e) {
                    log_message('error', 'SaaS storage quota update failed (birthordeath addDeathdata): ' . $e->getMessage());
                }
            }

            //update death status in patient table
            $patient_data = array('id' => $this->input->post('patient', TRUE), 'is_dead' => 'yes');
            $this->patient_model->add($patient_data);

            $event_data = array(
                'patient_id' => $this->input->post('patient', TRUE),
                'death_date' => $this->customlib->YYYYMMDDHisTodateFormat($death_date, $this->time_format),
                'case_id'    => $case_reference_id,
            );

            $this->system_notification->send_system_notification('add_death_record', $event_data);
        }
        echo json_encode($array);
    }

    /**
     * SaaS storage pre-check (form_validation callback).
     * Returns false (blocking the save) when the combined size of the uploaded
     * birth files would push the tenant over its storage quota. The param string
     * is a comma-separated list of file fields; SaasValidation sets the message.
     */
    public function validateCanUploadFile($str, $params_string)
    {
        $storage_array = array_map('trim', explode(',', $params_string));
        return $this->saasvalidation->validateCanUploadFile($str, $storage_array);
    }

    /**
     * SaaS: on a birth-file replace (edit), adjust the storage quota by the size
     * difference (old vs new) and delete the replaced physical file.
     *
     * Only a previously-uploaded birth file is counted/removed. The shared
     * "no_image" placeholder (uploads/patient_images/no_image.png) and an empty
     * column never consumed quota and must NOT be subtracted or deleted — hence
     * the uploads/birth_image/ prefix guard. uses updateResouceQuota/
     * deleteResouceQuota (both throw on API failure), so a plain catch suffices.
     *
     * @param string $field      $_FILES key of the newly uploaded file
     * @param string $old_value  prior column value (full relative path or placeholder)
     */
    private function meterBirthFileReplace($field, $old_value)
    {
        $is_real_old = (!empty($old_value) && strpos($old_value, 'uploads/birth_image/') === 0);
        $old_kb = $is_real_old ? $this->media_storage->getUploadedFileSize($old_value, '') : 0;
        $new_kb = $this->media_storage->getTmpFileSize($field);

        try {
            if ($old_kb > $new_kb) {
                $this->saasvalidation->deleteResouceQuota('storage', $old_kb - $new_kb);
            } elseif ($new_kb > $old_kb) {
                $this->saasvalidation->updateResouceQuota('storage', $new_kb - $old_kb);
            }
        } catch (Exception $e) {
            log_message('error', 'SaaS storage quota update failed (birthordeath update_birth ' . $field . '): ' . $e->getMessage());
        }

        // Remove the orphaned old file (the new upload uses a unique name, so it
        // never overwrote the old one). Skip the shared placeholder.
        if ($is_real_old) {
            $this->media_storage->filedelete(basename($old_value), dirname($old_value));
        }
    }

    /**
     * SaaS: on a birth-record delete, release one file's storage from the quota
     * and remove the physical file. Only a real uploaded birth file is acted on
     * (uploads/birth_image/ prefix) — the shared "no_image" placeholder and empty
     * columns never consumed quota and must not be released or deleted. Uses the
     * throwing deleteResouceQuota, so a plain catch suffices (no silent-failure).
     *
     * @param string $value column value (full relative path or placeholder)
     */
    private function releaseBirthFileQuota($value)
    {
        if (empty($value) || strpos($value, 'uploads/birth_image/') !== 0) {
            return;
        }
        // Read size before unlinking the file.
        $kb = $this->media_storage->getUploadedFileSize($value, '');
        if ($kb > 0) {
            try {
                $this->saasvalidation->deleteResouceQuota('storage', $kb);
            } catch (Exception $e) {
                log_message('error', 'SaaS storage quota release failed (birthordeath delete): ' . $e->getMessage());
            }
        }
        $this->media_storage->filedelete(basename($value), dirname($value));
    }

   public function addBirthdata()
    {
        if (!$this->rbac->hasPrivilege('birth_record', 'can_add')) {
            access_denied();
        }

        $custom_fields = $this->customfield_model->getByBelong('birth_report');

        foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
            if ($custom_fields_value['validation']) {
                $custom_fields_id   = $custom_fields_value['id'];
                $custom_fields_name = $custom_fields_value['name'];
                $this->form_validation->set_rules("custom_fields[birth_report][" . $custom_fields_id . "]", $custom_fields_name, 'trim|required|xss_clean');
            }
        }
        $this->form_validation->set_rules('child_name', $this->lang->line('child_name'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('mother', $this->lang->line('mother_name'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('contact', $this->lang->line('phone'), 'trim|numeric|xss_clean');
        $this->form_validation->set_rules('birth_date', $this->lang->line('birth_date'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('gender', $this->lang->line('gender'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('weight', $this->lang->line('weight'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('first_img', $this->lang->line('image'), 'callback_handle_upload[first_img]|callback_validateCanUploadFile[first_img,second_img,child_img,document]');
        $this->form_validation->set_rules('second_img', $this->lang->line('image'), 'callback_handle_upload[second_img]');
        $this->form_validation->set_rules('child_img', $this->lang->line('image'), 'callback_handle_upload[child_img]');
        $this->form_validation->set_rules('document', $this->lang->line('image'), 'callback_handle_doc_upload[document]');

        if ($this->form_validation->run() == false) {
            $msg = array(
                'child_name' => form_error('child_name'),
                'birth_date' => form_error('birth_date'),
                'first_img'  => form_error('first_img'),
                'second_img' => form_error('second_img'),
                'child_img'  => form_error('child_img'),
                'document'   => form_error('document'),
                'mother'     => form_error('mother'),
                'gender'     => form_error('gender'),
                'weight'     => form_error('weight'),
                'contact'    => form_error('contact'),
            );

            if (!empty($custom_fields)) {
                foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
                    if ($custom_fields_value['validation']) {
                        $custom_fields_id                                                     = $custom_fields_value['id'];
                        $custom_fields_name                                                   = $custom_fields_value['name'];
                        $error_msg2["custom_fields[birth_report][" . $custom_fields_id . "]"] = form_error("custom_fields[birth_report][" . $custom_fields_id . "]");
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
            $custom_field_post = $this->input->post("custom_fields[birth_report]", TRUE);

            $custom_value_array = array();
            if (!empty($custom_field_post)) {
                foreach ($custom_field_post as $key => $value) {
                    $check_field_type = $this->input->post("custom_fields[birth_report][" . $key . "]", TRUE);
                    $field_value      = is_array($check_field_type) ? implode(",", $check_field_type) : $check_field_type;
                    $array_custom     = array(
                        'belong_table_id' => 0,
                        'custom_field_id' => $key,
                        'field_value'     => $field_value,
                    );
                    $custom_value_array[] = $array_custom;
                }
            }

            $birthdate         = $this->input->post('birth_date', TRUE);
            $birth_date        = $this->customlib->dateFormatToYYYYMMDDHis($birthdate, $this->time_format);
            $ref_year          = date('Y', strtotime($birthdate));
            $case_reference_id = null;
            if ($this->input->post('case_id', TRUE) != "") {
                $case_reference_id = $this->input->post('case_id', TRUE);
            }
            $birth_data = array(
                'case_reference_id' => $case_reference_id,
                'child_name'        => $this->input->post('child_name', TRUE),
                'birth_date'        => $birth_date,
                'weight'            => $this->input->post('weight', TRUE),
                'patient_id'        => $this->input->post('mother_name', TRUE),
                'contact'           => $this->input->post('contact', TRUE),
                'birth_report'      => $this->input->post('birth_report', TRUE),
                'father_name'       => $this->input->post('father_name', TRUE),
                'gender'            => $this->input->post('gender', TRUE),
                'address'           => $this->input->post('address', TRUE),
                'generated_by'           => $this->customlib->getStaffID(),
                'is_active'         => 'yes',
            );
            $insert_id = $this->birthordeath_model->addBirthdata($birth_data);
            if ($insert_id) {

                if (!empty($custom_value_array)) {
                    $this->customfield_model->insertRecord($custom_value_array, $insert_id);
                }  

                //new file upload function
                if (isset($_FILES["first_img"]) && !empty($_FILES['first_img']['name'])) {
                    $uploaddir = FCPATH.'uploads/birth_image/' . $insert_id . '/';
                    if (!is_dir($uploaddir)) {
                        mkdir($uploaddir, 0777, true);
                    }
                    $filename = $this->media_storage->fileupload("first_img",'./uploads/birth_image/' . $insert_id . '/');
                    $mother_pic  = 'uploads/birth_image/' . $insert_id . '/' . $filename;
                }else {
                    $mother_pic = "uploads/patient_images/no_image.png";
                }

                if (isset($_FILES["second_img"]) && !empty($_FILES['second_img']['name'])) {
                    $uploaddir = FCPATH.'uploads/birth_image/' . $insert_id . '/';
                    if (!is_dir($uploaddir)) {
                        mkdir($uploaddir, 0777, true);
                    }
                    $filename = $this->media_storage->fileupload("second_img",'./uploads/birth_image/' . $insert_id . '/');
                    $father_pic  = 'uploads/birth_image/' . $insert_id . '/' . $filename;
                }else {
                   $father_pic = "uploads/patient_images/no_image.png";
                }

                if (isset($_FILES["child_img"]) && !empty($_FILES['child_img']['name'])) {
                    $uploaddir = FCPATH.'uploads/birth_image/' . $insert_id . '/';
                    if (!is_dir($uploaddir)) {
                        mkdir($uploaddir, 0777, true);
                    }
                    $filename = $this->media_storage->fileupload("child_img",'./uploads/birth_image/' . $insert_id . '/');
                    $child_pic  = 'uploads/birth_image/' . $insert_id . '/' . $filename;
                }else {
                    $child_pic = "uploads/patient_images/no_image.png";
                }

                if (isset($_FILES["document"]) && !empty($_FILES['document']['name'])) {
                    $uploaddir = FCPATH.'uploads/birth_image/' . $insert_id . '/';
                    if (!is_dir($uploaddir)) {
                        mkdir($uploaddir, 0777, true);
                    }
                    $filename = $this->media_storage->fileupload("document",'./uploads/birth_image/' . $insert_id . '/');
                    $document  = 'uploads/birth_image/' . $insert_id . '/' . $filename;
                }else {
                    $document = "";
                }
                //new file upload function

                $data_img = array('id' => $insert_id, 'mother_pic' => $mother_pic, 'father_pic' => $father_pic, 'document' => $document, 'child_pic' => $child_pic);
                $this->birthordeath_model->addBirthdata($data_img);

                // SaaS: add the size of every uploaded birth file to the storage quota
                // usage in one call. updateStorageLimit sums $_FILES sizes (which survive
                // move_uploaded_file) across all listed fields and skips absent ones.
                try {
                    $saas_quota_result = $this->saasvalidation->updateStorageLimit('storage', ['first_img', 'second_img', 'child_img', 'document']);
                    // Silent-failure capture: updateStorageLimit does not throw on API
                    // rejection — it returns a JSON string with status:false instead.
                    if (is_string($saas_quota_result)) {
                        $saas_decoded = json_decode($saas_quota_result, true);
                        if (isset($saas_decoded['status']) && $saas_decoded['status'] === false) {
                            log_message('error', 'SaaS storage quota update returned failure (birthordeath addBirthdata): ' . $saas_quota_result);
                        }
                    }
                } catch (Exception $e) {
                    log_message('error', 'SaaS storage quota update failed (birthordeath addBirthdata): ' . $e->getMessage());
                }
            }

            $event_data = array(
                'mother_id'  => $this->input->post('mother_name', TRUE),
                'child_name' => $this->input->post('child_name', TRUE),
                'birth_date' => $this->customlib->dateFormatToYYYYMMDDHis($this->input->post('birth_date', TRUE), $this->customlib->getHospitalTimeFormat()),
                'case_id'    => $this->input->post('case_id', TRUE),
            );

            $this->system_notification->send_system_notification('add_birth_record', $event_data);
            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
        }
        echo json_encode($array);
    }

    public function download($id)
    {
        $getbirthdata = $this->birthordeath_model->getBirthData($id);
        $this->media_storage->filedownload($getbirthdata['document'],'./');
    }

    public function download_deathrecord($id)
    {
        $death = $this->birthordeath_model->getDeDetails($id);
        $this->media_storage->filedownload($death['attachment'],'./uploads/death_image/');
    }

    public function image_upload()
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
                    $this->form_validation->set_message('image_upload', 'Error While Uploading patient Image');
                    return false;
                }
                if (!in_array($ext, $allowed_extension) || !in_array($file_type, $allowed_mime_type)) {
                    $this->form_validation->set_message('image_upload', 'Extension Error While Uploading patient Image');
                    return false;
                }
                if ($file_size > $image_validate['upload_size']) {
                    $this->form_validation->set_message('image_upload', $this->lang->line('file_size_shoud_be_less_than') . number_format($image_validate['upload_size'] / 1048576, 2) . " MB");
                    return false;
                }
            } else {
                $this->form_validation->set_message('image_upload', "File Type / Extension Error Uploading Image");
                return false;
            }
            return true;
        }
        return true;
    }

    public function handle_upload($str, $var)
    {
        $image_validate = $this->config->item('image_validate');
        if (isset($_FILES[$var]) && !empty($_FILES[$var]['name'])) {
            $file_type         = $_FILES[$var]['type'];
            $file_size         = $_FILES[$var]["size"];
            $file_name         = $_FILES[$var]["name"];
            $allowed_extension = $image_validate['allowed_extension'];
            $ext               = pathinfo($file_name, PATHINFO_EXTENSION);
            $allowed_mime_type = $image_validate['allowed_mime_type'];
            if ($files = @getimagesize($_FILES[$var]['tmp_name'])) {

                if (!in_array($files['mime'], $allowed_mime_type)) {
                    $this->form_validation->set_message('handle_upload', 'Error While Uploading Image');
                    return false;
                }
                if (!in_array($ext, $allowed_extension) || !in_array($file_type, $allowed_mime_type)) {
                    $this->form_validation->set_message('handle_upload', 'Extension Error While Uploading Image');
                    return false;
                }
                if ($file_size > $image_validate['upload_size']) {
                    $this->form_validation->set_message('handle_upload', $this->lang->line('file_size_shoud_be_less_than') . number_format($image_validate['upload_size'] / 1048576, 2) . " MB");
                    return false;
                }
            } else {
                $this->form_validation->set_message('handle_upload', "File Type / Extension Error Uploading Image");
                return false;
            }

            return true;
        }
        return true;
    }

    public function update_birth()
    {
        if (!$this->rbac->hasPrivilege('birth_record', 'can_edit')) {
            access_denied();
        }
        $custom_fields = $this->customfield_model->getByBelong('birth_report');
        if (!empty($custom_fields)) {
            foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
                if ($custom_fields_value['validation']) {
                    $custom_fields_id   = $custom_fields_value['id'];
                    $custom_fields_name = $custom_fields_value['name'];

                    $this->form_validation->set_rules("custom_fields[birth_report][" . $custom_fields_id . "]", $custom_fields_name, 'trim|required|xss_clean');

                }
            }
        }
        $this->form_validation->set_rules('child_name', $this->lang->line('child_name'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('mother', $this->lang->line('mother_name'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('birth_date', $this->lang->line('birth_date'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('gender', $this->lang->line('gender'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('weight', $this->lang->line('weight'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('mother_pic', $this->lang->line('image'), 'callback_handle_upload[mother_pic]|callback_validateCanUploadFile[mother_pic,father_pic,child_img,document]');
        $this->form_validation->set_rules('father_pic', $this->lang->line('image'), 'callback_handle_upload[father_pic]');
        $this->form_validation->set_rules('child_img', $this->lang->line('image'), 'callback_handle_upload[child_img]');
        $this->form_validation->set_rules('document', $this->lang->line('image'), 'callback_handle_upload[document]');

        if ($this->form_validation->run() == false) {
            $msg = array(
                'child_name' => form_error('child_name'),
                'birth_date' => form_error('birth_date'),
                'mother_pic' => form_error('mother_pic'),
                'father_pic' => form_error('father_pic'),
                'child_img'  => form_error('child_img'),
                'document'   => form_error('document'),
                'mother'     => form_error('mother'),
                'gender'     => form_error('gender'),
                'weight'     => form_error('weight'),
            );
            if (!empty($custom_fields)) {
                foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
                    if ($custom_fields_value['validation']) {
                        $custom_fields_id                                                     = $custom_fields_value['id'];
                        $custom_fields_name                                                   = $custom_fields_value['name'];
                        $error_msg2["custom_fields[birth_report][" . $custom_fields_id . "]"] = form_error("custom_fields[birth_report][" . $custom_fields_id . "]");
                    }
                }
                if (!empty($error_msg2)) {
                    $error_msg = array_merge($msg, $error_msg2);
                } else {
                    $error_msg = $msg;
                }
                $json_array = array('status' => 'fail', 'error' => $error_msg, 'message' => '');
            }
        } else {
            $id = $this->input->post('id', TRUE);
            $custom_fieldvalue_array = $this->input->post("custom_field_value", TRUE);
            $custom_field_post       = $this->input->post("custom_fields[birth_report]", TRUE);
            $ddata                   = array();
            $custom_value_array      = array();

            if (!empty($custom_fields)) {
                foreach ($custom_field_post as $key => $value) {
                    $check_field_type = $this->input->post("custom_fields[birth_report][" . $key . "]", TRUE);
                    $field_value      = is_array($check_field_type) ? implode(",", $check_field_type) : $check_field_type;
                    $array_custom     = array(
                        'belong_table_id' => $id,
                        'custom_field_id' => $key,
                        'field_value'     => $field_value,
                    );
                    $custom_value_array[] = $array_custom;
                }
                $this->customfield_model->updateRecord($custom_value_array, $id, 'birth_report');
            }
            $birthdate         = $this->input->post('birth_date', TRUE);
            $birth_date        = $this->customlib->dateFormatToYYYYMMDDHis($birthdate, $this->time_format);
            $case_reference_id = $this->input->post('case_id', TRUE);
            if (empty($case_reference_id)) {
                $case_reference_id = null;
            }
            $birth_data = array(
                'id'                => $id,
                'case_reference_id' => $case_reference_id,
                'child_name'        => $this->input->post('child_name', TRUE),
                'birth_date'        => $birth_date,
                'weight'            => $this->input->post('weight', TRUE),
                'patient_id'        => $this->input->post('mother_name', TRUE),
                'contact'           => $this->input->post('contact', TRUE),
                'birth_report'      => $this->input->post('birth_report', TRUE),
                'father_name'       => $this->input->post('father_name', TRUE),
                'address'           => $this->input->post('address', TRUE),
                'gender'            => $this->input->post('gender', TRUE),
                'is_active'         => 'yes',
            );
            $insert_id = $this->birthordeath_model->addBirthdata($birth_data);
            if (!empty($id)) {

                // SaaS: snapshot existing file columns so each replace below can diff
                // the old file size against the new one and delete what it replaces.
                $existing_birth = $this->birthordeath_model->getBirthData($id);

                 //new file upload function
                if (isset($_FILES["mother_pic"]) && !empty($_FILES['mother_pic']['name'])) {
                    $uploaddir = FCPATH.'uploads/birth_image/' . $insert_id . '/';
                    if (!is_dir($uploaddir)) {
                        mkdir($uploaddir, 0777, true);
                    }
                    $filename = $this->media_storage->fileupload("mother_pic",'./uploads/birth_image/' . $insert_id . '/');
                    $mother_pic  = 'uploads/birth_image/' . $insert_id . '/' . $filename;
                    $data_img = array('id' => $id, 'mother_pic' => $mother_pic);
                    $this->birthordeath_model->addBirthdata($data_img);
                    $this->meterBirthFileReplace('mother_pic', isset($existing_birth['mother_pic']) ? $existing_birth['mother_pic'] : '');
                }

                if (isset($_FILES["father_pic"]) && !empty($_FILES['father_pic']['name'])) {
                    $uploaddir = FCPATH.'uploads/birth_image/' . $insert_id . '/';
                    if (!is_dir($uploaddir)) {
                        mkdir($uploaddir, 0777, true);
                    }
                    $filename = $this->media_storage->fileupload("father_pic",'./uploads/birth_image/' . $insert_id . '/');
                    $father_pic  = 'uploads/birth_image/' . $insert_id . '/' . $filename;
                    $data_img = array('id' => $id, 'father_pic' => $father_pic);
                    $this->birthordeath_model->addBirthdata($data_img);
                    $this->meterBirthFileReplace('father_pic', isset($existing_birth['father_pic']) ? $existing_birth['father_pic'] : '');
                }

                if (isset($_FILES["child_img"]) && !empty($_FILES['child_img']['name'])) {
                    $uploaddir = FCPATH.'uploads/birth_image/' . $insert_id . '/';
                    if (!is_dir($uploaddir)) {
                        mkdir($uploaddir, 0777, true);
                    }
                    $filename = $this->media_storage->fileupload("child_img",'./uploads/birth_image/' . $insert_id . '/');
                    $child_pic  = 'uploads/birth_image/' . $insert_id . '/' . $filename;
                    $data_img = array('id' => $id, 'child_pic' => $child_pic);
                    $this->birthordeath_model->addBirthdata($data_img);
                    $this->meterBirthFileReplace('child_img', isset($existing_birth['child_pic']) ? $existing_birth['child_pic'] : '');
                }

                if (isset($_FILES["document"]) && !empty($_FILES['document']['name'])) {
                    $uploaddir = FCPATH.'uploads/birth_image/' . $insert_id . '/';
                    if (!is_dir($uploaddir)) {
                        mkdir($uploaddir, 0777, true);
                    }
                    $filename = $this->media_storage->fileupload("document",'./uploads/birth_image/' . $insert_id . '/');
                    $document  = 'uploads/birth_image/' . $insert_id . '/' . $filename;
                    $data_img = array('id' => $id, 'document' => $document);
                    $this->birthordeath_model->addBirthdata($data_img);
                    $this->meterBirthFileReplace('document', isset($existing_birth['document']) ? $existing_birth['document'] : '');
                }
                //new file upload function

            }
            $json_array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('record_added_successfully'));
        }
        echo json_encode($json_array);
    }

    public function delete($id)
    {
        if (!$this->rbac->hasPrivilege('birth_record', 'can_delete')) {
            access_denied();
        }
        // SaaS: capture the file columns BEFORE the row is deleted so their
        // storage can be released and the physical files removed (model->delete
        // only drops the DB row, it leaves the files on disk).
        $existing_birth = $this->birthordeath_model->getBirthData($id);
        $result = $this->birthordeath_model->delete($id);
        if ($result !== false) {
            $this->releaseBirthFileQuota(isset($existing_birth['mother_pic']) ? $existing_birth['mother_pic'] : '');
            $this->releaseBirthFileQuota(isset($existing_birth['father_pic']) ? $existing_birth['father_pic'] : '');
            $this->releaseBirthFileQuota(isset($existing_birth['child_pic']) ? $existing_birth['child_pic'] : '');
            $this->releaseBirthFileQuota(isset($existing_birth['document']) ? $existing_birth['document'] : '');
        }
        $this->session->set_flashdata('msg', '<div class="alert alert-success">' . $this->lang->line('delete_message') . '</div>');
        redirect('admin/birthordeath');
    }

    public function deletedeath($id)
    {
        if (!$this->rbac->hasPrivilege('death_record', 'can_delete')) {
            access_denied();
        }
        // SaaS: capture the document filename BEFORE the row is deleted so its
        // storage can be released and the physical file removed (model->deletedeath
        // only drops the DB row, it leaves the file on disk). Death stores a bare
        // filename in `attachment` under uploads/death_image.
        $existing_death = $this->birthordeath_model->getDeathBasicById($id);
        $result = $this->birthordeath_model->deletedeath($id);
        if ($result !== false) {
            $old_attachment = (!empty($existing_death['attachment'])) ? $existing_death['attachment'] : '';
            if (!empty($old_attachment)) {
                // Read size before unlinking the file.
                $kb = $this->media_storage->getUploadedFileSize($old_attachment, 'uploads/death_image');
                if ($kb > 0) {
                    try {
                        $this->saasvalidation->deleteResouceQuota('storage', $kb);
                    } catch (Exception $e) {
                        log_message('error', 'SaaS storage quota release failed (birthordeath deletedeath): ' . $e->getMessage());
                    }
                }
                $this->media_storage->filedelete($old_attachment, 'uploads/death_image');
            }
        }
        $this->session->set_flashdata('msg', '<div class="alert alert-success">' . $this->lang->line('delete_message') . '</div>');
        redirect('admin/birthordeath');
    }

    public function update_death()
    {
        if (!$this->rbac->hasPrivilege('death_record', 'can_edit')) {
            access_denied();
        }
        $array         = array();
        $custom_fields = $this->customfield_model->getByBelong('death_report');
        if (!empty($custom_fields)) {
            foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
                if ($custom_fields_value['validation']) {
                    $custom_fields_id   = $custom_fields_value['id'];
                    $custom_fields_name = $custom_fields_value['name'];

                    $this->form_validation->set_rules("custom_fields[death_report][" . $custom_fields_id . "]", $custom_fields_name, 'trim|required|xss_clean');

                }
            }
        }
        $patient_type = $this->customlib->getPatienttype();
        $this->form_validation->set_rules('epatient', $this->lang->line('patient'), 'trim|required|xss_clean');

		$this->form_validation->set_rules('case_id', $this->lang->line('case_id'), array('required', 'xss_clean',
            array('check_exists', array($this->birthordeath_model, 'valid_case_id')),
        )
        );
		
        $this->form_validation->set_rules('guardian_name', $this->lang->line('guardian_name'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('death_date', $this->lang->line('death_date'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('file', $this->lang->line('image'), 'callback_image_upload|callback_validateCanUploadFile[document]');

        if ($this->form_validation->run() == false) {

            $msg = array(
                'epatient'      => form_error('epatient'),
                'guardian_name' => form_error('guardian_name'),
                'file'          => form_error('file'),
                'case_id'       => form_error('case_id'),
                'death_date'    => form_error('death_date'),
            );

            if (!empty($custom_fields)) {
                foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
                    if ($custom_fields_value['validation']) {
                        $custom_fields_id                                                     = $custom_fields_value['id'];
                        $custom_fields_name                                                   = $custom_fields_value['name'];
                        $error_msg2["custom_fields[death_report][" . $custom_fields_id . "]"] = form_error("custom_fields[death_report][" . $custom_fields_id . "]");
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
            $id                      = $this->input->post('id', TRUE);
            $custom_field_post       = $this->input->post("custom_fields[death_report]", TRUE);
            $custom_fieldvalue_array = $this->input->post("custom_field_value", TRUE);
            $ddata                   = array();

            if (!empty($custom_field_post)) {
                foreach ($custom_field_post as $key => $value) {
                    $check_field_type = $this->input->post("custom_fields[death_report][" . $key . "]", TRUE);
                    $field_value      = is_array($check_field_type) ? implode(",", $check_field_type) : $check_field_type;
                    $array_custom     = array(
                        'belong_table_id' => $id,
                        'custom_field_id' => $key,
                        'field_value'     => $field_value,
                    );
                    $custom_value_array[] = $array_custom;
                }
                $this->customfield_model->updateRecord($custom_value_array, $id, 'death_report');
            }

            $deathdate  = $this->input->post('death_date', TRUE);
            $death_date = $this->customlib->dateFormatToYYYYMMDDHis($deathdate, $this->time_format);
            $death_data = array(
                'id'                => $id,
                'patient_id'        => $this->input->post('epatient', TRUE),
                'guardian_name'     => $this->input->post('guardian_name', TRUE),
                'death_date'        => $death_date,
                'case_reference_id' => $this->input->post('case_id', TRUE),
                'death_report'      => $this->input->post('death_report', TRUE),
                'is_active'         => 'yes',
            );
            $this->birthordeath_model->addDeathdata($death_data);

            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('update_message'));

            if (isset($_FILES["document"]) && !empty($_FILES['document']['name'])) {
                // SaaS: capture the existing document for quota diff + cleanup. Death
                // records store a bare filename in `attachment` under uploads/death_image.
                $existing_death = $this->birthordeath_model->getDeathBasicById($id);
                $old_attachment = (!empty($existing_death['attachment'])) ? $existing_death['attachment'] : '';
                $old_kb         = (!empty($old_attachment)) ? $this->media_storage->getUploadedFileSize($old_attachment, 'uploads/death_image') : 0;

                $file_name = $this->media_storage->fileupload("document",'./uploads/death_image/');
                $data_img = array('id' => $id, 'attachment' => $file_name, 'attachment_name' => $_FILES["document"]["name"]);
                $this->birthordeath_model->addDeathdata($data_img);

                // SaaS: adjust storage quota by the size difference (new vs replaced).
                try {
                    $new_kb = $this->media_storage->getTmpFileSize('document');
                    if ($old_kb > $new_kb) {
                        $this->saasvalidation->deleteResouceQuota('storage', $old_kb - $new_kb);
                    } elseif ($new_kb > $old_kb) {
                        $this->saasvalidation->updateResouceQuota('storage', $new_kb - $old_kb);
                    }
                } catch (Exception $e) {
                    log_message('error', 'SaaS storage quota update failed (birthordeath update_death): ' . $e->getMessage());
                }

                // Remove the replaced physical file (new upload uses a unique name).
                if (!empty($old_attachment)) {
                    $this->media_storage->filedelete($old_attachment, 'uploads/death_image');
                }
            }

            //update death status in patient table
            $patient_data = array('id' => $this->input->post('patient', TRUE), 'is_dead' => 'yes');
            $this->patient_model->add($patient_data);
        }

        echo json_encode($array);
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