<div class="row">
            <div class="col-md-12">
                <!-- Horizontal Form -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <?php echo $this->lang->line('edit_pharses_for'); ?> <?php echo $lang1; ?>
                        </h3>
                    </div><!-- /.card-header --> 
                    <div class="card-body">
                        <?php echo validation_errors(); ?>
                        <?php
                        foreach ($language_pharses as $key => $pharses) {
                            ?>
                            <div class="col-md-4">
                                <div class="mb-3 form-loading">
                                    <label for="exampleInputEmail1"><?php echo $pharses['key'] ?></label>
                                    <div class="input-group">
                                        <input name="key_id" type="hidden" class="key_id" value="<?php echo $pharses['id']; ?>" />
                                        <input name="pharsesid" type="hidden" class="recordid" value="<?php echo $pharses['pharsesid']; ?>" />
                                        <input autofocus="" type="text" placeholder="<?= $this->lang->line('enter_phrase_for_key') ?>" name="pharses<?php echo $pharses['id']; ?>" class="form-control" value="<?php echo set_value('language', $pharses['pharses']); ?>">
                                        <span class="input-group-btn">
                                            <button class="btn btn-secondary pharses_edit" type="button" data-id="<?php echo $pharses['id']; ?>" data-langid="<?php echo $id; ?>"><i class="fa fa-pencil"></i></button>
                                        </span>
                                    </div><!--./input-group-->
                                    <div class="loading">
                                        <img src="<?php echo $this->media_storage->getImageURL('/backend/images/loading.gif') ?>" id="loader_<?php echo $pharses['id']; ?>">
                                    </div><!--./loading-->
                                </div><!--./form-group form-loading-->
                            </div><!--./col-md-4-->
                            <?php
                        }
                        ?>
                    </div><!-- /.card-body -->
                    <div class="card-footer">
                    </div>
                </div>
                <!-- general form elements disabled -->
            </div><!--/.col (right) -->
        </div>   <!-- /.row -->
    

<script type="text/javascript">
    $(document).on('click', '.pharses_edit', function () {
        var id = $(this).data('id');
        var langid = $(this).data('langid');
        var value = $(this).closest('.input-group').find('.form-control').val();
        var recordid = $(this).closest('.input-group').find('.recordid').val();
        var key_id = $(this).closest('.input-group').find('.key_id').val();
        $('#loader_' + key_id).show();
        var base_url = '<?php echo base_url() ?>';
        $.ajax({
            type: "POST",
            url: base_url + "admin/language/editlanguage",
            data: {'recordid': recordid, 'key_id': key_id, 'id': id, 'value': value, 'langid': langid},
            dataType: "json",
            success: function (data) {
                if (data.status == 1) {

                    $('#loader_' + key_id).hide();
                }
            }
        });
    });
</script>
<script src="<?php echo base_url(); ?>backend/dist/js/savemode.js"></script>