<div class="row">   
            <div class="col-md-12">
                <div class="card">
                <?php $this->load->view('admin/report/_human_resource');?>
                <div class="card-header ptbnull"></div>
                    <div class="card-header">
                        <h3 class="card-title"><?php echo $this->lang->line('staff_day_wise_attendance_report'); ?> </h3>
                    </div>
                    <form id='form1' action="<?php echo site_url('admin/staffattendance/staffdaywiseattendancereport') ?>" method="post" accept-charset="utf-8">
                        <div class="card-body pb-0">
                            <?php echo $this->customlib->getCSRF(); ?>
                            <div class="row">
                                <div class="col-sm-6 col-md-3">
                                    <div class="mb-3">
                                        <label><?php echo $this->lang->line('role'); ?></label>
                                        <select  id="role" name="role" class="form-control" >
                                            <option value="select"><?php echo $this->lang->line('select'); ?></option>
                                            <?php
                                            foreach ($role as $role_key => $value) {
                                                ?>
                                                <option value="<?php echo $value["type"] ?>" <?php
                                                if ($role_selected == $value["type"]) {
                                                    echo "selected =selected";
                                                }
                                                ?>><?php echo $value["type"]; ?></option>
                                                        <?php
                                                        $count++;
                                                    }
                                                    ?>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('role'); ?></span>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-md-3">
                                    <div class="mb-3">
										<label for="exampleInputEmail1"><?php echo $this->lang->line('date'); ?></label><small class="req"> *</small>

                                        <input name="date" placeholder="" type="text" class="form-control date" value="<?php echo set_value('date', date($this->customlib->getHospitalDateFormat())); ?>" readonly="readonly" />
                                        <span class="text-danger"><?php echo form_error('date'); ?></span>						
                                        
                                    </div>
                                </div>
                                <div class="col-sm-6 col-md-3">
                                    <div class="mb-3">
                                        <label for="exampleInputEmail1"> <?php echo $this->lang->line('source'); ?></label>
                                        <select id="attendance_mode" name="attendance_mode" class="form-control">
                                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                                            <option value="1" <?php echo set_select('attendance_mode', 1, (set_value('attendance_mode') == 1) ? TRUE : FALSE); ?>><?php echo $this->lang->line('manual'); ?></option>
                                            <option value="2" <?php echo set_select('attendance_mode', 2, (set_value('attendance_mode') == 2) ? TRUE : FALSE); ?>><?php echo $this->lang->line('qrcode') . " / " . $this->lang->line('barcode'); ?></option>
                                            <option value="3" <?php echo set_select('attendance_mode', 3, (set_value('attendance_mode') == 3) ? TRUE : FALSE); ?>><?php echo $this->lang->line('biometric'); ?></option>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('attendance_mode'); ?></span>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-md-3 d-flex align-items-end">
                                    <div class="mb-3">
                                        <button type="submit" name="search" value="search" class="btn btn-primary d-inline-flex align-items-center gap-1 py-2 checkbox-toggle">
                                            <i class="fa fa-search"></i>
                                            <?php echo $this->lang->line('search'); ?>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div> 
                    </form>
                    
                        <div id="attendencelist">
                            <div class="table-responsive card-body pt-0">
                                    <div class="download_label"><?php echo $this->lang->line('staff_day_wise_attendance_report'); ?></div>
                                    <table class="table table-hover table-striped example">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th><?php echo $this->lang->line('staff_id'); ?></th>
                                                <th><?php echo $this->lang->line('role'); ?></th>
                                                <th><?php echo $this->lang->line('name'); ?></th>
                                                <th width="10%" class="text text-center"><?php echo $this->lang->line('attendance'); ?></th>
                                                <?php if ($sch_setting->biometric) { ?>
                                                <th><?php echo $this->lang->line('date'); ?></th>
                                                <?php } ?>
                                                <th width="10%"><?php echo $this->lang->line('source'); ?></th>
                                                <?php if ($sch_setting->biometric) { ?>
                                                <th><?php echo $this->lang->line('ip_address'); ?></th>
                                                <th><?php echo $this->lang->line('agent'); ?></th>
                                                <th><?php echo $this->lang->line('scan_location'); ?></th>
                                                <?php } ?>
                                            </tr>
                                        </thead>
                                        <?php
                                        if (isset($resultlist)) {
                                        if (!empty($resultlist)) {
                                        ?>
                                        <tbody>
                                            <?php
                                            $row_count = 1;
                                            foreach ($resultlist as $key => $value) {
                                            ?>
                                            <tr>
                                                <td><?php echo $row_count; ?></td>
                                                <td><?php echo $value['employee_id']; ?></td>
                                                <td><?php echo $value['user_type']; ?></td>
                                                <td><?php echo $value['name'] . " " . $value['surname']; ?></td>
                                                <td class="text text-center">
													<?php
                                                        if (IsNullOrEmptyString($value['staff_attendance_type_id'])) {
                                                    ?>
                                                    <span class="badge bg-danger"><?php echo $this->lang->line('n_a'); ?> </span>
                                                    <?php
                                                        } else {
                                                    ?>
                                                    <span class="<?php echo $value['long_name_style']; ?>"><?php echo $this->lang->line(strtolower($value['long_lang_name'])); ?></span>
                                                    <?php
                                                        }
                                                    ?>
                                                </td>
                                                <?php
                                                if ($sch_setting->biometric) {
                                                ?>
                                                <td>
                                                    <?php
                                                    if ($value['biometric_attendence'] || $value['qrcode_attendance']) {
                                                        echo $this->customlib->dateyyyymmddToDateTimeformat($value['attendence_dt']);
                                                    }
                                                    ?>
                                                </td>
                                                <?php
                                                }
                                                ?>
                                                <td>
                                                    <?php
                                                    if (IsNullOrEmptyString($value['biometric_attendence']) && IsNullOrEmptyString($value['qrcode_attendance'])) {
                                                        echo $this->lang->line('n_a');
                                                    } elseif (($value['biometric_attendence'] == 0) && ($value['qrcode_attendance']  == 0)) {
                                                        echo $this->lang->line('manual');
                                                    } elseif ($value['biometric_attendence']) {
                                                        echo $this->lang->line('biometric');
                                                    } elseif ($value['qrcode_attendance']) {
                                                        echo $this->lang->line('qrcode') . " / " . $this->lang->line('barcode');
                                                    }
                                                    ?>
                                                </td>
                                                <?php
                                                if ($sch_setting->biometric) {
                                                ?>
                                                <td>
                                                    <?php
                                                    if ($value['biometric_attendence'] || $value['qrcode_attendance']) {
                                                        if (isJSON($value['biometric_device_data'])) {
                                                            $json_data = json_decode($value['biometric_device_data']);
                                                            echo ($json_data->ip);
                                                        }
                                                    }
                                                    ?>
                                                </td>
                                                <td class="text-rtl-left"><?php echo $value['user_agent'];; ?></td>
                                                <td class="text-rtl-left">
													<?php
                                                        if (isJSON($value['biometric_device_data'])) {
														
                                                            $json_data = json_decode($value['biometric_device_data']);
                                                            echo $json_data->country."/ ". $json_data->region."/ ". $json_data->city;
                                                            echo "<br/>";
                                                            echo    "<a target='_blank' href='http://maps.google.com/?q=". $json_data->latitude.", ". $json_data->longitude."'>".$json_data->latitude.", ". $json_data->longitude."</a>";
                                                                           
                                                        }
                                                    ?>
												</td>
                                                <?php
                                                }
                                                ?>
											</tr>
                                                <?php
                                                    $row_count++;
                                                }
                                                ?>
                                        </tbody>
                                        <?php
                                    } } ?>
                                    </table>
                            </div>
                        </div>
					</div>
            <script type="text/javascript">
                $(document).ready(function () {
                    var date_format = '<?php echo $result = strtr($this->customlib->getHospitalDateFormat(), ['d' => 'dd', 'm' => 'mm', 'Y' => 'yyyy',]) ?>';
                    /* #date init removed - auto-init via class + event delegation */

                    $('.detail_popover').popover({
                        placement: 'right',
                        title: '',
                        trigger: 'hover',
                        container: 'body',
                        html: true,
                        content: function () {
                            return $(this).closest('th').find('.fee_detail_popover').html();
                        }
                    });
                });
            </script>
			
            <script type="text/javascript">
                var base_url = '<?php echo base_url() ?>';
                function printDiv(elem) {
                    popup(jQuery(elem).html());
                }               
            </script>