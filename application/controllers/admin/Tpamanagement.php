<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Tpamanagement extends Admin_Controller
{
    protected $search_type;


    public function __construct()
    {
        parent::__construct();
        $this->load->library("datatables");
        $this->config->load("payroll"); // search_type config is defined in payroll config
        $this->search_type = $this->config->item('search_type');
    }

    public function index()
    {
        if (!$this->rbac->hasPrivilege('organisation', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'tpa_management');
        $data['title']  = $this->lang->line('tpa_management');
        $data['module'] = 'tpa_management';
        $this->load->view('layout/header', $data);
        $this->load->view('admin/tpamanagement/index', $data);
        $this->load->view('layout/footer', $data);
    }

    public function gettpadatatable()
    {
        if (!$this->rbac->hasPrivilege('organisation', 'can_view')) { access_denied(); }
        $dt_response = $this->organisation_model->getAlltpaRecord();
        $dt_response = json_decode($dt_response);
        $dt_data     = array();
        if (!empty($dt_response->data)) {
            foreach ($dt_response->data as $key => $value) {

                $row = array();
                //====================================
                $action1 = "";
                $action2 = "";
                $action3 = "";
                $action4 = "";
                
                if ($this->rbac->hasPrivilege('organisation', 'can_delete')) {
                    $action1 = "<a href='#' onclick=\"delete_recordById('admin/tpamanagement/delete/" . $value->id . "')\" class='btn btn-sm btn-outline-danger' data-bs-toggle='tooltip' title='" . $this->lang->line('delete') . "'><i class='fa fa-trash'></i></a>";
                }

                if ($this->rbac->hasPrivilege('organisation', 'can_view')) {
                    $action3 = "<a href='" . base_url() . 'admin/tpa/master/' . $value->id . "' class='btn btn-sm btn-outline-secondary' data-bs-toggle='tooltip' title='" . $this->lang->line('organization_profile') . "'><i class='fa fa-reorder' aria-hidden='true'></i></a>";
                    $action4 = "<a href='" . base_url() . 'admin/tpamanagement/tpareport/' . (int)$value->id . "' class='btn btn-sm btn-outline-secondary' data-bs-toggle='tooltip' title='" . $this->lang->line('tpa_report') . "'><i class='fas fa-umbrella'></i></a>";
                }

                if ($this->rbac->hasPrivilege('organisation', 'can_edit')) {
                    $action2 = "<a href='#' onclick=\"get_orgdata('" . $value->id . "')\" class='btn btn-sm btn-outline-primary' data-bs-toggle='tooltip' title='" . $this->lang->line('edit') . "'><i class='fa fa-pencil'></i></a>";
                }

                //==============================
                $row[]     = html_escape($value->organisation_name);
                $row[]     = html_escape($value->code);
                $row[]     = html_escape($value->contact_no);
                $row[]     = html_escape($value->address);
                $row[]     = html_escape($value->contact_person_name);
                $row[]     = html_escape($value->contact_person_phone);
                $row[]     = "<div class='d-inline-flex gap-1'>" . $action3 . $action2 . $action4 . $action1 . "</div>";
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

    public function add_organisation()
    {
        if (!$this->rbac->hasPrivilege('organisation', 'can_add')) {
            access_denied();
        }
        $this->form_validation->set_rules('name', $this->lang->line('name'), 'required|xss_clean');
        $this->form_validation->set_rules('code', $this->lang->line('code'), 'required|xss_clean');
        $this->form_validation->set_rules('contact_number', $this->lang->line('contact_no'), 'required|numeric|xss_clean');
        if ($this->form_validation->run() == false) {

            $msg = array(
                'name'           => form_error('name'),
                'code'           => form_error('code'),
                'contact_number' => form_error('contact_number'),
            );

            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $organisation = array(
                'organisation_name'    => $this->input->post('name', TRUE),
                'code'                 => $this->input->post('code', TRUE),
                'contact_no'           => $this->input->post('contact_number', TRUE),
                'address'              => $this->input->post('address', TRUE),
                'contact_person_name'  => $this->input->post('contact_person_name', TRUE),
                'contact_person_phone' => $this->input->post('contact_person_phone', TRUE),
            );
            $this->organisation_model->add($organisation);
            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
        }
        echo json_encode($array);
    }

    public function get_data($id)
    {
        if (!$this->rbac->hasPrivilege('organisation', 'can_view')) {
            access_denied();
        }
        $org   = $this->organisation_model->get($id);
        $array = array(
            'id'                     => $org['id'],
            'ename'                  => $org['organisation_name'],
            'ecode'                  => $org['code'],
            'econtact_number'        => $org['contact_no'],
            'eaddress'               => $org['address'],
            'econtact_person_name'  => $org['contact_person_name'],
            'econtact_person_phone' => $org['contact_person_phone'],
        );
        echo json_encode($array);
    }

    public function edit()
    {
        if (!$this->rbac->hasPrivilege('organisation', 'can_edit')) {
            access_denied();
        }
        $this->form_validation->set_rules('ename', $this->lang->line('name'), 'required|xss_clean');
        $this->form_validation->set_rules('ecode', $this->lang->line('code'), 'required|xss_clean');
        $this->form_validation->set_rules('econtact_number', $this->lang->line('contact_no'), 'required|numeric|xss_clean');
        if ($this->form_validation->run() == false) {
            $msg = array(
                'e1' => form_error('ename'),
                'e2' => form_error('ecode'),
                'e3' => form_error('econtact_number'),
                'e4' => form_error('eaddress'),
                'e5' => form_error('econtact_person_name'),
                'e6' => form_error('econtact_person_phone'),
            );
            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $organisation = array(
                'id'                   => $this->input->post('org_id', TRUE),
                'organisation_name'    => $this->input->post('ename', TRUE),
                'code'                 => $this->input->post('ecode', TRUE),
                'contact_no'           => $this->input->post('econtact_number', TRUE),
                'address'              => $this->input->post('eaddress', TRUE),
                'contact_person_name'  => $this->input->post('econtact_person_name', TRUE),
                'contact_person_phone' => $this->input->post('econtact_person_phone', TRUE),
            );
            $this->organisation_model->add($organisation);
            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('update_message'));
        }
        echo json_encode($array);
    }

    public function delete($id)
    {
        if (!$this->rbac->hasPrivilege('organisation', 'can_delete')) {
            access_denied();
        }
        $this->organisation_model->delete($id);
        $json_array = json_encode(array('status' => 1, 'msg' => $this->lang->line('delete_message')));
        echo $json_array;
    }

    public function checkvalidation()
    {
        if (!$this->rbac->hasPrivilege('tpa_report', 'can_view')) { access_denied(); }
        $param = array(
            'search_type'     => $this->input->post('search_type', TRUE),
            'organisation'    => $this->input->post('organisation', TRUE),
            'constant_id'     => $this->input->post('constant_id', TRUE),
            'date_from'       => $this->input->post('date_from', TRUE),
            'date_to'         => $this->input->post('date_to', TRUE),
            'case_id'         => $this->input->post('case_id', TRUE),
            'charge_category' => $this->input->post('charge_category', TRUE),
            'charge_id'       => $this->input->post('charge_id', TRUE),
        );

        $json_array = array('status' => 'success', 'error' => '', 'param' => $param, 'message' => $this->lang->line('success_message'));
        echo json_encode($json_array);
    }
	
    public function tpareport($org_id = '')
    {
        if (!$this->rbac->hasPrivilege('tpa_report', 'can_view')) { access_denied(); }
        $this->session->set_userdata('top_menu', 'Reports');
        $this->session->set_userdata('sub_menu', 'reports/tpa');
        $this->session->set_userdata('subsub_menu', 'reports/tpamanagement/tpareport');

        $doctorlist                  = $this->staff_model->getEmployeeByRoleID(3);
        $data['doctorlist']          = $doctorlist;
        $data['organisation']        = $this->organisation_model->get();
        $data["searchlist"]          = $this->search_type;
        $data['opd_charge_category'] = $this->charge_category_model->getCategoryByModule("opd");
        $data['ipd_charge_category'] = $this->charge_category_model->getCategoryByModule("ipd");
        $data['charge_category']     = array_merge($data['opd_charge_category'], $data['ipd_charge_category']);
        $data['preselect_org']       = (int) $org_id;

        $data['module'] = 'tpa_management';
        $this->load->view('layout/header', $data);
        $this->load->view('admin/tpamanagement/tpareport', $data);
        $this->load->view('layout/footer', $data);
    }

    public function tpareports()
    {
        if (!$this->rbac->hasPrivilege('tpa_report', 'can_view')) { access_denied(); }
        $search['search_type'] = $this->input->post('search_type', TRUE);
        $search['date_from']   = $this->input->post('date_from', TRUE);
        $search['date_to']     = $this->input->post('date_to', TRUE);
        $start_date            = '';
        $end_date              = '';

        if ($search['search_type'] == 'period') {
            $start_date = $this->customlib->dateFormatToYYYYMMDD($search['date_from']);
            $end_date   = $this->customlib->dateFormatToYYYYMMDD($search['date_to']);
        } else {
            if (isset($search['search_type']) && $search['search_type'] != '') {
                $dates               = $this->customlib->get_betweendate($search['search_type']);
                $data['search_type'] = $search['search_type'];
                $start_date          = $dates['from_date'];
                $end_date            = $dates['to_date'];
            }
        }

        $search_array['start_date']      = $start_date;
        $search_array['end_date']        = $end_date;
        $search_array['constant_id']     = $this->input->post('constant_id', TRUE);
        $search_array['organisation']    = $this->input->post('organisation', TRUE);
        $search_array['case_id']         = $this->input->post('case_id', TRUE);
        $search_array['charge_category'] = $this->input->post('charge_category', TRUE);
        $search_array['charge_id']       = $this->input->post('charge_id', TRUE);

        $reportdata = $this->report_model->tpareportsRecords($search_array);
        $reportdata = json_decode($reportdata);
        $dt_data    = array();
        if (!empty($reportdata->data)) {
            foreach ($reportdata->data as $key => $value) {

                $tax        = "(" . $value->tax . "%)";
                $tax_amount = amountFormat(($value->apply_charge * $value->tax) / 100);

                $row       = array();
                $row[]     = html_escape($this->customlib->getSessionPrefixByType($value->prefixno)) . (int)$value->id;
                $row[]     = html_escape($value->case_reference_id);
                $row[]     = html_escape(strtoupper($value->reference));
                $row[]     = html_escape($value->insurance_id);
                $row[]     = html_escape($value->organisation_name);
                $row[]     = composePatientName($value->patient_name, $value->patient_id);
                $row[]     = $this->customlib->YYYYMMDDHisTodateFormat($value->date);
                $row[]     = composeStaffNameByString($value->name, $value->surname, $value->employee_id);
                $row[]     = html_escape($value->charge_name);
                $row[]     = html_escape($value->charge_category_name);
                $row[]     = html_escape($value->charge_type);
                $row[]     = html_escape($value->standard_charge);
                $row[]     = html_escape($value->apply_charge);
                $row[]     = html_escape($value->tpa_charge);
                $row[]     = $tax . ' ' . $tax_amount;
                $row[]     = html_escape($value->amount);
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

}
