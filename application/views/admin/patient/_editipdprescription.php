<?php
if (!function_exists('_check_test_ipd')) {
    function _check_test_ipd($type, $id, $array) {
        if (!empty($array->tests)) {
            foreach ($array->tests as $test_value) {
                if ($type == 'pathology' && $test_value->pathology_id == $id) return true;
                if ($type == 'radiology' && $test_value->radiology_id == $id) return true;
            }
        }
        return false;
    }
}
if (!empty($result->tests)) {
    foreach ($result->tests as $test_prev_value) {
        if ($test_prev_value->pathology_id != "") {
            echo '<input type="hidden" name="prev_pathology[]" value="' . $test_prev_value->pathology_id . '">';
        } elseif ($test_prev_value->radiology_id != "") {
            echo '<input type="hidden" name="prev_radiology[]" value="' . $test_prev_value->radiology_id . '">';
        }
    }
}
?>
<input type="hidden" name="action" value="update">
<input type="hidden" name="ipd_id" value="<?php echo $result->ipd_id; ?>">
<input type="hidden" name="ipd_prescription_basic_id" value="<?php echo $prescription_id; ?>">
<div class="row">

    <!-- LEFT: Prescription details -->
    <div class="col-lg-9 border-end opd-rx-left">

        <!-- Header Note -->
        <div class="sh-form-card mb-3">
            <div class="sh-card-header">
                <span class="sh-card-header-title"><i class="fa fa-sticky-note me-1"></i> <?php echo $this->lang->line('header_note'); ?></span>
            </div>
            <div class="p-2">
                <textarea name="header_note" class="form-control opd-note-area" id="compose-textareanew"><?php echo html_escape($result->header_note); ?></textarea>
            </div>
        </div>

        <!-- Findings -->
        <div class="sh-form-card mb-3 findings-section sh-overflow-visible" >
            <div class="sh-card-header">
                <span class="sh-card-header-title"><i class="fa fa-stethoscope me-1"></i> <?php echo $this->lang->line('findings'); ?></span>
            </div>
            <div class="p-2 sh-overflow-visible" >
                <div class="row g-3 align-items-start">
                    <div class="col-md-3">
                        <label class="form-label mb-1"><?php echo $this->lang->line('finding_category'); ?></label>
                        <select class="form-control select2 findingtype w-100"  name='finding_type[]' id="finding_type" multiple>
                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                            <?php foreach ($findingresult as $fvalue) { ?>
                                <option value="<?php echo $fvalue["id"]; ?>"><?php echo $fvalue["category"]; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="col-md-3 position-relative" >
                        <label class="form-label mb-1" for="filterinput"><?php echo $this->lang->line('finding_list'); ?></label>
                        <div id="dd" class="wrapper-dropdown-3">
                            <input class="form-control filterinput height-33" type="text" placeholder="<?php echo $this->lang->line('select'); ?>">
                            <ul class="dropdown scroll150 section_ul">
                                <li><label class="checkbox"><?php echo $this->lang->line('select'); ?></label></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label mb-1"><?php echo $this->lang->line('finding_description'); ?></label>
                        <textarea name="finding_description" id="finding_description" rows="3" class="form-control"><?php echo html_escape($result->finding_description); ?></textarea>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label mb-1 d-block"><?php echo $this->lang->line('finding_print'); ?></label>
                        <div class="form-check mt-2">
                            <input class="form-check-input" type="checkbox" id="finding_print_chk_edit" name="finding_print" value="yes" <?php if ($result->is_finding_print == 'yes') echo 'checked'; ?>>
                            <label class="form-check-label" for="finding_print_chk_edit"><?php echo $this->lang->line('yes'); ?></label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Medicines -->
        <div class="sh-form-card mb-3">
            <div class="sh-card-header">
                <span class="sh-card-header-title flex-grow-1"><i class="fa fa-medkit me-1"></i> <?php echo $this->lang->line('medicine'); ?></span>
                <a class="btn btn-sm btn-info addplus-xs add-record" data-added="0">
                    <i class="fa fa-plus me-1"></i><?php echo $this->lang->line('add_medicine'); ?>
                </a>
            </div>
            <div class="p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th class="sh-mw-min-160"><i class="fa fa-medkit me-1 text-muted"></i><?php echo $this->lang->line('medicine'); ?></th>
                                <th style="min-width:110px"><i class="fa fa-tint me-1 text-muted"></i><?php echo $this->lang->line("dose"); ?></th>
                                <th class="sh-mw-min-120"><i class="fa fa-clock-o me-1 text-muted"></i><?php echo $this->lang->line("dose_interval"); ?></th>
                                <th class="sh-mw-min-120"><i class="fa fa-calendar me-1 text-muted"></i><?php echo $this->lang->line("dose_duration"); ?></th>
                                <th class="sh-mw-min-160"><i class="fa fa-info-circle me-1 text-muted"></i><?php echo $this->lang->line('instruction'); ?></th>
                                <th class="text-center" style="width:50px"><?php echo $this->lang->line('action'); ?></th>
                            </tr>
                        </thead>
                        <tbody id="tableID">
                            <?php
                            $medicine_row = 1;
                            foreach ($result->medicines as $medicine_key => $medicine_value) {
                            ?>
                            <input type="hidden" name="prev_medicine[]" value="<?php echo $medicine_value->ipd_prescription_detail_id; ?>">
                            <tr id="row<?php echo $medicine_row; ?>">
                                <td>
                                    <input type="hidden" name="rows[]" value="<?php echo $medicine_row; ?>">
                                    <input type="hidden" name="ipd_prescription_detail_id_<?php echo $medicine_row; ?>" value="<?php echo $medicine_value->ipd_prescription_detail_id; ?>">
                                    <input type="hidden" name="post_pharmacy_id" class="post_medicine_id" value="<?php echo $medicine_value->pharmacy_id; ?>">
                                    <input type="hidden" name="post_dosage_id" class="post_dosage_id" value="<?php echo $medicine_value->dosage_id; ?>">
                                    <input type="hidden" name="medicine_cat_<?php echo $medicine_row; ?>" value="1">
                                    <select data-rowid="<?php echo $medicine_row; ?>" class="form-control select2 medicine_name w-100"  name="medicine_<?php echo $medicine_row; ?>">
                                        <option value=""><?php echo $this->lang->line('select'); ?></option>
                                        <?php foreach ($medicineName as $mkey => $mvalue) { ?>
                                            <option value="<?php echo $mvalue["id"]; ?>" <?php echo ($medicine_value->pharmacy_id == $mvalue["id"]) ? 'selected' : ''; ?>><?php echo $mvalue["medicine_name"]; ?></option>
                                        <?php } ?>
                                    </select>
                                    <small id="stock_info_<?php echo $medicine_row; ?>" class="text-muted"></small>
                                </td>
                                <td>
                                    <select class="form-control select2 medicine_dosage w-100"  name="dosage_<?php echo $medicine_row; ?>">
                                        <option value=""><?php echo $this->lang->line('select'); ?></option>
                                        <?php foreach ($dosage as $dkey => $dvalue) { ?>
                                            <option value="<?php echo $dvalue["id"]; ?>" <?php echo ($medicine_value->dosage_id == $dvalue["id"]) ? 'selected' : ''; ?>><?php echo $dvalue["dosage"] . ' (' . $dvalue["unit"] . ')'; ?></option>
                                        <?php } ?>
                                    </select>
                                </td>
                                <td>
                                    <select class="form-control select2 w-100"  name="interval_dosage_<?php echo $medicine_row; ?>">
                                        <option value=""><?php echo $this->lang->line('select'); ?></option>
                                        <?php foreach ($intervaldosage as $dkey => $dvalue) { ?>
                                            <option value="<?php echo $dvalue["id"]; ?>" <?php if ($medicine_value->dose_interval_id == $dvalue["id"]) echo "selected"; ?>><?php echo $dvalue["name"]; ?></option>
                                        <?php } ?>
                                    </select>
                                </td>
                                <td>
                                    <select class="form-control select2 w-100"  name="duration_dosage_<?php echo $medicine_row; ?>">
                                        <option value=""><?php echo $this->lang->line('select'); ?></option>
                                        <?php foreach ($durationdosage as $dkey => $dvalue) { ?>
                                            <option value="<?php echo $dvalue["id"]; ?>" <?php if ($medicine_value->dose_duration_id == $dvalue["id"]) echo "selected"; ?>><?php echo $dvalue["name"]; ?></option>
                                        <?php } ?>
                                    </select>
                                </td>
                                <td>
                                    <textarea name="instruction_<?php echo $medicine_row; ?>" rows="1" class="form-control"><?php echo html_escape($medicine_value->instruction); ?></textarea>
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-outline-danger closebtn delete_row_prescription" data-row-id="<?php echo $medicine_row; ?>" data-record-id="<?php echo $medicine_value->ipd_prescription_detail_id; ?>">
                                        <i class="fa fa-remove"></i>
                                    </button>
                                </td>
                            </tr>
                            <?php $medicine_row++; } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Footer Note -->
        <div class="sh-form-card mb-3">
            <div class="sh-card-header">
                <span class="sh-card-header-title"><i class="fa fa-sticky-note-o me-1"></i> <?php echo $this->lang->line('footer_note'); ?></span>
            </div>
            <div class="p-2">
                <textarea name="footer_note" rows="1" class="form-control opd-note-area" id="compose-textareas"><?php echo html_escape($result->footer_note); ?></textarea>
            </div>
        </div>

    </div><!-- /.col-lg-9 -->

    <!-- RIGHT: Metadata sidebar -->
    <div class="col-lg-3 opd-rx-right pt-2">

        <!-- Prescribe By -->
        <div class="sh-form-card mb-3">
            <div class="sh-card-header">
                <span class="sh-card-header-title"><i class="fa fa-user-md me-1"></i> <?php echo $this->lang->line('prescribe_by'); ?></span>
            </div>
            <div class="p-2">
                <select class="form-control select2 w-100"  name='prescribe_by'>
                    <option value=""><?php echo $this->lang->line('select'); ?></option>
                    <?php foreach ($priscribe_list as $key => $value) { ?>
                        <option value="<?php echo $value["id"]; ?>" <?php if ($value["id"] == $result->prescribe_by) echo "selected"; ?>><?php echo $value['name']; ?></option>
                    <?php } ?>
                </select>
            </div>
        </div>

        <!-- Attachment & Custom Fields -->
        <div class="sh-form-card mb-3">
            <div class="sh-card-header">
                <span class="sh-card-header-title"><i class="fa fa-paperclip me-1"></i> <?php echo $this->lang->line('attachment'); ?></span>
            </div>
            <div class="p-2">
                <input type="file" class="filestyle form-control" name="document" autocomplete="off">
                <div class="mt-2">
                    <?php echo display_custom_fields('prescription', $prescription_id); ?>
                </div>
            </div>
        </div>

        <!-- Pathology -->
        <div class="sh-form-card mb-3">
            <div class="sh-card-header">
                <span class="sh-card-header-title"><i class="fa fa-flask me-1"></i> <?php echo $this->lang->line('pathology'); ?></span>
            </div>
            <div class="p-2">
                <select class="form-control multiselect2 w-100"  name='pathology[]' multiple id="pathologyOpt">
                    <?php foreach ($pathology as $key => $value) { ?>
                        <option value="<?php echo $value["id"]; ?>" <?php echo _check_test_ipd('pathology', $value["id"], $result) ? "selected" : ""; ?>><?php echo "(" . $value["short_name"] . ") " . $value["test_name"]; ?></option>
                    <?php } ?>
                </select>
            </div>
        </div>

        <!-- Radiology -->
        <div class="sh-form-card mb-3">
            <div class="sh-card-header">
                <span class="sh-card-header-title"><i class="fa fa-eye me-1"></i> <?php echo $this->lang->line('radiology'); ?></span>
            </div>
            <div class="p-2">
                <select class="form-control multiselect2 w-100"  name='radiology[]' id="radiologyOpt" multiple>
                    <?php foreach ($radiology as $key => $value) { ?>
                        <option value="<?php echo $value["id"]; ?>" <?php echo _check_test_ipd('radiology', $value["id"], $result) ? "selected" : ""; ?>><?php echo "(" . $value["short_name"] . ") " . $value["test_name"]; ?></option>
                    <?php } ?>
                </select>
            </div>
        </div>

        <!-- Notification -->
        <div class="sh-form-card mb-3">
            <div class="sh-card-header">
                <span class="sh-card-header-title"><i class="fa fa-bell me-1"></i> <?php echo $this->lang->line('notification_to'); ?></span>
            </div>
            <div class="p-2">
                <?php
                foreach ($roles as $role_key => $role_value) {
                    $userdata = $this->customlib->getUserData();
                    $role_id  = $userdata["role_id"];
                    ?>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="visible[]" value="<?php echo $role_value['id']; ?>" id="visible_edit_<?php echo $role_value['id']; ?>" <?php if ($role_value["id"] == $role_id) echo "checked onclick='return false;'"; ?> <?php echo set_checkbox('visible[]', $role_value['id'], false); ?>>
                        <label class="form-check-label" for="visible_edit_<?php echo $role_value['id']; ?>"><?php echo $role_value['name']; ?></label>
                    </div>
                    <?php
                }
                ?>
            </div>
        </div>

    </div><!-- /.col-lg-3 -->

</div><!-- /.row -->
