<?php
defined('BASEPATH') or exit('No direct script access allowed');

// Maps a target token status to its human action label.
function pf_token_action_label($status)
{
    $map = array(
        'ACTIVE'              => 'Activate',
        'WAITING'             => 'Queue',
        'CALLED'              => 'Call',
        'IN_PROCESS'          => 'Start',
        'CLOSED'              => 'Complete',
        'NO_SHOW'             => 'No Show',
        'SKIPPED'             => 'Skip',
        'WAITING_SECOND_CALL' => 'Second Call',
    );
    return isset($map[$status]) ? $map[$status] : ucfirst(strtolower($status));
}
?>
<style>
    .pf-token-no { font-weight: 700; font-size: 0.95rem; }
    .pf-token-badge { min-width: 130px; font-size: .75rem; font-weight: 600; letter-spacing: .3px; padding: .35em .6em; }
    .bg-purple { background-color: #6f42c1 !important; }
</style>
<?php
// Color-coded badge class per token status.
if (!function_exists('pf_token_badge_class')) {
    function pf_token_badge_class($status)
    {
        $map = array(
            'NOT_ISSUED'          => 'bg-secondary text-white',
            'ACTIVE'              => 'bg-success text-white',
            'WAITING'             => 'bg-warning text-dark',
            'CALLED'              => 'bg-primary text-white',
            'WAITING_SECOND_CALL' => 'bg-info text-white',
            'IN_PROCESS'          => 'bg-purple text-white',
            'CLOSED'              => 'bg-success-subtle text-success-emphasis border border-success-subtle',
            'NO_SHOW'             => 'bg-danger text-white',
            'SKIPPED'             => 'bg-dark text-white',
        );
        return isset($map[$status]) ? $map[$status] : 'bg-secondary text-white';
    }
}
?>

<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
        <div class="d-flex align-items-center gap-2">
            <i class="fa fa-ticket sh-icon-soft"></i>
            <h3 class="card-title titlefix mb-0"><?php echo $this->lang->line('token_queue') ?: 'Token Queue'; ?> <small class="text-muted">(P1-L5 Token Control)</small></h3>
        </div>
        <a href="<?php echo site_url('admin/patientflow'); ?>" class="btn btn-sm btn-outline-secondary">
            <i class="fa fa-users"></i> <?php echo $this->lang->line('patient_flow'); ?>
        </a>
    </div>

    <div class="card-body border-bottom py-2">
        <div class="d-flex align-items-center flex-wrap gap-2 small">
            <i class="fa fa-info-circle text-primary"></i>
            <span><?php echo $this->lang->line('token_queue_hint') ?: 'Only paid patients enter the doctor queue. R1 activates the token after payment confirmation and manages the queue until the consultation is completed.'; ?></span>
        </div>
    </div>

    <div class="card-body border-bottom">
        <form method="get" action="<?php echo site_url('admin/patientflow/token_queue'); ?>" class="row g-3 align-items-end">
            <div class="col-md-4 col-lg-3">
                <label class="form-label form-label-sm"><?php echo $this->lang->line('token_status') ?: 'Token Status'; ?></label>
                <select name="status" class="form-select form-select-sm">
                    <option value=""><?php echo $this->lang->line('all') ?: 'All'; ?></option>
                    <?php foreach ($token_statuses as $ts) { ?>
                    <option value="<?php echo $ts; ?>" <?php if ($filter_status === $ts) { echo 'selected'; } ?>>
                        <?php echo $ts; ?>
                    </option>
                    <?php } ?>
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-outline-primary btn-sm">
                    <i class="fa fa-filter"></i> <?php echo $this->lang->line('filter'); ?>
                </button>
                <?php if (!empty($filter_status)) { ?>
                <a href="<?php echo site_url('admin/patientflow/token_queue'); ?>" class="btn btn-outline-secondary btn-sm">
                    <i class="fa fa-times"></i>
                </a>
                <?php } ?>
            </div>
        </form>
    </div>
    <div class="card-body table-responsive">
        <table class="table table-striped table-bordered table-hover">
            <thead>
                <tr>
                    <th><?php echo $this->lang->line('token') ?: 'Token'; ?></th>
                    <th><?php echo $this->lang->line('patient'); ?></th>
                    <th class="text-center"><?php echo $this->lang->line('token_status') ?: 'Token Status'; ?></th>
                    <th class="text-center"><?php echo $this->lang->line('current_level'); ?></th>
                    <th><?php echo $this->lang->line('activated_at') ?: 'Activated At'; ?></th>
                    <th class="text-end text-nowrap"><?php echo $this->lang->line('action'); ?></th>
                </tr>
            </thead>
            <tbody>
<?php foreach ($token_queue as $q) { ?>
                <tr>
                    <td class="align-middle">
                        <span class="pf-token-no"><?php echo $q->token_no ? $q->token_no : '—'; ?></span>
                        <div class="small text-muted"><?php echo $q->visit_no; ?></div>
                    </td>
                    <td class="align-middle">
                        <strong><?php echo htmlspecialchars($q->patient_name); ?></strong>
                        <div class="small text-muted">
                            <?php
                            echo ($q->gender ? $q->gender . ' · ' : '') . ($q->age ? $q->age . 'Y' : '');
                            if ($q->mobileno) { echo ' · ' . $q->mobileno; }
                            ?>
                        </div>
                    </td>
                    <td class="align-middle text-center">
                        <span class="badge pf-token-badge <?php echo pf_token_badge_class($q->token_status); ?>">
                            <?php echo htmlspecialchars(str_replace('_', ' ', $q->token_status)); ?>
                        </span>
                    </td>
                    <td class="align-middle text-center">
                        <span class="badge bg-primary-subtle text-primary-emphasis"><?php echo $q->level_code ? $q->level_code : '—'; ?></span>
                        <div class="small text-muted"><?php echo $q->level_title; ?></div>
                    </td>
                    <td class="align-middle small">
                        <?php echo !empty($q->token_activated_at) ? date('d-M Y H:i', strtotime($q->token_activated_at)) : '—'; ?>
                    </td>
                    <td class="align-middle text-end text-nowrap">
                        <?php if (!empty($pf_is_r1) && !empty($token_transitions[$q->token_status])) { ?>
                            <?php foreach ($token_transitions[$q->token_status] as $to) { ?>
                            <button type="button"
                                    class="btn btn-sm btn-outline-primary mb-1 w-100"
                                    data-visit="<?php echo (int) $q->id; ?>"
                                    data-to="<?php echo $to; ?>"
                                    onclick="pfSetTokenStatus(this);">
                                <?php echo htmlspecialchars(pf_token_action_label($to)); ?>
                            </button>
                            <?php } ?>
                        <?php } else { ?>
                            <span class="text-muted">—</span>
                        <?php } ?>
                    </td>
                </tr>
<?php } ?>
<?php if (empty($token_queue)) { ?>
                <tr>
                    <td colspan="6" class="text-center text-muted py-4"><?php echo $this->lang->line('token_queue_empty') ?: 'No tokens in the queue yet.'; ?></td>
                </tr>
<?php } ?>
            </tbody>
        </table>
    </div>
</div>

<script>
function pfSetTokenStatus(btn) {
    var visitId = btn.getAttribute('data-visit');
    var to      = btn.getAttribute('data-to');
    if (!visitId || !to) { return; }
    if (!confirm('Change token status to "' + to + '"?')) { return; }

    var remarks = prompt('<?php echo $this->lang->line('remarks') ?: 'Remarks'; ?> (optional):', '');

    var data = { visit_id: visitId, status: to };
    if (remarks) { data.remarks = remarks; }

    $.ajax({
        url: '<?php echo site_url('admin/patientflow/set_token_status'); ?>',
        type: 'POST',
        data: data,
        dataType: 'json',
        success: function (response) {
            if (response.status) {
                toastr.success(response.message);
                setTimeout(function () { window.location.reload(); }, 900);
            } else {
                toastr.error(response.message);
            }
        },
        error: function () {
            toastr.error('<?php echo $this->lang->line('token_action_failed') ?: 'An error occurred while updating the token status.'; ?>');
        }
    });
}
</script>
