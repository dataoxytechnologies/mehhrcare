<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Icd10 extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->helper('url');
        $this->load->model('icd10_model');
    }

    // ─── ICD CODES TAB ───────────────────────────────────────────────────────

    public function index()
    {
        if (!$this->rbac->hasPrivilege('icd10_codes', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'setup');
        $this->session->set_userdata('sub_menu', 'icd10/index');
        $this->session->set_userdata('sub_sidebar_menu', 'setup/icd10/icd10_codes');
        $data['title']       = 'ICD-10 Codes';
        $data['codesresult'] = $this->icd10_model->get();
        $data['groupsresult'] = $this->icd10_model->getgroup();
        $data['module'] = 'setup';
        $this->load->view('layout/header', $data);
        $this->load->view('admin/icd10/icd10List', $data);
        $this->load->view('layout/footer', $data);
    }

    // ─── ICD GROUPS TAB ──────────────────────────────────────────────────────

    public function icd10group()
    {
        if (!$this->rbac->hasPrivilege('icd10_groups', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'setup');
        $this->session->set_userdata('sub_menu', 'icd10/index');
        $this->session->set_userdata('sub_sidebar_menu', 'setup/icd10/icd10_groups');
        $data['title']        = 'ICD-10 Groups';
        $data['groupsresult'] = $this->icd10_model->getgroup();
        $data['module'] = 'setup';
        $this->load->view('layout/header', $data);
        $this->load->view('admin/icd10/icd10groupList', $data);
        $this->load->view('layout/footer', $data);
    }

    // ─── AJAX: ADD / EDIT CODE ───────────────────────────────────────────────

    public function add()
    {
        if (!$this->rbac->hasPrivilege('icd10_codes', 'can_add')) {
            access_denied();
        }
        $this->form_validation->set_rules('icd_code', $this->lang->line('icd10_code'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('icd_description', $this->lang->line('icd10_description'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('group_id', $this->lang->line('icd10_group'), 'trim|required|xss_clean');
        if ($this->form_validation->run() == false) {
            $msg = array(
                'icd_code'        => form_error('icd_code'),
                'icd_description' => form_error('icd_description'),
                'group_id'        => form_error('group_id'),
            );
            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $data = array(
                'icd_code'        => $this->input->post('icd_code', TRUE),
                'icd_description' => $this->input->post('icd_description', TRUE),
                'group_id'        => $this->input->post('group_id', TRUE),
            );
            $id = $this->input->post('code_id', TRUE);
            if (!empty($id)) {
                $data['id'] = $id;
            }
            $this->icd10_model->add($data);
            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
        }
        echo json_encode($array);
    }

    // ─── AJAX: ADD / EDIT GROUP ──────────────────────────────────────────────

    public function addgroup()
    {
        if (!$this->rbac->hasPrivilege('icd10_groups', 'can_add')) {
            access_denied();
        }
        $this->form_validation->set_rules('group_name', $this->lang->line('group_name'), 'trim|required|xss_clean');
        if ($this->form_validation->run() == false) {
            $msg   = array('group_name' => form_error('group_name'));
            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $data = array(
                'group_name'  => $this->input->post('group_name', TRUE),
                'description' => $this->input->post('description', TRUE),
            );
            $id = $this->input->post('group_id', TRUE);
            if (!empty($id)) {
                $data['id'] = $id;
            }
            $this->icd10_model->addgroup($data);
            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
        }
        echo json_encode($array);
    }

    // ─── AJAX: DELETE CODE ───────────────────────────────────────────────────

    public function delete($id)
    {
        if (!$this->rbac->hasPrivilege('icd10_codes', 'can_delete')) {
            access_denied();
        }
        $this->icd10_model->remove($id);
        echo json_encode(array('status' => 1, 'msg' => $this->lang->line('delete_message')));
    }

    // ─── AJAX: DELETE GROUP ──────────────────────────────────────────────────

    public function deletegroup($id)
    {
        if (!$this->rbac->hasPrivilege('icd10_groups', 'can_delete')) {
            access_denied();
        }
        $this->icd10_model->removegroup($id);
        echo json_encode(array('status' => 1, 'msg' => $this->lang->line('delete_message')));
    }

    // ─── AJAX: GET SINGLE CODE (edit modal) ─────────────────────────────────

    public function get_data($id)
    {
        $result = $this->icd10_model->get($id);
        echo json_encode($result);
    }

    // ─── AJAX: GET SINGLE GROUP (edit modal) ────────────────────────────────

    public function getgroup_data($id)
    {
        $result = $this->icd10_model->getgroup($id);
        echo json_encode($result);
    }

    // ─── AJAX: GET CODES BY GROUP (IPD/OPD form dropdown) ───────────────────

    public function get_codes_by_group()
    {
        $group_id = $this->input->post('group_id', TRUE);
        if (empty($group_id)) {
            $result = $this->icd10_model->get();
        } else {
            $result = $this->icd10_model->getByGroup($group_id);
        }
        echo json_encode($result);
    }

}
