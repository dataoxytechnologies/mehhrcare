<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Fee_engine - Patient flow programme fee calculation engine.
 *
 * Handles benefit resolution at enrollment, payment schedule generation,
 * and later-quarter split calculations using whole-rupee integer maths.
 */
class Fee_engine
{

    protected $CI;

    public function __construct()
    {
        $this->CI = &get_instance();
        $this->CI->load->database();
    }

    /**
     * Resolve benefit at enrollment.
     *
     * Computes once at enrollment and snapshots immutably.
     *
     * @param int   $enrollment_id
     * @param int   $group_id        Patient_flow_programme_group.id
     * @param int   $programme_id    Patient_flow_programme.id
     * @param int   $enrolled_on     Reference date (Y-m-d) for campaign eligibility
     * @param int   $quarter_no      Quarter number being enrolled (1-based)
     * @param array $candidates_in   Pre-filtered candidates (optional)
     *
     * @return array ['source' => 'ASSISTANCE'|'CAMPAIGN'|null, 'source_id' => int, 'discount_percent' => float, 'payment_mode' => string, 'valid_from_quarter' => int, 'valid_to_quarter' => int, 'candidates_json' => string]
     */
    public function resolve_benefit($enrollment_id, $group_id, $programme_id, $enrolled_on, $quarter_no = 1, $candidates_in = null)
    {
        $candidates = array();

        // --- Assistance candidates ---
        $assist_rows = $this->CI->db
            ->select('fac.id, fac.category_code, fac.discount_percent, fac.validity_quarters, fac.payment_mode')
            ->from('patient_flow_fee_assistance_category fac')
            ->join('patient_flow_programme_group pg', 'pg.id = fac.group_id', 'inner')
            ->where('fac.group_id', $group_id)
            ->where('fac.is_active', 1)
            ->get()
            ->result();

        foreach ($assist_rows as $a) {
            $candidates[] = array(
                'source'          => 'ASSISTANCE',
                'source_id'       => (int) $a->id,
                'percent'         => (float) $a->discount_percent,
                'validity'        => (int) $a->validity_quarters,
                'payment_mode'    => $a->payment_mode,
                'category_code'   => $a->category_code,
                'allow_stacking'  => 0,
            );
        }

        // --- Campaign candidates ---
        $camp_rows = $this->CI->db
            ->select('dc.id, dc.campaign_code, dc.discount_percent, dc.benefit_quarters, dc.payment_mode, dc.status, dc.start_date, dc.end_date, dc.allow_stacking')
            ->from('patient_flow_discount_campaign dc')
            ->where('dc.status', 'APPROVED')
            ->where('dc.start_date <=', $enrolled_on)
            ->where('dc.end_date >=', $enrolled_on)
            ->get()
            ->result();

        foreach ($camp_rows as $c) {
            // Scope check: no scope rows = all programmes; otherwise match group/programme
            $in_scope = true;
            $scope_rows = $this->CI->db
                ->where('campaign_id', (int) $c->id)
                ->get('patient_flow_discount_campaign_scope')
                ->result();
            if (!empty($scope_rows)) {
                $in_scope = false;
                foreach ($scope_rows as $s) {
                    if (($s->group_id === null || (int) $s->group_id === $group_id)
                        && ($s->programme_id === null || (int) $s->programme_id === $programme_id)) {
                        $in_scope = true;
                        break;
                    }
                }
            }
            if (!$in_scope) {
                continue;
            }

            $candidates[] = array(
                'source'          => 'CAMPAIGN',
                'source_id'       => (int) $c->id,
                'percent'         => (float) $c->discount_percent,
                'validity'        => (int) $c->benefit_quarters,
                'payment_mode'    => $c->payment_mode,
                'campaign_code'   => $c->campaign_code,
                'allow_stacking'  => ((int) ($c->allow_stacking ?? 0) === 1) ? 1 : 0,
            );
        }

        $stacking_allowed = false;
        foreach ($candidates as $candidate) {
            if (!empty($candidate['allow_stacking'])) {
                $stacking_allowed = true;
                break;
            }
        }

        // Default rule: one benefit only. If a campaign explicitly allows stacking,
        // combine the eligible percentages up to 100%. The underlying schedule table
        // still stores a single source row, so the highest-priority source remains the
        // record owner while candidates_json preserves the full list for audit.
        $winner = null;
        if (!empty($candidates)) {
            if ($stacking_allowed) {
                $combined = 0.0;
                $primary = null;
                foreach ($candidates as $candidate) {
                    $combined += (float) $candidate['percent'];
                    if ($primary === null || (float) $candidate['percent'] > (float) $primary['percent']) {
                        $primary = $candidate;
                    } elseif ((float) $candidate['percent'] === (float) $primary['percent']
                        && $candidate['source'] === 'ASSISTANCE') {
                        $primary = $candidate;
                    }
                }

                $winner = $primary;
                $winner['percent'] = min(100.0, (float) $combined);
                $winner['validity'] = max(array_map(function ($candidate) {
                    return (int) ($candidate['validity'] ?? 1);
                }, $candidates));
            } else {
                usort($candidates, function ($a, $b) {
                    if ($b['percent'] === $a['percent']) {
                        return ($a['source'] === 'ASSISTANCE') ? -1 : 1;
                    }
                    return ($b['percent'] <=> $a['percent']);
                });
                $winner = $candidates[0];
            }
        }

        $result = array(
            'source'             => $winner ? $winner['source'] : null,
            'source_id'          => $winner ? (int) $winner['source_id'] : null,
            'discount_percent'   => $winner ? (float) $winner['percent'] : 0.0,
            'payment_mode'       => $winner ? $winner['payment_mode'] : 'MILESTONE',
            'valid_from_quarter' => (int) $quarter_no,
            'valid_to_quarter'   => $winner ? (int) ($quarter_no + $winner['validity'] - 1) : (int) $quarter_no,
            'candidates_json'    => json_encode($candidates),
        );

        return $result;
    }

    /**
     * Split a later-quarter (assisted) fee across S1/S2/S3.
     * Whole-rupee integer maths, never floats.
     *
     * @param float $quarter_fee   Total quarter fee after discount
     * @param float $discount_pct  Discount percentage already applied
     * @return array [S1, S2, S3] amounts in whole rupees
     */
    public function split_later_quarter($quarter_fee, $discount_pct)
    {
        $total = (int) round($quarter_fee * (100 - $discount_pct) / 100);
        $third = (int) (round(($total / 3) / 10) * 10);       // S1 and S2: nearest ₹10
        return array($third, $third, $total - (2 * $third));  // S3: the remainder
    }

    /**
     * Generate payment schedule rows for an enrollment.
     *
     * Called at enrollment (Q1) and at each quarter start.
     *
     * @param int   $enrollment_id
     * @param int   $quarter_no
     * @param array $benefit      From resolve_benefit() result
     * @param array $milestones   Milestone rows from patient_flow_programme_fee_milestone
     * @param array $stages       Stage rows from stage_master for Q1 (Q1S1..Q1S4)
     * @param float $quarter_fee  Total quarter fee (standard amount for the quarter)
     *
     * @return array of schedule rows ready for insert
     */
    public function generate_schedule($enrollment_id, $quarter_no, $benefit, $milestones, $stages, $quarter_fee)
    {
        $discount = (float) ($benefit['discount_percent'] ?? 0);
        $payment_mode = $benefit['payment_mode'] ?? 'MILESTONE';
        $rows = array();

        $norm_milestones = array();
        foreach ((array) $milestones as $m) {
            $norm_milestones[] = is_array($m) ? $m : (array) $m;
        }

        $norm_stages = array();
        foreach ((array) $stages as $s) {
            $norm_stages[] = is_array($s) ? $s : (array) $s;
        }

        // --- Q1: use the stage-allocation values required by the fee table, unless no stage data is defined. ---
        if ($quarter_no === 1) {
            $has_stage_allocation = false;
            foreach ($norm_stages as $s) {
                if ((float) ($s['standard_amount'] ?? 0) > 0) {
                    $has_stage_allocation = true;
                    break;
                }
            }

            if ($has_stage_allocation) {
                $seq = 1;
                foreach ($norm_stages as $s) {
                    $standard = (float) ($s['standard_amount'] ?? 0);
                    if ($standard <= 0) {
                        continue;
                    }

                    $stage_id = (int) ($s['id'] ?? $s['stage_id'] ?? 0);
                    $disc_amount = 0.0;
                    $payable = $standard;
                    if ($discount > 0) {
                        $disc_amount = (float) round($standard * $discount / 100);
                        $payable = (float) round($standard - $disc_amount);
                    }

                    $rows[] = $this->_schedule_row($enrollment_id, 1, $seq, 'STAGE', null, $stage_id, $stage_id, $standard, $discount, $disc_amount, $payable, $benefit['source_id'] ?? null, 'PENDING');
                    $seq++;
                }
                return $rows;
            }

            $initial = null;
            $remaining = null;
            foreach ($norm_milestones as $m) {
                if (($m['milestone_code'] ?? null) === 'Q1_INITIAL') {
                    $initial = $m;
                }
                if (($m['milestone_code'] ?? null) === 'Q1_REMAINING') {
                    $remaining = $m;
                }
            }

            if ($initial) {
                $rows[] = $this->_schedule_row($enrollment_id, 1, 1, 'MILESTONE', 'Q1_INITIAL', null, null, $initial['amount'], 0, 0, $initial['amount'], $benefit['source_id'] ?? null, 'PENDING');
            }

            if ($remaining) {
                $disc_amount = (int) round($remaining['amount'] * $discount / 100);
                $payable = (int) round($remaining['amount'] - $disc_amount);
                $rows[] = $this->_schedule_row($enrollment_id, 1, 2, 'MILESTONE', 'Q1_REMAINING', null, null, $remaining['amount'], $discount, $disc_amount, $payable, $benefit['source_id'] ?? null, 'PENDING');
            }
        }
        // --- Q2+ ---
        else {
            // No benefit: single milestone
            if ($benefit['source'] === null || $discount <= 0) {
                $milestone = null;
                foreach ($norm_milestones as $m) {
                    if (($m['milestone_code'] ?? null) === 'LATER_QUARTER') {
                        $milestone = $m;
                        break;
                    }
                }
                if (!$milestone) {
                    $milestone = end($norm_milestones);
                }
                if ($milestone) {
                    $rows[] = $this->_schedule_row($enrollment_id, $quarter_no, 1, 'MILESTONE', $milestone['milestone_code'], null, null, $milestone['amount'], 0, 0, $milestone['amount'], null, 'PENDING');
                }
            }
            // With benefit: split across S1/S2/S3
            else {
                $split = $this->split_later_quarter($quarter_fee, $discount);
                $seq = 1;
                foreach ($norm_stages as $s) {
                    if ($seq > 3) {
                        break;
                    }
                    $stage_amount = (isset($split[$seq - 1])) ? (int) $split[$seq - 1] : 0;
                    $stage_id = (int) ($s['id'] ?? 0);
                    $rows[] = $this->_schedule_row($enrollment_id, $quarter_no, $seq, 'STAGE', null, $stage_id, $stage_id, $stage_amount, $discount, (int) round($stage_amount * $discount / 100), $stage_amount, $benefit['source_id'], 'PENDING');
                    $seq++;
                }
            }
        }

        return $rows;
    }

    /**
     * Build a single schedule row array.
     */
    protected function _schedule_row($enrollment_id, $quarter_no, $seq_no, $unit_type, $milestone_code, $stage_from_id, $stage_to_id, $standard_amount, $discount_percent, $discount_amount, $payable_amount, $benefit_id, $status)
    {
        return array(
            'enrollment_id'   => (int) $enrollment_id,
            'quarter_no'      => (int) $quarter_no,
            'seq_no'          => (int) $seq_no,
            'unit_type'       => $unit_type,
            'milestone_code'  => $milestone_code,
            'stage_from_id'   => (int) $stage_from_id,
            'stage_to_id'     => (int) $stage_to_id,
            'standard_amount' => (float) $standard_amount,
            'discount_percent'=> (float) $discount_percent,
            'discount_amount' => (float) $discount_amount,
            'payable_amount'  => (float) $payable_amount,
            'benefit_id'      => $benefit_id ? (int) $benefit_id : null,
            'status'          => $status,
            'paid_at'         => null,
            'payment_ref'     => null,
            'generated_at'    => date('Y-m-d H:i:s'),
        );
    }

    /**
     * Verification: worked examples from programme_patientflow.md.
     *
     * Expected results:
     *   HFT one partner, Cat A, ordinary quarter (10,000 x 60%) = 6,000  -> 2,000 / 2,000 / 2,000
     *   FSC, Cat A, ordinary quarter (8,000 x 80%)            = 6,400  -> 2,130 / 2,130 / 2,140
     *   FSC, Cat C, ordinary quarter (8,000 x 50%)            = 4,000  -> 1,330 / 1,330 / 1,340
     *
     * @return array of test results
     */
    public function verify_examples()
    {
        $tests = array(
            array('fee' => 10000, 'disc' => 40, 'expected' => array(2000, 2000, 2000), 'label' => 'HFT Cat A ordinary (10000 x 60%)'),
            array('fee' => 8000,  'disc' => 20, 'expected' => array(2130, 2130, 2140), 'label' => 'FSC Cat A ordinary (8000 x 80%)'),
            array('fee' => 8000,  'disc' => 50, 'expected' => array(1330, 1330, 1340), 'label' => 'FSC Cat C ordinary (8000 x 50%)'),
        );

        $results = array();
        foreach ($tests as $t) {
            $got = $this->split_later_quarter($t['fee'], $t['disc']);
            $results[] = array(
                'label'     => $t['label'],
                'fee'       => $t['fee'],
                'discount'  => $t['disc'],
                'expected'  => $t['expected'],
                'got'       => $got,
                'pass'      => ($got === $t['expected']),
            );
        }
        return $results;
    }
}