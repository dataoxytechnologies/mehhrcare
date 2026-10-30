<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Themestudio extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('hospital_theme_settings_model');
        $this->load->model('user_theme_preference_model');
    }

    public function index()
    {
        if (!$this->rbac->hasPrivilege('theme_studio', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'setup');
        $this->session->set_userdata('sub_menu', 'schsettings/index');
        $this->session->set_userdata('inner_menu', 'admin/themestudio');

        $data['title']          = $this->lang->line('theme_studio');
        $data['module']         = 'setup';
        $data['hospital_theme'] = $this->hospital_theme_settings_model->get();

        $this->load->view('layout/header', $data);
        $this->load->view('admin/themestudio/index', $data);
        $this->load->view('layout/footer', $data);
    }

    public function save()
    {
        if (!$this->rbac->hasPrivilege('theme_studio', 'can_edit')) {
            echo json_encode([
                'status'  => 'fail',
                'error'   => 'access_denied',
                'message' => $this->lang->line('access_denied'),
            ]);
            return;
        }

        $this->form_validation->set_rules('theme_preset',  'Theme',        'trim|required|xss_clean|in_list[clinical,slate,midnight,oled,nightshift]');
        $this->form_validation->set_rules('primary_color', 'Primary color','trim|required|xss_clean|regex_match[/^#[0-9A-Fa-f]{6}$/]');
        $this->form_validation->set_rules('font_color',    'Font color',   'trim|required|xss_clean|regex_match[/^#[0-9A-Fa-f]{6}$/]');
        $this->form_validation->set_rules('font_family',   'Font',         'trim|required|xss_clean|in_list[Inter,Roboto,Nunito]');
        $this->form_validation->set_rules('corner_radius', 'Corner',       'trim|required|xss_clean|in_list[sharp,soft,rounded]');
        $this->form_validation->set_rules('text_size',     'Text size',    'trim|required|xss_clean|in_list[compact,normal,comfort,large]');
        $this->form_validation->set_rules('density',       'Density',      'trim|required|xss_clean|in_list[compact,comfortable,spacious]');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode([
                'status'  => 'fail',
                'error'   => $this->form_validation->error_array(),
                'message' => '',
            ]);
            return;
        }

        $payload = [
            'theme_preset'  => $this->input->post('theme_preset',  TRUE),
            'primary_color' => $this->input->post('primary_color', TRUE),
            'font_color'    => $this->input->post('font_color',    TRUE),
            'font_family'   => $this->input->post('font_family',   TRUE),
            'corner_radius' => $this->input->post('corner_radius', TRUE),
            'text_size'     => $this->input->post('text_size',     TRUE),
            'density'       => $this->input->post('density',       TRUE),
        ];

        $ok = $this->hospital_theme_settings_model->update($payload);

        if ($ok) {
            // Saver (superadmin) was just setting the hospital brand — they expect
            // to SEE the new brand immediately on next page load. Clear their own
            // personal user_theme_preferences row (often pre-populated by the
            // migration mapping sh_variant -> preset) so the cascade falls back
            // to the freshly-saved hospital defaults instead of shadowing them
            // with the saver's old personal pref. Customer can later set a
            // personal override via /admin/appearance if desired.
            $admin = $this->session->userdata('hospitaladmin');
            if (!empty($admin['id'])) {
                $this->user_theme_preference_model->deleteByUser($admin['id'], 'staff');
            }
            echo json_encode([
                'status'  => 'success',
                'error'   => '',
                'message' => $this->lang->line('success_message'),
            ]);
        } else {
            echo json_encode([
                'status'  => 'fail',
                'error'   => 'db_error',
                'message' => '',
            ]);
        }
    }
}
