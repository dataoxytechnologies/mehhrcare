<div class="row">
   <div class="col-md-12">
    <div class="card">
     <div class="card-header">
      <h3 class="card-title titlefix"><?php echo $this->lang->line("duty_roster"); ?></h3>
	   <div class="d-flex gap-2 align-items-center flex-wrap float-end">  
			<?php if ($this->rbac->hasPrivilege('roster_shift', 'can_view')) {?>
				<a href="<?php echo base_url("admin/dutyroster/shift"); ?>" class="btn btn-primary btn-sm"><i class="fa fa-reorder"></i> <?php echo $this->lang->line('shift'); ?>  </a>
			<?php } if ($this->rbac->hasPrivilege('roster_list', 'can_view')) {?>
				<a href="<?php echo base_url("admin/dutyroster/rosterlist"); ?>" class="btn btn-primary btn-sm"><i class="fa fa-reorder"></i> <?php echo $this->lang->line('roster'); ?>  </a>
			<?php } if ($this->rbac->hasPrivilege('roster_assign', 'can_view')) {?>
				<a href="<?php echo base_url("admin/dutyroster/rosterassign"); ?>" class="btn btn-primary btn-sm"><i class="fa fa-reorder"></i> <?php echo $this->lang->line('assign_roster'); ?>  </a>	
			<?php } ?>
	   </div>
     </div>  

	<?php if ($this->rbac->hasPrivilege('duty_roster', 'can_view')) {?>
     <form id='form111' action="<?php echo site_url('admin/dutyroster/roster_report') ?>" method="post" accept-charset="utf-8">
      <?php echo $this->customlib->getCSRF(); ?>
      <div class="card-body row pb-0">
	   <div class="col-sm-6 col-md-4">
        <div class="mb-1"> 
          <label><?php echo $this->lang->line('search_type'); ?> <small class="req"> *</small> </label> 
          <select class="form-control" name="search_type"  id="search_type_select" onchange="showdate(this.value)">
              <option value=""><?php echo $this->lang->line('select') ?></option>
              <?php 
              foreach ($searchlist as $key => $search) { ?>
                  <option value="<?php echo $key ?>" <?php
                                                            if ((isset($search_type)) && ($search_type == $key)) {
                                                                    echo "selected";
                                                                }
                                                                ?>  ><?php echo $search ?></option>
                  <?php }?>
          </select>
          <span class="text-danger" id="error_search_type"><?php echo form_error('search_type'); ?></span>
		</div>
       </div>
	   
							<div class="col-sm-6 col-md-3 d-none" id="fromdate">
                              <div class="mb-1">
                                  <label><?php echo $this->lang->line('date_from'); ?></label><small class="req"> *</small>
                                  <input id="date_from" name="date_from" placeholder="" type="text" class="form-control date" value="<?php echo set_value('date_from', date($this->customlib->getHospitalDateFormat())); ?>"  />
                                  <span class="text-danger"><?php echo form_error('date_from'); ?></span>
                              </div>
							</div>
							<div class="col-sm-6 col-md-3 d-none" id="todate">
                              <div class="mb-1">
                                  <label><?php echo $this->lang->line('date_to'); ?></label><small class="req"> *</small>
                                  <input id="date_to" name="date_to" placeholder="" type="text" class="form-control date" value="<?php echo set_value('date_to', date($this->customlib->getHospitalDateFormat())); ?>"  />
                                  <span class="text-danger"><?php echo form_error('date_to'); ?></span>
                              </div>
							</div>
       <div class="col-sm-6 col-md-4">
        <div class="mb-1">
         <label><?php echo $this->lang->line("staff"); ?></label>
         <div class="d-flex align-items-start gap-2">
          <div class="flex-grow-1">
           <select class="form-control select2" name="staff" id="staff" style="width:100%;">
            <option value=""><?php echo $this->lang->line('select') ?></option>
            <?php foreach($staff_list as $key=>$value){ ?>
            <option value="<?php echo $value['id'];?>" <?php if($staff==$value['id']){ echo "Selected"; } ?>><?php echo $value['name']." ".$value['surname']." (".$value['employee_id'].")";?></option>
            <?php } ?>
           </select>
          </div>
          <button type="submit" name="search" value="search_filter" class="btn btn-primary text-nowrap"><i class="fa fa-search"></i> <?php echo $this->lang->line('search'); ?></button>
         </div>
         <span class="text-danger" id="error_staff"><?php echo form_error('staff'); ?></span>
        </div>
       </div>
      </div>
     </form>
     <div class="card border0 clear">
		<div class="d-flex justify-content-end gap-2 mb-2 pe-3 pt-0">
			<button type="button" title="<?php echo $this->lang->line('export_to_excel') ?>" onclick="fnExcelReport();" class="btn btn-secondary dt-button buttons-excel buttons-html5"><i class="fa fa-file-excel-o"></i></button>
            <button type="button" title="<?php echo $this->lang->line('print') ?>" onclick="print_table('myTable');" class="btn btn-secondary dt-button buttons-print"><i class="fa fa-print"></i></button>
		</div>
      <div class="card-body table-responsive pt-0" id="myTable">
       <div class="download_label"><?php echo $this->lang->line('roster_report'); ?></div>
        <div id="table-heading"></div>
        <table id="headerTable" class="table table-striped table-bordered table-hover allajaxlist" data-export-title="<?php echo $this->lang->line('roster_report'); ?>">
         <thead>
          <tr>
           <th><?php echo $this->lang->line('staff') ?></th>
           <th><?php echo $this->lang->line('date') ?></th>
           <th><?php echo $this->lang->line('shift_start') ?></th>
           <th><?php echo $this->lang->line('shift_end') ?></th>           
           <th><?php echo $this->lang->line('shift_hour') ?></th>           
           <th><?php echo $this->lang->line('shift') ?></th>           
           <th><?php echo $this->lang->line('department') ?></th>           
           <th><?php echo $this->lang->line('floor') ?></th> 
          </tr>
         </thead>
         <tbody>
        <?php 
        if(!empty($roster_data)){    
			foreach($roster_data as $key => $value){
				$i = 0;
				$group_rowspan = count($value);
					foreach($value as $val){
					$i++;
                    $duty_date = date('Y-m-d',strtotime($val['roster_duty_date'] ));
                  ?>
          <tr<?php echo ($i == 1) ? ' class="roster-group-start"' : ''; ?>>
			<?php if($i == 1){ ?>
           <td valign="middle" rowspan="<?php echo $group_rowspan; ?>" class="roster-staff-cell">
            <?php echo html_escape($val['staff_name'])." ".html_escape($val['staff_surname'])." (".html_escape($val['employee_id']).")" ;?>
           </td>
			<?php } ?>
           <td><?php echo $this->customlib->YYYYMMDDTodateFormat($duty_date);?></td>
           <td><?php echo $this->customlib->getHospitalTime_Format($val['shift_start']);?></td>
           <td><?php echo $this->customlib->getHospitalTime_Format($val['shift_end']);?></td>
           <td><?php echo html_escape($val['shift_hour']);?></td>
           <td><?php echo html_escape($val['shift_name']);?></td>
           <td><?php echo html_escape($val['department_name']);?></td>
           <td><?php echo html_escape($val['floor_name']);?></td>
          </tr>
          <?php } ?>
	<?php }
		}else{ ?>  
             <tr>
                <td colspan="8"><center><span class="text-danger"> <?php echo $this->lang->line('no_record_found'); ?></span></center></td>
             </tr>
            
		<?php } ?>  
         </tbody>
        </table>
      </div>
     </div>
	 <?php } ?>	 
    </div>
   </div>
  </div>
</div>

<script type="text/javascript">
    $(document).ready(function (e) {
        $(".select2").select2();
    });
</script>
<script type="text/javascript">
    function print_table(divID) {
        var search_type       = $("#search_type_select").val();
        var date_from         = $("#date_from").val();
        var date_to           = $("#date_to").val();
        var staff             = $("#staff").val();
        var staff_name        = $("#staff option:selected").text();
        var search_type_label = $("#search_type_select option:selected").text();
        if (!search_type) { return; }
        // Open window synchronously (inside click handler) so browsers don't block it as a popup
        var newWin = window.open('', 'Print-Window');
        $.ajax({
            url: '<?php echo base_url(); ?>admin/dutyroster/roster_report_print',
            type: 'POST',
            dataType: 'json',
            data: {
                search_type:       search_type,
                date_from:         date_from,
                date_to:           date_to,
                staff:             staff,
                staff_name:        staff_name,
                search_type_label: search_type_label
            },
            success: function(result) {
                if (result.status == 1) {
                    newWin.document.open();
                    newWin.document.write(result.page);
                    newWin.document.close();
                    setTimeout(function() { newWin.focus(); newWin.print(); }, 500);
                } else {
                    newWin.close();
                }
            },
            error: function() { newWin.close(); }
        });
    }

    function fnExcelReport()
    {
        var tab_text = "<table border='2px'><tr >";
        var j = 0;
        var tab = document.getElementById('headerTable');

        for (j = 0; j < tab.rows.length; j++) {
            tab_text = tab_text + tab.rows[j].innerHTML + "</tr>";
        }

        tab_text = tab_text + "</table>";
        tab_text = tab_text.replace(/<A[^>]*>|<\/A>/g, "");
        tab_text = tab_text.replace(/<img[^>]*>/gi, "");
        tab_text = tab_text.replace(/<input[^>]*>|<\/input>/gi, "");
        var sa = window.open('data:application/vnd.ms-excel,' + encodeURIComponent(tab_text));
        return sa;
    }
</script>
<script type="text/javascript">
	function showdate(value) {
		if (value == 'period') {
                $('#fromdate').removeClass('d-none');
                $('#todate').removeClass('d-none');
        } else {
                $('#fromdate').addClass('d-none');
                $('#todate').addClass('d-none');
		}
	}
		 
		 <?php if($search_type == 'period'){ ?>
		 $(document).ready(function() { showdate('period'); });
		 <?php } ?>
</script>