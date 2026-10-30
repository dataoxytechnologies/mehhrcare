$(document).ready(function () {
    $('.example').each(function () {
        var $tbl = $(this);
        // Per-table export heading: prefer the table's own data-export-title
        // (same convention as initDatatable); fall back to the first .download_label
        // so every existing page keeps its current behaviour.
        var exportTitle = $tbl.data('exportTitle') || $('.download_label').html();
        $tbl.DataTable({
            "aaSorting": [],
            rowReorder: {
            selector: 'td:nth-child(2)'
            },
            //responsive: 'false',
            dom: '<"dt-toolbar"f<"dt-toolbar-right"lB>>rtip',
            buttons: [

                {
                    extend: 'copyHtml5',
                    text: '<i class="fa fa-files-o"></i>',
                    titleAttr: 'Copy',
                    title: exportTitle,
                     exportOptions: {
                    columns: ["thead th:not(.noExport)"]
                  }
                },

                {
                    extend: 'excelHtml5',
                    text: '<i class="fa fa-file-excel-o"></i>',
                    titleAttr: 'Excel',
                   
                    title: exportTitle,
                     exportOptions: {
                    columns: ["thead th:not(.noExport)"]
                  }
                },

                {
                    extend: 'csvHtml5',
                    text: '<i class="fa fa-file-text-o"></i>',
                    titleAttr: 'CSV',
                    title: exportTitle,
                     exportOptions: {
                    columns: ["thead th:not(.noExport)"]
                  }
                },

                {
                    extend: 'pdfHtml5',
                    text: '<i class="fa fa-file-pdf-o"></i>',
                    titleAttr: 'PDF',
                    title: exportTitle,
                    exportOptions: {
                    columns: ["thead th:not(.noExport)"]
                  }
                },

                {
                    extend: 'print',
                    text: '<i class="fa fa-print"></i>',
                    titleAttr: 'Print',
                    title: exportTitle,
                 customize: function ( win ) {

                    $(win.document.body).find('th').addClass('display').css('text-align', 'left');
                    $(win.document.body).find('td').addClass('display').css('text-align', 'left');
                    $(win.document.body).find('table').addClass('display').css('font-size', '14px');
                    // Only strip the H1 when it's the noisy document.title fallback (no real
                    // export title set). A real heading comes from data-export-title (exportTitle)
                    // or a .download_label — keep and centre it in either case.
                    if (!$.trim(exportTitle || '') && !$.trim($('.download_label').text())) {
                        $(win.document.body).find('h1').remove();
                    } else {
                        $(win.document.body).find('h1').css('text-align', 'center');
                    }
                },
                     exportOptions: {
                    columns: ["thead th:not(.noExport)"],
                    format: {
                        body: function(data, row, column, node) {
                            // exportData strips HTML so checkboxes become empty — read DOM state directly.
                            var $cb = $(node).find('input[type="checkbox"]');
                            if ($cb.length) {
                                return $cb.prop('checked') ? 'Yes' : 'No';
                            }
                            return data;
                        }
                    }
                  }
                }
            ],
            "language": {
                sLengthMenu: "_MENU_"
            }
        });
    });
});


/*--dropify--*/
$(document).ready(function(){
                // Basic
                $('.filestyle').dropify();

                // Translated
                $('.dropify-fr').dropify({
                    messages: {
                        default: 'Glissez-déposez un fichier ici ou cliquez',
                        replace: 'Glissez-déposez un fichier ou cliquez pour remplacer',
                        remove:  'Supprimer',
                        error:   'Désolé, le fichier trop volumineux'
                    }
                });

                // Used events
                var drEvent = $('#input-file-events').dropify();

                drEvent.on('dropify.beforeClear', function(event, element){
                    return confirm("Do you really want to delete \"" + element.file.name + "\" ?");
                });

                drEvent.on('dropify.afterClear', function(event, element){
                    alert('File deleted');
                });

                drEvent.on('dropify.errors', function(event, element){
                    console.log('Has Errors');
                });

                var drDestroy = $('#input-file-to-destroy').dropify();
                drDestroy = drDestroy.data('filestyle')
                $('#toggleDropify').on('click', function(e){
                    e.preventDefault();
                    if (drDestroy.isDropified()) {
                        drDestroy.destroy();
                    } else {
                        drDestroy.init();
                    }
                })
            });
/*--end dropify--*/

/*--nprogress--*/
 $('body').show();
    $('.version').text(NProgress.version);
    NProgress.start();
    setTimeout(function() { NProgress.done(); $('.fade').removeClass('out'); }, 1000);
/*--nprogress--*/    
// _selector, // selector  class of table
// _url, // url is url of controller where data to be fetch
// params={}, is parameter of post method
// rm_export_btn=[], // var rm_export_btn = ["btn-pdf"] //"btn-copy","btn-excel","btn-csv","btn-pdf","btn-print" // btn-all
// pageLength=100, //per page data
// aoColumnDefs=[{ "bSortable": false, "aTargets": [ -1 ] ,'sClass': 'dt-body-right'}],
// searching=true,
// aaSorting=[],
// dataSrc="data" it is array source of data


   function initDatatable(_selector,_url,params={},rm_export_btn=[],pageLength=100,aoColumnDefs=[{ "bSortable": false, "aTargets": [ -1 ] ,'sClass': 'dt-body-right'}],searching=true,aaSorting=[],dataSrc="data"){
        if ($.fn.DataTable.isDataTable('.'+_selector)) { // if exist datatable it will destrory first
         $('.'+_selector).DataTable().destroy();
       }
        // Resolve the export heading once so the print customize can decide whether
        // to keep the <h1> based on the REAL title, not on .download_label existence.
        var exportTitle = $('.'+_selector).data("exportTitle");
        var table = $('.'+_selector)
    .on( 'preInit.dt', function (e, settings ) {

     var api = new $.fn.dataTable.Api( settings );
     $.each(rm_export_btn, function(key, expt_select) {
     if(expt_select === "btn-all"){
       api.buttons().remove();

     }else{
       api.buttons('.'+expt_select).remove();

     }
    });

    }).DataTable({
        // "scrollX": true,
        dom: '<"dt-toolbar"f<"dt-toolbar-right"lB>>r<t>ip',
    
         lengthMenu: [[100, -1], [100, "All"]],
       
          buttons: [
            {
                extend:    'copy',
                text:      '<i class="fa fa-files-o"></i>',
                titleAttr: 'Copy',
                 className: "btn-copy",
                title: exportTitle,
                  exportOptions: {
                    columns: ["thead th:not(.noExport)"]
                  }
            },
            {
                extend:    'excel',
                text:      '<i class="fa fa-file-excel-o"></i>',
                titleAttr: 'Excel',
                     className: "btn-excel",
                title: exportTitle,
                  exportOptions: {
                    columns: ["thead th:not(.noExport)"]
                  }
            },
            {
                extend:    'csv',
                text:      '<i class="fa fa-file-text-o"></i>',
                titleAttr: 'CSV',
                className: "btn-csv",
                title: exportTitle,
                  exportOptions: {
                    columns: ["thead th:not(.noExport)"]
                  }
            },
            {
                extend:    'pdf',
                text:      '<i class="fa fa-file-pdf-o"></i>',
                titleAttr: 'PDF',
                className: "btn-pdf",
                title: exportTitle,
                  exportOptions: {
                    columns: ["thead th:not(.noExport)"]
                  },

            },
            {
                extend:    'print',
                text:      '<i class="fa fa-print"></i>',
                titleAttr: 'Print',
                className: "btn-print",
                title: exportTitle,
                customize: function ( win ) {

                    $(win.document.body).find('th').addClass('display').css('text-align', 'left');
                    $(win.document.body).find('table').addClass('display').css('font-size', '14px');
                     $(win.document.body).find('td').addClass('display').css('text-align', 'left');
                    // Only strip the H1 when it's the noisy document.title fallback (no real
                    // export title set). A real heading comes from data-export-title (exportTitle)
                    // or a .download_label — keep and centre it in either case.
                    if (!$.trim(exportTitle || '') && !$.trim($('.download_label').text())) {
                        $(win.document.body).find('h1').remove();
                    } else {
                        $(win.document.body).find('h1').css('text-align', 'center');
                    }
                },
                exportOptions: {
                    columns: ["thead th:not(.noExport)"],
                    format: {
                        body: function(data, row, column, node) {
                            // exportData strips HTML so checkboxes become empty — read DOM state directly.
                            var $cb = $(node).find('input[type="checkbox"]');
                            if ($cb.length) {
                                return $cb.prop('checked') ? 'Yes' : 'No';
                            }
                            return data;
                        }
                    }
                  }

            }
        ],
      
         // "scrollY":        "320px",
         
           "language": {
            processing: '<i class="fa fa-spinner fa-spin fa-1x fa-fw"></i><span class="sr-only">Loading...</span> ',
             sLengthMenu: "_MENU_"
        },
        "pageLength": pageLength,
        "searching": searching,
        "aaSorting": aaSorting, // default sorting [ [0,'asc'], [1,'asc'] ]
        "autoWidth": false,
        "aoColumnDefs": aoColumnDefs, //disable sorting { "bSortable": false, "aTargets": [ 1,2 ] }
        "processing": true,
        "serverSide": true,
        
        "ajax":{
        "url": baseurl+_url,
        "dataSrc": dataSrc,
        "type": "POST",
        'data': params,
     },
        "drawCallback": function() {
            this.api().table().container().querySelectorAll('[data-bs-toggle="tooltip"]').forEach(function(el) {
                var existing = bootstrap.Tooltip.getInstance(el);
                if (existing) { existing.dispose(); }
                new bootstrap.Tooltip(el);
            });
        }

    });
    return table;
    }

   function emptyDatatable(_selector,dataSrc="data"){

        $('.'+_selector).DataTable({
        "searching": false,
        "processing": true,
        "paging":   false,
        "ordering": false,
        "info":     true,
        "ajax": {
            "url": base_url+'backend/json-files/datatable_empty.json',
            "dataSrc": dataSrc
        }
    });
    }