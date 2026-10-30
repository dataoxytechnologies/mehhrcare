<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Generatecertificate extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();

        $this->load->library('Customlib');
        $this->load->library('datatables');
        $this->load->model('certificate_model');
        $this->load->model('generatecertificate_model');
        $this->load->library('system_notification');
    }

    public function index()
    {
        if (!$this->rbac->hasPrivilege('generate_certificate', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'Certificate');
        $this->session->set_userdata('sub_menu', 'admin/generatecertificate');
        $certificateList         = $this->certificate_model->getpatientcertificate();
        $data['certificateList'] = $certificateList;
        $patientlist             = $this->patient_model->getPatientListall();
        $data['patientlist']     = $patientlist;
        $data['module'] = 'certificate';
        $this->load->view('layout/header', $data);
        $this->load->view('admin/certificate/generatecertificate', $data);
        $this->load->view('layout/footer', $data);
    }

    public function search()
    {
        $this->session->set_userdata('top_menu', 'Certificate');
        $this->session->set_userdata('sub_menu', 'admin/generatecertificate');

        $certificateList         = $this->certificate_model->getpatientcertificate();
        $data['certificateList'] = $certificateList;
        $data['module']          = $this->input->post('module', TRUE);
        $data['patient_status']  = $this->input->post('patient_status', TRUE);
        $data['certificate']     = $this->input->post('certificate_id', TRUE);
        $button                  = $this->input->post('search', TRUE);
        if ($this->input->server('REQUEST_METHOD') == "GET") {
            $data['module'] = 'certificate';
            $this->load->view('layout/header', $data);
            $this->load->view('admin/certificate/generatecertificate', $data);
            $this->load->view('layout/footer', $data);
        } else {
            $module         = $this->input->post('module', TRUE);
            $patient_status = $this->input->post('patient_status', TRUE);
            $search         = $this->input->post('search', TRUE);
            $certificate    = $this->input->post('certificate_id', TRUE);
            $data['module'] = 'certificate';
            $this->load->view('layout/header', $data);
            $this->load->view('admin/certificate/generatecertificate', $data);
            $this->load->view('layout/footer', $data);
        }
    }

    public function checkvalidation()
    {
        $search = $this->input->post('search', TRUE);
        $this->form_validation->set_rules('module', $this->lang->line('module'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('certificate_id', $this->lang->line('certificate_template'), 'trim|required|xss_clean');
        if ($this->form_validation->run() == false) {
            $msg = array(
                'module'         => form_error('module'),
                'certificate_id' => form_error('certificate_id'),

            );
            $json_array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $param = array(
                'module'         => $this->input->post('module', TRUE),
                'certificate_id' => $this->input->post('certificate_id', TRUE),
                'patient_status' => $this->input->post('patient_status', TRUE),
            );

            $json_array = array('status' => 'success', 'error' => '', 'param' => $param, 'message' => $this->lang->line('success_message'));
        }
        echo json_encode($json_array);
    }

    public function getgeneratedatatable()
    {
        $modules = $this->input->post('module', TRUE);
        $status  = $this->input->post('patient_status', TRUE);

        // Guard the module: if it is neither opd nor ipd (e.g. a stray/empty
        // request before a real search), $dt_response would stay undefined and
        // the json_decode below would null-fault — corrupting the JSON body and
        // triggering the DataTables "Invalid JSON response" alert. Return a
        // valid, empty DataTables payload instead.
        $dt_response = null;
        if ($modules == 'opd') {
            $dt_response = $this->patient_model->getAllOpdPatientforcertificate($status);
        } elseif ($modules == 'ipd') {
            $dt_response = $this->patient_model->getAllIpdPatientforcertificate($status);
        }

        $dt_response = json_decode($dt_response);
        $dt_data     = array();

        //====================================
        if (!empty($dt_response->data)) {
            foreach ($dt_response->data as $key => $value) {
                $row = array();

                $checkbox_fields = "<input type='checkbox' class='checkbox center-block'  name='check' data-patient_id='" . (int)$value->id . "' value='" . (int)$value->id . "'>";

                $moduleno = '';
                if ($value->module == 'opd') {
                    $moduleno = $this->customlib->getSessionPrefixByType('checkup_id') . $value->checkup_id;
                } elseif ($value->module == 'ipd') {
                    $moduleno = $this->customlib->getSessionPrefixByType('ipd_no') . $value->id;
                }
                //====================================
                // Null-safe casts: PHP 8.4 emits a deprecation notice when null
                // is passed to strtolower()/lang->line(), which would also poison
                // the JSON output. Coerce to string first.
                $row[] = $checkbox_fields;
                $row[] = $moduleno;
                $row[] = html_escape($value->patient_name) . " (" . html_escape($value->patient_id) . ")";
                $row[] = $this->lang->line(strtolower((string) $value->gender));
                $row[] = html_escape($value->mobileno);
                $row[] = $this->lang->line((string) $value->discharged);
                //====================
                $dt_data[] = $row;
            }
        }
        $json_data = array(
            "draw"            => intval(isset($dt_response->draw) ? $dt_response->draw : 0),
            "recordsTotal"    => intval(isset($dt_response->recordsTotal) ? $dt_response->recordsTotal : 0),
            "recordsFiltered" => intval(isset($dt_response->recordsFiltered) ? $dt_response->recordsFiltered : 0),
            "data"            => $dt_data,
        );
        echo json_encode($json_data);
    }

    public function generatemultiple()
    {
        $patienttid          = $this->input->post('data', TRUE);
        $patient_array       = json_decode($patienttid);
        $certificate_id      = $this->input->post('certificate_id', TRUE);
        $module_status       = $this->input->post('module_status', TRUE);
        $data                = array();
        $pat_arr             = array();
        $data['sch_setting'] = $this->setting_model->get();
        $data['certificate'] = $this->generatecertificate_model->getcertificatebyid($certificate_id, $module_status);

        foreach ($patient_array as $key => $value) {
            $pat_arr[] = $value->patient_id;
        }
        if ($module_status == "opd") {
            $data['patients'] = $this->patient_model->getPatientsByArrayopd($pat_arr);
           
            foreach($data['patients'] as $opd){
                
                $event_data = array(
                    'patient_id'       => $opd->patient_id,
                    'opd_ipd_no'       => $this->lang->line($opd->module) . $opd->id,
                    'certificate_name' => $data['certificate'][0]->certificate_name,
                );            
                
                $this->system_notification->send_system_notification('patient_certificate_generate', $event_data);
                
            }
            
        } elseif ($module_status == "ipd") {
            $data['patients'] = $this->patient_model->getPatientsByArrayipd($pat_arr);
            
            foreach($data['patients'] as $ipd){
                
                $event_data = array(
                    'patient_id'       => $ipd->patient_id,
                    'opd_ipd_no'       => $this->lang->line($ipd->module) . $ipd->id,
                    'certificate_name' => $data['certificate'][0]->certificate_name,
                );
                
                $this->system_notification->send_system_notification('patient_certificate_generate', $event_data);
                
            }
            
        }       
 
        $certificates = $this->load->view('admin/certificate/printcertificate', $data, true);
        echo $certificates;
    } 

}
