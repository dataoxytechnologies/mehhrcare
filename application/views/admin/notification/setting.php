<div class="row">
            <?php $this->load->view('setting/sidebar'); ?>
            <div class="col-md-10">
                <!-- left column -->
                <form id="form1" action="<?php echo site_url('admin/notification/setting') ?>" id="employeeform" name="employeeform" method="post" accept-charset="utf-8">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title"><?php echo $this->lang->line('notification_setting'); ?></h3>
                        </div>
                        <div class="around10">
                            <?php if ($this->session->flashdata('msg')) { ?>
                                <?php echo $this->session->flashdata('msg');
                                $this->session->unset_userdata('msg');
                                 ?>
                            <?php } ?> 
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <th><?php echo $this->lang->line('event'); ?></th>
                                    <th><?php echo $this->lang->line('option'); ?></th>
                                    <th><?php echo $this->lang->line('template_id'); ?></th>
                                    <?php if ($this->module_lib->hasModule('whatsapp_messaging') && $this->module_lib->hasActive('whatsapp_messaging')){ ?>
                                    <th><?php echo $this->lang->line('whatsapp_template_id'); ?></th>
                                    <?php } ?>
                                    <th><?php echo $this->lang->line('sample_message'); ?></th>
                                </thead>
                                 <tbody>
                                    <?php
                                        $i        = 1;
                                        $last_key = count($notificationlist);
                                        foreach ($notificationlist as $note_key => $note_value) {
                                            $hr = "";

                                            if ($i != $last_key) {
                                                $hr = "<hr>";
                                            }
                                            ?>
                                        <tr>
                                            <td width="18%">
                                                <input type="hidden" name="ids[]" value="<?php echo $note_value->id; ?>">
                                                <?php echo $this->lang->line($note_value->type); ?>
                                            </td>
                                            <td width="28%">
                                                <label class="form-check form-check-inline">
                                                    <input type="checkbox" name="mail_<?php echo $note_value->id; ?>" value="1" <?php echo set_checkbox('mail_' . $note_value->id, 1, set_value('mail_' . $note_value->id, $note_value->is_mail) ? true : false); ?>> <?php echo $this->lang->line('email'); ?>
                                                </label>
                                               <?php
                                                    if ($note_value->display_sms) {
                                                            ?>  
                                                <label class="form-check form-check-inline">
                                                    <input type="checkbox" name="sms_<?php echo $note_value->id; ?>" value="1" <?php echo set_checkbox('sms_' . $note_value->id, 1, set_value('sms_' . $note_value->id, $note_value->is_sms) ? true : false); ?>>
                                                    <?php echo $this->lang->line('sms'); ?>
                                                </label>
                                                <?php }
                                                            if ($note_value->display_notification) {
                                                                    ?>
                                                    <label class="form-check form-check-inline">
                    <input type="checkbox" name="notification_<?php echo $note_value->id; ?>" value="1" <?php echo set_checkbox('notification_' . $note_value->id, 1, set_value('notification_' . $note_value->id, $note_value->is_notification) ? true : false); ?>>
                                                  <?php echo $this->lang->line('mobile_app')?>
                                                </label>

    <?php
}
    ?>
<?php if ($this->module_lib->hasModule('whatsapp_messaging') && $this->module_lib->hasActive('whatsapp_messaging')){ ?>
<?php if ($note_value->display_whatsapp) { ?>
                                                <label class="form-check form-check-inline">
                                                    <input type="checkbox" name="whatsapp_<?php echo $note_value->id; ?>" value="1" <?php echo set_checkbox('whatsapp_' . $note_value->id, 1, set_value('whatsapp_' . $note_value->id, $note_value->is_whatsapp) ? true : false); ?>>
                                                    WhatsApp
                                                </label>
<?php } ?>
<?php } ?>
                                            </td>
                                            <td width="15%"><?php echo $note_value->template_id ; ?></td>
                                            <?php if ($this->module_lib->hasModule('whatsapp_messaging') && $this->module_lib->hasActive('whatsapp_messaging')){ ?>
                                            <td width="15%"><?php echo $note_value->whatsapp_template_id ; ?></td>
                                            <?php } ?>
                                            <td width="25%">
                                                <?php
if (!empty($note_value)) {
        echo $note_value->template;
    }
    ?>
                                                <br/>
<?php if ($this->rbac->hasPrivilege('notification_setting', 'can_edit')) { ?>
<button type="button" class="button_template btn btn-primary btn-sm" id="load" data-record-id="<?php echo $note_value->id; ?>" data-loading-text="<i class='fa fa-spinner fa-spin '></i> Please wait.."><i class="fa fa-pencil-square-o"></i></button>
<?php }?>

                                            </td>
                                        </tr>


                                        <?php
$i++;
}
?>
                                </tbody>
                            </table>
                          </div>  
                        </div> 
                      <?php if ($this->rbac->hasPrivilege('notification_setting', 'can_edit')) { ?>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-info float-end"><?php echo $this->lang->line('save'); ?></button>
                        </div> 
                       <?php } ?>   
                             
            </div>
            </form>   
        </div>
</div><!--./wrapper-->


</div>
<div class="modal fade sh-modal sh-modal-accent" id="templateModal" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <form action="<?php echo site_url('admin/notification/savetemplate') ?>" method="post" id="templateForm">
            <div class="modal-header">
                <h5 class="modal-title" id="myModalLabel"><?php echo $this->lang->line('template'); ?></h5>
                <div class="d-flex align-items-center gap-2 ms-auto">
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            </div>
            <div class="pup-scroll-area">
                <div class="modal-body template_modal_body">
                </div>
            </div>
            <div class="modal-footer">
                <?php if ($this->rbac->hasPrivilege('notification_setting', 'can_edit')) { ?>
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                <button type="submit" class="template_update btn btn-info" id="load" data-loading-text="<i class='fa fa-spinner fa-spin '></i> Processing.."><?php echo $this->lang->line('save'); ?></button>
                <?php } ?>
            </div>
            </form>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function(){
        $(document).on('click', '.button_template', function() {
            $('.template_message_error').html("");
             var $this = $(this);
             var id=$this.data('recordId');
            $this.btnLoading();
            $.ajax({
                type: 'POST',
                dataType: 'JSON',
                url: baseurl+"admin/notification/gettemplate",
                data: {'id':id},
                beforeSend: function() {
                },
                success: function(data) {
                   if(data.status){
                    shModal('templateModal').show();
                    $('.template_modal_body').html(data.template);

                   }
                },
                error: function(xhr) { // if error occured
                    alert("Error occured.please try again");
 $this.btnReset();
                },
                complete: function() {
 $this.btnReset();
                }
            });
        });

    });

  $("#templateForm").submit(function(e) {
$('.template_message_error').html("");
 var submit_btn = $(this).find("button[type=submit]:focus" );
    var form = $(this);
    var url = form.attr('action');

    $.ajax({
                url: url,
                type: 'POST',
                dataType: 'JSON',
                data: form.serialize(), // serializes the form's elements.
               beforeSend: function() {
                 submit_btn.btnLoading();
                },
                success: function(data) {
                   if(data.status){
                successMsg(data.message);
                 window.location.reload(true);
                   }else{
                    $.each(data.error,function(key,val){
                    $('.'+key+'_error').html(val);

                    });
                   }
                },
                error: function(xhr) { // if error occured
                    alert("Error occured.please try again");
     submit_btn.btnReset();
                },
                complete: function() {
    submit_btn.btnReset();
                }
         });

    e.preventDefault(); // avoid to execute the actual submit of the form.
});

</script>
<?php

function checkExists($notificationlist, $key) {

    foreach ($notificationlist as $not_key => $not_value) {
        if ($not_value->type == $key) {
            return array(
                'is_mail' => $not_value->is_mail,
                'is_sms' => $not_value->is_sms,
                'is_mobileapp' => $not_value->is_mobileapp
            );
        }
    }
    return false;
}
?>