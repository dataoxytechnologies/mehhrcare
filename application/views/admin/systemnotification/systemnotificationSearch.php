<div class="row">          
            <div class="col-md-12"> 
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fa fa-search"></i> <?php echo $this->lang->line('select_criteria'); ?></h3>
                    </div>
                    <div class="card-body">
                        <div class="">
                            <div class="col-md-6">
                                <form role="form" action="<?php echo site_url('admin/expense/expenseSearch') ?>" method="post" class="form-horizontal">
                                    <?php echo $this->customlib->getCSRF(); ?>
                                    <div class="mb-3">
                                        <div class="col-sm-6">
                                            <label><?php echo $this->lang->line('date_from'); ?></label>
                                            <input autofocus="" id="datefrom"  name="date_from" placeholder="" type="text" class="form-control date" value="<?php echo set_value('date_from', date($this->customlib->getHospitalDateFormat())); ?>" readonly="readonly"/>
                                            <span class="text-danger"><?php echo form_error('class_id'); ?></span>
                                        </div>
                                        <div class="col-sm-6">
                                            <label><?php echo $this->lang->line('date_to'); ?></label>
                                            <input id="dateto" name="date_to" placeholder="" type="text" class="form-control date" value="<?php echo set_value('date_to', date($this->customlib->getHospitalDateFormat())); ?>" readonly="readonly"/>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <div class="col-sm-6">
                                            <button type="submit" name="search" value="search_filter" class="btn btn-primary btn-sm checkbox-toggle"><i class="fa fa-search"></i> <?php echo $this->lang->line('search'); ?></button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="col-md-6">
                                <form role="form" action="<?php echo site_url('admin/expense/expenseSearch') ?>" method="post" class="form-horizontal">
                                    <?php echo $this->customlib->getCSRF(); ?>
                                    <div class="mb-3">
                                        <div class="col-sm-12">
                                            <label><?php echo $this->lang->line('search'); ?></label>
                                            <input type="text" value="<?php echo set_value('search_text', ""); ?>" name="search_text"  class="form-control" placeholder="<?= $this->lang->line('search_by_exp') ?>">
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <div class="col-sm-6">
                                            <button type="submit" name="search" value="search_full" class="btn btn-primary btn-sm checkbox-toggle"><i class="fa fa-search"></i> <?php echo $this->lang->line('search'); ?></button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <?php if (isset($resultList)) {
                    ?><div class="card">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fa fa-money"></i> <?php echo $exp_title; ?></h3>
                            <div class="d-flex gap-2 align-items-center flex-wrap float-end">
                            </div>
                        </div>
                        <div class="card-body table-responsive no-padding">
                            <?php
                            if (empty($resultList)) {
                                ?>
                                <div class="col-md-12">
                                    <div class="alert alert-danger">
                                        <?php echo $this->lang->line('no_record_found'); ?>
                                    </div>
                                </div>
                                <?php
                            } else {
                                ?>
                                <table class="table table-hover table-striped">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th><?php echo $this->lang->line('date'); ?></th>
                                            <th><?php echo $this->lang->line('name'); ?></th>                                 
                                            <th><?php echo $this->lang->line('amount'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $count = 1;
                                        $grand_total = 0;
                                        foreach ($resultList as $key => $value) {
                                            $grand_total = $grand_total + $value['amount'];
                                            ?>
                                            <tr>
                                                <td>
                                                    <?php echo $count; ?>
                                                </td>
                                                <td>
                                                    <?php echo date($this->customlib->getHospitalDateFormat(), $this->customlib->dateyyyymmddTodateformat($value['date'])); ?>
                                                </td>
                                                <td>
                                                    <?php echo $value['name']; ?>
                                                </td>
                                                <td>
                                                    <?php echo $value['note']; ?>
                                                </td>
                                                <td>
                                                    <?php echo $value['amount']; ?>
                                                </td>
                                            </tr>
                                            <?php
                                            $count++;
                                        }
                                        ?>
                                        <tr>
                                            <th colspan="4" class="text-end"><?php echo $this->lang->line('grand_total'); ?></th>
                                            <th>
                                                <?php echo number_format($grand_total, 2, '.', ''); ?>
                                            </th>
                                        </tr>
                                    </tbody>
                                </table>
                                <?php
                            }
                            ?>
                        </div>
                        <div class="card-footer">
                            <div class="mailbox-controls"> 
                                <div class="float-end">
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php
                }
                ?>
            </div>
        </div>

<script type="text/javascript">
    $(document).ready(function () {
        var date_format = '<?php echo $result = strtr($this->customlib->getHospitalDateFormat(), ['d' => 'dd', 'm' => 'mm', 'Y' => 'yyyy',]) ?>';
        /* .date init removed - auto-init via event delegation */
    });
</script>