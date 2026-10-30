<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
$genderList = $this->customlib->getGender();
?>
<script src="<?php echo base_url('/') ?>backend/js/Chart.bundle.js"></script>
<script src="<?php echo base_url('/') ?>backend/js/utils.js"></script>
<div class="row">
            <div class="col-md-2 patient_sidebarlists sh-sidebar-col">
                <div class="ss-card">
                    <div class="ss-head">
                        <h3 class="ss-title"><i class="fa fa-user"></i> <?php echo $this->lang->line('patient_view'); ?></h3>
                        <span class="ss-count" id="patient_sidebar_total" style="display:none;">0</span>
                    </div>
                    <div class="ss-search-wrap">
                        <i class="fa fa-search search-icon"></i>
                        <input type="text" id="patient_sidebar_search" class="form-control" placeholder="Search ID / Name">
                    </div>
                    <div id="patient_sidebar_content">
                        <div class="text-center sh-loading-pad"><i class="fa fa-spinner fa-spin"></i></div>
                    </div>
                    <div id="patient_sidebar_pagination" class="ss-pagination" style="display:none;">
                        <button id="patient_sidebar_prev" data-bs-toggle="tooltip" title="<?= $this->lang->line('previous') ?>"><i class="fa fa-chevron-left"></i></button>
                        <span id="patient_sidebar_pageinfo" class="ss-pagination-info"></span>
                        <button id="patient_sidebar_next" data-bs-toggle="tooltip" title="<?= $this->lang->line('next') ?>"><i class="fa fa-chevron-right"></i></button>
                    </div>
                </div>
            </div>
            <div class="col-md-10 patient_itemcol sh-no-pl">
                <!-- OPD Patient Profile Wrap -->
                <div class="opd-profile-wrap">

                  <!-- ══ PAGE HEAD ══ -->
                  <div class="page-head">
                    <div class="ph-crumbs">
                      <span><?php echo $this->lang->line('patient'); ?></span>
                      <span class="sep">/</span>
                      <span><?php echo composePatientName($result['patient_name'], $result['id']); ?></span>
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
                            <?php echo composePatientName($result['patient_name'], $result['id']); ?>
                            <span class="badge ms-1 sh-badge-opd">OPD</span>
                            <?php if ($result['is_antenatal'] == 1): ?><span class="badge ms-1 sh-badge-antenatal">Antenatal</span><?php endif; ?>
                          </h1>
                          <div class="sub">
                            <span class="mono">P-<?php echo $result['id']; ?></span>
                            <span class="dot-sep"><?php echo $this->lang->line(strtolower($result['gender'])); ?> &middot; <?php echo $this->customlib->get_patient_current_age($result['id']); ?></span>
                            <?php if (!empty($result['mobileno'])): ?><span class="dot-sep"><?php echo html_escape($result['mobileno']); ?></span><?php endif; ?>
                            <span class="dot-sep"><?php echo (int)$total_visits; ?> <?php echo $this->lang->line('visits'); ?></span>
                          </div>
                        </div>
                      </div>
                    </div>

                    <!-- Tab navigation -->
                    <nav class="ph-tabs" role="tablist">
                      <a class="active" href="#overview" data-bs-toggle="tab" role="tab"><i class="fa fa-th"></i> <?php echo $this->lang->line('overview'); ?></a>
                      <?php if ($this->rbac->hasPrivilege('visit', 'can_view')): ?><a href="#activity" data-bs-toggle="tab" role="tab"><i class="far fa-caret-square-down"></i> <?php echo $this->lang->line('visits'); ?></a><?php endif; ?>
                      <?php if ($this->rbac->hasPrivilege('opd_lab_investigation', 'can_view')): ?><a href="#labinvestigation" data-bs-toggle="tab" role="tab"><i class="fas fa-flask"></i> <?php echo $this->lang->line('lab_investigation'); ?></a><?php endif; ?>
                      <?php if ($this->rbac->hasPrivilege('opd_treatment_history', 'can_view')): ?><a href="#treatment_history" data-bs-toggle="tab" role="tab"><i class="fas fa-notes-medical"></i> <?php echo $this->lang->line('treatment_history'); ?></a><?php endif; ?>
                      <?php if ($result['is_antenatal'] == 1 && $this->rbac->hasPrivilege('opd_previous_obstetric_history', 'can_view')): ?><a href="#obstetric_history" data-bs-toggle="tab" role="tab"><i class="fas fa-baby"></i> <?php echo $this->lang->line('previous_obstetric_history'); ?></a><?php endif; ?>
                      <?php if ($this->rbac->hasPrivilege('opd_timeline', 'can_view')): ?><a href="#timeline" data-bs-toggle="tab" role="tab"><i class="far fa-calendar-check"></i> <?php echo $this->lang->line('timeline'); ?></a><?php endif; ?>
                      <?php if ($this->rbac->hasPrivilege('opd_vitals', 'can_view')): ?><a href="#vitals" data-bs-toggle="tab" role="tab"><i class="fa fa-heartbeat"></i> <?php echo $this->lang->line('vitals'); ?></a><?php endif; ?>
                    </nav>
                  </div><!-- /.page-head -->

                  <div class="tab-content p-3">
                    <div class="tab-pane active show" id="overview">

                      <!-- ══ PATIENT INFORMATION CARD ══ -->
                      <section class="bcard">
                        <div class="bcard-head">
                          <div class="title"><?php echo $this->lang->line('patient_details'); ?></div>
                          <input type="hidden" id="patient_id" name="patient_id" value="<?php echo $result['id']; ?>">
                        </div>
                        <div class="bcard-grid">
                          <div class="field"><div class="l"><?php echo $this->lang->line('patient'); ?></div><div class="v"><?php echo composePatientName($result['patient_name'], $result['id']); ?></div></div>
                          <div class="field"><div class="l"><?php echo $this->lang->line('gender'); ?></div><div class="v"><?php echo $this->lang->line(strtolower($result['gender'])); ?></div></div>
                          <div class="field"><div class="l"><?php echo $this->lang->line('age'); ?></div><div class="v"><?php echo $this->customlib->get_patient_current_age($result['id']); ?></div></div>
                          <div class="field"><div class="l"><?php echo $this->lang->line('phone'); ?></div><div class="v"><?php echo html_escape($result['mobileno']); ?></div></div>
                          <div class="field"><div class="l"><?php echo $this->lang->line('guardian_name'); ?></div><div class="v"><?php echo html_escape($result['guardian_name']); ?></div></div>
                          <div class="field"><div class="l"><?php echo $this->lang->line('blood_group'); ?></div><div class="v"><?php echo !empty($result['blood_group']) ? html_escape($result['blood_group']) : '<span class="v mut">—</span>'; ?></div></div>
                          <div class="field"><div class="l"><?php echo $this->lang->line('tpa'); ?></div><div class="v"><?php echo isset($result['organisation_name']) ? html_escape($result['organisation_name']) : '<span class="v mut">—</span>'; ?></div></div>
                          <div class="field"><div class="l"><?php echo $this->lang->line('tpa_id'); ?></div><div class="v"><?php echo !empty($result['insurance_id']) ? html_escape($result['insurance_id']) : '<span class="v mut">—</span>'; ?></div></div>
                          <div class="field"><div class="l"><?php echo $this->lang->line('tpa_validity'); ?></div><div class="v"><?php echo !empty($result['insurance_validity']) ? $this->customlib->YYYYMMDDTodateFormat($result['insurance_validity']) : '<span class="v mut">—</span>'; ?></div></div>
                          <div class="field"><div class="l"><?php echo $this->lang->line('total_visits'); ?></div><div class="v"><?php echo (int)$total_visits; ?></div></div>
                          <div class="field">
                            <div class="l"><?php echo $this->lang->line('barcode'); ?></div>
                            <div class="v"><a href="<?php echo $this->media_storage->getImageURL('./uploads/patient_id_card/barcodes/'.$patient_id.'.png'); ?>" target="_blank">
                              <img  class="sh-qr-code" src="<?php echo $this->media_storage->getImageURL('./uploads/patient_id_card/barcodes/'.$patient_id.'.png'); ?>" width="80" height="22"></a></div>
                          </div>
                          <div class="field">
                            <div class="l"><?php echo $this->lang->line('qrcode'); ?></div>
                            <div class="v"><a href="<?php echo $this->media_storage->getImageURL('./uploads/patient_id_card/qrcode/'.$patient_id.'.png'); ?>" target="_blank">
                              <img  class="sh-qr-code"  src="<?php echo $this->media_storage->getImageURL('./uploads/patient_id_card/qrcode/'.$patient_id.'.png'); ?>" width="48" height="48"></a></div>
                          </div>
                        </div>
                      </section><!-- /.bcard -->

                      <!-- ══ RELATED LISTS ══ -->
                      <div class="rellist-wrap">

                        <!-- Activity Stats Grid -->
                        <div class="rellist">
                          <div class="rellist-head">
                            <div class="l">
                              <div class="ic blue"><i class="fas fa-chart-bar"></i></div>
                              <div class="title"><?php echo $this->lang->line('medical_history'); ?></div>
                            </div>
                          </div>
                          <div class="rellist-body pad">
                            <div class="sh-chip-grid">
                              <?php
                                $rl_stats = [
                                  [$this->lang->line('visits'),    $total_visits,      'blue',   'fa fa-stethoscope'],
                                  [$this->lang->line('ipd'),       $total_ipd,         'teal',   'fas fa-procedures'],
                                  [$this->lang->line('pharmacy'),  $total_pharmacy,    'amber',  'fa fa-medkit'],
                                  [$this->lang->line('pathology'), $total_pathology,   'violet', 'fas fa-flask'],
                                  [$this->lang->line('radiology'), $total_radiology,   'green',  'fas fa-x-ray'],
                                  [$this->lang->line('ambulance'), $total_ambulance,   'blue',   'fas fa-ambulance'],
                                ];
                                foreach ($rl_stats as [$lbl, $val, $col, $ic]):
                              ?>
                              <div class="kpi">
                                <div class="ic <?php echo $col; ?>"><i class="<?php echo $ic; ?>"></i></div>
                                <div>
                                  <div class="val"><?php echo (int)$val; ?></div>
                                  <div class="lbl"><?php echo $lbl; ?></div>
                                </div>
                              </div>
                              <?php endforeach; ?>
                            </div>
                          </div>
                        </div>

                        <!-- Clinical Alerts: Allergies + Symptoms + ICD -->
                        <?php
                          $rl_has_alerts = !empty($patientdetails['patient']['allergy'])
                                        || !empty($patientdetails['patient']['symptoms'])
                                        || !empty($opd_icd_codes);
                        ?>
                        <?php if ($rl_has_alerts): ?>
                        <div class="rellist">
                          <div class="rellist-head">
                            <div class="l">
                              <div class="ic amber"><i class="fa fa-exclamation-triangle"></i></div>
                              <div class="title"><?php echo $this->lang->line('known_allergies'); ?> / <?php echo $this->lang->line('symptoms'); ?> / ICD-10</div>
                            </div>
                          </div>
                          <div class="rellist-body pad alerts-card">
                            <?php if (!empty($patientdetails['patient']['allergy'])): ?>
                            <div class="alert-section">
                              <div class="alert-section-label"><i class="fa fa-ban"></i> <?php echo $this->lang->line('known_allergies'); ?></div>
                              <div class="alert-badges">
                                <?php foreach ($patientdetails['patient']['allergy'] as $row): ?>
                                <span class="badge badge-allergy"><?php echo html_escape($row['known_allergies']); ?></span>
                                <?php endforeach; ?>
                              </div>
                            </div>
                            <?php endif; ?>
                            <?php if (!empty($patientdetails['patient']['symptoms'])): ?>
                            <div class="alert-section">
                              <div class="alert-section-label"><i class="fa fa-stethoscope"></i> <?php echo $this->lang->line('symptoms'); ?></div>
                              <div class="alert-badges">
                                <?php foreach ($patientdetails['patient']['symptoms'] as $row): ?>
                                <span class="badge badge-symptom"><?php echo html_escape($row['symptoms']); ?></span>
                                <?php endforeach; ?>
                              </div>
                            </div>
                            <?php endif; ?>
                            <?php if (!empty($patientdetails['patient']['findings'])): ?>
                            <div class="alert-section">
                              <div class="alert-section-label"><i class="fa fa-clipboard"></i> <?php echo $this->lang->line('findings'); ?></div>
                              <div class="alert-badges">
                                <?php foreach ($patientdetails['patient']['findings'] as $row): ?>
                                <span class="badge badge-finding"><?php echo html_escape($row['finding_description']); ?></span>
                                <?php endforeach; ?>
                              </div>
                            </div>
                            <?php endif; ?>
                            <?php if (!empty($opd_icd_codes)): ?>
                            <div class="alert-section alert-section-icd">
                              <div class="alert-section-label"><i class="fa fa-book"></i> ICD-10</div>
                              <div class="alert-icd-list">
                                <?php foreach ($opd_icd_codes as $icd): ?>
                                <div class="icd-item"><span class="icd-code"><?php echo html_escape($icd['icd_code']); ?></span> <span class="icd-desc"><?php echo html_escape($icd['icd_description']); ?></span></div>
                                <?php endforeach; ?>
                              </div>
                            </div>
                            <?php endif; ?>
                          </div>
                        </div>
                        <?php endif; ?>

                        <!-- Consultant Doctors -->
                        <?php if (!empty($patientdetails['patient']['doctor'])): ?>
                        <div class="rellist">
                          <div class="rellist-head">
                            <div class="l">
                              <div class="ic teal"><i class="fa fa-user-md"></i></div>
                              <div class="title"><?php echo $this->lang->line('consultant_doctor'); ?></div>
                              <div class="count"><?php echo count($patientdetails['patient']['doctor']); ?></div>
                            </div>
                          </div>
                          <div class="rellist-body pad">
                            <div class="sh-doctor-stack">
                              <?php foreach ($patientdetails['patient']['doctor'] as $doc_value): ?>
                              <div class="sh-doctor-row">
                                <img src="<?php echo $this->media_storage->getImageURL(!empty($doc_value['image']) ? 'uploads/staff_images/'.$doc_value['image'] : 'uploads/staff_images/no_image.png'); ?>" width="32" height="32">
                                <a href="<?php echo base_url().'admin/staff/profile/'.$doc_value['id']; ?>" class="doc-link">
                                  <?php echo html_escape($doc_value['name'].' '.$doc_value['surname']); ?>
                                  <span class="doc-emp">(<?php echo html_escape($doc_value['employee_id']); ?>)</span>
                                </a>
                              </div>
                              <?php endforeach; ?>
                            </div>
                          </div>
                        </div>
                        <?php endif; ?>

                        <!-- Recent Visits -->
                        <?php if (!empty($patientdetails['patient']['visitdetails'])): ?>
                        <div class="rellist">
                          <div class="rellist-head">
                            <div class="l">
                              <div class="ic blue"><i class="fa fa-stethoscope"></i></div>
                              <div class="title"><?php echo $this->lang->line('visit_details'); ?></div>
                              <div class="count"><?php echo count($patientdetails['patient']['visitdetails']); ?></div>
                            </div>
                          </div>
                          <div class="rellist-body">
                            <table class="table table-sm mb-0 sh-fs-12-5">
                              <thead><tr>
                                <th><?php echo $this->lang->line('opd_no'); ?></th>
                                <th><?php echo $this->lang->line('appointment_date'); ?></th>
                                <th><?php echo $this->lang->line('consultant'); ?></th>
                                <th><?php echo $this->lang->line('symptoms'); ?></th>
                              </tr></thead>
                              <tbody>
                              <?php $rl_i = 0; foreach ($patientdetails['patient']['visitdetails'] as $value): if (++$rl_i > $recent_record_count) break; ?>
                              <tr>
                                <td><a href="<?php echo base_url().'admin/patient/visitdetails/'.$id.'/'.$value['opd_id']; ?>"><?php echo $this->customlib->getSessionPrefixByType('opd_no').$value['opd_id']; ?></a></td>
                                <td><?php echo $this->customlib->YYYYMMDDHisTodateFormat($value['appointment_date'], $timeformat); ?></td>
                                <td><?php echo composeStaffNameByString($value['name'], $value['surname'], $value['employee_id']); ?></td>
                                <td><?php echo html_escape($value['symptoms'] ?? ''); ?></td>
                              </tr>
                              <?php endforeach; ?>
                              </tbody>
                            </table>
                          </div>
                        </div>
                        <?php endif; ?>

                        <!-- Timeline Preview -->
                        <?php if (!empty($timeline_list)): ?>
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
                              <?php $rl_i = 0; foreach ($timeline_list as $tl): if (++$rl_i > $recent_record_count) break; ?>
                              <div class="rl-tl-item">
                                <div class="rl-tl-dot"></div>
                                <div class="rl-tl-card">
                                  <div class="rl-tl-meta">
                                    <span class="rl-tl-date"><?php if ($tl['timeline_date']) echo date($this->customlib->getHospitalDateFormat(true, true), strtotime($tl['timeline_date'])); ?></span>
                                    <?php if (!empty($tl['document'])): ?>
                                    <div class="rl-tl-actions">
                                      <a href="<?php echo base_url().'admin/timeline/download_patient_timeline/'.$tl['id']; ?>" class="btn btn-sm btn-outline-secondary" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('download'); ?>"><i class="fa fa-download"></i></a>
                                    </div>
                                    <?php endif; ?>
                                  </div>
                                  <div class="rl-tl-title"><?php echo html_escape($tl['title']); ?></div>
                                  <?php if (!empty($tl['description'])): ?><div class="rl-tl-body"><?php echo html_escape($tl['description']); ?></div><?php endif; ?>
                                </div>
                              </div>
                              <?php endforeach; ?>
                            </div>
                          </div>
                        </div>
                        <?php endif; ?>

                      </div><!-- /.rellist-wrap -->
                    </div><!-- /#overview -->
                        
                        <?php if ($this->rbac->hasPrivilege('opd_vitals', 'can_view')) { ?>
                        <div class="tab-pane fade card" id="vitals">
                            <div class="box-tab-header">
                                <h3 class="box-tab-title"><?php echo $this->lang->line('vitals'); ?></h3>
                           <div class="box-tab-tools rtl-start-0 rtl-end-auto"> 
                                <?php if ($this->rbac->hasPrivilege('opd_vitals', 'can_add')) { ?>
                               <a data-bs-toggle="modal" onclick="holdModal('vitalModal')" id="add_vital_modal" class="btn btn-primary btn-sm vital"><i class="fa fa-plus"></i> <?php echo $this->lang->line('add_vital'); ?></a>
                                <?php } ?>
                            </div>
                            </div>
                            <div class="vitals-header no-border">
                                <div id="vital_list">
                                    <?php
                                    if (empty($patient_vital_date)) {
                                        ?>
                                        <div align="center" class="dataTables_empty">No data available in table <br /><br /><img src="https://smart-hospital.in/shappresource/images/addnewitem.svg" width="150"><br /><br /><span class="text-success bolds"><i class="fa fa-arrow-left"></i> Add new record or search with different criteria.</span></div>
                                    <?php } else {   ?>
										<div class="table_inner">
											<table class="table table-striped table-bordered ">
												<thead>
													<tr>
														<th><?php echo $this->lang->line("date"); ?> </th>
														<?php foreach($vital_list as $vl){ ?>
														<th><?php echo $vl["name"]; ?> <br> (<?php echo $vl["reference_range"]; ?> <?php echo $vl["unit"]; ?>) </th>
														<?php } ?>
													</tr>
												</thead>
												<tbody> 													 
													<?php foreach($patient_vital_date as $pvd){ //$messure_date = $pvd['messure_date']?>
													<tr>
														<th><?php echo $date = $this->customlib->YYYYMMDDTodateFormat($pvd['messure_date']);
															$messure_date = date('Y-m-d', strtotime($pvd['messure_date']));
														?></th>														
														<?php foreach($vital_list as $vl){ 														
															$vid = $vl["id"];  ?>														
																<td>
                                                                    <div class="relative">
																		<?php  foreach($patientvital[$messure_date][$vid] as $pmi){ ?>	
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
																						<a class="btn btn-secondary btn-sm" data-bs-toggle="tooltip" onclick="delete_patient_vital('<?php echo $pmi['id']; ?>')" title="<?php echo $this->lang->line('delete'); ?>"><i class="fa fa-trash"></i></a>
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
                        <?php } ?>
                        
                        <?php if ($this->rbac->hasPrivilege('visit', 'can_view')) { ?>
                            <div class="tab-pane fade card" id="activity">
                                <div class="box-tab-header">
                                    <h3 class="box-tab-title"><?php echo $this->lang->line('visits'); ?></h3>
                                    <div class="box-tab-tools rtl-start-0 rtl-end-auto">                                       
                                        <?php if ($this->rbac->hasPrivilege('visit', 'can_add')) { ?>
                                            <a href="#" onclick="getRevisitRecord('<?php echo $opd_details_id['opdid'] ?>')" class="btn btn-primary btn-sm revisitpatient"  data-bs-toggle="modal" title=""><i class="fas fa-exchange-alt"></i> <?php echo $this->lang->line('new_visit'); ?>
                                            </a>
                                        <?php } ?>
                                    </div>
                                </div> 
                                <div class="download_label"><?php echo composePatientName($result['patient_name'],$result['id']) . " " . $this->lang->line('opd_details'); ?></div>
                                <div class="table-responsive overflow-visible-lg">
                                    <table class="table table-hover ajaxlistvisit" data-export-title="<?php echo composePatientName($result['patient_name'],$result['id']) . " " . $this->lang->line('opd_details'); ?>">
                                        <thead>
                                        <tr>
                                        <th><?php echo $this->lang->line('opd_no'); ?></th>
                                        <th><?php echo $this->lang->line('case_id'); ?></th>
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
                                        </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        <?php } ?>

                        <!-- -->
                        <div class="tab-pane fade card" id="labinvestigation">
                            <div class="box-tab-header">
                                <h3 class="box-tab-title"><?php echo $this->lang->line('lab_investigation'); ?></h3>
                            </div>
                            <div class="impbtnview-t9 rtl-end-auto rtl-start-20">                                
                            </div>                            
                               <div class="download_label"><?php echo composePatientName($result['patient_name'],$result['id']) . " " . $this->lang->line('opd_details'); ?></div>
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-hover example" data-export-title="<?php echo composePatientName($result['patient_name'],$result['id']) . " " . $this->lang->line('opd_details'); ?>">
                                    <thead>
                                        <th><?php echo $this->lang->line('test_name'); ?></th>
                                        <th><?php echo $this->lang->line('case_id'); ?></th>
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
                                           <td><?php echo $row['case_reference_id']; ?></td>
                                            <td><?php echo $this->lang->line($row['type']); ?></td>
                                            <td><label>
                                 <?php echo composeStaffNameByString($row['collection_specialist_staff_name'],$row['collection_specialist_staff_surname'],$row['collection_specialist_staff_employee_id']); ?>
                                 </label>                                    
                                     <br/>                                     
                                     <label><?php echo $this->lang->line('pathology_center');  ?> : </label>                                    
                                        <?php
                                   echo $row['test_center']; 
                                    ?>
                                    <br/>
                                    <?php if($row['collection_date']){ echo $this->customlib->YYYYMMDDTodateFormat($row['collection_date']); } ?></td>
                                       
                                       <td>
                                    <?php
                                    if($row['reporting_date']){
                                    echo  $this->customlib->YYYYMMDDTodateFormat($row['reporting_date']); 
                                    } ?>                                        
                                    </td>
                                    <td class="text-start">                                         
                                        <?php      
                                    echo composeStaffNameByString($row['approved_by_staff_name'],$row['approved_by_staff_surname'],$row['approved_by_staff_employee_id']);
                                     ?>
                                     <br/>
                                    <?php  if($row['parameter_update']){                              
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
                         <!-- -->
                        <div class="tab-pane fade card" id="treatment_history">
                            <div class="box-tab-header">
                                <h3 class="box-tab-title"><?php echo $this->lang->line('treatment_history'); ?></h3>
                            </div>
                            <div class="impbtnview-t9 rtl-end-auto rtl-start-20">
                                
                            </div>                            
                               <div class="download_label"><?php echo composePatientName($result['patient_name'],$result['id']) . " " . $this->lang->line('opd_details'); ?></div>
                            <div class="table-responsive overflow-visible-lg">
                                <table class="table table-striped table-bordered table-hover treatmentlist"  data-export-title="<?php echo composePatientName($result['patient_name'],$result['id']) . " " . $this->lang->line('opd_details'); ?>">
                                        <thead>
                                        <th><?php echo $this->lang->line('opd_no'); ?></th>
                                        <th><?php echo $this->lang->line('case_id'); ?></th>
                                        <th><?php echo $this->lang->line('appointment_date'); ?></th>
                                        <th><?php echo $this->lang->line('symptoms'); ?></th>
                                        <th><?php echo $this->lang->line('consultant'); ?></th>
                                        <th class="text-end noExport"><?php echo $this->lang->line('action') ?></th>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                </table>
                            </div> 
                        </div> 
						
                        <!--obstetric history-->
                        <?php if($result['is_antenatal']==1){ ?>
						<?php if ($this->rbac->hasPrivilege('opd_previous_obstetric_history', 'can_add')) { ?>
                        <div class="tab-pane fade" id="obstetric_history">
                            <div class="box-tab-header">
                                <h3 class="box-tab-title"><?php echo $this->lang->line('previous_obstetric_history'); ?></h3>
                                <div class="box-tab-tools rtl-start-0 rtl-end-auto">
									<?php if ($this->rbac->hasPrivilege('opd_previous_obstetric_history', 'can_add')) { ?>
                                     <a href="#"  onclick="addobstetric();" class="btn btn-primary btn-sm "  data-bs-toggle="modal" title=""><i class="fas fa-plus"></i> <?php echo $this->lang->line('add_previous_obstetric_history'); ?>
                                        </a>
									<?php } ?>	
                             </div>
                            </div>                            
                            <div class="impbtnview-t9 rtl-end-auto rtl-start-20">                                 
                            </div>
                            
                               <div class="download_label"><?php echo composePatientName($result['patient_name'],$result['id']) . " " . $this->lang->line('opd_details'); ?></div>
                            <div class="table-responsive">
                               <table class="table table-striped table-bordered table-hover example"  data-export-title="<?php echo composePatientName($result['patient_name'],$result['id']) . " " . $this->lang->line('opd_details'); ?>">
                                        <thead>
                                        <tr>
                                            <th><?php echo $this->lang->line('place_of_delivery'); ?></th>
                                        <th><?php echo $this->lang->line('duration_of_pregnancy'); ?></th>
                                        <th><?php echo $this->lang->line('complication_in_pregnancy_or_puerperium'); ?></th>
                                        <th><?php echo $this->lang->line('birth_weight'); ?></th>
                                        <th><?php echo $this->lang->line('gender'); ?></th>
                                        <th><?php echo $this->lang->line('infant_feeding') ?></th>
                                        <th><?php echo $this->lang->line('birth_status') ?></th>
                                        <th><?php echo $this->lang->line('death_month') ?></th>
                                        <th><?php echo $this->lang->line('death_cause') ?></th>
                                         <th class="text-end noExport"><?php echo $this->lang->line('action') ?></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                            <?php  
											foreach($obstetric_history as $row){ ?>
                                                <tr>
                                                    <td><?php echo $row['place_of_delivery']; ?></td>
                                                    <td><?php echo $row['pregnancy_duration']; ?></td>
                                                    <td><?php echo $row['pregnancy_complications']; ?></td>
                                                    <td><?php echo $row['birth_weight']; ?></td>
                                                    <td><?php echo $row['gender']; ?></td>
                                                    <td><?php echo $row['infant_feeding']; ?></td>
                                                    <td><?php echo $row['alive_dead']; ?></td>
                                                    <td><?php echo $row['date']; ?></td>
                                                    <td><?php echo $row['death_cause']; ?></td>
                                                    <td class="text-end noExport">
													<?php if ($this->rbac->hasPrivilege('opd_previous_obstetric_history', 'can_view')) { ?>
													<a href='javascript:void(0)' onclick="viewobstetric('<?php echo $row['id']; ?>')" data-record-id="<?php echo $row['id']; ?>" class='btn btn-secondary btn-sm edit_obstetric' data-bs-toggle='tooltip' title="<?php echo $this->lang->line('view_history'); ?>"><i class='fas fa fa-reorder'></i></a>
													<?php } ?>
													</td>
                                                </tr>
                                           <?php } ?>
                                        </tbody>
                                </table>
                            </div> 
                        </div> 
						<?php } } ?>
					
                        <!-- end obstetric histor y-->
                        <div class="tab-pane fade card" id="timeline">
                            <div class="box-tab-header">
                                <h3 class="box-tab-title"><?php echo $this->lang->line('timeline'); ?></h3>                          
                                <div class="box-tab-tools rtl-start-0 rtl-end-auto"> 
                                    <?php if ($this->rbac->hasPrivilege('opd_timeline', 'can_add')) { ?>
                                        <a data-bs-toggle="modal" onclick="holdModal('myTimelineModal')" class="btn btn-primary btn-sm addtimeline"><i class="fa fa-plus"></i>  <?php echo $this->lang->line('add') ?> <?php echo $this->lang->line('timeline'); ?></a>
                                <?php } ?>
                                </div>  
                            </div>
                            <div class="timeline-header no-border">
                                <div id="timeline_list">
                                    <?php
                                    if (empty($timeline_list)) {
                                        ?>
                                        <div align="center" class="dataTables_empty">No data available in table <br /><br /><img src="https://smart-hospital.in/shappresource/images/addnewitem.svg" width="150"><br /><br /><span class="text-success bolds"><i class="fa fa-arrow-left"></i> Add new record or search with different criteria.</span></div>
                                    <?php } else {
                                     ?>
                                        <ul class="timeline timeline-inverse">
                                            <?php
                                            foreach ($timeline_list as $key => $value) {
                                                ?>      
                                                <li class="time-label">
                                                    <span class="bg-blue">    <?php
                                                echo date($this->customlib->getHospitalDateFormat(true, true), strtotime($value['timeline_date'])); 
                                                ?></span>
                                                </li> 
                                                <li>
                                                    <i class="fa fa-list-alt bg-blue"></i>
                                                    <div class="timeline-item">
                                                        <span class="time">
                                                        <?php   if ($this->rbac->hasPrivilege('opd_timeline', 'can_delete')) { 
                                                             if ($value['generated_users_type'] != 'patient') {
                                                            ?>
                                                            <a class="btn btn-secondary btn-sm" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('delete'); ?>" onclick="delete_timeline('<?php echo $value['id']; ?>')"><i class="fa fa-trash"></i></a>
                                                        <?php } }?>
                                                        </span>
                                                        <span class="time">
                                                          <?php   if ($this->rbac->hasPrivilege('opd_timeline', 'can_edit')) {  
                                                            if ($value['generated_users_type'] != 'patient') { ?>
                                                            <a onclick="editTimeline('<?php echo $value['id']; ?>')" class="btn btn-secondary btn-sm" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('edit'); ?>">
                                                                <i class="fa fa-pencil"></i>
                                                            </a> 
                                                        <?php } } ?>
                                                        </span> 
                                                        <?php if (!empty($value["document"])) { ?>
                                                            <span class="time">
                                                                <a class="btn btn-secondary btn-sm" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('download'); ?>" href="<?php echo base_url() . "admin/timeline/download_patient_timeline/" . $value["id"]; ?>"><i class="fa fa-download"></i></a></span>
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
                        <div class="tab-pane card fade" id="prescription">
                               <div class="download_label"><?php echo composePatientName($result['patient_name'],$result['id']) . " " . $this->lang->line('opd_details'); ?></div>
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
                                                    <td><?php echo $prescription_value["note"] ?></td>
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
                    </div>
                </div>
            </div> 

<div class="modal fade sh-modal sh-modal-accent" id="modal-chkstatus" tabindex="-1" aria-labelledby="modal-chkstatusLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-chkstatusLabel"><?php echo $this->lang->line('status'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="form-chkstatus" action="" method="POST">
                <div class="pup-scroll-area">
                    <div class="modal-body" id="zoom_details">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('close'); ?></button>
                </div>
            </form>
        </div>
    </div>
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
                <input type="hidden" name="type" id="type" value="opd" />
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
                                                <select name="symptoms_type[]" id="act" class="form-control form-control-sm select2 act" multiple>
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
                                                    <input class="form-control form-control-sm filterinput" type="text">
                                                    <ul class="dropdown scroll150 section_ul">
                                                        <li><label class="checkbox"><?php echo $this->lang->line('select'); ?></label></li>
                                                    </ul>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label"><?php echo $this->lang->line('symptoms_description'); ?></label>
                                                <textarea class="form-control form-control-sm" id="symptoms_description" name="symptoms"></textarea>
                                            </div>
                                            <div class="col-sm-6">
                                                <label class="form-label">ICD-10 Group</label>
                                                <select id="opd_icd10_group_filter" class="form-control form-control-sm select2">
                                                    <option value="">-- All Groups --</option>
                                                    <?php if (!empty($icd10_groups)) { foreach ($icd10_groups as $g) { ?>
                                                    <option value="<?php echo $g['id']; ?>"><?php echo html_escape($g['group_name']); ?></option>
                                                    <?php } } ?>
                                                </select>
                                            </div>
                                            <div class="col-sm-6">
                                                <label class="form-label">ICD-10 Diagnosis</label>
                                                <select name="icd_code_ids[]" id="opd_icd_code_ids" class="form-control form-control-sm select2" multiple>
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
                                                <label><?php echo $this->lang->line('appointment_date'); ?></label><small class="req"> *</small>
                                                <input name="appointment_date" class="form-control form-control-sm datetime" id="appointmentdate" placeholder="" type="text" />
                                                <span class="text-danger"><?php echo form_error('appointment_date'); ?></span>
                                            </div>
                                            <div class="col-sm-6">
                                                <label><?php echo $this->lang->line('case'); ?></label>
                                                <input class="form-control form-control-sm" type="text" name="case" id="edit_case" />
                                                <span class="text-danger"><?php echo form_error('case'); ?></span>
                                            </div>
                                            <div class="col-sm-6">
                                                <label><?php echo $this->lang->line('casualty'); ?></label>
                                                <select name="casualty" id="edit_casualty" class="form-control form-control-sm">
                                                    <?php foreach ($yesno_condition as $yesno_key => $yesno_value) { ?>
                                                    <option value="<?php echo $yesno_key ?>" <?php if ($yesno_key == 'no') { echo "selected"; } ?>><?php echo $yesno_value ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                            <div class="col-sm-6">
                                                <label><?php echo $this->lang->line('old_patient'); ?></label>
                                                <select name="old_patient" id="edit_oldpatient" class="form-control form-control-sm">
                                                    <?php foreach ($yesno_condition as $yesno_key => $yesno_value) { ?>
                                                    <option value="<?php echo $yesno_key ?>"><?php echo $yesno_value ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                            <div class="col-sm-6">
                                                <label><?php echo $this->lang->line('tpa'); ?></label>
                                                <input type="text" name="organisation" class="form-control form-control-sm" id="edit_organisation" />
                                            </div>
                                            <div class="col-sm-6">
                                                <label><?php echo $this->lang->line('reference'); ?></label>
                                                <input type="text" name="refference" class="form-control form-control-sm" id="edit_refference" />
                                                <span class="text-danger"><?php echo form_error('refference'); ?></span>
                                            </div>
                                            <div class="col-12">
                                                <label><?php echo $this->lang->line('consultant_doctor'); ?></label><small class="req"> *</small>
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
                                                <label><?php echo $this->lang->line('payment_date'); ?></label><small class="req"> *</small>
                                                <input type="text" name="payment_date" id="edit_visit_payment_date" class="form-control form-control-sm datetime" autocomplete="off">
                                                <input type="hidden" id="edit_visit_payment_id" name="edit_payment_id">
                                            </div>
                                            <div class="col-sm-6">
                                                <label><?php echo $this->lang->line('amount') . " (" . $currency_symbol . ")"; ?></label><small class="req"> *</small>
                                                <input type="text" name="amount" id="edit_visit_payment" class="form-control form-control-sm" value="">
                                            </div>
                                            <div class="col-sm-6">
                                                <label><?php echo $this->lang->line('payment_mode'); ?></label>
                                                <select class="form-control form-control-sm visit_payment_mode" name="payment_mode" id="visit_payment_mode">
                                                    <?php foreach ($payment_mode as $key => $value) { ?>
                                                    <option value="<?php echo $key ?>" <?php if ($key == 'cash') { echo "selected"; } ?>><?php echo $value ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                            <div class="col-sm-6">
                                                <label><?php echo $this->lang->line('payment_note'); ?></label>
                                                <input type="text" name="note" id="edit_visit_payment_note" class="form-control form-control-sm" />
                                            </div>
                                            <div class="cheque_div col-12" style="display:none;">
                                                <div class="row g-2">
                                                    <div class="col-sm-6">
                                                        <label><?php echo $this->lang->line('cheque_no'); ?></label><small class="req"> *</small>
                                                        <input type="text" name="cheque_no" id="edit_visit_cheque_no" class="form-control form-control-sm">
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <label><?php echo $this->lang->line('cheque_date'); ?></label><small class="req"> *</small>
                                                        <input type="text" name="cheque_date" id="edit_visit_cheque_date" class="form-control form-control-sm date">
                                                    </div>
                                                    <div class="col-12">
                                                        <label><?php echo $this->lang->line('attach_document'); ?></label>
                                                        <input type="file" class="filestyle form-control form-control-sm" name="document">
                                                        <span class="text-danger"><?php echo form_error('document'); ?></span>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php if ($this->rbac->hasPrivilege('opd_antenatal', 'can_view')) { ?>
                                            <div class="col-sm-6">
                                                <label class="vertical-align-middle"><?php echo $this->lang->line('is_antenatal'); ?></label>
                                                <input type="checkbox" name="is_for_antenatal" value="1" id="is_antenatal">
                                            </div>
                                            <?php } ?>
                                        </div><!-- /row g-2 -->
                                    </div><!-- /p-2 -->
                                </div><!-- /sh-form-card -->
                            </div><!-- /col-lg-4 -->
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
<!-- Timeline -->
<div class="modal fade sh-modal sh-modal-accent" id="myTimelineModal" tabindex="-1" aria-labelledby="myTimelineModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered  modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myTimelineModalLabel"><?php echo $this->lang->line('add_timeline'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="add_timeline" accept-charset="utf-8" enctype="multipart/form-data" method="post">
                <div class="pup-scroll-area">
                    <div class="modal-body modal-background">
                        <div class="sh-form-card mb-3">
                            <div class="sh-card-header">
                                <span class="sh-card-header-title"><?php echo $this->lang->line('timeline'); ?></span>
                            </div>
                            <div class="p-2">
                                <div class="mb-3">
                                    <label class="form-label"><?php echo $this->lang->line('title'); ?></label><small class="req"> *</small>
                                    <input type="hidden" name="patient_id" id="patient_id" value="<?php echo $id ?>">
                                    <input id="timeline_title" name="timeline_title" placeholder="" type="text" class="form-control form-control-sm" />
                                    <span class="text-danger"><?php echo form_error('timeline_title'); ?></span>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label"><?php echo $this->lang->line('date'); ?></label><small class="req"> *</small>
                                    <input id="timeline_date" name="timeline_date" value="<?php echo set_value('timeline_date', date($this->customlib->getHospitalDateFormat(true,true))); ?>" placeholder="" type="text" class="form-control form-control-sm datetime" />
                                    <span class="text-danger"><?php echo form_error('timeline_date'); ?></span>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label"><?php echo $this->lang->line('description'); ?></label>
                                    <textarea id="timeline_desc" name="timeline_desc" placeholder="" class="form-control form-control-sm"></textarea>
                                    <span class="text-danger"><?php echo form_error('description'); ?></span>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label"><?php echo $this->lang->line('attach_document'); ?></label>
                                    <input id="timeline_doc_id" name="timeline_doc" placeholder="" type="file" class="filestyle form-control form-control-sm" data-height="40" value="<?php echo set_value('timeline_doc'); ?>" />
                                    <span class="text-danger"><?php echo form_error('timeline_doc'); ?></span>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label align-top"><?php echo $this->lang->line('visible_to_this_person'); ?></label>
                                    <input id="visible_check" checked="checked" name="visible_check" value="yes" placeholder="" type="checkbox" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                    <button type="submit" data-loading-text="<?php echo $this->lang->line('processing') ?>" id="add_timelinebtn" class="btn btn-info"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                </div>
            </form>
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
            <form id="edit_timeline" accept-charset="utf-8" enctype="multipart/form-data" method="post">
                <div class="pup-scroll-area">
                    <div class="modal-body modal-background">
                        <div class="sh-form-card mb-3">
                            <div class="sh-card-header">
                                <span class="sh-card-header-title"><?php echo $this->lang->line('timeline'); ?></span>
                            </div>
                            <div class="p-2">
                                <div class="mb-3">
                                    <label class="form-label"><?php echo $this->lang->line('title'); ?></label><small class="req"> *</small>
                                    <input type="hidden" name="patient_id" id="epatientid" value="">
                                    <input type="hidden" name="timeline_id" id="etimelineid" value="">
                                    <input id="etimelinetitle" name="timeline_title" placeholder="" type="text" class="form-control form-control-sm" />
                                    <span class="text-danger"><?php echo form_error('timeline_title'); ?></span>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label"><?php echo $this->lang->line('date'); ?></label><small class="req"> *</small>
                                    <input type="text" name="timeline_date" class="form-control form-control-sm datetime" id="etimelinedate" />
                                    <span class="text-danger"><?php echo form_error('timeline_date'); ?></span>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label"><?php echo $this->lang->line('description'); ?></label>
                                    <textarea id="timelineedesc" name="timeline_desc" placeholder="" class="form-control form-control-sm"></textarea>
                                    <span class="text-danger"><?php echo form_error('description'); ?></span>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label"><?php echo $this->lang->line('attach_document'); ?></label>
                                    <input id="etimeline_doc_id" name="timeline_doc" placeholder="" type="file" class="filestyle form-control form-control-sm" data-height="40" value="<?php echo set_value('timeline_doc'); ?>" />
                                    <span class="text-danger"><?php echo form_error('timeline_doc'); ?></span>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label align-top"><?php echo $this->lang->line('visible_to_this_person'); ?></label>
                                    <input id="evisible_check" name="visible_check" value="yes" placeholder="" type="checkbox" />
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

<!-- -->
<div class="modal fade sh-modal sh-modal-accent" id="edit_prescription" tabindex="-1" aria-labelledby="edit_prescriptionLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="edit_prescriptionLabel"><?php echo $this->lang->line('edit_prescription'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="pup-scroll-area">
                <div class="modal-body" id="editdetails_prescription">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('close'); ?></button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade sh-modal sh-modal-accent" id="viewModal" tabindex="-1" aria-labelledby="viewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewModalLabel"><?php echo $this->lang->line('visit_details'); ?></h5>
                <div id='edit_delete' class="d-flex align-items-center gap-1 ms-auto me-2">
                    <?php if ($this->rbac->hasPrivilege('revisit', 'can_edit')) { ?>
                        <a href="#" class="btn btn-sm btn-light" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('edit'); ?>"><i class="fa fa-pencil"></i></a>
                    <?php } ?>
                    <?php if ($this->rbac->hasPrivilege('revisit', 'can_delete')) { ?>
                        <a href="#" class="btn btn-sm btn-light" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('delete'); ?>"><i class="fa fa-trash"></i></a>
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
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="prescriptionviewLabel"><?php echo $this->lang->line('prescription'); ?></h5>
                <div id='edit_deleteprescription' class="d-flex align-items-center gap-1 ms-auto me-2"></div>
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
                <div id='edit_deleteprescriptionmanual' class="d-flex align-items-center gap-1 ms-auto me-2"></div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="pup-scroll-area">
                <div class="modal-body" id="getdetails_prescriptionmanual">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('close'); ?></button>
            </div>
        </div>
    </div>
</div>

<!-- -->
<div class="modal fade sh-modal sh-modal-accent" id="myModaledit" tabindex="-1" aria-labelledby="myModaleditLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myModaleditLabel"><?php echo $this->lang->line('patient_details'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="pup-scroll-area">
            <div class="modal-body modal-background">
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12">
                        <form id="formeditrecord" accept-charset="utf-8" enctype="multipart/form-data" method="post">
                            <input id="eupdateid" name="updateid" placeholder="" type="hidden" class="form-control" value="" />
                            <div class="sh-form-card mb-3">
                                <div class="sh-card-header">
                                    <span class="sh-card-header-title"><?php echo $this->lang->line('patient_details'); ?></span>
                                </div>
                                <div class="p-2">
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
                                                            <?php foreach ($genderList as $key => $value) { ?>
                                                            <option value="<?php echo $key; ?>" <?php if (set_value('gender') == $key) echo "selected"; ?>><?php echo $value; ?>
                                                            </option> <?php } ?>
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
                                                        <label><?php echo $this->lang->line('age') ?></label>
                                                        <div class="sh-age-input-row" style="--yr-w:43%;--mo-w:53%;--mo-gap:4px">
                                                            <input type="text" placeholder="<?php echo $this->lang->line('year') ?>" name="age" id="eage_year" value="" class="form-control age-y">
                                                            <input type="text" id="eage_month" placeholder="<?php echo $this->lang->line('month') ?>" name="month" value="" class="form-control age-m">
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
                                                                <option value="<?php echo $value; ?>" <?php if (set_value('blood_group') == $key) echo "selected"; ?>><?php echo $value; ?></option>
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
<?php echo $this->lang->line('patient_photo'); ?>
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
                                    </div><!--./row--> 
                                </div><!--./col-md-8--> 
                            </div><!--./row-->
                                </div><!-- /p-2 -->
                            </div><!-- /sh-form-card -->
                            <!--./row-->
                            <div class="modal-footer">
                                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                                <button type="submit" data-loading-text="<?php echo $this->lang->line('processing') ?>" id="formeditrecordbtn" class="btn btn-info"><?php echo $this->lang->line('save'); ?></button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            </div><!-- /pup-scroll-area -->
        </div>
    </div>
</div>

<div class="modal fade sh-modal sh-modal-nospace" id="revisitModal" tabindex="-1" aria-labelledby="revisitModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="revisitModalLabel"><?php echo $this->lang->line('patient_details'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formrevisit" accept-charset="utf-8" enctype="multipart/form-data" method="post">
                <input type="hidden" name="password" id="revisit_password">
                <input type="hidden" name="patientid" id="pid">
                <input type="hidden" name="mobileno" id="pmobileno">
                <input type="hidden" name="email" id="pemail">
                <div class="pup-scroll-area">
                    <div class="modal-body modal-background">
                        <div class="row">

                            <!-- LEFT: Patient Info + Symptoms -->
                            <div class="col-lg-8 col-md-8 col-sm-8">

                                <!-- Patient Info Card -->
                                <div class="sh-form-card mb-2" id="patientDetails">
                                    <div class="sh-card-header">
                                        <span class="sh-card-header-title" id="listname"></span>
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
                                                    <span class="sh-info-value" id="tpa"></span>
                                                </div>
                                                <div class="sh-info-item col-6 col-md-3">
                                                    <small class="sh-info-label"><i class="fas fa-id-badge"></i> <?php echo $this->lang->line('tpa_id'); ?></small>
                                                    <span class="sh-info-value" id="tpa_id"></span>
                                                </div>
                                                <div class="sh-info-item col-6 col-md-3">
                                                    <small class="sh-info-label"><i class="fas fa-calendar-check"></i> <?php echo $this->lang->line('tpa_validity'); ?></small>
                                                    <span class="sh-info-value" id="tpa_validity"></span>
                                                </div>
                                                <div class="sh-info-item col-6 col-md-3">
                                                    <small class="sh-info-label"><i class="fas fa-fingerprint"></i> <?php echo $this->lang->line('national_identification_number'); ?></small>
                                                    <span class="sh-info-value" id="identification_number"></span>
                                                </div>
                                            </div>
                                            <div class="sh-row-divider row g-0">
                                                <div class="sh-info-item col-6 col-md-3">
                                                    <small class="sh-info-label"><i class="fas fa-allergies"></i> <?php echo $this->lang->line('any_known_allergies'); ?></small>
                                                    <span class="sh-info-value" id="any_known_allergies"></span>
                                                </div>
                                                <div class="sh-info-item col-6 col-md-3">
                                                    <small class="sh-info-label"><i class="fas fa-sticky-note"></i> <?php echo $this->lang->line('remarks'); ?></small>
                                                    <span class="sh-info-value" id="remarks"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="opd-pd-photo-cell">
                                            <?php
                                            $image = $result['image'];
                                            $_rv_has_image = !empty($image) && strpos($image, 'no_image') === false;
                                            $file = $_rv_has_image ? $image : "uploads/patient_images/no_image.png";
                                            ?>
                                            <img class="opd-pd-photo-lg<?php echo $_rv_has_image ? '' : ' d-none'; ?>" id="patient_image" src="<?php echo $this->media_storage->getImageURL($file); ?>" alt="<?php echo $this->lang->line('patient'); ?>">
                                            <div class="opd-pd-initials-lg<?php echo $_rv_has_image ? ' d-none' : ''; ?>" id="patient_image_initials" aria-hidden="true"><?php echo html_escape($ph_initial); ?></div>
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
                                                <select name='symptoms_type' id="act" class="form-control form-control-sm select2 act">
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
                                                    <input class="form-control form-control-sm filterinput" type="text">
                                                    <ul class="dropdown scroll150 section_ul">
                                                        <li><label class="checkbox"><?php echo $this->lang->line('select'); ?></label></li>
                                                    </ul>
                                                </div>
                                            </div>
                                            <div class="col-sm-4">
                                                <label class="form-label"><?php echo $this->lang->line('symptoms_description'); ?></label>
                                                <textarea name="symptoms" id="esymptoms" class="form-control form-control-sm"><?php echo set_value('symptoms'); ?></textarea>
                                            </div>
                                            <div class="col-sm-4">
                                                <label class="form-label">ICD-10 Group</label>
                                                <select id="opd_edit_icd10_group_filter" class="form-control form-control-sm select2">
                                                    <option value="">-- All Groups --</option>
                                                    <?php if (!empty($icd10_groups)) { foreach ($icd10_groups as $g) { ?>
                                                    <option value="<?php echo $g['id']; ?>"><?php echo html_escape($g['group_name']); ?></option>
                                                    <?php } } ?>
                                                </select>
                                            </div>
                                            <div class="col-sm-8">
                                                <label class="form-label">ICD-10 Diagnosis</label>
                                                <select name="icd_code_ids[]" id="opd_edit_icd_code_ids" class="form-control form-control-sm select2" multiple>
                                                    <?php if (!empty($icd10_codes)) { foreach ($icd10_codes as $c) { ?>
                                                    <option value="<?php echo $c['id']; ?>">[<?php echo html_escape($c['icd_code']); ?>] <?php echo html_escape($c['icd_description']); ?></option>
                                                    <?php } } ?>
                                                </select>
                                            </div>
                                            <div class="col-sm-6">
                                                <label class="form-label"><?php echo $this->lang->line('any_known_allergies'); ?></label>
                                                <textarea name="known_allergies" id="revisit_allergies" class="form-control form-control-sm"><?php echo set_value('known_allergies'); ?></textarea>
                                            </div>
                                            <div class="col-sm-6">
                                                <label class="form-label"><?php echo $this->lang->line('note'); ?></label>
                                                <textarea name="note_remark" id="revisit_note" class="form-control form-control-sm"><?php echo set_value('note_remark'); ?></textarea>
                                            </div>
                                            <div class="col-sm-12">
                                                <div class="row">
                                                    <?php echo display_custom_fields('opd'); ?>
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
                                                    <label class="form-label"><?php echo $this->lang->line('visit_date'); ?></label><small class="req"> *</small>
                                                    <input id="appointment_date" name="appointment_date" placeholder="" type="text" class="form-control datetime" />
                                                    <span class="text-danger"><?php echo form_error('appointment_date'); ?></span>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="mb-3">
                                                    <label class="form-label"><?php echo $this->lang->line('case'); ?></label>
                                                    <input class="form-control" type='text' id="revisit_case" name='case' />
                                                    <span class="text-danger"><?php echo form_error('case'); ?></span>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="mb-3">
                                                    <label class="form-label"><?php echo $this->lang->line('casualty'); ?></label>
                                                    <select name="casualty" class="form-control casualty">
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
                                                    <select name="old_patient" class="form-control">
                                                        <?php foreach ($yesno_condition as $yesno_key => $yesno_value) { ?>
                                                        <option value="<?php echo $yesno_key ?>" <?php if ($yesno_key == 'no') echo "selected"; ?>><?php echo $yesno_value ?></option>
                                                        <?php } ?>
                                                    </select>
                                                    <span class="text-danger"><?php echo form_error('old_patient'); ?></span>
                                                </div>
                                            </div>
                                            <div class="col-sm-6" hidden>
                                                <div class="mb-3">
                                                    <label class="form-label"><?php echo $this->lang->line('tpa'); ?></label>
                                                    <input class="form-control" type='text' id="rorganisation_name" name='organisation_name' readonly />
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="mb-3">
                                                    <label class="form-label"><?php echo $this->lang->line('reference'); ?></label>
                                                    <input class="form-control" type='text' id="revisit_refference" name='refference' />
                                                    <span class="text-danger"><?php echo form_error('refference'); ?></span>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="mb-3 form-check pt-3">
                                                    <input class="form-check-input" type="checkbox" value="1" id="is_tpa" name="is_tpa">
                                                    <label class="form-check-label" for="is_tpa"><?php echo $this->lang->line('apply_tpa'); ?></label>
                                                    <input type='hidden' id="rorganisation" name='organisation' />
                                                    <input type='hidden' id="insurance_validity" name='insurance_validity' />
                                                    <input type='hidden' id="insurance_id" name='insurance_id' />
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
                                                    <label class="form-label"><?php echo $this->lang->line('consultant_doctor'); ?></label><small class="req"> *</small>
                                                    <select name='consultant_doctor' id="consultant_doctor" class="form-control select2" <?php if ($disable_option == true) echo "disabled"; ?>>
                                                        <option value=""><?php echo $this->lang->line('select'); ?></option>
                                                        <?php foreach ($doctors as $dkey => $dvalue) { ?>
                                                        <option value="<?php echo $dvalue["id"]; ?>" <?php if ((isset($doctor_select)) && ($doctor_select == $dvalue["id"])) echo "selected"; ?>><?php echo $dvalue["name"] . " " . $dvalue["surname"] . " (" . $dvalue["employee_id"] . ")"; ?></option>
                                                        <?php } ?>
                                                    </select>
                                                    <?php if ($disable_option == true) { ?>
                                                    <input type="hidden" name="consultant_doctor" value="<?php echo $doctor_select ?>">
                                                    <?php } ?>
                                                    <span class="text-danger"><?php echo form_error('consultant_doctor'); ?></span>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
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
                                            <div class="col-sm-6">
                                                <div class="mb-3">
                                                    <label class="form-label"><?php echo $this->lang->line('charge'); ?></label><small class="req"> *</small>
                                                    <select name="charge_id" class="form-control charge select2">
                                                        <option value=""><?php echo $this->lang->line('select'); ?></option>
                                                    </select>
                                                    <span class="text-danger"><?php echo form_error('charge_id'); ?></span>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="mb-3">
                                                    <label class="form-label"><?php echo $this->lang->line('standard_charge') . " (" . $currency_symbol . ")"; ?></label>
                                                    <input type="text" readonly name="standard_charge" id="standard_charge" class="form-control" value="<?php echo set_value('standard_charge'); ?>">
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="mb-3">
                                                    <label class="form-label"><?php echo $this->lang->line('applied_charge') . " (" . $currency_symbol . ")"; ?></label><small class="req"> *</small>
                                                    <input type="text" name="amount" id="apply_charge" class="form-control apply_charge">
                                                    <span class="text-danger"><?php echo form_error('apply_charge'); ?></span>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="mb-3">
                                                    <label class="form-label"><?php echo $this->lang->line('discount'); ?></label>
                                                    <div class="input-group">
                                                        <input type="text" class="form-control discount_percentage" name="discount_percentage" id="discount_percentage" value='0' autocomplete="off">
                                                        <span class="input-group-text">%</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="mb-3">
                                                    <label class="form-label"><?php echo $this->lang->line('tax'); ?></label>
                                                    <div class="input-group">
                                                        <input type="text" class="form-control right-border-none" name="percentage" id="percentage" readonly autocomplete="off">
                                                        <span class="input-group-text">%</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="mb-3">
                                                    <label class="form-label"><?php echo $this->lang->line('amount') . " (" . $currency_symbol . ")"; ?></label><small class="req"> *</small>
                                                    <input type="text" name="apply_amount" readonly id="apply_amount" class="form-control apply_amount">
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
                                                    <select name="payment_mode" class="form-control payment_mode">
                                                        <?php foreach ($payment_mode as $payment_key => $payment_value) { ?>
                                                        <option value="<?php echo $payment_key ?>" <?php if ($payment_key == 'cash') echo "selected"; ?>><?php echo $payment_value ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="mb-3">
                                                    <label class="form-label"><?php echo $this->lang->line('paid_amount'); ?></label><small class="req"> *</small>
                                                    <input type="text" name="paid_amount" id="paid_amount" class="form-control paid_amount">
                                                </div>
                                            </div>
                                            <div class="col-sm-12">
                                                <div class="cheque_div" style="display: none;">
                                                    <div class="row g-2">
                                                        <div class="col-sm-6">
                                                            <label class="form-label"><?php echo $this->lang->line('cheque_no'); ?></label><small class="req"> *</small>
                                                            <input type="text" name="cheque_no" id="cheque_no" class="form-control">
                                                            <span class="text-danger"><?php echo form_error('cheque_no'); ?></span>
                                                        </div>
                                                        <div class="col-sm-6">
                                                            <label class="form-label"><?php echo $this->lang->line('cheque_date'); ?></label><small class="req"> *</small>
                                                            <input type="text" name="cheque_date" id="cheque_date" class="form-control date">
                                                            <span class="text-danger"><?php echo form_error('cheque_date'); ?></span>
                                                        </div>
                                                        <div class="col-sm-12">
                                                            <label class="form-label"><?php echo $this->lang->line('attach_document'); ?></label>
                                                            <input type="file" id="document_file" class="filestyle form-control" name="document">
                                                            <span class="text-danger"><?php echo form_error('document'); ?></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php if ($this->module_lib->hasActive('live_consultation')) { ?>
                                            <div class="col-sm-6">
                                                <div class="mb-3">
                                                    <label class="form-label"><?php echo $this->lang->line('live_consultation'); ?></label>
                                                    <select name="live_consult" id="live_consult" class="form-control">
                                                        <?php foreach ($yesno_condition as $yesno_key => $yesno_value) { ?>
                                                        <option value="<?php echo $yesno_key ?>" <?php if ($yesno_key == 'no') echo "selected"; ?>><?php echo $yesno_value ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <?php } ?>
                                            <?php if ($result['gender'] == 'Female') { if ($this->rbac->hasPrivilege('opd_antenatal', 'can_add')) { ?>
                                            <div class="col-sm-6" id="antenatal_div">
                                                <label class="form-label">&nbsp;</label>
                                                <div>
                                                    <input type="checkbox" class="form-check-input" name="is_for_antenatal" id="is_for_antenatal" value="1">
                                                    <label class="form-check-label ms-1" for="is_for_antenatal"><?php echo $this->lang->line('is_antenatal'); ?></label>
                                                </div>
                                            </div>
                                            <?php } } ?>
                                        </div>
                                    </div>
                                </div>

                            </div><!-- /col-lg-4 -->

                        </div>
                    </div>
                </div><!-- /pup-scroll-area -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                    <button type="submit" data-loading-text="<?php echo $this->lang->line('processing') ?>" name="save_print" class="btn btn-info printsavebtn"><i class="fa fa-print"></i> <?php echo $this->lang->line('save_print'); ?></button>
                    <button type="submit" id="formrevisitbtn" name="save" data-loading-text="<?php echo $this->lang->line('processing') ?>" class="btn btn-info"><i class="fa fa-check-circle"></i> <span><?php echo $this->lang->line('save'); ?></span></button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- -->

<!-- Add Diagnosis -->
<div class="modal fade sh-modal sh-modal-accent" id="add_diagnosis" tabindex="-1" aria-labelledby="add_diagnosisLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="add_diagnosisLabel"><?php echo $this->lang->line('add_diagnosis'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="form_diagnosis" accept-charset="utf-8" enctype="multipart/form-data" method="post">
                <div class="pup-scroll-area">
                    <div class="modal-body modal-background">
                        <div class="sh-form-card mb-3">
                            <div class="sh-card-header">
                                <span class="sh-card-header-title"><?php echo $this->lang->line('add_diagnosis'); ?></span>
                            </div>
                            <div class="p-2">
                                <div class="row g-2">
                                    <div class="col-sm-12">
                                        <div class="mb-2">
                                            <label class="form-label"><?php echo $this->lang->line('report_type'); ?></label><small class="req"> *</small>
                                            <input type="text" name="report_type" class="form-control form-control-sm" id="report_type" />
                                            <input type="hidden" value="<?php echo $id ?>" name="patient" id="patient" />
                                        </div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="mb-2">
                                            <label class="form-label"><?php echo $this->lang->line('report_date'); ?></label><small class="req"> *</small>
                                            <input type="text" name="report_date" class="form-control form-control-sm date" id="report_date" />
                                        </div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="mb-2">
                                            <label class="form-label" for="report_document"><?php echo $this->lang->line('document'); ?></label>
                                            <input type="file" class="form-control form-control-sm filestyle" name="report_document" id="report_document" />
                                        </div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="mb-2">
                                            <label class="form-label"><?php echo $this->lang->line('report_center_name'); ?></label>
                                            <input type="text" class="form-control form-control-sm" name="report_center" id="report_center" />
                                        </div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="mb-2">
                                            <label class="form-label"><?php echo $this->lang->line('description'); ?></label>
                                            <textarea name="description" class="form-control form-control-sm" id="description"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                    <button type="submit" id="form_diagnosisbtn" data-loading-text="<?php echo $this->lang->line('processing'); ?>" class="btn btn-info"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
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
                    <div class="modal-body modal-background">
                        <div class="sh-form-card mb-3">
                            <div class="sh-card-header">
                                <span class="sh-card-header-title"><?php echo $this->lang->line('edit_diagnosis'); ?></span>
                            </div>
                            <div class="p-2">
                                <div class="row g-2">
                                    <div class="col-sm-12">
                                        <div class="mb-2">
                                            <label class="form-label"><?php echo $this->lang->line('report_type'); ?></label><small class="req"> *</small>
                                            <input type="text" name="report_type" class="form-control form-control-sm" id="ereporttype" />
                                            <input type="hidden" value="" name="diagnosis_id" id="eid" />
                                            <input type="hidden" value="" name="diagnosispatient_id" id="epatient_id" />
                                        </div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="mb-2">
                                            <label class="form-label"><?php echo $this->lang->line('report_date'); ?></label><small class="req"> *</small>
                                            <input type="text" name="report_date" class="form-control form-control-sm date" id="ereportdate" />
                                        </div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="mb-2">
                                            <label class="form-label" for="ereportdocument"><?php echo $this->lang->line('document'); ?></label>
                                            <input type="file" class="form-control form-control-sm filestyle" name="report_document" id="ereportdocument" />
                                        </div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="mb-2">
                                            <label class="form-label"><?php echo $this->lang->line('report_center_name'); ?></label>
                                            <input type="text" name="report_center" class="form-control form-control-sm" id="ereportcenter" />
                                        </div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="mb-2">
                                            <label class="form-label"><?php echo $this->lang->line('description'); ?></label>
                                            <textarea name="description" class="form-control form-control-sm" id="edescription"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                    <button type="submit" id="form_editdiagnosisbtn" data-loading-text="<?php echo $this->lang->line('processing'); ?>" class="btn btn-info"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade sh-modal sh-modal-accent" id="myPaymentModal" tabindex="-1" aria-labelledby="myPaymentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myPaymentModalLabel"><?php echo $this->lang->line('add_payment'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="add_payment" accept-charset="utf-8" method="post">
                <div class="pup-scroll-area">
                    <div class="modal-body modal-background">
                        <div class="sh-form-card mb-3">
                            <div class="sh-card-header">
                                <span class="sh-card-header-title"><?php echo $this->lang->line('add_payment'); ?></span>
                            </div>
                            <div class="p-2">
                                <div class="row g-2">
                                    <div class="col-md-6">
                                        <div class="mb-2">
                                            <label class="form-label"><?php echo $this->lang->line('amount') . " (" . $currency_symbol . ")"; ?></label><small class="req"> *</small>
                                            <input type="text" name="amount" id="amount" class="form-control form-control-sm">
                                            <input type="hidden" name="patient_id" id="payment_patient_id">
                                            <input type="hidden" name="total" id="total">
                                            <span class="text-danger"><?php echo form_error('amount'); ?></span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-2">
                                            <label class="form-label"><?php echo $this->lang->line('payment_mode'); ?></label>
                                            <select class="form-control form-control-sm" name="payment_mode">
                                                <?php foreach ($payment_mode as $key => $value) { ?>
                                                    <option value="<?php echo $key ?>" <?php if ($key == 'cash') echo "selected"; ?>><?php echo $value ?></option>
                                                <?php } ?>
                                            </select>
                                            <span class="text-danger"><?php echo form_error('payment_mode'); ?></span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-2">
                                            <label class="form-label"><?php echo $this->lang->line('date'); ?></label><small class="req"> *</small>
                                            <input type="text" name="payment_date" id="date" class="form-control form-control-sm date">
                                            <span class="text-danger"><?php echo form_error('payment_date'); ?></span>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="mb-2">
                                            <label class="form-label" for="file_document"><?php echo $this->lang->line('attach_document'); ?></label>
                                            <input type="file" class="filestyle form-control form-control-sm" name="document">
                                            <span class="text-danger"><?php echo form_error('document'); ?></span>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="mb-2">
                                            <label class="form-label"><?php echo $this->lang->line('note'); ?></label>
                                            <input type="text" name="note" id="note" class="form-control form-control-sm" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                    <button type="submit" id="add_paymentbtn" data-loading-text="<?php echo $this->lang->line('processing') ?>" class="btn btn-info"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- -->

<!-- Add Prescription -->
<div class="modal fade sh-modal sh-modal-nospace" id="add_prescription" tabindex="-1" aria-labelledby="prescription_title" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="prescription_title"><?php echo $this->lang->line('add_prescription'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="form_prescription" accept-charset="utf-8" enctype="multipart/form-data" method="post">
                <div class="pup-scroll-area">
                    <div class="modal-body modal-text-white">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                    <button type="submit" name="save_print" value="save_print" data-loading-text="<?php echo $this->lang->line('processing'); ?>" class="btn btn-info"><i class="fa fa-print"></i> <?php echo $this->lang->line('save_print'); ?></button>
                    <button type="submit" name="save" value="save" data-loading-text="<?php echo $this->lang->line('processing'); ?>" class="btn btn-info"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div><!-- Add Prescription -->

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

<!-- Add Obstetric history -->
<div class="modal fade sh-modal sh-modal-accent" id="add_obstetric" tabindex="-1" aria-labelledby="add_obstetricLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="add_obstetricLabel" id="obstetric_title"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="form_obstetric" accept-charset="utf-8" enctype="multipart/form-data" method="post">
                <input type="hidden" id="patient_id" name="patient_id" value="<?php echo $result['id']; ?>">
                <input type="hidden" id="obstetric_opdid" name="obstetric_opdid" value="<?php echo $opd_details_id['opdid'] ?>">
                <div class="pup-scroll-area">
                    <div class="modal-body">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                    <button type="submit" name="save" value="save" data-loading-text="<?php echo $this->lang->line('processing'); ?>" class="btn btn-info"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div><!-- Add Obstetric history -->

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
                <div class="pup-scroll-area">
                    <div class="modal-body">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                    <button type="submit" name="save" value="save" data-loading-text="<?php echo $this->lang->line('processing'); ?>" class="btn btn-info"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div><!-- end Obstetric prescription -->

<!-- Obstetric history view -->
<div class="modal fade sh-modal sh-modal-accent" id="viewobstetrichistory" tabindex="-1" aria-labelledby="viewobstetrichistoryLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewobstetrichistoryLabel"><?php echo $this->lang->line('previous_obstetric_history'); ?></h5>
                <div id='edit_deletehistory' class="d-flex align-items-center gap-1 ms-auto me-2"></div>
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

<div class="modal fade sh-modal sh-modal-accent" id="moveIPDModal" tabindex="-1" aria-labelledby="moveIPDModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="moveIPDModalLabel"><?php echo $this->lang->line('move_patient_to_ipd'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?php echo site_url('admin/patient/moveopd') ?>" id="form_confirm-move" method="POST" accept-charset="utf-8">
                <div class="pup-scroll-area">
                    <div class="modal-body">
                        <p><?php echo $this->lang->line('some_text_in_the_modal'); ?></p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                    <button type="submit" data-loading-text="<?php echo $this->lang->line('processing'); ?>" class="btn btn-info btn-ok"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('move'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

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
                <div class="modal-body modal-background">
                    <div id="reportbilldata" class=""></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('close'); ?></button>
            </div>
        </div>
    </div>
</div>
<!-- end lab investigation modal-->

<div class="modal fade sh-modal sh-modal-accent" id="vitalModal" tabindex="-1" aria-labelledby="vitalModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="vitalModalLabel"><div id="modal_title"></div></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formadd" accept-charset="utf-8" method="post">
                <div class="pup-scroll-area">
                    <div class="modal-body modal-background">
                        <input type="hidden" class="id" name="patient_id" id="patient_id" value="<?php echo $result['id']; ?>">
                        <div class="sh-form-card mb-3">
                            <div class="sh-card-header">
                                <span class="sh-card-header-title"><?php echo $this->lang->line('vitals'); ?></span>
                            </div>
                            <div class="p-2">
                                <div class="table-responsive overflow-visible mt5">
                                    <table class="table table-striped mb0 table-bordered table-hover  tablefull12 tblProducts " id="tableID_vitals">
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
                                                    <option value="<?php echo $row['id']; ?>"><?php echo $row['name']; ?><?php if($row['reference_range']){ echo ' ('.$row['reference_range'] . " " .$row['unit'] . ')'; } ?></option>
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
                                            <td class='text-center align-middle'>
                                                <button type="button"  class="closebtn delete_vitals_row btn btn-sm btn-outline-danger" data-row-id="1" autocomplete="off"><i class="fa fa-remove"></i></button>
                                            </td>                                        
                                        </tr>
                                    </table>
                                
                                    <div class="d-flex justify-content-end">
                                        <a class="btn btn-info addplus-xs add-vital-record mb10 mt5" data-added="0"><i class="fa fa-plus"></i> <?php echo $this->lang->line('add')?></a>
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

<div class="modal fade sh-modal sh-modal-accent" id="myvitalEditModal" tabindex="-1" aria-labelledby="myvitalEditModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myvitalEditModalLabel"><?php echo $this->lang->line('edit_vital'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="edit_vital" accept-charset="utf-8" method="post">
                <div class="pup-scroll-area">
                    <div class="modal-body modal-background">
                        <div class="sh-form-card mb-3">
                            <div class="sh-card-header">
                                <span class="sh-card-header-title"><?php echo $this->lang->line('vitals'); ?></span>
                            </div>
                            <div class="p-2">
                                    <div class="mb-3">
                                        <label class="form-label"><?php echo $this->lang->line('vital_name'); ?></label><small class="req"> *</small>
                                        <input type="hidden" name="patient_id" id="vepatientid" value="">
                                        <input type="hidden" name="evital_id" id="evital_id" value="">
										<input type="hidden" name="evital_name" id="evital_name" value="">										
                                        <select id="evital_name_edit" name="evital_name_edit" class="form-control form-control-sm" disabled>
                                            <option value=""><?php echo $this->lang->line("select"); ?></option>
                                            <?php foreach($vital_list as $row){ ?>
                                                <option value="<?php echo $row['id']; ?>"><?php echo $row['name'].' ('.$row['reference_range']. " " .$row['unit'] .')'; ?></option>
                                            <?php } ?>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('vital_title'); ?></span>
                                    </div>                                   
                                    <div class="mb-3">
                                        <label class="form-label"><?php echo $this->lang->line('vital_value'); ?></label><small class="req"> *</small>
                                        <input id="evital_value" name="evital_value" placeholder="" type="text" class="form-control form-control-sm" />
                                        <span class="text-danger"><?php echo form_error('description'); ?></span>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label"><?php echo $this->lang->line('date'); ?></label><small class="req"> *</small>
                                        <input id="evital_date" name="emessure_date" value="" placeholder="" type="text" class="form-control form-control-sm datetime" />
                                        <span class="text-danger"><?php echo form_error('messure_date'); ?></span>
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

<script type="text/javascript">
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
</script>
<script>
    var prescription_rows=2;
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

    $(document).on('click', '.filterinput', function () {      
        if (!$(this).closest('.wrapper-dropdown-3').hasClass("active")) {
            $(".wrapper-dropdown-3").not($(this)).removeClass('active');
            $(this).closest("div.wrapper-dropdown-3").addClass('active');
        }
    }); 

    $(document).on('click', 'input[name="section[]"]', function () {
        $(this).closest('label').toggleClass('active_section');
    });    
</script>
<script type="text/javascript">

    $(function () {
        //Initialize Select2 Elements
        $(function () {
            $('.ph-tabs a').removeClass('active');
            $('.tab-pane').removeClass('active show');
            $('a[href="#overview"]').addClass('active');
            $('#overview').addClass('active show');

            $('.ph-tabs a').click(function (e) {
                $(this).tab('show');
                var scrollmem = $('body').scrollTop();
                window.location.hash = this.hash;
                $('html,body').scrollTop(scrollmem);
            });
        });
    });

    function edit_prescription(id) {
        $("#prescription_title").html('<?php echo $this->lang->line('edit_prescription'); ?>');
        $.ajax({
            url: base_url+'admin/prescription/editopdPrescription',
            dataType:'JSON',
            data:{'prescription_id':id} ,
            type:"POST",
             beforeSend: function() {
                  
              },
               success: function (res) {
                // Destroy before replacing HTML so CKEditor can restore original textareas cleanly
                ['compose-textareanew', 'compose-textareas'].forEach(function(id) {
                    if (window.CKEDITOR && CKEDITOR.instances[id]) { try { CKEDITOR.instances[id].destroy(true); } catch(e) {} }
                });
                shModal('prescriptionview').hide();
                $('.modal-body',"#add_prescription").html(res.page);
                var medicineTable= $('.modal-body',"#add_prescription").find('table');
                medicineTable.find('.select2').select2();
                $('.modal-body',"#add_prescription").find('.multiselect2').select2({
                    placeholder: 'Select',
                    allowClear: false,
                    minimumResultsForSearch: 2
                });

                shModal('add_prescription').show();
                $('.filestyle','#add_prescription').dropify();
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
      $('#modal-chkstatus').on('shown.bs.modal', function (e) {
            var $modalDiv = $(e.delegateTarget);           
              var id=$(e.relatedTarget).data('id');         
            
            $.ajax({
                type: "POST",
                url: base_url + 'admin/conference/getlivestatus',
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

    $(document).on('click','.print_visit_bill',function(){
       
    var opd_id=$(this).data('opdId');   
           var $this = $(this);     
     $.ajax({
                url: base_url+'admin/patient/printbill',
                type: "POST",
                data: {opd_id: opd_id},
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

    $(document).on('click','.get_opd_detail',function(){
    var visitid=$(this).data('recordId');
    var opdid = $(this).data('opdId')
           var $this = $(this);
     $.ajax({
                url: base_url+'admin/patient/getopdDetails',
                type: "POST",
                data: {visit_id: visitid,opd_id:opdid},
                dataType: 'json',
                   beforeSend: function() {
                  $this.btnLoading();
                   },
                success: function (data) {
                    var patient_id = "<?php echo $result["id"] ?>";
                    $('#edit_delete').html("<?php if ($this->rbac->hasPrivilege('visit', 'can_edit')) { ?><a href='#'' class='btn btn-sm btn-light' onclick='editRecord(" + visitid + ")' data-bs-target='#editModal' data-bs-toggle='tooltip'  title='<?php echo $this->lang->line('edit'); ?>'><i class='fa fa-pencil'></i></a><?php } ?><?php if ($this->rbac->hasPrivilege('visit', 'can_delete')) { ?><a href='#' data-bs-toggle='tooltip' data-patient_id="+patient_id+" data-record-id="+opdid+" class='delete_opd btn btn-sm btn-light' title='<?php echo $this->lang->line('delete'); ?>'><i class='fa fa-trash'></i></a><?php } ?>");
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

    $(document).on('click','.delete_opd',function(){
     let patient_id=$(this).data('patient_id');
     let id=$(this).data('recordId');
      if (confirm(<?php echo "'" . $this->lang->line('delete_confirm') . "'"; ?>)) {
            $.ajax({
                url: baseurl+'admin/patient/deleteOPD',
                type: "POST",
                data: {opdid: id,'patient_id':patient_id},
                dataType: 'json',
                success: function (data) {
                    successMsg(<?php echo "'" . $this->lang->line('delete_message') . "'"; ?>);
                    if(data.total_remain <= 0){
window.location.href = baseurl+'admin/patient/search';
                    }else{

                    window.location.reload(true);
                    }
                }
            })
        }
    });    
    
    function delete_patient(id) {
        if (confirm(<?php echo "'" . $this->lang->line('delete_confirm') . "'"; ?>)) {
            $.ajax({
                url: '<?php echo base_url(); ?>admin/patient/deleteOPDPatient',
                type: "POST",
                data: {id: id},
                dataType: 'json',
                success: function (data) {
                    successMsg(<?php echo "'" . $this->lang->line('delete_message') . "'"; ?>);
                    window.location.href = '<?php echo base_url() ?>admin/patient/search';
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
                $("#ename").val(data.patient_name);
                $("#eguardian_name").val(data.guardian_name);
                $("#emobileno").val(data.mobileno);
                $("#eemail").val(data.email);
                $("#eaddress").val(data.address);
                $("#eage_year").val(data.age);
                $("#eage_month").val(data.month);
                $("#ebirth_date").val(data.dob);
                $("#enote").val(data.note);
                $("#exampleInputFile").attr("data-default-file", '<?php echo base_url() ?>' + data.image);
                $(".dropify-render").find("img").attr("src", '<?php echo base_url() ?>' + data.image); 
                $("#eknown_allergies").val(data.known_allergies); 
                $('select[id="blood_groups"] option[value="' + data.blood_group + '"]').attr("selected", "selected");
                $('select[id="egenders"] option[value="' + data.gender + '"]').attr("selected", "selected");
                $('select[id="marital_statuss"] option[value="' + data.marital_status + '"]').attr("selected", "selected");
                shModal("myModal").hide();
                holdModal('myModaledit');
            },
        });
    } 

    $(document).ready(function (e) {
        $("#formeditrecord").on('submit', (function (e) {
            $("#formeditrecordbtn").btnLoading();
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
                    $("#formeditrecordbtn").btnReset();
                },
                error: function () {

                }
            });
        }));
    });     
 
     function getRecord_id(visitid) { 

        $('#prescription_title').html('<?php echo $this->lang->line('add_prescription'); ?>');
         $.ajax({
            url: base_url+'admin/prescription/addopdPrescription',
            dataType:'JSON',
            data:{'visit_detail_id':visitid},
            type:"POST",
             beforeSend: function() {
              },
               success: function (res) {
                ['compose-textareaneww', 'compose-textareass'].forEach(function(id) {
                    if (window.CKEDITOR && CKEDITOR.instances[id]) { try { CKEDITOR.instances[id].destroy(true); } catch(e) {} }
                });
                $('.modal-body',"#add_prescription").html(res.page);
                $('.modal-body',"#add_prescription").find('table').find('.select2').select2();
                $('.modal-body',"#add_prescription").find('.multiselect2').select2({
                    placeholder: 'Select',
                    allowClear: false,
                    minimumResultsForSearch: 2
                });
                shModal('add_prescription').show();
                $('.filestyle','#add_prescription').dropify();
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

    function editRecord(visitid) {      
        $.ajax({ 
            url: '<?php echo base_url(); ?>admin/patient/getopdvisitdetails',
            type: "GET",
            data: {visitid: visitid},
            dataType: 'json',
            success: function (data) {
             
                $('#visitid').val(visitid);
                $('#visit_transaction_id').val(data.transaction_id);
                $('#customfield').html(data.custom_fields_value);
                $("#patientid").val(data.patient_id);
                $("#patientname").val(data.patient_name);
                $("#appointmentdate").val(data.appointment_date);
                $("#edit_case").val(data.case_type);
                $("#symptoms_description").val(data.symptoms);
                $("#edit_casualty").val(data.casualty);
                $("#edit_knownallergies").val(data.known_allergies);
                $("#edit_refference").val(data.refference);
                $("#edit_revisit_note").text(data.note);
                $("#edit_amount").val(data.apply_charge);
                $('select[id="edit_oldpatient"] option[value="' + data.patient_old + '"]').attr("selected", "selected");
                $("#edit_height").val(data.height);
                $("#edit_weight").val(data.weight);
                $("#edit_bp").val(data.bp);
                $("#edit_pulse").val(data.pulse);
                $("#edit_temperature").val(data.temperature);
                $("#edit_respiration").val(data.respiration);
                $("#edit_opdid").val(data.opdid);
                 if(data.is_antenatal==1){
                     $("#is_antenatal").prop('checked',true);
                }
                 $("#eknown_allergies").val(data.visit_known_allergies);
                 $("#edit_visit_payment_date").val(data.payment_date);
                 $("#edit_visit_payment").val(data.amount);
                 $("#visit_payment_mode").val(data.payment_mode).prop('selected');
                 $(".visit_payment_mode").trigger('change');
                 $("#edit_visit_cheque_no").val(data.cheque_no);
                 $("#edit_visit_cheque_date").val(data.cheque_date);
                 $("#edit_visit_payment_note").val(data.payment_note);
                 $("#edit_organisation").val(data.organisation_name);
                shModal("viewModal").hide();
                $("#edit_organisation").prop('readonly',true);
                $('select[id="edit_consdoctor"] option[value="'+data.cons_doctor+'"]').attr("selected","selected");

                $(".select2").select2().select2('val', data.cons_doctor);
                
                holdModal('editModal');
            },
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
                $("#ereportcenter").val(data.report_center);
                $("#edescription").val(data.description);
                holdModal('edit_diagnosis');
            },
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
                       if(sub_btn_clicked_name === "save_print") {                            
                            printprescription(data.visitid,true);
                        }
                        shModal('add_prescription').hide();
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
    });

    $(document).ready(function (e) {
        $("#form_diagnosis").on('submit', (function (e) {           
            $("#form_diagnosisbtn").btnLoading();
            e.preventDefault();
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
                error: function () {
                }
            });
        }));
    });

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

    $(document).on('select2:select','.medicine_category',function(){      
      getMedicine($(this),$(this).val(),0);
       selected_medicine_category_id =$(this).val();   
       var medicine_dosage=getDosages(selected_medicine_category_id);
       $(this).closest('tr').find('.medicine_dosage').html(medicine_dosage);
    }); 
    
    $(document).on('select2:select','.medicine_name',function(){   
        var row_id_val= $(this).data('rowid');
        if($(this).val()!="") {
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

    function getMedicine(med_cat_obj,val,medicine_id){
      var medicine_colomn=med_cat_obj.closest('tr').find('.medicine_name');
        medicine_colomn.html("");    
        $.ajax({
            url: '<?php echo base_url(); ?>admin/pharmacy/get_medicine_name',
            type: "POST",
            data: {medicine_category_id: val},
            dataType: 'json',
              beforeSend: function() {
              medicine_colomn.html("<option value=''><?php echo $this->lang->line('select') ?></option>");
            }, 
            success: function (res) {
                var div_data="<option value=''><?php echo $this->lang->line('select') ?></option>";
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
                    div_data += "<option value='" + obj.dosage + "'>" + obj.dosage +""+ obj.dosage +"</option>";
                });
                $("#search-dosage" + id).html("<option value=''><?php echo $this->lang->line('select') ?></option>");
                $('#search-dosage' + id).append(div_data); 
                $('#search-dosage' + id).select2("val", '');
            }
        });
    }    

	var prescription_rows=0;
    $(document).on('click','.add-record',function(){
            var rowCount = $('#tableID tr').length;
            if(rowCount==0){
                prescription_rows=1;
            }else{
                prescription_rows=rowCount+1;
            }
    
        var row = "<tr id='row" + prescription_rows + "'><td><input type='hidden' name='rows[]' value='"+prescription_rows+"' autocomplete='off'><input type='hidden' name='medicine_cat_"+prescription_rows+"' value='1'><select class='form-control select2 medicine_name' data-rowId='"+prescription_rows+"'  name='medicine_"+prescription_rows+"' id='search-query"+prescription_rows+"'><option value=''><?php echo $this->lang->line('select') ?></option><?php foreach ($medicineName as $mkey => $mvalue) { ?><option value='<?php echo $mvalue["id"]; ?>'><?php echo str_replace(["\r", "\n"], ' ', addslashes($mvalue["medicine_name"])); ?></option><?php } ?></select><small id='stock_info_"+prescription_rows+"'></small></td><td><select class='form-control select2 medicine_dosage'  name='dosage_"+prescription_rows+"' id='search-dosage"+prescription_rows+"'><option value=''><?php echo $this->lang->line('select'); ?></option><?php foreach ($dosage as $dkey => $dvalue) { ?><option value='<?php echo $dvalue["id"]; ?>'><?php echo str_replace(["\r", "\n"], ' ', addslashes($dvalue["dosage"] . ' (' . $dvalue["unit"] . ')')); ?></option><?php } ?></select></td><td><select class='form-control select2 interval_dosage'  name='interval_dosage_"+prescription_rows+"' id='search-interval-dosage"+prescription_rows+"'><option value='<?php echo set_value('interval_dosage_id'); ?>'><?php echo $this->lang->line('select'); ?></option><?php foreach ($intervaldosage as $dkey => $dvalue) { ?><option value='<?php echo $dvalue["id"]; ?>'><?php echo $dvalue["name"] ?></option><?php } ?></select></td><td><select class='form-control select2 duration_dosage'  name='duration_dosage_"+prescription_rows+"' id='search-duration-dosage"+prescription_rows+"'><option value='<?php echo set_value('duration_dosage_id'); ?>'><?php echo $this->lang->line('select') ?></option><?php foreach ($durationdosage as $dkey => $dvalue) { ?><option value='<?php echo $dvalue["id"]; ?>'><?php echo $dvalue["name"] ?></option><?php } ?></select></td><td><textarea name='instruction_"+prescription_rows+"' rows='1' class='form-control sh-instruction-textarea-2'></textarea></td><td class='text-center'><button type='button' data-row-id='"+prescription_rows+"' class='btn btn-sm btn-outline-danger closebtn delete_row_prescription'><i class='fa fa-remove'></i></button></td></tr>";
      $('#tableID').append(row).find('.select2').select2({ dropdownParent: $('#add_prescription') });
      
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
                                alert("<?php echo $this->lang->line('fail'); ?>")
                            }
                        });
                        window.location.reload(true);
                    }
                    $("#add_timelinebtn").btnReset();
                },
                error: function (e) {
                    alert("<?php echo $this->lang->line('fail'); ?>");
                    $("#add_timelinebtn").btnReset();
                    console.log(e);
                }
            });
        }));
    });

    function CalculateAgeInQCe(DOB, txtAge, Txndate) {
        if (DOB.value != '') {
            now = new Date(Txndate)
            var txtValue = DOB;

            if (txtValue != null)
                dob = txtValue.split('/');
            if (dob.length === 3) {
                born = new Date(dob[2], dob[1] * 1 - 1, dob[0]);
                if (now.getMonth() == born.getMonth() && now.getDate() == born.getDate()) {
                    age = now.getFullYear() - born.getFullYear();
                } else {
                    age = Math.floor((now.getTime() - born.getTime()) / (365.25 * 24 * 60 * 60 * 1000));
                }
                if (isNaN(age) || age < 0) {
                  
                } else {
                    if (now.getMonth() > born.getMonth()) {
                        var calmonth = now.getMonth() - born.getMonth();
                    } else {
                        var calmonth = born.getMonth() - now.getMonth();
                    }                   
                    $("#eage_year").val(age);
                    $("#eage_month").val(calmonth);
                    return age;                    
                }
            }
        }
    }

    $(document).ready(function () {
        $("#ebirth_date").change(function () {
            var mdate = $("#ebirth_date").val().toString();
            var yearThen = parseInt(mdate.substring(6, 10), 10);
            var dayThen = parseInt(mdate.substring(0, 2), 10);
            var monthThen = parseInt(mdate.substring(3, 5), 10);
            var DOB = dayThen + "/" + monthThen + "/" + yearThen;            
            CalculateAgeInQCe(DOB, '', new Date());
        });
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
                    alert("<?php echo $this->lang->line('fail'); ?>");
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
                            alert("<?php echo $this->lang->line('fail'); ?>")
                        }
                    }); 
                },
                error: function () {
                    alert("<?php echo $this->lang->line('fail'); ?>")
                }
            });
        }
    }
 
    function view_prescription(visitid) {
        $.ajax({
            url: '<?php echo base_url(); ?>admin/prescription/getPrescription/' + visitid ,
            success: function (res) {
                $("#getdetails_prescription").html(res);
            },
            error: function () {
                alert("<?php echo $this->lang->line('fail'); ?>")
            }
        });

        holdModal('prescriptionview');
    }

    function viewmanual_prescription(visitid) {
        $.ajax({
            url: '<?php echo base_url(); ?>admin/prescription/getPrescriptionmanual/' + visitid ,
            success: function (res) {
                $("#getdetails_prescriptionmanual").html(res);
                $('#edit_deleteprescriptionmanual').html("<?php if ($this->rbac->hasPrivilege('prescription', 'can_view')) { ?><a href='#'' class='btn btn-sm btn-light' data-bs-toggle='tooltip' onclick='printprescriptionmanual(" + visitid + ")'   title='<?php echo $this->lang->line('print'); ?>'><i class='fa fa-print'></i></a><?php } ?>");
            },
            error: function () {
                alert("<?php echo $this->lang->line('fail'); ?>")
            }
        });
        holdModal('prescriptionviewmanual');
    }
</script>

<script type="text/javascript">

    $(document).ready(function (e) {
        $('.select2').select2();
    });

    $(document).ready(function (e) {
        $("form#formrevisit button[type=submit]").click(function() {            
        $("button[type=submit]", $(this).parents("form")).removeAttr("clicked");
        $(this).attr("clicked", "true");
    });

        $("#formrevisit").on('submit', (function (e) {
              var sub_btn_clicked = $("button[type=submit][clicked=true]");                  
              var sub_btn_clicked_name=sub_btn_clicked.attr('name');
           
            e.preventDefault();
            $.ajax({
                url: '<?php echo base_url(); ?>admin/patient/add_revisit',
                type: "POST",
                data: new FormData(this),
                dataType: 'json',
                contentType: false,
                cache: false,
                processData: false,
                    beforeSend: function() {
                    sub_btn_clicked.btnLoading() ; 
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
                        $('.ajaxlistvisit').DataTable().ajax.reload();
                        shModal('revisitModal').hide();
                          if(sub_btn_clicked_name === "save_print") {                            
                           printVisitBill(data.id);
                        }       
                    }
                    $("#formrevisitbtn").btnReset();
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
                
                holdModal('myTimelineEditModal');

            },
        });
    }
    
    function renderRevisitAvatar(imageUrl, patientName) {
        var name = (patientName || '').replace(/\s*\([^)]*\)\s*/g, ' ').trim();
        var hasRealImage = imageUrl && imageUrl !== '' && imageUrl.indexOf('no_image') === -1;
        if (hasRealImage) {
            $("#patient_image").attr("src", "<?php echo base_url(); ?>" + imageUrl).removeClass('d-none');
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

    function getRevisitRecord(opdid) {
        var password = makeid(5);
        
        $('.select2-selection__rendered').html("");
        $.ajax({
            url: base_url+'admin/patient/getopdvisitdata',
            type: "POST",
            data: {opdid: opdid },
            dataType: 'json',
            success: function (data) {
                
                $("#revisit_id").val(data.id);
                $("#revisit_password").val(password);
                $("#listname").text(data.patients_name);
                $('#guardian').text(data.guardian_name);
                $('#rgender').text(data.gender);
                $("#revisit_contact").val(data.mobileno);
                $("#listnumber").text(data.mobileno);
                $("#pmobileno").val(data.mobileno);
                $("#appointment_date").val(data.appointment_date);
                $("#revisit_case").val(data.case_type);
                $("#pid").val(data.patientid);
                $("#revisit_allergies").val(data.known_allergies);
                $("#revisit_note").val(data.note);
                $("#revisit_refference").val(data.refference);
                $("#pemail").val(data.email);
                $("#remail").text(data.email);
                if (data.live_consult) {
                  $("#live_consultrevisit").val(data.live_consult);
                }

                renderRevisitAvatar(data.image, data.patients_name);
           
                $("#rage").text(data.patient_age);
                $("#revisit_month").val(data.month);
                $("#esymptoms").val(data.symptoms);
                $("#revisit_blood_group").val(data.blood_group);
                $("#rblood_group").text(data.blood_group_name);
                $("#revisi_tax").val(data.tax);
                $("#revisit_address").val(data.address);
                $("#raddress").text(data.address);
                $("#rmarital_status").text(data.marital_status);
                $("#any_known_allergies").text(data.any_known_allergies);
                $("#remarks").text(data.note);
                $("#rorganisation_name").val(data.organisation_name);
                $("#insurance_validity").val(data.insurance_validity);
                $("#insurance_id").val(data.insurance_id);
                $("#rorganisation").val(data.organisation_id);
                $("#tpa").text(data.organisation_name);
                $("#tpa_id").text(data.insurance_id);
                $("#tpa_validity").text(data.insurance_validity);
                $("#identification_number").text(data.identification_number);
                $("#consultant_doctor").select2("val", data.cons_doctor);
                $('select[id="revisit_old_patient"] option[value="' + data.old_patient + '"]').attr("selected", "selected");
                $('select[id="revisit_gender"] option[value="' + data.gender + '"]').attr("selected", "selected");
                $('select[id="revisit_marital_status"] option[value="' + data.marital_status + '"]').attr("selected", "selected");
                holdModal('revisitModal');
            },
        })
    }

    function printprescription(visitid) {
        var base_url = '<?php echo base_url() ?>';
        $.ajax({
            url: base_url + 'admin/prescription/printPrescription' ,
            type: 'GET',
            data: { visitid: visitid },
            dataType:"JSON",
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
    
    function holdModal(modalId) {
        (function(){var _el=document.getElementById(modalId); if(_el) bootstrap.Modal.getOrCreateInstance(_el,{backdrop:'static',keyboard:false}).show();})();;
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

    function deleteOpdPatientDiagnosis1(url, Msg) {
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

    var attr = {};

    $(document).on('select2:select','.charge_category',function(){
		var charge_category=$(this).val();      
		$('.charge').html("<option value=''><?php echo $this->lang->line('loading') ?></option>");
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
            }
        });
      }
    }

    $(document).on('input paste keyup','.apply_charge,.discount_percentage', function(e){ 
		update_amount($(e.target).closest('div.modal'));
	});

	function update_amount(object_model){   
        
        let  apply_charge= object_model.find('.apply_charge').val();
        let  discount_percentage=object_model.find('.discount_percentage').val();
        console.log(discount_percentage);
        let discount= (parseFloat(apply_charge) * discount_percentage/100);
		let price_with_discount=((parseFloat(apply_charge))-(parseFloat(apply_charge) * discount_percentage/100));
        let  tax_percentage=object_model.find('#percentage').val();
        
            if(tax_percentage !='' && tax_percentage !=0){
                 apply_amount=(parseFloat(price_with_discount) * tax_percentage/100)+price_with_discount;      
                 object_model.find('.apply_amount').val(apply_amount.toFixed(2));            
                 object_model.find('.paid_amount').val(apply_amount.toFixed(2));            
            }
    }    

    $(document).on('select2:select','.charge',function(){
        var charge=$(this).val();        
        var patient_id = $("#patient_id").val() ;
        let closetst_div=$(this).closest('div.modal');
        let is_tpa= $("input:checkbox[name=is_tpa]").prop('checked') ? 1 : 0;
      $.ajax({
            url: '<?php echo base_url(); ?>admin/patient/getChargeById',
            type: "POST",
            data: {charge_id: charge, 'patient_id':patient_id,'is_tpa':is_tpa},
            dataType: 'json',
            beforeSend: function() {
                 
            },
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
                    $('#apply_charge').val(parseFloat(res.result.standard_charge) * quantity);
                    $('#standard_charge').val(res.result.standard_charge);
                    $('#schedule_charge').val(res.result.org_charge);
                    $('#org_id').val(res.result.org_charge_id);

                    if(res.display_tpa_charge){
                        if(res.result.percentage ==null){
                            apply_amount=parseFloat(res.result.org_charge);
                        }else{
                            apply_amount=(parseFloat(res.result.org_charge) * res.result.percentage/100)+(parseFloat(res.result.org_charge));
                        }
                       
                        $('#apply_charge').val(res.result.org_charge);
                        $('#apply_amount').val(apply_amount.toFixed(2));
                        $('#paid_amount').val(apply_amount.toFixed(2));
                    }else {
                        if(res.result.percentage ==null){
                            apply_amount=parseFloat(res.result.standard_charge);
                        }else{
                            apply_amount=(parseFloat(res.result.standard_charge) * res.result.percentage/100)+(parseFloat(res.result.standard_charge));
                        }
                        
                        $('#apply_charge').val(res.result.standard_charge);
                        $('#apply_amount').val(apply_amount.toFixed(2));
                        $('#paid_amount').val(apply_amount.toFixed(2));
                       
                    } 
                } 
            }
        });
	}); 

    function get_Charges(orgid) {
        var charge =$('.charge').val();
        $.ajax({
            url: '<?php echo base_url(); ?>admin/patient/getChargeById',
            type: "POST",
            data: {charge_id: charge, organisation_id: orgid},
            dataType: 'json',
            beforeSend: function() {
                reset_form();
            },
            success: function (res) {
                if (res) {
                    $('#percentage').val(res.percentage);
                    if (orgid) {
                         if(res.percentage ==null){
                            apply_amount=parseFloat(res.org_charge);
                        }else{
                            apply_amount=(parseFloat(res.org_charge) * res.percentage/100)+(parseFloat(res.org_charge));
                        }                          
                        $('#apply_charge').val(res.org_charge);
                        $('#apply_amount').val(apply_amount);
                        $('#standard_charge').val(res.standard_charge);
                    } else {
                        if(res.percentage ==null){
                            apply_amount=parseFloat(res.standard_charge);
                        }else{
                            apply_amount=(parseFloat(res.standard_charge) * res.percentage/100)+(parseFloat(res.standard_charge));
                        }                     
                        $('#standard_charge').val(res.standard_charge);
                        $('#apply_charge').val(res.standard_charge);
                        $('#apply_amount').val(apply_amount);
                    }
                }else {
                    $('#standard_charge').val('');
                    $('#apply_charge').val('');
                }
            }
        });
    }

    function reset_form(){ 
        $('.charge_category').val('').trigger('change.select2');
        $('.charge ').val('').empty().trigger('change');
        $('#percentage').val("");        
        $('#standard_charge').val("");
        $('#schedule_charge').val("");                
        $('#org_id').val("");
        $('#org_charge_amount').val("");
        $('#apply_charge').val("");
        $('#apply_amount').val("");
        $('#paid_amount').val("")   
    }
    
    $(document).on('change','#is_tpa,#addpatient_id',function(){
        reset_form();
    });

    $(document).ready(function (e) {
 modal_click_disabled('viewModal');
        
        $("#add_bill").on('submit', (function (e) {
            if (confirm('Are you sure?')) {
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

                    },
                    error: function (e) {
                        alert("<?php echo $this->lang->line('fail'); ?>");
                        console.log(e);
                    }
                });
            } else {
                return false;
            }
        }));
    });

    $(document).ready(function (e) {
        $("#add_payment").on('submit', (function (e) {
            e.preventDefault();
            $("#add_paymentbtn").btnLoading();
            $.ajax({
                url: '<?php echo base_url(); ?>admin/payment/create',
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
                    $("#add_paymentbtn").btnReset();
                }, error: function () {}
            });
        }));
    });

    function calculate() {
        var totalopdcharges = $("#totalopdcharges").val();
        var total_payment = $("#total_payment").val();
        var total_amount = parseInt(totalopdcharges) - parseInt(total_payment);
        var discount = $("#discount").val();
        var other_charge = $("#other_charge").val();
        var tax = $("#tax").val();
        var gross_total = parseInt(total_amount) + parseInt(other_charge) + parseInt(tax);
        var net_amount = parseInt(total_amount) + parseInt(other_charge) + parseInt(tax) - parseInt(discount);
        $("#gross_total").val(gross_total);
        $("#net_amount").val(net_amount);
        $("#net_amount_span").html(net_amount);
        $("#save_button").show();
        $("#printBill").show();
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
            url: base_url + 'admin/payment/getOPDBill/',
            type: 'POST',
            data: {patient_id: patientid, ipdid: ipdid, total_amount: total_amount, discount: discount, other_charge: other_charge, gross_total: gross_total, tax: tax, net_amount: net_amount, status: status},
            success: function (result) {
                $("#testdata").html(result);
                popup(result);
            }
        });
    }

    function printVisitBill(opdid) {        
        $.ajax({
                url: base_url+'admin/patient/printbill',
                type: "POST",
                data: {opd_id: opdid},
                dataType: 'json',
                   beforeSend: function() {
            
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
    }

    function generateBill(id, amount) {
        $("#opdidhide").val(id);
        $("#totalopdcharges").val(amount);
        shModal("addBillModal").show();
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

$(".revisitpatient").click(function(){
    $('#formrevisit').trigger("reset"); 
    $('#select2-act-container').html("");   
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
   function getDosages(medicine_category_id){
    var dosage_opt="<option value=''><?php echo $this->lang->line('select') ?></option>";
   var sss='<?php echo json_encode($category_dosage); ?>';
   var aaa=JSON.parse(sss);
  
   if (aaa[medicine_category_id]){
    $.each(aaa[medicine_category_id], function(key, item) 
    {
      dosage_opt+="<option value='"+item.id+"'>"+item.dosage+" ("+item.unit+")</option>";
    });

}
return dosage_opt;
   }
</script> 

<script type="text/javascript">
    $(document).on('click','.move_opd',function(e){
            var data = $(this).data();
            var this_modal=$('#moveIPDModal');
            $('.title', this_modal).text(data.opdId);
            $('.btn-ok', this_modal).data('recordId', data.recordId);
        var btn= $(this);
             $.ajax({
                url: base_url+'admin/patient/moveIpdForm',
                type: "POST",
                data: {'visit_details_id':data.recordId},
                dataType: 'json',
               beforeSend: function () {
               btn.btnLoading();

                },
                success: function (data) {

                    if (data.status == "fail") {
                    var message = "";
                    $.each(data.error, function (index, value) {
                        message += value;
                    });
                    errorMsg(message);
                    } else {
                   $('.modal-body',this_modal).html(data.page);
                   $('.modal-body',this_modal).find('.select2').select2();

                    }
                  btn.btnReset();

                },
                error: function (xhr) { // if error occured
                    alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");
                     btn.btnReset();
                },
                complete: function () {
                     btn.btnReset();
                }
            });
            bootstrap.Modal.getOrCreateInstance(document.getElementById('moveIPDModal'), {backdrop:'static',keyboard:false}).show();
    });

       $("form#form_confirm-move").on('submit', (function (e) {
            e.preventDefault(); // avoid to execute the actual submit of the form.
            if(confirm('<?php echo $this->lang->line('are_you_sure_want_to_move_patient'); ?>')) {
            var btn = $(this).find("button[type=submit]:focus" );
            var move_opd_id=btn.data('recordId');
            var form = $(this);
            var url = form.attr('action');
            $.ajax({
                url: url,
                type: "POST",
                data: form.serialize(),
                dataType: 'json',
                beforeSend: function () {
                btn.btnLoading();
                },
                success: function (data) {
                 
                    var move_id = data.move_id ;
                    if (data.status == "fail") {
                    var message = "";
                    $.each(data.error, function (index, value) {
                        message += value;
                    });
                    errorMsg(message);
                    } else {
                       $('.ajaxlistvisit').DataTable().ajax.reload();
                 window.location.assign("<?php echo base_url(); ?>admin/patient/ipdprofile/"+move_id); 
                    }
                  btn.btnReset();

                },
                error: function (xhr) { // if error occured
                    alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");
                     btn.btnReset();
                },
                complete: function () {
                     btn.btnReset();
                }
            });
            }
        }));

    function getBed(bed_group, bed = '', active, htmlid = 'bed_no') {

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
                    div_data += "<option value=" + obj.id + ">" + obj.name + "</option>";
                });
                $("#" + htmlid).html("<option value=''><?php echo $this->lang->line('select') ?></option>");
                $('#' + htmlid).append(div_data);
                $("#" + htmlid).select2().select2('val', bed);
            }
        });
    }

    $(document).ready(function (e) {
        modal_click_disabled('add_prescription');
    });
</script>
<script type="text/javascript">
     $(document).ready(function () {       
           $('a[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
            $($.fn.dataTable.tables(true)).DataTable()
               .columns.adjust()
               .responsive.recalc();
            });   
      });      
</script>
<!-- //========datatable start===== -->
<script type="text/javascript">
( function ( $ ) {
    var id = "<?php echo $this->uri->segment(4); ?>"; 
    'use strict';
    $(document).ready(function () {
        initDatatable('ajaxlistvisit','admin/patient/getopdvisitdatatable/'+ id);
        initDatatable('treatmentlist','admin/patient/getopdtreatmenthistory/'+ id);      
    });
} ( jQuery ) )
</script>
<script>
    
    $(document).on('change', '.findingtype', function () {
        $this = $(this);
         var selected_id = $("#item_name").val();        
        var section_ul = $(this).closest('div.row').find('ul.section_ul');
        var finding_id = $(this).val();
        div_data="";
        $.ajax({
            type: 'POST',
            url: base_url + 'admin/patient/findingbycategory',
            data: {'finding_id': finding_id,'selected_id':selected_id},
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
             bootstrap.Modal.getOrCreateInstance(document.getElementById('viewDetailReportModal'), {backdrop:'static',keyboard:false}).show();
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

      $(document).ready(function(){
     $.ajax({
    url: baseurl +"admin/patient/yearchart",
    type: 'POST',
    data: {patient_id:'<?php echo $patient_id; ?>'},
    dataType: 'json',
    beforeSend: function() {
    
    }, 
    success: function(data) {
      var ctx = document.getElementById("medical-history-chart").getContext("2d");

    new Chart(ctx, {
    type: 'line',
    data: {
    labels:data.labels,
    datasets: data.dataset,
  }
});

    },
    error: function(xhr) { // if error occured
        alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");      
    },
    complete: function() {
      
    }
});
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
                ['compose-textareaneww', 'compose-textareass'].forEach(function(id) {
                    if (window.CKEDITOR && CKEDITOR.instances[id]) { try { CKEDITOR.instances[id].destroy(true); } catch(e) {} }
                });
                $('.modal-body',"#add_obstetric").html(res.page);
                $('.modal-body',"#add_obstetric").find('table').find('.select2').select2();
                $('.modal-body',"#add_obstetric").find('.multiselect2').select2({});
                $('.filestyle','#add_obstetric').dropify();
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
                 $('#edit_deletehistory').html("<a href='javascript:void(0)' class='btn btn-sm btn-light' data-bs-toggle='tooltip' onclick='printobstetrichistory(" + id + ")' title='<?php echo $this->lang->line('print'); ?>'><i class='fa fa-print'></i></a><a href='javascript:void(0)' class='btn btn-sm btn-light' data-bs-toggle='tooltip' onclick='editobstetric(" + id + ")' title='<?php echo $this->lang->line('edit'); ?>'><i class='fa fa-pencil'></i></a><a href='javascript:void(0)' class='btn btn-sm btn-light' data-bs-toggle='tooltip' onclick='delete_obstetric(" + id + ")' title='<?php echo $this->lang->line('delete'); ?>'><i class='fa fa-trash'></i></a>");
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
                ['compose-textareanew', 'compose-textareas'].forEach(function(id) {
                    if (window.CKEDITOR && CKEDITOR.instances[id]) { try { CKEDITOR.instances[id].destroy(true); } catch(e) {} }
                });
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
</script>

<script>
    function addantenatal(visitid,case_reference_id) {
        $("#antenatal_title").html('<?php echo $this->lang->line('add_antenatal_finding'); ?>');
        var patient_id = $("#patient_id").val();
		 
        var ipdid="";
      
         $.ajax({
            url: base_url+'admin/antenatal/addantenatalprescription',
            dataType:'JSON',
            data:{'visit_detail_id':visitid,'ipdid':ipdid,'patient_id':patient_id,'case_reference_id':case_reference_id},
            type:"POST",
             beforeSend: function() {
              },
               success: function (res) {
                ['compose-textareaneww', 'compose-textareass'].forEach(function(id) {
                    if (window.CKEDITOR && CKEDITOR.instances[id]) { try { CKEDITOR.instances[id].destroy(true); } catch(e) {} }
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
                /* picker init removed - auto-init via class + event delegation in footer.php */
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
        holdModal('findingview');
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
                /* picker init removed - auto-init via class + event delegation in footer.php */
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
      
        var msg = '<?php echo $this->lang->line('are_you_sure');?>';
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
	
	var total_rows_vitals=1;
	$(document).on('click','.add-vital-record',function(){		
		var table = document.getElementById("tableID_vitals");
        var id = total_rows_vitals+1;
        
        var div = "<td><input type='hidden' name='total_rows_vitals[]' value='" + id + "'><select name='vital_name_"+id+"' id='vital_name_"+id+"' class='form-control'><option value=''><?php echo $this->lang->line('select'); ?></option><?php foreach($vital_list as $row){ ?><option value='<?php echo $row['id']; ?>'><?php echo $row['name']; ?><?php if($row['reference_range']){ echo ' ('.$row['reference_range'] .' '. $row['unit'].')'; } ?></option><?php } ?></select></td><td><input name='from_reference_range_"+id+"' id='from_reference_range_"+id+"'  type='text' class='form-control'  /></td><td><input id='vital_date_"+id+"' name='vital_date_"+id+"'  type='text' class='form-control datetime'  /></td>";

        var row =  "<tr id='row_vitals" + id + "'>" + div + "<td class='text-center align-middle'><button type='button' data-row-id='"+id+"' class='closebtn delete_vitals_row btn btn-sm btn-outline-danger'><i class='fa fa-remove'></i></button></td></tr>";
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
                errorMsg("please_add_at_lease_one_vital --r");
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
                      $('.ajaxlist').DataTable().ajax.reload();
                      $("#formadd").trigger('reset');
                      shModal('myModal').hide();
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
                holdModal('myvitalEditModal');
            },
        });
    }   
    
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
</script>
<!-- //========datatable end===== -->
<link rel="stylesheet" href="<?php echo base_url(); ?>backend/dist/css/animate.min.css">
<script type="text/javascript">
    var patient_sidebar_loaded      = false;
    var patient_sidebar_cur_page    = 1;
    var patient_sidebar_total_pages = 1;
    var patient_current_id          = <?php echo (int) $id; ?>;
    var patient_sidebar_base_url    = '<?php echo base_url('admin/patient/profile/'); ?>';
    var patient_sidebar_ajax_url    = '<?php echo base_url('admin/patient/getpatientsidebarpage/'); ?>';
function patientSidebarEsc(s) { return $('<div>').text(s == null ? '' : s).html(); }

    function loadPatientSidebar(page) {
        var search = $('#patient_sidebar_search').val();
        $('#patient_sidebar_content').fadeTo(100, 0.3, function() {
            $.getJSON(patient_sidebar_ajax_url + page + '?search=' + encodeURIComponent(search), function(res) {
                patient_sidebar_cur_page    = res.page;
                patient_sidebar_total_pages = Math.ceil(res.total / res.per_page);

                if (res.total > 0) { $('#patient_sidebar_total').text(res.total).show(); } else { $('#patient_sidebar_total').hide(); }

                var html = '';
                if (!res.patients || res.patients.length === 0) {
                    html = '<div class="ss-empty"><i class="fa fa-user-slash"></i><div>No patients found</div></div>';
                } else {
                    html = '<ul class="ss-list">';
                    $.each(res.patients, function(i, p) {
                        var isActive = (p.patient_id == patient_current_id);
                        var name     = patientSidebarEsc(p.patient_name);
                        var pid      = patientSidebarEsc(p.patient_id);
                        var initial  = (p.patient_name || '?').charAt(0).toUpperCase();
                        var badgeHtml = p.last_opd_id
                            ? '<span class="ss-badge">' + (res.opd_prefix || '') + p.last_opd_id + '</span>'
                            : '';
                        html += '<li class="ss-item' + (isActive ? ' active' : '') + '">';
                        html += '<a class="ss-link" href="' + patient_sidebar_base_url + p.patient_id + '/1" title="' + name + ' (' + pid + ')">';
                        html +=   '<div class="ss-avatar">' + initial + '</div>';
                        html +=   '<div class="ss-info">';
                        html +=     '<div class="ss-name">' + name + ' (' + pid + ')</div>';
                        html +=   '</div>';
                        html += badgeHtml;
                        html += '</a></li>';
                    });
                    html += '</ul>';
                }

                $('#patient_sidebar_content').html(html).fadeTo(200, 1);

                if (res.total > 0) {
                    $('#patient_sidebar_pageinfo').text(page + '/' + patient_sidebar_total_pages);
                    $('#patient_sidebar_prev').prop('disabled', page <= 1);
                    $('#patient_sidebar_next').prop('disabled', page >= patient_sidebar_total_pages);
                    $('#patient_sidebar_pagination').show();
                } else {
                    $('#patient_sidebar_pagination').hide();
                }
            }).fail(function() {
                $('#patient_sidebar_content').html('<div class="ss-empty text-danger"><i class="fa fa-exclamation-circle"></i><div>Failed to load</div></div>').fadeTo(200, 1);
            });
        });
    }

    $(document).ready(function () {
        if (!patient_sidebar_loaded) {
            patient_sidebar_loaded = true;
            loadPatientSidebar(1);
        }

        $(document).on('click', '#patient_sidebar_prev', function() {
            if (patient_sidebar_cur_page > 1) loadPatientSidebar(patient_sidebar_cur_page - 1);
        });

        $(document).on('click', '#patient_sidebar_next', function() {
            if (patient_sidebar_cur_page < patient_sidebar_total_pages) loadPatientSidebar(patient_sidebar_cur_page + 1);
        });

        var patient_search_timer;
        $(document).on('keyup', '#patient_sidebar_search', function() {
            clearTimeout(patient_search_timer);
            patient_search_timer = setTimeout(function() {
                loadPatientSidebar(1);
            }, 400);
        });

        // ICD-10: group filter for Add Visit form
        $('#opd_icd10_group_filter').on('change', function () {
            var group_id = $(this).val();
            $.post('<?php echo base_url(); ?>admin/icd10/get_codes_by_group', { group_id: group_id }, function (data) {
                var $select = $('#opd_icd_code_ids');
                $select.empty();
                $.each(data, function (i, c) {
                    $select.append('<option value="' + c.id + '">[' + c.icd_code + '] ' + c.icd_description + '</option>');
                });
                $select.trigger('change');
            });
        });

        // ICD-10: group filter for Edit Visit form
        $('#opd_edit_icd10_group_filter').on('change', function () {
            var group_id = $(this).val();
            $.post('<?php echo base_url(); ?>admin/icd10/get_codes_by_group', { group_id: group_id }, function (data) {
                var $select = $('#opd_edit_icd_code_ids');
                $select.empty();
                $.each(data, function (i, c) {
                    $select.append('<option value="' + c.id + '">[' + c.icd_code + '] ' + c.icd_description + '</option>');
                });
                $select.trigger('change');
            });
        });

        // ICD-10: pre-fill saved codes when OPD edit visit modal opens
        function loadOpdIcdCodes(opd_id) {
            $.get('<?php echo base_url(); ?>admin/patient/get_opd_icd_codes/' + opd_id, function (data) {
                var $select = $('#opd_edit_icd_code_ids');
                var saved = data.map(function (c) { return String(c.id); });
                $select.find('option').each(function () {
                    $(this).prop('selected', saved.indexOf($(this).val()) !== -1);
                });
                $select.trigger('change');
            });
        }
    });
</script>
