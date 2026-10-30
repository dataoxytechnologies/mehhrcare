<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Timeline extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('form_validation');
        $this->load->library('Customlib');
        $this->load->library('SaasValidation');
        $this->load->config('image_valid');
    }

    public function add()
    {
        $this->form_validation->set_rules('timeline_title', $this->lang->line('title'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('timeline_date', $this->lang->line('date'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('timeline_doc', $this->lang->line('document'), 'callback_validateCanUploadFile[timeline_doc]');
        $title = $this->input->post("timeline_title");

        if ($this->form_validation->run() == false) {
            $msg = array(
                'timeline_title' => form_error('timeline_title'),
                'timeline_date'  => form_error('timeline_date'),
                'timeline_doc'   => form_error('timeline_doc'),
            );

            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $visible_check = $this->input->post('visible_check');
            $timeline_date = $this->input->post('timeline_date');
            if (empty($visible_check)) {
                $visible = '';
            } else {
                $visible = $visible_check;
            }
            $timeline = array(
                'title'         => $this->input->post('timeline_title'),
                'description'   => $this->input->post('timeline_desc'),
                'timeline_date' => $this->customlib->dateFormatToYYYYMMDD($timeline_date),
                'status'        => $visible,
                'date'          => date('Y-m-d'));

            $id = $this->timeline_model->add($timeline);

            if (isset($_FILES["timeline_doc"]) && !empty($_FILES['timeline_doc']['name'])) {
                $uploaddir = './uploads/patient_timeline/';
                if (!is_dir($uploaddir) && !mkdir($uploaddir)) {
                    die("Error creating folder $uploaddir");
                }
                $fileInfo = pathinfo($_FILES["timeline_doc"]["name"]);
                $document = basename($_FILES['timeline_doc']['name']);

                $img_name = $id . '.' . $fileInfo['extension'];
                if (move_uploaded_file($_FILES["timeline_doc"]["tmp_name"], $uploaddir . $img_name)) {
                    // SaaS: add the uploaded timeline document size to the storage quota usage.
                    try {
                        $saas_quota_result = $this->saasvalidation->updateStorageLimit('storage', ['timeline_doc']);
                        // Capture silent API failures (lib returns JSON status:false instead of throwing).
                        if (is_string($saas_quota_result)) {
                            $saas_quota_decoded = json_decode($saas_quota_result);
                            if (isset($saas_quota_decoded->status) && $saas_quota_decoded->status === false) {
                                log_message('error', 'SaaS storage quota update returned failure (timeline add): ' . ($saas_quota_decoded->message ?? 'unknown'));
                            }
                        }
                    } catch (Exception $e) {
                        log_message('error', 'SaaS storage quota update failed (timeline add): ' . $e->getMessage());
                    }
                }
            } else {
                $document = "";
                $img_name = "";
            }

            $upload_data = array('id' => $id, 'document' => $img_name);
            $this->timeline_model->add($upload_data);
            $msg   = $this->lang->line('timeline_added_successfully');
            $array = array('status' => 'success', 'error' => '', 'message' => $msg);
        }
        echo json_encode($array);
    }

    public function add_staff_timeline()
    {
        $this->form_validation->set_rules('timeline_title', $this->lang->line('title'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('timeline_date', $this->lang->line('date'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('timeline_doc', $this->lang->line('image'), 'callback_handle_upload[timeline_doc]|callback_validateCanUploadFile[timeline_doc]');
        $title = $this->input->post("timeline_title");

        if ($this->form_validation->run() == false) {

            $msg = array(
                'timeline_title' => form_error('timeline_title'),
                'timeline_date'  => form_error('timeline_date'),
                'timeline_doc'   => form_error('timeline_doc'),
            );

            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $visible_check = $this->input->post('visible_check');
            $timeline_date = $this->input->post('timeline_date');
			
            if (empty($visible_check)) {
                $visible = '';
            } else {
                $visible = $visible_check;
            }
			
            $timeline = array(
                'title'         => $this->input->post('timeline_title'),
                'timeline_date' => $this->customlib->dateFormatToYYYYMMDD($timeline_date),
                'description'   => $this->input->post('timeline_desc'),
                'status'        => $visible,
                'date'          => date('Y-m-d'),
                'staff_id'      => $this->input->post('staff_id'));
				
            $id = $this->timeline_model->add_staff_timeline($timeline);

            if (isset($_FILES["timeline_doc"]) && !empty($_FILES['timeline_doc']['name'])) {
                $uploaddir = './uploads/staff_timeline/';
                if (!is_dir($uploaddir) && !mkdir($uploaddir)) {
                    die("Error creating folder $uploaddir");
                }
                $fileInfo = pathinfo($_FILES["timeline_doc"]["name"]);
                $document = basename($_FILES['timeline_doc']['name']);

                $img_name = $id . '.' . $fileInfo['extension'];
                if (move_uploaded_file($_FILES["timeline_doc"]["tmp_name"], $uploaddir . $img_name)) {
                    // SaaS: add the uploaded timeline document size to the storage quota usage.
                    try {
                        $saas_quota_result = $this->saasvalidation->updateStorageLimit('storage', ['timeline_doc']);
                        // Capture silent API failures (lib returns JSON status:false instead of throwing).
                        if (is_string($saas_quota_result)) {
                            $saas_quota_decoded = json_decode($saas_quota_result);
                            if (isset($saas_quota_decoded->status) && $saas_quota_decoded->status === false) {
                                log_message('error', 'SaaS storage quota update returned failure (timeline add_staff_timeline): ' . ($saas_quota_decoded->message ?? 'unknown'));
                            }
                        }
                    } catch (Exception $e) {
                        log_message('error', 'SaaS storage quota update failed (timeline add_staff_timeline): ' . $e->getMessage());
                    }
                }
            } else {
                $document = "";
                $img_name = "";
            }

            $upload_data = array('id' => $id, 'document' => $img_name);
            $this->timeline_model->add_staff_timeline($upload_data);
            $msg   = $this->lang->line('timeline_added_successfully');
            $array = array('status' => 'success', 'error' => '', 'message' => $msg);
        }
        echo json_encode($array);

    }

    public function edit_staff_timeline()
    {
        $this->form_validation->set_rules('timeline_title', $this->lang->line('title'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('timeline_date', $this->lang->line('date'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('timeline_doc', $this->lang->line('image'), 'callback_handle_upload[timeline_doc]|callback_validateCanUploadFile[timeline_doc]');

        $title = $this->input->post("timeline_title");
        if ($this->form_validation->run() == false) {
            $msg = array(
                'timeline_title' => form_error('timeline_title'),
                'timeline_date'  => form_error('timeline_date'),
                'timeline_doc'   => form_error('timeline_doc'),
            );
            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {

            $visible_check = $this->input->post('visible_check');
            $staffid       = $this->input->post('staff_id');
            $timelineid    = $this->input->post('timeline_id');
            $timeline_date = $this->input->post('timeline_date');
            $date          = $this->customlib->dateFormatToYYYYMMDD($timeline_date);
            if (empty($visible_check)) {
                $visible = '';
            } else {
                $visible = $visible_check;
            }
            $timeline = array(
                'id'            => $timelineid,
                'title'         => $this->input->post('timeline_title'),
                'timeline_date' => $this->customlib->dateFormatToYYYYMMDD($timeline_date),
                'description'   => $this->input->post('timeline_desc'),
                'status'        => $visible,
                'date'          => date('Y-m-d'),
                'staff_id'      => $staffid);

            $this->timeline_model->add_staff_timeline($timeline);

            // SaaS: capture the existing timeline document for quota diff + cleanup.
            $saas_existing = $this->timeline_model->geteditstaffTimeline($timelineid);
            $saas_old_doc  = (!empty($saas_existing) && !empty($saas_existing['document'])) ? $saas_existing['document'] : '';
            $saas_prev_kb  = (!empty($saas_old_doc)) ? $this->media_storage->getUploadedFileSize($saas_old_doc, 'uploads/staff_timeline') : 0;

            if (isset($_FILES["timeline_doc"]) && !empty($_FILES['timeline_doc']['name'])) {
                $uploaddir = './uploads/staff_timeline/';
                if (!is_dir($uploaddir) && !mkdir($uploaddir)) {
                    die("Error creating folder $uploaddir");
                }
                $fileInfo = pathinfo($_FILES["timeline_doc"]["name"]);
                $document = basename($_FILES['timeline_doc']['name']);

                $img_name = $timelineid . '.' . $fileInfo['extension'];
                move_uploaded_file($_FILES["timeline_doc"]["tmp_name"], $uploaddir . $img_name);

                $upload_data = array('id' => $timelineid, 'document' => $img_name);
                $this->timeline_model->add_staff_timeline($upload_data);

                // SaaS: remove orphaned old file (when name/extension changed) and adjust quota by size diff.
                if (!empty($saas_old_doc) && $saas_old_doc != $img_name) {
                    $this->media_storage->filedelete($saas_old_doc, 'uploads/staff_timeline');
                }
                try {
                    $saas_new_kb = $this->media_storage->getTmpFileSize('timeline_doc');
                    if ($saas_prev_kb > $saas_new_kb) {
                        $this->saasvalidation->deleteResouceQuota('storage', $saas_prev_kb - $saas_new_kb);
                    } elseif ($saas_new_kb > $saas_prev_kb) {
                        $this->saasvalidation->updateResouceQuota('storage', $saas_new_kb - $saas_prev_kb);
                    }
                } catch (Exception $e) {
                    log_message('error', 'SaaS storage quota update failed (timeline edit_staff_timeline): ' . $e->getMessage());
                }
            }

            $msg   = $this->lang->line('timeline_added_successfully');
            $array = array('status' => 'success', 'error' => '', 'message' => $msg);
        }
        echo json_encode($array);
    }

    public function add_patient_timeline()
    {
        $this->form_validation->set_rules('timeline_title', $this->lang->line('title'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('timeline_date', $this->lang->line('date'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('timeline_doc', $this->lang->line('document'), 'callback_handle_upload[timeline_doc]|callback_validateCanUploadFile[timeline_doc]');

        if ($this->form_validation->run() == false) {

            $msg = array(
                'timeline_title' => form_error('timeline_title'),
                'timeline_date'  => form_error('timeline_date'),
                'timeline_doc'   => form_error('timeline_doc'),
            );

            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $visible_check = $this->input->post('visible_check', TRUE);
            $timeline_date = $this->input->post('timeline_date', TRUE);
            $user_id       = $this->customlib->getStaffID();

            if (empty($visible_check)) {
                $visible = '';
            } else {
                $visible = $visible_check;
            }

            $timeline = array(
                'title'                => $this->input->post('timeline_title', TRUE),
                'timeline_date'        => $this->customlib->dateFormatToYYYYMMDDHis($timeline_date),
                'description'          => $this->input->post('timeline_desc', TRUE),
                'status'               => $visible,
                'date'                 => date('Y-m-d'),
                'patient_id'           => $this->input->post('patient_id', TRUE),
                'generated_users_type' => 'staff',
                'generated_users_id'   => $user_id,
            );

            $id = $this->timeline_model->add_patient_timeline($timeline);

            if (isset($_FILES["timeline_doc"]) && !empty($_FILES['timeline_doc']['name'])) {
                $file_name = $this->media_storage->fileupload("timeline_doc",'./uploads/patient_timeline/');
                $upload_data = array('id' => $id, 'document' => $file_name);
                $this->timeline_model->add_patient_timeline($upload_data);

                // SaaS: add the uploaded timeline document size to the storage quota usage.
                if (!IsNullOrEmptyString($file_name)) {
                    try {
                        $saas_quota_result = $this->saasvalidation->updateStorageLimit('storage', ['timeline_doc']);
                        // Capture silent API failures (lib returns JSON status:false instead of throwing).
                        if (is_string($saas_quota_result)) {
                            $saas_quota_decoded = json_decode($saas_quota_result);
                            if (isset($saas_quota_decoded->status) && $saas_quota_decoded->status === false) {
                                log_message('error', 'SaaS storage quota update returned failure (timeline add_patient_timeline): ' . ($saas_quota_decoded->message ?? 'unknown'));
                            }
                        }
                    } catch (Exception $e) {
                        log_message('error', 'SaaS storage quota update failed (timeline add_patient_timeline): ' . $e->getMessage());
                    }
                }
            }

            $msg   = $this->lang->line('timeline_added_successfully');
            $array = array('status' => 'success', 'error' => '', 'message' => $msg);
        }
        echo json_encode($array);
    }

    public function edit_patient_timeline()
    {
        $this->form_validation->set_rules('timeline_title', $this->lang->line('title'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('timeline_date', $this->lang->line('date'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('timeline_doc', $this->lang->line('document'), 'callback_handle_upload[timeline_doc]|callback_validateCanUploadFile[timeline_doc]');

        $title = $this->input->post("timeline_title", TRUE);

        if ($this->form_validation->run() == false) {

            $msg = array(
                'timeline_title' => form_error('timeline_title'),
                'timeline_date'  => form_error('timeline_date'),
                'timeline_doc'   => form_error('timeline_doc'),
            );

            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {

            $visible_check = $this->input->post('visible_check', TRUE);
            $patientid     = $this->input->post('patient_id', TRUE);
            $timelineid    = $this->input->post('timeline_id', TRUE);
            $timeline_date = $this->input->post('timeline_date', TRUE);
            $date          = $this->customlib->dateFormatToYYYYMMDDHis($timeline_date);
            $user_id       = $this->customlib->getStaffID();
            if (empty($visible_check)) {
                $visible = '';
            } else {
                $visible = $visible_check;
            }
            $timeline = array(
                'id'                   => $timelineid,
                'title'                => $this->input->post('timeline_title', TRUE),
                'timeline_date'        => $date,
                'description'          => $this->input->post('timeline_desc', TRUE),
                'status'               => $visible,
                'date'                 => date('Y-m-d'),
                'patient_id'           => $patientid,
                'generated_users_type' => 'staff',
                'generated_users_id'   => $user_id,
            );

            $this->timeline_model->add_patient_timeline($timeline);

            // SaaS: capture the existing timeline document for quota diff + cleanup.
            $saas_existing = $this->timeline_model->geteditTimeline($timelineid);
            $saas_old_doc  = (!empty($saas_existing) && !empty($saas_existing['document'])) ? $saas_existing['document'] : '';
            $saas_prev_kb  = (!empty($saas_old_doc)) ? $this->media_storage->getUploadedFileSize($saas_old_doc, 'uploads/patient_timeline') : 0;

            if (isset($_FILES["timeline_doc"]) && !empty($_FILES['timeline_doc']['name'])) {
                $file_name = $this->media_storage->fileupload("timeline_doc",'./uploads/patient_timeline/');
                $upload_data = array('id' => $timelineid, 'document' => $file_name);
                $this->timeline_model->add_patient_timeline($upload_data);

                // SaaS: remove the replaced old file and adjust quota by the size diff.
                if (!IsNullOrEmptyString($file_name)) {
                    if (!empty($saas_old_doc) && $saas_old_doc != $file_name) {
                        $this->media_storage->filedelete($saas_old_doc, 'uploads/patient_timeline');
                    }
                    try {
                        $saas_new_kb = $this->media_storage->getTmpFileSize('timeline_doc');
                        if ($saas_prev_kb > $saas_new_kb) {
                            $this->saasvalidation->deleteResouceQuota('storage', $saas_prev_kb - $saas_new_kb);
                        } elseif ($saas_new_kb > $saas_prev_kb) {
                            $this->saasvalidation->updateResouceQuota('storage', $saas_new_kb - $saas_prev_kb);
                        }
                    } catch (Exception $e) {
                        log_message('error', 'SaaS storage quota update failed (timeline edit_patient_timeline): ' . $e->getMessage());
                    }
                }
            }


            $msg   = $this->lang->line('timeline_edit_successfully');
            $array = array('status' => 'success', 'error' => '', 'message' => $msg);
        }
        echo json_encode($array);
    }

    public function download($timeline_id, $doc)
    {
        $this->load->helper('download');
        $filepath = "./uploads/patient_timeline/" . $doc;
        $data     = file_get_contents($filepath);
        $name     = $doc;
        force_download($name, $data);
    }

    public function download_staff_timeline($timeline_id, $doc)
    {
        $this->load->helper('download');
        $filepath = "./uploads/staff_timeline/" . $doc;
        $data     = file_get_contents($filepath);
        $name     = $doc;
        force_download($name, $data);
    }

    public function download_patient_timeline($timeline_id)
    {
        $timeline_list = $this->timeline_model->geteditTimeline($timeline_id); 
        $this->media_storage->filedownload($timeline_list['document'],'./uploads/patient_timeline/');
    }

    public function delete_timeline($id)
    {
        if (!empty($id)) {
            $this->timeline_model->delete_timeline($id);
        }
    }

    public function delete_staff_timeline($id)
    {
        if (!empty($id)) {
            $this->timeline_model->delete_staff_timeline($id);
        }
    }

    public function delete_patient_timeline($id)
    {
        if (!empty($id)) {
            // SaaS: release the timeline document's storage from the quota and remove the file.
            $saas_row = $this->timeline_model->geteditTimeline($id);
            if (!empty($saas_row) && !empty($saas_row['document'])) {
                $saas_kb = $this->media_storage->getUploadedFileSize($saas_row['document'], 'uploads/patient_timeline');
                if ($saas_kb > 0) {
                    try {
                        $this->saasvalidation->deleteResouceQuota('storage', $saas_kb);
                    } catch (Exception $e) {
                        log_message('error', 'SaaS storage quota release failed (timeline delete_patient_timeline): ' . $e->getMessage());
                    }
                }
                $this->media_storage->filedelete($saas_row['document'], 'uploads/patient_timeline');
            }

            $this->timeline_model->delete_patient_timeline($id);
        }
    }

    public function staff_timeline($id = 77)
    {
        $userdata = $this->customlib->getUserData();
        $userid   = $userdata['id'];
        $status   = '';
        if ($userid == $id) {
            $status = 'yes';
        }

        $result         = $this->timeline_model->getStaffTimeline($id, $status);
        $data["result"] = $result;
        $this->load->view("admin/staff_timeline", $data);
    }

    public function patient_timeline($id = 77)
    {
        $userdata = $this->customlib->getUserData();
        $userid   = $userdata['id'];
        $status   = '';
        if ($userid == $id) {
            $status = 'yes';
        }

        $result         = $this->timeline_model->getPatientTimeline($id, $status);
        $data["result"] = $result;
        $this->load->view("admin/patient_timeline", $data);
    }

    /**
     * SaaS storage pre-check (form_validation callback).
     * Returns false (blocking the save) when the upload would push the
     * tenant over its storage quota. SaasValidation sets the error message.
     */
    public function validateCanUploadFile($str, $params_string)
    {
        $storage_array = array_map('trim', explode(',', $params_string));
        return $this->saasvalidation->validateCanUploadFile($str, $storage_array);
    }

    public function handle_upload($str, $var)
    {
        $image_validate = $this->config->item('file_validate');
        if (isset($_FILES[$var]) && !empty($_FILES[$var]['name'])) {
            $file_type         = $_FILES[$var]['type'];
            $file_size         = $_FILES[$var]["size"];
            $file_name         = $_FILES[$var]["name"];
            $allowed_extension = $image_validate['allowed_extension'];
          
            $ext               = pathinfo($file_name, PATHINFO_EXTENSION);
            $allowed_mime_type = $image_validate['allowed_mime_type'];          
        
                if (!in_array($file_type, $allowed_mime_type)) {
                    $this->form_validation->set_message('handle_upload', $this->lang->line('error_while_uploading_document'));
                    return false;
                }

                if (!in_array($ext, $allowed_extension) || !in_array($file_type, $allowed_mime_type)) {
                    $this->form_validation->set_message('handle_upload', $this->lang->line('extension_error_while_uploading_document'));
                    return false;
                }
                if ($file_size > $image_validate['upload_size']) {
                    $this->form_validation->set_message('handle_upload', $this->lang->line('file_size_shoud_be_less_than') . number_format($image_validate['upload_size'] / 1048576, 2) . " MB");
                    return false;
                }          

            return true;
        }
        return true;
    }

}
