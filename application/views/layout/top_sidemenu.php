<ul class="sessionul fixedmenu" style="display: none;">
    <li class="dropdown">
        <a class="dropdown-toggle drop5" data-bs-toggle="dropdown" href="#" aria-expanded="false">
            <span><?php echo $this->lang->line('quick_links'); ?></span> <i class="fas fa-th ms-auto"></i>
        </a>
        <ul class="dropdown-menu verticalmenu top-side-vertical-menu">
            <?php 
            if ($this->rbac->hasPrivilege('income', 'can_add')) { ?>
			
                <li role="presentation"><a class="top-side-menu-item" role="menuitem" tabindex="-1" href="<?php echo base_url(); ?>admin/income"> &nbsp;<i class="fa fa-usd"></i> <?php echo $this->lang->line('add_income'); ?></a></li>
				
            <?php } if ($this->rbac->hasPrivilege('expense', 'can_view')) {?> 
                <li role="presentation"><a class="top-side-menu-item" role="menuitem" tabindex="-1" href="<?php echo base_url(); ?>admin/expense"><i class="fa fa-credit-card"></i><?php echo $this->lang->line('add_expense'); ?></a></li>
				
            <?php } if ($this->rbac->hasPrivilege('staff_attendance', 'can_view')) {  ?>
			
                <li role="presentation"><a class="top-side-menu-item" role="menuitem" tabindex="-1" href="<?php echo base_url(); ?>admin/staffattendance"><i class="fa fa-calendar-check-o"></i><?php echo $this->lang->line('staff_attendance'); ?></a></li>
				
            <?php } if ($this->rbac->hasPrivilege('staff', 'can_view')) { ?>
			
                <li role="presentation"><a class="top-side-menu-item" role="menuitem" tabindex="-1" href="<?php echo base_url(); ?>admin/staff"><i class="fa fa-calendar-check-o"></i><?php echo $this->lang->line('staff_directory'); ?></a></li>
				
            <?php } if ($this->rbac->hasPrivilege('admission_enquiry', 'can_view')) {?>
			
                <li role="presentation"><a class="top-side-menu-item" role="menuitem" tabindex="-1" href="<?php echo base_url(); ?>admin/enquiry"><i class="fa fa-calendar-check-o"></i><?php echo $this->lang->line('admission_enquiry'); ?></a></li>
				
            <?php } if ($this->rbac->hasPrivilege('complaint', 'can_view')) { ?>
			
                <li role="presentation"><a class="top-side-menu-item" role="menuitem" tabindex="-1" href="<?php echo base_url(); ?>admin/complaint"><i class="fa fa-calendar-check-o"></i><?php echo $this->lang->line('complain'); ?></a></li>
				
            <?php } if ($this->rbac->hasPrivilege('upload_content', 'can_view')) {?>
			
                <li role="presentation"><a class="top-side-menu-item" role="menuitem" tabindex="-1" href="<?php echo base_url(); ?>admin/content"><i class="fa fa-download"></i><?php echo $this->lang->line('upload_content'); ?></a></li>
				
            <?php } if ($this->rbac->hasPrivilege('item_stock', 'can_add')) {  ?>
			
                <li role="presentation"><a class="top-side-menu-item" role="menuitem" tabindex="-1" href="<?php echo base_url(); ?>admin/itemstock"><i class="fa fa-object-group"></i><?php echo $this->lang->line('add_item_stock'); ?></a></li>
				
            <?php } if ($this->rbac->hasPrivilege('notice_board', 'can_view')) {  ?>
			
                <li role="presentation"><a class="top-side-menu-item" role="menuitem" tabindex="-1" href="<?php echo base_url(); ?>admin/notification"><i class="fa fa-bullhorn"></i><?php echo $this->lang->line('notice_board'); ?></a></li>
				
            <?php } if ($this->rbac->hasPrivilege('email_sms', 'can_view')) {  ?>
			
                <li role="presentation"><a class="top-side-menu-item" role="menuitem" tabindex="-1" href="<?php echo base_url(); ?>admin/mailsms/compose"><i class="fa fa-envelope-o"></i><?php echo $this->lang->line('send_email_/_sms'); ?></a></li>
				
            <?php }?>
        </ul>
    </li>
</ul>
