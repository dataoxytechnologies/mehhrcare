<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Staffidcard extends Admin_Controller
{
	public $sch_setting_detail;


    public function __construct()
    {
        parent::__construct();
        $this->load->model(array('Staffidcard_model'));
        $this->load->library('SaasValidation');
        $this->config->load('image_valid');
        $this->sch_setting_detail = $this->setting_model->getSetting();
    }

    public function index()
    {
        if (!$this->rbac->hasPrivilege('staff_id_card', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'Certificate');
        $this->session->set_userdata('sub_menu', 'admin/staffidcard');
        $data['staffidcardlist'] = $this->Staffidcard_model->staffidcardlist();
        $data['module'] = 'certificate';
        $this->load->view('layout/header', $data);
        $this->load->view('admin/staffidcard/staffidcardview', $data);
        $this->load->view('layout/footer', $data);
    }

    /**
     * SaaS storage pre-check (form_validation callback).
     * Pass a comma-separated list of $_FILES field names; blocks the save when
     * their combined size would push the tenant over its storage quota.
     */
    public function validateCanUploadFile($str, $params_string)
    {
        $storage_array = array_map('trim', explode(',', $params_string));
        return $this->saasvalidation->validateCanUploadFile($str, $storage_array);
    }

    public function create()
    {
        if (!$this->rbac->hasPrivilege('staff_id_card', 'can_add')) {
            access_denied();
        }
        $this->form_validation->set_rules('hospital_name', $this->lang->line('hospital_name'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('address', $this->lang->line('address_phone_email'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('title', $this->lang->line('id_card_title'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('background_image', $this->lang->line('background_image'), 'callback_background_handle_upload|callback_validateCanUploadFile[background_image,logo_img,sign_image]');
        $this->form_validation->set_rules('logo_img', $this->lang->line('logo_image'), 'callback_logo_handle_upload');
        $this->form_validation->set_rules('sign_image', $this->lang->line('signature_image'), 'callback_signature_handle_upload');

        if ($this->form_validation->run() == false) {
            $this->data['staffidcardlist'] = $this->Staffidcard_model->staffidcardlist();
            $data['module'] = 'certificate';
            $this->load->view('layout/header', $data);
            $this->load->view('admin/staffidcard/staffidcardview', $this->data);
            $this->load->view('layout/footer', $data);
        } else {
            $staff_id          = 0;
            $department        = 0;
            $designation       = 0;
            $name              = 0;
            $fathername        = 0;
            $mothername        = 0;
            $date_of_joining   = 0;
            $permanent_address = 0;
            $phone             = 0;
            $dob               = 0;
            $barcode           = 0;
            if ($this->input->post('is_active_staff_id', TRUE) == 1) {
                $staff_id = $this->input->post('is_active_staff_id', TRUE);
            }
            if ($this->input->post('is_active_department', TRUE) == 1) {
                $department = $this->input->post('is_active_department', TRUE);
            }
            if ($this->input->post('is_active_designation', TRUE) == 1) {
                $designation = $this->input->post('is_active_designation', TRUE);
            }
            if ($this->input->post('is_active_staff_name', TRUE) == 1) {
                $name = $this->input->post('is_active_staff_name', TRUE);
            }
            if ($this->input->post('is_active_staff_father_name', TRUE) == 1) {
                $fathername = $this->input->post('is_active_staff_father_name', TRUE);
            }
            if ($this->input->post('is_active_staff_mother_name', TRUE) == 1) {
                $mothername = $this->input->post('is_active_staff_mother_name', TRUE);
            }
            if ($this->input->post('is_active_date_of_joining', TRUE) == 1) {
                $date_of_joining = $this->input->post('is_active_date_of_joining', TRUE);
            }
            if ($this->input->post('is_active_staff_permanent_address', TRUE) == 1) {
                $permanent_address = $this->input->post('is_active_staff_permanent_address', TRUE);
            }
            if ($this->input->post('is_active_staff_phone', TRUE) == 1) {
                $phone = $this->input->post('is_active_staff_phone', TRUE);
            }
            if ($this->input->post('is_active_staff_dob', TRUE) == 1) {
                $dob = $this->input->post('is_active_staff_dob', TRUE);
            }
            if ($this->input->post('is_active_staff_barcode', TRUE) == 1) {
                $barcode = $this->input->post('is_active_staff_barcode', TRUE);
            }
            $data = array(
                'title'                    => $this->input->post('title', TRUE),
                'hospital_name'            => $this->input->post('hospital_name', TRUE),
                'hospital_address'         => $this->input->post('address', TRUE),
                'header_color'             => $this->input->post('header_color', TRUE),
                'enable_staff_id'          => $staff_id,
                'enable_staff_department'  => $department,
                'enable_designation'       => $designation,
                'enable_name'              => $name,
                'enable_fathers_name'      => $fathername,
                'enable_mothers_name'      => $mothername,
                'enable_date_of_joining'   => $date_of_joining,
                'enable_permanent_address' => $permanent_address,
                'enable_staff_dob'         => $dob,
                'enable_staff_phone'       => $phone,
                'enable_staff_barcode'     => $barcode,
                'status'                   => 1,
            );
            $insert_id = $this->Staffidcard_model->addstaffidcard($data);
           
            if (isset($_FILES["background_image"]) && !empty($_FILES['background_image']['name'])) {
                $background = $this->media_storage->fileupload("background_image",'./uploads/staff_id_card/background/');
            } else{
                $background="";
            }  

            if (isset($_FILES["logo_img"]) && !empty($_FILES['logo_img']['name'])) {
                $logo_img = $this->media_storage->fileupload("logo_img",'./uploads/staff_id_card/logo/');
            } else{
                $logo_img="";
            }  

            if (isset($_FILES["sign_image"]) && !empty($_FILES['sign_image']['name'])) {
                $sign_image = $this->media_storage->fileupload("sign_image",'./uploads/staff_id_card/signature/');
            } else{
                $sign_image="";
            }  

            $upload_data = array('id' => $insert_id, 'logo' => $logo_img, 'background' => $background, 'sign_image' => $sign_image);
            $this->Staffidcard_model->addstaffidcard($upload_data);

            // SaaS: add the combined size of any uploaded images to the storage quota usage.
            if (!IsNullOrEmptyString($background) || !IsNullOrEmptyString($logo_img) || !IsNullOrEmptyString($sign_image)) {
                try {
                    $this->saasvalidation->updateStorageLimit('storage', ['background_image', 'logo_img', 'sign_image']);
                } catch (Exception $e) {
                    log_message('error', 'SaaS storage quota update failed (staffidcard create): ' . $e->getMessage());
                }
            }

            $this->session->set_flashdata('msg', '<div class="alert alert-success text-left">' . $this->lang->line('success_message') . '</div>');
            redirect('admin/staffidcard/index');
        }
    }

    public function edit($id)
    {
        if (!$this->rbac->hasPrivilege('staff_id_card', 'can_edit')) {
            access_denied();
        }
        $data['id']                    = $id;
        $editstaffidcard               = $this->Staffidcard_model->get($id);
        $this->data['editstaffidcard'] = $editstaffidcard;
        $this->form_validation->set_rules('hospital_name', $this->lang->line('hospital_name'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('address', $this->lang->line('address_phone_email'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('title', $this->lang->line('id_card_title'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('background_image', $this->lang->line('background_image'), 'callback_background_handle_upload|callback_validateCanUploadFile[background_image,logo_img,sign_image]');
        $this->form_validation->set_rules('logo_img', $this->lang->line('logo_image'), 'callback_logo_handle_upload');
        $this->form_validation->set_rules('sign_image', $this->lang->line('signature_image'), 'callback_signature_handle_upload');

        if ($this->form_validation->run() == false) {
            $this->data['staffidcardlist'] = $this->Staffidcard_model->staffidcardlist();
            $data['module'] = 'certificate';
            $this->load->view('layout/header', $data);
            $this->load->view('admin/staffidcard/staffidcardedit', $this->data);
            $this->load->view('layout/footer', $data);
        } else {
            $staff_id          = 0;
            $department        = 0;
            $designation       = 0;
            $name              = 0;
            $fathername        = 0;
            $mothername        = 0;
            $date_of_joining   = 0;
            $permanent_address = 0;
            $phone             = 0;
            $dob               = 0;
            $barcode           = 0;
            if ($this->input->post('is_active_staff_id', TRUE) == 1) {
                $staff_id = $this->input->post('is_active_staff_id', TRUE);
            }
            if ($this->input->post('is_active_department', TRUE) == 1) {
                $department = $this->input->post('is_active_department', TRUE);
            }
            if ($this->input->post('is_active_designation', TRUE) == 1) {
                $designation = $this->input->post('is_active_designation', TRUE);
            }
            if ($this->input->post('is_active_staff_name', TRUE) == 1) {
                $name = $this->input->post('is_active_staff_name', TRUE);
            }
            if ($this->input->post('is_active_staff_father_name', TRUE) == 1) {
                $fathername = $this->input->post('is_active_staff_father_name', TRUE);
            }
            if ($this->input->post('is_active_staff_mother_name', TRUE) == 1) {
                $mothername = $this->input->post('is_active_staff_mother_name', TRUE);
            }
            if ($this->input->post('is_active_date_of_joining', TRUE) == 1) {
                $date_of_joining = $this->input->post('is_active_date_of_joining', TRUE);
            }
            if ($this->input->post('is_active_staff_permanent_address', TRUE) == 1) {
                $permanent_address = $this->input->post('is_active_staff_permanent_address', TRUE);
            }
            if ($this->input->post('is_active_staff_phone', TRUE) == 1) {
                $phone = $this->input->post('is_active_staff_phone', TRUE);
            }
            if ($this->input->post('is_active_staff_dob', TRUE) == 1) {
                $dob = $this->input->post('is_active_staff_dob', TRUE);
            }
            if ($this->input->post('is_active_staff_barcode', TRUE) == 1) {
                $barcode = $this->input->post('is_active_staff_barcode', TRUE);
            }

            $remove_background_image    = $this->input->post('remove_background_image', TRUE);
            $remove_logo                = $this->input->post('remove_logo', TRUE);
            $remove_sign_image          = $this->input->post('remove_sign_image', TRUE);

            if ($remove_background_image != '') {
                $data['background'] = '';
            }

            if ($remove_logo != '') {
                $data['logo'] = '';
            }

            if ($remove_sign_image != '') {
                $data['sign_image'] = '';
            }            


                if (isset($_FILES["background_image"]) && !empty($_FILES['background_image']['name'])) {
                    $data['background'] = $this->media_storage->fileupload("background_image",'./uploads/staff_id_card/background/');
                }  

                if (isset($_FILES["logo_img"]) && !empty($_FILES['logo_img']['name'])) {
                     $data['logo']= $this->media_storage->fileupload("logo_img",'./uploads/staff_id_card/logo/');
                }
    
                if (isset($_FILES["sign_image"]) && !empty($_FILES['sign_image']['name'])) {
                    $data['sign_image']= $this->media_storage->fileupload("sign_image",'./uploads/staff_id_card/signature/');
                } 

            
                $data['id']                       = $this->input->post('id', TRUE);
                $data['title']                     = $this->input->post('title', TRUE);
                $data['hospital_name']             = $this->input->post('hospital_name', TRUE);
                $data['hospital_address']          = $this->input->post('address', TRUE);
                $data['header_color']              = $this->input->post('header_color', TRUE);
                $data['enable_staff_id']           = $staff_id;
                $data['enable_staff_department']   = $department;
                $data['enable_designation']        = $designation;
                $data['enable_name']               = $name;
                $data['enable_fathers_name']       = $fathername;
                $data['enable_mothers_name']       = $mothername;
                $data['enable_date_of_joining']    = $date_of_joining;
                $data['enable_permanent_address']  = $permanent_address;
                $data['enable_staff_dob']          = $dob;
                $data['enable_staff_phone']        = $phone;
                $data['enable_staff_barcode']      = $barcode;
                $data['status']                    = 1;
           
            $this->Staffidcard_model->addstaffidcard($data);

            // SaaS: per-image quota adjust (replace = diff, remove = release) and
            // clean up the replaced/removed physical files. Dirs are fixed per column.
            $old_card    = (!empty($editstaffidcard)) ? $editstaffidcard[0] : null;
            $saas_images = array(
                array('file' => 'background_image', 'col' => 'background', 'dir' => 'uploads/staff_id_card/background', 'remove' => 'remove_background_image'),
                array('file' => 'logo_img',         'col' => 'logo',       'dir' => 'uploads/staff_id_card/logo',       'remove' => 'remove_logo'),
                array('file' => 'sign_image',       'col' => 'sign_image', 'dir' => 'uploads/staff_id_card/signature',  'remove' => 'remove_sign_image'),
            );
            foreach ($saas_images as $img) {
                try {
                    $old_file = ($old_card !== null && !empty($old_card->{$img['col']})) ? $old_card->{$img['col']} : '';
                    $old_kb   = (!empty($old_file)) ? $this->media_storage->getUploadedFileSize($old_file, $img['dir']) : 0;
                    $new_uploaded = isset($_FILES[$img['file']]) && !empty($_FILES[$img['file']]['name']);
                    $removed      = $this->input->post($img['remove'], TRUE) != '';

                    if ($new_uploaded) {
                        $new_kb = $this->media_storage->getTmpFileSize($img['file']);
                        if (!empty($old_file)) {
                            $this->media_storage->filedelete($old_file, $img['dir']);
                        }
                        if ($old_kb > $new_kb) {
                            $this->saasvalidation->deleteResouceQuota('storage', $old_kb - $new_kb);
                        } elseif ($new_kb > $old_kb) {
                            $this->saasvalidation->updateResouceQuota('storage', $new_kb - $old_kb);
                        }
                    } elseif ($removed && !empty($old_file)) {
                        if ($old_kb > 0) {
                            $this->saasvalidation->deleteResouceQuota('storage', $old_kb);
                        }
                        $this->media_storage->filedelete($old_file, $img['dir']);
                    }
                } catch (Exception $e) {
                    log_message('error', 'SaaS storage quota update failed (staffidcard edit ' . $img['col'] . '): ' . $e->getMessage());
                }
            }

            $this->session->set_flashdata('msg', '<div class="alert alert-success text-left">' . $this->lang->line('update_message') . '</div>');
            redirect('admin/staffidcard');
        }
    }

    public function delete($id)
    {
        $data['title'] = 'Certificate List';

        // SaaS: release storage for all 3 images and remove the physical files.
        $rows = $this->Staffidcard_model->get($id);
        if (!empty($rows)) {
            $row         = $rows[0];
            $saas_images = array(
                array('col' => 'background', 'dir' => 'uploads/staff_id_card/background'),
                array('col' => 'logo',       'dir' => 'uploads/staff_id_card/logo'),
                array('col' => 'sign_image', 'dir' => 'uploads/staff_id_card/signature'),
            );
            foreach ($saas_images as $img) {
                if (!empty($row->{$img['col']})) {
                    $kb = $this->media_storage->getUploadedFileSize($row->{$img['col']}, $img['dir']);
                    if ($kb > 0) {
                        try {
                            $this->saasvalidation->deleteResouceQuota('storage', $kb);
                        } catch (Exception $e) {
                            log_message('error', 'SaaS storage quota release failed (staffidcard delete ' . $img['col'] . '): ' . $e->getMessage());
                        }
                    }
                    $this->media_storage->filedelete($row->{$img['col']}, $img['dir']);
                }
            }
        }

        $this->Staffidcard_model->remove($id);
        $this->session->set_flashdata('msg', '<div class="alert alert-success text-left">' . $this->lang->line('delete_message') . '</div>');
        redirect('admin/staffidcard/index');
    }

    public function view()
    {
        $id                 = $this->input->post('certificateid', TRUE);
        $data['idcard']     = $this->Staffidcard_model->idcardbyid($id);
        $data['scan_type']  = $this->sch_setting_detail->scan_code_type;
        $this->load->view('admin/staffidcard/staffidcardpreview', $data);
    }

    public function background_handle_upload()
    {

        $image_validate = $this->config->item('image_validate');

        if (isset($_FILES["background_image"]) && !empty($_FILES['background_image']['name'])) {

            $file_type         = $_FILES["background_image"]['type'];
            $file_size         = $_FILES["background_image"]["size"];
            $file_name         = $_FILES["background_image"]["name"];
            $allowed_extension = $image_validate['allowed_extension'];
            $ext               = pathinfo($file_name, PATHINFO_EXTENSION);
            $allowed_mime_type = $image_validate['allowed_mime_type'];
            if ($files = @getimagesize($_FILES['background_image']['tmp_name'])) {

                if (!in_array($files['mime'], $allowed_mime_type)) {
                    $this->form_validation->set_message('background_handle_upload', $this->lang->line('file_type_not_allowed'));
                    return false;
                }

                if (!in_array(strtolower($ext), $allowed_extension) || !in_array($file_type, $allowed_mime_type)) {
                    $this->form_validation->set_message('background_handle_upload', $this->lang->line('file_extension_not_allowed'));
                    return false;
                }
                if ($file_size > $image_validate['upload_size']) {
                    $this->form_validation->set_message('background_handle_upload', $this->lang->line('file_size_shoud_be_less_than') . number_format($image_validate['upload_size'] / 1048576, 2) . " MB");
                    return false;
                }
            } else {
                $this->form_validation->set_message('background_handle_upload', $this->lang->line('file_type_extension_not_allowed'));
                return false;
            }

            return true;
        }
        return true;
    }

    public function logo_handle_upload()
    {

        $image_validate = $this->config->item('image_validate');

        if (isset($_FILES["logo_img"]) && !empty($_FILES['logo_img']['name'])) {

            $file_type         = $_FILES["logo_img"]['type'];
            $file_size         = $_FILES["logo_img"]["size"];
            $file_name         = $_FILES["logo_img"]["name"];
            $allowed_extension = $image_validate['allowed_extension'];
            $ext               = pathinfo($file_name, PATHINFO_EXTENSION);
            $allowed_mime_type = $image_validate['allowed_mime_type'];
            if ($files = @getimagesize($_FILES['logo_img']['tmp_name'])) {

                if (!in_array($files['mime'], $allowed_mime_type)) {
                    $this->form_validation->set_message('logo_handle_upload', $this->lang->line('file_type_not_allowed'));
                    return false;
                }

                if (!in_array(strtolower($ext), $allowed_extension) || !in_array($file_type, $allowed_mime_type)) {
                    $this->form_validation->set_message('logo_handle_upload', $this->lang->line('file_extension_not_allowed'));
                    return false;
                }
                if ($file_size > $image_validate['upload_size']) {
                    $this->form_validation->set_message('logo_handle_upload', $this->lang->line('file_size_shoud_be_less_than') . number_format($image_validate['upload_size'] / 1048576, 2) . " MB");
                    return false;
                }
            } else {
                $this->form_validation->set_message('logo_handle_upload', $this->lang->line('file_type_extension_not_allowed'));
                return false;
            }

            return true;
        }
        return true;
    }

    public function signature_handle_upload()
    {

        $image_validate = $this->config->item('image_validate');

        if (isset($_FILES["sign_image"]) && !empty($_FILES['sign_image']['name'])) {

            $file_type         = $_FILES["sign_image"]['type'];
            $file_size         = $_FILES["sign_image"]["size"];
            $file_name         = $_FILES["sign_image"]["name"];
            $allowed_extension = $image_validate['allowed_extension'];
            $ext               = pathinfo($file_name, PATHINFO_EXTENSION);
            $allowed_mime_type = $image_validate['allowed_mime_type'];
            if ($files = @getimagesize($_FILES['sign_image']['tmp_name'])) {

                if (!in_array($files['mime'], $allowed_mime_type)) {
                    $this->form_validation->set_message('signature_handle_upload', $this->lang->line('file_type_not_allowed'));
                    return false;
                }

                if (!in_array(strtolower($ext), $allowed_extension) || !in_array($file_type, $allowed_mime_type)) {
                    $this->form_validation->set_message('signature_handle_upload', $this->lang->line('file_extension_not_allowed'));
                    return false;
                }
                if ($file_size > $image_validate['upload_size']) {
                    $this->form_validation->set_message('signature_handle_upload', $this->lang->line('file_size_shoud_be_less_than') . number_format($image_validate['upload_size'] / 1048576, 2) . " MB");
                    return false;
                }
            } else {
                $this->form_validation->set_message('signature_handle_upload', $this->lang->line('file_type_extension_not_allowed'));
                return false;
            }

            return true;
        }
        return true;
    }

}
