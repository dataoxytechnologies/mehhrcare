<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
$genderList = $this->customlib->getGender();
$case_reference_id=$result['case_reference_id'];
$categorylist = $this->operationtheatre_model->category_list();
?>
<link rel="stylesheet" href="<?php echo base_url(); ?>backend/multiselect/css/jquery.multiselect.css">
<script src="<?php echo base_url(); ?>backend/multiselect/js/jquery.multiselect.js"></script>

<div class="row">
            <!-- IPD Sub Sidebar -->
            <div class="col-md-2 ipd_sidebarlists ipd-sidebar-px" id="ipd_sidebarlists">
                <div class="ipd-sidebar-card">
                    <div class="ipd-sidebar-head">
                        <h3 class="ipd-sidebar-title"><i class="fa fa-bed"></i> <?php echo $this->lang->line('ipd_patient'); ?></h3>
                        <span class="ipd-sidebar-count" id="ipd_sidebar_total" style="display:none;">0</span>
                    </div>
                    <div class="ipd-search-wrap">
                        <i class="fa fa-search search-icon"></i>
                        <input type="text" id="ipd_sidebar_search" class="form-control" placeholder="Search ID / Name / Bed">
                    </div>
                    <div id="ipd_sidebar_content">
                        <div class="sh-loading-pad text-center"><i class="fa fa-spinner fa-spin"></i></div>
                    </div>
                    <div id="ipd_sidebar_pagination" class="ipd-pagination" style="display:none;">
                        <button id="ipd_sidebar_prev" title="<?= $this->lang->line('previous') ?>"><i class="fa fa-chevron-left"></i></button>
                        <span id="ipd_sidebar_pageinfo" class="ipd-pagination-info"></span>
                        <button id="ipd_sidebar_next" title="<?= $this->lang->line('next') ?>"><i class="fa fa-chevron-right"></i></button>
                    </div>
                </div>
            </div>
            <!-- Main IPD Content -->
            <div class="col-md-10 ipd-pl-2 ipd_itemcol" id="ipd_itemcol">
            <div class="card">
            <div class="card border0 mb0">
            <!-- IPD Detail Wrap — page-head + tab content -->
                <div class="ipd-detail-wrap opd-profile-wrap">

                  <!-- ══ PAGE HEAD ══ -->
                  <div class="page-head">
                    <div class="ph-crumbs">
                      <span><?php echo $this->lang->line('ipd'); ?></span>
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
                            <?php if ($result['ipd_discharge'] == 'yes'): ?>
                              <span class="badge bg-secondary ms-1"><?php echo $this->lang->line('discharge'); ?></span>
                            <?php else: ?>
                              <span class="badge ms-1 sh-badge-admitted"><span></span><?php echo $this->lang->line('admitted'); ?></span>
                            <?php endif; ?>
                            <?php if ($result['is_antenatal'] == 1): ?><span class="badge ms-1 sh-badge-antenatal">Antenatal</span><?php endif; ?>
                          </h1>
                          <div class="sub">
                            <span class="mono"><?php echo html_escape($result['case_reference_id']); ?> &middot; <?php echo $this->customlib->getSessionPrefixByType('ipd_no').$ipdid; ?></span>
                            <span class="dot-sep"><?php echo $this->lang->line(strtolower($result['gender'])); ?> &middot; <?php echo $this->customlib->get_patient_current_age($result['patient_id']); ?></span>
                            <?php if (!empty($result['bed_name'])): ?><span class="dot-sep"><?php echo html_escape($result['bed_name'].' &mdash; '.$result['bedgroup_name'].' &mdash; '.$result['floor_name']); ?></span><?php endif; ?>
                            <?php if (!empty($result['name'])): ?><span class="dot-sep"><?php echo html_escape($result['name'].' '.$result['surname']); ?></span><?php endif; ?>
                            <?php if (!empty($result['date'])): ?>
                              <?php
                                $ipd_los = '';
                                try {
                                  $d = (new DateTime($result['date']))->diff(new DateTime());
                                  $ipd_los = ($d->days > 0 ? $d->days.'d ' : '').$d->h.'h';
                                } catch(Exception $e) {}
                              ?>
                              <?php if ($ipd_los): ?><span class="dot-sep">LOS <?php echo $ipd_los; ?></span><?php endif; ?>
                            <?php endif; ?>
                          </div>
                        </div>
                      </div>

                      <div class="ph-actions">
                        <a href="javascript:void(0)" onclick="getRecord('<?php echo $ipdid; ?>')" class="btn btn-sm ph-act ph-act-profile" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('profile'); ?>"><i class="fa fa-reorder"></i></a>
                        <?php if ($result['is_active'] != 'no' && $result['ipd_discharge'] != 'yes' && $this->rbac->hasPrivilege('ipd_patient', 'can_edit')): ?>
                          <a href="javascript:void(0)" onclick="getEditRecord('<?php echo $ipdid; ?>')" class="btn btn-sm ph-act ph-act-edit" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('edit_profile'); ?>"><i class="fa fa-pencil"></i></a>
                        <?php endif; ?>
                        <?php if (!$is_discharge && $this->rbac->hasPrivilege('ipd_patient_discharge_revert', 'can_view')): ?>
                          <a href="javascript:void(0)" onclick="discharge_revert('<?php echo $result['case_reference_id']; ?>')" class="btn btn-sm ph-act ph-act-revert" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('discharge_revert'); ?>"><i class="fa fa-undo"></i></a>
                        <?php endif; ?>
                        <?php if ($this->rbac->hasPrivilege('ipd_patient_discharge', 'can_view')): ?>
                          <a href="javascript:void(0)" class="btn btn-sm patient_discharge ph-act ph-act-discharge" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('patient_discharge'); ?>"><i class="fa fa-hospital-o"></i></a>
                        <?php endif; ?>
                        <?php if ($result['ipd_discharge'] != 'yes' && $this->rbac->hasPrivilege('ipd_patient', 'can_delete')): ?>
                          <a href="javascript:void(0)" onclick="deleteIpdPatient('<?php echo $ipdid; ?>')" class="btn btn-sm text-danger ph-act ph-act-delete" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('delete_patient'); ?>"><i class="fa fa-trash"></i></a>
                        <?php endif; ?>
                      </div>
                    </div>

                    <!-- Tab navigation -->
                    <div class="ph-tabs-wrap">
                    <button class="ph-tabs-arrow d-none" id="ph_tabs_prev" type="button"><i class="fa fa-chevron-left"></i></button>
                    <nav class="ph-tabs" id="ph_tabs_nav" role="tablist">
                      <a class="active" href="#overview" data-bs-toggle="tab" role="tab"><i class="fa fa-th"></i> <?php echo $this->lang->line('overview'); ?></a>
                      <?php if ($this->rbac->hasPrivilege('nurse_note', 'can_view')): ?><a href="#nurse_note" data-bs-toggle="tab" role="tab"><i class="fas fa-sticky-note"></i> <?php echo $this->lang->line('nurse_notes'); ?></a><?php endif; ?>
                      <?php if ($this->rbac->hasPrivilege('ipd_medication', 'can_view')): ?><a href="#medication" class="medication" data-bs-toggle="tab" role="tab"><i class="fa fa-medkit"></i> <?php echo $this->lang->line('medication'); ?></a><?php endif; ?>
                      <?php if ($this->rbac->hasPrivilege('ipd_prescription', 'can_view')): ?><a href="#prescription" data-bs-toggle="tab" role="tab"><i class="fas fa-file-prescription"></i> <?php echo $this->lang->line('prescription'); ?></a><?php endif; ?>
                      <?php if ($this->rbac->hasPrivilege('consultant_register', 'can_view')): ?><a href="#consultant_register" data-bs-toggle="tab" role="tab"><i class="far fa-calendar-check"></i> <?php echo $this->lang->line('consultant_register'); ?></a><?php endif; ?>
                      <?php if ($this->rbac->hasPrivilege('ipd_lab_investigation', 'can_view')): ?><a href="#labinvestigation" data-bs-toggle="tab" role="tab"><i class="fas fa-diagnoses"></i> <?php echo $this->lang->line('lab_investigation'); ?></a><?php endif; ?>
                      <?php if ($this->rbac->hasPrivilege('ipd_operation_theatre', 'can_view')): ?><a href="#operationtheatre" class="operationtheatre" data-bs-toggle="tab" role="tab"><i class="fas fa-cut"></i> <?php echo $this->lang->line('operations'); ?></a><?php endif; ?>
                      <?php if ($this->rbac->hasPrivilege('charges', 'can_view')): ?><a href="#charges" data-bs-toggle="tab" role="tab"><i class="fas fa-donate"></i> <?php echo $this->lang->line('charges'); ?></a><?php endif; ?>
                      <?php if ($this->rbac->hasPrivilege('payment', 'can_view')): ?><a href="#payment" data-bs-toggle="tab" role="tab"><i class="fas fa-hand-holding-usd"></i> <?php echo $this->lang->line('payments'); ?></a><?php endif; ?>
                      <?php if ($this->module_lib->hasActive('live_consultation') && $this->rbac->hasPrivilege('ipd_live_consultation', 'can_view')): ?><a href="#live_consult" class="live_consult" data-bs-toggle="tab" role="tab"><i class="fa fa-video-camera"></i> <?php echo $this->lang->line('live_consultation'); ?></a><?php endif; ?>
                      <?php if ($this->rbac->hasPrivilege('bed_history', 'can_view')): ?><a href="#bed_history" class="bed_history" data-bs-toggle="tab" role="tab"><i class="fa fa-bed"></i> <?php echo $this->lang->line('bed_history'); ?></a><?php endif; ?>
                      <?php if ($this->rbac->hasPrivilege('ipd_timeline', 'can_view')): ?><a href="#timeline" data-bs-toggle="tab" role="tab"><i class="far fa-calendar-check"></i> <?php echo $this->lang->line('timeline'); ?></a><?php endif; ?>
                      <?php if ($this->rbac->hasPrivilege('ipd_treatment_history', 'can_view')): ?><a href="#treatment_history" data-bs-toggle="tab" role="tab"><i class="fas fa-hourglass-half"></i> <?php echo $this->lang->line('treatment_history'); ?></a><?php endif; ?>
                      <?php if ($this->rbac->hasPrivilege('ipd_vitals', 'can_view')): ?><a href="#vitals" data-bs-toggle="tab" role="tab"><i class="fa fa-heartbeat"></i> <?php echo $this->lang->line('vitals'); ?></a><?php endif; ?>
                      <?php if ($result['is_antenatal'] == 1): ?>
                        <?php if ($this->rbac->hasPrivilege('ipd_previous_obstetric_history', 'can_view')): ?><a href="#obstetric_history" data-bs-toggle="tab" role="tab"><i class="fas fa-diagnoses"></i> <?php echo $this->lang->line('previous_obstetric_history'); ?></a><?php endif; ?>
                        <?php if ($this->rbac->hasPrivilege('ipd_postnatal_history', 'can_view')): ?><a href="#post_antenatal" data-bs-toggle="tab" role="tab"><i class="fas fa-diagnoses"></i> <?php echo $this->lang->line('postnatal_history'); ?></a><?php endif; ?>
                        <?php if ($this->rbac->hasPrivilege('ipd_antenatal', 'can_view')): ?><a href="#addantenatal" data-bs-toggle="tab" role="tab"><i class="fas fa-diagnoses"></i> <?php echo $this->lang->line('antenatal'); ?></a><?php endif; ?>
                      <?php endif; ?>
                    </nav>
                    <button class="ph-tabs-arrow" id="ph_tabs_next" type="button"><i class="fa fa-chevron-right"></i></button>
                    </div><!-- /.ph-tabs-wrap -->
                  </div><!-- /.page-head -->

                  <div class="tab-content p-3">
                    <div class="tab-pane active show" id="overview">


                      <!-- ══ VISIT INFORMATION BUSINESS CARD ══ -->
                      <section class="bcard">
                        <div class="bcard-head">
                          <div class="title"><?php echo $this->lang->line('overview'); ?></div>
                        </div>
                        <div class="bcard-grid">
                          <div class="field"><div class="l"><?php echo $this->lang->line('patient'); ?></div><div class="v"><?php echo composePatientName($result['patient_name'], $result['patient_id']); ?></div></div>
                          <div class="field"><div class="l"><?php echo $this->lang->line('case_id'); ?></div><div class="v mono"><?php echo html_escape($result['case_reference_id']); ?></div></div>
                          <div class="field"><div class="l"><?php echo $this->lang->line('ipd_no'); ?></div><div class="v mono"><?php echo $this->customlib->getSessionPrefixByType('ipd_no').$ipdid; ?></div></div>
                          <div class="field"><div class="l"><?php echo $this->lang->line('admission_date'); ?></div><div class="v"><?php echo $this->customlib->YYYYMMDDHisTodateFormat($result['date'], $time_format); ?></div></div>
                          <div class="field"><div class="l"><?php echo $this->lang->line('gender'); ?></div><div class="v"><?php echo $this->lang->line(strtolower($result['gender'])); ?></div></div>
                          <div class="field"><div class="l"><?php echo $this->lang->line('age'); ?></div><div class="v"><?php echo $this->customlib->get_patient_current_age($result['patient_id']); ?></div></div>
                          <div class="field"><div class="l"><?php echo $this->lang->line('phone'); ?></div><div class="v"><?php echo html_escape($result['mobileno']); ?></div></div>
                          <div class="field"><div class="l"><?php echo $this->lang->line('guardian_name'); ?></div><div class="v"><?php echo html_escape($result['guardian_name']); ?></div></div>
                          <div class="field"><div class="l"><?php echo $this->lang->line('bed'); ?></div><div class="v"><?php echo html_escape($result['bed_name'].' — '.$result['bedgroup_name'].' — '.$result['floor_name']); ?></div></div>
                          <div class="field"><div class="l"><?php echo $this->lang->line('tpa'); ?></div><div class="v"><?php if (isset($result['organisation_name'])) echo html_escape($result['organisation_name']); ?></div></div>
                          <div class="field"><div class="l"><?php echo $this->lang->line('tpa_id'); ?></div><div class="v"><?php if (isset($result['insurance_id'])) echo html_escape($result['insurance_id']); ?></div></div>
                          <div class="field"><div class="l"><?php echo $this->lang->line('tpa_validity'); ?></div><div class="v"><?php if (!empty($result['insurance_validity'])) echo $this->customlib->YYYYMMDDTodateFormat($result['insurance_validity']); ?></div></div>
                          <div class="field">
                            <div class="l"><?php echo $this->lang->line('barcode'); ?></div>
                            <div class="v"><?php if (file_exists($this->customlib->getFolderPath().'./uploads/patient_id_card/barcodes/'.$patient_id.'.png')) { ?><a href="<?php echo $this->media_storage->getImageURL('./uploads/patient_id_card/barcodes/'.$patient_id.'.png'); ?>" target="_blank">
                                <img  class="sh-qr-code"  src="<?php echo $this->media_storage->getImageURL('./uploads/patient_id_card/barcodes/'.$patient_id.'.png'); ?>" width="80" height="22"></a><?php } ?></div>
                          </div>
                          <div class="field">
                            <div class="l"><?php echo $this->lang->line('qrcode'); ?></div>
                            <div class="v"><?php if (file_exists($this->customlib->getFolderPath().'./uploads/patient_id_card/qrcode/'.$patient_id.'.png')) { ?><a href="<?php echo $this->media_storage->getImageURL('./uploads/patient_id_card/qrcode/'.$patient_id.'.png'); ?>" target="_blank">
                                <img  class="sh-qr-code"  src="<?php echo $this->media_storage->getImageURL('./uploads/patient_id_card/qrcode/'.$patient_id.'.png'); ?>" width="48" height="48"></a><?php } ?></div>
                          </div>
                        </div>
                      </section><!-- /.bcard -->

                      <!-- ══ RELATED LISTS ══ -->
                      <div class="rellist-wrap">

                        <!-- Billing Overview -->
                        <?php
                          $rl_has_graph = $this->module_lib->hasActive('ipd')||$this->module_lib->hasActive('pharmacy')||$this->module_lib->hasActive('pathology')||$this->module_lib->hasActive('radiology')||$this->module_lib->hasActive('blood_bank')||$this->module_lib->hasActive('ambulance');
                        ?>
                        <?php if ($rl_has_graph): ?>
                        <div class="rellist">
                          <div class="rellist-head">
                            <div class="l">
                              <div class="ic green"><i class="fa fa-bar-chart"></i></div>
                              <div class="title"><?php echo $this->lang->line('overview'); ?></div>
                            </div>
                          </div>
                          <div class="rellist-body pad">
                            <div class="ov-tile-grid">
                              <?php foreach ([
                                'ipd'       => ['label'=>$this->lang->line('ipd_billing_payment_graph'),       'icon'=>'fa fa-bed', 'key'=>'ipd',       'ratio_key'=>'ipd_bill_payment_ratio'],
                                'pharmacy'  => ['label'=>$this->lang->line('pharmacy_billing_payment_graph'),  'icon'=>'fas fa-mortar-pestle','key'=>'pharmacy', 'ratio_key'=>'pharmacy_bill_payment_ratio'],
                                'pathology' => ['label'=>$this->lang->line('pathology_billing_payment_graph'), 'icon'=>'fas fa-flask',    'key'=>'pathology',  'ratio_key'=>'pathology_bill_payment_ratio'],
                                'radiology' => ['label'=>$this->lang->line('radiology_billing_payment_graph'), 'icon'=>'fas fa-x-ray',   'key'=>'radiology',  'ratio_key'=>'radiology_bill_payment_ratio'],
                                'blood_bank'=> ['label'=>$this->lang->line('blood_bank_billing_payment_graph'),'icon'=>'fas fa-tint',    'key'=>'blood_bank', 'ratio_key'=>'blood_bank_bill_payment_ratio'],
                                'ambulance' => ['label'=>$this->lang->line('ambulance_billing_payment_graph'), 'icon'=>'fas fa-ambulance','key'=>'ambulance', 'ratio_key'=>'ambulance_bill_payment_ratio'],
                              ] as $rl_mod => $rl_bg):
                                if (!$this->module_lib->hasActive($rl_mod)) continue;
                                $rl_ratio = isset($graph[$rl_bg['key']][$rl_bg['ratio_key']]) ? $graph[$rl_bg['key']][$rl_bg['ratio_key']] : 0;
                              ?>
                                <div class="ov-module">
                                  <div class="ov-module-head">
                                    <div class="ov-module-label"><?php echo $rl_bg['label']; ?></div>
                                    <i class="<?php echo $rl_bg['icon']; ?> ov-module-icon"></i>
                                  </div>
                                  <div class="ov-module-value"><?php echo $rl_ratio; ?>%</div>
                                  <div class="ov-module-bar">
                                    <div class="ov-module-fill" style="--w:<?php echo $rl_ratio; ?>%;"></div>
                                  </div>
                                </div>
                              <?php endforeach; ?>
                            </div>
                          </div>
                        </div>
                        <?php endif; ?>

                        <!-- Clinical Alerts: Allergies + Symptoms + ICD -->
                        <?php
                          $rl_has_alerts = !empty($result['known_allergies']) || !empty($result['symptoms']) || !empty($icd_codes);
                        ?>
                        <?php if ($rl_has_alerts): ?>
                        <div class="rellist sh-rellist-red">
                          <div class="rellist-head">
                            <div class="l">
                              <div class="ic red"><i class="fa fa-exclamation-triangle"></i></div>
                              <div class="title"><?php echo $this->lang->line('alerts'); ?></div>
                            </div>
                          </div>
                          <div class="rellist-body pad">
                            <div class="ov-tile-grid">
                              <?php if (!empty($result['known_allergies'])): foreach (explode(',', $result['known_allergies']) as $rl_al): $rl_al = trim($rl_al); if (!$rl_al) continue; ?>
                                <div class="ov-tile ov-alert red">
                                  <div class="ov-tile-label"><i class="fa fa-exclamation-circle me-1"></i><?php echo $this->lang->line('allergy'); ?></div>
                                  <div class="ov-tile-value"><?php echo html_escape($rl_al); ?></div>
                                </div>
                              <?php endforeach; endif; ?>
                              <?php if (!empty($result['symptoms'])): foreach (explode(',', $result['symptoms']) as $rl_sym): $rl_sym = trim($rl_sym); if (!$rl_sym) continue; ?>
                                <div class="ov-tile ov-alert amber">
                                  <div class="ov-tile-label"><i class="fa fa-notes-medical me-1"></i><?php echo $this->lang->line('symptoms'); ?></div>
                                  <div class="ov-tile-value"><?php echo html_escape($rl_sym); ?></div>
                                </div>
                              <?php endforeach; endif; ?>
                              <?php if (!empty($icd_codes)): foreach ($icd_codes as $rl_icd): ?>
                                <div class="ov-tile ov-alert blue">
                                  <div class="ov-tile-label"><i class="fa fa-code me-1"></i>ICD-10</div>
                                  <div class="ov-tile-value"><?php echo html_escape('['.$rl_icd['icd_code'].'] '.$rl_icd['icd_description']); ?></div>
                                </div>
                              <?php endforeach; endif; ?>
                            </div>
                          </div>
                        </div>
                        <?php endif; ?>

                        <!-- Current Vitals -->
                        <?php if ($this->rbac->hasPrivilege('ipd_vitals', 'can_view') && !empty($patientcurrentvital)): ?>
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
                            <div class="ov-tile-grid tight">
                              <?php foreach ($patientcurrentvital as $rl_vital):
                                $rl_vrange = $this->lang->line('normal'); $rl_vcol = 'var(--border,#e3e6ea)';
                                if (strpos($rl_vital['reference_range'],'-')!==false) {
                                  $rl_rng = explode('-',$rl_vital['reference_range']); $rl_rmin=$rl_rng[0]; $rl_rmax=$rl_rng[1];
                                  if (strpos($rl_vital['patient_range'],'-')!==false) {
                                    $rl_vp=explode('-',$rl_vital['patient_range']);
                                    if($rl_vp[0]<$rl_rmin||$rl_vp[1]>$rl_rmax){$rl_vrange=$this->lang->line('abnormal');$rl_vcol='var(--red,#dc3545)';}
                                  } else {
                                    if($rl_vital['patient_range']<$rl_rmin){$rl_vrange=$this->lang->line('low');$rl_vcol='var(--blue,#0d6efd)';}
                                    if($rl_vital['patient_range']>$rl_rmax){$rl_vrange=$this->lang->line('high');$rl_vcol='var(--red,#dc3545)';}
                                  }
                                }
                                $rl_bmi = '';
                                if ($rl_vital['id']=='4' && $rl_height > 0 && $rl_weight > 0) {
                                  $rl_bmi = ' (BMI '.round($rl_weight/(($rl_height/100)*($rl_height/100)),1).')';
                                }
                              ?>
                                <div class="ov-tile ov-vital" style="--v-col:<?php echo $rl_vcol; ?>;">
                                  <div class="ov-tile-label"><?php echo html_escape($rl_vital['name']); ?></div>
                                  <div class="ov-tile-value">
                                    <?php echo html_escape($rl_vital['patient_range']); ?>
                                    <span class="ov-tile-sub"><?php echo html_escape($rl_vital['unit']).$rl_bmi; ?></span>
                                  </div>
                                  <div class="ov-vital-status"><?php echo $rl_vrange; ?></div>
                                </div>
                              <?php endforeach; ?>
                            </div>
                          </div>
                        </div>
                        <?php endif; ?>

                        <!-- Credit Limit -->
                        <?php if ($credit_limit > 0): ?>
                        <div class="rellist">
                          <div class="rellist-head">
                            <div class="l">
                              <div class="ic green"><i class="fa fa-credit-card"></i></div>
                              <div class="title"><?php echo $this->lang->line('credit_limit'); ?></div>
                            </div>
                          </div>
                          <div class="rellist-body pad">
                            <?php
                              $rl_credit_pct = ($credit_limit > 0) ? min(100, round(($used_credit_limit / $credit_limit) * 100)) : 0;
                              $rl_credit_col = $rl_credit_pct >= 90 ? 'var(--red,#dc3545)' : ($rl_credit_pct >= 60 ? 'var(--amber,#f59e0b)' : 'var(--green,#198754)');
                            ?>
                            <div class="ov-credit-stats">
                              <div class="ov-credit-stat">
                                <div class="ov-credit-label"><i class="fa fa-wallet me-1"></i><?php echo $this->lang->line('credit_limit'); ?></div>
                                <div class="ov-credit-value"><?php echo $currency_symbol.number_format($credit_limit,2); ?></div>
                              </div>
                              <div class="ov-credit-stat">
                                <div class="ov-credit-label"><i class="fa fa-arrow-up me-1"></i><?php echo $this->lang->line('used_credit_limit'); ?></div>
                                <div class="ov-credit-value ov-credit-v-red"><?php echo $currency_symbol.number_format($used_credit_limit,2); ?></div>
                              </div>
                              <div class="ov-credit-stat">
                                <div class="ov-credit-label"><i class="fa fa-check-circle me-1"></i><?php echo $this->lang->line('balance_credit_limit'); ?></div>
                                <div class="ov-credit-value ov-credit-v-green"><?php echo $currency_symbol.number_format($balance_credit_limit,2); ?></div>
                              </div>
                            </div>
                            <div class="ov-credit-bar">
                              <div class="ov-credit-fill" style="--ov-fill-w:<?php echo $rl_credit_pct; ?>%;--ov-fill-c:<?php echo $rl_credit_col; ?>;"></div>
                            </div>
                            <div class="ov-credit-pct-text"><?php echo $rl_credit_pct; ?>% <?php echo $this->lang->line('used_credit_limit'); ?></div>
                          </div>
                        </div>
                        <?php endif; ?>

                        <!-- Consultant Doctors -->
                        <div class="rellist">
                          <div class="rellist-head">
                            <div class="l">
                              <div class="ic blue"><i class="fa fa-user-md"></i></div>
                              <div class="title"><?php echo $this->lang->line('consultant_doctor'); ?></div>
                            </div>
                            <div class="r">
                              <?php if ($is_discharge && ($this->rbac->hasPrivilege('ipd_patient', 'can_add') || $this->rbac->hasPrivilege('ipd_patient', 'can_edit'))): ?>
                                <a href="#" class="btn btn-sm btn-outline-primary adddoctor" onclick="get_doctoripd('<?php echo $ipdid; ?>')" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('add_doctor'); ?>"><i class="fa fa-plus"></i></a>
                              <?php endif; ?>
                            </div>
                          </div>
                          <div class="rellist-body pad">
                            <div class="ov-doctors-wrap">
                              <!-- Primary attending doctor -->
                              <?php $rl_doc_initial = mb_strtoupper(mb_substr(trim($result['name'].' '.$result['surname']), 0, 1)); ?>
                              <div class="ov-doctor">
                                <div class="ov-doctor-avatar">
                                  <?php if (!empty($result['doctor_image'])): ?>
                                    <img src="<?php echo $this->media_storage->getImageURL('uploads/staff_images/'.$result['doctor_image']); ?>" alt="">
                                  <?php else: ?>
                                    <?php echo $rl_doc_initial; ?>
                                  <?php endif; ?>
                                </div>
                                <div>
                                  <div class="ov-doctor-name"><?php echo html_escape($result['name'].' '.$result['surname']); ?></div>
                                  <div class="ov-doctor-id"><i class="fa fa-id-badge me-1"></i><?php echo html_escape($result['employee_id']); ?></div>
                                </div>
                              </div>
                              <!-- Additional consultant doctors -->
                              <?php foreach ($doctors_ipd as $rl_dk => $rl_dv):
                                if ($roles == 7 || $logged_staff_id == $rl_dv['id'] || $roles != 7 || $doctor_restriction == 'disabled'):
                                  $rl_dname    = $rl_dv['ipd_doctorname'].' '.$rl_dv['ipd_doctorsurname'];
                                  $rl_dinitial = mb_strtoupper(mb_substr(trim($rl_dname), 0, 1));
                              ?>
                                <div class="ov-doctor">
                                  <div class="ov-doctor-avatar alt">
                                    <?php if (!empty($rl_dv['image'])): ?>
                                      <img src="<?php echo $this->media_storage->getImageURL('uploads/staff_images/'.$rl_dv['image']); ?>" alt="">
                                    <?php else: ?>
                                      <?php echo $rl_dinitial; ?>
                                    <?php endif; ?>
                                  </div>
                                  <div>
                                    <div class="ov-doctor-name"><?php echo html_escape($rl_dname); ?></div>
                                    <div class="ov-doctor-id"><i class="fa fa-id-badge me-1"></i><?php echo html_escape($rl_dv['employee_id']); ?></div>
                                  </div>
                                  <?php if ($this->rbac->hasPrivilege('ipd_patient', 'can_delete')): ?>
                                    <a class="ov-doctor-remove" onclick="delete_record('<?php echo base_url(); ?>admin/patient/delete_doctors/<?php echo $result['patient_id']; ?>/<?php echo $ipdid; ?>/<?php echo $rl_dv['id']; ?>','<?php echo $this->lang->line('delete_message'); ?>')" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('remove'); ?>">
                                      <i class="fa fa-times"></i>
                                    </a>
                                  <?php endif; ?>
                                </div>
                              <?php endif; endforeach; ?>
                            </div>
                          </div>
                        </div>

                        <!-- Nurse Notes (latest) -->
                        <?php if ($this->rbac->hasPrivilege('nurse_note', 'can_view') && !empty($nurse_note)): ?>
                        <div class="rellist">
                          <div class="rellist-head">
                            <div class="l">
                              <div class="ic violet"><i class="fas fa-sticky-note"></i></div>
                              <div class="title"><?php echo $this->lang->line('nurse_notes'); ?></div>
                              <div class="count"><?php echo count($nurse_note); ?></div>
                            </div>
                          </div>
                          <div class="rellist-body pad" id="timeline_list">
                            <div class="rl-tl">
                              <?php for ($rl_i=0; $rl_i<$recent_record_count; $rl_i++): if (empty($nurse_note[$rl_i])) continue; $rl_nn = $nurse_note[$rl_i]; ?>
                              <div class="rl-tl-item">
                                <div class="rl-tl-dot violet"></div>
                                <div class="rl-tl-card">
                                  <div class="rl-tl-meta">
                                    <span class="rl-tl-date"><i class="far fa-clock me-1"></i><?php echo $this->customlib->YYYYMMDDHisTodateFormat($rl_nn['date'], $this->customlib->getHospitalTimeFormat()); ?></span>
                                  </div>
                                  <div class="rl-tl-title"><?php echo html_escape($rl_nn['name'].' '.$rl_nn['surname'].' ('.$rl_nn['employee_id'].')'); ?></div>
                                  <?php if (!empty($rl_nn['note'])): ?><div class="rl-tl-body"><?php echo nl2br(html_escape($rl_nn['note'])); ?></div><?php endif; ?>
                                  <?php if (!empty($rl_nn['comment'])): ?><div class="rl-tl-body"><?php echo nl2br(html_escape($rl_nn['comment'])); ?></div><?php endif; ?>
                                </div>
                              </div>
                              <?php endfor; ?>
                            </div>
                          </div>
                        </div>
                        <?php endif; ?>

                        <!-- Recent Prescriptions / Findings -->
                        <?php if (!empty($prescription_detail)): ?>
                        <div class="rellist">
                          <div class="rellist-head">
                            <div class="l">
                              <div class="ic amber"><i class="fas fa-file-prescription"></i></div>
                              <div class="title"><?php echo $this->lang->line('prescription'); ?></div>
                              <div class="count"><?php echo count($prescription_detail); ?></div>
                            </div>
                          </div>
                          <div class="rellist-body pad">
                            <div class="ov-prescription-list">
                              <?php for ($rl_i=0; $rl_i<min($recent_record_count, count($prescription_detail)); $rl_i++): if (empty($prescription_detail[$rl_i])) continue; ?>
                                <div class="ov-prescription"><?php echo html_escape($prescription_detail[$rl_i]['finding_description']); ?></div>
                              <?php endfor; ?>
                            </div>
                          </div>
                        </div>
                        <?php endif; ?>

                        <!-- Timeline (latest) -->
                        <?php if ($this->rbac->hasPrivilege('ipd_timeline', 'can_view') && !empty($timeline_list)): ?>
                        <div class="rellist">
                          <div class="rellist-head">
                            <div class="l">
                              <div class="ic blue"><i class="far fa-calendar-check"></i></div>
                              <div class="title"><?php echo $this->lang->line('timeline'); ?></div>
                              <div class="count"><?php echo count($timeline_list); ?></div>
                            </div>
                          </div>
                          <div class="rellist-body pad">
                            <div class="rl-tl">
                              <?php for ($rl_i=0; $rl_i<$recent_record_count; $rl_i++): if (empty($timeline_list[$rl_i])) continue; $rl_tl = $timeline_list[$rl_i]; ?>
                              <div class="rl-tl-item">
                                <div class="rl-tl-dot"></div>
                                <div class="rl-tl-card">
                                  <div class="rl-tl-meta">
                                    <span class="rl-tl-date"><i class="far fa-clock me-1"></i><?php echo date($this->customlib->getHospitalDateFormat(true,true), strtotime($rl_tl['timeline_date'])); ?></span>
                                    <?php if (!empty($rl_tl['document'])): ?>
                                    <span class="rl-tl-actions"><a class="btn btn-sm btn-secondary" href="<?php echo base_url().'admin/timeline/download_patient_timeline/'.$rl_tl['id']; ?>" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('download'); ?>"><i class="fa fa-download"></i></a></span>
                                    <?php endif; ?>
                                  </div>
                                  <div class="rl-tl-title"><?php echo html_escape($rl_tl['title']); ?></div>
                                  <?php if (!empty($rl_tl['description'])): ?><div class="rl-tl-body"><?php echo $rl_tl['description']; ?></div><?php endif; ?>
                                </div>
                              </div>
                              <?php endfor; ?>
                            </div>
                          </div>
                        </div>
                        <?php endif; ?>

                      </div><!-- /.rellist-wrap -->
                    </div><!-- /#overview -->

                            <?php if ($this->rbac->hasPrivilege('nurse_note', 'can_view')) { ?>
                            <div class="tab-pane card tab-content-height" id="nurse_note">
                                <div class="box-tab-header">
                                    <h3 class="box-tab-title"><?php echo $this->lang->line('nurse_notes'); ?></h3>
                                    <div class="box-tab-tools rtl-end-auto rtl-start-0">
                                        <?php if ($this->rbac->hasPrivilege('nurse_note', 'can_add') && $is_discharge) { ?>
                                            <a href="javascript:void(0)" class="btn btn-sm btn-primary addnursenote" onclick="shModal('add_nurse_note').show(); return false;">
                                                <i class="fas fa-plus"></i> <?php echo $this->lang->line('add_nurse_note'); ?>
                                            </a>
                                        <?php } ?>
                                    </div>
                                </div>

                                <div class="download_label"><?php echo composePatientName($result['patient_name'], $result['patient_id']) . " " . $this->lang->line('ipd_details'); ?></div>

                                <?php if (empty($nurse_note)) { ?>
                                    <div class="dataTables_empty">
                                        No data available in table <br><br>
                                        <img src="<?php echo base_url('backend/images/addnewitem.svg'); ?>" width="150" alt=""><br><br>
                                        <span class="text-success bolds"><i class="fa fa-arrow-left"></i> Search with different criteria.</span>
                                    </div>
                                <?php } else { ?>
                                    <div class="nn-timeline">
                                        <?php foreach ($nurse_note as $key => $value) {
                                            $id = $value['id'];
                                            $note_date = $this->customlib->YYYYMMDDHisTodateFormat($value['date'], $this->customlib->getHospitalTimeFormat());
                                        ?>
                                            <div class="nn-item">
                                                <div class="nn-card">
                                                    <div class="nn-card-header">
                                                        <span class="nn-date-badge">
                                                            <i class="fa fa-calendar"></i> <?php echo $note_date; ?>
                                                        </span>
                                                        <span class="nn-staff">
                                                            <i class="fa fa-user-md text-muted me-1"></i><?php echo $value['name'] . ' ' . $value['surname']; ?>
                                                            <span class="nn-staff-id">(<?php echo $value['employee_id']; ?>)</span>
                                                        </span>
                                                        <?php if ($is_discharge) { ?>
                                                            <div class="nn-actions btn-group btn-group-sm" role="group">
                                                                <?php if ($this->rbac->hasPrivilege('nurse_note', 'can_edit')) { ?>
                                                                    <button type="button" class="btn btn-outline-primary" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('edit'); ?>" onclick="editNursenote('<?php echo $value['id']; ?>')">
                                                                        <i class="fa fa-pencil"></i>
                                                                    </button>
                                                                    <button type="button" class="btn btn-outline-secondary" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('comment'); ?>" onclick="addcommentNursenote('<?php echo $value['id']; ?>', <?php echo $value['ipd_id']; ?>)">
                                                                        <i class="fa fa-comment"></i>
                                                                    </button>
                                                                <?php } ?>
                                                                <?php if ($this->rbac->hasPrivilege('nurse_note', 'can_delete')) { ?>
                                                                    <button type="button" class="btn btn-outline-danger" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('delete'); ?>" onclick="delete_record('<?php echo base_url(); ?>admin/patient/deleteIpdnursenote/<?php echo $value['id']; ?>/<?php echo $value['ipd_id']; ?>', '<?php echo $this->lang->line('delete_message'); ?>')">
                                                                        <i class="fa fa-trash"></i>
                                                                    </button>
                                                                <?php } ?>
                                                            </div>
                                                        <?php } ?>
                                                    </div>

                                                    <div class="nn-card-body">
                                                        <div class="nn-section-label"><?php echo $this->lang->line('note'); ?></div>
                                                        <blockquote class="nn-note"><?php echo nl2br($value['note']); ?></blockquote>

                                                        <?php if (!empty($fields_nurse)) {
                                                            $has_fields = false;
                                                            $fields_html = '';
                                                            foreach ($fields_nurse as $fields_value) {
                                                                if (!empty($fields_value->name) && !empty($value[$fields_value->name])) {
                                                                    $has_fields = true;
                                                                    $fields_html .= '<dt class="col-sm-4">' . $fields_value->name . '</dt>';
                                                                    $fields_html .= '<dd class="col-sm-8">' . $value[$fields_value->name] . '</dd>';
                                                                }
                                                            }
                                                            if ($has_fields) { ?>
                                                                <dl class="row nn-fields mt-3 mb-0"><?php echo $fields_html; ?></dl>
                                                        <?php }
                                                        } ?>

                                                        <?php if (!empty(trim(strip_tags($value['comment'])))) { ?>
                                                            <div class="nn-comment">
                                                                <div class="nn-section-label"><i class="fa fa-comment-o"></i> <?php echo $this->lang->line('comment'); ?></div>
                                                                <div><?php echo nl2br($value['comment']); ?></div>
                                                                <div class="nn-comment-meta text-end mt-2">
                                                                    <i class="fa fa-user"></i> <?php echo $this->lang->line('created_by') . ': ' . $value['cb_name'] . ' ' . $value['cb_surname'] . ' (' . $value['cb_employee_id'] . ')'; ?>
                                                                </div>
                                                            </div>
                                                        <?php } ?>
                                                    </div>

                                                    <?php foreach ($nursenote[$id] as $ckey => $cvalue) {
                                                        $c_by   = !empty($cvalue['staffname']) ? '(' . $cvalue['staffname'] . ' ' . $cvalue['staffsurname'] . ': ' . $cvalue['employee_id'] . ')' : '';
                                                        $c_date = !empty($cvalue['created_at']) ? $this->customlib->YYYYMMDDHisTodateFormat($cvalue['created_at'], $this->customlib->getHospitalTimeFormat()) : '';
                                                    ?>
                                                        <div class="nn-subcomment">
                                                            <div><i class="fa fa-reply text-muted me-1"></i><?php echo nl2br($cvalue['comment_staff']); ?>
                                                                <?php if ($is_discharge && $this->rbac->hasPrivilege('nurse_note', 'can_delete')) { ?>
                                                                    <a class="btn btn-sm btn-link text-danger p-0 ms-2" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('delete'); ?>" onclick="delete_record('<?php echo base_url(); ?>admin/patient/deletenursenotecomment/<?php echo $cvalue['id']; ?>', '<?php echo $this->lang->line('delete_message'); ?>')">
                                                                        <i class="fa fa-trash"></i>
                                                                    </a>
                                                                <?php } ?>
                                                            </div>
                                                            <div class="nn-subcomment-meta text-end"><?php echo $c_date . ' ' . $c_by; ?></div>
                                                        </div>
                                                    <?php } ?>
                                                </div>
                                            </div>
                                        <?php } ?>
                                    </div>
                                <?php } ?>
                            </div>
                            <?php } ?>

                        <?php if ($this->rbac->hasPrivilege('ipd_vitals', 'can_view')) {?>
                        <div class="tab-pane card" id="vitals">
                            <div class="box-tab-header">
                                <h3 class="box-tab-title"><?php echo $this->lang->line('vitals'); ?></h3>
                           <div class="box-tab-tools  rtl-end-auto rtl-start-0">                                 
                                <?php if ($this->rbac->hasPrivilege('ipd_vitals', 'can_add')) {?>
                                <a href="javascript:void(0)" onclick="shModal('vitalModal').show()" id="add_vital_modal" class="btn btn-primary btn-sm vital"><i class="fa fa-plus"></i> <?php echo $this->lang->line('add_vital'); ?> </a>
                                <?php } ?>                               
                            </div>
                            </div>
                            <div class="timeline-header no-border">
                                <div id="vital_list">
                                    <?php
                                    if (empty($patient_vital_date)) {
                                        ?>
                                        <div class="table_inner">
                                            <table class="table table-striped table-bordered ">
                                                <thead>
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

												<thead>
													<tr>
														<th><?php echo $this->lang->line("date"); ?> </th>
														<?php foreach($vital_list as $vl){ ?>
														<th ><?php echo $vl["name"]; ?> <br> (<?php echo $vl["reference_range"]; ?> <?php echo $vl["unit"]; ?>) </th>
														<?php } ?>
													</tr>
												</thead>
												<tbody> 
													 
													<?php foreach($patient_vital_date as $pvd){  ?>
													<tr>
														<th><?php echo $date = $this->customlib->YYYYMMDDTodateFormat($pvd['messure_date']);
															$messure_date = date('Y-m-d', strtotime($pvd['messure_date']));
														?></th>														
														<?php foreach($vital_list as $vl){ 														
															$id = $vl["id"]; 
															  ?>														
																<td>
                                                                    <div class="relative">
																		<?php  foreach($patientvital[$messure_date][$id] as $pmi){ ?>		
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
																						<?php if ($this->rbac->hasPrivilege('ipd_vitals', 'can_edit')) {?>
																						<a onclick="editvital('<?php echo $pmi['id']; ?>')" class="btn btn-secondary btn-sm" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('edit'); ?>"><i class="fa fa-pencil"></i></a>
																						<?php  } ?>
																						<?php if ($this->rbac->hasPrivilege('ipd_vitals', 'can_delete')) {?>
																						<a class="btn btn-secondary btn-sm" data-bs-toggle="tooltip" onclick="delete_patient_vital('<?php echo $pmi['id']; ?>')" title="<?php echo $this->lang->line('delete'); ?>"><i class="fa fa-trash"></i></a>
																						<?php  } ?>																						
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
                        <?php } ?>
                             <?php  if ($this->rbac->hasPrivilege('consultant_register', 'can_view')) { ?>
                            <div class="tab-pane card tab-content-height" id="consultant_register">
                                <div class="box-tab-header">
                                    <h3 class="box-tab-title"><?php echo $this->lang->line('consultant_register'); ?></h3>
                                    <div class="box-tab-tools  rtl-end-auto rtl-start-0">
                                           <?php
                                            if ($this->rbac->hasPrivilege('consultant_register', 'can_add')) {
                                               if($is_discharge) { 
                                                    ?>
                                                        <a href="javascript:void(0)" class="btn btn-sm btn-primary addconsultant" onclick="shModal('add_instruction').show(); return false;"><i class="fas fa-plus"></i> <?php echo $this->lang->line('consultant_register'); ?></a>
                                                    <?php
                                                }
                                            }
                                        ?>
                                    </div>    
                                </div><!--./box-tab-header-->                              
                                
                                <div class="download_label"><?php echo $result['patient_name'] . " " . $this->lang->line('consultant_register'); ?></div>
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered  example">
                                        <thead>
                                             <tr>
                                                <th><?php echo $this->lang->line('applied_date'); ?></th>
                                                <th><?php echo $this->lang->line('consultant_doctor'); ?></th>
                                                <th><?php echo $this->lang->line('instruction'); ?></th>
                                                <th><?php echo $this->lang->line('consultant_date'); ?></th>
                                                <?php if (is_array($fields_consultant) || is_object($fields_consultant))
                                                {
                                                    foreach ($fields_consultant as $fields_key => $fields_value)
                                                    { ?>
                                                    <th><?php echo ucfirst($fields_value->name); ?></th>
                                                    <?php }
                                                }
                                                ?>                                      
                                                <th class="text-end noExport"><?php echo $this->lang->line('action') ?></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php

                                            if (!empty($consultant_register)) {
                                                foreach ($consultant_register as $consultant_key => $consultant_value) {
                                                    ?>  
                                                    <tr>
        <td><?php echo $this->customlib->YYYYMMDDHisTodateFormat($consultant_value['date'],$this->customlib->getHospitalTimeFormat()); ?></td>
                                                        <td><?php echo $consultant_value["name"] . " " . $consultant_value["surname"]." (".$consultant_value["employee_id"].")" ?></td>
                                                        <td><?php echo nl2br($consultant_value["instruction"]); ?></td>
                                                        <td><?php echo $this->customlib->YYYYMMDDTodateFormat($consultant_value['ins_date']); ?></td> 
                                                    <?php if (is_array($fields_consultant) || is_object($fields_consultant))
                                                        {
                                                            
                                 foreach ($fields_consultant as $fields_key => $fields_value) {
                                     $display_field = $consultant_value[$fields_value->name];
                                  ?>
                                  <td>
                                 <?php echo $display_field; ?>
                                                                    </td>
                                                                    <?php
                                                                }                                                          
                                                        }
                                                        ?>
                                                        <td class="text-end">                                                           
                                                            <?php  if($is_discharge) { if ($this->rbac->hasPrivilege('consultant_register', 'can_edit')) { ?>
                                                                 <a onclick="editConsultantRegister('<?php echo $consultant_value['id']; ?>')" class="btn btn-secondary btn-sm" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('edit'); ?>">
                                                                        <i class="fa fa-pencil"></i>
                                                                </a>  
                                                            <?php }
                                                        } ?>
                                                         <?php  if($is_discharge) { if ($this->rbac->hasPrivilege('consultant_register', 'can_delete')) { ?>
                                                                <a class="btn btn-secondary btn-sm" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('delete'); ?>" onclick="delete_record('<?php echo base_url(); ?>admin/patient/deleteIpdPatientConsultant/<?php echo $consultant_value['id']; ?>', '<?php echo $this->lang->line('delete_message'); ?>')">
                                                                    <i class="fa fa-trash"></i>
                                                                </a> 
                                                            <?php }} ?>
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
                            <?php } ?>
                            <?php if ($this->rbac->hasPrivilege('ipd_prescription ', 'can_view')) { ?>
                                <div class="tab-pane card tab-content-height" id="prescription">
                                    <div class="box-tab-header">
                                        <h3 class="box-tab-title"><?php echo $this->lang->line('prescription'); ?></h3>
                                        <div class="box-tab-tools rtl-start-0 rtl-end-auto">
                                            <?php if ($this->rbac->hasPrivilege('ipd_prescription ', 'can_add')) {
                                                if($is_discharge){
                                             ?>
                                                <a href="#" class="btn btn-sm btn-primary dropdown-toggle addprescription"  data-bs-toggle="modal"><i class="fas fa-plus"></i> <?php echo $this->lang->line('add_prescription'); ?></a>
                                            <?php }} ?>
                                        </div>    
                                   </div><!--./box-tab-header-->                                   
                                    <div class="download_label"><?php echo $result['patient_name'] . " " . $this->lang->line('ipd_details'); ?></div>
                                    <div class="table-responsive">
                                        <table class="table table-striped table-bordered  example">
                                            <thead>
                                            <th><?php echo $this->lang->line('prescription_no'); ?></th>
                                            <th><?php echo $this->lang->line('date'); ?></th>
                                            <th><?php echo $this->lang->line('finding'); ?></th>
                                               <?php 
                                        if (!empty($fields_prescription)) {
                                            foreach ($fields_prescription as $fields_key => $fields_value) {
                                                ?>
                                                <th><?php echo $fields_value->name; ?></th>
                                                <?php
                                            }  
                                        }
                                    ?>  
                                            <th class="text-end noExport"><?php echo $this->lang->line('action') ?></th>
                                            </thead> 
                                            <tbody>
                                                <?php
                                                if (!empty($prescription_detail)) {
                                                    foreach ($prescription_detail as $prescription_key => $prescription_value) {
                                                        ?>  
                                                        <tr>
                                                            <td><?php echo $this->customlib->getSessionPrefixByType('ipd_prescription').$prescription_value["id"] ?></td>
                                                            <td><?php
                                                                // Show date + time so same-day visits by different doctors are distinguishable.
                                                                // created_at is a MySQL TIMESTAMP returned in UTC; convert to the hospital
                                                                // timezone, else the time reads ~hours off. Fall back to date-only if missing.
                                                                $pres_dt = $this->customlib->utcToHospitalDateFormat($prescription_value['created_at'], true);
                                                                if ($pres_dt === '') {
                                                                    $pres_dt = date($this->customlib->getHospitalDateFormat(), strtotime($prescription_value['date']));
                                                                }
                                                                echo $pres_dt;
                                                            ?></td>
                                                            <td><?php echo $prescription_value['finding_description']; ?></td>
                                                            <?php 
                                                             
                                                            if (!empty($fields_prescription)) {
                                                                $display_field = '';
                                                                foreach ($fields_prescription as $fields_key => $fields_value) {
                                                                    $display_field = $prescription_value[$fields_value->name];
                                                                ?>
                                                                    <td><?php echo $display_field; ?></td>
                                                                <?php
                                                                }
                                                            }
                                                            ?>
                                                            <td class="text-end">
                                                                <a href="#prescription" class="btn btn-secondary btn-sm" onclick="view_prescription('<?php echo $prescription_value["id"] ?>', '<?php echo $prescription_value["ipd_id"] ?>','<?php echo $result["ipd_discharge"]?>')"   data-bs-toggle="tooltip" title="<?php echo $this->lang->line('view') . " " . $this->lang->line('prescription'); ?>">
                                                                    <i class="fas fa-file-prescription"></i>
                                                                </a>
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
                                <?php } ?>       
                                <!-- -->           
                                <!-- diagnosis -->
                                <?php if ($this->rbac->hasPrivilege('ipd_lab_investigation ', 'can_view')) { ?>
                                <div class="tab-pane card tab-content-height" id="labinvestigation">
                                    <div class="box-tab-header">
                                        <h3 class="box-tab-title"><?php echo $this->lang->line('lab_investigation'); ?></h3>
                                        <div class="box-tab-tools  rtl-end-auto rtl-start-0">
                                        </div>    
                                   </div><!--./box-tab-header-->                                   
                                    <div class="download_label"><?php echo $result['patient_name'] . " " . $this->lang->line('lab_investigation'); ?></div>
                                    <div class="table-responsive">
                                        <table class="table table-striped table-bordered  example" data-export-title="<?php echo $this->lang->line('lab_investigation'); ?>">
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
                                     <label for=""><?php 
                                     if(!empty($row['test_center'])){
                                     if($row['type']=='pathology'){ echo $this->lang->line('pathology_center'). " : ";  }else{ echo $this->lang->line('radiology_center'). " : ";

                                        } } ?> </label>                                    
                                        <?php
                                   echo $row['test_center']; 
                                    ?>
                                    <br/>
                                     <?php if($row['collection_date']){ echo $this->customlib->YYYYMMDDTodateFormat($row['collection_date']); } ?></td>
                                       <td>
                                    <?php
                                    if($row['reporting_date']){
                                            echo  $this->customlib->YYYYMMDDTodateFormat($row['reporting_date']); 
                                    }
                                    ?> 
                                    </td>
                                    <td class="text-start">                                         
                                        <?php      
                                    echo composeStaffNameByString($row['approved_by_staff_name'],$row['approved_by_staff_surname'],$row['approved_by_staff_employee_id']);
                                     ?>
                                     <br/>
                                    <?php if($row['parameter_update']){                               
                                    echo  $this->customlib->YYYYMMDDTodateFormat($row['parameter_update']);
                                    }
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
                            <!-- Timeline -->
                          <?php if ($this->rbac->hasPrivilege('ipd_timeline', 'can_view')) { ?>  
                            <div class="tab-pane card tab-content-height" id="timeline">
                                <div class="box-tab-header">
                                    <h3 class="box-tab-title"><?php echo $this->lang->line('timeline'); ?></h3>
                                    <div class="box-tab-tools  rtl-end-auto rtl-start-0">
                                           <?php if ($result['ipd_discharge'] != 'yes') { if ($this->rbac->hasPrivilege('ipd_timeline', 'can_add')) { ?>
                                         <a href="javascript:void(0)" class="btn btn-sm btn-primary addtimeline" onclick="shModal('myTimelineModal').show()"><i class="fa fa-plus"></i> <?php echo $this->lang->line('add_timeline'); ?></a>
                                          <?php } } ?>
                                    </div>    
                                </div><!--./box-tab-header-->
                                
                                <div class="download_label"><?php echo $result['patient_name'] . " " . $this->lang->line('ipd_details'); ?></div>
                                <div class="timeline-header no-border">
                                    <div id="timeline_list">
                                <?php if (empty($timeline_list)) { ?>
                                            <div class="dataTables_empty">
                                                No data available in table <br><br>
                                                <img src="<?php echo base_url('backend/images/addnewitem.svg'); ?>" width="150" alt=""><br><br>
                                                <span class="text-success bolds"><i class="fa fa-arrow-left"></i> Search with different criteria.</span>
                                            </div>
                                            <?php } else { ?>
                                            <ul class="timeline timeline-inverse">
                                                <?php
                                                foreach ($timeline_list as $key => $value) {                                                  
                                                    ?>      
                                                    <li class="time-label">
                                                        <span class="bg-blue">    
                                                <?php  echo  date($this->customlib->getHospitalDateFormat(true, true), strtotime($value['timeline_date']));  ?></span>
                                                    </li> 
                                                    <li>
                                                        <i class="fa fa-list-alt bg-blue"></i>
                                                        <div class="timeline-item">
                                                            <?php if($is_discharge) { if ($this->rbac->hasPrivilege('ipd_timeline', 'can_delete')) { 
                                                                if ($value['generated_users_type'] != 'patient') {
                                                             ?>
                                                                <span class="time"><a class="btn btn-secondary btn-sm" data-bs-toggle="tooltip" onclick="delete_timeline('<?php echo $value['id']; ?>')" title="<?php echo $this->lang->line('delete'); ?>"><i class="fa fa-trash"></i></a></span>
                                                                    <?php }}} ?>
                                                                    <?php if($is_discharge) {
                                                                    if ($this->rbac->hasPrivilege('ipd_timeline', 'can_edit')) {
                                                                    if ($value['generated_users_type'] != 'patient') {
                                                                ?><span class="time"><a onclick="editTimeline('<?php echo $value['id']; ?>')" class="btn btn-secondary btn-sm" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('edit'); ?>"><i class="fa fa-pencil"></i></a></span> 

                                                            <?php }}}?>
                                                            <?php if (!empty($value["document"])) { ?>
                                                            <span class="time"><a class="btn btn-secondary btn-sm" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('download'); ?>" href="<?php echo base_url()."admin/timeline/download_patient_timeline/".$value["id"]; ?>"><i class="fa fa-download"></i></a></span>

                                                            <?php } ?>
                                                            <h3 class="timeline-header text-aqua"> <?php echo $value['title']; ?> </h3>
                                                            <div class="timeline-body">
                                                              <?php echo $value['description']; ?> 
                                                            </div>
                                                        </div>
                                                    </li>
                                                <?php } ?> 
                                                <li><i class="fa fa-clock-o bg-gray"></i></li> 
                                                <?php } ?>  
                                        </ul>
                                    </div>
                                </div>
                            </div>  
                            <?php } ?>  

                            <?php if ($this->rbac->hasPrivilege('ipd_live_consultation', 'can_view')) { ?>
                                <div class="tab-pane card tab-content-height" id="live_consult">
                                    <div class="box-tab-header">
                                        <h3 class="box-tab-title"><?php echo $this->lang->line('live_consultation'); ?></h3>
                                        <div class="box-tab-tools  rtl-end-auto rtl-start-0">                                            
                                        </div>    
                                    </div><!--./box-tab-header-->  
									<?php
                              if ($this->session->flashdata('msg')) { 
                                ?>
                                <?php echo $this->session->flashdata('msg'); $this->session->unset_userdata('msg'); ?>
                            <?php
                             } 
                            ?>
                                    <div class="download_label"><?php echo $result['patient_name'] . " " . $this->lang->line('ipd_details'); ?></div>
                                    <div class="table-responsive">
                                        <table class="table table-striped table-bordered  example">
                                            <thead>
                                            <th><?php echo $this->lang->line('consultation_title'); ?></th>
                                                <th><?php echo $this->lang->line('date'); ?></th>
                                                <th><?php echo $this->lang->line('created_by'); ?> </th>
                                                <th><?php echo $this->lang->line('created_for'); ?></th>
                                                <th><?php echo $this->lang->line('patient'); ?></th>
                                                <th><?php echo $this->lang->line('status'); ?></th>
                                                <th class="text-end noExport"><?php echo $this->lang->line('action'); ?></th>
                                            </thead>
                                            <tbody>
                                                    <?php
                                                if (empty($ipdconferences)) {
                                                    ?>

                                                    <?php
                                                } else {
                                                    foreach ($ipdconferences as $conference_key => $conference_value) {

                                            $return_response = json_decode($conference_value->return_response);
                                            ?>
                                            <tr>
                                                <td class="mailbox-name">
                                                    <?php echo $conference_value->title; ?>                                                    
                                                </td>
                                                <td class="mailbox-name">
                                                <?php echo date($this->customlib->getHospitalDateFormat(true, true), strtotime($conference_value->date))?>
                                                <td class="mailbox-name">
                                                    <?php
                                                    if ($conference_value->created_id == $logged_staff_id) {
                                                        echo $this->lang->line('self');
                                                    } else {
                                                        $name= ($conference_value->create_by_surname == "") ? $conference_value->create_by_name : $conference_value->create_by_name . " " . $conference_value->create_by_surname;
                                                        echo  $name. " (".$conference_value->create_by_role_name.": ".$conference_value->create_by_employee_id.")";
                                                    }
                                                    ?></td>
                                                <td class="mailbox-name">
                                                    <?php
                                                          $name= ($conference_value->create_for_surname == "") ? $conference_value->create_for_name : $conference_value->create_for_name . " " . $conference_value->create_for_surname;
                                                            echo  $name. " (".$conference_value->create_for_role_name.": ".$conference_value->create_for_employee_id.")";
                                                    ?>
                                                </td>
                                                <td class="mailbox-name">
                                                     <?php

                                                        $name= ($conference_value->patient_name == "") ? $conference_value->patient_name : $conference_value->patient_name ;
                                                        echo  $name. " (".$conference_value->patient_unique_id.")";
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
                                                <td class="mailbox-date relative text-end" width="90">
                                                    <?php 
                                                if($conference_value->status == 0){
                                                    ?>
                                                <a href="#" class="btn btn-sm label-success start-mr-20" data-bs-toggle="modal" data-bs-target="#modal-chkstatus" data-id="<?php echo $conference_value->id; ?>">
                                                <span class="label" ><i class="fa fa-video-camera"></i> <?php echo $this->lang->line('start') ?></span></a>
                                                    <?php
                                                }
                                                     ?>
                                                    <?php

                                                    if ($conference_value->api_type != 'self') {
                                                        ?>
                                                        <?php 
                                                        if($is_discharge) {
                                                        if($this->rbac->hasPrivilege('live_classes','can_delete')){
                                                            ?>
                                                            <a href="<?php echo base_url(); ?>admin/zoom_conference/delete_consult/<?php echo $conference_value->id . "/" . $return_response->id; ?>"class="btn btn-secondary btn-sm"  data-bs-toggle="tooltip" title="<?php echo $this->lang->line('delete'); ?>" onclick="return confirm('<?php echo $this->lang->line('delete_confirm') ?>');">
                                                            <i class="fa fa-remove"></i>
                                                        </a>
                                                            <?php
                                                        } }
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
                            <?php } ?> 
                            <?php if ($this->rbac->hasPrivilege('bed_history', 'can_view')) {  ?>
                                <div class="tab-pane card tab-content-height" id="bed_history">
                                    <div class="box-tab-header">
                                        <h3 class="box-tab-title"><?php echo $this->lang->line("bed_history"); ?></h3>
                                        <div class="box-tab-tools  rtl-end-auto rtl-start-0">
                                        </div>
                                    </div>
                                    <div class="download_label"></div>
                                    <div class="table-responsive">
                                        <table class="table table-striped table-bordered  example">
                                            <thead>
                                                <th><?php echo $this->lang->line('bed_group'); ?></th>
                                                <th><?php echo $this->lang->line('bed'); ?> </th>
                                                <th><?php echo $this->lang->line('from_date'); ?></th>
                                                <th><?php echo $this->lang->line('to_date'); ?></th>
                                                <th><?php echo $this->lang->line("active_bed"); ?></th>
                                            </thead>
                                            <tbody> 
                                                <?php foreach($bed_history as $history){ ?>
                                                    <tr>
                                                        <td class="mailbox-name"><?php echo $history->bed_group; ?></td>
                                                        <td class="mailbox-name"><?php echo $history->bed; ?></td>
                                                        <td class="mailbox-name"><?php if($history->from_date !=''){ echo date($this->customlib->getHospitalDateFormat(true, true), strtotime($history->from_date)); } ?></td>
                                                        <td class="mailbox-name"><?php if($history->to_date !=''){ echo date($this->customlib->getHospitalDateFormat(true, true), strtotime($history->to_date)); } ?></td>
                                                        <td class="mailbox-name"><?php echo $this->lang->line($history->is_active); ?></td>
                                                    </tr>
                                                <?php } ?>
                                            </tbody>
                                        </table>
                                    </div> 
                                </div> 
                                <?php } ?>
                                <?php if ($this->rbac->hasPrivilege('ipd_medication', 'can_view')) {  ?>
                                <div class="tab-pane card tab-content-height" id="medication">
                                    <div class="box-tab-header">
                                        <h3 class="box-tab-title"><?php echo $this->lang->line('medication'); ?></h3>
                                        <div class="box-tab-tools  rtl-end-auto rtl-start-0">
                                        <?php if($is_discharge) { if ($this->rbac->hasPrivilege('ipd_medication', 'can_add')) {  ?>
                                            <a href="javascript:void(0)" class="btn btn-sm btn-primary addmedication" onclick="addmedicationModal(); return false;"><i class="fa fa-plus"></i> <?php echo $this->lang->line("add_medication_dose"); ?></a>
                                        <?php } }?>
                                        </div>    
                                   </div><!--./box-tab-header-->            
                                    <div class="download_label"><?php echo $result['patient_name'] . " " . $this->lang->line('ipd_details'); ?></div>
                                    <div class="opd-med-v2">
                                    <?php if (!empty($medication)):
                                        $med_by_date = [];
                                        foreach ($medication as $mv) { $med_by_date[$mv['date']][] = $mv; }
                                        krsort($med_by_date);
                                        foreach ($med_by_date as $group_date => $group_items):
                                            $display_date = $this->customlib->YYYYMMDDTodateFormat($group_date);
                                            $day_name     = $this->lang->line(strtolower(date('D', strtotime($group_date))));
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
                                            $btn_pharmacy_id  = $med_value['pharmacy_id'];
                                            $btn_category_id  = '';
                                            foreach ($med_value['dosage'][$group_date] as $mkey => $mvalue):
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
                                                    if ($this->rbac->hasPrivilege('ipd_medication', 'can_edit'))
                                                        $d_edit = '<a href="#" class="dose-btn edit" data-bs-toggle="tooltip" title="'.$this->lang->line('edit').'" onclick="medicationDoseModal('.$dose['id'].')"><i class="fa fa-pencil"></i></a>';
                                                    if ($this->rbac->hasPrivilege('ipd_medication', 'can_delete'))
                                                        $d_del  = '<a href="#" class="dose-btn del delete_record_dosage" data-bs-toggle="tooltip" title="'.$this->lang->line('delete').'" data-record-id="'.$dose['id'].'"><i class="fa fa-trash"></i></a>';
                                                ?>
                                                <div class="dose-chip">
                                                    <div class="dose-amount"><?php echo html_escape($dose['medicine_dosage'].' '.$dose['unit']); ?></div>
                                                    <div class="dose-time"><i class="fa fa-clock-o"></i> <?php echo $this->customlib->getHospitalTime_Format($dose['time']); ?></div>
                                                    <?php if (!empty($dose['remark'])): ?><div class="dose-remark"><?php echo html_escape($dose['remark']); ?></div><?php endif; ?>
                                                    <div class="dose-foot">
                                                        <span class="dose-creator"><?php echo html_escape($dose['staff_name'].' '.$dose['staff_surname'].' ('.$dose['staff_employee_id'].')'); ?></span>
                                                        <?php if ($d_edit || $d_del): ?><span class="dose-actions"><?php echo $d_edit.$d_del; ?></span><?php endif; ?>
                                                    </div>
                                                </div>
                                                <?php endforeach; ?>
                                                <?php if ($is_discharge && $this->rbac->hasPrivilege('ipd_medication', 'can_add')): ?>
                                                <a href="javascript:void(0)" class="dose-add-btn addmedication" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('add_medication_dose'); ?>"
                                                   onclick="medicationModal('<?php echo $btn_category_id; ?>','<?php echo $btn_pharmacy_id; ?>','<?php echo $display_date; ?>'); return false;"><i class="fa fa-plus"></i></a>
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
                        <?php if ($this->rbac->hasPrivilege('ipd_operation_theatre', 'can_view')) {  ?>
                            <div class="tab-pane card tab-content-height" id="operationtheatre">
                                <div class="box-tab-header">
                                    <h3 class="box-tab-title"><?php echo $this->lang->line("operations"); ?></h3>
                                        <div class="box-tab-tools  rtl-end-auto rtl-start-0">
                                            <?php 
                                            if($is_discharge) {
                                           if ($this->rbac->hasPrivilege('ipd_operation_theatre', 'can_add')) { ?>
                                             <a href="javascript:void(0)" onclick="shModal('add_operationtheatre').show()" class="btn btn-primary btn-sm addoperationtheatre"><i class="fa fa-plus"></i> <?php echo $this->lang->line("add_operation"); ?></a>
                                         <?php }}?>
                                        </div>    
                                </div><!--./box-tab-header-->  
                                    <div class="download_label"><?php echo $result['patient_name'] . " " . $this->lang->line('ipd_details'); ?></div>
                                    <div class="table_inner"> 
                                        <table class="table table-striped table-bordered  example">
                                          <thead>
                                                <th><?php echo $this->lang->line("reference_no"); ?></th>
                                                <th><?php echo $this->lang->line("operation_date"); ?></th>
                                                <th><?php echo $this->lang->line("operation_name"); ?></th>
                                                <th><?php echo $this->lang->line("operation_category"); ?></th>
                                                <th><?php echo $this->lang->line("ot_technician"); ?></th>
                                                <?php if (is_array($fields_ot) || is_object($fields))
                                                    {
                                                        foreach ($fields_ot as $fields_key => $fields_value)
                                                        { ?>
                                                           <th><?php echo ucfirst($fields_value->name); ?></th>
                                                        <?php }
                                                    }
                                                ?>
                                                <th class="text-end noExport"><?php echo $this->lang->line('action'); ?></th>                                           
                                    </thead>
                                    <tbody>                                         
                                             <?php
                                        if (!empty($operation_theatre)) {
                                            foreach ($operation_theatre as $ot_key => $ot_value) {
                                               
                                                ?>  
                                                <tr>    
                                                    <td><?php echo $this->customlib->getSessionPrefixByType('operation_theater_reference_no').$ot_value["id"] ?></td>
                                                    <td><?php echo $this->customlib->YYYYMMDDHisTodateFormat($ot_value["date"],$this->customlib->getHospitalTimeFormat()); ?></td>
                                                    <td><?php echo $ot_value["operation"] ?></td>
                                                    <td><?php echo $ot_value["category"] ?></td>
                                                    <td><?php echo $ot_value['ot_technician'] ?></td>
                                                    <?php
                                                    if (!empty($fields_ot)) {

                                                        foreach ($fields_ot as $fields_key => $fields_value) {
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
                                                        <a href='#' data-bs-toggle='tooltip' title="<?php echo $this->lang->line('show'); ?>" class='btn btn-secondary btn-sm'   data-bs-target='#view_ot_modal' onclick='viewdetail("<?php echo $ot_value['id']; ?>")'>  <i class='fa fa-reorder'></i> </a>
                                                        <?php if($is_discharge) { 
                                                            if ($this->rbac->hasPrivilege('ipd_operation_theatre', 'can_edit')) { ?>
                                                            <a onclick="editot('<?php echo $ot_value['id']; ?>')" class="btn btn-secondary btn-sm" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('edit'); ?>">
                                                                    <i class="fa fa-pencil"></i>
                                                            </a>   
                                                        <?php } if ($this->rbac->hasPrivilege('ipd_operation_theatre', 'can_delete')) { ?>
                                                            <a onclick="deleteot('<?php echo $ot_value['id']; ?>')" class="btn btn-secondary btn-sm" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('delete'); ?>">
                                                            <i class="fa fa-trash"></i>
                                                            </a> 
                                                            <?php  }} ?>  
                                                    </td>
                                                </tr>                                            
                                            <?php } }?> 
                                    </tbody>                                
                                    </table>                                  
                                </div> 
                            </div> 
                   
                        <!--Charges-->
                       <?php } if ($this->rbac->hasPrivilege('charges', 'can_view')) { ?>     
                            <div class="tab-pane card tab-content-height" id="charges">
                                <div class="box-tab-header">
                                    <h3 class="box-tab-title"><?php echo $this->lang->line('charges'); ?></h3>
                                        <div class="box-tab-tools  rtl-end-auto rtl-start-0">
                                            <?php
                                            if ($this->rbac->hasPrivilege('charges', 'can_add')) {
                                               if($is_discharge) {
                                                    ?>
                                                        <a href="javascript:void(0)" class="btn btn-sm btn-primary addcharges" onclick="shModal('myChargesModal').show(); return false;"><i class="fa fa-plus"></i> <?php echo $this->lang->line('add_charges'); ?></a>
                                                    <?php
                                                }
                                            }
                                            ?>       
                                        </div>    
                                </div><!--./box-tab-header-->  
                                                 
                                <div class="download_label"><?php echo $this->lang->line('charges'); ?></div>
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered  example">
                                        <thead class="white-space-nowrap">
                                        <th><?php echo $this->lang->line('date'); ?></th>
                                        <th><?php echo $this->lang->line('charge_name')?> / <br> 
                                            <?php echo $this->lang->line('charge_note'); ?></th>
                                        <th><?php echo $this->lang->line('charge_type'); ?></th>
                                        <th><?php echo $this->lang->line('charge_category'); ?></th>
                                         <th><?php echo $this->lang->line('qty'); ?></th>
                                        <th class="text-end"><?php echo $this->lang->line('standard_charge') . ' (' . $currency_symbol . ')'; ?> </th>
                                        <th class="text-end"><?php echo $this->lang->line('applied_charge') . ' (' . $currency_symbol . ')'; ?></th>
                                        <th class="text-end"><?php echo $this->lang->line('tpa_charge') . ' (' . $currency_symbol . ')';?></th>
                                        <th class="text-end"><?php echo $this->lang->line('discount') . ' (' . $currency_symbol . ')';?></th>
                                            <th class="text-end"><?php echo $this->lang->line('tax'); ?></th>                                      
                                        <th class="text-end"><?php echo $this->lang->line('amount') .' (' . $currency_symbol . ')'; ?></th>
                                        <th class="text-end noExport"><?php echo $this->lang->line('action') ?></th>
                                        </thead>
                                       
                                            <?php
                                            $total = 0;
                                            if (!empty($charges)) { ?>
                                            <tbody>
                                            <?php                                        

                                                foreach ($charges as $charge) {
                                                    $total += $charge["amount"];
                                                    $discount_amount = calculatePercent($charge['apply_charge'],$charge['discount_percentage']);
                                                    $tax_amount = calculatePercent(($charge['apply_charge']-$discount_amount),$charge['tax']);
                                                    $taxamount = amountFormat($tax_amount);
                                                    ?>
                                                    <tr>
                                                        <td><?php echo $this->customlib->YYYYMMDDHisTodateFormat($charge['date'],$this->customlib->getHospitalTimeFormat()); ?></td>
                                                        <td class="text-capitalize"><?php echo $charge["name"] ?>
                                                        <div class="bill_item_footer text-muted"><label><?php if($charge["note"]!=''){ echo $this->lang->line('charge_note').': ';} ?> </label> <?php echo $charge["note"] ?></div>
                                                        </td>
                                                        <td class="text-capitalize"><?php echo $charge["charge_type"] ?></td>
                                                        <td class="text-capitalize">
                                                            <?php echo $charge["charge_category_name"] ?>                                                          
                                                        </td>
                                                        <td class="text-capitalize"><?php echo $charge['qty'] ?></td>
                                                        <td class="text-end"><?php echo $charge["standard_charge"] ?></td>
                                                        <td class="text-end"><?php echo number_format($charge["apply_charge"], 2) ?></td>
                                                        <td class="text-end"><?php echo $charge["tpa_charge"] ?></td>
                                                        <td class="text-end"><?php echo number_format($discount_amount,2)." (".$charge["discount_percentage"]."%) "; ?></td>
                                                        <td class="text-end"><?php echo $taxamount." (".$charge["tax"]."%) "; ?></td>                                     
                                                        <td class="text-end"><?php echo number_format($charge["amount"], 2) ?></td>
                                                        <td class="text-end white-space-nowrap" > 
                                                            <a href="javascript:void(0);" class="btn btn-secondary btn-sm print_charge" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('print'); ?>" data-record-id="<?php echo $charge['id']; ?>" data-loading-text="<?php echo $this->lang->line('please_wait'); ?>">
                                                            <i class="fa fa-print"></i>
                                                            </a><?php if($is_discharge) { if ($this->rbac->hasPrivilege('charges', 'can_edit')) { ?>
                                                            <a href='javascript:void(0);' class='btn btn-secondary btn-sm edit_charge' data-loading-text="<?php echo $this->lang->line('please_wait'); ?>" data-bs-toggle='tooltip' data-record-id='<?php echo $charge['id']; ?>'  title="<?php echo  $this->lang->line('edit')?>"><i class='fa fa-pencil'></i></a>
                                                            <?php } if ($this->rbac->hasPrivilege('charges', 'can_delete')) { ?>
                                                            <a href='javascript:void(0);' data-record-id="<?php echo $charge['id'];?>" class="btn btn-secondary btn-sm delete-charge" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('delete'); ?>">
                                                                    <i class="fa fa-trash"></i>
                                                            </a>
                                                            <?php } } ?> 
                                                        </td>
                                                    </tr>
                                                <?php } ?>
                                        </tbody>
                                        <tr class="box box-solid total-bg">
                                            <td colspan="9"></td><td class="text-end"><?php echo  $this->lang->line('total') . " : " ; ?></td>
                                            <td  class="text-end"><?php echo  $currency_symbol . "" . amountFormat($total); ?> <input type="hidden" id="charge_total" name="charge_total" value="<?php echo $total ?>">
                                            </td>
                                            <td></td>
                                        </tr>

                                    <?php } ?>                                        
                                    </table>
                                </div> 
                            </div>
                           <?php }  
                           if ($this->rbac->hasPrivilege('payment', 'can_view')) { ?> 
                            <div class="tab-pane card tab-content-height" id="payment">
                                <div class="box-tab-header">
                                    <h3 class="box-tab-title"><?php echo $this->lang->line('payment'); ?></h3>
                                    <div class="box-tab-tools rtl-end-auto rtl-start-0">
                                           <?php
                                            if ($this->rbac->hasPrivilege('payment', 'can_add')) {
                                                    ?>
                                                        <a href="javascript:void(0)" class="btn btn-sm btn-primary addpayment" onclick="addpaymentModal(); return false;"><i class="fa fa-plus"></i> <?php echo $this->lang->line('add_payment'); ?></a>
                                                    <?php
                                            }
                                            ?> 
                                    </div>    
                                </div><!--./box-tab-header-->                                
                                <div class="download_label"><?php echo $this->lang->line('payment'); ?></div>
                                <div class="table-responsive">
                                <table class="table table-striped table-bordered  example">
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
                                            $total_payment=0;
                                            if (!empty($payment_details)) {                                               

                                                foreach ($payment_details as $payment) {
                                                    if ($payment['type'] != 'refund') {
                                                    if (!empty($payment['amount'])) {
                                                        $total_payment += $payment['amount'];
                                                    }
                                                    ?>
                                                    <tr>
                                                        <td><?php echo $this->customlib->getSessionPrefixByType('transaction_id').$payment['id']; ?></td>
                                                        <td><?php echo $this->customlib->YYYYMMDDHisTodateFormat($payment['payment_date'],$this->customlib->getHospitalTimeFormat()); ?></td>
                                                        <td><?php echo $payment["note"] ?></td>
                                                        <td class="text-capitalize"><?php echo $this->lang->line(strtolower($payment["payment_mode"]))."<br>";

                                                        if($payment['payment_mode'] == "Cheque"){
                                                             if($payment['cheque_no']!=''){
                                       echo $this->lang->line("cheque_no"). ": ".$payment['cheque_no'];                                      
                                    echo "<br>";
                                }
                                    if($payment['cheque_date']!='' && $payment['cheque_date']!='0000-00-00'){
                                       echo $this->lang->line("cheque_date") .": ".$this->customlib->YYYYMMDDTodateFormat($payment['cheque_date']);
                                   }                                      

                                     }
                                    ?>
                                                        </td>
                                                        <td class="text-end"><?php echo $payment["amount"] ?></td>                                                    
                                                        <td class="text-end">

                                                        <?php  if ($payment['payment_mode'] == "Cheque" && $payment['attachment'] != "")  {                                                           
    ?>
    <a href='<?php echo site_url('admin/transaction/download/'.$payment['id']);?>' class='btn btn-secondary btn-sm'  title='<?php echo $this->lang->line('download'); ?>'><i class='fa fa-download'></i></a>
    <?php
}
         ?>       

            <a href="javascript:void(0)" class="btn btn-secondary btn-sm print_trans" data-record-id="<?php echo $payment['id'] ?>" data-loading-text="<?php echo $this->lang->line('please_wait'); ?>" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('print'); ?>"><i class="fa fa-print"></i></a>
			
			<?php  if ($this->rbac->hasPrivilege('payment', 'can_edit')) { ?>
            <a href="javascript:void(0);" class="btn btn-secondary btn-sm editpayment" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('edit'); ?>" data-payment-amount="<?php echo $payment["amount"] ?>" data-record-id="<?php echo $payment['id']; ?>"><i class="fa fa-pencil"></i></a>
			<?php } ?>
			
                                                    <?php if (!empty($payment["document"])) { ?>
                                                                <a href="<?php echo base_url(); ?>admin/payment/download/<?php echo $payment["document"]; ?>"  class="btn btn-secondary btn-sm"  data-bs-toggle="tooltip" title="<?php echo $this->lang->line('download'); ?>">
                                                                    <i class="fa fa-download"></i>
                                                                </a>
                                                            <?php } ?>
        <?php if($is_discharge) {
            if ($this->rbac->hasPrivilege('payment', 'can_delete')) { 
            ?>
            <a href="javascript:void(0);" onclick="deletePayment('<?php echo $payment['id']; ?>')" class="btn btn-secondary btn-sm" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('delete'); ?>"><i class="fa fa-trash"></i></a>
                                                            
                                     <?php } }?>
                                                        </td>
                                                    </tr>
                                                <?php } } ?>                                 
                                            </tbody>
                                                <tr class="box box-solid total-bg">
                                                    <td></td> 
                                                    <td></td> 
                                                    <td></td> 
                                                    <td></td>
                                                     <td  colspan = "" class="text-end"><?php echo $this->lang->line('total') . " : " . $currency_symbol . "" . number_format($total_payment, 2) ; ?>
                                                    </td>
                                                    <td></td>   
                                                </tr>
                                    <?php } ?>
                                    </table>
                                </div><!--./table-responsive--> 
                            </div><!--#/Bill payment --> 
                        <?php } ?>
                        <?php if ($this->rbac->hasPrivilege('ipd_treatment_history', 'can_view')) { ?>
                            <!--- treatment history tab---->
                            <div class="tab-pane card tab-content-height" id="treatment_history">
                                <div class="box-tab-header">
                                    <h3 class="box-tab-title"><?php echo $this->lang->line('treatment_history'); ?></h3>
                                    <div class="box-tab-tools  rtl-end-auto rtl-start-0">
                                          
                                    </div>    
                                </div><!--./box-tab-header-->                                
                                <div class="download_label"><?php echo $this->lang->line('treatment_history'); ?></div>
                                <div class="table-responsive">
                                 <table class="table table-striped table-bordered  treatmentlist"  data-export-title="<?php echo $this->lang->line('treatment_history'); ?>">
                                    <thead>
                                        <th><?php echo $this->lang->line('ipd_no'); ?></th>
                                         <th><?php echo $this->lang->line('symptoms'); ?></th>
                                        <th><?php echo $this->lang->line('consultant_doctor'); ?></th>
                                        <th class="text-end" ><?php echo $this->lang->line('bed'); ?></th>
                                    </thead>
                                    <tbody> 
                                    </tbody>
                                 </table>
                                </div><!--./table-responsive--> 
                            </div><!--#/Bill payment --> 
                            <?php } ?> 
                            <!--- end treatmenthistory tab--> 
                              <!--obstetric history-->
                        <?php if($result['is_antenatal']==1){ ?>
                        <?php if ($this->rbac->hasPrivilege('ipd_previous_obstetric_history', 'can_view')) { ?>
                        <div class="tab-pane card" id="obstetric_history">
                            <div class="box-tab-header">
                                <h3 class="box-tab-title"><?php echo $this->lang->line('previous_obstetric_history'); ?></h3>
                                <div class="box-tab-tools  rtl-end-auto rtl-start-0">
                                    <?php if ($this->rbac->hasPrivilege('ipd_previous_obstetric_history', 'can_add')) { ?>
                                    <a href="#"  onclick="addobstetric();" class="btn btn-primary btn-sm "  data-bs-toggle="modal" title=""><i class="fas fa-plus"></i> <?php echo $this->lang->line('add_previous_obstetric_history'); ?></a>
                                    <?php } ?>                                    
                                </div>
                            </div>
                            
                            <div class="impbtnview-t9">
                            </div>
                            
                            <div class="download_label"><?php echo composePatientName($result['patient_name'],$result['id']) . " " . $this->lang->line('previous_obstetric_history'); ?></div>
                            <div class="table-responsive">
                               <table class="table table-striped table-bordered table-hover example">
                                        <thead>
                                        <tr>
                                            <th><?php echo $this->lang->line('place_of_delivery'); ?></th>
                                        <th><?php echo $this->lang->line('duration_of_pregnancy'); ?></th>
                                        <th><?php echo $this->lang->line('complication_in_pregnancy_or_puerperium'); ?></th>
                                        <th><?php echo $this->lang->line('birth_weight'); ?></th>
                                        <th><?php echo $this->lang->line('gender'); ?></th>
                                        <th><?php echo $this->lang->line('infant_feeding') ?></th>
                                        <th><?php echo $this->lang->line('birth_status') ?></th>
                                        <th><?php echo $this->lang->line('alive'); ?> / <?php echo $this->lang->line('dead'); ?> <?php echo $this->lang->line('date'); ?></th>
                                        <th><?php echo $this->lang->line('death_cause') ?></th>
                                         <th class="text-end noExport"><?php echo $this->lang->line('action') ?></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach($obstetric_history as $row){ ?>
                                                <tr>
                                                    <td><?php echo $row['place_of_delivery']; ?></td>
                                                    <td><?php echo $row['pregnancy_duration']; ?></td>
                                                    <td><?php echo $row['pregnancy_complications']; ?></td>
                                                    <td><?php echo $row['birth_weight']; ?></td>
                                                    <td><?php echo $this->lang->line(strtolower($row['gender'])); ?></td>
                                                    <td><?php echo $row['infant_feeding']; ?></td>
                                                    <td><?php echo $this->lang->line($row['alive_dead']); ?></td>
                                                    <td><?php if($row['date']){ echo $this->customlib->YYYYMMDDTodateFormat($row['date']); } ?></td>
                                                    <td><?php echo $row['death_cause']; ?></td>
                                                    <td class="text-end noExport">                                                    
                                                    <?php if ($this->rbac->hasPrivilege('ipd_previous_obstetric_history', 'can_view')) { ?>
                                                    <a href='javascript:void(0)' onclick="viewobstetric('<?php echo $row['id']; ?>')" data-record-id = "<?php echo $row['id']; ?>" class='btn btn-secondary btn-sm edit_obstetric '  data-bs-toggle='tooltip' title="<?php echo $this->lang->line('view_history'); ?>" ><i class='fas fa fa-reorder'   ></i></a>
                                                    <?php } ?>                                                    
                                                    </td>
                                                </tr>
                                           <?php } ?>
                                        </tbody>
                                </table>
                            </div> 
                        </div> 
                        <?php } ?>
                        
                        <?php if ($this->rbac->hasPrivilege('ipd_antenatal', 'can_view')) { ?>
                        <div class="tab-pane card" id="addantenatal">
                            <div class="box-tab-header">
                                <h3 class="box-tab-title"><?php echo $this->lang->line('antenatal'); ?></h3>
                                <div class="box-tab-tools  rtl-end-auto rtl-start-0">
                                     <?php if ($this->rbac->hasPrivilege('ipd_antenatal', 'can_add')) { ?>
                                     <a href="javascript:void(0);"   class="btn btn-primary btn-sm " onclick="addipdantenatal('<?php echo $ipdid; ?>')" title=""><i class="fas fa-plus"></i> <?php echo $this->lang->line('add_antenatal'); ?>
                                        </a>
                                    <?php } ?>
                             </div>
                            </div>                            
                            <div class="download_label"><?php echo composePatientName($result['patient_name'],$result['id']) . " " . $this->lang->line('opd_details'); ?></div>
                            
                            <div class="table-responsive">
                               <table class="table table-striped table-bordered table-hover example"  data-export-title="<?php echo composePatientName($result['patient_name'],$result['id']) . " " . $this->lang->line('opd_details'); ?>">
                                        <thead>
                                        <tr>
                                            <th><?php echo $this->lang->line('opd/ipd_no'); ?></th>
                                            <th><?php echo $this->lang->line('checkup_id'); ?></th>                                         
                                            <th><?php echo $this->lang->line('date'); ?></th>
                                             <th class="text-end noExport"><?php echo $this->lang->line('action') ?></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach($antenatallist as $row){ ?>
                                                <tr>                                                    
                                                    <?php if($row['status']=='ipd'){?>
                                                        <td><?php if($row['ipdid']!="" && $row['ipdid']!= null){ echo $this->customlib->getSessionPrefixByType('ipd_no').$row['ipdid']; }  ?></td>
                                                     <?php }else{ ?>
                                                        <td><?php if($row['opd_detail_id']!="" && $row['opd_detail_id']!= null){echo $this->opd_prefix.$row['opd_detail_id'];} ?></td>
                                                     <?php } ?>
                                                    <td><?php if($row['visit_details_id']!="" && $row['visit_details_id']!=null){echo $this->customlib->getSessionPrefixByType('checkup_id').$row['visit_details_id'];} ?></td>                                                    
                                                    <td><?php echo $this->customlib->YYYYMMDDHisTodateFormat($row['date']); ?></td>
                                                    <td class="text-end noExport"><?php if($row['status']=='ipd'){ ?>                                                    
                                                         <a href='javascript:void(0)' onclick="viewipdantenatal('<?php echo $row['primary_id']; ?>')" data-record-id = "<?php echo $row['primary_id']; ?>" class='btn btn-secondary btn-sm edit_obstetric '  data-bs-toggle='tooltip' title="<?php echo $this->lang->line('view_history'); ?>" ><i class='fas fa fa-reorder' ></i></a>                                                         
                                                         <?php }else{ ?>
                                                            <a href='javascript:void(0)' onclick="viewantenatal('<?php echo $row['visit_details_id']; ?>')" data-record-id = "<?php echo $row['visit_details_id']; ?>" class='btn btn-secondary btn-sm edit_obstetric ' data-bs-toggle='tooltip' title="<?php echo $this->lang->line('view_history'); ?>" ><i class='fas fa fa-reorder'  ></i></a>
                                                    <?php } ?>
                                                </td>
                                                </tr>

                                           <?php } ?>
                                        </tbody>
                                </table>
                            </div> 
                        </div>
                    <?php } ?>
                    <?php } ?>

                    <?php if ($this->rbac->hasPrivilege('ipd_postnatal_history', 'can_view')) { ?>
                    <div class="tab-pane card" id="post_antenatal">
                            <div class="box-tab-header">
                                <h3 class="box-tab-title"><?php echo $this->lang->line('postnatal_history'); ?></h3>
                                <div class="box-tab-tools  rtl-end-auto rtl-start-0">
                            <?php if ($this->rbac->hasPrivilege('ipd_postnatal_history', 'can_add')) { ?>      
                                     <a href="#"  onclick="addpostnatal();" class="btn btn-primary btn-sm "  data-bs-toggle="modal" title=""><i class="fas fa-plus"></i> <?php echo $this->lang->line('add_postnatal'); ?>
                                        </a>
                            <?php } ?>
                             </div>
                            </div>
                            <div class="impbtnview-t9">
                            </div>                            
                               <div class="download_label"><?php echo composePatientName($result['patient_name'],$result['id']) . " " . $this->lang->line('opd_details'); ?></div>
                            <div class="table-responsive overflow-visible">
                               <table class="table table-striped table-bordered table-hover example"  data-export-title="<?php echo composePatientName($result['patient_name'],$result['id']) . " " . $this->lang->line('opd_details'); ?>">
                                        <thead>
                                        <tr>
                                            <th><?php echo $this->lang->line('labor_time'); ?></th>
                                            <th><?php echo $this->lang->line('delivery_time'); ?></th>
                                            <th><?php echo $this->lang->line('routine_question'); ?></th>
                                            <th><?php echo $this->lang->line('general_remark'); ?></th>
                                         <th class="text-end"><?php echo $this->lang->line('action'); ?></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach($postnatal_history as $row){ ?>
                                                <tr>
                                                    <td><?php echo $this->customlib->YYYYMMDDHisTodateFormat($row['labor_time'],$this->customlib->getHospitalTimeFormat()); ?></td>
                                                    <td><?php echo $this->customlib->YYYYMMDDHisTodateFormat($row['delivery_time'],$this->customlib->getHospitalTimeFormat()); ?></td>
                                                    <td><?php echo $row['routine_question']; ?></td>
                                                    <td><?php echo $row['general_remark']; ?></td>
                                                    <td class="text-end noExport">                                                    
                                                    <?php if ($this->rbac->hasPrivilege('ipd_postnatal_history', 'can_edit')) { ?>
                                                    <a href='javascript:void(0)'  data-record-id = "<?php echo $row['id']; ?>" class='btn btn-secondary btn-sm view_postnatal '  data-bs-toggle='tooltip' title="<?php echo $this->lang->line('edit'); ?>" ><i class='fas fa fa-pencil'   ></i></a> 
                                                    <?php } if ($this->rbac->hasPrivilege('ipd_postnatal_history', 'can_delete')) { ?>
                                                    <a href='javascript:void(0)'  data-record-id = "<?php echo $row['id']; ?>" class='btn btn-secondary btn-sm delete_postnatal '  data-bs-toggle='tooltip' title="<?php echo $this->lang->line('delete'); ?>" ><i class='fas fa fa-trash'   ></i></a>
                                                    
                                                    <?php } ?>                                                    
                                                    </td>
                                                </tr>

                                           <?php } ?>
                                        </tbody>
                                </table>
                            </div> 
                        </div> 
                     <?php } ?>                     
                        <!-- end obstetric history-->
                        </div><!-- /.tab-content -->
                    </div><!-- /.nav-tabs-custom -->
            </div><!-- /.box border0 mb0 -->
            </div><!-- /.box box-primary -->
            </div><!-- /.ipd_itemcol -->
        </div><!-- /.row -->

<div id="modal-chkstatus" class="modal fade sh-modal sh-modal-accent" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog2 modal-lg modal-dialog-centered">
    <form id="form-chkstatus" action="" method="POST">
        <div class="modal-content">
            <div class="">
                <button type="button" class="btn-close modalclosezoom" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="pup-scroll-area">
                <div class="modal-body" id="zoom_details">
                </div>
            </div>
        </div>
    </form>
    </div>
</div>

<!-- Add Doctors -->
<div class="modal fade sh-modal sh-modal-accent" id="add_doctor" tabindex="-1" aria-labelledby="add_doctorLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="add_doctorLabel"><?php echo $this->lang->line('add_doctor'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="form_doctor" accept-charset="utf-8" enctype="multipart/form-data" method="post">    
                <input type="hidden" name="ipdid_doctor" id="ipdid_doctor" value="<?php echo $result['ipdid'] ?>">
                <div class="pup-scroll-area">
                    <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-12"> 
                        <?php 
                        $ipdarray[]='';
                        $doctors_ipd_array[]='';
                        foreach($doctors_ipd as $doctors_ipd_value){
                            $doctors_ipd_array[] = $doctors_ipd_value['consult_doctor'];                            
                        }  $ipdarray[] = $doctors_ipd_array;  ?>                        
                          
                           <select placeholder="<?= $this->lang->line('select') ?>" name="doctorOpt[]" class="doctorinput select2" multiple id="doctorOpt">                              
                                <?php  foreach ($doctorsipd as $dkey => $dvalue) {   ?>
                                <option value="<?php echo $dvalue["id"]; ?>"<?php
                                        if ((isset($doctors_ipd)) && ( in_array($dvalue["id"], $ipdarray[1])))                              
                                        { echo "selected"; }?>>                                     
                                        <?php echo $dvalue["name"] . " " . $dvalue["surname"]." (". $dvalue["employee_id"].")" ?> 
                                </option>   
                                <?php } ?> 
                            </select>
                             <span class="text-danger"><?php echo form_error('doctorOpt[]'); ?></span>
                        </div>
                    </div>
                </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                    <button type="submit" id="form_doctorbtn" data-loading-text="<?php echo $this->lang->line('processing'); ?>" class="btn btn-info"> <i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade sh-modal sh-modal-accent" id="discharge_revert" tabindex="-1" aria-labelledby="discharge_revertLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="discharge_revertLabel"><?php echo $this->lang->line('discharge_revert'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="form_discharge_revert" accept-charset="utf-8"  enctype="multipart/form-data" method="post">    
                <input type="hidden" name="ipd_details_id" id="ipd_details_id" value="<?php echo $result['ipdid'] ?>">
                <input type="hidden" name="opd_details_id" id="opd_details_id">
                <div class="pup-scroll-area">
                    <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="mb-3">
                                <label for="exampleInputFile">
                                    <?php echo $this->lang->line('bed_group'); ?></label>
                                <div>
                                    <select class="form-control" name='bed_group_id' id='bed_group_id' onchange="getBed(this.value, '', 'yes')">
                                        <option value=""><?php echo $this->lang->line('select'); ?></option>
                                        <?php foreach ($bedgroup_list as $key => $bedgroup) {
                                            ?>
                                            <option value="<?php echo $bedgroup["id"] ?>"><?php echo $bedgroup["name"] . " - " . $bedgroup["floor_name"] ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                        </div> 

                        <div class="col-sm-12">
                            <div class="mb-3">
                                <label for="exampleInputFile">
                                    <?php echo $this->lang->line('bed_no'); ?></label><small class="req"> *</small> 
                                <div><select class="form-control select2" name='bed_no' id='bed_nos'>
                                        <option value=""><?php echo $this->lang->line('select'); ?></option>
                                    </select>
                                </div>
                                <span class="text-danger"><?php echo form_error('bed_no'); ?></span></div>
                        </div>
                                            <div class="col-sm-12">
                                                <div class="mb-3">
                                                    <label for="exampleInputFile">
                                                        <?php echo $this->lang->line('revert_reason'); ?></label><small class="req"> *</small> 
                                                    <div> 
                                                        <textarea name="discharge_revert_reason" rows="3" class="form-control"></textarea>
                                                    </div>
                                                    <span class="text-danger"><?php echo form_error('bed_no'); ?></span></div>
                                            </div>
                    </div>
                </div>    
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                    <button type="submit" id="submit_discharge_revert" data-loading-text="<?php echo $this->lang->line('processing'); ?>" class="btn btn-info"> <i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Timeline -->
<div class="modal fade sh-modal sh-modal-accent" id="myTimelineModal" tabindex="-1" aria-labelledby="myTimelineModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myTimelineModalLabel"><?php echo $this->lang->line('add_timeline'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="add_timeline" accept-charset="utf-8" enctype="multipart/form-data" method="post">
                <div class="modal-body">
                    <div class="sh-form-card">
                        <div class="sh-card-header">
                            <span class="sh-card-header-title"><i class="fa fa-history me-1"></i><?php echo $this->lang->line('add_timeline'); ?></span>
                        </div>
                        <div class="p-2">
                            <div class="row g-3">
                                <input type="hidden" name="patient_id" id="patient_id" value="<?php echo $result['patient_id'] ?>">
                                <div class="col-md-6">
                                    <label class="form-label mb-1"><?php echo $this->lang->line('title'); ?> <small class="text-danger">*</small></label>
                                    <input id="timeline_title" name="timeline_title" type="text" class="form-control" />
                                    <span class="text-danger"><?php echo form_error('timeline_title'); ?></span>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label mb-1"><?php echo $this->lang->line('date'); ?> <small class="text-danger">*</small></label>
                                    <input id="timeline_date" name="timeline_date" value="<?php echo set_value('timeline_date', date($this->customlib->getHospitalDateFormat(true,true))); ?>" type="text" class="form-control datetime" />
                                    <span class="text-danger"><?php echo form_error('timeline_date'); ?></span>
                                </div>
                                <div class="col-12">
                                    <label class="form-label mb-1"><?php echo $this->lang->line('description'); ?></label>
                                    <textarea id="timeline_desc" name="timeline_desc" class="form-control" rows="4"></textarea>
                                    <span class="text-danger"><?php echo form_error('description'); ?></span>
                                </div>
                                <div class="col-12">
                                    <label class="form-label mb-1"><?php echo $this->lang->line('attach_document'); ?></label>
                                    <input id="timeline_doc_id" name="timeline_doc" type="file" class="filestyle form-control" data-height="40" value="<?php echo set_value('timeline_doc'); ?>" />
                                    <span class="text-danger"><?php echo form_error('timeline_doc'); ?></span>
                                </div>
                                <div class="col-12">
                                    <div class="form-check">
                                        <input id="visible_check" checked="checked" name="visible_check" value="yes" type="checkbox" class="form-check-input" />
                                        <label class="form-check-label" for="visible_check"><?php echo $this->lang->line('visible_to_this_person'); ?></label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                    <button type="submit" id="add_timelinebtn" data-loading-text="<?php echo $this->lang->line('processing'); ?>" class="btn btn-info"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade sh-modal sh-modal-accent" id="nursenoteEditModal" tabindex="-1" aria-labelledby="nursenoteEditModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="nursenoteEditModalLabel"><?php echo $this->lang->line('edit_nurse_note'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="edit_nursenote" accept-charset="utf-8" enctype="multipart/form-data" method="post">
                <div class="modal-body">
                    <div class="sh-form-card">
                        <div class="sh-card-header">
                            <span class="sh-card-header-title"><i class="fas fa-notes-medical"></i> <?php echo $this->lang->line('edit_nurse_note'); ?></span>
                        </div>
                        <div class="px-2 py-3">
                            <div class="row g-3">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label class="form-label"><?php echo $this->lang->line('date'); ?><small class="req"> *</small></label>
                                        <input type="text" name="date" id="endate" value="" class="form-control datetime">
                                        <input type="hidden" name="nurseid" id="nurse_id">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label class="form-label"><?php echo $this->lang->line('nurse'); ?><small class="req"> *</small></label>
                                        <select name="nurse" id="edit_nurse" class="form-control select2">
                                            <option value=""><?php echo $this->lang->line('select') ?></option>
                                            <?php foreach ($nurse as $value) { ?>
                                            <option <?php if ((isset($nurse_select)) && ($nurse_select == $value["id"])) { echo "selected"; } ?> value="<?php echo $value["id"] ?>"><?php echo composeStaffNameByString($value["name"],$value["surname"],$value["employee_id"]); ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label class="form-label"><?php echo $this->lang->line('note'); ?> <small class="req"> *</small></label>
                                        <textarea name="note" id="enote" rows="3" class="form-control"></textarea>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label class="form-label"><?php echo $this->lang->line('comment'); ?> <small class="req"> *</small></label>
                                        <textarea name="comment" id="ecomment" rows="3" class="form-control"></textarea>
                                    </div>
                                </div>
                                <div class="col-sm-12" id="customfieldnurse"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                    <button type="submit" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i>" id="edit_nursenotebtn" class="btn btn-info"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade sh-modal sh-modal-accent" id="nursenoteCommentModal" tabindex="-1" aria-labelledby="nursenoteCommentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="nursenoteCommentModalLabel"><i class="fa fa-comment me-2 sh-icon-soft"></i><?php echo $this->lang->line('add') . " " . $this->lang->line('comment'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="comment_nursenote" accept-charset="utf-8" enctype="multipart/form-data" method="post">
                <input type="hidden" name="nurseid" id="nurse_noteid">
                <div class="pup-scroll-area">
                    <div class="modal-body">
                        <div class="sh-form-card">
                            <div class="sh-card-header">
                                <span class="sh-card-header-title"><?php echo $this->lang->line('details'); ?></span>
                            </div>
                            <div class="p-2">
                                <div class="row g-3">
                                    <div class="col-12">
                                        <label class="form-label form-label-sm"><?php echo $this->lang->line('comment'); ?> <small class="req">*</small></label>
                                        <textarea name="comment_staff" id="comment_staff" rows="4" class="form-control form-control-sm"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                    <button type="submit" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i>" id="comment_nursenotebtn" class="btn btn-info"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade sh-modal sh-modal-accent" id="patient_discharge" tabindex="-1" aria-labelledby="patient_dischargeLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="patient_dischargeLabel"><?php echo $this->lang->line('patient_discharge'); ?></h5>
                <div id="allpayments_print" class="ms-auto me-2"></div>
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

<!-- Add OT -->
<div class="modal fade sh-modal sh-modal-accent" id="add_operationtheatre" tabindex="-1" aria-labelledby="add_operationtheatreLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form id="form_operationtheatre" accept-charset="utf-8" enctype="multipart/form-data" method="post">
                <div class="modal-header">
                    <h5 class="modal-title" id="add_operationtheatreLabel"><?php echo $this->lang->line("add_operation"); ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="pup-scroll-area">
                <div class="modal-body">
                    <input type="hidden" name="ipdid" id="ipdid" value="<?php echo $ipdid ?>">
                    <input type="hidden" name="case_id" value="<?php echo $result['case_reference_id']; ?>">
                    <div class="sh-form-card">
                        <div class="sh-card-header">
                            <span class="sh-card-header-title"><i class="fa fa-stethoscope me-1"></i> <?php echo $this->lang->line('operation_theatre'); ?></span>
                        </div>
                        <div class="p-2">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label mb-1"><?php echo $this->lang->line('operation_category'); ?> <small class="text-danger">*</small></label>
                                    <select name="operation_category" id="operation_category" class="form-control select2" onchange="getcategory(this.value)">
                                        <option value=""><?php echo $this->lang->line('select'); ?></option>
                                        <?php foreach ($categorylist as $operation) { ?>
                                        <option value="<?php echo $operation['id']; ?>"><?php echo $operation['category']; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label mb-1" for="operation_name"><?php echo $this->lang->line('operation_name'); ?> <small class="text-danger">*</small></label>
                                    <select name="operation_name" id="operation_name" class="form-control select2"></select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label mb-1"><?php echo $this->lang->line('operation_date'); ?> <small class="text-danger">*</small></label>
                                    <input type="text" value="" id="date" name="date" class="form-control datetime">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label mb-1"><?php echo $this->lang->line('consultant_doctor'); ?> <small class="text-danger">*</small></label>
                                    <select class="form-control select2" id="consultant_doctorid" name="consultant_doctor">
                                        <option value=""><?php echo $this->lang->line('select'); ?></option>
                                        <?php foreach ($doctors as $dkey => $dvalue) { ?>
                                        <option value="<?php echo $dvalue["id"]; ?>" <?php if ((isset($doctor_select)) && ($doctor_select == $dvalue["id"])) { echo "selected"; } ?>><?php echo composeStaffNameByString($dvalue["name"], $dvalue["surname"], $dvalue["employee_id"]); ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label mb-1"><?php echo $this->lang->line('assistant_consultant') . ' 1'; ?></label>
                                    <input type="text" name="ass_consultant_1" class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label mb-1"><?php echo $this->lang->line('assistant_consultant') . ' 2'; ?></label>
                                    <input type="text" name="ass_consultant_2" class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label mb-1"><?php echo $this->lang->line('anesthetist'); ?></label>
                                    <input type="text" name="anesthetist" class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label mb-1"><?php echo $this->lang->line('anesthesia_type'); ?></label>
                                    <input type="text" name="anaethesia_type" class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label mb-1"><?php echo $this->lang->line('ot_technician'); ?></label>
                                    <input type="text" name="ot_technician" class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label mb-1"><?php echo $this->lang->line('ot_assistant'); ?></label>
                                    <input type="text" name="ot_assistant" class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label mb-1"><?php echo $this->lang->line('remark'); ?></label>
                                    <textarea name="ot_remark" id="ot_remark" rows="2" class="form-control"></textarea>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label mb-1"><?php echo $this->lang->line('result'); ?></label>
                                    <textarea name="ot_result" id="ot_result" rows="2" class="form-control"></textarea>
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
                    <button type="submit" id="form_addoperationtheatrbtn" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i>" class="btn btn-info"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Edit Operation Theatre -->
<div class="modal fade sh-modal sh-modal-accent" id="edit_operationtheatre" tabindex="-1" aria-labelledby="edit_operationtheatreLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="edit_operationtheatreLabel"><?php echo $this->lang->line("edit_operation"); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="form_editoperationtheatre" accept-charset="utf-8" enctype="multipart/form-data" method="post">
                <input type="hidden" name="opdid" id="opdid" value="<?php echo $ipdid ?>">
                <input type="hidden" name="otid" id="otid" value="">
                <div class="modal-body">
                    <div class="sh-form-card">
                        <div class="sh-card-header">
                            <span class="sh-card-header-title"><i class="fa fa-stethoscope me-1"></i> <?php echo $this->lang->line('operation_theatre'); ?></span>
                        </div>
                        <div class="p-2">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label mb-1"><?php echo $this->lang->line('operation_category'); ?> <small class="text-danger">*</small></label>
                                    <select name="eoperation_category" id="eoperation_category" class="form-control select2" onchange="getcategory(this.value)">
                                        <option value=""><?php echo $this->lang->line('select'); ?></option>
                                        <?php foreach ($categorylist as $operation) { ?>
                                        <option value="<?php echo $operation['id']; ?>"><?php echo $operation['category']; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label mb-1" for="eoperation_name"><?php echo $this->lang->line('operation_name'); ?> <small class="text-danger">*</small></label>
                                    <select name="eoperation_name" id="eoperation_name" class="form-control select2">
                                        <option value=""><?php echo $this->lang->line('select'); ?></option>
                                        <?php foreach ($operation_list as $op) { ?>
                                        <option value="<?php echo $op['id']; ?>"><?php echo $op['operation']; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label mb-1"><?php echo $this->lang->line('operation_date'); ?> <small class="text-danger">*</small></label>
                                    <input type="text" value="" id="edate" name="date" class="form-control datetime">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label mb-1"><?php echo $this->lang->line('consultant_doctor'); ?> <small class="text-danger">*</small></label>
                                    <select class="form-control select2" id="econsultant_doctorid" name="consultant_doctor">
                                        <option value=""><?php echo $this->lang->line('select'); ?></option>
                                        <?php foreach ($doctors as $dkey => $dvalue) { ?>
                                        <option value="<?php echo $dvalue["id"]; ?>" <?php if ((isset($doctor_select)) && ($doctor_select == $dvalue["id"])) { echo "selected"; } ?>><?php echo composeStaffNameByString($dvalue["name"], $dvalue["surname"], $dvalue["employee_id"]); ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label mb-1"><?php echo $this->lang->line('assistant_consultant') . ' 1'; ?></label>
                                    <input type="text" name="ass_consultant_1" id="eass_consultant_1" class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label mb-1"><?php echo $this->lang->line('assistant_consultant') . ' 2'; ?></label>
                                    <input type="text" name="ass_consultant_2" id="eass_consultant_2" class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label mb-1"><?php echo $this->lang->line('anesthetist'); ?></label>
                                    <input type="text" name="anesthetist" id="eanesthetist" class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label mb-1"><?php echo $this->lang->line('anaethesia_type'); ?></label>
                                    <input type="text" name="anaethesia_type" id="eanaethesia_type" class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label mb-1"><?php echo $this->lang->line('ot_technician'); ?></label>
                                    <input type="text" name="ot_technician" id="eot_technician" class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label mb-1"><?php echo $this->lang->line('ot_assistant'); ?></label>
                                    <input type="text" name="ot_assistant" id="eot_assistant" class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label mb-1"><?php echo $this->lang->line('remark'); ?></label>
                                    <textarea name="eot_remark" id="eot_remark" rows="2" class="form-control"></textarea>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label mb-1"><?php echo $this->lang->line('result'); ?></label>
                                    <textarea name="eot_result" id="eot_result" rows="2" class="form-control"></textarea>
                                </div>
                                <div class="col-12" id="custom_field_ot"></div>
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

<!-- Edit Timeline -->
<div class="modal fade sh-modal sh-modal-accent" id="myTimelineEditModal" tabindex="-1" aria-labelledby="myTimelineEditModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form id="edit_timeline" accept-charset="utf-8" enctype="multipart/form-data" method="post">
                <div class="modal-header">
                    <h5 class="modal-title" id="myTimelineEditModalLabel"><?php echo $this->lang->line('edit_timeline'); ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="pup-scroll-area">
                <div class="modal-body">
                    <input type="hidden" name="patient_id" id="epatientid" value="">
                    <input type="hidden" name="timeline_id" id="etimelineid" value="">
                    <div class="sh-form-card">
                        <div class="sh-card-header">
                            <span class="sh-card-header-title"><i class="fa fa-history me-1"></i><?php echo $this->lang->line('edit_timeline'); ?></span>
                        </div>
                        <div class="p-2">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label mb-1"><?php echo $this->lang->line('title'); ?> <small class="text-danger">*</small></label>
                                    <input id="etimelinetitle" name="timeline_title" type="text" class="form-control" />
                                    <span class="text-danger"><?php echo form_error('timeline_title'); ?></span>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label mb-1"><?php echo $this->lang->line('date'); ?> <small class="text-danger">*</small></label>
                                    <input type="text" name="timeline_date" class="form-control datetime" id="etimelinedate" />
                                    <span class="text-danger"><?php echo form_error('timeline_date'); ?></span>
                                </div>
                                <div class="col-12">
                                    <label class="form-label mb-1"><?php echo $this->lang->line('description'); ?></label>
                                    <textarea id="timelineedesc" name="timeline_desc" class="form-control" rows="4"></textarea>
                                    <span class="text-danger"><?php echo form_error('description'); ?></span>
                                </div>
                                <div class="col-12">
                                    <label class="form-label mb-1"><?php echo $this->lang->line('attach_document'); ?></label>
                                    <input id="etimeline_doc_id" name="timeline_doc" type="file" class="filestyle form-control" data-height="40" value="<?php echo set_value('timeline_doc'); ?>" />
                                    <span class="text-danger"><?php echo form_error('timeline_doc'); ?></span>
                                </div>
                                <div class="col-12">
                                    <div class="form-check">
                                        <input id="evisible_check" name="visible_check" value="yes" type="checkbox" class="form-check-input" />
                                        <label class="form-check-label" for="evisible_check"><?php echo $this->lang->line('visible_to_this_person'); ?></label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                    <button type="submit" data-loading-text="<?php echo $this->lang->line('processing'); ?>" id="edit_timelinebtn" class="btn btn-info"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Diagnosis -->
<div class="modal fade sh-modal sh-modal-accent" id="edit_diagnosis" tabindex="-1" aria-labelledby="edit_diagnosisLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="edit_diagnosisLabel"><?php echo $this->lang->line('edit_diagnosis'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="form_editdiagnosis" accept-charset="utf-8" enctype="multipart/form-data" method="post">
                <div class="pup-scroll-area">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="mb-3">
                                    <label>
<?php echo $this->lang->line('report_type'); ?></label><small class="req"> *</small> 
                                    <input type="text" name="report_type" class="form-control" id="ereporttype" />
                                    <input type="hidden" value="" name="diagnosis_id" class="form-control" id="eid" /> 
                                    <input type="hidden" value="" name="diagnosispatient_id" class="form-control" id="epatient_id" />   
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="mb-3">
                                    <label>
<?php echo $this->lang->line('report_date'); ?></label><small class="req"> *</small>
                                    <input type="text" name="report_date" class="form-control date" id="ereportdate"/>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="mb-3">
                                    <label class="align-top"><?php echo $this->lang->line('document'); ?></label> <input type="file" class="form-control filestyle" name="report_document" id="ereportdocument" />
                                </div> 
                            </div>
                            <div class="col-sm-12">
                                <div class="mb-3">
                                    <label class="align-top"><?php echo $this->lang->line("report_center_name"); ?></label> <input type="text" class="form-control" name="report_center" id="ereportcenter" />
                                </div> 
                            </div>
                            <div class="col-sm-12">
                                <div class="mb-3">
                                    <label><?php echo $this->lang->line('description'); ?></label> 
                                    <textarea name="description" class="form-control" id="edescription"></textarea>
                                </div> 
                            </div>
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                    <button type="submit" id="form_editdiagnosisbtn" data-loading-text="<?php echo $this->lang->line('processing'); ?>" class="btn btn-info"><?php echo $this->lang->line('save'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Prescription -->
<div class="modal fade sh-modal sh-modal-nospace" id="add_prescription" tabindex="-1" aria-labelledby="edit_prescription_title" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="edit_prescription_title"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="form_prescription" class="modal-text-white" accept-charset="utf-8" enctype="multipart/form-data" method="post">
                <div class="pup-scroll-area">
                    <div class="modal-body">
                    </div><!--./modal-body-->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                    <button type="submit" name="save_print" value="save_print" data-loading-text='<i class="fa fa-circle-o-notch fa-spin"></i>' class="btn btn-info"><i class="fa fa-print"></i> <?php echo $this->lang->line('save_print'); ?></button>
                    <button type="submit" name="save" value="save" data-loading-text='<i class="fa fa-circle-o-notch fa-spin"></i>' class="btn btn-info"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- -->
<div class="modal fade sh-modal sh-modal-accent" id="viewModal" tabindex="-1" aria-labelledby="viewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewModalLabel"><?php echo $this->lang->line('patient_details'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formrevisit" accept-charset="utf-8" enctype="multipart/form-data" method="post">
                <div class="pup-scroll-area">
                    <div class="modal-body">
                        <div class="sh-form-card">
                            <div class="sh-card-header">
                                <span class="sh-card-header-title"><?php echo $this->lang->line('patient_details'); ?></span>
                            </div>
                            <div class="sh-info-grid">
                                <div class="row g-0">
                                    <div class="col-6 col-md-3 sh-info-item">
                                        <span class="sh-info-label"><?php echo $this->lang->line('patient_name'); ?></span>
                                        <span class="sh-info-value highlight"><span id="patient_name"></span> (<span id='patients_id'></span>)</span>
                                    </div>
                                    <div class="col-6 col-md-3 sh-info-item">
                                        <span class="sh-info-label"><?php echo $this->lang->line('guardian_name'); ?></span>
                                        <span class="sh-info-value"><span id='guardian_name'></span></span>
                                    </div>
                                    <div class="col-6 col-md-3 sh-info-item">
                                        <span class="sh-info-label"><?php echo $this->lang->line('gender'); ?></span>
                                        <span class="sh-info-value"><span id='gen'></span></span>
                                    </div>
                                    <div class="col-6 col-md-3 sh-info-item">
                                        <span class="sh-info-label"><?php echo $this->lang->line('marital_status'); ?></span>
                                        <span class="sh-info-value"><span id="marital_status"></span></span>
                                    </div>
                                </div>
                                <div class="row g-0 sh-row-divider">
                                    <div class="col-6 col-md-3 sh-info-item">
                                        <span class="sh-info-label"><?php echo $this->lang->line('phone'); ?></span>
                                        <span class="sh-info-value"><span id="contact"></span></span>
                                    </div>
                                    <div class="col-6 col-md-3 sh-info-item">
                                        <span class="sh-info-label"><?php echo $this->lang->line('email'); ?></span>
                                        <span class="sh-info-value"><span id='email'></span></span>
                                    </div>
                                    <div class="col-6 col-md-3 sh-info-item">
                                        <span class="sh-info-label"><?php echo $this->lang->line('address'); ?></span>
                                        <span class="sh-info-value"><span id='patient_address'></span></span>
                                    </div>
                                    <div class="col-6 col-md-3 sh-info-item">
                                        <span class="sh-info-label"><?php echo $this->lang->line('age'); ?></span>
                                        <span class="sh-info-value"><span id="age"></span></span>
                                    </div>
                                </div>
                                <div class="row g-0 sh-row-divider">
                                    <div class="col-6 col-md-3 sh-info-item">
                                        <span class="sh-info-label"><?php echo $this->lang->line('blood_group'); ?></span>
                                        <span class="sh-info-value"><span id="blood_group"></span></span>
                                    </div>
                                    <div class="col-6 col-md-3 sh-info-item">
                                        <span class="sh-info-label"><?php echo $this->lang->line('symptoms'); ?></span>
                                        <span class="sh-info-value"><span id='symptoms'></span></span>
                                    </div>
                                    <div class="col-6 col-md-3 sh-info-item">
                                        <span class="sh-info-label"><?php echo $this->lang->line('known_allergies'); ?></span>
                                        <span class="sh-info-value"><span id="known_allergies"></span></span>
                                    </div>
                                    <div class="col-6 col-md-3 sh-info-item">
                                        <span class="sh-info-label"><?php echo $this->lang->line('admission_date'); ?></span>
                                        <span class="sh-info-value"><span id="admission_date"></span></span>
                                    </div>
                                </div>
                                <div class="row g-0 sh-row-divider">
                                    <div class="col-6 col-md-3 sh-info-item">
                                        <span class="sh-info-label"><?php echo $this->lang->line('case'); ?></span>
                                        <span class="sh-info-value"><span id='case'></span></span>
                                    </div>
                                    <div class="col-6 col-md-3 sh-info-item">
                                        <span class="sh-info-label"><?php echo $this->lang->line('old_patient'); ?></span>
                                        <span class="sh-info-value"><span id='old_patient'></span></span>
                                    </div>
                                    <div class="col-6 col-md-3 sh-info-item">
                                        <span class="sh-info-label"><?php echo $this->lang->line('casualty'); ?></span>
                                        <span class="sh-info-value"><span id="casualty"></span></span>
                                    </div>
                                    <div class="col-6 col-md-3 sh-info-item">
                                        <span class="sh-info-label"><?php echo $this->lang->line('reference'); ?></span>
                                        <span class="sh-info-value"><span id="refference"></span></span>
                                    </div>
                                </div>
                                <div class="row g-0 sh-row-divider">
                                    <div class="col-6 col-md-3 sh-info-item">
                                        <span class="sh-info-label"><?php echo $this->lang->line('tpa'); ?></span>
                                        <span class="sh-info-value"><span id="organisation"></span></span>
                                    </div>
                                    <div class="col-6 col-md-3 sh-info-item">
                                        <span class="sh-info-label"><?php echo $this->lang->line('bed_group'); ?></span>
                                        <span class="sh-info-value"><span id="bed_group"></span></span>
                                    </div>
                                    <div class="col-6 col-md-3 sh-info-item">
                                        <span class="sh-info-label"><?php echo $this->lang->line('consultant_doctor'); ?></span>
                                        <span class="sh-info-value"><span id='doc'></span></span>
                                    </div>
                                    <div class="col-6 col-md-3 sh-info-item">
                                        <span class="sh-info-label"><?php echo $this->lang->line('bed_number'); ?></span>
                                        <span class="sh-info-value"><span id='bed_name'></span></span>
                                    </div>
                                </div>
                            </div>
                            <div id="field_data"></div>
                        </div>
                    </div>
                </div>
            </form>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('close'); ?></button>
            </div>
        </div>
    </div>
</div>

<!-- -->
<div class="modal fade sh-modal sh-modal-accent" id="prescriptionview" tabindex="-1" aria-labelledby="prescriptionviewLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered sh-modal-autoheight modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="prescriptionviewLabel"><?php echo $this->lang->line('prescription'); ?></h5>
                <div id='edit_deleteprescription' class="d-flex align-items-center gap-2 ms-auto me-2"></div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="pup-scroll-area">
                <div class="modal-body" id="getdetails_prescription"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('close'); ?></button>
            </div>
        </div>
    </div>
</div>

<!-- -->
<div class="modal fade sh-modal sh-modal-accent" id="myPaymentModal" tabindex="-1" aria-labelledby="myPaymentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myPaymentModalLabel"><?php echo $this->lang->line('add_payment'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="add_payment" accept-charset="utf-8" method="post">
                <input type="hidden" name="net_amount" value="<?php echo $total - $total_payment ?>">
                <input type="hidden" name="case_reference_id" id="case_reference_id" value="<?php echo $result['case_reference_id']; ?>">
                <input type="hidden" name="patient_id" value="<?php echo $result['id']; ?>">
                <input type="hidden" name="ipdid" value="<?php echo $ipdid ?>">
                <input type="hidden" name="total" id="total">
                <div class="modal-body">
                    <div class="sh-form-card">
                        <div class="sh-card-header">
                            <span class="sh-card-header-title"><i class="fa fa-money me-1"></i> <?php echo $this->lang->line('payment_details'); ?></span>
                        </div>
                        <div class="p-2">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label mb-1"><?php echo $this->lang->line('date'); ?> <small class="text-danger">*</small></label>
                                    <input type="text" name="payment_date" id="date" class="form-control datetime">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label mb-1"><?php echo $this->lang->line('amount') . ' (' . $currency_symbol . ')'; ?> <small class="text-danger">*</small></label>
                                    <input type="text" name="amount" id="amount" class="form-control" value="<?php echo $total - $total_payment ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label mb-1"><?php echo $this->lang->line('payment_mode'); ?></label>
                                    <select class="form-control payment_mode" name="payment_mode">
                                        <?php foreach ($payment_mode as $key => $value) { ?>
                                        <option value="<?php echo $key ?>" <?php if ($key == 'cash') { echo "selected"; } ?>><?php echo $value ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="col-12 cheque_div" style="display:none">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label mb-1"><?php echo $this->lang->line('cheque_no'); ?> <small class="text-danger">*</small></label>
                                            <input type="text" name="cheque_no" id="cheque_no" class="form-control">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label mb-1"><?php echo $this->lang->line('cheque_date'); ?> <small class="text-danger">*</small></label>
                                            <input type="text" name="cheque_date" id="cheque_date" class="form-control date">
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label mb-1"><?php echo $this->lang->line('attach_document'); ?></label>
                                            <input type="file" id="payment_file" class="filestyle form-control" name="document">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <label class="form-label mb-1"><?php echo $this->lang->line('note'); ?></label>
                                    <textarea name="note" id="note" rows="2" class="form-control"></textarea>
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

<!-- -->
<div class="modal fade sh-modal sh-modal-accent" id="myMedicationModal" tabindex="-1" aria-labelledby="myMedicationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <?php if ($this->rbac->hasPrivilege('ipd_medication', 'can_add')) { ?>
                <h5 class="modal-title" id="myMedicationModalLabel"><?php echo $this->lang->line("add_medication_dose"); ?></h5>
                <?php } ?>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
        <form id="add_medicationdose" accept-charset="utf-8" method="post">
                <input type="hidden" name="ipdid" id="mipdid" value="<?php echo $ipdid ?>">
                <input type="hidden" name="medicine_name_id" id="mpharmacy_id" value="">
                <input type="hidden" name="date" id="mdate" value="">
                <div class="modal-body">
                    <div class="sh-form-card">
                        <div class="sh-card-header">
                            <span class="sh-card-header-title"><i class="fa fa-medkit"></i> <?php echo $this->lang->line('add_medication_dose'); ?></span>
                        </div>
                        <div class="px-2 py-3">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label"><?php echo $this->lang->line('date'); ?><small class="req"> *</small></label>
                                        <input type="text" name="date" id="add_dose_date" class="form-control date">
                                        <span class="text-danger"><?php echo form_error('date'); ?></span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label"><?php echo $this->lang->line("time"); ?></label>
                                        <div class="input-group">
                                            <input type="text" name="time" class="form-control timepicker" id="add_dose_time" value="<?php echo set_value('time'); ?>">
                                            <div class="input-group-text"><i class="fa fa-clock-o"></i></div>
                                        </div>
                                        <span class="text-danger"><?php echo form_error('time'); ?></span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label"><?php echo $this->lang->line("medicine_category"); ?><small class="req"> *</small></label>
                                        <select class="form-control medicine_category_medication select2" id="add_dose_medicine_category" name='medicine_category_id'>
                                            <option value="<?php echo set_value('medicine_category_id'); ?>"><?php echo $this->lang->line('select'); ?></option>
                                            <?php foreach ($medicineCategory as $dkey => $dvalue) { ?>
                                            <option value="<?php echo $dvalue["id"]; ?>"><?php echo $dvalue["medicine_category"] ?></option>
                                            <?php } ?>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('medicine_category_id'); ?></span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label"><?php echo $this->lang->line("medicine_name"); ?><small class="req"> *</small></label>
                                        <select class="form-control select2 medicine_name_medication" id="add_dose_medicine_id" name='medicine_name_id'>
                                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('medicine_name_id'); ?></span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label"><?php echo $this->lang->line("dosage"); ?><small class="req"> *</small></label>
                                        <select class="form-control select2 dosage_medication" id="mdosage" onchange="" name='dosage'>
                                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('dosage'); ?></span>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
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
                    <button type="submit" id="add_medicationdosebtn" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i>" class="btn btn-info"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- -->

<div class="modal fade sh-modal sh-modal-accent" id="myaddMedicationModal" tabindex="-1" aria-labelledby="myaddMedicationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myaddMedicationModalLabel"><?php echo $this->lang->line("add_medication_dose"); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
        <form id="add_medication" accept-charset="utf-8" method="post">
                <input type="hidden" name="ipdid" value="<?php echo $ipdid ?>">
                <div class="modal-body">
                    <div class="sh-form-card">
                        <div class="sh-card-header">
                            <span class="sh-card-header-title"><i class="fa fa-medkit"></i> <?php echo $this->lang->line('add_medication_dose'); ?></span>
                        </div>
                        <div class="px-2 py-3">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label"><?php echo $this->lang->line('date'); ?><small class="req"> *</small></label>
                                        <input type="text" name="date" id="date" class="form-control date">
                                        <span class="text-danger"><?php echo form_error('date'); ?></span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label"><?php echo $this->lang->line("time"); ?></label>
                                        <div class="input-group">
                                            <input type="text" name="time" class="form-control timepicker" id="mtime" value="<?php echo set_value('time'); ?>">
                                            <div class="input-group-text"><i class="fa fa-clock-o"></i></div>
                                        </div>
                                        <span class="text-danger"><?php echo form_error('time'); ?></span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label"><?php echo $this->lang->line("medicine_category"); ?><small class="req"> *</small></label>
                                        <select class="form-control medicine_category_medication select2" id="mmedicine_category_id" name='medicine_category_id'>
                                            <option value="<?php echo set_value('medicine_category_id'); ?>"><?php echo $this->lang->line('select'); ?></option>
                                            <?php foreach ($medicineCategory as $dkey => $dvalue) { ?>
                                            <option value="<?php echo $dvalue["id"]; ?>"><?php echo $dvalue["medicine_category"] ?></option>
                                            <?php } ?>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('medicine_category_id'); ?></span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label"><?php echo $this->lang->line("medicine_name"); ?><small class="req"> *</small></label>
                                        <select class="form-control select2 medicine_name_medication" id="mmedicine_id" name='medicine_name_id'>
                                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('medicine_name_id'); ?></span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label"><?php echo $this->lang->line("dosage"); ?><small class="req"> *</small></label>
                                        <select class="form-control select2 dosage_medication" id="dosage" onchange="get_dosagename(this.value)" name='dosage'>
                                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('dosage'); ?></span>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
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
                    <button type="submit" id="add_medicationbtn" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i>" class="btn btn-info"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- -->

<!-- -->
<div class="modal fade sh-modal sh-modal-accent" id="myMedicationDoseModal" tabindex="-1" aria-labelledby="myMedicationDoseModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myMedicationDoseModalLabel"><?php echo $this->lang->line('edit_medication_dose'); ?></h5>
                <div id="edit_delete" class="ms-auto d-flex align-items-center gap-2"></div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
                <form id="update_medication" accept-charset="utf-8" method="post">
                    <input type="hidden" name="medication_id" id="medication_id" value="">
                    <input type="hidden" name="ipdid" value="<?php echo $ipdid ?>">
                    <div class="modal-body">
                        <div class="sh-form-card">
                            <div class="sh-card-header">
                                <span class="sh-card-header-title"><i class="fa fa-medkit"></i> <?php echo $this->lang->line('edit_medication_dose'); ?></span>
                            </div>
                            <div class="px-2 py-3">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label"><?php echo $this->lang->line('date'); ?><small class="req"> *</small></label>
                                            <input type="text" name="date" id="date_edit_medication" class="form-control date">
                                            <span class="text-danger"><?php echo form_error('date'); ?></span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label"><?php echo $this->lang->line("time"); ?></label>
                                            <div class="input-group">
                                                <input type="text" name="time" class="form-control timepicker" id="dosagetime" value="<?php echo set_value('time'); ?>">
                                                <div class="input-group-text"><i class="fa fa-clock-o"></i></div>
                                            </div>
                                            <span class="text-danger"><?php echo form_error('time'); ?></span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label"><?php echo $this->lang->line("medicine_category"); ?><small class="req"> *</small></label>
                                            <select class="form-control medicine_category_medication select2" id="mmedicine_category_edit_id" name='medicine_category_id'>
                                                <option value="<?php echo set_value('medicine_category_id'); ?>"><?php echo $this->lang->line('select'); ?></option>
                                                <?php foreach ($medicineCategory as $dkey => $dvalue) { ?>
                                                <option value="<?php echo $dvalue["id"]; ?>"><?php echo $dvalue["medicine_category"] ?></option>
                                                <?php } ?>
                                            </select>
                                            <span class="text-danger"><?php echo form_error('medicine_category_id'); ?></span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label"><?php echo $this->lang->line("medicine_name"); ?><small class="req"> *</small></label>
                                            <select class="form-control select2 medicine_name_medication" id="mmedicine_edit_id" name='medicine_name_id'>
                                                <option value=""><?php echo $this->lang->line('select'); ?></option>
                                            </select>
                                            <span class="text-danger"><?php echo form_error('medicine_name_id'); ?></span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label"><?php echo $this->lang->line("dosage"); ?><small class="req"> *</small></label>
                                            <select class="form-control select2" id="medicine_dose_edit_id" name='dosage_id'>
                                                <option value="<?php echo set_value('dosage_id'); ?>"><?php echo $this->lang->line('select'); ?></option>
                                                <?php foreach ($dosage as $key => $value) { ?>
                                                <option value="<?php echo $value["id"]; ?>"><?php echo $value["dosage"]." ".$value['unit']; ?></option>
                                                <?php } ?>
                                            </select>
                                            <span class="text-danger"><?php echo form_error('dosage_id'); ?></span>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
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
                        <button type="submit" id="update_medicationbtn" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i>" class="btn btn-info"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                    </div>
                </form>
        </div>
    </div>
</div>
<!-- -->

<!--Add Charges-->
<div class="modal fade sh-modal sh-modal-accent" id="myChargesModal" tabindex="-1" aria-labelledby="myChargesModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myChargesModalLabel"><?php echo $this->lang->line('add_charges'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="add_charges" accept-charset="utf-8" method="post">
                <input type="hidden" name="patient_id" id="patient_id" value="<?php echo $result['patient_id'] ?>">
                <input type="hidden" name="ipdid" value="<?php echo $ipdid ?>">
                <input type="hidden" name="patient_charge_id" id="editpatient_charge_id" value="0">
                <input type="hidden" name="organisation_id" id="organisation_id" value="<?php echo $result["organisation_id"] ?>">
                <input type="hidden" name="insurance_validity" id="insurance_validity" value="<?php echo $result["insurance_validity"] ?>">
                <input type="hidden" name="insurance_id" id="insurance_id" value="<?php echo $result["insurance_id"] ?>">
                <input type="hidden" class="reset_value total_charge" id="total_charge" name="total_charge" value="0">
                <div class="pup-scroll-area">
                    <div class="modal-body modal-background">

                        <!-- Card 1: Charge input -->
                        <div class="sh-form-card mb-3">
                            <div class="sh-card-header">
                                <span class="sh-card-header-title"><?php echo $this->lang->line('add_charges'); ?></span>
                                <div class="ms-auto">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="1" id="is_tpa" name="is_tpa" onclick="reset_fields()">
                                        <label class="form-check-label" for="is_tpa"><?php echo $this->lang->line('apply_tpa'); ?></label>
                                    </div>
                                </div>
                            </div>
                            <div class="p-2">
                                <div class="row g-2">
                                    <div class="col-sm-2">
                                        <label class="form-label"><?php echo $this->lang->line('charge_type'); ?> <small class="text-danger">*</small></label>
                                        <select name="charge_type" id="add_charge_type" class="form-control form-control-sm charge_type select2">
                                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                                            <?php foreach ($charge_type as $key => $value) { ?>
                                            <option value="<?php echo $value->id; ?>"><?php echo $value->charge_type; ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    <div class="col-sm-2">
                                        <label class="form-label"><?php echo $this->lang->line('charge_category'); ?> <small class="text-danger">*</small></label>
                                        <select name="charge_category" id="charge_category" class="form-control form-control-sm charge_category select2">
                                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                                        </select>
                                    </div>
                                    <div class="col-sm-2">
                                        <label class="form-label"><?php echo $this->lang->line('charge_name'); ?> <small class="text-danger">*</small></label>
                                        <select name="charge_id" id="charge_id" class="form-control form-control-sm charge select2">
                                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                                        </select>
                                    </div>
                                    <div class="col-sm-2">
                                        <label class="form-label"><?php echo $this->lang->line('standard_charge') . ' (' . $currency_symbol . ')'; ?></label>
                                        <input type="text" readonly name="standard_charge" id="standard_charge" class="form-control form-control-sm standard_charge" value="<?php echo set_value('standard_charge'); ?>">
                                    </div>
                                    <div class="col-sm-2">
                                        <label class="form-label"><?php echo $this->lang->line('tpa_charge') . ' (' . $currency_symbol . ')'; ?></label>
                                        <input type="text" readonly name="schedule_charge" id="schedule_charge" class="form-control form-control-sm schedule_charge" value="">
                                    </div>
                                    <div class="col-sm-2">
                                        <label class="form-label"><?php echo $this->lang->line('qty'); ?> <small class="text-danger">*</small></label>
                                        <input type="text" name="qty" id="qty" value="1" class="form-control form-control-sm qty">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Note+Date+Add & Billing summary side by side -->
                        <div class="d-flex flex-wrap gap-3">
                            <div class="sh-flex-col">
                                <div class="sh-form-card h-100 overflow-hidden">
                                    <div class="sh-card-header">
                                        <span class="sh-card-header-title"><?php echo $this->lang->line('net_amount'); ?></span>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between px-3 py-2 border-bottom">
                                        <span class="text-secondary small fw-semibold"><?php echo $this->lang->line('total') . ' (' . $currency_symbol . ')'; ?></span>
                                        <input type="text" placeholder="<?= $this->lang->line('total') ?>" value="0" name="apply_charge" id="apply_charge" class="form-control form-control-sm text-end total apply_charge_add_charge sh-bill-summary-input" readonly>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between px-3 py-2 border-bottom">
                                        <span class="text-secondary small fw-semibold"><?php echo $this->lang->line('discount_percentage') . ' (' . $currency_symbol . ')'; ?></span>
                                        <div class="d-flex gap-2">
                                            <div class="input-group input-group-sm sh-discount-pct-group">
                                                <input type="text" value="0" name="discount_percentage" id="discount_percentage_add_charge" class="form-control text-end discount_percentage_add_charge">
                                                <span class="input-group-text">%</span>
                                            </div>
                                            <input type="text" value="0" onkeyup="get_percentage(this.value)" placeholder="<?php echo $this->lang->line('discount'); ?>" name="discount_percentage_amount" id="discount_percentage_amount" class="form-control form-control-sm text-end discount_percentage_amount sh-bill-summary-input">
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between px-3 py-2 border-bottom">
                                        <span class="text-secondary small fw-semibold"><?php echo $this->lang->line('tax') . ' (' . $currency_symbol . ')'; ?></span>
                                        <div class="d-flex gap-2">
                                            <div class="input-group input-group-sm sh-discount-pct-group">
                                                <input type="text" name="charge_tax" id="charge_tax" class="form-control text-end charge_tax" readonly>
                                                <span class="input-group-text">%</span>
                                            </div>
                                            <input type="text" value="0" name="tax" id="tax" class="form-control form-control-sm text-end tax sh-bill-summary-input" readonly>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between px-3 py-2">
                                        <span class="text-secondary small fw-semibold"><?php echo $this->lang->line('net_amount') . ' (' . $currency_symbol . ')'; ?></span>
                                        <input type="text" placeholder="<?= $this->lang->line('net_amount') ?>" value="0" name="amount" id="final_amount" class="form-control form-control-sm text-end fw-semibold net_amount sh-bill-summary-input" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="sh-flex-col">
                                <div class="sh-form-card h-100">
                                    <div class="sh-card-header">
                                        <span class="sh-card-header-title"><?php echo $this->lang->line('charge_note'); ?></span>
                                    </div>
                                    <div class="p-2">
                                        <div class="row g-2">
                                            <div class="col-12">
                                                <label class="form-label"><?php echo $this->lang->line('date'); ?> <small class="text-danger">*</small></label>
                                                <input id="datetimepicker" name="date" type="text" class="form-control form-control-sm datetime">
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label"><?php echo $this->lang->line('charge_note'); ?></label>
                                                <textarea name="note" id="edit_note" rows="3" class="form-control form-control-sm"></textarea>
                                            </div>
                                            <div class="col-12 text-end">
                                                <button type="submit" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i>" name="charge_data" value="add" class="btn btn-info btn-sm"><i class="fa fa-plus me-1"></i><?php echo $this->lang->line('add'); ?></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

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
                                                <th><?php echo $this->lang->line('qty'); ?></th>
                                                <th class="text-end"><?php echo $this->lang->line('standard_charge') . ' (' . $currency_symbol . ')'; ?></th>
                                                <th class="text-end"><?php echo $this->lang->line('tpa_charge') . ' (' . $currency_symbol . ')'; ?></th>
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

<div class="modal fade sh-modal sh-modal-accent" id="myChargeseditModal" tabindex="-1" aria-labelledby="myChargeseditModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myChargeseditModalLabel"><?php echo $this->lang->line('edit_charges'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="edit_charges" accept-charset="utf-8" method="post">
                <input type="hidden" name="patient_id" id="patient_id" value="<?php echo $result["patient_id"] ?>">
                <input type="hidden" name="ipdid" value="<?php echo $ipdid ?>">
                <input type="hidden" name="patient_charge_id" id="patient_charge_id" value="0">
                <input type="hidden" name="organisation_id" id="edit_organisation_id" value="<?php echo $result["organisation_id"] ?>">
                <input type="hidden" name="insurance_validity" id="edit_insurance_validity" value="<?php echo $result["insurance_validity"] ?>">
                <input type="hidden" name="insurance_id" id="edit_insurance_id" value="<?php echo $result["insurance_id"] ?>">
                <input type="hidden" class="reset_value total_charge" id="edit_total_charge" name="edit_total_charge" value="0">
                <div class="pup-scroll-area">
                    <div class="modal-body modal-background">

                        <!-- Card 1: Charge input -->
                        <div class="sh-form-card mb-3">
                            <div class="sh-card-header">
                                <span class="sh-card-header-title"><?php echo $this->lang->line('edit_charges'); ?></span>
                                <div class="ms-auto">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="1" id="edit_is_tpa" name="is_tpa" onclick="reset_fields()">
                                        <label class="form-check-label" for="edit_is_tpa"><?php echo $this->lang->line('apply_tpa'); ?></label>
                                    </div>
                                </div>
                            </div>
                            <div class="p-2">
                                <div class="row g-2">
                                    <div class="col-sm-2">
                                        <label class="form-label"><?php echo $this->lang->line('charge_type'); ?> <small class="text-danger">*</small></label>
                                        <select name="charge_type" id="edit_charge_type" class="form-control form-control-sm charge_type select2">
                                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                                            <?php foreach ($charge_type as $key => $value) { ?>
                                            <option value="<?php echo $value->id; ?>"><?php echo $value->charge_type; ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    <div class="col-sm-2">
                                        <label class="form-label"><?php echo $this->lang->line('charge_category'); ?> <small class="text-danger">*</small></label>
                                        <select name="charge_category" id="editcharge_category" class="form-control form-control-sm charge_category select2">
                                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                                        </select>
                                    </div>
                                    <div class="col-sm-2">
                                        <label class="form-label"><?php echo $this->lang->line('charge_name'); ?> <small class="text-danger">*</small></label>
                                        <select name="charge_id" id="editcharge_id" class="form-control form-control-sm charge select2">
                                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                                        </select>
                                    </div>
                                    <div class="col-sm-2">
                                        <label class="form-label"><?php echo $this->lang->line('standard_charge') . ' (' . $currency_symbol . ')'; ?></label>
                                        <input type="text" readonly name="standard_charge" id="editstandard_charge" class="form-control form-control-sm standard_charge" value="<?php echo set_value('standard_charge'); ?>">
                                    </div>
                                    <div class="col-sm-2">
                                        <label class="form-label"><?php echo $this->lang->line('tpa_charge') . ' (' . $currency_symbol . ')'; ?></label>
                                        <input type="text" readonly name="schedule_charge" id="editschedule_charge" class="form-control form-control-sm schedule_charge" value="">
                                    </div>
                                    <div class="col-sm-2">
                                        <label class="form-label"><?php echo $this->lang->line('qty'); ?> <small class="text-danger">*</small></label>
                                        <input type="text" name="qty" id="editqty" class="form-control form-control-sm qty">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Note+Date & Billing summary side by side -->
                        <div class="d-flex flex-wrap gap-3">
                            <div class="sh-flex-col">
                                <div class="sh-form-card h-100">
                                    <div class="sh-card-header">
                                        <span class="sh-card-header-title"><?php echo $this->lang->line('charge_note'); ?></span>
                                    </div>
                                    <div class="p-2">
                                        <div class="row g-2">
                                            <div class="col-12">
                                                <label class="form-label"><?php echo $this->lang->line('date'); ?> <small class="text-danger">*</small></label>
                                                <input id="editcharge_date" name="date" type="text" class="form-control form-control-sm datetime">
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label"><?php echo $this->lang->line('charge_note'); ?></label>
                                                <textarea name="note" id="enote" rows="3" class="form-control form-control-sm"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="sh-flex-col">
                                <div class="sh-form-card h-100 overflow-hidden">
                                    <div class="sh-card-header">
                                        <span class="sh-card-header-title"><?php echo $this->lang->line('net_amount'); ?></span>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between px-3 py-2 border-bottom">
                                        <span class="text-secondary small fw-semibold"><?php echo $this->lang->line('total') . ' (' . $currency_symbol . ')'; ?></span>
                                        <input type="text" placeholder="<?= $this->lang->line('total') ?>" value="0" name="apply_charge" id="editapply_charge" class="form-control form-control-sm text-end total sh-bill-summary-input" readonly>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between px-3 py-2 border-bottom">
                                        <span class="text-secondary small fw-semibold"><?php echo $this->lang->line('discount') . ' (' . $currency_symbol . ')'; ?></span>
                                        <div class="d-flex gap-2">
                                            <div class="input-group input-group-sm sh-discount-pct-group">
                                                <input type="text" name="editdiscount_percentage" id="editdiscount_percentage" class="form-control text-end discount_percentage_add_charge">
                                                <span class="input-group-text">%</span>
                                            </div>
                                            <input type="text" value="0" onkeyup="update_percentage(this.value)" placeholder="<?php echo $this->lang->line('discount'); ?>" name="discount_percentage" id="editdiscount" class="form-control form-control-sm text-end discount_percentage_amount sh-bill-summary-input">
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between px-3 py-2 border-bottom">
                                        <span class="text-secondary small fw-semibold"><?php echo $this->lang->line('tax') . ' (' . $currency_symbol . ')'; ?></span>
                                        <div class="d-flex gap-2">
                                            <div class="input-group input-group-sm sh-discount-pct-group">
                                                <input type="text" name="charge_tax" id="editcharge_tax" class="form-control text-end charge_tax" readonly>
                                                <span class="input-group-text">%</span>
                                            </div>
                                            <input type="text" value="0" name="tax" id="edittax" class="form-control form-control-sm text-end tax sh-bill-summary-input" readonly>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between px-3 py-2">
                                        <span class="text-secondary small fw-semibold"><?php echo $this->lang->line('net_amount') . ' (' . $currency_symbol . ')'; ?></span>
                                        <input type="text" placeholder="<?= $this->lang->line('net_amount') ?>" value="0" name="amount" id="editfinal_amount" class="form-control form-control-sm text-end fw-semibold net_amount sh-bill-summary-input" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                    <button type="submit" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i>" name="charge_data" value="save" class="btn btn-info"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- -->

<div class="modal fade sh-modal sh-modal-accent" id="myModaledit" tabindex="-1" aria-labelledby="myModaleditLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myModaleditLabel"><span id="evlistname"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formeditrecord" accept-charset="utf-8" enctype="multipart/form-data" method="post">
                <input name="patient_id" id="evpatients_id" type="hidden" value="<?php echo $result['id'] ?>">
                <input name="otid" id="otid" type="hidden" value="<?php echo set_value('id'); ?>">
                <input type="hidden" id="updateid" name="updateid">
                <input type="hidden" id="ipdid_edit" name="ipdid">
                <input type="hidden" id="previous_bed_id" name="previous_bed_id">
                <div class="pup-scroll-area">
                    <div class="modal-body">
                        <div class="row" id="evpatientDetails" style="display:none">
                            <div class="col-md-9 col-sm-9 col-9">
                                <ul class="singlelist">
                                    <li class="singlelist24bold"><span id="evlistname_old"></span></li>
                                    <li>
                                        <i class="fas fa-user-secret" data-bs-toggle="tooltip" data-placement="top" title="<?php echo $this->lang->line('guardian'); ?>"></i>
                                        <span id="evguardian"></span>
                                    </li>
                                </ul>
                                <ul class="multilinelist">
                                    <li>
                                        <i class="fas fa-venus-mars" data-bs-toggle="tooltip" data-placement="top" title="<?php echo $this->lang->line('gender'); ?>"></i>
                                        <span id="evgenders"></span>
                                    </li>
                                    <li>
                                        <i class="fas fa-tint" data-bs-toggle="tooltip" data-placement="top" title="<?php echo $this->lang->line('blood_group'); ?>"></i>
                                        <span id="evblood_group"></span>
                                    </li>
                                    <li>
                                        <i class="fas fa-ring" data-bs-toggle="tooltip" data-placement="top" title="<?php echo $this->lang->line('marital_status'); ?>"></i>
                                        <span id="evmarital_status"></span>
                                    </li>
                                </ul>
                                <ul class="singlelist">
                                    <li>
                                        <i class="fas fa-hourglass-half" data-bs-toggle="tooltip" data-placement="top" title="<?php echo $this->lang->line('age'); ?>"></i>
                                        <span id="evage"></span>
                                    </li>
                                    <li>
                                        <i class="fa fa-phone-square" data-bs-toggle="tooltip" data-placement="top" title="<?php echo $this->lang->line('phone'); ?>"></i>
                                        <span id="evlistnumber"></span>
                                    </li>
                                    <li>
                                        <i class="fa fa-envelope" data-bs-toggle="tooltip" data-placement="top" title="<?php echo $this->lang->line('email'); ?>"></i>
                                        <span id="evemail"></span>
                                    </li>
                                    <li>
                                        <i class="fas fa-street-view" data-bs-toggle="tooltip" data-placement="top" title="<?php echo $this->lang->line('address'); ?>"></i>
                                        <span id="evaddress"></span>
                                    </li>
                                    <li>
                                        <b><?php echo $this->lang->line('any_known_allergies') ?></b>
                                        <span id="evallergies"></span>
                                    </li>
                                    <li>
                                        <b><?php echo $this->lang->line('remarks') ?></b>
                                        <span id="evnote"></span>
                                    </li>
                                    <li>
                                        <b><?php echo $this->lang->line('tpa') ?></b>
                                        <span id="etpa_name"></span>
                                    </li>
                                    <li>
                                        <b><?php echo $this->lang->line('tpa_id') ?></b>
                                        <span id="etpa_id"></span>
                                    </li>
                                    <li>
                                        <b><?php echo $this->lang->line('tpa_validity') ?></b>
                                        <span id="etpa_validity"></span>
                                    </li>
                                    <li>
                                        <b><?php echo $this->lang->line('national_identification_number') ?></b>
                                        <span id="eidentification_number"></span>
                                    </li>
                                </ul>
                            </div>
                            <div class="col-md-3 col-sm-3 col-3">
                                <div class="float-end">
                                    <?php $file = "uploads/patient_images/no_image.png"; ?>
                                    <img class="profile-user-img img-fluid" src="<?php echo $this->media_storage->getImageURL($file) ?>" id="evimage" alt="User profile picture">
                                </div>
                            </div>
                        </div>
                        <div class="row g-2">
                            <div class="col-lg-7 border-end">
                                <div id="evajax_load"></div>
                                <div class="sh-form-card h-100">
                                    <div class="sh-card-header">
                                        <span class="sh-card-header-title"><?php echo $this->lang->line('visit_details'); ?></span>
                                    </div>
                                    <div class="p-2">
                                        <div class="row g-2">
                                            <div class="col-sm-6">
                                                <label class="form-label"><?php echo $this->lang->line('symptoms_type'); ?></label>
                                                <select name='symptoms_type' id="act" class="form-control form-control-sm select2 act">
                                                    <option value=""><?php echo $this->lang->line('select'); ?></option>
                                                    <?php foreach ($symptomsresulttype as $dkey => $dvalue) { ?>
                                                    <option value="<?php echo $dvalue['id']; ?>"><?php echo $dvalue['symptoms_type']; ?></option>
                                                    <?php } ?>
                                                </select>
                                                <span class="text-danger"><?php echo form_error('symptoms_type'); ?></span>
                                            </div>
                                            <div class="col-sm-6">
                                                <label class="form-label"><?php echo $this->lang->line('symptoms_title'); ?></label>
                                                <div id="dd" class="wrapper-dropdown-3">
                                                    <input class="form-control form-control-sm filterinput" type="text">
                                                    <ul class="dropdown scroll150 section_ul">
                                                        <li><label class="checkbox"><?php echo $this->lang->line('select'); ?></label></li>
                                                    </ul>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label"><?php echo $this->lang->line('symptoms_description'); ?></label>
                                                <textarea rows="3" name="symptoms" id="symptoms_description" class="form-control form-control-sm"></textarea>
                                            </div>
                                            <div class="col-sm-6">
                                                <label class="form-label"><?php echo $this->lang->line('icd10_groups'); ?></label>
                                                <select id="edit_icd10_group_filter" class="form-control form-control-sm select2">
                                                    <option value=""><?php echo $this->lang->line('select_group'); ?></option>
                                                    <?php if (!empty($icd10_groups)) { foreach ($icd10_groups as $g) { ?>
                                                    <option value="<?php echo $g['id']; ?>"><?php echo html_escape($g['group_name']); ?></option>
                                                    <?php } } ?>
                                                </select>
                                            </div>
                                            <div class="col-sm-6">
                                                <label class="form-label"><?php echo $this->lang->line('icd10_diagnosis'); ?></label>
                                                <select name="icd_code_ids[]" id="edit_icd_code_ids" class="form-control form-control-sm select2" multiple>
                                                    <?php if (!empty($icd10_codes)) { foreach ($icd10_codes as $c) { ?>
                                                    <option value="<?php echo $c['id']; ?>">[<?php echo html_escape($c['icd_code']); ?>] <?php echo html_escape($c['icd_description']); ?></option>
                                                    <?php } } ?>
                                                </select>
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label"><?php echo $this->lang->line('note'); ?></label>
                                                <textarea name="note" id='evnoteipd' rows="3" class="form-control form-control-sm"><?php echo set_value('note'); ?></textarea>
                                            </div>
                                            <div class="col-12" id="customfield"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-5">
                                <div class="sh-form-card h-100">
                                    <div class="sh-card-header">
                                        <span class="sh-card-header-title"><?php echo $this->lang->line('ipd_details'); ?></span>
                                    </div>
                                    <div class="p-2">
                                        <div class="row g-2">
                                            <div class="col-sm-6">
                                                <label class="form-label"><?php echo $this->lang->line('admission_date'); ?><small class="req"> *</small></label>
                                                <input id="edit_admission_date" name="appointment_date" type="text" class="form-control form-control-sm datetime">
                                                <span class="text-danger"><?php echo form_error('appointment_date'); ?></span>
                                            </div>
                                            <div class="col-sm-6">
                                                <label class="form-label"><?php echo $this->lang->line('case'); ?></label>
                                                <input class="form-control form-control-sm" type="text" id="patient_case" name='case_type'>
                                                <span class="text-danger"><?php echo form_error('case'); ?></span>
                                            </div>
                                            <div class="col-sm-6">
                                                <label class="form-label"><?php echo $this->lang->line('casualty'); ?></label>
                                                <select name="casualty" id="patient_casualty" class="form-control form-control-sm">
                                                    <option value="<?php echo $this->lang->line('yes') ?>"><?php echo $this->lang->line('yes') ?></option>
                                                    <option value="<?php echo $this->lang->line('no') ?>" selected><?php echo $this->lang->line('no') ?></option>
                                                </select>
                                            </div>
                                            <div class="col-sm-6">
                                                <label class="form-label"><?php echo $this->lang->line('old_patient'); ?></label>
                                                <select name="old_patient" id="old" class="form-control form-control-sm">
                                                    <option value="<?php echo $this->lang->line('yes') ?>"><?php echo $this->lang->line('yes') ?></option>
                                                    <option value="<?php echo $this->lang->line('no') ?>"><?php echo $this->lang->line('no') ?></option>
                                                </select>
                                            </div>
                                            <div class="col-sm-6">
                                                <label class="form-label"><?php echo $this->lang->line('credit_limit') . " (" . $currency_symbol . ")"; ?></label>
                                                <input type="text" id="credits_limits" name="credit_limit" class="form-control form-control-sm">
                                            </div>
                                            <div class="col-sm-6">
                                                <label class="form-label"><?php echo $this->lang->line('reference'); ?></label>
                                                <input class="form-control form-control-sm" type="text" name='refference' id="patient_refference">
                                                <span class="text-danger"><?php echo form_error('refference'); ?></span>
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label"><?php echo $this->lang->line('consultant_doctor'); ?><small class="req"> *</small></label>
                                                <select class="form-control form-control-sm select2" <?php if ($disable_option == true) { echo "disabled"; } ?> name='cons_doctor' id="patient_consultant">
                                                    <option value=""><?php echo $this->lang->line('select') ?></option>
                                                    <?php foreach ($doctors as $dkey => $dvalue) { ?>
                                                    <option value="<?php echo $dvalue['id']; ?>"><?php echo composeStaffNameByString($dvalue['name'], $dvalue['surname'], $dvalue['employee_id']); ?></option>
                                                    <?php } ?>
                                                </select>
                                                <?php if ($disable_option == true) { ?>
                                                <input type="hidden" name="cons_doctor" value="<?php echo $doctor_select ?>">
                                                <?php } ?>
                                                <span class="text-danger"><?php echo form_error('cons_doctor'); ?></span>
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label"><?php echo $this->lang->line('bed_group'); ?></label>
                                                <select class="form-control form-control-sm" name='bed_group_id' id='ebed_group_id' onchange="getBed(this.value, '', 'yes','ebed_nos')">
                                                    <option value=""><?php echo $this->lang->line('select'); ?></option>
                                                    <?php foreach ($bedgroup_list as $key => $bedgroup) { ?>
                                                    <option value="<?php echo $bedgroup['id'] ?>"><?php echo $bedgroup['name'] . " - " . $bedgroup['floor_name'] ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label"><?php echo $this->lang->line('bed_no'); ?><small class="req"> *</small></label>
                                                <select class="form-control form-control-sm select2" name='bed_no' id='ebed_nos'>
                                                    <option value=""><?php echo $this->lang->line('select'); ?></option>
                                                </select>
                                                <span class="text-danger"><?php echo form_error('bed_no'); ?></span>
                                            </div>
                                            <?php if ($result['gender'] != 'Male') { ?>
                                            <div class="col-sm-6">
                                                <label class="form-label vertical-align-middle"><?php echo $this->lang->line('is_antenatal'); ?></label>
                                                <input type="checkbox" name="is_for_antenatal" id="is_antenatal">
                                            </div>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                    <button type="submit" id="formeditrecordbtn" data-loading-text='<i class="fa fa-circle-o-notch fa-spin"></i>' class="btn btn-info"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- discharged summary   -->
<div class="modal fade sh-modal sh-modal-accent" id="myModaldischarged" tabindex="-1" aria-labelledby="myModaldischargedLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myModaldischargedLabel"><?php echo $this->lang->line('discharged_summary'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div id='summary_print'></div>
            <form id="formdishrecord" accept-charset="utf-8" enctype="multipart/form-data" method="post">
                <div class="pup-scroll-area">
                    <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12">
                            <div class="row row-eq">
                                <div class="col-lg-12 col-md-12 col-sm-12">
                                    <div class="ptt10">
                                        <div id="evajax_load"></div>
                                        <div class="row" id="" >
                                            <div class="col-md-12 col-sm-12 col-12">
                                                <ul class="multilinelist">                                                 
                                                      <li>  <label for="pwd"><?php echo $this->lang->line('name'); ?></label>                                  
                                                        <span id="disevlistname"></span>
                                                    </li>
                                                     <li>
                                                        <label for="pwd"><?php echo $this->lang->line('age'); ?></label>
                                                        <span id="disevage"></span>
                                                    </li> 
                                                     <li>
                                                        <label for="pwd"><?php echo $this->lang->line('gender'); ?></label>
                                                        <span id="disevgenders" ></span>
                                                    </li>
                                                </ul>   
                                                <ul class="multilinelist">                                                    
                                                    <li>
                                                         <label><?php echo $this->lang->line('admission_date'); ?></label>
                                                        <span id="disedit_admission_date"></span>
                                                    </li> 
                                                    <li>
                                                         <label><?php echo $this->lang->line('discharged_date'); ?></label>
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
                                                    <label for="pwd"><?php echo $this->lang->line('diagnosis'); ?></label>
                                                    <input name="diagnosis" id='disdiagnosis' rows="3" class="form-control" >
                                                </div>
                                            </div>
                                            <div class="col-sm-12">
                                                <div class="mb-3">
                                                    <label for="pwd"><?php echo $this->lang->line('operation'); ?></label>
                                                    <input name="operation" id='disoperation'  class="form-control" >
                                                </div>
                                            </div>                                                 
                                            <div class="col-sm-12">
                                                <div class="mb-3">
                                                    <label for="pwd"><?php echo $this->lang->line('note'); ?></label> 
                                                    <textarea name="note" id='disevnoteipd' rows="3" class="form-control" ><?php echo set_value('note'); ?></textarea>
                                                </div>
                                            </div>                                          
                                            <div class="col-md-12"> 
                                                <div class="dividerhr"></div>
                                            </div><!--./col-md-12-->                                         
                                            <div class="col-sm-4">
                                                <div class="mb-3">
                                                    <label for="pwd"><?php echo $this->lang->line('investigation'); ?></label> 
                                                    <textarea name="investigations" id='disinvestigations' rows="3" class="form-control" ><?php echo set_value('note'); ?></textarea>
                                                </div>
                                            </div>
                                            <div class="col-sm-8">
                                                <div class="mb-3">
                                                    <label for="pwd"><?php echo $this->lang->line('treatment_at_home'); ?></label> 
                                                    <textarea name="treatment_at_home" id='distreatment_at_home' rows="3" class="form-control" ><?php echo set_value('note'); ?></textarea>
                                                </div>
                                            </div>     
                                        </div>
                                        <input name="patient_id" id="disevpatients_id" type="hidden">
                                        <input type="hidden" id="disupdateid" name="updateid">
                                        <input type="hidden" id="disipdid" name="ipdid">
                                        </div>
                                </div>                               
                            </div><!--./row-->   
                        </div><!--./col-md-12-->       
                    </div><!--./row--> 
                </div>             
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                    <button type="submit" id="formdishrecordbtn" data-loading-text="<?php echo $this->lang->line('processing') ?>" class="btn btn-info"> <?php echo $this->lang->line('save'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- discharged summary   -->

<!-- Add Instruction -->
<div class="modal fade sh-modal sh-modal-accent" id="add_instruction" tabindex="-1" aria-labelledby="add_instructionLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="add_instructionLabel"><?php echo $this->lang->line('add_consultant_register'); ?></h5>
                <button type="button" class="btn-close close_button" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="consultant_register_form" accept-charset="utf-8" enctype="multipart/form-data" method="post">
                <input type="hidden" name="patient_id" id="ins_patient_id" value="<?php echo $result["id"] ?>">
                <input type="hidden" name="ipdid" value="<?php echo $ipdid ?>">
                <div class="modal-body">
                    <div class="sh-form-card">
                        <div class="sh-card-header">
                            <span class="sh-card-header-title"><i class="fa fa-user-md me-1"></i> <?php echo $this->lang->line('consultant_register'); ?></span>
                        </div>
                        <div class="p-2">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label mb-1"><?php echo $this->lang->line('applied_date'); ?> <small class="text-danger">*</small></label>
                                    <input type="text" name="date" value="" class="form-control datetime">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label mb-1"><?php echo $this->lang->line('consultant_date'); ?> <small class="text-danger">*</small></label>
                                    <input type="text" id="instruction_date" name="insdate" value="<?php echo set_value('date', date($this->customlib->getHospitalDateFormat())); ?>" class="form-control date">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label mb-1"><?php echo $this->lang->line('consultant_doctor'); ?> <small class="text-danger">*</small></label>
                                    <input type="hidden" name="doctor" id="doctor_set">
                                    <select name="doctor_field" <?php if ($disable_option == true) { echo "disabled"; } ?> id="doctor_field" class="form-control select2">
                                        <option value=""><?php echo $this->lang->line('select'); ?></option>
                                        <?php foreach ($doctors as $key => $value) { ?>
                                        <option <?php if ((isset($doctor_select)) && ($doctor_select == $dvalue["id"])) { echo "selected"; } ?> value="<?php echo $value["id"] ?>"><?php echo $value["name"] . " " . $value["surname"] . " (" . $value["employee_id"] . ")"; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label class="form-label mb-1"><?php echo $this->lang->line('instruction'); ?> <small class="text-danger">*</small></label>
                                    <textarea name="instruction" rows="3" class="form-control"></textarea>
                                </div>
                                <div class="col-12">
                                    <?php echo display_custom_fields('ipdconsultinstruction'); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                    <button type="submit" id="consultant_registerbtn" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i>" class="btn btn-info"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- -->
<div class="modal fade sh-modal sh-modal-accent" id="findingview" tabindex="-1" aria-labelledby="findingviewLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
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
                <div class="modal-body" id="getdetails_finding"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('close'); ?></button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade sh-modal sh-modal-accent" id="edit_instruction" tabindex="-1" aria-labelledby="edit_instructionLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="edit_instructionLabel"><?php echo $this->lang->line('edit_consultant_register'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editconsultant_register_form" accept-charset="utf-8" enctype="multipart/form-data" method="post">
                <input type="hidden" name="instruction_id" id="instruction_id" value="">
                <input type="hidden" name="ipdid" value="<?php echo $ipdid ?>">
                <div class="modal-body">
                    <div class="sh-form-card">
                        <div class="sh-card-header">
                            <span class="sh-card-header-title"><i class="fa fa-user-md me-1"></i> <?php echo $this->lang->line('consultant_register'); ?></span>
                        </div>
                        <div class="p-2">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label mb-1"><?php echo $this->lang->line('applied_date'); ?> <small class="text-danger">*</small></label>
                                    <input type="text" name="date" id="ecdate" value="" class="form-control datetime">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label mb-1"><?php echo $this->lang->line('instruction_date'); ?> <small class="text-danger">*</small></label>
                                    <input type="text" id="ecinsdate" name="insdate" value="<?php echo set_value('date', date($this->customlib->getHospitalDateFormat())); ?>" class="form-control date">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label mb-1"><?php echo $this->lang->line('consultant_doctor'); ?> <small class="text-danger">*</small></label>
                                    <input type="hidden" name="doctor" id="editdoctor_set">
                                    <select name="doctor_field" <?php if ($disable_option == true) { echo "disabled"; } ?> id="editdoctor_field" class="form-control select2">
                                        <option value=""><?php echo $this->lang->line('select'); ?></option>
                                        <?php foreach ($doctors as $key => $value) { ?>
                                        <option <?php if ((isset($doctor_select)) && ($doctor_select == $dvalue["id"])) { echo "selected"; } ?> value="<?php echo $value["id"] ?>"><?php echo $value["name"] . " " . $value["surname"] . " (" . $value["employee_id"] . ")"; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label class="form-label mb-1"><?php echo $this->lang->line('instruction'); ?> <small class="text-danger">*</small></label>
                                    <textarea name="instruction" id="ecinstruction" rows="3" class="form-control"></textarea>
                                </div>
                                <div class="col-12" id="customfieldconsult"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                    <button type="submit" id="editconsultant_registerbtn" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i>" class="btn btn-info"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade sh-modal sh-modal-accent" id="add_nurse_note" tabindex="-1" aria-labelledby="add_nurse_noteLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="add_nurse_noteLabel"><?php echo $this->lang->line('add_nurse_note'); ?></h5>
                <button type="button" class="btn-close close_button" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="nurse_note_form" accept-charset="utf-8" enctype="multipart/form-data" method="post">
                <input name="patient_id" placeholder="" id="nurse_patient_id" value="<?php echo $result["id"] ?>" type="hidden" class="form-control" />
                <input type="hidden" name="ipdid" value="<?php echo $ipdid ?>">
                <div class="modal-body">
                    <div class="sh-form-card">
                        <div class="sh-card-header">
                            <span class="sh-card-header-title"><i class="fas fa-notes-medical"></i> <?php echo $this->lang->line('add_nurse_note'); ?></span>
                        </div>
                        <div class="px-2 py-3">
                            <div class="row g-3">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label class="form-label"><?php echo $this->lang->line('date'); ?><small class="req"> *</small></label>
                                        <input type="text" name="date" value="" class="form-control datetime">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label class="form-label"><?php echo $this->lang->line('nurse'); ?><small class="req"> *</small></label>
                                        <input type="hidden" name="nurse" id="nurse_set">
                                        <select name="nurse_field" <?php if ($disable_option == true) { echo "disabled"; } ?> id="nurse_field" class="form-control select2">
                                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                                            <?php foreach ($nurse as $key => $value) { ?>
                                            <option <?php if ((isset($nurse_select)) && ($nurse_select == $dvalue["id"])) { echo "selected"; } ?> value="<?php echo $value["id"] ?>"><?php echo composeStaffNameByString($value["name"],$value["surname"],$value["employee_id"]); ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label class="form-label"><?php echo $this->lang->line('note'); ?> <small class="req"> *</small></label>
                                        <textarea name="note" rows="3" class="form-control"></textarea>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label class="form-label"><?php echo $this->lang->line('comment'); ?> <small class="req"> *</small></label>
                                        <textarea name="comment" rows="3" class="form-control"></textarea>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <?php echo display_custom_fields('ipdnursenote'); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                    <button type="submit" id="nurse_notebtn" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i>" class="btn btn-info"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- change bed -->
<div class="modal fade sh-modal sh-modal-accent" id="alot_bed" tabindex="-1" aria-labelledby="alot_bedLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="alot_bedLabel"><?php echo $this->lang->line('bed'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
         <form id="alot_bed_form" accept-charset="utf-8" enctype="multipart/form-data" method="post">
            <div class="pup-scroll-area">
                <div class="modal-body">
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12">
                            <div class="alert alert-info">
<?php echo $this->lang->line('bed_alot_message') ?>
                            </div>
                            <div class="row">
                                <input name="patient_id" placeholder=""  value="<?php echo $result["id"] ?>" type="hidden" class="form-control"   />
                                <div class="col-md-12">
                                    <label><?php echo $this->lang->line('bed') . " " . $this->lang->line('group'); ?><small class="req"> *</small></label>
                                    <select class="form-control" onchange="getBed(this.value, '', 'yes', 'alotbedoption')" name="bedgroup">
                                        <option value=""><?php echo $this->lang->line('select') ?></option>
<?php foreach ($bedgroup_list as $key => $bedgroup) {
    ?>
                                            <option value="<?php echo $bedgroup["id"] ?>"><?php echo $bedgroup["name"] . " - " . $bedgroup["floor_name"] ?></option>
<?php } ?>
                                    </select>
                                </div>
                                <div class="col-md-12 sh-mt-10">
                                    <label><?php echo $this->lang->line('bed') . " " . $this->lang->line('no'); ?><small class="req"> *</small></label>
                                    <select class="form-control select2" id="alotbedoption" name="bedno">
                                    </select>
                                </div>
                                <div class="col-md-12 sh-mt-10">
                                </div>
                            </div>
                       </div>
                    </div>
                 </form>
            </div>
            </div>
            <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                    <button type="submit" id="alotbedbtn" form="alot_bed_form" data-loading-text="<?php echo $this->lang->line('processing') ?>"  class="btn btn-info"><?php echo $this->lang->line('save'); ?></button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade sh-modal sh-modal-accent" id="view_ot_modal" tabindex="-1" aria-labelledby="view_ot_modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="view_ot_modalLabel"><?php echo $this->lang->line('operation_details'); ?></h5>
                <div class="d-flex align-items-center gap-2 ms-auto" id="action_detail_modal"></div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="pup-scroll-area">
                <div class="modal-body min-h-3">
                    <div id="show_ot_data"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('close'); ?></button>
            </div>
        </div>
    </div>
</div>

<!--lab investigation modal-->
<div class="modal fade sh-modal sh-modal-accent sh-modal-autoheight" id="viewDetailReportModal" tabindex="-1" aria-labelledby="modal_head" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal_head"></h5>
                <div id="action_detail_report_modal" class="ms-auto me-2 d-flex align-items-center gap-2"></div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="pup-scroll-area">
                <div class="modal-body">
                    <div id="reportbilldata"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('close'); ?></button>
            </div>
        </div>
    </div>
</div>
<!-- end lab investigation modal-->

<div class="modal fade sh-modal sh-modal-accent" id="editpayment_modal" tabindex="-1" aria-labelledby="editpayment_modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form id="editpaymentform" accept-charset="utf-8" method="post">
                <div class="modal-header">
                    <h5 class="modal-title" id="editpayment_modalLabel"><?php echo $this->lang->line('payment_details'); ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="pup-scroll-area">
                    <div class="modal-body">
                        <div class="sh-form-card">
                            <div class="sh-card-header">
                                <span class="sh-card-header-title"><?php echo $this->lang->line('payment_details'); ?></span>
                            </div>
                            <div class="p-2">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label mb-1"><?php echo $this->lang->line('date'); ?> <small class="text-danger">*</small></label>
                                        <input type="text" name="payment_date" id="payment_date" class="form-control datetime" autocomplete="off">
                                        <input type="hidden" class="form-control" id="edit_payment_id" name="edit_payment_id">
                                        <span class="text-danger"></span>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label mb-1"><?php echo $this->lang->line('amount') . " (" . $currency_symbol . ")"; ?> <small class="text-danger">*</small></label>
                                        <input type="text" name="amount" id="edit_payment" class="form-control" value="">
                                        <span class="text-danger"><?php echo form_error('amount'); ?></span>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label mb-1"><?php echo $this->lang->line('payment_mode'); ?></label>
                                        <select class="form-control payment_mode" name="payment_mode" id="payment_mode">
                                            <?php foreach ($payment_mode as $key => $value) { ?>
                                                <option value="<?php echo $key ?>" <?php if ($key == 'cash') { echo "selected"; } ?>><?php echo $value ?></option>
                                            <?php } ?>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('apply_charge'); ?></span>
                                    </div>
                                    <div class="col-12 cheque_div" style="display: none;">
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label class="form-label mb-1"><?php echo $this->lang->line('cheque_no'); ?> <small class="text-danger">*</small></label>
                                                <input type="text" name="cheque_no" id="edit_cheque_no" class="form-control">
                                                <span class="text-danger"><?php echo form_error('cheque_no'); ?></span>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label mb-1"><?php echo $this->lang->line('cheque_date'); ?> <small class="text-danger">*</small></label>
                                                <input type="text" name="cheque_date" id="edit_cheque_date" class="form-control date">
                                                <span class="text-danger"><?php echo form_error('cheque_date'); ?></span>
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label mb-1"><?php echo $this->lang->line('attach_document'); ?></label>
                                                <input type="file" class="filestyle form-control" name="document">
                                                <span class="text-danger"><?php echo form_error('document'); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label mb-1"><?php echo $this->lang->line('note'); ?></label>
                                        <input type="text" name="note" id="edit_payment_note" class="form-control"/>
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

<!-- Add Obstetric history -->
<div class="modal fade sh-modal sh-modal-accent" id="add_obstetric" tabindex="-1" aria-labelledby="obstetric_title" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="obstetric_title"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="form_obstetric" accept-charset="utf-8" enctype="multipart/form-data" method="post">
                <input type="hidden" id="patient_id" name="patient_id" value="<?php echo $result['id']; ?>" >
                <input type="hidden" id="obstetric_ipdid" name="obstetric_ipdid" value="<?php echo $ipdid; ?>" >
                <div class="pup-scroll-area">
                    <div class="modal-body">
                    </div> <!--./modal-body-->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                    <button type="submit" name="save" value="save" data-loading-text="<?php echo $this->lang->line('processing'); ?>" class="btn btn-info add_obstetricbtn"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div><!-- Add Obstetric history -->

<div class="modal fade sh-modal sh-modal-accent" id="postnatal" tabindex="-1" aria-labelledby="postnatal_title" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="postnatal_title"></h5>
                <div class="d-flex align-items-center gap-2 ms-auto me-2" id="edit_deletepostnatal"></div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="form_postnatal" accept-charset="utf-8" enctype="multipart/form-data" method="post">
                <input type="hidden" id="patient_id" name="patient_id" value="<?php echo $result['id']; ?>">
                <input type="hidden" id="postnatal_id" name="postnatal_id" value="">
                <input type="hidden" id="postnatal_ipd_id" name="postnatal_ipd_id" value="<?php echo $ipdid ?>">
                <div class="pup-scroll-area">
                    <div class="modal-body">
                        <div class="sh-form-card">
                            <div class="sh-card-header">
                                <span class="sh-card-header-title"><i class="fa fa-child"></i> <?php echo $this->lang->line('postnatal_history'); ?></span>
                            </div>
                            <div class="px-2 py-3">
                                <div class="row g-3">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label class="form-label"><?php echo $this->lang->line('labor_time'); ?><small class="req"> *</small></label>
                                            <input type="text" class="form-control datetime" name="labor_time" id="labor_time">
                                            <span class="text-danger"><?php echo form_error('labor_time'); ?></span>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label class="form-label"><?php echo $this->lang->line('delivery_time'); ?><small class="req"> *</small></label>
                                            <input type="text" class="form-control datetime" name="delivery_time" id="delivery_time">
                                            <span class="text-danger"><?php echo form_error('delivery_time'); ?></span>
                                        </div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label class="form-label"><?php echo $this->lang->line('routine_question'); ?></label>
                                            <textarea class="form-control" name="routine_question" id="routine_question" rows="3"></textarea>
                                            <span class="text-danger"><?php echo form_error('routine_question'); ?></span>
                                        </div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label class="form-label"><?php echo $this->lang->line('general_remark'); ?></label>
                                            <textarea class="form-control" name="general_remark" id="general_remark" rows="3"></textarea>
                                            <span class="text-danger"><?php echo form_error('general_remark'); ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                    <button type="submit" name="save" value="save" data-loading-text="<?php echo $this->lang->line('processing'); ?>" class="btn btn-info"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div><!-- postnatal history -->

<!-- Add Obstetric prescription -->
<div class="modal fade sh-modal sh-modal-accent" id="viewobstetrichistory" tabindex="-1" aria-labelledby="viewobstetrichistoryLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewobstetrichistoryLabel"><?php echo $this->lang->line('previous_obstetric_history'); ?></h5>
                <div class="d-flex align-items-center gap-2 ms-auto me-2">
                    <span id='edit_printhistory'></span>
                    <span id='edit_edithistory'></span>
                    <span id='edit_deletehistory'></span>
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

<!-- Add Obstetric prescription -->
<div class="modal fade sh-modal sh-modal-nospace" id="obstetric_prescription" tabindex="-1" aria-labelledby="antenatal_title" aria-hidden="true">
    <div class="modal-dialog pup100 modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="antenatal_title"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="antenatal_prescription" accept-charset="utf-8" enctype="multipart/form-data" method="post">
                <input type="hidden" id="visit_details_id" name="visit_details_id" value="<?php echo $result['id']; ?>" >
                <div class="pup-scroll-area">
                    <div class="modal-body">
                    </div> <!--./modal-body-->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                    <button type="submit" name="save" value="save" data-loading-text="<?php echo $this->lang->line('processing'); ?>" class="btn btn-info"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div><!-- end  Obstetric prescription -->

<!-- Add Obstetric prescription -->
<div class="modal fade sh-modal sh-modal-nospace" id="ipdantenatalprescription" tabindex="-1" aria-labelledby="ipdantenatal_title" aria-hidden="true">
    <div class="modal-dialog pup100 modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ipdantenatal_title"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="antenatal_ipdprescription" accept-charset="utf-8" enctype="multipart/form-data" method="post">
                <input type="hidden" id="visit_details_id" name="visit_details_id" value="<?php echo $result['id']; ?>" >
                 <div class="pup-scroll-area">
                    <div class="modal-body">
                    </div> <!--./modal-body-->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                    <button type="submit" name="save" value="save" id="save_antenatal_btn" data-loading-text="<?php echo $this->lang->line('processing'); ?>" class="btn btn-info"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div><!-- end  Obstetric prescription -->

<div class="modal fade sh-modal sh-modal-accent" id="vitalModal" tabindex="-1" aria-labelledby="vitalModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="vitalModalLabel"><span id="modal_title"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formadd" accept-charset="utf-8" method="post">
                <div class="pup-scroll-area">
                    <div class="modal-body">
                        <div class="sh-form-card">
                            <div class="sh-card-header">
                                <span class="sh-card-header-title"><i class="fa fa-heartbeat me-1"></i><?php echo $this->lang->line('add_vital'); ?></span>
                            </div>
                            <div class="p-2">
                                <input type="hidden" class="id" name="patient_id" id="patient_id" value="<?php echo $result['patient_id']; ?>">
                                <div class="table-responsive overflow-visible">
                                    <table class="table table-striped mb0 table-bordered table-hover tablefull12 tblProducts" id="tableID_vitals">
                                        <thead>
                                            <tr class="font13 white-space-nowrap">
                                                <th><?php echo $this->lang->line('vital_name'); ?><small class="req"> *</small></th>
                                                <th><?php echo $this->lang->line('vital_value'); ?><small class="req"> *</small></th>
                                                <th><?php echo $this->lang->line('date'); ?><small class="req"> *</small></th>
                                                <th class="text-end"></th>
                                            </tr>
                                        </thead>
                                        <tr id="row_vitals1">
                                            <td>
                                                <input type="hidden" name="total_rows_vitals[]" id="calculate" value="1">
                                                <select name="vital_name_1" id="vital_name_1" class="form-control">
                                                    <option value=""><?php echo $this->lang->line("select"); ?></option>
                                                    <?php foreach($vital_list as $row){ ?>
                                                    <option value="<?php echo $row['id']; ?>"><?php echo $row['name']; ?><?php if($row['reference_range']){ echo ' ('.$row['reference_range'] . " " .$row['unit'] .')'; } ?></option>
                                                    <?php } ?>
                                                </select>
                                                <span class="text-danger"><?php echo form_error('charge_type[]'); ?></span>
                                            </td>
                                            <td>
                                                <input autofocus="" name="from_reference_range_1" id="from_reference_range_1" type="text" class="form-control" />
                                                <span class="text-danger"><?php echo form_error('from_reference_range[]'); ?></span>
                                            </td>
                                            <td>
                                                <input id="vital_date_1" name="vital_date_1" type="text" class="form-control datetime" />
                                                <span class="text-danger"><?php echo form_error('vital_date'); ?></span>
                                            </td>
                                            <td class="text-center align-middle">
                                                <button type="button" class="btn btn-sm btn-outline-danger delete_vitals_row" data-row-id="1" autocomplete="off"><i class="fa fa-remove"></i></button>
                                            </td>
                                        </tr>
                                    </table>
                                    <div class="d-flex justify-content-end">
                                        <a class="btn btn-info addplus-xs add-vital-record mt-2" data-added="0"><i class="fa fa-plus"></i> <?php echo $this->lang->line('add'); ?></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                    <button type="submit" id="formaddbtn" data-loading-text="<?php echo $this->lang->line('processing'); ?>" class="btn btn-info"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Vital -->
<div class="modal fade sh-modal sh-modal-accent" id="myvitalEditModal" tabindex="-1" aria-labelledby="myvitalEditModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myvitalEditModalLabel"><?php echo $this->lang->line('edit_vital'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="edit_vital" accept-charset="utf-8" enctype="multipart/form-data" method="post">
                <div class="pup-scroll-area">
                    <div class="modal-body">
                        <div class="sh-form-card">
                            <div class="sh-card-header">
                                <span class="sh-card-header-title"><i class="fa fa-heartbeat me-1"></i><?php echo $this->lang->line('edit_vital'); ?></span>
                            </div>
                            <div class="p-2">
                                <input type="hidden" name="patient_id" id="vepatientid" value="">
                                <input type="hidden" name="evital_id" id="evital_id" value="">
                                <input type="hidden" name="evital_name" id="evital_name" value="">
                                <div class="row g-3">
                                    <div class="col-12">
                                        <label class="form-label mb-1"><?php echo $this->lang->line('vital_name'); ?> <small class="text-danger">*</small></label>
                                        <select id="evital_name_edit" name="evital_name_edit" class="form-control" disabled>
                                            <option value=""><?php echo $this->lang->line("select"); ?></option>
                                            <?php foreach($vital_list as $row){ ?>
                                            <option value="<?php echo $row['id']; ?>"><?php echo $row['name']; ?><?php if($row['reference_range']){ echo ' ('.$row['reference_range'] . " " .$row['unit'] .')'; } ?></option>
                                            <?php } ?>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('vital_title'); ?></span>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label mb-1"><?php echo $this->lang->line('vital_value'); ?> <small class="text-danger">*</small></label>
                                        <input id="evital_value" name="evital_value" type="text" class="form-control" />
                                        <span class="text-danger"><?php echo form_error('description'); ?></span>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label mb-1"><?php echo $this->lang->line('vital_date'); ?> <small class="text-danger">*</small></label>
                                        <input id="evital_date" name="emessure_date" type="text" class="form-control datetime" autocomplete="off" />
                                        <span class="text-danger"><?php echo form_error('vital_date'); ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                    <button type="submit" data-loading-text="<?php echo $this->lang->line('processing'); ?>" id="edit_vitalbtn" class="btn btn-info"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<script type="text/javascript">
var date_format = '<?php echo strtr($this->customlib->getHospitalDateFormat(), ['d' => 'dd', 'm' => 'MM', 'Y' => 'yyyy',]) ?>';
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

<script>
    $(function () {
        //Initialize Select2 Elements
        $('.select2').select2()
    });
</script>

<script>
    var orgid="<?php echo $result['organisation_id'] ?>";
    var prescription_rows=2;
    var selected_medicine_category_id=1;

$(function () {
    $('.select2').select2();
});

</script>

<script>
    var ipd_id ="<?php echo $ipdid ?>";
    $(document).on('change', '.act', function () {
        $this = $(this);
        var sys_val = $(this).val();
        var section_ul = $(this).closest('div.row').find('ul.section_ul');
        $.ajax({
            type: 'POST',
            url: base_url + 'admin/patient/getPartialsymptoms',
            data: {'sys_id': sys_val},
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
</script>
<script type="text/javascript"> 
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

    $(document).on('click', '.filterinput', function (e) {
        e.stopPropagation();
        var wrapper = $(this).closest('div.wrapper-dropdown-3');
        var isOpen  = wrapper.hasClass('active');
        $('div.wrapper-dropdown-3').removeClass('active');
        if (!isOpen) { wrapper.addClass('active'); }
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
    function addpaymentModal() {
        var total = $("#charge_total").val();
        var patient_id = '<?php echo $result["id"] ?>';
        $("#total").val(total);
        $("#payment_file").dropify();
        $("#payment_patient_id").val(patient_id);
        shModal('myPaymentModal').show();
    }

    function addmedicationModal() {
        shModal('myaddMedicationModal').show();
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
                $("#mdosage").select2("val", '');
                $("#add_dose_medicine_category").select2("val",medicine_category_id);
                getMedicineForMedication(medicine_category_id,pharmacy_id);
                $("#add_dose_date").val(date);
                $("#mpharmacy_id").val(pharmacy_id);
                $("#mdate").val(date);
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
                console.log(data.dosagetime);                
                $("#date_edit_medication").val(data.date);
				$('#dosagetime').val(timeConvert(data.time));				
                $('select[id="medicine_dose_id"] option[value="' + data.medicine_dosage_id + '"]').attr("selected", "selected");
                $("#medicine_dose_edit_id").select2().select2('val', data.medicine_dosage_id);
                $("#mmedicine_category_edit_id ").val(data.medicine_category_id).trigger('change');
                getMedicineForMedication(data.medicine_category_id,data.pharmacy_id);
                $("#medicine_dosage_remark").val(data.remark);
                $("#medication_id").val(data.id);
                <?php if ($this->rbac->hasPrivilege('ipd_medication', 'can_delete')) {  ?>
                $('#edit_delete').html("<a href='#' class='delete_record_dosage btn btn-light btn-sm' data-record-id='"+ medication_id + "' data-bs-toggle='tooltip' title='<?php echo $this->lang->line('delete'); ?>'><i class='fa fa-trash'></i></a>");
                <?php } ?>
                shModal('myMedicationDoseModal').show();
            },
        });
    }

$('#myChargesModal').on('hidden.bs.modal', function (e) {
    $(".charge",$(this)).select2('destroy').val("").select2().find('option:not(:first)').remove();
     $('.charge_type ',$(this)).select2('val', '');
     $('.charge_category',$(this)).select2('destroy').val("").select2().find('option:not(:first)').remove();
});

 $("#add_instruction").on('hidden.bs.modal', function (e) {
     $(".filestyle").next(".dropify-clear").trigger("click");
     $('#consultant_register_form #doctor_field').select2("val", "");
     $('form#consultant_register_form').find('input:text, input:password, input:file, textarea').val('');
     $('form#consultant_register_form').find('select option:selected').removeAttr('selected');
     $('form#consultant_register_form').find('input:checkbox, input:radio').removeAttr('checked');
 });

 $("#add_operationtheatre").on('hidden.bs.modal', function (e) {
     $(".filestyle").next(".dropify-clear").trigger("click");
     $('#form_operationtheatre #operation_category').select2("val", "");
     $('#form_operationtheatre #operation_name').select2("val", "");
     $('#form_operationtheatre #consultant_doctorid').select2("val", "");
     $('form#form_operationtheatre').find('input:text, input:password, input:file, textarea').val('');
     $('form#form_operationtheatre').find('select option:selected').removeAttr('selected');
     $('form#form_operationtheatre').find('input:checkbox, input:radio').removeAttr('checked');
 });

    $('#modal-chkstatus').on('shown.bs.modal', function (e) {
            var $modalDiv = $(e.delegateTarget);            
            var id=$(e.relatedTarget).data('id');            
            
            $.ajax({
                type: "POST",
                url: base_url + 'admin/zoom_conference/getlivestatus',
                data: {'id':id},
                dataType: "JSON",
                beforeSend: function () {
                $('#zoom_details').html("");
                    $modalDiv.addClass('modal_loading');
                },
                success: function (data) {                    
                   $('#zoom_details').html(data.page);
                    $modalDiv.removeClass('modal_loading');
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    $modalDiv.removeClass('modal_loading');
                },
                complete: function (data) {
                    $modalDiv.removeClass('modal_loading');
                }
            });
        })

    function getRecord(ipdid) {
        $.ajax({
            url: '<?php echo base_url(); ?>admin/patient/getIpdDetails',
            type: "POST",
            data: {ipdid: ipdid},
            dataType: 'json',
            success: function (data) {
                shModal('viewModal').show();
                var table_html = '';
                if (data.field_data) {
                    $.each(data.field_data, function (i, obj) {
                        var field_value = (obj.field_value == null) ? "" : obj.field_value;
                        var name = obj.name;
                        table_html += "<div class='row'><div class='col-lg-2 col-md-2 col-sm-2'><span ><b>" + capitalizeFirstLetter(name) + "</b></span></div></div> <div class='row'><div class='col-lg-10 col-md-10 col-sm-10'><span >" + field_value + "</span></div></div><br>";
                    });
                }
                $("#field_data").html(table_html);
                $("#patients_id").html(data.patient_id);
                $("#patient_name").html(data.patient_name);
                $("#contact").html(data.mobileno);
                $("#email").html(data.email);
                $("#age").html(data.age);
                $("#gen").html(data.gender);
                $("#guardian_name").html(data.guardian_name);
                $("#admission_date").html(data.date);
                $("#case").html(data.case_type);
                $("#casualty").html(data.casualty);
                $("#symptoms").html(data.symptoms);
                $("#known_allergies").html(data.known_allergies);
                $("#refference").html(data.refference);
                $("#doc").html(data.name + ' ' + data.surname + ' (' + data.employee_id + ')');
                $("#amount").html(data.amount);
                $("#tax").html(data.tax);
                $("#blood_group").html(data.blood_group_name);
                $("#old_patient").html(data.patient_old);
                $("#payment_mode").html(data.payment_mode);
                $("#organisation").html(data.organisation_name);
                $("#opdid").val(data.opdid);
                $("#patient_address").html(data.address);
                $("#marital_status").html(data.marital_status);
                $("#note").val(data.note);
                $("#bed_group").html(data.bedgroup_name + '-' + data.floor_name);
                $("#bed_name").html(data.bed_name);
                $("#etpa_id").html(data.insurance_id);
                $("#etpa_validity").html(data.insurance_validity);
                $("#eidentification_number").html(data.identification_number);
                $("#evblood_group").html(data.blood_group_name);
            },
            error: function (xhr, status, error) {
                console.error('getRecord AJAX error:', status, error, xhr.responseText);
            },
        });
    }

    function getEditRecord(ipdid) {
        
        $.ajax({
            url: '<?php echo base_url(); ?>admin/patient/getIpdDetails',
            type: "POST",
            data: {ipdid: ipdid},
            dataType: 'json',
            success: function (data) {
                $('#customfield').html(data.custom_fields_value);
                $('#evlistname').html(data.patient_name+" ("+data.patient_id+")");
                $('#evguardian').html(data.guardian_name);
                $('#evlistnumber').html(data.mobileno);
                $('#evemail').html(data.email);
                $("#etpa_name").html(data.organisation_name);
                $("#etpa_id").html(data.insurance_id);
                $("#etpa_validity").html(data.einsurance_validity);
                $("#eidentification_number").html(data.identification_number);
                $("#evaddress").html(data.address);
                $("#enote").html(data.note);
                $("#evgenders").html(data.gender);
                $("#evmarital_status").html(data.marital_status);
                $("#evblood_group").html(data.blood_group_name);
                $("#evallergies").html(data.known_allergies);
                $("#patients_ids").val(data.patient_unique_id);
                $("#patient_names").val(data.patient_name);
                $("#edit_admission_date").val(data.date);
                $("#contacts").val(data.mobileno);
                $("#patient_image").val(data.image);
                $("#emails").val(data.email);
                $("#ages").val(data.age);
                $("#months").val(data.month);
                $("#evheight").val(data.height);
                $("#evweight").val(data.weight);
                $("#evbp").val(data.bp);
                $("#evpulse").val(data.pulse);
                $("#evtemperature").val(data.temperature);
                $("#evrespiration").val(data.respiration);
                $("#edit_patient_address").val(data.address);
                $("#patient_case").val(data.case_type);
                $("#symptoms_description").val(data.symptoms);
                $("#patient_allergies").val(data.known_allergies);
                $("#evnoteipd").val(data.ipdnote);
                $("#patient_refference").val(data.refference);
                $("#guardian_names").val(data.guardian_name);
                $("#credits_limits").val(data.ipdcredit_limit);
                $("#ipdid_edit").val(data.ipdid);
                $("#ipdid").val(data.ipdid);
                $("#previous_bed_id").val(data.bed);
                $("#evpatients_id").val(data.patient_id);                
                if(data.is_antenatal != 0){                    
                     $('#is_antenatal').prop('checked', true); 
                }else{                    
                    $('#is_antenatal').prop('checked', false); 
                }                
                $("#ebed_group_id").val(data.bed_group_id).attr('selected', true);              
                getBed(data.bed_group_id, data.bed, 'yes','ebed_nos'); 
                $('select[id="patient_consultant"] option[value="' + data.cons_doctor + '"]').attr("selected", "selected");
                $('select[id="patient_casualty"] option[value="' + data.casualty + '"]').attr("selected", "selected");
                $('select[id="old"] option[value="' + data.patient_old + '"]').attr("selected", "selected");
                $('select[id="genders"] option[value="' + data.gender + '"]').attr("selected", "selected");
                $('select[id="marital_statuss"] option[value="' + data.marital_status + '"]').attr("selected", "selected");
                $("#patient_consultant").select2().select2('val', data.cons_doctor);
                // $("#evaddpatient_id").select2().select2('val', data.patient_id);
                $('.select2').select2();
                loadIpdIcdCodes(ipdid);
                shModal('myModaledit').show();
            },
        });
    }

    function get_doctoripd(ipdid) {
         shModal('add_doctor').show();
    }

    function getEditRecordDischarged(id, ipdid) {       
        var active = '<?php echo $result['is_active'] ?>';
        $.ajax({
            url: '<?php echo base_url(); ?>admin/patient/getIpdDetails',
            type: "POST",
            data: {recordid: id, ipdid: ipdid, active: active},
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
                $("#disedit_admission_date").html(data.date);
                $("#disedit_discharge_date").html(data.discharge_date);
                $("#disipdid").val(data.ipdid);
                $("#disupdateid").val(data.summary_id);
                $("#disevpatients_id").val(data.patient_id);
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
            url: base_url + 'admin/patient/getsummaryDetails/' + insert_id,
            type: 'POST',
            data: {id: insert_id, print: 'yes'},
            success: function (result) {
                popup(result);
            }
        });
    }

    function get_ePatientDetails(id) {
        var base_url = "<?php echo $this->media_storage->getImageURL('backend/images/loading.gif') ?>";
        $("#ajax_load").html("<center><img src='" + base_url + "'/>");
        $.ajax({
            url: '<?php echo base_url(); ?>admin/patient/patientDetails',
            type: "POST",
            data: {id: id},
            dataType: 'json',
            success: function (res) {
                if (res) {
                    $("#evajax_load").html("");
                    $("#evpatientDetails").show();
                    $('#evpatient_unique_id').html(res.patient_unique_id);
                    $('#evlistname').html(res.patient_name+" ("+res.id+")");
                    $('#evpatients_id').val(res.id);
                    $('#evguardian').html(res.guardian_name);
                    $('#evlistnumber').html(res.mobileno);
                    $('#evemail').html(res.email);
                    $("#evage").html(res.patient_age );
                    $('#evdoctname').val(res.name + " " + res.surname);
                    $("#evbp").html(res.bp);
                    $("#esymptoms").html(res.symptoms);
                    $("#evknown_allergies").html(res.known_allergies);
                    $("#evaddress").html(res.address);
                    $("#evnote").html(res.note);
                    $("#evgenders").html(res.gender);
                    $("#evmarital_status").html(res.marital_status);
                    $("#evblood_group").html(res.blood_group_name);
                    $("#evallergies").html(res.known_allergies);
                    $("#evimage").attr("src",res.image+'<?php echo img_time(); ?>');
                } else {
                    $("#evajax_load").html("");
                    $("#evpatientDetails").hide();
                }
            }
        });
    }

    $(document).ready(function (e) {
        modal_click_disabled('add_prescription', 'patient_discharge', 'vitalModal', 'myvitalEditModal', 'myTimelineModal', 'add_operationtheatre', 'viewModal', 'myModaledit', 'add_doctor', 'myModaldischarged', 'edit_diagnosis', 'myTimelineEditModal', 'nursenoteEditModal', 'edit_operationtheatre', 'findingview');

        $("#formeditrecord").on('submit', (function (e) {
            $("#formeditrecordbtn").btnLoading();
            e.preventDefault();
            $.ajax({
                url: '<?php echo base_url(); ?>admin/patient/ipd_update',
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
                    $("#formeditrecordbtn").btnReset();
                },
                error: function () {
                   
                }
            });
        }));
    });

    $(document).ready(function (e) {
        $("#formdishrecord").on('submit', (function (e) {
            $("#formdishrecordbtn").btnLoading();
            e.preventDefault();
            $.ajax({
                url: '<?php echo base_url(); ?>admin/patient/add_discharged_summary',
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

    $(document).ready(function (e) {
        $("#alot_bed_form").on('submit', (function (e) {
            $("#alotbedbtn").btnLoading();
            e.preventDefault();
            var bedid = $("#alotbedoption").val();            
            var ipdid = '<?php echo $ipdid ?>';
            var patient_id = '<?php echo $result["id"] ?>';
            $.ajax({
                url: '<?php echo base_url(); ?>admin/patient/updatebed',
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
                        revert(patient_id,bedid, ipdid);
                    }
                    $("#alotbedbtn").btnReset();
                },
                error: function () {

                }
            });
        }));
    });

    function editRecord(id, opdid) {
        $.ajax({
            url: '<?php echo base_url(); ?>admin/patient/opd_details',
            type: "POST",
            data: {recordid: id, opdid: opdid},
            dataType: 'json',
            success: function (data) {
                $("#patientid").val(data.patient_unique_id);
                $("#patientname").val(data.patient_name);
                $("#appointmentdate").val(data.appointment_date);
                $("#edit_case").val(data.case_type);
                $("#edit_symptoms").val(data.symptoms);
                $("#edit_casualty").val(data.casualty);
                $("#edit_knownallergies").val(data.known_allergies);
                $("#edit_refference").val(data.refference);
                $("#edit_consdoctor").val(data.cons_doctor);
                $("#edit_amount").val(data.amount);
                $("#edit_tax").val(data.tax);
                $("#edit_paymentmode").val(data.payment_mode);
                $("#edit_opdid").val(opdid);
            },
        });
    }

    $(document).ready(function (e) {
        $("#add_payment").on('submit', (function (e) {
            e.preventDefault();            
            $.ajax({
                url: '<?php echo base_url(); ?>admin/payment/create',
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
   
    $(document).ready(function (e) {
        $("#add_medication").on('submit', (function (e) {
            e.preventDefault();
            $("#add_medicationbtn").btnLoading();
            $.ajax({
                url: '<?php echo base_url(); ?>admin/patient/addmedicationdose',
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

    $(document).ready(function (e) {
        $("#add_medicationdose").on('submit', (function (e) {
            e.preventDefault();
            $("#add_medicationdosebtn").btnLoading();
            $.ajax({
                url: '<?php echo base_url(); ?>admin/patient/addmedicationdose',
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

    $(document).ready(function (e) {
        $("#formedit").on('submit', (function (e) {
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
                }, error: function () {}
            });
        }));
    });
   
    function getBed(bed_group, bed = '', active, htmlid = null) {

        if(htmlid!=null){
            htmlid = htmlid ;
        }else{
            htmlid = 'bed_nos';
        }

        var div_data = "";
        $('#' + htmlid).html("<option value='l'><?php echo $this->lang->line('loading') ?></option>");
        $("#" + htmlid).select2("val", 'l');
        $.ajax({
            url: '<?php echo base_url(); ?>admin/setup/bed/getbedbybedgroup',
            type: "POST",
            data: {bed_group: bed_group, bed_id: bed, active: active},
            dataType: 'json',
            success: function (res) {
                $.each(res, function (i, obj)
                {
                    var sel = "";
                    if (bed == obj.id) {
                        sel = "selected";
                    }
                    div_data += "<option " + sel + " value=" + obj.id + ">" + obj.name + "</option>";
                });
                $("#" + htmlid).html("<option value=''><?php echo $this->lang->line('select') ?></option>");
                $('#' + htmlid).append(div_data);
                $("#" + htmlid).select2().select2('val', bed);
            }
        });
    }

    $(document).ready(function (e) {
     $("form#form_prescription button[type=submit]").click(function() {            
        $("button[type=submit]", $(this).parents("form")).removeAttr("clicked");
        $(this).attr("clicked", "true");
    });

        $("#form_prescription").on('submit', (function (e) {
            e.preventDefault();
            // Sync CKEditor content into the underlying textareas before building FormData.
            // CKEditor only auto-syncs on a native submit; this AJAX path uses preventDefault()
            // + FormData, so header_note/footer_note would otherwise post stale/empty values.
            if (window.CKEDITOR && CKEDITOR.instances) {
                for (var inst in CKEDITOR.instances) { CKEDITOR.instances[inst].updateElement(); }
            }
            var sub_btn_clicked = $("button[type=submit][clicked=true]");
            var sub_btn_clicked_name=sub_btn_clicked.attr('name');
            var clicked_btn = $("button[type=submit]");
            var btn = clicked_btn;
            $.ajax({
                url: base_url+'admin/patient/add_ipdprescription',
                type: "POST",
                data: new FormData(this),
                dataType: 'json',
                contentType: false,
                cache: false,
                processData: false,
                beforeSend: function(){
                 sub_btn_clicked.btnLoading();
                }, 
                success: function (data) {
                sub_btn_clicked.btnReset();
                if (data.status == 0) {
                    var message = "";
                    $.each(data.error, function (index, value) {
                        message += value;
                    });
                    errorMsg(message);
                } else {
                    successMsg(data.message);                   
                    if(sub_btn_clicked_name === "save_print") {                            
                      printprescription(data.ipd_prescription_basic_id,true);
                    }else if (sub_btn_clicked_name === "save") {                        
                    window.location.reload(true);
                    }    

                }
                }, 
                error: function () {
                sub_btn_clicked.btnReset();
                },
  
                complete: function(){
                sub_btn_clicked.btnReset();
                }
            });
        }));
    });

    $(document).ready(function (e) {
        $("#form_diagnosis").on('submit', (function (e) {
            e.preventDefault();
            $("#form_diagnosisbtn").btnLoading();
            $.ajax({
                url: '<?php echo base_url(); ?>admin/patient/add_diagnosis',
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
                    $("#form_diagnosisbtn").btnReset();
                },
                error: function () {}
            });
        }));
    });

    $(document).ready(function (e) {
        $("#form_doctor").on('submit', (function (e) {
            e.preventDefault();
            $("#form_doctorbtn").btnLoading();
            $.ajax({
                url: '<?php echo base_url(); ?>admin/patient/addipddoctor',
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
                    $("#form_doctorbtn").btnReset();
                },
                error: function () {}
            });
        }));
    });

    $(document).ready(function (e) {
        $("#form_editdiagnosis").on('submit', (function (e) {
            e.preventDefault();
            $("#form_editdiagnosisbtn").btnLoading();
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
                    $("#form_diagnosisbtn").btnReset();
                },
                error: function () {}
            });
        }));
    });

    $(document).on('select2:select','.medicine_category',function(){      
      getMedicine($(this),$(this).val(),0);
       selected_medicine_category_id =$(this).val();   
       var medicine_dosage=getDosages(selected_medicine_category_id);
       $(this).closest('tr').find('.medicine_dosage').html(medicine_dosage);
    });

    $(document).on('select2:select','.medicine_category_medication',function(){
       var medicine_category=$(this).val();      
      $('.medicine_name_medication').html("<option value=''><?php echo $this->lang->line('loading') ?></option>");
     getMedicineForMedication(medicine_category,"");
     getMedicineDosageForMedication(medicine_category);
    });

    function getMedicineForMedication(medicine_category,medicine_id) {
      var div_data = "<option value=''><?= $this->lang->line('select') ?></option>";
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
            }
        });
      }
    }

    function getMedicineDosageForMedication(medicine_category) {
        var div_data = "<option value=''><?= $this->lang->line('select') ?></option>";
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
                var div_data="<option value=''><?= $this->lang->line('select') ?></option>";
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

    function getMedicineDosage(id) {       
        var category_selected = $("#medicine_cat" + id).val();
        var arr = category_selected.split('-');
        var category_set = arr[0];
        div_data = '';
        $("#search-dosage" + id).html("<option value='l'><?php echo $this->lang->line('loading') ?></option>");
        $('#search-dosage' + id).select2("val", +id);
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
                $("#search-dosage" + id).html("<option value=''><?= $this->lang->line('select') ?></option>");
                $('#search-dosage' + id).append(div_data);
                $('#search-dosage' + id).select2("val", '');
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
                $("#ereportcenter").val(data.report_center);
                $("#ereportdate").val(data.report_date);
                $("#edescription").val(data.description);
                shModal('edit_diagnosis').show();
            },
        });
    }
    
    var prescription_rows=0;
    $(document).on('click','.add-record',function(){
         var rowCount = $('#tableID tr').length;
            if(rowCount==0){
                prescription_rows=1;;
            }else{
                prescription_rows=rowCount+1;
            }
    
        var row = "<tr id='row" + prescription_rows + "'>" +
            "<td><input type='hidden' name='rows[]' value='"+prescription_rows+"' autocomplete='off'><input type='hidden' name='medicine_cat_"+prescription_rows+"' value='1'><select class='form-control select2 medicine_name' data-rowId='"+prescription_rows+"' style='width:100%' name='medicine_"+prescription_rows+"' id='search-query"+prescription_rows+"'><option value=''><?php echo $this->lang->line('select'); ?></option><?php foreach ($medicineName as $mkey => $mvalue) { ?><option value='<?php echo $mvalue["id"]; ?>'><?php echo addslashes($mvalue["medicine_name"]); ?></option><?php } ?></select><small id='stock_info_"+prescription_rows+"' class='text-muted'></small></td>" +
            "<td><select class='form-control select2 medicine_dosage' style='width:100%' name='dosage_"+prescription_rows+"' id='search-dosage"+prescription_rows+"'><option value=''><?php echo $this->lang->line('select'); ?></option><?php foreach ($dosage as $dkey => $dvalue) { ?><option value='<?php echo $dvalue["id"]; ?>'><?php echo addslashes($dvalue["dosage"] . ' (' . $dvalue["unit"] . ')'); ?></option><?php } ?></select></td>" +
            "<td><select class='form-control select2 interval_dosage' style='width:100%' name='interval_dosage_"+prescription_rows+"' id='search-interval-dosage"+prescription_rows+"'><option value=''><?php echo $this->lang->line('select'); ?></option><?php foreach ($intervaldosage as $dkey => $dvalue) { ?><option value='<?php echo $dvalue["id"]; ?>'><?php echo $dvalue["name"] ?></option><?php } ?></select></td>" +
            "<td><select class='form-control select2 duration_dosage' style='width:100%' name='duration_dosage_"+prescription_rows+"' id='search-duration-dosage"+prescription_rows+"'><option value=''><?php echo $this->lang->line('select'); ?></option><?php foreach ($durationdosage as $dkey => $dvalue) { ?><option value='<?php echo $dvalue["id"]; ?>'><?php echo $dvalue["name"] ?></option><?php } ?></select></td>" +
            "<td><textarea rows='1' style='height:32px' name='instruction_"+prescription_rows+"' class='form-control'></textarea></td>" +
            "<td class='text-center align-middle'><button type='button' data-row-id='"+prescription_rows+"' class='btn btn-sm btn-outline-danger closebtn delete_row_prescription' title='<?php echo $this->lang->line('delete'); ?>'><i class='fa fa-remove'></i></button></td>" +
            "</tr>";
      $('#tableID').append(row).find('.select2').select2();
    });  
  
  $(document).on('click','.delete_row_prescription',function(e){       
        var del_row_id=$(this).data('rowId');        
        var result = confirm("<?php echo $this->lang->line('delete_confirm')?>");
        if (result) {
            $("#row" + del_row_id).html("");
        }
  });

    $(document).ready(function (e) {
        $("#add_timeline").on('submit', (function (e) {
            var patient_id = $("#patient_id").val();
            e.preventDefault();
            $("#add_timelinebtn").btnLoading();
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
        $("#add_bill").on('submit', (function (e) {
            if (confirm('<?php echo $this->lang->line('confirmed')?>')) {
                $("#save_button").btnLoading();
                e.preventDefault();
                $.ajax({
                    url: "<?php echo site_url("admin/payment/addbill") ?>",
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
                            window.location.href = '<?php echo base_url(); ?>admin/patient/discharged_patients';
                        }
                        $("#save_button").btnReset();
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
                             toastr.success(
                            '<?php echo $this->lang->line('delete_message'); ?>',
                            '',
                            {
                              timeOut: 1000,
                              fadeOut: 1000,
                              onHidden: function () {
                                  window.location.reload(true);
                                }
                            }
                          );
                          
                        },
                        error: function () {
                            alert("Fail")
                        }
                    });
                }, error: function () {
                    alert("Fail")
                }
            });
        }
    }
	
    $(document).ready(function (e) {
        $(function () {
            var hash = window.location.hash;
            if (hash) {
                var tabEl = document.querySelector('ul.nav-tabs a[href="' + hash + '"]');
                if (tabEl) { bootstrap.Tab.getOrCreateInstance(tabEl).show(); }
            }
            $('.nav-tabs a').click(function (e) {
                bootstrap.Tab.getOrCreateInstance(this).show();
                var scrollmem = $('body').scrollTop();
                window.location.hash = this.hash;
                $('html,body').scrollTop(scrollmem);
            });
        });
    });

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

                } else
                {
                    $("#evisible_check").attr('checked', true);
                }
                shModal('myTimelineEditModal').show();
            },
        });
    }

    function editNursenote(id) {      
        $.ajax({
            url: '<?php echo base_url(); ?>admin/patient/editNursenote',
            type: "POST",
            data: {id: id},
            dataType: 'json',
            success: function (data) {
                 $("#nurse_id").val(data.nid);
                 $("#endate").val(data.note_date);
                 $("#enote").val(data.note);
                 $("#ecomment").val(data.comment);
                 $('select[id="edit_nurse"] option[value="' + data.staff_id + '"]').attr("selected", "selected");
                 $("#edit_nurse").select2().select2('val', data.staff_id);
                 $('#customfieldnurse').html(data.custom_fields_value);
                shModal('nursenoteEditModal').show();
            },
        });
    }

    function editConsultantRegister(id) {      
        $.ajax({
            url: '<?php echo base_url(); ?>admin/patient/editConsultantRegister',
            type: "POST",
            data: {id: id},
            dataType: 'json',
            success: function (data) {
                  $("#instruction_id").val(data.id);                  
                  $("#ecdate").val(data.date);
                  $("#ecinsdate").val(data.ins_date);
                  $("#ecinstruction").val(data.instruction);
                  $('select[id="editdoctor_field"] option[value="' + data.cons_doctor + '"]').attr("selected", "selected");
                  $("#editdoctor_field").select2().select2('val', data.cons_doctor);
                  $('#customfieldconsult').html(data.custom_fields_value);
                shModal('edit_instruction').show();
            },
        });
    }

    function addcommentNursenote(id,ipdid) {
        $("#nurse_noteid").val(id);
        shModal('nursenoteCommentModal').show();
    }

    $(document).ready(function (e) {
        $("#form_operationtheatre").on('submit', (function (e) {
             var did = $("#consultant_doctorid").val();
            $("#consultant_doctorname").val(did);
            $("#form_operationtheatrebtn").btnLoading();
            e.preventDefault();
            $.ajax({
                url: '<?php echo base_url(); ?>admin/operationtheatre/addipdot',
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

	function editot(id) {
        var date_format = '<?php echo $result = strtr($this->customlib->getHospitalDateFormat(), ['d' => 'dd', 'm' => 'mm', 'Y' => 'yyyy',]) ?>';
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
                $("#edate").val(data.otdate);
                $("#eass_consultant_1").val(data.ass_consultant_1);
                $("#eass_consultant_2").val(data.ass_consultant_2);
                $("#eanesthetist").val(data.anesthetist);
                $("#eanaethesia_type").val(data.anaethesia_type);
                $("#eot_technician").val(data.ot_technician);
                $("#eot_assistant").val(data.ot_assistant);
                $("#custom_field_ot").html(data.custom_fields_value);
                $("#eot_remark").val(data.remark);
                $("#eot_result").val(data.result);
                $("#edit_operationtheatre #econsultant_doctorid").select2().select2('val', data.consultant_doctor);
                $("#edit_operationtheatre #eoperation_name").select2().select2('val', data.operation_id);
                shModal('edit_operationtheatre').show();

            },
        });
    }

    $(document).ready(function (e) {
        $("#form_editoperationtheatre").on('submit', (function (e) {
            $("#form_editoperationtheatrebtn").btnLoading();
            var cons = $("#cons_doctor").val();
            $("#cons_name").val(cons);
            e.preventDefault();
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

     $(document).ready(function (e) {
        $("#edit_nursenote").on('submit', (function (e) {
            $("#edit_nursenotebtn").btnLoading();
            e.preventDefault();
            $.ajax({
                url: "<?php echo site_url("admin/patient/updatenursenote") ?>",
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
                    $("#edit_nursenotebtn").btnReset();
                },
                error: function (e) {
                    alert("Fail");
                    console.log(e);
                }
            });
        }));
    });

      $(document).ready(function (e) {
        $("#comment_nursenote").on('submit', (function (e) {
            $("#comment_nursenotebtn").btnLoading(); 
            e.preventDefault();
            $.ajax({
                url: "<?php echo site_url("admin/patient/addnursenotecomment") ?>",
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
                    $("#comment_nursenotebtn").btnReset();
                },
                error: function (e) {
                    alert("Fail");
                    console.log(e);
                }
            });
        }));
    });

       function edit_prescription(id) {
        $('#edit_prescription_title').html('<?php echo $this->lang->line('edit_prescription'); ?>');
        $.ajax({
            url: base_url+'admin/prescription/editipdPrescription',
            dataType:'JSON',
            data:{'prescription_id':id} , 
            type:"POST",
             beforeSend: function() {
                  
              },
               success: function (res) {
                ['compose-textareanew', 'compose-textareas'].forEach(function(eid) {
                    if (window.CKEDITOR && CKEDITOR.instances[eid]) { CKEDITOR.instances[eid].destroy(true); }
                });
                shModal('prescriptionview').hide();
                $('.modal-body',"#add_prescription").html(res.page);
                $('.filestyle','#add_prescription').dropify();

                var medicineTable= $('.modal-body',"#add_prescription").find('table#tableID');
                $('.select2').select2();
                medicineTable.find('.select2').select2();
                $('.modal-body',"#add_prescription").find('.multiselect2').select2({   
                    placeholder: 'Select',
                    allowClear: false,
                    minimumResultsForSearch: 2
                });
                prescription_rows=medicineTable.find('tr').length+1;

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

    function view_prescription(id, ipdid, discharged='') {
        $.ajax({
            url: base_url+'admin/prescription/getIPDPrescription/',  
            dataType:'JSON',
            data:{'prescription_id':id} ,
            type:"POST",
             beforeSend: function() {
      
          },
          success: function(res) {
            $("#getdetails_prescription").html(res.page);
          },
             error: function(xhr) { // if error occured
          alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");              
         },
              complete: function() {

             }
        });

        if(discharged != "yes"){
         $('#edit_deleteprescription').html("<?php if ($this->rbac->hasPrivilege('ipd_prescription', 'can_view')) { ?><a href='#prescription' onclick='printprescription(" + id + ")' class='btn btn-sm btn-light' data-bs-toggle='tooltip' title='<?php echo $this->lang->line('print'); ?>'><i class='fa fa-print'></i></a><?php } ?><?php if ($this->rbac->hasPrivilege('ipd_prescription', 'can_edit')) { ?><a href='#prescription' onclick='edit_prescription(" + id + ")' data-bs-target='#edit_prescription' class='btn btn-sm btn-light' data-bs-toggle='tooltip' title='<?php echo $this->lang->line('edit'); ?>'><i class='fa fa-pencil'></i></a><?php }  if ($this->rbac->hasPrivilege('ipd_prescription', 'can_delete')) { ?><a href='javascript:void(0)' onclick='delete_prescription(" + id + ")' class='btn btn-sm btn-light' data-bs-toggle='tooltip' title='<?php echo $this->lang->line('delete'); ?>'><i class='fa fa-trash'></i></a><?php } ?>");
        }else{
        $('#edit_deleteprescription').html("<?php if ($this->rbac->hasPrivilege('ipd_prescription', 'can_view')) { ?><a href='#prescription' onclick='printprescription(" + id + ")' class='btn btn-sm btn-light' data-bs-toggle='tooltip' title='<?php echo $this->lang->line('print'); ?>'><i class='fa fa-print'></i></a><?php } ?>");
        }

        shModal('prescriptionview').show();
    }

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
                $('#editcharge_category').select2("val", charge_category);
            }
        });
         }
    }

	$(document).on('select2:select','.charge_category',function(){
		var charge_category=$(this).val();      
		$('.charge').html("<option value=''><?php echo $this->lang->line('loading'); ?></option>");
		getchargecode(charge_category,"");
	});

   $(document).on('select2:select','.medicine_name',function(){ 
    var row_id_val= $(this).data('rowid');
    var  medid=$(this).val();
    if(medid!=""){
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
	
	function delete_prescription(prescription_id) {        
        if (confirm('<?php echo $this->lang->line("are_you_sure_you_want_to_delete_this"); ?>')) {
            $.ajax({
            url: '<?php echo base_url(); ?>admin/prescription/deleteopdPrescription/'+prescription_id,
                success: function (res) {
                    window.location.reload(true);
                },
                error: function () {
                    alert("Fail")
                }
            });
        }
    }

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
                 $("#editcharge_id").select2("val", charge_id);             
            }
        });
      }
    }

    $(document).on('click','.print_charge',function(){   

        var $this = $(this);
        var record_id=$this.data('recordId')
        $this.btnLoading();
        $.ajax({
          url: '<?php echo base_url(); ?>admin/patient/printCharge',
          type: "POST",
          data:{'id':record_id,'type':'ipd'},
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

	$(document).on('select2:select','.charge_type',function(){
		let closetst_div=$(this).closest('div.modal');
		// reset_form(closetst_div);    
        var charge_type=$(this).val();     
         closetst_div.find('#qty').val(1);
        $('.charge_category').html("<option value=''><?php echo $this->lang->line('loading') ?></option>");
		getcharge_category(charge_type,"");
	});

	$(document).on('select2:select','.charge_category',function(){
		let closetst_div=$(this).closest('div.modal');
		// reset_form(closetst_div);
	});

	reset_form=(_modal_div)=>{
                    _modal_div.find('#qty').val(1);
                   $(".charge",_modal_div).select2('destroy').val("").select2().find('option:not(:first)').remove();
                    _modal_div.find('#apply_charge').val("");
                    _modal_div.find('.standard_charge').val("");
                    _modal_div.find('.schedule_charge').val("");
                    _modal_div.find('.charge_tax').val(0);                  
                    _modal_div.find('.total').val(0);                
                    _modal_div.find('.tax').val(0);
                    _modal_div.find('.final_amount').val(0);
                    _modal_div.find('.discount_percentage_amount').val(0);
                    _modal_div.find('.discount_percentage_add_charge').val(0);
                    _modal_div.find('.net_amount').val(0);
	}

	$(document).on('select2:select','.charge',function(){

        var charge=$(this).val();  
        let closetst_div        =     $(this).closest('div.modal');     
        var charge              =     $(this).val();
        closetst_div.find('#qty').val(1);

        var orgid               =     $('#edit_organisation_id').val();  
        var patient_id          =     closetst_div.find("#patient_id").val();   
        let is_tpa              =     closetst_div.find("input:checkbox[name=is_tpa]").prop('checked') ? 1 : 0;   
        let organization_charge =     "";
        $.ajax({
            url: '<?php echo base_url(); ?>admin/patient/getChargeById',
            type: "POST",
            data: {charge_id: charge, organisation_id: orgid,is_tpa:is_tpa,patient_id:patient_id},
            dataType: 'json',
            success: function (res) {
                if(res.status == 0){
                    errorMsg(res.msg);
                }else{
                    if(res.status == 2){
                        errorMsg(res.msg);
                    }

                    $('.total',closetst_div).val(parseFloat(res.result.standard_charge) * quantity);
                    $('.standard_charge',closetst_div).val(res.result.standard_charge);
                    $('.schedule_charge',closetst_div).val(res.result.org_charge);
                    $('.charge_tax',closetst_div).val(res.result.percentage);
                    var discount_percent = 0;
                    var quantity=$('.qty',closetst_div).val();                   
                    if(res.display_tpa_charge==false){
                        var total_charge       =      res.result.standard_charge;
                        $('.total_charge',closetst_div).val(res.result.standard_charge);//added
                    }else if(res.display_tpa_charge==true){
                        var total_charge       =      res.result.org_charge;
                        $('.total_charge',closetst_div).val(res.result.org_charge);//added
                    }
                    var apply_charge          =      isNaN(parseFloat(total_charge)*parseFloat(quantity)) ? 0 : parseFloat(total_charge)*parseFloat(quantity);                  
                    var discount_amount       =      (apply_charge*discount_percent)/100;
                    $('.total',closetst_div).val(apply_charge);
                    var final_amount=apply_charge-discount_amount;

                    $('.discount_percentage_add_charge').val((discount_percent).toFixed(2));//added
                    $('.discount_percentage_amount').val((discount_amount).toFixed(2));//added

                    $('.tax',closetst_div).val(((parseFloat(final_amount)*res.result.percentage)/100).toFixed(2));
                    $('.net_amount',closetst_div).val((parseFloat(final_amount)+parseFloat((final_amount*res.result.percentage)/100)).toFixed(2));            
                }
            }
        });
    });
 
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
          $('.qty').val(1);     
          $('#preview_charges').html('');                   
          $('.total_charge').val('0');                   
    }

    let update_edit_charge_amount=(object_model)=>{
        let is_tpa= $("input:checkbox[name=is_tpa]").prop('checked') ? 1 : 0;//added
        let quantity=  object_model.find('#editqty').val();
        let standard_charge=  object_model.find('#editstandard_charge').val();
        let schedule_charge= object_model.find('#editschedule_charge').val();      
        let tax_percent=object_model.find('#editcharge_tax').val();
        let total_charge= object_model.find('#edit_total_charge').val();  //addedd  
        let apply_charge=isNaN(parseFloat(total_charge)*parseFloat(quantity)) ? 0 : parseFloat(total_charge)*parseFloat(quantity); 
        let discount_percentage=object_model.find('#editdiscount_percentage').val();
        let discount_amount=(parseFloat(apply_charge) * discount_percentage/100);
        $('#editapply_charge').val(apply_charge.toFixed(2));       
        $('#editdiscount').val(discount_amount.toFixed(2));       
        let discount_percent= 0;
        let final_amount=apply_charge-discount_amount; 
        $('#edittax').val(((final_amount*tax_percent)/100).toFixed(2));
        $('#editfinal_amount').val((final_amount+((final_amount*tax_percent)/100)).toFixed(2));
    }

    
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
         $("#grass_amount").val(net_amount.toFixed(2));
         $("#grass_amount_span").html(net_amount.toFixed(2));
         $("#net_amount").val(net_amount_payble.toFixed(2));
         $("#net_amount_span").html(net_amount_payble.toFixed(2));
         $("#net_amount_payble").val(net_amount_payble.toFixed(2));
         $("#save_button").show();
         $("#printBill").show();
    }

        $(document).on('input paste keyup','.apply_charge_add_charge,.discount_percentage_add_charge,.qty', function(e){ 
        update_charge_amount($(e.target).closest('div.modal'));
        });
        
        function update_charge_amount(object_model){ 

            let quantity= object_model.find('.qty').val();
            let is_tpa = object_model.find("input:checkbox[name=is_tpa]").prop('checked') ? 1 : 0;   
            let tax_percent= object_model.find('.charge_tax').val();       
            let total_charge=object_model.find('.total_charge').val(); 
            let apply_charge=isNaN(parseFloat(total_charge)*parseFloat(quantity))?0 : parseFloat(total_charge)*parseFloat(quantity); 
            object_model.find('.total').val(apply_charge.toFixed(2));        
            let  discount_percentage=object_model.find('.discount_percentage_add_charge').val();
            let discount_amount=isNaN((parseFloat(apply_charge) * discount_percentage/100)) ? 0 : (parseFloat(apply_charge) * discount_percentage/100);
            let tax_amount=((apply_charge-discount_amount)*tax_percent)/100;
            let final_amount=(apply_charge-discount_amount)+tax_amount;
            object_model.find('.discount_percentage_amount').val((discount_amount).toFixed(2));
            object_model.find('.tax').val((tax_amount).toFixed(2));
            object_model.find('.net_amount').val((final_amount).toFixed(2));
        }

    //calculate discount amount to discount persantage
    function get_percentage(discount_amount){
        var discount_amount=(discount_amount != "") ? discount_amount: 0;
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

    function revert(patient_id, billid, bedid, ipdid) {

        $.ajax({
            url: '<?php echo base_url(); ?>admin/patient/revertBill',
            type: "POST",
            data: {patient_id: patient_id, bill_id: billid, bed_id: bedid, ipdid: ipdid},
            dataType: 'json',
            success: function (res) {
                if (res.status == "fail") {
                    var message = "";
                    errorMsg(res.message);
                } else {
                    successMsg(res.message);
                    window.location.href = '<?php echo base_url() ?>admin/patient/ipdsearch';
                }
            }
        });
    }     

    function checkbed(patient_id, billid, bedid,ipdid) {
        var v = 'false';
        if (confirm('<?php echo $this->lang->line('are_you_sure_you_want_to_delete_this')?>')) {
            $.ajax({
                url: '<?php echo base_url(); ?>admin/setup/bed/checkbed',
                type: "POST",
                data: {bed_id: bedid},
                dataType: 'json',
                success: function (res) {
                    if (res.status == "fail") {
                        shModal("alot_bed").show();
                    } else {
                        revert(patient_id, billid, bedid,ipdid)
                    }
                }
            });
        }
    }

    $(document).ready(function (e) {
        $("#consultant_register_form").on('submit', (function (e) {
            var doctor_id = $("#doctor_field").val();
            $("#doctor_set").val(doctor_id);
            $("#consultant_registerbtn").btnLoading();
            e.preventDefault();
            $.ajax({
                url: '<?php echo base_url(); ?>admin/patient/add_consultant_instruction',
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
                    $("#consultant_registerbtn").btnReset();
                },
                error: function () {
                   
                }
            });
        }));
    });

    $(document).ready(function (e) {
        $("#editconsultant_register_form").on('submit', (function (e) {
            var doctor_id = $("#editdoctor_field").val();
            $("#editdoctor_set").val(doctor_id);
            $("#editconsultant_registerbtn").btnLoading();
            e.preventDefault();
            $.ajax({
                url: '<?php echo base_url(); ?>admin/patient/update_consultant_instruction',
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
                    $("#editconsultant_registerbtn").btnReset();
                },
                error: function () {
                   
                }
            });
        }));
    });

    $(document).ready(function (e) {
            $("#nurse_note_form").on('submit', (function (e) {
            var nurse_id = $("#nurse_field").val();
            $("#nurse_set").val(nurse_id);
            $("#nurse_notebtn").btnLoading();
            e.preventDefault();
            $.ajax({
                url: '<?php echo base_url(); ?>admin/patient/add_nurse_note',
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
                    $("#nurse_notebtn").btnReset();
                },
                error: function () {
                   
                }
            });
        }));
    });

    function delete_consultant_row(id) {
        var table = document.getElementById("constableID");
        var rowCount = table.rows.length;
        $("#row" + id).html("");
    }
</script>
<script type="text/javascript">

    function deleteIpdPatient(ipdid) {
        if (confirm('<?php echo $this->lang->line('delete_confirm') ?>')) {
            $.ajax({
                url: base_url + 'admin/patient/deleteIpdPatient/',
                type: 'POST',
                data: {ipdid: ipdid},
                dataType:"JSON",
                success: function (data) {
                    if (data.status == "fail") {
                        var message = "";
                        $.each(data.error, function (index, value) {
                            message += value;
                        });
                        errorMsg(message);
                    } else {
                        successMsg(data.message);
                        window.location.href = '<?php echo base_url() . "admin/patient/ipdsearch" ?>';
                    }
                }
            });
        }
    }
	
    function printBill(patientid, ipdid) {
        var total_amount = $("#total_amount").val();
        var discount = $("#discount").val();
        var other_charge = $("#other_charge").val();
        var gross_total = $("#gross_total").val();
        var tax = $("#tax").val();
        var net_amount = $("#net_amount").val();
        var status = $("#status").val();
        var base_url = '<?php echo base_url() ?>';
        $.ajax({
            url: base_url + 'admin/payment/getBill/',
            type: 'POST',
            data: {patient_id: patientid, ipdid: ipdid, total_amount: total_amount, discount: discount, other_charge: other_charge, gross_total: gross_total, tax: tax, net_amount: net_amount, status: status},
            success: function (result) {
                $("#testdata").html(result);
                popup(result);
            }
        });
    }
    
    function delete_record(url, Msg) {
        if (confirm(<?php echo "'" . $this->lang->line('delete_confirm') . "'"; ?>)) {
            $.ajax({
                url: url,
                success: function (res) {
                    successMsg(Msg);
                    window.location.reload(true);
                }
            })
        }
    }
   
    $(document).on('click','.delete_record_dosage',function(){
        if (confirm(<?php echo "'" . $this->lang->line('delete_confirm') . "'"; ?>)) {
            var id=$(this).data('recordId');
            $.ajax({
                url: '<?php echo base_url(); ?>admin/patient/deletemedicationdosage',
                type: "POST",
                data: {medication_id: id},
                dataType: 'json',
                success: function (data) {
                    successMsg(<?php echo "'" . $this->lang->line('delete_message') . "'"; ?>);
                    window.location.reload(true);
                }
            })
        }
    });

    function printprescription(id,print_status=false) {
        var base_url = '<?php echo base_url() ?>';
        $.ajax({
            url: base_url + 'admin/prescription/printIPDPrescription/' + id ,
            type: 'POST',
            data: {prescription_id: id, print: 'yes'},
            dataType: "json",
            success: function (result) {
                $("#testdata").html(result.page);
                popup(result.page,print_status);
            }
        });
    }

    $(function () {
        if ($.fn.wysihtml5) {
            $("#compose-textareas,#compose-textareanew").wysihtml5({
                toolbar: {
                    "image": false,
                }
            });
        }
    });
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

   $('#myaddMedicationModal').on('hidden.bs.modal', function () {
    $('#add_medication').find('input:text, input:password, input:file, textarea').val('');
    $('#add_medication').find('select option:selected').removeAttr('selected');
    $('#add_medication').find('input:checkbox, input:radio').removeAttr('checked');
    $('.medicine_category_medication').select2("val", "");;
    $('.medicine_name_medication').select2("val", "");;
    $('.dosage_medication').select2("val", "");;
     $('#mtime').val('12:00 PM');
   });

$(".addnursenote").click(function(){       
    $('#nurse_note_form').trigger("reset");
});

$(".adddiagnosis").click(function(){        
    $('#form_diagnosis').trigger("reset");   
    $('#add_diagnosis .filestyle').dropify();
});

$(".addtimeline").click(function(){     
    $('#add_timeline').trigger("reset");
    $(".dropify-clear").trigger("click");
});

$(".addpayment").click(function(){      
    $('#add_payment').trigger("reset");
    $(".dropify-clear").trigger("click");
});

$(".addcharges").click(function(){      
    $('#add_charges').trigger("reset");
    $('#select2-charge_category-container').html("");
    $('#select2-code-container').html("");
});

  $(document).on('click','.addprescription',function(){
     $.ajax({
            url: base_url+'admin/prescription/addipdPrescription',
            dataType:'JSON',
            data:{'ipd_id':ipd_id},
            type:"POST",
             beforeSend: function() {
              },
               success: function (res) {
                ['compose-textareanewadd', 'compose-textareasadd'].forEach(function(eid) {
                    if (window.CKEDITOR && CKEDITOR.instances[eid]) { CKEDITOR.instances[eid].destroy(true); }
                });
                $('#edit_prescription_title').html('<?php echo $this->lang->line('add_prescription'); ?>');
                $('.modal-body',"#add_prescription").html(res.page);
                $('.modal-body',"#add_prescription").find('table').find('.select2').select2();
                $('.select2').select2();
                $('.modal-body',"#add_prescription").find('.multiselect2').select2({
                    placeholder: 'Select',
                    allowClear: false,
                    minimumResultsForSearch: 2
                });
                $('.filestyle','#add_prescription').dropify();
                shModal('add_prescription').show();
             },
              complete: function() {
                if (window.CKEDITOR) {
                    CKEDITOR.replace('compose-textareanewadd', { allowedContent: true });
                    CKEDITOR.replace('compose-textareasadd', { allowedContent: true });
                }
             },
             error: function(xhr) { // if error occured
              alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");
         }
        });
  });

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

    $(document).on('change','.payment_mode',function(){
      var mode=$(this).val();
      if(mode == "Cheque"){
        $('.cheque_div').css("display", "block");
      }else{
        $('.cheque_div').css("display", "none");
      }
    });
</script>

<script type="text/javascript">
  $(document).on('click','.edit_charge',function(){
        var edit_charge_id=$(this).data('recordId');
       var createModal=$('#myChargeseditModal');
       var $this = $(this);
       $this.btnLoading();
       shModal('myChargeseditModal').show();
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
                    $('#editschedule_charge').val(res.result.tpa_charge);
                }
                $('#editdiscount_percentage').val(res.result.discount_percentage);
                $('#patient_charge_id').val(res.result.id);
                $('#editqty').val(res.result.qty);
                $('#editapply_charge').val(res.result.apply_charge);
                $('#editfinal_amount').val(res.result.amount); 
                $('#editcharge_date').val(res.result.date);       
                var discount_amount=(res.result.discount_amount);
                $('#editdiscount').val(discount_amount);
                $('#editcharge_tax').val(res.result.percentage);
                var tax_charge=((res.result.apply_charge-discount_amount)*res.result.percentage)/100;
                $('#edittax').val(tax_charge.toFixed(2));
                $('#editpatient_charge_id').val(res.result.id);
                $('textarea#enote').val(res.result.note);          
                $('#edit_charge_type').select2('val',res.result.charge_type_master_id);

                if(res.result.organisation_id==null  || res.result.organisation_id==''){
                    $('#myChargeseditModal').find('#is_tpa').prop("checked",false);
                    $('#edit_total_charge').val(res.result.standard_charge);//added
                }else{
                    $('#myChargeseditModal').find('#is_tpa').prop("checked",true);
                    $('#edit_total_charge').val(res.result.tpa_charge);//added
                }
                getcharge_category(res.result.charge_type_master_id,res.result.charge_category_id);
                getchargecode(res.result.charge_category_id,res.result.charge_id);
          },
             error: function(xhr) { // if error occured
          alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");
             $this.btnReset();               
      },
      complete: function() {
            $this.btnReset(); 
            update_edit_charge_amount($("#myChargeseditModal").closest('div.modal'));//added    
      }
      });
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
                url: '<?php echo base_url(); ?>admin/charges/add_ipdcharges',
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
                        var charge='<tr id="'+row_id+'"><td>'+data.date+'<input type="hidden" name="pre_date[]" value="'+data.date+'"></td><td>'+data.charge_type_name+'</td><td>'+data.charge_category+'</td><td>'+data.charge_name+'<input type="hidden" name="pre_tax_percentage[]" value="'+data.tax_percentage+'"><input type="hidden" name="pre_charge_id[]" value="'+data.charge_id+'"><br><h6>'+data.note+'<input type="hidden" name="pre_note[]" value="'+data.note+'"></h6></td><td>'+data.qty+'<input type="hidden" name="pre_qty[]" value="'+data.qty+'"></td><td class="text-end">'+data.standard_charge+'<input type="hidden" name="pre_standard_charge[]" value="'+data.standard_charge+'"></td><td class="text-end">'+data.tpa_charge+'<input type="hidden" name="pre_tpa_charges[]" value="'+data.tpa_charge+'"></td><td class="text-end">'+data.amount+'<input type="hidden" name="pre_total[]" value="'+data.amount+'"></td><td class="text-end">'+data.discount_percentage_amount+' ('+data.discount_percentage+'%)<input type="hidden" name="pre_discount_percentage[]" value="'+data.discount_percentage+'"></td><td class="text-end">'+data.tax+' ('+data.tax_percentage+'%)<input type="hidden" name="pre_tax[]" value="'+data.tax+'"><input type="hidden" name="pre_apply_charge[]" value="'+data.apply_charge+'"></td><td class="text-end">'+data.net_amount+'<input type="hidden" name="pre_net_amount[]" value="'+data.net_amount+'"></td><td class="text-center"><button type="button" class="btn btn-sm btn-outline-danger closebtn delete_row" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('delete'); ?>" data-row-id="'+row_id+'" autocomplete="off"><i class="fa fa-remove"></i></button></td></tr>';
                        $('#preview_charges').append(charge);                        
                        charge_reset();
                        

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
        var del_row_id=$(this).data('rowId');
        var del_record_id=$(this).data('recordId');
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
        reset_form($('#myChargesModal'));
        $("#charge_category",$('#myChargesModal')).select2('destroy').val('').select2().empty();
        $("#add_charge_type",$('#myChargesModal')).select2('destroy').val('').select2();
        $("#charge_id",$('#myChargesModal')).select2('destroy').val('').select2().empty();
        $(".datetime",$('#myChargesModal')).val('');
        $("#edit_note",$('#myChargesModal')).val('');
    }
</script>

<script type="text/javascript">

   function getDosages(medicine_category_id,selected_dosage=""){
   
    var dosage_opt="<option value=''><?php echo $this->lang->line('select') ?></option>";
   var category_dosage_array=<?php echo json_encode($category_dosage ?: new stdClass()); ?>;
  
   if (category_dosage_array[medicine_category_id]){
    $.each(category_dosage_array[medicine_category_id], function(key, item) 
    {
         var sel = "";
        if (selected_dosage == item.id) {
             sel = "selected";
        } 
      dosage_opt+="<option value='"+item.id+"' "+sel+">"+item.dosage+" "+item.unit+"</option>";
    });
   }
     return dosage_opt;
   }
</script>
<script type="text/javascript">	
	/* timepicker focus init removed - .timepicker class auto-init via event delegation */

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
            data:{'module_type':'ipd'},
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
          data:{'id':record_id,'case_id':case_id,'module_type':'ipd'},
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

    function viewdetail(ot_id){
        shModal('view_ot_modal').show();
        $.ajax({
            url: '<?php echo base_url(); ?>admin/operationtheatre/otdetails',
            type: "POST",
            data: {ot_id: ot_id},
            dataType: 'json',
            success: function (data) {
               shModal('view_ot_modal').show();
               $('#show_ot_data').html(data.page);     
               $('#action_detail_modal').html(data.actions);     
            },
        });
     }

    $(document).ready(function (e) {
        modal_click_disabled('view_ot_modal', 'viewDetailReportModal', 'editpayment_modal');
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
</script>
<!-- //========datatable end===== -->

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
<!-- //========datatable start===== -->
<script type="text/javascript">
( function ( $ ) {
     var id = "<?php echo $patient_id; ?>";     
    'use strict';
    $(document).ready(function () {
        initDatatable('treatmentlist','admin/patient/getipdtreatmenthistory/'+id);
    });
} ( jQuery ) )
</script>

<script src="<?php echo base_url()?>backend/js/Chart.min.js"></script>
<script src="<?php echo base_url()?>backend/js/jquery.scrolling-tabs.js"></script>

<script type="text/javascript">
     $(document).ready(function () {       
           $('a[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
            $($.fn.dataTable.tables(true)).DataTable()
               .columns.adjust()
               .responsive.recalc();
            });
      });
	  
		$(document).ready(function (e) {
         $(document).on('click', '.delete-charge', function(e){
            e.preventDefault();
         let recordid=$(this).data('recordId');
          if (confirm('<?php echo $this->lang->line("delete_confirm") ?>')) {
            $.ajax({
                url: base_url+'admin/patient/deleteIpdPatientCharge',
                type: "POST",
                data: {'id':recordid},
                dataType: 'json',            
                 beforeSend: function(){
                
                 },
                success: function (data) {
                if (data.status == 1) {
                     successMsg(data.msg);
                     window.location.reload(true);
                 }                
                },
                 error: function () {
            
                },  
                complete: function(){
              
                }
            });
        }
        });

        $("#edit_charges").on('submit', (function (e) {
            e.preventDefault();         
            $.ajax({
                url: base_url+'admin/charges/edit_ipdcharges',
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

    $('.close_button').click(function(){
        $("#nurse_field").select2().select2('val', '');
        $("#doctor_field").select2().select2('val', '');
    })    
     
</script>
<script type="text/javascript">
    function discharge_revert(case_id){
         shModal('discharge_revert').show(); 
         var base_url = '<?php echo base_url() ?>';      
        $.ajax({
            type: 'POST',
            url: base_url + 'admin/bill/discharge_revert',
            data: {'module_type': 'ipd','case_id':case_id},
            dataType: 'json',            
            success: function (res) {              
             if(res.status=='success'){
                $('#bed_group_id').val(res.data.bed_group_id);
                $('#opd_details_id').val(res.data.opd_details_id);
                getBed(res.data.bed_group_id, res.data.bed, 'yes');
             }else{
                errorMsg(res.message);
             }
            },            
        });
    } 

    $("#form_discharge_revert").on('submit', (function (e) {
            e.preventDefault();         
            $.ajax({
                url: base_url+'admin/bill/discharged_bed_revert',
                type: "POST",
                data: new FormData(this),
                dataType: 'json',
                contentType: false,
                cache: false,
                processData: false,
                beforeSend: function(){
                  $("#submit_discharge_revert").btnLoading();
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
                    $("#submit_discharge_revert").btnReset();
                },
                 error: function () {
                 $("#submit_discharge_revert").btnReset();
                },  
                complete: function(){
                 $("#submit_discharge_revert").btnReset();
                }
            });
        }));

    $('.close_btn').click(function(){
        $('#comment_staff').val('');
    })

    function deletePayment(id) {
        if (confirm(<?php echo "'" . $this->lang->line('delete_confirm') . "'"; ?>)) {
            $.ajax({
                url: '<?php echo base_url(); ?>admin/patient/deleteIpdPatientPayment/'+ id,
                success: function (res) {
                    successMsg(<?php echo "'" . $this->lang->line('delete_message') . "'"; ?>);
                    window.location.reload(true);
                }
            })
        }
    }
</script> 
<script type="text/javascript">
         Chart.types.Doughnut.extend({
            name: "DoughnutTextInside",
            showTooltip: function () {
                this.chart.ctx.save();
                Chart.types.Doughnut.prototype.showTooltip.apply(this, arguments);
                this.chart.ctx.restore();
            },
            draw: function () {
                Chart.types.Doughnut.prototype.draw.apply(this, arguments);
                var width = this.chart.width,
                        height = this.chart.height;
                var fontSize = (height / 190).toFixed(2);
                this.chart.ctx.font = fontSize + "em Verdana";
                this.chart.ctx.textBaseline = "middle";
                this.chart.ctx.textAlign = "left";
                var text = "<?php echo $donut_graph_percentage; ?>%",
                        textX = Math.round((width - this.chart.ctx.measureText(text).width) / 2),
                        textY = height / 2;
                this.chart.ctx.fillText(text, textX, textY);
            }
        });
    

        var used_credit_limit="";
        var balance_credit_limit="";

        used_credit_limit=<?php echo number_format($used_credit_limit, 2, '.', ''); ?>;
        balance_credit_limit=<?php echo number_format($balance_credit_limit, 2, '.', ''); ?>;

        var data = [{
                lebel: 'complete',
                value: used_credit_limit.toFixed(2),
                color: "#f40000"
            }, {
                value: balance_credit_limit.toFixed(2),
                color: "#4CAF50"
            }
        ];

        if ($('#pieChart').length) {
            var DoughnutTextInsideChart = new Chart($('#pieChart')[0].getContext('2d')).DoughnutTextInside(data, {
                responsive: true
            });
        }
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
    function addobstetric() {
       
       shModal("add_obstetric").show();
       var patient_id = $("#patient_id").val() ;
       $("#obstetric_title").html('<?php echo $this->lang->line('add_obstetric_history'); ?>');
    
       $.ajax({
            url: base_url+'admin/antenatal/addobstetric',
            dataType:'JSON',
            data:{patient_id:patient_id},
            type:"POST",
             beforeSend: function() {
              },
               success: function (res) {                
                $('.modal-body',"#add_obstetric").html(res.page);
                $('.modal-body',"#add_obstetric").find('table').find('.select2').select2();
                 $('.modal-body',"#add_obstetric").find('.multiselect2').select2({  
               
                });
                 $('.filestyle','#add_obstetric').dropify();
             },
            complete: function() {
                  if ($.fn.wysihtml5) {
                      $("#compose-textareass,#compose-textareaneww").wysihtml5({
                          toolbar: {
                              "image": false,
                          }
                      });
                  }
             },
             error: function(xhr) { // if error occured
              alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");
         }
        });
    }
</script>

<script>
    $("#form_obstetric").on('submit', (function (e) {        
           
            var sub_btn_clicked = $("button[type=submit][clicked=true]");   
            var sub_btn_clicked_name=sub_btn_clicked.attr('name');
            e.preventDefault();           
            $.ajax({
                url: '<?php echo base_url(); ?>admin/antenatal/add_obstetric',
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
                        window.location.reload();
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
    function viewobstetric(id){
        $.ajax({
            url: base_url+'admin/antenatal/getobstetrichistory',
            dataType:'JSON',
            data:{'id':id} ,
            type:"POST",
            beforeSend: function() {
                  
              },
                success: function (res) {
                    shModal('viewobstetrichistory').show();                    
                    $('.modal-body',"#viewobstetrichistory").html(res.page);
                    $('#edit_printhistory').html( "<a href='javascript:void(0)' class='btn btn-sm btn-light' data-bs-toggle='tooltip' onclick='printobstetrichistory(" + id + ")' title='<?php echo $this->lang->line('print'); ?>'><i class='fa fa-print'></i></a>" );
                    <?php if ($this->rbac->hasPrivilege('ipd_previous_obstetric_history', 'can_edit')) { ?>
                    $('#edit_edithistory').html( "<a href='javascript:void(0)' class='btn btn-sm btn-light' data-bs-toggle='tooltip' onclick='editobstetric(" + id + ")' title='<?php echo $this->lang->line('edit'); ?>'><i class='fa fa-pencil'></i></a>" );
                    <?php } ?>
                    <?php if ($this->rbac->hasPrivilege('ipd_previous_obstetric_history', 'can_delete')) { ?>
                    $('#edit_deletehistory').html( "<a href='javascript:void(0)' class='btn btn-sm btn-light text-danger' data-bs-toggle='tooltip' onclick='delete_obstetric(" + id + ")' title='<?php echo $this->lang->line('delete'); ?>'><i class='fa fa-trash'></i></a>" );
                    <?php } ?>                    
               }
           });
    }

    function editobstetric(id) {     
       $("#obstetric_title").html('<?php echo $this->lang->line('edit_obstetric_history'); ?>');
        $.ajax({
            url: base_url+'admin/antenatal/editobstetrichistory',
            dataType:'JSON',
            data:{'id':id} ,
            type:"POST",
             beforeSend: function() {
                  
              },
               success: function (res) {
                shModal('viewobstetrichistory').hide();
                $('.modal-body',"#add_obstetric").html(res.page);
                var medicineTable= $('.modal-body',"#add_obstetric").find('table#tableID');
                medicineTable.find('.select2').select2();
                $('.modal-body',"#add_obstetric").find('.multiselect2').select2({   
                    placeholder: 'Select',
                    allowClear: false,
                    minimumResultsForSearch: 2
                });                
                            shModal('add_obstetric').show();
                            $('.filestyle','#add_obstetric').dropify();
                },
                complete: function() {
                            if ($.fn.wysihtml5) {
                                $("#compose-textareas,#compose-textareanew").wysihtml5({
                                    toolbar: {
                                        "image": false,
                                    }
                                });
                            }
                         },
                         error: function(xhr) { // if error occured
                          alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");
                }
            });
    }
</script>
<script>
    function addpostnatal() {       
		$("#postnatal_title").html("<?php echo $this->lang->line('add_postnatal'); ?>");
		$("#patient_id").val('');
		$("#labor_time").val('');
        $("#delivery_time").val('');
        $("#routine_question").val('');
        $("#general_remark").val('');
        $("#postnatal_id").val('');
		shModal("postnatal").show();
		var patient_id = $("#patient_id").val() ;        
    }

    $("#form_postnatal").on('submit', (function (e) {
            var sub_btn_clicked = $("button[type=submit][clicked=true]");   
            var sub_btn_clicked_name=sub_btn_clicked.attr('name');
            e.preventDefault();
            $.ajax({
                url: '<?php echo base_url(); ?>admin/antenatal/add_postnatal',
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
                        window.location.reload();
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

<script type="text/javascript">
    $(document).on('click','.view_postnatal',function(){
        var id = $(this).data('recordId');     
          $.ajax({
                url: '<?php echo base_url(); ?>admin/antenatal/getpostnatal/'+id,
                type: "POST",               
                dataType: 'json',
                contentType: false,                
                processData: false,
                  beforeSend: function() {
                   
                 },
                success: function (res) {
                    $("#postnatal_title").html("<?php echo $this->lang->line('edit_postnatal'); ?>");
                    $("#labor_time").val(res.data.labour_time);
                    $("#delivery_time").val(res.data.delivery_time);
                    $("#routine_question").val(res.data.routine_question);
                    $("#general_remark").val(res.data.general_remark);
                    $("#postnatal_id").val(res.data.id);                   
                    shModal("postnatal").show();
                }            
             });
    });
</script>

<script>
     $(document).on('click','.delete_postnatal',function(){
            var id = $(this).data('recordId');
            if (confirm('<?php echo $this->lang->line('are_you_sure_to_delete_this'); ?>')) {
                $.ajax({
                    url: '<?php echo base_url(); ?>admin/antenatal/deletepostnatal/'+id,
                    dataType:"json",
                    success: function (res) {
                        successMsg(res.msg);
                       window.location.reload(true);
                    },
                    error: function () {
                        alert("Fail")
                    }
                });
        }
     });
</script>

<script>
     $(".showdiv").css("display","none");
    function showdiv(idd){  
        var optionValue = $("#alive_or_dead_"+idd).val();
            if(optionValue=='Dead')
            {
                 $("#showdiv_"+idd).css("display","block");
            }else{
                 $("#showdiv_"+idd).css("display","none");
            }
    }
</script>

<script>
    function printobstetrichistory(id) {
        var base_url = '<?php echo base_url() ?>';
        $.ajax({
            url: base_url + 'admin/antenatal/printobstetrichistory' ,
            type: 'post',
            data: { id: id },
            dataType:"JSON",

            success: function (result) {
                popup(result.page);
            }
        });
    }
</script>
<script>
    function delete_obstetric(id) { 
        var msg = '<?php echo $this->lang->line('are_you_sure_to_delete_this'); ?>';
        if (confirm(msg)) {
            $.ajax({
            url: base_url + 'admin/antenatal/deleteobstetrichistory/'+id,
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
    function addipdantenatal(ipdid) {      
        $("#ipdantenatal_title").html('<?php echo $this->lang->line("add_antenatal_finding"); ?>');
        var patient_id = $("#patient_id").val();
        var visit_detail_id ="";      
         $.ajax({
            url: base_url+'admin/antenatal/addipdantenatalfinding',
            dataType:'JSON',
            data:{'ipdid':ipdid,'visit_detail_id':visit_detail_id},
            type:"POST",
             beforeSend: function() {
              },
               success: function (res) {
                $('.modal-body',"#ipdantenatalprescription").html(res.page);
                $('.modal-body',"#ipdantenatalprescription").find('table').find('.select2').select2();
                 $('.modal-body',"#ipdantenatalprescription").find('.multiselect2').select2({   
                    placeholder: 'Select',
                    allowClear: false,
                    minimumResultsForSearch: 2
                });
                shModal('ipdantenatalprescription').show();
                 $('.filestyle','#ipdantenatalprescription').dropify();                 
             },
              complete: function() {
                  if ($.fn.wysihtml5) {
                      $("#compose-textareass,#compose-textareaneww").wysihtml5({
                          toolbar: {
                              "image": false,
                          }
                      });
                  }
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
                        $('.ajaxlistvisit').DataTable().ajax.reload();
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
    
    $("#antenatal_ipdprescription").on('submit', (function (e) {
            e.preventDefault();           
            $.ajax({
                url: '<?php echo base_url(); ?>admin/antenatal/add_ipdantenatalprescription',
                type: "POST",
                data: new FormData(this),
                dataType: 'json',
                contentType: false,
                cache: false,
                processData: false,
                beforeSend: function() {
                    $("#save_antenatal_btn").btnLoading();
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
                        shModal('ipdantenatalprescription').hide();
                        window.location.reload(true);
                    }
                },
                 error: function(xhr) { // if error occured
                    alert("Error occured.please try again");
                   $("#save_antenatal_btn").btnReset();
                },
                 complete: function() {
                     $("#save_antenatal_btn").btnReset();
                 }
            }); 
        })); 

</script>
<script>
   function viewantenatal(visitid) {
        $.ajax({
            url: '<?php echo base_url(); ?>admin/antenatal/getantenatalprescription/' + visitid + '/ipd',
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

    function printipdantenatalprescription(ipdid) {      
        var base_url = '<?php echo base_url() ?>';
        $.ajax({
            url: base_url + 'admin/antenatal/printipdantenatalprescription' ,
            type: 'GET',
            data: { ipdid: ipdid },
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
                    shModal('prescriptionview').hide();
                    $('.modal-body',"#obstetric_prescription").html(res.page);                    
                    shModal('obstetric_prescription').show();                  
                    $('.filestyle','#obstetric_prescription').dropify();
                    /* re-init removed - event delegation handles dynamic content */
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
    function viewipdantenatal(antenatal_id) {       
        $.ajax({
            url: '<?php echo base_url(); ?>admin/antenatal/getipdantenatalfindings/' + antenatal_id ,
            success: function (res) {
                $("#getdetails_finding").html(res);
            },
            error: function () {
                alert("<?php echo $this->lang->line('fail'); ?>")
            }
        }); 
        shModal('findingview').show();
    }
</script>
<script>
    function edit_ipdantenatalprescription(id) {
        $("#ipdantenatal_title").html('<?php echo $this->lang->line("edit_antenatal_finding"); ?>');		
        $.ajax({
            url: base_url+'admin/antenatal/editipdantenatalprescription',
            dataType:'JSON',
            data:{'antenatal_id':id} ,
            type:"POST",
             beforeSend: function() {
                  
              },
               success: function (res) {
                    shModal('prescriptionview').hide();                    
                    $('.modal-body',"#ipdantenatalprescription").html(res.page);                    
                    shModal('ipdantenatalprescription').show();                    
                    $('.filestyle','#ipdantenatalprescription').dropify();
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
    $('#add_vital_modal').click(function(){
        $('#modal_title').empty();
        $('#modal_title').append('<?php echo $this->lang->line('add_vital') ; ?>');
    })

    $(document).on('click','.edit_vital_modal',function(){
        $('#modal_title').empty();
        $('#modal_title').append('<?php echo $this->lang->line('edit_vital') ; ?>');
    })
</script>
<script>
	
	var total_rows_vitals=1;
	$(document).on('click','.add-vital-record',function(){		
		var table = document.getElementById("tableID_vitals");
        var id = total_rows_vitals+1;        
        var div = "<td><input type='hidden' name='total_rows_vitals[]' value='" + id + "'><select name='vital_name_"+id+"' id='vital_name_"+id+"' class='form-control'><option value=''><?php echo $this->lang->line('select'); ?></option><?php foreach($vital_list as $row){ ?><option value='<?php echo $row['id']; ?>'><?php echo $row['name']; ?><?php if($row['reference_range']){ echo ' ('.$row['reference_range'] .')'; } ?></option><?php } ?></select></td><td><input name='from_reference_range_"+id+"' id='from_reference_range_"+id+"'  type='text' class='form-control'  /></td><td><input id='vital_date_"+id+"' name='vital_date_"+id+"'  type='text' class='form-control datetime'  /></td>";
        var row =  "<tr id='row_vitals" + id + "'>" + div + "<td class='text-center align-middle'><button type='button' data-row-id='"+id+"' class='btn btn-sm btn-outline-danger delete_vitals_row'><i class='fa fa-remove'></i></button></td></tr>";
        $('#tableID_vitals').append(row);       
        total_rows_vitals++;       
	});
	
	$(document).on('click','.delete_vitals_row',function(e){
        if(confirm("<?php echo $this->lang->line('are_you_sure_to_delete_this'); ?>")){
            var modal_=$(e.target).closest('div.modal');
            var del_row_id=$(this).data('rowId');
            $("#row_vitals" + del_row_id).remove();             
        }        
	});
	
    $(document).ready(function (e) {
        $("#formadd").on('submit', (function (e) {           
            e.preventDefault();
            var total_rows = document.getElementsByName("total_rows_vitals[]").length;
            if(total_rows==0){
                errorMsg("<?php echo $this->lang->line('please_add_at_lease_one_vital'); ?>");
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
                $("#evital_date").val(data.messure_date);
                shModal('myvitalEditModal').show();
            },
        });
    }  
    
    function delete_patient_vital(id) {        
        var msg = '<?php echo $this->lang->line('are_you_sure_you_want_to_delete_this'); ?>';
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
    
    $(document).ready(function (e) {
        modal_click_disabled('add_obstetric', 'viewobstetrichistory', 'add_obstetric');
    });
</script>

<!-- IPD Sub Sidebar -->
<script>
    var ipd_sidebar_loaded      = false;
    var ipd_sidebar_cur_page    = 1;
    var ipd_sidebar_total_pages = 1;
    var ipd_sidebar_timer       = null;
    var ipd_current_id          = <?php echo (int)$ipdid; ?>;
    var ipd_ajax_url            = '<?php echo base_url('admin/patient/getipdsidebarpage/'); ?>';
    var ipd_base_url            = '<?php echo base_url('admin/patient/ipdprofile/'); ?>';
    var ipd_bed_group_id        = <?php echo isset($ipd_bed_group_id) ? (int)$ipd_bed_group_id : 0; ?>;

    function escHtml(s) { return $('<div>').text(s == null ? '' : s).html(); }

    function loadIpdSidebar(page) {
        var search = $('#ipd_sidebar_search').val();
        $('#ipd_sidebar_content').fadeTo(100, 0.3, function () {
            $.getJSON(ipd_ajax_url + page + '?search=' + encodeURIComponent(search) + '&bed_group_id=' + ipd_bed_group_id, function (res) {
                ipd_sidebar_cur_page    = res.page;
                ipd_sidebar_total_pages = Math.ceil(res.total / res.per_page);

                // Update total count badge
                if (res.total > 0) {
                    $('#ipd_sidebar_total').text(res.total).show();
                } else {
                    $('#ipd_sidebar_total').hide();
                }

                var html = '';
                if (!res.patients || res.patients.length === 0) {
                    html  = '<div class="ipd-empty">';
                    html += '<i class="fa fa-user-slash"></i>';
                    html += '<div>No patients found</div>';
                    html += '</div>';
                } else {
                    html = '<ul class="ipd-patient-list">';
                    $.each(res.patients, function (i, p) {
                        var isActive = (p.ipd_id == ipd_current_id);
                        var name     = escHtml(p.patient_name);
                        var pid      = escHtml(p.patient_id);
                        var initial  = (p.patient_name || '?').charAt(0).toUpperCase();
                        var ipdLabel = (res.ipd_prefix || '') + p.ipd_id;

                        // Build meta line (bed group + bed name if present)
                        var metaParts = [];
                        if (p.bed_group_name) metaParts.push(escHtml(p.bed_group_name));
                        if (p.bed_name)       metaParts.push(escHtml(p.bed_name));
                        var metaHtml = metaParts.length
                            ? '<div class="ipd-patient-meta"><i class="fa fa-bed me-1"></i>' + metaParts.join(' · ') + '</div>'
                            : '<div class="ipd-patient-meta">ID: ' + pid + '</div>';

                        html += '<li class="ipd-patient-item' + (isActive ? ' active' : '') + '">';
                        html += '<a class="ipd-patient-link" href="' + ipd_base_url + p.ipd_id + '/1" title="' + name + ' (' + pid + ')">';
                        html +=   '<div class="ipd-avatar">' + initial + '</div>';
                        html +=   '<div class="ipd-patient-info">';
                        html +=     '<div class="ipd-patient-name">' + name + '</div>';
                        html +=     metaHtml;
                        html +=   '</div>';
                        html +=   '<span class="ipd-patient-badge">' + ipdLabel + '</span>';
                        html += '</a></li>';
                    });
                    html += '</ul>';
                }

                $('#ipd_sidebar_content').html(html).fadeTo(200, 1);

                if (ipd_sidebar_total_pages > 1) {
                    $('#ipd_sidebar_pageinfo').text(page + ' / ' + ipd_sidebar_total_pages);
                    $('#ipd_sidebar_prev').prop('disabled', page <= 1);
                    $('#ipd_sidebar_next').prop('disabled', page >= ipd_sidebar_total_pages);
                    $('#ipd_sidebar_pagination').show();
                } else {
                    $('#ipd_sidebar_pagination').hide();
                }
            }).fail(function () {
                $('#ipd_sidebar_content').html('<div class="ipd-empty error"><i class="fa fa-exclamation-circle"></i><div>Failed to load</div></div>').fadeTo(200, 1);
            });
        });
    }

    $(document).ready(function () {
        $('.ph-tabs a').removeClass('active');
        $('.tab-pane').removeClass('active show');
        $('a[href="#overview"]').addClass('active');
        $('#overview').addClass('active show');


        $(document).on('click', '#ipd_sidebar_prev', function () {
            if (ipd_sidebar_cur_page > 1) loadIpdSidebar(ipd_sidebar_cur_page - 1);
        });
        $(document).on('click', '#ipd_sidebar_next', function () {
            if (ipd_sidebar_cur_page < ipd_sidebar_total_pages) loadIpdSidebar(ipd_sidebar_cur_page + 1);
        });

        $(document).on('keyup', '#ipd_sidebar_search', function () {
            clearTimeout(ipd_sidebar_timer);
            ipd_sidebar_timer = setTimeout(function () { loadIpdSidebar(1); }, 400);
        });

        ipd_sidebar_loaded = true;
        loadIpdSidebar(1);
        setTimeout(function () {
            $('.navlistscroll').scrollingTabs('refresh');
        }, 100);
    });

    // ICD-10: group filter in edit form
    $('#edit_icd10_group_filter').on('change', function () {
        var group_id = $(this).val();
        $.ajax({
            url: '<?php echo base_url(); ?>admin/icd10/get_codes_by_group',
            type: 'POST',
            data: { group_id: group_id },
            dataType: 'json',
            success: function (data) {
                var $select = $('#edit_icd_code_ids');
                $select.empty();
                $.each(data, function (i, c) {
                    $select.append('<option value="' + c.id + '">[' + c.icd_code + '] ' + c.icd_description + '</option>');
                });
                $select.trigger('change.select2');
            }
        });
    });

    // ICD-10: pre-fill saved codes when edit modal opens
    function loadIpdIcdCodes(ipd_id) {
        $.ajax({
            url: '<?php echo base_url(); ?>admin/patient/get_ipd_icd_codes/' + ipd_id,
            type: 'GET',
            dataType: 'json',
            success: function (data) {
                var $select = $('#edit_icd_code_ids');
                var saved = data.map(function (c) { return String(c.id); });
                $select.find('option').each(function () {
                    $(this).prop('selected', saved.indexOf($(this).val()) !== -1);
                });
                $select.trigger('change.select2');
            }
        });
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

<!-- //========datatable end===== -->   
