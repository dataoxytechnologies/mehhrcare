
<?php
$hospital = $sch_setting[0];
$i = 0;
?>

<table cellpadding="0" cellspacing="0" width="100%">
    <tr>
        <?php
        foreach ($patients as $patient) {
            $i++;
            ?>
            <td valign="top" width="32%" class="sh-idcard-outer-td">
                <table cellpadding="0" cellspacing="0" width="100%" class="tc-container sh-idcard-card-bg sh-idcard-print" style="--idcard-header: <?php echo htmlspecialchars($patient_id_card[0]->header_color) ?>">
                    <tr>
                        <td valign="top">
                            <?php if(!empty($patient_id_card[0]->background)){ ?>
                            <img src="<?php echo $this->media_storage->getImageURL('uploads/patient_id_card/background/' . $patient_id_card[0]->background); ?>" class="tcmybg" />
                            <?php } ?>
                        </td>
                    </tr>
                    <tr>
                        <td valign="top">
                            <div class="patienttop">

                                <div class="sttext1">
                                <?php if(!empty($patient_id_card[0]->logo)){ ?>
                                <img src="<?php echo $this->media_storage->getImageURL('uploads/patient_id_card/logo/' . $patient_id_card[0]->logo); ?>"  />
                                <?php } ?>
                                    <?php echo $patient_id_card[0]->hospital_name ?></div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td valign="top" class="text-center sh-idcard-addr-td">
                            <p>  <?php echo $patient_id_card[0]->hospital_address ?></p>

                        </td>
                    </tr>
                    <tr>
                        <td valign="top" class="sh-idcard-title-td"><?php echo $patient_id_card[0]->title ?></td>
                    </tr>
                    <tr>
                        <td valign="top">
                            <div class="staround">
                                <div class="cardleft">
                                    <div class="stimg">
                                        <?php if(!empty($patient->image)){ ?>
                                        <img src="<?php echo $this->media_storage->getImageURL($patient->image); ?>" class="img-fluid" />
                                        <?php } ?>
                                    </div>
                                    <?php if ($patient_id_card[0]->enable_barcode == 1) { ?>
                                    <div class="barcodeimg"><img src="<?php echo $this->media_storage->getImageURL($patient->barcode); ?>" /></div>
                                    <?php } ?>
                                </div><!--./cardleft-->
                                <div class="cardright">
                                    <ul class="stlist">
                                        <?php if ($patient_id_card[0]->enable_patient_name == 1) { ?><li><?php echo $this->lang->line('patient_name'); ?><span> <?php echo $patient->patient_name; ?></span></li><?php } ?>

                                        <?php if ($patient_id_card[0]->enable_guardian_name == 1) { ?><li><?php echo $this->lang->line('guardian_name'); ?><span><?php echo $patient->guardian_name; ?></span></li><?php } ?>

                                        <?php if ($patient_id_card[0]->enable_patient_unique_id == 1) { ?><li><?php echo $this->lang->line('patient_unique_id'); ?><span> <?php echo $patient->id; ?></span></li><?php } ?>

                                        <?php if ($patient_id_card[0]->enable_address == 1) { ?><li><?php echo $this->lang->line('address')?><span><?php echo $patient->address; ?></span></li><?php } ?>

                                        <?php if ($patient_id_card[0]->enable_phone == 1) { ?><li>Phone<span><?php echo $patient->mobileno; ?></span></li><?php } ?>
                                        <?php
                                        if ($patient_id_card[0]->enable_dob == 1) {
                                            ?>
                                            <li><?php echo $this->lang->line('date_of_birth');?>
                                                <span>
                                                    <?php
                                                    echo $dob = "";
                                                    if ($patient->dob != "0000-00-00" && $patient->dob !="") {
                                                        $dob = date($this->customlib->getHospitalDateFormat(true, false), strtotime($patient->dob));
                                                    }
                                                    echo $dob;
                                                    ?>
                                                </span></li>
                                            <?php
                                        }
                                        ?>

                                        <?php if ($patient_id_card[0]->enable_blood_group == 1) { ?><li class="stred"><?php echo $this->lang->line('blood_group'); ?><span><?php echo $patient->blood_group; ?></span></li><?php } ?>
                                    </ul>
                                </div><!--./cardright-->
                            </div><!--./staround-->
                        </td>
                    </tr>
                    <tr>
                        <td valign="top" class="principal text-end">
                            <?php if(!empty($patient_id_card[0]->sign_image)){ ?>
                            <img src="<?php echo $this->media_storage->getImageURL('uploads/patient_id_card/signature/' . $patient_id_card[0]->sign_image); ?>" width="66" height="40" />
                            <?php } ?>
                        </td>
                    </tr>
                </table>
            </td>

            <?php
            if ($i == 3) {
                // three items in a row. Edit this to get more or less items on a row
                ?></tr><tr><?php
                $i = 0;
            }
        }
        ?>
    </tr>
</table>