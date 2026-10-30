<div class="row">
            <!-- left column -->
            <?php $this->load->view('setting/sidebar'); ?>
            <div class="col-md-10">
                <!-- general form elements -->
                <div class="card">
                    <div class="card-header ptbnull d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <h3 class="card-title mb-0"><?php echo $this->lang->line('language_list'); ?></h3>
                        <?php if ($this->rbac->hasPrivilege('languages', 'can_add')) { ?>
                        <a href="#" data-bs-target="#myModal" class="btn btn-primary btn-sm" data-bs-toggle="modal" title="<?php echo $this->lang->line('add'); ?>">
                            <i class="fa fa-plus"></i> <?php echo $this->lang->line('add'); ?>
                        </a>
                        <?php } ?>
                    </div><!-- /.card-header -->
                    <div class="card-body">
                        <div class="alert alert-warning">
                            To change language key phrases, go your language directory e.g. for English language go edit file  /application/language/English/app_files/system_lang.php
                        </div>
                        <?php if ($this->session->flashdata('msg')) { ?>
                            <?php 
                                echo $this->session->flashdata('msg');
                                $this->session->unset_userdata('msg');
                             ?>
                        <?php } ?>
                        <div class="table-responsive">
                            <table class="table table-hover table-striped align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th class="sh-th-50">#</th>
                                        <th><?php echo $this->lang->line('language'); ?></th>
                                        <th><?php echo $this->lang->line('short_code'); ?></th>
                                        <th><?php echo $this->lang->line('country_code'); ?></th>
                                        <th><?php echo $this->lang->line('status'); ?></th>
                                        <th class="text-center sh-th-90"><?php echo $this->lang->line('active'); ?></th>
                                        <th class="text-center sh-th-90"><?php echo $this->lang->line('is_rtl'); ?></th>
                                        <th class="text-end rtl-text-start sh-th-140"><?php echo $this->lang->line('action'); ?></th>
                                    </tr>
                                </thead>
                                <tbody id="result_data">
                                    <?php $this->load->view('admin/language/languageResult'); ?>
                                </tbody>
                            </table>
                        </div>
                    </div><!-- /.card-body -->
                    <div class="card-footer">
                        <div class="mailbox-controls">
                        </div>
                    </div>
                </div>
            </div>
        </div>   <!-- /.row -->
    
</div> 

<div class="modal fade sh-modal sh-modal-accent" id="myModal" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myModalLabel"><?php echo $this->lang->line('add_language'); ?></h5>
                <div class="d-flex align-items-center gap-2 ms-auto">
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            </div>
            <form id="addlanguage" method="post" accept-charset="utf-8" enctype="multipart/form-data">
                <div class="pup-scroll-area">
                    <div class="modal-body">
                        <div class="sh-form-card mb-0">
                            <div class="sh-card-header">
                                <span class="sh-card-header-title"><?php echo $this->lang->line('add_language'); ?></span>
                            </div>
                            <div class="p-3">
                                <div class="row">
                                    <div class="mb-3 col-12">
                                        <label for="invoice_no" class="form-label"><?php echo $this->lang->line('language'); ?><small class="req"> *</small></label>
                                        <input id="invoice_no" name="language" type="text" class="form-control" value="<?php echo set_value('language'); ?>" />
                                    </div>
                                    <div class="mb-3 col-12">
                                        <label for="short_code" class="form-label"><?php echo $this->lang->line('short_code'); ?><small class="req"> *</small></label>
                                        <input id="short_code" name="short_code" type="text" class="form-control" value="<?php echo set_value('short_code'); ?>" />
                                    </div>
                                    <div class="mb-0 col-12">
                                        <label for="country_code" class="form-label"><?php echo $this->lang->line('country_code'); ?><small class="req"> *</small></label>
                                        <input id="country_code" name="country_code" type="text" class="form-control" value="<?php echo set_value('country_code'); ?>" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                    <button type="submit" class="btn btn-info"><?php echo $this->lang->line('save'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>
 
<script type="text/javascript">
//automatically show next editable

    $(document).ready(function (e) {
        $("#addlanguage").on('submit', (function (e) {
            e.preventDefault();
            $.ajax({
                url: '<?php echo base_url(); ?>admin/language/create',
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
                },
                error: function () {
                    alert("<?php echo $this->lang->line('fail'); ?>");
                }
            });
        }));
    });
</script>           
<script type="text/javascript">
    $(document).ready(function () {
        var onload=  $('#languageSwitcher').val();
        $(document).on('click', '.chk', function () {

            var checked = $(this).is(':checked');
           
            var rowid = $(this).data('rowid');
            var role = $(this).data('role');
            var confirm_msg='<?php echo $this->lang->line('status_changed') ?>';
           
            if (checked) {

                if (!confirm(confirm_msg)) {
                    $(this).removeAttr('checked');

                } else {
                    var status = "yes";

                    if(role=='2'){
                        changeStatusselect(rowid);
                    }else{
                        changeStatusunselect(rowid);
                    }

                }

            } else if (!confirm(confirm_msg)) {

                $(this).prop("checked", true);
            } else {
                
                var status = "no";
                if(role=='2'){
                        changeStatusselect(rowid);
                    }else{
                        changeStatusunselect(rowid);
                    }

            }
        });
    });

    function changeStatusselect(rowid) {
        var base_url = '<?php echo base_url() ?>';
        $.ajax({
            type: "POST",
            url: base_url + "admin/language/select_language/"+rowid,
            data: {},
            success: function (data) {
              successMsg("<?php echo $this->lang->line('status_change_successfully'); ?>");
              $('#languageSwitcher').html(data);
               window.location.reload('true');
            }
        });
    }

    function changeStatusunselect(rowid) {
        var base_url = '<?php echo base_url() ?>';
        $.ajax({
            type: "POST",
            url: base_url + "admin/language/unselect_language/"+rowid,
            data: {},
            success: function (data) {
               successMsg("<?php echo $this->lang->line('status_change_successfully'); ?>");
               window.location.reload('true');
           }
        });
    }


function load(){
    $.ajax({
        type: "POST",
        url: '<?php echo base_url() ?>admin/language/onloadlanguage',
        data: {},
        success: function (data) {
           window.location.reload('true');
        }
    });
}

    function defoult(id){
        $.ajax({
        type: "POST",
        url: '<?php echo base_url() ?>admin/language/defoult_language/'+id,
        data: {},
        success: function (data) {
           window.location.reload('true');
        }
        });
    }
    
    $(document).ready(function (e) {
        modal_click_disabled('myModal');
    });
 
    function rtl(id){
        var rtl_val ='#rtl_'+id;

        if ($(rtl_val).is(":checked")){
           status = 'yes';
        }else{
           status = 'no';  
        }
        if (confirm("<?php echo $this->lang->line('are_you_sure'); ?>")) {

            $.ajax({
            type: "POST",
            url: '<?php echo base_url() ?>admin/language/rtl',
            data: {'status':status,'id':id},
            dataType: "json",
            success: function (data) {
                if(data.status==1){
                    window.location.reload('true');
                }else{
                    // alert("<?php echo $this->lang->line('something_went_wrong'); ?>");
                    window.location.reload('true');
                }
            }
            });
        }else{
             // alert("<?php echo $this->lang->line('something_went_wrong'); ?>");
             window.location.reload('true');
        }
    }
</script>