<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Expmedicine extends Admin_Controller
{
	public $search_type;
	public $time_format;


    public function __construct()
    {
        parent::__construct();
        $this->config->load("payroll");
        $this->search_type = $this->config->item('search_type_expiry');
        $this->load->library('datatables');
        $this->time_format = $this->customlib->getHospitalTimeFormat();
    }

    public function search()
    {
        if (!$this->rbac->hasPrivilege('ambulance', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'Transport');
        $this->session->set_userdata('sub_menu', 'vehicle/index');
        $data['title']       = $this->lang->line('add_vehicle');
        $listVehicle         = $this->vehicle_model->get();
        $data['listVehicle'] = $listVehicle;
        $data['module'] = 'pharmacy';
        $this->load->view('layout/header', $data);
        $this->load->view('admin/vehicle/search', $data);
        $this->load->view('layout/footer', $data);
    }

    public function add()
    {
        if (!$this->rbac->hasPrivilege('ambulance', 'can_add')) {
            access_denied();
        }
        $this->form_validation->set_rules('vehicle_no', $this->lang->line('vehicle_number'), 'required');
        $this->form_validation->set_rules('vehicle_model', $this->lang->line('vehicle_model'), 'required');
        $this->form_validation->set_rules('vehicle_type', $this->lang->line('vehicle_type'), 'required');

        if ($this->form_validation->run() == false) {
            $msg = array(
                'vehicle_no'    => form_error('vehicle_no'),
                'vehicle_model' => form_error('vehicle_model'),
                'vehicle_type'  => form_error('vehicle_type'),
            );
            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $manufacture_year = $this->input->post('manufacture_year');
            $data             = array(
                'vehicle_no'     => $this->input->post('vehicle_no'),
                'vehicle_model'  => $this->input->post('vehicle_model'),
                'driver_name'    => $this->input->post('driver_name'),
                'driver_licence' => $this->input->post('driver_licence'),
                'driver_contact' => $this->input->post('driver_contact'),
                'vehicle_type'   => $this->input->post('vehicle_type'),
                'note'           => $this->input->post('note'),
            );
            ($manufacture_year != "") ? $data['manufacture_year'] = $manufacture_year : '';
            $this->vehicle_model->add($data);
            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
        }
        echo json_encode($array);
    }

    public function edit()
    {
        if (!$this->rbac->hasPrivilege('ambulance', 'can_view')) {
            access_denied();
        }
        $id          = $this->input->post("id");
        $listVehicle = $this->vehicle_model->getDetails($id);
        echo json_encode($listVehicle);
    }

    public function update()
    {
        if (!$this->rbac->hasPrivilege('ambulance', 'can_edit')) {
            access_denied();
        }
        $this->form_validation->set_rules('vehicle_no', $this->lang->line('vehicle_number'), 'required');
        $this->form_validation->set_rules('vehicle_model', $this->lang->line('vehicle_model'), 'required');
        $this->form_validation->set_rules('vehicle_type', $this->lang->line('vehicle_type'), 'required');
        if ($this->form_validation->run() == false) {
            $msg = array(
                'vehicle_no'    => form_error('vehicle_no'),
                'vehicle_model' => form_error('vehicle_model'),
                'vehicle_type'  => form_error('vehicle_type'),
            );
            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $id               = $this->input->post('id');
            $manufacture_year = $this->input->post('manufacture_year');
            $data             = array(
                'id'             => $id,
                'vehicle_no'     => $this->input->post('vehicle_no'),
                'vehicle_model'  => $this->input->post('vehicle_model'),
                'driver_name'    => $this->input->post('driver_name'),
                'driver_licence' => $this->input->post('driver_licence'),
                'driver_contact' => $this->input->post('driver_contact'),
                'vehicle_type'   => $this->input->post('vehicle_type'),
                'note'           => $this->input->post('note'),
            );
            ($manufacture_year != "") ? $data['manufacture_year'] = $manufacture_year : '';
            $this->vehicle_model->add($data);
            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
        }
        echo json_encode($array);
    }

    public function delete($id)
    {
        if (!$this->rbac->hasPrivilege('ambulance', 'can_delete')) {
            access_denied();
        }
        $this->vehicle_model->remove($id);
        redirect('admin/Vehicle/search');
    }

    public function addCallAmbulance()
    {
        if (!$this->rbac->hasPrivilege('ambulance_call', 'can_add')) {
            access_denied();
        }
        $this->form_validation->set_rules('patient_name', $this->lang->line('patient_name'), 'required');
        $this->form_validation->set_rules('vehicle_no', $this->lang->line('vehicle_model'), 'required');
        $this->form_validation->set_rules('date', $this->lang->line('date'), 'required');
        $this->form_validation->set_rules('amount', $this->lang->line('amount'), 'required');
        if ($this->form_validation->run() == false) {
            $msg = array(
                'vehicle_no'   => form_error('vehicle_no'),
                'date'         => form_error('date'),
                'amount'       => form_error('amount'),
                'patient_name' => form_error('patient_name'),
            );
            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $date = $this->input->post("date");
            $data = array(
                'patient_name' => $this->input->post('patient_name'),
                'contact_no'   => $this->input->post('contact_no'),
                'address'      => $this->input->post('address'),
                'vehicle_no'   => $this->input->post('vehicle_no'),
                'driver'       => $this->input->post('driver'),
                'amount'       => $this->input->post('amount'),
                'date'         => $this->customlib->dateFormatToYYYYMMDDHis($date, $this->time_format),
            );
            $this->vehicle_model->addCallAmbulance($data);
            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
        }
        echo json_encode($array);
    }

    public function getCallAmbulance()
    {
        if (!$this->rbac->hasPrivilege('ambulance_call', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'Transport');
        $this->session->set_userdata('sub_menu', 'vehicle/getcallambulance');
        $data['title']       = $this->lang->line('add_vehicle');
        $listCall            = $this->vehicle_model->getCallAmbulance();
        $vehiclelist         = $this->vehicle_model->get();
        $data['listCall']    = $listCall;
        $data['vehiclelist'] = $vehiclelist;
        $data['module'] = 'pharmacy';
        $this->load->view('layout/header', $data);
        $this->load->view('admin/vehicle/ambulance_call', $data);
        $this->load->view('layout/footer', $data);
    }

    public function editCall()
    {
        if (!$this->rbac->hasPrivilege('ambulance_call', 'can_view')) {
            access_denied();
        }
        $id       = $this->input->post("id");
        $listCall = $this->vehicle_model->getCallDetails($id);
        $date     = $this->customlib->YYYYMMDDHisTodateFormat($listCall['date'], $this->time_format);
        $listCall["date"] = $date;
        echo json_encode($listCall);
    }

    public function updateCallAmbulance()
    {
        if (!$this->rbac->hasPrivilege('ambulance_call', 'can_edit')) {
            access_denied();
        }
        $this->form_validation->set_rules('patient_name', $this->lang->line('patient_name'), 'required');
        $this->form_validation->set_rules('vehicle_no', $this->lang->line('vehicle_number'), 'required');
        $this->form_validation->set_rules('date', $this->lang->line('date'), 'required');
        $this->form_validation->set_rules('amount', $this->lang->line('amount'), 'required');
        if ($this->form_validation->run() == false) {
            $msg = array(
                'patient_name' => form_error('patient_name'),
                'vehicle_no'   => form_error('vehicle_no'),
                'date'         => form_error('date'),
                'amount'       => form_error('amount'),
            );
            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $id   = $this->input->post('id');
            $date = $this->input->post('date');

            $data = array(
                'id'           => $id,
                'patient_name' => $this->input->post('patient_name'),
                'contact_no'   => $this->input->post('contact_no'),
                'address'      => $this->input->post('address'),
                'vehicle_no'   => $this->input->post('vehicle_no'),
                'driver'       => $this->input->post('driver_name'),
                'amount'       => $this->input->post('amount'),
                'date'         => $this->customlib->dateFormatToYYYYMMDDHis($date, $this->time_format),
            );
            $this->vehicle_model->addCallAmbulance($data);
            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('update_message'));
        }
        echo json_encode($array);
    }

    public function deleteCallAmbulance($id)
    {
        if (!$this->rbac->hasPrivilege('ambulance_call', 'can_delete')) {
            access_denied();
        }
        $this->vehicle_model->delete($id);
        redirect('admin/Vehicle/getcallambulance');
    }

    public function getVehicleDetail()
    {
        $id     = $this->input->post('id');
        $result = $this->vehicle_model->getDetails($id);
        echo json_encode($result);
    }

    public function checkvalidation()
    {
        $filter_type = $this->input->post('filter_type', TRUE);
        $this->form_validation->set_rules('search_type', $this->lang->line('search_type'), 'trim|required|xss_clean');

        if ($this->form_validation->run() == false) {
            $msg = array(
                'search_type' => form_error('search_type'),
            );
            $json_array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $param = array(
                'filter_type'       => $filter_type,
                'search_type'       => $this->input->post('search_type', TRUE),
                'date_from'         => $this->input->post('date_from', TRUE),
                'date_to'           => $this->input->post('date_to', TRUE),
                'supplier'          => $this->input->post('supplier', TRUE),
                'medicine_category' => $this->input->post('medicine_category', TRUE),
            );
            $json_array = array('status' => 'success', 'error' => '', 'param' => $param, 'message' => $this->lang->line('success_message'));
        }
        echo json_encode($json_array);
    }

    public function expmedicinereport()
    {
        if (!$this->rbac->hasPrivilege('expiry_medicine_report', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'Reports');
        $this->session->set_userdata('sub_menu', 'reports/pharmacy');
        $this->session->set_userdata('subsub_menu', 'reports/expmedicine/expmedicinereport');

        $data["searchlist"]       = $this->search_type;
        $supplierCategory         = $this->medicine_category_model->getSupplierCategory();
        $data["supplierCategory"] = $supplierCategory;
        $medicineCategory         = $this->medicine_category_model->getMedicineCategory();
        $data["medicineCategory"] = $medicineCategory;
        $data['module'] = 'reports';
        $this->load->view('layout/header', $data);
        $this->load->view('admin/expmedicine/expmedicinereport', $data);
        $this->load->view('layout/footer', $data);
    }

    public function expmedicinereports()
    {
        $filter_type                    = $this->input->post('filter_type', TRUE);
        $search['search_type']          = $this->input->post('search_type', TRUE);
        $condition['medicine_category'] = $this->input->post('medicine_category', TRUE);
        $condition['supplier']          = $this->input->post('supplier', TRUE);
        $condition['filter_type']       = $filter_type;
        if (!empty($search['search_type'])) {
            $dates = $this->customlib->get_betweendate($search['search_type']);
        } else {
            $dates = $this->customlib->get_betweendate('this_year');
        }
        $start_date = $dates['from_date'];
        $end_date   = $dates['to_date'];

        $totals = $this->report_model->expmedicinereportsTotalAmount($start_date, $end_date, $condition);
        $reportdata   = $this->report_model->expmedicinereportsRecords($start_date, $end_date, $condition);
        $reportdata   = json_decode($reportdata);

        $dt_data = array();
        if (!empty($reportdata->data)) {
            foreach ($reportdata->data as $key => $value) {
                $row   = array();
                $row[] = html_escape($value->medicine_name);
                $row[] = html_escape($value->medicine_category);
                $row[] = html_escape($value->group_name);
                $row[] = html_escape($value->company_name);
                $row[] = html_escape($value->supplier);
                $row[] = !empty($value->purchase_no) ? ($this->customlib->getSessionPrefixByType('purchase_no') . $value->purchase_no) : '';
                $row[] = html_escape($value->batch_no);
                $row[] = $this->customlib->YYYYMMDDTodateFormat($value->inward_date);
                $row[] = $this->customlib->YYYYMMDDTodateFormat($value->expiry);
                $remaining = (int)$value->remaining_days;
                if ($remaining < 0) {
                    $row[] = '<span class="label label-danger" data-order="' . $remaining . '">' . $this->lang->line('expired') . '</span>';
                } elseif ($remaining <= 180) {
                    $row[] = '<span class="label label-warning" data-order="' . $remaining . '">' . $remaining . ' ' . $this->lang->line('days') . '</span>';
                } else {
                    $row[] = '<span class="label label-success" data-order="' . $remaining . '">' . $remaining . ' ' . $this->lang->line('days') . '</span>';
                }
                $row[] = (int)$value->quantity;
                $row[] = (int)$value->available_quantity;
                $row[] = number_format((float)$value->purchase_rate, 2);
                $row[] = number_format((float)$value->available_quantity * (float)$value->purchase_rate, 2);
                $row[] = number_format((float)$value->amount, 2);
                $dt_data[] = $row;
            }
        }

        $json_data = array(
            "draw"            => intval($reportdata->draw),
            "recordsTotal"    => intval($reportdata->recordsTotal),
            "recordsFiltered" => intval($reportdata->recordsFiltered),
            "data"            => $dt_data,
            "total_available_qty"  => $totals['total_available_qty'],
            "total_stock_value"    => number_format((float) $totals['total_stock_value'],   2),
            "total_packing_qty"   => $totals['total_packing_qty'],
            "total_purchase_rate" => number_format((float) $totals['total_purchase_rate'], 2),
            "total_amount"        => number_format((float) $totals['total_amount'],        2),
        );
        echo json_encode($json_data);
    }

    public function medicinepurchasereport()
    {
        if (!$this->rbac->hasPrivilege('medicine_purchase_report', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'Reports');
        $this->session->set_userdata('sub_menu', 'reports/pharmacy');
        $this->session->set_userdata('subsub_menu', 'reports/expmedicine/medicinepurchasereport');
        $data["searchlist"]       = $this->search_type;
        $supplierCategory         = $this->medicine_category_model->getSupplierCategory();
        $data["supplierCategory"] = $supplierCategory;
        $data['module'] = 'reports';
        $this->load->view('layout/header', $data);
        $this->load->view('admin/expmedicine/medicinepurchasereport', $data);
        $this->load->view('layout/footer', $data);
    }

    public function medicinepurchasereports()
    {
        $search['search_type'] = $this->input->post('search_type');
        $search['date_from']   = $this->input->post('date_from');
        $search['date_to']     = $this->input->post('date_to');
        $condition['supplier'] = $this->input->post('supplier');

        if (!empty($search['search_type'])) {
            $dates = $this->customlib->get_betweendate($search['search_type']);
        } else {
            $dates = $this->customlib->get_betweendate('this_year');
        }
        $start_date = $dates['from_date'];
        $end_date   = $dates['to_date'];

        $reportdata = $this->report_model->medicinePurchaseReportRecords($start_date, $end_date, $condition);
        $reportdata = json_decode($reportdata);

        $dt_data = array();
        if (!empty($reportdata->data)) {
            foreach ($reportdata->data as $key => $value) {
                $row   = array();
                $row[] = $this->customlib->getSessionPrefixByType('purchase_no') . $value->id;
                $row[] = html_escape($value->invoice_no);
                $row[] = $this->customlib->YYYYMMDDHisTodateFormat($value->date);
                $row[] = html_escape($value->supplier);
                $row[] = number_format((float) $value->total, 2);
                $row[] = number_format((float) $value->discount, 2);
                $row[] = number_format((float) $value->tax, 2);
                $row[] = number_format((float) $value->net_amount, 2);
                $row[] = html_escape($value->payment_mode);
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

    public function medicinepurchasecheckvalidation()
    {
        $this->form_validation->set_rules('search_type', $this->lang->line('search_type'), 'trim|required|xss_clean');
        if ($this->form_validation->run() == false) {
            $msg        = array('search_type' => form_error('search_type'));
            $json_array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $param      = array(
                'search_type' => $this->input->post('search_type', TRUE),
                'date_from'   => $this->input->post('date_from', TRUE),
                'date_to'     => $this->input->post('date_to', TRUE),
                'supplier'    => $this->input->post('supplier', TRUE),
            );
            $json_array = array('status' => 'success', 'error' => '', 'param' => $param, 'message' => $this->lang->line('success_message'));
        }
        echo json_encode($json_array);
    }

    public function medicinepurchasereturnreport()
    {
        if (!$this->rbac->hasPrivilege('medicine_purchase_return_report', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'Reports');
        $this->session->set_userdata('sub_menu', 'reports/pharmacy');
        $this->session->set_userdata('subsub_menu', 'reports/expmedicine/medicinepurchasereturnreport');
        $data["searchlist"]       = $this->search_type;
        $supplierCategory         = $this->medicine_category_model->getSupplierCategory();
        $data["supplierCategory"] = $supplierCategory;
        $data['module'] = 'reports';
        $this->load->view('layout/header', $data);
        $this->load->view('admin/expmedicine/medicinepurchasereturnreport', $data);
        $this->load->view('layout/footer', $data);
    }

    public function medicinepurchasereturnreports()
    {
        $search['search_type'] = $this->input->post('search_type');
        $condition['supplier'] = $this->input->post('supplier');
        $condition['reason']   = $this->input->post('reason');

        if (!empty($search['search_type'])) {
            $dates = $this->customlib->get_betweendate($search['search_type']);
        } else {
            $dates = $this->customlib->get_betweendate('this_year');
        }
        $start_date = $dates['from_date'];
        $end_date   = $dates['to_date'];

        ob_start();
        $raw = $this->report_model->medicinePurchaseReturnReportRecords($start_date, $end_date, $condition);
        ob_get_clean(); // discard any PHP notice/warning output that would corrupt JSON
        $reportdata = json_decode($raw);

        $dt_data = array();
        if (!empty($reportdata->data)) {
            foreach ($reportdata->data as $key => $value) {
                $row   = array();
                $row[] = $this->customlib->YYYYMMDDHisTodateFormat($value->return_date);
                $row[] = html_escape($value->invoice_no);
                $row[] = html_escape($value->supplier);
                $row[] = html_escape($value->reason);
                $row[] = html_escape($value->returned_by_name);
                $row[] = html_escape($value->note);
                $row[] = number_format((float) $value->total_amount, 2);
                $dt_data[] = $row;
            }
        }
        $json_data = array(
            "draw"            => intval($reportdata->draw ?? 0),
            "recordsTotal"    => intval($reportdata->recordsTotal ?? 0),
            "recordsFiltered" => intval($reportdata->recordsFiltered ?? 0),
            "data"            => $dt_data,
        );
        echo json_encode($json_data);
    }

    public function medicinepurchasereturncheckvalidation()
    {
        $this->form_validation->set_rules('search_type', $this->lang->line('search_type'), 'trim|required|xss_clean');
        if ($this->form_validation->run() == false) {
            $msg        = array('search_type' => form_error('search_type'));
            $json_array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $param      = array(
                'search_type' => $this->input->post('search_type', TRUE),
                'date_from'   => $this->input->post('date_from', TRUE),
                'date_to'     => $this->input->post('date_to', TRUE),
                'supplier'    => $this->input->post('supplier', TRUE),
                'reason'      => $this->input->post('reason', TRUE),
            );
            $json_array = array('status' => 'success', 'error' => '', 'param' => $param, 'message' => $this->lang->line('success_message'));
        }
        echo json_encode($json_array);
    }

    // ─── D4a: Medicine Sale Report ───────────────────────────────────────────

    public function medicinesalereport()
    {
        if (!$this->rbac->hasPrivilege('medicine_sale_report', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'Reports');
        $this->session->set_userdata('sub_menu', 'reports/pharmacy');
        $this->session->set_userdata('subsub_menu', 'reports/expmedicine/medicinesalereport');
        $data["searchlist"]       = $this->search_type;
        $medicineCategory         = $this->medicine_category_model->getMedicineCategory();
        $data["medicineCategory"] = $medicineCategory;
        $data['module'] = 'reports';
        $this->load->view('layout/header', $data);
        $this->load->view('admin/expmedicine/medicinesalereport', $data);
        $this->load->view('layout/footer', $data);
    }

    public function medicinesalereports()
    {
        $search['search_type']          = $this->input->post('search_type', TRUE);
        $condition['medicine_category'] = $this->input->post('medicine_category', TRUE);
        $condition['medicine_name']     = $this->input->post('medicine_name', TRUE);

        if (!empty($search['search_type'])) {
            $dates = $this->customlib->get_betweendate($search['search_type']);
        } else {
            $dates = $this->customlib->get_betweendate('this_year');
        }
        $start_date = $dates['from_date'];
        $end_date   = $dates['to_date'];

        $reportdata = $this->report_model->medicineSaleReportRecords($start_date, $end_date, $condition);
        $reportdata = json_decode($reportdata);

        $dt_data = array();
        if (!empty($reportdata->data)) {
            foreach ($reportdata->data as $key => $value) {
                $row   = array();
                $row[] = 'PH-' . html_escape($value->bill_id);
                $row[] = html_escape($value->patient_name);
                $row[] = $this->customlib->YYYYMMDDTodateFormat($value->date);
                $row[] = html_escape($value->medicine_name);
                $row[] = html_escape($value->medicine_category);
                $row[] = html_escape($value->batch_no);
                $row[] = (int)$value->quantity;
                $row[] = number_format((float)$value->sale_price, 2);
                $row[] = number_format((float)$value->total, 2);
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

    public function medicinesalecheckvalidation()
    {
        $this->form_validation->set_rules('search_type', $this->lang->line('search_type'), 'trim|required|xss_clean');
        if ($this->form_validation->run() == false) {
            $msg        = array('search_type' => form_error('search_type'));
            $json_array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $param = array(
                'search_type'      => $this->input->post('search_type', TRUE),
                'date_from'        => $this->input->post('date_from', TRUE),
                'date_to'          => $this->input->post('date_to', TRUE),
                'medicine_category'=> $this->input->post('medicine_category', TRUE),
                'medicine_name'    => $this->input->post('medicine_name', TRUE),
            );
            $json_array = array('status' => 'success', 'error' => '', 'param' => $param, 'message' => $this->lang->line('success_message'));
        }
        echo json_encode($json_array);
    }

    // ─── D4b: Medicine Profit & Loss Report ──────────────────────────────────

    public function medicineprofitlossreport()
    {
        if (!$this->rbac->hasPrivilege('medicine_profit_loss_report', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'Reports');
        $this->session->set_userdata('sub_menu', 'reports/pharmacy');
        $this->session->set_userdata('subsub_menu', 'reports/expmedicine/medicineprofitlossreport');
        $data["searchlist"]       = $this->search_type;
        $medicineCategory         = $this->medicine_category_model->getMedicineCategory();
        $data["medicineCategory"] = $medicineCategory;
        $data['module'] = 'reports';
        $this->load->view('layout/header', $data);
        $this->load->view('admin/expmedicine/medicineprofitlossreport', $data);
        $this->load->view('layout/footer', $data);
    }

    public function medicineprofitlossreports()
    {
        $search['search_type']          = $this->input->post('search_type', TRUE);
        $condition['medicine_category'] = $this->input->post('medicine_category', TRUE);

        if (!empty($search['search_type'])) {
            $dates = $this->customlib->get_betweendate($search['search_type']);
        } else {
            $dates = $this->customlib->get_betweendate('this_year');
        }
        $start_date = $dates['from_date'];
        $end_date   = $dates['to_date'];

        $reportdata = $this->report_model->medicineProfitLossReportRecords($start_date, $end_date, $condition);
        $reportdata = json_decode($reportdata);

        $dt_data = array();
        if (!empty($reportdata->data)) {
            foreach ($reportdata->data as $key => $value) {
                $pl_class = ((float)$value->net_pl >= 0) ? '' : 'text-danger';
                $row   = array();
                $row[] = html_escape($value->medicine_name);
                $row[] = html_escape($value->medicine_category);
                $row[] = (int)$value->total_purchased;
                $row[] = number_format((float)$value->avg_purchase_price, 2);
                $row[] = (int)$value->total_sold;
                $row[] = number_format((float)$value->avg_sale_price, 2);
                $row[] = number_format((float)$value->revenue, 2);
                $row[] = number_format((float)$value->total_cost, 2);
                $row[] = '<span class="' . $pl_class . '">' . number_format((float)$value->net_pl, 2) . '</span>';
                $pl_pct = ((float)$value->total_cost > 0) ? number_format(((float)$value->net_pl / (float)$value->total_cost) * 100, 1) . '%' : '0%';
                $row[] = '<span class="' . $pl_class . '">' . $pl_pct . '</span>';
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

    public function medicineprofitlosscheckvalidation()
    {
        $this->form_validation->set_rules('search_type', $this->lang->line('search_type'), 'trim|required|xss_clean');
        if ($this->form_validation->run() == false) {
            $msg        = array('search_type' => form_error('search_type'));
            $json_array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $param = array(
                'search_type'      => $this->input->post('search_type', TRUE),
                'date_from'        => $this->input->post('date_from', TRUE),
                'date_to'          => $this->input->post('date_to', TRUE),
                'medicine_category'=> $this->input->post('medicine_category', TRUE),
            );
            $json_array = array('status' => 'success', 'error' => '', 'param' => $param, 'message' => $this->lang->line('success_message'));
        }
        echo json_encode($json_array);
    }

}
