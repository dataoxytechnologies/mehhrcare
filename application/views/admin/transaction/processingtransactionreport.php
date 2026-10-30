<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
?>
<div class="row">
            <!-- left column -->
            <div class="col-md-12">
                <!-- general form elements -->
                <div class="card border0 mb0">
                     <?php $this->load->view('admin/report/_finance');?>  
                     <div class="card-header ptbnull"></div>
                        <div class="card-header">
                            <h3 class="card-title"><?php echo $this->lang->line('processing_transaction_report') ?></h3>
                            <div class="d-flex gap-2 align-items-center flex-wrap float-end"></div>
                        </div>
                    <div class="card-body pb0">
                        <div class="row">
                             
                        </div>
                    </div>
           
    <div class="card-body">    
        <div class="table-responsive">  
            <div class="download_label"> <?php echo $this->lang->line('processing_transaction_report') ?></div>
            <table class="table table-striped table-bordered table-hover transaction-list" data-export-title="<?php echo $this->lang->line('processing_transaction_report') ?>">
                <thead>
                    <tr>
                        <th><?php echo $this->lang->line('patient_name');?></th>
                        <th><?php echo $this->lang->line('date');?></th>
                        <th><?php echo $this->lang->line('case_reference_no');?></th>
                        <th><?php echo $this->lang->line('opd_no');?></th>
                        <th><?php echo $this->lang->line('ipd_no');?></th>
                        <th><?php echo $this->lang->line('pharmacy_bill_no');?></th>
                        <th><?php echo $this->lang->line('pathology_bill_no');?></th>
                        <th><?php echo $this->lang->line('radiology_bill_no');?></th>
                        <th><?php echo $this->lang->line('blood_donor_cycle_no');?></th>
                        <th><?php echo $this->lang->line('blood_issue_no');?></th>
                        <th><?php echo $this->lang->line('ambulance_call_no');?></th>
                        <th><?php echo $this->lang->line('appointment_no');?></th>
                        <th><?php echo $this->lang->line('amount');?>(<?php echo $currency_symbol; ?>)</th>
                        <th><?php echo $this->lang->line('payment_mode');?></th>
                        <th><?php echo $this->lang->line('note');?></th>
                        
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>                
        </div>
    </div>                       
                </div>
            </div>
        </div>   <!-- /.row -->
    
</div>
<div class="modal fade sh-modal sh-modal-branded" id="collectionModal" tabindex="-1" aria-labelledby="collectionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="collectionModalLabel"><?php echo $this->lang->line('collection_list'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="pup-scroll-area">
                <div class="modal-body">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('close'); ?></button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<script type="text/javascript">
    // Linked .start_date / .end_date pickers — same pattern as transactionreport.php
    $(document).ready(function(){
        document.querySelectorAll('.start_date').forEach(function(startEl) {
            if (!startEl.classList.contains('date')) startEl.classList.add('date');
            SHPicker.onChange(startEl, function(e) {
                if (!e.date) return;
                document.querySelectorAll('.end_date').forEach(function(endEl) {
                    SHPicker.setRestriction(endEl, { minDate: e.date });
                });
            });
        });
        document.querySelectorAll('.end_date').forEach(function(endEl) {
            if (!endEl.classList.contains('date')) endEl.classList.add('date');
            SHPicker.onChange(endEl, function(e) {
                if (!e.date) return;
                document.querySelectorAll('.start_date').forEach(function(startEl) {
                    SHPicker.setRestriction(startEl, { maxDate: e.date });
                });
            });
        });
    });
$(document).ready(function() {
    initDatatable('transaction-list','admin/transaction/processingtransaction');
});
    $(document).on('click','.daily_collection',function(e){
         var $btn = $(this);
        e.preventDefault();
        var dateText = $(this).closest('tr').find('td:first').text().trim();
        $.ajax({
            url: baseurl+'admin/transaction/gettransactionbydate',
            type: "POST",
            data: {'date': $(this).data('date')},
            dataType: 'json',
            beforeSend: function() {
          $btn.btnLoading();
            },
            success: function (data) {
                  $btn.btnReset();
           $('#collectionModal .modal-body').html(data.page);
           $('#collectionModalLabel').text(dateText);
           $('#collectionModal .example').DataTable({ 
            dom: "Bfrtip",
            buttons: [

                {
                    extend: 'copyHtml5',
                    text: '<i class="fa fa-files-o"></i>',
                    titleAttr: 'Copy',
                    title: $('.download_label').html(),
                     exportOptions: {
                    columns: ["thead th:not(.noExport)"]
                  }
                },

                {
                    extend: 'excelHtml5',
                    text: '<i class="fa fa-file-excel-o"></i>',
                    titleAttr: 'Excel',
                   
                    title: $('.download_label').html(),
                     exportOptions: {
                    columns: ["thead th:not(.noExport)"]
                  }
                },

                {
                    extend: 'csvHtml5',
                    text: '<i class="fa fa-file-text-o"></i>',
                    titleAttr: 'CSV',
                    title: $('.download_label').html(),
                     exportOptions: {
                    columns: ["thead th:not(.noExport)"]
                  }
                },

                {
                    extend: 'pdfHtml5',
                    text: '<i class="fa fa-file-pdf-o"></i>',
                    titleAttr: 'PDF',
                    title: $('.download_label').html(),
                    exportOptions: {
                    columns: ["thead th:not(.noExport)"]
                  }
                },

                {
                    extend: 'print',
                    text: '<i class="fa fa-print"></i>',
                    titleAttr: 'Print',
                    title: $('.download_label').html(),
                 customize: function ( win ) {

                    $(win.document.body).find('th').addClass('display').css('text-align', 'left');
                    $(win.document.body).find('td').addClass('display').css('text-align', 'left');
                    $(win.document.body).find('table').addClass('display').css('font-size', '14px');
                    $(win.document.body).find('h1').css('text-align', 'center');
                },
                     exportOptions: {
                    columns: ["thead th:not(.noExport)"]
                  }
                }
            ]
        });

           shModal('collectionModal').show();
            },
    error: function(xhr) { // if error occured
        alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");
      $btn.btnReset();
    },
    complete: function() {
    $btn.btnReset();
    }
        });
    });
</script>