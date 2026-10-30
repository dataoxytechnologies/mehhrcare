<div class="row">
            <div class="col-md-12">
                <!-- Horizontal Form -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><?php echo $this->lang->line('add_language'); ?></h3>
                        <div class="d-flex gap-2 align-items-center flex-wrap float-end">
                        </div>
                    </div><!-- /.card-header -->
                    <!-- form start -->
                    <form id="form1" action="<?php echo site_url('admin/language/create') ?>"  id="employeeform" name="employeeform" class="form-horizontal form-label-left" method="post" accept-charset="utf-8">
                        <div class="card-body">  
                            <?php echo $this->customlib->getCSRF(); ?>
                            <div class="mb-3">
                                <label class="form-label col-md-3 col-sm-3 col-12" for="exampleInputEmail1"><?php echo $this->lang->line('language'); ?><small class="req">  *</small></label>
                                <div class="col-md-6 col-sm-6 col-12">
                                    <input autofocus="" id="language" name="language"  type="text" placeholder="" class="form-control col-md-7 col-12" value="<?php echo set_value('language'); ?>" />
                                    <span class="text-danger"><?php echo form_error('language'); ?></span>
                                </div> </div>
                        </div><!-- /.card-body -->
                        <div class="card-footer">
                            <div class="mb-3">
                                <div class="col-md-6 col-sm-6 col-12 offset-md-3">
                                    <button type="submit" class="btn btn-info"><?php echo $this->lang->line('save'); ?></button>
                                </div>
                            </div>

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