<?php
include(APPPATH . 'views/admin/shared/_print_css.php');
?>

<div class="fixed-print-header">
    <?php if (!empty($print_details[0]['print_header'])) { ?>
        <img src="<?php echo $this->media_storage->getImageURL($print_details[0]['print_header']); ?>"
             class="img-fluid sh-avatar-cover" >
    <?php } ?>
</div>

<table class="table-print-full" width="100%">
    <thead>
        <tr><td><div class="header-space">&nbsp;</div></td></tr>
    </thead>
    <tbody>
        <tr><td>
            <div class="content-body sh-px-12" >
            <div class="print-area">

                <!-- ① Document title -->
                <div class="sh-print-title"><?php echo $this->lang->line('antenatal_finding'); ?></div>

                <!-- ② Patient info block -->
                <div class="sh-print-info-block">
                    <table class="sh-print-info-table">
                        <colgroup>
                            <col style="width:20%"><col style="width:30%">
                            <col style="width:20%"><col style="width:30%">
                        </colgroup>
                        <tr>
                            <th><?php echo $this->lang->line('ipd_no'); ?></th>
                            <td><?php echo $this->customlib->getPatientSessionPrefixByType('ipd_no') . ($result->ipdid ?: '-'); ?></td>
                            <th><?php echo $this->lang->line('patient_name'); ?></th>
                            <td><?php echo ($result->patient_name ?: '-') . ($result->id ? ' (' . $result->id . ')' : ''); ?></td>
                        </tr>
                        <tr>
                            <th><?php echo $this->lang->line('age'); ?></th>
                            <td><?php echo ($this->customlib->getPatientAge($result->age, $result->month, $result->day) ?: '-'); ?></td>
                            <th><?php echo $this->lang->line('blood_group'); ?></th>
                            <td><?php echo ($result->blood_group ?: '-'); ?></td>
                        </tr>
                        <tr>
                            <th><?php echo $this->lang->line('gender'); ?></th>
                            <td><?php echo ($result->gender ? $this->lang->line(strtolower($result->gender)) : '-'); ?></td>
                            <th><?php echo $this->lang->line('phone'); ?></th>
                            <td><?php echo ($result->mobileno ?: '-'); ?></td>
                        </tr>
                        <tr>
                            <th><?php echo $this->lang->line('email'); ?></th>
                            <td><?php echo ($result->email ?: '-'); ?></td>
                            <th><?php echo $this->lang->line('known_allergies'); ?></th>
                            <td><?php echo ($result->known_allergies ?: '-'); ?></td>
                        </tr>
                        <?php if (!empty($result->antenatal_date)) { ?>
                        <tr>
                            <th><?php echo $this->lang->line('date'); ?></th>
                            <td colspan="3"><?php echo $this->customlib->YYYYMMDDHisTodateFormat($result->antenatal_date); ?></td>
                        </tr>
                        <?php } ?>
                    </table>
                </div>

                <!-- Divider for clarity -->
                <div class="sh-section-divider"></div>

                <!-- ③ Primary Examine -->
                <div class="sh-print-section-title"><?php echo $this->lang->line('primary_examine'); ?></div>
                <div class="sh-print-info-block">
                    <table class="sh-print-info-table">
                        <colgroup>
                            <col style="width:20%"><col style="width:30%">
                            <col style="width:20%"><col style="width:30%">
                        </colgroup>
                        <tr>
                            <th><?php echo $this->lang->line('bleeding'); ?></th>
                            <td><?php echo ($result->bleeding ?: '-'); ?></td>
                            <th><?php echo $this->lang->line('headache'); ?></th>
                            <td><?php echo ($result->headache ?: '-'); ?></td>
                        </tr>
                        <tr>
                            <th><?php echo $this->lang->line('pain'); ?></th>
                            <td><?php echo ($result->pain ?: '-'); ?></td>
                            <th><?php echo $this->lang->line('constipation'); ?></th>
                            <td><?php echo ($result->constipation ?: '-'); ?></td>
                        </tr>
                        <tr>
                            <th><?php echo $this->lang->line('vomiting'); ?></th>
                            <td><?php echo ($result->vomiting ?: '-'); ?></td>
                            <th><?php echo $this->lang->line('cough'); ?></th>
                            <td><?php echo ($result->cough ?: '-'); ?></td>
                        </tr>
                        <tr>
                            <th><?php echo $this->lang->line('vaginal'); ?></th>
                            <td><?php echo ($result->vaginal ?: '-'); ?></td>
                            <th><?php echo $this->lang->line('weight'); ?></th>
                            <td><?php echo ($result->antenatal_weight ?: '-'); ?></td>
                        </tr>
                        <tr>
                            <th><?php echo $this->lang->line('height'); ?></th>
                            <td><?php echo ($result->antenatal_height ?: '-'); ?></td>
                            <th><?php echo $this->lang->line('discharge'); ?></th>
                            <td><?php echo ($result->discharge ?: '-'); ?></td>
                        </tr>
                        <tr>
                            <th><?php echo $this->lang->line('oedema'); ?></th>
                            <td><?php echo ($result->oedema ?: '-'); ?></td>
                            <th><?php echo $this->lang->line('condition'); ?></th>
                            <td><?php echo ($result->general_condition ?: '-'); ?></td>
                        </tr>
                        <tr>
                            <th><?php echo $this->lang->line('special_findings_and_remark'); ?></th>
                            <td colspan="3"><?php echo ($result->finding_remark ?: '-'); ?></td>
                        </tr>
                        <tr>
                            <th><?php echo $this->lang->line('pelvic_examination'); ?></th>
                            <td colspan="3"><?php echo ($result->pelvic_examination ?: '-'); ?></td>
                        </tr>
                        <tr>
                            <th><?php echo $this->lang->line('sp'); ?></th>
                            <td colspan="3"><?php echo ($result->sp ?: '-'); ?></td>
                        </tr>
                    </table>
                </div>

                <!-- Divider -->
                <div class="sh-section-divider"></div>

                <!-- ④ Antenatal Examine -->
                <div class="sh-print-section-title"><?php echo $this->lang->line('antenatal_examine'); ?></div>
                <div class="sh-print-info-block">
                    <table class="sh-print-info-table">
                        <colgroup>
                            <col style="width:20%"><col style="width:30%">
                            <col style="width:20%"><col style="width:30%">
                        </colgroup>
                        <tr>
                            <th><?php echo $this->lang->line('uter_size'); ?></th>
                            <td><?php echo ($result->uter_size ?: '-'); ?></td>
                            <th><?php echo $this->lang->line('uterus_size'); ?></th>
                            <td><?php echo ($result->uterus_size ?: '-'); ?></td>
                        </tr>
                        <tr>
                            <th><?php echo $this->lang->line('presentation_position'); ?></th>
                            <td><?php echo ($result->presentation_position ?: '-'); ?></td>
                            <th><?php echo $this->lang->line('presenting_part_to_brim'); ?></th>
                            <td><?php echo ($result->brim_presentation ?: '-'); ?></td>
                        </tr>
                        <tr>
                            <th><?php echo $this->lang->line('foeta_heart'); ?></th>
                            <td><?php echo ($result->foeta_heart ?: '-'); ?></td>
                            <th><?php echo $this->lang->line('blood_pressure'); ?></th>
                            <td><?php echo ($result->blood_pressure ?: '-'); ?></td>
                        </tr>
                        <tr>
                            <th><?php echo $this->lang->line('vaginal'); ?></th>
                            <td><?php echo ($result->vaginal ?: '-'); ?></td>
                            <th><?php echo $this->lang->line('antenatal_weight'); ?></th>
                            <td><?php echo ($result->antenatal_weight ?: '-'); ?></td>
                        </tr>
                        <tr>
                            <th><?php echo $this->lang->line('antenatal_oedema'); ?></th>
                            <td><?php echo ($result->antenatal_oedema ?: '-'); ?></td>
                            <th><?php echo $this->lang->line('urine_sugar'); ?></th>
                            <td><?php echo ($result->urine_sugar ?: '-'); ?></td>
                        </tr>
                        <tr>
                            <th><?php echo $this->lang->line('urine_aaibumen'); ?></th>
                            <td><?php echo ($result->urine ?: '-'); ?></td>
                            <th><?php echo $this->lang->line('remark'); ?></th>
                            <td><?php echo ($result->remark ?: '-'); ?></td>
                        </tr>
                        <tr>
                            <th><?php echo $this->lang->line('next_visit'); ?></th>
                            <td colspan="3"><?php echo ($result->next_visit ?: '-'); ?></td>
                        </tr>
                    </table>
                </div>


            </div>
            </div>
        </td></tr>
    </tbody>
    <tfoot>
        <tr><td>
            <?php if (!empty($print_details[0]['print_footer'])) { ?>
                <div class="footer-space">&nbsp;</div>
            <?php } ?>
        </td></tr>
    </tfoot>
</table>

<?php if (!empty($print_details[0]['print_footer'])) { ?>
<div class="footer-fixed">
    <?php echo $print_details[0]['print_footer']; ?>
</div>
<?php } ?>

<script type="text/javascript">
    $('#edit_deletefinding').html("<a href='#'' onclick='printipdantenatalprescription(<?php echo $id;?>)'  data-bs-toggle='tooltip' title='<?php echo $this->lang->line('print'); ?>'><i class='fa fa-print'></i></a><a href='#'' onclick='edit_ipdantenatalprescription(<?php echo $id;?>)' data-bs-target='#edit_prescription' data-bs-toggle='tooltip' title='<?php echo $this->lang->line('edit'); ?>'><i class='fa fa-pencil'></i></a><a onclick='delete_ipdantenatal(<?php echo $id;?>)'  href='#'  data-bs-toggle='tooltip' title='<?php echo $this->lang->line('delete'); ?>'><i class='fa fa-trash'></i></a>");
</script>
