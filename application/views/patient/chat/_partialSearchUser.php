<?php
if (!empty($chat_user)) {
    echo "<ul class='list-group' id='contact-list'>";
    foreach ($chat_user as $user_key => $user_value) {
        ?>
        <li class="list-group-item" data-user-type="<?php echo ($user_value->patient_id == "") ? 'staff' : 'patient'; ?>" data-user-id="<?php echo ($user_value->patient_id == "") ? $user_value->staff_id : $user_value->patient_id; ?>">
            <div class="row align-items-center">
                <div class="col-auto">
                    <?php
                    if ($user_value->image == "") {
                        $img =  "uploads/staff_images/no_image.png";
                    } else {
                        $img = ($user_value->patient_id == "") ?  "uploads/staff_images/" . $user_value->image :  $user_value->image;
                    }
                    ?>
                    <img src="<?php echo $this->media_storage->getImageURL($img); ?>" alt="" class="img-fluid rounded-circle" style="width: 40px; height: 40px; object-fit: cover;">
                </div>
                <div class="col">
                    <span class="name d-block fw-bold"> 
                        <?php
                        if ($user_value->patient_id != "") {
                            echo composePatientName($user_value->first_name, $user_value->patient_id);
                        } else {
                            echo composeStaffNameByString($user_value->name, $user_value->surname, $user_value->employee_id);
                        }
                        ?>
                    </span>
                    <small class="text-muted">
                        <?php
                        if ($user_value->patient_id != "") {
                            echo "(" . $this->lang->line('patient') . ")";
                        } else {
                            echo "(" . $this->lang->line('staff') . ")";
                        }
                        ?>
                    </small>
                </div>
            </div>
        </li>
        <?php
    }
    echo "</ul>";
}
?>
