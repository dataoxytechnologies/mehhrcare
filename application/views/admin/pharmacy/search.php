<?php $currency_symbol = $this->customlib->getHospitalCurrencyFormat(); ?>
<div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title titlefix"><?php echo $this->lang->line('medicines_stock'); ?></h3>
                        <div class="d-flex gap-2 align-items-center flex-wrap float-end">
                            <?php if ($this->rbac->hasPrivilege('import_medicine', 'can_view')) { ?>                
                                <a  href="<?php echo base_url(); ?>admin/pharmacy/import" class="btn btn-primary btn-sm"><i class="fa fa-upload"></i> <?php echo $this->lang->line('import_medicine'); ?>
                                </a>
                            <?php } ?>
                            <?php if ($this->rbac->hasPrivilege('medicine', 'can_add')) { ?>
                                <a data-bs-toggle="modal" onclick="holdModal('myModal')" class="btn btn-primary btn-sm addmedicine"><i class="fa fa-plus"></i> <?php echo $this->lang->line('add_medicine'); ?></a> 
                            <?php } ?>
                            <?php if ($this->rbac->hasPrivilege('medicine_purchase', 'can_view')) { ?>
                                <a href="<?php echo base_url(); ?>admin/pharmacy/purchase" class="btn btn-primary btn-sm"><i class="fa fa-reorder"></i> <?php echo $this->lang->line('purchase'); ?></a>
                            <?php } ?>
                        </div>
                    </div><!-- /.card-header -->
                    <div class="card-body">
                        <div class="download_label"><?php echo $this->lang->line('medicines_stock'); ?></div>

                        <input type="hidden" id="medicine_name_filter">

                        <?php if ($this->rbac->hasPrivilege('medicine', 'can_delete')) { ?>
                            <div class="d-flex justify-content-end pb-3">
                                <button type="button" class="btn btn-primary btn-sm delete_selected" id="load" data-loading-text="<i class='fa fa-spinner fa-spin '></i> <?php echo $this->lang->line('please_wait'); ?>"><i class="fa fa-trash"></i> <?php echo $this->lang->line('delete_selected'); ?></button>
                            </div>
                        <?php } ?>

                        <div class="table-responsive-mobile">
                        <table class="table table-striped table-bordered table-hover ajaxlist " cellspacing="0" width="100%" data-export-title="<?php echo $this->lang->line('medicines_stock'); ?>">
                            <thead>
                                <tr>
                                    <th class="noExport"><input type="checkbox" name="checkAll"> #</th>
                                    <th><?php echo $this->lang->line('medicine_name'); ?></th>
                                    <th><?php echo $this->lang->line('medicine_company'); ?></th>
                                    <th><?php echo $this->lang->line('medicine_composition'); ?></th>
                                    <th><?php echo $this->lang->line('medicine_category'); ?></th> 
                                    <th><?php echo $this->lang->line('medicine_group'); ?></th>
                                    <th><?php echo $this->lang->line('unit'); ?></th>
                                    <th><?php echo $this->lang->line('available_qty'); ?></th>
                                    <th width="10%" class="text-end noExport"><?php echo $this->lang->line('action'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>                            
                      </div>
                    </div>
                </div>                                                    
            </div>                                                                                                                                          
        </div>

<div class="modal fade sh-modal sh-modal-accent" id="myModal" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myModalLabel"><?php echo $this->lang->line('add_medicine_details'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formadd" accept-charset="utf-8" method="post" enctype="multipart/form-data">
                <div class="pup-scroll-area"><div class="modal-body modal-background">

                    <div class="sh-form-card mb-3">
                        <div class="sh-card-header">
                            <span class="sh-card-header-title"><?php echo $this->lang->line('medicine_details'); ?></span>
                        </div>
                        <div class="p-3">
                            <div class="row g-3">
                                <div class="col-sm-3">
                                    <label><?php echo $this->lang->line('medicine_name'); ?> <small class="req">*</small></label>
                                    <input id="medicine_name" name="medicine_name" type="text" class="form-control">
                                    <span class="text-danger"><?php echo form_error('medicine_name'); ?></span>
                                </div>
                                <div class="col-sm-3">
                                    <label><?php echo $this->lang->line('medicine_category'); ?> <small class="req">*</small></label>
                                    <select class="form-control select2 medicine_category_id" name="medicine_category_id">
                                        <option value="<?php echo set_value('medicine_category_id'); ?>"><?php echo $this->lang->line('select'); ?></option>
                                        <?php foreach ($medicineCategory as $dkey => $dvalue) { ?>
                                        <option value="<?php echo $dvalue['id']; ?>"><?php echo $dvalue['medicine_category']; ?></option>
                                        <?php } ?>
                                    </select>
                                    <span class="text-danger"><?php echo form_error('medicine_category_id'); ?></span>
                                </div>
                                <div class="col-sm-3">
                                    <label><?php echo $this->lang->line('medicine_company'); ?></label>
                                    <select name="medicine_company" class="form-control">
                                        <option value=""><?php echo $this->lang->line('select'); ?></option>
                                        <?php foreach ($company as $key => $value) { ?>
                                        <option value="<?php echo $value['id']; ?>"><?php echo $value['company_name']; ?></option>
                                        <?php } ?>
                                    </select>
                                    <span class="text-danger"><?php echo form_error('medicine_company'); ?></span>
                                </div>
                                <div class="col-sm-3">
                                    <label><?php echo $this->lang->line('medicine_composition'); ?></label>
                                    <input type="text" name="medicine_composition" value="" class="form-control">
                                    <span class="text-danger"><?php echo form_error('medicine_composition'); ?></span>
                                </div>
                                <div class="col-sm-3">
                                    <label><?php echo $this->lang->line('medicine_group'); ?></label>
                                    <select name="medicine_group" class="form-control">
                                        <option value=""><?php echo $this->lang->line('select'); ?></option>
                                        <?php foreach ($get_medicine_group as $key => $value) { ?>
                                        <option value="<?php echo $value['id']; ?>"><?php echo $value['group_name']; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="col-sm-3">
                                    <label><?php echo $this->lang->line('unit'); ?> <small class="req">*</small></label>
                                    <select name="unit" class="form-control">
                                        <option value=""><?php echo $this->lang->line('select'); ?></option>
                                        <?php foreach ($unitname as $key => $value) { ?>
                                        <option value="<?php echo $value['id']; ?>"><?php echo $value['unit_name']; ?></option>
                                        <?php } ?>
                                    </select>
                                    <span class="text-danger"><?php echo form_error('unit'); ?></span>
                                </div>
                                <div class="col-sm-3">
                                    <label><?php echo $this->lang->line('min_level'); ?></label>
                                    <input type="text" name="min_level" class="form-control">
                                </div>
                                <div class="col-sm-3">
                                    <label><?php echo $this->lang->line('re_order_level'); ?></label>
                                    <input type="text" name="reorder_level" class="form-control">
                                </div>
                                <div class="col-sm-3">
                                    <label><?php echo $this->lang->line('tax'); ?></label>
                                    <div class="input-group">
                                        <input type="text" class="form-control right-border-none" name="vat" autocomplete="off">
                                        <span class="input-group-text">%</span>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <label><?php echo $this->lang->line('unit_packing'); ?> <small class="req">*</small></label>
                                    <input type="text" name="unit_packing" class="form-control">
                                    <span class="text-danger"><?php echo form_error('unit_packing'); ?></span>
                                </div>
                                <div class="col-sm-3">
                                    <label><?php echo $this->lang->line('vat_a_c'); ?></label>
                                    <input type="text" name="vat_ac" class="form-control">
                                </div>
                                <div class="col-sm-3">
                                    <label><?php echo $this->lang->line('rack_number'); ?></label>
                                    <input type="text" name="rack_number" class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="sh-form-card mb-0">
                        <div class="sh-card-header">
                            <span class="sh-card-header-title"><?php echo $this->lang->line('add_more_details'); ?></span>
                        </div>
                        <div class="p-3">
                            <div class="row g-3">
                                <div class="col-sm-6">
                                    <label><?php echo $this->lang->line('note'); ?></label>
                                    <textarea name="note" class="form-control"></textarea>
                                </div>
                                <div class="col-sm-6">
                                    <label><?php echo $this->lang->line('medicine_photo_jpg_jpeg_png'); ?></label>
                                    <input type="file" name="file" id="file" class="form-control filestyle">
                                </div>
                            </div>
                        </div>
                    </div>

                </div></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                    <button type="submit" id="formaddbtn" data-loading-text='<i class="fa fa-circle-o-notch fa-spin"></i>' class="btn btn-info"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade sh-modal sh-modal-accent" id="myModalImport" tabindex="-1" aria-labelledby="myModalImportLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myModalImportLabel"><?php echo $this->lang->line('add') . " " . $this->lang->line('medicine'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formimp" accept-charset="utf-8" method="post" enctype="multipart/form-data">
                <div class="pup-scroll-area"><div class="modal-body">
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <label class="form-label"><?php echo $this->lang->line('medicine') . " " . $this->lang->line('category'); ?></label><small class="req"> *</small>
                            <select class="form-control select2 medicine_category_id" name='medicine_category_id'>
                                <option value="<?php echo set_value('medicine_category_id'); ?>"><?php echo $this->lang->line('select') ?></option>
                                <?php foreach ($medicineCategory as $dkey => $dvalue) { ?>
                                    <option value="<?php echo $dvalue["id"]; ?>"><?php echo $dvalue["medicine_category"] ?></option>
                                <?php } ?>
                            </select>
                            <span class="text-danger"><?php echo form_error('medicine_category_id'); ?></span>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label"><?php echo $this->lang->line('medicine'); ?> CSV File Upload</label>
                            <input type="file" name="medicine_image" class="form-control filestyle" />
                        </div>
                    </div>
                </div></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                    <button type="submit" id="formimpbtn" data-loading-text="<?php echo $this->lang->line('processing') ?>" class="btn btn-info">Import <?php echo $this->lang->line('medicine'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade sh-modal sh-modal-accent" id="myModaledit" tabindex="-1" aria-labelledby="myModaleditLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myModaleditLabel"><?php echo $this->lang->line('edit_medicine_details'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formedit" accept-charset="utf-8" enctype="multipart/form-data" method="post">
                <div class="pup-scroll-area"><div class="modal-body modal-background">
                    <input type="hidden" name="id" id="id" value="<?php echo set_value('id'); ?>">

                    <div class="sh-form-card mb-3">
                        <div class="sh-card-header">
                            <span class="sh-card-header-title"><?php echo $this->lang->line('medicine_details'); ?></span>
                        </div>
                        <div class="p-3">
                            <div class="row g-3">
                                <div class="col-sm-3">
                                    <label><?php echo $this->lang->line('medicine_name'); ?> <small class="req">*</small></label>
                                    <input type="text" id="medicines_name" name="medicine_name" value="<?php echo set_value('medicine_name'); ?>" class="form-control">
                                    <span class="text-danger"><?php echo form_error('medicine_name'); ?></span>
                                </div>
                                <div class="col-sm-3">
                                    <label><?php echo $this->lang->line('medicine_category'); ?> <small class="req">*</small></label>
                                    <select class="form-control select2" name="medicine_category_id" id="medicines_category_id">
                                        <option value="<?php echo set_value('medicine_category_id'); ?>"><?php echo $this->lang->line('select'); ?></option>
                                        <?php foreach ($medicineCategory as $dkey => $dvalue) { ?>
                                        <option value="<?php echo $dvalue['id']; ?>"><?php echo $dvalue['medicine_category']; ?></option>
                                        <?php } ?>
                                    </select>
                                    <span class="text-danger"><?php echo form_error('medicine_category_id'); ?></span>
                                </div>
                                <div class="col-sm-3">
                                    <label><?php echo $this->lang->line('medicine_company'); ?></label>
                                    <select id="medicine_company" name="medicine_company" class="form-control">
                                        <option value=""><?php echo $this->lang->line('select'); ?></option>
                                        <?php foreach ($company as $key => $value) { ?>
                                        <option value="<?php echo $value['id']; ?>"><?php echo $value['company_name']; ?></option>
                                        <?php } ?>
                                    </select>
                                    <span class="text-danger"><?php echo form_error('medicine_company'); ?></span>
                                </div>
                                <div class="col-sm-3">
                                    <label><?php echo $this->lang->line('medicine_composition'); ?></label>
                                    <input type="text" id="medicine_composition" name="medicine_composition" value="<?php echo set_value('medicine_composition'); ?>" class="form-control">
                                    <span class="text-danger"><?php echo form_error('medicine_composition'); ?></span>
                                </div>
                                <div class="col-sm-3">
                                    <label><?php echo $this->lang->line('medicine_group'); ?></label>
                                    <select name="medicine_group" id="medicine_group" class="form-control">
                                        <option value=""><?php echo $this->lang->line('select'); ?></option>
                                        <?php foreach ($get_medicine_group as $key => $value) { ?>
                                        <option value="<?php echo $value['id']; ?>"><?php echo $value['group_name']; ?></option>
                                        <?php } ?>
                                    </select>
                                    <span class="text-danger"><?php echo form_error('medicine_group'); ?></span>
                                </div>
                                <div class="col-sm-3">
                                    <label><?php echo $this->lang->line('unit'); ?> <small class="req">*</small></label>
                                    <select name="unit" id="unit" class="form-control">
                                        <option value=""><?php echo $this->lang->line('select'); ?></option>
                                        <?php foreach ($unitname as $key => $value) { ?>
                                        <option value="<?php echo $value['id']; ?>"><?php echo $value['unit_name']; ?></option>
                                        <?php } ?>
                                    </select>
                                    <span class="text-danger"><?php echo form_error('unit'); ?></span>
                                </div>
                                <div class="col-sm-3">
                                    <label><?php echo $this->lang->line('min_level'); ?></label>
                                    <input type="text" name="min_level" id="min_level" value="<?php echo set_value('min_level'); ?>" class="form-control">
                                </div>
                                <div class="col-sm-3">
                                    <label><?php echo $this->lang->line('re_order_level'); ?></label>
                                    <input type="text" name="reorder_level" id="reorder_level" value="<?php echo set_value('reorder_level'); ?>" class="form-control">
                                </div>
                                <div class="col-sm-3">
                                    <label><?php echo $this->lang->line('tax'); ?></label>
                                    <div class="input-group">
                                        <input type="text" id="vat" name="vat" value="<?php echo set_value('vat'); ?>" class="form-control right-border-none" autocomplete="off">
                                        <span class="input-group-text">%</span>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <label><?php echo $this->lang->line('unit_packing'); ?> <small class="req">*</small></label>
                                    <input type="text" id="unit_packing" name="unit_packing" value="<?php echo set_value('unit_packing'); ?>" class="form-control">
                                    <span class="text-danger"><?php echo form_error('unit_packing'); ?></span>
                                </div>
                                <div class="col-sm-3">
                                    <label><?php echo $this->lang->line('vat_a_c'); ?></label>
                                    <input type="text" id="vat_ac" name="vat_ac" value="<?php echo set_value('vat_ac'); ?>" class="form-control">
                                </div>
                                <div class="col-sm-3">
                                    <label><?php echo $this->lang->line('rack_number'); ?></label>
                                    <input type="text" id="rack_number" name="rack_number" value="<?php echo set_value('rack_number'); ?>" class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="sh-form-card mb-0">
                        <div class="sh-card-header">
                            <span class="sh-card-header-title"><?php echo $this->lang->line('add_more_details'); ?></span>
                        </div>
                        <div class="p-3">
                            <div class="row g-3">
                                <div class="col-sm-6">
                                    <label><?php echo $this->lang->line('note'); ?></label>
                                    <textarea id="edit_note" name="edit_note" class="form-control"></textarea>
                                </div>
                                <div class="col-sm-6">
                                    <label><?php echo $this->lang->line('medicine_photo'); ?></label>
                                    <input type="file" name="medicine_image" class="form-control filestyle">
                                    <span class="text-danger"><?php echo form_error('image'); ?></span>
                                    <input type="hidden" name="pre_medicine_image" id="pre_medicine_image" class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>

                </div></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                    <button type="submit" id="formeditbtn" data-loading-text='<i class="fa fa-circle-o-notch fa-spin"></i>' class="btn btn-info"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade sh-modal sh-modal-nospace" id="viewModal" tabindex="-1" aria-labelledby="viewModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewModalLabel"><?php echo $this->lang->line('medicine_details'); ?></h5>
                <div class="d-flex align-items-center gap-2">
                    <div id='edit_delete' class="d-flex align-items-center gap-2">
                        <a href="#" onclick="holdModal('myModaledit')" class="btn btn-sm btn-light" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('edit'); ?>"><i class="fa fa-pencil"></i></a>
                        <a href="#" class="btn btn-sm btn-light" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('delete'); ?>"><i class="fa fa-trash"></i></a>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            </div>
            <div class="pup-scroll-area"><div class="modal-body modal-background">
                <div class="sh-form-card mb-2">
                    <div class="sh-card-header d-flex align-items-center justify-content-between">
                        <span class="sh-card-header-title"><?php echo $this->lang->line('medicine_details'); ?></span>
                        <img id="medicine_image" src="#" class="rounded sh-med-img-thumb" />
                    </div>
                    <form id="view" accept-charset="utf-8" method="get">
                        <div class="sh-info-grid">
                            <div class="row g-0">
                                <div class="col-6 sh-info-item">
                                    <span class="sh-info-label"><?php echo $this->lang->line('medicine_name'); ?></span>
                                    <span class="sh-info-value highlight" id="medicine_names"></span>
                                </div>
                                <div class="col-6 sh-info-item">
                                    <span class="sh-info-label"><?php echo $this->lang->line('medicine_category'); ?></span>
                                    <span class="sh-info-value" id="medicine_category_ids"></span>
                                </div>
                            </div>
                            <div class="row g-0 sh-row-divider">
                                <div class="col-6 sh-info-item">
                                    <span class="sh-info-label"><?php echo $this->lang->line('medicine_company'); ?></span>
                                    <span class="sh-info-value" id="medicine_companys"></span>
                                </div>
                                <div class="col-6 sh-info-item">
                                    <span class="sh-info-label"><?php echo $this->lang->line('medicine_composition'); ?></span>
                                    <span class="sh-info-value" id="medicine_compositions"></span>
                                </div>
                            </div>
                            <div class="row g-0 sh-row-divider">
                                <div class="col-6 sh-info-item">
                                    <span class="sh-info-label"><?php echo $this->lang->line('medicine_group'); ?></span>
                                    <span class="sh-info-value" id="medicine_groups"></span>
                                </div>
                                <div class="col-6 sh-info-item">
                                    <span class="sh-info-label"><?php echo $this->lang->line('unit'); ?></span>
                                    <span class="sh-info-value" id="units"></span>
                                </div>
                            </div>
                            <div class="row g-0 sh-row-divider">
                                <div class="col-6 sh-info-item">
                                    <span class="sh-info-label"><?php echo $this->lang->line('min_level'); ?></span>
                                    <span class="sh-info-value" id="min_levels"></span>
                                </div>
                                <div class="col-6 sh-info-item">
                                    <span class="sh-info-label"><?php echo $this->lang->line('re_order_level'); ?></span>
                                    <span class="sh-info-value" id="reorder_levels"></span>
                                </div>
                            </div>
                            <div class="row g-0 sh-row-divider">
                                <div class="col-6 sh-info-item">
                                    <span class="sh-info-label"><?php echo $this->lang->line('tax'); ?> (%)</span>
                                    <span class="sh-info-value" id="vats"></span>
                                </div>
                                <div class="col-6 sh-info-item">
                                    <span class="sh-info-label"><?php echo $this->lang->line('unit_packing'); ?></span>
                                    <span class="sh-info-value" id="unit_packings"></span>
                                </div>
                            </div>
                            <div class="row g-0 sh-row-divider">
                                <div class="col-6 sh-info-item">
                                    <span class="sh-info-label"><?php echo $this->lang->line('vat_a_c'); ?></span>
                                    <span class="sh-info-value" id="vat_acs"></span>
                                </div>
                                <div class="col-6 sh-info-item">
                                    <span class="sh-info-label"><?php echo $this->lang->line('rack_number'); ?></span>
                                    <span class="sh-info-value" id="rack_number_v"></span>
                                </div>
                            </div>
                            <div class="row g-0 sh-row-divider">
                                <div class="col-12 sh-info-item">
                                    <span class="sh-info-label"><?php echo $this->lang->line('note'); ?></span>
                                    <span class="sh-info-value" id="medicine_note"></span>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div id="tabledata"></div>
            </div></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('close'); ?></button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade sh-modal sh-modal-accent" id="addBulkModal" tabindex="-1" aria-labelledby="addBulkModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addBulkModalLabel"><?php echo $this->lang->line('add') . " " . $this->lang->line('stock') ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formbatch" accept-charset="utf-8" method="post">
            <div class="pup-scroll-area"><div class="modal-body">
                <div class="row">
                    <div class="col-12">
                        <input type="hidden" name="pharmacy_id" id="pharm_id">
                            <div class="row g-3">
                                <div class="col-sm-3">
                                    <div class="mb-3">
                                        <label><?php echo $this->lang->line('batch') . " " . $this->lang->line('no'); ?></label>
                                        <small class="req"> *</small> 
                                        <input type="text" name="batch_no" class="form-control">
                                        <span class="text-danger"><?php echo form_error('batch_no'); ?></span>
                                    </div>
                                </div> 
                                <div class="col-sm-3">
                                    <div class="mb-3">
                                        <label><?php echo $this->lang->line('expire_date') ; ?></label>
                                        <small class="req"> *</small> 
                                        <input type="text" id="expiry" name="expiry_date" class="form-control date">
                                        <span class="text-danger"><?php echo form_error('expiry_date'); ?></span>
                                    </div>
                                </div> 
                                <div class="col-sm-3">
                                    <div class="mb-3">
                                        <label><?php echo $this->lang->line('inward') . " " . $this->lang->line('date'); ?></label>
                                        <small class="req"> *</small> 
                                        <input type="text" id="inward_date" name="inward_date" class="form-control date">
                                        <span class="text-danger"><?php echo form_error('inward_date'); ?></span>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="mb-3">
                                        <label><?php echo $this->lang->line('packing') . " " . $this->lang->line('qty'); ?></label>
                                        <small class="req"> *</small> 
                                        <input type="text" name="packing_qty" class="form-control">
                                        <span class="text-danger"><?php echo form_error('packing_qty'); ?></span>
                                    </div>
                                </div> 
                                <div class="col-sm-3">
                                    <div class="mb-3">
                                        <label><?php echo $this->lang->line('purchase_rate') . " (" . $currency_symbol . ")"; ?></label>
                                        <input type="text" name="purchase_rate_packing" class="form-control">
                                        <span class="text-danger"><?php echo form_error('purchase_rate_packing'); ?></span>
                                    </div>
                                </div> 
                                <div class="col-sm-3">
                                    <div class="mb-3">
                                        <label><?php echo $this->lang->line('quantity'); ?></label>
                                        <small class="req"> *</small> 
                                        <input type="text" name="quantity" class="form-control">
                                        <span class="text-danger"><?php echo form_error('quantity'); ?></span>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="mb-3">
                                        <label><?php echo $this->lang->line('mrp') . " (" . $currency_symbol . ")"; ?></label>
                                        <small class="req"> *</small> 
                                        <input type="text" name="mrp" class="form-control">
                                        <span class="text-danger"><?php echo form_error('mrp'); ?></span>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="mb-3">
                                        <label><?php echo $this->lang->line('sale_price') . " (" . $currency_symbol . ")"; ?></label>
                                        <small class="req"> *</small> 
                                        <input  name="sale_rate" type="text" class="form-control"/>
                                        <span class="text-danger"><?php echo form_error('sale_rate'); ?></span>
                                    </div>
                                </div> 
                                <div class="col-sm-3">
                                    <div class="mb-3">
                                        <label><?php echo $this->lang->line('batch') . " " . $this->lang->line('amount') . " (" . $currency_symbol . ")"; ?></label>
                                        <input type="text" name="amount" class="form-control">
                                        <span class="text-danger"><?php echo form_error('amount'); ?></span>
                                    </div>
                                </div> 
                            </div><!--./row-->
                    </div>
                </div>
            </div></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                <button type="submit" id="formbatchbtn" data-loading-text="<?php echo $this->lang->line("processing") ?>" class="btn btn-info"><?php echo $this->lang->line('save'); ?></button>
            </div>
           </form>
        </div>
    </div>
</div>

<div class="modal fade sh-modal sh-modal-accent" id="addBadStockModal" tabindex="-1" aria-labelledby="addBadStockModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addBadStockModalLabel"><?php echo $this->lang->line('add_bad_stock'); ?></h5>
                <button type="button" class="btn-close close_btn" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formstock" accept-charset="utf-8" method="post">
                <div class="pup-scroll-area"><div class="modal-body modal-background">
                    <input type="hidden" name="pharmacy_id" id="pharm_id">

                    <div class="sh-form-card mb-0">
                        <div class="sh-card-header">
                            <span class="sh-card-header-title"><?php echo $this->lang->line('add_bad_stock'); ?></span>
                        </div>
                        <div class="p-3">
                            <div class="row g-3">
                                <div class="col-sm-3">
                                    <label><?php echo $this->lang->line('batch_no'); ?> <small class="req">*</small></label>
                                    <select name="batch_no" onchange="getExpire(this.value)" id="batch_stock_no" class="form-control">
                                        <option value=""><?php echo $this->lang->line('select'); ?></option>
                                    </select>
                                    <span class="text-danger"><?php echo form_error('batch_no'); ?></span>
                                </div>
                                <div class="col-sm-3">
                                    <label><?php echo $this->lang->line('expiry_date'); ?> <small class="req">*</small></label>
                                    <input type="text" id="batch_expire" name="expiry_date" class="form-control expiry_date">
                                    <span class="text-danger"><?php echo form_error('expiry_date'); ?></span>
                                </div>
                                <div class="col-sm-3">
                                    <label><?php echo $this->lang->line('outward_date'); ?> <small class="req">*</small></label>
                                    <input type="text" name="inward_date" value="<?php echo date($this->customlib->getHospitalDateFormat()); ?>" class="form-control date">
                                    <span class="text-danger"><?php echo form_error('inward_date'); ?></span>
                                </div>
                                <div class="col-sm-3">
                                    <label><?php echo $this->lang->line('qty'); ?> <small class="req">*</small></label>
                                    <input type="text" name="packing_qty" class="form-control">
                                    <input type="hidden" name="pharmacy_id" id="pharmacy_stock_id">
                                    <input type="hidden" name="available_quantity" id="batch_available_qty">
                                    <input type="hidden" name="medicine_batch_id" id="medicine_batch_id">
                                    <span class="text-danger"><?php echo form_error('packing_qty'); ?></span>
                                </div>
                                <div class="col-sm-12">
                                    <label><?php echo $this->lang->line('note'); ?></label>
                                    <textarea name="note" class="form-control"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                </div></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                    <button type="submit" id="formstockbtn" data-loading-text='<i class="fa fa-circle-o-notch fa-spin"></i>' class="btn btn-info"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(function () {
        //Initialize Select2 Elements
        $('.select2').select2()
    });
	
    $(function () {
        $('#easySelectable').easySelectable();
    })    
</script>

<script type="text/javascript">
                    (function ($) {
                        //selectable html elements
                        $.fn.easySelectable = function (options) {
                            var el = $(this);
                            var options = $.extend({
                                'item': 'li',
                                'state': true,
                                onSelecting: function (el) {

                                },
                                onSelected: function (el) {

                                },
                                onUnSelected: function (el) {

                                }
                            }, options);
                            el.on('dragstart', function (event) {
                                event.preventDefault();
                            });
                            el.off('mouseover');
                            el.addClass('easySelectable');
                            if (options.state) {
                                el.find(options.item).addClass('es-selectable');
                                el.on('mousedown', options.item, function (e) {
                                    $(this).trigger('start_select');
                                    var offset = $(this).offset();
                                    var hasClass = $(this).hasClass('es-selected');
                                    var prev_el = false;
                                    el.on('mouseover', options.item, function (e) {
                                        if (prev_el == $(this).index())
                                            return true;
                                        prev_el = $(this).index();
                                        var hasClass2 = $(this).hasClass('es-selected');
                                        if (!hasClass2) {
                                            $(this).addClass('es-selected').trigger('selected');
                                            el.trigger('selected');
                                            options.onSelecting($(this));
                                            options.onSelected($(this));
                                        } else {
                                            $(this).removeClass('es-selected').trigger('unselected');
                                            el.trigger('unselected');
                                            options.onSelecting($(this))
                                            options.onUnSelected($(this));
                                        }
                                    });
                                    if (!hasClass) {
                                        $(this).addClass('es-selected').trigger('selected');
                                        el.trigger('selected');
                                        options.onSelecting($(this));
                                        options.onSelected($(this));
                                    } else {
                                        $(this).removeClass('es-selected').trigger('unselected');
                                        el.trigger('unselected');
                                        options.onSelecting($(this));
                                        options.onUnSelected($(this));
                                    }
                                    var relativeX = (e.pageX - offset.left);
                                    var relativeY = (e.pageY - offset.top);
                                });
                                $(document).on('mouseup', function () {
                                    el.off('mouseover');
                                });
                            } else {
                                el.off('mousedown');
                            }
                        };
                    })(jQuery);
</script>

<script type="text/javascript">
            $(document).ready(function (e) {
                $("#formadd").on('submit', (function (e) {
                    e.preventDefault();
                    $("#formaddbtn").btnLoading();
                    $.ajax({
                        url: '<?php echo base_url(); ?>admin/pharmacy/add',
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

            $(document).ready(function (e) {
                $("#formstock").on('submit', (function (e) {
                    e.preventDefault();
                    $("#formstockbtn").btnLoading();
                    $.ajax({
                        url: '<?php echo base_url(); ?>admin/pharmacy/addBadStock',
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
                            $("#formstockbtn").btnReset();
                        },
                        error: function () {

                        }
                    });
                }));
            });
			
            $(document).ready(function (e) {
                $("#formedit").on('submit', (function (e) {
                    e.preventDefault();
                    $("#formeditbtn").btnLoading();
                    $.ajax({
                        url: '<?php echo base_url(); ?>admin/pharmacy/update',
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
                /* picker init removed - auto-init via class + event delegation in footer.php */
            });
			
            function getRecord(id) {
                $.ajax({
                    url: '<?php echo base_url(); ?>admin/pharmacy/getDetails',
                    type: "POST",
                    data: {pharmacy_id: id},
                    dataType: 'json',
                    success: function (data) {
                        $("#id").val(data.id);
                        $("#medicines_name").val(data.medicine_name);
                        $("#medicines_category_id").val(data.medicine_category_id);
                        $("#medicine_company").val(data.medicine_company);
                        $("#medicine_composition").val(data.medicine_composition);
                        $("#medicine_group").val(data.medicine_group);
                        $("#unit").val(data.unit);
                        $("#min_level").val(data.min_level);
                        $("#reorder_level").val(data.reorder_level);
                        $("#vat").val(data.vat); 
                        $("#unit_packing").val(data.unit_packing); 
                        $("#pre_medicine_image").val(data.pre_medicine_image);
                        $("#vat_ac").val(data.vat_ac);
                        $("#rack_number").val(data.rack_number);
                        $("#edit_note").val(data.note);
                        $("#updateid").val(id);
                        shModal('viewModal').hide();
                        $(".select2").select2().select2('val', data.medicine_category_id);
                        holdModal('myModaledit');
                    },
                });
            }
			
            function viewDetail(id) {
               
                $.ajax({
                    url: '<?php echo base_url(); ?>admin/pharmacy/getDetails',
                    type: "POST",
                    data: {pharmacy_id: id},
                    dataType: 'json',
                    success: function (data) {
                        $.ajax({
                            url: '<?php echo base_url(); ?>admin/pharmacy/getMedicineBatch',
                            type: "POST",
                            data: {pharmacy_id: id},
                            success: function (data) {
                                $('#tabledata').html(data);
                            },
                        });

                        $("#medicine_image").attr('src', data.medicine_image+'<?php echo img_time(); ?>');
                        $("#medicine_names").html(data.medicine_name);
                        $("#medicine_category_ids").html(data.medicine_category);
                        $("#medicine_companys").html(data.company_name);
                        $("#medicine_compositions").html(data.medicine_composition);
                        $("#medicine_groups").html(data.group_name);
                        $("#units").html(data.unit_name);
                        $("#min_levels").html(data.min_level);
                        $("#reorder_levels").html(data.reorder_level);
                        $("#vats").html(data.vat);
                        $("#unit_packings").html(data.unit_packing);
                        $("#suppliers").html(data.supplier);
                        $("#vat_acs").html(data.vat_ac);
                        $("#rack_number_v").html(data.rack_number);
                        $("#medicine_note").html(data.note);						
                        $('#edit_delete').html("<?php if ($this->rbac->hasPrivilege('medicine', 'can_edit')) { ?><a href='#' onclick='getRecord(" + id + ")' class='btn btn-sm btn-light' data-bs-toggle='tooltip' title='<?php echo $this->lang->line('edit'); ?>'><i class='fa fa-pencil'></i></a><?php } if ($this->rbac->hasPrivilege('medicine', 'can_delete')) { ?><a onclick='delete_record(" + id + ")' href='#' class='btn btn-sm btn-light' data-bs-toggle='tooltip' title='<?php echo $this->lang->line('delete'); ?>'><i class='fa fa-trash'></i></a><?php } ?>");
                        holdModal('viewModal');
                    },
                });
            }
			
            function addBulk(id) {
                $.ajax({
                    url: '<?php echo base_url(); ?>admin/pharmacy/getPharmacy',
                    type: "POST",
                    data: {pharmacy_id: id},
                    dataType: 'json',
                    success: function (data) {
                        $("#pharm_id").val(id);
                        holdModal('addBulkModal');
                    },
                })
            }
			
            $(document).ready(function (e) {
                $("#formbatch").on('submit', (function (e) {
                    e.preventDefault();
                    $("#formbatchbtn").btnLoading();
                    $.ajax({
                        url: '<?php echo base_url(); ?>admin/pharmacy/medicineBatch',
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
                            $("#formbatchbtn").btnReset();
                        },
                        error: function () {
                            
                        }
                    });
                }));
            });
			
            function delete_record(id) {
                if (confirm('<?php echo $this->lang->line('are_you_sure'); ?>')) {
                    $.ajax({
                        url: '<?php echo base_url(); ?>admin/pharmacy/delete/' + id,
                        type: "POST",
                        data: {opdid: ''},
                        dataType: 'json',
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
                        }
                    })
                }
            }
			
            function holdModal(modalId) {
                (function(){var _el=document.getElementById(modalId);if(_el)bootstrap.Modal.getOrCreateInstance(_el,{backdrop:'static',keyboard:false}).show();})();;
            }

            function addbadstock(id) {
                $("#pharmacy_stock_id").val(id);
                getbatchnolist(id);
                holdModal('addBadStockModal');
            }
 
            function getbatchnolist(id, selectid = '') {
                var div_data = "";
                $("#batch_stock_no").html("<option value=''><?php echo $this->lang->line('select') ?></option>");
                $.ajax({
                    type: "POST",
                    url: base_url + "admin/pharmacy/getBatchNoList",
                    data: {'pharmacy_id': id},
                    dataType: 'json',
                    success: function (res) {
                        $.each(res, function (i, obj)
                        {
                            var sel = "";
                            if (obj.batch_no == selectid) {
                                sel = "selected";
                            }
                            div_data += "<option " + sel + " value='" + obj.batch_no + "'>" + obj.batch_no + "</option>";
                        });
                        $('#batch_stock_no').append(div_data);
                    }
                });
            }

            function getExpire(batch_no) {               
               if(batch_no==""){
                 $("#batch_expire").val('');
               }else{
                    $.ajax({
                        type: "POST",
                        url: base_url + "admin/pharmacy/getExpireDate",
                        data: {'batch_no': batch_no},
                        dataType: 'json',
                        success: function (data) {
                            if (data != null) {
                                $('#batch_expire').val(data.expiry);
                                $('#batch_available_qty').val(data.available_quantity);
                                $('#medicine_batch_id').val(data.id);
                            }
                        }
                    });
               }                
            }

    $(document).on('click','.delete_selected',function(){       
		var $this = $(this);     
		let obj =  [];       
		$('input:checkbox.enable_delete').each(function () {
		(this.checked ? obj.push($(this).val()) : "");
 });

 if (obj.length === 0) {  errorMsg('<?php echo $this->lang->line('no_record_selected'); ?>');  }else{
if (confirm('<?php echo $this->lang->line('are_you_sure_you_want_to_delete_this'); ?>')) {
      $.ajax({
          url: base_url+'admin/pharmacy/bulk_delete',          
          type: "POST",
          dataType: 'json',
          data:{'delete_id':obj},
          beforeSend: function() {
            $this.btnLoading();
               
          },
          success: function(res) {     
            if(res.status == 0){
                var message = "";
                $.each(res.error, function (index, value) {
                    message += value;
                });
                errorMsg(message);
            }else{
                successMsg(res.message);
            }
          $this.btnReset();
         
         if(res.status){
            table.ajax.reload();
         }
          },
          error: function(xhr) { // if error occured
             alert("Error occured.please try again");
             $this.btnReset();                
      },
      complete: function() {
            $this.btnReset();              
      }
      });
  }
 }
  
  });

    $('.close_btn').click(function(){
        $('#formstock')[0].reset();
    });
</script>

<script type="text/javascript">

	$('#myModal').on('hidden.bs.modal', function () {
		$(".filestyle").next(".dropify-clear").trigger("click");
		$(".medicine_category_id").select2("val", "");
		$('#formadd').find('input:text, input:password, input:file, textarea').val('');
		$('#formadd').find('select option:selected').removeAttr('selected');
		$('#formadd').find('input:checkbox, input:radio').removeAttr('checked');
	});

$("input[name='checkAll']").click(function () {
    $("input[name='pharmacy[]']").not(this).prop('checked', this.checked);
});
</script>
<!-- //========datatable start===== -->
<script type="text/javascript">
( function ( $ ) {
    'use strict';
    $(document).ready(function () {
        initDatatable('ajaxlist','admin/pharmacy/getpharmacyDatatable',[],[],100,[
          { 'bSortable': false, 'aTargets': [ 0,-1 ] }
       ]);

        var nameFilterTimer;
        $('#medicine_name_filter').on('keyup', function () {
            clearTimeout(nameFilterTimer);
            var filterVal = $(this).val();
            nameFilterTimer = setTimeout(function () {
                initDatatable('ajaxlist', 'admin/pharmacy/getpharmacyDatatable', { medicine_name_filter: filterVal }, [], 100, [
                    { 'bSortable': false, 'aTargets': [ 0,-1 ] }
                ]);
            }, 400);
        });
    });
} ( jQuery ) )
</script>
<!-- //========datatable end===== -->

<!-- Month/Year picker for batch_expire (addBadStockModal) — mirrors purchase.php initMonthYearPicker.
     Format must stay 'MMM/yyyy' to round-trip with Customlib::getMedicine_expire_month() which emits date('M/Y'). -->
<script type="text/javascript">
    function initMonthYearPicker(el) {
        if (el._pickerInit) return;
        el._pickerInit = new tempusDominus.TempusDominus(el, {
            allowInputToggle: true,
            container: document.body,
            localization: { format: 'MMM/yyyy', locale: 'en-US' },
            display: {
                viewMode: 'months',
                components: {
                    calendar: true, decades: true, year: true, month: true,
                    date: false, clock: false, hours: false, minutes: false, seconds: false
                },
                buttons: { today: false, clear: true, close: true },
                theme: 'light',
                icons: {
                    type: 'icons', date: 'fa fa-calendar',
                    up: 'fa fa-arrow-up', down: 'fa fa-arrow-down',
                    previous: 'fa fa-chevron-left', next: 'fa fa-chevron-right',
                    clear: 'fa fa-trash', close: 'fa fa-times'
                }
            }
        });
    }
    $(document).ready(function () {
        var el = document.getElementById('batch_expire');
        if (el) initMonthYearPicker(el);
    });
</script>
