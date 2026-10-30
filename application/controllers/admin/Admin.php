<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Admin extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('Enc_lib');
        $this->load->library('datatables');
        $this->config->load("payroll");
        $this->config->load("image_valid");
        $this->config->load("mailsms");
        $this->load->model('transaction_model');
        $marital_status = $this->config->item('marital_status');
        $bloodgroup     = $this->config->item('bloodgroup');
        $this->load->library('Customlib');
        $this->load->helper('customfield_helper');
    }

    public function unauthorized()
    {
        $data = array();
        $data['module'] = 'setup';
        $this->load->view('layout/header', $data);
        $this->load->view('unauthorized', $data);
        $this->load->view('layout/footer', $data);
    }

    public function getUserImage()
    {
        $id     = $this->customlib->getLoggedInUserID();
        $result = $this->staff_model->get($id);
    }    

    public function updatePurchaseCode()
    {
        $this->form_validation->set_rules('email', $this->lang->line('email'), 'required|valid_email|trim|xss_clean');
        $this->form_validation->set_rules('envato_market_purchase_code', $this->lang->line('purchase_code'), 'required|trim|xss_clean');

        if ($this->form_validation->run() == false) {
            $data = array(
                'email'                       => form_error('email'),
                'envato_market_purchase_code' => form_error('envato_market_purchase_code'),
            );
            $array = array('status' => '2', 'error' => $data);
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(200)
                ->set_output(json_encode($array));
        } else {

            $response = $this->auth->app_update();
        }
    }

    public function updateaddon()
    {
        $this->form_validation->set_rules('app-email', $this->lang->line('email'), 'required|valid_email|trim|xss_clean');
        $this->form_validation->set_rules('app-envato_market_purchase_code', $this->lang->line('purchase_code'), 'required|trim|xss_clean');

        if ($this->form_validation->run() == false) {

            $data = array(
                'app-email'                       => form_error('app-email'),
                'app-envato_market_purchase_code' => form_error('app-envato_market_purchase_code'),
            );
            
            $array = array('status' => '2', 'error' => $data);

            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(200)
                ->set_output(json_encode($array));
        } else {
            //==================
            $response = $this->auth->addon_update();
        }
    }
    
    public function backup()
    {
        if (!$this->rbac->hasPrivilege('backup', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'setup');
        $this->session->set_userdata('sub_menu', 'schsettings/index');
        $this->session->set_userdata('inner_menu', 'admin/backup');
        $data['title'] = $this->lang->line('backup_history');
        if ($this->input->server('REQUEST_METHOD') == "POST") {
            if ($this->input->post('backup', TRUE) == "upload") {
                $this->form_validation->set_rules('file', $this->lang->line('image'), 'callback_handle_upload');
                if ($this->form_validation->run() == false) {

                } else {
                    if (isset($_FILES["file"]) && !empty($_FILES['file']['name'])) {
                        $fileInfo  = pathinfo($_FILES["file"]["name"]);
                        $file_name = "db-" . date("Y-m-d_H-i-s") . ".sql";
                        move_uploaded_file($_FILES["file"]["tmp_name"], "./backup/temp_uploaded/" . $file_name);
                        $folder_name  = 'temp_uploaded';
                        $path         = './backup/';
                        $filePath     = $path . $folder_name . '/' . $file_name;
                        $file_restore = $this->load->file($path . $folder_name . '/' . $file_name, true);
                        $db           = (array) get_instance()->db;
                        $conn         = mysqli_connect('localhost', $db['username'], $db['password'], $db['database']);   
                        $sql   = '';
                        $error = '';

                        if (file_exists($filePath)) {
                            $lines = file($filePath);
                            foreach ($lines as $line) {
                                // Ignoring comments from the SQL script
                                if (substr($line, 0, 2) == '--' || $line == '') {
                                    continue;
                                }
        
                                $sql .= $line;
        
                                if (substr(trim($line), -1, 1) == ';') {
                                    $result = mysqli_query($conn, $sql);
                                    if (!$result) {
                                        $error .= mysqli_error($conn) . "\n";
                                    }
                                    $sql = '';
                                }
                            }
                            $msg = $this->lang->line('restored_message');
                        } // end if file exists                                
                         
                        $this->session->set_flashdata('msg', '<div class="alert alert-success text-left">' . $this->lang->line('success_message') . '</div>');
                        redirect('admin/admin/backup');
                    }
                }
            }
            if ($this->input->post('backup', TRUE) == "backup") {
                $this->session->set_flashdata('msg', '<div class="alert alert-success text-left">' . $this->lang->line('success_message') . '</div>');
                $this->load->helper('download');
                $this->load->dbutil();
                $version  = $this->customlib->getAppVersion();
                $filename = "db_ver_" . $version . '_' . date("Y-m-d_H-i-s") . ".sql";
                $prefs    = array(
                    'ignore'     => array(),
                    'format'     => 'txt',
                    'filename'   => 'mybackup.sql',
                    'add_drop'   => true,
                    'add_insert' => true,
                    'newline'    => "\n",
                );
                $backup = $this->dbutil->backup($prefs);
                $this->load->helper('file');
                write_file('./backup/database_backup/' . $filename, $backup);
                redirect('admin/admin/backup');
                force_download($filename, $backup);
                $this->session->set_flashdata('feedback', $this->lang->line('success_message'));
                redirect('admin/admin/backup');
            } else if ($this->input->post('backup', TRUE) == "restore") {
                $folder_name  = 'database_backup';
                $file_name    = $this->input->post('filename', TRUE);
                $path         = './backup/';
                $filePath     = $path . $folder_name . '/' . $file_name;
                $file_restore = $this->load->file($path . $folder_name . '/' . $file_name, true);
                $db           = (array) get_instance()->db;
                $conn         = mysqli_connect('localhost', $db['username'], $db['password'], $db['database']);
                $sql   = '';
                $error = '';

                if (file_exists($filePath)) {
                    $lines = file($filePath);
                    foreach ($lines as $line) {
                        // Ignoring comments from the SQL script
                        if (substr($line, 0, 2) == '--' || $line == '') {
                            continue;
                        }

                        $sql .= $line;

                        if (substr(trim($line), -1, 1) == ';') {
                            $result = mysqli_query($conn, $sql);
                            if (!$result) {
                                $error .= mysqli_error($conn) . "\n";
                            }
                            $sql = '';
                        }
                    }
                    $msg = $this->lang->line('restored_message');
                } // end if file exists
                $this->session->set_flashdata('msg', '<div class="alert alert-success text-left">' . $msg . '</div>');
                redirect('admin/admin/backup');
            }
        }
        $dir    = "./backup/database_backup/";
        $result = array();
        $cdir   = scandir($dir);
        foreach ($cdir as $key => $value) {
            if (!in_array($value, array(".", ".."))) {
                if (is_dir($dir . DIRECTORY_SEPARATOR . $value)) {
                    $result[$value] = dirToArray($dir . DIRECTORY_SEPARATOR . $value);
                } else {
                    $result[] = $value;
                }
            }
        }
        $data['dbfileList']  = $result;
        $setting_result      = $this->setting_model->get();
        $data['settinglist'] = $setting_result;
        $data['module'] = 'setup';
        $this->load->view('layout/header', $data);
        $this->load->view('admin/backup', $data);
        $this->load->view('layout/footer', $data);
    }
	
    public function changepass()
    {
        $this->session->set_userdata('top_menu', 'System Settings');
        $this->session->set_userdata('sub_menu', 'changepass/index');
        $data['title'] = $this->lang->line('change_password');
        $this->form_validation->set_rules('current_pass', $this->lang->line('current_password'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('new_pass', $this->lang->line('new_password'), 'trim|required|xss_clean|matches[confirm_pass]');
        $this->form_validation->set_rules('confirm_pass', $this->lang->line('confirm_password'), 'trim|required|xss_clean');
        if ($this->form_validation->run() == false) {
            $sessionData            = $this->session->userdata('hospitaladmin');
            $this->data['id']       = $sessionData['id'];
            $this->data['username'] = $sessionData['username'];
            $data['module'] = 'system_settings';
            $this->load->view('layout/header', $data);
            $this->load->view('admin/change_password', $data);
            $this->load->view('layout/footer', $data);
        } else {
            $sessionData = $this->session->userdata('hospitaladmin');
            $userdata    = $this->customlib->getUserData();
            $data_array  = array(
                'current_pass' => $this->input->post('current_pass'),
                'new_pass'     => md5($this->input->post('new_pass')),
                'user_id'      => $sessionData['id'],
                'user_email'   => $sessionData['email'],
                'user_name'    => $sessionData['username'],
            );
            $newdata = array(
                'id'       => $sessionData['id'],
                'password' => $this->enc_lib->passHashEnc($this->input->post('new_pass')),
            );
            $check  = $this->enc_lib->passHashDyc($this->input->post('current_pass'), $userdata["password"]);
            $query1 = $this->admin_model->checkOldPass($data_array);

            if ($query1) {

                if ($check) {
                    $query2 = $this->staff_model->add($newdata);
                    if ($query2) {
                        $data['error_message'] = "<div class='alert alert-success'>" . $this->lang->line('password_changed_successfully') . "</div>";
                        $data['module'] = 'system_settings';
                        $this->load->view('layout/header', $data);
                        $this->load->view('admin/change_password', $data);
                        $this->load->view('layout/footer', $data);
                    }
                } else {
                    $data['error_message'] = "<div class='alert alert-danger'>" . $this->lang->line('invalid_current_password') . "</div>";
                    $data['module'] = 'system_settings';
                    $this->load->view('layout/header', $data);
                    $this->load->view('admin/change_password', $data);
                    $this->load->view('layout/footer', $data);
                }
            } else {

                $data['error_message'] = "<div class='alert alert-danger'>" . $this->lang->line('invalid_current_password') . "</div>";
                $data['module'] = 'system_settings';
                $this->load->view('layout/header', $data);
                $this->load->view('admin/change_password', $data);
                $this->load->view('layout/footer', $data);
            }
        }
    }

    public function downloadbackup($file)
    {
        $this->load->helper('download');
        $filepath = "./backup/database_backup/" . $file;
        $data     = file_get_contents($filepath);
        $name     = $file;
        force_download($name, $data);
    }

    public function dropbackup($file)
    {
        if (!$this->rbac->hasPrivilege('backup', 'can_delete')) {
            access_denied();
        }
        unlink('./backup/database_backup/' . $file);
        redirect('admin/admin/backup');
    }

    public function disablepatient()
    {
        $data['title']      = 'Search';
        $userdata           = $this->customlib->getUserData();
        $data['fields']     = $this->customfield_model->get_custom_fields('patient', 1);
        $data["bloodgroup"] = $this->bloodbankstatus_model->get_product(null, 1);
        $data['module'] = 'patient';
        $this->load->view('layout/header', $data);
        $this->load->view('admin/searchdisablepatient', $data);
        $this->load->view('layout/footer', $data);
    }

    public function search()
    {
        if (!$this->rbac->hasPrivilege('patient', 'can_view')) {
            access_denied();
        }

        $this->session->set_userdata('top_menu', 'patient');
        $data['title']       = 'Search';
        $search_text         = $this->input->post('search_text', TRUE);
        $data['search_text'] = '';
        if($search_text){
            $data['search_text'] = trim($search_text);
        } 
        $data['organisation']   = $this->organisation_model->get();
        $data["bloodgroup"]  = $this->bloodbankstatus_model->get_product(null, 1);
        $data['fields']      = $this->customfield_model->get_custom_fields('patient', 1);
        $userdata            = $this->customlib->getUserData();
        $data['module'] = 'patient';
        $this->load->view('layout/header', $data);
        $this->load->view('admin/search', $data);
        $this->load->view('layout/footer', $data);
    }

    /*
    This Function is used to Get all active patient Recoerds
     */

    public function getpatientdatatable()
    {
        $search_text = $this->input->post('search_text', TRUE);
        $dt_response = $this->patient_model->searchDataTablePatientRecord($search_text);
   
        $fields      = $this->customfield_model->get_custom_fields('patient', 1);
        $dt_response = json_decode($dt_response);
        $dt_data     = array();
        $info        = array();
        $data        = array();
        $url         = array();
        $info_data   = array('OPD', 'IPD', 'Radiology', 'Pathology', 'Pharmacy', 'Operation Theatre');
        $info_url    = array();	
		
        if (!empty($dt_response->data)) {
            foreach ($dt_response->data as $key => $value) {
                $row = array();
                if ($value->is_active == 'yes') {
                    $id = $value->id;
					
					$patient_ipd = $this->patient_model->getPatientIpdForPatientList($id);
					$ipd_id ='';
					if(!empty($patient_ipd[0]['ipd_id'])){
						$ipd_id	=	$patient_ipd[0]['ipd_id'];
					}
					
                    $info_url[0] = base_url() . 'admin/patient/profile/' . $value->id . "/" . $value->is_active;
                    $info_url[1] = base_url() . 'admin/patient/ipdprofile/' . $ipd_id;
                    $info_url[2] = base_url() . 'admin/radio/getTestReportBatch';
                    $info_url[3] = base_url() . 'admin/pathology/getTestReportBatch';
                    $info_url[4] = base_url() . 'admin/pharmacy/bill';

                    $info[0] = $this->db->where("patient_id", $id)->get("opd_details");
                    $info[1] = $this->db->where("patient_id", $id)->where('ipd_details.discharged', 'no')->get("ipd_details");
                    $info[2] = $this->db->where("patient_id", $id)->get("radiology_report");
                    $info[3] = $this->db->where("patient_id", $id)->get("pathology_report");
                    $info[4] = $this->db->where("patient_id", $id)->get("pharmacy_bill_basic");       

                    for ($i = 0; $i < sizeof($info); $i++) {
                        if ($info[$i]->num_rows() > 0) {
                            $data[$i] = $info_data[$i];
                            $url[$i]  = $info_url[$i];
                        } else {
                            unset($data[$i]);
                            unset($url[$i]);
                        }
                    }
                    $result[$key]['info'] = $data;
                    $result[$key]['url']  = $url;
                } else {
                    unset($result[$key]);
                }
               
                if ($value->age) {
                    $age = $value->age . " " . $this->lang->line("years");
                } else {
                    $age = " 0 " . $this->lang->line("years");
                }
                if ($value->month) {
                    $month = ", " . $value->month . " " . $this->lang->line("month");
                } else {
                    $month = ", " . " 0 " . $this->lang->line("month");
                }

                $action = "<div class='d-flex align-items-center justify-content-end gap-1'>";
             

                if (!empty($result[$key]['info'])) {
                    $action .= "<div class='btn-group' data-bs-toggle='tooltip' data-bs-trigger='hover focus' title='" . $this->lang->line('show') . "'>";
                    $action .= "<a href='#' class='btn btn-sm btn-light' data-bs-toggle='dropdown'><i class='fa fa-ellipsis-v'></i></a>";
                    $action .= "<ul class='dropdown-menu dropdown-menu2' role='menu'>";
                    foreach ($result[$key]['info'] as $pkey => $pvalue) {
                        $action .= "<li><a href='" . $result[$key]['url'][$pkey] . "' class='dropdown-item'>" . $pvalue . "</a></li>";
                    }
                    $action .= "</ul>";
                    $action .= "</div>";
                }

                   $action .= "<a href='javascript:void(0)' onclick='getpatientData(" . $value->id . ")' class='btn btn-sm btn-light' data-bs-toggle='tooltip' title='" . $this->lang->line('show') . "'><i class='fa fa-reorder'></i></a>";
                $action .= "</div>";			
				
                $checkbox     = "<input  class='chk2 enable_delete' type='checkbox' name='patient[]' value='" . $value->id . "'>";

                if($value->gender){
                    $gender =   $this->lang->line(strtolower($value->gender));
                }else{
                    $gender =   '';
                }
                //==============================
                $row[] = $checkbox;
                $row[] = composePatientName($value->patient_name, $value->id);
                $row[] = $this->customlib->get_patient_current_age($value->id);                
                $row[] = $gender;                
                $row[] = html_escape($value->mobileno);
                $row[] = html_escape($value->guardian_name);
                $row[] = html_escape($value->address);
                if ($value->is_dead == 'yes') {
                    $row[] = $this->lang->line('yes');
                } else {
                    $row[] = $this->lang->line('no');
                }

                //====================
                if (!empty($fields)) {
                    foreach ($fields as $fields_key => $fields_value) {
                        $display_field = $value->{"$fields_value->name"};
                        if ($fields_value->type == "link") {
                            $display_field = "<a href=\"" . html_escape($value->{"$fields_value->name"}) . "\" target='_blank'>" . html_escape($value->{"$fields_value->name"}) . "</a>";
                        } else {
                            $display_field = html_escape($display_field);
                        }
                        $row[] = $display_field;
                    }
                }
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

    /**
     *    This Function is used to Get all Deactive patient Recoerds
     */

    public function getdisablepatientdatatable()
    {
        $dt_response = $this->patient_model->getAlldisablepatientRecord();
		 
        
        $fields      = $this->customfield_model->get_custom_fields('patient', 1);
        $dt_response = json_decode($dt_response);
        $dt_data     = array();
        $info        = array();
        $data        = array();
        $url         = array();
        $info_data   = array('OPD', 'IPD', 'Radiology', 'Pathology', 'Pharmacy', 'Operation Theatre');
        $info_url    = array();
        if (!empty($dt_response->data)) {
            foreach ($dt_response->data as $key => $value) {

                $row = array();

                $age = $this->customlib->get_patient_current_age($value->id);

                $action = "<a href='javascript:void(0)' onclick='getpatientData(" . $value->id . ")' class='btn btn-default btn-xs pull-right'  data-bs-toggle='modal' title='" . $this->lang->line('show') . "'><i class='fa fa-reorder'></i></a>";
				
                $action .= "<div class='btn-group' style='margin-left:2px;'>";
                if (!empty($result[$key]['info'])) {
                    $action .= "<a href='#' style='width: 20px;border-radius: 2px;' class='btn btn-default btn-xs'  data-bs-toggle='dropdown' title='" . $this->lang->line('show') . "'><i class='fa fa-ellipsis-v'></i></a>";
                    $action .= "<ul class='dropdown-menu dropdown-menu2' role='menu'>";

                    foreach ($result[$key]['info'] as $pkey => $pvalue) {
                        $action .= "<li>" . "<a href='" . $result[$key]['url'][$pkey] . "' class='btn btn-default btn-xs'  data-bs-toggle='' title=''>" . $pvalue . "</a>" . "</li>";
                    }
                    $action .= "</ul>";
                }
                $action .= "</div>";

                $row[] = composePatientName($value->patient_name, $value->id);
                $row[] = $age;
                
                if($value->gender){
                    $row[] =    $this->lang->line(strtolower($value->gender));
                }else{
                    $row[] =    '';
                }
                
                $row[] = html_escape($value->mobileno);
                $row[] = html_escape($value->guardian_name);
                $row[] = html_escape($value->address);
                if (!empty($fields)) {
                    foreach ($fields as $fields_key => $fields_value) {
                        $display_field = $value->{"$fields_value->name"};
                        if ($fields_value->type == "link") {
                            $display_field = "<a href=\"" . html_escape($value->{"$fields_value->name"}) . "\" target='_blank'>" . html_escape($value->{"$fields_value->name"}) . "</a>";
                        } else {
                            $display_field = html_escape($display_field);
                        }
                        $row[] = $display_field;
                    }
                }
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

    public function patientDetails()
    {
        if (!$this->rbac->hasPrivilege('patient', 'can_view')) {
            access_denied();
        }
        $id   = $this->input->post("id");
        $data = $this->patient_model->patientDetails($id);
        if (($data['dob'] == '') || ($data['dob'] == '0000-00-00') || ($data['dob'] == '1970-01-01')) {
            $data['dob'] = "";
        } else {
            $data['dob'] = $this->customlib->YYYYMMDDTodateFormat($data['dob']);
        }

        echo json_encode($data);
    }

    public function getCollectionbymonth()
    {
        $result = $this->admin_model->getMonthlyCollection();
        return $result;
    }

    public function getCollectionbyday($date)
    {
        $result = $this->admin_model->getCollectionbyDay($date);
        if ($result[0]['amount'] == "") {
            $return = 0;
        } else {
            $return = $result[0]['amount'];
        }
        return $return;
    }

    public function getExpensebyday($date)
    {
        $result = $this->admin_model->getExpensebyDay($date);
        if ($result[0]['amount'] == "") {
            $return = 0;
        } else {
            $return = $result[0]['amount'];
        }
        return $return;
    }

    public function getExpensebymonth()
    {
        $result = $this->admin_model->getMonthlyExpense();
        return $result;
    }

    public function whatever($feecollection_array, $start_month_date, $end_month_date)
    {
        $return_amount = 0;
        $st_date       = strtotime($start_month_date);
        $ed_date       = strtotime($end_month_date);
        if (!empty($feecollection_array)) {
            while ($st_date <= $ed_date) {
                $date = date('Y-m-d', $st_date);
                foreach ($feecollection_array as $key => $value) {
                    if ($value['date'] == $date) {
                        $return_amount = $return_amount + $value['amount'] + $value['amount_fine'];
                    }
                }
                $st_date = $st_date + 86400;
            }
        } else {

        }
        return $return_amount;
    }

    public function startmonthandend()
    {
        $startmonth = $this->setting_model->getStartMonth();
        if ($startmonth == 1) {
            $endmonth = 12;
        } else {
            $endmonth = $startmonth - 1;
        }
        return array($startmonth, $endmonth);
    }

    public function handle_upload()
    {
        if (isset($_FILES["file"]) && !empty($_FILES['file']['name'])) {
            $allowedExts = array('sql');
            $temp        = explode(".", $_FILES["file"]["name"]);
            $extension   = end($temp);
            if ($_FILES["file"]["error"] > 0) {
                $error .= "Error opening the file<br />";
            }
            if ($_FILES["file"]["type"] != 'application/octet-stream') {

                $this->form_validation->set_message('handle_upload', $this->lang->line('file_type_not_allowed'));
                return false;
            }
            if (!in_array(strtolower($extension), $allowedExts)) {

                $this->form_validation->set_message('handle_upload', $this->lang->line('extension_not_allowed'));
                return false;
            }
            if ($_FILES["file"]["size"] > 10240000) {

                $this->form_validation->set_message('handle_upload', $this->lang->line('file_size_shoud_be_less_than_100kB'));
                return false;
            }
            return true;
        } else {
            $this->form_validation->set_message('handle_upload', $this->lang->line('the_file_field_is_required'));
            return false;
        }
    }

    public function generate_key($length = 12)
    {
        $str        = "";
        $characters = array_merge(range('A', 'Z'), range('a', 'z'), range('0', '9'));
        $max        = count($characters) - 1;
        for ($i = 0; $i < $length; $i++) {
            $rand = mt_rand(0, $max);
            $str .= $characters[$rand];
        }
        return $str;
    }

    public function addCronsecretkey($id)
    {
        $key  = $this->generate_key(25);
        $data = array('cron_secret_key' => $key);
        $this->setting_model->add_cronsecretkey($data, $id);
        redirect('admin/admin/backup');
    }

    public function dashboard()
    {
        $this->session->set_userdata('top_menu', 'dashboard');
        $this->session->set_userdata('sub_menu', '');
        $role                        = $this->customlib->getStaffRole();
        $role_id                     = json_decode($role)->id;
        $staffid                     = $this->customlib->getStaffID();
        $notifications               = $this->notification_model->getUnreadStaffNotification($staffid, $role_id);
        $data['notifications']       = $notifications;
        $systemnotifications         = $this->notification_model->getUnreadNotification();
        $data['systemnotifications'] = $systemnotifications;
        $Current_year                = date('Y');
        $Next_year                   = date("Y");
        $current_date                = date('Y-m-d');
        $data['title']               = 'Dashboard';
        $Current_start_date          = date('01');
        $Current_date                = date('d');
        $Current_month               = date('m');
        $month_collection            = 0;
        $month_expense               = 0;
        $total_opd_patients          = 0;
        $total_ipd_patients          = 0;
        $ar[0]                       = 01;
        $ar[1]                       = 12;
        $year_str_month              = $Current_year . '-' . $ar[0] . '-01';
        $year_end_month              = date("Y-m-t", strtotime($Next_year . '-' . $ar[1] . '-01'));
        //======================Current Month Collection ==============================
        $first_day_this_month = date('Y-m-01');
        $tot_roles            = $this->role_model->get();
        foreach ($tot_roles as $key => $value) {  
		
            $roles[$key]['count'] = $this->role_model->count_roles($value["id"]);
            $roles[$key]['id'] = $value["id"];
            $roles[$key]['name'] = $value["name"];
			
        }
        $data["roles"]       = $roles;
        $expense             = $this->expense_model->getTotalExpenseBwdate(date('Y-m-01'), date('Y-m-t'));
        $data["expense"]     = $expense;
        $start_month         = strtotime($year_str_month);
        $start               = strtotime($year_str_month);
        $end                 = strtotime($year_end_month);
        $coll_month          = array();
        $s                   = array();
        $ex                  = array();
        $total_month         = array();
        $start_session_month = strtotime($year_str_month);
        while ($start_month <= $end) {
			$monthly_general_income  = 0;
			
            $total_month[] = $this->lang->line(strtolower(date('M', $start_month)));
            $month_start   = date('Y-m-d', $start_month);			
			$month_end     = date("Y-m-01", strtotime("+1 month", $start_month));           
            $where_date    = array('payment_date >=' => $month_start, 'payment_date <' => $month_end) ;
            $monthly_return        = $this->transaction_model->get_monthTransaction($where_date);
			
			$where_date_income           = array('date >=' => $month_start, 'date <' => $month_end);
			$monthly_general_income     = $this->income_model->getTotal($where_date_income);
			
			$return        = $monthly_general_income + $monthly_return;
			
            if (!empty($return)) {
                $at  = 0;
                $s[] = $at + $return;
            } else {
                $s[] = "0.00";
            }
            // getTotalExpenseBwdate uses SQL BETWEEN (inclusive both ends). Passing the next
            // month's 1st as the end double-counts expenses dated on the 1st (they fall in two
            // buckets). Use this month's last day instead. (Income above uses '<' so it's fine.)
            $expense_month_end = date("Y-m-t", $start_month);
            $expense_monthly = $this->expense_model->getTotalExpenseBwdate($month_start, $expense_month_end);
            if (!empty($expense_monthly)) {
                $amt  = 0;
                $ex[] = $amt + $expense_monthly->amount;
            }
            $start_month = strtotime("+1 month", $start_month);
        }
        $data['yearly_collection'] = $s;
        $data['yearly_expense']    = $ex;
        $data['total_month']       = $total_month;
        // Calendar removed 2026-05-15 — Variant B dashboard. Backup: dashboard_legacy_20260515.php
        $userdata                  = $this->customlib->getUserData();
        $data["role"]              = $userdata["user_type"];

        $search_opd_income  = array('payment_date >=' => date('Y-m-01'), 'payment_date <' => date("Y-m-01", strtotime('+1 month')),'opd_id !='=>null);
        $data['opd_income'] = $this->transaction_model->get_monthTransaction($search_opd_income);
        
        $search_ipd_income  = array('payment_date >=' => date('Y-m-01'), 'payment_date <' => date("Y-m-01", strtotime('+1 month')),'ipd_id !='=>null);
        $data['ipd_income'] = $this->transaction_model->get_monthTransaction($search_ipd_income);
        
        $search_pharmacy_income     = array('payment_date >=' => date('Y-m-01'), 'payment_date <' => date("Y-m-01", strtotime('+1 month')),'pharmacy_bill_basic_id !='=>null);
        $data['pharmacy_income']    = $this->transaction_model->get_monthTransaction($search_pharmacy_income);
        
        $search_pathology_income    = array('payment_date >=' => date('Y-m-01'), 'payment_date <' => date("Y-m-01", strtotime('+1 month')),'pathology_billing_id !='=>null);
        $data['pathology_income']   = $this->transaction_model->get_monthTransaction($search_pathology_income);
         
        $search_radiology_income    = array('payment_date >=' => date('Y-m-01'), 'payment_date <' => date("Y-m-01", strtotime('+1 month')),'radiology_billing_id !='=>null);
        $data['radiology_income']   = $this->transaction_model->get_monthTransaction($search_radiology_income);
        
        $search_blood_bank_income   = array('payment_date >=' => date('Y-m-01'), 'payment_date <' => date("Y-m-01", strtotime('+1 month')),'blood_issue_id !='=>null);
        $search_blood_components_income   = array('payment_date >=' => date('Y-m-01'), 'payment_date <' => date("Y-m-01", strtotime('+1 month')),'blood_donor_cycle_id !='=>null);
		
        $blood_income  = $this->transaction_model->get_monthTransaction($search_blood_bank_income);
        $components_income  = $this->transaction_model->get_monthTransaction($search_blood_components_income);
		
		$data['blood_bank_income']  =	$blood_income + $components_income;		
        
        $search_ambulance_income    = array('payment_date >=' => date('Y-m-01'), 'payment_date <' => date("Y-m-01", strtotime('+1 month')),'ambulance_call_id !='=>null);       
        $data['ambulance_income']   = $this->transaction_model->get_monthTransaction($search_ambulance_income);
         
        // BETWEEN is inclusive; use this month's last day (not next month's 1st) to avoid
        // counting expenses dated on the 1st of next month.
        $month_expences             = $this->expense_model->getTotalExpenseBwdate(date('Y-m-01'), date('Y-m-t'));
        $data['expences']           = $month_expences->amount;
        
        $where_date                 = array('date >=' => date('Y-m-01'), 'date <' => date("Y-m-01", strtotime('+1 month')));
        $data['general_income']     = $this->income_model->getTotal($where_date);

        // ── Last month per-module income + expense (for the "Last Month" column) ──
        $lm_start   = date('Y-m-01', strtotime('first day of last month'));
        $lm_end     = date('Y-m-01'); // first of this month (exclusive, same as current-month income)
        $lm_exp_end = date('Y-m-t', strtotime('first day of last month'));
        $lm_base    = array('payment_date >=' => $lm_start, 'payment_date <' => $lm_end);
        $lm_blood   = $this->transaction_model->get_monthTransaction(array_merge($lm_base, array('blood_issue_id !=' => null)))
                    + $this->transaction_model->get_monthTransaction(array_merge($lm_base, array('blood_donor_cycle_id !=' => null)));
        $data['module_income_last'] = array(
            'opd'        => $this->transaction_model->get_monthTransaction(array_merge($lm_base, array('opd_id !=' => null))),
            'ipd'        => $this->transaction_model->get_monthTransaction(array_merge($lm_base, array('ipd_id !=' => null))),
            'pharmacy'   => $this->transaction_model->get_monthTransaction(array_merge($lm_base, array('pharmacy_bill_basic_id !=' => null))),
            'pathology'  => $this->transaction_model->get_monthTransaction(array_merge($lm_base, array('pathology_billing_id !=' => null))),
            'radiology'  => $this->transaction_model->get_monthTransaction(array_merge($lm_base, array('radiology_billing_id !=' => null))),
            'blood_bank' => $lm_blood,
            'ambulance'  => $this->transaction_model->get_monthTransaction(array_merge($lm_base, array('ambulance_call_id !=' => null))),
            'general'    => $this->income_model->getTotal(array('date >=' => $lm_start, 'date <' => $lm_end)),
        );
        $lm_expense_obj           = $this->expense_model->getTotalExpenseBwdate($lm_start, $lm_exp_end);
        $data['expences_last']    = isset($lm_expense_obj->amount) ? (float)$lm_expense_obj->amount : 0;

        $parameter              = array(
            'opd'        => $data['opd_income'],
            'ipd'        => $data['ipd_income'],
            'pharmacy'   => $data['pharmacy_income'],
            'pathology'  => $data['pathology_income'],
            'radiology'  => $data['radiology_income'],
            'blood_bank' => $data['blood_bank_income'],
            'ambulance'  => $data['ambulance_income'],
            'general'    => $data['general_income'],
        );

        $label  = array($this->lang->line('opd'), $this->lang->line('ipd'), $this->lang->line('pharmacy'), $this->lang->line('pathology'), $this->lang->line('radiology'), $this->lang->line('blood_bank'), $this->lang->line('ambulance'), $this->lang->line('income'));
        $module = array('opd', 'ipd', 'pharmacy', 'pathology', 'radiology', 'blood_bank', 'ambulance', 'income');

        $tot_data = array_sum($parameter);

        // KPI: total revenue MTD (used by Variant B hero KPI tile)
        $data['kpi_total_revenue'] = $tot_data;
        // TODO: compute kpi_revenue_delta (vs last month) — requires previous-month sum query

        $jsonarr  = array();
        $i        = 0;

        foreach ($parameter as $key => $value) {
            if($value){
                $data[$key . "_income"] = number_format($value, 2);
            }else{
                $data[$key . "_income"] = number_format(0, 2);
            }
            if (($this->module_lib->hasActive($module[$i]))) {
                if ($tot_data != 0) {
                    $jsonarr['value'][] = round((($value / $tot_data) * 100), 0);
                    $jsonarr['label'][] = $label[$i];
                } else {
                    $jsonarr['value'][] = 0;
                    $jsonarr['label'][] = $label[$i];
                }
            }
            if ($tot_data != 0) {
                $data[$key . "_cdata"] = ($value / $tot_data) * 100;
            } else {
                $data[$key . "_cdata"] = 0;
            }

            $i++;
        }

        $data['mysqlVersion'] = $this->setting_model->getMysqlVersion();
        $data['sqlMode']      = $this->setting_model->getSqlMode();
        $data['jsonarr']      = $jsonarr;

        // ─── Variant B dashboard widgets (added 2026-05-15) ────────────────
        // Bed occupancy
        $this->load->model('bed_model');
        $bed_stats = $this->bed_model->getBedOccupancyStats();
        $data['bed_occupancy_total']     = $bed_stats['total'];
        $data['bed_occupancy_occupied']  = $bed_stats['occupied'];
        $data['bed_occupancy_available'] = $bed_stats['available'];
        $data['bed_occupancy_unused']    = $bed_stats['unused'];


        // ── Patient Flow dashboard widgets ─────────────────────────────────
        $this->load->model("patientflow_model");
        $pf_stats = $this->patientflow_model->get_dashboard_stats();
        $data["pf_active_visits"]         = $pf_stats["active"];
        $data["pf_pending_payment_count"] = $pf_stats["pending_payment_count"];
        $data["pf_pending_payment_amount"] = $pf_stats["pending_payment_amount"];
        $data["pf_queue_active"]          = $pf_stats["queue_active"];
        $data["pf_completed_today"]       = $pf_stats["completed_today"];
        // Today's appointments
        $this->load->model('appointment_model');
        $appt_summary = $this->appointment_model->getTodaySummary();
        $data['today_appointments_count']  = $appt_summary['total'];
        $data['today_appointments_status'] = array(
            'confirmed' => $appt_summary['confirmed'],
            'pending'   => $appt_summary['pending'],
            'cancelled' => $appt_summary['cancelled'],
        );

        // Next upcoming appointments today (for Next Appointments ops card)
        $next_raw  = $this->appointment_model->getNextAppointmentsDashboard(4);
        $next_list = array();
        foreach ($next_raw as $a) {
            $status = strtolower((string)($a['appointment_status'] ?? ''));
            if (strpos($status, 'cancel') !== false) {
                $badge = 'err';
            } elseif (strpos($status, 'approve') !== false || strpos($status, 'confirm') !== false) {
                $badge = 'ok';
            } else {
                $badge = 'warn';
            }
            $next_list[] = array(
                'time'        => date('H:i', strtotime($a['date'])),
                'name'        => isset($a['patient_name']) ? $a['patient_name'] : '—',
                'type'        => $status ? ucwords(str_replace('_', ' ', $status)) : 'Pending',
                'badge_class' => $badge,
            );
        }
        $data['next_appointments'] = $next_list;

        // Blood bank stock by group
        $this->load->model('bloodbankstatus_model');
        $data['blood_bank_stock'] = $this->bloodbankstatus_model->getStockByGroup();

        // Staff attendance per role for today
        $this->load->model('staffattendancemodel');
        $data['staff_attendance'] = $this->staffattendancemodel->getDashboardAttendanceByRole();

        // Outstanding bills (Chunk 4)
        $this->load->model('bill_model');
        $outstanding = $this->bill_model->getOutstandingTotal();
        $data['outstanding_amount']  = $outstanding['total_balance'];
        $data['outstanding_count']   = $outstanding['bill_count'];
        $data['outstanding_overdue'] = $outstanding['overdue_count'];

        // Pharmacy low-stock summary (Chunk 4)
        $this->load->model('pharmacy_model');
        $low_stock = $this->pharmacy_model->getLowStockSummary();
        $data['low_stock_count']          = $low_stock['count'];
        $data['low_stock_critical_count'] = $low_stock['critical_count'];
        $data['low_stock_critical_names'] = $low_stock['critical_names'];

        // Medicines with in-stock batches expiring within the next 30 days
        $data['medicine_expiring_days'] = 30;
        $data['medicine_expiring_soon'] = $this->pharmacy_model->getExpiringSoonCount(30);

        // Blood bank flow — issued today / this week (Chunk 4)
        $this->load->model('bloodissue_model');
        $bb_flow = $this->bloodissue_model->getDashboardFlow();
        $data['bb_issued_today'] = $bb_flow['today'];
        $data['bb_issued_week']  = $bb_flow['week'];
        // Note: expiring + cross-match deferred — blood_issue schema has no expiry/cross_match columns yet

        // Recent activity feed (Chunk 4) — UNION across patients, transactions, appointment
        $activity_rows = $this->transaction_model->getRecentActivity(10);
        $activity      = array();
        foreach ($activity_rows as $r) {
            $ts = !empty($r['sort_at']) ? strtotime($r['sort_at']) : false;
            if ($ts === false) {
                $time_ago = '';
            } else {
                $diff = time() - $ts;
                if ($diff < 0)        { $time_ago = $this->lang->line('just_now'); }   // future timestamp — never show "-Ns ago"
                elseif ($diff < 60)   { $time_ago = $diff . $this->lang->line('sec_ago'); }
                elseif ($diff < 3600) { $time_ago = floor($diff / 60) . $this->lang->line('min_ago'); }
                elseif ($diff < 86400){ $time_ago = floor($diff / 3600) . $this->lang->line('hr_ago'); }
                elseif ($diff < 2592000){ $time_ago = floor($diff / 86400) . $this->lang->line('day_ago'); }
                else                  { $time_ago = date('M j', $ts); }
            }
            // Show the name with its type, e.g. "Nitin (Patient)" / "Nitin (Staff)".
            $actor = $r['subject'] ?: '—';
            if ($actor !== '—' && !empty($r['subject_type'])) {
                $type_label = ($r['subject_type'] === 'staff') ? $this->lang->line('staff') : $this->lang->line('patient');
                $actor     .= ' (' . $type_label . ')';
            }
            // Translatable action text + module label, derived from the row source.
            $source = isset($r['source']) ? $r['source'] : '';
            if ($source === 'patient') {
                $action_text = $this->lang->line('registered_new_patient');
                $module_lbl  = $this->lang->line('opd');
            } elseif ($source === 'appointment') {
                $status     = strtolower(trim(str_replace('appointment', '', (string)$r['action_text'])));
                if ($status === '') { $status = 'booked'; }
                $status_lbl = $this->lang->line($status);
                if (!$status_lbl) { $status_lbl = ucfirst($status); }
                $action_text = $this->lang->line('appointment') . ' ' . $status_lbl;
                $module_lbl  = $this->lang->line('appointment');
            } elseif ($source === 'transaction') {
                $action_text = $this->lang->line('posted_bill_payment');
                $module_lbl  = $r['module']; // payment mode (e.g. CASH) — user data, not translatable
            } else {
                $action_text = $r['action_text'];
                $module_lbl  = $r['module'];
            }
            $activity[] = array(
                'actor'        => $actor,
                'action'       => $action_text,
                'detail'       => $r['detail'],
                'module'       => $module_lbl,
                'module_class' => $r['module_class'] ?: 'info',
                'amount'       => $r['amount'],
                'time_ago'     => $time_ago,
            );
        }
        $data['recent_activity'] = $activity;
        // ────────────────────────────────────────────────────────────────────

        $data['module']       = 'dashboard';
        $this->load->view('layout/header', $data);
        $this->load->view('admin/dashboard', $data);
        $this->load->view('layout/footer', $data);
    }

    public function updateandappCode()
    {
        $this->form_validation->set_rules('app-email', $this->lang->line('email'), 'required|valid_email|trim|xss_clean');
        $this->form_validation->set_rules('app-envato_market_purchase_code', $this->lang->line('purchase_code'), 'required|trim|xss_clean');

        if ($this->form_validation->run() == false) {
            $data = array(
                'app-email'                       => form_error('app-email'),
                'app-envato_market_purchase_code' => form_error('app-envato_market_purchase_code'),
            );
            $array = array('status' => '2', 'error' => $data);

            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(200)
                ->set_output(json_encode($array));
        } else {
            //==================
            $response = $this->auth->andapp_update();
        }
    }
}
