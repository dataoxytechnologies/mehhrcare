# Patientflow Module - Flow & Knowledge

## Overview
Patientflow is a token/level-based patient journey system in Smart Hospital. It manages visit registration/enrollment, level transitions, billing/invoice, token queue, case taking, and configuration via Patientflowsetting.

## Controllers
- `application/controllers/admin/Patientflow.php` (main flow)
- `application/controllers/admin/Patientflowsetting.php` (masters/mappings)

## Models
- `application/models/Patientflow_model.php` (~3497 lines) - schema migrations, visit/level logic, case taking, mappings, seeds
- Related: `f_series_seed_a.php` through `f_series_seed_g.php`

## Key DB Tables (from Patientflow_model migrations/seeds)
- `patient_flow_levels` - Hierarchical levels (parent_id). Permissions per level: can_approve, can_change_payment, can_generate_billing_ref, can_generate_invoice, can_print_billing, can_change_status, can_generate_token, can_create_pbhp_id, can_cash_received, can_change_programme_status, can_case_taking, can_view_case_taking.
- `patient_flow_visits` - Main visit record. Tracks patient, current_level_id, opd_id, invoice_ref/billing_reference/invoice_no, billing_status, token_no/token_status/timing, cash_received, programme_status, case_taking_type/description/at/by, status/completed_at/by, patient_uuid linkage.
- `patient_flow_settings` - Key-value settings
- `patients` - Extended with `patient_uuid` (MC-KTR-YYYY-###### style)
- `patient_flow_constitutions`, `constitution_body_build`, `constitution_face_shape`, `constitution_nose_type`, `constitution_master` - Constitution classification
- `programme_master`, `programme_subcategory` - Programme management
- `stage_master` (quarter/stage, duration_days, operational_rule)
- `broad_area_series` (LKO/SBM/WTB/FLKO/FSBM/FWTB/FUOC etc., pair_group)
- `medicine_master` (code, medicine_identity, series F/FA/FB/FC/FD/FX/E, details, status Active/Inactive/Locked)
- `constit_f_series_mapping` (series_id × stage_id → medicine_id, Locked)
- `formulation_early_mapping` (pair_group × stage_id × session → medicine_id for FA/FB)
- `formulation_universal_mapping` (stage_id × session → medicine_id for FC/FD)

## Main Flow (Patientflow controller)
Key endpoints:
- `index()` redirects to board/register
- `register()` - Registration view
- `save_visit()` - Create visit; sets current level, generates references
- `approve($visit_id)` / `forward($visit_id)` / `backward($visit_id)` - Level transitions
- `toggle_payment()` / `mark_paid()` - Payment status
- `toggle_cash_received()` - Cash received flag
- `change_programme_status()` - enroll/declined/deferred
- `generate_token()` / `token_queue()` / `set_token_status()` - Queue/token management
- `create_pbhp()` - PBHP ID creation
- `generate_billing_reference()` / `generate_invoice()` / `set_billing_status()` / `print_billing()` - Billing
- `enroll_from_opd()` / `add_patient_from_opd()` - OPD integration
- `levels()` / `save()` / `delete()` / `levels_ajax()` - Level config
- `set_visit_status($visit_id)` - active/completed/cancelled
- `save_case_taking()` - Case taking details (validated in controller)
- `patientflowreport()` - Reports

## Patientflowsetting Groups
Groups in `$groups`: constitution_master, programme_master, stage_master, medicine_master, f_series_mapping, formulation_mapping, daily_mapping, broad_area_rules (added). Views under `application/views/admin/patientflow/setting/`.

### Medicine Master
- Table `medicine_master`. Seed creates F codes (F001-...), FA/FB/FC/FD daily, FX/E.
- UI allows Add/Edit/Delete for all rows (Locked guard removed). Status Active/Inactive/Locked.

### F-Series Mapping
- Maps series × stage → F medicine via `constit_f_series_mapping`. Edit pre-fills values.

### Formulation Mapping / Daily Mapping
- Use `formulation_early_mapping` (FA/FB by pair_group × stage × session) and `formulation_universal_mapping` (FC/FD by stage × session). Same data source, different views.

### Broad-area Rules
- Maps concern patterns + applicability to series (LKO/SBM/WTB/FUOC variants) with 2 outcomes (1 or 2). Uses `broad_area_series` pair_group/codes.

## Case Taking
Modal in board (`application/views/admin/patientflow/board.php`): 
- Fields include sex, primary concern/duration/system/nature, BOD1-4/FSH1-4/NOS1-4 (from constitution lookups), assessment certainty, case status, review reason (conditional), tissue/report/secondary/additional/previous/programme suggestion, description.
- Controller validates required fields; BOD/FSH/NOS optional. View modal shows 2-column layout, larger modal.
- Data stored in `patient_flow_visits` case taking columns + extra JSON/fields as per model.

## Patient UUID
Patients get `patient_uuid` (MC-KTR-YYYY-######). Displayed in board and case taking modals.

## Notes
- Level-based permissions control actions (case taking, billing, tokens, etc.)
- Token workflow tied to payment/cash received
- Billing flows: billing reference (R2) and invoice
