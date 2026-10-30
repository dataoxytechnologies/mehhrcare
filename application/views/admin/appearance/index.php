<?php
/**
 * Staff personal Appearance view.
 *
 * Matches the admin Settings card pattern (same as user/change_password.php on
 * the patient side) — outer .card + .card-header (title) + .card-body (shared partial).
 */
$save_url  = base_url('admin/appearance/save');
$reset_url = base_url('admin/appearance/reset');
?>

<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title titlefix">
          <i class="fa fa-paint-brush me-1 opacity-75 sh-text-accent"></i>
          <?= html_escape($title ?? $this->lang->line('appearance_settings')) ?>
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
