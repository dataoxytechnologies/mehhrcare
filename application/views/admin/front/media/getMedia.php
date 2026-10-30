<div class="row g-3 mb-3">
    <div class="col-md-6">
        <label class="form-label"><?php echo $this->lang->line('search_by_file_name'); ?></label>
        <div class="input-group">
            <span class="input-group-text"><i class="fa fa-search"></i></span>
            <input type="text" class="form-control search_text" placeholder="<?php echo $this->lang->line('search_by_file_name'); ?>">
        </div>
    </div>
    <div class="col-md-6">
        <label class="form-label"><?php echo $this->lang->line('filter_by_file_type'); ?></label>
        <select class="form-control file_type">
            <option value=""><?php echo $this->lang->line('select'); ?></option>
            <?php foreach ($mediaTypes as $type_key => $type_value) { ?>
                <option value="<?php echo $type_value; ?>"><?php echo $type_value; ?></option>
            <?php } ?>
        </select>
    </div>
</div>

<div id="media_div"></div>
<div class="text-end mt-3" id="pagination_link"></div>

<script type="text/javascript">
    function load_country_data(page) {
        $('#media_div').html("");
        var keyword   = $('.search_text').val();
        var file_type = $('.file_type').val();
        $.ajax({
            url: "<?php echo base_url(); ?>admin/front/media/getPage/" + page,
            method: "GET",
            data: {'keyword': keyword, 'file_type': file_type},
            dataType: "json",
            success: function (data) {
                if (data.result_status === 1) {
                    $.each(data.result, function (index, value) {
                        $("#media_div").append(data.result[index]);
                    });
                    $('#pagination_link').html(data.pagination_link);
                }
            }
        });
    }
    $(document).ready(function () {
        $(".search_text").keyup(function () { load_country_data(1); });
        $(".file_type").change(function () { load_country_data(1); });
        load_country_data(1);
    });
</script>
