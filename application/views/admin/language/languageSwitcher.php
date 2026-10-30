<?php
$lang           = $this->setting_model->get();
$defoult        = $lang[0]['lang_id'];
$session        = $this->session->userdata('hospitaladmin');
$id             = $session['id'];
$defoultlang    = $this->setting_model->get_stafflang($id);

if (!empty($defoultlang) && $defoultlang['lang_id'] != 0) {
    $defoult = $defoultlang['lang_id'];
}

$json_languages = json_decode($lang[0]['languages']);

foreach ($json_languages as $value) {
    $result   = $this->db->select('language, country_code')
                         ->from('languages')
                         ->where('id', $value)
                         ->get()->row_array();
    if (empty($result)) continue;
    $active   = ($defoult == $value) ? ' active' : '';
?>
<?php $cc = !empty($result['country_code']) ? strtolower($result['country_code']) : ''; ?>
<li>
    <a class="dropdown-item<?php echo $active; ?>"
       href="<?php echo base_url('admin/language/user_language/' . (int)$value); ?>">
        <?php if ($cc): ?>
            <img src="https://flagcdn.com/w20/<?php echo htmlspecialchars($cc); ?>.png"
                 srcset="https://flagcdn.com/w40/<?php echo htmlspecialchars($cc); ?>.png 2x"
                 width="20" height="15" alt="" loading="lazy" class="sh-lang-flag me-2">
        <?php else: ?>
            <i class="fas fa-globe me-2 text-muted sh-lang-flag-ph"></i>
        <?php endif; ?>
        <?php echo html_escape($result['language']); ?>
        <?php if ($active): ?>
            <i class="fas fa-check ms-2 float-end sh-fs-11" style="margin-top:3px;"></i>
        <?php endif; ?>
    </a>
</li>
<?php } ?>
