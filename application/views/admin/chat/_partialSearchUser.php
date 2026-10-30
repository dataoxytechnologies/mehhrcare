<?php
if (!empty($chat_user)) {
    echo "<ul class='list-group' id='contact-list'>";
    foreach ($chat_user as $user_key => $user_value) {
        ?>
        <li class="list-group-item" data-user-type="<?php echo ($user_value->patient_id == "") ? 'Staff' : 'Patient'; ?>" data-user-id="<?php echo ($user_value->patient_id == "") ? $user_value->staff_id : $user_value->patient_id; ?>">
            <div class="col-2 col-sm-1">
                <?php
                if ($user_value->image == "") {
                    $img =  "uploads/staff_images/no_image.png";
                } else {
                    $img = ($user_value->patient_id == "") ?  "uploads/staff_images/" . $user_value->image :  $user_value->image;
                }
                ?>

                <img src="<?php echo $this->media_storage->getImageURL($img); ?>" alt="Glenda Patterson" class="img-fluid">
            </div>
            <div class="col-10 col-sm-9">
                <span class="name"> <?php
              
                    if ($user_value->patient_id != "") {
                        echo composePatientName($user_value->first_name,$user_value->patient_id);
                    } else {
                        echo composeStaffNameByString($user_value->name, $user_value->surname, $user_value->employee_id);
                    }
                    ?>
                        
                    </span>
                <br>

                <span>
                    <?php
                    if ($user_value->patient_id != "") {
                        echo "(" . $this->lang->line('patient') . ")";
                    } else {
                        echo "(" . $this->lang->line('staff') . ")";
                    }
                    ?>
                </span>
            </div>
            <div class="clearfix"></div>
        </li>
        <?php
    }
    echo "</ul>";
}
?>