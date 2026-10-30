<div class="row">

            <?php $this->load->view('setting/sidebar'); ?>

            <div class="col-md-10">
                <div class="card">

                    <div class="card-header ptbnull">
                        <h3 class="card-title titlefix">
                            <i class="fa fa-cogs"></i> <?= $this->lang->line('queue_process_setting') ?>
                            <?php $mode = isset($result->message_mode) ? (int)$result->message_mode : 0; ?>
                            <?php if ($mode == 1): ?>
                                <span class="stat-badge queue"><i class="fa fa-check-circle"></i> <?= $this->lang->line('queue_active') ?></span>
                            <?php else: ?>
                                <span class="stat-badge direct"><i class="fa fa-bolt"></i> <?= $this->lang->line('direct_active') ?></span>
                            <?php endif; ?>
                        </h3>
                    </div>

                    <form role="form" id="queueprocess_form" method="post">
                        <input type="hidden" name="sch_id" value="<?php echo (int)$result->id; ?>">
                        <input type="hidden" name="message_mode" id="message_mode_input" value="<?php echo $mode; ?>">

                        <div class="card-body">

                            <!-- ── TOGGLE + MODE DESCRIPTIONS ──────────────── -->
                            <div class="toggle-section">

                                <div class="toggle-wrap">
                                    <div class="big-toggle <?php echo ($mode == 1) ? 'on' : ''; ?>" id="bigToggle" onclick="toggleMode()">
                                        <div class="knob">
                                            <i class="fa <?php echo ($mode == 1) ? 'fa-list-alt' : 'fa-bolt'; ?>" id="knobIcon"></i>
                                        </div>
                                        <span class="lbl-left"><?= $this->lang->line('toggle_direct') ?></span>
                                        <span class="lbl-right"><?= $this->lang->line('toggle_queue') ?></span>
                                    </div>
                                    <div class="toggle-hint"><?= $this->lang->line('click_to_switch_mode') ?></div>
                                </div>

                                <div class="mode-desc-row">

                                    <div class="mode-desc direct <?php echo ($mode == 0) ? 'active' : ''; ?>" id="descDirect">
                                        <div class="md-title"><i class="fa fa-bolt"></i> <?= $this->lang->line('direct_mode') ?></div>
                                        <p><?= $this->lang->line('direct_mode_desc') ?></p>
                                    </div>

                                    <div class="mode-desc queue <?php echo ($mode == 1) ? 'active' : ''; ?>" id="descQueue">
                                        <div class="md-title"><i class="fa fa-list-alt"></i> <?= $this->lang->line('queue_mode') ?></div>
                                        <p><?= $this->lang->line('queue_mode_desc') ?></p>
                                    </div>

                                </div>

                            </div>
                            <!-- /toggle section -->

                            <!-- ── QUEUE PARAMS ────────────────────────────── -->
                            <div class="params-section <?php echo ($mode == 0) ? 'dimmed' : ''; ?>" id="paramsSection">

                                <!-- Cron strip -->
                                <div class="cron-strip">
                                    <i class="fa fa-clock-o"></i>
                                    <div>
                                        <strong><?= $this->lang->line('cron_setup_required') ?></strong>
                                        <?= $this->lang->line('cron_setup_instruction') ?><br>
                                        <code><?php echo site_url('cron/index/' . (isset($result->cron_secret_key) ? $result->cron_secret_key : 'YOUR_KEY')); ?></code><br>
                                        <span class="sh-cron-note"><?= $this->lang->line('cron_recommended_interval') ?></span>
                                    </div>
                                </div>

                                <!-- Fields row -->
                                <div class="field-row">

                                    <div class="field-item">
                                        <div class="field-label"><?= $this->lang->line('retry_attempts') ?> <span class="req">*</span></div>
                                        <div class="stepper">
                                            <button type="button" class="stepper-btn" onclick="step('attempts', -1)">−</button>
                                            <input type="number"
                                                class="stepper-val"
                                                name="message_queue_attempts"
                                                id="attemptsVal"
                                                value="<?php echo (isset($result->message_queue_attempts) && $result->message_queue_attempts > 0) ? (int)$result->message_queue_attempts : 3; ?>"
                                                min="1" max="10">
                                            <button type="button" class="stepper-btn" onclick="step('attempts', 1)">+</button>
                                        </div>
                                        <small class="text-muted"><?= $this->lang->line('max_retries_hint') ?></small>
                                        <span class="text-danger"><?php echo form_error('message_queue_attempts'); ?></span>
                                    </div>

                                    <div class="field-item">
                                        <div class="field-label"><?= $this->lang->line('batch_size') ?> <span class="req">*</span></div>
                                        <div class="stepper">
                                            <button type="button" class="stepper-btn" onclick="step('batch', -10)">−</button>
                                            <input type="number"
                                                class="stepper-val"
                                                name="message_queue_batch_size"
                                                id="batchVal"
                                                value="<?php echo (isset($result->message_queue_batch_size) && $result->message_queue_batch_size > 0) ? (int)$result->message_queue_batch_size : 50; ?>"
                                                min="1" max="500">
                                            <button type="button" class="stepper-btn" onclick="step('batch', 10)">+</button>
                                        </div>
                                        <small class="text-muted"><?= $this->lang->line('messages_per_cron_hint') ?></small>
                                        <span class="text-danger"><?php echo form_error('message_queue_batch_size'); ?></span>
                                    </div>

                                    <div class="tip-box">
                                        <div class="tip-title"><i class="fa fa-lightbulb-o"></i> <?= $this->lang->line('tip') ?></div>
                                        <?= $this->lang->line('batch_size_tip') ?>
                                    </div>

                                </div><!-- /field-row -->

                            </div><!-- /params-section -->

                        </div><!-- /.box-body -->

                        <div class="card-footer">
                            <?php if ($this->rbac->hasPrivilege('general_setting', 'can_edit')): ?>
                                <button type="button"
                                    class="btn btn-primary float-end btn-save-queueprocess"
                                    data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i> <?php echo $this->lang->line('processing'); ?>">
                                    <i class="fa fa-save"></i> <?php echo $this->lang->line('save'); ?>
                                </button>
                            <?php endif; ?>
                        </div>

                    </form>
                </div><!-- /.box -->
            </div><!-- /.col -->

        </div>

<script>
    var queueActive = <?php echo $mode; ?> === 1;

    function toggleMode() {
        queueActive = !queueActive;
        var toggle = document.getElementById('bigToggle');
        var icon   = document.getElementById('knobIcon');

        toggle.classList.toggle('on', queueActive);
        icon.className = queueActive ? 'fa fa-list-alt' : 'fa fa-bolt';

        document.getElementById('descDirect').classList.toggle('active', !queueActive);
        document.getElementById('descQueue').classList.toggle('active', queueActive);
        document.getElementById('paramsSection').classList.toggle('dimmed', !queueActive);
        document.getElementById('message_mode_input').value = queueActive ? 1 : 0;
    }

    function step(field, delta) {
        var id  = field === 'attempts' ? 'attemptsVal' : 'batchVal';
        var el  = document.getElementById(id);
        var val = parseInt(el.value) + delta;
        el.value = Math.max(parseInt(el.min), Math.min(parseInt(el.max), val));
    }

    $(".btn-save-queueprocess").on('click', function () {
        var $this = $(this);
        $this.btnLoading();
        $.ajax({
            url: '<?php echo site_url("schsettings/savequeueprocess"); ?>',
            type: 'POST',
            data: $('#queueprocess_form').serialize(),
            dataType: 'json',
            success: function (data) {
                if (data.status == "fail") {
                    var msg = "";
                    $.each(data.error, function (k, v) { msg += v; });
                    errorMsg(msg);
                } else {
                    successMsg(data.message);
                }
                $this.btnReset();
            }
        });
    });
</script>
