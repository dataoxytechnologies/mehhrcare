<div class="bb-panels">

    <!-- Blood Panel -->
    <div class="bb-panel">
        <div class="bb-panel-hdr">
            <div class="bb-panel-title"><i class="fa fa-droplet"></i> <?= $this->lang->line("blood"); ?></div>
            <div class="d-flex align-items-center gap-2">
                <div class="bb-badge"><span class="count"><?= count($blood_data) ?> <?= $this->lang->line("bags"); ?></span></div>
                <?php if($this->rbac->hasPrivilege('blood_stock', 'can_add')) { ?>
                <button type="button" class="btn-bb-add" onclick="bloodDetailsModal('<?= $blood_group_id; ?>')" title="<?= $this->lang->line('add') ?>"><i class="fa fa-plus fa-xs"></i></button>
                <?php } ?>
            </div>
        </div>
        <div class="bb-table-wrap">
            <table class="bb-table">
                <thead><tr>
                    <th><?=$this->lang->line("bags"); ?></th>
                    <th><?=$this->lang->line("lot"); ?></th>
                    <th><?=$this->lang->line("institution"); ?></th>
                    <th class="text-end"><?=$this->lang->line("action"); ?></th>
                </tr></thead>
                <tbody>
                    <?php foreach($blood_data as $value){ ?>
                    <tr>
                        <td><span class="bb-bags-val"><?= $this->customlib->bag_string($value["bag_no"],$value["volume"],$value["unit"]); ?></span></td>
                        <td><?= html_escape($value["lot"]); ?></td>
                        <td><?= html_escape($value["institution"]); ?></td>
                        <td class="text-end"><div class="white-space-nowrap"><?php if($this->rbac->hasPrivilege('blood_issue', 'can_add')) { ?><button type="button" onclick="bloodIssueModal(<?= (int)$value['product_id'] ?>,<?= (int)$value['bag_id'] ?>)" class="btn-issue"><?= $this->lang->line("issue"); ?></button><?php } ?></div></td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Components Panel -->
    <div class="bb-panel">
        <div class="bb-panel-hdr">
            <div class="bb-panel-title"><i class="fa fa-flask"></i> <?= $this->lang->line("components"); ?></div>
            <div class="d-flex align-items-center gap-2">
                <div class="bb-badge"><span class="count"><?= count($component_data) ?> <?= $this->lang->line("bags"); ?></span></div>
                <?php if($this->rbac->hasPrivilege('blood_bank_components', 'can_add')) { ?>
                <button type="button" class="btn-bb-add" onclick="componentDetailsModal(<?= (int)$blood_group_id ?>)" title="<?= $this->lang->line('add') ?>"><i class="fa fa-plus fa-xs"></i></button>
                <?php } ?>
            </div>
        </div>
        <div class="bb-table-wrap">
            <table class="bb-table">
                <thead><tr>
                    <th><?=$this->lang->line("bags"); ?></th>
                    <th><?=$this->lang->line("lot"); ?></th>
                    <th><?=$this->lang->line("components"); ?></th>
                    <th class="text-end"><?=$this->lang->line("action"); ?></th>
                </tr></thead>
                <tbody>
                    <?php foreach($component_data as $value){ ?>
                    <tr>
                        <td><span class="bb-bags-val"><?= $this->customlib->bag_string($value["bag_no"],$value["volume"],$value["unit"]); ?></span></td>
                        <td><?= html_escape($value["lot"]); ?></td>
                        <td><span class="bb-component-tag"><?= html_escape($value["name"]); ?></span></td>
                        <td class="text-end"><div class="white-space-nowrap"><?php if($this->rbac->hasPrivilege('issue_component', 'can_add')) { ?><button type="button" onclick="componentIssueModal(<?= (int)$value['product_id'] ?>,<?= (int)$value['id'] ?>,<?= (int)$value['bloodid']; ?>)" class="btn-issue"><?= $this->lang->line("issue"); ?></button><?php } ?></div></td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

</div><!-- /.bb-panels -->
