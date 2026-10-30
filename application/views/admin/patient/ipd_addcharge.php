<div class="modal fade sh-modal sh-modal-accent" id="edit_chargeModal" tabindex="-1" aria-labelledby="edit_chargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="edit_chargeModalLabel"><?php echo $this->lang->line('edit_charge'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
             <form id="edit_charges" accept-charset="utf-8"  method="post">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12">
                            <input type="hidden" name="opd_id" value="<?php echo $result['id'] ?>" >
                            <input type="hidden" name="patient_charge_id" id="patient_charge_id" value="0">
                            <input type="hidden" name="organisation_id" id="organisation_id" value="<?php echo $visitdata['organisation_id'] ?>">
                                <div class="row">
                                    <div class="col-sm-2">
                                        <div class="mb-3">
                                            <label><?php echo $this->lang->line('charge_type'); ?></label><small class="req"> *</small> 
                                            <select name="charge_type" class="form-control charge_type select2">
                                                <option value=""><?php echo $this->lang->line('select'); ?></option>
                                                <?php foreach ($charge_type as $key => $value) {
                                                    ?>
                                                    <option value="<?php echo $value->id; ?>">
                                                    <?php echo $value->charge_type; ?>
                                                    </option>
                                                <?php } ?>
                                            </select>
                                            <span class="text-danger"><?php echo form_error('charge_type'); ?></span>
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="mb-3">
                                            <label><?php echo $this->lang->line('charge_category'); ?></label><small class="req"> *</small> 
                                            <select name="charge_category" id="charge_category" class="w-100 form-control select2 charge_category">
                                                <option value=""><?php echo $this->lang->line('select'); ?></option>
                                            </select>
                                            <span class="text-danger"><?php echo form_error('charge_category'); ?></span>
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="mb-3">
                                            <label><?php echo $this->lang->line('charge_name'); ?></label><small class="req"> *</small>
                                                <select name="charge_id" id="charge_id" class="w-100 form-control addcharge select2">
                                                <option value=""><?php echo $this->lang->line('select'); ?></option>
                                            </select>
                                            <span class="text-danger"><?php echo form_error('code'); ?></span>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="mb-3">
                                            <label><?php echo $this->lang->line('standard_charge') . " (" . $currency_symbol . ")" ?></label>
                                            <input type="text" readonly name="standard_charge" id="addstandard_charge" class="form-control" value="<?php echo set_value('standard_charge'); ?>"> 
                                            <span class="text-danger"><?php echo form_error('standard_charge'); ?></span>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="mb-3">
                                            <label><?php echo $this->lang->line('tpa_charge') . " (" . $currency_symbol . ")" ?></label>
                                            <input type="text" readonly name="schedule_charge" id="addscd_charge" placeholder="" class="form-control" value="<?php echo set_value('schedule_charge'); ?>">    
                                            <span class="text-danger"><?php echo form_error('schedule_charge'); ?></span>
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="mb-3">
                                            <label><?php echo $this->lang->line('qty'); ?></label><small class="req"> *</small> 
                                        <input type="text" name="qty" id="qty" class="form-control" value="1"> 
                                            <span class="text-danger"><?php echo form_error('qty'); ?></span>
                                        </div>
                                    </div>
                                </div>                            
                                    <div class="divider"></div>
                                        <div class="row">
                                            <div class="col-sm-4">
                                        <div class="mb-3">
                                            <label><?php echo $this->lang->line('date'); ?></label> <small class="req"> *</small> 
                                            <input id="charge_date" name="date" placeholder="" type="text" class="form-control billDateDisabled" />
                                        </div>
                                    </div>
                                            <div class="col-sm-4">
                                                <div class="row">
                                                    <div class="col-sm-12">
                                                        <div class="mb-3">
                                                            <label><?php echo $this->lang->line('charge_note'); ?></label>
                                                            <textarea name="note" id="edit_note" rows="3" class="form-control"></textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div><!--./col-sm-6-->
                                            <div class="col-sm-4">
                                                <table class="printablea4">
                                                    <tr>
                                                        <th width="40%"><?php echo $this->lang->line('total') . " (" . $currency_symbol . ")"; ?></th>
                                                        <td width="60%" colspan="2" class="text-end ipdbilltable">
                                                        <input type="text" placeholder="<?= $this->lang->line('total') ?>" value="0" name="apply_charge" id="apply_charge" class="form-control total sh-print-right-30"  readonly /></td>
                                                    </tr>
                                                    <tr>
                                                        <th><?php echo $this->lang->line('tax') . " (" . $currency_symbol . ")"; ?></th>
                                                        <td class="text-end ipdbilltable">
                                                            <h4 class="sh-print-right-70"> %</h4>
                                                    <input type="text" placeholder="<?php echo $this->lang->line('tax'); ?>" name="charge_tax" id="charge_tax" class="form-control charge_tax" readonly class="sh-print-right-70"/></td>

                                                        <td class="text-end ipdbilltable">
                                                            <input type="text" placeholder="<?php echo $this->lang->line('tax'); ?>" name="tax" value="0" id="tax" class="form-control tax sh-print-right-50"  readonly/>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th><?php echo $this->lang->line('net_amount') . " (" . $currency_symbol . ")"; ?></th>
                                                        <td colspan="2" class="text-end ipdbilltable">
                                                            <input type="text" placeholder="<?= $this->lang->line('net_amount') ?>" value="0" name="amount" id="final_amount" class="form-control net_amount sh-print-right-30"  readonly/></td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </div><!--./row-->
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                    <button type="submit" data-loading-text="<?php echo $this->lang->line('processing') ?>" name="charge_data" class="btn btn-info"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('edit') ?></button>
                </div>
            </form>               
        </div>
    </div>  
</div>