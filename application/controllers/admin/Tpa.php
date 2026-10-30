<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Tpa extends Admin_Controller
{
	public $charge_type;


    public function __construct()
    {
        parent::__construct();
        $this->config->load("payroll");
        $this->load->library("datatables");
        $this->charge_type = $this->customlib->getChargeMaster();
    }

    public function master($id)
    {
        if (!$this->rbac->hasPrivilege('tpa_charges', 'can_view')) {
            access_denied();
        }
        // Highlight only the TPA Management top menu; clear any stale
        // report sub-menu so Reports > TPA does not stay selected.
        $this->session->set_userdata('top_menu', 'tpa_management');
        $this->session->unset_userdata('sub_menu');
        $this->session->unset_userdata('subsub_menu');
        $data["charge_type"] = $this->setting_model->getChargeMaster();;
        $data['result'] = $this->organisation_model->get($id);
        $data['title']  = $this->lang->line('tpa_master');
        $data['module'] = 'tpa_management';
        $this->load->view('layout/header', $data);
        $this->load->view('admin/tpamanagement/tpamasters', $data);
        $this->load->view('layout/footer', $data);
    }

    public function checkvalidation()
    {
        $param = array(
            'charge_type'           => $this->input->post('charge_type', TRUE),
            'charge_type_master_id' => $this->input->post('charge_type_master_id', TRUE),
        );

        $json_array = array('status' => 'success', 'error' => '', 'param' => $param, 'message' => $this->lang->line('success_message'));
        echo json_encode($json_array);
    }

    public function tpadetails()
    {
        $charge_type_master_id = $this->input->post('charge_type_master_id', TRUE);
        $charge_type_id        = $this->input->post('charge_type', TRUE);
        $reportdata            = $this->tpa_model->org_chargedatatable($charge_type_master_id, $charge_type_id);

        $reportdata = json_decode($reportdata);

        $dt_data = array();
        if (!empty($reportdata->data)) {
            foreach ($reportdata->data as $key => $value) {

                $action = "<div class='d-flex gap-1 justify-content-end'>";
                if ($this->rbac->hasPrivilege('tpa_charges', 'can_edit')) {
                    $action .= '<a class="btn btn-sm btn-light" data-bs-toggle="tooltip" title="' . $this->lang->line('edit') . '" onclick="get_org_charge(' . $value->id . ')"><i class="fa fa-pencil"></i></a>';
                }
                if ($this->rbac->hasPrivilege('tpa_charges', 'can_delete')) {
                    $action .= '<a class="btn btn-sm btn-light" data-bs-toggle="tooltip" title="' . $this->lang->line('delete') . '" onclick="delete_orgById(' . $value->id . ')"><i class="fa fa-trash"></i></a>';
                }
                $action .= "</div>";
                $row       = array();
                $row[]     = html_escape($value->charge_type);
                $row[]     = html_escape($value->charge_category);
                $row[]     = html_escape($value->charge_name);
                $row[]     = html_escape($value->description);
                $row[]     = html_escape($value->standard_charge);
                $row[]     = html_escape($value->org_charge);
                $row[]     = $action;
                $dt_data[] = $row;
            }
        }

        $json_data = array(
            "draw"            => intval($reportdata->draw),
            "recordsTotal"    => intval($reportdata->recordsTotal),
            "recordsFiltered" => intval($reportdata->recordsFiltered),
            "data"            => $dt_data,
        );
        echo json_encode($json_data);
    }

    public function add($id)
    {
        if (!$this->rbac->hasPrivilege('charges', 'can_add')) {
            access_denied();
        }
        $check_value = 0;

        $Charge_type  = $this->input->post('charge_type', TRUE);
        $other_charge = $this->input->post('other_charge', TRUE);
        if (isset($other_charge)) {
            foreach ($other_charge as $key => $value) {
                $charge_id   = (int)$value;
                $check_value = 1;
                if (empty($this->input->post('org_othcharge_' . $charge_id, TRUE))) {
                    $msg['e' . $charge_id] = "The Organisation Charge Field  " . $charge_id . " Required";
                    $array                 = array('status' => 'fail', 'error' => $msg, 'message' => '');
                } else {
                    $charge        = $charge_id;
                    $org_othcharge = $this->input->post('org_othcharge_' . $charge_id, TRUE);
                    $data          = array('org_id' => $id, 'charge_type' => $Charge_type, 'charge_id' => $charge, 'org_charge' => $org_othcharge);
                    $data_array[]  = $data;
                    $array         = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('successfully_inserted'));
                }
            }
        }

        if ($check_value == "0") {
            $msg['eerror'] = $this->lang->line('the_charges_field_required');
            $array         = array('status' => 'fail', 'error' => $msg, 'message' => '');
        }

        if ($array['status'] == "success") {
            $this->tpa_model->add($data_array);
        }

        echo json_encode($array);
    }

    public function get_org_charge($id)
    {
        $res = $this->tpa_model->get_org_charge($id);
        echo json_encode($res);
    }

    public function edit_org()
    {
        $this->form_validation->set_rules('org_charge', $this->lang->line('tpa_charge'), 'required|xss_clean|valid_amount|greater_than[0]');
        if ($this->form_validation->run() == false) {

            $msg = array(
                'charge' => form_error('org_charge'),
            );

            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $id                   = $this->input->post('org_charge_id');
            $charge['org_charge'] = $this->input->post('org_charge');
            $this->tpa_model->edit_org($id, $charge);
            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('tpa_charge_successfully_updated'));
        }
        echo json_encode($array);
    }

    public function delete($id)
    {
        $this->tpa_model->delete($id);
        echo json_encode(array('msg' => $this->lang->line('delete_message')));
    }
}
