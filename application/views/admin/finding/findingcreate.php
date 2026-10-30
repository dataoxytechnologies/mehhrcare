<!-- Content Wrapper. Contains page content -->
<!-- Content Header (Page header) -->
    <!-- Main content -->
    <div class="row">
            <div class="col-md-12">
                <!-- Horizontal Form -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><?php echo $this->lang->line('add_expense_head'); ?></h3>
                        <div class="d-flex gap-2 align-items-center flex-wrap float-end">
                            <a href="<?php echo base_url(); ?>category" class="btn btn-primary btn-sm"  data-bs-toggle="tooltip" title="<?php echo $this->lang->line('add'); ?>" > <i class="fa fa-list"> <?php echo $this->lang->line('list'); ?></i>
                            </a>
                        </div>
                    </div><!-- /.card-header -->
                    <form id="formi" action="<?php echo site_url('category/create') ?>"  id="employeeform" name="employeeform" method="post" accept-charset="utf-8">
                        <div class="card-body">
                            <?php if ($this->session->flashdata('msg')) { ?>
                                <?php echo $this->session->flashdata('msg') ?>
                            <?php } ?>
                            <?php echo $this->customlib->getCSRF(); ?>
                            <div class="mb-3">
                                <label for="exampleInputEmail1"><?php echo $this->lang->line('category'); ?></label>
                                <input autofocus="" id="category" name="category" placeholder="" type="text" class="form-control"  value="<?php echo set_value('category'); ?>" />
                                <span class="text-danger"><?php echo form_error('category'); ?></span>
                            </div>
                        </div><!-- /.card-body -->
                        <div class="card-footer">
                            <button type="submit" class="btn btn-info float-end"><?php echo $this->lang->line('save'); ?></button>
                        </div>
                    </form>
                </div> 
            </div><!--/.col (right) -->
        </div>   <!-- /.row -->
    

<script type="text/javascript">
    $(document).ready(function () {
        $("#btnreset").click(function () {
            $("#form1")[0].reset();
        });
    });
</script>