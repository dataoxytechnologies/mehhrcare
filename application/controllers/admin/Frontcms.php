<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Frontcms extends Admin_Controller
{
	public $front_themes;


    public function __construct()
    {
        parent::__construct();

        $this->load->config('ci-blog');
        $this->front_themes = $this->config->item('ci_front_themes');
        $this->load->model('cms_page_model');
    }

    public function index()
    {
        if (!$this->rbac->hasPrivilege('front_cms_setting', 'can_view')) {
            access_denied();
        }
        $data['front_themes'] = $this->front_themes;
        $frontcmslist         = $this->frontcms_setting_model->get();
        $data['title']        = $this->lang->line('add_front_cms_setting');
        $data['title_list']   = $this->lang->line('front_cms_setting');
        $this->session->set_userdata('top_menu', 'setup');
        $this->session->set_userdata('sub_menu', 'schsettings/index');
        $this->session->set_userdata('inner_menu', 'admin/frontcms/index');
    
        $this->form_validation->set_rules('logo', $this->lang->line('image'), 'callback_handle_upload');
        if ($this->form_validation->run() == true) {
            $frontcms        = $this->input->post('is_active_front_cms', TRUE);
            $sidebar_options = $this->input->post('sidebar_options', TRUE);
            if (isset($sidebar_options)) {
                $sidebar_options = json_encode($sidebar_options);
            } else {
                $sidebar_options = json_encode(array());
            }
            if (isset($frontcms)) {
                $is_active_front_cms = $frontcms;
            } else {
                $is_active_front_cms = 0;
            }
            $home_section_raw = $this->input->post('home_section_data', TRUE);
            $static_pages_raw = $this->input->post('static_pages_data', TRUE);
            // Marquee items come as a textarea string — split into array
            if (isset($home_section_raw['marquee']['items']) && is_string($home_section_raw['marquee']['items'])) {
                $items = array_values(array_filter(array_map('trim', explode("\n", $home_section_raw['marquee']['items']))));
                $home_section_raw['marquee']['items'] = $items;
            }
            // Reindex repeater items to ensure clean JSON arrays
            if (isset($home_section_raw['stats']['items']) && is_array($home_section_raw['stats']['items'])) {
                $home_section_raw['stats']['items'] = array_values($home_section_raw['stats']['items']);
            }
            if (isset($home_section_raw['locations']['items']) && is_array($home_section_raw['locations']['items'])) {
                $home_section_raw['locations']['items'] = array_values(array_filter($home_section_raw['locations']['items'], function($r){ return !empty($r['name']); }));
            }
            if (isset($home_section_raw['testimonials']['items']) && is_array($home_section_raw['testimonials']['items'])) {
                $home_section_raw['testimonials']['items'] = array_values(array_filter($home_section_raw['testimonials']['items'], function($r){ return !empty($r['name']) || !empty($r['quote']); }));
            }
            if (isset($home_section_raw['tpas']['items']) && is_array($home_section_raw['tpas']['items'])) {
                $home_section_raw['tpas']['items'] = array_values(array_filter($home_section_raw['tpas']['items'], function($r){ return !empty($r['name']); }));
            }
            $data = array(
                'id'                           => (int)$this->input->post('id', TRUE),
                'contact_us_email'             => $this->input->post('contact_us_email', TRUE),
                'is_active_front_cms'          => $this->input->post('is_active_front_cms', TRUE),
                'is_active_online_appointment' => $this->input->post('is_active_online_appointment', TRUE),
                'is_active_rtl'                => $this->input->post('is_active_rtl', TRUE),
                'is_active_sidebar'            => $this->input->post('is_active_sidebar', TRUE),
                'theme'                        => $this->input->post('theme', TRUE),
                'theme_color'                  => $this->input->post('theme_color', TRUE) ?: 'aurora',
                'home_layout'                  => $this->input->post('home_layout', TRUE) ?: 'hospital',
                'home_section_data'            => $home_section_raw ? json_encode($home_section_raw) : null,
                'static_pages_data'            => $static_pages_raw ? json_encode($static_pages_raw) : null,
                'complain_form_email'          => $this->input->post('complain_form_email', TRUE),
                'sidebar_options'              => $sidebar_options,
                'google_analytics'             => $this->input->post('google_analytics'),
                'footer_text'                  => $this->input->post('footer_text', TRUE),
                'fb_url'                       => $this->input->post('fb_url', TRUE),
                'twitter_url'                  => $this->input->post('twitter_url', TRUE),
                'youtube_url'                  => $this->input->post('youtube_url', TRUE),
                'google_plus'                  => $this->input->post('google_plus', TRUE),
                'instagram_url'                => $this->input->post('instagram_url', TRUE),
                'pinterest_url'                => $this->input->post('pinterest_url', TRUE),
                'linkedin_url'                 => $this->input->post('linkedin_url', TRUE),
            );
           
            if (isset($_FILES["logo"]) && !empty($_FILES["logo"]['name'])) {
                $img_name = $this->media_storage->fileupload("logo", "./uploads/hospital_content/logo/");
                if ($this->input->post('id', TRUE) != '') {
                    $row = $this->frontcms_setting_model->get($this->input->post('id', TRUE));
                    if ($row->logo != '') {
                        $this->media_storage->filedelete($row->logo, "uploads/hospital_content/logo/");
                    }
                }
                $data['logo'] = "./uploads/hospital_content/logo/" .$img_name;
            }

            if (isset($_FILES["fav_icon"]) && !empty($_FILES["fav_icon"]['name'])) {
                $img_name = $this->media_storage->fileupload("fav_icon", "./uploads/hospital_content/logo/");
                if ($this->input->post('id', TRUE) != '') {
                    $row = $this->frontcms_setting_model->get($this->input->post('id', TRUE));
                    if ($row->fav_icon != '') {
                        $this->media_storage->filedelete($row->fav_icon, "uploads/hospital_content/logo/");
                    }
                }
                $data['fav_icon'] = "./uploads/hospital_content/logo/" .$img_name;
            }

            $this->frontcms_setting_model->add($data);
            $this->session->set_flashdata('msg', '<div class="alert alert-success text-left">' . $this->lang->line('success_message') . '</div>');
            redirect('admin/frontcms');
        }

        if (!$frontcmslist) {
            $frontcmslist                      = new stdClass();
            $frontcmslist->id                  = 0;
            $frontcmslist->is_active_front_cms = 0;
            $frontcmslist->contact_us_email    = '';
            $frontcmslist->is_active_sidebar   = '';
            $is_active_front_cms               = 0;
            $frontcmslist->google_analytics    = '';
            $frontcmslist->logo                = '';
            $frontcmslist->fav_icon            = '';
            $frontcmslist->sidebar_options     = json_encode(array());
            $frontcmslist->is_active_rtl       = '';
            $frontcmslist->theme               = '';
            $frontcmslist->theme_color         = 'aurora';
            $frontcmslist->home_layout         = 'hospital';
            $frontcmslist->home_section_data   = null;
            $frontcmslist->static_pages_data   = null;
            $frontcmslist->complain_form_email = '';
            $frontcmslist->footer_text         = '';
            $frontcmslist->fb_url              = '';
            $frontcmslist->twitter_url         = '';
            $frontcmslist->youtube_url         = '';
            $frontcmslist->google_plus         = '';
            $frontcmslist->instagram_url       = '';
            $frontcmslist->pinterest_url       = '';
            $frontcmslist->linkedin_url        = '';
        }
        if (empty($frontcmslist->theme_color))       { $frontcmslist->theme_color       = 'aurora'; }
        if (empty($frontcmslist->home_layout))        { $frontcmslist->home_layout        = 'hospital'; }
        if (!isset($frontcmslist->home_section_data)) { $frontcmslist->home_section_data  = null; }
        if (!isset($frontcmslist->static_pages_data)) { $frontcmslist->static_pages_data  = null; }

        $home_sections_decoded = $frontcmslist->home_section_data ? json_decode($frontcmslist->home_section_data, true) : array();
        $static_pages_decoded  = $frontcmslist->static_pages_data ? json_decode($frontcmslist->static_pages_data, true) : array();

        // Only pass non-_old themes to UI selector
        $data['active_themes'] = array_filter($this->front_themes, function($k) {
            return strpos($k, '_old') === false;
        }, ARRAY_FILTER_USE_KEY);

        $data['frontcmslist']          = $frontcmslist;
        $data['home_sections_decoded'] = $home_sections_decoded;
        $data['static_pages_decoded']  = $static_pages_decoded;
        $data['cms_pages']             = $this->cms_page_model->get();
        $data['module'] = 'setup';
        $this->load->view('layout/header', $data);
        $this->load->view('admin/frontcms/index', $data);
        $this->load->view('layout/footer', $data);
    }

    public function save_page_section()
    {
        if (!$this->rbac->hasPrivilege('front_cms_setting', 'can_edit')) {
            echo json_encode(array('status' => 'fail', 'error' => $this->lang->line('access_denied'), 'message' => ''));
            return;
        }
        $page_id          = (int)$this->input->post('page_id', TRUE);
        $layout_type      = $this->input->post('layout_type', TRUE) ?: 'blank';
        $page_section_raw = $this->input->post('page_section_data', TRUE);
        $update_data = array(
            'layout_type'       => $layout_type,
            'page_section_data' => $page_section_raw ? json_encode($page_section_raw) : null,
        );
        $this->db->where('id', $page_id);
        $this->db->update('front_cms_pages', $update_data);
        echo json_encode(array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message')));
    }

    public function reset_home_sections()
    {
        if (!$this->rbac->hasPrivilege('front_cms_setting', 'can_edit')) {
            echo json_encode(array('status' => 'fail', 'error' => $this->lang->line('access_denied')));
            return;
        }
        $row = $this->frontcms_setting_model->get();
        if (!$row) {
            echo json_encode(array('status' => 'fail', 'error' => 'No settings record found.'));
            return;
        }
        $this->db->where('id', $row->id);
        $this->db->update('front_cms_settings', array('home_section_data' => json_encode($this->_default_home_section_data())));
        echo json_encode(array('status' => 'success', 'message' => $this->lang->line('success_message')));
    }

    private function _default_home_section_data()
    {
        return array(
            'hero_media' => array(
                'hospital'   => array('bg_image' => 'https://images.unsplash.com/photo-1538108149393-fbbd81895907?w=1920&q=80&auto=format&fit=crop', 'bg_video' => 'https://videos.pexels.com/video-files/6130116/6130116-hd_1920_1080_30fps.mp4'),
                'specialist' => array('bg_image' => 'https://images.unsplash.com/photo-1559839734-2b71ea197ec2?w=1920&q=80&auto=format&fit=crop', 'bg_video' => ''),
                'dental'     => array('bg_image' => 'https://images.unsplash.com/photo-1606811971618-4486d14f3f99?w=1920&q=80&auto=format&fit=crop', 'bg_video' => ''),
                'eye'        => array('bg_image' => 'https://images.unsplash.com/photo-1551601651-2a8555f1a136?w=1920&q=80&auto=format&fit=crop', 'bg_video' => 'https://videos.pexels.com/video-files/7580479/7580479-uhd_4096_2160_25fps.mp4'),
                'diagnostic' => array('bg_image' => 'https://images.pexels.com/videos/4468981/free-video-4468981.jpg?auto=compress&cs=tinysrgb&w=1920', 'bg_video' => 'https://videos.pexels.com/video-files/4468981/4468981-hd_1920_1080_25fps.mp4'),
                'cardiology' => array('bg_image' => 'https://images.unsplash.com/photo-1576091160550-2173dba999ef?w=1920&q=80&auto=format&fit=crop', 'bg_video' => 'https://videos.pexels.com/video-files/7088514/7088514-hd_1920_1080_25fps.mp4'),
            ),
            'hero' => array(
                'show' => '1', 'headline' => 'World-class care, close to home',
                'subheadline' => 'Doctor-led cardiac care in Bandra West, Mumbai',
                'cta_primary' => 'Book Appointment', 'cta_secondary' => 'Find a Doctor', 'emergency_number' => '',
                'badge_text' => '22 years · 6,800 angioplasties · 0 readmits in 2025',
                'trust_1_value' => '9.8/10', 'trust_1_label' => 'Insurance partners',
                'trust_2_value' => '6,800+', 'trust_2_label' => 'Angioplasties performed',
                'trust_3_value' => '38', 'trust_3_label' => 'Patient rating',
                'doc_photo' => 'https://images.unsplash.com/photo-1612349317150-e413f6a5b16d?w=400&q=80',
                'doc_name' => 'Prof. Rahul Bhatt, MD DM', 'doc_role' => 'Senior Interventional Cardiologist',
                'doc_stat_1_value' => '22', 'doc_stat_1_label' => 'Years',
                'doc_stat_2_value' => '6800+', 'doc_stat_2_label' => 'Procedures',
                'doc_stat_3_value' => 'Top 1%', 'doc_stat_3_label' => 'NIRF Rank',
                'doc_creds' => "St George's London · FRCP · TAVI · Cath lab II",
                'doc_cta' => 'Book a slot today', 'doc_next' => 'Next: 4:30 pm today',
                'doc_fine' => 'No upfront payment · cashless on 38 insurers',
                'ecg_left' => 'Cath lab on-call 24×7 · Bandra West', 'ecg_right' => 'Tap-to-call · 1800·HEART·11',
            ),
            'marquee' => array('show' => '1', 'items' => array('NABL accredited lab','NABH certified hospital','96% same-day appointments','Reports in 4 hours','Cashless on 38 insurers','24×7 emergency care','Free pickup & drop for diagnostics')),
            'quick_tiles' => array('show' => '1', 'tile_1_title' => 'Book an appointment', 'tile_1_sub' => 'Book an appointment subtitle', 'tile_1_more' => '', 'tile_2_title' => 'View lab reports', 'tile_2_sub' => 'View lab reports subtitle', 'tile_2_more' => '', 'tile_3_title' => 'Health check-ups', 'tile_3_sub' => 'Health check-ups subtitle', 'tile_3_more' => '', 'tile_4_title' => 'Emergency & trauma', 'tile_4_sub' => 'Emergency & trauma subtitle', 'tile_4_more' => ''),
            'departments' => array('show' => '1', 'kicker' => 'Centres of excellence', 'title' => 'Our Specialties', 'subtitle' => '', 'count' => '4'),
            'doctors'     => array('show' => '1', 'kicker' => 'Our Specialist Doctors', 'title' => 'Our Doctors', 'count' => '4'),
            'how_it_works' => array('show' => '1', 'title' => 'Three steps. No paperwork.', 'items' => array(
                array('title' => 'Pick a slot',          'desc' => 'Choose your doctor, day and time. No signup required, no payment to book — just confirm and we will hold the slot.'),
                array('title' => 'Meet the specialist',  'desc' => 'In-person at the hospital or a video consult from home. Past reports, history and family profiles — all on one screen.'),
                array('title' => 'Get reports & care',   'desc' => 'Lab reports on WhatsApp same-day. Follow-ups, refills and billing — all from your patient portal, no chasing.'),
            )),
            'stats' => array('show' => '1', 'kicker' => 'By the numbers', 'title' => 'Trusted care, measurable results', 'items' => array(
                array('value' => '500+',  'label' => 'Hospital Beds',   'trend' => ''),
                array('value' => '120+',  'label' => 'Expert Doctors',  'trend' => 'Across 24 specialties'),
                array('value' => '50K+',  'label' => 'Happy Patients',  'trend' => ''),
            )),
            'locations' => array('show' => '1', 'kicker' => 'Find us near you', 'title' => 'Our Locations', 'items' => array(
                array('name' => 'Smart Hospital — Andheri West', 'tag' => 'Multi-specialty & Emergency', 'address' => 'Plot 42, Link Road, Andheri West, Mumbai 400053', 'hours' => '24×7 Emergency · OPD: Mon–Sat 9 AM–9 PM', 'phone' => '+91 22 4567 8900', 'map_url' => 'https://maps.app.goo.gl/exampleAndheriBranch'),
                array('name' => 'Smart Hospital — Bandra East',  'tag' => 'OPD & Diagnostics',         'address' => '8 Kalanagar, Bandra East, Mumbai 400051',        'hours' => 'Mon–Sat, 8 AM–8 PM · Sun: 10 AM–2 PM',   'phone' => '+91 22 2645 1122', 'map_url' => 'https://maps.app.goo.gl/exampleBandraBranch'),
            )),
            'testimonials' => array('show' => '1', 'kicker' => '', 'title' => 'What Patients Say', 'items' => array(
                array('name' => 'Ramesh Kumar', 'quote' => 'The doctors took great care of my mother during her stay…'),
                array('name' => 'Priya Sharma', 'quote' => 'Booking the appointment online was simple and the consultation was very thorough…'),
                array('name' => 'Anil Verma',   'quote' => 'Best hospital experience my family has had. Nursing staff is attentive…'),
                array('name' => 'Meera Iyer',   'quote' => 'I was nervous before my surgery, but the team made me feel safe…'),
            )),
            'tpas' => array('show' => '1', 'kicker' => 'Cashless cover', 'title' => 'Cashless on 38 insurers & all major TPAs.', 'subtitle' => 'Bring the card. We handle the paperwork. Pre-authorisation in under 90 minutes, on average — including weekends.', 'cta_text' => 'Check your insurer', 'cta_url' => '', 'items' => array(
                array('name' => 'Star Health'), array('name' => 'HDFC Ergo'),      array('name' => 'ICICI Lombard'),
                array('name' => 'Bajaj Allianz'), array('name' => 'Niva Bupa'),   array('name' => 'Care Health'),
                array('name' => 'SBI General'), array('name' => 'Tata AIG'),      array('name' => 'Aditya Birla'),
                array('name' => 'Manipal Cigna'), array('name' => 'New India'),   array('name' => 'Reliance General'),
            )),
            'cta' => array('show' => '1', 'title' => 'Ready to Book?', 'subtitle' => '', 'button_text' => 'Book Now', 'bullet_1' => '', 'bullet_2' => '', 'bullet_3' => ''),
            'order' => array('marquee','quick_tiles','departments','doctors','how_it_works','stats','locations','testimonials','tpas','cta'),
        );
    }

    public function handle_upload()
    {
        if (isset($_FILES["logo"]) && !empty($_FILES["logo"]['name'])) {
            $allowedExts = array('jpg', 'jpeg', 'png');
            $temp        = explode(".", $_FILES["logo"]["name"]);
            $extension   = end($temp);
            if ($_FILES["logo"]["error"] > 0) {
                $error .= "Error opening the file<br />";
            }
            if ($_FILES["logo"]["type"] != 'image/gif' &&
                $_FILES["logo"]["type"] != 'image/jpeg' &&
                $_FILES["logo"]["type"] != 'image/png') {
                $this->form_validation->set_message('handle_upload', $this->lang->line('invalid_file_type'));
                return false;
            }
            if (!in_array(strtolower($extension), $allowedExts)) {
                $this->form_validation->set_message('handle_upload', $this->lang->line('file_extension_not_allowed'));
                return false;
            }
            if ($_FILES["logo"]["size"] > 204800) {
                $this->form_validation->set_message('handle_upload', $this->lang->line('file_size_shoud_be_less_than_kb'));
                return false;
            }
            return true;
        } else {
            return true;
        }
    }

}
