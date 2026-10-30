<input type="hidden" name="visit_details_id" value="<?php echo $visit_details_id;?>">
<input type="hidden" name="action" value="add">
<input type="hidden" name="ipd_prescription_basic_id" value="0">
<div class="row">

    <!-- LEFT: Prescription details -->
    <div class="col-lg-9 border-end opd-rx-left">

        <!-- Header Note -->
        <div class="sh-form-card mb-3">
            <div class="sh-card-header">
                <span class="sh-card-header-title"><i class="fa fa-sticky-note me-1"></i> <?php echo $this->lang->line('header_note'); ?></span>
            </div>
            <div class="p-2">
                <textarea name="header_note" class="form-control opd-note-area" id="compose-textareaneww"></textarea>
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
                        <select class="form-control multiselect2 findingtype w-100"  name='finding_type[]' id="finding_type" multiple>
                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                            <?php foreach ($findingtype as $fvalue) { ?>
                                <option value="<?php echo $fvalue["id"]; ?>"><?php echo $fvalue["category"] ?></option>
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
                        <textarea name="finding_description" id="finding_description" rows="3" class="form-control"></textarea>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label mb-1 d-block"><?php echo $this->lang->line('finding_print'); ?></label>
                        <div class="form-check mt-2">
                            <input class="form-check-input" type="checkbox" id="finding_print_chk_add" name="finding_print" value="yes" checked>
                            <label class="form-check-label" for="finding_print_chk_add"><?php echo $this->lang->line('yes'); ?></label>
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
                                <th style="min-width:200px"><?php echo $this->lang->line('medicine'); ?></th>
                                <th style="min-width:110px"><?php echo $this->lang->line("dose"); ?></th>
                                <th class="sh-mw-min-120"><?php echo $this->lang->line("dose_interval"); ?></th>
                                <th class="sh-mw-min-120"><?php echo $this->lang->line("dose_duration"); ?></th>
                                <th class="sh-mw-min-160"><?php echo $this->lang->line('instruction'); ?></th>
                                <th class="text-center" style="width:50px"><?php echo $this->lang->line('action'); ?></th>
                            </tr>
                        </thead>
                        <tbody id="tableID">
                            <tr id="row1">
                                <td>
                                    <input type="hidden" name="rows[]" value="1">
                                    <input type="hidden" name="medicine_cat_1" value="1">
                                    <select class="form-control select2 medicine_name w-100" data-rowid="1"  name="medicine_1">
                                        <option value=""><?php echo $this->lang->line('select');?></option>
                                        <?php foreach ($medicineName as $mkey => $mvalue) { ?>
                                            <option value="<?php echo $mvalue["id"]; ?>"><?php echo $mvalue["medicine_name"]; ?></option>
                                        <?php } ?>
                                    </select>
                                    <small id="stock_info_1"></small>
                                    <div id="suggesstion-box0"></div>
                                </td>
                                <td>
                                    <select class="form-control select2 medicine_dosage w-100"  name="dosage_1">
                                        <option value=""><?php echo $this->lang->line('select'); ?></option>
                                        <?php foreach ($dosage as $dkey => $dvalue) { ?>
                                            <option value="<?php echo $dvalue["id"]; ?>"><?php echo $dvalue["dosage"] . ' (' . $dvalue["unit"] . ')'; ?></option>
                                        <?php } ?>
                                    </select>
                                </td>
                                <td>
                                    <select class="form-control select2 interval_dosage w-100"  name='interval_dosage_1'>
                                        <option value="<?php echo set_value('interval_dosage_id'); ?>"><?php echo $this->lang->line('select') ?></option>
                                        <?php foreach ($intervaldosage as $dkey => $dvalue) { ?>
                                            <option value="<?php echo $dvalue["id"]; ?>"><?php echo $dvalue["name"] ?></option>
                                        <?php } ?>
                                    </select>
                                </td>
                                <td>
                                    <select class="form-control select2 w-100"  name='duration_dosage_1'>
                                        <option value="<?php echo set_value('interval_dosage'); ?>"><?php echo $this->lang->line('select') ?></option>
                                        <?php foreach ($durationdosage as $dkey => $dvalue) { ?>
                                            <option value="<?php echo $dvalue["id"]; ?>"><?php echo $dvalue["name"] ?></option>
                                        <?php } ?>
                                    </select>
                                </td>
                                <td>
                                    <textarea name="instruction_1" rows="1" class="form-control opd-instruction-area"></textarea>
                                </td>
                                <td class="text-center">
                                    <button type='button' data-row-id='1' class='btn btn-sm btn-outline-danger closebtn delete_row_prescription'><i class='fa fa-remove'></i></button>
                                </td>
                            </tr>
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
                <textarea name="footer_note" class="form-control opd-note-area" id="compose-textareass"></textarea>
            </div>
        </div>

    </div><!-- /.col-lg-9 -->

    <!-- RIGHT: Metadata sidebar -->
    <div class="col-lg-3 opd-rx-right pt-2">

        <!-- Attachment -->
        <div class="sh-form-card mb-3">
            <div class="sh-card-header">
                <span class="sh-card-header-title"><i class="fa fa-paperclip me-1"></i> <?php echo $this->lang->line('attachment'); ?></span>
            </div>
            <div class="p-2">
                <input type="file" class="filestyle form-control" name="document" autocomplete="off">
            </div>
        </div>

        <!-- Custom Fields -->
        <div class="sh-form-card mb-3">
            <div class="sh-card-header">
                <span class="sh-card-header-title"><i class="fa fa-list me-1"></i> <?php echo $this->lang->line('custom_fields'); ?></span>
            </div>
            <div class="p-2">
                <?php echo display_custom_fields('prescription'); ?>
            </div>
        </div>

        <div class="sh-form-card mb-3">
            <div class="sh-card-header">
                <span class="sh-card-header-title"><i class="fa fa-flask me-1"></i> <?php echo $this->lang->line('pathology'); ?></span>
            </div>
            <div class="p-2">
                <select class="form-control multiselect2 w-100"  name='pathology[]' multiple id="pathologyOpt">
                    <?php foreach ($pathology as $key => $value) { ?>
                        <option value="<?php echo $value["id"]; ?>"><?php echo " (" . $value["short_name"] . ") " . $value["test_name"]; ?></option>
                    <?php } ?>
                </select>
            </div>
        </div>

        <div class="sh-form-card mb-3">
            <div class="sh-card-header">
                <span class="sh-card-header-title"><i class="fa fa-eye me-1"></i> <?php echo $this->lang->line('radiology'); ?></span>
            </div>
            <div class="p-2">
                <select class="form-control multiselect2 w-100"  name='radiology[]' id="radiologyOpt" multiple>
                    <?php foreach ($radiology as $key => $value) { ?>
                        <option value="<?php echo $value["id"]; ?>"><?php echo " (" . $value["short_name"] . ") " . $value["test_name"]; ?></option>
                    <?php } ?>
                </select>
            </div>
        </div>

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
                        <input class="form-check-input" type="checkbox" name="visible[]" value="<?php echo $role_value['id']; ?>" id="visible_<?php echo $role_value['id']; ?>" <?php if ($role_value["id"] == $role_id) { echo "checked onclick='return false;'"; } ?> <?php echo set_checkbox('visible[]', $role_value['id'], false) ?>>
                        <label class="form-check-label" for="visible_<?php echo $role_value['id']; ?>"><b><?php echo $role_value['name']; ?></b></label>
                    </div>
                    <?php
                }
                ?>
            </div>
        </div>

    </div><!-- /.col-lg-3 -->

</div><!-- /.row -->
