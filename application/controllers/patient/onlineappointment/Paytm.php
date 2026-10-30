<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Paytm extends Patient_Controller
{
	public $setting;


    public $pay_method = "";
    public $amount = 0;

    function __construct() {
        parent::__construct();
        $this->pay_method = $this->paymentsetting_model->getActiveMethod();
        $this->setting = $this->setting_model->get()[0];
        $this->load->library('paytm_lib');
        $this->load->model(array('onlineappointment_model','charge_model'));
    }

    public function index() {
        
        $data = array();
        $appointment_id = $this->session->userdata('appointment_id');
        $appointment_data = $this->onlineappointment_model->getAppointmentDetails($appointment_id);
        $charges_array = $this->charge_model->getChargeDetailsById($appointment_data->charge_id);
        $tax=0;
        $standard_charge=0;
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
        $this->session->set_userdata('gateway_processing_charge', $gateway_processing_charge);
        $this->session->set_userdata('charge_type', $charge_type);
        //processing fees added

        $data['setting'] = $this->setting;
        $data['api_error'] = array();
        $amount= $total;
        $data['amount'] = ($amount+$gateway_processing_charge);
        $paytmParams = array();
        $ORDER_ID = time();
        $CUST_ID = time();

        $paytmParams = array(
            "MID" => $this->pay_method->api_publishable_key,
            "WEBSITE" => $this->pay_method->paytm_website,
            "INDUSTRY_TYPE_ID" => $this->pay_method->paytm_industrytype,
            "CHANNEL_ID" => "WEB",
            "ORDER_ID" => $ORDER_ID,
            "CUST_ID" => $appointment_id,
            "TXN_AMOUNT" => $data['amount'],
            "CALLBACK_URL" => base_url() . "patient/onlineappointment/paytm/complete",
        );

        $paytmChecksum = $this->paytm_lib->getChecksumFromArray($paytmParams, $this->pay_method->api_secret_key);
        $paytmParams["CHECKSUMHASH"] = $paytmChecksum;
        $transactionURL              = 'https://securegw.paytm.in/order/process';//for sand-box
        
        $data['paytmParams'] = $paytmParams;
        $data['transactionURL'] = $transactionURL;
        $this->load->view("patient/onlineappointment/paytm/index", $data);
    }

    public function complete()
    {

        $paytmChecksum = "";
        $paramList = array();
        $isValidChecksum = "FALSE";
        $paramList = $_POST;
        $paytmChecksum = isset($_POST["CHECKSUMHASH"]) ? $_POST["CHECKSUMHASH"] : "";
        $isValidChecksum = $this->paytm_lib->verifychecksum_e($paramList, $this->pay_method->api_secret_key, $paytmChecksum);
        $patient_data  = $this->session->userdata('patient');
        $patient_id  = $patient_data['patient_id'];
        if ($isValidChecksum == "TRUE") {

            if ($_POST["STATUS"] == "TXN_SUCCESS") {
            $appointment_id = $this->session->userdata('appointment_id');
            $charge_id  = $this->session->userdata('charge_id');
            $appointment_data = $this->onlineappointment_model->getAppointmentDetails($appointment_id);
            $amount = $this->session->userdata('payment_amount');
            $charge_type                = $this->session->userdata('charge_type'); 
            $gateway_processing_charge  = $this->session->userdata('gateway_processing_charge'); 
            $transactionid=$_POST['TXNID'];
			
			$charges_array = $this->charge_model->getChargeDetailsById($charge_id);
			 
			if(isset($charges_array->standard_charge)){
				
				$tax=($charges_array->standard_charge*$charges_array->percentage/100);
				$standard_charge=$charges_array->standard_charge;
			}else{
				
				$tax=0;
				$standard_charge=0;
			}
						
            $payment_data = array(
				'standard_amount' => $standard_charge,
				'tax' => $tax,
                'appointment_id' => $appointment_id,
                'paid_amount'    => $amount,
                'charge_id'      => $charge_id,
                'payment_type'   => 'Online',
                'transaction_id'=>  $transactionid,
                'payment_mode'   => 'Paytm',
                'note'           => "Payment deposit through Paytm TXN ID: " . $transactionid,
                'date'           => date("Y-m-d H:i:s"),
            ); 

            $payment_section = $this->config->item('payment_section');

            $transaction_array = array(
                "processing_charge_type"    => $charge_type,
                "gateway_processing_charge" => $gateway_processing_charge,
                'amount'                    => $amount,
                'patient_id'                => $patient_id,
                'section'                   => $payment_section['appointment'],
                'type'                      => 'payment',
                'appointment_id'            => $appointment_id,
                'payment_mode'              => "Online",
                'payment_date'              => date('Y-m-d H:i:s'),
                'received_by'               => '',
                'note'                      =>  "Payment deposit through Paytm TXN ID: " . $transactionid,
            );
            
            $return_detail = $this->onlineappointment_model->paymentSuccess($payment_data,$transaction_array);
            redirect(base_url("patient/onlineappointment/checkout/successinvoice/" . $return_detail));
               
            } else {
                redirect(base_url("patient/onlineappointment/checkout/paymentfailed"));
            }
        } else {
            redirect(base_url("patient/onlineappointment/checkout/paymentfailed"));
        }

    }

}

?>
