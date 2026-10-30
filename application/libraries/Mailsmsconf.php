<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Mailsmsconf
{
    public $config_mailsms;
    public $CI;

    public function __construct()
    {
        $this->CI = &get_instance();
        $this->CI->config->load('mailsms');
        $this->CI->load->helper('message');
        $this->CI->load->library('customlib');
        $this->CI->load->library('smsgateway');
        $this->CI->load->library('mailgateway');
        $this->CI->load->library('pushnotification');
		
        if (file_exists(APPPATH . 'libraries/Whatsappgateway.php')) {
            $this->CI->load->library('whatsappgateway');
        }
		
        $this->CI->load->model('notificationsetting_model');
        $this->config_mailsms = $this->CI->config->item('mailsms');
    }

    public function mailsms($send_for, $sender_details, $date = null)
    {
        $chk_mail_sms    = $this->CI->customlib->sendMailSMS($send_for);
        $currency_symbol = $this->CI->customlib->getHospitalCurrencyFormat();

        // Hydrate common patient fields from DB
        if (isset($sender_details['patient_id']) && $sender_details['patient_id'] != '') {
            $patient_data                         = $this->CI->notificationsetting_model->getpatientDetails($sender_details['patient_id']);
            $sender_details['email']              = $patient_data['email'];
            $sender_details['patient_name']       = composePatientName($patient_data['patient_name'], $sender_details['patient_id']);
            $sender_details['mobileno']           = $patient_data['mobileno'];
            $sender_details['contact_no']         = $patient_data['mobileno'];
            $sender_details['dob']                = $patient_data['dob'];
            $sender_details['gender']             = $patient_data['gender'];
            $sender_details['currency_symbol']    = $currency_symbol;
            $sender_details['app_key']            = isset($patient_data['app_key']) ? $patient_data['app_key'] : '';
        }

        if (empty($chk_mail_sms)) {
            return;
        }

        // ---------------------------------------------------------------
        // OPD PATIENT REGISTRATION
        // ---------------------------------------------------------------
        if ($send_for == 'opd_patient_registration') {

            if ($chk_mail_sms['template'] == '') return;

            $body = hospitalOpdRegistration(
                $sender_details['patient_id'],
                $sender_details['opd_details_id'],
                nl2br($chk_mail_sms['template']),
                $chk_mail_sms['subject'],
                $sender_details['appointment_date']
            );

            if ($chk_mail_sms['mail']) {
                $this->CI->mailgateway->sentMail($send_for, $sender_details['email'], $body['subject'], $body['message']);
            }
            if ($chk_mail_sms['sms']) {
                $this->CI->smsgateway->sendSMS($sender_details['contact_no'], $body['message'], $chk_mail_sms['template_id'], $send_for);
            }
            if ($chk_mail_sms['mobileapp'] && !empty($sender_details['app_key'])) {
                $this->CI->pushnotification->sentNotification($send_for, $sender_details['app_key'], $body['subject'], $body['message']);
            }
            if ($chk_mail_sms['whatsapp'] && !empty($sender_details['contact_no'])) {
                $this->CI->whatsappgateway->send($send_for, $sender_details['contact_no'], $body['message'], $chk_mail_sms['whatsapp_template_id'] ?? '');
            }

        // ---------------------------------------------------------------
        // IPD PATIENT REGISTRATION
        // ---------------------------------------------------------------
        } elseif ($send_for == 'ipd_patient_registration') {

            if ($chk_mail_sms['template'] == '') return;

            $body = hospitalIpdRegistration(
                $sender_details['patient_id'],
                $sender_details['ipdid'],
                nl2br($chk_mail_sms['template']),
                $chk_mail_sms['subject']
            );

            if ($chk_mail_sms['mail']) {
                $this->CI->mailgateway->sentMail($send_for, $sender_details['email'], $body['subject'], $body['message']);
            }
            if ($chk_mail_sms['sms']) {
                $this->CI->smsgateway->sendSMS($sender_details['contact_no'], $body['message'], $chk_mail_sms['template_id'], $send_for);
            }
            if ($chk_mail_sms['mobileapp'] && !empty($sender_details['app_key'])) {
                $this->CI->pushnotification->sentNotification($send_for, $sender_details['app_key'], $body['subject'], $body['message']);
            }
            if ($chk_mail_sms['whatsapp'] && !empty($sender_details['contact_no'])) {
                $this->CI->whatsappgateway->send($send_for, $sender_details['contact_no'], $body['message'], $chk_mail_sms['whatsapp_template_id'] ?? '');
            }

        // ---------------------------------------------------------------
        // IPD PATIENT DISCHARGED
        // ---------------------------------------------------------------
        } elseif ($send_for == 'ipd_patient_discharged') {

            if ($chk_mail_sms['template'] == '') return;

            $body = hospitalIpdDischarged(
                $sender_details,
                nl2br($chk_mail_sms['template']),
                $chk_mail_sms['subject']
            );

            if ($chk_mail_sms['mail']) {
                $this->CI->mailgateway->sentMail($send_for, $sender_details['email'], $body['subject'], $body['message']);
            }
            if ($chk_mail_sms['sms']) {
                $this->CI->smsgateway->sendSMS($sender_details['contact_no'], $body['message'], $chk_mail_sms['template_id'], $send_for);
            }
            if ($chk_mail_sms['mobileapp'] && !empty($sender_details['app_key'])) {
                $this->CI->pushnotification->sentNotification($send_for, $sender_details['app_key'], $body['subject'], $body['message']);
            }
            if ($chk_mail_sms['whatsapp'] && !empty($sender_details['contact_no'])) {
                $this->CI->whatsappgateway->send($send_for, $sender_details['contact_no'], $body['message'], $chk_mail_sms['whatsapp_template_id'] ?? '');
            }

        // ---------------------------------------------------------------
        // OPD PATIENT DISCHARGED
        // ---------------------------------------------------------------
        } elseif ($send_for == 'opd_patient_discharged') {

            if ($chk_mail_sms['template'] == '') return;

            $body = hospitalOpdDischarged(
                $sender_details,
                nl2br($chk_mail_sms['template']),
                $chk_mail_sms['subject']
            );

            if ($chk_mail_sms['mail']) {
                $this->CI->mailgateway->sentMail($send_for, $sender_details['email'], $body['subject'], $body['message']);
            }
            if ($chk_mail_sms['sms']) {
                $this->CI->smsgateway->sendSMS($sender_details['contact_no'], $body['message'], $chk_mail_sms['template_id'], $send_for);
            }
            if ($chk_mail_sms['mobileapp'] && !empty($sender_details['app_key'])) {
                $this->CI->pushnotification->sentNotification($send_for, $sender_details['app_key'], $body['subject'], $body['message']);
            }
            if ($chk_mail_sms['whatsapp'] && !empty($sender_details['contact_no'])) {
                $this->CI->whatsappgateway->send($send_for, $sender_details['contact_no'], $body['message'], $chk_mail_sms['whatsapp_template_id'] ?? '');
            }

        // ---------------------------------------------------------------
        // APPOINTMENT APPROVED
        // ---------------------------------------------------------------
        } elseif ($send_for == 'appointment_approved') {

            if ($chk_mail_sms['template'] == '') return;

            $body = hospitalAppointmentConfirmation(
                $sender_details,
                $sender_details['appointment_id'],
                nl2br($chk_mail_sms['template']),
                $chk_mail_sms['subject']
            );

            if ($chk_mail_sms['mail']) {
                $this->CI->mailgateway->sentMail($send_for, $sender_details['email'], $body['subject'], $body['message']);
            }
            if ($chk_mail_sms['sms']) {
                $this->CI->smsgateway->sendSMS($sender_details['mobileno'], $body['message'], $chk_mail_sms['template_id'], $send_for);
            }
            if ($chk_mail_sms['mobileapp'] && !empty($sender_details['app_key'])) {
                $this->CI->pushnotification->sentNotification($send_for, $sender_details['app_key'], $body['subject'], $body['message']);
            }
            if ($chk_mail_sms['whatsapp'] && !empty($sender_details['mobileno'])) {
                $this->CI->whatsappgateway->send($send_for, $sender_details['mobileno'], $body['message'], $chk_mail_sms['whatsapp_template_id'] ?? '');
            }

        // ---------------------------------------------------------------
        // LOGIN CREDENTIAL
        // ---------------------------------------------------------------
        } elseif ($send_for == 'login_credential') {

            if ($chk_mail_sms['template'] == '') return;

            $body = hospitalLoginCredential(
                $sender_details['credential_for'],
                $sender_details,
                nl2br($chk_mail_sms['template']),
                $chk_mail_sms['subject']
            );

            if ($chk_mail_sms['mail']) {
                $this->CI->mailgateway->sentMail($send_for, $sender_details['email'], $body['subject'], $body['message'],1);
            }
            if ($chk_mail_sms['sms']) {
                $this->CI->smsgateway->sendSMS($sender_details['contact_no'], $body['message'], $chk_mail_sms['template_id'], $send_for,1);
            }
            if ($chk_mail_sms['mobileapp'] && !empty($sender_details['app_key'])) {
                $this->CI->pushnotification->sentNotification($send_for, $sender_details['app_key'], $body['subject'], $body['message'],1);
            }
            if ($chk_mail_sms['whatsapp'] && !empty($sender_details['contact_no'])) {
                $this->CI->whatsappgateway->send($send_for, $sender_details['contact_no'], $body['message'], $chk_mail_sms['whatsapp_template_id'] ?? '',1);
            }

        // ---------------------------------------------------------------
        // LIVE CONSULT (Zoom/telemedicine)
        // ---------------------------------------------------------------
        } elseif ($send_for == 'live_consult') {

            if ($chk_mail_sms['template'] == '') return;

            $body = hospitalLiveConsult(
                $sender_details['conference_id'],
                nl2br($chk_mail_sms['template']),
                $chk_mail_sms['subject']
            );

            if ($chk_mail_sms['mail']) {
                $this->CI->mailgateway->sentMail($send_for, $sender_details['email'], $body['subject'], $body['message']);
            }
            if ($chk_mail_sms['sms']) {
                $this->CI->smsgateway->sendSMS($sender_details['contact_no'], $body['message'], $chk_mail_sms['template_id'], $send_for);
            }
            if ($chk_mail_sms['mobileapp'] && !empty($sender_details['app_key'])) {
                $this->CI->pushnotification->sentNotification($send_for, $sender_details['app_key'], $body['subject'], $body['message']);
            }
            if ($chk_mail_sms['whatsapp'] && !empty($sender_details['contact_no'])) {
                $this->CI->whatsappgateway->send($send_for, $sender_details['contact_no'], $body['message'], $chk_mail_sms['whatsapp_template_id'] ?? '');
            }

        // ---------------------------------------------------------------
        // LIVE MEETING (staff online meeting)
        // ---------------------------------------------------------------
        } elseif ($send_for == 'live_meeting') {

            $this->sendMeeting($chk_mail_sms, $sender_details, $chk_mail_sms['template'], $chk_mail_sms['subject'], $chk_mail_sms['template_id']);
        }
    }

    // -----------------------------------------------------------------------
    // Live meeting — loops over staff list
    // -----------------------------------------------------------------------
    public function sendMeeting($chk_mail_sms, $staff_details, $template, $subject, $template_id)
    {
        $staff_email_list = [];
        $staff_sms_list   = [];

        if (!empty($staff_details)) {
            foreach ($staff_details as $staff_value) {
                $row = [
                    'title'       => $staff_value['title'],
                    'date'        => $staff_value['date'],
                    'duration'    => $staff_value['duration'],
                    'employee_id' => $staff_value['employee_id'],
                    'department'  => $staff_value['department'],
                    'designation' => $staff_value['designation'],
                    'name'        => $staff_value['name'],
                    'contact_no'  => $staff_value['contact_no'],
                    'email'       => $staff_value['email'],
                ];

                if (!empty($staff_value['email'])) {
                    $staff_email_list[$staff_value['email']] = $row;
                }
                if (!empty($staff_value['contact_no'])) {
                    $staff_sms_list[$staff_value['contact_no']] = $row;
                }
            }

            if ($chk_mail_sms['mail'] && !empty($staff_email_list)) {
                foreach ($staff_email_list as $email => $staff_value) {
                    $body = hospitalLiveMeetingStaff($staff_value, nl2br($template), $subject);
                    $this->CI->mailgateway->sentMail('live_meeting', $email, $body['subject'], $body['message']);
                }
            }

            if ($chk_mail_sms['sms'] && !empty($staff_sms_list)) {
                foreach ($staff_sms_list as $contact_no => $staff_value) {
                    $sms_body = hospitalLiveMeetingStaff($staff_value, nl2br($template), $subject);
                    $this->CI->smsgateway->sendSMS($contact_no, $sms_body['message'], $template_id, 'live_meeting');
                }
            }

            if ($chk_mail_sms['whatsapp'] && !empty($staff_sms_list)) {
                foreach ($staff_sms_list as $contact_no => $staff_value) {
                    $wa_body = hospitalLiveMeetingStaff($staff_value, nl2br($template), $subject);
                    $this->CI->whatsappgateway->send('live_meeting', $contact_no, $wa_body['message'], $chk_mail_sms['whatsapp_template_id'] ?? '');
                }
            }
        }
    }
}
