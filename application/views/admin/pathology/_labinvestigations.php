<div class="row sh-form-card h-100">
                        <div class="sh-card-header">
                             <span class="sh-card-header-title"><?php echo $this->lang->line('patient_details'); ?></span>
                         </div>
<table class="table table-striped table-bordered noborder"> 
    <tbody>
        <tr>   
            <th width="25%"><?php echo $this->lang->line('bill_no'); ?></th>
            <td width="25%"><?php echo $this->customlib->getSessionPrefixByType('pathology_billing') .$result->pathology_bill_id; ?></td>
            <th width="25%"><?php echo $this->lang->line('patient'); ?></th>
            <td width="25%"><?php echo composePatientName($result->patient_name,$result->patient_id); ?></td>
        </tr>
        <tr>
            <th width="25%"><?php echo $this->lang->line('approve_date'); ?></th>
            <td width="25%"><?php if($result->parameter_update){ echo $this->customlib->YYYYMMDDTodateFormat($result->parameter_update); } ?></td>
            <th width="25%"><?php echo $this->lang->line('report_collection_date'); ?>:</th>
            <td width="25%"><?php if($result->collection_date){ echo $this->customlib->YYYYMMDDTodateFormat($result->collection_date); } ?></td>  
        </tr>
        <tr>
            <th width="25%"><?php echo $this->lang->line('test_name'); ?></th>
            <td width="25%"><?php echo $result->test_name; ?></td>
            <th width="25%"><?php echo $this->lang->line('expected_date'); ?></th>
            <td width="25%"><?php if($result->reporting_date){ echo $this->customlib->YYYYMMDDTodateFormat($result->reporting_date); } ?></td>  
        </tr>
        <tr>
            <th width="25%"><?php echo $this->lang->line('collection_by'); ?></th>
            <td width="25%"><?php echo composeStaffNameByString($result->collection_specialist_staff_name,$result->collection_specialist_staff_surname,$result->collection_specialist_staff_employee_id); ?></td>  
            <th width="25%"><?php echo $this->lang->line('pathology_center'); ?></th>
            <td width="25%"><?php echo $result->pathology_center ?></td>   
        </tr>
        <tr>
             <th width="25%"><?php echo $this->lang->line('case_id'); ?></th>
             <td width="25%"><?php echo $result->case_reference_id ; ?></td>
             <th width="25%"><?php echo $this->lang->line('approved_by'); ?></th>
             <td width="25%"><?php echo $result->doctor_name ; ?></td>
        </tr>
        <tr>           
         <?php if($result->pathology_report != ""){ ?>
            <td>
               <a href="<?php echo site_url('admin/pathology/downloadReport/'.$result->id) ?>" class='btn btn-secondary btn-sm' data-bs-toggle='tooltip' title='<?php echo $this->lang->line('download'); ?>'><i class="fa fa-download"></i></a>
           </td>
        <?php } ?>
        </tr>
    </tbody>
</table>
  </div>

<div class="row sh-form-card mt-3">
    <div class="sh-card-header">
        <span class="sh-card-header-title"><?php echo $this->lang->line('lab_investigation'); ?></span>
    </div>
    <div class="col-md-12">   
        <h4 class="text-center"><strong><?php echo $result->test_name; ?></strong>
        <br/>
        <?php echo "(".$result->short_name.")"; ?>
        </h4>           
        <table class="table table-hover">
            <thead>
                <tr class="line">
                    <th>#</th>
                    <th class="text-start"><?php echo $this->lang->line('test_parameter_name'); ?></th>
                    <th class="text-center"><?php echo $this->lang->line('report_value'); ?></th>                  
                    <th class="text-end"><?php echo $this->lang->line('reference_range'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php
                if(!empty($result->pathology_parameter)){
                $row_counter=1;
                foreach ($result->pathology_parameter as $parameter_key=> $parameter_value) {
                ?>                        
                <tr>
                    <td><?php echo $row_counter; ?></td>
                    <td class="text-start"><?php echo $parameter_value->parameter_name; ?><div class="bill_item_footer text-muted"><label><?php if($parameter_value->description !=''){ echo $this->lang->line('description').': ';} ?></label> <?php echo $parameter_value->description; ?></div></td>                     
                    <td class="text-center"><?php echo $parameter_value->pathology_report_value." ".$parameter_value->unit_name;?></td>  
                    <td class="text-end"><?php echo $parameter_value->reference_range." ".$parameter_value->unit_name; ?></td>                    
                </tr>
                <?php
                    $row_counter++;
                }?>
                <?php if($parameter_value->pathology_result!=""){ ?> 
                <tr> 
                    <td colspan="4"><p><b><?php echo $this->lang->line('result'); ?>: </b> <?php echo nl2br($parameter_value->pathology_result); ?></p></td>
                </tr>                             
                <?php
                }  } ?>
                                        
            </tbody>
        </table>
    </div>
</div>
</div>
</div>