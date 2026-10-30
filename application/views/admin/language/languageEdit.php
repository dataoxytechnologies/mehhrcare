<div class="row">
            <div class="col-md-12">
                <!-- Horizontal Form -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><?php echo $title; ?></h3>
                    </div><!-- /.card-header -->
                    <form action="<?php echo site_url("sections/edit/" . $id) ?>"  id="employeeform" name="employeeform" method="post" accept-charset="utf-8">
                        <div class="card-body">
                            <?php echo validation_errors(); ?>
                            <?php echo $this->customlib->getCSRF(); ?>
                            <div class="mb-3">
                                <label for="exampleInputEmail1"><?php echo form_error('section'); ?></label>
                                <input id="section" name="section" placeholder="" type="text" class="form-control" value="<?php echo set_value('section', $section['section']); ?>" />
                                <span class="text-danger"><?php echo form_error('section'); ?></span>
                            </div>
                        </div><!-- /.card-body -->
                        <div class="card-footer">
                            <button type="button" class="btn btn-secondary"><?php echo $this->lang->line('reset'); ?></button>
                            <button type="submit" class="btn btn-info float-end"><?php echo $this->lang->line('save'); ?></button>
                        </div>
                    </form>
                </div>
                <!-- general form elements disabled -->
            </div><!--/.col (right) -->
        </div>   <!-- /.row -->
    
