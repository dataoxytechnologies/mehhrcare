<?php 
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
if($module=='radiolog'){
 $print='print_receipt';
}elseif($module=='pathology'){
 $print='print_trans';
}else{
 $print='print_trans'; 
}
?>
<div class="sh-form-card mb-0">
    <div class="sh-card-header">
        <span class="sh-card-header-title"><i class="fa fa-money me-1"></i><?php echo $this->lang->line('payments'); ?></span>
    </div>
    <div class="table-responsive">
        <table class="table table-striped table-bordered table-hover example mb-0">
                <thead>
                    <th><?php echo $this->lang->line('date'); ?></th>
                    <th><?php echo $this->lang->line('note'); ?></th>
                    <th><?php echo $this->lang->line('payment_mode'); ?></th>
                    <th><?php echo $this->lang->line('payment_type'); ?></th>
                    <th class="text-end"><?php echo $this->lang->line('paid_amount') . " (" . $currency_symbol . ")"; ?></th>
                    <th class="text-center"><?php echo $this->lang->line('action'); ?></th>
                </thead>
                <tbody>
                <?php
                $total = 0;$refund_total=0;
                    if (!empty($payment_details)) {                        
                        foreach ($payment_details as $payment) {                           
                            if (!empty($payment['amount']) && $payment['type']!='refund' ) {
                                $total += $payment['amount'];
                            }
                            if($payment['type']=='refund'){
                                $refund_total += $payment['amount'];
                            }
                            ?>
                            <tr>
                                <td><?php echo $this->customlib->YYYYMMDDHisTodateFormat($payment['payment_date'], $this->customlib->getHospitalTimeFormat()); ?></td>
                                <td><?php echo html_escape($payment["note"]) ?></td>
                              <td><?php if(!empty($payment["payment_mode"])){ echo $this->lang->line(strtolower($payment["payment_mode"]))."<br>"; }
							  
                                if($payment['payment_mode'] == "Cheque"){
                                     if($payment['cheque_no']!=''){
               echo $this->lang->line("cheque_no"). ": ".html_escape($payment['cheque_no']);              
            echo "<br>";
        }
            if($payment['cheque_date']!='' && $payment['cheque_date']!='0000-00-00'){
               echo $this->lang->line("cheque_date"). ": ".$this->customlib->YYYYMMDDTodateFormat($payment['cheque_date']);
           } 
             }                                ?>
                                </td> 
                                <td><?php echo $this->lang->line($payment["type"]); ?></td>
                                <td class="text-end"><?php echo amountFormat($payment["amount"]) ?></td>
                                <td class="text-center">
                                <?php  if ($payment['payment_mode'] == "Cheque" && $payment['attachment'] != "")  {
                            ?>
                            <a href='<?php echo site_url('patient/dashboard/downloadreceipt/'.(int)$payment['id']);?>' class='btn btn-default btn-sm'  title='<?php echo $this->lang->line('download'); ?>'><i class='fa fa-download'></i></a>
                            <?php
                        }
                                 ?>
                                 <a href="javascript:void(0);"  class="btn btn-default btn-sm <?php echo $print; ?> " data-bs-toggle="tooltip" title="<?php echo $this->lang->line('print'); ?>" data-loading-text="<i class='fa fa-circle-o-notch fa-spi'></i>" data-record-id="<?php echo (int)$payment['id']; ?>" data-module-type="<?php echo html_escape($module); ?>">
                                            <i class="fa fa-print"></i>
                                        </a> 
                                    </td>
                            </tr>
                <?php               
                 } }?>  
                        <tr class="box box-solid total-bg">
                            <td></td>
                            <td></td>
                            <td></td>
                            <td class="text-end"><?php echo $this->lang->line('total'); ?></td>
                            <td class="text-end"><?php echo $currency_symbol.amountFormat($total- $refund_total); ?></td>
                            <td></td>   
                        </tr>
                    </tbody>
        </table>
    </div>
</div>
