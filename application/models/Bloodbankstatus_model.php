<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Bloodbankstatus_model extends MY_Model
{

    public function getBloodGroup($id = null, $type = null)
    {
        if (!empty($id)) {
            $query = $this->db->where("id", $id)->get('blood_bank_products');
            return $query->row_array();
        } else {
            if ($type != null) {
                $query = $this->db->where("is_blood_group", $type)->get("blood_bank_products");
            } else {
                $query = $this->db->get("blood_bank_products");
            }
            return $query->result_array();
        }
    }

    public function getBloodbank($patient_id)
    {
        $i             = 1;
        $custom_fields = $this->customfield_model->get_custom_fields('blood_issue', '', '', '', 1);

        $custom_field_column_array = array();
        $field_var_array           = array();
        if (!empty($custom_fields)) {
            foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
                $tb_counter = "table_custom_" . $i;
                array_push($custom_field_column_array, 'table_custom_' . $i . '.field_value');
                array_push($field_var_array, '`table_custom_' . $i . '`.`field_value` as `' . $custom_fields_value->name . '`');
                $this->db->join('custom_field_values as ' . $tb_counter, 'blood_issue.id = ' . $tb_counter . '.belong_table_id AND ' . $tb_counter . '.custom_field_id = ' . $custom_fields_value->id, "left");
                $i++;
            }
        }

        $field_variable      = (empty($field_var_array)) ? "" : "," . implode(',', $field_var_array);
        $custom_field_column = (empty($custom_field_column_array)) ? "" : "," . implode(',', $custom_field_column_array);

        $query = $this->db->select('blood_issue.*,sum(transactions.amount)as paid_amount,blood_bank_products.name as blood_group,patients.patient_name,patients.gender,blood_donor.donor_name,blood_donor_cycle.bag_no,blood_donor_cycle.unit' . $field_variable)
            ->join('patients', 'patients.id = blood_issue.patient_id')
            ->join('blood_donor_cycle', 'blood_donor_cycle.id = blood_issue.blood_donor_cycle_id')
            ->join('blood_donor', 'blood_donor_cycle.blood_donor_id = blood_donor.id')
            ->join('blood_bank_products', 'blood_bank_products.id = blood_donor.blood_bank_product_id')
            ->join('transactions', 'transactions.blood_issue_id = blood_issue.id')
            ->group_by('transactions.blood_issue_id')
            ->where('blood_issue.patient_id', $patient_id)
            ->get('blood_issue');
        return $query->result_array();
    }

    public function getBloodbankStatusByid($blood_bank_product_id)
    {
        return $this->db->select('sum(blood_donor_cycle.quantity) as total')
            ->join('blood_donor', 'blood_donor.id=blood_donor_cycle.blood_donor_id')
            ->join('blood_bank_products', 'blood_donor.blood_bank_product_id=blood_bank_products.id')
            ->where('blood_donor.blood_bank_product_id', $blood_bank_product_id)
            ->where('blood_donor_cycle.available', 1)
            ->get('blood_donor_cycle')->row_array();
    }

    public function getBillDetailsBloodbank($id)
    {
        $i             = 1;
        $custom_fields = $this->customfield_model->get_custom_fields('blood_issue');
        $custom_field_column_array = array();
        $field_var_array           = array();
        if (!empty($custom_fields)) {
            foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
                $tb_counter = "table_custom_" . $i;
                array_push($custom_field_column_array, 'table_custom_' . $i . '.field_value');
                array_push($field_var_array, '`table_custom_' . $i . '`.`field_value` as `' . $custom_fields_value->name . '`');
                $this->db->join('custom_field_values as ' . $tb_counter, 'blood_issue.id = ' . $tb_counter . '.belong_table_id AND ' . $tb_counter . '.custom_field_id = ' . $custom_fields_value->id, "left");
                $i++;
            }
        }

        $field_variable      = (empty($field_var_array)) ? "" : "," . implode(',', $field_var_array);
        $custom_field_column = (empty($custom_field_column_array)) ? "" : "," . implode(',', $custom_field_column_array);   

        $this->db->select('blood_issue.*,organisation.organisation_name,blood_issue.insurance_id,blood_issue.insurance_validity,IFNULL((SELECT SUM(amount) FROM transactions WHERE blood_issue_id=blood_issue.id and transactions.patient_id = blood_issue.patient_id),0) as total_deposit, blood_bank_products.id as blood_group_id,blood_bank_products.name as blood_group,patients.patient_name,patients.gender,blood_donor.donor_name,blood_donor_cycle.bag_no,blood_donor_cycle.volume,blood_donor_cycle.unit,blood_donor_cycle.quantity,blood_donor_cycle.donate_date,blood_donor_cycle.lot,charge_type_master.id as charge_type_id,charge_type_master.charge_type,charge_categories.name as charge_category_name,charges.charge_category_id,charge_units.unit as unit_name' . $field_variable);
        $this->db->join('patients', 'patients.id = blood_issue.patient_id', "left");
        $this->db->join('blood_donor_cycle', 'blood_donor_cycle.id = blood_issue.blood_donor_cycle_id', "left");
        $this->db->join('blood_donor', 'blood_donor_cycle.blood_donor_id = blood_donor.id', "left");
        $this->db->join('blood_bank_products', 'blood_bank_products.id = blood_donor.blood_bank_product_id', "left");
        $this->db->join('charges', 'blood_issue.charge_id = charges.id', 'inner');
        $this->db->join('charge_categories', 'charge_categories.id = charges.charge_category_id', 'inner');
        $this->db->join('charge_units', 'charge_units.id = blood_donor_cycle.unit', 'left');
        $this->db->join("charge_type_master", 'charge_categories.charge_type_id = charge_type_master.id', "left");        
        $this->db->join("organisation", 'organisation.id = blood_issue.organisation_id', "left");
        $this->db->where('blood_issue.id', $id);
        $query = $this->db->get('blood_issue');
        return $query->row_array();
    }

    public function addBloodGroup($data)
    {
        if (isset($data['id'])) {
            $this->db->where('id', $data['id']);
            $this->db->update('blood_bank_status', $data);
        } else {
            $this->db->insert('blood_bank_status', $data);
            return $this->db->insert_id();
        }
    }

    public function getall()
    {
        $this->datatables->select('id,blood_group,status');
        $this->datatables->from('blood_bank_status');
        $this->datatables->add_column('view', '<a href="' . site_url('admin/bloodbankstatuss/edit/$1') . '" class="btn btn-default btn-xs" data-toggle="tooltip" title="" data-original-title="Edit"> <i class="fa fa-pencil"></i></a><a href="' . site_url('admin/bloodgroup/delete/$1') . '" class="btn btn-default btn-xs" data-toggle="tooltip" title="" data-original-title="Delete">
                                                        <i class="fa fa-remove"></i>
                                                    </a>', 'id,status');
        return $this->datatables->generate();
    }

    public function getDatatableAllproducts()
    {
        $this->datatables
            ->select('blood_bank_products.*')
            ->searchable('name')
            ->orderable('name,is_blood_group,volume')
            ->sort('id', 'desc')
            ->from('blood_bank_products');
        return $this->datatables->generate('json');
    }

    public function add_product($data)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================

        if (isset($data['id']) && $data['id'] != '') {
            $this->db->where('id', $data['id']);
            $this->db->update('blood_bank_products', $data);
            $message = UPDATE_RECORD_CONSTANT . " On Blood Bank Products id " . $data['id'];
            $action = "Update";
            $record_id = $data['id'];
            $this->log($message, $record_id, $action);
        } else {
            $this->db->insert('blood_bank_products', $data);
            $insert_id = $this->db->insert_id();
            $message = INSERT_RECORD_CONSTANT . " On Blood Bank Products id " . $insert_id;
            $action = "Insert";
            $record_id = $insert_id;
            $this->log($message, $record_id, $action);
        }

        //======================Code End==============================
        $this->db->trans_complete(); # Completing transaction
        if ($this->db->trans_status() === false) {
            # Something went wrong.
            $this->db->trans_rollback();
            return false;
        } else {
            return $record_id;
        }

    }
    public function valid_product($str)
    {
        $name = $this->input->post('name');
        if ($this->check_name_exists($name)) {
            $this->form_validation->set_message('check_exists', 'Name already exists');
            return false;
        } else {
            return true;
        }
    }

    public function check_name_exists($name)
    {
        $id = $this->input->post("id");
        if ($id != 0) {
            $data  = array('name' => $name, 'id !=' => $id);
            $query = $this->db->where($data)->get('blood_bank_products');

            if ($query->num_rows() > 0) {
                return true;
            } else {
                return false;
            }
        } else {
            $this->db->where('name', $name);
            $query = $this->db->get('blood_bank_products');
            if ($query->num_rows() > 0) {
                return true;
            } else {
                return false;
            }
        }
    }

    public function get_product($id = null, $type = null)
    {
        if ($id != null) {

            $query = $this->db->where("id", $id)->get('blood_bank_products');
            return $query->row_array();

        } else {

            $query   = $this->db->get("blood_bank_products");
            $list    = $query->result_array();
            $product = array();
            foreach ($list as $key => $value) {
                if ($type == '') {
                    $product[$value['id']] = $value['name'];
                } else {
                    if ($type == $value['is_blood_group']) {
                        $product[$value['id']] = $value['name'];
                    }

                }

            }
            return $product;

        }
    }

    public function delete_product($id)
    {
        $this->db->where('id', $id);
        $this->db->delete('blood_bank_products');
        //insert log
        $message = DELETE_RECORD_CONSTANT . " On Blood Bank Products id " . $id;
        $action = "Delete";
        $record_id = $id;
        $this->log($message, $record_id, $action);
        //insert log
        return true;
    }

    public function deleteComponent($id)
    {
        $this->db->where('id', $id);
        $this->db->delete('blood_donor_cycle');
        return true;
    }

    public function updatestockbyid($blood_donor_cycle_id)
    {
        $data = array('available' => 0);
        $this->db->where('id', $blood_donor_cycle_id);
        $this->db->update('blood_donor_cycle', $data);
    }

    public function get_stock_bloodgroup()
    {
        return $this->db->select('blood_bank_products.id,blood_bank_products.name ')->from('blood_donor')->join('blood_bank_products', 'blood_bank_products.id=blood_donor.blood_bank_product_id')->group_by('blood_donor.blood_bank_product_id')->get()->result_array();
    }

    public function validate_paymentamount()
    {
        $payment_amount = $this->input->post('payment_amount');
        $net_amount     = $this->input->post('net_amount');
        if ($payment_amount > $net_amount) {

            $this->form_validation->set_message('check_exists', 'Amount should not be greater than balance ' . $net_amount);
            return false;
        } else {
            return true;
        }

    }

    /**
     * Dashboard widget: stock units per blood group.
     * Returns: ['O+'=>24, 'A+'=>18, 'B+'=>12, 'AB+'=>3, 'O-'=>8, 'A-'=>2, 'B-'=>5, 'AB-'=>1]
     * Used by admin/admin/dashboard Variant B (2026-05-15).
     */
    public function getStockByGroup()
    {
        $groups = $this->db->where('is_blood_group', 1)->get('blood_bank_products')->result_array();
        $stock = array();
        foreach ($groups as $g) {
            $row = $this->db->select('SUM(blood_donor_cycle.quantity) as total', false)
                ->join('blood_donor', 'blood_donor.id = blood_donor_cycle.blood_donor_id')
                ->where('blood_donor.blood_bank_product_id', $g['id'])
                ->where('blood_donor_cycle.available', 1)
                ->get('blood_donor_cycle')
                ->row_array();
            $stock[$g['name']] = (int)($row['total'] ?? 0);
        }
        return $stock;
    }

}
