<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<style>
    .patientflow-setting-tabs { list-style: none; margin: 0; padding: 0; display: block; }
    .patientflow-setting-tabs > li { display: block; float: none; margin: 0; }
    .patientflow-setting-tabs > li > a { display: block; padding: 10px 15px; border-radius: 4px; color: #333; text-decoration: none; }
    .patientflow-setting-tabs > li > a:hover { background: #f5f5f5; }
    .patientflow-setting-tabs > li > h2 { display: block; padding: 0px 4px; border-radius: 4px; color: #333; text-decoration: none; margin: 0px; }
    .patientflow-setting-tabs > li.active > a,
    .patientflow-setting-tabs > li.active > a:hover { background: #e8eef5; color: #1a73e8; font-weight: 600; }
</style>
<div class="row">
    <div class="col-md-3">
        <div class="card"><div class="card-body" style="padding:8px;">
            <ul class="patientflow-setting-tabs" role="tablist">
                <li class="mt-2"><h2><i class="fa fa-cog"></i> Master Settings</h2></li><hr>
                <?php foreach ($groups as $key => $label) { ?>
                <li class="<?php echo $active_group === $key ? 'active' : ''; ?>">
                    <a href="<?php echo site_url('admin/patientflowsetting/setting?group=' . $key); ?>" data-group="<?php echo $key; ?>"><?php echo $label; ?></a>
                </li>
                <?php } ?>
            </ul>
        </div></div>
    </div>
    <div class="col-md-9">
        <?php
        $sub_data = array('groups' => $groups, 'active_group' => $active_group);
        if (isset($settings)) { $sub_data['settings'] = $settings; }
        if (isset($constitutions)) { $sub_data['constitutions'] = $constitutions; }
        if (isset($body_builds)) { $sub_data['body_builds'] = $body_builds; }
        if (isset($face_shapes)) { $sub_data['face_shapes'] = $face_shapes; }
        if (isset($nose_types)) { $sub_data['nose_types'] = $nose_types; }
        if (isset($programmes)) { $sub_data['programmes'] = $programmes; }
        if (isset($subcategories)) { $sub_data['subcategories'] = $subcategories; }
        if (isset($stages)) { $sub_data['stages'] = $stages; }
        $this->load->view('admin/patientflow/setting/' . $active_group, $sub_data);
        ?>
    </div>
    <div class="clearfix"></div>
</div>
