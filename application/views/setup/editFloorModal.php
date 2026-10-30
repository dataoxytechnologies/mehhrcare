<form id="editfloor_data" accept-charset="utf-8" enctype="multipart/form-data">
    <div class="sh-form-card">
        <div class="sh-card-header">
            <span class="sh-card-header-title"><?php echo $this->lang->line('details'); ?></span>
        </div>
        <div class="p-3">
            <div class="row">
                <div class="mb-3 col-12">
                    <label class="form-label"><?php echo $this->lang->line('name'); ?></label><small class="req"> *</small>
                    <input name="name" placeholder="" type="text" class="form-control" value="<?php echo set_value('name', $floor_data['name']); ?>" />
                    <input id="floor_id" name="floor_id" type="hidden" value="<?php echo $floor_data['id']; ?>" />
                </div>
                <div class="mb-3 col-12">
                    <label class="form-label"><?php echo $this->lang->line('description'); ?></label>
                    <textarea class="form-control sh-no-resize" id="description" name="description" placeholder="" rows="2"><?php echo set_value('description', $floor_data['description']); ?></textarea>
                </div>
            </div>
        </div>
    </div>
</form>

<script type="text/javascript">
    $("#editfloor_data").on('submit', function (e) {
        e.preventDefault();
        var id = $("#floor_id").val();
        $.ajax({
            url: '<?php echo base_url(); ?>admin/setup/floor/edit/' + id,
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
                $("#editfloor_databtn").btnReset();
            },
            error: function () {
                alert("Fail");
            }
        });
    });
</script>
