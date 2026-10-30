<?php 
                                        if (!empty($diagnosis_details)) {
                                            foreach ($diagnosis_details as $diagnosis_key => $diagnosis_value) {

                                                ?>  
                                                <tr>
                                                    <td><?php echo $diagnosis_value["report_type"] ?></td>
                                                    <td><?php echo date($this->customlib->getHospitalDateFormat(), strtotime($diagnosis_value['report_date'])) ?></td>
                                                    <td><?php echo $diagnosis_value["description"] ?></td>
                                                    <td class="text-end">
                                                        <?php if (!empty($diagnosis_value["document"])) { ?>
                                                            <a href="<?php echo base_url() . "admin/patient/report_download/" . $diagnosis_value["document"] ?>" data-bs-toggle="tooltip" class="btn btn-secondary btn-sm" title="<?php echo $this->lang->line('download'); ?>" title="<?php echo $this->lang->line('download'); ?>" ><i class="fa fa-download"></i></a>
                                                        <?php } ?>

                                                        <?php
                                                        if ($this->rbac->hasPrivilege('opd editdiagnosis', 'can_delete')) {
                                                            if (isset($diagnosis_value["diagnosis"])) {
                                                                ?>
                                                                <a 
                                                                    onclick="editDiagnosis('<?php echo $diagnosis_value['id']; ?>')" class="btn btn-secondary btn-sm" data-bs-toggle="tooltip" title=""  title="<?php echo $this->lang->line('edit'); ?>">
                                                                    <i class="fa fa-pencil"></i>
                                                                </a>   
                                                            <?php }
                                                        }
                                                        ?>
        <?php if ($this->rbac->hasPrivilege('opd_diagnosis', 'can_delete')) { ?>
                                                            <a 
                                                                onclick="deleteOpdPatientDiagnosis('<?php echo $diagnosis_value['patient_id']; ?>', '<?php echo $diagnosis_value['id']; ?>')" class="btn btn-secondary btn-sm" data-bs-toggle="tooltip" title=""  title="<?php echo $this->lang->line('delete'); ?>">
                                                                <i class="fa fa-trash"></i>
                                                            </a>   
                                                <?php } ?>
                                                    </td>
                                                </tr>
                                                <?php
                                            }
                                        }
                                        ?>   