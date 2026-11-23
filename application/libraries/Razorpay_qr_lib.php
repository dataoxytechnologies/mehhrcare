<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Razorpay QR Code client (lightweight, no external SDK).
 *
 * Wraps Razorpay's QR Code API:
 *   POST https://api.razorpay.com/v1/payments/qr_codes   (create)
 *   GET  https://api.razorpay.com/v1/payments/qr_codes/:id (fetch / verify)
 *
 * Authentication: HTTP Basic with key_id:key_secret from the active
 * payment method settings (api_publishable_key / api_secret_key).
 */
class Razorpay_qr_lib
{
    // private $base_url = 'https://api.razorpay.com/v1/qr_codes';
    private $base_url = 'https://api.razorpay.com/v1/payments/qr_codes';
    private $key_id;
    private $key_secret;
    private $timeout = 30;

    public function __construct($params = [])
    {
        $this->key_id     = !empty($params['key_id']) ? $params['key_id'] : '';
        $this->key_secret = !empty($params['key_secret']) ? $params['key_secret'] : '';
    }

    public function set_credentials($key_id, $key_secret)
    {
        $this->key_id = $key_id;
        $this->key_secret = $key_secret;
    }

    public function set_timeout($seconds)
    {
        $this->timeout = (int) $seconds;
    }

    /**
     * Create a single-use UPI QR code for a fixed amount.
     *
     * @param array $params
     *     amount (float, currency units), name, description,
     *     customer (name, contact, email), notes (array)
     * @return array ['success'=>bool, 'data'=>array|string, 'error'=>string]
     */
    public function create_qr(array $params = [])
    {
        if (empty($this->key_id) || empty($this->key_secret)) {
            return array('success' => false, 'error' => 'Razorpay credentials not configured.');
        }

        $amount = !empty($params['amount']) ? (float) $params['amount'] : 0;
        if ($amount <= 0) {
            return array('success' => false, 'error' => 'Invalid amount.');
        }

        $customer = !empty($params['customer']) && is_array($params['customer']) ? $params['customer'] : array();
        $notes    = !empty($params['notes']) && is_array($params['notes']) ? $params['notes'] : array();

        $body = array(
            'type'             => 'upi_qr',
            'name'             => !empty($params['name']) ? (string) $params['name'] : 'Patient Payment',
            'usage'            => 'single_use',
            'fixed_amount'     => true,
            'payment_amount'   => (int) round($amount * 100), // paise
            'description'      => !empty($params['description']) ? (string) $params['description'] : 'Patient flow payment',
            // 'customer'         => array_filter($customer),
            'notes'            => $notes,
        );

        $response = $this->request('POST', $this->base_url, $body, true);
        if (!$response['success']) {
            return $response;
        }

        $data = $response['data'];
        $qr_code_raw = !empty($data['qr_code']) ? $data['qr_code'] : '';
        $qr_code_image = '';
        if (is_array($qr_code_raw)) {
            $qr_code_image = !empty($qr_code_raw['image_url']) ? (string) $qr_code_raw['image_url'] : '';
        } else {
            $qr_code_image = (string) $qr_code_raw;
        }
        return array(
            'success' => true,
            'data'    => array(
                'id'           => !empty($data['id']) ? (string) $data['id'] : '',
                'qr_code'      => $qr_code_image,
                'short_url'    => !empty($data['short_url']) ? (string) $data['short_url'] : '',
                'amount'       => !empty($data['amount']) ? (float) ($data['amount'] / 100) : $amount,
                'status'       => !empty($data['status']) ? (string) $data['status'] : 'created',
                'payment_amount' => !empty($data['payment_amount']) ? (int) $data['payment_amount'] : 0,
            ),
        );
    }

    /**
     * Fetch a QR code's current status from Razorpay.
     *
     * @param string $qr_code_id
     * @return array ['success'=>bool, 'data'=>array|string, 'error'=>string]
     */
    public function fetch_qr($qr_code_id)
    {
        if (empty($this->key_id) || empty($this->key_secret)) {
            return array('success' => false, 'error' => 'Razorpay credentials not configured.');
        }
        if (empty($qr_code_id)) {
            return array('success' => false, 'error' => 'QR code id is required.');
        }

        $url = $this->base_url . '/' . rawurlencode((string) $qr_code_id);
        return $this->request('GET', $url, null, false);
    }

    /**
     * Low-level authenticated request to Razorpay API.
     */
    private function request($method, $url, ?array $body = null, $json = true)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $this->timeout);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_USERPWD, $this->key_id . ':' . $this->key_secret);

        $headers = array('Accept: application/json');
        $method = strtoupper($method);
        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
        } elseif ($method !== 'GET') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        }
        if ($json && $body !== null) {
            $payload = json_encode($body);
            $headers[] = 'Content-Type: application/json';
            $headers[] = 'Content-Length: ' . strlen($payload);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        }
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $raw = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $errno = curl_errno($ch);
        $error = curl_error($ch);
        curl_close($ch);

        if ($errno !== 0) {
            return array('success' => false, 'error' => 'cURL error (' . $errno . '): ' . $error, 'http_code' => $httpCode);
        }

        $decoded = json_decode($raw, true);
        if ($decoded === null && $raw !== '') {
            $decoded = array('raw' => $raw);
        }

        $success = ($httpCode >= 200 && $httpCode < 300);
        if (!$success) {
            $msg = !empty($decoded['error']['description']) ? $decoded['error']['description']
                : (!empty($decoded['error']) ? (string) $decoded['error'] : 'HTTP ' . $httpCode);
            error_log('[Razorpay_qr_lib] ' . $method . ' ' . $url . ' failed: ' . $msg . ' (HTTP ' . $httpCode . ')');
            return array('success' => false, 'error' => $msg, 'http_code' => $httpCode, 'data' => $decoded);
        }

        return array('success' => true, 'data' => $decoded, 'http_code' => $httpCode);
    }
}
