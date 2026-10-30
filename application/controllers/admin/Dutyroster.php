<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Dutyroster extends Admin_Controller
{
	public $search_type;
	public $time_format;


    public function __construct()
    {
        parent::__construct();
      
        $this->load->library('datatables');      
        $this->load->model('dutyroster_model');
        $this->load->model('floor_model');
        $this->load->model('bed_model');
        $this->load->model('staff_model');
		$this->config->load("payroll");
		$this->search_type = $this->config->item('search_type');
		$this->time_format = $this->customlib->getHospitalTimeFormat();
    }

	//----------------------- Duty Roster Shift Start -------------------
    public function shift(){  
		if (!$this->rbac->hasPrivilege('roster_shift', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'dutyroster');
        $this->session->set_userdata('sub_menu', 'dutyroster/shift');		
        $data['module'] = 'duty_roster';
        $this->load->view("layout/header", $data);
        $this->load->view("admin/dutyroster/shift", $data);
        $this->load->view("layout/footer", $data);
    }

    public function getShiftDatatable()
    {
        $dt_response = $this->dutyroster_model->getDatatableAllRecord();
        $dt_response = json_decode($dt_response);
        $dt_data     = array();
        if (!empty($dt_response->data)) {
            foreach ($dt_response->data as $shift_key => $shift_value) {

                $row    = array();
					$action = "<div class=''>";	
					
					if ($this->rbac->hasPrivilege('roster_shift', 'can_edit')) {	
						$action .= "<a  href='javascript:void(0)' class='btn btn-default btn-xs edit_record edit_shift_modal' data-loading-text='" . $this->lang->line('please_wait') . "' data-bs-toggle='tooltip' data-record-id=" . $shift_value->id . "  title='" . $this->lang->line('edit') . "'><i class='fa fa-pencil'></i></a>";
					}
					
					if ($this->rbac->hasPrivilege('roster_shift', 'can_delete')) {
						$action .= "<a class='btn btn-default btn-xs' data-bs-toggle='tooltip' title='" . $this->lang->line('delete') . "' onclick='delete_shift(" . $shift_value->id . ")' data-original-title='" . $this->lang->line('delete') . "'> <i class='fa fa-trash'></i></a>";
					}
			  
					$action .= "</div>";

                $row[] = $shift_value->shift_name;
                $row[] = $this->customlib->getHospitalTime_Format($shift_value->shift_start);
                $row[] = $this->customlib->getHospitalTime_Format($shift_value->shift_end);
                $row[] = $shift_value->shift_hour;
                $row[] = $action;

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

    public function add_shift()
    {        
		
        $this->form_validation->set_rules('shift_name', $this->lang->line('shift_name'), 'required|xss_clean');
        $this->form_validation->set_rules('shift_start', $this->lang->line('shift_start'), 'required|xss_clean');
        $this->form_validation->set_rules('shift_end', $this->lang->line('shift_end'), 'required|xss_clean');
		
        if ($this->form_validation->run() == false) {
            $msg = array(
                'shift_name'     	=> form_error('shift_name'),                
                'shift_start'     	=> form_error('shift_start'),                
                'shift_end'     	=> form_error('shift_end'),                
            );
            $json_array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
			
			if($this->customlib->getHospitalTimeFormat()){			 
				$datetime_1     =  $this->input->post('shift_start', TRUE);
				$datetime_2     =  $this->input->post('shift_end', TRUE);
			}else{
				$datetime_1 	= date("H:i:s", strtotime($this->input->post('shift_start', TRUE)));
				$datetime_2   	= date("H:i:s", strtotime($this->input->post('shift_end', TRUE)));
			}
				$check_shift_time   =  $this->dutyroster_model->check_shift_time($datetime_1,$datetime_2,$this->input->post('shift_id', TRUE));
				if($check_shift_time!=0){
					
					if (strtotime($datetime_1) > strtotime($datetime_2)){						
						$timein = date("Y-m-d H:i:s",strtotime($this->input->post('shift_start', TRUE)));
						$timeout = date('Y-m-d H:i:s', strtotime($this->input->post('shift_end', TRUE)) + 86400);  
						$start_timein = new DateTime($timein);						
						$diff = $start_timein->diff(new DateTime($timeout)); 
					}else{
						$start_datetime = new DateTime($datetime_1); 
						$diff = $start_datetime->diff(new DateTime($datetime_2));
					}
					
					$data = array(
						'id'                => $this->input->post('shift_id', TRUE),
						'shift_name'        => $this->input->post('shift_name', TRUE),
						'shift_start'       => $datetime_1,
						'shift_end'         => $datetime_2,
						'shift_hour'        => $diff->h.":".$diff->i.":".$diff->s,               
					);
            
					$insert_id  = $this->dutyroster_model->add_shift($data);
					$json_array = array('status' => 1, 'error' => '', 'message' => $this->lang->line('success_message'));
				}else{
					$json_array = array('status' => 2, 'error' => $this->lang->line("duplicate_shift_time_not_allowed"), 'message' => '');
				}
				
        }
        echo json_encode($json_array);
    }
	
	public function delete_shift($id)
    {    
		if (!$this->rbac->hasPrivilege('roster_shift', 'can_delete')) {
            access_denied();
        }
        $result = $this->dutyroster_model->delete_shift($id);
        echo json_encode(array('status' => 1, 'msg' => $this->lang->line('delete_message')));        
    }
    
    public function getShiftDetails()
    {      
        $id           = $this->input->post("shift_id", TRUE);
        $result       = $this->dutyroster_model->getShiftList($id);
		$result['shift_start']  = $this->customlib->getHospitalTime_Format($result['shift_start']);
		$result['shift_end']  	= $this->customlib->getHospitalTime_Format($result['shift_end']);
		
        $json_array   = array('status' => '1', 'error' => '', 'result' => $result);
        echo json_encode($json_array);
    }
	
	//----------------------- Duty Roster Shift End-------------------
	
	//----------------------- Duty Roster List Start -------------------
	
	public function rosterlist()
    {       
		if (!$this->rbac->hasPrivilege('roster_list', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'dutyroster');
        $this->session->set_userdata('sub_menu', 'dutyroster/rosterlist');		
		$data['shift_list']   = $this->dutyroster_model->getShiftList();		
        $data['module'] = 'duty_roster';
        $this->load->view("layout/header", $data);
        $this->load->view("admin/dutyroster/rosterlist", $data);
        $this->load->view("layout/footer", $data);
    }

    public function add_roster_list()
    {
        $this->form_validation->set_rules('duty_roster_shift_id', $this->lang->line('shift_name'), 'required|xss_clean');
        $this->form_validation->set_rules('duty_roster_start_date', $this->lang->line('start_date'), 'required|xss_clean');
        $this->form_validation->set_rules('duty_roster_end_date', $this->lang->line('end_date'), 'required|xss_clean');
        
        if ($this->form_validation->run() == false) {
            $msg = array(
                'duty_roster_shift_id'         => form_error('duty_roster_shift_id'),                
                'duty_roster_start_date'       => form_error('duty_roster_start_date'),                
                'duty_roster_end_date'         => form_error('duty_roster_end_date'),                
            );
            $json_array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {	

            $duty_roster_shift_id    =   $this->input->post('duty_roster_shift_id', TRUE);
			$datetime_1              =   $this->customlib->dateFormatToYYYYMMDD($this->input->post('duty_roster_start_date', TRUE));
			$datetime_2              =   $this->customlib->dateFormatToYYYYMMDD($this->input->post('duty_roster_end_date', TRUE));
            $days = ((strtotime($datetime_2) - strtotime($datetime_1)) / (60 * 60 * 24))+1;
 
            $check_roster_date   =  $this->dutyroster_model->check_roster_date($datetime_1,$datetime_2,$this->input->post('id', TRUE));
			 
             if($check_roster_date!=0){
                $data = array(
                    'id'                         => $this->input->post('id', TRUE),
                    'duty_roster_shift_id'       => $this->input->post('duty_roster_shift_id', TRUE),
                    'duty_roster_start_date'     => $this->customlib->dateFormatToYYYYMMDD($this->input->post("duty_roster_start_date", TRUE)),
                    'duty_roster_end_date'       => $this->customlib->dateFormatToYYYYMMDD($this->input->post("duty_roster_end_date", TRUE)),
                    'duty_roster_total_day'      => $days,               
                );
            
                $insert_id  = $this->dutyroster_model->add_roster_list($data);
                $json_array = array('status' => 1, 'error' => '', 'message' => $this->lang->line('success_message'));
            }else{
                $json_array = array('status' => 2, 'error' => $this->lang->line("duplicate_roster_shift_and_date_not_allowed"), 'message' => '');
            }
		}
        echo json_encode($json_array);

    }
	
	public function getRosterListDatatable()
    {
        $dt_response = $this->dutyroster_model->getRosterListDatatable();		 
        $dt_response = json_decode($dt_response);
        $dt_data     = array();
        if (!empty($dt_response->data)) {
            foreach ($dt_response->data as $shift_key => $shift_value) {
                $row  = array();
					$action = "<div class=''>";	
					if ($this->rbac->hasPrivilege('roster_list', 'can_delete')) {	
						$action .= "<a class='btn btn-default btn-xs' data-bs-toggle='tooltip' title='" . $this->lang->line('delete') . "' onclick='delete_roster_list(" . $shift_value->id . ")' data-original-title='" . $this->lang->line('delete') . "'> <i class='fa fa-trash'></i></a>";
					}
					
					$action .= "</div>";

                $row[] = $shift_value->shift_name;
                $row[] = $this->customlib->YYYYMMDDTodateFormat($shift_value->duty_roster_start_date);
                $row[] = $this->customlib->YYYYMMDDTodateFormat($shift_value->duty_roster_end_date);
                $row[] = $this->customlib->getHospitalTime_Format($shift_value->shift_start);
                $row[] = $this->customlib->getHospitalTime_Format($shift_value->shift_end);
                $row[] = $shift_value->shift_hour;
                $row[] = $shift_value->duty_roster_total_day;
                $row[] = $action;

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

    public function getRosterListDetails()
    {      
        $id           = $this->input->post("record_id", TRUE);
        $result       = $this->dutyroster_model->getRosterListDetails($id); 
        $result['roster_start_date'] = $this->customlib->YYYYMMDDTodateFormat($result['duty_roster_start_date']) ;    
        $result['roster_end_date']   = $this->customlib->YYYYMMDDTodateFormat($result['duty_roster_end_date']) ;   
        $json_array   = array('status' => '1', 'error' => '', 'result' => $result);
        echo json_encode($json_array);
    }    

    public function delete_roster_list($id)
    {       
		if (!$this->rbac->hasPrivilege('roster_list', 'can_delete')) {
            access_denied();
        }
        $result = $this->dutyroster_model->delete_roster_list($id);
        echo json_encode(array('status' => 1, 'msg' => $this->lang->line('delete_message')));        
    }	
	
	//----------------------- Duty Roster List End -------------------

	//----------------------- Duty Roster Assign Start -------------------
	
	public function rosterassign()
    {       
		if (!$this->rbac->hasPrivilege('roster_assign', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'dutyroster');
        $this->session->set_userdata('sub_menu', 'dutyroster/rosterassign');		
        
        $data['shift_list'] 	 	= $this->dutyroster_model->getshiftlist();       
        $data['roster_list'] 	 	= $this->dutyroster_model->getRosterList();       
        $data['floor'] 			 	= $this->floor_model->floor_list();
        $data['department_list'] 	= $this->department_model->getDepartmentType();
        $data['staffRole']       	= $this->staff_model->getStaffRole();
        $resultlist         		= $this->staff_model->searchFullText("", 1);
        $data['resultlist'] 		= $resultlist;

        $data['module'] = 'duty_roster';
        $this->load->view("layout/header", $data);
        $this->load->view("admin/dutyroster/rosterassign", $data);
        $this->load->view("layout/footer", $data);
    }

	public function add_roster_assign(){

        $this->form_validation->set_rules('duty_roster_staff', $this->lang->line('staff'), 'required|xss_clean');
        $this->form_validation->set_rules('duty_roster_list_id', $this->lang->line('shift_date'), 'required|xss_clean');
        
        if ($this->form_validation->run() == false) {
            $msg = array(
                'duty_roster_list_id'     => form_error('duty_roster_list_id'),                
                'duty_roster_staff'       => form_error('duty_roster_staff'),                
            );
            $json_array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else{
            $duty_roster_staff_array     =     $this->input->post('duty_roster_staff', TRUE);
            $duty_roster_list_id         =     $this->input->post('duty_roster_list_id', TRUE);
			 $edit_code					 =	   $this->input->post('edit_code', TRUE);
			 
            $check_roster_assign =  $this->dutyroster_model->check_roster_assign($duty_roster_list_id,$duty_roster_staff_array,$edit_code); 
  

            if($check_roster_assign!=0){
                $roster_list_id     =   $this->input->post('duty_roster_list_id', TRUE);
                $getrosterdate      =   $this->dutyroster_model->getRosterList($roster_list_id);

                foreach($getrosterdate as $key=>$value){
                    $from_date=$value['duty_roster_start_date'];
                    $duty_roster_total_day=$value['duty_roster_total_day'];
                } 

                //delete old recored
                if($edit_code!=0 && !empty($edit_code) && $edit_code!=""){
                    $this->dutyroster_model->delete_roster_assign($edit_code);
                }
                //delete old recored
        
                $max_id             =   $this->dutyroster_model->getmaxroster_assign_id();
                if(empty($max_id)){
                    $code=0;
                }else{
                    $code=$max_id->code;
                }
                $maxid=($code+1);
                $j=0;
				if($this->input->post('duty_roster_floor', TRUE)){
					$duty_roster_floor	=	$this->input->post('duty_roster_floor', TRUE);
				}else{
					$duty_roster_floor	=	null;
				}
				if($this->input->post('duty_roster_department', TRUE)){
					$duty_roster_department	=	$this->input->post('duty_roster_department', TRUE);
				}else{
					$duty_roster_department	=	null;
				}
				
                for($i=1;$i<=$duty_roster_total_day;$i++){                
                     $data = array(
                    'code'                  => $maxid,
                    'floor_id'              => $duty_roster_floor,
                    'department_id'         => $duty_roster_department,
                    'staff_id'              => $duty_roster_staff_array,
                    'duty_roster_list_id'   => $this->input->post('duty_roster_list_id', TRUE),
                    'roster_duty_date'      => date('Y-m-d', strtotime($from_date. " + $j days")),
                    'generated_by'      => $this->customlib->getStaffID()
                );
            
                $insert_id  = $this->dutyroster_model->add_roster_assign($data);
                $j++;
                }  
               
                $json_array = array('status' => 1, 'error' => '', 'message' => $this->lang->line('success_message'));
            }else{
                $json_array = array('status' => 2, 'error' => $this->lang->line("duplicate_roster_assigned_not_allowed"), 'message' => '');
            }
        }
        echo json_encode($json_array);
    }

    public function getdutyRoster_assignList()
    {
        $dt_response = $this->dutyroster_model->getdutyRoster_assignList();  
        $dt_response = json_decode($dt_response);
        $dt_data     = array();

        if (!empty($dt_response->data)) {
            foreach ($dt_response->data as $shift_key => $shift_value) {

                $row    = array();
                $action = "<div class=''>";
				
				if ($this->rbac->hasPrivilege('roster_assign', 'can_edit')) {	
					$action .= "<a  href='javascript:void(0)' class='btn btn-default btn-xs edit_record edit_assign_roster' data-loading-text='" . $this->lang->line('please_wait') . "' data-bs-toggle='tooltip' data-record_id=" . $shift_value->code . "  title='" . $this->lang->line('edit') . "'><i class='fa fa-pencil'></i></a>";
				}
				if ($this->rbac->hasPrivilege('roster_assign', 'can_delete')) {					
					$action .= "<a class='btn btn-default btn-xs' data-bs-toggle='tooltip' title='" . $this->lang->line('delete') . "' onclick='delete_roster_assign(" . $shift_value->code . ")' data-original-title='" . $this->lang->line('delete') . "'> <i class='fa fa-trash'></i></a>";
				}
				
                $action .= "</div>";

                $duty_roster_start_date = $this->customlib->YYYYMMDDTodateFormat($shift_value->duty_roster_start_date);
                $duty_roster_end_date   = $this->customlib->YYYYMMDDTodateFormat($shift_value->duty_roster_end_date);

				$row[] = $shift_value->staff_name." ".$shift_value->staff_surname." (".$shift_value->staff_employee_id.")";				
                $row[] = $shift_value->floor_name;                
                $row[] = $shift_value->department_name;                
                $row[] = $shift_value->shift_name;
                $row[] = $duty_roster_start_date." - ".$duty_roster_end_date;
                $row[] = $this->customlib->getHospitalTime_Format($shift_value->shift_start)." - ".$this->customlib->getHospitalTime_Format($shift_value->shift_end);
                $row[] = composeStaffNameByString($shift_value->generated_byname, $shift_value->generated_bysurname, $shift_value->generated_byemployee_id);
                $row[] = $action;

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

    public function edit_assign_roster()
    {
        $id           = $this->input->post("id", TRUE);
        $result       = $this->dutyroster_model->getassignRosterDetails($id);
		 
        $json_array   = array('status' => '1', 'error' => '', 'result' => $result);
        echo json_encode($json_array);
    }
    
    public function delete_roster_assign($code)
    {       
		if (!$this->rbac->hasPrivilege('roster_assign', 'can_delete')) {
            access_denied();
        }
        $result = $this->dutyroster_model->delete_roster_assign($code);
        echo json_encode(array('status' => 1, 'msg' => $this->lang->line('delete_message')));        
    }
	
	public function getrosterlistbyshift()
    {
        $shift = $this->input->get('shift', TRUE);
        $data     = $this->dutyroster_model->getRosterListByShiftId($shift);
        echo json_encode($data);
    }
    
	//----------------------- Duty Roster Assign End -------------------

    //----------------------- Duty Roster Report Start -----------------
    public function roster_report()
    {
        if (!$this->rbac->hasPrivilege('duty_roster', 'can_view')) { access_denied(); }
        $this->session->set_userdata('top_menu', 'dutyroster');
        $this->session->set_userdata('sub_menu', 'dutyroster/roster_report');

        $data['searchlist']  = $this->search_type;
        $data['staff_list']  = $this->staff_model->searchFullText("", 1);
        $data['search_type'] = $this->input->post('search_type', TRUE);
        $data['date_from']   = $this->input->post('date_from', TRUE);
        $data['date_to']     = $this->input->post('date_to', TRUE);
        $staff               = $this->input->post('staff', TRUE);
        $data['staff']       = $staff;
        $data['roster_data'] = array();

        $this->form_validation->set_rules('search_type', $this->lang->line('search_type'), 'trim|required|xss_clean');

        if ($this->form_validation->run()) {
            if ($data['search_type'] == 'period') {
                $start_date = $this->customlib->dateFormatToYYYYMMDD($data['date_from']);
                $end_date   = $this->customlib->dateFormatToYYYYMMDD($data['date_to']);
            } else {
                $dates      = $this->customlib->get_betweendate($data['search_type']);
                $start_date = $dates['from_date'];
                $end_date   = $dates['to_date'];
            }
            $data['roster_data'] = $this->dutyroster_model->getrosterassign_datails($staff, $start_date, $end_date);
        }

        $data['module'] = 'duty_roster';
        $this->load->view("layout/header", $data);
        $this->load->view("admin/dutyroster/roster_report", $data);
        $this->load->view("layout/footer", $data);
    }

    public function roster_report_print()
    {
        if (!$this->rbac->hasPrivilege('duty_roster', 'can_view')) {
            access_denied();
        }
        $staff             = $this->input->post('staff', TRUE);
        $search_type       = $this->input->post('search_type', TRUE);
        $date_from         = $this->input->post('date_from', TRUE);
        $date_to           = $this->input->post('date_to', TRUE);
        $staff_name        = $this->input->post('staff_name', TRUE);
        $search_type_label = $this->input->post('search_type_label', TRUE);

        if ($search_type == 'period') {
            $start_date = $this->customlib->dateFormatToYYYYMMDD($date_from);
            $end_date   = $this->customlib->dateFormatToYYYYMMDD($date_to);
        } else {
            if ($search_type != '') {
                $dates = $this->customlib->get_betweendate($search_type);
            } else {
                $dates = $this->customlib->get_betweendate('this_year');
            }
            $start_date = $dates['from_date'];
            $end_date   = $dates['to_date'];
        }

        $data['roster_data']        = $this->dutyroster_model->getrosterassign_datails($staff, $start_date, $end_date);
        $data['staff_name']         = $staff_name;
        $data['search_type_label']  = $search_type_label;
        $data['date_from']          = $date_from;
        $data['date_to']            = $date_to;
        $data['start_date_display'] = $this->customlib->YYYYMMDDTodateFormat($start_date);
        $data['end_date_display']   = $this->customlib->YYYYMMDDTodateFormat($end_date);
        $page = $this->load->view('admin/dutyroster/print/roster_report_print', $data, TRUE);
        echo json_encode(array('status' => 1, 'page' => $page));
    }

    //----------------------- Duty Roster Report End -------------------
    
}
