<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Page extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();
        $config = array(
            'field' => 'slug',
            'title' => 'title',
            'table' => 'front_cms_pages',
            'id'    => 'id',
        );
        $this->load->library('slug', $config);
        $this->load->config('ci-blog');
        $this->load->library('imageResize');
    }

    public function index()
    {
        if (!$this->rbac->hasPrivilege('pages', 'can_view')) {
            access_denied();
        }
        $data = array();
        $this->session->set_userdata('top_menu', 'Front CMS');
        $this->session->set_userdata('sub_menu', 'admin/front/page');
        $listPages = $this->cms_page_model->get();

        $data['listPages'] = $listPages;
        $data['module'] = 'front_cms';
        $this->load->view('layout/header', $data);
        $this->load->view('admin/front/pages/index', $data);
        $this->load->view('layout/footer', $data);
    }

    public function create()
    {
        if (!$this->rbac->hasPrivilege('pages', 'can_add')) {
            access_denied();
        }
        $data['title']      = $this->lang->line('add_book');
        $data['title_list'] = $this->lang->line('book_details');
        $this->session->set_userdata('top_menu', 'Front CMS');
        $this->session->set_userdata('sub_menu', 'admin/front/page');
        $data['category'] = $this->customlib->getPageContentCategory();
        $this->form_validation->set_rules('title', $this->lang->line('title'), 'trim|required|xss_clean');

        if ($this->form_validation->run() == false) {
            $data['module'] = 'front_cms';
            $this->load->view('layout/header', $data);
            $this->load->view('admin/front/pages/create', $data);
            $this->load->view('layout/footer', $data);
        } else {
            $category = $this->input->post('content_category', TRUE);
            if (isset($category)) {
                $contents_category = $category;
            } else {
                $contents_category = "";
            }

            $layout_type      = $this->input->post('layout_type', TRUE) ?: 'blank';
            $page_section_raw = $this->input->post('page_section_data', TRUE);
            if ($layout_type === 'faq' && isset($page_section_raw['faq']) && is_array($page_section_raw['faq'])) {
                $page_section_raw['faq'] = array_values($page_section_raw['faq']);
            }

            $data = array(
                'title'             => $this->input->post('title', TRUE),
                'description'       => htmlspecialchars_decode($this->security->xss_clean($this->input->post('description'))),
                'meta_title'        => $this->input->post('meta_title', TRUE),
                'meta_keyword'      => $this->input->post('meta_keywords', TRUE),
                'feature_image'     => $this->input->post('image', TRUE),
                'type'              => 'page',
                'sidebar'           => $this->input->post('sidebar', TRUE),
                'meta_description'  => $this->input->post('meta_description', TRUE),
                'layout_type'       => $layout_type,
                'page_section_data' => $page_section_raw ? json_encode($page_section_raw) : null,
            );
            $data['slug']     = $this->slug->create_uri($data);
            $data['url']      = $this->config->item('ci_front_page_url') . $data['slug'];
            $insert_id        = $this->cms_page_model->add($data);
            $content_category = $this->input->post('content_category', TRUE);
            if (isset($content_category)) {
                if ($content_category != "standard") {
                    $contents   = array();
                    $contents[] = array('page_id' => $insert_id, 'content_type' => $content_category);
                    $insert_id  = $this->cms_page_content_model->batch_insert($contents);
                }
            }
            $this->session->set_flashdata('msg', '<div class="alert alert-success text-left">' . $this->lang->line('success_message') . '</div>');
            redirect('admin/front/page');
        }
    }

    public function edit($slug)
    {
        if (!$this->rbac->hasPrivilege('pages', 'can_edit')) {
            access_denied();
        }
        $data['title']      = $this->lang->line('edit_book');
        $data['title_list'] = $this->lang->line('book_details');
        $this->session->set_userdata('top_menu', 'Front CMS');
        $this->session->set_userdata('sub_menu', 'admin/front/page');
        $result = $this->cms_page_model->getBySlug($slug);

        if (empty($result['category_content'])) {
            $result['category_content'] = array('standard');
        }

        $data['category'] = $this->customlib->getPageContentCategory();
        $data['result']   = $result;
        $this->form_validation->set_rules('title', $this->lang->line('title'), 'trim|required|xss_clean');

        if ($this->form_validation->run() == false) {
            $data['module'] = 'front_cms';
            $this->load->view('layout/header', $data);
            $this->load->view('admin/front/pages/edit', $data);
            $this->load->view('layout/footer', $data);
        } else {
            $layout_type      = $this->input->post('layout_type', TRUE) ?: 'blank';
            $page_section_raw = $this->input->post('page_section_data', TRUE);
            if ($layout_type === 'faq' && isset($page_section_raw['faq']) && is_array($page_section_raw['faq'])) {
                $page_section_raw['faq'] = array_values($page_section_raw['faq']);
            }

            $data = array(
                'id'                => $this->input->post('id', TRUE),
                'title'             => $this->input->post('title', TRUE),
                'description'       => htmlspecialchars_decode($this->security->xss_clean($this->input->post('description'))),
                'meta_title'        => $this->input->post('meta_title', TRUE),
                'meta_keyword'      => $this->input->post('meta_keywords', TRUE),
                'feature_image'     => $this->input->post('image', TRUE),
                'sidebar'           => $this->input->post('sidebar', TRUE),
                'meta_description'  => $this->input->post('meta_description', TRUE),
                'layout_type'       => $layout_type,
                'page_section_data' => $page_section_raw ? json_encode($page_section_raw) : null,
            );

            $content_category = $this->input->post('content_category', TRUE);

            if (isset($content_category)) {
                if ($content_category != "standard") {
                    $contents  = array('page_id' => $this->input->post('id', TRUE), 'content_type' => $content_category);
                    $insert_id = $this->cms_page_content_model->insertOrUpdate($contents);
                } else {
                    $insert_id = $this->cms_page_content_model->deleteByPageID($this->input->post('id', TRUE));
                }
            }
            $data['slug'] = $this->slug->create_uri($data, $this->input->post('id', TRUE));
            $data['url']  = $this->config->item('ci_front_page_url') . $data['slug'];
            $this->cms_page_model->add($data);
            $this->session->set_flashdata('msg', '<div class="alert alert-success text-left">' . $this->lang->line('update_message') . '</div>');
            redirect('admin/front/page');
        }
    }

    public function delete($slug)
    {
        if (!$this->rbac->hasPrivilege('pages', 'can_delete')) {
            access_denied();
        }
        $data['title'] = 'Fees Master List';
        $this->cms_page_model->removeBySlug($slug);
        echo json_encode(array("status" => 1, "msg" => $this->lang->line('delete_message')));
    }

}
