<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Hospital Message Helper
 *
 * Each function here is responsible for hydrating {{placeholders}} in a
 * notification template and returning ['message' => ..., 'subject' => ...].
 *
 * These functions are called by Mailsmsconf::mailsms() before dispatching
 * to any gateway (email / sms / push / whatsapp).
 */

// -----------------------------------------------------------------------
// OPD Patient Registration
// -----------------------------------------------------------------------
function hospitalOpdRegistration($patient_id, $opd_id, $template, $subject, $appointment_date)
{
    $CI = &get_instance();

    $patient                     = $CI->patient_model->getDetails($opd_id);
    $patient['opdno']            = $CI->customlib->getSessionPrefixByType('opd_no') . $opd_id;
    $patient['appointment_date'] = $appointment_date;

    foreach ($patient as $key => $value) {
        if (!empty($value)) {
            $template = str_replace('{{' . $key . '}}', $value, $template);
            $subject  = str_replace('{{' . $key . '}}', $value, $subject);
        }
    }

    return ['message' => $template, 'subject' => $subject];
}

// -----------------------------------------------------------------------
// IPD Patient Registration
// -----------------------------------------------------------------------
function hospitalIpdRegistration($patient_id, $ipd_id, $template, $subject)
{
    $CI = &get_instance();

    $patient            = $CI->patient_model->getIpdDetails($ipd_id);
    $patient['ipd_no']  = $CI->customlib->getSessionPrefixByType('ipd_no') . $ipd_id;

    foreach ($patient as $key => $value) {
        if (!empty($value)) {
            $template = str_replace('{{' . $key . '}}', $value, $template);
            $subject  = str_replace('{{' . $key . '}}', $value, $subject);
        }
    }

    return ['message' => $template, 'subject' => $subject];
}

// -----------------------------------------------------------------------
// IPD Patient Discharged
// -----------------------------------------------------------------------
function hospitalIpdDischarged($details, $template, $subject)
{
    foreach ($details as $key => $value) {
        if (!empty($value)) {
            $template = str_replace('{{' . $key . '}}', $value, $template);
            $subject  = str_replace('{{' . $key . '}}', $value, $subject);
        }
    }

    return ['message' => $template, 'subject' => $subject];
}

// -----------------------------------------------------------------------
// OPD Patient Discharged
// -----------------------------------------------------------------------
function hospitalOpdDischarged($details, $template, $subject)
{
    if (!empty($details)) {
        foreach ($details as $key => $value) {
            if (!empty($value)) {
                $template = str_replace('{{' . $key . '}}', $value, $template);
                $subject  = str_replace('{{' . $key . '}}', $value, $subject);
            }
        }
    }

    return ['message' => $template, 'subject' => $subject];
}

// -----------------------------------------------------------------------
// Appointment Approved / Confirmation
// -----------------------------------------------------------------------
function hospitalAppointmentConfirmation($sender_details, $appointment_id, $template, $subject)
{
    $CI = &get_instance();

    $patient                  = $CI->appointment_model->getDetailsFornotification($appointment_id);
    $patient['patient_name']  = $sender_details['patient_name'];

    foreach ($patient as $key => $value) {
        if (!empty($value)) {
            $template = str_replace('{{' . $key . '}}', $value, $template);
            $subject  = str_replace('{{' . $key . '}}', $value, $subject);
        }
    }

    return ['message' => $template, 'subject' => $subject];
}

// -----------------------------------------------------------------------
// Login Credential (patient or staff)
// -----------------------------------------------------------------------
function hospitalLoginCredential($credential_for, $sender_details, $template, $subject)
{
    $CI = &get_instance();

    if ($credential_for == 'patient') {
        $patient                        = $CI->patient_model->patientProfileDetails($sender_details['id']);
        $sender_details['url']          = site_url('site/userlogin');
        $sender_details['display_name'] = $patient['patient_name'];
    } elseif ($credential_for == 'staff') {
        $staff                          = $CI->staff_model->get($sender_details['id']);
        $sender_details['url']          = site_url('site/login');
        $sender_details['display_name'] = $staff['name'] . ' ' . $staff['surname'];
    }

    foreach ($sender_details as $key => $value) {
        if (!empty($value)) {
            $template = str_replace('{{' . $key . '}}', $value, $template);
            $subject  = str_replace('{{' . $key . '}}', $value, $subject);
        }
    }

    return ['message' => $template, 'subject' => $subject];
}

// -----------------------------------------------------------------------
// Live Consult (Zoom / telemedicine)
// -----------------------------------------------------------------------
function hospitalLiveConsult($conference_id, $template, $subject)
{
    $CI = &get_instance();

    $conference = $CI->conference_model->getconference($conference_id);

    foreach ($conference as $key => $value) {
        if (!empty($value)) {
            $template = str_replace('{{' . $key . '}}', $value, $template);
            $subject  = str_replace('{{' . $key . '}}', $value, $subject);
        }
    }

    return ['message' => $template, 'subject' => $subject];
}

// -----------------------------------------------------------------------
// Live Meeting (staff online meeting)
// -----------------------------------------------------------------------
function hospitalLiveMeetingStaff($staff_detail, $template, $subject)
{
    foreach ($staff_detail as $key => $value) {
        if (!empty($value)) {
            $template = str_replace('{{' . $key . '}}', $value, $template);
            $subject  = str_replace('{{' . $key . '}}', $value, $subject);
        }
    }

    return ['message' => $template, 'subject' => $subject];
}
