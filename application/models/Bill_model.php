<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Bill_model extends MY_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    public function add($bill_data,$module_data,$opd_ipd_transaction,$discount_percentage)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $this->db->insert("bill", $bill_data);

         $bill_id = $this->db->insert_id();

         if($opd_ipd_transaction['amount'] > 0){
         $opd_ipd_transaction['bill_id']=$bill_id;
         $this->db->insert("transactions", $opd_ipd_transaction);
         }
        $this->db->where("id", $bill_data["case_id"])->update("case_references", array('bill_id'=>$bill_id,'discount_percentage'=>$discount_percentage));

        if (!empty($module_data)) {
            # code...
                 foreach ($module_data as $m_key => $m_value) {
                    $module_data[$m_key]['bill_id']=$bill_id;
                 }
               
                $this->db->insert_batch('transactions', $module_data); 
        } 

        //======================Code End==============================

        $this->db->trans_complete(); # Completing transaction
        /* Optional */

        if ($this->db->trans_status() === false) {
            # Something went wrong.
            $this->db->trans_rollback();
            return false;
        } else {
            return true;
        }
    }

    /**
     * Patient dashboard — returns module-wise pending balance for a given patient.
     *
     * Mirrors the logic of Report_model::getmodulewisebalance_report (used by
     * /admin/report/balanceamountreport) but is patient-scoped and aggregated
     * per module so the dashboard's "Pending Bills" card shows ONE row per
     * module with the total outstanding balance across all bills in that
     * module.
     *
     * Raw SQL is unavoidable here: a UNION ALL across 7 module billing tables
     * with subquery aggregates, an outer GROUP BY and HAVING is not cleanly
     * expressible in CI Query Builder. The inner UNION block is ported from
     * Report_model line 519+ with these deliberate differences:
     *   - WHERE patients.id = ? added to every subquery (patient scope)
     *   - blood_bank uses ONLY the 'blood_bank_all_recored' variant — the admin
     *     report has 3 variants of the same blood_issue records (component
     *     breakdown) which would triple-count on a per-patient summary
     *   - outer SELECT wraps with GROUP BY type, SUM, HAVING balance > 0
     *
     * Balance formula matches the admin report exactly: net_amount - paid_amount.
     * Refunds are not subtracted (consistent with how the admin report computes
     * its balance column).
     *
     * Returns array of rows: [type, balance_amount, bill_count, latest_date]
     * sorted by balance_amount DESC. Empty array if no module has balance > 0.
     */
    public function getPatientModuleBalance($patient_id)
    {
        $patient_id = (int) $patient_id;
        if ($patient_id <= 0) {
            return array();
        }

        $sql = "
            SELECT type,
                   SUM(net_amount - paid_amount) AS balance_amount,
                   MAX(date_) AS latest_date,
                   COUNT(*) AS bill_count
            FROM (

                SELECT 'radiology_test' AS type,
                       patients.id AS patient_id,
                       radiology_billing.date AS date_,
                       IFNULL(radiology_billing.net_amount, 0) AS net_amount,
                       (SELECT IFNULL(SUM(transactions.amount),0)
                        FROM transactions
                        WHERE transactions.radiology_billing_id = radiology_billing.id) AS paid_amount
                FROM radiology_billing
                INNER JOIN patients ON patients.id = radiology_billing.patient_id
                WHERE patients.id = ?

                UNION ALL

                SELECT 'pathology_test' AS type,
                       patients.id AS patient_id,
                       pathology_billing.date AS date_,
                       IFNULL(pathology_billing.net_amount, 0) AS net_amount,
                       (SELECT IFNULL(SUM(transactions.amount),0)
                        FROM transactions
                        WHERE transactions.pathology_billing_id = pathology_billing.id) AS paid_amount
                FROM pathology_billing
                INNER JOIN patients ON patients.id = pathology_billing.patient_id
                WHERE patients.id = ?

                UNION ALL

                SELECT 'blood_bank' AS type,
                       patients.id AS patient_id,
                       NULL AS date_,
                       IFNULL(blood_issue.net_amount, 0) AS net_amount,
                       (SELECT IFNULL(SUM(transactions.amount),0)
                        FROM transactions
                        WHERE transactions.blood_issue_id = blood_issue.id) AS paid_amount
                FROM blood_issue
                INNER JOIN patients ON patients.id = blood_issue.patient_id
                WHERE patients.id = ?

                UNION ALL

                SELECT 'pharmacy_bill' AS type,
                       patients.id AS patient_id,
                       pharmacy_bill_basic.date AS date_,
                       IFNULL(pharmacy_bill_basic.net_amount, 0) AS net_amount,
                       (SELECT IFNULL(SUM(transactions.amount),0)
                        FROM transactions
                        WHERE transactions.pharmacy_bill_basic_id = pharmacy_bill_basic.id
                          AND transactions.type = 'payment') AS paid_amount
                FROM pharmacy_bill_basic
                INNER JOIN patients ON patients.id = pharmacy_bill_basic.patient_id
                WHERE patients.id = ?

                UNION ALL

                SELECT 'ambulance_call' AS type,
                       patients.id AS patient_id,
                       NULL AS date_,
                       IFNULL(ambulance_call.net_amount, 0) AS net_amount,
                       (SELECT IFNULL(SUM(transactions.amount),0)
                        FROM transactions
                        WHERE transactions.ambulance_call_id = ambulance_call.id) AS paid_amount
                FROM ambulance_call
                INNER JOIN patients ON patients.id = ambulance_call.patient_id
                WHERE patients.id = ?

                UNION ALL

                SELECT 'opd_patient' AS type,
                       patients.id AS patient_id,
                       NULL AS date_,
                       IFNULL((SELECT SUM(amount) FROM patient_charges
                        WHERE patient_charges.opd_id = opd_details.id), 0) AS net_amount,
                       (SELECT IFNULL(SUM(amount),0) FROM transactions
                        WHERE transactions.opd_id = opd_details.id) AS paid_amount
                FROM opd_details
                INNER JOIN patients ON patients.id = opd_details.patient_id
                WHERE patients.id = ?

                UNION ALL

                SELECT 'ipd_patient' AS type,
                       patients.id AS patient_id,
                       NULL AS date_,
                       IFNULL((SELECT SUM(amount) FROM patient_charges
                        WHERE patient_charges.ipd_id = ipd_details.id), 0) AS net_amount,
                       (SELECT IFNULL(SUM(amount),0) FROM transactions
                        WHERE transactions.ipd_id = ipd_details.id) AS paid_amount
                FROM ipd_details
                INNER JOIN patients ON patients.id = ipd_details.patient_id
                WHERE patients.id = ?

            ) AS module_bills
            GROUP BY type
            HAVING balance_amount > 0
            ORDER BY balance_amount DESC
        ";

        $bindings = array_fill(0, 7, $patient_id);
        return $this->db->query($sql, $bindings)->result_array();
    }

    /**
     * Dashboard widget: hospital-wide outstanding balance.
     * Sums balance (net_amount - paid_amount) across all 7 module-billing
     * sources where balance > 0. Counts bills + overdue (>30 days old).
     *
     * Raw SQL required because the formula UNIONs 7 disparate billing tables
     * with the same balance formula, then filters/aggregates the derived rows.
     * CI Query Builder cannot express this cleanly.
     * Used by admin/admin/dashboard Variant B (2026-05-15).
     */
    public function getOutstandingTotal()
    {
        $sql = "
            SELECT
                IFNULL(SUM(balance_amount), 0) AS total_balance,
                COUNT(*)                       AS bill_count,
                SUM(CASE WHEN latest_date IS NOT NULL
                           AND latest_date < DATE_SUB(CURDATE(), INTERVAL 30 DAY)
                         THEN 1 ELSE 0 END)    AS overdue_count
            FROM (
                SELECT IFNULL(radiology_billing.net_amount, 0)
                       - IFNULL((SELECT SUM(amount) FROM transactions WHERE radiology_billing_id = radiology_billing.id), 0) AS balance_amount,
                       radiology_billing.date AS latest_date
                FROM radiology_billing
                UNION ALL
                SELECT IFNULL(pathology_billing.net_amount, 0)
                       - IFNULL((SELECT SUM(amount) FROM transactions WHERE pathology_billing_id = pathology_billing.id), 0),
                       pathology_billing.date
                FROM pathology_billing
                UNION ALL
                SELECT IFNULL(blood_issue.net_amount, 0)
                       - IFNULL((SELECT SUM(amount) FROM transactions WHERE blood_issue_id = blood_issue.id), 0),
                       NULL
                FROM blood_issue
                UNION ALL
                SELECT IFNULL(pharmacy_bill_basic.net_amount, 0)
                       - IFNULL((SELECT SUM(amount) FROM transactions WHERE pharmacy_bill_basic_id = pharmacy_bill_basic.id AND type = 'payment'), 0),
                       pharmacy_bill_basic.date
                FROM pharmacy_bill_basic
                UNION ALL
                SELECT IFNULL(ambulance_call.net_amount, 0)
                       - IFNULL((SELECT SUM(amount) FROM transactions WHERE ambulance_call_id = ambulance_call.id), 0),
                       NULL
                FROM ambulance_call
                UNION ALL
                SELECT IFNULL((SELECT SUM(amount) FROM patient_charges WHERE opd_id = opd_details.id), 0)
                       - IFNULL((SELECT SUM(amount) FROM transactions WHERE opd_id = opd_details.id), 0),
                       NULL
                FROM opd_details
                UNION ALL
                SELECT IFNULL((SELECT SUM(amount) FROM patient_charges WHERE ipd_id = ipd_details.id), 0)
                       - IFNULL((SELECT SUM(amount) FROM transactions WHERE ipd_id = ipd_details.id), 0),
                       NULL
                FROM ipd_details
            ) AS all_bills
            WHERE balance_amount > 0
        ";
        $row = $this->db->query($sql)->row_array();
        return array(
            'total_balance' => (float)($row['total_balance'] ?? 0),
            'bill_count'    => (int)($row['bill_count'] ?? 0),
            'overdue_count' => (int)($row['overdue_count'] ?? 0),
        );
    }

}
