<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
?>
<div class="fixed-print-header">
        <?php if (!empty($print_details[0]['print_header'])) { ?>
                        <div>
                            <img src="<?php
                            if (!empty($print_details[0]['print_header'])) {
                                echo $this->media_storage->getImageURL($print_details[0]['print_header']);
                            }
                            ?>" class="img-fluid sh-avatar-cover" >
                        </div>
                    <?php } ?>
</div>  
<table class="table-print-full" width="100%">
    <thead>
        <tr>
            <td><div class="header-space">&nbsp;</div></td>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>
      <div class="content-body">                    
<div class="print-area"  style="padding:15px">
<?php 
$discont_amt=calculatePercent($result['amount'], $result['discount_percentage']);
$total_discount_amount = $result['amount'] - $discont_amt;
$tax_amt=calculatePercent($total_discount_amount, $result['tax_percentage']);
?>
<div class="row">
        <div class="col-12">
           <div class="card">
                <div class="card-body">  
                    <div class="row">                       
                        <div class="col-md-6">
                            <table class="noborder_table mb-0">
                                <tr>
                                    <th width="15%"><?php echo $this->lang->line('bill_no'); ?></th>
                                    <td width="35%"><?php echo $prefix.$result['id']; ?></td>
                                </tr>
                                <tr>
                                    <th width="15%"><?php echo $this->lang->line('patient_name'); ?></th>
                                    <td width="35%"><?php echo $result['patient_name']." (".$result['patient_id'].")"; ?></td>
                                </tr>
                                <tr>
                                    <th width="15%"><?php echo $this->lang->line('blood_group'); ?></th>
                                    <td width="35%"><?php echo $result['blood_group']; ?></td>
                                </tr>
                                <tr>
                                    <th width="15%"><?php echo $this->lang->line('donor_name'); ?></th>
                                    <td width="35%"><?php echo $result["donor_name"]; ?></td>
                                </tr>
                             </table>
                        </div>
                        <div class="col-md-6">
                            <table class="noborder_table mb-0">
                                <tr>
                                    <th width="15%"><?php echo $this->lang->line('issue_date'); ?></th>
                                    <td width="35%"><?php echo $this->customlib->YYYYMMDDHisTodateFormat($result['date_of_issue'], $this->customlib->getHospitalTimeFormat()); ?></td>
                                </tr>
                                <tr>
                                    <th width="15%"><?php echo $this->lang->line('case_id'); ?></th>
                                    <td width="35%"><?php echo $result['case_reference_id']; ?></td>
                                </tr>
                                <tr>
                                    <th width="15%"><?php echo $this->lang->line('bag'); ?></th>
                                    <td width="35%"><?php echo $this->customlib->bag_string($result['bag_no'],$result['volume'],$result['unit_name']); ?></td>
                                </tr>
                             </table>
                        </div>                  
                    </div>
                    
                    <div class="row">                       
                        <div class="col-md-12">
                            <table class="noborder_table mb-0">
                    
                                <?php    if (!empty($fields)) {
                                foreach ($fields as $fields_key => $fields_value) {
                                  $display_field = $result["$fields_value->name"];
                                  if ($fields_value->type == "link") {
                                      $display_field = "<a href=" . $result["$fields_value->name"] . " target='_blank'>" . $result["$fields_value->name"] . "</a>";
                                  }
                                   ?>
                                    <tr>
                                        <th width="15%"><?php echo $fields_value->name; ?></th> 
                                        <td><?php echo $display_field; ?></td> 
                                    </tr>
                                <?php  }
                                } ?>
                              
                            </table>
                        </div>                  
                    </div>  
                    <div class="row">
                        <div class="col-md-12">
                            <table class="print-table mb-0">
                             <thead>
                                <tr class="line">
                                   <td>#</td>
                                   <td class="text-start"><?php echo $this->lang->line('description'); ?></td>                                 
                                   <td class="text-center"><?php echo $this->lang->line('tax'); ?> (%)</td>
                                   <td class="text-end"><?php echo $this->lang->line('amount').' ('. $currency_symbol .')'; ?></td>
                                </tr>
                             </thead>
                             <tbody>
                                <tr>
                                   <td>1</td>
                                   <td><?php echo $result['charge_category_name'];?><br></td>
                                    <td class="text-center"><?php echo $tax_amt." (".$result['tax_percentage']."%) ";?><br>
                                    </td>                                 
                                   <td class="text-end"><?php echo $result['amount'] ?></td>
                                </tr>
                                <tr><td colspan="4" class="p-0 p-0" ></td></tr>
                                <tr>
                                   <td colspan="2" class="no-line"></td>
                                   <td class="text-end no-line"><?php echo $this->lang->line('total'); ?></td>
                                   <td class="text-end no-line"><?php echo $currency_symbol . "" . amountFormat($result['amount']); ?></td>
                                </tr>
                                <tr>
                                   <td colspan="2" class="no-line"></td>
                                   <td class="text-end no-line"><?php echo $this->lang->line('discount'); ?></td>
                                   <td class="text-end no-line"><?php echo $currency_symbol . "" . $discont_amt. " (".$result['discount_percentage']."%) "; ?></td>
                                </tr>
                                <tr>
                                   <td colspan="2" class="no-line"></td>
                                   <td class="text-end no-line"><?php echo $this->lang->line('tax'); ?></td>
                                   <td class="text-end no-line"><?php echo $currency_symbol . "" . $tax_amt ." (".$result['tax_percentage']."%) "; ?></td>
                                </tr>
                                <tr>
                                   <td colspan="2" class="no-line"></td>
                                   <td class="text-end no-line"><?php echo $this->lang->line('paid'); ?></td>
                                   <td class="text-end no-line">
                                    <?php echo $currency_symbol . "" . amountFormat($result['total_deposit']); ?>
                                    </td>
                                </tr>
                                  <tr>
                                   <td colspan="2" class="no-line"></td>
                                   <td class="text-end no-line"><?php echo $this->lang->line('total_due'); ?></td>
                                   <td class="text-end no-line">
                                    <?php echo $currency_symbol . "" . amountFormat(($result['amount']-$discont_amt)+$tax_amt-$result['total_deposit']); ?>

                                    </td>
                                </tr>
                             </tbody>
                          </table>
                        </div>
                    </div>    
                </div>
            </div>
        </div>
    </div>
</div>
</div>
    </td></tr></tbody>
    <tfoot><tr><td>

<?php
                if (!empty($print_details[0]['print_footer'])) {
                    ?>
   <div class="footer-space">&nbsp;</div>
<?php
}
?>



</td></tr></tfoot>
</table>
<?php
                if (!empty($print_details[0]['print_footer'])) {
                    ?>
<div class="footer-fixed">

<?php   echo $print_details[0]['print_footer'];?>
            
</div>
<?php
}
?>