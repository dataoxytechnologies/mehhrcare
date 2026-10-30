<script src="<?php echo base_url(); ?>backend/plugins/ckeditor/ckeditor.js"></script>
<!-- Content Wrapper. Contains page content -->
<!-- Content Header (Page header) -->

    <!-- Main content -->
    <form id="form1" action="<?php echo site_url('admin/front/gallery/edit/' . $result['slug']) ?>" enctype="multipart/form-data" name="employeeform" method="post" accept-charset="utf-8">
    <div class="row">
                <div class="col-md-9">
                    <!-- Horizontal Form -->
                    <div class="card">
                        <div class="card-header d-flex align-items-center">
                            <h3 class="card-title titlefix"><?php echo $this->lang->line('edit_gallery'); ?></h3>
                        </div><!-- /.card-header -->
                        <!-- form start -->

                        <div class="card-body">
                            <input type="hidden" name="id" value="<?php echo $result['id']; ?>">
                            <?php if ($this->session->flashdata('msg')) {?>
                                <?php echo $this->session->flashdata('msg') ?>
                            <?php }?>
                            <?php
if (isset($error_message)) {
    echo "<div class='alert alert-danger'>" . $error_message . "</div>";
}
?>
                            <?php echo $this->customlib->getCSRF(); ?>
                            <div class="mb-3">
                                <span><a href="<?php echo site_url() . $result['url']; ?>" target="_blank" class="text-danger"><i class="fa fa-globe" aria-hidden="true"></i> <?php echo site_url() . $result['url']; ?></a></span>
                            </div>
                            <div class="mb-3">
                                <label for="exampleInputEmail1"><?php echo $this->lang->line('title'); ?></label>
                                <input id="title" name="title" placeholder="" type="text" class="form-control"  value="<?php echo set_value('title', $result['title']); ?>" />
                                <span class="text-danger"><?php echo form_error('title'); ?></span>
                            </div>
                            <div class="dividerhr"></div>
                            <div class="formgroup10 mb10">
                                <label for="exampleInputEmail1"><?php echo $this->lang->line('description'); ?></label>
                                <button type="button" class="btn btn-primary btn-sm float-end" id="media_images" data-bs-toggle="modal" data-bs-target="#mediaModal"><i class="fa fa-plus"></i>
                                    <?php echo $this->lang->line('add_media'); ?>
                                </button>
                            </div>
                            <div class="mb-3">
                                <textarea id="editor1" name="description" placeholder="" type="text" class="form-control ss" >
                                    <?php echo set_value('description', $result['description']); ?>
                                </textarea>
                                <span class="text-danger"><?php echo form_error('description'); ?></span>
                            </div>
                            <div class="dividerhr"></div>
                            <div class="formgroup10 sh-gallery-section">
                                <label><?php echo $this->lang->line('gallery_images'); ?></label>
                                <button type="button" class="btn btn-primary btn-sm float-end gallery_image" id="gallery_images"><i class="fa fa-plus"></i> <?php echo $this->lang->line('add_images'); ?></button>
                            </div><!-- /.card-header -->
                            <div class="mediarow">
                                <div class="gallery_content sh-gallery-grid">
                                    <div class="sh-gallery-empty">
                                        <i class="fa fa-picture-o sh-empty-icon" aria-hidden="true"></i>
                                        <p>No images added yet</p>
                                        <small>Click &ldquo;<?php echo $this->lang->line('add_images'); ?>&rdquo; to choose images</small>
                                    </div>
                                    <?php
if (!empty($result['page_contents'])) {
    foreach ($result['page_contents'] as $page_content_key => $page_content_value) {
        $img_name_safe = html_escape($page_content_value->img_name);
        ?>
                                        <div class='img_div_modal gallery_img sh-gallery-tile div_record_<?php echo $page_content_value->id; ?>'>
                                            <div class='fadeoverlay sh-tile-thumb'>
                                                <img class='img-fluid sh-tile-img' data-fid='<?php echo $page_content_value->id; ?>' data-content_name='<?php echo $img_name_safe; ?>' data-is_image='' data-img='<?php echo $page_content_value->dir_path . $page_content_value->img_name; ?>' src='<?php echo $this->media_storage->getImageURL($page_content_value->thumb_path . $page_content_value->img_name); ?>' alt='<?php echo $img_name_safe; ?>'>
                                                <input type='hidden' value='<?php echo $page_content_value->id; ?>' name='gallery_images[]'>
                                                <span class='sh-tile-chip'>
                                                    <?php if ($page_content_value->file_type == 'video'): ?>
                                                        <i class='fa fa-youtube-play'></i>
                                                    <?php else: ?>
                                                        <i class='fa fa-picture-o'></i>
                                                    <?php endif; ?>
                                                </span>
                                                <div class='overlay3 sh-tile-actions'>
                                                    <a href='#' class='uploadclosebtn delete_gallery_img sh-tile-delete' data-record_id='<?php echo $page_content_value->id; ?>' data-bs-toggle='modal' data-bs-target='#confirm-delete' title='<?php echo $this->lang->line('delete'); ?>' aria-label='<?php echo $this->lang->line('delete'); ?>'><i class='fa fa-trash'></i></a>
                                                </div>
                                            </div>
                                            <p class='sh-tile-name' title='<?php echo $img_name_safe; ?>'><?php echo $img_name_safe; ?></p>
                                        </div>
                                        <?php
}
}
?>
                                </div>
                            </div>
                        </div><!-- /.card-body -->
                    </div>
                    <div class="card">
                        <div class="card-header d-flex align-items-center">
                            <h3 class="card-title titlefix"><?php echo $this->lang->line('seo_detail'); ?></h3>
                            <div class="ms-auto d-flex gap-1">
                                <button type="button" class="btn btn-box-tool" data-bs-toggle="collapse" data-bs-target="#seoDetailBody" aria-expanded="false"><i class="fa fa-plus"></i></button>
                            </div>
                        </div>
                        <div class="card-body collapse" id="seoDetailBody">
                            <div class="mb-3">
                                <label for="exampleInputEmail1"><?php echo $this->lang->line('meta_title'); ?></label>
                                <input id="meta_title" name="meta_title" placeholder="" type="text" class="form-control"  value="<?php echo set_value('meta_title', $result['meta_title']); ?>" />
                                <span class="text-danger"><?php echo form_error('meta_title'); ?></span>
                            </div>
                            <div class="mb-3">
                                <label for="exampleInputEmail1"><?php echo $this->lang->line('meta_keyword'); ?></label>
                                <input id="meta_keywords" name="meta_keywords" placeholder="" type="text" class="form-control"  value="<?php echo set_value('meta_keywords', $result['meta_keyword']); ?>" />
                                <span class="text-danger"><?php echo form_error('meta_keywords'); ?></span>
                            </div>
                            <div class="mb-3">
                                <label for="exampleInputEmail1"><?php echo $this->lang->line('meta_description'); ?></label>
                                <textarea id="meta_description" name="meta_description" placeholder="" type="text" class="form-control"><?php echo set_value('meta_description', $result['meta_description']); ?></textarea>
                                <span class="text-danger"><?php echo form_error('meta_description'); ?></span>
                            </div>
                        </div><!-- /.card-body -->
                    </div>
                </div><!--/.col (right) -->
                <!-- left column -->
                <div class="col-md-3 col-sm-12">
                    <div class="">
                        <!-- page settings -->
                        <div class="card">
                            <div class="card-header d-flex align-items-center">
                                <h3 class="card-title titlefix"><?php echo $this->lang->line('sidebar_setting'); ?></h3>
                                <div class="ms-auto d-flex gap-1">
                                    <button type="button" class="btn btn-box-tool" data-bs-toggle="collapse" data-bs-target="#sidebarSettingBody" title="<?php echo $this->lang->line('collapse'); ?>"><i class="fa fa-minus"></i></button>
                                </div><!-- /.ms-auto d-flex gap-1 -->
                            </div><!-- /.card-header -->
                            <div class="card-body collapse show" id="sidebarSettingBody">
                                <div class="mb-3 d-flex align-items-center justify-content-between">
                                    <label for="sidebar"><?php echo $this->lang->line('sidebar'); ?></label>
                                    <div class="form-check form-switch m-0">
                                        <input id="sidebar" name="sidebar" type="checkbox" role="switch" class="form-check-input chk" value="1" <?php echo set_checkbox('sidebar', 1, $result['sidebar'] == 1); ?>/>
                                    </div>
                                </div>
                            </div><!-- /.card-body -->
                        </div><!-- /.box -->

                        <!-- page image -->
                        <div class="card">
                            <div class="card-header d-flex align-items-center">
                                <h3 class="card-title titlefix"><?php echo $this->lang->line('featured_image'); ?></h3>
                                <div class="ms-auto d-flex gap-1">
                                    <button type="button" class="btn btn-box-tool" data-bs-toggle="collapse" data-bs-target="#featuredImageBody" title="<?php echo $this->lang->line('collapse'); ?>"><i class="fa fa-minus"></i></button>
                                </div><!-- /.ms-auto d-flex gap-1 -->
                            </div><!-- /.card-header -->
                            <div class="card-body collapse show" id="featuredImageBody">
                                <div class="mb-3">
                                    <div class="input-group input-group-sm">
                                        <input class="form-control iframe-btn" placeholder="<?php echo $this->lang->line('select_image'); ?>" type="text" name="image" id="image" value="<?php echo html_escape($result['feature_image']); ?>">
                                        <a href="#" class="btn btn-primary feature_image_btn" id="feature_image" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('select_image'); ?>"><i class="fa fa-folder-open"></i></a>
                                        <a href="#" class="btn btn-outline-secondary delete_media" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('delete'); ?>"><i class="fa fa-trash"></i></a>
                                    </div>
                                    <?php $image_display = ($result['feature_image'] != "") ? "" : "display: none"; ?>
                                    <div id="image_preview" class="border rounded p-2 text-center" style="<?php echo $image_display; ?>">
                                        <img src="<?php echo $this->media_storage->getImageURL($result['feature_image']); ?>" class="img-fluid feature_image_url">
                                    </div>
                                </div>
                            </div><!-- /.card-body -->
                        </div><!-- /.box -->
                        <!-- Save button -->
                        <div class="card">
                            <div class="card-body d-grid">
                                <button type="submit" class="btn btn-primary btn-lg"><i class="fa fa-save me-2"></i><?php echo $this->lang->line('save'); ?></button>
                            </div><!-- /.card-body -->
                        </div><!-- /.box -->
                    </div>
                </div><!-- /.col-md-3 -->
    </div><!-- /.row -->
    </form>


<script>
    $(document).ready(function () {
        var popup_target = 'media_images';
        var date_format = '<?php echo $result = strtr($this->customlib->getHospitalDateFormat(), ['d' => 'dd', 'm' => 'mm', 'Y' => 'yyyy']) ?>';
        /* .date init removed - auto-init via event delegation */

        CKEDITOR.replace('editor1',
                {
                    allowedContent: true
                });

        modal_click_disabled('mediaModal');

        $(document).on('click', '.feature_image_btn', function (event) {
            event.preventDefault();
            popup_target = 'feature_image';
            shModal('mediaModal').toggle();
        });

        $(document).on('click', '.gallery_image', function (event) {
            event.preventDefault();
            popup_target = 'gallery_images';
            shModal('mediaModal').toggle();
        });

        $('#mediaModal').on('show.bs.modal', function (event) {
            if (event.relatedTarget && event.relatedTarget.id) {
                popup_target = event.relatedTarget.id;
            }
            var $modalDiv = $(event.delegateTarget);
            $('.modal-media-body').html("");
            $.ajax({
                type: "POST",
                url: baseurl + "admin/front/media/getMedia",
                dataType: 'text',
                data: {},
                beforeSend: function () {
                    $modalDiv.addClass('modal_loading');
                },
                success: function (data) {
                    $('.modal-media-body').html(data);
                },
                error: function (xhr) { // if error occured
                    $modalDiv.removeClass('modal_loading');
                },
                complete: function () {
                    $modalDiv.removeClass('modal_loading');
                },
            });
        });

        $('.detail_popover').popover({
            placement: 'right',
            trigger: 'hover',
            container: 'body',
            html: true,
            content: function () {
                return $(this).closest('td').find('.fee_detail_popover').html();
            }
        });

        $(document).on('click', '.img_div_modal', function (event) {
            $('.img_div_modal div.fadeoverlay').removeClass('active');
            $(this).closest('.img_div_modal').find('.fadeoverlay').addClass('active');
        });

        $(document).on('click', '.add_media', function (event) {
            var content_html = $('div#media_div').find('.fadeoverlay.active').find('img').data('img');
            var content_id = $('div#media_div').find('.fadeoverlay.active').find('img').data('fid');
            var is_image = $('div#media_div').find('.fadeoverlay.active').find('img').data('is_image');
            var content_type = $('div#media_div').find('.fadeoverlay.active').find('img').data('content_type');
            var content_name = $('div#media_div').find('.fadeoverlay.active').find('img').data('content_name');
            var vid_url = $('div#media_div').find('.fadeoverlay.active').find('img').data('vid_url');
            var content = "";

            if (popup_target === "media_images") {
                if (typeof content_html !== "undefined") {
                    if (is_image === 1) {
                        content = '<img src="' + content_html + '">';
                    } else if (content_type == "video") {
                        var youtubeID = YouTubeGetID(vid_url);
                        content = '<iframe id="video" width="420" height="315" src="//www.youtube.com/embed/' + youtubeID + '?rel=0" frameborder="0" allowfullscreen></iframe>';
                    } else {
                        content = '<a href="' + content_html + '">' + content_name + '</a>';
                    }
                    InsertHTML(content);
                    shModal('mediaModal').hide();
                }
            } else if (popup_target === "feature_image") {
                if (typeof content_html !== "undefined" && is_image == 1) {
                    addImage(content_html);
                    shModal('mediaModal').hide();
                }
            } else if (popup_target === "gallery_images") {
                if (content_type === "image/gif" || content_type === "image/jpeg" || content_type === "image/png" || content_type === "video") {
                    insert_gallery(content_html, content_id, content_name, is_image);
                } else {
                    //error show
                }
                shModal('mediaModal').hide();
            }
        });

        $(document).on("click", ".pagination li a", function (event) {
            event.preventDefault();
            var page = $(this).data("ci-pagination-page");
            load_country_data(page);
        });
    });

    function YouTubeGetID(url) {
        var ID = '';
        url = url.replace(/(>|<)/gi, '').split(/(vi\/|v=|\/v\/|youtu\.be\/|\/embed\/)/);
        if (url[2] !== undefined) {
            ID = url[2].split(/[^0-9a-z_\-]/i);
            ID = ID[0];
        } else {
            ID = url;
        }
        return ID;
    }

    function addImage(content_html) {
        $('.feature_image_url').attr('src', content_html);
        $('#image').val(content_html);
        $('#image_preview').css("display", "block");
    }

    $(document).on('click', '.delete_media', function () {
        $('.feature_image_url').attr('src', '');
        $('#image').val('');
        $('#image_preview').css("display", "none");
    });

    function InsertHTML(content_html) {
        // Get the editor instance that we want to interact with.
        var editor = CKEDITOR.instances.editor1;
        // Check the active editing mode.
        if (editor.mode == 'wysiwyg')
        {
            editor.insertHtml(content_html);
        } else
            alert('You must be in WYSIWYG mode!');
    }

    function escapeHtml(str) {
        return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#39;');
    }

    function insert_gallery(content_image, content_id, content_name, is_image) {
        var safe_name = escapeHtml(content_name);
        var safe_img = escapeHtml(content_image);
        var icon = (is_image == 1) ? 'fa-picture-o' : 'fa-youtube-play';
        var output = '';
        output += "<div class='img_div_modal gallery_img sh-gallery-tile div_record_" + content_id + "'>";
        output += "<div class='fadeoverlay sh-tile-thumb'>";
        output += "<img class='img-fluid sh-tile-img' data-fid='" + content_id + "' data-content_name=\"" + safe_name + "\" data-is_image='" + is_image + "' data-img=\"" + safe_img + "\" src=\"" + safe_img + "\" alt=\"" + safe_name + "\">";
        output += "<input type='hidden' value='" + content_id + "' name='gallery_images[]'>";
        output += "<span class='sh-tile-chip'><i class='fa " + icon + "'></i></span>";
        output += "<div class='overlay3 sh-tile-actions'>";
        output += "<a href='#' class='uploadclosebtn delete_gallery_img sh-tile-delete' data-record_id='" + content_id + "' data-bs-toggle='modal' data-bs-target='#confirm-delete'><i class='fa fa-trash'></i></a>";
        output += "</div>";
        output += "</div>";
        output += "<p class='sh-tile-name' title=\"" + safe_name + "\">" + safe_name + "</p>";
        output += "</div>";
        $(output).appendTo(".gallery_content");
    }

    $(document).on('click', '.delete_gallery_img', function () {
        $(this).closest('.gallery_img').remove();
    });

</script>

<!-- Modal -->
<div class="modal fade sh-modal sh-modal-nospace" id="mediaModal" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog pup100 modal-dialog-centered">
        <div class="modal-content modal-media-content">
            <div class="modal-header modal-media-header">
                <h5 class="modal-title modal-media-title" id="myModalLabel"><?php echo $this->lang->line('media_manager'); ?></h5>
                <div class="d-flex align-items-center gap-2 ms-auto">
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            </div>
            <div class="pup-scroll-area">
                <div class="modal-body modal-media-body pupscroll">

                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                <button type="button" class="btn btn-info add_media"><?php echo $this->lang->line('add'); ?></button>
            </div>
        </div>
    </div>
</div>
