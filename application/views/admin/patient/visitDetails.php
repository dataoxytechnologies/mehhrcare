<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
$genderList = $this->customlib->getGender();
$case_reference_id=$result['case_reference_id'];
?> 
<div class="row">
            <?php if ($tab_type > 0): ?>
            <div class="col-md-2 sidebarlists sh-sidebar-col-2">
                <div class="ss-card">
                    <div class="ss-head">
                        <h3 class="ss-title">
                            <i class="fa fa-stethoscope"></i>
                            <?php
                            if ($tab_type == 1)     echo $this->lang->line('todays_opd');
                            elseif ($tab_type == 2) echo $this->lang->line('upcoming_opd');
                            else                    echo $this->lang->line('old_opd');
                            ?>
                        </h3>
                        <span class="ss-count" id="opd_sidebar_total" style="display:none;">0</span>
                    </div>
                    <div class="ss-search-wrap">
                        <i class="fa fa-search search-icon"></i>
                        <input type="text" id="opd_sidebar_search" class="form-control" placeholder="Search OPD / ID / Name">
                    </div>
                    <div id="opd_sidebar_content">
                        <div class="text-center sh-loading-pad"><i class="fa fa-spinner fa-spin"></i></div>
                    </div>
                    <div id="opd_sidebar_pagination" class="ss-pagination" style="display:none;">
                        <button id="opd_sidebar_prev" title="<?= $this->lang->line('previous') ?>"><i class="fa fa-chevron-left"></i></button>
                        <span id="opd_sidebar_pageinfo" class="ss-pagination-info"></span>
                        <button id="opd_sidebar_next" title="<?= $this->lang->line('next') ?>"><i class="fa fa-chevron-right"></i></button>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            <div class="<?php echo ($tab_type > 0) ? 'col-md-10 sh-pl-2' : 'col-md-12'; ?> itemcol">
                                <!-- OPD Detail Wrap — page-head + tab content -->
                                <div class="card">
                                <div class="card border0 mb0">
                                <div class="opd-detail-wrap opd-profile-wrap">

                                  <!-- ══ PAGE HEAD: patient identity + ph-tabs ══ -->
                                  <div class="page-head">
                                    <div class="ph-crumbs">
                                      <span><?php echo $this->lang->line('opd'); ?></span>
                                      <span class="sep">/</span>
                                      <span><?php echo html_escape($result['case_reference_id']); ?> &middot; <?php echo composePatientName($result['patient_name'], $result['patient_id']); ?></span>
                                    </div>
                                    <div class="ph-title-row">
                                      <div class="ph-title">
                                        <?php
                                          $ph_has_image  = !empty($result['image']) && strpos($result['image'], 'no_image') === false;
                                          $ph_clean_name = preg_replace('/\s*\([^)]*\)\s*/', ' ', (string) $result['patient_name']);
                                          $ph_parts      = preg_split('/\s+/', trim($ph_clean_name), -1, PREG_SPLIT_NO_EMPTY);
                                          if (empty($ph_parts)) {
                                              $ph_initial = '?';
                                          } elseif (count($ph_parts) === 1) {
                                              $ph_initial = mb_substr($ph_parts[0], 0, 1);
                                          } else {
                                              $ph_initial = mb_substr($ph_parts[0], 0, 1) . mb_substr(end($ph_parts), 0, 1);
                                          }
                                          $ph_initial = mb_strtoupper($ph_initial);
                                        ?>
                                        <?php if ($ph_has_image): ?>
                                          <img class="ph-av" src="<?php echo $this->media_storage->getImageURL($result['image']); ?>" alt="<?php echo html_escape($result['patient_name']); ?>">
                                        <?php else: ?>
                                          <div class="ph-av"><?php echo html_escape($ph_initial); ?></div>
                                        <?php endif; ?>
                                        <div>
                                          <h1>
                                            <?php echo composePatientName($result['patient_name'], $result['patient_id']); ?>
                                            <input type="hidden" id="result_opdid" name="" value="<?php echo $result['id'] ?>">
                                            <input type="hidden" id="result_pid" name="" value="<?php echo $result['patient_id'] ?>">
                                          </h1>
                                          <div class="sub">
                                            <span class="mono"><?php echo html_escape($result['case_reference_id']); ?> &middot; <?php echo $this->customlib->getSessionPrefixByType('opd_no').$opdid; ?></span>
                                            <span class="dot-sep"><?php echo $this->lang->line(strtolower($result['gender'])); ?> &middot; <?php echo $this->customlib->get_patient_current_age($result['patient_id']); ?></span>
                                            <?php if (!empty($patientdetails['patient']['doctor'])): ?>
                                              <?php foreach ($patientdetails['patient']['doctor'] as $ph_doc): ?>
                                                <?php if ($doctor_restriction != 'enabled' || $logged_staff_id == $ph_doc['id']): ?>
                                                  <span class="dot-sep"><?php echo html_escape($ph_doc['name'].' '.$ph_doc['surname']); ?></span>
                                                <?php endif; ?>
                                              <?php endforeach; ?>
                                            <?php endif; ?>
                                          </div>
                                        </div>
                                      </div>
                                      <div class="ph-actions">
                                        <?php if ($this->rbac->hasPrivilege('opd_patient', 'can_edit')): ?>
                                          <a href="#" onclick="editRecord('<?php echo $visitminid; ?>')" data-bs-target="#editModal" class="btn btn-sm ph-act ph-act-edit" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('edit'); ?>"><i class="fa fa-pencil"></i></a>
                                        <?php endif; ?>
                                        <?php if ($this->rbac->hasPrivilege('opd_patient_discharge', 'can_view')): ?>
                                          <a href="#" class="btn btn-sm patient_discharge ph-act ph-act-discharge" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('patient_discharge'); ?>"><i class="fa fa-hospital-o"></i></a>
                                        <?php endif; ?>
                                        <?php if (!$is_discharge && $this->rbac->hasPrivilege('opd_patient_discharge_revert', 'can_view')): ?>
                                          <a href="#" onclick="discharge_revert('<?php echo $result['case_reference_id']; ?>')" class="btn btn-sm ph-act ph-act-revert" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('discharge_revert'); ?>"><i class="fa fa-undo"></i></a>
                                        <?php endif; ?>
                                        <?php if ($this->rbac->hasPrivilege('opd_patient', 'can_delete')): ?>
                                          <a class="btn btn-sm text-danger ph-act ph-act-delete" href="#" onclick="delete_patient('<?php echo $result['id'] ?>','<?php echo $result['patient_id'] ?>')" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('delete_patient'); ?>"><i class="fa fa-trash"></i></a>
                                        <?php endif; ?>
                                      </div>
                                    </div>

                                    <!-- Tab navigation (ph-tabs style) -->
                                    <div class="ph-tabs-wrap">
                                    <button class="ph-tabs-arrow d-none" id="ph_tabs_prev" type="button"><i class="fa fa-chevron-left"></i></button>
                                    <nav class="ph-tabs" id="ph_tabs_nav" role="tablist">
                                      <a class="active" href="#overview" data-bs-toggle="tab" role="tab"><i class="fa fa-th"></i> <?php echo $this->lang->line('overview'); ?></a>
                                      <?php if ($this->rbac->hasPrivilege('visit', 'can_view')): ?><a href="#activity" data-bs-toggle="tab" role="tab"><i class="far fa-caret-square-down"></i> <?php echo $this->lang->line('visits'); ?></a><?php endif; ?>
                                      <?php if ($this->rbac->hasPrivilege('opd_medication', 'can_view')): ?><a href="#medication" class="medication" data-bs-toggle="tab" role="tab"><i class="fa fa-medkit"></i> <?php echo $this->lang->line('medication'); ?></a><?php endif; ?>
                                      <?php if ($this->rbac->hasPrivilege('opd_lab_investigation', 'can_view')): ?><a href="#labinvestigation" data-bs-toggle="tab" role="tab"><i class="fas fa-diagnoses"></i> <?php echo $this->lang->line('lab_investigation'); ?></a><?php endif; ?>
                                      <?php if ($this->rbac->hasPrivilege('opd_operation_theatre', 'can_view')): ?><a href="#operationtheatre" data-bs-toggle="tab" role="tab"><i class="fas fa-cut"></i> <?php echo $this->lang->line('operations'); ?></a><?php endif; ?>
                                      <?php if ($this->rbac->hasPrivilege('opd_charges', 'can_view')): ?><a href="#charges" data-bs-toggle="tab" role="tab"><i class="fas fa-donate"></i> <?php echo $this->lang->line('charges'); ?></a><?php endif; ?>
                                      <?php if ($this->rbac->hasPrivilege('opd_payment', 'can_view')): ?><a href="#payment" data-bs-toggle="tab" role="tab"><i class="fas fa-hand-holding-usd"></i> <?php echo $this->lang->line('payments'); ?></a><?php endif; ?>
                                      <?php if ($this->module_lib->hasActive('live_consultation') && $this->rbac->hasPrivilege('opd_live_consult', 'can_view')): ?><a href="#live_consult" data-bs-toggle="tab" role="tab"><i class="fa fa-video-camera"></i> <?php echo $this->lang->line('live_consultation'); ?></a><?php endif; ?>
                                      <?php if ($this->rbac->hasPrivilege('opd_timeline', 'can_view')): ?><a href="#timeline" data-bs-toggle="tab" role="tab"><i class="far fa-calendar-check"></i> <?php echo $this->lang->line('timeline'); ?></a><?php endif; ?>
                                      <?php if ($this->rbac->hasPrivilege('opd_treatment_history', 'can_view')): ?><a href="#treatment_history" data-bs-toggle="tab" role="tab"><i class="fas fa-diagnoses"></i> <?php echo $this->lang->line('treatment_history'); ?></a><?php endif; ?>
                                      <?php if ($this->rbac->hasPrivilege('opd_vitals', 'can_view')): ?><a href="#vitals" data-bs-toggle="tab" role="tab"><i class="fa fa-heartbeat"></i> <?php echo $this->lang->line('vitals'); ?></a><?php endif; ?>
                                    </nav>
                                    <button class="ph-tabs-arrow" id="ph_tabs_next" type="button"><i class="fa fa-chevron-right"></i></button>
                                    </div><!-- /.ph-tabs-wrap -->
                                  </div><!-- /.page-head -->

                                  <div class="tab-content p-3">
                                    <div class="tab-pane  active show" id="overview">

                                      <!-- ══ VISIT INFORMATION BUSINESS CARD ══ -->
                                      <section class="bcard">
                                        <div class="bcard-head">
                                          <div class="title"><?php echo $this->lang->line('overview'); ?></div>
                                        </div>
                                        <div class="bcard-grid">
                                          <div class="field"><div class="l"><?php echo $this->lang->line('patient'); ?></div><div class="v"><?php echo composePatientName($result['patient_name'], $result['patient_id']); ?></div></div>
                                          <div class="field"><div class="l"><?php echo $this->lang->line('case_id'); ?></div><div class="v mono"><?php echo html_escape($result['case_reference_id']); ?></div></div>
                                          <div class="field"><div class="l"><?php echo $this->lang->line('opd_no'); ?></div><div class="v mono"><?php echo $this->customlib->getSessionPrefixByType('opd_no').$opdid; ?></div></div>
                                          <div class="field"><div class="l"><?php echo $this->lang->line('gender'); ?></div><div class="v"><?php echo $this->lang->line(strtolower($result['gender'])); ?></div></div>
                                          <div class="field"><div class="l"><?php echo $this->lang->line('age'); ?></div><div class="v"><?php echo $this->customlib->get_patient_current_age($result['patient_id']); ?></div></div>
                                          <div class="field"><div class="l"><?php echo $this->lang->line('phone'); ?></div><div class="v"><?php echo html_escape($result['mobileno']); ?></div></div>
                                          <div class="field"><div class="l"><?php echo $this->lang->line('guardian_name'); ?></div><div class="v"><?php echo html_escape($result['guardian_name']); ?></div></div>
                                          <div class="field"><div class="l"><?php echo $this->lang->line('tpa'); ?></div><div class="v"><?php if (isset($result['organisation_name'])) echo html_escape($result['organisation_name']); ?></div></div>
                                          <div class="field"><div class="l"><?php echo $this->lang->line('tpa_id'); ?></div><div class="v"><?php if (isset($result['insurance_id'])) echo $result['insurance_id']; ?></div></div>
                                          <div class="field"><div class="l"><?php echo $this->lang->line('tpa_validity'); ?></div><div class="v"><?php if (isset($result['insurance_validity'])) echo $this->customlib->YYYYMMDDTodateFormat($result['insurance_validity']); ?></div></div>
                                          <div class="field">
                                            <div class="l"><?php echo $this->lang->line('barcode'); ?></div>
                                            <div class="v"><a href="<?php echo $this->media_storage->getImageURL('./uploads/patient_id_card/barcodes/'.$id.'.png'); ?>" target="_blank">
                                              <img  class="sh-qr-code"  src="<?php echo $this->media_storage->getImageURL('./uploads/patient_id_card/barcodes/'.$id.'.png'); ?>" width="80" height="22"></a></div>
                                          </div>
                                          <div class="field">
                                            <div class="l"><?php echo $this->lang->line('qrcode'); ?></div>
                                            <div class="v"><a href="<?php echo $this->media_storage->getImageURL('./uploads/patient_id_card/qrcode/'.$id.'.png'); ?>" target="_blank">
                                              <img  class="sh-qr-code"  src="<?php echo $this->media_storage->getImageURL('./uploads/patient_id_card/qrcode/'.$id.'.png'); ?>" width="48" height="48"></a></div>
                                          </div>
                                        </div>
                                      </section><!-- /.bcard -->

                                      <!-- ══ RELATED LISTS ══ -->
                                      <div class="rellist-wrap">

                                        <!-- Billing Progress -->
                                        <?php
                                        $rl_has_graph = $this->module_lib->hasActive('opd')||$this->module_lib->hasActive('pharmacy')||$this->module_lib->hasActive('pathology')||$this->module_lib->hasActive('radiology')||$this->module_lib->hasActive('blood_bank')||$this->module_lib->hasActive('ambulance');
                                        if ($rl_has_graph): ?>
                                        <div class="rellist">
                                          <div class="rellist-head">
                                            <div class="l">
                                              <div class="ic green"><i class="fa fa-bar-chart"></i></div>
                                              <div class="title"><?php echo $this->lang->line('overview'); ?></div>
                                            </div>
                                          </div>
                                          <div class="rellist-body pad">
                                            <div class="sh-chip-grid-200">
                                              <?php if ($this->module_lib->hasActive('opd')): ?>
                                              <div><div class="sh-bill-graph-title"><?php echo $this->lang->line('opd_billing_payment_graph'); ?></div>
                                              <div class="sh-bill-graph-meta"><?php echo $graph['opd']['opd_bill_payment_ratio']; ?>% &mdash; <?php echo $this->customlib->get_payment_bill($graph['opd']['payment']['total_payment'],$graph['opd']['bill']['total_bill']); ?></div>
                                              <div class="progress sh-progress-mini"><div class="progress-bar bg-info" style="--w:<?php echo $graph['opd']['opd_bill_payment_ratio']; ?>%"></div></div></div>
                                              <?php endif; if ($this->module_lib->hasActive('pharmacy')): ?>
                                              <div><div class="sh-bill-graph-title"><?php echo $this->lang->line('pharmacy_billing_payment_graph'); ?></div>
                                              <div class="sh-bill-graph-meta"><?php echo $graph['pharmacy']['pharmacy_bill_payment_ratio']; ?>% &mdash; <?php echo $this->customlib->get_payment_bill(($graph['pharmacy']['payment']['total_payment']-$graph['pharmacy']['payment_refund']['total_payment']),$graph['pharmacy']['bill']['total_bill']); ?></div>
                                              <div class="progress sh-progress-mini"><div class="progress-bar bg-info" style="--w:<?php echo $graph['pharmacy']['pharmacy_bill_payment_ratio']; ?>%"></div></div></div>
                                              <?php endif; if ($this->module_lib->hasActive('pathology')): ?>
                                              <div><div class="sh-bill-graph-title"><?php echo $this->lang->line('pathology_billing_payment_graph'); ?></div>
                                              <div class="sh-bill-graph-meta"><?php echo $graph['pathology']['pathology_bill_payment_ratio']; ?>% &mdash; <?php echo $this->customlib->get_payment_bill($graph['pathology']['payment']['total_payment'],$graph['pathology']['bill']['total_bill']); ?></div>
                                              <div class="progress sh-progress-mini"><div class="progress-bar bg-info" style="--w:<?php echo $graph['pathology']['pathology_bill_payment_ratio']; ?>%"></div></div></div>
                                              <?php endif; if ($this->module_lib->hasActive('radiology')): ?>
                                              <div><div class="sh-bill-graph-title"><?php echo $this->lang->line('radiology_billing_payment_graph'); ?></div>
                                              <div class="sh-bill-graph-meta"><?php echo $graph['radiology']['radiology_bill_payment_ratio']; ?>% &mdash; <?php echo $this->customlib->get_payment_bill($graph['radiology']['payment']['total_payment'],$graph['radiology']['bill']['total_bill']); ?></div>
                                              <div class="progress sh-progress-mini"><div class="progress-bar bg-info" style="--w:<?php echo $graph['radiology']['radiology_bill_payment_ratio']; ?>%"></div></div></div>
                                              <?php endif; if ($this->module_lib->hasActive('blood_bank')): ?>
                                              <div><div class="sh-bill-graph-title"><?php echo $this->lang->line('blood_bank_billing_payment_graph'); ?></div>
                                              <div class="sh-bill-graph-meta"><?php echo $graph['blood_bank']['blood_bank_bill_payment_ratio']; ?>% &mdash; <?php echo $this->customlib->get_payment_bill($graph['blood_bank']['payment']['total_payment'],$graph['blood_bank']['bill']['total_bill']); ?></div>
                                              <div class="progress sh-progress-mini"><div class="progress-bar bg-info" style="--w:<?php echo $graph['blood_bank']['blood_bank_bill_payment_ratio']; ?>%"></div></div></div>
                                              <?php endif; if ($this->module_lib->hasActive('ambulance')): ?>
                                              <div><div class="sh-bill-graph-title"><?php echo $this->lang->line('ambulance_billing_payment_graph'); ?></div>
                                              <div class="sh-bill-graph-meta"><?php echo $graph['ambulance']['ambulance_bill_payment_ratio']; ?>% &mdash; <?php echo $this->customlib->get_payment_bill($graph['ambulance']['payment']['total_payment'],$graph['ambulance']['bill']['total_bill']); ?></div>
                                              <div class="progress sh-progress-mini"><div class="progress-bar bg-info" style="--w:<?php echo $graph['ambulance']['ambulance_bill_payment_ratio']; ?>%"></div></div></div>
                                              <?php endif; ?>
                                            </div>
                                          </div>
                                        </div>
                                        <?php endif; ?>

                                        <!-- Vitals -->
                                        <?php if ($this->rbac->hasPrivilege('opd_vitals', 'can_view') && !empty($patientcurrentvital)): ?>
                                        <?php
                                          $rl_height = ''; $rl_weight = '';
                                          foreach ($patientcurrentvital as $rl_v) {
                                            if ($rl_v['id'] == '1' && $rl_v['patient_range'] != '') $rl_height = (strpos($rl_v['patient_range'],'-')!==false) ? explode('-',$rl_v['patient_range'])[0] : $rl_v['patient_range'];
                                            if ($rl_v['id'] == '2' && $rl_v['patient_range'] != '') $rl_weight = (strpos($rl_v['patient_range'],'-')!==false) ? explode('-',$rl_v['patient_range'])[0] : $rl_v['patient_range'];
                                          }
                                        ?>
                                        <div class="rellist">
                                          <div class="rellist-head">
                                            <div class="l">
                                              <div class="ic teal"><i class="fas fa-heartbeat"></i></div>
                                              <div class="title"><?php echo $this->lang->line('current_vitals'); ?></div>
                                              <div class="count"><?php echo count($patientcurrentvital); ?></div>
                                            </div>
                                          </div>
                                          <div class="rellist-body pad">
                                            <div class="sh-chip-grid-120">
                                              <?php foreach ($patientcurrentvital as $rl_vital):
                                                $rl_vrange = $this->lang->line('normal'); $rl_vcol = 'var(--border)';
                                                if (strpos($rl_vital['reference_range'],'-')!==false) {
                                                  $rl_rng = explode('-',$rl_vital['reference_range']); $rl_rmin=$rl_rng[0]; $rl_rmax=$rl_rng[1];
                                                  if (strpos($rl_vital['patient_range'],'-')!==false) {
                                                    $rl_vp=explode('-',$rl_vital['patient_range']);
                                                    if($rl_vp[0]<$rl_rmin||$rl_vp[1]>$rl_rmax){$rl_vrange=$this->lang->line('abnormal');$rl_vcol='var(--red)';}
                                                  } else {
                                                    if($rl_vital['patient_range']<$rl_rmin){$rl_vrange=$this->lang->line('low');$rl_vcol='var(--blue)';}
                                                    if($rl_vital['patient_range']>$rl_rmax){$rl_vrange=$this->lang->line('high');$rl_vcol='var(--amber)';}
                                                  }
                                                }
                                              ?>
                                              <div class="sh-vital-card" style="--v-col:<?php echo $rl_vcol; ?>">
                                                <div class="sh-vital-label"><?php echo $rl_vital['name']; ?></div>
                                                <div class="sh-vital-value"><?php echo $rl_vital['patient_range']; ?> <span class="sh-vital-unit"><?php echo $rl_vital['unit']; ?></span></div>
                                                <div class="sh-vital-range"><?php echo $rl_vrange; ?></div>
                                              </div>
                                              <?php endforeach; ?>
                                              <?php if ($rl_weight!=''&&$rl_height!=''):
                                                $rl_h1=$rl_height*0.01; $rl_bh=$rl_h1*$rl_h1; $rl_bmi=($rl_bh>0)?round($rl_weight/$rl_bh,2):'';
                                                if ($rl_bmi): ?>
                                              <div class="sh-vital-card">
                                                <div class="sh-vital-label"><?php echo $this->lang->line('bmi'); ?></div>
                                                <div class="sh-vital-value"><?php echo $rl_bmi; ?></div>
                                              </div>
                                              <?php endif; endif; ?>
                                            </div>
                                          </div>
                                        </div>
                                        <?php endif; ?>

                                        <!-- Allergies, Findings, Symptoms, ICD codes -->
                                        <?php $rl_has_clinical = !empty($patientdetails['patient']['allergy'])||!empty($patientdetails['patient']['findings'])||!empty($patientdetails['patient']['symptoms'])||!empty($opd_icd_codes); ?>
                                        <?php if ($rl_has_clinical): ?>
                                        <div class="rellist sh-rellist-danger">
                                          <div class="rellist-head">
                                            <div class="l">
                                              <div class="ic danger"><i class="fa fa-exclamation"></i></div>
                                              <div class="title"><?php echo $this->lang->line('known_allergies'); ?> &amp; <?php echo $this->lang->line('findings'); ?></div>
                                            </div>
                                          </div>
                                          <div class="rellist-body pad">
                                            <div class="sh-chip-grid-200-16">
                                              <?php if (!empty($patientdetails['patient']['allergy'])): ?>
                                              <div>
                                                <div class="sh-section-label"><?php echo $this->lang->line('known_allergies'); ?></div>
                                                <?php foreach ($patientdetails['patient']['allergy'] as $rl_row): ?><div class="sh-section-item"><?php echo html_escape($rl_row['known_allergies']); ?></div><?php endforeach; ?>
                                              </div>
                                              <?php endif; ?>
                                              <?php if (!empty($patientdetails['patient']['findings'])): ?>
                                              <div>
                                                <div class="sh-section-label"><?php echo $this->lang->line('findings'); ?></div>
                                                <?php foreach ($patientdetails['patient']['findings'] as $rl_row): ?><div class="sh-section-item"><?php echo html_escape($rl_row['finding_description']); ?></div><?php endforeach; ?>
                                              </div>
                                              <?php endif; ?>
                                              <?php if (!empty($patientdetails['patient']['symptoms'])): ?>
                                              <div>
                                                <div class="sh-section-label"><?php echo $this->lang->line('symptoms'); ?></div>
                                                <?php foreach ($patientdetails['patient']['symptoms'] as $rl_row): ?><div class="sh-section-item"><?php echo html_escape($rl_row['symptoms']); ?></div><?php endforeach; ?>
                                              </div>
                                              <?php endif; ?>
                                              <?php if (!empty($opd_icd_codes)): ?>
                                              <div>
                                                <div class="sh-section-label"><?php echo $this->lang->line('icd10_diagnosis'); ?></div>
                                                <?php foreach ($opd_icd_codes as $rl_icd): ?><div class="sh-section-item"><span class="badge bg-info"><?php echo html_escape($rl_icd['icd_code']); ?></span> <?php echo html_escape($rl_icd['icd_description']); ?></div><?php endforeach; ?>
                                              </div>
                                              <?php endif; ?>
                                            </div>
                                          </div>
                                        </div>
                                        <?php endif; ?>

                                        <!-- Consultant Doctor -->
                                        <?php if (!empty($patientdetails['patient']['doctor'])): ?>
                                        <div class="rellist">
                                          <div class="rellist-head">
                                            <div class="l">
                                              <div class="ic blue"><i class="fa fa-user-md"></i></div>
                                              <div class="title"><?php echo $this->lang->line('consultant_doctor'); ?></div>
                                            </div>
                                          </div>
                                          <div class="rellist-body pad">
                                            <div class="sh-doctor-flex">
                                              <?php foreach ($patientdetails['patient']['doctor'] as $rl_doc):
                                                if ($doctor_restriction == 'enabled' && $logged_staff_id != $rl_doc['id']) continue; ?>
                                              <div class="sh-doctor-row-md">
                                                <?php if ($rl_doc['image']!=''): ?>
                                                  <img src="<?php echo $this->media_storage->getImageURL('uploads/staff_images/'.$rl_doc['image']); ?>">
                                                <?php else: ?>
                                                  <div class="sh-doc-initials"><?php echo mb_strtoupper(mb_substr($rl_doc['name'],0,1)); ?></div>
                                                <?php endif; ?>
                                                <div>
                                                  <div class="doc-name"><?php echo html_escape($rl_doc['name'].' '.$rl_doc['surname']); ?></div>
                                                  <div class="doc-emp"><?php echo html_escape($rl_doc['employee_id']); ?></div>
                                                </div>
                                              </div>
                                              <?php endforeach; ?>
                                            </div>
                                          </div>
                                        </div>
                                        <?php endif; ?>

                                        <!-- Medication summary -->
                                        <?php if (!empty($medicationreport_overview)): ?>
                                        <div class="rellist">
                                          <div class="rellist-head">
                                            <div class="l">
                                              <div class="ic purple"><i class="fa fa-medkit"></i></div>
                                              <div class="title"><?php echo $this->lang->line('medication'); ?></div>
                                            </div>
                                          </div>
                                          <div class="rellist-body">
                                            <table class="table table-striped table-bordered table-hover mb0">
                                              <thead><tr><th><?php echo $this->lang->line('date'); ?></th><th><?php echo $this->lang->line('medicine_name'); ?></th><th><?php echo $this->lang->line('dose'); ?></th><th><?php echo $this->lang->line('time'); ?></th><th><?php echo $this->lang->line('remark'); ?></th></tr></thead>
                                              <tbody>
                                                <?php for ($rl_i=0;$rl_i<$recent_record_count;$rl_i++): if (!empty($medicationreport_overview[$rl_i])): ?>
                                                <tr>
                                                  <td><?php if ($medicationreport_overview[$rl_i]['date']) echo $this->customlib->YYYYMMDDTodateFormat($medicationreport_overview[$rl_i]['date']); ?></td>
                                                  <td><?php echo $medicationreport_overview[$rl_i]['medicine_name']; ?></td>
                                                  <td><?php echo $medicationreport_overview[$rl_i]['medicine_dosage'].' ('.$medicationreport_overview[$rl_i]['unit'].')'; ?></td>
                                                  <td><?php echo $this->customlib->getHospitalTime_Format($medicationreport_overview[$rl_i]['time']); ?></td>
                                                  <td><?php echo $medicationreport_overview[$rl_i]['remark']; ?></td>
                                                </tr>
                                                <?php endif; endfor; ?>
                                              </tbody>
                                            </table>
                                          </div>
                                        </div>
                                        <?php endif; ?>

                                        <!-- Lab Investigation summary -->
                                        <?php if (!empty($investigations)): ?>
                                        <div class="rellist">
                                          <div class="rellist-head">
                                            <div class="l">
                                              <div class="ic amber"><i class="fas fa-diagnoses"></i></div>
                                              <div class="title"><?php echo $this->lang->line('lab_investigation'); ?></div>
                                            </div>
                                          </div>
                                          <div class="rellist-body">
                                            <table class="table table-striped table-bordered table-hover mb0">
                                              <thead><tr><th><?php echo $this->lang->line('test_name'); ?></th><th><?php echo $this->lang->line('lab'); ?></th><th><?php echo $this->lang->line('sample_collected'); ?></th><th><?php echo $this->lang->line('expected_date'); ?></th><th><?php echo $this->lang->line('approved_by'); ?></th></tr></thead>
                                              <tbody>
                                                <?php $rl_i=0; foreach ($investigations as $rl_inv): ++$rl_i; if ($rl_i<=$recent_record_count): ?>
                                                <tr>
                                                  <td><?php echo html_escape($rl_inv['test_name']); ?> (<?php echo html_escape($rl_inv['short_name']); ?>)</td>
                                                  <td><?php echo $this->lang->line($rl_inv['type']); ?></td>
                                                  <td><?php echo composeStaffNameByString($rl_inv['collection_specialist_staff_name'],$rl_inv['collection_specialist_staff_surname'],$rl_inv['collection_specialist_staff_employee_id']); ?><br/><?php echo ($rl_inv['type']=='pathology')?$this->lang->line('pathology_center'):$this->lang->line('radiology'); ?>: <?php echo html_escape($rl_inv['test_center']); ?><?php if ($rl_inv['collection_date']) echo '<br/>'.$this->customlib->YYYYMMDDTodateFormat($rl_inv['collection_date']); ?></td>
                                                  <td><?php if ($rl_inv['reporting_date']) echo $this->customlib->YYYYMMDDTodateFormat($rl_inv['reporting_date']); ?></td>
                                                  <td><?php echo composeStaffNameByString($rl_inv['approved_by_staff_name'],$rl_inv['approved_by_staff_surname'],$rl_inv['approved_by_staff_employee_id']); ?><?php if ($rl_inv['parameter_update']) echo '<br/>'.$this->customlib->YYYYMMDDTodateFormat($rl_inv['parameter_update']); ?></td>
                                                </tr>
                                                <?php endif; endforeach; ?>
                                              </tbody>
                                            </table>
                                          </div>
                                        </div>
                                        <?php endif; ?>

                                        <!-- Operations summary -->
                                        <?php if (!empty($operation_theatre)): ?>
                                        <div class="rellist">
                                          <div class="rellist-head">
                                            <div class="l">
                                              <div class="ic"><i class="fas fa-cut"></i></div>
                                              <div class="title"><?php echo $this->lang->line('operation'); ?></div>
                                            </div>
                                          </div>
                                          <div class="rellist-body">
                                            <table class="table table-striped table-bordered table-hover mb0">
                                              <thead><tr><th><?php echo $this->lang->line('reference_no'); ?></th><th><?php echo $this->lang->line('operation_date'); ?></th><th><?php echo $this->lang->line('operation_name'); ?></th><th><?php echo $this->lang->line('operation_category'); ?></th><th><?php echo $this->lang->line('ot_technician'); ?></th></tr></thead>
                                              <tbody>
                                                <?php $rl_i=0; foreach ($operation_theatre as $rl_ot): $rl_i++; if ($rl_i<=$recent_record_count): ?>
                                                <tr>
                                                  <td><?php echo $this->customlib->getSessionPrefixByType('operation_theater_reference_no').$rl_ot['id']; ?></td>
                                                  <td><?php echo $this->customlib->YYYYMMDDHisTodateFormat($rl_ot['date'],$this->customlib->getHospitalTimeFormat()); ?></td>
                                                  <td><?php echo $rl_ot['operation']; ?></td>
                                                  <td><?php echo $rl_ot['category']; ?></td>
                                                  <td><?php echo $rl_ot['ot_technician']; ?></td>
                                                </tr>
                                                <?php endif; endforeach; ?>
                                              </tbody>
                                            </table>
                                          </div>
                                        </div>
                                        <?php endif; ?>

                                        <!-- Charges summary -->
                                        <?php if (!empty($charges_detail)): ?>
                                        <div class="rellist">
                                          <div class="rellist-head">
                                            <div class="l">
                                              <div class="ic green"><i class="fas fa-donate"></i></div>
                                              <div class="title"><?php echo $this->lang->line('charges'); ?></div>
                                            </div>
                                          </div>
                                          <div class="rellist-body">
                                            <table class="table table-striped table-bordered table-hover mb0">
                                              <thead><tr><th><?php echo $this->lang->line('name'); ?></th><th><?php echo $this->lang->line('charge_type'); ?></th><th><?php echo $this->lang->line('standard_charge').' ('.$currency_symbol.')'; ?></th><th class="text-end"><?php echo $this->lang->line('discount'); ?></th><th class="text-end"><?php echo $this->lang->line('tax'); ?></th><th class="text-end"><?php echo $this->lang->line('applied_charge').' ('.$currency_symbol.')'; ?></th><th class="text-end"><?php echo $this->lang->line('amount').' ('.$currency_symbol.')'; ?></th></tr></thead>
                                              <tbody>
                                                <?php $rl_total=0; $rl_i=0; foreach ($charges_detail as $rl_ch): ++$rl_i; if ($rl_i<=$recent_record_count):
                                                  $rl_disc=($rl_ch['apply_charge']*$rl_ch['discount_percentage'])/100;
                                                  $rl_tax=(($rl_ch['apply_charge']-$rl_disc)*$rl_ch['tax']/100);
                                                  $rl_taxfmt=amountFormat($rl_tax);
                                                  $rl_total+=$rl_ch['amount']; ?>
                                                <tr>
                                                  <td><?php echo $rl_ch['name']; ?><?php if ($rl_ch['note']!='') echo '<div class="text-muted small">'.$this->lang->line('charge_note').': '.html_escape($rl_ch['note']).'</div>'; ?></td>
                                                  <td class="text-capitalize"><?php echo $rl_ch['charge_type']; ?></td>
                                                  <td><?php echo $rl_ch['standard_charge']; ?></td>
                                                  <td class="text-end"><?php echo amountFormat($rl_disc).' ('.$rl_ch['discount_percentage'].'%)'; ?></td>
                                                  <td class="text-end"><?php echo amountFormat($rl_taxfmt).' ('.$rl_ch['tax'].'%)'; ?></td>
                                                  <td class="text-end"><?php echo $rl_ch['apply_charge']; ?></td>
                                                  <td class="text-end"><?php echo $rl_ch['amount']; ?></td>
                                                </tr>
                                                <?php endif; endforeach; ?>
                                              </tbody>
                                            </table>
                                          </div>
                                        </div>
                                        <?php endif; ?>

                                        <!-- Payment summary -->
                                        <?php if (!empty($payment_details)): ?>
                                        <div class="rellist">
                                          <div class="rellist-head">
                                            <div class="l">
                                              <div class="ic teal"><i class="fas fa-hand-holding-usd"></i></div>
                                              <div class="title"><?php echo $this->lang->line('payment'); ?></div>
                                            </div>
                                          </div>
                                          <div class="rellist-body">
                                            <table class="table table-striped table-bordered table-hover mb0">
                                              <thead><tr><th><?php echo $this->lang->line('transaction_id'); ?></th><th><?php echo $this->lang->line('date'); ?></th><th><?php echo $this->lang->line('note'); ?></th><th><?php echo $this->lang->line('payment_mode'); ?></th><th class="text-end"><?php echo $this->lang->line('paid_amount').' ('.$currency_symbol.')'; ?></th></tr></thead>
                                              <tbody>
                                                <?php $rl_i=0; foreach ($payment_details as $rl_pay): ++$rl_i; if ($rl_i<=$recent_record_count): ?>
                                                <tr>
                                                  <td><?php echo $this->customlib->getSessionPrefixByType('transaction_id').$rl_pay['id']; ?></td>
                                                  <td><?php echo $this->customlib->YYYYMMDDHisTodateFormat($rl_pay['payment_date'],$this->customlib->getHospitalTimeFormat()); ?></td>
                                                  <td><?php echo html_escape($rl_pay['note']); ?></td>
                                                  <td><?php echo $this->lang->line(strtolower($rl_pay['payment_mode']));
                                                    if ($rl_pay['payment_mode']=='Cheque') {
                                                      if ($rl_pay['cheque_no']!='') echo '<br/>'.$this->lang->line('cheque_no').': '.$rl_pay['cheque_no'];
                                                      if ($rl_pay['cheque_date']!=''&&$rl_pay['cheque_date']!='0000-00-00') echo '<br/>'.$this->lang->line('cheque_date').': '.$this->customlib->YYYYMMDDTodateFormat($rl_pay['cheque_date']);
                                                    } ?></td>
                                                  <td class="text-end"><?php echo $rl_pay['amount']; ?></td>
                                                </tr>
                                                <?php endif; endforeach; ?>
                                              </tbody>
                                            </table>
                                          </div>
                                        </div>
                                        <?php endif; ?>

                                        <!-- Live Consultation summary -->
                                        <?php if (!empty($visitconferences)): ?>
                                        <div class="rellist">
                                          <div class="rellist-head">
                                            <div class="l">
                                              <div class="ic blue"><i class="fa fa-video-camera"></i></div>
                                              <div class="title"><?php echo $this->lang->line('live_consultation'); ?></div>
                                            </div>
                                          </div>
                                          <div class="rellist-body">
                                            <table class="table table-striped table-bordered table-hover mb0">
                                              <thead><tr><th><?php echo $this->lang->line('consultation_title'); ?></th><th><?php echo $this->lang->line('date'); ?></th><th><?php echo $this->lang->line('created_by'); ?></th><th><?php echo $this->lang->line('created_for'); ?></th><th><?php echo $this->lang->line('patient'); ?></th></tr></thead>
                                              <tbody>
                                                <?php $rl_i=0; foreach ($visitconferences as $rl_conf): ++$rl_i; if ($rl_i<=$recent_record_count):
                                                  $rl_conf_resp=json_decode(json_encode($rl_conf->return_response),true); ?>
                                                <tr>
                                                  <td><a href="#" data-bs-toggle="popover" class="detail_popover"><?php echo $rl_conf->title; ?></a><div class="fee_detail_popover" style="display:none"><?php if($rl_conf->description=='') echo '<p class="text-danger">'.$this->lang->line('no_description').'</p>'; else echo '<p class="text-info">'.$rl_conf->description.'</p>'; ?></div></td>
                                                  <td><?php echo date($this->customlib->getHospitalDateFormat(true,true),strtotime($rl_conf->date)); ?></td>
                                                  <td><?php if($rl_conf->created_id==$logged_staff_id) echo $this->lang->line('self'); else { $rl_n=($rl_conf->create_by_surname=='')?$rl_conf->create_by_name:$rl_conf->create_by_name.' '.$rl_conf->create_by_surname; echo $rl_n.' ('.$rl_conf->for_create_role_name.' : '.$rl_conf->for_create_employee_id.')'; } ?></td>
                                                  <td><?php $rl_n=($rl_conf->create_for_surname=='')?$rl_conf->create_for_name:$rl_conf->create_for_name.' '.$rl_conf->create_for_surname; echo $rl_n.' ('.$rl_conf->for_create_role_name.' : '.$rl_conf->for_create_employee_id.')'; ?></td>
                                                  <td><?php echo $rl_conf->patient_name.' ('.$this->lang->line('patient').' : '.$rl_conf->patientid.')'; ?></td>
                                                </tr>
                                                <?php endif; endforeach; ?>
                                              </tbody>
                                            </table>
                                          </div>
                                        </div>
                                        <?php endif; ?>

                                        <!-- Timeline -->
                                        <?php if (!empty($timeline_list)): ?>
                                        <div class="rellist">
                                          <div class="rellist-head">
                                            <div class="l">
                                              <div class="ic"><i class="far fa-calendar-check"></i></div>
                                              <div class="title"><?php echo $this->lang->line('timeline'); ?></div>
                                            </div>
                                          </div>
                                          <div class="rellist-body pad" id="timeline_list">
                                            <div class="rl-tl">
                                              <?php $rl_i=0; foreach ($timeline_list as $rl_tl): ++$rl_i; if ($rl_i<=$recent_record_count): ?>
                                              <div class="rl-tl-item">
                                                <div class="rl-tl-dot"></div>
                                                <div class="rl-tl-card">
                                                  <div class="rl-tl-meta">
                                                    <span class="rl-tl-date"><i class="far fa-clock me-1"></i><?php if($rl_tl['timeline_date']) echo date($this->customlib->getHospitalDateFormat(true,true),strtotime($rl_tl['timeline_date'])); ?></span>
                                                    <?php if (!empty($rl_tl['document'])): ?>
                                                    <span class="rl-tl-actions">
                                                      <a class="btn btn-sm btn-secondary" href="<?php echo base_url().'admin/timeline/download_patient_timeline/'.$rl_tl['id']; ?>" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('download'); ?>"><i class="fa fa-download"></i></a>
                                                    </span>
                                                    <?php endif; ?>
                                                  </div>
                                                  <div class="rl-tl-title"><?php echo html_escape($rl_tl['title']); ?></div>
                                                  <?php if (!empty($rl_tl['description'])): ?><div class="rl-tl-body"><?php echo $rl_tl['description']; ?></div><?php endif; ?>
                                                </div>
                                              </div>
                                              <?php endif; endforeach; ?>
                                            </div>
                                          </div>
                                        </div>
                                        <?php endif; ?>

                                      </div><!-- /.rellist-wrap -->
                                    </div><!-- /#overview -->

                        
                        <?php if ($this->rbac->hasPrivilege('visit', 'can_view')) { ?>
                            <div class="tab-pane card" id="activity">
                                <div class="box-tab-header">
                                        <div class="d-flex align-items-center gap-2 flex-wrap">
                                            <h3 class="box-tab-title"><?php echo $this->lang->line('checkups'); ?></h3>
                                            <h5 class="opd-no-badge"><?php echo $opd_prefix.$result['id']; ?></h5>
                                        </div>
                                        <div class="box-tab-tools">
                                            <?php if ($this->rbac->hasPrivilege('checkup', 'can_add')) { if($is_discharge){ ?>
                                        <a href="#"  onclick="getRevisitRecord('<?php echo $visitdata['visitid'] ?>')" class="btn btn-primary btn-sm revisitrecheckup"  data-bs-toggle="modal" title=""><i class="fa fa-plus"></i> <?php echo $this->lang->line('new_checkup'); ?></a>
                                       <?php }} ?>
                                         </div>
                                </div>
                                <div class="download_label"><?php echo composePatientName($result['patient_name'],$result['patient_id']) . " " . $this->lang->line('opd_details'); ?></div>
                                <div class="table-responsive overflow-visible-lg">
                                    <table class="table table-striped table-bordered table-hover ajaxlist" cellspacing="0" width="" data-export-title="<?php echo composePatientName($result['patient_name'],$result['patient_id']) . " " . $this->lang->line('opd_details'); ?>">
                                        <thead>
                                        <th><?php echo $this->lang->line('checkup_id'); ?></th>
                                        <th><?php echo $this->lang->line('appointment_date'); ?></th>
                                        <th><?php echo $this->lang->line('consultant'); ?></th>
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
                        
                        <?php if ($this->rbac->hasPrivilege('opd_operation_theatre', 'can_view')) { ?>
                        <div class="tab-pane card" id="operationtheatre">
                            <div class="box-tab-header">
                                <h3 class="box-tab-title"><?php echo $this->lang->line("operations"); ?></h3>
                                <div class="box-tab-tools">
                                   <?php if($is_discharge){ 
                                    if ($this->rbac->hasPrivilege('opd_operation_theatre', 'can_add')) { ?>
                                    <a href="javascript:void(0)" onclick="shModal('add_operationtheatre').show()" class="btn btn-primary btn-sm addoperationtheatre"><i class="fa fa-plus"></i> <?php echo $this->lang->line("add_operation"); ?></a>
                                <?php }} ?>
                                </div>
                            </div>   
                            
                           <div class="download_label"><?php echo composePatientName($result['patient_name'],$result['patient_id']) . " " . $this->lang->line('opd_details'); ?></div>
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-hover example" data-export-title="<?php echo $this->lang->line('operation_theatre'); ?>">
                                    <thead>
                                        <th><?php echo $this->lang->line("reference_no"); ?></th>
                                        <th><?php echo $this->lang->line("operation_date"); ?></th>
                                        <th><?php echo $this->lang->line("operation_name"); ?></th>
                                        <th><?php echo $this->lang->line("operation_category"); ?></th>
                                        <th><?php echo $this->lang->line("ot_technician"); ?></th>
                                        <?php 
                                            if (!empty($ot_fields)) {
                                                foreach ($ot_fields as $fields_key => $fields_value) {
                                                    ?>
                                                    <th class="white-space-nowrap"><?php echo $fields_value->name; ?></th>
                                                    <?php
                                                } 
                                            }
                                        ?> 
                                        <th class="text-end noExport"><?php echo $this->lang->line('action'); ?></th>
                                    </thead>
                                    <tbody id="">
                                            <?php
                                        if (!empty($operation_theatre)) {
                                            foreach ($operation_theatre as $ot_key => $ot_value) {
                                                ?>  
                                                <tr>    
                                                    <td><?php echo $this->customlib->getSessionPrefixByType('operation_theater_reference_no'). $ot_value["id"] ?></td>
                                     <td><?php echo 
                                        $this->customlib->YYYYMMDDHisTodateFormat($ot_value["date"],$this->customlib->getHospitalTimeFormat());
                                        ?></td>
                                                    <td><?php echo $ot_value["operation"]; ?></td>
                                                    <td><?php echo $ot_value["category"] ?></td>
                                                    <td><?php echo $ot_value['ot_technician'] ?></td>
                                                    <?php
                                                    if (!empty($ot_fields)) {

                                                        foreach ($ot_fields as $fields_key => $fields_value) {
                                                            $display_field = $ot_value[$fields_value->name];
                                                            if ($fields_value->type == "link") {
                                                                $display_field = "<a href=" . $ot_value[$fields_value->name] . " target='_blank'>" . $ot_value[$fields_value->name] . "</a>";
                                                            }
                                                            ?>
                                                            <td>
                                                                <?php echo $display_field; ?>
                                                            </td>
                                                            <?php
                                                        }
                                                    }
                                                    ?>
                                                    <td class="text-end">
                                                        <a href='javascript:void(0);' class='btn btn-secondary btn-sm viewot'  data-keyboard="false" data-loading-text='<i class="fa fa-circle-o-notch fa-spin"></i>' data-bs-toggle='tooltip' data-record-id='<?php echo $ot_value['id']; ?>'  title="<?php echo  $this->lang->line('show')?>"><i class='fa fa-reorder'></i></a>
                                                        <?php
                                                        if($is_discharge){
                                                            if ($this->rbac->hasPrivilege('opd_operation_theatre', 'can_edit')) { ?>                                                          
                                                            <a data-record-id='<?php echo $ot_value['id']; ?>' class="btn btn-secondary btn-sm editot" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('edit'); ?>">
                                                                    <i class="fa fa-pencil"></i>
                                                            </a>   
                                                            <?php }
                                                            if ($this->rbac->hasPrivilege('opd_operation_theatre', 'can_delete')) { ?>

                                                            <a onclick="deleteot('<?php echo $ot_value['id']; ?>')" class="btn btn-secondary btn-sm" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('delete'); ?>">
                                                            <i class="fa fa-trash"></i>
                                                            </a> 
                                                            <?php
                                                        }
                                                        } 
                                                        ?>  
                                                    </td>
                                                </tr>                                            
                                            <?php } }?>
                                    </tbody>
                                </table>
                            </div> 
                        </div> 
                        
                        <?php
                        }
                         if ($this->rbac->hasPrivilege('opd_medication', 'can_view')) { ?>
                        <div class="tab-pane card" id="medication">
                                    <div class="box-tab-header">
                                        <h3 class="box-tab-title"><?php echo $this->lang->line("medication"); ?></h3>
                                        <div class="box-tab-tools">
                                        <?php if ($this->rbac->hasPrivilege('opd_medication', 'can_add')) {   if($is_discharge){ ?>
                                            <a href="#" class="btn btn-sm btn-primary dropdown-toggle addmedication" onclick="addmedicationModal()" data-bs-toggle='modal'><i class="fa fa-plus"></i> <?php echo $this->lang->line("add_medication_dose"); ?></a>
                                        <?php }} ?>
                                        </div>    
                                   </div><!--./box-tab-header-->           
                                    <div class="download_label"><?php echo composePatientName($result['patient_name'],$result['patient_id']) . " " . $this->lang->line('opd_details'); ?></div>
                                    <div class="opd-med-v2">
                                    <?php if (!empty($medication)):
                                        $med_by_date = [];
                                        foreach ($medication as $mv) { $med_by_date[$mv['date']][] = $mv; }
                                        krsort($med_by_date);
                                        foreach ($med_by_date as $group_date => $group_items):
                                            $display_date = $this->customlib->YYYYMMDDTodateFormat($group_date);
                                            $day_name     = $this->lang->line(strtolower(date('l', strtotime($group_date))));
                                    ?>
                                    <div class="med-date-group">
                                        <div class="med-date-header">
                                            <div class="med-date-badge"><i class="fa fa-calendar"></i> <?php echo $display_date; ?></div>
                                            <span class="med-date-day"><?php echo $day_name; ?></span>
                                            <div class="med-date-line"></div>
                                        </div>
                                        <div class="med-col-header">
                                            <div class="med-name-col"><?php echo $this->lang->line('medicine_name'); ?></div>
                                            <div class="med-doses-label"><?php echo $this->lang->line('dose'); ?></div>
                                        </div>
                                        <?php foreach ($group_items as $med_value):
                                            $pharmacy_id          = $med_value['pharmacy_id'];
                                            $medicine_category_id = $med_value['medicine_category_id'];
                                            foreach ($med_value['dosage'][$group_date] as $mkey => $mvalue):
                                                $btn_category_id = $medicine_category_id;
                                                $btn_pharmacy_id  = $pharmacy_id;
                                        ?>
                                        <div class="med-row">
                                            <div class="med-name-col">
                                                <div class="med-name"><?php echo html_escape($mvalue['name']); ?></div>
                                            </div>
                                            <div class="med-doses">
                                                <?php foreach ($mvalue['dose_list'] as $didx => $dose):
                                                    $btn_category_id = $dose['medicine_category_id'];
                                                    $btn_pharmacy_id  = $dose['pharmacy_id'];
                                                    $d_edit = $d_del = '';
                                                    if ($this->rbac->hasPrivilege('opd_medication', 'can_edit'))
                                                        $d_edit = '<a href="#" class="dose-btn edit" data-bs-toggle="tooltip" title="'.$this->lang->line('edit').'" onclick="medicationDoseModal('.$dose['id'].')"><i class="fa fa-pencil"></i></a>';
                                                    if ($this->rbac->hasPrivilege('opd_medication', 'can_delete'))
                                                        $d_del  = '<a href="#" class="dose-btn del delete_record_dosage" data-bs-toggle="tooltip" title="'.$this->lang->line('delete').'" data-record-id="'.$dose['id'].'"><i class="fa fa-trash"></i></a>';
                                                ?>
                                                <div class="dose-chip">
                                                    <div class="dose-amount"><?php echo html_escape($dose['medicine_dosage'].' '.$dose['unit']); ?></div>
                                                    <div class="dose-time"><i class="fa fa-clock-o"></i> <?php echo date('h:i A', strtotime($dose['time'])); ?></div>
                                                    <?php if (!empty($dose['remark'])): ?><div class="dose-remark"><?php echo html_escape($dose['remark']); ?></div><?php endif; ?>
                                                    <div class="dose-foot">
                                                        <span class="dose-creator"><?php echo html_escape($dose['staff_name'].' '.$dose['staff_surname'].' ('.$dose['staff_employee_id'].')'); ?></span>
                                                        <?php if ($d_edit || $d_del): ?><span class="dose-actions"><?php echo $d_edit.$d_del; ?></span><?php endif; ?>
                                                    </div>
                                                </div>
                                                <?php endforeach; ?>
                                                <?php if ($this->rbac->hasPrivilege('opd_medication', 'can_add') && $is_discharge): ?>
                                                <a href="#" class="dose-add-btn addmedication" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('add_medication_dose'); ?>"
                                                   onclick="medicationModal('<?php echo $btn_category_id; ?>','<?php echo $btn_pharmacy_id; ?>','<?php echo $display_date; ?>')"
                                                   data-bs-toggle="modal"><i class="fa fa-plus"></i></a>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <?php endforeach; endforeach; ?>
                                    </div>
                                    <?php endforeach;
                                    else: ?>
                                    <div class="dataTables_empty">
                                        No data available in table <br><br>
                                        <img src="<?php echo base_url('backend/images/addnewitem.svg'); ?>" width="150" alt=""><br><br>
                                        <span class="text-success bolds"><i class="fa fa-arrow-left"></i> Search with different criteria.</span>
                                    </div>
                                    <?php endif; ?>
                                    </div>
                                </div>                          
                        <?php } ?>                       
                        
                        <!-- -->
                        <?php if ($this->rbac->hasPrivilege('opd_lab_investigation', 'can_view')) { ?>
                        <div class="tab-pane card" id="labinvestigation">
                            <div class="box-tab-header">
                                <h3 class="box-tab-title"><?php echo $this->lang->line('lab_investigation'); ?></h3>                           
                        </div>    
                            <div class="download_label"><?php echo composePatientName($result['patient_name'],$result['patient_id']) . " " . $this->lang->line('opd_details'); ?></div>
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-hover example" data-export-title="<?php echo $this->lang->line('lab_investigation'); ?>">
                                    <thead>
                                        <th><?php echo $this->lang->line('test_name'); ?></th>
                                         <th><?php echo $this->lang->line('lab'); ?></th>
                                        <th><?php echo $this->lang->line('sample_collected'); ?></th>
                                        <th><?php echo $this->lang->line('expected_date'); ?></th>
                                         <th><?php echo $this->lang->line('approved_by'); ?></th>
                                        <th class="text-end noExport"><?php echo $this->lang->line('action'); ?></th>
                                    </thead>
                                    <tbody id="">
                                        <?php foreach($investigations as $row ){ ?>
                                            <tr>
                                            <td><?php echo $row['test_name']; ?><br/>
                                           <?php echo "(".$row['short_name'].")"; ?></td>
                                            <td><?php echo $this->lang->line($row['type']); ?></td>
                                            <td><label>
                                 <?php echo composeStaffNameByString($row['collection_specialist_staff_name'],$row['collection_specialist_staff_surname'],$row['collection_specialist_staff_employee_id']); ?>
                                 </label>                                    
                                     <br/>
                                     <label ><?php if($row['type']=='pathology'){ echo $this->lang->line('pathology_center');  }else{ echo $this->lang->line('radiology_center');
                                     }  ?> : </label>                                    
                                        <?php
                                   echo $row['test_center']; 
                                    ?>
                                    <br/>
                                     <?php if($row['collection_date']){ echo $this->customlib->YYYYMMDDTodateFormat($row['collection_date']); } ?></td>                                       
                                       <td>
                                    <?php
                                    if($row['reporting_date']){
                                    echo  $this->customlib->YYYYMMDDTodateFormat($row['reporting_date']); 
                                    }?>                                        
                                    </td>
                                    <td class="text-start">                                        
                                        <?php      
                                    echo composeStaffNameByString($row['approved_by_staff_name'],$row['approved_by_staff_surname'],$row['approved_by_staff_employee_id']);
                                     ?>
                                     <br/>
                                    <?php    if($row['parameter_update']){                            
                                    echo  $this->customlib->YYYYMMDDTodateFormat($row['parameter_update']); }
                                     ?>                                         
                                    </td>
                                    <td class="text-end"><a href='javascript:void(0)'  data-loading-text='<i class="fa fa-reorder"></i>' data-record-id='<?php echo $row['report_id'];?>' data-type-id='<?php echo $row['type'];?>'  class='btn btn-secondary btn-sm view_report' data-bs-toggle='tooltip' title='<?php echo $this->lang->line("show"); ?>'><i class='fa fa-reorder'></i></a></td>
                                    </tr>
                                     <?php } ?>
                                    </tbody>
                                </table>
                            </div> 
                        </div>     
                        <?php } ?>                       

                        <!-- Charges -->
                            <?php if ($this->rbac->hasPrivilege('opd_charges', 'can_view')) { ?>
                            <div class="tab-pane card" id="charges">
                                <div class="box-tab-header">
                                  <h3 class="box-tab-title"><?php echo $this->lang->line('charges'); ?></h3>
                                <div class="box-tab-tools">
                                    <?php if ($this->rbac->hasPrivilege('opd_charges', 'can_add')) { 
                                        if($is_discharge){ ?>
                                        <a href="javascript:void(0)" onclick="shModal('add_chargeModal').show()" class="btn btn-primary btn-sm addcharges"><i class="fa fa-plus"></i> <?php echo $this->lang->line('add_charges') ?></a>
                                        <?php }
                                    } ?>
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
                                                             <div class="bill_item_footer text-muted"><label><?php if($charges_value["note"] !=''){ echo $this->lang->line('charge_note').': ';} ?></label> <?php echo html_escape($charges_value["note"]); ?></div>
                                                        </td>
                                                        <td class="text-capitalize"><?php echo $charges_value["charge_type"] ?></td>
                                                        <td class="text-capitalize"><?php echo $charges_value["charge_category_name"] ?></td>
                                                        <td class="text-capitalize"><?php echo $charges_value['qty'] ?></td>                                        
                                                        <td class="text-end"><?php echo $charges_value["standard_charge"] ?></td>
                                                        <td class="text-end"><?php echo $charges_value["apply_charge"] ?></td>
                                                        <td class="text-end"><?php echo $charges_value["tpa_charge"] ?></td>
                                                        <td class="text-end"><?php echo amountFormat(round($discount_amount,2))." (".$charges_value["discount_percentage"]."%) " ;?></td>
                                                        <td class="text-end"><?php echo $taxamount." (".$charges_value["tax"]."%) " ;?></td>                                    
                                                        <td class="text-end"><?php echo $charges_value["amount"] ?></td>
                                                        <td class="text-end"> 
    <a href="javascript:void(0);" class="btn btn-secondary btn-sm print_charge" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('print');?>" data-loading-text="<?php echo $this->lang->line('please_wait') ;?>" data-record-id="<?php echo $charges_value['id']; ?>">
    <i class="fa fa-print"></i>
    </a> 
     <?php 
    if($is_discharge){
        if ($this->rbac->hasPrivilege('opd_charges', 'can_edit')) { 
    ?>
    <a href='javascript:void(0);' class='btn btn-secondary btn-sm edit_charge' data-loading-text='<?php echo $this->lang->line('please_wait') ;?>' data-bs-toggle='tooltip' data-record-id='<?php echo $charges_value['id']; ?>'  title="<?php echo  $this->lang->line('edit')?>"><i class='fa fa-pencil'></i></a>
                                                            <?php } } if ($this->rbac->hasPrivilege('opd_charges', 'can_delete')) {
                                                            if($is_discharge){ ?>                                                              
                        <a href="javascript:void(0);" onclick="deleteOpdPatientCharge('<?php echo $charges_value['id']; ?>')" class="btn btn-secondary btn-sm" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('delete'); ?>"><i class="fa fa-trash"></i></a>
                                                    <?php } }?>   
                                                        </td>                                                       
                                                    </tr>
                                                    <?php
                                                }
                                            }
                                            ?> 
                                        </tbody>
                                        <tr class="box box-solid total-bg">
                                            <td colspan='11' class="text-end"><?php echo $this->lang->line('total') . " : " . $currency_symbol . "" . amountFormat($total); ?> 
                                            <input type="hidden" id="charge_total" name="charge_total" value="<?php echo $total ?>">
                                            </td>
                                             <td></td>                                            
                                        </tr>
                                    </table>
                                </div> 
                            </div> 
                            <?php } ?>                            
                            <!-- -->   

                            <!-- -->
                            <?php  if ($this->rbac->hasPrivilege('opd_treatment_history', 'can_view')) { ?>
                        <div class="tab-pane card" id="treatment_history">
                            <div class="box-tab-header">
                                <h3 class="box-tab-title"><?php echo $this->lang->line('treatment_history'); ?></h3>
                            </div>
                            <div class="impbtnview-t9">
                                
                            </div>                            
                               <div class="download_label"><?php echo composePatientName($result['patient_name'],$result['id']) . " " . $this->lang->line('opd_details'); ?></div>
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-hover treatmentlist"  data-export-title="<?php echo composePatientName($result['patient_name'],$result['id']) . " " . $this->lang->line('opd_details'); ?>">
                                        <thead>
                                        <th><?php echo $this->lang->line('opd_no'); ?></th>
                                        <th><?php echo $this->lang->line('case_id'); ?></th>
                                        <th><?php echo $this->lang->line('appointment_date'); ?></th>
                                        <th><?php echo $this->lang->line('symptoms'); ?></th>
                                        <th><?php echo $this->lang->line('consultant_doctor'); ?></th>
                                        <th class="text-end noExport"><?php echo $this->lang->line('action') ?></th>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                </table>
                            </div> 
                        </div>
                         <?php } ?>    

                            <!--payment -->
                            <?php if ($this->rbac->hasPrivilege('opd_payment', 'can_view')) {
                                ?>
                            <div class="tab-pane card" id="payment">
                                <div class="box-tab-header">
                                  <h3 class="box-tab-title"><?php echo $this->lang->line('payments'); ?></h3>
                                <?php
                                if ($this->rbac->hasPrivilege('opd_payment', 'can_add')) {
                                      if($is_discharge){ ?>
                                    <div class="box-tab-tools">                                     
                                        <a href="#" class="btn btn-sm btn-primary dropdown-toggle addpayment"  data-bs-toggle='modal'><i class="fa fa-plus"></i> <?php echo $this->lang->line('add_payment'); ?></a>
                                    </div><!--./impbtnview-->
                                    <?php
                                    }
                                }
                                ?>
                            </div>    
                            <div class="download_label"><?php echo $this->lang->line('payments'); ?></div>
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
                                                        <td><?php echo html_escape($payment["note"]) ?></td>
                                                        <td ><?php echo $this->lang->line(strtolower($payment["payment_mode"] ?? ''))."<br>";

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
    <a href='<?php echo site_url('admin/transaction/download/'.$payment['id']);?>' class='btn btn-secondary btn-sm' data-bs-toggle='tooltip' title='<?php echo $this->lang->line('download'); ?>'><i class='fa fa-download'></i></a>
    <?php
}
         ?>
 <a href="javascript:void(0);" class="btn btn-secondary btn-sm print_trans" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('print') ;?>" data-loading-text="<?php echo $this->lang->line('please_wait') ;?>" data-record-id="<?php echo $payment['id']; ?>">
                                                                    <i class="fa fa-print"></i>
                                                                </a>  
                                                            <?php if (!empty($payment["document"])) { ?>
                                                                <a href="<?php echo base_url(); ?>admin/payment/download/<?php echo $payment["document"]; ?>"  class="btn btn-secondary btn-sm"  data-bs-toggle="tooltip" title="<?php echo $this->lang->line('download'); ?>">
                                                                    <i class="fa fa-download"></i>
                                                                </a>
                                                                <?php } ?>                                                              
                                                            <?php if ($this->rbac->hasPrivilege('opd_payment', 'can_edit')) { ?>
                                                            <a href="javascript:void(0);" class="btn btn-secondary btn-sm editpayment" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('edit'); ?>" data-payment-amount="<?php echo $payment["amount"] ?>" data-record-id="<?php echo $payment['id']; ?>"><i class="fa fa-pencil"></i></a>
                                                            <?php } ?>
                                                            <?php
                                                             if($is_discharge){ 
                                                            if ($this->rbac->hasPrivilege('opd_payment', 'can_delete')) { ?>
                                                                <a href="javascript:void(0);" onclick="deletePayment('<?php echo $payment['id']; ?>')" class="btn btn-secondary btn-sm" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('delete'); ?>"><i class="fa fa-trash"></i></a>   
                                                    <?php } } ?>
                                                        </td>
                                                    </tr>
                                        <?php } }?> 
                                        </tbody>
                                                <tr class="box box-solid total-bg">
                                                    <td></td> 
                                                    <td></td> 
                                                    <td></td> 
                                                    <td></td> 
                                                     <td  class="text-end"><?php echo $this->lang->line('total') . " : " . $currency_symbol . "" . number_format((float)$total_payment, 2, '.', ''); ?>
                                                    </td> 
                                                        <td></td>
                                                </tr>
                                    </table>
                                </div> 
                            </div> 
                            <!-- -->
                            <?php } ?>
                                
                        <?php if ($this->rbac->hasPrivilege('opd_timeline', 'can_view')) { ?>        
                        <div class="tab-pane card" id="timeline">
                            <div class="box-tab-header">
                                <h3 class="box-tab-title"><?php echo $this->lang->line('timeline'); ?></h3>
                            <div class="box-tab-tools"> 
<?php if ($this->rbac->hasPrivilege('opd_timeline', 'can_add')) { 
      if($is_discharge){ ?>
                                    <a href="javascript:void(0)" onclick="shModal('myTimelineModal').show()" class="btn btn-primary btn-sm addtimeline"><i class="fa fa-plus"></i>  <?php echo $this->lang->line('add_timeline'); ?></a>
<?php }
} ?>
                            </div>
                        </div>    
                            <div class="timeline-header no-border">
                                <div id="timeline_list">
                                        <?php if (empty($timeline_list)): ?>
                                        <div class="dataTables_empty">
                                            No data available in table <br><br>
                                            <img src="<?php echo base_url('backend/images/addnewitem.svg'); ?>" width="150" alt=""><br><br>
                                            <span class="text-success bolds"><i class="fa fa-arrow-left"></i> Search with different criteria.</span>
                                        </div>
                                        <?php else: ?>
                                        <div class="rl-tl sh-pt-8">
                                          <?php foreach ($timeline_list as $key => $value): ?>
                                          <div class="rl-tl-item">
                                            <div class="rl-tl-dot"></div>
                                            <div class="rl-tl-card">
                                              <div class="rl-tl-meta">
                                                <span class="rl-tl-date"><i class="far fa-clock me-1"></i><?php echo date($this->customlib->getHospitalDateFormat(true,true), strtotime($value['timeline_date'])); ?></span>
                                                <span class="rl-tl-actions">
                                                  <?php if (!empty($value['document'])): ?>
                                                    <a class="btn btn-sm btn-secondary" href="<?php echo base_url().'admin/timeline/download_patient_timeline/'.$value['id']; ?>" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('download'); ?>"><i class="fa fa-download"></i></a>
                                                    <a class="btn btn-sm btn-secondary" target="_blank" href="<?php echo $this->customlib->getBaseUrl().'uploads/patient_timeline/'.$value['document']; ?>" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('view'); ?>"><i class="fa fa-reorder"></i></a>
                                                  <?php endif; ?>
                                                  <?php if ($this->rbac->hasPrivilege('opd_timeline', 'can_edit') && $is_discharge && $value['generated_users_type'] != 'patient'): ?>
                                                    <a class="btn btn-sm btn-secondary" onclick="editTimeline('<?php echo $value['id']; ?>')" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('edit'); ?>"><i class="fa fa-pencil"></i></a>
                                                  <?php endif; ?>
                                                  <?php if ($this->rbac->hasPrivilege('opd_timeline', 'can_delete') && $is_discharge && $value['generated_users_type'] != 'patient'): ?>
                                                    <a class="btn btn-sm btn-secondary text-danger" onclick="delete_timeline('<?php echo $value['id']; ?>')" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('delete'); ?>"><i class="fa fa-trash"></i></a>
                                                  <?php endif; ?>
                                                </span>
                                              </div>
                                              <div class="rl-tl-title"><?php echo html_escape($value['title']); ?></div>
                                              <?php if (!empty($value['description'])): ?><div class="rl-tl-body"><?php echo $value['description']; ?></div><?php endif; ?>
                                            </div>
                                          </div>
                                          <?php endforeach; ?>
                                        </div>
                                        <?php endif; ?>
                                </div>
                            </div>
                        </div>  
                        <?php } ?>
                        <!-- -->
                        
                        <?php if ($this->rbac->hasPrivilege('opd_vitals', 'can_view')) { ?>
                        <div class="tab-pane card" id="vitals">
                            <div class="box-tab-header">
                                <h3 class="box-tab-title"><?php echo $this->lang->line('vitals'); ?></h3>
                           <div class="box-tab-tools"> 
                                <?php if ($this->rbac->hasPrivilege('opd_vitals', 'can_add')) { ?>
                                <a href="javascript:void(0)" onclick="shModal('vitalModal').show()" id="add_vital_modal" class="btn btn-primary btn-sm vital"><i class="fa fa-plus"></i> <?php echo $this->lang->line('add_vital'); ?></a>
                                <?php  } ?>                               
                            </div>
                            </div>
                            <div class="timeline-header no-border">
                                <div id="vital_list">
                                    <?php
                                    if (empty($patient_vital_date)) {
                                        ?>
                                        <div class="table_inner">
                                            <table class="table table-striped table-bordered ">
                                                <thead class="relative z-4">
                                                    <tr>
                                                        <th><?php echo $this->lang->line("date"); ?> </th>
                                                        <?php foreach($vital_list as $vl){ ?>
                                                        <th ><?php echo $vl["name"]; ?> <br> (<?php echo $vl["reference_range"]; ?> <?php echo $vl["unit"]; ?>) </th>
                                                        <?php } ?>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr><td colspan="<?php echo (is_array($vital_list) ? count($vital_list) : 0) + 1; ?>" class="dataTables_empty">
                                                        No data available in table <br><br>
                                                        <img src="<?php echo base_url('backend/images/addnewitem.svg'); ?>" width="150" alt=""><br><br>
                                                        <span class="text-success bolds"><i class="fa fa-arrow-left"></i> Search with different criteria.</span>
                                                    </td></tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    <?php } else {   ?>
                                        <div class="table_inner">
                                            <table class="table table-striped table-bordered ">
                                                <thead class="relative z-4">
                                                    <tr>
                                                        <th><?php echo $this->lang->line("date"); ?> </th>                                                  
                                                        <?php foreach($vital_list as $vl){ ?>
                                                        <th ><?php echo $vl["name"]; ?> <br> (<?php echo $vl["reference_range"]; ?> <?php echo $vl["unit"]; ?>) </th>
                                                        <?php } ?>
                                                    </tr>
                                                </thead>
                                                <tbody>                                                      
                                                    <?php foreach($patient_vital_date as $pvd){ $messure_date =''; ?>
                                                    <tr>
                                                        <th><?php echo $date = $this->customlib->YYYYMMDDTodateFormat($pvd['messure_date']);
                                                        if($pvd['messure_date']){
                                                            $messure_date = date('Y-m-d', strtotime($pvd['messure_date']));
                                                        }
                                                        ?></th>                                                     
                                                        <?php foreach($vital_list as $vl){                                                      
                                                            $vital_id = $vl["id"];                                                 
                                                             
                                                              ?>                                                        
                                                                <td>
                                                                    <div class="relative">
                                                                        <?php  foreach($patientvital[$messure_date][$vital_id] as $pmi){ ?>       
                                                                        <table>
                                                                            <tr>                                                                            
                                                                                <td class="tablehovericon">                                             
                                                                                    <?php 
                                                                                        echo $pmi['patient_range'];                                                                     
                                                                                        $datetime = $this->customlib->YYYYMMDDHisTodateFormat($pmi['messure_date'],$this->customlib->getHospitalTimeFormat()); 
                                                                                        $str2 = substr($datetime, 10);
                                                                                        echo  " (".$str2 .")";  
                                                                                    ?>
                                                                                    <span class="hover-show-icon">                                                      
                                                                                        <?php if ($this->rbac->hasPrivilege('opd_vitals', 'can_edit')) { ?>
                                                                                        <a onclick="editvital('<?php echo $pmi['id']; ?>')" class="btn btn-secondary btn-sm" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('edit'); ?>"><i class="fa fa-pencil"></i></a>
                                                                                        <?php } ?>
                                                                                        <?php if ($this->rbac->hasPrivilege('opd_vitals', 'can_delete')) { ?>
                                                                                        <a class="btn btn-secondary btn-sm" data-bs-toggle="tooltip" onclick="delete_patient_vital('<?php echo $pmi['id']; ?>')"  title="<?php echo $this->lang->line('delete'); ?>"><i class="fa fa-trash"></i></a>
                                                                                        <?php } ?>
                                                                                    </span>
                                                                                </td>
                                                                            </tr>                                                                   
                                                                        </table>                                                                    
                                                                        <?php } ?>      
                                                                    </div>                                                              
                                                                </td>                                                            
                                                        <?php }  ?>                                                         
                                                    </tr>
                                                    <?php } ?>                                                   
                                                </tbody>                                   
                                            </table>
                                        </div>
                                    <?php } ?> 
                                </div>
                            </div>
                        </div> 
                        <?php }  
                        
                        if ($this->module_lib->hasActive('live_consultation')) { if ($this->rbac->hasPrivilege('opd_live_consult', 'can_view')) { ?> 

                        <div class="tab-pane card" id="live_consult">
                            <div class="box-tab-header">
                                <h3 class="box-tab-title"><?php echo $this->lang->line('live_consultation'); ?></h3>
                            </div>   
                            
                            <?php
                              if ($this->session->flashdata('msg')) { 
                                ?>
                                <?php echo $this->session->flashdata('msg'); $this->session->unset_userdata('msg'); ?>
                            <?php
                             } 
                            ?>
                            
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-hover example">
                                    <thead>
                                    <th><?php echo $this->lang->line('consultation_title'); ?></th>
                                        <th><?php echo $this->lang->line('date'); ?></th>
                                        <th><?php echo $this->lang->line('created_by'); ?> </th>
                                        <th><?php echo $this->lang->line('created_for'); ?></th>
                                        <th><?php echo $this->lang->line('patient'); ?></th>
                                        <th class="noExport"><?php echo $this->lang->line('status'); ?></th>
                                        <th class="text-end noExport"><?php echo $this->lang->line('action'); ?></th>
                                    </thead>
                                    <tbody>
                                        <?php
                                    if (empty($visitconferences)) {
                                        ?>

                                        <?php
                                    } else {
                                
                                        foreach ($visitconferences as $conference_key => $conference_value) {
                                        
                                            $return_response = isJSON($conference_value->return_response) ? json_decode($conference_value->return_response):false;
                                          
                                            ?>
                                            <tr>
                                                <td class="mailbox-name">                                                 
                                                    <a href="#" data-bs-toggle="popover" class="detail_popover"><?php echo $conference_value->title; ?></a>
                                                    <div class="fee_detail_popover" style="display: none">
                                                        <?php
                                                        if ($conference_value->description == "") {
                                                            ?>
                                                            <p class="text text-danger"><?php echo $this->lang->line('no_description'); ?></p>
                                                            <?php
                                                        } else {
                                                            ?>
                                                            <p class="text text-info"><?php echo $conference_value->description; ?></p>
                                                            <?php
                                                        }
                                                        ?>
                                                    </div>
                                                </td>
                                                <td class="mailbox-name">
                                                <?php echo date($this->customlib->getHospitalDateFormat(true, true), strtotime($conference_value->date))?>
                                                <td class="mailbox-name">
                                                    <?php                                                      
                                                    if($conference_value->created_id != ''){
                                                        if ($conference_value->created_id == $logged_staff_id) {                                                            
                                                            echo $this->lang->line('self');                                                            
                                                        } else {                            
                                                            if($conference_value->create_by_name !=''){                                                            
                                                                echo $this->lang->line('patient');                                                
                                                            }else{                                                                
                                                                $name= ($conference_value->create_by_surname == "") ? $conference_value->create_by_name : $conference_value->create_by_name . " " . $conference_value->create_by_surname;
                                                                echo  $name. " (".$conference_value->create_by_role_name." : ".$conference_value->create_by_employee_id.")"; 
                                                            }                                                            
                                                        }
                                                    }else{                                                        
                                                        $name= ($conference_value->patient_name == "") ? $conference_value->patient_name : $conference_value->patient_name ;
                                                        echo  $this->lang->line('patient'). " (".$name." : ".$conference_value->patientid.")";                                                        
                                                    }
                                                    ?></td>
                                                <td class="mailbox-name">
                                                    <?php
                                                          $name= ($conference_value->create_for_surname == "") ? $conference_value->create_for_name : $conference_value->create_for_name . " " . $conference_value->create_for_surname;
        echo  $name. " (".$conference_value->for_create_role_name." : ".$conference_value->for_create_employee_id.")";                                                  
                                                    ?>
                                                </td>
                                                <td class="mailbox-name">
                                                     <?php
                                                          $name= ($conference_value->patient_name == "") ? $conference_value->patient_name : $conference_value->patient_name ;
        echo  $name. " (".$this->lang->line('patient')." : ".$conference_value->patientid.")";                                                  
                                                    ?>
                                                </td>
                                              <td class="mailbox-name">
                                                <form class="chgstatus_form" method="POST" action="<?php echo site_url('admin/zoom_conference/changeconsultation')?>">
                                                    <input type="hidden" name="conference_id" value="<?php echo $conference_value->id;?>">
                                                 <select class="form-control chgstatus_dropdown" name="chg_status">
                                                     <option value="0" <?php if($conference_value->status==0) echo "selected='selected'" ?>><?php echo $this->lang->line('awaited'); ?></option>
                                                     <option value="1" <?php if($conference_value->status==1) echo "selected='selected'" ?>><?php echo $this->lang->line('cancelled'); ?> </option>
                                                     <option value="2" <?php if($conference_value->status==2) echo "selected='selected'" ?>><?php echo $this->lang->line('finished'); ?> </option>
                                                 </select>
                                                </form>
                                                </td>
                                                <td class="mailbox-date float-end relative">
                                                    <?php                                                    
                                                if($conference_value->status == 0 && $conference_value->live_consult_link){                                                  
                                                ?>
                                                <a href="<?php echo $return_response->start_url; ?>" class="btn btn-secondary btn-sm starsuccessbtn"  target="_blank" >
                                                <i class="fa fa-sign-in"></i> <?php echo $this->lang->line('start'); ?> 
                                                </a>
                                                <?php
                                                }
                                                     ?>                                                    
                                                    <?php
                                                    if ($conference_value->api_type != 'self') {
                                                        ?>
                                                        <?php 
                                                        if($this->rbac->hasPrivilege('live_classes','can_delete')){                                                            
                                                            if($conference_value->live_consult_link){
                                                                ?>
                                                                <a href="<?php echo base_url(); ?>admin/zoom_conference/delete_consult/<?php echo $conference_value->id . "/" . $return_response->id; ?>"class="btn btn-secondary btn-sm"  data-bs-toggle="tooltip" title="<?php echo $this->lang->line('delete'); ?>" onclick="return confirm('<?php echo $this->lang->line('delete_confirm') ?>');">
                                                                <i class="fa fa-remove"></i>
                                                            </a>
                                                                <?php
                                                            }else{
                                                                ?>
                                                            <button class="btn btn-secondary btn-sm" data-loading-text='<i class="fa fa-circle-o-notch fa-spin"></i>'   id="assign_link" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('add_zoom_link'); ?>" data-link-id="<?php echo $conference_value->id; ?>" ><i class="fa fa-link"></i></button>
                                                                <a href="<?php echo base_url(); ?>admin/zoom_conference/delete_consult/<?php echo $conference_value->id; ?>"class="btn btn-secondary btn-sm"  data-bs-toggle="tooltip" title="<?php echo $this->lang->line('delete'); ?>" onclick="return confirm('<?php echo $this->lang->line('delete_confirm') ?>');">
                                                                <i class="fa fa-remove"></i>
                                                            </a>
                                                                <?php
                                                            }
                                                          
                                                        }
                                                        ?>                                                        
                                                        <?php
                                                    }
                                                    ?>
                                                </td>
                                            </tr>
                                            <?php
                                        }
                                    }
                                    ?>
                                    </tbody>
                                </table>
                            </div> 
                        </div> 
                        <?php }} ?>
                        
                        <div class="tab-pane card" id="prescription">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-hover example">                       
                                    <thead>
                                    <th><?php echo $this->lang->line('opd') . " " . $this->lang->line('id'); ?></th>
                                    <th><?php echo $this->lang->line('appointment') . " " . $this->lang->line('date'); ?></th>
                                    <th><?php echo $this->lang->line('note'); ?></th>
                                    <th class="text-end noExport"><?php echo $this->lang->line('action'); ?></th>
                                    </thead>
                                    <tbody>
                                        <?php
                                        if (!empty($prescription_detail)) {
                                            foreach ($prescription_detail as $prescription_key => $prescription_value) {
                                                ?>  
                                                <tr>
                                                    <td><?php echo $prescription_value["opd_id"] ?></td>
                                                    <td><?php echo !empty($prescription_value["appointment_date"]) ? date($this->customlib->getHospitalDateFormat(true, true), strtotime($prescription_value["appointment_date"])) : ''; ?></td>
                                                    <td><?php echo html_escape($prescription_value["note"]) ?></td>
                                                    <th class="float-end"><a href="#" data-bs-toggle='tooltip' title="<?php echo $this->lang->line('test_report_detail'); ?>" onclick="view_prescription('<?php echo $prescription_value["opd_id"] ?>')"><i class="fa fa-reorder"></i></a></th>
                                                </tr>
                                                <?php
                                            }
                                        }
                                        ?> 
                                    </tbody>
                                </table>
                            </div> 
                        </div>
                        <!-- -->
                    </div>
                </div>
                </div><!-- /.card border0 mb0 -->
                </div><!-- /.card -->
                </form>
            </div>

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
                        <div class="row g-2">
                            <!-- LEFT COLUMN -->
                            <div class="col-lg-7 border-end">
                                <div id="ajax_load"></div>
                                <div class="sh-form-card h-100">
                                    <div class="sh-card-header">
                                        <span class="sh-card-header-title"><?php echo $this->lang->line('visit_details'); ?></span>
                                    </div>
                                    <div class="p-2">
                                        <div class="row g-2">
                                            <div class="col-sm-6">
                                                <label class="form-label"><?php echo $this->lang->line('symptoms_type'); ?></label>
                                                <select name="symptoms_type" id="act" class="form-control form-control-sm select2 act">
                                                    <option value=""><?php echo $this->lang->line('select'); ?></option>
                                                    <?php foreach ($symptomsresulttype as $dkey => $dvalue) { ?>
                                                    <option value="<?php echo $dvalue["id"]; ?>"><?php echo $dvalue["symptoms_type"]; ?></option>
                                                    <?php } ?>
                                                </select>
                                                <span class="text-danger"><?php echo form_error('symptoms_type'); ?></span>
                                            </div>
                                            <div class="col-sm-6">
                                                <label class="form-label"><?php echo $this->lang->line('symptoms'); ?></label>
                                                <div id="dd" class="wrapper-dropdown-3">
                                                    <input class="form-control filterinput" type="text" autocomplete="off">
                                                    <ul class="dropdown scroll150 section_ul">
                                                        <li class="section-placeholder"><span><?php echo $this->lang->line('select'); ?></span></li>
                                                    </ul>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label"><?php echo $this->lang->line('symptoms_description'); ?></label>
                                                <textarea class="form-control form-control-sm" id="symptoms_description" name="symptoms"></textarea>
                                            </div>
                                            <div class="col-sm-6">
                                                <label class="form-label"><?php echo $this->lang->line('icd10_group'); ?></label>
                                                <select id="opd_edit_icd10_group_filter" class="form-control form-control-sm select2">
                                                    <option value=""><?php echo $this->lang->line('select_group'); ?></option>
                                                    <?php if (!empty($icd10_groups)) { foreach ($icd10_groups as $g) { ?>
                                                    <option value="<?php echo $g['id']; ?>"><?php echo html_escape($g['group_name']); ?></option>
                                                    <?php } } ?>
                                                </select>
                                            </div>
                                            <div class="col-sm-6">
                                                <label class="form-label"><?php echo $this->lang->line('icd10_diagnosis'); ?></label>
                                                <select name="icd_code_ids[]" id="opd_edit_icd_code_ids" class="form-control form-control-sm select2" multiple>
                                                    <?php if (!empty($icd10_codes)) { foreach ($icd10_codes as $c) { ?>
                                                    <option value="<?php echo $c['id']; ?>">[<?php echo html_escape($c['icd_code']); ?>] <?php echo html_escape($c['icd_description']); ?></option>
                                                    <?php } } ?>
                                                </select>
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label"><?php echo $this->lang->line('note'); ?></label>
                                                <textarea rows="3" class="form-control form-control-sm" id="edit_revisit_note" name="revisit_note"></textarea>
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label"><?php echo $this->lang->line('any_known_allergies'); ?></label>
                                                <textarea name="known_allergies" rows="3" id="eknown_allergies" class="form-control form-control-sm"><?php echo set_value('address'); ?></textarea>
                                            </div>
                                            <div class="col-12">
                                                <div id="customfield"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div><!-- /col-lg-7 -->
                            <!-- RIGHT COLUMN -->
                            <div class="col-lg-5">
                                <div class="sh-form-card h-100">
                                    <div class="sh-card-header">
                                        <span class="sh-card-header-title"><?php echo $this->lang->line('visit_details'); ?></span>
                                    </div>
                                    <div class="p-2">
                                        <input type="hidden" name="opdid" id="edit_opdid">
                                        <div class="row g-2">
                                            <div class="col-sm-6">
                                                <label class="form-label"><?php echo $this->lang->line('appointment_date'); ?></label><small class="req"> *</small>
                                                <input name="appointment_date" class="form-control form-control-sm datetime" id="appointmentdate" placeholder="" type="text" />
                                                <span class="text-danger"><?php echo form_error('appointment_date'); ?></span>
                                            </div>
                                            <div class="col-sm-6">
                                                <label class="form-label"><?php echo $this->lang->line('case'); ?></label>
                                                <input class="form-control form-control-sm" type="text" name="case" id="edit_case" />
                                                <span class="text-danger"><?php echo form_error('case'); ?></span>
                                            </div>
                                            <div class="col-sm-6">
                                                <label class="form-label"><?php echo $this->lang->line('casualty'); ?></label>
                                                <select name="casualty" id="edit_casualty" class="form-control form-control-sm">
                                                    <?php foreach ($yesno_condition as $yesno_key => $yesno_value) { ?>
                                                    <option value="<?php echo $yesno_key ?>" <?php if ($yesno_key == 'no') { echo "selected"; } ?>><?php echo $yesno_value ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                            <div class="col-sm-6">
                                                <label class="form-label"><?php echo $this->lang->line('old_patient'); ?></label>
                                                <select name="old_patient" id="edit_oldpatient" class="form-control form-control-sm">
                                                    <?php foreach ($yesno_condition as $yesno_key => $yesno_value) { ?>
                                                    <option value="<?php echo $yesno_key ?>"><?php echo $yesno_value ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                            <div class="col-sm-6">
                                                <label class="form-label"><?php echo $this->lang->line('reference'); ?></label>
                                                <input type="text" name="refference" class="form-control form-control-sm" id="edit_refference" />
                                                <span class="text-danger"><?php echo form_error('refference'); ?></span>
                                            </div>
                                            <div class="col-sm-6">
                                                <label class="form-label"><?php echo $this->lang->line('consultant_doctor'); ?></label><small class="req"> *</small>
                                                <select onchange="" name="consultant_doctor" <?php if ($disable_option == true) { echo "disabled"; } ?> class="form-control form-control-sm select2" id="edit_consdoctor">
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
                                            <div class="col-sm-6">
                                                <label class="form-label"><?php echo $this->lang->line('payment_date'); ?></label><small class="req"> *</small>
                                                <input type="text" name="payment_date" id="edit_visit_payment_date" class="form-control form-control-sm datetime" autocomplete="off">
                                                <input type="hidden" id="edit_visit_payment_id" name="edit_payment_id">
                                            </div>
                                            <div class="col-sm-6">
                                                <label class="form-label"><?php echo $this->lang->line('amount') . " (" . $currency_symbol . ")"; ?></label><small class="req"> *</small>
                                                <input type="text" name="amount" id="edit_visit_payment" class="form-control form-control-sm" value="">
                                            </div>
                                            <div class="col-sm-6">
                                                <label class="form-label"><?php echo $this->lang->line('payment_mode'); ?></label>
                                                <select class="form-control form-control-sm visit_payment_mode" name="payment_mode" id="visit_payment_mode">
                                                    <?php foreach ($payment_mode as $key => $value) { ?>
                                                    <option value="<?php echo $key ?>" <?php if ($key == 'cash') { echo "selected"; } ?>><?php echo $value ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                            <div class="col-sm-6">
                                                <label class="form-label"><?php echo $this->lang->line('payment_note'); ?></label>
                                                <input type="text" name="note" id="edit_visit_payment_note" class="form-control form-control-sm" />
                                            </div>
                                            <div class="col-sm-6 cheque_div" style="display:none;">
                                                <label class="form-label"><?php echo $this->lang->line('cheque_no'); ?></label><small class="req"> *</small>
                                                <input type="text" name="cheque_no" id="edit_visit_cheque_no" class="form-control form-control-sm">
                                            </div>
                                            <div class="col-sm-6 cheque_div" style="display:none;">
                                                <label class="form-label"><?php echo $this->lang->line('cheque_date'); ?></label><small class="req"> *</small>
                                                <input type="text" name="cheque_date" id="edit_visit_cheque_date" class="form-control form-control-sm date">
                                            </div>
                                            <div class="col-12 cheque_div" style="display:none;">
                                                <label class="form-label"><?php echo $this->lang->line('attach_document'); ?></label>
                                                <input type="file" class="filestyle form-control form-control-sm" name="document">
                                                <span class="text-danger"><?php echo form_error('document'); ?></span>
                                            </div>
                                            <?php if ($result['gender'] == 'Female') { if ($this->rbac->hasPrivilege('opd_antenatal', 'can_view')) { ?>
                                            <div class="col-sm-6" id="antenatal_div">
                                                <label class="form-label"><input type="checkbox" class="align-top" name="is_for_antenatal" id="edit_is_for_antenatal"> <?php echo $this->lang->line('is_antenatal'); ?></label>
                                            </div>
                                            <?php } } ?>
                                        </div><!-- /row g-2 -->
                                    </div><!-- /p-2 -->
                                </div><!-- /sh-form-card -->
                            </div><!-- /col-lg-5 -->
                        </div><!-- /row g-2 -->
                    </div><!-- /modal-body -->
                </div><!-- /pup-scroll-area -->

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
                <input type="hidden" name="insurance_validity" id="editinsurance_validity" value="<?php echo $result['insurance_validity'] ?>">
                <input type="hidden" name="insurance_id" id="editinsurance_id" value="<?php echo $result['insurance_id'] ?>">
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
                                        <select name="charge_type" id="editcharge_type" class="form-control form-control-sm editcharge_type charge_type select2">
                                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                                            <?php foreach ($charge_type as $key => $value) { ?>
                                            <option value="<?php echo $value->id; ?>"><?php echo $value->charge_type; ?></option>
                                            <?php } ?>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('charge_type'); ?></span>
                                    </div>
                                    <div class="col-sm-2">
                                        <label class="form-label"><?php echo $this->lang->line('charge_category'); ?></label><small class="req"> *</small>
                                        <select name="charge_category" id="editcharge_category" class="form-control form-control-sm select2 editcharge_category">
                                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('charge_category'); ?></span>
                                    </div>
                                    <div class="col-sm-2">
                                        <label class="form-label"><?php echo $this->lang->line('charge_name'); ?></label><small class="req"> *</small>
                                        <select name="charge_id" id="editcharge_id" class="form-control form-control-sm editcharge select2">
                                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('code'); ?></span>
                                    </div>
                                    <div class="col-sm-2">
                                        <label class="form-label"><?php echo $this->lang->line('standard_charge') . " (" . $currency_symbol . ")"; ?></label>
                                        <input type="text" readonly name="standard_charge" id="editstandard_charge" class="form-control form-control-sm standard_charge" value="<?php echo set_value('standard_charge'); ?>">
                                        <span class="text-danger"><?php echo form_error('standard_charge'); ?></span>
                                    </div>
                                    <div class="col-sm-2">
                                        <label class="form-label"><?php echo $this->lang->line('tpa_charge') . " (" . $currency_symbol . ")"; ?></label>
                                        <input type="text" readonly name="schedule_charge" id="editscd_charge" class="form-control form-control-sm schedule_charge" value="<?php echo set_value('schedule_charge'); ?>">
                                        <span class="text-danger"><?php echo form_error('schedule_charge'); ?></span>
                                    </div>
                                    <div class="col-sm-2">
                                        <label class="form-label"><?php echo $this->lang->line('qty'); ?></label><small class="req"> *</small>
                                        <input type="text" name="qty" id="editqty" class="form-control form-control-sm qty" value="1">
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
                                                <input type="text" name="editdiscount_percentage" id="editdiscount_percentage" class="form-control text-end editdiscount_percentage discount_percentage_add_charge">
                                                <span class="input-group-text">%</span>
                                            </div>
                                            <input type="text" value="0" onkeyup="update_percentage(this.value)" placeholder="<?php echo $this->lang->line('discount'); ?>" name="discount_percentage" id="editdiscount" class="form-control form-control-sm text-end discount discount_percentage_amount sh-bill-summary-input">
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
                    <button type="submit" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i>" name="charge_data" class="btn btn-info"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
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
                                        <select name="charge_type" id="add_charge_type" class="form-control form-control-sm charge_type select2 reset_value">
                                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                                            <?php foreach ($charge_type as $key => $value) { ?>
                                            <option value="<?php echo $value->id; ?>"><?php echo $value->charge_type; ?></option>
                                            <?php } ?>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('charge_type'); ?></span>
                                    </div>
                                    <div class="col-sm-2">
                                        <label class="form-label"><?php echo $this->lang->line('charge_category'); ?></label><small class="req"> *</small>
                                        <select name="charge_category" id="charge_category" class="form-control form-control-sm select2 charge_category reset_value">
                                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('charge_category'); ?></span>
                                    </div>
                                    <div class="col-sm-2">
                                        <label class="form-label"><?php echo $this->lang->line('charge_name'); ?></label><small class="req"> *</small>
                                        <select name="charge_id" id="charge_id" class="form-control form-control-sm addcharge select2 reset_value">
                                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('code'); ?></span>
                                    </div>
                                    <div class="col-sm-2">
                                        <label class="form-label"><?php echo $this->lang->line('standard_charge') . " (" . $currency_symbol . ")"; ?></label>
                                        <input type="text" readonly name="standard_charge" id="addstandard_charge" class="form-control form-control-sm reset_value standard_charge" value="<?php echo set_value('standard_charge'); ?>">
                                        <span class="text-danger"><?php echo form_error('standard_charge'); ?></span>
                                    </div>
                                    <div class="col-sm-2">
                                        <label class="form-label"><?php echo $this->lang->line('tpa_charge') . " (" . $currency_symbol . ")"; ?></label>
                                        <input type="text" readonly name="schedule_charge" id="addscd_charge" class="form-control form-control-sm reset_value schedule_charge" value="<?php echo set_value('schedule_charge'); ?>">
                                        <span class="text-danger"><?php echo form_error('schedule_charge'); ?></span>
                                    </div>
                                    <div class="col-sm-2">
                                        <label class="form-label"><?php echo $this->lang->line('qty'); ?></label><small class="req"> *</small>
                                        <input type="text" name="qty" id="qty" class="form-control form-control-sm qty" value="1">
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
                                                <textarea name="note" id="edit_note" rows="3" class="form-control form-control-sm edit_charge_note"></textarea>
                                            </div>
                                            <div class="col-12 text-end">
                                                <button type="submit" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i>" name="charge_data" value="add" class="btn btn-info btn-sm"><i class="fa fa-plus me-1"></i><?php echo $this->lang->line('add'); ?></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div><!--./d-flex-->

                        <!-- Preview charges table -->
                        <div class="sh-form-card mt-3">
                            <div class="p-0">
                                <div class="table-responsive sh-charge-preview-scroll">
                                    <table class="table table-sm table-striped table-bordered mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th><?php echo $this->lang->line('date'); ?></th>
                                                <th><?php echo $this->lang->line('charge_type'); ?></th>
                                                <th><?php echo $this->lang->line('charge_category'); ?></th>
                                                <th><?php echo $this->lang->line('charge_name'); ?> / <br><?php echo $this->lang->line('charge_note'); ?></th>
                                                <th class="text-end"><?php echo $this->lang->line('standard_charge') . ' (' . $currency_symbol . ')'; ?></th>
                                                <th class="text-end"><?php echo $this->lang->line('tpa_charge') . ' (' . $currency_symbol . ')'; ?></th>
                                                <th class="text-end"><?php echo $this->lang->line('qty'); ?></th>
                                                <th class="text-end"><?php echo $this->lang->line('total') . ' (' . $currency_symbol . ')'; ?></th>
                                                <th class="text-end"><?php echo $this->lang->line('discount') . ' (' . $currency_symbol . ')'; ?></th>
                                                <th class="text-end"><?php echo $this->lang->line('tax') . ' (' . $currency_symbol . ')'; ?></th>
                                                <th class="text-end"><?php echo $this->lang->line('net_amount') . ' (' . $currency_symbol . ')'; ?></th>
                                                <th class="text-end"><?php echo $this->lang->line('action'); ?></th>
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
                    <button type="submit" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i>" value="save" name="charge_data" class="btn btn-info"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- -->

<!-- Add Operation Theatre -->
<div class="modal fade sh-modal sh-modal-accent" id="add_operationtheatre" tabindex="-1" aria-labelledby="add_operationtheatreLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="add_operationtheatreLabel"><?php echo $this->lang->line("add_operation"); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="form_operationtheatre" accept-charset="utf-8" enctype="multipart/form-data" method="post">
                <input type="hidden" value="<?php echo $opdid ?>" name="opdid" id="opdid">
                <div class="pup-scroll-area">
                    <div class="modal-body modal-background">
                        <div class="sh-form-card mb-3">
                            <div class="sh-card-header">
                                <span class="sh-card-header-title"><?php echo $this->lang->line('add_operation'); ?></span>
                            </div>
                            <div class="p-2">
                                <div class="row g-2">
                                    <div class="col-sm-6">
                                        <label class="form-label"><?php echo $this->lang->line('operation_category'); ?></label><small class="req"> *</small>
                                        <select name="operation_category" id="operation_category" class="form-control form-control-sm select2" onchange="getcategory(this.value)">
                                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                                            <?php foreach ($categorylist as $operation) { ?>
                                            <option value="<?php echo $operation['id']; ?>"><?php echo $operation['category']; ?></option>
                                            <?php } ?>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('operation_category'); ?></span>
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="form-label"><?php echo $this->lang->line('operation_name'); ?></label><small class="req"> *</small>
                                        <select name="operation_name" id="operation_name" class="form-control form-control-sm select2">
                                        </select>
                                        <span class="text-danger"><?php echo form_error('operation_name'); ?></span>
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="form-label"><?php echo $this->lang->line('operation_date'); ?></label><small class="req"> *</small>
                                        <input type="text" value="" id="date" name="date" class="form-control form-control-sm datetime">
                                        <span class="text-danger"><?php echo form_error('date'); ?></span>
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="form-label"><?php echo $this->lang->line('consultant_doctor'); ?></label><small class="req"> *</small>
                                        <select class="form-control form-control-sm select2" <?php if ($disable_option == true) { echo "disabled"; } ?> id="consultant_doctorid" name="consultant_doctor">
                                            <option value="<?php echo set_value('consultant_doctor'); ?>"><?php echo $this->lang->line('select'); ?></option>
                                            <?php foreach ($doctors as $dkey => $dvalue) { ?>
                                            <option value="<?php echo $dvalue["id"]; ?>" <?php if ((isset($doctor_select)) && ($doctor_select == $dvalue["id"])) { echo "selected"; } ?>><?php echo composeStaffNameByString($dvalue["name"], $dvalue["surname"], $dvalue["employee_id"]); ?></option>
                                            <?php } ?>
                                        </select>
                                        <input type="hidden" id="consultant_doctorname" name="consultant_doctor">
                                        <span class="text-danger"><?php echo form_error('consultant_doctor'); ?></span>
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="form-label"><?php echo $this->lang->line('assistant_consultant') . " 1"; ?></label>
                                        <input type="text" name="ass_consultant_1" class="form-control form-control-sm">
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="form-label"><?php echo $this->lang->line('assistant_consultant') . " 2"; ?></label>
                                        <input type="text" name="ass_consultant_2" class="form-control form-control-sm">
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="form-label"><?php echo $this->lang->line('anesthetist'); ?></label>
                                        <input type="text" name="anesthetist" class="form-control form-control-sm">
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="form-label"><?php echo $this->lang->line('anesthesia_type'); ?></label>
                                        <input type="text" name="anaethesia_type" class="form-control form-control-sm">
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="form-label"><?php echo $this->lang->line('ot_technician'); ?></label>
                                        <input type="text" name="ot_technician" class="form-control form-control-sm">
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="form-label"><?php echo $this->lang->line('ot_assistant'); ?></label>
                                        <input type="text" name="ot_assistant" class="form-control form-control-sm">
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="form-label"><?php echo $this->lang->line('remark'); ?></label>
                                        <textarea name="ot_remark" id="ot_remark" class="form-control form-control-sm"></textarea>
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="form-label"><?php echo $this->lang->line('result'); ?></label>
                                        <textarea name="ot_result" id="ot_result" class="form-control form-control-sm"></textarea>
                                    </div>
                                    <div class="col-12">
                                        <?php echo display_custom_fields('operationtheatre'); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                    <button type="submit" id="form_operationtheatrebtn" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i>" class="btn btn-info"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Edit Operation Theatre -->

<div class="modal fade sh-modal sh-modal-accent" id="edit_operationtheatre" tabindex="-1" aria-labelledby="edit_operationtheatreLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="edit_operationtheatreLabel"><?php echo $this->lang->line("edit_operation"); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="form_editoperationtheatre" accept-charset="utf-8" enctype="multipart/form-data" method="post">
                <input type="hidden" value="<?php echo $opdid ?>" name="opdid" id="opdid">
                <input type="hidden" value="" name="otid" id="otid">
                <div class="pup-scroll-area">
                    <div class="modal-body modal-background">
                        <div class="sh-form-card mb-3">
                            <div class="sh-card-header">
                                <span class="sh-card-header-title"><?php echo $this->lang->line('edit_operation'); ?></span>
                            </div>
                            <div class="p-2">
                                <div class="row g-2">
                                    <div class="col-sm-6">
                                        <label class="form-label"><?php echo $this->lang->line('operation_category'); ?></label><small class="req"> *</small>
                                        <select name="eoperation_category" id="eoperation_category" class="form-control form-control-sm select2" onchange="getcategory(this.value)">
                                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                                            <?php foreach ($categorylist as $operation) { ?>
                                            <option value="<?php echo $operation['id']; ?>"><?php echo $operation['category']; ?></option>
                                            <?php } ?>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('operation_category'); ?></span>
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="form-label"><?php echo $this->lang->line('operation_name'); ?></label><small class="req"> *</small>
                                        <select name="eoperation_name" id="eoperation_name" class="form-control form-control-sm select2">
                                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                                            <?php foreach ($operationlist as $operation) { ?>
                                            <option value="<?php echo $operation['id']; ?>"><?php echo $operation['operation']; ?></option>
                                            <?php } ?>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('operation_name'); ?></span>
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="form-label"><?php echo $this->lang->line('operation_date'); ?></label><small class="req"> *</small>
                                        <input type="text" value="" id="edate" name="date" class="form-control form-control-sm datetime">
                                        <span class="text-danger"><?php echo form_error('date'); ?></span>
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="form-label"><?php echo $this->lang->line('consultant_doctor'); ?></label><small class="req"> *</small>
                                        <select class="form-control form-control-sm select2" <?php if ($disable_option == true) { echo "disabled"; } ?> id="econsultant_doctorid" name="consultant_doctor">
                                            <option value="<?php echo set_value('consultant_doctor'); ?>"><?php echo $this->lang->line('select'); ?></option>
                                            <?php foreach ($doctors as $dkey => $dvalue) { ?>
                                            <option value="<?php echo $dvalue["id"]; ?>" <?php if ((isset($doctor_select)) && ($doctor_select == $dvalue["id"])) { echo "selected"; } ?>><?php echo composeStaffNameByString($dvalue["name"], $dvalue["surname"], $dvalue["employee_id"]); ?></option>
                                            <?php } ?>
                                        </select>
                                        <input type="hidden" id="econsultant_doctorname" name="consultant_doctor">
                                        <span class="text-danger"><?php echo form_error('consultant_doctor'); ?></span>
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="form-label"><?php echo $this->lang->line('assistant_consultant') . " 1"; ?></label>
                                        <input type="text" name="ass_consultant_1" id="eass_consultant_1" class="form-control form-control-sm">
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="form-label"><?php echo $this->lang->line('assistant_consultant') . " 2"; ?></label>
                                        <input type="text" name="ass_consultant_2" id="eass_consultant_2" class="form-control form-control-sm">
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="form-label"><?php echo $this->lang->line('anesthetist'); ?></label>
                                        <input type="text" name="anesthetist" id="eanesthetist" class="form-control form-control-sm">
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="form-label"><?php echo $this->lang->line('anesthesia_type'); ?></label>
                                        <input type="text" name="anaethesia_type" id="eanaethesia_type" class="form-control form-control-sm">
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="form-label"><?php echo $this->lang->line('ot_technician'); ?></label>
                                        <input type="text" name="ot_technician" id="eot_technician" class="form-control form-control-sm">
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="form-label"><?php echo $this->lang->line('ot_assistant'); ?></label>
                                        <input type="text" value="" name="ot_assistant" id="eot_assistant" class="form-control form-control-sm">
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="form-label"><?php echo $this->lang->line('remark'); ?></label>
                                        <textarea name="eot_remark" id="eot_remark" class="form-control form-control-sm"></textarea>
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="form-label"><?php echo $this->lang->line('result'); ?></label>
                                        <textarea name="eot_result" id="eot_result" class="form-control form-control-sm"></textarea>
                                    </div>
                                    <div id="custom_fields_ot" class="col-12">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                    <button type="submit" id="form_editoperationtheatrebtn" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i>" class="btn btn-info"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade sh-modal sh-modal-accent" id="myaddMedicationModal" tabindex="-1" aria-labelledby="myaddMedicationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myaddMedicationModalLabel"><?php echo $this->lang->line("add_medication_dose"); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="add_medication" accept-charset="utf-8" method="post">
                <div class="pup-scroll-area">
                    <div class="modal-body">
                        <div class="sh-form-card">
                            <div class="sh-card-header">
                                <span class="sh-card-header-title"><?php echo $this->lang->line('medication'); ?></span>
                            </div>
                            <div class="p-3">
                                <input type="hidden" name="opdid" value="<?php echo $opdid ?>">
                                <div class="row">
                                    <div class="mb-3 col-md-6">
                                        <label class="form-label"><?php echo $this->lang->line('date'); ?></label><small class="req"> *</small>
                                        <input type="text" name="date" id="date" class="form-control date">
                                        <span class="text-danger"><?php echo form_error('date'); ?></span>
                                    </div>
                                    <div class="mb-3 col-md-6">
                                        <label class="form-label"><?php echo $this->lang->line("time"); ?></label><small class="req"> *</small>
                                        <div class="bootstrap-timepicker">
                                            <div class="input-group">
                                                <input type="text" name="time" class="form-control timepicker" id="mtime" value="<?php echo set_value('time'); ?>">
                                                <div class="input-group-text">
                                                    <i class="fa fa-clock-o"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <span class="text-danger"><?php echo form_error('time'); ?></span>
                                    </div>
                                    <div class="mb-3 col-md-6">
                                        <label class="form-label"><?php echo $this->lang->line("medicine_category"); ?></label><small class="req"> *</small>
                                        <select class="form-control medicine_category_medication select2" id="mmedicine_category_id" name='medicine_category_id'>
                                            <option value="<?php echo set_value('medicine_category_id'); ?>"><?php echo $this->lang->line('select') ?></option>
                                            <?php foreach ($medicineCategory as $dkey => $dvalue) { ?>
                                            <option value="<?php echo $dvalue["id"]; ?>"><?php echo $dvalue["medicine_category"] ?></option>
                                            <?php } ?>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('medicine_category_id'); ?></span>
                                    </div>
                                    <div class="mb-3 col-md-6">
                                        <label class="form-label"><?php echo $this->lang->line("medicine_name"); ?></label><small class="req"> *</small>
                                        <select class="form-control select2 medicine_name_medication" id="mmedicine_id" name='medicine_name_id'>
                                            <option value=""><?php echo $this->lang->line('select') ?></option>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('medicine_name_id'); ?></span>
                                    </div>
                                    <div class="mb-3 col-md-6">
                                        <label class="form-label"><?php echo $this->lang->line("dosage"); ?></label><small class="req"> *</small>
                                        <select class="form-control select2 dosage_medication" id="dosage" onchange="get_dosagename(this.value)" name='dosage'>
                                            <option value=""><?php echo $this->lang->line('select') ?></option>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('dosage'); ?></span>
                                    </div>
                                    <div class="mb-3 col-12">
                                        <label class="form-label"><?php echo $this->lang->line("remarks"); ?></label>
                                        <textarea name="remark" id="remark" class="form-control"></textarea>
                                    </div>
                                </div>
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
<!-- -->
<!-- -->
<div class="modal fade sh-modal sh-modal-accent" id="myMedicationModal" tabindex="-1" aria-labelledby="myMedicationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myMedicationModalLabel"><?php echo $this->lang->line("add_medication_dose"); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="add_medicationdose" accept-charset="utf-8" method="post">
                <input type="hidden" name="opdid" value="<?php echo $opdid ?>">
                <div class="pup-scroll-area">
                    <div class="modal-body modal-background">
                        <div class="sh-form-card mb-3">
                            <div class="sh-card-header">
                                <span class="sh-card-header-title"><?php echo $this->lang->line('add_medication_dose'); ?></span>
                            </div>
                            <div class="p-2">
                                <div class="row g-2">
                                    <div class="col-md-6">
                                        <label class="form-label"><?php echo $this->lang->line('date'); ?></label><small class="req"> *</small>
                                        <input type="text" name="date" id="add_dose_date" class="form-control form-control-sm date">
                                        <span class="text-danger"><?php echo form_error('date'); ?></span>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label"><?php echo $this->lang->line('time'); ?></label><small class="req"> *</small>
                                        <div class="input-group input-group-sm bootstrap-timepicker">
                                            <input type="text" name="time" class="form-control form-control-sm timepicker" id="add_dose_time" value="<?php echo set_value('time'); ?>">
                                            <div class="input-group-text"><i class="fa fa-clock-o"></i></div>
                                        </div>
                                        <span class="text-danger"><?php echo form_error('time'); ?></span>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label"><?php echo $this->lang->line('medicine_category'); ?></label><small class="req"> *</small>
                                        <select class="form-control form-control-sm medicine_category_medication select2" id="add_dose_medicine_category" name="medicine_category_id">
                                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                                            <?php foreach ($medicineCategory as $dkey => $dvalue) { ?>
                                            <option value="<?php echo $dvalue["id"]; ?>"><?php echo $dvalue["medicine_category"]; ?></option>
                                            <?php } ?>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('medicine_category_id'); ?></span>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label"><?php echo $this->lang->line('medicine_name'); ?></label><small class="req"> *</small>
                                        <select class="form-control form-control-sm select2 medicine_name_medication" id="add_dose_medicine_id" name="medicine_name_id">
                                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('medicine_name_id'); ?></span>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label"><?php echo $this->lang->line('dosage'); ?></label><small class="req"> *</small>
                                        <select class="form-control form-control-sm select2 dosage_medication" id="mdosage" name="dosage">
                                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('dosage'); ?></span>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label"><?php echo $this->lang->line('remarks'); ?></label>
                                        <textarea name="remark" id="remark" class="form-control form-control-sm"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                    <button type="submit" id="add_medicationdosebtn" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i>" class="btn btn-info"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- -->

<!-- -->
<div class="modal fade sh-modal sh-modal-accent" id="myMedicationDoseModal" tabindex="-1" aria-labelledby="myMedicationDoseModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myMedicationDoseModalLabel"><?php echo $this->lang->line("edit_medication_dose"); ?></h5>
                <?php if ($this->rbac->hasPrivilege('opd_medication', 'can_delete')) { ?>
                <div id='edit_delete_medication' class="d-flex align-items-center gap-2 ms-auto me-2"></div>
                <?php } ?>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="update_medication" accept-charset="utf-8" method="post">
                <div class="pup-scroll-area">
                    <div class="modal-body">
                        <div class="sh-form-card">
                            <div class="sh-card-header">
                                <span class="sh-card-header-title"><?php echo $this->lang->line("medication"); ?></span>
                            </div>
                            <div class="p-3">
                                <input type="hidden" name="medication_id" id="medication_id" value="">
                                <input type="hidden" name="opdid" value="<?php echo $opdid ?>">
                                <div class="row">
                                    <div class="mb-3 col-md-6">
                                        <label class="form-label"><?php echo $this->lang->line('date'); ?></label><small class="req"> *</small>
                                        <input type="text" name="date" id="date_edit_medication" class="form-control date">
                                        <span class="text-danger"><?php echo form_error('date'); ?></span>
                                    </div>
                                    <div class="mb-3 col-md-6">
                                        <label class="form-label"><?php echo $this->lang->line("time"); ?></label><small class="req"> *</small>
                                        <div class="bootstrap-timepicker">
                                            <div class="input-group">
                                                <input type="text" name="time" class="form-control timepicker" id="dosagetime" value="<?php echo set_value('time'); ?>">
                                                <div class="input-group-text">
                                                    <i class="fa fa-clock-o"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <span class="text-danger"><?php echo form_error('time'); ?></span>
                                    </div>
                                    <div class="mb-3 col-md-6">
                                        <label class="form-label"><?php echo $this->lang->line("medicine_category"); ?></label><small class="req"> *</small>
                                        <select class="form-control medicine_category_medication select2" id="mmedicine_category_edit_id" name='medicine_category_id'>
                                            <option value="<?php echo set_value('medicine_category_id'); ?>"><?php echo $this->lang->line('select') ?></option>
                                            <?php foreach ($medicineCategory as $dkey => $dvalue) { ?>
                                            <option value="<?php echo $dvalue["id"]; ?>"><?php echo $dvalue["medicine_category"] ?></option>
                                            <?php } ?>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('medicine_category_id'); ?></span>
                                    </div>
                                    <div class="mb-3 col-md-6">
                                        <label class="form-label"><?php echo $this->lang->line("medicine_name"); ?></label><small class="req"> *</small>
                                        <select class="form-control select2 medicine_name_medication" id="mmedicine_edit_id" name='medicine_name_id'>
                                            <option value=""><?php echo $this->lang->line('select') ?></option>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('medicine_name_id'); ?></span>
                                    </div>
                                    <div class="mb-3 col-md-6">
                                        <label class="form-label"><?php echo $this->lang->line("dosage"); ?></label><small class="req"> *</small>
                                        <select class="form-control select2" id="medicine_dose_edit_id" name='dosage_id'>
                                            <option value="<?php echo set_value('dosage_id'); ?>"><?php echo $this->lang->line('select'); ?></option>
                                            <?php foreach ($dosage as $key => $value) { ?>
                                            <option value="<?php echo $value["id"]; ?>"><?php echo $value["dosage"]." ".$value['unit']; ?></option>
                                            <?php } ?>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('dosage_id'); ?></span>
                                    </div>
                                    <div class="mb-3 col-12">
                                        <label class="form-label"><?php echo $this->lang->line("remarks"); ?></label>
                                        <textarea name="remark" id="medicine_dosage_remark" class="form-control"></textarea>
                                    </div>
                                </div>
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
<!-- -->

<!--lab investigation modal-->
<div class="modal fade sh-modal sh-modal-accent" id="viewDetailReportModal" tabindex="-1" aria-labelledby="viewDetailReportModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewDetailReportModalLabel" id="modal_head"><?php echo $this->lang->line('lab_investigation_details'); ?></h5>
                 <div id='action_detail_report_modal'></div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="pup-scroll-area">
                <div class="modal-body modal-background " >
                    <div id="reportbilldata"></div>
                </div>
            </div>
              <div class="modal-footer">
                  <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
              </div>
        </div>
    </div>
</div>
<!-- end lab investigation modal-->

<!-- Timeline -->
<div class="modal fade sh-modal sh-modal-accent" id="myTimelineModal" tabindex="-1" aria-labelledby="myTimelineModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myTimelineModalLabel"><?php echo $this->lang->line('add_timeline'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="pup-scroll-area">
            <div class="modal-body modal-background " >

                          <div class="row  sh-form-card h-100 mt5">
                              <div class="sh-card-header">
                             <span class="sh-card-header-title"><?php echo $this->lang->line('timeline'); ?></span>
                          </div>
                    <div class="col-lg-12 col-md-12 col-sm-12">
                        <form id="add_timeline" accept-charset="utf-8" enctype="multipart/form-data" method="post">
                                <div class="row">
                                    <div class=" col-md-12">
                                        <div class="mb-3">
                                            <label><?php echo $this->lang->line('title'); ?></label><small class="req"> *</small>
                                            <input type="hidden" name="patient_id" id="patient_id" value="<?php echo $result['patient_id'] ?>">
                                            <input id="timeline_title" name="timeline_title" placeholder="" type="text" class="form-control"  />
                                            <span class="text-danger"><?php echo form_error('timeline_title'); ?></span>
                                        </div>
                                        <div class="mb-3">
                                            <label><?php echo $this->lang->line('date'); ?></label><small class="req"> *</small>
                                            <input id="timeline_date" name="timeline_date" value="<?php echo set_value('timeline_date', date($this->customlib->getHospitalDateFormat(true,true))); ?>" placeholder="" type="text" class="form-control datetime"  />
                                            <span class="text-danger"><?php echo form_error('timeline_date'); ?></span>
                                        </div>
                                        <div class="mb-3">
                                            <label><?php echo $this->lang->line('description'); ?></label>
                                            <textarea id="timeline_desc" name="timeline_desc" placeholder=""  class="form-control"></textarea>
                                            <span class="text-danger"><?php echo form_error('description'); ?></span>
                                        </div>
                                        <div class="mb-3">
                                            <label><?php echo $this->lang->line('attach_document'); ?></label>
                                            <div><input id="timeline_doc_id" name="timeline_doc" placeholder="" type="file"  class="filestyle form-control" data-height="40"  value="<?php echo set_value('timeline_doc'); ?>" /><span class="text-danger"><?php echo form_error('timeline_doc'); ?></span></div>
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
    <div class="modal-dialog modal-dialog-centered  modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myTimelineEditModalLabel"><?php echo $this->lang->line('edit_timeline'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="pup-scroll-area">
                <div class="modal-body modal-background ">
                 <div class="row  sh-form-card h-100 mt5">
                              <div class="sh-card-header">
                             <span class="sh-card-header-title"><?php echo $this->lang->line('timeline'); ?></span>
                         </div>
                    <div class="col-lg-12 col-md-12 col-sm-12">
                        <form id="edit_timeline" accept-charset="utf-8" enctype="multipart/form-data" method="post">
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
                                            <input type="text" name="timeline_date" class="form-control datetime" id="etimelinedate"/>
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
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('close'); ?></button>
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
                <div class="modal-body">
                </div><!--./modal-body-->
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                    <button type="submit" name="save_print" value="save_print" data-loading-text="<?php echo $this->lang->line('processing'); ?>" class="btn btn-info"><i class="fa fa-print"></i> <?php echo $this->lang->line('save_print'); ?></button>
                    <button type="submit" name="save" value="save" class="btn btn-info" id="form_prescriptionbtn" data-loading-text="<?php echo $this->lang->line('processing') ?>"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
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
                <div id='edit_delete' class="d-flex align-items-center gap-2 ms-auto me-2">
                <?php if ($this->rbac->hasPrivilege('revisit', 'can_edit')) { ?>
                    <a href="javascript:void(0)" class="btn btn-sm btn-light" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('edit'); ?>"><i class="fa fa-pencil"></i></a>
                <?php } if ($this->rbac->hasPrivilege('revisit', 'can_delete')) { ?>
                    <a href="#" data-bs-toggle="tooltip"  class="btn btn-sm btn-light" title="<?php echo $this->lang->line('delete'); ?>"><i class="fa fa-trash"></i></a>
                <?php } ?>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="pup-scroll-area">
                <div class="modal-body">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('close'); ?></button>
            </div>
        </div>
    </div>
</div>

<!-- -->
<div class="modal fade sh-modal sh-modal-accent" id="prescriptionview" tabindex="-1" aria-labelledby="prescriptionviewLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="prescriptionviewLabel"><?php echo $this->lang->line('prescription'); ?></h5>
                <div id='edit_deleteprescription' class="d-flex align-items-center gap-2 ms-auto me-2"></div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="pup-scroll-area">
                <div class="modal-body" id="getdetails_prescription">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('close'); ?></button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade sh-modal sh-modal-accent" id="prescriptionviewmanual" tabindex="-1" aria-labelledby="prescriptionviewmanualLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="prescriptionviewmanualLabel"><?php echo $this->lang->line('prescription'); ?></h5>
                <div id='edit_deleteprescriptionmanual'></div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="pup-scroll-area">
                <div class="modal-body modal-text-white" id="getdetails_prescriptionmanual">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('close'); ?></button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade sh-modal sh-modal-accent" id="myModaledit" tabindex="-1" aria-labelledby="myModaleditLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myModaleditLabel"><?php echo $this->lang->line('patient_details'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formeditpa" accept-charset="utf-8" action="" enctype="multipart/form-data" method="post">
                <div class="modal-body">
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
                                                        <label ><?php echo $this->lang->line('date_of_birth'); ?></label> 
                                                        <input type="text" name="dob" id="birth_date" placeholder=""  class="form-control date patient_dob" /><?php echo set_value('dob'); ?>
                                                    </div>
                                                </div> 
                                                <div class="col-sm-5" id="calculate">
                                                    <div class="mb-3">
                                                        <label><?php echo $this->lang->line('age').' ('.$this->lang->line('yy_mm_dd').')'; ?> </label><small class="req"> *</small> 
                                                        <div class="sh-age-input-row-3">
                                                            <input type="text" placeholder="<?php echo $this->lang->line('year'); ?>" name="age[year]" id="age_year" value="" class="form-control patient_age_year age-y">
                                                            <input type="text" id="age_month" placeholder="<?php echo $this->lang->line('month'); ?>" name="age[month]" value="" class="form-control patient_age_month age-m">
                                                            <input type="text" id="age_day" placeholder="<?php echo $this->lang->line('day'); ?>" name="age[day]" value="" class="form-control patient_age_day age-d">
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
                                                            <?php foreach ($bloodgroup as $key => $value) {  ?>
                                                            <option value="<?php echo $key; ?>" <?php if (set_value('blood_group') == $key) { echo "selected"; } ?>><?php echo $value; ?></option>
                                                            <?php  }  ?>
                                                        </select>
                                                        <span class="text-danger"><?php echo form_error('blood_group'); ?></span>
                                                    </div>
                                                </div>
                                                <div class="col-sm-3">
                                                    <div class="mb-3">
                                                        <label ><?php echo $this->lang->line('marital_status'); ?></label>
                                                        <select name="marital_status" id="marital_statuss" class="form-control">
                                                            <option value=""><?php echo $this->lang->line('select') ?></option>
                                                            <?php foreach ($marital_status as $key => $value) {  ?>
                                                            <option value="<?php echo $value; ?>" <?php if (set_value('marital_status') == $key) echo "selected"; ?>><?php echo $value; ?></option>
                                                            <?php } ?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="mb-3">
                                                        <label ><?php echo $this->lang->line('patient') . " " . $this->lang->line('photo'); ?></label>
                                                        <div><input class="filestyle form-control-file" type='file' name='file' id="exampleInputFile" size='20' data-height="26" data-default-file="<?php echo base_url() ?>uploads/patient_images/no_image.png" ></div>
                                                        <span class="text-danger"><?php echo form_error('file'); ?></span>
                                                    </div>
                                                </div>
                                            </div> 
                                        </div><!--./col-md-6-->
                                        <div class="col-sm-3">
                                            <div class="mb-3">
                                                <label ><?php echo $this->lang->line('phone'); ?></label>
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
                                                <label ><?php echo $this->lang->line('address'); ?></label> 
                                                <input name="address" id="eaddress" placeholder="" class="form-control" /><?php echo set_value('address'); ?>
                                            </div> 
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="mb-3">
                                                <label ><?php echo $this->lang->line('remarks'); ?></label> 
                                                <textarea name="note" id="enote" class="form-control" ><?php echo set_value('note'); ?></textarea>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="mb-3">
                                                <label ><?php echo $this->lang->line('any_known_allergies'); ?></label> 
                                                <textarea name="known_allergies" id="eknown_allergies" placeholder="" class="form-control" ><?php echo set_value('address'); ?></textarea>
                                            </div> 
                                        </div> 
                                    <div id="customfieldpatient" >
                                        
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

<!-- discharged summary   -->
<div class="modal fade sh-modal sh-modal-accent" id="myModaldischarged" tabindex="-1" aria-labelledby="myModaldischargedLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myModaldischargedLabel"><?php echo $this->lang->line('discharged') . " " . $this->lang->line('summary') ?></h5>
                  <div id='summary_print'></div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
          
            <form id="formdishrecord" accept-charset="utf-8" enctype="multipart/form-data" method="post">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12 ">
                            <div class="row row-eq">
                                <div class="col-lg-12 col-md-12 col-sm-12">
                                    <div class="ptt10">
                                        <div id="evajax_load"></div>
                                        <div class="row" id="" >
                                            <div class="col-md-12 col-sm-12 col-12">
                                                <ul class="multilinelist">                                                 
                                                    <li><label ><?php echo $this->lang->line('name'); ?></label>                                  
                                                        <span id="disevlistname"></span>
                                                    </li>
                                                    <li>
                                                        <label ><?php echo $this->lang->line('age'); ?></label>
                                                        <span id="disevage"></span>
                                                    </li> 
                                                    <li>
                                                        <label ><?php echo $this->lang->line('gender'); ?></label>
                                                        <span id="disevgenders" ></span>
                                                    </li>
                                                </ul>   
                                                <ul class="multilinelist">                                                    
                                                    <li>
                                                         <label><?php echo $this->lang->line('admission_date'); ?></label>
                                                        <span id="disedit_admission_date"></span>
                                                    </li> 
                                                    <li>
                                                         <label><?php echo $this->lang->line('discharged') . " " . $this->lang->line('date') ?></label>
                                                        <span id="disedit_discharge_date"></span>
                                                    </li> 
                                                </ul>  
												<ul class="singlelist">  
                                                    <li>
                                                        <label><?php echo $this->lang->line('address')?></label>
                                                        <span id="disevaddress"></span>
                                                    </li>
												</ul>
                                            </div>
                                        </div>
                                        <div class="row">
                                             <div class="col-sm-12">
                                                <div class="mb-3">
                                                    <label ><?php echo $this->lang->line('diagnosis'); ?></label>
                                                    <input name="diagnosis" id='disdiagnosis' rows="3" class="form-control" >
                                                </div>
                                            </div>
                                            <div class="col-sm-12">
                                                <div class="mb-3">
                                                    <label ><?php echo $this->lang->line('operation'); ?></label>
                                                    <input name="operation" id='disoperation'  class="form-control" >
                                                </div>
                                            </div> 
                                            <div class="col-sm-12">
                                                <div class="mb-3">
                                                    <label ><?php echo $this->lang->line('note'); ?></label> 
                                                    <textarea name="note" id='disevnoteipd' rows="3" class="form-control" ><?php echo set_value('note'); ?></textarea>
                                                </div>
                                            </div>                                          
                                            <div class="col-md-12"> 
                                                <div class="dividerhr"></div>
                                            </div><!--./col-md-12-->                                         
                                            <div class="col-sm-4">
                                                <div class="mb-3">
                                                    <label ><?php echo $this->lang->line('investigations'); ?></label> 
                                                    <textarea name="investigations" id='disinvestigations' rows="3" class="form-control" ><?php echo set_value('note'); ?></textarea>
                                                </div>
                                            </div>
                                            <div class="col-sm-8">
                                                <div class="mb-3">
                                                    <label ><?php echo $this->lang->line('treatment_at_home'); ?></label> 
                                                    <textarea name="treatment_at_home" id='distreatment_at_home' rows="3" class="form-control" ><?php echo set_value('note'); ?></textarea>
                                                </div>
                                            </div>     
                                        </div>
                                        <input name="patient_id" id="disevpatients_id" type="hidden">
                                        <input type="hidden" id="disupdateid" name="updateid">
                                        <input type="hidden" id="disopdid" name="opdid">
                                        </div>
                                </div>                               
                            </div><!--./row-->   
                        </div><!--./col-md-12-->       
                    </div><!--./row--> 
                </div>             
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                    <button type="submit" id="formdishrecordbtn" data-loading-text="<?php echo $this->lang->line('processing') ?>" class="btn btn-info"><?php echo $this->lang->line('save'); ?></button>
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
                <div id="allpayments_print" class="ms-auto me-1"></div>
                <div id="deathdoc_download" class="me-2"></div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="pup-scroll-area">
                <div class="modal-body" id="patient_discharge_result"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('close'); ?></button>
                <button type="submit" form="form_patient_discharge" id="add_paymentbtn" data-loading-text="<?php echo $this->lang->line('processing'); ?>" class="btn btn-info printsavebtn d-none"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
            </div>
        </div>
    </div>
</div>

<!-- revisit modal -->
<div class="modal fade sh-modal sh-modal-nospace" id="revisitModal" tabindex="-1" aria-labelledby="revisitModalLabel" aria-hidden="true">
    <div class="modal-dialog">
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
                <input type="hidden" id="revisit_name" name="name" value="" />
                <div class="pup-scroll-area">
                    <div class="modal-body">
                        <div class="row">

                            <!-- LEFT: Patient Info + Symptoms -->
                            <div class="col-lg-8 col-md-8 col-sm-8">

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
                                            <img class="opd-pd-photo-lg d-none" id="patient_image" src="" alt="<?php echo $this->lang->line('patient'); ?>">
                                            <div class="opd-pd-initials-lg d-none" id="patient_image_initials" aria-hidden="true"></div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Symptoms Card -->
                                <div class="sh-form-card mb-2">
                                    <div class="sh-card-header">
                                        <span class="sh-card-header-title"><i class="fas fa-stethoscope"></i> <?php echo $this->lang->line('symptoms'); ?></span>
                                    </div>
                                    <div class="p-2">
                                        <div class="row">
                                            <div class="col-sm-3 col-6">
                                                <div class="mb-3">
                                                    <label class="form-label"><?php echo $this->lang->line('symptoms_type'); ?></label>
                                                    <select name='symptoms_type' id="act" class="form-control select2 act">
                                                        <option value=""><?php echo $this->lang->line('select') ?></option>
                                                        <?php foreach ($symptomsresulttype as $dkey => $dvalue) { ?>
                                                        <option value="<?php echo $dvalue["id"]; ?>"><?php echo $dvalue["symptoms_type"]; ?></option>
                                                        <?php } ?>
                                                    </select>
                                                    <span class="text-danger"><?php echo form_error('symptoms_type'); ?></span>
                                                </div>
                                            </div>
                                            <div class="col-sm-3 col-6">
                                                <div class="mb-3">
                                                    <label class="form-label"><?php echo $this->lang->line('symptoms_title'); ?></label>
                                                    <div id="dd" class="wrapper-dropdown-3">
                                                        <input class="form-control filterinput" type="text" autocomplete="off">
                                                        <ul class="dropdown scroll150 section_ul">
                                                            <li class="section-placeholder"><span><?php echo $this->lang->line('select'); ?></span></li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-6 col-12">
                                                <div class="mb-3">
                                                    <label class="form-label"><?php echo $this->lang->line('symptoms'); ?></label>
                                                    <textarea name="symptoms" id="esymptoms" class="form-control" rows="3"><?php echo set_value('address'); ?></textarea>
                                                </div>
                                            </div>
                                            <div class="col-sm-6 col-12">
                                                <div class="mb-3">
                                                    <label class="form-label"><?php echo $this->lang->line('note'); ?></label>
                                                    <textarea name="note_remark" id="revisit_note" class="form-control" rows="3"><?php echo set_value('note_remark'); ?></textarea>
                                                </div>
                                            </div>
                                            <div class="col-sm-6 col-12">
                                                <div class="mb-3">
                                                    <label class="form-label"><?php echo $this->lang->line('any_known_allergies'); ?></label>
                                                    <textarea name="known_allergies" id="eknown_allergies" class="form-control" rows="3"><?php echo set_value('address'); ?></textarea>
                                                </div>
                                            </div>
                                            <div class="col-sm-12">
                                                <div class="mb-3">
                                                    <?php echo display_custom_fields('opdrecheckup'); ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div><!-- /col-lg-8 -->

                            <!-- RIGHT: Form Cards -->
                            <div class="col-lg-4 col-md-4 col-sm-4 sh-col-sep-left">

                                <!-- Visit Details Card -->
                                <div class="sh-form-card mb-2">
                                    <div class="sh-card-header">
                                        <span class="sh-card-header-title"><i class="fas fa-calendar-alt"></i> <?php echo $this->lang->line('visit_details'); ?></span>
                                    </div>
                                    <div class="p-2">
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <div class="mb-3">
                                                    <label class="form-label"><?php echo $this->lang->line('appointment_date'); ?> <small class="req"> *</small></label>
                                                    <input id="revisit_date" name="appointment_date" type="text" class="form-control datetime" />
                                                    <span class="text-danger"><?php echo form_error('appointment_date'); ?></span>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="mb-3">
                                                    <label class="form-label"><?php echo $this->lang->line('case'); ?></label>
                                                    <input class="form-control" type="text" id="revisit_case" name="revisit_case" />
                                                    <span class="text-danger"><?php echo form_error('case'); ?></span>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="mb-3">
                                                    <label class="form-label"><?php echo $this->lang->line('casualty'); ?></label>
                                                    <select name="casualty" id="revisit_casualty" class="form-control">
                                                        <?php foreach ($yesno_condition as $yesno_key => $yesno_value) { ?>
                                                        <option value="<?php echo $yesno_key ?>" <?php if ($yesno_key == 'no') echo "selected"; ?>><?php echo $yesno_value ?></option>
                                                        <?php } ?>
                                                    </select>
                                                    <span class="text-danger"><?php echo form_error('casualty'); ?></span>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="mb-3">
                                                    <label class="form-label"><?php echo $this->lang->line('old_patient'); ?></label>
                                                    <select name="old_patient" id="revisit_old_patient" class="form-control">
                                                        <?php foreach ($yesno_condition as $yesno_key => $yesno_value) { ?>
                                                        <option value="<?php echo $yesno_key ?>" <?php if ($yesno_key == 'no') echo "selected"; ?>><?php echo $yesno_value ?></option>
                                                        <?php } ?>
                                                    </select>
                                                    <span class="text-danger"><?php echo form_error('old_patient'); ?></span>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="mb-3">
                                                    <label class="form-label"><?php echo $this->lang->line('reference'); ?></label>
                                                    <input class="form-control" id="revisit_refference" type="text" name="refference" />
                                                    <span class="text-danger"><?php echo form_error('refference'); ?></span>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="mb-3 form-check pt5">
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

                                <!-- Consultant Doctor & Charges Card -->
                                <div class="sh-form-card mb-2">
                                    <div class="sh-card-header">
                                        <span class="sh-card-header-title"><i class="fas fa-user-md"></i> <?php echo $this->lang->line('consultant_doctor'); ?> &amp; <?php echo $this->lang->line('charges'); ?></span>
                                    </div>
                                    <div class="p-2">
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <div class="mb-3">
                                                    <label class="form-label"><?php echo $this->lang->line('consultant_doctor'); ?> <small class="req"> *</small></label>
                                                    <select class="form-control select2" <?php if ($disable_option == true) echo "disabled"; ?> name="consultant_doctor" id="revisit_doctor">
                                                        <option value=""><?php echo $this->lang->line('select') ?></option>
                                                        <?php foreach ($doctors as $dkey => $dvalue) { ?>
                                                        <option value="<?php echo $dvalue["id"]; ?>" <?php if ((isset($doctor_select)) && ($doctor_select == $dvalue["id"])) echo "selected"; ?>><?php echo composeStaffNameByString($dvalue["name"], $dvalue["surname"], $dvalue["employee_id"]); ?></option>
                                                        <?php } ?>
                                                    </select>
                                                    <?php if ($disable_option == true) { ?>
                                                    <input type="hidden" name="consultant_doctor" value="<?php echo $doctor_select ?>">
                                                    <?php } ?>
                                                    <span class="text-danger"><?php echo form_error('refference'); ?></span>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label"><?php echo $this->lang->line('charge_category'); ?></label>
                                                    <select name="charge_category" class="form-control charge_category select2">
                                                        <option value=""><?php echo $this->lang->line('select'); ?></option>
                                                        <?php foreach ($charge_category as $key => $value) { ?>
                                                        <option value="<?php echo $value['id']; ?>"><?php echo $value['name']; ?></option>
                                                        <?php } ?>
                                                    </select>
                                                    <span class="text-danger"><?php echo form_error('charge_category'); ?></span>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label"><?php echo $this->lang->line('charge'); ?> <small class="req"> *</small></label>
                                                    <select name="charge_id" id="revisit_charge_id" class="form-control charge select2">
                                                        <option value=""><?php echo $this->lang->line('select') ?></option>
                                                    </select>
                                                    <span class="text-danger"><?php echo form_error('charge_id'); ?></span>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label"><?php echo $this->lang->line('standard_charge') . " (" . $currency_symbol . ")" ?></label>
                                                    <input type="text" readonly name="standard_charge" id="standard_chargevisit" class="form-control" value="<?php echo set_value('standard_charge'); ?>">
                                                    <span class="text-danger"><?php echo form_error('standard_charge'); ?></span>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label"><?php echo $this->lang->line('applied_charge') . " (" . $currency_symbol . ")" ?> <small class="req"> *</small></label>
                                                    <input type="text" name="amount" id="apply_chargevisit" class="form-control apply_charge">
                                                    <span class="text-danger"><?php echo form_error('apply_charge'); ?></span>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label"><?php echo $this->lang->line('discount'); ?></label>
                                                    <div class="input-group">
                                                        <input type="text" class="form-control discount_percentage" name="discount_percentage" id="discount_percentage" value="0" autocomplete="off">
                                                        <span class="input-group-text">%</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label"><?php echo $this->lang->line('tax'); ?></label>
                                                    <div class="input-group">
                                                        <input type="text" class="form-control right-border-none" name="percentage" id="percentage" readonly autocomplete="off">
                                                        <span class="input-group-text">%</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label"><?php echo $this->lang->line('amount') . ' (' . $currency_symbol . ')'; ?> <small class="req"> *</small></label>
                                                    <input name="apply_amount" readonly type="text" class="form-control" id="revisit_amount" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Payment Card -->
                                <div class="sh-form-card mb-2">
                                    <div class="sh-card-header">
                                        <span class="sh-card-header-title"><i class="fas fa-credit-card"></i> <?php echo $this->lang->line('payment'); ?></span>
                                    </div>
                                    <div class="p-2">
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <div class="mb-3">
                                                    <label class="form-label"><?php echo $this->lang->line('payment_mode'); ?></label>
                                                    <select name="payment_mode" id="revisit_payment" class="form-control revisit_payment_mode">
                                                        <?php foreach ($payment_mode as $payment_key => $payment_value) { ?>
                                                        <option value="<?php echo $payment_key ?>" <?php if ($payment_key == 'cash') echo "selected"; ?>><?php echo $payment_value ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="mb-3">
                                                    <label class="form-label"><?php echo $this->lang->line('paid_amount') . ' (' . $currency_symbol . ')'; ?> <small class="req"> *</small></label>
                                                    <input name="paid_amount" type="text" class="form-control paid_amount" id="paid_amount" />
                                                    <span class="text-danger"><?php echo form_error('paid_amount'); ?></span>
                                                </div>
                                            </div>
                                            <div class="col-md-6 revisit_cheque_div" style="display:none;">
                                                <div class="mb-3">
                                                    <label class="form-label"><?php echo $this->lang->line('cheque_no'); ?> <small class="req"> *</small></label>
                                                    <input type="text" name="cheque_no" id="cheque_no" class="form-control">
                                                    <span class="text-danger"><?php echo form_error('cheque_no'); ?></span>
                                                </div>
                                            </div>
                                            <div class="col-md-6 revisit_cheque_div" style="display:none;">
                                                <div class="mb-3">
                                                    <label class="form-label"><?php echo $this->lang->line('cheque_date'); ?> <small class="req"> *</small></label>
                                                    <input type="text" name="cheque_date" id="cheque_date" class="form-control date">
                                                    <span class="text-danger"><?php echo form_error('cheque_date'); ?></span>
                                                </div>
                                            </div>
                                            <div class="col-sm-12 revisit_cheque_div" style="display:none;">
                                                <div class="mb-3">
                                                    <label class="form-label"><?php echo $this->lang->line('attach_document'); ?></label>
                                                    <input type="file" class="filestyle form-control" name="document">
                                                    <span class="text-danger"><?php echo form_error('document'); ?></span>
                                                </div>
                                            </div>
                                            <?php if ($this->module_lib->hasActive('live_consultation')) { ?>
                                            <div class="col-sm-6">
                                                <div class="mb-3">
                                                    <label class="form-label"><?php echo $this->lang->line('live_consultation'); ?></label>
                                                    <select name="live_consult" id="live_consultvisit" class="form-control">
                                                        <?php foreach ($yesno_condition as $yesno_key => $yesno_value) { ?>
                                                        <option value="<?php echo $yesno_key ?>" <?php if ($yesno_key == 'no') echo "selected"; ?>><?php echo $yesno_value ?></option>
                                                        <?php } ?>
                                                    </select>
                                                    <span class="text-danger"><?php echo form_error('live_consult'); ?></span>
                                                </div>
                                            </div>
                                            <?php } ?>
                                            <?php if ($result['gender'] == 'Female') { if ($this->rbac->hasPrivilege('opd_antenatal', 'can_view')) { ?>
                                            <div class="col-sm-6" id="antenatal_div">
                                                <div class="mb-3">
                                                    <div class="form-check mt-4">
                                                        <input class="form-check-input" type="checkbox" name="is_for_antenatal" id="is_for_antenatal" value="1">
                                                        <label class="form-check-label" for="is_for_antenatal"><?php echo $this->lang->line('is_antenatal'); ?></label>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php } } ?>
                                        </div>
                                    </div>
                                </div>

                            </div><!-- /col-lg-4 -->

                        </div><!-- /row -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                    <button type="submit" id="formrevisitbtn" data-loading-text='<i class="fa fa-circle-o-notch fa-spin"></i>' class="btn btn-info"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- -->
<div class="modal fade sh-modal sh-modal-accent" id="myPaymentModal" tabindex="-1" aria-labelledby="myPaymentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myPaymentModalLabel"><?php echo $this->lang->line('add_payment'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="add_payment" accept-charset="utf-8" method="post">
                <input type="hidden" name="opd_id" id="payment_opd_id" value="<?php echo $result['id']; ?>">
                <input type="hidden" name="case_reference_id" value="<?php echo $result['case_reference_id']; ?>">
                <input type="hidden" name="patient_id" value="<?php echo $id; ?>">
                <input type="hidden" name="net_amount" value="<?php echo $total - $total_payment; ?>">
                <div class="pup-scroll-area">
                    <div class="modal-body modal-background">
                        <div class="sh-form-card">
                            <div class="sh-card-header">
                                <span class="sh-card-header-title"><?php echo $this->lang->line('add_payment'); ?></span>
                            </div>
                            <div class="p-2">
                                <div class="row g-2">
                                    <div class="col-md-6">
                                        <label class="form-label"><?php echo $this->lang->line('date'); ?></label><small class="req"> *</small>
                                        <input type="text" name="payment_date" id="date" class="form-control form-control-sm datetime" autocomplete="off">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label"><?php echo $this->lang->line('amount') . " (" . $currency_symbol . ")"; ?></label><small class="req"> *</small>
                                        <input type="text" name="amount" id="amount" class="form-control form-control-sm" value="<?php echo number_format((float)($total - $total_payment), 2, '.', ''); ?>">
                                        <span class="text-danger"><?php echo form_error('amount'); ?></span>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label"><?php echo $this->lang->line('payment_mode'); ?></label>
                                        <select class="form-control form-control-sm payment_mode" name="payment_mode">
                                            <?php foreach ($payment_mode as $key => $value) { ?>
                                            <option value="<?php echo $key ?>" <?php if ($key == 'cash') { echo "selected"; } ?>><?php echo $value ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label"><?php echo $this->lang->line('note'); ?></label>
                                        <input type="text" name="note" id="note" class="form-control form-control-sm">
                                    </div>
                                    <div class="col-md-6 cheque_div" style="display:none;">
                                        <label class="form-label"><?php echo $this->lang->line('cheque_no'); ?></label><small class="req"> *</small>
                                        <input type="text" name="cheque_no" id="cheque_no" class="form-control form-control-sm">
                                        <span class="text-danger"><?php echo form_error('cheque_no'); ?></span>
                                    </div>
                                    <div class="col-md-6 cheque_div" style="display:none;">
                                        <label class="form-label"><?php echo $this->lang->line('cheque_date'); ?></label><small class="req"> *</small>
                                        <input type="text" name="cheque_date" id="cheque_date" class="form-control form-control-sm date">
                                        <span class="text-danger"><?php echo form_error('cheque_date'); ?></span>
                                    </div>
                                    <div class="col-12 cheque_div" style="display:none;">
                                        <label class="form-label"><?php echo $this->lang->line('attach_document'); ?></label>
                                        <input type="file" class="filestyle form-control form-control-sm" name="document">
                                        <span class="text-danger"><?php echo form_error('document'); ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                    <button type="submit" id="add_paymentbtn" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i>" class="btn btn-info"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
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
                <div id="action_detail_modal"></div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="pup-scroll-area">
                <div class="modal-body modal-background">
                    <div id="show_ot_data"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('close'); ?></button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade sh-modal sh-modal-accent" id="editpayment_modal" tabindex="-1" aria-labelledby="editpayment_modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editpayment_modalLabel"><?php echo $this->lang->line('payment_details'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editpaymentform" accept-charset="utf-8" method="post">
                <input type="hidden" id="edit_payment_id" name="edit_payment_id">
                <div class="pup-scroll-area">
                    <div class="modal-body modal-background">
                        <div class="sh-form-card">
                            <div class="sh-card-header">
                                <span class="sh-card-header-title"><?php echo $this->lang->line('payment_details'); ?></span>
                            </div>
                            <div class="p-2">
                                <div class="row g-2">
                                    <div class="col-md-6">
                                        <label class="form-label"><?php echo $this->lang->line('date'); ?></label><small class="req"> *</small>
                                        <input type="text" name="payment_date" id="payment_date" class="form-control form-control-sm datetime" autocomplete="off">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label"><?php echo $this->lang->line('amount') . " (" . $currency_symbol . ")"; ?></label><small class="req"> *</small>
                                        <input type="text" name="amount" id="edit_payment" class="form-control form-control-sm" value="">
                                        <span class="text-danger"><?php echo form_error('amount'); ?></span>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label"><?php echo $this->lang->line('payment_mode'); ?></label>
                                        <select class="form-control form-control-sm payment_mode" name="payment_mode" id="payment_mode">
                                            <?php foreach ($payment_mode as $key => $value) { ?>
                                            <option value="<?php echo $key ?>" <?php if ($key == 'cash') { echo "selected"; } ?>><?php echo $value ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label"><?php echo $this->lang->line('note'); ?></label>
                                        <input type="text" name="note" id="edit_payment_note" class="form-control form-control-sm">
                                    </div>
                                    <div class="col-md-6 cheque_div" style="display:none;">
                                        <label class="form-label"><?php echo $this->lang->line('cheque_no'); ?></label><small class="req"> *</small>
                                        <input type="text" name="cheque_no" id="edit_cheque_no" class="form-control form-control-sm">
                                        <span class="text-danger"><?php echo form_error('cheque_no'); ?></span>
                                    </div>
                                    <div class="col-md-6 cheque_div" style="display:none;">
                                        <label class="form-label"><?php echo $this->lang->line('cheque_date'); ?></label><small class="req"> *</small>
                                        <input type="text" name="cheque_date" id="edit_cheque_date" class="form-control form-control-sm date">
                                        <span class="text-danger"><?php echo form_error('cheque_date'); ?></span>
                                    </div>
                                    <div class="col-12 cheque_div" style="display:none;">
                                        <label class="form-label"><?php echo $this->lang->line('attach_document'); ?></label>
                                        <input type="file" class="filestyle form-control form-control-sm" name="document">
                                        <span class="text-danger"><?php echo form_error('document'); ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                    <button type="submit" id="editpaymentbtn" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i>" class="btn btn-info"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- -->
<div class="modal fade sh-modal sh-modal-accent" id="findingview" tabindex="-1" aria-labelledby="findingviewLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="findingviewLabel"><?php echo $this->lang->line('antenatal_finding'); ?></h5>
                <div class="d-flex align-items-center gap-2 ms-auto me-2">
                    <span id="edit_printfinding"></span>
                    <span id="edit_editfinding"></span>
                    <span id="edit_deletefinding"></span>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="pup-scroll-area">
                <div class="modal-body" id="getdetails_finding">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('close'); ?></button>
            </div>
        </div>
    </div>
</div>

<!-- Add Obstetric prescription -->
<div class="modal fade sh-modal sh-modal-nospace" id="obstetric_prescription" tabindex="-1" aria-labelledby="antenatal_title" aria-hidden="true">
    <div class="modal-dialog pup100 modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="antenatal_title"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="antenatal_prescription" accept-charset="utf-8" enctype="multipart/form-data" method="post">
                <input type="hidden" id="visit_details_id" name="visit_details_id" value="<?php echo $result['id']; ?>">
                <input type="hidden" id="id" name="id" value="<?php echo $result['id']; ?>">
                <div class="pup-scroll-area">
                    <div class="modal-body">
                    </div><!-- /.modal-body -->
                </div><!-- /.pup-scroll-area -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                    <button type="submit" name="save" value="save" data-loading-text="<?php echo $this->lang->line('processing'); ?>" class="btn btn-info"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div><!-- end Obstetric prescription -->
 
<div class="modal fade sh-modal sh-modal-accent" id="vitalModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content modal-media-content mx-2">
            <div class="modal-header modal-media-header overflow-hidden">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                <h5 class="modal-title"><div id="modal_title"></div></h5>
            </div>
           
                <form id="formadd" accept-charset="utf-8" method="post">
                    <div class="pup-scroll-area">
                       <div class="modal-body modal-background " >
                        <input type="hidden" class="id" name="patient_id" id="patient_id" value="<?php echo $result['patient_id']; ?>">
                        <div class="row sh-form-card h-100">
                            <div class="sh-card-header">
                             <span class="sh-card-header-title"><?php echo $this->lang->line('vitals'); ?></span>
                         </div>
                                <div class="table-responsive overflow-visible mt5">
                                    <table class="table table-striped mb5 table-bordered table-hover  tablefull12 tblProducts" id="tableID_vitals">
                                        <thead>
                                            <tr class="font13 white-space-nowrap">
                                                <th ><?php echo $this->lang->line('vital_name'); ?><small class="req"> *</small></th>
                                                <th ><?php echo $this->lang->line('vital_value'); ?><small class="req"> *</small></th>
                                                <th ><?php echo $this->lang->line('date'); ?><small class="req"> *</small></th>
                                                <th class="text-end" ></th>
                                            </tr>
                                        </thead>
                                        <tr id="row_vitals1">                                        
                                            <td>
                                                <input type="hidden" name="total_rows_vitals[]" id="calculate" value="1">
                                                <select name="vital_name_1" id="vital_name_1" class="form-control">
                                                    <option value=""><?php echo $this->lang->line("select"); ?></option>
                                                    <?php foreach($vital_list as $row){ ?>
                                                    <option value="<?php echo $row['id']; ?>"><?php echo html_escape($row['name']); ?><?php if($row['reference_range']){ echo ' ('.html_escape($row['reference_range']) . " " .html_escape($row['unit']) . ')'; } ?>  </option>
                                                        <?php } ?>
                                                </select>
                                                <span class="text-danger"><?php echo form_error('charge_type[]'); ?></span>
                                            </td>                                            
                                            <td>                                            
                                                <input autofocus="" name="from_reference_range_1" id="from_reference_range_1"  type="text" class="form-control"  />
                                                <span class="text-danger"><?php echo form_error('from_reference_range[]'); ?></span>
                                            </td>                                            
                                            <td>
                                                <input id="vital_date_1" name="vital_date_1"  placeholder="" type="text" class="form-control datetime"  />
                                                <span class="text-danger"><?php echo form_error('vital_date'); ?></span>
                                            </td>                                                
                                            <td class="text-center align-middle" width="10%">
                                                <button type="button"  class="closebtn delete_vitals_row btn btn-sm btn-outline-danger " data-row-id="1" autocomplete="off"><i class="fa fa-remove"></i></button>
                                            </td>                                        
                                        </tr>
                                    </table>
                                    <div class="d-flex justify-content-end">
                                        <a class="btn btn-info addplus-xs add-vital-record mb10 mt5" data-added="0"><i class="fa fa-plus"></i> <?php echo $this->lang->line('add')?></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div><!-- /.pup-scroll-area -->

                     <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                        <button type="submit" id="formaddbtn" data-loading-text="<?php echo $this->lang->line('processing'); ?>" class="btn btn-info"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                    </div>
                    <input type="hidden" id="row_count" value='1'>
                </form>

        </div>
    </div>
</div>

<div class="modal fade sh-modal sh-modal-accent" id="myvitalEditModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content modal-media-content mx-2">
            <div class="modal-header modal-media-header overflow-hidden">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                <h5 class="modal-title"><?php echo $this->lang->line('edit_vital'); ?></h5> 
            </div>
                <form id="edit_vital" accept-charset="utf-8" enctype="multipart/form-data" method="post">
                    <div class="pup-scroll-area">
                       <div class="modal-body modal-background " >
                      <div class="row  sh-form-card h-100">
                               <div class="sh-card-header">
                             <span class="sh-card-header-title"><?php echo $this->lang->line('vitals'); ?></span>
                         </div>
                                <div class=" col-md-12">
                                    <div class="mb-3">
                                        <label ><?php echo $this->lang->line('vital_name'); ?></label><small class="req"> *</small>
                                        <input type="hidden" name="patient_id" id="vepatientid" value="">
                                        <input type="hidden" name="evital_id" id="evital_id" value="">
                                        <input type="hidden" name="evital_name" id="evital_name" value="">                                      
                                        <select id="evital_name_edit" name="evital_name_edit" placeholder="" type="text" class="form-control" disabled />
                                        <option value=""><?php echo $this->lang->line("select"); ?></option>
                                           <?php foreach($vital_list as $row){ ?>
                                             <option value="<?php echo $row['id']; ?>"><?php echo html_escape($row['name']); ?><?php if($row['reference_range']){ echo ' ('.html_escape($row['reference_range']) . " " . html_escape($row['unit']) . ')'; } ?></option>
                                           <?php } ?>
                                           </select>
                                        <span class="text-danger"><?php echo form_error('vital_title'); ?></span>
                                    </div>                                   
                                    <div class="mb-3">
                                        <label ><?php echo $this->lang->line('vital_value'); ?></label><small class="req"> *</small>
                                       <input id="evital_value" name="evital_value" placeholder="" type="text" class="form-control"  />
                                        <span class="text-danger"><?php echo form_error('description'); ?></span>
                                    </div>
                                    <div class="mb-3">
                                        <label ><?php echo $this->lang->line('date'); ?></label><small class="req"> *</small>
                                        <input id="emessure_date" name="emessure_date" value="" placeholder="" type="text" class="form-control datetime"  />
                                        <span class="text-danger"><?php echo form_error('messure_date'); ?></span>                         
                                    </div>                                   
                                </div>
                            </div>
                        </div>
                    </div><!-- /.pup-scroll-area -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                        <button type="submit" data-loading-text="<?php echo $this->lang->line('processing'); ?>" id="edit_vitalbtn" class="btn btn-info"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                    </div>
                </form>
        </div>
    </div> 
</div>

<!-- //========datatable start===== -->
<script>
    (function ($) {      
        $('#vitalModal').on('hidden.bs.modal', function () {                
            var row_count = $('#row_count').val();
            for(var i = 2; i <= row_count; i++){                     
                $("#row_vitals" + i).remove();                  
            }
        })  
    })(jQuery);
</script> 

<script type="text/javascript">
    
    var total_rows_vitals=1;
    $(document).on('click','.add-vital-record',function(){
        var table = document.getElementById("tableID_vitals");
        var id = total_rows_vitals+1;        
        var div = "<td><input type='hidden' name='total_rows_vitals[]' value='" + id + "'><select name='vital_name_"+id+"' id='vital_name_"+id+"' class='form-control'><option value=''><?php echo $this->lang->line('select'); ?></option><?php foreach($vital_list as $row){ ?><option value='<?php echo $row['id']; ?>'><?php echo $row['name']; ?><?php if($row['reference_range']){ echo ' ('.$row['reference_range']  .' '. $row['unit'].')'; } ?></option><?php } ?></select></td><td><input name='from_reference_range_"+id+"' id='from_reference_range_"+id+"'  type='text' class='form-control'  /></td><td><input id='vital_date_"+id+"' name='vital_date_"+id+"'  type='text' class='form-control datetime'  /></td>";
        var row =  "<tr id='row_vitals" + id + "'>" + div + "<td class='text-center align-middle'><button type='button' data-row-id='"+id+"' class='closebtn delete_vitals_row btn btn-sm btn-outline-danger'><i class='fa fa-remove'></i></button></td></tr>";
        $('#tableID_vitals').append(row);       
        total_rows_vitals++;        
        var row_count = $('#row_count').val();
        row_count++;
        $('#row_count').val(row_count);       
    });
    
    $(document).on('click','.delete_vitals_row',function(e){
        if(confirm("<?php echo $this->lang->line('are_you_sure_to_delete_this'); ?>")){
            var modal_=$(e.target).closest('div.modal');
            var del_row_id=$(this).data('rowId');
            $("#row_vitals" + del_row_id).remove();             
        }        
    });
    
    $(document).ready(function (e) {

        $(document).on('click','#assign_link',function(e){       
            var $this = $(this);
    let conference_id= $(this).data('linkId');
           $.ajax({
               url: base_url+'admin/patient/addlivelink',
               type: "POST",
               data: {'conference_id':conference_id},
               dataType: 'JSON',
               beforeSend: function() {
                 $this.btnLoading();      
          },
               success: function (data) {
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
                    $this.btnReset();  
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

        $("#formadd").on('submit', (function (e) { 
            e.preventDefault();
            var total_rows = document.getElementsByName("total_rows_vitals[]").length;
            if(total_rows==0){
                errorMsg("<?php echo $this->lang->line("please_add_at_lease_one_vital"); ?>");
                return;
            } 
            $.ajax({
                url: '<?php echo base_url(); ?>admin/vital/addpatientvital',
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
                    $("#formaddbtn").btnReset();
                },
                error: function () {
                }
            });
        }));
    });

$(document).on('input paste keyup','.apply_charge_add_charge,.discount_percentage_add_charge', function(e){ 
    update_charge_amount($(e.target).closest('div.modal'));
});

    function update_charge_amount(object_model){         
        let  apply_charge= object_model.find('.apply_charge_add_charge').val();      
        let  discount_percentage=object_model.find('#discount_percentage_add_charge').val();
        let discount_amount=isNaN((parseFloat(apply_charge) * discount_percentage/100)) ? 0 :(parseFloat(apply_charge) * discount_percentage/100) ;
        let  tax_percentage=object_model.find('#charge_tax').val();
        let tax_amount=((apply_charge-discount_amount)*tax_percentage)/100;

            if(tax_percentage !='' && tax_percentage !=0){
                 apply_amount=(parseFloat(apply_charge-discount_amount) * tax_percentage/100)+((parseFloat(apply_charge))-(discount_amount));      
                 object_model.find('#tax').val((tax_amount).toFixed(2));
                 object_model.find('.discount_percentage_amount').val((discount_amount).toFixed(2));            
                 object_model.find('.net_amount').val((apply_amount).toFixed(2));            
            }else{

                apply_amount=((parseFloat(apply_charge))-(discount_amount));      
                object_model.find('#tax').val((tax_amount).toFixed(2));
                object_model.find('.discount_percentage_amount').val((discount_amount).toFixed(2));            
                object_model.find('.net_amount').val((apply_amount).toFixed(2)); 
            }
    }

    //calculate discount amount to discount percantage
    function get_percentage(discount_amount){
        var discount_amount=(discount_amount != "") ?discount_amount: 0;
        var total=$('#apply_charge').val();
        
        var charge_tax=$('#charge_tax').val();
        var discount_percent=0;
        var net_amount=0;     
        discount_percent=((parseInt(discount_amount)/parseInt(total))*100);
        $('#discount_percentage_add_charge').val(discount_percent.toFixed(2));
		
		var taxamt = (((parseInt(total)-parseInt(discount_amount))*parseInt(charge_tax)) )/100;		
        $('#tax').val(taxamt.toFixed(2));
		
		net_amount=((parseInt(total)-parseInt(discount_amount))+parseInt(taxamt));
        $('#final_amount').val(net_amount.toFixed(2));		
    }

    function update_percentage(discount_amount){
        var discount_amount=(discount_amount != "") ?discount_amount: 0;
        var total=$('#editapply_charge').val();
        var tax=$('#editcharge_tax').val(); 
        var discount_percent=0;
        var net_amount=0;
		 
        discount_percent=((parseInt(discount_amount)/parseInt(total))*100);
        $('#editdiscount_percentage').val(discount_percent.toFixed(2));
		
		var taxamt = (((parseInt(total)-parseInt(discount_amount))*parseInt(tax)) )/100;		
        $('#edittax').val(taxamt.toFixed(2));
		
        net_amount=((parseInt(total)-parseInt(discount_amount))+parseInt(taxamt));		
        $('#editfinal_amount').val(net_amount.toFixed(2));
    }
    //calculate discount amount to discount persantage

    $(document).on('input paste keyup','.apply_charge,.discount_percentage', function(e){ 
        update_amount($(e.target).closest('div.modal'));
    });
    
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
</script>

<script type="text/javascript">
( function ( $ ) {
    var id = "<?php echo $this->uri->segment(4); ?>"; 
    'use strict';
    $(document).ready(function () {
        initDatatable('treatmentlist','admin/patient/getopdtreatmenthistory/'+ id);      
    });
} ( jQuery ) )
</script>

<script type="text/javascript">

( function ( $ ) {
     var opdid = "<?php echo $this->uri->segment(5); ?>";   
    'use strict';
    $(document).ready(function () {
        modal_click_disabled('view_ot_modal', 'myPaymentModal', 'viewModal', 'add_chargeModal', 'editpayment_modal', 'add_operationtheatre', 'myTimelineModal', 'vitalModal', 'myaddMedicationModal', 'myMedicationModal', 'myMedicationDoseModal', 'edit_diagnosis', 'edit_operationtheatre', 'editModal', 'myModaledit', 'myTimelineEditModal', 'myModaldischarged', 'prescriptionview', 'prescriptionviewmanual', 'revisitModal', 'myvitalEditModal', 'findingview')
        initDatatable('ajaxlist','admin/patient/getvisitdatatable/'+ opdid);        
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
        s += "<label ><?= $this->lang->line('act') ?></label>";
        s += "<select class='form-control act select2' id='act' name='act" + rows + "' data-row_id='" + rows + "'>";
        s += "<option value=''>--Select--</option>";
        s += $('#act-template').html();
        s += "</select>";
        s += "<small class='text text-danger help-inline'></small>";
        s += "</div>";
        s += "</div>";
        s += "<div class='col-md-5'>";
        s += "<label ><?= $this->lang->line('section') ?></label>";
        s += "<div id='dd' class='wrapper-dropdown-3'>";
        s += "<input class='form-control filterinput' type='text' autocomplete='off'>";
        s += "<ul class='dropdown scroll150 section_ul'>";
        s += "<li class='section-placeholder'><span>--Select--</span></li>";
        s += "</ul>";
        s += "</div>";
        s += "</div>";
        s += "<div class='col-md-1'>";
        s += "<div class='mb-3'>";
        s += "<label >&nbsp;</label>";
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
   <?php foreach ($symptomsresulttype as $dkey => $dvalue) {   ?>
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
    // .timepicker inputs auto-initialized as TD 6 time-only pickers via event delegation in footer.php

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
    function addmedicationModal() {
        document.querySelector("#add_medication").reset();
        $("#mmedicine_id").val("").trigger("change");
        shModal('myaddMedicationModal').show();
    }

   $('#myaddMedicationModal').on('hidden.bs.modal', function () {
    $('#add_medication').find('input:text, input:password, input:file, textarea').val('');
    $('#add_medication').find('select option:selected').removeAttr('selected');
    $('#add_medication').find('input:checkbox, input:radio').removeAttr('checked');
    $('.medicine_category_medication').val("").trigger("change");
    $('.medicine_name_medication').val("").trigger("change");
    $('.dosage_medication').val("").trigger("change");
     $('#mtime').val('12:00 PM');
   });

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
                shModal('myMedicationModal').show();
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
                $('#dosagetime').val(timeConvert(data.time));
                $('select[id="medicine_dose_id"] option[value="' + data.medicine_dosage_id + '"]').attr("selected", "selected");
                $("#medicine_dose_edit_id").select2().select2('val', data.medicine_dosage_id);
                $("#mmedicine_category_edit_id ").val(data.medicine_category_id).trigger('change');
                getMedicineForMedication(data.medicine_category_id,data.pharmacy_id);
                $("#medicine_dosage_remark").val(data.remark);
                $("#medication_id").val(data.id);
                $('#edit_delete_medication').html("<a href='#' class='delete_record_dosage btn btn-sm btn-light' data-record-id='"+ medication_id + "' data-bs-toggle='tooltip' title='<?php echo $this->lang->line('delete'); ?>' data-bs-target='' data-bs-toggle='modal'   title='<?php echo $this->lang->line('delete'); ?>'><i class='fa fa-trash'></i></a>");
                shModal('myMedicationDoseModal').show();
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
                // Destroy before replacing HTML so CKEditor can restore original textareas cleanly
                ['compose-textareanew', 'compose-textareas'].forEach(function(id) {
                    if (window.CKEDITOR && CKEDITOR.instances[id]) { CKEDITOR.instances[id].destroy(true); }
                });
                $('.modal-title',"#add_prescription").html('<?php echo $this->lang->line('edit_prescription'); ?>');
                shModal('prescriptionview').hide();
                $('.modal-body',"#add_prescription").html(res.page);
                $('.filestyle2',"#add_prescription").dropify();
                var medicineTable= $('.modal-body',"#add_prescription").find('table#tableID');
                medicineTable.find('.select2').select2();
                $('.modal-body',"#add_prescription").find('.multiselect2').select2({
                    placeholder: 'Select',
                    allowClear: false,
                    minimumResultsForSearch: 2
                });

                $('#tableID tr').each(function(){
                    // _editopdprescription.php already renders the medicine + dose <select>
                    // options server-side (with the saved one pre-selected). So just init
                    // select2 and set the value — do NOT rebuild via getDosages()/getMedicine()
                    // (there is no per-row medicine_category dropdown here, so those wiped the
                    // server-rendered dose list to empty).
                    var post_medicine_id = $(this).find("td input.post_medicine_id").val();
                    var dosage_id = $(this).find("td input.post_dosage_id").val();
                    $(this).find('.medicine_name').select2().select2('val', post_medicine_id);
                    $(this).find('.medicine_dosage').select2().select2('val', dosage_id);
                });

                shModal('add_prescription').show();
            },

            complete: function() {
                if (window.CKEDITOR) {
                    CKEDITOR.replace('compose-textareanew', { allowedContent: true });
                    CKEDITOR.replace('compose-textareas', { allowedContent: true });
                }
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
                shModal('edit_diagnosis').show();

            },
        });
    }
    
    $(document).on('click','.editot',function(){
        let id=$(this).data('recordId');
        $.ajax({
            url: '<?php echo base_url(); ?>admin/operationtheatre/getotDetails',
            type: "get",
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
                shModal('edit_operationtheatre').show();

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
               
                var delete_action = "<a href='#'  class='btn btn-sm btn-light' data-bs-toggle='tooltip'  onclick='delete_record(" + visitid + ")' title='<?php echo $this->lang->line('delete'); ?>'><i class='fa fa-trash'></i></a>";
                
                var patient_id = "<?php echo $result["id"] ?>";
				
				var print_button ="<a href='#' class='btn btn-sm btn-light' data-bs-toggle='tooltip' onclick='getopdrecheckupDetails_print(" + visitid + ")' title='<?php echo $this->lang->line('print'); ?>'><i class='fa fa-print'></i></a>";
				
                $('#edit_delete').html(print_button+"<?php if ($this->rbac->hasPrivilege('visit', 'can_edit')) { ?><a href='#'' onclick='editRecord(" + visitid + ")' data-bs-target='#editModal' data-bs-toggle='tooltip' class=' btn btn-sm btn-light'  title='<?php echo $this->lang->line('edit'); ?>'><i class='fa fa-pencil'></i></a><?php } ?><?php if ($this->rbac->hasPrivilege('visit', 'can_delete')) { ?>"+delete_action+"<?php } ?>" );
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
	
    function getopdrecheckupDetails_print(visitid) {
        $.ajax({
            url: '<?php echo base_url(); ?>admin/patient/getopdrecheckupdetails_print',
            type: 'POST',
            data: { visit_id: visitid },
            success: function (result) {
                popup(result);
            }
        });
    }

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
                $("#edit_oldpatient").val(data.patient_old);
                $("#edit_refference").val(data.refference);
                $("#edit_revisit_note").val(data.note);
                $('select[id="edit_organisation"] option[value="'+data.organisation_id+'"]').attr("selected","selected");             
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
                 
                if(data.is_antenatal != '0'){
                    $("#edit_is_for_antenatal").attr('checked', true);                    
                } else {                     
                    $("#edit_is_for_antenatal").attr('checked', false);                 
                }        
                 
                shModal("viewModal").hide();
                loadOpdEditIcdCodes(data.opdid);
                shModal('editModal').show();
            },
        });
    }

    function loadOpdEditIcdCodes(opd_id) {
        $.ajax({
            url: '<?php echo base_url(); ?>admin/patient/get_opd_icd_codes/' + opd_id,
            type: 'GET',
            dataType: 'json',
            success: function (data) {
                var $select = $('#opd_edit_icd_code_ids');
                var saved = data.map(function (c) { return String(c.id); });
                $select.find('option').each(function () {
                    $(this).prop('selected', saved.indexOf($(this).val()) !== -1);
                });
                $select.trigger('change.select2');
            }
        });
    }

    $('#opd_edit_icd10_group_filter').on('change', function () {
        var group_id = $(this).val();
        $.ajax({
            url: '<?php echo base_url(); ?>admin/icd10/get_codes_by_group',
            type: 'POST',
            data: { group_id: group_id },
            dataType: 'json',
            success: function (data) {
                var $select = $('#opd_edit_icd_code_ids');
                $select.empty();
                $.each(data, function (i, c) {
                    $select.append('<option value="' + c.id + '">[' + c.icd_code + '] ' + c.icd_description + '</option>');
                });
                $select.trigger('change.select2');
            }
        });
    });

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

    function delete_patient(id, patient_id) {
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

    function getEditRecord(id) {       
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
                $("#age_year").val(data.age);
                $("#age_month").val(data.month);
                $("#age_day").val(data.day);
                $("#birth_date").val(data.dob);
                $("#enote").val(data.note);
                $("#exampleInputFile").attr("data-default-file", '<?php echo base_url() ?>' + data.image);
                $(".dropify-render").find("img").attr("src", '<?php echo base_url() ?>' + data.image);
                $("#eknown_allergies").val(data.known_allergies);
                $('select[id="blood_groups"] option[value="' + data.blood_bank_product_id + '"]').attr("selected", "selected");
                $('select[id="egenders"] option[value="' + data.gender + '"]').attr("selected", "selected");
                $('select[id="marital_statuss"] option[value="' + data.marital_status + '"]').attr("selected", "selected");
                shModal("myModal").hide();
                shModal('myModaledit').show();
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
                $("#etimelineid").val(data.id);
                $("#epatientid").val(data.patient_id);
                $("#etimelinetitle").val(data.title);
                $("#etimelinedate").val(data.timeline_datetime);               
                $("#timelineedesc").val(data.description);
                if (data.status == '') {
                
                } else  {
                    $("#evisible_check").attr('checked', true);
                }
               
                shModal('myTimelineEditModal').show();
            },
        });
    }

    function getRecordDischarged(id, opdid) {     
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
                shModal('myModaldischarged').show();
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
                ['compose-textareaneww', 'compose-textareass'].forEach(function(id) {
                    if (window.CKEDITOR && CKEDITOR.instances[id]) { CKEDITOR.instances[id].destroy(true); }
                });
                $('.modal-title',"#add_prescription").html('<?php echo $this->lang->line('add_prescription'); ?>');
                $('.modal-body',"#add_prescription").html(res.page);
                $('.modal-body',"#add_prescription").find('.filestyle').dropify();
                $('.modal-body',"#add_prescription").find('table').find('.select2').select2();
                $('.modal-body',"#add_prescription").find('.multiselect2').select2({
                        placeholder: 'Select',
                        allowClear: false,
                        minimumResultsForSearch: 2
                    });
                shModal('add_prescription').show();
            },

            complete: function() {
                if (window.CKEDITOR) {
                    CKEDITOR.replace('compose-textareaneww', { allowedContent: true });
                    CKEDITOR.replace('compose-textareass', { allowedContent: true });
                }
             },
             error: function(xhr) {
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
        $("form#form_prescription button[type=submit]").click(function() {            
        $("button[type=submit]", $(this).parents("form")).removeAttr("clicked");
        $(this).attr("clicked", "true");
    });

        $("#form_prescription").on('submit', (function (e) {

             var sub_btn_clicked = $("button[type=submit][clicked=true]");
            var sub_btn_clicked_name=sub_btn_clicked.attr('name');
            e.preventDefault();
            if (window.CKEDITOR) {
                for (var inst in CKEDITOR.instances) { CKEDITOR.instances[inst].updateElement(); }
            }
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

                        if(sub_btn_clicked_name === "save_print") {                                      
                            printprescription(data.visitid,true);
                        }
                        shModal('add_prescription').hide();
                        $('.ajaxlist').DataTable().ajax.reload();
                      
                    }                   
                    sub_btn_clicked.btnReset()  ;
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

    var prescription_rows=0;
    $(document).on('click','.add-record',function(){
        var rowCount = $('#tableID tr').length;
        if(rowCount==0){
          prescription_rows=1
        }else{
          prescription_rows=rowCount+1;
        }
        
        var table_row = "<tr id='row" + prescription_rows + "'><td><input type='hidden' name='rows[]' value='"+prescription_rows+"' autocomplete='off'><input type='hidden' name='medicine_cat_"+prescription_rows+"' value='1'><select class='form-control select2 medicine_name' data-rowId='"+prescription_rows+"'  name='medicine_"+prescription_rows+"' id='search-query"+prescription_rows+"'><option value=''><?php echo $this->lang->line('select') ?></option><?php foreach ($medicineName as $mkey => $mvalue) { ?><option value='<?php echo $mvalue["id"]; ?>'><?php echo str_replace(["\r", "\n"], ' ', addslashes($mvalue["medicine_name"])); ?></option><?php } ?></select><small id='stock_info_"+prescription_rows+"'></small></td><td><select class='form-control select2 medicine_dosage'  name='dosage_"+prescription_rows+"' id='search-dosage"+prescription_rows+"'><option value=''><?php echo $this->lang->line('select'); ?></option><?php foreach ($dosage as $dkey => $dvalue) { ?><option value='<?php echo $dvalue["id"]; ?>'><?php echo str_replace(["\r", "\n"], ' ', addslashes($dvalue["dosage"] . ' (' . $dvalue["unit"] . ')')); ?></option><?php } ?></select></td><td><select class='form-control select2 interval_dosage'  name='interval_dosage_"+prescription_rows+"' id='search-interval-dosage"+prescription_rows+"'><option value='<?php echo set_value('interval_dosage_id'); ?>'><?php echo $this->lang->line('select'); ?></option><?php foreach ($intervaldosage as $dkey => $dvalue) { ?><option value='<?php echo $dvalue["id"]; ?>'><?php echo $dvalue["name"] ?></option><?php } ?></select></td><td><select class='form-control select2 duration_dosage'  name='duration_dosage_"+prescription_rows+"' id='search-duration-dosage"+prescription_rows+"'><option value='<?php echo set_value('duration_dosage_id'); ?>'><?php echo $this->lang->line('select') ?></option><?php foreach ($durationdosage as $dkey => $dvalue) { ?><option value='<?php echo $dvalue["id"]; ?>'><?php echo $dvalue["name"] ?></option><?php } ?></select></td><td><textarea name='instruction_"+prescription_rows+"' rows='1' class='form-control sh-instruction-textarea-2'></textarea></td><td class='text-center'><button type='button' data-row-id='"+prescription_rows+"' class='btn btn-sm btn-outline-danger closebtn delete_row_prescription'><i class='fa fa-remove'></i></button></td></tr>";

        $('#tableID').append(table_row).find('.select2').select2({ dropdownParent: $('#add_prescription') });
        
    });

//delete medicine row from prescription
    $(document).on('click','.delete_row_prescription',function(e){       
        var del_row_id=$(this).data('rowId');        
        var result = confirm("Delete Confirm?");
        if (result) {
            $("#row" + del_row_id).html("");
        }
  });
//delete medicine row from prescription

    function delete_row(id) {
         var result = confirm("Delete Confirm?");         
            if (result == true){         
                var table = document.getElementById("tableID");
                var rowCount = table.rows.length;
                $("#row" + id).html("");   
            }     
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
                    console.log(e);
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
                            window.location.reload(true);
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
        $('#edit_deleteprescription').html("<?php if ($this->rbac->hasPrivilege('prescription', 'can_view')) { ?><a href='javascript:void(0)' onclick='printprescription(" + visitid + ")' class='btn btn-sm btn-light' data-bs-toggle='tooltip' title='<?php echo $this->lang->line('print'); ?>'><i class='fa fa-print'></i></a><?php } ?><?php if ($this->rbac->hasPrivilege('prescription', 'can_delete')) { ?><a href='javascript:void(0)' onclick='delete_prescription(" + visitid + ")' class='btn btn-sm btn-light' data-bs-toggle='tooltip' title='<?php echo $this->lang->line('delete'); ?>'><i class='fa fa-trash'></i></a><?php } ?>");
        shModal('prescriptionview').show();
    }

    function viewmanual_prescription(visitid) {
        $.ajax({
            url: '<?php echo base_url(); ?>admin/prescription/getPrescriptionmanual/' + visitid ,
            success: function (res) {
                $("#getdetails_prescriptionmanual").html(res);
                $('#edit_deleteprescriptionmanual').html("<?php if ($this->rbac->hasPrivilege('prescription', 'can_view')) { ?><a href='#' onclick='printprescriptionmanual(" + visitid + ")' data-bs-toggle='tooltip' title='<?php echo $this->lang->line('print'); ?>' class='btn btn-sm btn-light'><i class='fa fa-print'></i></a><?php } ?>");
            },
            error: function () {
                alert("Fail")
            }
        });
        shModal('prescriptionviewmanual').show();
    }
</script>
<link rel="stylesheet" href="<?php echo base_url(); ?>backend/dist/css/animate.min.css">
<script type="text/javascript">
    var opd_sidebar_loaded   = false;
    var opd_sidebar_cur_page = 1;
    var opd_sidebar_total_pages = 1;
    var opd_tab_type   = <?php echo (int) $tab_type; ?>;
    var opd_current_id = <?php echo (int) $opdid; ?>;
    var opd_prefix     = '<?php echo $opd_prefix; ?>';
    var opd_sidebar_base_url = '<?php echo base_url('admin/patient/visitdetails/'); ?>';
    var opd_sidebar_ajax_url = '<?php echo base_url('admin/patient/getopdsidebarpage/'); ?>';

    function opdSidebarEsc(s) { return $('<div>').text(s == null ? '' : s).html(); }

    function loadOpdSidebar(page) {
        var search = $('#opd_sidebar_search').val();
        $('#opd_sidebar_content').fadeTo(100, 0.3, function() {
            $.getJSON(opd_sidebar_ajax_url + opd_tab_type + '/' + page + '?search=' + encodeURIComponent(search), function(res) {
                opd_sidebar_cur_page    = res.page;
                opd_sidebar_total_pages = Math.ceil(res.total / res.per_page);

                if (res.total > 0) { $('#opd_sidebar_total').text(res.total).show(); } else { $('#opd_sidebar_total').hide(); }

                var html = '';
                if (!res.patients || res.patients.length === 0) {
                    html = '<div class="ss-empty"><i class="fa fa-user-slash"></i><div>No patients found</div></div>';
                } else {
                    html = '<ul class="ss-list">';
                    $.each(res.patients, function(i, p) {
                        var isActive = (p.opd_id == opd_current_id);
                        var name     = opdSidebarEsc(p.patient_name);
                        var pid      = opdSidebarEsc(p.patient_id);
                        var initial  = (p.patient_name || '?').charAt(0).toUpperCase();
                        html += '<li class="ss-item' + (isActive ? ' active' : '') + '">';
                        html += '<a class="ss-link" href="' + opd_sidebar_base_url + p.patient_id + '/' + p.opd_id + '/' + opd_tab_type + '" title="' + name + ' (' + pid + ')">';
                        html +=   '<div class="ss-avatar">' + initial + '</div>';
                        html +=   '<div class="ss-info">';
                        html +=     '<div class="ss-name">' + name + '</div>';
                        html +=     '<div class="ss-meta"><i class="fa fa-hashtag me-1"></i>ID: ' + pid + '</div>';
                        html +=   '</div>';
                        html +=   '<span class="ss-badge">' + res.opd_prefix + p.opd_id + '</span>';
                        html += '</a></li>';
                    });
                    html += '</ul>';
                }

                $('#opd_sidebar_content').html(html).fadeTo(200, 1);

                if (opd_sidebar_total_pages > 1) {
                    $('#opd_sidebar_pageinfo').text(page + ' / ' + opd_sidebar_total_pages);
                    $('#opd_sidebar_prev').prop('disabled', page <= 1);
                    $('#opd_sidebar_next').prop('disabled', page >= opd_sidebar_total_pages);
                    $('#opd_sidebar_pagination').show();
                } else {
                    $('#opd_sidebar_pagination').hide();
                }
            }).fail(function() {
                $('#opd_sidebar_content').html('<div class="ss-empty text-danger"><i class="fa fa-exclamation-circle"></i><div>Failed to load</div></div>').fadeTo(200, 1);
            });
        });
    }

    $(document).ready(function () {
        $('.ph-tabs a').removeClass('active');
        $('.tab-pane').removeClass('active show');
        $('a[href="#overview"]').addClass('active');
        $('#overview').addClass('active show');

        // Auto-load sidebar data on page load (sidebar is visible by default)
        if (opd_tab_type > 0 && !opd_sidebar_loaded) {
            opd_sidebar_loaded = true;
            loadOpdSidebar(1);
        }

        $(".dshow").click(function () {
            $('.dshow').hide();
            $('.dhide').show();
            $('.itemcol').removeClass('col-md-12').addClass('col-md-10');
            $('.sidebarlists').fadeIn(300);
            if (!opd_sidebar_loaded) {
                opd_sidebar_loaded = true;
                loadOpdSidebar(1);
            }
        });

        $(".dhide").click(function () {
            $('.dshow').show();
            $('.dhide').hide();
            $('.sidebarlists').fadeOut(300, function () {
                $('.itemcol').addClass('col-md-12').removeClass('col-md-10');
            });
        });

        $(document).on('click', '#opd_sidebar_prev', function() {
            if (opd_sidebar_cur_page > 1) loadOpdSidebar(opd_sidebar_cur_page - 1);
        });

        $(document).on('click', '#opd_sidebar_next', function() {
            if (opd_sidebar_cur_page < opd_sidebar_total_pages) loadOpdSidebar(opd_sidebar_cur_page + 1);
        });

        var opd_search_timer;
        $(document).on('keyup', '#opd_sidebar_search', function() {
            clearTimeout(opd_search_timer);
            opd_search_timer = setTimeout(function() {
                loadOpdSidebar(1);
            }, 400);
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
                url: '<?php echo base_url(); ?>admin/patient/addvisitDetails',
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

                    $("#formrevisitbtn").btnReset();
                },
                error: function () {
                    
                }
            });
        }));
    });

    function renderVisitAvatar(imageUrl, patientName) {
        var name = (patientName || '').replace(/\s*\([^)]*\)\s*/g, ' ').trim();
        var hasRealImage = imageUrl && imageUrl !== '' && imageUrl.indexOf('no_image') === -1;
        if (hasRealImage) {
            $("#patient_image").attr("src", "<?php echo base_url(); ?>" + imageUrl + "<?php echo img_time(); ?>").removeClass('d-none');
            $("#patient_image_initials").text('').addClass('d-none');
        } else {
            var parts = name.split(/\s+/).filter(Boolean);
            var initials = parts.length === 0 ? '?'
                : parts.length === 1 ? parts[0].charAt(0)
                : parts[0].charAt(0) + parts[parts.length - 1].charAt(0);
            $("#patient_image").addClass('d-none').removeAttr('src');
            $("#patient_image_initials").text(initials.toUpperCase()).removeClass('d-none');
        }
    }

    function makeid(length) {
        var result = '';
        var characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        var charactersLength = characters.length;
        for (var i = 0; i < length; i++) {
            result += characters.charAt(Math.floor(Math.random() * charactersLength));
        }
        return result;
    }

    function getRevisitRecord(visitid) 
    {      
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
                renderVisitAvatar(data.image, data.patient_name);
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
                shModal('revisitModal').show();
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
        $.ajax({
            url: '<?php echo base_url(); ?>admin/prescription/prescriptionmanual_print/' + visitid,
            type: 'POST',
            success: function (result) {
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
                console.log(res);
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
        $('#charge_category').html("<option value='l'><?php echo $this->lang->line('select') ?></option>");
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

    function update_amount(object_model){      
        
        let  apply_charge= object_model.find('.apply_charge').val();      
        let  discount_percentage=object_model.find('#discount_percentage').val();
        let discount_amount=(parseFloat(apply_charge) * discount_percentage/100);
        let  tax_percentage=object_model.find('#percentage').val();
        if(tax_percentage !='' && tax_percentage !=0){
            apply_amount=(parseFloat(apply_charge-discount_amount) * tax_percentage/100)+((parseFloat(apply_charge))-(discount_amount));      
            object_model.find('#revisit_amount').val(apply_amount.toFixed(2));            
            object_model.find('.paid_amount').val(apply_amount.toFixed(2));            
        }
    }

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
                    }else{
                        if(res.status == 2){
                            errorMsg(res.msg);
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
    
    $(document).on('select2:select','.charge_type',function(charge_type,charge_category){   
        var charge_type=$(this).val();     
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
                    $('.charge_category').html("<option value=''><?php echo $this->lang->line('select'); ?></option>"); //loading
                    $('.charge_category').append(div_data);
                    $('.charge_category').select2("val", charge_category);
                    $('.addcharge').select2("val", '');
                }
            });
        }
    }    

    $(document).on('select2:select','.editcharge_type',function(charge_type,charge_category){
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

    $(document).on('select2:select','.charge_category',function(){
        var charge_category=$(this).val();      
        $('.charge').html("<option value=''><?php echo $this->lang->line('loading') ?></option>");   
        $('.addcharge').html("<option value=''><?php echo $this->lang->line('loading') ?></option>");
        $('#percentage').val('');
        $('#standard_chargevisit').val('');
        $('#apply_chargevisit').val('');
        $('#revisit_amount').val('');
        $('#paid_amount').val('');                  
        $('#discount_percentage').val('');  
        $('#discount_percentage_amount').val(0);
        $('#charge_id').val('').trigger('change');    
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
                    $('.charge').html(div_data).trigger('change');
                    $('.addcharge').html(div_data).trigger('change');
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
                $('.charge').html(div_data).trigger('change');
                $('.editcharge').html(div_data).trigger('change');

                $('.editcharge').val(charge_id).trigger('change');    
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
                        console.log(e);
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
             $("#add_chargesbtn").btnLoading();
               
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
                        var row_id=makeid(8);                
                
                        var charge='<tr id="'+row_id+'"><td>'+data.date+'<input type="hidden" name="pre_date[]" value="'+data.date+'"></td><td>'+data.charge_type_name+'</td><td>'+data.charge_category+'</td><td>'+data.charge_name+'<input type="hidden" name="pre_charge_id[]" value="'+data.charge_id+'"><br><h6>'+data.note+'<input type="hidden" name="pre_note[]" value="'+data.note+'"></h6></td><td class="text-end">'+data.standard_charge+'<input type="hidden" name="pre_standard_charge[]" value="'+data.standard_charge+'"><input type="hidden" name="pre_tax_percentage[]" value="'+data.tax_percentage+'"></td><td class="text-end">'+data.tpa_charge+'<input type="hidden" name="pre_tpa_charges[]" value="'+data.tpa_charge+'"></td><td class="text-end">'+data.qty+'<input type="hidden" name="pre_qty[]" value="'+data.qty+'"></td><td class="text-end">'+data.amount+'<input type="hidden" name="pre_total[]" value="'+data.amount+'"></td><td class="text-end">'+data.discount_percentage_amount+' ('+data.discount_percentage+'%)<input type="hidden" name="pre_discount_percentage[]" value="'+data.discount_percentage+'"></td><td class="text-end">'+data.tax+' ('+data.tax_percentage+'%)<input type="hidden" name="pre_tax[]" value="'+data.tax+'"><input type="hidden" name="pre_apply_charge[]" value="'+data.apply_charge+'"></td><td class="text-end">'+data.net_amount+'<input type="hidden" name="pre_net_amount[]" value="'+data.net_amount+'"></td><td class="text-center"><button type="button" class="btn btn-sm btn-outline-danger closebtn delete_row" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('delete'); ?>" data-row-id="'+row_id+'" data-record-id="'+data.charge_id+'" autocomplete="off"><i class="fa fa-remove"></i></button></td></tr>';
                        $('#preview_charges').append(charge);
                     
                       charge_reset($('#add_chargeModal'));
                    }else{
                         successMsg(res.message);
                        window.location.reload(true);
                    }                   
                },
                error: function () {
                    $("#add_chargesbtn").btnReset();
                },
                complete: function () {
                    $("#add_chargesbtn").btnReset();
                }
            });
        }));
    });

    $(document).on('click','.delete_row',function(e){       
        var del_row_id=$(this).data('row-id');
		 
        var del_record_id=$(this).data('recordId');
        var result = confirm("<?php echo $this->lang->line('delete_confirm')?>");        
        
        if (result) {           
            $('#'+del_row_id).remove();
        }
 
        if(del_record_id > 0){
                $.ajax({
                    url: "<?php echo site_url("admin/patient/deletemedicine"); ?>",
                        type: "POST",
                        data: {prescription_detail_id:del_record_id},
                        success:function(data){ 						
                            
                        }
                });
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

    function charge_reset(_modal_div){ 
        _modal_div.find('.charge_type').select2('destroy').val('').select2();
        _modal_div.find('.addcharge').select2('destroy').val('').select2().empty();
        _modal_div.find('.charge_category').select2('destroy').val('').select2().empty();        
        $('#addstandard_charge').val('');                  
        $('#addscd_charge').val('');
        _modal_div.find('#qty').val(1);
        _modal_div.find('#apply_charge').val(0);
        _modal_div.find('#addstandard_charge').val("");
        _modal_div.find('#addscd_charge').val("");
        _modal_div.find('#charge_tax').val("");        
        _modal_div.find('#tax').val(0);
        _modal_div.find('#final_amount').val(0);
        _modal_div.find('#charge_date').val("");
        _modal_div.find('#discount_percentage_add_charge').val(0);
        _modal_div.find('#discount_percentage_amount').val(0);
        _modal_div.find('#total_charge').val(0);
    }
    
    $(document).ready(function (e) {
        $("#edit_charges").on('submit', (function (e) {
            e.preventDefault();
            let button_clicked = $("button[type=submit]",this);         
            $.ajax({
                url: base_url+'admin/charges/edit_opdcharges',
                type: "POST",
                data: new FormData(this),
                dataType: 'json',
                contentType: false,
                cache: false,
                processData: false,

                 beforeSend: function(){
                 button_clicked.btnLoading();
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
                   button_clicked.btnReset();
                },
                 error: function () {
                button_clicked.btnReset();
                },
  
                complete: function(){
                button_clicked.btnReset();
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
        $('form#add_payment').find('select option:selected').removeAttr('selected');
        $('form#add_payment').find('input:checkbox, input:radio').removeAttr('checked');
    });

$(document).on('click','.addpayment',function(){     
       shModal('myPaymentModal').show();
       $(".cheque_div").css("display","none");
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

    $(document).on('change','.visit_payment_mode',function(){
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
      dosage_opt+="<option value='"+item.id+"'>"+item.dosage+" (" +item.unit+ ") </option>";
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
             error: function(xhr) { 
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
        
        var total_charge= $('#total_charge').val();
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

$(document).on('input paste keyup','.editdiscount_percentage,#editqty', function(e){ 

update_edit_charge_amount($(e.target).closest('div.modal'));
});

let update_edit_charge_amount=(object_model)=>{
        let is_tpa= $("input:checkbox[name=edit_is_tpa]").prop('checked') ? 1 : 0;
        let quantity=  object_model.find('#editqty').val();
        let tax_percent=object_model.find('#editcharge_tax').val();
        var total_charge= object_model.find('#edit_total_charge').val();  //addedd  
        let apply_charge=isNaN(parseFloat(total_charge)*parseFloat(quantity))?0 : parseFloat(total_charge)*parseFloat(quantity); 
        let  discount_percentage=object_model.find('#editdiscount_percentage').val();
        let discount_amount=(parseFloat(apply_charge) * discount_percentage/100);
        $('#editapply_charge').val(apply_charge.toFixed(2));       
        $('#editdiscount').val(discount_amount.toFixed(2));       
        let discount_percent= 0;       
        let final_amount=apply_charge-discount_amount;      
        $('#edittax').val(((final_amount*tax_percent)/100).toFixed(2));
        $('#editfinal_amount').val((final_amount+((final_amount*tax_percent)/100)).toFixed(2));
}

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
            success: function(res) {     
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
                var discount_amount=(res.result.discount_amount);                
                $('#editdiscount').val(discount_amount);
                var tax_charge=((res.result.apply_charge-discount_amount)*res.result.percentage)/100;
                $('#edittax').val(tax_charge.toFixed(2));
                $('#edit_note').val(res.result.note);       
                $('#editcharge_type').select2('val',res.result.charge_type_master_id);
                bootstrap.Modal.getOrCreateInstance(document.getElementById('edit_chargeModal'), {backdrop:'static',keyboard:false}).show();
               
                if(res.result.organisation_id==null || res.result.organisation_id==''){
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

    $(document).on('select2:select','#add_charge_type',function(e){
        let closetst_div=$(this).closest('div.modal');   
        // reset_form(closetst_div);
    });
  
    $(document).on('select2:select','#charge_category',function(){
        let closetst_div=$(this).closest('div.modal');
        // reset_form(closetst_div);
    });

    reset_form=(_modal_div)=>{  
                    _modal_div.find('#qty').val(1);
                    _modal_div.find('#apply_charge').val(0);
                    _modal_div.find('#addstandard_charge').val("");
                    _modal_div.find('#addscd_charge').val("");
                    _modal_div.find('#charge_tax').val("");                   
                    _modal_div.find('#tax').val(0);
                    _modal_div.find('#final_amount').val(0);
                    _modal_div.find('#discount_percentage_add_charge').val(0);
    }  

    $(document).on('select2:select','.addcharge',function(e){
        let closetst_div=$(this).closest('div.modal');
        var charge=$(this).val();
        var patient_id=closetst_div.find("#patient_id").val();
        let is_tpa= $("input:checkbox[name=is_tpa]").prop('checked') ? 1 : 0;
        let organization_charge="";        
        $('#qty').val('1');
        $.ajax({
            url: '<?php echo base_url(); ?>admin/patient/getChargeById',
            type: "POST",
            data: {'charge_id': charge ,'patient_id':patient_id,'is_tpa':is_tpa},
            dataType: 'json',
            beforeSend: function() {
                // reset_form(closetst_div);
          },
            success: function (res) {
                if(res.status == 0){
                        errorMsg(res.msg);
                    }else{
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
                    var discount_percent= 0;
                    var apply_charge=isNaN(parseFloat(total_charge)*parseFloat(quantity))?0 : parseFloat(total_charge)*parseFloat(quantity);
                    var discount_amount= (apply_charge*discount_percent)/100;
                    $('#apply_charge').val(apply_charge.toFixed(2));
                    var final_amount=apply_charge-discount_amount;
                    $('#discount_percentage_amount').val( (discount_amount).toFixed(2));//added

                    $('#tax').val(((final_amount*res.result.percentage)/100).toFixed(2));

                    $('#final_amount').val((final_amount+((final_amount*res.result.percentage)/100)).toFixed(2));


                    if(res.status == 2){
                       // alert("hii");return;;
                            errorMsg(res.msg);
                    }
                }
            }
        });
    });

  $(document).on('select2:select','.editcharge',function(e){
        let closetst_div=$(this).closest('div.modal');
        var charge=$(this).val();
        var patient_id=closetst_div.find("#editpatient_id").val();
        let is_tpa= $("input:checkbox[name=edit_is_tpa]").prop('checked') ? 1 : 0;
        let organization_charge="";        
        $('#qty').val('1');
        $.ajax({
            url: '<?php echo base_url(); ?>admin/patient/getChargeById',
            type: "POST",
            data: {'charge_id': charge ,'patient_id':patient_id,'is_tpa':is_tpa},
            dataType: 'json',
            beforeSend: function() {
                reset_form(closetst_div);
          },
            success: function (res) {
                if(res.status == 0){
                        errorMsg(res.msg);
                    }else{
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
                    var discount_percent= 0;
                    var apply_charge=isNaN(parseFloat(total_charge)*parseFloat(quantity)) ? 0 : parseFloat(total_charge)*parseFloat(quantity);
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
         $('.death_status_div').removeClass('d-none');
         $('.reffer_div').addClass('d-none');
      }else if(status == "2"){
         $('.reffer_div').removeClass('d-none');
         $('.death_status_div').addClass('d-none');
      }else{
         $('.reffer_div').addClass('d-none');
         $('.death_status_div').addClass('d-none');
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
                $('.filestyle', payment_modal).dropify();
                $('.date', payment_modal).trigger("change");
                var hasDischarge = $('#discharge_card_exists', payment_modal).val() === '1';
                $('#add_paymentbtn', payment_modal).toggleClass('d-none', hasDischarge);
                payment_modal.removeClass('modal_loading');
            },
            error: function(xhr){ 
               alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");            
               
           },
           complete: function() {
                payment_modal.removeClass('modal_loading');      
            }
        });        
    });

    $(document).on('submit','#form_patient_discharge', function(e){
            e.preventDefault();
            var clicked_btn = $("button[type=submit]");           

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
    function getcategory(id,operation=null) {       
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
			 $('#viewDetailReportModal #modal_head').html(data.test);
			 
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
        if (confirm('<?php echo $this->lang->line('are_you_sure'); ?>')) {
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
</script>

<script type="text/javascript">
       $(".patient_dob").on('changeDate', function(event, date) {          
           var birth_date = $(".patient_dob").val();
           
            $.ajax({
                url: '<?php echo base_url(); ?>admin/patient/getpatientage',
                type: "POST",
                dataType: "json",
                data: {birth_date:birth_date},
                success: function (data) {
                  $('.patient_age_year').val(data.year); 
                  $('.patient_age_month').val(data.month);
                  $('.patient_age_day').val(data.day);
                }
           });
});   
</script> 
<script>
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
</script>
<script>    
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
<script>
    $('#add_vital_modal').click(function(){
        $('#modal_title').empty();
        $('#modal_title').append('<?php echo $this->lang->line('add_vital'); ?>');
    })

    $(document).on('click','.edit_vital_modal',function(){
        $('#modal_title').empty();
        $('#modal_title').append('<?php echo $this->lang->line('edit_vital'); ?>');
    })
</script>
<script>    
    function editvital(id) {
        $.ajax({
            url: '<?php echo base_url(); ?>admin/vital/editvital',
            type: "POST",
            data: {id: id},
            dataType: 'json',
            success: function (data) {              
              
                $("#evital_id").val(data.id);
                $("#vepatientid").val(data.patient_id);             
                $("#evital_name_edit").val(data.vital_id).prop("selected",true);
                $("#evital_name").val(data.vital_id);
                $("#evital_value").val(data.reference_range);
                $("#emessure_date").val(data.messure_date);
              
                if (data.status == '') {
                   
                } else
                {
                  
                }
                
                shModal('myvitalEditModal').show();

            },
        });
    }
    
     $(document).ready(function (e) {
        $("#edit_vital").on('submit', (function (e) {
            $("#edit_vitalbtn").btnLoading();
            var patient_id = $("#patient_id").val();
            e.preventDefault();
            $.ajax({
                url: "<?php echo site_url("admin/vital/editpatientvital") ?>",
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
                    $("#edit_vitalbtn").btnReset();
                },
                error: function (e) {
                    alert("<?php echo $this->lang->line('fail'); ?>");
                    console.log(e);
                }
            });
        }));
    });
    
    function delete_patient_vital(id) { 
        var msg = '<?php echo $this->lang->line('are_you_sure'); ?>';
        if (confirm(msg)) {
            $.ajax({
                url: base_url + 'admin/vital/delete_patient_vital/'+id,
                success: function (res) {
                    window.location.reload(true);
                },
                error: function () {
                    alert("Fail")
                }
            });
        }
    }
</script>
<script>
    function viewantenatal(visitid) {
        $.ajax({
            url: '<?php echo base_url(); ?>admin/antenatal/getantenatalprescription/' + visitid + '/opd',
            success: function (res) {
                $("#getdetails_finding").html(res);
            },
            error: function () {
                alert("<?php echo $this->lang->line('fail'); ?>")
            }
        }); 

        shModal('findingview').show();
    }

     function printantenatalprescription(visitid) {
        var base_url = '<?php echo base_url() ?>';
        $.ajax({
            url: base_url + 'admin/antenatal/printantenatalprescription' ,
            type: 'GET',
            data: { visitid: visitid },
            dataType:"JSON",

            success: function (result) {
                popup(result.page);
            }
        });
    }
</script>
<script>
    function edit_antenatalprescription(id) {
        $("#antenatal_title").html('<?php echo $this->lang->line('edit_antenatal_finding'); ?>');
        $.ajax({
            url: base_url+'admin/antenatal/editantenatalprescription',
            dataType:'JSON',
            data:{'antenatal_id':id} ,
            type:"POST",
             beforeSend: function() {
                  
              },
               success: function (res) {
                shModal('findingview').hide();
                $('.modal-body',"#obstetric_prescription").html(res.page);
                // #datetimepicker auto-initialized via .datetime class + event delegation
                var medicineTable= $('.modal-body',"#add_prescription").find('table#tableID');
                medicineTable.find('.select2').select2();
                shModal('obstetric_prescription').show();
                $('.filestyle','#obstetric_prescription').dropify();
             },

              complete: function() {
               
             },
             error: function(xhr) { // if error occured
              alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");              
         }                                                                                    
        });
    }
</script>
<script>
    $("#antenatal_prescription").on('submit', (function (e) {          
           
            var sub_btn_clicked = $("button[type=submit][clicked=true]");   
            var sub_btn_clicked_name=sub_btn_clicked.attr('name');
            e.preventDefault();

            $.ajax({
                url: '<?php echo base_url(); ?>admin/antenatal/add_antenatalprescription',
                type: "POST",
                data: new FormData(this),
                dataType: 'json',
                contentType: false,                
                processData: false,
                  beforeSend: function() {
                    sub_btn_clicked.btnLoading() ; 
                 },
                success: function (data) {
                    if (data.status == "0") {
                        var message = "";
                        $.each(data.error, function (index, value) {
                            message += value;
                        });
                        errorMsg(message);
                    } else {
                        successMsg(data.message);                       
                        shModal('obstetric_prescription').hide();
                        $('.ajaxlist').DataTable().ajax.reload();                        
                    }
                      sub_btn_clicked.btnReset()  ;
                },
                 error: function(xhr) { // if error occured
                    alert("Error occured.please try again");
                   sub_btn_clicked.btnReset()  ;
                },
                 complete: function() {
                     sub_btn_clicked.btnReset();  
                 }
            }); 
        }));    
</script>

<script>
    function addantenatal(visitid) {

        $("#antenatal_title").html('<?php echo $this->lang->line('add_antenatal_finding'); ?>');
        var patient_id = $("#patient_id").val();
        var ipdid="";      
         $.ajax({
            url: base_url+'admin/antenatal/addantenatalprescription',
            dataType:'JSON',
            data:{'visit_detail_id':visitid,'ipdid':ipdid,'patient_id':patient_id},
            type:"POST",
             beforeSend: function() {
              },
               success: function (res) {
                ['compose-textareaneww', 'compose-textareass'].forEach(function(id) {
                    if (window.CKEDITOR && CKEDITOR.instances[id]) { CKEDITOR.instances[id].destroy(true); }
                });
                $('.modal-body',"#obstetric_prescription").html(res.page);
                $('.modal-body',"#obstetric_prescription").find('table').find('.select2').select2();
                $('.modal-body',"#obstetric_prescription").find('.multiselect2').select2({
                    placeholder: 'Select',
                    allowClear: false,
                    minimumResultsForSearch: 2
                });
                shModal('obstetric_prescription').show();
                $('.filestyle','#obstetric_prescription').dropify();
                // #datetimepicker auto-initialized via .datetime class + event delegation
             },

              complete: function() {
                  if (window.CKEDITOR) {
                      CKEDITOR.replace('compose-textareaneww', { allowedContent: true });
                      CKEDITOR.replace('compose-textareass', { allowedContent: true });
                  }
             },
             error: function(xhr) { // if error occured
              alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");
         }
        });
    }

    $(document).on('select2:select','.medicine_name',function(){ 
        var row_id_val= $(this).data('rowid');
        if( $(this).val()!=""){
        $.ajax({
            type: "POST",
            url: base_url + "admin/pharmacy/get_medicine_stockinfo",
            data: {'pharmacy_id': $(this).val()},
            dataType: 'json',
            success: function (res) {
                
                $('#stock_info_'+row_id_val).html(res);
            }
        });
    }else{
        $('#stock_info_'+row_id_val).html("");
    }
    });
</script>
<!-- //========datatable end===== -->

<script type="text/javascript">    
    $(document).on('change','#edit_is_tpa',function(e){
        let modal_div=$(e.target).closest('div.modal');
        reset_edit_fields(modal_div);
    });

    function reset_edit_fields(closetst_div){
        closetst_div.find('.editcharge_type').select2('destroy').val('').select2();
        closetst_div.find('.editcharge_category').select2('destroy').val('').select2().empty();
        closetst_div.find('.editcharge').select2('destroy').val('').select2().empty();        
          $('.standard_charge',closetst_div).val('');
          $('.schedule_charge',closetst_div).val('');
          $('.discount_percentage_add_charge',closetst_div).val(0);
          $('.discount_percentage_amount',closetst_div).val(0);
          $('.charge_tax',closetst_div).val('');
          $('.tax',closetst_div).val(0);
          $('.total',closetst_div).val(0);
          $('.net_amount',closetst_div).val(0);
    }
     
    $(document).on('change','#is_tpa',function(e){
        let modal_div=$(e.target).closest('div.modal');
        reset_fields(modal_div);
    });

    function reset_fields(modal_div){       
        modal_div.find('.charge_type').select2('destroy').val('').select2();
        modal_div.find('.charge').select2('destroy').val('').select2().empty();
        modal_div.find('.charge_category').select2('destroy').val('').select2().empty();        
          $('.standard_charge').val('');
          $('.schedule_charge').val('');
          $('.discount_percentage_add_charge').val(0);
          $('.discount_percentage_amount').val(0);
          $('.charge_tax').val('');
          $('.tax').val(0);
          $('.total').val(0);
          $('.net_amount').val(0);
    }    

function reset_value(){

    $(".reset_value").val('').trigger('change');
    $(".qty").val(1);
    $(".printablea4").find('input').val(0);
    $("#preview_charges").html('');
}

</script>
<script>
(function () {
    var nav  = document.getElementById('ph_tabs_nav');
    var prev = document.getElementById('ph_tabs_prev');
    var next = document.getElementById('ph_tabs_next');
    if (!nav || !prev || !next) return;

    function updateArrows() {
        prev.classList.toggle('d-none', nav.scrollLeft <= 2);
        next.classList.toggle('d-none', nav.scrollLeft + nav.clientWidth >= nav.scrollWidth - 2);
    }

    prev.addEventListener('click', function () { nav.scrollBy({ left: -200, behavior: 'smooth' }); });
    next.addEventListener('click', function () { nav.scrollBy({ left:  200, behavior: 'smooth' }); });
    nav.addEventListener('scroll', updateArrows);
    window.addEventListener('resize', updateArrows);
    updateArrows();
})();
</script>
