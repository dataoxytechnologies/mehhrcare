<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
$genderList = $this->customlib->getGender();
$case_reference_id=$result['case_reference_id'];
?>
<input type="hidden" id="result_opdid" value="<?php echo $result['id'] ?>">
<input type="hidden" id="result_pid" value="<?php echo $result['patient_id'] ?>">
<div class="opd-profile-wrap">
    <div class="page-head">
        <div class="ph-crumbs">
            <span><?php echo $this->lang->line('billing'); ?></span>
            <span class="sep">/</span>
            <span><?php echo composePatientName($result['patient_name'], $result['patient_id']); ?></span>
        </div>
        <div class="ph-title-row">
            <div class="ph-title">
                <?php
                $ovd_has_img  = !empty($result['image']) && strpos($result['image'], 'no_image') === false;
                $_ovd_parts   = preg_split('/\s+/', trim($result['patient_name'] ?? ''), -1, PREG_SPLIT_NO_EMPTY);
                $ph_initial   = count($_ovd_parts) === 0 ? '?' : (count($_ovd_parts) === 1
                    ? mb_strtoupper(mb_substr($_ovd_parts[0], 0, 1))
                    : mb_strtoupper(mb_substr($_ovd_parts[0], 0, 1) . mb_substr($_ovd_parts[count($_ovd_parts) - 1], 0, 1)));
                ?>
                <?php if ($ovd_has_img): ?>
                    <img class="ph-av" style="object-fit:cover;" src="<?php echo $this->media_storage->getImageURL($result['image']); ?>" alt="<?php echo html_escape($result['patient_name']); ?>">
                <?php else: ?>
                    <div class="ph-av"><?php echo html_escape($ph_initial); ?></div>
                <?php endif; ?>
                <div>
                    <h1>
                        <?php echo composePatientName($result['patient_name'], $result['patient_id']); ?>
                        <span class="badge ms-1 opd-type-badge">OPD</span>
                    </h1>
                    <div class="sub">
                        <span class="mono">P-<?php echo $result['patient_id']; ?></span>
                        <span class="dot-sep"><?php echo $this->customlib->get_patient_current_age($result['patient_id']); ?></span>
                        <?php if (!empty($result['mobileno'])): ?><span class="dot-sep"><?php echo html_escape($result['mobileno']); ?></span><?php endif; ?>
                        <span class="dot-sep"><?php echo $opd_prefix.$result['id']; ?></span>
                    </div>
                </div>
            </div>
            <div class="ph-actions">
                <?php if ($this->rbac->hasPrivilege('opd_patient_discharge', 'can_view')) { ?>
                <a class="btn btn-sm ph-act ph-act-discharge patient_discharge" href="#" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('patient_discharge'); ?>"><i class="fa fa-hospital-o"></i></a>
                <?php } if (!$is_discharge) { if ($this->rbac->hasPrivilege('opd_patient_discharge_revert', 'can_view')) { ?>
                <a class="btn btn-sm ph-act ph-act-revert" onclick="discharge_revert('<?php echo $result['case_reference_id']; ?>')" href="#" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('discharge_revert'); ?>"><i class="fa fa-undo"></i></a>
                <?php } } ?>
            </div>
        </div>
        <nav class="ph-tabs" role="tablist">
            <?php if ($this->rbac->hasPrivilege('checkup', 'can_view')) { ?>
            <a class="active" href="#activity" data-bs-toggle="tab" role="tab"><i class="far fa-caret-square-down"></i> <?php echo $this->lang->line('visits'); ?></a>
            <?php } ?>
            <?php if ($this->rbac->hasPrivilege('opd_charges', 'can_view')) { ?>
            <a href="#charges" data-bs-toggle="tab" role="tab"><i class="fas fa-donate"></i> <?php echo $this->lang->line('charges'); ?></a>
            <?php } ?>
            <?php if ($this->rbac->hasPrivilege('opd_payment', 'can_view')) { ?>
            <a href="#payment" data-bs-toggle="tab" role="tab"><i class="fas fa-hand-holding-usd"></i> <?php echo $this->lang->line('payment'); ?></a>
            <?php } ?>
        </nav>
    </div><!-- /.page-head -->

    <div class="tab-content">
                        <?php if ($this->rbac->hasPrivilege('checkup', 'can_view')) { ?>
                            <div class="tab-pane fade card active show" id="activity">
                                <div class="box-tab-header">
                                    <h3 class="box-tab-title"><?php echo $this->lang->line('checkups'); ?></h3>
                                        <div class="box-tab-tools">
                                            <?php if ($this->rbac->hasPrivilege('checkup', 'can_add')) { if($is_discharge){ ?> 
                                        <a href="#" onclick="getRevisitRecord('<?php echo $visitdata['visitid'] ?>')" class="btn btn-primary btn-sm revisitrecheckup"><i class="fas fa-exchange-alt"></i> <?php echo $this->lang->line('recheckup'); ?></a>
                                       <?php }} ?> 
                                         </div>    
                                   </div>
                               <div class="download_label"><?php echo composePatientName($result['patient_name'],$result['patient_id']) . " " . $this->lang->line('opd_details'); ?></div>
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered table-hover ajaxlist" id="opdVisitDetailTable" cellspacing="0" width="" data-export-title="<?php echo composePatientName($result['patient_name'],$result['patient_id']) . " " . $this->lang->line('opd_details'); ?>">
                                        <thead>
                                        <th><?php echo $this->lang->line('checkup_id'); ?></th>
                                        <th><?php echo $this->lang->line('appointment_date'); ?></th>
                                        <th><?php echo $this->lang->line('consultant_doctor'); ?></th>
                                        <th><?php echo $this->lang->line('reference'); ?></th>
                                        <th><?php echo $this->lang->line('symptoms'); ?></th>
                                        <?php 
                                            if (!empty($fields)) {
                                                foreach ($fields as $fields_key => $fields_value) {
                                                    ?>
                                                    <th><?php echo $fields_value->name; ?></th>
                                                    <?php
                                                } 
                                            } 
                                        ?> 
                                        <th class="text-end noExport"><?php echo $this->lang->line('action') ?></th>
                                        </thead>
                                        <tbody>                                        
                                        </tbody>
                                    </table>
                                </div> 
                            </div>
                        <?php } ?>                 

                        <!-- Charges -->
                            <?php if ($this->rbac->hasPrivilege('opd_charges', 'can_view')) { ?>
                            <div class="tab-pane fade card" id="charges">
                                <div class="box-tab-header">
                                  <h3 class="box-tab-title"><?php echo $this->lang->line('charges'); ?></h3>
                                <div class="box-tab-tools">
                            <?php if ($this->rbac->hasPrivilege('opd_charges', 'can_add')) { 
                                if($is_discharge){ ?>
                                        <a onclick="holdModal('add_chargeModal')" class="btn btn-primary btn-sm addcharges"><i class="fa fa-plus"></i> <?php echo $this->lang->line('add_charges') ?></a>
                            <?php } } ?>
                                </div>
                            </div>     
                               <div class="download_label"><?php echo composePatientName($result['patient_name'],$result['patient_id']) . " " . $this->lang->line('opd_details'); ?></div>
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered table-hover example">
                                         
										
										<thead>
                                            <th><?php echo $this->lang->line('date'); ?></th>
                                            <th><?php echo $this->lang->line('charge_name'); ?> / <?php echo $this->lang->line('charge_note'); ?></th>
                                            <th><?php echo $this->lang->line('charge_type'); ?></th>
                                            <th><?php echo $this->lang->line('charge_category'); ?></th>
                                            <th><?php echo $this->lang->line('qty'); ?></th>
                                            <th class="text-end"><?php echo $this->lang->line('standard_charge') . ' (' . $currency_symbol . ')'; ?> </th>
                                            <th class="text-end"><?php echo $this->lang->line('applied_charge') . ' (' . $currency_symbol . ')'; ?></th>
                                            <th class="text-end"><?php echo $this->lang->line('tpa_charge') . ' (' . $currency_symbol . ')';?></th>
                                            <th class="text-end"><?php echo $this->lang->line('discount'); ?></th>
                                            <th class="text-end"><?php echo $this->lang->line('tax'); ?></th>
                                            <th class="text-end"><?php echo $this->lang->line('amount') . ' (' . $currency_symbol . ')'; ?></th>
                                            <th class="text-end noExport"><?php echo $this->lang->line('action') ?></th>
                                        </thead>
										
                                        <tbody>
                                         <?php 
                                            $total = 0; 
                                            if (!empty($charges_detail)) {
                                                foreach ($charges_detail as $charges_key => $charges_value) {
                                                    $discount_amount = amountFormat(($charges_value['apply_charge']*$charges_value['discount_percentage']/100)) ;
                                                    $tax_amount = (($charges_value['apply_charge']-$discount_amount)*$charges_value['tax']/100) ;
                                                    $taxamount = amountFormat($tax_amount);
                                                    $total += $charges_value["amount"];
                                                ?>  
                                                    <tr>
                                                        <td><?php echo $this->customlib->YYYYMMDDHisTodateFormat($charges_value['date'],$this->customlib->getHospitalTimeFormat()); ?></td>
                                                        <td>
                                                            <?php echo $charges_value["name"]; ?>
                                                             <div class="bill_item_footer text-muted"><label><?php if($charges_value["note"] !=''){ echo $this->lang->line('charge_note').': ';} ?></label> <?php echo $charges_value["note"]; ?></div>
                                                        </td>
                                                        <td class="text-capitalize"><?php echo $charges_value["charge_type"] ?></td>
                                                        <td class="text-capitalize"><?php echo $charges_value["charge_category_name"] ?></td>
                                                        <td class="text-capitalize"><?php echo $charges_value['qty'] ?></td>
                                                        <td class="text-end"><?php echo $charges_value["standard_charge"] ?></td>
                                                        <td class="text-end"><?php echo $charges_value["apply_charge"] ?></td>
                                                        <td class="text-end"><?php echo $charges_value["tpa_charge"] ?></td>
                                                        <td class="text-end"><?php echo number_format(($discount_amount),2)." (".$charges_value["discount_percentage"]."%) " ;?></td>
                                                        <td class="text-end"><?php echo $taxamount." (".$charges_value["tax"]."%) " ;?></td>
                                                        <td class="text-end"><?php echo $charges_value["amount"] ?></td>
                                                        <td class="text-end"> 
															<a href="javascript:void(0);" class="btn btn-secondary btn-sm print_charge" data-bs-toggle="tooltip" data-loading-text="<?php echo $this->lang->line('please_wait') ;?>" data-record-id="<?php echo $charges_value['id']; ?>"  title="<?php echo $this->lang->line('print');?>"><i class="fa fa-print"></i></a> 
															<?php 
															if($is_discharge){
																if ($this->rbac->hasPrivilege('opd_charges', 'can_edit')) { 
															?>
															<a href='javascript:void(0);' class='btn btn-secondary btn-sm edit_charge' data-loading-text='<?php echo $this->lang->line('please_wait') ;?>' data-bs-toggle='tooltip' data-record-id='<?php echo $charges_value['id']; ?>'  title="<?php echo  $this->lang->line('edit')?>"><i class='fa fa-pencil'></i></a>
                                                            <?php } } if ($this->rbac->hasPrivilege('opd_charges', 'can_delete')) {
                                                            if($is_discharge){ ?>                                                              
															<a href="javascript:void(0);" onclick="deleteOpdPatientCharge('<?php echo $charges_value['id']; ?>')" class="btn btn-secondary btn-sm" data-bs-toggle="tooltip"  title="<?php echo $this->lang->line('delete'); ?>"><i class="fa fa-trash"></i></a>
															<?php } }?>   
                                                        </td>                                                       
                                                    </tr>
                                                    <?php
                                                }
                                            }
                                            ?> 
                                        </tbody>
                                        <tr class="total-bg fw-bold">
                                            <td colspan='11' class="text-end"><?php echo $this->lang->line('total') . " : " . $currency_symbol . "" . amountFormat($total); ?> 
                                            <input type="hidden" id="charge_total" name="charge_total" value="<?php echo $total ?>">
                                            </td>
                                             <td></td>                                            
                                        </tr>
                                    </table>
                                </div> 
                            </div>    
                            <!-- -->   
                            <!--payment -->
                            <?php } if ($this->rbac->hasPrivilege('opd_payment', 'can_view')) {
                                ?>
                            <div class="tab-pane fade card" id="payment">
                                <div class="box-tab-header">
                                  <h3 class="box-tab-title"><?php echo $this->lang->line('payment'); ?></h3>

                                <?php
                                if ($this->rbac->hasPrivilege('opd_payment', 'can_add')) {
                                      if($is_discharge){ ?>
                                    <div class="box-tab-tools">                                     
                                        <a href="#" class="btn btn-sm btn-primary addpayment"><i class="fa fa-plus"></i> <?php echo $this->lang->line('add_payment'); ?></a>
                                    </div><!--./impbtnview-->
                                    <?php
                                    }
                                }
                                ?>
                            </div>    
                            <div class="download_label"><?php echo $this->lang->line('payment'); ?></div>
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered table-hover example">
                                        <thead>
                                        <th><?php echo $this->lang->line('transaction_id'); ?></th>
                                        <th><?php echo $this->lang->line('date'); ?></th>
                                        <th><?php echo $this->lang->line('note'); ?></th>
                                        <th><?php echo $this->lang->line('payment_mode'); ?></th>
                                        <th class="text-end"><?php echo $this->lang->line('paid_amount') . " (" . $currency_symbol . ")"; ?></th>
                                        <th class="text-end noExport"><?php echo $this->lang->line('action') ?></th>
                                        </thead>
                                        <tbody>
                                        <?php
                                        $total_payment = 0;
                                            if (!empty($payment_details)) {
                                                $total_payment = 0;
                                                foreach ($payment_details as $payment) {
                                                    if (!empty($payment['amount'])) {
                                                        $total_payment += $payment['amount'];
                                                    }
                                                    ?> 
                                                    <tr>
                                                        <td><?php echo $this->customlib->getSessionPrefixByType('transaction_id').$payment['id']; ?></td>
                                                        <td><?php echo $this->customlib->YYYYMMDDHisTodateFormat($payment['payment_date'],$this->customlib->getHospitalTimeFormat()); ?></td>
                                                        <td><?php echo $payment["note"] ?></td>
                                                        <td><?php echo $this->lang->line(strtolower($payment["payment_mode"]))."<br>";
                                                        if($payment['payment_mode'] == "Cheque"){
                                                             if($payment['cheque_no']!=''){
                                       echo $this->lang->line('cheque_no') . ": ".$payment['cheque_no'];                                      
                                    echo "<br>";
                                }
                                    if($payment['cheque_date']!='' && $payment['cheque_date']!='0000-00-00'){
                                       echo $this->lang->line('cheque_date') .": ".$this->customlib->YYYYMMDDTodateFormat($payment['cheque_date']);
                                   } 
                                     }
                                                        ?>                                                           

                                                        </td>
                                                        <td class="text-end"><?php echo $payment["amount"] ?></td>
                                                        <td class="text-end">
            <?php         if ($payment['payment_mode'] == "Cheque" && $payment['attachment'] != "")  {
    ?>
    <a href='<?php echo site_url('admin/transaction/download/'.$payment['id']);?>' class='btn btn-secondary btn-sm' title='<?php echo $this->lang->line('download'); ?>'><i class='fa fa-download'></i></a>
    <?php
}
         ?>
 <a href="javascript:void(0);" class="btn btn-secondary btn-sm print_trans" data-bs-toggle="tooltip" data-loading-text="<?php echo $this->lang->line('please_wait') ;?>" data-record-id="<?php echo $payment['id']; ?>"  title="<?php echo $this->lang->line('print') ;?>">
                                                                    <i class="fa fa-print"></i>
                                                                </a>  
                                                            <?php if (!empty($payment["document"])) { ?>
                                                                <a href="<?php echo base_url(); ?>admin/payment/download/<?php echo $payment["document"]; ?>"  class="btn btn-secondary btn-sm"  data-bs-toggle="tooltip" title="<?php echo $this->lang->line('download'); ?>">
                                                                    <i class="fa fa-download"></i>
                                                                </a>
                                              <?php } ?>
														<a href="javascript:void(0);"  class="btn btn-secondary btn-sm editpayment" data-bs-toggle="tooltip" data-payment-amount="<?php echo $payment["amount"] ?>" data-record-id="<?php echo $payment['id']; ?>" title="<?php echo $this->lang->line('edit'); ?>"><i class="fa fa-pencil"></i></a>													

                                                            <?php
                                                             if($is_discharge){ 
                                                            if ($this->rbac->hasPrivilege('opd_payment', 'can_delete')) { ?>
            <a href="javascript:void(0);"onclick="deletePayment('<?php echo $payment['id']; ?>')" class="btn btn-secondary btn-sm" data-bs-toggle="tooltip"  title="<?php echo $this->lang->line('delete'); ?>">
                                                                <i class="fa fa-trash"></i>
                                                            </a>   
                                                    <?php } } ?>
                                                        </td>
                                                    </tr>
                                        <?php } }?> 
                                        </tbody>
                                                <tr class="total-bg fw-bold"> 
                                                    <td></td> 
                                                    <td></td> 
                                                    <td></td> 
                                                    <td></td> 
                                                    <td class="text-end"><?php echo $this->lang->line('total') . " : " . $currency_symbol . "" . number_format((float)$total_payment, 2, '.', ''); ?>
                                                    </td> 
                                                    <td></td>                                                    
                                                </tr>                                            
                                    </table>
                                </div> 
                            </div> 
                            <!-- -->
                                <?php } ?>
    </div><!-- /.tab-content -->
</div><!-- /.opd-profile-wrap -->

<!--new edit modal-->
<div class="modal fade sh-modal sh-modal-accent" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel"><?php echo $this->lang->line('edit_visit_details'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formedit" accept-charset="utf-8" enctype="multipart/form-data" method="post">
                <input type="hidden" name="visitid" id="visitid" />
                <input type="hidden" name="visit_transaction_id" id="visit_transaction_id" />
                <input type="hidden" name="type" id="type" value="visit" />
                <div class="pup-scroll-area">
                    <div class="modal-body">
                        <!-- Row 1: Visit Details + Appointment Config -->
                        <div class="row g-3 mb-3">
                            <!-- Card 1: Visit Details -->
                            <div class="col-lg-7">
                                <div class="sh-form-card h-100">
                                    <div class="sh-card-header">
                                        <span class="sh-card-header-title"><?php echo $this->lang->line('visit_details'); ?></span>
                                    </div>
                                    <div class="p-3">
                                        <div id="ajax_load"></div>
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <div class="mb-3">
                                                    <label><?php echo $this->lang->line('symptoms_type'); ?></label>
                                                    <select name="symptoms_type" id="act" class="form-control select2 act w-100" >
                                                        <option value=""><?php echo $this->lang->line('select'); ?></option>
                                                        <?php foreach ($symptomsresulttype as $dkey => $dvalue) { ?>
                                                            <option value="<?php echo $dvalue["id"]; ?>"><?php echo $dvalue["symptoms_type"]; ?></option>
                                                        <?php } ?>
                                                    </select>
                                                    <span class="text-danger"><?php echo form_error('symptoms_type'); ?></span>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="mb-3">
                                                    <label><?php echo $this->lang->line('symptoms'); ?></label>
                                                    <div id="dd" class="wrapper-dropdown-3">
                                                        <input class="form-control filterinput" type="text" autocomplete="off">
                                                        <ul class="dropdown scroll150 section_ul">
                                                            <li class="section-placeholder"><span><?php echo $this->lang->line('select'); ?></span></li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-12">
                                                <div class="mb-3">
                                                    <label><?php echo $this->lang->line('symptoms_description'); ?></label>
                                                    <textarea class="form-control" id="symptoms_description" name="symptoms"></textarea>
                                                </div>
                                            </div>
                                            <div class="col-sm-12">
                                                <div class="mb-3">
                                                    <label><?php echo $this->lang->line('note'); ?></label>
                                                    <textarea rows="3" class="form-control" id="edit_revisit_note" name="revisit_note"></textarea>
                                                </div>
                                            </div>
                                            <div class="col-sm-12">
                                                <div class="mb-3">
                                                    <label><?php echo $this->lang->line('any_known_allergies'); ?></label>
                                                    <textarea name="known_allergies" rows="3" id="eknown_allergies" class="form-control"></textarea>
                                                </div>
                                            </div>
                                            <div class="col-sm-12">
                                                <div id="customfield"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Card 2: Appointment Config -->
                            <div class="col-lg-5">
                                <div class="sh-form-card mb-3">
                                    <div class="sh-card-header">
                                        <span class="sh-card-header-title"><?php echo $this->lang->line('appointment'); ?></span>
                                    </div>
                                    <div class="p-3">
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <div class="mb-3">
                                                    <label><?php echo $this->lang->line('appointment_date'); ?></label><small class="req"> *</small>
                                                    <input name="appointment_date" class="form-control datetime" id="appointmentdate" placeholder="" type="text" />
                                                    <span class="text-danger"><?php echo form_error('appointment_date'); ?></span>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="mb-3">
                                                    <label><?php echo $this->lang->line('case'); ?></label>
                                                    <input class="form-control" type="text" name="case" id="edit_case" />
                                                    <span class="text-danger"><?php echo form_error('case'); ?></span>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="mb-3">
                                                    <label><?php echo $this->lang->line('casualty'); ?></label>
                                                    <select name="casualty" id="edit_casualty" class="form-control">
                                                        <?php foreach ($yesno_condition as $yesno_key => $yesno_value) { ?>
                                                            <option value="<?php echo $yesno_key ?>" <?php if ($yesno_key == 'no') { echo "selected"; } ?>><?php echo $yesno_value ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="mb-3">
                                                    <label><?php echo $this->lang->line('old_patient'); ?></label>
                                                    <select name="old_patient" id="edit_oldpatient" class="form-control">
                                                        <?php foreach ($yesno_condition as $yesno_key => $yesno_value) { ?>
                                                            <option value="<?php echo $yesno_key ?>"><?php echo $yesno_value ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="mb-3">
                                                    <label><?php echo $this->lang->line('tpa'); ?></label>
                                                    <select class="form-control" onchange="get_Charges(this.value)" id="edit_organisation" name="organisation">
                                                        <option value="0"><?php echo $this->lang->line('select'); ?></option>
                                                        <?php foreach ($organisation as $orgkey => $orgvalue) { ?>
                                                            <option value="<?php echo $orgvalue["id"]; ?>"><?php echo $orgvalue["organisation_name"] ?></option>
                                                        <?php } ?>
                                                    </select>
                                                    <span class="text-danger"><?php echo form_error('refference'); ?></span>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="mb-3">
                                                    <label><?php echo $this->lang->line('reference'); ?></label>
                                                    <input type="text" name="refference" class="form-control" id="edit_refference" />
                                                    <span class="text-danger"><?php echo form_error('refference'); ?></span>
                                                </div>
                                            </div>
                                            <div class="col-sm-12">
                                                <div class="mb-3">
                                                    <label><?php echo $this->lang->line('consultant_doctor'); ?></label><small class="req"> *</small>
                                                    <select onchange="" name="consultant_doctor" <?php if ($disable_option == true) { echo "disabled"; } ?> class="w-100 form-control select2" id="edit_consdoctor">
                                                        <option value=""><?php echo $this->lang->line('select'); ?></option>
                                                        <?php foreach ($doctors as $dkey => $dvvalue) { ?>
                                                            <option value="<?php echo $dvvalue["id"] ?>"><?php echo composeStaffNameByString($dvvalue["name"], $dvvalue["surname"], $dvvalue["employee_id"]); ?></option>
                                                        <?php } ?>
                                                    </select>
                                                    <?php if ($disable_option == true) { ?>
                                                        <input type="hidden" name="consultant_doctor" value="<?php echo $doctor_select ?>">
                                                    <?php } ?>
                                                    <span class="text-danger"><?php echo form_error('refference'); ?></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Card 3: Payment Details -->
                                <div class="sh-form-card">
                                    <div class="sh-card-header">
                                        <span class="sh-card-header-title"><?php echo $this->lang->line('payment_details'); ?></span>
                                    </div>
                                    <div class="p-3">
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <div class="mb-3">
                                                    <label><?php echo $this->lang->line('payment_date'); ?></label><small class="req"> *</small>
                                                    <input type="text" name="payment_date" id="edit_visit_payment_date" class="form-control datetime" autocomplete="off">
                                                    <input type="hidden" id="edit_visit_payment_id" name="edit_payment_id">
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="mb-3">
                                                    <label><?php echo $this->lang->line('amount') . " (" . $currency_symbol . ")"; ?></label><small class="req"> *</small>
                                                    <input type="text" name="amount" id="edit_visit_payment" class="form-control" value="">
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="mb-3">
                                                    <label><?php echo $this->lang->line('payment_mode'); ?></label>
                                                    <select class="form-control visit_payment_mode" name="payment_mode" id="visit_payment_mode">
                                                        <?php foreach ($payment_mode as $key => $value) { ?>
                                                            <option value="<?php echo $key ?>" <?php if ($key == 'cash') { echo "selected"; } ?>><?php echo $value ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="mb-3">
                                                    <label><?php echo $this->lang->line('payment_note'); ?></label>
                                                    <input type="text" name="note" id="edit_visit_payment_note" class="form-control" />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="cheque_div" style="display:none;">
                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <div class="mb-3">
                                                        <label><?php echo $this->lang->line('cheque_no'); ?></label><small class="req"> *</small>
                                                        <input type="text" name="cheque_no" id="edit_visit_cheque_no" class="form-control">
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="mb-3">
                                                        <label><?php echo $this->lang->line('cheque_date'); ?></label><small class="req"> *</small>
                                                        <input type="text" name="cheque_date" id="edit_visit_cheque_date" class="form-control date">
                                                    </div>
                                                </div>
                                                <div class="col-sm-12">
                                                    <div class="mb-3">
                                                        <label><?php echo $this->lang->line('attach_document'); ?></label>
                                                        <input type="file" class="filestyle form-control" name="document">
                                                        <span class="text-danger"><?php echo form_error('document'); ?></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <?php if ($result['gender'] == 'Female') { ?>
                                        <div class="row">
                                            <div class="col-sm-12" id="antenatal_div">
                                                <div class="mb-3">
                                                    <label>&nbsp;</label><br />
                                                    <input type="checkbox" name="is_for_antenatal" id="edit_is_for_antenatal" value="0"> <?php echo $this->lang->line('is_antenatal') ?>
                                                </div>
                                            </div>
                                        </div>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                    <button type="submit" id="formeditbtn" name="save" data-loading-text="<?php echo $this->lang->line('processing') ?>" class="btn btn-info"><i class="fa fa-check-circle"></i> <span><?php echo $this->lang->line('save'); ?></span></button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- end new added modal-->

<!-- Add Charges -->
<!-- Add Charges -->
<div class="modal fade sh-modal sh-modal-accent" id="edit_chargeModal" tabindex="-1" aria-labelledby="edit_chargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="edit_chargeModalLabel"><?php echo $this->lang->line('edit_charge'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="edit_charges" accept-charset="utf-8" method="post">
                <input type="hidden" name="opd_id" value="<?php echo $result['id'] ?>">
                <input type="hidden" name="patient_charge_id" id="editpatient_charge_id" value="0">
                <input type="hidden" name="patient_id" id="editpatient_id" value="<?php echo $result['patient_id'] ?>">
                <input type="hidden" name="organisation_id" id="editorganisation_id" value="<?php echo $result['organisation_id'] ?>">
                <input type="hidden" name="insurance_validity" id="insurance_validity" value="<?php echo $result['insurance_validity'] ?>">
                <input type="hidden" name="insurance_id" id="insurance_id" value="<?php echo $result['insurance_id'] ?>">
                <input type="hidden" class="reset_value" id="edit_total_charge" name="edit_total_charge" value="0">
                <div class="pup-scroll-area">
                    <div class="modal-body modal-background">
                        <!-- Card 1: Charge input -->
                        <div class="sh-form-card mb-3">
                            <div class="sh-card-header">
                                <span class="sh-card-header-title"><?php echo $this->lang->line('edit_charge'); ?></span>
                                <div class="ms-auto">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="1" id="edit_is_tpa" name="edit_is_tpa" onclick="reset_value()">
                                        <label class="form-check-label" for="edit_is_tpa"><?php echo $this->lang->line('apply_tpa'); ?></label>
                                    </div>
                                </div>
                            </div>
                            <div class="p-2">
                                <div class="row g-2">
                                    <div class="col-sm-2">
                                        <label class="form-label"><?php echo $this->lang->line('charge_type'); ?></label><small class="req"> *</small>
                                        <select name="charge_type" id="editcharge_type" class="form-control form-control-sm charge_type select2 reset_value w-100" >
                                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                                            <?php foreach ($charge_type as $key => $value) { ?>
                                            <option value="<?php echo $value->id; ?>"><?php echo $value->charge_type; ?></option>
                                            <?php } ?>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('charge_type'); ?></span>
                                    </div>
                                    <div class="col-sm-2">
                                        <label class="form-label"><?php echo $this->lang->line('charge_category'); ?></label><small class="req"> *</small>
                                        <select name="charge_category" id="editcharge_category" class="w-100 form-control form-control-sm select2 charge_category editcharge_category reset_value">
                                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('charge_category'); ?></span>
                                    </div>
                                    <div class="col-sm-2">
                                        <label class="form-label"><?php echo $this->lang->line('charge_name'); ?></label><small class="req"> *</small>
                                        <select name="charge_id" id="editcharge_id" class="w-100 form-control form-control-sm addcharge select2 editcharge reset_value">
                                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('code'); ?></span>
                                    </div>
                                    <div class="col-sm-2">
                                        <label class="form-label"><?php echo $this->lang->line('standard_charge') . " (" . $currency_symbol . ")" ?></label>
                                        <input type="text" readonly name="standard_charge" id="editstandard_charge" class="form-control form-control-sm reset_value" value="<?php echo set_value('standard_charge'); ?>">
                                        <span class="text-danger"><?php echo form_error('standard_charge'); ?></span>
                                    </div>
                                    <div class="col-sm-2">
                                        <label class="form-label"><?php echo $this->lang->line('tpa_charge') . " (" . $currency_symbol . ")" ?></label>
                                        <input type="text" readonly name="schedule_charge" id="editscd_charge" class="form-control form-control-sm reset_value" value="<?php echo set_value('schedule_charge'); ?>">
                                        <span class="text-danger"><?php echo form_error('schedule_charge'); ?></span>
                                    </div>
                                    <div class="col-sm-2">
                                        <label class="form-label"><?php echo $this->lang->line('qty'); ?></label><small class="req"> *</small>
                                        <input type="text" name="qty" id="editqty" class="form-control form-control-sm reset_qty" value="1">
                                        <span class="text-danger"><?php echo form_error('qty'); ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Cards 2+3 side by side -->
                        <div class="d-flex flex-wrap gap-3">
                            <!-- Card 3: Billing summary -->
                            <div class="sh-flex-col">
                                <div class="sh-form-card h-100 overflow-hidden">
                                    <div class="sh-card-header">
                                        <span class="sh-card-header-title"><?php echo $this->lang->line('net_amount'); ?></span>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between px-3 py-2 border-bottom">
                                        <span class="text-secondary small fw-semibold"><?php echo $this->lang->line('total') . " (" . $currency_symbol . ")"; ?></span>
                                        <input type="text" placeholder="<?= $this->lang->line('total') ?>" value="0" name="apply_charge" id="editapply_charge" class="form-control form-control-sm text-end total sh-bill-summary-input" readonly>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between px-3 py-2 border-bottom">
                                        <span class="text-secondary small fw-semibold"><?php echo $this->lang->line('discount') . " (" . $currency_symbol . ")"; ?></span>
                                        <div class="d-flex gap-2">
                                            <div class="input-group input-group-sm sh-discount-pct-group">
                                                <input type="text" name="editdiscount_percentage" id="editdiscount_percentage" class="form-control text-end editdiscount_percentage reset_value">
                                                <span class="input-group-text">%</span>
                                            </div>
                                            <input type="text" value="0" onkeyup="update_percentage(this.value)" placeholder="<?php echo $this->lang->line('discount'); ?>" name="discount_percentage" id="editdiscount" class="form-control form-control-sm text-end discount sh-bill-summary-input">
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between px-3 py-2 border-bottom">
                                        <span class="text-secondary small fw-semibold"><?php echo $this->lang->line('tax') . " (" . $currency_symbol . ")"; ?></span>
                                        <div class="d-flex gap-2">
                                            <div class="input-group input-group-sm sh-discount-pct-group">
                                                <input type="text" name="charge_tax" id="editcharge_tax" class="form-control text-end charge_tax" readonly>
                                                <span class="input-group-text">%</span>
                                            </div>
                                            <input type="text" value="0" name="tax" id="edittax" class="form-control form-control-sm text-end tax sh-bill-summary-input" readonly>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between px-3 py-2">
                                        <span class="text-secondary small fw-semibold"><?php echo $this->lang->line('net_amount') . " (" . $currency_symbol . ")"; ?></span>
                                        <input type="text" placeholder="<?= $this->lang->line('net_amount') ?>" value="0" name="amount" id="editfinal_amount" class="form-control form-control-sm text-end fw-semibold net_amount sh-bill-summary-input" readonly>
                                    </div>
                                </div>
                            </div>
                            <!-- Card 2: Date + Note -->
                            <div class="sh-flex-col">
                                <div class="sh-form-card h-100">
                                    <div class="sh-card-header">
                                        <span class="sh-card-header-title"><?php echo $this->lang->line('charge_note'); ?></span>
                                    </div>
                                    <div class="p-2">
                                        <div class="row g-2">
                                            <div class="col-12">
                                                <label class="form-label"><?php echo $this->lang->line('date'); ?></label><small class="req"> *</small>
                                                <input id="editcharge_date" name="date" type="text" class="form-control form-control-sm datetime">
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label"><?php echo $this->lang->line('charge_note'); ?></label>
                                                <textarea name="note" id="edit_note" rows="3" class="form-control form-control-sm"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div><!--./d-flex-->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                    <button type="submit" data-loading-text='<i class="fa fa-circle-o-notch fa-spin"></i>' name="charge_data" class="btn btn-info"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save') ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade sh-modal sh-modal-accent" id="add_chargeModal" tabindex="-1" aria-labelledby="add_chargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="add_chargeModalLabel"><?php echo $this->lang->line('add_charges'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="add_charges" accept-charset="utf-8" method="post">
                <input type="hidden" name="opd_id" value="<?php echo $result['id'] ?>">
                <input type="hidden" name="patient_id" id="patient_id" value="<?php echo $result['patient_id'] ?>">
                <input type="hidden" name="patient_charge_id" id="patient_charge_id" value="0">
                <input type="hidden" name="organisation_id" id="organisation_id" value="<?php echo $result['organisation_id'] ?>">
                <input type="hidden" name="insurance_validity" id="insurance_validity" value="<?php echo $result['insurance_validity'] ?>">
                <input type="hidden" name="insurance_id" id="insurance_id" value="<?php echo $result['insurance_id'] ?>">
                <input type="hidden" class="reset_value" id="total_charge" name="total_charge" value="0">
                <div class="pup-scroll-area">
                    <div class="modal-body modal-background">
                        <!-- Card 1: Charge input -->
                        <div class="sh-form-card mb-3">
                            <div class="sh-card-header">
                                <span class="sh-card-header-title"><?php echo $this->lang->line('add_charges'); ?></span>
                                <div class="ms-auto">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="1" id="is_tpa" name="is_tpa" onclick="reset_value()">
                                        <label class="form-check-label" for="is_tpa"><?php echo $this->lang->line('apply_tpa'); ?></label>
                                    </div>
                                </div>
                            </div>
                            <div class="p-2">
                                <div class="row g-2">
                                    <div class="col-sm-2">
                                        <label class="form-label"><?php echo $this->lang->line('charge_type'); ?></label><small class="req"> *</small>
                                        <select name="charge_type" id="add_charge_type" class="form-control form-control-sm charge_type select2 reset_value w-100" >
                                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                                            <?php foreach ($charge_type as $key => $value) { ?>
                                            <option value="<?php echo $value->id; ?>"><?php echo $value->charge_type; ?></option>
                                            <?php } ?>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('charge_type'); ?></span>
                                    </div>
                                    <div class="col-sm-2">
                                        <label class="form-label"><?php echo $this->lang->line('charge_category'); ?></label><small class="req"> *</small>
                                        <select name="charge_category" id="charge_category" class="w-100 form-control form-control-sm select2 charge_category reset_value">
                                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('charge_category'); ?></span>
                                    </div>
                                    <div class="col-sm-2">
                                        <label class="form-label"><?php echo $this->lang->line('charge_name'); ?></label><small class="req"> *</small>
                                        <select name="charge_id" id="charge_id" class="w-100 form-control form-control-sm addcharge select2 reset_value">
                                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('code'); ?></span>
                                    </div>
                                    <div class="col-sm-2">
                                        <label class="form-label"><?php echo $this->lang->line('standard_charge') . " (" . $currency_symbol . ")" ?></label>
                                        <input type="text" readonly name="standard_charge" id="addstandard_charge" class="form-control form-control-sm reset_value" value="<?php echo set_value('standard_charge'); ?>">
                                        <span class="text-danger"><?php echo form_error('standard_charge'); ?></span>
                                    </div>
                                    <div class="col-sm-2">
                                        <label class="form-label"><?php echo $this->lang->line('tpa_charge') . " (" . $currency_symbol . ")" ?></label>
                                        <input type="text" readonly name="schedule_charge" id="addscd_charge" class="form-control form-control-sm reset_value" value="<?php echo set_value('schedule_charge'); ?>">
                                        <span class="text-danger"><?php echo form_error('schedule_charge'); ?></span>
                                    </div>
                                    <div class="col-sm-2">
                                        <label class="form-label"><?php echo $this->lang->line('qty'); ?></label><small class="req"> *</small>
                                        <input type="text" name="qty" id="qty" class="form-control form-control-sm reset_qty" value="1">
                                        <span class="text-danger"><?php echo form_error('qty'); ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Cards 3+4 side by side -->
                        <div class="d-flex flex-wrap gap-3">
                            <!-- Card 4: Billing summary -->
                            <div class="sh-flex-col">
                                <div class="sh-form-card h-100 overflow-hidden">
                                    <div class="sh-card-header">
                                        <span class="sh-card-header-title"><?php echo $this->lang->line('net_amount'); ?></span>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between px-3 py-2 border-bottom">
                                        <span class="text-secondary small fw-semibold"><?php echo $this->lang->line('total') . " (" . $currency_symbol . ")"; ?></span>
                                        <input type="text" placeholder="<?= $this->lang->line('total') ?>" value="0" name="apply_charge" id="apply_charge" class="form-control form-control-sm text-end total apply_charge_add_charge sh-bill-summary-input" readonly>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between px-3 py-2 border-bottom">
                                        <span class="text-secondary small fw-semibold"><?php echo $this->lang->line('discount_percentage') . " (" . $currency_symbol . ")"; ?></span>
                                        <div class="d-flex gap-2">
                                            <div class="input-group input-group-sm sh-discount-pct-group">
                                                <input type="text" value="0" name="discount_percentage" id="discount_percentage_add_charge" class="form-control text-end discount_percentage_add_charge">
                                                <span class="input-group-text">%</span>
                                            </div>
                                            <input type="text" value="0" onkeyup="get_percentage(this.value)" placeholder="<?php echo $this->lang->line('discount'); ?>" name="discount_percentage_amount" id="discount_percentage_amount" class="form-control form-control-sm text-end discount_percentage_amount sh-bill-summary-input">
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between px-3 py-2 border-bottom">
                                        <span class="text-secondary small fw-semibold"><?php echo $this->lang->line('tax') . " (" . $currency_symbol . ")"; ?></span>
                                        <div class="d-flex gap-2">
                                            <div class="input-group input-group-sm sh-discount-pct-group">
                                                <input type="text" name="charge_tax" id="charge_tax" class="form-control text-end charge_tax" readonly>
                                                <span class="input-group-text">%</span>
                                            </div>
                                            <input type="text" value="0" name="tax" id="tax" class="form-control form-control-sm text-end tax sh-bill-summary-input" readonly>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between px-3 py-2">
                                        <span class="text-secondary small fw-semibold"><?php echo $this->lang->line('net_amount') . " (" . $currency_symbol . ")"; ?></span>
                                        <input type="text" placeholder="<?= $this->lang->line('net_amount') ?>" value="0" name="amount" id="final_amount" class="form-control form-control-sm text-end fw-semibold net_amount sh-bill-summary-input" readonly>
                                    </div>
                                </div>
                            </div>
                            <!-- Card 3: Note + Date + Add -->
                            <div class="sh-flex-col">
                                <div class="sh-form-card h-100">
                                    <div class="sh-card-header">
                                        <span class="sh-card-header-title"><?php echo $this->lang->line('charge_note'); ?></span>
                                    </div>
                                    <div class="p-2">
                                        <div class="row g-2">
                                            <div class="col-12">
                                                <label class="form-label"><?php echo $this->lang->line('date'); ?></label><small class="req"> *</small>
                                                <input id="charge_date" name="date" type="text" class="form-control form-control-sm datetime">
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label"><?php echo $this->lang->line('charge_note'); ?></label>
                                                <textarea name="note" id="edit_note" rows="3" class="form-control form-control-sm"></textarea>
                                            </div>
                                            <div class="col-12 text-end">
                                                <button type="submit" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i>" name="charge_data" value="add" id="add_chargesbtn" class="btn btn-info btn-sm"><i class="fa fa-plus me-1"></i><?php echo $this->lang->line('add'); ?></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div><!--./d-flex-->
                        <!-- Preview charges table -->
                        <div class="sh-form-card mt-3">
                            <div class="p-0">
                                <div>
                                    <table class="table table-sm table-striped table-bordered sh-tbl-fixed mb-0 sh-table-fixed" >
                                        <thead class="table-light">
                                            <tr>
                                                <th style="width:11%;"><?php echo $this->lang->line('date'); ?></th>
                                                <th style="width:8%;"><?php echo $this->lang->line('charge_type'); ?></th>
                                                <th style="width:10%;"><?php echo $this->lang->line('charge_category'); ?></th>
                                                <th style="width:11%;"><?php echo $this->lang->line('charge_name'); ?><br><?php echo $this->lang->line('charge_note'); ?></th>
                                                <th class="text-end" style="width:10%;"><?php echo $this->lang->line('standard_charge') . ' (' . $currency_symbol . ')'; ?></th>
                                                <th class="text-end" style="width:9%;"><?php echo $this->lang->line('tpa_charge') . ' (' . $currency_symbol . ')'; ?></th>
                                                <th class="text-end" style="width:5%;"><?php echo $this->lang->line('qty'); ?></th>
                                                <th class="text-end" style="width:7%;"><?php echo $this->lang->line('total') . ' (' . $currency_symbol . ')'; ?></th>
                                                <th class="text-end" style="width:8%;"><?php echo $this->lang->line('discount') . ' (' . $currency_symbol . ')'; ?></th>
                                                <th class="text-end" style="width:7%;"><?php echo $this->lang->line('tax') . ' (' . $currency_symbol . ')'; ?></th>
                                                <th class="text-end" style="width:10%;"><?php echo $this->lang->line('net_amount') . ' (' . $currency_symbol . ')'; ?></th>
                                                <th class="text-center" style="width:4%;"><?php echo $this->lang->line('action'); ?></th>
                                            </tr>
                                        </thead>
                                        <tbody id="preview_charges"></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                    <button type="submit" data-loading-text='<i class="fa fa-circle-o-notch fa-spin"></i>' value="save" id="saveAddCharges" name="charge_data" class="btn btn-info"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save') ?></button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- -->

<!-- Add Diagnosis -->
<div class="modal fade sh-modal sh-modal-accent" id="add_operationtheatre" tabindex="-1" aria-labelledby="add_operationtheatreLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="add_operationtheatreLabel"><?php echo $this->lang->line("add_operation"); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12">
                            <form id="form_operationtheatre" accept-charset="utf-8" enctype="multipart/form-data" method="post">
                                <div class="row">
                                      <input type="hidden" value="<?php echo $opdid ?>" name="opdid" class="form-control" id="opdid" /> 
                                      <div class="col-sm-6">
                                            <div class="mb-3">
                                                <label><?php echo $this->lang->line('operation_category'); ?></label><small class="req"> *</small>
                                                <select name="operation_category" id="operation_category" class="form-control select2 w-100" onchange="getcategory(this.value)" >
                                                    <option value=""><?php echo $this->lang->line('select') ?></option>
                                                    <?php foreach($categorylist as $operation){ ?>
                                                    <option value="<?php echo $operation['id']; ?>"><?php echo $operation['category']; ?></option>
                                                <?php } ?>
                                                </select>
                                                <span class="text-danger"><?php echo form_error('operation_category'); ?></span>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="mb-3">
                                                <label><?php echo $this->lang->line('operation_name'); ?></label>
                                                <small class="req"> *</small> 
                                                <div>
                                                    <select name="operation_name" id="operation_name" class="form-control select2  w-100" >
                                                </select>
                                                </div>                                                
                                                <span class="text-danger"><?php echo form_error('operation_name'); ?></span>
                                            </div>
                                        </div>                                        
                                        <div class="col-sm-6">
                                            <div class="mb-3">
                                                <label><?php echo $this->lang->line('operation_date'); ?></label>
                                                <small class="req"> *</small> 
                                                <input type="text" id="date" name="date" class="form-control datetime">
                                                <span class="text-danger"><?php echo form_error('date'); ?></span>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="mb-3">
                                                <label>
                                                    <?php echo $this->lang->line('consultant_doctor'); ?></label>
                                                <small class="req"> *</small> 
                                                <div><select class="form-control select2"  <?php
                                                    if ($disable_option == true) {
                                                        echo "disabled";
                                                    }
                                                    ?> class="w-100" id='consultant_doctorid' name='consultant_doctor' >
                                                        <option value="<?php echo set_value('consultant_doctor'); ?>"><?php echo $this->lang->line('select') ?></option>
                                                        <?php foreach ($doctors as $dkey => $dvalue) {
                                                            ?>
                                                            <option value="<?php echo $dvalue["id"]; ?>" <?php
                                                                    if ((isset($doctor_select)) && ($doctor_select == $dvalue["id"])) {
                                                                        echo "selected";
                                                                    }
                                                                    ?>><?php echo composeStaffNameByString($dvalue["name"] , $dvalue["surname"],$dvalue["employee_id"]); ?></option>   
                                                                    <?php } ?>
                                                    </select>
                                                    <input type="hidden" id="consultant_doctorname" name="consultant_doctor">
                                                </div>
                                                <span class="text-danger"><?php echo form_error('consultant_doctor'); ?></span>
                                            </div>
                                        </div> 
                                        <div class="col-sm-6">
                                            <div class="mb-3">
                                                <label><?php echo $this->lang->line('assistant_consultant') . " " . '1'; ?></label>
                                                <input type="text" name="ass_consultant_1" class="form-control">              
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="mb-3">
                                                <label><?php echo $this->lang->line('assistant_consultant') . " " . '2'; ?></label>
                                                <input type="text" name="ass_consultant_2" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="mb-3">
                                                <label><?php echo $this->lang->line('anesthetist'); ?></label>
                                                <input type="text" name="anesthetist" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="mb-3">
                                                <label><?php echo $this->lang->line('anesthesia_type'); ?></label>
                                                <input type="text" name="anaethesia_type" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="mb-3">
                                                <label><?php echo $this->lang->line('ot_technician'); ?></label>
                                                <input type="text" name="ot_technician" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="mb-3">
                                                <label><?php echo $this->lang->line('ot_assistant'); ?></label>
                                                <input type="text" value="" name="ot_assistant" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="mb-3">
                                                <label><?php echo $this->lang->line('remark'); ?></label>
                                                <textarea name="ot_remark" id="ot_remark" class="form-control" ></textarea> 
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="mb-3">
                                                <label><?php echo $this->lang->line('result'); ?></label>
                                                <textarea name="ot_result" id="ot_result" class="form-control"></textarea>
                                            </div>
                                        </div>
                                        <div>
                                            <?php echo display_custom_fields('operationtheatre'); ?>
                                        </div>                                      
                                </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                    <button type="submit" id="form_operationtheatrebtn" data-loading-text="<?php echo $this->lang->line('processing'); ?>" class="btn btn-info"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
            </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Operation Theatre -->
<div class="modal fade sh-modal sh-modal-accent" id="edit_operationtheatre" tabindex="-1" aria-labelledby="edit_operationtheatreLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="edit_operationtheatreLabel"><?php echo $this->lang->line("edit_operation"); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12">
                            <form id="form_editoperationtheatre" accept-charset="utf-8" enctype="multipart/form-data" method="post">
                                <div class="row">
                                      <input type="hidden" value="<?php echo $opdid ?>" name="opdid" class="form-control" id="opdid" /> 
                                    <input type="hidden" value="" name="otid" class="form-control" id="otid" />    <div class="col-sm-6">
                                            <div class="mb-3">
                                                <label><?php echo $this->lang->line('operation_category'); ?></label>
                                                   <small class="req"> *</small>
                                                <select name="eoperation_category" id="eoperation_category" class="form-control select2 w-100" onchange="getcategory(this.value)" >
                                                    <option value=""><?php echo $this->lang->line('select') ?></option>
                                                    <?php foreach($categorylist as $operation){ ?>
                                                    <option value="<?php echo $operation['id']; ?>"><?php echo $operation['category']; ?></option>
                                                <?php } ?>
                                                </select>
                                                <span class="text-danger"><?php echo form_error('operation_category'); ?></span>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="mb-3">
                                                <label><?php echo $this->lang->line('operation_name'); ?></label>
                                                <small class="req"> *</small>
                                                <div>
                                                    <select name="eoperation_name" id="eoperation_name" class="form-control select2 w-100"  >
                                                    <option value=""><?php echo $this->lang->line('select') ?></option>
                                                    <?php foreach($operationlist as $operation){ ?>
                                                    <option value="<?php echo $operation['id']; ?>"><?php echo $operation['operation']; ?></option>
                                                <?php } ?>
                                                </select>
                                                </div>
                                                <span class="text-danger"><?php echo form_error('operation_name'); ?></span>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="mb-3">
                                                <label><?php echo $this->lang->line('operation_date'); ?></label>
                                                <small class="req"> *</small> 
                                                <input type="text" value="" id="edate" name="date" class="form-control datetime">
                                                <span class="text-danger"><?php echo form_error('date'); ?></span>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="mb-3">
                                                <label for="exampleInputFile">
                                                    <?php echo $this->lang->line('consultant_doctor'); ?></label>  <small class="req"> *</small> 
                                                <div>
                                                    <select class="form-control select2"  <?php
                                                    if ($disable_option == true) {
                                                        echo "disabled";
                                                    }
                                                    ?> class="w-100" id='econsultant_doctorid' name='consultant_doctor' >
                                                        <option value="<?php echo set_value('consultant_doctor'); ?>"><?php echo $this->lang->line('select') ?></option>
                                                        <?php foreach ($doctors as $dkey => $dvalue) {
                                                            ?>
                                            <option value="<?php echo $dvalue["id"]; ?>" <?php
                                                                    if ((isset($doctor_select)) && ($doctor_select == $dvalue["id"])) {
                                                                        echo "selected";
                                                                    }
                                                                    ?>><?php echo composeStaffNameByString($dvalue["name"] , $dvalue["surname"],$dvalue["employee_id"]); ?></option>   
                                                                    <?php } ?>
                                                    </select>
                                                    <input type="hidden" id="econsultant_doctorname" name="consultant_doctor">
                                                </div>
                                                <span class="text-danger"><?php echo form_error('consultant_doctor'); ?></span>
                                            </div>
                                        </div> 
                                        <div class="col-sm-6">
                                            <div class="mb-3">
                                                <label><?php echo $this->lang->line('assistant_consultant') . " " . '1'; ?></label>
                                                <input type="text" name="ass_consultant_1" id="eass_consultant_1" class="form-control">                     
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="mb-3">
                                                <label><?php echo $this->lang->line('assistant_consultant') . " " . '2'; ?></label>
                                                <input type="text" name="ass_consultant_2"  id="eass_consultant_2" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="mb-3">
                                                <label><?php echo $this->lang->line('anesthetist'); ?></label>
                                                <input type="text" name="anesthetist" id="eanesthetist" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="mb-3">
                                                <label><?php echo $this->lang->line('anesthesia_type'); ?></label>
                                                <input type="text" name="anaethesia_type" id="eanaethesia_type" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="mb-3">
                                                <label><?php echo $this->lang->line('ot_technician'); ?></label>
                                                <input type="text" name="ot_technician" id="eot_technician" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="mb-3">
                                                <label><?php echo $this->lang->line('ot_assistant'); ?></label>
                                                <input type="text" value="" name="ot_assistant"  id="eot_assistant"  class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="mb-3">
                                                <label><?php echo $this->lang->line('remark'); ?></label>
                                                <textarea name="eot_remark" id="eot_remark" class="form-control" ></textarea> 
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="mb-3">
                                                <label><?php echo $this->lang->line('result'); ?></label>
                                                <textarea name="eot_result" id="eot_result" class="form-control"></textarea>
                                            </div>
                                        </div>
                                        <div id="custom_fields_ot">
                                            
                                        </div>                                       
                                </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                    <button type="submit" id="form_editoperationtheatrebtn" data-loading-text="<?php echo $this->lang->line('processing'); ?>" class="btn btn-info"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
            </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade sh-modal sh-modal-accent" id="myaddMedicationModal" tabindex="-1" aria-labelledby="myaddMedicationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myaddMedicationModalLabel"><?php echo $this->lang->line("add_medication_dose"); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
                <form id="add_medication" accept-charset="utf-8" method="post">
                    <div class="modal-body">
                                <div class="row">
                                     <div class="col-md-6 col-lg-6 col-sm-6">
                                        <div class="mb-3">
                                            <label><?php echo $this->lang->line('date'); ?></label><small class="req"> *</small> 
                                            <input type="text" name="date" id="date" class="form-control date">
                                        <span class="text-danger"><?php echo form_error('date'); ?></span>
                                        <input type="hidden" name="opdid" value="<?php echo $opdid ?>" >
                                        </div>
                                    </div> 
                                    <div class="col-md-6 col-lg-6 col-sm-6">
                                        <div class="mb-3">
                                            <label for="pwd"><?php echo $this->lang->line("time"); ?></label><small class="req"> *</small>
                                            <div class="bootstrap-timepicker">
                                                <div class="mb-3">
                                                    <div class="input-group">
                                                        <input type="text" name="time" class="form-control timepicker" id="mtime" value="<?php echo set_value('time'); ?>">
                                                        <div class="input-group-text">
                                                            <i class="fa fa-clock-o"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <span class="text-danger"><?php echo form_error('time'); ?></span>
                                        </div>
                                    </div>                                    
                                </div>
                                <div class="row">                       
                                    <div class="col-md-6 col-lg-6 col-sm-6">
                                        <div class="mb-3">
                                            <label><?php echo $this->lang->line("medicine_category"); ?></label><small class="req"> *</small>
                                            <select class="form-control medicine_category_medication select2 w-100"  id="mmedicine_category_id" name='medicine_category_id'>
                                                <option value="<?php echo set_value('medicine_category_id'); ?>"><?php echo $this->lang->line('select') ?>
                                                </option>
                                                    <?php foreach ($medicineCategory as $dkey => $dvalue) {
                                                    ?>
                                                    <option value="<?php echo $dvalue["id"]; ?>"><?php echo $dvalue["medicine_category"] ?>
                                                    </option>
                                                            <?php }?>
                                                </select>   
                                            <span class="text-danger"><?php echo form_error('medicine_category_id'); ?></span>
                                        </div>
                                    </div> 
                                     <div class="col-md-6 col-lg-6 col-sm-6">
                                        <div class="mb-3">
                                            <label><?php echo $this->lang->line("medicine_name"); ?></label><small class="req"> *</small> 
                                        <select class="form-control select2 medicine_name_medication w-100"   id="mmedicine_id" name='medicine_name_id'>
                                                <option value=""><?php echo $this->lang->line('select') ?>
                                                    </option>
                                                </select>
                                            <span class="text-danger"><?php echo form_error('medicine_name_id'); ?></span>
                                        </div>
                                    </div> 
                                    <div class="col-md-6 col-lg-6 col-sm-6">
                                        <div class="mb-3">
                                            <label><?php echo $this->lang->line("dosage"); ?></label><small class="req"> *</small> 
                                        <select class="form-control select2 dosage_medication w-100"   id="dosage" onchange="get_dosagename(this.value)" name='dosage'>
                                                <option value=""><?php echo $this->lang->line('select') ?>
                                                    </option>
                                                </select>
                                            <span class="text-danger"><?php echo form_error('dosage'); ?></span>
                                        </div>
                                    </div> 
                                </div>                                
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label><?php echo $this->lang->line("remarks"); ?></label> 
                                            <textarea  name="remark" id="remark" class="form-control"></textarea>
                                        </div>
                                    </div>
                                </div>
                </div>            
            <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                    <button type="submit" id="add_medicationbtn" data-loading-text="<?php echo $this->lang->line('processing'); ?>" class="btn btn-info"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
            </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade sh-modal sh-modal-accent" id="myMedicationModal" tabindex="-1" aria-labelledby="myMedicationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myMedicationModalLabel"><?php echo $this->lang->line("add_medication_dose"); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="add_medicationdose" accept-charset="utf-8" method="post">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label><?php echo $this->lang->line('date'); ?></label><small class="req"> *</small> 
                                <input type="text" name="date" id="add_dose_date" class="form-control date">
                                <span class="text-danger"><?php echo form_error('date'); ?></span>
                                <input type="hidden" name="opdid" value="<?php echo $opdid ?>" >
                            </div>
                        </div> 
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="pwd"><?php echo $this->lang->line("time"); ?></label><small class="req"> *</small> 
                                <div class="bootstrap-timepicker">
                                    <div class="mb-3">
                                        <div class="input-group">
                                            <input type="text" name="time" class="form-control timepicker" id="add_dose_time" value="<?php echo set_value('time'); ?>">
                                            <div class="input-group-text">
                                                <i class="fa fa-clock-o"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <span class="text-danger"><?php echo form_error('time'); ?></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label><?php echo $this->lang->line("medicine_category"); ?></label><small class="req"> *</small>
                                <select class="form-control medicine_category_medication select2 w-100"  id="add_dose_medicine_category" name='medicine_category_id'>
                                    <option value=""><?php echo $this->lang->line('select') ?>
                                    </option>
                                        <?php foreach ($medicineCategory as $dkey => $dvalue) {
                                        ?>
                                        <option value="<?php echo $dvalue["id"]; ?>"><?php echo $dvalue["medicine_category"] ?>
                                        </option>
                                                <?php }?>
                                    </select>   
                                <span class="text-danger"><?php echo form_error('medicine_category_id'); ?></span>
                            </div>
                        </div> 
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label><?php echo $this->lang->line("medicine_name"); ?></label><small class="req"> *</small> 
                            <select class="form-control select2 medicine_name_medication w-100"   id="add_dose_medicine_id" name='medicine_name_id'>
                                    <option value=""><?php echo $this->lang->line('select') ?>
                                        </option>
                                    </select>
                                <span class="text-danger"><?php echo form_error('medicine_name_id'); ?></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label><?php echo $this->lang->line("dosage"); ?></label> <small class="req"> *</small> 
                            <select class="form-control select2 dosage_medication w-100"   id="mdosage" onchange="" name='dosage'>
                                    <option value=""><?php echo $this->lang->line('select'); ?>
                                        </option>
                                    </select>
                                <span class="text-danger"><?php echo form_error('dosage'); ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label><?php echo $this->lang->line("remarks"); ?></label> 
                                <textarea  name="remark" id="remark" class="form-control"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                    <button type="submit" id="add_medicationdosebtn" data-loading-text="<?php echo $this->lang->line('processing'); ?>" class="btn btn-info"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade sh-modal sh-modal-accent" id="myMedicationDoseModal" tabindex="-1" aria-labelledby="myMedicationDoseModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myMedicationDoseModalLabel"><?php echo  $this->lang->line("edit_medication_dose"); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="update_medication" accept-charset="utf-8" method="post">
                   <div class="modal-body">
                        <?php if ($this->rbac->hasPrivilege('opd_medication', 'can_delete')) { ?>
                            <div id='edit_delete_medication' class="mb-2"></div>
                        <?php } ?>
                        <input type="hidden" name="medication_id" id="medication_id" value="">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label><?php echo $this->lang->line('date'); ?></label><small class="req"> *</small> 
                                        <input type="text" name="date" id="date_edit_medication" class="form-control date">
                                    <span class="text-danger"><?php echo form_error('date'); ?></span>
                                    <input type="hidden" name="opdid" value="<?php echo $opdid ?>" >
                                    </div>
                                </div> 
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="pwd"><?php echo $this->lang->line("time"); ?></label><small class="req"> *</small>
                                        <div class="bootstrap-timepicker">
                                            <div class="mb-3">
                                                <div class="input-group">
                                                    <input type="text" name="time" class="form-control timepicker" id="dosagetime" value="<?php echo set_value('time'); ?>">
                                                    <div class="input-group-text">
                                                        <i class="fa fa-clock-o"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div> 
                                        <span class="text-danger"><?php echo form_error('time'); ?></span>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label><?php echo $this->lang->line("medicine_category"); ?></label><small class="req"> *</small>
                                        <select class="form-control medicine_category_medication select2 w-100"  id="mmedicine_category_edit_id" name='medicine_category_id'>
                                            <option value="<?php echo set_value('medicine_category_id'); ?>"><?php echo $this->lang->line('select') ?>
                                            </option>
                                                <?php foreach ($medicineCategory as $dkey => $dvalue) {
                                                ?>
                                                <option value="<?php echo $dvalue["id"]; ?>"><?php echo $dvalue["medicine_category"] ?>
                                                </option>
                                                        <?php }?>
                                            </select>   
                                        <span class="text-danger"><?php echo form_error('medicine_category_id'); ?></span>
                                    </div>
                                </div> 
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label><?php echo $this->lang->line("medicine_name"); ?></label><small class="req"> *</small> 
                                    <select class="form-control select2 medicine_name_medication w-100"   id="mmedicine_edit_id" name='medicine_name_id'>
                                            <option value=""><?php echo $this->lang->line('select') ?>
                                                </option>
                                            </select>
                                        <span class="text-danger"><?php echo form_error('medicine_name_id'); ?></span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label><?php echo $this->lang->line("dosage"); ?></label><small class="req"> *</small>
                                        <select class="form-control  select2 w-100"  id="medicine_dose_edit_id" name='dosage_id'>
                                        <option value="<?php echo set_value('dosage_id'); ?>"><?php echo $this->lang->line('select'); ?>
                                        </option>
                                        <?php foreach ($dosage as $key => $value) { ?>
                                        <option value="<?php echo $value["id"]; ?>"><?php echo $value["dosage"]." ".$value['unit'] ; ?>
                                                </option>                                        
                                        <?php } ?>
                                        </select>   
                                        <span class="text-danger"><?php echo form_error('dosage_id'); ?></span>
                                    </div>
                                </div>
                            </div>
                             
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label><?php echo $this->lang->line("remarks"); ?></label> 
                                        <textarea  name="remark" id="medicine_dosage_remark" class="form-control"></textarea>
                                      
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                            <button type="submit" id="update_medicationbtn" data-loading-text="<?php echo $this->lang->line('processing'); ?>" class="btn btn-info"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                        </div>
                </form>
        </div>
    </div>
</div>

<!--lab investigation modal-->
<div class="modal fade sh-modal sh-modal-accent" id="viewDetailReportModal" tabindex="-1" aria-labelledby="modal_head" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal_head"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id='action_detail_report_modal' class="mb-2"></div>
                <div id="reportbilldata"></div>
            </div>
        </div>
    </div>
</div>
<!-- end lab investigation modal-->

<!-- Timeline -->
<div class="modal fade sh-modal sh-modal-accent" id="myTimelineModal" tabindex="-1" aria-labelledby="myTimelineModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myTimelineModalLabel"><?php echo $this->lang->line('add_timeline'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12">
                            <form id="add_timeline" accept-charset="utf-8"  enctype="multipart/form-data" method="post">
                                <div class="row">
                                    <div class=" col-md-12">
                                        <div class="mb-3">
                                            <label><?php echo $this->lang->line('title'); ?></label><small class="req"> *</small>
                                            <input type="hidden" name="patient_id" id="patient_id" value="<?php echo $id ?>">
                                            <input id="timeline_title" name="timeline_title" placeholder="" type="text" class="form-control"  />
                                            <span class="text-danger"><?php echo form_error('timeline_title'); ?></span>
                                        </div>
                                        <div class="mb-3">
                                            <label><?php echo $this->lang->line('date'); ?></label><small class="req"> *</small>
                                            <input id="timeline_date" name="timeline_date" value="<?php echo set_value('timeline_date', date($this->customlib->getHospitalDateFormat())); ?>" placeholder="" type="text" class="form-control date"  />
                                            <span class="text-danger"><?php echo form_error('timeline_date'); ?></span>
                                        </div>
                                        <div class="mb-3">
                                            <label><?php echo $this->lang->line('description'); ?></label>
                                            <textarea id="timeline_desc" name="timeline_desc" placeholder=""  class="form-control"></textarea>
                                            <span class="text-danger"><?php echo form_error('description'); ?></span>
                                        </div>
                                        <div class="mb-3">
                                            <label><?php echo $this->lang->line('attach_document'); ?></label>
                                            <div><input id="timeline_doc_id" name="timeline_doc" placeholder="" type="file"  class="filestyle form-control" data-height="40"  value="<?php echo set_value('timeline_doc'); ?>" />
                                                <span class="text-danger"><?php echo form_error('timeline_doc'); ?></span></div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="vertical-align-middle"><?php echo $this->lang->line('visible_to_this_person'); ?></label>
                                            <input id="visible_check" checked="checked" name="visible_check" value="yes" placeholder="" type="checkbox" />
                                        </div>
                                    </div>
                                </div>
                        </div>
                    </div>
                </div>    
            </div>
            <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                    <button type="submit" data-loading-text="<?php echo $this->lang->line('processing') ?>" id="add_timelinebtn" class="btn btn-info"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- -->

<!-- Edit Timeline -->
<div class="modal fade sh-modal sh-modal-accent" id="myTimelineEditModal" tabindex="-1" aria-labelledby="myTimelineEditModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myTimelineEditModalLabel"><?php echo $this->lang->line('edit_timeline'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12">
                            <form id="edit_timeline"   accept-charset="utf-8"  enctype="multipart/form-data" method="post">
                                <div class="row">
                                    <div class=" col-md-12">
                                        <div class="mb-3">
                                            <label><?php echo $this->lang->line('title'); ?></label><small class="req"> *</small>
                                            <input type="hidden" name="patient_id" id="epatientid" value="">
                                            <input type="hidden" name="timeline_id" id="etimelineid" value="">
                                            <input id="etimelinetitle" name="timeline_title" placeholder="" type="text" class="form-control"  />
                                            <span class="text-danger"><?php echo form_error('timeline_title'); ?></span>
                                        </div>
                                        <div class="mb-3">
                                            <label><?php echo $this->lang->line('date'); ?></label><small class="req"> *</small>
                                            <input type="text" name="timeline_date" class="form-control date" id="etimelinedate"/>
                                            <span class="text-danger"><?php echo form_error('timeline_date'); ?></span>
                                        </div>
                                        <div class="mb-3">
                                            <label><?php echo $this->lang->line('description'); ?></label>
                                            <textarea id="timelineedesc" name="timeline_desc" placeholder=""  class="form-control"></textarea>
                                            <span class="text-danger"><?php echo form_error('description'); ?></span>
                                        </div>
                                        <div class="mb-3">
                                            <label><?php echo $this->lang->line('attach_document'); ?></label>
                                            <div><input id="etimeline_doc_id" name="timeline_doc" placeholder="" type="file"  class="filestyle form-control" data-height="40"  value="<?php echo set_value('timeline_doc'); ?>" />
                                                <span class="text-danger"><?php echo form_error('timeline_doc'); ?></span></div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="vertical-align-middle"><?php echo $this->lang->line('visible_to_this_person'); ?></label>
                                            <input id="evisible_check"  name="visible_check" value="yes" placeholder="" type="checkbox" />
                                        </div>
                                    </div>
                                </div>
                        </div>
                    </div>
                </div>    
            </div>
            <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                    <button type="submit" data-loading-text="<?php echo $this->lang->line('processing') ?>" id="edit_timelinebtn" class="btn btn-info"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
            </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade sh-modal sh-modal-accent" id="edit_prescription" tabindex="-1" aria-labelledby="edit_prescriptionLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="edit_prescriptionLabel"><?php echo $this->lang->line('edit') . " " . $this->lang->line('prescription'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body" id="editdetails_prescription">
            </div>
        </div>
    </div> 
</div>
 
<div class="modal fade sh-modal sh-modal-nospace" id="add_prescription" tabindex="-1" aria-labelledby="add_prescriptionLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="add_prescriptionLabel"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="form_prescription" accept-charset="utf-8" enctype="multipart/form-data" method="post">
                    <div class="modal-body sh-scroll-flex" >
                    </div><!--./modal-body-->
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                    <button type="submit" class="btn btn-info" id="form_prescriptionbtn" data-loading-text="<?php echo $this->lang->line('processing') ?>"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade sh-modal sh-modal-accent" id="viewModal" tabindex="-1" aria-labelledby="viewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewModalLabel"><?php echo $this->lang->line('visit_details'); ?></h5>
                <div class="d-flex align-items-center gap-2">
                    <div id="edit_delete" class="d-flex align-items-center gap-2">
                        <?php if ($this->rbac->hasPrivilege('revisit', 'can_edit')) { ?>
                            <a href="javascript:void(0)" class="btn btn-sm btn-light" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('edit'); ?>"><i class="fa fa-pencil"></i></a>
                        <?php } if ($this->rbac->hasPrivilege('revisit', 'can_delete')) { ?>
                            <a href="#" class="btn btn-sm btn-light" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('delete'); ?>"><i class="fa fa-trash"></i></a>
                        <?php } ?>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
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

<!-- -->
<div class="modal fade sh-modal sh-modal-branded" id="prescriptionview" tabindex="-1" aria-labelledby="prescriptionviewLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="prescriptionviewLabel"><?php echo $this->lang->line('prescription'); ?></h5>
                <div class="d-flex align-items-center gap-2" id="edit_deleteprescription"></div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="getdetails_prescription">
            </div>
        </div>
    </div>
</div>

<div class="modal fade sh-modal sh-modal-branded" id="prescriptionviewmanual" tabindex="-1" aria-labelledby="prescriptionviewmanualLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="prescriptionviewmanualLabel"><?php echo $this->lang->line('prescription'); ?></h5>
                <div class="d-flex align-items-center gap-2" id="edit_deleteprescriptionmanual"></div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="getdetails_prescriptionmanual">
            </div>
        </div>
    </div>
</div>

<div class="modal fade sh-modal sh-modal-nospace" id="myModaledit" tabindex="-1" aria-labelledby="myModaleditLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myModaleditLabel"><?php echo $this->lang->line('patient_details'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
                <form id="formeditpa" accept-charset="utf-8" action="" enctype="multipart/form-data" method="post">
                    <div class="modal-body sh-scroll-flex" >
                        <input id="eupdateid" name="updateid" placeholder="" type="hidden" class="form-control"  value="" />
                            <div class="row row-eq">
                                <div class="col-lg-12 col-md-12 col-sm-12">
                                    <div class="row ptt10">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label><?php echo $this->lang->line('name'); ?></label><small class="req"> *</small> 
                                                <input id="ename" name="name" placeholder="" type="text" class="form-control"  value="<?php echo set_value('name'); ?>" />
                                                <span class="text-danger"><?php echo form_error('name'); ?></span>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="mb-3">
                                                <label><?php echo $this->lang->line('guardian_name') ?></label>
                                                <input type="text" name="guardian_name"  id="eguardian_name"placeholder="" value="" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-sm-12">  
                                            <div class="row">  
                                                <div class="col-sm-3">
                                                    <div class="mb-3">
                                                        <label> <?php echo $this->lang->line('gender'); ?></label>
                                                        <select class="form-control" name="gender" id="egenders">
                                                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                                                            <?php
                                                            foreach ($genderList as $key => $value) {
                                                                ?>
                                                                <option value="<?php echo $key; ?>" <?php if (set_value('gender') == $key) echo "selected"; ?>><?php echo $value; ?></option>
                                                                <?php
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-sm-4">
                                                    <div class="mb-3">
                                                        <label for="dob"><?php echo $this->lang->line('date_of_birth'); ?></label> 
                                                        <input type="text" name="dob" id="ebirth_date" placeholder="" class="form-control date" /><?php echo set_value('dob'); ?>
                                                    </div>
                                                </div>
                                                <div class="col-sm-5" id="calculate">
                                                    <div class="mb-3">
                                                        <label><?php echo $this->lang->line('age').' ('.$this->lang->line('yy_mm_dd').')'; ?></label><small class="req"> *</small> 
                                                        <div class="sh-clear">
                                                            <input type="text" placeholder="<?php echo $this->lang->line('year'); ?>" name="age[year]" id="eage_year" value="" class="form-control sh-print-left-40" >
                                                            <input type="text" id="eage_month" placeholder="<?php echo $this->lang->line('month'); ?>" name="age[month]" value="" class="form-control sh-print-left-40" >
                                                             <input type="text" id="eage_day" placeholder="<?php echo $this->lang->line('day'); ?>" name="age[day]" value="" class="form-control sh-print-left-40" >
                                                        </div>
                                                    </div>
                                                </div> 
                                            </div>  
                                        </div><!--./col-md-6-->  
                                        <div class="col-md-6 col-sm-12"> 
                                            <div class="row"> 
                                                <div class="col-md-3">
                                                    <div class="mb-3">
                                                        <label> <?php echo $this->lang->line('blood_group'); ?></label>
                                                        <select class="form-control" id="blood_groups" name="blood_group">
                                                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                                                            <?php
                                                                foreach ($bloodgroup as $key => $value) {
                                                                    ?>
                                                                  <option value="<?php echo $key; ?>" <?php if (set_value('blood_group') == $key) {
                                                                    echo "selected";
                                                                   }
                                                                ?>><?php echo $value; ?></option>
                                                                <?php
                                                                }
                                                            ?>
                                                        </select>
                                                        <span class="text-danger"><?php echo form_error('blood_group'); ?></span>
                                                    </div>
                                                </div>
                                                <div class="col-sm-3">
                                                    <div class="mb-3">
                                                        <label for="pwd"><?php echo $this->lang->line('marital_status'); ?></label>
                                                        <select name="marital_status" id="marital_statuss" class="form-control">
                                                            <option value=""><?php echo $this->lang->line('select') ?></option>
                                                            <?php foreach ($marital_status as $key => $value) {
                                                                ?>
                                                                <option value="<?php echo $value; ?>" <?php if (set_value('marital_status') == $key) echo "selected"; ?>><?php echo $value; ?></option>
                                                            <?php } ?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="mb-3">
                                                        <label for="exampleInputFile">
                                                            <?php echo $this->lang->line('patient') . " " . $this->lang->line('photo'); ?>
                                                        </label>
                                                        <div>
                                                            <input class="filestyle form-control-file" type='file' name='file' id="exampleInputFile" size='20' data-height="26" data-default-file="<?php echo base_url() ?>uploads/patient_images/no_image.png" >
                                                        </div>
                                                        <span class="text-danger"><?php echo form_error('file'); ?></span>
                                                    </div>
                                                </div>
                                            </div> 
                                        </div><!--./col-md-6-->
                                        <div class="col-sm-3">
                                            <div class="mb-3">
                                                <label for="pwd"><?php echo $this->lang->line('phone'); ?></label>
                                                <input id="emobileno" autocomplete="off" name="contact"  type="text" placeholder="" class="form-control"  value="<?php echo set_value('mobileno'); ?>" />
                                            </div>
                                        </div> 
                                        <div class="col-sm-3">
                                            <div class="mb-3">
                                                <label><?php echo $this->lang->line('email'); ?></label>
                                                <input type="text" placeholder="" id="eemail" value="<?php echo set_value('email'); ?>" name="email" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="mb-3">
                                                <label for="address"><?php echo $this->lang->line('address'); ?></label> 
                                                <input name="address" id="eaddress" placeholder="" class="form-control" /><?php echo set_value('address'); ?>
                                            </div> 
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="mb-3">
                                                <label for="pwd"><?php echo $this->lang->line('remarks'); ?></label> 
                                                <textarea name="note" id="enote" class="form-control" ><?php echo set_value('note'); ?></textarea>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="mb-3">
                                                <label for="email"><?php echo $this->lang->line('any_known_allergies'); ?></label> 
                                                <textarea name="known_allergies" id="eknown_allergies" placeholder="" class="form-control" ><?php echo set_value('address'); ?></textarea>
                                            </div> 
                                        </div> 
                                    <div id="customfieldpatient">
                                        
                                    </div> 
                                    </div><!--./row--> 
                                </div><!--./col-md-8--> 
                            </div><!--./row--> 
                         </div> 
         
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                                    <button type="submit" id="formeditpabtn" data-loading-text="<?php echo $this->lang->line('processing') ?>" class="btn btn-info"><?php echo $this->lang->line('save'); ?></button>
                                </div>
                        </form>
					</div>
    </div>
</div>
<div class="modal fade sh-modal sh-modal-accent" id="patient_discharge" tabindex="-1" aria-labelledby="patient_dischargeLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="patient_dischargeLabel"><?php echo $this->lang->line('patient_discharge'); ?></h5>
                <div class="d-flex align-items-center gap-2">
                    <div id="allpayments_print" class="d-flex align-items-center gap-2"></div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            </div>
            <div class="pup-scroll-area">
                <div class="modal-body sh-modal-canvas" id="patient_discharge_result">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('close'); ?></button>
                <?php if ($this->rbac->hasPrivilege('opd_patient_discharge', 'can_add') || $this->rbac->hasPrivilege('opd_patient_discharge', 'can_edit')) { ?>
                <button type="submit" form="form_patient_discharge" data-loading-text="<?php echo $this->lang->line('processing'); ?>" class="btn btn-info"><i class="fa fa-check-circle"></i> <span><?php echo $this->lang->line('save'); ?></span></button>
                <?php } ?>
            </div>
        </div>
    </div>
</div>

<!-- discharged summary   -->
<div class="modal fade sh-modal sh-modal-nospace" id="revisitModal" tabindex="-1" aria-labelledby="revisitModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="revisitModalLabel"><?php echo $this->lang->line('patient_details'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formrevisit" accept-charset="utf-8" enctype="multipart/form-data" method="post">
                <input type="hidden" name="id" id="pid">
                <input type="hidden" name="password" id="revisit_password">
                <input type="hidden" name="opd_id" value="<?php echo $result['id']; ?>">
                <input type="hidden" name="case_reference_id" value="<?php echo $result['case_reference_id']; ?>">
                <input type="hidden" name="email" id="revisit_email">
                <input type="hidden" name="contact" id="revisit_contact">
                <input id="revisit_name" name="name" type="hidden" value="" />
                <div class="pup-scroll-area">
                    <div class="modal-body modal-background">
                        <div class="row">

                            <!-- LEFT: Patient Info + Symptoms -->
                            <div class="col-lg-8">

                                <!-- Patient Info Card -->
                                <div class="sh-form-card mb-2" id="patientDetails">
                                    <div class="sh-card-header">
                                        <span class="sh-card-header-title" id="patientname"></span>
                                    </div>
                                    <div class="d-flex align-items-start">
                                        <div class="sh-info-grid flex-grow-1">
                                            <div class="row g-0">
                                                <div class="sh-info-item col-6 col-md-3">
                                                    <small class="sh-info-label"><i class="fas fa-user-secret"></i> <?php echo $this->lang->line('guardian'); ?></small>
                                                    <span class="sh-info-value" id="guardian"></span>
                                                </div>
                                                <div class="sh-info-item col-6 col-md-3">
                                                    <small class="sh-info-label"><i class="fas fa-venus-mars"></i> <?php echo $this->lang->line('gender'); ?></small>
                                                    <span class="sh-info-value" id="rgender"></span>
                                                </div>
                                                <div class="sh-info-item col-6 col-md-3">
                                                    <small class="sh-info-label"><i class="fas fa-tint"></i> <?php echo $this->lang->line('blood_group'); ?></small>
                                                    <span class="sh-info-value" id="rblood_group"></span>
                                                </div>
                                                <div class="sh-info-item col-6 col-md-3">
                                                    <small class="sh-info-label"><i class="fas fa-ring"></i> <?php echo $this->lang->line('marital_status'); ?></small>
                                                    <span class="sh-info-value" id="rmarital_status"></span>
                                                </div>
                                            </div>
                                            <div class="sh-row-divider row g-0">
                                                <div class="sh-info-item col-6 col-md-3">
                                                    <small class="sh-info-label"><i class="fas fa-hourglass-half"></i> <?php echo $this->lang->line('age'); ?></small>
                                                    <span class="sh-info-value" id="rage"></span>
                                                </div>
                                                <div class="sh-info-item col-6 col-md-3">
                                                    <small class="sh-info-label"><i class="fa fa-phone-square"></i> <?php echo $this->lang->line('phone'); ?></small>
                                                    <span class="sh-info-value" id="listnumber"></span>
                                                </div>
                                                <div class="sh-info-item col-6 col-md-3">
                                                    <small class="sh-info-label"><i class="fa fa-envelope"></i> <?php echo $this->lang->line('email'); ?></small>
                                                    <span class="sh-info-value" id="remail"></span>
                                                </div>
                                                <div class="sh-info-item col-6 col-md-3">
                                                    <small class="sh-info-label"><i class="fas fa-street-view"></i> <?php echo $this->lang->line('address'); ?></small>
                                                    <span class="sh-info-value" id="raddress"></span>
                                                </div>
                                            </div>
                                            <div class="sh-row-divider row g-0">
                                                <div class="sh-info-item col-6 col-md-3">
                                                    <small class="sh-info-label"><i class="fas fa-shield-alt"></i> <?php echo $this->lang->line('tpa'); ?></small>
                                                    <span class="sh-info-value" id="rtpa_name"></span>
                                                </div>
                                                <div class="sh-info-item col-6 col-md-3">
                                                    <small class="sh-info-label"><i class="fas fa-id-badge"></i> <?php echo $this->lang->line('tpa_id'); ?></small>
                                                    <span class="sh-info-value" id="rtpa_id"></span>
                                                </div>
                                                <div class="sh-info-item col-6 col-md-3">
                                                    <small class="sh-info-label"><i class="fas fa-calendar-check"></i> <?php echo $this->lang->line('tpa_validity'); ?></small>
                                                    <span class="sh-info-value" id="rtpa_validity"></span>
                                                </div>
                                                <div class="sh-info-item col-6 col-md-3">
                                                    <small class="sh-info-label"><i class="fas fa-fingerprint"></i> <?php echo $this->lang->line('national_identification_number'); ?></small>
                                                    <span class="sh-info-value" id="ridentification_number"></span>
                                                </div>
                                            </div>
                                            <div class="sh-row-divider row g-0">
                                                <div class="sh-info-item col-6 col-md-3">
                                                    <small class="sh-info-label"><i class="fas fa-allergies"></i> <?php echo $this->lang->line('any_known_allergies'); ?></small>
                                                    <span class="sh-info-value" id="rallergies"></span>
                                                </div>
                                                <div class="sh-info-item col-6 col-md-3">
                                                    <small class="sh-info-label"><i class="fas fa-sticky-note"></i> <?php echo $this->lang->line('remarks'); ?></small>
                                                    <span class="sh-info-value" id="rnote"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="opd-pd-photo-cell">
                                            <img id="patient_image" class="opd-pd-photo-lg d-none" alt="<?php echo $this->lang->line('patient'); ?>">
                                            <div class="opd-pd-initials-lg d-none" id="patient_image_initials"></div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Symptoms Card -->
                                <div class="sh-form-card mb-2">
                                    <div class="sh-card-header">
                                        <span class="sh-card-header-title"><i class="fas fa-stethoscope"></i> <?php echo $this->lang->line('symptoms'); ?></span>
                                    </div>
                                    <div class="p-2">
                                        <div class="row g-2">
                                            <div class="col-sm-4">
                                                <label class="form-label"><?php echo $this->lang->line('symptoms_type'); ?></label>
                                                <select name="symptoms_type" id="act" class="form-control form-control-sm select2 act w-100" >
                                                    <option value=""><?php echo $this->lang->line('select') ?></option>
                                                    <?php foreach ($symptomsresulttype as $dkey => $dvalue) { ?>
                                                        <option value="<?php echo $dvalue["id"]; ?>"><?php echo $dvalue["symptoms_type"]; ?></option>
                                                    <?php } ?>
                                                </select>
                                                <span class="text-danger"><?php echo form_error('symptoms_type'); ?></span>
                                            </div>
                                            <div class="col-sm-4">
                                                <label class="form-label"><?php echo $this->lang->line('symptoms_title'); ?></label>
                                                <div id="dd" class="wrapper-dropdown-3">
                                                    <input class="form-control form-control-sm filterinput" type="text" autocomplete="off">
                                                    <ul class="dropdown scroll150 section_ul">
                                                        <li class="section-placeholder"><span><?php echo $this->lang->line('select'); ?></span></li>
                                                    </ul>
                                                </div>
                                            </div>
                                            <div class="col-sm-4">
                                                <label class="form-label"><?php echo $this->lang->line('symptoms_description'); ?></label>
                                                <textarea name="symptoms" id="esymptoms" class="form-control form-control-sm"></textarea>
                                            </div>
                                            <div class="col-sm-6">
                                                <label class="form-label"><?php echo $this->lang->line('any_known_allergies'); ?></label>
                                                <textarea name="known_allergies" id="eknown_allergies" class="form-control form-control-sm"></textarea>
                                            </div>
                                            <div class="col-sm-6">
                                                <label class="form-label"><?php echo $this->lang->line('note'); ?></label>
                                                <textarea name="note_remark" id="revisit_note" class="form-control form-control-sm"></textarea>
                                            </div>
                                            <div class="col-sm-12">
                                                <?php echo display_custom_fields('opdrecheckup'); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div><!-- /col-lg-8 -->

                            <!-- RIGHT: Appointment + Charge -->
                            <div class="col-lg-4 sh-col-sep-left">

                                <!-- Appointment Card -->
                                <div class="sh-form-card mb-2">
                                    <div class="sh-card-header">
                                        <span class="sh-card-header-title"><i class="fas fa-calendar-alt"></i> <?php echo $this->lang->line('appointment'); ?></span>
                                    </div>
                                    <div class="p-2">
                                        <div class="row g-2">
                                            <div class="col-sm-6">
                                                <label class="form-label"><?php echo $this->lang->line('appointment_date'); ?></label><small class="req"> *</small>
                                                <input id="revisit_date" name="appointment_date" placeholder="" type="text" class="form-control form-control-sm datetime" />
                                                <span class="text-danger"><?php echo form_error('appointment_date'); ?></span>
                                            </div>
                                            <div class="col-sm-6">
                                                <label class="form-label"><?php echo $this->lang->line('case'); ?></label>
                                                <input class="form-control form-control-sm" type="text" id="revisit_case" name="revisit_case" />
                                                <span class="text-danger"><?php echo form_error('case'); ?></span>
                                            </div>
                                            <div class="col-sm-6">
                                                <label class="form-label"><?php echo $this->lang->line('casualty'); ?></label>
                                                <select name="casualty" id="revisit_casualty" class="form-control form-control-sm">
                                                    <?php foreach ($yesno_condition as $yesno_key => $yesno_value) { ?>
                                                        <option value="<?php echo $yesno_key ?>" <?php if ($yesno_key == 'no') { echo "selected"; } ?>><?php echo $yesno_value ?></option>
                                                    <?php } ?>
                                                </select>
                                                <span class="text-danger"><?php echo form_error('casualty'); ?></span>
                                            </div>
                                            <div class="col-sm-6">
                                                <label class="form-label"><?php echo $this->lang->line('old_patient'); ?></label>
                                                <select name="old_patient" id="revisit_old_patient" class="form-control form-control-sm">
                                                    <?php foreach ($yesno_condition as $yesno_key => $yesno_value) { ?>
                                                        <option value="<?php echo $yesno_key ?>" <?php if ($yesno_key == 'no') { echo "selected"; } ?>><?php echo $yesno_value ?></option>
                                                    <?php } ?>
                                                </select>
                                                <span class="text-danger"><?php echo form_error('old_patient'); ?></span>
                                            </div>
                                            <div class="col-sm-6">
                                                <label class="form-label"><?php echo $this->lang->line('reference'); ?></label>
                                                <input class="form-control form-control-sm" id="revisit_refference" type="text" name="refference" />
                                                <span class="text-danger"><?php echo form_error('refference'); ?></span>
                                            </div>
                                            <div class="col-sm-6">
                                                <label class="form-label">&nbsp;</label>
                                                <div class="form-check pt5">
                                                    <input class="form-check-input" type="checkbox" value="1" id="revisit_is_tpa" name="revisit_is_tpa">
                                                    <label class="form-check-label" for="revisit_is_tpa"><?php echo $this->lang->line('apply_tpa'); ?></label>
                                                    <input type="hidden" name="organisation" id="revisit_organisation" value="<?php echo $result['organisation_id']; ?>">
                                                    <input type="hidden" name="insurance_validity" id="revisit_insurance_validity" value="<?php echo $result['insurance_validity']; ?>">
                                                    <input type="hidden" name="insurance_id" id="revisit_insurance_id" value="<?php echo $result['insurance_id']; ?>">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Charge & Payment Card -->
                                <div class="sh-form-card">
                                    <div class="sh-card-header">
                                        <span class="sh-card-header-title"><i class="fas fa-receipt"></i> <?php echo $this->lang->line('consultant_doctor'); ?> &amp; <?php echo $this->lang->line('charge'); ?></span>
                                    </div>
                                    <div class="p-2">
                                        <div class="row g-2">
                                            <div class="col-sm-6">
                                                <label class="form-label"><?php echo $this->lang->line('consultant_doctor'); ?></label>
                                                <select onchange="" class="form-control form-control-sm w-100"  <?php if ($disable_option == true) { echo "disabled"; } ?> name="consultant_doctor" id="revisit_doctor">
                                                    <option value=""><?php echo $this->lang->line('select') ?></option>
                                                    <?php foreach ($doctors as $dkey => $dvalue) { ?>
                                                        <option value="<?php echo $dvalue["id"]; ?>" <?php if ((isset($doctor_select)) && ($doctor_select == $dvalue["id"])) { echo "selected"; } ?>><?php echo composeStaffNameByString($dvalue["name"], $dvalue["surname"], $dvalue["employee_id"]); ?></option>
                                                    <?php } ?>
                                                </select>
                                                <span class="text-danger"><?php echo form_error('consultant_doctor'); ?></span>
                                            </div>
                                            <div class="col-sm-6">
                                                <label class="form-label"><?php echo $this->lang->line('charge_category'); ?></label>
                                                <select name="charge_category" class="w-100 form-control form-control-sm charge_category select2">
                                                    <option value=""><?php echo $this->lang->line('select'); ?></option>
                                                    <?php foreach ($charge_category as $key => $value) { ?>
                                                        <option value="<?php echo $value['id']; ?>"><?php echo $value['name']; ?></option>
                                                    <?php } ?>
                                                </select>
                                                <span class="text-danger"><?php echo form_error('charge_category'); ?></span>
                                            </div>
                                            <div class="col-sm-6">
                                                <label class="form-label"><?php echo $this->lang->line('charge'); ?></label><small class="req"> *</small>
                                                <select name="charge_id" class="w-100 form-control form-control-sm charge select2">
                                                    <option value=""><?php echo $this->lang->line('select') ?></option>
                                                </select>
                                                <span class="text-danger"><?php echo form_error('charge_id'); ?></span>
                                            </div>
                                            <div class="col-sm-6">
                                                <label class="form-label"><?php echo $this->lang->line('standard_charge') . " (" . $currency_symbol . ")" ?></label>
                                                <input type="text" readonly name="standard_charge" id="standard_chargevisit" class="form-control form-control-sm" value="<?php echo set_value('standard_charge'); ?>">
                                                <span class="text-danger"><?php echo form_error('standard_charge'); ?></span>
                                            </div>
                                            <div class="col-sm-6">
                                                <label class="form-label"><?php echo $this->lang->line('applied_charge') . " (" . $currency_symbol . ")" ?></label><small class="req"> *</small>
                                                <input type="text" name="amount" id="apply_chargevisit" class="form-control form-control-sm">
                                                <span class="text-danger"><?php echo form_error('apply_charge'); ?></span>
                                            </div>
                                            <div class="col-sm-6">
                                                <label class="form-label"><?php echo $this->lang->line('discount'); ?></label>
                                                <div class="input-group input-group-sm">
                                                    <input type="text" class="form-control form-control-sm discount_percentage" name="discount_percentage" id="discount_percentage" value="0" autocomplete="off">
                                                    <span class="input-group-text"> %</span>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <label class="form-label"><?php echo $this->lang->line('tax'); ?></label>
                                                <div class="input-group input-group-sm">
                                                    <input type="text" class="form-control form-control-sm right-border-none" name="percentage" id="percentage" readonly autocomplete="off">
                                                    <span class="input-group-text"> %</span>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <label class="form-label"><?php echo $this->lang->line('amount'); ?> <?php echo '(' . $currency_symbol . ')'; ?></label><small class="req"> *</small>
                                                <input name="apply_amount" readonly type="text" class="form-control form-control-sm" id="revisit_amount" />
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Payment Card -->
                                <div class="sh-form-card mt-2">
                                    <div class="sh-card-header">
                                        <span class="sh-card-header-title"><i class="fas fa-money-bill-wave"></i> <?php echo $this->lang->line('payment'); ?></span>
                                    </div>
                                    <div class="p-2">
                                        <div class="row g-2">
                                            <div class="col-sm-6">
                                                <label class="form-label"><?php echo $this->lang->line('payment_mode'); ?></label>
                                                <select name="payment_mode" id="revisit_payment" class="form-control form-control-sm revisit_payment_mode">
                                                    <?php foreach ($payment_mode as $payment_key => $payment_value) { ?>
                                                        <option value="<?php echo $payment_key ?>" <?php if ($payment_key == 'cash') { echo "selected"; } ?>><?php echo $payment_value ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                            <div class="col-sm-6">
                                                <label class="form-label"><?php echo $this->lang->line('paid_amount') . " (" . $currency_symbol . ")"; ?></label><small class="req"> *</small>
                                                <input name="paid_amount" type="text" class="form-control form-control-sm" id="paid_amount" />
                                            </div>
                                            <?php if ($this->module_lib->hasActive('live_consultation')) { ?>
                                                <div class="col-sm-6">
                                                    <label class="form-label"><?php echo $this->lang->line('live_consultation'); ?></label>
                                                    <select name="live_consult" id="live_consultvisit" class="form-control form-control-sm">
                                                        <?php foreach ($yesno_condition as $yesno_key => $yesno_value) { ?>
                                                            <option value="<?php echo $yesno_key ?>" <?php if ($yesno_key == 'no') { echo "selected"; } ?>><?php echo $yesno_value ?></option>
                                                        <?php } ?>
                                                    </select>
                                                    <span class="text-danger"><?php echo form_error('live_consult'); ?></span>
                                                </div>
                                            <?php } ?>
                                        </div>
                                        <div class="revisit_cheque_div" style="display:none;">
                                            <div class="row g-2 mt-1">
                                                <div class="col-sm-6">
                                                    <label class="form-label"><?php echo $this->lang->line('cheque_no'); ?></label><small class="req"> *</small>
                                                    <input type="text" name="cheque_no" id="cheque_no" class="form-control form-control-sm">
                                                    <span class="text-danger"><?php echo form_error('cheque_no'); ?></span>
                                                </div>
                                                <div class="col-sm-6">
                                                    <label class="form-label"><?php echo $this->lang->line('cheque_date'); ?></label><small class="req"> *</small>
                                                    <input type="text" name="cheque_date" id="cheque_date" class="form-control form-control-sm date">
                                                    <span class="text-danger"><?php echo form_error('cheque_date'); ?></span>
                                                </div>
                                                <div class="col-sm-12">
                                                    <label class="form-label"><?php echo $this->lang->line('attach_document'); ?></label>
                                                    <input type="file" class="filestyle form-control form-control-sm" name="document">
                                                    <span class="text-danger"><?php echo form_error('document'); ?></span>
                                                </div>
                                            </div>
                                        </div>
                                        <?php if ($result['gender'] == 'Female') { ?>
                                        <div class="row g-2 mt-1">
                                            <div class="col-sm-6" id="antenatal_div">
                                                <label class="form-label">&nbsp;</label>
                                                <div>
                                                    <input type="checkbox" name="is_for_antenatal" id="is_for_antenatal" value="1"> <?php echo $this->lang->line('is_antenatal') ?>
                                                </div>
                                            </div>
                                        </div>
                                        <?php } ?>
                                    </div>
                                </div>

                            </div><!-- /col-lg-4 -->

                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                    <button type="submit" id="formrevisitbtn" data-loading-text="<?php echo $this->lang->line('processing'); ?>" class="btn btn-info"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- -->
<div class="modal fade sh-modal sh-modal-accent" id="myPaymentModal" tabindex="-1" aria-labelledby="myPaymentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myPaymentModalLabel"><?php echo $this->lang->line('add_payment'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="add_payment" accept-charset="utf-8" method="post">
                <div class="pup-scroll-area">
                <div class="modal-body modal-background">
                    <div class="sh-form-card">
                        <div class="sh-card-header">
                            <span class="sh-card-header-title"><?php echo $this->lang->line('add_payment'); ?></span>
                        </div>
                        <div class="p-2">
                            <input type="hidden" name="opd_id" id="payment_opd_id" class="form-control" value="<?php echo $result['id']; ?>">
                                                <input type="hidden" name="case_reference_id" id="payment_opd_id" class="form-control" value="<?php echo $result['case_reference_id']; ?>">
                                                <input type="hidden" name="patient_id"value="<?php echo $id; ?>">
                        <div class="row">
                            <div class="col-lg-12 col-md-12 col-sm-12">
                                    <div class="row">
                                    <div class="col-md-6">
                                            <div class="mb-3">
                                                <label><?php echo $this->lang->line('date'); ?></label><small class="req"> *</small>                           
                                                <input type="text" name="payment_date" id="date" class="form-control datetime" autocomplete="off">
                                                <span class="text-danger"></span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label><?php echo $this->lang->line('amount') . " (" . $currency_symbol . ")"; ?></label><small class="req"> *</small>                                        
                                                <input type="text" name="amount" id="amount" class="form-control" value="<?php echo $total-$total_payment ; ?>">  
                                                 <input type="hidden" name="net_amount"  class="form-control" value="<?php echo $total-$total_payment ; ?>">  
                                                <span class="text-danger"><?php echo form_error('amount'); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label><?php echo $this->lang->line('payment_mode'); ?></label>
                                                <select class="form-control payment_mode" name="payment_mode">
                                                <?php foreach ($payment_mode as $key => $value) {
                                                    ?>
                                                    <option value="<?php echo $key ?>" <?php
                                                    if ($key == 'cash') {
                                                        echo "selected";
                                                    }
                                                    ?>><?php echo $value ?></option>
                                                <?php } ?>
                                                </select>
                                                <span class="text-danger"><?php echo form_error('apply_charge'); ?></span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label><?php echo $this->lang->line('note'); ?></label>
                                                <input type="text" name="note" id="note" class="form-control"/>
                                            </div>
                                        </div>
                                    </div>
									<div class="cheque_div" style="display: none;">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label><?php echo $this->lang->line('cheque_no'); ?></label><small class="req"> *</small>
                                                    <input type="text" name="cheque_no" id="cheque_no" class="form-control">
                                                    <span class="text-danger"><?php echo form_error('cheque_no'); ?></span>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label><?php echo $this->lang->line('cheque_date'); ?></label><small class="req"> *</small>
                                                    <input type="text" name="cheque_date" id="cheque_date" class="form-control date">
                                                    <span class="text-danger"><?php echo form_error('cheque_date'); ?></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="mb-3">
                                                    <label><?php echo $this->lang->line('attach_document'); ?></label>
                                                    <input type="file" class="filestyle form-control"   name="document">
                                                    <span class="text-danger"><?php echo form_error('document'); ?></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                            </div>
                        </div>
                    </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                    <button type="submit" id="add_paymentbtn" data-loading-text='<i class="fa fa-circle-o-notch fa-spin"></i>' class="btn btn-info"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                </div>
                </form>
        </div>
    </div>
</div>
<!-- -->
<div class="modal fade sh-modal sh-modal-accent" id="view_ot_modal" tabindex="-1" aria-labelledby="view_ot_modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="view_ot_modalLabel"><?php echo $this->lang->line('operation_details'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id='action_detail_modal' class="mb-2"></div>
                <div id="show_ot_data"></div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade sh-modal sh-modal-accent" id="editpayment_modal" tabindex="-1" aria-labelledby="editpayment_modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <form id="editpaymentform" accept-charset="utf-8" method="post">
             <div class="modal-header">
                    <h5 class="modal-title" id="editpayment_modalLabel"><?php echo $this->lang->line('payment_details'); ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="pup-scroll-area">
                <div class="modal-body modal-background">
                    <div class="sh-form-card">
                        <div class="sh-card-header">
                            <span class="sh-card-header-title"><?php echo $this->lang->line('payment_details'); ?></span>
                        </div>
                        <div class="p-2">
                   <div class="row">
                            <div class="col-lg-12 col-md-12 col-sm-12">
                                    <div class="row">
                                    <div class="col-md-6">
                                            <div class="mb-3">
                                                <label><?php echo $this->lang->line('date'); ?></label><small class="req"> *</small>
                                                <input type="text" name="payment_date" id="payment_date" class="form-control datetime" autocomplete="off">
                                                 <input type="hidden" class="form-control" id="edit_payment_id" name="edit_payment_id" >
                                                <span class="text-danger"></span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label><?php echo $this->lang->line('amount') . " (" . $currency_symbol . ")"; ?></label><small class="req"> *</small>                                         
                                                <input type="text" name="amount" id="edit_payment" class="form-control" value=""> 
                                                <span class="text-danger"><?php echo form_error('amount'); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label><?php echo $this->lang->line('payment_mode'); ?></label>
                                                <select class="form-control payment_mode" name="payment_mode" id="payment_mode">
                                                <?php foreach ($payment_mode as $key => $value) {
                                                    ?>
                                                    <option value="<?php echo $key ?>" <?php
                                                    if ($key == 'cash') {
                                                        echo "selected";
                                                    }
                                                    ?>><?php echo $value ?></option>
                                                <?php } ?>
                                                </select>
                                                <span class="text-danger"><?php echo form_error('apply_charge'); ?></span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label><?php echo $this->lang->line('note'); ?></label>
                                                <input type="text" name="note" id="edit_payment_note" class="form-control"/>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="cheque_div" style="display: none;">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label><?php echo $this->lang->line('cheque_no'); ?></label><small class="req"> *</small>
                                                    <input type="text" name="cheque_no" id="edit_cheque_no" class="form-control">
                                                    <span class="text-danger"><?php echo form_error('cheque_no'); ?></span>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label><?php echo $this->lang->line('cheque_date'); ?></label><small class="req"> *</small>
                                                    <input type="text" name="cheque_date" id="edit_cheque_date" class="form-control date">
                                                    <span class="text-danger"><?php echo form_error('cheque_date'); ?></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="mb-3">
                                                    <label><?php echo $this->lang->line('attach_document'); ?></label>
                                                    <input type="file" class="filestyle form-control"   name="document">
                                                    <span class="text-danger"><?php echo form_error('document'); ?></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                            </div>
                        </div>
                </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                    <button type="submit" id="editpaymentbtn" data-loading-text='<i class="fa fa-circle-o-notch fa-spin"></i>' class="btn btn-info"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
	
	$(document).on('input paste keyup','.editdiscount_percentage,#editqty', function(e){ 
		update_edit_charge_amount($(e.target).closest('div.modal'));
	});

    let update_edit_charge_amount=(object_model)=>{
        var is_tpa= $('input[name="edit_is_tpa"]:checked').val(); 
        let quantity           =  object_model.find('#editqty').val();
        let standard_charge    =  object_model.find('#editstandard_charge').val();
        let schedule_charge    =  object_model.find('#editscd_charge').val();      
        let tax_percentage     =  object_model.find('#editcharge_tax').val();    
        var total_charge= object_model.find('#edit_total_charge').val();   //added 
        let apply_charge      =  isNaN(parseFloat(total_charge)*parseFloat(quantity)) ? 0 : parseFloat(total_charge)*parseFloat(quantity); 
        $('#editapply_charge').val(apply_charge.toFixed(2));       
        let discount_percentage  =  object_model.find('#editdiscount_percentage').val();
        let discount_amount      =  (parseFloat(apply_charge) * discount_percentage/100);
        $('#editdiscount').val(discount_amount.toFixed(2));       
        let final_amount=apply_charge-discount_amount;
        $('#edittax').val(((final_amount*tax_percentage)/100).toFixed(2));
        $('#editfinal_amount').val((final_amount+((final_amount*tax_percentage)/100)).toFixed(2));
    }

    $(document).on('click','.editpayment',function(){
         var $this = $(this);
         var record_id = $this.data('recordId'); 
         var amount    =  $this.data('paymentAmount'); 
         $("#edit_payment").val(amount);
          $("#edit_payment_id").val(record_id);
          $.ajax({
                url: '<?php echo base_url(); ?>admin/patient/getopdpaymentdetails',
                type: 'post',
                data: {'payment_id':record_id},
                dataType: 'json',
                success: function (data) {
                    $("#payment_mode").val(data.payment_mode).prop('selected');
                      $(".payment_mode").trigger('change');
                      $("#edit_cheque_no").val(data.cheque_no);
                      $("#edit_cheque_date").val(data.cheque_date);
                      $("#payment_date").val(data.payment_date);
                      $("#edit_payment_note").val(data.note);
                }
           });
            
         shModal('editpayment_modal').show();
  });
  
	$(document).ready(function (e) {
        $("#editpaymentform").on('submit', (function (e) {
            e.preventDefault();
            $("#editpaymentbtn").btnLoading();
            var payment_id = $("#edit_payment_id").val();
            var payment = $("#edit_payment").val();
            $.ajax({
                url: '<?php echo base_url(); ?>admin/patient/editpayment',
                type: "POST",
                data: new FormData(this),
                dataType: 'json',
                contentType: false,
                cache: false,
                processData: false,
                 beforeSend: function(){
                  $("#editpaymentbtn").btnLoading();
                 },
                success: function (data) {
                    if (data.status == 0) {
                        var message = data.message;
                        $.each(data.error, function (index, value) {
                            message += value;
                        });
                        errorMsg(message);
                    } else {
                        successMsg(data.message);
                        window.location.reload(true);
                    }
                    $("#editpaymentbtn").btnReset();
                },
                error: function () {
                 $("#editpaymentbtn").btnReset();
                },
  
                complete: function(){
                $("#editpaymentbtn").btnReset();
                }
            });
        }));
    });
</script>

<!-- //========datatable start===== -->
<script type="text/javascript">
	
    $(document).on('click','.print_ot_bill',function(){
         var $this = $(this);
         var record_id=$this.data('recordId');
        $this.btnLoading();
        $.ajax({
          url: '<?php echo base_url(); ?>admin/operationtheatre/print_otdetails',
          type: "POST",
          data:{'id':record_id},
          dataType: 'json',
           beforeSend: function() {
                 $this.btnLoading();      
          },
          success: function(res) {
           popup(res.page);
          },
             error: function(xhr) { // if error occured
          alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");
                  $this.btnReset();              
         },
              complete: function() {
                   $this.btnReset();                 
             }
      });
    });

    $('.close_modal').click(function(){
        $('#add_medication')[0].reset();
        $("#mmedicine_category_id").select2().select2('val', '');
        $("#mmedicine_id").select2().select2('val', '');
        $("#dosage").select2().select2('val', '');
    })
</script>

<script type="text/javascript">
( function ( $ ) {
     var opdid = "<?php echo $this->uri->segment(5); ?>"; 
     var patient_id = "<?php echo $this->uri->segment(4); ?>";
    'use strict';
    $(document).ready(function () {
        modal_click_disabled('view_ot_modal', 'myPaymentModal', 'viewModal', 'add_chargeModal')
        initDatatable('ajaxlist','admin/bill/getvisitdatatable/'+ opdid);        
    }); 
} ( jQuery ) )

</script>

<!-- //========datatable end===== -->
<script type="text/javascript">
    
 var datetime_format = '<?php echo strtr($this->customlib->getHospitalDateFormat(true, true), ['d' => 'DD', 'm' => 'MM', 'Y' => 'YYYY', 'H' => 'hh', 'i' => 'mm']) ?>';

    $(document).on('click', '.add-btn', function () {
        var s = "";
        s += "<div class='row'>";
        s += "<input name='rows[]' type='hidden' value='" + rows + "'>";
        s += "<div class='col-md-6'>";
        s += "<div class='mb-3'>";
        s += "<label for='act'><?= $this->lang->line('act') ?></label>";
        s += "<select class='form-control act select2' id='act' name='act" + rows + "' data-row_id='" + rows + "'>";
        s += "<option value=''>--Select--</option>";
        s += $('#act-template').html();
        s += "</select>";
        s += "<small class='text text-danger help-inline'></small>";
        s += "</div>";
        s += "</div>";
        s += "<div class='col-md-5'>";
        s += "<label for='validationDefault02'><?= $this->lang->line('section') ?></label>";
        s += "<div id='dd' class='wrapper-dropdown-3'>";
        s += "<input class='form-control filterinput' type='text' autocomplete='off'>";
        s += "<ul class='dropdown scroll150 section_ul'>";
        s += "<li class='section-placeholder'><span>--Select--</span></li>";
        s += "</ul>";
        s += "</div>";
        s += "</div>";
        s += "<div class='col-md-1'>";
        s += "<div class='mb-3'>";
        s += "<label for='removebtn'>&nbsp;</label>";
        s += "<button type='button' class='form-control btn btn-sm btn-danger remove_row'><i class='fa fa-remove'></i></button>";
        s += "</div>";
        s += "</div>";
        s += "</div>";
        $(".multirow").append(s);
        $('.select2').select2();
        link = 2;
        rows++;
    });
</script>
<script type="text/html" id="act-template">    
   <?php foreach ($symptomsresulttype as $dkey => $dvalue) {
                                                            ?>
        <option value="<?php echo $dvalue["id"]; ?>"><?php echo $dvalue["symptoms_type"] ;?></option> 
        <?php
    }
    ?>
</script>
<script>
    $(document).on('change', '.act', function () {
        $this = $(this);
        var sys_val = $(this).val();
        var row_id = $this.data('row_id');
        var section_ul = $(this).closest('div.row').find('ul.section_ul');
        var sel_option = "";
        $.ajax({
            type: 'POST',
            url: base_url + 'admin/patient/getPartialsymptoms',
            data: {'sys_id': sys_val, 'row_id': row_id},
            dataType: 'JSON',
            beforeSend: function () {
                // setting a timeout
                $('ul.section_ul').find('li:not(:first-child)').remove();
                $("div.wrapper-dropdown-3").removeClass('active');
            },
            success: function (data) {           
                section_ul.append(data.record);
            },
            error: function (xhr) { // if error occured
                alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");
            },
            complete: function () {

            }
        });
    });
</script>

<script type="text/javascript">
    // .timepicker inputs auto-initialized via event delegation in footer.php (TD 6)

    $(document).on('select2:select','.medicine_category_medication',function(){
       var medicine_category=$(this).val();      
      $('.medicine_name_medication').html("<option value=''><?php echo $this->lang->line('loading'); ?></option>");
     getMedicineForMedication(medicine_category,"");
     getMedicineDosageForMedication(medicine_category);
    });

    function getMedicineForMedication(medicine_category,medicine_id) {
      var div_data = "<option value=''><?php echo $this->lang->line('select'); ?></option>";
      if(medicine_category != ""){
          $.ajax({
            url: base_url+'admin/pharmacy/get_medicine_name',
            type: "POST",
            data: {medicine_category_id: medicine_category},
            dataType: 'json',
            success: function (res) {
              
                $.each(res, function (i, obj)
                {
                    var sel = "";
                    div_data += "<option value='" + obj.id + "'>" + obj.medicine_name + "</option>";

                });
                $('.medicine_name_medication').html(div_data);
                $(".medicine_name_medication").select2("val", medicine_id);
                $("#mmedicine_edit_id").val(medicine_id).trigger("change");
                $("#add_dose_medicine_id").val(medicine_id).trigger("change");
            }
        });
      }
    }

    function getMedicineDosageForMedication(medicine_category) {
        var div_data = "<option value=''><?php echo $this->lang->line('select'); ?></option>";
        if(medicine_category != ""){
          $.ajax({
            url: base_url+'admin/pharmacy/get_medicine_dosage',
            type: "POST",
            data: {medicine_category_id: medicine_category},
            dataType: 'json',
            success: function (res) {              
                $.each(res, function (i, obj)
                {
                    var sel = "";
                    div_data += "<option value='" + obj.id + "'>" + obj.dosage + " " + obj.unit + "</option>";

                });
                $('.dosage_medication').html(div_data);
                $(".dosage_medication").select2("val", '');             
            }
        });
      }
    }

    function get_dosagename(id) {
        $.ajax({
            url: '<?php echo base_url(); ?>admin/pharmacy/get_dosagename',
            type: "POST",
            data: {dosage_id: id},
            dataType: 'json',
            success: function (res) {
                if (res) {
                    $('#medicine_dosage_medication').val(res.dosage_unit);
                } else {

                }
            }
        });
    }

    $(document).ready(function (e) {
        $("#add_medication").on('submit', (function (e) {
            e.preventDefault();
            $("#add_medicationbtn").btnLoading();
            $.ajax({
                url: '<?php echo base_url(); ?>admin/patient/addmedicationdoseopd',
                type: "POST",
                data: new FormData(this),
                dataType: 'json',
                contentType: false,
                cache: false,
                processData: false,
                 beforeSend: function(){
                $("#add_medicationbtn").btnLoading();
                 },
                success: function (data) {
                    if (data.status == "fail") {
                        var message = data.message;
                        $.each(data.error, function (index, value) {
                            message += value;
                        });
                        errorMsg(message);
                    } else {
                        successMsg(data.message);
                        window.location.reload(true);
                    }
                    $("#add_medicationbtn").btnReset();
                },
                error: function () {
                 $("#add_medicationbtn").btnReset();
                },
  
                complete: function(){
                $("#add_medicationbtn").btnReset();
                }
            });
        }));
    });

    $(document).on('click', '.remove_row', function () {
        $this = $(this);
        $this.closest('.row').remove();
    });

    $(document).mouseup(function (e)
    {
        var container = $(".wrapper-dropdown-3"); // YOUR CONTAINER SELECTOR
        if (!container.is(e.target) // if the target of the click isn't the container...
                && container.has(e.target).length === 0) // ... nor a descendant of the container
        {
            $("div.wrapper-dropdown-3").removeClass('active');
        }
    });

    $(document).on('click', '.filterinput', function () {
        if (!$(this).closest('.wrapper-dropdown-3').hasClass("active")) {
            $(".wrapper-dropdown-3").not($(this)).removeClass('active');
            $(this).closest("div.wrapper-dropdown-3").addClass('active');
        }
    });

    $(document).on('click', 'input[name="section[]"]', function () {
        $(this).closest('label').toggleClass('active_section');
    });
 
    $(document).on('keyup', '.filterinput', function () {
        var valThis = $(this).val().toLowerCase();
        var closer_section = $(this).closest('div').find('.section_ul > li');
        var noresult = 0;
        if (valThis == "") {
            closer_section.show();
            noresult = 1;
            $('.no-results-found').remove();
        } else {
            closer_section.each(function () {
                var text = $(this).text().toLowerCase();
                var match = text.indexOf(valThis);
                if (match >= 0) {
                    $(this).show();
                    noresult = 1;
                    $('.no-results-found').remove();
                } else {
                    $(this).hide();
                }
            });
        }
        ;
        if (noresult == 0) {
            closer_section.append('<li class="no-results-found">No results found.</li>');
        }
    });
</script>
<script type="text/javascript">
    function holdModal(modalId) {
        $("#report_document").dropify();
        (function(){var _el=document.getElementById(modalId); if(_el) bootstrap.Modal.getOrCreateInstance(_el, {backdrop:'static', keyboard:false}).show();})();;
    }

    function addmedicationModal() {        
        holdModal('myaddMedicationModal');
    }

    function medicationModal(medicine_category_id,pharmacy_id,date) {

        var div_data = "<option value=''><?php echo $this->lang->line('select'); ?></option>";
        if(medicine_category_id != ""){
          $.ajax({
            url: '<?php echo base_url(); ?>admin/patient/getMedicineDoseDetails',
            type: "POST",
            data: {medicine_category_id: medicine_category_id},
            dataType: 'json',
            success: function (res) {
                $.each(res, function (i, obj)
                {
                    var sel = "";
                    div_data += "<option value='" + obj.id + "'>" + obj.dosage +" "+ obj.unit + "</option>";

                });

                $("#mdosage").html(div_data);
                $("#add_dose_medicine_category").select2("val",medicine_category_id);
                $("#mdosage").select2("val", '');
                 getMedicineForMedication(medicine_category_id,pharmacy_id);              
                $("#add_dose_date").val(date);
                holdModal('myMedicationModal');
            },
        });
      }
    }

    function medicationDoseModal(medication_id) {        
        $.ajax({
            url: '<?php echo base_url(); ?>admin/patient/getMedicationDoseDetails',
            type: "POST",
            data: {medication_id: medication_id},
            dataType: 'json',
            success: function (data) {
                $("#date_edit_medication").val(data.date);               
                SHPicker.setDate('#dosagetime', timeConvert(data.time)); // sets time on TD 6 timepicker
                $('select[id="medicine_dose_id"] option[value="' + data.medicine_dosage_id + '"]').attr("selected", "selected");
                $("#medicine_dose_edit_id").select2().select2('val', data.medicine_dosage_id);
                $("#mmedicine_category_edit_id ").val(data.medicine_category_id).trigger('change');
                getMedicineForMedication(data.medicine_category_id,data.pharmacy_id);
                $("#medicine_dosage_remark").val(data.remark);
                $("#medication_id").val(data.id);
                $('#edit_delete_medication').html("<a href='#' class='delete_record_dosage' data-record-id='"+ medication_id + "' data-bs-toggle='tooltip' title='<?php echo $this->lang->line('delete'); ?>' data-bs-target='' data-bs-toggle='modal'  title='<?php echo $this->lang->line('delete'); ?>'><i class='fa fa-trash'></i></a>");
                holdModal('myMedicationDoseModal');
            },
        });
    }

    $(document).ready(function (e) {
    $(document).on('click','.delete_record_dosage',function(){
         if (confirm(<?php echo "'" . $this->lang->line('delete_confirm') . "'"; ?>)) {
     var id=$(this).data('recordId');
    $.ajax({
                url: base_url+'admin/patient/deletemedication',
                type: "POST",
                data: {'id':id},
                dataType: 'json',
                 beforeSend: function(){
              
                 },
                success: function (data) {
                  successMsg(data.message);
                  window.location.reload(true); 
                },
                error: function () {
                 alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");
                },
  
                complete: function(){

                }
            });
}
    });

        $("#add_medicationdose").on('submit', (function (e) {
            e.preventDefault();
            $("#add_medicationdosebtn").btnLoading();
            $.ajax({
                url: '<?php echo base_url(); ?>admin/patient/addmedicationdoseopd',
                type: "POST",
                data: new FormData(this),
                dataType: 'json',
                contentType: false,
                cache: false,
                processData: false,
                 beforeSend: function(){
                $("#add_medicationdosebtn").btnLoading();
                 },
                success: function (data) {
                    if (data.status == "fail") {
                        var message = data.message;
                        $.each(data.error, function (index, value) {
                            message += value;
                        });
                        errorMsg(message);
                    } else {
                        successMsg(data.message);
                        window.location.reload(true);
                    }
                    $("#add_medicationdosebtn").btnReset();
                },
                error: function () {
                 $("#add_medicationdosebtn").btnReset();
                },
  
                complete: function(){
                $("#add_medicationdosebtn").btnReset();
                }
            });
        }));
    });

     $(document).ready(function (e) {
        $("#update_medication").on('submit', (function (e) {
            e.preventDefault();
            $("#update_medicationbtn").btnLoading();
            $.ajax({
                url: '<?php echo base_url(); ?>admin/patient/updatemedication',
                type: "POST",
                data: new FormData(this),
                dataType: 'json',
                contentType: false,
                cache: false,
                processData: false,
                 beforeSend: function(){
                $("#update_medicationbtn").btnLoading();
                 },
                success: function (data) {
                    if (data.status == "fail") {
                        var message = "";
                        $.each(data.error, function (index, value) {
                            message += value;
                        });
                        errorMsg(message);
                    } else {
                        successMsg(data.message);
                        window.location.reload(true);
                    }
                    $("#update_medicationbtn").btnReset();
                },
                error: function () {
                 $("#update_medicationbtn").btnReset();
                },  
                complete: function(){
                $("#update_medicationbtn").btnReset();
                }
            });
        }));
    });

    $(function () {
        //Initialize Select2 Elements
        $(function () {
            var hash = window.location.hash;
            hash && $('ul.nav-tabs a[href="' + hash + '"]').tab('show');

            $('.nav-tabs a').click(function (e) {
                $(this).tab('show');
                var scrollmem = $('body').scrollTop();
                window.location.hash = this.hash;
                $('html,body').scrollTop(scrollmem);
                   var pid = $("#result_pid").val();
                   var opdid = $("#result_opdid").val();
                 if (this.hash == '#charges') {
                   
                 }else if(this.hash == '#payment') {

                 }else if(this.hash == '#diagnosis'){
                   
                 }
            });
        });
    });

    function getdatavalue(dataurl) {       
        var pid = $("#result_pid").val();
        var opdid = $("#result_opdid").val();
        var base_url = '<?php echo base_url(); ?>';
        var url = base_url+dataurl;
        $.ajax({
            url: url,
            type: 'POST',
            data: {pid: pid, opdid: opdid},
            success: function (result) {             
              $('#datadiganosis').html(result);
            }
        });
    }
 
    $(function () {
        ['compose-textareas', 'compose-textareanew'].forEach(function(id) {
            if (document.getElementById(id)) {
                if (CKEDITOR.instances[id]) CKEDITOR.instances[id].destroy(true);
                CKEDITOR.replace(id, { enterMode: CKEDITOR.ENTER_BR });
            }
        });
    });

    function edit_prescription(id) {
        $.ajax({
            url: base_url+'admin/prescription/editopdPrescription',
            dataType:'JSON',
            data:{'prescription_id':id} ,
            type:"POST",
             beforeSend: function() {
    $('.modal-title',"#add_prescription").html('');

              },
               success: function (res) {
     $('.modal-title',"#add_prescription").html('<?php echo $this->lang->line('edit_prescription'); ?>');

                shModal('prescriptionview').hide();
                $('.modal-body',"#add_prescription").html(res.page);
                var medicineTable= $('.modal-body',"#add_prescription").find('table#tableID');
        medicineTable.find('.select2').select2();
        $('.modal-body',"#add_prescription").find('.multiselect2').select2({   
            placeholder: 'Select',
            allowClear: false,
            minimumResultsForSearch: 2
        });
 
    medicineTable.find("tbody tr").each(function() {

    var medicine_category_obj = $(this).find("td select.medicine_category");
    var post_medicine_category_id = $(this).find("td input.post_medicine_category_id").val();
    var post_medicine_id = $(this).find("td input.post_medicine_id").val();
    var dosage_id = $(this).find("td input.post_dosage_id").val();
    var medicine_dosage=getDosages(post_medicine_category_id,dosage_id);

    $(this).find('.medicine_dosage').html(medicine_dosage);
    $(this).find('.medicine_dosage').select2().select2('val', dosage_id);
    
    getMedicine(medicine_category_obj,post_medicine_category_id,post_medicine_id);

     });
                shModal('add_prescription').show();
             },

              complete: function() {
                ['compose-textareas', 'compose-textareanew'].forEach(function(id) {
                    if (document.getElementById(id)) {
                        if (CKEDITOR.instances[id]) CKEDITOR.instances[id].destroy(true);
                        CKEDITOR.replace(id, { enterMode: CKEDITOR.ENTER_BR });
                    }
                });
             },
             error: function(xhr) { // if error occured
              alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");              
         }                                                                                    
        });
    }

    function editDiagnosis(id) {
        $.ajax({
            url: '<?php echo base_url(); ?>admin/patient/editDiagnosis',
            type: "POST",
            data: {id: id},
            dataType: 'json',
            success: function (data) {
                $("#eid").val(data.id);
                $("#epatient_id").val(data.patient_id);
                $("#ereporttype").val(data.report_type);
                $("#ereportdate").val(data.report_date);
                $("#edescription").val(data.description);
                $("#ereportcenter").val(data.report_center);
                holdModal('edit_diagnosis');

            },
        });
    }

$(document).on('click','.editot',function(){
    let id=$(this).data('recordId');
       $.ajax({
            url: '<?php echo base_url(); ?>admin/operationtheatre/getotDetails',
            type: "POST",
            data: {id: id},
            dataType: 'json',
            success: function (data) {
                console.log(data);
                $("#otid").val(data.id);                
                 $('#eoperation_category').select2().select2('val',data.category_id);                
                 getcategory(data.category_id,data.operation_id);
                // #edate auto-initialized via .datetime class + event delegation. Set value:
                SHPicker.setDate('#edate', new Date(data.date));
                $("#eass_consultant_1").val(data.ass_consultant_1);
                $("#eass_consultant_2").val(data.ass_consultant_2);
                $("#eanesthetist").val(data.anesthetist);
                $("#eanaethesia_type").val(data.anaethesia_type);
                $("#eot_technician").val(data.ot_technician);
                $("#eot_assistant").val(data.ot_assistant);
                $("#eot_remark").val(data.remark);
                $("#eot_result").val(data.result);
                $('#econsultant_doctorid').select2().select2('val',data.consultant_doctor);
                $('#custom_fields_ot').html(data.custom_fields_value);
                 $('#eoperation_name').select2().select2('val',data.operation_id);
                holdModal('edit_operationtheatre');
            },
        });
    });    
    
    $(document).ready(function (e) {
        $("#form_editoperationtheatre").on('submit', (function (e) {
            $("#form_editoperationtheatrebtn").btnLoading();
            var cons = $("#cons_doctor").val();
            $("#cons_name").val(cons);
            e.preventDefault();
             var did = $("#econsultant_doctorid").val();            
            $("#econsultant_doctorname").val(did);

            $.ajax({
                url: '<?php echo base_url(); ?>admin/operationtheatre/update',
                type: "POST",
                data: new FormData(this),
                dataType: 'json',
                contentType: false,
                cache: false,
                processData: false,
                success: function (data) {
                    if (data.status == "fail") {
                        var message = "";
                        $.each(data.error, function (index, value) {
                            message += value;
                        });
                        errorMsg(message);
                    } else {
                        successMsg(data.message);
                        window.location.reload(true);
                    }
                    $("#form_editoperationtheatrebtn").btnReset();
                },
                error: function () {

                }
            });
        }));
    });    

    function getchargecode(charge_category) {
        var div_data = "";
        $('#code').html("<option value='l'><?php echo $this->lang->line('loading') ?></option>");
        $("#code").select2("val", 'l');
        $.ajax({
            url: '<?php echo base_url(); ?>admin/charges/getchargeDetails',
            type: "POST",
            data: {charge_category: charge_category},
            dataType: 'json',
            success: function (res) {               
                $.each(res, function (i, obj)
                {
                    var sel = "";
                    div_data += "<option value='" + obj.id + "'>" + obj.code + " - " + obj.description + "</option>";
                });

                $('#code').html("<option value=''><?php echo $this->lang->line('select'); ?></option>");
                $('#code').append(div_data);
                $("#code").select2("val", '');
                $('#standard_charge').val('');
                $('#apply_charge').val('');
            }
        });
    }

    $(document).ready(function (e) {
        $("#form_editdiagnosis").on('submit', (function (e) {
           
            $("#form_editdiagnosisbtn").btnLoading();
            e.preventDefault();
            $.ajax({
                url: '<?php echo base_url(); ?>admin/patient/update_diagnosis',
                type: "POST",
                data: new FormData(this),
                dataType: 'json',
                contentType: false,
                cache: false,
                processData: false,
                success: function (data) {
                    if (data.status == "fail") {
                        var message = "";
                        $.each(data.error, function (index, value) {
                            message += value;
                        });                     
                        errorMsg(message);
                    } else {
                        successMsg(data.message);
                        window.location.reload(true);
                    }
                    $("#form_editdiagnosisbtn").btnReset();
                },
                error: function () {
                  
                }
            });
        }));
    });

    $(document).on('click','.get_opd_detail',function(){
       var visitid=$(this).data('recordId');
       var $this = $(this);
   
        $.ajax({
            url: base_url+'admin/patient/getopdrecheckupDetails',
            type: "POST",
            data: {visit_id: visitid},
            dataType: 'json',
               beforeSend: function() {
              $this.btnLoading();

               },
            success: function (data) {
                    var delete_action = "<a href='#' class='btn btn-sm btn-light' data-bs-toggle='tooltip' onclick='delete_record(" + visitid + ")' title='<?php echo $this->lang->line('delete'); ?>'><i class='fa fa-trash'></i></a>";

                var patient_id = "<?php echo $result["id"] ?>";
                $('#edit_delete').html("<?php if ($this->rbac->hasPrivilege('visit', 'can_edit')) { ?><a href='#' onclick='editRecord(" + visitid + ")' class='btn btn-sm btn-light' data-bs-toggle='tooltip' title='<?php echo $this->lang->line('edit'); ?>'><i class='fa fa-pencil'></i></a><?php } ?><?php if ($this->rbac->hasPrivilege('visit', 'can_delete')) { ?>"+delete_action+"<?php } ?>" );
                $('#viewModal .modal-body').html(data.page);
              shModal('viewModal').show();

            },

             error: function(xhr) { // if error occured
          alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");
             $this.btnReset();
               
          },
          complete: function() {
                $this.btnReset();         
          }
        });
});

$(document).on('click','#add_newcharge',function(){ 

});
   
    function editRecord(visitid) {      
        var $exampleDestroy = $('#edit_consdoctor').select2();
        $.ajax({
            url: '<?php echo base_url(); ?>admin/patient/getvisitdetailsdata',
            type: "GET",
            data: {visitid: visitid},
            dataType: 'json',
            success: function (data) {
                $exampleDestroy.val(data.cons_doctor).select2('destroy').select2()
                $('#customfield').html(data.custom_fields_value);
                $("#appointmentdate").val(data.appointment_date);
                $('#visitid').val(visitid);
                $('#visit_transaction_id').val(data.transaction_id);
                $("#edit_case").val(data.case_type);
                $("#symptoms_description").val(data.symptoms);
                $("#edit_casualty").val(data.casualty);
                $("#edit_oldpatient").val(data.old_patient);
                $("#edit_refference").val(data.refference);
                $("#edit_revisit_note").val(data.note);
                $('select[id="edit_organisation"] option[value="'+data.organisation_id+'"]').attr("selected","selected");                
                $("#edit_height").val(data.height);
                $("#edit_weight").val(data.weight);
                $("#edit_bp").val(data.bp);
                $("#edit_pulse").val(data.pulse);
                $("#edit_temperature").val(data.temperature);
                $("#edit_respiration").val(data.respiration);
                $("#edit_paymentmode").val(data.payment_mode);
                $("#edit_opdid").val(data.opdid);
                $("#eknown_allergies").val(data.visit_known_allergies);
                 $("#edit_visit_payment_date").val(data.payment_date);
                 $("#edit_visit_payment").val(data.amount);
                 $("#visit_payment_mode").val(data.payment_mode).prop('selected');
                 $(".visit_payment_mode").trigger('change');
                 $("#edit_visit_cheque_no").val(data.cheque_no);
                 $("#edit_visit_cheque_date").val(data.cheque_date);
                 $("#edit_visit_payment_note").val(data.payment_note);
      

                 $('#edit_is_for_antenatal').prop('checked', (data.is_antenatal == "1") ? true :false);
                shModal("viewModal").hide();
                holdModal('editModal');
            },
        });
    }
    
    function delete_record(id) {
        if (confirm(<?php echo "'" . $this->lang->line('delete_confirm') . "'"; ?>)) {
            $.ajax({
                url: '<?php echo base_url(); ?>admin/patient/deleteVisit/'+id,
                type: "POST",
                data: {id: id},
                dataType: 'json',
                success: function (data) {
                    successMsg(<?php echo "'" . $this->lang->line('delete_message') . "'"; ?>);
                    window.location.reload(true);
                }
            })
        }
    }

    function deleteot(id) {
        if (confirm(<?php echo "'" . $this->lang->line('delete_confirm') . "'"; ?>)) {
            $.ajax({
                url: '<?php echo base_url(); ?>admin/operationtheatre/delete/'+id,
                type: "POST",
                data: {id: id},
                dataType: 'json',
                success: function (data) {
                    successMsg(<?php echo "'" . $this->lang->line('delete_message') . "'"; ?>);
                    window.location.reload(true);
                }
            })
        }
    }

    function delete_patient(id, patient_id) 
    {
        if (confirm(<?php echo "'" . $this->lang->line('delete_confirm') . "'"; ?>)) {
            $.ajax({
                url: '<?php echo base_url(); ?>admin/patient/deleteOPDPatient',
                type: "POST",
                data: {'id': id},
                dataType: 'json',
                success: function (data) {
                    successMsg(<?php echo "'" . $this->lang->line('delete_message') . "'"; ?>);
                    window.location.href = '<?php echo base_url() ?>admin/patient/profile/'+patient_id;
                }
            })
        }
    }

    function getEditRecord(id) 
    {       
        $.ajax({
            url: '<?php echo base_url(); ?>admin/patient/getpatientDetails',
            type: "POST",
            data: {id: id},
            dataType: 'json',
            success: function (data) {
               $("#eupdateid").val(data.id);
                $('#customfieldpatient').html(data.custom_fields_value);
                $("#ename").val(data.patient_name);
                $("#eguardian_name").val(data.guardian_name);
                $("#emobileno").val(data.mobileno);
                $("#eemail").val(data.email);
                $("#eaddress").val(data.address);
                $("#eage_year").val(data.age);
                $("#eage_month").val(data.month);
                $("#eage_day").val(data.day);
                $("#ebirth_date").val(data.dob);
                $("#enote").val(data.note);
                $("#exampleInputFile").attr("data-default-file", '<?php echo base_url() ?>' + data.image);
                $(".dropify-render").find("img").attr("src", '<?php echo base_url() ?>' + data.image);
                $("#eknown_allergies").val(data.known_allergies);
                $('select[id="blood_groups"] option[value="' + data.blood_bank_product_id + '"]').attr("selected", "selected");
                $('select[id="egenders"] option[value="' + data.gender + '"]').attr("selected", "selected");
                $('select[id="marital_statuss"] option[value="' + data.marital_status + '"]').attr("selected", "selected");
                shModal("myModal").hide();
                holdModal('myModaledit');
            },
        });
    }

    function editTimeline(id) {      
        $.ajax({
            url: '<?php echo base_url(); ?>admin/patient/editTimeline',
            type: "POST",
            data: {id: id},
            dataType: 'json',
            success: function (data) {
                var date_format = '<?php echo $results = strtr($this->customlib->getHospitalDateFormat(), ['d' => 'dd', 'm' => 'MM', 'Y' => 'yyyy',]) ?>';
                var dt = new Date(data.timeline_date).toString(date_format);
                $("#etimelineid").val(data.id);
                $("#epatientid").val(data.patient_id);
                $("#etimelinetitle").val(data.title);
                $("#etimelinedate").val(dt);               
                $("#timelineedesc").val(data.description);
                if (data.status == '') {
                
                } else
                {
                    $("#evisible_check").attr('checked', true);
                }
                holdModal('myTimelineEditModal');
            },
        });
    }

    function getRecordDischarged(id, opdid) 
    {     
        $.ajax({
            url: '<?php echo base_url(); ?>admin/patient/getopdDetailsSummary',
            type: "POST",
            data: {patient_id: id, opd_id: opdid},
            dataType: 'json',
            success: function (data) {
               
                $('#disevlistname').html(data.patient_name);
                $('#disevguardian').html(data.guardian_name);
                $('#disevlistnumber').html(data.mobileno);
                $('#disevemail').html(data.email);
                if (data.age == "") {
                    $("#disevage").html("");
                } else {
                    if (data.age) {
                        var age = data.age + " " + "Years";
                    } else {
                        var age = '';
                    }
                    if (data.month) {
                        var month = data.month + " " + "Month";
                    } else {
                        var month = '';
                    }
                    if (data.dob) {
                        var dob = "(" + data.dob + ")";
                    } else {
                        var dob = '';
                    }

                    $("#disevage").html(age + "," + month + " " + dob);
                }
                $("#disevaddress").html(data.address);
                $("#disenote").html(data.note);
                $("#disevgenders").html(data.gender);
                $("#disevmarital_status").html(data.marital_status);
                $("#disedit_admission_date").html(data.appointment_date);
                $("#disedit_discharge_date").html(data.discharge_date);
                $("#disopdid").val(data.opdid);
                $("#disupdateid").val(data.summary_id);
                $("#disevpatients_id").val(data.pid);               
                $("#disinvestigations").val(data.summary_investigations);
                $("#disevnoteipd").val(data.summary_note);
                $("#disdiagnosis").val(data.disdiagnosis);
                $("#disoperation").val(data.disoperation);
                $("#distreatment_at_home").val(data.summary_treatment_home);
                 $('#summary_print').html("<?php if ($this->rbac->hasPrivilege('discharged_summary', 'can_view')) { ?><a href='#' data-bs-toggle='tooltip' onclick='printData(" + data.summary_id + ")'   title='<?php echo $this->lang->line('print'); ?>'><i class='fa fa-print'></i></a> <?php } ?>");               
                holdModal('myModaldischarged');
            },
        });
    }

    function printData(insert_id) {
        var base_url = '<?php echo base_url() ?>';
        $.ajax({
            url: base_url + 'admin/patient/getopdsummaryDetails/' + insert_id,
            type: 'POST',
            data: {id: insert_id, print: 'yes'},
            success: function (result) {
                popup(result);
            }
        });
    }
   
    $(document).ready(function (e) {
        $("#formeditpa").on('submit', (function (e) {
            $("#formeditpabtn").btnLoading();
            e.preventDefault();
            $.ajax({
                url: '<?php echo base_url(); ?>admin/patient/update',
                type: "POST",
                data: new FormData(this),
                dataType: 'json',
                contentType: false,
                cache: false,
                processData: false,
                success: function (data) {
                    if (data.status == "fail") {
                        var message = "";
                        $.each(data.error, function (index, value) {
                            message += value;
                        });
                        errorMsg(message);
                    } else {
                        successMsg(data.message);
                        window.location.reload(true);
                    }
                    $("#formeditpabtn").btnReset();
                },
                error: function () {
                    
                }
            });
        }));
    }); 

    function getRecord_id(visitid) {      
         $.ajax({
            url: base_url+'admin/prescription/addopdPrescription',
            dataType:'JSON',
            data:{'visit_detail_id':visitid},
            type:"POST",
             beforeSend: function() {
                  $('.modal-title',"#add_prescription").html('');
              },
               success: function (res) {
                
                $('.modal-title',"#add_prescription").html('<?php echo $this->lang->line('add_prescription'); ?>');
                $('.modal-body',"#add_prescription").html(res.page);
                $('.modal-body',"#add_prescription").find('table').find('.select2').select2();
                $('.modal-body',"#add_prescription").find('.multiselect2').select2({   
                        placeholder: 'Select',
                        allowClear: false,
                        minimumResultsForSearch: 2
                    });

                shModal('add_prescription').show();
             },

              complete: function() {
                ['compose-textareass', 'compose-textareaneww'].forEach(function(id) {
                    if (document.getElementById(id)) {
                        if (CKEDITOR.instances[id]) CKEDITOR.instances[id].destroy(true);
                        CKEDITOR.replace(id, { enterMode: CKEDITOR.ENTER_BR });
                    }
                });
             },
             error: function(xhr) { // if error occured
              alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");             
         }                                                                                    
        });
    }

    $(document).ready(function (e) {
        $("#formedit").on('submit', (function (e) {
            $("#formeditbtn").btnLoading();
            e.preventDefault();
            $.ajax({
                url: '<?php echo base_url(); ?>admin/patient/opd_detail_update',
                type: "POST",
                data: new FormData(this),
                dataType: 'json',
                contentType: false,
                cache: false,
                processData: false,
                success: function (data) {
                    if (data.status == "fail") {
                        var message = "";
                        $.each(data.error, function (index, value) {
                            message += value;
                        });
                        errorMsg(message);
                    } else {
                        successMsg(data.message);
                        window.location.reload(true);
                    }
                    $("#formeditbtn").btnReset();
                },
                error: function () {

                }
            });
        }));
    });

    $(document).ready(function (e) {
        $("#form_prescription").on('submit', (function (e) {
        $("#form_prescriptionbtn").btnLoading();
            e.preventDefault();
            $.ajax({
                url: '<?php echo base_url(); ?>admin/patient/add_opd_prescription',
                type: "POST",
                data: new FormData(this),
                dataType: 'json',
                contentType: false,
                cache: false,
                processData: false,
                success: function (data) {
                    if (data.status == "0") {
                        var message = "";
                        $.each(data.error, function (index, value) {
                            message += value;
                        });
                        errorMsg(message);
                    } else {
                        successMsg(data.message);
                       window.location.reload(true);
                    }
                    $("#form_prescriptionbtn").btnReset();
                },
                error: function () {
                       $("#form_prescriptionbtn").btnReset();
                },
                complete: function () {
                       $("#form_prescriptionbtn").btnReset();
                }
            });
        }));
    });    

    $(document).ready(function (e) {
        $("#form_operationtheatre").on('submit', (function (e) {
             var did = $("#consultant_doctorid").val();
            $("#consultant_doctorname").val(did);
            $("#form_operationtheatrebtn").btnLoading();
            e.preventDefault();
            $.ajax({
                url: '<?php echo base_url(); ?>admin/operationtheatre/add',
                type: "POST",
                data: new FormData(this),
                dataType: 'json',
                contentType: false,
                cache: false,
                processData: false,
                success: function (data) {
                    if (data.status == "fail") {
                        var message = "";
                        $.each(data.error, function (index, value) {
                            message += value;
                        });
                        errorMsg(message);
                    } else {
                        successMsg(data.message);
                        window.location.reload(true);
                    }
                    $("#form_operationtheatrebtn").btnReset();
                },
                error: function () {
                   
                }
            });
        }));
    });

    var prescription_rows=2;
        $(document).on('click','.add-record',function(){
        var table = document.getElementById("tableID");
        var table_len = (table.rows.length);
        var id = parseInt(table_len);
        var div = "<input type='hidden' name='rows[]' value='"+prescription_rows+"' autocomplete='off'><div id=row1><div class='col-sm-2 col-6'><div class='mb-3'><label><?php echo $this->lang->line('medicine_category'); ?></label> <small class='req'> *</small><select class='form-control select2 medicine_category'  name='medicine_cat_"+prescription_rows+"'  id='medicine_cat" + prescription_rows + "'><option value='<?php echo set_value('medicine_category_id'); ?>'><?php echo $this->lang->line('select'); ?></option><?php foreach ($medicineCategory as $dkey => $dvalue) { ?><option value='<?php echo $dvalue["id"]; ?>'><?php echo $dvalue["medicine_category"] ?></option><?php } ?></select></div></div><div class='col-sm-2 col-6'><div class=form-group><label><?php echo $this->lang->line('medicine'); ?></label> <select class='form-control select2 medicine_name'  name='medicine_"+prescription_rows+"' id='search-query" + prescription_rows + "'><option value='l'><?php echo $this->lang->line('select') ?></option></select></div></div><div class='col-sm-2 col-6'><div class=form-group><label><?php echo $this->lang->line('dose'); ?></label><select  class='form-control select2 medicine_dosage' name='dosage_"+prescription_rows+"' id='search-dosage" + prescription_rows + "'><option value='l'><?php echo $this->lang->line('select') ?></option></select></div></div><div class='col-sm-2 col-6'><div class=form-group><label><?php echo $this->lang->line('dose_interval'); ?></label><select  class='form-control select2 interval_dosage' name='interval_dosage_"+prescription_rows+"' id='search-interval-dosage" + prescription_rows + "'><option value='<?php echo set_value('interval_dosage_id'); ?>'><?php echo $this->lang->line('select') ?></option><?php foreach ($intervaldosage as $dkey => $dvalue) { ?><option value='<?php echo $dvalue["id"]; ?>'><?php echo $dvalue["name"] ?></option><?php } ?></select></div></div><div class='col-sm-2 col-6'><div class=form-group><label><?php echo $this->lang->line('dose_duration'); ?></label><select  class='form-control select2 duration_dosage' name='duration_dosage_"+prescription_rows+"' id='search-duration-dosage" + prescription_rows + "'><option value='<?php echo set_value('duration_dosage_id'); ?>'><?php echo $this->lang->line('select') ?></option><?php foreach ($durationdosage as $dkey => $dvalue) { ?><option value='<?php echo $dvalue["id"]; ?>'><?php echo $dvalue["name"] ?></option><?php } ?></select></div></div><div class='col-sm-2 col-6'><div class=form-group><label><?php echo $this->lang->line('instruction'); ?></label><textarea style='height:28px' name='instruction_"+prescription_rows+"' class=form-control id=description></textarea></div></div></div>";
      var table_row= "<tr id='row" + prescription_rows + "'><td>" + div + "</td><td><button type='button' onclick='delete_row("+prescription_rows+")' data-row-id='"+prescription_rows+"' class='btn btn-sm btn-outline-danger delete_row'><i class='fa fa-remove'></i></button></td></tr>";
        $(table).find('tbody').append(table_row);
      
 $('.modal-body',"#add_prescription").find('table#tableID').find('.select2').select2();
        prescription_rows++;
    });     

    function delete_row(id) {        
        var table = document.getElementById("tableID");
        var rowCount = table.rows.length;
        $("#row" + id).html("");       
    }

    $(document).ready(function (e) {
        $("#add_timeline").on('submit', (function (e) {
            $("#add_timelinebtn").btnLoading();
            var patient_id = $("#patient_id").val();
            e.preventDefault();
            $.ajax({
                url: "<?php echo site_url("admin/timeline/add_patient_timeline") ?>",
                type: "POST",
                data: new FormData(this),
                dataType: 'json',
                contentType: false,
                cache: false,
                processData: false,
                success: function (data) {
                    if (data.status == "fail") {
                        var message = "";
                        $.each(data.error, function (index, value) {
                            message += value;
                        });
                        errorMsg(message);
                    } else {
                        successMsg(data.message);
                        $.ajax({
                            url: '<?php echo base_url(); ?>admin/timeline/patient_timeline/' + patient_id,
                            success: function (res) {
                                $('#timeline_list').html(res);
                                shModal('myTimelineModal').toggle();
                            },
                            error: function () {
                                alert("Fail")
                            }
                        });
                    window.location.reload(true);
                    }
                    $("#add_timelinebtn").btnReset();
                },
                error: function (e) {
                    alert("Fail");
                }
            });
        }));
    });

    $(document).ready(function (e) {
        $("#edit_timeline").on('submit', (function (e) {
            $("#edit_timelinebtn").btnLoading();
            var patient_id = $("#patient_id").val();
            e.preventDefault();
            $.ajax({
                url: "<?php echo site_url("admin/timeline/edit_patient_timeline") ?>",
                type: "POST",
                data: new FormData(this),
                dataType: 'json',
                contentType: false,
                cache: false,
                processData: false,
                success: function (data) {
                    if (data.status == "fail") {
                        var message = "";
                        $.each(data.error, function (index, value) {
                            message += value;
                        });
                        errorMsg(message);
                    } else {
                        successMsg(data.message);
                        window.location.reload(true);
                    }
                    $("#edit_timelinebtn").btnReset();
                },
                error: function (e) {
                    alert("Fail");
                }
            });
        }));
    });

    function delete_timeline(id) {
        var patient_id = $("#patient_id").val();
        if (confirm('<?php echo $this->lang->line("delete_confirm") ?>')) {
            $.ajax({
                url: '<?php echo base_url(); ?>admin/timeline/delete_patient_timeline/' + id,
                success: function (res) {
                    $.ajax({
                        url: '<?php echo base_url(); ?>admin/timeline/patient_timeline/' + patient_id,
                        success: function (res) {

                            $('#timeline_list').html(res);
                            successMsg('<?php echo $this->lang->line('delete_message') ?>');
                        },
                        error: function () {
                            alert("Fail")
                        }
                    });
                },
                error: function () {
                    alert("Fail")
                }
            });
        }
    }

    function view_prescription(visitid) {
        $.ajax({
            url: '<?php echo base_url(); ?>admin/prescription/getPrescription/' + visitid,
            success: function (res) {
                $("#getdetails_prescription").html(res);
            },
            error: function () {
                alert("Fail")
            }
        }); 

        holdModal('prescriptionview');
    }

    function viewmanual_prescription(visitid) {
        $.ajax({
            url: '<?php echo base_url(); ?>admin/prescription/getPrescriptionmanual/' + visitid ,
            success: function (res) {
                $("#getdetails_prescriptionmanual").html(res);
                $('#edit_deleteprescriptionmanual').html("<?php if ($this->rbac->hasPrivilege('prescription', 'can_view')) { ?><a href='#' onclick='printprescriptionmanual(" + visitid + ")' class='btn btn-sm btn-light' data-bs-toggle='tooltip' title='<?php echo $this->lang->line('print'); ?>'><i class='fa fa-print'></i></a><?php } ?>");
            },
            error: function () {
                alert("Fail")
            }
        });
        holdModal('prescriptionviewmanual');
    }

</script>
<link rel="stylesheet" href="<?php echo base_url(); ?>backend/dist/css/animate.min.css">
<script type="text/javascript">
    $(document).ready(function () {
        $(".dshow").click(function () {
            $('.sidebarlists').fadeIn(1000);
            $('.sidebarlists').show();
            $('.dshow').hide();
            $('.sidebarlists').removeClass('animated slideInRight faster').addClass('animated slideInLeft faster');
            $('.dhide').show();
            $('.itemcol').removeClass('col-md-12').addClass('col-md-9');
        });

        $(".dhide").click(function () {
            $('.sidebarlists').fadeOut(1000);
            $('.sidebarlists').hide();
            $('.dshow').show();
            $('.dhide').hide();
            $('.sidebarlists').addClass('animated slideInLeft faster').removeClass('animated slideInRight faster');
            $('.itemcol').addClass('col-md-12').removeClass('col-md-9');
          
        });
    });
</script>
<script type="text/javascript">

    $(document).ready(function (e) {
        $('.select2').select2();
    });    

    $(document).ready(function (e) {
        $("#formrevisit").on('submit', (function (e) {
            $("#formrevisitbtn").btnLoading();
            e.preventDefault();
            $.ajax({
                url: '<?php echo base_url(); ?>admin/bill/addvisitDetails',
                type: "POST",
                data: new FormData(this),
                dataType: 'json',
                contentType: false,
                cache: false,
                processData: false,
                success: function (data) {

                    if (data.status == "fail") {
                        var message = "";
                        $.each(data.error, function (index, value) {
                            message += value;
                        });
                        errorMsg(message);
                    } else {
                        successMsg(data.message);
                        window.location.reload(true);                  
                    }
                    $("#formrevisitbtn").btnReset();
                },
                error: function () {
                    
                }
            });
        }));
    });

    function makeid(length) {
        var result = '';
        var characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        var charactersLength = characters.length;
        for (var i = 0; i < length; i++) {
            result += characters.charAt(Math.floor(Math.random() * charactersLength));
        }
        return result;
    }

    function renderOpdVisitAvatar(imageUrl, patientName) {
        var name = (patientName || '').replace(/\s*\([^)]*\)\s*/g, ' ').trim();
        var hasRealImage = imageUrl && imageUrl.indexOf('no_image') === -1;
        if (hasRealImage) {
            $('#patient_image').attr('src', '<?php echo base_url(); ?>' + imageUrl + '<?php echo img_time(); ?>').removeClass('d-none');
            $('#patient_image_initials').text('').addClass('d-none');
        } else {
            var parts = name.split(/\s+/).filter(Boolean);
            var initials = parts.length === 0 ? '?' : parts.length === 1 ? parts[0].charAt(0) : parts[0].charAt(0) + parts[parts.length - 1].charAt(0);
            $('#patient_image').addClass('d-none').removeAttr('src');
            $('#patient_image_initials').text(initials.toUpperCase()).removeClass('d-none');
        }
    }

    function getRevisitRecord(visitid) {

        $('.select2-selection__rendered').html("");
        $.ajax({
            url: '<?php echo base_url(); ?>admin/patient/getvisitDetails',
            type: "POST",
            data: {visitid: visitid},
            dataType: 'json',
            success: function (data) {               
                $("#patientname").html(data.patients_name);
                $('#guardian').html(data.guardian_name);
                $('#rgender').html(data.gender);
                $("#listnumber").html(data.mobileno);
                $("#remail").html(data.email);
                $("#rblood_group").html(data.blood_group_name);
                $("#raddress").html(data.address);
                $("#rmarital_status").html(data.marital_status);
                $("#rtpa_name").html(data.organisation_name);
                $("#rtpa_id").html(data.insurance_id);
                $("#rtpa_validity").html(data.tpa_validity);
                $("#ridentification_number").html(data.identification_number);
                $("#rallergies").html(data.any_known_allergies);
                $("#rnote").html(data.note);
                renderOpdVisitAvatar(data.image, data.patient_name);

                var date_format = '<?php echo $result = strtr($this->customlib->getHospitalDateFormat(), ['d' => 'dd', 'm' => 'MM', 'Y' => 'yyyy',]) ?>';
                var dob_format = new Date(data.dob).toString(date_format);
                
                $("#rage").html(data.patient_age);
                $("#revisit_id").val(data.id);
                $("#revisit_name").val(data.patient_name);
                $('#revisit_guardian').val(data.guardian_name);
                $("#revisit_contact").val(data.mobileno);
                 $("#revisit_date").val(data.appointment_date);
                $("#revisit_case").val(data.case_type);                
                $("#pid").val(data.patientid);
                $("#revisit_refference").val(data.refference);
                $("#revisit_email").val(data.email);
                if (data.live_consult) {
                    $("#live_consultvisit").val(data.live_consult);
                } 
                $("#esymptoms").val(data.symptoms);
                $("#revisit_age").val(data.age);
                $("#revisit_month").val(data.month);                
                $("#revisit_blood_group").val(data.blood_group);
                $("#revisi_tax").val(data.tax);
                $("#revisit_address").val(data.address);
                $("#revisit_note").val(data.note);              
                $('select[id="revisit_old_patient"] option[value="' + data.old_patient + '"]').attr("selected", "selected");
                $('select[id="revisit_doctor"] option[value="' + data.cons_doctor + '"]').attr("selected", "selected");
                $('select[id="revisit_organisation"] option[value="' + data.organisation_id + '"]').attr("selected", "selected");
                $('select[id="revisit_organisation"]').attr("disabled", true);               
                holdModal('revisitModal');
            },
        })
    }

    function printprescription(visitid) {
        var base_url = '<?php echo base_url() ?>';
        $.ajax({
            url: base_url + 'admin/prescription/printPrescription',
            type: 'GET',
            data: { visitid: visitid },
            dataType: "json",
            success: function (result) {
                popup(result.page);
            }
        });
    }

    function printprescriptionmanual(visitid) {
        var base_url = '<?php echo base_url() ?>';
        $.ajax({
            url: base_url + 'admin/prescription/getPrescriptionmanual/' + visitid,
            type: 'POST',
            data: {payslipid: visitid, print: 'yes'},
            success: function (result) {
                $("#testdata").html(result);
                popup(result);
            }
        });
    }     

    function deleteOpdPatientDiagnosis(patient_id, id) {
        if (confirm(<?php echo "'" . $this->lang->line('delete_confirm') . "'"; ?>)) {
            $.ajax({
                url: '<?php echo base_url(); ?>admin/patient/deleteOpdPatientDiagnosis/' + patient_id + '/' + id,
                success: function (res) {
                    successMsg(<?php echo "'" . $this->lang->line('delete_message') . "'"; ?>);
                    window.location.reload(true);
                }
            })
        }
    }

    function deleteOpdPatientCharge(id) {
        if (confirm(<?php echo "'" . $this->lang->line('delete_confirm') . "'"; ?>)) {
            $.ajax({
                url: '<?php echo base_url(); ?>admin/patient/deleteOpdPatientCharge/'+ id,
                success: function (res) {
                    successMsg(<?php echo "'" . $this->lang->line('delete_message') . "'"; ?>);
                    window.location.reload(true);
                }
            })
        }
    }

    function deletePayment(id) {
        if (confirm(<?php echo "'" . $this->lang->line('delete_confirm') . "'"; ?>)) {
            $.ajax({
                url: '<?php echo base_url(); ?>admin/patient/deletePayment/'+ id,
                success: function (res) {
                    successMsg(<?php echo "'" . $this->lang->line('delete_message') . "'"; ?>);
                    window.location.reload(true);
                }
            })
        }
    }

    var attr = {};

    $(document).ready(function (e) {
        $("#formdishrecord").on('submit', (function (e) {
            $("#formdishrecordbtn").btnLoading();
            e.preventDefault();
            $.ajax({
                url: '<?php echo base_url(); ?>admin/patient/add_opddischarged_summary',
                type: "POST",
                data: new FormData(this),
                dataType: 'json',
                contentType: false,
                cache: false,
                processData: false,
                success: function (data) {
                    if (data.status == "fail") {
                        var message = "";
                        $.each(data.error, function (index, value) {
                            message += value;
                        });
                        errorMsg(message);
                    } else {
                        successMsg(data.message);
                        window.location.reload(true);
                    }
                    $("#formdishrecordbtn").btnReset();
                },
                error: function () {                    

                }
            });
        }));
    });

    function getMedicineName(id) {
        console.log(id);
        var category_selected = $("#medicine_cat" + id).val();
        var arr = category_selected.split('-');
        var category_set = arr[0];
        div_data = '';
        $("#search-query" + id).html("<option value='l'><?php echo $this->lang->line('loading') ?></option>");
        $('#search-query' + id).select2("val", +id);
        $.ajax({
            type: "POST",
            url: base_url + "admin/pharmacy/get_medicine_name",
            data: {'medicine_category_id': category_selected},
            dataType: 'json',
            success: function (res) {
               
                $.each(res, function (i, obj)
                {
                    var sel = "";
                    div_data += "<option value='" + obj.medicine_name + "'>" + obj.medicine_name + "</option>";
                });
             
                $("#search-query" + id).html("<option value=''><?php echo $this->lang->line('select'); ?></option>");
                $('#search-query' + id).append(div_data);
                $('#search-query' + id).select2("val", '');
                getMedicineDosage(id);

            }
        });
    }
    ;

    function getMedicineDosage(id) {
        var category_selected = $("#medicine_cat" + id).val();
        var arr = category_selected.split('-');
        var category_set = arr[0];
        div_data = '';
        $("#search-dosage" + id).html("<option value='l'><?php echo $this->lang->line('loading') ?></option>");
        $.ajax({
            type: "POST",
            url: base_url + "admin/pharmacy/get_medicine_dosage",
            data: {'medicine_category_id': category_selected},
            dataType: 'json',
            success: function (res) {
                $.each(res, function (i, obj)
                {
                    var sel = "";
                    div_data += "<option value='" + obj.dosage + "'>" + obj.dosage + "</option>";
                });
                $("#search-dosage" + id).html("<option value=''><?php echo $this->lang->line('select'); ?></option>");
                $('#search-dosage' + id).append(div_data);
            }
        });
    }

    function getcharge_category(id) {
        var div_data = "";
        $('#charge_category').html("<option value='l'><?php echo $this->lang->line('loading') ?></option>");
        $("#charge_category").select2("val", 'l');

        $.ajax({
            url: '<?php echo base_url(); ?>admin/charges/get_charge_category',
            type: "POST",
            data: {charge_type: id},
            dataType: 'json',
            success: function (res) {
                $.each(res, function (i, obj)
                {
                    var sel = "";
                    div_data += "<option value='" + obj.name + "'>" + obj.name + "</option>";
                });
                $('#charge_category').html("<option value=''><?php echo $this->lang->line('select'); ?></option>");
                $('#charge_category').append(div_data);
                $("#charge_category").select2("val", '');
            }
        });
    }  

    $(document).on('input paste keyup','.apply_charge,.discount_percentage', function(e){ 
        update_amount($(e.target).closest('div.modal'));
    });

    function update_amount(object_model){  
        let  apply_charge= object_model.find('#apply_chargevisit').val();
        let  discount_percentage=object_model.find('#discount_percentage').val();
        let  discount= (parseFloat(apply_charge) * discount_percentage/100);
        let  price_with_discount=((parseFloat(apply_charge))-(parseFloat(apply_charge) * discount_percentage/100));
        let  tax_percentage=object_model.find('#percentage').val();        
        if(tax_percentage !='' && tax_percentage !=0){
             apply_amount=(parseFloat(price_with_discount) * tax_percentage/100)+price_with_discount;      
             object_model.find('#revisit_amount').val(apply_amount.toFixed(2));            
             object_model.find('#paid_amount').val(apply_amount.toFixed(2));            
        }
    }


    $(document).on('change','.charge_type',function(){
        var charge_type=$(this).val();     
        $('.charge_category').html("<option value=''><?php echo $this->lang->line('loading') ?></option>");
     getcharge_category(charge_type,"");

    });

    function getcharge_category(charge_type,charge_category) {
           var div_data = "";
           if(charge_type != ""){

        $.ajax({
            url: base_url+'admin/charges/get_charge_category',
            type: "POST",
            data: {charge_type: charge_type},
            dataType: 'json',
            success: function (res) {
                $.each(res, function (i, obj)
                {
                    var sel = "";
                    div_data += "<option value='" + obj.id + "'>" + obj.name + "</option>";
                });
                $('.charge_category').html("<option value=''><?php echo $this->lang->line('select'); ?></option>");
                $('.charge_category').append(div_data);
                $('.charge_category').select2("val", charge_category);
            }
        });
         }
    }

 $(document).on('select2:select','.charge_category',function(){
        var charge_category=$(this).val();      
        $('.charge').html("<option value=''><?php echo $this->lang->line('loading') ?></option>");   
        $('.addcharge').html("<option value=''><?php echo $this->lang->line('loading') ?></option>");

        //=============
        $('#apply_charge').val('0');  
        $('#discount_percentage_add_charge').val('0');  
        $('#discount_percentage_amount').val(0);
        $('#charge_tax').val('0');  
        $('#tax').val('0');  
        $('#final_amount').val('0');  
        $('#addstandard_charge').val('0');  
        $('#addscd_charge').val('0');  
        $('#qty').val('1');  
        $('#charge_id').val('').trigger('change');   
        //===============
        
        getchargecode(charge_category,"");
 });

    function getchargecode(charge_category,charge_id) {      
      var div_data = "<option value=''><?php echo $this->lang->line('select'); ?></option>";
      if(charge_category != ""){
          $.ajax({
            url: base_url+'admin/charges/getchargeDetails',
            type: "POST",
            data: {charge_category: charge_category},
            dataType: 'json',
            success: function (res) {
                $.each(res, function (i, obj)
                {
                    var sel = "";
                    div_data += "<option value='" + obj.id + "'>" + obj.name + "</option>";
                });
                $('.charge').html(div_data);
                $(".charge").select2("val", charge_id);
                $('.addcharge').html(div_data);
                $(".addcharge").select2("val", charge_id);             
            }
        });
      }
    }

    $(document).ready(function (e) {
        $("#add_bill").on('submit', (function (e) {
            if (confirm('<?php echo $this->lang->line('are_you_sure')?>')) {
                $("#save_button").btnLoading();
                e.preventDefault();
                $.ajax({
                    url: "<?php echo site_url("admin/payment/addopdbill") ?>",
                    type: "POST",
                    data: new FormData(this),
                    dataType: 'json',
                    contentType: false,
                    cache: false,
                    processData: false,
                    success: function (data) {
                        if (data.status == "fail") {
                            var message = "";
                            $.each(data.error, function (index, value) {
                                message += value;
                            });
                            errorMsg(message);
                        } else {
                            successMsg(data.message);
                            window.location.reload = true;
                        }
                        $("#save_button").btnReset();
                         location.reload();
                    },
                    error: function (e) {
                        alert("Fail");
                    }
                });
            } else {
                return false;
            }
        }));
    });
 
    $(document).ready(function (e) {
        $("#add_charges button[type=submit]").click(function() {
        $("button[type=submit]", $(this).parents("form")).removeAttr("clicked");
        $(this).attr("clicked", "true");
    });

        $("#add_charges").on('submit', (function (e) {
            e.preventDefault();
            var $this = $("button[type=submit][clicked=true]");
            var form = $(this);
            var form_data = form.serializeArray();
            var button_val=$this.attr('value');
            form_data.push({name: "add_type", value: button_val});
            $.ajax({ 
                url: '<?php echo base_url(); ?>admin/charges/add_opdcharges',
                type: "post", 
                data: form_data,
                dataType: 'json',
                beforeSend: function () {
                    if(button_val=='save'){
                        $this.btnLoading();
                    }else{
                        $this.btnLoading();
                    }
            },
                success: function (res) {
                    if (res.status == "fail") {
                        var message = "";
                        $.each(res.error, function (index, value) {
                            message += value;
                        });
                        errorMsg(message);
                    } else if(res.status == "new_charge") {
                        var data=res.data;
                        var row_id=makeid(8);  note
                
                        var charge='<tr id="'+row_id+'"><td>'+data.date+'<input type="hidden" name="pre_date[]" value="'+data.date+'"></td><td>'+data.charge_type_name+'</td><td>'+data.charge_category+'</td><td>'+data.charge_name+'<input type="hidden" name="pre_charge_id[]" value="'+data.charge_id+'"></td><td>'+data.standard_charge+'<input type="hidden" name="pre_standard_charge[]" value="'+data.standard_charge+'"><input type="hidden" name="pre_tax_percentage[]" value="'+data.tax_percentage+'"></td><td>'+data.tpa_charge+'<input type="hidden" name="pre_tpa_charges[]" value="'+data.tpa_charge+'"></td><td>'+data.qty+'<input type="hidden" name="pre_qty[]" value="'+data.qty+'"></td><td>'+data.amount+'<input type="hidden" name="pre_total[]" value="'+data.amount+'"></td><td>'+data.discount_percentage_amount+' ('+data.discount_percentage+'%)<input type="hidden" name="pre_discount_percentage[]" value="'+data.discount_percentage+'"></td><td>'+data.tax+'('+data.tax_percentage+'%)<input type="hidden" name="pre_tax[]" value="'+data.tax+'"><input type="hidden" name="pre_apply_charge[]" value="'+data.apply_charge+'"><input type="hidden" name="pre_note[]" value="'+data.note+'"></td><td>'+data.net_amount+'<input type="hidden" name="pre_net_amount[]" value="'+data.net_amount+'"></td><td><button type="button" class="btn btn-sm btn-outline-danger delete_row" data-row-id="'+row_id+'" autocomplete="off"><i class="fa fa-remove"></i></button></td></tr>';
						
                        $('#preview_charges').append(charge);                        
                       charge_reset();
                    }else{
                         successMsg(res.message);
                        window.location.reload(true);
                    }                   
                },
                error: function () {
                    if(button_val=='save'){
                        $this.btnReset();
                    }else{
                        $this.btnReset();
                    }             
                },
                complete: function () {
                    if(button_val=='save'){
                        $this.btnReset();
                      
                    }else{
                        $this.btnReset();
                    }
                }
            });
        }));
    });
	
    $(document).on('click','.delete_row',function(e){       
        var del_row_id=$(this).data('rowId');
      var result = confirm("<?php echo $this->lang->line('delete_confirm')?>");
if (result) {
    $('#'+del_row_id).remove();
}

  });

function makeid(length) {
    var result           = '';
    var characters       = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    var charactersLength = characters.length;
    for ( var i = 0; i < length; i++ ) {
      result += characters.charAt(Math.floor(Math.random() * 
 charactersLength));
   }
   return result;
}

    function charge_reset(){     
        $("#charge_category").select2("val", '');
        $("#add_charge_type").select2("val", '');
        $("#charge_id").select2("val", '');
        $('#addstandard_charge').val('');                  
        $('#addscd_charge').val('');                  
        $('#qty').val('');                  
        $('#apply_charge, #discount_percentage_add_charge, #discount_percentage_amount, #charge_tax, #tax, #final_amount').val(0);
        // $('#is_tpa').prop('checked',false);
        $('#discount_percentage_add_charge').val(0);     
    }
    
    $(document).ready(function (e) {
        $("#edit_charges").on('submit', (function (e) {
            e.preventDefault();
         
            $.ajax({
                url: base_url+'admin/charges/edit_opdcharges',
                type: "POST",
                data: new FormData(this),
                dataType: 'json',
                contentType: false,
                cache: false,
                processData: false,

                 beforeSend: function(){
                  $("#add_chargesbtn").btnLoading();
                 },
                success: function (data) {
                    if (data.status == "fail") {
                        var message = "";
                        $.each(data.error, function (index, value) {
                            message += value;
                        });
                        errorMsg(message);
                    } else {
                        successMsg(data.message);
                        window.location.reload(true);
                    }
                    $("#add_chargesbtn").btnReset();
                },
                 error: function () {
                 $("#add_chargesbtn").btnReset();
                },
  
                complete: function(){
                 $("#add_chargesbtn").btnReset();
                }
            });
        }));
    });
    
    $(document).ready(function (e) {
        $("#add_payment").on('submit', (function (e) {
            e.preventDefault();
         
            $.ajax({
                url: base_url+'admin/payment/addOPDPayment',
                type: "POST",
                data: new FormData(this),
                dataType: 'json',
                contentType: false,
                cache: false,
                processData: false,
                 beforeSend: function(){
                  $("#add_paymentbtn").btnLoading();
                 },
                success: function (data) {
                    if (data.status == "fail") {
                        var message = "";
                        $.each(data.error, function (index, value) {
                            message += value;
                        });
                        errorMsg(message);
                    } else {
                        successMsg(data.message);
                        window.location.reload(true);
                    }
                    $("#add_paymentbtn").btnReset();
                },
                 error: function () {
                 $("#add_paymentbtn").btnReset();
                },
  
                complete: function(){
                 $("#add_paymentbtn").btnReset();
                }
            });
        }));
    });

    function calculate() {
        var discount_percent = $("#discount_percent").val();
        var tax_percent = $("#tax_percent").val();
        var other_charge = $("#other_charge").val();
        var paid_amount = $("#paid_amountpa").val();
        var total_amount = $("#total_amount").val();
        var subtotal_amount = parseFloat(total_amount) + parseFloat(other_charge);       

        if (discount_percent != '') {
            var discount = (subtotal_amount * discount_percent) / 100;
            $("#discount").val(discount.toFixed(2));
        } else {
            var discount = $("#discount").val();
        }

        if (tax_percent != '') {
            var tax = ((subtotal_amount - discount) * tax_percent) / 100;
            $("#tax").val(tax.toFixed(2));
        } else {
            var tax = $("#tax").val();
        }

        var gross_total = parseFloat(total_amount) + parseFloat(other_charge) + parseFloat(tax) - parseFloat(discount);
        var net_amount = parseFloat(total_amount) + parseFloat(other_charge) + parseFloat(tax) - parseFloat(discount);
        var net_amount_payble = parseFloat(net_amount) - parseFloat(paid_amount);
        $("#gross_total").val(gross_total.toFixed(2));
        $("#net_amount").val(net_amount.toFixed(2));
        $("#grass_amount").val(net_amount.toFixed(2));
        $("#grass_amount_span").html(net_amount.toFixed(2));
        $("#net_amount_span").html(net_amount_payble.toFixed(2));
        $("#net_amount_payble").val(net_amount_payble.toFixed(2));
        $("#save_button").show();
        $("#printBill").show();
    }

    function printBill(patientid, opdid) {
        var total_amount = $("#total_amount").val();
        var discount = $("#discount").val();
        var other_charge = $("#other_charge").val();
        var gross_total = $("#gross_total").val();
        var tax = $("#tax").val();
        var net_amount = $("#net_amount").val();
        var status = $("#status").val();
        var base_url = '<?php echo base_url() ?>';
        $.ajax({
            url: base_url + 'admin/payment/getOPDBill/',
            type: 'POST',
            data: {patient_id: patientid, opdid: opdid, total_amount: total_amount, discount: discount, other_charge: other_charge, gross_total: gross_total, tax: tax, net_amount: net_amount, status: status},
            success: function (result) {
                $("#testdata").html(result);
                popup(result);
            }
        });
    }

</script>
<script type="text/javascript">

    $(document).on('change','.chgstatus_dropdown',function(){
        $(this).parent('form.chgstatus_form').submit()
    });

    $("form.chgstatus_form").submit(function(e) {

    e.preventDefault(); // avoid to execute the actual submit of the form.

    var form = $(this);
    var url = form.attr('action');

    $.ajax({
           type: "POST",
           url: url,
           data: form.serialize(), // serializes the form's elements.
           dataType:"JSON",
           success: function(data)
           {
               if (data.status == 0) {
                    var message = "";
                    $.each(data.error, function (index, value) {

                        message += value;
                    });
                    errorMsg(message);
                } else {
                    successMsg(data.message);
                    window.location.reload(true);
                }               
           }
         });
}); 

$(".addcharges").click(function(){	
	$('#add_charges').trigger("reset");		
	$('#select2-charge_category-container').html("");		
	$('#select2-code-container').html("");		
});

$(".revisitrecheckup").click(function(){	
	$('#formrevisit').trigger("reset");			
});

$("#myPaymentModal").on('hidden.bs.modal', function (e) {
     $(".filestyle").next(".dropify-clear").trigger("click");    
     $('.cheque_div').css("display", "none");
     $('form#add_payment').find('input:text, input:password, input:file, textarea').val('');
     $('form#add_payment').find('select option:selected').removeAttr('selected');
     $('form#add_payment').find('input:checkbox, input:radio').removeAttr('checked');
});

$(document).on('click','.addpayment',function(){     
       shModal('myPaymentModal').show();
});

$(".adddiagnosis").click(function(){	
	$('#form_diagnosis').trigger("reset");	
	$(".dropify-clear").trigger("click");
});

$(".addtimeline").click(function(){	
	$('#add_timeline').trigger("reset");	
	$(".dropify-clear").trigger("click");
});

$(".prescription").click(function(){    
    $('#form_prescription').trigger("reset");
    $('#select2-medicine_cat0-container').html('');
    $('#select2-search-query0-container').html('');
    $('#select2-search-dosage0-container').html('');
    var table = document.getElementById("tableID");
    var table_len = (table.rows.length);    
    for (i = 1; i < table_len; i++) {           
        delete_row(i);
    }
});     

</script>
<script type="text/javascript">
        $(document).ready(function(){
$("#radiologyOpt").select2({
   
    placeholder: 'Select',
    allowClear: false,
    minimumResultsForSearch: 2
});

$("#pathologyOpt").select2({
   
    placeholder: 'Select',
    allowClear: false,
    minimumResultsForSearch: 2
});
});
     
</script>
<script type="text/javascript">
    $(document).on('change','.payment_mode',function(){
      var mode=$(this).val();
      if(mode == "Cheque"){
         $('.filestyle','#myPaymentModal').dropify();
       $(".date").trigger("change");
        $('.cheque_div').css("display", "block");       
      }else{
        $('.cheque_div').css("display", "none");
      }
    });
       
    $(document).on('select2:select','.medicine_category',function(){      
      getMedicine($(this),$(this).val(),0);
       selected_medicine_category_id =$(this).val();   
       var medicine_dosage=getDosages(selected_medicine_category_id);
       $(this).closest('tr').find('.medicine_dosage').html(medicine_dosage);
    });

    function getMedicine(med_cat_obj,val,medicine_id){
      var medicine_colomn=med_cat_obj.closest('tr').find('.medicine_name');
        medicine_colomn.html("");    
        $.ajax({
            url: '<?php echo base_url(); ?>admin/pharmacy/get_medicine_name',
            type: "POST",
            data: {medicine_category_id: val},
            dataType: 'json',
              beforeSend: function() {
              medicine_colomn.html("<option value=''><?php echo $this->lang->line('loading') ?></option>");

            },
            success: function (res) {
                var div_data="<option value=''><?php echo $this->lang->line('select'); ?></option>";
                $.each(res, function (i, obj)
                {
                    var sel = "";
                            if (medicine_id == obj.id) {
                                sel = "selected";
                            }
                            div_data += "<option value=" + obj.id + " " + sel + ">" + obj.medicine_name + "</option>";

                });
           
                medicine_colomn.html(div_data);
                medicine_colomn.select2("val", medicine_id);
               
            }
        });
}
</script>

<script type="text/javascript">
   function getDosages(medicine_category_id){
    var dosage_opt="<option value=''><?php echo $this->lang->line('select') ?></option>";  
   var sss='<?php echo json_encode($category_dosage); ?>';
   var aaa=JSON.parse(sss);
  
   if (aaa[medicine_category_id]){
    $.each(aaa[medicine_category_id], function(key, item) 
    {
      dosage_opt+="<option value='"+item.id+"'>"+item.dosage+"</option>";
    });

}
return dosage_opt;
   }
</script>

<script type="text/javascript">
           $(document).on('click','.print_visit',function(){
      var $this = $(this);
         var record_id=$this.data('recordId')
       $this.btnLoading();
      $.ajax({
          url: '<?php echo base_url(); ?>admin/patient/printVisit',
          type: "POST",
          data:{'visit_detail_id':record_id},
          dataType: 'json',
           beforeSend: function() {
                 $this.btnLoading();      
          },
          success: function(res) {
           popup(res.page);
          },
             error: function(xhr) { // if error occured
          alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");
                  $this.btnReset();              
         },
         complete: function() {
               $this.btnReset();                 
         }
      });
  });

         $(document).on('click','.print_trans',function(){
      var $this = $(this);
         var record_id=$this.data('recordId')
       $this.btnLoading();
      $.ajax({
          url: '<?php echo base_url(); ?>admin/transaction/printTransaction',
          type: "POST",
          data:{'id':record_id},
          dataType: 'json',
           beforeSend: function() {
                 $this.btnLoading();      
          },
          success: function(res) {
           popup(res.page);
          },
             error: function(xhr) { // if error occured
          alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");
                  $this.btnReset();              
         },
              complete: function() {
                   $this.btnReset();                 
             }
      });
  });

    $(document).on('click','.print_charge',function(){    

        var $this = $(this);
        var record_id=$this.data('recordId')
        $this.btnLoading();
        $.ajax({
            url: '<?php echo base_url(); ?>admin/patient/printCharge',
            type: "POST",
            data:{'id':record_id,'type':'opd'},
            dataType: 'json',
            beforeSend: function() {
                 $this.btnLoading();      
            },
            success: function(res) {
                popup(res.page);
            },
            error: function(xhr) { // if error occured
                alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");
                $this.btnReset();              
            },
            complete: function() {
                $this.btnReset();
                 
            }
      });
  });

    $(document).on('change keyup input paste','#qty',function(){
        let is_tpa= $("input:checkbox[name=is_tpa]").prop('checked') ? 1 : 0;
        var quantity=$(this).val();
        var tax_percent=$('#charge_tax').val();
        var discount_percent= $('#discount_percentage_add_charge').val();
        var total_charge= $('#total_charge').val();//added
        var apply_charge=isNaN(parseFloat(total_charge)*parseFloat(quantity)) ? 0 : parseFloat(total_charge)*parseFloat(quantity); 
        $('#apply_charge').val(apply_charge.toFixed(2));       
        var discount_amount= isNaN((apply_charge*discount_percent)/100) ? 0 : (apply_charge*discount_percent)/100;
        var final_amount=apply_charge-discount_amount;
        $('#discount_percentage_amount').val(discount_amount);
        $('#tax').val(((final_amount*tax_percent)/100).toFixed(2));
        $('#final_amount').val((final_amount+((final_amount*tax_percent)/100)).toFixed(2));
    });
</script>
<script type="text/javascript">
  	$(document).on('click','.edit_charge',function(){	 
		 
        var edit_charge_id=$(this).data('recordId');
		var createModal=$('#edit_chargeModal');
		var $this = $(this);
		$this.btnLoading();
		$.ajax({
			url: base_url+'admin/patient/getCharge',
			type: "POST",
			data:{'id':edit_charge_id},
			dataType: 'json',
			beforeSend: function() {
				$this.btnLoading();
			},
			success: function(res){   
				$('#editstandard_charge').val(res.result.standard_charge);
				if(res.result.tpa_charge>0){
					$('#editscd_charge').val(res.result.tpa_charge);
				}
				$('#editqty').val(res.result.qty);
				$('#editcharge_tax').val(res.result.percentage);				
				if(res.result.discount_percentage != ''){
					$('#editdiscount_percentage').val(res.result.discount_percentage);
				}else{
					$('#editdiscount_percentage').val('0.00');
				} 
				$('#editapply_charge').val(res.result.apply_charge);
				$('#editfinal_amount').val(res.result.amount);			 	 
				$('#editcharge_date').val(res.result.date);			 
				$('#editorg_id').val(res.result.org_charge_id);
				$('#editpatient_charge_id').val(res.result.id);
                var discount_amount=res.result.discount_amount;        
                $('#editdiscount').val(discount_amount);
				var tax_charge=((res.result.apply_charge-discount_amount)*res.result.percentage)/100;
				$('#edittax').val(tax_charge.toFixed(2));
				$('#edit_note').val(res.result.note);       
				$('#editcharge_type').select2('val',res.result.charge_type_master_id);
				bootstrap.Modal.getOrCreateInstance(document.getElementById('edit_chargeModal'), {backdrop:'static', keyboard:false}).show();  
                
                if(res.result.organisation_id==null || res.result.organisation_id=='' || res.result.organisation_id==0){
                    $("#edit_is_tpa").prop("checked",false);
                    $('#edit_total_charge').val(res.result.standard_charge);//added
                }else{
                    $("#edit_is_tpa").prop("checked",true);
                    $('#edit_total_charge').val(res.result.tpa_charge);//added
                }             
				geteditcharge_category(res.result.charge_type_master_id,res.result.charge_category_id);
				geteditchargecode(res.result.charge_category_id,res.result.charge_id);
			},
            error: function(xhr) { // if error occured
				alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");
				$this.btnReset();               
			},
			complete: function() {
				$this.btnReset();     
                update_edit_charge_amount($("#edit_chargeModal").closest('div.modal'));//added
			}
		});
	});
    
    $(document).on('change','.editcharge_type',function(){
        var charge_type=$(this).val();     
        $('.editcharge_category').html("<option value=''><?php echo $this->lang->line('loading') ?></option>");
     geteditcharge_category(charge_type,"");
    });

    function geteditcharge_category(charge_type,charge_category) {
           var div_data = "";
           if(charge_type != ""){

        $.ajax({
            url: base_url+'admin/charges/get_charge_category',
            type: "POST",
            data: {charge_type: charge_type},
            dataType: 'json',
            success: function (res) {
                $.each(res, function (i, obj)
                {
                    var sel = "";
                    div_data += "<option value='" + obj.id + "'>" + obj.name + "</option>";
                });
                $('.editcharge_category').html("<option value=''><?php echo $this->lang->line('select'); ?></option>");
                $('.editcharge_category').append(div_data);
                $('.editcharge_category').select2("val", charge_category);
            }
        });
         }
    }

       
$(document).on('select2:select','.editcharge_category',function(){
        var charge_category=$(this).val();      
        $('.charge').html("<option value=''><?php echo $this->lang->line('loading') ?></option>");   
        $('.editcharge').html("<option value=''><?php echo $this->lang->line('loading') ?></option>");

        $('#editcharge_id').val('').trigger('change'); //added 
        $('#editapply_charge').val(0);  //added
        $('#editdiscount_percentage').val(0);  //added
        $('#editdiscount').val(0);  //added
        $('#editfinal_amount').val(0);//added
        $('#editcharge_tax').val(0);  //added
        $('#edittax').val(0);  //added

        geteditchargecode(charge_category,"");
 });

    function geteditchargecode(charge_category,charge_id) {      
      var div_data = "<option value=''><?php echo $this->lang->line('select'); ?></option>";
      if(charge_category != ""){
          $.ajax({
            url: base_url+'admin/charges/getchargeDetails',
            type: "POST",
            data: {charge_category: charge_category},
            dataType: 'json',
            success: function (res) {
                $.each(res, function (i, obj)
                {
                    var sel = "";
                    div_data += "<option value='" + obj.id + "'>" + obj.name + "</option>";
                });
                $('.charge').html(div_data);
                $(".charge").select2("val", charge_id);
                $('.editcharge').html(div_data);
                $(".editcharge").select2("val", charge_id);             
            }
        });
      }
    }
 
   $(document).on('select2:select','.addcharge',function(){
        var charge=$(this).val();
        var orgid=$('#organisation_id').val();
        let is_tpa= $("input:checkbox[name=is_tpa]").prop('checked') ? 1 : 0;
        var patient_id=$("#patient_id").val();
        $('#qty').val('1');

      $.ajax({
            url: '<?php echo base_url(); ?>admin/patient/getChargeById',
            type: "POST",
            data: {charge_id: charge, organisation_id: orgid,is_tpa:is_tpa,patient_id:patient_id},
            dataType: 'json',
            success: function (res) {
                if(res.status == 0){
                    errorMsg(res.msg);
                }else {
                    if(res.display_tpa_charge){
                        total_charge=res.result.org_charge;
                    }else{
                        total_charge=res.result.standard_charge;
                    }

                    var quantity=$('#qty').val();
                    $('#apply_charge').val(parseFloat(res.result.standard_charge) * quantity);
                    $('#addstandard_charge').val(res.result.standard_charge);
                    $('#addscd_charge').val(res.result.org_charge);
                    $('#charge_tax').val(res.result.percentage);
                    $('#total_charge').val(total_charge);//added
                    var standard_charge= res.result.standard_charge;
                    var schedule_charge= res.result.org_charge;
                    var discount_percent= 0;
                    var apply_charge=isNaN(parseFloat(total_charge)*parseFloat(quantity))?0 : parseFloat(total_charge)*parseFloat(quantity);
                    var discount_amount= (apply_charge*discount_percent)/100;
                    $('#apply_charge').val(apply_charge.toFixed(2));
                    var final_amount=apply_charge-discount_amount;
                   
                    $('#discount_percentage_amount').val( (discount_amount).toFixed(2));//added
                    $('#discount_percentage_add_charge').val((discount_percent).toFixed(2));//added

                    $('#tax').val(((final_amount*res.result.percentage)/100).toFixed(2));
                    $('#final_amount').val((final_amount+((final_amount*res.result.percentage)/100)).toFixed(2));
                    if(res.status == 2){
                            errorMsg(res.msg);
                    }
                }
            }
        });
 });

   $(document).on('select2:select','.editcharge',function(){
        var charge=$(this).val();
        var orgid=$('#editorganisation_id').val();
        let is_tpa= $("input:checkbox[name=edit_is_tpa]").prop('checked') ? 1 : 0;
        var patient_id=$("#editpatient_id").val();
        $('#qty').val('1');

      $.ajax({
            url: '<?php echo base_url(); ?>admin/patient/getChargeById',
            type: "POST",
            data: {charge_id: charge, organisation_id: orgid,is_tpa:is_tpa,patient_id:patient_id},
            dataType: 'json',
            success: function (res) {
                if(res.status == 0){
                    errorMsg(res.msg);
                }else {
                    if(res.display_tpa_charge){
                        total_charge=res.result.org_charge;
                    }else{
                        total_charge=res.result.standard_charge;
                    }

                    var quantity=$('#editqty').val();
                    $('#editapply_charge').val(parseFloat(res.result.standard_charge) * quantity);
                    $('#editstandard_charge').val(res.result.standard_charge);
                    $('#editscd_charge').val(res.result.org_charge);
                    $('#editcharge_tax').val(res.result.percentage);

                    $('#edit_total_charge').val(total_charge);//added

                    var standard_charge= res.result.standard_charge;
                    var schedule_charge= res.result.org_charge;
                    var discount_percent= 0;
                    var apply_charge=isNaN(parseFloat(total_charge)*parseFloat(quantity))?0 : parseFloat(total_charge)*parseFloat(quantity);
                    var discount_amount= (apply_charge*discount_percent)/100;
                    $('#editapply_charge').val(apply_charge.toFixed(2));
                    var final_amount=apply_charge-discount_amount;

                    $('#editdiscount_percentage').val((discount_percent).toFixed(2));//added
                    $('#editdiscount').val((discount_amount).toFixed(2));//added

                    $('#edittax').val(((final_amount*res.result.percentage)/100).toFixed(2));
                    $('#editfinal_amount').val((final_amount+((final_amount*res.result.percentage)/100)).toFixed(2));
                    if(res.status == 2){
                            errorMsg(res.msg);
                    }
                }
            }
        });
 });

   

   $(document).on('change','.death_status',function(){
      var status=$(this).val();
      if(status == "1"){
         $('.filestyle','#addPaymentModal').dropify();
          $('.filestyle','#add_refund').dropify();
        $('.death_status_div').css("display", "block");
        $('.reffer_div').css("display", "none");
      }else if(status == "2"){
        $('.reffer_div').css("display", "block");
         $('.death_status_div').css("display", "none");
      }else{
        $('.reffer_div').css("display", "none");
         $('.death_status_div').css("display", "none");
      }
    });

    $(document).on('click','.patient_discharge',function(){ 
           
            var case_reference_id="<?php echo $case_reference_id;?>";
            var payment_modal=$('#patient_discharge');
            payment_modal.addClass('modal_loading'); 
            shModal(payment_modal[0]).show(); 
            $.ajax({
            url: base_url+'admin/bill/patient_discharge/'+case_reference_id,
            type: "POST",
            data:{'module_type':'opd'},
            dataType: 'json',
               beforeSend: function() {
              
               }, 
            success: function (data) {              
                
           $('.modal-body',payment_modal).html(data.page);
           $('.filestyle','#patient_discharge').dropify();
           $('.date','#patient_discharge').trigger("change");
              payment_modal.removeClass('modal_loading'); 
            },

             error: function(xhr) { // if error occured
          alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");            
               
      },
      complete: function() {
            payment_modal.removeClass('modal_loading');      
      }
        }); 
       
    });

    $(document).on('submit','#form_patient_discharge', function(e){
            e.preventDefault();
            var clicked_btn = $("button[form=form_patient_discharge]");

            var form = $(this);    
            var btn = clicked_btn;
            btn.btnLoading();
            $.ajax({
                url: form.attr('action'),
                 type: "POST",                   
                data: new FormData(this),
                dataType: 'json',
                contentType: false,              
                processData: false,            
                success: function (data) {
                    if (data.status == "fail") {
                        var message = "";
                        $.each(data.error, function (index, value) {
                            message += value;
                        });
                        errorMsg(message);
                    } else {
                        successMsg(data.message);
                        window.location.reload(true);
                        }
                     btn.btnReset();
                },
                error: function () {

                },
                complete: function(){
                 btn.btnReset();
   }
            }); 
        });

    $(document).on('click','.print_dischargecard',function(){
         var $this = $(this);
         var record_id=$this.data('recordId');
         var case_id=$this.data('case_id');   
       $this.btnLoading();
      $.ajax({
          url: '<?php echo base_url(); ?>admin/bill/print_dischargecard',
          type: "POST",
          data:{'id':record_id,'case_id':case_id,'module_type':'opd'},
          dataType: 'json',
           beforeSend: function() {
                 $this.btnLoading();      
          },
          success: function(res) {
           popup(res.page);
          },
             error: function(xhr) { // if error occured
          alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");
                  $this.btnReset();              
         },
              complete: function() {
                   $this.btnReset();                 
             }
      });
  }); 

    $(document).on('click','.viewot',function(){
         var $this = $(this);
         var record_id=$this.data('recordId');          
       $this.btnLoading();
      $.ajax({
              url: base_url+'admin/operationtheatre/otdetails',
          type: "POST",
           data: {ot_id: record_id},
          dataType: 'json',
           beforeSend: function() {
                 $this.btnLoading();      
          },
          success: function(data) {
               shModal('view_ot_modal').show();
               $('#show_ot_data').html(data.page);     
               $('#action_detail_modal').html(data.actions); 
          },
             error: function(xhr) { // if error occured
          alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");
                  $this.btnReset();
              
         },
              complete: function() {
                   $this.btnReset();                 
             }
      });
  });
    
    $(document).ready(function (e) {
        modal_click_disabled('patient_discharge');
    }); 
</script>
<script>
    function getcategory(id,operation=null)
    {       
        var div_data = "";
        $('#operation_name').html("<option value='l'><?php echo $this->lang->line('loading') ?></option>");
        $.ajax({
            url: '<?php echo base_url(); ?>admin/operationtheatre/getoperationbycategory',
            type: "POST",
            data: {id:id},
            dataType: 'json',
            async: false,
            success: function (res) {
                $.each(res, function (i, obj)
                {
                    var sel = "";
                    if ((operation != '') && (operation == obj.id)) {
                        sel = "selected";
                    }
                    div_data += "<option value=" + obj.id + " " + sel + ">" + obj.operation + "</option>";
                });
                $("#operation_name").html("<option value=''><?= $this->lang->line('select') ?></option>");
                $('#operation_name').append(div_data);
                $("#operation_name").select2().select2('val', operation);

                if(operation!=""){
                    $("#eoperation_name").html("<option value=''><?= $this->lang->line('select') ?></option>");
                    $('#eoperation_name').append(div_data);
                    $("#eoperation_name").select2().select2('val', operation);
                }
            }
        });
    }
</script>
<script>
     $(document).on('click','.view_report',function(){
         var id=$(this).data('recordId');
         var lab=$(this).data('typeId');
         getinvestigationparameter(id,$(this),lab);
       });

        function getinvestigationparameter(id,btn_obj,lab){
         var modal_view=$('#viewDetailReportModal');
         var $this = btn_obj;   
        $.ajax({
            url: base_url+'admin/patient/getinvestigationparameter',
            type: "POST",
            data: {'id': id,'lab':lab},
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
<script type="text/javascript">
    $(document).on('click','.print_bill',function(){
    var id=$(this).data('recordId');
      
        var $this = $(this);
        var lab   = $(this).data('typeId');
        $.ajax({
            url: base_url+'admin/patient/printpathoparameter',
            type: "POST",
            data: {'id': id,'lab':lab},
            dataType: 'json',
               beforeSend: function() {
              $this.btnLoading();
               },
            success: function (data) {        
                popup(data.page);
            },

             error: function(xhr) { // if error occured
          alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");
             $this.btnReset();
               
      },
      complete: function() {
            $this.btnReset();     
      }
        });
    });
</script>
<script>    
    $(document).on('change', '.findingtype', function () {
        $this = $(this);
       
        var section_ul = $(this).closest('div.row').find('ul.section_ul');
        var finding_id = $(this).val();
        div_data="";
        $.ajax({
            type: 'POST',
            url: base_url + 'admin/patient/findingbycategory',
            data: {'finding_id': finding_id},
            dataType: 'JSON',            
            beforeSend: function () {
                // setting a timeout
                $('ul.section_ul').find('li:not(:first-child)').remove();
            },
            success: function (data) {
                section_ul.append(data.record);

            },
            error: function (xhr) { // if error occured
                alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");

            },
            complete: function () {

            }            
        });
    });
   
    $(document).on('change', '.findinghead', function () {

        $this = $(this);
        var head_id = $(this).val();
        div_data="";
        $.ajax({
            type: 'POST',
            url: base_url + 'admin/patient/getfinding',
            data: {'head_id': head_id},
            
            success: function (res) {
                $("#finding_description").val(res);
            },            
        });
    });

    $('.close_button').click(function(){
        $('#form_operationtheatre')[0].reset();
        $("#operation_category").select2().select2('val', '');
        $("#operation_name").select2().select2('val', '');
        $("#consultant_doctorid").select2().select2('val', '');
    })
</script> 

<script type="text/javascript">
    function delete_prescription(visitid) {   
        if (confirm('Are you sure')) {
            $.ajax({
                url: '<?php echo base_url(); ?>admin/prescription/deletePrescription/'+visitid,
                success: function (res) {
                    window.location.reload(true);
                },
                error: function () {
                    alert("Fail")
                }
            });
        }
    }

    $(document).ready(function (e) {
        modal_click_disabled('viewDetailReportModal');
    });
 
    function discharge_revert(case_id){
        var base_url = '<?php echo base_url() ?>';      
        $.ajax({
            type: 'POST',
            url: base_url + 'admin/bill/discharge_revert',
            data: {'module_type': 'opd','case_id':case_id},
            dataType: 'json',
            
            success: function (res) {              
             if(res.status=='success'){
                successMsg(res.message);
                window.location.reload(true);
             }else{
                errorMsg(res.message);
             }
            },            
        });
    }

    $(document).on('change','.revisit_payment_mode',function(){
      var mode=$(this).val();
      if(mode == "Cheque"){
         $('.filestyle','#revisitModal').dropify();
       $(".date").trigger("change");
        $('.revisit_cheque_div').css("display", "block");
       
      }else{
        $('.revisit_cheque_div').css("display", "none");
      }
    }); 
    $(document).on('change','.visit_payment_mode',function(){
      var mode=$(this).val();

      if(mode == "Cheque"){
       
       $('.filestyle','#editModal').dropify();
       $(".date").trigger("change");
       $('.cheque_div').css("display", "block");
       
      }else{

        $('.cheque_div').css("display", "none");
      }
    });
</script>
<!-- //========datatable end===== -->

<script>        
function reset_value(){
$(".reset_value").val('').trigger('change');
$(".reset_qty").val(1);
$('#apply_charge, #discount_percentage_add_charge, #discount_percentage_amount, #charge_tax, #tax, #final_amount, #editapply_charge, #editdiscount, #editcharge_tax, #edittax, #editfinal_amount').val(0);      
$("#preview_charges").html('');    
}

$(document).on('input paste keyup','.apply_charge_add_charge,.discount_percentage_add_charge', function(e){ 
update_charge_amount($(e.target).closest('div.modal'));
});

function update_charge_amount(object_model){     
        let  apply_charge= object_model.find('.apply_charge_add_charge').val();      
        let  discount_percentage=object_model.find('#discount_percentage_add_charge').val();
        let  discount_amount=(parseFloat(apply_charge) * discount_percentage/100);
        let  tax_percentage=object_model.find('#charge_tax').val();
        let  tax_amount=((apply_charge-discount_amount)*tax_percentage)/100;

        if(tax_percentage !='' && tax_percentage !=0){
            apply_amount=(parseFloat(apply_charge-discount_amount) * tax_percentage/100)+((parseFloat(apply_charge))-(discount_amount));      
            object_model.find('#tax').val((tax_amount).toFixed(2));
            object_model.find('.discount_percentage_amount').val(discount_amount.toFixed(2));            
            object_model.find('.net_amount').val(apply_amount.toFixed(2));            
        }else{
            apply_amount=((parseFloat(apply_charge))-(discount_amount));      
            object_model.find('#tax').val((tax_amount).toFixed(2));
            object_model.find('.discount_percentage_amount').val((discount_amount).toFixed(2));            
            object_model.find('.net_amount').val((apply_amount).toFixed(2)); 
        }
    }

    function get_percentage(discount_amount){
        var discount_amount=(discount_amount != "") ?discount_amount: 0;
        var total=$('#apply_charge').val();
        var tax=$('#tax').val();
        var tax_per=$('#charge_tax').val();
        var discount_percent=0;
        var net_amount=0;     
        var tax_amount=0;     
        discount_percent=((parseInt(discount_amount)/parseInt(total))*100);
        $('#discount_percentage_add_charge').val(discount_percent.toFixed(2));
        tax_amount=parseFloat((((total)-(discount_amount))*tax_per)/100);
        net_amount=((parseInt(total)-parseInt(discount_amount))+parseInt(tax_amount));
        $('#tax').val(tax_amount.toFixed(2));
        $('#final_amount').val(net_amount.toFixed(2));
    }

    function update_percentage(discount_amount){
        var discount_amount=(discount_amount != "") ?discount_amount: 0;
        var total=$('#editapply_charge').val();
        var tax=$('#edittax').val();
        var tax_per=$('#editcharge_tax').val();
        var discount_percent=0;
        var net_amount=0;     
        var tax_amount=0;     
        discount_percent=((parseInt(discount_amount)/parseInt(total))*100);
        $('#editdiscount_percentage').val(discount_percent.toFixed(2));
        tax_amount=parseFloat((((total)-(discount_amount))*tax_per)/100);
        net_amount=((parseInt(total)-parseInt(discount_amount))+parseInt(tax_amount));
        $('#edittax').val(tax_amount.toFixed(2));
        $('#editfinal_amount').val(net_amount.toFixed(2));
    }
//calculate discount amount to discount persantage

</script>
<script>

     $(document).on('select2:select','.charge',function(){
        var charge=$(this).val();
        var orgid = $("#revisit_organisation").val();
        var patient_id = $("#patient_id").val();
        let is_tpa= $("input:checkbox[name=revisit_is_tpa]").prop('checked') ? 1 : 0;
        $('#discount_percentage').val("") ;
        if(charge==''){
            reset_revisit();
            return false;
        }

        $.ajax({
            url: '<?php echo base_url(); ?>admin/patient/getChargeById',
            type: "POST",
            data: {charge_id: charge, organisation_id: orgid,patient_id:patient_id,is_tpa:is_tpa},
            dataType: 'json',
            success: function (res) { 

                if(res.status == 0){
                        errorMsg(res.msg);
                         $('#percentage').val('');
                         $('#standard_chargevisit').val('');
                         $('#apply_chargevisit').val('');
                         $('#revisit_amount').val('');
                         $('#paid_amount').val('');                  
                         $('#discount_percentage').val('');    
                    }else{
                        if(res.status == 2){
                            errorMsg(res.msg);
                             $('#percentage').val('');
                             $('#standard_chargevisit').val('');
                             $('#apply_chargevisit').val('');
                             $('#revisit_amount').val('');
                             $('#paid_amount').val('');                  
                             $('#discount_percentage').val('');   
                        }
                    var tax=res.result.percentage;
                    var quantity=$('#qty').val();
                    $('#percentage').val(tax);
                    $('#apply_chargevisit').val(parseFloat(res.result.standard_charge));
                    $('#standard_chargevisit').val(res.result.standard_charge);                    

                    if(res.display_tpa_charge){
                        if(res.result.percentage ==null){
                            apply_amount=parseFloat(res.result.org_charge);
                        }else{
                            apply_amount=(parseFloat(res.result.org_charge) * res.result.percentage/100)+(parseFloat(res.result.org_charge));
                        }
                        $('#apply_chargevisit').val(res.result.org_charge);
                        $('#revisit_amount').val(apply_amount.toFixed(2));
                        $('#paid_amount').val(apply_amount.toFixed(2));    
                    }else{
                        if(res.result.percentage ==null){
                            apply_amount=parseFloat(res.result.standard_charge);
                        }else{
                            apply_amount=(parseFloat(res.result.standard_charge) * res.result.percentage/100)+(parseFloat(res.result.standard_charge));
                        }                        
                        $('#apply_chargevisit').val(res.result.standard_charge);
                        $('#revisit_amount').val(apply_amount.toFixed(2));
                        $('#paid_amount').val(apply_amount.toFixed(2));                       
                    }                                           
                }              

            }
        });
    });
   
    $(document).on('change','#revisit_is_tpa',function(){
        reset_revisit();
    });

    function reset_revisit(){
        $('#percentage').val('');
        $('#standard_chargevisit').val('');
        $('#apply_chargevisit').val('');
        $('#revisit_amount').val('');
        $('#paid_amount').val('');                  
        $('#discount_percentage').val('');                  
        $('#charge_id').val('').trigger('change');    
        $('.charge_category').val('').trigger('change.select2');
        $('.charge ').val('').empty().trigger('change');               
    }
    </script>
