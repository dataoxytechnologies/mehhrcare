<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Smsconfig extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        if (!$this->rbac->hasPrivilege('sms_setting', 'can_view')) {
            access_denied();
        }

        $this->session->set_userdata('top_menu', 'setup');
        $this->session->set_userdata('sub_menu', 'schsettings/index');
        $this->session->set_userdata('inner_menu', 'smsconfig/index');
        $data['title']      = 'SMS Config List';
        $sms_result         = $this->smsconfig_model->get();
        $data['statuslist'] = $this->customlib->getStatus();
        $data['smslist']    = $sms_result;
        $this->load->view('layout/header', $data);
        $this->load->view('smsconfig/smsList', $data);
        $this->load->view('layout/footer', $data);
    }

    public function clickatell()
    {
        $this->form_validation->set_error_delimiters('', '');
        $this->form_validation->set_rules('clickatell_user', $this->lang->line('username'), 'required|xss_clean');
        $this->form_validation->set_rules('clickatell_password', $this->lang->line('password'), 'required|xss_clean');
        $this->form_validation->set_rules('clickatell_api_id', $this->lang->line('clickatell_api_key'), 'required|xss_clean');
        if ($this->form_validation->run()) {

            $data = array(
                'type'      => 'clickatell',
                'username'  => $this->input->post('clickatell_user', TRUE),
                'password'  => $this->input->post('clickatell_password', TRUE),
                'api_id'    => $this->input->post('clickatell_api_id', TRUE),
                'is_active' => $this->input->post('clickatell_status', TRUE),
            );
            $this->smsconfig_model->add($data);
            echo json_encode(array('st' => 0, 'msg' => $this->lang->line('update_message')));
        } else {

            $data = array(
                'clickatell_user'     => form_error('clickatell_user'),
                'clickatell_password' => form_error('clickatell_password'),
                'clickatell_api_id'   => form_error('clickatell_api_id'),
            );

            echo json_encode(array('st' => 1, 'msg' => $data));
        }
    }

    public function twilio()
    {
        $this->form_validation->set_error_delimiters('', '');
        $this->form_validation->set_rules('twilio_account_sid', $this->lang->line('twilio_account_sid'), 'required|xss_clean');
        $this->form_validation->set_rules('twilio_auth_token', $this->lang->line('authentication_token'), 'required|xss_clean');
        $this->form_validation->set_rules('twilio_sender_phone_number', $this->lang->line('registered_phone_number'), 'required|xss_clean');

        if ($this->form_validation->run()) {
            $data = array(
                'type'      => 'twilio',
                'api_id'    => $this->input->post('twilio_account_sid', TRUE),
                'password'  => $this->input->post('twilio_auth_token', TRUE),
                'contact'   => $this->input->post('twilio_sender_phone_number', TRUE),
                'is_active' => $this->input->post('twilio_status', TRUE),
            );
            $this->smsconfig_model->add($data);
            echo json_encode(array('st' => 0, 'msg' => $this->lang->line('update_message')));
        } else {
            $data = array(
                'twilio_account_sid'         => form_error('twilio_account_sid'),
                'twilio_auth_token'          => form_error('twilio_auth_token'),
                'twilio_sender_phone_number' => form_error('twilio_sender_phone_number'),
            );

            echo json_encode(array('st' => 1, 'msg' => $data));
        }
    }

    public function custom()
    {
        $this->form_validation->set_error_delimiters('', '');
        $this->form_validation->set_rules('name', $this->lang->line('name'), 'required|xss_clean');
        if ($this->form_validation->run()) {
            $data = array(
                'type'      => 'custom',
                'name'      => $this->input->post('name', TRUE),
                'is_active' => $this->input->post('custom_status', TRUE),
            );
            $this->smsconfig_model->add($data);
            echo json_encode(array('st' => 0, 'msg' => $this->lang->line('update_message')));
        } else {

            $data = array(
                'name' => form_error('name'),
            );

            echo json_encode(array('st' => 1, 'msg' => $data));
        }
    }

    public function msgnineone()
    {
        $this->form_validation->set_error_delimiters('', '');
        $this->form_validation->set_rules('authkey', $this->lang->line('auth_key'), 'required|xss_clean');
        $this->form_validation->set_rules('senderid', $this->lang->line('sender_id'), 'required|xss_clean');
        if ($this->form_validation->run()) {
            $data = array(
                'type'      => 'msg_nineone',
                'authkey'   => $this->input->post('authkey', TRUE),
                'senderid'  => $this->input->post('senderid', TRUE),
                'is_active' => $this->input->post('msg_nineone_status', TRUE),
            );
            $this->smsconfig_model->add($data);
            echo json_encode(array('st' => 0, 'msg' => $this->lang->line('update_message')));
        } else {
            $data = array(
                'authkey'  => form_error('authkey'),
                'senderid' => form_error('senderid'),
            );

            echo json_encode(array('st' => 1, 'msg' => $data));
        }
    }

    public function smscountry()
    {
        $this->form_validation->set_error_delimiters('', '');
        $this->form_validation->set_rules('smscountry', $this->lang->line('username'), 'required|xss_clean');
        $this->form_validation->set_rules('smscountrypassword', $this->lang->line('password'), 'required|xss_clean');
        $this->form_validation->set_rules('smscountrysenderid', $this->lang->line('sender_id'), 'required|xss_clean');
        if ($this->form_validation->run()) {
            $data = array(
                'type'      => 'smscountry',
                'username'  => $this->input->post('smscountry', TRUE),
                'password'  => $this->input->post('smscountrypassword', TRUE),
                'senderid'  => $this->input->post('smscountrysenderid', TRUE),
                'is_active' => $this->input->post('smscountry_status', TRUE),
            );
            $this->smsconfig_model->add($data);
            echo json_encode(array('st' => 0, 'msg' => $this->lang->line('update_message')));
        } else {

            $data = array(
                'smscountry'         => form_error('smscountry'),
                'smscountrypassword' => form_error('smscountrypassword'),
                'smscountrysenderid' => form_error('smscountrysenderid'),
            );

            echo json_encode(array('st' => 1, 'msg' => $data));
        }
    }

    public function textlocal()
    {
        $this->form_validation->set_error_delimiters('', '');
        $this->form_validation->set_rules('text_local', $this->lang->line('username'), 'required|xss_clean');
        $this->form_validation->set_rules('text_localpassword', $this->lang->line('password'), 'required|xss_clean');
        $this->form_validation->set_rules('text_localsenderid', $this->lang->line('sender_id'), 'required|xss_clean');
        if ($this->form_validation->run()) {
            $data = array(
                'type'      => 'text_local',
                'username'  => $this->input->post('text_local', TRUE),
                'password'  => $this->input->post('text_localpassword', TRUE),
                'senderid'  => $this->input->post('text_localsenderid', TRUE),
                'is_active' => $this->input->post('text_local_status', TRUE),
            );
            $this->smsconfig_model->add($data);
            echo json_encode(array('st' => 0, 'msg' => $this->lang->line('update_message')));
        } else {

            $data = array(
                'text_local'         => form_error('text_local'),
                'text_localpassword' => form_error('text_localpassword'),
                'text_localsenderid' => form_error('text_localsenderid'),
            );

            echo json_encode(array('st' => 1, 'msg' => $data));
        }
    }

    public function bulk_sms()
    {
        $this->form_validation->set_error_delimiters('', '');
        $this->form_validation->set_rules('bulk_sms_user_name', $this->lang->line('username'), 'required|xss_clean');
        $this->form_validation->set_rules('bulk_sms_user_password', $this->lang->line('password'), 'required|xss_clean');
        $this->form_validation->set_rules('bulk_sms_status', $this->lang->line('status'), 'required|xss_clean');

        if ($this->form_validation->run()) {

            $data = array(
                'type'      => 'bulk_sms',
                'username'  => $this->input->post('bulk_sms_user_name', TRUE),
                'password'  => $this->input->post('bulk_sms_user_password', TRUE),
                'is_active' => $this->input->post('bulk_sms_status', TRUE),
            );
            $this->smsconfig_model->add($data);
            echo json_encode(array('st' => 0, 'msg' => $this->lang->line('update_message')));
        } else {

            $data = array(
                'bulk_sms_user_name'     => form_error('bulk_sms_user_name'),
                'bulk_sms_user_password' => form_error('bulk_sms_user_password'),
                'bulk_sms_status'        => form_error('bulk_sms_status'),

            );

            echo json_encode(array('st' => 1, 'msg' => $data));
        }
    }

    public function mobireach()
    {
        $this->form_validation->set_error_delimiters('', '');

        $this->form_validation->set_rules('mobireach_auth_key', $this->lang->line('auth_key'), 'required|xss_clean');
        $this->form_validation->set_rules('mobireach_sender_id', $this->lang->line('sender_id'), 'required|xss_clean');
        $this->form_validation->set_rules('mobireach_route_id', $this->lang->line('route_id'), 'required|xss_clean');
        $this->form_validation->set_rules('mobireach_status', $this->lang->line('status'), 'required|xss_clean');

        if ($this->form_validation->run()) {

            $data = array(
                'type'      => 'mobireach',
                'authkey'   => $this->input->post('mobireach_auth_key', TRUE),
                'senderid'  => $this->input->post('mobireach_sender_id', TRUE),
                'api_id'    => $this->input->post('mobireach_route_id', TRUE),
                'is_active' => $this->input->post('mobireach_status', TRUE),
            );
            $this->smsconfig_model->add($data);
            echo json_encode(array('st' => 0, 'msg' => $this->lang->line('update_message')));

        } else {

            $data = array(
                'mobireach_auth_key'  => form_error('mobireach_auth_key'),
                'mobireach_sender_id' => form_error('mobireach_sender_id'),
                'mobireach_route_id'  => form_error('mobireach_route_id'),
                'mobireach_status'    => form_error('mobireach_status'),

            );

            echo json_encode(array('st' => 1, 'msg' => $data));
        }
    }

    public function nexmo()
    {
        $this->form_validation->set_error_delimiters('', '');

        $this->form_validation->set_rules('nexmo_api_key', $this->lang->line('nexmo_api_key'), 'required|xss_clean');
        $this->form_validation->set_rules('nexmo_api_secret', $this->lang->line('nexmo_api_secret'), 'required|xss_clean');
        $this->form_validation->set_rules('nexmo_registered_phone_number', $this->lang->line('registered_from_number'), 'required|xss_clean');
        $this->form_validation->set_rules('nexmo_status', $this->lang->line('status'), 'required|xss_clean');

        if ($this->form_validation->run()) {

            $data = array(
                'type'      => 'nexmo',
                'authkey'   => $this->input->post('nexmo_api_secret', TRUE),
                'senderid'  => $this->input->post('nexmo_registered_phone_number', TRUE),
                'api_id'    => $this->input->post('nexmo_api_key', TRUE),
                'is_active' => $this->input->post('nexmo_status', TRUE),
            );
            $this->smsconfig_model->add($data);
            echo json_encode(array('st' => 0, 'msg' => $this->lang->line('update_message')));

        } else {

            $data = array(
                'nexmo_api_secret'              => form_error('nexmo_api_secret'),
                'nexmo_registered_phone_number' => form_error('nexmo_registered_phone_number'),
                'nexmo_api_key'                 => form_error('nexmo_api_key'),
                'nexmo_status'                  => form_error('nexmo_status'),

            );

            echo json_encode(array('st' => 1, 'msg' => $data));
        }
    }

    public function africastalking()
    {

        $this->form_validation->set_error_delimiters('', '');

        $this->form_validation->set_rules('africastalking_username', $this->lang->line('username'), 'required|xss_clean');
        $this->form_validation->set_rules('africastalking_apikey', $this->lang->line('api_key'), 'required|xss_clean');
        $this->form_validation->set_rules('africastalking_short_code', $this->lang->line('short_code'), 'required|xss_clean');
        $this->form_validation->set_rules('africastalking_status', $this->lang->line('status'), 'required|xss_clean');

        if ($this->form_validation->run()) {

            $data = array(
                'type'      => 'africastalking',
                'username'  => $this->input->post('africastalking_username', TRUE),
                'api_id'    => $this->input->post('africastalking_apikey', TRUE),
                'senderid'  => $this->input->post('africastalking_short_code', TRUE),
                'is_active' => $this->input->post('africastalking_status', TRUE),
            );
            $this->smsconfig_model->add($data);
            echo json_encode(array('st' => 0, 'msg' => $this->lang->line('update_message')));

        } else {

            $data = array(
                'africastalking_username'   => form_error('africastalking_username'),
                'africastalking_apikey'     => form_error('africastalking_apikey'),
                'africastalking_short_code' => form_error('africastalking_short_code'),
                'africastalking_status'     => form_error('africastalking_status'),

            );

            echo json_encode(array('st' => 1, 'msg' => $data));
        }
    }

}
