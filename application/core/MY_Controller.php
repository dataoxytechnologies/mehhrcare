<?php

define('THEMES_DIR', 'themes');
define('BASE_URI', str_replace('index.php', '', $_SERVER['SCRIPT_NAME']));

class MY_Controller extends CI_Controller
{

    protected $langs = array();

    public function __construct()
    {
        parent::__construct();
        $lang_array = array();
		$this->load->library('Db_manager');
        $this->load->helper('lang');
        $this->load->helper('language');
        $this->load->config('license');
        $this->load->library('auth');
        $this->load->library('module_lib');
        $this->load->helper('directory');
        $this->load->helper('custom_helper');
        $this->load->helper('mime_helper');
		
        $this->load->helper('mask');
		
        $this->load->model(array('setting_model','chatuser_model','ambulance_model'));
  
        if ($this->session->has_userdata('hospitaladmin')) {
            $admin    = $this->session->userdata('hospitaladmin');
            $language = ($admin['language']['language']);
        } else if ($this->session->has_userdata('patient')) {
            $student  = $this->session->userdata('patient');
            $language = ($student['language']['language']);
        } else {
            $sss      = $this->setting_model->get();
			$language = $sss[0]['language'];
        }
 
        $this->config->set_item('language', $language);
        $map = directory_map(APPPATH . "./language/" . $language . "/app_files");
        foreach ($map as $lang_key => $lang_value) {
            $lang_array[] = 'app_files/' . str_replace(".php", "", $lang_value);
        }
        $this->load->language($lang_array, $language);

        // RTL flag — exposed globally to every view so headers can swap CSS
        // and emit dir="rtl" without each controller having to set it.
        $rtl_mode = ($this->customlib->getRTL() === 'yes');
        $this->load->vars(['rtl_mode' => $rtl_mode]);

        // Theme Studio tokens — merged hospital defaults + per-user overrides.
        // Re-resolved per request (no session caching) so superadmin brand
        // changes propagate immediately to all users without their own prefs.
        $admin_sess   = $this->session->userdata('hospitaladmin');
        $patient_sess = $this->session->userdata('patient');
        if (!empty($admin_sess) && !empty($admin_sess['id'])) {
            $sh_theme_tokens = theme_resolve_tokens($admin_sess['id'], 'staff');
        } elseif (!empty($patient_sess) && !empty($patient_sess['patient_id'])) {
            $sh_theme_tokens = theme_resolve_tokens($patient_sess['patient_id'], 'patient');
        } else {
            $sh_theme_tokens = theme_resolve_tokens();
        }
        $this->load->vars(['sh_theme_tokens' => $sh_theme_tokens]);
    }

}

class Admin_Controller extends MY_Controller
{
  protected $chat_count = 0;

    public function __construct()
    {
        parent::__construct();
        $this->load->library('rbac');
        $this->auth->is_logged_in();
        $this->check_license();       
    }
  
     public function check_license()
    {
        // =======================================================================
        // MANUAL TOGGLE FOR LICENSE CHECKING
        // =======================================================================
        // - To DISABLE license validation (for Local / Dev / Staging): return true;
        // - To ENABLE license validation (for Production): comment out 'return true;' below
        // =======================================================================
        return true; // <-- COMMENT THIS LINE ON PRODUCTION TO ENABLE LICENSE CHECK

        $license = $this->config->item('SHLK');
        if (!empty($license)) {
            $regex = "/^[A-Z0-9]{6}-[A-Z0-9]{6}-[A-Z0-9]{6}-/";

            if (preg_match($regex, $license)) {
                $valid_string = $this->aes->validchk('encrypt', base_url());
                if (strpos($license, $valid_string) !== false) {
                    true; //valid
                } else {
                    $this->update_ss_routine();
                }
            } else {
                $this->update_ss_routine();

            }
        }
    }
	
    public function update_ss_routine()
    {
        $license       = $this->config->item('SHLK');
        $fname         = APPPATH . 'config/license.php';
        $update_handle = fopen($fname, "r");
        $content       = fread($update_handle, filesize($fname));
        $file_contents = str_replace('$config[\'SHLK\'] = \'' . $license . '\'', '$config[\'SHLK\'] = \'\'', $content);
        $update_handle = fopen($fname, 'w') or die("can't open file");
        if (fwrite($update_handle, $file_contents)) {

        }
        fclose($update_handle);

        $this->config->set_item('SHLK', '');
    }

}

class Patient_Controller extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->config('payroll');
        $this->auth->is_logged_in_user('patient');
    }

}

class Public_Controller extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
    }

}

class Parent_Controller extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->auth->is_logged_in_user('parent');
    }

}

class Front_Controller extends CI_Controller
{
    protected $data           = array();
    protected $school_details = array();
    protected $parent_menu    = '';
    protected $page_title     = '';
    protected $theme_path     = '';
    protected $front_setting  = '';

    public function __construct()
    {
        parent::__construct();
        $this->load->helper('lang');
        $this->load->helper('language');
        $this->check_installation();
        if ($this->config->item('installed') == true) {
            $this->db->reconnect();
        }

        $this->school_details = $this->setting_model->getSetting();
        $this->load->model('frontcms_setting_model');
        $this->front_setting = $this->frontcms_setting_model->get();
		$front_cms_class  = $this->router->fetch_class();
        $front_cms_method = $this->router->fetch_method();

            if ($this->front_setting->is_active_front_cms) {
                $this->config->set_item('front_layout', true);
            }
			
            if (!$this->front_setting->is_active_front_cms) {
                $this->config->set_item('front_layout', false);
            }

            if(!$this->front_setting->is_active_front_cms && !$this->front_setting->is_active_online_appointment){              
                 redirect('site/userlogin');
            }  

           if($this->front_setting->is_active_online_appointment){
                if (!$this->front_setting->is_active_front_cms && 
                    !($front_cms_class == "welcome" && $front_cms_method == "appointment") 
                ) {
                    redirect('site/userlogin');
                }
            }

        $this->theme_path = $this->front_setting->theme;
    
      //================
        $language = ($this->school_details->language);
        $this->config->set_item('language', $language);
        $this->load->helper('directory');
        $lang_array = array('form_validation_lang');
        $map        = directory_map(APPPATH . "./language/" . $language . "/app_files");
        foreach ($map as $lang_key => $lang_value) {
            $lang_array[] = 'app_files/' . str_replace(".php", "", $lang_value);
        }        
   
        $this->load->language($lang_array, $language);
      //===============
        $this->load->config('ci-blog');
    }

    protected function load_theme($content = null, $layout = true)
    {
        $this->data['main_menus']     = '';
        $this->data['school_setting'] = $this->school_details;
        $this->data['front_setting']  = $this->front_setting;
        $menu_list                    = $this->cms_menu_model->getBySlug('main-menu');
        $footer_menu_list             = $this->cms_menu_model->getBySlug('bottom-menu');
        if (count($menu_list) > 0) {
            $this->data['main_menus'] = $this->cms_menuitems_model->getMenus($menu_list['id']);
        }

        if (count($footer_menu_list) > 0) {
            $this->data['footer_menus'] = $this->cms_menuitems_model->getMenus($footer_menu_list['id']);
        }
        $this->data['layout_type']   = $layout;
        $this->data['theme_color']   = isset($this->front_setting->theme_color)  ? $this->front_setting->theme_color  : 'aurora';
        $this->data['home_layout']   = isset($this->front_setting->home_layout)  ? $this->front_setting->home_layout  : 'hospital';
        $raw_sections = isset($this->front_setting->home_section_data) ? $this->front_setting->home_section_data : '';
        $this->data['home_sections'] = $raw_sections ? json_decode($raw_sections, true) : array();
        $this->data['home_sections'] = resolve_hero_media($this->data['home_sections'], $this->data['home_layout']);
        $raw_static = isset($this->front_setting->static_pages_data) ? $this->front_setting->static_pages_data : '';
        $this->data['static_pages']  = $raw_static ? json_decode($raw_static, true) : array();
        $this->data['header'] = $this->load->view('themes/' . $this->theme_path . '/header', $this->data, true);
        $this->data['slider'] = $this->load->view('themes/' . $this->theme_path . '/home_slider', $this->data, true);
        $this->data['footer'] = $this->load->view('themes/' . $this->theme_path . '/footer', $this->data, true);
        $this->base_assets_url = 'backend/' . THEMES_DIR . '/' . $this->theme_path . '/';

        $this->data['base_assets_url'] = BASE_URI . $this->base_assets_url;
        
        $is_captcha                  = $this->captchalib->is_captcha('appointment');
        $this->data["is_captcha"]    = $is_captcha;
        if($is_captcha){
            $captcha_result = $this->captchalib->generate_captcha();
        $this->data["captcha_image"] = is_array($captcha_result) ? $captcha_result['image'] : '';
        }
        if ($layout == true) {
            $this->data['content'] = (is_null($content)) ? '' : $this->load->view(THEMES_DIR . '/' . $this->theme_path . '/' . $content, $this->data, true);
            $this->load->view(THEMES_DIR . '/' . $this->theme_path . '/layout', $this->data);
        } else {
            $this->data['content'] = (is_null($content)) ? '' : $this->load->view(THEMES_DIR . '/' . $this->theme_path . '/' . $content, $this->data, true);
            $this->load->view(THEMES_DIR . '/' . $this->theme_path . '/base_layout', $this->data);
        }        
    }

    protected function load_theme_form($content = null, $layout = true)
    {
        $this->data['main_menus']     = '';
        $this->data['school_setting'] = $this->school_details;
        $this->data['front_setting']  = $this->front_setting;
        $menu_list                    = $this->cms_menu_model->getBySlug('main-menu');
        $footer_menu_list             = $this->cms_menu_model->getBySlug('bottom-menu');
        if (count($menu_list) > 0) {
            $this->data['main_menus'] = $this->cms_menuitems_model->getMenus($menu_list['id']);
        }

        if (count($footer_menu_list) > 0) {
            $this->data['footer_menus'] = $this->cms_menuitems_model->getMenus($footer_menu_list['id']);
        }
        $this->data['theme_color']   = isset($this->front_setting->theme_color)  ? $this->front_setting->theme_color  : 'aurora';
        $this->data['home_layout']   = isset($this->front_setting->home_layout)  ? $this->front_setting->home_layout  : 'hospital';
        $raw_sections = isset($this->front_setting->home_section_data) ? $this->front_setting->home_section_data : '';
        $this->data['home_sections'] = $raw_sections ? json_decode($raw_sections, true) : array();
        $this->data['home_sections'] = resolve_hero_media($this->data['home_sections'], $this->data['home_layout']);
        $raw_static = isset($this->front_setting->static_pages_data) ? $this->front_setting->static_pages_data : '';
        $this->data['static_pages']  = $raw_static ? json_decode($raw_static, true) : array();
        $this->data['header'] = $this->load->view('themes/' . $this->theme_path . '/header', $this->data, true);
        $this->data['slider'] = $this->load->view('themes/' . $this->theme_path . '/home_slider', $this->data, true);
        $this->data['footer'] = $this->load->view('themes/' . $this->theme_path . '/footer', $this->data, true);
        $this->base_assets_url = 'backend/' . THEMES_DIR . '/' . $this->theme_path . '/';

        $this->data['base_assets_url'] = BASE_URI . $this->base_assets_url;

        $is_captcha                  = $this->captchalib->is_captcha('appointment');
        $this->data["is_captcha"]    = $is_captcha;
        $captcha_result = $this->captchalib->generate_captcha();
        $this->data["captcha_image"] = is_array($captcha_result) ? $captcha_result['image'] : '';
        $this->data['content']       = (is_null($content)) ? '' : $this->load->view(THEMES_DIR . '/' . $this->theme_path . '/' . $content, $this->data, true);

        $this->load->view(THEMES_DIR . '/' . $this->theme_path . '/layout', $this->data);
    }

    private function check_installation()
    {
        if ($this->uri->segment(1) !== 'install') {
            $this->load->config('migration');
            if ($this->config->item('installed') == false && $this->config->item('migration_enabled') == false) {
                $proto    = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
                $app_root = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\') . '/';
                header('Location: ' . $proto . '://' . $_SERVER['HTTP_HOST'] . $app_root . 'install/start');
                exit();
            } else {
                if (is_dir(APPPATH . 'controllers/install')) {
                    echo '<h3>Delete the install folder from application/controllers/install</h3>';
                    die;
                }
            }
        }
    }

}
