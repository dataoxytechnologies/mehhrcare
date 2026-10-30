<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
?>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <?php $this->load->view('admin/report/_patient'); ?>
            <div class="card-header ptbnull"></div>
            <div class="card-header">
                <h3 class="card-title titlefix"><i class="fa fa-sitemap"></i> <?php echo $this->lang->line('patient_flow_report') ?: 'Patient Flow Report'; ?></h3>
            </div>
            <form method="post" action="<?php echo base_url('admin/patientflow/patientflowreport'); ?>" autocomplete="off">
                <div class="card-body pb-0">
                    <?php echo $this->customlib->getCSRF(); ?>
                    <div class="row mb-3">
                        <div class="col-lg-3 col-sm-6 col-md-3">
                            <div class="mb-3">
                                <label><?php echo $this->lang->line('patient_name') ?: 'Name'; ?></label>
                                <input type="text" name="patient_name" class="form-control" value="<?php echo isset($filters['patient_name']) ? html_escape($filters['patient_name']) : ''; ?>" placeholder="<?php echo $this->lang->line('patient_name') ?: 'Name'; ?>">
                            </div>
                        </div>
                        <div class="col-lg-3 col-sm-6 col-md-3">
                            <div class="mb-3">
                                <label><?php echo $this->lang->line('mobile_number') ?: 'Mobile No'; ?></label>
                                <input type="text" name="mobileno" class="form-control" value="<?php echo isset($filters['mobileno']) ? html_escape($filters['mobileno']) : ''; ?>">
                            </div>
                        </div>
                        <div class="col-lg-3 col-sm-6 col-md-3">
                            <div class="mb-3">
                                <label><?php echo $this->lang->line('visit_no') ?: 'Visit No'; ?></label>
                                <input type="text" name="visit_no" class="form-control" value="<?php echo isset($filters['visit_no']) ? html_escape($filters['visit_no']) : ''; ?>">
                            </div>
                        </div>
                        <div class="col-lg-3 col-sm-6 col-md-3">
                            <div class="mb-3">
                                <label><?php echo $this->lang->line('flow_level') ?: 'Flow Level'; ?></label>
                                <select name="level_id" class="form-control">
                                    <option value=""><?php echo $this->lang->line('select') ?: 'Select'; ?></option>
                                    <?php foreach ($levels as $lv) { ?>
                                        <option value="<?php echo (int)$lv->id; ?>" <?php echo ((isset($filters['level_id']) && $filters['level_id'] == $lv->id) ? 'selected' : ''); ?>><?php echo html_escape($lv->code . ' - ' . $lv->title); ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                    </div>
<div class="row mb-3">
                        <div class="col-lg-3 col-sm-6 col-md-3">
                            <div class="mb-3">
                                <label><?php echo $this->lang->line('visit_status') ?: 'Status'; ?></label>
                                <select name="status" class="form-control">
                                    <option value=""><?php echo $this->lang->line('select') ?: 'Select'; ?></option>
                                    <?php foreach (array('active', 'completed', 'cancelled') as $s) { ?>
                                        <option value="<?php echo $s; ?>" <?php echo ((isset($filters['status']) && $filters['status'] == $s) ? 'selected' : ''); ?>><?php echo ucfirst($s); ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-3 col-sm-6 col-md-3">
                            <div class="mb-3">
                                <label><?php echo $this->lang->line('payment_status') ?: 'Payment Status'; ?></label>
                                <select name="payment_status" class="form-control">
                                    <option value=""><?php echo $this->lang->line('select') ?: 'Select'; ?></option>
                                    <?php foreach (array('pending', 'paid') as $s) { ?>
                                        <option value="<?php echo $s; ?>" <?php echo ((isset($filters['payment_status']) && $filters['payment_status'] == $s) ? 'selected' : ''); ?>><?php echo ucfirst($s); ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-3 col-sm-6 col-md-3">
                            <div class="mb-3">
                                <label><?php echo $this->lang->line('approve_status') ?: 'Approve Status'; ?></label>
                                <select name="approve_status" class="form-control">
                                    <option value=""><?php echo $this->lang->line('select') ?: 'Select'; ?></option>
                                    <?php foreach (array('pending', 'approved') as $s) { ?>
                                        <option value="<?php echo $s; ?>" <?php echo ((isset($filters['approve_status']) && $filters['approve_status'] == $s) ? 'selected' : ''); ?>><?php echo ucfirst($s); ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-3 col-sm-6 col-md-3">
                            <div class="mb-3">
                                <label><?php echo $this->lang->line('programme_status') ?: 'Programme Status'; ?></label>
                                <select name="programme_status" class="form-control">
                                    <option value=""><?php echo $this->lang->line('select') ?: 'Select'; ?></option>
                                    <?php $pstatuses = !empty($programme_statuses) ? $programme_statuses : array('enroll', 'declined', 'deferred'); foreach ($pstatuses as $s) { $s = is_array($s) ? (isset($s['value']) ? $s['value'] : '') : (is_object($s) ? (isset($s->value) ? $s->value : '') : $s); ?>
                                        <option value="<?php echo html_escape($s); ?>" <?php echo ((isset($filters['programme_status']) && $filters['programme_status'] == $s) ? 'selected' : ''); ?>><?php echo ucfirst($s); ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-lg-3 col-sm-6 col-md-3">
                            <div class="mb-3">
                                <label><?php echo $this->lang->line('token_status') ?: 'Token Status'; ?></label>
                                <select name="token_status" class="form-control">
                                    <option value=""><?php echo $this->lang->line('select') ?: 'Select'; ?></option>
                                    <?php foreach (array('NOT_ISSUED', 'ACTIVE', 'CALLED', 'CLOSED') as $s) { ?>
                                        <option value="<?php echo $s; ?>" <?php echo ((isset($filters['token_status']) && $filters['token_status'] == $s) ? 'selected' : ''); ?>><?php echo $s; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-3 col-sm-6 col-md-3">
                            <div class="mb-3">
                                <label><?php echo $this->lang->line('date_from') ?: 'From Date'; ?></label>
                                <input type="date" name="date_from" class="form-control" value="<?php echo isset($filters['date_from']) ? html_escape($filters['date_from']) : ''; ?>">
                            </div>
                        </div>
                        <div class="col-lg-3 col-sm-6 col-md-3">
                            <div class="mb-3">
                                <label><?php echo $this->lang->line('date_to') ?: 'To Date'; ?></label>
                                <input type="date" name="date_to" class="form-control" value="<?php echo isset($filters['date_to']) ? html_escape($filters['date_to']) : ''; ?>">
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-auto d-flex align-items-end ps-md-3">
                            <div class="mb-3 d-flex gap-1">
                                <button type="submit" class="btn btn-primary d-inline-flex align-items-center gap-1 py-2"><i class="fa fa-search"></i> <?php echo $this->lang->line('search') ?: 'Search'; ?></button>
                                <a href="<?php echo base_url('admin/patientflow/patientflowreport'); ?>" class="btn btn-default d-inline-flex align-items-center gap-1 py-2"><i class="fa fa-refresh"></i> <?php echo $this->lang->line('reset') ?: 'Reset'; ?></a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
            <div class="card-body pt-0">
                <?php if (empty($is_search)) { ?>
                    <div class="alert alert-info"><?php echo $this->lang->line('search_to_view') ?: 'Use the filters above and click Search to show results.'; ?></div>
                <?php } elseif (empty($visits)) { ?>
                    <div class="alert alert-info"><?php echo $this->lang->line('no_record_found') ?: 'No records found'; ?></div>
                <?php } else { ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th><?php echo $this->lang->line('visit_no') ?: 'Visit No'; ?></th>
                                    <th><?php echo $this->lang->line('patient_info') ?: 'Name'; ?></th>
                                    <th><?php echo $this->lang->line('flow_level') ?: 'Level'; ?></th>
                                    <th><?php echo $this->lang->line('visit_status') ?: 'Status'; ?></th>
                                    <th><?php echo $this->lang->line('payment_status') ?: 'Payment'; ?></th>
                                    <th><?php echo $this->lang->line('approve_status') ?: 'Approve'; ?></th>
                                    <th><?php echo $this->lang->line('programme_status') ?: 'Programme'; ?></th>
                                    <th><?php echo $this->lang->line('token_status') ?: 'Token'; ?></th>
                                    <th class="text-end"><?php echo $this->lang->line('amount') ?: 'Amount'; ?></th>
                                    <th><?php echo $this->lang->line('created_at') ?: 'Date'; ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($visits as $v) { ?>
                                <tr>
                                    <td><?php echo html_escape($v->visit_no); ?></td>
                                    <td><?php echo html_escape($v->patient_name); ?><br><small class="text-muted">UID: <?php echo html_escape($v->patient_uuid); ?><br><?php echo 'Mobile'; ?>: <?php echo html_escape($v->mobileno); ?></small></td>
                                    <td><span class="label label-primary"><?php echo html_escape($v->level_code); ?></span><br> <?php echo html_escape($v->level_title); ?></td>
                                    <td><span class="label label-info"><?php echo ucfirst($v->status); ?></span></td>
                                    <td><span class="label <?php echo ($v->payment_status === 'paid') ? 'label-success' : 'label-warning'; ?>"><?php echo ucfirst($v->payment_status); ?></span></td>
                                    <td><span class="label label-default"><?php echo ucfirst($v->approve_status); ?></span></td>
                                    <td><?php echo $v->programme_status ? ucfirst($v->programme_status) : '&mdash;'; ?></td>
                                    <td><?php echo $v->token_status ? $v->token_status : '&mdash;'; ?></td>
                                    <td class="text-end"><?php echo number_format((float)$v->amount, 2); ?></td>
                                    <td><?php echo date('d-m-Y H:i', strtotime($v->created_at)); ?></td>
                                </tr>
                                <?php } ?>
                                <?php
                                // Running total of the amount column
                                $report_total = 0;
                                foreach ($visits as $rt) {
                                    $report_total += (float) $rt->amount;
                                }
                                ?>
                            </tbody>
                            <tfoot>
                                <tr class="active">
                                    <th colspan="8" class="text-end"><?php echo $this->lang->line('total') ?: 'Total'; ?></th>
                                    <th class="text-end"><?php echo number_format($report_total, 2); ?></th>
                                    <th>&nbsp;</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>
