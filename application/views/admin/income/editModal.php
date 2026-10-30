<?php $currency_symbol = $this->customlib->getHospitalCurrencyFormat();?>
<form id="edit_incomedata" accept-charset="utf-8" enctype="multipart/form-data">
    <?php if ($this->session->flashdata('msg')) { echo $this->session->flashdata('msg'); } ?>
    <?php if (isset($error_message)) { echo "<div class='alert alert-danger'>" . $error_message . "</div>"; } ?>
    <?php echo $this->customlib->getCSRF(); ?>
    <div class="sh-form-card mb-3">
        <div class="sh-card-header">
            <span class="sh-card-header-title"><?php echo $this->lang->line('details'); ?></span>
        </div>
        <div class="p-2">
            <div class="row g-3">
                <div class="col-sm-6">
                    <label class="form-label form-label-sm"><?php echo $this->lang->line('income_head'); ?> <small class="req">*</small></label>
                    <select autofocus id="inc_head_id" name="inc_head_id" class="form-select form-select-sm">
                        <option value=""><?php echo $this->lang->line('select'); ?></option>
                        <?php foreach ($incheadlist as $inchead) { ?>
                        <option value="<?php echo $inchead['id'] ?>"<?php if ($income['inc_head_id'] == $inchead['id']) echo ' selected'; ?>><?php echo html_escape($inchead['income_category']) ?></option>
                        <?php } ?>
                    </select>
                    <span class="text-danger"><?php echo form_error('inc_head_id'); ?></span>
                </div>
                <div class="col-sm-6">
                    <label class="form-label form-label-sm"><?php echo $this->lang->line('name'); ?> <small class="req">*</small></label>
                    <input id="name" name="name" type="text" class="form-control form-control-sm" value="<?php echo html_escape(set_value('name', $income['name'])); ?>">
                    <input id="income_id" type="hidden" value="<?php echo (int)$income['id']; ?>">
                    <span class="text-danger"><?php echo form_error('name'); ?></span>
                </div>
                <div class="col-sm-6">
                    <label class="form-label form-label-sm"><?php echo $this->lang->line('invoice_number'); ?></label>
                    <input id="invoice_no" name="invoice_no" type="text" class="form-control form-control-sm" value="<?php echo html_escape(set_value('invoice_no', $income['invoice_no'])); ?>">
                    <span class="text-danger"><?php echo form_error('invoice_no'); ?></span>
                </div>
                <div class="col-sm-6">
                    <label class="form-label form-label-sm"><?php echo $this->lang->line('date'); ?> <small class="req">*</small></label>
                    <input id="editdate" name="date" type="text" class="form-control form-control-sm date" value="<?php echo html_escape(set_value('date', $this->customlib->YYYYMMDDTodateFormat($income['date']))); ?>" readonly autocomplete="off">
                    <span class="text-danger"><?php echo form_error('date'); ?></span>
                </div>
                <div class="col-sm-6">
                    <label class="form-label form-label-sm"><?php echo $this->lang->line('amount') . " (" . $currency_symbol . ")"; ?> <small class="req">*</small></label>
                    <input id="amount" name="amount" type="text" class="form-control form-control-sm" value="<?php echo html_escape(set_value('amount', $income['amount'])); ?>">
                    <span class="text-danger"><?php echo form_error('amount'); ?></span>
                </div>
                <div class="col-sm-6">
                    <label class="form-label form-label-sm"><?php echo $this->lang->line('attach_document'); ?></label>
                    <input id="documentsf" name="documents" type="file" class="filestyle form-control form-control-sm" data-height="26" value="<?php echo set_value('documents'); ?>">
                    <span class="text-danger"><?php echo form_error('documents'); ?></span>
                </div>
                <div class="col-sm-12">
                    <label class="form-label form-label-sm"><?php echo $this->lang->line('description'); ?></label>
                    <textarea class="form-control form-control-sm" id="description" name="description" rows="3"><?php echo html_escape(set_value('description', $income['note'])); ?></textarea>
                    <span class="text-danger"><?php echo form_error('description'); ?></span>
                </div>
                <?php echo display_custom_fields('income', $income['id']); ?>
            </div>
        </div>
    </div>
    <div class="modal-footer px-0 pb-0 border-top-0">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
        <button type="submit" data-loading-text="<?php echo $this->lang->line('processing'); ?>" id="edit_incomedatabtn" class="btn btn-info"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
    </div>
</form>

<script type="text/javascript">
    $(document).ready(function () {
        $('.filestyle').dropify();
    });
</script>

<script type="text/javascript"> $(document).ready(function (e) {
        $("#edit_incomedata").on('submit', (function (e) {
            $("#edit_incomedatabtn").btnLoading();
            var id = $("#income_id").val();
            e.preventDefault();
            $.ajax({
                url: '<?php echo base_url(); ?>admin/income/edit/' + id,
                type: "POST",
                data: new FormData(this),
                dataType: 'json',
                contentType: false,
                cache: false,
                processData: false,
                success: function (data) {
                    if (data.status == "fail") {
                        var message = "";
                        $.each(data.error, function (index, value) {
                            message += value;
                        });
                        errorMsg(message);
                    } else {
                        successMsg(data.message);
                        window.location.reload(true);
                    }
                    $("#edit_incomedatabtn").btnReset();
                },
                error: function () {
                    alert("Fail")
                }
            });
        }));
    });
    $(document).ready(function () {
        // #editdate auto-initialized as Tempus Dominus 6 picker via .date class + event delegation in footer.php
        // Apply additional restriction: max date = today (preserves old endDate:'+0d' behavior)
        var dateEl = document.getElementById('editdate');
        if (dateEl) {
            // Trigger init via shim/delegation, then apply restriction
            var triggerInit = function() {
                if (dateEl._pickerInit) {
                    dateEl._pickerInit.updateOptions({
                        restrictions: { maxDate: new tempusDominus.DateTime() }
                    });
                }
            };
            dateEl.addEventListener('focus', triggerInit, { once: true });
        }
    });
</script>