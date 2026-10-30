<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

function generateRandomColors($num) {
    $colors_picker = ['#b0dd38','#e30e66','#febe10','#ff1253','#6780b2','#dda73b','#005976','#5db681','#8f44fc','#2d2d2d','#3b07f8','#140253','#beacfd','#c36a56','#5db681','#2c391c','#c26b3d','#412414','#710f58','#607d8b','#d1b303','#4d8878','#7f7d01','#2a2a00','#c36a56','#ee4b57','#85aa55'];
    $colors=[];
    
    for ($i = 0; $i < $num; $i++) {
        $colors[] = $colors_picker[$i];
    }

    return $colors;
}

function isJSON($string)
{
    return is_string($string) && is_array(json_decode($string, true)) && (json_last_error() == JSON_ERROR_NONE) ? true : false;
}

function findPrefixType($prefixes, $search_prefix)
{
    foreach ($prefixes as $prefix_key => $prefix_value) {
        if ($prefix_value->prefix == $search_prefix) {
            return $prefix_value->type;
        }
    }
    return false;
}

function splitPrefixID($search)
{
    $search_prefix = preg_replace('/[^0-9]/', '', $search);

    return $search_prefix;
}

function splitPrefixType($search)
{
    $search_prefix = preg_replace('/[^a-zA-Z]/', '', $search);

    return $search_prefix;
}

function chkDuplicate($arr)
{
    $dups = array();
    foreach (array_count_values($arr) as $val => $c) {
        if ($c > 1) {
            $dups[] = $val;
        }
    }

    return $dups;
}

function has_duplicate_array($array)
{
    return count($array) !== count(array_unique($array));
}

function amountFormat($amount)
{
	if ($amount != "") {
		// Cast to float before round(): PHP 8.x throws a fatal TypeError when a
		// non-numeric string (e.g. bad data in a credit_limit/amount column) is
		// passed to round(). (float) coerces such values to 0.0 instead of crashing.
		$amount = round((float) $amount, 2);
		return number_format((float) $amount, 2, '.', '');
	}
}

function uniqueFileName()
{
    return time() . uniqid(rand());
}

function composePatientName($patient_name, $patient_id)
{
    $name = "";
    if ($patient_name != "") {
        $name = ($patient_id != "") ? $patient_name . " (" . $patient_id . ")" : $patient_name;
    }

    return $name;
}

function composeStaffName($staff)
{
    $name = "";
    if (!empty($staff)) {
        $name = ($staff->surname == "") ? $staff->name : $staff->name . " " . $staff->surname;
    }

    return $name;
}

function composeStaffNameByString($staff_name, $staff_surname, $staff_employeid)
{
    $name = "";
    if ($staff_name != "") {
        $name = ($staff_surname == "") ? $staff_name . " (" . $staff_employeid . ")" : $staff_name . " " . $staff_surname . " (" . $staff_employeid . ")";
    }

    return $name;
}

function calculatePercent($amount, $percent)
{
    $ci = &get_instance();
    $ci->load->helper('custom');
    $percent_amt = 0;
    if ($amount != "") {
        $percent_amt = ($amount * $percent) / 100;
        $percent_amt = amountFormat($percent_amt);
    }
    return $percent_amt;
}

function chat_couter()
{
    $ci = &get_instance();
    return $ci->chatuser_model->getChatUnreadCount();
}

function cal_percentage($first_amount, $secound_amount)
{
    if ($secound_amount > 0) {
        $count1 = $first_amount / $secound_amount;
        $count2 = $count1 * 100;
        $count  = number_format($count2, 2);
    } else {
        $count = 0;
    }

    return $count;
}

function searchForKeyData($id, $array, $find_key)
{
    foreach ($array as $key => $val) {

        if ($val[$find_key] == $id) {
            return $key;
        }
    }
    return null;
}

function rand_color()
{
    $array = array(
        '#267278',
        '#50aed3',
        '#e46031',
        '#65228d',
        '#48b24f',
        '#e4B031',
        '#cad93f',
        '#d21f75',
        '#3b3989',
        '#58595b',
    );
    return $array;
}

function sortInnerData($a, $b)
{
    return $a['total_counts'] < $b['total_counts']?1:-1;
}


function img_time(){
   return "?".time();
}

function random_string($len = 5){
  $string = substr(str_shuffle(str_repeat("0123456789abcdefghijklmnopqrstuvwxyz", 5)), 0, $len);
  return $string;
}

if (!function_exists('custom_url')) {

    function custom_url() {

        $CI = &get_instance();
        $session_data=$CI->session->userdata('admin');
        return $session_data['base_url'];
      
    }
}

if (!function_exists('dir_path')) {

    function dir_path() {

        $CI = &get_instance();
        $session_data=$CI->session->userdata('admin');
        return $session_data['folder_path'];
      
    }
}

if (!function_exists('IsNullOrEmptyString')) {

function IsNullOrEmptyString($str){
    return ($str === null || trim($str) === '');
}
}

if (!function_exists('check_report_level_exceed')) {

    function check_report_level_exceed($old_reference_range,$range_from,$range_to,$patient_range) {
        
    if($patient_range == ""){
        return false;
    }
    if(IsNullOrEmptyString($range_from)){
    
        $range=explode('-',$old_reference_range);
        $range_from=trim($range[0] ?? '');
        $range_to=trim($range[1] ?? '');
    }

    $range_to= IsNullOrEmptyString($range_to) ? $range_from : $range_to;

    if ($range_from <= $patient_range && $range_to >= $patient_range) {
      return false;
    }
      return true;
      
    }
}

if (!function_exists('is_active_2fa')) {

    function is_active_2fa()
    {
        $CI = &get_instance();
        $CI->load->model(array("google_authenticator/gauth_model"));
       return $CI->gauth_model->is_enable();

    }

}

function getLocation()
{
    $ip = getIP();
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "http://ipwhois.app/json/" . $ip);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $execute = curl_exec($ch);
    curl_close($ch);
    $ipResult = json_decode($execute);
    return $ipResult;
}

function getIP()
{
    // Get real visitor IP behind CloudFlare network
    if (isset($_SERVER["HTTP_CF_CONNECTING_IP"])) {
        $_SERVER['REMOTE_ADDR']    = $_SERVER["HTTP_CF_CONNECTING_IP"];
        $_SERVER['HTTP_CLIENT_IP'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
    }
    $client  = @$_SERVER['HTTP_CLIENT_IP'];
    $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
    $remote  = $_SERVER['REMOTE_ADDR'];

    if (filter_var($client, FILTER_VALIDATE_IP)) {
        $ip = $client;
    } elseif (filter_var($forward, FILTER_VALIDATE_IP)) {
        $ip = $forward;
    } else {
        $ip = $remote;
    }

    return $ip;
}

if (!function_exists('check_selected')) {
    function check_selected($paymentlist) {
        if (!is_array($paymentlist) && !is_object($paymentlist)) {
            return 'none';
        }
        foreach ($paymentlist as $row) {
            if (isset($row->is_active) && $row->is_active === 'yes') {
                return $row->payment_type;
            }
        }
        return 'none';
    }
}

if (!function_exists('check_in_array')) {
    function check_in_array($payment_type, $paymentlist) {
        if (!is_array($paymentlist) && !is_object($paymentlist)) {
            return false;
        }
        foreach ($paymentlist as $row) {
            if (isset($row->payment_type) && $row->payment_type === $payment_type) {
                return $row;
            }
        }
        return false;
    }
}

function getAgentDetail()
{
    $CI           = &get_instance();

    $CI->load->library('user_agent');

    $user_agent = "";
    if ($CI->agent->is_mobile('iphone')) {
        $user_agent .= "Iphone | ";
    } else if ($CI->agent->is_mobile()) {
        $user_agent .= "Mobile | ";
    } else {
        $user_agent .= "Desktop | ";
    }

    if ($CI->agent->is_browser()) {
        $agent = $CI->agent->browser() . ' ' . $CI->agent->version();
    } elseif ($CI->agent->is_robot()) {
        $agent = $CI->agent->robot();
    } elseif ($CI->agent->is_mobile()) {
        $agent = $CI->agent->mobile();
    } else {
        $agent = 'Unidentified User Agent';
    }

    $user_agent .= $CI->agent->platform() . " | ";
    $user_agent .= $agent;
    return  $user_agent;
}
