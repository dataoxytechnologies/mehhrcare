<script src="<?php echo base_url(); ?>backend/plugins/ckeditor/ckeditor.js"></script>

<!-- Content Wrapper. Contains page content -->
<!-- Content Header (Page header) -->
    <!-- Main content -->
    <form id="form1" action="<?php echo site_url('admin/front/notice/edit/' . $result['slug']) ?>" enctype="multipart/form-data" name="employeeform" method="post" accept-charset="utf-8">
    <div class="row">
                <div class="col-md-9">
                    <!-- Horizontal Form -->
                    <div class="card">
                        <div class="card-header d-flex align-items-center">
                            <h3 class="card-title titlefix"><?php echo $this->lang->line('edit_news'); ?></h3>
                        </div><!-- /.card-header -->
                        <!-- form start -->
                        <div class="card-body">
                            <?php if ($this->session->flashdata('msg')) {?>
                                <?php echo $this->session->flashdata('msg') ?>
                            <?php }?>
                            <?php
if (isset($error_message)) {
    echo "<div class='alert alert-danger'>" . $error_message . "</div>";
}
?>
                            <?php echo $this->customlib->getCSRF(); ?>
                            <input type="hidden" name="id" value="<?php echo (int)$result['id'] ?>"/>
                            <div class="mb-3">
                                <span><a href="<?php echo html_escape(site_url() . $result['url']); ?>" target="_blank"><i class="fa fa-globe" aria-hidden="true"></i> <?php echo html_escape(site_url() . $result['url']); ?></a></span>
                            </div>
                            <div class="mb-3">
                                <label for="exampleInputEmail1"><?php echo $this->lang->line('title'); ?></label><small class="req"> *</small>
                                <input id="title" name="title" placeholder="" type="text" class="form-control"  value="<?php echo html_escape(set_value('title', $result['title'])); ?>" />
                                <span class="text-danger"><?php echo form_error('title'); ?></span>
                            </div>
                            <div class="dividerhr"></div>
                            <div class="mb-3">
                                <label class="form-label" for="email">
                                    <?php echo $this->lang->line('date'); ?>
                                </label><small class="req"> *</small>
                                <input class="form-control date" id="date" name="date" readonly="readonly" value="<?php echo set_value('start_date', date($this->customlib->getHospitalDateFormat())); ?>" type="text"/>
                                <span class="text-danger"><?php echo form_error('date'); ?></span>
                            </div>
                            <div class="dividerhr"></div>
                            <div class="formgroup10 form-group mb10"> <!-- Submit button !-->
                                <label for="exampleInputEmail1"><?php echo $this->lang->line('description'); ?></label><small class="req"> *</small>
                                <button type="button" class="btn btn-primary btn-sm float-end" id="media_images" data-bs-toggle="modal" data-bs-target="#mediaModal"><i class="fa fa-plus"></i>
                                    <?php echo $this->lang->line('add_media'); ?>
                                </button>
                            </div>
                            <div class="mb-3">
                                <textarea id="editor1" name="description" placeholder="" type="text" class="form-control ss">
                                    <?php echo set_value('description', $result['description']); ?>
                                </textarea>
                                <span class="text-danger"><?php echo form_error('description'); ?></span>
                            </div>
                            <div class="dividerhr"></div>
                        </div><!-- /.card-body -->
                    </div>
                    <div class="card">
                        <div class="card-header d-flex align-items-center">
                            <h3 class="card-title titlefix"><?php echo $this->lang->line('seo_detail'); ?></h3>
                            <div class="ms-auto d-flex gap-1">
                                <button type="button" class="btn btn-box-tool card-box-toggle" data-bs-target="#seoDetailBody"><i class="fa fa-plus"></i></button>
                            </div>
                        </div>
                        <div id="seoDetailBody" style="display: none;">
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="meta_title"><?php echo $this->lang->line('meta_title'); ?></label>
                                <input id="meta_title" name="meta_title" placeholder="" type="text" class="form-control" value="<?php echo set_value('meta_title', $result['meta_title']); ?>" />
                                <span class="text-danger"><?php echo form_error('meta_title'); ?></span>
                            </div>
                            <div class="mb-3">
                                <label for="meta_keywords"><?php echo $this->lang->line('meta_keyword'); ?></label>
                                <input id="meta_keywords" name="meta_keywords" placeholder="" type="text" class="form-control" value="<?php echo set_value('meta_keywords', $result['meta_keyword']); ?>" />
                                <span class="text-danger"><?php echo form_error('meta_keywords'); ?></span>
                            </div>
                            <div class="mb-3">
                                <label for="meta_description"><?php echo $this->lang->line('meta_description'); ?></label>
                                <textarea id="meta_description" name="meta_description" placeholder="" type="text" class="form-control"><?php echo set_value('meta_description', $result['meta_description']); ?></textarea>
                                <span class="text-danger"><?php echo form_error('meta_description'); ?></span>
                            </div>
                        </div>
                        </div><!-- /.collapse wrapper -->
                    </div>
                </div><!--/.col (right) -->
                <!-- left column -->
                <div class="col-md-3 col-sm-12">
                    <!-- page settings -->
                    <div class="card">
                        <div class="card-header d-flex align-items-center">
                            <h3 class="card-title titlefix"><?php echo $this->lang->line('sidebar_setting'); ?></h3>
                            <div class="ms-auto d-flex gap-1">
                                <button type="button" class="btn btn-box-tool card-box-toggle" data-bs-target="#sidebarSettingBody" title="<?= $this->lang->line('collapse') ?>"><i class="fa fa-minus"></i></button>
                            </div><!-- /.ms-auto d-flex gap-1 -->
                        </div><!-- /.card-header -->
                        <div id="sidebarSettingBody">
                        <div class="card-body">
                            <div class="mb-3 d-flex align-items-center justify-content-between">
                                <label for="sidebar"><?php echo $this->lang->line('sidebar'); ?></label>
                                <div class="form-check form-switch m-0">
                                    <input id="sidebar" name="sidebar" type="checkbox" role="switch" class="form-check-input chk" <?php echo set_checkbox('sidebar', '1', (set_value('sidebar', $result['sidebar']) == 1) ? true : false); ?> value="1" />
                                </div>
                            </div>
                        </div><!-- /.card-body -->
                        </div><!-- /.collapse wrapper -->
                    </div><!-- /.box -->
                    <!-- page image -->
                    <div class="card">
                        <div class="card-header d-flex align-items-center">
                            <h3 class="card-title titlefix"><?php echo $this->lang->line('featured_image'); ?></h3>
                            <div class="ms-auto d-flex gap-1">
                                <button type="button" class="btn btn-box-tool card-box-toggle" data-bs-target="#featuredImageBody" title="<?= $this->lang->line('collapse') ?>"><i class="fa fa-minus"></i></button>
                            </div><!-- /.ms-auto d-flex gap-1 -->
                        </div><!-- /.card-header -->
                        <div id="featuredImageBody">
                        <div class="card-body">
                            <div class="mb-3">
                                <div class="input-group input-group-sm">
                                    <input class="form-control iframe-btn" placeholder="<?php echo $this->lang->line('select_image'); ?>" type="text" name="image" id="image" value="<?php echo html_escape($result['feature_image']); ?>">
                                    <a href="#" class="btn btn-primary feture_image_btn" id="feture_image" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('select_image'); ?>"><i class="fa fa-folder-open"></i></a>
                                    <a href="#" class="btn btn-outline-secondary delete_media" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('delete'); ?>"><i class="fa fa-trash"></i></a>
                                </div>
                                <?php $image_display = ($result['feature_image'] != "") ? "" : "display: none"; ?>
                                <div id="image_preview" class="border rounded" style="<?php echo $image_display; ?>">
                                    <img src="<?php echo $this->media_storage->getImageURL($result['feature_image']); ?>" class="img-fluid feature_image_url">
                                </div>
                            </div>
                        </div><!-- /.card-body -->
                        </div><!-- /.collapse wrapper -->
                    </div><!-- /.card -->
                    <!-- Save button -->
                    <div class="card">
                        <div class="card-body d-grid">
                            <button type="submit" class="btn btn-primary btn-lg"><i class="fa fa-save me-2"></i><?php echo $this->lang->line('save'); ?></button>
                        </div><!-- /.card-body -->
                    </div><!-- /.box -->
                </div><!-- /.col-md-3 -->
    </div>
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
		
        $(document).on('click', '.feture_image_btn', function (event) {
            event.preventDefault();
            popup_target = 'feture_image';
            shModal('mediaModal').toggle();
        });

        $('#mediaModal').on('show.bs.modal', function (event) {
            if (event.relatedTarget) {
                popup_target = event.relatedTarget.id;
            }
            var button = $(event.relatedTarget) // Button that triggered the modal
            console.log(button);
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
            var is_image = $('div#media_div').find('.fadeoverlay.active').find('img').data('is_image');
            var content_name = $('div#media_div').find('.fadeoverlay.active').find('img').data('content_name');
            var content_type = $('div#media_div').find('.fadeoverlay.active').find('img').data('content_type');
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
            } else {
                if (is_image === 1) {
                    addImage(content_html);
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
        $('#image_preview').removeClass('d-none');
    }
	
    $(document).on('click', '.card-box-toggle', function () {
        var target = $(this).data('bs-target');
        $(target).slideToggle(200);
        $(this).find('i').toggleClass('fa-minus fa-plus');
    });

    $(document).on('click', '.delete_media', function () {
        $('.feature_image_url').attr('src', '');
        $('#image').val('');
        $('#image_preview').addClass('d-none');
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