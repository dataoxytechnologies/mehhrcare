<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

require_once APPPATH . "third_party/omnipay/vendor/autoload.php";

use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

/**
 * Patientflow-specific QR code generator.
 *
 * Isolated from the shared QR_Code library so patientflow payment QR codes can
 * use different settings (larger version, medium ECC) without affecting patient
 * / staff ID cards elsewhere in the system.
 *
 * Uses QRCode->render() with returnResource => true so the GD image resource is
 * returned before the vendor library reaches imagedestroy() (which raises a
 * deprecation-turned-exception on PHP 8.5). The resource is then saved directly
 * with imagepng(), bypassing the vendor dumpImage() path entirely.
 */
class Patientflow_qr
{

    public function __construct()
    {
        $CI = &get_instance();
    }

    /**
     * Generate a patientflow payment QR image.
     *
     * @param string $upload_path Directory to write the PNG into (trailing slash).
     * @param string $text        Data to encode (e.g. payment URL).
     * @param string $file_name   Basename without extension.
     * @return bool|false True on success, false on failure.
     */
    public function generate($upload_path, $text, $file_name)
    {
        if (!is_dir($upload_path)) {
            @mkdir($upload_path, 0777, true);
        }

        // Ensure directory is writable
        if (is_dir($upload_path) && !is_writable($upload_path)) {
            @chmod($upload_path, 0777);
        }

        $options = new QROptions([
            'version' => 10,            // ~1392 bits capacity at ECC_M (fits long payment URLs)
            'eccLevel' => QRCode::ECC_M, // medium ECC -> more data room than ECC_H
            'scale' => 4,
            'returnResource' => true,   // return GD resource, skip dumpImage()/imagedestroy()
            'imageBase64' => false,
            'imageTransparent' => true,
            'foregroundColor' => '#000000',
            'backgroundColor' => '#ffffff',
            'outputType' => QRCode::OUTPUT_IMAGE_PNG
        ]);

        $qrCode = new QRCode($options);
        $imageResource = $qrCode->render($text);

        if ($imageResource === false || $imageResource === null) {
            return false;
        }

        $target = $upload_path . $file_name . '.png';
        $result = imagepng($imageResource, $target);
        imagedestroy($imageResource);

        return $result !== false;
    }
}
