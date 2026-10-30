<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
?>

<!-- Main content -->
<div class="modal fade sh-modal sh-modal-branded" id="viewDetailReportModal" tabindex="-1" aria-labelledby="viewDetailReportModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewDetailReportModalLabel"><?php echo $this->lang->line('bill_details'); ?></h5>
                <div class="d-flex align-items-center gap-2" id="action_detail_report_modal"></div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="pup-scroll-area">
                <div class="modal-body"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('close'); ?></button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade sh-modal sh-modal-branded" id="viewModalBill" tabindex="-1" aria-labelledby="viewModalBillLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewModalBillLabel"><?php echo $this->lang->line('bill_details'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="pup-scroll-area">
                <div class="modal-body">
                    <div id="reportdata"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('close'); ?></button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade sh-modal sh-modal-branded" id="viewModal" tabindex="-1" aria-labelledby="viewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewModalLabel"><?php echo $this->lang->line('bill_details'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="pup-scroll-area">
                <div class="modal-body">
                    <div id="pharmacy_reportdata"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('close'); ?></button>
            </div>
        </div>
    </div>
</div>

    <div class="row">
            <div class="col-md-12">                
                <div class="card">
                    <?php $this->load->view('admin/report/_patient');?>
                    <div class="card-header ptbnull"></div>
                    <div class="card-header">                        
                        <h3 class="card-title"><?php echo  $this->lang->line("patient_visit_report"); ?></h3>
                    </div>
                        <div class="card-body pb0">                       
                            <form action="<?= base_url(); ?>admin/patient/patientvisitreport" method="post" class="pt-2">
                                <div class="row g-3 align-items-end">
                                    <input type="hidden" name="ci_csrf_token" value="">
                                    <div class="col-sm-6 col-md-3">
                                        <label class="form-label" for="patient_id_input"><?php echo $this->lang->line('patient_id'); ?></label><small class="req"> *</small>
                                        <input id="patient_id_input" name="patient_id" placeholder="<?php echo $this->lang->line('patient_id'); ?>" type="text" class="form-control" value="<?php echo set_value('patient_id'); ?>" />
                                        <span class="text-danger"><?php echo form_error('patient_id'); ?></span>
                                    </div>
                                    <div class="col-sm-6 col-md-2 d-flex align-items-end">
                                        <button type="submit" name="search" value="search_filter" class="btn btn-primary d-inline-flex align-items-center gap-1 py-2 checkbox-toggle">
                                            <i class="fa fa-search"></i>
                                            <?php echo $this->lang->line('search'); ?>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>                     
                    <div id="visit_report">
                        <div class="card-body table-responsive pt-0">
                            <div id="printhead">
                                <h5><?php echo $this->lang->line("patient_visit_report") . "<br>"; ?></h5>
                            </div>
                            <div>
                                <div class="card-header" id="headreport" class="d-none">
                                    <h3 class="card-title text-center"><?php if(!empty($patient_name)){
                                echo composePatientName($patient_name,$patient_id) .' '.$this->lang->line("visit_details"); } ?></h3>
                              </div>
                            </div>
                            <div class="download_label"><?php echo $this->lang->line('opd_report'); ?></div>
                            <div id="excel_print_div" class="ptt10">
                                <a class="btn btn-secondary btn-sm float-end" id="print"  data-bs-toggle="tooltip" title="<?php echo $this->lang->line('print'); ?>" onclick="printDiv()"><i class="fa fa-print"></i></a> 
                                <a class="btn btn-secondary btn-sm float-end" id="btnExport"  data-bs-toggle="tooltip" title="<?php echo $this->lang->line('export_to_excel'); ?>" onclick="tablesToExcel(array1, array2, array3, array4, array5, array6, array7, 'myfile.xls');"> <i class="fa fa-file-excel-o"></i> </a>
                            </div>
                            <table class="table table-striped table-bordered table-hover allajaxlist" id="1">
                                <caption><h4><?= $this->lang->line("opd_details"); ?></h4></caption>
                                <thead>
                                    <tr>
                                        <th><?php echo $this->lang->line('opd_no'); ?></th>
                                        <th><?php echo $this->lang->line('case_id'); ?></th>
                                        <th><?php echo $this->lang->line('date'); ?></th>
                                        <th><?php echo $this->lang->line('checkup_id'); ?></th>
                                        <th><?php echo $this->lang->line('doctor_name'); ?></th>
                                        <th width="20%"><?php echo $this->lang->line('symptoms'); ?></th>
                                        <th width="20%"><?php echo $this->lang->line('findings'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if (!empty($opd_data)) {
                                        foreach ($opd_data as  $value) {

                                             if ($value['case_reference_id'] > 0) {
                                                $case_id = $value['case_reference_id'];
                                            } else {
                                                $case_id = '';
                                            }
                                    ?>
                                            <tr>
                                                <td><?php echo $this->customlib->getSessionPrefixByType('opd_no') . $value['id']; ?></td>
                                                <td><?php echo $case_id ; ?></td>
                                                <td><?php if($value['appointment_date']){ echo $this->customlib->YYYYMMDDTodateFormat($value['appointment_date']); } ?></td>
                                                <td><?php echo $this->customlib->getSessionPrefixByType('checkup_id') . $value['visit_id']; ?></td>
                                                <td><?php echo  $value['name'] . " " . $value['surname'] . "(" . $value['employee_id'] . ")"; ?></td>
                                                <td><?php echo $value['symptoms']; ?></td>
                                                <td><?php echo $value['finding_description']; ?></td>
                                            </tr>
                                           
                                     <?php   }
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="card-body table-responsive">
                            <div class="download_label"><?php echo $this->lang->line('ipd_report'); ?></div>
                            <table class="table table-striped table-bordered table-hover allajaxlist" id="2">
                            <caption><h4><?= $this->lang->line("ipd_details"); ?></h4></caption>
                                <thead>
                                   <tr>
                                        <th><?php echo $this->lang->line('ipd_no'); ?></th>
                                        <th><?php echo $this->lang->line('case_id'); ?></th>
                                        <th width="8%"><?php echo $this->lang->line('date'); ?></th>
                                        <th><?php echo $this->lang->line('doctor_name'); ?></th>
                                        <th width="20%" ><?php echo $this->lang->line('symptoms'); ?></th>
                                        <th width="20%" ><?php echo $this->lang->line('findings'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>

                                    <?php
                                    if (!empty($ipd_data)) {
                                        foreach ($ipd_data as $key => $value) {

                                            if ($value['case_reference_id'] > 0) {
                                                $case_id = $value['case_reference_id'];
                                            } else {
                                                $case_id = '';
                                            }
                                    ?>
                                            <tr>  
												<td><?= $this->customlib->getSessionPrefixByType('ipd_no') . $value['id']; ?></td>
												<td><?php echo $case_id ; ?></td>
												<td><?php if($value['date']){ echo $this->customlib->YYYYMMDDTodateFormat($value['date']); } ?></td>
                                                <td><?php echo  $value['name'] . " " . $value['surname'] . "(" . $value['employee_id'] . ")";?></td>
                                                <td><?php echo $value['symptoms'] ; ?></td>
                                                <td><?php echo $value['finding_description'];  ?></td>
                                            <tr>                                          
                                    <?php
                                        }
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="card-body table-responsive">
                            <div class="download_label"><?php echo $this->lang->line('pharmacy_report'); ?></div>
                            <table class="table table-striped table-bordered table-hover allajaxlist" id="3">
                            <caption><h4><?= $this->lang->line("pharmacy_details"); ?></h4></caption>
                                <tr>
                                    <th><?php echo $this->lang->line('bill_no'); ?></th>
                                    <th><?php echo $this->lang->line('case_id'); ?></th>
                                    <th><?php echo $this->lang->line('date'); ?></th>
                                    <th class="text-end"><?php echo $this->lang->line('amount') . " " . '(' . $currency_symbol . ')'; ?></th>
                                    <th class="text-end"><?php echo $this->lang->line('discount') . " " . '(%)'; ?></th>
                                    <th class="text-end"><?php echo $this->lang->line('tax') . " " . '(%)'; ?></th>
                                    <th class="text-end"><?php echo $this->lang->line('net_amount')  . " " . '(' . $currency_symbol . ')'; ?></th>
                                    <th class="text-end"><?php echo $this->lang->line('paid_amount')  . " " . '(' . $currency_symbol . ')'; ?></th>
                                    <th class="text-end"><?php echo $this->lang->line('refund_amount')  . " " . '(' . $currency_symbol . ')'; ?></th>
                                    <th class="text-end"><?php echo $this->lang->line("balance_amount") . " " . '(' . $currency_symbol . ')'; ?></th>
                                </tr>
                                <tbody>
                                    <?php
                                    if (!empty($pharmacy_data)) {

                                        $total_net= 0 ;$total_paid=0;$total_balance=0;$total_discount=0;$total_discount_percent=0;$total_refund_amount=0; $total_amt=0; $total_tax= 0;
                                       
                                        foreach ($pharmacy_data as $value) {
                                            
                                            $total_amt+= $value['total'];
                                            $balance_amount = ($value['net_amount']-($value['paid_amount']-$value['refund_amount']));
                                            $total_net+= $value['net_amount'] ;
                                            $total_paid+= $value['paid_amount'] ;
                                            $total_balance+= $balance_amount ;
                                            $total_discount+= $value['discount'];                                            
                                            $total_refund_amount+= $value['refund_amount'];
                                            $total_tax+= $value['tax'];

                                            if ($value['case_reference_id'] > 0) {
                                                $case_id = $value['case_reference_id'];
                                            } else {
                                                $case_id = '';
                                            }
                                    ?>
                                            <tr>
                                                <td><?php echo $this->customlib->getSessionPrefixByType('pharmacy_billing') . $value['id']; ?></td>
                                                <td><?php echo $case_id ; ?></td>
                                                <td><?php echo $this->customlib->YYYYMMDDHisTodateFormat($value['date'], $this->customlib->getHospitalTimeFormat()); ?></td>
                                                <td class="text-end"><?php echo amountFormat($value['total']); ?></td>
                                                <td class="text-end"><?php echo amountFormat($value['discount'])." (".$value['discount_percentage']."%)"; ?></td>
                                                <td class="text-end"><?php                                                 
                                                $tax_base = $value['total'] - $value['discount'];
                                                $tax_percentage = $tax_base != 0 ? number_format((float)(($value['tax']*100) / $tax_base), 2, '.', '') : '0.00';                                               
                                                echo $value['tax']." (".$tax_percentage."%)"; ?></td>
                                                <td class="text-end"><?php echo amountFormat($value['net_amount']); ?></td>
                                                <td class="text-end"><?php echo  number_format((float)$value['paid_amount']-$value['refund_amount'], 2, '.', ''); ?></td>
                                                <td class="text-end"><?php echo  number_format((float)$value['refund_amount'], 2, '.', ''); ?></td>
                                                <td class="text-end"><?php echo  number_format((float)$balance_amount, 2, '.', '');; ?><div  class='rowoptionview'><a href='javascript:void(0)'  data-loading-text=' ' data-record-id='<?php echo $value['id']; ?>' class='btn btn-secondary btn-sm ' onclick="viewDetail(<?php echo $value['id']; ?>)"  data-module-type="pharmacy"  data-bs-toggle='tooltip' title='<?php echo $this->lang->line('view'); ?>' ><i class='fa fa-reorder'></i></a></div></td>
                                            </tr>                                            
                                    <?php
                                        } ?>
                                        <tr>
                                            <td colspan="2"></td>
                                            <td><b><?= $this->lang->line("total"); ?></b></td>
                                            <td class="text-end"><b><?php echo $currency_symbol.number_format($total_amt,2); ?></b></td>
                                            <td class="text-end"><b><?php echo $currency_symbol.number_format($total_discount,2); ?></b></td>
                                            <td class="text-end"><b><?php echo $currency_symbol.number_format($total_tax,2); ?></b></td>
                                            <td class="text-end"><b><?php echo $currency_symbol.number_format($total_net,2); ?></b></td>
                                            <td class="text-end"><b><?php echo $currency_symbol.number_format($total_paid,2); ?></b></td>
                                            <td class="text-end"><b><?php echo $currency_symbol.number_format($total_refund_amount,2); ?></b></td>
                                            <td class="text-end"><b><?php echo $currency_symbol.number_format($total_balance,2); ?></b></td>
                                        </tr>
                                    <?php }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="card-body table-responsive">
                            <div class="download_label"><?php echo $this->lang->line('pathology_report'); ?></div>
                            <table class="table table-striped table-bordered table-hover allajaxlist" id="4">
                            <caption><h4><?= $this->lang->line("pathology_details"); ?></h4></caption>
								<thead>
									<tr>
										<th><?php echo $this->lang->line('bill_no'); ?></th>
										<th><?php echo $this->lang->line('case_id'); ?></th>
										<th><?php echo $this->lang->line('date');  ?></th>
										<th class="text-end"><?php echo $this->lang->line('amount') . " (" . $currency_symbol . ")";  ?></th>				
										<th class="text-end"><?php echo $this->lang->line('discount') . " (%)";  ?></th>
										<th class="text-end"><?php echo $this->lang->line('tax') . " (%)";  ?></th>
										<th class="text-end"><?php echo $this->lang->line('net_amount') . " (" . $currency_symbol . ")";  ?></th>                    
										<th class="text-end"><?php echo $this->lang->line('paid_amount') . " (" . $currency_symbol . ")";  ?></th>
										<th class="text-end"><?php echo $this->lang->line('balance_amount')."(".$currency_symbol.")"; ?></th>
									</tr>
								</thead>
								<tbody>

								<?php
								if (!empty($pathology_data)) {  
									$total_net= 0 ;$total_paid=0;$total_balance=0;$total_discount=0;$total_discount_percent=0;$total_amount=0;$total_tax=0;
									foreach ($pathology_data as $value) {
				
										$balance_amount = ($value['net_amount']) - ($value['paid_amount']);
										$total_net+= $value['net_amount'];
										$total_paid+= $value['paid_amount'];
										$total_balance+= $balance_amount;
										$total_discount+= $value['discount'];
										$total_discount_percent+= $value['discount_percentage'];
										$total_amount+= $value['total'];
										$total_tax+= $value['tax'];
				
										if ($value['case_reference_id'] > 0) {
											$case_id = $value['case_reference_id'];
										} else {
											$case_id = '';
										}
								?>
										<tr>
											<td><?php echo $this->customlib->getSessionPrefixByType('pathology_billing') . $value['id']; ?></td>
											<td><?php echo $case_id ; ?></td>
											<td><?php echo $this->customlib->YYYYMMDDHisTodateFormat($value['date'], $this->customlib->getHospitalTimeFormat()); ?></td>
											<td class="text-end"><?php echo amountFormat($value['total']); ?></td>						
											<td class="text-end"><?php echo amountFormat($value['discount']).' ('.$value['discount_percentage'].'%)'; ?></td>						
											<td class="text-end"><?php  
												$tax_percentage = number_format(($value['tax']*100)/($value['total']-$value['discount']),2);						
												echo amountFormat($value['tax']).' ('.$tax_percentage.'%)'; ?></td>						
											<td class="text-end"><?php echo amountFormat($value['net_amount']); ?></td>
											<td class="text-end"><?php echo amountFormat($value['paid_amount']); ?></td>
											<td class="text-end"><?php echo number_format($balance_amount, 2); ?><div class='rowoptionview'><a href='javascript:void(0)'  data-loading-text=' ' data-record-id='<?php echo $value['id']; ?>' class='btn btn-secondary btn-sm view_detail' data-module-type="pathology"  data-bs-toggle="tooltip" title="<?php echo $this->lang->line('view'); ?>" ><i class='fa fa-reorder'></i></a></div></td>
										</tr>
								<?php
									}
									?>
									<tr>
										<td colspan="2"></td>
										<td><b><?= $this->lang->line("total_amount"); ?>: </b></td>
										<td class="text-end"><b><?php echo $currency_symbol.number_format($total_amount,2); ?></b></td>
										<td class="text-end"><b><?php echo $currency_symbol.number_format($total_discount,2); ?></b></td>
										<td class="text-end"><b><?php echo $currency_symbol.number_format($total_tax,2); ?></b></td>
										<td class="text-end"><b><?php echo $currency_symbol.number_format($total_net,2); ?></b></td>
										<td class="text-end"><b><?php echo $currency_symbol.number_format($total_paid,2); ?></b></td>
										<td class="text-end"><b><?php echo $currency_symbol.number_format($total_balance,2); ?></b></td>
									</tr>
								<?php }
								?>
								</tbody>
                            </table>
                        </div>
                        <div class="card-body table-responsive">
                            <div class="download_label"><?php echo $this->lang->line('radiology_report'); ?></div>
                            <table class="table table-striped table-bordered table-hover allajaxlist" id="5">
                            <caption><h4><?= $this->lang->line("radiology_details"); ?></h4></caption>
                                <thead>
									<tr>
										<th><?php echo $this->lang->line('bill_no'); ?></th>
										<th><?php echo $this->lang->line('case_id'); ?></th>
										<th><?php echo $this->lang->line('date'); ?></th>
										<th class="text-end"><?php echo $this->lang->line('amount') . " (" . $currency_symbol . ")";  ?></th>					
										<th class="text-end"><?php echo $this->lang->line('discount'). " (" . $currency_symbol . ")"; ?></th>
										<th class="text-end"><?php echo $this->lang->line('tax'). " (" . $currency_symbol . ")"; ?></th>
										<th class="text-end"><?php echo $this->lang->line('net_amount') . " (" . $currency_symbol . ")";  ?></th>
										<th class="text-end"><?php echo $this->lang->line('paid_amount') . " (" . $currency_symbol . ")";  ?></th>
										<th class="text-end"><?php echo $this->lang->line('balance_amount')."(".$currency_symbol.")"; ?></th>
									</tr>
								</thead>
								<tbody>
									<?php
									if (!empty($radiology_data)) { 
					
										$total_net= 0 ;$total_paid=0;$total_balance=0;$total_discount=0;$total_discount_percent=0;$total_amount=0;;$total_tax=0;
										foreach ($radiology_data as $value) {
					
											$balance_amount = ($value['net_amount']) - ($value['paid_amount']);
											$total_net+= $value['net_amount'] ;
											$total_paid+= $value['paid_amount'] ;
											$total_balance+= $balance_amount ;
											$total_discount+= $value['discount'];
											$total_discount_percent+= $value['discount_percentage'];
											$total_amount+= $value['total'];
											$total_tax+= $value['tax'];
					
											if ($value['case_reference_id'] > 0) {
												$case_id = $value['case_reference_id'];
											} else {
												$case_id = '';
											}
									?>
											<tr>
												<td><?php echo $this->customlib->getSessionPrefixByType('radiology_billing') . $value['id']; ?></td>
												<td><?php echo $case_id ; ?></td>
												<td><?php echo $this->customlib->YYYYMMDDHisTodateFormat($value['date'], $this->customlib->getHospitalTimeFormat()); ?></td>	
												<td class="text-end"><?php echo amountFormat($value['total']) ; ?></td>							
												<td class="text-end"><?php echo amountFormat($value['discount']).' ('.$value['discount_percentage'].'%)' ; ?></td>			
												<td class="text-end"><?php  $tax_percentage = number_format(($value['tax']*100)/($value['total']-$value['discount']),2);			
												echo amountFormat($value['tax']).' ('.$tax_percentage.')'; ?></td>							
												<td class="text-end"><?php echo amountFormat($value['net_amount']); ?></td>
												<td class="text-end"><?php echo amountFormat($value['paid_amount']); ?></td>
												<td class="text-end"><?php echo number_format($balance_amount, 2); ?><div class='rowoptionview'><a href='javascript:void(0)'  data-loading-text=' ' data-record-id='<?php echo $value['id']; ?>' class='btn btn-secondary btn-sm view_detail' data-module-type="radiology" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('view'); ?>" ><i class='fa fa-reorder'></i></a></div></td>
											</tr>                       
									<?php
										} ?>
										<tr>
											<td colspan="2"></td>
											<td><b><?= $this->lang->line("total_amount"); ?>: </b></td>
											<td class="text-end"><b><?php echo $currency_symbol.number_format($total_amount,2); ?></b></td>
											<td class="text-end"><b><?php echo $currency_symbol.number_format($total_discount,2); ?></b></td>						
											<td class="text-end"><b><?php echo $currency_symbol.number_format($total_tax,2); ?></b></td>
											<td class="text-end"><b><?php echo $currency_symbol.number_format($total_net,2); ?></b></td>
											<td class="text-end"><b><?php echo $currency_symbol.number_format($total_paid,2); ?></b></td>
											<td class="text-end"><b><?php echo $currency_symbol.number_format($total_balance,2); ?></b></td>
										</tr>
									<?php }
									?>
								</tbody>
                            </table>
                        </div>
                        <div class="card-body table-responsive">
                            <div class="download_label"><?php echo $this->lang->line('blood_bank_report'); ?></div>
                            <table class="table table-striped table-bordered table-hover allajaxlist" id="6">
                            <caption><h4><?= $this->lang->line("blood_bank_issue_details"); ?></h4></caption> 
                                <thead>
                  <tr>  
                    <th><?php echo $this->lang->line('bill_no'); ?></th>
                    <th><?php echo $this->lang->line('case_id'); ?></th>
                    <th><?php echo $this->lang->line('issue_date'); ?></th>
                    <th><?php echo $this->lang->line('donor_name'); ?></th>
                    <th><?php echo $this->lang->line('bags'); ?></th>					
                    <th class="text-end"><?php echo $this->lang->line('amount') . " (" . $currency_symbol . ")"; ?></th>
                    <th class="text-end"><?php echo $this->lang->line('discount') . " (" . $currency_symbol . ")"; ?></th>
                    <th class="text-end"><?php echo $this->lang->line('tax') . " (" . $currency_symbol . ")"; ?></th>
                    <th class="text-end"><?php echo $this->lang->line('net_amount') . " (" . $currency_symbol . ")"; ?></th>
                    <th class="text-end"><?php echo $this->lang->line('paid_amount') . " (" . $currency_symbol . ")"; ?></th>
                    <th class="text-end"><?php echo $this->lang->line('balance_amount') . " (" . $currency_symbol . ")"; ?></th>
                </tr>
            </thead>
            <tbody>
			<?php 
            if(!empty($blood_bank_data['blood_issue'])){  
                $total_net= 0 ;$total_paid=0;$total_balance=0;$total_discount=0;$total_discount_percent=0;$total_standard_charge=0;$total_tax=0;
   
                foreach ($blood_bank_data['blood_issue'] as $key => $value) {
					$tax = 0;
					$discount_amt = 0;
                    $balance_amount = ($value['net_amount']) - ($value['paid_amount']);
                    $total_net+= $value['net_amount'];
                    $total_paid+= $value['paid_amount'];
                    $total_balance+= $balance_amount ;
                    $total_discount+= calculatePercent($value['net_amount'],$value['discount_percentage']);
                    $total_discount_percent+= $value['discount_percentage'];
                    $total_standard_charge+= $value['standard_charge'];
                    $total_tax+= $value['standard_charge'];

                    $prefix = $this->customlib->getSessionPrefixByType('blood_bank_billing') . $value['id'];
                       ?>
                   <tr>
                        <td><?php echo $prefix; ?></td>
                        <td><?php echo $value['case_reference_id']; ?></td>
                        <td><?php echo $this->customlib->YYYYMMDDHisTodateFormat($value['date_of_issue'], $this->customlib->getHospitalTimeFormat()); ?></td>
                        <td><?php echo $value['donor_name']; ?></td>
                        <td><?php echo $this->customlib->bag_string($value['bag_no'],$value['volume'],$value['charge_unit']); ?></td>
						<td class="text-end"><?php echo amountFormat($value['standard_charge']); ?></td>					
                        <td class="text-end"><?php 						
						$discount_amt = calculatePercent($value['standard_charge'],$value['discount_percentage']);						
						echo amountFormat($discount_amt).' ('.$value['discount_percentage'].'%)'; ?></td>						
						<td class="text-end"><?php 					
						$tax =  (($value['standard_charge']-$discount_amt)*$value['tax_percentage'])/100;						
						$total_tax+= $tax;					
						echo amountFormat($tax).' ('.$value['tax_percentage'].'%)'; ?></td>						
                        <td class="text-end"><?php echo amountFormat($value['net_amount']); ?></td>
                        <td class="text-end"><?php echo amountFormat($value['paid_amount']); ?></td>
                        <td class="text-end"><?php echo amountFormat($value['net_amount'] - $value['paid_amount']); ?><div class='rowoptionview'><a href='javascript:void(0)'  data-loading-text=' ' data-record-id='<?php echo $value['id']; ?>' class='btn btn-secondary btn-sm view_detail' data-module-type="blood_issue"  data-bs-toggle="tooltip" title="<?php echo $this->lang->line('view'); ?>"  ><i class='fa fa-reorder'></i></a></div></td>
                    </tr>
                   <?php
                } ?>
                <tr>
                    <td colspan="4"></td>
                    <td><b><?= $this->lang->line("total_amount"); ?>: </b></td>
                    <td class="text-end"><b><?php echo $currency_symbol.number_format($total_standard_charge,2); ?></b></td>
                    <td class="text-end"><b><?php echo $currency_symbol.number_format($total_discount,2); ?></b></td>
                    <td class="text-end"><b><?php echo $currency_symbol.number_format($total_tax,2); ?></b></td>					
                    <td class="text-end"><b><?php echo $currency_symbol.number_format($total_net,2); ?></b></td>
                    <td class="text-end"><b><?php echo $currency_symbol.number_format($total_paid,2); ?></b></td>
                    <td class="text-end"><b><?php echo $currency_symbol.number_format($total_balance,2); ?></b></td>
                </tr>
            <?php }
                ?>
            </tbody>
                            </table>
                        </div>
						
                         <div class="card-body table-responsive">
                            <div class="download_label"><?php echo $this->lang->line('blood_bank_report'); ?></div>
                            <table class="table table-striped table-bordered table-hover allajaxlist" id="6">
                            <caption><h4><?= $this->lang->line("blood_bank_component_details"); ?></h4></caption>
                                <thead>
									<tr>
										<th><?php echo $this->lang->line('bill_no'); ?></th>
										<th><?php echo $this->lang->line('case_id'); ?></th>
										<th><?php echo $this->lang->line('issue_date'); ?></th>
										<th><?php echo $this->lang->line('donor_name'); ?></th>
										<th><?php echo $this->lang->line('component'); ?></th>
										<th><?php echo $this->lang->line('bags'); ?></th>                                  
										<th class="text-end"><?php echo $this->lang->line('amount') . " (" . $currency_symbol . ")"; ?></th>
										<th class="text-end"><?php echo $this->lang->line('discount') . " (" . $currency_symbol . ")"; ?></th>
										<th class="text-end"><?php echo $this->lang->line('tax') . " (" . $currency_symbol . ")"; ?></th>
										<th class="text-end"><?php echo $this->lang->line('net_amount') . " (" . $currency_symbol . ")"; ?></th>
										<th class="text-end"><?php echo $this->lang->line('paid_amount') . " (" . $currency_symbol . ")"; ?></th>
										<th class="text-end"><?php echo $this->lang->line('balance_amount') . " (" . $currency_symbol . ")"; ?></th>
									</tr>
									</thead>
									<tbody>
										<?php 
										if(!empty($blood_bank_data['component_issue'])){ 
											$total_net= 0 ;$total_paid=0;$total_balance=0;$total_discount=0;$total_discount_percent=0;$total_standard_charge=0;$total_tax=0;
						
										foreach ($blood_bank_data['component_issue'] as $key => $value) {
											$tax = 0;
											$discount_amt = 0;
											$balance_amount = ($value['net_amount']) - ($value['paid_amount']);
											$total_net+= $value['net_amount'];
											$total_paid+= $value['paid_amount'];
											$total_balance+= $balance_amount ;
											$total_discount+= calculatePercent($value['net_amount'],$value['discount_percentage']);
											$total_discount_percent+= $value['discount_percentage'];
											$total_standard_charge+= $value['standard_charge'];
											$total_tax+= $value['standard_charge'];											
											$prefix = $this->customlib->getSessionPrefixByType('blood_bank_billing') . $value['id'];
											?>
										<tr>
												<td><?php echo $prefix; ?></td>
												<td><?php echo $value['case_reference_id']; ?></td>
												<td><?php echo $this->customlib->YYYYMMDDHisTodateFormat($value['date_of_issue'], $this->customlib->getHospitalTimeFormat());?></td>
												<td><?php echo $value['donor_name']; ?></td>
												<td><?php echo $value['component_name']; ?></td>
												<td><?php echo $this->customlib->bag_string($value['bag_no'],$value['volume'],$value['charge_unit']); ?></td>
												<td class="text-end"><?php echo amountFormat($value['standard_charge']); ?></td>					
												<td class="text-end"><?php 						
												$discount_amt = calculatePercent($value['standard_charge'],$value['discount_percentage']);						
												echo amountFormat($discount_amt).' ('.$value['discount_percentage'].'%)'; ?></td>						
												<td class="text-end"><?php 					
												$tax =  (($value['standard_charge']-$discount_amt)*$value['tax_percentage'])/100;						
												$total_tax+= $tax;					
												echo amountFormat($tax).' ('.$value['tax_percentage'].'%)'; ?></td>                      
												<td class="text-end"><?php echo amountFormat($value['net_amount']); ?></td>
												<td class="text-end"><?php echo amountFormat($value['paid_amount']); ?></td>
												<td class="text-end"><?php echo amountFormat($value['net_amount'] - $value['paid_amount']); ?><div class='rowoptionview'><a href='javascript:void(0)'  data-loading-text=' ' data-record-id='<?php echo $value['id']; ?>' class='btn btn-secondary btn-sm view_detail' data-module-type="component_issue"  data-bs-toggle="tooltip" title="<?php echo $this->lang->line('view'); ?>"  ><i class='fa fa-reorder'></i></a></div></td>
											</tr>
										<?php
										} ?>
										<tr>
											<td colspan="5"></td>
											<td><b><?= $this->lang->line("total_amount"); ?>: </b></td>
											<td class="text-end"><b><?php echo $currency_symbol.number_format($total_standard_charge,2); ?></b></td>
											<td class="text-end"><b><?php echo $currency_symbol.number_format($total_discount,2); ?></b></td>
											<td class="text-end"><b><?php echo $currency_symbol.number_format($total_tax,2); ?></b></td>					
											<td class="text-end"><b><?php echo $currency_symbol.number_format($total_net,2); ?></b></td>
											<td class="text-end"><b><?php echo $currency_symbol.number_format($total_paid,2); ?></b></td>
											<td class="text-end"><b><?php echo $currency_symbol.number_format($total_balance,2); ?></b></td>
										</tr>
									<?php }
										?>                
									</tbody>
                            </table>
                        </div>
                        <div class="card-body table-responsive">
                            <div class="download_label"><?php echo $this->lang->line('ambulance_report'); ?></div>
                            <table class="table table-striped table-bordered table-hover allajaxlist" id="7">
                            <caption><h4><?= $this->lang->line("ambulance_details"); ?></h4></caption>
                            <thead>
								<tr>
									<th><?php echo $this->lang->line('bill_no'); ?></th>
									<th><?php echo $this->lang->line('case_id'); ?></th>
									<th><?php echo $this->lang->line('date'); ?></th>
									<th><?php echo $this->lang->line('vehicle_number'); ?></th>
									<th class="text-end"><?php echo $this->lang->line('amount') . " (" . $currency_symbol . ")";  ?></th>
									<th class="text-end"><?php echo $this->lang->line('discount') . " (" . $currency_symbol . ")";  ?></th>
									<th class="text-end"><?php echo $this->lang->line('tax') . " (" . $currency_symbol . ")";  ?></th>
									<th class="text-end"><?php echo $this->lang->line('net_amount') . " (" . $currency_symbol . ")";  ?></th>
									<th class="text-end"><?php echo $this->lang->line('paid_amount') . " (" . $currency_symbol . ")";  ?></th>
									<th class="text-end"><?php echo $this->lang->line('balance_amount')."(".$currency_symbol.")"; ?></th>
								</tr>
							</thead>
							<tbody>

								<?php
								if (!empty($ambulance_data)) {  
				
									$total_net= 0 ;$total_paid=0;$total_balance=0;$total_amount=0;$discount_amount=0;$total_tax=0;
									foreach ($ambulance_data as $value) {
				
										$discount_amount += $value['discount'];
										$total_amount += $value['standard_charge'];
										$balance_amount = ($value['net_amount']) - ($value['paid_amount']);
										$total_net+= $value['net_amount'] ;
										$total_paid+= $value['paid_amount'];
										$total_balance+= $balance_amount ;
				
										if ($value['case_reference_id'] > 0) {
											$case_id = $value['case_reference_id'];
										} else {
											$case_id = '';
										}
								?>
										<tr>
											<td><?php echo $this->customlib->getSessionPrefixByType('ambulance_call_billing') . $value['id']; ?></td>
											<td><?php echo $case_id ; ?></td>
											<td><?php echo $this->customlib->YYYYMMDDHisTodateFormat($value['date'], $this->customlib->getHospitalTimeFormat()); ?></td>
											<td><?php echo $value['vehicle_model']; ?></td>	
											<td class="text-end"><?php echo amountFormat($value['standard_charge']); ?></td>
											<td class="text-end"><?php echo amountFormat($value['discount']).' ('.$value['discount_percentage'].'%)'; ?></td>
											<td class="text-end"><?php $tax = (($value['standard_charge']-$value['discount'])*$value['tax_percentage'])/100;				
											$total_tax += $tax;
											echo amountFormat($tax).' ('.$value['tax_percentage'].'%)'; ?></td>						
											<td class="text-end"><?php echo amountFormat($value['net_amount']); ?></td>
											<td class="text-end"><?php echo amountFormat($value['paid_amount']); ?></td>
											<td class="text-end"><?php echo number_format($balance_amount, 2); ?><div class='rowoptionview'><a href='javascript:void(0)'  data-loading-text=' ' data-record-id='<?php echo $value['id']; ?>' class='btn btn-secondary btn-sm'  onclick="viewDetailBill('<?php echo $value['id']; ?>')" data-module-type="ambulance"  data-bs-toggle="tooltip" title="<?php echo $this->lang->line('view'); ?>"  ><i class='fa fa-reorder'></i></a></div></td>
										</tr>                        
								<?php
									} ?>
									<tr>
										<td colspan="3"></td>
										<td><b><?= $this->lang->line("total_amount"); ?>: </b></td>
										<td class="text-end"><b><?php echo $currency_symbol.number_format($total_amount,2); ?></b></td>
										<td class="text-end"><b><?php echo $currency_symbol.number_format($discount_amount,2); ?></b></td>
										<td class="text-end"><b><?php echo $currency_symbol.number_format($total_tax,2); ?></b></td>						
										<td class="text-end"><b><?php echo $currency_symbol.number_format($total_net,2); ?></b></td>
										<td class="text-end"><b><?php echo $currency_symbol.number_format($total_paid,2); ?></b></td>
										<td class="text-end"><b><?php echo $currency_symbol.number_format($total_balance,2); ?></b></td>
									</tr>
								<?php
								}
								?>
							</tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

<script>    
    document.getElementById("headreport").style.display = "block";
    document.getElementById("print").style.display = "block";
    document.getElementById("btnExport").style.display = "block";
    document.getElementById("printhead").style.display = "none";
    document.getElementById("excel_print_div").style.display = "block";

    function printDiv() {
        var patient_id = document.querySelector('input[name="patient_id"]').value;
        if (!patient_id) { return; }
        $.ajax({
            url: '<?php echo base_url(); ?>admin/patient/patientvisitreport_print',
            type: 'POST',
            data: { patient_id: patient_id },
            success: function(result) {
                popup(result);
            }
        });
    }
</script>
<script>
    var array1 = new Array();
    var array2 = new Array();
    var array3 = new Array();
    var array4 = new Array();
    var array5 = new Array();
    var array6 = new Array();
    var array7 = new Array();
    var n = 7; //Total table
    for (var x = 1; x <= n; x++) {
        array1[x - 1] = x;
        array2[x - 1] = x + 'th';
    }

    var tablesToExcel = (function() {
        var uri = 'data:application/vnd.ms-excel;base64,',
            template = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets>',
            templateend = '</x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--></head>',
            body = '<body>',
            tablevar = '<table>{table',
            tablevarend = '}</table>',
            bodyend = '</body></html>',
            worksheet = '<x:ExcelWorksheet><x:Name>',
            worksheetend = '</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet>',
            worksheetvar = '{worksheet',
            worksheetvarend = '}',
            base64 = function(s) {
                return window.btoa(unescape(encodeURIComponent(s)))
            },
            format = function(s, c) {
                return s.replace(/{(\w+)}/g, function(m, p) {
                    return c[p];
                })
            },
            wstemplate = '',
            tabletemplate = '';

        return function(table, name, filename) {
            var tables = table;

            for (var i = 0; i < tables.length; ++i) {
                wstemplate += worksheet + worksheetvar + i + worksheetvarend + worksheetend;
                tabletemplate += tablevar + i + tablevarend;
            }

            var allTemplate = template + wstemplate + templateend;
            var allWorksheet = body + tabletemplate + bodyend;
            var allOfIt = allTemplate + allWorksheet;
            var ctx = {};
            for (var j = 0; j < tables.length; ++j) {
                ctx['worksheet' + j] = name[j];
            }

            for (var k = 0; k < tables.length; ++k) {
                var exceltable;
                if (!tables[k].nodeType) exceltable = document.getElementById(tables[k]);
                ctx['table' + k] = exceltable.innerHTML;
            }

            window.location.href = uri + base64(format(allOfIt, ctx));
        }
    })();
</script>
<script>
		$(document).on('click','.view_detail',function(){
			var id=$(this).data('recordId');
			var module_name = $(this).data('moduleType');          
			PatientPathologyDetails(id,$(this), module_name);
		});

        function PatientPathologyDetails(id,btn_obj,module_name){
         var modal_view=$('#viewDetailReportModal');
         var $this = btn_obj;   
        $.ajax({
            url: base_url+'admin/patient/getPatientPathologyDetails',
            type: "POST",
            data: {'id': id,'module_name':module_name},
            dataType: 'json',
            beforeSend: function() {
				$this.btnLoading();
                modal_view.addClass('modal_loading');                
            },
            success: function (data) {        
                        
				$('#viewDetailReportModal .modal-body').html(data.page);  
				$('#viewDetailReportModal #action_detail_report_modal').html(data.actions);            

				shModal('viewDetailReportModal').show();
				modal_view.removeClass('modal_loading');
            },

            error: function(xhr) { // if error occured
				alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");
				$this.btnReset();
                modal_view.removeClass('modal_loading');
			},
			complete: function() {
				$this.btnReset();
                modal_view.removeClass('modal_loading');          
			}
        });  
        }
</script>
<script>
    function viewDetailBill(id) {
        var modal = $('#viewModalBill');
        $.ajax({
            url: '<?php echo base_url() ?>admin/patient/getAmbulanceBillDetail/' + id,
            type: "GET",
            dataType: 'json',
            beforeSend: function() {
                shModal('viewModalBill').show();
                modal.addClass('modal_loading');
            },
            success: function (data) {
                $('#reportdata').html(data.page);
                modal.removeClass('modal_loading');
            },
            error: function() {
                errorMsg('<?php echo $this->lang->line('something_went_wrong'); ?>');
                modal.removeClass('modal_loading');
            },
            complete: function() {
                modal.removeClass('modal_loading');
            }
        });
    }
</script>
<script>
    function viewDetail(id) {      
        var view_modal=$('#viewModal');
        $.ajax({
            url: '<?php echo base_url() ?>admin/patient/getpharmacybilldetails/',
            type: "GET",
            data: {'id': id},
            dataType:"JSON",
            beforeSend: function(){
           shModal('viewModal').show();
           view_modal.addClass('modal_loading');
           },
           complete: function(){
             view_modal.removeClass('modal_loading');
           },
            success: function (data) {
                $('#pharmacy_reportdata').html(data.page);               
                view_modal.removeClass('modal_loading');
            },
        });
    }
</script>