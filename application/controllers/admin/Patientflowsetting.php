<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Patientflowsetting - Patient Flow Settings section.
 * URL: admin/patientflowsetting/setting?group=constitution_master
 */
class Patientflowsetting extends Admin_Controller
{
    private $groups = array(
        'constitution_master' => 'Constitution Master',
        'stage_master' => 'Stage Master',
        'medicine_master' => 'Medicine Master',
        'f_series_mapping' => 'F-Series Mapping',
        'daily_mapping' => 'Daily Mapping',
        'broad_area_rules' => 'Broad Area Rules',
    );
    private $programme_groups = array(
        'programme_group' => 'Programme Group',
        'programme_master' => 'Programme Master',
        'fee_master' => 'Fee Master',
        'discounts' => 'Discounts & Campaigns',
        'patient_accounts' => 'Patient Fee Accounts',
        'schedules' => 'Patient Fee Schedules',
        'consultation_fees' => 'Consultation & Vial Fees',
    );

    public function __construct()
    {
        parent::__construct();
        $this->load->model('patientflow_model');
        $this->lang->load('patientflow');
        $this->load->library('fee_engine');
    }

    public function index()
    {
        redirect('admin/patientflowsetting/setting?group=constitution_master');
    }

    public function setting()
    {
        if (!$this->rbac->hasPrivilege('patient_flow', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'patient_flow');
        $this->session->set_userdata('sub_menu', 'patient_flow_setting');
        $group = trim((string) $this->input->get('group', true));
        if ($group === '' || (!isset($this->programme_groups[$group]) && !isset($this->groups[$group]))) {
            $group = 'constitution_master';
        }
        $data['title'] = $this->lang->line('patient_flow_setting');
        $data['title'] = $data['title'] ? $data['title'] : 'Patient Flow Setting';
        $data['groups'] = $this->groups;
        $data['programme_groups'] = $this->programme_groups;
        $data['active_group'] = $group;
        if ($group === 'constitution_master') {
            $data['body_builds'] = $this->patientflow_model->get_constitution_lookups('body');
            $data['face_shapes'] = $this->patientflow_model->get_constitution_lookups('face');
            $data['nose_types'] = $this->patientflow_model->get_constitution_lookups('nose');
            $data['constitutions'] = $this->patientflow_model->get_constitution_list();
        } elseif ($group === 'programme_master') {
            $data['programmes'] = $this->patientflow_model->get_programmes();
            $data['subcategories'] = $this->patientflow_model->get_subcategories();
        } elseif ($group === 'stage_master') {
            $data['stages'] = $this->patientflow_model->get_stages();
        } elseif ($group === 'medicine_master') {
            $data['medicines'] = $this->patientflow_model->get_medicines();
            $data['medicines_grouped'] = $this->patientflow_model->get_medicines_grouped();
        } elseif ($group === 'f_series_mapping') {
            $data['broad_series'] = $this->patientflow_model->get_broad_series();
            $data['stages'] = $this->patientflow_model->get_stages();
            $data['f_medicines'] = $this->patientflow_model->get_medicines('F');
            $fid = (int) $this->input->get('series_id', true);
            $data['filter_series_id'] = $fid;
            $data['f_mappings'] = $this->patientflow_model->get_f_mapping($fid);
        } elseif ($group === 'daily_mapping') {
            $data['stages'] = $this->patientflow_model->get_stages();
            $data['fa_medicines'] = $this->patientflow_model->get_medicines('FA');
            $data['fb_medicines'] = $this->patientflow_model->get_medicines('FB');
            $data['fc_medicines'] = $this->patientflow_model->get_medicines('FC');
            $data['fd_medicines'] = $this->patientflow_model->get_medicines('FD');
            $data['early_mappings'] = $this->patientflow_model->get_early_mapping();
            $data['universal_mappings'] = $this->patientflow_model->get_universal_mapping();
            $data['medicines_grouped'] = $this->patientflow_model->get_medicines_grouped();
        } elseif ($group === 'broad_area_rules') {
            $data['broad_rules'] = $this->patientflow_model->get_broad_rules();
            $data['broad_series'] = $this->patientflow_model->get_broad_series();
        } elseif ($group === 'fee_master') {
            $data['fee_items'] = $this->patientflow_model->get_fee_items();
        } elseif ($group === 'discounts') {
            $data['discounts'] = $this->patientflow_model->get_discount_campaigns();
            $data['categories'] = $this->patientflow_model->get_assistance_categories();
        } elseif ($group === 'patient_accounts') {
            $data['accounts'] = $this->patientflow_model->get_patient_fee_accounts();
        } elseif ($group === 'schedules') {
            $data['schedules'] = $this->patientflow_model->get_patient_fee_schedules();
        } elseif ($group === 'consultation_fees') {
            $data['fees'] = $this->patientflow_model->get_consultation_fees();
        } elseif ($group === 'programme_group') {
            $data['programme_groups_list'] = $this->patientflow_model->get_programme_groups();
        }
        $this->load->view('layout/header', $data);
        $this->load->view('admin/patientflow/setting/setting', $data);
        $this->load->view('layout/footer', $data);
    }

    public function save_fee_item()
    {
        if (!$this->rbac->hasPrivilege('patient_flow', 'can_add')
            && !$this->rbac->hasPrivilege('patient_flow', 'can_edit')) {
            $this->response_json(array('status' => false, 'message' => 'Access denied.'));
            return;
        }
        $id = (int) $this->input->post('id', true);
        $this->form_validation->set_rules('fee_type', 'Fee Type', 'trim|required|in_list[ITEM,MILESTONE,STAGE]');
        $this->form_validation->set_rules('code', 'Fee Code', 'trim|required');
        $this->form_validation->set_rules('name', 'Name', 'trim|required');
        $this->form_validation->set_rules('amount', 'Amount', 'trim|numeric');
        if ($this->form_validation->run() === false) {
            $this->response_json(array('status' => false, 'message' => validation_errors()));
            return;
        }
        $data = array(
            'fee_type'   => $this->input->post('fee_type', true),
            'code'       => $this->input->post('code', true),
            'name'       => $this->input->post('name', true),
            'group_code' => $this->input->post('group_code', true),
            'coverage'   => $this->input->post('coverage', true),
            'amount'     => $this->input->post('amount', true),
            'gst_type'   => $this->input->post('gst_type', true),
            'gst_rate'   => $this->input->post('gst_rate', true),
            'note'       => $this->input->post('note', true),
            'is_active'  => $this->input->post('is_active', true),
        );
        $res = $this->patientflow_model->save_fee_item($data, $id);
        $this->response_json($res);
    }

    public function delete_fee_item()
    {
        if (!$this->rbac->hasPrivilege('patient_flow', 'can_edit')) {
            $this->response_json(array('status' => false, 'message' => 'Access denied.'));
            return;
        }
        $id = (int) $this->input->post('id', true);
        $res = $this->patientflow_model->delete_fee_item($id);
        $this->response_json($res);
    }

    public function toggle_fee_item()
    {
        if (!$this->rbac->hasPrivilege('patient_flow', 'can_edit')) {
            $this->response_json(array('status' => false, 'message' => 'Access denied.'));
            return;
        }
        $id = (int) $this->input->post('id', true);
        $res = $this->patientflow_model->toggle_fee_item($id);
        $this->response_json($res);
    }

    public function get_fee_item()
    {
        if (!$this->rbac->hasPrivilege('patient_flow', 'can_view')) {
            $this->response_json(array('status' => false, 'message' => 'Access denied.'));
            return;
        }
        $id = (int) $this->input->post('id', true);
        $row = $this->patientflow_model->get_fee_item_by_id($id);
        if (!$row) {
            $this->response_json(array('status' => false, 'message' => 'Fee item not found.'));
            return;
        }
        $this->response_json(array(
            'status' => true,
            'data' => array(
                'id'         => $row->id,
                'fee_type'   => $row->fee_type,
                'code'       => $row->code,
                'name'       => $row->name,
                'group_code' => $row->group_code,
                'coverage'   => $row->coverage,
                'amount'     => $row->amount,
                'gst_type'   => $row->gst_type,
                'gst_rate'   => $row->gst_rate,
                'note'       => $row->note,
                'is_active'  => $row->is_active,
            ),
        ));
    }

    public function get_discount_campaign()
    {
        if (!$this->rbac->hasPrivilege('patient_flow', 'can_view')) {
            $this->response_json(array('status' => false, 'message' => 'Access denied.'));
            return;
        }
        $id = (int) $this->input->post('id', true);
        $row = $this->patientflow_model->get_discount_campaign_by_id($id);
        if (!$row) {
            $this->response_json(array('status' => false, 'message' => 'Campaign not found.'));
            return;
        }
        $this->response_json(array(
            'status' => true,
            'data' => array(
                'id'               => $row->id,
                'campaign_code'    => $row->campaign_code,
                'reason'           => $row->reason,
                'discount_percent' => $row->discount_percent,
                'start_date'       => $row->start_date,
                'end_date'         => $row->end_date,
                'benefit_quarters' => $row->benefit_quarters,
                'allow_stacking'   => $row->allow_stacking,
                'payment_mode'     => $row->payment_mode,
                'status'           => $row->status,
            ),
        ));
    }

    public function delete_discount_campaign()
    {
        if (!$this->rbac->hasPrivilege('patient_flow', 'can_delete')) {
            $this->response_json(array('status' => false, 'message' => 'Access denied.'));
            return;
        }
        $id = (int) $this->input->post('id', true);
        $res = $this->patientflow_model->delete_discount_campaign($id);
        $this->response_json($res);
    }

    public function get_assistance_category()
    {
        if (!$this->rbac->hasPrivilege('patient_flow', 'can_view')) {
            $this->response_json(array('status' => false, 'message' => 'Access denied.'));
            return;
        }
        $id = (int) $this->input->post('id', true);
        $row = $this->patientflow_model->get_assistance_category_by_id($id);
        if (!$row) {
            $this->response_json(array('status' => false, 'message' => 'Assistance category not found.'));
            return;
        }
        $this->response_json(array(
            'status' => true,
            'data' => array(
                'id'               => $row->id,
                'group_id'         => $row->group_id,
                'category_code'    => $row->category_code,
                'discount_percent' => $row->discount_percent,
                'validity_quarters'=> $row->validity_quarters,
                'payment_mode'     => $row->payment_mode,
                'is_active'        => $row->is_active,
            ),
        ));
    }

    public function save_assistance_category()
    {
        if (!$this->rbac->hasPrivilege('patient_flow', 'can_add')
            && !$this->rbac->hasPrivilege('patient_flow', 'can_edit')) {
            $this->response_json(array('status' => false, 'message' => 'Access denied.'));
            return;
        }
        $id = (int) $this->input->post('id', true);
        $data = array(
            'group_id'          => $this->input->post('group_id', true),
            'category_code'     => $this->input->post('category_code', true),
            'discount_percent'  => $this->input->post('discount_percent', true),
            'validity_quarters' => $this->input->post('validity_quarters', true),
            'payment_mode'      => $this->input->post('payment_mode', true),
            'is_active'         => $this->input->post('is_active', true),
        );
        $res = $this->patientflow_model->save_assistance_category($id, $data);
        $this->response_json($res);
    }

    public function delete_assistance_category()
    {
        if (!$this->rbac->hasPrivilege('patient_flow', 'can_delete')) {
            $this->response_json(array('status' => false, 'message' => 'Access denied.'));
            return;
        }
        $id = (int) $this->input->post('id', true);
        $res = $this->patientflow_model->delete_assistance_category($id);
        $this->response_json($res);
    }

    public function toggle_assistance_category()
    {
        if (!$this->rbac->hasPrivilege('patient_flow', 'can_edit')) {
            $this->response_json(array('status' => false, 'message' => 'Access denied.'));
            return;
        }
        $id = (int) $this->input->post('id', true);
        $res = $this->patientflow_model->toggle_assistance_category($id);
        $this->response_json($res);
    }

    public function get_patient_fee_schedule()
    {
        if (!$this->rbac->hasPrivilege('patient_flow', 'can_view')) {
            $this->response_json(array('status' => false, 'message' => 'Access denied.'));
            return;
        }
        $id = (int) $this->input->post('id', true);
        $row = $this->patientflow_model->get_patient_fee_schedule_by_id($id);
        if (!$row) {
            $this->response_json(array('status' => false, 'message' => 'Schedule not found.'));
            return;
        }
        $this->response_json(array('status' => true, 'data' => $row));
    }

    public function save_patient_fee_schedule()
    {
        if (!$this->rbac->hasPrivilege('patient_flow', 'can_add') && !$this->rbac->hasPrivilege('patient_flow', 'can_edit')) {
            $this->response_json(array('status' => false, 'message' => 'Access denied.'));
            return;
        }
        $id = (int) $this->input->post('id', true);
        $data = array(
            'account_id'       => $this->input->post('account_id', true),
            'quarter_no'       => $this->input->post('quarter_no', true),
            'stage_from'       => $this->input->post('stage_from', true),
            'stage_to'         => $this->input->post('stage_to', true),
            'label'            => $this->input->post('label', true),
            'standard_amount'  => $this->input->post('standard_amount', true),
            'discount_percent' => $this->input->post('discount_percent', true),
            'payable_amount'   => $this->input->post('payable_amount', true),
            'status'           => $this->input->post('status', true),
        );
        $res = $this->patientflow_model->save_patient_fee_schedule($id, $data);
        $this->response_json($res);
    }

    public function delete_patient_fee_schedule()
    {
        if (!$this->rbac->hasPrivilege('patient_flow', 'can_delete')) {
            $this->response_json(array('status' => false, 'message' => 'Access denied.'));
            return;
        }
        $id = (int) $this->input->post('id', true);
        $res = $this->patientflow_model->delete_patient_fee_schedule($id);
        $this->response_json($res);
    }
    public function get_consultation_fee()
    {
        if (!$this->rbac->hasPrivilege('patient_flow', 'can_view')) {
            $this->response_json(array('status' => false, 'message' => 'Access denied.'));
            return;
        }
        $id = (int) $this->input->post('id', true);
        $row = $this->patientflow_model->get_consultation_fee_by_id($id);
        if (!$row) {
            $this->response_json(array('status' => false, 'message' => 'Consultation fee not found.'));
            return;
        }
        $this->response_json(array('status' => true, 'data' => $row));
    }

    public function save_consultation_fee()
    {
        if (!$this->rbac->hasPrivilege('patient_flow', 'can_add') && !$this->rbac->hasPrivilege('patient_flow', 'can_edit')) {
            $this->response_json(array('status' => false, 'message' => 'Access denied.'));
            return;
        }
        $id = (int) $this->input->post('id', true);
        $data = array(
            'fee_code' => $this->input->post('fee_code', true),
            'item_name' => $this->input->post('item_name', true),
            'category' => $this->input->post('category', true),
            'base_amount' => $this->input->post('base_amount', true),
            'basis' => $this->input->post('basis', true),
            'gst_type' => $this->input->post('gst_type', true),
            'gst_rate' => $this->input->post('gst_rate', true),
            'min_duration_minutes' => $this->input->post('min_duration_minutes', true),
            'extra_time_chargeable' => $this->input->post('extra_time_chargeable', true),
            'requires_enrolled_patient' => $this->input->post('requires_enrolled_patient', true),
            'rule_note' => $this->input->post('rule_note', true),
            'effective_from' => $this->input->post('effective_from', true),
            'effective_to' => $this->input->post('effective_to', true),
            'is_active' => $this->input->post('is_active', true),
        );
        $res = $this->patientflow_model->save_consultation_fee($id, $data);
        $this->response_json($res);
    }

    public function delete_consultation_fee()
    {
        if (!$this->rbac->hasPrivilege('patient_flow', 'can_delete')) {
            $this->response_json(array('status' => false, 'message' => 'Access denied.'));
            return;
        }
        $id = (int) $this->input->post('id', true);
        $res = $this->patientflow_model->delete_consultation_fee($id);
        $this->response_json($res);
    }

    public function get_patient_fee_account()
    {
        if (!$this->rbac->hasPrivilege('patient_flow', 'can_view')) {
            $this->response_json(array('status' => false, 'message' => 'Access denied.'));
            return;
        }
        $id = (int) $this->input->post('id', true);
        $row = $this->patientflow_model->get_patient_fee_account_by_id($id);
        if (!$row) {
            $this->response_json(array('status' => false, 'message' => 'Patient fee account not found.'));
            return;
        }
        $this->response_json(array('status' => true, 'data' => $row));
    }

    public function save_patient_fee_account()
    {
        if (!$this->rbac->hasPrivilege('patient_flow', 'can_add') && !$this->rbac->hasPrivilege('patient_flow', 'can_edit')) {
            $this->response_json(array('status' => false, 'message' => 'Access denied.'));
            return;
        }
        $id = (int) $this->input->post('id', true);
        $data = array(
            'uid' => $this->input->post('uid', true),
            'patient_id' => $this->input->post('patient_id', true),
            'partner_patient_id' => $this->input->post('partner_patient_id', true),
            'programme_code' => $this->input->post('programme_code', true),
            'coverage' => $this->input->post('coverage', true),
            'enrolled_on' => $this->input->post('enrolled_on', true),
            'benefit_discount_id' => $this->input->post('benefit_discount_id', true),
            'benefit_percent' => $this->input->post('benefit_percent', true),
            'benefit_valid_quarters' => $this->input->post('benefit_valid_quarters', true),
            'benefit_status' => $this->input->post('benefit_status', true),
            'benefit_payment_mode' => $this->input->post('benefit_payment_mode', true),
            'benefit_requested_by' => $this->input->post('benefit_requested_by', true),
            'benefit_approved_by' => $this->input->post('benefit_approved_by', true),
            'benefit_approved_at' => $this->input->post('benefit_approved_at', true),
            'stagewise_approved_by' => $this->input->post('stagewise_approved_by', true),
        );
        $res = $this->patientflow_model->save_patient_fee_account($id, $data);
        $this->response_json($res);
    }

    public function delete_patient_fee_account()
    {
        if (!$this->rbac->hasPrivilege('patient_flow', 'can_delete')) {
            $this->response_json(array('status' => false, 'message' => 'Access denied.'));
            return;
        }
        $id = (int) $this->input->post('id', true);
        $res = $this->patientflow_model->delete_patient_fee_account($id);
        $this->response_json($res);
    }



    public function save_constitution()
    {
        if (!$this->rbac->hasPrivilege('patient_flow', 'can_add')
            && !$this->rbac->hasPrivilege('patient_flow', 'can_edit')) {
            access_denied();
        }
        $id = (int) $this->input->post('id', true);
        $this->form_validation->set_rules('gender', 'Gender', 'trim|required|in_list[Male,Female]');
        $this->form_validation->set_rules('body_build_id', 'Body Build', 'trim|required|integer');
        $this->form_validation->set_rules('face_shape_id', 'Face Shape', 'trim|required|integer');
        $this->form_validation->set_rules('nose_type_id', 'Nose Type', 'trim|required|integer');
        if ($this->form_validation->run() === false) {
            $this->session->set_flashdata('error_msg', validation_errors());
            redirect('admin/patientflowsetting/setting?group=constitution_master');
            return;
        }
        $raw_active = $this->input->post('is_active', true);
        $extra = array(
            'description' => trim((string) $this->input->post('description', false)),
            'is_active' => ((string) $raw_active === '0') ? 0 : 1,
        );
        $res = $this->patientflow_model->save_constitution_master(
            $id,
            $this->input->post('gender', true),
            (int) $this->input->post('body_build_id', true),
            (int) $this->input->post('face_shape_id', true),
            (int) $this->input->post('nose_type_id', true),
            $extra
        );
        $this->session->set_flashdata($res['status'] ? 'success_msg' : 'error_msg', $res['message']);
        redirect('admin/patientflowsetting/setting?group=constitution_master');
    }

    public function delete_constitution($id = 0)
    {
        if (!$this->rbac->hasPrivilege('patient_flow', 'can_delete')) {
            access_denied();
        }
        $id = (int) $id;
        if ($id > 0) {
            $this->patientflow_model->delete_constitution_master($id);
            $this->session->set_flashdata('success_msg', 'Constitution deleted successfully.');
        }
        redirect('admin/patientflowsetting/setting?group=constitution_master');
    }

    public function constitution_ajax($id = 0)
    {
        if (!$this->rbac->hasPrivilege('patient_flow', 'can_view')) {
            access_denied();
        }
        header('Content-Type: application/json');
        echo json_encode($this->patientflow_model->get_constitution_master((int) $id));
    }

    public function save_lookup()
    {
        if (!$this->rbac->hasPrivilege('patient_flow', 'can_add')
            && !$this->rbac->hasPrivilege('patient_flow', 'can_edit')) {
            access_denied();
        }
        $type = trim((string) $this->input->post('lookup_type', true));
        $id = (int) $this->input->post('id', true);
        $this->form_validation->set_rules('code', 'Code', 'trim|required|max_length[10]');
        $this->form_validation->set_rules('name', 'Name', 'trim|required|max_length[100]');
        if ($this->form_validation->run() === false) {
            $this->session->set_flashdata('error_msg', validation_errors());
            redirect('admin/patientflowsetting/setting?group=constitution_master');
            return;
        }
        $data = array(
            'code' => strtoupper(trim((string) $this->input->post('code', true))),
            'name' => trim((string) $this->input->post('name', true)),
            'is_active' => ((string) $this->input->post('is_active', true) === '0') ? 0 : 1,
        );
        if ($type === 'body') {
            $data['variant'] = (int) $this->input->post('variant', true);
            if ($data['variant'] < 1) { $data['variant'] = 1; }
        }
        $res = $this->patientflow_model->save_constitution_lookup($type, $id, $data);
        $this->session->set_flashdata($res['status'] ? 'success_msg' : 'error_msg', $res['message']);
        redirect('admin/patientflowsetting/setting?group=constitution_master');
    }

    public function delete_lookup($type = '', $id = 0)
    {
        if (!$this->rbac->hasPrivilege('patient_flow', 'can_delete')) {
            access_denied();
        }
        $res = $this->patientflow_model->delete_constitution_lookup(trim((string) $type), (int) $id);
        $this->session->set_flashdata($res['status'] ? 'success_msg' : 'error_msg', $res['message']);
        redirect('admin/patientflowsetting/setting?group=constitution_master');
    }

    public function lookup_ajax($type = '', $id = 0)
    {
        if (!$this->rbac->hasPrivilege('patient_flow', 'can_view')) {
            access_denied();
        }
        header('Content-Type: application/json');
        echo json_encode($this->patientflow_model->get_constitution_lookup(trim((string) $type), (int) $id));
    }

    public function save_programme()
    {
        if (!$this->rbac->hasPrivilege('patient_flow', 'can_add')
            && !$this->rbac->hasPrivilege('patient_flow', 'can_edit')) {
            access_denied();
        }
        $id = (int) $this->input->post('id', true);
        $this->form_validation->set_rules('code', 'Code', 'trim|required|max_length[10]');
        $this->form_validation->set_rules('name', 'Name', 'trim|required|max_length[150]');
        if ($this->form_validation->run() === false) {
            $this->session->set_flashdata('error_msg', validation_errors());
            redirect('admin/patientflowsetting/setting?group=programme_master');
            return;
        }
        $res = $this->patientflow_model->save_programme($id, array(
            'code' => strtoupper(trim((string) $this->input->post('code', true))),
            'name' => trim((string) $this->input->post('name', true)),
            'is_active' => ((string) $this->input->post('is_active', true) === '0') ? 0 : 1,
        ));
        $this->session->set_flashdata($res['status'] ? 'success_msg' : 'error_msg', $res['message']);
        redirect('admin/patientflowsetting/setting?group=programme_master');
    }

    public function delete_programme($id = 0)
    {
        if (!$this->rbac->hasPrivilege('patient_flow', 'can_delete')) {
            access_denied();
        }
        $res = $this->patientflow_model->delete_programme((int) $id);
        $this->session->set_flashdata($res['status'] ? 'success_msg' : 'error_msg', $res['message']);
        redirect('admin/patientflowsetting/setting?group=programme_master');
    }

    public function save_subcategory()
    {
        if (!$this->rbac->hasPrivilege('patient_flow', 'can_add')
            && !$this->rbac->hasPrivilege('patient_flow', 'can_edit')) {
            access_denied();
        }
        $id = (int) $this->input->post('id', true);
        $this->form_validation->set_rules('programme_id', 'Programme', 'trim|required|integer');
        $this->form_validation->set_rules('code', 'Code', 'trim|required|max_length[15]');
        $this->form_validation->set_rules('name', 'Name', 'trim|required|max_length[150]');
        if ($this->form_validation->run() === false) {
            $this->session->set_flashdata('error_msg', validation_errors());
            redirect('admin/patientflowsetting/setting?group=programme_master');
            return;
        }
        $res = $this->patientflow_model->save_subcategory($id, array(
            'programme_id' => (int) $this->input->post('programme_id', true),
            'code' => strtoupper(trim((string) $this->input->post('code', true))),
            'name' => trim((string) $this->input->post('name', true)),
            'is_active' => ((string) $this->input->post('is_active', true) === '0') ? 0 : 1,
        ));
        $this->session->set_flashdata($res['status'] ? 'success_msg' : 'error_msg', $res['message']);
        redirect('admin/patientflowsetting/setting?group=programme_master');
    }

    public function delete_subcategory($id = 0)
    {
        if (!$this->rbac->hasPrivilege('patient_flow', 'can_delete')) {
            access_denied();
        }
        $res = $this->patientflow_model->delete_subcategory((int) $id);
        $this->session->set_flashdata($res['status'] ? 'success_msg' : 'error_msg', $res['message']);
        redirect('admin/patientflowsetting/setting?group=programme_master');
    }

    public function save_stage()
    {
        if (!$this->rbac->hasPrivilege('patient_flow', 'can_add')
            && !$this->rbac->hasPrivilege('patient_flow', 'can_edit')) {
            access_denied();
        }
        $id = (int) $this->input->post('id', true);
        $this->form_validation->set_rules('quarter', 'Quarter', 'trim|required');
        $this->form_validation->set_rules('stage', 'Stage', 'trim|required');
        $this->form_validation->set_rules('duration_days', 'Duration', 'trim|required|integer');
        if ($this->form_validation->run() === false) {
            $this->session->set_flashdata('error_msg', validation_errors());
            redirect('admin/patientflowsetting/setting?group=stage_master');
            return;
        }
        $res = $this->patientflow_model->save_stage(
            $id,
            $this->input->post('quarter', true),
            $this->input->post('stage', true),
            (int) $this->input->post('duration_days', true),
            $this->input->post('operational_rule', false),
            ((string) $this->input->post('is_active', true) === '0') ? 0 : 1
        );
        $this->session->set_flashdata($res['status'] ? 'success_msg' : 'error_msg', $res['message']);
        redirect('admin/patientflowsetting/setting?group=stage_master');
    }

    public function delete_stage($id = 0)
    {
        if (!$this->rbac->hasPrivilege('patient_flow', 'can_delete')) {
            access_denied();
        }
        $res = $this->patientflow_model->delete_stage((int) $id);
        $this->session->set_flashdata($res['status'] ? 'success_msg' : 'error_msg', $res['message']);
        redirect('admin/patientflowsetting/setting?group=stage_master');
    }

    public function save_medicine()
    {
        if (!$this->rbac->hasPrivilege('patient_flow', 'can_add')
            && !$this->rbac->hasPrivilege('patient_flow', 'can_edit')) {
            access_denied();
        }
        $id = (int) $this->input->post('id', true);
        $this->form_validation->set_rules('code', 'Code', 'trim|required|max_length[10]');
        $this->form_validation->set_rules('medicine_identity', 'Identity', 'trim|required|max_length[200]');
        $this->form_validation->set_rules('series', 'Series', 'trim|required');
        if ($this->form_validation->run() === false) {
            $this->session->set_flashdata('error_msg', validation_errors());
            redirect('admin/patientflowsetting/setting?group=medicine_master');
            return;
        }
        $fields = array('code', 'medicine_identity', 'series', 'gp_code', 'indication', 'purpose', 'patient_dose', 'route_method', 'timing', 'frequency', 'duration_text', 'dispensing_qty_ml', 'container_vial', 'stock_deduction_ml', 'use_note', 'control_note', 'supply_status', 'status');
        $data = array();
        foreach ($fields as $fl) {
            $data[$fl] = trim((string) $this->input->post($fl, false));
        }
        if ($data['dispensing_qty_ml'] === '') { $data['dispensing_qty_ml'] = null; }
        if ($data['stock_deduction_ml'] === '') { $data['stock_deduction_ml'] = null; }
        $data['is_active'] = ($data['status'] === 'Inactive') ? 0 : 1;
        $res = $this->patientflow_model->save_medicine($id, $data);
        $this->session->set_flashdata($res['status'] ? 'success_msg' : 'error_msg', $res['message']);
        redirect('admin/patientflowsetting/setting?group=medicine_master');
    }

    public function delete_medicine($id = 0)
    {
        if (!$this->rbac->hasPrivilege('patient_flow', 'can_delete')) {
            access_denied();
        }
        $res = $this->patientflow_model->delete_medicine((int) $id);
        $this->session->set_flashdata($res['status'] ? 'success_msg' : 'error_msg', $res['message']);
        redirect('admin/patientflowsetting/setting?group=medicine_master');
    }

    public function save_f_mapping()
    {
        if (!$this->rbac->hasPrivilege('patient_flow', 'can_add')
            && !$this->rbac->hasPrivilege('patient_flow', 'can_edit')) {
            access_denied();
        }
        $this->form_validation->set_rules('series_id', 'Series', 'trim|required|integer');
        $this->form_validation->set_rules('stage_id', 'Stage', 'trim|required|integer');
        $this->form_validation->set_rules('medicine_id', 'Medicine', 'trim|required|integer');
        if ($this->form_validation->run() === false) {
            $this->session->set_flashdata('error_msg', validation_errors());
            redirect('admin/patientflowsetting/setting?group=f_series_mapping');
            return;
        }
        $res = $this->patientflow_model->save_f_mapping(
            (int) $this->input->post('series_id', true),
            (int) $this->input->post('stage_id', true),
            (int) $this->input->post('medicine_id', true)
        );
        $this->session->set_flashdata($res['status'] ? 'success_msg' : 'error_msg', $res['message']);
        redirect('admin/patientflowsetting/setting?group=f_series_mapping');
    }

    public function delete_f_mapping($id = 0)
    {
        if (!$this->rbac->hasPrivilege('patient_flow', 'can_delete')) {
            access_denied();
        }
        $this->db->where('id', (int) $id)->delete('constit_f_series_mapping');
        $this->session->set_flashdata('success_msg', 'F-series mapping deleted.');
        redirect('admin/patientflowsetting/setting?group=f_series_mapping');
    }

    /** ---------- Early Mapping CRUD ---------- */

    public function save_early_mapping()
    {
        if (!$this->rbac->hasPrivilege('patient_flow', 'can_add')
            && !$this->rbac->hasPrivilege('patient_flow', 'can_edit')) {
            access_denied();
        }
        $this->form_validation->set_rules('id', 'ID', 'trim|integer');
        $this->form_validation->set_rules('pair_group', 'Pair Group', 'trim|required|max_length[20]');
        $this->form_validation->set_rules('stage_id', 'Stage', 'trim|required|integer');
        $this->form_validation->set_rules('medicine_id', 'Medicine', 'trim|required|integer');
        if ($this->form_validation->run() === false) {
            $this->session->set_flashdata('error_msg', validation_errors());
            redirect('admin/patientflowsetting/setting?group=daily_mapping');
            return;
        }
        $res = $this->patientflow_model->save_early_mapping(
            (int) $this->input->post('id', true),
            $this->input->post('pair_group', true),
            (int) $this->input->post('stage_id', true),
            (int) $this->input->post('medicine_id', true)
        );
        $this->session->set_flashdata($res['status'] ? 'success_msg' : 'error_msg', $res['message']);
        redirect('admin/patientflowsetting/setting?group=daily_mapping');
    }

    public function delete_early_mapping($id = 0)
    {
        if (!$this->rbac->hasPrivilege('patient_flow', 'can_delete')) {
            access_denied();
        }
        $res = $this->patientflow_model->delete_early_mapping((int) $id);
        $this->session->set_flashdata($res['status'] ? 'success_msg' : 'error_msg', $res['message']);
        redirect('admin/patientflowsetting/setting?group=daily_mapping');
    }

    /** ---------- Universal Mapping CRUD ---------- */

    public function save_universal_mapping()
    {
        if (!$this->rbac->hasPrivilege('patient_flow', 'can_add')
            && !$this->rbac->hasPrivilege('patient_flow', 'can_edit')) {
            access_denied();
        }
        $this->form_validation->set_rules('id', 'ID', 'trim|integer');
        $this->form_validation->set_rules('stage_id', 'Stage', 'trim|required|integer');
        $this->form_validation->set_rules('session', 'Session', 'trim|required|max_length[20]');
        $this->form_validation->set_rules('medicine_id', 'Medicine', 'trim|required|integer');
        if ($this->form_validation->run() === false) {
            $this->session->set_flashdata('error_msg', validation_errors());
            redirect('admin/patientflowsetting/setting?group=daily_mapping');
            return;
        }
        $res = $this->patientflow_model->save_universal_mapping(
            (int) $this->input->post('id', true),
            (int) $this->input->post('stage_id', true),
            $this->input->post('session', true),
            (int) $this->input->post('medicine_id', true)
        );
        $this->session->set_flashdata($res['status'] ? 'success_msg' : 'error_msg', $res['message']);
        redirect('admin/patientflowsetting/setting?group=daily_mapping');
    }

    public function delete_universal_mapping($id = 0)
    {
        if (!$this->rbac->hasPrivilege('patient_flow', 'can_delete')) {
            access_denied();
        }
        $res = $this->patientflow_model->delete_universal_mapping((int) $id);
        $this->session->set_flashdata($res['status'] ? 'success_msg' : 'error_msg', $res['message']);
        redirect('admin/patientflowsetting/setting?group=daily_mapping');
    }

    /** ---------- Broad Area Rules CRUD ---------- */

    public function save_broad_rule()
    {
        if (!$this->rbac->hasPrivilege('patient_flow', 'can_add')
            && !$this->rbac->hasPrivilege('patient_flow', 'can_edit')) {
            access_denied();
        }
        $this->form_validation->set_rules('id', 'ID', 'trim|integer');
        $this->form_validation->set_rules('applicability', 'Applicability', 'trim|required|in_list[Male/general,Female,Female only,Any]');
        if ($this->form_validation->run() === false) {
            $this->session->set_flashdata('error_msg', validation_errors());
            redirect('admin/patientflowsetting/setting?group=broad_area_rules');
            return;
        }
        $concern_pattern = $this->input->post('concern_pattern', true);
        if (is_array($concern_pattern)) {
            if (empty($concern_pattern)) {
                $this->session->set_flashdata('error_msg', 'Concern Pattern field is required.');
                redirect('admin/patientflowsetting/setting?group=broad_area_rules');
                return;
            }
            $concern_pattern = implode(', ', $concern_pattern);
        } else {
            $concern_pattern = trim((string) $concern_pattern);
            if ($concern_pattern === '') {
                $this->session->set_flashdata('error_msg', 'Concern Pattern field is required.');
                redirect('admin/patientflowsetting/setting?group=broad_area_rules');
                return;
            }
        }
        $results = $this->input->post('results', true);
        if (!is_array($results)) {
            $results = array();
        }
        $res = $this->patientflow_model->save_broad_rule(
            (int) $this->input->post('id', true),
            $concern_pattern,
            $this->input->post('applicability', true),
            $results,
            $this->input->post('is_active', true)
        );
        $this->session->set_flashdata($res['status'] ? 'success_msg' : 'error_msg', $res['message']);
        redirect('admin/patientflowsetting/setting?group=broad_area_rules');
    }

    public function delete_broad_rule($id = 0)
    {
        if (!$this->rbac->hasPrivilege('patient_flow', 'can_delete')) {
            access_denied();
        }
        $res = $this->patientflow_model->delete_broad_rule((int) $id);
        $this->session->set_flashdata($res['status'] ? 'success_msg' : 'error_msg', $res['message']);
        redirect('admin/patientflowsetting/setting?group=broad_area_rules');
    }

    // ============ Step 1: Programme Groups ============

    /**
     * Save programme group.
     */
    public function save_programme_group()
    {
        if (!$this->rbac->hasPrivilege('patient_flow', 'can_add')
            && !$this->rbac->hasPrivilege('patient_flow', 'can_edit')) {
            access_denied();
        }
        $id = (int) $this->input->post('id', true);
        $this->form_validation->set_rules('code', 'Code', 'trim|required|max_length[20]');
        $this->form_validation->set_rules('name', 'Name', 'trim|required|max_length[100]');
        $this->form_validation->set_rules('coverage_model', 'Coverage Model', 'trim|required|in_list[PARTNER_BASED,PER_PATIENT]');
        if ($this->form_validation->run() === false) {
            $this->session->set_flashdata('error_msg', validation_errors());
            redirect('admin/patientflowsetting/setting?group=programme_group');
            return;
        }
        $res = $this->patientflow_model->save_programme_group($id, array(
            'code' => $this->input->post('code', true),
            'name' => $this->input->post('name', true),
            'coverage_model' => $this->input->post('coverage_model', true),
            'is_active' => $this->input->post('is_active', true),
        ));
        $this->session->set_flashdata($res['status'] ? 'success_msg' : 'error_msg', $res['message']);
        redirect('admin/patientflowsetting/setting?group=programme_group');
    }

    /**
     * Delete programme group.
     */
    public function delete_programme_group($id = 0)
    {
        if (!$this->rbac->hasPrivilege('patient_flow', 'can_delete')) {
            access_denied();
        }
        $res = $this->patientflow_model->delete_programme_group((int) $id);
        $this->session->set_flashdata($res['status'] ? 'success_msg' : 'error_msg', $res['message']);
        redirect('admin/patientflowsetting/setting?group=programme_group');
    }

    // ============ Step 4: Approval Workflows ============

    /**
     * Request assistance: any junior creates a PENDING assistance request.
     */
    public function save_assistance_request()
    {
        if (!$this->rbac->hasPrivilege('patient_flow', 'can_add')) {
            $this->response_json(array('status' => false, 'message' => 'Access denied.'));
            return;
        }
        $enrollment_id = (int) $this->input->post('enrollment_id', true);
        $category_id   = (int) $this->input->post('category_id', true);
        $reason        = trim((string) $this->input->post('reason', true));
        $res = $this->patientflow_model->save_assistance_request($enrollment_id, $category_id, $this->customlib->getStaffID(), $reason);
        $this->response_json($res);
    }

    /**
     * Approve/reject assistance request: one active fee_director, not the requester.
     */
    public function approve_assistance_request($request_id = 0)
    {
        if (!$this->rbac->hasPrivilege('patient_flow', 'can_edit')) {
            $this->response_json(array('status' => false, 'message' => 'Access denied.'));
            return;
        }
        $request_id    = (int) $request_id;
        $approve       = (string) $this->input->post('approve', true) === '1';
        $decision_note = trim((string) $this->input->post('decision_note', true));
        $res = $this->patientflow_model->approve_assistance_request($request_id, $this->customlib->getStaffID(), $approve, $decision_note);
        $this->response_json($res);
    }

    /**
     * Save discount campaign: Admin or Director creates DRAFT campaign.
     */
    public function save_discount_campaign()
    {
        if (!$this->rbac->hasPrivilege('patient_flow', 'can_add')
            && !$this->rbac->hasPrivilege('patient_flow', 'can_edit')) {
            $this->response_json(array('status' => false, 'message' => 'Access denied.'));
            return;
        }
        $id = (int) $this->input->post('id', true);
        $data = array(
            'campaign_code'  => $this->input->post('campaign_code', true),
            'reason'         => $this->input->post('reason', true),
            'discount_percent' => $this->input->post('discount_percent', true),
            'start_date'     => $this->input->post('start_date', true),
            'end_date'       => $this->input->post('end_date', true),
            'benefit_quarters' => $this->input->post('benefit_quarters', true),
            'allow_stacking' => $this->input->post('allow_stacking', true),
            'payment_mode'   => $this->input->post('payment_mode', true),
        );
        $res = $this->patientflow_model->save_discount_campaign($id, $data, $this->customlib->getStaffID());
        $this->response_json($res);
    }

    /**
     * Approve/reject campaign: one active Director.
     */
    public function approve_campaign($campaign_id = 0)
    {
        if (!$this->rbac->hasPrivilege('patient_flow', 'can_edit')) {
            $this->response_json(array('status' => false, 'message' => 'Access denied.'));
            return;
        }
        $campaign_id = (int) $campaign_id;
        $approve     = (string) $this->input->post('approve', true) === '1';
        $res = $this->patientflow_model->approve_campaign($campaign_id, $this->customlib->getStaffID(), $approve);
        $this->response_json($res);
    }

    /**
     * Set stage-wise approval for a campaign patient: Director sets stagewise_approved_by on benefit.
     */
    public function set_stagewise_approval()
    {
        if (!$this->rbac->hasPrivilege('patient_flow', 'can_edit')) {
            $this->response_json(array('status' => false, 'message' => 'Access denied.'));
            return;
        }
        $enrollment_id = (int) $this->input->post('enrollment_id', true);
        $approve       = (string) $this->input->post('approve', true) === '1';
        $res = $this->patientflow_model->set_stagewise_approval($enrollment_id, $this->customlib->getStaffID(), $approve);
        $this->response_json($res);
    }

    /**
     * Mark payment schedule row as PAID: Reception collects payment.
     */
    public function mark_payment_paid($schedule_id = 0)
    {
        if (!$this->rbac->hasPrivilege('patient_flow', 'can_edit')) {
            $this->response_json(array('status' => false, 'message' => 'Access denied.'));
            return;
        }
        $schedule_id   = (int) $schedule_id;
        $payment_ref   = trim((string) $this->input->post('payment_ref', true));
        $res = $this->patientflow_model->mark_payment_paid($schedule_id, $this->customlib->getStaffID(), $payment_ref);
        $this->response_json($res);
    }

    // ============ Step 5: Enrollment & Schedule Generation ============

    /**
     * Create enrollment, resolve benefit, and generate payment schedule.
     */
    public function save_enrollment()
    {
        if (!$this->rbac->hasPrivilege('patient_flow', 'can_add')) {
            $this->response_json(array('status' => false, 'message' => 'Access denied.'));
            return;
        }
        $patient_id     = (int) $this->input->post('patient_id', true);
        $programme_id   = (int) $this->input->post('programme_id', true);
        $coverage       = trim((string) $this->input->post('coverage', true));
        $enrolled_on    = trim((string) $this->input->post('enrolled_on', true));
        $partner_id     = (int) $this->input->post('partner_patient_id', true);
        $staff_id       = $this->customlib->getStaffID();

        // 1. Create enrollment
        $enr = $this->patientflow_model->save_enrollment($patient_id, $programme_id, $coverage, $enrolled_on, $staff_id, $partner_id);
        if (!$enr['status']) {
            $this->response_json($enr);
            return;
        }
        $enrollment_id = $enr['id'];

        // 2. Resolve benefit using Fee_engine
        $prog = $this->patientflow_model->get_programme_by_id($programme_id);
        if (!$prog) {
            $this->response_json(array('status' => false, 'message' => 'Programme not found.'));
            return;
        }
        $group = $this->db->where('id', $prog->group_id)->get('patient_flow_programme_group')->row();
        $group_id = $group ? (int) $group->id : 0;
        $benefit = $this->fee_engine->resolve_benefit($enrollment_id, $group_id, $programme_id, $enrolled_on, 1);

        // 3. Save benefit snapshot
        $benefit_res = $this->patientflow_model->save_enrollment_benefit(
            $enrollment_id,
            $benefit['source'],
            $benefit['source_id'],
            $benefit['discount_percent'],
            $benefit['payment_mode'],
            $benefit['valid_from_quarter'],
            $benefit['valid_to_quarter'],
            $benefit['candidates_json'],
            $staff_id
        );
        if (!$benefit_res['status']) {
            $this->response_json($benefit_res);
            return;
        }

        // 4. Generate payment schedule
        $sched_res = $this->patientflow_model->generate_payment_schedule_for_enrollment($enrollment_id, 1);
        if (!$sched_res['status']) {
            $this->response_json($sched_res);
            return;
        }

        $this->response_json(array(
            'status'  => true,
            'message' => 'Enrollment created with benefit and schedule.',
            'enrollment_id' => $enrollment_id,
            'uid'     => $enr['uid'],
            'benefit' => $benefit,
            'schedule_rows' => $sched_res['rows'],
        ));
    }

    /**
     * Get enrollment details as JSON.
     */
    public function get_enrollment($enrollment_id = 0)
    {
        if (!$this->rbac->hasPrivilege('patient_flow', 'can_view')) {
            $this->response_json(array('status' => false, 'message' => 'Access denied.'));
            return;
        }
        $enr = $this->patientflow_model->get_enrollment((int) $enrollment_id);
        if (!$enr) {
            $this->response_json(array('status' => false, 'message' => 'Enrollment not found.'));
            return;
        }
        $benefit = $this->db->where('enrollment_id', $enrollment_id)
            ->where('is_current', 1)
            ->get('patient_flow_enrollment_benefit')->row();
        $schedules = $this->db->where('enrollment_id', $enrollment_id)
            ->order_by('quarter_no', 'asc')->order_by('seq_no', 'asc')
            ->get('patient_flow_programme_payment_schedule')->result();
        $this->response_json(array(
            'status'   => true,
            'enrollment' => $enr,
            'benefit'  => $benefit ? (array) $benefit : null,
            'schedules' => $schedules,
        ));
    }

    /**
     * List all enrollments as JSON.
     */
    public function get_enrollments()
    {
        if (!$this->rbac->hasPrivilege('patient_flow', 'can_view')) {
            $this->response_json(array('status' => false, 'message' => 'Access denied.'));
            return;
        }
        $status = trim((string) $this->input->get('status', true));
        $enrollments = $this->patientflow_model->get_enrollments($status ? $status : null);
        $this->response_json(array(
            'status'      => true,
            'enrollments' => $enrollments,
        ));
    }

    // ============ Step 6: Billing Integration ============

    /**
     * Generate a consultation invoice for a patient visit.
     * Consultation is always a separate invoice, GST exempt, not discounted.
     */
    public function generate_consultation_invoice()
    {
        if (!$this->rbac->hasPrivilege('patient_flow', 'can_add')) {
            $this->response_json(array('status' => false, 'message' => 'Access denied.'));
            return;
        }
        $patient_id = (int) $this->input->post('patient_id', true);
        $visit_id   = (int) $this->input->post('visit_id', true);
        $fee_code   = trim((string) $this->input->post('fee_code', true));
        $res = $this->patientflow_model->generate_consultation_invoice(
            $patient_id, $visit_id, $this->customlib->getStaffID(), $fee_code
        );
        $this->response_json($res);
    }

    /**
     * Add a vial line item to an invoice based on medicine's container_vial.
     */
    public function add_vial_line()
    {
        if (!$this->rbac->hasPrivilege('patient_flow', 'can_add')) {
            $this->response_json(array('status' => false, 'message' => 'Access denied.'));
            return;
        }
        $invoice_id   = (int) $this->input->post('invoice_id', true);
        $medicine_id  = (int) $this->input->post('medicine_id', true);
        $is_free      = (string) $this->input->post('is_free_first_stage_e', true) === '1';
        $res = $this->patientflow_model->add_vial_line_to_invoice(
            $invoice_id, $medicine_id, $this->customlib->getStaffID(), $is_free
        );
        $this->response_json($res);
    }

    /**
     * Add duplicate booklet item line to invoice.
     */
    public function add_booklet_line()
    {
        if (!$this->rbac->hasPrivilege('patient_flow', 'can_add')) {
            $this->response_json(array('status' => false, 'message' => 'Access denied.'));
            return;
        }
        $invoice_id = (int) $this->input->post('invoice_id', true);
        $res = $this->patientflow_model->add_booklet_line_to_invoice(
            $invoice_id, $this->customlib->getStaffID()
        );
        $this->response_json($res);
    }

    /**
     * Get invoice details with line items.
     */
    public function get_invoice($invoice_id = 0)
    {
        if (!$this->rbac->hasPrivilege('patient_flow', 'can_view')) {
            $this->response_json(array('status' => false, 'message' => 'Access denied.'));
            return;
        }
        $invoice = $this->db->where('id', (int) $invoice_id)->get('patient_flow_fee_invoice')->row();
        if (!$invoice) {
            $this->response_json(array('status' => false, 'message' => 'Invoice not found.'));
            return;
        }
        $lines = $this->db->where('invoice_id', $invoice_id)
            ->get('patient_flow_fee_invoice_line')->result();
        $this->response_json(array(
            'status' => true,
            'invoice' => $invoice,
            'lines'  => $lines,
        ));
    }

    // ============ Step 7: Dispensing Gate ============

    /**
     * Check if dispensing is allowed for a medicine on a visit.
     */
    public function check_dispensing_gate($visit_id = 0, $medicine_id = 0)
    {
        if (!$this->rbac->hasPrivilege('patient_flow', 'can_view')) {
            $this->response_json(array('status' => false, 'message' => 'Access denied.'));
            return;
        }
        $visit_id   = (int) $visit_id;
        $medicine_id = (int) $medicine_id;
        $res = $this->patientflow_model->check_dispensing_gate($visit_id, $medicine_id);
        $this->response_json($res);
    }

    /**
     * Check clinical approval status for a visit's prescriptions.
     */
    public function check_clinical_approval($visit_id = 0)
    {
        if (!$this->rbac->hasPrivilege('patient_flow', 'can_view')) {
            $this->response_json(array('status' => false, 'message' => 'Access denied.'));
            return;
        }
        $visit_id = (int) $visit_id;
        $res = $this->patientflow_model->check_clinical_approval($visit_id);
        $this->response_json($res);
    }

    /**
     * Approve clinical review of a prescription (senior staff only).
     */
    public function approve_clinical_prescription($prescription_id = 0)
    {
        if (!$this->rbac->hasPrivilege('patient_flow', 'can_edit')) {
            $this->response_json(array('status' => false, 'message' => 'Access denied.'));
            return;
        }
        $prescription_id = (int) $prescription_id;
        $approve         = (string) $this->input->post('approve', true) === '1';
        $res = $this->patientflow_model->approve_clinical($prescription_id, $this->customlib->getStaffID(), $approve);
        $this->response_json($res);
    }

    // ============ Step 8: Reports ============

    /**
     * Audit trail report — from fee_audit_log.
     */
    public function get_audit_trail()
    {
        if (!$this->rbac->hasPrivilege('patient_flow', 'can_view')) {
            $this->response_json(array('status' => false, 'message' => 'Access denied.'));
            return;
        }
        $limit   = (int) $this->input->get('limit', true) ?: 100;
        $offset  = (int) $this->input->get('offset', true) ?: 0;
        $entity  = trim((string) $this->input->get('entity', true));
        $action  = trim((string) $this->input->get('action', true));
        $res = $this->patientflow_model->get_audit_trail($limit, $offset, $entity, $action);
        $this->response_json(array('status' => true, 'audit_trail' => $res));
    }

    /**
     * Discount utilisation report — by category and campaign.
     */
    public function get_discount_utilisation()
    {
        if (!$this->rbac->hasPrivilege('patient_flow', 'can_view')) {
            $this->response_json(array('status' => false, 'message' => 'Access denied.'));
            return;
        }
        $res = $this->patientflow_model->get_discount_utilisation();
        $this->response_json(array('status' => true, 'discount_utilisation' => $res));
    }

    /**
     * GST summary report — GST collected by type and rate.
     */
    public function get_gst_summary()
    {
        if (!$this->rbac->hasPrivilege('patient_flow', 'can_view')) {
            $this->response_json(array('status' => false, 'message' => 'Access denied.'));
            return;
        }
        $res = $this->patientflow_model->get_gst_summary();
        $this->response_json(array('status' => true, 'gst_summary' => $res));
    }

    /**
     * Pending-payment list — schedule rows with status = PENDING.
     */
    public function get_pending_payments()
    {
        if (!$this->rbac->hasPrivilege('patient_flow', 'can_view')) {
            $this->response_json(array('status' => false, 'message' => 'Access denied.'));
            return;
        }
        $limit  = (int) $this->input->get('limit', true) ?: 100;
        $offset = (int) $this->input->get('offset', true) ?: 0;
        $res = $this->patientflow_model->get_pending_payments($limit, $offset);
        $this->response_json(array('status' => true, 'pending_payments' => $res));
    }

    /**
     * Send a JSON response and terminate the request.
     *
     * Usage:
     *   $this->response_json(array('status' => true, 'pending_payments' => $res));
     *   $this->response_json(array('status' => false, 'message' => 'Not found'), 404);
     *
     * @param  array $data       Payload to encode as JSON.
     * @param  int   $http_code  HTTP status code (default 200).
     * @return void
     */
    public function response_json($data = array(), $http_code = 200)
    {
        // Ensure nothing has already been output (prevents broken JSON).
        if (ob_get_length()) {
            ob_clean();
        }

        // Set headers only if not already sent.
        if (!headers_sent()) {
            header('Content-Type: application/json; charset=utf-8');
            header('X-Content-Type-Options: nosniff');
            http_response_code((int) $http_code);
        }

        // Encode safely.
        $json = json_encode(
            $data,
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PARTIAL_OUTPUT_ON_ERROR
        );

        // json_encode can still fail on invalid UTF-8.
        if ($json === false) {
            if (!headers_sent()) {
                http_response_code(500);
            }
            $json = json_encode(array(
                'status'  => false,
                'message' => 'Response encoding error: ' . json_last_error_msg(),
            ));
        }

        echo $json;
        exit;
    }
    
    
}