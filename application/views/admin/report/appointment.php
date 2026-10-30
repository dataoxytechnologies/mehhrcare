<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
?>
<div class="row">
            <div class="col-md-12">
                <div class="card border0 mb0">
                    <?php $this->load->view('admin/report/_appointment');?>
                </div>
            </div>  
        </div>
