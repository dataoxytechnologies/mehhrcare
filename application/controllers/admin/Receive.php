<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Receive extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('form_validation');
        $this->load->library('datatables');
        $this->load->library('SaasValidation');
        $this->config->load("image_valid");
    }

    public function index()
    {
        if (!$this->rbac->hasPrivilege('postal_receive', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'front_office');
        $this->session->set_userdata('sub_menu', 'admin/receive');
        $this->form_validation->set_rules('from_title', $this->lang->line('from_title'), 'trim|required|xss_clean');
        if ($this->form_validation->run() == false) {
            $data['module'] = 'front_office';
            $this->load->view('layout/header', $data);
            $this->load->view('admin/frontoffice/receiveview', $data);
            $this->load->view('layout/footer', $data);
        } else {
            $date     = $this->input->post('date', TRUE);
            $dispatch = array(
                'reference_no' => $this->input->post('ref_no', TRUE),
                'to_title'     => $this->input->post('to_title', TRUE),
                'address'      => $this->input->post('address', TRUE),
                'note'         => $this->input->post('note', TRUE),
                'from_title'   => $this->input->post('from_title', TRUE),
                'date'         => $this->customlib->dateFormatToYYYYMMDD($date),
                'type'         => 'receive',
            );
            $dispatch_id = $this->dispatch_model->insert('dispatch_receive', $dispatch);

            if (isset($_FILES["file"]) && !empty($_FILES['file']['name'])) {
                $file_name = $this->media_storage->fileupload("file",'./uploads/front_office/dispatch_receive/');
                $this->dispatch_model->image_add('receive', $dispatch_id, $file_name);

                // SaaS: add uploaded file size to the storage quota usage.
                try {
                    $this->saasvalidation->updateStorageLimit('storage', ['file']);
                } catch (Exception $e) {
                    log_message('error', 'SaaS storage quota update failed (receive add): ' . $e->getMessage());
                }
            }

            $this->session->set_flashdata('msg', '<div class="alert alert-success">' . $this->lang->line('receive_added_successfully') . '</div>');
            redirect('admin/receive');
        }
    }

    public function getreceivedatatable()
    {
        $dt_response = $this->dispatch_model->getAllreceiveRecord();
        $dt_response = json_decode($dt_response);
        $dt_data     = array();
        if (!empty($dt_response->data)) {
            foreach ($dt_response->data as $key => $value) {

                $row    = array();
                $action = '';
                //====================================
                $action .= "<a href='#' onclick='getRecord(" . $value->id . ")' class='btn btn-sm btn-outline-secondary' data-bs-toggle='tooltip' data-bs-placement='top' title='" . $this->lang->line('view') . "'><i class='fa fa-reorder'></i></a>";

                if ($value->image !== "") {
                    $action .= "<a href='" . base_url() . 'admin/receive/download/' . $value->id . "' class='btn btn-sm btn-outline-secondary' data-bs-toggle='tooltip' data-bs-placement='top' title='" . $this->lang->line('download') . "'><i class='fa fa-download'></i></a>";
                }

                if ($this->rbac->hasPrivilege('postal_receive', 'can_edit')) {
                    $action .= "<a href='#' onclick='get(" . $value->id . ")' class='btn btn-sm btn-outline-primary' data-bs-toggle='tooltip' data-bs-placement='top' title='" . $this->lang->line('edit') . "'><i class='fa fa-pencil'></i></a>";
                }

                if ($this->rbac->hasPrivilege('postal_receive', 'can_delete')) {
                    $action .= "<a href='#' onclick='delete_ById(" . $value->id . ")' class='btn btn-sm btn-outline-danger' data-bs-toggle='tooltip' data-bs-placement='top' title='" . $this->lang->line('delete') . "'><i class='fa fa-trash'></i></a>";
                }

                //==============================
                $row[]     = $value->from_title;
                $row[]     = $value->reference_no;
                $row[]     = $value->to_title;                
                $row[]     = $value->address;
                $row[]     = $value->note;
                if($value->date){
                $row[]     = $this->customlib->YYYYMMDDTodateFormat($value->date);
                }else{
                $row[]     = '';    
                }
                $row[]     = "<div class='d-flex flex-wrap justify-content-end gap-1'>" . $action . "</div>";
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

    public function download($id)
    {
        $receive_data = $this->dispatch_model->dis_rec_data($id,'receive'); 
        $this->media_storage->filedownload($receive_data['image'],'./uploads/front_office/dispatch_receive/');
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

    /**
     * SaaS: release a receive record's image from the storage quota. Returns the
     * image filename so the caller can delete the physical file (model->delete
     * does not unlink it).
     */
    private function releaseReceiveImageQuota($id)
    {
        $existing  = $this->dispatch_model->dis_rec_data($id, 'receive');
        $old_image = (!empty($existing['image'])) ? $existing['image'] : '';
        if (!empty($old_image)) {
            $kb = $this->media_storage->getUploadedFileSize($old_image, 'uploads/front_office/dispatch_receive');
            if ($kb > 0) {
                try {
                    $this->saasvalidation->deleteResouceQuota('storage', $kb);
                } catch (Exception $e) {
                    log_message('error', 'SaaS storage quota release failed (receive): ' . $e->getMessage());
                }
            }
        }
        return $old_image;
    }

    public function add()
    {
        $this->form_validation->set_rules('from_title', $this->lang->line('from_title'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('file', $this->lang->line('documents'), 'callback_handle_upload|callback_validateCanUploadFile[file]');
        if ($this->form_validation->run() == false) {
            $msg = array(
                'name' => form_error('from_title'),
                'file' => form_error('file'),
            );
            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $date     = $this->input->post('date', TRUE);
            $dispatch = array(
                'reference_no' => $this->input->post('ref_no', TRUE),
                'to_title'     => $this->input->post('to_title', TRUE),
                'address'      => $this->input->post('address', TRUE),
                'note'         => $this->input->post('note', TRUE),
                'from_title'   => $this->input->post('from_title', TRUE),
                'date'         => $this->customlib->dateFormatToYYYYMMDD($date),
                'type'         => 'receive',
            );
            $dispatch_id = $this->dispatch_model->insert('dispatch_receive', $dispatch);

            if (isset($_FILES["file"]) && !empty($_FILES['file']['name'])) {
                $file_name = $this->media_storage->fileupload("file",'./uploads/front_office/dispatch_receive/');
                $this->dispatch_model->image_add('receive', $dispatch_id, $file_name);

                // SaaS: add uploaded file size to the storage quota usage.
                try {
                    $this->saasvalidation->updateStorageLimit('storage', ['file']);
                } catch (Exception $e) {
                    log_message('error', 'SaaS storage quota update failed (receive add): ' . $e->getMessage());
                }
            }

            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
        }
        echo json_encode($array);
    }

    public function editreceive()
    {

        if (!$this->rbac->hasPrivilege('postal_receive', 'can_view')) {
            access_denied();
        }
        $id = $this->input->post('id', TRUE);
        $this->form_validation->set_rules('from_title', $this->lang->line('from_title'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('file', $this->lang->line('documents'), 'callback_handle_upload|callback_validateCanUploadFile[file]');
        if ($this->form_validation->run() == false) {
            $msg = array(
                'name' => form_error('from_title'),
                'file' => form_error('file'),
            );
            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $date    = $this->input->post('date', TRUE);
            $receive = array(
                'reference_no' => $this->input->post('ref_no', TRUE),
                'from_title'   => $this->input->post('from_title', TRUE),
                'address'      => $this->input->post('address', TRUE),
                'note'         => $this->input->post('note', TRUE),
                'to_title'     => $this->input->post('to_title', TRUE),
                'date'         => $this->customlib->dateFormatToYYYYMMDD($date),
                'type'         => 'receive',
            );
            $this->dispatch_model->update_dispatch('dispatch_receive', $id, 'receive', $receive);

            if (isset($_FILES["file"]) && !empty($_FILES['file']['name'])) {
                // SaaS: capture the existing image for quota diff + cleanup.
                $existing  = $this->dispatch_model->dis_rec_data($id, 'receive');
                $old_image = (!empty($existing['image'])) ? $existing['image'] : '';
                $old_kb    = (!empty($old_image)) ? $this->media_storage->getUploadedFileSize($old_image, 'uploads/front_office/dispatch_receive') : 0;

                $file_name = $this->media_storage->fileupload("file",'./uploads/front_office/dispatch_receive/');
                if (!IsNullOrEmptyString($file_name)) {
                    if (!empty($old_image)) {
                        $this->media_storage->filedelete($old_image, 'uploads/front_office/dispatch_receive');
                    }
                    $this->dispatch_model->image_update('receive', $id, $file_name);

                    // SaaS: adjust storage quota by the size difference (new vs replaced).
                    try {
                        $new_kb = $this->media_storage->getTmpFileSize('file');
                        if ($old_kb > $new_kb) {
                            $this->saasvalidation->deleteResouceQuota('storage', $old_kb - $new_kb);
                        } elseif ($new_kb > $old_kb) {
                            $this->saasvalidation->updateResouceQuota('storage', $new_kb - $old_kb);
                        }
                    } catch (Exception $e) {
                        log_message('error', 'SaaS storage quota update failed (receive edit): ' . $e->getMessage());
                    }
                }
            }

            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('update_message'));
        }
        echo json_encode($array);
    }

    public function delete($id)
    {
        if (!$this->rbac->hasPrivilege('postal_receive', 'can_delete')) {
            access_denied();
        }

        // SaaS: release the image's storage and remove the physical file (model->delete leaves it).
        $img = $this->releaseReceiveImageQuota($id);
        if (!empty($img)) {
            $this->media_storage->filedelete($img, 'uploads/front_office/dispatch_receive');
        }

        $this->dispatch_model->delete($id);
        echo json_encode(array('msg' => $this->lang->line('delete_message')));
    }

    public function imagedelete($id, $image)
    {
        if (!$this->rbac->hasPrivilege('postal_receive', 'can_delete')) {
            access_denied();
        }
        $this->dispatch_model->image_delete($id, $image);
    }

    public function get_receive($id)
    {
        $data = $this->dispatch_model->recevie_data($id);
        $a    = array(
            'datedd' => $this->customlib->YYYYMMDDTodateFormat($data['date']),
        );
        $result = array_merge($a, $data);
        echo json_encode($result);
    }

    public function handle_upload()
    {
        $image_validate = $this->config->item('file_validate');
        if (isset($_FILES["file"]) && !empty($_FILES['file']['name'])) {
            $file_type         = $_FILES["file"]['type'];
            $file_size         = $_FILES["file"]["size"];
            $file_name         = $_FILES["file"]["name"];
            $allowed_extension = $image_validate['allowed_extension'];
            $ext               = pathinfo($file_name, PATHINFO_EXTENSION);
            $allowed_mime_type = $image_validate['allowed_mime_type'];
            if ($files = @filesize($_FILES['file']['tmp_name'])) {
                if (!in_array($file_type, $allowed_mime_type)) {
                    $this->form_validation->set_message('handle_upload', $this->lang->line('file_type_not_allowed'));
                    return false;
                }

                if (!in_array(strtolower($ext), $allowed_extension) || !in_array($file_type, $allowed_mime_type)) {
                    $this->form_validation->set_message('handle_upload', $this->lang->line('file_extension_not_allowed'));
                    return false;
                }
                if ($file_size > $image_validate['upload_size']) {
                    $this->form_validation->set_message('handle_upload', $this->lang->line('file_size_shoud_be_less_than') . number_format($image_validate['upload_size'] / 1048576, 2) . " MB");
                    return false;
                }
            } else {
                $this->form_validation->set_message('handle_upload', $this->lang->line('error_file_uploading'));
                return false;
            }

            return true;
        }
        return true;
    }

}
