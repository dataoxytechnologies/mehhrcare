<div class="card mb-3">
    <div class="card-header ptbnull">
        <h3 class="card-title titlefix"><?php echo $this->lang->line('upload_share_content'); ?></h3>
        <div class="d-flex gap-2 align-items-center flex-wrap float-end">
            <?php if ($this->rbac->hasPrivilege('upload_share_content', 'can_add')) { ?>
            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
                <i class="fa fa-cloud-upload me-1"></i> <?php echo $this->lang->line('upload'); ?>
            </button>
            <?php } ?>
        </div>
    </div>
</div>

<!-- Main layout -->
<div class="sh-cnt-layout">

    <!-- Content panel -->
    <div class="sh-cnt-panel">
        <div class="sh-cnt-toolbar">
            <div class="sh-cnt-search">
                <form method="POST" action="#" id="searchform" class="sh-display-contents">
                    <input type="text" name="table_search" class="post_search_text" placeholder="<?php echo $this->lang->line('search'); ?>…">
                    <button type="submit"><i class="fa fa-search"></i></button>
                </form>
            </div>
            <span id="sh-sel-pill"><span id="sh-sel-num">0</span> <?php echo $this->lang->line('files_selected'); ?></span>
            <div class="sh-cnt-vtoggle">
                <button class="sh-cnt-vbtn active" id="grid" title="<?php echo $this->lang->line('card_view'); ?>"><i class="fa fa-th"></i></button>
                <button class="sh-cnt-vbtn" id="list" title="<?php echo $this->lang->line('list_view'); ?>"><i class="fa fa-th-list"></i></button>
            </div>
        </div>
        <div class="sh-cnt-body">
            <div class="modal_loader_div" style="display:none;"></div>
            <form class="post-list"><input type="hidden" value=""></form>
            <div class="pagination-container"></div>
            <div class="pagination-nav mt-3"></div>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="sh-cnt-sidebar" id="sh-sidebar">

        <div class="sh-sb-preview" id="sh-sb-preview" onclick="shSbPreviewClick()">
            <!-- Default -->
            <div class="sh-sb-state-default">
                <div class="sh-sb-preview-icon fi-img sh-opacity-35"><i class="fa fa-cloud-upload"></i></div>
                <div class="sh-sb-hint"><?php echo $this->lang->line('upload'); ?></div>
            </div>
            <!-- Count -->
            <div class="sh-sb-state-count">
                <div class="sh-sb-count-icon"><i class="fa fa-copy"></i></div>
                <div class="sh-sb-count-badge" id="sh-sb-badge"></div>
                <div class="sh-sb-count-lbl"><?php echo $this->lang->line('files_selected'); ?></div>
            </div>
            <!-- File -->
            <div class="sh-sb-state-file">
                <div class="sh-sb-preview-icon fi-def" id="sh-sb-file-icon"><i class="fa fa-file" id="sh-sb-file-icon-i"></i></div>
                <div class="sh-sb-file-name" id="sh-sb-file-name"></div>
            </div>
        </div>

        <!-- Stats (always visible) -->
        <div class="sh-sb-stats">
            <div class="sh-sb-stat">
                <span class="sh-sb-stat-lbl"><?php echo $this->lang->line('total_documents'); ?></span>
                <span class="sh-sb-stat-val total_files"><?php echo $count->number; ?></span>
            </div>
            <div class="sh-sb-stat">
                <span class="sh-sb-stat-lbl"><?php echo $this->lang->line('size'); ?></span>
                <span class="sh-sb-stat-val total_size"><?php echo format_file_size($count->file_size); ?></span>
            </div>
        </div>

        <!-- Action buttons (shown in count/file state) -->
        <div class="sh-sb-actions">
            <?php if ($this->rbac->hasPrivilege('share_content', 'can_view')) { ?>
            <button type="button" class="btn btn-sm btn-outline-secondary flex-fill" id="share_files" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('share'); ?>">
                <i class="fa fa-share-alt"></i> <?php echo $this->lang->line('share'); ?>
            </button>
            <?php } ?>
            <?php if ($this->rbac->hasPrivilege('generate_url', 'can_view')) { ?>
            <button type="button" class="btn btn-sm btn-outline-secondary" id="share_url" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('generate_url'); ?>">
                <i class="fa fa-globe"></i>
            </button>
            <?php } ?>
            <?php if ($this->rbac->hasPrivilege('upload_share_content', 'can_delete')) { ?>
            <button class="btn btn-sm btn-outline-danger side_delete_btn" data-bs-toggle="modal" data-bs-target="#confirm-delete" title="<?php echo $this->lang->line('delete'); ?>">
                <i class="fa fa-trash"></i>
            </button>
            <?php } ?>
        </div>

        <!-- Edit form (file state only) -->
        <div class="sh-sb-edit">
            <div class="sh-sb-edit-hd"><?php echo $this->lang->line('file_name'); ?> &amp; <?php echo $this->lang->line('content_type'); ?></div>
            <form action="<?php echo site_url('admin/content/ajaxupdate'); ?>" method="POST" id="side_form">
                <input type="hidden" name="id" value="">
                <div class="mb-2">
                    <label class="form-label form-label-sm"><?php echo $this->lang->line('file_name'); ?> <small class="req">*</small></label>
                    <input type="text" class="form-control form-control-sm" value="" name="name">
                </div>
                <div class="mb-3">
                    <label class="form-label form-label-sm"><?php echo $this->lang->line('content_type'); ?> <small class="req">*</small></label>
                    <select id="side_content_type" name="content_type" class="form-select form-select-sm">
                        <option value=""><?php echo $this->lang->line('select'); ?></option>
                        <?php foreach ($content_types as $content_type_key => $content_type_value) { ?>
                        <option value="<?php echo $content_type_value->id; ?>"><?php echo $content_type_value->name; ?></option>
                        <?php } ?>
                    </select>
                </div>
                <button type="submit" class="btn btn-sm btn-primary w-100"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
            </form>
        </div>

    </div><!-- .sh-cnt-sidebar -->

</div><!-- .sh-cnt-layout -->


<!-- ═══════════════════ UPLOAD MODAL ═══════════════════ -->
<div id="addModal" class="modal fade sh-modal sh-modal-accent" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addModalLabel"><?php echo $this->lang->line('upload'); ?></h5>
                <button type="button" class="btn-close close_btn" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="fileupload" action="<?php echo site_url('admin/content/ajaxupload'); ?>" enctype="multipart/form-data">
                <div class="pup-scroll-area">
                    <div class="modal-body">
                        <div class="sh-form-card">
                            <div class="sh-card-header">
                                <span class="sh-card-header-title"><?php echo $this->lang->line('upload'); ?></span>
                            </div>
                            <div class="px-3 py-2">
                                <div class="row g-3">
                                    <div class="col-12 col-md-6">
                                        <label class="form-label form-label-sm" for="content_type"><?php echo $this->lang->line('content_type'); ?> <small class="req">*</small></label>
                                        <select id="content_type" name="content_type" class="form-select form-select-sm">
                                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                                            <?php foreach ($content_types as $content_type_key => $content_type_value) { ?>
                                            <option value="<?php echo $content_type_value->id; ?>"><?php echo $content_type_value->name; ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    <div class="col-12"><hr class="my-1"></div>
                                    <div class="col-12 col-sm-5">
                                        <label class="form-label form-label-sm"><?php echo $this->lang->line('upload_your_file'); ?></label>
                                        <input type="file" name="upload[]" class="filestyle" id="file" data-max-file-size="10M">
                                    </div>
                                    <div class="col-12 col-sm-2 text-center">
                                        <div class="orline"><span>or</span></div>
                                    </div>
                                    <div class="col-12 col-sm-5">
                                        <label class="form-label form-label-sm"><?php echo $this->lang->line('upload_youtube_video_link'); ?></label>
                                        <input type="text" name="url" class="form-control form-control-sm" id="url" placeholder="https://youtube.com/...">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('close'); ?></button>
                    <button type="submit" class="btn btn-info" id="load" data-loading-text="<i class='fa fa-spinner fa-spin'></i> <?php echo $this->lang->line('saving'); ?>"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ═══════════════════ GENERATE URL MODAL ═══════════════════ -->
<div id="shareURLModal" class="modal fade sh-modal sh-modal-accent" tabindex="-1" aria-labelledby="shareURLModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="shareURLModalLabel"><?php echo $this->lang->line('generate_url'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="shareurl" action="<?php echo site_url('admin/content/generate_url'); ?>" enctype="multipart/form-data">
                <div class="pup-scroll-area">
                    <div class="modal-body">
                        <div class="share_link_div displaynone mb-3">
                            <div class="input-group input-group-sm">
                                <a class="share_link form-control text-break small" href="#" target="_blank"></a>
                                <button type="button" class="btn btn-outline-secondary" onclick="copylink()" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('copy'); ?>"><i class="fa fa-copy"></i></button>
                            </div>
                        </div>
                        <div class="sh-form-card mb-3 from_content">
                            <div class="sh-card-header">
                                <span class="sh-card-header-title"><?php echo $this->lang->line('generate_url'); ?></span>
                            </div>
                            <div class="px-3 py-2">
                                <div class="mb-3">
                                    <label class="form-label form-label-sm"><?php echo $this->lang->line('title'); ?> <small class="req">*</small></label>
                                    <input id="url_title" name="title" type="text" class="form-control form-control-sm">
                                </div>
                                <div class="d-flex gap-3">
                                    <div class="flex-fill">
                                        <label class="form-label form-label-sm"><?php echo $this->lang->line('share_date'); ?> <small class="req">*</small></label>
                                        <input id="url_share_date" name="share_date" type="text" class="form-control form-control-sm date">
                                    </div>
                                    <div class="flex-fill">
                                        <label class="form-label form-label-sm"><?php echo $this->lang->line('valid_upto'); ?></label>
                                        <input id="url_valid_upto" name="valid_upto" type="text" class="form-control form-control-sm date">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                    <button type="submit" class="btn btn-info" id="load_url" data-loading-text="<i class='fa fa-spinner fa-spin'></i> <?php echo $this->lang->line('saving'); ?>"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('generate_url'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ═══════════════════ SHARE MODAL ═══════════════════ -->
<div id="shareModal" class="modal fade sh-modal sh-modal-accent" tabindex="-1" aria-labelledby="shareModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="shareModalLabel"><?php echo $this->lang->line('share_selected'); ?></h5>
                <button type="button" class="btn-close share_close_btn" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="share_form" action="<?php echo site_url('admin/content/share'); ?>" enctype="multipart/form-data">
                <div class="pup-scroll-area">
                    <div class="modal-body">
                        <div class="row g-1">
                            <div class="col-md-7">
                                <div class="sh-form-card h-100">
                                    <div class="sh-card-header">
                                        <span class="sh-card-header-title"><?php echo $this->lang->line('share_details'); ?></span>
                                    </div>
                                    <div class="px-3 py-2">
                                        <div class="mb-3">
                                            <label class="form-label form-label-sm"><?php echo $this->lang->line('title'); ?> <small class="req">*</small></label>
                                            <input autofocus id="share_title" name="title" type="text" class="form-control form-control-sm" value="" autocomplete="off">
                                        </div>
                                        <div class="d-flex gap-3 mb-3">
                                            <div class="flex-fill">
                                                <label class="form-label form-label-sm"><?php echo $this->lang->line('share_date'); ?> <small class="req">*</small></label>
                                                <input id="share_date" name="share_date" type="text" class="form-control form-control-sm date">
                                            </div>
                                            <div class="flex-fill">
                                                <label class="form-label form-label-sm"><?php echo $this->lang->line('valid_upto'); ?></label>
                                                <input id="upload_date" name="valid_upto" type="text" class="form-control form-control-sm date">
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label form-label-sm"><?php echo $this->lang->line('description'); ?></label>
                                            <textarea class="form-control form-control-sm" id="description" name="description" rows="3" autocomplete="off"></textarea>
                                        </div>
                                        <div>
                                            <p class="form-label form-label-sm fw-semibold mb-2"><?php echo $this->lang->line('selected_document'); ?></p>
                                            <div class="content_list_uploaded"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="sh-form-card h-100">
                                    <div class="sh-card-header">
                                        <span class="sh-card-header-title"><?php echo $this->lang->line('send_to'); ?></span>
                                    </div>
                                    <div class="px-3 py-2">
                                        <div class="d-flex gap-3 mb-3" id="input-type">
                                            <div class="form-check">
                                                <input class="form-check-input" value="group" name="send_to" type="radio" id="send_to_group" data-bs-target="#share_group" checked>
                                                <label class="form-check-label" for="send_to_group"><?php echo $this->lang->line('group'); ?></label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" value="individual" name="send_to" type="radio" id="send_to_individual" data-bs-target="#share_individual">
                                                <label class="form-check-label" for="send_to_individual"><?php echo $this->lang->line('individual'); ?></label>
                                            </div>
                                        </div>
                                        <div class="tab-content">
                                            <div id="share_group" class="tab-pane active">
                                                <div class="border rounded p-3 sh-mh-240 overflow-y-auto">
                                                    <div class="form-check mb-2">
                                                        <input class="form-check-input" type="checkbox" name="user[]" value="patient" id="chk_patient">
                                                        <label class="form-check-label fw-semibold" for="chk_patient"><?php echo $this->lang->line('patient'); ?></label>
                                                    </div>
                                                    <?php foreach ($roles as $role_key => $role_value) {
                                                        if ($role_value["name"] != 'Super Admin') { ?>
                                                    <div class="form-check mb-2">
                                                        <input class="form-check-input" type="checkbox" name="user[]" value="<?php echo $role_value['id']; ?>" id="chk_role_<?php echo $role_value['id']; ?>">
                                                        <label class="form-check-label fw-semibold" for="chk_role_<?php echo $role_value['id']; ?>"><?php echo $role_value['name']; ?></label>
                                                    </div>
                                                    <?php } } ?>
                                                </div>
                                            </div>
                                            <div id="share_individual" class="tab-pane">
                                                <div class="mb-2">
                                                    <div class="input-group input-group-sm">
                                                        <button class="btn btn-outline-secondary dropdown-toggle category-select-btn" type="button" id="category-selector-btn" data-bs-toggle="dropdown" aria-expanded="false">
                                                            <span class="category-label"><?php echo $this->lang->line('select'); ?></span>
                                                        </button>
                                                        <ul class="dropdown-menu" id="category-dropdown-menu">
                                                            <li><a class="dropdown-item" href="#" data-value="Patient"><?php echo $this->lang->line('patient'); ?></a></li>
                                                            <?php foreach ($roles as $role_key => $role_value) {
                                                                if ($role_value["name"] != 'Super Admin') { ?>
                                                            <li><a class="dropdown-item" href="#" data-value="Staff-<?php echo $role_value['id']; ?>"><?php echo html_escape($role_value['name']); ?></a></li>
                                                            <?php } } ?>
                                                        </ul>
                                                        <input type="hidden" name="selected_value" id="category-selector" value="">
                                                        <input type="text" value="" data-record="" data-email="" data-mobileno="" class="form-control" autocomplete="off" id="search-query">
                                                        <button class="btn btn-primary add-btn" type="button"><?php echo $this->lang->line('add') ?></button>
                                                    </div>
                                                    <div id="suggesstion-box" class="position-relative"></div>
                                                </div>
                                                <div class="border rounded p-2 individual-list-container">
                                                    <input type="text" name="SearchDualList" class="form-control form-control-sm mb-2" placeholder="<?php echo $this->lang->line('search') ?>...">
                                                    <ul class="list-group send_list"></ul>
                                                </div>
                                            </div>
                                            <div id="share_class" class="tab-pane">
                                                <div class="border rounded p-2 sh-mh-200 overflow-y-auto">
                                                    <p class="fw-semibold mb-2"><?php //echo $this->lang->line('section'); ?> <small class="req">*</small></p>
                                                    <ul class="list-group section_list listcheckbox"></ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary share_close_btn" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                    <button type="submit" class="btn btn-info" id="load_share" data-loading-text="<i class='fa fa-spinner fa-spin'></i> <?php echo $this->lang->line('saving'); ?>"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('send'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ═══════════════════ SINGLE DELETE ═══════════════════ -->
<div class="modal fade sh-modal sh-modal-confirm" id="single-delete" tabindex="-1" aria-labelledby="singleDeleteLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered sh-modal-max-380">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="confirm-icon-wrap sh-confirm-danger">
                    <i class="fa fa-exclamation-triangle"></i>
                </div>
                <h4 id="singleDeleteLabel"><?php echo $this->lang->line('confirm_delete'); ?></h4>
                <p><?php echo $this->lang->line('you_are_about_to_delete'); ?> <strong class="title"></strong><?php echo $this->lang->line('this_procedure_is_irreversible_do_you_want_to_proceed'); ?></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                <button type="button" class="btn btn-danger btn-ok" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i> <?php echo $this->lang->line('please_wait'); ?>"><i class="fa fa-trash me-1"></i><?php echo $this->lang->line('delete'); ?></button>
            </div>
        </div>
    </div>
</div>

<!-- ═══════════════════ BULK DELETE ═══════════════════ -->
<div class="modal fade sh-modal sh-modal-confirm" id="confirm-delete" tabindex="-1" aria-labelledby="confirmDeleteLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered sh-modal-max-380">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="confirm-icon-wrap sh-confirm-danger">
                    <i class="fa fa-exclamation-triangle"></i>
                </div>
                <h4 id="confirmDeleteLabel"><?php echo $this->lang->line('confirm_delete'); ?></h4>
                <p><?php echo $this->lang->line('you_are_about_to_delete'); ?></p>
                <div class="delete_files_list text-start sh-delete-files-list"></div>
                <p class="mb-0"><?php echo $this->lang->line('this_procedure_is_irreversible_do_you_want_to_proceed'); ?></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                <button type="button" class="btn btn-danger btn-ok" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i> <?php echo $this->lang->line('please_wait'); ?>"><i class="fa fa-trash me-1"></i><?php echo $this->lang->line('delete'); ?></button>
            </div>
        </div>
    </div>
</div>

<!-- ═══════════════════ FILE VIEWER ═══════════════════ -->
<div id="viewModel" class="modal fade" tabindex="-1" aria-hidden="true" data-bs-backdrop="false">
    <div class="modal-dialog modal-fullscreen m-0">
        <div class="modal-content border-0 h-100">
            <div class="modal-header border-0 d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <a href="#" data-bs-dismiss="modal" class="vm-back-btn me-2"><i class="fa fa-arrow-left"></i></a>
                    <span class="model_file_name"></span>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <a href="#" class="pdfdownload-icon" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('download'); ?>"><i class="fa fa-download"></i></a>
                    <button type="button" class="popupclose btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            </div>
            <div class="modal-body d-flex align-items-center justify-content-center object-video"></div>
        </div>
    </div>
</div>

<script type="text/javascript">
    let _grid_view = 1;
    var selected_data = [];
    var selected_sidebar_data = [];
    var branch_base_url = "<?php echo $this->customlib->getBaseUrl(); ?>";
    var share_by_side_panel = false;
    var files_selected = "<?php echo $this->lang->line('files_selected'); ?>";

    /* ── Sidebar icon helper ────────────────────────────────── */
    function shFileIconClass(fileType) {
        var imgTypes = ['jpg','jpeg','jpe','jp2','j2k','jpf','jpg2','jpx','jpm','mj2','mjp2','png','gif','tiff','tif','webp'];
        if (imgTypes.indexOf(fileType) > -1) return ['fi-img', 'fa-image'];
        var map = {
            'pdf':   ['fi-pdf', 'fa-file-pdf'],
            'doc':   ['fi-doc', 'fa-file-word'],
            'docx':  ['fi-doc', 'fa-file-word'],
            'xls':   ['fi-xls', 'fa-file-excel'],
            'xlsx':  ['fi-xls', 'fa-file-excel'],
            'ppt':   ['fi-doc', 'fa-file-powerpoint'],
            'pptx':  ['fi-doc', 'fa-file-powerpoint'],
            'video': ['fi-vid', 'fa-play-circle'],
            'mp4':   ['fi-vid', 'fa-play-circle'],
            'webm':  ['fi-vid', 'fa-play-circle'],
            '3gp':   ['fi-vid', 'fa-play-circle'],
            'zip':   ['fi-zip', 'fa-file-archive'],
            'rar':   ['fi-zip', 'fa-file-archive'],
        };
        return map[fileType] || ['fi-def', 'fa-file'];
    }

    /* ── Sidebar state ──────────────────────────────────────── */
    function shUpdateSidebar() {
        var $sb    = $('#sh-sidebar');
        var $pill  = $('#sh-sel-pill');
        var $num   = $('#sh-sel-num');
        var $badge = $('#sh-sb-badge');

        $sb.removeClass('state-count state-file');

        if (share_by_side_panel && selected_sidebar_data.length > 0) {
            $sb.addClass('state-file');
            var f  = selected_sidebar_data[0];
            var ic = shFileIconClass(f.filetype || '');
            $('#sh-sb-file-icon').attr('class', 'sh-sb-preview-icon ' + ic[0]);
            $('#sh-sb-file-icon-i').attr('class', 'fa ' + ic[1]);
            $('#sh-sb-file-name').text(f.filename || '');
            $pill.hide();
        } else if (selected_data.length > 0) {
            $sb.addClass('state-count');
            var n = selected_data.length;
            $badge.text(n + ' ' + (n === 1 ? 'file' : 'files') + ' selected');
            $num.text(n);
            $pill.show();
        } else {
            $badge.text('');
            $pill.hide();
        }
    }

    /* ── Sidebar preview click ──────────────────────────────── */
    function shSbPreviewClick() {
        var sb = document.getElementById('sh-sidebar');
        if (!sb.classList.contains('state-count') && !sb.classList.contains('state-file')) {
            shModal('addModal').show();
        }
    }

    /* ── Checkbox change ────────────────────────────────────── */
    $(document).on('change', '.share_checkbox, .share_checkbox_list', function(){
        share_by_side_panel = false;
        if ($(this).is(':checked')) {
            $('.pagination-container').find('input.share_checkbox[value='+$(this).val()+']').prop('checked', true);
            $('.pagination-container').find('input.share_checkbox_list[value='+$(this).val()+']').prop('checked', true);
            var $div = $(this).closest('.top_list_div');
            checkbox_selected('add', $(this).val(), $(this).data('real_name'), $div.data('path')||'', $div.data('name')||'', $div.data('fileType')||'');
        } else {
            $('.pagination-container').find('input.share_checkbox[value='+$(this).val()+']').prop('checked', false);
            $('.pagination-container').find('input.share_checkbox_list[value='+$(this).val()+']').prop('checked', false);
            checkbox_selected('remove', $(this).val());
        }
        shUpdateSidebar();
    });

    function checkbox_selected(action, recid, filename, filepath, uploadname, filetype) {
        filename  = filename  || "";
        filepath  = filepath  || "";
        uploadname= uploadname|| "";
        filetype  = filetype  || "";
        if (action === "add") {
            selected_data.push({ id: recid, filename: filename, filepath: filepath, uploadname: uploadname, filetype: filetype });
        } else if (action === "remove") {
            $.each(selected_data, function(i, el) {
                if (this.id == recid) { selected_data.splice(i, 1); }
            });
        }
    }

    /* ── View toggle ────────────────────────────────────────── */
    $(document).ready(function() {
        $('#grid').click(function(e){
            e.preventDefault();
            _grid_view = 1;
            $('#grid').addClass('active');
            $('#list').removeClass('active');
            posts.init();
        });
        $('#list').click(function(e){
            e.preventDefault();
            _grid_view = 0;
            $('#list').addClass('active');
            $('#grid').removeClass('active');
            posts.init();
        });
    });

    /* ── Card click (single select) ─────────────────────────── */
    $(document).on('click', '.top_list_div', function(event){
        var $target = $(event.target);
        if ($target.is("input:checkbox[name='share_checkbox[]']") || $target.parent().hasClass('delete_file') || $target.hasClass('delete_file')) return;
        clearcheckbox();
        update_sidebar($(this));
    });

    function update_sidebar(selector) {
        var content_div  = selector.closest('div.top_list_div');
        var content_data = content_div.data();
        $('.side_delete_btn').data('recordId', content_data.recordId);
        $('.side_delete_btn').data('name', content_data.real_name);
        $('#side_form input[name="name"]').val(content_data.real_name);
        $('#side_form input[name="id"]').val(content_data.recordId);
        $('#side_form select option[value="' + content_data.typeId + '"]').prop("selected", true);
        selected_sidebar_data = [];
        share_by_side_panel = true;
        selected_sidebar_data.push({
            id:         content_data.recordId,
            filename:   content_data.real_name,
            filepath:   content_data.path     || '',
            uploadname: content_data.name     || '',
            filetype:   content_data.fileType || ''
        });
        shUpdateSidebar();
    }

    /* ── List row click ─────────────────────────────────────── */
    $(document).on("click", '.table_contents tbody tr', function(event){
        var $target = $(event.target);
        if ($target.is("input:checkbox[name='share_checkbox[]']") || $target.hasClass('sh-btn-icon') || $target.closest('.sh-btn-icon').length) return;
        clearcheckbox();
        var content_data  = $(this).data();
        $('.side_delete_btn').data('recordId', content_data.recordId);
        $('.side_delete_btn').data('name', content_data.real_name);
        $('#side_form input[name="name"]').val(content_data.real_name);
        $('#side_form input[name="id"]').val(content_data.recordId);
        $('#side_form select option[value="' + content_data.typeId + '"]').prop("selected", true);
        selected_sidebar_data = [];
        share_by_side_panel = true;
        selected_sidebar_data.push({
            id:         content_data.recordId,
            filename:   content_data.real_name,
            filepath:   content_data.path     || '',
            uploadname: content_data.name     || '',
            filetype:   content_data.fileType || ''
        });
        shUpdateSidebar();
    });

    /* ── Div image viewer ───────────────────────────────────── */
    $(document).on('click', '.div_image', function(){
        var content_div      = $(this).closest('div.top_list_div');
        var fileType         = content_div.data('fileType');
        var real_name        = content_div.data('real_name');
        var file_upload_name = content_div.data('name');
        var filepath         = content_div.data('path');
        var recordId         = content_div.data('recordId');
        $('.pdfdownload-icon').attr('href', branch_base_url + "admin/content/download_content/" + recordId);
        $('.model_file_name').text(real_name);
        var modal_view = false;

        if (['jpg','jpeg','png','svg','webp','gif'].indexOf(fileType) > -1) {
            modal_view = true;
            var img = $('<img />', { class:'img-fluid', src: branch_base_url+filepath+file_upload_name, alt: real_name, style:'max-height:calc(100vh - 80px);max-width:100%;object-fit:contain;' });
            $('#viewModel .modal-body').html(img);
        } else if (fileType === "pdf") {
            modal_view = true;
            var pdf = $('<embed />', { src: branch_base_url+filepath+file_upload_name+"#toolbar=0", type:'application/pdf', style:'width:100%;height:calc(100vh - 80px);border:0;' });
            $('#viewModel .modal-body').html(pdf);
        } else if (['mp4','webm','3gp','m4a'].indexOf(fileType) > -1) {
            modal_view = true;
            var video = $('<video />', { src: branch_base_url+filepath+file_upload_name, controls:'controls', style:'max-width:100%;max-height:calc(100vh - 80px);' });
            $('#viewModel .modal-body').html(video);
        } else if (fileType === "video") {
            modal_view = true;
            var youtubeID = YouTubeGetID(file_upload_name);
            $('#viewModel .modal-body').html('<div class="ratio ratio-16x9" style="max-width:900px;width:100%;"><iframe src="https://www.youtube.com/embed/'+youtubeID+'" allowfullscreen frameborder="0"></iframe></div>');
        }
        if (modal_view) { shModal('viewModel').show(); }
    });

    function YouTubeGetID(url) {
        var ID = '';
        url = url.replace(/(>|<)/gi,'').split(/(vi\/|v=|\/v\/|youtu\.be\/|\/embed\/)/);
        if (url[2] !== undefined) { ID = url[2].split(/[^0-9a-z_\-]/i); ID = ID[0]; } else { ID = url; }
        return ID;
    }

    /* ── Share / URL modal content list ────────────────────── */
    $(document).on('click','#share_files',function(){
        form_content_list();
        shModal('shareModal').show();
    });
    $(document).on('click','#share_url',function(){
        form_content_list();
        shModal('shareURLModal').show();
    });

    function form_content_list(){
        var new_selected = share_by_side_panel ? selected_sidebar_data : selected_data;
        var imgExts = ['jpg','jpeg','png','gif','svg','webp'];
        var sub_ul = $('<ul/>',{'class':'list-group flex-column w-100 content-share-list'});
        $.each(new_selected, function(key, input) {
            var ext   = (input.uploadname||input.filename||'').split('.').pop().toLowerCase();
            var isImg = imgExts.indexOf(ext) > -1;
            var li    = $('<li/>',{'class':'list-group-item d-flex align-items-center gap-2 py-2'});
            if (isImg && input.filepath && input.uploadname) {
                li.append($('<img/>',{src: branch_base_url+input.filepath+input.uploadname, style:'width:36px;height:36px;object-fit:cover;border-radius:4px;flex-shrink:0;'}));
            } else {
                li.append($('<i/>',{'class':'fa fa-file-o text-muted','style':'font-size:20px;width:36px;text-align:center;flex-shrink:0;'}));
            }
            li.append($('<span/>',{'class':'flex-grow-1 text-truncate small'}).text(input.filename));
            li.append($('<a/>',{href:branch_base_url+'admin/content/download_content/'+input.id,'class':'btn btn-sm btn-outline-secondary flex-shrink-0','data-bs-toggle':'tooltip',title:'<?php echo $this->lang->line("download"); ?>'}).append($('<i/>',{'class':'fa fa-download'})));
            sub_ul.append(li);
        });
        $(".content_list_uploaded").html(sub_ul);
        $('[data-bs-toggle="tooltip"]',$(".content_list_uploaded")).tooltip();
    }

    /* ── Share form submit ──────────────────────────────────── */
    $(document).on('submit','form#share_form',function(e){
        e.preventDefault();
        var form     = $(this);
        var formData = new FormData();
        var user_list= (!jQuery.isEmptyObject(attr)) ? JSON.stringify(attr) : "";
        $(this).serializeArray().forEach(function(inp){ formData.append(inp.name, inp.value); });
        formData.append('user_list', user_list);
        var new_selected = share_by_side_panel ? selected_sidebar_data : selected_data;
        $.each(new_selected, function(k,inp){ formData.append('selected_contents[]', inp.id); });
        var $this = form.find("button[type=submit]:focus");
        $this.btnLoading();
        $.ajax({
            url: form.attr('action'), type:"POST", data:formData, dataType:'json',
            contentType:false, cache:false, processData:false,
            beforeSend:function(){ $this.btnLoading(); },
            success:function(data){
                if (!data.status) { var msg=""; $.each(data.error,function(i,v){msg+=v;}); errorMsg(msg); }
                else { reset_share_form(); successMsg(data.msg); shModal('shareModal').hide(); }
            },
            error:function(){ alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>"); $this.btnReset(); },
            complete:function(){ $this.btnReset(); }
        });
    });

    /* ── URL form submit ────────────────────────────────────── */
    $(document).on('submit','form#shareurl',function(e){
        e.preventDefault();
        var form     = $(this);
        var formData = new FormData();
        var user_list= (!jQuery.isEmptyObject(attr)) ? JSON.stringify(attr) : "";
        $(this).serializeArray().forEach(function(inp){ formData.append(inp.name, inp.value); });
        var new_selected = share_by_side_panel ? selected_sidebar_data : selected_data;
        $.each(new_selected, function(k,inp){ formData.append('selected_contents[]', inp.id); });
        var $this = form.find("button[type=submit]:focus");
        $this.btnLoading();
        $.ajax({
            url: form.attr('action'), type:"POST", data:formData, dataType:'json',
            contentType:false, cache:false, processData:false,
            beforeSend:function(){ $this.btnLoading(); },
            success:function(data){
                if (!data.status) { var msg=""; $.each(data.error,function(i,v){msg+=v;}); errorMsg(msg); }
                else {
                    reset_share_form();
                    successMsg(data.msg);
                    $('.from_content').fadeOut(400);
                    $('.share_link').attr('href',data.shared_url).text(data.shared_url);
                    $('.share_link_div').removeClass('displaynone').show();
                    $('.url_content_list').html("");
                }
            },
            error:function(){ alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>"); $this.btnReset(); },
            complete:function(){ $this.btnReset(); }
        });
    });

    /* ── Side form submit ───────────────────────────────────── */
    $(document).on('submit','form#side_form',function(e){
        e.preventDefault();
        var form  = $(this);
        var $this = form.find("button[type=submit]:focus");
        $this.btnLoading();
        $.ajax({
            url: form.attr('action'), type:"POST", data:new FormData(this), dataType:'json',
            contentType:false, cache:false, processData:false,
            beforeSend:function(){ $this.btnLoading(); },
            success:function(data){
                if (!data.status) { var msg=""; $.each(data.error,function(i,v){msg+=v;}); errorMsg(msg); }
                else { successMsg(data.msg); posts.init(); }
            },
            error:function(){ alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>"); $this.btnReset(); },
            complete:function(){ $this.btnReset(); }
        });
    });

    /* ── Upload form submit ─────────────────────────────────── */
    $(document).on('submit','form#fileupload',function(e){
        e.preventDefault();
        var form  = $(this);
        var $this = form.find("button[type=submit]:focus");
        $this.btnLoading();
        $.ajax({
            url: form.attr('action'), type:"POST", data:new FormData(this), dataType:'json',
            contentType:false, cache:false, processData:false,
            beforeSend:function(){ $this.btnLoading(); },
            success:function(data){
                if (!data.status) { var msg=""; $.each(data.error,function(i,v){msg+=v;}); errorMsg(msg); }
                else {
                    $('.total_files').text(data.file_count);
                    $('.total_size').text(data.file_size);
                    successMsg(data.msg);
                    posts.init();
                    shModal('addModal').hide();
                }
            },
            error:function(){ alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>"); $this.btnReset(); },
            complete:function(){ $this.btnReset(); }
        });
    });

    /* ── Single delete ──────────────────────────────────────── */
    $('#single-delete').on('show.bs.modal', function(e){
        var data = $(e.relatedTarget).data();
        $('.title', this).text(data.name);
        $('.btn-ok', this).data('recordId', data.recordId);
    });
    $(document).on('click','#single-delete .btn-ok',function(e){
        var delete_btn = $(this);
        var id = $(this).data('recordId');
        $.ajax({
            type:"POST", url: branch_base_url+"admin/content/delete", data:{'id':id}, dataType:"json",
            beforeSend:function(){ delete_btn.btnLoading(); },
            success:function(){ window.location.reload(); },
            error:function(){ alert("<?php echo $this->lang->line('error_occured_please_try_again'); ?>"); delete_btn.btnReset(); },
            complete:function(){ delete_btn.btnReset(); }
        });
    });

    /* ── Bulk delete ────────────────────────────────────────── */
    $('#confirm-delete').on('show.bs.modal', function(e){
        var new_selected = share_by_side_panel ? selected_sidebar_data : selected_data;
        var delete_ul = $('<ul/>');
        $.each(new_selected, function(key, input){
            $('<li/>').append($('<a>').attr('href','javascript:void(0)').text(input.filename)).addClass('ui-menu-item').attr('role','menuitem').appendTo(delete_ul);
        });
        $(".delete_files_list").html(delete_ul);
    });
    $(document).on('click','#confirm-delete .btn-ok',function(e){
        var delete_selected = share_by_side_panel ? selected_sidebar_data : selected_data;
        var ids = [];
        $.each(delete_selected, function(i,el){ ids.push(el.id); });
        var delete_btn = $(this);
        $.ajax({
            type:"POST", url: branch_base_url+"admin/content/delete_array", data:{'id':ids}, dataType:"json",
            beforeSend:function(){ delete_btn.btnLoading(); },
            success:function(){ window.location.reload(); },
            error:function(){ alert("<?php echo $this->lang->line('error_occured_please_try_again'); ?>"); delete_btn.btnReset(); },
            complete:function(){ delete_btn.btnReset(); }
        });
    });

    /* ── Reset share form ───────────────────────────────────── */
    function reset_share_form() {
        reset_form('#share_form');
        $('#shareModal .tab-pane').removeClass('active show');
        $('#share_group').addClass('active show');
        $("input:radio[value='group']").prop('checked', true);
        $('ul.send_list').html('');
        $('ul.section_list').html('');
        selected_sidebar_data = [];
        share_by_side_panel = false;
        selected_data = [];
        $('.pagination-container').find('input.share_checkbox').prop('checked', false);
        $('.pagination-container').find('input.share_checkbox_list').prop('checked', false);
        shUpdateSidebar();
    }

    function clearcheckbox() {
        selected_data = [];
        $('.pagination-container').find('input.share_checkbox').prop('checked', false);
        $('.pagination-container').find('input.share_checkbox_list').prop('checked', false);
    }

    /* ── Modal cleanup ──────────────────────────────────────── */
    $('#viewModel').on('hidden.bs.modal', function(){ $('#viewModel .modal-body').html(""); });
    $('#shareURLModal').on('hidden.bs.modal', function(){
        $('.from_content').css("display","block");
        $('.share_link_div').hide().addClass('displaynone');
        $('.share_link').attr('href',"").text("");
        reset_form('#shareurl');
    });
    $('#addModal').on('show.bs.modal', function(){
        var $input = $('.filestyle','#addModal');
        if (!$input.parent().hasClass('dropify-wrapper')) {
            var drEvent = $input.dropify();
        }
    });
    $('#addModal').on('hidden.bs.modal', function(){
        reset_form('#fileupload');
        $('#fileupload').find(".dropify-clear").trigger('click');
    });
    $('#shareModal').on('hidden.bs.modal', function(){
        attr = {};
        reset_form('#share_form');
        $('#shareModal .tab-pane').removeClass('active show');
        $('#share_group').addClass('active show');
        $("input:radio[value='group']").prop('checked', true);
        $(".send_list").empty();
        $(".section_list").empty();
    });
    $('.close_btn').click(function(){ $(".dropify-clear").trigger("click"); });

    /* ── Share tab toggle ───────────────────────────────────── */
    $('input[name="send_to"]').click(function(){
        var target = $(this).attr('data-bs-target');
        $('#shareModal .tab-pane').removeClass('active show');
        $(target).addClass('active show');
    });

    /* ── Individual search in share modal ───────────────────── */
    $(document).ready(function(){
        modal_click_disabled('shareModal', 'addModal', 'confirm-delete', 'shareURLModal', 'viewModel');

        $(document).on('click','#category-dropdown-menu .dropdown-item',function(e){
            e.preventDefault();
            $('#category-selector').val($(this).data('value'));
            $('#category-selector-btn .category-label').text($(this).text());
        });

        $('[name="SearchDualList"]').keyup(function(e){
            var code = e.keyCode || e.which;
            if (code == '9') return;
            if (code == '27') $(this).val(null);
            var $rows = $(this).closest('.individual-list-container').find('.list-group li');
            var val   = $.trim($(this).val()).replace(/ +/g,' ').toLowerCase();
            $rows.show().filter(function(){
                return !~$(this).text().replace(/\s+/g,' ').toLowerCase().indexOf(val);
            }).hide();
        });
    });

    function escHtml(str) {
        var d = document.createElement('div');
        d.appendChild(document.createTextNode(str || ''));
        return d.innerHTML;
    }

    $(document).on('keyup','#search-query',function(){
        $("#search-query").attr('data-record',"").attr('data-email',"").attr('data-mobileno',"");
        $("#suggesstion-box").hide();
        var category_selected = $("[name='selected_value']").val();
        if (!category_selected) return;
        var arr = category_selected.split('-');
        var category_set = arr[0];
        $.ajax({
            type:"POST", url:"<?php echo site_url('admin/mailsms/search') ?>",
            data:{'keyword':$(this).val(),'category':category_selected}, dataType:'JSON',
            beforeSend:function(){ $("#search-query").css("background","#FFF url(../../backend/images/loading.gif) no-repeat 165px"); },
            success:function(data){
                if (data.length > 0) {
                    setTimeout(function(){
                        $("#suggesstion-box").show();
                        var cList = $('<ul/>').addClass('selector-list');
                        $.each(data, function(i,obj){
                            var email, contact, name;
                            if (category_set === "Staff") {
                                email = obj.email; contact = obj.phone;
                                name = obj.name+' '+obj.surname+' ('+obj.employee_id+')';
                            } else if (category_set === "Patient") {
                                email = obj.email; contact = obj.mobileno;
                                name = obj.patient_name+' ('+obj.patient_unique_id+')';
                            }
                            $('<li/>').addClass('ui-menu-item').attr('category',category_set).attr('record_id',obj.id).attr('email',email).attr('mobileno',contact).attr('app_key',obj.app_key).text(name).appendTo(cList);
                        });
                        $("#suggesstion-box").html(cList);
                        $("#search-query").css("background","#FFF");
                    },1000);
                } else { $("#suggesstion-box").hide(); $("#search-query").css("background","#FFF"); }
            }
        });
    });

    $(document).on('click','.selector-list li',function(){
        var val = $(this).text();
        $("#search-query").attr('value',val).val(val).attr('data-record',$(this).attr('record_id')).attr('data-email',$(this).attr('email')).attr('data-mobileno',$(this).attr('mobileno'));
        $("#suggesstion-box").hide();
    });

    var attr = {};
    $(document).on('click','.add-btn',function(){
        var value     = $("#search-query").val();
        var record_id = $("#search-query").attr('data-record');
        var email     = $("#search-query").attr('data-email');
        var mobileno  = $("#search-query").attr('data-mobileno');
        var app_key   = $("#search-query").attr('data-app_key');
        var category_selected = $("[name='selected_value']").val();
        var word = (category_selected||'').split('-')[0];
        if (record_id && category_selected) {
            var chkexists = checkRecordExists(category_selected+"-"+record_id);
            if (chkexists) {
                attr[category_selected+"-"+record_id] = [{'category':word,'record_id':record_id,'email':email,'mobileno':mobileno,'app_key':app_key}];
                $("#search-query").attr('value',"").val("").attr('data-record',"");
                $(".send_list").append('<li class="list-group-item" id="'+escHtml(category_selected)+'-'+escHtml(record_id)+'"><i class="fa fa-user"></i> <span>'+escHtml(value)+' ('+escHtml(word)+')</span> <i class="fa fa-trash float-end text-danger" title="<?php echo $this->lang->line('delete'); ?>" onclick="delete_record(\''+escHtml(category_selected)+'-'+escHtml(record_id)+'\')"></i></li>');
            } else { errorMsg('<?php echo $this->lang->line('record_already_exists') ?>'); }
        } else { errorMsg("<?php echo $this->lang->line('message_to') . " field is required" ?>"); }
    });

    function checkRecordExists(find) { return !(find in attr); }
    function delete_record(record) { delete attr[record]; $('#'+record).remove(); return false; }

    function copylink(){
        var text = ($('a.share_link').text() || '').trim();
        if (!text) return;
        var $icon = $('.share_link_div button i');
        var orig  = $icon.attr('class');
        var done  = function(ok){
            $icon.attr('class', ok ? 'fa fa-check text-success' : 'fa fa-times text-danger');
            setTimeout(function(){ $icon.attr('class', orig); }, 1500);
        };
        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(text)
                .then(function(){ done(true); })
                .catch(function(){ copylinkExec(text, done); });
        } else {
            copylinkExec(text, done);
        }
    }

    function copylinkExec(text, cb){
        var $temp = $('<textarea>').css({position:'fixed', top:0, left:0, opacity:0}).val(text);
        $('body').append($temp);
        $temp[0].select();
        var ok = false;
        try { ok = document.execCommand('copy'); } catch(e) {}
        $temp.remove();
        if (cb) cb(ok);
    }
</script>

<script type="text/javascript">
var app = {
    Posts: function(){
        this.init = function(){ this.get_items_pagination(); };
        this.get_items_pagination = function(){
            _this = this;
            if ($('form.post-list input').val()) {
                var data = JSON.parse($('form.post-list input').val());
                _this.ajax_get_items_pagination(data.page);
            } else {
                _this.ajax_get_items_pagination(1,'name');
            }
            $(document).on('submit','form#searchform',function(e){
                e.preventDefault();
                _this.ajax_get_items_pagination(1);
            });
            $(document).on('click','.pagination-nav .pagination li.unactive',function(){
                _this.ajax_get_items_pagination($(this).attr('p'));
            });
        };
        this.ajax_get_items_pagination = function(page){
            if ($(".pagination-container").length) {
                var post_data = { page:page, search:$('.post_search_text').val(), grid:_grid_view };
                $('form.post-list input').val(JSON.stringify(post_data));
                var data = { data: JSON.parse($('form.post-list input').val()) };
                $.each(selected_data, function(key,input){ data['selected_content['+key+']'] = input.id; });
                $.ajax({
                    url: branch_base_url+'admin/content/getuploaddata',
                    type:'POST', data:data, dataType:'JSON',
                    beforeSend:function(){ $('.modal_loader_div').css("display","block"); },
                    success:function(response){
                        $(".pagination-container").html(response.content);
                        $('.pagination-nav').html(response.navigation);
                        $('.modal_loader_div').fadeOut(400);
                    },
                    error:function(){ alert("<?php echo $this->lang->line('error_occured_please_try_again'); ?>"); $('.modal_loader_div').fadeOut(400); },
                    complete:function(){ $('.modal_loader_div').fadeOut(400); }
                });
            }
        };
    }
};

jQuery(document).ready(function(){
    posts = new app.Posts();
    posts.init();
});
</script>
