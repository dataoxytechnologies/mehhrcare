<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Appointpriority extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('form_validation');
        $this->load->model('appoint_priority_model');
    }

    public function index()
    {
        if (!$this->rbac->hasPrivilege('appointment_priority', 'can_view')) {
            access_denied();
        }

        $this->session->set_userdata('top_menu', 'setup');
        $this->session->set_userdata('sub_sidebar_menu', 'admin/appointpriority/index');
        $this->session->set_userdata('sub_menu', 'admin/onlineappointment');
        
        $this->form_validation->set_rules('appoint_priority', $this->lang->line('appointment_priority'), 'trim|required|xss_clean');

        if ($this->form_validation->run() == false) {
            $data['appoint_priority_list'] = $this->appoint_priority_model->appoint_priority_list();
            $data['module'] = 'front_office';
            $this->load->view('layout/header', $data);
            $this->load->view('admin/frontoffice/appointpriorityview', $data);
            $this->load->view('layout/footer', $data);
        } else {
            $appoint_priority = array(
                'appoint_priority' => $this->input->post('appoint_priority', TRUE),
                'description'      => $this->input->post('description', TRUE),
            );
            $this->appoint_priority_model->add($appoint_priority);
            $this->session->set_flashdata('msg', '<div class="alert alert-success">' . $this->lang->line('appointment_priority_added_successfully') . '</div>');
            redirect('admin/appointpriority');
        }
    }

   
    /* this funtion is used to save multiple priority */
    public function add_multiple_priority() {   
        $total_rows = $this->input->post('total_rows');
        if (isset($total_rows) && !empty($total_rows)) {          
          
          //validation to check each input keeps the value or not
            foreach ($total_rows as $row_key => $row_value) {  
                $this->form_validation->set_rules('appoint_priority_'.$row_value, $this->lang->line('priority'), 'trim|required|xss_clean');        
                
                if ($this->form_validation->run() == false) {           
                    $msg = array(
                        'name' => form_error('appoint_priority_'.$row_value),
                    );
                    $json_array = array('status' => 'fail', 'error' => $msg, 'message' => '');  
                    echo json_encode($json_array);
                    return false;          
                }
            } 
            //validation to check each input keeps the value or not

            // save the multiple data 
            $total_rows   = $this->input->post('total_rows');  
            foreach ($total_rows as $row_key => $row_values) {                 
                $data  = array('appoint_priority' => $this->input->post('appoint_priority_'.$row_values, TRUE));
                $this->appoint_priority_model->add($data);
            }
            // save the multiple data 

            $json_array = array('status' => 1, 'error' => '', 'message' => $this->lang->line('success_message'));      
            echo json_encode($json_array);
    }else{
        //validation if user not added any row 
        $message= $this->lang->line("please_add_at_least_one_priority");
        $json_array = array('status' => '2', 'error' => $message ,'message' => '');  
        //validation if user not added any row 
        echo json_encode($json_array);
    }
}


    public function edit()
    {
        $this->form_validation->set_rules('appoint_priority', $this->lang->line('priority'), 'trim|required|xss_clean');
        $id = $this->input->post('id', TRUE);
        if ($this->form_validation->run() == false) {

            $msg = array(
                'name' => form_error('appoint_priority'),
            );

            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {

            $appoint_priority = array(
                'appoint_priority' => $this->input->post('appoint_priority', TRUE),
            );

            $this->appoint_priority_model->update($id, $appoint_priority);
            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('update_message'));
        }

        echo json_encode($array);
    }

    public function edit1($appoint_priority_id)
    {
        if (!$this->rbac->hasPrivilege('appointment_priority', 'can_edit')) {
            access_denied();
        }
        $this->form_validation->set_rules('appoint_priority', $this->lang->line('appointment_priority'), 'trim|required|xss_clean');

        if ($this->form_validation->run() == false) {
            $data['appoint_priority_list'] = $this->appoint_priority_model->appoint_priority_list();
            $data['appoint_priority_data'] = $this->appoint_priority_model->appoint_priority_list($appoint_priority_id);
            $data['module'] = 'front_office';
            $this->load->view('layout/header', $data);
            $this->load->view('admin/frontoffice/appointpriorityview', $data);
            $this->load->view('layout/footer', $data);
        } else {
            $appoint_priority = array(
                'appoint_priority' => $this->input->post('appoint_priority', TRUE),
            );
            $this->appoint_priority_model->update($appoint_priority_id, $appoint_priority);
            $this->session->set_flashdata('msg', '<div class="alert alert-success"> ' . $this->lang->line('appointment_priority_updated_successfully') . '</div>');
            redirect('admin/visitorspurpose');
        }
    }

    public function delete($id)
    {
        if (!$this->rbac->hasPrivilege('appointment_priority', 'can_delete')) {
            access_denied();
        }
        $this->appoint_priority_model->delete($id);
        echo json_encode(array('status' => 1, 'msg' => $this->lang->line('delete_message')));
    }

    public function get_data($id)
    {
        $result = $this->appoint_priority_model->appoint_priority_list($id);
        echo json_encode($result);
    }

}
