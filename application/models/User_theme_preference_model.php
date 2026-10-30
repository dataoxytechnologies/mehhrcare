<?php defined('BASEPATH') OR exit('No direct script access allowed');

class User_theme_preference_model extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getByUser($user_id, $user_type)
    {
        $this->db->select('id, theme_preset, text_size, density');
        $this->db->from('user_theme_preferences');
        $this->db->where('user_id', (int)$user_id);
        $this->db->where('user_type', $user_type);
        $query = $this->db->get();
        return $query->row_array();
    }

    public function upsert($user_id, $user_type, $data)
    {
        $this->db->trans_start();
        $this->db->trans_strict(false);

        $existing = $this->getByUser($user_id, $user_type);
        if ($existing) {
            $this->db->where('user_id', (int)$user_id);
            $this->db->where('user_type', $user_type);
            $this->db->update('user_theme_preferences', $data);
            $id = (int)$existing['id'];
            $message = UPDATE_RECORD_CONSTANT . ' On User Theme Preferences id ' . $id;
            $action  = UPDATE_RECORD_CONSTANT;
        } else {
            $data['user_id']   = (int)$user_id;
            $data['user_type'] = $user_type;
            $this->db->insert('user_theme_preferences', $data);
            $id = $this->db->insert_id();
            $message = INSERT_RECORD_CONSTANT . ' On User Theme Preferences id ' . $id;
            $action  = INSERT_RECORD_CONSTANT;
        }
        $this->log($message, $id, $action);

        $this->db->trans_complete();

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            return false;
        }
        return $id;
    }

    public function deleteByUser($user_id, $user_type)
    {
        $this->db->trans_start();
        $this->db->trans_strict(false);

        $existing = $this->getByUser($user_id, $user_type);
        if ($existing) {
            $this->db->where('user_id', (int)$user_id);
            $this->db->where('user_type', $user_type);
            $this->db->delete('user_theme_preferences');

            $message = DELETE_RECORD_CONSTANT . ' On User Theme Preferences id ' . $existing['id'];
            $action  = DELETE_RECORD_CONSTANT;
            $this->log($message, $existing['id'], $action);
        }

        $this->db->trans_complete();
        return $this->db->trans_status() !== false;
    }
}
