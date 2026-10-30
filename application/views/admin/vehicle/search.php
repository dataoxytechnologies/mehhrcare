<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
$genderList = $this->customlib->getGender();
?>
<div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title titlefix"><?php echo $this->lang->line('ambulance_list'); ?></h3>
                        <div class="d-flex gap-2 align-items-center flex-wrap float-end">
                            <?php if($this->rbac->hasPrivilege('ambulance', 'can_add')) { ?>  
                                <a data-bs-toggle="modal" onclick="holdModal('myModal')" class="btn btn-primary btn-sm addambulance"><i class="fa fa-plus"></i> <?php echo $this->lang->line('add_ambulance'); ?></a>
                            <?php } ?>                            
                        </div>
                    </div><!-- /.card-header -->
                    <div class="card-body">
                        <div class="table-responsive">
                            <div class="download_label"><?php echo $this->lang->line('ambulance_list'); ?></div>
                                <table class="table table-striped table-bordered table-hover ajaxlist" cellspacing="0" width="100%" data-export-title="<?php echo $this->lang->line('ambulance_list'); ?>">
                                    <thead>
                                        <tr>
                                            <th><?php echo $this->lang->line('vehicle_number'); ?></th>
                                            <th><?php echo $this->lang->line('vehicle_model'); ?></th>
                                            <th><?php echo $this->lang->line('year_made'); ?></th>
                                            <th><?php echo $this->lang->line('driver_name'); ?></th>
                                            <th><?php echo $this->lang->line('driver_license'); ?></th>
                                            <th><?php echo $this->lang->line('driver_contact'); ?></th>
                                            <th><?php echo $this->lang->line('note'); ?></th>
                                            <th class="text-end"><?php echo $this->lang->line('vehicle_type'); ?></th>
                                            <th class="text-end noExport"><?php echo $this->lang->line('action'); ?></th>
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
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myModalLabel"><?php echo $this->lang->line('add_ambulance'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formadd" method="post" accept-charset="utf-8">
                <div class="pup-scroll-area">
                    <div class="modal-body p-2">
                        <div class="sh-form-card">
                            <div class="sh-card-header">
                                <span class="sh-card-header-title"><?php echo $this->lang->line('ambulance_details'); ?></span>
                            </div>
                            <div class="p-2">
                                <div class="row">
                                    <div class="col-sm-4">
                                        <div class="mb-3">
                                            <label><?php echo $this->lang->line('vehicle_number'); ?></label><small class="req"> *</small>
                                            <input name="vehicle_no" placeholder="" type="text" class="form-control" />
                                            <span class="text-danger"><?php echo form_error('vehicle_no'); ?></span>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="mb-3">
                                            <label><?php echo $this->lang->line('vehicle_model'); ?></label><small class="req"> *</small>
                                            <input name="vehicle_model" placeholder="" type="text" class="form-control" />
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="mb-3">
                                            <label><?php echo $this->lang->line('year_made'); ?></label>
                                            <input name="manufacture_year" placeholder="" type="text" class="form-control" value="<?php echo set_value('manufacture_year'); ?>" />
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="mb-3">
                                            <label><?php echo $this->lang->line('driver_name'); ?></label>
                                            <input name="driver_name" placeholder="" type="text" class="form-control" value="<?php echo set_value('driver_name'); ?>" />
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="mb-3">
                                            <label><?php echo $this->lang->line('driver_license'); ?></label>
                                            <input name="driver_licence" placeholder="" type="text" class="form-control" />
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="mb-3">
                                            <label><?php echo $this->lang->line('driver_contact'); ?></label>
                                            <input name="driver_contact" placeholder="" type="text" class="form-control" />
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="mb-3">
                                            <label><?php echo $this->lang->line('vehicle_type'); ?></label><small class="req"> *</small>
                                            <select name="vehicle_type" class="form-control">
                                                <option value=""><?php echo $this->lang->line('select'); ?></option>
                                                <option value="<?php echo $this->lang->line("owned") ?>"><?php echo $this->lang->line("owned") ?></option>
                                                <option value="<?php echo $this->lang->line("contractual") ?>"><?php echo $this->lang->line("contractual") ?></option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-8">
                                        <div class="mb-3">
                                            <label><?php echo $this->lang->line('note'); ?></label>
                                            <textarea class="form-control" name="note" placeholder=""><?php echo set_value('note'); ?></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                    <button type="submit" data-loading-text="<?php echo $this->lang->line('processing'); ?>" id="formaddbtn" class="btn btn-info"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                </div>
            </form>
        </div>    
    </div>
</div>

<!-- dd -->
<div class="modal fade sh-modal sh-modal-accent" id="myModaledit" tabindex="-1" aria-labelledby="myModaleditLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myModaleditLabel"><?php echo $this->lang->line('edit_ambulance'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formedit" accept-charset="utf-8">
                <div class="pup-scroll-area">
                    <div class="modal-body p-2">
                        <div class="sh-form-card">
                            <div class="sh-card-header">
                                <span class="sh-card-header-title"><?php echo $this->lang->line('ambulance_details'); ?></span>
                            </div>
                            <div class="p-2">
                                <div class="row">
                                    <input type="hidden" name="id" id="ids" value="<?php echo set_value('id'); ?>">
                                    <div class="col-sm-4">
                                        <div class="mb-3">
                                            <label><?php echo $this->lang->line('vehicle_number'); ?></label><small class="req"> *</small>
                                            <input id="vehicle_nos" name="vehicle_no" type="text" class="form-control" value="<?php echo set_value('vehicle_no'); ?>" />
                                            <span class="text-danger"><?php echo form_error('vehicle_no'); ?></span>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="mb-3">
                                            <label><?php echo $this->lang->line('vehicle_model'); ?></label><small class="req"> *</small>
                                            <input id="vehicle_models" name="vehicle_model" type="text" class="form-control" value="<?php echo set_value('vehicle_model'); ?>" />
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="mb-3">
                                            <label><?php echo $this->lang->line('year_made'); ?></label>
                                            <input id="manufacture_years" name="manufacture_year" type="text" class="form-control" value="<?php echo set_value('manufacture_year'); ?>" />
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="mb-3">
                                            <label><?php echo $this->lang->line('driver_name'); ?></label>
                                            <input id="driver_names" name="driver_name" type="text" class="form-control" value="<?php echo set_value('driver_name'); ?>" />
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="mb-3">
                                            <label><?php echo $this->lang->line('driver_license'); ?></label>
                                            <input id="driver_licences" name="driver_licence" type="text" class="form-control" value="<?php echo set_value('driver_licence'); ?>" />
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="mb-3">
                                            <label><?php echo $this->lang->line('driver_contact'); ?></label>
                                            <input id="driver_contacts" name="driver_contact" type="text" class="form-control" value="<?php echo set_value('driver_contact'); ?>" />
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="mb-3">
                                            <label><?php echo $this->lang->line('vehicle_type'); ?></label><small class="req"> *</small>
                                            <select name="vehicle_type" id="vehicle_type" class="form-control">
                                                <option value=""><?php echo $this->lang->line('select'); ?></option>
                                                <option value="<?php echo $this->lang->line("owned") ?>"><?php echo $this->lang->line("owned") ?></option>
                                                <option value="<?php echo $this->lang->line("contractual") ?>"><?php echo $this->lang->line("contractual") ?></option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-8">
                                        <div class="mb-3">
                                            <label><?php echo $this->lang->line('note'); ?></label>
                                            <textarea class="form-control" id="note" name="note" placeholder=""><?php echo set_value('note'); ?></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                    <button type="submit" id="formeditbtn" data-loading-text="<?php echo $this->lang->line('processing'); ?>" class="btn btn-info"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                </div>
            </form> 
        </div>
    </div>    
</div>

<script type="text/javascript">
    $(document).ready(function () {
        $('.detail_popover').popover({
            placement: 'right',
            trigger: 'hover',
            container: 'body',
            html: true,
            content: function () {
                return $(this).closest('td').find('.fee_detail_popover').html();
            }
        });
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
                    $("#formaddbtn").btnLoading();
                    e.preventDefault();
                    $.ajax({
                        url: '<?php echo base_url(); ?>admin/vehicle/add',
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
                                shModal('myModal').hide();
                                table.ajax.reload();
                            }
                            $("#formaddbtn").btnReset();
                        },
                        error: function () {
                            
                        }
                    });
                }));
            });
			
            $(document).ready(function (e) {
                $("#formedit").on('submit', (function (e) {
                    $("#formeditbtn").btnLoading();
                    e.preventDefault();
                    $.ajax({
                        url: '<?php echo base_url(); ?>admin/vehicle/update',
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
                                shModal('myModaledit').hide();
                                table.ajax.reload();
                            }
                            $("#formeditbtn").btnReset();
                        },
                        error: function () {
                            
                        }
                    });
                }));
            });
			
            function getRecord(id) {
                bootstrap.Modal.getOrCreateInstance(document.getElementById('myModaledit'),{backdrop:'static',keyboard:false}).show();
                $.ajax({
                    url: '<?php echo base_url(); ?>admin/vehicle/edit',
                    type: "POST",
                    data: {id: id},
                    dataType: 'json',
                    success: function (data) {
                        $("#ids").val(data.id);
                        $("#vehicle_nos").val(data.vehicle_no);
                        $("#vehicle_models").val(data.vehicle_model);
                        $("#manufacture_years").val(data.manufacture_year);
                        $("#driver_names").val(data.driver_name);
                        $("#driver_licences").val(data.driver_licence);
                        $("#driver_contacts").val(data.driver_contact);
                        $("#vehicle_type").val(data.vehicle_type);
                        $("#note").text(data.note);
                    },
                });
            }
			
            function holdModal(modalId) {
                (function(){var _el=document.getElementById(modalId);if(_el)bootstrap.Modal.getOrCreateInstance(_el,{backdrop:'static',keyboard:false}).show();})();;
            }

$(".addambulance").click(function(){
	$('#formadd').trigger("reset");
});

function delete_bill(id) {
    var url = 'admin/vehicle/delete/' + id;
    var msg = "<?php echo $this->lang->line('delete_message'); ?>";
    delete_recordById(url, msg);
    window.location.reload(true);
}
</script>
<!-- //========datatable start===== -->
<script type="text/javascript">
( function ( $ ) {
    'use strict';
    $(document).ready(function () {
        window.table = initDatatable('ajaxlist','admin/vehicle/getvehicleDatatable',[],[],100);
    });
} ( jQuery ) )
</script>
<!-- //========datatable end===== -->