<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Twocheckout extends Patient_Controller {

    public $pay_method     = array();
    public $patient_data;
    public $setting;

    public function __construct() {
        parent::__construct();
        $this->config->load("payroll");
        $this->pay_method     = $this->paymentsetting_model->getActiveMethod();
        $this->setting        = $this->setting_model->get()[0];
        $this->load->model(array('onlineappointment_model','charge_model'));
    }
  
    public function index() {
        $data['api_error'] = array();
        $appointment_id = $this->session->userdata('appointment_id');
        $appointment_data = $this->onlineappointment_model->getAppointmentDetails($appointment_id);
        $data['setting'] = $this->setting;
        $charges_array = $this->charge_model->getChargeDetailsById($appointment_data->charge_id);
        $tax=0;
        $standard_charge=0;
        $data['email']=$appointment_data->email;
        $data['mobileno']=$appointment_data->mobileno;
        if(isset($charges_array->standard_charge)){
            $charge = $charges_array->standard_charge + ($charges_array->standard_charge*$charges_array->percentage/100);
            $tax=($charges_array->standard_charge*$charges_array->percentage/100);
            $standard_charge=$charges_array->standard_charge;
        }else{
            $charge=0;
            $tax=0;
            $standard_charge=0;
        }
        $data['standard_charge']=$standard_charge;
        $data['tax_amount']=$tax;
        $this->session->set_userdata('payment_amount',$charge);
        $this->session->set_userdata('charge_id',$appointment_data->charge_id);
        $total = $charge;

        //processing fees added
        $charge_type = $this->pay_method->charge_type;
        $charge_value= $this->pay_method->charge_value;
        $gateway_processing_charge=0;
        
        if($charge_type=='percentage'){
            $gateway_processing_charge=(($standard_charge * $charge_value)/100);
        }elseif($charge_type=='fix'){
            $gateway_processing_charge=$charge_value;
        }else{
            $gateway_processing_charge=0;   
        }   
        $data['gateway_processing_charge'] = $gateway_processing_charge;
        $data['amount'] = $total+$gateway_processing_charge;
        $payment_amount_including_processing_fee   =   ($total+$gateway_processing_charge);
        $this->session->set_userdata('payment_amount_including_processing_fee', $payment_amount_including_processing_fee);
        //processing fees added

        $this->load->view('patient/onlineappointment/twocheckout/index', $data);
    }
 
    public function pay(){
        $appointment_id = $this->session->userdata('appointment_id');
        $appointment_data = $this->onlineappointment_model->getAppointmentDetails($appointment_id);
        $data['setting'] = $this->setting;
        $charges_array = $this->charge_model->getChargeDetailsById($appointment_data->charge_id);
        $tax=0;
        $standard_charge=0;
        $data['email']=$appointment_data->email;
        $data['mobileno']=$appointment_data->mobileno;
        if(isset($charges_array->standard_charge)){
            $charge = $charges_array->standard_charge + ($charges_array->standard_charge*$charges_array->percentage/100);
            $tax=($charges_array->standard_charge*$charges_array->percentage/100);
            $standard_charge=$charges_array->standard_charge;
        }else{
            $charge=0;
            $tax=0;
            $standard_charge=0;
        }
        $data['standard_charge']=$standard_charge;
        $data['tax_amount']=$tax;
        $this->session->set_userdata('payment_amount',$charge);
        $this->session->set_userdata('charge_id',$appointment_data->charge_id);
        $total = $charge;
      
        //processing fees added
        $charge_type = $this->pay_method->charge_type;
        $charge_value= $this->pay_method->charge_value;
        $gateway_processing_charge=0;
        
        if($charge_type=='percentage'){
            $gateway_processing_charge=(($standard_charge * $charge_value)/100);
        }elseif($charge_type=='fix'){
            $gateway_processing_charge=$charge_value;
        }else{
            $gateway_processing_charge=0;   
        }   
        $data['gateway_processing_charge'] = $gateway_processing_charge;
        $data['amount'] = $total+$gateway_processing_charge;
        $payment_amount_including_processing_fee   =   ($total+$gateway_processing_charge);
        $this->session->set_userdata('payment_amount_including_processing_fee', $payment_amount_including_processing_fee);
        //processing fees added

        $data['api_error'] = array();
        $this->form_validation->set_rules('phone', $this->lang->line('phone'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('email', $this->lang->line('email'), 'trim|required|xss_clean');
        if ($this->form_validation->run() == false) {
            $data['amount'] =number_format((float)($total+$gateway_processing_charge), 2, '.', '');
            $this->load->view('patient/onlineappointment/twocheckout/index', $data);
        } else {
            $data['api_error'] = array();
            $data['currency']=$data['setting']['currency'];
            $data['amount'] =number_format((float)($total+$gateway_processing_charge), 2, '.', '');
            $data['api_config']=$this->pay_method;
            $this->load->view('patient/onlineappointment/twocheckout/pay', $data);
        } 
    }

    public function success(){        
        $amount  = $this->session->userdata('payment_amount');
        $appointment_id  = $this->session->userdata('appointment_id');
        $charge_id  = $this->session->userdata('charge_id');         
    }

}