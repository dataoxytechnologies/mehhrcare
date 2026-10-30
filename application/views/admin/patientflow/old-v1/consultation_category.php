<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
        <div class="d-flex align-items-center gap-2">
            <i class="fa fa-tags sh-icon-soft"></i>
            <h3 class="card-title titlefix mb-0"><?php echo $this->lang->line('consultation_category') ?: 'Consultation Category'; ?></h3>
        </div>
    </div>

    <?php if ($this->session->flashdata('success_msg')) { ?>
        <div class="card-body py-2"><?php echo $this->session->flashdata('success_msg'); ?></div>
    <?php } ?>
    <?php if ($this->session->flashdata('error_msg')) { ?>
        <div class="card-body py-2"><?php echo $this->session->flashdata('error_msg'); ?></div>
    <?php } ?>

    <!-- Add / Edit form -->
    <div class="card-body border-bottom">
        <form id="cc_form" action="<?php echo site_url('admin/patientflow/save_consultation_category'); ?>" method="post" accept-charset="utf-8">
            <input type="hidden" name="id" id="cc_id" value="<?php echo $edit_category ? (int) $edit_category->id : 0; ?>">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label form-label-sm"><?php echo $this->lang->line('name'); ?> <small class="req">*</small></label>
                    <input type="text" name="name" id="cc_name" class="form-control" required
                           value="<?php echo $edit_category ? htmlspecialchars($edit_category->name) : ''; ?>"
                           placeholder="<?php echo $this->lang->line('name'); ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label form-label-sm"><?php echo $this->lang->line('amount') ?: 'Amount'; ?> (<?php echo $this->customlib->getHospitalCurrencyFormat() ?: ''; ?>)</label>
                    <input type="number" step="0.01" min="0" name="amount" id="cc_amount" class="form-control"
                           value="<?php echo $edit_category ? $edit_category->amount : ''; ?>" placeholder="0.00">
                </div>
                <div class="col-md-4">
                    <label class="form-label form-label-sm"><?php echo $this->lang->line('description') ?: 'Description'; ?></label>
                    <input type="text" name="description" id="cc_description" class="form-control"
                           value="<?php echo $edit_category ? htmlspecialchars((string) $edit_category->description) : ''; ?>">
                </div>
                <div class="col-md-1">
                    <label class="form-label form-label-sm"><?php echo $this->lang->line('active') ?: 'Active'; ?></label>
                    <div class="form-check form-switch mt-1">
                        <input class="form-check-input" type="checkbox" name="is_active" id="cc_is_active" value="1"
                            <?php echo ($edit_category && !$edit_category->is_active) ? '' : 'checked'; ?>>
                    </div>
                </div>
                <div class="col-md-2 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="fa fa-check"></i> <?php echo $this->lang->line('save') ?: 'Save'; ?>
                    </button>
                    <?php if ($edit_category) { ?>
                        <a href="<?php echo site_url('admin/patientflow/consultation_category'); ?>" class="btn btn-default btn-sm">
                            <i class="fa fa-times"></i>
                        </a>
                    <?php } ?>
                </div>
            </div>
        </form>
    </div>


    <!-- Listing -->
    <div class="card-body table-responsive">
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th><?php echo $this->lang->line('category_id') ?: 'ID'; ?></th>
                    <th><?php echo $this->lang->line('name'); ?></th>
                    <th><?php echo $this->lang->line('amount') ?: 'Amount'; ?></th>
                    <th><?php echo $this->lang->line('description') ?: 'Description'; ?></th>
                    <th><?php echo $this->lang->line('status'); ?></th>
                    <th class="text-right"><?php echo $this->lang->line('action'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($categories)) { ?>
                    <tr>
                        <td colspan="6" class="text-center text-muted py-3">
                            <?php echo $this->lang->line('no_record_found'); ?>
                        </td>
                    </tr>
                <?php } ?>
                <?php foreach ($categories as $cat) { ?>
                    <tr>
                        <td><?php echo (int) $cat->id; ?></td>
                        <td><?php echo htmlspecialchars($cat->name); ?></td>
                        <td><?php echo number_format((float) $cat->amount, 2); ?></td>
                        <td><?php echo htmlspecialchars((string) $cat->description); ?></td>
                        <td>
                            <span class="badge <?php echo $cat->is_active ? 'bg-success' : 'bg-secondary'; ?>">
                                <?php echo $cat->is_active ? ($this->lang->line('active') ?: 'Active') : ($this->lang->line('inactive') ?: 'Inactive'); ?>
                            </span>
                        </td>
                        <td class="text-right">
                            <a href="<?php echo site_url('admin/patientflow/consultation_category/' . (int) $cat->id); ?>"
                               class="btn btn-default btn-xs" title="<?php echo $this->lang->line('edit'); ?>">
                                <i class="fa fa-pencil"></i>
                            </a>
                            <a href="<?php echo site_url('admin/patientflow/delete_consultation_category/' . (int) $cat->id); ?>"
                               class="btn btn-default btn-xs" title="<?php echo $this->lang->line('delete'); ?>"
                               onclick="return confirm('<?php echo $this->lang->line('delete_confirm') ?: 'Are you sure you want to delete this record?'; ?>');">
                                <i class="fa fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>
