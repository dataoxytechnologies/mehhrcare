<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Smsgateway
{

    private $_CI;
    private $message_queue = 0; // 0 = direct send, 1 = queue mode

    public function __construct()
    {
        $this->_CI = &get_instance();
        $this->_CI->load->model('smsconfig_model');
        $this->_CI->load->model('MsgQueue_model');
        $this->message_queue = $this->_CI->customlib->checkMessageMode();
    }

    /**
     * Queue-aware SMS dispatcher.
     * If queue mode is ON → inserts into message_queue table.
     * If queue mode is OFF → sends directly via active SMS gateway.
     */
    public function sendSMS($send_to, $msg, $template_id, $notification_type = '', $priority = 0)
    {
        $sms_detail = $this->_CI->smsconfig_model->getActiveSMS();

        if (empty($sms_detail) || empty($send_to) || empty($msg)) {
            return false;
        }

        if ($this->message_queue) {
            $data = [
                'notification_type' => $notification_type,
                'sender_details'    => json_encode([
                    'forward_through' => 'sms',
                    'forward_value'   => $send_to,
                    'message'         => $msg,
                    'template_id'     => $template_id,
                ]),
                'priority'          => $priority,
                'schedule_date'     => '',
            ];
            return $this->_CI->MsgQueue_model->add($data);
        }

        return $this->send($send_to, $msg, $template_id);
    }

    // Legacy direct send — used by Cron queue processor and direct-mode calls
    public function send($send_to, $msg, $template_id = null)
    {
        $sms_detail = $this->_CI->smsconfig_model->getActiveSMS();

        if (!empty($sms_detail)) {
            if ($sms_detail->type == 'clickatell') {
                $params = array(
                    'apiToken' => $sms_detail->api_id,
                );
                $this->_CI->load->library('clickatell', $params);
                try {
                    $result = $this->_CI->clickatell->sendMessage(['to' => [$send_to], 'content' => $msg]);
                    return !empty($result);
                } catch (Exception $e) {
                    log_message('error', 'Clickatell SMS error: ' . $e->getMessage());
                    return false;
                }
            } else if ($sms_detail->type == 'twilio') {
                $params = array(
                    'mode'        => 'sandbox',
                    'account_sid' => $sms_detail->api_id,
                    'auth_token'  => $sms_detail->password,
                    'api_version' => '2010-04-01',
                    'number'      => $sms_detail->contact,
                );

                $this->_CI->load->library('twilio', $params);
                $from     = $sms_detail->contact;
                $to       = $send_to;
                $message  = $msg;
                $response = $this->_CI->twilio->sms($from, $to, $message);
                return isset($response->IsError) ? !$response->IsError : true;

            } else if ($sms_detail->type == 'msg_nineone') {

                $params = array(
                    'authkey'  => $sms_detail->authkey,
                    'senderid' => $sms_detail->senderid,
                    'templateid' => $template_id
                );

                $this->_CI->load->library('msgnineone', $params);
                $this->_CI->msgnineone->sendSMS($send_to, $msg,$template_id);
            } else if ($sms_detail->type == 'smscountry') {
                $params = array(
                    'username' => $sms_detail->username,
                    'senderid' => $sms_detail->senderid,
                    'password' => $sms_detail->password,
                );
                $this->_CI->load->library('smscountry', $params);
                $this->_CI->smscountry->sendSMS($send_to, $msg,$template_id);
            } else if ($sms_detail->type == 'text_local') {
                $to     = $send_to;
                $params = array(
                    'username' => $sms_detail->username,
                    'hash'     => $sms_detail->password,
                );
                $this->_CI->load->library('textlocalsms', $params);
                $this->_CI->textlocalsms->sendSms(array($to), $msg, $sms_detail->senderid);
            }else if ($sms_detail->type == 'bulk_sms') {
                $to = $send_to;
                $params = array(
                    'username' => $sms_detail->username,
                    'password' => $sms_detail->password,
                );

                $this->_CI->load->library('bulk_sms_lib', $params);
                $this->_CI->bulk_sms_lib->sendSms(array($to), $msg);
            } else if ($sms_detail->type == 'mobireach') {
                $to = $send_to;
                $params = array(
                    'authkey' => $sms_detail->authkey,
                    'senderid' => $sms_detail->senderid,
                    'routeid' => $sms_detail->api_id,
                );
                $this->_CI->load->library('mobireach_lib', $params);
                $this->_CI->mobireach_lib->sendSms(array($to), $msg);

            } else if ($sms_detail->type == 'nexmo') {
                $to = $send_to;
                $params = array(
                    'from' => $sms_detail->senderid,
                    'api_key' => $sms_detail->api_id,
                    'api_secret' => $sms_detail->authkey,
                );
                $this->_CI->load->library('nexmo_lib', $params);
                $this->_CI->nexmo_lib->sendSms($to, $msg);

            } else if ($sms_detail->type == 'africastalking') {
                $to = $send_to;
                $params = array(
                    'from' => $sms_detail->senderid,
                    'api_key' => $sms_detail->api_id,
                    'api_username' => $sms_detail->username,
                );
                $this->_CI->load->library('africastalking_lib', $params);
                $this->_CI->africastalking_lib->sendSms($to, $msg);

            } else if ($sms_detail->type == 'custom') {
                 $params = array(
                    'templateid' => $template_id,

                );
                $this->_CI->load->library('customsms',$params);
                $from    = $sms_detail->contact;
                $to      = $send_to;
                $message = $msg;

                $this->_CI->customsms->sendSMS($to, $message);
            } else {

            }
        }
        return true;
    }

}
