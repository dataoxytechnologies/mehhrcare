<!-- Content Wrapper. Contains page content -->
<div class="row">            
            <div class="col-md-12">
                <!-- general form elements -->
                <div class="card">
                    <div class="card-header ptbnull">
                        <h3 class="card-title titlefix"> <?php echo  $this->lang->line('content_share_list'); ?></h3>
                        <div class="d-flex gap-2 align-items-center flex-wrap float-end">
                        </div><!-- /.ms-auto d-flex gap-1 -->
                    </div><!-- /.card-header -->
                    <div class="card-body">                       
                        <div class="table-responsive mailbox-messages overflow-visible-lg">
                                 <table class="table table-striped table-bordered table-hover content-list" data-export-title="<?php echo  $this->lang->line('content_share_list'); ?>">
                                <thead>
                                    <tr>
                                        <th><?php echo $this->lang->line('title'); ?></th>
                                        <th><?php echo $this->lang->line('send_to'); ?></th>
                                        <th><?php echo $this->lang->line('share_date'); ?></th>
                                        <th><?php echo $this->lang->line('valid_upto'); ?></th>
                                        <th><?php echo $this->lang->line('shared_by'); ?></th>
                                        <th><?php echo $this->lang->line('description'); ?></th>
                                        <th class="text-end noExport"><?php echo $this->lang->line('action'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table><!-- /.table -->
                        </div><!-- /.mail-box-messages -->
                    </div><!-- /.card-body -->
                </div>
            </div><!--/.col (left) --> 
        </div>
    


<div id="linkModal" class="modal fade sh-modal sh-modal-accent" tabindex="-1" aria-labelledby="linkModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="linkModalLabel"><?php echo $this->lang->line('link'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="pup-scroll-area">
                <div class="modal-body">
                    <div class="input-group">
                        <input type="text" class="form-control share_link" readonly>
                        <button type="button" class="btn btn-outline-secondary share_copy_btn"
                                data-bs-toggle="tooltip" title="<?php echo $this->lang->line('copy'); ?>"
                                onclick="copylink()"><i class="fa fa-copy"></i></button>
                        <a href="#" target="_blank" class="btn btn-outline-secondary share_open_btn"
                           data-bs-toggle="tooltip" title="<?php echo $this->lang->line('open_in_new_tab'); ?>"><i class="fa fa-external-link"></i></a>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('close'); ?></button>
            </div>
        </div>
    </div>
</div>

<div id="viewShareModal" class="modal fade sh-modal sh-modal-accent" tabindex="-1" aria-labelledby="viewShareModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewShareModalLabel"><?php echo $this->lang->line('shared_contents'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="pup-scroll-area">
                <div class="modal-body">
                    <div class="modal_loader_div" style="display: none;"></div>
                    <div class="modal-body-inner"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('close'); ?></button>
            </div>
        </div>
    </div>
</div>

<script>
    // Fill modal with content from link href
$("#viewShareModal").on("show.bs.modal", function(e) {
    var link = $(e.relatedTarget);
    let recordid=link.data('recordid');
                $.ajax({
                    url: baseurl+'admin/content/getsharedcontents',
                    type: "POST",
                    data: {"share_content_id" : recordid},
                    dataType: 'json',                   
                    beforeSend: function () {
                        $('#viewShareModal .modal-body .modal-body-inner').html(""); 
                        $('#viewShareModal .modal-body .modal_loader_div').css("display", "block"); 
                    },
                    success: function (data)
                    {
                          $('#viewShareModal .modal-body .modal-body-inner').html(data.page); 
                          $('#viewShareModal .modal-body .modal_loader_div').fadeOut(400);
                    },
                    error: function (xhr) { // if error occured
                    alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");
                 
                    },
                    complete: function () {
            
                    }
            });
});

    ( function ( $ ) {
    'use strict';
    $(document).ready(function () {

        modal_click_disabled('viewShareModal', 'linkModal');

        initDatatable('content-list','admin/content/getsharelist',[],[],100,
            [
                { "bSortable": true, "aTargets": [ -2 ] ,'sClass': 'dt-body-left', "width": "20%"},
                 { "bSortable": false, "aTargets": [ -1 ] ,'sClass': 'dt-body-right'}
            ]);

        $('#content-list').on('draw.dt', function () {
            $('[data-bs-toggle="tooltip"]').tooltip();
        });
    });
} ( jQuery ) )

    $("#linkModal").on('hidden.bs.modal', function () {
    $('#linkModal .share_link').val('');
});

$('#linkModal').on('show.bs.modal', function (event) {
    var filtre = $(event.relatedTarget).data();
    $(this).find('.share_link').val(filtre.link);
    $(this).find('.share_open_btn').attr('href', filtre.link);
});

function copylink() {
    var $inp = $('.share_link');
    $inp[0].select();
    document.execCommand("copy");
}

</script>