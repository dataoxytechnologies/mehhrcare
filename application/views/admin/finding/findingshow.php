<!-- Content Header (Page header) -->
    <!-- Main content -->
    <div class="row">
            <!-- left column -->
            <div class="col-md-12">
                <!-- general form elements -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><?php echo $title; ?></h3>
                        <div class="d-flex gap-2 align-items-center flex-wrap float-end">
                        </div><!-- /.ms-auto d-flex gap-1 -->
                    </div><!-- /.card-header -->
                    <div class="card-body no-padding">
                        <div class="mailbox-controls">
                            <!-- Check all button -->
                            <a href="<?php echo base_url(); ?>category/create" class="btn btn-primary btn-sm"  data-bs-toggle="tooltip" title="<?php echo $this->lang->line('add_category'); ?>">
                                <i class="fa fa-plus"></i> <?php echo $this->lang->line('add_category'); ?>
                            </a>
                            <div class="float-end">
                            </div><!-- /.float-end -->
                        </div>
                        <div class="table-responsive mailbox-messages">
                            <table class="table table-hover table-striped">
                                <tbody>
                                    <tr>
                                        <th><?php echo $this->lang->line('category'); ?></th>
                                        <td class="mailbox-name"><a href="#"> <?php echo $category['category'] ?></a></td>
                                    </tr>
                                </tbody>
                            </table><!-- /.table -->
                        </div><!-- /.mail-box-messages -->
                    </div><!-- /.card-body -->
                    <div class="card-footer">
                        <div class="mailbox-controls">
                            <!-- Check all button -->
                            <div class="float-end">
                            </div><!-- /.float-end -->
                        </div>
                    </div>
                </div>
            </div>
            <!-- right column -->
        </div>   <!-- /.row -->
    
</div>