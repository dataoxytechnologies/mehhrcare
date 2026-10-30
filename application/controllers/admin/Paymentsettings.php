<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Paymentsettings extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        if (!$this->rbac->hasPrivilege('payment_methods', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'setup');
        $this->session->set_userdata('sub_menu', 'schsettings/index');
        $this->session->set_userdata('inner_menu', 'admin/paymentsettings');
        $data['title']  = $this->lang->line('sms_config_list');
        $payment_result = $this->paymentsetting_model->get();
        $data['statuslist']  = $this->customlib->getStatus();
        $data['paymentlist'] = $payment_result;
        $data['module'] = 'setup';
        $this->load->view('layout/header', $data);
        $this->load->view('admin/payment_setting/paymentsettingList', $data);
        $this->load->view('layout/footer', $data);
    }

    public function paypal()
    {
        $this->form_validation->set_error_delimiters('', '');
        $this->form_validation->set_rules('paypal_username', $this->lang->line('username'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('paypal_password', $this->lang->line('password'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('paypal_signature', $this->lang->line('signature'), 'trim|required|xss_clean');
        if($this->input->post('charge_type', TRUE)!="none" && $this->input->post('charge_type', TRUE)!=""){
           $this->form_validation->set_rules('paypal_charge_value', $this->lang->line('percentage_fix_amount'), 'trim|required|xss_clean|numeric');
        }
        if ($this->form_validation->run()) {
            $data = array(
                'payment_type'  => 'paypal',
                'api_username'  => $this->input->post('paypal_username', TRUE),
                'api_password'  => $this->input->post('paypal_password', TRUE),
                'api_signature' => $this->input->post('paypal_signature', TRUE),
                'paypal_demo'   => 'TRUE',
                'charge_type'   => $this->input->post('charge_type', TRUE),
                'charge_value'  => $this->input->post('paypal_charge_value', TRUE),
            );
            $this->paymentsetting_model->add($data);
            echo json_encode(array('st' => 0, 'msg' => $this->lang->line('update_message')));
        } else {

            $data = array(
                'paypal_username'       => form_error('paypal_username'),
                'paypal_password'       => form_error('paypal_password'),
                'paypal_signature'      => form_error('paypal_signature'),
                'paypal_charge_value'   => form_error('paypal_charge_value'),
            );

            echo json_encode(array('st' => 1, 'msg' => $data));
        }
    }

    public function stripe()
    {
        $this->form_validation->set_error_delimiters('', '');
        $this->form_validation->set_rules('api_secret_key', $this->lang->line('stripe_api_secret_key'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('api_publishable_key', $this->lang->line('stripe_publishable_key'), 'trim|required|xss_clean');
        if($this->input->post('charge_type', TRUE)!="none" && $this->input->post('charge_type', TRUE)!=""){
           $this->form_validation->set_rules('stripe_charge_value', $this->lang->line('percentage_fix_amount'), 'trim|required|xss_clean|numeric');
        }

        if ($this->form_validation->run()) {
            $data = array(
                'api_secret_key'      => $this->input->post('api_secret_key', TRUE),
                'api_publishable_key' => $this->input->post('api_publishable_key', TRUE),
                'payment_type'        => 'stripe',
                'charge_type'         => $this->input->post('charge_type', TRUE),
                'charge_value'        => $this->input->post('stripe_charge_value', TRUE),
            );

            $this->paymentsetting_model->add($data);
            echo json_encode(array('st' => 0, 'msg' => $this->lang->line('update_message')));
        } else {

            $data = array(
                'api_secret_key'      => form_error('api_secret_key'),
                'api_publishable_key' => form_error('api_publishable_key'),
                'stripe_charge_value'    => form_error('stripe_charge_value'),
            );
            echo json_encode(array('st' => 1, 'msg' => $data));
        }
    }

    public function payu()
    {
        $this->form_validation->set_error_delimiters('', '');
        $this->form_validation->set_rules('key', $this->lang->line('payu_money_key'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('salt', $this->lang->line('payu_money_salt'), 'trim|required|xss_clean');
        
        if($this->input->post('charge_type', TRUE)!="none" && $this->input->post('charge_type', TRUE)!=""){
           $this->form_validation->set_rules('payu_charge_value', $this->lang->line('percentage_fix_amount'), 'trim|required|xss_clean|numeric');
        }

        if ($this->form_validation->run()) {
            $data = array(
                'api_secret_key' => $this->input->post('key', TRUE),
                'salt'           => $this->input->post('salt', TRUE),
                'payment_type'   => 'payu',
                'charge_type'    => $this->input->post('charge_type', TRUE),
                'charge_value'   => $this->input->post('payu_charge_value', TRUE),
            );
            $this->paymentsetting_model->add($data);
            echo json_encode(array('st' => 0, 'msg' => $this->lang->line('update_message')));
        } else {
            $data = array(
                'key'  => form_error('key'),
                'salt' => form_error('salt'),
                'payu_charge_value' => form_error('payu_charge_value'),
            );
            echo json_encode(array('st' => 1, 'msg' => $data));
        }
    }    

    public function paytm()
    {
        $this->form_validation->set_error_delimiters('', '');
        $this->form_validation->set_rules('paytm_merchantid', $this->lang->line('merchant_id'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('paytm_merchantkey', $this->lang->line('merchant_key'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('paytm_website', $this->lang->line('website'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('paytm_industrytype', $this->lang->line('industry_type'), 'trim|required|xss_clean');

        if($this->input->post('charge_type', TRUE)!="none" && $this->input->post('charge_type', TRUE)!=""){
           $this->form_validation->set_rules('paytm_charge_value', $this->lang->line('percentage_fix_amount'), 'trim|required|xss_clean|numeric');
        }

        if ($this->form_validation->run()) {
            $data = array(
                'api_secret_key'      => $this->input->post('paytm_merchantkey', TRUE),
                'api_publishable_key' => $this->input->post('paytm_merchantid', TRUE),
                'paytm_website'       => $this->input->post('paytm_website', TRUE),
                'paytm_industrytype'  => $this->input->post('paytm_industrytype', TRUE),
                'payment_type'        => 'paytm',
                'charge_type'         => $this->input->post('charge_type', TRUE),
                'charge_value'        => $this->input->post('paytm_charge_value', TRUE),
            );

            $this->paymentsetting_model->add($data);
            echo json_encode(array('st' => 0, 'msg' => $this->lang->line('update_message')));

        } else {
            $data = array(
                'paytm_merchantkey'  => form_error('paytm_merchantkey'),
                'paytm_merchantid'   => form_error('paytm_merchantid'),
                'paytm_website'      => form_error('paytm_website'),
                'paytm_industrytype' => form_error('paytm_industrytype'),
                'paytm_charge_value' => form_error('paytm_charge_value'),

            );
            echo json_encode(array('st' => 1, 'msg' => $data));
        }
    }
	
    public function midtrans()
    {
        $this->form_validation->set_error_delimiters('', '');
        $this->form_validation->set_rules('midtrans_serverkey', $this->lang->line('server_key'), 'trim|required|xss_clean');
        if($this->input->post('charge_type', TRUE)!="none" && $this->input->post('charge_type', TRUE)!=""){
           $this->form_validation->set_rules('midtrans_charge_value',$this->lang->line('percentage_fix_amount'), 'trim|required|xss_clean|numeric');
        }

        if ($this->form_validation->run()) {
            $data = array(
                'api_secret_key'        => $this->input->post('midtrans_serverkey', TRUE),
                'payment_type'          => 'midtrans',
                'charge_type'           => $this->input->post('charge_type', TRUE),
                'charge_value'          => $this->input->post('midtrans_charge_value', TRUE),
            );
            $this->paymentsetting_model->add($data);
            echo json_encode(array('st' => 0, 'msg' => $this->lang->line('update_message')));
        } else {
            $data = array(
                'midtrans_serverkey'       => form_error('midtrans_serverkey'),
                'midtrans_charge_value'    => form_error('midtrans_charge_value'),

            );
            echo json_encode(array('st' => 1, 'msg' => $data));
        }
    }

    public function setting()
    {
        $this->form_validation->set_error_delimiters('', '');
        $this->form_validation->set_rules(
            'payment_setting', $this->lang->line('payment_setting'), array(
                'required',
                array('paymentsetting', array($this->paymentsetting_model, 'valid_paymentsetting')),
            )
        );
        if ($this->form_validation->run()) {
            $paymentsetting = $this->input->post('payment_setting', TRUE);
            $other          = false;
            if ($paymentsetting == "none") {
                $other = true;
                $data  = array(
                    'is_active' => 'no',
                );
            } else {
                $data = array(
                    'payment_type' => $this->input->post('payment_setting', TRUE),
                    'is_active'    => 'yes',
                );
            }
            $this->paymentsetting_model->active($data, $other);
            echo json_encode(array('st' => 0, 'msg' => $this->lang->line('update_message')));
        } else {
            $data = array(
                'payment_setting' => form_error('payment_setting'),
            );
            echo json_encode(array('st' => 1, 'msg' => $data));
        }
    }

    public function paystack()
    {
        $this->form_validation->set_error_delimiters('', '');
        $this->form_validation->set_rules('paystack_secretkey', $this->lang->line('paystack_secret_key'), 'trim|required|xss_clean');
       
        if($this->input->post('charge_type', TRUE)!="none" && $this->input->post('charge_type', TRUE)!=""){
           $this->form_validation->set_rules('paystack_charge_value',$this->lang->line('percentage_fix_amount'), 'trim|required|xss_clean|numeric');
        }
        if ($this->form_validation->run()) {
            $data = array(
                'api_secret_key' => $this->input->post('paystack_secretkey', TRUE),
                'payment_type'   => 'paystack',
                'charge_type'    => $this->input->post('charge_type', TRUE),
                'charge_value'   => $this->input->post('paystack_charge_value', TRUE),
            );
            $this->paymentsetting_model->add($data);
            echo json_encode(array('st' => 0, 'msg' => $this->lang->line('update_message')));
        } else {
            $data = array(
                'paystack_secretkey' => form_error('paystack_secretkey'),
                'paystack_charge_value' => form_error('paystack_charge_value'),
            );
            echo json_encode(array('st' => 1, 'msg' => $data));
        }
    }

    public function instamojo()
    {
        $this->form_validation->set_error_delimiters('', '');
        $this->form_validation->set_rules('instamojo_apikey', $this->lang->line('private_api_key'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('instamojo_authtoken', $this->lang->line('private_auth_token'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('instamojo_salt', $this->lang->line('private_salt'), 'trim|required|xss_clean');

        
        if($this->input->post('charge_type', TRUE)!="none" && $this->input->post('charge_type', TRUE)!=""){
           $this->form_validation->set_rules('instamojo_charge_value',$this->lang->line('percentage_fix_amount'), 'trim|required|xss_clean|numeric');
        }

        if ($this->form_validation->run()) {
            $data = array(
                'api_secret_key'      => $this->input->post('instamojo_apikey', TRUE),
                'api_publishable_key' => $this->input->post('instamojo_authtoken', TRUE),
                'salt'                => $this->input->post('instamojo_salt', TRUE),
                'payment_type'        => 'instamojo',
                'charge_type'         => $this->input->post('charge_type', TRUE),
                'charge_value'        => $this->input->post('instamojo_charge_value', TRUE),
            );

            $this->paymentsetting_model->add($data);
            echo json_encode(array('st' => 0, 'msg' => $this->lang->line('update_message')));
        } else {
            $data = array(
                'instamojo_apikey'    => form_error('instamojo_apikey'),
                'instamojo_authtoken' => form_error('instamojo_authtoken'),
                'instamojo_salt'      => form_error('instamojo_salt'),
                'instamojo_charge_value'    => form_error('instamojo_charge_value'),
            );
            echo json_encode(array('st' => 1, 'msg' => $data));
        }
    }

    public function razorpay()
    {
        $this->form_validation->set_error_delimiters('', '');
        $this->form_validation->set_rules('razorpay_keyid', $this->lang->line('razorpay_key_id'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('razorpay_secretkey', $this->lang->line('razorpay_secret_key'), 'trim|required|xss_clean');
        
        if($this->input->post('charge_type', TRUE)!="none" && $this->input->post('charge_type', TRUE)!=""){
           $this->form_validation->set_rules('razorpay_charge_value',$this->lang->line('percentage_fix_amount'), 'trim|required|xss_clean|numeric');
        }

        if ($this->form_validation->run()) {

            $data = array(
                'api_secret_key'      => $this->input->post('razorpay_secretkey', TRUE),
                'api_publishable_key' => $this->input->post('razorpay_keyid', TRUE),
                'payment_type'        => 'razorpay',
                'charge_type'         => $this->input->post('charge_type', TRUE),
                'charge_value'        => $this->input->post('razorpay_charge_value', TRUE),
            );

            $this->paymentsetting_model->add($data);
            echo json_encode(array('st' => 0, 'msg' => $this->lang->line('update_message')));

        } else {

            $data = array(
                'razorpay_keyid'        => form_error('razorpay_keyid'),
                'razorpay_secretkey'    => form_error('razorpay_secretkey'),
                'razorpay_charge_value' => form_error('razorpay_charge_value'),
            );
            echo json_encode(array('st' => 1, 'msg' => $data));
        }
    }

    public function pesapal()
    {
        $this->form_validation->set_error_delimiters('', '');
        $this->form_validation->set_rules('pesapal_consumer_key', $this->lang->line('consumer_key'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('pesapal_consumer_secret', $this->lang->line('consumer_secret'), 'trim|required|xss_clean');
        if($this->input->post('charge_type', TRUE)!="none" && $this->input->post('charge_type', TRUE)!=""){
           $this->form_validation->set_rules('pesapal_charge_value',$this->lang->line('percentage_fix_amount'), 'trim|required|xss_clean|numeric');
        }

        if ($this->form_validation->run()) {
            $data = array(
                'api_secret_key'      => $this->input->post('pesapal_consumer_secret', TRUE),
                'api_publishable_key' => $this->input->post('pesapal_consumer_key', TRUE),
                'payment_type'        => 'pesapal',
                'charge_type'         => $this->input->post('charge_type', TRUE),
                'charge_value'        => $this->input->post('pesapal_charge_value', TRUE),
            );

            $this->paymentsetting_model->add($data);
            echo json_encode(array('st' => 0, 'msg' => $this->lang->line('update_message')));
        } else {

            $data = array(
                'pesapal_consumer_key'    => form_error('pesapal_consumer_key'),
                'pesapal_consumer_secret' => form_error('pesapal_consumer_secret'),
                'pesapal_charge_value' => form_error('pesapal_charge_value'),
            );

            echo json_encode(array('st' => 1, 'msg' => $data));
        }
    }

    public function ipayafrica()
    {
        $this->form_validation->set_error_delimiters('', '');
        $this->form_validation->set_rules('ipayafrica_vendorid', $this->lang->line('vendor_id'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('ipayafrica_hashkey', $this->lang->line('hashkey'), 'trim|required|xss_clean');
        if($this->input->post('charge_type', TRUE)!="none" && $this->input->post('charge_type', TRUE)!=""){
           $this->form_validation->set_rules('ipayafrica_charge_value',$this->lang->line('percentage_fix_amount'), 'trim|required|xss_clean|numeric');
        }

        if ($this->form_validation->run()) {
            $data = array(
                'api_secret_key'      => $this->input->post('ipayafrica_hashkey', TRUE),
                'api_publishable_key' => $this->input->post('ipayafrica_vendorid', TRUE),
                'payment_type'        => 'ipayafrica',
                'charge_type'         => $this->input->post('charge_type', TRUE),
                'charge_value'        => $this->input->post('ipayafrica_charge_value', TRUE),
            );

            $this->paymentsetting_model->add($data);
            echo json_encode(array('st' => 0, 'msg' => $this->lang->line('update_message')));
        } else {
            $data = array(
                'ipayafrica_vendorid' => form_error('ipayafrica_vendorid'),
                'ipayafrica_hashkey'  => form_error('ipayafrica_hashkey'),
                'ipayafrica_charge_value'  => form_error('ipayafrica_charge_value'),
            );
			
            echo json_encode(array('st' => 1, 'msg' => $data));
        }
    }

    public function billplz()
    {
        $this->form_validation->set_error_delimiters('', '');
        $this->form_validation->set_rules('billplz_api_key', $this->lang->line('api_key'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('billplz_customer_service_email', $this->lang->line('customer_service_email'), 'trim|required|xss_clean');

        if($this->input->post('charge_type', TRUE)!="none" && $this->input->post('charge_type', TRUE)!=""){
           $this->form_validation->set_rules('billplz_charge_value',$this->lang->line('percentage_fix_amount'), 'trim|required|xss_clean|numeric');
        }

        if ($this->form_validation->run()) {

            $data = array(
                'api_secret_key' => $this->input->post('billplz_api_key', TRUE),
                'api_email'      => $this->input->post('billplz_customer_service_email', TRUE),
                'payment_type'   => 'billplz',
                'charge_type'    => $this->input->post('charge_type', TRUE),
                'charge_value'   => $this->input->post('billplz_charge_value', TRUE),
            );

            $this->paymentsetting_model->add($data);
            echo json_encode(array('st' => 0, 'msg' => $this->lang->line('update_message')));
        } else {
            $data = array(
                'billplz_api_key'                => form_error('billplz_api_key'),
                'billplz_customer_service_email' => form_error('billplz_customer_service_email'),
                'billplz_charge_value' => form_error('billplz_charge_value'),
            );

            echo json_encode(array('st' => 1, 'msg' => $data));
        }
    }

    public function jazzcash()
    {
        $this->form_validation->set_error_delimiters('', '');
        $this->form_validation->set_rules('jazzcash_pp_MerchantID', $this->lang->line('merchant_id'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('jazzcash_pp_Password', $this->lang->line('password'), 'trim|required|xss_clean');

        if($this->input->post('charge_type', TRUE)!="none" && $this->input->post('charge_type', TRUE)!=""){
           $this->form_validation->set_rules('jazzcash_charge_value',$this->lang->line('percentage_fix_amount'), 'trim|required|xss_clean|numeric');
        }

        if ($this->form_validation->run()) {

            $data = array(
                'api_secret_key' => $this->input->post('jazzcash_pp_MerchantID', TRUE),
                'api_password'   => $this->input->post('jazzcash_pp_Password', TRUE),
                'payment_type'   => 'jazzcash',
                'charge_type'    => $this->input->post('charge_type', TRUE),
                'charge_value'   => $this->input->post('jazzcash_charge_value', TRUE),
            );

            $this->paymentsetting_model->add($data);
            echo json_encode(array('st' => 0, 'msg' => $this->lang->line('update_message')));
        } else {

            $data = array(
                'jazzcash_pp_MerchantID'  => form_error('jazzcash_pp_MerchantID'),
                'jazzcash_pp_Password'    => form_error('jazzcash_pp_Password'),
                'jazzcash_charge_value'   => form_error('jazzcash_charge_value'),
            );
            echo json_encode(array('st' => 1, 'msg' => $data));
        }
    }

    /**
     * this function is used to add ccavenue credential
     */
    public function ccavenue()
    {
        $this->form_validation->set_error_delimiters('', '');
        $this->form_validation->set_rules('ccavenue_secret', $this->lang->line('merchant_id'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('ccavenue_salt', $this->lang->line('working_key'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('ccavenue_api_publishable_key', $this->lang->line('access_code'), 'trim|required|xss_clean');

        if($this->input->post('charge_type', TRUE)!="none"){
           $this->form_validation->set_rules('ccavenue_charge_value', $this->lang->line('percentage_fix_amount'), 'trim|required|xss_clean|numeric');
        }

        if ($this->form_validation->run()) {
            $data = array(
                'api_secret_key'      => $this->input->post('ccavenue_secret', TRUE),
                'salt'                => $this->input->post('ccavenue_salt', TRUE),
                'api_publishable_key' => $this->input->post('ccavenue_api_publishable_key', TRUE),
                'payment_type'        => 'ccavenue',
                'charge_type'         => $this->input->post('charge_type', TRUE),
                'charge_value'        => $this->input->post('ccavenue_charge_value', TRUE),
            );
            $this->paymentsetting_model->add($data);
            echo json_encode(array('st' => 0, 'msg' => $this->lang->line('update_message')));
        } else {
            $data = array(
                'ccavenue_secret'              => form_error('ccavenue_secret'),
                'ccavenue_salt'                => form_error('ccavenue_salt'),
                'ccavenue_api_publishable_key' => form_error('ccavenue_api_publishable_key'),
                'ccavenue_charge_value'        => form_error('ccavenue_charge_value'),
            );
            echo json_encode(array('st' => 1, 'msg' => $data));
        }
    }

    public function flutterwave()
    {
        $this->form_validation->set_error_delimiters('', '');
        $this->form_validation->set_rules('public_key', $this->lang->line('public_key'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('secret_key', $this->lang->line('secret_key'), 'trim|required|xss_clean');
        
        if($this->input->post('charge_type', TRUE)!="none" && $this->input->post('charge_type', TRUE)!=""){
           $this->form_validation->set_rules('flutterwave_charge_value', $this->lang->line('percentage_fix_amount'), 'trim|required|xss_clean|numeric');
        }

        if ($this->form_validation->run()) {
            $data = array(
                'api_publishable_key' => $this->input->post('public_key', TRUE),
                'api_secret_key'      => $this->input->post('secret_key', TRUE),
                'payment_type'        => 'flutterwave',
                'charge_type'         => $this->input->post('charge_type', TRUE),
                'charge_value'        => $this->input->post('flutterwave_charge_value', TRUE),
            );
            $this->paymentsetting_model->add($data);
            echo json_encode(array('st' => 0, 'msg' => $this->lang->line('update_message')));
        } else {

            $data = array(
                'public_key' => form_error('public_key'),
                'secret_key' => form_error('secret_key'),
                'flutterwave_charge_value' => form_error('flutterwave_charge_value'),
            );

            echo json_encode(array('st' => 1, 'msg' => $data));
        }
    }

    public function sslcommerz()
    {
        $this->form_validation->set_error_delimiters('', '');
        $this->form_validation->set_rules('sslcommerz_api_key', $this->lang->line('store_id'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('sslcommerz_store_password', $this->lang->line('sslcommerz_store_password'), 'trim|required|xss_clean');
        if($this->input->post('charge_type', TRUE)!="none" && $this->input->post('charge_type', TRUE)!=""){
           $this->form_validation->set_rules('sslcommerz_charge_value',$this->lang->line('percentage_fix_amount'), 'trim|required|xss_clean|numeric');
        }

        if ($this->form_validation->run()) {

            $data = array(
                'api_password'        => $this->input->post('sslcommerz_store_password', TRUE),
                'api_publishable_key' => $this->input->post('sslcommerz_api_key', TRUE),
                'payment_type'        => 'sslcommerz',
                'charge_type'         => $this->input->post('charge_type', TRUE),
                'charge_value'        => $this->input->post('sslcommerz_charge_value', TRUE),
            );

            $this->paymentsetting_model->add($data);
            echo json_encode(array('st' => 0, 'msg' => $this->lang->line('update_message')));

        } else {

            $data = array(
                'sslcommerz_store_password' => form_error('sslcommerz_store_password'),
                'sslcommerz_api_key'        => form_error('sslcommerz_api_key'),
                'sslcommerz_charge_value'   => form_error('sslcommerz_charge_value'),
            );

            echo json_encode(array('st' => 1, 'msg' => $data));
        }
    }

    public function walkingm()
    {
        $this->form_validation->set_error_delimiters('', '');
        $this->form_validation->set_rules('walkingm_client_id', $this->lang->line('client_id'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('walkingm_client_secret', $this->lang->line('client_secret'), 'trim|required|xss_clean');
        if($this->input->post('charge_type', TRUE)!="none" && $this->input->post('charge_type', TRUE)!=""){
           $this->form_validation->set_rules('walkingm_charge_value',$this->lang->line('percentage_fix_amount'), 'trim|required|xss_clean|numeric');
        }

        if ($this->form_validation->run()) {
            $data = array(
                'api_publishable_key' => $this->input->post('walkingm_client_id', TRUE),
                'api_secret_key'      => $this->input->post('walkingm_client_secret', TRUE),
                'payment_type'        => 'walkingm',
                'charge_type'         => $this->input->post('charge_type', TRUE),
                'charge_value'        => $this->input->post('walkingm_charge_value', TRUE),
            );
            $this->paymentsetting_model->add($data);
            echo json_encode(array('st' => 0, 'msg' => $this->lang->line('update_message')));
        } else {
            $data = array(
                'walkingm_client_id'     => form_error('walkingm_client_id'),
                'walkingm_client_secret' => form_error('walkingm_client_secret'),
                'walkingm_charge_value' => form_error('walkingm_charge_value'),
            );

            echo json_encode(array('st' => 1, 'msg' => $data));
        }
    }

    public function mollie() {
        
        $this->form_validation->set_error_delimiters('', '');
        $this->form_validation->set_rules('mollie_api_key', $this->lang->line('api_key'), 'trim|required|xss_clean');
        if($this->input->post('charge_type', TRUE)!="none" && $this->input->post('charge_type', TRUE)!=""){
           $this->form_validation->set_rules('mollie_charge_value', $this->lang->line('percentage_fix_amount'), 'trim|required|xss_clean|numeric');
        }

        if ($this->form_validation->run()) {
        $data = array(
             'api_publishable_key' => $this->input->post('mollie_api_key', TRUE),
             'payment_type' => 'mollie',
             'charge_type'         => $this->input->post('charge_type', TRUE),
             'charge_value'        => $this->input->post('mollie_charge_value', TRUE),
        );
            $this->paymentsetting_model->add($data);
            echo json_encode(array('st' => 0, 'msg' => $this->lang->line('update_message')));
        } else {
            $data = array(
                'mollie_api_key' => form_error('mollie_api_key'),
                'mollie_charge_value' => form_error('mollie_charge_value'),
            );
            echo json_encode(array('st' => 1, 'msg' => $data));
        }
    }

     public function cashfree() {
        
        $this->form_validation->set_error_delimiters('', '');
        $this->form_validation->set_rules('cashfree_app_id', $this->lang->line('app_id'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('cashfree_secret_key', $this->lang->line('secret_key'), 'trim|required|xss_clean');
        
        if($this->input->post('charge_type', TRUE)!="none" && $this->input->post('charge_type', TRUE)!=""){
           $this->form_validation->set_rules('cashfree_charge_value',$this->lang->line('percentage_fix_amount'), 'trim|required|xss_clean|numeric');
        }

        if ($this->form_validation->run()) {
            $data = array(
                'api_publishable_key'   => $this->input->post('cashfree_app_id', TRUE),
                'api_secret_key'        => $this->input->post('cashfree_secret_key', TRUE),
                'payment_type'          => 'cashfree',
                'charge_type'           => $this->input->post('charge_type', TRUE),
                'charge_value'          => $this->input->post('cashfree_charge_value', TRUE),
            );
            $this->paymentsetting_model->add($data);
            echo json_encode(array('st' => 0, 'msg' => $this->lang->line('update_message')));
        } else {
            $data = array(
                'cashfree_app_id'       => form_error('cashfree_app_id'),
                'cashfree_secret_key'   => form_error('cashfree_secret_key'),
                'cashfree_charge_value' => form_error('cashfree_charge_value'),
            );

            echo json_encode(array('st' => 1, 'msg' => $data));
        }
    }

     public function payfast() {

        $this->form_validation->set_error_delimiters('', '');
        $this->form_validation->set_rules('payfast_api_publishable_key', $this->lang->line('merchant_id'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('payfast_api_secret_key', $this->lang->line('merchant_key'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('payfast_salt', $this->lang->line('security_passphrase'), 'trim|required|xss_clean');
       
        if($this->input->post('charge_type', TRUE)!="none" && $this->input->post('charge_type', TRUE)!=""){
           $this->form_validation->set_rules('payfast_charge_value', $this->lang->line('percentage_fix_amount'), 'trim|required|xss_clean|numeric');
        }

        if ($this->form_validation->run()) {
            $data = array(
                'api_publishable_key'   => $this->input->post('payfast_api_publishable_key', TRUE),
                'api_secret_key'        => $this->input->post('payfast_api_secret_key', TRUE),
                'salt'                  => $this->input->post('payfast_salt', TRUE),
                'payment_type'          => 'payfast',
                'charge_type'           => $this->input->post('charge_type', TRUE),
                'charge_value'          => $this->input->post('payfast_charge_value', TRUE),
            );
            $this->paymentsetting_model->add($data);
            echo json_encode(array('st' => 0, 'msg' => $this->lang->line('update_message')));
        } else {
            $data = array(
                'payfast_api_publishable_key'   => form_error('payfast_api_publishable_key'),
                'payfast_api_secret_key'        => form_error('payfast_api_secret_key'),
                'payfast_salt'                  => form_error('payfast_salt'),
                'payfast_charge_value'        => form_error('payfast_charge_value'),
            );
            echo json_encode(array('st' => 1, 'msg' => $data));
        }
    }

    public function toyyibPay() {

        $this->form_validation->set_error_delimiters('', '');
        $this->form_validation->set_rules('toyyibpay_api_secret_key', $this->lang->line('secret_key'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('toyyibpay_category_code', $this->lang->line('category_code'), 'trim|required|xss_clean');

        if($this->input->post('charge_type', TRUE)!="none" && $this->input->post('charge_type', TRUE)!=""){
           $this->form_validation->set_rules('toyyibpay_charge_value', $this->lang->line('percentage_fix_amount'), 'trim|required|xss_clean|numeric');
        }

        if ($this->form_validation->run()) {
        $data = array(
             'api_secret_key'  =>   $this->input->post('toyyibpay_api_secret_key', TRUE),
             'api_signature'   =>   $this->input->post('toyyibpay_category_code', TRUE),
             'payment_type'    =>   'toyyibpay',
             'charge_type'     =>   $this->input->post('charge_type', TRUE),
             'charge_value'    =>   $this->input->post('toyyibpay_charge_value', TRUE),
        );
            $this->paymentsetting_model->add($data);
            echo json_encode(array('st' => 0, 'msg' => $this->lang->line('update_message')));
        } else {
            $data = array(
                'toyyibpay_api_secret_key' => form_error('toyyibpay_api_secret_key'),
                'toyyibpay_category_code'  => form_error('toyyibpay_category_code'),
                'toyyibpay_charge_value'   => form_error('toyyibpay_charge_value'),
            );
            echo json_encode(array('st' => 1, 'msg' => $data));
        }
    } 

     public function skrill() {
        $this->form_validation->set_error_delimiters('', '');
        $this->form_validation->set_rules('skrill_api_email', $this->lang->line('merchant_account_email'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('skrill_salt', $this->lang->line('merchant_secret_salt'), 'trim|required|xss_clean');
        if($this->input->post('charge_type', TRUE)!="none" && $this->input->post('charge_type', TRUE)!=""){
           $this->form_validation->set_rules('skrill_charge_value',$this->lang->line('percentage_fix_amount'), 'trim|required|xss_clean|numeric');
        }

        if ($this->form_validation->run()) {
        $data = array(
             'api_email' => $this->input->post('skrill_api_email', TRUE),
             'salt' => $this->input->post('skrill_salt', TRUE),
             'payment_type' => 'skrill',
             'charge_type'  => $this->input->post('charge_type', TRUE),
             'charge_value' => $this->input->post('skrill_charge_value', TRUE),
        );

            $this->paymentsetting_model->add($data);
            echo json_encode(array('st' => 0, 'msg' => $this->lang->line('update_message')));

        } else {

            $data = array(
                'skrill_api_email' => form_error('skrill_api_email'),
                'skrill_salt' => form_error('skrill_salt'),
                'skrill_charge_value'       => form_error('skrill_charge_value'),
            );

            echo json_encode(array('st' => 1, 'msg' => $data));
        }
    }

    public function twocheckout() {

        $this->form_validation->set_error_delimiters('', '');
        $this->form_validation->set_rules('twocheckout_api_publishable_key', $this->lang->line('merchant_code'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('twocheckout_api_secret_key', $this->lang->line('secret_key'), 'trim|required|xss_clean');

        if($this->input->post('charge_type', TRUE)!="none" && $this->input->post('charge_type', TRUE)!=""){
           $this->form_validation->set_rules('twocheckout_charge_value',$this->lang->line('percentage_fix_amount'), 'trim|required|xss_clean|numeric');
        }
        if ($this->form_validation->run()) {
            $data = array(
                'api_secret_key'        => $this->input->post('twocheckout_api_secret_key', TRUE),
                'api_publishable_key'   => $this->input->post('twocheckout_api_publishable_key', TRUE),
                'payment_type'          => 'twocheckout',
                'charge_type'           => $this->input->post('charge_type', TRUE),
                'charge_value'          => $this->input->post('twocheckout_charge_value', TRUE),
            );
            $this->paymentsetting_model->add($data);
            echo json_encode(array('st' => 0, 'msg' => $this->lang->line('update_message')));
        } else {
            $data = array(
                'twocheckout_api_secret_key'        => form_error('twocheckout_api_secret_key'),
                'twocheckout_api_publishable_key'   => form_error('twocheckout_api_publishable_key'),
                'twocheckout_charge_value'          => form_error('twocheckout_charge_value'),
            );
            echo json_encode(array('st' => 1, 'msg' => $data));
        }
    }

    public function payhere()
    {
        $this->form_validation->set_error_delimiters('', '');
        $this->form_validation->set_rules('payhere_api_publishable_key', $this->lang->line('merchant_id'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('payhere_api_secret_key', $this->lang->line('merchant_secret'), 'trim|required|xss_clean');
        if($this->input->post('charge_type', TRUE)!="none" && $this->input->post('charge_type', TRUE)!=""){
           $this->form_validation->set_rules('payhere_charge_value',$this->lang->line('percentage_fix_amount'), 'trim|required|xss_clean|numeric');
        }

        if ($this->form_validation->run()) {
            $data = array(
                'api_publishable_key' => $this->input->post('payhere_api_publishable_key', TRUE),
                'api_secret_key'      => $this->input->post('payhere_api_secret_key', TRUE),
                'payment_type'        => 'payhere',
                'charge_type'         => $this->input->post('charge_type', TRUE),
                'charge_value'        => $this->input->post('payhere_charge_value', TRUE),
            );
            $this->paymentsetting_model->add($data);
            echo json_encode(array('st' => 0, 'msg' => $this->lang->line('update_message')));
        } else {
            $data = array(
                'payhere_api_publishable_key' => form_error('payhere_api_publishable_key'),
                'payhere_api_secret_key'      => form_error('payhere_api_secret_key'),
                'payhere_charge_value'        => form_error('payhere_charge_value'),
            );
            echo json_encode(array('st' => 1, 'msg' => $data));
        }
    }

public function onepay() 
{     
        $this->form_validation->set_error_delimiters('', '');
        $this->form_validation->set_rules('onepay_merchant_id', $this->lang->line('onepay_merchant_id'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('onepay_salt', $this->lang->line('access_code'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('onepay_api_signature', $this->lang->line('hash_key'), 'trim|required|xss_clean');
        if($this->input->post('charge_type', TRUE)!="none" && $this->input->post('charge_type', TRUE)!=""){
           $this->form_validation->set_rules('onepay_charge_value', $this->lang->line('percentage_fix_amount'), 'trim|required|xss_clean|numeric');
        }

        if ($this->form_validation->run()) {
            $data = array(
                'api_publishable_key'   => $this->input->post('onepay_merchant_id', TRUE),
                'salt'                  => $this->input->post('onepay_salt', TRUE),
                'api_signature'         => $this->input->post('onepay_api_signature', TRUE),
                'payment_type'          => 'onepay',
                'charge_type'           => $this->input->post('charge_type', TRUE),
                'charge_value'          => $this->input->post('onepay_charge_value', TRUE),
            );
            $this->paymentsetting_model->add($data);
            echo json_encode(array('st' => 0, 'msg' => $this->lang->line('update_message')));
        } else {
            $data = array(
                'onepay_merchant_id'        => form_error('onepay_merchant_id'),
                'onepay_salt'               => form_error('onepay_salt'),
                'onepay_api_signature'      => form_error('onepay_api_signature'),
                'onepay_charge_value'       => form_error('onepay_charge_value'),
            );
            echo json_encode(array('st' => 1, 'msg' => $data));
        }
    }

}
