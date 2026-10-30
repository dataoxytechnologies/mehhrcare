<?php

if (!defined("BASEPATH")) {
    exit("No direct script access allowed");
}

class Pharmacy_model extends MY_Model
{
    public function add($pharmacy)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $this->db->insert("pharmacy", $pharmacy);
        $insert_id = $this->db->insert_id();
        $message = INSERT_RECORD_CONSTANT . " On Pharmacy id " . $insert_id;
        $action = "Insert";
        $record_id = $insert_id;
        $this->log($message, $record_id, $action);
        //======================Code End==============================

        $this->db->trans_complete(); # Completing transaction
        /* Optional */

        if ($this->db->trans_status() === false) {
            # Something went wrong.
            $this->db->trans_rollback();
            return false;
        } else {
            return $record_id;
        }
    }

    public function addImport($medicine_data)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $this->db->insert("pharmacy", $medicine_data);
        $insert_id = $this->db->insert_id();
        $message = INSERT_RECORD_CONSTANT . " On Pharmacy id " . $insert_id;
        $action = "Insert";
        $record_id = $insert_id;
        $this->log($message, $record_id, $action);
        //======================Code End==============================

        $this->db->trans_complete(); # Completing transaction
        /* Optional */

        if ($this->db->trans_status() === false) {
            # Something went wrong.
            $this->db->trans_rollback();
            return false;
        } else {
            return $record_id;
        }
    }

    public function get_medicine_stockinfo($pharmacy_id)
    {
        return $this->db
            ->select(
                "medicine_batch_details.available_quantity,`pharmacy`.`min_level`, (SELECT sum(available_quantity) FROM `medicine_batch_details` WHERE pharmacy_id=pharmacy.id) as `total_qty`,IFNULL((SELECT SUM(quantity) FROM `pharmacy_bill_detail` WHERE medicine_batch_detail_id=medicine_batch_details.id),0) as used_quantity",
            )
            ->from("medicine_batch_details")
            ->join(
                "pharmacy",
                "pharmacy.id=medicine_batch_details.pharmacy_id",
                "inner",
            )
            ->where("pharmacy.id", $pharmacy_id)
            ->get()
            ->row_array();
    }

    public function getAllpharmacyRecord($medicine_name = "")
    {
        if (!empty($medicine_name)) {
            $this->datatables->where(
                'pharmacy.medicine_name LIKE \'%' .
                    $this->db->escape_like_str($medicine_name) .
                    '%\'',
            );
        }
        $this->datatables
            ->select(
                "pharmacy.*,medicine_category.id as medicine_categoryid,medicine_category.medicine_category,(SELECT sum(available_quantity) FROM `medicine_batch_details` WHERE pharmacy_id=pharmacy.id) as `total_qty`,unit.unit_name,pharmacy_company.company_name,medicine_group.group_name",
            )
            ->join(
                "medicine_category",
                "pharmacy.medicine_category_id = medicine_category.id",
                "left",
            )
            ->join(
                "medicine_batch_details",
                "pharmacy.id = medicine_batch_details.pharmacy_id",
                "left",
            )
            ->join("unit", "pharmacy.unit = unit.id", "left")
            ->join(
                "pharmacy_company",
                "pharmacy_company.id = pharmacy.medicine_company",
                "left",
            )
            ->join(
                "medicine_group",
                "medicine_group.id=pharmacy.medicine_group",
                "left",
            )
            ->join(
                "pharmacy_bill_detail",
                "pharmacy_bill_detail.medicine_batch_detail_id = medicine_batch_details.id",
                "left",
            )
            ->searchable(
                "pharmacy.medicine_name,pharmacy.medicine_company,pharmacy.medicine_composition,pharmacy.medicine_category_id,pharmacy.medicine_group",
            )
            ->orderable(
                "null,
                pharmacy.id,pharmacy.medicine_name,
                pharmacy.medicine_company,
                pharmacy.medicine_composition,
                pharmacy.medicine_category_id,
                pharmacy.medicine_group,
                pharmacy.unit,
                null",
            )
            ->group_by("pharmacy.id")
            ->sort("pharmacy.id", "desc")
            ->where(
                "`pharmacy`.`medicine_category_id`=`medicine_category`.`id`",
            )
            ->from("pharmacy");
        return $this->datatables->generate("json");
    }

    public function getAllstockpharmacyRecord($condition)
    {
        if ($condition["medicine_category"] != "") {
            $this->datatables->where(
                "`pharmacy`.`medicine_category_id`=" .
                    $condition["medicine_category"],
            );
        }
        $this->datatables
            ->select(
                "pharmacy.*,medicine_category.id as medicine_categoryid,medicine_category.medicine_category,(SELECT sum(available_quantity) FROM `medicine_batch_details` WHERE pharmacy_id=pharmacy.id) as `total_qty`,unit.unit_name,pharmacy_company.company_name,medicine_group.group_name",
            )
            ->join(
                "medicine_category",
                "pharmacy.medicine_category_id = medicine_category.id",
                "left",
            )
            ->join(
                "medicine_batch_details",
                "pharmacy.id = medicine_batch_details.pharmacy_id",
                "left",
            )
            ->join("unit", "pharmacy.unit = unit.id", "left")
            ->join(
                "pharmacy_company",
                "pharmacy_company.id = pharmacy.medicine_company",
                "left",
            )
            ->join(
                "medicine_group",
                "medicine_group.id=pharmacy.medicine_group",
                "left",
            )
            ->join(
                "pharmacy_bill_detail",
                "pharmacy_bill_detail.medicine_batch_detail_id = medicine_batch_details.id",
                "left",
            )
            ->searchable(
                "pharmacy.medicine_name,pharmacy.medicine_company,pharmacy.medicine_composition,pharmacy.medicine_category_id,pharmacy.medicine_group",
            )
            ->orderable(
                "pharmacy.id,pharmacy.medicine_name,pharmacy.medicine_company,pharmacy.medicine_composition,pharmacy.medicine_category_id,pharmacy.medicine_group,pharmacy.unit",
            )
            ->group_by("pharmacy.id");
        $this->datatables->sort("pharmacy.id", "desc");
        $this->datatables->where(
            "`pharmacy`.`medicine_category_id`=`medicine_category`.`id`",
        );
        $this->datatables->from("pharmacy");
        return $this->datatables->generate("json");
    }

    public function getAvailableQuantity($pharmacy_id)
    {
        $this->db->select(
            "sum(pharmacy_bill_detail.quantity) as used_quantity",
        );
        $this->db->join(
            "pharmacy_bill_detail",
            "pharmacy_bill_detail.medicine_batch_detail_id = medicine_batch_details.id",
        );
        $this->db->where(
            "`medicine_batch_details`.`pharmacy_id`",
            $pharmacy_id,
        );
        $query = $this->db->get("medicine_batch_details");
        if ($query->num_rows() > 0) {
            return $query->row_array();
        } else {
            return false;
        }
    }

    public function searchFullText()
    {
        $this->db->select(
            "pharmacy.*,medicine_category.id as medicine_category_id,medicine_category.medicine_category",
        );
        $this->db->join(
            "medicine_category",
            "pharmacy.medicine_category_id = medicine_category.id",
            "left",
        );
        $this->db->where(
            "`pharmacy`.`medicine_category_id`=`medicine_category`.`id`",
        );
        $this->db->order_by("pharmacy.medicine_name");
        $query = $this->db->get("pharmacy");
        return $query->result_array();
    }

    public function searchtestdata()
    {
        $this->db->select("pharmacy.*");
        $this->db->order_by("pharmacy.medicine_name");
        $query = $this->db->get("pharmacy");
        return $query->result_array();
    }

    public function getpatientPharmacyYearCounter($patient_id, $year)
    {
        $sql =
            "SELECT count(*) as `total_visits`,Year(date) as `year` FROM `pharmacy_bill_basic` WHERE YEAR(date) >= " .
            $this->db->escape($year) .
            " AND patient_id=" .
            $this->db->escape($patient_id) .
            " GROUP BY  YEAR(date)";
        $query = $this->db->query($sql);
        return $query->result_array();
    }

    public function getPharmacyBillAmountByCaseId($case_id)
    {
        $sql =
            "SELECT sum(net_amount) as `net_amount`,IFNULL((SELECT sum(amount) FROM `transactions` WHERE pharmacy_bill_basic_id in (SELECT pharmacy_bill_basic.id FROM `pharmacy_bill_basic` WHERE case_reference_id=197) and section = 'Pharmacy'),0) as `paid_amount` FROM `pharmacy_bill_basic` WHERE case_reference_id=" .
            $this->db->escape($case_id);
        $query = $this->db->query($sql);
        return $query->row();
    }

    public function check_medicine_exists($medicine_name, $medicine_category_id)
    {
        $this->db->where([
            "medicine_category_id" => $medicine_category_id,
            "medicine_name" => $medicine_name,
        ]);
        $query = $this->db
            ->join(
                "medicine_category",
                "medicine_category.id = pharmacy.medicine_category_id",
            )
            ->get("pharmacy");
        if ($query->num_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function bulkdelete($id)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        if (!empty($id)) {
            $this->db->where_in("id", $id);
            $this->db->delete("pharmacy");
            $message = DELETE_RECORD_CONSTANT . " On Pharmacy id " . $id;
            $action = "Delete";
            $record_id = $id;
            $this->log($message, $record_id, $action);
        }
        //======================Code End==============================
        $this->db->trans_complete(); # Completing transaction
        /* Optional */

        if ($this->db->trans_status() === false) {
            # Something went wrong.
            $this->db->trans_rollback();
            return false;
        } else {
            return $record_id;
        }
    }

    public function searchFullTextPurchase()
    {
        $this->db->select(
            "supplier_bill_detail.*,supplier_bill_basic.supplier_id,supplier_bill_basic.supplier_name,supplier_bill_basic.total,supplier_bill_basic.net_amount,medicine_supplier.medicine_supplier,medicine_supplier.supplier_person,medicine_supplier.supplier_person,medicine_supplier.contact,medicine_supplier.supplier_person_contact,medicine_supplier.address,medicine_category,pharmacy.medicine_name",
        );
        $this->db->join(
            "supplier_bill_basic",
            "supplier_bill_detail.supplier_bill_basic_id=supplier_bill_basic.id",
        );
        $this->db->join(
            "medicine_supplier",
            "supplier_bill_basic.supplier_id=medicine_supplier.id",
        );
        $this->db->join(
            "medicine_category",
            "supplier_bill_detail.medicine_category_id = medicine_category.id",
            "left",
        );
        $this->db->join(
            "pharmacy",
            "supplier_bill_detail.medicine_name = pharmacy.id",
            "left",
        );
        $query = $this->db->get("supplier_bill_detail");
        return $query->result_array();
    }

    public function getDetails($id)
    {
        $this->db->select(
            "pharmacy.*,medicine_category.id as medicine_category_id,medicine_category.medicine_category,unit.unit_name,pharmacy_company.company_name,medicine_group.group_name",
        );
        $this->db->join(
            "medicine_category",
            "pharmacy.medicine_category_id = medicine_category.id",
            "inner",
        );
        $this->db->join("unit", "pharmacy.unit = unit.id", "left");
        $this->db->join(
            "pharmacy_company",
            "pharmacy.medicine_company = pharmacy_company.id",
            "left",
        );
        $this->db->join(
            "medicine_group",
            "pharmacy.medicine_group = medicine_group.id",
            "left",
        );
        $this->db->where("pharmacy.id", $id);
        $this->db->order_by("pharmacy.id", "desc");
        $query = $this->db->get("pharmacy");
        return $query->row_array();
    }

    public function update($pharmacy)
    {
        $query = $this->db
            ->where("id", $pharmacy["id"])
            ->update("pharmacy", $pharmacy);
    }

    public function delete($id)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $this->db->where("id", $id)->delete("pharmacy");
        $message = DELETE_RECORD_CONSTANT . " On Pharmacy id " . $id;
        $action = "Delete";
        $record_id = $id;
        $this->log($message, $record_id, $action);
        //======================Code End==============================

        $this->db->trans_complete(); # Completing transaction
        /* Optional */

        if ($this->db->trans_status() === false) {
            # Something went wrong.
            $this->db->trans_rollback();
            return false;
        } else {
            return $record_id;
        }
    }

    public function getPharmacy($id = null)
    {
        $query = $this->db->get("pharmacy");
        return $query->result_array();
    }

    // SaaS: single medicine row by id (getPharmacy() above ignores $id and returns ALL rows,
    // so it cannot be used for the storage-quota diff on medicine-image replace).
    public function getMedicineById($id)
    {
        return $this->db->select('id, medicine_image')
            ->where('id', $id)
            ->get('pharmacy')
            ->row_array();
    }

    public function medicineDetail($medicine_batch)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $this->db->insert("medicine_batch_details", $medicine_batch);
        $insert_id = $this->db->insert_id();
        $message =
            INSERT_RECORD_CONSTANT .
            " On Medicine Batch Details id " .
            $insert_id;
        $action = "Insert";
        $record_id = $insert_id;
        $this->log($message, $record_id, $action);
        //======================Code End==============================

        $this->db->trans_complete(); # Completing transaction
        /* Optional */

        if ($this->db->trans_status() === false) {
            # Something went wrong.
            $this->db->trans_rollback();
            return false;
        } else {
            return $record_id;
        }
    }

    public function getMedicineBatch($pharm_id)
    {
        $this->db->select(
            "medicine_batch_details.*, pharmacy.id as pharmacy_id, pharmacy.medicine_name, supplier_bill_basic.id as purchase_no",
        );
        $this->db->join(
            "pharmacy",
            "medicine_batch_details.pharmacy_id = pharmacy.id",
            "inner",
        );
        $this->db->join(
            "supplier_bill_basic",
            "supplier_bill_basic.id = medicine_batch_details.supplier_bill_basic_id",
            "inner",
        );
        $this->db->where("pharmacy.id", $pharm_id);
        $query = $this->db->get("medicine_batch_details");
        return $query->result();
    }

    public function getMedicineSales($pharmacy_id)
    {
        $this->db->select(
            "pharmacy_bill_basic.id, pharmacy_bill_basic.date, medicine_batch_details.batch_no, medicine_batch_details.expiry, medicine_batch_details.mrp, pharmacy_bill_detail.quantity, pharmacy_bill_detail.sale_rate, (pharmacy_bill_detail.quantity * pharmacy_bill_detail.sale_rate) AS total, patients.patient_name",
        );
        $this->db->join(
            "medicine_batch_details",
            "medicine_batch_details.id = pharmacy_bill_detail.medicine_batch_detail_id",
            "inner",
        );
        $this->db->join(
            "pharmacy_bill_basic",
            "pharmacy_bill_basic.id = pharmacy_bill_detail.pharmacy_bill_basic_id",
            "inner",
        );
        $this->db->join(
            "patients",
            "patients.id = pharmacy_bill_basic.patient_id",
            "left",
        );
        $this->db->where("medicine_batch_details.pharmacy_id", $pharmacy_id);
        $this->db->order_by("pharmacy_bill_basic.date", "desc");
        $query = $this->db->get("pharmacy_bill_detail");
        return $query->result_array();
    }

    public function getMedicineProfitLoss($pharmacy_id)
    {
        $this->db->select(
            "mbd.batch_no, mbd.expiry, mbd.packing_qty, mbd.purchase_price, mbd.sale_rate, IFNULL(SUM(pbd.quantity), 0) AS qty_sold, IFNULL(SUM(pbd.quantity * pbd.sale_rate), 0) AS revenue, IFNULL(SUM(pbd.quantity * mbd.purchase_price), 0) AS cost, (IFNULL(SUM(pbd.quantity * pbd.sale_rate), 0) - IFNULL(SUM(pbd.quantity * mbd.purchase_price), 0)) AS profit_loss",
            false,
        );
        $this->db->from("medicine_batch_details mbd");
        $this->db->join(
            "pharmacy_bill_detail pbd",
            "pbd.medicine_batch_detail_id = mbd.id",
            "left",
        );
        $this->db->where("mbd.pharmacy_id", $pharmacy_id);
        $this->db->group_by("mbd.id");
        $this->db->order_by("mbd.inward_date", "desc");
        $query = $this->db->get();
        return $query->result_array();
    }

    public function getMedicineName()
    {
        $query = $this->db
            ->select("pharmacy.id, pharmacy.medicine_name, medicine_category.medicine_category as category_name")
            ->join("medicine_category", "medicine_category.id = pharmacy.medicine_category_id", "left")
            ->order_by("pharmacy.medicine_name", "asc")
            ->get("pharmacy");
        return $query->result_array();
    }

    public function getMedicineNamePat()
    {
        $query = $this->db
            ->select("pharmacy.id,pharmacy.medicine_name")
            ->get("pharmacy");
        return $query->result_array();
    }

    public function addBill(
        $data,
        $insert_array,
        $update_array,
        $delete_array,
        $payment_array,
    ) {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        if (isset($data["id"]) && $data["id"] != 0) {
            $insert_id = $data["id"];
            $this->db
                ->where("id", $data["id"])
                ->update("pharmacy_bill_basic", $data);
            $message =
                UPDATE_RECORD_CONSTANT .
                " On Pharmacy Bill Basic id " .
                $data["id"];
            $action = "Update";
            $record_id = $data["id"];
            $this->log($message, $record_id, $action);
        } else {
            $this->db->insert("pharmacy_bill_basic", $data);
            $insert_id = $this->db->insert_id();
            $message =
                INSERT_RECORD_CONSTANT .
                " On Pharmacy Bill Basic id " .
                $insert_id;
            $action = "Insert";
            $record_id = $insert_id;
            $this->log($message, $record_id, $action);
        }

        if (!empty($delete_array)) {
            $this->db->where_in("id", $delete_array);
            $this->db->delete("pharmacy_bill_detail");
        }

        if (isset($update_array) && !empty($update_array)) {
            $this->db->update_batch(
                "pharmacy_bill_detail",
                $update_array,
                "id",
            );
        }

        if (isset($insert_array) && !empty($insert_array)) {
            $total_rec = count($insert_array);
            for ($i = 0; $i < $total_rec; $i++) {
                $insert_array[$i]["pharmacy_bill_basic_id"] = $insert_id;
            }
            $this->db->insert_batch("pharmacy_bill_detail", $insert_array);
        }

        if (isset($payment_array) && !empty($payment_array)) {
            $payment_array["pharmacy_bill_basic_id"] = $insert_id;
            $payment_array["case_reference_id"] = $data["case_reference_id"];
            $this->db->insert("transactions", $payment_array);
        }

        $this->db->trans_complete(); # Completing transaction
        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            return false;
        } else {
            $this->db->trans_commit();
            return $insert_id;
        }
    }

    public function addBillSupplier($data)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        if (isset($data["id"])) {
            $this->db
                ->where("id", $data["id"])
                ->update("supplier_bill_basic", $data);
            $message =
                UPDATE_RECORD_CONSTANT .
                " On Supplier Bill Basic id " .
                $data["id"];
            $action = "Update";
            $record_id = $data["id"];
            $this->log($message, $record_id, $action);
        } else {
            $this->db->insert("supplier_bill_basic", $data);
            $insert_id = $this->db->insert_id();
            $message =
                INSERT_RECORD_CONSTANT .
                " On Supplier Bill Basic id " .
                $insert_id;
            $action = "Insert";
            $record_id = $insert_id;
            $this->log($message, $record_id, $action);
        }
        //======================Code End==============================

        $this->db->trans_complete(); # Completing transaction
        /* Optional */

        if ($this->db->trans_status() === false) {
            # Something went wrong.
            $this->db->trans_rollback();
            return false;
        } else {
            return $record_id;
        }
    }

    public function addBillBatch($data)
    {
        $query = $this->db->insert_batch("pharmacy_bill_detail", $data);
    }

    public function addBillBatchSupplier($data)
    {
        $query = $this->db->insert_batch("supplier_bill_detail", $data);
    }

    public function addBillMedicineBatchSupplier($data1)
    {
        $query = $this->db->insert_batch("medicine_batch_details", $data1);
    }

    public function updateBillBatch($data)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $this->db
            ->where("pharmacy_bill_basic_id", $data["id"])
            ->update("pharmacy_bill_detail");
        $message =
            UPDATE_RECORD_CONSTANT .
            " On Pharmacy Bill Basic_id id " .
            $data["id"];
        $action = "Update";
        $record_id = $data["id"];
        $this->log($message, $record_id, $action);
        //======================Code End==============================

        $this->db->trans_complete(); # Completing transaction
        /* Optional */

        if ($this->db->trans_status() === false) {
            # Something went wrong.
            $this->db->trans_rollback();
            return false;
        } else {
            return $record_id;
        }
    }

    public function updateBillBatchSupplier($data)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $this->db
            ->where("supplier_bill_basic_id", $data["id"])
            ->update("supplier_bill_basic_id");
        $message =
            UPDATE_RECORD_CONSTANT .
            " On Pharmacy Bill Basic_id id " .
            $data["id"];
        $action = "Update";
        $record_id = $data["id"];
        $this->log($message, $record_id, $action);
        //======================Code End==============================

        $this->db->trans_complete(); # Completing transaction
        /* Optional */

        if ($this->db->trans_status() === false) {
            # Something went wrong.
            $this->db->trans_rollback();
            return false;
        } else {
            return $record_id;
        }
    }

    public function updateBillDetail($data)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $this->db
            ->where("id", $data["id"])
            ->update("pharmacy_bill_detail", $data);
        $message =
            UPDATE_RECORD_CONSTANT .
            " On Pharmacy Bill Detail id " .
            $data["id"];
        $action = "Update";
        $record_id = $data["id"];
        $this->log($message, $record_id, $action);
        //======================Code End==============================

        $this->db->trans_complete(); # Completing transaction
        /* Optional */

        if ($this->db->trans_status() === false) {
            # Something went wrong.
            $this->db->trans_rollback();
            return false;
        } else {
            return $record_id;
        }
    }

    public function updateBillSupplierDetail($data)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $this->db
            ->where("id", $data["id"])
            ->update("supplier_bill_detail", $data);
        $message =
            UPDATE_RECORD_CONSTANT .
            " On Supplier Bill Detail id " .
            $data["id"];
        $action = "Update";
        $record_id = $data["id"];
        $this->log($message, $record_id, $action);
        //======================Code End==============================

        $this->db->trans_complete(); # Completing transaction
        /* Optional */

        if ($this->db->trans_status() === false) {
            # Something went wrong.
            $this->db->trans_rollback();
            return false;
        } else {
            return $record_id;
        }
    }

    public function updateMedicineBatchDetail($data1)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $query = $this->db
            ->where("id", $data1["id"])
            ->update("medicine_batch_details", $data1);
        $message =
            UPDATE_RECORD_CONSTANT .
            " On Medicine Batch Details id " .
            $data1["id"];
        $action = "Update";
        $record_id = $data1["id"];
        $this->log($message, $record_id, $action);
        //======================Code End==============================

        $this->db->trans_complete(); # Completing transaction
        /* Optional */

        if ($this->db->trans_status() === false) {
            # Something went wrong.
            $this->db->trans_rollback();
            return false;
        } else {
            return $record_id;
        }
    }

    public function deletePharmacyBill($id)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================

        // Delete connected sale-return records BEFORE deleting the bill. The FK
        // (prb_bill_sale_fk) is ON DELETE SET NULL, so once the bill row is gone the return
        // rows' pharmacy_bill_basic_id becomes NULL and can no longer be matched — leaving
        // orphaned sale-return records. Detail rows are removed via FK cascade, but delete
        // them explicitly too in case FK enforcement is off.
        $return_rows = $this->db
            ->select("id")
            ->where("pharmacy_bill_basic_id", $id)
            ->get("pharmacy_return_basic")
            ->result_array();
        if (!empty($return_rows)) {
            $return_ids = array_column($return_rows, "id");
            $this->db->where_in("pharmacy_return_basic_id", $return_ids)->delete("pharmacy_return_detail");
            $this->db->where_in("id", $return_ids)->delete("pharmacy_return_basic");
        }

        $query = $this->db
            ->where("pharmacy_bill_basic_id", $id)
            ->delete("pharmacy_bill_detail");
        if ($query) {
            $this->db->where("id", $id)->delete("pharmacy_bill_basic");
            $this->customfield_model->delete_custom_fieldRecord(
                $id,
                "pharmacy",
            );
        }

        if (!empty($id)) {
            $this->db->delete("transactions", ["pharmacy_bill_basic_id" => $id]);
        }

        $message =
            DELETE_RECORD_CONSTANT . " On Pharmacy Bill Detail id " . $id;
        $action = "Delete";
        $record_id = $id;
        $this->log($message, $record_id, $action);
        //======================Code End==============================

        $this->db->trans_complete(); # Completing transaction
        /* Optional */

        if ($this->db->trans_status() === false) {
            # Something went wrong.
            $this->db->trans_rollback();
            return false;
        } else {
            return $record_id;
        }
    }

    public function deleteSupplierBill($id)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================

        // delete organization charges
        $id = (int) $id;
        $sql = "DELETE organisations_medicine_charges
                FROM organisations_medicine_charges
                LEFT JOIN medicine_batch_details
                ON medicine_batch_details.id = organisations_medicine_charges.medicine_batch_details_id
                WHERE medicine_batch_details.supplier_bill_basic_id = $id";
        $this->db->query($sql);
        // delete organization charges

        $query = $this->db
            ->where("supplier_bill_basic_id", $id)
            ->delete("medicine_batch_details");
        if ($query) {
            $this->db->where("id", $id)->delete("supplier_bill_basic");
        }

        $message =
            DELETE_RECORD_CONSTANT . " On Medicine Batch Details id " . $id;
        $action = "Delete";
        $record_id = $id;
        $this->log($message, $record_id, $action);
        //======================Code End==============================

        $this->db->trans_complete(); # Completing transaction
        /* Optional */

        if ($this->db->trans_status() === false) {
            # Something went wrong.
            $this->db->trans_rollback();
            return false;
        } else {
            return $record_id;
        }
    }

    public function getMaxId()
    {
        $query = $this->db
            ->select("max(id) as purchase_no")
            ->get("supplier_bill_basic");
        $result = $query->row_array();
        return $result["purchase_no"];
    }

    public function getindate($purchase_id)
    {
        $query = $this->db
            ->select("supplier_bill_basic.*,")
            ->where("supplier_bill_basic.id", $purchase_id)
            ->get("supplier_bill_basic");
        return $query->row_array();
    }

    public function getdate($id)
    {
        $query = $this->db
            ->select("pharmacy_bill_basic.*,")
            ->where("pharmacy_bill_basic.id", $id)
            ->get("pharmacy_bill_basic");
        return $query->row_array();
    }

    public function getSupplier()
    {
        $query = $this->db
            ->select("supplier_bill_basic.*,medicine_supplier.supplier")
            ->join(
                "medicine_supplier",
                "medicine_supplier.id = supplier_bill_basic.supplier_id",
            )
            ->order_by("id", "desc")
            ->get("supplier_bill_basic");
        return $query->result_array();
    }

    public function getAllpharmacypurchaseRecord()
    {
        $this->datatables
            ->select("supplier_bill_basic.*,medicine_supplier.supplier")
            ->join(
                "medicine_supplier",
                "medicine_supplier.id = supplier_bill_basic.supplier_id",
            )
            ->searchable(
                "supplier_bill_basic.id,supplier_bill_basic.invoice_no,supplier",
            )
            ->orderable(
                "supplier_bill_basic.id,supplier_bill_basic.date,supplier_bill_basic.invoice_no,supplier,supplier_bill_basic.total,supplier_bill_basic.tax,supplier_bill_basic.discount,supplier_bill_basic.net_amount",
            )
            ->sort("supplier_bill_basic.id", "desc")
            ->from("supplier_bill_basic");
        return $this->datatables->generate("json");
    }

    public function getBillBasic($limit = "", $start = "")
    {
        $query = $this->db
            ->select("pharmacy_bill_basic.*,patients.patient_name")
            ->order_by("pharmacy_bill_basic.id", "desc")
            ->join("patients", "patients.id = pharmacy_bill_basic.patient_id")
            ->where("patients.is_active", "yes")
            ->limit($limit, $start)
            ->get("pharmacy_bill_basic");
        return $query->result_array();
    }

    public function getAllpharmacybillRecord()
    {
        $custom_fields = $this->customfield_model->get_custom_fields(
            "pharmacy",
            1,
        );
        $custom_field_column_array = [];

        $field_var_array = [];
        $i = 1;
        if (!empty($custom_fields)) {
            foreach (
                $custom_fields
                as $custom_fields_key => $custom_fields_value
            ) {
                $tb_counter = "table_custom_" . $i;
                array_push(
                    $custom_field_column_array,
                    "table_custom_" . $i . ".field_value",
                );
                array_push(
                    $field_var_array,
                    "`table_custom_" .
                        $i .
                        "`.`field_value` as `" .
                        $custom_fields_value->name .
                        "`",
                );
                $this->datatables->join(
                    "custom_field_values as " . $tb_counter,
                    "pharmacy_bill_basic.id = " .
                        $tb_counter .
                        ".belong_table_id AND " .
                        $tb_counter .
                        ".custom_field_id = " .
                        $custom_fields_value->id,
                    "left",
                );
                $i++;
            }
        }

        $field_variable = empty($field_var_array)
            ? ""
            : "," . implode(",", $field_var_array);
        $custom_field_column = empty($custom_field_column_array)
            ? ""
            : "," . implode(",", $custom_field_column_array);
        $this->datatables
            ->select(
                'pharmacy_bill_basic.*,IFNULL((select sum(amount) as amount_paid from transactions WHERE transactions.pharmacy_bill_basic_id =pharmacy_bill_basic.id and transactions.type="payment" ),0) as paid_amount, IFNULL((select sum(amount) as refund from transactions WHERE transactions.pharmacy_bill_basic_id =pharmacy_bill_basic.id and transactions.type="refund" ),0) as refund_amount, patients.patient_name,patients.id as pid, generated_by_staff.name as generated_byname,generated_by_staff.surname as generated_bysurname,generated_by_staff.employee_id as generated_byemployee_id' .
                    $field_variable,
            )
            ->join(
                "patients",
                "patients.id = pharmacy_bill_basic.patient_id",
                "left",
            )
            ->join(
                "staff as generated_by_staff",
                "generated_by_staff.id = pharmacy_bill_basic.generated_by",
                "left",
            )

            ->searchable(
                "pharmacy_bill_basic.id,pharmacy_bill_basic.discount,pharmacy_bill_basic.case_reference_id,pharmacy_bill_basic.date,patients.patient_name" .
                    $custom_field_column .
                    ",pharmacy_bill_basic.doctor_name",
            )

            ->orderable(
                "pharmacy_bill_basic.id, pharmacy_bill_basic.case_reference_id,pharmacy_bill_basic.date, patients.patient_name, generated_by_staff.name as generated_byname, pharmacy_bill_basic.doctor_name" .
                    $custom_field_column .
                    ',pharmacy_bill_basic.discount,pharmacy_bill_basic.tax,
                pharmacy_bill_basic.net_amount,paid_amount, paid_amount,refund_amount ,""',
            )

            ->sort("pharmacy_bill_basic.id", "desc")
            ->from("pharmacy_bill_basic");

        return $this->datatables->generate("json");
    }

    public function getpharmacybillByCaseId($case_id)
    {
        $query = $this->db
            ->select(
                'pharmacy_bill_basic.*,
            IFNULL((SELECT sum(transactions.amount) from transactions WHERE transactions.pharmacy_bill_basic_id=pharmacy_bill_basic.id),0) as `amount_paid`,
            IFNULL((select sum(transactions.amount) as refund_amount from transactions WHERE transactions.pharmacy_bill_basic_id =pharmacy_bill_basic.id and transactions.type="refund" ),0) as refund_amount,
            patients.patient_name,patients.id as patient_id',
            )
            ->join(
                "patients",
                "patients.id = pharmacy_bill_basic.patient_id",
                "left",
            )
            ->where("pharmacy_bill_basic.case_reference_id", $case_id)
            ->get("pharmacy_bill_basic");
        return $query->result();
    }

    public function getAllpharmacybillByCaseIdSql($case_id)
    {
        $this->datatables
            ->query(
                "SELECT `pharmacy_bill_basic`.*, `patients`.`patient_name`, `patients`.`id` as `patient_unique_id`,transaction_add.amt_payment,transaction_refund.amt_refund FROM `pharmacy_bill_basic` left join ( SELECT sum(amount) as amt_payment, pharmacy_bill_basic_id FROM `transactions` where transactions.type='payment'  GROUP BY `transactions`.`pharmacy_bill_basic_id`) as transaction_add on `pharmacy_bill_basic`.`id` = `transaction_add`.`pharmacy_bill_basic_id` left join (SELECT sum(amount) as amt_refund, pharmacy_bill_basic_id FROM `transactions` where transactions.type='refund'  GROUP BY `transactions`.`pharmacy_bill_basic_id`) as transaction_refund on `pharmacy_bill_basic`.`id` = `transaction_refund`.`pharmacy_bill_basic_id` LEFT JOIN `patients` ON `patients`.`id` = `pharmacy_bill_basic`.`patient_id` WHERE `pharmacy_bill_basic`.`case_reference_id` = " .
                    $case_id,
            )
            ->searchable(
                "pharmacy_bill_basic.id,pharmacy_bill_basic.date,pharmacy_bill_basic.doctor_name,pharmacy_bill_basic.discount_percentage,pharmacy_bill_basic.net_amount,transaction_add.amt_payment,transaction_refund.amt_refund,pharmacy_bill_basic.case_reference_id,patients.patient_name,pharmacy_bill_basic.total",
            )
            ->orderable(
                "pharmacy_bill_basic.id,pharmacy_bill_basic.date,pharmacy_bill_basic.doctor_name,pharmacy_bill_basic.discount_percentage,pharmacy_bill_basic.net_amount,transaction_add.amt_payment,transaction_refund.amt_refund,pharmacy_bill_basic.case_reference_id,patients.patient_name,pharmacy_bill_basic.total",
            )
            ->sort("pharmacy_bill_basic.id", "desc")
            ->query_where_enable(true);
        return $this->datatables->generate("json");
    }

    public function getAllpharmacybillByCaseId($case_id)
    {
        $this->datatables
            ->select(
                "pharmacy_bill_basic.*,sum(transactions.amount) as paid_amount,patients.patient_name,patients.id as patient_unique_id",
            )
            ->join(
                "patients",
                "patients.id = pharmacy_bill_basic.patient_id",
                "left",
            )
            ->join(
                "transactions",
                "transactions.pharmacy_bill_basic_id = pharmacy_bill_basic.id",
                "left",
            )
            ->searchable(
                "pharmacy_bill_basic.id,pharmacy_bill_basic.case_reference_id,pharmacy_bill_basic.date,patients.patient_name,pharmacy_bill_basic.doctor_name",
            )
            ->orderable(
                "pharmacy_bill_basic.id,pharmacy_bill_basic.case_reference_id,pharmacy_bill_basic.date,patients.patient_name,pharmacy_bill_basic.doctor_name,pharmacy_bill_basic.net_amount,paid_amount",
            )
            ->group_by("transactions.pharmacy_bill_basic_id")
            ->where("pharmacy_bill_basic.case_reference_id", $case_id)
            ->sort("pharmacy_bill_basic.id", "desc")
            ->from("pharmacy_bill_basic");
        return $this->datatables->generate("json");
    }

    public function totalPatientPharmacy($patient_id)
    {
        $query = $this->db
            ->select("count(pharmacy_bill_basic.patient_id) as total")
            ->where("patient_id", $patient_id)
            ->get("pharmacy_bill_basic");
        return $query->row_array();
    }

    public function getBillBasicPatient($patient_id)
    {
        $i = 1;
        $custom_fields = $this->customfield_model->get_custom_fields(
            "pharmacy",
            "",
            "",
            "",
            1,
        );
        $custom_field_column_array = [];

        $field_var_array = [];
        if (!empty($custom_fields)) {
            foreach (
                $custom_fields
                as $custom_fields_key => $custom_fields_value
            ) {
                $tb_counter = "table_custom_" . $i;
                array_push(
                    $custom_field_column_array,
                    "table_custom_" . $i . ".field_value",
                );
                array_push(
                    $field_var_array,
                    "`table_custom_" .
                        $i .
                        "`.`field_value` as `" .
                        $custom_fields_value->name .
                        "`",
                );
                $this->datatables->join(
                    "custom_field_values as " . $tb_counter,
                    "pharmacy_bill_basic.id = " .
                        $tb_counter .
                        ".belong_table_id AND " .
                        $tb_counter .
                        ".custom_field_id = " .
                        $custom_fields_value->id,
                    "left",
                );
                $i++;
            }
        }

        $field_variable = empty($field_var_array)
            ? ""
            : "," . implode(",", $field_var_array);
        $custom_field_column = empty($custom_field_column_array)
            ? ""
            : "," . implode(",", $custom_field_column_array);
        $this->db->select(
            'pharmacy_bill_basic.*,IFNULL((select sum(amount) as amount_paid from transactions WHERE transactions.pharmacy_bill_basic_id =pharmacy_bill_basic.id and transactions.type="payment" ),0) as paid_amount, IFNULL((select sum(amount) as refund from transactions WHERE transactions.pharmacy_bill_basic_id =pharmacy_bill_basic.id and transactions.type="refund" ),0) as refund_amount,patients.patient_name,patients.id as pid' .
                $field_variable,
        );
        $this->db->join(
            "patients",
            "patients.id = pharmacy_bill_basic.patient_id",
        );
        $this->db->where("pharmacy_bill_basic.patient_id", $patient_id);
        $this->db->order_by("pharmacy_bill_basic.id", "desc");
        $query = $this->db->get("pharmacy_bill_basic");
        return $query->result_array();
    }

    public function get_medicine_name($medicine_category_id)
    {
        $this->db->select("pharmacy.*, medicine_category.medicine_category as category_name");
        $this->db->join("medicine_category", "medicine_category.id = pharmacy.medicine_category_id", "left");
        if (!empty($medicine_category_id)) {
            $this->db->where(
                "pharmacy.medicine_category_id",
                $medicine_category_id,
            );
        }
        $this->db->order_by("pharmacy.medicine_name", "asc");
        $query = $this->db->get("pharmacy");
        return $query->result_array();
    }

    public function get_medicine_dosage($medicine_category_id)
    {
        $this->db
            ->select(
                "medicine_dosage.dosage,unit.unit_name as unit,medicine_dosage.id",
            )
            ->join("unit", "unit.id=medicine_dosage.units_id");
        $this->db->where(
            "medicine_dosage.medicine_category_id",
            $medicine_category_id,
        );
        $query = $this->db->get("medicine_dosage");
        return $query->result_array();
    }

    public function get_dosagename($id)
    {
        $this->db
            ->select(
                "medicine_dosage.dosage,charge_units.unit,medicine_dosage.id",
            )
            ->join(
                "charge_units",
                "charge_units.id=medicine_dosage.charge_units_id",
            );
        $this->db->where("medicine_dosage.id", $id);
        $query = $this->db->get("medicine_dosage");
        return $query->row_array();
    }

    public function get_supplier_name($supplier_category_id)
    {
        $query = $this->db
            ->where("id", $supplier_category_id)
            ->get("medicine_supplier");
        return $query->result_array();
    }

    public function getBillDetails($id, $check_print = null)
    {
        if ($check_print == "print") {
            $custom_fields = $this->customfield_model->get_custom_fields(
                "pharmacy",
                "",
                1,
            );
        } else {
            $custom_fields = $this->customfield_model->get_custom_fields(
                "pharmacy",
            );
        }

        $custom_field_column_array = [];
        $field_var_array = [];
        $i = 1;
        if (!empty($custom_fields)) {
            foreach (
                $custom_fields
                as $custom_fields_key => $custom_fields_value
            ) {
                $tb_counter = "table_custom_" . $i;
                array_push(
                    $custom_field_column_array,
                    "table_custom_" . $i . ".field_value",
                );
                array_push(
                    $field_var_array,
                    "`table_custom_" .
                        $i .
                        "`.`field_value` as `" .
                        $custom_fields_value->name .
                        "`",
                );
                $this->datatables->join(
                    "custom_field_values as " . $tb_counter,
                    "pharmacy_bill_basic.id = " .
                        $tb_counter .
                        ".belong_table_id AND " .
                        $tb_counter .
                        ".custom_field_id = " .
                        $custom_fields_value->id,
                    "left",
                );
                $i++;
            }
        }
        $field_variable = implode(",", $field_var_array);
        $this->db->select(
            'pharmacy_bill_basic.*,patients.insurance_id,patients.insurance_validity,organisation.organisation_name,organisation.code,IFNULL((select sum(amount) as amount_paid from transactions WHERE transactions.pharmacy_bill_basic_id =pharmacy_bill_basic.id and transactions.type="payment" ),0) as paid_amount, IFNULL((select sum(amount) as refund from transactions WHERE transactions.pharmacy_bill_basic_id =pharmacy_bill_basic.id and transactions.type="refund" ),0) as refund_amount,staff.name,staff.surname,staff.id as staff_id,staff.employee_id,staff_roles.role_id as staff_roles_id,patients.patient_name,patients.id as patientid,patients.id as patient_unique_id,patients.mobileno,patients.age,' .
                $field_variable,
        );
        $this->db->join(
            "patients",
            "pharmacy_bill_basic.patient_id = patients.id",
        );
        $this->db->join(
            "organisation",
            "organisation.id = patients.organisation_id",
            "left",
        );
        $this->db->join("staff", "pharmacy_bill_basic.generated_by = staff.id");
        $this->db->join("staff_roles", "staff_roles.staff_id = staff.id");
        $this->db->where("pharmacy_bill_basic.id", $id);
        $query = $this->db->get("pharmacy_bill_basic");
        return $query->row_array();
    }

    public function getAllBillDetails($id)
    {
        $sql =
            "SELECT pharmacy_bill_detail.*,medicine_batch_details.expiry,medicine_batch_details.pharmacy_id,medicine_batch_details.batch_no,medicine_batch_details.tax,pharmacy.medicine_name,pharmacy.unit,pharmacy.id as `medicine_id`,pharmacy.medicine_category_id,medicine_category.medicine_category,unit.unit_name as `unit_name` FROM `pharmacy_bill_detail` INNER JOIN medicine_batch_details on medicine_batch_details.id=pharmacy_bill_detail.medicine_batch_detail_id INNER JOIN pharmacy on pharmacy.id= medicine_batch_details.pharmacy_id
        LEFT JOIN unit on unit.id= pharmacy.unit  INNER JOIN medicine_category on medicine_category.id= pharmacy.medicine_category_id WHERE pharmacy_bill_basic_id =" .
            $this->db->escape($id);
        $query = $this->db->query($sql);
        return $query->result_array();
    }

    public function getSupplierDetails($id)
    {
        $this->db->select(
            "supplier_bill_basic.*,medicine_supplier.supplier,medicine_supplier.supplier_person,medicine_supplier.contact,medicine_supplier.address,medicine_supplier.supplier_drug_licence,medicine_supplier.supplier_person_contact",
        );
        $this->db->join(
            "medicine_supplier",
            "medicine_supplier.id=supplier_bill_basic.supplier_id",
        );
        $this->db->where("supplier_bill_basic.id", $id);
        $query = $this->db->get("supplier_bill_basic");
        return $query->row_array();
    }

    public function getAllSupplierDetails($id)
    {
        $query = $this->db
            ->select(
                "medicine_batch_details.*,pharmacy.medicine_name,pharmacy.unit,pharmacy.id as medicine_id,medicine_category.medicine_category,medicine_category.id as medicine_category_id",
            )
            ->join(
                "pharmacy",
                "medicine_batch_details.pharmacy_id = pharmacy.id",
            )
            ->join(
                "medicine_category",
                "pharmacy.medicine_category_id = medicine_category.id",
            )
            ->where("medicine_batch_details.supplier_bill_basic_id", $id)
            ->get("medicine_batch_details");
        return $query->result_array();
    }

    public function get_TPA_amount($batch_id, $org_id)
    {
        $batch_id = (int) $batch_id;
        $org_id = (int) $org_id;
        $sql = "SELECT organisations_medicine_charges.org_charge,organisations_medicine_charges.id as orgnization_medicine_charge_id FROM organisations_medicine_charges
         where organisations_medicine_charges.medicine_batch_details_id=$batch_id and  `organisations_medicine_charges`.`org_id`=$org_id";
        $query = $this->db->query($sql);
        return $query->row();
    }

    public function getBillDetailsPharma($id)
    {
        $this->db->select("pharmacy_bill_basic.*,patients.patient_name");
        $this->db->join(
            "patients",
            "patients.id = pharmacy_bill_basic.patient_id",
            "left",
        );
        $this->db->where("pharmacy_bill_basic.id", $id);
        $query = $this->db->get("pharmacy_bill_basic");
        return $query->row_array();
    }

    public function getAllBillDetailsPharma($id)
    {
        $query = $this->db
            ->select(
                "pharmacy_bill_detail.*,pharmacy.medicine_name,pharmacy.unit,pharmacy.id as medicine_id",
            )
            ->join(
                "pharmacy",
                "pharmacy_bill_detail.medicine_name = pharmacy.id",
            )
            ->where("pharmacy_bill_basic_id", $id)
            ->get("pharmacy_bill_detail");
        return $query->result_array();
    }

    public function getQuantity($batch_no, $med_id)
    {
        $query = $this->db
            ->select(
                "medicine_batch_details.id,medicine_batch_details.available_quantity,medicine_batch_details.quantity,medicine_batch_details.purchase_price,medicine_batch_details.sale_rate",
            )
            ->where("batch_no", $batch_no)
            ->where("pharmacy_id", $med_id)
            ->get("medicine_batch_details");
        return $query->row_array();
    }

    public function getQuantityedit($batch_no)
    {
        $query = $this->db
            ->select(
                "medicine_batch_details.id,medicine_batch_details.available_quantity,medicine_batch_details.quantity,medicine_batch_details.purchase_price,medicine_batch_details.sale_rate",
            )
            ->where("batch_no", $batch_no)
            ->get("medicine_batch_details");
        return $query->row_array();
    }

    public function checkvalid_medicine_exists($str)
    {
        $medicine_name = $this->input->post("medicine_name");
        if ($this->check_medicie_exists($medicine_name)) {
            $this->form_validation->set_message(
                "check_exists",
                "Record already exists",
            );
            return false;
        } else {
            return true;
        }
    }

    public function check_medicie_exists($name, $id)
    {
        if ($id != 0) {
            $data = ["id != " => $id, "medicine_name" => $name];
            $query = $this->db->where($data)->get("pharmacy");
            if ($query->num_rows() > 0) {
                return true;
            } else {
                return false;
            }
        } else {
            $this->db->where("medicine_name", $name);
            $query = $this->db->get("pharmacy");
            if ($query->num_rows() > 0) {
                return true;
            } else {
                return false;
            }
        }
    }

    public function availableQty($update_quantity)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $query = $this->db
            ->where("id", $update_quantity["id"])
            ->update("medicine_batch_details", $update_quantity);
        $message =
            UPDATE_RECORD_CONSTANT .
            " On Medicine Batch Details id " .
            $update_quantity["id"];
        $action = "Update";
        $record_id = $update_quantity["id"];
        $this->log($message, $record_id, $action);
        //======================Code End==============================

        $this->db->trans_complete(); # Completing transaction
        /* Optional */

        if ($this->db->trans_status() === false) {
            # Something went wrong.
            $this->db->trans_rollback();
            return false;
        } else {
            return $record_id;
        }
    }

    public function getsingleMedicineBatchdetails($medicine_batch_id)
    {
        $query = $this->db
            ->select("available_quantity")
            ->where("id", $medicine_batch_id)
            ->get("medicine_batch_details");
        return $query->row_array();
    }

    public function totalQuantity($pharmacy_id)
    {
        $query = $this->db
            ->select("sum(available_quantity) as total_qty")
            ->where("pharmacy_id", $pharmacy_id)
            ->get("medicine_batch_details");
        return $query->row_array();
    }

    public function searchBillReport($date_from, $date_to)
    {
        $this->db->select("pharmacy_bill_basic.*");
        $this->db->where("date >=", $date_from);
        $this->db->where("date <=", $date_to);
        $query = $this->db->get("pharmacy_bill_basic");
        return $query->result_array();
    }

    public function delete_medicine_batch($id)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $this->db->where("id", $id)->delete("medicine_batch_details");
        $message =
            DELETE_RECORD_CONSTANT . " On Medicine Batch Details id " . $id;
        $action = "Delete";
        $record_id = $id;
        $this->log($message, $record_id, $action);
        //======================Code End==============================

        $this->db->trans_complete(); # Completing transaction
        /* Optional */

        if ($this->db->trans_status() === false) {
            # Something went wrong.
            $this->db->trans_rollback();
            return false;
        } else {
            return $record_id;
        }
    }

    public function delete_bill_detail($delete_arr)
    {
        foreach ($delete_arr as $key => $value) {
            $id = $value["id"];
            $this->db->where("id", $id)->delete("prescription");
        }
    }

    public function getBillNo()
    {
        $query = $this->db->select("max(id) as id")->get("pharmacy_bill_basic");
        return $query->row_array();
    }

    public function getExpiryDate($medicine_batch_detail_id)
    {
        $query = $this->db
            ->where("id", $medicine_batch_detail_id)
            ->get("medicine_batch_details");
        return $query->row_array();
    }

    public function getMedicineBatchByID(
        $medicine_batch_detail_id = null,
        $organisation_id = null,
    ) {
        $sql =
            "SELECT medicine_batch_details.*, `organisations_medicine_charges`.`org_charge`,IFNULL((SELECT SUM(quantity) FROM `pharmacy_bill_detail` WHERE medicine_batch_detail_id=medicine_batch_details.id),0) as used_quantity FROM `medicine_batch_details`
        left join organisations_medicine_charges on organisations_medicine_charges.medicine_batch_details_id=medicine_batch_details.id
        and organisations_medicine_charges.org_id=" .
            $this->db->escape($organisation_id) .
            "
        WHERE medicine_batch_details.id=" .
            $this->db->escape($medicine_batch_detail_id);

        $query = $this->db->query($sql);
        return $query->row();
    }

    public function getExpireDate($batch_no)
    {
        $query = $this->db
            ->where("batch_no", $batch_no)
            ->get("medicine_batch_details");
        return $query->row_array();
    }

    public function getmedicinedetailsbyid($id)
    {
        $query = $this->db->where("pharmacy.id", $id)->get("pharmacy");
        return $query->row_array();
    }

    public function getBatchNoList($pharmacy_id, $batch_id = 0)
    {
        if ($batch_id > 0) {
            $sql =
                "SELECT medicine_batch_details.*, (medicine_batch_details.available_quantity-IFNULL((SELECT SUM(quantity) FROM `pharmacy_bill_detail` WHERE medicine_batch_detail_id=medicine_batch_details.id),0)) as remaining_quantity FROM `medicine_batch_details` WHERE medicine_batch_details.pharmacy_id=" .
                $this->db->escape($pharmacy_id);
        } else {
            $sql =
                "SELECT medicine_batch_details.*, (medicine_batch_details.available_quantity-IFNULL((SELECT SUM(quantity) FROM `pharmacy_bill_detail` WHERE medicine_batch_detail_id=medicine_batch_details.id),0)) as remaining_quantity FROM `medicine_batch_details` WHERE medicine_batch_details.pharmacy_id=" .
                $this->db->escape($pharmacy_id) .
                " HAVING remaining_quantity > 0";
        }

        $query = $this->db->query($sql);
        return $query->result_array();
    }

    public function addBadStock($data)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $this->db->insert("medicine_bad_stock", $data);
        $insert_id = $this->db->insert_id();
        $message =
            INSERT_RECORD_CONSTANT . " On Medicine Bad Stock id " . $insert_id;
        $action = "Insert";
        $record_id = $insert_id;
        $this->log($message, $record_id, $action);
        //======================Code End==============================

        $this->db->trans_complete(); # Completing transaction
        /* Optional */

        if ($this->db->trans_status() === false) {
            # Something went wrong.
            $this->db->trans_rollback();
            return false;
        } else {
            return $record_id;
        }
    }

    public function updateMedicineBatch($data)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $this->db
            ->where("id", $data["id"])
            ->update("medicine_batch_details", $data);
        $message =
            UPDATE_RECORD_CONSTANT .
            " On Medicine Batch Details id " .
            $data["id"];
        $action = "Update";
        $record_id = $data["id"];
        $this->log($message, $record_id, $action);
        //======================Code End==============================
        $this->db->trans_complete(); # Completing transaction
        /* Optional */

        if ($this->db->trans_status() === false) {
            # Something went wrong.
            $this->db->trans_rollback();
            return false;
        } else {
            return $record_id;
        }
    }

    public function getMedicineBadStock($id)
    {
        $query = $this->db
            ->where("pharmacy_id", $id)
            ->get("medicine_bad_stock");
        return $query->result();
    }

    public function getsingleMedicineBadStock($id)
    {
        $query = $this->db->where("id", $id)->get("medicine_bad_stock");
        return $query->row_array();
    }

    public function deleteBadStock($id)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $this->db->where("id", $id)->delete("medicine_bad_stock");
        $message = DELETE_RECORD_CONSTANT . " On Medicine Bad Stock id " . $id;
        $action = "Delete";
        $record_id = $id;
        $this->log($message, $record_id, $action);
        //======================Code End==============================

        $this->db->trans_complete(); # Completing transaction
        /* Optional */

        if ($this->db->trans_status() === false) {
            # Something went wrong.
            $this->db->trans_rollback();
            return false;
        } else {
            return $record_id;
        }
    }

    public function searchNameLike($category, $value)
    {
        $query = $this->db
            ->where("medicine_category_id", $category)
            ->like("medicine_name", $value)
            ->get("pharmacy");
        return $query->result_array();
    }

    public function validate_paymentamount()
    {
        $final_amount = 0;
        $amount = $this->input->post("amount");
        $payment_amount = $this->input->post("payment_amount");
        if (!empty($amount)) {
            $final_amount = $amount;
        } elseif (!empty($payment_amount)) {
            $final_amount = $payment_amount;
        }

        $net_amount = $this->input->post("net_amount");
        if ($final_amount > $net_amount) {
            $this->form_validation->set_message(
                "check_exists",
                $this->lang->line("amount_should_not_be_greater_than_balance") .
                    " " .
                    $net_amount,
            );
            return false;
        } else {
            return true;
        }
    }

    public function getIpdPrescriptionBasic($ipd_prescription_basic_id)
    {
        $this->db->select("ipd_prescription_basic.*");
        $this->db->where(
            "ipd_prescription_basic.id",
            $ipd_prescription_basic_id,
        );
        $query = $this->db->get("ipd_prescription_basic");
        return $query->row();
    }

    public function getpharmacydoctor()
    {
        return $this->db
            ->select("pharmacy_bill_basic.doctor_name")
            ->group_by("pharmacy_bill_basic.doctor_name")
            ->from("pharmacy_bill_basic")
            ->get()
            ->result_array();
    }

    public function update_sale_rate($data)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        if (isset($data) && !empty($data)) {
            foreach ($data as $key => $value) {
                $sale_rate = $value["sale_rate"];
                $this->db->where("id", $value["id"]);
                $this->db->update("medicine_batch_details", [
                    "sale_rate" => $sale_rate,
                ]);
                $message =
                    UPDATE_RECORD_CONSTANT .
                    " On  medicine_batch_details id " .
                    $value["id"];
                $action = "Update";
                $record_id = $value["id"];
                $this->log($message, $record_id, $action);
            }
            //======================Code End==============================
            $this->db->trans_complete(); # Completing transaction
            /* Optional */
            if ($this->db->trans_status() === false) {
                # Something went wrong.
                $this->db->trans_rollback();
                return false;
            }
        }
    }

    public function addunit($data)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        if (isset($data["id"])) {
            $this->db->where("id", $data["id"]);
            $this->db->update("unit", $data);
            $message = UPDATE_RECORD_CONSTANT . " On Unit id " . $data["id"];
            $action = "Update";
            $record_id = $data["id"];
            $this->log($message, $record_id, $action);
        } else {
            $this->db->insert("unit", $data);
            $insert_id = $this->db->insert_id();
            $message = INSERT_RECORD_CONSTANT . " On Unit id " . $insert_id;
            $action = "Insert";
            $record_id = $insert_id;
            $this->log($message, $record_id, $action);
        }
        //======================Code End==============================

        $this->db->trans_complete(); # Completing transaction
        /* Optional */

        if ($this->db->trans_status() === false) {
            # Something went wrong.
            $this->db->trans_rollback();
            return false;
        } else {
            return $record_id;
        }
    }

    public function getpharmacyunit($id = null)
    {
        if (!empty($id)) {
            $this->db->select("unit.*");
            $this->db->where("id", $id);
            $this->db->where("unit_type", "pharmacy");
            $query = $this->db->get("unit");
            return $query->row_array();
        } else {
            $this->db->select("unit.*");
            $this->db->where("unit_type", "pharmacy");
            $query = $this->db->get("unit");
            return $query->result_array();
        }
    }

    public function deletepharmacyunit($id)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $this->db->where("id", $id)->delete("unit");
        $message = DELETE_RECORD_CONSTANT . " On Unit id " . $id;
        $action = "Delete";
        $record_id = $id;

        $this->log($message, $record_id, $action);
        //======================Code End==============================

        $this->db->trans_complete(); # Completing transaction
        /* Optional */

        if ($this->db->trans_status() === false) {
            # Something went wrong.
            $this->db->trans_rollback();
            return false;
        } else {
            return $record_id;
        }
    }

    public function add_company($data)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        if (isset($data["id"])) {
            $this->db->where("id", $data["id"]);
            $this->db->update("pharmacy_company", $data);
            $message = UPDATE_RECORD_CONSTANT . " On Company id " . $data["id"];
            $action = "Update";
            $record_id = $data["id"];
            $this->log($message, $record_id, $action);
        } else {
            $this->db->insert("pharmacy_company", $data);
            $insert_id = $this->db->insert_id();
            $message = INSERT_RECORD_CONSTANT . " On Company id " . $insert_id;
            $action = "Insert";
            $record_id = $insert_id;
            $this->log($message, $record_id, $action);
        }
        //======================Code End==============================

        $this->db->trans_complete(); # Completing transaction
        /* Optional */

        if ($this->db->trans_status() === false) {
            # Something went wrong.
            $this->db->trans_rollback();
            return false;
        } else {
            return $record_id;
        }
    }

    public function deletecompany($id)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $this->db->where("id", $id)->delete("pharmacy_company");
        $message = DELETE_RECORD_CONSTANT . " On company id " . $id;
        $action = "Delete";
        $record_id = $id;

        $this->log($message, $record_id, $action);
        //======================Code End==============================

        $this->db->trans_complete(); # Completing transaction
        /* Optional */

        if ($this->db->trans_status() === false) {
            # Something went wrong.
            $this->db->trans_rollback();
            return false;
        } else {
            return $record_id;
        }
    }

    public function getcomapnyname($id = null)
    {
        if (!empty($id)) {
            $this->db->select("pharmacy_company.*");
            $this->db->where("id", $id);
            $query = $this->db->get("pharmacy_company");
            return $query->row_array();
        } else {
            $this->db->select("pharmacy_company.*");
            $query = $this->db->get("pharmacy_company");
            return $query->result_array();
        }
    }

    public function add_medicine_group($data)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        if (isset($data["id"])) {
            $this->db->where("id", $data["id"]);
            $this->db->update("medicine_group", $data);
            $message =
                UPDATE_RECORD_CONSTANT . " On medicine group id " . $data["id"];
            $action = "Update";
            $record_id = $data["id"];
            $this->log($message, $record_id, $action);
        } else {
            $this->db->insert("medicine_group", $data);
            $insert_id = $this->db->insert_id();
            $message =
                INSERT_RECORD_CONSTANT . " On medicine group id " . $insert_id;
            $action = "Insert";
            $record_id = $insert_id;
            $this->log($message, $record_id, $action);
        }
        //======================Code End==============================

        $this->db->trans_complete(); # Completing transaction
        /* Optional */

        if ($this->db->trans_status() === false) {
            # Something went wrong.
            $this->db->trans_rollback();
            return false;
        } else {
            return $record_id;
        }
    }

    public function get_medicine_group($id = null)
    {
        if (!empty($id)) {
            $this->db->select("medicine_group.*");
            $this->db->where("id", $id);
            $query = $this->db->get("medicine_group");
            return $query->row_array();
        } else {
            $this->db->select("medicine_group.*");
            $query = $this->db->get("medicine_group");
            return $query->result_array();
        }
    }

    public function deletegroup($id)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $this->db->where("id", $id)->delete("medicine_group");
        $message = DELETE_RECORD_CONSTANT . " On medicine group id " . $id;
        $action = "Delete";
        $record_id = $id;
        $this->log($message, $record_id, $action);
        //======================Code End==============================

        $this->db->trans_complete(); # Completing transaction
        /* Optional */

        if ($this->db->trans_status() === false) {
            # Something went wrong.
            $this->db->trans_rollback();
            return false;
        } else {
            return $record_id;
        }
    }

    public function addtpacharge($data)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        if (isset($data["id"]) && $data["id"] != "") {
            $this->db
                ->where("id", $data["id"])
                ->update("organisations_medicine_charges", $data);
            $record_id = $data["id"];
        } else {
            $this->db->insert("organisations_medicine_charges", $data);
            $record_id = $this->db->insert_id();
        }
        //======================Code End==============================
        $this->db->trans_complete(); # Completing transaction
        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            return false;
        } else {
            return $record_id;
        }
    }

    // ================================================================
    // PURCHASE RETURN METHODS
    // ================================================================

    public function getMedicineBatchesForPurchase($supplier_bill_basic_id)
    {
        $query = $this->db
            ->select(
                "medicine_batch_details.id, medicine_batch_details.pharmacy_id, medicine_batch_details.batch_no, medicine_batch_details.purchase_price, medicine_batch_details.available_quantity, medicine_batch_details.expiry, pharmacy.medicine_name",
            )
            ->join(
                "pharmacy",
                "pharmacy.id = medicine_batch_details.pharmacy_id",
                "left",
            )
            ->where(
                "medicine_batch_details.supplier_bill_basic_id",
                $supplier_bill_basic_id,
            )
            ->where("medicine_batch_details.available_quantity >", 0)
            ->order_by("pharmacy.medicine_name", "asc")
            ->get("medicine_batch_details");
        return $query->result_array();
    }

    public function addPurchaseReturn($data)
    {
        $this->db->trans_start();
        $this->db->trans_strict(false);
        $this->db->insert("purchase_return_basic", $data);
        $insert_id = $this->db->insert_id();
        $message =
            INSERT_RECORD_CONSTANT .
            " On Purchase Return Basic id " .
            $insert_id;
        $this->log($message, $insert_id, "Insert");
        $this->db->trans_complete();
        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            return false;
        }
        return $insert_id;
    }

    public function addPurchaseReturnDetail($data)
    {
        $this->db->trans_start();
        $this->db->trans_strict(false);
        $this->db->insert_batch("purchase_return_detail", $data);
        $this->log(
            INSERT_RECORD_CONSTANT .
                " On Purchase Return Detail (" .
                count($data) .
                " rows)",
            0,
            "Insert",
        );
        $this->db->trans_complete();
        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            return false;
        }
        return true;
    }

    public function updateBatchAvailableQtyBulk(array $detail_data)
    {
        $this->db->trans_start();
        $this->db->trans_strict(false);
        foreach ($detail_data as $row) {
            $this->db
                ->set(
                    "available_quantity",
                    "available_quantity - " . (int) $row["quantity"],
                    false,
                )
                ->where("id", $row["medicine_batch_details_id"])
                ->update("medicine_batch_details");
            $this->log(
                UPDATE_RECORD_CONSTANT .
                    " On Medicine Batch Details id " .
                    $row["medicine_batch_details_id"] .
                    " qty decremented by " .
                    $row["quantity"],
                $row["medicine_batch_details_id"],
                "Update",
            );
        }
        $this->db->trans_complete();
        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            return false;
        }
        return true;
    }

    public function updateBatchAvailableQty($batch_id, $return_qty)
    {
        $this->db->trans_start();
        $this->db->trans_strict(false);
        $this->db
            ->set(
                "available_quantity",
                "available_quantity - " . (int) $return_qty,
                false,
            )
            ->where("id", $batch_id)
            ->update("medicine_batch_details");
        $message =
            UPDATE_RECORD_CONSTANT .
            " On Medicine Batch Details id " .
            $batch_id .
            " qty decremented by " .
            $return_qty;
        $this->log($message, $batch_id, "Update");
        $this->db->trans_complete();
        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            return false;
        }
        return true;
    }

    public function getPurchaseReturnHistoryCount($supplier_bill_basic_id)
    {
        return $this->db
            ->where("supplier_bill_basic_id", $supplier_bill_basic_id)
            ->count_all_results("purchase_return_basic");
    }

    public function getPurchaseReturnedAmount($supplier_bill_basic_id)
    {
        $query = $this->db
            ->select("SUM(total_amount) as returned_amount")
            ->where("supplier_bill_basic_id", $supplier_bill_basic_id)
            ->get("purchase_return_basic");
        $row = $query->row_array();
        return $row["returned_amount"] ? (float) $row["returned_amount"] : 0;
    }

    public function getPurchaseReturnHistory($supplier_bill_basic_id)
    {
        $query = $this->db
            ->select(
                "purchase_return_basic.id, purchase_return_basic.return_date, purchase_return_basic.total_amount, purchase_return_basic.reason, purchase_return_basic.note, staff.name as returned_by_name, staff.surname as returned_by_surname",
            )
            ->join(
                "staff",
                "staff.id = purchase_return_basic.returned_by",
                "left",
            )
            ->where(
                "purchase_return_basic.supplier_bill_basic_id",
                $supplier_bill_basic_id,
            )
            ->order_by("purchase_return_basic.id", "desc")
            ->get("purchase_return_basic");
        return $query->result_array();
    }

    public function getPurchaseReturnDetail($purchase_return_basic_id)
    {
        $query = $this->db
            ->select(
                "purchase_return_detail.id, purchase_return_detail.batch_no, purchase_return_detail.quantity, purchase_return_detail.purchase_price, purchase_return_detail.amount, pharmacy.medicine_name",
            )
            ->join(
                "pharmacy",
                "pharmacy.id = purchase_return_detail.pharmacy_id",
                "left",
            )
            ->where(
                "purchase_return_detail.purchase_return_basic_id",
                $purchase_return_basic_id,
            )
            ->order_by("pharmacy.medicine_name", "asc")
            ->get("purchase_return_detail");
        return $query->result_array();
    }

    // =====================================================================
    // SALE RETURN METHODS
    // =====================================================================

    public function getSaleReturnBillsList()
    {
        $query = $this->db
            ->select(
                'pharmacy_return_basic.id, pharmacy_return_basic.pharmacy_bill_basic_id, pharmacy_return_basic.return_no, pharmacy_return_basic.date, pharmacy_return_basic.total, pharmacy_return_basic.discount, pharmacy_return_basic.tax, pharmacy_return_basic.net_amount, pharmacy_return_basic.patient_id, pharmacy_return_basic.returned_by as returned_by_id, staff.employee_id as returned_by_employee_id, IFNULL(patients.patient_name, pharmacy_return_basic.customer_name) as patient_name, CONCAT(staff.name, " ", staff.surname) as returned_by_name',
            )
            ->join(
                "patients",
                "patients.id = pharmacy_return_basic.patient_id",
                "left",
            )
            ->join(
                "staff",
                "staff.id = pharmacy_return_basic.returned_by",
                "left",
            )
            ->order_by("pharmacy_return_basic.id", "desc")
            ->get("pharmacy_return_basic");
        return $query->result_array();
    }

    public function getPreviousReturnsForBill($bill_id)
    {
        $query = $this->db
            ->select(
                "id, return_no, date, total, discount, discount_percentage, tax, net_amount",
            )
            ->where("pharmacy_bill_basic_id", $bill_id)
            ->order_by("id", "asc")
            ->get("pharmacy_return_basic")
            ->result_array();
        return $query;
    }

    public function getSaleReturnHistoryForBill($bill_id)
    {
        $query = $this->db
            ->select(
                'pharmacy_return_basic.id, pharmacy_return_basic.return_no, pharmacy_return_basic.date,
                 pharmacy_return_basic.total, pharmacy_return_basic.discount, pharmacy_return_basic.tax,
                 pharmacy_return_basic.net_amount, pharmacy_return_basic.note,
                 CONCAT(staff.name, " ", staff.surname) as returned_by_name',
            )
            ->join(
                "staff",
                "staff.id = pharmacy_return_basic.returned_by",
                "left",
            )
            ->where("pharmacy_return_basic.pharmacy_bill_basic_id", $bill_id)
            ->order_by("pharmacy_return_basic.id", "asc")
            ->get("pharmacy_return_basic");
        return $query->result_array();
    }

    public function getSaleReturnCountForBill($bill_id)
    {
        return $this->db
            ->where("pharmacy_bill_basic_id", (int) $bill_id)
            ->count_all_results("pharmacy_return_basic");
    }

    public function getSaleReturnNo()
    {
        $query = $this->db
            ->select("max(id) as id")
            ->get("pharmacy_return_basic");
        return $query->row_array();
    }

    public function addSaleReturn(
        $basic_data,
        $detail_array,
        $payment_array = [],
    ) {
        $this->db->trans_start();
        $this->db->trans_strict(false);

        // Step 1: Insert return header
        $this->db->insert("pharmacy_return_basic", $basic_data);
        $return_basic_id = $this->db->insert_id();

        // Step 2: Insert return detail rows
        foreach ($detail_array as $detail) {
            $detail["pharmacy_return_basic_id"] = $return_basic_id;
            $this->db->insert("pharmacy_return_detail", $detail);
        }

        // Step 3: Update original sale bill (only if return is against a specific bill)
        $bill_id = !empty($basic_data["pharmacy_bill_basic_id"])
            ? (int) $basic_data["pharmacy_bill_basic_id"]
            : 0;
        if ($bill_id > 0) {
            // Fetch old net_amount BEFORE update (required to calculate exact refund amount)
            $old_bill = $this->db
                ->select("net_amount, discount_percentage, tax_percentage")
                ->where("id", $bill_id)
                ->get("pharmacy_bill_basic")
                ->row_array();
            $old_net_amount = !empty($old_bill["net_amount"])
                ? (float) $old_bill["net_amount"]
                : 0;

            // 3a: Reduce quantity in pharmacy_bill_detail for each returned item
            foreach ($detail_array as $item) {
                $bill_detail_row = $this->db
                    ->select("id, quantity, sale_price, discount")
                    ->where("pharmacy_bill_basic_id", $bill_id)
                    ->where(
                        "medicine_batch_detail_id",
                        $item["medicine_batch_detail_id"],
                    )
                    ->get("pharmacy_bill_detail")
                    ->row_array();

                if (!empty($bill_detail_row)) {
                    // Hard stop: return qty must not exceed actual sold qty (DB check — not from POST)
                    if (
                        (float) $item["quantity"] >
                        (float) $bill_detail_row["quantity"]
                    ) {
                        $this->db->trans_rollback();
                        return false;
                    }
                    $new_qty =
                        (float) $bill_detail_row["quantity"] -
                        (float) $item["quantity"];
                    $this->db
                        ->where("id", $bill_detail_row["id"])
                        ->update("pharmacy_bill_detail", [
                            "quantity" => $new_qty,
                        ]);
                }
            }

            // 3b: Recalculate pharmacy_bill_basic totals from updated detail rows
            // JOIN medicine_batch_details to get per-item tax rate (tax_percentage in basic is 0)
            $remaining = $this->db
                ->select(
                    "pharmacy_bill_detail.quantity, pharmacy_bill_detail.sale_price, pharmacy_bill_detail.discount, medicine_batch_details.tax as tax_rate",
                )
                ->join(
                    "medicine_batch_details",
                    "medicine_batch_details.id = pharmacy_bill_detail.medicine_batch_detail_id",
                    "left",
                )
                ->where("pharmacy_bill_basic_id", $bill_id)
                ->get("pharmacy_bill_detail")
                ->result_array();

            $disc_pct = !empty($old_bill["discount_percentage"])
                ? (float) $old_bill["discount_percentage"]
                : 0;
            $new_total = 0;
            $new_tax = 0;
            foreach ($remaining as $row) {
                $disc_per_unit =
                    ((float) $row["sale_price"] * (float) $row["discount"]) /
                    100;
                $sub_total =
                    (float) $row["quantity"] *
                    ((float) $row["sale_price"] - $disc_per_unit);
                // Tax base = sub_total minus its share of gross discount (mirrors sr_update_amount JS)
                $row_gross_disc = ($sub_total * $disc_pct) / 100;
                $row_tax =
                    (($sub_total - $row_gross_disc) *
                        (float) $row["tax_rate"]) /
                    100;
                $new_total += $sub_total;
                $new_tax += $row_tax;
            }

            $discount = ($new_total * $disc_pct) / 100;
            $new_net = round($new_total - $discount + $new_tax, 2);

            $this->db->where("id", $bill_id)->update("pharmacy_bill_basic", [
                "total" => round($new_total, 2),
                "discount" => round($discount, 2),
                "tax" => round($new_tax, 2),
                "net_amount" => $new_net,
            ]);

            // 3c: Insert refund transaction — amount = actual reduction (old_net - new_net)
            // This guarantees: balance = new_net - paid + refund = old_net - paid (unchanged)
            $refund_amount = round($old_net_amount - $new_net, 2);
            if ($refund_amount > 0) {
                $refund_transaction = [
                    "amount" => $refund_amount,
                    "type" => "refund",
                    "patient_id" => !empty($basic_data["patient_id"])
                        ? $basic_data["patient_id"]
                        : null,
                    "section" => !empty($payment_array["section"])
                        ? $payment_array["section"]
                        : "Pharmacy",
                    "pharmacy_bill_basic_id" => $bill_id,
                    "payment_mode" => !empty($payment_array["payment_mode"])
                        ? $payment_array["payment_mode"]
                        : "Cash",
                    "note" => !empty($basic_data["note"])
                        ? $basic_data["note"]
                        : null,
                    "payment_date" => date("Y-m-d H:i:s"),
                    "received_by" => $basic_data["returned_by"],
                ];
                if (!empty($payment_array["cheque_no"])) {
                    $refund_transaction["cheque_no"] =
                        $payment_array["cheque_no"];
                    $refund_transaction["cheque_date"] =
                        $payment_array["cheque_date"];
                }
                if (!empty($payment_array["attachment"])) {
                    $refund_transaction["attachment"] =
                        $payment_array["attachment"];
                    $refund_transaction["attachment_name"] =
                        $payment_array["attachment_name"];
                }
                $this->db->insert("transactions", $refund_transaction);
            }
        }

        $this->log(
            "Sale return created",
            $return_basic_id,
            INSERT_RECORD_CONSTANT,
        );
        $this->db->trans_complete();

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            return false;
        }
        return $return_basic_id;
    }

    public function getSaleReturnDetails($id)
    {
        $query = $this->db
            ->select(
                'pharmacy_return_basic.id, pharmacy_return_basic.return_no, pharmacy_return_basic.date,
                 pharmacy_return_basic.patient_id, pharmacy_return_basic.customer_name, pharmacy_return_basic.customer_type,
                 pharmacy_return_basic.doctor_name, pharmacy_return_basic.total, pharmacy_return_basic.discount,
                 pharmacy_return_basic.discount_percentage, pharmacy_return_basic.tax, pharmacy_return_basic.tax_percentage,
                 pharmacy_return_basic.net_amount, pharmacy_return_basic.note,
                 CONCAT(patients.firstname, " ", patients.lastname) as patient_name',
            )
            ->join(
                "patients",
                "patients.id = pharmacy_return_basic.patient_id",
                "left",
            )
            ->where("pharmacy_return_basic.id", $id)
            ->get("pharmacy_return_basic");
        return $query->row_array();
    }

    public function getSaleReturnItems($return_basic_id)
    {
        $query = $this->db
            ->select(
                'pharmacy_return_detail.id, pharmacy_return_detail.quantity, pharmacy_return_detail.sale_price,
                 pharmacy_return_detail.discount, pharmacy_return_detail.amount,
                 pharmacy.medicine_name, medicine_batch_details.batch_no, medicine_batch_details.expiry,
                 medicine_category.medicine_category as category_name',
            )
            ->join(
                "medicine_batch_details",
                "medicine_batch_details.id = pharmacy_return_detail.medicine_batch_detail_id",
                "left",
            )
            ->join(
                "pharmacy",
                "pharmacy.id = medicine_batch_details.pharmacy_id",
                "left",
            )
            ->join(
                "medicine_category",
                "medicine_category.id = pharmacy.medicine_category_id",
                "left",
            )
            ->where(
                "pharmacy_return_detail.pharmacy_return_basic_id",
                $return_basic_id,
            )
            ->order_by("pharmacy.medicine_name", "asc")
            ->get("pharmacy_return_detail");
        return $query->result_array();
    }

    /**
     * Dashboard widget: low-stock summary.
     * Items where total available_quantity < min_level.
     * Returns: ['count'=>N, 'critical_count'=>N (out of stock), 'critical_names'=>"name1, name2, name3"]
     * Used by admin/admin/dashboard Variant B (2026-05-15).
     */
    public function getLowStockSummary()
    {
        // INNER JOIN medicine_category mirrors the Medicines Stock page (Pharmacy_model::searchFullText),
        // which only lists medicines that have a valid category. This keeps the dashboard count in sync
        // with that page (medicines whose category was deleted/orphaned are excluded from both).
        $sql = "
            SELECT pharmacy.medicine_name,
                   pharmacy.min_level,
                   IFNULL((SELECT SUM(available_quantity) FROM medicine_batch_details WHERE pharmacy_id = pharmacy.id), 0) AS total_qty
            FROM pharmacy
            INNER JOIN medicine_category ON medicine_category.id = pharmacy.medicine_category_id
            WHERE pharmacy.min_level > 0
            HAVING total_qty < pharmacy.min_level
            ORDER BY (pharmacy.min_level - total_qty) DESC
        ";
        $rows = $this->db->query($sql)->result_array();

        $count = count($rows);
        $critical_count = 0;
        foreach ($rows as $r) {
            if ((int)$r['total_qty'] <= 0) {
                $critical_count++;
            }
        }
        $top_names = array_slice(array_column($rows, 'medicine_name'), 0, 3);
        return array(
            'count'          => $count,
            'critical_count' => $critical_count,
            'critical_names' => implode(', ', $top_names),
        );
    }

    /**
     * Dashboard widget: number of medicines that have in-stock batches
     * expiring within the next $days days.
     * medicine_batch_details.expiry is stored as a DATE (last day of the expiry month),
     * so direct date comparison is safe.
     */
    public function getExpiringSoonCount($days = 30)
    {
        $this->db->select('COUNT(DISTINCT pharmacy_id) AS cnt', false);
        $this->db->where('available_quantity >', 0);
        $this->db->where('expiry >=', date('Y-m-d'));
        $this->db->where('expiry <=', date('Y-m-d', strtotime('+' . (int)$days . ' days')));
        $row = $this->db->get('medicine_batch_details')->row_array();
        return (int)($row['cnt'] ?? 0);
    }
}
