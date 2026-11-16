<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Add payment_mode column to patient_flow_visits so the board can show how
 * a programme bill was settled (Cash / Online / Razorpay).
 */
class Migration_add_payment_mode_to_patient_flow_visits extends CI_Migration
{
    public function up()
    {
        if (!$this->db->field_exists('payment_mode', 'patient_flow_visits')) {
            $this->dbforge->add_column('patient_flow_visits', array(
                'payment_mode' => array(
                    'type'       => 'VARCHAR',
                    'constraint' => 20,
                    'null'       => TRUE,
                    'after'      => 'cash_received',
                ),
            ));
        }
    }

    public function down()
    {
        if ($this->db->field_exists('payment_mode', 'patient_flow_visits')) {
            $this->dbforge->drop_column('patient_flow_visits', 'payment_mode');
        }
    }
}
