
<table cellpadding="0" cellspacing="0" width="100%">
    <tr> 
        <td valign="top" width="32%" class="sh-idcard-outer-td">
        <table cellpadding="0" cellspacing="0" width="100%" class="tc-container sh-idcard-card-bg sh-idcard-print" style="--idcard-header: <?php echo htmlspecialchars($idcard->header_color); ?>">
            <tr>
                <td valign="top">
                    <?php if($idcard->background != ''){ ?>
                    <img src="<?php echo $this->media_storage->getImageURL('uploads/patient_id_card/background/'.$idcard->background); ?>" class="tcmybg" />
                    <?php } ?>
                </td>
            </tr>
            <tr>
                <td valign="top">
                    <div class="patienttop">
                        <div class="sttext1">
                        <?php if($idcard->logo != ''){ ?>
                        <img src="<?php echo $this->media_storage->getImageURL('uploads/patient_id_card/logo/'.$idcard->logo) ?><?php echo $idcard->logo; ?>"  /><?php } ?>
                            <?php echo $idcard->hospital_name; ?></div>
                    </div>
                </td>
            </tr>
            <tr>
                <td valign="top" class="text-center sh-idcard-addr-td">
                    <p><?php echo $idcard->hospital_address; ?></p>
                </td>
            </tr>
            <tr>
                <td valign="top" class="sh-idcard-title-td"><?php echo $idcard->title; ?></td>
            </tr>
                        <tr>
                            <td valign="top">
                                <div class="staround">
                                    <div class="cardleft">
                                        <div class="stimg">
                                            <img src="<?php echo $this->media_storage->getImageURL('uploads/patient_images/no_image.png') ?>" class="img-fluid" />
                                        </div>
                                    <?php
                                    if ($idcard->enable_barcode) {
                                        if($scan_type == "qrcode"){ ?>
                                        <?php if (file_exists("./uploads/patient_id_card/qrcode/default.png")) { ?>
                                            <div class="barcodeimg">
                                                <img src="<?php echo $this->media_storage->getImageURL('./uploads/patient_id_card/qrcode/default.png');?>"  />
                                            </div>
                                    <?php  }   ?>
                                    <?php
                                    } else { ?>
                                    <?php if (file_exists("./uploads/staff_id_card/barcodes/default.png")) { ?>
                                     <div class="barcodeimg text-center">
                                         <img src="<?php echo $this->media_storage->getImageURL('./uploads/staff_id_card/barcodes/default.png');?>" />
                                     </div>
                                    <?php  }  } } ?>

                                    </div><!--./cardleft-->
                                    <div class="cardright">
                                        <ul class="stlist">
                                            <?php
                                            if ($idcard->enable_patient_name == 1) {
                                                echo "<li>".$this->lang->line('patient_name')."<span> James Bond</span></li>";
                                            }
                                            ?>
                                            <?php
                                            if ($idcard->enable_guardian_name == 1) {
                                                echo "<li>".$this->lang->line('guardian_name')."<span> Guardian Name</span></li>";
                                            }
                                            ?>
                                            <?php
                                            if ($idcard->enable_patient_unique_id == 1) {
                                                echo "<li>".$this->lang->line('patient_unique_id')."<span> 1001</span></li>";
                                            }
                                            ?>
                                            <?php
                                            if ($idcard->enable_address == 1) {
                                                echo "<li>".$this->lang->line('address')."<span>D.No.1 Street Name Address Line 2 Address Line 3</span></li>";
                                            }
                                            ?>
                                            <?php
                                            if ($idcard->enable_phone == 1) {
                                                echo "<li>".$this->lang->line('phone')."<span>1234567890</span></li>";
                                            }
                                            ?>
                                            <?php
                                            if ($idcard->enable_dob == 1) {
                                                echo "<li>".$this->lang->line('dbo')."<span>25.06.2006</span></li>";
                                            }
                                            ?>
                                            <?php
                                            if ($idcard->enable_blood_group == 1) {
                                                echo "<li class='stred'>".$this->lang->line('blood_group')."<span>A+</span></li>";
                                            }
                                            ?>

                                        </ul>
                                    </div><!--./cardright-->
                                </div><!--./staround-->
                            </td>
                        </tr>
                <tr>
					<td valign="top" class="principal text-end">
                        <?php if(!empty($idcard->sign_image)){ ?>
                        <img src="<?php echo $this->media_storage->getImageURL('uploads/patient_id_card/signature/'.$idcard->sign_image) ?>" width="66" height="40" />
                        <?php } ?>
                    </td>
                </tr>
            </table>
        </td>
    </tr>  
</table>