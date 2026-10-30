<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
?>
<div class="row">
    <!-- ── Top row: TPA details card ────────────────────────────────────── -->
    <div class="col-12">
        <div class="sh-form-card">
            <div class="sh-card-header">
                <span class="sh-card-header-title"><i class="fa fa-hospital-o"></i><?php echo $this->lang->line('tpa_name'); ?></span>
            </div>
            <div class="sh-info-grid tpa-info">
                <div class="row g-0">
                    <div class="col-6 col-md-4 col-lg-2 sh-info-item">
                        <span class="sh-info-label"><i class="fa fa-building-o"></i><?php echo $this->lang->line('tpa_name'); ?></span>
                        <span class="sh-info-value highlight"><?php echo html_escape($result['organisation_name']); ?></span>
                    </div>
                    <div class="col-6 col-md-4 col-lg-2 sh-info-item">
                        <span class="sh-info-label"><i class="fa fa-hashtag"></i><?php echo $this->lang->line('code'); ?></span>
                        <span class="sh-info-value"><?php echo html_escape($result['code']) ?: '—'; ?></span>
                    </div>
                    <div class="col-6 col-md-4 col-lg-2 sh-info-item">
                        <span class="sh-info-label"><i class="fa fa-phone"></i><?php echo $this->lang->line('contact_no'); ?></span>
                        <span class="sh-info-value"><?php echo html_escape($result['contact_no']) ?: '—'; ?></span>
                    </div>
                    <div class="col-6 col-md-4 col-lg-2 sh-info-item">
                        <span class="sh-info-label"><i class="fa fa-user"></i><?php echo $this->lang->line('contact_person_name'); ?></span>
                        <span class="sh-info-value"><?php echo html_escape($result['contact_person_name']) ?: '—'; ?></span>
                    </div>
                    <div class="col-6 col-md-4 col-lg-2 sh-info-item">
                        <span class="sh-info-label"><i class="fa fa-mobile"></i><?php echo $this->lang->line('contact_person_phone'); ?></span>
                        <span class="sh-info-value"><?php echo html_escape($result['contact_person_phone']) ?: '—'; ?></span>
                    </div>
                    <div class="col-6 col-md-4 col-lg-2 sh-info-item">
                        <span class="sh-info-label"><i class="fa fa-map-marker"></i><?php echo $this->lang->line('address'); ?></span>
                        <span class="sh-info-value"><?php echo html_escape($result['address']) ?: '—'; ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- ── Below row: filter + charges table ────────────────────────────── -->
    <div class="col-12">
        <div class="sh-form-card">
            <div class="sh-card-header">
                <span class="sh-card-header-title"><i class="fa fa-list"></i><?php echo $this->lang->line('tpa_details'); ?></span>
            </div>
            <div class="card-body">
                <form id="form1" action="" method="post">
                    <?php echo $this->customlib->getCSRF(); ?>
                    <div class="row g-3 align-items-end">
                        <div class="col-12 col-md-6 col-lg-3">
                            <label class="form-label"><?php echo $this->lang->line('charge_type'); ?></label>
                            <select class="form-control select2" name="charge_type">
                                <option value=""><?php echo $this->lang->line('select') ?></option>
                                <?php foreach ($charge_type as $ckey => $value) { ?>
                                    <option value="<?php echo $value['id']; ?>"><?php echo $value['charge_type'] ?></option>
                                <?php } ?>
                            </select>
                            <input type="hidden" value="<?php echo $result['id']; ?>" name="charge_type_master_id" />
                            <span class="text-danger" id="error_search_type"><?php echo form_error('search_type'); ?></span>
                        </div>
                        <div class="col-12 col-md-auto ps-md-3">
                            <button type="submit" name="search" value="search_filter" class="btn btn-primary checkbox-toggle"><i class="fa fa-search"></i> <?php echo $this->lang->line('search'); ?></button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="card-body table-responsive border-top pt-3">
                <div class="download_label"><?php echo $this->lang->line('tpa_report'); ?></div>
                <table class="table table-striped table-bordered table-hover allajaxlist" data-export-title="<?php echo $this->lang->line('tpa_details'); ?>" cellspacing="0" width="100%">
                    <thead>
                        <th><?php echo $this->lang->line('charge_type'); ?></th>
                        <th><?php echo $this->lang->line('charge_category'); ?></th>
                        <th><?php echo $this->lang->line('charge_name'); ?></th>
                        <th><?php echo $this->lang->line('description'); ?></th>
                        <th class="text-end"><?php echo $this->lang->line('standard_charge') . " (" . $currency_symbol . ")"; ?></th>
                        <th class="text-end"><?php echo $this->lang->line('tpa_charge') . " (" . $currency_symbol . ")"; ?></th>
                        <th class="text-end noExport"><?php echo $this->lang->line('action'); ?></th>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<div class="modal fade sh-modal sh-modal-accent edit_org" tabindex="-1" aria-labelledby="editTpaChargeLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editTpaChargeLabel"><?php echo $this->lang->line('edit_tpa_charge'); ?></h5>
                <div class="d-flex align-items-center gap-2 ms-auto">
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            </div>
            <form id="edit_org_form" method="POST" action="<?php echo base_url(); ?>admin/tpa/edit_org">
                <div class="pup-scroll-area">
                    <div class="modal-body">
                        <div class="sh-form-card">
                            <div class="sh-card-header">
                                <span class="sh-card-header-title"><i class="fa fa-list me-1"></i><?php echo $this->lang->line('charge_details'); ?></span>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-striped align-middle mb-0">
                                    <thead>
                                        <tr>
                                            <th><?php echo $this->lang->line('charge_type'); ?></th>
                                            <th><?php echo $this->lang->line('charge_category'); ?></th>
                                            <th><?php echo $this->lang->line('charge_name'); ?></th>
                                            <th><?php echo $this->lang->line('description'); ?></th>
                                            <th class="text-end"><?php echo $this->lang->line('standard_charge') . " (" . $currency_symbol . ")"; ?></th>
                                            <th class="text-end white-space-nowrap" width="10"><?php echo $this->lang->line('tpa_charge') . " (" . $currency_symbol . ")"; ?> <small class="req">*</small></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td id="charge_type"></td>
                                            <td id="charge_category"></td>
                                            <td id="charge_name"></td>
                                            <td id="description"></td>
                                            <td class="text-end" id="standard_charge_data"></td>
                                            <td class="text-end" width="100">
                                                <input type="text" name="org_charge" id="org_charge_data" class="form-control form-control-sm text-end">
                                                <input type="hidden" name="org_charge_id" id="org_charge_id_data">
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                    <button type="submit" id="edit_org_btn" data-loading-text="<?php echo $this->lang->line('processing') ?>" class="btn btn-info"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<script type="text/javascript">
var table;
$(function () {
    $('.select2').select2()
});

    function delete_orgById(id){
		if(confirm('<?php echo $this->lang->line('are_you_sure'); ?>')){
        var delete_url='admin/tpa/delete/' + id;      
        $.ajax({
            url: base_url +delete_url,
            type: 'POST',
            dataType: "json",
            success: function (msg) {
                successMsg(msg.msg);
                table.ajax.reload();
            }
        });
		}
    }
    
    function get_org_charge(id) {
        $.ajax({
            url: '<?php echo base_url(); ?>admin/tpa/get_org_charge/' + id,
            dataType: 'json',
            success: function (res) {
                $('.edit_org').attr("id", 'edit_org_data');
                bootstrap.Modal.getOrCreateInstance(document.getElementById('edit_org_data'),{backdrop:'static',keyboard:false}).show();
                $('#org_charge_id_data').val(res.org_charge_id);
                $('#description').html('<label>' + res.description + '</label>');
                $('#charge_type').html('<label>' + res.charge_type + '</label>');
                $('#charge_name').html('<label>' + res.name + '</label>');
                $('#charge_category').html('<label>' + res.charge_category + '</label>');
                $('#standard_charge_data').html('<label>' + res.standard_charge + '</label>');
                $('#org_charge_data').val(res.org_charge);
            }
        });
    }

    $(document).ready(function (e) {
        $('#edit_org_form').on('submit', (function (e) {
            $("#edit_org_btn").btnLoading();
            e.preventDefault();
            $.ajax({
                url: $(this).attr('action'),
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
                        table.ajax.reload();
                         shModal('edit_org_data').hide();
                    }
                    $("#edit_org_btn").btnReset();
                },
                error: function () {}
            });
        }));
    });

  ( function ( $ ) {
    'use strict';
    $(document).ready(function () {
       $('#form1').on('submit', (function (e) {
        e.preventDefault();
        var search= 'search_filter';
        var formData = new FormData(this);
        $.ajax({
            url: '<?php echo base_url(); ?>admin/tpa/checkvalidation',
            type: "POST",
            data: formData,
            dataType: 'json',
            contentType: false,
            cache: false,
            processData: false,
            success: function (data) {
                if (data.status == "fail") {
                   $.each(data.error, function(key, value) {
                        $('#error_' + key).html(value);
                    });
                } else {

                table = initDatatable('allajaxlist', 'admin/tpa/tpadetails/',data.param,[],100,[
                    { "bSortable": false, "aTargets": [ -1 ], "sClass": "dt-body-right" },
                    { "sWidth": "120px", "aTargets": [ -2, -3 ], "sClass": "dt-body-right" }
                ]);
                }
            }
        });
        }
       ));
   });
} ( jQuery ) );  
</script>