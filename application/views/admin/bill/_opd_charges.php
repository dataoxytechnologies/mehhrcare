<?php 
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
 if (!empty($charges_detail)) {
?>
    <div class="d-flex gap-2 align-items-center flex-wrap justify-content-end mb-2 box-miustop">
        <?php if ($this->rbac->hasPrivilege('opd_billing_payment', 'can_add')) {?>
            <a href="javascript:void(0);" data-loading-text='' data-caseid='<?php echo $charges_detail[0]["case_reference_id"]; ?>' data-module='opd' data-totalamount='' data-record-id='<?php echo $charges_detail[0]["opd_id"]; ?>'  data-bs-toggle='tooltip' title=''    class="btn btn-primary btn-sm add_payment"  ><i class="fa fa-credit-card me-1"></i><?php echo $this->lang->line('add_payment'); ?></a>
           <?php } if ($this->rbac->hasPrivilege('opd_billing_payment', 'can_view')) {?>
             <a href="javascript:void(0);" data-loading-text='' data-case_id='<?php echo $charges_detail[0]["case_reference_id"]; ?>' data-module_type='opd_id' data-record-id='<?php echo $charges_detail[0]["opd_id"]; ?>'  data-bs-toggle='tooltip' title=''    class="btn btn-primary btn-sm view_payment"  ><i class="fa fa-history me-1"></i><?php echo $this->lang->line('view_payments'); ?></a>
              <?php } if ($this->rbac->hasPrivilege('generate_bill', 'can_view')) { ?>
            <a href="javascript:void(0);"   class="btn btn-primary btn-sm text-end view_generate_bill"  data-bs-toggle="tooltip" title="" data-module_type='ipd_opd' data-case_id='<?php echo $charges_detail[0]["case_reference_id"]; ?>'><i class="fas fa-exchange-alt"></i> <?php echo $this->lang->line('generate_bill'); ?></a>
        <?php } ?>
    </div>
<?php } ?>

<div class="table-responsive">
    <table class="table table-striped table-bordered table-hover bill-charges-table">
        <thead>
        <tr>
            <th><?php echo $this->lang->line('date'); ?></th>
            <th><?php echo $this->lang->line('charge_name'); ?> / <?php echo $this->lang->line('charge_note'); ?></th>
            <th><?php echo $this->lang->line('charge_type'); ?></th>
            <th><?php echo $this->lang->line('charge_category'); ?></th>
            <th><?php echo $this->lang->line('qty'); ?></th>
            <th><?php echo $this->lang->line('standard_charge') . ' (' . $currency_symbol . ')'; ?></th>
            <th><?php echo $this->lang->line('applied_charge') . ' (' . $currency_symbol . ')'; ?></th>
            <th><?php echo $this->lang->line('tpa_charge') . ' (' . $currency_symbol . ')'; ?></th>
            <th><?php echo $this->lang->line('discount'); ?></th>
            <th class="text-end"><?php echo $this->lang->line('tax'); ?></th>
            <th class="text-end"><?php echo $this->lang->line('amount') . ' (' . $currency_symbol . ')'; ?></th>
            <th class="text-end text-rtl-left noExport"><?php echo $this->lang->line('action'); ?></th>
        </tr>
        </thead>
        <tbody>
         <?php 
            $total =$amount=$total_tax =0;$tax=0;
            if (!empty($charges_detail)) {
                foreach ($charges_detail as $charges_key => $charges_value) {
						$discount_amount = amountFormat(($charges_value['apply_charge']*$charges_value['discount_percentage']/100)) ;						
                        $total += $charges_value["apply_charge"];
                        $amount += $charges_value["amount"];
                        $tax=0;
                        if($charges_value["tax"]>0){
                            $tax_raw=($charges_value["apply_charge"]-(($charges_value["apply_charge"]*$charges_value["discount_percentage"])/100));
                            $tax=(($tax_raw*$charges_value["tax"])/100);
                        }
                        ?>  
                        <tr>                           
                            <td><?php if($charges_value['date']!='' && $charges_value['date']!='0000-00-00'){ echo $this->customlib->YYYYMMDDHisTodateFormat($charges_value['date'], $this->customlib->getHospitalTimeFormat()); } ?></td>
                              <td class="">
                                <?php echo $charges_value["name"]; ?>
                                 <div class="bill_item_footer text-muted"> <?php echo $charges_value["note"]; ?></div>
                             </td>
                            <td class="text-capitalize"><?php echo $charges_value["charge_type"] ?></td>
                            <td class="text-capitalize"><?php echo $charges_value["charge_category_name"] ?></td>
                            <td class="text-capitalize"><?php echo $charges_value['qty']." ".$charges_value["unit"]; ?></td>
                            <td class="text-capitalize"><?php echo $charges_value["standard_charge"] ?></td>
                            <td class="text-capitalize"><?php echo $charges_value["apply_charge"] ?></td>                            
                            <td class="text-capitalize"><?php echo $charges_value["tpa_charge"] ?></td>                            
                            <td class="text-capitalize"><?php echo amountFormat(($discount_amount))." (".$charges_value["discount_percentage"]."%) " ;?></td>						
                            <td class="text-end"><?php echo amountFormat($tax)." (".$charges_value["tax"]."%)"; ?></td>
                            <td class="text-end"><?php echo amountFormat($charges_value["amount"]) ?></td>
                            <td class="text-end text-rtl-left"> 
                                <a href="javascript:void(0);" class="btn btn-secondary btn-sm print_charge" data-bs-toggle="tooltip" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i>" data-record-id="<?php echo $charges_value['id']; ?>"  data-moduletype='opd' title="<?php echo $this->lang->line('print'); ?>"><i class="fa fa-print"></i></a>
                            </td>
                        </tr>
                        <?php
                }
            }
            $total_tax+=$tax;
            ?>
        </tbody>
        <?php if (!empty($charges_detail)): ?>
        <tfoot>
            <tr class="bill-total-row">
                <td colspan="10" class="text-end pe-3"><?php echo $this->lang->line('total'); ?></td>
                <td class="text-end fw-bold"><?php echo $currency_symbol . amountFormat($amount); ?></td>
                <td></td>
            </tr>
        </tfoot>
        <?php else: ?>
        <tfoot>
            <tr>
                <td colspan="12" class="text-center py-4">
                    <div class="bill-empty-state">
                        <i class="fas fa-receipt bill-empty-icon"></i>
                        <div class="mt-2"><?php echo $this->lang->line('no_record_found'); ?></div>
                    </div>
                </td>
            </tr>
        </tfoot>
        <?php endif; ?>
    </table>
</div> 