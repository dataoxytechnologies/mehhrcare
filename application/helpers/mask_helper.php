<?php
// application/helpers/mask_helper.php

if (!defined('BASEPATH')) exit('No direct script access allowed');

// ============================================================
// CORE MASKING ENGINE - All masking calculated here
// ============================================================

function mask_sensitive($value, $show_start = 2, $show_end = 2) {
    $len = strlen($value);

    // Auto-adjust for short values
    $show_start = ($len <= 2) ? 0 : (($len <= 4) ? 1 : $show_start);
    $show_end   = ($len <= 2) ? 0 : (($len <= 4) ? 1 : $show_end);

    // Calculate mask length
    $mask_len = max(0, $len - $show_start - $show_end);

    // Single calculation at the end
    return substr($value, 0, $show_start)
         . str_repeat('*', $mask_len)
         . ($show_end > 0 ? substr($value, -$show_end) : '');
}

// ============================================================
// EMAIL MASKING
// ============================================================

function mask_email($email) {
    if (empty($email) || !strpos($email, '@')) return '***@***.***';

    $parts  = explode("@", $email);
    $name   = $parts[0];
    $domain = $parts[1];

    $len        = strlen($name);
    $show_start = ($len <= 2) ? 0 : (($len <= 4) ? 1 : 2);
    $show_end   = ($len <= 4) ? 0 : 1;
    $mask_len   = max(0, $len - $show_start - $show_end);

    $masked_name = substr($name, 0, $show_start)
                 . str_repeat('*', $mask_len)
                 . ($show_end > 0 ? substr($name, -$show_end) : '');

    return $masked_name . '@' . $domain;
}

// ============================================================
// MOBILE NUMBER MASKING
// ============================================================

function mask_mobile($mobile) {
    if (empty($mobile)) return '***';
    return mask_sensitive($mobile, 3, 3);
}

// ============================================================
// SMS PASSWORD MASKING
// ============================================================

function mask_sms_password($password) {
    if (empty($password)) return '***';
    return mask_sensitive($password, 1, 1);
}

// ============================================================
// EMAIL PASSWORD MASKING
// ============================================================

function mask_email_password($password) {
    if (empty($password)) return '***';
    return mask_sensitive($password, 2, 1);
}

// ============================================================
// PAYMENT GATEWAY KEY / SECRET MASKING
// ============================================================

function mask_payment_key($key) {
    if (empty($key)) return '***';
    return mask_sensitive($key, 4, 4);
}

function mask_payment_secret($secret) {
    if (empty($secret)) return '***';
    return mask_sensitive($secret, 2, 2);
}

// ============================================================
// API KEY MASKING
// ============================================================

function mask_api_key($api_key) {
    if (empty($api_key)) return '***';
    return mask_sensitive($api_key, 4, 4);
}

// ============================================================
// BANK ACCOUNT NUMBER MASKING
// ============================================================

function mask_bank_account($account_no) {
    if (empty($account_no)) return '***';
    return mask_sensitive($account_no, 2, 4);
}

// ============================================================
// CREDIT / DEBIT CARD MASKING
// ============================================================

function mask_card_number($card_no) {
    // Remove spaces if any
    $card_no = str_replace(' ', '', $card_no);
    if (empty($card_no)) return '***';

    // Format: 1234 **** **** 5678
    return substr($card_no, 0, 4)
         . ' '
         . str_repeat('*', 4)
         . ' '
         . str_repeat('*', 4)
         . ' '
         . substr($card_no, -4);
}

// ============================================================
// CVV MASKING
// ============================================================

function mask_cvv($cvv) {
    if (empty($cvv)) return '***';
    return str_repeat('*', strlen($cvv)); // Always fully masked
}

// ============================================================
// PAN CARD MASKING (India)
// ============================================================

function mask_pan($pan) {
    if (empty($pan)) return '***';
    return mask_sensitive(strtoupper($pan), 2, 2);
}

// ============================================================
// AADHAAR MASKING (India - 12 digit)
// ============================================================

function mask_aadhaar($aadhaar) {
    $aadhaar = str_replace(' ', '', $aadhaar);
    if (empty($aadhaar)) return '***';
    return 'XXXX XXXX ' . substr($aadhaar, -4);
}

// ============================================================
// USERNAME MASKING
// ============================================================

function mask_username($username) {
    if (empty($username)) return '***';
    return mask_sensitive($username, 2, 1);
}

// ============================================================
// IP ADDRESS MASKING
// ============================================================

function mask_ip($ip) {
    if (empty($ip)) return '*.*.*.*';
    $parts = explode('.', $ip);
    // Mask last two octets
    return $parts[0] . '.' . $parts[1] . '.*.*';
}

// ============================================================
// GENERIC PASSWORD MASKING (any password)
// ============================================================

function mask_password($password) {
    if (empty($password)) return '***';
    return mask_sensitive($password, 1, 1);
}
