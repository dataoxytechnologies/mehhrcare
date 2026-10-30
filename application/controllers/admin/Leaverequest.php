<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Leaverequest extends Admin_Controller
{
	public $contract_type;
	public $marital_status;
	public $payment_mode;
	public $payroll_status;
	public $staff_attendance;
	public $status;

    public function __construct()
    {
        parent::__construct();

        $this->config->load("payroll");
        $this->load->library('datatables');
        $this->config->load('image_valid');
        $this->contract_type    = $this->config->item('contracttype');
        $this->marital_status   = $this->config->item('marital_status');
        $this->staff_attendance = $this->config->item('staffattendance');
        $this->payroll_status   = $this->config->item('payroll_status');
        $this->payment_mode     = $this->config->item('payment_mode');
        $this->status           = $this->config->item('status');
        $this->load->library('system_notification');
        $this->load->library('SaasValidation');
    }

    /**
     * SaaS storage pre-check (form_validation callback).
     * Returns false (blocking the save) when the uploaded leave document would
     * push the tenant over its storage quota. SaasValidation sets the message.
     */
    public function validateCanUploadFile($str, $params_string)
    {
        $storage_array = array_map('trim', explode(',', $params_string));
        return $this->saasvalidation->validateCanUploadFile($str, $storage_array);
    }

    public function approveleaverequest()
    {
        if (!$this->rbac->hasPrivilege('approve_leave_request', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'HR');
        $this->session->set_userdata('sub_menu', 'admin/leaverequest/leaverequest');
        $LeaveTypes        = $this->staff_model->getLeaveType();
        $userdata          = $this->customlib->getUserData();
        $data["leavetype"] = $LeaveTypes;
        $staffRole         = $this->staff_model->getStaffRole();
        $data["staffrole"] = $staffRole;
        $data["status"]    = $this->status;
        $this->load->view("layout/header", $data);
        $this->load->view("admin/staff/approveleaverequest", $data);
        $this->load->view("layout/footer", $data);
    }

    public function getleaverequestDatatable()
    {
        $dt_response = $this->leaverequest_model->getAllleaverequestRecord();
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

                 $label="";

                if ($value->status == "approve") {
                    $label = "class='label label-success'";
                } else if ($value->status == "pending") {
                    $label = "class='label label-warning'";
                } else if ($value->status == "disapprove") {
                    $label = "class='label label-danger'";
                }

                $row = array();
                //====================================

                $first_action = "<a href='#leavedetails' onclick='getRecord(" . $value->id . ")' class='btn btn-default btn-xs'  data-bs-toggle='tooltip'  role='button' title='" . $this->lang->line('view') . "'><i class='fa fa-reorder'></i></a>";

                $status_leave = "";
                $action       = '';
                if ($value->applied_by == $this->customlib->getAdminSessionUserName()) {
                    if ($this->rbac->hasPrivilege('approve_leave_request', 'can_edit')) {
                        $action .= "<a href='#addleave' onclick='editRecord(" . $value->id . ")' class='btn btn-default btn-xs'  data-bs-toggle='tooltip' title='" . $this->lang->line('edit') . "'><i class='fa fa-pencil'></i></a>";
                    }

                    if (!empty($value->document_file)) {
                        $action .= "<a href=" . base_url() . 'admin/staff/download/' . $value->staff_id . '/' . $value->ddocument_file . " onclick='' class='btn btn-default btn-xs'  data-bs-toggle='tooltip' title='" . $this->lang->line('download') . "'><i class='fa fa-download'></i></a>";
                    }
                }
				
				if ($this->rbac->hasPrivilege('approve_leave_request', 'can_delete')) {
                    if ($value->status != "approve") {
                        $action .= "<a href='#leavedetails' onclick='deleterecord(" . $value->id . ")' class='btn btn-default btn-xs'  data-bs-toggle='tooltip' title='" . $this->lang->line('delete') . "'><i class='fa fa-trash'></i></a>";
                    }
                }

                $leave_date = $this->customlib->YYYYMMDDTodateFormat($value->leave_from) . ' - ' . $this->customlib->YYYYMMDDTodateFormat($value->leave_to);
                
                if ($value->apply_by_name != '') {
                    $status_leave = "<small' " . $label . " ' >" . $this->lang->line($value->status) . "</small>" . ' ' . $this->lang->line('by') . ' ' . composeStaffNameByString($value->apply_by_name, $value->apply_by_surname, $value->apply_by_employee_id);
                } else {
                    $status_leave = "<small' " . $label . " ' >" . $this->lang->line($value->status) . "</small>";
                }
                
                if($value->approved_date){
                    $approved_date = $this->customlib->YYYYMMDDTodateFormat($value->approved_date);
                }else{
                    $approved_date = '';
                }

                //==============================
                $row[] = $value->name . ' ' . $value->surname . $designation;
                $row[] = $value->type;
                $row[] = $leave_date;
                $row[] = $value->leave_days;
                $row[]     = $this->customlib->YYYYMMDDTodateFormat($value->date);               
                $row[]     = $status_leave;
                $row[]     = $approved_date;
                $row[]     = $first_action . $action;
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

    public function deleteRecord()
    {
        $id = $this->input->post("id", TRUE);
        if (!empty($id)) {
            // SaaS: capture the document BEFORE the row is deleted so its storage can
            // be released and the physical file removed (deleteleave only drops the row).
            $existing_leave = $this->leaverequest_model->getLeaveRequestById($id);
            $result = $this->staff_model->deleteleave($id);
            if ($result !== false && !empty($existing_leave['document_file'])) {
                $doc_dir = 'uploads/staff_documents/' . $existing_leave['staff_id'];
                // Read size before unlinking the file.
                $kb = $this->media_storage->getUploadedFileSize($existing_leave['document_file'], $doc_dir);
                if ($kb > 0) {
                    try {
                        $this->saasvalidation->deleteResouceQuota('storage', $kb);
                    } catch (Exception $e) {
                        log_message('error', 'SaaS storage quota release failed (leaverequest deleteRecord): ' . $e->getMessage());
                    }
                }
                $this->media_storage->filedelete($existing_leave['document_file'], $doc_dir);
            }
            $array  = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('delete_message'));
        } else {
            $array = array('status' => 'fail', 'error' => '', 'message' => '');
        }
        echo json_encode($array);
    }

    public function countLeave($id)
    {
        $lid               = $this->input->post("lid", TRUE);
        $alloted_leavetype = $this->leaverequest_model->allotedLeaveType($id);
        $i    = 0;
        $html = "<select name='leave_type' id='leave_type' class='form-control'><option value=''>" . $this->lang->line('select') . "</option>";
        $data = array();
        if (!empty($alloted_leavetype[0]["alloted_leave"])) {
            foreach ($alloted_leavetype as $key => $value) {
                $count_leaves[]            = $this->leaverequest_model->countLeavesData($id, $value["leave_type_id"]);
                $data[$i]['type']          = $value["type"];
                $data[$i]['id']            = $value["leave_type_id"];
                $data[$i]['alloted_leave'] = $value["alloted_leave"];
                $data[$i]['approve_leave'] = $count_leaves[$i]['approve_leave'];
                $i++;
            }

            foreach ($data as $dkey => $dvalue) {
                if (!empty($dvalue["alloted_leave"])) {
                    if ($lid == $dvalue["id"]) {
                        $a = "selected";
                    } else {
                        $a = "";
                    }

                    if ($dvalue["alloted_leave"] == "") {
                        $available = $dvalue["approve_leave"];
                    } else {
                        $available = $dvalue["alloted_leave"] - $dvalue["approve_leave"];
                    }
					
                    if ($available > 0) {
                        $html .= "<option value=" . $dvalue["id"] . " $a>" . $dvalue["type"] . " (" . $available . ")" . "</option>";
                    }
                }
            }
        }
        $html .= "</select>";
        echo $html;
    }

    public function leaveStatus()
    {
        if ((!$this->rbac->hasPrivilege('approve_leave_request', 'can_edit'))) {
            access_denied();
        }

        $leave_request_id = $this->input->post("leave_request_id", TRUE);
        $status           = $this->input->post("status", TRUE);
        $adminRemark      = $this->input->post("detailremark", TRUE);
        
        if($status == 'approve' || $status == 'disapprove'){
                $approved_date = date("Y-m-d");
            }else{
                $approved_date = NULL;
        }
                
        $data             = array(
            'status'            => $status,
            'approved_date'            => $approved_date,
            'admin_remark'      => $adminRemark,
            'status_updated_by' => $this->customlib->getStaffID(),
        );

        $this->leaverequest_model->changeLeaveStatus($data, $leave_request_id);
        $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));

        $result     = $this->staff_model->getLeaveRecord($leave_request_id);
        $leave_days = $this->dateDifference($result->leave_from, $result->leave_to);
 
        if($status == 'approve'){
            $system_status =   $this->lang->line('approved');
        }elseif($status == 'disapprove'){
            $system_status =   $this->lang->line('disapproved');
        }else{
            $system_status =   '';
        }

        $event_data = array(
            'apply_date'    => $this->customlib->YYYYMMDDTodateFormat($result->date),
            'leave_type'    => $result->type,
            'leave_date'    => $this->customlib->YYYYMMDDTodateFormat($result->leave_from) . ' - ' . $this->customlib->YYYYMMDDTodateFormat($result->leave_to),
            'days'          => $leave_days,
            'role_id'       => $result->role_id,
            'staff_id'      => $result->staff_id,
            'staff_name'    => $result->name,
            'staff_surname' => $result->surname,
            'employee_id'   => $this->input->post('staff_id', TRUE),
            'leave_status'  => $system_status,
        );
        $this->system_notification->send_system_notification('staff_leave_status', $event_data);
        echo json_encode($array);
    }

    public function leaveRecord()
    {
        $id     = $this->input->post("id", TRUE);
        $result = $this->staff_model->getLeaveRecord($id);

        if ($result->applier_employee_id != '') {
            $result->applied_by = composeStaffNameByString($result->applier_name, $result->applier_surname, $result->applier_employee_id);
        } else {
            $result->applied_by = "";
        }

        if ($result->employee_id != '') {
            $result->staffname = composeStaffNameByString($result->name, $result->surname, $result->employee_id);
        } else {
            $result->staffname = "";
        }

        $leave_from        = date("m/d/Y", strtotime($result->leave_from));
        $result->leavefrom = $leave_from;
        $leave_to          = date("m/d/Y", strtotime($result->leave_to));
        $result->leaveto   = $leave_to;
        $result->days      = $this->dateDifference($result->leave_from, $result->leave_to);
        $result->status    = $result->status;
        echo json_encode($result);
    }

    public function dateDifference($date_1, $date_2, $differenceFormat = '%a')
    {
        $datetime1 = date_create($date_1);
        $datetime2 = date_create($date_2);
        $interval  = date_diff($datetime1, $datetime2);
        return $interval->format($differenceFormat) + 1;
    }

    public function addLeave()
    {
        $role         = $this->input->post("role", TRUE);
        $empid        = $this->input->post("empname", TRUE);
        $applied_date = $this->input->post("applieddate", TRUE);
        $leavetype    = $this->input->post("leave_type", TRUE);
        $reason       = $this->input->post("reason", TRUE);
        $remark       = $this->input->post("remark", TRUE);
        $status       = $this->input->post("addstatus", TRUE);
        $request_id   = $this->input->post("leaverequestid", TRUE);
        $this->form_validation->set_rules('role', $this->lang->line('role'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('empname', $this->lang->line('name'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('leave_from_date', $this->lang->line('leave_from_date'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('leave_to_date', $this->lang->line('leave_to_date'), 'trim|required|xss_clean');    
        
        $this->form_validation->set_rules('leave_type', $this->lang->line('leave_type'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('applieddate', $this->lang->line('apply_date'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('userfile', $this->lang->line('image'), 'callback_validateCanUploadFile[userfile]');

        if ($this->form_validation->run() == false) {
            $msg = array(
                'role'        => form_error('role'),
                'empname'     => form_error('empname'),
                'applieddate' => form_error('applieddate'),
                'leavedates'  => form_error('leavedates'),
                'leave_type'  => form_error('leave_type'),
                'leave_from_date'  => form_error('leave_from_date'),
                'leave_to_date'  => form_error('leave_to_date'),
            );

            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {

            $leave_from_date         = $this->input->post("leave_from_date", TRUE);
            $leave_to_date         = $this->input->post("leave_to_date", TRUE);
            
            $leavefrom = $this->customlib->dateFormatToYYYYMMDD($leave_from_date);
            $leaveto   = $this->customlib->dateFormatToYYYYMMDD($leave_to_date);
            $staff_id  = $empid;

            $applied_by = $this->customlib->getStaffID();
            $leave_days = $this->dateDifference($leavefrom, $leaveto);

            $staff_leave = $this->leaverequest_model->myallotedLeaveType($staff_id, $leavetype);
            $approve_leave  = $this->leaverequest_model->countLeavesData($staff_id, $leavetype);
            $pending_leave = $staff_leave['alloted_leave'] - $approve_leave['approve_leave'] ;

            if($pending_leave >= $leave_days)
            {
            if (!empty($request_id))
             {
                $data = array('id' => $request_id,
                    'staff_id'         => $staff_id,
                    'date'             => $this->customlib->dateFormatToYYYYMMDD($applied_date),
                    'leave_type_id'    => $leavetype,
                    'leave_days'       => $leave_days,
                    'leave_from'       => $leavefrom,
                    'leave_to'         => $leaveto,
                    'employee_remark'  => $reason,
                    'status'           => $status,
                    'admin_remark'     => $remark,
                    'applied_by'       => $applied_by,
                );

                $this->leaverequest_model->addLeaveRequest($data);

                if (isset($_FILES["userfile"]) && !empty($_FILES['userfile']['name'])) {
                    $uploaddir = './uploads/staff_documents/' . $staff_id . '/';
                    if (!is_dir($uploaddir) && !mkdir($uploaddir)) {
                        die("Error creating folder $uploaddir");
                    }

                    // SaaS: capture the existing document for quota diff + cleanup (edit).
                    $existing_leave = $this->leaverequest_model->getLeaveRequestById($request_id);
                    $old_doc = (!empty($existing_leave['document_file'])) ? $existing_leave['document_file'] : '';
                    $old_kb  = (!empty($old_doc)) ? $this->media_storage->getUploadedFileSize($old_doc, 'uploads/staff_documents/' . $staff_id) : 0;

                    $file_name = $this->media_storage->fileupload("userfile",'./uploads/staff_documents/' . $staff_id . '/');
                    $data_img = array('id' => $request_id, 'document_file' => $file_name, 'status' => $status);
                    $this->leaverequest_model->addLeaveRequest($data_img);

                    // SaaS: adjust storage quota by the size difference (new vs replaced).
                    try {
                        $new_kb = $this->media_storage->getTmpFileSize('userfile');
                        if ($old_kb > $new_kb) {
                            $this->saasvalidation->deleteResouceQuota('storage', $old_kb - $new_kb);
                        } elseif ($new_kb > $old_kb) {
                            $this->saasvalidation->updateResouceQuota('storage', $new_kb - $old_kb);
                        }
                    } catch (Exception $e) {
                        log_message('error', 'SaaS storage quota update failed (leaverequest addLeave edit): ' . $e->getMessage());
                    }

                    // Remove the replaced old document (new upload uses a unique name).
                    if (!empty($old_doc) && $old_doc !== $file_name) {
                        $this->media_storage->filedelete($old_doc, 'uploads/staff_documents/' . $staff_id);
                    }
                }


            } else {
                
                if($status == 'approve' || $status == 'disapprove'){
                    $approved_date = date("Y-m-d");
                }else{
                    $approved_date = NULL;
                }
                
                $addLeaveRequest = array(
                    'staff_id'          => $staff_id,
                    'date'              => $this->customlib->dateFormatToYYYYMMDD($applied_date),
                    'leave_days'        => $leave_days,
                    'leave_type_id'     => $leavetype,
                    'leave_from'        => $leavefrom,
                    'leave_to'          => $leaveto,
                    'employee_remark'   => $reason,
                    'status'            => $status,
                    'approved_date'      => $approved_date,
                    'admin_remark'      => $remark,
                    'applied_by'        => $applied_by,
                    'status_updated_by' => $this->customlib->getStaffID(),
                );

                $insert_id = $this->leaverequest_model->addLeaveRequest($addLeaveRequest);

                if (isset($_FILES["userfile"]) && !empty($_FILES['userfile']['name'])) {

                    $uploaddir = './uploads/staff_documents/' . $staff_id . '/';
                        if (!is_dir($uploaddir) && !mkdir($uploaddir)) {
                            die("Error creating folder $uploaddir");
                        }

                    $file_name = $this->media_storage->fileupload("userfile",'./uploads/staff_documents/' . $staff_id . '/');
                    $data_img = array('id' => $insert_id, 'document_file' => $file_name, 'status' => $status);
                    $this->leaverequest_model->addLeaveRequest($data_img);

                    // SaaS: add the new document size to the storage quota usage.
                    try {
                        $new_kb = $this->media_storage->getTmpFileSize('userfile');
                        if ($new_kb > 0) {
                            $this->saasvalidation->updateResouceQuota('storage', $new_kb);
                        }
                    } catch (Exception $e) {
                        log_message('error', 'SaaS storage quota update failed (leaverequest addLeave): ' . $e->getMessage());
                    }
                }
            }

            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
            $staff_details     = $this->notificationsetting_model->getstaffDetails($staff_id);
            $leavetype_details = $this->notificationsetting_model->getleavetypesDetails($leavetype);

            $event_data = array(
                'apply_date'    => $this->customlib->YYYYMMDDTodateFormat($applied_date),
                'leave_type'    => $leavetype_details['type'],
                'leave_date'    => $this->customlib->YYYYMMDDTodateFormat($leavefrom) . ' - ' . $this->customlib->YYYYMMDDTodateFormat($leaveto),
                'days'          => $leave_days,
                'role_id'       => $role,
                'staff_id'      => $staff_details['id'],
                'staff_name'    => $staff_details['name'],
                'staff_surname' => $staff_details['surname'],
                'employee_id'   => $staff_details['employee_id'],
                'leave_status'  => $this->lang->line($status),
            );

            $this->system_notification->send_system_notification('staff_leave', $event_data);

            }else{
                 $msg = array(
                    'applieddate' => $this->lang->line('selected_leave_days') . " > " . $this->lang->line('available_leaves'),
                );

                $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
            }
        }

        echo json_encode($array);
    }

    public function add_staff_leave()
    {
        $userdata     = $this->customlib->getUserData();
        $applied_date = $this->input->post("applieddate", TRUE);
        $leavetype    = $this->input->post("leave_type", TRUE);
        $reason       = $this->input->post("reason", TRUE);
        $remark       = '';
        $status       = 'pending';
        $request_id   = $this->input->post("leaverequestid", TRUE);
        $this->form_validation->set_rules('applieddate', $this->lang->line('apply_date'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('leave_from_date', $this->lang->line('leave_from_date'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('leave_to_date', $this->lang->line('leave_to_date'), 'trim|required|xss_clean');        
        $this->form_validation->set_rules('leave_type', $this->lang->line('leave_type'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('userfile', $this->lang->line('image'), 'callback_handle_doc_upload[userfile]|callback_validateCanUploadFile[userfile]');

        if ($this->form_validation->run() == false) {

            $msg = array(
                'applieddate' => form_error('applieddate'),
                'leave_from_date'  => form_error('leave_from_date'),
                'leave_to_date'  => form_error('leave_to_date'),
                'leave_type'  => form_error('leave_type'),
                'userfile'    => form_error('userfile'),
            );

            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            
            $leave_from_date =    $this->input->post("leave_from_date", TRUE);
            $leave_to_date =    $this->input->post("leave_to_date", TRUE);
            $staff_id    = $userdata["id"];
            $leavefrom   = $this->customlib->dateFormatToYYYYMMDD($leave_from_date);
            $leaveto     = $this->customlib->dateFormatToYYYYMMDD($leave_to_date);
            $leave_days  = $this->dateDifference($leavefrom, $leaveto);
            $staff_leave = $this->leaverequest_model->myallotedLeaveType($staff_id, $leavetype);
            $approve_leave  = $this->leaverequest_model->countLeavesData($staff_id, $leavetype);
            $pending_leave = $staff_leave['alloted_leave'] - $approve_leave['approve_leave'] ;           
          
            if($pending_leave >= $leave_days)
            {
            $staff_id = $userdata["id"];

            //new upload method            
			
				if (isset($_FILES["userfile"]) && !empty($_FILES['userfile']['name'])) {
                    $uploaddir = './uploads/staff_documents/' . $staff_id . '/';
                    if (!is_dir($uploaddir) && !mkdir($uploaddir)) {
                        die("Error creating folder $uploaddir");
                    }

                    // SaaS: on edit (request_id present), capture the existing document
                    // for quota diff + cleanup. document_file stores a bare filename under
                    // uploads/staff_documents/<staff_id>. For a fresh add, old_kb stays 0.
                    $old_doc = '';
                    $old_kb  = 0;
                    if (!empty($request_id)) {
                        $existing_leave = $this->leaverequest_model->getLeaveRequestById($request_id);
                        $old_doc = (!empty($existing_leave['document_file'])) ? $existing_leave['document_file'] : '';
                        $old_kb  = (!empty($old_doc)) ? $this->media_storage->getUploadedFileSize($old_doc, 'uploads/staff_documents/' . $staff_id) : 0;
                    }

                    $document = $this->media_storage->fileupload("userfile", $uploaddir);

                    // SaaS: adjust storage quota by the size difference (new vs replaced).
                    // For a fresh add this is add-only (old_kb = 0).
                    try {
                        $new_kb = $this->media_storage->getTmpFileSize('userfile');
                        if ($old_kb > $new_kb) {
                            $this->saasvalidation->deleteResouceQuota('storage', $old_kb - $new_kb);
                        } elseif ($new_kb > $old_kb) {
                            $this->saasvalidation->updateResouceQuota('storage', $new_kb - $old_kb);
                        }
                    } catch (Exception $e) {
                        log_message('error', 'SaaS storage quota update failed (leaverequest add_staff_leave): ' . $e->getMessage());
                    }

                    // Remove the replaced old document (new upload uses a unique name).
                    if (!empty($old_doc) && $old_doc !== $document) {
                        $this->media_storage->filedelete($old_doc, 'uploads/staff_documents/' . $staff_id);
                    }
                } else {
                    $document = '';
                }
				
            //new upload method

            $applied_by = $this->customlib->getStaffID();
            $leave_days = $this->dateDifference($leavefrom, $leaveto);            
            
            if($status == 'approve' || $status == 'disapprove'){
                $approved_date = date("Y-m-d");
            }else{
                $approved_date = NULL;
            }
                
            if (!empty($request_id)) {
                $data = array('id' => $request_id,
                    'staff_id'         => $staff_id,
                    'date'             => $this->customlib->dateFormatToYYYYMMDD($applied_date),
                    'leave_type_id'    => $leavetype,
                    'leave_days'       => $leave_days,
                    'leave_from'       => $leavefrom,
                    'leave_to'         => $leaveto,
                    'employee_remark'  => $reason,
                    'status'           => $status,
                    'approved_date'           => $approved_date,
                    'admin_remark'     => $remark,
                    'applied_by'       => $applied_by,
                    'document_file'    => $document,
                    'status_updated_by'    => 1);
            } else {
                $data = array('staff_id' => $staff_id, 
					'date' => $this->customlib->dateFormatToYYYYMMDD($applied_date), 
					'leave_days' => $leave_days, 
					'leave_type_id' => $leavetype, 
					'leave_from' => $leavefrom, 
					'leave_to' => $leaveto, 
					'employee_remark' => $reason, 
					'status' => $status, 
					'admin_remark' => $remark, 
					'applied_by' => $applied_by, 
					'document_file' => $document,
					'status_updated_by'    => 1);
            }

            $this->leaverequest_model->addLeaveRequest($data);
            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));

            $staff_details     = $this->notificationsetting_model->getstaffDetails($staff_id);
            $leavetype_details = $this->notificationsetting_model->getleavetypesDetails($leavetype);

            $event_data = array(
                'apply_date'    => $this->customlib->YYYYMMDDTodateFormat($applied_date),
                'leave_type'    => $leavetype_details['type'],
                'leave_date'    => $this->customlib->YYYYMMDDTodateFormat($leavefrom) . ' - ' . $this->customlib->YYYYMMDDTodateFormat($leaveto),
                'days'          => $leave_days,
                'leave_status'  => $this->lang->line($status),
                'role_id'       => $staff_details['role_id'],
                'staff_id'      => $staff_details['id'],
                'staff_name'    => $staff_details['name'],
                'staff_surname' => $staff_details['surname'],
                'employee_id'   => $staff_details['employee_id'],
            );
            $this->system_notification->send_system_notification('staff_leave', $event_data);

            }else{

                $msg = array(
                    'applieddate' => $this->lang->line('selected_leave_days') . " > " . $this->lang->line('available_leaves'),
                );

                $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
            }
        }
        echo json_encode($array);
    }

    public function test()
    {
        $data = array
            (
            "staff_id"        => 5,
            "date"            => '2018-06-25',
            "leave_days"      => 1,
            "leave_type_id"   => 5,
            "leave_from"      => '2018-06-25',
            "leave_to"        => '2018-06-25',
            "employee_remark" => 'safsdf',
            "status"          => 'pending',
            "admin_remark"    => '',
            "applied_by"      => 'admin',
            "document_file"   => '',
        );

        $this->db->insert("staff_leave_request", $data);
    }

    /** This function is used to validate document for upload
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
