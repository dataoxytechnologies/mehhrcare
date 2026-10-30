<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class MsgQueue_model extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Insert a message into the queue
     *
     * @param array $data {
     *   notification_type  string  e.g. 'opd_patient_registration'
     *   sender_details     string  JSON encoded payload
     *   priority           int     1=high, 0=normal
     *   schedule_date      string  datetime or empty string
     *   message_id         int     optional reference id
     *   send_notification_id int   optional reference id
     * }
     * @return int|false  inserted id or false on failure
     */
    public function add($data)
    {
        $insert = [
            'notification_type'    => isset($data['notification_type'])    ? $data['notification_type']    : '',
            'sender_details'       => isset($data['sender_details'])       ? $data['sender_details']       : '',
            'schedule_date'        => isset($data['schedule_date'])        ? $data['schedule_date']        : null,
            'priority'             => isset($data['priority'])             ? $data['priority']             : 0,
            'message_id'           => isset($data['message_id'])           ? $data['message_id']           : 0,
            'send_notification_id' => isset($data['send_notification_id']) ? $data['send_notification_id'] : 0,
            'status'               => 'pending',
            'attempts'             => 0,
            'created_at'           => date('Y-m-d H:i:s'),
        ];

        $this->db->insert('message_queue', $insert);
        return $this->db->insert_id();
    }
}
