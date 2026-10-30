<form id="editbedtype" accept-charset="utf-8" enctype="multipart/form-data">
    <div class="sh-form-card">
        <div class="sh-card-header">
            <span class="sh-card-header-title"><?php echo $this->lang->line('details'); ?></span>
        </div>
        <div class="p-3">
            <div class="row">
                <div class="mb-3 col-12">
                    <label class="form-label"><?php echo $this->lang->line('name'); ?></label><small class="req"> *</small>
                    <input name="name" placeholder="" type="text" class="form-control" value="<?php echo set_value('name', $bedtype_data['name']); ?>" />
                    <input id="bedtype_id" name="bedtype_id" type="hidden" value="<?php echo $bedtype_data['id']; ?>" />
                </div>
            </div>
        </div>
    </div>
</form>

<script type="text/javascript">
    $("#editbedtype").on('submit', function (e) {
        e.preventDefault();
        var id = $("#bedtype_id").val();
        $.ajax({
            url: '<?php echo base_url(); ?>admin/setup/bedtype/edit/' + id,
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
                $("#editbedtypebtn").btnReset();
            },
            error: function () {
                alert("Fail");
            }
        });
    });
</script>
