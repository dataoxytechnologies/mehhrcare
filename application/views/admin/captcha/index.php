<div class="row">
    <?php $this->load->view('setting/sidebar'); ?>
    <div class="col-md-10">
        <div class="card">
            <div class="card-header ptbnull">
                <h5 class="card-title mb-0"><?php echo $this->lang->line('captcha_settings'); ?></h5>
            </div>
            <div class="card-body">
                <div class="download_label"><?php echo $this->lang->line('captcha_settings'); ?></div>
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle example mb-0" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th><?php echo $this->lang->line('name'); ?></th>
                                <?php if ($this->rbac->hasPrivilege('captcha_setting', 'can_edit')) { ?>
                                    <th class="text-end noExport sh-th-140"><?php echo $this->lang->line('action'); ?></th>
                                <?php } ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if (!empty($fields)) {
                                foreach ($fields as $fields_key => $fields_value) {
                                    ?>
                                    <tr>
                                        <td><?php echo $fields_value; ?></td>
                                        <?php if ($this->rbac->hasPrivilege('captcha_setting', 'can_edit')) { ?>
                                            <td class="text-end noExport">
                                                <div class="form-check form-switch d-inline-flex m-0">
                                                    <input class="form-check-input chk" type="checkbox" role="switch"
                                                           id="field_<?php echo $fields_key ?>"
                                                           name="<?php echo $fields_key; ?>"
                                                           data-role="field_<?php echo $fields_key ?>"
                                                           <?php echo set_checkbox($fields_key, $fields_key, findSelected($inserted_fields, $fields_key)); ?> />
                                                </div>
                                            </td>
                                        <?php } ?>
                                    </tr>
                                    <?php
                                }
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php 

function findSelected($inserted_fields,$find){
    foreach ($inserted_fields as $inserted_key => $inserted_value) {
       if($find == $inserted_value->name && $inserted_value->status==1){
        return true;
       }
    }
    return false;
}

?>

<script type="text/javascript">
    $(document).ready(function () {

        $(document).on('click', '.chk', function(event) {
            var name=$(this).attr('name');
            var status=1;
            if(this.checked) {
             status=1;
            } else {
             status=0;
            }
             if(confirm("<?php echo $this->lang->line('confirm_status'); ?>")){              
                changeStatus(name, status);
            }
            else{
                event.preventDefault();
            }
        });
    });

    function changeStatus(name, status) {
        var base_url = '<?php echo base_url() ?>';
        $.ajax({
            type: "POST",
            url: base_url + "admin/captcha/changeStatus",
            data: {'name': name, 'status': status},
            dataType: "json",
            success: function (data) {
                successMsg(data.msg);
            }
        });
    }
</script>
