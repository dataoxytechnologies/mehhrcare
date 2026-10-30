<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

require_once(APPPATH . 'third_party/omnipay/vendor/autoload.php');

use Google\Auth\Credentials\ServiceAccountCredentials;
use Google\Auth\HttpHandler\HttpHandlerFactory;

class Pushnotification
{

    public $CI;
    private $message_queue = 0; // 0 = direct send, 1 = queue mode

    //com.qdocs.smarthospital2025
    private $fcmUrl = 'https://fcm.googleapis.com/v1/projects/smarthospitalver6/messages:send';
    private $key_file_path = APPPATH . "third_party/firebase_notification_key.json";
    private $scope = "https://www.googleapis.com/auth/firebase.messaging";
    private $token;

    public function __construct()
    {
        $this->CI           = &get_instance();
        $this->CI->load->model('MsgQueue_model');
        $this->message_queue = $this->CI->customlib->checkMessageMode();
    }

    /**
     * Queue-aware push notification dispatcher.
     * If queue mode ON  → inserts into message_queue table.
     * If queue mode OFF → sends directly via Firebase.
     */
    public function sentNotification($notification_type, $app_key, $subject, $message, $priority = 0)
    {
        if (empty($app_key)) {
            return false;
        }

        if ($this->message_queue) {
            $data = [
                'notification_type' => $notification_type,
                'sender_details'    => json_encode([
                    'forward_through' => 'notification',
                    'forward_value'   => $app_key,
                    'title'           => $subject,
                    'message'         => $message,
                ]),
                'priority'          => $priority,
                'schedule_date'     => '',
            ];
            return $this->CI->MsgQueue_model->add($data);
        }

        $push_array = ['title' => $subject, 'body' => $message];
        return $this->send($app_key, $push_array, 'mail_sms');
    }

    public function send($tokens, $msg, $action = "")
    {
        $creadentials = new ServiceAccountCredentials($this->scope, json_decode(file_get_contents($this->key_file_path), true));
        $this->token = $creadentials->fetchAuthToken(HttpHandlerFactory::build());
        $data = [
            'token' => $tokens,
            'title' => $msg['title'],
            'body' => $msg['body'],
            'action' => $action,
            'sound' => 'mySound',
        ];

        return $this->to($data);
    }

    public function to($data)
    {		
        $headers = [
            'Authorization: Bearer ' . $this->token['access_token'],
            'Content-Type: application/json'
        ];

        $fields = [
            'message' => [
                'token' => $data['token'],
                'data' => [
                    'title' => $data['title'],
                    'body' => $data['body']
                ]
            ]
        ];
 
        $fields = json_encode($fields);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->fcmUrl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);

        $result = curl_exec($ch);
        $errno  = curl_errno($ch);
        $error  = curl_error($ch);
        curl_close($ch);

        if (!$result || $errno) {
            log_message('error', 'Pushnotification cURL failed: ' . $error);
            return false;
        }

        return $result;
    }
}
