<div id="activelicmodal" class="modal fade sh-modal sh-modal-accent" tabindex="-1" aria-labelledby="activelicmodalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="activelicmodalLabel"><?= $this->lang->line('register_your_purchase_code') ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?php echo site_url('admin/admin/updatePurchaseCode') ?>" method="POST" id="purchase_code">
                <div class="modal-body lic_modal-body">
                    <div class="alert alert-warning d-flex align-items-start gap-2 mb-3 layout-alert-sm" role="alert">
                        <i class="fa fa-info-circle mt-1"></i>
                        <div><b>Important:</b> Smart Hospital Regular License allows to use Smart Hospital for single hospital/branch/end/client but for customer convenience registering Smart Hospital allows to register Smart Hospital licence purchase code on upto 3 urls e.g. 1. For localhost 2. For testing environment and 3. For your production url (testing and production url should be on same domain).</div>
                    </div>
                    <div class="error_message"></div>
                    <div class="mb-3">
                        <label for="input-envato_market_purchase_code" class="form-label">Envato Market Purchase Code for Smart Hospital ( <a target="_blank" href="https://help.market.envato.com/hc/en-us/articles/202822600-Where-Is-My-Purchase-Code-"> How to find it?</a> )</label>
                        <input type="text" class="form-control" id="input-envato_market_purchase_code" name="envato_market_purchase_code">
                        <div id="error" class="text text-danger"></div>
                    </div>
                    <div class="mb-3">
                        <label for="input-email" class="form-label"><?= $this->lang->line('your_email_registered_with_envato') ?></label>
                        <input type="text" class="form-control" id="input-email" name="email">
                        <div id="error" class="text text-danger"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary" data-loading-text="<i class='fa fa-spinner fa-spin '></i> Saving...">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
