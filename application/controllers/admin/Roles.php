<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Roles extends Admin_Controller
{

    private $perm_category = array();

    public function __construct()
    {
        parent::__construct();
        $this->load->config('mailsms');
        $this->perm_category = $this->config->item('perm_category');
    }

    public function index()
    {
        $data['title'] = $this->lang->line('add_role');
        $this->session->set_userdata('top_menu', 'setup');
        $this->session->set_userdata('sub_menu', 'schsettings/index');
        $this->session->set_userdata('inner_menu', 'admin/roles');
        $this->form_validation->set_rules(
            'name', $this->lang->line('name'), array(
                'required', 'xss_clean',
                array('check_exists', array($this->role_model, 'valid_check_exists')),
            )
        );
        if ($this->form_validation->run() == false) {
            $listroute         = $this->role_model->get();
            $data['listroute'] = $listroute;
            $data['module'] = 'setup';
            $this->load->view('layout/header', $data);
            $this->load->view('admin/roles/create', $data);
            $this->load->view('layout/footer', $data);
        } else {
            $data = array(
                'name' => $this->input->post('name', TRUE),
            );
            $this->role_model->add($data);
            $this->session->set_flashdata('msg', '<div class="alert alert-success text-left">' . $this->lang->line('success_message') . '</div>');
            redirect('admin/roles');
        }
    }

    public function permission($id, $open_tab = 0)
    {
        $data['title']           = $this->lang->line('add_role');
        $data['id']              = $id;
        $role                    = $this->role_model->get($id);
        $role_id                 = $role['id'];
        $data['role']            = $role;
        $data['open_tab']        = $open_tab;
        $role_permission         = $this->role_model->find($role['id']);
        $data['role_permission'] = $role_permission;
        $data['module'] = 'setup';
        $this->load->view('layout/header', $data);
        $this->load->view('admin/roles/allotmodule', $data);
        $this->load->view('layout/footer', $data);
    }
	
	public function savecheck()
    {
        $this->form_validation->set_error_delimiters('<p>', '</p>');
        $this->form_validation->set_rules('action', $this->lang->line('action'), 'required|trim|xss_clean');
        $this->form_validation->set_rules('per_cat', $this->lang->line('permission_category'), 'required|trim|xss_clean');
        $this->form_validation->set_rules('role_id', $this->lang->line('role'), 'required|trim|xss_clean');
        $this->form_validation->set_rules('add_remove', $this->lang->line('add_remove'), 'required|trim|xss_clean');

        if ($this->form_validation->run() == false) {
            $data = array(
                'action'   => form_error('action'),
                'per_cat' => form_error('per_cat'),
                'role_id' => form_error('role_id'),
                'add_remove' => form_error('add_remove'),
            );
            $array = array('status' => 0, 'error' => $data);
            echo json_encode($array);
        } else {

                $update_array=[
                    'action'=>$this->input->post('action', TRUE),
                    'perm_cat_id'=>$this->input->post('per_cat', TRUE),
                    'role_id'=>$this->input->post('role_id', TRUE),
                    'value'=>$this->input->post('add_remove', TRUE)
                ];
 
            $this->role_model->updatePermission($update_array);
            $array = array('status' => 1, 'error' => '', 'message' => $this->lang->line('permission_updated_successfully'));
            echo json_encode($array);
        }
    }
	
    public function edit($id)
    {
        $data['title']    = $this->lang->line('edit_role');
        $data['id']       = $id;
        $editrole         = $this->role_model->get($id);
        $data['editrole'] = $editrole;
        $data['name']     = $editrole["name"];

        $this->form_validation->set_rules(
            'name', $this->lang->line('name'), array(
                'required', 'xss_clean',
                array('check_exists', array($this->role_model, 'valid_check_exists')),
            )
        );
        if ($this->form_validation->run() == false) {
            $listroute         = $this->role_model->get();
            $data['listroute'] = $listroute;
            $data['module'] = 'setup';
            $this->load->view('layout/header', $data);
            $this->load->view('admin/roles/edit', $data);
            $this->load->view('layout/footer', $data);
        } else {
            $data = array(
                'id'   => $id,
                'name' => $this->input->post('name', TRUE),
            );
            $this->role_model->add($data);
            $this->session->set_flashdata('msg', '<div class="alert alert-success text-left">' . $this->lang->line('update_message') . '</div>');
            redirect('admin/roles/index');
        }
    }

    public function delete($id)
    {
        $data['title'] = $this->lang->line('fees_master_list');
        $this->role_model->remove($id);
        echo json_encode(array('status' => 1, 'msg' => $this->lang->line('delete_message')));
    }

}
