<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
?>
<form id="add_refund" accept-charset="utf-8" action="<?php echo base_url()?>admin/bill/add_refund" method="post">
    <input type="hidden" name="opd_id" value="<?php echo $opd_id;?>" class="form-control" >
    <input type="hidden" name="id" value="<?php echo $id;?>" class="form-control" >
    <input type="hidden" name="ipd_id" value="<?php echo $ipd_id;?>"  class="form-control" >
    <input type="hidden" name="case_reference_id" value="<?php echo $case_id; ?>" class="form-control">
    <div class="sh-form-card m-2">
        <div class="sh-card-header">
            <span class="sh-card-header-title"><i class="fa fa-undo me-1"></i> <?php echo $this->lang->line('refund'); ?></span>
        </div>
        <div class="p-3">
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3" id="dp">
                        <label><?php echo $this->lang->line('date'); ?></label><small class="req"> *</small>
                        <input type="text" name="payment_date" id="daterefund" value="<?php if(!empty($refund['payment_date'])){  echo $this->customlib->YYYYMMDDTodateFormat($refund['payment_date']); } ?>" class="form-control date" autocomplete="off">
                        <span class="text-danger"></span>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label><?php echo $this->lang->line('amount') . " (" . $currency_symbol . ")"; ?></label><small class="req"> *</small>
                        <input type="text" name="amount" value="<?php if(!empty($refund['amount'])){ echo $refund['amount']; } ?>" id="amount" class="form-control">
                        <span class="text-danger"><?php echo form_error('amount'); ?></span>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label><?php echo $this->lang->line('payment_mode') ; ?></label>
                        <select class="form-control payment_mode" name="payment_mode">
                            <?php foreach ($payment_mode as $key => $value) { ?>
                            <option value="<?php echo $key ?>" <?php if ($key == 'cash') { echo "selected"; } ?>><?php echo $value ?></option>
                            <?php } ?>
                        </select>
                        <span class="text-danger"><?php echo form_error('apply_charge'); ?></span>
                    </div>
                </div>
                <div class="col-md-6 cheque_div d-none" >
                    <div class="mb-3">
                        <label><?php echo $this->lang->line('cheque_no'); ?></label><small class="req"> *</small>
                        <input type="text" name="cheque_no" id="cheque_no" class="form-control">
                        <span class="text-danger"><?php echo form_error('cheque_no'); ?></span>
                    </div>
                </div>
                <div class="col-md-6 cheque_div d-none" >
                    <div class="mb-3">
                        <label><?php echo $this->lang->line('cheque_date'); ?></label><small class="req"> *</small>
                        <input type="text" value="<?php if(!empty($refund['cheque_date'])){ echo $this->customlib->YYYYMMDDTodateFormat($refund['cheque_date']); }?>" name="cheque_date" id="cheque_date" class="form-control date">
                        <span class="text-danger"><?php echo form_error('cheque_date'); ?></span>
                    </div>
                </div>
                <div class="col-sm-12 cheque_div d-none" >
                    <div class="mb-3">
                        <label><?php echo $this->lang->line('attach_document'); ?></label>
                        <input type="file" class="filestyle form-control" name="document">
                        <span class="text-danger"><?php echo form_error('document'); ?></span>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="mb-3">
                        <label><?php echo $this->lang->line('note'); ?></label>
                        <input type="text" name="note" value="<?php if(!empty($refund['note'])){ echo $refund['note']; } ?>" id="note" class="form-control"/>
                    </div>
                </div>
            </div>
            <div class="text-end mt-2">
                <?php if(!empty($refund)){ ?>
                <button type="button" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i>" id="print_trans" class="btn btn-info print_trans" value="" data-record-id="<?php echo $refund['id']; ?>"><i class="fa fa-print"></i> <?php echo $this->lang->line('print'); ?></button>
                <?php } ?>
                <button id="add_paymentbtn" type="submit" data-loading-text="<?php echo $this->lang->line('processing'); ?>" value="" class="btn btn-info"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
            </div>
        </div>
    </div>
</form>
