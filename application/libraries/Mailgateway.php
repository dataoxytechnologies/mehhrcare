<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Mailgateway
{
    public  $hospital_setting;
    private $_CI;
    private $message_queue = 0; // 0 = direct send, 1 = queue mode

    public function __construct()
    {
        $this->_CI = &get_instance();
        $this->_CI->load->model('setting_model');
        $this->_CI->load->library('mailer');
        $this->_CI->load->model('MsgQueue_model');
        $this->hospital_setting  = $this->_CI->setting_model->get();
        $this->message_queue     = $this->_CI->customlib->checkMessageMode();
    }

    // -----------------------------------------------------------------------
    // UNIFIED EMAIL SENDER — queue or direct
    // -----------------------------------------------------------------------

    /**
     * @param string $notification_type  e.g. 'opd_patient_registration'
     * @param string $send_to            recipient email address
     * @param string $subject            email subject (already hydrated)
     * @param string $message            email body (already hydrated)
     * @param int    $priority           1=high, 0=normal
     * @param array  $files              optional attachments
     * @param string $cc                 optional CC address
     */
    public function sentMail($notification_type, $send_to, $subject, $message, $priority = 0, $files = [], $cc = '')
    {
        if (empty($this->_CI->mail_config) || empty($send_to)) {
            return false;
        }

        if ($this->message_queue) {
            $data = [
                'notification_type' => $notification_type,
                'sender_details'    => json_encode([
                    'forward_through' => 'email',
                    'forward_value'   => $send_to,
                    'subject'         => $subject,
                    'message'         => $message,
                    'files'           => $files,
                    'cc'              => $cc,
                ]),
                'priority'          => $priority,
                'schedule_date'     => '',
            ];
            return $this->_CI->MsgQueue_model->add($data);
        }

        return $this->_CI->mailer->send_mail($send_to, $subject, $message, $files, $cc);
    }

    // -----------------------------------------------------------------------
    // PUSH NOTIFICATION SENDER — direct only (queue handled in Pushnotification lib)
    // -----------------------------------------------------------------------

    public function sentNotificationDirect($app_key, $subject, $message)
    {
        if (empty($app_key)) {
            return false;
        }
        $push_array = ['title' => $subject, 'body' => $message];
        return $this->_CI->pushnotification->send($app_key, $push_array, 'mail_sms');
    }


}
