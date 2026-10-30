<!-- Content Wrapper. Contains page content -->
<!-- Main content -->
    <div class="row">
            <!-- left column -->
            <div class="col-md-12">
                <!-- general form elements -->
                <div class="card" id="holist">
                    <?php 
						if ($this->session->flashdata('msg')) { 
							echo $this->session->flashdata('msg');
                            $this->session->unset_userdata('msg');                                 
						}
					?>
                    <div class="card-header ptbnull d-flex align-items-center flex-wrap gap-2">
                        <h3 class="card-title titlefix"><?php echo $this->lang->line('page_list'); ?></h3>
                            <div class="ms-auto d-flex gap-1 flex-wrap">
                                <?php if ($this->rbac->hasPrivilege('pages', 'can_add')) {   ?>
                                <div class="btn-group">
                                    <a href="<?php echo site_url('admin/front/page/create'); ?>" class="btn btn-primary btn-sm front-btn-split-left"><?php echo $this->lang->line('add_page'); ?></a>
                                    <button type="button" class="btn btn-primary dropdown-toggle btn-sm front-btn-split-right" data-bs-toggle="dropdown">
                                        <span class="caret"></span>
                                    </button>
                                    <ul class="dropdown-menu" role="menu">
                                    <?php if ($this->rbac->hasPrivilege('event', 'can_view')) { ?>
                                    <li><a class="dropdown-item" href="<?php echo base_url(); ?>admin/front/events"><?php echo $this->lang->line('add_event'); ?></a></li>
                                    <?php }if ($this->rbac->hasPrivilege('gallery', 'can_view')) { ?>
                                        <li><a class="dropdown-item" href="<?php echo base_url(); ?>admin/front/gallery"><?php echo $this->lang->line('add_gallery'); ?></a></li>
                                    <?php }if ($this->rbac->hasPrivilege('notice', 'can_view')) { ?>
                                        <li><a class="dropdown-item" href="<?php echo base_url(); ?>admin/front/notice"><?php echo $this->lang->line('add_news'); ?></a></li>
                                    <?php } ?>
                                    </ul>
                                </div>
                                <?php }if ($this->rbac->hasPrivilege('media_manager', 'can_view')) {?>
									<a href="<?php echo site_url('admin/front/media'); ?>" class="btn btn-sm btn-primary"><i class="fa fa-reorder"></i> <?php echo $this->lang->line('media_manager'); ?></a>
                                <?php }if ($this->rbac->hasPrivilege('menus', 'can_view')) {?>
									<a href="<?php echo site_url('admin/front/menus'); ?>" class="btn btn-sm btn-primary"><i class="fa fa-reorder"></i> <?php echo $this->lang->line('menus'); ?></a>
                                <?php }?>
                                <?php /* Banners button hidden
                                if ($this->rbac->hasPrivilege('banner_images', 'can_view')) {?>
									<a href="<?php echo site_url('admin/front/banner'); ?>" class="btn btn-sm btn-primary"><i class="fa fa-reorder"></i> <?php echo $this->lang->line('banners'); ?></a>
                                <?php }*/?>
                            </div>
                    </div><!-- /.card-header -->
                    <div class="card-body">
                        <div class="mailbox-controls">
                            <div class="float-end">
                            </div><!-- /.float-end -->
                        </div>
                        <div class="table-responsive mailbox-messages">
                            <div class="download_label"><?php echo $this->lang->line('pages'); ?></div>
                            <table class="table table-striped table-bordered table-hover example" >
                                <thead>
                                    <tr>
                                        <th><?php echo $this->lang->line('title'); ?></th>
                                        <th><?php echo $this->lang->line('url'); ?></th>
                                        <th><?php echo $this->lang->line('page_type'); ?></th>
                                        <th class="text-end noExport"><?php echo $this->lang->line('action'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($listPages)) {  ?>
									
									<?php
									} else {										
										$count = 1;
										foreach ($listPages as $page) {
									?>
                                    <tr id="<?php echo (int)$page["id"]; ?>">
										<td class="mailbox-name"><a href="#" ><?php echo html_escape($page['title']) ?></a></td>
                                        <td class="mailbox-name"><a href="<?php echo html_escape(base_url() . $page['url']) ?>" target="_blank"><?php echo html_escape(base_url() . $page['url']) ?></a></td>
                                        <td class="mailbox-name">
											<?php if ($page['content_type'] == "gallery") {  ?>
											<span class="label bg-green"><?php echo $this->lang->line($page['content_type']); ?></span>
                                            <?php } elseif ($page['content_type'] == "events") {  ?>
                                            <span class="badge bg-info text-dark"><?php echo $this->lang->line('event'); ?></span>
                                            <?php } elseif ($page['content_type'] == "notice") {  ?>
                                            <span class="badge bg-warning text-dark"><?php echo $this->lang->line($page['content_type']); ?></span>
                                            <?php } else {  ?>
                                            <span class="badge bg-secondary"><?php echo $this->lang->line('standard'); ?></span>
                                            <?php }?>
										</td>
                                        <td class="text-end noExport">
                                            <div class="d-inline-flex gap-1">
                                            <?php if ($this->rbac->hasPrivilege('pages', 'can_edit')) { ?>
                                                <a href="<?php echo site_url('admin/front/page/edit/' . $page['slug']); ?>" class="btn btn-sm btn-outline-primary" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('edit'); ?>"><i class="fa fa-pencil"></i></a>
                                            <?php } if ($this->rbac->hasPrivilege('pages', 'can_delete')) {   if ($page['page_type'] != "default") {    ?>
                                                <a class="btn btn-sm btn-outline-danger" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('delete'); ?>" onclick="delete_recordByIdReload('<?php echo 'admin/front/page/delete/' . html_escape($page['slug']); ?>', '<?php echo $this->lang->line('delete_message'); ?>')"><i class="fa fa-trash"></i></a>
                                            <?php } } ?>
                                            </div>
                                        </td>
									</tr>
                                    <?php }
										$count++;
									}
									?>
                                </tbody>
                            </table><!-- /.table -->
                        </div><!-- /.mail-box-messages -->
                    </div><!-- /.card-body -->
                </div>
            </div><!--/.col (left) -->
        </div>
    

