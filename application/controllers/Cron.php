<?php

class Cron extends CI_Controller
{
	public $cron_key;


    private $setting_result;
    public function __construct($key = "")
    {
        parent::__construct();
        $setting_result = $this->setting_model->getSetting();
    
        $this->setting_result = $setting_result;
        $this->cron_key       = $setting_result->cron_secret_key;
        $this->set_timezone();
        $this->load->library('mailer');
        $this->load->library('smsgateway');
        $this->load->library('pushnotification');
        if (file_exists(APPPATH . 'libraries/Whatsappgateway.php')) {
            $this->load->library('whatsappgateway');
        }
    }

    public function index($key = "")
    {
        if ($key != "" && $this->cron_key == $key) {
            $this->autobackup($key);
            $this->expMedicineNotification($key);
            $this->outofStockMedicineNotification($key);
            $this->lowStockMedicineNotification($key);
            $this->messageQueue($key);
        } else {
            echo "Invalid Key or Direct access is not allowed";
            return;
        }
    }

    // -------------------------------------------------------------------
    // MESSAGE QUEUE PROCESSOR
    // Picks up to batch_size pending messages, dispatches them, handles retries.
    // -------------------------------------------------------------------
    public function messageQueue($key = '')
    {
        if (($key == '') || ($this->cron_key != $key)) {
            echo "Invalid Key or Direct access is not allowed";
            return;
        }

        $setting = $this->setting_result;
        $batch   = isset($setting->message_queue_batch_size) ? (int) $setting->message_queue_batch_size : 50;

        // Lock rows inside a transaction to prevent duplicate processing
        $this->db->trans_start();
        $messages = $this->db->query(
            "SELECT * FROM message_queue WHERE status='pending' ORDER BY priority DESC, id ASC LIMIT $batch FOR UPDATE"
        )->result();

        if (empty($messages)) {
            $this->db->trans_complete();
            return;
        }

        $ids = array_column($messages, 'id');
        $this->db->where_in('id', $ids)->update('message_queue', [
            'status'     => 'processing',
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        $this->db->trans_complete();

        foreach ($messages as $msg) {
            $this->_processQueueMessage($msg, $setting);
        }
    }

    private function _processQueueMessage($msg, $setting)
    {
        $max_attempts = isset($setting->message_queue_attempts) ? (int) $setting->message_queue_attempts : 3;
        $payload      = json_decode($msg->sender_details, true);
        $sent         = false;

        if (empty($payload['forward_through'])) {
            $this->_deleteQueueMessage($msg->id);
            return;
        }

        switch ($payload['forward_through']) {

            case 'email':
                $files = isset($payload['files']) ? $payload['files'] : [];
                $cc    = isset($payload['cc'])    ? $payload['cc']    : '';
                $sent  = $this->mailer->send_mail(
                    $payload['forward_value'],
                    $payload['subject'],
                    $payload['message'],
                    $files,
                    $cc
                );
                break;

            case 'sms':
                $sent = $this->smsgateway->send(
                    $payload['forward_value'],
                    $payload['message'],
                    isset($payload['template_id']) ? $payload['template_id'] : null
                );
                break;

            case 'notification':
                $push_array = [
                    'title' => isset($payload['title'])   ? $payload['title']   : '',
                    'body'  => isset($payload['message']) ? $payload['message'] : '',
                ];
                $this->pushnotification->send($payload['forward_value'], $push_array, 'notification');
                $sent = true;
                break;

            case 'whatsapp':
                if (isset($this->whatsappgateway)) {
                    $sent = $this->whatsappgateway->sentWhatsappMsg(
                        $payload['forward_value'],
                        $payload['message'],
                        isset($payload['template_id']) ? $payload['template_id'] : null
                    );
                }
                break;
        }

        if ($sent === true) {
            $this->_deleteQueueMessage($msg->id);
            return;
        }

        $new_attempts = (int) $msg->attempts + 1;

        if ($new_attempts >= $max_attempts) {
            // Move to failed table
            $this->db->insert('failed_message_queue', [
                'notification_type' => $msg->notification_type,
                'sender_details'    => $msg->sender_details,
                'schedule_date'     => $msg->schedule_date,
                'priority'          => $msg->priority,
                'status'            => 'failed',
                'sent_at'           => $msg->sent_at,
                'created_at'        => $msg->created_at,
                'updated_at'        => date('Y-m-d H:i:s'),
                'attempts'          => $new_attempts,
                'failed_at'         => date('Y-m-d H:i:s'),
            ]);
            $this->_deleteQueueMessage($msg->id);
        } else {
            // Retry later
            $this->db->where('id', $msg->id)
                ->set('attempts', 'attempts+1', false)
                ->update('message_queue', [
                    'status'     => 'pending',
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
        }
    }

    private function _deleteQueueMessage($id)
    {
        $this->db->where('id', $id)->delete('message_queue');
    }



    public function set_timezone()
    {
     
        if ($this->setting_result->timezone != "") {
            date_default_timezone_set($this->setting_result->timezone);
        } else {
            return date_default_timezone_set('UTC');
        }
    }


    public function releaseAppointment($key = '')
    {
        if (($key == "") || ($this->cron_key != $key)) {
            echo "Invalid Key or Direct access is not allowed";
            return;
        }
        $this->onlineappointment_model->releaseAppointment();
     
    }



    public function autobackup($key = '')
    {
        if ($this->cron_key == $key) {
            $this->load->dbutil();
            $filename = "db-" . date("Y-m-d_H-i-s") . ".sql";
            $prefs    = array(
                'ignore'     => array(),
                'format'     => 'txt',
                'filename'   => 'mybackup.sql',
                'add_drop'   => true,
                'add_insert' => true,
                'newline'    => "\n",
            );
            $backup = $this->dbutil->backup($prefs);
            $this->load->helper('file');
            write_file('./backup/database_backup/' . $filename, $backup);
            echo "success";
        } else {
            echo "Please pass Cron Secret Key or passed Cron Secret Key is not valid";
        }
    }

    public function expMedicineNotification($key = '')
    {
        if (($key == "") || ($this->cron_key != $key)) {
            echo "Invalid Key or Direct access is not allowed";
            return;
        }

        $staff_roles   = array();
        $result        = $this->expmedicine_model->getList();
        $data['list']  = $result;
        $medicine_data = array();

        if (sizeof($result) > 0) {
            $i = 0;
            foreach ($result as $value) {
                $pharmacy_id     = $value['id'];
                $medicine_name   = $value['medicine_name'];
                $medicine_data[] = $medicine_name;
                $i++;
            }

            $roleresult = $this->staff_model->getStaffbyrole($id = 7);
            if (!empty($roleresult)) {
                $staff_roles[] = array('role_id' => 7, 'send_notification_id' => '');
                foreach ($roleresult as $key => $value) {
                    for ($i = 0; $i < sizeof($medicine_data); $i++) {
                        $notification_data = array(
                            'notification_title' => 'Medicine Expire Alert',
                            'notification_desc'                             => 'Medicine ' . $medicine_data[$i] . ' Expire Alert',
                            'notification_for'                              => 'Super Admin',
                            'receiver_id'                                   => $value["id"],
                            'date'                                          => date("Y-m-d H:i:s"),
                            'is_active'                                     => 'yes',
                        );

                        $send_data = array(
                            'message'         => $this->lang->line('medicine_expire_alert'),
                            'title'           => 'Medicine ' . $medicine_data[$i] . ' Expire Alert',
                            'date'            => date('Y-m-d'),
                            'created_by'      => 'admin',
                            'created_id'      => 0,
                            'visible_staff'   => 'Yes',
                            'visible_patient' => 'No',
                            'publish_date'    => date('Y-m-d'),
                        );
                        $this->notification_model->insertBatch($send_data, $staff_roles);
                    }
                }
            }
        } else {
        }
    }

    public function outofStockMedicineNotification($key = "")
    {
        if (($key == "") || ($this->cron_key != $key)) {
            echo "Invalid Key or Direct access is not allowed";
            return;
        }

        $staff_roles   = array();
        $medicine_data = array();
        $resultlist    = $this->pharmacy_model->searchFullText();
        $i             = 0;
        if (!empty($resultlist)) {
            foreach ($resultlist as $value) {
                $pharmacy_id                 = $value['id'];
                $medicine_name               = $value['medicine_name'];
                $available_qty               = $this->pharmacy_model->totalQuantity($pharmacy_id);
                $totalAvailableQty           = $available_qty['total_qty'];
                $resultlist[$i]["total_qty"] = $totalAvailableQty;

                if ($totalAvailableQty <= 0) {
                    $medicine_data[] = $medicine_name;
                } elseif ($totalAvailableQty <= $min_level) {
                } else if ($totalAvailableQty <= $reorder_level) {
                }
                $i++;
            }

            $roleresult = $this->staff_model->getStaffbyrole($id = 7);
            if (!empty($roleresult)) {
                $staff_roles[] = array('role_id' => 7, 'send_notification_id' => '');
                foreach ($roleresult as $key => $value) {
                    for ($i = 0; $i < sizeof($medicine_data); $i++) {
                        $notification_data = array(
                            'notification_title' => 'Medicine Out of Stock Alert',
                            'notification_desc'                             => 'Medicine ' . $medicine_data[$i] . ' Out of Stock Alert',
                            'notification_for'                              => 'Super Admin',
                            'receiver_id'                                   => $value["id"],
                            'date'                                          => date("Y-m-d H:i:s"),
                            'is_active'                                     => 'yes',
                        );

                        $send_data = array(
                            'message'         => $this->lang->line('medicine_out_of_stock_alert'),
                            'title'           => 'Medicine ' . $medicine_data[$i] . ' Out of Stock Alert',
                            'date'            => date('Y-m-d'),
                            'created_by'      => 'admin',
                            'created_id'      => 0,
                            'visible_staff'   => 'Yes',
                            'visible_patient' => 'No',
                            'publish_date'    => date('Y-m-d'),
                        );

                        $this->notification_model->insertBatch($send_data, $staff_roles);
                    }
                }
            }
        }
    }

    public function lowStockMedicineNotification($key = "")
    {
        if (($key == "") || ($this->cron_key != $key)) {
            echo "Invalid Key or Direct access is not allowed";
            return;
        }

        $staff_roles   = array();
        $medicine_data = array();
        $resultlist    = $this->pharmacy_model->searchFullText();
        $i             = 0;
        if (!empty($resultlist)) {
            foreach ($resultlist as $value) {
                $pharmacy_id                 = $value['id'];
                $medicine_name               = $value['medicine_name'];
                $min_level                   = $value['min_level'];
                $reorder_level               = $value['reorder_level'];
                $available_qty               = $this->pharmacy_model->totalQuantity($pharmacy_id);
                $totalAvailableQty           = $available_qty['total_qty'];
                $resultlist[$i]["total_qty"] = $totalAvailableQty;
                if ($totalAvailableQty <= 0) {
                } elseif ($totalAvailableQty <= $min_level) {
                    $medicine_data[] = $medicine_name;
                } else if ($totalAvailableQty <= $reorder_level) {
                }

                $i++;
            }
            $roleresult = $this->staff_model->getStaffbyrole($id = 7);
            if (!empty($roleresult)) {
                $staff_roles[] = array('role_id' => 7, 'send_notification_id' => '');
                foreach ($roleresult as $key => $value) {
                    for ($i = 0; $i < sizeof($medicine_data); $i++) {
                        $notification_data = array(
                            'notification_title' => 'Low Medicine Stock Alert',
                            'notification_desc'                             => 'Medicine ' . $medicine_data[$i] . ' low stock Alert',
                            'notification_for'                              => 'Super Admin',
                            'receiver_id'                                   => $value["id"],
                            'date'                                          => date("Y-m-d H:i:s"),
                            'is_active'                                     => 'yes',
                        );

                        $send_data = array(
                            'message'         => $this->lang->line('low_medicine_stock_alert'),
                            'title'           => 'Medicine ' . $medicine_data[$i] . ' low stock Alert',
                            'date'            => date('Y-m-d'),
                            'created_by'      => 'admin',
                            'created_id'      => 0,
                            'visible_staff'   => 'Yes',
                            'visible_patient' => 'No',
                            'publish_date'    => date('Y-m-d'),
                        );
                        $this->notification_model->insertBatch($send_data, $staff_roles);
                    }
                }
            }
        }
    }
}
