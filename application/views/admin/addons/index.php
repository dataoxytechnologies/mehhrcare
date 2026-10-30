<!--<div class="row">-->
<!--            <?php $this->load->view('setting/sidebar'); ?>-->
<!--            <div class="col-md-10">-->
                <!-- general form elements -->
<!--                <div class="card">-->
<!--                    <div class="card-header ptbnull">-->
<!--                        <h3 class="card-title titlefix"><?php echo $this->lang->line('addons'); ?></h3>-->
<!--                    </div><!-- /.card-header -->-->
<!--                    <div class="card-body">-->
<!--                        <form class="d-flex align-items-center justify-content-center gap-2 flex-nowrap mb-3 mx-auto sh-w-560" role="form" action="<?php echo current_url(); ?>" method="post" enctype="multipart/form-data" id="local_form">-->
<!--                            <div class="flex-grow-1 relative z-index-1 mb-0 sh-w-420">-->
<!--                                <input class="filestyle form-control sh-h-40" data-height="40" type="file" name="file" id="exampleInputFile">-->
<!--                                <span class="text-danger"><?php echo form_error('file'); ?></span>-->
<!--                            </div>-->
<!--                            <button type="submit" class="btn btn-primary text-nowrap sh-h-42"><i class="fa fa-upload"></i> <?php echo $this->lang->line('upload'); ?></button>-->
<!--                        </form>-->
<!--                        <form class="post-list">-->
<!--                            <input type="hidden" value="" />-->
<!--                        </form>-->
<!--                        <div class="position-relative sh-min-h-300">                            -->
<!--                            <div class="modal_loader_div" style="display: none;"></div>-->
<!--                            <div class="row">-->
<!--                                <div class="col-md-12">-->
<!--                                    <div class="pagination-container"></div>-->
<!--                                    <div class="pagination-nav float-end"></div>-->
<!--                                </div>-->
<!--                            </div>-->
<!--                        </div>-->
<!--                    </div><!-- /.card-body -->-->
<!--                </div>-->
<!--            </div><!--/.col (left) -->-->
            <!-- right column -->
<!--        </div>-->
    


<!--<div id="addonUpdateModal" class="modal fade sh-modal sh-modal-accent" tabindex="-1" aria-labelledby="addonUpdateModalLabel" aria-hidden="true">-->
<!--    <div class="modal-dialog modal-dialog-centered">-->
<!--        <div class="modal-content">-->
<!--            <div class="modal-header">-->
<!--                <h5 class="modal-title" id="addonUpdateModalLabel"><?php echo $this->lang->line('update_your_addon'); ?></h5>-->
<!--                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>-->
<!--            </div>-->
<!--            <form action="<?php echo site_url('admin/admin/updateAddonVerify'); ?>" method="POST" id="update_addon_verify">-->
<!--                <div class="pup-scroll-area">-->
<!--                    <div class="modal-body addon_update_modal-body">-->
<!--                        <div class="sh-form-card">-->
<!--                            <div class="sh-card-header">-->
<!--                                <span class="sh-card-header-title"><?php echo $this->lang->line('details'); ?></span>-->
<!--                            </div>-->
<!--                            <div class="p-2">-->
<!--                                <div class="error_message">-->
<!--                                </div>-->
<!--                                <input type="hidden" name="addon" class="addon_name" value="">-->
<!--                                <input type="hidden" name="product_id" class="product_id" value="">-->
<!--                                <div class="mb-3">-->
<!--                                    <label class="ainline"><span>Envato Market Purchase Code for Addon Update ( <a target="_blank" href="https://help.market.envato.com/hc/en-us/articles/202822600-Where-Is-My-Purchase-Code-"> How to find it?</a> )</span></label>-->
<!--                                    <input type="text" class="form-control" id="input-addon_check_update_envato_market_purchase_code" name="addon_check_update_envato_market_purchase_code">-->
<!--                                    <div id="error" class="input-error text text-danger"></div>-->
<!--                                </div>-->
<!--                            </div>-->
<!--                        </div>-->
<!--                    </div>-->
<!--                </div>-->
<!--                <div class="modal-footer">-->
<!--                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>-->
<!--                    <button type="submit" class="btn btn-info" data-loading-text="<i class='fa fa-spinner fa-spin '></i> Saving..."><?php echo $this->lang->line('update'); ?></button>-->
<!--                </div>-->
<!--            </form>-->
<!--        </div>-->
<!--    </div>-->
<!--</div>-->

<!--<script type="text/javascript">-->
<!--    var app = {-->

<!--        Posts: function() {-->

<!--            /**-->
<!--             * This method contains the list of functions that needs to be loaded-->
<!--             * when the "Posts" object is instantiated.-->
<!--             *-->
<!--             */-->
<!--            this.init = function() {-->
<!--                this.get_items_pagination();-->
<!--            }-->

<!--            /**-->
<!--             * Load items pagination.-->
<!--             */-->
<!--            this.get_items_pagination = function() {-->

<!--                _this = this;-->

                /* Check if our hidden form input is not empty, meaning it's not the first time viewing the page. */
<!--                if ($('form.post-list input').val()) {-->
                    /* Submit hidden form input value to load previous page number */
<!--                    data = JSON.parse($('form.post-list input').val());-->
<!--                    _this.ajax_get_items_pagination(data.page);-->
<!--                } else {-->
                    /* Load first page */
<!--                    _this.ajax_get_items_pagination(1, 'name');-->
<!--                }-->

                /* Search */
<!--                $(document).on('submit', 'form#searchform', function(e) {-->
                    e.preventDefault(); // avoid to execute the actual submit of the form.
<!--                    _this.ajax_get_items_pagination(1);-->
<!--                });-->

<!--                $(document).on('click', '.pagination-nav .pagination li.unactive', function() {-->
<!--                    var page = $(this).attr('p');-->
<!--                    _this.ajax_get_items_pagination(page);-->
<!--                });-->
<!--            }-->

<!--            /**-->
<!--             * AJAX items pagination.-->
<!--             */-->
<!--            this.ajax_get_items_pagination = function(page) {-->
<!--                if ($(".pagination-container").length) {-->

<!--                    var post_data = {-->
<!--                        page: page,-->
<!--                        search: $('.post_search_text').val(),-->
<!--                    };-->

<!--                    $('form.post-list input').val(JSON.stringify(post_data));-->
<!--                    var data = {-->
<!--                        data: JSON.parse($('form.post-list input').val()),-->
<!--                    };-->

<!--                    $.ajax({-->
<!--                        url: baseurl + 'admin/addons/getuploaddata',-->
<!--                        type: 'POST',-->
<!--                        data: data,-->
<!--                        dataType: 'JSON',-->
<!--                        beforeSend: function() {-->
<!--                            $('.modal_loader_div').css("display", "block");-->
<!--                        },-->
<!--                        success: function(response) {-->
<!--                            $(".pagination-container").html(response.content);-->
<!--                            $('.pagination-nav').html(response.navigation);-->
<!--                            $('.modal_loader_div').fadeOut(400);-->

<!--                        },-->
                        error: function(xhr) { // if error occured
<!--                            alert("<?php echo $this->lang->line('error_occured_please_try_again'); ?>");-->
<!--                            $('.modal_loader_div').fadeOut(400);-->
<!--                        },-->
<!--                        complete: function() {-->
<!--                            $('.modal_loader_div').fadeOut(400);-->
<!--                        }-->
<!--                    });-->
<!--                }-->
<!--            }-->
<!--        }-->
<!--    }-->

<!--    /**-->
<!--     * When the document has been loaded...-->
<!--     *-->
<!--     */-->
<!--    jQuery(document).ready(function() {-->
<!--        modal_click_disabled('addonUpdateModal');-->
        posts = new app.Posts(); /* Instantiate the Posts Class */
        posts.init(); /* Load Posts class methods */

<!--    });-->

<!--    $(document).on('click', '.install', function(e) {-->
<!--        e.preventDefault();-->
<!--        let _button = $(this);-->
<!--        let product_id = _button.data('productId');-->
<!--        let directory = _button.data('directory');-->

<!--        _button.btnLoading();-->
<!--        $.ajax({-->
<!--            url: base_url + 'admin/addons/install',-->
<!--            type: "POST",-->
<!--            data: {-->
<!--                'addon': directory,-->
<!--                'product_id': product_id-->
<!--            },-->
<!--            dataType: 'json',-->
<!--            cache: false,-->
<!--            beforeSend: function() {-->
<!--                _button.btnLoading();-->
<!--            },-->
<!--            success: function(data) {-->

<!--                if (!data.status) {-->
<!--                    var message = "";-->

<!--                    errorMsg(data.msg);-->
<!--                } else {-->
<!--                    successMsg(data.msg);-->
<!--                    posts.init();-->
<!--                }-->
<!--            },-->
            error: function(xhr) { // if error occured
<!--                alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");-->
<!--                _button.btnReset();-->
<!--            },-->
<!--            complete: function() {-->
<!--                _button.btnReset();-->
<!--            }-->
<!--        });-->
<!--    });-->

<!--    $('#addonUpdateModal').on('shown.bs.modal', function(e) {-->

<!--        let product_id = $(e.relatedTarget).data('productId');-->
<!--        let directory = $(e.relatedTarget).data('directory');-->
<!--        let addon_name = $(e.relatedTarget).data('addonName');-->
<!--        $(this).find("input[class=product_id]").val(product_id);-->
<!--        $(this).find("input[class=addon_name]").val(addon_name);-->
<!--        $(this).find("button[type=submit]").data('product_id', product_id);-->
<!--        $(this).find("button[type=submit]").data('directory', directory);-->
<!--    });-->

<!--    $("#update_addon_verify").on('submit', (function(e) {-->
<!--        e.preventDefault();-->
<!--        let form = $(this);-->
<!--        let $this = $(this).find("button[type=submit]:focus");-->
<!--        $this.btnLoading();-->
<!--        let actionUrl = form.attr('action');-->
<!--        $.ajax({-->
<!--            url: actionUrl,-->
<!--            type: "POST",-->
<!--            data: form.serialize(),-->
<!--            dataType: 'json',-->
              
<!--            beforeSend: function() {-->
<!--                $('.addon_update_modal-body .error_message').html("");-->
<!--                $("[class^='input-error']").html("");-->
<!--                $this.btnLoading();-->
<!--            },-->
<!--            success: function(response, textStatus, xhr) {-->
<!--                if (xhr.status != 200) {-->
                 
<!--                 }else if(xhr.status == 200){-->

<!--                     if (response.status == 0) {-->
<!--                         $.each(response.error, function(key, value) {-->
                        
<!--                        $('#input-' + key).parents('.form-group').find('#error').html(value);-->
<!--                    });-->
<!--                     } else if(response.status == 2){-->
     
<!--                         errorMsg(response.message);-->
<!--                     }else if(response.status == 1){     -->
    
<!--                        let product_id = $this.data('product_id');-->
<!--                        let directory =   $this.data('directory');-->
<!--                        update_addon(product_id,directory);-->
<!--                     }-->

<!--                 }-->
              
<!--                $this.btnReset();-->
<!--            },-->
            error: function(xhr) { // if error occured
             
<!--                $this.btnReset();-->

<!--                if (xhr.status != 200) {-->
<!--                    console.log("sdfsdfdsf");-->
<!--                    var r = jQuery.parseJSON(xhr.responseText);          -->
               var $newmsgDiv = $("<div/>") // creates a div element              
                         .addClass("alert alert-danger") // add a class
<!--                         .html(r.message);-->
<!--                     $('.addon_update_modal-body .error_message').append($newmsgDiv);-->
<!--                 }-->

<!--            },-->
<!--            complete: function() {-->
<!--                $this.btnReset();-->
<!--            }-->

<!--        });-->
<!--    }));-->

<!--let update_addon=(product_id,directory)=>{-->
<!--        let _button = $(this);     -->
<!--        $.ajax({-->
<!--            url: base_url + 'admin/addons/install',-->
<!--            type: "POST",-->
<!--            data: {-->
<!--                'addon': directory,-->
<!--                'product_id': product_id-->
<!--            },-->
<!--            dataType: 'json',-->
<!--            cache: false,-->
<!--            beforeSend: function() {-->
              
<!--            },-->
<!--            success: function(data) {-->

<!--                if (!data.status) {-->
<!--                    var message = "";-->

<!--                    errorMsg(data.msg);-->
<!--                } else {-->
<!--                    successMsg(data.msg);-->
                    
<!--                    posts.init();-->
<!--                    shModal('addonUpdateModal').hide();-->
<!--                }-->
<!--            },-->
            error: function(xhr) { // if error occured
<!--                alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");-->
           
<!--            },-->
<!--            complete: function() {-->
           
<!--            }-->
<!--        });-->
    
<!--}-->

<!--    $(document).on('click', '.update', function(e) {-->
<!--        e.preventDefault();-->
<!--        let _button = $(this);-->
<!--        let product_id = _button.data('productId');-->
<!--        let directory = _button.data('directory');-->

<!--        _button.btnLoading();-->
<!--        $.ajax({-->
<!--            url: base_url + 'admin/addons/install',-->
<!--            type: "POST",-->
<!--            data: {-->
<!--                'addon': directory,-->
<!--                'product_id': product_id-->
<!--            },-->
<!--            dataType: 'json',-->

<!--            cache: false,-->

<!--            beforeSend: function() {-->
<!--                _button.btnLoading();-->
<!--            },-->
<!--            success: function(data) {-->

<!--                if (!data.status) {-->
<!--                    var message = "";-->

<!--                    errorMsg(data.msg);-->
<!--                } else {-->
<!--                    successMsg(data.msg);-->
<!--                    posts.init();-->
<!--                }-->
<!--            },-->
            error: function(xhr) { // if error occured
<!--                alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");-->
<!--                _button.btnReset();-->
<!--            },-->
<!--            complete: function() {-->
<!--                _button.btnReset();-->
<!--            }-->
<!--        });-->
<!--    });-->

<!--    $(document).on('click', '.uninstall', function(e) {-->
		
<!--		if (confirm('<?php echo $this->lang->line("are_you_sure"); ?>')) {-->
			
<!--			e.preventDefault();-->
<!--			let _button = $(this);-->
<!--			let product_id = _button.data('productId');-->
<!--			let directory = _button.data('directory');-->

<!--			_button.btnLoading();-->
<!--			$.ajax({-->
<!--				url: base_url + 'admin/addons/uninstall',-->
<!--				type: "POST",-->
<!--				data: {-->
<!--					'addon': directory,-->
<!--					'product_id': product_id-->
<!--				},-->
<!--				dataType: 'json',-->
	
<!--				cache: false,-->
	
<!--				beforeSend: function() {-->
<!--					_button.btnLoading();-->
<!--				},-->
<!--				success: function(data) {-->
	
<!--					if (!data.status) {-->
<!--						var message = "";-->
	
<!--						errorMsg(data.msg);-->
<!--					} else {-->
<!--						successMsg(data.msg);-->
<!--						posts.init();-->
<!--					}-->
<!--				},-->
				error: function(xhr) { // if error occured
<!--					alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");-->
<!--					_button.btnReset();-->
<!--				},-->
<!--				complete: function() {-->
<!--					_button.btnReset();-->
<!--				}-->
<!--			});-->
<!--		}-->
<!--    });-->
<!--</script>-->