<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
?>

<html lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title><?php echo $this->lang->line('prescription'); ?></title>
    </head>
    <div id="html-2-pdfwrapper">
        <div class="row">
            <!-- left column -->
            <div class="col-md-12">
                <div class="pprinta4">
                <?php  if (!empty($print_details['print_header'])) { ?>
                    <img src="<?php
                    if (!empty($print_details['print_header'])) {
                        echo $this->media_storage->getImageURL($print_details['print_header']);
                    }
                    ?>" class="img-fluid sh-print-header-img">
                <?php }?>
                    <div class="sh-ipd-antenatal-spacer-10"></div>
                </div> 
                <div class=""> 
                   <label id="printantehead"></label>
                    <table width="100%" class="printablea4">
                        <tr>
                            <th width="25%"><?php echo $this->lang->line("ipd_no"); ?></th>
                            <td width="25%"><?php echo $this->customlib->getPatientSessionPrefixByType('ipd_no') .$result->ipdid ; ?></td>
                            <th width="25%"><?php echo $this->lang->line("temperature"); ?></th>
                            <td width="25%"><?php echo $result->temperature ; ?></td>                        
                        </tr>
                        <tr>
                            <th width="25%"><?php echo $this->lang->line("pulse"); ?></th>
                            <td width="25%"><?php echo $result->pulse; ?></td>
                        </tr>
                        <tr>
                            <th width="25%"><?php echo $this->lang->line("patient_name"); ?></th>
                            <td width="25%"><?php echo $result->patient_name ?> (<?php echo $result->id ?>)</td>
                            <th width="25%"><?php echo $this->lang->line("height"); ?></th>
                            <td><?php echo $result->height ?></td>  
                        </tr>
                        <tr>
                            <th width="25%"><?php echo $this->lang->line("age"); ?></th>
                            <td><?php echo $this->customlib->getPatientAge($result->age,$result->month,$result->day);  ?></td>
                             <th width="25%"><?php echo $this->lang->line("blood_group"); ?></th>
                            <td><?php echo $result->blood_group; ?></td>                           
                        </tr>
                        <tr>
                           <th width="25%"><?php echo $this->lang->line("gender"); ?></th>
                            <td><?php echo $this->lang->line(strtolower($result->gender)) ?></td>
                            <th width="25%"><?php echo $this->lang->line("weight"); ?></th>
                            <td><?php echo $result->weight ?></td>
                        </tr>                        
                        <tr>
                            <th width="25%"><?php echo $this->lang->line("phone"); ?></th>
                            <td width="25%"><?php echo $result->mobileno ?></td>   
                            <th width="25%"><?php echo $this->lang->line("email"); ?></th>
                            <td width="25%"><?php echo $result->email ?></td>
                        </tr> 
                        <tr>                            
                            <th width="25%"><?php echo $this->lang->line("known_allergies"); ?></th>
                            <td><?php echo $result->known_allergies; ?></td>
                        </tr>                       
                    </table>
                    <hr> 
                     <h4><?php echo $this->lang->line('primary_examine'); ?></h4>
                     <table width="100%" class="printablea4">
                        <tr>
                            <th width="25%"><?php echo $this->lang->line('bleeding'); ?></th>
                            <td width="25%"><?php echo $result->bleeding ; ?></td>
                            <th width="25%"><?php echo $this->lang->line('headache'); ?></th>
                            <td width="25%"><?php echo $result->headache ; ?></td>                        
                        </tr>
                        <tr> 
                                <th width="25%"><?php echo $this->lang->line('pain'); ?></th>
                                <td width="25%"><?php echo $result->pain ?></td>
                                <th width="25%"><?php echo $this->lang->line('constipation'); ?></th>
                                <td width="25%"><?php echo $result->constipation; ?></td>
                        </tr>
                        <tr>
                            <th width="25%"><?php echo $this->lang->line('vomiting'); ?></th>
                            <td width="25%"><?php echo $result->vomiting; ?> </td>
                            <th width="25%"><?php echo $this->lang->line('cough'); ?></th>
                            <td><?php  echo $result->cough;  ?></td>
                        </tr>
                        <tr>                            
                            <th width="25%"><?php echo $this->lang->line('vaginal'); ?></th>
                            <td><?php echo $result->vaginal ?></td>
                            <th width="25%"><?php echo $this->lang->line("weight"); ?></th>
                            <td><?php echo $result->antenatal_weight ?></td>
                        </tr>
                        <tr>                            
                            <th width="25%"><?php echo $this->lang->line('height'); ?></th>
                            <td><?php echo $result->antenatal_height ?></td>
                             <th><?php echo $this->lang->line('date'); ?></th><td><?php  if ($result->antenatal_date!="" &&  $result->antenatal_date!='1970-01-01' && $result->antenatal_date!='0000-00-00') {
                                    echo $this->customlib->YYYYMMDDHisTodateFormat($result->antenatal_date);
                                } ?> </td>
                        </tr>                       
                        <tr>
                            <th width="25%"><?php echo $this->lang->line("discharge"); ?></th>
                            <td width="25%"><?php echo $result->discharge ?></td>   
                            <th width="25%"><?php echo $this->lang->line("oedema"); ?></th>
                            <td width="25%"><?php echo $result->oedema ?></td>
                        </tr> 
                        <tr>        
                            <th><?php echo $this->lang->line('condition'); ?></th><td><?php echo $result->general_condition ; ?> </td>
                        </tr>
                        <tr>
							<th><?php echo $this->lang->line('special_findings_and_remark'); ?></th><td><?php echo $result->finding_remark ; ?></td>
						</tr>
                        <tr>        
                            <th><?php echo $this->lang->line('pelvic_examination'); ?></th><td><?php echo $result->pelvic_examination ; ?></td>                            
                        </tr> 
                        <tr>        
                            <th><?php echo $this->lang->line('sp'); ?></th><td><?php echo $result->sp ; ?></td>                            
                        </tr>                      
                    </table>
                    <hr/>
                    <h4><?php echo $this->lang->line('antenatal_examine'); ?></h4>
                     <table width="100%" class="printablea4">
                        <tr>
                            <th width="25%"><?php echo $this->lang->line('uter_size'); ?></th>
                            <td width="25%"><?php echo $result->uter_size ; ?></td>
                            <th width="25%"><?php echo $this->lang->line('uterus_size'); ?></th>
                            <td width="25%"><?php echo $result->uterus_size ; ?></td>                        
                        </tr>
                        <tr> 
                                <th width="25%"><?php echo $this->lang->line('presentation_position'); ?></th>
                                <td width="25%"><?php echo $result->presentation_position ?></td>
                                <th width="25%"><?php echo $this->lang->line('presenting_part_to_brim'); ?></th>
                                <td width="25%"><?php echo $result->brim_presentation; ?></td>
                        </tr>
                        <tr>
                            <th width="25%"><?php echo $this->lang->line('foeta_heart'); ?></th>
                            <td width="25%"><?php echo $result->foeta_heart; ?> </td>
                            <th width="25%"><?php echo $this->lang->line('blood_pressure'); ?></th>
                            <td><?php echo $result->blood_pressure; ?></td>
                        </tr>
                        <tr>                            
                            <th width="25%"><?php echo $this->lang->line('vaginal'); ?></th>
                            <td><?php echo $result->vaginal ?></td>
                            <th width="25%"><?php echo $this->lang->line('antenatal_weight'); ?></th>
                            <td><?php echo $result->antenatal_weight ?></td>
                        </tr>
                        <tr>                            
                            <th width="25%"><?php echo $this->lang->line('antenatal_oedema'); ?></th>
                            <td><?php echo $result->antenatal_oedema ?></td>
                            <th width="25%"><?php echo $this->lang->line('urine_sugar'); ?></th>
                            <td><?php echo $result->urine_sugar ?></td>
                        </tr>
                        <tr>                            
                            <th width="25%"><?php echo $this->lang->line('urine_aaibumen'); ?></th>
                            <td><?php echo $result->urine; ?></td>
                        </tr>
                        <tr>
                            <th width="25%"><?php echo $this->lang->line('remark') ; ?></th>
                            <td width="25%"><?php echo $result->remark ?></td> 
                        </tr> 
                         <tr>
                            <th width="25%"><?php echo $this->lang->line('next_visit'); ?></th>
                            <td width="25%"><?php echo $result->next_visit ?></td>
                        </tr> 
                        
                        <?php
                         if (!empty($fields)) { 
                            foreach ($fields as $fields_key => $fields_value) { ?> 
                                <tr>  
                                <td><?php echo $fields_value->name; ?></td>
                                <td><?php echo $result->{"$fields_value->name"}; ?></td>
                                </tr>
                        <?php  }  
                            }
                        ?>
                         
                    </table>
                    <hr class="sh-ipd-antenatal-hr" />                    
                </div>
            </div>
            <!--/.col (left) -->
        </div>
    </div>
</html>

<script type="text/javascript">
    $('#edit_printfinding').html("<a href='#'' onclick='printipdantenatalprescription(<?php echo $id;?>)'  data-bs-toggle='tooltip' title='<?php echo $this->lang->line('print'); ?>'><i class='fa fa-print'></i></a>");
</script>