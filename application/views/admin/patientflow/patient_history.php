<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<style>
    .ph-card { border: 1px solid rgba(0,0,0,0.08); border-radius: 8px; overflow: hidden; margin-bottom: 1rem; }
    .ph-card-header { background: #f8f9fa; border-bottom: 1px solid rgba(0,0,0,0.06); padding: 10px 16px; font-weight: 600; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 8px; }
    .ph-card-body { padding: 12px 16px; }
    .ph-meta-row { display: flex; flex-wrap: wrap; gap: 16px; margin-bottom: 10px; font-size: 0.9rem; }
    .ph-meta-item { flex: 1; min-width: 140px; }
    .ph-label { font-weight: 600; color: #6c757d; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.5px; }
    .ph-value { color: #212529; }
    .ph-table { width: 100%; border-collapse: collapse; margin-top: 8px; }
    .ph-table th, .ph-table td { border: 1px solid #dee2e6; padding: 6px 8px; font-size: 0.85rem; text-align: left; }
    .ph-table th { background: #f1f3f5; font-weight: 600; color: #495057; }
    .ph-badge { display: inline-block; padding: 4px 8px; border-radius: 10px; font-size: 0.75rem; font-weight: 600; }
    .ph-badge-active { background: #d1e7dd; color: #0f5132; }
    .ph-badge-completed { background: #e2e3e5; color: #383d41; }
    .ph-badge-cancelled { background: #f8d7da; color: #842029; }
    .ph-empty { text-align: center; padding: 40px 20px; color: #6c757d; }
    .ph-section-title { font-size: 0.85rem; font-weight: 700; color: #495057; margin: 12px 0 6px 0; text-transform: uppercase; letter-spacing: 0.5px; }
</style>

<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <nav class="small">
                <ol class="breadcrumb mb-0" style="background: transparent; padding: 0;">
                    <li class="breadcrumb-item"><a href="<?php echo site_url('admin/patientflow'); ?>"><?php echo $this->lang->line('patient_flow') ?: 'Patient Flow'; ?></a></li>
                    <li class="breadcrumb-item active"><?php echo $this->lang->line('patient_history') ?: 'Patient History'; ?></li>
                </ol>
            </nav>
            <h1 class="h4 mb-1"><?php echo $this->lang->line('patient_history') ?: 'Patient History'; ?></h1>
            <p class="text-muted mb-0"><?php echo htmlspecialchars((string) ($patient->patient_name ?: '')); ?></p>
        </div>
        <a href="<?php echo site_url('admin/patientflow'); ?>" class="btn btn-outline-secondary"><i class="fa fa-arrow-left me-1"></i> <?php echo $this->lang->line('back') ?: 'Back'; ?></a>
    </div>

    <!-- Patient Info Card -->
    <div class="card mb-4">
        <div class="card-header bg-light"><strong><?php echo $this->lang->line('patient_info') ?: 'Patient Information'; ?></strong></div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3"><span class="ph-label"><?php echo $this->lang->line('patient_name') ?: 'Name'; ?></span><br><span class="ph-value"><?php echo htmlspecialchars((string) ($patient->patient_name ?: '-')); ?></span></div>
                <div class="col-md-3"><span class="ph-label"><?php echo $this->lang->line('patient_uuid') ?: 'UUID'; ?></span><br><span class="ph-value"><?php echo htmlspecialchars((string) ($patient->patient_uuid ?: '-')); ?></span></div>
                <div class="col-md-2"><span class="ph-label"><?php echo $this->lang->line('age') ?: 'Age'; ?></span><br><span class="ph-value"><?php echo htmlspecialchars((string) ($patient->age ?: '-')); ?></span></div>
                <div class="col-md-2"><span class="ph-label"><?php echo $this->lang->line('gender') ?: 'Gender'; ?></span><br><span class="ph-value"><?php echo htmlspecialchars((string) ($patient->gender ?: '-')); ?></span></div>
                <div class="col-md-2"><span class="ph-label"><?php echo $this->lang->line('mobile') ?: 'Mobile'; ?></span><br><span class="ph-value"><?php echo htmlspecialchars((string) ($patient->mobileno ?: '-')); ?></span></div>
                <div class="col-md-3"><span class="ph-label"><?php echo $this->lang->line('blood_group') ?: 'Blood Group'; ?></span><br><span class="ph-value"><?php echo htmlspecialchars((string) ($patient->blood_group ?: '-')); ?></span></div>
                <div class="col-md-3"><span class="ph-label"><?php echo $this->lang->line('email') ?: 'Email'; ?></span><br><span class="ph-value"><?php echo htmlspecialchars((string) ($patient->email ?: '-')); ?></span></div>
                <div class="col-md-3"><span class="ph-label"><?php echo $this->lang->line('guardian') ?: 'Guardian'; ?></span><br><span class="ph-value"><?php echo htmlspecialchars((string) ($patient->guardian_name ?: '-')); ?></span></div>
                <div class="col-md-3"><span class="ph-label"><?php echo $this->lang->line('marital_status') ?: 'Marital Status'; ?></span><br><span class="ph-value"><?php echo htmlspecialchars((string) ($patient->marital_status ?: '-')); ?></span></div>
            </div>
            <?php if (!empty($patient->known_allergies)): ?>
            <div class="mt-3"><span class="ph-label"><?php echo $this->lang->line('known_allergies') ?: 'Known Allergies'; ?></span><br><span class="ph-value"><?php echo htmlspecialchars((string) $patient->known_allergies); ?></span></div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Visit History -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <strong><?php echo $this->lang->line('visit_history') ?: 'Visit History'; ?></strong>
            <span class="badge bg-primary"><?php echo count($visits ?? array()); ?> <?php echo $this->lang->line('visits') ?: 'visits'; ?></span>
        </div>
        <div class="card-body">
            <?php if (empty($visits)): ?>
            <div class="ph-empty">
                <i class="fa fa-history fa-3x mb-3 opacity-25"></i>
                <p><?php echo $this->lang->line('no_visit_history') ?: 'No visit history found.'; ?></p>
            </div>
            <?php else: ?>
            <?php foreach ($visits as $visit): ?>
            <div class="ph-card mb-3">
                <div class="ph-card-header">
                    <div class="d-flex align-items-center gap-2">
                        <span class="ph-badge <?php echo ($visit->status === 'completed') ? 'ph-badge-completed' : (($visit->status === 'cancelled') ? 'ph-badge-cancelled' : 'ph-badge-active'); ?>">
                            <?php echo ucfirst((string) ($visit->status ?: 'active')); ?>
                        </span>
                        <span class="text-muted small"><?php echo htmlspecialchars((string) ($visit->level_code ?: '-')); ?> · <?php echo htmlspecialchars((string) ($visit->level_title ?: '-')); ?></span>
                        <span class="badge bg-info-subtle text-info-emphasis text-monospace small">Stage: <?php echo htmlspecialchars((string) ($visit->stage_code ?: '-')); ?></span>
                    </div>
                    <div class="text-end">
                        <div class="fw-bold text-monospace small"><?php echo htmlspecialchars((string) ($visit->visit_code ?: ('#' . (int) $visit->id))); ?></div>
                        <div class="text-muted small"><?php echo htmlspecialchars((string) ($visit->created_at ?: '-')); ?></div>
                    </div>
                </div>
                <div class="ph-card-body">
                    <div class="ph-meta-row">
                        <div class="ph-meta-item"><span class="ph-label"><?php echo $this->lang->line('case_taking_type') ?: 'Case Taking Type'; ?></span><br><span class="ph-value"><?php echo htmlspecialchars((string) ($visit->case_taking_type ?: '-')); ?></span></div>
                        <div class="ph-meta-item"><span class="ph-label"><?php echo $this->lang->line('taken_by') ?: 'Taken By'; ?></span><br><span class="ph-value"><?php echo htmlspecialchars((string) (($visit->case_taking_by_name ?: '') . ' ' . ($visit->case_taking_by_surname ?: '') ?: '-')); ?></span></div>
                        <div class="ph-meta-item"><span class="ph-label"><?php echo $this->lang->line('taken_at') ?: 'Taken At'; ?></span><br><span class="ph-value"><?php echo htmlspecialchars((string) ($visit->case_taking_at ?: '-')); ?></span></div>
                        <div class="ph-meta-item"><span class="ph-label">Billing</span><br><span class="ph-value"><?php echo htmlspecialchars((string) (($visit->billing_reference ?: '—') . ' / ' . ($visit->invoice_no ?: '—'))); ?></span></div>
                    </div>

                    <?php if (!empty($visit->case_taking_description) || !empty($visit->ct_primary_concern) || !empty($visit->ct_sex)): ?>
                    <div class="ph-section-title"><?php echo $this->lang->line('case_taking_details') ?: 'Case Taking Details'; ?></div>
                    <table class="ph-table">
                        <?php if (!empty($visit->ct_sex)): ?><tr><th style="width:30%"><?php echo $this->lang->line('sex') ?: 'Sex'; ?></th><td><?php echo htmlspecialchars((string) $visit->ct_sex); ?></td></tr><?php endif; ?>
                        <?php if (!empty($visit->ct_primary_concern)): ?><tr><th style="width:30%"><?php echo $this->lang->line('primary_concern') ?: 'Primary Concern'; ?></th><td><?php echo htmlspecialchars((string) $visit->ct_primary_concern); ?></td></tr><?php endif; ?>
                        <?php if (!empty($visit->ct_concern_duration)): ?><tr><th style="width:30%"><?php echo $this->lang->line('duration_of_concern') ?: 'Duration'; ?></th><td><?php echo htmlspecialchars((string) $visit->ct_concern_duration); ?></td></tr><?php endif; ?>
                        <?php if (!empty($visit->ct_primary_system)): ?><tr><th style="width:30%"><?php echo $this->lang->line('primary_system') ?: 'Primary System / Area'; ?></th><td><?php echo htmlspecialchars((string) $visit->ct_primary_system); ?></td></tr><?php endif; ?>
                        <?php if (!empty($visit->ct_dominant_nature)): ?><tr><th style="width:30%"><?php echo $this->lang->line('dominant_nature') ?: 'Dominant Nature'; ?></th><td><?php echo htmlspecialchars((string) $visit->ct_dominant_nature); ?></td></tr><?php endif; ?>
                        <?php if (!empty($visit->ct_constitution_code)): ?><tr><th style="width:30%"><?php echo $this->lang->line('constitution_code') ?: 'Constitution Code'; ?></th><td><?php echo htmlspecialchars((string) $visit->ct_constitution_code); ?></td></tr><?php endif; ?>
                        <?php if (!empty($visit->ct_status)): ?><tr><th style="width:30%"><?php echo $this->lang->line('case_status') ?: 'Case Status'; ?></th><td><?php echo htmlspecialchars((string) $visit->ct_status); ?></td></tr><?php endif; ?>
                        <?php if (!empty($visit->ct_assessment_certainty)): ?><tr><th style="width:30%"><?php echo $this->lang->line('assessment_certainty') ?: 'Assessment Certainty'; ?></th><td><?php echo htmlspecialchars((string) $visit->ct_assessment_certainty); ?></td></tr><?php endif; ?>
                        <?php if (!empty($visit->ct_tissue_structure)): ?><tr><th style="width:30%"><?php echo $this->lang->line('tissue_structure') ?: 'Tissue / Structure'; ?></th><td><?php echo htmlspecialchars((string) $visit->ct_tissue_structure); ?></td></tr><?php endif; ?>
                        <?php if (!empty($visit->ct_report_info)): ?><tr><th style="width:30%"><?php echo $this->lang->line('report_information') ?: 'Report Information'; ?></th><td><?php echo htmlspecialchars((string) $visit->ct_report_info); ?></td></tr><?php endif; ?>
                        <?php if (!empty($visit->ct_secondary_concerns)): ?><tr><th style="width:30%"><?php echo $this->lang->line('secondary_concerns') ?: 'Secondary Concerns'; ?></th><td><?php echo htmlspecialchars((string) $visit->ct_secondary_concerns); ?></td></tr><?php endif; ?>
                        <?php if (!empty($visit->case_taking_description)): ?><tr><th style="width:30%"><?php echo $this->lang->line('description') ?: 'Description'; ?></th><td><?php echo nl2br(htmlspecialchars((string) $visit->case_taking_description)); ?></td></tr><?php endif; ?>
                        <?php if (!empty($visit->ct_additional_note)): ?><tr><th style="width:30%"><?php echo $this->lang->line('additional_note') ?: 'Additional Note'; ?></th><td><?php echo htmlspecialchars((string) $visit->ct_additional_note); ?></td></tr><?php endif; ?>
                        <?php if (!empty($visit->ct_previous_treatment)): ?><tr><th style="width:30%"><?php echo $this->lang->line('previous_treatment') ?: 'Previous Treatment'; ?></th><td><?php echo htmlspecialchars((string) $visit->ct_previous_treatment); ?></td></tr><?php endif; ?>
                        <?php if (!empty($visit->ct_programme_suggestion)): ?><tr><th style="width:30%"><?php echo $this->lang->line('programme_suggestion') ?: 'Programme Suggestion'; ?></th><td><?php echo htmlspecialchars((string) $visit->ct_programme_suggestion); ?></td></tr><?php endif; ?>
                    </table>
                    <?php endif; ?>

                    <?php if (!empty($visit->medicines)): ?>
                    <div class="ph-section-title"><?php echo $this->lang->line('prescribed_medicines') ?: 'Prescribed Medicines'; ?></div>
                    <table class="ph-table">
                        <thead>
                            <tr><th style="width:35%"><?php echo $this->lang->line('medicine') ?: 'Medicine'; ?></th><th><?php echo $this->lang->line('series') ?: 'Series'; ?></th><th><?php echo $this->lang->line('variant') ?: 'Variant'; ?></th><th><?php echo $this->lang->line('pharmacy') ?: 'Pharmacy'; ?></th></tr>
                        </thead>
                        <tbody>
                        <?php foreach ($visit->medicines as $m): ?>
                        <tr>
                            <td><?php echo htmlspecialchars((string) ($m['medicine_identity'] ?: $m['code'] ?: $m['medicine_id'])); ?></td>
                            <td><?php echo htmlspecialchars((string) ($m['series'] ?: '-')); ?></td>
                            <td><?php echo htmlspecialchars((string) ($m['variant'] ?: '-')); ?></td>
                            <td><?php echo htmlspecialchars((string) ($m['pharmacy_name'] ?: '-')); ?></td>
                        </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php if (!empty($visits)): ?>
<script>
// Optional: nothing needed server-rendered
</script>
<?php endif; ?>
