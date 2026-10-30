<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
$genderList = $this->customlib->getGender();
?>
<div class="row">
           <?php $this->load->view('setting/sidebar'); ?>
			<?php if($this->rbac->hasPrivilege('general_setting', 'can_view')) { ?>
            <div class="col-md-10">
               <div class="card">
                    <div class="card-header ptbnull">
                    <h3 class="card-title titlefix"> <?php echo $this->lang->line('general_settings'); ?></h3>
                        <div class="d-flex gap-2 align-items-center flex-wrap float-end">
                        </div><!-- /.box-tools -->
                    </div><!-- /.box-header -->
                    <div class="">
                        <form role="form" id="schsetting_form" action="<?php echo site_url('schsettings/ajax_schedit') ?>"  method="post" enctype="multipart/form-data">
                            <div class="card-body">

                                <!-- Hospital Info -->
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="sh-form-row">
                                            <label class="sh-label"><?php echo $this->lang->line('hospital_name'); ?><small class="req"> *</small></label>
                                            <div class="sh-field">
                                                <input type="text" class="form-control" id="name" name="sch_name" value="<?php echo html_escape($settinglist->name) ?>">
                                                <input type="hidden" name="sch_id" value="<?php echo (int)$settinglist->id; ?>">
                                                <span class="text-danger"><?php echo form_error('sch_name'); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="sh-form-row">
                                            <label class="sh-label"><?php echo $this->lang->line('hospital_code'); ?></label>
                                            <div class="sh-field">
                                                <input type="text" class="form-control" id="dise_code" name="sch_dise_code" value="<?php echo html_escape($settinglist->dise_code) ?>">
                                                <span class="text-danger"><?php echo form_error('dise_code'); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="sh-form-row">
                                            <label class="sh-label"><?php echo $this->lang->line('address'); ?><small class="req"> *</small></label>
                                            <div class="sh-field">
                                                <input type="text" class="form-control" id="address" name="sch_address" value="<?php echo html_escape($settinglist->address) ?>">
                                                <span class="text-danger"><?php echo form_error('address'); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="sh-form-row">
                                            <label class="sh-label"><?php echo $this->lang->line('phone'); ?><small class="req"> *</small></label>
                                            <div class="sh-field">
                                                <input type="text" class="form-control" id="phone" name="sch_phone" value="<?php echo html_escape($settinglist->phone) ?>">
                                                <span class="text-danger"><?php echo form_error('phone'); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="sh-form-row">
                                            <label class="sh-label"><?php echo $this->lang->line('email'); ?><small class="req"> *</small></label>
                                            <div class="sh-field">
                                                <input type="text" class="form-control" id="email" name="sch_email" value="<?php echo html_escape($settinglist->email) ?>">
                                                <span class="text-danger"><?php echo form_error('email'); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Logos -->
                                <div class="settinghr"></div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="sh-form-row">
                                            <label class="sh-label"><?php echo $this->lang->line('hospital_logo'); ?><small class="req"> *</small></label>
                                            <div class="sh-field d-flex align-items-center gap-2 flex-wrap">
                                                <?php if ($settinglist->image == ""): ?>
                                                    <img src="<?php echo $this->media_storage->getImageURL('uploads/hospital_content/logo/images.png') ?>" alt="" class="sh-img-h22">
                                                <?php else: ?>
                                                    <img src="<?php echo $this->media_storage->getImageURL('uploads/hospital_content/logo/'.$settinglist->image) ?>" alt="" class="sh-img-h22">
                                                <?php endif; ?>
                                                <?php if ($this->rbac->hasPrivilege('general_setting', 'can_edit')): ?>
                                                    <a href="#schsetting" role="button" class="btn btn-primary btn-sm upload_logo" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i> Processing"><i class="fa fa-picture-o"></i> <?php echo $this->lang->line('edit_logo'); ?></a>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="sh-form-row">
                                            <label class="sh-label"><?php echo $this->lang->line('hospital_small_logo'); ?><small class="req"> *</small></label>
                                            <div class="sh-field d-flex align-items-center gap-2 flex-wrap">
                                                <?php if ($settinglist->mini_logo == ""): ?>
                                                    <img src="<?php echo $this->media_storage->getImageURL('uploads/hospital_content/logo/images.png') ?>" alt="" class="sh-img-h22">
                                                <?php else: ?>
                                                    <img src="<?php echo $this->media_storage->getImageURL('uploads/hospital_content/logo/'.$settinglist->mini_logo) ?>" alt="" class="sh-img-h22">
                                                <?php endif; ?>
                                                <?php if ($this->rbac->hasPrivilege('general_setting', 'can_edit')): ?>
                                                    <a href="#" role="button" class="btn btn-primary btn-sm upload_minilogo" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i> Processing"><i class="fa fa-picture-o"></i> <?php echo $this->lang->line('edit_small_logo'); ?></a>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Language -->
                                <div class="settinghr"></div>
                                <h4 class="session-head"><?php echo $this->lang->line('language'); ?></h4>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="sh-form-row">
                                            <label class="sh-label"><?php echo $this->lang->line('language'); ?><small class="req"> *</small></label>
                                            <div class="sh-field">
                                                <select id="language_id" name="sch_lang_id" class="form-control">
                                                    <option value=""><?php echo $this->lang->line('select') ?></option>
                                                    <?php foreach ($languagelist as $language): ?>
                                                        <option value="<?php echo (int)$language['id']; ?>" <?php if($settinglist->lang_id==$language['id']){ echo 'selected'; } ?>><?php echo html_escape($language['language']) ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                                <span class="text-danger"><?php echo form_error('language_id'); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Date / Time -->
                                <div class="settinghr"></div>
                                <h4 class="session-head"><?php echo $this->lang->line('date_time'); ?></h4>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="sh-form-row">
                                            <label class="sh-label"><?php echo $this->lang->line('date_format'); ?><small class="req"> *</small></label>
                                            <div class="sh-field">
                                                <select id="date_format" name="sch_date_format" class="form-control">
                                                    <option value=""><?php echo $this->lang->line('select'); ?></option>
                                                    <?php foreach ($dateFormatList as $key => $dateformat): ?>
                                                        <option value="<?php echo $key ?>" <?php if($settinglist->date_format==$key){ echo 'selected'; } ?>><?php echo $dateformat; ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                                <span class="text-danger"><?php echo form_error('date_format'); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="sh-form-row">
                                            <label class="sh-label"><?php echo $this->lang->line('time_zone'); ?><small class="req"> *</small></label>
                                            <div class="sh-field">
                                                <select name="sch_timezone" class="form-control">
                                                    <option value=""><?php echo $this->lang->line('select') ?></option>
                                                    <?php foreach ($timezoneList as $key => $timezone): ?>
                                                        <option value="<?php echo $key ?>" <?php if($settinglist->timezone==$key){ echo 'selected'; } ?>><?php echo $timezone ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                                <span class="text-danger"><?php echo form_error('timezone'); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Currency -->
                                <div class="settinghr"></div>
                                <h4 class="session-head"><?php echo $this->lang->line('currency') ?></h4>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="sh-form-row">
                                            <label class="sh-label"><?php echo $this->lang->line('currency'); ?><small class="req"> *</small></label>
                                            <div class="sh-field">
                                                <select id="currency" name="sch_currency" class="form-control">
                                                    <option value=""><?php echo $this->lang->line('select'); ?></option>
                                                    <?php foreach ($currencyList as $currency): ?>
                                                        <option value="<?php echo $currency ?>" <?php if($settinglist->currency==$currency){ echo 'selected'; } ?>><?php echo $currency; ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                                <span class="text-danger"><?php echo form_error('currency'); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="sh-form-row">
                                            <label class="sh-label"><?php echo $this->lang->line('currency_symbol'); ?><small class="req"> *</small></label>
                                            <div class="sh-field">
                                                <input id="currency_symbol" name="sch_currency_symbol" type="text" class="form-control" value="<?php echo html_escape($settinglist->currency_symbol) ?>">
                                                <span class="text-danger"><?php echo form_error('currency_symbol'); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="sh-form-row">
                                            <label class="sh-label"><?php echo $this->lang->line('credit_limit'); ?><small class="req"> *</small></label>
                                            <div class="sh-field">
                                                <input id="credit_limit" name="credit_limit" type="text" class="form-control" value="<?php echo html_escape($settinglist->credit_limit); ?>">
                                                <span class="text-danger"><?php echo form_error('credit_limit'); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="sh-form-row">
                                            <label class="sh-label"><?php echo $this->lang->line('time_format') ?><small class="req"> *</small></label>
                                            <div class="sh-field">
                                                <select id="time_format" name="time_format" class="form-control">
                                                    <option value=""><?php echo $this->lang->line('select'); ?></option>
                                                    <?php foreach ($timeFormat as $time_k => $time_v): ?>
                                                        <option value="<?php echo $time_k; ?>" <?php if($settinglist->time_format==$time_k){ echo 'selected'; } ?>><?php echo $time_v; ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                                <span class="text-danger"><?php echo form_error('time_format'); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Mobile App -->
                                <div class="settinghr"></div>
                                <div class="d-flex align-items-center gap-3 mb-3">
                                    <h4 class="session-head mb-0"><?php echo $this->lang->line('mobile_app'); ?> <?php if ($app_response): ?><small class="text-success">(<?php echo $this->lang->line('android_app_purchase_code_already_registered'); ?>)</small><?php endif; ?></h4>
                                    <?php if (!$app_response): ?>
                                        <button type="button" class="btn btn-info btn-sm impbtntitle3" data-bs-toggle="modal" data-bs-target="#andappModal"><?php echo $this->lang->line('register_your_android_app')?></button>
                                    <?php endif; ?>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="sh-form-row">
                                            <label class="sh-label"><?php echo $this->lang->line('mobile_app_api_url') ?></label>
                                            <div class="sh-field">
                                                <input type="text" name="mobile_api_url" id="mobile_api_url" class="form-control" value="<?php echo html_escape($settinglist->mobile_api_url); ?>">
                                                <span class="text-danger"><?php echo form_error('mobile_api_url'); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="sh-form-row">
                                            <label class="sh-label"><?php echo $this->lang->line('mobile_app_primary_color_code'); ?></label>
                                            <div class="sh-field">
                                                <input type="text" name="app_primary_color_code" id="app_primary_color_code" class="form-control" value="<?php echo html_escape($settinglist->app_primary_color_code); ?>">
                                                <span class="text-danger"><?php echo form_error('app_primary_color_code'); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="sh-form-row">
                                            <label class="sh-label"><?php echo $this->lang->line('mobile_app_secondary_color_code'); ?></label>
                                            <div class="sh-field">
                                                <input type="text" name="app_secondary_color_code" id="app_secondary_color_code" class="form-control" value="<?php echo html_escape($settinglist->app_secondary_color_code); ?>">
                                                <span class="text-danger"><?php echo form_error('app_secondary_color_code'); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="sh-form-row">
                                            <label class="sh-label"><?php echo $this->lang->line('mobile_app_logo'); ?></label>
                                            <div class="sh-field d-flex align-items-center gap-2 flex-wrap">
                                                <?php if ($settinglist->app_logo == ""): ?>
                                                    <img src="<?php echo $this->media_storage->getImageURL('uploads/hospital_content/logo/images.png') ?>" alt="" class="sh-img-h22">
                                                <?php else: ?>
                                                    <img src="<?php echo $this->media_storage->getImageURL('uploads/hospital_content/logo/'.$settinglist->app_logo) ?>" alt="" class="sh-img-h22">
                                                <?php endif; ?>
                                                <?php if ($this->rbac->hasPrivilege('general_setting', 'can_edit')): ?>
                                                    <a href="#" role="button" class="btn btn-primary btn-sm upload_applogo" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i> Processing"><i class="fa fa-picture-o"></i> <?php echo $this->lang->line('edit_app_logo'); ?></a>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Miscellaneous -->
                                <div class="settinghr"></div>
                                <h4 class="session-head"><?php echo $this->lang->line('miscellaneous'); ?></h4>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="sh-form-row">
                                            <label class="sh-label"><?php echo $this->lang->line('doctor_restriction_mode'); ?></label>
                                            <div class="sh-field pt-1">
                                                <label class="radio-inline"><input type="radio" name="doctor_restriction_mode" value="disabled" <?php if($settinglist->doctor_restriction=='disabled'){ echo 'checked'; } ?>> <?php echo $this->lang->line('disabled'); ?></label>
                                                <label class="radio-inline"><input type="radio" name="doctor_restriction_mode" value="enabled" <?php if($settinglist->doctor_restriction=='enabled'){ echo 'checked'; } ?>> <?php echo $this->lang->line('enabled'); ?></label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="sh-form-row">
                                            <label class="sh-label"><?php echo $this->lang->line('superadmin_visibility'); ?></label>
                                            <div class="sh-field pt-1">
                                                <label class="radio-inline"><input type="radio" name="superadmin_restriction_mode" value="disabled" <?php if($settinglist->superadmin_restriction=='disabled'){ echo 'checked'; } ?>> <?php echo $this->lang->line('disabled'); ?></label>
                                                <label class="radio-inline"><input type="radio" name="superadmin_restriction_mode" value="enabled" <?php if($settinglist->superadmin_restriction=='enabled'){ echo 'checked'; } ?>> <?php echo $this->lang->line('enabled'); ?></label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="sh-form-row">
                                            <label class="sh-label"><?php echo $this->lang->line('patient_panel'); ?></label>
                                            <div class="sh-field pt-1">
                                                <label class="radio-inline"><input type="radio" name="patient_panel" value="disabled" <?php if($settinglist->patient_panel=='disabled'){ echo 'checked'; } ?>> <?php echo $this->lang->line('disabled'); ?></label>
                                                <label class="radio-inline"><input type="radio" name="patient_panel" value="enabled" <?php if($settinglist->patient_panel=='enabled'){ echo 'checked'; } ?>> <?php echo $this->lang->line('enabled'); ?></label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="sh-form-row">
                                            <label class="sh-label"><?php echo $this->lang->line('scan_type'); ?></label>
                                            <div class="sh-field pt-1">
                                                <label class="radio-inline"><input type="radio" name="scan_code_type" value="barcode" <?php if($settinglist->scan_code_type=='barcode'){ echo 'checked'; } ?>> <?php echo $this->lang->line('barcode'); ?></label>
                                                <label class="radio-inline"><input type="radio" name="scan_code_type" value="qrcode" <?php if($settinglist->scan_code_type=='qrcode'){ echo 'checked'; } ?>> <?php echo $this->lang->line('qrcode'); ?></label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="sh-form-row">
                                            <label class="sh-label"><?= $this->lang->line('patient_delete_account') ?></label>
                                            <div class="sh-field pt-1">
                                                <label class="radio-inline"><input type="radio" name="patient_delete_account" value="disabled" <?php if($settinglist->patient_delete_account=='disabled' || $settinglist->patient_delete_account==''){ echo 'checked'; } ?>> <?php echo $this->lang->line('disabled'); ?></label>
                                                <label class="radio-inline"><input type="radio" name="patient_delete_account" value="enabled" <?php if($settinglist->patient_delete_account=='enabled'){ echo 'checked'; } ?>> <?php echo $this->lang->line('enabled'); ?></label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="sh-form-row">
                                            <label class="sh-label"><?= $this->lang->line('notification_poll_interval') ?></label>
                                            <div class="sh-field">
                                                <select name="notification_poll_interval" class="form-control">
                                                    <?php
                                                    $poll_options = array(30 => '30 Seconds', 60 => '1 Minute', 120 => '2 Minutes', 300 => '5 Minutes');
                                                    $current_poll = isset($settinglist->notification_poll_interval) ? (int)$settinglist->notification_poll_interval : 60;
                                                    foreach ($poll_options as $val => $label) {
                                                        echo '<option value="'.$val.'"'.($current_poll == $val ? ' selected' : '').'>'.$label.'</option>';
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- File Upload Path -->
                                <div class="settinghr"></div>
                                <h4 class="session-head"><?php echo $this->lang->line('file_upload_path'); ?></h4>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="sh-form-row">
                                            <label class="sh-label"><?php echo $this->lang->line('base_url'); ?><small class="req"> *</small></label>
                                            <div class="sh-field">
                                                <input type="text" class="form-control" id="base_url" name="base_url" value="<?php echo html_escape($settinglist->base_url); ?>">
                                                <span class="text-danger"><?php echo form_error('base_url'); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="sh-form-row">
                                            <label class="sh-label"><?php echo $this->lang->line('file_upload_path'); ?><small class="req"> *</small></label>
                                            <div class="sh-field">
                                                <input type="text" class="form-control" id="folder_path" name="folder_path" value="<?php echo html_escape($settinglist->folder_path); ?>">
                                                <span class="text-danger"><?php echo form_error('folder_path'); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Theme Studio has its own dedicated page (admin/themestudio) + sidebar link,
                                     so the visible card was removed here 2026-05-22 to stop duplicating it.
                                     This hidden `theme` input MUST stay: Schsettings::ajax_schedit() still
                                     validates `theme` as required|in_list[a,b,c], so the General Settings
                                     save breaks without it. -->
                                <input type="hidden" name="theme" value="<?php echo htmlspecialchars(in_array($settinglist->theme, ['a','b','c']) ? $settinglist->theme : 'a'); ?>">

                            </div><!-- /.box-body -->
                            <div class="card-footer">
                                <?php
                                if ($this->rbac->hasPrivilege('general_setting', 'can_edit')) {
                                    ?>
                                    <button type="button" class="btn btn-primary submit_schsetting float-end" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i> Processing"><i class="fa fa-check-circle"></i>  <?php echo $this->lang->line('save'); ?></button>
                                    <?php
                                }
                                ?>
                            </div>
                        </form>
                    </div><!-- /.box-body -->
                </div>
            </div><!--./col-md-9-->
            <?php } ?>
        </div>

<div class="modal fade sh-modal sh-modal-accent" id="modal-uploadfile" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myModalLabel"><?php echo $this->lang->line('edit_logo'); ?> <span id="imagesize">(182px X 18px)</span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="ajaxlogo" method="post" action="<?php echo site_url('schsettings/ajax_editlogo') ?>" enctype="multipart/form-data">
                <div class="pup-scroll-area">
                    <div class="modal-body">
                        <div class="sh-form-card">
                            <div class="sh-card-header">
                                <span class="sh-card-header-title"><?php echo $this->lang->line('details'); ?></span>
                            </div>
                            <div class="p-2">
                                <input type="file" class="filestyle" id="file" name="file" data-allowed-file-extensions="jpg jpeg png gif svg webp" data-height="150"/>
                                <input value="<?php echo (int)$settinglist->id ?>" type="hidden" name="id" id="id"/>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                    <button type="button" class="btn btn-info submit_logo" disabled><i class="fa fa-upload"></i> <?php echo $this->lang->line('upload'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="andappModal" class="modal fade sh-modal sh-modal-accent" tabindex="-1" aria-labelledby="andappModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="andappModalLabel"><?php echo $this->lang->line('register_your_android_app_purchase_code'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?php echo site_url('admin/admin/updateandappCode') ?>" method="POST" id="andapp_code">
                <div class="pup-scroll-area">
                    <div class="modal-body andapp_modal-body">
                        <div class="error_message"></div>
                        <div class="sh-form-card">
                            <div class="sh-card-header">
                                <span class="sh-card-header-title"><?php echo $this->lang->line('details'); ?></span>
                            </div>
                            <div class="p-2">
                                <div class="row g-3">
                                    <div class="col-sm-12">
                                        <label class="form-label form-label-sm ainline" for="input-app-envato_market_purchase_code"><span><?php echo $this->lang->line('envato_market_purchase_code_for_smart_hospital_android_app'); ?> ( <a target="_blank" href="https://help.market.envato.com/hc/en-us/articles/202822600-Where-Is-My-Purchase-Code-"> <?php echo $this->lang->line('how_to_find_it'); ?></a> )</span></label>
                                        <input type="text" class="form-control form-control-sm" id="input-app-envato_market_purchase_code" name="app-envato_market_purchase_code">
                                        <div class="input-error text text-danger"></div>
                                    </div>
                                    <div class="col-sm-12">
                                        <label class="form-label form-label-sm" for="input-app-email"><?php echo $this->lang->line('your_email_registered_with_envato'); ?></label>
                                        <input type="text" class="form-control form-control-sm" id="input-app-email" name="app-email">
                                        <div class="input-error text text-danger"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                    <button type="submit" class="btn btn-info" data-loading-text="<i class='fa fa-spinner fa-spin '></i> Saving..."><?php echo $this->lang->line('save'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<script type="text/javascript">
    var base_url = '<?php echo base_url(); ?>';
    $('.upload_logo').on('click', function (e) {
        e.preventDefault();
        $("#myModalLabel").html('<?php echo $this->lang->line('edit_logo') ?> (182px X 18px)');
        $("#imagesize").html(' (182px X 18px)');
        var $this = $(this);
        $("#ajaxlogo").attr('action', '<?php echo site_url('schsettings/ajax_editlogo') ?>');
        $this.btnLoading();
        bootstrap.Modal.getOrCreateInstance(document.getElementById('modal-uploadfile'),{backdrop:'static',keyboard:false}).show();
    });

     var base_url = '<?php echo base_url(); ?>';
    $('.upload_applogo').on('click', function (e) {
        e.preventDefault();
        $("#myModalLabel").html('<?php echo $this->lang->line('edit_app_logo'); ?> (152px X 18px)');
        $("#imagesize").html(' (152px X 18px)');
        var $this = $(this);
        $("#ajaxlogo").attr('action', '<?php echo site_url('schsettings/ajax_applogo') ?>');
        bootstrap.Modal.getOrCreateInstance(document.getElementById('modal-uploadfile'),{backdrop:'static',keyboard:false}).show();
    });

    $('.upload_minilogo').on('click', function (e) {
        e.preventDefault();
        $("#myModalLabel").html('<?php echo $this->lang->line('edit_small_logo'); ?> (41px X 25px)');
        $("#imagesize").html(' (41px X 25px)');
        var $this = $(this);
        $("#ajaxlogo").attr('action', '<?php echo site_url('schsettings/ajax_minilogo') ?>');
        bootstrap.Modal.getOrCreateInstance(document.getElementById('modal-uploadfile'),{backdrop:'static',keyboard:false}).show();
    });

// init/clear dropify once the modal is fully visible (dropify needs a painted element to size correctly)
    $('#modal-uploadfile').on('shown.bs.modal', function () {
        $('.upload_logo, .upload_applogo, .upload_minilogo').btnReset();
        var $file = $('#ajaxlogo .filestyle');
        if (!$file.closest('.dropify-wrapper').length) {
            $file.dropify();
        } else {
            $('#ajaxlogo .dropify-clear').trigger('click'); // reset any prior selection between logo types
        }
        $('.submit_logo').prop('disabled', true);
    });

    /* Dead handlers: #modal-uploadappfile and #modal-minilogo don't exist in the DOM —
       all three logo flows (.upload_logo/.upload_applogo/.upload_minilogo) reuse the
       single #modal-uploadfile modal, whose shown.bs.modal handler above already fires.
    $('#modal-uploadappfile').on('shown.bs.modal', function () {
        $('.upload_applogo').btnReset();
    });
    $('#modal-minilogo').on('shown.bs.modal', function () {
        $('.upload_minilogo').btnReset();
    }); */

    $('.edit_setting').on('click', function (e) {
        e.preventDefault();
        var $this = $(this);
        $this.btnLoading();
        $.ajax({
            url: base_url + '/schsettings/getSchsetting',
            type: 'POST',
            data: {},
            dataType: "json",
            success: function (result) {
                $('input[name="sch_id"]').val(result.id);
                $('input[name="sch_name"]').val(result.name);
                $('input[name="sch_dise_code"]').val(result.dise_code);
                $('input[name="sch_phone"]').val(result.phone);
                $('input[name="credit_limit"]').val(result.credit_limit);
                $('#opd_record_month').val(result.opd_record_month);
                $('input[name="sch_email"]').val(result.email);
                $('input[name="fee_due_days"]').val(result.fee_due_days);
                $('input[name="sch_currency_symbol"]').val(result.currency_symbol);
                $('input[name="sch_mobile_api_url"]').val(result.mobile_api_url);
                $('input[name="sch_app_primary_color_code"]').val(result.app_primary_color_code);
                $('input[name="sch_app_secondary_color_code"]').val(result.app_secondary_color_code);
                $('textarea[name="sch_address"]').text(result.address);
                $("input[name=sch_is_rtl]").filter(function(){ return this.value == result.is_rtl; }).prop('checked', true);
                $("input[name=doctor_restriction_mode]").filter(function(){ return this.value == result.doctor_restriction; }).prop('checked', true);
                $("input[name=superadmin_restriction_mode]").filter(function(){ return this.value == result.superadmin_restriction; }).prop('checked', true);
                $("input[name=theme]").filter(function(){ return this.value === result.theme; }).prop('checked', true);
                $('select[name="sch_session_id"]').val(result.session_id);
                $('select[name="sch_start_month"]').val(result.start_month);
                $('select[name="sch_lang_id"]').val(result.lang_id);
                $('select[name="sch_timezone"]').val(result.timezone);
                $('select[name="sch_date_format"]').val(result.date_format);
                $('select[name="time_format"]').val(result.time_format);
                $('select[name="sch_currency"]').val(result.currency);

                bootstrap.Modal.getOrCreateInstance(document.getElementById('schsetting'),{backdrop:'static',keyboard:false}).show();
            },
            error: function () {
                console.log("error on form");
            }

        }).done(function () {
            $this.btnReset();
        });
    });

    $(document).on('click', '.submit_schsetting', function (e) {
        var $this = $(this);
        $this.btnLoading();
        $.ajax({
            url: '<?php echo site_url("schsettings/ajax_schedit") ?>',
            type: 'post',
            data: $('#schsetting_form').serialize(),
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

                $this.btnReset();
            }
        });
    });
</script>
<script type="text/javascript">
    // Enable the Upload button only once dropify reports a chosen file
    // (dropify's clear also fires a change with no files, which disables it again).
    $(document).on('change', '#ajaxlogo .filestyle', function () {
        $('.submit_logo').prop('disabled', !(this.files && this.files.length));
    });

    // AJAX upload to the form's current action (set per logo type by the trigger handlers above).
    $(document).on('click', '.submit_logo', function (e) {
        e.preventDefault();
        var $btn = $(this);
        var form = document.getElementById('ajaxlogo');
        var fileInput = form.querySelector('input[type="file"]');
        if (!fileInput.files || !fileInput.files.length) {
            return;
        }
        $btn.btnLoading();
        $.ajax({
            url: $(form).attr('action'),
            type: 'POST',
            data: new FormData(form), // captures file (name="file") + hidden id
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function (data) {
                if (data.success) {
                    toastr.success(data.message);
                    setTimeout(function () { window.location.reload(1); }, 1500);
                } else {
                    $btn.btnReset();
                    var message = '';
                    $.each(data.error, function (index, value) { message += value; });
                    toastr.error(message);
                }
            },
            error: function () {
                $btn.btnReset();
                toastr.error('Error. Please, try again!');
            }
        });
    });
</script>
