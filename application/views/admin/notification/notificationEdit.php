<div class="row">
    <div class="col-md-12">
        <form id="form1" action="<?php echo base_url() ?>admin/notification/edit/<?php echo $id ?>" name="employeeform" method="post" accept-charset="utf-8">
            <?php
            $prev_roles = explode(',', $notification['roles'] ?? '');
            foreach ($prev_roles as $prev_roles_key => $prev_roles_value) { ?>
                <input type="hidden" name="prev_roles[]" value="<?php echo $prev_roles_value; ?>">
            <?php } ?>
            <?php echo $this->customlib->getCSRF(); ?>
            <?php if ($this->session->flashdata('msg')) { ?>
                <?php echo $this->session->flashdata('msg');
                $this->session->unset_userdata('msg'); ?>
            <?php } ?>
            <?php if (isset($error_message)) { ?>
                <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
                    <i class="fa fa-exclamation-circle me-2"></i><?php echo $error_message; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php } ?>

            <div class="row g-3 align-items-stretch">

                <!-- Left: Message Content -->
                <div class="col-md-8">
                    <div class="card h-100 d-flex flex-column">
                        <div class="card-header">
                            <h3 class="card-title mb-0"><?php echo $this->lang->line('edit_message'); ?></h3>
                        </div>
                        <div class="card-body d-flex flex-column flex-grow-1">
                            <div class="mb-3">
                                <label class="form-label form-label-sm"><?php echo $this->lang->line('title'); ?> <small class="req">*</small></label>
                                <input autofocus id="title" name="title" type="text" class="form-control form-control-sm" placeholder="<?php echo $this->lang->line('title'); ?>..." value="<?php echo set_value('title', $notification['title']); ?>" />
                                <span class="text-danger small"><?php echo form_error('title'); ?></span>
                            </div>
                            <div class="flex-grow-1 d-flex flex-column">
                                <label class="form-label form-label-sm"><?php echo $this->lang->line('message'); ?> <small class="req">*</small></label>
                                <textarea id="compose-textarea" name="message" class="form-control flex-grow-1 sh-min-h-260"><?php echo set_value('message', $notification['message']); ?></textarea>
                                <span class="text-danger small"><?php echo form_error('message'); ?></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right: Settings -->
                <div class="col-md-4 d-flex flex-column">

                    <!-- Schedule -->
                    <div class="sh-form-card mb-3">
                        <div class="sh-card-header">
                            <span class="sh-card-header-title"><i class="fa fa-calendar me-1"></i> <?php echo $this->lang->line('notice_date'); ?> &amp; <?php echo $this->lang->line('publish_on'); ?></span>
                        </div>
                        <div class="p-3">
                            <div class="row g-2">
                                <div class="col-6">
                                    <label class="form-label form-label-sm"><?php echo $this->lang->line('notice_date'); ?> <small class="req">*</small></label>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text"><i class="fa fa-calendar"></i></span>
                                        <input id="date" name="date" type="text" class="form-control form-control-sm date" value="<?php echo set_value('date', $this->customlib->YYYYMMDDTodateFormat($notification['date'])); ?>" />
                                    </div>
                                    <span class="text-danger small"><?php echo form_error('date'); ?></span>
                                </div>
                                <div class="col-6">
                                    <label class="form-label form-label-sm"><?php echo $this->lang->line('publish_on'); ?> <small class="req">*</small></label>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text"><i class="fa fa-calendar-check-o"></i></span>
                                        <input id="publish_date" name="publish_date" type="text" class="form-control form-control-sm date" value="<?php echo set_value('publish_date', $this->customlib->YYYYMMDDTodateFormat($notification['publish_date'])); ?>" />
                                    </div>
                                    <span class="text-danger small"><?php echo form_error('publish_date'); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recipients -->
                    <div class="sh-form-card flex-grow-1 d-flex flex-column">
                        <div class="sh-card-header">
                            <span class="sh-card-header-title"><i class="fa fa-users me-1"></i> <?php echo $this->lang->line('message_to'); ?></span>
                        </div>
                        <div class="flex-grow-1">
                            <?php
                            $userdata = $this->customlib->getUserData();
                            $role_id  = $userdata["role_id"];
                            foreach ($roles as $role_key => $role_value) {
                                $is_checked = (set_value('visible[]', in_array($role_value['id'], $prev_roles)) == 1) ? TRUE : FALSE;
                                $is_own     = ($role_value["id"] == $role_id);
                                $last       = ($role_key === array_key_last($roles));
                            ?>
                            <label for="role_<?php echo (int)$role_value['id']; ?>"
                                class="d-flex align-items-center px-3 py-2 gap-2 <?php echo !$last ? 'border-bottom' : ''; ?> <?php echo $is_own ? 'bg-body-tertiary' : 'cursor-pointer'; ?>">
                                <input class="form-check-input flex-shrink-0 mt-0" type="checkbox" name="visible[]"
                                    id="role_<?php echo (int)$role_value['id']; ?>"
                                    value="<?php echo (int)$role_value['id']; ?>"
                                    <?php if ($is_own) { echo 'checked onclick="return false;"'; } else { echo set_checkbox('visible[]', $role_value['id'], $is_checked); } ?> />
                                <span class="form-label-sm mb-0 flex-grow-1 <?php echo $is_own ? 'text-muted' : ''; ?>">
                                    <?php echo html_escape($role_value['name']); ?>
                                </span>
                                <?php if ($is_own) { ?>
                                    <span class="badge rounded-pill sh-badge-you">You</span>
                                <?php } ?>
                            </label>
                            <?php } ?>
                            <span class="text-danger small px-3"><?php echo form_error('visible[]'); ?></span>
                        </div>
                    </div>

                    <!-- Send -->
                    <div class="mt-3">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fa fa-paper-plane-o me-1"></i> <?php echo $this->lang->line('send'); ?>
                        </button>
                    </div>

                </div>

            </div>
        </form>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        var date_format = '<?php echo $result = strtr($this->customlib->getHospitalDateFormat(), ['d' => 'dd', 'm' => 'mm', 'Y' => 'yyyy',]) ?>';
        $("#btnreset").click(function () {
            $("#form1")[0].reset();
        });
    });
</script>
<script>
    $(function () {
        if (window.CKEDITOR && CKEDITOR.instances && CKEDITOR.instances['compose-textarea']) {
            CKEDITOR.instances['compose-textarea'].destroy(true);
        }
        if (window.CKEDITOR) {
            CKEDITOR.replace('compose-textarea', {
                allowedContent: true,
                bodyClass: document.body.classList.contains('variant-b') ? 'variant-b' : ''
            });
        }
    });
</script>
