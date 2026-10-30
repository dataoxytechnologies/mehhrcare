<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Staffattendancemodel extends MY_Model
{

    public $current_date;

    public function __construct()
    {
        parent::__construct();
        $this->current_date = $this->setting_model->getDateYmd();
    }

    public function get($id = null)
    {
        $this->db->select("staff.*,staff_attendance.*,staff_roles.role_id")->join("staff", "staff.id = staff_attendance.staff_id")->join("staff_roles", "staff.id = staff_roles.staff_id")->from('staff_attendance');
        $this->db->where("staff.is_active", 1);
        if ($id != null) {
            $this->db->where('staff_attendance.id', $id);
        } else {
            $this->db->order_by('staff_attendance.id');
        }
        $query = $this->db->get();
        if ($id != null) {
            $result = $query->row_array();
        } else {
            $result = $query->result_array();
            if ($this->session->has_userdata('hospitaladmin')) {
                $superadmin_rest = $this->session->userdata['hospitaladmin']['superadmin_restriction'];
                if ($superadmin_rest == 'enabled') {
                    $search     = in_array(7, array_column($result, 'role_id'));
                    $search_key = array_search(7, array_column($result, 'role_id'));
                    if (!empty($search)) {
                        unset($result[$search_key]);
                    }
                }
            }
        }
        return $result;
    }

    public function getUserType()
    {
        $query = $this->db->query("select distinct user_type from staff where is_active = 1");
        return $query->result_array();
    }

    public function searchAttendenceUserType($user_type, $date)
    {	
		$userdata                   = $this->customlib->getUserData();
        $role_id                    = $userdata['role_id'];
        $condition = '';
        if ($this->session->has_userdata('hospitaladmin')) {
            $superadmin_rest = $this->session->userdata['hospitaladmin']['superadmin_restriction'];
            if ($superadmin_rest == 'disabled') {              
              $condition = "and roles.id != 7 "  ;
            }
        }
        if ($user_type == "select") {
            $query = $this->db->query("select staff_attendance.qrcode_attendance,staff_attendance.out_time,staff_attendance.in_time,staff_attendance.id,staff_attendance.biometric_attendence,staff_attendance.biometric_device_data,staff_attendance.staff_attendance_type_id,staff_attendance.remark,staff.name,staff.surname,staff.employee_id,staff.contact_no,staff.email,roles.id as role_id,roles.name as user_type,IFNULL(staff_attendance.date, 'xxx') as date,staff.id as staff_id from staff left join staff_roles on staff_roles.staff_id = staff.id left join roles on staff_roles.role_id = roles.id left join staff_attendance on (staff.id = staff_attendance.staff_id) and staff_attendance.date = " . $this->db->escape($date) . " where staff.is_active = 1 $condition");
        } else {
            $query = $this->db->query("select staff_attendance.qrcode_attendance,staff_attendance.out_time,staff_attendance.in_time,staff_attendance.staff_attendance_type_id,staff_attendance.biometric_attendence,staff_attendance.biometric_device_data,staff_attendance.remark,staff.name,staff.surname,staff.employee_id,staff.contact_no,staff.email,roles.name as user_type,roles.id as role_id,IFNULL(staff_attendance.date, 'xxx') as date, IFNULL(staff_attendance.id, 0) as id, staff.id as staff_id from staff left join staff_roles on (staff.id = staff_roles.staff_id) left join roles on (roles.id = staff_roles.role_id) left join staff_attendance on (staff.id = staff_attendance.staff_id) and staff_attendance.date = " . $this->db->escape($date) . " where roles.name = " . $this->db->escape($user_type) . " and staff.is_active = 1 $condition");
        }
        $result = $query->result_array();
        if ($this->session->has_userdata('hospitaladmin')) {
            $superadmin_rest = $this->session->userdata['hospitaladmin']['superadmin_restriction'];
            if ($superadmin_rest == 'enabled' && $role_id != 7) {
                $search     = in_array(7, array_column($result, 'role_id'));
                $search_key = array_search(7, array_column($result, 'role_id'));
                if (!empty($search)) {
                    unset($result[$search_key]);
                    $result = array_values($result);
                }
            }
        }
        return $result;
    }

    public function add($data)
    {   
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        if (isset($data['id']) && $data['id'] != '') {
            $this->db->where('id', $data['id']);
            $this->db->update('staff_attendance', $data);
            $message = UPDATE_RECORD_CONSTANT . " For Staff Attendance id " . $data['id'];
            $action = "Update";
            $record_id = $data['id'];
            $this->log($message, $record_id, $action);
        //======================Code End==============================
            $this->db->trans_complete(); # Completing transaction
            /* Optional */
            if ($this->db->trans_status() === false) {
                # Something went wrong.
                $this->db->trans_rollback();
                return false;
            } else {
                return $record_id;
            }
        } else {
            $this->db->insert('staff_attendance', $data);
            $insert_id = $this->db->insert_id();
            $message = INSERT_RECORD_CONSTANT . " On Staff Attendance id " . $insert_id;
            $action = "Insert";
            $record_id = $insert_id;
            $this->log($message, $record_id, $action);
            //======================Code End==============================
            $this->db->trans_complete(); # Completing transaction
            /* Optional */
            if ($this->db->trans_status() === false) {
                # Something went wrong.
                $this->db->trans_rollback();
                return false;
            } else {
            }
            return $insert_id;
        }
    }

    public function getStaffAttendanceType()
    {
        $query = $this->db->select('*')->where("is_active", 'yes')->get("staff_attendance_type");
        return $query->result_array();
    }

    public function searchAttendanceReport($user_type, $date)
    {
        if ($user_type == "select") {
            $query = $this->db->query("select staff_attendance.staff_attendance_type_id,staff_attendance_type.type as `att_type`,staff_attendance_type.key_value as `key`,staff_attendance.remark,staff.name,staff.surname,staff.employee_id,staff.contact_no,staff.email,roles.name as user_type,IFNULL(staff_attendance.date, 'xxx') as date, IFNULL(staff_attendance.id, 0) as attendence_id, staff.id as id from staff left join staff_attendance on (staff.id = staff_attendance.staff_id) and staff_attendance.date = " . $this->db->escape($date) . " left join staff_attendance_type on staff_attendance_type.id = staff_attendance.staff_attendance_type_id left join staff_roles on staff_roles.staff_id = staff.id left join roles on staff_roles.role_id = roles.id where staff.is_active = 1");
        } else {
            $query = $this->db->query("select staff_attendance.staff_attendance_type_id,staff_attendance_type.type as `att_type`,staff_attendance_type.key_value as `key`,staff_attendance.remark,staff.name,staff.surname,staff.employee_id,staff.contact_no,staff.email,roles.name as user_type,IFNULL(staff_attendance.date, 'xxx') as date, IFNULL(staff_attendance.id, 0) as attendence_id, staff.id as id from staff  left join staff_roles on (staff.id = staff_roles.staff_id) left join roles on (roles.id = staff_roles.role_id) left join staff_attendance on (staff.id = staff_attendance.staff_id) and staff_attendance.date = " . $this->db->escape($date) . " left join staff_attendance_type on staff_attendance_type.id = staff_attendance.staff_attendance_type_id  where roles.name = '" . $user_type . "' and staff.is_active = 1 ");
        }
        return $query->result_array();
    }

    public function attendanceYearCount()
    {
        $query = $this->db->select("distinct year(date) as year")->get("staff_attendance");
        return $query->result_array();
    }

    public function searchStaffattendance($date, $staff_id = 8)
    {
        $query = $this->db->query("select staff_attendance.staff_attendance_type_id,staff_attendance_type.type as `att_type`,staff_attendance_type.key_value as `key`,staff_attendance.remark,staff.name,staff.surname,staff.contact_no,staff.email,roles.name as user_type,IFNULL(staff_attendance.date, 'xxx') as date, IFNULL(staff_attendance.id, 0) as attendence_id, staff.id as id from staff left join staff_attendance on (staff.id = staff_attendance.staff_id) and staff_attendance.date = " . $this->db->escape($date) . " left join staff_roles on staff_roles.staff_id = staff.id left join roles on staff_roles.role_id = roles.id left join staff_attendance_type on staff_attendance_type.id = staff_attendance.staff_attendance_type_id  where staff.id = '" . $staff_id . "'  ");
        return $query->row_array();
    }    
    
    public function onlineattendence_old($data) {

        $this->db->where('staff_id', $data['staff_id']);
        $this->db->where('date', $data['date']);
        $q = $this->db->get('staff_attendance');
        if ($q->num_rows() == 0) {
            $this->db->insert('staff_attendance', $data);
            return ($this->db->affected_rows() != 1) ? false : true;
        }
        return false;
    }
	
	public function onlineattendence($data, $role_id)
    {
        $status = false;
        $this->db->where('staff_id', $data['staff_id']);
        $this->db->where('date', $data['date']);
        $q = $this->db->get('staff_attendance');
        $time = date('H:i:s');
        if ($q->num_rows() == 0) {
            $attendance_range = $this->staffAttendaceSetting_model->getAttendanceTypeByRole($role_id, $time);
            if ($attendance_range) {
                $data['staff_attendance_type_id'] = $attendance_range->staff_attendence_type_id;
                $data['in_time'] = $time;

                $this->db->insert('staff_attendance', $data);
                $status = 1; //for successfully saving

            } else {
                $status = 2; //for range not exist to save
            }
        } else {

            $return_result = $q->row();
            if (!IsNullOrEmptyString($return_result->in_time)  && !IsNullOrEmptyString($return_result->out_time)) {

                $status = 0;
            } else {
                $updateArr = ['out_time' => $time];
                $return_attendance_type =  $this->staff_schedule_hours($return_result->staff_id, $return_result->in_time);
                if ($return_attendance_type) {
                    $updateArr['staff_attendance_type_id'] = $return_attendance_type;
                }

                $this->db->where('id', $return_result->id);
                $this->db->update('staff_attendance', $updateArr);
                $status = 1;
            }
        }
        return $status;
    }

    public function staff_schedule_hours($staff_id, $in_time)
    {
        $date = date('Y-m-d');
        $sql    = "SELECT staff_roles.role_id,staff_attendence_schedules.staff_attendence_type_id as staff_attendence_schedule_staff_attendence_type_id,staff_attendence_schedules.entry_time_from,staff_attendence_schedules.entry_time_to,staff_attendence_schedules.total_institute_hour FROM `staff` INNER JOIN staff_roles on staff_roles.staff_id=staff.id INNER join staff_attendence_schedules on staff_attendence_schedules.role_id = staff_roles.role_id WHERE staff.id=" . $this->db->escape($staff_id);     

        $current_time = date('H:i:s');
        $query  = $this->db->query($sql);
        if ($query->num_rows() > 0) {

            $return_attedance_type = false;
            $time_entry_seconds = strtotime("1970-01-01 $in_time UTC");
            $time_current_seconds = strtotime("1970-01-01 $current_time UTC");
            $total_spend_time = $time_current_seconds - $time_entry_seconds;

            $result = $query->result();
            $find_array = array();           

            foreach ($result as $result_key => $result_value) {

                $entry_time_from_seconds = strtotime("1970-01-01 $result_value->entry_time_from UTC");
                $entry_time_to_seconds = strtotime("1970-01-01 $result_value->entry_time_to UTC");

                if ($entry_time_from_seconds  <= $time_entry_seconds && $entry_time_to_seconds >= $time_entry_seconds) {

                    $find_array[] = array(
                        'staff_attendence_type_id' => $result_value->staff_attendence_schedule_staff_attendence_type_id,
                        'time_schedule_seconds' => strtotime("1970-01-01 $result_value->total_institute_hour UTC")
                    );
                }
            }
            if (count($find_array) > 1) {

                if ($total_spend_time < $find_array[0]['time_schedule_seconds'] && $total_spend_time > $find_array[1]['time_schedule_seconds']) {
                    $return_attedance_type = $find_array[1]['staff_attendence_type_id'];
                }
            }

            return $return_attedance_type;
        } else {
            return false;
        }
    }	
	
	public function searchAttendenceUserTypeWithMode($user_type, $date,$mode) {
        $condition = '';

        if ($mode == 1) {
            $condition = " and staff_attendance.biometric_attendence= 0 and staff_attendance.qrcode_attendance=0";
        } elseif ($mode == 2) {
            $condition = " and staff_attendance.biometric_attendence= 0 and staff_attendance.qrcode_attendance=1";
        } elseif ($mode == 3) {
            $condition = " and staff_attendance.biometric_attendence= 1 and staff_attendance.qrcode_attendance=0";
        }

        if ($this->session->has_userdata('admin')) {
            $getStaffRole     = $this->customlib->getStaffRole();
            $staffrole   =   json_decode($getStaffRole);       
            $superadmin_visible = $this->customlib->superadmin_visible(); 
            if ($superadmin_visible == 'disabled' && $staffrole->id != 7) {                 
                $condition = " and roles.id != 7";
            } 
        }
        
        if ($user_type == "select") { 
            $query = $this->db->query("select staff_attendance.id,staff_attendance.created_at as attendence_dt, staff_attendance.staff_attendance_type_id,staff_attendance.biometric_attendence,staff_attendance.qrcode_attendance,staff_attendance.user_agent,staff_attendance.biometric_device_data,staff_attendance.remark,staff.name,staff.surname,staff.employee_id,staff.contact_no,staff.email,roles.name as user_type,IFNULL(staff_attendance.date, 'xxx') as date,staff.id as staff_id, staff_attendance_type.type as `att_type`,staff_attendance_type.key_value as `key`,staff_attendance_type.long_lang_name,staff_attendance_type.long_name_style  from staff left join staff_roles on staff_roles.staff_id = staff.id left join roles on staff_roles.role_id = roles.id left join staff_attendance on (staff.id = staff_attendance.staff_id) and staff_attendance.date = " . $this->db->escape($date) . " left join staff_attendance_type on staff_attendance_type.id = staff_attendance.staff_attendance_type_id where staff.is_active = 1 $condition order by staff_attendance.created_at asc");
        } else {
            $query = $this->db->query("select staff_attendance.staff_attendance_type_id,staff_attendance.created_at as attendence_dt,staff_attendance.biometric_attendence,staff_attendance.qrcode_attendance,staff_attendance.user_agent,staff_attendance.biometric_device_data,staff_attendance.remark,staff.name,staff.surname,staff.employee_id,staff.contact_no,staff.email,roles.name as user_type,IFNULL(staff_attendance.date, 'xxx') as date, IFNULL(staff_attendance.id, 0) as id, staff.id as staff_id ,staff_attendance_type.type as `att_type`,staff_attendance_type.key_value as `key`,staff_attendance_type.long_lang_name,staff_attendance_type.long_name_style from staff left join staff_roles on (staff.id = staff_roles.staff_id) left join roles on (roles.id = staff_roles.role_id) left join staff_attendance on (staff.id = staff_attendance.staff_id) and staff_attendance.date = " . $this->db->escape($date) . " left join staff_attendance_type on staff_attendance_type.id = staff_attendance.staff_attendance_type_id where roles.name = " . $this->db->escape($user_type) . " and staff.is_active = 1 $condition order by staff_attendance.created_at asc");

        }
        return $query->result_array();
    }

    /**
     * Dashboard widget: total + present count per role for a given date.
     * Returns array of ['role_id', 'name', 'total', 'present'].
     *
     * Raw SQL required because we need a single query with a conditional COUNT
     * on the LEFT JOIN attendance row — CI Query Builder doesn't express
     * conditional aggregates cleanly here. The date parameter is escaped.
     * Used by admin/admin/dashboard Variant B (2026-05-15).
     */
    public function getDashboardAttendanceByRole($date = null)
    {
        if ($date === null) {
            $date = date('Y-m-d');
        }
        $esc = $this->db->escape($date);
        $sql = "SELECT
                    roles.id   AS role_id,
                    roles.name AS name,
                    COUNT(DISTINCT staff.id) AS total,
                    SUM(CASE WHEN staff_attendance.id IS NOT NULL THEN 1 ELSE 0 END) AS present
                FROM staff
                LEFT JOIN staff_roles ON staff_roles.staff_id = staff.id
                LEFT JOIN roles       ON roles.id = staff_roles.role_id
                LEFT JOIN staff_attendance ON staff_attendance.staff_id = staff.id AND staff_attendance.date = {$esc}
                WHERE staff.is_active = 1 AND roles.id IS NOT NULL
                GROUP BY roles.id, roles.name
                HAVING total > 0
                ORDER BY total DESC";
        return $this->db->query($sql)->result_array();
    }

}
