<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Payhere extends Patient_Controller
{
    public $payment_method = array();
    public $pay_method     = array();
    public $patient_data;
    public $setting;

    public function __construct()
    {
        parent::__construct();
        $this->config->load("payroll");
        $this->pay_method     = $this->paymentsetting_model->getActiveMethod();
        $this->setting        = $this->setting_model->get()[0];
        $this->load->model(array('onlineappointment_model','charge_model','gateway_ins_model'));
    }

    public function index()
    { 
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
        $this->session->set_userdata('gateway_processing_charge', $gateway_processing_charge);
        $this->session->set_userdata('charge_type', $charge_type);
        //processing fees added
        
        $this->load->view('patient/onlineappointment/payhere/index', $data);
    }

    public function pay()
    {
        $appointment_id  = $this->session->userdata('appointment_id');
        $buyer_data      = $this->onlineappointment_model->getAppointmentDetails($appointment_id);
        $payment_amount             = $this->session->userdata('payment_amount'); 
        $amount                     = $this->session->userdata('payment_amount_including_processing_fee'); 
        $charge_type                = $this->session->userdata('charge_type'); 
        $gateway_processing_charge  = $this->session->userdata('gateway_processing_charge'); 

        $htmlform=array(
            'merchant_id'=>$this->pay_method->api_publishable_key,
            'return_url'=>base_url().'patient/onlineappointment/payhere/success',
            'cancel_url'=>base_url().'patient/onlineappointment/payhere/cancel',
            'notify_url'=>base_url().'gateway_ins/payhere',
            'order_id'=>time().rand(99,999),
            'items'=>'Patient Fees',
            'currency'=>$this->setting['currency'],
            'amount'=>$amount,
            'first_name'=>$buyer_data->name,
            'last_name'=>'',
            'email'=>$buyer_data->email,
            'phone'=>'',
            'address'=>'',
            'city'=>'',
            'country'=>''
        );

        $data['htmlform']=$htmlform;

        $data['params']['transaction_id']=$htmlform['order_id'];

        $ins_data=array(
            'unique_id'=>$htmlform['order_id'],
            'parameter_details'=>json_encode($htmlform),
            'gateway_name'=>'payhere',
            'type'=>'appointment',
            'online_appointment_id'=>$appointment_id,
            'module_type'=>'appointment',
            'payment_status'=>'processing',
            );
            
            $transactionid = $htmlform['order_id'];
            $payment_section = $this->config->item('payment_section');
            $save_record = array(
                "processing_charge_type"    => $charge_type,
                "gateway_processing_charge" => $gateway_processing_charge,
                'amount'                    => $payment_amount,//charge amount only
                'patient_id'                => $this->customlib->getPatientSessionUserID(),
                'section'                   => $payment_section['appointment'],
                'type'                      => 'payment',
                'appointment_id'            => $appointment_id,
                'payment_mode'              => "Online",
                'note'                      => "Online fees deposit through Payhere TXN ID: " . $transactionid ,
                'payment_date'              => date('Y-m-d H:i:s'),
                'received_by'               => 1,
            );

            $gateway_ins_id=$this->gateway_ins_model->add_gateway_ins($ins_data);
            $save_record["gateway_ins_id"] = $gateway_ins_id;

            $this->gateway_ins_model->add_transactions_processing($save_record);
            $this->load->view('patient/onlineappointment/payhere/pay', $data);
    }


    public function success($payment_type = '')
    {
        $appointment_id   = $this->session->userdata('appointment_id');
        $appointment_data = $this->onlineappointment_model->getAppointmentDetails($appointment_id);
        $patient_data     = $this->session->userdata('patient');
        $patient_id       = $patient_data['patient_id'];
        $charge_id        = $this->session->userdata('charge_id');
        $charge_type      = $this->session->userdata('charge_type'); 
        $gateway_processing_charge  = $this->session->userdata('gateway_processing_charge'); 
        
        if ($appointment_data) {
            $amount         = $this->session->userdata('payment_amount');
            $transactionid  = $_GET['payment_id'];
			
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
                'transaction_id' => $transactionid,
                'payment_type'   => 'Online',
                'payment_mode'   => 'Payhere',
                'note'           => "Payment deposit through Payhere TXN ID: " . $transactionid,
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
                'note'                      => "Payment deposit through Payhere TXN ID: " . $transactionid,

            );

            $return_detail = $this->onlineappointment_model->paymentSuccess($payment_data,$transaction_array);
       
             redirect(base_url("patient/onlineappointment/checkout/successinvoice/" . $return_detail));
        } else {
   
            redirect(base_url("patient/onlineappointment/checkout/paymentfailed"));
        }
    } 
}