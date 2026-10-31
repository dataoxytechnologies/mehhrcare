<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Patientflow — phase 1 patient flow module.
 *
 * New self-contained section. Currently exposes the workflow LEVELS / roles
 * hierarchy management (the R1 -> R2 -> Cash -> ... chain). Later this same
 * controller will host the visit list + transitions/board.
 */
class Patientflow extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('patientflow_model');
        $this->load->model('role_model');
        $this->lang->load('patientflow');
        $this->load->helper('customfield_helper');
        $this->load->helper('custom');
    }

    /**
     * Role-scoped flow context for the logged-in staff.
     *
     * - Super Admin bypasses level ownership (sees/forwards everything).
     * - Everyone else is limited to the levels owned by their own role(s).
     *
     * @return array
     */
    private function pf_context()
    {
        $admin      = $this->session->userdata('hospitaladmin');
        $roles      = ($admin && isset($admin['roles'])) ? $admin['roles'] : array();
        $role_ids   = array_values($roles);          // [id, ...]
        $role_names = array_keys($roles);            // [name, ...]

        $is_superadmin = in_array('Super Admin', $role_names, true);

        if ($is_superadmin) {
            $allowed_level_ids = null;               // no restriction
            $my_levels         = $this->patientflow_model->get_chain();
        } else {
            $my_levels         = $this->patientflow_model->get_levels_for_roles($role_ids);
            $allowed_level_ids = array_map(function ($l) { return (int) $l->id; }, $my_levels);
        }

        return array(
            'role_ids'          => $role_ids,
            'is_superadmin'     => $is_superadmin,
            'allowed_level_ids' => $allowed_level_ids,
            'my_levels'         => $my_levels,
        );
    }

    /**
     * Check whether the logged-in staff (per pf_context) holds a level flag:
     * superadmin always bypasses; otherwise any of their levels with the
     * given column set to 1 grants the permission.
     *
     * @param array  $ctx  pf_context() result
     * @param string $flag column name on patient_flow_levels
     * @return bool
     */
    private function pf_has_level_flag($ctx, $flag)
    {
        if (!empty($ctx['is_superadmin'])) {
            return true;
        }
        if (empty($ctx['my_levels'])) {
            return false;
        }
        foreach ($ctx['my_levels'] as $ml) {
            if (isset($ml->$flag) && (int) $ml->$flag === 1) {
                return true;
            }
        }
        return false;
    }

    /**
     * Patient flow board — the actual patient flow (list of registered patients
     * and their current level in the chain).
     */
    public function index()
    {
        if (!$this->rbac->hasPrivilege('patient_flow', 'can_view')) {
            access_denied();
        }

        $this->session->set_userdata('top_menu', 'patient_flow');
        $this->session->set_userdata('sub_menu', 'patient_flow');

        $ctx = $this->pf_context();

        // Intersect the ?level= filter with the role-scoped allowed levels so a
        // restricted user cannot probe foreign levels via URL.
        $filter_level = $this->input->get_post('level', true);
        if ($filter_level !== null && $filter_level !== '' && is_array($ctx['allowed_level_ids'])
            && !in_array((int) $filter_level, $ctx['allowed_level_ids'], true)) {
            $filter_level = null;
        }

        // Free-text search across the identifiers shown on the board listing
        // (visit no, token, OPD id, UUID, patient name, mobile, PBHP id, billing ref, etc).
        $filter_search = $this->input->get_post('search', true);
        if ($filter_search !== null) {
            $filter_search = trim((string) $filter_search);
        }

        // Status filter (?status=active|completed|cancelled|all). Defaults to 'active'.
        $filter_status = strtolower(trim((string) $this->input->get_post('status', true)));
        $allowed_statuses = array('active', 'completed', 'cancelled', 'all');
        if ($filter_status === '' || !in_array($filter_status, $allowed_statuses, true)) {
            $filter_status = 'active';
        }

        $data['title']             = $this->lang->line('patient_flow');
        $data['visits']            = $this->patientflow_model->get_visits(
            $filter_level ? intval($filter_level) : null,
            $ctx['allowed_level_ids'],
            200,
            $filter_search,
            $filter_status
        );
        $data['filter_status']     = $filter_status;

        // Constitution lookups for the case-taking modal selects (BOD/FSH/NOS
        // slots come from the body-build / face-shape / nose-type masters).
        $data['body_builds']       = $this->patientflow_model->get_constitution_lookups('body', true);
        $data['face_shapes']       = $this->patientflow_model->get_constitution_lookups('face', true);
        $data['nose_types']        = $this->patientflow_model->get_constitution_lookups('nose', true);

        // Load each visit's step-by-step history so the board can render a timeline.
        $visit_logs = array();
        if (!empty($data['visits'])) {
            $visit_ids = array_map(function ($vv) { return (int) $vv->id; }, $data['visits']);
            $visit_logs = $this->patientflow_model->get_logs_by_visit_ids($visit_ids);
        }
        $data['visit_logs']        = $visit_logs;
        // Build patient visit history for the case-taking modal Patient History column.
        $pf_visit_history = array();
        if (!empty($data['visits'])) {
            $pf_patient_ids = array();
            foreach ($data['visits'] as $pf_vv) {
                if (!empty($pf_vv->patient_id)) {
                    $pf_patient_ids[(int) $pf_vv->patient_id] = true;
                }
            }
            foreach (array_keys($pf_patient_ids) as $pf_pid) {
                $pf_visit_history[$pf_pid] = $this->patientflow_model->get_patient_flow_history($pf_pid);
            }
        }
        $data['pf_visit_history'] = $pf_visit_history;

        // Filter dropdown: own levels for staff, all levels for superadmin.
        $data['levels']            = $ctx['allowed_level_ids'] === null ? $this->patientflow_model->get_chain() : $ctx['my_levels'];
        $data['filter_level']      = $filter_level;
        $data['filter_search']     = $filter_search;
        $data['pf_is_superadmin']  = $ctx['is_superadmin'];
        $data['pf_my_levels']      = $ctx['my_levels'];

        // Payment-status permission: a staff may toggle a visit's payment
        // status if superadmin OR any of their levels has the
        // can_change_payment flag checked in the Levels form.
        $data['pf_is_cashier'] = $this->pf_has_level_flag($ctx, 'can_change_payment');

        // Senior-role flag: used to gate medicine identity visibility and
        // FX prescribing. Role name comes from the session (e.g. "Senior Doctor").
        $staff_role = $this->customlib->getStaffRole();
        $pf_is_senior = false;
        if ($staff_role) {
            $role_obj = json_decode($staff_role, true);
            if (!empty($role_obj['name']) && stripos($role_obj['name'], 'senior') !== false) {
                $pf_is_senior = true;
            }
        }
        $data['pf_is_senior'] = $pf_is_senior;

        // Token-generation permission from the can_generate_token level flag.

        $data['pf_can_generate_token'] = $this->pf_has_level_flag($ctx, 'can_generate_token');

        // PBHP-ID creation permission from the can_create_pbhp_id level flag.
        $data['pf_can_create_pbhp'] = $this->pf_has_level_flag($ctx, 'can_create_pbhp_id');
        $data['pbhp_codes']         = $this->patientflow_model->pbhp_codes();

        // Programme enrolment list for the case-taking modal dropdown.
        $data['programmes']         = $this->patientflow_model->get_programme_enrolments();

        // Broad area rules for the case-taking modal "Primary system / area" select.
        $data['broad_area_rules']   = $this->patientflow_model->get_broad_rules();

        // Cash-received permission from the can_cash_received level flag.
        $data['pf_can_cash_received'] = $this->pf_has_level_flag($ctx, 'can_cash_received');
        // Programme-status permission from the can_change_programme_status level flag.
        $data['pf_can_programme_status'] = $this->pf_has_level_flag($ctx, 'can_change_programme_status');
        $data['programme_statuses'] = $this->patientflow_model->programme_statuses();

        // Billing permissions from level flags.
        $data['pf_can_generate_billing'] = $this->pf_has_level_flag($ctx, 'can_generate_billing_ref');
        $data['pf_can_print_billing']    = $this->pf_has_level_flag($ctx, 'can_print_billing');
        // Invoice permission from the can_generate_invoice level flag.
        $data['pf_can_generate_invoice'] = $this->pf_has_level_flag($ctx, 'can_generate_invoice');
        // Default consultant doctor + payment mode for the flow OPD modal.
        $data['pf_default_doctor_id'] = $this->patientflow_model->get_first_doctor_id();

        // ---- Data for the OPD "New Visit" modal (mirrors Patient::search()) ----
        $this->load->model('icd10_model');
        $data['payment_mode']       = $this->config->item('payment_mode');
        $data['yesno_condition']    = $this->config->item('yesno_condition');
        $data['doctors']            = $this->staff_model->getStaffbyrole(3);
        $data['charge_category']    = $this->charge_category_model->getCategoryByModule("opd");
        $data['symptomsresulttype'] = $this->symptoms_model->getsymtype();
        $data['doctor_select']      = '';
        $data['disable_option']     = false;
        $data['currency_symbol']    = $this->customlib->getHospitalCurrencyFormat();
        $data['icd10_groups']       = $this->icd10_model->getgroup();
        $data['icd10_codes']        = $this->icd10_model->get();

        // Allowed billing statuses (R2 billing reference management).
        $data['billing_statuses'] = $this->patientflow_model->billing_statuses();

        // Data for the flow-specific "New Patient" modal (admin/patientflow/patientaddmodal).
        $data['bloodgroup']    = $this->config->item('bloodgroup');
        $data['organisation']  = $this->organisation_model->get();

        $this->load->view('layout/header', $data);
        $this->load->view('admin/patientflow/board', $data);
        $this->load->view('layout/footer', $data);
    }

    /**
     * Register a new patient into the flow (R1).
     */
    public function register()
    {
        if (!$this->rbac->hasPrivilege('patient_flow', 'can_add')) {
            access_denied();
        }

        $this->session->set_userdata('top_menu', 'patient_flow');
        $this->session->set_userdata('sub_menu', 'patient_flow_add');

        $data['title'] = $this->lang->line('add_patient');
        $data['levels'] = $this->patientflow_model->get_chain();
        $data['first_level'] = $this->patientflow_model->get_first_level();

        $this->load->view('layout/header', $data);
        $this->load->view('admin/patientflow/register', $data);
        $this->load->view('layout/footer', $data);
    }

    /**
     * Save a newly registered patient + flow visit (R1).
     */
    public function save_visit()
    {
        if (!$this->rbac->hasPrivilege('patient_flow', 'can_add')) {
            access_denied();
        }

        $this->form_validation->set_rules('name', $this->lang->line('name'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('gender', $this->lang->line('gender'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('age_year', $this->lang->line('year'), 'trim|required|numeric');
        $this->form_validation->set_rules('age_month', $this->lang->line('month'), 'trim|required|numeric');
        $this->form_validation->set_rules('age_day', $this->lang->line('day'), 'trim|required|numeric');
        $this->form_validation->set_rules('mobileno', $this->lang->line('phone'), 'trim|numeric');

        if ($this->form_validation->run() == false) {
            $this->session->set_flashdata('error_msg', validation_errors());
            redirect('admin/patientflow/register');
            return;
        }

        $this->load->model('patient_model');

        // Dedupe: if a patient with the same name + mobile already exists, reuse
        // it and only create a new VISIT (no duplicate patient row).
        $existing_patient_id = $this->patientflow_model->find_patient_by_name_mobile(
            $this->input->post('name', true),
            $this->input->post('mobileno', true)
        );

        // 1) Create the real patient row (reuses core model — no core file modified).
        $patient_data = array(
            'patient_name'          => $this->input->post('name', true),
            'mobileno'              => $this->input->post('mobileno', true),
            'email'                 => $this->input->post('email', true),
            'gender'                => $this->input->post('gender', true),
            'guardian_name'         => $this->input->post('guardian_name', true),
            'address'               => $this->input->post('address', true),
            'known_allergies'       => $this->input->post('known_allergies', true),
            'note'                  => $this->input->post('note', true),
            'marital_status'        => $this->input->post('marital_status', true) ?: '',
            'blood_bank_product_id' => null,
            'identification_number' => $this->input->post('identification_number', true),
            'dob'                   => $this->input->post('dob', true) ? date('Y-m-d', strtotime($this->input->post('dob', true))) : null,
            'age'                   => intval($this->input->post('age_year', true)),
            'month'                 => intval($this->input->post('age_month', true)),
            'day'                   => intval($this->input->post('age_day', true)),
            'as_of_date'            => date('Y-m-d'),
            'is_active'             => 'yes',
        );

        // Patient UUID (MC-KTR-YYYY-######) — generate for every patient
        $patient_uuid = $this->patientflow_model->generate_patient_uuid();

        $patient_id_created = false;
        if ($existing_patient_id) {
            $patient_id = (int) $existing_patient_id;
            // Backfill a missing UUID on an existing patient.
            $this->patientflow_model->backfill_patient_uuid($patient_id, $patient_uuid);
        } else {
            // For a brand-new patient, set the UUID directly in the insert data.
            $patient_data['patient_uuid'] = $patient_uuid;
            $patient_id = $this->patient_model->add_patient($patient_data);
            if (!$patient_id) {
                $this->session->set_flashdata('error_msg', $this->lang->line('save_failed'));
                redirect('admin/patientflow/register');
                return;
            }
            // Guarantee the UUID is set even if add_patient() missed it for any reason.
            $this->patientflow_model->ensure_patient_uuid($patient_id, $patient_uuid);
            $patient_id_created = true;
        }


        // 2) Create flow visit at the first level.
        $first_level = $this->patientflow_model->get_first_level();
        $visit_no = $this->patientflow_model->generate_visit_no('PF');

        // The visit amount starts at 0 — charges are pulled from the linked
        // OPD's data (patient_charges.amount, net after tax/discount) whenever
        // the flow needs the final figure (forward / print / payment counters).
        $visit_id = $this->patientflow_model->add_visit(array(
            'patient_id'          => $patient_id,
            'visit_no'            => $visit_no,
            'current_level_id'    => $first_level ? $first_level->id : null,
            'level_started_at'    => date('Y-m-d H:i:s'),
            'amount'              => 0,
            'payment_status'      => 'pending',
            'created_by'          => $this->customlib->getStaffID(),
        ));

        // 3) Create the core OPD record for this visit (same record the OPD
        //    module uses: case_reference + opd_details + visit_details + charge)
        //    and link its id to the flow visit. Nothing is collected here —
        //    payment happens at the counter levels — so the OPD charge carries
        //    the consultation amount while the transaction stays a 0-amount
        //    placeholder. An OPD failure must never block the flow registration.
        $opd_id = null;
        try {
            $payment_section = $this->config->item('payment_section');
            $opd_data        = array(
                'patient_id'   => $patient_id,
                'generated_by' => $this->customlib->getStaffID(),
            );
            $transaction_data = array(
                'case_reference_id' => 0,
                'opd_id'            => 0,
                'patient_id'        => $patient_id,
                'amount'            => 0,
                'type'              => 'payment',
                'section'           => !empty($payment_section['opd']) ? $payment_section['opd'] : 'opd',
                'payment_mode'      => '',
                'note'              => 'Patient flow registration - payment due at counter',
                'payment_date'      => date('Y-m-d H:i:s'),
                'received_by'       => $this->customlib->getLoggedInUserID(),
            );
            $charge = array(
                'opd_id'              => 0,
                'date'                => date('Y-m-d H:i:s'),
                'charge_id'           => null,
                'qty'                 => 1,
                'apply_charge'        => 0,
                'standard_charge'     => 0,
                'tpa_charge'          => 0,
                'amount'              => 0,
                'discount_percentage' => 0,
                'created_at'          => date('Y-m-d'),
                'note'                => 'Patient flow registration',
                'tax'                 => 0,
                'organisation_id'     => null,
                'insurance_id'        => null,
                'insurance_validity'  => null,
            );
            $opd_visit_data = array(
                'appointment_date'  => date('Y-m-d H:i:s'),
                'opd_details_id'    => 0,
                'symptoms'          => null,
                'refference'        => null,
                'cons_doctor'       => null,
                'casualty'          => 'no',
                'case_type'         => null,
                'payment_mode'      => '',
                'note'              => 'Patient flow visit ' . $visit_no,
                'live_consult'      => 'no',
                'generated_by'      => $this->customlib->getLoggedInUserID(),
                'patient_charge_id' => null,
                'transaction_id'    => null,
                'can_delete'        => 'no',
                'known_allergies'   => $this->input->post('known_allergies', true),
            );

            $opd_id = $this->patient_model->add_opd($opd_data, $transaction_data, $charge, $opd_visit_data);

            // SaaS: keep the OPD usage counter accurate (mirrors Patient::addpatient()).
            if ($opd_id) {
                $this->load->library('saasvalidation');
                try {
                    $this->saasvalidation->updateResouceQuota('no_of_opd', 1);
                } catch (Exception $e) {
                    log_message('error', 'SaaS no_of_opd quota increment failed (patient flow save_visit): ' . $e->getMessage());
                }
            }
        } catch (Exception $e) {
            log_message('error', 'Patient flow: OPD creation failed for visit #' . $visit_id . ': ' . $e->getMessage());
            $opd_id = null;
        }

        // 4) Link the OPD id to the flow visit (shown on the flow board) and
        //    sync the visit amount from the OPD's net charge (after tax/discount).
        if ($opd_id) {
            $this->patientflow_model->set_visit_opd_id($visit_id, $opd_id);
            $this->patientflow_model->sync_visit_amount_from_opd($visit_id);
        }

        // 5) Log registration (remark records the linked OPD id for traceability).
        $this->patientflow_model->add_log(array(
            'visit_id'      => $visit_id,
            'to_level_id'   => $first_level ? $first_level->id : null,
            'action'        => 'register',
            'remarks'       => $opd_id ? ('OPD created (OPD ID: ' . $opd_id . ')') : null,
            'acted_by'      => $this->customlib->getStaffID(),
        ));

        if ($existing_patient_id) {
            $this->session->set_flashdata('success_msg', $this->lang->line('existing_patient_visit_created') ?: 'Existing patient found — a new visit was created in the flow.');
        } else {
            $this->session->set_flashdata('success_msg', $this->lang->line('patient_registered'));
        }
        redirect('admin/patientflow');
    }

    /**
     * Mark a visit as approved by a "can approve" counter level.
     * Server-side guard: superadmin, or the staff's role owns the current level
     * AND that level is marked can_approve = 1.
     */
    public function approve($visit_id = 0)
    {
        if (!$this->rbac->hasPrivilege('patient_flow', 'can_add')) {
            access_denied();
        }

        $visit_id = intval($visit_id);
        if ($visit_id) {
            $ctx = $this->pf_context();

            $may_approve = $ctx['is_superadmin']
                || $this->patientflow_model->can_approve_visit($visit_id, $ctx['role_ids']);

            if (!$may_approve) {
                access_denied();
                return;
            }

            $role_id = null;
            $role_json = $this->customlib->getStaffRole();
            if ($role_json) {
                $role_data = json_decode($role_json, true);
                $role_id = isset($role_data['id']) ? $role_data['id'] : null;
            }

            $result = $this->patientflow_model->approve_visit(
                $visit_id,
                $this->customlib->getStaffID(),
                $role_id
            );
            if ($result['status']) {
                $this->session->set_flashdata('success_msg', $this->lang->line('visit_approved') ?: 'Visit approved.');
            } else {
                $this->session->set_flashdata('error_msg', $result['message']);
            }
        }
        redirect('admin/patientflow');
    }

    /**
     * Toggle a visit's payment status (pending <-> paid).
     * Only a cashier (role owns a 'cash' counter-type level) or a superadmin may
     * change payment status.
     */
    public function toggle_payment($visit_id = 0)
    {
        if (!$this->rbac->hasPrivilege('patient_flow', 'can_view')) {
            access_denied();
        }

        $visit_id = intval($visit_id);
        if ($visit_id) {
            $ctx = $this->pf_context();

            // Payment-status permission: superadmin, or a staff whose role
            // owns a level with the can_change_payment flag checked.
            $is_cashier = $ctx['is_superadmin'];
            if (!$is_cashier) {
                foreach ($ctx['my_levels'] as $ml) {
                    if (isset($ml->can_change_payment) && (int) $ml->can_change_payment === 1) {
                        $is_cashier = true;
                        break;
                    }
                }
            }
            if (!$is_cashier) {
                access_denied();
                return;
            }

            $visit = $this->patientflow_model->get_visit($visit_id);
            $new_status = ($visit && $visit->payment_status === 'paid') ? 'pending' : 'paid';

            $role_id = null;
            $role_json = $this->customlib->getStaffRole();
            if ($role_json) {
                $role_data = json_decode($role_json, true);
                $role_id = isset($role_data['id']) ? $role_data['id'] : null;
            }

            $result = $this->patientflow_model->set_payment_status(
                $visit_id,
                $new_status,
                $this->customlib->getStaffID(),
                $role_id
            );
            if ($result['status']) {
                $this->session->set_flashdata('success_msg', $result['message']);
                if (!empty($result['token_no'])) {
                    $this->session->set_flashdata('token_no', $result['token_no']);
                    $this->session->set_flashdata('token_patient', $visit ? $visit->patient_name : '');
                }
            } else {
                $this->session->set_flashdata('error_msg', $result['message']);
            }
        }
        redirect('admin/patientflow');
    }

    /**
     * Mark a visit's payment status as PAID (one-way action).
     * Used from the board action column. Only a cashier (role owns a level with
     * can_change_payment checked) or a superadmin may do this. Server-side
     * permission is enforced here, not just hidden in the UI.
     */
    public function mark_paid($visit_id = 0)
    {
        if (!$this->rbac->hasPrivilege('patient_flow', 'can_view')) {
            access_denied();
        }

        $visit_id = intval($visit_id);
        if ($visit_id) {
            $ctx = $this->pf_context();

            $visit = $this->patientflow_model->get_visit($visit_id);

            // Payment-status permission: superadmin, or a staff whose role
            // owns the visit's CURRENT level AND that level has the
            // can_change_payment flag checked (scoped to the current level).
            $is_cashier = $ctx['is_superadmin'];
            if (!$is_cashier) {
                if (!$visit) {
                    $this->session->set_flashdata('error_msg', 'Visit not found.');
                    redirect('admin/patientflow');
                    return;
                }
                $owns_level = false;
                foreach ($ctx['my_levels'] as $ml) {
                    if ((int) $ml->id === (int) $visit->current_level_id) { $owns_level = true; break; }
                }
                $is_cashier = $owns_level && !empty($visit->level_can_change_payment);
            }
            if (!$is_cashier) {
                access_denied();
                return;
            }

            if ($visit && $visit->payment_status === 'paid') {
                // Already paid — nothing to do.
                $this->session->set_flashdata('info', $this->lang->line('already_paid'));
                redirect('admin/patientflow');
                return;
            }

            $role_id = null;
            $role_json = $this->customlib->getStaffRole();
            if ($role_json) {
                $role_data = json_decode($role_json, true);
                $role_id = isset($role_data['id']) ? $role_data['id'] : null;
            }

            $result = $this->patientflow_model->set_payment_status(
                $visit_id,
                'paid',
                $this->customlib->getStaffID(),
                $role_id,
                false // Do NOT auto-generate a token when marking as paid.
            );
            if ($result['status']) {
                $this->session->set_flashdata('success', $result['message']);
                if (!empty($result['token_no'])) {
                    $this->session->set_flashdata('token_no', $result['token_no']);
                    $this->session->set_flashdata('token_patient', $visit ? $visit->patient_name : '');
                }
            } else {
                $this->session->set_flashdata('error', $result['message']);
            }
        }
        redirect('admin/patientflow');
    }
    /**
     * Toggle a visit's cash-received status (0 <-> 1).
     * Used from the board action column. Only a staff whose level has the
     * can_cash_received flag checked (or a superadmin) may do this. Server-side
     * permission is enforced here, not just hidden in the UI.
     */
    public function toggle_cash_received($visit_id = 0)
    {
        if (!$this->rbac->hasPrivilege('patient_flow', 'can_view')) {
            access_denied();
        }

        $visit_id = intval($visit_id);
        if ($visit_id) {
            $ctx = $this->pf_context();

            $visit = $this->patientflow_model->get_visit($visit_id);
            if (!$visit) {
                $this->session->set_flashdata('error_msg', 'Visit not found.');
                redirect('admin/patientflow');
                return;
            }

            // Superadmin bypass, else the staff must own the visit's CURRENT
            // level AND that level must have the can_cash_received flag.
            $allowed = $ctx['is_superadmin'];
            if (!$allowed) {
                $owns_level = false;
                foreach ($ctx['my_levels'] as $ml) {
                    if ((int) $ml->id === (int) $visit->current_level_id) { $owns_level = true; break; }
                }
                $allowed = $owns_level && !empty($visit->level_can_cash_received);
            }
            if (!$allowed) {
                access_denied();
                return;
            }

            $role_id = null;
            $role_json = $this->customlib->getStaffRole();
            if ($role_json) {
                $role_data = json_decode($role_json, true);
                $role_id = isset($role_data['id']) ? $role_data['id'] : null;
            }

            $new_received = (int) $visit->cash_received ? 0 : 1;
            $result = $this->patientflow_model->set_cash_received(
                $visit_id,
                $new_received,
                $this->customlib->getStaffID(),
                $role_id
            );
            if ($result['status']) {
                $this->session->set_flashdata('success', $result['message']);
            } else {
                $this->session->set_flashdata('error', $result['message']);
            }
        }
        redirect('admin/patientflow');
    }

    /**
     * Set a visit's programme status (enroll | declined | deferred).
     * Only a staff whose level has the can_change_programme_status flag
     * checked (or a superadmin) may do this. Server-side enforced.
     */
    public function change_programme_status($visit_id = 0)
    {
        if (!$this->rbac->hasPrivilege('patient_flow', 'can_view')) {
            access_denied();
        }

        $visit_id = intval($visit_id);
        $status = $this->input->post('programme_status');
        if ($visit_id && $this->input->method() === 'post') {
            $ctx = $this->pf_context();

            if (!$this->pf_has_level_flag($ctx, 'can_change_programme_status')) {
                access_denied();
                return;
            }

            $role_id = null;
            $role_json = $this->customlib->getStaffRole();
            if ($role_json) {
                $role_data = json_decode($role_json, true);
                $role_id = isset($role_data['id']) ? $role_data['id'] : null;
            }

            $result = $this->patientflow_model->set_programme_status(
                $visit_id,
                $status,
                $this->customlib->getStaffID(),
                $role_id
            );

            if ($this->input->is_ajax_request()) {
                header('Content-Type: application/json');
                echo json_encode($result);
                return;
            }
            if ($result['status']) {
                $this->session->set_flashdata('success', $result['message']);
            } else {
                $this->session->set_flashdata('error', $result['message']);
            }
        }
        redirect('admin/patientflow');
    }

    /**
     * Get programme details for a visit (used by modal).
     */
    public function get_programme_details()
    {

        if(!empty($this->input->post('visit_id'))){
            $visit_id = $this->input->post('visit_id');
        } else {
            $visit_id = 0;

        }

        if (!$this->rbac->hasPrivilege('patient_flow', 'can_view')) {
            header('Content-Type: application/json');
            echo json_encode(array('status' => false, 'message' => 'Access denied.'));
            return;
        }

        $visit_id = (int) $visit_id;
        if ($visit_id <= 0) {
            header('Content-Type: application/json');
            echo json_encode(array('status' => false, 'message' => 'Invalid visit ID.'));
            return;
        }

        $visit = $this->patientflow_model->get_visit($visit_id);
        if (!$visit) {
            header('Content-Type: application/json');
            echo json_encode(array('status' => false, 'message' => 'Visit not found.'));
            return;
        }

        $programme_id = (int) ($visit->ct_programme_id ?? 0);
        if ($programme_id <= 0) {
            header('Content-Type: application/json');
            echo json_encode(array('status' => false, 'message' => 'No programme assigned to this visit.'));
            return;
        }

        $programme = $this->patientflow_model->get_programme_by_id($programme_id);
        if (!$programme) {
            header('Content-Type: application/json');
            echo json_encode(array('status' => false, 'message' => 'Programme not found.'));
            return;
        }

        // Get the current programme status from the visit
        $visit_status = $visit->ct_programme_status ?? 'enroll';

        header('Content-Type: application/json');
        echo json_encode(array(
            'status' => true,
            'programme' => array(
                'id' => (int) $programme->id,
                'code' => (string) $programme->code,
                'name' => (string) $programme->name,
            ),
            'current_status' => $visit_status,
        ));
    }

    /**
     * Generate / activate a queue token for a visit.
    */
    public function generate_token($visit_id)
    {
        if (!$this->rbac->hasPrivilege('patient_flow', 'can_add')) {
            access_denied();
        }

        $visit_id = intval($visit_id);
        if ($visit_id) {
            $ctx = $this->pf_context();

            $may_generate = $this->pf_has_level_flag($ctx, 'can_generate_token');
            $may_generate = $may_generate
                || $this->patientflow_model->can_manage_token($visit_id, $ctx['role_ids']);
            if (!$may_generate) {
                access_denied();
                return;
            }

            $visit = $this->patientflow_model->get_visit($visit_id);

            $role_id = null;
            $role_json = $this->customlib->getStaffRole();
            if ($role_json) {
                $role_data = json_decode($role_json, true);
                $role_id = isset($role_data['id']) ? $role_data['id'] : null;
            }

            $result = $this->patientflow_model->generate_token(
                $visit_id,
                $this->customlib->getStaffID(),
                $role_id
            );
            if ($result['status']) {
                $this->session->set_flashdata('success', $result['message']);
                if (!empty($result['token_no'])) {
                    $this->session->set_flashdata('token_no', $result['token_no']);
                    $this->session->set_flashdata('token_patient', $visit ? $visit->patient_name : '');
                }
            } else {
                $this->session->set_flashdata('error', $result['message']);
            }
        }
        redirect('admin/patientflow');
    }

    // ============================================================
    // Token Queue (P1-L5 Token Control / R1)
    // ============================================================

    /**
     * Token queue board (P1-L5 Token Control). Only paid patients enter the
     * doctor queue — R1 activates the token after payment confirmation and
     * manages the queue until the consultation is completed.
     */
    public function token_queue()
    {
        if (!$this->rbac->hasPrivilege('patient_flow', 'can_view')) {
            access_denied();
        }

        $this->session->set_userdata('top_menu', 'patient_flow');
        $this->session->set_userdata('sub_menu', 'patient_flow_token_queue');

        $ctx = $this->pf_context();

        // Status filter (?status=WAITING etc). Guard restricted users so they
        // cannot probe foreign statuses beyond their levels.
        $filter_status = strtoupper(trim((string) $this->input->get('status', true)));

        $data['title']         = $this->lang->line('token_queue') ?: 'Token Queue';
        $data['token_queue']   = $this->patientflow_model->get_token_queue(
            $filter_status !== '' ? $filter_status : null,
            $ctx['allowed_level_ids'],
            200
        );
        $data['token_statuses']   = $this->patientflow_model->token_statuses();
        $data['token_transitions'] = $this->patientflow_model->token_transitions();
        $data['filter_status']    = $filter_status;
        $data['pf_is_superadmin'] = $ctx['is_superadmin'];
        $data['pf_my_levels']     = $ctx['my_levels'];

        // Token Control (R1) flag: superadmin or a staff whose role owns the
        // P1-L5 token control level may manage the queue. Also grants access to
        // any level's owner with the can_generate_token flag checked in the Levels form.

        $data['pf_is_r1'] = $ctx['is_superadmin'];
        if (!$data['pf_is_r1']) {
            $data['pf_is_r1'] = $this->pf_has_level_flag($ctx, 'can_generate_token');
        }
        if (!$data['pf_is_r1']) {
            $first_level = $this->patientflow_model->get_first_level();
            if ($first_level) {
                foreach ($ctx['my_levels'] as $ml) {
                    if ((int) $ml->id === (int) $first_level->id) {
                        $data['pf_is_r1'] = true;
                        break;
                    }
                }
            }
        }

        $this->load->view('layout/header', $data);
        $this->load->view('admin/patientflow/token_queue', $data);
        $this->load->view('layout/footer', $data);
    }

    /**
     * Move a visit's token to a new lifecycle status (JSON).
     * Guard: superadmin, or the staff's role owns the visit's current level
     * AND that level is the P1-L5 token control counter (R1).
     */
    public function set_token_status()
    {
        if (!$this->rbac->hasPrivilege('patient_flow', 'can_add')) {
            access_denied();
        }

        $visit_id   = intval($this->input->post('visit_id', true));
        $new_status = strtoupper(trim((string) $this->input->post('status', true)));
        $remarks    = trim((string) $this->input->post('remarks', true));

        if (!$visit_id || $new_status === '') {
            echo json_encode(array('status' => false, 'message' => 'Visit id and status are required.'));
            return;
        }

        $ctx = $this->pf_context();
        $may_manage = $ctx['is_superadmin']
            || $this->patientflow_model->can_manage_token($visit_id, $ctx['role_ids']);

        if (!$may_manage) {
            echo json_encode(array('status' => false, 'message' => 'You are not allowed to manage the token queue for this visit.'));
            return;
        }

        $role_id = null;
        $role_json = $this->customlib->getStaffRole();
        if ($role_json) {
            $role_data = json_decode($role_json, true);
            $role_id = isset($role_data['id']) ? $role_data['id'] : null;
        }

        $result = $this->patientflow_model->set_token_status(
            $visit_id,
            $new_status,
            $this->customlib->getStaffID(),
            $role_id,
            $remarks
        );
        echo json_encode($result);
    }

    /**
     * Create a PBHP id for a visit (AJAX, called from the board's
     * Create-PBHP-ID modal). Guarded by the can_create_pbhp_id level flag
     * (or superadmin). Only visits without a PBHP id can get one.
     */
    public function create_pbhp()
    {
        if (!$this->rbac->hasPrivilege('patient_flow', 'can_add')) {
            access_denied();
        }

        $visit_id = intval($this->input->post('visit_id', true));
        $code     = strtoupper(trim((string) $this->input->post('pbhp_code', true)));

        if (!$visit_id || $code === '') {
            echo json_encode(array('status' => false, 'message' => 'Visit id and PBHP code are required.'));
            return;
        }

        $ctx = $this->pf_context();
        if (!$ctx['is_superadmin'] && !$this->pf_has_level_flag($ctx, 'can_create_pbhp_id')) {
            echo json_encode(array('status' => false, 'message' => 'You are not allowed to create PBHP IDs.'));
            return;
        }

        // The submitted code must be one of the known codes.
        if (!in_array($code, array_values($this->patientflow_model->pbhp_codes()), true)) {
            echo json_encode(array('status' => false, 'message' => 'Invalid PBHP code.'));
            return;
        }

        $role_id = null;
        $role_json = $this->customlib->getStaffRole();
        if ($role_json) {
            $role_data = json_decode($role_json, true);
            $role_id = isset($role_data['id']) ? $role_data['id'] : null;
        }

        $result = $this->patientflow_model->create_pbhp_id(
            $visit_id,
            $code,
            $this->customlib->getStaffID(),
            $role_id
        );
        echo json_encode($result);
    }

    /**
     * Send a visit back to the previous step in the chain.
     * Guard: superadmin, or the staff's role owns the visit's CURRENT level
     * (the level the patient was wrongly forwarded to).
     */
    public function backward($visit_id = 0)
    {
        if (!$this->rbac->hasPrivilege('patient_flow', 'can_add')) {
            access_denied();
        }

        $visit_id = intval($visit_id);
        if ($visit_id) {
            $ctx = $this->pf_context();

            $may_backward = $ctx['is_superadmin']
                || $this->patientflow_model->can_backward($visit_id, $ctx['role_ids']);

            if (!$may_backward) {
                access_denied();
                return;
            }

            $role_id = null;
            $role_json = $this->customlib->getStaffRole();
            if ($role_json) {
                $role_data = json_decode($role_json, true);
                $role_id = isset($role_data['id']) ? $role_data['id'] : null;
            }

            $pf_before = $this->patientflow_model->get_visit($visit_id);
            $result = $this->patientflow_model->backward_visit(
                $visit_id,
                $this->customlib->getStaffID(),
                $role_id
            );
            if ($result['status']) {
                $this->session->set_flashdata('success_msg', $result['message']);

                // Notify the receiving (previous) counter's role + Super Admin.
                $pf_name = ($pf_before && $pf_before->patient_name) ? $pf_before->patient_name : ('Patient #' . $visit_id);
                $pf_desc = $pf_name . ' (' . ($pf_before ? $pf_before->visit_no : '') . ') was sent back to ' . ($pf_before ? $pf_before->prev_title : 'previous step') . '.';
                $pf_role = $this->patientflow_model->get_level_role_name($pf_before ? $pf_before->prev_level_id : null);
                $this->patientflow_model->notify_staff($pf_role, $this->lang->line('pf_notify_backward_title') ?: 'Patient sent back', $pf_desc);
                $this->patientflow_model->notify_staff('Super Admin', $this->lang->line('pf_notify_backward_title') ?: 'Patient sent back', $pf_desc);
            } else {
                $this->session->set_flashdata('error_msg', $result['message']);
            }
        }
        redirect('admin/patientflow');
    }

    /**
     * Advance a visit to the next level in the chain.
     */
    public function forward($visit_id = 0)
    {
        if (!$this->rbac->hasPrivilege('patient_flow', 'can_add')) {
            access_denied();
        }

        $visit_id = intval($visit_id);
        if ($visit_id) {
            $ctx = $this->pf_context();

            // Server-side ownership guard: only the role that owns the visit's
            // current level may forward it (superadmin bypasses).
            $may_forward = $ctx['is_superadmin']
                || $this->patientflow_model->can_forward($visit_id, $ctx['role_ids']);

            if (!$may_forward) {
                access_denied();
                return;
            }

            $role_json = $this->customlib->getStaffRole();
            $role_id = null;
            if ($role_json) {
                $role_data = json_decode($role_json, true);
                $role_id = isset($role_data['id']) ? $role_data['id'] : null;
            }

            // Charges come from the linked OPD's data — sync the visit amount
            // from the OPD's net charge (patient_charges.amount, after tax and
            // discount) before advancing, so the downstream payment counters
            // always see the final figure. No charge popup is involved anymore.
            $this->patientflow_model->sync_visit_amount_from_opd($visit_id);

            $pf_before = $this->patientflow_model->get_visit($visit_id);
            $result = $this->patientflow_model->advance_visit(
                $visit_id,
                $this->customlib->getStaffID(),
                $role_id
            );
            if ($result['status']) {
                $this->session->set_flashdata('success_msg', $this->lang->line('visit_forwarded'));

                // Notify the receiving counter's role + Super Admin.
                $pf_name = ($pf_before && $pf_before->patient_name) ? $pf_before->patient_name : ('Patient #' . $visit_id);
                $pf_desc = $pf_name . ' (' . ($pf_before ? $pf_before->visit_no : '') . ') was forwarded to ' . $result['next_title'] . '.';
                $pf_role = $this->patientflow_model->get_level_role_name($result['next_level_id']);
                $this->patientflow_model->notify_staff($pf_role, $this->lang->line('pf_notify_forward_title') ?: 'Patient moved forward', $pf_desc);
                $this->patientflow_model->notify_staff('Super Admin', $this->lang->line('pf_notify_forward_title') ?: 'Patient moved forward', $pf_desc);
            } else {
                $this->session->set_flashdata('error_msg', $result['message']);
            }
        }
        redirect('admin/patientflow');
    }

    // ============================================================
    // Level-2 (R2) Billing Reference + Billing Status
    // ============================================================

    /**
     * Generate the R2 billing reference (BR-KTR-YYMMDD-######) for a visit.
     * Only a staff whose role owns the visit's current level AND that level is
     * the phase-1 level-2 billing counter (R2) may generate it (superadmin
     * bypasses). Idempotent: refuses to regenerate once a reference exists.
     */
    public function generate_billing_reference($visit_id = 0)
    {
        if (!$this->rbac->hasPrivilege('patient_flow', 'can_add')) {
            access_denied();
        }

        $visit_id = intval($visit_id);
        if ($visit_id) {
            $ctx = $this->pf_context();

            // Level-flag permission: superadmin, or a staff whose role owns a
            // level with can_generate_billing_ref checked in the Levels form.
            $may_manage = $this->pf_has_level_flag($ctx, 'can_generate_billing_ref');

            if (!$may_manage) {
                access_denied();
                return;
            }

            $role_id = null;
            $role_json = $this->customlib->getStaffRole();
            if ($role_json) {
                $role_data = json_decode($role_json, true);
                $role_id = isset($role_data['id']) ? $role_data['id'] : null;
            }

            $reference = $this->patientflow_model->generate_billing_reference();
            $result = $this->patientflow_model->set_billing_reference(
                $visit_id,
                $reference,
                $this->customlib->getStaffID(),
                $role_id
            );

            if ($result['status']) {
                $this->session->set_flashdata('success_msg', $result['message']);
            } else {
                $this->session->set_flashdata('error_msg', $result['message']);
            }
        }
        redirect('admin/patientflow');
    }

    /**
     * Generate an invoice number for a visit (same functionality as the
     * billing reference, with its own level flag can_generate_invoice).
     * Permission: superadmin, or a staff whose role owns the visit's CURRENT
     * level with can_generate_invoice checked in the Levels form.
     */
    public function generate_invoice($visit_id = 0)
    {
        if (!$this->rbac->hasPrivilege('patient_flow', 'can_add')) {
            access_denied();
        }

        $visit_id = intval($visit_id);
        if ($visit_id) {
            $ctx = $this->pf_context();
            $visit = $this->patientflow_model->get_visit($visit_id);
            if (!$visit) {
                access_denied();
                return;
            }

            $is_superadmin = $ctx['is_superadmin'];
            $may_manage = $is_superadmin;
            if (!$may_manage) {
                if (!$visit) {
                    $this->session->set_flashdata('error_msg', 'Visit not found.');
                    redirect('admin/patientflow');
                    return;
                }
                // Ownership: staff must own the visit's CURRENT level AND that
                // level must have can_generate_invoice checked (scoped check).
                $owns_level = false;
                foreach ($ctx['my_levels'] as $ml) {
                    if ((int) $ml->id === (int) $visit->current_level_id) { $owns_level = true; break; }
                }
                $may_manage = $owns_level && !empty($visit->level_can_generate_invoice);
            }

            if (!$may_manage) {
                access_denied();
                return;
            }

            $role_id = null;
            $role_json = $this->customlib->getStaffRole();
            if ($role_json) {
                $role_data = json_decode($role_json, true);
                $role_id = isset($role_data['id']) ? $role_data['id'] : null;
            }

            $invoice_no = $this->patientflow_model->generate_invoice_no();
            $result = $this->patientflow_model->set_invoice_no(
                $visit_id,
                $invoice_no,
                $this->customlib->getStaffID(),
                $role_id
            );

            if ($result['status']) {
                $this->session->set_flashdata('success_msg', $result['message']);
            } else {
                $this->session->set_flashdata('error_msg', $result['message']);
            }
        }
        redirect('admin/patientflow');
    }

    /**
     * Change the billing status of a visit (pending | billed | paid | cancelled).
     * Same R2 ownership guard as generate_billing_reference().
     */
    public function set_billing_status($visit_id = 0, $status = '')
    {
        if (!$this->rbac->hasPrivilege('patient_flow', 'can_add')) {
            access_denied();
        }

        $visit_id = intval($visit_id);
        // The board's dropdown posts the status in the POST body; a plain URL
        // like set_billing_status/{id}/{status} passes it as a URI segment.
        // Accept both so either entry point works.
        $posted_status = $this->input->post('status', true);
        $status = ($posted_status !== null && $posted_status !== '')
            ? trim((string) $posted_status)
            : trim((string) $status);

        if ($visit_id && in_array($status, $this->patientflow_model->billing_statuses(), true)) {
            $ctx = $this->pf_context();

            // Level-flag permission: superadmin, or a staff whose role owns a
            // level with can_generate_billing_ref checked in the Levels form.
            $may_manage = $this->pf_has_level_flag($ctx, 'can_generate_billing_ref');

            if (!$may_manage) {
                access_denied();
                return;
            }

            $role_id = null;
            $role_json = $this->customlib->getStaffRole();
            if ($role_json) {
                $role_data = json_decode($role_json, true);
                $role_id = isset($role_data['id']) ? $role_data['id'] : null;
            }

            $result = $this->patientflow_model->set_billing_status(
                $visit_id,
                $status,
                $this->customlib->getStaffID(),
                $role_id
            );

            if ($result['status']) {
                $this->session->set_flashdata('success_msg', $result['message']);
            } else {
                $this->session->set_flashdata('error_msg', $result['message']);
            }
        } else {
            $this->session->set_flashdata('error_msg', 'Invalid billing status.');
        }
        redirect('admin/patientflow');
    }

    /**
     * Full-page printable billing slip (opens in a new tab). Requires the
     * can_print_billing level flag (superadmin bypasses); the billing
     * management controls are NOT rendered here.
     */
    public function print_billing($visit_id = 0)
    {
        if (!$this->rbac->hasPrivilege('patient_flow', 'can_view')) {
            access_denied();
        }

        // Level-flag permission: superadmin, or a staff whose role owns a
        // level with can_print_billing checked in the Levels form.
        $ctx = $this->pf_context();
        if (!$this->pf_has_level_flag($ctx, 'can_print_billing')) {
            access_denied();
            return;
        }

        $visit_id = intval($visit_id);
        if (!$visit_id) {
            redirect('admin/patientflow');
        }

        $visit = $this->patientflow_model->get_visit($visit_id);
        if (!$visit) {
            redirect('admin/patientflow');
        }

        // Charges are defined on the reference OPD (patient_charges rows linked
        // to the visit's opd_id), NOT on the visit. Fetch them live from the OPD
        // so the slip always shows the exact figure the OPD module calculated.
        $opd_charges = array();
        if (!empty($visit->opd_id)) {
            $opd_charges = $this->charge_model->getopdCharges($visit->opd_id);
        }

        // Net OPD figure (after discount/tax) — the same number the OPD module
        // shows. Falls back to the stored visit amount only for legacy visits
        // that have no linked OPD yet.
        $opd_net_amount = !empty($visit->opd_id)
            ? $this->patientflow_model->get_opd_net_amount($visit->opd_id)
            : (float) $visit->amount;

        // Role-scoped visibility: staff may only print slips for visits parked
        // at levels their role owns (superadmin sees everything).
        $ctx = $this->pf_context();
        if (!$ctx['is_superadmin'] && is_array($ctx['allowed_level_ids'])
            && !in_array((int) $visit->current_level_id, $ctx['allowed_level_ids'], true)) {
            access_denied();
            return;
        }

        // Determine whether the patient has moved past the cashier's level.
        // If the patient's current level_no is greater than the cashier's level_no,
        // the billing reference should be hidden and replaced with "Billing Invoice".
        $cashier_level_no = null;
        if (!empty($ctx['my_levels'])) {
            foreach ($ctx['my_levels'] as $ml) {
                if (!empty($ml->can_generate_billing_ref)) {
                    if ($cashier_level_no === null || (int) $ml->level_no < $cashier_level_no) {
                        $cashier_level_no = (int) $ml->level_no;
                    }
                }
            }
        }
        $patient_level_no = isset($visit->level_no) ? (int) $visit->level_no : 0;
        $data['hide_billing_ref'] = ($cashier_level_no !== null && $patient_level_no > $cashier_level_no);

        $data['visit']            = $visit;
        $data['opd_charges']      = $opd_charges;
        $data['opd_net_amount']   = $opd_net_amount;
        $data['billing_statuses'] = $this->patientflow_model->billing_statuses();
        $data['currency_symbol']  = $this->customlib->getHospitalCurrencyFormat();
        $setting_rows             = $this->setting_model->get();
        $data['setting']          = !empty($setting_rows) ? $setting_rows[0] : array();

        // Fetch prescribed medicines for this visit
        $data['visit_medicines'] = $this->patientflow_model->get_visit_medicines($visit_id);

        $this->load->view('admin/patientflow/print_billing', $data);
    }

    /**
     * Printable invoice for a generated programme bill.
     */
    public function print_programme_bill($visit_id = 0)
    {
        if (!$this->rbac->hasPrivilege('patient_flow', 'can_view')) {
            access_denied();
            return;
        }

        $visit_id = intval($visit_id);
        if (!$visit_id) {
            redirect('admin/patientflow');
        }

        $visit = $this->patientflow_model->get_visit($visit_id);
        if (!$visit) {
            redirect('admin/patientflow');
        }

        $invoice = null;
        if ($this->db->field_exists('invoice_no', 'patient_flow_visits') && !empty($visit->invoice_no)) {
            $invoice = $this->db
                ->where('invoice_type', 'PROGRAMME')
                ->where('invoice_no', trim((string) $visit->invoice_no))
                ->get('patient_flow_fee_invoice')
                ->row();
        }

        if (!$invoice) {
            $this->session->set_flashdata('error_msg', 'Programme bill invoice not found for this visit.');
            redirect('admin/patientflow');
        }

        $lines = $this->db->where('invoice_id', (int) $invoice->id)->get('patient_flow_fee_invoice_line')->result();
        $schedule_id = 0;
        foreach ($lines as $line) {
            if (!empty($line->schedule_id)) {
                $schedule_id = (int) $line->schedule_id;
                break;
            }
        }

        $schedule = null;
        $enrollment = null;
        $programme_name = 'Programme';
        $programme_group_name = '—';
        $benefit_meta = array(
            'source' => null,
            'source_name' => '—',
            'source_code' => '—',
            'discount_percent' => 0,
            'payment_mode' => 'MILESTONE',
        );

        if ($schedule_id > 0) {
            $schedule = $this->db->where('id', $schedule_id)->get('patient_flow_programme_payment_schedule')->row();
            if ($schedule && !empty($schedule->enrollment_id) && $this->db->table_exists('patient_flow_patient_programme_enrollment')) {
                $enrollment = $this->db->where('id', (int) $schedule->enrollment_id)->get('patient_flow_patient_programme_enrollment')->row();
                if ($enrollment && !empty($enrollment->programme_id) && $this->db->table_exists('patient_flow_programme')) {
                    $prog = $this->db->where('id', (int) $enrollment->programme_id)->get('patient_flow_programme')->row();
                    if ($prog) {
                        $programme_name = trim((string) ($prog->name ?: ($prog->code ?: 'Programme')));
                    }
                }

                if ($this->db->table_exists('patient_flow_programme_group') && !empty($enrollment->group_id)) {
                    $group = $this->db->where('id', (int) $enrollment->group_id)->get('patient_flow_programme_group')->row();
                    if ($group) {
                        $programme_group_name = trim((string) ($group->name ?: ($group->code ?: 'Programme Group')));
                    }
                }

                if ($this->db->table_exists('patient_flow_enrollment_benefit')) {
                    $benefit = $this->db->where('enrollment_id', (int) $schedule->enrollment_id)
                        ->where('is_current', 1)
                        ->order_by('id', 'DESC')
                        ->get('patient_flow_enrollment_benefit')
                        ->row();

                    if ($benefit) {
                        $benefit_meta['source'] = trim((string) ($benefit->source ?? ''));
                        $benefit_meta['discount_percent'] = (float) ($benefit->discount_percent ?? 0);
                        $benefit_meta['payment_mode'] = trim((string) ($benefit->payment_mode ?: 'MILESTONE'));

                        if ($benefit_meta['source'] === 'ASSISTANCE' && $this->db->table_exists('patient_flow_fee_assistance_category')) {
                            $source_row = $this->db->where('id', (int) ($benefit->source_id ?? 0))->get('patient_flow_fee_assistance_category')->row();
                            if ($source_row) {
                                $benefit_meta['source_name'] = trim((string) ($source_row->category_name ?: ($source_row->category_code ?: 'Assistance')));
                                $benefit_meta['source_code'] = trim((string) ($source_row->category_code ?: 'A'));
                            }
                        } elseif ($benefit_meta['source'] === 'CAMPAIGN' && $this->db->table_exists('patient_flow_discount_campaign')) {
                            $source_row = $this->db->where('id', (int) ($benefit->source_id ?? 0))->get('patient_flow_discount_campaign')->row();
                            if ($source_row) {
                                $benefit_meta['source_name'] = trim((string) ($source_row->reason ?: ($source_row->campaign_code ?: 'Campaign')));
                                $benefit_meta['source_code'] = trim((string) ($source_row->campaign_code ?: 'C'));
                            }
                        }
                    }
                }
            }
        }

        $setting_rows = $this->setting_model->get();
        $data = array(
            'visit' => $visit,
            'invoice' => $invoice,
            'lines' => $lines,
            'schedule' => $schedule,
            'enrollment' => $enrollment,
            'programme_name' => $programme_name,
            'programme_group_name' => $programme_group_name,
            'benefit_meta' => $benefit_meta,
            'currency_symbol' => $this->customlib->getHospitalCurrencyFormat(),
            'setting' => !empty($setting_rows) ? $setting_rows[0] : array(),
        );

        $this->load->view('admin/patientflow/print_programme_bill', $data);
    }

    /**
     * Generate and display a payment QR code for a patient flow visit.
     * Gated to level 2 (billing) management: superadmin, or a staff whose
     * role owns the visit's current level when that level is level 2.
     */
    public function generate_qr_code($visit_id = 0)
    {
        if (!$this->rbac->hasPrivilege('patient_flow', 'can_view')) {
            access_denied();
            return;
        }
        $visit_id = intval($visit_id);
        if (!$visit_id) {
            redirect('admin/patientflow');
        }

        $visit = $this->patientflow_model->get_visit($visit_id);
        if (!$visit) {
            redirect('admin/patientflow');
        }

        // Build the payment URL that the QR will encode.
        $setting_rows = $this->setting_model->get();
        $setting = !empty($setting_rows) ? $setting_rows[0] : array();
        $baseUrl = !empty($setting['base_url']) ? rtrim($setting['base_url'], '/') : base_url();

        // Use billing_reference if available, otherwise visit_no, otherwise show an error.
        $paymentRef = !empty($visit->billing_reference) ? $visit->billing_reference : $visit->visit_no;

        if (empty($paymentRef)) {
            $this->session->set_flashdata('error_msg', 'Cannot generate QR code: no billing reference or visit number available for this visit.');
            redirect('admin/patientflow');
        }

        $paymentUrl = $baseUrl . '/admin/patientflow/pay?ref=' . urlencode($paymentRef);

        // Generate the QR image using the patientflow-specific QR library.
        $this->load->library('Patientflow_qr');
        $uploadPath = $this->customlib->getFolderPath() . 'uploads/patientflow/qrcode/';
        if (!is_dir($uploadPath)) {
            @mkdir($uploadPath, 0777, true);
        }
        // Ensure directory is writable by the web server process
        if (is_dir($uploadPath) && !is_writable($uploadPath)) {
            @chmod($uploadPath, 0777);
        }
        $fileName = 'pf_visit_' . $visit_id;
        $generated = $this->patientflow_qr->generate($uploadPath, $paymentUrl, $fileName);

        if (!$generated) {

            $this->session->set_flashdata('error_msg', 'Failed to generate QR code.');
            redirect('admin/patientflow');
        }

        $data['visit']          = $visit;
        $data['qr_image']       = 'uploads/patientflow/qrcode/' . $fileName . '.png';
        $data['payment_url']    = $paymentUrl;
        $data['setting']        = $setting;
        $data['currency_symbol'] = $this->customlib->getHospitalCurrencyFormat();

        $this->load->view('admin/patientflow/qr_code', $data);
    }

    /**
     * Front-end payment page: loads when a user scans the patientflow QR code
     * or opens the payment URL (admin/patientflow/pay?ref=...).
     * Looks up the visit by billing_reference or visit_no and renders the
     * payment view.
     */
    public function pay()
    {
        $ref = trim((string) $this->input->get('ref', true));
        if ($ref === '') {
            show_error('No billing reference provided.', 400, 'Invalid Request');
            return;
        }

        // Look up the visit by billing_reference, falling back to visit_no.
        $this->db->select('v.*, p.patient_name, p.mobileno, p.gender, p.age, p.dob, p.patient_uuid, p.guardian_name, p.known_allergies, p.email, p.address, p.identification_number, l.code AS level_code, l.title AS level_title');
        $this->db->from('patient_flow_visits v');
        $this->db->join('patients p', 'p.id = v.patient_id', 'left');
        $this->db->join('patient_flow_levels l', 'l.id = v.current_level_id', 'left');
        $this->db->where('(v.billing_reference = ' . $this->db->escape($ref) . ' OR v.visit_no = ' . $this->db->escape($ref) . ')');
        $this->db->limit(1);
        $visit = $this->db->get()->row();

        if (!$visit) {
            show_error('Visit not found for the provided reference: ' . htmlspecialchars($ref), 404, 'Not Found');
            return;
        }

        $data['visit']            = $visit;
        $data['patient']          = (array) $visit;
        $data['payment_ref']      = $ref;
        $data['total_amount']     = 0; // will be set based on visit charges
        $data['currency_symbol']  = $this->customlib->getHospitalCurrencyFormat();
        $data['patient_name']     = $visit->patient_name;
        $data['visit_no']         = $visit->visit_no;
        $data['billing_reference']= $visit->billing_reference;
        $data['payment_mode']     = $this->config->item('payment_mode');
        $data['setting']          = $this->setting_model->get();

        $this->load->view('admin/patientflow/pay', $data);
    }

    /**
     * Full-page view: the ordered chain of levels + role hierarchy editor.
     */
    // ============================================================
    // Consultation Categories (single page: listing + add form)
    // ============================================================

    /**
     * AJAX: enroll a patient into the patient flow (first level) after an OPD
     * was saved from the board's OPD Form modal. ONLY a staff whose role owns
     * the FIRST level (R1) — or a superadmin — may enroll. Silently skips
     * otherwise and for patients that already have a visit in the flow.
     */
    public function enroll_from_opd()
    {
        $patient_id = (int) $this->input->post('patient_id');
        $amount     = $this->input->post('amount');
        $amount     = ($amount !== '' && $amount !== null && is_numeric($amount)) ? (float) $amount : 0;

        if (!$patient_id) {
            echo json_encode(array('status' => false, 'message' => 'No patient.'));
            return;
        }

        $ctx = $this->pf_context();

        // R1-only rule: the staff must own the FIRST level (superadmin bypasses).
        $first_level = $this->patientflow_model->get_first_level();
        $is_r1 = $ctx['is_superadmin'];
        if (!$is_r1 && $first_level) {
            foreach ($ctx['my_levels'] as $ml) {
                if ((int) $ml->id === (int) $first_level->id) { $is_r1 = true; break; }
            }
        }

        if (!$is_r1 || !$first_level) {
            echo json_encode(array('status' => false, 'message' => 'Not allowed.'));
            return;
        }

        // Skip if the patient already has a visit in the flow — but still sync
        // the OPD that was just saved (via the board's OPD modal) into the
        // existing visit, otherwise the OPD id stays unlinked (e.g. a patient
        // created from the flow's New Patient modal gets its OPD afterwards).
        $existing = $this->patientflow_model->find_active_visit_by_patient($patient_id);
        if ($existing) {
            $posted_opd_id = (int) $this->input->post('opd_id');
            $latest_opd_id = (int) $this->patientflow_model->get_latest_opd_id_by_patient($patient_id);
            $opd_id = $posted_opd_id ? $posted_opd_id : $latest_opd_id;

            if ($opd_id) {
                if (empty($existing->opd_id)) {
                    $this->patientflow_model->set_visit_opd_id((int) $existing->id, $opd_id);
                } elseif ((int) $existing->opd_id !== $opd_id) {
                    // A NEWER OPD was just saved for this visit — keep it linked.
                    $this->patientflow_model->set_visit_opd_id((int) $existing->id, $opd_id);
                }
                $this->patientflow_model->sync_visit_amount_from_opd((int) $existing->id);
                $this->patientflow_model->add_log(array(
                    'visit_id' => (int) $existing->id,
                    'to_level_id' => $existing->current_level_id,
                    'action'    => 'opd_linked',
                    'remarks'   => 'OPD linked (OPD ID: ' . $opd_id . ')',
                    'acted_by'  => $this->customlib->getStaffID(),
                ));
            }

            echo json_encode(array('status' => true, 'message' => 'Already in flow.', 'visit_id' => (int) $existing->id));
            return;
        }

        $visit_no = $this->patientflow_model->generate_visit_no('PF');
        $visit_id = $this->patientflow_model->add_visit(array(
            'patient_id'            => $patient_id,
            'visit_no'              => $visit_no,
            'current_level_id'      => $first_level->id,
            'level_started_at'      => date('Y-m-d H:i:s'),
            'amount'                => $amount,
            'payment_status'        => 'pending',
            'created_by'            => $this->customlib->getStaffID(),
        ));

        // Link the OPD that triggered this enrollment (the OPD id posted by the
        // board's OPD modal, or the patient's newest OPD) to the flow visit.
        $opd_id = (int) $this->input->post('opd_id');
        if (!$opd_id) {
            $opd_id = (int) $this->patientflow_model->get_latest_opd_id_by_patient($patient_id);
        }
        if ($opd_id) {
            $this->patientflow_model->set_visit_opd_id($visit_id, $opd_id);
            // Charges come from the OPD data — sync the visit amount from the
            // OPD's net charge (after tax/discount) right away.
            $this->patientflow_model->sync_visit_amount_from_opd($visit_id);
        }

        $this->patientflow_model->add_log(array(
            'visit_id'    => $visit_id,
            'to_level_id' => $first_level->id,
            'action'      => 'register',
            'remarks'     => $opd_id ? ('Enrolled via OPD form (OPD ID: ' . $opd_id . ')') : 'Enrolled via OPD form',
            'acted_by'    => $this->customlib->getStaffID(),
        ));

        echo json_encode(array('status' => true, 'visit_id' => $visit_id, 'visit_no' => $visit_no));
    }

    /**
     * AJAX: create a patient from the flow-specific New-Patient modal and, if the
     * staff owns the FIRST level (R1) / is superadmin, enroll them into the flow
     * at that level in the same step. Returns the core patient id for the OPD
     * form's auto-select (same JSON shape as admin/patient/addpatient).
     */
    public function add_patient_from_opd()
    {
        // Reuse the same validation/creation path as the core Patient controller.
        $this->load->model('patient_model');

        $this->form_validation->set_rules('name', $this->lang->line('name'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('gender', $this->lang->line('gender'), 'trim|required|xss_clean');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('status' => 'fail', 'error' => validation_errors()));
            return;
        }

        $patient_data = array(
            'patient_name'          => $this->input->post('name', true),
            'guardian_name'         => $this->input->post('guardian_name', true),
            'mobileno'              => $this->input->post('mobileno', true),
            'gender'                => $this->input->post('gender', true),
            'email'                 => $this->input->post('email', true),
            'address'               => $this->input->post('address', true),
            'blood_group'           => $this->input->post('blood_group', true),
            'marital_status'        => $this->input->post('marital_status', true) ?: '',
            'known_allergies'       => $this->input->post('known_allergies', true),
            'note'                  => $this->input->post('note', true),
            'identification_number' => $this->input->post('identification_number', true),
            'organisation_id'       => $this->input->post('organisation_id', true) ?: null,
            'insurance_id'          => $this->input->post('insurance_id', true),
            'insurance_validity'    => $this->input->post('validity', true) ? date('Y-m-d', strtotime($this->input->post('validity', true))) : null,
            'dob'                   => $this->input->post('dob', true) ? date('Y-m-d', strtotime($this->input->post('dob', true))) : null,
            'age'                   => intval($this->input->post('age[year]', true)),
            'month'                 => intval($this->input->post('age[month]', true)),
            'day'                   => intval($this->input->post('age[day]', true)),
            'as_of_date'            => date('Y-m-d'),
            'is_active'             => 'yes',
        );

        // Patient UUID (MC-KTR-YYYY-######)
        $patient_uuid = $this->patientflow_model->generate_patient_uuid();
        $patient_data['patient_uuid'] = $patient_uuid;

        $patient_id = $this->patient_model->add_patient($patient_data);
        if (!$patient_id) {
            echo json_encode(array('status' => 'fail', 'error' => $this->lang->line('save_failed')));
            return;
        }

        $this->patientflow_model->ensure_patient_uuid($patient_id, $patient_uuid);

        // Handle the photo file upload if provided (mirrors Patient::index()).
        if (isset($_FILES['file']) && !empty($_FILES['file']['name'])) {
            $file_info = pathinfo($_FILES['file']['name']);
            $img_name  = $patient_id . '.' . $file_info['extension'];
            if (@move_uploaded_file($_FILES['file']['tmp_name'], './uploads/patient_images/' . $img_name)) {
                $this->patient_model->add(array('id' => $patient_id, 'image' => 'uploads/patient_images/' . $img_name));
            }
        }

        // R1-only flow enrollment: staff must own the FIRST level (or be superadmin).
        $ctx = $this->pf_context();
        $first_level = $this->patientflow_model->get_first_level();
        $is_r1 = $ctx['is_superadmin'];
        if (!$is_r1 && $first_level) {
            foreach ($ctx['my_levels'] as $ml) {
                if ((int) $ml->id === (int) $first_level->id) { $is_r1 = true; break; }
            }
        }

        if ($is_r1 && $first_level && !$this->patientflow_model->find_active_visit_by_patient($patient_id)) {
            $visit_no = $this->patientflow_model->generate_visit_no('PF');
            $visit_id = $this->patientflow_model->add_visit(array(
                'patient_id'            => $patient_id,
                'visit_no'              => $visit_no,
                'current_level_id'      => $first_level->id,
                'level_started_at'      => date('Y-m-d H:i:s'),
                'amount'                => 0,
                'payment_status'        => 'pending',
                'created_by'            => $this->customlib->getStaffID(),
            ));
            $this->patientflow_model->add_log(array(
                'visit_id'    => $visit_id,
                'to_level_id' => $first_level->id,
                'action'      => 'register',
                'remarks'     => 'Registered via New Patient (OPD form)',
                'acted_by'    => $this->customlib->getStaffID(),
            ));
        }

        echo json_encode(array('status' => 'success', 'id' => $patient_id, 'message' => $this->lang->line('patient_registered')));
    }

    public function levels()
    {
        if (!$this->rbac->hasPrivilege('patient_flow', 'can_view')) {
            access_denied();
        }

        $this->session->set_userdata('top_menu', 'patient_flow');
        $this->session->set_userdata('sub_menu', 'patient_flow_levels');

        $data['title']        = $this->lang->line('patient_flow');
        $data['levels']       = $this->patientflow_model->get_chain();
        $data['unassigned_levels'] = $this->patientflow_model->get_unassigned_role_levels();
        $data['roles']        = $this->role_model->get();
        $data['counter_types'] = array(
            ''           => $this->lang->line('select_counter_type'),
            'reception'  => $this->lang->line('reception'),
            'cash'       => $this->lang->line('cash'),
            'doctor'     => $this->lang->line('doctor'),
            'counsellor' => $this->lang->line('counsellor'),
            'other'      => $this->lang->line('other'),
        );

        $this->load->view('layout/header', $data);
        $this->load->view('admin/patientflow/levels', $data);
        $this->load->view('layout/footer', $data);
    }

    /**
     * Save (add or update) a workflow level.
     */
    public function save()
    {
        if (!$this->rbac->hasPrivilege('patient_flow_levels', 'can_add')
            && !$this->rbac->hasPrivilege('patient_flow_levels', 'can_edit')) {
            access_denied();
        }

        $id = $this->input->post('id');

        $phase      = $this->input->post('phase');
        $code       = $this->input->post('code');
        $level_no   = $this->input->post('level_no');
        $title      = $this->input->post('title');
        $role_id    = $this->input->post('role_id');
        $parent_id  = $this->input->post('parent_id');
        $counter    = $this->input->post('counter_type');
        $can_approve= $this->input->post('can_approve');
        $can_change_payment = $this->input->post('can_change_payment');
        $can_generate_billing_ref = $this->input->post('can_generate_billing_ref');
        $can_generate_invoice = $this->input->post('can_generate_invoice');
        $can_case_taking = $this->input->post('can_case_taking');
        $can_view_case_taking = $this->input->post('can_view_case_taking');
        $can_senior_approval = $this->input->post('can_senior_approval');
        $can_print_billing = $this->input->post('can_print_billing');
        $can_change_status = $this->input->post('can_change_status');
        $can_generate_token = $this->input->post('can_generate_token');
        $can_create_pbhp_id = $this->input->post('can_create_pbhp_id');
        $can_cash_received = $this->input->post('can_cash_received');
        $can_change_programme_status = $this->input->post('can_change_programme_status');
        $can_prescribe_medicine = $this->input->post('can_prescribe_medicine');
        $patient_enrolled = $this->input->post('patient_enrolled');
        $print_vial = $this->input->post('print_vial');
        $generate_bill = $this->input->post('generate_bill');

        $this->form_validation->set_rules('title', $this->lang->line('title'), 'trim|required|max_length[120]');
        $this->form_validation->set_rules('code', $this->lang->line('code'), 'trim|required|max_length[20]');
        $this->form_validation->set_rules('level_no', $this->lang->line('level_no'), 'trim|required|integer');

        if ($this->form_validation->run() == false) {
            redirect('admin/patientflow/levels');
            return;
        }

        // Cycle prevention: a level may not be its own parent or a descendant's child.
        if ($id && $parent_id == $id) {
            $this->session->set_flashdata('error_msg', $this->lang->line('invalid_parent'));
            redirect('admin/patientflow/levels');
        }
        if ($id && $parent_id) {
            $descendants = $this->patientflow_model->get_descendants($id);
            if (in_array($parent_id, $descendants)) {
                $this->session->set_flashdata('error_msg', $this->lang->line('cycle_parent'));
                redirect('admin/patientflow/levels');
            }
        }

        $data = array(
            'phase'        => $phase ? $phase : 'P1',
            'code'         => $code,
            'level_no'     => intval($level_no),
            'title'        => $title,
            'role_id'      => $role_id ? intval($role_id) : null,
            'parent_id'    => $parent_id ? intval($parent_id) : null,
            'counter_type' => $counter ? $counter : null,
            'can_approve'  => $can_approve ? 1 : 0,
            'can_change_payment' => $can_change_payment ? 1 : 0,
            'can_generate_billing_ref' => $can_generate_billing_ref ? 1 : 0,
            'can_generate_invoice' => $can_generate_invoice ? 1 : 0,
            'can_case_taking' => $can_case_taking ? 1 : 0,
            'can_view_case_taking' => $can_view_case_taking ? 1 : 0,
            'can_senior_approval' => $can_senior_approval ? 1 : 0,
            'can_print_billing' => $can_print_billing ? 1 : 0,
            'can_change_status' => $can_change_status ? 1 : 0,
            'can_generate_token' => $can_generate_token ? 1 : 0,
            'can_create_pbhp_id' => $can_create_pbhp_id ? 1 : 0,
            'can_cash_received'  => $can_cash_received ? 1 : 0,
            'can_change_programme_status' => $can_change_programme_status ? 1 : 0,
            'can_prescribe_medicine' => $can_prescribe_medicine ? 1 : 0,
            'patient_enrolled' => $patient_enrolled ? 1 : 0,
            'print_vial' => $print_vial ? 1 : 0,
            'generate_bill' => $generate_bill ? 1 : 0,
            'is_active'    => 1,
        );

        if ($id) {
            $this->patientflow_model->update(intval($id), $data);
            $msg = 'updated';
        } else {
            $this->patientflow_model->add($data);
            $msg = 'added';
        }

        $this->session->set_flashdata('success_msg', $this->lang->line('level_' . $msg));
        redirect('admin/patientflow/levels');
    }

    /**
     * Delete a workflow level. Any direct child levels are reparented up to
     * the deleted level's parent, so the chain stays connected.
     */
    public function delete($id = 0)
    {
        if (!$this->rbac->hasPrivilege('patient_flow_levels', 'can_delete')) {
            access_denied();
        }

        $id = intval($id);
        if ($id) {
            if ($this->patientflow_model->delete_with_reparent($id)) {
                $this->session->set_flashdata('success_msg', $this->lang->line('level_deleted'));
            } else {
                $this->session->set_flashdata('error_msg', $this->lang->line('delete_blocked_children'));
            }
        }
        redirect('admin/patientflow/levels');
    }

    /**
     * AJAX endpoints (kept minimal for phase 1; the board/list uses these).
     */

    /** Return levels JSON for the board / list filters. */
    public function levels_ajax()
    {
        if (!$this->rbac->hasPrivilege('patient_flow', 'can_view')) {
            access_denied();
        }
        header('Content-Type: application/json');
        echo json_encode($this->patientflow_model->get_chain());
    }


    /**
     * View a specific patient flow visit with all details
     * @param int $id - patient_flow_visits.id
     */
    public function view($id)
    {
        if (!$this->rbac->hasPrivilege('patient_flow', 'can_view')) {
            access_denied();
        }

        // Get visit with all details using the new method
        $visit = $this->patientflow_model->get_visit_full_details($id);
        
        if (!$visit) {
            set_alert('danger', $this->lang->line('record_not_found'));
            redirect('admin/patientflow');
        }

        // Get patient's visit history
        $history = $this->patientflow_model->get_visits(null, null, 50);
        $patient_history = array();
        foreach ($history as $h) {
            if ($h->patient_id == $visit->patient_id) {
                $patient_history[] = $h;
            }
        }

        // Get OPD details if exists
        $opd_details = null;
        if (!empty($visit->opd_id)) {
            $this->db->select('od.*, vd.id AS visit_details_id, vd.appointment_date, vd.symptoms, vd.refference, vd.payment_mode, vd.note, vd.note_remark, s.name AS doctor_name, s.surname AS doctor_surname, s.employee_id AS doctor_employee_id');
            $this->db->from('opd_details od');
            $this->db->join('visit_details vd', 'vd.opd_details_id = od.id', 'left');
            $this->db->join('staff s', 's.id = vd.cons_doctor', 'left');
            $this->db->where('od.id', (int) $visit->opd_id);
            $opd_details = $this->db->get()->row();
            
            if ($opd_details) {
                // OPD charges — the amount saved on patient_charges (post
                // discount/tax) for this single linked OPD.

                $opd_details->charges          = $this->charge_model->getopdCharges($visit->opd_id);
                $opd_details->net_amount       = $this->patientflow_model->get_opd_net_amount($visit->opd_id);
                $opd_details->currency_symbol  = $this->customlib->getHospitalCurrencyFormat();
            }
        }

        // Get flow logs
        $logs = $this->patientflow_model->get_logs_by_visit_ids(array($id));
        $visit_logs = isset($logs[$id]) ? $logs[$id] : array();

        // Get billing statuses
        $billing_statuses = $this->patientflow_model->billing_statuses();

        // Get all levels for dropdown
        $levels = $this->patientflow_model->get_chain();

        // Level-flag permissions for the detail view.
        $view_ctx = $this->pf_context();

        $data = array(
            'title' => 'Patient Flow Details',
            'visit' => $visit,
            'history' => $patient_history,
            'opd_details' => $opd_details,
            'logs' => $visit_logs,
            'levels' => $levels,
            'billing_statuses' => $billing_statuses,
            'can_forward' => $this->patientflow_model->can_forward($id, $this->session->userdata('role_ids')),
            'can_backward' => $this->patientflow_model->can_backward($id, $this->session->userdata('role_ids')),
            'can_approve' => $this->patientflow_model->can_approve_visit($id, $this->session->userdata('role_ids')),
            'can_manage_billing' => $this->pf_has_level_flag($view_ctx, 'can_generate_billing_ref'),
            'can_print_billing' => $this->pf_has_level_flag($view_ctx, 'can_print_billing'),
        );

        $this->load->view('layout/header', $data);
        $this->load->view('admin/patientflow/view', $data);
        $this->load->view('layout/footer');
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////// VIEW FILE CODE AJAX /////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /**
     * AJAX: Advance visit to next level
     */
    public function advance()
    {
        if (!$this->rbac->hasPrivilege('patient_flow', 'can_edit')) {
            echo json_encode(['status' => false, 'message' => 'Permission denied']);
            return;
        }
        
        $visit_id = $this->input->post('visit_id');
        $remarks = $this->input->post('remarks');
        
        $result = $this->patientflow_model->advance_visit(
            $visit_id,
            $this->session->userdata('staff_id'),
            $this->session->userdata('role_id'),
            $remarks
        );
        
        echo json_encode($result);
    }

    /**
     * AJAX: Generate billing reference
     */
    public function generate_billing_ref()
    {
        if (!$this->rbac->hasPrivilege('patient_flow', 'can_edit')) {
            echo json_encode(['status' => false, 'message' => 'Permission denied']);
            return;
        }

        // Level-flag permission: superadmin, or a staff whose role owns a
        // level with can_generate_billing_ref checked in the Levels form.
        if (!$this->pf_has_level_flag($this->pf_context(), 'can_generate_billing_ref')) {
            echo json_encode(['status' => false, 'message' => 'You are not allowed to generate billing references.']);
            return;
        }
        
        $visit_id = $this->input->post('visit_id');
        $reference = $this->patientflow_model->generate_billing_reference();
        
        $result = $this->patientflow_model->set_billing_reference(
            $visit_id,
            $reference,
            $this->session->userdata('staff_id'),
            $this->session->userdata('role_id')
        );
        
        echo json_encode($result);
    }

    /**
     * AJAX: Update billing status
     */
    public function update_billing_status()
    {
        if (!$this->rbac->hasPrivilege('patient_flow', 'can_edit')) {
            echo json_encode(['status' => false, 'message' => 'Permission denied']);
            return;
        }

        // Level-flag permission: superadmin, or a staff whose role owns a
        // level with can_generate_billing_ref checked in the Levels form.
        if (!$this->pf_has_level_flag($this->pf_context(), 'can_generate_billing_ref')) {
            echo json_encode(['status' => false, 'message' => 'You are not allowed to change the billing status.']);
            return;
        }
        
        $visit_id = $this->input->post('visit_id');
        $status = $this->input->post('status');
        $remarks = $this->input->post('remarks');
        
        $result = $this->patientflow_model->set_billing_status(
            $visit_id,
            $status,
            $this->session->userdata('staff_id'),
            $this->session->userdata('role_id')
        );
        
        echo json_encode($result);
    }

    /**
     * Change visit status (active / completed / cancelled).
     * Ownership gate: only the role owning the visit's current level may
     * change its status (superadmin bypasses).
     */
    public function set_visit_status($visit_id = 0)
    {
        if (!$this->rbac->hasPrivilege('patient_flow', 'can_edit')) {
            $this->session->set_flashdata('error_msg', 'Permission denied');
            redirect('admin/patientflow');
            return;
        }

        $visit_id = intval($visit_id);
        $status   = strtolower(trim((string) $this->input->post('status', true)));

        $allowed = array('active', 'completed', 'cancelled');
        if (!$visit_id || !in_array($status, $allowed, true)) {
            $this->session->set_flashdata('error_msg', 'Invalid status.');
            redirect('admin/patientflow');
            return;
        }

        // Ownership gate.
        $ctx = $this->pf_context();
        $visit = $this->patientflow_model->get_visit($visit_id);
        if (!$visit) {
            $this->session->set_flashdata('error_msg', 'Visit not found.');
            redirect('admin/patientflow');
            return;
        }

        $owned = $ctx['is_superadmin'];
        if (!$owned) {
            foreach ($ctx['my_levels'] as $ml) {
                if ((int) $ml->id === (int) $visit->current_level_id) { $owned = true; break; }
            }
        }

        if (!$owned) {
            $this->session->set_flashdata('error_msg', 'You are not allowed to change the status of this visit.');
            redirect('admin/patientflow');
            return;
        }

        // Level-scoped permission: only a staff whose role owns a level where
        // can_change_status is checked may change this visit's status.
        if (!$ctx['is_superadmin'] && empty($visit->level_can_change_status)) {
            $this->session->set_flashdata('error_msg', 'Status change is not allowed at this level.');
            redirect('admin/patientflow');
            return;
        }

        $update = array('status' => $status);
        if ($status === 'completed') {
            $update['completed_at'] = date('Y-m-d H:i:s');
            $update['completed_by'] = $this->customlib->getStaffID();
        }
        $this->patientflow_model->update_visit($visit_id, $update);

        $this->session->set_flashdata('success_msg', 'Visit status updated to "' . ucfirst($status) . '".');
        redirect('admin/patientflow');
    }

    /**
     * Patient Flow Report (Reports > Patient > Patient Flow Report):
     * filterable listing of all flow visits.
     */
    /**\n     * AJAX: Get prefill data for bill generation from patientflow visit.\n     * Returns patient name, prescriptions (with medicine details), doctor, and total amount.\n     */
    public function get_bill_prefill()
    {
        if (!$this->rbac->hasPrivilege('patient_flow', 'can_view')) {
            echo json_encode(['status' => false, 'message' => 'Permission denied']);
            return;
        }

        $visit_id = (int) $this->input->post('visit_id', true);
        $patient_id = (int) $this->input->post('patient_id', true);

        if (!$visit_id) {
            echo json_encode(['status' => false, 'message' => 'Visit ID is required.']);
            return;
        }

        // Fetch visit record
        $visit = $this->patientflow_model->get_visit($visit_id);
        if (!$visit) {
            echo json_encode(['status' => false, 'message' => 'Visit not found.']);
            return;
        }

        // Resolve patient_id from visit if not provided
        if (!$patient_id && isset($visit->patient_id)) {
            $patient_id = (int) $visit->patient_id;
        }

        // Fetch patient name
        $patient_name = '';
        if ($patient_id && $this->db->table_exists('patients')) {
            $patient = $this->db->where('id', $patient_id)->get('patients')->row();
            if ($patient) {
                $patient_name = trim((string) ($patient->patient_name ?: ''));
                if (empty($patient_name) && !empty($patient->name)) {
                    $patient_name = trim((string) $patient->name);
                }
            }
        }

        // Fetch prescriptions with medicine details
        $prescriptions = $this->patientflow_model->get_prescriptions_for_visit($visit_id, '');
        $medicines = array();
        $total_amount = 0;

        foreach ($prescriptions as $rx) {
            $medicine_name = trim((string) ($rx['medicine_identity'] ?: $rx['medicine_code'] ?: ''));
            $series = (string) ($rx['series'] ?: '');
            $price = 0; // default price; medicine_master may not store price, set 0
            $amount = 0;

            $medicines[] = array(
                'id' => (int) $rx['id'],
                'medicine_id' => (int) $rx['medicine_id'],
                'medicine_name' => $medicine_name,
                'medicine_code' => (string) ($rx['medicine_code'] ?: ''),
                'series' => $series,
                'variant' => (int) $rx['variant'],
                'price' => $price,
                'amount' => $amount,
                'payment_confirmed' => (int) $rx['payment_confirmed'],
                'clinical_approval' => (string) $rx['clinical_approval'],
            );

            if ((int) $rx['payment_confirmed'] === 1) {
                $total_amount += (float) $amount;
            }
        }

        // Fetch doctor name from prescribed_by or visit's level role
        $doctor_name = '';
        $doctor_id = null;
        if (!empty($prescriptions)) {
            $firstRx = $prescriptions[0];
            if (!empty($firstRx['prescribed_by'])) {
                $doctor_id = (int) $firstRx['prescribed_by'];
                $doctor = $this->db->where('id', $doctor_id)->get('staff')->row();
                if ($doctor) {
                    $doctor_name = trim((string) ($doctor->name ?: '') . ' ' . ($doctor->surname ?: ''));
                }
            }
        }

        // Fetch programme name and status from visit's ct_programme_id
        $programme_name = '';
        $programme_status = '';
        if (!empty($visit->ct_programme_id)) {
            $programme = $this->db->where('code', $visit->ct_programme_id)->get('patient_flow_programme')->row();
            if ($programme) {
                $programme_name = trim((string) $programme->name);
                $programme_status = $visit->ct_programme_status ?? 'enroll';
            }
        }

        echo json_encode(array(
            'status' => true,
            'visit_id' => $visit_id,
            'patient_id' => $patient_id,
            'patient_name' => $patient_name,
            'doctor_name' => $doctor_name,
            'doctor_id' => $doctor_id,
            'programme_name' => $programme_name,
            'programme_status' => $programme_status,
            'medicines' => $medicines,
            'total_amount' => number_format((float) $total_amount, 2),
            'currency_symbol' => $this->customlib->getHospitalCurrencyFormat(),
        ));
    }

    /**\n     * AJAX: Save bill confirmation for patientflow prescriptions.\n     * Marks prescriptions as payment confirmed and stores billed amounts.\n     */
    public function save_bill()
    {
        if (!$this->rbac->hasPrivilege('patient_flow', 'can_edit')) {
            echo json_encode(['status' => false, 'message' => 'Permission denied']);
            return;
        }

        // Level-flag: only levels with can_generate_billing_ref can save bills
        if (!$this->pf_has_level_flag($this->pf_context(), 'can_generate_billing_ref')) {
            echo json_encode(['status' => false, 'message' => 'You are not allowed to save bills.']);
            return;
        }

        $visit_id = (int) $this->input->post('visit_id', true);
        $patient_id = (int) $this->input->post('patient_id', true);
        $total_amount = (float) $this->input->post('total_amount', true);
        $medicines = $this->input->post('medicines', true);

        if (!$visit_id) {
            echo json_encode(['status' => false, 'message' => 'Visit ID is required.']);
            return;
        }

        $staff_id = $this->session->userdata('staff_id');

        // If medicines array provided, update each prescription
        if (is_array($medicines)) {
            foreach ($medicines as $rx) {
                $rx_id = (int) $rx['id'];
                $amount = (float) ($rx['amount'] ?? 0);
                $this->db->where('id', $rx_id)->where('visit_id', $visit_id)
                    ->update('patient_flow_prescription', array(
                        'payment_confirmed' => 1,
                    ));
            }
        }

        echo json_encode(array(
            'status' => true,
            'message' => 'Bill saved successfully.',
            'visit_id' => $visit_id,
            'total_amount' => number_format($total_amount, 2),
        ));
    }

    public function patientflowreport()
    {
        if (!$this->rbac->hasPrivilege("patient_flow", "can_view")) {
            access_denied();
        }
        $this->session->set_userdata("top_menu", "Reports");
        $this->session->set_userdata("sub_menu", "reports/patient");
        $this->session->set_userdata("subsub_menu", "reports/patient/patientflowreport");
        $filters = array(
            "patient_name"     => $this->input->post("patient_name", true),
            "mobileno"         => $this->input->post("mobileno", true),
            "visit_no"         => $this->input->post("visit_no", true),
            "status"           => $this->input->post("status", true),
            "payment_status"   => $this->input->post("payment_status", true),
            "approve_status"   => $this->input->post("approve_status", true),
            "programme_status" => $this->input->post("programme_status", true),
            "token_status"     => $this->input->post("token_status", true),
            "level_id"         => $this->input->post("level_id", true),
            "date_from"        => $this->input->post("date_from", true),
            "date_to"          => $this->input->post("date_to", true),
        );

        $data["filters"] = $filters;
        $data["levels"]  = $this->patientflow_model->get();
        $data["programme_statuses"] = $this->patientflow_model->programme_statuses();
        $data["module"]  = "reports";

        // Only load the listing when a search has actually been submitted (POST).
        // On the initial page load (GET) no results are shown.
        $data["is_search"] = ($this->input->server("REQUEST_METHOD") === "POST");
        $data["visits"]    = array();
        if ($data["is_search"]) {
            $data["visits"] = $this->patientflow_model->get_flow_report($filters);
        }

        $this->load->view("layout/header", $data);
        $this->load->view("admin/patientflow/patientflowreport", $data);
        $this->load->view("layout/footer", $data);
    }

    /**
     * Delete a patient-flow visit. Gated by the "Patient Flow" can_delete
     * permission (Roles & Permission > Patient Flow > Delete).
     */
    public function delete_visit($visit_id = 0)
    {
        if (!$this->rbac->hasPrivilege('patient_flow', 'can_delete')) {
            access_denied();
        }

        $visit_id = intval($visit_id);
        if ($visit_id) {
            $deleted = $this->patientflow_model->delete_visit($visit_id);
            if ($deleted) {
                $this->session->set_flashdata('success_msg', 'Patient flow visit deleted successfully.');
            } else {
                $this->session->set_flashdata('error_msg', 'Unable to delete the patient flow visit.');
            }
        }
        redirect('admin/patientflow');
    }

    /**
     * AJAX: fetch patient's visit history for the Patient History panel.
     * Called from pfCtLoadPatientHistory() via JSON.
     */
    public function get_patient_visits()
    {
        if (!$this->rbac->hasPrivilege('patient_flow', 'can_view')) {
            echo json_encode(array('status' => false, 'message' => 'Not allowed.'));
            return;
        }
        $patient_id = intval($this->input->post('patient_id', true));
        $visits = $this->patientflow_model->get_patient_visits($patient_id);
        echo json_encode(array('status' => true, 'visits' => $visits));
    }

    /**
     * Save case-taking details for a visit (AJAX). Only allowed when the
     * acting staff owns the visit current level and that level has the
     * can_case_taking flag, or for superadmin.
     */
    public function save_case_taking()
    {
        if (!$this->rbac->hasPrivilege('patient_flow', 'can_add')) {
            echo json_encode(array('status' => false, 'message' => 'Not allowed.'));
            return;
        }
        $visit_id   = intval($this->input->post('visit_id', true));
        $type       = trim((string) $this->input->post('case_taking_type', true));
        $description = trim((string) $this->input->post('case_taking_description', true));
        if (!$visit_id || $type === '') {
            echo json_encode(array('status' => false, 'message' => 'Visit id and case taking type are required.'));
            return;
        }

        // ---- Case-taking detail fields (client requirement) ----
        $missing = array();
        $extra   = array();

        // Sex: M/F input feeding constitution.
        $sex = strtoupper(trim((string) $this->input->post('ct_sex', true)));
        if (!in_array($sex, array('M', 'F'), true)) {
            $missing[] = 'Sex';
            $extra['ct_sex'] = null;
        } else {
            $extra['ct_sex'] = $sex;
        }

        // Required free-text concern fields.
        $req_text = array(
            'ct_primary_concern'  => 'Primary concern',
            'ct_concern_duration' => 'Duration of concern',
            'ct_primary_system'   => 'Primary system / area',
            'ct_dominant_nature'  => 'Dominant nature',
        );
        foreach ($req_text as $f => $label) {
            $val = trim((string) $this->input->post($f, true));
            if ($val === '') {
                $missing[] = $label;
            }
            $extra[$f] = ($val === '') ? null : $val;
        }

// BOD1-4 / FSH1-4 / NOS1-4 slots (optional; stored as master ids if provided).
$slots = array('ct_bod' => 'BOD', 'ct_fsh' => 'FSH', 'ct_nos' => 'NOS');
foreach ($slots as $pfx => $label) {
    for ($i = 1; $i <= 4; $i++) {
        $val = (int) $this->input->post($pfx . $i, true);
        $extra[$pfx . $i] = ($val > 0) ? $val : null;
    }
}

        // Assessment certainty + case status (workflow).
        $certainty = trim((string) $this->input->post('ct_assessment_certainty', true));
        if ($certainty === '') {
            $missing[] = 'Assessment certainty';
        }
        $extra['ct_assessment_certainty'] = ($certainty === '') ? null : $certainty;

        $ct_status = trim((string) $this->input->post('ct_status', true));
        if ($ct_status === '') {
            $missing[] = 'Case status';
        }
        $extra['ct_status'] = ($ct_status === '') ? null : $ct_status;

        // Conditional: reason mandatory when unresolved / overridden.
        $review_reason = trim((string) $this->input->post('ct_review_reason', true));
        if ($certainty !== 'Certain' || $ct_status === 'In Review') {
            if ($review_reason === '') {
                $missing[] = 'Review Required reason';
            }
        }
        $extra['ct_review_reason'] = ($review_reason === '') ? null : $review_reason;

        // Conditional / optional supporting information.
        $opt_text = array(
            'ct_tissue_structure', 'ct_report_info', 'ct_secondary_concerns',
            'ct_additional_note', 'ct_previous_treatment', 'ct_programme_suggestion',
        );
        foreach ($opt_text as $f) {
            $val = trim((string) $this->input->post($f, true));
            $extra[$f] = ($val === '') ? null : $val;
        }

        // Programme Enrolment (optional selectbox: HFT / FSC / SSC / CHC)
        $programme_id = trim((string) $this->input->post('ct_programme_id', true));
        $extra['ct_programme_id'] = ($programme_id === '') ? null : $programme_id;

        if (!empty($missing)) {
            echo json_encode(array(
                'status'  => false,
                'message' => 'Please fill in: ' . implode(', ', $missing) . '.',
            ));
            return;
        }
        $visit = $this->patientflow_model->get_visit($visit_id);
        if (!$visit) {
            echo json_encode(array('status' => false, 'message' => 'Visit not found.'));
            return;
        }
        $ctx = $this->pf_context();
        $may = $ctx['is_superadmin'];
        if (!$may) {
            // Check if staff owns the current level with case taking permission
            # OR if the level requires senior approval (can_senior_approval flag).
            # In the senior approval case, the staff can still proceed but the
            # system will enforce second authorization during save.
            $owns_level = false;
            foreach ($ctx['my_levels'] as $ml) {
                if ((int) $ml->id === (int) $visit->current_level_id) {
                    $owns_level = !empty($visit->level_can_case_taking) || !empty($ml->can_senior_approval);
                    break;
                }
            }
            $may = $owns_level;
        }
        if (!$may) {
            echo json_encode(array('status' => false, 'message' => 'You are not allowed to take a case at this level.'));
            return;
        }
        $result = $this->patientflow_model->set_case_taking(
            $visit_id, $type, $description, $this->customlib->getStaffID(), $extra
        );
        echo json_encode($result);
    }

    public function get_medicines_for_visit()
    {
        if (!$this->rbac->hasPrivilege('patient_flow', 'can_add')) {
            echo json_encode(array('status' => false, 'message' => 'Access denied.'));
            return;
        }
        $visit_id    = intval($this->input->post('visit_id', true));
        $gender      = strtoupper(trim((string) $this->input->post('gender', true)));
        $concern     = trim((string) $this->input->post('concern', true));
        $body_build  = intval($this->input->post('body_build_id', true));
        $face_shape  = intval($this->input->post('face_shape_id', true));
        $nose_type   = intval($this->input->post('nose_type_id', true));

        if ($visit_id && ($gender === '' || $concern === '' || $body_build <= 0 || $face_shape <= 0 || $nose_type <= 0)) {
            $v = $this->patientflow_model->get_visit($visit_id);
            if ($v) {
                if ($gender === '' && !empty($v->ct_sex)) { $gender = strtoupper(trim((string) $v->ct_sex)); }
                if ($concern === '' && !empty($v->ct_primary_concern)) { $concern = trim((string) $v->ct_primary_concern); }
                if ($body_build <= 0 && !empty($v->ct_bod1)) { $body_build = intval($v->ct_bod1); }
                if ($face_shape <= 0 && !empty($v->ct_fsh1)) { $face_shape = intval($v->ct_fsh1); }
                if ($nose_type <= 0 && !empty($v->ct_nos1)) { $nose_type = intval($v->ct_nos1); }
            }
        }

        if (!$visit_id || $gender === '' || $concern === ''
            || $body_build <= 0 || $face_shape <= 0 || $nose_type <= 0) {
            echo json_encode(array('status' => false, 'message' => 'Case taking (Sex, Concern, Body, Face, Nose) must be completed first.'));
            return;
        }
        $result = $this->patientflow_model->resolve_medicines_for_visit(
            $visit_id, $gender, $concern, $body_build, $face_shape, $nose_type
        );
        echo json_encode(array('status' => true, 'data' => $result));
    }

    public function get_medicines_by_series()
    {
        if (!$this->rbac->hasPrivilege('patient_flow', 'can_add')) {
            echo json_encode(array('status' => false, 'message' => 'Access denied.'));
            return;
        }
        $visit_id   = intval($this->input->post('visit_id', true));
        $series_code = strtoupper(trim((string) $this->input->post('series_code', true)));
        $stage_id   = intval($this->input->post('stage_id', true));

        if (!$visit_id) {
            echo json_encode(array('status' => false, 'message' => 'Visit is required.'));
            return;
        }

        // When no series_code is provided, fetch medicines grouped by all series
        if ($series_code === '') {
            $result = $this->patientflow_model->get_medicines_grouped_by_series($visit_id, $stage_id);
            echo json_encode(array('status' => true, 'data' => $result));
            return;
        }

        $result = $this->patientflow_model->get_medicines_by_series_code($visit_id, $series_code, $stage_id);
        echo json_encode(array('status' => true, 'data' => $result));
    }

    public function save_prescription()
    {
        if (!$this->rbac->hasPrivilege('patient_flow', 'can_add')) {
            echo json_encode(array('status' => false, 'message' => 'Access denied.'));
            return;
        }
        $visit_id   = intval($this->input->post('visit_id', true));
        $medicine_id = intval($this->input->post('medicine_id', true));
        $series     = strtoupper(trim((string) $this->input->post('series', true)));
        $variant    = intval($this->input->post('variant', true));
        $is_fx      = ($series === 'FX');
        $is_e       = ($series === 'E');
        if (!$visit_id || !$medicine_id || $series === '') {
            echo json_encode(array('status' => false, 'message' => 'Visit, medicine and series are required.'));
            return;
        }
        // FX medicines may only be prescribed by Senior staff.
        if ($is_fx) {
            $staff_role = $this->customlib->getStaffRole();
            $is_senior = false;
            if ($staff_role) {
                $role_obj = json_decode($staff_role, true);
                if (!empty($role_obj['name']) && stripos($role_obj['name'], 'senior') !== false) {
                    $is_senior = true;
                }
            }
            if (!$is_senior) {
                echo json_encode(array('status' => false, 'message' => 'FX medicines can only be prescribed by Senior Doctor.'));
                return;
            }
            $fx = $this->patientflow_model->check_fx_limit($this->customlib->getStaffID(), $visit_id);
            if (!$fx['allowed']) {
                echo json_encode(array('status' => false, 'message' => $fx['message']));
                return;
            }
        }
        $result = $this->patientflow_model->save_prescription(
            $visit_id, $medicine_id, $this->customlib->getStaffID(), $series, $variant, $is_fx, $is_e
        );
        echo json_encode($result);
    }

    public function save_prescriptions_bulk()
    {
        if (!$this->rbac->hasPrivilege('patient_flow', 'can_add')) {
            echo json_encode(array('status' => false, 'message' => 'Access denied.'));
            return;
        }
        $visit_id           = intval($this->input->post('visit_id', true));
        $broad_area_series  = trim((string) $this->input->post('broad_area_series', true));
        $medicines          = json_decode((string) $this->input->post('medicines', true), true);
        if (!$visit_id || !is_array($medicines) || empty($medicines)) {
            echo json_encode(array('status' => false, 'message' => 'Visit and medicines are required.'));
            return;
        }
        $result = $this->patientflow_model->save_prescriptions_bulk(
            $visit_id, $medicines, $this->customlib->getStaffID(), $broad_area_series
        );
        echo json_encode($result);
    }

    /**
     * Print vial labels for a visit's prescribed medicines.
     * Renders a 75mm x 33mm label per medicine.
     */
    public function print_vial_labels($visit_id = 0)
    {
        if (!$this->rbac->hasPrivilege('patient_flow', 'can_view')) {
            $this->response_json(array('status' => false, 'message' => 'Access denied.'));
            return;
        }

        $visit_id = intval($visit_id);
        if ($visit_id <= 0) {
            $this->response_json(array('status' => false, 'message' => 'Invalid visit ID.'));
            return;
        }

        $visit = $this->patientflow_model->get_visit($visit_id);
        if (!$visit) {
            $this->response_json(array('status' => false, 'message' => 'Visit not found.'));
            return;
        }

        $medicines = $this->patientflow_model->get_visit_medicines_for_label($visit_id);
// echo '<pre>';
// print_r($medicines);
// die();
        $data = array(
            'visit'     => $visit,
            'medicines' => $medicines,
        );

        $this->load->view('admin/patientflow/print_vial_labels', $data);
    }

    public function clinical_approve()
    {
        if (!$this->rbac->hasPrivilege('patient_flow', 'can_add')) {
            echo json_encode(array('status' => false, 'message' => 'Access denied.'));
            return;
        }
        $prescription_id = intval($this->input->post('prescription_id', true));
        $approve         = ($this->input->post('approve', true) === '1' || $this->input->post('approve', true) === 'true');
        if (!$prescription_id) {
            echo json_encode(array('status' => false, 'message' => 'Prescription id required.'));
            return;
        }
        $result = $this->patientflow_model->approve_clinical(
            $prescription_id, $this->customlib->getStaffID(), $approve
        );
        echo json_encode($result);
    }

    public function fx_second_approval()
    {
        if (!$this->rbac->hasPrivilege('patient_flow', 'can_add')) {
            echo json_encode(array('status' => false, 'message' => 'Access denied.'));
            return;
        }
        $prescription_id = intval($this->input->post('prescription_id', true));
        if (!$prescription_id) {
            echo json_encode(array('status' => false, 'message' => 'Prescription id required.'));
            return;
        }
        $result = $this->patientflow_model->fx_second_approval(
            $prescription_id, $this->customlib->getStaffID()
        );
        echo json_encode($result);
    }

    public function confirm_payment()
    {
        if (!$this->rbac->hasPrivilege('patient_flow', 'can_add')) {
            echo json_encode(array('status' => false, 'message' => 'Access denied.'));
            return;
        }
        $prescription_id = intval($this->input->post('prescription_id', true));
        if (!$prescription_id) {
            echo json_encode(array('status' => false, 'message' => 'Prescription id required.'));
            return;
        }
        $result = $this->patientflow_model->confirm_payment(
            $prescription_id, $this->customlib->getStaffID()
        );
        echo json_encode($result);
    }

    public function get_prescriptions_for_visit()
    {
        if (!$this->rbac->hasPrivilege('patient_flow', 'can_add')) {
            echo json_encode(array('status' => false, 'message' => 'Access denied.'));
            return;
        }
        $visit_id = intval($this->input->post('visit_id', true));
        $role_json = $this->customlib->getStaffRole();
        $role_name = '';
        if ($role_json) {
            $role_data = json_decode($role_json, true);
            $role_name = isset($role_data['name']) ? (string) $role_data['name'] : '';
        }
        if (!$visit_id) {
            echo json_encode(array('status' => false, 'message' => 'Visit id required.'));
            return;
        }
        $rows = $this->patientflow_model->get_prescriptions_for_visit($visit_id, $role_name);
        echo json_encode(array('status' => true, 'data' => $rows));
    }
    public function get_programme_bill_prefill()
    {
        if (!$this->rbac->hasPrivilege('patient_flow', 'can_view')) {
            echo json_encode(array('status' => false, 'message' => 'Access denied.'));
            return;
        }

        $visit_id   = (int) $this->input->post('visit_id', true);
        $patient_id = (int) $this->input->post('patient_id', true);
        if (!$visit_id) {
            echo json_encode(array('status' => false, 'message' => 'Visit ID is required.'));
            return;
        }

        $visit = $this->patientflow_model->get_visit($visit_id);
        if (!$visit) {
            echo json_encode(array('status' => false, 'message' => 'Visit not found.'));
            return;
        }

        if (!$patient_id && isset($visit->patient_id)) {
            $patient_id = (int) $visit->patient_id;
        }

        $patient_name = '';
        if ($patient_id && $this->db->table_exists('patients')) {
            $p = $this->db->where('id', $patient_id)->get('patients')->row();
            if ($p) {
                $patient_name = trim((string) ($p->patient_name ?: ($p->name ?: '')));
            }
        }

        $stage = $this->patientflow_model->get_stage_from_visit($visit_id);
        $stage_id = isset($stage['stage_id']) ? (int) $stage['stage_id'] : 0;
        $stage_code = isset($stage['stage_code']) ? (string) $stage['stage_code'] : '';

        $enrollments = $this->patientflow_model->get_active_enrollments_by_patient($patient_id);
        if (empty($enrollments)) {
            echo json_encode(array('status' => false, 'message' => 'No active programme enrollment found for this patient.'));
            return;
        }

        $active_campaigns = array();
        $assistance_categories = array();
        $prefill_rows = array();
        foreach ($enrollments as $enr) {
            $programme_id = (int) ($enr->programme_id ?? 0);
            $group_id = 0;
            if ($programme_id > 0 && $this->db->table_exists('patient_flow_programme')) {
                $programme = $this->db->select('group_id')->where('id', $programme_id)->get('patient_flow_programme')->row();
                if ($programme) {
                    $group_id = (int) ($programme->group_id ?? 0);
                }
            }

            if ($group_id > 0 && $this->db->table_exists('patient_flow_fee_assistance_category')) {
                $rows = $this->db->where('group_id', $group_id)->where('is_active', 1)->order_by('id', 'ASC')->get('patient_flow_fee_assistance_category')->result();
                foreach ($rows as $cat) {
                    $key = (string) ($cat->category_code ?: $cat->id);
                    if (!isset($assistance_categories[$key])) {
                        $assistance_categories[$key] = array(
                            'id' => (int) $cat->id,
                            'category_code' => trim((string) ($cat->category_code ?? '')),
                            'discount_percent' => (float) ($cat->discount_percent ?? 0),
                            'validity_quarters' => (int) ($cat->validity_quarters ?? 2),
                            'payment_mode' => trim((string) ($cat->payment_mode ?? 'STAGEWISE')),
                            'group_id' => (int) $group_id,
                        );
                    }
                }
            }

            $campaigns = $this->patientflow_model->get_active_discount_campaigns($programme_id, $group_id, date('Y-m-d'));
            foreach ($campaigns as $campaign) {
                $key = (string) ($campaign->campaign_code ?: $campaign->id);
                if (!isset($active_campaigns[$key])) {
                    $active_campaigns[$key] = array(
                        'id' => (int) $campaign->id,
                        'campaign_code' => trim((string) ($campaign->campaign_code ?? '')),
                        'discount_percent' => (float) ($campaign->discount_percent ?? 0),
                        'reason' => trim((string) ($campaign->reason ?? '')),
                        'start_date' => $campaign->start_date,
                        'end_date' => $campaign->end_date,
                        'allow_stacking' => (int) ($campaign->allow_stacking ?? 0),
                        'payment_mode' => trim((string) ($campaign->payment_mode ?? 'MILESTONE')),
                    );
                }
            }

            $schedule_rows = $this->patientflow_model->get_pending_payment_schedule_rows($enr->id, $stage_id ?: null);
            if (empty($schedule_rows)) {
                $schedule_rows = $this->patientflow_model->get_pending_payment_schedule_rows($enr->id, null);
            }

            foreach ($schedule_rows as $r) {
                $standard_amount = (float) ($r->standard_amount ?? 0);
                $payable_amount  = (float) ($r->payable_amount ?? 0);
                $discount_amount = max(0.00, $standard_amount - $payable_amount);
                $benefit = $this->db->where('enrollment_id', (int) $enr->id)
                    ->where('is_current', 1)
                    ->get('patient_flow_enrollment_benefit')->row();
                $discount_percent = (float) ($r->discount_percent ?? 0);
                if ($benefit && $benefit->discount_percent > $discount_percent) {
                    $discount_percent = (float) $benefit->discount_percent;
                }

                $prefill_rows[] = array(
                    'schedule_id'      => (int) $r->id,
                    'enrollment_id'    => (int) $r->enrollment_id,
                    'enrollment_uid'   => (string) ($r->enrollment_uid ?: ($enr->uid ?: '')),
                    'programme_id'    => (int) ($enr->programme_id ?? 0),
                    'programme_name'   => (string) ($enr->programme_name ?: ($enr->programme_code ?: 'Programme')),
                    'programme_code'   => (string) ($enr->programme_code ?: ''),
                    'coverage'         => (string) ($enr->coverage ?: ''),
                    'quarter_no'       => (int) ($r->quarter_no ?? 0),
                    'seq_no'           => (int) ($r->seq_no ?? 0),
                    'unit_type'        => (string) ($r->unit_type ?: 'MILESTONE'),
                    'standard_amount'  => number_format($standard_amount, 2, '.', ''),
                    'discount_percent' => number_format($discount_percent, 2, '.', ''),
                    'discount_amount'  => number_format($discount_amount, 2, '.', ''),
                    'payable_amount'   => number_format($payable_amount, 2, '.', ''),
                    'stage_from_id'    => (int) ($r->stage_from_id ?? 0),
                    'stage_to_id'      => (int) ($r->stage_to_id ?? 0),
                    'status'           => (string) ($r->status ?: 'PENDING'),
                    'payment_mode'     => $benefit ? (string) $benefit->payment_mode : 'MILESTONE',
                );
            }
        }

        if (empty($prefill_rows)) {
            echo json_encode(array('status' => false, 'message' => 'No pending payment schedule row covers this stage. Please generate a schedule first.', 'can_generate_schedule' => true));
            return;
        }

        echo json_encode(array(
            'status'       => true,
            'patient_id'   => $patient_id,
            'patient_name' => $patient_name,
            'visit_id'     => $visit_id,
            'stage_id'     => $stage_id,
            'stage_code'   => $stage_code,
            'campaigns'    => array_values($active_campaigns),
            'assistance'   => array_values($assistance_categories),
            'rows'         => $prefill_rows,
        ));
    }

    public function generate_programme_bill()
    {
        if (!$this->rbac->hasPrivilege('patient_flow', 'can_add')) {
            echo json_encode(array('status' => false, 'message' => 'Access denied.'));
            return;
        }

        $visit_id     = (int) $this->input->post('visit_id', true);
        $schedule_id  = (int) $this->input->post('schedule_id', true);
        $assistance_id = (int) $this->input->post('assistance_id', true);
        $campaign_id  = (int) $this->input->post('campaign_id', true);
        $amount       = (float) $this->input->post('amount', true);
        $discount_pct = (float) $this->input->post('discount_percent', true);
        $payment_mode = trim((string) $this->input->post('payment_mode', true));
        $note         = trim((string) $this->input->post('note', true));

        if (!$visit_id || !$schedule_id) {
            echo json_encode(array('status' => false, 'message' => 'Visit and schedule are required.'));
            return;
        }

        $row = $this->db->where('id', $schedule_id)->get('patient_flow_programme_payment_schedule')->row();
        if (!$row || $row->status !== 'PENDING') {
            echo json_encode(array('status' => false, 'message' => 'Selected schedule row is not pending.'));
            return;
        }

        $visit = $this->patientflow_model->get_visit($visit_id);
        if (!$visit) {
            echo json_encode(array('status' => false, 'message' => 'Visit not found.'));
            return;
        }

        $patient_id = !empty($visit->patient_id) ? (int) $visit->patient_id : 0;
        if ($patient_id <= 0) {
            echo json_encode(array('status' => false, 'message' => 'Patient not found for this visit.'));
            return;
        }

        if ($this->db->field_exists('invoice_no', 'patient_flow_visits') && !empty($visit->invoice_no)) {
            $existing_invoice = $this->db
                ->where('invoice_type', 'PROGRAMME')
                ->where('invoice_no', trim((string) $visit->invoice_no))
                ->get('patient_flow_fee_invoice')
                ->row();
            if ($existing_invoice) {
                echo json_encode(array('status' => false, 'message' => 'This visit already has a generated programme bill.'));
                return;
            }
        }

        if ($this->db->table_exists('patient_flow_fee_invoice_line')) {
            $existing_schedule_invoice = $this->db
                ->select('i.id, i.status, i.invoice_no')
                ->from('patient_flow_fee_invoice i')
                ->join('patient_flow_fee_invoice_line l', 'l.invoice_id = i.id', 'left')
                ->where('i.invoice_type', 'PROGRAMME')
                ->where('l.schedule_id', (int) $schedule_id)
                ->where_in('i.status', array('PAID', 'PENDING'))
                ->limit(1)
                ->get()
                ->row();
            if ($existing_schedule_invoice) {
                echo json_encode(array('status' => false, 'message' => 'This programme schedule already has a generated bill.'));
                return;
            }
        }

        if ($row->status === 'PAID') {
            echo json_encode(array('status' => false, 'message' => 'This programme schedule is already paid.'));
            return;
        }

        $selected_assistance_discount = 0.0;
        if ($assistance_id > 0 && $this->db->table_exists('patient_flow_fee_assistance_category')) {
            $assistance = $this->db->where('id', $assistance_id)->where('is_active', 1)->get('patient_flow_fee_assistance_category')->row();
            if (!$assistance) {
                echo json_encode(array('status' => false, 'message' => 'Selected fee assistance is not active anymore.'));
                return;
            }
            $selected_assistance_discount = (float) ($assistance->discount_percent ?? 0);
        }

        if ($campaign_id > 0) {
            $campaign = $this->db->where('id', $campaign_id)->where('status', 'APPROVED')->where('start_date <=', date('Y-m-d'))->where('end_date >=', date('Y-m-d'))->get('patient_flow_discount_campaign')->row();
            if (!$campaign) {
                echo json_encode(array('status' => false, 'message' => 'Selected discount campaign is not active anymore.'));
                return;
            }
            $discount_pct = (float) max($discount_pct, (float) ($campaign->discount_percent ?? $discount_pct));
        }

        if ($selected_assistance_discount > 0) {
            $discount_pct = (float) max($discount_pct, $selected_assistance_discount);
        }

        if ($amount <= 0 || $discount_pct < 0 || $discount_pct > 100) {
            echo json_encode(array('status' => false, 'message' => 'Enter a valid amount and discount percentage.'));
            return;
        }

        $max_amount = (float) ($row->payable_amount ?? 0);
        if ($amount > $max_amount + 0.01) {
            echo json_encode(array('status' => false, 'message' => 'Bill amount cannot exceed the scheduled payable amount.'));
            return;
        }

        $staff_id = (int) $this->customlib->getStaffID();
        $invoice_no = $this->patientflow_model->generate_invoice_no();

        if ($this->db->table_exists('patient_flow_fee_invoice')) {
            $invoice_payload = array(
                'invoice_no'    => $invoice_no,
                'invoice_type'  => 'PROGRAMME',
                'patient_id'    => $patient_id,
                'enrollment_id'  => (int) $row->enrollment_id,
                'invoice_date'  => date('Y-m-d H:i:s'),
                'subtotal'      => $amount,
                'gst_total'     => 0,
                'grand_total'   => $amount,
                'status'        => 'PAID',
                'created_by'    => $staff_id,
            );
            $this->db->insert('patient_flow_fee_invoice', $invoice_payload);
            $invoice_id = (int) $this->db->insert_id();

            $description = 'Programme Billing';
            if ($this->db->table_exists('patient_flow_patient_programme_enrollment')) {
                $enr = $this->db->where('id', (int) $row->enrollment_id)->get('patient_flow_patient_programme_enrollment')->row();
                if ($enr && $this->db->table_exists('patient_flow_programme')) {
                    $prog = $this->db->where('id', (int) $enr->programme_id)->get('patient_flow_programme')->row();
                    if ($prog) {
                        $description = 'Programme - ' . ($prog->name ?: $prog->code ?: 'Programme');
                    }
                }
            }
            $this->patientflow_model->add_fee_invoice_line(
                $invoice_id,
                'PROGRAMME',
                $description,
                1,
                $amount,
                0,
                $amount,
                'EXEMPT',
                0,
                0,
                'PROGRAMME',
                $schedule_id
            );
        }

        $this->db->where('id', $schedule_id)->update('patient_flow_programme_payment_schedule', array(
            'status'      => 'PAID',
            'paid_at'     => date('Y-m-d H:i:s'),
            'payment_ref' => $invoice_no,
        ));

        $this->patientflow_model->write_fee_audit_log('programme_payment_schedule', $schedule_id, 'apply_campaign',
            json_encode(array('assistance_id' => $assistance_id, 'campaign_id' => $campaign_id, 'discount_percent' => $discount_pct)),
            json_encode(array('invoice_no' => $invoice_no, 'assistance_id' => $assistance_id, 'campaign_id' => $campaign_id, 'discount_percent' => $discount_pct, 'payment_mode' => $payment_mode, 'note' => $note)),
            $staff_id);

        if ($this->db->field_exists('invoice_no', 'patient_flow_visits')) {
            $this->db->where('id', $visit_id)->update('patient_flow_visits', array(
                'invoice_no'     => $invoice_no,
                'invoice_no_at'  => date('Y-m-d H:i:s'),
                'billing_status' => 'billed',
            ));
        }

        $this->patientflow_model->write_fee_audit_log('programme_payment_schedule', $schedule_id, 'create_invoice',
            json_encode(array('status' => 'PENDING')), json_encode(array(
                'status' => 'PAID',
                'invoice_no' => $invoice_no,
                'amount' => $amount,
                'discount_percent' => $discount_pct,
                'payment_mode' => $payment_mode,
                'note' => $note,
            )), $staff_id);

        echo json_encode(array(
            'status'    => true,
            'message'   => 'Programme bill generated successfully.',
            'invoice_no'=> $invoice_no,
            'amount'    => number_format($amount, 2, '.', ''),
        ));
    }

}