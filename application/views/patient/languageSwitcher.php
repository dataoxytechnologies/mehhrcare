<?php
$lang        = $this->setting_model->get();
$defoult     = $lang[0]['lang_id'];
$session     = $this->session->userdata('patient');
$id          = $session['patient_id'];
$defoultlang = $this->setting_model->get_patientlang($id);
if (!empty($defoultlang)) {
    if ($defoultlang['lang_id'] != 0) {
        $defoult = $defoultlang['lang_id'];
    } else {
        $defoult = $lang[0]['lang_id'];
    }
}
$json_languages = json_decode($lang[0]['languages']);
foreach ($json_languages as $value) {
    $result = $this->db->select('language, country_code')->from('languages')->where('id', $value)->get()->row_array();
    if (empty($result)) continue;
    $active = ($defoult == $value) ? ' active' : '';
?>
<li>
    <a class="dropdown-item<?php echo $active; ?>" href="javascript:void(0)" onclick="set_patient_language(<?php echo (int)$value; ?>)">
        <img src="https://flagcdn.com/w20/<?php echo htmlspecialchars(strtolower($result['country_code'])); ?>.png"
             width="16" height="12" class="me-2"
             onerror="this.style.display='none'">
        <?php echo html_escape($result['language']); ?>
        <?php if ($active): ?>
            <i class="fa fa-check ms-2 float-end" style="font-size:11px;margin-top:3px;"></i>
        <?php endif; ?>
    </a>
</li>
<?php } ?>
