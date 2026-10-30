<div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title titlefix text-center"><?php echo $this->lang->line('patient_queue'); ?></h3>
                    </div>
                    <div class="card-body">
                        <form action="<?php echo site_url("admin/onlineappointment/patientqueue"); ?>" method="post" accept-charset="utf-8">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="pwd"><?php echo $this->lang->line('doctor') ?></label>
                                    <span class="req"> *</span>
                                    <select name="doctor" onchange="getDoctorShift()" id="doctor" class="form-control select2">
                                        <option value=""><?php echo $this->lang->line('select'); ?></option>
                                        <?php foreach ($doctors as $doctor_key => $doctor_value) {?>
                                                <option value="<?php echo $doctor_value['id']; ?>" <?php echo $doctor_value["id"] == set_value("doctor") ? "selected" : ""; ?>><?php echo composeStaffNameByString($doctor_value['name'],$doctor_value['surname'],$doctor_value['employee_id']); ?></option>
                                        <?php }?>
                                    </select>
                                    <span class="text-danger"><?php echo form_error('doctor'); ?></span>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="pwd"><?php echo $this->lang->line('shift'); ?></label><span class="req"> *</span>
                                    <select name="global_shift" id="global_shift" class="form-control select2">
                                        <option value=""><?php echo $this->lang->line('select'); ?></option>
                                    </select>
                                    <span class="text-danger"><?php echo form_error('global_shift'); ?></span>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="date"><?php echo $this->lang->line('date'); ?></label><span class="req"> *</span>                                    
                                    <div class='input-group'>
                                        <input type='text' id="datetimepicker" value="<?php echo set_value('date'); ?>" class="form-control date" name="date" /><span class="input-group-text"><i class="fa fa-calendar"></i></span>
                                    </div>
                                    <span class="text-danger"><?php echo form_error('date'); ?></span>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="slot"><?php echo $this->lang->line('slot'); ?></label>                                   
                                    <select name="slot" id="slot" class="form-control">
                                        <option value=""><?php echo $this->lang->line('select'); ?></option>
                                    </select>
                                    <span class="text-danger"><?php echo form_error('slot'); ?></span>
                                </div>
                            </div>
                        </div>
                        <div class="float-end">
                        <?php if ($this->rbac->hasPrivilege('patient_queue', 'can_edit')) { ?>
                            <button type="submit" name="submit" value="regenerate" class="btn btn-primary btn-sm"><?php echo $this->lang->line('reorder_queue'); ?></button>
                        <?php } ?>    
                            <button type="submit" name="submit" value="search" class="btn btn-primary btn-sm"><?php echo $this->lang->line('search') ?></button>
                        </div>
                        </form>
                    </div>

                    <?php if (isset($resultlist)) {?>
                    <div class="card-body">
                        
                        <div class="table-responsive mailbox-messages" id="myTable">
                        <h3 id="table-heading"></h3>
                         <table id="headerTable" class="table table-hover table-striped table-bordered">
                            <div class="float-end mb-2">
                            <button type="button"  data-bs-toggle="tooltip" title="<?php echo $this->lang->line("export_to_excel"); ?>" onclick="fnExcelReport();" class="btn btn-sm btn-outline-secondary"><i class="fa fa-file-excel-o"></i></button>
                            <button type="button"  data-bs-toggle="tooltip" title="<?php echo $this->lang->line("print"); ?>" onclick="print_table('myTable');" class="btn btn-sm btn-outline-secondary"><i class="fa fa-print"></i></button>
                        </div>
                                <thead>
                                    <tr>                                         
                                        <th><?php echo $this->lang->line("appointment_s_no"); ?></th>
                                        <th><?php echo $this->lang->line("patient_name"); ?></th>
                                        <th><?php echo $this->lang->line('phone'); ?></th>
                                        <th><?php echo $this->lang->line('email'); ?></th>
                                        <th><?php echo $this->lang->line('date'); ?></th>
                                        <th><?php echo $this->lang->line('time'); ?></th>
                                        <th><?php echo $this->lang->line('source'); ?></th>
                                    </tr>
                                </thead>
                            <?php if (!empty($resultlist)) {?>
                                <tbody class="row_position">
                                <?php foreach ($resultlist as $result_key => $result_value) {?>
                                    <tr id="<?php echo $result_value["queue_id"]; ?>">
                                        <td><?php echo $result_value["position"]; ?></td>
                                        <td><?php echo $result_value["patient_name"]; ?> (<?php echo $result_value["patient_unique_id"]; ?>)</td>
                                        <td><?php echo $result_value["mobileno"]; ?></td>
                                        <td><?php echo $result_value["email"]; ?></td>
                                        <td><?php echo date($this->customlib->getHospitalDateFormat(true, false), strtotime($result_value["date"])); ?></td>
                                        <td><?php echo $result_value["date"]?date("h:i A", strtotime($result_value["date"])):""; ?></td>
                                        <td <?php echo $result_value["source"] == "Online" ? "class='sh-text-red'" : ""; ?>><?php echo $this->lang->line(strtolower($result_value["source"])); ?></td>
                                    </tr>
                                <?php }?>
                                </tbody>
                            <?php }else{
                                ?>
                                    <tr>
                                        <td colspan="7" class="text text-center text-danger"><?php echo $this->lang->line('no_record_found'); ?></td>
                                    </tr>
                                <?php
                            }
                            ?>
                            </table>
                        </div>
                    </div>
                <?php }?>
                </div>
            </div>
        </div>

<script>
    $(document).ready(function(){
        if($("#doctor").val() != ''){
            var selected  = <?php echo set_value("global_shift") != '' ? set_value("global_shift") : 0; ?>;
            getDoctorShift(selected);
        }
    });

    $('#datetimepicker').on('change', function(e){
        if($("#global_shift").val() != ''){
            getShift();
        }
    })

    function getShift(){
        var div_data = "";
        var date = $("#datetimepicker").val();
        var doctor = $("#doctor").val();
        var global_shift = $("#global_shift").val();
        $.ajax({
            url: '<?php echo base_url(); ?>admin/onlineappointment/getShift',
            type: "POST",
            data: {doctor: doctor, date: date, global_shift:global_shift},
            dataType: 'json',
            success: function(res){
                $.each(res, function (i, obj)
                {
                    div_data += "<option value=" + obj.id + ">" + obj.start_time +" - "+ obj.end_time +"</option>";
                });
                $("#slot").html("<option value=''><?php echo $this->lang->line('select'); ?></option>");
                $('#slot').append(div_data);
                <?php if (isset($shift)) {?>
                $("#slot").val(<?php echo $shift; ?>);
                <?php }?>
            }
        });
    }
</script>
<script>
    function getQueue(){
        $.ajax({
            url: '<?php echo base_url(); ?>site/getShift',
            type: "POST",
            data: {doctor: doctor, date: date},
            dataType: 'json',
            success: function(res){
            }
        });
    }
    
    function getDoctorShift(prev_val = 0){
        var doctor_id = $("#doctor").val();
        var select = "";
        var select_box = "<option value=''><?php echo $this->lang->line('select'); ?></option> ";
        $.ajax({
            type: 'POST',
            url: base_url + "admin/onlineappointment/doctorshiftbyid",
            data: {doctor_id:doctor_id},
            dataType: 'json',
            success: function(res){
                $.each(res, function(i, list){
                    selected = list.id == prev_val ? "selected" : "";
                    select_box += "<option value='"+ list.id +"' "+ selected +">"+ list.name +"</option>";
                });
                $("#global_shift").html(select_box);
                    <?php if (isset($shift)) {?>
                        if($("#datetimepicker").val() != ''){
                            getShift();
                        }
                    <?php }?>
           }
        });
    }
</script>
<script>
    $( ".row_position" ).sortable({
        delay: 150,
        stop: function() {
            var selectedData = new Array();
            $('.row_position>tr').each(function() {
                selectedData.push($(this).attr("id"));
            });
            updateOrder(selectedData);
        }
    });

    function updateOrder(data) {
        $.ajax({
            url: base_url + "admin/onlineappointment/sortQueue",
            type:'post',
            dataType:'json',
            data:{position:data},
            success:function(data){
                if(data.status =="success"){
                    successMsg(data.message);
                }else{
                    errorMsg(data.message);
                }
            }
        })
    }
</script>
<script type="text/javascript">
    function print_table(divID) {
        var doctor      = $("#doctor").val();
        var date        = $("#datetimepicker").val();
        var shift       = <?php echo isset($shift) ? (int)$shift : 0; ?>;
        var doctor_name = $("#doctor option:selected").text();
        if (!doctor || !date) { return; }
        $.ajax({
            url: '<?php echo base_url(); ?>admin/onlineappointment/patientqueue_print',
            type: 'POST',
            data: { doctor: doctor, date: date, shift: shift, doctor_name: doctor_name },
            success: function(result) {
                popup(result);
            }
        });
    }

    function fnExcelReport()
    {
        var tab_text = "<table border='2px'><tr >";
        var textRange;
        var j = 0;
        tab = document.getElementById('headerTable'); // id of table

        for (j = 0; j < tab.rows.length; j++)
        {
            tab_text = tab_text + tab.rows[j].innerHTML + "</tr>";           
        }

        tab_text = tab_text + "</table>";
        tab_text = tab_text.replace(/<A[^>]*>|<\/A>/g, "");//remove if u want links in your table
        tab_text = tab_text.replace(/<img[^>]*>/gi, ""); // remove if u want images in your table
        tab_text = tab_text.replace(/<input[^>]*>|<\/input>/gi, ""); // reomves input params
        var ua = window.navigator.userAgent;
        var msie = ua.indexOf("MSIE ");
        if (msie > 0 || !!navigator.userAgent.match(/Trident.*rv\:11\./))      // If Internet Explorer
        {
            txtArea1.document.open("txt/html", "replace");
            txtArea1.document.write(tab_text);
            txtArea1.document.close();
            txtArea1.focus();
            sa = txtArea1.document.execCommand("SaveAs", true, "Say Thanks to Sumit.xls");
        } else                 //other browser not tested on IE 11
            sa = window.open('data:application/vnd.ms-excel,' + encodeURIComponent(tab_text));
        return (sa);
    }
</script>