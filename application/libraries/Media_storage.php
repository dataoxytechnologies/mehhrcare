<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Media_storage
{

    private $_CI;

    public function __construct()
    {
        $this->_CI = &get_instance();
        $this->_CI->load->library('customlib');
    }

    public function fileupload($media_name, $upload_path = "")
    {
        if (file_exists($_FILES[$media_name]['tmp_name']) && !$_FILES[$media_name]['error'] == UPLOAD_ERR_NO_FILE) {

            $name        = $_FILES[$media_name]['name'];
            $file_name   = time() . "-" . uniqid(rand()) . "!" . $name;
            $destination = FCPATH . $upload_path . $file_name;

            if (move_uploaded_file($_FILES[$media_name]["tmp_name"], $destination)) {
                return $file_name;
            }

        }

        return null;
    }

    public function filedownload($file_name, $download_path = "")
    {
        $file_url           = FCPATH . $download_path . "/" . $file_name;
        $download_file_name = substr($file_name, (strpos($file_name, '!') + 1));
        $this->_CI->load->helper('download');
        $data = file_get_contents($file_url);
        force_download($download_file_name, $data);
    }

    public function fileview($file_name)
    {
        if (!IsNullOrEmptyString($file_name)) {
            $download_file_name = substr($file_name, (strpos($file_name, '!') + 1));
            return $download_file_name;
        }
        return null;
    }

    public function getImageURL($file_name)
    {
        if (!IsNullOrEmptyString($file_name)) {
            if (strpos($file_name, 'http') === 0) {
                return $file_name . img_time();
            }
            $download_file_name = $this->_CI->customlib->getBaseUrl() . $file_name . img_time();
            return $download_file_name;
        }
        return null;
    }

    public function filedelete($file_name, $path = "")
    {
        if (!IsNullOrEmptyString($file_name)) {
            $url = FCPATH . $path . "/" . $file_name;
            if (file_exists($url)) {
                if (unlink($url)) {
                    return true;
                }
            }
        }

        return false;
    }

    /* -------------------------------------------------------------------------
     * SaaS storage-quota helpers (used by SaasValidation / resource metering).
     * convertBytesToKB() is a hard dependency of SaasValidation; the size
     * getters are used by controllers when wiring storage quota on upload.
     * ---------------------------------------------------------------------- */

    public function convertBytesToKB($file_size)
    {
        return round($file_size / 1024); // nearest whole KB
    }

    // Size (KB) of a single file currently sitting in $_FILES, before it is moved.
    public function getTmpFileSize($media_name)
    {
        if (
            isset($_FILES[$media_name]) &&
            isset($_FILES[$media_name]['tmp_name']) &&
            $_FILES[$media_name]['error'] !== UPLOAD_ERR_NO_FILE
        ) {
            $file_size = $_FILES[$media_name]['size'];
            if ($file_size > 0) {
                return $this->convertBytesToKB($file_size);
            }
        }

        return 0;
    }

    // Total size (KB) of a multi-file ($media_name[]) upload, before move.
    public function getTmpMultipleFileSize($media_name)
    {
        if (isset($_FILES[$media_name]) && !empty($_FILES[$media_name]['name'][0])) {
            $files      = $_FILES[$media_name];
            $total_size = 0;

            for ($i = 0; $i < count($files['name']); $i++) {
                if ($files['error'][$i] === UPLOAD_ERR_OK && $files['size'][$i] > 0) {
                    $total_size += $files['size'][$i];
                }
            }

            return $this->convertBytesToKB($total_size);
        }

        return 0;
    }

    // Size (KB) of a file already saved on disk. Mirrors fileupload()/filedelete()
    // which resolve paths against FCPATH in this project.
    public function getUploadedFileSize($file_name, $file_path = "")
    {
        if ($file_path == "") {
            $file_url = FCPATH . $file_name;
        } else {
            $file_url = FCPATH . $file_path . "/" . $file_name;
        }

        if (file_exists($file_url)) {
            return $this->convertBytesToKB(filesize($file_url));
        }

        return 0;
    }

    /**
     * Multi-directory attachment resolver for SaaS quota release sites.
     * The `transactions.attachment` column stores only the filename (no path);
     * different controllers historically save to different upload directories
     * (mostly uploads/payment_document/, but Bloodbank::partialbill and Radio
     * partial-bill use uploads/patient_timeline/). This helper probes known
     * candidate directories in priority order and returns the one where the
     * file actually exists. Default fallback preserves prior behavior so
     * release-skip + filedelete-no-op semantics for genuinely-missing files
     * remain unchanged.
     *
     * @param string $attachment value from transactions.attachment column
     * @return string directory suitable for getUploadedFileSize() / filedelete()
     */
    public function resolveAttachmentDir($attachment)
    {
        if (empty($attachment)) {
            return '';
        }
        // Full relative path already stored in column.
        if (strpos($attachment, '/') !== false) {
            return '';
        }
        // Try known upload directories in priority order; first match wins.
        $candidates = array('uploads/payment_document', 'uploads/patient_timeline');
        foreach ($candidates as $candidate) {
            if (file_exists(FCPATH . $candidate . '/' . $attachment)) {
                return $candidate;
            }
        }
        // Legacy fallback: some older code (e.g. pathology bill payment document) saved
        // straight to the web root (FCPATH) with no upload dir. Detect that so size/delete/
        // quota-release still resolve for those records.
        if (file_exists(FCPATH . $attachment)) {
            return '';
        }
        // File not found in any known directory — return historical default so
        // downstream getUploadedFileSize() returns 0 and filedelete() no-ops.
        return 'uploads/payment_document';
    }

}
