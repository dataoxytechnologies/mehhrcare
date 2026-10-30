<?php
/**
 * Patient personal Appearance view.
 *
 * Matches the patient panel card-with-header pattern used by
 * user/change_password.php — outer .card + .card-header (title) +
 * .card-body (containing the shared partial).
 */
$save_url  = base_url('patient/appearance/save');
$reset_url = base_url('patient/appearance/reset');
?>

<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title titlefix">
          <i class="fa fa-paint-brush me-1 opacity-75 sh-appearance-title-ic"></i>
          <?= html_escape($title ?? ($this->lang->line('theme') ?: 'Theme')) ?>
        </h3>
      </div>
      <div class="card-body">
        <?php $this->load->view('_partials/appearance_card', [
          'user_pref'      => $user_pref,
          'hospital_theme' => $hospital_theme,
          'save_url'       => $save_url,
          'reset_url'      => $reset_url,
        ]); ?>
      </div>
    </div>
  </div>
</div>
