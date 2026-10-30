<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Stripe extends Patient_Controller
{
	public $setting;


    public $pay_method = "";
    public $amount = 0;

    function __construct() {
        parent::__construct();
        $this->pay_method = $this->paymentsetting_model->getActiveMethod();
        $this->setting = $this->setting_model->get()[0];
        $this->load->library(array('stripe_payment'));
        $this->load->model(array('onlineappointment_model','charge_model'));
    }

    public function index() {
        $appointment_id = $this->session->userdata('appointment_id');
        $appointment_data = $this->onlineappointment_model->getAppointmentDetails($appointment_id);
        $data['setting'] = $this->setting;
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
        
        $data['name'] = $appointment_data->name;
        $data['hospital_name'] = $this->setting['name'];
        $data['currency_name'] = $this->setting['currency'];
        $data['api_publishable_key'] = $this->pay_method->api_publishable_key;
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
        $this->session->set_userdata('charge_type', $charge_type);
        $this->session->set_userdata('gateway_processing_charge', $gateway_processing_charge);
        //processing fees added

        $total = $charge+$gateway_processing_charge;
        $data['amount'] = ($total);
        $this->session->set_userdata('payment_amount',$charge);
        $this->session->set_userdata('charge_id',$appointment_data->charge_id);
        $this->load->view('patient/onlineappointment/stripe/index', $data);
    }


    public function create_payment_intent()
    {
        $jsonStr = file_get_contents('php://input');
        $jsonObj = json_decode($jsonStr);
        $this->stripe_payment->PaymentIntent($jsonObj );
    }
	
    public function create_customer()
    {
        $jsonStr = file_get_contents('php://input');
        $jsonObj = json_decode($jsonStr);
        $user_detail = $this->session->userdata('params');
        $logged_user=$this->customlib->getLoggedInUserData();
        $jsonObj->fullname = $logged_user['name'];
        $jsonObj->email = $logged_user['email'];
        $this->stripe_payment->AddCustomer($jsonObj);
    }
    
    public function insert_payment()
    {
		
        $jsonStr = file_get_contents('php://input');
        $jsonObj = json_decode($jsonStr);
        $return_response = $this->stripe_payment->InsertTransaction($jsonObj);
        if ($return_response['status']) {
            $payment = $return_response['payment'];
            
            // If transaction was successful
            if (!empty($payment) && $payment->status == 'succeeded') {
                $transactionid = $payment->id;
                //=============================
                $payment_section = $this->config->item('payment_section');
                $appointment_id = $this->session->userdata('appointment_id');
                $charge_id = $this->session->userdata('charge_id');
				$charge_type = $this->session->userdata('charge_type'); 
				$gateway_processing_charge  = $this->session->userdata('gateway_processing_charge'); 
		
                $charges_array = $this->charge_model->getChargeDetailsById($charge_id);
				 
				if(isset($charges_array->standard_charge)){
					 
					$tax=($charges_array->standard_charge*$charges_array->percentage/100);
					$standard_charge=$charges_array->standard_charge;
				}else{
					 
					$tax=0;
					$standard_charge=0;
				} 			
				
				$gateway_response['standard_amount'] = $standard_charge; 
                $gateway_response['tax'] = $tax; 
                $gateway_response['appointment_id'] = $appointment_id; 
                $gateway_response['paid_amount']    = $standard_charge+$tax; //($payment->amount/100);
                $gateway_response['transaction_id'] = $transactionid;
                $gateway_response['charge_id']      = $charge_id;
                $gateway_response['payment_mode']   = 'Stripe';
                $gateway_response['payment_type']   = 'Online';
                $gateway_response['note']           = "Payment deposit through Stripe TXN ID: " . $transactionid;
                $gateway_response['date']           = date("Y-m-d H:i:s");
                $transaction_array = array(
					"processing_charge_type"    => $charge_type,
                    "gateway_processing_charge" => $gateway_processing_charge,
                    'amount'                 => $standard_charge+$tax, //($payment->amount/100),
                    'patient_id'             => $this->customlib->getPatientSessionUserID(),
                    'section'                => $payment_section['appointment'],
                    'type'                   => 'payment',
                    'appointment_id'         => $appointment_id,
                    'payment_mode'           => "Online",
                    'note'                   => "Online fees deposit through Stripe TXN ID: " . $transactionid ,
                    'payment_date'           => date('Y-m-d H:i:s'),
                    'received_by'            => 1,
                );
                $return_detail = $this->onlineappointment_model->paymentSuccess($gateway_response, $transaction_array);
                    echo json_encode(['status'=>1,'msg' => 'Transaction successful.','return_url'=>base_url("patient/onlineappointment/checkout/successinvoice/" . $appointment_id)]);
            } else {
                http_response_code(500);
                echo json_encode(['status'=>0,'msg' => 'Transaction has been failed!','return_url'=>base_url('patient/onlineappointment/checkout/paymentfailed')]);
            }
        } else {
            http_response_code(500);
            echo json_encode(['status'=>0,'msg' => $return_response['error']]);
        }
    }

    public function complete() {
        $amount = $this->session->userdata('payment_amount');
        $charge_type = $this->session->userdata('charge_type'); 
        $gateway_processing_charge  = $this->session->userdata('gateway_processing_charge'); 
        $stripeToken         = $this->input->post('stripeToken');
        $stripeTokenType     = $this->input->post('stripeTokenType');
        $stripeEmail         = $this->input->post('stripeEmail');
        $data                = $this->input->post();
        $data['stripeToken'] = $stripeToken;
        $data['total']  = $amount;
        $data['description'] = 'test product';
        $data['currency']    = 'USD'; 
        $response            = $this->stripe_payment->PaymentIntent($data);
		 
        if ($response->isSuccessful()) {
            $transactionid = $response->getTransactionReference();
            $response      = $response->getData();
            if ($response['status'] == 'succeeded') {
                $payment_section = $this->config->item('payment_section');
                $appointment_id = $this->session->userdata('appointment_id');
                $charge_id = $this->session->userdata('charge_id');
				
				$charges_array = $this->charge_model->getChargeDetailsById($charge_id);
				 
				if(isset($charges_array->standard_charge)){
					 
					$tax=($charges_array->standard_charge*$charges_array->percentage/100);
					$standard_charge=$charges_array->standard_charge;
				}else{
					 
					$tax=0;
					$standard_charge=0;
				}               
				
                $gateway_response['standard_amount'] = $standard_charge; 
                $gateway_response['tax'] = $tax; 
                $gateway_response['appointment_id'] = $appointment_id; 
                $gateway_response['paid_amount']    = $standard_charge+$tax; //$amount;
                $gateway_response['transaction_id'] = $transactionid;
                $gateway_response['charge_id']      = $charge_id;
                $gateway_response['payment_mode']   = 'Stripe';
                $gateway_response['payment_type']   = 'Online';
                $gateway_response['note']           = "Payment deposit through Stripe TXN ID: " . $transactionid;
                $gateway_response['date']           = date("Y-m-d H:i:s");
                $transaction_array = array(                
					
                    "processing_charge_type"    => $charge_type,
                    "gateway_processing_charge" => $gateway_processing_charge,
                    'amount'                    => $standard_charge+$tax, //$amount,
                    'patient_id'                => $this->customlib->getPatientSessionUserID(),
                    'section'                   => $payment_section['appointment'],
                    'type'                      => 'payment',
                    'appointment_id'            => $appointment_id,
                    'payment_mode'              => "Online",
                    'note'                      => "Online fees deposit through Stripe TXN ID: " . $transactionid ,
                    'payment_date'              => date('Y-m-d H:i:s'),
                    'received_by'               => 1,
                );
                $return_detail = $this->onlineappointment_model->paymentSuccess($gateway_response, $transaction_array);
                redirect(base_url("patient/onlineappointment/checkout/successinvoice/" . $appointment_id));
            }
        } elseif ($response->isRedirect()) {
            $response->redirect();
        } else {
            redirect(site_url('patient/onlineappointment/checkout/paymentfailed'));
        }
    }

}

?>