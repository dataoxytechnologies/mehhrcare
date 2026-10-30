<?php
/* Payment success confirmation — patient/pay/successinvoice
   Shown after an invoice payment completes.
   The patient layout (header.php) already opens <main class="content">, so this
   view must NOT re-wrap in .content-wrapper / section.content (that double-applies
   the sidebar/topbar offset — same fix as the Stripe redesign, 2026-05-20).
   All styling lives in backend/css/sh-theme.css → ".sh-pay-success" block. No inline CSS. */
?>
<div class="sh-pay-success">
    <div class="sh-pay-success-card">

        <div class="sh-pay-success-icon">
            <i class="fa fa-check"></i>
        </div>

        <h1 class="sh-pay-success-title"><?php echo $this->lang->line('success'); ?></h1>
        <p class="sh-pay-success-text"><?php echo $this->lang->line('thank_you_for_your_payment'); ?></p>

        <div class="sh-pay-success-actions">
            <a href="<?php echo base_url('patient/dashboard/bill'); ?>" class="btn btn-light">
                <i class="fa fa-file-text-o me-1"></i><?php echo $this->lang->line('bill'); ?>
            </a>
            <a href="<?php echo base_url('patient/dashboard'); ?>" class="btn btn-primary">
                <i class="fa fa-home me-1"></i><?php echo $this->lang->line('dashboard'); ?>
            </a>
        </div>

    </div>
</div>
