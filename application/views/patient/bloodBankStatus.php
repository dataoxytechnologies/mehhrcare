<div class="container-fluid px-3 py-3">
    <div class="card" style="border:1px solid var(--border);background:var(--surface);">
        <div class="card-body p-3">
            <div class="download_label"><?php echo $this->lang->line('blood_bank') . ' ' . $this->lang->line('status'); ?></div>
            <div class="table-responsive">
                <table class="table table-striped table-bordered table-hover example">
                    <thead>
                        <tr>
                            <th><?php echo $this->lang->line('blood_group'); ?></th>
                            <th><?php echo $this->lang->line('status') . ' ' . $this->lang->line('in_bags'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($bloodGroup as $category) { ?>
                        <tr>
                            <td><?php echo html_escape($category['blood_group']); ?></td>
                            <td><?php echo html_escape($category['status']); ?></td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
