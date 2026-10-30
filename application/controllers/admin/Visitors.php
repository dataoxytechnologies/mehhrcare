<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Visitors extends Admin_Controller
{
	public $visit_to;


    public function __construct()
    {
        parent::__construct();
        $this->load->library('form_validation');
        $this->load->library('datatables');
        $this->load->library('SaasValidation');
        $this->config->load("image_valid");
        $this->config->load("payroll");
        $this->visit_to = $this->config->item('visit_to');

    }

    public function index()
    {
        if (!$this->rbac->hasPrivilege('visitor_book', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'front_office');
        $this->session->set_userdata('sub_menu', 'admin/visitors');
        $this->form_validation->set_rules('purpose', $this->lang->line('purpose'), 'required|xss_clean');
        $this->form_validation->set_rules('name',$this->lang->line('name'), 'required|xss_clean');
        $this->form_validation->set_rules('date',$this->lang->line('date'), 'required|xss_clean');
        $this->form_validation->set_rules('contact',$this->lang->line('contact'),'numeric|xss_clean');

        if ($this->form_validation->run() == false) {
            $data['Purpose']  = $this->visitors_model->getPurpose();
            $data['visit_to'] = $this->visit_to;
            $data['module'] = 'front_office';
            $this->load->view('layout/header', $data);
            $this->load->view('admin/frontoffice/visitorview', $data);
            $this->load->view('layout/footer', $data);
        } else {
            $date     = $this->input->post('date', TRUE);
            $visitors = array(
                'purpose'      => $this->input->post('purpose', TRUE),
                'name'         => $this->input->post('name', TRUE),
                'contact'      => $this->input->post('contact', TRUE),
                'id_proof'     => $this->input->post('id_proof', TRUE),
                'no_of_pepple' => $this->input->post('pepples', TRUE),
                'date'         => $this->customlib->dateFormatToYYYYMMDD($date),
                'in_time'      => $this->input->post('time', TRUE),
                'out_time'     => $this->input->post('out_time', TRUE),
                'note'         => $this->input->post('note', TRUE),
            );
            $visitor_id = $this->visitors_model->add($visitors);
            if (isset($_FILES["file"]) && !empty($_FILES['file']['name'])) {
                $file_name = $this->media_storage->fileupload("file",'./uploads/front_office/visitors/');
                $this->visitors_model->image_add($visitor_id, $file_name);

                // SaaS: add uploaded file size to the storage quota usage.
                try {
                    $saas_quota_result = $this->saasvalidation->updateStorageLimit('storage', ['file']);
                    // Silent-failure capture: updateStorageLimit does not throw on API
                    // rejection — it returns a JSON string with status:false instead.
                    if (is_string($saas_quota_result)) {
                        $saas_decoded = json_decode($saas_quota_result, true);
                        if (isset($saas_decoded['status']) && $saas_decoded['status'] === false) {
                            log_message('error', 'SaaS storage quota update returned failure (visitors index add): ' . $saas_quota_result);
                        }
                    }
                } catch (Exception $e) {
                    log_message('error', 'SaaS storage quota update failed (visitors index add): ' . $e->getMessage());
                }
            }

            $this->session->set_flashdata('msg', '<div class="alert alert-success">' . $this->lang->line('visitors_added_successfully') . '</div>');
            redirect('admin/visitors');
        }
    }

    public function getvisitorsdatatable()
    {
        $dt_response = $this->visitors_model->getAllvisitorsRecord();
        $dt_response = json_decode($dt_response);
        $dt_data     = array();
        if (!empty($dt_response->data)) {
            foreach ($dt_response->data as $key => $value) {
                $action = '';
                $row = array();
                //====================================

               

                $action .= "<a href='#' onclick=\"getRecord(" . $value->id . ")\" class='btn btn-sm btn-outline-secondary' data-bs-toggle='tooltip' data-bs-placement='top' title='" . $this->lang->line('view') . "'><i class='fa fa-eye'></i></a>";

                if ($this->rbac->hasPrivilege('visitor_book', 'can_edit')) {
                    $action .= "<a href='#' onclick=\"get(" . $value->id . ")\" class='btn btn-sm btn-outline-primary' data-bs-toggle='tooltip' data-bs-placement='top' title='" . $this->lang->line('edit') . "'><i class='fa fa-pencil btn btn-sm btn-outline-secondary'></i></a>";
                }

                if ($value->image !== "") {
                    $action .= "<a href='" . base_url() . 'admin/visitors/download/' . $value->id . "' class='btn btn-sm btn-outline-secondary' data-bs-toggle='tooltip' data-bs-placement='top' title='" . $this->lang->line('download') . "'><i class='fa fa-download'></i></a>";
                }

                if ($this->rbac->hasPrivilege('visitor_book', 'can_delete')) {
                    if ($value->image !== "") {
                        $action .= "<a href='#' onclick=\"deletevisitorimage('" . $value->id . "')\" class='btn btn-sm btn-outline-danger' data-bs-toggle='tooltip' data-bs-placement='top' title='" . $this->lang->line('delete') . "'><i class='fa fa-trash'></i></a>";
                    } else {
                        $action .= "<a href='#' onclick=\"deletevisitor('" . $value->id . "')\" class='btn btn-sm btn-outline-danger' data-bs-toggle='tooltip' data-bs-placement='top' title='" . $this->lang->line('delete') . "'><i class='fa fa-trash'></i></a>";
                    }
                }
                
                if($value->visit_to){
                    $visit_to   =   $this->lang->line($value->visit_to);
                }else{
                    $visit_to   =   '';
                }

                //==============================
                $row[]         = $value->purpose;
                $row[]         = $value->name;
                $row[]         = $visit_to;
                $ipd_opd_staff = '';
                if ($value->visit_to == 'staff') {
                    $ipd_opd_staff = composeStaffNameByString($value->staff_name, $value->surname, $value->employee_id);
                } else if ($value->visit_to == 'opd_patient') {
                    $ipd_opd_staff = composePatientName($value->opd_patient_name, $value->opd_patient_id) . ' (' . $this->customlib->getSessionPrefixByType('opd_no') . $value->opd_no . ')';

                } else if ($value->visit_to == 'ipd_patient') {
                    $ipd_opd_staff = composePatientName($value->ipd_patient_name, $value->ipd_patient_id) . ' (' . $this->customlib->getSessionPrefixByType('ipd_no') . $value->ipd_no . ')';
                }
                $row[]     = $ipd_opd_staff;
                $row[]     = $value->contact;
                $row[]     = $this->customlib->YYYYMMDDTodateFormat($value->date);
                $row[]     = $value->in_time;
                $row[]     = $value->out_time;
                $row[]     = " <div class='d-inline-flex flex-wrap gap-1'>" . $action . "</div>";
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

    /**
     * SaaS: release a visitor's image from the storage quota. Returns the
     * image filename so the caller can delete the physical file when the
     * model does not (model->delete leaves it; model->image_delete unlinks it).
     */
    private function releaseVisitorImageQuota($id)
    {
        $existing  = $this->visitors_model->visitors_list($id);
        $old_image = (!empty($existing['image'])) ? $existing['image'] : '';
        if (!empty($old_image)) {
            $kb = $this->media_storage->getUploadedFileSize($old_image, 'uploads/front_office/visitors');
            if ($kb > 0) {
                try {
                    $this->saasvalidation->deleteResouceQuota('storage', $kb);
                } catch (Exception $e) {
                    log_message('error', 'SaaS storage quota release failed (visitors): ' . $e->getMessage());
                }
            }
        }
        return $old_image;
    }

    public function add()
    {
        $this->form_validation->set_rules('purpose', $this->lang->line('purpose'), 'required|xss_clean');
        $this->form_validation->set_rules('contact', $this->lang->line('phone'), 'numeric|xss_clean');
        $this->form_validation->set_rules('name', $this->lang->line('name'), 'required|xss_clean');
        $this->form_validation->set_rules('date', $this->lang->line('date'), 'required|xss_clean');
        $this->form_validation->set_message('check_default', $this->lang->line('purpose'));
        $this->form_validation->set_rules('file', $this->lang->line('documents'), 'callback_handle_upload|callback_validateCanUploadFile[file]');
        if ($this->form_validation->run() == false) {
            $msg = array(
                'e1' => form_error('purpose'),
                'e2' => form_error('name'),
                'e3' => form_error('date'),
                'e4' => form_error('check_default'),
                'e5' => form_error('file'),
                'e6' => form_error('contact'),
            );

            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $date     = $this->input->post('date', TRUE);
            $visitors = array(
                'purpose'          => $this->input->post('purpose', TRUE),
                'name'             => $this->input->post('name', TRUE),
                'contact'          => $this->input->post('contact', TRUE),
                'id_proof'         => $this->input->post('id_proof', TRUE),
                'visit_to'         => $this->input->post('visit_to', TRUE),
                'ipd_opd_staff_id' => $this->input->post('ipd_opd_staff', TRUE),
                'related_to'       => $this->input->post('related_to', TRUE),
                'no_of_pepple'     => $this->input->post('pepples', TRUE),
                'date'             => $this->customlib->dateFormatToYYYYMMDD($date),
                'in_time'          => $this->input->post('time', TRUE),
                'out_time'         => $this->input->post('out_time', TRUE),
                'note'             => $this->input->post('note', TRUE),
            );
            $visitor_id = $this->visitors_model->add($visitors);

            if (isset($_FILES["file"]) && !empty($_FILES['file']['name'])) {
                $file_name = $this->media_storage->fileupload("file", "./uploads/front_office/visitors/");
                if (!IsNullOrEmptyString($file_name)) {
                    $this->visitors_model->image_add($visitor_id, $file_name);

                    // SaaS: add uploaded file size to the storage quota usage.
                    try {
                        $saas_quota_result = $this->saasvalidation->updateStorageLimit('storage', ['file']);
                        // Silent-failure capture: updateStorageLimit does not throw on API
                        // rejection — it returns a JSON string with status:false instead.
                        if (is_string($saas_quota_result)) {
                            $saas_decoded = json_decode($saas_quota_result, true);
                            if (isset($saas_decoded['status']) && $saas_decoded['status'] === false) {
                                log_message('error', 'SaaS storage quota update returned failure (visitors add): ' . $saas_quota_result);
                            }
                        }
                    } catch (Exception $e) {
                        log_message('error', 'SaaS storage quota update failed (visitors add): ' . $e->getMessage());
                    }
                }
            }

            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
        }

        echo json_encode($array);
    }

    public function delete($id)
    {
        if (!$this->rbac->hasPrivilege('visitor_book', 'can_delete')) {
            access_denied();
        }

        // SaaS: release the image's storage and remove the physical file (model->delete leaves it).
        $img = $this->releaseVisitorImageQuota($id);
        if (!empty($img)) {
            $this->media_storage->filedelete($img, 'uploads/front_office/visitors');
        }

        $this->visitors_model->delete($id);
    }

    public function deletevisitor()
    {
		if (!$this->rbac->hasPrivilege('visitor_book', 'can_delete')) {
            access_denied();
        }
        $id = $this->input->post('id');

        // SaaS: release the image's storage and remove the physical file (model->delete leaves it).
        $img = $this->releaseVisitorImageQuota($id);
        if (!empty($img)) {
            $this->media_storage->filedelete($img, 'uploads/front_office/visitors');
        }

        $this->visitors_model->delete($id);
        $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('delete_message'));
        echo 1;
    }

    public function deletevisitorimage()
    {
		if (!$this->rbac->hasPrivilege('visitor_book', 'can_delete')) {
            access_denied();
        }
        $id    = $this->input->post('id');

        // SaaS: release the image's storage (model->image_delete removes the file itself).
        $this->releaseVisitorImageQuota($id);

        $this->visitors_model->image_delete($id);
        $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('delete_message'));
        echo 1;
    }

    public function edit()
    {
        if (!$this->rbac->hasPrivilege('visitor_book', 'can_edit')) {
            access_denied();
        }

        $id = $this->input->post('id');
        $this->form_validation->set_rules('purpose', $this->lang->line('purpose'), 'required|callback_check_default|xss_clean');
        $this->form_validation->set_rules('name', $this->lang->line('name'), 'required|xss_clean');
        $this->form_validation->set_rules('date', $this->lang->line('date'), 'required|xss_clean');
        $this->form_validation->set_message('check_default', $this->lang->line('the_purpose_field_requred'));
        $this->form_validation->set_rules('file', $this->lang->line('documents'), 'callback_handle_upload|callback_validateCanUploadFile[file]');
        $this->form_validation->set_rules('contact',$this->lang->line('contact'),'numeric|xss_clean');

        if ($this->form_validation->run() == false) {
            $msg = array(
                'e1' => form_error('purpose'),
                'e2' => form_error('name'),
                'e3' => form_error('date'),
                'e4' => form_error('check_default'),
                'e5' => form_error('file'),
                'e6' => form_error('contact'),
            );
            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $date     = $this->input->post('date', TRUE);
            $visitors = array(
                'purpose'          => $this->input->post('purpose', TRUE),
                'name'             => $this->input->post('name', TRUE),
                'contact'          => $this->input->post('contact', TRUE),
                'id_proof'         => $this->input->post('id_proof', TRUE),
                'visit_to'         => $this->input->post('visit_to', TRUE),
                'ipd_opd_staff_id' => $this->input->post('ipd_opd_staff', TRUE),
                'related_to'       => $this->input->post('related_to', TRUE),
                'no_of_pepple'     => $this->input->post('pepples', TRUE),
                'date'             => $this->customlib->dateFormatToYYYYMMDD($date),
                'in_time'          => $this->input->post('time', TRUE),
                'out_time'         => $this->input->post('out_time', TRUE),
                'note'             => $this->input->post('note', TRUE),
            );

            if (isset($_FILES["file"]) && !empty($_FILES['file']['name'])) {
                // SaaS: capture the existing image for quota diff + cleanup.
                $existing  = $this->visitors_model->visitors_list($id);
                $old_image = (!empty($existing['image'])) ? $existing['image'] : '';
                $old_kb    = (!empty($old_image)) ? $this->media_storage->getUploadedFileSize($old_image, 'uploads/front_office/visitors') : 0;

                $file_name = $this->media_storage->fileupload("file",'./uploads/front_office/visitors/');
                if (!IsNullOrEmptyString($file_name)) {
                    if (!empty($old_image)) {
                        $this->media_storage->filedelete($old_image, 'uploads/front_office/visitors');
                    }
                    $this->visitors_model->image_update($id, $file_name);

                    // SaaS: adjust storage quota by the size difference (new vs replaced).
                    try {
                        $new_kb = $this->media_storage->getTmpFileSize('file');
                        if ($old_kb > $new_kb) {
                            $this->saasvalidation->deleteResouceQuota('storage', $old_kb - $new_kb);
                        } elseif ($new_kb > $old_kb) {
                            $this->saasvalidation->updateResouceQuota('storage', $new_kb - $old_kb);
                        }
                    } catch (Exception $e) {
                        log_message('error', 'SaaS storage quota update failed (visitors edit): ' . $e->getMessage());
                    }
                }
            }

            $this->visitors_model->update($id, $visitors);
            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('update_message'));
        }
        echo json_encode($array);
    }

    public function details($id)
    {
        if (!$this->rbac->hasPrivilege('visitor_book', 'can_view')) {
            access_denied();
        }

        $data['data'] = $this->visitors_model->visitors_list($id);
        $this->load->view('admin/frontoffice/Visitormodelview', $data);
    }

    public function download($id)
    {
        $visitors_listdata = $this->visitors_model->visitors_list($id); 
        $this->media_storage->filedownload($visitors_listdata['image'],'./uploads/front_office/visitors/');
    }

    public function imagedelete($id, $image)
    {
        if (!$this->rbac->hasPrivilege('visitor_book', 'can_delete')) {
            access_denied();
        }

        // SaaS: release the image's storage (model->image_delete removes the file itself).
        $this->releaseVisitorImageQuota($id);

        $this->visitors_model->image_delete($id, $image);
    }

    public function check_default($post_string)
    {
        return $post_string == "" ? false : true;
    }

    public function get_visitor($id)
    {
        $data   = $this->visitors_model->visitors_list($id);
        $a      = array('datedd' => $this->customlib->YYYYMMDDTodateFormat($data['date']));
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

    public function get_ipd_opd_staff_list()
    {
        $visit_to = $this->input->get('visit_to', TRUE);
        $result   = array();
        if ($visit_to == 'staff') {
            $result = $this->visitors_model->getstaff();
        } elseif ($visit_to == 'ipd_patient') {
            $result = $this->visitors_model->getipd();
        } elseif ($visit_to == 'opd_patient') {
            $result = $this->visitors_model->getopd();
        }
        echo json_encode(array('status' => 1, 'data' => $result, 'visit_to' => $visit_to));
    }

}
