<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
?>

<!-- Main content -->
    <div class="row">
            <div class="col-md-12">
                <div class="card">
                 <?php $this->load->view('admin/report/_pharmacy');?>
                 <div class="card-header ptbnull"></div>
                    <div class="card-header">
                        <h3 class="card-title"><?php echo $this->lang->line('stock_report'); ?></h3>
                    </div>
                    <form id="form1" action="" method="post">
                        <div class="card-body row pb-0">
                            <?php echo $this->customlib->getCSRF(); ?>
                             <div class="col-sm-6 col-md-3">
                                <div class="mb-3">
                                    <label><?php echo $this->lang->line('medicine_category'); ?></label>
                                     <select class="form-control select2" name='medicine_category'  >
                                            <option value="<?php echo set_value('medicine_category_id'); ?>"><?php echo $this->lang->line('select'); ?>
                                            </option>
                                            <?php foreach ($medicineCategory as $dkey => $dvalue) {
                                                ?>
                                                <option value="<?php echo $dvalue["id"]; ?>"><?php echo $dvalue["medicine_category"] ?>
                                                </option>   
                                        <?php } ?>
                                        </select>
                                </div>
                            </div>
                            <div class="col-sm-6 col-md-3">
                                <div class="mb-3">
                                    <label><?php echo $this->lang->line('stock_type'); ?></label>
                                     <select name="stock_type" class="form-control select2 supplier_select2"  id="" name='' >
                                        <option value=""><?php echo $this->lang->line('all'); ?></option>
                                        <?php foreach ($supplierCategory as $dkey => $dvalue) {
                                            ?>
                                            <option value="<?php echo $dkey; ?>" ><?php echo $dvalue;  ?></option>   
                                    <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-6 col-md-auto d-flex align-items-end ps-md-3">
                                <div class="mb-3">
                                    <button type="submit" name="search" value="search_filter" class="btn btn-primary d-inline-flex align-items-center gap-1 py-2 checkbox-toggle">
                                        <i class="fa fa-search"></i>
                                        <?php echo $this->lang->line('search'); ?>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                    <div class="card-body pt-0">
                        <div class="table-responsive-mobile">
                            <table class="table table-striped table-bordered table-hover ajaxlist " cellspacing="0" width="100%" data-export-title="<?php echo $this->lang->line('medicines_stock'); ?>">
                                <thead>
                                    <tr>
                                        <th><?php echo $this->lang->line('medicine_name'); ?></th>
                                        <th><?php echo $this->lang->line('medicine_company'); ?></th>
                                        <th><?php echo $this->lang->line('medicine_composition'); ?></th>
                                        <th><?php echo $this->lang->line('medicine_category'); ?></th> 
                                        <th><?php echo $this->lang->line('medicine_group'); ?></th>
                                        <th><?php echo $this->lang->line('unit'); ?></th>
                                        <th class=""><?php echo $this->lang->line('available_qty'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                          </div>
                    </div>

                </div>
            </div>
        </div>


<script>
    $(function () {
        //Initialize Select2 Elements
        $('.select2').select2();
    });
</script>
<script type="text/javascript">
   
    function showdate(value) {
        if (value == 'period') {
            $('#fromdate').show();
            $('#todate').show();
        } else {
            $('#fromdate').hide();
            $('#todate').hide();
        }
    }
</script>
<script>
    $(document).ready(function (e) {
      
        emptyDatatable('allajaxlist', 'data');
    });
    
( function ( $ ) {
    'use strict';
    $(document).ready(function () {
       $('#form1').on('submit', (function (e) {
        e.preventDefault();
        var search= 'search_filter';
        var formData = new FormData(this);
        formData.append('search', 'search_filter');
        $.ajax({
            url: '<?php echo base_url(); ?>admin/report/stockcheckvalidation',
            type: "POST",
            data: formData,
            dataType: 'json',
            contentType: false,
            cache: false,
            processData: false,
            success: function (data) {
                if (data.status == "fail") {
                   $.each(data.error, function(key, value) {
                        $('#error_' + key).html(value);
                    });
                } else {
                    $("#error_search_type").html('');
                    $("#error_collect_staff").html('');
                   initDatatable('ajaxlist','admin/report/getpharmacystock',data.param,[],100,[
                        { 'bSortable': false, 'aTargets': [ 1 ] }
                    ]);
                }
            }
        });
        }
       ));
   });

} ( jQuery ) );

</script>
<!-- //========datatable start===== -->
<script type="text/javascript">
( function ( $ ) {
    'use strict';
    $(document).ready(function () {
        initDatatable('ajaxlist','admin/report/getpharmacystock',[],[],100,[
          { 'bSortable': false, 'aTargets': [ 1 ] }
       ]);
    });
} ( jQuery ) )
</script>
<!-- //========datatable end===== -->