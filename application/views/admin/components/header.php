<?php
$user_id = $this->session->userdata('user_id');
$profile_info = $this->db->where('user_id', $user_id)->get('tbl_account_details')->row();
$user_info = $this->db->where('user_id', $user_id)->get('tbl_users')->row();
?>
<header class="topbar topnavbar-wrapper">
            <nav class="navbar top-navbar navbar-expand-md navbar-dark">
                <!-- ============================================================== -->
                <!-- Logo -->
                <!-- ============================================================== -->
                
                <!-- ============================================================== -->
                <!-- End Logo -->
                <!-- ============================================================== -->
                <div class="navbar-wrapper">
                    <!-- ============================================================== -->
                    <!-- toggle and nav items -->
                    <!-- ============================================================== -->
                    
                    <ul class="nav navbar-nav navbar-nav-menu">
                        <!-- This is  -->
                        <li class="nav-item"> <a class="nav-link nav-toggler d-block d-md-none waves-effect waves-dark" href="javascript:void(0)"><i class="ti-menu"></i></a> </li>
                        <li class="nav-item"> <a class="nav-link sidebartoggler d-none d-lg-block d-md-block waves-effect waves-dark" href="javascript:void(0)"><i class="icon-menu"></i></a> </li>
                        <!-- ============================================================== -->
                        <!-- Search -->
                        <!-- ============================================================== -->
                        
                    </ul>
                    <div style="float: left;height: 100%;color: white;font-size: 1.8em;line-height: 53px;" class="navbar-header-time">
                    <small class="text-sm">
                                &nbsp;<?php echo lang(date('l')) . ' ' . lang(date('jS')) . ' ' . lang(date('F')) . ' ' . date('\- Y,'); ?>
                                &nbsp;<?= lang('time') ?>
                                &nbsp;<span id="txt"></span></small>
                        </div>        
                    <!-- ============================================================== -->
                    <!-- User profile and search -->
                    <!-- ============================================================== -->
                    <ul class="nav navbar-nav navbar-right">
                        <!-- ============================================================== -->
                        <!-- Comment -->
                        <!-- ============================================================== -->
                        <li class="dropdown dropdown-list notifications">
                            <a href="<?= base_url()?>admin/dashboard">
                            <i class="fas fa-tachometer-alt"></i>    
                            </a>
                        </li>
                        <li class="dropdown dropdown-list notifications">
                        <a href="<?= base_url()?>chat/conversations">
                            <i class="far fa-comments"></i>   
                            </a>
                        </li>
                        <li class="dropdown dropdown-list notifications">
                        <a href="<?= base_url()?>admin/announcements">
                            <i class="fas fa-bullhorn"></i>    
                            </a>
                        </li>
                        <li class="dropdown dropdown-list notifications">
                        <a href="<?= base_url()?>admin/mailbox">
                            <i class="far fa-envelope"></i>    
                            </a>
                        </li>
                        <li class="dropdown dropdown-list notifications">
                        <a href="<?= base_url()?>admin/filemanager">
                            <i class="far fa-folder-open"></i> 
                            </a>
                        </li>
                        <li class="dropdown dropdown-list notifications">
                        <a href="<?= base_url()?>admin/calendar">
                            <i class="far fa-calendar-alt"></i>   
                            </a>
                        </li>
                        <li class="dropdown dropdown-list notifications">
                        <a href="<?= base_url()?>admin/settings/contacts">
                            <i class="fas fa-address-book"></i>
                            </a>
                        </li>

                <!-- START Alert menu-->
                        <li class="dropdown dropdown-list notifications">
                            <?php $this->load->view('admin/components/notifications'); ?>
                        </li>

                       
                        <!-- ============================================================== -->
                        <!-- End Comment -->
                        <!-- ============================================================== -->
                        <!-- ============================================================== -->
                        <!-- Messages -->
                        <!-- ============================================================== -->
                        
                        <!-- ============================================================== -->
                        <!-- End Messages -->
                        <!-- ============================================================== -->
                        <!-- ============================================================== -->
                        <!-- mega menu -->
                        <!-- ============================================================== -->
                        
                        <!-- ============================================================== -->
                        <!-- End mega menu -->
                        <!-- ============================================================== -->
                        <!-- ============================================================== -->
                        <!-- User Profile -->
                        <!-- ============================================================== -->
                        <li class="nav-item dropdown u-pro">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                <span class="hidden-xs"><?= $profile_info->fullname ?></span>
                            </a>
                            
                            <div class="dropdown-menu dropdown-menu-right animated flipInY">
                                <!-- text-->
                                <a href="<?= base_url('admin/user/user_details/' . $user_id) ?>" class="dropdown-item"><i class="ti-user"></i> My Profile</a>
                                <!-- text-->
                                <!-- text-->
                                <a href="<?= base_url('admin/mailbox') ?>" class="dropdown-item"><i class="ti-email"></i> Inbox</a>
                                <!-- text
                                <div class="dropdown-divider"></div>
                                text-->
                                <!-- <a href="javascript:void(0)" class="dropdown-item"><i class="ti-settings"></i> Account Setting</a> -->
                                <!-- text-->
                                <!-- text-->
                                <a href="<?php echo base_url() ?>login/logout" class="dropdown-item"><i class="fa fa-power-off"></i> Logout</a>
                                <!-- text-->
                            </div>
                        </li>
                        <!-- ============================================================== -->
                        <!-- End User Profile -->
                        <!-- ============================================================== -->
                        <li class="nav-item right-side-toggle"> 
                            <a class="nav-link  waves-effect waves-light" href="<?php echo base_url() ?>admin/settings">
                                <i class="ti-settings"></i>
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>
        </header>