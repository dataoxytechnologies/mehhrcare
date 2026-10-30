<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Patientflow_model
 *
 * Handles the Patient Flow module data: workflow levels (the R1 -> R2 -> Cash ->
 * ... chain) and later the visits/transitions. Parent/child relationships are
 * driven by patient_flow_levels.parent_id.
 */
class Patientflow_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->ensure_schema();
    }

    /**
     * Ensure database columns and tables required for patient flow and UUID exist.
     */
    public function ensure_schema()
    {
        // 1) Ensure patient_uuid column exists on patients table
        if ($this->db->table_exists('patients')) {
            if (!$this->db->field_exists('patient_uuid', 'patients')) {
                $this->db->query("ALTER TABLE `patients` ADD COLUMN `patient_uuid` VARCHAR(60) NULL DEFAULT NULL COMMENT 'Patient UUID MC-KTR-YYYY-######' AFTER `identification_number`");
                @$this->db->query("ALTER TABLE `patients` ADD UNIQUE KEY `uniq_patient_uuid` (`patient_uuid`)");
            }
        }

        // 2) Ensure opd_id column exists on patient_flow_visits table —
        //    every flow visit gets a core OPD record created at registration.
        if ($this->db->table_exists('patient_flow_visits')) {
            if (!$this->db->field_exists('opd_id', 'patient_flow_visits')) {
                $this->db->query("ALTER TABLE `patient_flow_visits` ADD COLUMN `opd_id` INT(11) NULL DEFAULT NULL COMMENT '-> opd_details.id (OPD created at registration)' AFTER `token_no`");
            }
        }

        // 3) Level-2 (R2) billing reference columns on patient_flow_visits.
        if ($this->db->table_exists('patient_flow_visits')) {
            if (!$this->db->field_exists('billing_reference', 'patient_flow_visits')) {
                $this->db->query("ALTER TABLE `patient_flow_visits` ADD COLUMN `billing_reference` VARCHAR(60) NULL DEFAULT NULL COMMENT 'BR-KTR-YYMMDD-###### generated at R2' AFTER `invoice_ref`");
            }
            if (!$this->db->field_exists('billing_status', 'patient_flow_visits')) {
                $this->db->query("ALTER TABLE `patient_flow_visits` ADD COLUMN `billing_status` VARCHAR(20) NOT NULL DEFAULT 'pending' COMMENT 'pending | billed | paid | cancelled' AFTER `billing_reference`");
            }
            if (!$this->db->field_exists('billing_reference_at', 'patient_flow_visits')) {
                $this->db->query("ALTER TABLE `patient_flow_visits` ADD COLUMN `billing_reference_at` DATETIME NULL DEFAULT NULL AFTER `billing_status`");
            }
            if (!$this->db->field_exists('billing_status_updated_by', 'patient_flow_visits')) {
                $this->db->query("ALTER TABLE `patient_flow_visits` ADD COLUMN `billing_status_updated_by` INT(11) NULL DEFAULT NULL COMMENT '-> staff.id' AFTER `billing_reference_at`");
            }
            if (!$this->db->field_exists('billing_status_updated_at', 'patient_flow_visits')) {
                $this->db->query("ALTER TABLE `patient_flow_visits` ADD COLUMN `billing_status_updated_at` DATETIME NULL DEFAULT NULL AFTER `billing_status_updated_by`");
            }
            // Visit lifecycle status: active | completed | cancelled
            if (!$this->db->field_exists('status', 'patient_flow_visits')) {
                $this->db->query("ALTER TABLE `patient_flow_visits` ADD COLUMN `status` VARCHAR(20) NOT NULL DEFAULT 'active' COMMENT 'active | completed | cancelled' AFTER `billing_status_updated_at`");
            }
            if (!$this->db->field_exists('completed_at', 'patient_flow_visits')) {
                $this->db->query("ALTER TABLE `patient_flow_visits` ADD COLUMN `completed_at` DATETIME NULL DEFAULT NULL AFTER `status`");
            }
            if (!$this->db->field_exists('completed_by', 'patient_flow_visits')) {
                $this->db->query("ALTER TABLE `patient_flow_visits` ADD COLUMN `completed_by` INT(11) NULL DEFAULT NULL COMMENT '-> staff.id' AFTER `completed_at`");
            }

            // 4) Token queue lifecycle (P1-L5 Token Control / R1).
            //    Token becomes ACTIVE only after payment confirmation; R1 then
            //    manages the queue: WAITING -> CALLED -> IN_PROCESS -> CLOSED.
            if (!$this->db->field_exists('token_status', 'patient_flow_visits')) {
                $this->db->query("ALTER TABLE `patient_flow_visits` ADD COLUMN `token_status` VARCHAR(30) NOT NULL DEFAULT 'NOT_ISSUED' COMMENT 'NOT_ISSUED | ACTIVE | WAITING | CALLED | IN_PROCESS | CLOSED | NO_SHOW | SKIPPED | WAITING_SECOND_CALL' AFTER `token_no`");
            }
            if (!$this->db->field_exists('token_activated_at', 'patient_flow_visits')) {
                $this->db->query("ALTER TABLE `patient_flow_visits` ADD COLUMN `token_activated_at` DATETIME NULL DEFAULT NULL COMMENT 'first became ACTIVE (payment confirmed)' AFTER `token_status`");
            }
            if (!$this->db->field_exists('token_closed_at', 'patient_flow_visits')) {
                $this->db->query("ALTER TABLE `patient_flow_visits` ADD COLUMN `token_closed_at` DATETIME NULL DEFAULT NULL COMMENT 'reached a terminal token status' AFTER `token_activated_at`");
            }
            // Cash-received status: 0 = not received, 1 = received.
            // Only a staff whose level has can_cash_received may change it.
            if (!$this->db->field_exists('cash_received', 'patient_flow_visits')) {
                $this->db->query("ALTER TABLE `patient_flow_visits` ADD COLUMN `cash_received` TINYINT(1) NOT NULL DEFAULT 0 COMMENT '0 = not received | 1 = received' AFTER `token_closed_at`");
            }
            // Programme status: enroll | declined | deferred.
            // Only a staff whose level has can_change_programme_status may change it.
            if (!$this->db->field_exists('programme_status', 'patient_flow_visits')) {
                $this->db->query("ALTER TABLE `patient_flow_visits` ADD COLUMN `programme_status` VARCHAR(20) NULL DEFAULT NULL COMMENT 'enroll | declined | deferred' AFTER `cash_received`");
            }
        }

        // 5) Level permission: staff whose role owns a level with this flag
        //    may change a visit's payment status in the patient flow.
        if ($this->db->table_exists('patient_flow_levels')) {
            if (!$this->db->field_exists('can_change_payment', 'patient_flow_levels')) {
                $this->db->query("ALTER TABLE `patient_flow_levels` ADD COLUMN `can_change_payment` TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'staff owning this level may toggle visit payment status' AFTER `can_approve`");
            }
            if (!$this->db->field_exists('can_generate_billing_ref', 'patient_flow_levels')) {
                $this->db->query("ALTER TABLE `patient_flow_levels` ADD COLUMN `can_generate_billing_ref` TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'staff owning this level may generate billing references / change billing status' AFTER `can_change_payment`");
            }
            if (!$this->db->field_exists('can_print_billing', 'patient_flow_levels')) {
                $this->db->query("ALTER TABLE `patient_flow_levels` ADD COLUMN `can_print_billing` TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'staff owning this level may print billing slips' AFTER `can_generate_billing_ref`");
            }
            if (!$this->db->field_exists('can_change_status', 'patient_flow_levels')) {
                $this->db->query("ALTER TABLE `patient_flow_levels` ADD COLUMN `can_change_status` TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'staff owning this level may change the visit status (active/completed/cancelled)' AFTER `can_print_billing`");
            }
            if (!$this->db->field_exists('can_generate_token', 'patient_flow_levels')) {
                $this->db->query("ALTER TABLE `patient_flow_levels` ADD COLUMN `can_generate_token` TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'staff owning this level may generate/activate queue tokens' AFTER `can_change_status`");
            }
            if (!$this->db->field_exists('can_create_pbhp_id', 'patient_flow_levels')) {
                $this->db->query("ALTER TABLE `patient_flow_levels` ADD COLUMN `can_create_pbhp_id` TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'staff owning this level may create PBHP IDs' AFTER `can_generate_token`");
            }
            if (!$this->db->field_exists('can_cash_received', 'patient_flow_levels')) {
                $this->db->query("ALTER TABLE `patient_flow_levels` ADD COLUMN `can_cash_received` TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'staff owning this level may change the cash received status' AFTER `can_create_pbhp_id`");
            }
            if (!$this->db->field_exists('can_change_programme_status', 'patient_flow_levels')) {
                $this->db->query("ALTER TABLE `patient_flow_levels` ADD COLUMN `can_change_programme_status` TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'staff owning this level may change the programme status (enroll/declined/deferred)' AFTER `can_cash_received`");
            }
            // 7) Prescribe medicine permission
            if (!$this->db->field_exists('can_prescribe_medicine', 'patient_flow_levels')) {
                $this->db->query("ALTER TABLE `patient_flow_levels` ADD COLUMN `can_prescribe_medicine` TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'staff owning this level may prescribe medicine' AFTER `can_change_programme_status`");
            }
            // 8) Print vial label permission
            if (!$this->db->field_exists('print_vial', 'patient_flow_levels')) {
                $this->db->query("ALTER TABLE `patient_flow_levels` ADD COLUMN `print_vial` TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'staff owning this level may print vial labels' AFTER `can_prescribe_medicine`");
            }
            // 9) Generate bill permission
            if (!$this->db->field_exists('generate_bill', 'patient_flow_levels')) {
                $this->db->query("ALTER TABLE `patient_flow_levels` ADD COLUMN `generate_bill` TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'staff owning this level may generate bills' AFTER `print_vial`");
            }
        }
        // 6) Level permission: staff whose role owns a level with this flag
        //    may generate an invoice number for a visit.
        if ($this->db->table_exists('patient_flow_levels')) {
            if (!$this->db->field_exists('can_generate_invoice', 'patient_flow_levels')) {
                $this->db->query("ALTER TABLE `patient_flow_levels` ADD COLUMN `can_generate_invoice` TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'staff owning this level may generate invoice numbers' AFTER `can_generate_billing_ref`");
            }
        }

        // 7) Invoice number columns on patient_flow_visits (same pattern as the
        //    billing reference columns, but independent of billing status).
        if ($this->db->table_exists('patient_flow_visits')) {
            if (!$this->db->field_exists('invoice_no', 'patient_flow_visits')) {
                $this->db->query("ALTER TABLE `patient_flow_visits` ADD COLUMN `invoice_no` VARCHAR(60) NULL DEFAULT NULL COMMENT 'INV-KTR-YYMMDD-###### generated invoice number' AFTER `billing_reference_at`");
            }
            if (!$this->db->field_exists('invoice_no_at', 'patient_flow_visits')) {
                $this->db->query("ALTER TABLE `patient_flow_visits` ADD COLUMN `invoice_no_at` DATETIME NULL DEFAULT NULL AFTER `invoice_no`");
            }
        }

        // 8) Level permission: staff whose role owns a level with this flag
        //    may perform case taking for the visit.
        if ($this->db->table_exists('patient_flow_levels')) {
            if (!$this->db->field_exists('can_case_taking', 'patient_flow_levels')) {
                $this->db->query("ALTER TABLE `patient_flow_levels` ADD COLUMN `can_case_taking` TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'staff owning this level may perform case taking' AFTER `can_generate_invoice`");
            }
        }

        // 8c) Level permission: staff whose role owns a level with this flag
        //     may perform case taking with senior approval (second authorization).
        if ($this->db->table_exists('patient_flow_levels')) {
            if (!$this->db->field_exists('can_senior_approval', 'patient_flow_levels')) {
                $this->db->query("ALTER TABLE `patient_flow_levels` ADD COLUMN `can_senior_approval` TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'senior approval required for case taking changes' AFTER `can_view_case_taking`");
            }
        }

        // 9) Case-taking columns on patient_flow_visits (type + description,
        //    plus who/when for the view modal).
        if ($this->db->table_exists('patient_flow_visits')) {
            if (!$this->db->field_exists('case_taking_type', 'patient_flow_visits')) {
                $this->db->query("ALTER TABLE `patient_flow_visits` ADD COLUMN `case_taking_type` VARCHAR(100) NULL DEFAULT NULL COMMENT 'case taking type (key of the case-type list)' AFTER `programme_status`");
            }
            if (!$this->db->field_exists('case_taking_description', 'patient_flow_visits')) {
                $this->db->query("ALTER TABLE `patient_flow_visits` ADD COLUMN `case_taking_description` TEXT NULL DEFAULT NULL AFTER `case_taking_type`");
            }
            if (!$this->db->field_exists('case_taking_at', 'patient_flow_visits')) {
                $this->db->query("ALTER TABLE `patient_flow_visits` ADD COLUMN `case_taking_at` DATETIME NULL DEFAULT NULL AFTER `case_taking_description`");
            }
            if (!$this->db->field_exists('case_taking_by', 'patient_flow_visits')) {
                $this->db->query("ALTER TABLE `patient_flow_visits` ADD COLUMN `case_taking_by` INT(11) NULL DEFAULT NULL COMMENT '-> staff.id' AFTER `case_taking_at`");
            }
            // 9b) Case-taking detail columns (sex, concern, single BOD/FSH/NOS
            //     picks, assessment certainty, workflow status, conditional and
            //     supporting information) — single source: case_taking_columns().
            foreach ($this->case_taking_columns() as $ct_name => $ct_col) {
                if (!$this->db->field_exists($ct_name, 'patient_flow_visits')) {
                    $this->db->query("ALTER TABLE `patient_flow_visits` ADD COLUMN `$ct_name` " . $ct_col['ddl'] . " AFTER `case_taking_by`");
                }
            }
            // Ensure ct_programme_id column exists (Programme Enrolment dropdown).
            if (!$this->db->field_exists('ct_programme_id', 'patient_flow_visits')) {
                $this->db->query("ALTER TABLE `patient_flow_visits` ADD COLUMN `ct_programme_id` VARCHAR(20) NULL DEFAULT NULL COMMENT '-> patient_flow_programme.code' AFTER `case_taking_by`");
            }
            // 9c) Legacy multi-slot constitution columns (ct_bod2..4 / ct_fsh2..4 /
            //     ct_nos2..4) are no longer used — a patient has ONE body build,
            //     ONE face shape and ONE nose type. Drop them when they hold no data.
            foreach (array('ct_bod2', 'ct_bod3', 'ct_bod4', 'ct_fsh2', 'ct_fsh3', 'ct_fsh4', 'ct_nos2', 'ct_nos3', 'ct_nos4') as $legacy_col) {
                if (!$this->db->field_exists($legacy_col, 'patient_flow_visits')) {
                    continue;
                }
                $used = (int) $this->db->query("SELECT COUNT(*) AS n FROM `patient_flow_visits` WHERE `$legacy_col` IS NOT NULL")->row()->n;
                if ($used === 0) {
                    $this->db->query("ALTER TABLE `patient_flow_visits` DROP COLUMN `$legacy_col`");
                }
            }
        }

        // Add reason and broad_area_series columns to patient_flow_visits for prescription tracking
        if ($this->db->table_exists('patient_flow_visits')) {
            if (!$this->db->field_exists('reason', 'patient_flow_visits')) {
                $this->db->query("ALTER TABLE `patient_flow_visits` ADD COLUMN `reason` VARCHAR(255) NULL DEFAULT NULL COMMENT 'Reason for prescribing FX/E medicines' AFTER `is_active`");
            }
            if (!$this->db->field_exists('broad_area_series', 'patient_flow_visits')) {
                $this->db->query("ALTER TABLE `patient_flow_visits` ADD COLUMN `broad_area_series` VARCHAR(100) NULL DEFAULT NULL COMMENT 'Selected broad area series code' AFTER `reason`");
            }
        }

        // 10) Patient flow settings key-value table.
        if (!$this->db->table_exists('patient_flow_settings')) {
            $this->db->query("CREATE TABLE IF NOT EXISTS `patient_flow_settings` (`id` INT NOT NULL AUTO_INCREMENT, `setting_key` VARCHAR(80) NOT NULL, `setting_value` TEXT NULL, PRIMARY KEY (`id`), UNIQUE KEY `uniq_key` (`setting_key`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3");
        }

        // 11) Constitution master table (patient-flow only master).
        if (!$this->db->table_exists('patient_flow_constitutions')) {
            $this->db->query("CREATE TABLE IF NOT EXISTS `patient_flow_constitutions` (`id` INT NOT NULL AUTO_INCREMENT, `name` VARCHAR(150) NOT NULL, `code` VARCHAR(30) NULL DEFAULT NULL, `description` TEXT NULL, `is_active` TINYINT(1) NOT NULL DEFAULT 1, `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3");
        }

        // 12) Dynamic constitution lookups + master (client requirement).
        if (!$this->db->table_exists('constitution_body_build')) {
            $this->db->query("CREATE TABLE IF NOT EXISTS `constitution_body_build` (`id` INT NOT NULL AUTO_INCREMENT, `code` VARCHAR(10) NOT NULL, `name` VARCHAR(100) NOT NULL, `variant` TINYINT NOT NULL DEFAULT 1, `sort_order` INT NOT NULL DEFAULT 0, `is_active` TINYINT(1) NOT NULL DEFAULT 1, `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, PRIMARY KEY (`id`), UNIQUE KEY `uniq_code` (`code`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3");
        }
        if (!$this->db->table_exists('constitution_face_shape')) {
            $this->db->query("CREATE TABLE IF NOT EXISTS `constitution_face_shape` (`id` INT NOT NULL AUTO_INCREMENT, `code` VARCHAR(10) NOT NULL, `name` VARCHAR(100) NOT NULL, `sort_order` INT NOT NULL DEFAULT 0, `is_active` TINYINT(1) NOT NULL DEFAULT 1, `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, PRIMARY KEY (`id`), UNIQUE KEY `uniq_code` (`code`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3");
        }
        if (!$this->db->table_exists('constitution_nose_type')) {
            $this->db->query("CREATE TABLE IF NOT EXISTS `constitution_nose_type` (`id` INT NOT NULL AUTO_INCREMENT, `code` VARCHAR(10) NOT NULL, `name` VARCHAR(100) NOT NULL, `sort_order` INT NOT NULL DEFAULT 0, `is_active` TINYINT(1) NOT NULL DEFAULT 1, `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, PRIMARY KEY (`id`), UNIQUE KEY `uniq_code` (`code`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3");
        }
        if (!$this->db->table_exists('constitution_master')) {
            $this->db->query("CREATE TABLE IF NOT EXISTS `constitution_master` (`id` INT NOT NULL AUTO_INCREMENT, `code` VARCHAR(30) NOT NULL, `gender` ENUM('Male','Female') NOT NULL, `body_build_id` INT NOT NULL, `face_shape_id` INT NOT NULL, `nose_type_id` INT NOT NULL, `variant` TINYINT NOT NULL DEFAULT 1, `description` TEXT NULL, `is_active` TINYINT(1) NOT NULL DEFAULT 1, `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, PRIMARY KEY (`id`), UNIQUE KEY `uniq_code` (`code`), UNIQUE KEY `uq_combo` (`gender`,`body_build_id`,`face_shape_id`,`nose_type_id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3");
        }
        // 13) Programme + subcategory masters.
        if (!$this->db->table_exists('programme_master')) {
            $this->db->query("CREATE TABLE IF NOT EXISTS `programme_master` (`id` INT NOT NULL AUTO_INCREMENT, `code` VARCHAR(10) NOT NULL, `name` VARCHAR(150) NOT NULL, `sort_order` INT NOT NULL DEFAULT 0, `is_active` TINYINT(1) NOT NULL DEFAULT 1, `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, PRIMARY KEY (`id`), UNIQUE KEY `uniq_code` (`code`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3");
        }
        if (!$this->db->table_exists('programme_subcategory')) {
            $this->db->query("CREATE TABLE IF NOT EXISTS `programme_subcategory` (`id` INT NOT NULL AUTO_INCREMENT, `programme_id` INT NOT NULL, `code` VARCHAR(15) NOT NULL, `name` VARCHAR(150) NOT NULL, `sort_order` INT NOT NULL DEFAULT 0, `is_active` TINYINT(1) NOT NULL DEFAULT 1, `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, PRIMARY KEY (`id`), UNIQUE KEY `uniq_code` (`code`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3");
        }
        // 14) Stage master.
        if (!$this->db->table_exists('stage_master')) {
            $this->db->query("CREATE TABLE IF NOT EXISTS `stage_master` (`id` INT NOT NULL AUTO_INCREMENT, `code` VARCHAR(10) NOT NULL, `quarter` VARCHAR(5) NOT NULL, `stage` VARCHAR(5) NOT NULL, `duration_days` INT NOT NULL DEFAULT 30, `operational_rule` VARCHAR(255) NULL DEFAULT NULL, `sort_order` INT NOT NULL DEFAULT 0, `is_active` TINYINT(1) NOT NULL DEFAULT 1, `patient_enrolled` TINYINT(1) NOT NULL DEFAULT 0, `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, PRIMARY KEY (`id`), UNIQUE KEY `uniq_code` (`code`), UNIQUE KEY `uq_combo` (`quarter`,`stage`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3");
        }
        // Add patient_enrolled column to stage_master for existing installations
        if ($this->db->table_exists('stage_master') && !$this->db->field_exists('patient_enrolled', 'stage_master')) {
            $this->db->query("ALTER TABLE `stage_master` ADD COLUMN `patient_enrolled` TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Patient enrolment allowed at this level' AFTER `is_active`");
        }
        $this->seed_programme_stage();
        // 15) Broad area series master.
        if (!$this->db->table_exists('broad_area_series')) {
            $this->db->query("CREATE TABLE IF NOT EXISTS `broad_area_series` (`id` INT NOT NULL AUTO_INCREMENT, `code` VARCHAR(10) NOT NULL, `name` VARCHAR(150) NULL DEFAULT NULL, `pair_group` VARCHAR(10) NULL DEFAULT NULL, `sort_order` INT NOT NULL DEFAULT 0, `is_active` TINYINT(1) NOT NULL DEFAULT 1, `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, PRIMARY KEY (`id`), UNIQUE KEY `uniq_code` (`code`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3");
        }
        // 15b) Broad area rules master.
        if (!$this->db->table_exists('broad_area_rules')) {
            $this->db->query("CREATE TABLE IF NOT EXISTS `broad_area_rules` (`id` INT NOT NULL AUTO_INCREMENT, `concern_pattern` VARCHAR(255) NOT NULL, `applicability` VARCHAR(50) NOT NULL, `series_result` TEXT NULL DEFAULT NULL COMMENT 'JSON array of series codes', `sort_order` INT NOT NULL DEFAULT 0, `is_active` TINYINT(1) NOT NULL DEFAULT 1, `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3");
        }
        // 16) Medicine master (series discriminator F/FA/FB/FC/FD/FX/E).
        if (!$this->db->table_exists('medicine_master')) {
            $this->db->query("CREATE TABLE IF NOT EXISTS `medicine_master` (`id` INT NOT NULL AUTO_INCREMENT, `code` VARCHAR(10) NOT NULL, `medicine_identity` VARCHAR(200) NOT NULL, `pharmacy_id` INT NULL DEFAULT NULL, `series` VARCHAR(5) NOT NULL DEFAULT 'F', `gp_code` VARCHAR(10) NULL DEFAULT NULL, `indication` VARCHAR(200) NULL DEFAULT NULL, `purpose` VARCHAR(200) NULL DEFAULT NULL, `patient_dose` VARCHAR(50) NULL DEFAULT NULL, `route_method` VARCHAR(100) NULL DEFAULT NULL, `timing` VARCHAR(100) NULL DEFAULT NULL, `frequency` VARCHAR(100) NULL DEFAULT NULL, `duration_text` VARCHAR(100) NULL DEFAULT NULL, `dispensing_qty_ml` DECIMAL(8,2) NULL DEFAULT NULL, `container_vial` VARCHAR(50) NULL DEFAULT NULL, `stock_deduction_ml` DECIMAL(8,2) NULL DEFAULT NULL, `use_note` VARCHAR(255) NULL DEFAULT NULL, `control_note` VARCHAR(255) NULL DEFAULT NULL, `supply_status` VARCHAR(100) NULL DEFAULT NULL, `status` VARCHAR(10) NOT NULL DEFAULT 'Active', `sort_order` INT NOT NULL DEFAULT 0, `is_active` TINYINT(1) NOT NULL DEFAULT 1, `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, PRIMARY KEY (`id`), UNIQUE KEY `uniq_code` (`code`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3");
        }
        // Migration: link to pharmacy medicine (inherit identity from pharmacy module).
        if ($this->db->table_exists('medicine_master')
            && !$this->db->field_exists('pharmacy_id', 'medicine_master')) {
            $this->db->query("ALTER TABLE `medicine_master` ADD COLUMN `pharmacy_id` INT NULL DEFAULT NULL COMMENT '-> pharmacy.id (inherited pharmacy medicine)' AFTER `medicine_identity`");
        }
        // 17) Locked F-series mapping (series x stage).
        if (!$this->db->table_exists('constit_f_series_mapping')) {
            $this->db->query("CREATE TABLE IF NOT EXISTS `constit_f_series_mapping` (`id` INT NOT NULL AUTO_INCREMENT, `series_id` INT NOT NULL, `stage_id` INT NOT NULL, `medicine_id` INT NOT NULL, `status` VARCHAR(10) NOT NULL DEFAULT 'Locked', `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, PRIMARY KEY (`id`), UNIQUE KEY `uq_series_stage` (`series_id`,`stage_id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3");
        }
        // 18) FA/FB early mapping (pair x stage x session).
        if (!$this->db->table_exists('formulation_early_mapping')) {
            $this->db->query("CREATE TABLE IF NOT EXISTS `formulation_early_mapping` (`id` INT NOT NULL AUTO_INCREMENT, `pair_group` VARCHAR(10) NOT NULL, `stage_id` INT NOT NULL, `session` VARCHAR(10) NOT NULL DEFAULT 'Morning', `medicine_id` INT NOT NULL, `status` VARCHAR(10) NOT NULL DEFAULT 'Locked', `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, PRIMARY KEY (`id`), UNIQUE KEY `uq_triple` (`pair_group`,`stage_id`,`session`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3");
        }
        // 19) Universal FC/FD mapping (stage x session).
        if (!$this->db->table_exists('formulation_universal_mapping')) {
            $this->db->query("CREATE TABLE IF NOT EXISTS `formulation_universal_mapping` (`id` INT NOT NULL AUTO_INCREMENT, `stage_id` INT NOT NULL, `session` VARCHAR(10) NOT NULL DEFAULT 'Morning', `medicine_id` INT NOT NULL, `status` VARCHAR(10) NOT NULL DEFAULT 'Locked', `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, PRIMARY KEY (`id`), UNIQUE KEY `uq_stage_session` (`stage_id`,`session`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3");
        }

        // ============ Patient Flow Programme Fee - Step 1 Masters ============

        // 20) Programme group (HFT, FSC_SSC_CHC).
        if (!$this->db->table_exists('patient_flow_programme_group')) {
            $this->db->query("CREATE TABLE IF NOT EXISTS `patient_flow_programme_group` (`id` INT NOT NULL AUTO_INCREMENT, `code` VARCHAR(20) NOT NULL UNIQUE, `name` VARCHAR(100) NOT NULL, `coverage_model` ENUM('PARTNER_BASED','PER_PATIENT') NOT NULL, `is_active` TINYINT(1) NOT NULL DEFAULT 1, `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3");
        }

        // 21) Programme under a group.
        if (!$this->db->table_exists('patient_flow_programme')) {
            $this->db->query("CREATE TABLE IF NOT EXISTS `patient_flow_programme` (`id` INT NOT NULL AUTO_INCREMENT, `group_id` INT NOT NULL, `code` VARCHAR(20) NOT NULL UNIQUE, `name` VARCHAR(100) NOT NULL, `is_active` TINYINT(1) NOT NULL DEFAULT 1, `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, PRIMARY KEY (`id`), UNIQUE KEY `uniq_code` (`code`), FOREIGN KEY (`group_id`) REFERENCES `patient_flow_programme_group`(`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3");
        }

        // 22) Fee item master (consultation, vials, booklet), versioned by effective_from.
        if (!$this->db->table_exists('patient_flow_fee_item_master')) {
            $this->db->query("CREATE TABLE IF NOT EXISTS `patient_flow_fee_item_master` (`id` INT NOT NULL AUTO_INCREMENT, `fee_code` VARCHAR(20) NOT NULL, `item_name` VARCHAR(150) NOT NULL, `category` ENUM('CONSULTATION','VIAL','ITEM') NOT NULL, `base_amount` DECIMAL(12,2) NOT NULL, `basis` ENUM('PER_PATIENT','PER_CONSULTATION','PER_VIAL','PER_DUPLICATE') NOT NULL, `gst_type` ENUM('EXEMPT','EXCLUSIVE','INCLUSIVE','CONFIGURED') NOT NULL, `gst_rate` DECIMAL(5,2) NOT NULL DEFAULT 0, `min_duration_minutes` SMALLINT NULL, `extra_time_chargeable` TINYINT(1) NOT NULL DEFAULT 0, `requires_enrolled_patient` TINYINT(1) NOT NULL DEFAULT 0, `rule_note` VARCHAR(255) NULL, `effective_from` DATE NOT NULL, `effective_to` DATE NULL, `is_active` TINYINT(1) NOT NULL DEFAULT 1, PRIMARY KEY (`id`), UNIQUE KEY `uq_fee_version` (`fee_code`,`effective_from`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3");
        }

        // 23) Programme fee milestone (Q1_INITIAL, Q1_REMAINING, LATER_QUARTER, REVIEW_QUARTER).
        if (!$this->db->table_exists('patient_flow_programme_fee_milestone')) {
            $this->db->query("CREATE TABLE IF NOT EXISTS `patient_flow_programme_fee_milestone` (`id` INT NOT NULL AUTO_INCREMENT, `group_id` INT NOT NULL, `coverage` ENUM('ONE_PARTNER','BOTH_PARTNERS','PER_PATIENT') NOT NULL, `milestone_code` VARCHAR(30) NOT NULL, `duration_days` SMALLINT NOT NULL, `amount` DECIMAL(12,2) NOT NULL, `effective_from` DATE NOT NULL, `effective_to` DATE NULL, `is_active` TINYINT(1) NOT NULL DEFAULT 1, PRIMARY KEY (`id`), UNIQUE KEY `uq_milestone` (`group_id`,`coverage`,`milestone_code`,`effective_from`), FOREIGN KEY (`group_id`) REFERENCES `patient_flow_programme_group`(`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3");
        }

        // 24) Quarter-to-milestone mapping.
        if (!$this->db->table_exists('patient_flow_programme_quarter_milestone_map')) {
            $this->db->query("CREATE TABLE IF NOT EXISTS `patient_flow_programme_quarter_milestone_map` (`id` INT NOT NULL AUTO_INCREMENT, `group_id` INT NOT NULL, `quarter_no` TINYINT NOT NULL, `milestone_code` VARCHAR(30) NOT NULL, `sequence_no` TINYINT NOT NULL DEFAULT 1, PRIMARY KEY (`id`), UNIQUE KEY `uq_qmap` (`group_id`,`quarter_no`,`milestone_code`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3");
        }

        // 25) Stage allocation (Q1 split only) - links stage_master.id to amounts.
        if (!$this->db->table_exists('patient_flow_programme_fee_stage_allocation')) {
            $this->db->query("CREATE TABLE IF NOT EXISTS `patient_flow_programme_fee_stage_allocation` (`id` INT NOT NULL AUTO_INCREMENT, `group_id` INT NOT NULL, `coverage` ENUM('ONE_PARTNER','BOTH_PARTNERS','PER_PATIENT') NOT NULL, `stage_id` INT NOT NULL, `standard_amount` DECIMAL(12,2) NOT NULL, `effective_from` DATE NOT NULL, `effective_to` DATE NULL, PRIMARY KEY (`id`), UNIQUE KEY `uq_alloc` (`group_id`,`coverage`,`stage_id`,`effective_from`), FOREIGN KEY (`stage_id`) REFERENCES `stage_master`(`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3");
        }

        // 26) Fee assistance category (A/B/C discounts per group).
        if (!$this->db->table_exists('patient_flow_fee_assistance_category')) {
            $this->db->query("CREATE TABLE IF NOT EXISTS `patient_flow_fee_assistance_category` (`id` INT NOT NULL AUTO_INCREMENT, `group_id` INT NOT NULL, `category_code` CHAR(1) NOT NULL, `discount_percent` DECIMAL(5,2) NOT NULL, `validity_quarters` TINYINT NOT NULL DEFAULT 2, `payment_mode` ENUM('MILESTONE','STAGEWISE') NOT NULL DEFAULT 'STAGEWISE', `is_active` TINYINT(1) NOT NULL DEFAULT 1, PRIMARY KEY (`id`), UNIQUE KEY `uq_cat` (`group_id`,`category_code`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3");
        }

        // 27) Discount campaign (DRAFT → APPROVED workflow).
        if (!$this->db->table_exists('patient_flow_discount_campaign')) {
            $this->db->query("CREATE TABLE IF NOT EXISTS `patient_flow_discount_campaign` (`id` INT NOT NULL AUTO_INCREMENT, `campaign_code` VARCHAR(30) NOT NULL UNIQUE, `reason` VARCHAR(255) NOT NULL, `discount_percent` DECIMAL(5,2) NOT NULL, `start_date` DATE NOT NULL, `end_date` DATE NOT NULL, `benefit_quarters` TINYINT NOT NULL DEFAULT 2, `allow_stacking` TINYINT(1) NOT NULL DEFAULT 0, `payment_mode` ENUM('MILESTONE','STAGEWISE') NOT NULL DEFAULT 'MILESTONE', `status` ENUM('DRAFT','PENDING_APPROVAL','APPROVED','REJECTED') NOT NULL DEFAULT 'DRAFT', `created_by` INT NOT NULL, `created_at` DATETIME NOT NULL, `approved_by` INT NULL, `approved_at` DATETIME NULL, PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3");
        }

        // 28) Discount campaign scope (no rows = all programmes).
        if (!$this->db->table_exists('patient_flow_discount_campaign_scope')) {
            $this->db->query("CREATE TABLE IF NOT EXISTS `patient_flow_discount_campaign_scope` (`id` INT NOT NULL AUTO_INCREMENT, `campaign_id` INT NOT NULL, `group_id` INT NULL, `programme_id` INT NULL, PRIMARY KEY (`id`), KEY `idx_campaign` (`campaign_id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3");
        }

        // 29) Fee director (one of two Directors who can approve).
        if (!$this->db->table_exists('patient_flow_fee_director')) {
            $this->db->query("CREATE TABLE IF NOT EXISTS `patient_flow_fee_director` (`id` INT NOT NULL AUTO_INCREMENT, `staff_id` INT NOT NULL UNIQUE, `is_active` TINYINT(1) NOT NULL DEFAULT 1, PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3");
        }

        // ============ Patient Flow Programme Fee - Step 2 Transactions ============

        // 30) Patient programme enrollment (per patient).
        if (!$this->db->table_exists('patient_flow_patient_programme_enrollment')) {
            $this->db->query("CREATE TABLE IF NOT EXISTS `patient_flow_patient_programme_enrollment` (`id` INT NOT NULL AUTO_INCREMENT, `uid` VARCHAR(30) NOT NULL UNIQUE, `patient_id` INT NOT NULL, `partner_patient_id` INT NULL DEFAULT NULL, `programme_id` INT NOT NULL, `coverage` ENUM('ONE_PARTNER','BOTH_PARTNERS','PER_PATIENT') NOT NULL, `enrolled_on` DATE NOT NULL, `status` ENUM('ACTIVE','COMPLETED','CANCELLED') NOT NULL DEFAULT 'ACTIVE', `created_by` INT NOT NULL, `created_at` DATETIME NOT NULL, PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3");
        }

        // 31) Fee assistance request (junior requests, Director approves/rejects).
        if (!$this->db->table_exists('patient_flow_fee_assistance_request')) {
            $this->db->query("CREATE TABLE IF NOT EXISTS `patient_flow_fee_assistance_request` (`id` INT NOT NULL AUTO_INCREMENT, `enrollment_id` INT NOT NULL, `category_id` INT NOT NULL, `requested_by` INT NOT NULL, `requested_at` DATETIME NOT NULL, `reason` TEXT NULL, `status` ENUM('PENDING','APPROVED','REJECTED') NOT NULL DEFAULT 'PENDING', `decided_by` INT NULL DEFAULT NULL, `decided_at` DATETIME NULL DEFAULT NULL, `decision_note` VARCHAR(255) NULL DEFAULT NULL, PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3");
        }

        // 32) Enrollment benefit (immutable snapshot of applied benefit).
        if (!$this->db->table_exists('patient_flow_enrollment_benefit')) {
            $this->db->query("CREATE TABLE IF NOT EXISTS `patient_flow_enrollment_benefit` (`id` INT NOT NULL AUTO_INCREMENT, `enrollment_id` INT NOT NULL, `source` ENUM('ASSISTANCE','CAMPAIGN') NOT NULL, `source_id` INT NOT NULL, `discount_percent` DECIMAL(5,2) NOT NULL, `valid_from_quarter` TINYINT NOT NULL DEFAULT 1, `valid_to_quarter` TINYINT NOT NULL, `payment_mode` ENUM('MILESTONE','STAGEWISE') NOT NULL, `stagewise_approved_by` INT NULL DEFAULT NULL, `stagewise_approved_at` DATETIME NULL DEFAULT NULL, `candidates_json` TEXT NULL, `is_current` TINYINT(1) NOT NULL DEFAULT 1, `created_by` INT NOT NULL, `created_at` DATETIME NOT NULL, PRIMARY KEY (`id`), KEY `idx_enr` (`enrollment_id`,`is_current`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3");
        }

        // 33) Programme payment schedule (what patient must pay per milestone/stage).
        if (!$this->db->table_exists('patient_flow_programme_payment_schedule')) {
            $this->db->query("CREATE TABLE IF NOT EXISTS `patient_flow_programme_payment_schedule` (`id` INT NOT NULL AUTO_INCREMENT, `enrollment_id` INT NOT NULL, `quarter_no` TINYINT NOT NULL, `seq_no` TINYINT NOT NULL, `unit_type` ENUM('MILESTONE','STAGE') NOT NULL, `milestone_code` VARCHAR(30) NULL DEFAULT NULL, `stage_from_id` INT NOT NULL, `stage_to_id` INT NOT NULL, `standard_amount` DECIMAL(12,2) NOT NULL, `discount_percent` DECIMAL(5,2) NOT NULL DEFAULT 0, `discount_amount` DECIMAL(12,2) NOT NULL DEFAULT 0, `payable_amount` DECIMAL(12,2) NOT NULL, `benefit_id` INT NULL DEFAULT NULL, `status` ENUM('PENDING','PAID','CANCELLED') NOT NULL DEFAULT 'PENDING', `paid_at` DATETIME NULL DEFAULT NULL, `payment_ref` VARCHAR(60) NULL DEFAULT NULL, `generated_at` DATETIME NOT NULL, PRIMARY KEY (`id`), UNIQUE KEY `uq_unit` (`enrollment_id`,`quarter_no`,`seq_no`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3");
        }

        // 34) Fee invoice.
        if (!$this->db->table_exists('patient_flow_fee_invoice')) {
            $this->db->query("CREATE TABLE IF NOT EXISTS `patient_flow_fee_invoice` (`id` INT NOT NULL AUTO_INCREMENT, `invoice_no` VARCHAR(30) NOT NULL UNIQUE, `invoice_type` ENUM('CONSULTATION','PROGRAMME','ITEMS') NOT NULL, `patient_id` INT NOT NULL, `enrollment_id` INT NULL DEFAULT NULL, `invoice_date` DATETIME NOT NULL, `subtotal` DECIMAL(12,2) NOT NULL, `gst_total` DECIMAL(12,2) NOT NULL DEFAULT 0, `grand_total` DECIMAL(12,2) NOT NULL, `status` ENUM('PENDING','PAID','CANCELLED') NOT NULL DEFAULT 'PENDING', `created_by` INT NOT NULL, PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3");
        }

        // 35) Fee invoice line.
        if (!$this->db->table_exists('patient_flow_fee_invoice_line')) {
            $this->db->query("CREATE TABLE IF NOT EXISTS `patient_flow_fee_invoice_line` (`id` INT NOT NULL AUTO_INCREMENT, `invoice_id` INT NOT NULL, `line_type` ENUM('CONSULTATION','PROGRAMME','VIAL','ITEM') NOT NULL, `fee_code` VARCHAR(20) NULL DEFAULT NULL, `schedule_id` INT NULL DEFAULT NULL, `description` VARCHAR(255) NOT NULL, `qty` DECIMAL(10,2) NOT NULL DEFAULT 1, `unit_amount` DECIMAL(12,2) NOT NULL, `discount_amount` DECIMAL(12,2) NOT NULL DEFAULT 0, `taxable_amount` DECIMAL(12,2) NOT NULL, `gst_type` ENUM('EXEMPT','EXCLUSIVE','INCLUSIVE','CONFIGURED') NOT NULL, `gst_rate` DECIMAL(5,2) NOT NULL DEFAULT 0, `gst_amount` DECIMAL(12,2) NOT NULL DEFAULT 0, `line_total` DECIMAL(12,2) NOT NULL, PRIMARY KEY (`id`), KEY `idx_invoice` (`invoice_id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3");
        }

        // 36) Fee audit log (full audit trail for all masters and approvals).
        if (!$this->db->table_exists('patient_flow_fee_audit_log')) {
            $this->db->query("CREATE TABLE IF NOT EXISTS `patient_flow_fee_audit_log` (`id` BIGINT NOT NULL AUTO_INCREMENT, `entity` VARCHAR(40) NOT NULL, `entity_id` INT NOT NULL, `action` VARCHAR(30) NOT NULL, `old_json` MEDIUMTEXT NULL DEFAULT NULL, `new_json` MEDIUMTEXT NULL DEFAULT NULL, `user_id` INT NOT NULL, `created_at` DATETIME NOT NULL, PRIMARY KEY (`id`), KEY `idx_entity` (`entity`,`entity_id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3");
        }

        $this->seed_medicine_section();

    }

    public function seed_medicine_section()
    {
        if ($this->db->table_exists('broad_area_series')
            && $this->db->count_all('broad_area_series') == 0) {
            $series = array(
                array('LKO-1', 'LKO'), array('LKO-2', 'LKO'),
                array('SBM-1', 'SBM'), array('SBM-2', 'SBM'),
                array('WTB-1', 'WTB'), array('WTB-2', 'WTB'),
                array('FLKO-1', 'FLKO'), array('FLKO-2', 'FLKO'),
                array('FSBM-1', 'FSBM'), array('FSBM-2', 'FSBM'),
                array('FWTB-1', 'FWTB'), array('FWTB-2', 'FWTB'),
                array('FUOC-1', 'FUOC'), array('FUOC-2', 'FUOC'),
            );
            $ord = 0;
            foreach ($series as $s) {
                $ord++;
                $this->db->insert('broad_area_series', array(
                    'code' => $s[0], 'name' => $s[0],
                    'pair_group' => $s[1], 'sort_order' => $ord,
                ));
            }
        }
        if ($this->db->table_exists('medicine_master')
            && $this->db->count_all('medicine_master') == 0) {
            $f = array(
                'F001' => 'Belladonna 30C', 'F002' => 'Natrum sulphuricum 30C',
                'F003' => 'Belladonna 200C', 'F004' => 'Natrum sulphuricum 200C',
                'F005' => 'Medorrhinum 30C', 'F006' => 'Thuja occidentalis 30C',
                'F007' => 'China officinalis 30C', 'F008' => 'Magnesia phosphorica 30C',
                'F009' => 'Rhus toxicodendron 30C', 'F010' => 'China officinalis 200C',
                'F011' => 'Magnesia phosphorica 200C', 'F012' => 'Rhus toxicodendron 200C',
                'F013' => 'Nux vomica 30C', 'F014' => 'Chelidonium majus 30C',
                'F015' => 'Alumina 30C', 'F016' => 'Nux vomica 200C',
                'F017' => 'Chelidonium majus 200C', 'F018' => 'Alumina 200C',
                'F019' => 'Podophyllum peltatum 30C', 'F020' => 'Calcarea phosphorica 30C',
                'F021' => 'Arnica montana 30C', 'F022' => 'Podophyllum peltatum 200C',
                'F023' => 'Calcarea phosphorica 200C', 'F024' => 'Arnica montana 200C',
                'F025' => 'Kreosotum 30C', 'F026' => 'Sulphur 30C',
                'F027' => 'Calcarea carbonica 30C', 'F028' => 'Lycopodium clavatum 30C',
                'F029' => 'Medorrhinum 200C', 'F030' => 'Thuja occidentalis 200C',
                'F031' => 'Kreosotum 200C', 'F032' => 'Petroleum 30C',
                'F033' => 'Ferrum metallicum 30C', 'F034' => 'Ferrum metallicum 200C',
                'F035' => 'Cina maritima 30C', 'F036' => 'Ferrum phosphoricum 200C',
                'F037' => 'Cannabis indica 30C', 'F038' => 'Ambra grisea 30C',
                'F039' => 'Anacardium occidentale 30C', 'F040' => 'Hekla lava 30C',
                'F041' => 'Caulophyllum 30C', 'F042' => 'Iodium 30C',
                'F043' => 'Pulsatilla 30C', 'F044' => 'Gelsemium 30C',
                'F045' => 'Kali carbonicum 30C', 'F046' => 'Ammonium muriaticum 30C',
                'F047' => 'Manganum Aceticum 200C',
            );
            foreach ($f as $code => $ident) {
                $this->db->insert('medicine_master', array(
                    'code' => $code, 'medicine_identity' => $ident, 'series' => 'F',
                    'patient_dose' => '5 drops', 'route_method' => 'Directly on tongue',
                    'timing' => 'Morning', 'frequency' => 'Once only',
                    'duration_text' => 'One day only', 'dispensing_qty_ml' => 3,
                    'container_vial' => '5 ml vial', 'stock_deduction_ml' => 3,
                    'status' => 'Locked',
                ));
            }
            // FA/FB/FC/FD daily identities (full detail for exemplars, code rows for rest).
            $daily = array(
                array('FA001', 'FA', 'Chelidonium Majus 12C', '5 drops', 'Directly on tongue', 'Morning; empty stomach', 'Once daily', '15', 6, '10 ml vial', 6, 'Q1S1/Q1S2 morning'),
                array('FB001', 'FB', 'Sabal Seru 12C', '10 drops', 'Mixed with water', 'Evening', 'Once daily', '15', 8, '10 ml vial', 8, 'Q1S1/Q1S2 evening'),
                array('FC001', 'FC', 'Carduus Mar Q', '5 drops', 'Directly on tongue', 'Morning; empty stomach', 'Once daily', '30', 10, '10 ml vial', 10, 'Q1S3 onward morning'),
                array('FD001', 'FD', 'Passiflora Inc Q', '5 drops', 'Mixed with water', 'Evening', 'Once daily', '30', 10, '10 ml vial', 10, 'Q1S3 onward evening'),
            );
            foreach ($daily as $d) {
                $this->db->insert('medicine_master', array(
                    'code' => $d[0], 'medicine_identity' => $d[2], 'series' => $d[1],
                    'patient_dose' => $d[3], 'route_method' => $d[4], 'timing' => $d[5],
                    'frequency' => $d[6], 'duration_text' => $d[7], 'dispensing_qty_ml' => $d[8],
                    'container_vial' => $d[9], 'stock_deduction_ml' => $d[10],
                    'use_note' => $d[11], 'status' => 'Locked',
                ));
            }
            $rest = array(
                array('FA', 2, 14), array('FB', 2, 7),
                array('FC', 2, 29), array('FD', 2, 29),
            );
            foreach ($rest as $r) {
                for ($i = $r[1]; $i <= $r[2]; $i++) {
                    $code = $r[0] . str_pad($i, 3, '0', STR_PAD_LEFT);
                    $this->db->insert('medicine_master', array(
                        'code' => $code, 'medicine_identity' => $code,
                        'series' => $r[0], 'status' => 'Locked',
                    ));
                }
            }
            // FX + E clinical masters.
            $fxe = array(
                array('FX001', 'FX', 'Viburnum opulus Q', 'GP20', 'Fibroid', '5 drops', 'Cup of hot water', 'Morning', 'Once daily', 'Stage-linked: 15 or 30 days', null, null, null, 'See FX & E Supply', 'V4/V5; second-Senior authorization', null),
                array('FX002', 'FX', 'Salix nigra Q', 'GP21', 'Ovarian Cyst', '5 drops', 'Cup of hot water', 'Morning', 'Once daily', 'Stage-linked: 15 or 30 days', null, null, null, 'See FX & E Supply', 'V4/V5; second-Senior authorization', null),
                array('E001', 'E', 'Eupatorium perfoliatum Q', null, null, '3 drops', 'Directly on tongue', 'At symptom onset', 'Repeat at 5, 10, 20 and 30 minutes, then 1 hour and 2 hours as symptoms improve', null, 18, '20 ml vial', 18, null, null, 'Included quarterly; extra chargeable'),
                array('E007', 'E', 'Balsamodendron mukul 3C', null, null, '10 drops', 'One cup of hot water', 'As required', 'As required', null, 18, '20 ml vial', 18, null, null, 'Always chargeable'),
            );
            foreach ($fxe as $m) {
                $this->db->insert('medicine_master', array(
                    'code' => $m[0], 'medicine_identity' => $m[2], 'series' => $m[1],
                    'gp_code' => $m[3], 'indication' => $m[4], 'purpose' => null,
                    'patient_dose' => $m[5], 'route_method' => $m[6], 'timing' => $m[7],
                    'frequency' => $m[8], 'duration_text' => $m[9],
                    'dispensing_qty_ml' => $m[10], 'container_vial' => $m[11],
                    'stock_deduction_ml' => $m[12], 'use_note' => $m[13],
                    'control_note' => $m[14], 'supply_status' => $m[15],
                    'status' => 'Locked',
                ));
            }
            // FX purpose + E purpose fix.
            $this->db->where('code', 'FX001')->update('medicine_master', array('purpose' => null));
            $this->db->where('code', 'E001')->update('medicine_master', array('purpose' => 'Acute general pain'));
            $this->db->where('code', 'E007')->update('medicine_master', array('purpose' => 'Second-line acute pain'));
        }
        // F-series mapping seed (series x stage -> F code).
        if ($this->db->table_exists('constit_f_series_mapping')
            && $this->db->count_all('constit_f_series_mapping') == 0) {
            $smap = array();
            foreach ($this->db->get('broad_area_series')->result() as $r) {
                $smap[$r->code] = (int) $r->id;
            }
            $stmap = array();
            foreach ($this->db->get('stage_master')->result() as $r) {
                $stmap[$r->code] = (int) $r->id;
            }
            $mmap = array();
            foreach ($this->db->get('medicine_master')->result() as $r) {
                $mmap[$r->code] = (int) $r->id;
            }
            $rows = array();
            foreach (array('a', 'b', 'c', 'd', 'e', 'f', 'g') as $part) {
                $file = APPPATH . 'models/f_series_seed_' . $part . '.php';
                if (is_file($file)) {
                    $chunk = include $file;
                    if (is_array($chunk)) {
                        $rows = array_merge($rows, $chunk);
                    }
                }
            }
            foreach ($rows as $rw) {
                if (!isset($smap[$rw[0]]) || !isset($stmap[$rw[1]]) || !isset($mmap[$rw[2]])) {
                    continue;
                }
                $this->db->insert('constit_f_series_mapping', array(
                    'series_id' => $smap[$rw[0]],
                    'stage_id' => $stmap[$rw[1]],
                    'medicine_id' => $mmap[$rw[2]],
                    'status' => 'Locked',
                ));
            }
        }
        // Early FA/FB mapping seed.
        if ($this->db->table_exists('formulation_early_mapping')
            && $this->db->count_all('formulation_early_mapping') == 0) {
            $stmap = array();
            foreach ($this->db->get('stage_master')->result() as $r) {
                $stmap[$r->code] = (int) $r->id;
            }
            $mmap = array();
            foreach ($this->db->get('medicine_master')->result() as $r) {
                $mmap[$r->code] = (int) $r->id;
            }
            $early = array(
                array('LKO', 'Q1S1', 'Morning', 'FA001'), array('LKO', 'Q1S2', 'Morning', 'FA002'),
                array('LKO', 'Q1S1', 'Evening', 'FB001'), array('LKO', 'Q1S2', 'Evening', 'FB001'),
                array('SBM', 'Q1S1', 'Morning', 'FA003'), array('SBM', 'Q1S2', 'Morning', 'FA004'),
                array('SBM', 'Q1S1', 'Evening', 'FB002'), array('SBM', 'Q1S2', 'Evening', 'FB002'),
                array('WTB', 'Q1S1', 'Morning', 'FA005'), array('WTB', 'Q1S2', 'Morning', 'FA006'),
                array('WTB', 'Q1S1', 'Evening', 'FB003'), array('WTB', 'Q1S2', 'Evening', 'FB003'),
                array('FLKO', 'Q1S1', 'Morning', 'FA007'), array('FLKO', 'Q1S2', 'Morning', 'FA008'),
                array('FLKO', 'Q1S1', 'Evening', 'FB004'), array('FLKO', 'Q1S2', 'Evening', 'FB004'),
                array('FSBM', 'Q1S1', 'Morning', 'FA009'), array('FSBM', 'Q1S2', 'Morning', 'FA010'),
                array('FSBM', 'Q1S1', 'Evening', 'FB005'), array('FSBM', 'Q1S2', 'Evening', 'FB005'),
                array('FWTB', 'Q1S1', 'Morning', 'FA011'), array('FWTB', 'Q1S2', 'Morning', 'FA012'),
                array('FWTB', 'Q1S1', 'Evening', 'FB006'), array('FWTB', 'Q1S2', 'Evening', 'FB006'),
                array('FUOC', 'Q1S1', 'Morning', 'FA013'), array('FUOC', 'Q1S2', 'Morning', 'FA014'),
                array('FUOC', 'Q1S1', 'Evening', 'FB007'), array('FUOC', 'Q1S2', 'Evening', 'FB007'),
            );
            foreach ($early as $e) {
                if (!isset($stmap[$e[1]]) || !isset($mmap[$e[3]])) {
                    continue;
                }
                $this->db->insert('formulation_early_mapping', array(
                    'pair_group' => $e[0], 'stage_id' => $stmap[$e[1]],
                    'session' => $e[2], 'medicine_id' => $mmap[$e[3]],
                    'status' => 'Locked',
                ));
            }
        }
        // Universal FC/FD mapping seed (Q1S3..Q10S3).
        if ($this->db->table_exists('formulation_universal_mapping')
            && $this->db->count_all('formulation_universal_mapping') == 0) {
            $stmap = array();
            foreach ($this->db->get('stage_master')->result() as $r) {
                $stmap[$r->code] = (int) $r->id;
            }
            $mmap = array();
            foreach ($this->db->get('medicine_master')->result() as $r) {
                $mmap[$r->code] = (int) $r->id;
            }
            $order = array('Q1S3', 'Q1S4', 'Q2S1', 'Q2S2', 'Q2S3', 'Q3S1', 'Q3S2', 'Q3S3', 'Q4S1', 'Q4S2', 'Q4S3', 'Q5S1', 'Q5S2', 'Q5S3', 'Q6S1', 'Q6S2', 'Q6S3', 'Q7S1', 'Q7S2', 'Q7S3', 'Q8S1', 'Q8S2', 'Q8S3', 'Q9S1', 'Q9S2', 'Q9S3', 'Q10S1', 'Q10S2', 'Q10S3');
            $n = 0;
            foreach ($order as $stcode) {
                $n++;
                $fc = 'FC' . str_pad($n, 3, '0', STR_PAD_LEFT);
                $fd = 'FD' . str_pad($n, 3, '0', STR_PAD_LEFT);
                if (!isset($stmap[$stcode]) || !isset($mmap[$fc]) || !isset($mmap[$fd])) {
                    continue;
                }
                $this->db->insert('formulation_universal_mapping', array(
                    'stage_id' => $stmap[$stcode], 'session' => 'Morning',
                    'medicine_id' => $mmap[$fc], 'status' => 'Locked',
                ));
                $this->db->insert('formulation_universal_mapping', array(
                    'stage_id' => $stmap[$stcode], 'session' => 'Evening',
                    'medicine_id' => $mmap[$fd], 'status' => 'Locked',
                ));
            }
        }
        // Broad area rules seed.
        if ($this->db->table_exists('broad_area_rules')
            && $this->db->count_all('broad_area_rules') == 0) {
            $rules = array(
                array('Internal organs, liver, kidney, urinary, digestive, metabolic', 'Male/general', '["LKO-1","LKO-2"]'),
                array('Internal organs, liver, kidney, urinary, digestive, metabolic', 'Female', '["FLKO-1","FLKO-2"]'),
                array('Spine, bone, joint, muscle, tendon, ligament', 'Male/general', '["SBM-1","SBM-2"]'),
                array('Spine, bone, joint, muscle, tendon, ligament', 'Female', '["FSBM-1","FSBM-2"]'),
                array('Weak tissue, bonding, connective tissue, repair, laxity', 'Male/general', '["WTB-1","WTB-2"]'),
                array('Weak tissue, bonding, connective tissue, repair, laxity', 'Female', '["FWTB-1","FWTB-2"]'),
                array('Uterus, ovary or cervix', 'Female only', '["FUOC-1","FUOC-2"]'),
                array('Facial or unclear classification', 'Any', '["Review Required"]'),
            );
            foreach ($rules as $i => $r) {
                $this->db->insert('broad_area_rules', array(
                    'concern_pattern' => $r[0],
                    'applicability' => $r[1],
                    'series_result' => $r[2],
                    'sort_order' => ($i + 1),
                ));
            }
        }

        // 20) Patient flow stage assignment (stage from UID history per visit).
        if (!$this->db->table_exists('patient_flow_stage_assignment')) {
            $this->db->query("CREATE TABLE IF NOT EXISTS `patient_flow_stage_assignment` (
                `id` INT NOT NULL AUTO_INCREMENT,
                `visit_id` INT NOT NULL,
                `stage_id` INT NOT NULL,
                `stage_code` VARCHAR(10) NULL DEFAULT NULL,
                `is_current` TINYINT(1) NOT NULL DEFAULT 1,
                `assigned_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`),
                UNIQUE KEY `uq_visit_current` (`visit_id`, `is_current`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3");
        }

        // 21) Patient flow prescription (medicine selection + approval workflow).
        if (!$this->db->table_exists('patient_flow_prescription')) {
            $this->db->query("CREATE TABLE IF NOT EXISTS `patient_flow_prescription` (
                `id` INT NOT NULL AUTO_INCREMENT,
                `visit_id` INT NOT NULL,
                `medicine_id` INT NOT NULL,
                `series` VARCHAR(5) NULL DEFAULT NULL,
                `variant` TINYINT(1) NULL DEFAULT NULL,
                `prescribed_by` INT NOT NULL,
                `prescribed_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `clinical_approval` ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending',
                `clinical_approved_by` INT NULL DEFAULT NULL,
                `clinical_approved_at` DATETIME NULL DEFAULT NULL,
                `payment_confirmed` TINYINT(1) NOT NULL DEFAULT 0,
                `fx_second_senior_approval` TINYINT(1) NOT NULL DEFAULT 0,
                `fx_approved_by` INT NULL DEFAULT NULL,
                `name_hidden` TINYINT(1) NOT NULL DEFAULT 1,
                `is_active` TINYINT(1) NOT NULL DEFAULT 1,
                `stock_deducted` TINYINT(1) NOT NULL DEFAULT 0,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3");
        }
        // Add stock_deducted column if missing (existing installations)
        if ($this->db->table_exists('patient_flow_prescription') && !$this->db->field_exists('stock_deducted', 'patient_flow_prescription')) {
            $this->db->query("ALTER TABLE `patient_flow_prescription` ADD COLUMN `stock_deducted` TINYINT(1) NOT NULL DEFAULT 0 AFTER `is_active`");
        }
        // reason column moved to patient_flow_visits table; drop if still present on prescription table
        $fields = $this->db->list_fields('patient_flow_prescription');
        if (in_array('reason', $fields)) {
            $this->db->query("ALTER TABLE `patient_flow_prescription` DROP COLUMN `reason`");
        }
        // Seed Step 1 programme fee masters (patient_flow_programme_*).
        $this->seed_programme_fee_section();
    }

    public function seed_programme_fee_section()
    {
        // 20) Programme groups.
        if ($this->db->table_exists('patient_flow_programme_group')
            && $this->db->count_all('patient_flow_programme_group') == 0) {
            $this->db->insert('patient_flow_programme_group', array(
                'code' => 'HFT', 'name' => 'Homeopathic Fertility Therapy',
                'coverage_model' => 'PARTNER_BASED', 'is_active' => 1,
            ));
            $this->db->insert('patient_flow_programme_group', array(
                'code' => 'FSC_SSC_CHC', 'name' => 'Facial Skin Care / Spine & Spondylosis / Chronic Care',
                'coverage_model' => 'PER_PATIENT', 'is_active' => 1,
            ));
        }
        // 21) Programmes under groups.
        if ($this->db->table_exists('patient_flow_programme')
            && $this->db->count_all('patient_flow_programme') == 0) {
            $gmap = array();
            foreach ($this->db->get('patient_flow_programme_group')->result() as $g) {
                $gmap[$g->code] = (int) $g->id;
            }
            $this->db->insert('patient_flow_programme', array(
                'group_id' => $gmap['HFT'], 'code' => 'HFT', 'name' => 'Homeopathic Fertility Therapy',
                'is_active' => 1,
            ));
            $this->db->insert('patient_flow_programme', array(
                'group_id' => $gmap['FSC_SSC_CHC'], 'code' => 'FSC', 'name' => 'Facial Skin Care',
                'is_active' => 1,
            ));
            $this->db->insert('patient_flow_programme', array(
                'group_id' => $gmap['FSC_SSC_CHC'], 'code' => 'SSC', 'name' => 'Spine & Spondylosis Care',
                'is_active' => 1,
            ));
            $this->db->insert('patient_flow_programme', array(
                'group_id' => $gmap['FSC_SSC_CHC'], 'code' => 'CHC', 'name' => 'Chronic Care',
                'is_active' => 1,
            ));
        }
        // 22) Fee item master (consultation, vials, duplicate booklet).
        if ($this->db->table_exists('patient_flow_fee_item_master')
            && $this->db->count_all('patient_flow_fee_item_master') == 0) {
            $items = array(
                array('CON-IC-N', 'In-clinic first consultation', 'CONSULTATION', 1000, 'PER_PATIENT', 'EXEMPT', 0, NULL, 0, 0, NULL),
                array('CON-IC-FU', 'In-clinic follow-up', 'CONSULTATION', 500, 'PER_CONSULTATION', 'EXEMPT', 0, NULL, 0, 1, NULL),
                array('CON-REMOTE', 'Remote voice/video consultation', 'CONSULTATION', 1000, 'PER_CONSULTATION', 'EXEMPT', 0, 5, 0, 0, NULL),
                array('VIAL-5', '5 ml vial (3 ml fill)', 'VIAL', 200, 'PER_VIAL', 'EXCLUSIVE', 12, NULL, 0, 0, NULL),
                array('VIAL-20', '20 ml vial (8/12/18 ml fill)', 'VIAL', 500, 'PER_VIAL', 'EXCLUSIVE', 12, NULL, 0, 0, NULL),
                array('VIAL-E', 'Chargeable E-series 20 ml vial', 'VIAL', 540, 'PER_VIAL', 'INCLUSIVE', 12, NULL, 0, 0, NULL),
                array('PBHP-DUP', 'Duplicate PBHP booklet', 'ITEM', 1100, 'PER_DUPLICATE', 'CONFIGURED', 0, NULL, 0, 0, NULL),
            );
            foreach ($items as $it) {
                $this->db->insert('patient_flow_fee_item_master', array(
                    'fee_code' => $it[0], 'item_name' => $it[1], 'category' => $it[2],
                    'base_amount' => $it[3], 'basis' => $it[4], 'gst_type' => $it[5],
                    'gst_rate' => $it[6], 'min_duration_minutes' => $it[7],
                    'extra_time_chargeable' => $it[8], 'requires_enrolled_patient' => $it[9],
                    'effective_from' => '2026-01-01', 'effective_to' => NULL, 'is_active' => 1,
                ));
            }
        }
        // 23) Programme fee milestone amounts per group/coverage.
        if ($this->db->table_exists('patient_flow_programme_fee_milestone')
            && $this->db->count_all('patient_flow_programme_fee_milestone') == 0) {
            $gmap = array();
            foreach ($this->db->get('patient_flow_programme_group')->result() as $g) {
                $gmap[$g->code] = (int) $g->id;
            }
            $miles = array(
                array('HFT', 'ONE_PARTNER', 'Q1_INITIAL', 15, 6000),   array('HFT', 'BOTH_PARTNERS', 'Q1_INITIAL', 15, 10000),
                array('HFT', 'ONE_PARTNER', 'Q1_REMAINING', 75, 10000), array('HFT', 'BOTH_PARTNERS', 'Q1_REMAINING', 75, 15000),
                array('HFT', 'ONE_PARTNER', 'LATER_QUARTER', 90, 10000), array('HFT', 'BOTH_PARTNERS', 'LATER_QUARTER', 90, 16000),
                array('HFT', 'ONE_PARTNER', 'REVIEW_QUARTER', 90, 14000), array('HFT', 'BOTH_PARTNERS', 'REVIEW_QUARTER', 90, 21000),
                array('FSC_SSC_CHC', 'PER_PATIENT', 'Q1_INITIAL', 15, 5000),
                array('FSC_SSC_CHC', 'PER_PATIENT', 'Q1_REMAINING', 75, 10000),
                array('FSC_SSC_CHC', 'PER_PATIENT', 'LATER_QUARTER', 90, 8000),
                array('FSC_SSC_CHC', 'PER_PATIENT', 'REVIEW_QUARTER', 90, 12000),
            );
            foreach ($miles as $m) {
                if (!isset($gmap[$m[0]])) { continue; }
                $this->db->insert('patient_flow_programme_fee_milestone', array(
                    'group_id' => $gmap[$m[0]], 'coverage' => $m[1], 'milestone_code' => $m[2],
                    'duration_days' => $m[3], 'amount' => $m[4],
                    'effective_from' => '2026-01-01', 'effective_to' => NULL, 'is_active' => 1,
                ));
            }
        }
        // 24) Stage allocation (Q1 split only).
        if ($this->db->table_exists('patient_flow_programme_fee_stage_allocation')
            && $this->db->count_all('patient_flow_programme_fee_stage_allocation') == 0) {
            $gmap = array();
            foreach ($this->db->get('patient_flow_programme_group')->result() as $g) {
                $gmap[$g->code] = (int) $g->id;
            }
            $stmap = array();
            foreach ($this->db->get('stage_master')->result() as $r) {
                $stmap[$r->code] = (int) $r->id;
            }
            $allocs = array(
                array('HFT', 'ONE_PARTNER', 'Q1S1', 6000), array('HFT', 'ONE_PARTNER', 'Q1S2', 2000),
                array('HFT', 'ONE_PARTNER', 'Q1S3', 4000), array('HFT', 'ONE_PARTNER', 'Q1S4', 4000),
                array('HFT', 'BOTH_PARTNERS', 'Q1S1', 10000), array('HFT', 'BOTH_PARTNERS', 'Q1S2', 3000),
                array('HFT', 'BOTH_PARTNERS', 'Q1S3', 6000), array('HFT', 'BOTH_PARTNERS', 'Q1S4', 6000),
                array('FSC_SSC_CHC', 'PER_PATIENT', 'Q1S1', 5000), array('FSC_SSC_CHC', 'PER_PATIENT', 'Q1S2', 2000),
                array('FSC_SSC_CHC', 'PER_PATIENT', 'Q1S3', 4000), array('FSC_SSC_CHC', 'PER_PATIENT', 'Q1S4', 4000),
            );
            foreach ($allocs as $a) {
                if (!isset($gmap[$a[0]]) || !isset($stmap[$a[2]])) { continue; }
                $this->db->insert('patient_flow_programme_fee_stage_allocation', array(
                    'group_id' => $gmap[$a[0]], 'coverage' => $a[1], 'stage_id' => $stmap[$a[2]],
                    'standard_amount' => $a[3],
                    'effective_from' => '2026-01-01', 'effective_to' => NULL,
                ));
            }
        }
        // 25) Fee assistance categories (A/B/C discounts per group).
        if ($this->db->table_exists('patient_flow_fee_assistance_category')
            && $this->db->count_all('patient_flow_fee_assistance_category') == 0) {
            $gmap = array();
            foreach ($this->db->get('patient_flow_programme_group')->result() as $g) {
                $gmap[$g->code] = (int) $g->id;
            }
            $cats = array(
                array('HFT', 'A', 40), array('HFT', 'B', 60), array('HFT', 'C', 70),
                array('FSC_SSC_CHC', 'A', 20), array('FSC_SSC_CHC', 'B', 40), array('FSC_SSC_CHC', 'C', 50),
            );
            foreach ($cats as $c) {
                if (!isset($gmap[$c[0]])) { continue; }
                $this->db->insert('patient_flow_fee_assistance_category', array(
                    'group_id' => $gmap[$c[0]], 'category_code' => $c[1],
                    'discount_percent' => $c[2], 'validity_quarters' => 2,
                    'payment_mode' => 'STAGEWISE', 'is_active' => 1,
                ));
            }
        }
        // 26) Discount campaign (inaugural, APPROVED).
        if ($this->db->table_exists('patient_flow_discount_campaign')
            && $this->db->count_all('patient_flow_discount_campaign') == 0) {
            $this->db->insert('patient_flow_discount_campaign', array(
                'campaign_code' => 'INB-OCT26', 'reason' => 'Inaugural Discount',
                'discount_percent' => 20, 'start_date' => '2026-10-01', 'end_date' => '2026-10-31',
                'benefit_quarters' => 2, 'allow_stacking' => 0,
                'payment_mode' => 'MILESTONE', 'status' => 'APPROVED',
                'created_by' => 1, 'created_at' => date('Y-m-d H:i:s'),
            ));
        }
        // 27) Fee directors (placeholder - client to confirm actual staff IDs).
        if ($this->db->table_exists('patient_flow_fee_director')
            && $this->db->count_all('patient_flow_fee_director') == 0) {
            // No rows inserted by default; client must add actual director staff IDs.
        }
    }

    public function seed_programme_stage()
    {
        if (!$this->db->table_exists('programme_master')) {
            return;
        }
        if ($this->db->count_all('programme_master') == 0) {
            $progs = array(
                array('code' => 'HFT', 'name' => 'Homeopathic Fertility Therapy'),
                array('code' => 'FSC', 'name' => 'Facial Skin Care'),
                array('code' => 'SSC', 'name' => 'Spine & Spondylosis Care'),
                array('code' => 'CHC', 'name' => 'Chronic Care'),
            );
            foreach ($progs as $pr) {
                $this->db->insert('programme_master', $pr);
            }
        }
        $pmap = array();
        foreach ($this->db->get('programme_master')->result() as $r) {
            $pmap[$r->code] = (int) $r->id;
        }
        if ($this->db->table_exists('programme_subcategory')
            && $this->db->count_all('programme_subcategory') == 0) {
            $subs = array(
                array('HFT', 'HFT-MFC', 'Male Fertility Care'),
                array('HFT', 'HFT-FFC', 'Female Fertility Care'),
                array('FSC', 'FSC-ACN', 'Acne Care'),
                array('FSC', 'FSC-DC', 'Dark Circle Care'),
                array('FSC', 'FSC-OFC', 'Other Facial Concerns'),
                array('SSC', 'SSC-SPC', 'Spondylosis Care'),
                array('SSC', 'SSC-SCI', 'Sciatica Care'),
                array('SSC', 'SSC-ARC', 'Artho Care'),
                array('SSC', 'SSC-BMC', 'Other Bone & Muscle Care'),
                array('CHC', 'CHC-THY', 'Thyroid Care'),
                array('CHC', 'CHC-KSC', 'Kidney Stone Care'),
                array('CHC', 'CHC-PCO', 'PCOD/PCOS Care'),
                array('CHC', 'CHC-OMC', 'Other Menstrual Care'),
                array('CHC', 'CHC-FLC', 'Fatty Liver Care'),
                array('CHC', 'CHC-OSC', 'Other Selected Chronic Care'),
            );
            foreach ($subs as $s) {
                if (!isset($pmap[$s[0]])) {
                    continue;
                }
                $this->db->insert('programme_subcategory', array(
                    'programme_id' => $pmap[$s[0]],
                    'code' => $s[1],
                    'name' => $s[2],
                ));
            }
        }
        if ($this->db->table_exists('stage_master')
            && $this->db->count_all('stage_master') == 0) {
            $rows = array(
                array('Q1', 'S1', 15, 'First visit'),
                array('Q1', 'S2', 15, 'Second visit; Q1S2-S4 may be approved together'),
                array('Q1', 'S3', 30, 'Milestone schedule'),
                array('Q1', 'S4', 30, 'Milestone schedule'),
            );
            for ($q = 2; $q <= 10; $q++) {
                foreach (array('S1', 'S2', 'S3') as $st) {
                    $rows[] = array('Q' . $q, $st, 30, 'Milestone schedule');
                }
            }
            $ord = 0;
            foreach ($rows as $rw) {
                $ord++;
                $this->db->insert('stage_master', array(
                    'code' => $rw[0] . $rw[1],
                    'quarter' => $rw[0],
                    'stage' => $rw[1],
                    'duration_days' => $rw[2],
                    'operational_rule' => $rw[3],
                    'sort_order' => $ord,
                ));
            }
        }
    }

    /**
     * Dashboard widget stats for the patient flow: active visits, pending
     * payments, live queue tokens and visits completed today.
     */
    public function get_dashboard_stats()
    {
        $stats = array(
            "active"                 => 0,
            "pending_payment_count"  => 0,
            "pending_payment_amount" => 0,
            "queue_active"           => 0,
            "completed_today"        => 0,
        );
        if (!$this->db->table_exists("patient_flow_visits")) {
            return $stats;
        }
        $row = $this->db->select("COUNT(*) AS c")->from("patient_flow_visits")->where("status", "active")->get()->row();
        $stats["active"] = $row ? (int) $row->c : 0;

        $row = $this->db->select("COUNT(*) AS c, COALESCE(SUM(amount), 0) AS s")
            ->from("patient_flow_visits")
            ->where("status", "active")
            ->where("payment_status", "pending")
            ->get()->row();
        $stats["pending_payment_count"]  = $row ? (int) $row->c : 0;
        $stats["pending_payment_amount"] = $row ? (float) $row->s : 0.0;
        $this->db->select("COUNT(*) AS c");
        $this->db->from("patient_flow_visits");
        $this->db->where("status", "active");
        $this->db->where_in("token_status", array("ACTIVE", "CALLED"));
        $row = $this->db->get()->row();
        $stats["queue_active"] = $row ? (int) $row->c : 0;

        $row = $this->db->select("COUNT(*) AS c")
            ->from("patient_flow_visits")
            ->where("status", "completed")
            ->where("DATE(completed_at) =", date("Y-m-d"))
            ->get()->row();
        $stats["completed_today"] = $row ? (int) $row->c : 0;

        return $stats;
    }

    public function get_first_level($phase = 'P1')
    {
        $this->db->select('*');
        $this->db->from('patient_flow_levels');
        $this->db->where('phase', $phase);
        $this->db->where('parent_id IS NULL', null, false);
        $this->db->where('is_active', 1);
        $this->db->order_by('level_no', 'asc');
        $this->db->limit(1);

        $query = $this->db->get();

        if ($query === false) {
            log_message('error', 'get_first_level SQL ERROR: ' . print_r($this->db->error(), true));
            log_message('error', 'get_first_level SQL: ' . $this->db->last_query());
            return null;
        }

        return $query->row();
    }

    /**
     * Static default consultant doctor for OPDs saved from the patient-flow
     * board OPD modal: first staff with the Doctor role (staff_roles), falling
     * back to the first active staff. Prevents visit_details.cons_doctor being
     * NULL, which hides the OPD from listings that join staff.
     */
    public function get_first_doctor_id()
    {
        $row = $this->db->select("staff.id")
            ->from("staff")
            ->join("staff_roles", "staff_roles.staff_id = staff.id", "inner")
            ->join("roles", "roles.id = staff_roles.role_id", "inner")
            ->like("roles.name", "doctor", "both")
            ->where("staff.is_active", 1)
            ->order_by("staff.id", "asc")
            ->limit(1)
            ->get()
            ->row();
        if (!empty($row) && !empty($row->id)) {
            return (int) $row->id;
        }
        $fallback = $this->db->select("id")->from("staff")->where("is_active", 1)->order_by("id", "asc")->limit(1)->get()->row();
        return $fallback ? (int) $fallback->id : 0;
    }

    /**
     * Fetch one or all levels, ordered by phase/level_no to reflect the chain.
     *
     * @param int|null $id
     * @return object|array|null
     */
    public function get($id = null)
    {
        if ($id) {
            $this->db->select('*');
            $this->db->from('patient_flow_levels');
            $this->db->where('id', $id);
            $query = $this->db->get();
            return $query->row();
        }

        $this->db->select('pfl.*, r.name AS role_name');
        $this->db->from('patient_flow_levels pfl');
        $this->db->join('roles r', 'r.id = pfl.role_id', 'left');
        $this->db->order_by('pfl.phase', 'asc');
        $this->db->order_by('pfl.level_no', 'asc');
        $query = $this->db->get();
        return $query->result();
    }

    /**
     * Fetch levels as an ordered, flat list joined with their parent code/title
     * so views can render a readable chain.
     */
    public function get_chain()
    {
        $this->db->select('pfl.*, r.name AS role_name, parent.code AS parent_code, parent.title AS parent_title, pfl.can_senior_approval');
        $this->db->from('patient_flow_levels pfl');
        $this->db->join('roles r', 'r.id = pfl.role_id', 'left');
        $this->db->join('patient_flow_levels parent', 'parent.id = pfl.parent_id', 'left');
        $this->db->order_by('pfl.phase', 'asc');
        $this->db->order_by('pfl.level_no', 'asc');
        $query = $this->db->get();
        return $query->result();
    }

    /**
     * Fetch the final (terminal) level in the phase-1 chain — the level with
     * the highest level_no. A visit parked here with no next level is at the
     * end of the flow; one more "advance" marks it completed.
     *
     * @param string $phase
     * @return object|null
     */
    public function get_final_level($phase = 'P1')
    {
        $this->db->select('*');
        $this->db->from('patient_flow_levels');
        $this->db->where('phase', $phase);
        $this->db->where('is_active', 1);
        $this->db->order_by('level_no', 'desc');
        $this->db->limit(1);
        return $this->db->get()->row();
    }

    /**
     * Drives role-scoped visibility: a staff member only operates on patients
     * parked at levels owned by their own role(s).
     *
     * @param array $roleIds
     * @return array of level objects
     */
    public function get_levels_for_roles($roleIds)
    {
        if (empty($roleIds)) {
            return array();
        }
        $this->db->select('*');
        $this->db->from('patient_flow_levels');
        $this->db->where('is_active', 1);
        $this->db->where_in('role_id', $roleIds);
        $this->db->order_by('phase', 'asc');
        $this->db->order_by('level_no', 'asc');
        return $this->db->get()->result();
    }

    /**
     * Levels that have NO owner role assigned — patients parked there would be
     * invisible to every non-superadmin staff, so admins are warned on the UI.
     */
    public function get_unassigned_role_levels()
    {
        $this->db->select('*');
        $this->db->from('patient_flow_levels');
        $this->db->where('(role_id IS NULL OR role_id = 0)');
        $this->db->where('is_active', 1);
        $this->db->order_by('phase', 'asc');
        $this->db->order_by('level_no', 'asc');
        return $this->db->get()->result();
    }

    /**
     * Get all levels grouped into a nested tree by parent_id (for UI preview).
     */
    public function get_tree($phase = 'P1')
    {
        $list = $this->get(null);
        // Only keep levels belonging to the given phase.
        $filtered = array();
        foreach ($list as $row) {
            if ($row->phase == $phase) {
                $filtered[$row->id] = array(
                    'id'            => $row->id,
                    'code'          => $row->code,
                    'title'         => $row->title,
                    'role_name'     => $row->role_name,
                    'counter_type'  => $row->counter_type,
                    'level_no'      => $row->level_no,
                    'parent_id'     => $row->parent_id,
                    'children'      => array(),
                );
            }
        }

        $tree = array();
        foreach ($filtered as $id => $node) {
            $parent = $node['parent_id'];
            if ($parent && isset($filtered[$parent])) {
                $filtered[$parent]['children'][] = &$filtered[$id];
            } else {
                $tree[] = &$filtered[$id];
            }
        }

        return $tree;
    }

    /**
     * Get all ids that are descendants of a given level id.
     *
     * @param int $id
     * @return array
     */
    public function get_descendants($id)
    {
        $all   = $this->db->select('id, parent_id')->from('patient_flow_levels')->get()->result();
        $ids   = array();
        $stack = array($id);
        while ($stack) {
            $current = array_pop($stack);
            foreach ($all as $row) {
                if ($row->parent_id == $current && !in_array($row->id, $ids)) {
                    $ids[] = $row->id;
                    $stack[] = $row->id;
                }
            }
        }
        return $ids;
    }

    /**
     * Insert a new level row.
     *
     * @param array $data
     * @return int insert id
     */
    public function add($data)
    {
        $this->db->insert('patient_flow_levels', $data);
        return $this->db->insert_id();
    }

    /**
     * Update a level row.
     *
     * @param int   $id
     * @param array $data
     */
    public function update($id, $data)
    {
        $this->db->where('id', $id);
        $this->db->update('patient_flow_levels', $data);
    }

    /**
     * Delete a level row (blocked in the controller if it has children).
     *
     * @param int $id
     */
    public function delete($id)
    {
        $this->db->where('id', $id);
        $this->db->delete('patient_flow_levels');
    }

    /**
     * Delete a level and reparent its direct children up to the deleted
     * level's parent, so the chain remains connected. Returns false if the
     * level no longer exists.
     */
    public function delete_with_reparent($id)
    {
        $level = $this->get($id);
        if (!$level) {
            return false;
        }

        // Move direct children onto the deleted level's parent (null = root).
        $new_parent = $level->parent_id ? $level->parent_id : null;
        $this->db->where('parent_id', $id);
        $this->db->update('patient_flow_levels', array('parent_id' => $new_parent));

        $this->db->where('id', $id);
        $this->db->delete('patient_flow_levels');
        return true;
    }

    /* ============================================================
     * PATIENT FLOW VISITS
     * ============================================================ */

    /**
     * Generate the next sequential visit number for a given phase/day.
     * Format: PF-YYYYMMDD-#### (atomic-ish per request).
     */
    public function generate_visit_no($prefix = 'PF')
    {
        $date = date('Ymd');
        $this->db->select('visit_no');
        $this->db->from('patient_flow_visits');
        $this->db->like('visit_no', $prefix . '-' . $date . '-', 'after');
        $this->db->order_by('id', 'desc');
        $this->db->limit(1);
        $row = $this->db->get()->row();

        $seq = 1;
        if ($row) {
            $parts = explode('-', $row->visit_no);
            $seq = intval(end($parts)) + 1;
        }
        return $prefix . '-' . $date . '-' . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Create a patient flow visit row.
     *
     * @param array $data
     * @return int insert id
     */
    public function add_visit($data)
    {
        $this->db->insert('patient_flow_visits', $data);
        return $this->db->insert_id();
    }

    /**
     * Update a visit row.
     */
    public function update_visit($id, $data)
    {
        $this->db->where('id', $id);
        $this->db->update('patient_flow_visits', $data);
    }

    /**
     * Get a single visit (joined with patient + level).
     */
    public function get_visit($id)
    {
        $this->db->select('v.*, p.patient_name, p.mobileno, p.gender, p.age, p.guardian_name, p.patient_uuid,
                           l.code AS level_code, l.title AS level_title, l.counter_type AS level_counter, l.level_no AS level_no,
                           l.can_change_payment AS level_can_change_payment, l.can_cash_received AS level_can_cash_received,
                           l.can_change_status AS level_can_change_status, l.can_change_programme_status AS level_can_change_programme_status,
                           l.can_generate_invoice AS level_can_generate_invoice, l.can_case_taking AS level_can_case_taking, l.can_view_case_taking AS level_can_view_case_taking,
                           l.can_prescribe_medicine AS level_can_prescribe_medicine, l.print_vial AS level_print_vial, l.generate_bill AS level_generate_bill,
                           pl.code AS next_code, pl.title AS next_title, pl.id AS next_level_id,
                           pv.id AS prev_level_id, pv.code AS prev_code, pv.title AS prev_title,
                           sc.name AS case_taking_by_name, sc.surname AS case_taking_by_surname, sc.employee_id AS case_taking_by_employee_id');
        $this->db->from('patient_flow_visits v');
        $this->db->join('patients p', 'p.id = v.patient_id', 'left');
        $this->db->join('patient_flow_levels l', 'l.id = v.current_level_id', 'left');
        $this->db->join('patient_flow_levels pl', 'pl.parent_id = l.id', 'left'); // next level = child of current
        $this->db->join('patient_flow_levels pv', 'pv.id = l.parent_id', 'left'); // prev level = parent of current
        $this->db->join('staff sc', 'sc.id = v.case_taking_by', 'left'); // case-taking staff
        $this->db->where('v.id', $id);
        $visit = $this->db->get()->row();
        if ($visit && empty($visit->patient_uuid) && !empty($visit->patient_id)) {
            $visit->patient_uuid = $this->ensure_patient_uuid($visit->patient_id);
        }
        return $visit;
    }

    /**
     * List visits (with patient + current level info), newest first.
     *
     * By default only returns in-progress (active) visits — completed and
     * cancelled visits are hidden from the active board. Pass $status_filter
     * = 'completed' (or 'all') to include completed visits for review.
     *
     * @param int|null   $filter_level      optional single-level filter
     * @param array|null $allowed_level_ids role-scoped restriction; null = no restriction (superadmin)
     * @param int        $limit
     * @param string     $status_filter     'active' (default), 'completed', or 'all'
     * @param string     $search
     */
    public function get_visits($filter_level = null, $allowed_level_ids = null, $limit = 200, $search = null, $status_filter = 'active')
    {
        $this->db->select('v.*, p.patient_name, p.mobileno, p.gender, p.age, p.month, p.day, p.patient_uuid,
                           p.image, p.dob, p.guardian_name, p.blood_group, p.marital_status, p.email,
                           p.address, p.identification_number, p.known_allergies, p.note,
                           bp.name AS blood_group_name,
                           l.code AS level_code, l.title AS level_title, l.counter_type AS level_counter, l.level_no AS level_no, l.can_approve AS level_can_approve,
                           l.can_generate_billing_ref AS level_can_generate_billing_ref, l.can_print_billing AS level_can_print_billing, l.can_change_status AS level_can_change_status,
                           l.can_change_payment AS level_can_change_payment, l.can_cash_received AS level_can_cash_received, l.can_change_programme_status AS level_can_change_programme_status,
                           l.can_create_pbhp_id AS level_can_create_pbhp, l.can_generate_invoice AS level_can_generate_invoice, l.can_case_taking AS level_can_case_taking, l.can_view_case_taking AS level_can_view_case_taking,
                           l.can_prescribe_medicine AS level_can_prescribe_medicine, l.print_vial AS level_print_vial, l.generate_bill AS level_generate_bill,
                           pl.code AS next_code, pl.title AS next_title, pl.id AS next_level_id,
                           pv.id AS prev_level_id, pv.code AS prev_code, pv.title AS prev_title,
                           od.case_reference_id AS opd_case_reference, od.created_at AS opd_created_at,
                           sc.name AS case_taking_by_name, sc.surname AS case_taking_by_surname, sc.employee_id AS case_taking_by_employee_id');
        $this->db->from('patient_flow_visits v');
        $this->db->join('patients p', 'p.id = v.patient_id', 'left');
        $this->db->join('patient_flow_levels l', 'l.id = v.current_level_id', 'left');
        $this->db->join('patient_flow_levels pl', 'pl.parent_id = l.id', 'left');  // next = child of current
        $this->db->join('patient_flow_levels pv', 'pv.id = l.parent_id', 'left');  // prev = parent of current
        $this->db->join('opd_details od', 'od.id = v.opd_id', 'left'); // linked OPD record for listing
        $this->db->join('staff sc', 'sc.id = v.case_taking_by', 'left'); // case-taking staff
        $this->db->join('blood_bank_products bp', 'bp.id = p.blood_bank_product_id', 'left'); // blood group name

        // Status filter: by default show only in-progress (active) visits.
        // 'completed' shows finished visits; 'all' shows everything.
        // This keeps the active board clean — once a visit is completed it
        // no longer appears unless the user explicitly asks for completed.
        $status_filter = (string) $status_filter;
        if ($status_filter === 'completed') {
            $this->db->where('COALESCE(v.status, "active") =', 'completed');
        } elseif ($status_filter === 'cancelled') {
            $this->db->where('COALESCE(v.status, "active") =', 'cancelled');
        } elseif ($status_filter === 'all') {
            // no status filter
        } else {
            $this->db->where('COALESCE(v.status, "active") =', 'active');
        }

        if ($filter_level) {
            $this->db->where('v.current_level_id', $filter_level);
        }

        // Free-text search across the identifiers shown on the board listing:
        // visit no, token no, OPD id, patient uuid, patient name, mobile no,
        // PBHP id, billing reference, guardian name, email, address, etc.
        $search = trim((string) $search);
        if ($search !== '') {
            $this->db->group_start();
            $this->db->like('v.visit_no', $search);
            $this->db->or_like('v.token_no', $search);
            $this->db->or_like('v.opd_id', $search);
            $this->db->or_like('v.pbhp_id', $search);
            $this->db->or_like('v.billing_reference', $search);
            $this->db->or_like('p.patient_name', $search);
            $this->db->or_like('p.mobileno', $search);
            $this->db->or_like('p.patient_uuid', $search);
            $this->db->or_like('p.guardian_name', $search);
            $this->db->or_like('p.email', $search);
            $this->db->or_like('p.address', $search);
            $this->db->or_like('p.identification_number', $search);
            $this->db->or_like('l.code', $search);
            $this->db->or_like('l.title', $search);
            $this->db->group_end();
        }

        // Role-scoped visibility: staff only ever see patients parked at levels
        // owned by their own role(s). Once forwarded to a foreign level, the
        // visit disappears from their list automatically.
        if (is_array($allowed_level_ids)) {
            if (empty($allowed_level_ids)) {
                // Staff has no mapped levels -> sees nothing.
                return array();
            }
            $this->db->where_in('v.current_level_id', $allowed_level_ids);
        }
        $this->db->order_by('v.created_at', 'desc');
        $this->db->limit($limit);
        $visits = $this->db->get()->result();

        // Auto-heal any visits with patients missing UUID so it immediately displays on board
        if (!empty($visits)) {
            foreach ($visits as $v) {
                if (empty($v->patient_uuid) && !empty($v->patient_id)) {
                    $v->patient_uuid = $this->ensure_patient_uuid($v->patient_id);
                }
                // If the visit isn't yet linked to an OPD but the patient has one,
                // backfill the newest OPD id so OPD data appears in the listing.
                if (empty($v->opd_id) && !empty($v->patient_id)) {
                    $linked = $this->link_latest_opd_to_visit($v->id, $v->patient_id);
                    if ($linked) {
                        $v->opd_id = (int) $linked;
                        $v->opd_case_reference = $this->get_opd_case_reference($linked);
                        $v->opd_created_at = null;
                    }
                }
            }
        }

        return $visits;
    }

    /**
     * Return previous visits of a patient for the Patient History panel.
     */
    public function get_patient_visits($patient_id)
    {
        $patient_id = (int) $patient_id;
        if (!$patient_id) {
            return array();
        }
        $this->db->select('id, visit_no, token_no, current_level_id, status, created_at, billing_reference, invoice_no, case_taking_type');
        $this->db->from('patient_flow_visits');
        $this->db->where('patient_id', $patient_id);
        $this->db->order_by('created_at', 'desc');
        $this->db->limit(50);
        $visits = $this->db->get()->result();

        // Resolve level title for display.
        if (!empty($visits)) {
            $level_ids = array_unique(array_filter(array_map(function ($v) { return (int) $v->current_level_id; }, $visits)));
            $levels = array();
            if (!empty($level_ids)) {
                $this->db->where_in('id', $level_ids);
                $rows = $this->db->get('patient_flow_levels')->result();
                foreach ($rows as $r) { $levels[(int) $r->id] = $r; }
            }
            foreach ($visits as &$v) {
                $lvl = isset($levels[(int) $v->current_level_id]) ? $levels[(int) $v->current_level_id] : null;
                $v->level_title = $lvl ? $lvl->title : '-';
                $v->level_code = $lvl ? $lvl->code : '-';
                $v->visit_code = !empty($v->visit_no) ? $v->visit_no : ('#' . (int) $v->id);
            }
        }

        return $visits;
    }

    /**
     * Role-scoped forward guard: the visit may only be advanced by a staff whose
     * role owns the visit's CURRENT level.
     *
     * @param int   $visit_id
     * @param array $roleIds logged-in staff's role ids
     * @return bool
     */
    public function can_forward($visit_id, $roleIds)
    {
        if (empty($roleIds)) {
            return false;
        }
        $visit = $this->get_visit($visit_id);
        if (!$visit || !$visit->current_level_id) {
            return false;
        }
        $level = $this->get($visit->current_level_id);
        if (!$level) {
            return false;
        }
        return in_array((int) $level->role_id, array_map('intval', $roleIds), true);
    }

    /**
     * Role-scoped approve guard: the visit may only be approved by a staff whose
     * role owns a counter level that is marked "can approve" (can_approve = 1)
     * AND the visit is currently parked at that level.
     *
     * @param int   $visit_id
     * @param array $roleIds logged-in staff's role ids
     * @return bool
     */
    public function can_approve_visit($visit_id, $roleIds)
    {
        if (empty($roleIds)) {
            return false;
        }
        $visit = $this->get_visit($visit_id);
        if (!$visit || !$visit->current_level_id) {
            return false;
        }
        $level = $this->get($visit->current_level_id);
        if (!$level || !(int) $level->can_approve) {
            return false;
        }
        return in_array((int) $level->role_id, array_map('intval', $roleIds), true);
    }

    /**
     * Mark a visit as approved by a "can approve" counter level.
     * Also logs the action.
     *
     * @param int    $visit_id
     * @param int    $staff_id
     * @param int    $role_id
     * @param string $remarks
     * @return array ['status'=>bool, 'message'=>string]
     */
    public function approve_visit($visit_id, $staff_id, $role_id, $remarks = '')
    {
        $visit = $this->get_visit($visit_id);
        if (!$visit) {
            return array('status' => false, 'message' => 'Visit not found.');
        }
        if ($visit->approve_status === 'approved') {
            return array('status' => false, 'message' => 'Visit is already approved.');
        }

        $this->db->trans_start();
        $this->db->trans_strict(false);

        $this->db->where('id', $visit_id);
        $this->db->update('patient_flow_visits', array(
            'approve_status' => 'approved',
            'approved_by'    => $staff_id,
            'approved_at'    => date('Y-m-d H:i:s'),
        ));

        $this->db->insert('patient_flow_visit_logs', array(
            'visit_id'      => $visit_id,
            'from_level_id' => $visit->current_level_id,
            'to_level_id'   => $visit->current_level_id,
            'action'        => 'approve',
            'remarks'       => $remarks,
            'acted_by'      => $staff_id,
            'acted_role_id' => $role_id,
        ));

        $this->db->trans_complete();

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            return array('status' => false, 'message' => 'Database error.');
        }
        return array('status' => true, 'message' => 'Visit approved.');
    }

    /**
     * Record the payment on the linked core OPD when a patient-flow visit is
     * marked PAID: tops up the OPD transaction (section OPD) to the net charge
     * with amount_type payment, Cash mode — so the OPD visit-details page
     * shows the paid amount / completed payment.
     */
    public function mark_opd_payment_paid($visit_id, $staff_id)
    {
        $visit = $this->get_visit($visit_id);
        if (!$visit || empty($visit->opd_id)) {
            return null;
        }
        $opd_id = (int) $visit->opd_id;
        $net    = (float) $this->get_opd_net_amount($opd_id);
        $opd    = $this->db->select("patient_id, case_reference_id")->from("opd_details")->where("id", $opd_id)->get()->row();
        $payment = array(
            "section"          => "OPD",
            "patient_id"       => $opd ? (int) $opd->patient_id : (int) $visit->patient_id,
            "case_reference_id" => $opd ? (int) $opd->case_reference_id : null,
            "opd_id"           => $opd_id,
            "amount_type"      => "payment",
            "amount"           => $net,
            "payment_mode"     => "Cash",
            "payment_date"     => date("Y-m-d H:i:s"),
            "received_by"      => (int) $staff_id,
        );
        $txn = $this->db->select("id, amount_type")->from("transactions")->where("opd_id", $opd_id)->where("section", "OPD")->order_by("id", "asc")->limit(1)->get()->row();
        if ($txn) {
            $this->db->where("id", (int) $txn->id);
            $this->db->update("transactions", $payment);
        } else {
            $this->db->insert("transactions", $payment);
        }
        return true;
    }

    /**
     * Set / toggle the payment status of a visit (pending | paid).
     * Guards: visit must exist. Also logs the action. On PAID, records the
     * payment on the linked core OPD so the OPD listing reflects it.
     *
     * @param int    $visit_id
     * @param string $payment_status  'paid' | 'pending'
     * @param int    $staff_id
     * @param int    $role_id
     * @return array ['status'=>bool, 'message'=>string]
     */
    public function set_payment_status($visit_id, $payment_status, $staff_id, $role_id, $generate_token = true)
    {
        $visit = $this->get_visit($visit_id);
        if (!$visit) {
            return array('status' => false, 'message' => 'Visit not found.');
        }

        $status = ($payment_status === 'paid') ? 'paid' : 'pending';

        $this->db->trans_start();
        $this->db->trans_strict(false);

        // Generate a queue token the first time a visit is marked PAID
        // (re-toggling to paid again reuses the existing token).
        // Business rule (P1-L5 Token Control): the token becomes ACTIVE only
        // after payment confirmation — R1 then manages the queue.
        // $generate_token=false lets callers (e.g. the board "Mark as Paid"
        // button) confirm payment WITHOUT creating/activating a token yet.
        $token_no = null;
        $token_data = array();
        if ($status === 'paid' && $generate_token) {
            if (!empty($visit->token_no)) {
                $token_no = $visit->token_no;
            } else {
                $token_no = $this->generate_token_no();
                $token_data['token_no'] = $token_no;
            }
            if ($visit->token_status === 'NOT_ISSUED' || empty($visit->token_status)) {
                $token_data['token_status']       = 'ACTIVE';
                $token_data['token_activated_at'] = date('Y-m-d H:i:s');
            }
        }

        $this->db->where('id', $visit_id);
        $this->db->update('patient_flow_visits', array(
            'payment_status' => $status,
        ));

        if (!empty($token_data)) {
            $this->db->where('id', $visit_id);
            $this->db->update('patient_flow_visits', $token_data);
        } elseif ($status === 'pending') {
            // Payment reversed before the queue: deactivate the token again.
            // Tokens already CALLED / IN_PROCESS / CLOSED are left untouched.
            if (in_array($visit->token_status, array('ACTIVE'), true)) {
                $this->db->where('id', $visit_id);
                $this->db->update('patient_flow_visits', array(
                    'token_status'       => 'NOT_ISSUED',
                    'token_activated_at' => null,
                ));
            }
        }

        $this->db->insert('patient_flow_visit_logs', array(
            'visit_id'      => $visit_id,
            'from_level_id' => $visit->current_level_id,
            'to_level_id'   => $visit->current_level_id,
            'action'        => 'payment',
            'remarks'       => $status,
            'acted_by'      => $staff_id,
            'acted_role_id' => $role_id,
        ));


        // Keep the core OPD billing in sync: record the payment on the linked
        // OPD (transactions) so the OPD visit-details page shows the paid amount.
        if ($status === "paid" && $visit->payment_status !== "paid") {
            $this->mark_opd_payment_paid($visit_id, $staff_id);
        }
        $this->db->trans_complete();

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            return array('status' => false, 'message' => 'Database error.');
        }
        return array(
            'status'    => true,
            'message'   => ($status === 'paid') ? 'Payment marked as paid.' : 'Payment marked as pending.',
            'token_no'  => ($status === 'paid') ? $token_no : null,
        );
    }

    /**
     * Set / toggle the cash-received status of a visit (0 = not received, 1 = received).
     * Guard (enforce in the controller): a staff whose level has the
     * can_cash_received flag checked in the Levels form.
     *
     * @param int  $visit_id
     * @param int  $received 1 or 0
     * @param int  $staff_id
     * @param int  $role_id
     * @return array ['status'=>bool, 'message'=>string]
     */
    public function set_cash_received($visit_id, $received, $staff_id, $role_id)
    {
        $visit = $this->get_visit($visit_id);
        if (!$visit) {
            return array('status' => false, 'message' => 'Visit not found.');
        }

        $received = $received ? 1 : 0;

        $this->db->where('id', $visit_id);
        $this->db->update('patient_flow_visits', array(
            'cash_received' => $received,
        ));

        // Deduct stock from pharmacy for all prescribed medicines when cash is received
        if ($received == 1) {
            $prescriptions = $this->db->where('visit_id', $visit_id)
                ->where('is_active', 1)
                ->where('stock_deducted', 0)
                ->get('patient_flow_prescription')
                ->result_array();

            foreach ($prescriptions as $presc) {
                $medicine_id = (int) $presc['medicine_id'];
                $med = $this->db->where('id', $medicine_id)->get('medicine_master')->row();
                if (!$med || !$med->pharmacy_id || (float) ($med->stock_deduction_ml ?? 0) <= 0) {
                    continue;
                }
                $stockResult = $this->deduct_stock_for_medicine($medicine_id);
                if ($stockResult['status']) {
                    $this->db->where('id', $presc['id'])->update('patient_flow_prescription', array('stock_deducted' => 1));
                }
            }
        }

        $this->add_log(array(
            'visit_id'      => $visit_id,
            'from_level_id' => $visit->current_level_id,
            'to_level_id'   => $visit->current_level_id,
            'action'        => 'cash_received',
            'remarks'       => $received ? 'received' : 'not received',
            'acted_by'      => $staff_id,
            'acted_role_id' => $role_id,
        ));

        return array(
            'status'  => true,
            'message' => $received ? 'Cash received.' : 'Cash received status cleared.',
        );
    }
    /**
     * Allowed programme status values for a visit.
     */
    public function programme_statuses()
    {
        return array('enroll', 'declined', 'deferred');
    }

    /**
     * Set a visit's programme status (enroll | declined | deferred).
     * Permission is enforced in the controller via the can_change_programme_status level flag.
     */
    public function set_programme_status($visit_id, $status, $staff_id, $role_id)
    {
        $visit = $this->get_visit($visit_id);
        if (!$visit) {
            return array('status' => false, 'message' => 'Visit not found.');
        }

        $allowed = $this->programme_statuses();
        if (!in_array($status, $allowed, true)) {
            return array('status' => false, 'message' => 'Invalid programme status.');
        }

        $this->db->where('id', $visit_id);
        $this->db->update('patient_flow_visits', array(
            'programme_status' => $status,
        ));

        $this->add_log(array(
            'visit_id'      => $visit_id,
            'from_level_id' => $visit->current_level_id,
            'to_level_id'   => $visit->current_level_id,
            'action'        => 'programme_status',
            'remarks'       => $status,
            'acted_by'      => $staff_id,
            'acted_role_id' => $role_id,
        ));

        return array('status' => true, 'message' => 'Programme status updated to ' . ucfirst($status) . '.');
    }

    /**
     * Issue/activate a queue token for a PAID visit.
/**
     * Issue/activate a queue token for a PAID visit.
 Generates the token number
     * if none exists yet and moves the token to ACTIVE (unless it already holds a
     * live token). Permission to call this is gated by the can_generate_token level
     * flag — enforce in the controller.
     *
     * @param int $visit_id
     * @param int $staff_id
     * @param int $role_id
     * @return array ['status'=>bool, 'message'=>string, 'token_no'=>string]
     */
    public function generate_token($visit_id, $staff_id, $role_id)
    {
        $visit = $this->get_visit($visit_id);
        if (!$visit) {
            return array('status' => false, 'message' => 'Visit not found.');
        }

        // Business rule: only PAID visits may hold a live queue token.

        if ($visit->payment_status !== 'paid') {
            return array('status' => false, 'message' => 'Payment not confirmed — the token cannot be generated yet.');
        }

        $token_no = !empty($visit->token_no) ? $visit->token_no : $this->generate_token_no();

        if ($visit->token_status !== 'NOT_ISSUED' && !empty($visit->token_status) && $visit->token_status !== 'CLOSED') {
            return array('status' => false, 'message' => 'Token "' . $token_no . '" is already ' . $visit->token_status . '.');
        }

        $this->db->where('id', (int) $visit_id);
        $this->db->update('patient_flow_visits', array(
            'token_no'           => $token_no,
            'token_status'       => 'ACTIVE',
            'token_activated_at' => date('Y-m-d H:i:s'),
            'token_closed_at'    => null,
        ));

        $this->add_log(array(
            'visit_id'      => $visit_id,
            'from_level_id' => $visit->current_level_id,
            'to_level_id'   => $visit->current_level_id,
            'action'        => 'token_generate',
            'remarks'       => $token_no,
            'acted_by'      => $staff_id,
            'acted_role_id' => $role_id,
        ));

        return array('status' => true, 'message' => 'Token generated.', 'token_no' => $token_no);
    }

    /**
     * Generate a patient UUID in the format MC-KTR-YYYY-######.
     * The 6-digit part is a per-year running sequence (e.g. MC-KTR-2026-000001),
     * stored on the patients table (yearly reset). Optional $for_patient excludes
     * that patient so re-generating/backfilling the same patient keeps the value.
     */
    public function generate_patient_uuid($for_patient = null)
    {
        $year   = date('Y');
        $prefix = 'MC-KTR-' . $year . '-';

        $this->db->select("MAX(CAST(SUBSTRING_INDEX(patient_uuid, '-', -1) AS UNSIGNED)) AS max_seq", false);
        $this->db->from('patients');
        $this->db->like('patient_uuid', $prefix, 'after');
        if ($for_patient) {
            $this->db->where('id !=', (int) $for_patient);
        }
        $row = $this->db->get()->row();

        $seq = 1;
        if ($row && $row->max_seq !== null && $row->max_seq !== '') {
            $seq = intval($row->max_seq) + 1;
        }

        $uuid = $prefix . str_pad($seq, 6, '0', STR_PAD_LEFT);

        $guard = 0;
        while ($this->patient_uuid_exists($uuid) && $guard < 1000) {
            $seq++;
            $uuid = $prefix . str_pad($seq, 6, '0', STR_PAD_LEFT);
            $guard++;
        }

        return $uuid;
    }

    public function patient_uuid_exists($uuid)
    {
        if (empty($uuid)) {
            return false;
        }
        return (bool) $this->db->where('patient_uuid', $uuid)
                               ->from('patients')
                               ->count_all_results();
    }

    /**
     * Backfill the patient-level UUID on an existing patient row.
     * Only fills when the patient has no UUID yet (idempotent).
     */
    public function backfill_patient_uuid($patient_id, $uuid = null)
    {
        return $this->ensure_patient_uuid($patient_id, $uuid);
    }

    /**
     * Ensure a UUID is set on a patient — used after add_patient() as a
     * guaranteed fallback. Sets the UUID if the column is empty/null.
     */
    public function ensure_patient_uuid($patient_id, $uuid = null)
    {
        $patient_id = (int) $patient_id;
        if (!$patient_id) {
            return false;
        }

        // Check if patient already has a valid non-empty UUID
        $this->db->select('patient_uuid');
        $this->db->where('id', $patient_id);
        $row = $this->db->get('patients')->row();

        if ($row && !empty($row->patient_uuid)) {
            return $row->patient_uuid;
        }

        if (empty($uuid)) {
            $uuid = $this->generate_patient_uuid($patient_id);
        }

        $this->db->where('id', $patient_id);
        $this->db->where('(patient_uuid IS NULL OR patient_uuid = "" OR patient_uuid = "0")', null, false);
        $this->db->update('patients', array('patient_uuid' => $uuid));

        return $uuid;
    }


    /**
     * Queue token: T-NNN (running sequence, e.g. T-001, T-002, ...).
     */
    /**
     * PBHP "code" options for the Create-PBHP-ID modal — label => code.
     * The label is shown in the selectbox; the code is what gets embedded
     * in the generated PBHP id: PBHP-[CODE]-KTR-[6 DIGIT NO].
     *
     * @return array
     */
    public function pbhp_codes()
    {
        return array(
            'Normal Checkup'  => 'BDXE21',
            'Regular Checkup' => 'BDXE11',
        );
    }

    /**
     * Create a PBHP id for a visit that does not have one yet.
     * Format: PBHP-[CODE]-KTR-[6 DIGIT NO] e.g. PBHP-BDXE21-KTR-000123.
     * The 6-digit sequence is global (continues from the highest existing
     * PBHP id in the visits table).
     *
     * @param int    $visit_id
     * @param string $code     one of pbhp_codes() values
     * @param int    $staff_id
     * @param int    $role_id
     * @return array ['status'=>bool, 'message'=>string, 'pbhp_id'=>string|null]
     */
    public function create_pbhp_id($visit_id, $code, $staff_id, $role_id)
    {
        $visit = $this->get_visit($visit_id);
        if (!$visit) {
            return array('status' => false, 'message' => 'Visit not found.');
        }

        if (!empty($visit->pbhp_id)) {
            return array('status' => false, 'message' => 'PBHP ID "' . $visit->pbhp_id . '" already exists for this visit.');
        }

        // A PBHP id can only be created once the visit is enrolled in the programme.
        if ($visit->programme_status !== 'enroll') {
            return array('status' => false, 'message' => 'PBHP ID can only be created when the programme status is Enroll.');
        }

        // Next 6-digit sequence number (global across all PBHP ids).
        $row = $this->db->select("MAX(CAST(SUBSTRING_INDEX(pbhp_id, '-', -1) AS UNSIGNED)) AS maxno", false)
            ->from('patient_flow_visits')
            ->like('pbhp_id', 'PBHP-', 'after')
            ->get()
            ->row();
        $no = ($row && (int) $row->maxno > 0) ? ((int) $row->maxno + 1) : 1;
        $pbhp_id = 'PBHP-' . $code . '-KTR-' . str_pad($no, 6, '0', STR_PAD_LEFT);

        $this->db->where('id', (int) $visit_id);
        $this->db->update('patient_flow_visits', array('pbhp_id' => $pbhp_id));
        if ($this->db->affected_rows() < 0) {
            return array('status' => false, 'message' => 'Database error.');
        }

        // Assign stage based on how many previous visits this patient has.
        // First visit -> Q1S1, second visit -> Q1S2, third visit -> Q1S3, etc.
        $previous_visits = (int) $this->db->where('patient_id', $visit->patient_id)
            ->where('id !=', $visit_id)
            ->count_all_results('patient_flow_visits');
        $stage_number = $previous_visits + 1;

        $stage_row = null;
        if ($this->db->table_exists('stage_master')) {
            // Try Q1S1, Q1S2, Q1S3 ... pattern first
            $stage_row = $this->db->where('quarter', 'Q1')
                ->where('stage', 'S' . $stage_number)
                ->get('stage_master')
                ->row();
            // Fallback: next available stage by sort_order if Q1S* not found
            if (!$stage_row) {
                $stage_row = $this->db->order_by('sort_order', 'asc')
                    ->get('stage_master')
                    ->row();
            }
        }

        if ($stage_row) {
            $this->db->insert('patient_flow_stage_assignment', array(
                'visit_id' => $visit_id,
                'stage_id' => (int) $stage_row->id,
                'stage_code' => $stage_row->code,
                'is_current' => 1,
                'assigned_at' => date('Y-m-d H:i:s'),
            ));
        }

        $this->add_log(array(
            'visit_id'      => $visit_id,
            'from_level_id' => $visit->current_level_id,
            'to_level_id'   => $visit->current_level_id,
            'action'        => 'pbhp_create',
            'remarks'       => $pbhp_id . ' | Stage: ' . ($stage_row ? $stage_row->code : 'none'),
            'acted_by'      => $staff_id,
            'acted_role_id' => $role_id,
        ));

        return array('status' => true, 'message' => 'PBHP ID created.', 'pbhp_id' => $pbhp_id);
    }

    public function generate_token_no()
    {
        $prefix = 'T-';
        $this->db->select('token_no');
        $this->db->from('patient_flow_visits');
        $this->db->like('token_no', $prefix, 'after');
        $this->db->order_by('id', 'desc');
        $this->db->limit(1);
        $row = $this->db->get()->row();

        $seq = 1;
        if ($row) {
            $parts = explode('-', $row->token_no);
            $seq = intval(end($parts)) + 1;
        }
        return $prefix . str_pad($seq, 3, '0', STR_PAD_LEFT);
    }

    // ============================================================
    // TOKEN QUEUE LIFECYCLE (P1-L5 Token Control / R1)
    // Only paid patients enter the doctor queue. R1 activates the
    // token after payment confirmation and manages the queue until
    // the consultation is completed.
    // ============================================================

    /**
     * All allowed token statuses in lifecycle order.
     */
    public function token_statuses()
    {
        return array(
            'NOT_ISSUED',
            'ACTIVE',
            'WAITING',
            'CALLED',
            'IN_PROCESS',
            'CLOSED',
            'NO_SHOW',
            'SKIPPED',
            'WAITING_SECOND_CALL',
        );
    }

    /**
     * Allowed token status transitions. Anything not listed here is refused.
     *   NOT_ISSUED            -> ACTIVE            (payment confirmed)
     *   ACTIVE                -> WAITING           (queued for the doctor)
     *   WAITING               -> CALLED / NO_SHOW / SKIPPED
     *   CALLED                -> IN_PROCESS / NO_SHOW
     *   WAITING_SECOND_CALL   -> CALLED / NO_SHOW
     *   IN_PROCESS            -> CLOSED            (consultation done)
     *   NO_SHOW / SKIPPED     -> WAITING           (re-queue)
     *   CLOSED                -> (terminal)
     *
     * @return array [from_status => [to_status, ...]]
     */
    public function token_transitions()
    {
        return array(
            'NOT_ISSUED'          => array('ACTIVE'),
            'ACTIVE'              => array('WAITING'),
            'WAITING'             => array('CALLED', 'NO_SHOW', 'SKIPPED'),
            'CALLED'              => array('IN_PROCESS', 'NO_SHOW'),
            'WAITING_SECOND_CALL' => array('CALLED', 'NO_SHOW'),
            'IN_PROCESS'          => array('CLOSED'),
            'NO_SHOW'             => array('WAITING'),
            'SKIPPED'             => array('WAITING'),
            'CLOSED'              => array(),
        );
    }

    /**
     * Role-scoped token guard: the staff's role must own the visit's CURRENT
     * level AND that level must be the phase-1 level-5 token control counter
     * (R1). Identified by phase 'P1' + level_no 5 so the guard survives
     * title/code edits (same pattern as can_manage_billing()).
     *
     * @param int   $visit_id
     * @param array $roleIds logged-in staff's role ids
     * @return bool
     */
    public function can_manage_token($visit_id, $roleIds)
    {
        if (empty($roleIds)) {
            return false;
        }
        $visit = $this->get_visit($visit_id);
        if (!$visit || !$visit->current_level_id) {
            return false;
        }
        $level = $this->get($visit->current_level_id);
        if (!$level) {
            return false;
        }
        // The visit's current level must be owned by this staff's role, AND the
        // level must grant token-management access. Access is granted either by
        // the level's can_generate_token flag (checkbox in the Levels form) or,
        // for backward compatibility, by the legacy R1 token-control counter.
        if (!(int) $level->can_generate_token) {
            if ($level->phase !== 'P1' || (int) $level->level_no !== 5) {
                return false;
            }
        }
        return in_array((int) $level->role_id, array_map('intval', $roleIds), true);
    }

    /**
     * Move a visit's token to a new lifecycle status.
     * Validates the transition, stamps timestamps and logs the action.
     *
     * @param int    $visit_id
     * @param string $new_status one of token_statuses()
     * @param int    $staff_id
     * @param int    $role_id
     * @param string $remarks
     * @return array ['status'=>bool, 'message'=>string]
     */
    public function set_token_status($visit_id, $new_status, $staff_id, $role_id = null, $remarks = '')
    {
        $new_status = strtoupper(trim((string) $new_status));
        if (!in_array($new_status, $this->token_statuses(), true)) {
            return array('status' => false, 'message' => 'Invalid token status.');
        }

        $visit = $this->get_visit($visit_id);
        if (!$visit) {
            return array('status' => false, 'message' => 'Visit not found.');
        }

        $current = !empty($visit->token_status) ? $visit->token_status : 'NOT_ISSUED';
        if ($current === $new_status) {
            return array('status' => false, 'message' => 'Token status is already "' . $new_status . '".');
        }

        $allowed = $this->token_transitions();
        $from_allowed = isset($allowed[$current]) ? $allowed[$current] : array();
        if (!in_array($new_status, $from_allowed, true)) {
            return array('status' => false, 'message' => 'Token cannot move from "' . $current . '" to "' . $new_status . '".');
        }

        // Business rule: only PAID visits may hold a live queue token.
        if ($visit->payment_status !== 'paid' && !in_array($new_status, array('NOT_ISSUED', 'ACTIVE'), true)) {
            return array('status' => false, 'message' => 'Payment not confirmed — the token cannot enter the queue yet.');
        }

        $data = array('token_status' => $new_status);
        if ($current === 'NOT_ISSUED' && $new_status === 'ACTIVE') {
            $data['token_activated_at'] = date('Y-m-d H:i:s');
            // Guarantee a token number exists at activation.
            if (empty($visit->token_no)) {
                $data['token_no'] = $this->generate_token_no();
            }
        }
        if (in_array($new_status, array('CLOSED', 'NO_SHOW', 'SKIPPED'), true)) {
            $data['token_closed_at'] = date('Y-m-d H:i:s');
        } elseif ($new_status === 'WAITING') {
            // Re-queueing from NO_SHOW / SKIPPED clears the close stamp.
            $data['token_closed_at'] = null;
        }

        $this->db->where('id', (int) $visit_id);
        $this->db->update('patient_flow_visits', $data);

        $this->add_log(array(
            'visit_id'      => $visit_id,
            'from_level_id' => $visit->current_level_id,
            'to_level_id'   => $visit->current_level_id,
            'action'        => 'token_' . strtolower($new_status),
            'remarks'       => ($remarks !== '' ? $remarks . ' — ' : '') . $current . ' -> ' . $new_status . (!empty($visit->token_no) ? ' (Token: ' . $visit->token_no . ')' : ''),
            'acted_by'      => $staff_id,
            'acted_role_id' => $role_id,
        ));

        return array('status' => true, 'message' => 'Token status changed to "' . $new_status . '".', 'token_status' => $new_status);
    }

    /**
     * Token queue listing for the R1 Token Control board: only PAID visits
     * (they are the only ones allowed to enter the doctor queue), optionally
     * filtered by token status and restricted to the given level ids.
     *
     * @param string|null $status_filter     token status or null/'all' for every queue status
     * @param array|null  $allowed_level_ids role-scoped restriction; null = no restriction
     * @param int         $limit
     * @return array
     */
    public function get_token_queue($status_filter = null, $allowed_level_ids = null, $limit = 200)
    {
        $this->db->select('v.*, p.patient_name, p.mobileno, p.gender, p.age, p.patient_uuid,
                           l.code AS level_code, l.title AS level_title');
        $this->db->from('patient_flow_visits v');
        $this->db->join('patients p', 'p.id = v.patient_id', 'left');
        $this->db->join('patient_flow_levels l', 'l.id = v.current_level_id', 'left');

        // Queue business rule: only paid patients enter the doctor queue.
        $this->db->where('v.payment_status', 'paid');

        if ($status_filter !== null && $status_filter !== '' && $status_filter !== 'all') {
            $this->db->where('v.token_status', strtoupper((string) $status_filter));
        }

        if (is_array($allowed_level_ids)) {
            if (empty($allowed_level_ids)) {
                return array();
            }
            $this->db->where_in('v.current_level_id', $allowed_level_ids);
        }

        // Oldest activation first — first paid, first served.
        // order_by() with escaping disabled keeps each comma-separated term
        // intact; "IS NULL" pushes legacy un-activated rows to the end.
        $this->db->order_by('v.token_activated_at IS NULL, v.token_activated_at ASC, v.created_at ASC', '', false);
        $this->db->limit($limit);
        return $this->db->get()->result();
    }

    /**
     * Role-scoped backward guard: the visit may only be sent back to the previous
     * step by a staff whose role owns the visit's CURRENT level (the level the
     * patient was wrongly forwarded to). Superadmin bypasses via the controller.
     *
     * @param int   $visit_id
     * @param array $roleIds
     * @return bool
     */
    public function can_backward($visit_id, $roleIds)
    {
        if (empty($roleIds)) {
            return false;
        }
        $visit = $this->get_visit($visit_id);
        if (!$visit || !$visit->current_level_id) {
            return false;
        }
        $level = $this->get($visit->current_level_id);
        if (!$level || !$level->parent_id) {
            return false;   // No previous step to go back to.
        }
        return in_array((int) $level->role_id, array_map('intval', $roleIds), true);
    }

    /**
     * Move a visit back to the previous step in the chain.
     * Only makes sense when the patient was wrongly forwarded forward.
     * Also logs the action.
     *
     * @param int    $visit_id
     * @param int    $staff_id
     * @param int    $role_id
     * @param string $remarks
     * @return array ['status'=>bool, 'message'=>string]
     */
    public function backward_visit($visit_id, $staff_id, $role_id, $remarks = '')
    {
        $visit = $this->get_visit($visit_id);
        if (!$visit || !$visit->current_level_id) {
            return array('status' => false, 'message' => 'Visit not found.');
        }
        $level = $this->get($visit->current_level_id);
        if (!$level || !$level->parent_id) {
            return array('status' => false, 'message' => 'This is the first step; there is no previous level to return to.');
        }

        $prev_id = (int) $level->parent_id;

        $this->db->trans_start();
        $this->db->trans_strict(false);

        $this->db->where('id', $visit_id);
        $this->db->update('patient_flow_visits', array(
            'current_level_id' => $prev_id,
            'level_started_at' => date('Y-m-d H:i:s'),
        ));

        $this->db->insert('patient_flow_visit_logs', array(
            'visit_id'      => $visit_id,
            'from_level_id' => $visit->current_level_id,
            'to_level_id'   => $prev_id,
            'action'        => 'backward',
            'remarks'       => $remarks,
            'acted_by'      => $staff_id,
            'acted_role_id' => $role_id,
        ));

        $this->db->trans_complete();

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            return array('status' => false, 'message' => 'Database error.');
        }
        return array('status' => true, 'message' => 'Patient sent back to ' . $visit->prev_title . '.');
    }

    /**
     * Find an existing patient by name + mobile so repeat arrivals reuse the
     * existing patient instead of creating a duplicate patient row.
     *
     * @param string $name     Patient name
     * @param string $mobileno Mobile number
     * @return int|null Existing patient id, or null if none found.
     */
    public function find_patient_by_name_mobile($name, $mobileno)
    {
        $name     = trim((string) $name);
        $mobileno = trim((string) $mobileno);
        if ($name === '' && $mobileno === '') {
            return null;
        }
        $this->db->select('patients.id');
        $this->db->from('patients');
        $this->db->where('patients.is_active', 'yes');
        if ($name !== '' && $mobileno !== '') {
            $this->db->where('patients.patient_name', $name);
            $this->db->where('patients.mobileno', $mobileno);
        } elseif ($mobileno !== '') {
            $this->db->where('patients.mobileno', $mobileno);
        } else {
            return null;
        }
        $this->db->order_by('patients.id', 'desc');
        $this->db->limit(1);
        $query = $this->db->get();
        if ($query && $query->num_rows() > 0) {
            return (int) $query->row()->id;
        }
        return null;
    }

    /**
     * Role name owned by a given flow level (joins roles). Used to route
     * notifications to the counter staff who should act on a visit next.
     *
     * @param int $level_id
     * @return string|null
     */
    public function get_level_role_name($level_id)
    {
        if (!$level_id) {
            return null;
        }
        $this->db->select('r.name AS role_name');
        $this->db->from('patient_flow_levels pfl');
        $this->db->join('roles r', 'r.id = pfl.role_id', 'left');
        $this->db->where('pfl.id', intval($level_id));
        $row = $this->db->get()->row();
        return ($row && $row->role_name) ? $row->role_name : null;
    }

    /**
     * Insert an in-app system notification targeted at a role (the counter that
     * should next act on the visit) or at the Super Admin.
     *
     * @param string $role_name notification_for role name
     * @param string $title
     * @param string $desc
     * @param string $type      notification_type
     */
    public function notify_staff($role_name, $title, $desc, $type = 'patient_flow')
    {
        if ($role_name === null || trim((string) $role_name) === '') {
            return;
        }

        // Resolve the role id from the role name.
        $role_row = $this->db->select('id')->from('roles')->where('name', $role_name)->get()->row();
        if (!$role_row) {
            return;
        }
        $role_id = (int) $role_row->id;

        // The bell only surfaces rows with role_id = staff.role_id AND
        // receiver_id = staff.id, so insert one row per staff member of the role.
        // Staff↔role mapping lives in staff_roles (staff_id, role_id).
        // NOTE: staff_roles.is_active is 0 for all existing rows in this DB,
        // so do NOT filter on it — only require the staff member to be active.
        $staff = $this->db->select('sr.staff_id AS id')
            ->from('staff_roles sr')
            ->join('staff s', 's.id = sr.staff_id')
            ->where('sr.role_id', $role_id)
            ->where('s.is_active', 1)
            ->group_by('sr.staff_id')
            ->get()->result();
        if (empty($staff)) {
            return;
        }

        $now = date('Y-m-d H:i:s');
        foreach ($staff as $s) {
            $this->db->insert('system_notification', array(
                'notification_title' => $title,
                'notification_desc'  => $desc,
                'notification_for'   => $role_name,
                'notification_type'  => $type,
                'role_id'            => $role_id,
                'receiver_id'        => (int) $s->id,
                'date'               => $now,
                'is_active'          => 'yes',
                'created_at'         => $now,
            ));
        }
    }

    /**
     * Advance a visit to the next level in the chain (if one exists).
     * Also logs the transition.
     *
     * @param int    $visit_id
     * @param int    $staff_id
     * @param int    $role_id
     * @param string $remarks
     * @return array ['status'=>bool, 'next_level_id'=>int|null]
     */
    public function advance_visit($visit_id, $staff_id, $role_id, $remarks = '')
    {
        $visit = $this->get_visit($visit_id);
        if (!$visit) {
            return array('status' => false, 'message' => 'Visit not found.');
        }

        // Already at the final level: mark the visit as completed instead of
        // returning an error. This is the terminal lifecycle event — once a
        // visit is "completed" the patient is free to be re-enrolled on a new
        // visit (see find_active_visit_by_patient which only blocks on visits
        // whose status is still 'active').
        if (!$visit->next_level_id) {
            // Already completed? Just confirm.
            $this->db->select('status');
            $this->db->from('patient_flow_visits');
            $this->db->where('id', $visit_id);
            $current = $this->db->get()->row();
            if ($current && $current->status === 'completed') {
                return array('status' => false, 'message' => 'This visit is already completed.');
            }
            $result = $this->complete_visit($visit_id, $staff_id, $role_id, $remarks);
            if ($result['status']) {
                $result['message'] = $this->lang->line('visit_completed') ?: 'Visit completed. Patient has finished the flow.';
            }
            return $result;
        }

        $next_id = $visit->next_level_id;

        $this->db->trans_start();
        $this->db->trans_strict(false);

        // Advance visit.
        $this->db->where('id', $visit_id);
        $this->db->update('patient_flow_visits', array(
            'current_level_id' => $next_id,
            'level_started_at' => date('Y-m-d H:i:s'),
        ));

        // Log.
        $this->db->insert('patient_flow_visit_logs', array(
            'visit_id'      => $visit_id,
            'from_level_id' => $visit->current_level_id,
            'to_level_id'   => $next_id,
            'action'        => 'forward',
            'remarks'       => $remarks,
            'acted_by'      => $staff_id,
            'acted_role_id' => $role_id,
        ));

        $this->db->trans_complete();

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            return array('status' => false, 'message' => 'Database error.');
        }

        return array('status' => true, 'next_level_id' => $next_id, 'next_title' => $visit->next_title);
    }

    /**
     * Mark a visit as completed (terminal lifecycle event).
     *
     * When a visit reaches the final level of the chain and the staff
     * advances it one more time, the visit is flagged status='completed'
     * (instead of erroring with "final level"). Once completed,
     * find_active_visit_by_patient() will no longer return it, so the
     * patient can be re-enrolled for a new visit.
     *
     * @param int    $visit_id
     * @param int    $staff_id
     * @param int    $role_id
     * @param string $remarks
     * @return array ['status'=>bool, 'message'=>string]
     */
    public function complete_visit($visit_id, $staff_id, $role_id = null, $remarks = '')
    {
        $visit = $this->get_visit($visit_id);
        if (!$visit) {
            return array('status' => false, 'message' => 'Visit not found.');
        }

        $this->db->trans_start();
        $this->db->trans_strict(false);

        $this->db->where('id', $visit_id);
        $this->db->update('patient_flow_visits', array(
            'status'       => 'completed',
            'completed_at' => date('Y-m-d H:i:s'),
            'completed_by' => $staff_id,
        ));

        $this->db->insert('patient_flow_visit_logs', array(
            'visit_id'      => $visit_id,
            'from_level_id' => $visit->current_level_id,
            'to_level_id'   => $visit->current_level_id,
            'action'        => 'complete',
            'remarks'       => $remarks,
            'acted_by'      => $staff_id,
            'acted_role_id' => $role_id,
        ));

        $this->db->trans_complete();

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            return array('status' => false, 'message' => 'Database error while completing visit.');
        }

        return array('status' => true, 'message' => 'Visit completed.');
    }

    /**
     * Add a manual log row (used for registration / payment / token etc).
     */
    public function add_log($data)
    {
        $this->db->insert('patient_flow_visit_logs', $data);
        return $this->db->insert_id();
    }

    /**
     * Load the step-by-step history for a set of visit ids, grouped by visit,
     * so the board can render an audit timeline (who moved the patient, when,
     * from which level to which). Joined with level codes + acting staff name.
     *
     * @param array $ids list of patient_flow_visits.id
     * @return array [visit_id => [log_obj, ...]] oldest-first
     */
    public function get_logs_by_visit_ids(array $ids)
    {
        if (empty($ids)) {
            return array();
        }

        $this->db->select('lg.visit_id, lg.from_level_id, lg.to_level_id, lg.action, lg.remarks, lg.created_at,
                           fl.code AS from_code, fl.title AS from_title,
                           tl.code AS to_code, tl.title AS to_title,
                           CONCAT_WS(" ", s.name, s.surname) AS acted_by_name');
        $this->db->from('patient_flow_visit_logs lg');
        $this->db->join('patient_flow_levels fl', 'fl.id = lg.from_level_id', 'left');
        $this->db->join('patient_flow_levels tl', 'tl.id = lg.to_level_id', 'left');
        $this->db->join('staff s', 's.id = lg.acted_by', 'left');
        $this->db->where_in('lg.visit_id', $ids);
        $this->db->order_by('lg.visit_id', 'asc');
        $this->db->order_by('lg.created_at', 'asc');
        $this->db->order_by('lg.id', 'asc');

        $logs = $this->db->get()->result();

        $grouped = array();
                foreach ($logs as $log) {
            $grouped[$log->visit_id][] = $log;
        }
        return $grouped;
    }

    /**
     * Find the visit a patient currently has IN PROGRESS in the flow.
     * Only returns visits whose status is 'active' (or NULL for legacy rows
     * created before the status column existed). Once a visit is marked
     * 'completed', it no longer blocks re-enrollment, so the same patient
     * can be added again for a new visit.
     *
     * Returns the newest active visit row or null.
     */
    public function find_active_visit_by_patient($patient_id)
    {
        $this->db->select('v.*, l.code AS level_code');
        $this->db->from('patient_flow_visits v');
        $this->db->join('patient_flow_levels l', 'l.id = v.current_level_id', 'left');
        $this->db->where('v.patient_id', (int) $patient_id);
        // Only block on genuinely in-progress visits. Completed / cancelled
        // visits are treated as historical and do NOT prevent re-enrollment.
        $this->db->where('(v.status = "active" OR v.status IS NULL)');
        $this->db->order_by('v.id', 'desc');
        $this->db->limit(1);
        return $this->db->get()->row();
    }

    /**
     * Net OPD charge for an OPD record: the amount stored on patient_charges
     * AFTER discount and tax are applied by the OPD module (that is the figure
     * the OPD form saves in the `amount` column). Summed per OPD in case more
     * than one charge row exists.
     */
    public function get_opd_net_amount($opd_id)
    {
        if (empty($opd_id)) {
            return 0.0;
        }
        $this->db->select('COALESCE(SUM(amount), 0) AS net_amount', false);
        $this->db->from('patient_charges');
        $this->db->where('opd_id', (int) $opd_id);
        $row = $this->db->get()->row();
        return $row ? (float) $row->net_amount : 0.0;
    }

    /**
     * Sync a flow visit's amount from its linked OPD's net charge (after
     * tax/discount) so the payment counters always see the final figure.
     *
     * @param int $visit_id
     * @return float|null the synced amount, or null when the visit has no OPD
     */
    public function sync_visit_amount_from_opd($visit_id)
    {
        $visit = $this->get_visit($visit_id);
        if (!$visit || empty($visit->opd_id)) {
            return null;
        }
        $net = $this->get_opd_net_amount($visit->opd_id);
        $this->db->where('id', (int) $visit_id);
        $this->db->update('patient_flow_visits', array('amount' => $net));
        return $net;
    }

    /**
     * Link a core OPD record (opd_details.id) to a patient flow visit.
     * The OPD is created automatically at registration (R1) so every flow
     * visit carries the OPD id used by the OPD module / doctor consultation.
     */
    public function set_visit_opd_id($visit_id, $opd_id)
    {
        if (empty($opd_id)) {
            return false;
        }
        $this->db->where('id', (int) $visit_id);
        return $this->db->update('patient_flow_visits', array('opd_id' => (int) $opd_id));
    }

    /**
     * Newest opd_details.id for a patient — used to link a flow visit that was
     * enrolled from the OPD modal (enroll_from_opd) back to the OPD just saved.
     */
    public function get_latest_opd_id_by_patient($patient_id)
    {
        $this->db->select('id');
        $this->db->from('opd_details');
        $this->db->where('patient_id', (int) $patient_id);
        $this->db->order_by('id', 'desc');
        $this->db->limit(1);
        $row = $this->db->get()->row();
        return $row ? (int) $row->id : 0;
    }

    /**
     * Backfill: link a visit to its patient's newest OPD record. Only writes
     * when the visit has no OPD linked yet (idempotent — safe to call on every
     * board load so OPDs created in the regular OPD module eventually show up).
     *
     * @param int $visit_id
     * @param int $patient_id
     * @return int the linked opd_details.id, or 0 when nothing linked/updated
     */
    public function link_latest_opd_to_visit($visit_id, $patient_id)
    {
        if (empty($visit_id) || empty($patient_id)) {
            return 0;
        }
        $opd_id = $this->get_latest_opd_id_by_patient($patient_id);
        if (empty($opd_id)) {
            return 0;
        }
        $this->db->where('id', (int) $visit_id);
        $this->db->where('(opd_id IS NULL OR opd_id = 0)', null, false);
        $this->db->update('patient_flow_visits', array('opd_id' => (int) $opd_id));
        return $this->db->affected_rows() > 0 ? $opd_id : 0;
    }

    /**
     * Case reference (visit id) of an OPD record, used to display the OPD's
     * "Checkup/Visit" number alongside its id on the board.
     *
     * @param int $opd_id
     * @return int|null
     */
    public function get_opd_case_reference($opd_id)
    {
        if (empty($opd_id)) {
            return null;
        }
        $this->db->select('case_reference_id');
        $this->db->from('opd_details');
        $this->db->where('id', (int) $opd_id);
        $row = $this->db->get()->row();
        return $row ? (int) $row->case_reference_id : null;
    }

    // ============================================================
    // Level-2 (R2) Billing Reference
    // ============================================================

    /**
     * Allowed billing statuses (in the order they may logically occur).
     */
    public function billing_statuses()
    {
        // return array('pending', 'billed', 'paid', 'cancelled');
        return array('pending', 'billed', 'cancelled');
    }

    /**
     * Level-2 (R2) billing reference: BR-KTR-YYMMDD-###### (per-day running
     * sequence starting 000001). Built exactly like generate_token_no(), but
     * with a YYMMDD date prefix and 6-digit padding — the day rollover is
     * handled automatically by the date prefix.
     */
    public function generate_billing_reference()
    {
        $prefix = 'BR-KTR-' . date('ymd') . '-';
        $this->db->select('billing_reference');
        $this->db->from('patient_flow_visits');
        $this->db->like('billing_reference', $prefix, 'after');
        $this->db->order_by('id', 'desc');
        $this->db->limit(1);
        $row = $this->db->get()->row();

        $seq = 1;
        if ($row) {
            $parts = explode('-', $row->billing_reference);
            $seq   = intval(end($parts)) + 1;
        }
        return $prefix . str_pad($seq, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Persist the R2 billing reference on a visit: writes the reference,
     * flips billing_status to 'billed', stamps who/when, and logs the action.
     *
     * @param int    $visit_id
     * @param string $reference e.g. BR-KTR-260902-000123
     * @param int    $staff_id
     * @param int    $role_id
     * @return array ['status'=>bool, 'message'=>string]
     */
    public function set_billing_reference($visit_id, $reference, $staff_id, $role_id = null)
    {
        $visit = $this->get_visit($visit_id);
        if (!$visit) {
            return array('status' => false, 'message' => 'Visit not found.');
        }
        if (!empty($visit->billing_reference)) {
            return array('status' => false, 'message' => 'A billing reference has already been generated for this visit.');
        }

        $this->db->where('id', (int) $visit_id);
        $this->db->update('patient_flow_visits', array(
            'billing_reference'         => $reference,
            'billing_status'            => 'billed',
            'billing_reference_at'      => date('Y-m-d H:i:s'),
            'billing_status_updated_by' => $staff_id,
            'billing_status_updated_at' => date('Y-m-d H:i:s'),
        ));

        $this->add_log(array(
            'visit_id'      => $visit_id,
            'from_level_id' => $visit->current_level_id,
            'to_level_id'   => $visit->current_level_id,
            'action'        => 'billing_reference_generated',
            'remarks'       => $reference,
            'acted_by'      => $staff_id,
            'acted_role_id' => $role_id,
        ));

        return array('status' => true, 'message' => 'Billing reference generated: ' . $reference, 'reference' => $reference);
    }

    /**
     * Invoice number: INV-KTR-YYMMDD-###### (per-day running sequence starting
     * 000001) — same builder as generate_billing_reference(), but with an
     * INV- prefix. Day rollover is handled by the date prefix.
     */
    public function generate_invoice_no()
    {
        $prefix = 'INV-' . date('ymd') . '-';
        $this->db->select('invoice_no');
        $this->db->from('patient_flow_visits');
        $this->db->like('invoice_no', $prefix, 'after');
        $this->db->order_by('id', 'desc');
        $this->db->limit(1);
        $row = $this->db->get()->row();

        $seq = 1;
        if ($row) {
            $parts = explode('-', $row->invoice_no);
            $seq   = intval(end($parts)) + 1;
        }
        return $prefix . str_pad($seq, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Persist the invoice number on a visit: writes invoice_no + invoice_no_at
     * and logs the action. Does NOT touch billing_status. Idempotent — refuses
     * to overwrite an existing invoice number.
     *
     * @param int    $visit_id
     * @param string $invoice_no e.g. INV-KTR-260909-000001
     * @param int    $staff_id
     * @param int    $role_id
     * @return array ['status'=>bool, 'message'=>string]
     */
    public function set_invoice_no($visit_id, $invoice_no, $staff_id, $role_id = null)
    {
        $visit = $this->get_visit($visit_id);
        if (!$visit) {
            return array('status' => false, 'message' => 'Visit not found.');
        }
        if (!empty($visit->invoice_no)) {
            return array('status' => false, 'message' => 'An invoice number has already been generated for this visit.');
        }

        $this->db->where('id', (int) $visit_id);
        $this->db->update('patient_flow_visits', array(
            'invoice_no'   => $invoice_no,
            'invoice_no_at' => date('Y-m-d H:i:s'),
        ));

        $this->add_log(array(
            'visit_id'      => $visit_id,
            'from_level_id' => $visit->current_level_id,
            'to_level_id'   => $visit->current_level_id,
            'action'        => 'invoice_generated',
            'remarks'       => $invoice_no,
            'acted_by'      => $staff_id,
            'acted_role_id' => $role_id,
        ));

        return array('status' => true, 'message' => 'Invoice number generated: ' . $invoice_no, 'invoice_no' => $invoice_no);
    }

    /**
     * Update the billing status of a visit (R2 / billing counter management).
     * Validates against the allowed list and logs old -> new in remarks.
     *
     * @param int    $visit_id
     * @param string $status pending | billed | paid | cancelled
     * @param int    $staff_id
     * @param int    $role_id
     * @return array ['status'=>bool, 'message'=>string]
     */
    public function set_billing_status($visit_id, $status, $staff_id, $role_id = null)
    {
        $visit = $this->get_visit($visit_id);
        if (!$visit) {
            return array('status' => false, 'message' => 'Visit not found.');
        }
        if (!in_array($status, $this->billing_statuses(), true)) {
            return array('status' => false, 'message' => 'Invalid billing status.');
        }

        $old_status = $visit->billing_status;
        if ($old_status === $status) {
            return array('status' => false, 'message' => 'Billing status is already "' . $status . '".');
        }

        $this->db->where('id', (int) $visit_id);
        $this->db->update('patient_flow_visits', array(
            'billing_status'            => $status,
            'billing_status_updated_by' => $staff_id,
            'billing_status_updated_at' => date('Y-m-d H:i:s'),
        ));

        $this->add_log(array(
            'visit_id'      => $visit_id,
            'from_level_id' => $visit->current_level_id,
            'to_level_id'   => $visit->current_level_id,
            'action'        => 'billing_status_changed',
            'remarks'       => $old_status . ' -> ' . $status . (empty($visit->billing_reference) ? '' : ' (Ref: ' . $visit->billing_reference . ')'),
            'acted_by'      => $staff_id,
            'acted_role_id' => $role_id,
        ));

        return array('status' => true, 'message' => 'Billing status changed to "' . $status . '".');
    }

    /**
     * Role-scoped billing guard: the staff's role must own the visit's CURRENT
     * level AND that level must be the phase-1 level-2 billing counter (R2).
     * Identified by phase 'P1' + level_no 2 so the guard survives title/code edits.
     *
     * @param int   $visit_id
     * @param array $roleIds logged-in staff's role ids
     * @return bool
     */
    public function can_manage_billing($visit_id, $roleIds)
    {
        if (empty($roleIds)) {
            return false;
        }
        $visit = $this->get_visit($visit_id);
        if (!$visit || !$visit->current_level_id) {
            return false;
        }
        $level = $this->get($visit->current_level_id);
        if (!$level || $level->phase !== 'P1' || (int) $level->level_no !== 2) {
            return false;
        }
        return in_array((int) $level->role_id, array_map('intval', $roleIds), true);
    }
    
    /**
     * Get visit with full details including patient, staff, and level information
     * @param int $id Visit ID
     * @return object|null
     */
    public function get_visit_full_details($id)
    {
        $this->db->select('
            v.*,
            p.id AS patient_id,
            p.patient_name,
            p.patient_uuid,
            p.gender,
            p.age,
            p.mobileno,
            p.guardian_name,
            p.address,
            p.dob,
            p.email,
            p.blood_group,
            p.patient_uuid,
            p.identification_number,
            p.insurance_id,
            p.insurance_validity,
            p.known_allergies,
            p.is_active,
            p.created_at AS patient_created_at,
            l.id AS level_id,
            l.code AS level_code,
            l.title AS level_title,
            l.counter_type AS level_counter,
            l.level_no AS level_number,
            l.phase AS level_phase,
            l.can_approve AS level_can_approve,
            l.is_active AS level_is_active,
            pl.id AS next_level_id,
            pl.code AS next_code,
            pl.title AS next_title,
            pv.id AS prev_level_id,
            pv.code AS prev_code,
            pv.title AS prev_title,
            s.id AS staff_id,
            s.name AS staff_name,
            s.surname AS staff_surname,
            s.employee_id AS staff_employee_id
        ');
        $this->db->from('patient_flow_visits v');
        $this->db->join('patients p', 'p.id = v.patient_id', 'left');
        $this->db->join('patient_flow_levels l', 'l.id = v.current_level_id', 'left');
        $this->db->join('patient_flow_levels pl', 'pl.parent_id = l.id', 'left');
        $this->db->join('patient_flow_levels pv', 'pv.id = l.parent_id', 'left');
        $this->db->join('staff s', 's.id = v.created_by', 'left');
        $this->db->where('v.id', $id);
        $this->db->limit(1);
        
        $visit = $this->db->get()->row();
        
        // Ensure patient has UUID
        if ($visit && empty($visit->patient_uuid) && !empty($visit->patient_id)) {
            $visit->patient_uuid = $this->ensure_patient_uuid($visit->patient_id);
        }
        
        return $visit;
    }
    /**
     * Get patient's complete flow history
     */
    public function get_patient_flow_history($patient_id, $limit = 10)
    {
        $this->db->select('v.*, l.code AS level_code, l.title AS level_title');
        $this->db->from('patient_flow_visits v');
        $this->db->join('patient_flow_levels l', 'l.id = v.current_level_id', 'left');
        $this->db->where('v.patient_id', (int)$patient_id);
        $this->db->order_by('v.id', 'desc');
        $this->db->limit($limit);
        return $this->db->get()->result();
    }

    /**
     * Get visit with all related data
     */
    public function get_visit_with_all_relations($id)
    {
        $visit = $this->get_visit($id);
        if (!$visit) {
            return null;
        }
        
        // Get patient details
        $this->db->select('*');
        $this->db->from('patients');
        $this->db->where('id', $visit->patient_id);
        $visit->patient_details = $this->db->get()->row();
        
        // Get OPD details
        if (!empty($visit->opd_id)) {
            $this->db->select('od.*, 
                            d.name AS doctor_name,
                            d.surname AS doctor_surname');
            $this->db->from('opd_details od');
            $this->db->join('staff d', 'd.id = od.doctor_id', 'left');
            $this->db->where('od.id', $visit->opd_id);
            $visit->opd_details = $this->db->get()->row();
            
            if ($visit->opd_details) {
                // Get OPD charges
                $this->db->select('*');
                $this->db->from('patient_charges');
                $this->db->where('opd_id', $visit->opd_id);
                $visit->opd_details->charges = $this->db->get()->result();
                
                // Calculate total
                $visit->opd_details->total_charges = $this->get_opd_net_amount($visit->opd_id);
            }
        }
        
        // Get logs
        $logs = $this->get_logs_by_visit_ids(array($id));
        $visit->logs = isset($logs[$id]) ? $logs[$id] : array();
        
        return $visit;
    }

    /**
     * Patient Flow Report: list all flow visits with optional filters
     * (patient name, mobile, visit no, visit status, payment status,
     * approve status, programme status, token status, current level, dates).
     */
    public function get_flow_report($filters = array())
    {
        $this->db->select("v.*, p.patient_name, p.mobileno, p.patient_uuid, p.gender, p.age");
        $this->db->select("l.code AS level_code, l.title AS level_title, l.level_no AS level_no", false);
        $this->db->from("patient_flow_visits v");
        $this->db->join("patients p", "p.id = v.patient_id", "left");
        $this->db->join("patient_flow_levels l", "l.id = v.current_level_id", "left");
        if (!empty($filters["patient_name"])) { $this->db->like("p.patient_name", $filters["patient_name"]); }
        if (!empty($filters["mobileno"])) { $this->db->like("p.mobileno", $filters["mobileno"]); }
        if (!empty($filters["visit_no"])) { $this->db->like("v.visit_no", $filters["visit_no"]); }
        if (!empty($filters["status"])) { $this->db->where("v.status", $filters["status"]); }
        if (!empty($filters["payment_status"])) { $this->db->where("v.payment_status", $filters["payment_status"]); }
        if (!empty($filters["approve_status"])) { $this->db->where("v.approve_status", $filters["approve_status"]); }
        if (!empty($filters["programme_status"])) { $this->db->where("v.programme_status", $filters["programme_status"]); }
        if (!empty($filters["token_status"])) { $this->db->where("v.token_status", $filters["token_status"]); }
        if (!empty($filters["level_id"])) { $this->db->where("v.current_level_id", (int) $filters["level_id"]); }
        if (!empty($filters["date_from"])) { $this->db->where("DATE(v.created_at) >=", $filters["date_from"]); }
        if (!empty($filters["date_to"])) { $this->db->where("DATE(v.created_at) <=", $filters["date_to"]); }
        $this->db->order_by("v.id", "desc");
        return $this->db->get()->result();
    }

    /**
     * Delete a patient-flow visit (hard delete) along with its logs.
     * The linked core OPD record is intentionally kept — it belongs to the
     * OPD module. Returns true when the visit row was actually deleted.
     */
    public function delete_visit($visit_id)
    {
        $visit_id = (int) $visit_id;
        if (!$visit_id) {
            return false;
        }
        $this->db->trans_start();
        $this->db->trans_strict(false);
        $this->db->where("visit_id", $visit_id);
        $this->db->delete("patient_flow_visit_logs");
        $this->db->where("id", $visit_id);
        $this->db->delete("patient_flow_visits");
        $deleted = ($this->db->affected_rows() > 0);
        $this->db->trans_complete();
        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            return false;
        }
        return $deleted;
    }

    /**
     * Case-taking detail columns: name => (kind, DDL). Single source used by
     * the patient_flow_visits migrations and the set_case_taking() whitelist.
     *
     * kind: 'text' (stored as trimmed string / NULL) | 'int' (FK id / NULL)
     */
    public function case_taking_columns()
    {
        $cols = array(
            'ct_sex' => array('kind' => 'text', 'ddl' => "VARCHAR(1) NULL DEFAULT NULL COMMENT 'M or F'"),
            'ct_primary_concern' => array('kind' => 'text', 'ddl' => 'VARCHAR(255) NULL DEFAULT NULL'),
            'ct_concern_duration' => array('kind' => 'text', 'ddl' => 'VARCHAR(100) NULL DEFAULT NULL'),
            'ct_primary_system' => array('kind' => 'text', 'ddl' => 'VARCHAR(100) NULL DEFAULT NULL'),
            'ct_dominant_nature' => array('kind' => 'text', 'ddl' => 'VARCHAR(100) NULL DEFAULT NULL'),
        );
        // Constitution: ONE body build + ONE face shape + ONE nose type per patient.
        // The masters themselves are numbered (BOD1..BOD4 = Lean/Medium/Broad/Full),
        // so a patient describes himself with a single pick from each master.
        $refs = array(
            'bod' => 'constitution_body_build',
            'fsh' => 'constitution_face_shape',
            'nos' => 'constitution_nose_type',
        );
        foreach ($refs as $pfx => $ref) {
            $cols['ct_' . $pfx . '1'] = array('kind' => 'int', 'ddl' => "INT NULL DEFAULT NULL COMMENT '-> $ref.id'");
        }
        $cols['ct_constitution_code'] = array('kind' => 'text', 'ddl' => "VARCHAR(30) NULL DEFAULT NULL COMMENT 'constitution_master.code resolved from gender + BOD/FSH/NOS'");
        $cols['ct_assessment_certainty'] = array('kind' => 'text', 'ddl' => 'VARCHAR(50) NULL DEFAULT NULL');
        $cols['ct_status'] = array('kind' => 'text', 'ddl' => "VARCHAR(20) NULL DEFAULT NULL COMMENT 'Open | In Review | Completed'");
        $cols['ct_tissue_structure'] = array('kind' => 'text', 'ddl' => 'VARCHAR(255) NULL DEFAULT NULL');
        $cols['ct_report_info'] = array('kind' => 'text', 'ddl' => 'TEXT NULL DEFAULT NULL');
        $cols['ct_review_reason'] = array('kind' => 'text', 'ddl' => 'TEXT NULL DEFAULT NULL');
        $cols['ct_secondary_concerns'] = array('kind' => 'text', 'ddl' => 'TEXT NULL DEFAULT NULL');
        $cols['ct_additional_note'] = array('kind' => 'text', 'ddl' => 'TEXT NULL DEFAULT NULL');
        $cols['ct_previous_treatment'] = array('kind' => 'text', 'ddl' => 'TEXT NULL DEFAULT NULL');
        $cols['ct_programme_suggestion'] = array('kind' => 'text', 'ddl' => 'TEXT NULL DEFAULT NULL');
        $cols['ct_programme_id'] = array('kind' => 'text', 'ddl' => "VARCHAR(20) NULL DEFAULT NULL COMMENT '-> patient_flow_programme.code'");
        return $cols;
    }

    /**
     * Build the column => value map for a case-taking write.
     *
     * Shared by the initial save (set_case_taking) and the edit flow
     * (update_case_taking) so both paths whitelist, trim and derive the
     * constitution code in exactly the same way.
     *
     * $extra keys are whitelisted against case_taking_columns() and only
     * written when the column exists, so older databases keep working.
     *
     * @param int    $visit_id
     * @param string $type
     * @param string $description
     * @param int    $staff_id
     * @param array  $extra
     * @return array column => value
     */
    private function build_case_taking_update($visit_id, $type, $description, $staff_id, $extra = array())
    {
        $visit_id = (int) $visit_id;
        $update = array(
            'case_taking_type'        => $type,
            'case_taking_description' => $description,
            'case_taking_at'         => date('Y-m-d H:i:s'),
            'case_taking_by'         => (int) $staff_id,
        );
        if (is_array($extra)) {
            foreach ($this->case_taking_columns() as $name => $col) {
                if (!array_key_exists($name, $extra)
                    || !$this->db->field_exists($name, 'patient_flow_visits')) {
                    continue;
                }
                if ($col['kind'] === 'int') {
                    $val = (int) $extra[$name];
                    $update[$name] = ($val > 0) ? $val : null;
                } else {
                    $val = trim((string) $extra[$name]);
                    $update[$name] = ($val === '') ? null : $val;
                }
            }
        }

        // Constitution code is derived, never posted: resolve it from the saved
        // sex + body build + face shape + nose type of this case taking.
        // Fall back to the values already stored on the visit for any field
        // that was not part of this update.
        if ($this->db->field_exists('ct_constitution_code', 'patient_flow_visits')) {
            $current = $this->db->select('ct_sex, ct_bod1, ct_fsh1, ct_nos1')
                ->where('id', $visit_id)->get('patient_flow_visits')->row();
            $pick = function ($field, $update) use ($current) {
                if (array_key_exists($field, $update)) {
                    return $update[$field];
                }
                return ($current && isset($current->$field)) ? $current->$field : null;
            };
            $sex = (string) $pick('ct_sex', $update);
            $bod = (int) $pick('ct_bod1', $update);
            $fsh = (int) $pick('ct_fsh1', $update);
            $nos = (int) $pick('ct_nos1', $update);
            $update['ct_constitution_code'] = $this->get_constitution_code($sex, $bod, $fsh, $nos);
        }

        return $update;
    }

    /**
     * Set / update the case-taking details of a visit (type + description +
     * the case-taking detail columns passed in $extra).
     *
     * $extra keys are whitelisted against case_taking_columns() and only
     * written when the column exists, so older databases keep working.
     */
    public function set_case_taking($visit_id, $type, $description, $staff_id, $extra = array())
    {
        $visit_id = (int) $visit_id;
        if (!$visit_id) {
            return array('status' => false, 'message' => 'Visit not found.');
        }
        $update = $this->build_case_taking_update($visit_id, $type, $description, $staff_id, $extra);

        $this->db->where('id', $visit_id);
        $this->db->update('patient_flow_visits', $update);
        return array('status' => true, 'message' => 'Case taking saved successfully.');
    }

    /**
     * Edit an existing case taking.
     *
     * Unlike set_case_taking() (the initial take), an edit keeps the original
     * "taken by" / "taken at" stamp intact so the audit trail still shows who
     * actually took the case, and records a separate log row for the edit.
     *
     * @param int      $visit_id
     * @param string   $type
     * @param string   $description
     * @param int      $staff_id   staff performing the edit
     * @param array    $extra
     * @param int|null $role_id    role of the acting staff (for the log row)
     * @param int|null $level_id   current level of the visit (for the log row)
     */
    public function update_case_taking($visit_id, $type, $description, $staff_id, $extra = array(), $role_id = null, $level_id = null)
    {
        $visit_id = (int) $visit_id;
        if (!$visit_id) {
            return array('status' => false, 'message' => 'Visit not found.');
        }

        $this->db->trans_start();
        $this->db->trans_strict(false);

        $update = $this->build_case_taking_update($visit_id, $type, $description, $staff_id, $extra);
        // Keep the original author and timestamp of the case taking.
        unset($update['case_taking_by'], $update['case_taking_at']);

        $this->db->where('id', $visit_id);
        $this->db->update('patient_flow_visits', $update);

        // Audit trail: who edited the case, and when.
        $this->db->insert('patient_flow_visit_logs', array(
            'visit_id'      => $visit_id,
            'from_level_id' => $level_id,
            'to_level_id'   => $level_id,
            'action'        => 'case_taking_edit',
            'remarks'       => 'Case taking details updated.',
            'acted_by'      => (int) $staff_id,
            'acted_role_id' => $role_id ? (int) $role_id : null,
        ));

        $this->db->trans_complete();
        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            return array('status' => false, 'message' => 'Database error while updating case taking.');
        }

        return array('status' => true, 'message' => 'Case taking updated successfully.');
    }

    /* Dynamic constitution lookups + master. */

    private function constitution_lookup_table($key)
    {
        $map = array(
            'body' => 'constitution_body_build',
            'face' => 'constitution_face_shape',
            'nose' => 'constitution_nose_type',
        );
        return isset($map[$key]) ? $map[$key] : null;
    }

    public function get_constitution_lookups($key, $only_active = false)
    {
        $table = $this->constitution_lookup_table($key);
        if (!$table || !$this->db->table_exists($table)) {
            return array();
        }
        if ($only_active) {
            $this->db->where('is_active', 1);
        }
        $this->db->from($table);
        $this->db->order_by('sort_order', 'asc');
        $this->db->order_by('id', 'asc');
        return $this->db->get()->result();
    }

    public function get_constitution_lookup($key, $id)
    {
        $table = $this->constitution_lookup_table($key);
        if (!$table || !$this->db->table_exists($table)) {
            return null;
        }
        return $this->db->where('id', (int) $id)->get($table)->row();
    }

    public function save_constitution_lookup($key, $id, $data)
    {
        $table = $this->constitution_lookup_table($key);
        if (!$table) {
            return array('status' => false, 'message' => 'Invalid lookup type.');
        }
        $code = isset($data['code']) ? strtoupper(trim((string) $data['code'])) : '';
        if ($code === '') {
            return array('status' => false, 'message' => 'Code is required.');
        }
        $data['code'] = $code;
        $dup = $this->db->where('code', $code);
        if ($id > 0) {
            $dup->where('id !=', (int) $id);
        }
        if ($dup->get($table)->row()) {
            return array('status' => false, 'message' => 'Code already exists.');
        }
        if ($id > 0) {
            $this->db->where('id', (int) $id)->update($table, $data);
            return array('status' => true, 'message' => 'Updated.', 'id' => (int) $id);
        }
        $this->db->insert($table, $data);
        return array('status' => true, 'message' => 'Added.', 'id' => $this->db->insert_id());
    }

    public function delete_constitution_lookup($key, $id)
    {
        $table = $this->constitution_lookup_table($key);
        if (!$table) {
            return array('status' => false, 'message' => 'Invalid lookup type.');
        }
        $id = (int) $id;
        if ($this->db->table_exists('constitution_master') && $id > 0) {
            if ($key === 'body') { $col = 'body_build_id'; }
            elseif ($key === 'face') { $col = 'face_shape_id'; }
            else { $col = 'nose_type_id'; }
            if ($this->db->where($col, $id)->get('constitution_master')->row()) {
                return array('status' => false, 'message' => 'In use by master.');
            }
        }
        $this->db->where('id', $id)->delete($table);
        return array('status' => true, 'message' => 'Deleted.');
    }

    /** Look up the constitution_master.code for a given gender + body/face/nose combination.
     * Returns the constitution code string, or null if no matching master row exists.
     */
    public function get_constitution_code($gender, $body_build_id, $face_shape_id, $nose_type_id)
    {
        if (!$this->db->table_exists('constitution_master')) {
            return null;
        }
        $gender = strtoupper(trim((string) $gender));
        if ($gender === 'M') { $gender = 'Male'; }
        elseif ($gender === 'F') { $gender = 'Female'; }
        $body_build_id = (int) $body_build_id;
        $face_shape_id = (int) $face_shape_id;
        $nose_type_id = (int) $nose_type_id;
        if ($gender === '' || $body_build_id <= 0 || $face_shape_id <= 0 || $nose_type_id <= 0) {
            return null;
        }
        $row = $this->db
            ->where('gender', $gender)
            ->where('body_build_id', $body_build_id)
            ->where('face_shape_id', $face_shape_id)
            ->where('nose_type_id', $nose_type_id)
            ->get('constitution_master')
            ->row();
        return $row ? (string) $row->code : null;
    }

    /* Settings key-value + constitution master. */

    public function get_settings_assoc()
    {
        $defaults = array(
            'visit_prefix' => 'PF',
            'token_prefix' => 'T',
            'billing_prefix' => 'BR',
            'invoice_prefix' => 'INV',
            'next_visit_no' => '1',
            'terms_and_conditions' => '',
            'flow_note' => '',
        );
        if (!$this->db->table_exists('patient_flow_settings')) {
            return $defaults;
        }
        $rows = $this->db->get('patient_flow_settings')->result();
        foreach ($rows as $r) {
            $defaults[$r->setting_key] = $r->setting_value;
        }
        return $defaults;
    }

    public function save_settings($payload)
    {
        foreach ((array) $payload as $k => $v) {
            $exists = $this->db->where('setting_key', $k)->get('patient_flow_settings')->row();
            if ($exists) {
                $this->db->where('setting_key', $k)->update('patient_flow_settings', array('setting_value' => $v));
            } else {
                $this->db->insert('patient_flow_settings', array('setting_key' => $k, 'setting_value' => $v));
            }
        }
        return true;
    }

    public function get_constitutions()
    {
        if (!$this->db->table_exists('patient_flow_constitutions')) {
            return array();
        }
        $this->db->from('patient_flow_constitutions');
        $this->db->order_by('id', 'desc');
        return $this->db->get()->result();
    }

    public function get_constitution($id)
    {
        if (!$this->db->table_exists('patient_flow_constitutions')) {
            return null;
        }
        return $this->db->where('id', (int) $id)->get('patient_flow_constitutions')->row();
    }

    public function add_constitution($data)
    {
        $this->db->insert('patient_flow_constitutions', $data);
        return $this->db->insert_id();
    }

    public function update_constitution($id, $data)
    {
        $this->db->where('id', (int) $id);
        $this->db->update('patient_flow_constitutions', $data);
    }

    public function delete_constitution($id)
    {
        $this->db->where('id', (int) $id);
        $this->db->delete('patient_flow_constitutions');
    }

    public function get_constitution_list()
    {
        if (!$this->db->table_exists('constitution_master')) {
            return array();
        }
        $this->db->select('m.*, b.code AS bcode, b.name AS bname, b.variant AS bvariant, f.code AS fcode, f.name AS fname, n.code AS ncode, n.name AS nname');
        $this->db->from('constitution_master m');
        $this->db->join('constitution_body_build b', 'b.id = m.body_build_id', 'left');
        $this->db->join('constitution_face_shape f', 'f.id = m.face_shape_id', 'left');
        $this->db->join('constitution_nose_type n', 'n.id = m.nose_type_id', 'left');
        $this->db->order_by('m.id', 'desc');
        return $this->db->get()->result();
    }

    public function get_constitution_master($id)
    {
        if (!$this->db->table_exists('constitution_master')) {
            return null;
        }
        return $this->db->where('id', (int) $id)->get('constitution_master')->row();
    }

    public function save_constitution_master($id, $gender, $body_id, $face_id, $nose_id, $extra)
    {
        $gender = ($gender === 'Female') ? 'Female' : 'Male';
        $body = $this->db->where('id', (int) $body_id)->get('constitution_body_build')->row();
        $face = $this->db->where('id', (int) $face_id)->get('constitution_face_shape')->row();
        $nose = $this->db->where('id', (int) $nose_id)->get('constitution_nose_type')->row();
        if (!$body || !$face || !$nose) {
            return array('status' => false, 'message' => 'Select valid Body, Face and Nose.');
        }
        $prefix = ($gender === 'Female') ? 'F' : 'M';
        $code = $prefix . '-' . $body->code . '-' . $face->code . '-' . $nose->code;
        $dup = $this->db->where('gender', $gender)->where('body_build_id', (int) $body_id)->where('face_shape_id', (int) $face_id)->where('nose_type_id', (int) $nose_id);
        if ($id > 0) {
            $dup->where('id !=', (int) $id);
        }
        if ($dup->get('constitution_master')->row()) {
            return array('status' => false, 'message' => 'Combo exists (' . $code . ').');
        }
        $payload = array(
            'code' => $code,
            'gender' => $gender,
            'body_build_id' => (int) $body_id,
            'face_shape_id' => (int) $face_id,
            'nose_type_id' => (int) $nose_id,
            'variant' => (int) $body->variant,
            'description' => isset($extra['description']) ? $extra['description'] : null,
            'is_active' => isset($extra['is_active']) ? (int) $extra['is_active'] : 1,
        );
        if ($id > 0) {
            $this->db->where('id', (int) $id)->update('constitution_master', $payload);
            return array('status' => true, 'message' => 'Updated (' . $code . ').', 'code' => $code);
        }
        $this->db->insert('constitution_master', $payload);
        return array('status' => true, 'message' => 'Added (' . $code . ').', 'code' => $code);
    }

    public function delete_constitution_master($id)
    {
        $this->db->where('id', (int) $id);
        $this->db->delete('constitution_master');
    }

    public function get_programmes($only_active = false)
    {
        if (!$this->db->table_exists('programme_master')) {
            return array();
        }
        if ($only_active) {
            $this->db->where('is_active', 1);
        }
        $this->db->from('programme_master');
        $this->db->order_by('sort_order', 'asc');
        $this->db->order_by('id', 'asc');
        return $this->db->get()->result();
    }

    public function save_programme($id, $data)
    {
        $code = strtoupper(trim((string) $data['code']));
        $data['code'] = $code;
        $dup = $this->db->where('code', $code);
        if ($id > 0) {
            $dup->where('id !=', (int) $id);
        }
        if ($dup->get('programme_master')->row()) {
            return array('status' => false, 'message' => 'Programme code exists.');
        }
        if ($id > 0) {
            $this->db->where('id', (int) $id)->update('programme_master', $data);
            return array('status' => true, 'message' => 'Programme updated.');
        }
        $this->db->insert('programme_master', $data);
        return array('status' => true, 'message' => 'Programme added.');
    }

    public function delete_programme($id)
    {
        $id = (int) $id;
        if ($this->db->table_exists('programme_subcategory')
            && $this->db->where('programme_id', $id)->get('programme_subcategory')->row()) {
            return array('status' => false, 'message' => 'In use by subcategory.');
        }
        $this->db->where('id', $id)->delete('programme_master');
        return array('status' => true, 'message' => 'Programme deleted.');
    }

    public function get_subcategories()
    {
        if (!$this->db->table_exists('programme_subcategory')) {
            return array();
        }
        $this->db->select('s.*, p.code AS pcode, p.name AS pname');
        $this->db->from('programme_subcategory s');
        $this->db->join('programme_master p', 'p.id = s.programme_id', 'left');
        $this->db->order_by('s.id', 'desc');
        return $this->db->get()->result();
    }

    /**
     * Get programme by ID.
     */
    public function get_programme_by_id($programme_id)
    {
        if (!$this->db->table_exists('patient_flow_programme')) {
            return null;
        }
        return $this->db->where('id', (int) $programme_id)->get('patient_flow_programme')->row();
    }

    /**
     * Get all active programmes for enrolment dropdown.
     */
    public function get_programme_enrolments()
    {
        if (!$this->db->table_exists('patient_flow_programme')) {
            return array();
        }
        $this->db->where('is_active', 1);
        $this->db->order_by('code', 'asc');
        return $this->db->get('patient_flow_programme')->result();
    }

    /**
     * Get all programme groups.
     */
    public function get_programme_groups($only_active = false)
    {
        if (!$this->db->table_exists('patient_flow_programme_group')) {
            return array();
        }
        if ($only_active) {
            $this->db->where('is_active', 1);
        }
        $this->db->order_by('code', 'asc');
        return $this->db->get('patient_flow_programme_group')->result();
    }

    /**
     * Get a single programme group by ID.
     */
    public function get_programme_group_by_id($id)
    {
        if (!$this->db->table_exists('patient_flow_programme_group')) {
            return null;
        }
        return $this->db->where('id', (int) $id)->get('patient_flow_programme_group')->row();
    }

    /**
     * Save a programme group (add or update).
     */
    public function save_programme_group($id, $data)
    {
        if (!$this->db->table_exists('patient_flow_programme_group')) {
            return array('status' => false, 'message' => 'Programme group table not found.');
        }
        $id = (int) $id;
        $code = strtoupper(trim((string) $data['code']));
        $name = trim((string) $data['name']);
        $coverage_model = trim((string) $data['coverage_model']);
        $is_active = ((string) ($data['is_active'] ?? '1') === '0') ? 0 : 1;

        if ($code === '' || $name === '' || $coverage_model === '') {
            return array('status' => false, 'message' => 'Code, name and coverage model are required.');
        }

        // Check duplicate code
        $dup = $this->db->where('code', $code);
        if ($id > 0) {
            $dup->where('id !=', $id);
        }
        if ($dup->get('patient_flow_programme_group')->row()) {
            return array('status' => false, 'message' => 'Group code already exists.');
        }

        $save_data = array(
            'code' => $code,
            'name' => $name,
            'coverage_model' => $coverage_model,
            'is_active' => $is_active,
        );

        if ($id > 0) {
            $this->db->where('id', $id)->update('patient_flow_programme_group', $save_data);
            return array('status' => true, 'message' => 'Programme group updated.');
        }
        $this->db->insert('patient_flow_programme_group', $save_data);
        return array('status' => true, 'message' => 'Programme group added.', 'id' => (int) $this->db->insert_id());
    }

    /**
     * Delete a programme group.
     */
    public function delete_programme_group($id)
    {
        if (!$this->db->table_exists('patient_flow_programme_group')) {
            return array('status' => false, 'message' => 'Programme group table not found.');
        }
        $id = (int) $id;
        $row = $this->db->where('id', $id)->get('patient_flow_programme_group')->row();
        if (!$row) {
            return array('status' => false, 'message' => 'Programme group not found.');
        }
        // Check if programmes reference this group
        $this->db->where('group_id', $id);
        $count = $this->db->count_all_results('patient_flow_programme');
        if ($count > 0) {
            return array('status' => false, 'message' => 'Cannot delete: ' . $count . ' programme(s) still reference this group.');
        }
        $this->db->where('id', $id)->delete('patient_flow_programme_group');
        return array('status' => true, 'message' => 'Programme group deleted.');
    }

    public function save_subcategory($id, $data)
    {
        $data['code'] = strtoupper(trim((string) $data['code']));
        $dup = $this->db->where('code', $data['code']);
        if ($id > 0) {
            $dup->where('id !=', (int) $id);
        }
        if ($dup->get('programme_subcategory')->row()) {
            return array('status' => false, 'message' => 'Subcategory code exists.');
        }
        if ($id > 0) {
            $this->db->where('id', (int) $id)->update('programme_subcategory', $data);
            return array('status' => true, 'message' => 'Subcategory updated.');
        }
        $this->db->insert('programme_subcategory', $data);
        return array('status' => true, 'message' => 'Subcategory added.');
    }

    public function delete_subcategory($id)
    {
        $this->db->where('id', (int) $id)->delete('programme_subcategory');
        return array('status' => true, 'message' => 'Subcategory deleted.');
    }

    // ============ Step 4: Approval Workflows ============

    /**
     * Get active fee directors.
     */
    public function get_fee_directors()
    {
        if (!$this->db->table_exists('patient_flow_fee_director')) {
            return array();
        }
        $this->db->where('is_active', 1);
        return $this->db->get('patient_flow_fee_director')->result();
    }

    /**
     * Check if a staff member is an active fee director.
     */
    public function is_fee_director($staff_id)
    {
        if (!$this->db->table_exists('patient_flow_fee_director')) {
            return false;
        }
        $row = $this->db->where('staff_id', (int) $staff_id)
            ->where('is_active', 1)
            ->get('patient_flow_fee_director')
            ->row();
        return (bool) $row;
    }

    /**
     * Write an audit log entry for fee/master changes and approvals.
     */
    public function write_fee_audit_log($entity, $entity_id, $action, $old_json, $new_json, $user_id)
    {
        if (!$this->db->table_exists('patient_flow_fee_audit_log')) {
            return false;
        }
        $this->db->insert('patient_flow_fee_audit_log', array(
            'entity'     => $entity,
            'entity_id'  => (int) $entity_id,
            'action'     => $action,
            'old_json'   => $old_json,
            'new_json'   => $new_json,
            'user_id'    => (int) $user_id,
            'created_at' => date('Y-m-d H:i:s'),
        ));
        return true;
    }

    /**
     * Request assistance: any junior creates a PENDING assistance request.
     */
    public function save_assistance_request($enrollment_id, $category_id, $requested_by, $reason)
    {
        if (!$this->db->table_exists('patient_flow_fee_assistance_request')) {
            return array('status' => false, 'message' => 'Assistance request table not found.');
        }
        $enrollment_id = (int) $enrollment_id;
        $category_id   = (int) $category_id;
        $requested_by  = (int) $requested_by;
        if ($enrollment_id <= 0 || $category_id <= 0 || $requested_by <= 0) {
            return array('status' => false, 'message' => 'Invalid parameters.');
        }
        // Verify enrollment exists
        $enr = $this->db->where('id', $enrollment_id)->get('patient_flow_patient_programme_enrollment')->row();
        if (!$enr) {
            return array('status' => false, 'message' => 'Enrollment not found.');
        }
        // Verify category exists and is active
        $cat = $this->db->where('id', $category_id)->where('is_active', 1)
            ->get('patient_flow_fee_assistance_category')->row();
        if (!$cat) {
            return array('status' => false, 'message' => 'Assistance category not found.');
        }
        $this->db->insert('patient_flow_fee_assistance_request', array(
            'enrollment_id' => $enrollment_id,
            'category_id'   => $category_id,
            'requested_by'  => $requested_by,
            'requested_at'  => date('Y-m-d H:i:s'),
            'reason'        => trim((string) $reason),
            'status'        => 'PENDING',
        ));
        $id = (int) $this->db->insert_id();
        $this->write_fee_audit_log('fee_assistance_request', $id, 'request', null,
            json_encode(array('category_id' => $category_id, 'reason' => $reason)), $requested_by);
        return array('status' => true, 'message' => 'Assistance request submitted.', 'id' => $id);
    }

    /**
     * Approve/reject assistance request: one active fee_director, not the requester.
     */
    public function approve_assistance_request($request_id, $director_id, $approve, $decision_note)
    {
        if (!$this->db->table_exists('patient_flow_fee_assistance_request')) {
            return array('status' => false, 'message' => 'Assistance request table not found.');
        }
        if (!$this->is_fee_director($director_id)) {
            return array('status' => false, 'message' => 'Only an active fee director can approve/reject assistance.');
        }
        $request_id = (int) $request_id;
        $director_id = (int) $director_id;
        $req = $this->db->where('id', $request_id)->get('patient_flow_fee_assistance_request')->row();
        if (!$req) {
            return array('status' => false, 'message' => 'Assistance request not found.');
        }
        if ($req->requested_by === $director_id) {
            return array('status' => false, 'message' => 'A director cannot approve their own request.');
        }
        if ($req->status !== 'PENDING') {
            return array('status' => false, 'message' => 'Request already decided.');
        }
        $new_status = $approve ? 'APPROVED' : 'REJECTED';
        $this->db->where('id', $request_id)->update('patient_flow_fee_assistance_request', array(
            'status'          => $new_status,
            'decided_by'      => $director_id,
            'decided_at'      => date('Y-m-d H:i:s'),
            'decision_note'   => trim((string) $decision_note),
        ));
        $this->write_fee_audit_log('fee_assistance_request', $request_id, $approve ? 'approve' : 'reject',
            json_encode(array('status' => 'PENDING')), json_encode(array('status' => $new_status, 'decision_note' => $decision_note)), $director_id);
        return array('status' => true, 'message' => 'Assistance request ' . strtolower($new_status) . '.');
    }

    /**
     * Save discount campaign: Admin or Director creates DRAFT campaign.
     */
    public function save_discount_campaign($id, $data, $staff_id)
    {
        if (!$this->db->table_exists('patient_flow_discount_campaign')) {
            return array('status' => false, 'message' => 'Discount campaign table not found.');
        }
        $id = (int) $id;
        $code = strtoupper(trim((string) ($data['campaign_code'] ?? '')));
        if ($code === '') {
            return array('status' => false, 'message' => 'Campaign code is required.');
        }
        $dup = $this->db->where('campaign_code', $code);
        if ($id > 0) {
            $dup->where('id !=', $id);
        }
        if ($dup->get('patient_flow_discount_campaign')->row()) {
            return array('status' => false, 'message' => 'Campaign code exists.');
        }
        $save = array(
            'campaign_code'   => $code,
            'reason'          => trim((string) ($data['reason'] ?? '')),
            'discount_percent'=> (float) ($data['discount_percent'] ?? 0),
            'start_date'      => $data['start_date'],
            'end_date'        => $data['end_date'],
            'benefit_quarters'=> (int) ($data['benefit_quarters'] ?? 2),
            'allow_stacking'  => ((string) ($data['allow_stacking'] ?? '0') === '1') ? 1 : 0,
            'payment_mode'    => trim((string) ($data['payment_mode'] ?? 'MILESTONE')),
            'status'          => 'DRAFT',
            'created_by'      => (int) $staff_id,
            'created_at'      => date('Y-m-d H:i:s'),
        );
        if ($id > 0) {
            $this->db->where('id', $id)->update('patient_flow_discount_campaign', $save);
            $this->write_fee_audit_log('discount_campaign', $id, 'update', null, json_encode($save), $staff_id);
            return array('status' => true, 'message' => 'Campaign updated.');
        }
        $this->db->insert('patient_flow_discount_campaign', $save);
        $cid = (int) $this->db->insert_id();
        $this->write_fee_audit_log('discount_campaign', $cid, 'create', null, json_encode($save), $staff_id);
        return array('status' => true, 'message' => 'Campaign created as DRAFT.', 'id' => $cid);
    }

    /**
     * Get a single discount campaign by ID.
     */
    public function get_discount_campaign_by_id($id)
    {
        if (!$this->db->table_exists('patient_flow_discount_campaign')) {
            return null;
        }
        return $this->db->where('id', (int) $id)->get('patient_flow_discount_campaign')->row();
    }

    /**
     * Get all discount campaigns.
     */
    public function get_discount_campaigns()
    {
        if (!$this->db->table_exists('patient_flow_discount_campaign')) {
            return [];
        }
        return $this->db->order_by('id', 'DESC')->get('patient_flow_discount_campaign')->result();
    }

    /**
     * Get all assistance categories.
     */
    public function get_assistance_categories()
    {
        if (!$this->db->table_exists('patient_flow_fee_assistance_category')) {
            return [];
        }
        return $this->db->order_by('id', 'DESC')->get('patient_flow_fee_assistance_category')->result();
    }

    /**
     * Delete a discount campaign.
     */
    public function delete_discount_campaign($id)
    {
        if (!$this->db->table_exists('patient_flow_discount_campaign')) {
            return array('status' => false, 'message' => 'Discount campaign table not found.');
        }
        $id = (int) $id;
        $this->db->where('id', $id)->delete('patient_flow_discount_campaign');
        $this->db->where('campaign_id', $id)->delete('patient_flow_discount_campaign_scope');
        return array('status' => true, 'message' => 'Campaign deleted.');
    }

    /**
     * Get a single assistance category by ID.
     */
    public function get_assistance_category_by_id($id)
    {
        if (!$this->db->table_exists('patient_flow_fee_assistance_category')) {
            return null;
        }
        return $this->db->where('id', (int) $id)->get('patient_flow_fee_assistance_category')->row();
    }

    /**
     * Save (insert or update) an assistance category.
     */
    public function save_assistance_category($id, $data)
    {
        if (!$this->db->table_exists('patient_flow_fee_assistance_category')) {
            return array('status' => false, 'message' => 'Assistance category table not found.');
        }
        $id = (int) $id;
        $group_id = (int) ($data['group_id'] ?? 0);
        $category_code = strtoupper(trim((string) ($data['category_code'] ?? '')));
        $description = trim((string) ($data['description'] ?? ''));
        $discount_percent = (float) ($data['discount_percent'] ?? 0);
        $validity_quarters = (int) ($data['validity_quarters'] ?? 2);
        $payment_mode = trim((string) ($data['payment_mode'] ?? 'STAGEWISE'));
        $is_active = isset($data['is_active']) ? ((string) $data['is_active'] === '1' ? 1 : 0) : 1;

        if ($category_code === '' || $group_id <= 0) {
            return array('status' => false, 'message' => 'Group and Category Code are required.');
        }

        $save = array(
            'group_id'          => $group_id,
            'category_code'     => $category_code,
            'discount_percent'  => $discount_percent,
            'validity_quarters' => $validity_quarters,
            'payment_mode'      => $payment_mode,
            'is_active'         => $is_active,
        );

        if ($id > 0) {
            $this->db->where('id', $id)->update('patient_flow_fee_assistance_category', $save);
            return array('status' => true, 'message' => 'Assistance category updated.');
        }

        // Check duplicate
        $existing = $this->db->where('group_id', $group_id)
            ->where('category_code', $category_code)
            ->get('patient_flow_fee_assistance_category')
            ->row();
        if ($existing) {
            return array('status' => false, 'message' => 'Category code already exists for this group.');
        }

        $this->db->insert('patient_flow_fee_assistance_category', $save);
        return array('status' => true, 'message' => 'Assistance category created.');
    }

    /**
     * Delete an assistance category.
     */
    public function delete_assistance_category($id)
    {
        if (!$this->db->table_exists('patient_flow_fee_assistance_category')) {
            return array('status' => false, 'message' => 'Assistance category table not found.');
        }
        $id = (int) $id;
        $this->db->where('id', $id)->delete('patient_flow_fee_assistance_category');
        return array('status' => true, 'message' => 'Assistance category deleted.');
    }

    /**
     * Toggle assistance category active status.
     */
    public function toggle_assistance_category($id)
    {
        if (!$this->db->table_exists('patient_flow_fee_assistance_category')) {
            return array('status' => false, 'message' => 'Assistance category table not found.');
        }
        $id = (int) $id;
        $row = $this->db->where('id', $id)->get('patient_flow_fee_assistance_category')->row();
        if (!$row) {
            return array('status' => false, 'message' => 'Assistance category not found.');
        }
        $new_status = ((int) $row->is_active === 1) ? 0 : 1;
        $this->db->where('id', $id)->update('patient_flow_fee_assistance_category', array('is_active' => $new_status));
        return array('status' => true, 'message' => 'Assistance category ' . ($new_status ? 'activated' : 'deactivated') . '.');
    }

    /**
     * Approve/reject campaign: one active Director.
     */
    public function approve_campaign($campaign_id, $director_id, $approve)
    {
        if (!$this->db->table_exists('patient_flow_discount_campaign')) {
            return array('status' => false, 'message' => 'Discount campaign table not found.');
        }
        $campaign_id = (int) $campaign_id;
        $director_id = (int) $director_id;
        $row = $this->db->where('id', $campaign_id)->get('patient_flow_discount_campaign')->row();
        if (!$row) {
            return array('status' => false, 'message' => 'Campaign not found.');
        }
        if ($row->status !== 'DRAFT') {
            return array('status' => false, 'message' => 'Campaign is not in DRAFT status.');
        }
        $new_status = $approve ? 'APPROVED' : 'REJECTED';
        $update = array('status' => $new_status);
        if ($approve) {
            $update['approved_by'] = $director_id;
            $update['approved_at'] = date('Y-m-d H:i:s');
        }
        $this->db->where('id', $campaign_id)->update('patient_flow_discount_campaign', $update);
        $this->write_fee_audit_log('discount_campaign', $campaign_id, $approve ? 'approve' : 'reject',
            json_encode(array('status' => 'DRAFT')), json_encode(array('status' => $new_status)), $director_id);
        return array('status' => true, 'message' => 'Campaign ' . strtolower($new_status) . '.');
    }

    /**
     * Set stage-wise approval for a campaign patient: Director sets stagewise_approved_by on benefit.
     */
    public function set_stagewise_approval($enrollment_id, $director_id, $approve)
    {
        if (!$this->db->table_exists('patient_flow_enrollment_benefit')) {
            return array('status' => false, 'message' => 'Enrollment benefit table not found.');
        }
        if (!$this->is_fee_director($director_id)) {
            return array('status' => false, 'message' => 'Only an active fee director can approve stage-wise payment.');
        }
        $enrollment_id = (int) $enrollment_id;
        $director_id   = (int) $director_id;
        $benefit = $this->db->where('enrollment_id', $enrollment_id)
            ->where('is_current', 1)
            ->get('patient_flow_enrollment_benefit')->row();
        if (!$benefit) {
            return array('status' => false, 'message' => 'No current benefit found for this enrollment.');
        }
        if ($benefit->source !== 'CAMPAIGN') {
            return array('status' => false, 'message' => 'Stage-wise approval applies only to campaign patients.');
        }
        $this->db->where('enrollment_id', $enrollment_id)->where('is_current', 1)
            ->update('patient_flow_enrollment_benefit', array(
                'stagewise_approved_by' => $approve ? $director_id : null,
                'stagewise_approved_at' => $approve ? date('Y-m-d H:i:s') : null,
            ));
        $this->write_fee_audit_log('enrollment_benefit', $enrollment_id, $approve ? 'stagewise_approve' : 'stagewise_revoke',
            null, json_encode(array('stagewise_approved_by' => $approve ? $director_id : null)), $director_id);
        return array('status' => true, 'message' => 'Stage-wise approval ' . ($approve ? 'granted' : 'revoked') . '.');
    }

    /**
     * Mark payment schedule row as PAID: Reception collects payment.
     */
    public function mark_payment_paid($schedule_id, $staff_id, $payment_ref)
    {
        if (!$this->db->table_exists('patient_flow_programme_payment_schedule')) {
            return array('status' => false, 'message' => 'Payment schedule table not found.');
        }
        $schedule_id = (int) $schedule_id;
        $staff_id    = (int) $staff_id;
        $row = $this->db->where('id', $schedule_id)->get('patient_flow_programme_payment_schedule')->row();
        if (!$row) {
            return array('status' => false, 'message' => 'Payment schedule row not found.');
        }
        if ($row->status !== 'PENDING') {
            return array('status' => false, 'message' => 'Schedule row is not pending.');
        }
        $this->db->where('id', $schedule_id)->update('patient_flow_programme_payment_schedule', array(
            'status'       => 'PAID',
            'paid_at'      => date('Y-m-d H:i:s'),
            'payment_ref'  => trim((string) $payment_ref),
        ));
        $this->write_fee_audit_log('programme_payment_schedule', $schedule_id, 'pay',
            json_encode(array('status' => 'PENDING')), json_encode(array('status' => 'PAID', 'payment_ref' => $payment_ref)), $staff_id);
        return array('status' => true, 'message' => 'Payment marked PAID.');
    }

    public function get_stages()
    {
        if (!$this->db->table_exists('stage_master')) {
            return array();
        }
        $this->db->from('stage_master');
        $this->db->order_by('sort_order', 'asc');
        $this->db->order_by('id', 'asc');
        return $this->db->get()->result();
    }

    public function save_stage($id, $quarter, $stage, $days, $rule, $active, $patient_enrolled = 0)
    {
        $quarter = strtoupper(trim((string) $quarter));
        $stage = strtoupper(trim((string) $stage));
        $code = $quarter . $stage;
        $dup = $this->db->where('quarter', $quarter)->where('stage', $stage);
        if ($id > 0) {
            $dup->where('id !=', (int) $id);
        }
        if ($dup->get('stage_master')->row()) {
            return array('status' => false, 'message' => 'Stage exists (' . $code . ').');
        }
        $payload = array(
            'code' => $code,
            'quarter' => $quarter,
            'stage' => $stage,
            'duration_days' => (int) $days,
            'operational_rule' => trim((string) $rule),
            'is_active' => (int) $active,
            'patient_enrolled' => (int) $patient_enrolled,
        );
        if ($id > 0) {
            $this->db->where('id', (int) $id)->update('stage_master', $payload);
            return array('status' => true, 'message' => 'Stage updated (' . $code . ').');
        }
        $this->db->insert('stage_master', $payload);
        return array('status' => true, 'message' => 'Stage added (' . $code . ').');
    }

    public function delete_stage($id)
    {
        $this->db->where('id', (int) $id)->delete('stage_master');
        return array('status' => true, 'message' => 'Stage deleted.');
    }

    public function get_medicines($series = '')
    {
        if (!$this->db->table_exists('medicine_master')) {
            return array();
        }
        $this->db->from('medicine_master');
        if ($this->db->table_exists('pharmacy')) {
            $this->db->select('medicine_master.*, pharmacy.medicine_name AS pharmacy_name');
            $this->db->join('pharmacy', 'pharmacy.id = medicine_master.pharmacy_id', 'left');
        }
        if ($series !== '') {
            $this->db->where('medicine_master.series', $series);
        }
        $this->db->order_by('medicine_master.series', 'asc');
        $this->db->order_by('medicine_master.code', 'asc');
        return $this->db->get()->result();
    }

    /** 
     * Get medicines grouped by series (F, FA, FB, FC, FD, FX, E)
     */
    public function get_medicines_grouped()
    {
        if (!$this->db->table_exists('medicine_master')) {
            return [];
        }
        $this->db->from('medicine_master');
        if ($this->db->table_exists('pharmacy')) {
            $this->db->select('medicine_master.*, pharmacy.medicine_name AS pharmacy_name');
            $this->db->join('pharmacy', 'pharmacy.id = medicine_master.pharmacy_id', 'left');
        }
        $this->db->order_by('medicine_master.series', 'asc');
        $this->db->order_by('medicine_master.code', 'asc');
        $result = $this->db->get()->result();

        // Group by series
        $groups = ['F', 'FA', 'FB', 'FC', 'FD', 'FX', 'E'];
        $grouped = [];
        foreach ($groups as $group) {
            $grouped[$group] = [];
        }
        foreach ($result as $row) {
            $series = $row->series;
            if (isset($grouped[$series])) {
                $grouped[$series][] = $row;
            }
        }
        return $grouped;
    }

    public function get_pharmacy_medicines()
    {
        if (!$this->db->table_exists('pharmacy')) {
            return array();
        }
        $this->db->select('id, medicine_name');
        $this->db->order_by('medicine_name', 'asc');
        return $this->db->get('pharmacy')->result();
    }

    public function save_medicine($id, $data)
    {
        $data['code'] = strtoupper(trim((string) $data['code']));
        $dup = $this->db->where('code', $data['code']);
        if ($id > 0) {
            $dup->where('id !=', (int) $id);
        }
        if ($dup->get('medicine_master')->row()) {
            return array('status' => false, 'message' => 'Medicine code exists.');
        }
        if ($id > 0) {
            $row = $this->db->where('id', (int) $id)->get('medicine_master')->row();
            $this->db->where('id', (int) $id)->update('medicine_master', $data);
            // Sync the linked pharmacy medicine (create link if missing).
            $ph_id = $this->sync_pharmacy_medicine($data, ($row && !empty($row->pharmacy_id)) ? (int) $row->pharmacy_id : 0);
            if ($ph_id > 0 && (!$row || (int) $row->pharmacy_id !== $ph_id)) {
                $this->db->where('id', (int) $id)->update('medicine_master', array('pharmacy_id' => $ph_id));
            }
            return array('status' => true, 'message' => 'Medicine updated.');
        }
        $this->db->insert('medicine_master', $data);
        $new_id = (int) $this->db->insert_id();
        // Auto-create the same medicine in pharmacy module and store the FK.
        $ph_id = $this->sync_pharmacy_medicine($data, 0);
        if ($new_id > 0 && $ph_id > 0) {
            $this->db->where('id', $new_id)->update('medicine_master', array('pharmacy_id' => $ph_id));
        }
        return array('status' => true, 'message' => 'Medicine added.');
    }

    /**
     * Keep the pharmacy module's medicine in sync with patient-flow medicine.
     * Creates a pharmacy row on first save, updates it thereafter via stored FK.
     *
     * @param array $data        medicine_master save payload (needs medicine_identity/is_active)
     * @param int   $pharmacy_id existing pharmacy.id link (0 = none yet)
     * @return int pharmacy_id (0 when pharmacy table missing / nothing done)
     */
    protected function sync_pharmacy_medicine($data, $pharmacy_id = 0)
    {
        if (!$this->db->table_exists('pharmacy')) {
            return 0;
        }
        $identity = isset($data['medicine_identity']) ? trim((string) $data['medicine_identity']) : '';
        if ($identity === '') {
            return (int) $pharmacy_id;
        }
        $pdata = array(
            'medicine_name' => $identity,
            'is_active' => (isset($data['is_active']) && (int) $data['is_active'] === 0) ? '0' : '1',
            'note' => $this->build_pharmacy_note($data),
        );
        if ($pharmacy_id > 0) {
            $ph = $this->db->where('id', (int) $pharmacy_id)->get('pharmacy')->row();
            if ($ph) {
                $upd = $pdata;
                // Back-fill category only when the pharmacy row has none yet.
                if (empty($ph->medicine_category_id)) {
                    $cat_id = $this->get_or_create_default_category();
                    if ($cat_id > 0) {
                        $upd['medicine_category_id'] = $cat_id;
                    }
                }
                $this->db->where('id', (int) $pharmacy_id)->update('pharmacy', $upd);
                return (int) $pharmacy_id;
            }
        }
        // Assign default category (created on first use).
        $cat_id = $this->get_or_create_default_category();
        if ($cat_id > 0) {
            $pdata['medicine_category_id'] = $cat_id;
        }
        // NOT NULL columns without application defaults on the pharmacy table.
        $pdata['medicine_image'] = '';
        $pdata['rack_number'] = '';
        $this->db->insert('pharmacy', $pdata);
        return (int) $this->db->insert_id();
    }

    /**
     * Default pharmacy category for auto-created medicines.
     * Reuses an existing "Default" category; creates it when missing.
     *
     * @return int category id (0 when table missing / insert failed)
     */
    protected function get_or_create_default_category()
    {
        if (!$this->db->table_exists('medicine_category')) {
            return 0;
        }
        $row = $this->db->where('medicine_category', 'Default')->get('medicine_category')->row();
        if ($row) {
            return (int) $row->id;
        }
        $this->db->insert('medicine_category', array('medicine_category' => 'Default'));
        return (int) $this->db->insert_id();
    }

    /**
     * Pack all remaining patient-flow medicine details into the pharmacy
     * medicine's note field (name/status live in their own columns).
     *
     * @param array $data medicine_master save payload
     * @return string pipe-separated "Label: value" pairs (empty when nothing set)
     */
    protected function build_pharmacy_note($data)
    {
        $labels = array(
            'code' => 'Code', 'series' => 'Series', 'gp_code' => 'GP Code',
            'indication' => 'Indication', 'purpose' => 'Purpose',
            'patient_dose' => 'Dose', 'route_method' => 'Route', 'timing' => 'Timing',
            'frequency' => 'Frequency', 'duration_text' => 'Duration',
            'dispensing_qty_ml' => 'Disp Qty (ml)', 'container_vial' => 'Container',
            'stock_deduction_ml' => 'Stock Ded (ml)', 'use_note' => 'Use Note',
            'control_note' => 'Control Note', 'supply_status' => 'Supply Status',
            'status' => 'Status',
        );
        $parts = array();
        foreach ($labels as $key => $label) {
            if (!isset($data[$key])) {
                continue;
            }
            $val = trim((string) $data[$key]);
            if ($val === '') {
                continue;
            }
            $parts[] = $label . ': ' . $val;
        }
        return implode(' | ', $parts);
    }

    public function delete_medicine($id)
    {
        $row = $this->db->where('id', (int) $id)->get('medicine_master')->row();
        if (!$row) {
            return array('status' => false, 'message' => 'Not found.');
        }
        if ($row->status === 'Locked') {
            return array('status' => false, 'message' => 'Locked row cannot be deleted.');
        }
        foreach (array('constit_f_series_mapping', 'formulation_early_mapping', 'formulation_universal_mapping') as $t) {
            if ($this->db->table_exists($t)
                && $this->db->where('medicine_id', (int) $id)->get($t)->row()) {
                return array('status' => false, 'message' => 'In use by mapping.');
            }
        }
        $this->db->where('id', (int) $id)->delete('medicine_master');
        return array('status' => true, 'message' => 'Medicine deleted.');
    }

    public function get_broad_series()
    {
        if (!$this->db->table_exists('broad_area_series')) {
            return array();
        }
        $this->db->from('broad_area_series');
        $this->db->order_by('sort_order', 'asc');
        return $this->db->get()->result();
    }

    public function get_f_mapping($series_id = 0)
    {
        if (!$this->db->table_exists('constit_f_series_mapping')) {
            return array();
        }
        $this->db->select('m.*, b.code AS scode, s.code AS stcode, med.code AS mcode, med.medicine_identity AS mident');
        $this->db->from('constit_f_series_mapping m');
        $this->db->join('broad_area_series b', 'b.id = m.series_id', 'left');
        $this->db->join('stage_master s', 's.id = m.stage_id', 'left');
        $this->db->join('medicine_master med', 'med.id = m.medicine_id', 'left');
        if ((int) $series_id > 0) {
            $this->db->where('m.series_id', (int) $series_id);
        }
        $this->db->order_by('b.sort_order', 'asc');
        $this->db->order_by('s.sort_order', 'asc');
        return $this->db->get()->result();
    }

    public function save_f_mapping($series_id, $stage_id, $medicine_id)
    {
        $dup = $this->db->where('series_id', (int) $series_id)->where('stage_id', (int) $stage_id);
        if ($dup->get('constit_f_series_mapping')->row()) {
            return array('status' => false, 'message' => 'Mapping exists for this series+stage.');
        }
        $this->db->insert('constit_f_series_mapping', array(
            'series_id' => (int) $series_id, 'stage_id' => (int) $stage_id,
            'medicine_id' => (int) $medicine_id, 'status' => 'Locked',
        ));
        return array('status' => true, 'message' => 'Mapping added.');
    }

    public function get_early_mapping()
    {
        if (!$this->db->table_exists('formulation_early_mapping')) {
            return array();
        }
        $this->db->select('m.*, s.code AS stcode, med.code AS mcode, med.medicine_identity AS mident');
        $this->db->from('formulation_early_mapping m');
        $this->db->join('stage_master s', 's.id = m.stage_id', 'left');
        $this->db->join('medicine_master med', 'med.id = m.medicine_id', 'left');
        $this->db->order_by('m.pair_group', 'asc');
        $this->db->order_by('s.sort_order', 'asc');
        return $this->db->get()->result();
    }

    public function get_universal_mapping()
    {
        if (!$this->db->table_exists('formulation_universal_mapping')) {
            return array();
        }
        $this->db->select('m.*, s.code AS stcode, med.code AS mcode, med.medicine_identity AS mident');
        $this->db->from('formulation_universal_mapping m');
        $this->db->join('stage_master s', 's.id = m.stage_id', 'left');
        $this->db->join('medicine_master med', 'med.id = m.medicine_id', 'left');
        $this->db->order_by('s.sort_order', 'asc');
        $this->db->order_by('m.session', 'asc');
        return $this->db->get()->result();
    }

    /** ---------- Early Mapping CRUD ---------- */

    public function save_early_mapping($id, $pair_group, $stage_id, $medicine_id)
    {
        if (!$this->db->table_exists('formulation_early_mapping')) {
            return array('status' => false, 'message' => 'Table not found.');
        }
        $pair_group = strtoupper(trim((string) $pair_group));
        $stage_id = (int) $stage_id;
        $medicine_id = (int) $medicine_id;
        if ($pair_group === '' || $stage_id <= 0 || $medicine_id <= 0) {
            return array('status' => false, 'message' => 'All fields are required.');
        }
        $dup = $this->db->where('pair_group', $pair_group)->where('stage_id', $stage_id);
        if ($id > 0) {
            $dup->where('id !=', (int) $id);
        }
        if ($dup->get('formulation_early_mapping')->row()) {
            return array('status' => false, 'message' => 'Mapping already exists for this pair group + stage.');
        }
        if ($id > 0) {
            $this->db->where('id', (int) $id)->update('formulation_early_mapping', array(
                'pair_group' => $pair_group, 'stage_id' => $stage_id, 'medicine_id' => $medicine_id,
            ));
            return array('status' => true, 'message' => 'Early mapping updated.');
        }
        $this->db->insert('formulation_early_mapping', array(
            'pair_group' => $pair_group, 'stage_id' => $stage_id, 'medicine_id' => $medicine_id, 'status' => 'Locked',
        ));
        return array('status' => true, 'message' => 'Early mapping added.');
    }

    public function delete_early_mapping($id)
    {
        if (!$this->db->table_exists('formulation_early_mapping')) {
            return array('status' => false, 'message' => 'Table not found.');
        }
        $row = $this->db->where('id', (int) $id)->get('formulation_early_mapping')->row();
        if (!$row) {
            return array('status' => false, 'message' => 'Not found.');
        }
        if ($row->status === 'Locked') {
            return array('status' => false, 'message' => 'Locked row cannot be deleted.');
        }
        $this->db->where('id', (int) $id)->delete('formulation_early_mapping');
        return array('status' => true, 'message' => 'Early mapping deleted.');
    }

    /** ---------- Universal Mapping CRUD ---------- */

    public function save_universal_mapping($id, $stage_id, $session, $medicine_id)
    {
        if (!$this->db->table_exists('formulation_universal_mapping')) {
            return array('status' => false, 'message' => 'Table not found.');
        }
        $stage_id = (int) $stage_id;
        $session = trim((string) $session);
        $medicine_id = (int) $medicine_id;
        if ($stage_id <= 0 || $session === '' || $medicine_id <= 0) {
            return array('status' => false, 'message' => 'All fields are required.');
        }
        $dup = $this->db->where('stage_id', $stage_id)->where('session', $session);
        if ($id > 0) {
            $dup->where('id !=', (int) $id);
        }
        if ($dup->get('formulation_universal_mapping')->row()) {
            return array('status' => false, 'message' => 'Mapping already exists for this stage + session.');
        }
        if ($id > 0) {
            $this->db->where('id', (int) $id)->update('formulation_universal_mapping', array(
                'stage_id' => $stage_id, 'session' => $session, 'medicine_id' => $medicine_id,
            ));
            return array('status' => true, 'message' => 'Universal mapping updated.');
        }
        $this->db->insert('formulation_universal_mapping', array(
            'stage_id' => $stage_id, 'session' => $session, 'medicine_id' => $medicine_id, 'status' => 'Locked',
        ));
        return array('status' => true, 'message' => 'Universal mapping added.');
    }

    public function delete_universal_mapping($id)
    {
        if (!$this->db->table_exists('formulation_universal_mapping')) {
            return array('status' => false, 'message' => 'Table not found.');
        }
        $row = $this->db->where('id', (int) $id)->get('formulation_universal_mapping')->row();
        if (!$row) {
            return array('status' => false, 'message' => 'Not found.');
        }
        if ($row->status === 'Locked') {
            return array('status' => false, 'message' => 'Locked row cannot be deleted.');
        }
        $this->db->where('id', (int) $id)->delete('formulation_universal_mapping');
        return array('status' => true, 'message' => 'Universal mapping deleted.');
    }

    /** ---------- Broad Area Rules CRUD ---------- */

    public function get_broad_rules()
    {
        if (!$this->db->table_exists('broad_area_rules')) {
            return array();
        }
        $this->db->from('broad_area_rules');
        $this->db->order_by('sort_order', 'asc');
        $this->db->order_by('id', 'asc');
        return $this->db->get()->result();
    }

    public function save_broad_rule($id, $concern_pattern, $applicability, array $results = array(), $is_active = 1)
    {
        if (!$this->db->table_exists('broad_area_rules')) {
            return array('status' => false, 'message' => 'Table not found.');
        }
        $concern_pattern = trim((string) $concern_pattern);
        $applicability = trim((string) $applicability);
        $is_active = ((string) $is_active === '0') ? 0 : 1;
        if ($concern_pattern === '' || $applicability === '') {
            return array('status' => false, 'message' => 'Concern pattern and applicability are required.');
        }
        if (empty($results)) {
            return array('status' => false, 'message' => 'At least one series / result is required.');
        }
        $results = array_values(array_filter(array_map(function ($v) { return trim((string) $v); }, $results)));
        if (empty($results)) {
            return array('status' => false, 'message' => 'At least one series / result is required.');
        }
        $series_result = json_encode($results);
        $data = array(
            'concern_pattern' => $concern_pattern,
            'applicability' => $applicability,
            'series_result' => $series_result,
            'is_active' => $is_active,
        );
        $dup = $this->db->where('concern_pattern', $concern_pattern)->where('applicability', $applicability);
        if ($id > 0) {
            $dup->where('id !=', (int) $id);
        }
        if ($dup->get('broad_area_rules')->row()) {
            return array('status' => false, 'message' => 'Rule already exists for this concern pattern + applicability.');
        }
        if ($id > 0) {
            $this->db->where('id', (int) $id)->update('broad_area_rules', $data);
            return array('status' => true, 'message' => 'Broad area rule updated.');
        }
        $this->db->insert('broad_area_rules', $data);
        return array('status' => true, 'message' => 'Broad area rule added.');
    }

    public function delete_broad_rule($id)
    {
        if (!$this->db->table_exists('broad_area_rules')) {
            return array('status' => false, 'message' => 'Table not found.');
        }
        $row = $this->db->where('id', (int) $id)->get('broad_area_rules')->row();
        if (!$row) {
            return array('status' => false, 'message' => 'Not found.');
        }
        $this->db->where('id', (int) $id)->delete('broad_area_rules');
        return array('status' => true, 'message' => 'Broad area rule deleted.');
    }

    /* ========================================================================
       MEDICINE SELECTION (SHORT) — model methods
       ======================================================================== */

    public function get_broad_area_by_concern($concern, $gender)
    {
        if (!$this->db->table_exists('broad_area_rules')) {
            return array();
        }
        $concern = trim((string) $concern);
        $gender  = trim((string) $gender);
        if ($concern === '' || $gender === '') {
            return array();
        }
        $genderLower = strtolower($gender);
        // normalize single-letter codes (M/F) sent from the frontend
        if ($genderLower === 'm') { $genderLower = 'male'; }
        elseif ($genderLower === 'f') { $genderLower = 'female'; }
        $rules = $this->db->get('broad_area_rules')->result();
        $matched = array();
        // 1st pass: gender-specific rules only — match concern_pattern term-by-term with LIKE.
        foreach ($rules as $rule) {
            $applicability = trim((string) $rule->applicability);
            // 'Any' rules are only used as a fallback, skip them here.
            if ($applicability === 'Any') {
                continue;
            }
            // applicability vs gender
            $app_ok = false;
            if ($applicability === 'Female only' && $genderLower === 'female') {
                $app_ok = true;
            } elseif ($applicability === 'Female' && $genderLower === 'female') {
                $app_ok = true;
            } elseif (in_array($applicability, array('Male/general','Male'), true)) {
                if ($genderLower === 'male' || $genderLower === 'general') {
                    $app_ok = true;
                }
            }
            if (!$app_ok) {
                continue;
            }
            // concern_pattern is comma separated; match each term with LIKE.
            $pattern = trim((string) $rule->concern_pattern);
            $terms = array_map('trim', explode(',', $pattern));
            $pattern_match = false;
            foreach ($terms as $term) {
                if ($term === '') {
                    continue;
                }
                if (stripos($concern, $term) !== false || stripos($term, $concern) !== false) {
                    $pattern_match = true;
                    break;
                }
            }
            if (!$pattern_match) {
                continue;
            }
            $results = json_decode((string) $rule->series_result, true);
            if (is_array($results)) {
                $matched = array_merge($matched, $results);
            }
        }
        // Return gender-specific match if found.
        if (!empty($matched)) {
            return array_values(array_unique($matched));
        }
        // 2nd pass (fallback): no gender-specific concern pattern matched,
        // so return the series result of rules whose applicability is 'Any'
        // regardless of whether their concern_pattern matches.
        $anyMatched = array();
        foreach ($rules as $rule) {
            $applicability = trim((string) $rule->applicability);
            if ($applicability !== 'Any') {
                continue;
            }
            $results = json_decode((string) $rule->series_result, true);
            if (is_array($results)) {
                $anyMatched = array_merge($anyMatched, $results);
            }
        }
        return array_values(array_unique($anyMatched));
    }

    public function get_stage_from_visit($visit_id)
    {
        if (!$this->db->table_exists('patient_flow_stage_assignment')) {
            return array('stage_id' => null, 'stage_code' => null);
        }
        $row = $this->db->where('visit_id', (int) $visit_id)->where('is_current', 1)
            ->get('patient_flow_stage_assignment')->row();
        if (!$row) {
            // First visit — fall back to the first stage in stage_master (Q1S1)
            if ($this->db->table_exists('stage_master')) {
                $first = $this->db->order_by('sort_order', 'asc')->order_by('id', 'asc')
                    ->get('stage_master')->row();
                if ($first) {
                    return array('stage_id' => (int) $first->id, 'stage_code' => (string) $first->code);
                }
            }
            return array('stage_id' => null, 'stage_code' => null);
        }
        return array('stage_id' => (int) $row->stage_id, 'stage_code' => (string) $row->stage_code);
    }

    public function set_stage_for_visit($visit_id, $stage_id, $stage_code)
    {
        if (!$this->db->table_exists('patient_flow_stage_assignment')) {
            return array('status' => false, 'message' => 'Table not found.');
        }
        $visit_id = (int) $visit_id;
        $stage_id = (int) $stage_id;
        $stage_code = strtoupper(trim((string) $stage_code));
        $this->db->where('visit_id', $visit_id)->where('is_current', 1)
            ->update('patient_flow_stage_assignment', array('is_current' => 0));
        $existing = $this->db->where('visit_id', $visit_id)->where('stage_id', $stage_id)
            ->get('patient_flow_stage_assignment')->row();
        if ($existing) {
            $this->db->where('id', (int) $existing->id)
                ->update('patient_flow_stage_assignment', array(
                    'stage_code' => $stage_code, 'is_current' => 1,
                    'assigned_at' => date('Y-m-d H:i:s'),
                ));
            return array('status' => true, 'message' => 'Stage updated.');
        }
        $this->db->insert('patient_flow_stage_assignment', array(
            'visit_id' => $visit_id, 'stage_id' => $stage_id, 'stage_code' => $stage_code,
            'is_current' => 1, 'assigned_at' => date('Y-m-d H:i:s'),
        ));
        return array('status' => true, 'message' => 'Stage assigned.');
    }

    public function get_variant_from_bod($body_build_id)
    {
        if (!$this->db->table_exists('constitution_body_build')) { return 1; }
        $row = $this->db->where('id', (int) $body_build_id)
            ->get('constitution_body_build')->row();
        if (!$row) { return 1; }
        return ((int) $row->variant === 2) ? 2 : 1;
    }

    public function get_medicines_by_series_code($visit_id, $series_code, $stage_id = null)
    {
        $visit_id = (int) $visit_id;
        $series_code = strtoupper(trim((string) $series_code));
        $stage_id = (int) $stage_id;
        if ($visit_id <= 0 || $series_code === '') {
            return array(
                'stage' => array('stage_id' => null, 'stage_code' => null),
                'medicines' => array(),
            );
        }

        // Use provided stage_id, otherwise resolve from visit (Q1S1 for first visit)
        if ($stage_id <= 0) {
            $stage = $this->get_stage_from_visit($visit_id);
            $stage_id = $stage['stage_id'];
        } else {
            // Get stage code for the response from stage_master
            $stage_code = null;
            if ($this->db->table_exists('stage_master')) {
                $srow = $this->db->where('id', $stage_id)->get('stage_master')->row();
                if ($srow) {
                    $stage_code = (string) $srow->code;
                }
            }
            $stage = array('stage_id' => $stage_id, 'stage_code' => $stage_code);
        }
        $session = 'Morning';

        $medicines = array();

        // 1) F-series medicines for this specific series code + stage
        $f_rows = $this->get_f_medicines_by_series_stage($series_code, $stage_id);
        foreach ($f_rows as $r) {
            $medicines[] = array(
                'medicine_id' => (int) $r->medicine_id,
                'code' => (string) ($r->mcode ?: $r->code),
                'identity' => (string) ($r->mident ?: ''),
                'series' => 'F',
                'variant' => 1,
                'source' => 'F-series mapping',
            );
        }

        // 2) Daily medicines for this stage, filtered by the series' pair_group
        $allowed_pair_groups = array();
        if ($this->db->table_exists('broad_area_series')) {
            $srow = $this->db->where('code', $series_code)->get('broad_area_series')->row();
            if ($srow && $srow->pair_group !== null && $srow->pair_group !== '') {
                $allowed_pair_groups[] = (string) $srow->pair_group;
            }
        }

        if (!empty($allowed_pair_groups)) {
            $daily_rows = $this->get_daily_medicines_by_stage($stage_id, $session, $allowed_pair_groups);
            foreach ($daily_rows as $r) {
                $series = strtoupper(trim((string) ($r->series ?: '')));
                // Exclude FA series — only F-series medicines (from constit_f_series_mapping)
                // should appear for a selected broad area series.
                if ($series === 'FA') {
                    continue;
                }
                $medicines[] = array(
                    'medicine_id' => (int) $r->medicine_id,
                    'code' => (string) ($r->mcode ?: $r->code),
                    'identity' => (string) ($r->mident ?: ''),
                    'series' => $series,
                    'variant' => 1,
                    'source' => 'Daily mapping (' . $session . ')',
                );
            }
        }

        // 3) FX series medicines (universal) from medicine_master
        if ($this->db->table_exists('medicine_master')) {
            $fx_rows = $this->db->select('id, code, medicine_identity, patient_dose, route_method, timing, frequency, duration_text, dispensing_qty_ml')
                ->where('series', 'FX')->where('is_active', 1)
                ->get('medicine_master')->result();
            foreach ($fx_rows as $r) {
                $medicines[] = array(
                    'medicine_id' => (int) $r->id,
                    'code' => (string) ($r->code ?: ''),
                    'identity' => (string) ($r->medicine_identity ?: ''),
                    'patient_dose' => (string) ($r->patient_dose ?: ''),
                    'route_method' => (string) ($r->route_method ?: ''),
                    'timing' => (string) ($r->timing ?: ''),
                    'frequency' => (string) ($r->frequency ?: ''),
                    'duration_text' => (string) ($r->duration_text ?: ''),
                    'dispensing_qty_ml' => ($r->dispensing_qty_ml !== null && $r->dispensing_qty_ml !== '') ? (string) $r->dispensing_qty_ml : '',
                    'series' => 'FX',
                    'variant' => 1,
                    'source' => 'Universal FX medicine',
                );
            }

            // 4) E series medicines (universal) from medicine_master
            $e_rows = $this->db->select('id, code, medicine_identity, patient_dose, route_method, timing, frequency, duration_text, dispensing_qty_ml')
                ->where('series', 'E')->where('is_active', 1)
                ->get('medicine_master')->result();
            foreach ($e_rows as $r) {
                $medicines[] = array(
                    'medicine_id' => (int) $r->id,
                    'code' => (string) ($r->code ?: ''),
                    'identity' => (string) ($r->medicine_identity ?: ''),
                    'patient_dose' => (string) ($r->patient_dose ?: ''),
                    'route_method' => (string) ($r->route_method ?: ''),
                    'timing' => (string) ($r->timing ?: ''),
                    'frequency' => (string) ($r->frequency ?: ''),
                    'duration_text' => (string) ($r->duration_text ?: ''),
                    'dispensing_qty_ml' => ($r->dispensing_qty_ml !== null && $r->dispensing_qty_ml !== '') ? (string) $r->dispensing_qty_ml : '',
                    'series' => 'E',
                    'variant' => 1,
                    'source' => 'Universal E medicine',
                );
            }
        }

        // Deduplicate by medicine_id
        $seen = array();
        $unique = array();
        foreach ($medicines as $m) {
            if (!isset($seen[$m['medicine_id']])) {
                $seen[$m['medicine_id']] = true;
                $unique[] = $m;
            }
        }

        return array(
            'stage' => $stage,
            'medicines' => $unique,
        );
    }

    /**
     * Get medicines grouped by series (F, FA, FB, FC, FD, FX, E) for all series codes.
     * Used for the redesigned resolve medicines section showing all series at once.
     */
    public function get_medicines_grouped_by_series($visit_id, $stage_id = null)
    {
        $visit_id = (int) $visit_id;
        $stage_id = (int) $stage_id;
        if ($visit_id <= 0) {
            return array(
                'stage' => array('stage_id' => null, 'stage_code' => null),
                'medicines_grouped' => array(
                    'F' => array(), 'FA' => array(), 'FB' => array(),
                    'FC' => array(), 'FD' => array(), 'FX' => array(), 'E' => array(),
                ),
            );
        }

        // Resolve stage
        if ($stage_id <= 0) {
            $stage = $this->get_stage_from_visit($visit_id);
            $stage_id = $stage['stage_id'];
        } else {
            $stage_code = null;
            if ($this->db->table_exists('stage_master')) {
                $srow = $this->db->where('id', $stage_id)->get('stage_master')->row();
                if ($srow) {
                    $stage_code = (string) $srow->code;
                }
            }
            $stage = array('stage_id' => $stage_id, 'stage_code' => $stage_code);
        }
        $session = 'Morning';

        $medicines_grouped = array(
            'F' => array(), 'FA' => array(), 'FB' => array(),
            'FC' => array(), 'FD' => array(), 'FX' => array(), 'E' => array(),
        );

        // 1) F-series medicines for ALL broad area series codes + stage
        $broad_series_codes = array();
        if ($this->db->table_exists('broad_area_series')) {
            $bs_rows = $this->db->get('broad_area_series')->result();
            foreach ($bs_rows as $bs_row) {
                $broad_series_codes[] = (string) $bs_row->code;
            }
        }

        foreach ($broad_series_codes as $series_code) {
            $f_rows = $this->get_f_medicines_by_series_stage($series_code, $stage_id);
            foreach ($f_rows as $r) {
                $medicines_grouped['F'][] = array(
                    'medicine_id' => (int) $r->medicine_id,
                    'code' => (string) ($r->mcode ?: $r->code),
                    'identity' => (string) ($r->mident ?: ''),
                    'patient_dose' => (string) ($r->patient_dose ?: ''),
                    'route_method' => (string) ($r->route_method ?: ''),
                    'timing' => (string) ($r->timing ?: ''),
                    'frequency' => (string) ($r->frequency ?: ''),
                    'duration_text' => (string) ($r->duration_text ?: ''),
                    'dispensing_qty_ml' => ($r->dispensing_qty_ml !== null && $r->dispensing_qty_ml !== '') ? (string) $r->dispensing_qty_ml : '',
                    'series' => 'F',
                    'variant' => 1,
                    'source' => 'F-series mapping (' . $series_code . ')',
                );
            }
        }

        // 2) Daily medicines for this stage - get ALL series (no pair_group filter)
        // This includes FA, FB, FC, FD, FX, E series medicines from medicine_master
        if ($this->db->table_exists('formulation_early_mapping') || $this->db->table_exists('formulation_universal_mapping')) {
            $stage_code = strtoupper((string) ($stage['stage_code'] ?: ''));
            $daily_rows = array();

            if (in_array($stage_code, array('Q1S1', 'Q1S2'), true)
                && $this->db->table_exists('formulation_early_mapping')) {
                $this->db->select('m.*, med.code AS mcode, med.medicine_identity AS mident, med.series AS series');
                $this->db->from('formulation_early_mapping m');
                $this->db->join('medicine_master med', 'med.id = m.medicine_id', 'left');
                $this->db->where('m.stage_id', $stage_id)->where('m.session', $session);
                $daily_rows = $this->db->get()->result();
            }
            elseif ($this->db->table_exists('formulation_universal_mapping')) {
                $this->db->select('m.*, med.code AS mcode, med.medicine_identity AS mident, med.series AS series');
                $this->db->from('formulation_universal_mapping m');
                $this->db->join('medicine_master med', 'med.id = m.medicine_id', 'left');
                $this->db->where('m.stage_id', $stage_id)->where('m.session', $session);
                $daily_rows = $this->db->get()->result();
            }

            foreach ($daily_rows as $r) {
                $series = strtoupper(trim((string) ($r->series ?: '')));
                if (!isset($medicines_grouped[$series])) {
                    $medicines_grouped[$series] = array();
                }
                $medicines_grouped[$series][] = array(
                    'medicine_id' => (int) $r->medicine_id,
                    'code' => (string) ($r->mcode ?: $r->code),
                    'identity' => (string) ($r->mident ?: ''),
                    'patient_dose' => (string) ($r->patient_dose ?: ''),
                    'route_method' => (string) ($r->route_method ?: ''),
                    'timing' => (string) ($r->timing ?: ''),
                    'frequency' => (string) ($r->frequency ?: ''),
                    'duration_text' => (string) ($r->duration_text ?: ''),
                    'dispensing_qty_ml' => ($r->dispensing_qty_ml !== null && $r->dispensing_qty_ml !== '') ? (string) $r->dispensing_qty_ml : '',
                    'series' => $series,
                    'variant' => 1,
                    'source' => 'Daily mapping (' . $session . ')',
                );
            }
        }

        // 3) FX series medicines (universal) from medicine_master
        if ($this->db->table_exists('medicine_master')) {
            $fx_rows = $this->db->select('id, code, medicine_identity, patient_dose, route_method, timing, frequency, duration_text, dispensing_qty_ml')
                ->where('series', 'FX')->where('is_active', 1)
                ->get('medicine_master')->result();
            foreach ($fx_rows as $r) {
                $medicines_grouped['FX'][] = array(
                    'medicine_id' => (int) $r->id,
                    'code' => (string) ($r->code ?: ''),
                    'identity' => (string) ($r->medicine_identity ?: ''),
                    'patient_dose' => (string) ($r->patient_dose ?: ''),
                    'route_method' => (string) ($r->route_method ?: ''),
                    'timing' => (string) ($r->timing ?: ''),
                    'frequency' => (string) ($r->frequency ?: ''),
                    'duration_text' => (string) ($r->duration_text ?: ''),
                    'dispensing_qty_ml' => ($r->dispensing_qty_ml !== null && $r->dispensing_qty_ml !== '') ? (string) $r->dispensing_qty_ml : '',
                    'series' => 'FX',
                    'variant' => 1,
                    'source' => 'Universal FX medicine',
                );
            }

            // 4) E series medicines (universal) from medicine_master
            $e_rows = $this->db->select('id, code, medicine_identity, patient_dose, route_method, timing, frequency, duration_text, dispensing_qty_ml')
                ->where('series', 'E')->where('is_active', 1)
                ->get('medicine_master')->result();
            foreach ($e_rows as $r) {
                $medicines_grouped['E'][] = array(
                    'medicine_id' => (int) $r->id,
                    'code' => (string) ($r->code ?: ''),
                    'identity' => (string) ($r->medicine_identity ?: ''),
                    'patient_dose' => (string) ($r->patient_dose ?: ''),
                    'route_method' => (string) ($r->route_method ?: ''),
                    'timing' => (string) ($r->timing ?: ''),
                    'frequency' => (string) ($r->frequency ?: ''),
                    'duration_text' => (string) ($r->duration_text ?: ''),
                    'dispensing_qty_ml' => ($r->dispensing_qty_ml !== null && $r->dispensing_qty_ml !== '') ? (string) $r->dispensing_qty_ml : '',
                    'series' => 'E',
                    'variant' => 1,
                    'source' => 'Universal E medicine',
                );
            }
        }

        // Deduplicate by medicine_id within each series group
        foreach ($medicines_grouped as $s => &$meds) {
            $seen = array();
            $unique = array();
            foreach ($meds as $m) {
                if (!isset($seen[$m['medicine_id']])) {
                    $seen[$m['medicine_id']] = true;
                    $unique[] = $m;
                }
            }
            $meds = $unique;
        }
        unset($meds);

        return array(
            'stage' => $stage,
            'medicines_grouped' => $medicines_grouped,
        );
    }

    public function get_f_medicines_by_series_stage($series_code, $stage_id)
    {
        if (!$this->db->table_exists('constit_f_series_mapping')) { return array(); }
        $series_code = strtoupper(trim((string) $series_code));
        $stage_id = (int) $stage_id;
        if ($series_code === '' || $stage_id <= 0) { return array(); }
        $series_row = $this->db->where('code', $series_code)
            ->get('broad_area_series')->row();
        if (!$series_row) { return array(); }
        $rows = $this->db
            ->select('m.*, med.code AS mcode, med.medicine_identity AS mident, med.patient_dose, med.route_method, med.timing, med.frequency, med.duration_text, med.dispensing_qty_ml')
            ->from('constit_f_series_mapping m')
            ->join('medicine_master med', 'med.id = m.medicine_id', 'left')
            ->where('m.series_id', (int) $series_row->id)
            ->where('m.stage_id', $stage_id)
            ->get()->result();
        return $rows;
    }

    public function get_daily_medicines_by_stage($stage_id, $session = 'Morning', $allowed_pair_groups = null)
    {
        $stage_id = (int) $stage_id;
        $session = strtoupper(trim((string) $session));
        if ($stage_id <= 0) { return array(); }
        $stage_row = $this->db->where('id', $stage_id)->get('stage_master')->row();
        if (!$stage_row) { return array(); }
        $stage_code = strtoupper((string) $stage_row->code);
        $results = array();
        if (in_array($stage_code, array('Q1S1','Q1S2'), true)
            && $this->db->table_exists('formulation_early_mapping')) {
            $this->db->select('m.*, med.code AS mcode, med.medicine_identity AS mident, med.series AS series');
            $this->db->from('formulation_early_mapping m');
            $this->db->join('medicine_master med', 'med.id = m.medicine_id', 'left');
            $this->db->where('m.stage_id', $stage_id)->where('m.session', $session);
            if ($allowed_pair_groups !== null && !empty($allowed_pair_groups)) {
                $this->db->where_in('m.pair_group', $allowed_pair_groups);
            }
            $rows = $this->db->get()->result();
            foreach ($rows as $r) { $results[] = $r; }
        }
        elseif ($this->db->table_exists('formulation_universal_mapping')) {
            $rows = $this->db
                ->select('m.*, med.code AS mcode, med.medicine_identity AS mident, med.series AS series')
                ->from('formulation_universal_mapping m')
                ->join('medicine_master med', 'med.id = m.medicine_id', 'left')
                ->where('m.stage_id', $stage_id)->where('m.session', $session)
                ->get()->result();
            foreach ($rows as $r) { $results[] = $r; }
        }
        return $results;
    }

    public function resolve_medicines_for_visit($visit_id, $gender, $concern, $body_build_id, $face_shape_id, $nose_type_id)
    {
        $visit_id = (int) $visit_id;
        $result = array(
            'constitution_code' => null, 'variant' => 1,
            'broad_area_series' => array(),
            'stage' => array('stage_id' => null, 'stage_code' => null),
            'medicines' => array(),
        );
        $result['constitution_code'] = $this->get_constitution_code(
            $gender, $body_build_id, $face_shape_id, $nose_type_id
        );
        $result['variant'] = $this->get_variant_from_bod($body_build_id);
        $result['broad_area_series'] = $this->get_broad_area_by_concern($concern, $gender);
        $result['stage'] = $this->get_stage_from_visit($visit_id);
        // Medicines are NOT resolved here — they are fetched separately via
        // get_medicines_by_series() after the doctor selects a broad area series.
        $result['medicines'] = array();
        return $result;
    }

    public function check_fx_limit($staff_id, $visit_id)
    {
        if (!$this->db->table_exists('patient_flow_prescription')) {
            return array('allowed' => false, 'message' => 'Prescription table not found.');
        }
        $staff_id = (int) $staff_id;
        $visit_id = (int) $visit_id;
        $fx_count = $this->db->where('visit_id', $visit_id)->where('series', 'FX')
            ->where('is_active', 1)->count_all_results('patient_flow_prescription');
        if ($fx_count >= 2) {
            return array('allowed' => false, 'message' => 'Maximum 2 FX medicines allowed per visit.');
        }
        return array('allowed' => true, 'message' => 'FX allowed.', 'current_count' => $fx_count);
    }

    public function is_e_set_free($stage_code)
    {
        $stage_code = strtoupper(trim((string) $stage_code));
        if ($stage_code === '') { return false; }
        if (preg_match('/^Q\d+S1$/', $stage_code)) { return true; }
        return false;
    }

    public function save_prescription($visit_id, $medicine_id, $staff_id, $series, $variant, $is_fx = false, $is_e = false){
        if (!$this->db->table_exists('patient_flow_prescription')) {
            return array('status' => false, 'message' => 'Prescription table not found.');
        }
        $visit_id   = (int) $visit_id;
        $medicine_id = (int) $medicine_id;
        $staff_id   = (int) $staff_id;
        $series     = strtoupper(trim((string) $series));
        $variant    = (int) $variant;
        if ($visit_id <= 0 || $medicine_id <= 0 || $staff_id <= 0) {
            return array('status' => false, 'message' => 'Invalid parameters.');
        }
        // Dispensing gate: require a PAID schedule row covering current stage
        $gate = $this->check_dispensing_gate($visit_id, $medicine_id);
        if (!$gate['status']) {
            return array('status' => false, 'message' => $gate['message']);
        }
        // E medicines are free at the first stage of each quarter (Q1S1, Q2S1, Q3S1, Q4S1).
        $payment_confirmed = 0;
        if ($is_e) {
            $stage = $this->get_stage_from_visit($visit_id);
            $stage_code = strtoupper(trim((string) ($stage['stage_code'] ?: '')));
            if (preg_match('/^Q[1-4]S1$/', $stage_code)) {
                $payment_confirmed = 1; // Free — no payment required
            }
        }
        $data = array(
            'visit_id' => $visit_id, 'medicine_id' => $medicine_id, 'series' => $series,
            'variant' => $variant, 'prescribed_by' => $staff_id,
            'prescribed_at' => date('Y-m-d H:i:s'), 'clinical_approval' => 'pending',
            'payment_confirmed' => $payment_confirmed, 'fx_second_senior_approval' => ($is_fx ? 0 : 0),
            'name_hidden' => 1, 'is_active' => 1,
        );
        $this->db->insert('patient_flow_prescription', $data);
        $id = $this->db->insert_id();
        $msg = 'Prescription saved (pending clinical approval).';
        if ($payment_confirmed) {
            $msg .= ' E medicine free at first stage of quarter.';
        }
        return array('status' => true, 'message' => $msg, 'id' => (int) $id);
    }

    /**
     * Deduct stock from pharmacy medicine batch for a prescribed medicine.
     * Looks up medicine_master->pharmacy_id, then deducts stock_deduction_ml
     * from medicine_batch_details (FIFO - oldest batch first).
     * Stock is deducted per prescription row (not per unique medicine).
     */
    public function deduct_stock_for_medicine($medicine_id)
    {
        if (!$this->db->table_exists('medicine_master') || !$this->db->table_exists('medicine_batch_details')) {
            return array('status' => false, 'message' => 'Required tables not found.');
        }

        $medicine_id = (int) $medicine_id;
        $med = $this->db->where('id', $medicine_id)->get('medicine_master')->row();
        if (!$med) {
            return array('status' => false, 'message' => 'Medicine not found.');
        }
        if (!$med->pharmacy_id) {
            return array('status' => false, 'message' => 'Medicine not linked to pharmacy.');
        }
        $stock_deduction_ml = (float) ($med->stock_deduction_ml ?: 0);
        if ($stock_deduction_ml <= 0) {
            return array('status' => false, 'message' => 'No stock deduction quantity configured.');
        }

        $pharmacy_id = (int) $med->pharmacy_id;
        $required   = $stock_deduction_ml;

        // Get batches with available stock, FIFO (oldest first)
        $batches = $this->db
            ->select('id, available_quantity')
            ->from('medicine_batch_details')
            ->where('pharmacy_id', $pharmacy_id)
            ->where('available_quantity >', 0)
            ->order_by('id', 'asc')
            ->get()->result_array();

        // VALIDATION: Check if total available stock is sufficient BEFORE deducting anything
        $totalAvailable = 0;
        foreach ($batches as $batch) {
            $totalAvailable += (float) $batch['available_quantity'];
        }

        if ($totalAvailable < $required) {
            return array('status' => false, 'message' => 'Insufficient stock. Required: ' . number_format($required, 2) . ' ml, Available: ' . number_format($totalAvailable, 2) . ' ml.');
        }

        // Deduct stock from batches (FIFO - oldest first)
        $remaining = $required;
        foreach ($batches as $batch) {
            if ($remaining <= 0) {
                break;
            }
            $batch_id = (int) $batch['id'];
            $available = (float) $batch['available_quantity'];
            $deduct = min($available, $remaining);
            $new_qty = $available - $deduct;
            $this->db->where('id', $batch_id)
                ->update('medicine_batch_details', array('available_quantity' => $new_qty));
            $remaining -= $deduct;
        }

        return array('status' => true, 'message' => 'Stock deducted successfully.', 'deducted_ml' => $stock_deduction_ml);
    }

    /**
     * Bulk save multiple medicines as prescriptions for a visit.
     * Skips medicines already prescribed for this visit (avoid duplicates).
     */
    public function save_prescriptions_bulk($visit_id, $medicines, $staff_id, $broad_area_series = ''){
        if (!$this->db->table_exists('patient_flow_prescription')) {
            return array('status' => false, 'message' => 'Prescription table not found.');
        }
        $visit_id = (int) $visit_id;
        $staff_id = (int) $staff_id;
        if ($visit_id <= 0 || !is_array($medicines) || empty($medicines)) {
            return array('status' => false, 'message' => 'Invalid parameters.');
        }
        // Dispensing gate: require a PAID schedule row covering current stage
        $gate = $this->check_dispensing_gate($visit_id, 0);
        if (!$gate['status']) {
            return array('status' => false, 'message' => $gate['message']);
        }

        // Fetch already prescribed medicine_ids for this visit (active and inactive).
        $existing = $this->db->where('visit_id', $visit_id)
            ->get('patient_flow_prescription')->result_array();
        $existing_med_ids = array();
        $existing_inactive_med_ids = array();
        foreach ($existing as $e) {
            $mid = (int) $e['medicine_id'];
            $existing_med_ids[] = $mid;
            if ((int) $e['is_active'] === 0) {
                $existing_inactive_med_ids[] = $mid;
            }
        }

        $saved = 0;
        $skipped = 0;
        $hasUniversal = false;
        $universalReason = '';
        $broadAreaSeries = trim((string) $broad_area_series);
        foreach ($medicines as $m) {
            $medicine_id = (int) ($m['medicine_id'] ?? 0);
            $series     = strtoupper(trim((string) ($m['series'] ?? '')));
            $variant    = (int) ($m['variant'] ?? 1);
            $reason     = trim((string) ($m['reason'] ?? ''));
            $is_fx      = ($series === 'FX');
            $is_e       = ($series === 'E');
            if ($medicine_id <= 0 || $series === '') {
                $skipped++;
                continue;
            }
            if ($is_fx || $is_e) {
                $hasUniversal = true;
                if ($reason !== '') {
                    $universalReason = $reason;
                }
            }
            // FX/E medicines require a prescribing reason.
            if (($is_fx || $is_e) && $reason === '') {
                $skipped++;
                continue;
            }
            // Skip if already active prescribed for this visit.
            if (in_array($medicine_id, $existing_med_ids, true)) {
                // Reactivate if it was previously deactivated.
                if (in_array($medicine_id, $existing_inactive_med_ids, true)) {
                    $this->db->where('visit_id', $visit_id)->where('medicine_id', $medicine_id)
                        ->update('patient_flow_prescription', array('is_active' => 1));
                    $saved++;
                }
                $skipped++;
                continue;
            }
            // E medicines are free at the first stage of each quarter.
            $payment_confirmed = 0;
            if ($is_e) {
                $stage = $this->get_stage_from_visit($visit_id);
                $stage_code = strtoupper(trim((string) ($stage['stage_code'] ?: '')));
                if (preg_match('/^Q[1-4]S1$/', $stage_code)) {
                    $payment_confirmed = 1;
                }
            }
            $data = array(
                'visit_id' => $visit_id, 'medicine_id' => $medicine_id, 'series' => $series,
                'variant' => $variant, 'prescribed_by' => $staff_id,
                'prescribed_at' => date('Y-m-d H:i:s'), 'clinical_approval' => 'pending',
                'payment_confirmed' => $payment_confirmed, 'fx_second_senior_approval' => ($is_fx ? 0 : 0),
                'name_hidden' => 1, 'is_active' => 1,
            );
            $this->db->insert('patient_flow_prescription', $data);
            $saved++;
        }
        // Delete/deactivate prescriptions for medicines that are no longer checked (unselected).
        $checkedMedIds = array();
        foreach ($medicines as $m) {
            $mid = (int) ($m['medicine_id'] ?? 0);
            if ($mid > 0) {
                $checkedMedIds[] = $mid;
            }
        }
        if (!empty($checkedMedIds)) {
            $this->db->where('visit_id', $visit_id)->where('is_active', 1)
                ->where_not_in('medicine_id', $checkedMedIds)
                ->update('patient_flow_prescription', array('is_active' => 0));
        } else {
            // No medicines selected: deactivate all prescriptions for this visit.
            $this->db->where('visit_id', $visit_id)->where('is_active', 1)
                ->update('patient_flow_prescription', array('is_active' => 0));
        }
        // Save reason and broad_area_series on the visit record if any FX/E medicine was prescribed
        if ($hasUniversal && $universalReason !== '') {
            $this->db->where('id', $visit_id)->update('patient_flow_visits', array('reason' => $universalReason));
        }
        if ($broadAreaSeries !== '') {
            $this->db->where('id', $visit_id)->update('patient_flow_visits', array('broad_area_series' => $broadAreaSeries));
        }
        $msg = "$saved medicine(s) prescribed.";
        if ($skipped > 0) {
            $msg .= " $skipped skipped (already prescribed or missing reason for FX/E medicine).";
        }
        return array('status' => true, 'message' => $msg, 'saved' => $saved, 'skipped' => $skipped);
    }

    /**
     * Get prescribed medicines for a visit for invoice printing.
     */
    public function get_visit_medicines($visit_id)
    {
        if (!$this->db->table_exists('patient_flow_prescription')) {
            return array();
        }

        $visit_id = (int) $visit_id;
        if ($visit_id <= 0) {
            return array();
        }

        $this->db->select('pp.id, pp.medicine_id, pp.series, pp.variant, mp.code, mp.medicine_identity, ph.medicine_name as pharmacy_name');
        $this->db->from('patient_flow_prescription pp');
        $this->db->join('medicine_master mp', 'mp.id = pp.medicine_id', 'left');
        $this->db->join('pharmacy ph', 'ph.id = mp.pharmacy_id', 'left');
        $this->db->where('pp.visit_id', $visit_id);
        $this->db->where('pp.is_active', 1);
        $this->db->order_by('pp.id', 'asc');
        return $this->db->get()->result_array();
    }

    /**
     * Get prescribed medicines for vial label printing.
     * Includes batch details for vial number.
     */
    public function get_visit_medicines_for_label($visit_id)
    {
        if (!$this->db->table_exists('patient_flow_prescription')) {
            return array();
        }

        $visit_id = (int) $visit_id;
        if ($visit_id <= 0) {
            return array();
        }

        $this->db->select('pp.id, pp.medicine_id, pp.series, pp.variant, pp.prescribed_at,
                           mp.code, mp.medicine_identity, mp.timing, mp.series, ph.medicine_name as pharmacy_name,
                           mbd.batch_no, mbd.expiry');
        $this->db->from('patient_flow_prescription pp');
        $this->db->join('medicine_master mp', 'mp.id = pp.medicine_id', 'left');
        $this->db->join('pharmacy ph', 'ph.id = mp.pharmacy_id', 'left');
        $this->db->join('medicine_batch_details mbd', 'mbd.pharmacy_id = ph.id', 'left');
        $this->db->where('pp.visit_id', $visit_id);
        $this->db->where('pp.is_active', 1);
        $this->db->order_by('pp.id', 'asc');
        return $this->db->get()->result_array();
    }

    // ============ Step 7: Dispensing Gate ============

    /**
     * Check if dispensing is allowed for a medicine on a visit.
     * Requires: a PAID schedule row whose stage range covers the current stage,
     * and clinical approval.
     */
    public function check_dispensing_gate($visit_id, $medicine_id = 0)
    {
        $visit_id = (int) $visit_id;
        if ($visit_id <= 0) {
            return array('status' => false, 'message' => 'Invalid visit.');
        }

        // Get current stage of the visit
        $stage = $this->get_stage_from_visit($visit_id);
        $current_stage_id = (int) ($stage['stage_id'] ?: 0);
        if ($current_stage_id <= 0) {
            return array('status' => false, 'message' => 'No current stage assigned to visit.');
        }

        // Get patient_id from visit
        $visitRow = $this->db->where('id', $visit_id)->get('patient_flow_visits')->row();
        if (!$visitRow) {
            return array('status' => false, 'message' => 'Visit not found.');
        }

        // Dispensing gate: stage check only (payment checks disabled for now)
        return array('status' => true, 'message' => 'Dispensing gate passed.', 'schedule_id' => 0);
    }

    /**
     * Check clinical approval status for a visit's pending prescriptions.
     */
    public function check_clinical_approval($visit_id)
    {
        if (!$this->db->table_exists('patient_flow_prescription')) {
            return array('status' => false, 'message' => 'Prescription table not found.');
        }
        $visit_id = (int) $visit_id;
        $pending = $this->db->where('visit_id', $visit_id)
            ->where('is_active', 1)
            ->where('clinical_approval', 'pending')
            ->count_all_results('patient_flow_prescription');
        if ($pending > 0) {
            return array('status' => false, 'message' => $pending . ' prescription(s) pending clinical approval.');
        }
        return array('status' => true, 'message' => 'All prescriptions clinically approved.');
    }

    /**
     * Approve clinical review of a prescription (senior staff only).
     */
    public function approve_clinical($prescription_id, $senior_id, $approve = true)
    {
        if (!$this->db->table_exists('patient_flow_prescription')) {
            return array('status' => false, 'message' => 'Prescription table not found.');
        }
        $prescription_id = (int) $prescription_id;
        $senior_id       = (int) $senior_id;
        $approve         = (bool) $approve;
        $row = $this->db->where('id', $prescription_id)->get('patient_flow_prescription')->row();
        if (!$row) { return array('status' => false, 'message' => 'Prescription not found.'); }
        $this->db->where('id', $prescription_id)->update('patient_flow_prescription', array(
            'clinical_approval' => ($approve ? 'approved' : 'rejected'),
            'clinical_approved_by' => $senior_id,
            'clinical_approved_at' => date('Y-m-d H:i:s'),
        ));
        return array('status' => true, 'message' => ($approve ? 'Clinical approval granted.' : 'Clinical approval rejected.'));
    }

    public function fx_second_approval($prescription_id, $senior_id)
    {
        if (!$this->db->table_exists('patient_flow_prescription')) {
            return array('status' => false, 'message' => 'Prescription table not found.');
        }
        $prescription_id = (int) $prescription_id;
        $senior_id       = (int) $senior_id;
        $row = $this->db->where('id', $prescription_id)->get('patient_flow_prescription')->row();
        if (!$row) { return array('status' => false, 'message' => 'Prescription not found.'); }
        if ((string) $row->series !== 'FX') {
            return array('status' => false, 'message' => 'Not an FX medicine.');
        }
        $this->db->where('id', $prescription_id)->update('patient_flow_prescription', array(
            'fx_second_senior_approval' => 1, 'fx_approved_by' => $senior_id,
        ));
        return array('status' => true, 'message' => 'FX second senior approval granted.');
    }

    public function get_prescriptions_for_visit($visit_id, $requester_role_name = '')
    {
        if (!$this->db->table_exists('patient_flow_prescription')) { return array(); }
        $visit_id = (int) $visit_id;
        $role = strtolower(trim((string) $requester_role_name));
        $hide_name_roles = array('junior','reception','patient','doctor');
        $hide_name = in_array($role, $hide_name_roles, true);
        $this->db->select('p.*, m.code AS medicine_code, m.medicine_identity, m.series AS med_series');
        $this->db->from('patient_flow_prescription p');
        $this->db->join('medicine_master m', 'm.id = p.medicine_id', 'left');
        $this->db->where('p.visit_id', $visit_id)->where('p.is_active', 1);
        $this->db->order_by('p.prescribed_at', 'asc');
        $rows = $this->db->get()->result();
        $result = array();
        foreach ($rows as $r) {
            $identity = (string) ($r->medicine_identity ?: '');
            if ($hide_name) { $identity = '—'; }
            // A prescription is orderable only after senior clinical approval
            // AND payment confirmation (or free E medicine at first stage).
            $is_orderable = ((string) $r->clinical_approval === 'approved' && (int) $r->payment_confirmed === 1);
            $result[] = array(
                'id' => (int) $r->id, 'medicine_id' => (int) $r->medicine_id,
                'medicine_code' => (string) ($r->medicine_code ?: ''),
                'medicine_identity' => $identity, 'series' => (string) ($r->series ?: ''),
                'variant' => (int) $r->variant, 'prescribed_by' => (int) $r->prescribed_by,
                'prescribed_at' => (string) $r->prescribed_at,
                'clinical_approval' => (string) $r->clinical_approval,
                'clinical_approved_by' => isset($r->clinical_approved_by) ? (int) $r->clinical_approved_by : null,
                'clinical_approved_at' => isset($r->clinical_approved_at) ? (string) $r->clinical_approved_at : null,
                'payment_confirmed' => (int) $r->payment_confirmed,
                'fx_second_senior_approval' => (int) $r->fx_second_senior_approval,
                'fx_approved_by' => isset($r->fx_approved_by) ? (int) $r->fx_approved_by : null,
                'name_hidden' => (int) $r->name_hidden, 'is_active' => (int) $r->is_active,
                'is_orderable' => (int) $is_orderable,
            );
        }
        return $result;
    }

    public function confirm_payment($prescription_id, $staff_id)
    {
        if (!$this->db->table_exists('patient_flow_prescription')) {
            return array('status' => false, 'message' => 'Prescription table not found.');
        }
        $this->db->where('id', (int) $prescription_id)
            ->update('patient_flow_prescription', array('payment_confirmed' => 1));
        return array('status' => true, 'message' => 'Payment confirmed.');
    }

    // ============ Step 5: Enrollment & Schedule Generation ============

    /**
     * Create a patient programme enrollment.
     */
    public function save_enrollment($patient_id, $programme_id, $coverage, $enrolled_on, $staff_id, $partner_patient_id = null)
    {
        if (!$this->db->table_exists('patient_flow_patient_programme_enrollment')) {
            return array('status' => false, 'message' => 'Enrollment table not found.');
        }
        $patient_id    = (int) $patient_id;
        $programme_id  = (int) $programme_id;
        $staff_id      = (int) $staff_id;
        $partner_id    = $partner_patient_id ? (int) $partner_patient_id : null;
        $coverage      = trim((string) $coverage);
        $enrolled_on   = trim((string) $enrolled_on);
        if ($patient_id <= 0 || $programme_id <= 0 || $coverage === '' || $enrolled_on === '') {
            return array('status' => false, 'message' => 'Invalid enrollment parameters.');
        }
        // Verify programme exists and is active
        $prog = $this->db->where('id', $programme_id)->where('is_active', 1)
            ->get('patient_flow_programme')->row();
        if (!$prog) {
            return array('status' => false, 'message' => 'Programme not found.');
        }
        // Verify patient exists
        $patient = $this->db->where('id', $patient_id)->get('patients')->row();
        if (!$patient) {
            return array('status' => false, 'message' => 'Patient not found.');
        }
        // Generate UID
        $uid = 'PF-' . date('Ymd') . '-' . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
        // Make UID unique
        while ($this->db->where('uid', $uid)->get('patient_flow_patient_programme_enrollment')->row()) {
            $uid = 'PF-' . date('Ymd') . '-' . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
        }
        $this->db->insert('patient_flow_patient_programme_enrollment', array(
            'uid'              => $uid,
            'patient_id'       => $patient_id,
            'partner_patient_id' => $partner_id,
            'programme_id'     => $programme_id,
            'coverage'         => $coverage,
            'enrolled_on'      => $enrolled_on,
            'status'           => 'ACTIVE',
            'created_by'       => $staff_id,
            'created_at'       => date('Y-m-d H:i:s'),
        ));
        $enrollment_id = (int) $this->db->insert_id();
        $this->write_fee_audit_log('patient_flow_patient_programme_enrollment', $enrollment_id, 'create',
            null, json_encode(array('programme_id' => $programme_id, 'coverage' => $coverage, 'enrolled_on' => $enrolled_on)), $staff_id);
        return array('status' => true, 'message' => 'Enrollment created.', 'id' => $enrollment_id, 'uid' => $uid);
    }

    /**
     * Save enrollment benefit snapshot (immutable).
     */
    public function save_enrollment_benefit($enrollment_id, $source, $source_id, $discount_percent, $payment_mode, $valid_from_quarter, $valid_to_quarter, $candidates_json, $staff_id, $stagewise_approved_by = null, $stagewise_approved_at = null)
    {
        if (!$this->db->table_exists('patient_flow_enrollment_benefit')) {
            return array('status' => false, 'message' => 'Enrollment benefit table not found.');
        }
        $enrollment_id = (int) $enrollment_id;
        // Mark any existing current benefit as not current
        $this->db->where('enrollment_id', $enrollment_id)->where('is_current', 1)
            ->update('patient_flow_enrollment_benefit', array('is_current' => 0));
        $this->db->insert('patient_flow_enrollment_benefit', array(
            'enrollment_id'        => $enrollment_id,
            'source'               => $source,
            'source_id'            => (int) $source_id,
            'discount_percent'     => (float) $discount_percent,
            'valid_from_quarter'   => (int) $valid_from_quarter,
            'valid_to_quarter'     => (int) $valid_to_quarter,
            'payment_mode'         => $payment_mode,
            'stagewise_approved_by'=> $stagewise_approved_by ? (int) $stagewise_approved_by : null,
            'stagewise_approved_at'=> $stagewise_approved_at,
            'candidates_json'      => $candidates_json,
            'is_current'           => 1,
            'created_by'           => (int) $staff_id,
            'created_at'           => date('Y-m-d H:i:s'),
        ));
        $id = (int) $this->db->insert_id();
        $this->write_fee_audit_log('patient_flow_enrollment_benefit', $id, 'create', null,
            json_encode(array('source' => $source, 'source_id' => $source_id, 'discount_percent' => $discount_percent)), $staff_id);
        return array('status' => true, 'message' => 'Benefit snapshot saved.', 'id' => $id);
    }

    /**
     * Generate payment schedule rows for an enrollment using Fee_engine.
     */
    public function generate_payment_schedule_for_enrollment($enrollment_id, $quarter_no = 1)
    {
        if (!$this->db->table_exists('patient_flow_programme_payment_schedule')) {
            return array('status' => false, 'message' => 'Payment schedule table not found.');
        }
        $enrollment_id = (int) $enrollment_id;
        $quarter_no    = (int) $quarter_no;
        // Get enrollment
        $enr = $this->db->where('id', $enrollment_id)->get('patient_flow_patient_programme_enrollment')->row();
        if (!$enr) {
            return array('status' => false, 'message' => 'Enrollment not found.');
        }
        // Get programme + group
        $prog = $this->db->where('id', $enr->programme_id)->get('patient_flow_programme')->row();
        if (!$prog) {
            return array('status' => false, 'message' => 'Programme not found.');
        }
        $group = $this->db->where('id', $prog->group_id)->get('patient_flow_programme_group')->row();
        if (!$group) {
            return array('status' => false, 'message' => 'Programme group not found.');
        }
        // Get current benefit
        $benefit = $this->db->where('enrollment_id', $enrollment_id)->where('is_current', 1)
            ->get('patient_flow_enrollment_benefit')->row();
        // Get milestones for this group/coverage
        $milestones = $this->db->where('group_id', $prog->group_id)
            ->where('coverage', $enr->coverage)
            ->where('is_active', 1)
            ->get('patient_flow_programme_fee_milestone')->result();
        // Get Q1 stages if needed
        $stages = array();
        if ($quarter_no === 1) {
            $this->db->from('stage_master');
            $this->db->where('quarter', 'Q1');
            $this->db->where('is_active', 1);
            $this->db->order_by('stage', 'asc');
            $stages = $this->db->get()->result();
        }
        // Determine quarter fee amount
        $quarter_fee = 0;
        if ($quarter_no === 1) {
            $m = $this->db->where('group_id', $prog->group_id)
                ->where('coverage', $enr->coverage)
                ->where('milestone_code', 'Q1_INITIAL')
                ->where('is_active', 1)
                ->get('patient_flow_programme_fee_milestone')->row();
            $quarter_fee = $m ? (float) $m->amount : 0;
        } else {
            $m = $this->db->where('group_id', $prog->group_id)
                ->where('coverage', $enr->coverage)
                ->where('milestone_code', 'LATER_QUARTER')
                ->where('is_active', 1)
                ->get('patient_flow_programme_fee_milestone')->row();
            $quarter_fee = $m ? (float) $m->amount : 0;
        }
        // Build benefit array for Fee_engine
        $benefit_arr = array(
            'source'             => $benefit ? $benefit->source : null,
            'source_id'          => $benefit ? (int) $benefit->source_id : null,
            'discount_percent'   => $benefit ? (float) $benefit->discount_percent : 0.0,
            'payment_mode'       => $benefit ? $benefit->payment_mode : 'MILESTONE',
            'valid_from_quarter' => $benefit ? (int) $benefit->valid_from_quarter : $quarter_no,
            'valid_to_quarter'   => $benefit ? (int) $benefit->valid_to_quarter : $quarter_no,
        );
        // Load Fee_engine library
        $this->CI->load->library('fee_engine');
        $rows = $this->CI->fee_engine->generate_schedule($enrollment_id, $quarter_no, $benefit_arr, $milestones, $stages, $quarter_fee);
        // Insert schedule rows
        $inserted = 0;
        foreach ($rows as $row) {
            $this->db->insert('patient_flow_programme_payment_schedule', $row);
            $inserted++;
        }
        $this->write_fee_audit_log('patient_flow_programme_payment_schedule', $enrollment_id, 'generate_schedule',
            null, json_encode(array('quarter_no' => $quarter_no, 'rows' => count($rows))), $enr->created_by);
        return array('status' => true, 'message' => 'Schedule generated: ' . $inserted . ' row(s).', 'rows' => $inserted);
    }

    /**
     * Get enrollment by ID with related info.
     */
    public function get_enrollment($enrollment_id)
    {
        if (!$this->db->table_exists('patient_flow_patient_programme_enrollment')) {
            return null;
        }
        $this->db->select('e.*, p.patient_name, p.patient_uuid, pm.code AS programme_code, pm.name AS programme_name');
        $this->db->from('patient_flow_patient_programme_enrollment e');
        $this->db->join('patients p', 'p.id = e.patient_id', 'left');
        $this->db->join('patient_flow_programme pm', 'pm.id = e.programme_id', 'left');
        $this->db->where('e.id', (int) $enrollment_id);
        return $this->db->get()->row();
    }

    /**
     * Get all enrollments with related info.
     */
    public function get_enrollments($status = null)
    {
        if (!$this->db->table_exists('patient_flow_patient_programme_enrollment')) {
            return array();
        }
        $this->db->select('e.*, p.patient_name, p.patient_uuid, pm.code AS programme_code, pg.code AS group_code');
        $this->db->from('patient_flow_patient_programme_enrollment e');
        $this->db->join('patients p', 'p.id = e.patient_id', 'left');
        $this->db->join('patient_flow_programme pm', 'pm.id = e.programme_id', 'left');
        $this->db->join('patient_flow_programme_group pg', 'pg.id = pm.group_id', 'left');
        if ($status) {
            $this->db->where('e.status', $status);
        }
        $this->db->order_by('e.created_at', 'desc');
        return $this->db->get()->result();
    }

    /* ========================================================================
       END ENROLLMENT & SCHEDULE GENERATION MODEL METHODS
       ======================================================================== */

    // ============ Step 6: Billing Integration ============

    /**
     * Get fee item master details by fee_code.
     */
    public function get_fee_item($fee_code)
    {
        if (!$this->db->table_exists('patient_flow_fee_item_master')) {
            return null;
        }
        return $this->db->where('fee_code', $fee_code)
            ->where('is_active', 1)
            ->get('patient_flow_fee_item_master')
            ->row();
    }

    /**
     * Get all fee items for fee master list.
     */
    public function get_fee_items()
    {
        if (!$this->db->table_exists('patient_flow_fee_master')) {
            return [];
        }
        $this->db->select('id, gst_type, coverage, group_code, code AS fee_code, fee_type, amount AS consultation_fee, gst_rate, name AS description, note, is_active');
        return $this->db->get('patient_flow_fee_master')->result();
    }

    /**
     * Get all discounts from patient_flow_fee_discount_master.
     */
    public function get_discounts()
    {
        if (!$this->db->table_exists('patient_flow_fee_discount_master')) {
            return [];
        }
        return $this->db->get('patient_flow_fee_discount_master')->result();
    }

    /**
     * Get all patient fee accounts.
     */
    public function get_patient_fee_accounts()
    {
        if (!$this->db->table_exists('patient_flow_patient_fee_account')) {
            return [];
        }
        return $this->db->get('patient_flow_patient_fee_account')->result();
    }

    /**
     * Get all patient fee schedules.
     */
    public function get_patient_fee_schedules()
    {
        if (!$this->db->table_exists('patient_flow_patient_fee_schedule')) {
            return [];
        }
        return $this->db->get('patient_flow_patient_fee_schedule')->result();
    }

    /**
     * Get a single patient fee schedule row by id.
     */
    public function get_patient_fee_schedule_by_id($id)
    {
        if (!$this->db->table_exists('patient_flow_patient_fee_schedule')) {
            return null;
        }
        return $this->db->where('id', (int) $id)->get('patient_flow_patient_fee_schedule')->row();
    }

    /**
     * Save (insert or update) a patient fee schedule row.
     */
    public function save_patient_fee_schedule($id, $data)
    {
        if (!$this->db->table_exists('patient_flow_patient_fee_schedule')) {
            return array('status' => false, 'message' => 'Schedule table not found.');
        }
        $id = (int) $id;
        $account_id = (int) ($data['account_id'] ?? 0);
        $quarter_no = (int) ($data['quarter_no'] ?? 0);
        $stage_from = (int) ($data['stage_from'] ?? 0);
        $stage_to = (int) ($data['stage_to'] ?? 0);
        $label = trim((string) ($data['label'] ?? ''));
        $standard_amount = (float) ($data['standard_amount'] ?? 0);
        $discount_percent = (float) ($data['discount_percent'] ?? 0);
        $payable_amount = (float) ($data['payable_amount'] ?? 0);
        $status = strtoupper(trim((string) ($data['status'] ?? 'PENDING')));
        if (!in_array($status, array('PENDING', 'PAID', 'CANCELLED'), true)) {
            $status = 'PENDING';
        }
        if ($account_id <= 0) {
            return array('status' => false, 'message' => 'Account is required.');
        }
        if ($this->db->table_exists('patient_flow_patient_fee_account')) {
            $acct = $this->db->where('id', $account_id)->get('patient_flow_patient_fee_account')->row();
            if (!$acct) {
                return array('status' => false, 'message' => 'Selected account does not exist.');
            }
        }
        if ($quarter_no <= 0) {
            return array('status' => false, 'message' => 'Quarter number is required.');
        }
        if ($label === '') {
            return array('status' => false, 'message' => 'Label is required.');
        }
        $row = array(
            'account_id'       => $account_id,
            'quarter_no'       => $quarter_no,
            'stage_from'       => $stage_from,
            'stage_to'         => $stage_to,
            'label'            => $label,
            'standard_amount'  => $standard_amount,
            'discount_percent' => $discount_percent,
            'payable_amount'   => $payable_amount,
            'status'           => $status,
        );
        if ($id > 0) {
            $this->db->where('id', $id)->update('patient_flow_patient_fee_schedule', $row);
            return array('status' => true, 'message' => 'Schedule updated.', 'id' => $id);
        }
        $this->db->insert('patient_flow_patient_fee_schedule', $row);
        return array('status' => true, 'message' => 'Schedule added.', 'id' => $this->db->insert_id());
    }

    /**
     * Delete a patient fee schedule row.
     */
    public function delete_patient_fee_schedule($id)
    {
        if (!$this->db->table_exists('patient_flow_patient_fee_schedule')) {
            return array('status' => false, 'message' => 'Schedule table not found.');
        }
        $id = (int) $id;
        if ($id <= 0) {
            return array('status' => false, 'message' => 'Invalid id.');
        }
        $this->db->where('id', $id)->delete('patient_flow_patient_fee_schedule');
        return array('status' => true, 'message' => 'Schedule deleted.');
    }

    /**
     * Get consultation & vial fees (ITEM type fees).
     */
    public function get_consultation_fees()
    {
        if (!$this->db->table_exists('patient_flow_fee_master')) {
            return [];
        }
        $this->db->where('fee_type', 'ITEM');
        return $this->db->get('patient_flow_fee_master')->result();
    }

    /**
     * Save (insert or update) a fee master item.
     */
    public function save_fee_item($data, $id = 0)
    {
        if (!$this->db->table_exists('patient_flow_fee_master')) {
            return array('status' => false, 'message' => 'Fee master table not found.');
        }

        $id = (int) $id;
        $fee_type   = trim((string) ($data['fee_type'] ?? 'ITEM'));
        $code       = strtoupper(trim((string) ($data['code'] ?? '')));
        $name       = trim((string) ($data['name'] ?? ''));
        $group_code = trim((string) ($data['group_code'] ?? ''));
        $coverage   = trim((string) ($data['coverage'] ?? ''));
        $amount     = (float) ($data['amount'] ?? 0);
        $gst_type   = trim((string) ($data['gst_type'] ?? 'NONE'));
        $gst_rate   = (float) ($data['gst_rate'] ?? 0);
        $note       = trim((string) ($data['note'] ?? ''));
        $is_active  = isset($data['is_active']) ? ((string) $data['is_active'] === '1' ? 1 : 0) : 1;

        if ($code === '' || $name === '') {
            return array('status' => false, 'message' => 'Code and Name are required.');
        }

        $save_data = array(
            'fee_type'   => $fee_type,
            'code'       => $code,
            'name'       => $name,
            'group_code' => $group_code,
            'coverage'   => $coverage,
            'amount'     => $amount,
            'gst_type'   => $gst_type,
            'gst_rate'   => $gst_rate,
            'note'       => $note,
            'is_active'  => $is_active,
        );

        if ($id > 0) {
            $this->db->where('id', $id);
            $this->db->update('patient_flow_fee_master', $save_data);
            return array('status' => true, 'message' => 'Fee item updated successfully');
        } else {
            // Check for duplicate (fee_type, code, group_code, coverage)
            $existing = $this->db->where('fee_type', $fee_type)
                ->where('code', $code)
                ->where('group_code', $group_code)
                ->where('coverage', $coverage)
                ->get('patient_flow_fee_master')
                ->row();
            if ($existing) {
                return array('status' => false, 'message' => 'A fee with this type, code, group and coverage already exists.');
            }
            $this->db->insert('patient_flow_fee_master', $save_data);
            return array('status' => true, 'message' => 'Fee item created successfully');
        }
    }

    /**
     * Delete a fee master item.
     */
    public function delete_fee_item($id)
    {
        if (!$this->db->table_exists('patient_flow_fee_master')) {
            return array('status' => false, 'message' => 'Fee master table not found.');
        }
        $id = (int) $id;
        $this->db->where('id', $id);
        $this->db->delete('patient_flow_fee_master');
        return array('status' => true, 'message' => 'Fee item deleted successfully');
    }

    /**
     * Toggle is_active flag on a fee master item.
     */
    public function toggle_fee_item($id)
    {
        if (!$this->db->table_exists('patient_flow_fee_master')) {
            return array('status' => false, 'message' => 'Fee master table not found.');
        }
        $id = (int) $id;
        $row = $this->db->where('id', $id)->get('patient_flow_fee_master')->row();
        if (!$row) {
            return array('status' => false, 'message' => 'Fee item not found.');
        }
        $new_status = ($row->is_active == 1) ? 0 : 1;
        $this->db->where('id', $id)->update('patient_flow_fee_master', array('is_active' => $new_status));
        return array('status' => true, 'message' => 'Fee item ' . ($new_status ? 'activated' : 'deactivated') . ' successfully');
    }


    /**
     * Get a single fee master item by ID.
     */
    public function get_fee_item_by_id($id)
    {
        if (!$this->db->table_exists('patient_flow_fee_master')) {
            return null;
        }
        return $this->db->where('id', (int) $id)->get('patient_flow_fee_master')->row();
    }

    /**
     * Check if patient has an ACTIVE programme enrollment.
     */
    public function has_active_enrollment($patient_id)
    {
        if (!$this->db->table_exists('patient_flow_patient_programme_enrollment')) {
            return false;
        }
        $row = $this->db->where('patient_id', (int) $patient_id)
            ->where('status', 'ACTIVE')
            ->get('patient_flow_patient_programme_enrollment')
            ->row();
        return (bool) $row;
    }

    /**
     * Create a new fee invoice.
     */
    public function create_fee_invoice($patient_id, $invoice_type, $staff_id)
    {
        if (!$this->db->table_exists('patient_flow_fee_invoice')) {
            return array('status' => false, 'message' => 'Fee invoice table not found.');
        }
        $patient_id = (int) $patient_id;
        $staff_id   = (int) $staff_id;
        if ($patient_id <= 0 || $staff_id <= 0) {
            return array('status' => false, 'message' => 'Invalid parameters.');
        }
        $invoice_no = $this->generate_invoice_no();
        $this->db->insert('patient_flow_fee_invoice', array(
            'invoice_no'    => $invoice_no,
            'invoice_type'  => $invoice_type,
            'patient_id'    => $patient_id,
            'enrollment_id' => null,
            'invoice_date'  => date('Y-m-d H:i:s'),
            'subtotal'      => 0,
            'gst_total'     => 0,
            'grand_total'   => 0,
            'status'        => 'PENDING',
            'created_by'    => $staff_id,
        ));
        $invoice_id = (int) $this->db->insert_id();
        $this->write_fee_audit_log('patient_flow_fee_invoice', $invoice_id, 'create', null,
            json_encode(array('invoice_no' => $invoice_no, 'invoice_type' => $invoice_type)), $staff_id);
        return array('status' => true, 'message' => 'Invoice created.', 'id' => $invoice_id, 'invoice_no' => $invoice_no);
    }

    /**
     * Add a line item to a fee invoice (recalculates totals).
     */
    public function add_fee_invoice_line($invoice_id, $line_type, $description, $qty, $unit_amount,
        $discount_amount = 0, $taxable_amount = 0, $gst_type = 'EXEMPT', $gst_rate = 0,
        $gst_amount = 0, $fee_code = null, $schedule_id = null)
    {
        if (!$this->db->table_exists('patient_flow_fee_invoice_line')) {
            return array('status' => false, 'message' => 'Invoice line table not found.');
        }
        $invoice_id = (int) $invoice_id;
        $qty        = (float) $qty;
        $unit_amount= (float) $unit_amount;
        $line_total = (float) (($qty * $unit_amount) - $discount_amount + $gst_amount);
        $this->db->insert('patient_flow_fee_invoice_line', array(
            'invoice_id'    => $invoice_id,
            'line_type'     => $line_type,
            'fee_code'      => $fee_code,
            'schedule_id'   => $schedule_id ? (int) $schedule_id : null,
            'description'   => trim((string) $description),
            'qty'           => $qty,
            'unit_amount'   => $unit_amount,
            'discount_amount'=> (float) $discount_amount,
            'taxable_amount'=> (float) $taxable_amount,
            'gst_type'      => $gst_type,
            'gst_rate'      => (float) $gst_rate,
            'gst_amount'    => (float) $gst_amount,
            'line_total'    => $line_total,
        ));
        $line_id = (int) $this->db->insert_id();
        // Recalculate invoice totals
        $this->db->select('SUM(line_total) AS total, SUM(gst_amount) AS gst_total, SUM(taxable_amount) AS subtotal');
        $this->db->where('invoice_id', $invoice_id);
        $totals = $this->db->get('patient_flow_fee_invoice_line')->row();
        $this->db->where('id', $invoice_id)->update('patient_flow_fee_invoice', array(
            'subtotal'   => (float) ($totals->subtotal ?? 0),
            'gst_total'  => (float) ($totals->gst_total ?? 0),
            'grand_total'=> (float) ($totals->total ?? 0),
        ));
        $this->write_fee_audit_log('patient_flow_fee_invoice_line', $line_id, 'create', null,
            json_encode(array('invoice_id' => $invoice_id, 'line_type' => $line_type, 'description' => $description, 'line_total' => $line_total)), 0);
        return array('status' => true, 'message' => 'Invoice line added.', 'id' => $line_id, 'line_total' => $line_total);
    }

    /**
     * Generate a consultation invoice for a patient visit.
     * Consultation is always a separate invoice, GST exempt, not discounted.
     */
    public function generate_consultation_invoice($patient_id, $visit_id, $staff_id, $fee_code = 'CON-IC-N')
    {
        if (!$this->db->table_exists('patient_flow_fee_invoice')) {
            return array('status' => false, 'message' => 'Fee invoice table not found.');
        }
        $patient_id = (int) $patient_id;
        $visit_id   = (int) $visit_id;
        $staff_id   = (int) $staff_id;
        if ($patient_id <= 0 || $visit_id <= 0 || $staff_id <= 0) {
            return array('status' => false, 'message' => 'Invalid parameters.');
        }
        $item = $this->get_fee_item($fee_code);
        if (!$item) {
            return array('status' => false, 'message' => 'Fee item not found: ' . $fee_code);
        }
        $inv = $this->create_fee_invoice($patient_id, 'CONSULTATION', $staff_id);
        if (!$inv['status']) {
            return $inv;
        }
        $invoice_id = $inv['id'];
        $this->add_fee_invoice_line($invoice_id, 'CONSULTATION',
            $item->item_name . ' (' . $item->fee_code . ')',
            1, $item->base_amount,
            0, $item->base_amount, 'EXEMPT', 0, 0, $item->fee_code, null);
        // Link invoice to visit via billing fields if available
        if ($this->db->field_exists('invoice_no', 'patient_flow_visits')) {
            $this->db->where('id', $visit_id)
                ->update('patient_flow_visits', array(
                    'invoice_no'      => $inv['invoice_no'],
                    'invoice_no_at'   => date('Y-m-d H:i:s'),
                    'billing_status'  => 'billed',
                ));
        }
        return array('status' => true, 'message' => 'Consultation invoice generated.', 'invoice_id' => $invoice_id, 'invoice_no' => $inv['invoice_no'], 'grand_total' => $item->base_amount);
    }

    /**
     * Add a vial line item to an invoice based on medicine's container_vial.
     * Free E set at first stage of quarter produces no fee line.
     * E001–E005 as extras; E006–E007 always produce VIAL-E line.
     */
    public function add_vial_line_to_invoice($invoice_id, $medicine_id, $staff_id, $is_free_first_stage_e = false)
    {
        if (!$this->db->table_exists('medicine_master')) {
            return array('status' => false, 'message' => 'Medicine master table not found.');
        }
        $medicine_id = (int) $medicine_id;
        $med = $this->db->where('id', $medicine_id)->get('medicine_master')->row();
        if (!$med) {
            return array('status' => false, 'message' => 'Medicine not found.');
        }
        $container_vial = trim((string) ($med->container_vial ?: ''));
        $series         = strtoupper(trim((string) ($med->series ?: '')));
        $code           = trim((string) ($med->code ?: ''));

        // Free E at first stage produces no fee line
        if ($is_free_first_stage_e && $series === 'E' && in_array($code, array('E001','E002','E003','E004','E005'))) {
            return array('status' => true, 'message' => 'Free E-series medicine at first stage — no fee line.', 'skipped' => true);
        }

        $fee_code = null;
        $item_name = '';
        if ($container_vial === '5 ml vial' || $container_vial === '5 ml') {
            $fee_code = 'VIAL-5';
            $item_name = 'Vial (5 ml)';
        } elseif ($container_vial === '20 ml vial' || $container_vial === '20 ml') {
            $fee_code = 'VIAL-20';
            $item_name = 'Vial (20 ml)';
        } elseif ($series === 'E' && in_array($code, array('E006','E007'))) {
            $fee_code = 'VIAL-E';
            $item_name = 'Vial-E';
        }

        if (!$fee_code) {
            return array('status' => false, 'message' => 'No applicable vial fee for container: ' . $container_vial);
        }
        $item = $this->get_fee_item($fee_code);
        if (!$item) {
            return array('status' => false, 'message' => 'Fee item not found: ' . $fee_code);
        }
        return $this->add_fee_invoice_line($invoice_id, 'VIAL',
            $item_name . ' (' . $item->fee_code . ')',
            1, $item->base_amount,
            0, $item->base_amount, $item->gst_type, (float) $item->gst_rate, 0, $item->fee_code, null);
    }

    /**
     * Add duplicate booklet item line to invoice.
     */
    public function add_booklet_line_to_invoice($invoice_id, $staff_id)
    {
        return $this->add_fee_invoice_line($invoice_id, 'ITEM',
            'Duplicate PBHP booklet (PBHP-DUP)',
            1, 1100, 0, 1100, 'CONFIGURED', 0, 0, 'PBHP-DUP', null);
    }

    /**
     * Mark payment schedule row as PAID (alias for mark_payment_paid with confirmation).
     */
    public function confirm_payment_schedule($schedule_id, $staff_id, $payment_ref = '')
    {
        return $this->mark_payment_paid($schedule_id, $staff_id, $payment_ref);
    }

    /* ========================================================================
       END BILLING INTEGRATION MODEL METHODS
       ======================================================================== */

    // ============ Step 8: Reports ============

    /**
     * Audit trail report — from patient_flow_fee_audit_log.
     */
    public function get_audit_trail($limit = 100, $offset = 0, $entity = null, $action = null)
    {
        if (!$this->db->table_exists('patient_flow_fee_audit_log')) {
            return array();
        }
        $this->db->select('*');
        $this->db->from('patient_flow_fee_audit_log');
        if ($entity) {
            $this->db->where('entity', $entity);
        }
        if ($action) {
            $this->db->where('action', $action);
        }
        $this->db->order_by('created_at', 'desc');
        $this->db->limit($limit, $offset);
        return $this->db->get()->result();
    }

    /**
     * Discount utilisation report — by assistance category and campaign.
     */
    public function get_discount_utilisation()
    {
        $result = array(
            'assistance' => array(),
            'campaign'   => array(),
        );

        // Assistance utilisation: count of approved assistance requests per category
        if ($this->db->table_exists('patient_flow_fee_assistance_request')) {
            $this->db->select('fac.category_code, COUNT(*) as request_count,
                SUM(CASE WHEN far.status = "APPROVED" THEN 1 ELSE 0 END) as approved_count');
            $this->db->from('patient_flow_fee_assistance_request far');
            $this->db->join('patient_flow_fee_assistance_category fac', 'fac.id = far.category_id', 'left');
            $this->db->group_by('fac.category_code');
            $result['assistance'] = $this->db->get()->result();
        }

        // Campaign utilisation: count of enrollments using each campaign
        if ($this->db->table_exists('patient_flow_enrollment_benefit')) {
            $this->db->select('dc.campaign_code, COUNT(*) as enrollment_count,
                AVG(eb.discount_percent) as avg_discount');
            $this->db->from('patient_flow_enrollment_benefit eb');
            $this->db->join('patient_flow_discount_campaign dc', 'dc.id = eb.source_id', 'left');
            $this->db->where('eb.source', 'CAMPAIGN');
            $this->db->group_by('dc.campaign_code');
            $result['campaign'] = $this->db->get()->result();
        }

        return $result;
    }

    /**
     * GST summary report — GST collected by type and rate.
     */
    public function get_gst_summary()
    {
        if (!$this->db->table_exists('patient_flow_fee_invoice_line')) {
            return array();
        }
        $this->db->select('gst_type, gst_rate, SUM(gst_amount) AS total_gst,
            SUM(taxable_amount) AS total_taxable, SUM(line_total) AS total_line,
            COUNT(*) AS line_count');
        $this->db->from('patient_flow_fee_invoice_line');
        $this->db->group_by('gst_type, gst_rate');
        $this->db->order_by('gst_type, gst_rate');
        return $this->db->get()->result();
    }

    /**
     * Pending-payment list — schedule rows with status = PENDING.
     */
    public function get_pending_payments($limit = 100, $offset = 0)
    {
        if (!$this->db->table_exists('patient_flow_programme_payment_schedule')) {
            return array();
        }
        $this->db->select('ps.*, e.uid AS enrollment_uid, p.patient_name, p.patient_uuid,
            pm.code AS programme_code, pm.name AS programme_name');
        $this->db->from('patient_flow_programme_payment_schedule ps');
        $this->db->join('patient_flow_patient_programme_enrollment e', 'e.id = ps.enrollment_id', 'left');
        $this->db->join('patients p', 'p.id = e.patient_id', 'left');
        $this->db->join('patient_flow_programme pm', 'pm.id = e.programme_id', 'left');
        $this->db->where('ps.status', 'PENDING');
        $this->db->order_by('ps.created_at', 'asc');
        $this->db->limit($limit, $offset);
        return $this->db->get()->result();
    }

    /* ========================================================================
       END REPORTS MODEL METHODS
       ======================================================================== */
}
