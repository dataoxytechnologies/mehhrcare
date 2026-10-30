<?php $uri =  $this->uri->segment(5); 
if($uri){
    $uri = $uri ;
}else{
    $uri = 0 ;
}
?>
<?php  if ($this->session->flashdata('msg')) { ?> <div>  <?php echo $this->session->flashdata('msg') ?> </div> <?php $this->session->unset_userdata('msg'); }   ?>
        <div class="row">
            <div class="col-md-3">
                <div class="card border0">
                    <ul class="tablists nav flex-column" role="tablist">
                    <?php
                        $count_tab = 0;
                        foreach ($role_permission as $key => $value) {
                            $isActive = ($count_tab == $open_tab);
                    ?>
                        <li class="<?php echo $isActive ? 'active' : ''; ?>" role="presentation">
                            <a onclick="activeurl(<?php echo $count_tab; ?>,'<?php echo $value->name; ?>')"
                               class="tab <?php echo $isActive ? 'active' : ''; ?>"
                               id="tab_<?php echo $count_tab; ?>"
                               href="#tab<?php echo $count_tab; ?>"
                               data-bs-toggle="pill"
                               data-bs-target="#tab<?php echo $count_tab; ?>"
                               data-act-tab="<?php echo $count_tab; ?>"
                               role="tab"
                               aria-controls="tab<?php echo $count_tab; ?>"
                               aria-selected="<?php echo $isActive ? 'true' : 'false'; ?>"><?php echo $value->name; ?></a>
                        </li>
                    <?php
                            $count_tab++;
                        }
                    ?>
                    </ul>
                </div>
            </div>            
            <div class="col-md-9">
                <div class="card">
                    <div class="card-header ptbnull">
                        <h5 class="card-title mb-0">
                            <span id="permissionname">
                                <?php
                                $count_tab = 0;
                                foreach ($role_permission as $key => $value) {
                                    if ($count_tab == $open_tab) {
                                        echo $value->name . " Permissions";
                                    }
                                    $count_tab++;
                                }
                                ?>
                            </span>
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="modal_loader_div" style="display: none;"></div>
                        <div class="tab-content">
                            <?php echo $this->customlib->getCSRF(); ?>
                            <?php
                            $tab_content_counter = 0;
                            for ($i = 0; $i < count($role_permission); $i++) {
                                $value = $role_permission[$i];
                                ?>
                                <div class="tab-pane <?php echo ($tab_content_counter == $open_tab) ? "active" : ""; ?>" id="tab<?php echo $tab_content_counter; ?>">
                                    <input type="hidden" name="open_tab" id="open_tab" class="open_tab" value="<?php echo $uri; ?>" />
                                    <input type="hidden" name="role_id" value="<?php echo $role['id'] ?>" />
                                    <div class="table-responsive">
                                        <table class="table table-striped table-hover align-middle mb-0">
                                            <thead>
                                                <tr>
                                                    <th><?php echo $this->lang->line('feature'); ?></th>
                                                    <th class="text-center sh-th-90"><?php echo $this->lang->line('view'); ?></th>
                                                    <th class="text-center sh-th-90"><?php echo $this->lang->line('add'); ?></th>
                                                    <th class="text-center sh-th-90"><?php echo $this->lang->line('edit'); ?></th>
                                                    <th class="text-center sh-th-90"><?php echo $this->lang->line('delete'); ?></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (!empty($value->permission_category)) {
                                                    $first = $value->permission_category[0];
                                                    ?>
                                                    <tr>
                                                        <td>
                                                            <input type="hidden" name="per_cat[]" value="<?php echo $first->id; ?>" />
                                                            <input type="hidden" name="<?php echo "roles_permissions_id_" . $first->id; ?>" value="<?php echo $first->roles_permissions_id; ?>" />
                                                            <?php echo $first->name; ?>
                                                        </td>
                                                        <?php foreach (['can_view' => 'enable_view', 'can_add' => 'enable_add', 'can_edit' => 'enable_edit', 'can_delete' => 'enable_delete'] as $action => $enableKey) { ?>
                                                            <td class="text-center">
                                                                <?php if ($first->$enableKey == 1) { ?>
                                                                    <div class="form-check form-switch d-inline-flex m-0">
                                                                        <input class="form-check-input permission_chk" type="checkbox" role="switch"
                                                                               data-action="<?php echo $action; ?>"
                                                                               data-role_id="<?php echo $role['id'] ?>"
                                                                               data-per_cat="<?php echo $first->id; ?>"
                                                                               name="<?php echo $action . "-perm_" . $first->id; ?>"
                                                                               value="<?php echo $first->id; ?>"
                                                                               <?php echo ($first->$action == 1) ? 'checked' : ''; ?>>
                                                                    </div>
                                                                <?php } ?>
                                                            </td>
                                                        <?php } ?>
                                                    </tr>
                                                    <?php
                                                } else {
                                                    ?>
                                                    <tr><td colspan="5"></td></tr>
                                                    <?php
                                                } ?>
                                                <?php if (!empty($value->permission_category) && count($value->permission_category) > 1) {
                                                    unset($value->permission_category[0]);
                                                    foreach ($value->permission_category as $new_feature_value) { ?>
                                                        <tr>
                                                            <td>
                                                                <input type="hidden" name="per_cat[]" value="<?php echo $new_feature_value->id; ?>" />
                                                                <input type="hidden" name="<?php echo "roles_permissions_id_" . $new_feature_value->id; ?>" value="<?php echo $new_feature_value->roles_permissions_id; ?>" />
                                                                <?php echo $new_feature_value->name; ?>
                                                            </td>
                                                            <?php foreach (['can_view' => 'enable_view', 'can_add' => 'enable_add', 'can_edit' => 'enable_edit', 'can_delete' => 'enable_delete'] as $action => $enableKey) { ?>
                                                                <td class="text-center">
                                                                    <?php if ($new_feature_value->$enableKey == 1) { ?>
                                                                        <div class="form-check form-switch d-inline-flex m-0">
                                                                            <input class="form-check-input permission_chk" type="checkbox" role="switch"
                                                                                   data-action="<?php echo $action; ?>"
                                                                                   data-role_id="<?php echo $role['id'] ?>"
                                                                                   data-per_cat="<?php echo $new_feature_value->id; ?>"
                                                                                   name="<?php echo $action . "-perm_" . $new_feature_value->id; ?>"
                                                                                   value="<?php echo $new_feature_value->id; ?>"
                                                                                   <?php echo ($new_feature_value->$action == 1) ? 'checked' : ''; ?>>
                                                                        </div>
                                                                    <?php } ?>
                                                                </td>
                                                            <?php } ?>
                                                        </tr>
                                                    <?php }
                                                } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <?php
                                $tab_content_counter++;
                            } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

<script type="text/javascript">
(function () {
    $('#tab_'+<?php echo $uri; ?>).addClass('active');    
})();
</script>
<script type="text/javascript">
    function activeurl(tab,name){     
            $('.open_tab').val(tab);           
            $(".tab").removeClass("active");
            $('#tab_'+tab).addClass('active');
            $('#permissionname').html(name+' Permissions'); 
    }
</script >  
<script >
	$(document).ready(function() {
        $(document).on('change', '.permission_chk', function() {
        let add_remove=0;
            if ($(this).is(':checked')) {
                add_remove=1;
            } else {
                add_remove=0;
            }

            $.ajax({
                type: "POST",
                url: base_url + "admin/roles/savecheck",
                data: {
                    'action': $(this).data('action'),
                    'per_cat': $(this).data('per_cat'),
                    'role_id': $(this).data('role_id'),
                    'add_remove': add_remove,
                },
                dataType: "json",
                beforeSend: function() {
                    $('.modal_loader_div').css("display", "block");
                },
                success: function(data)   {
                        if (data.status == 0) {
                            var message = "";
                            $.each(data.error, function (index, value) {

                                message += value;
                            });
                            errorMsg(message);
                        } else {
                            successMsg(data.message);
                        }
                        $('.modal_loader_div').fadeOut(400);
                       
                    },
                     error: function(xhr) { // if error occured
                       alert("<?php echo $this->lang->line('error_occured_please_try_again'); ?>");
                        $('.modal_loader_div').fadeOut(400);
                       
                     },
                    complete: function() {
                    $('#section_id').removeClass('dropdownloading');
                    $('.modal_loader_div').fadeOut(400);
                }
            });

        });
    });
</script>