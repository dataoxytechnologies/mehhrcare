
<?php $dummy_date = "2020-01-01"; ?>
        <table cellpadding="0" cellspacing="0" width="100%">
            <tr> 
                <td valign="top" width="32%" class="sh-idcard-outer-td">
                    <table cellpadding="0" cellspacing="0" width="100%" class="tc-container sh-idcard-card-bg sh-idcard-print" style="--idcard-header: <?php echo htmlspecialchars($idcard->header_color); ?>">
                        <tr>
                            <td valign="top">
                                <?php if($idcard->background != ''){ ?>
                                <img src="<?php echo $this->media_storage->getImageURL('uploads/staff_id_card/background/'.$idcard->background) ?>" class="tcmybg" />
                                <?php } ?>
                            </td>
                        </tr>
                        <tr>
                            <td valign="top">
                                <div class="patienttop">
                                    <div class="sttext1">
                                    <?php if($idcard->logo != ''){ ?>
                                    <img src="<?php echo $this->media_storage->getImageURL('uploads/staff_id_card/logo/'.$idcard->logo) ?>"  />
                                    <?php } ?>
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
                                    if ($idcard->enable_staff_barcode) {
                                        if($scan_type == "qrcode"){ ?>                                        
                                        <?php if (file_exists("./uploads/staff_id_card/qrcode/default.png")) { ?>
                                            <div class="barcodeimg">
                                                <img src="<?php echo $this->media_storage->getImageURL('./uploads/staff_id_card/qrcode/default.png');?>" />
                                            </div>
                                    <?php  }   ?>
                                    <?php
                                    }elseif ($scan_type == "barcode") { ?>                                        
                                    <?php if (file_exists("./uploads/staff_id_card/barcodes/default.png")) {
                                     ?>
                                     <div class="barcodeimg text-center">
                                         <img src="<?php echo $this->media_storage->getImageURL('./uploads/staff_id_card/barcodes/default.png');?>" />
                                     </div>
                                    <?php  }  } } ?>
                                    </div><!--./cardleft-->
                                    <div class="cardright">
                                        <ul class="stlist">
                                            <?php
                                            if ($idcard->enable_name == 1) {
                                                echo "<li>"; echo $this->lang->line('staff_name'); echo "<span>Mohan Patil</span></li>";
                                            } 
                                            ?>
                                            <?php
                                            if ($idcard->enable_staff_id == 1) {
                                                echo "<li>"; echo $this->lang->line('staff_id');echo "<span>9000</span></li>";
                                            }
                                            ?>
                                            <?php
                                            if ($idcard->enable_designation == 1) {
                                                echo "<li>"; echo $this->lang->line('designation');echo "<span>Administator</span></li>";
                                            }
                                            ?>
                                            <?php
                                            if ($idcard->enable_staff_department == 1) {
                                                 echo "<li>"; echo $this->lang->line('department');echo "<span>Admin</span></li>";
                                            }
                                            ?>
                                            <?php
                                            if ($idcard->enable_fathers_name == 1) {
                                                echo "<li>"; echo $this->lang->line('father_name'); echo "<span>Sohan Patil</span></li>";
                                            }
                                            ?>
                                            <?php
                                            if ($idcard->enable_mothers_name == 1) {
                                                echo "<li>"; echo $this->lang->line('mother_name'); echo "<span>Kirti Patil</span></li>";
                                            }
                                            ?>
                                            <?php
                                            if ($idcard->enable_date_of_joining == 1) {
                                                echo "<li>"; echo $this->lang->line('date_of_joining'); echo "<span>"; echo $this->customlib->YYYYMMDDTodateFormat($dummy_date); echo "</span></li>";
                                            }
                                            ?>
                                            <?php
                                            if ($idcard->enable_permanent_address == 1) {
                                                echo "<li>"; echo $this->lang->line('address'); echo "<span>Near Railway Station Jabalpur</span></li>";
                                            }
                                            ?>
                                             <?php
                                            if ($idcard->enable_staff_phone == 1) {
                                                echo "<li>"; echo $this->lang->line('phone'); echo "<span>9845624781</span></li>";
                                            }
                                            ?>
                                            <?php
                                            if ($idcard->enable_staff_dob == 1) {
                                                echo "<li>"; echo $this->lang->line('date_of_birth'); echo "<span>"; echo $this->customlib->YYYYMMDDTodateFormat($dummy_date); echo "</span></li>";
                                            }
                                            ?>
                                        </ul>
                                    </div><!--./cardright-->
                                </div><!--./staround-->
                            </td>
                        </tr>
                        <tr>
                            <td valign="top" class="principal text-end">
                            <?php if($idcard->sign_image != ''){ ?>
                            <img src="<?php echo $this->media_storage->getImageURL('uploads/staff_id_card/signature/'.$idcard->sign_image) ?>" width="66" height="40" />
                            <?php } ?>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>  
        </table>