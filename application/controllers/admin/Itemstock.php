<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Itemstock extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->helper('form');
        $this->load->library('datatables');
        $this->load->library('SaasValidation');
        $this->config->load('image_valid');
    }

    public function index()
    {        
        $this->session->set_userdata('top_menu', 'Inventory');
        $this->session->set_userdata('sub_menu', 'Itemstock/index');
        $data['title']      = $this->input->post('add_item', TRUE);
        $data['title_list'] = $this->input->post('recent_items', TRUE);
        $this->form_validation->set_rules('item_id', $this->input->post('item'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('quantity', $this->input->post('quantity'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('item_category_id', $this->input->post('item_category'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('item_photo', $this->lang->line('photo'), 'callback_handle_upload|callback_validateCanUploadFile[item_photo]');
        if ($this->form_validation->run() == false) {

        } else {
            $date     = $this->input->post('date', TRUE);
            $store_id = ($this->input->post('store_id', TRUE)) ? $this->input->post('store_id', TRUE) : null;
            $data     = array(
                'item_id'     => $this->input->post('item_id', TRUE),
                'symbol'      => $this->input->post('symbol', TRUE),
                'supplier_id' => $this->input->post('supplier_id', TRUE),
                'store_id'    => $store_id,
                'quantity'    => $this->input->post('symbol', TRUE) . $this->input->post('quantity', TRUE),
                'date'        => $this->customlib->dateFormatToYYYYMMDD($date),
                'description' => $this->input->post('description', TRUE),
            );

            $insert_id = $this->itemstock_model->add($data);
            if (isset($_FILES["item_photo"]) && !empty($_FILES['item_photo']['name'])) {
                $file_name = $this->media_storage->fileupload("item_photo", './uploads/inventory_items/');
                if (!IsNullOrEmptyString($file_name)) {
                    $data_img = array('id' => $insert_id, 'attachment' => 'uploads/inventory_items/' . $file_name);
                    $this->itemstock_model->add($data_img);
                    try {
                        $this->saasvalidation->updateStorageLimit('storage', ['item_photo']);
                    } catch (Exception $e) {
                        log_message('error', 'SaaS storage quota update failed (itemstock index add): ' . $e->getMessage());
                    }
                }
            }

            $this->session->set_flashdata('msg', '<div class="alert alert-success text-left">' . $this->lang->line('item_added_successfully') . '</div>');
            redirect('admin/itemstock/index');
        }
        $itemcategory         = $this->itemcategory_model->get();
        $data['itemcatlist']  = $itemcategory;
        $itemsupplier         = $this->itemsupplier_model->get();
        $data['itemsupplier'] = $itemsupplier;
        $itemstore            = $this->itemstore_model->get();
        $data['itemstore']    = $itemstore;
        $data['module'] = 'inventory';
        $this->load->view('layout/header', $data);
        $this->load->view('admin/itemstock/itemList', $data);
        $this->load->view('layout/footer', $data);
    }

    public function getitemstockDatatable()
    {
        $dt_response = $this->itemstock_model->getAllitemstockRecord();
        $dt_response = json_decode($dt_response);
        $dt_data     = array();
        if (!empty($dt_response->data)) {
            foreach ($dt_response->data as $key => $value) {

                $row         = array();
                //====================================
                $column_first  = "<a href='#' class='detail_popover' data-bs-toggle='popover' title=''>" . html_escape($value->name) . "</a>";
                $column_first .= "<div class='fee_detail_popover' style='display: none'></div>";

                $action = '';
                if ($value->attachment) {
                    $action .= "<a href='" . base_url() . 'admin/itemstock/download/' . $value->id . "' class='btn btn-sm btn-outline-secondary' data-bs-toggle='tooltip' data-bs-placement='top' title='" . $this->lang->line('download') . "'><i class='fa fa-download'></i></a>";
                }

                if ($this->rbac->hasPrivilege('item_stock', 'can_edit')) {
                    $action .= "<a href='#' onclick='get_data(" . $value->id . ")' class='btn btn-sm btn-outline-primary' data-bs-toggle='tooltip' data-bs-placement='top' title='" . $this->lang->line('edit') . "'><i class='fa fa-pencil'></i></a>";
                }

                if ($this->rbac->hasPrivilege('item_stock', 'can_delete')) {
                    $action .= "<a href='#' onclick='delete_record(" . $value->id . ")' class='btn btn-sm btn-outline-danger' data-bs-toggle='tooltip' data-bs-placement='top' title='" . $this->lang->line('delete') . "'><i class='fa fa-trash'></i></a>";
                }

                //==============================
                $row[] = $column_first;
                $row[] = html_escape($value->item_category);
                $row[] = html_escape($value->item_supplier);
                $row[] = html_escape($value->item_store);
                if($value->date){
                $row[] = $this->customlib->YYYYMMDDTodateFormat($value->date);
                }else{
                $row[] = '';
                }
                $row[] = html_escape($value->description);
                $row[] = html_escape($value->quantity);
				$row[] = composeStaffNameByString($value->generated_byname, $value->generated_bysurname, $value->generated_byemployee_id);
                $row[]     = $value->purchase_price;
                $row[]     = "<div class='white-space-nowrap'>" . $action . "</div>";
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

    public function add()
    {
        $this->form_validation->set_rules('item_id', $this->lang->line('item'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('purchase_price', $this->lang->line('purchase_price'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('quantity', $this->lang->line('quantity'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('item_category_id', $this->lang->line('item_category'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('supplier_id', $this->lang->line('supplier'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('item_photo', $this->lang->line('item_photo'), 'callback_handle_upload|callback_validateCanUploadFile[item_photo]');

        if ($this->form_validation->run() == false) {
            $msg = array(
                'e1'             => form_error('item_id'),
                'e2'             => form_error('quantity'),
                'e3'             => form_error('item_category_id'),
                'item_photo'     => form_error('item_photo'),
                'purchase_price' => form_error('purchase_price'),
                'supplier_id'    => form_error('supplier_id'),
            );

            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $date     = $this->customlib->dateFormatToYYYYMMDD($this->input->post('date', TRUE));
            $store_id = ($this->input->post('store_id', TRUE)) ? $this->input->post('store_id', TRUE) : null;
            $data     = array(
                'item_id'        => $this->input->post('item_id', TRUE),
                'symbol'         => $this->input->post('symbol', TRUE),
                'supplier_id'    => $this->input->post('supplier_id', TRUE),
                'purchase_price' => $this->input->post('purchase_price', TRUE),
                'store_id'       => $store_id,
                'quantity'       => $this->input->post('symbol', TRUE) . $this->input->post('quantity', TRUE),
                'date'           => $date,
                'description'    => $this->input->post('description', TRUE),
                'generated_by'    => $this->customlib->getLoggedInUserID(),
            );

            $insert_id = $this->itemstock_model->add($data);

            if (isset($_FILES["item_photo"]) && !empty($_FILES['item_photo']['name'])) {
                $file_name = $this->media_storage->fileupload("item_photo",'./uploads/inventory_items/');
                if (!IsNullOrEmptyString($file_name)) {
                    $data_img = array('id' => $insert_id, 'attachment' => 'uploads/inventory_items/' . $file_name);
                    $this->itemstock_model->add($data_img);

                    // SaaS: add the uploaded photo's size to the storage quota usage.
                    try {
                        $this->saasvalidation->updateStorageLimit('storage', ['item_photo']);
                    } catch (Exception $e) {
                        log_message('error', 'SaaS storage quota update failed (itemstock add): ' . $e->getMessage());
                    }
                }
            }

            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
        }

        echo json_encode($array);
    }

    public function update()
    {
        $this->form_validation->set_rules('item_id', $this->lang->line('item'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('quantity', $this->lang->line('quantity'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('item_category_id', $this->lang->line('item_category'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('supplier_id', $this->lang->line('supplier'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('purchase_price', $this->lang->line('purchase_price'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('item_photo', $this->lang->line('item_photo'), 'callback_handle_upload|callback_validateCanUploadFile[item_photo]');
        if ($this->form_validation->run() == false) {
            $msg = array(
                'e1'             => form_error('item_id'),
                'e2'             => form_error('quantity'),
                'e3'             => form_error('item_category_id'),
                'purchase_price' => form_error('purchase_price'),
                'item_photo'     => form_error('item_photo'),
                'supplier_id'    => form_error('supplier_id'),
            );

            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $store_id = ($this->input->post('store_id', TRUE)) ? $this->input->post('store_id', TRUE) : null;
            $updateid = $this->input->post("itemstockid", TRUE);
            $date     = $this->input->post('date', TRUE);
            $data     = array(
                'id'             => $updateid,
                'item_id'        => $this->input->post('item_id', TRUE),
                'symbol'         => $this->input->post('symbol', TRUE),
                'supplier_id'    => $this->input->post('supplier_id', TRUE),
                'store_id'       => $store_id,
                'quantity'       => $this->input->post('symbol', TRUE) . $this->input->post('quantity', TRUE),
                'purchase_price' => $this->input->post('purchase_price', TRUE),
                'date'           => $this->customlib->dateFormatToYYYYMMDD($date),
                'description'    => $this->input->post('description', TRUE),
            );

            $insert_id = $this->itemstock_model->add($data);
           
            if (isset($_FILES["item_photo"]) && !empty($_FILES['item_photo']['name'])) {
                // Size of the photo being replaced, for quota diff calculation.
                $existing     = $this->itemstock_model->get($updateid);
                $prev_size_kb = (!empty($existing['attachment'])) ? $this->media_storage->getUploadedFileSize($existing['attachment']) : 0;

                $file_name = $this->media_storage->fileupload("item_photo",'./uploads/inventory_items/');
                if (!IsNullOrEmptyString($file_name)) {
                    // Remove the old physical file that is being replaced.
                    if (!empty($existing['attachment'])) {
                        $this->media_storage->filedelete($existing['attachment'], '');
                    }

                    $data_img = array('id' => $updateid, 'attachment' => 'uploads/inventory_items/' . $file_name);
                    $this->itemstock_model->add($data_img);

                    // SaaS: adjust storage quota by the size difference (new vs replaced).
                    try {
                        $new_size_kb = $this->media_storage->getTmpFileSize('item_photo');
                        if ($prev_size_kb > $new_size_kb) {
                            $this->saasvalidation->deleteResouceQuota('storage', $prev_size_kb - $new_size_kb);
                        } elseif ($new_size_kb > $prev_size_kb) {
                            $this->saasvalidation->updateResouceQuota('storage', $new_size_kb - $prev_size_kb);
                        }
                    } catch (Exception $e) {
                        log_message('error', 'SaaS storage quota update failed (itemstock update): ' . $e->getMessage());
                    }
                }
            }

            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
        }

        echo json_encode($array);
    }

    public function download($id)
    {
        $getitemstock=$this->itemstock_model->get($id);
        $this->media_storage->filedownload($getitemstock['attachment'],'./');
    }

    public function getItemByCategory()
    {
        $item_category_id = $this->input->get('item_category_id', TRUE);
        $data             = $this->item_model->getItemByCategory($item_category_id);
        echo json_encode($data);
    }

    public function getItemunit()
    {
        $id   = $this->input->get('id', TRUE);
        $data = $this->item_model->getItemunit($id);
        echo json_encode($data);
    }

    public function delete($id)
    {
        if (!$this->rbac->hasPrivilege('item_stock', 'can_delete')) {
            access_denied();
        }
        $data['title'] = $this->lang->line('fees_master_list');

        // SaaS: release the photo's storage from the quota and remove the file.
        $row = $this->itemstock_model->get($id);
        if (!empty($row['attachment'])) {
            $file_size_kb = $this->media_storage->getUploadedFileSize($row['attachment']);
            if ($file_size_kb > 0) {
                try {
                    $this->saasvalidation->deleteResouceQuota('storage', $file_size_kb);
                } catch (Exception $e) {
                    log_message('error', 'SaaS storage quota release failed (itemstock delete): ' . $e->getMessage());
                }
            }
            $this->media_storage->filedelete($row['attachment'], '');
        }

        $this->itemstock_model->remove($id);
        redirect('admin/itemstock/index');
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

    public function handle_upload()
    {
        $image_validate = $this->config->item('file_validate');
        if (isset($_FILES["item_photo"]) && !empty($_FILES['item_photo']['name'])) {
            $file_type         = $_FILES["item_photo"]['type'];
            $file_size         = $_FILES["item_photo"]["size"];
            $file_name         = $_FILES["item_photo"]["name"];
            $allowed_extension = $image_validate['allowed_extension'];
            $ext               = pathinfo($file_name, PATHINFO_EXTENSION);
            $allowed_mime_type = $image_validate['allowed_mime_type'];
            if ($files = @filesize($_FILES['item_photo']['tmp_name'])) {
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

    public function edit($id)
    {
        $data['id']           = $id;
        $item                 = $this->itemstock_model->get($id);
        $data['item']         = $item;
        $data['title_list']   = $this->lang->line('fees_master_list');
        $itemcategory         = $this->itemcategory_model->get();
        $data['itemcatlist']  = $itemcategory;
        $itemsupplier         = $this->itemsupplier_model->get();
        $data['itemsupplier'] = $itemsupplier;
        $itemstore            = $this->itemstore_model->get();
        $data['itemstore']    = $itemstore;
        $item["date"]         = $this->customlib->YYYYMMDDTodateFormat($item['date']);		 
        $item["unit"]         =  $this->item_model->getItemunit($item['item_id'])['unit'];
        echo json_encode($item);
    }

    public function save_edit($id)
    {
        $this->form_validation->set_rules('quantity', $this->lang->line('quantity'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('item_id', $this->lang->line('item'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('item_category_id', $this->lang->line('item_category'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('item_photo', $this->lang->line('item_photo'), 'callback_handle_upload|callback_validateCanUploadFile[item_photo]');
        if ($this->form_validation->run() == false) {
            $msg = array(
                'e1'         => form_error('item_id'),
                'e2'         => form_error('item_category_id'),
                'e3'         => form_error('quantity'),
                'item_photo' => form_error('item_photo'),
            );
            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {

            $store_id = ($this->input->post('store_id', TRUE)) ? $this->input->post('store_id', TRUE) : null;
            $date     = $this->input->post('date', TRUE);
            $data     = array(
                'id'          => $id,
                'item_id'     => $this->input->post('item_id', TRUE),
                'symbol'      => $this->input->post('symbol', TRUE),
                'supplier_id' => $this->input->post('supplier_id', TRUE),
                'store_id'    => $store_id,
                'quantity'    => $this->input->post('symbol', TRUE) . $this->input->post('quantity', TRUE),
                'date'        => $this->customlib->dateFormatToYYYYMMDD($date),
                'description' => $this->input->post('description', TRUE),
            );

            $this->itemstock_model->add($data);

            if (isset($_FILES["item_photo"]) && !empty($_FILES['item_photo']['name'])) {
                $existing     = $this->itemstock_model->get($id);
                $prev_size_kb = (!empty($existing['attachment'])) ? $this->media_storage->getUploadedFileSize($existing['attachment']) : 0;

                $file_name = $this->media_storage->fileupload("item_photo", './uploads/inventory_items/');
                if (!IsNullOrEmptyString($file_name)) {
                    if (!empty($existing['attachment'])) {
                        $this->media_storage->filedelete($existing['attachment'], '');
                    }
                    $data_img = array('id' => $id, 'attachment' => 'uploads/inventory_items/' . $file_name);
                    $this->itemstock_model->add($data_img);
                    try {
                        $new_size_kb = $this->media_storage->getTmpFileSize('item_photo');
                        if ($prev_size_kb > $new_size_kb) {
                            $this->saasvalidation->deleteResouceQuota('storage', $prev_size_kb - $new_size_kb);
                        } elseif ($new_size_kb > $prev_size_kb) {
                            $this->saasvalidation->updateResouceQuota('storage', $new_size_kb - $prev_size_kb);
                        }
                    } catch (Exception $e) {
                        log_message('error', 'SaaS storage quota update failed (itemstock save_edit): ' . $e->getMessage());
                    }
                }
            }

            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
        }

        echo json_encode($array);
    }

    public function edit_new($id)
    {
        if (!$this->rbac->hasPrivilege('item_stock', 'can_edit')) {
            access_denied();
        }
        $data['title']        = $this->lang->line('edit_fees_master');
        $data['id']           = $id;
        $item                 = $this->itemstock_model->get($id);
        $data['item']         = $item;
        $data['title_list']   = $this->lang->line('fees_master_list');
        $item_result          = $this->itemstock_model->get();
        $data['itemlist']     = $item_result;
        $itemcategory         = $this->itemcategory_model->get();
        $data['itemcatlist']  = $itemcategory;
        $itemsupplier         = $this->itemsupplier_model->get();
        $data['itemsupplier'] = $itemsupplier;
        $itemstore            = $this->itemstore_model->get();
        $data['itemstore']    = $itemstore;
        $this->form_validation->set_rules('item_id', $this->lang->line('item'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('item_category_id', $this->lang->line('item_category'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('item_photo', $this->lang->line('item_photo'), 'callback_handle_upload|callback_validateCanUploadFile[item_photo]');

        if ($this->form_validation->run() == false) {
            $data['module'] = 'inventory';
            $this->load->view('layout/header', $data);
            $this->load->view('admin/itemstock/itemEdit', $data);
            $this->load->view('layout/footer', $data);
        } else {
            $store_id = ($this->input->post('store_id', TRUE)) ? $this->input->post('store_id', TRUE) : null;
            $date     = $this->input->post('date', TRUE);
            $data     = array(
                'id'          => $id,
                'item_id'     => $this->input->post('item_id', TRUE),
                'symbol'      => $this->input->post('symbol', TRUE),
                'supplier_id' => $this->input->post('supplier_id', TRUE),
                'store_id'    => $store_id,
                'quantity'    => $this->input->post('symbol', TRUE) . $this->input->post('quantity', TRUE),
                'date'        => $this->customlib->dateFormatToYYYYMMDD($date),
                'description' => $this->input->post('description', TRUE),
            );

            $this->itemstock_model->add($data);

            if (isset($_FILES["item_photo"]) && !empty($_FILES['item_photo']['name'])) {
                $existing     = $this->itemstock_model->get($id);
                $prev_size_kb = (!empty($existing['attachment'])) ? $this->media_storage->getUploadedFileSize($existing['attachment']) : 0;

                $file_name = $this->media_storage->fileupload("item_photo", './uploads/inventory_items/');
                if (!IsNullOrEmptyString($file_name)) {
                    if (!empty($existing['attachment'])) {
                        $this->media_storage->filedelete($existing['attachment'], '');
                    }
                    $data_img = array('id' => $id, 'attachment' => 'uploads/inventory_items/' . $file_name);
                    $this->itemstock_model->add($data_img);
                    try {
                        $new_size_kb = $this->media_storage->getTmpFileSize('item_photo');
                        if ($prev_size_kb > $new_size_kb) {
                            $this->saasvalidation->deleteResouceQuota('storage', $prev_size_kb - $new_size_kb);
                        } elseif ($new_size_kb > $prev_size_kb) {
                            $this->saasvalidation->updateResouceQuota('storage', $new_size_kb - $prev_size_kb);
                        }
                    } catch (Exception $e) {
                        log_message('error', 'SaaS storage quota update failed (itemstock edit_new): ' . $e->getMessage());
                    }
                }
            }

            $this->session->set_flashdata('msg', '<div class="alert alert-success text-left">' . $this->lang->line('item_stock_updated_successfully') . '</div>');
            redirect('admin/itemstock/index');
        }
    }
}
