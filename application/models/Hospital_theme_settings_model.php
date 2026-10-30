<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Hospital_theme_settings_model extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function get()
    {
        $this->db->select('id, theme_preset, primary_color, font_color, font_family, corner_radius, text_size, density');
        $this->db->from('hospital_theme_settings');
        $this->db->where('id', 1);
        $query = $this->db->get();
        return $query->row_array();
    }

    public function update($data)
    {
        $this->db->trans_start();
        $this->db->trans_strict(false);

        $this->db->where('id', 1);
        $this->db->update('hospital_theme_settings', $data);

        $message = UPDATE_RECORD_CONSTANT . ' On Hospital Theme Settings id 1';
        $action  = UPDATE_RECORD_CONSTANT;
        $this->log($message, 1, $action);

        $this->db->trans_complete();

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            return false;
        }
        return 1;
    }
}
