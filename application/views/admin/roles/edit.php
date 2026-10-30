<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
?>
<div class="row">
            <?php $this->load->view('setting/sidebar'); ?>
            <div class="col-md-3">
                <div class="card" >
                    <div class="card-header">
                        <h3 class="card-title"><?php echo $this->lang->line('role'); ?></h3>
                    </div>
                    <form id="form1" action="<?php echo site_url('admin/roles/edit/' . $id) ?>" id="employeeform" name="employeeform" method="post" accept-charset="utf-8">
                        <div class="card-body">
                            <?php if ($this->session->flashdata('msg')) { ?>
                                <?php echo $this->session->flashdata('msg') ?>
                            <?php } ?>
                            <?php
                            if (isset($error_message)) {
                                echo "<div class='alert alert-danger'>" . $error_message . "</div>";
                            }
                            ?>      
                            <?php echo $this->customlib->getCSRF(); ?>                     
                            <div class="mb-3">
                                <label for="exampleInputEmail1"><?php echo $this->lang->line('name'); ?></label>
                                <input autofocus="" id="name" name="name" placeholder="" type="text" class="form-control" value="<?php
                                if (isset($name)) {
                                    echo $name;
                                }
                                ?>" />
                                <span class="text-danger"><?php echo form_error('name'); ?></span>
                            </div>

                            <div class="mb-3">
                                <input autofocus="" name="id" placeholder="" type="hidden" class="form-control" value="<?php
                                if (isset($id)) {
                                    echo $id;
                                }
                                ?>" />
                            </div>

                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-info float-end"><?php echo $this->lang->line('save'); ?></button>
                        </div>
                    </form>
                </div>
            </div>         
            <div class="col-md-7">
                <div class="card" id="route">
                    <div class="card-header ptbnull">
                        <h3 class="card-title titlefix"><?php echo $this->lang->line('role_list'); ?></h3>
                    </div>
                    <div class="card-body">
                        <div class="mailbox-controls">                         
                            <div class="float-end">
                            </div>
                        </div>
                        <div class="table-responsive mailbox-messages">
                            <div class="download_label"><?php echo $this->lang->line('role_list'); ?></div>
                            <table class="table table-striped table-bordered table-hover example">
                                <thead>
                                    <tr>
                                        <th><?php echo $this->lang->line('role'); ?>
                                        </th>
                                        <th><?php echo $this->lang->line('type'); ?>
                                        </th>
                                        <th class="text-end noExport"><?php echo $this->lang->line('action'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($listroute)) {
                                        ?>

                                        <?php
                                    } else {
                                        $count = 1;
                                        foreach ($listroute as $data) {
                                            ?>
                                            <tr>
                                                <td class="mailbox-name"><?php echo $data['name'] ?></td>
                                                <td class="mailbox-name"> 
                                                    <?php
                                                    if ($data['is_system']) {

                                                        echo $this->lang->line('system');
                                                    } else {
                                                        echo $this->lang->line('custom');
                                                    }
                                                    ?>
                                                </td>

                                                <td class="text-end text-nowrap no-print">
                                                    <?php
                                                    if (!$data['is_superadmin']) {
                                                        ?>
                                                        <a href="<?php echo site_url('admin/roles/permission/' . $data['id']); ?>" class="btn btn-secondary btn-sm" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('assign_permission'); ?>">
                                                            <i class="fa fa-tag"></i>
                                                        </a>

                                                        <a href="<?php echo site_url('admin/roles/edit/' . $data['id']); ?>" class="btn btn-secondary btn-sm" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('edit'); ?>">
                                                            <i class="fa fa-pencil"></i>
                                                        </a>
                                                        <?php
                                                        if (!$data['is_system']) {
                                                            ?>
                                                            <a class="btn btn-secondary btn-sm" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('delete'); ?>" onclick="delete_recordByIdReload('admin/roles/delete/<?php echo $data['id']?>')">
                                                                <i class="fa fa-remove"></i>
                                                            </a>
                                                            <?php
                                                        }
                                                    }
                                                    ?>
                                                </td>
                                            </tr>
                                            <?php
                                        }
                                        $count++;
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>          
        </div>