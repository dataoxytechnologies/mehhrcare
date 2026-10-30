
<?php $i=0; ?>
<table cellpadding="0" cellspacing="0" width="100%">
    <tr>
        <?php
        foreach ($staffs as $staff_value) {
            $i++;
            ?>
            <td valign="top" width="32%" class="sh-idcard-outer-td">
                <table cellpadding="0" cellspacing="0" width="100%" class="tc-container sh-idcard-card-bg sh-idcard-print" style="--idcard-header: <?php echo htmlspecialchars($id_card[0]->header_color) ?>">
                    <tr>
                        <td valign="top">
                            <?php if (!empty($id_card[0]->background)) { ?>
                            <img src="<?php echo $this->media_storage->getImageURL('uploads/staff_id_card/background/' . $id_card[0]->background); ?>" class="tcmybg" />
                            <?php } ?>
                        </td>
                    </tr>
                    <tr>
                        <td valign="top">
                            <div class="patienttop">
                                <div class="sttext1">
                                <?php if(!empty($id_card[0]->logo)) { ?>
                                <img src="<?php echo $this->media_storage->getImageURL('uploads/staff_id_card/logo/' . $id_card[0]->logo); ?>" width="30" height="30" />
                                <?php  } ?>
                                    <?php echo $id_card[0]->hospital_name ?></div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td valign="top" class="text-center sh-idcard-addr-td">
                            <p> <?php echo $id_card[0]->hospital_address ?></p>
                        </td>
                    </tr>
                    <tr>
                        <td valign="top" class="sh-idcard-title-td"><?php echo $id_card[0]->title ?></td>
                    </tr>
                    <tr>
                        <td valign="top">
                            <div class="staround">
                                <div class="cardleft">
                                    <div class="stimg">
                                        <?php if(!empty($staff_value->image)){ ?>
                                        <img src="<?php echo $this->media_storage->getImageURL('uploads/staff_images/'.$staff_value->image); ?>" class="img-fluid" />
                                        <?php }else{ ?>
                                        <img src="<?php echo $this->media_storage->getImageURL('uploads/patient_images/no_image.png'); ?>" class="img-fluid" />
                                        <?php } ?>
                                    </div>
                                    <?php if ($id_card[0]->enable_staff_barcode == 1) { ?>
                                    <div class="barcodeimg">
                                        <img src="<?php echo $this->media_storage->getImageURL($staff_value->barcode); ?>" />
                                    </div>
                                    <?php } ?>
                                </div><!--./cardleft-->
                                <div class="cardright">
                                    <ul class="stlist">
                                        <?php if ($id_card[0]->enable_name == 1) { ?><li><?php echo $this->lang->line('staff_name'); ?><span> <?php echo $staff_value->name; ?> <?php echo $staff_value->surname; ?></span></li><?php } ?>
                                        <?php if ($id_card[0]->enable_staff_id == 1) { ?><li><?php echo $this->lang->line('staff_id'); ?><span> <?php echo $staff_value->employee_id; ?></span></li><?php } ?>
                                        <?php if ($id_card[0]->enable_designation == 1) { ?><li><?php echo $this->lang->line('designation'); ?><span><?php echo $staff_value->designation; ?></span></li><?php } ?>
                                        <?php if ($id_card[0]->enable_staff_department == 1) { ?><li><?php echo $this->lang->line('department'); ?><span> <?php echo $staff_value->department; ?></span></li><?php } ?>
                                        <?php if ($id_card[0]->enable_fathers_name == 1) { ?><li><?php echo $this->lang->line('father_name'); ?><span><?php echo $staff_value->father_name; ?></span></li><?php } ?>
                                        <?php if ($id_card[0]->enable_mothers_name == 1) { ?><li><?php echo $this->lang->line('mother_name'); ?><span><?php echo $staff_value->mother_name; ?></span></li><?php } ?>
                                        <?php if ($id_card[0]->enable_date_of_joining == 1) { ?><li><?php echo $this->lang->line('date_of_joining'); ?><span><?php if(!empty($staff_value->date_of_joining) && $staff_value->date_of_joining !='0000-00-00'){echo date($this->customlib->getHospitalDateFormat(), $this->customlib->dateYYYYMMDDtoStrtotime($staff_value->date_of_joining));} ?></span></li><?php } ?>
                                        <?php if ($id_card[0]->enable_permanent_address == 1) { ?><li class="stred"><?php echo $this->lang->line('address'); ?><span><?php echo $staff_value->local_address; ?></span></li><?php } ?>
                                        <?php if ($id_card[0]->enable_staff_phone == 1) { ?><li><?php echo $this->lang->line('phone'); ?><span><?php echo $staff_value->contact_no; ?></span></li><?php } ?>
                                        <?php
                                        if ($id_card[0]->enable_staff_dob == 1) {
                                            ?>
                                            <li><?php echo $this->lang->line('date_of_birth'); ?>
                                                <span>
                                                    <?php
                                                    echo $dob = "";
                                                    if ($staff_value->dob != "0000-00-00") {
                                                        $dob = date($this->customlib->getHospitalDateFormat(), $this->customlib->dateYYYYMMDDtoStrtotime($staff_value->dob));
                                                    }
                                                    
                                                    echo $dob;
                                                    ?>
                                                </span></li>
                                            <?php
                                        }
                                        ?>
                                    </ul>
                                </div><!--./cardright-->
                            </div><!--./staround-->
                        </td>
                    </tr>
                    <tr>
                        <td valign="top" class="principal text-end">
                        <?php if(!empty($id_card[0]->sign_image)){ ?>
                        <img src="<?php echo $this->media_storage->getImageURL('uploads/staff_id_card/signature/' . $id_card[0]->sign_image); ?>" width="66" height="40" />
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