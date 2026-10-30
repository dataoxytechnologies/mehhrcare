<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Staff extends Admin_Controller
{
	public $blood_group;
	public $contract_type;
	public $marital_status;
	public $patient_data;
	public $payment_mode;
	public $payroll_status;
	public $sch_setting_detail;
	public $staff_attendance;
	public $status;


    public function __construct()
    {
        parent::__construct();
        $this->config->load("payroll");
        $this->load->library('Enc_lib');
        $this->load->library('mailsmsconf');
        $this->load->library('datatables');
        $this->load->library('encoding_lib');
        $this->load->library('system_notification');
   
        $this->contract_type    = $this->config->item('contracttype');
        $this->marital_status   = $this->config->item('staff_marital_status');
        $this->staff_attendance = $this->config->item('staffattendance');
        $this->payroll_status   = $this->config->item('payroll_status');
        $this->payment_mode     = $this->config->item('payment_mode');
        $this->status           = $this->config->item('status');
        $this->blood_group      = $this->config->item('staff_bloodgroup');
        $this->config->load('image_valid');
        $this->load->library('customlib');
        $this->load->library('media_storage');
        $this->load->library('SaasValidation');
        $this->load->helper('customfield_helper');
        $this->sch_setting_detail  = $this->setting_model->getSetting();
    }

    /**
     * SaaS storage pre-check (form_validation callback).
     * Returns false (blocking the save) when the combined size of the uploaded
     * staff files would push the tenant over its storage quota. The param string
     * is a comma-separated list of file fields; SaasValidation sets the message.
     */
    public function validateCanUploadFile($str, $params_string)
    {
        $storage_array = array_map('trim', explode(',', $params_string));
        return $this->saasvalidation->validateCanUploadFile($str, $storage_array);
    }

    /**
     * SaaS: on a staff-file replace (edit), adjust the storage quota by the size
     * difference (old vs new) and delete the replaced physical file. Columns store
     * a bare filename; $dir is the relative upload directory for that field
     * (uploads/staff_images or uploads/staff_documents/<id>). Uses the throwing
     * updateResouceQuota/deleteResouceQuota, so a plain catch suffices.
     *
     * @param string $field     $_FILES key of the newly uploaded file
     * @param string $old_value prior column value (bare filename), '' if none
     * @param string $dir       relative directory the file lives in
     */
    private function meterStaffFileReplace($field, $old_value, $dir)
    {
        $old_kb = (!empty($old_value)) ? $this->media_storage->getUploadedFileSize($old_value, $dir) : 0;
        $new_kb = $this->media_storage->getTmpFileSize($field);

        try {
            if ($old_kb > $new_kb) {
                $this->saasvalidation->deleteResouceQuota('storage', $old_kb - $new_kb);
            } elseif ($new_kb > $old_kb) {
                $this->saasvalidation->updateResouceQuota('storage', $new_kb - $old_kb);
            }
        } catch (Exception $e) {
            log_message('error', 'SaaS storage quota update failed (staff edit ' . $field . '): ' . $e->getMessage());
        }

        // Remove the replaced old file (new upload uses a unique name, so it never
        // overwrote the old one).
        if (!empty($old_value)) {
            $this->media_storage->filedelete($old_value, $dir);
        }
    }

    public function index()
    {    
        if (!$this->rbac->hasPrivilege('staff', 'can_view')) {
            access_denied();
        }
        $data['title'] = $this->lang->line('staff_search');

        $this->session->set_userdata('top_menu', 'HR');
        $this->session->set_userdata('sub_menu', 'admin/staff');
        $staffRole    = $this->staff_model->getStaffRole();
        $data["role"] = $staffRole;
		$search = $this->input->post("search", TRUE);

        $data["role_id"] = "";
        $data['fields']  = $this->customfield_model->get_custom_fields('staff', 1);
        $search_text = $this->input->post('search_text', TRUE);
        if ($this->input->server('REQUEST_METHOD') === 'GET') {
			
			if(!empty($this->uri->segment(4))){
						$data['searchby']    = "filter";
                        $role                = $this->uri->segment(4);                        
                        $data["role_id"]     = $role; 
                        $resultlist          = $this->staff_model->getEmployee($role, 1);
                        $data['resultlist']  = $resultlist;
			}else{
				$resultlist         = $this->staff_model->searchFullText("", 1);
				$data['resultlist'] = $resultlist;
			}
            
        } elseif ($this->input->server('REQUEST_METHOD') === 'POST') {

            $role        = $this->input->post('role', TRUE);
            $search_text = $this->input->post('search_text', TRUE);
            $data["role_id"]     = $role;
            $data['search_text'] = $search_text;

            if (!empty($role)) {
                $data['searchby'] = "filter";
                $resultlist       = $this->staff_model->getEmployee($role, 1);
                $data['resultlist'] = $resultlist;
            } else {
                $data['searchby'] = "text";
                $resultlist       = $this->staff_model->searchFullText($search_text, 1);
                $data['resultlist'] = $resultlist;
            }
        }
        $data['module'] = 'human_resource';
        $this->load->view('layout/header', $data);
        $this->load->view('admin/staff/staffsearch', $data);
        $this->load->view('layout/footer', $data);
    }

    public function server_data()
    {
        $columns = array(
            0 => 'employee_id',
            1 => 'name',
            2 => 'user_type',
            3 => 'department',
            4 => 'designation',
            5 => 'contact_no',
        );

        $limit = 2;

        $start = $this->input->post('start');
        if (empty($start)) {
            $start = 0;
        }
        $order         = 'staff.' . $columns[1];
        $dir           = 'asc';
        $totalData     = 4;
        $totalFiltered = $totalData;
        $posts         = $this->staff_model->searchFullText("", 1, $order, $dir, $limit, $start);
        $data          = array();
        if (!empty($posts)) {
            foreach ($posts as $post) {
                $nestedData['employee_id'] = $post["employee_id"];
                $nestedData['name']        = $post["name"];
                $nestedData['user_type']   = $post["user_type"];
                $nestedData['department']  = $post["department"];
                $nestedData['designation'] = $post["designation"];
                $nestedData['contact_no']  = $post["contact_no"];
                $data[]                    = $nestedData;
            }
        }

        $json_data = array(
            "draw"            => 1,
            "recordsTotal"    => intval($totalData),
            "recordsFiltered" => 4,
            "data"            => $data,
        );

        echo json_encode($json_data);
    }

    public function disablestafflist()
    {
        if (!$this->rbac->hasPrivilege('disable_staff', 'can_view')) {
            access_denied();
        }

        $this->session->set_userdata('top_menu', 'HR');
        $this->session->set_userdata('sub_menu', 'admin/staff/disablestafflist');
        $data['title'] = $this->lang->line('staff_search');
        $staffRole     = $this->staff_model->getStaffRole();
        $data["role"]  = $staffRole;

        $search             = $this->input->post("search");
        $search_text        = $this->input->post('search_text');
        $resultlist         = $this->staff_model->searchFullText($search_text, 0);
        
        $data['resultlist'] = $resultlist;
         
        if (isset($search)) {
            if ($search == 'search_filter') {
                $this->form_validation->set_rules('role', $this->lang->line('role'), 'trim|required|xss_clean');
                if ($this->form_validation->run() == false) {

                    $resultlist         = array();
                    $data['resultlist'] = $resultlist;
                } else {
                    $data['searchby']    = "filter";
                    $role                = $this->input->post('role');
                    $data['employee_id'] = $this->input->post('empid');
                    $data['search_text'] = $this->input->post('search_text');
                    $resultlist          = $this->staff_model->getEmployee($role, 0);
                    $data['resultlist']  = $resultlist;
                }
            } else if ($search == 'search_full') {
                $data['searchby']    = "text";
                $data['search_text'] = trim($this->input->post('search_text'));
                $resultlist          = $this->staff_model->searchFullText($search_text, 0);
                $data['resultlist']  = $resultlist;
                $data['title']       = 'Search Details: ' . $data['search_text'];
            }
        }
        $data['module'] = 'human_resource';
        $this->load->view('layout/header', $data);
        $this->load->view('admin/staff/disablestaff', $data);
        $this->load->view('layout/footer', $data);
    }

    public function profile($id)
    {
        if (!$this->rbac->hasPrivilege('staff', 'can_view')) {
            access_denied();
        } 
        $data['enable_disable']   = 1;
        $staff_data               = $this->session->flashdata('staff_data');
        $data['staff_data']       = $staff_data;
        $data["id"]               = $id;
        $data['title']            = 'Staff Details';
        $staff_info               = $this->staff_model->getProfile($id);
        $staff_speciality         = $this->staff_model->getStaffSpeciality($id); 
        $data['staff_speciality'] = $staff_speciality;

        $userdata        = $this->customlib->getUserData();
        $userid          = $userdata['id'];
        $timeline_status = '';
        if ($userid == $id) {
            $data['enable_disable'] = 0;
            $timeline_status        = 'yes';
        }
        $timeline_list               = $this->timeline_model->getStaffTimeline($id, $timeline_status);
        $data["timeline_list"]       = $timeline_list;
        $staff_payroll               = $this->staff_model->getStaffPayroll($id);
        $staff_leaves                = $this->leaverequest_model->staff_leave_request($id);
        $alloted_leavetype           = $this->staff_model->allotedLeaveType($id);
        $salary                      = $this->payroll_model->getSalaryDetails($id);
        $attendencetypes             = $this->staffattendancemodel->getStaffAttendanceType();
        $data['attendencetypeslist'] = $attendencetypes;
        $i                           = 0;
        $leaveDetail                 = array();
        foreach ($alloted_leavetype as $key => $value) {
            $count_leaves[]                   = $this->leaverequest_model->countLeavesData($id, $value["leave_type_id"]);
            $leaveDetail[$i]['type']          = $value["type"];
            $leaveDetail[$i]['alloted_leave'] = $value["alloted_leave"];
            $leaveDetail[$i]['approve_leave'] = $count_leaves[$i]['approve_leave'];
            $i++;
        }
        $data["leavedetails"]  = $leaveDetail;
        $data["staff_leaves"]  = $staff_leaves;
        $data['staff_doc_id']  = $id;
        $data['staff']         = $staff_info;
        $data['staff_payroll'] = $staff_payroll;
        $data['salary']        = $salary;
        $monthlist             = $this->customlib->geLangMonthList();
        $startMonth            = $this->setting_model->getStartMonth();
        $data["monthlist"]     = $monthlist;
        $data['yearlist']      = $this->staffattendancemodel->attendanceYearCount();
        $year                  = date("Y");

        foreach ($monthlist as $key => $value) {
            $datemonth       = date("m", strtotime($value));
            $date_each_month = date('Y-' . $datemonth . '-01');
            $date_end        = date('t', strtotime($date_each_month));
            for ($n = 1; $n <= $date_end; $n++) {
                $att_date           = sprintf("%02d", $n);
                $attendence_array[] = $att_date;
                $datemonth          = date("m", strtotime($value));
                $att_dates          = $year . "-" . $datemonth . "-" . sprintf("%02d", $n);

                $date_array[]    = $att_dates;
                $res[$att_dates] = $this->staffattendancemodel->searchStaffattendance($att_dates, $id);
            }
        }

        $start_year               = date("Y");
        $date                     = $start_year . "-" . $startMonth;
        $newdate                  = date("Y-m-d", strtotime($date . "+1 month"));
        $countAttendance          = $this->countAttendance($start_year, $startMonth, $id);
        $data["countAttendance"]  = $countAttendance;
        $data["resultlist"]       = $res;
        $data["attendence_array"] = $attendence_array;
        $data["date_array"]       = $date_array;
        $data["payroll_status"]   = $this->payroll_status;
        $data["payment_mode"]     = $this->payment_mode;
        $data["contract_type"]    = $this->contract_type;
        $data["status"]           = $this->status;
        $roles                    = $this->role_model->get();
        $data["roles"]            = $roles;
        $stafflist                = $this->staff_model->get();       
        $data['stafflist']        = $stafflist;
        $employee_id              = $data['staff']['employee_id'];
		
        $data['getbarcode']       = $this->customlib->generatestaffbarcode($employee_id,$id,'barcode');
        $data['getqrcode']        = $this->customlib->generatestaffbarcode($employee_id,$id,'qrcode');       
		
        $data['superadmin_restriction']        = $this->sch_setting_detail->superadmin_restriction;
        $data['module'] = 'human_resource';
        $this->load->view('layout/header', $data);
        $this->load->view('admin/staff/staffprofile', $data);
        $this->load->view('layout/footer', $data); 
    }

    public function countAttendance($year, $no_of_months, $emp)
    {
        if (!$this->rbac->hasPrivilege('staff_attendance', 'can_view')) {
            access_denied();
        }
        $record = array();
        for ($i = 1; $i <= 1; $i++) {
            $r     = array();
            foreach ($this->staff_attendance as $att_key => $att_value) {
                $s           = $this->staff_model->count_attendance($year, $emp, $att_value);
                $r[$att_key] = $s;
            }

            $record[$year] = $r;
        }
        return $record;
    }

    public function getSession()
    {
        $session             = $this->session_model->getAllSession();
        $data                = array();
        $session_array       = $this->session->has_userdata('session_array');
        $data['sessionData'] = array('session_id' => 0);
        if ($session_array) {
            $data['sessionData'] = $this->session->userdata('session_array');
        } else {
            $setting             = $this->setting_model->get();
            $data['sessionData'] = array('session_id' => $setting[0]['session_id']);
        }
        $data['sessionList'] = $session;
        return $data;
    }

    public function getSessionMonthDropdown()
    {
        $startMonth = $this->setting_model->getStartMonth();
        $array      = array();
        for ($m = $startMonth; $m <= $startMonth + 11; $m++) {
            $month         = date('F', mktime(0, 0, 0, $m, 1, date('Y')));
            $array[$month] = $month;
        }
        return $array;
    }

    public function download($id,$staff_id)
    {
        $get_Leave_Record = $this->staff_model->getLeaveRecord($id); 
        $this->media_storage->filedownload($get_Leave_Record->document_file,"./uploads/staff_documents/$staff_id/");
    }
    
    public function download_document($staff_id,$type)
    {
        if (!$this->rbac->hasPrivilege('staff', 'can_view')) {
            access_denied();
        }
        $staff_data=$this->staff_model->getstaff($staff_id);

        if($type==1){
            $this->media_storage->filedownload($staff_data['resume'],"./uploads/staff_documents/$staff_id/");
        }else if($type==2){
            $this->media_storage->filedownload($staff_data['joining_letter'],"./uploads/staff_documents/$staff_id/");
        }else if($type==3){
            $this->media_storage->filedownload($staff_data['resignation_letter'],"./uploads/staff_documents/$staff_id/");
        }else if($type==4){
            $this->media_storage->filedownload($staff_data['other_document_file'],"./uploads/staff_documents/$staff_id/");
        }

    }

    public function doc_delete($id, $doc)
    {
        if (!$this->rbac->hasPrivilege('staff', 'can_delete')) {
            access_denied();
        }
        $this->staff_model->doc_delete($id, $doc); 
        $this->session->set_flashdata('msg', '<i class="fa fa-check-square-o" aria-hidden="true"></i> ' . $this->lang->line('document_deleted_successfully'));
        redirect('admin/staff/profile/' . $id);
    }

    public function ajax_attendance($id)
    {
        if (!$this->rbac->hasPrivilege('staff_attendance', 'can_view')) {
            access_denied();
        }
        $attendencetypes             = $this->staffattendancemodel->getStaffAttendanceType();
        $data['attendencetypeslist'] = $attendencetypes;
        $year                        = $this->input->post("year");

        if (!empty($year)) {

            $monthlist         = $this->customlib->getMonthDropdown();
            $startMonth        = $this->setting_model->getStartMonth();
            $data["monthlist"] = $monthlist;
            $data['yearlist']  = $this->staffattendancemodel->attendanceYearCount();
            $startMonth = $this->setting_model->getStartMonth();           

            foreach ($monthlist as $key => $value) {
                $datemonth       = date("m", strtotime($value));
                $date_each_month = date($year . "-" . $datemonth . '-01');
                $date_end        = date('t', strtotime($date_each_month));
                for ($n = 1; $n <= $date_end; $n++) {
                    $att_date           = sprintf("%02d", $n);
                    $attendence_array[] = $att_date;
                    $datemonth          = date("m", strtotime($value));
                    $att_dates          = $year . "-" . $datemonth . "-" . sprintf("%02d", $n);
                    $date_array[]    = $att_dates;
                    $res[$att_dates] = $this->staffattendancemodel->searchStaffattendance($att_dates,$id);
                }
            }

            $date                     = $year . "-" . $startMonth;
            $newdate                  = date("Y-m-d", strtotime($date . "+1 month"));
            $countAttendance          = $this->countAttendance($year, $startMonth, $id);
            $data["countAttendance"]  = $countAttendance;
            $data["id"]               = $id;
            $data["resultlist"]       = $res;
            $data["attendence_array"] = $attendence_array;
            $data["date_array"]       = $date_array;
            $data["year"]       = $year;
            $page=$this->load->view("admin/staff/ajaxattendance", $data,true);
            echo json_encode(array('status' => 1, 'page' => $page,'countAttendance'=>$countAttendance));
        } else {
            echo json_encode(array('status' => 0, 'error' => $this->lang->line('no_record_found'),'countAttendance'=>$countAttendance));
        }
    }



    public function validateCanAddNewResource($input, $resource_name)
    {
        list($resource_name, $quantity) = explode(',', $resource_name);

        return $this->saasvalidation->validateCanAddNewResource($input, $resource_name, $quantity);
    }



    public function create()
    {
		if (!$this->rbac->hasPrivilege('staff', 'can_add')) {
            access_denied();
        }
		
        $image_validate = $this->config->item('image_validate');
        $this->session->set_userdata('top_menu', 'HR');
        $this->session->set_userdata('sub_menu', 'admin/staff');
        $roles                  = $this->role_model->get();
        $data["roles"]          = $roles;
        $genderList             = $this->customlib->getGender();
        $data['genderList']     = $genderList;
        $payscaleList           = $this->staff_model->getPayroll();
        $leavetypeList          = $this->staff_model->getLeaveType();
        $data["leavetypeList"]  = $leavetypeList;
        $data["payscaleList"]   = $payscaleList;
        $designation            = $this->staff_model->getStaffDesignation();
        $data["designation"]    = $designation;
        $department             = $this->staff_model->getDepartment();
        $data["department"]     = $department;
        $specialist             = $this->staff_model->getspecialist();
        $data["specialist"]     = $specialist;
        $marital_status         = $this->marital_status;
        $data["marital_status"] = $marital_status;
        $data["bloodgroup"]     = $this->blood_group;
        $data['title']          = 'Add Staff';
        $data["contract_type"]  = $this->contract_type;
        $custom_fields          = $this->customfield_model->getByBelong('staff');
		
        foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
            if ($custom_fields_value['validation']) {
                $custom_fields_id   = $custom_fields_value['id'];
                $custom_fields_name = $custom_fields_value['name'];
                $this->form_validation->set_rules("custom_fields[staff][" . $custom_fields_id . "]", $custom_fields_name, 'trim|required|xss_clean');
            }
        }

        $this->form_validation->set_rules('name', $this->lang->line('name'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('role', $this->lang->line('role'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('gender', $this->lang->line('gender'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('dob', $this->lang->line('date_of_birth'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('email', $this->lang->line('email'), array('required', 'xss_clean', 'valid_email',
            array('check_exists', array($this->staff_model, 'valid_email_id')),
        )
        );
        $this->form_validation->set_rules('employee_id', $this->lang->line('staff_id'), array('required', 'xss_clean',
            array('check_exists', array($this->staff_model, 'valid_employee_id')),
        )
        );
		
		$this->form_validation->set_rules('validate_resource',  $this->lang->line('staff'),  "callback_validateCanAddNewResource[no_of_staff,1]");

        $this->form_validation->set_rules('file', $this->lang->line('image'), 'callback_handle_upload[file]|callback_validateCanUploadFile[file,first_doc,second_doc,third_doc,fourth_doc]');
        $this->form_validation->set_rules('first_doc', $this->lang->line('document'), 'callback_handle_upload[first_doc]');
        $this->form_validation->set_rules('second_doc', $this->lang->line('document'), 'callback_handle_upload[second_doc]');
        $this->form_validation->set_rules('third_doc', $this->lang->line('document'), 'callback_handle_upload[third_doc]');
        $this->form_validation->set_rules('fourth_doc', $this->lang->line('document'), 'callback_handle_upload[fourth_doc]');

        if ($this->form_validation->run() == false) {
            $data['module'] = 'human_resource';
            $this->load->view('layout/header', $data);
            $this->load->view('admin/staff/staffcreate', $data);
            $this->load->view('layout/footer', $data);
        } else {
		  try {
            $custom_field_post  = $this->input->post("custom_fields[staff]");
            $custom_value_array = array();
            if (!empty($custom_fields)) {
                foreach ($custom_field_post as $key => $value) {
                    $check_field_type = $this->input->post("custom_fields[staff][" . $key . "]");
                    $field_value      = is_array($check_field_type) ? implode(",", $check_field_type) : $check_field_type;
                    $array_custom     = array(
                        'belong_table_id' => 0,
                        'custom_field_id' => $key,
                        'field_value'     => $field_value,
                    );
                    $custom_value_array[] = $array_custom;
                }
            }
            $employee_id    = $this->input->post("employee_id", TRUE);
            $department     = $this->input->post("department", TRUE);
            $designation    = $this->input->post("designation", TRUE);
            $specialist     = $this->input->post("specialist[]", TRUE);
            $role           = $this->input->post("role", TRUE);
            $name           = $this->input->post("name", TRUE);
            $gender         = $this->input->post("gender", TRUE);
            $marital_status = $this->input->post("marital_status", TRUE);
            $dob            = $this->input->post("dob", TRUE);
            if (!empty($dob)) {
                $insert_dob = $this->customlib->dateFormatToYYYYMMDD($dob);
            }
            $contact_no      = $this->input->post("contactno", TRUE);
            $emergency_no    = $this->input->post("emgcontactno", TRUE);
            $email           = $this->input->post("email", TRUE);
            $date_of_joining = $this->input->post("date_of_joining", TRUE);
            if (!empty($date_of_joining)) {
                $insert_date_of_joining = $this->customlib->dateFormatToYYYYMMDD($date_of_joining);
            } else {
                $insert_date_of_joining = null;
            }

            $date_of_leaving = $this->input->post("date_of_leaving", TRUE);
            if (!empty($date_of_leaving)) {
                $insert_date_of_leaving = $this->customlib->dateFormatToYYYYMMDD($date_of_leaving);
            } else {
                $insert_date_of_leaving = null;
            }

            $address                     = $this->input->post("address", TRUE);
            $qualification               = $this->input->post("qualification", TRUE);
            $work_exp                    = $this->input->post("work_exp", TRUE);
            $basic_salary                = $this->input->post('basic_salary', TRUE);
            $account_title               = $this->input->post("account_title", TRUE);
            $bank_account_no             = $this->input->post("bank_account_no", TRUE);
            $bank_name                   = $this->input->post("bank_name", TRUE);
            $ifsc_code                   = $this->input->post("ifsc_code", TRUE);
            $bank_branch                 = $this->input->post("bank_branch", TRUE);
            $contract_type               = $this->input->post("contract_type", TRUE);
            $shift                       = $this->input->post("shift", TRUE);
            $location                    = $this->input->post("location", TRUE);
            $leave                       = $this->input->post("leave", TRUE);
            $facebook                    = $this->input->post("facebook", TRUE);
            $twitter                     = $this->input->post("twitter", TRUE);
            $linkedin                    = $this->input->post("linkedin", TRUE);
            $instagram                   = $this->input->post("instagram", TRUE);
            $permanent_address           = $this->input->post("permanent_address", TRUE);
            $father_name                 = $this->input->post("father_name", TRUE);
            $surname                     = $this->input->post("surname", TRUE);
            $mother_name                 = $this->input->post("mother_name", TRUE);
            $specialization              = $this->input->post("specialization", TRUE);
            $note                        = $this->input->post("note", TRUE);
            $epf_no                      = $this->input->post("epf_no", TRUE);
            $blood_group                 = $this->input->post("blood_group", TRUE);
            $pan_number                  = $this->input->post("pan_number", TRUE);
            $identification_number       = $this->input->post("identification_number", TRUE);
            $local_identification_number = $this->input->post("local_identification_number", TRUE);

            $password = $this->role->get_random_password($chars_min = 6, $chars_max = 6, $use_upper_case = false, $include_numbers = true, $include_special_chars = false);
            if (!empty($specialist)) {
                $specialist_separated = implode(",", $specialist);
            } else {
                $specialist_separated = '';
            }

            $data_insert = array(
                'password'                    => $this->enc_lib->passHashEnc($password),
                'employee_id'                 => $employee_id,
                'department_id'               => $department,
                'staff_designation_id'        => $designation,
                'specialist'                  => $specialist_separated,
                'qualification'               => $qualification,
                'work_exp'                    => $work_exp,
                'name'                        => $name,
                'contact_no'                  => $contact_no,
                'emergency_contact_no'        => $emergency_no,
                'email'                       => $email,
                'dob'                         => $insert_dob,
                'marital_status'              => $marital_status,
                'date_of_joining'             => $insert_date_of_joining,
                'date_of_leaving'             => $insert_date_of_leaving,
                'local_address'               => $address,
                'permanent_address'           => $permanent_address,
                'note'                        => $note,
                'surname'                     => $surname,
                'mother_name'                 => $mother_name,
                'father_name'                 => $father_name,
                'gender'                      => $gender,
                'account_title'               => $account_title,
                'bank_account_no'             => $bank_account_no,
                'bank_name'                   => $bank_name,
                'ifsc_code'                   => $ifsc_code,
                'bank_branch'                 => $bank_branch,
                'payscale'                    => '',
                'basic_salary'                => $basic_salary,
                'epf_no'                      => $epf_no,
                'contract_type'               => $contract_type,
                'blood_group'                 => $blood_group,
                'shift'                       => $shift,
                'location'                    => $location,
                'facebook'                    => $facebook,
                'twitter'                     => $twitter,
                'linkedin'                    => $linkedin,
                'instagram'                   => $instagram,
                'specialization'              => $specialization,
                'pan_number'                  => $pan_number,
                'identification_number'       => $identification_number,
                'local_identification_number' => $local_identification_number,
                'is_active'                   => 1,
            );
            $leave_type  = $this->input->post('leave_type', TRUE);
            $leave_array = array();
            if (!empty($leave_type)) {
                foreach ($leave_type as $leave_key => $leave_value) {
                    $leave_array[] = array(
                        'staff_id'      => 0,
                        'leave_type_id' => $leave_value,
                        'alloted_leave' => $this->input->post('alloted_leave_' . $leave_value, TRUE),
                    );
                }
            }

            $role_array = array('role_id' => $this->input->post('role', TRUE), 'staff_id' => 0);
            $insert_id  = $this->staff_model->batchInsert($data_insert, $role_array, $leave_array);
            $staff_id   = $insert_id;  

            //generate staff barcode and qrcode
            $staff_empid    =   $this->staff_model->get_staff_emp_id($staff_id);
            $scan_type      =   $this->sch_setting_detail->scan_code_type;
			
            $this->customlib->generatestaffbarcode($staff_empid,$staff_id,$scan_type);
			
            //generate staff barcode and qrcode

            if (!empty($custom_value_array)) {
                $this->customfield_model->insertRecord($custom_value_array, $insert_id);
            }

            //new upload file function
            if (isset($_FILES["file"]) && !empty($_FILES['file']['name'])) {
                $file_name = $this->media_storage->fileupload("file",'./uploads/staff_images/');
                $data_img = array('id' => $staff_id, 'image' => $file_name);
                $this->staff_model->add($data_img);
            }
            //new upload file function

            //new upload file function
            if (isset($_FILES["first_doc"]) && !empty($_FILES['first_doc']['name'])) {
                $uploaddir = $this->customlib->getFolderPath().'./uploads/staff_documents/' . $staff_id . '/';
                if (!is_dir($uploaddir) && !mkdir($uploaddir, 0755, true)) {
                    show_error("Could not create upload directory: $uploaddir");
                }
                $resume = $this->media_storage->fileupload("first_doc",'./uploads/staff_documents/' . $staff_id . '/');
            }else{
                $resume = "";
            }

            if (isset($_FILES["second_doc"]) && !empty($_FILES['second_doc']['name'])) {
                $uploaddir = $this->customlib->getFolderPath().'./uploads/staff_documents/' . $staff_id . '/';
                if (!is_dir($uploaddir) && !mkdir($uploaddir, 0755, true)) {
                    show_error("Could not create upload directory: $uploaddir");
                }
                $joining_letter = $this->media_storage->fileupload("second_doc",'./uploads/staff_documents/' . $staff_id . '/');
            }else{
                $joining_letter = "";
            }

            if (isset($_FILES["third_doc"]) && !empty($_FILES['third_doc']['name'])) {
                $uploaddir = $this->customlib->getFolderPath().'./uploads/staff_documents/' . $staff_id . '/';
                if (!is_dir($uploaddir) && !mkdir($uploaddir, 0755, true)) {
                    show_error("Could not create upload directory: $uploaddir");
                }
                $resignation_letter = $this->media_storage->fileupload("third_doc",'./uploads/staff_documents/' . $staff_id . '/');
            }else{
                $resignation_letter = "";
            }

            if (isset($_FILES["fourth_doc"]) && !empty($_FILES['fourth_doc']['name'])) {
                $uploaddir = $this->customlib->getFolderPath().'./uploads/staff_documents/' . $staff_id . '/';
                if (!is_dir($uploaddir) && !mkdir($uploaddir, 0755, true)) {
                    show_error("Could not create upload directory: $uploaddir");
                }
                $fourth_doc = $this->media_storage->fileupload("fourth_doc",'./uploads/staff_documents/' . $staff_id . '/');
                $fourth_title = 'Other Doucment';
            }else{
                $fourth_doc   = "";
                $fourth_title = "";
            }
            //new upload file function


            $data_doc = array('id' => $staff_id, 'resume' => $resume, 'joining_letter' => $joining_letter, 'resignation_letter' => $resignation_letter, 'other_document_name' => $fourth_title, 'other_document_file' => $fourth_doc);
            $this->staff_model->add($data_doc);

            // SaaS: add the size of every uploaded staff file (profile image + the four
            // documents) to the storage quota usage in one call. updateStorageLimit sums
            // $_FILES sizes (which survive move_uploaded_file) across all listed fields and
            // skips absent ones. This is a pure add — create() always makes a new staff row.
            try {
                $saas_quota_result = $this->saasvalidation->updateStorageLimit('storage', ['file', 'first_doc', 'second_doc', 'third_doc', 'fourth_doc']);
                // Silent-failure capture: updateStorageLimit does not throw on API
                // rejection — it returns a JSON string with status:false instead.
                if (is_string($saas_quota_result)) {
                    $saas_decoded = json_decode($saas_quota_result, true);
                    if (isset($saas_decoded['status']) && $saas_decoded['status'] === false) {
                        log_message('error', 'SaaS storage quota update returned failure (staff create): ' . $saas_quota_result);
                    }
                }
            } catch (Exception $e) {
                log_message('error', 'SaaS storage quota update failed (staff create): ' . $e->getMessage());
            }

            // SaaS: increment the staff count usage by 1 (count-based resource:
            // no_of_staff). The pre-check (callback_validateCanAddNewResource[no_of_staff,1])
            // only blocks when over limit; this is what actually raises the usage so the
            // limit stays meaningful. Own try/catch so a quota-API hiccup does not abort
            // the already-created staff's flow (updateResouceQuota throws on failure).
            try {
                $this->saasvalidation->updateResouceQuota('no_of_staff', 1);
            } catch (Exception $e) {
                log_message('error', 'SaaS no_of_staff quota increment failed (staff create): ' . $e->getMessage());
            }

            //===================

            if ($staff_id) {
                $staff_login_detail = array('id' => $staff_id, 'credential_for' => 'staff', 'username' => $email, 'password' => $password, 'contact_no' => $contact_no, 'email' => $email);
                $this->mailsmsconf->mailsms('login_credential', $staff_login_detail);
            }

            //==========================
            $this->session->set_flashdata('msg', '<div class="alert alert-success">' . $this->lang->line('success_message') . '</div>');

            redirect('admin/staff');
			
			
			
			
			} catch (Exception $e) {
                // Print the exception message for debugging or logging purposes
                echo 'Error: ' . $e->getMessage();
            }
        }
		
		
		
    }

    /**
     * This function is used to validate document for upload
     **/
    public function handle_upload($str, $var)
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
                    $this->form_validation->set_message('handle_upload', $this->lang->line('file_type_extension_error_uploading_document'));
                    return false;
                }

                if (!in_array($ext, $allowed_extension) || !in_array($file_type, $allowed_mime_type)) {
                    $this->form_validation->set_message('handle_upload', $this->lang->line('extension_error_while_uploading_document'));
                    return false;
                }
                if ($file_size > 2097152) {
                    $this->form_validation->set_message('handle_upload', $this->lang->line('file_size_shoud_be_less_than') . "2MB");
                    return false;
                }
            } else {
                $this->form_validation->set_message('handle_upload', $this->lang->line('error_while_uploading_document'));
                return false;
            }

            return true;
        }
        return true;
    }

    public function edit($id)
    {
        if (!$this->rbac->hasPrivilege('staff', 'can_edit')) {
            access_denied();
        }
        $data['title']             = $this->lang->line('edit_staff');
        $data['id']                = $id;
        $genderList                = $this->customlib->getGender();
        $data['genderList']        = $genderList;
        $payscaleList              = $this->staff_model->getPayroll();
        $leavetypeList             = $this->staff_model->getLeaveType();
        $data["leavetypeList"]     = $leavetypeList;
        $data["payscaleList"]      = $payscaleList;
        $staffRole                 = $this->staff_model->getStaffRole();
        $data["getStaffRole"]      = $staffRole;
        $designation               = $this->staff_model->getStaffDesignation();
        $data["designation"]       = $designation;
        $department                = $this->staff_model->getDepartment();
        $data["department"]        = $department;
        $specialist                = $this->staff_model->getSpecialist();
        $data["specialist"]        = $specialist;
        $data["bloodgroup"]        = $this->blood_group;
        $marital_status            = $this->marital_status;
        $data["marital_status"]    = $marital_status;
        $data['title']             = $this->lang->line('edit_staff');
        $staff                     = $this->staff_model->get($id);
        $data['staff']             = $staff;
        $specialist_list           = explode(",", $staff['specialist']);
        $data['specialist_list']   = $specialist_list;
        $data["contract_type"]     = $this->contract_type;
        $staffLeaveDetails         = $this->staff_model->getLeaveDetails($id);
        $data['staffLeaveDetails'] = $staffLeaveDetails;
        $resume                    = $this->input->post("resume", TRUE);
        $joining_letter            = $this->input->post("joining_letter", TRUE);
        $resignation_letter        = $this->input->post("resignation_letter", TRUE);
        $other_document_name       = $this->input->post("other_document_name", TRUE);
        $other_document_file       = $this->input->post("other_document_file", TRUE);
        $custom_fields             = $this->customfield_model->getByBelong('staff');
        foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {

            if ($custom_fields_value['validation']) {
                $custom_fields_id   = $custom_fields_value['id'];
                $custom_fields_name = $custom_fields_value['name'];
                $this->form_validation->set_rules("custom_fields[staff][" . $custom_fields_id . "]", $custom_fields_name, 'trim|required|xss_clean');
            }
        }

        $this->form_validation->set_rules('name', $this->lang->line('name'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('role', $this->lang->line('role'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('gender', $this->lang->line('gender'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('dob', $this->lang->line('date_of_birth'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('file', $this->lang->line('image'), 'callback_handle_image_upload[file]|callback_validateCanUploadFile[file,first_doc,second_doc,third_doc,fourth_doc]');
        $this->form_validation->set_rules('first_doc', $this->lang->line('document'), 'callback_handle_upload[first_doc]');
        $this->form_validation->set_rules('second_doc', $this->lang->line('document'), 'callback_handle_upload[second_doc]');
        $this->form_validation->set_rules('third_doc', $this->lang->line('document'), 'callback_handle_upload[third_doc]');
        $this->form_validation->set_rules('fourth_doc', $this->lang->line('document'), 'callback_handle_upload[fourth_doc]');
        $this->form_validation->set_rules(
            'email', $this->lang->line('email'), array('required', 'xss_clean', 'valid_email',
                array('check_exists', array($this->staff_model, 'valid_email_id')),
            )
        );
        $this->form_validation->set_rules('employee_id', $this->lang->line('staff_id'), array('required', 'xss_clean',
            array('check_exists', array($this->staff_model, 'valid_employee_id')),
        )
        );

        if ($this->form_validation->run() == false) {
            $data['module'] = 'human_resource';
            $this->load->view('layout/header', $data);
            $this->load->view('admin/staff/staffedit', $data);
            $this->load->view('layout/footer', $data);
        } else {
            $employee_id                 = $this->input->post("employee_id", TRUE);
            $department                  = $this->input->post("department", TRUE);
            $designation                 = $this->input->post("designation", TRUE);
            $specialist                  = $this->input->post("specialist[]", TRUE);
            $role                        = $this->input->post("role", TRUE);
            $name                        = $this->input->post("name", TRUE);
            $gender                      = $this->input->post("gender", TRUE);
            $marital_status              = $this->input->post("marital_status", TRUE);
            $dob                         = $this->input->post("dob", TRUE);
            $contact_no                  = $this->input->post("contactno", TRUE);
            $emergency_no                = $this->input->post("emgcontactno", TRUE);
            $email                       = $this->input->post("email", TRUE);
            $dateofjoining               = $this->input->post("date_of_joining", TRUE);
            $pan_number                  = $this->input->post("pan_number", TRUE);
            $identification_number       = $this->input->post("identification_number", TRUE);
            $local_identification_number = $this->input->post("local_identification_number", TRUE);

            if (!empty($this->input->post("date_of_joining", TRUE))) {
                $date_of_joining = $this->customlib->dateFormatToYYYYMMDD($dateofjoining);
            } else {
                $date_of_joining = null;
            }

            $dateofleaving = $this->input->post("date_of_leaving", TRUE);
            if (!empty($this->input->post("date_of_leaving", TRUE))) {
                $date_of_leaving = $this->customlib->dateFormatToYYYYMMDD($dateofleaving);
            } else {
                $date_of_leaving = null;
            }

            $address            = $this->input->post("address", TRUE);
            $qualification      = $this->input->post("qualification", TRUE);
            $work_exp           = $this->input->post("work_exp", TRUE);
            $basic_salary       = $this->input->post('basic_salary', TRUE);
            $account_title      = $this->input->post("account_title", TRUE);
            $bank_account_no    = $this->input->post("bank_account_no", TRUE);
            $bank_name          = $this->input->post("bank_name", TRUE);
            $ifsc_code          = $this->input->post("ifsc_code", TRUE);
            $bank_branch        = $this->input->post("bank_branch", TRUE);
            $contract_type      = $this->input->post("contract_type", TRUE);
            $shift              = $this->input->post("shift", TRUE);
            $location           = $this->input->post("location", TRUE);
            $leave              = $this->input->post("leave", TRUE);
            $facebook           = $this->input->post("facebook", TRUE);
            $twitter            = $this->input->post("twitter", TRUE);
            $linkedin           = $this->input->post("linkedin", TRUE);
            $instagram          = $this->input->post("instagram", TRUE);
            $permanent_address  = $this->input->post("permanent_address", TRUE);
            $father_name        = $this->input->post("father_name", TRUE);
            $surname            = $this->input->post("surname", TRUE);
            $mother_name        = $this->input->post("mother_name", TRUE);
            $blood_group        = $this->input->post("blood_group", TRUE);
            $note               = $this->input->post("note", TRUE);
            $epf_no             = $this->input->post("epf_no", TRUE);
            $specialization     = $this->input->post("specialization", TRUE);
            $custom_field_post  = $this->input->post("custom_fields[staff]", TRUE);
            $custom_value_array = array();
            if (!empty($custom_fields)) {
                foreach ($custom_field_post as $key => $value) {
                    $check_field_type = $this->input->post("custom_fields[staff][" . $key . "]");
                    $field_value      = is_array($check_field_type) ? implode(",", $check_field_type) : $check_field_type;
                    $array_custom     = array(
                        'belong_table_id' => $id,
                        'custom_field_id' => $key,
                        'field_value'     => $field_value,
                    );
                    $custom_value_array[] = $array_custom;
                }
                $this->customfield_model->updateRecord($custom_value_array, $id, 'staff');
            }
            $specialist_separated = '';
            if(!empty($specialist)){
                $specialist_separated = implode(",", $specialist);
            }

            $data1 = array('id'           => $id,
                'employee_id'                 => $employee_id,
                'department_id'               => $department,
                'staff_designation_id'        => $designation,
                'specialist'                  => $specialist_separated,
                'qualification'               => $qualification,
                'work_exp'                    => $work_exp,
                'name'                        => $name,
                'contact_no'                  => $contact_no,
                'emergency_contact_no'        => $emergency_no,
                'email'                       => $email,
                'dob'                         => $this->customlib->dateFormatToYYYYMMDD($dob),
                'marital_status'              => $marital_status,
                'date_of_joining'             => $date_of_joining,
                'date_of_leaving'             => $date_of_leaving,
                'local_address'               => $address,
                'permanent_address'           => $permanent_address,
                'note'                        => $note,
                'surname'                     => $surname,
                'mother_name'                 => $mother_name,
                'father_name'                 => $father_name,
                'gender'                      => $gender,
                'account_title'               => $account_title,
                'bank_account_no'             => $bank_account_no,
                'bank_name'                   => $bank_name,
                'ifsc_code'                   => $ifsc_code,
                'bank_branch'                 => $bank_branch,
                'payscale'                    => '',
                'basic_salary'                => $basic_salary,
                'epf_no'                      => $epf_no,
                'contract_type'               => $contract_type,
                'specialization'              => $specialization,
                'blood_group'                 => $blood_group,
                'shift'                       => $shift,
                'location'                    => $location,
                'facebook'                    => $facebook,
                'twitter'                     => $twitter,
                'linkedin'                    => $linkedin,
                'instagram'                   => $instagram,
                'pan_number'                  => $pan_number,
                'identification_number'       => $identification_number,
                'local_identification_number' => $local_identification_number,
            );

            $insert_id = $this->staff_model->add($data1);
            $role_id   = $this->input->post("role", TRUE);
            $role_data = array('staff_id' => $id, 'role_id' => $role_id);
            $this->staff_model->update_role($role_data);
            $leave_type    = $this->input->post("leave_type_id", TRUE);
            $alloted_leave = $this->input->post("alloted_leave", TRUE);
            $altid         = $this->input->post("altid", TRUE);

            if(!empty($leave_type)){
                $i = 0;
                foreach ($leave_type as $key => $value) {

                    if (!empty($altid[$i])){

                        $data2 = array('staff_id' => $id,
                            'leave_type_id'           => $leave_type[$i],
                            'id'                      => $altid[$i],
                            'alloted_leave'           => $alloted_leave[$i],
                        );
                    }else{

                        $data2 = array('staff_id' => $id,
                            'leave_type_id'           => $leave_type[$i],
                            'alloted_leave'           => $alloted_leave[$i],
                        );
                    }

                    $this->staff_model->add_staff_leave_details($data2);                   
                    $i++;
                }
            }

			
			//new upload file function
            if (isset($_FILES["file"]) && !empty($_FILES['file']['name'])) {
                $uploaddir = $this->customlib->getFolderPath().'./uploads/staff_images/';
                if (!is_dir($uploaddir) && !mkdir($uploaddir, 0755, true)) {
                    show_error("Could not create upload directory: $uploaddir");
                }
                $file_name = $this->media_storage->fileupload("file",'./uploads/staff_images/');
                $data_img = array('id' => $id, 'image' => $file_name);
                $this->staff_model->add($data_img);
                $this->meterStaffFileReplace('file', isset($staff['image']) ? $staff['image'] : '', 'uploads/staff_images');
            }
            //new upload file function

            //new upload file function
            if (isset($_FILES["first_doc"]) && !empty($_FILES['first_doc']['name'])) {
                $uploaddir = $this->customlib->getFolderPath().'./uploads/staff_documents/' . $id . '/';
                if (!is_dir($uploaddir) && !mkdir($uploaddir, 0755, true)) {
                    show_error("Could not create upload directory: $uploaddir");
                }
                $resume_doc = $this->media_storage->fileupload("first_doc",'./uploads/staff_documents/' . $id . '/');
                $this->meterStaffFileReplace('first_doc', isset($staff['resume']) ? $staff['resume'] : '', 'uploads/staff_documents/' . $id);
            }else{
                $resume_doc = $resume;
            }

            if (isset($_FILES["second_doc"]) && !empty($_FILES['second_doc']['name'])) {
                $uploaddir = $this->customlib->getFolderPath().'./uploads/staff_documents/' . $id . '/';
                if (!is_dir($uploaddir) && !mkdir($uploaddir, 0755, true)) {
                    show_error("Could not create upload directory: $uploaddir");
                }
                $joining_letter_doc = $this->media_storage->fileupload("second_doc",'./uploads/staff_documents/' . $id . '/');
                $this->meterStaffFileReplace('second_doc', isset($staff['joining_letter']) ? $staff['joining_letter'] : '', 'uploads/staff_documents/' . $id);
            }else{
                $joining_letter_doc = $joining_letter;
            }

            if (isset($_FILES["third_doc"]) && !empty($_FILES['third_doc']['name'])) {
                $uploaddir = $this->customlib->getFolderPath().'./uploads/staff_documents/' . $id . '/';
                if (!is_dir($uploaddir) && !mkdir($uploaddir, 0755, true)) {
                    show_error("Could not create upload directory: $uploaddir");
                }
                $resignation_letter_doc = $this->media_storage->fileupload("third_doc",'./uploads/staff_documents/' . $id . '/');
                $this->meterStaffFileReplace('third_doc', isset($staff['resignation_letter']) ? $staff['resignation_letter'] : '', 'uploads/staff_documents/' . $id);
            }else{
                // Preserve the existing resignation_letter instead of wiping it (the
                // other three documents already preserve their old value here). Using
                // the stored DB value keeps the file + its quota intact on a no-upload edit.
                $resignation_letter_doc = isset($staff['resignation_letter']) ? $staff['resignation_letter'] : "";
            }

            if (isset($_FILES["fourth_doc"]) && !empty($_FILES['fourth_doc']['name'])) {
                $uploaddir = $this->customlib->getFolderPath().'./uploads/staff_documents/' . $id . '/';
                if (!is_dir($uploaddir) && !mkdir($uploaddir, 0755, true)) {
                    show_error("Could not create upload directory: $uploaddir");
                }
                $fourth_doc = $this->media_storage->fileupload("fourth_doc",'./uploads/staff_documents/' . $id . '/');
                $fourth_title = 'Other Doucment';
                $this->meterStaffFileReplace('fourth_doc', isset($staff['other_document_file']) ? $staff['other_document_file'] : '', 'uploads/staff_documents/' . $id);
            }else{
                $fourth_title = 'Other Document';
                $fourth_doc   = $other_document_file;
            }
            //new upload file function

            $data_doc = array('id' => $id, 'resume' => $resume_doc, 'joining_letter' => $joining_letter_doc, 'resignation_letter' => $resignation_letter_doc, 'other_document_name' => $fourth_title, 'other_document_file' => $fourth_doc);

            $this->staff_model->add($data_doc);
            $this->session->set_flashdata('msg', '<div class="alert alert-success">' . $this->lang->line('update_message') . '</div>');
            redirect('admin/staff');
        }
    }

    public function delete($id)
    {
        if (!$this->rbac->hasPrivilege('staff', 'can_delete')) {
            access_denied();
        }
        $data['title'] = $this->lang->line('staff_list');

        // SaaS: release the storage quota for every file this staff owns and delete the
        // physical files BEFORE the row is removed (the record holds the filenames; once
        // removed they are gone). Mirrors ss720dev. Each release sits in its own try/catch
        // so a quota-API hiccup never blocks the delete, and getUploadedFileSize/filedelete
        // no-op safely when a file is missing — so existing delete behavior is preserved.
        $staff = $this->staff_model->get($id);
        if (!empty($staff)) {
            // Profile image lives in uploads/staff_images; the documents in
            // uploads/staff_documents/<id>. other_document_name is a title, not a file, so
            // it is intentionally excluded from the release map.
            $release_map = array(
                'image'               => 'uploads/staff_images',
                'resume'              => 'uploads/staff_documents/' . $id,
                'joining_letter'      => 'uploads/staff_documents/' . $id,
                'resignation_letter'  => 'uploads/staff_documents/' . $id,
                'other_document_file' => 'uploads/staff_documents/' . $id,
            );

            foreach ($release_map as $column => $dir) {
                if (!empty($staff[$column])) {
                    // Read size before unlinking the file.
                    $kb = $this->media_storage->getUploadedFileSize($staff[$column], $dir);
                    if ($kb > 0) {
                        try {
                            $this->saasvalidation->deleteResouceQuota('storage', $kb);
                        } catch (Exception $e) {
                            log_message('error', 'SaaS storage quota release failed (staff delete ' . $column . '): ' . $e->getMessage());
                        }
                    }
                    $this->media_storage->filedelete($staff[$column], $dir);
                }
            }
        }

        $this->staff_model->remove($id);

        // SaaS: decrement the staff count usage by 1 (count-based resource: no_of_staff),
        // mirroring the +1 increment done on create — so the API usage tracks the actual
        // staff count. deleteResouceQuota throws on failure; own try/catch keeps the
        // delete flow intact if the quota API is momentarily unavailable.
        try {
            $this->saasvalidation->deleteResouceQuota('no_of_staff', 1);
        } catch (Exception $e) {
            log_message('error', 'SaaS no_of_staff quota decrement failed (staff delete): ' . $e->getMessage());
        }

        $this->session->set_flashdata('message', $this->lang->line('delete_message'));
        redirect('admin/staff');
    }

    public function disablestaff($id)
    {
        if (!$this->rbac->hasPrivilege('disable_staff', 'can_view')) {
            access_denied();
        }
        $this->staff_model->disablestaff($id);
        $staff_details = $this->notificationsetting_model->getstaffDetails($id);
        $event_data = array(
            'staff_name'    => $staff_details['name'],
            'staff_surname' => $staff_details['surname'],
            'employee_id'   => $staff_details['employee_id'],
            'status'        => $this->lang->line('staff_disable'),
        );

        $this->system_notification->send_system_notification('staff_enabale_disable', $event_data);
        redirect('admin/staff/profile/' . $id);
    }

    public function enablestaff($id)
    {
		if (!$this->rbac->hasPrivilege('disable_staff', 'can_view')) {
            access_denied();
        }
        $this->staff_model->enablestaff($id);
        $staff_details = $this->notificationsetting_model->getstaffDetails($id);
        $event_data = array(
            'staff_name'    => $staff_details['name'],
            'staff_surname' => $staff_details['surname'],
            'employee_id'   => $staff_details['employee_id'],
            'status'        => $this->lang->line('enable'),
        );

        $this->system_notification->send_system_notification('staff_enabale_disable', $event_data);
        redirect('admin/staff/profile/' . $id);
    }

    public function staffLeaveSummary()
    {
        if (!$this->rbac->hasPrivilege('apply_leave', 'can_view')) {
            access_denied();
        }
        $resultdata         = $this->staff_model->getLeaveSummary();
        $data["resultdata"] = $resultdata;
        $data['module'] = 'human_resource';
        $this->load->view("layout/header", $data);
        $this->load->view("admin/staff/staff_leave_summary", $data);
        $this->load->view("layout/footer", $data);
    }

    public function getEmployeeByRole()
    {
        if (!$this->rbac->hasPrivilege('staff', 'can_view')) {
            access_denied();
        }
        $role = $this->input->post("role");
        $data = $this->staff_model->getEmployee($role);
        echo json_encode($data);
    }

    public function dateDifference($date_1, $date_2, $differenceFormat = '%a')
    {
        $datetime1 = date_create($date_1);
        $datetime2 = date_create($date_2);
        $interval  = date_diff($datetime1, $datetime2);
        return $interval->format($differenceFormat) + 1;
    }

    public function permission($id)
    {
        if (!$this->rbac->hasPrivilege('users', 'can_edit')) {
            access_denied();
        }
        $data['title']          = $this->lang->line('add_role');
        $data['id']             = $id;
        $staff                  = $this->staff_model->get($id);
        $data['staff']          = $staff;
        $userpermission         = $this->userpermission_model->getUserPermission($id);
        $data['userpermission'] = $userpermission;
        if ($this->input->server('REQUEST_METHOD') == "POST") {
            $staff_id   = $this->input->post('staff_id');
            $prev_array = $this->input->post('prev_array');
            if (!isset($prev_array)) {
                $prev_array = array();
            }
            $module_perm  = $this->input->post('module_perm');
            $delete_array = array_diff($prev_array, $module_perm);
            $insert_diff  = array_diff($module_perm, $prev_array);
            $insert_array = array();
            if (!empty($insert_diff)) {
                foreach ($insert_diff as $key => $value) {
                    $insert_array[] = array(
                        'staff_id'      => $staff_id,
                        'permission_id' => $value,
                    );
                }
            }
            $this->userpermission_model->getInsertBatch($insert_array, $staff_id, $delete_array);

            $this->session->set_flashdata('msg', '<div class="alert alert-success text-left">' . $this->lang->line('update_message') . '</div>');
            redirect('admin/staff');
        }

        $data['module'] = 'human_resource';
        $this->load->view('layout/header', $data);
        $this->load->view('admin/staff/permission', $data);
        $this->load->view('layout/footer', $data);
    }

    public function leaverequest()
    {
        if (!$this->rbac->hasPrivilege('apply_leave', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'HR');
        $this->session->set_userdata('sub_menu', 'admin/staff/leaverequest');
        $userdata          = $this->customlib->getUserData();
        $LeaveTypes        = $this->leaverequest_model->allotedLeaveType($userdata["id"]);
   
        $data["staff_id"]  = $userdata["id"];
        $data["leavetype"] = $LeaveTypes;
        $staffRole         = $this->staff_model->getStaffRole();
        $data["staffrole"] = $staffRole;
        $data["status"]    = $this->status;
		
        $data['module'] = 'human_resource';
        $this->load->view("layout/header", $data);
        $this->load->view("admin/staff/leaverequest", $data);
        $this->load->view("layout/footer", $data);
    }

    public function getleaveapplyDatatable()
    {
        if (!$this->rbac->hasPrivilege('apply_leave', 'can_view')) {
            access_denied();
        }
		$superadmin_restriction      = $this->sch_setting_detail->superadmin_restriction;
		
        $userdata    = $this->customlib->getUserData();
        $dt_response = $this->leaverequest_model->getAllleaveapplyRecord($userdata["id"]);
 
        $this->patient_data   = $this->session->userdata('hospitaladmin');

        $dt_response = json_decode($dt_response);
        $dt_data     = array();
        if (!empty($dt_response->data)) {
            foreach ($dt_response->data as $key => $value) {
                $status = $this->status;
                if (!empty($value->designation)) {

                    $designation = " (" . $value->designation . " - " . $value->employee_id . ")";
                } else {
                    if (!empty($value->employee_id)) {
                        $designation = " (" . $value->employee_id . ")";
                    } else {
                        $designation = '';
                    }
                }
                 $label ="";

                if ($value->status == "approve") {
                    $label = "class='label label-success'";
                } else if ($value->status == "pending") {
                    $label = "class='label label-warning'";
                } else if ($value->status == "disapprove") {
                    $label = "class='label label-danger'";
                }

                $row = array();
                //====================================
                $action = "<a href='#leavedetails' onclick='getRecord(" . $value->id . ")' class='btn btn-default btn-xs'  data-bs-toggle='tooltip'  role='button' title='" . $this->lang->line('view') . "'><i class='fa fa-reorder'></i></a>";

                if ($this->rbac->hasPrivilege('apply_leave', 'can_delete')) {
                    if ($value->status != "approve") {
                        $action .= "<a href='#leavedetails' onclick='deleterecord(" . $value->id . ")' class='btn btn-default btn-xs'  data-bs-toggle='tooltip' title='" . $this->lang->line('delete') . "'><i class='fa fa-trash'></i></a>";
                    }
                }
                $leave_date = date($this->customlib->YYYYMMDDTodateFormat($value->leave_from)) . ' - ' . date($this->customlib->YYYYMMDDTodateFormat($value->leave_to));
                
                $updated_by = '';
                if($value->status_updated_by){
					
					if($superadmin_restriction == 'disabled' && $value->role_id == 7){						
						$updated_by = '';
					}else{
						$updated_by = $this->lang->line('by') . ' ' . composeStaffNameByString($value->apply_by_name, $value->apply_by_surname, $value->apply_by_employee_id);
					}					
					
                }
                    
                $status_leave = "<small' " . $label . " ' >" . $value->status . "</small>" .' '. $updated_by;
                if( $this->patient_data['superadmin_restriction'] == "enabled" && $value->role_id==7){
                    $status_leave = "<small' " . $label . " ' >" . $value->status . "</small>";
                }
                
                //==============================
                $row[] = $value->name . ' ' . $value->surname . $designation;
                $row[] = $value->type;
                $row[] = $leave_date;
                $row[] = $value->leave_days;
                $row[] = $this->customlib->YYYYMMDDTodateFormat($value->date);
                $row[]     = $status_leave;
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

    public function change_password($id)
    {
        $sessionData = $this->session->userdata('hospitaladmin');
        $userdata    = $this->customlib->getUserData();

        $this->form_validation->set_rules('new_pass', $this->lang->line('password'), 'trim|required|xss_clean|matches[confirm_pass]');
        $this->form_validation->set_rules('confirm_pass', $this->lang->line('confirm_password'), 'trim|required|xss_clean');
        if ($this->form_validation->run() == false) {

            $msg = array(
                'new_pass'     => form_error('new_pass'),
                'confirm_pass' => form_error('confirm_pass'),
            );

            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {

            if (!empty($id)) {
                $newdata = array(
                    'id'       => $id,
                    'password' => $this->enc_lib->passHashEnc($this->input->post('new_pass')),
                );

                $query2 = $this->admin_model->saveNewPass($newdata);

                if ($query2) {
                    $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('password_changed_successfully'));
                } else {
                    $array = array('status' => 'fail', 'error' => '', 'message' => $this->lang->line('password_not_changed'));
                }
            } else {
                $array = array('status' => 'fail', 'error' => '', 'message' => $this->lang->line('password_not_changed'));
            }
        }
        echo json_encode($array);
    }

    public function import()
    {
        if (!$this->rbac->hasPrivilege('staff', 'can_add')) {
            access_denied();
        }
        $data['field'] = array(
            "staff_id"                 => "staff_id",
            "first_name"               => "first_name",
            "last_name"                => "last_name",
            "father_name"              => "father_name",
            "mother_name"              => "mother_name",
            "email_login_username"     => "email",
            "gender"                   => "gender",
            "date_of_birth"            => "date_of_birth",
            "date_of_joining"          => "date_of_joining",
            "phone"                    => "phone",
            "emergency_contact_number" => "emergency_contact_number",
            "marital_status"           => "marital_status",
            "current_address"          => "current_address",
            "permanent_address"        => "permanent_address",
            "qualification"            => "qualification",
            "work_experience"          => "work_experience",
            "note"                     => "note",
        ); 
        $roles               = $this->role_model->get();
        $data["roles"]       = $roles;
        $designation         = $this->staff_model->getStaffDesignation();
        $data["designation"] = $designation;
        $department          = $this->staff_model->getDepartment();
        $data["department"]  = $department;

        $this->form_validation->set_rules('file', $this->lang->line('file'), 'callback_handle_csv_upload');
        $this->form_validation->set_rules('role', $this->lang->line('role'), 'required|xss_clean');

        if ($this->form_validation->run() == false) {
            $data['module'] = 'human_resource';
            $this->load->view("layout/header", $data);
            $this->load->view("admin/staff/import/import", $data);
            $this->load->view("layout/footer", $data);
        } else {

            if (isset($_FILES["file"]) && !empty($_FILES['file']['name'])) {

                $ext = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
                if ($ext == 'csv') {

                    $file = $_FILES['file']['tmp_name'];
                    $this->load->library('CSVReader');
                    $result = $this->csvreader->parse_file($file);

                    $rowcount = 0;

                    if (!empty($result)) {

                        foreach ($result as $r_key => $r_value) {

                            $check_exists      = $this->staff_model->import_check_data_exists($result[$r_key]['name'], $result[$r_key]['employee_id']);
                            $check_emailexists = $this->staff_model->import_check_email_exists($result[$r_key]['name'], $result[$r_key]['employee_id']);

                            if ($check_exists == 0 && $check_emailexists == 0) {

                                $result[$r_key]['employee_id']          = $this->encoding_lib->toUTF8($result[$r_key]['employee_id']);
                                $result[$r_key]['qualification']        = $this->encoding_lib->toUTF8($result[$r_key]['qualification']);
                                $result[$r_key]['work_exp']             = $this->encoding_lib->toUTF8($result[$r_key]['work_exp']);
                                $result[$r_key]['name']                 = $this->encoding_lib->toUTF8($result[$r_key]['name']);
                                $result[$r_key]['surname']              = $this->encoding_lib->toUTF8($result[$r_key]['surname']);
                                $result[$r_key]['father_name']          = $this->encoding_lib->toUTF8($result[$r_key]['father_name']);
                                $result[$r_key]['mother_name']          = $this->encoding_lib->toUTF8($result[$r_key]['mother_name']);
                                $result[$r_key]['contact_no']           = $this->encoding_lib->toUTF8($result[$r_key]['contact_no']);
                                $result[$r_key]['emergency_contact_no'] = $this->encoding_lib->toUTF8($result[$r_key]['emergency_contact_no']);
                                $result[$r_key]['email']                = $this->encoding_lib->toUTF8($result[$r_key]['email']);
                                $result[$r_key]['dob']                  = $this->encoding_lib->toUTF8($result[$r_key]['dob']);
                                $result[$r_key]['marital_status']       = $this->encoding_lib->toUTF8($result[$r_key]['marital_status']);
                                $result[$r_key]['date_of_joining']      = $this->encoding_lib->toUTF8($result[$r_key]['date_of_joining']);
                                $result[$r_key]['date_of_leaving']      = $this->encoding_lib->toUTF8($result[$r_key]['date_of_leaving']);
                                $result[$r_key]['local_address']        = $this->encoding_lib->toUTF8($result[$r_key]['local_address']);
                                $result[$r_key]['permanent_address']    = $this->encoding_lib->toUTF8($result[$r_key]['permanent_address']);
                                $result[$r_key]['note']                 = $this->encoding_lib->toUTF8($result[$r_key]['note']);
                                $result[$r_key]['gender']               = $this->encoding_lib->toUTF8($result[$r_key]['gender']);
                                $result[$r_key]['account_title']        = $this->encoding_lib->toUTF8($result[$r_key]['account_title']);
                                $result[$r_key]['bank_account_no']      = $this->encoding_lib->toUTF8($result[$r_key]['bank_account_no']);
                                $result[$r_key]['bank_name']            = $this->encoding_lib->toUTF8($result[$r_key]['bank_name']);
                                $result[$r_key]['ifsc_code']            = $this->encoding_lib->toUTF8($result[$r_key]['ifsc_code']);
                                $result[$r_key]['payscale']             = $this->encoding_lib->toUTF8($result[$r_key]['payscale']);
                                $result[$r_key]['basic_salary']         = $this->encoding_lib->toUTF8($result[$r_key]['basic_salary']);
                                $result[$r_key]['epf_no']               = $this->encoding_lib->toUTF8($result[$r_key]['epf_no']);
                                $result[$r_key]['contract_type']        = $this->encoding_lib->toUTF8($result[$r_key]['contract_type']);
                                $result[$r_key]['shift']                = $this->encoding_lib->toUTF8($result[$r_key]['shift']);
                                $result[$r_key]['location']             = $this->encoding_lib->toUTF8($result[$r_key]['location']);
                                $result[$r_key]['facebook']             = $this->encoding_lib->toUTF8($result[$r_key]['facebook']);
                                $result[$r_key]['twitter']              = $this->encoding_lib->toUTF8($result[$r_key]['twitter']);
                                $result[$r_key]['linkedin']             = $this->encoding_lib->toUTF8($result[$r_key]['linkedin']);
                                $result[$r_key]['instagram']            = $this->encoding_lib->toUTF8($result[$r_key]['instagram']);
                                $result[$r_key]['resume']               = $this->encoding_lib->toUTF8($result[$r_key]['resume']);
                                $result[$r_key]['joining_letter']       = $this->encoding_lib->toUTF8($result[$r_key]['joining_letter']);
                                $result[$r_key]['resignation_letter']   = $this->encoding_lib->toUTF8($result[$r_key]['resignation_letter']);
                                $result[$r_key]['user_id']              = $this->input->post('role');
                                $result[$r_key]['staff_designation_id'] = $this->input->post('designation');
                                $result[$r_key]['department_id']        = $this->input->post('department');
                                $result[$r_key]['is_active']            = 1;

                                $password = $this->role->get_random_password($chars_min = 6, $chars_max = 6, $use_upper_case = false, $include_numbers = true, $include_special_chars = false);

                                $result[$r_key]['password'] = $this->enc_lib->passHashEnc($password);

                                $role_array = array('role_id' => $this->input->post('role'), 'staff_id' => 0);

                                $insert_id = $this->staff_model->batchInsert($result[$r_key], $role_array);
                                $staff_id  = $insert_id;
                                if ($staff_id) {

                                    $teacher_login_detail = array('id' => $staff_id, 'credential_for' => 'staff', 'username' => $result[$r_key]['email'], 'password' => $password, 'contact_no' => $result[$r_key]['contact_no'], 'email' => $result[$r_key]['email']);

                                    $this->mailsmsconf->mailsms('login_credential', $teacher_login_detail);
                                }
                                $rowcount++;
                            }
                        } ///Result loop
                    } //Not emprty l

                    $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('records_found_in_CSV_file_total') . $rowcount . $this->lang->line('records_imported_successfully'));
                }
            } else {
                $msg = array(
                    'e' => $this->lang->line('the_file_field_is_required'),
                );
                $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
            }

            $this->session->set_flashdata('msg', '<div class="alert alert-success text-center">' . $this->lang->line('total') . ' ' . count($result) . " " . $this->lang->line('records_found_in_CSV_file_total') . ' ' . $rowcount . ' ' . $this->lang->line('records_imported_successfully') . '</div>');
            redirect('admin/staff/import');
        }
    }

    public function handle_csv_upload()
    {
        $error = "";
        if (isset($_FILES["file"]) && !empty($_FILES['file']['name'])) {
            $allowedExts = array('csv');
            $mimes       = array('text/csv',
                'text/plain',
                'application/csv',
                'text/comma-separated-values',
                'application/excel',
                'application/vnd.ms-excel',
                'application/vnd.msexcel',
                'text/anytext',
                'application/octet-stream',
                'application/txt');
            $temp      = explode(".", $_FILES["file"]["name"]);
            $extension = end($temp);
            if ($_FILES["file"]["error"] > 0) {
                $error .= "Error opening the file<br />";
            }
            if (!in_array($_FILES['file']['type'], $mimes)) {
                $error .= "Error opening the file<br />";
                $this->form_validation->set_message('handle_csv_upload', $this->lang->line('file_type_not_allowed'));
                return false;
            }
            if (!in_array($extension, $allowedExts)) {
                $error .= "Error opening the file<br />";
                $this->form_validation->set_message('handle_csv_upload', $this->lang->line('extension_not_allowed'));
                return false;
            }
            if ($error == "") {
                return true;
            }
        } else {
            $this->form_validation->set_message('handle_csv_upload', $this->lang->line('please_select_file'));
            return false;
        }
    }

    public function exportformat()
    {
        $this->load->helper('download');
        $filepath = "./backend/import/staff_csvfile.csv";
        $data     = file_get_contents($filepath);
        $name     = 'staff_csvfile.csv';

        force_download($name, $data);
    }

    public function handle_image_upload($str, $var)
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
                    $this->form_validation->set_message('handle_image_upload', $this->lang->line('file_type_not_allowed'));
                    return false;
                }

                if (!in_array(strtolower($ext), $allowed_extension) || !in_array($file_type, $allowed_mime_type)) {
                    $this->form_validation->set_message('handle_image_upload', $this->lang->line('file_extension_not_allowed'));
                    return false;
                }
				
                if ($file_size > $image_validate['upload_size']) {
                    $this->form_validation->set_message('handle_image_upload', $this->lang->line('file_size_shoud_be_less_than') . number_format($image_validate['upload_size'] / 1048576, 2) . " MB");
                    return false;
                }
            } else {
                $this->form_validation->set_message('handle_image_upload', $this->lang->line('file_type_extension_not_allowed'));
                return false;
            }

            return true;
        }
        return true;
    }

}
