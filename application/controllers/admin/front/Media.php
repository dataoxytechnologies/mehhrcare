<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Media extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->config->load('image_valid');
        $this->load->model("filetype_model");
        $this->load->library('SaasValidation');
    }

    public function index()
    {
        if (!$this->rbac->hasPrivilege('media_manager', 'can_view')) {
            access_denied();
        }
        $data['title']      = 'Add Book';
        $data['title_list'] = 'Book Details';
        $this->session->set_userdata('top_menu', 'Front CMS');
        $this->session->set_userdata('sub_menu', 'admin/front/media');
        $data['mediaTypes'] = $this->customlib->mediaType();
        $data['module'] = 'front_cms';
        $this->load->view('layout/header', $data);
        $this->load->view('admin/front/media/index', $data);
        $this->load->view('layout/footer', $data);
    }

    public function getMedia()
    {
        $data               = array();
        $data['mediaTypes'] = $this->customlib->mediaType();
        $this->load->view('admin/front/media/getMedia', $data);
    }

    public function getPage()
    {
        $keyword    = $this->input->get('keyword', TRUE);
        $file_type  = $this->input->get('file_type', TRUE);
        $is_gallery = $this->input->get('is_gallery', TRUE);
        if (!isset($is_gallery)) {
            $is_gallery = 1;
        }
        $this->load->library("pagination");
        $config                     = array();
        $config["base_url"]         = "#";
        $config["total_rows"]       = $this->cms_media_model->count_all($keyword, $file_type);
        $config["per_page"]         = 60;
        $config["uri_segment"]      = 5;
        $config["use_page_numbers"] = true;
        $config["full_tag_open"]    = '<ul class="pagination">';
        $config["full_tag_close"]   = '</ul>';
        $config["first_tag_open"]   = '<li>';
        $config["first_tag_close"]  = '</li>';
        $config["last_tag_open"]    = '<li>';
        $config["last_tag_close"]   = '</li>';
        $config['next_link']        = '&gt;';
        $config["next_tag_open"]    = '<li>';
        $config["next_tag_close"]   = '</li>';
        $config["prev_link"]        = "&lt;";
        $config["prev_tag_open"]    = "<li>";
        $config["prev_tag_close"]   = "</li>";
        $config["cur_tag_open"]     = "<li class='active'><a href='#'>";
        $config["cur_tag_close"]    = "</a></li>";
        $config["num_tag_open"]     = "<li>";
        $config["num_tag_close"]    = "</li>";
        $config["num_links"]        = 1;
        $this->pagination->initialize($config);
        $page        = $this->uri->segment(5);
        $start       = ($page - 1) * $config["per_page"];
        $result      = $this->cms_media_model->fetch_details($config["per_page"], $start, $keyword, $file_type);
        $img_data    = array();
        $check_empty = 0;
        if (!empty($result)) {
            $check_empty = 1;
            foreach ($result as $res_key => $res_value) {

                $div = $this->genrateDiv($res_value, $is_gallery);

                $img_data[] = $div;
            }
        }
        if(empty($img_data)){
            $img_data[]="<div class='alert alert-danger text-left'><center>Record Not Found</center></div>";
        }
         $check_empty = 1;
        $output = array(
            'pagination_link' => $this->pagination->create_links(),
            'result_status'   => $check_empty,
            'result'          => $img_data,
        );
        echo json_encode($output);
    }

    public function deleteItem()
    {
        if (!$this->rbac->hasPrivilege('media_manager', 'can_delete')) {
            access_denied();
        }
        $record_id = $this->input->post('record_id', TRUE);
        $record    = $this->cms_media_model->get($record_id);
        if ($record) {

            $destination_path = "uploads/gallery/media/" . $record['img_name'];
            $thumb_path       = "uploads/gallery/media/thumb/" . $record['img_name'];
            $del_record       = $this->cms_media_model->remove($record_id);
            if ($del_record) {

                // SaaS: release the freed storage from the quota.
                if (!empty($record['file_size'])) {
                    try {
                        $kb = $this->media_storage->convertBytesToKB((int) $record['file_size']);
                        if ($kb > 0) {
                            $this->saasvalidation->deleteResouceQuota('storage', $kb);
                        }
                    } catch (Exception $e) {
                        log_message('error', 'SaaS storage quota release failed (media delete): ' . $e->getMessage());
                    }
                }

                if (is_readable($destination_path) && unlink($destination_path) && is_readable($thumb_path) && unlink($thumb_path)) {

                }
                echo json_encode(array('status' => 1, 'msg' => $this->lang->line('delete_message')));
            } else {
                echo json_encode(array('status' => 0, 'msg' => $this->lang->line('delete_message')));
            }
        } else {
            echo json_encode(array('status' => 0, 'msg' => $this->lang->line('delete_message')));
        }
    }

      public function genrateDiv($result, $is_gallery)
    {
        $is_image = "0";
        $is_video = "0";
        if ($result->file_type == 'image/png' || $result->file_type == 'image/jpeg' || $result->file_type == 'image/jpeg' || $result->file_type == 'image/jpeg' || $result->file_type == 'image/gif') {
            $file     = base_url() . $result->dir_path . $result->img_name;
            $file_src = base_url() . $result->dir_path . $result->img_name;
            $is_image = 1;
        } elseif ($result->file_type == 'video') {
            $file     = base_url() . $result->thumb_path . $result->img_name;
            $file_src = $result->vid_url;
            $is_video = 1;
        } elseif ($result->file_type == 'text/plain') {
            
            $file     = base_url('backend/images/txticon.png');
            $file_src = $this->media_storage->getImageURL($result->dir_path . $result->img_name);
       
        } elseif ($result->file_type == 'application/zip' || $result->file_type == 'application/x-rar') {
          
            $file     = base_url('backend/images/zipicon.png');
            $file_src = $this->media_storage->getImageURL($result->dir_path . $result->img_name);
       
        } elseif ($result->file_type == 'application/pdf') {
          
            $file     = base_url('backend/images/pdficon.png');
            $file_src = $this->media_storage->getImageURL($result->dir_path . $result->img_name);
        
        } elseif ($result->file_type == 'application/msword') {
           
            $file     = base_url('backend/images/wordicon.png');
            $file_src = $this->media_storage->getImageURL($result->dir_path . $result->img_name);
       
        } elseif ($result->file_type == 'application/vnd.ms-excel') {
            
            $file     = base_url('backend/images/excelicon.png');
            $file_src = $this->media_storage->getImageURL($result->dir_path . $result->img_name);
       
        } else {
           
            $file     = base_url('backend/images/docicon.png');
            $file_src = $this->media_storage->getImageURL($result->dir_path . $result->img_name);
       
        }

        $output = '';
        $output .= "<div class='col-sm-3 col-md-2 col-xs-6 img_div_modal image_div div_record_" . $result->id . "'>";
        $output .= "<div class='fadeoverlay'>";
        $output .= "<div class='fadeheight'>";
        $output .= "<img class='' data-fid='" . $result->id . "' data-content_type='" . $result->file_type . "' data-content_name='" . $result->img_name . "' data-is_image='" . $is_image . "' data-vid_url='" . $result->vid_url . "' data-img='" . base_url() . $result->dir_path . $result->img_name . "' src='" .  $this->media_storage->getImageURL($result->dir_path . $result->img_name) . "'>";
        if ($is_video == 1) {
            $output .= "<span class='mediatype-badge mediatype-video'><i class='fa fa-play'></i> VIDEO</span>";
        } else {
            $output .= "<span class='mediatype-badge mediatype-img'><i class='fa fa-picture-o'></i> IMG</span>";
        }
        if (!$is_gallery) {
            $output .= "<div class='overlay3'>";
            if ($this->rbac->hasPrivilege('media_manager', 'can_view')) {
                $output .= "<a href='#' title='" . $this->lang->line('view') . "' class='uploadcheckbtn' data-record_id='" . $result->id . "' data-bs-toggle='modal' data-bs-target='#detail' data-image='" . $file . "' data-source='" . $file_src . "' data-media_name='" . $result->img_name . "' data-media_size='" . $result->file_size . "' data-media_type='" . $result->file_type . "'><i class='fa fa-eye'></i></a>";
            }
            if ($this->rbac->hasPrivilege('media_manager', 'can_delete')) {
                $output .= "<a href='#' title='" . $this->lang->line('delete') . "' class='uploadclosebtn' data-record_id='" . $result->id . "' data-bs-toggle='modal' data-bs-target='#confirm-delete'><i class='fa fa-trash'></i></a>";
            }
            $output .= "<p class='processing' style='display:none;'>Processing...</p>";
            $output .= "</div>";
        }
        $output .= "</div>";
        $bytes = intval($result->file_size);
        if ($bytes >= 1048576)      $size_fmt = round($bytes / 1048576, 1) . ' MB';
        elseif ($bytes >= 1024)     $size_fmt = round($bytes / 1024, 0) . ' KB';
        elseif ($bytes > 0)         $size_fmt = $bytes . ' B';
        else                        $size_fmt = ($is_video == 1) ? 'YouTube' : '';
        $caption = ($is_video == 1) ? $result->vid_title : $result->img_name;
        $output .= "<div class='img-caption'><span class='img-caption-name'>" . htmlspecialchars($caption) . "</span><span class='img-caption-size'>" . $size_fmt . "</span></div>";
        $output .= "</div>";
        $output .= "</div>";
        return $output;
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

    public function addVideo()
    {
        if (!$this->rbac->hasPrivilege('media_manager', 'can_add')) {
            access_denied();
        }
        $this->form_validation->set_error_delimiters('', '');
        $video_url = $this->input->post('video_url', TRUE);

        if (isset($_FILES['files']) && $video_url == "") {          
             $this->form_validation->set_rules('files', 'files', 'callback_handle_upload|callback_validateCanUploadFile[files]|trim|xss_clean');
        } else {           
            $this->form_validation->set_rules('video_url', $this->lang->line('url'), 'required|trim|xss_clean');
        }

        if ($this->form_validation->run() == false) {
            $data = array(
                'video_url'  => form_error('video_url'),
                'files'      => form_error('files'),
            );
            $array = array('status' => 0, 'error' => $data);
            echo json_encode($array);
        } else {
            
        if (isset($_FILES['files']) && !empty($_FILES['files']) && $_FILES['files']['error'][0] == UPLOAD_ERR_OK) {        
            
        $image_validate    = $this->config->item('file_validate');
        $allowedExts       = $image_validate["allowed_extension"];
        $allowed_mime_type = $image_validate["allowed_mime_type"];
        
            $dir_path    = "uploads/gallery/media/";
            $thumb_path  = "uploads/gallery/media/thumb/";
            $config['thumb_path']   = $thumb_path;
            $config['dir_path']     = $dir_path;
            $config['thumb_width']  = 100;
            $config['thumb_height'] = 100;
            $this->load->library('imageResize', $config);
            $responses        = $this->imageresize->resize($_FILES["files"]);       
	
            $response_array   = array();
            if ($responses) {
                $img_array  = array();
                $validation = 0;
                foreach ($responses['images'] as $key => $value) {

                    $validation = 1;
                    $temp       = explode(".", $value['store_name']);
                    $file_type  = strtolower($value['file_type']);

                    $extension = end($temp);
                    $extension = strtolower($extension);

                    if (!in_array($extension, $allowedExts)) {
                        $validation = 0;
                    }
                    if (!in_array($file_type, $allowed_mime_type)) {
                        $validation = 0;
                    }
                }

                if ($validation == 1) {
                    $uploaded_bytes = 0;
                    foreach ($responses['images'] as $key => $value) {
                        $uploaded_bytes += (int) $value['file_size'];
                        $data = array(
                            'img_name'   => $value['store_name'],
                            'file_type'  => $value['file_type'],
                            'file_size'  => $value['file_size'],
                            'thumb_name' => $value['store_name'],
                            'thumb_path' => $value['thumb_path'],
                            'dir_path'   => $value['dir_path'],
                        );
                        $insert_id         = $this->cms_media_model->add($data);
                        $data['record_id'] = $insert_id;
                        $img_array[]       = $data;
                    }

                    // SaaS: add total stored (resized) size to the storage quota usage.
                    try {
                        $kb = $this->media_storage->convertBytesToKB($uploaded_bytes);
                        if ($kb > 0) {
                            $this->saasvalidation->updateResouceQuota('storage', $kb);
                        }
                    } catch (Exception $e) {
                        log_message('error', 'SaaS storage quota update failed (media add): ' . $e->getMessage());
                    }

                    $response_array['status'] = 1;
                    $response_array['msg']    = $this->lang->line('success_message');

                } else {
                    $response_array['status'] = 0;
                    $response_array['error']    = $this->lang->line('file_extension_not_allowed');
                }

            } else {
                $response_array['status'] = 0;
                $response_array['error']    = $this->lang->line('something_wrong');
            }
            echo json_encode($response_array);
        } else {       

            $url     = $this->input->post('video_url', TRUE);
            $youtube = "https://www.youtube.com/oembed?url=" . $url . "&format=json";
            $curl    = curl_init($youtube);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            $return   = curl_exec($curl);
            $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            curl_close($curl);

            $response = array('status' => 0, 'msg' => 'Something wrong');

            if ($httpcode == 200) {

                $img_array  = array();

                $dir_path    = "uploads/gallery/media/";
                $thumb_path  = "uploads/gallery/media/thumb/";

                $config['thumb_path']   = $thumb_path;
                $config['dir_path']     = $dir_path;
                $config['thumb_width']  = 100;
                $config['thumb_height'] = 100;
                $this->load->library('imageResize', $config);
                $upload_response = $this->imageresize->resizeVideoImg($return);

                if ($upload_response) {
                    $upload_response = json_decode($upload_response);
                    $data            = array(
                        'vid_url'    => $url,
                        'vid_title'  => $upload_response->vid_title,
                        'img_name'   => $upload_response->store_name,
                        'file_type'  => $upload_response->file_type,
                        'file_size'  => $upload_response->file_size,
                        'thumb_name' => $upload_response->store_name,
                        'thumb_path' => $upload_response->thumb_path,
                        'dir_path'   => $upload_response->dir_path,
                    );
                    $insert_id = $this->cms_media_model->add($data);

                    // SaaS: add stored size to the storage quota usage (URL/video thumb).
                    try {
                        $kb = $this->media_storage->convertBytesToKB((int) $upload_response->file_size);
                        if ($kb > 0) {
                            $this->saasvalidation->updateResouceQuota('storage', $kb);
                        }
                    } catch (Exception $e) {
                        log_message('error', 'SaaS storage quota update failed (media url add): ' . $e->getMessage());
                    }

                    echo json_encode(array('status' => 1, 'msg' => 'file upload successfully', 'error' => ''));
                } else {
                    echo json_encode(array('status' => 0, 'msg' => 'Please try again', 'error' => ''));
                }
            } else {
                echo json_encode(array('status' => 0, 'msg' => 'Please try again', 'error' => ''));
            }      
                
            }
        }
    }

    public function handle_upload()
    {	 
		$image_validate = $this->config->item('file_validate');
		$allowed_extension = $image_validate["allowed_extension"];
        $allowed_mime_type = $image_validate["allowed_mime_type"];			
			 
        if (!empty($_FILES["files"]["name"][0]) && !empty($_FILES["files"]["name"][0])) {

			for($i=0;$i<count($_FILES["files"]["name"]);$i++){
				
				$file_type = $_FILES["files"]['type'][$i];
				$file_size = $_FILES["files"]["size"][$i];
				$file_name = $_FILES["files"]["name"][$i];            
	
				$ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
	
				if (!in_array($file_type, $allowed_mime_type)) {
					$this->form_validation->set_message('handle_upload', $this->lang->line('file_type_not_allowed'));
					return false;
				} elseif (!in_array($ext, $allowed_extension) || !in_array($file_type, $allowed_mime_type)) {
					$this->form_validation->set_message('handle_upload', $this->lang->line('file_type_not_allowed'));
					return false;
				} elseif ($file_size > 2097152) {
					$this->form_validation->set_message('handle_upload', $this->lang->line('file_size_shoud_be_less_than') .
						number_format(2097152 / 1048576, 2) . ' MB');
					return false;
				}
				
			}
            return true;

        } else {
            $this->form_validation->set_message('handle_upload', $this->lang->line('please_choose_a_file_or_enter_youtube_video_link'));
            return false;
        }
    }
    
}
