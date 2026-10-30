<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Schsettings extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('upload');
        $this->load->library('Enc_lib');
        $this->load->library('SaasValidation');
        $this->load->model('staffAttendaceSetting_model');
        $this->load->model('attendencetype_model');
        $this->load->model("staff_model");
    }  

    public function index()
    {        
        $app_ver         = $this->config->item('app_ver');
        $data['app_response']   = $this->auth->andapp_validate();     

        $this->session->set_userdata('top_menu', 'setup');
        $this->session->set_userdata('sub_menu', 'schsettings/index');
        $this->session->set_userdata('inner_menu', 'schsettings/index');
        $data['title']          = 'Setting List';
        $setting_result         = $this->setting_model->getHospitalDetail();
        $setting_result->base_url        = ($setting_result->base_url == "") ? base_url() : $setting_result->base_url;
        $setting_result->folder_path     = ($setting_result->folder_path == "") ? FCPATH : $setting_result->folder_path;
        $data['settinglist']    = $setting_result;
        $timeFormat             = $this->customlib->timeFormat();
        $timezoneList           = $this->customlib->timezone_list();
        $data['title']          = 'Hospital Setting';
        $language_result        = $this->language_model->getEnable_languages();
        $month_list             = $this->customlib->getMonthList();
        $data['languagelist']   = $language_result;
        $data['timezoneList']   = $timezoneList;
        $data['timeFormat']     = $timeFormat;
        $data['monthList']      = $month_list;
        $dateFormat             = $this->customlib->getDateFormat();
        $currency               = $this->customlib->getCurrency();
        $data['dateFormatList'] = $dateFormat;
        $data['currencyList']   = $currency;
        $this->load->view('layout/header', $data);
        $this->load->view('setting/settingList', $data);
        $this->load->view('layout/footer', $data);

    }

    /**
     * SaaS storage pre-check (form_validation callback).
     * Returns false (blocking the save) when the uploaded logo would push the
     * tenant over its storage quota. SaasValidation sets the error message.
     */
    public function validateCanUploadFile($str, $params_string)
    {
        $storage_array = array_map('trim', explode(',', $params_string));
        return $this->saasvalidation->validateCanUploadFile($str, $storage_array);
    }

    public function ajax_editlogo()
    {
        $this->form_validation->set_rules('id', 'ID', 'trim|required|xss_clean');
        $this->form_validation->set_rules('file', $this->lang->line('image'), 'callback_handle_upload|callback_validateCanUploadFile[file]');
        if ($this->form_validation->run() == false) {
            $data = array(
                'file' => form_error('file'),
            );
            $array = array('success' => false, 'error' => $data);
            echo json_encode($array);
        } else {
            $id = $this->input->post('id', TRUE);

            if(isset($_FILES["file"]) && !empty($_FILES['file']['name'])) {
                // SaaS: capture the existing logo for quota diff + cleanup. The logo
                // column stores a bare filename under uploads/hospital_content/logo.
                $existing_setting = $this->setting_model->getHospitalDetail();
                $old_logo = (!empty($existing_setting->image)) ? $existing_setting->image : '';
                $old_kb   = (!empty($old_logo)) ? $this->media_storage->getUploadedFileSize($old_logo, 'uploads/hospital_content/logo') : 0;

                $file_name = $this->media_storage->fileupload("file",'./uploads/hospital_content/logo/');

                // SaaS: adjust storage quota by the size difference (new vs replaced).
                try {
                    $new_kb = $this->media_storage->getTmpFileSize('file');
                    if ($old_kb > $new_kb) {
                        $this->saasvalidation->deleteResouceQuota('storage', $old_kb - $new_kb);
                    } elseif ($new_kb > $old_kb) {
                        $this->saasvalidation->updateResouceQuota('storage', $new_kb - $old_kb);
                    }
                } catch (Exception $e) {
                    log_message('error', 'SaaS storage quota update failed (schsettings ajax_editlogo): ' . $e->getMessage());
                }

                // Remove the replaced physical file (new upload uses a unique name).
                if (!empty($old_logo) && $old_logo !== $file_name) {
                    $this->media_storage->filedelete($old_logo, 'uploads/hospital_content/logo');
                }
            }else{
                $file_name = "";
            }

            $data_record = array('id' => $id, 'image' => $file_name);
            $this->setting_model->add($data_record);
            $array = array('success' => true, 'error' => '', 'message' => $this->lang->line('update_message'));
            echo json_encode($array);
        }
    }

    public function ajax_applogo()
    {
        $this->form_validation->set_rules('id', 'ID', 'trim|required|xss_clean');
        $this->form_validation->set_rules('file', $this->lang->line('image'), 'callback_handle_upload|callback_validateCanUploadFile[file]');
        if ($this->form_validation->run() == false) {
            $data = array(
                'file' => form_error('file'),
            );
            $array = array('success' => false, 'error' => $data);
            echo json_encode($array);
        } else {
            $id = $this->input->post('id', TRUE);

            if(isset($_FILES["file"]) && !empty($_FILES['file']['name'])) {
                // SaaS: capture the existing app logo for quota diff + cleanup. The
                // app_logo column stores a bare filename under uploads/hospital_content/logo.
                $existing_setting = $this->setting_model->getHospitalDetail();
                $old_logo = (!empty($existing_setting->app_logo)) ? $existing_setting->app_logo : '';
                $old_kb   = (!empty($old_logo)) ? $this->media_storage->getUploadedFileSize($old_logo, 'uploads/hospital_content/logo') : 0;

                $file_name = $this->media_storage->fileupload("file",'./uploads/hospital_content/logo/');

                // SaaS: adjust storage quota by the size difference (new vs replaced).
                try {
                    $new_kb = $this->media_storage->getTmpFileSize('file');
                    if ($old_kb > $new_kb) {
                        $this->saasvalidation->deleteResouceQuota('storage', $old_kb - $new_kb);
                    } elseif ($new_kb > $old_kb) {
                        $this->saasvalidation->updateResouceQuota('storage', $new_kb - $old_kb);
                    }
                } catch (Exception $e) {
                    log_message('error', 'SaaS storage quota update failed (schsettings ajax_applogo): ' . $e->getMessage());
                }

                // Remove the replaced physical file (new upload uses a unique name).
                if (!empty($old_logo) && $old_logo !== $file_name) {
                    $this->media_storage->filedelete($old_logo, 'uploads/hospital_content/logo');
                }
            }else{
                $file_name = "";
            }

            $data_record = array('id' => $id, 'app_logo' => $file_name);
            $this->setting_model->add($data_record);
            $array = array('success' => true, 'error' => '', 'message' => $this->lang->line('update_message'));
            echo json_encode($array);
        }
    }

    public function ajax_minilogo()
    {
        $this->form_validation->set_rules('id', 'ID', 'trim|required|xss_clean');
        $this->form_validation->set_rules('file', $this->lang->line('image'), 'callback_handle_upload|callback_validateCanUploadFile[file]');
        if ($this->form_validation->run() == false) {
            $data = array(
                'file' => form_error('file'),
            );
            $array = array('success' => false, 'error' => $data);
            echo json_encode($array);
        } else {
            $id = $this->input->post('id', TRUE);

            if(isset($_FILES["file"]) && !empty($_FILES['file']['name'])) {
                // SaaS: capture the existing mini-logo for quota diff + cleanup. The
                // mini_logo column stores a bare filename under uploads/hospital_content/logo.
                $existing_setting = $this->setting_model->getHospitalDetail();
                $old_logo = (!empty($existing_setting->mini_logo)) ? $existing_setting->mini_logo : '';
                $old_kb   = (!empty($old_logo)) ? $this->media_storage->getUploadedFileSize($old_logo, 'uploads/hospital_content/logo') : 0;

                $file_name = $this->media_storage->fileupload("file",'./uploads/hospital_content/logo/');

                // SaaS: adjust storage quota by the size difference (new vs replaced).
                try {
                    $new_kb = $this->media_storage->getTmpFileSize('file');
                    if ($old_kb > $new_kb) {
                        $this->saasvalidation->deleteResouceQuota('storage', $old_kb - $new_kb);
                    } elseif ($new_kb > $old_kb) {
                        $this->saasvalidation->updateResouceQuota('storage', $new_kb - $old_kb);
                    }
                } catch (Exception $e) {
                    log_message('error', 'SaaS storage quota update failed (schsettings ajax_minilogo): ' . $e->getMessage());
                }

                // Remove the replaced physical file (new upload uses a unique name).
                if (!empty($old_logo) && $old_logo !== $file_name) {
                    $this->media_storage->filedelete($old_logo, 'uploads/hospital_content/logo');
                }
            }else{
                $file_name = "";
            }

            $data_record = array('id' => $id, 'mini_logo' => $file_name);
            $this->setting_model->add($data_record);
            $array = array('success' => true, 'error' => '', 'message' => $this->lang->line('update_message'));
            echo json_encode($array);
        }
    }

    public function editLogo($id)
    {
        $data['title']       = 'Hospital Logo';
        $setting_result      = $this->setting_model->get();
        $data['settinglist'] = $setting_result;
        $data['id']          = $id;
        $this->form_validation->set_rules('file', $this->lang->line('image'), 'callback_handle_upload');
        if ($this->form_validation->run() == false) {
            $this->load->view('layout/header', $data);
            $this->load->view('setting/editLogo', $data);
            $this->load->view('layout/footer', $data);
        } else {
            if (isset($_FILES["file"]) && !empty($_FILES['file']['name'])) {
                $fileInfo = pathinfo($_FILES["file"]["name"]);
                $img_name = $id . '.' . $fileInfo['extension'];
                move_uploaded_file($_FILES["file"]["tmp_name"], "./uploads/hospital_content/logo/" . $img_name);
            }
            $data_record = array('id' => $id, 'image' => $img_name);
            $this->setting_model->add($data_record);
            $this->session->set_flashdata('msg', '<div class="alert alert-left">added Successfully</div>');
            redirect('schsettings/index');
        }
    }

    public function handle_upload()
    {
        if (isset($_FILES["file"]) && !empty($_FILES['file']['name'])) {
            $allowedExts = array('jpg', 'jpeg', 'png');
            $temp        = explode(".", $_FILES["file"]["name"]);
            $extension   = end($temp);
            if ($_FILES["file"]["error"] > 0) {
                $error .= "Error opening the file<br />";
            }
            if ($_FILES["file"]["type"] != 'image/gif' &&
                $_FILES["file"]["type"] != 'image/jpeg' &&
                $_FILES["file"]["type"] != 'image/png') {
                $this->form_validation->set_message('handle_upload', 'File type not allowed');
                return false;
            }
            if (!in_array(strtolower($extension), $allowedExts)) {
                $this->form_validation->set_message('handle_upload', 'Extension not allowed');
                return false;
            }
            if ($_FILES["file"]["size"] > 102400) {
                $this->form_validation->set_message('handle_upload', 'File size shoud be less than 100 kB');
                return false;
            }
            return true;
        } else {
            $this->form_validation->set_message('handle_upload', 'Logo file is required');
            return false;
        }
    }

    public function view($id)
    {
        $data['title']   = 'Setting List';
        $setting         = $this->setting_model->get($id);
        $data['setting'] = $setting;
        $this->load->view('layout/header', $data);
        $this->load->view('setting/settingShow', $data);
        $this->load->view('layout/footer', $data);
    }

    public function getSchsetting()
    {
        $data = $this->setting_model->getSetting();
        echo json_encode($data);
    }

    public function ajax_schedit()
    {
        if (!$this->rbac->hasPrivilege('general_setting', 'can_edit')) {
            access_denied();
        }
        $this->form_validation->set_rules('sch_name', $this->lang->line('hospital_name'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('sch_phone', $this->lang->line('phone'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('sch_address', $this->lang->line('address'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('sch_email', $this->lang->line('email'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('sch_lang_id', $this->lang->line('language'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('sch_currency_symbol', $this->lang->line('currency_symbol'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('sch_timezone', $this->lang->line('time_zone'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('sch_currency', $this->lang->line('currency'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('sch_date_format', $this->lang->line('date_format'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('theme', $this->lang->line('theme'), 'trim|required|xss_clean|in_list[a,b,c]');
        $this->form_validation->set_rules('time_format', $this->lang->line('time_format'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('credit_limit', $this->lang->line('credit_limit'), 'trim|required|numeric|xss_clean');
        $this->form_validation->set_rules('doctor_restriction_mode', $this->lang->line('doctor_restriction_mode'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('superadmin_restriction_mode', $this->lang->line('superadmin_restriction_mode'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('base_url', $this->lang->line('base_url'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('folder_path', $this->lang->line('file_upload_path'), 'trim|required|xss_clean');

        if ($this->form_validation->run() == false) {
            $data = array(
                'sch_name'                    => form_error('sch_name'),
                'sch_phone'                   => form_error('sch_phone'),
                'sch_address'                 => form_error('sch_address'),
                'sch_email'                   => form_error('sch_email'),
                'sch_lang_id'                 => form_error('sch_lang_id'),
                'sch_currency_symbol'         => form_error('sch_currency_symbol'),
                'sch_timezone'                => form_error('sch_timezone'),
                'sch_currency'                => form_error('sch_currency'),
                'sch_date_format'             => form_error('sch_date_format'),
                'theme'                       => form_error('theme'),
                'credit_limit'                => form_error('credit_limit'),
                'time_format'                 => form_error('time_format'),
                'doctor_restriction_mode'     => form_error('doctor_restriction_mode'),
                'superadmin_restriction_mode' => form_error('superadmin_restriction_mode'),
                'base_url'                    => form_error('base_url'),
                'folder_path'                 => form_error('folder_path'),
            );
            $array = array('status' => 'fail', 'error' => $data);
            echo json_encode($array);
        } else {
            $data = array(
                'id'                       => $this->input->post('sch_id', TRUE),
                'name'                     => $this->input->post('sch_name', TRUE),
                'phone'                    => $this->input->post('sch_phone', TRUE),
                'dise_code'                => $this->input->post('sch_dise_code', TRUE),
                'address'                  => $this->input->post('sch_address', TRUE),
                'email'                    => $this->input->post('sch_email', TRUE),
                'lang_id'                  => $this->input->post('sch_lang_id', TRUE),
                'timezone'                 => $this->input->post('sch_timezone', TRUE),
                'date_format'              => $this->input->post('sch_date_format', TRUE),
                'time_format'              => $this->input->post('time_format', TRUE),
                'currency'                 => $this->input->post('sch_currency', TRUE),
                'currency_symbol'          => $this->input->post('sch_currency_symbol', TRUE),
                'theme'                    => $this->input->post('theme', TRUE),
                'credit_limit'             => $this->input->post('credit_limit', TRUE),
                'doctor_restriction'       => $this->input->post('doctor_restriction_mode', TRUE),
                'superadmin_restriction'   => $this->input->post('superadmin_restriction_mode', TRUE),
                'app_primary_color_code'   => $this->input->post('app_primary_color_code', TRUE),
                'app_secondary_color_code' => $this->input->post('app_secondary_color_code', TRUE),
                'mobile_api_url'           => $this->input->post('mobile_api_url', TRUE),
                'patient_panel'            => $this->input->post('patient_panel', TRUE),
                'patient_delete_account'   => $this->input->post('patient_delete_account', TRUE),
                'scan_code_type'           => $this->input->post('scan_code_type', TRUE),
                'base_url'                    => $this->input->post('base_url', TRUE),
                'folder_path'                 => $this->input->post('folder_path', TRUE),
                'notification_poll_interval'  => (int)$this->input->post('notification_poll_interval', TRUE),
            );
            $this->setting_model->add($data);
            $this->load->helper('lang');
            $this->session->userdata['hospitaladmin']['date_format']            = $this->input->post('sch_date_format', TRUE);
            $this->session->userdata['hospitaladmin']['currency_symbol']        = $this->input->post('sch_currency_symbol', TRUE);
            $this->session->userdata['hospitaladmin']['timezone']               = $this->input->post('sch_timezone', TRUE);
            $this->session->userdata['hospitaladmin']['theme']                  = $this->input->post('theme', TRUE);
            $this->session->userdata['hospitaladmin']['sh_variant']             = $this->input->post('theme', TRUE);
            $this->session->userdata['hospitaladmin']['credit_limit']           = $this->input->post('credit_limit', TRUE);
            $this->session->userdata['hospitaladmin']['opd_record_month']       = $this->input->post('opd_record_month', TRUE);
            $this->session->userdata['hospitaladmin']['doctor_restriction']     = $this->input->post('doctor_restriction_mode', TRUE);
            $this->session->userdata['hospitaladmin']['superadmin_restriction'] = $this->input->post('superadmin_restriction_mode', TRUE);
            set_language($this->input->post('sch_lang_id', TRUE));
            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
            echo json_encode($array);
        }
    }

    public function ajax_schedit_new()
    {
        if (!$this->rbac->hasPrivilege('general_setting', 'can_edit')) {
            access_denied();
        }
        $this->form_validation->set_rules('sch_name', $this->lang->line('hospital_name'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('sch_phone', $this->lang->line('phone'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('sch_address', $this->lang->line('address'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('sch_email', $this->lang->line('email'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('sch_lang_id', $this->lang->line('language'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('sch_currency_symbol', $this->lang->line('currency_symbol'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('sch_timezone', $this->lang->line('time_zone'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('sch_currency', $this->lang->line('currency'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('sch_date_format', $this->lang->line('date_format'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('sch_is_rtl', $this->lang->line('url'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('theme', $this->lang->line('theme'), 'trim|required|xss_clean|in_list[a,b,c]');
        $this->form_validation->set_rules('time_format', $this->lang->line('time_format'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('credit_limit', $this->lang->line('credit_limit'), 'trim|required|numeric|xss_clean');
        $this->form_validation->set_rules('doctor_restriction_mode', $this->lang->line('doctor_restriction_mode'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('superadmin_restriction_mode', $this->lang->line('superadmin_restriction_mode'), 'trim|required|xss_clean');

        if ($this->form_validation->run() == false) {           
            $this->load->view('layout/header');
            $this->load->view('admin/course/create', $data);
            $this->load->view('layout/footer');
        } else {
            $data = array(
                'id'                       => $this->input->post('sch_id', TRUE),
                'name'                     => $this->input->post('sch_name', TRUE),
                'phone'                    => $this->input->post('sch_phone', TRUE),
                'dise_code'                => $this->input->post('sch_dise_code', TRUE),
                'address'                  => $this->input->post('sch_address', TRUE),
                'email'                    => $this->input->post('sch_email', TRUE),
                'lang_id'                  => $this->input->post('sch_lang_id', TRUE),
                'timezone'                 => $this->input->post('sch_timezone', TRUE),
                'date_format'              => $this->input->post('sch_date_format', TRUE),
                'time_format'              => $this->input->post('time_format', TRUE),
                'is_rtl'                   => $this->input->post('sch_is_rtl', TRUE),
                'currency'                 => $this->input->post('sch_currency', TRUE),
                'currency_symbol'          => $this->input->post('sch_currency_symbol', TRUE),
                'theme'                    => $this->input->post('theme', TRUE),
                'credit_limit'             => $this->input->post('credit_limit', TRUE),
                'doctor_restriction'       => $this->input->post('doctor_restriction_mode', TRUE),
                'superadmin_restriction'   => $this->input->post('superadmin_restriction_mode', TRUE),
                'app_primary_color_code'   => $this->input->post('sch_app_primary_color_code', TRUE),
                'app_secondary_color_code' => $this->input->post('sch_app_secondary_color_code', TRUE),
                'mobile_api_url'           => $this->input->post('sch_mobile_api_url', TRUE),
                'patient_delete_account'   => $this->input->post('patient_delete_account', TRUE),
            );
            $this->setting_model->add($data);
            $this->load->helper('lang');
            $this->session->userdata['hospitaladmin']['date_format']            = $this->input->post('sch_date_format', TRUE);
            $this->session->userdata['hospitaladmin']['currency_symbol']        = $this->input->post('sch_currency_symbol', TRUE);
            $this->session->userdata['hospitaladmin']['is_rtl']                 = $this->input->post('sch_is_rtl', TRUE);
            $this->session->userdata['hospitaladmin']['timezone']               = $this->input->post('sch_timezone', TRUE);
            $this->session->userdata['hospitaladmin']['theme']                  = $this->input->post('theme', TRUE);
            $this->session->userdata['hospitaladmin']['sh_variant']             = $this->input->post('theme', TRUE);
            $this->session->userdata['hospitaladmin']['credit_limit']           = $this->input->post('credit_limit', TRUE);
            $this->session->userdata['hospitaladmin']['opd_record_month']       = $this->input->post('opd_record_month', TRUE);
            $this->session->userdata['hospitaladmin']['doctor_restriction']     = $this->input->post('doctor_restriction_mode', TRUE);
            $this->session->userdata['hospitaladmin']['superadmin_restriction'] = $this->input->post('superadmin_restriction_mode', TRUE);
            set_language($this->input->post('sch_lang_id', TRUE));
            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
            echo json_encode($array);
        }
    }

    public function attendance()
	{
        $this->session->set_userdata('top_menu', 'setup');
        $this->session->set_userdata('sub_menu', 'schsettings/attendance');
        $this->session->set_userdata('inner_menu', 'schsettings/attendance');
        $data['title']      = 'Setting List';
        $setting_result     = $this->setting_model->getHospitalDetail();       
        $data['result']     = $setting_result;       
        $staff_attendance_data = $this->staffAttendaceSetting_model->getRoleAttendanceSetting();   
        $attendance_type = $this->attendencetype_model->getScheduleTypeStaffAttendance();
        $user_roles            = $this->staff_model->getStaffRole();
        $data['user_roles'] = $user_roles;        
        $data['attendance_type'] = $attendance_type;
        $new_list_attendance = array();

        foreach ($staff_attendance_data as $key => $value) {
            if (array_key_exists($value->id, $new_list_attendance)) {
                $new_list_attendance[$value->id]['schedule'][] = $value;
            } else {
                $new_list_attendance[$value->id] = [
                    'role_id' => $value->id,
                    'role' => $value->role_name,
                    'schedule' => array($value)

                ];
            }
        }
        $data['list_attendance'] = $new_list_attendance;           
        $this->load->view('layout/header', $data);
        $this->load->view('setting/attendance', $data);
        $this->load->view('layout/footer', $data);        
    }
    

    public function saveattendance(){
        $this->form_validation->set_rules('biometric', $this->lang->line('biometric_attendance'), 'trim|required|xss_clean');       
        if ($this->form_validation->run() == false) {
            $data = array(
                'attendence_type' => form_error('attendence_type'),
            );
            $array = array('status' => 'fail', 'error' => $data);
            echo json_encode($array);
        } else {
            $data = array(
                'id'               => $this->input->post('sch_id', TRUE),
                'biometric_device' => $this->input->post('biometric_device', TRUE),
                'biometric'        => $this->input->post('biometric', TRUE),
            );
            
            $this->setting_model->add($data); 
            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
            echo json_encode($array);
        }
    }

     public function savestaffsetting(){
        $this->form_validation->set_rules('row[]', $this->lang->line('row'), 'trim|required|xss_clean');
        $row = $this->input->post('row', TRUE);
        $time_valid = true;

        if (!empty($row) && isset($row)) {
            foreach ($row as $row_key => $row_value) {
                $attendance_type      = $this->input->post('attendance_type_id_' . $row_value, TRUE);
                $class_section        = $this->input->post('role_id_' . $row_value, TRUE);
                $entry_time_from      = $this->input->post('entry_time_from_' . $row_value, TRUE);
                $entry_time_to        = $this->input->post('entry_time_to_' . $row_value, TRUE);
                $total_institute_hour = $this->input->post('total_institute_hour_' . $row_value, TRUE);
             
                if ($class_section == "" || $entry_time_from == "" || $entry_time_to == "" || $total_institute_hour == "" || $attendance_type == "") {
                    $this->form_validation->set_rules(
                        'fields',
                        'fields --r',
                        'trim|required|xss_clean',
                        array('required' => $this->lang->line('fields_values_required'))
                    );
                    $time_valid = false;
                    break;
                }
            }
        }

        if ($this->form_validation->run() == false){
            $msg = array(
                'row' => form_error('row'),
                'fields' => form_error('fields')
            );
            $array = array('status' => 0, 'error' => $msg, 'message' => '');
        } else {
            $insert_array = array();
            $role_array = array();
            foreach ($row as $row_key => $row_value) {
                $role_array[] = ($this->input->post('role_id_' . $row_value, TRUE));
                $role_id = $this->input->post('role_id_' . $row_value, TRUE);
                $attendance_type = $this->input->post('attendance_type_id_' . $row_value, TRUE);
                $entry_time_from = $this->input->post('entry_time_from_' . $row_value, TRUE);
                $entry_time_to = $this->input->post('entry_time_to_' . $row_value, TRUE);
                $total_institute_hour = $this->input->post('total_institute_hour_' . $row_value, TRUE);
       
                $insert_array[] = array(
                    'staff_attendence_type_id' => $attendance_type,
                    'role_id'                  => $class_section,
                    'entry_time_from'          => $entry_time_from,
                    'entry_time_to'            => $entry_time_to,
                    'total_institute_hour'     => ($total_institute_hour)
                );
            }

            $this->staffAttendaceSetting_model->add($insert_array, $role_array);
            $array = array('status' => 1, 'message' => $this->lang->line('update_message'));
        }
        echo json_encode($array);
    }

    public function queueprocess()
    {
        $this->session->set_userdata('top_menu', 'setup');
        $this->session->set_userdata('sub_menu', 'schsettings/index');
        $this->session->set_userdata('inner_menu', 'schsettings/queueprocess');
        $data['title']  = 'Queue Process Setting';
        $data['result'] = $this->setting_model->getSetting();
        $this->load->view('layout/header', $data);
        $this->load->view('setting/queueprocess', $data);
        $this->load->view('layout/footer', $data);
    }

    public function savequeueprocess()
    {
        $this->form_validation->set_rules('message_queue_attempts',  $this->lang->line('queue_attempts'),  'trim|required|integer|greater_than[0]|xss_clean');
        $this->form_validation->set_rules('message_queue_batch_size', $this->lang->line('queue_batch_size'), 'trim|required|integer|greater_than[0]|xss_clean');

        if ($this->form_validation->run() == false) {
            $errors = array(
                'message_queue_attempts'  => form_error('message_queue_attempts'),
                'message_queue_batch_size' => form_error('message_queue_batch_size'),
            );
            $array = array('status' => 'fail', 'error' => $errors, 'message' => '');
        } else {
            $data = array(
                'id'                       => $this->input->post('sch_id', TRUE),
                'message_mode'             => $this->input->post('message_mode', TRUE),
                'message_queue_attempts'   => $this->input->post('message_queue_attempts', TRUE),
                'message_queue_batch_size' => $this->input->post('message_queue_batch_size', TRUE),
            );
            $this->setting_model->add($data);

            // Refresh session so gateways pick up the new mode immediately (without re-login)
            $admin = $this->session->userdata('hospitaladmin');
            $admin['message_mode'] = (int) $data['message_mode'];
            $this->session->set_userdata('hospitaladmin', $admin);

            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
        }
        echo json_encode($array);
    }


}
