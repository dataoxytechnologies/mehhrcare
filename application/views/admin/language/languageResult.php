<?php
$count = 1;
$lang  = $this->db->select('lang_id')->from('sch_settings')->get()->row_array();
foreach ($languagelist as $language) {
    ?>
    <tr>
        <td><?php echo $count . "."; ?></td>
        <td><?php echo $language['language'] ?> <span class="flag-icon flag-icon-<?php echo $language['country_code']; ?>"></span></td>
        <td><?php echo $language['short_code']; ?></td>
        <td><?php echo $language['country_code']; ?></td>
        <td id="active<?php echo $language["id"]; ?>">
            <?php if ($lang['lang_id'] == $language['id']) { ?>
                <span class="badge bg-success"><?php echo $this->lang->line('active'); ?></span>
            <?php } ?>
        </td>
        <td class="text-center">
            <?php if (!empty($selected_lang) && in_array($language["id"], $selected_lang)) { ?>
                <div class="form-check d-inline-flex m-0">
                    <input class="form-check-input" type="radio" value="<?php echo $language["id"]; ?>" name="defoult" onclick="defoult(this.value)" <?php if ($lang['lang_id'] == $language['id']) { echo "checked"; } ?>>
                </div>
            <?php } ?>
        </td>
        <td class="text-center">
            <div class="form-check d-inline-flex m-0">
                <input class="form-check-input" type="checkbox" id="rtl_<?php echo $language["id"]; ?>" value="<?php echo $language["id"]; ?>" onchange="rtl(this.value)" <?php if ($language['is_rtl'] == 'yes') { echo "checked"; } ?>>
            </div>
        </td>
        <td class="text-end">
            <?php if ($language['is_deleted'] == 'yes' && $this->customlib->getSessionLanguage() != $language['id'] && $this->rbac->hasPrivilege('languages', 'can_delete')) { ?>
                <a onclick="return confirm('<?php echo $this->lang->line('delete_confirm') ?>');" href="<?php echo base_url(); ?>admin/language/delete/<?php echo $language['id']; ?>" class="btn btn-secondary btn-sm me-1" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('delete') ?>">
                    <i class="fa fa-remove"></i>
                </a>
            <?php } ?>
            <?php if ($this->customlib->getSessionLanguage() != $language['id'] && !empty($selected_lang)) { ?>
                <div class="form-check form-switch d-inline-flex m-0 align-middle">
                    <input class="form-check-input chk" type="checkbox" role="switch"
                           id="student<?php echo $language["id"] ?>"
                           data-role="<?php echo in_array($language["id"], $selected_lang) ? '1' : '2'; ?>"
                           data-rowid="<?php echo $language["id"] ?>"
                           <?php if (in_array($language["id"], $selected_lang)) echo 'checked'; ?> />
                </div>
            <?php } ?>
        </td>
    </tr>
    <?php
    $count++;
}
?>
