<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
?>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title titlefix"><?php echo $this->lang->line('purchase_import') ?: 'Purchase Import'; ?></h3>
                <div class="d-flex gap-2 align-items-center flex-wrap float-end">
                    <?php if ($this->rbac->hasPrivilege('import_purchase', 'can_view')): ?>
                        <a href="<?php echo site_url('admin/purchaseImport/export_sample') ?>">
                            <button class="btn btn-primary btn-sm"><i class="fa fa-download"></i> <?php echo $this->lang->line('download_sample') ?: 'Download Sample CSV'; ?></button>
                        </a>
                    <?php endif; ?>
                </div>
            </div><!-- /.card-header -->
            <div class="card-body">
                <?php if ($this->session->flashdata('import_msg')): ?>
                    <div><?php echo $this->session->flashdata('import_msg'); ?></div>
                    <?php $this->session->unset_userdata('import_msg'); ?>
                <?php endif; ?>
                <br/>
                <p><b><?php echo $this->lang->line('note') ?>:</b> <?php echo $this->lang->line('purchase_import_note') ?: 'CSV must contain the columns listed in the sample file.'; ?> <?php echo $this->lang->line('purchase_import_note2') ?: ''; ?></p>
                <hr/>
                <form action="<?php echo site_url('admin/purchaseImport/do_import') ?>" id="formpurchaseimport" name="formpurchaseimport" method="post" enctype="multipart/form-data">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="file"><?php echo $this->lang->line('select_csv_file') ?: 'Select CSV File'; ?></label><small class="req"> *</small>
                                    <div><input class="filestyle form-control" type='file' name='file' id="file" size='20' accept=".csv" />
                                        <span class="text-danger"><?php echo form_error('file'); ?></span></div>
                                </div>
                            </div>
                            <div class="col-md-12 pt20">
                                <button type="submit" class="btn btn-info btn-sm float-end"><i class="fa fa-upload"></i> <?php echo $this->lang->line('import_purchase') ?: 'Import Purchase CSV'; ?></button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function (e) {
        $("#formpurchaseimport").on('submit', (function (e) {
            e.preventDefault();
            $("#formpurchaseimportbtn").btnLoading();
            $.ajax({
                url: '<?php echo base_url(); ?>admin/purchaseImport/do_import',
                type: "POST",
                data: new FormData(this),
                dataType: 'json',
                contentType: false,
                cache: false,
                processData: false,
                success: function (data) {
                    if (data.status === false) {
                        var message = "";
                        if (data.skipped && data.skipped.length) {
                            $.each(data.skipped, function (index, value) {
                                message += value + "\n";
                            });
                        } else if (data.message) {
                            message = data.message;
                        }
                        errorMsg(message);
                    } else {
                        var message = data.message || 'Purchase CSV imported successfully.';
                        if (data.bills) {
                            message += '\nBills imported: ' + data.bills;
                        }
                        if (data.lines) {
                            message += '\nMedicine lines imported: ' + data.lines;
                        }
                        successMsg(message);
                        window.location.reload(true);
                    }
                    $("#formpurchaseimportbtn").btnReset();
                },
                error: function () {
                    errorMsg('An error occurred while importing the CSV file.');
                    $("#formpurchaseimportbtn").btnReset();
                }
            });
        }));
    });
</script>
