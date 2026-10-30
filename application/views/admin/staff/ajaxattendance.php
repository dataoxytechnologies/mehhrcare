        <?php
        if (!empty($resultlist)) {
            $j = 0;
            for ($i = 1; $i <= 31; $i++) {
                ?>
                <tr>
                    <td><?php echo $attendence_array[$j] ?></td>
                    <?php
                    foreach ($monthlist as $key => $value) {
                        $datemonth = date("m", strtotime($value));
                        $att_dates = $year . "-" . $datemonth . "-" . sprintf("%02d", $i);
                        ?>
                        <td><span data-bs-toggle="popover" class="detail_popover" title="" title=""><a href="#" class="text-body"><?php
                                    if (array_key_exists($att_dates, $resultlist)) {
                                        echo $resultlist[$att_dates]["key"];
                                    }
                                    ?></a></span>
                            <div class="fee_detail_popover d-none" ><?php echo $resultlist[$att_dates]["remark"]; ?></div>
                        </td>
                    <?php } ?>
                </tr>
                <?php
                $j++;
            }
            ?>
            <?php
        } else {
            echo "No Record Found";
        }
        ?>   
