<?php 
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
 ?>
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12">                      
                        <input name="pharmacy_bill_basic_id" id="pharmacy_bill_basic_id" type="hidden" class="form-control" value="<?php echo $bill['id']?>" />   
						<input name="patient_id" id="patienteditid" type="hidden" class="form-control" value="<?php echo $bill['patient_id'] ?>" />  
						<input name="action_type" id="action_type" value="update" type="hidden" class="form-control"/>
                           <a class="btn btn-sm btn-info float-end add-record" data-added="0"><i class="fa fa-plus"></i> <?php echo $this->lang->line('add'); ?></a>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="table-responsive">
                                        <table class="table tableover table-striped table-bordered table-hover tablefull12 tblProducts" id="tableID">
                                            <thead>
                                             <tr class="font13">
                                                    <th width="15%"><?php echo $this->lang->line('test') . " " . $this->lang->line('name'); ?><small class="req sh-text-danger" > *</small></th>
                                                    <th width="15%"><?php echo $this->lang->line('report') . " " . $this->lang->line('day'); ?></th>
                                                    <th width="15%"><?php echo $this->lang->line('report') . " " . $this->lang->line('date'); ?><small class="req sh-text-danger" > *</small></th>
                                                    <th class="text-end" width="10%"><?php echo $this->lang->line('amount') . " (" . $currency_symbol . ")"; ?></th>
                                                    <th class="text-end" width="2%"></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            	<?php 
  $row_value=1;
    foreach ($detail as $bill_detail_key => $bill_detail_value) {        
	?>
<input type="hidden" name="previous_ids[]" value="<?php echo $bill_detail_value['id'];?>">
<tr id="row<?php echo $row_value?>">
    <td>
        <input type="hidden" name="insert_id_<?php echo $row_value;?>" value="<?php echo $bill_detail_value['id'];?>">
        <input type="hidden" name="total_rows[]" value="<?php echo $row_value;?>">
         <select class="form-control medicine_category select2 w-100"  name='medicine_category_id_<?php echo $row_value;?>'>
         <option value=""><?php echo $this->lang->line('select') ?></option>
                    <?php foreach ($testlist as $key => $value) {
                        ?>
         <option value="<?php echo $value["id"]; ?>" <?php echo set_select('test_name_id_<?php echo $row_value;?>', $value['id'], ($value["id"] == $bill_detail_value['test_name']) ? TRUE :FALSE); ?>><?php echo $value["test_name"] ?>
                          </option>
                      <?php 
                  }
                      ?>
                                                    </select>
                                                    <span class="text-danger"><?php echo form_error('test_name_id[]'); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <input type="text" readonly name="report_day_<?php echo $row_value;?>" id="report_day0" class="form-control exp_date"/>
                                                </td>                                                
                                                <td>
                                                    <input type="text" name="report_date_<?php echo $row_value;?>" id="report_date0" class="form-control exp_date"/>
                                                </td>
                                                <td class="text-end">
                                                    <input type="text" name="amount_<?php echo $row_value;?>" id="amount0" placeholder="" class="form-control text-end subtot" readonly>
                                                    <span class="text-danger"><?php echo form_error('amount[]'); ?></span>
                                                </td>
                                                <td><button type="button" class="closebtn delete_row" data-row-id="<?php echo $row_value;?>" autocomplete="off"><i class="fa fa-remove"></i></button></td>
                                            </tr>
	<?php 
    $row_value++;
}
                                            	 ?>                                            
                                        </tbody>
                                        </table>
                                    </div>
                                    <div class="divider"></div>                                
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <div class="row">
                                                        <div class="col-sm-6">
                                                            <div class="mb-3">
                                                                <label for="exampleInputFile">
                                                                    <?php echo $this->lang->line('hospital_doctor'); ?></label>
                                                                <div>
    <select name='consultant_doctor' id="consultant_doctor" onchange="get_Docname(this.value)" class="form-control select2 w-100" <?php
if ($disable_option == true) {
    echo "disabled";
}
?> class="w-100"  >
                                                                        <option value=""><?php echo $this->lang->line('select') ?></option>
                                                                        <?php foreach ($doctors as $dkey => $dvalue) {
    ?>
                                                                            <option value="<?php echo $dvalue["id"]; ?>" <?php
if ((isset($doctor_select)) && ($doctor_select == $dvalue["id"])) {
        echo "selected";
    }
    ?>><?php echo $dvalue["name"] . " " . $dvalue["surname"] ?></option>
<?php }?>
                                                                    </select>
                                                                </div>
                                                                <span class="text-danger"><?php echo form_error('refference'); ?></span>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-6">
                                                            <div class="mb-3">
                <label><?php echo $this->lang->line('doctor') . " " . $this->lang->line('name'); ?></label>
                <input name="doctor_name" id="doctname" type="text" class="form-control" value="<?php echo $bill['doctor_name']?>" />
                                                                <span class="text-danger"><?php echo form_error('doctor_name'); ?></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-12">
                                                    <div class="mb-3">
                                                        <label><?php echo $this->lang->line('note'); ?></label>
                                                        <textarea name="note" rows="3" id="note" class="form-control"></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                        </div><!--./col-sm-6-->
                                        <div class="col-sm-6">
                                            <table class="printablea4">
                                                <tr>
                                                    <th width="40%"><?php echo $this->lang->line('total') . " (" . $currency_symbol . ")"; ?></th>
                                            <td width="60%" colspan="2" class="text-end ipdbilltable">
                <input type="text" placeholder="<?= $this->lang->line('total') ?>" value="<?php echo $bill['total'];?>" name="total" id="total" class="form-control total sh-print-right-30" /></td>
                                                </tr>
                                                <tr>
                                                 <th><?php echo $this->lang->line('discount') . " (" . $currency_symbol . ")"; ?></th>
                                                    <td class="text-end ipdbilltable">
                                                        <h4 class="sh-print-right-70"> %</h4>
                                                <input type="text" placeholder="<?= $this->lang->line('discount') ?>" name="discount_percent" id="discount_percent" class="form-control discount_percent" value="<?php echo $bill['tax_percentage'] ?>" class="sh-print-right-70"/></td>
                                         <td class="text-end ipdbilltable">
                                        <input type="text" placeholder="<?= $this->lang->line('discount') ?>" value="<?php echo $bill['discount'];?>" name="discount" id="discount" class="form-control discount sh-print-right-50" /></td>
                                                </tr>
                                                <tr>
                                                    <th><?php echo $this->lang->line('tax') . " (" . $currency_symbol . ")"; ?></th>
                                                    <td class="text-end ipdbilltable">
                                                        <h4 style="float: right;font-size: 12px;padding-left: 5px;"> %</h4><input type="text" placeholder="<?= $this->lang->line('tax') ?>" name="tax_percent" value="<?php echo $bill['tax_percentage'] ?>" id="tax_percent" class="form-control tax_percent sh-print-right-70" />
                                                    </td>
                                                    <td class="text-end ipdbilltable">
                                                        <input type="text" placeholder="<?= $this->lang->line('tax') ?>" name="tax" value="<?php echo $bill['tax'] ?>" id="tax" class="form-control tax sh-print-right-50" />
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th><?php echo $this->lang->line('net_amount') . " (" . $currency_symbol . ")"; ?></th>
                                                    <td colspan="2" class="text-end ipdbilltable">
                                                        <input type="text" placeholder="<?= $this->lang->line('net_amount') ?>" value="<?php echo $bill['net_amount'] ?>" name="net_amount" id="net_amount" class="form-control net_amount sh-print-right-30" /></td>
                                                </tr>
                                            </table>
                                          </div>
                                    </div><!--./row-->
                                </div><!--./col-md-12-->
                            </div><!--./row-->
                        </div><!--./card-footer-->
                    </div><!--./col-md-12-->