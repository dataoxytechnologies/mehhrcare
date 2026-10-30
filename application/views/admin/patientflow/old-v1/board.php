<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<style>
    .pf-visit-badge { min-width: 96px; font-weight: 600; letter-spacing: .3px; }
    .pf-token { font-weight: 700; font-size: 0.95rem; }
</style>

<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
        <div class="d-flex align-items-center gap-2">
            <i class="fa fa-users sh-icon-soft"></i>
            <h3 class="card-title titlefix mb-0"><?php echo $this->lang->line('patient_flow'); ?></h3>
        </div>
        <div class="d-flex gap-2 align-items-center flex-wrap">
            <?php if ($this->rbac->hasPrivilege('patient_flow', 'can_add')) { ?>
            <a href="<?php echo site_url('admin/patientflow/register'); ?>" class="btn btn-sm btn-primary">
                <i class="fa fa-plus"></i> <?php echo $this->lang->line('add_patient'); ?>
            </a>
            <?php } ?>
        </div>
    </div>

    <?php if (!$pf_is_superadmin) { ?>
    <div class="card-body border-bottom py-2">
        <div class="d-flex align-items-center flex-wrap gap-2 small">
            <i class="fa fa-shield-alt text-primary"></i>
            <span><?php echo $this->lang->line('showing_my_levels'); ?></span>
            <?php if (empty($pf_my_levels)) { ?>
                <span class="badge bg-danger-subtle text-danger-emphasis"><?php echo $this->lang->line('no_levels_assigned'); ?></span>
            <?php } else { ?>
                <?php foreach ($pf_my_levels as $ml) { ?>
                    <span class="badge bg-primary-subtle text-primary-emphasis"><?php echo $ml->code . ' — ' . $ml->title; ?></span>
                <?php } ?>
            <?php } ?>
        </div>
    </div>
    <?php } ?>

    <div class="card-body border-bottom">
        <form method="get" action="<?php echo site_url('admin/patientflow'); ?>" class="row g-3 align-items-end">
            <div class="col-md-4 col-lg-3">
                <label class="form-label form-label-sm"><?php echo $this->lang->line('filter_level'); ?></label>
                <select name="level" id="filter_level" class="form-select">
                    <option value=""><?php echo $this->lang->line('all_levels'); ?></option>
                    <?php foreach ($levels as $l) { ?>
                    <option value="<?php echo $l->id; ?>" <?php if ((string)$filter_level === (string)$l->id) { echo 'selected'; } ?>>
                        <?php echo $l->code . ' — ' . $l->title; ?>
                    </option>
                    <?php } ?>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-outline-primary btn-sm">
                    <i class="fa fa-filter"></i> <?php echo $this->lang->line('filter'); ?>
                </button>
                <?php if ($filter_level) { ?>
                <a href="<?php echo site_url('admin/patientflow'); ?>" class="btn btn-outline-secondary btn-sm">
                    <i class="fa fa-times"></i>
                </a>
                <?php } ?>
            </div>
        </form>
    </div>

    <div class="card-body table-responsive">
        <table class="table table-striped table-bordered table-hover" id="pfBoardTable">
            <thead>
                <tr>
                    <th><?php echo $this->lang->line('visit_no'); ?></th>
                    <th><?php echo $this->lang->line('patient'); ?></th>
                    <th><?php echo $this->lang->line('pbhp_id'); ?></th>
                    <th><?php echo $this->lang->line('consultation_category'); ?></th>
                    <th class="text-center"><?php echo $this->lang->line('current_level'); ?></th>
                    <th><?php echo $this->lang->line('payment'); ?></th>
                    <th class="text-center"><?php echo $this->lang->line('approval_status') ?: 'Approval'; ?></th>
                    <th class="text-end text-nowrap"><?php echo $this->lang->line('action'); ?></th>
                </tr>
            </thead>
            <tbody>
<?php foreach ($visits as $v) { ?>
                            <tr>
                                <td class="align-middle">
                                    <span class="pf-token"><?php echo $v->visit_no; ?></span>
                                    <?php if ($v->token_no) { ?>
                                    <div class="small text-muted"><?php echo $this->lang->line('token'); ?>: <?php echo $v->token_no; ?></div>
                                    <?php } ?>
                                </td>
                                <td class="align-middle">
                                    <strong><?php echo $v->patient_name; ?></strong>
                                    <div class="small text-muted">
                                        <?php
                                        echo ($v->gender ? $v->gender . ' · ' : '') . ($v->age ? $v->age . 'Y' : '');
                                        if ($v->mobileno) { echo ' · ' . $v->mobileno; }
                                        ?>
                                    </div>
                                </td>
                                <td class="align-middle"><?php echo ($v->level_no >= 3 && $v->pbhp_id) ? '<span class="badge bg-info-subtle text-info-emphasis">' . $v->pbhp_id . '</span>' : '<span class="text-muted">—</span>'; ?></td>
                                <td class="align-middle"><?php echo $v->consultation_category ?: '<span class="text-muted">—</span>'; ?></td>
                                <td class="align-middle text-center">
                                    <span class="badge bg-primary-subtle text-primary-emphasis">
                                        <?php echo $v->level_code ? $v->level_code : '—'; ?>
                                    </span>
                                    <div class="small text-muted"><?php echo $v->level_title; ?></div>
                                </td>
                                <td class="align-middle">
                                    <?php
                                    $pf_paid = ($v->payment_status == 'paid');
                                    $pf_pay_badge_class = $pf_paid ? 'bg-success-subtle text-success-emphasis' : 'bg-warning-subtle text-warning-emphasis';
                                    $pf_pay_label = $this->lang->line($pf_paid ? 'paid' : 'pending');
                                    if ($pf_is_cashier) { ?>
                                        <a href="<?php echo site_url('admin/patientflow/toggle_payment/' . $v->id); ?>"
                                           class="badge <?php echo $pf_pay_badge_class; ?>" style="text-decoration:none;cursor:pointer;"
                                           data-bs-toggle="tooltip" title="<?php echo $this->lang->line('toggle_payment') ?: 'Click to toggle payment status'; ?>">
                                            <?php echo $pf_pay_label; ?>
                                        </a>
                                    <?php } else { ?>
                                        <span class="badge <?php echo $pf_pay_badge_class; ?>"><?php echo $pf_pay_label; ?></span>
                                    <?php } ?>
                                </td>
                                <td class="align-middle text-center">
                                    <?php if ($v->approve_status == 'approved') { ?>
                                        <span class="badge bg-success-subtle text-success-emphasis"><i class="fa fa-check"></i> <?php echo $this->lang->line('approved') ?: 'Approved'; ?></span>
                                    <?php } else { ?>
                                        <span class="badge bg-secondary-subtle text-secondary-emphasis"><?php echo $this->lang->line('unapproved') ?: 'Unapproved'; ?></span>
                                    <?php } ?>
                                </td>
                                <td class="align-middle text-end text-nowrap">
                                    <?php
                                    // Ownership gate: only the role owning this visit's current
                                    // level may forward it (superadmin bypasses).
                                    $pf_owned = false;
                                    if ($pf_is_superadmin) {
                                        $pf_owned = true;
                                    } else {
                                        foreach ($pf_my_levels as $ml) {
                                            if ((int) $ml->id === (int) $v->current_level_id) { $pf_owned = true; break; }
                                        }
                                    }
                                    ?>
                                    <?php if ($v->next_level_id && $pf_owned) { ?>
                                    <?php if ((int) $v->level_no === 2) { ?>
                                    <button type="button" class="btn btn-sm btn-primary w-100 mb-1" data-bs-toggle="tooltip"
                                       title="<?php echo $this->lang->line('forward_to'); ?> <?php echo $v->next_title; ?>"
                                       data-cc-visit="<?php echo (int) $v->id; ?>"
                                       data-cc-name="<?php echo htmlspecialchars((string) $v->consultation_category); ?>"
                                       data-cc-default="<?php echo isset($v->category_charge) ? htmlspecialchars((string) $v->category_charge) : ''; ?>"
                                       onclick="pfPrefillCharge(this); holdModal('pfChargeModal');">
                                        <i class="fa fa-arrow-right"></i> &nbsp;<?php echo $this->lang->line('forward'); ?>
                                    </button>
                                    <?php } else { ?>
                                    <a href="<?php echo site_url('admin/patientflow/forward/' . $v->id); ?>"
                                       class="btn btn-sm btn-primary w-100 mb-1" data-bs-toggle="tooltip"
                                       title="<?php echo $this->lang->line('forward_to'); ?> <?php echo $v->next_title; ?>">
                                        <i class="fa fa-arrow-right"></i> &nbsp;<?php echo $this->lang->line('forward'); ?>
                                    </a>
                                    <?php } ?>
                                    <?php } elseif (!$v->next_level_id) { ?>
                                        <span class="badge bg-success-subtle text-success-emphasis mb-1 d-block"><?php echo $this->lang->line('completed'); ?></span>
                                    <?php } else { ?>
                                        <span class="text-muted small d-block mb-1"><i class="fa fa-lock"></i></span>
                                    <?php } ?>
                                    <?php if ($v->prev_level_id && $pf_owned) { ?>
                                    <a href="<?php echo site_url('admin/patientflow/backward/' . $v->id); ?>"
                                       class="btn btn-sm btn-outline-warning w-100 mb-1" data-bs-toggle="tooltip"
                                       title="<?php echo $this->lang->line('back_to') ?: 'Send back to'; ?> <?php echo $v->prev_title ? $v->prev_title : 'previous step'; ?>">
                                        <i class="fa fa-arrow-left"></i> &nbsp;<?php echo $this->lang->line('back') ?: 'Back'; ?>
                                    </a>
                                    <?php } ?>
                                    <?php if ($v->level_can_approve && $pf_owned && $v->approve_status != 'approved') { ?>
                                    <a href="<?php echo site_url('admin/patientflow/approve/' . $v->id); ?>"
                                       class="btn btn-sm btn-outline-success w-100 mb-1" data-bs-toggle="tooltip"
                                       title="<?php echo $this->lang->line('approve'); ?>">
                                        <i class="fa fa-check"></i> &nbsp;<?php echo $this->lang->line('approve'); ?>
                                    </a>
                                    <?php } ?>
                                    <?php if (($v->level_code == 'L5') && $pf_owned) { ?>
                                    <a href="javascript:void(0)" class="btn btn-sm btn-primary w-100 mb-1"
                                       data-bs-toggle="tooltip"
                                       title="<?php echo $this->lang->line('save_opd') ?: 'Save OPD'; ?>"
                                       onclick="pfSetOpdPatient(<?php echo (int)$v->patient_id; ?>, '<?php echo addslashes((string)$v->patient_name); ?>'); holdModal('myModal');">
                                        <i class="fa fa-plus"></i> &nbsp;<?php echo $this->lang->line('save_opd') ?: 'Save OPD'; ?>
                                    </a>
                                    <?php } ?>
                                    <button class="btn btn-sm btn-outline-info w-100" type="button"
                                            data-bs-toggle="collapse" data-bs-target="#pfLogs<?php echo $v->id; ?>"
                                            aria-expanded="false" aria-controls="pfLogs<?php echo $v->id; ?>">
                                        <i class="fa fa-history"></i> &nbsp;<?php echo $this->lang->line('history') ?: 'History'; ?>
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="8" class="p-0 border-0" style="padding: 0px !important;">
                                    <div class="collapse" id="pfLogs<?php echo $v->id; ?>">
                                        <div class="p-3 bg-body-tertiary border-top small">
                                            <strong><?php echo $this->lang->line('visit_history') ?: 'Visit history'; ?></strong>
                                            <?php
                                            $logs = isset($visit_logs[$v->id]) ? $visit_logs[$v->id] : array();
                                            if (!$logs) { echo '<div class="text-muted mt-1">' . ($this->lang->line('no_logs_yet') ?: 'No steps yet.') . '</div>'; }
                                            else { ?>
                                            <ul class="list-unstyled mb-0 mt-2">
                                                <?php foreach ($logs as $lg) { ?>
                                                <li class="d-flex align-items-center gap-2 mb-1">
                                                    <i class="fa fa-circle text-success" style="font-size:8px;"></i>
                                                    <span class="text-muted text-nowrap"><?php echo date('d-M Y H:i', strtotime($lg->created_at)); ?></span>
                                                    <span class="badge bg-primary-subtle text-primary-emphasis text-uppercase"><?php echo str_replace('_', ' ', htmlspecialchars($lg->action)); ?></span>
                                                    <?php if ($lg->from_code || $lg->to_code) { ?>
                                                    <span>
                                                        <?php echo $lg->from_code ? $lg->from_code : '—'; ?>
                                                        <i class="fa fa-long-arrow-right text-muted"></i>
                                                        <?php echo $lg->to_code ? $lg->to_code : '—'; ?>
                                                    </span>
                                                    <?php } ?>
                                                    <?php if ($lg->acted_by_name) { ?>
                                                    <span class="text-muted">· <?php echo htmlspecialchars($lg->acted_by_name); ?></span>
                                                    <?php } ?>
                                                </li>
                                                <?php } ?>
                                            </ul>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php } ?>
                        <?php if (empty($visits)) { ?>
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4"><?php echo $this->lang->line('no_patients_registered'); ?></td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div><div class="modal fade sh-modal sh-modal-nospace" id="myModal" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="formadd" accept-charset="utf-8" action="<?php echo base_url() . "admin/patient" ?>" enctype="multipart/form-data" method="post">
                <div class="modal-header">
                    <h5 class="modal-title" id="myModalLabel"><?php echo $this->lang->line('new_visit'); ?></h5>
                    <select onchange="get_PatientDetails(this.value)" class="form-control patient_list_ajax sh-inp-200" <?php
                            if ($disable_option == true) { echo "disabled"; }
                            ?> name='' id="addpatient_id">
                    </select>
                    <?php if ($this->rbac->hasPrivilege('patient', 'can_add')) { ?>
                        <a onclick="holdModal('myModalpa')" class="modalbtnpatient btn btn-light btn-sm text-nowrap" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('new_patient'); ?>"><i class="fa fa-plus"></i> <?php echo $this->lang->line('new_patient'); ?></a>
                    <?php } ?>
                </div>
                <div class="pup-scroll-area">
                <div class="modal-body">
                                <input name="patient_id" id="patient_id" type="hidden" class="form-control" />
                                <input name="email" id="pemail" type="hidden" class="form-control" />
                                <input name="mobileno" id="mobnumber" type="hidden" class="form-control" />
                                <input name="patient_name" id="patientname" type="hidden" class="form-control" />
                                <input name="password" id="password" type="hidden" class="form-control" />
                                <input name="organisation_id" id="organisation_id" type="hidden" class="form-control" />
                                <input name="insurance_validity" id="insurance_validity" type="hidden" class="form-control" />
                                <input name="insurance_id" id="insuranceid" type="hidden" class="form-control" />
                                <div class="row">
                                    <div class="col-lg-8 col-md-8 col-sm-8">
                                        <div id="ajax_load"></div>
                                        <div class="sh-form-card mb-2" id="patientDetails" class="d-none">
                                            <div class="sh-card-header">
                                                <span class="sh-card-header-title" id="listname"></span>
                                            </div>
                                            <div class="d-flex align-items-start">
                                                <div class="sh-info-grid flex-grow-1">
                                                <div class="row g-0">
                                                    <div class="sh-info-item col-6 col-md-3" id="li_guardian">
                                                        <small class="sh-info-label"><i class="fas fa-user-secret"></i> <?php echo $this->lang->line('guardian'); ?></small>
                                                        <span class="sh-info-value" id="guardian"></span>
                                                    </div>
                                                    <div class="sh-info-item col-6 col-md-3" id="li_gender">
                                                        <small class="sh-info-label"><i class="fas fa-venus-mars"></i> <?php echo $this->lang->line('gender'); ?></small>
                                                        <span class="sh-info-value" id="genders"></span>
                                                    </div>
                                                    <div class="sh-info-item col-6 col-md-3" id="li_blood">
                                                        <small class="sh-info-label"><i class="fas fa-tint"></i> <?php echo $this->lang->line('blood_group'); ?></small>
                                                        <span class="sh-info-value" id="blood_group"></span>
                                                    </div>
                                                    <div class="sh-info-item col-6 col-md-3" id="li_marital">
                                                        <small class="sh-info-label"><i class="fas fa-ring"></i> <?php echo $this->lang->line('marital_status'); ?></small>
                                                        <span class="sh-info-value" id="marital_status"></span>
                                                    </div>
                                                </div>
                                                <div class="sh-row-divider row g-0">
                                                    <div class="sh-info-item col-6 col-md-3" id="li_age">
                                                        <small class="sh-info-label"><i class="fas fa-hourglass-half"></i> <?php echo $this->lang->line('age'); ?></small>
                                                        <span class="sh-info-value" id="age"></span>
                                                    </div>
                                                    <div class="sh-info-item col-6 col-md-3" id="li_phone">
                                                        <small class="sh-info-label"><i class="fa fa-phone-square"></i> <?php echo $this->lang->line('phone'); ?></small>
                                                        <span class="sh-info-value" id="listnumber"></span>
                                                    </div>
                                                    <div class="sh-info-item col-6 col-md-3" id="li_email">
                                                        <small class="sh-info-label"><i class="fa fa-envelope"></i> <?php echo $this->lang->line('email'); ?></small>
                                                        <span class="sh-info-value" id="email"></span>
                                                    </div>
                                                    <div class="sh-info-item col-6 col-md-3" id="li_address">
                                                        <small class="sh-info-label"><i class="fas fa-street-view"></i> <?php echo $this->lang->line('address'); ?></small>
                                                        <span class="sh-info-value" id="address"></span>
                                                    </div>
                                                </div>
                                                <div class="sh-row-divider row g-0">
                                                    <div class="sh-info-item col-6 col-md-3" id="li_tpa">
                                                        <small class="sh-info-label"><i class="fas fa-shield-alt"></i> <?php echo $this->lang->line('tpa'); ?></small>
                                                        <span class="sh-info-value" id="organisation_name"></span>
                                                    </div>
                                                    <div class="sh-info-item col-6 col-md-3" id="li_tpa_id">
                                                        <small class="sh-info-label"><i class="fas fa-id-badge"></i> <?php echo $this->lang->line('tpa_id'); ?></small>
                                                        <span class="sh-info-value" id="insurance_id"></span>
                                                    </div>
                                                    <div class="sh-info-item col-6 col-md-3" id="li_validity">
                                                        <small class="sh-info-label"><i class="fas fa-calendar-check"></i> <?php echo $this->lang->line('tpa_validity'); ?></small>
                                                        <span class="sh-info-value" id="validity"></span>
                                                    </div>
                                                    <div class="sh-info-item col-6 col-md-3" id="li_national_id">
                                                        <small class="sh-info-label"><i class="fas fa-fingerprint"></i> <?php echo $this->lang->line('national_identification_number'); ?></small>
                                                        <span class="sh-info-value" id="identification_number"></span>
                                                    </div>
                                                </div>
                                                <div class="sh-row-divider row g-0">
                                                    <div class="sh-info-item col-6 col-md-3" id="li_allergies">
                                                        <small class="sh-info-label"><i class="fas fa-allergies"></i> <?php echo $this->lang->line('any_known_allergies'); ?></small>
                                                        <span class="sh-info-value" id="allergies"></span>
                                                    </div>
                                                    <div class="sh-info-item col-6 col-md-3" id="li_note">
                                                        <small class="sh-info-label"><i class="fas fa-sticky-note"></i> <?php echo $this->lang->line('remarks'); ?></small>
                                                        <span class="sh-info-value" id="note"></span>
                                                    </div>
                                                    <div class="sh-info-item col-6 col-md-3 d-none" id="show_barcode">
                                                        <small class="sh-info-label"><i class="fa fa-barcode"></i> <?php echo $this->lang->line('barcode'); ?></small>
                                                        <span><img id="getbarcode" class="pd-barcode-img" alt="" /></span>
                                                    </div>
                                                    <div class="sh-info-item col-6 col-md-3 d-none" id="show_qrcode">
                                                        <small class="sh-info-label"><i class="fa fa-qrcode"></i> <?php echo $this->lang->line('qrcode'); ?></small>
                                                        <span><img id="getqrcode" class="pd-qrcode-img" alt="" /></span>
                                                    </div>
                                                </div>
                                            </div>
                                                <div class="opd-pd-photo-cell">
                                                    <img class="opd-pd-photo-lg" src="<?php echo $this->media_storage->getImageURL('uploads/patient_images/no_image.png'); ?>" id="image" alt="<?php echo $this->lang->line('patient'); ?>">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="sh-form-card mb-2">
                                            <div class="sh-card-header">
                                                <span class="sh-card-header-title"><i class="fas fa-stethoscope"></i> <?php echo $this->lang->line('symptoms'); ?></span>
                                            </div>
                                            <div class="p-2"><div class="row">
                                            <div class="col-sm-3 col-6">
                                                <div class="mb-3">
                                                    <label for="exampleInputFile">
                                                        <?php echo $this->lang->line('symptoms_type') ; ?></label>
                                                    <div>
                                                        <select  name='symptoms_type'  id="act"  class="form-control select2 act w-100"   multiple >
                                                            <option value=""><?php echo $this->lang->line('select') ?></option>
                                                            <?php foreach ($symptomsresulttype as $dkey => $dvalue) {
                                                                ?>
                                                            <option value="<?php echo $dvalue["id"]; ?>"><?php echo $dvalue["symptoms_type"] ;?></option>
                                                        <?php } ?>
                                                        </select>
                                                    </div>
                                                    <span class="text-danger"><?php echo form_error('symptoms_type'); ?></span>
                                                </div>
                                            </div>                                            
                                            <input name="rows[]" type="hidden" value="1">
                                            <div class="col-sm-3 col-6">
                                                <div class="mb-3">
                                                    <label for="exampleInputFile"><?php echo $this->lang->line('symptoms_title'); ?></label>
                                                    <div id="dd" class="wrapper-dropdown-3">
                                                        <input class="form-control filterinput" type="text">
                                                        <ul class="dropdown scroll150 section_ul">
                                                            <li class="section-placeholder"><span><?php echo $this->lang->line('select') ; ?></span></li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-6 col-12">
                                                <div class="mb-3">
                                                    <label><?php echo $this->lang->line('symptoms_description'); ?></label>
                                                    <textarea class="form-control" id="esymptoms" name="symptoms" rows="3" ></textarea>
                                                </div>
                                            </div>
                                            <div class="col-sm-3 col-6">
                                                <div class="mb-3">
                                                    <label><?php echo $this->lang->line('icd10_group'); ?></label>
                                                    <select id="opd_icd10_group_filter" class="form-control select2 w-100" >
                                                        <option value=""><?php echo $this->lang->line('select_group'); ?></option>
                                                        <?php if (!empty($icd10_groups)) { foreach ($icd10_groups as $g) { ?>
                                                        <option value="<?php echo $g['id']; ?>"><?php echo html_escape($g['group_name']); ?></option>
                                                        <?php } } ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-sm-9 col-12">
                                                <div class="mb-3">
                                                    <label><?php echo $this->lang->line('icd10_diagnosis'); ?></label>
                                                    <select name="icd_code_ids[]" id="opd_icd_code_ids" class="form-control select2 w-100" multiple >
                                                        <?php if (!empty($icd10_codes)) { foreach ($icd10_codes as $c) { ?>
                                                        <option value="<?php echo $c['id']; ?>">[<?php echo html_escape($c['icd_code']); ?>] <?php echo html_escape($c['icd_description']); ?></option>
                                                        <?php } } ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-sm-6 col-12">
                                                <div class="mb-3">
                                                    <label for="pwd"><?php echo $this->lang->line('note'); ?></label>
                                                    <textarea name="note" rows="3" class="form-control" ><?php echo set_value('note'); ?></textarea>
                                                </div> 
                                            </div>
                                            <div class="col-sm-6 col-12">
                                                <div class="mb-3">
                                                    <label for="email"><?php echo $this->lang->line('any_known_allergies'); ?></label>
                                                    <textarea name="known_allergies"  rows="3" id="eknown_allergies" placeholder="" class="form-control"><?php echo set_value('address'); ?></textarea>
                                                </div> 
                                            </div>
                                            <div class="col-sm-12 col-12">
                                                <div class="mb-3">
                                                    <?php
                                                    echo display_custom_fields('opd');
                                                    ?>
                                                </div>
                                            </div>
                                            </div></div>
                                        </div>
                                    </div><!--./col-md-8-->
                                    <div class="col-lg-4 col-md-4 col-sm-4 ptt10">
                                        <div class="sh-form-card mb-3">
                                            <div class="sh-card-header">
                                                <span class="sh-card-header-title"><i class="fas fa-calendar-alt"></i> <?php echo $this->lang->line('visit_details'); ?></span>
                                            </div>
                                            <div class="p-2"><div class="row">
                                            <div class="col-sm-6">
                                                <div class="mb-3">
                                                    <label><?php echo $this->lang->line('appointment_date'); ?> <small class="req"> *</small></label>
                                                    <input id="datetimepicker" name="appointment_date" type="text" class="form-control datetime" />
                                                    <span class="text-danger"><?php echo form_error('appointment_date'); ?></span>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="mb-3">
                                                    <label for="exampleInputFile">
                                                    <?php echo $this->lang->line('case'); ?></label>
                                                    <div><input class="form-control" type='text' name='case' />
                                                    </div>
                                                    <span class="text-danger"><?php echo form_error('case'); ?></span>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="mb-3">
                                                    <label for="exampleInputFile"><?php echo $this->lang->line('casualty'); ?></label>
                                                    <div>
                                                        <select name="casualty" id="casualty" class="form-control">
                                                        <?php foreach ($yesno_condition as $yesno_key => $yesno_value) {
                                                            ?>
                                                            <option value="<?php echo $yesno_key ?>" <?php
                                                                    if ($yesno_key == 'no') {
                                                                        echo "selected";
                                                                    }
                                                                    ?> ><?php echo $yesno_value ?>
                                                            </option>
                                                            <?php } ?>
                                                        </select>                                                        
                                                    </div>
                                                    <span class="text-danger"><?php echo form_error('case'); ?></span>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="mb-3">
                                                    <label for="exampleInputFile">
                                                    <?php echo $this->lang->line('old_patient'); ?></label>
                                                    <div>
                                                        <select name="old_patient" id="" class="form-control">
                                                        <?php foreach ($yesno_condition as $yesno_key => $yesno_value) {
                                                            ?>
                                                            <option value="<?php echo $yesno_key ?>" <?php
                                                                    if ($yesno_key == 'no') {
                                                                        echo "selected";
                                                                    }
                                                                    ?> ><?php echo $yesno_value ?>
                                                            </option>
                                                            <?php } ?>
                                                        </select>
                                                    </div>
                                                    <span class="text-danger"><?php echo form_error('case'); ?></span>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="mb-3">
                                                    <label for="exampleInputFile">
                                                    <?php echo $this->lang->line('reference'); ?></label>
                                                    <div>
                                                        <input class="form-control" type='text' name='refference' />
                                                    </div>
                                                    <span class="text-danger"><?php echo form_error('refference'); ?></span>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="mb-3">
                                                    <label class="d-block">&nbsp;</label>
                                                    <div class="form-check mt-1">
                                                        <input class="form-check-input apply_tpa" id="is_tpa" name="is_tpa" type="checkbox" value="1" autocomplete="off">
                                                        <label class="form-check-label" for="is_tpa"><?php echo $this->lang->line('apply_tpa'); ?></label>
                                                    </div>
                                                </div>
                                            </div>
                                            </div></div>
                                        </div>
                                        <div class="sh-form-card mb-3">
                                            <div class="sh-card-header">
                                                <span class="sh-card-header-title"><i class="fas fa-user-md"></i> <?php echo $this->lang->line('consultant_doctor'); ?> &amp; <?php echo $this->lang->line('charges'); ?></span>
                                            </div>
                                            <div class="p-2"><div class="row">
                                            <div class="col-sm-6">
                                                <div class="mb-3">
                                                    <label for="exampleInputFile"><?php echo $this->lang->line('consultant_doctor'); ?> <small class="req"> *</small></label>
                                                    <div>
                                                        <select name='consultant_doctor' id="consultant_doctor" class="form-control select2" <?php
                                                            if ($disable_option == true) { echo "disabled";  }   ?> class="w-100"  >
                                                            <option value=""><?php echo $this->lang->line('select') ?></option>
                                                            <?php foreach ($doctors as $dkey => $dvalue) {   ?>
                                                                <option value="<?php echo $dvalue["id"]; ?>" <?php
                                                                        if ((isset($doctor_select)) && ($doctor_select == $dvalue["id"])) {
                                                                            echo "selected";
                                                                        }
                                                                        ?>><?php echo $dvalue["name"] . " " . $dvalue["surname"]." (".$dvalue["employee_id"].")" ?>
                                                                            
                                                                </option>   
                                                            <?php } ?>
                                                        </select>
                                                        <?php if ($disable_option == true) { ?>
                                                            <input type="hidden" name="consultant_doctor"  value="<?php echo $doctor_select ?>">
                                                        <?php } ?>
                                                    </div>
                                                    <span class="text-danger"><?php echo form_error('refference'); ?></span>
                                                </div>
                                            </div>
											<div class="col-md-6">
                                                <div class="mb-3">
                                                    <label><?php echo $this->lang->line('charge_category'); ?></label>
												    <select name="charge_category" class="w-100 form-control charge_category select2">
                                                        <option value=""><?php echo $this->lang->line('select'); ?></option>
                                                        <?php foreach ($charge_category as $key => $value) {  ?>
                                                        <option value="<?php echo $value['id']; ?>">
                                                        <?php echo $value['name']; ?>
                                                        </option>
                                                        <?php } ?>
                                                    </select>
                                                    <span class="text-danger"><?php echo form_error('charge_category'); ?></span>
                                                </div>
                                            </div>                                           
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label><?php echo $this->lang->line('charge'); ?> <small class="req"> *</small></label>
                                                    <select name="charge_id" class="w-100 form-control charge select2">
                                                    <option value=""><?php echo $this->lang->line('select')?></option>
                                                    </select>
                                                    <input type="hidden" class="form-control right-border-none" name="org_charge_amount" id="org_charge_amount" readonly autocomplete="off">
                                                    <span class="text-danger"><?php echo form_error('apply_charge'); ?></span>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label><?php echo $this->lang->line('standard_charge') . " (" . $currency_symbol . ")" ?></label>
                                                    <input type="text" readonly name="standard_charge" id="standard_charge" class="form-control" value="<?php echo set_value('standard_charge'); ?>"> 
                                                    <span class="text-danger"><?php echo form_error('standard_charge'); ?></span>
                                                </div>
                                            </div> 
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label><?php echo $this->lang->line('applied_charge') . " (" . $currency_symbol . ")" ?> <small class="req"> *</small></label>
                                                    <input type="text" name="amount" id="apply_charge" class="form-control apply_charge">    
                                                    <span class="text-danger"><?php echo form_error('amount'); ?></span>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3"> 
                                                    <label for="exampleInputEmail1"><?php echo $this->lang->line('discount'); ?></label>
                                                    <div class="input-group">                                                        
                                                        <input type="text" class="form-control discount_percentage" name="discount_percentage" id="discount_percentage" value='0' autocomplete="off">
                                                        <span class="input-group-text "> %</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3"> 
                                                    <label for="exampleInputEmail1"><?php echo $this->lang->line('tax'); ?></label>
                                                    <div class="input-group">                                                        
                                                        <input type="text" class="form-control right-border-none" name="percentage" id="percentage" readonly autocomplete="off">
                                                        <span class="input-group-text "> %</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label><?php echo $this->lang->line('amount'). " (" . $currency_symbol . ")" ?> <small class="req"> *</small></label>
                                                    <input type="text" name="apply_amount" readonly id="apply_amount" class="form-control">
                                                </div>
                                            </div>
                                            </div></div>
                                        </div>
                                        <div class="sh-form-card mb-3">
                                            <div class="sh-card-header">
                                                <span class="sh-card-header-title"><i class="fas fa-credit-card"></i> <?php echo $this->lang->line('payment'); ?></span>
                                            </div>
                                            <div class="p-2"><div class="row">
                                            <div class="col-sm-6">
                                                <div class="mb-3">
                                                    <label for="pwd"><?php echo $this->lang->line('payment_mode'); ?></label>
                                                    <select name="payment_mode" class="form-control payment_mode">
                                                        <?php foreach ($payment_mode as $payment_key => $payment_value) {
                                                            ?>
                                                            <option value="<?php echo $payment_key ?>" <?php
                                                                    if ($payment_key == 'Cash') {
                                                                        echo "selected";
                                                                    }
                                                                    ?> ><?php echo $payment_value ?></option>
                                                            <?php } ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="mb-3">
                                                    <label for="pwd"><?php echo $this->lang->line('paid_amount') . " (" . $currency_symbol . ")" ; ?> <small class="req"> *</small></label>
                                                    <input type="text" name="paid_amount" id="paid_amount" class="form-control paid_amount">    
                                                    <span class="text-danger"><?php echo form_error('paid_amount'); ?></span>
                                                </div>
                                            </div>
                                            <div class="cheque_div d-none col-12" >
                                                <div class="row">
                                                <div class="col-md-6 ps-0">
                                                <div class="mb-3">
                                                    <label><?php echo $this->lang->line('cheque_no'); ?> <small class="req"> *</small></label>
                                                    <input type="text" name="cheque_no" id="cheque_no" class="form-control">
                                                    <span class="text-danger"></span>
                                                </div>
                                                </div>
                                                <div class="col-md-6 pe-0">
                                                <div class="mb-3">
                                                    <label><?php echo $this->lang->line('cheque_date'); ?> <small class="req"> *</small></label>
                                                    <input type="text" name="cheque_date" id="cheque_date" class="form-control date">
                                                    <span class="text-danger"></span>
                                                </div>
                                                </div>
                                                <div class="col-sm-12 px-0">
                                                <div class="mb-3">
                                                    <label><?php echo $this->lang->line('attach_document'); ?></label>
                                                    <input type="file" class="filestyle form-control"   name="document">
                                                    <span class="text-danger"><?php echo form_error('document'); ?></span>
                                                </div>
                                                </div>
                                                </div>
                                            </div>
                                            <?php if ($this->module_lib->hasActive('live_consultation')) { ?>
                                            <div class="col-sm-6">
                                                <div class="mb-3">
                                                    <label>
                                                    <?php echo $this->lang->line('live_consultation'); ?></label>
                                                    <select name="live_consult"  class="form-control">
                                                        <?php foreach($yesno_condition as $yesno_key => $yesno_value) {  ?>
                                                            <option value="<?php echo $yesno_key ?>" <?php
                                                                    if ($yesno_key == 'no') {
                                                                        echo "selected";
                                                                    }
                                                                    ?> ><?php echo $yesno_value; ?>
                                                            </option>
                                                            <?php } ?>
                                                    </select>                                        
                                                    <span class="text-danger"><?php echo form_error('live_consult'); ?></span>
                                                </div>
                                            </div> 
                                            <?php  } ?>
                                            <div class="col-sm-6" id="antenatal_div" class="d-none">
                                                <div class="mb-3">
                                                    <label>&nbsp;</label>
                                                    <div class="sh-check-box">
                                                        <input type="checkbox" name="is_for_antenatal" id="is_for_antenatal" value="1">
                                                        <label for="is_for_antenatal"><?php echo $this->lang->line('is_for_antenatal'); ?></label>
                                                    </div>
                                                </div>
                                            </div>
                                            </div></div>
                                        </div>
                                    </div><!--./col-md-4-->
                                </div><!--./row-->
                </div>
                </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                <button type="submit" data-loading-text='<i class="fa fa-circle-o-notch fa-spin"></i>' name="save_print" class="btn btn-info printsavebtn"><i class="fa fa-print"></i> <?php echo $this->lang->line('save_print'); ?></button>
                <button type="submit" id="formaddbtn" name="save" data-loading-text='<i class="fa fa-circle-o-notch fa-spin"></i>' class="btn btn-info"><i class="fa fa-check-circle"></i> <span><?php echo $this->lang->line('save'); ?></span></button>
            </div>
        </form>                
    </div>
<script type="text/javascript">
/* ===== Patientflow: OPD "New Visit" modal glue (mirrors admin/patient/search.php) ===== */
function pfSetOpdPatient(pid, pname) {
    var m = $('#myModal');
    m.attr('data-pf-pid', pid).attr('data-pf-pname', pname);
}

// Initialize the modal's static select2 fields on page load (mirrors search.php).
$(function () {
    $('#myModal .charge_category, #myModal .charge, #myModal #consultant_doctor').select2({
        dropdownParent: $('#myModal')
    });
});

function holdModal(modalId) {
    (function () {
        var _el = document.getElementById(modalId);
        if (!_el) return;
        if (_el.parentNode !== document.body) { document.body.appendChild(_el); }
        bootstrap.Modal.getOrCreateInstance(_el, { backdrop: 'static', keyboard: false }).show();
    })();
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

function get_PatientDetails(id) {
    var base_url = "<?php echo $this->media_storage->getImageURL('backend/images/loading.gif') ?>";
    $("#ajax_load").html("<center><img src='" + base_url + "'/></center>");
    var password = makeid(5);
    if (id == '') {
        $("#ajax_load").html("");
        $("#patientDetails").addClass('d-none');
    } else {
        $.ajax({
            url: '<?php echo base_url(); ?>admin/patient/patientDetails',
            type: "POST",
            data: { id: id },
            dataType: 'json',
            success: function (res) {
                if (res) {
                    $("#ajax_load").html("");
                    $("#patientDetails").removeClass('d-none');
                    $('#patient_unique_id').text(res.id);
                    $('#patient_id').val(res.id);
                    $('#password').val(password);
                    $('#listname').text(res.patient_name + " (" + res.id + ")");
                    $('#guardian').text(res.guardian_name);
                    $('#listnumber').text(res.mobileno);
                    $('#email').text(res.email);
                    $('#mobnumber').val(res.mobileno);
                    $('#pemail').val(res.email);
                    if (res.gender == 'Female') {
<?php if ($this->rbac->hasPrivilege('opd_antenatal', 'can_view')) { ?>
                        $("#antenatal_div").removeClass('d-none');
<?php } ?>
                    }
                    $('#patientname').val(res.patient_name);
                    $('#doctname').val(res.name + " " + res.surname);
                    $('#consultant_doctor').val(null).trigger('change');
                    $('.charge_category').val(null).trigger('change');
                    $('.charge').val(null).empty().trigger('change');
                } else {
                    $("#ajax_load").html("");
                    $("#patientDetails").addClass('d-none');
                }
            }
        });
    }
}
function reset_form(_modal_div) {
    _modal_div.find('.charge_category').val('').trigger('change.select2');
    _modal_div.find('.charge ').val('').empty().trigger('change');
    _modal_div.find('#percentage').val("");
    _modal_div.find('#apply_charge').val("");
    _modal_div.find('#standard_charge').val("");
    _modal_div.find('#schedule_charge').val("");
    _modal_div.find('#org_id').val("");
    _modal_div.find('#org_charge_amount').val("");
    _modal_div.find('#apply_amount').val("");
    _modal_div.find('#paid_amount').val("");
    _modal_div.find('#discount_percentage').val("");
}

function update_amount(object_model) {
    var apply_charge = object_model.find('.apply_charge').val();
    var discount_percentage = object_model.find('#discount_percentage').val();
    var price_with_discount = (parseFloat(apply_charge)) - (parseFloat(apply_charge) * discount_percentage / 100);
    var tax_percentage = object_model.find('#percentage').val();
    var apply_amount = (parseFloat(price_with_discount) * tax_percentage / 100) + price_with_discount;
    object_model.find('#apply_amount').val(apply_amount.toFixed(2));
    object_model.find('.paid_amount').val(apply_amount.toFixed(2));
}

function getchargecode(charge_category, charge_id) {
    var div_data = "<option value=''><?php echo $this->lang->line('select'); ?></option>";
    if (charge_category != "") {
        $.ajax({
            url: base_url + 'admin/charges/getchargeDetails',
            type: "POST",
            data: { charge_category: charge_category },
            dataType: 'json',
            success: function (res) {
                div_data = "<option value=''><?php echo $this->lang->line('select'); ?></option>";
                $.each(res, function (i, obj) {
                    div_data += "<option value='" + obj.id + "'>" + obj.name + "</option>";
                });
                $('.charge').html(div_data);
                $(".charge").select2("val", charge_id);
            }
        });
    }
}

// Reinitialize the patient AJAX Select2 after the modal becomes visible.
$('#myModal').off('shown.bs.modal.select2init').on('shown.bs.modal.select2init', function () {
    var $sel = $('#addpatient_id');
    if ($sel.hasClass('select2-hidden-accessible')) {
        $sel.select2('destroy');
    }
    $sel.select2({
        ajax: {
            url: "<?php echo base_url(); ?>admin/patient/getPatientListAjax",
            type: "post",
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return { searchTerm: params.term };
            },
            processResults: function (response) {
                return { results: response };
            },
            cache: false
        },
        dropdownParent: $('#myModal')
    });
    $sel.focus();
});

// Pre-select the visit's patient in the OPD modal.
$('#myModal').off('shown.bs.modal.pfprefill').on('shown.bs.modal.pfprefill', function () {
    var m = $(this);
    var pid = m.attr('data-pf-pid');
    var pname = m.attr('data-pf-pname');
    m.removeAttr('data-pf-pid').removeAttr('data-pf-pname');
    if (pid && pname) {
        var $sel = $('#addpatient_id');
        $sel.empty().append(new Option(pname + ' (' + pid + ')', pid, true, true));
        $sel.val(pid);
        $sel.trigger('change');
    }
});
$(document).on('change', '#addpatient_id', function () {
    var div = $(this).closest('div.modal');
    $('#is_tpa').prop('checked', false);
    reset_form(div);
});

$(document).on('change', '#is_tpa', function () {
    var div = $(this).closest('div.modal');
    div.find('.charge_category').val(null).trigger('change');
    div.find('.charge').val(null).empty().trigger('change');
    div.find('#percentage,#apply_charge,#standard_charge,#schedule_charge,#org_id,#org_charge_amount,#apply_amount,#paid_amount,#discount_percentage').val('');
    div.find('select[name="payment_mode"]').val('Cash').trigger('change');
    div.find('select[name="payment_mode"] option[value="Cash"]').prop('selected', true);
});

$(document).on('select2:select', '.charge_category', function () {
    var charge_category = $(this).val();
    $('.charge').html("<option value=''><?php echo $this->lang->line('loading'); ?></option>");
    $('#percentage').val("");
    $('#apply_charge').val("");
    $('#standard_charge').val("");
    $('#schedule_charge').val("");
    $('#org_id').val("");
    $('#org_charge_amount').val("");
    $('#apply_amount').val("");
    $('#paid_amount').val("");
    $('#discount_percentage').val("");
    getchargecode(charge_category, "");
});

$(document).on('select2:select', '.charge', function () {
    var div = $(this).closest('div.modal');
    var charge = $(this).val();
    var patient_id = $("#patient_id").val();
    var apply_amount = 0;
    $('#org_charge_amount').val('');
    $('#discount_percentage').val('');
    var is_tpa = $("input:checkbox[name=is_tpa]").prop('checked') ? 1 : 0;
    if (charge == '') {
        reset_form(div);
        return false;
    }
    $.ajax({
        url: '<?php echo base_url(); ?>admin/patient/getChargeById',
        type: "POST",
        data: { charge_id: charge, 'patient_id': patient_id, 'is_tpa': is_tpa },
        dataType: 'json',
        success: function (res) {
            if (res.status == 0) {
                errorMsg(res.msg);
            } else if (res.status == 2) {
                errorMsg(res.msg);
            }
            var tax = res.result.percentage;
            var quantity = div.find('#qty').val();
            div.find('#percentage').val(tax);
            div.find('#apply_charge').val(parseFloat(res.result.standard_charge) * quantity);
            div.find('#standard_charge').val(res.result.standard_charge);
            div.find('#schedule_charge').val(res.result.org_charge);
            div.find('#org_id').val(res.result.org_charge_id);
            if (res.display_tpa_charge) {
                if (res.result.percentage == null) {
                    apply_amount = parseFloat(res.result.org_charge);
                } else {
                    apply_amount = (parseFloat(res.result.org_charge) * res.result.percentage / 100) + (parseFloat(res.result.org_charge));
                }
                div.find('#org_charge_amount').val(res.result.org_charge);
                div.find('#apply_charge').val(res.result.org_charge);
                div.find('#apply_amount').val(apply_amount.toFixed(2));
                div.find('.paid_amount').val(apply_amount.toFixed(2));
            } else {
                if (res.result.percentage == null) {
                    apply_amount = parseFloat(res.result.standard_charge);
                } else {
                    apply_amount = (parseFloat(res.result.standard_charge) * res.result.percentage / 100) + (parseFloat(res.result.standard_charge));
                }
                div.find('#standard_charge').val(res.result.standard_charge);
                div.find('#apply_charge').val(res.result.standard_charge);
                div.find('#apply_amount').val(apply_amount.toFixed(2));
                div.find('.paid_amount').val(apply_amount.toFixed(2));
            }
        }
    });
});

$(document).on('input paste keyup', '.apply_charge,.discount_percentage', function (e) {
    update_amount($(e.target).closest('div.modal'));
});
// Submit: create the OPD via the existing admin/patient endpoint, then reload the board.
$("#formadd").on('submit', (function (e) {
    e.preventDefault();
    var amount_to_be_paid = parseInt($("form#formadd #apply_amount").val());
    var amount_paying = parseInt($("form#formadd #paid_amount").val());
    if (amount_to_be_paid < amount_paying) {
        errorMsg("Invalid Amount");
        return false;
    }
    var $btn = $("button[type=submit][clicked=true]", "#formadd");
    if ($btn.length) { $btn.attr('disabled', true); }
    $.ajax({
        url: '<?php echo base_url(); ?>admin/patient',
        type: "POST",
        data: new FormData(this),
        dataType: 'json',
        contentType: false,
        cache: false,
        processData: false,
        success: function (data) {
            if ($btn.length) { $btn.attr('disabled', false); }
            if (data.status == "fail") {
                var message = "";
                $.each(data.error, function (index, value) { message += value; });
                errorMsg(message);
            } else {
                successMsg(data.message);
                var mEl = document.getElementById('myModal');
                if (mEl && typeof bootstrap !== 'undefined') {
                    bootstrap.Modal.getInstance(mEl).hide();
                }
                window.location.reload();
            }
        },
        error: function (xhr) {
            if ($btn.length) { $btn.attr('disabled', false); }
            alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");
        }
    });
}));

$("form#formadd button[type=submit]").click(function () {
    $("button[type=submit]", $(this).parents("form")).removeAttr("clicked");
    $(this).attr("clicked", "true");
});

$('#myModal').on('hidden.bs.modal', function () {
    $('#patientDetails').addClass('d-none');
    $('#formadd').find('input:text, input:password, input:file, textarea').val('');
    $('#formadd').find('select option:selected').removeAttr('selected');
    $('#formadd').find('input:checkbox, input:radio').removeAttr('checked');
    $('#formadd').find('select[name="payment_mode"]').val('Cash').trigger('change');
});
</script>

<!-- ===== Patientflow: Level-2 forward charge popup ===== -->
<div class="modal fade" id="pfChargeModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="fa fa-tags"></i> <?php echo $this->lang->line('consultation_category'); ?> —
                    <span id="pf_cc_name"></span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="" method="post" id="pfChargeForm">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="pf_cc_charge">
                            <?php echo $this->lang->line('amount') ?: 'Charge'; ?> (<?php echo $currency_symbol; ?>) <small class="req">*</small>
                        </label>
                        <input type="number" step="0.01" min="0" class="form-control" name="category_charge"
                               id="pf_cc_charge" required>
                        <small class="form-text text-muted">
                            <?php echo $this->lang->line('category_charge_hint') ?: 'Prefilled with the category charge - adjust if needed, then forward.'; ?>
                        </small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <?php echo $this->lang->line('cancel'); ?>
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-arrow-right"></i> <?php echo $this->lang->line('forward'); ?>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ===== Patientflow: Token generated popup ===== -->
<div class="modal fade" id="pfTokenModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content text-center">
            <div class="modal-header bg-success text-white justify-content-center">
                <h5 class="modal-title" style="padding-top: 12px; padding-bottom: 0px;">
                    <i class="fas fa-ticket-alt"></i> <?php echo $this->lang->line('token') ?: 'Token'; ?>
                </h5>
            </div>
            <div class="modal-body">
                <?php $pf_flash_token = $this->session->flashdata('token_no'); ?>
                <?php $pf_flash_token_patient = $this->session->flashdata('token_patient'); ?>
                <p class="mb-1 text-muted"><?php echo htmlspecialchars((string) $pf_flash_token_patient); ?></p>
                <h1 class="display-4 fw-bold text-primary mb-0" id="pf_token_no"><?php echo htmlspecialchars((string) $pf_flash_token); ?></h1>
                <p class="mb-0 text-muted small"><?php echo $this->lang->line('token_generated_hint') ?: 'Token generated - please proceed to the next counter.'; ?></p>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">
                    <?php echo $this->lang->line('close') ?: 'Close'; ?>
                </button>
            </div>
        </div>
    </div>
</div>
<?php if (!empty($pf_flash_token)) { ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    holdModal('pfTokenModal');
});
</script>
<?php } ?>

<script>
/* Prefill the popup from the clicked row's data attributes:
   - default: the visit's saved category_charge (if already set),
   - otherwise the consultation category's configured charge. */
var pfCategoryCharges = <?php
    $pf_cc_map = array();
    if (!empty($consultation_categories)) {
        foreach ($consultation_categories as $pf_cc) {
            $pf_cc_map[strtolower(trim($pf_cc->name))] = (float) $pf_cc->amount;
        }
    }
    echo json_encode($pf_cc_map);
?>;

function pfPrefillCharge(btn) {
    var catName  = btn.getAttribute('data-cc-name') || '';
    var saved    = btn.getAttribute('data-cc-default');
    var prefill  = (saved !== null && saved !== '') ? parseFloat(saved)
                   : (pfCategoryCharges[catName.toLowerCase()] || 0);

    document.getElementById('pf_cc_name').textContent = catName || '—';
    document.getElementById('pf_cc_charge').value = isNaN(prefill) ? '' : prefill.toFixed(2);
    document.getElementById('pfChargeForm').action =
        '<?php echo site_url('admin/patientflow/forward/'); ?>' + btn.getAttribute('data-cc-visit');
}
</script>
