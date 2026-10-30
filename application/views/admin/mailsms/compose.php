<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header ptbnull d-flex align-items-center justify-content-between flex-wrap gap-2">
                <h3 class="card-title titlefix mb-0"><?php echo $this->lang->line('send_sms'); ?></h3>
                <ul class="nav nav-pills sh-segmented-tabs">
                    <li class="nav-item"><a class="nav-link active" href="#tab_group" data-bs-toggle="tab"><i class="fa fa-users"></i> <?php echo $this->lang->line('group'); ?></a></li>
                    <li class="nav-item"><a class="nav-link" href="#tab_perticular" data-bs-toggle="tab"><i class="fa fa-user"></i> <?php echo $this->lang->line('individual'); ?></a></li>
                </ul>
            </div>

            <div class="card-body">
                <div class="tab-content">

                    <!-- Group Tab -->
                    <div class="tab-pane active" id="tab_group">
                        <form action="<?php echo site_url('admin/mailsms/send_group_sms') ?>" method="post" id="group_form">
                            <div class="row g-3">
                                <div class="col-md-8">
                                    <div class="mb-3">
                                        <label><?php echo $this->lang->line('title'); ?> <small class="req">*</small></label>
                                        <input class="form-control" name="group_title">
                                    </div>
                                    <div class="mb-3">
                                        <label><?php echo $this->lang->line('template_id'); ?></label>
                                        <input class="form-control" name="group_template_id">
                                        <div class="form-text text-danger"><?php echo $this->lang->line('this_field_require_for_smsgateway'); ?></div>
                                    </div>
                                    <div class="mb-3">
                                        <label><?php echo $this->lang->line('send_through'); ?> <small class="req">*</small></label>
                                        <div class="d-flex gap-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value="sms" name="group_send_by[]" id="grp_sms">
                                                <label class="form-check-label" for="grp_sms"><?php echo $this->lang->line('sms'); ?></label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value="push" name="group_send_by[]" id="grp_push">
                                                <label class="form-check-label" for="grp_push"><?php echo $this->lang->line('mobile_app'); ?></label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label><?php echo $this->lang->line('message'); ?> <small class="req">*</small></label>
                                        <textarea id="group_msg_text" name="group_message" class="form-control compose-textarea" rows="10"><?php echo set_value('message'); ?></textarea>
                                        <div class="d-flex justify-content-end mt-1">
                                            <small class="text-secondary tot_count_group_msg_text word_counter"><?php echo $this->lang->line('character_count'); ?>: 0</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label><?php echo $this->lang->line('message_to'); ?> <small class="req">*</small></label>
                                    <div class="sh-form-card p-3">
                                        <div class="form-check py-1">
                                            <input class="form-check-input" type="checkbox" name="user[]" value="patient" id="chk_patient">
                                            <label class="form-check-label" for="chk_patient"><?php echo $this->lang->line('patient'); ?></label>
                                        </div>
                                        <?php foreach ($roles as $role_key => $role_value) { ?>
                                        <div class="form-check py-1">
                                            <input class="form-check-input" type="checkbox" name="user[]" value="<?php echo $role_value['id']; ?>" id="chk_role_<?php echo $role_value['id']; ?>">
                                            <label class="form-check-label" for="chk_role_<?php echo $role_value['id']; ?>"><?php echo $role_value['name']; ?></label>
                                        </div>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-end mt-3 pt-3 border-top">
                                <button type="submit" class="btn btn-primary submit_group" data-loading-text="<i class='fa fa-spinner fa-spin'></i> <?php echo $this->lang->line('sending'); ?>">
                                    <i class="fa fa-paper-plane me-1"></i><?php echo $this->lang->line('send'); ?>
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Individual Tab -->
                    <div class="tab-pane" id="tab_perticular">
                        <form action="<?php echo site_url('admin/mailsms/send_individual_sms') ?>" method="post" id="individual_form">
                            <div class="row g-3">
                                <div class="col-md-8">
                                    <div class="mb-3">
                                        <label><?php echo $this->lang->line('title'); ?> <small class="req">*</small></label>
                                        <input class="form-control" name="individual_title">
                                    </div>
                                    <div class="mb-3">
                                        <label><?php echo $this->lang->line('send_through'); ?> <small class="req">*</small></label>
                                        <div class="d-flex gap-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="individual_send_by[]" value="sms" id="ind_sms">
                                                <label class="form-check-label" for="ind_sms"><?php echo $this->lang->line('sms'); ?></label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="individual_send_by[]" value="push" id="ind_push">
                                                <label class="form-check-label" for="ind_push"><?php echo $this->lang->line('mobile_app'); ?></label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label><?php echo $this->lang->line('template_id'); ?></label>
                                        <input class="form-control" name="individual_template_id">
                                        <div class="form-text text-danger"><?php echo $this->lang->line('this_field_require_for_smsgateway'); ?></div>
                                    </div>
                                    <div class="mb-3">
                                        <label><?php echo $this->lang->line('message'); ?> <small class="req">*</small></label>
                                        <textarea id="individual_msg_text" name="individual_message" class="form-control compose-textarea" rows="10"><?php echo set_value('message'); ?></textarea>
                                        <div class="d-flex justify-content-end mt-1">
                                            <small class="text-secondary tot_count_individual_msg_text word_counter"><?php echo $this->lang->line('character_count'); ?>: 0</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div>
                                        <label><?php echo $this->lang->line('message_to'); ?> <small class="req">*</small></label>
                                        <div class="position-relative sh-form-card p-2 w-100 overflow-visible">
                                            <div class="input-group">
                                                <button type="button" class="btn btn-secondary dropdown-toggle bs-dropdown-to-select bs-dropdown-to-select-group" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <span data-bind="bs-drp-sel-label"><?php echo $this->lang->line('select'); ?></span>
                                                    <input type="hidden" name="selected_value" data-bind="bs-drp-sel-value" value="">
                                                </button>
                                                <ul class="dropdown-menu" role="menu">
                                                    <li data-value="Patient"><a class="dropdown-item" href="#"><?php echo $this->lang->line('patient'); ?></a></li>
                                                    <?php foreach ($roles as $role_key => $role_value) { ?>
                                                    <li data-value="Staff-<?php echo $role_value['id'] ?>"><a class="dropdown-item" href="#"><?php echo $role_value['name']; ?></a></li>
                                                    <?php } ?>
                                                </ul>
                                                <input type="text" value="" data-record="" data-email="" data-mobileno="" data-app_key="" class="form-control" autocomplete="off" name="text" id="search-query" placeholder="<?php echo $this->lang->line('search'); ?>...">
                                                <button class="btn btn-primary add-btn" type="button"><i class="fa fa-plus me-1"></i><?php echo $this->lang->line('add'); ?></button>
                                            </div>
                                            <div id="suggesstion-box" class="sh-suggestion-dropdown d-none"></div>
                                        </div>
                                    </div>
                                    <div class="dual-list list-right">
                                        <div class="sh-form-card overflow-hidden">
                                            <div class="p-2 border-bottom">
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text"><i class="fa fa-search"></i></span>
                                                    <input type="text" name="SearchDualList" class="form-control" placeholder="<?php echo $this->lang->line('search'); ?>" />
                                                </div>
                                            </div>
                                            <div class="wellscroll p-2 sh-min-h-200">
                                                <ul class="list-group list-group-flush send_list"></ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-end mt-3 pt-3 border-top">
                                <button type="submit" class="btn btn-primary submit_individual" data-loading-text="<i class='fa fa-spinner fa-spin'></i> <?php echo $this->lang->line('sending'); ?>">
                                    <i class="fa fa-paper-plane me-1"></i><?php echo $this->lang->line('send'); ?>
                                </button>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).on('click', '.dropdown-menu li', function () {
        $("#suggesstion-box").empty().addClass("d-none");
    });
    $(document).ready(function (e) {
        $(document).on('click', '.dropdown-menu li', function (event) {
            var $li = $(this);
            var $btn = $li.closest('.dropdown-menu').prev('.bs-dropdown-to-select-group');
            if ($btn.length) {
                event.preventDefault();
                $btn.find('[data-bind="bs-drp-sel-value"]').val($li.attr('data-value'));
                $btn.find('[data-bind="bs-drp-sel-label"]').text($li.find('a').text().trim());
            }
        });
    });
</script>

<script type="text/javascript">
    var attr = {};

    $(document).ready(function () {
        $("#search-query").keyup(function () {
            $("#search-query").attr('data-record', "");
            $("#search-query").attr('data-email', "");
            $("#search-query").attr('data-mobileno', "");
            $("#search-query").attr('data-app_key', "");
            $("#suggesstion-box").addClass("d-none");
            var category_selected = $("input[name='selected_value']").val();
            var arr = category_selected.split('-');
            var category_set = arr[0];

            $.ajax({
                type: "POST",
                url: "<?php echo site_url('admin/mailsms/search') ?>",
                data: {'keyword': $(this).val(), 'category': category_selected},
                dataType: 'JSON',
                beforeSend: function () {
                    $("#search-query").css("background", "url(../../backend/images/loading.gif) no-repeat 165px");
                },
                success: function (data) {
                    if (data.length > 0) {
                        setTimeout(function () {
                            var cList = $('<div/>').addClass('list-group');
                            $.each(data, function (i, obj) {
                                if (category_set == "Staff") {
                                    var email = obj.email;
                                    var contact = obj.contact_no;
                                    var app_key = '';
                                    var name = obj.name + ' ' + obj.surname + ' (' + obj.employee_id + ')';
                                } else if (category_set == "Patient") {
                                    var email = obj.email;
                                    var contact = obj.mobileno;
                                    var app_key = obj.app_key;
                                    var name = obj.patient_name + ' (' + obj.id + ')';
                                }
                                $('<a/>').addClass('list-group-item list-group-item-action py-2 px-3')
                                    .attr({'href': '#', 'category': category_set, 'record_id': obj.id,
                                           'email': email, 'mobileno': contact, 'app_key': app_key})
                                    .text(name)
                                    .appendTo(cList);
                            });
                            $("#suggesstion-box").html(cList).removeClass("d-none");
                            $("#search-query").css("background", "");
                        }, 1000);
                    } else {
                        $("#suggesstion-box").addClass("d-none");
                        $("#search-query").css("background", "");
                    }
                }
            });
        });
    });

    $(document).on('click', '#suggesstion-box .list-group-item', function (e) {
        e.preventDefault();
        var val = $(this).text();
        var record_id = $(this).attr('record_id');
        var email = $(this).attr('email');
        var mobileno = $(this).attr('mobileno');
        var app_key = $(this).attr('app_key');
        $("#search-query").val(val).attr({'data-record': record_id, 'data-email': email,
            'data-app_key': app_key, 'data-mobileno': mobileno});
        $("#suggesstion-box").empty().addClass("d-none");
    });

    $(document).on('click', '.add-btn', function () {
        var value = $("#search-query").val();
        var record_id = $("#search-query").attr('data-record');
        var email = $("#search-query").attr('data-email');
        var mobileno = $("#search-query").attr('data-mobileno');
        var app_key = $("#search-query").attr('data-app_key');
        var category_selected = $("input[name='selected_value']").val();
        const myArray = category_selected.split("-");
        let word = myArray[0];

        if (record_id != "" && category_selected != "") {
            var chkexists = checkRecordExists(category_selected + "-" + record_id);
            if (chkexists) {
                var arr = [];
                arr.push({'category': category_selected, 'record_id': record_id,
                    'email': email, 'mobileno': mobileno, 'app_key': app_key});
                attr[category_selected + "-" + record_id] = arr;
                $("#search-query").attr('value', "").val("");
                $("#search-query").attr('data-record', "");
                $(".send_list").append('<li class="list-group-item d-flex align-items-center justify-content-between" id="' + category_selected + '-' + record_id + '"><span><i class="fa fa-user me-1"></i> ' + value + ' (' + word + ')</span><button type="button" class="btn btn-sm btn-outline-danger py-0 px-1 ms-2" title="<?php echo $this->lang->line('delete'); ?>" onclick="delete_record(\'' + category_selected + '-' + record_id + '\')"><i class="fa fa-trash"></i></button></li>');
            } else {
                errorMsg('<?php echo $this->lang->line('record_already_exists') ?>');
            }
        } else {
            errorMsg("<?php echo $this->lang->line('message_to') . " field is required" ?>");
        }
        getTotalRecord();
    });
</script>

<script type="text/javascript">
    function getTotalRecord() {
        $.each(attr, function (key, value) {});
    }

    function checkRecordExists(find) {
        return !(find in attr);
    }

    $(function () {
        $('[name="SearchDualList"]').keyup(function (e) {
            var code = e.keyCode || e.which;
            if (code == '9') return;
            if (code == '27') $(this).val(null);
            var $rows = $(this).closest('.dual-list').find('.list-group li');
            var val = $.trim($(this).val()).replace(/ +/g, ' ').toLowerCase();
            $rows.removeClass('d-none').filter(function () {
                return !~$(this).text().replace(/\s+/g, ' ').toLowerCase().indexOf(val);
            }).addClass('d-none');
        });
    });

    function delete_record(record) {
        delete attr[record];
        $('#' + record).remove();
        getTotalRecord();
        return false;
    }

    $("#individual_form").submit(function (event) {
        var $form = $(this), url = $form.attr('action');
        var formData = $(this).serializeArray();
        var user_list = (!jQuery.isEmptyObject(attr)) ? JSON.stringify(attr) : "";
        formData.push({name: "user_list", value: user_list});
        event.preventDefault();
        var $this = $('.submit_individual');
        $this.btnLoading();
        $.ajax({
            type: "POST", url: url, data: formData, dataType: "JSON",
            success: function (data) {
                if (data.status == 1) {
                    var message = "";
                    $.each(data.msg, function (index, value) { message += value; });
                    errorMsg(message);
                } else {
                    $('#individual_form')[0].reset();
                    $('.word_counter').html("<?php echo $this->lang->line('character_count'); ?>: 0");
                    $("ul.send_list").empty();
                    attr = {};
                    successMsg(data.msg);
                }
            },
            complete: function () { $this.btnReset(); }
        });
    });

    $("#group_form").submit(function (event) {
        var $form = $(this), url = $form.attr('action');
        var formData = $(this).serializeArray();
        event.preventDefault();
        var $this = $('.submit_group');
        $this.btnLoading();
        $.ajax({
            type: "POST", url: url, data: formData, dataType: "JSON",
            success: function (data) {
                if (data.status == 1) {
                    var message = "";
                    $.each(data.msg, function (index, value) { message += value; });
                    errorMsg(message);
                } else {
                    $('#group_form')[0].reset();
                    $('.word_counter').html("<?php echo $this->lang->line('character_count'); ?>: 0");
                    successMsg(data.msg);
                }
            },
            complete: function () { $this.btnReset(); }
        });
    });

    $(document).on('keypress keyup keydown paste change focus blur', '.compose-textarea', function (event) {
        var total_length = this.value.length;
        $(this).closest('.mb-3').find('.word_counter').html("<?php echo $this->lang->line('character_count'); ?>: " + total_length);
    });
</script>
