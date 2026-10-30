<div class="row">
    <div class="col-md-2">
        <?php $this->load->view('admin/printing/sidebar'); ?>
    </div>
    <div class="col-md-10">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title titlefix"><?php echo $this->lang->line('ipd_bill_header_footer'); ?></h3>
            </div>
            <div class="card-body">
                <form enctype="multipart/form-data" action="<?php echo site_url('admin/printing/update'); ?>" method="post">
                    <input type="hidden" name="id" value="<?php if (!empty($printing_list)) { echo $printing_list[0]['id']; } ?>">
                    <input type="hidden" name="function_name" value="<?php echo $function_name; ?>">

                    <div class="row g-3">
                        <div class="col-12">
                            <div class="sh-form-card h-100 mb-0">
                                <div class="sh-card-header">
                                    <span class="sh-card-header-title"><i class="fa fa-image me-1 opacity-75"></i><?php echo $this->lang->line('header_image'); ?></span>
                                </div>
                                <div class="p-3">
                                    <label class="form-label" for="header_image"><small class="text-muted">(2230px × 300px)</small></label>
                                    <div class="sh-upload-tall">
                                    <input id="header_image" data-default-file="<?php if (!empty($printing_list)) { echo $this->customlib->getBaseUrl() . $printing_list[0]['print_header']; } ?>" type="file" class="filestyle form-control" data-height="180" name="header_image">
                                    <input type="hidden" class="form-control" name="print_header">
                                    </div>
                                    <span class="text-danger small"><?php echo form_error('header_image'); ?></span>
                                    <?php if (!empty($printing_list[0]['print_header'])) { ?>
                                    <div class="header_image d-flex align-items-center gap-2 mt-2">
                                        <a class="uploadclosebtn" title="<?php echo $this->lang->line('delete'); ?>"><i class="fa fa-trash-o text-danger cursor-pointer" onclick="removeheader_image()"></i></a>
                                        <span class="text-muted small text-truncate"><?php echo $printing_list[0]['print_header']; ?></span>
                                    </div>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="sh-form-card h-100 mb-0">
                                <div class="sh-card-header">
                                    <span class="sh-card-header-title"><i class="fa fa-align-center me-1 opacity-75"></i><?php echo $this->lang->line('footer_content'); ?></span>
                                </div>
                                <div class="p-3">
                                    <textarea id="compose_textarea" name="footer_content" class="form-control h-250"><?php if (!empty($printing_list)) { echo $printing_list[0]['print_footer']; } ?></textarea>
                                    <span class="text-danger small"><?php echo form_error('footer_content'); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="text-end mt-3">
                        <button type="submit" class="btn btn-info" data-loading-text="<i class='fa fa-spinner fa-spin'></i> <?php echo $this->lang->line('save'); ?>"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
$(function () {
    CKEDITOR.env.isCompatible = true;
    CKEDITOR.replace('compose_textarea', { entities: false });
    $('#header_image').dropify();
});

function removeheader_image() {
    var result = confirm("<?php echo $this->lang->line('delete_confirm'); ?>");
    if (result) {
        $('.header_image').html('<input type="hidden" name="removeheader_image" value="1">');
        $(".dropify-clear").click();
    }
}
</script>
