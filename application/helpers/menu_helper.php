<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
if (!function_exists('active_link')) {

    function activate_menu($controller, $action) {
        $CI = get_instance();
        $method = $CI->router->fetch_method();
        $class = $CI->router->fetch_class();
        return ($method == $action && $controller == $class) ? 'active' : '';
    }

    function set_Topmenu($top_menu_name) {
        $CI = get_instance();
        $session_top_menu = $CI->session->userdata('top_menu');
        if ($session_top_menu == $top_menu_name) {
            return 'active';
        }
        return "";
    }

    function set_Submenu($sub_menu_name, $parent_top = null) {
        $CI = get_instance();
        if ($parent_top !== null && $CI->session->userdata('top_menu') !== $parent_top) {
            return "";
        }
        $session_sub_menu = $CI->session->userdata('sub_menu');
        if ($session_sub_menu == $sub_menu_name) {
            return 'active';
        }
        return "";
    }

    function set_SubSubmenu($sub_menu_name) {
        $CI = get_instance();
        $session_sub_menu = $CI->session->userdata('subsub_menu');
        if ($session_sub_menu == $sub_menu_name) {
            return 'active';
        }
        return "";
    }

    function set_Innermenu($inner_menu_name) {
        $CI = get_instance();
        $session_sub_menu = $CI->session->userdata('inner_menu');
        if ($session_sub_menu == $inner_menu_name) {
            return 'active';
        }
        return "";
    }

    function set_sidebar_Submenu($sub_sidebar_menu_name) {
        $CI = get_instance();
        $session_sub_menu = $CI->session->userdata('sub_sidebar_menu');
        if ($session_sub_menu == $sub_sidebar_menu_name) {
            return 'active';
        }
        return "";
    }

}

function access_denied() {
    redirect('admin/unauthorized');
}

function access_denied_patient() {
    redirect('patient/dashboard/unauthorized');
}

function update_config_installed() {
    $config_path = APPPATH . 'config/config.php';
    @chmod($config_path, FILE_WRITE_MODE);
    $config_file = file_get_contents($config_path);
    if ($config_file === false) {
        log_message('error', 'update_config_installed: could not read ' . $config_path);
        return FALSE;
    }
    $config_file = trim($config_file);

    $proto    = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $app_root = preg_replace('#/install/start.*$#', '/', $_SERVER['REQUEST_URI']);
    $base_url = $proto . '://' . $_SERVER['HTTP_HOST'] . $app_root;

    $config_file = str_replace("\$config['installed'] = false;", "\$config['installed'] = true;", $config_file);
    $config_file = preg_replace(
        "/\\\$config\['base_url'\]\s*=\s*'[^']*';/",
        "\$config['base_url'] = '" . $base_url . "';",
        $config_file
    );

    if (!$fp = fopen($config_path, FOPEN_WRITE_CREATE_DESTRUCTIVE)) {
        log_message('error', 'update_config_installed: could not open ' . $config_path . ' for writing');
        return FALSE;
    }
    flock($fp, LOCK_EX);
    fwrite($fp, $config_file, strlen($config_file));
    flock($fp, LOCK_UN);
    fclose($fp);
    @chmod($config_path, FILE_READ_MODE);
    return TRUE;
}

function update_autoload_installed() {
    $autoload_path = APPPATH . 'config/autoload.php';
    @chmod($autoload_path, FILE_WRITE_MODE);

    $autoload_file = file_get_contents($autoload_path);
    if ($autoload_file === false) {
        log_message('error', 'update_autoload_installed: could not read ' . $autoload_path);
        return FALSE;
    }
    $autoload_file = trim($autoload_file);

    $full_models = "array('addons_model','admin_model','ambulance_model','antenatal_model','appointment_model','appoint_priority_model','attendencetype_model','audit_model','bedgroup_model','bedtype_model','bed_model','bill_model','birthordeath_model','bloodbankstatus_model','blooddonor_model','bloodissue_model','blood_donorcycle_model','bulkmessage_model','calendar_model','captcha_model','casereference_model','certificate_model','chargetype_model','charge_category_model','charge_model','chatuser_model','cms_media_model','cms_menuitems_model','cms_menu_model','cms_page_content_model','cms_page_model','cms_program_model','ComplaintType_model'=>'complainttype_model','complaint_model','conferencehistory_model','conference_model','consultcharge_model','contenttype_model','content_model','customfield_model','department_model','designation_model','dispatch_model','dutyroster_model','emailconfig_model','expensehead_model','expense_model','expmedicine_model','filetype_model','finding_model','floor_model','frontcms_setting_model','gateway_ins_model','general_call_model','generatecertificate_model','generatepatientidcard_model','generatestaffidcard_model','holiday_model','hospital_theme_settings_model','icd10_model','incomehead_model','income_model','itemcategory_model','itemissue_model','itemstock_model','itemstore_model','itemsupplier_model','item_model','lab_model','language_model','leaverequest_model','leavetypes_model','medicine_category_model','medicine_dosage_model','messages_model','modulepermission_model','module_model','MsgQueue_model'=>'msgqueue_model','notificationsetting_model','notification_model','onlineappointment_model','operationtheatre_model','organisation_model','pathology_category_model','pathology_model','patient_id_card_model','patient_model','paymentsetting_model','payment_model','payroll_model','pharmacy_model','prefix_model','prescription_model','printing_model','radio_model','referral_category_model','referral_commission_model','referral_payment_model','referral_person_model','report_model','rolepermission_model','role_model','setting_model','sharecontent_model','smsconfig_model','source_model','specialist_model','StaffAttendaceSetting_model'=>'staffattendacesetting_model','staffattendancemodel','staffidcard_model','staffroles_model','staff_model','symptoms_model','systemnotification_model','taxcategory_model','timeline_model','tpa_model','transaction_model','unittype_model','uploadcontent_model','userlog_model','userpermission_model','user_model','user_theme_preference_model','vehicle_model','visitors_model','visitors_purpose_model','vital_model')";

    $full_libs = "array('database', 'email', 'session', 'form_validation', 'upload', 'pagination', 'Customlib', 'Role', 'Smsgateway', 'QDMailer', 'Adler32', 'Aes','media_storage')";

    $full_helpers = "array('url', 'file', 'menu', 'security', 'cookie', 'theme')";

    // Replace model array — handles both empty (installer state) and already-populated
    $autoload_file = preg_replace(
        "/\\\$autoload\['model'\]\s*=\s*array\([^)]*\)\s*;/",
        "\$autoload['model'] = " . $full_models . ";",
        $autoload_file
    );

    // Replace libraries array
    $autoload_file = preg_replace(
        "/\\\$autoload\['libraries'\]\s*=\s*array\([^)]*\)\s*;/",
        "\$autoload['libraries'] = " . $full_libs . ";",
        $autoload_file
    );

    // Replace helper array
    $autoload_file = preg_replace(
        "/\\\$autoload\['helper'\]\s*=\s*array\([^)]*\)\s*;/",
        "\$autoload['helper'] = " . $full_helpers . ";",
        $autoload_file
    );

    if (!$fp = fopen($autoload_path, FOPEN_WRITE_CREATE_DESTRUCTIVE)) {
        log_message('error', 'update_autoload_installed: could not open ' . $autoload_path . ' for writing');
        return FALSE;
    }
    flock($fp, LOCK_EX);
    fwrite($fp, $autoload_file, strlen($autoload_file));
    flock($fp, LOCK_UN);
    fclose($fp);
    @chmod($autoload_path, FILE_READ_MODE);
    return TRUE;
}

function delete_dir($dirPath) {
    if (!is_dir($dirPath)) {
        throw new InvalidArgumentException("$dirPath must be a directory");
    }
    if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
        $dirPath .= '/';
    }
    $files = glob($dirPath . '*', GLOB_MARK);
    foreach ($files as $file) {
        if (is_dir($file)) {
            delete_dir($file);
        } else {
            unlink($file);
        }
    }
    if (rmdir($dirPath)) {
        return true;
    }
    return false;
}

function admin_url($url = '') {
    if ($url == '') {
        return site_url() . 'site/login';
    } else {
        return site_url() . 'site/login';
    }
}

?>