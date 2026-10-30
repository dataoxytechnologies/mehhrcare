<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header ptbnull d-flex align-items-center justify-content-between flex-wrap gap-2">
                <h5 class="card-title mb-0"><?php echo $this->lang->line('banner_images'); ?></h5>
                <?php if ($this->rbac->hasPrivilege('banner_images', 'can_add')) { ?>
                <button type="button" class="btn btn-primary btn-sm gallery_image" id="gallery_images" data-bs-toggle="modal" data-bs-target="#mediaModal">
                    <i class="fa fa-plus"></i> <?php echo $this->lang->line('add_images'); ?>
                </button>
                <?php } ?>
            </div>
            <div class="card-body">
                <?php if (isset($banner_images) && !empty($banner_images)) { ?>
                <div class="banner-file-count">
                    <strong><?php echo count($banner_images); ?></strong> <?php echo $this->lang->line('banner_images') ?: 'images'; ?>
                </div>
                <?php } ?>
                <div id="banner_div">
                    <?php if (isset($banner_images) && !empty($banner_images)) {
                        foreach ($banner_images as $banner_image_key => $banner_image_value) { ?>
                        <div class="img_div_modal gallery_img div_record_<?php echo $banner_image_value->id ?>">
                            <div class="fadeoverlay">
                                <div class="fadeheight">
                                    <?php if ($this->rbac->hasPrivilege('banner_images', 'can_view')) { ?>
                                    <img data-fid="<?php echo $banner_image_value->id ?>"
                                         data-content_name="<?php echo html_escape($banner_image_value->img_name); ?>"
                                         data-img="<?php echo base_url($banner_image_value->thumb_path . $banner_image_value->img_name) ?>"
                                         src="<?php echo $this->media_storage->getImageURL($banner_image_value->thumb_path . $banner_image_value->img_name) ?>"
                                         alt="<?php echo html_escape($banner_image_value->img_name); ?>">
                                    <?php } ?>
                                    <?php if ($this->rbac->hasPrivilege('banner_images', 'can_delete')) { ?>
                                    <div class="overlay3">
                                        <a href="javascript:void(0);" title="<?php echo $this->lang->line('delete'); ?>"
                                           class="uploadclosebtn delete_gallery_img"
                                           data-record_id="<?php echo $banner_image_value->id ?>">
                                            <i class="fa fa-trash"></i>
                                        </a>
                                    </div>
                                    <?php } ?>
                                </div>
                                <div class="img-caption">
                                    <span class="img-caption-name" title="<?php echo html_escape($banner_image_value->img_name); ?>"><?php echo html_escape($banner_image_value->img_name); ?></span>
                                </div>
                            </div>
                        </div>
                    <?php }
                    } else { ?>
                    <div class="w-100">
                        <div class="alert alert-info text-center mb-0"><?php echo $this->lang->line('no_record_found'); ?></div>
                    </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        var baseurl = '<?php echo base_url(); ?>';
        var popup_target = 'gallery_image';

        modal_click_disabled('mediaModal');

        $('#mediaModal').on('show.bs.modal', function (event) {
            popup_target = $(event.relatedTarget)[0].id;
            var $modalDiv = $(event.delegateTarget);
            $('.modal-media-body').html("");
            $.ajax({
                type: "POST",
                url: baseurl + "admin/front/media/getMedia",
                dataType: 'text',
                data: {},
                beforeSend: function () { $modalDiv.addClass('modal_loading'); },
                success: function (data) { $('.modal-media-body').html(data); },
                error: function () { $modalDiv.removeClass('modal_loading'); },
                complete: function () { $modalDiv.removeClass('modal_loading'); },
            });
        });

        $(document).on('click', '.img_div_modal', function () {
            $('.img_div_modal div.fadeoverlay').removeClass('active');
            $(this).closest('.img_div_modal').find('.fadeoverlay').addClass('active');
        });

        $(document).on('click', '.add_media', function () {
            var $active = $('div#media_div').find('.fadeoverlay.active');
            var content_id   = $active.find('img').data('fid');
            var content_type = $active.find('img').data('content_type');

            if (popup_target === "gallery_images") {
                if (content_type === "image/gif" || content_type === "image/jpeg" || content_type === "image/png") {
                    $.ajax({
                        type: "POST",
                        url: baseurl + "admin/front/banner/add",
                        dataType: 'json',
                        data: {'content_id': content_id},
                        success: function (data) {
                            if (data.status == 1) {
                                successMsg(data.msg);
                                window.location.reload();
                            }
                        },
                        complete: function () { shModal('mediaModal').hide(); },
                    });
                }
            }
        });
    });

    $(document).on('click', '.delete_gallery_img', function (e) {
        var content_id = $(this).data('record_id');
        var baseurl = '<?php echo base_url(); ?>';
        if (confirm('<?php echo $this->lang->line('are_you_sure'); ?>')) {
            $.ajax({
                type: "POST",
                url: baseurl + "admin/front/banner/remove",
                dataType: 'json',
                data: {'content_id': content_id},
                success: function (data) {
                    if (data.status == 1) {
                        $(e.target).closest('.gallery_img').remove();
                        successMsg(data.msg);
                    } else {
                        errorMsg(data.msg);
                    }
                },
            });
        }
    });
</script>

<!-- Media picker modal -->
<div class="modal fade sh-modal sh-modal-nospace" id="mediaModal" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myModalLabel"><?php echo $this->lang->line('media_manager'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="pup-scroll-area">
                <div class="modal-body modal-media-body"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                <button type="button" class="btn btn-primary add_media"><?php echo $this->lang->line('add'); ?></button>
            </div>
        </div>
    </div>
</div>
