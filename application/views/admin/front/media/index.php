<div class="row">
    <?php if ($this->rbac->hasPrivilege('media_manager', 'can_add')) { ?>
    <div class="col-md-12">
        <div class="card mb-3">
            <div class="card-header ptbnull d-flex align-items-center justify-content-between flex-wrap gap-2">
                <h5 class="card-title mb-0"><?php echo $this->lang->line('media_manager'); ?></h5>
            </div>
            <div class="card-body">
                <div class="row align-items-center g-3">
                    <div class="col-md-5">
                        <form method="post" action="#" id="fileupload">
                            <label class="form-label fw-semibold d-flex align-items-center gap-2 mb-2">
                                <i class="fa fa-cloud-upload front-text-link"></i>
                                <?php echo $this->lang->line('upload_your_file'); ?>
                            </label>
                            <div class="files">
                                <input type="file" name="files[]" class="filestyle form-control" id="file" multiple="">
                            </div>
                        </form>
                    </div>
                    <div class="col-md-2 text-center">
                        <div class="d-flex align-items-center gap-2 text-muted">
                            <hr class="flex-grow-1 m-0">
                            <span class="text-uppercase small fw-semibold"><?php echo $this->lang->line('or') ?: 'or'; ?></span>
                            <hr class="flex-grow-1 m-0">
                        </div>
                    </div>
                    <div class="col-md-5">
                        <form action="<?php echo site_url('admin/front/media/addVideo'); ?>" id="video_form" method="POST">
                            <label for="video_url" class="form-label fw-semibold d-flex align-items-center gap-2 mb-2">
                                <i class="fa fa-youtube-play text-danger"></i>
                                <?php echo $this->lang->line('upload_youtube_video'); ?> <small class="req">*</small>
                            </label>
                            <div class="input-group">
                                <input type="text" class="form-control" name="video_url" id="video_url" placeholder="<?php echo $this->lang->line('url') ?>">
                                <button type="submit" class="btn btn-primary video_submit" data-loading-text="<i class='fa fa-spinner fa-spin'></i> <?php echo $this->lang->line('loading'); ?>"><?php echo $this->lang->line('submit'); ?></button>
                            </div>
                            <span class="text-danger small file_error"></span>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php } ?>

    <div class="col-md-12">
        <div class="card" id="holist">
            <div class="card-header ptbnull d-flex align-items-center justify-content-between flex-wrap gap-2">
                <h5 class="card-title mb-0"><?php echo $this->lang->line('media_library') ?: 'Media Files'; ?></h5>
            </div>
            <div class="card-body">
                <div class="row g-3 mb-3">
                    <div class="col-sm-6">
                        <label class="form-label"><?php echo $this->lang->line('search_by_file_name'); ?></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fa fa-search"></i></span>
                            <input type="text" value="" class="form-control search_text" placeholder="<?php echo $this->lang->line('enter_keyword'); ?>">
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label"><?php echo $this->lang->line('filter_by_file_type'); ?></label>
                        <select class="form-control file_type">
                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                            <?php foreach ($mediaTypes as $type_key => $type_value) { ?>
                                <option value="<?php echo $type_value; ?>"><?php echo $type_value; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="media-file-count" id="media_count"></div>
                <div class="mediarow">
                    <div id="media_div"></div>
                </div>
                <div class="text-end mt-3" id="pagination_link"></div>
            </div>
        </div>
    </div>
</div>


<script type="text/javascript">
    $(document).ready(function () {
        load(1);
        $(document).on("click", ".pagination li a", function (event) {
            event.preventDefault();
            var page = $(this).data("ci-pagination-page");
            load(page);
        });

        $(".search_text").keyup(function () {
            load(1);
        });

        $(".file_type").change(function () {
            load(1);
        });

        /* #postdate init removed - auto-init via class + event delegation */

        modal_click_disabled('confirm-delete');

        $('#confirm-delete').on('show.bs.modal', function (e) {
            var record_id = $(e.relatedTarget).data('record_id');
            $('#record_id').val(0).val(record_id);
            $('.del_modal_title').html("<?php echo $this->lang->line('delete_confirm'); ?>");
            $('.del_modal_body').html('<p><?php echo $this->lang->line('are_you_sure_want_to_delete'); ?></p>');
        });

        $('#detail').on('show.bs.modal', function (e) {
            var data = $(e.relatedTarget).data();
            var isVideo = data.media_type == "video";
            var icon = isVideo ? 'fa-youtube-play text-danger' : 'fa-file-image-o text-primary';
            var typeBadge = isVideo ? 'badge bg-danger fw-medium mt-1' : 'badge bg-primary fw-medium mt-1';
            $('#detailModalIcon').attr('class', 'fa ' + icon);
            $('#detailModalLabel').text(data.media_name);
            $('#modal_media_name').text(data.media_name);
            $('#modal_media_type').attr('class', typeBadge).text(data.media_type);
            $('#modal_media_size').text(isVideo ? "<?php echo $this->lang->line('n_a'); ?>" : convertSize(data.media_size));
            $('#modal_media_path').text(data.source);
            $('#openMediaBtn').attr('href', data.source);
            $('#copyPathBtn').off('click').on('click', function () {
                navigator.clipboard.writeText(data.source).then(function () {
                    var $btn = $('#copyPathBtn');
                    $btn.find('i').attr('class', 'fa fa-check text-success me-1');
                    setTimeout(function () { $btn.find('i').attr('class', 'fa fa-copy me-1'); }, 1500);
                });
            });
            updateMediaDetailPopup(data.media_type, data.source, data.image);
        });

        function updateMediaDetailPopup(media_type, url, thumb_path) {
            var content_popup = "";
            if (media_type == "video") {
                var youtubeID = YouTubeGetID(url);
                content_popup = '<object data="https://www.youtube.com/embed/' + youtubeID + '" width="100%" height="400"></object>';
            } else {
                content_popup = '<img src="' + thumb_path + '" class="img-fluid">';
            }
            $('.popup_image').html("").html(content_popup);
        }

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

        $("#video_form").submit(function (e) {
            e.preventDefault();
            var url = $(this).attr("action");
            var $this = $('.video_submit');
            var formData = new FormData(this);
            var fileInput = document.getElementById('file');
            if (fileInput && fileInput.files.length > 0) {
                for (var i = 0; i < fileInput.files.length; i++) {
                    formData.append('files[]', fileInput.files[i]);
                }
            }
            $.ajax({
                type: "POST",
                url: url,
                dataType: 'json',
                cache: false,
                contentType: false,
                processData: false,
                data: formData,
                beforeSend: function () {
                    $this.button('loading');
                    $("[class$='_error']").html("");
                },
                success: function (data) {
                    if (!data.status) {
                        var message = "";
                        $.each(data.error, function (index, value) {
                            message += value;
                        });
                        errorMsg(message);
                    } else {
                        reset_form('#video_form');
                        $('#video_form').find(".dropify-clear").trigger('click');
                        $('#file').val('');
                        successMsg(data.msg);
                        load(1);
                    }
                },
                error: function (xhr) { $this.button('reset'); },
                complete: function () { $this.button('reset'); },
            });
        });
    });

    function load(page) {
        var keyword   = $('.search_text').val();
        var file_type = $('.file_type').val();
        var is_gallery = 0;
        $.ajax({
            url: "<?php echo base_url(); ?>admin/front/media/getPage/" + page,
            method: "GET",
            data: {'keyword': keyword, 'file_type': file_type, 'is_gallery': is_gallery},
            dataType: "json",
            beforeSend: function () { $('#media_div').empty(); $('#media_count').html(''); },
            success: function (data) {
                $('#media_div').empty();
                if (data.result_status === 1) {
                    $.each(data.result, function (index, value) {
                        $("#media_div").append(data.result[index]);
                    });
                    $('#pagination_link').html(data.pagination_link);
                    var n = $('#media_div .img_div_modal').length;
                    $('#media_count').html('<strong>' + n + '</strong> files found');
                } else {
                    $('#media_count').html('<strong>0</strong> files found');
                }
            },
        });
    }

    $(document).on('click', '.btn_delete', function () {
        var $this = $('.btn_delete');
        var record_id = $('#record_id').val();
        $.ajax({
            url: "<?php echo site_url('admin/front/media/deleteItem') ?>",
            type: "POST",
            data: {'record_id': record_id},
            dataType: 'Json',
            beforeSend: function () { $this.button('loading'); },
            success: function (data) {
                if (data.status === 1) {
                    successMsg(data.msg);
                    load(1);
                } else {
                    errorMsg(data.msg);
                }
                shModal('confirm-delete').hide();
            },
            complete: function () { $this.button('reset'); },
            error: function () {}
        });
    });

    function convertSize(bytes, decimalPoint) {
        if (bytes == 0) return '0 Bytes';
        var k = 1024,
            dm = decimalPoint || 2,
            sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'],
            i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
    }
</script>

<div class="modal fade sh-modal sh-modal-accent" id="detail" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title d-flex align-items-center gap-2">
                    <i id="detailModalIcon" class="fa fa-file-image-o"></i>
                    <span id="detailModalLabel" class="text-truncate front-detail-title-max"></span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="pup-scroll-area">
                <div class="modal-body p-0">
                    <div class="sh-media-dv-wrap">
                        <div class="sh-media-dv-preview popup_image"></div>
                        <div class="sh-media-dv-meta">
                            <div class="sh-media-dv-fields">
                                <div class="sh-info-item border-bottom">
                                    <span class="sh-info-label"><i class="fa fa-tag fa-fw me-1"></i><?php echo $this->lang->line('media_name'); ?></span>
                                    <span class="sh-info-value text-break" id="modal_media_name"></span>
                                </div>
                                <div class="row g-0">
                                    <div class="col-6 sh-info-item border-end">
                                        <span class="sh-info-label"><i class="fa fa-cube fa-fw me-1"></i><?php echo $this->lang->line('media_type'); ?></span>
                                        <span id="modal_media_type" class="badge bg-primary fw-medium mt-1"></span>
                                    </div>
                                    <div class="col-6 sh-info-item">
                                        <span class="sh-info-label"><i class="fa fa-hdd-o fa-fw me-1"></i><?php echo $this->lang->line('media_size'); ?></span>
                                        <span class="sh-info-value" id="modal_media_size"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="sh-media-dv-url">
                                <span class="sh-info-label"><i class="fa fa-link fa-fw me-1"></i><?php echo $this->lang->line('media_path'); ?></span>
                                <div class="sh-media-dv-url-box" id="modal_media_path"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div><!--./pup-scroll-area-->
            <div class="modal-footer">
                <button type="button" id="copyPathBtn" class="btn btn-sm btn-outline-secondary me-auto">
                    <i class="fa fa-copy me-1"></i><?php echo $this->lang->line('copy_url') ?: 'Copy URL'; ?>
                </button>
                <a id="openMediaBtn" href="#" target="_blank" class="btn btn-sm btn-outline-primary">
                    <i class="fa fa-external-link me-1"></i><?php echo $this->lang->line('open') ?: 'Open'; ?>
                </a>
                <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('close'); ?></button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade sh-modal sh-modal-accent" id="confirm-delete" tabindex="-1" aria-labelledby="confirmDeleteLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <input type="hidden" id="record_id" name="record_id" value="0">
            <div class="modal-header">
                <h5 class="modal-title del_modal_title" id="confirmDeleteLabel"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body del_modal_body"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                <a class="btn btn_delete btn-danger" data-loading-text="<i class='fa fa-spinner fa-spin'></i> Please Wait.."><?php echo $this->lang->line('delete'); ?></a>
            </div>
        </div>
    </div>
</div>
