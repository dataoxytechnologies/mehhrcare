<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Icd10_model extends MY_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    // ─── ICD CODES ───────────────────────────────────────────────────────────

    public function get($id = null)
    {
        $this->db->select('icd10_codes.id, icd10_codes.icd_code, icd10_codes.icd_description, icd10_codes.group_id, icd10_groups.group_name');
        $this->db->from('icd10_codes');
        $this->db->join('icd10_groups', 'icd10_groups.id = icd10_codes.group_id', 'left');
        if (!empty($id)) {
            $this->db->where('icd10_codes.id', $id);
            return $this->db->get()->row_array();
        }
        $this->db->order_by('icd10_codes.icd_code', 'ASC');
        return $this->db->get()->result_array();
    }

    public function getByGroup($group_id)
    {
        $this->db->select('icd10_codes.id, icd10_codes.icd_code, icd10_codes.icd_description');
        $this->db->from('icd10_codes');
        $this->db->where('icd10_codes.group_id', $group_id);
        $this->db->order_by('icd10_codes.icd_code', 'ASC');
        return $this->db->get()->result_array();
    }

    public function add($data)
    {
        $this->db->trans_start();
        $this->db->trans_strict(false);
        if (isset($data['id'])) {
            $this->db->where('id', $data['id']);
            $this->db->update('icd10_codes', $data);
            $message   = UPDATE_RECORD_CONSTANT . " On ICD10 Code id " . $data['id'];
            $record_id = $data['id'];
            $this->log($message, $record_id, 'Update');
        } else {
            $this->db->insert('icd10_codes', $data);
            $record_id = $this->db->insert_id();
            $message   = INSERT_RECORD_CONSTANT . " On ICD10 Code id " . $record_id;
            $this->log($message, $record_id, 'Insert');
        }
        $this->db->trans_complete();
        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            return false;
        }
        return $record_id;
    }

    public function remove($id)
    {
        $this->db->trans_start();
        $this->db->trans_strict(false);
        $this->db->where('id', $id)->delete('icd10_codes');
        $message = DELETE_RECORD_CONSTANT . " On ICD10 Code id " . $id;
        $this->log($message, $id, 'Delete');
        $this->db->trans_complete();
        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            return false;
        }
        return $id;
    }

    // ─── ICD GROUPS ──────────────────────────────────────────────────────────

    public function getgroup($id = null)
    {
        $this->db->select('id, group_name, description');
        $this->db->from('icd10_groups');
        if (!empty($id)) {
            $this->db->where('id', $id);
            return $this->db->get()->row_array();
        }
        $this->db->order_by('group_name', 'ASC');
        return $this->db->get()->result_array();
    }

    public function addgroup($data)
    {
        $this->db->trans_start();
        $this->db->trans_strict(false);
        if (isset($data['id'])) {
            $this->db->where('id', $data['id']);
            $this->db->update('icd10_groups', $data);
            $message   = UPDATE_RECORD_CONSTANT . " On ICD10 Group id " . $data['id'];
            $record_id = $data['id'];
            $this->log($message, $record_id, 'Update');
        } else {
            $this->db->insert('icd10_groups', $data);
            $record_id = $this->db->insert_id();
            $message   = INSERT_RECORD_CONSTANT . " On ICD10 Group id " . $record_id;
            $this->log($message, $record_id, 'Insert');
        }
        $this->db->trans_complete();
        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            return false;
        }
        return $record_id;
    }

    public function removegroup($id)
    {
        $this->db->trans_start();
        $this->db->trans_strict(false);
        $this->db->where('id', $id)->delete('icd10_groups');
        $message = DELETE_RECORD_CONSTANT . " On ICD10 Group id " . $id;
        $this->log($message, $id, 'Delete');
        $this->db->trans_complete();
        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            return false;
        }
        return $id;
    }

    // ─── JUNCTION: IPD ───────────────────────────────────────────────────────

    public function saveIpdCodes($ipd_id, $code_ids)
    {
        $this->db->trans_start();
        $this->db->trans_strict(false);
        $this->db->where('ipd_id', $ipd_id)->delete('ipd_icd10_codes');
        if (!empty($code_ids)) {
            $batch = array();
            foreach ($code_ids as $cid) {
                if (!empty($cid)) {
                    $batch[] = array('ipd_id' => $ipd_id, 'icd_code_id' => $cid);
                }
            }
            if (!empty($batch)) {
                $this->db->insert_batch('ipd_icd10_codes', $batch);
            }
        }
        $message = UPDATE_RECORD_CONSTANT . " ICD10 Codes for IPD id " . $ipd_id;
        $this->log($message, $ipd_id, 'Update');
        $this->db->trans_complete();
        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            return false;
        }
        return true;
    }

    public function getIpdCodes($ipd_id)
    {
        $this->db->select('icd10_codes.id, icd10_codes.icd_code, icd10_codes.icd_description, icd10_groups.group_name');
        $this->db->from('ipd_icd10_codes');
        $this->db->join('icd10_codes', 'icd10_codes.id = ipd_icd10_codes.icd_code_id', 'inner');
        $this->db->join('icd10_groups', 'icd10_groups.id = icd10_codes.group_id', 'left');
        $this->db->where('ipd_icd10_codes.ipd_id', $ipd_id);
        $this->db->order_by('icd10_codes.icd_code', 'ASC');
        return $this->db->get()->result_array();
    }

    // ─── JUNCTION: OPD ───────────────────────────────────────────────────────

    public function saveOpdCodes($opd_id, $code_ids)
    {
        $this->db->trans_start();
        $this->db->trans_strict(false);
        $this->db->where('opd_id', $opd_id)->delete('opd_icd10_codes');
        if (!empty($code_ids)) {
            $batch = array();
            foreach ($code_ids as $cid) {
                if (!empty($cid)) {
                    $batch[] = array('opd_id' => $opd_id, 'icd_code_id' => $cid);
                }
            }
            if (!empty($batch)) {
                $this->db->insert_batch('opd_icd10_codes', $batch);
            }
        }
        $message = UPDATE_RECORD_CONSTANT . " ICD10 Codes for OPD id " . $opd_id;
        $this->log($message, $opd_id, 'Update');
        $this->db->trans_complete();
        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            return false;
        }
        return true;
    }

    public function getOpdCodes($opd_id)
    {
        $this->db->select('icd10_codes.id, icd10_codes.icd_code, icd10_codes.icd_description, icd10_groups.group_name');
        $this->db->from('opd_icd10_codes');
        $this->db->join('icd10_codes', 'icd10_codes.id = opd_icd10_codes.icd_code_id', 'inner');
        $this->db->join('icd10_groups', 'icd10_groups.id = icd10_codes.group_id', 'left');
        $this->db->where('opd_icd10_codes.opd_id', $opd_id);
        $this->db->order_by('icd10_codes.icd_code', 'ASC');
        return $this->db->get()->result_array();
    }

}
