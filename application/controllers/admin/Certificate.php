<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Certificate extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();

        $this->load->library('Customlib');
        $this->load->library('datatables');
        $this->load->library('SaasValidation');
        $this->load->model('certificate_model');
        $this->config->load('image_valid');
    }

    public function index()
    { 
        if (!$this->rbac->hasPrivilege('certificate', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'Certificate');
        $this->session->set_userdata('sub_menu', 'admin/generatecertificate');
        $data['certificateList'] = $this->certificate_model->certificateList();
        $data['module'] = 'certificate';
        $this->load->view('layout/header', $data);
        $this->load->view('admin/certificate/createcertificate', $data);
        $this->load->view('layout/footer', $data);
    } 

    public function getcertificatedatatable()
    {
        $dt_response = $this->certificate_model->getAllcertificateRecord();
        $dt_response = json_decode($dt_response);
        $dt_data     = array();
        if (!empty($dt_response->data)) {
            foreach ($dt_response->data as $key => $value) {
                $row = array();

                $certificate_action = "<a href='#' onclick='viewRecord(" . $value->id . ")' class='' data-bs-toggle='' data-bs-target=''  title=''>";

                //====================================

                if ($value->background_image != '' && !is_null($value->background_image)) {
                    $image_file=$this->media_storage->getImageURL("uploads/certificate/$value->background_image");
                    $image = "<img src='$image_file' width='40'>";
                } else {
                    $image = "<i class='fa fa-picture-o fa-3x'></i>";
                }

                $action = "<a href='#' onclick='viewRecord(" . $value->id . ")' class='btn btn-sm btn-outline-secondary' data-bs-toggle='tooltip' data-bs-placement='top' title='" . $this->lang->line('view') . "'><i class='fa fa-reorder'></i></a>";

                if ($this->rbac->hasPrivilege('certificate', 'can_edit')) {
                    $action .= "<a href='#' onclick='getRecord(" . $value->id . "),refreshmodal()' class='btn btn-sm btn-outline-primary' data-bs-toggle='tooltip' data-bs-placement='top' title='" . $this->lang->line('edit') . "'><i class='fa fa-pencil'></i></a>";
                }

                if ($this->rbac->hasPrivilege('certificate', 'can_delete')) {
                    $action .= "<a href='#' onclick='Delete_recored(" . $value->id . ")' class='btn btn-sm btn-outline-danger' data-bs-toggle='tooltip' data-bs-placement='top' title='" . $this->lang->line('delete') . "'><i class='fa fa-trash'></i></a>";
                }

                $row[] = $certificate_action . $value->certificate_name . "</a>";
                $row[] = $image;
                $row[] = "<div class='white-space-nowrap'>" . $action . "</div>";

                //====================
                $dt_data[] = $row;
            }
        }
        $json_data = array(
            "draw"            => intval($dt_response->draw),
            "recordsTotal"    => intval($dt_response->recordsTotal),
            "recordsFiltered" => intval($dt_response->recordsFiltered),
            "data"            => $dt_data,
        );
        echo json_encode($json_data);
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

    public function create()
    {

	    if (!$this->rbac->hasPrivilege('certificate', 'can_add')) {
            access_denied();
        }
        $this->form_validation->set_rules('certificate_name', $this->lang->line('certificate_template_name'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('certificate_text', $this->lang->line('body_text'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('background_image', $this->lang->line('background_image'), 'callback_handle_image_upload[background_image]|callback_validateCanUploadFile[background_image]');

        if ($this->form_validation->run() == false) {
            $msg = array(
                'certificate_name' => form_error('certificate_name'),
                'certificate_text' => form_error('certificate_text'),
                'background_image' => form_error('background_image'),
            );
            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');

        } else {
            $data['title'] = $this->lang->line('add_library');

            if (!empty($_FILES['background_image']['name'])) {
                $picture = $this->media_storage->fileupload("background_image",'uploads/certificate/');
            } else {
                $picture = '';
            }
            // SaaS: full relative path (background_image holds filename only).
            $picture_path = (!IsNullOrEmptyString($picture)) ? 'uploads/certificate/' . $picture : '';

            if ($this->input->post('is_active_patient_img', TRUE) == 1) {
                $enableimg = $this->input->post('is_active_patient_img', TRUE);
                $imgHeight = $this->input->post('image_height', TRUE);
            } else {
                $enableimg = 0;
                $imgHeight = 0;
            }
            $data = array(
                'certificate_name'     => $this->input->post('certificate_name', TRUE),
                'certificate_text'     => $this->input->post('certificate_text', TRUE),
                'left_header'          => $this->input->post('left_header', TRUE),
                'center_header'        => $this->input->post('center_header', TRUE),
                'right_header'         => $this->input->post('right_header', TRUE),
                'left_footer'          => $this->input->post('left_footer', TRUE),
                'right_footer'         => $this->input->post('right_footer', TRUE),
                'center_footer'        => $this->input->post('center_footer', TRUE),
                'created_for'          => 2,
                'status'               => 1,
                'background_image'     => $picture,
                'filename_path'        => $picture_path,
                'header_height'        => $this->input->post('header_height', TRUE),
                'content_height'       => $this->input->post('content_height', TRUE),
                'footer_height'        => $this->input->post('footer_height', TRUE),
                'content_width'        => $this->input->post('content_width', TRUE),
                'enable_patient_image' => $enableimg,
                'enable_image_height'  => $imgHeight,
            );

            $insert_id = $this->certificate_model->addcertificate($data);

            // SaaS: add the uploaded background image's size to the storage quota usage.
            if (!IsNullOrEmptyString($picture)) {
                try {
                    $this->saasvalidation->updateStorageLimit('storage', ['background_image']);
                } catch (Exception $e) {
                    log_message('error', 'SaaS storage quota update failed (certificate create): ' . $e->getMessage());
                }
            }

            $array     = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));

        }
        echo json_encode($array);
    }

    public function getcertificate()
    {
        $id          = $this->input->post("id", TRUE);
        $certificate = $this->certificate_model->get($id);        
        echo json_encode($certificate);
    }

    public function edit()
    {
        $this->form_validation->set_rules('certificate_name', $this->lang->line('certificate_template_name'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('certificate_text', $this->lang->line('body_text'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('background_image', $this->lang->line('background_image'), 'callback_handle_image_upload[background_image]|callback_validateCanUploadFile[background_image]');
        if ($this->form_validation->run() == false) {
            $msg = array(
                'certificate_name' => form_error('certificate_name'),
                'certificate_text' => form_error('certificate_text'),
                'background_image' => form_error('background_image'),
            );
            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');

        } else {

            if ($this->input->post('is_active_patient_img', TRUE) == 1) {
                $enableimg = $this->input->post('is_active_patient_img', TRUE);
                $imgHeight = $this->input->post('image_height', TRUE);
            } else {
                $enableimg = 0;
                $imgHeight = 0;
            }

            // SaaS: resolve the existing image's full path (for quota diff + cleanup).
            $existing = $this->certificate_model->get($this->input->post('id', TRUE));
            $old_path = '';
            if (!empty($existing['filename_path'])) {
                $old_path = $existing['filename_path'];
            } elseif (!empty($existing['background_image'])) {
                $old_path = 'uploads/certificate/' . $existing['background_image'];
            }

            if (isset($_FILES["background_image"]) && !empty($_FILES['background_image']['name'])) {
                $picture = $this->media_storage->fileupload("background_image",'./uploads/certificate/');
                $picture_path = (!IsNullOrEmptyString($picture)) ? 'uploads/certificate/' . $picture : $old_path;
            }else{
                $picture="";
                $picture_path = $old_path; // no new upload: preserve existing file reference
            }

            $data   = array(
                        'id'                   => $this->input->post('id', TRUE),
                        'certificate_name'     => $this->input->post('certificate_name', TRUE),
                        'certificate_text'     => $this->input->post('certificate_text', TRUE),
                        'left_header'          => $this->input->post('left_header', TRUE),
                        'center_header'        => $this->input->post('center_header', TRUE),
                        'right_header'         => $this->input->post('right_header', TRUE),
                        'left_footer'          => $this->input->post('left_footer', TRUE),
                        'right_footer'         => $this->input->post('right_footer', TRUE),
                        'center_footer'        => $this->input->post('center_footer', TRUE),
                        'created_for'          => 2,
                        'status'               => 1,
                        'background_image'     => $picture,
                        'filename_path'        => $picture_path,
                        'header_height'        => $this->input->post('header_height', TRUE),
                        'content_height'       => $this->input->post('content_height', TRUE),
                        'footer_height'        => $this->input->post('footer_height', TRUE),
                        'content_width'        => $this->input->post('content_width', TRUE),
                        'enable_patient_image' => $enableimg,
                        'enable_image_height'  => $imgHeight,
                    );

            $this->certificate_model->addcertificate($data);

            // SaaS: only when a NEW file replaced the old one — adjust quota by the
            // size diff and remove the replaced physical file.
            if (isset($_FILES["background_image"]) && !empty($_FILES['background_image']['name']) && !IsNullOrEmptyString($picture)) {
                try {
                    $prev_kb = (!empty($old_path)) ? $this->media_storage->getUploadedFileSize($old_path) : 0;
                    $new_kb  = $this->media_storage->getTmpFileSize('background_image');
                    if (!empty($old_path)) {
                        $this->media_storage->filedelete($old_path, '');
                    }
                    if ($prev_kb > $new_kb) {
                        $this->saasvalidation->deleteResouceQuota('storage', $prev_kb - $new_kb);
                    } elseif ($new_kb > $prev_kb) {
                        $this->saasvalidation->updateResouceQuota('storage', $new_kb - $prev_kb);
                    }
                } catch (Exception $e) {
                    log_message('error', 'SaaS storage quota update failed (certificate edit): ' . $e->getMessage());
                }
            }

            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));

        }
        echo json_encode($array);
    }

    public function delete($id)
    {
        if (!$this->rbac->hasPrivilege('certificate', 'can_delete')) {
            access_denied();
        }
        $data['title'] = $this->lang->line('certificate_list');

        // SaaS: release the image's storage from the quota and remove the file.
        $row = $this->certificate_model->get($id);
        if (!empty($row)) {
            $del_path = '';
            if (!empty($row['filename_path'])) {
                $del_path = $row['filename_path'];
            } elseif (!empty($row['background_image'])) {
                $del_path = 'uploads/certificate/' . $row['background_image'];
            }
            if (!empty($del_path)) {
                $kb = $this->media_storage->getUploadedFileSize($del_path);
                if ($kb > 0) {
                    try {
                        $this->saasvalidation->deleteResouceQuota('storage', $kb);
                    } catch (Exception $e) {
                        log_message('error', 'SaaS storage quota release failed (certificate delete): ' . $e->getMessage());
                    }
                }
                $this->media_storage->filedelete($del_path, '');
            }
        }

        $this->certificate_model->remove($id);
        $this->session->set_flashdata('msg', '<div class="alert alert-success text-left">' . $this->lang->line('delete_message') . '</div>');
    }

    public function view()
    {
        $id                  = $this->input->post('certificateid', TRUE);
        $output              = '';
        $data                = array();
        $data['certificate'] = $this->certificate_model->certifiatebyid($id);
        $preview             = $this->load->view('admin/certificate/preview_certificate', $data, true);
        echo $preview;
    }

    public function handle_image_upload($str, $var)
    {
        $image_validate = $this->config->item('image_validate');
        if (isset($_FILES[$var]) && !empty($_FILES[$var]['name'])) {

            $file_type         = $_FILES[$var]['type'];
            $file_size         = $_FILES[$var]["size"];
            $file_name         = $_FILES[$var]["name"];
            $allowed_extension = $image_validate['allowed_extension'];
            $ext               = pathinfo($file_name, PATHINFO_EXTENSION);
            $allowed_mime_type = $image_validate['allowed_mime_type'];
            if ($files = @getimagesize($_FILES[$var]['tmp_name'])) {

                if (!in_array($files['mime'], $allowed_mime_type)) {
                    $this->form_validation->set_message('handle_image_upload', $this->lang->line('file_type_not_allowed'));
                    return false;
                }

                if (!in_array(strtolower($ext), $allowed_extension) || !in_array($file_type, $allowed_mime_type)) {
                    $this->form_validation->set_message('handle_image_upload', $this->lang->line('file_extension_not_allowed'));
                    return false;
                }
                
                if ($file_size > $image_validate['upload_size']) {
                    $this->form_validation->set_message('handle_image_upload', $this->lang->line('file_size_shoud_be_less_than') . number_format($image_validate['upload_size'] / 1048576, 2) . " MB");
                    return false;
                }
            } else {
                $this->form_validation->set_message('handle_image_upload', $this->lang->line('file_type_extension_not_allowed'));
                return false;
            }

            return true;
        }
        return true;
    }

}