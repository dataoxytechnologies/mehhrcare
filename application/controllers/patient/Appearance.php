<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Appearance extends Patient_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('user_theme_preference_model');
        $this->load->model('hospital_theme_settings_model');
    }

    public function index()
    {
        $patient = $this->session->userdata('patient');
        $this->session->set_userdata('top_menu', 'Appearance');

        $data['title']          = $this->lang->line('theme');
        $data['user_pref']      = $this->user_theme_preference_model->getByUser($patient['patient_id'], 'patient');
        $data['hospital_theme'] = $this->hospital_theme_settings_model->get();

        $this->load->view('layout/patient/header', $data);
        $this->load->view('patient/appearance/index', $data);
        $this->load->view('layout/patient/footer', $data);
    }

    public function save()
    {
        $patient = $this->session->userdata('patient');
        if (empty($patient['patient_id'])) {
            echo json_encode(['status' => 'fail', 'error' => 'unauth', 'message' => '']);
            return;
        }

        $this->form_validation->set_rules('theme_preset', 'Theme',     'trim|required|xss_clean|in_list[clinical,slate,midnight,oled,nightshift]');
        $this->form_validation->set_rules('text_size',    'Text size', 'trim|required|xss_clean|in_list[compact,normal,comfort,large]');
        $this->form_validation->set_rules('density',      'Density',   'trim|required|xss_clean|in_list[compact,comfortable,spacious]');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode([
                'status'  => 'fail',
                'error'   => $this->form_validation->error_array(),
                'message' => '',
            ]);
            return;
        }

        $payload = [
            'theme_preset' => $this->input->post('theme_preset', TRUE),
            'text_size'    => $this->input->post('text_size',    TRUE),
            'density'      => $this->input->post('density',      TRUE),
        ];

        $ok = $this->user_theme_preference_model->upsert($patient['patient_id'], 'patient', $payload);

        if ($ok) {
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

    public function reset()
    {
        $patient = $this->session->userdata('patient');
        if (empty($patient['patient_id'])) {
            echo json_encode(['status' => 'fail', 'error' => 'unauth', 'message' => '']);
            return;
        }

        $ok = $this->user_theme_preference_model->deleteByUser($patient['patient_id'], 'patient');

        if ($ok) {
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
