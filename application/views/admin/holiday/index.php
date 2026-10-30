<div class="card">
   <div class="card-header ptbnull">
    <h3 class="card-title titlefix"><?php echo $this->lang->line('annual_calendar'); ?></h3>
		<div class="d-flex gap-2 align-items-center flex-wrap float-end">
			<?php if ($this->rbac->hasPrivilege('annual_calendar', 'can_add')) {?>
			<button type="button" onclick="add_holiday()" class="btn btn-sm btn-primary" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('add'); ?>"><i class="fa fa-plus"></i> <?php echo $this->lang->line('add'); ?></button>
			<?php } ?>
      </div>
   </div>

   <!-- Filter section -->
   <div class="card-body border-bottom">
    <form class="assign_teacher_form" action="<?php echo base_url(); ?>admin/holiday" method="post" enctype="multipart/form-data">
      <?php if ($this->session->flashdata('msg')) { ?>
      <?php echo $this->session->flashdata('msg');
             $this->session->unset_userdata('msg');
      ?>
      <?php } ?>
      <?php echo $this->customlib->getCSRF(); ?>
      <div class="row g-3 align-items-end">
        <div class="col-md-4 col-lg-3">
          <label class="form-label"><?php echo $this->lang->line('type'); ?><small class="req"> *</small></label>
          <select autofocus="" id="search_holiday_type" name="search_holiday_type" class="form-select">
            <option value=""><?php echo $this->lang->line('select'); ?></option>
            <option value="1" <?php if($search_holiday_type == 1){ echo "selected";} ?>><?php echo $this->lang->line('holiday'); ?></option>
            <option value="2" <?php if($search_holiday_type == 2){ echo "selected";} ?>><?php echo $this->lang->line('activity'); ?></option>
            <option value="3" <?php if($search_holiday_type == 3){ echo "selected";} ?>><?php echo $this->lang->line('vacation'); ?></option>
          </select>
          <span class="text-danger"><?php echo form_error('search_holiday_type'); ?></span>
        </div>
        <div class="col-auto">
          <button type="submit" id="search_filter" name="search" value="search_filter" class="btn btn-primary d-inline-flex align-items-center gap-1 py-2"><i class="fa fa-search"></i> <?php echo $this->lang->line('search'); ?></button>
        </div>
      </div>
    </form>
   </div>

   <!-- Holiday list section -->
   <div class="card-body table-responsive overflow-visible">
    <div class="download_label"><?php echo $this->lang->line('holiday_list'); ?></div>
    <table class="table table-hover table-striped table-bordered example">
     <thead>
      <tr>
       <th width="15%"><?php echo $this->lang->line('date'); ?></th>
       <th><?php echo $this->lang->line('type'); ?></th>
       <th><?php echo $this->lang->line('description'); ?></th>
       <th><?php echo $this->lang->line('created_by'); ?></th>
       <th width="5%"><?php echo $this->lang->line('front_site'); ?></th>
       <th class="text-end noExport" width="10%"><?php echo $this->lang->line('action'); ?></th>
      </tr>
     </thead>
     <tbody>
     <?php foreach ($holidaylist as $value) { ?>
      <tr>
       <td>
        <?php if($value['holiday_type']==3){
          echo date($this->customlib->YYYYMMDDTodateFormat($value['from_date'])) . ' '. $this->lang->line('to') .' '. date($this->customlib->YYYYMMDDTodateFormat($value['to_date']));
        }else{
          echo date($this->customlib->YYYYMMDDTodateFormat($value['from_date']));
        } ?>
       </td>
       <td><?php
        if($value['holiday_type']==1){
          echo $this->lang->line('holiday');
        }else if($value['holiday_type']==2){
          echo $this->lang->line('activity');
        }else if($value['holiday_type']==3){
          echo $this->lang->line('vacation');
        } ?></td>
       <td><?php echo html_escape($value['description']); ?></td>
       <td width="10%">
        <?php
          if($superadmin_restriction == 'disabled' && $value['role_id'] == 7){
            echo '';
          }else{
            echo html_escape($value['name'])." ".html_escape($value['surname'])." (".html_escape($value['employee_id']).")";
          }
        ?>
       </td>
       <td><?php
        if($value['front_site']==1){
          echo $this->lang->line('yes');
        }else{
          echo $this->lang->line('no');
        } ?></td>
       <td class="text-end noExport">
         <div class="d-inline-flex gap-1">
           <?php if ($this->rbac->hasPrivilege('annual_calendar', 'can_edit')) { ?>
             <a onclick="get('<?php echo (int)$value['id']; ?>')" class="btn btn-sm btn-outline-primary" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('edit') ?>"><i class="fa fa-pencil"></i></a>
           <?php } if ($this->rbac->hasPrivilege('annual_calendar', 'can_delete')) { ?>
             <a onclick="delete_holiday('<?php echo (int)$value['id']; ?>');" class="btn btn-sm btn-outline-danger" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('delete') ?>"><i class="fa fa-trash"></i></a>
           <?php } ?>
         </div>
       </td>
      </tr>
     <?php } ?>
     </tbody>
    </table>
   </div>
</div>

<div class="modal fade sh-modal sh-modal-accent" id="holiday_modal" tabindex="-1" aria-labelledby="holidayModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fa fa-calendar me-2 sh-icon-soft"></i><span id="title"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" onclick="refresh()" aria-label="Close"></button>
            </div>
            <form role="form" id="add_holiday" method="post" enctype="multipart/form-data" action="">
                <input type="hidden" name="id" id="id">
                <div class="pup-scroll-area">
                    <div class="modal-body">
                        <div class="sh-form-card">
                            <div class="sh-card-header">
                                <span class="sh-card-header-title"><?php echo $this->lang->line('details'); ?></span>
                            </div>
                            <div class="p-2">
                                <div class="row g-3">
                                    <div class="col-12">
                                        <label class="form-label form-label-sm"><?php echo $this->lang->line('type'); ?> <small class="req">*</small></label>
                                        <ul class="stepradiolist stepradiolist-tiles row gy-2">
                                            <li class="col-sm-4"><label><input type="radio" id="radio_1" name="holiday_type" value="1" onchange="showDate(this.value)"><div class="stepimage"><?php echo $this->lang->line('holiday'); ?></div></label></li>
                                            <li class="col-sm-4"><label><input type="radio" id="radio_2" name="holiday_type" value="2" onchange="showDate(this.value)"><div class="stepimage"><?php echo $this->lang->line('activity'); ?></div></label></li>
                                            <li class="col-sm-4"><label><input type="radio" id="radio_3" name="holiday_type" value="3" onchange="showDate(this.value)"><div class="stepimage"><?php echo $this->lang->line('vacation'); ?></div></label></li>
                                        </ul>
                                    </div>
                                    <div class="col-md-6" id="date_one">
                                        <label id="date_text" class="form-label form-label-sm"><?php echo $this->lang->line('from_date'); ?> <small class="req">*</small></label>
                                        <input id="from_date" name="from_date" type="text" class="form-control form-control-sm date" value="">
                                    </div>
                                    <div class="col-md-6" id="date_two">
                                        <label class="form-label form-label-sm"><?php echo $this->lang->line('to_date'); ?> <small class="req">*</small></label>
                                        <input id="to_date" name="to_date" type="text" class="form-control form-control-sm date" value="">
                                    </div>
                                    <div class="col-12">
                                        <label for="description" class="form-label form-label-sm"><?php echo $this->lang->line('description'); ?> <small class="req">*</small></label>
                                        <textarea rows="4" id="description" name="description" class="form-control form-control-sm"></textarea>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label form-label-sm d-block"><?php echo $this->lang->line('front_site'); ?></label>
                                        <div class="form-check form-switch d-inline-flex m-0">
                                            <input id="front_site" name="front_site" type="checkbox" role="switch" class="form-check-input chk" value="checked">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal" onclick="refresh()"><?php echo $this->lang->line('cancel'); ?></button>
                    <button type="submit" class="btn btn-info" data-loading-text="<i class='fa fa-spinner fa-spin '></i> Please wait"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<script type="text/javascript">

 $("#add_holiday").on('submit', (function (e) {
      e.preventDefault();
      var $this = $(this).find("button[type=submit]:focus");
      $.ajax({
          url: "<?php echo site_url("admin/holiday/add") ?>",
          type: "POST",
          data: new FormData(this),
          dataType: 'json',
          contentType: false,
          cache: false,
          processData: false,
          beforeSend: function () {
              $this.btnLoading();
          },
          success: function (res)
          {
             if (res.status == "fail") {
                  var message = "";
                  $.each(res.error, function (index, value) {
                      message += value;
                  });
                  errorMsg(message);

              } else {
                  successMsg(res.message);
                  window.location.reload(true);
              }
          },
          error: function (xhr) {
              alert("Error occured.please try again");
              $this.btnReset();
          },
          complete: function () {
              $this.btnReset();
          }
      });
 }));

 function showDate(val){
     $("#from_date").val('');
     $("#to_date").val('');
     if(val==""){
         $("#date_one").hide();
         $("#date_two").hide();
     }else if(val==1 || val==2){
         $("#date_one").show();
         $("#date_two").hide();
         $("#date_text").html('<?php echo $this->lang->line('date'); ?>');
     }else if(val==3){
         $("#date_one").show();
         $("#date_two").show();
         $("#date_text").html('<?php echo $this->lang->line('from_date'); ?>');
     }
 }

 function add_holiday() {
      $('#add_holiday').trigger("reset");
      $('input[name="holiday_type"]').prop('checked', false);
      $("#front_site").prop("checked", false);
      $('#title').html('<?php echo $this->lang->line('add_holiday'); ?>');
      bootstrap.Modal.getOrCreateInstance(document.getElementById('holiday_modal'),{backdrop:'static',keyboard:false}).show();
      showDate('');
  }

	function get(id) {
		var base_url = '<?php echo base_url() ?>';
		$('#title').html('<?php echo $this->lang->line('edit_holiday'); ?>');
		bootstrap.Modal.getOrCreateInstance(document.getElementById('holiday_modal'),{backdrop:'static',keyboard:false}).show();

		$.ajax({
			url: base_url+'admin/holiday/getholiday',
			type: "POST",
			data: {id: id},
			dataType: 'json',
			success: function (data) {
				if (data.status == 0) {
                     errorMsg(data.message);
                } else {
					showDate(data.result.holiday_type);
                    $('#from_date').val(data.result.from_date);
                    $('#id').val(data.result.id);
                    $('#description').val(data.result.description);
                    $('#to_date').val(data.result.to_date);

					$('#front_site').prop('checked', data.result.front_site == '1');
					$("#radio_"+data.result.holiday_type).prop('checked', true);
				}
			},
			error: function () {
			}
		});
	}

 function delete_holiday(id) {
      var confirmation = confirm('<?php echo $this->lang->line('delete_confirm') ?>');
      if (confirmation == true) {
          $.ajax({
              url: "<?php echo site_url("admin/holiday/delete_holiday") ?>",
              type: "POST",
              data: {'id': id},
              dataType: "json",
              success: function (res)
              {
                  if (res.status == 0) {
                      errorMsg(res.error);
                  } else {
                      successMsg(res.success);
                      window.location.reload(true);
                  }
              }
          });
      }
  }

</script>
