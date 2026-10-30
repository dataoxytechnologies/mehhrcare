<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Complaint extends Admin_Controller
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
        if (!$this->rbac->hasPrivilege('complain', 'can_view')) {
            access_denied();
        }

        $this->session->set_userdata('top_menu', 'front_office');
        $this->session->set_userdata('sub_menu', 'admin/complaint');
        $this->form_validation->set_rules('name', $this->lang->line('complaint_by'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('file', $this->lang->line('documents'), 'callback_handle_upload|callback_validateCanUploadFile[file]');

        if ($this->form_validation->run() == false) {
            $data['complaint_type']  = $this->complaint_model->getComplaintType();
            $data['complaintsource'] = $this->complaint_model->getComplaintSource();
            $data['module'] = 'front_office';
            $this->load->view('layout/header', $data);
            $this->load->view('admin/frontoffice/complaintview', $data);
            $this->load->view('layout/footer', $data);
        } else {
            $date      = $this->input->post('date', TRUE);
            $complaint = array(
                'complaint_type_id' => $this->input->post('complaint', TRUE),
                'source'            => $this->input->post('source', TRUE),
                'name'              => $this->input->post('name', TRUE),
                'contact'           => $this->input->post('contact', TRUE),
                'date'              => $this->customlib->dateFormatToYYYYMMDD($date),
                'description'       => $this->input->post('description', TRUE),
                'action_taken'      => $this->input->post('action_taken', TRUE),
                'assigned'          => $this->input->post('assigned', TRUE),
                'note'              => $this->input->post('note', TRUE),
            );
			
			if ($this->rbac->hasPrivilege('complain', 'can_add')) {
				
				$complaint_id = $this->complaint_model->add($complaint);
			
				if (isset($_FILES["file"]) && !empty($_FILES['file']['name'])) {
					$fileInfo = pathinfo($_FILES["file"]["name"]);
					$img_name = 'id' . $complaint_id . '.' . $fileInfo['extension'];
					move_uploaded_file($_FILES["file"]["tmp_name"], "./uploads/front_office/complaints/" . $img_name);
					$this->complaint_model->image_add($complaint_id, $img_name);

					// SaaS: add uploaded file size to the storage quota usage.
					try {
						$this->saasvalidation->updateStorageLimit('storage', ['file']);
					} catch (Exception $e) {
						log_message('error', 'SaaS storage quota update failed (complaint index add): ' . $e->getMessage());
					}
				}
			}

            $this->session->set_flashdata('msg', '<div class="alert alert-success">' . $this->lang->line('complaint_added_successfully') . ' </div>');
            redirect('admin/complaint');
        }
    }

    public function getcomplaintdatatable()
    {
		if (!$this->rbac->hasPrivilege('complain', 'can_view')) {
            access_denied();
        }
		
        $dt_response = $this->complaint_model->getAllcomplaintRecord();
        $dt_response = json_decode($dt_response);
        $dt_data     = array();
        if (!empty($dt_response->data)) {
            foreach ($dt_response->data as $key => $value) {
                $row    = array();
                $action = '';

                $action .= "<a href='#' onclick='getRecord(" . $value->id . ")' class='btn btn-sm btn-outline-secondary' data-bs-toggle='tooltip' data-bs-placement='top' title='" . $this->lang->line('view') . "'><i class='fa fa-reorder'></i></a>";

                if ($value->image !== "") {
                    $action .= "<a href='" . base_url() . 'admin/complaint/download/' . $value->id . "' class='btn btn-sm btn-outline-secondary' data-bs-toggle='tooltip' data-bs-placement='top' title='" . $this->lang->line('download') . "'><i class='fa fa-download'></i></a>";
                }

                if ($this->rbac->hasPrivilege('complain', 'can_edit')) {
                    $action .= "<a href='#' onclick='get(" . $value->id . ")' class='btn btn-sm btn-outline-primary' data-bs-toggle='tooltip' data-bs-placement='top' title='" . $this->lang->line('edit') . "'><i class='fa fa-pencil'></i></a>";
                }

                if ($this->rbac->hasPrivilege('complain', 'can_delete')) {
                    if ($value->image !== "") {
                        $action .= "<a href='#' onclick='delete_image_record(" . $value->id . ")' class='btn btn-sm btn-outline-danger' data-bs-toggle='tooltip' data-bs-placement='top' title='" . $this->lang->line('delete') . "'><i class='fa fa-trash'></i></a>";
                    } else {
                        $action .= "<a href='#' onclick='delete_record(" . $value->id . ")' class='btn btn-sm btn-outline-danger' data-bs-toggle='tooltip' data-bs-placement='top' title='" . $this->lang->line('delete') . "'><i class='fa fa-trash'></i></a>";
                    }
                }

                if ($value->date) {
                    $date = $this->customlib->YYYYMMDDTodateFormat($value->date);
                } else {
                    $date = '';
                }

                $row[] = $value->id;
                $row[] = $value->complaint_type;
                $row[] = $value->source;
                $row[] = $value->name;
                $row[] = $value->contact;
                $row[] = $date;
                $row[]     = "<div class='d-inline-flex gap-1 justify-content-end flex-nowrap'>" . $action . "</div>";
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

    // SaaS storage pre-check callback — blocks save when upload would exceed quota.
    public function validateCanUploadFile($str, $params_string)
    {
        $storage_array = array_map('trim', explode(',', $params_string));
        return $this->saasvalidation->validateCanUploadFile($str, $storage_array);
    }

    // Returns the image filename after releasing its storage quota (model->delete leaves the physical file).
    private function releaseComplaintImageQuota($id)
    {
        $existing  = $this->complaint_model->complaint_list($id);
        $old_image = (!empty($existing['image'])) ? $existing['image'] : '';
        if (!empty($old_image)) {
            $kb = $this->media_storage->getUploadedFileSize($old_image, 'uploads/front_office/complaints');
            if ($kb > 0) {
                try {
                    $this->saasvalidation->deleteResouceQuota('storage', $kb);
                } catch (Exception $e) {
                    log_message('error', 'SaaS storage quota release failed (complaint): ' . $e->getMessage());
                }
            }
        }
        return $old_image;
    }

    public function add()
    {

		if (!$this->rbac->hasPrivilege('complain', 'can_add')) {
            access_denied();
        }     
		 
        $this->form_validation->set_rules('name', $this->lang->line('complain_by'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('contact', $this->lang->line('phone'), 'trim|numeric|xss_clean');
        $this->form_validation->set_rules('file', $this->lang->line('documents'), 'callback_handle_upload|callback_validateCanUploadFile[file]');

        if ($this->form_validation->run() == false) {
            $msg = array(
                'name'    => form_error('name'),
                'contact' => form_error('contact'),
                'file'    => form_error('file'),
            );

            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $date      = $this->input->post('date', TRUE);

            if ($this->input->post('complaint', TRUE)) {
                $complaint = $this->input->post('complaint', TRUE);
            } else {
                $complaint = null;
            }

            $complaint = array(
                'complaint_type_id' => $complaint,
                'source'            => $this->input->post('source', TRUE),
                'name'              => $this->input->post('name', TRUE),
                'contact'           => $this->input->post('contact', TRUE),
                'date'              => $this->customlib->dateFormatToYYYYMMDD($date),
                'description'       => $this->input->post('description', TRUE),
                'action_taken'      => $this->input->post('action_taken', TRUE),
                'assigned'          => $this->input->post('assigned', TRUE),
                'note'              => $this->input->post('note', TRUE),
            );

            $complaint_id = $this->complaint_model->add($complaint);

            if (isset($_FILES["file"]) && !empty($_FILES['file']['name'])) {
                $file_name = $this->media_storage->fileupload("file", "./uploads/front_office/complaints/");
                if (!IsNullOrEmptyString($file_name)) {
                    $this->complaint_model->image_add($complaint_id, $file_name);

                    // SaaS: add uploaded file size to the storage quota usage.
                    try {
                        $this->saasvalidation->updateStorageLimit('storage', ['file']);
                    } catch (Exception $e) {
                        log_message('error', 'SaaS storage quota update failed (complaint add): ' . $e->getMessage());
                    }
                }
            }
            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
        }
        echo json_encode($array);
    }

    public function edit()
    {
        $id = $this->input->post('id', TRUE);
        if (!$this->rbac->hasPrivilege('complain', 'can_edit')) {
            access_denied();
        }
        $this->form_validation->set_rules('name', $this->lang->line('complain_by'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('file', $this->lang->line('documents'), 'callback_handle_upload|callback_validateCanUploadFile[file]');

        if ($this->form_validation->run() == false) {
            $msg = array(
                'name' => form_error('name'),
                'file' => form_error('file'),
            );

            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $date      = $this->input->post('date', TRUE);
            $complaint = array(
                'complaint_type_id' => $this->input->post('complaint', TRUE),
                'source'            => $this->input->post('source', TRUE),
                'name'              => $this->input->post('name', TRUE),
                'contact'           => $this->input->post('contact', TRUE),
                'date'              => $this->customlib->dateFormatToYYYYMMDD($date),
                'description'       => $this->input->post('description', TRUE),
                'action_taken'      => $this->input->post('action_taken', TRUE),
                'assigned'          => $this->input->post('assigned', TRUE),
                'note'              => $this->input->post('note', TRUE),
            );

            if (isset($_FILES["file"]) && !empty($_FILES['file']['name'])) {
                // SaaS: capture the existing image for quota diff + cleanup.
                $existing  = $this->complaint_model->complaint_list($id);
                $old_image = (!empty($existing['image'])) ? $existing['image'] : '';
                $old_kb    = (!empty($old_image)) ? $this->media_storage->getUploadedFileSize($old_image, 'uploads/front_office/complaints') : 0;

                $file_name = $this->media_storage->fileupload("file", "./uploads/front_office/complaints/");
                if (!IsNullOrEmptyString($file_name)) {
                    if (!empty($old_image)) {
                        $this->media_storage->filedelete($old_image, 'uploads/front_office/complaints');
                    }
                    $this->complaint_model->image_add($id, $file_name);

                    // SaaS: adjust storage quota by the size difference (new vs replaced).
                    try {
                        $new_kb = $this->media_storage->getTmpFileSize('file');
                        if ($old_kb > $new_kb) {
                            $this->saasvalidation->deleteResouceQuota('storage', $old_kb - $new_kb);
                        } elseif ($new_kb > $old_kb) {
                            $this->saasvalidation->updateResouceQuota('storage', $new_kb - $old_kb);
                        }
                    } catch (Exception $e) {
                        log_message('error', 'SaaS storage quota update failed (complaint edit): ' . $e->getMessage());
                    }
                }
            }

            $this->complaint_model->compalaint_update($id, $complaint);
            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('update_message'));
        }
        echo json_encode($array);
    }

    public function details($id)
    {
        if (!$this->rbac->hasPrivilege('complain', 'can_view')) {
            access_denied();
        }

        $data['complaint_data'] = $this->complaint_model->complaint_list($id);

        $this->load->view('admin/frontoffice/Complaintmodalview', $data);
    }

    public function delete($id)
    {
        if (!$this->rbac->hasPrivilege('complain', 'can_delete')) {
            access_denied();
        }

        // SaaS: release the image's storage and remove the physical file (model->delete leaves it).
        $img = $this->releaseComplaintImageQuota($id);
        if (!empty($img)) {
            $this->media_storage->filedelete($img, 'uploads/front_office/complaints');
        }

        $this->complaint_model->delete($id);
        $this->session->set_flashdata('msg', '<div class="alert alert-success">' . $this->lang->line('complaint_deleted_successfully') . ' </div>');
        redirect('admin/complaint');
    }

    public function download($id)
    {
		if (!$this->rbac->hasPrivilege('complain', 'can_download')) {
            access_denied();
        }
        $complaint= $this->complaint_model->complaint_list($id);  
        $this->media_storage->filedownload($complaint['image'], "./uploads/front_office/complaints/");
    }

    public function imagedelete($id)
    {
        if (!$this->rbac->hasPrivilege('complain', 'can_delete')) {
            access_denied();
        }
        $complaint_list=$this->complaint_model->complaint_list($id);
        $image= $complaint_list['image'];

        // SaaS: release the image's storage (model->image_delete removes the file itself).
        if (!empty($image)) {
            $kb = $this->media_storage->getUploadedFileSize($image, 'uploads/front_office/complaints');
            if ($kb > 0) {
                try {
                    $this->saasvalidation->deleteResouceQuota('storage', $kb);
                } catch (Exception $e) {
                    log_message('error', 'SaaS storage quota release failed (complaint imagedelete): ' . $e->getMessage());
                }
            }
        }

        $this->complaint_model->delete($id);
        $this->complaint_model->image_delete($id, "$image");
        echo json_encode(array("status" => 1, "msg" => $this->lang->line("delete_message")));
    }

    public function check_default($post_string)
    {
        return $post_string == "" ? false : true;
    }

    public function get_complaint($id)
    {
        $data = $this->complaint_model->complaint_list($id);
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
