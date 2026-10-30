<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Content extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('datatables');
        $this->load->library('SaasValidation');
        $this->config->load("image_valid");
    }

    function list() {

        if (!$this->rbac->hasPrivilege('content_share_list', 'can_view')) {
            access_denied();
        }
        
        $this->session->set_userdata('top_menu', 'Download Center');
        $this->session->set_userdata('sub_menu', 'admin/content/list');

        $data                           = array();
        $staff_id                       = $this->customlib->getStaffID();
        $data['count']                  = $this->uploadcontent_model->total_record($staff_id);
        $data['sch_setting']            = $this->setting_model->getSetting();
        $data['roles']                  = $this->role_model->get();
        $content_types                  = $this->contenttype_model->get();
        $data['content_types']          = $content_types;
        $data['superadmin_restriction'] = "";
        $data['module'] = 'download_center';
        $this->load->view('layout/header', $data);
        $this->load->view('admin/content/list', $data);
        $this->load->view('layout/footer', $data);
    }

    public function getsharelist()
    {
		if (!$this->rbac->hasPrivilege('content_share_list', 'can_view')) {
            access_denied();
        }
		
        $role_array = json_decode($this->customlib->getStaffRole());
        $role       = $role_array->id;
        if ($role == 7) {

            $m = $this->sharecontent_model->getsharelist();
        } else {

            $m = $this->sharecontent_model->getOtherStaffsharelist($role, $this->customlib->getStaffID());
        }
        $m = json_decode($m);

        $dt_data = array();
        if (!empty($m->data)) {
            foreach ($m->data as $key => $value) {

                $editbtn    = '';
                $deletebtn  = '';
                $share_link = '';

                $title = html_escape($value->title);

                $row   = array();
                $row[] = $title;

                if ($value->send_to == "public") {
                    $url_key         = $this->enc_lib->encrypt($value->id);
                    $shared_url_link = base_url().'site/share/' . $url_key;

                    $share_link = "<span data-bs-toggle='tooltip' data-bs-placement='top' title='" . $this->lang->line('link') . "'><button type='button' class='btn btn-sm btn-light' data-recordid='" . (int)$value->id . "' data-link='" . html_escape($shared_url_link) . "' data-bs-toggle='modal' data-bs-target='#linkModal'><i class='fa fa-link'></i></button></span>";
                }

                $editbtn = "<span data-bs-toggle='tooltip' data-bs-placement='top' title='" . $this->lang->line('view') . "'><button type='button' class='btn btn-sm btn-light' data-recordid='" . (int)$value->id . "' data-bs-toggle='modal' data-bs-target='#viewShareModal'><i class='fa fa-eye'></i></button></span>";

                if ($this->rbac->hasPrivilege('content_share_list', 'can_delete')) {
                    $deletebtn = "<a onclick='return confirm(" . '"' . $this->lang->line('delete_confirm') . '"' . ")' href='" . base_url() . "admin/content/delete_content/" . (int)$value->id . "' class='btn btn-sm btn-light' title='" . $this->lang->line('delete') . "' data-bs-toggle='tooltip' data-bs-placement='top'><i class='fa fa-trash'></i></a>";
                }else{
                    $deletebtn = "";
                }
                
                $row[] = $this->lang->line($value->send_to);
                
                if($value->share_date){
                    $row[] = $this->customlib->YYYYMMDDTodateFormat($value->share_date);
                }else{
                    $row[] =  '';
                }
                
                if($value->valid_upto){
                    $row[] = $this->customlib->YYYYMMDDTodateFormat($value->valid_upto);
                }else{
                    $row[] = '';
                }
                
                $row[] = $this->customlib->getStaffFullName($value->name, $value->surname, $value->employee_id);
                if ($value->description == "") {
                    $row[] = $this->lang->line('no_description');
                } else {
                    $row[] = html_escape($value->description);
                }
                $row[]     = "<div class='white-space-nowrap'>" . $share_link . ' ' . $editbtn . ' ' . $deletebtn . "</div>";
                $dt_data[] = $row;
            }
        }

        $json_data = array(
            "draw"            => intval($m->draw),
            "recordsTotal"    => intval($m->recordsTotal),
            "recordsFiltered" => intval($m->recordsFiltered),
            "data"            => $dt_data,
        );
        echo json_encode($json_data);
    }

    public function delete_content($id)
    {
		if (!$this->rbac->hasPrivilege('content_share_list', 'can_delete')) {
            access_denied();
        }
		
        $is_removed = $this->sharecontent_model->remove($id);
        redirect('admin/content/list');
    }

    public function getsharedcontents()
    {
		if (!$this->rbac->hasPrivilege('content_share_list', 'can_view')) {
            access_denied();
        }
		
        $response                    = array();
        $share_content_id            = $this->input->post('share_content_id', TRUE);
        $response['shared_contents'] = $this->sharecontent_model->getShareContentWithDocuments($share_content_id);
        $response['sch_setting']     = $this->setting_model->getSetting();
        $response['result_array']    = $this->sharecontent_model->getSharedUserBySharedID($share_content_id);
        $response_page               = $this->load->view('admin/content/_getsharedcontents', $response, true);
        $array                       = array('status' => '1', 'error' => '', 'page' => $response_page);
        echo json_encode($array);
    }
	
	public function upload()
    {
        if (!$this->rbac->hasPrivilege('upload_share_content', 'can_view')) {
            access_denied();
        }

        $this->session->set_userdata('top_menu', 'Download Center');
        $this->session->set_userdata('sub_menu', 'admin/content/upload');

        $data                           = array();       
        $staff_id                       = $this->customlib->getStaffID();
        $data['count']                  = $this->uploadcontent_model->total_record($staff_id);
        $data['sch_setting']            = array();
        $data['roles']                  =array();
        $content_types                  = $this->contenttype_model->get();
        $data['content_types']          = $content_types;
        
        $data['roles'] = $this->role_model->get();
        $data['module'] = 'download_center';
        $this->load->view('layout/header', $data);
        $this->load->view('admin/content/upload', $data);
        $this->load->view('layout/footer', $data);
    }

	public function share()
    {
        $this->form_validation->set_rules('title', $this->lang->line('title'), 'required|trim|xss_clean');
        $this->form_validation->set_rules('share_date', $this->lang->line('share_date'), 'required|trim|xss_clean');
        $this->form_validation->set_rules('send_to', $this->lang->line('send_to'), 'required|trim|xss_clean');
        $this->form_validation->set_rules('selected_contents[]', $this->lang->line('content'), 'required|trim|xss_clean');

        $data    = array();
        $send_to = $this->input->post('send_to', TRUE);

        if ($send_to == "group") {
            $groups = $this->input->post('user', TRUE);
            if (!isset($groups)) {
                $this->form_validation->set_rules('groups', $this->lang->line('group'), 'required|trim|xss_clean');
            }
        } elseif ($send_to == "individual") {
            $users_array = $this->input->post('user_list', TRUE);
            if (!isset($users_array)) {
                $this->form_validation->set_rules('users_array', $this->lang->line('users'), 'required|trim|xss_clean');
            } else {
                $individual_array_validate = json_decode($users_array);
                if (empty($individual_array_validate)) {
                    $this->form_validation->set_rules('users_array', $this->lang->line('users'), 'required|trim|xss_clean');
                }
            }
        } 

        if ($this->form_validation->run() == false) {
            $data = array(
                'title'               => form_error('title'),
                'share_date'          => form_error('share_date'),
                'send_to'             => form_error('send_to'),
                'groups'              => form_error('groups'),            
                'users_array'         => form_error('users_array'),
                'selected_contents[]' => form_error('selected_contents[]'),
            );
            $array = array('status' => 0, 'error' => $data);
            echo json_encode($array);
        } else {
            $upload_content    = array();
            $selected_contents = $this->input->post('selected_contents', TRUE);

            foreach ($selected_contents as $selected_content_key => $selected_content_value) {
                $upload_content[] = array(
                    'upload_content_id' => $selected_content_value,
                    'share_content_id'  => 0,
                );
            }

            $insert_data                = array();
            $insert_data['title']       = $this->input->post('title', TRUE);
            $insert_data['share_date']  = $this->customlib->dateFormatToYYYYMMDD($this->input->post('share_date', TRUE));
            $insert_data['valid_upto']  = $this->customlib->dateFormatToYYYYMMDD($this->input->post('valid_upto', TRUE));
            $insert_data['description'] = $this->input->post('description', TRUE);
            $insert_data['created_by'] = $this->customlib->getStaffID();
            $insert_data['send_to']    = $this->input->post('send_to', TRUE);
            $insert_content_for        = array();

            if ($insert_data['send_to'] == "group") {
                $groups = $this->input->post('user', TRUE);
                foreach ($groups as $group_key => $group_value) {
                    $insert_content_for[] = array(
                        'group_id'         => $group_value,
                        'share_content_id' => 0,
                    );
                }
            } elseif ($insert_data['send_to'] == "individual") {
                $individual_arr = json_decode($this->input->post('user_list'));
				
				 
                foreach ($individual_arr as $individual_key => $individual_value) {
					
                    $inv = array(
                        'share_content_id' => 0,
                        'staff_id'         => null,
                        'patient_id'       => null,
                    );
				 
                    if ($individual_value[0]->{"category"} == "Staff") {

                        $inv['staff_id']       = $individual_value[0]->{"record_id"};
                        $inv['patient_id']     = null;                         

                    } elseif ($individual_value[0]->{"category"} == "Patient") {

                        $inv['staff_id']       = null;
                        $inv['patient_id']     = $individual_value[0]->{"record_id"};                         

                    }

                    $insert_content_for[] = $inv;
                }
            } 

            $this->sharecontent_model->add($insert_data, $insert_content_for, $upload_content);
            echo json_encode(array('status' => 1, 'msg' => $this->lang->line('record_shared_successfully')));
        }
    }
    
    public function getuploaddata()
    {
        $staff_id       = $this->customlib->getStaffID();
        $pag_content    = '';
        $pag_navigation = '';

        $post_data = $this->input->post('data', TRUE);
        if (isset($post_data['page'])) {

            $page = (int)$post_data['page']; /* The page we are currently at */

            $cur_page = $page;
            $page -= 1;
            $per_page     = 12;
            $previous_btn = true;
            $next_btn     = true;
            $first_btn    = true;
            $last_btn     = true;
            $start        = $page * $per_page;

            $where_search = array();

            if (!empty($post_data['search'])) {
                $where_search['search'] = $post_data['search'];
            }

            $data['grid_view'] = !empty($post_data['grid']);
            
            $contents = $this->uploadcontent_model->getlimitwithsearch($staff_id, $per_page, $start, $where_search);

            $data['all_contents'] = $contents['total_rows'];

            $data['selected_content'] = $this->input->post('selected_content');

            $count       = $contents['count'];
            $pag_content = $this->load->view('admin/content/_getuploaddata', $data, true);

            $no_of_paginations = ceil($count / $per_page);
 
            if ($cur_page >= 7) {
                $start_loop = $cur_page - 3;
                if ($no_of_paginations > $cur_page + 3) {
                    $end_loop = $cur_page + 3;
                } else if ($cur_page <= $no_of_paginations && $cur_page > $no_of_paginations - 6) {
                    $start_loop = $no_of_paginations - 6;
                    $end_loop   = $no_of_paginations;
                } else {
                    $end_loop = $no_of_paginations;
                }
            } else {
                $start_loop = 1;
                if ($no_of_paginations > 7) {
                    $end_loop = 7;
                } else {
                    $end_loop = $no_of_paginations;
                }

            }
            $pag_navigation .= "<ul class='pagination'>";

            if ($first_btn && $cur_page > 1) {
                $pag_navigation .= "<li p='1' class='page-item unactive'><a class='page-link' href='javascript:void(0);'><i class='fa fa-angle-double-left'></i></a></li>";
            } else if ($first_btn) {
                $pag_navigation .= "<li p='1' class='page-item disabled'><a class='page-link' href='javascript:void(0);'><i class='fa fa-angle-double-left'></i></a></li>";
            }

            if ($previous_btn && $cur_page > 1) {
                $pre = $cur_page - 1;
                $pag_navigation .= "<li p='$pre' class='page-item unactive'><a class='page-link' href='javascript:void(0);'><i class='fa fa-angle-left'></i></a></li>";
            } else if ($previous_btn) {
                $pag_navigation .= "<li class='page-item disabled'><a class='page-link' href='javascript:void(0);'><i class='fa fa-angle-left'></i></a></li>";
            }
            for ($i = $start_loop; $i <= $end_loop; $i++) {

                if ($cur_page == $i) {
                    $pag_navigation .= "<li p='$i' class = 'page-item active' ><a class='page-link' href='javascript:void(0);'>{$i}</a></li>";
                } else {
                    $pag_navigation .= "<li p='$i' class='page-item unactive'><a class='page-link' href='javascript:void(0);'>{$i}</a></li>";
                }
            }

            if ($next_btn && $cur_page < $no_of_paginations) {
                $nex = $cur_page + 1;
                $pag_navigation .= "<li p='$nex' class='page-item unactive'><a class='page-link' href='javascript:void(0);'><i class='fa fa-angle-right'></i></a></li>";
            } else if ($next_btn) {
                $pag_navigation .= "<li class='page-item disabled'><a class='page-link' href='javascript:void(0);'><i class='fa fa-angle-right'></i></a></li>";
            }

            if ($last_btn && $cur_page < $no_of_paginations) {
                $pag_navigation .= "<li p='$no_of_paginations' class='page-item unactive'><a class='page-link' href='javascript:void(0);'><i class='fa fa-angle-double-right'></i></a></li>";
            } else if ($last_btn) {
                $pag_navigation .= "<li p='$no_of_paginations' class='page-item disabled'><a class='page-link' href='javascript:void(0);'><i class='fa fa-angle-double-right'></i></a></li>";
            }

            $pag_navigation = $pag_navigation . "</ul>";
        }

        $response = array(
            'content'    => $pag_content,
            'navigation' => $pag_navigation,
        );

        echo json_encode($response);
    }
    
	public function handle_upload_file($field, $var)
    {       
		$image_validate = $this->config->item('file_validate');

        if (isset($_FILES[$var])
            && !empty($_FILES[$var]['name'][0])
            && !empty($_FILES[$var]["type"][0])
            && $_FILES[$var]["size"][0] != 0 && file_exists($_FILES[$var]["tmp_name"][0])) {

            $file_type = $_FILES[$var]['type'][0];
            $file_size = $_FILES[$var]["size"][0];
            $file_name = $_FILES[$var]["name"][0];
            
			$allowed_extension = $image_validate["allowed_extension"];
            $allowed_mime_type = $image_validate["allowed_mime_type"];
			
            $ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

            if (!in_array($file_type, $allowed_mime_type)) {
                $this->form_validation->set_message('handle_upload_file', $this->lang->line('file_type_not_allowed'));
                return false;
            } elseif (!in_array($ext, $allowed_extension) || !in_array($file_type, $allowed_mime_type)) {
                $this->form_validation->set_message('handle_upload_file', $this->lang->line('file_type_not_allowed'));
                return false;
            } elseif ($file_size > $image_validate["upload_size"]) {
                $this->form_validation->set_message('handle_upload_file', $this->lang->line('file_size_shoud_be_less_than') .
                    number_format($image_validate["upload_size"] / 1048576, 2) . ' MB');
                return false;
            }

            return true;
        }
        $this->form_validation->set_message('handle_upload_file', $this->lang->line('please_choose_a_file_or_enter_youtube_video_link'));
        return false;
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

	public function ajaxupload()
	{
		if (!$this->rbac->hasPrivilege('upload_share_content', 'can_add')) {
			access_denied();
		}

		$this->form_validation->set_rules('content_type', $this->lang->line('content_type'), 'required|trim|xss_clean');

		$url = $this->input->post('url', TRUE);

		// ❗ FIX 1: Block file + URL together
		if (!empty($_FILES['upload']['name'][0]) && !empty($url)) {
			echo json_encode([
				'status' => 0,
				'error' => ['file' => 'Upload either file OR URL, not both']
			]);
			return;
		}

		// Validation rules
		if (!empty($_FILES['upload']['name'][0])) {
			$this->form_validation->set_rules(
				'file',
				$this->lang->line('file'),
				"callback_handle_upload_file[upload]|callback_validateCanUploadFile[upload]|trim|xss_clean"
			);
		} else {
			$this->form_validation->set_rules(
				'url',
				$this->lang->line('url'),
				'required|trim|xss_clean'
			);
		}

		if ($this->form_validation->run() == false) {
			echo json_encode([
				'status' => 0,
				'error' => [
					'title' => form_error('title'),
					'content_type' => form_error('content_type'),
					'file' => form_error('file'),
					'url' => form_error('url'),
				]
			]);
			return;
		}

		// ================= FILE UPLOAD =================
		if (!empty($_FILES['upload']['name'][0]) && $_FILES['upload']['error'][0] == UPLOAD_ERR_OK) {

			// ❗ FIX 2: STRICT FILE VALIDATION (ANTI-HACK)
			// Use the allowed types from config (image_valid.php -> file_validate)
			// so PDF/Word/Excel/CSV/ZIP are accepted, not only images.
			$file_validate = $this->config->item('file_validate');
			$allowed_ext   = $file_validate['allowed_extension'];
			$allowed_mime  = $file_validate['allowed_mime_type'];

			$finfo = finfo_open(FILEINFO_MIME_TYPE);

			foreach ($_FILES['upload']['name'] as $key => $name) {

				$ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
				if (!in_array($ext, $allowed_ext)) {
					echo json_encode(['status'=>0,'error'=>['file'=>'Invalid file extension']]);
					return;
				}

				$mime = finfo_file($finfo, $_FILES['upload']['tmp_name'][$key]);
				if (!in_array($mime, $allowed_mime)) {
					echo json_encode(['status'=>0,'error'=>['file'=>'Invalid file content']]);
					return;
				}
			}

			finfo_close($finfo);

			// Safe to process
			$dir_path   = "uploads/hospital_content/material/media/";
			$thumb_path = "uploads/hospital_content/material/media/thumb/";

			$config = [
				'thumb_path' => $thumb_path,
				'dir_path' => $dir_path,
				'thumb_width' => 100,
				'thumb_height' => 100
			];

			$this->load->library('imageResize', $config);
			$responses = $this->imageresize->resize($_FILES["upload"]);

			if ($responses) {
				foreach ($responses['images'] as $value) {

					$data = [
						'real_name'       => $value['name'],
						'img_name'        => $value['store_name'],
						'mime_type'       => $value['file_type'],
						'file_type'       => find_file_type($value['file_type']),
						'file_size'       => $value['file_size'],
						'thumb_name'      => $value['thumb_name'],
						'thumb_path'      => $value['thumb_path'],
						'dir_path'        => $value['dir_path'],
						'content_type_id' => $this->input->post('content_type', TRUE),
						'upload_by'       => $this->customlib->getStaffID(),
						'created_at'      => date('Y-m-d H:i:s'),
					];

					$this->uploadcontent_model->add($data);
				}

				// SaaS: add the total stored (resized) file size to the storage quota
				// usage. file_size from imageResize is the saved file's byte size.
				try {
					$uploaded_bytes = 0;
					foreach ($responses['images'] as $img) {
						$uploaded_bytes += (int) $img['file_size'];
					}
					$kb = $this->media_storage->convertBytesToKB($uploaded_bytes);
					if ($kb > 0) {
						$this->saasvalidation->updateResouceQuota('storage', $kb);
					}
				} catch (Exception $e) {
					log_message('error', 'SaaS storage quota update failed (content file add): ' . $e->getMessage());
				}

				$staff_id = $this->customlib->getStaffID();
				$count = $this->uploadcontent_model->total_record($staff_id);

				echo json_encode([
					'status' => 1,
					'msg' => $this->lang->line('success_message'),
					'file_count' => $count->number,
					'file_size' => format_file_size($count->file_size)
				]);
			} else {
				echo json_encode(['status'=>0,'error'=>['file'=>'Something went wrong']]);
			}

		} else {
			// ================= URL UPLOAD =================

			$youtube = "https://www.youtube.com/oembed?url=" . $url . "&format=json";

			$curl = curl_init($youtube);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
			$return = curl_exec($curl);
			$httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
			curl_close($curl);

			if ($httpcode == 200) {

				$this->load->library('imageResize', [
					'thumb_path' => "uploads/hospital_content/material/media/thumb/",
					'dir_path' => "uploads/hospital_content/material/media/",
					'thumb_width' => 100,
					'thumb_height' => 100
				]);

				$upload_response = $this->imageresize->resizeVideoImg($return);

				if ($upload_response) {

					$upload_response = json_decode($upload_response);

					// resizeVideoImg() returns file_size=0 for the YouTube thumbnail it
					// downloads. Read actual on-disk bytes so the DB record and quota are
					// both accurate — delete() reads file_size from DB to release quota.
					$_main_path  = './' . $upload_response->dir_path . $upload_response->store_name;
					$_thumb_full = './' . $upload_response->thumb_path . $upload_response->thumb_name;
					$actual_bytes = 0;
					if (file_exists($_main_path)) {
						$actual_bytes += (int) filesize($_main_path);
					}
					if (!empty($upload_response->thumb_name) && file_exists($_thumb_full)) {
						$actual_bytes += (int) filesize($_thumb_full);
					}

					$data = [
						'real_name'       => $upload_response->vid_title,
						'vid_url'         => $url,
						'vid_title'       => $upload_response->vid_title,
						'img_name'        => $upload_response->store_name,
						'file_type'       => $upload_response->file_type,
						'file_size'       => $actual_bytes,
						'thumb_name'      => $upload_response->thumb_name,
						'thumb_path'      => $upload_response->thumb_path,
						'dir_path'        => $upload_response->dir_path,
						'content_type_id' => $this->input->post('content_type', TRUE),
						'upload_by'       => $this->customlib->getStaffID(),
						'created_at'      => date('Y-m-d H:i:s'),
					];

					$this->uploadcontent_model->add($data);

					// SaaS: add the actual stored bytes to the quota.
					try {
						$kb = $this->media_storage->convertBytesToKB($actual_bytes);
						if ($kb > 0) {
							$this->saasvalidation->updateResouceQuota('storage', $kb);
						}
					} catch (Exception $e) {
						log_message('error', 'SaaS storage quota update failed (content url add): ' . $e->getMessage());
					}

					$staff_id = $this->customlib->getStaffID();
					$count = $this->uploadcontent_model->total_record($staff_id);

					echo json_encode([
						'status' => 1,
						'msg' => $this->lang->line('file_upload_successfully'),
						'file_count' => $count->number,
						'file_size' => format_file_size($count->file_size)
					]);

				} else {
					echo json_encode(['status'=>0,'error'=>['file'=>'Something went wrong']]);
				}

			} else {
				echo json_encode(['status'=>0,'error'=>['file'=>'Invalid URL']]);
			}
		}
	}
	
	public function download_content($id)
    {
        $this->load->helper('file'); // Load file helper
        $content = $this->uploadcontent_model->get($id);
        $this->media_storage->filedownload($content->img_name, $content->dir_path);
    }
	
    public function generate_url()
    {
        $this->form_validation->set_rules('title', $this->lang->line('title'), 'required|trim|xss_clean');
        $this->form_validation->set_rules('share_date', $this->lang->line('share_date'), 'required|trim|xss_clean');
        $this->form_validation->set_rules('selected_contents[]', $this->lang->line('content'), 'required|trim|xss_clean');

        $data = array();

        if ($this->form_validation->run() == false) {

            $data = array(
                'title'               => form_error('title'),
                'share_date'          => form_error('share_date'),
                'selected_contents[]' => form_error('selected_contents[]'),
            );
            $array = array('status' => 0, 'error' => $data);
            echo json_encode($array);
        } else {
            $upload_content    = array();
            $selected_contents = $this->input->post('selected_contents', TRUE);

            foreach ($selected_contents as $selected_content_key => $selected_content_value) {
                $upload_content[] = array(
                    'upload_content_id' => $selected_content_value,
                    'share_content_id'  => 0,
                );
            }

            $insert_data                = array();
            $insert_data['title']       = $this->input->post('title', TRUE);
            $insert_data['send_to']     = 'public';
            $insert_data['share_date']  = $this->customlib->dateFormatToYYYYMMDD($this->input->post('share_date', TRUE));
            $insert_data['valid_upto']  = $this->customlib->dateFormatToYYYYMMDD($this->input->post('valid_upto', TRUE));
            $insert_data['description'] = $this->input->post('description', TRUE);
            $insert_data['created_by']  = $this->customlib->getStaffID();
            $insert_content_for         = array();

            $insert_id = $this->sharecontent_model->add($insert_data, $insert_content_for, $upload_content);
            $url_key   = $this->enc_lib->encrypt($insert_id);
            echo json_encode(array('status' => 1, 'shared_url' => ($this->customlib->getBaseUrl().'site/share/' . $url_key), 'msg' => $this->lang->line('success_message')));
        }
    }
    
    public function delete_array()
    {
		if (!$this->rbac->hasPrivilege('upload_share_content', 'can_delete')) {
            access_denied();
        }
		
		
        $id_array     = $this->input->post('id', TRUE);
        $removed_data = $this->uploadcontent_model->getByIdArray($id_array);

        $is_removed = $this->uploadcontent_model->remove_array($id_array);
        if ($is_removed) {
            $released_bytes = 0;
            if (!empty($removed_data)) {
                foreach ($removed_data as $remove_data_key => $remove_data_value) {
                    $released_bytes += (int) $remove_data_value->file_size;
                    $this->media_storage->filedelete($remove_data_value->img_name, $remove_data_value->dir_path);
                    $this->media_storage->filedelete($remove_data_value->thumb_name, $remove_data_value->thumb_path);
                }
            }

            // SaaS: release the freed storage from the quota.
            try {
                $kb = $this->media_storage->convertBytesToKB($released_bytes);
                if ($kb > 0) {
                    $this->saasvalidation->deleteResouceQuota('storage', $kb);
                }
            } catch (Exception $e) {
                log_message('error', 'SaaS storage quota release failed (content delete_array): ' . $e->getMessage());
            }

            echo json_encode(array('status' => 1, 'msg' => $this->lang->line('success_message')));
        } else {
            echo json_encode(array('status' => 2, 'msg' => $this->lang->line('something_went_wrong')));
        }
    }
	
	public function delete()
    {
		if (!$this->rbac->hasPrivilege('upload_share_content', 'can_delete')) {
            access_denied();
        }
		
        $id                  = $this->input->post('id', TRUE);
        $upload_content_data = $this->uploadcontent_model->get($id);
     
        $is_removed = $this->uploadcontent_model->remove($id);
		
        if ($is_removed) {
            // SaaS: release the freed storage from the quota.
            try {
                $kb = $this->media_storage->convertBytesToKB((int) $upload_content_data->file_size);
                if ($kb > 0) {
                    $this->saasvalidation->deleteResouceQuota('storage', $kb);
                }
            } catch (Exception $e) {
                log_message('error', 'SaaS storage quota release failed (content delete): ' . $e->getMessage());
            }
            $this->media_storage->filedelete($upload_content_data->img_name, $upload_content_data->dir_path);
            $this->media_storage->filedelete($upload_content_data->thumb_name, $upload_content_data->thumb_path);            
                     
            echo json_encode(array('status' => 1, 'msg' => $this->lang->line('success_message')));
        } else {
            echo json_encode(array('status' => 2, 'msg' => $this->lang->line('something_went_wrong')));
        }
    }
    
}
