<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Systemnotification extends Admin_Controller
{
	public $notificationurl;


    public function __construct()
    {
        parent::__construct();
        $this->load->library('Enc_lib');
        $this->load->library('customlib');
        $this->config->load("mailsms");
        $this->notificationurl = $this->config->item('notification_url');
    }

    public function index()
    {
        if (!$this->rbac->hasPrivilege('notification_center', 'can_view')) {
            access_denied();
            return;
        }
        $notifications = $this->notification_model->getSystemNotification();

        $config['base_url']        = base_url() . "admin/systemnotification/index";
        $config['total_rows']      = sizeof($notifications);
        $config['per_page']        = 20;
        $config['uri_segment']     = 4;
        $config['num_tag_open']    = '<li>';
        $config['num_tag_close']   = '</li>';
        $config['cur_tag_open']    = '<li class="active"><a href="javascript:void(0);">';
        $config['cur_tag_close']   = '</a></li>';
        $config['next_link']       = '<i class="fa fa-angle-right"></i>';
        $config['prev_link']       = '<i class="fa fa-angle-left"></i>';
        $config['next_tag_open']   = '<li class="pg-next">';
        $config['next_tag_close']  = '</li>';
        $config['prev_tag_open']   = '<li class="pg-prev">';
        $config['prev_tag_close']  = '</li>';
        $config['first_tag_open']  = '<li>';
        $config['first_tag_close'] = '</li>';
        $config['last_tag_open']   = '<li>';
        $config['last_tag_close']  = '</li>';
        $this->load->library('pagination', $config);

        $page             = ($this->uri->segment(4)) ? ($this->uri->segment(4)) : 0;
        $notificationlist = $this->notification_model->getSystemNotification($config['per_page'], $page);

        $grouped     = array('today' => array(), 'yesterday' => array(), 'earlier_this_week' => array(), 'older' => array());
        $today_ts    = strtotime(date('Y-m-d'));
        $yest_ts     = strtotime('-1 day', $today_ts);
        $week_ts     = strtotime('-7 days', $today_ts);
        $unread_cnt  = 0;
        foreach ($notificationlist as $row) {
            $row_ts = strtotime(date('Y-m-d', strtotime($row['date'])));
            if ($row_ts >= $today_ts) {
                $grouped['today'][] = $row;
            } elseif ($row_ts >= $yest_ts) {
                $grouped['yesterday'][] = $row;
            } elseif ($row_ts >= $week_ts) {
                $grouped['earlier_this_week'][] = $row;
            } else {
                $grouped['older'][] = $row;
            }
            if (empty($row['readdone']) || $row['readdone'] != 'no') {
                $unread_cnt++;
            }
        }

        $type_color_map = array(
            'appointment'        => 'info',
            'opd'                => 'teal',
            'ipd'                => 'info',
            'pharmacy'           => 'warn',
            'pathology'          => 'warn',
            'radiology'          => 'violet',
            'blood_bank'         => 'danger',
            'live_consultation'  => 'violet',
            'referral'           => 'teal',
            'certificate'        => 'success',
            'ambulance'          => 'danger',
            'birth_death_record' => 'success',
            'human_resource'     => 'success',
        );

        $data["notifications"]      = $notificationlist;
        $data["notifications_by_day"] = $grouped;
        $data["unread_count"]       = $unread_cnt;
        $data["read_count"]         = sizeof($notificationlist) - $unread_cnt;
        $data["type_color_map"]     = $type_color_map;
        $data['notificationurl']    = $this->notificationurl;
        $this->pagination->initialize($config);
        $data['module'] = 'messaging';
        $this->load->view('layout/header', $data);
        $this->load->view('admin/systemnotification/index', $data);
        $this->load->view('layout/footer', $data);
    }

    public function deleteall()
    {
        if (!$this->rbac->hasPrivilege('notification_center', 'can_view')) {
            echo json_encode(array('status' => 0));
            return;
        }
        $role_id = $this->customlib->getLoggedInUserID();
        $this->notification_model->deleteByUser($role_id);
        $return = array('status' => 1, 'msg' => $this->lang->line('data_deleted_successfully'));
        echo json_encode($return);
    }

    public function deleteone()
    {
        if (!$this->rbac->hasPrivilege('notification_center', 'can_view')) {
            echo json_encode(array('status' => 0));
            return;
        }
        $id      = (int)$this->input->post("id", TRUE);
        $role_id = $this->customlib->getLoggedInUserID();
        $ok      = $this->notification_model->deleteSystemNotificationForUser($id, $role_id);
        echo json_encode(array('status' => $ok ? 1 : 0, 'msg' => $this->lang->line('data_deleted_successfully')));
    }

//-------------------------------------------------------------------------------------------------------
    public function updateStatus()
    {
        if (!$this->rbac->hasPrivilege('notification_center', 'can_view')) {
            return;
        }
        $notification_id = $this->input->post("id", TRUE);
        $userdata = $this->customlib->getUserData();
        $userid   = $userdata["id"];
        $data     = array('notification_id' => $notification_id,
            'receiver_id'                       => $userid,
            'is_active'                         => 'no',
            'date'                              => date("Y-m-d H:i:s"),
        );
        $this->notification_model->updateReadNotification($data);
    }

//----------------------------------------------------------------------------------------------------
    public function unreadNotification()
    {
        $result = $this->notification_model->getUnreadNotification();
    }

//---------------------------------------------------------------------------------------------------
    public function moveotpatient($id, $patientid)
    {
        $ot_details = $this->operationtheatre_model->getotDetails($id, $patientid);

        $ot_id             = $ot_details['id'];
        $patient_name      = $ot_details['patient_name'];
        $operation_name    = $ot_details['operation_name'];
        $patient_id        = $ot_details['patient_id'];
        $patient_unique_id = $ot_details['patient_unique_id'];
        $charge_id         = $ot_details['charge_id'];
        $gender            = $ot_details['gender'];
        $email             = $ot_details['email'];
        $phone             = $ot_details['mobileno'];
        $age               = $ot_details['age'];
        $month             = $ot_details['month'];
        $doctor            = $ot_details['consultant_doctor'];
        $consultant1       = $ot_details['ass_consultant_1'];
        $consultant2       = $ot_details['ass_consultant_2'];
        $note              = $ot_details['message'];
        $ot_date           = $ot_details['date'];
        $amount            = $ot_details['apply_charge'];

        $ot_data = array(
            'patient_id'        => $patient_id,
            'patient_name'      => $patient_name,
            'operation_name'    => $operation_name,
            'patient_unique_id' => $patient_unique_id,
            'gender'            => $gender,
            'age'               => $age,
            'month'             => $month,
            'mobileno'          => $phone,
            'date'              => $this->customlib->dateFormatToYYYYMMDD($ot_date),
            'ass_consultant_1'  => $consultant1,
            'ass_consultant_2'  => $consultant2,
        );

        if (!empty($ot_id)) {
            $data['ot_data'] = $ot_data;
        }

        $this->session->set_flashdata('ot_data', $data);
        redirect("admin/operationtheatre/otsearch/");
    }

//-----------------------------------------------------------------------------------------------------
    public function moveappointment($id)
    {
        $details = $this->appointment_model->getDetails($id);

        $app_id             = $details['id'];
        $patient_name       = $details['patient_name'];
        $appointment_no     = $details['appointment_no'];
        $patient_id         = $details['patient_id'];
        $gender             = $details['gender'];
        $email              = $details['email'];
        $phone              = $details['mobileno'];
        $appointment_status = $details['appointment_status'];
        $appointment_no     = $details['appointment_no'];
        $doctor             = $details['doctor'];
        $note               = $details['message'];
        $date               = $details['date'];
        $docname            = $details['name'];
        $docsname           = $details['surname'];

        $app_data = array(
            'id'                 => $app_id,
            'patient_id'         => $patient_id,
            'patient_name'       => $patient_name,
            'appointment_no'     => $appointment_no,
            'gender'             => $gender,
            'mobileno'           => $phone,
            'appointment_status' => $appointment_status,
            'date'               => $this->customlib->dateFormatToYYYYMMDD($date),
            'email'              => $email,
            'name'               => $docname,
            'surname'            => $docsname,
            'message'            => $note,
        );

        if (!empty($app_id)) {
            $data['app_data'] = $app_data;
        }

        $this->session->set_flashdata('app_data', $data);
        redirect("admin/appointment/search/");
    }

//-----------------------------------------------------------------------------------------------------
    public function moveipdnotification($patientid, $id)
    {
        $details    = $this->patient_model->getIpdnotiDetails($id);
        $ipdid      = $details['id'];
        $patient_id = $details['patient_id'];
        $ipd_data   = array(
            'id'         => $ipdid,
            'patient_id' => $patient_id,
        );

        if (!empty($ipdid)) {
            $data['ipd_data'] = $ipd_data;
        }

        redirect("admin/patient/ipdprofile/" . $patient_id);
    }

    public function moveipdpresnotification($patientid, $id, $presid)
    {
        $details    = $this->patient_model->getIpdnotiDetails($id);
        $ipdid      = $details['id'];
        $patient_id = $details['patient_id'];

        $ipdnpres_data = array(
            'id'         => $ipdid,
            'patient_id' => $patient_id,
            'presid'     => $presid,
        );

        if (!empty($ipdid)) {
            $data['ipdnpres_data'] = $ipdnpres_data;
        }

        $this->session->set_flashdata('ipdnpres_data', $data);
        redirect("admin/patient/ipdprofile/" . $patient_id . "#prescription");
    }

//----------------------------------------------------------------------------------------------------

    public function moveopdnotification($patientid, $id)
    {
        $details    = $this->patient_model->getOpdnotiDetails($id);
        $opdid      = $details['id'];
        $patient_id = $details['patient_id'];

        $opdn_data = array(
            'id'         => $opdid,
            'patient_id' => $patient_id,
        );

        if (!empty($opdid)) {
            $data['opdn_data'] = $opdn_data;
        }

        $this->session->set_flashdata('opdn_data', $data);
        redirect("admin/patient/profile/" . $patient_id);
    }

//---------------------------------------------------------------------------------------------------
        
    public function moveopdpresnotification($patientid, $id)
    {
        $details    = $this->patient_model->getOpdnotiDetails($id);
        $opdid      = $details['id'];
        $patient_id = $details['patient_id'];

        $opdnpres_data = array(
            'id'         => $opdid,
            'patient_id' => $patient_id,
        );

        if (!empty($opdid)) {
            $data['opdnpres_data'] = $opdnpres_data;
        }

        $this->session->set_flashdata('opdnpres_data', $data);
        redirect("admin/patient/profile/" . $patient_id);
    }

//----------------------------------------------------------------------------------------------------

    public function pollNotifications()
    {
        if (!$this->input->is_ajax_request()) {
            redirect('admin/admin/dashboard');
            return;
        }
        if (!$this->rbac->hasPrivilege('notification_center', 'can_view')) {
            echo json_encode(array('status' => 0));
            return;
        }

        $last_check_raw = $this->input->post('last_check', TRUE);
        $since_datetime = '';
        if (!empty($last_check_raw)) {
            $ts = strtotime($last_check_raw);
            if ($ts !== false) {
                $since_datetime = date('Y-m-d H:i:s', $ts);
            }
        }
        if (empty($since_datetime)) {
            $since_datetime = date('Y-m-d H:i:s', time() - 60);
        }

        $new_notifications = $this->notification_model->getNewNotificationsSince($since_datetime);
        $unread_count_row  = $this->notification_model->getCountUnreadNotification();

        foreach ($new_notifications as &$_n) {
            $_type = isset($_n['notification_type']) ? $_n['notification_type'] : '';
            $_icon = ($_type !== '') ? @$this->customlib->notification_icon($_type) : '';
            $_n['icon_class']   = $_icon ? $_icon : 'fa fa-bell';
            $_n['module_label'] = ($_type !== '')
                ? ucwords(str_replace('_', ' ', $_type))
                : 'System';
        }
        unset($_n);

        echo json_encode(array(
            'status'            => 1,
            'new_notifications' => $new_notifications,
            'unread_count'      => (int) $unread_count_row->count,
        ));
    }

//----------------------------------------------------------------------------------------------------

    public function movesalarypay($staffid, $id)
    {
        $details       = $this->staff_model->getstaffProfile($staffid, $id);
        $staffid       = $details['staffid'];
        $payslipid     = $details['id'];
        $staff_name    = $details['name'];
        $staff_surname = $details['surname'];
        $employee_id   = $details['employee_id'];

        $staff_data = array(
            'staffid'     => $staffid,
            'id'          => $payslipid,
            'name'        => $staff_name,
            'surname'     => $staff_surname,
            'employee_id' => $employee_id,
        );

        if (!empty($staffid)) {
            $data['staff_data'] = $staff_data;
        }

        $this->session->set_flashdata('staff_data', $data);
        redirect("admin/staff/profile/" . $staffid);
    }
}
