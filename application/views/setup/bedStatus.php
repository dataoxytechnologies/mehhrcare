<div class="row">
    <div class="col-md-2">
        <?php $this->load->view('setup/bedsidebar'); ?>
    </div>
    <div class="col-md-10">
        <!-- general form elements -->
        <?php if ($this->rbac->hasPrivilege('bed_status', 'can_view')) { ?>
        <div class="card">
            <div class="card-header">
                <h3 class="card-title titlefix"><?php echo $this->lang->line('bed_status'); ?></h3>
            </div>
            <div class="card-body">
                <div class="download_label"><?php echo $this->lang->line('bed_status'); ?></div>
                <div class="table-responsive mailbox-messages">
                    <table class="table table-hover table-striped table-bordered example">
                        <thead>
                            <tr>
                                <th><?php echo $this->lang->line('name'); ?></th>
                                <th><?php echo $this->lang->line('bed_type'); ?></th>
                                <th><?php echo $this->lang->line('bed_group'); ?></th>
                                <th><?php echo $this->lang->line('floor'); ?></th>
                                <th><?php echo $this->lang->line('status'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if (empty($bed_list)) {
                            } else {
                                foreach ($bed_list as $key => $value) {
                                    if ($value['is_active'] == 'no') {
                                        $color = "danger";
                                    } elseif ($value['is_active'] == 'yes') {
                                        $color = "success";
                                    } elseif ($value['is_active'] == 'unused') {
                                        $color = "";
                                    }
                                    ?>
                                    <tr class="<?php echo $color ?>">
                                        <td class="mailbox-name">
                                            <?php echo $value['name'] ?>
                                        </td>
                                        <td><?php echo $value['bed_type_name']; ?></td>
                                        <td><?php echo $value['bedgroup']; ?></td>
                                        <td><?php echo $value['floor_name']; ?></td>
                                        <td class="mailbox-name">
                                            <?php
                                            if ($value['is_active'] == 'yes') {
                                                echo $this->lang->line("available");
                                            } elseif ($value['is_active'] == 'no') {
                                                echo $this->lang->line("allotted");
                                            } elseif ($value['is_active'] == 'unused') {
                                                echo $this->lang->line("unused");
                                            }
                                            ?>
                                        </td>
                                    </tr>
                                    <?php
                                }
                            }
                            ?>
                        </tbody>
                    </table><!-- /.table -->
                </div><!-- /.mail-box-messages -->
            </div><!-- /.card-body -->
        </div>
        <?php } ?>
    </div><!--/.col (left) -->
    <!-- right column -->
</div>

<script>
    $(document).ready(function () {
        document.querySelectorAll('.detail_popover').forEach(function (el) {
            new bootstrap.Popover(el, {
                placement: 'right',
                trigger: 'hover',
                container: 'body',
                html: true,
                content: function () {
                    var td = el.closest('td');
                    var inner = td ? td.querySelector('.fee_detail_popover') : null;
                    return inner ? inner.innerHTML : '';
                }
            });
        });
    });
</script>
