<?php 
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
if($module=='radiology_billing_id'){
 $print='print_receipt';
}elseif($module=='pathology_billing_id'){
 $print='print_trans';
}else{
 $print='print_trans'; 
}
?>
<div class="sh-form-card">
    <div class="sh-card-header">
        <span class="sh-card-header-title"><i class="fas fa-money-bill-wave me-1"></i><?php echo $this->lang->line('payments'); ?></span>
    </div>
    <div class="p-2">
    <div class="table-responsive">
        <table class="table table-striped table-bordered table-hover table-sm mb-0 example">
                <thead>
                    <th><?php echo $this->lang->line('date'); ?></th>
                    <th><?php echo $this->lang->line('note'); ?></th>
                    <th><?php echo $this->lang->line('payment_mode'); ?></th>
                    <th class="text-end"><?php echo $this->lang->line('paid_amount') . " (" . $currency_symbol . ")"; ?></th>
                    <th class="text-end"><?php echo $this->lang->line('action'); ?></th>
                </thead>
                <tbody>
                    <?php					 
                            $total = 0;
							if (!empty($payment_details)) {                                                
                                                foreach ($payment_details as $payment) {                                                   
                                                    if (!empty($payment['amount'])) {
                                                        $total += $payment['amount'];
                                                    }
                                                    ?>
                <tr>
                    <td><?php echo $this->customlib->YYYYMMDDHisTodateFormat($payment['payment_date'],$this->customlib->getHospitalTimeFormat()); ?></td>
                                                        <td><?php echo html_escape($payment["note"]) ?></td>
                                                      <td><?php echo $this->lang->line(strtolower($payment["payment_mode"]))."<br>";
                                                        if($payment['payment_mode'] == "Cheque"){
                                                             if($payment['cheque_no']!=''){
                                       echo $this->lang->line("cheque_no"). ": ".html_escape($payment['cheque_no']);
                                    echo "<br>";
                                }
                                    if($payment['cheque_date']!='' && $payment['cheque_date']!='0000-00-00'){
                                       echo $this->lang->line("cheque_date"). ": ".$this->customlib->YYYYMMDDTodateFormat($payment['cheque_date']);
                                   }                                     

                                     }
                                                        ?>
                    </td> 
                    <td class="text-end"><?php echo amountFormat($payment["amount"]) ?></td>
                    <td class="text-end">
                                                        <?php  if ($payment['payment_mode'] == "Cheque" && $payment['attachment'] != "")  {
                                                    ?>
                                                    <a href='<?php echo site_url('admin/transaction/download/'.$payment['id']);?>' class='btn btn-secondary btn-sm' data-bs-toggle='tooltip'  title='<?php echo $this->lang->line('download'); ?>'><i class='fa fa-download'></i></a>
                                                    <?php
                                                }
                                                         ?>
                                                         <a href="javascript:void(0);" class="btn btn-secondary btn-sm <?php echo $print; ?> " data-bs-toggle="tooltip" data-loading-text="<i class='fa fa-circle-o-notch fa-spi'></i>" data-record-id="<?php echo $payment['id']; ?>"  title="<?php echo $this->lang->line('print'); ?>">
                                                                    <i class="fa fa-print"></i>
                                                                </a> 
                    </td>
                </tr>
                                        <?php                                       
                                         } }?>  
                 <tr class="total-bg fw-bold">
                    <td></td>
                    <td></td>
                    <td class="text-end"><?php echo $this->lang->line('total'); ?></td>
                    <td class="text-end"><?php echo $currency_symbol.amountFormat($total); ?></td>
                    <td></td>                                 
                </tr>
                </tbody>
            </table>                        
        </div>
    </div>
    </div>
</div>
