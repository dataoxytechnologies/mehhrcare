<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Contenttype extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('datatables');
        $this->load->model(array('contenttype_model'));
    }

    public function index()
    {
        if (!$this->rbac->hasPrivilege('content_type', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'Download Center');
        $this->session->set_userdata('sub_menu', 'admin/contenttype');       
        $contenttype         = $this->contenttype_model->get();
        $data["contenttype"] = $contenttype;        
        $data['module'] = 'download_center';
        $this->load->view('layout/header', $data);
        $this->load->view('admin/contenttype/index', $data);
        $this->load->view('layout/footer', $data);
    }

    public function add()
    {
        if ((!$this->rbac->hasPrivilege('content_type', 'can_add'))) {
            access_denied();
        } 
        
        $this->form_validation->set_rules('name', $this->lang->line('name'), 'trim|required|xss_clean');        
        
        if ($this->form_validation->run() == false) {
            $msg = array(
                'name' => form_error('name'),
            );
            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            
                $data  = array('name' => $this->input->post("name", TRUE),'description' => $this->input->post("description", TRUE));
                $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
             
             $this->contenttype_model->add($data);
        }
        echo json_encode($array);
    } 

    public function edit()
    {
        if ((!$this->rbac->hasPrivilege('content_type', 'can_edit'))) {
            access_denied();
        }        
        
        $this->form_validation->set_rules('name', $this->lang->line('name'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('id', $this->lang->line('id'), 'trim|required|xss_clean');
        
        
        if ($this->form_validation->run() == false) {
            $msg = array(
                'name' => form_error('name'),
            );
            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            
                $data  = array('id' => $this->input->post("id", TRUE),'name' => $this->input->post("name", TRUE),'description' => $this->input->post("description", TRUE));
                $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
             
             $this->contenttype_model->add($data);
        }
        echo json_encode($array);
    }
    
    public function delete($id)
    {
        if (!$this->rbac->hasPrivilege('content_type', 'can_delete')) {
            access_denied();
        }         

        $this->contenttype_model->remove($id);
        echo json_encode(array('status' => 1, 'msg' => $this->lang->line('delete_message')));
    }
    
    public function get_data($id)
    {      
		if (!$this->rbac->hasPrivilege('content_type', 'can_view')) {
            access_denied();
        }
        $result = $this->contenttype_model->get($id);
        echo json_encode($result);
    }
}
