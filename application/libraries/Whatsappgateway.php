<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Whatsappgateway
{
    private $_CI;
    private $message_queue   = 0; // 0 = direct send, 1 = queue mode
    private $whatsapp_detail = null;

    public function __construct()
    {
        $this->_CI           = &get_instance();
        $this->_CI->load->model('MsgQueue_model');
        $this->message_queue = $this->_CI->customlib->checkMessageMode();

        // Load whatsapp config if model exists
        if (file_exists(APPPATH . 'models/Whatsappconfig_model.php')) {
            $this->_CI->load->model('Whatsappconfig_model');
            $this->whatsapp_detail = $this->_CI->Whatsappconfig_model->getActiveWhatsApp();
        }
    }

    /**
     * Queue-aware WhatsApp dispatcher.
     * If queue mode ON  → inserts into message_queue table.
     * If queue mode OFF → sends directly via Twilio or Meta.
     *
     * @param string $notification_type  e.g. 'appointment_approved'
     * @param string $send_to            phone number with country code
     * @param string $msg                message body or template params (JSON array for Meta)
     * @param string $template_id        WhatsApp template id
     * @param int    $priority           1=high, 0=normal
     * @param string $schedule_date      optional datetime
     */
    public function send($notification_type, $send_to, $msg, $template_id, $priority = 0, $schedule_date = '')
    {
        if (empty($send_to)) {
            return false;
        }

        if ($this->message_queue) {
            $data = [
                'notification_type' => $notification_type,
                'schedule_date'     => $schedule_date,
                'sender_details'    => json_encode([
                    'forward_through' => 'whatsapp',
                    'forward_value'   => $send_to,
                    'message'         => $msg,
                    'template_id'     => $template_id,
                ]),
                'priority'          => $priority,
            ];
            return $this->_CI->MsgQueue_model->add($data);
        }

        return $this->sentWhatsappMsg($send_to, $msg, $template_id);
    }

    /**
     * Direct WhatsApp send — used by Cron queue processor.
     */
    public function sentWhatsappMsg($send_to, $msg, $template_id)
    {
        if (empty($this->whatsapp_detail)) {
            return false;
        }

        if ($this->whatsapp_detail->type == 'twilio') {
            return $this->sendByTwilio($this->whatsapp_detail, $send_to, $template_id, $msg);
        } elseif ($this->whatsapp_detail->type == 'meta') {
            return $this->sendMetaTemplate($this->whatsapp_detail, $send_to, $template_id, $this->whatsapp_detail->language, $msg);
        }

        return false;
    }

    /* ===================== META ===================== */
    private function sendMetaTemplate($whatsapp_detail, $send_to, $template_id, $language, $msg)
    {
        $params = [
            'access_token'    => $whatsapp_detail->authkey,
            'phone_number_id' => $whatsapp_detail->contact,
        ];
        $this->_CI->load->library('meta_whatsapp', $params);

        $components = [['type' => 'body', 'parameters' => []]];

        if (is_array($msg)) {
            foreach ($msg as $value) {
                $components[0]['parameters'][] = ['type' => 'text', 'text' => $value];
            }
        } else {
            $components[0]['parameters'][] = ['type' => 'text', 'text' => $msg];
        }

        return $this->_CI->meta_whatsapp->sendTemplate($send_to, $template_id, $language, $components);
    }
    /* ===================== META ===================== */

    /* ===================== TWILIO ===================== */
    private function sendByTwilio($whatsapp_detail, $send_to, $template_id, $msg)
    {
        $params = [
            'username'             => $whatsapp_detail->username,
            'password'             => $whatsapp_detail->password,
            'api_version'          => '2010-04-01',
            'number'               => $whatsapp_detail->contact,
            'whatsapp_template_id' => $template_id,
        ];
        $this->_CI->load->library('twilio_whatsapp', $params);
        $response = $this->_CI->twilio_whatsapp->send($whatsapp_detail->contact, $send_to, $msg);
        return ($response && empty($response->error_message));
    }
    /* ===================== TWILIO ===================== */
}
