

<link rel="stylesheet" href="https://kendo.cdn.telerik.com/2018.2.620/styles/kendo.common-material.min.css" />
    <link rel="stylesheet" href="https://kendo.cdn.telerik.com/2018.2.620/styles/kendo.material.min.css" />
    <link rel="stylesheet" href="https://kendo.cdn.telerik.com/2018.2.620/styles/kendo.material.mobile.min.css" />

    <script src="https://kendo.cdn.telerik.com/2018.2.620/js/jquery.min.js"></script>
    <script src="https://kendo.cdn.telerik.com/2018.2.620/js/kendo.all.min.js"></script>

 <style>
        .demo-section label {
            display: block;
            margin: 15px 0 5px 0;
        }
        #get {
            float: right;
            margin: 25px auto 0;
        }
    </style>



<?php echo message_box('success'); ?>
<?php echo message_box('error'); ?>
<?php
$all_customer_group = $this->db->where('type', 'client')->order_by('customer_group_id', 'DESC')->get('tbl_customer_group')->result();
$mdate = date('Y-m-d');
$last_7_days = date('Y-m-d', strtotime('today - 7 days'));
$all_goal_tracking = $this->client_model->get_permission('tbl_goal_tracking');

$all_goal = 0;
$wthout_all_goal = 0;
$direct_complete_achivement = 0;
$without_complete_achivement = 0;
if (!empty($all_goal_tracking)) {
    foreach ($all_goal_tracking as $v_goal_track) {
        $goal_achieve = $this->client_model->get_progress($v_goal_track, true);

        if ($v_goal_track->goal_type_id == 11) {

            if ($v_goal_track->end_date <= $mdate) { // check today is last date or not

                if ($v_goal_track->email_send == 'no') {// check mail are send or not
                    if ($v_goal_track->achievement <= $goal_achieve['achievement']) {
                        if ($v_goal_track->notify_goal_achive == 'on') {// check is notify is checked or not check
                            $this->client_model->send_goal_mail('goal_achieve', $v_goal_track);
                        }
                    } else {
                        if ($v_goal_track->notify_goal_not_achive == 'on') {// check is notify is checked or not check
                            $this->client_model->send_goal_mail('goal_not_achieve', $v_goal_track);
                        }
                    }
                }
            }
            $all_goal += $v_goal_track->achievement;
            $direct_complete_achivement += $goal_achieve['achievement'];
        }
        if ($v_goal_track->goal_type_id == 10) {

            if ($v_goal_track->end_date <= $mdate) { // check today is last date or not

                if ($v_goal_track->email_send == 'no') {// check mail are send or not
                    if ($v_goal_track->achievement <= $goal_achieve['achievement']) {
                        if ($v_goal_track->notify_goal_achive == 'on') {// check is notify is checked or not check
                            $this->client_model->send_goal_mail('goal_achieve', $v_goal_track);
                        }
                    } else {
                        if ($v_goal_track->notify_goal_not_achive == 'on') {// check is notify is checked or not check
                            $this->client_model->send_goal_mail('goal_not_achieve', $v_goal_track);
                        }
                    }
                }
            }
            $wthout_all_goal += $v_goal_track->achievement;
            $without_complete_achivement += $goal_achieve['achievement'];
        }
    }
}
// 30 days before

for ($iDay = 7; $iDay >= 0; $iDay--) {
    $date = date('Y-m-d', strtotime('today - ' . $iDay . 'days'));
    $where = array('date_added >=' => $date . " 00:00:00", 'date_added <=' => $date . " 23:59:59");
    $invoice_result[$date] = count($this->db->where($where)->get('tbl_client')->result());
}

$all_terget_achievement = $this->db->where(array('goal_type_id' => 11, 'start_date >=' => $last_7_days, 'end_date <=' => $mdate))->get('tbl_goal_tracking')->result();
$without_terget_achievement = $this->db->where(array('goal_type_id' => 10, 'start_date >=' => $last_7_days, 'end_date <=' => $mdate))->get('tbl_goal_tracking')->result();
if (!empty($all_terget_achievement)) {
    $all_terget_achievement = $all_terget_achievement;
} else {
    $all_terget_achievement = array();
}
if (!empty($without_terget_achievement)) {
    $without_terget_achievement = $without_terget_achievement;
} else {
    $without_terget_achievement = array();
}
$terget_achievement = array_merge($all_terget_achievement, $without_terget_achievement);
$total_terget = 0;
if (!empty($terget_achievement)) {
    foreach ($terget_achievement as $v_terget) {
        $total_terget += $v_terget->achievement;
    }
}

$curency = $this->client_model->check_by(array('code' => config_item('default_currency')), 'tbl_currencies');
if ($this->session->userdata('user_type') == 1) {
    $margin = 'margin-bottom:30px';
    ?>
    <div class="col-sm-12 bg-white p0" style="<?= $margin ?>">
        <div class="col-md-4">
            <div class="row row-table pv-lg">
                <div class="col-xs-6">
                    <p class="m0 lead"><?= ($all_goal) ?></p>
                    <p class="m0">
                        <small><?= lang('without_converted') ?></small>
                    </p>
                </div>
                <div class="col-xs-6">
                    <p class="m0 lead"><?= ($direct_complete_achivement) ?></p>
                    <p class="m0">
                        <small><?= lang('completed') . ' ' . lang('achievements') ?></small>
                    </p>
                </div>

            </div>
        </div>
        <div class="col-md-4">
            <div class="row row-table pv-lg">

                <div class="col-xs-6 ">
                    <p class="m0 lead"><?= ($wthout_all_goal) ?></p>
                    <p class="m0">
                        <small><?= lang('converted_client') ?></small>
                    </p>
                </div>
                <div class="col-xs-6">
                    <p class="m0 lead">

                        <?= $without_complete_achivement ?></p>
                    <p class="m0">
                        <small><?= lang('completed') . ' ' . lang('achievements') ?></small>
                    </p>
                </div>

            </div>

        </div>
        <div class="col-md-4">
            <div class="row row-table ">

                <div class="col-xs-6 pt">
                    <div data-sparkline="" data-bar-color="#23b7e5" data-height="60" data-bar-width="8"
                         data-bar-spacing="6" data-chart-range-min="0" values="<?php
                    if (!empty($invoice_result)) {
                        foreach ($invoice_result as $v_invoice_result) {
                            echo $v_invoice_result . ',';
                        }
                    }
                    ?>">
                    </div>
                    <p class="m0">
                        <small>
                            <?php
                            if (!empty($invoice_result)) {
                                foreach ($invoice_result as $date => $v_invoice_result) {
                                    echo date('d', strtotime($date)) . ' ';
                                }
                            }
                            ?>
                        </small>
                    </p>

                </div>
                <?php
                $total_goal = $all_goal + $wthout_all_goal;
                $complete_achivement = $direct_complete_achivement + $without_complete_achivement;
                if (!empty($tolal_goal)) {
                    if ($tolal_goal <= $complete_achivement) {
                        $total_progress = 100;
                    } else {
                        $progress = ($complete_achivement / $tolal_goal) * 100;
                        $total_progress = round($progress);
                    }
                } else {
                    $total_progress = 0;
                }
                ?>
                <div class="col-xs-6 text-center pt">
                    <div class="inline ">
                        <div class="easypiechart text-success"
                             data-percent="<?= $total_progress ?>"
                             data-line-width="5" data-track-Color="#f0f0f0"
                             data-bar-color="#<?php
                             if ($total_progress == 100) {
                                 echo '8ec165';
                             } elseif ($total_progress >= 40 && $total_progress <= 50) {
                                 echo '5d9cec';
                             } elseif ($total_progress >= 51 && $total_progress <= 99) {
                                 echo '7266ba';
                             } else {
                                 echo 'fb6b5b';
                             }
                             ?>" data-rotate="270" data-scale-Color="false"
                             data-size="50"
                             data-animate="2000">
                                                        <span class="small "><?= $total_progress ?>
                                                            %</span>
                            <span class="easypie-text"><strong><?= lang('done') ?></strong></span>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
<?php }

$id = $this->uri->segment(5);
$search_by = $this->uri->segment(4);
$created = can_action('4', 'created');
$edited = can_action('4', 'edited');
$deleted = can_action('4', 'deleted');
?>
<div class="row">
    <div class="col-sm-12">
        <div class="btn-group pull-right btn-with-tooltip-group _filter_data" data-toggle="tooltip"
             data-title="<?php echo lang('filter_by'); ?>">
            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown"
                    aria-haspopup="true" aria-expanded="false">
                <i class="fa fa-filter" aria-hidden="true"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-left"
                style="width:300px;<?php if (!empty($search_by) && $search_by == 'group') {
                    echo 'display:block';
                } ?>">
                <li class="<?php
                if (empty($search_by)) {
                    echo 'active';
                } ?>"><a
                        href="<?= base_url() ?>admin/client/manage_client"><?php echo lang('all'); ?></a>
                </li>
                <li class="divider"></li>
                <?php if (count($all_customer_group) > 0) { ?>
                    <li class="dropdown-submenu pull-left groups <?php if (!empty($id)) {
                        if ($search_by == 'group') {
                            echo 'active';
                        }
                    } ?>">
                        <a href="#" tabindex="-1"><?php echo lang('customer_group'); ?></a>
                        <ul class="dropdown-menu dropdown-menu-left"
                            style="<?php if (!empty($search_by) && $search_by == 'group') {
                                echo 'display:block';
                            } ?>">
                            <?php foreach ($all_customer_group as $group) {
                                ?>
                                <li class="<?php if (!empty($id)) {
                                    if ($search_by == 'group') {
                                        if ($id == $group->customer_group_id) {
                                            echo 'active';
                                        }
                                    }
                                } ?>">
                                    <a href="<?= base_url() ?>admin/client/manage_client/group/<?php echo $group->customer_group_id; ?>"><?php echo $group->customer_group; ?></a>
                                </li>
                            <?php }
                            ?>
                        </ul>
                    </li>
                    <div class="clearfix"></div>
                    <li class="divider"></li>
                <?php } ?>
            </ul>
        </div>
        <?php if (!empty($created) || !empty($edited)){ ?>
        <div class="nav-tabs-custom">
            <!-- Tabs within a box -->
            <ul class="nav nav-tabs">
                <li class="<?= $active == 1 ? 'active' : '' ?>"><a href="#client_list"
                                                                   data-toggle="tab">Contact list</a></li>
                <li class="<?= $active == 2 ? 'active' : '' ?>"><a href="#new_client"
                                                                   data-toggle="tab">New Contact</a></li>
                
                <li><a style="background-color: #1797be;color: #ffffff"
                       href="<?= base_url() ?>admin/client/import">Import Contact</a>
                </li>
            </ul>
            <div class="tab-content bg-white">
                <!-- Stock Category List tab Starts -->
                <div class="tab-pane <?= $active == 1 ? 'active' : '' ?>" id="client_list" style="position: relative;">
                    <?php } else { ?>
                    <div class="panel panel-custom">
                        <?php } ?>
                        <div class="box">
                            <table class="table table-striped DataTables " id="DataTables" cellspacing="0" width="100%">
                                <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Company Name</th>
                                    <th>Contact Type</th>
                                    <th>Date</th>
                                    <?php $show_custom_fields = custom_form_table(12, null);
                                    if (!empty($show_custom_fields)) {
                                        foreach ($show_custom_fields as $c_label => $v_fields) {
                                            if (!empty($c_label)) {
                                                ?>
                                                <th><?= $c_label ?> </th>
                                            <?php }
                                        }
                                    }
                                    ?>
                                    <th class="hidden-print"><?= lang('action') ?></th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                if (!empty($all_client_info)) {
                                    $u = 1;
                                    foreach ($all_client_info as $client_details) {
                                        ?>
                                        <tr>
                                            <td>
                                                <?php echo $u++;?>
                                            </td>
                                            <td>
                                                <?= $client_details->first_name.' '.$client_details->last_name ?>
                                            </td>
                                            <td>
                                                <?= $client_details->email ?>
                                            </td>
                                            <td><?= $client_details->phone; ?></td>
                                            <td>Company Name</td>
                                            <td>
                                                <?php if($client_details->contact_type ==1 ){echo 'Lead';}  ?>
                                                <?php if($client_details->contact_type ==2 ){echo 'Contact';}  ?>
                                                <?php if($client_details->contact_type ==3 ){echo 'Client';}  ?>
                                                <?php if($client_details->contact_type ==4 ){echo 'Ex-Client';}  ?>
                                                <?php if($client_details->contact_type ==5 ){echo 'Reffral Partner';}  ?>
                                                <?php if($client_details->contact_type ==6 ){echo 'Scope';}  ?>
                                            </td>
                                            <td><?php echo $client_details->contact_date;?></td>
                                            <?php 
                                            // $show_custom_fields = custom_form_table(12, $client_details->client_id);
                                            if (!empty($show_custom_fields)) {
                                                foreach ($show_custom_fields as $c_label => $v_fields) {
                                                    if (!empty($c_label)) {
                                                        ?>
                                                        <td><?= $v_fields ?> </td>
                                                    <?php }
                                                }
                                            }
                                            ?>
                                            <td>
                                                <?php if (!empty($edited)) { ?>
                                                    <?php echo btn_edit('admin/contact/manage_client/' . $client_details->id) ?>
                                                <?php }
                                                if (!empty($deleted)) {
                                                    ?>
                                                    <?php echo btn_delete('admin/contact/delete_client/' . $client_details->id) ?>
                                                <?php } ?>
                                                <?php echo btn_view('admin/contact/contact_details/' . $client_details->id) ?>
                                            </td>
                                        </tr>
                                        <?php
                                    }
                                } else {
                                    ?>
                                    <tr>
                                        <td colspan="9">
                                            <?= lang('no_data') ?>
                                        </td>
                                    </tr>
                                <?php }
                                ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <?php if (!empty($created) || !empty($edited)) { ?>
                        <div class="tab-pane <?= $active == 2 ? 'active' : '' ?>" id="new_client"
                        style="position: relative;">
                        <form role="form" enctype="multipart/form-data" id="form" data-parsley-validate="" novalidate=""
                              action="<?php echo base_url(); ?>admin/contact/save_client/<?php
                              if (!empty($client_info)) {
                                  echo $client_info->id;
                              }
                              ?>" method="post" class="form-horizontal contacts-form" >
                            <div class="panel-body">
                                <label class="control-label col-sm-3"></label>
                                <div class="col-sm-8">
                                    <div class="nav-tabs-custom">
                                        <!-- Tabs within a box -->
                                        <ul class="nav nav-tabs">
                                            <li class="active"><a href="#general"
                                                                  data-toggle="tab">General</a>
                                            </li>
                                            <li><a href="#sales_tab"
                                                   data-toggle="tab">Sales</a>
                                            </li>
                                            <li><a href="#relationship_info"
                                                   data-toggle="tab">Relationship Information</a>
                                            </li>
                                            <li><a href="#services_info" data-toggle="tab">Services Info</a></li>
                                            <li><a href="#access_control" data-toggle="tab">Access Control</a>
                                            </li>
                                        </ul>
                                        <div class="tab-content bg-white">
                                            <!-- ************** general *************-->
                                            <div class="chart tab-pane active" id="general">
                                                <?php 
                                                    if(!empty($client_info))
                                                    {
                                                        $where = array(
                                                            'user_id' => $client_info->id,
                                                            'user_type' => 'contact'
                                                        );
                                                        $additional_emails = $this->db->where($where)->get('tbl_additional_email')->result();
                                                        $additional_phones = $this->db->where($where)->get('tbl_additional_phone')->result();
                                                        $website_info = $this->db->where('contact_id',$client_info->id)->get('tbl_website_url')->result();
                                                    }
                                                ?>
                                                <div class="form-group">
                                                    <label class="col-lg-4 control-label">Contact type
                                                       </label>
                                                    <div class="col-lg-5">
                                                        <label class="control-label">
                                                        <?php if ($client_info->contact_type == 1){echo 'Lead';}?>
                                                        <?php if ($client_info->contact_type == 2){echo 'Contact';}?>
                                                        <?php if ($client_info->contact_type == 3){echo 'Client';}?>
                                                        <?php if ($client_info->contact_type == 4){echo 'Ex-Client';}?>
                                                        <?php if ($client_info->contact_type == 5){echo 'Reffral Partner';}?>
                                                        <?php if ($client_info->contact_type == 6){echo 'Scope';}?>
                                                        </label>
                                                    </div>
                                                </div>


                                                <div class="form-group">
                                                    <label class="col-lg-4 control-label">First Name
                                                       </label>
                                                    <div class="col-lg-5">
                                                    <label class="control-label">
                                                       <?php
                                                               if (!empty($client_info->first_name)) {
                                                                   echo $client_info->first_name;
                                                               }
                                                               ?>
                                                               </label>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="col-lg-4 control-label">Last Name
                                                        <span
                                                            class="text-danger"> *</span></label>
                                                    <div class="col-lg-5">
                                                    <label class="control-label">
                                                        <?php
                                                               if (!empty($client_info->last_name)) {
                                                                   echo $client_info->last_name;
                                                               }
                                                               ?>
                                                               </label>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="col-lg-4 control-label">Alias/Nickname
                                                        <span
                                                            class="text-danger"> *</span></label>
                                                    <div class="col-lg-5">
                                                    <label class="control-label">
                                                        <?php
                                                               if (!empty($client_info->nick_name)) {
                                                                   echo $client_info->nick_name;
                                                               }
                                                               ?>
                                                               </label>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="col-sm-4 control-label"><?= lang('password') ?><span
                                                            class="text-danger">*</span></label>
                                                    <div class="col-sm-5">
                                                        <label class="control-label">
                                                       <?php
                                                               if (!empty($client_info->password)) {
                                                                   echo $client_info->password;
                                                               }
                                                               ?>
                                                        </label>
                                                    </div>
                                                </div>
                                                
                                                <div class="form-group">
                                                    <label class="col-lg-4 control-label">Email
                                                        <span
                                                            class="text-danger"> *</span></label>
                                                    <div class="col-lg-5">
                                                    <label class="control-label">
                                                        <?php
                                                               if (!empty($client_info->email)) {
                                                                   echo $client_info->email;
                                                               }
                                                               ?>
                                                               </label>
                                                    </div>
                                                </div>
                                                <div class="form-group additional-email additional_icon">
                                                    <label class="col-lg-4 control-label">Additional Email</label>
                                               
                                                    <?php if(!empty($additional_emails)){
                                                        $j= 0 ;
                                                        foreach ($additional_emails as $item)
                                                        {
                                                    ?>
                                                    <div class="col-lg-5 d-flex">
                                                        <label class="control-label">
                                                       <?php
                                                               if (!empty($item->additional_email)) {
                                                                   echo $item->additional_email;
                                                               }
                                                               ?> - 
                                                            <?php if($item->type == 1){echo 'Home';} ?>
                                                            <?php if($item->type == 2){echo 'Work';} ?>
                                                            <?php if($item->type == 3){echo 'Other';}?>
                                                            </label>
                                                    </div>
                                                    <?php 
                                                        }
                                                        }
                                                    ?>

                                                </div>
                                               
                                                <div class="form-group">
                                                    <label
                                                        class="col-lg-4 control-label">Phone</label>
                                                    <div class="col-lg-5">
                                                    <label class="control-label">
                                                       <?php
                                                               if (!empty($client_info->phone)) {
                                                                   echo $client_info->phone;
                                                               }
                                                        ?>
                                                        </label>
                                                    </div>
                                                </div>
                                                <?php if(!empty($additional_phones)){
                                                        $i = 0;
                                                        foreach ($additional_phones as $item)
                                                        {
                                                    ?>

                                                <div class="form-group additional-phone additional_icon">
                                                    <label class="col-lg-4 control-label"><?php if($i ==0){echo 'Additional Phone';$i++;}?></label>
                                                    
                                                    <div class="col-lg-5 d-flex">
                                                    <label class="control-label">
                                                        <?php
                                                        if (!empty($item->additional_phone)) {
                                                            echo $item->additional_phone;
                                                        }
                                                        ?>
                                                            -
                                                           <?php if($item->type == 1){echo 'Home';} ?>
                                                           <?php if($item->type == 2){echo 'Work';} ?>
                                                           <?php if($item->type == 3){echo 'Other';} ?>
                                                           </label>
                                                        </div>
                                                </div>
                                                <?php }
                                                        }
                                                    ?>
                                                <div class="form-group">
                                                    <label
                                                        class="col-lg-4 control-label">Industries<span
                                                            class="text-danger"> *</span></label>
                                                    <div class="col-lg-5">
                                                        <label class="control-label">  SPA </label>
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label
                                                        class="col-lg-4 control-label">
                                                        <a href="#"
                                                           onclick="fetch_lat_long_from_google_cprofile(); return false;"
                                                           data-toggle="tooltip"
                                                           data-title="<?php echo lang('fetch_from_google') . ' - ' . lang('customer_fetch_lat_lng_usage'); ?>"><i
                                                                id="gmaps-search-icon" class="fa fa-google"
                                                                aria-hidden="true"></i></a>
                                                        <?= lang('latitude') . '( ' . lang('google_map') . ' )' ?>
                                                    </label>
                                                    <div class="col-lg-5">
                                                    <label class="control-label">
                                                        <?php
                                                        if (!empty($client_info->latitude)) {
                                                            echo $client_info->latitude;
                                                        }
                                                        ?>
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label
                                                        class="col-lg-4 control-label"><?= lang('longitude') . '( ' . lang('google_map') . ' )' ?></label>
                                                    <div class="col-lg-5">
                                                    <label class="control-label">
                                                        <?php
                                                               if (!empty($client_info->longitude)) {
                                                                   echo $client_info->longitude;
                                                               }
                                                               ?>
                                                               </label>
                                                    </div>
                                                </div>
                                                <input type="hidden" class="add-company-flag" name="add-company-flag" value="1">
                                                <?php
                                                if (!empty($client_info)) {
                                                    $client_id = $client_info->id;
                                                } else {
                                                    $client_id = null;
                                                }
                                                ?>
                                                <?= custom_form_Fields(12, $id); ?>
                                            </div><!-- ************** general *************-->
                                            
                            <div class="chart tab-pane" id="sales_tab">
                              <?php 
                              if(!empty($client_info)){
                                  $where = array(
                                      'contact_id' => $client_info->id
                                  );
                                  if(!empty($client_info->web))
                                  {
                                    $contact_web = $this->db->where($where)->get('tbl_contact_web')->result();
                                  }
                                  if(!empty($client_info->social_media))
                                  {
                                    $contact_social_media = $this->db->where($where)->get('tbl_contact_social_media')->result();
                                  }
                                  if(!empty($client_info->networking))
                                  {
                                    $contact_networking = $this->db->where($where)->get('tbl_contact_networking')->result();
                                  }
                                  if(!empty($client_info->cold_call))                                  
                                  {
                                    $contact_cold_call = $this->db->where($where)->get('tbl_contact_cold_call')->result();
                                  }

                                  if(!empty($client_info->sales_rep_check))
                                  {
                                    $contact_sales_rep = $this->db->where($where)->get('tbl_contact_sales_rep')->result();
                                  }
                                  if(!empty($client_info->request_proposal))
                                  {
                                    $contact_request_proposal = $this->db->where($where)->get('tbl_contact_request_proposal')->result();
                                  }
                              }
                              ?>
                                <div class="form-group">
                                        <label
                                            class="col-lg-4 control-label">White Label Brand</label>
                                        <div class="col-lg-5">
                                        <label class="control-label">
                                                    <?php if (!empty($whitebrand)): foreach ($whitebrand as $item): ?>
                                                        <?= (!empty($client_info->white_brand) && $client_info->white_brand == $item->white_brand ? $item->white_brand: NULL) ?>
                                                        
                                                    <?php 
                                                        endforeach;
                                                    endif;
                                                    ?>
                                                    </label>
                                        </div>
                                    </div>
                                                   
                                                    <!-- intereste Level -->
                                    <div class="form-group">
                                        <label
                                            class="col-lg-4 control-label">Interest Level</label>
                                        <div class="col-lg-5">
                                        <label class="control-label">
                                                <?= (!empty($client_info->interest_level) && $client_info->interest_level == '1' ? 'Not Applicable' : NULL) ?>
                                                <?= (!empty($client_info->interest_level) && $client_info->interest_level == '2' ? 'To Be Determined' : NULL) ?>
                                                <?= (!empty($client_info->interest_level) && $client_info->interest_level == '3' ? 'No Interest' : NULL) ?>
                                                <?= (!empty($client_info->interest_level) && $client_info->interest_level == '4' ? 'Low Interest' : NULL) ?>
                                                <?= (!empty($client_info->interest_level) && $client_info->interest_level == '5' ? 'Medium Interest' : NULL) ?>
                                                <?= (!empty($client_info->interest_level) && $client_info->interest_level == '6' ? 'high Interest' : NULL) ?>
                                        </label>
                                        </div>
                                    </div>
                                    <!-- sales rep -->
                                    <div class="form-group">
                                        <label
                                            class="col-lg-4 control-label">Sales Rep</label>
                                        <div class="col-lg-5">
                                        <label class="control-label">
                                                    <?php if (!empty($all_staffes)): foreach ($all_staffes as $staff): ?>
                                                            <?= (!empty($client_info->sales_rep) && $client_info->sales_rep == $staff->username ? $staff->username : NULL) ?>
                                                        <?php
                                                    endforeach;
                                                    endif;
                                                    ?>
                                                    </label>
                                        </div>
                                    </div>

                                    <!-- Referral Type and Source -->
                                    <?php 
                                            if(!empty($client_info->web)){?>
                                <div class="form-group">
                                    <div class="checkbox c-checkbox">
                                        <div class="col-lg-9 pull-right">
                                        <label class="needsclick col-lg-4">
                                            <large><?php 
                                            if(!empty($client_info->web)){echo 'Web';} ?>
                                           </large>
                                        </label>
                                        </div>
                                    </div>
                                    <div class="form-group child-settings <?php 
                                            if(empty($client_info->web)){echo 'display-none';}?>">
                                    <div class="col-lg-12 margin-t-5">
                                        <label class="col-lg-4 control-label">Website</label>
                                        <div class="col-lg-5">
                                        
                                             <?php if(!empty($contact_web)){echo $contact_web[0]->w_wbsite;} ?>
                                        </div>
                                    </div>
                                    <div class="col-lg-12 margin-t-5">
                                        <label class="col-lg-4 control-label">Google Organic</label>
                                        <div class="col-lg-5">
                                             <?php if(!empty($contact_web)){echo $contact_web[0]->w_google_organic;} ?>
                                        </div>
                                    </div>
                                    <div class="col-lg-12 margin-t-5">
                                        <label
                                                class="col-lg-4 control-label">Bing Organic</label>
                                        <div class="col-lg-5">
                                                 <?php if(!empty($contact_web)){echo $contact_web[0]->w_bing_organic;} ?>
                                        </div>
                                    </div>
                                    <div class="col-lg-12 margin-t-5">
                                        <label class="col-lg-4 control-label">Google Ads</label>
                                        <div class="col-lg-5">
                                                 <?php if(!empty($contact_web)){echo $contact_web[0]->w_google_ads;} ?>
                                        </div>
                                    </div>
                                    <div class="col-lg-12 margin-t-5">
                                        <label
                                                class="col-lg-4 control-label">Bing Ads</label>
                                        <div class="col-lg-5">
                                                 <?php if(!empty($contact_web)){echo $contact_web[0]->w_bing_ads;} ?>
                                        </div>
                                    </div>
                                    <div class="col-lg-12 margin-t-5">
                                        <label class="col-lg-4 control-label">Other</label>
                                        <div class="col-lg-5">
                                                 <?php if(!empty($contact_web)){echo $contact_web[0]->w_other;} ?>
                                        </div>
                                    </div>
                                </div>
                                </div>
                                <?php }?>

                                                
                                                <?php 
                                                            if(!empty($client_info->social_media)){?>
                                                <div class="form-group">
                                                    <div class="col-lg-9 pull-right">
                                                        <div class="checkbox c-checkbox">
                                                            <label class="needsclick col-lg-4">
                                                                <large><?php 
                                                            if(!empty($client_info->social_media)){echo 'Social Media';} ?>
                                                                </large>
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div class="form-group child-settings <?php 
                                            if(empty($client_info->social_media)){echo 'display-none';}?>">
                                                    <div class="col-lg-12 margin-t-5">
                                                        <label
                                                                class="col-lg-4 control-label">Facebook Organic</label>
                                                        <div class="col-lg-5">
                                                                <?php if(!empty($contact_social_media)){echo $contact_social_media[0]->s_facebook_or;} ?>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-12 margin-t-5">
                                                        <label
                                                                class="col-lg-4 control-label">Facebook Ad</label>
                                                        <div class="col-lg-5">
                                                            <?php if(!empty($contact_social_media)){echo $contact_social_media[0]->s_facebook_ad;} ?>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-12 margin-t-5">
                                                        <label
                                                                class="col-lg-4 control-label">Instagram Oragnic</label>
                                                        <div class="col-lg-5">
                                                           <?php if(!empty($contact_social_media)){echo $contact_social_media[0]->social_instagram_or;} ?>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-12 margin-t-5">
                                                        <label
                                                                class="col-lg-4 control-label">Instagram Ad</label>
                                                        <div class="col-lg-5">
                                                            <?php if(!empty($contact_social_media)){echo $contact_social_media[0]->social_instagram_ad;} ?>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-12 margin-t-5">
                                                        <label
                                                                class="col-lg-4 control-label">Pinterest Organic</label>
                                                        <div class="col-lg-5">
                                                            <?php if(!empty($contact_social_media)){echo $contact_social_media[0]->social_pinterest_or;} ?>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-12 margin-t-5">
                                                        <label
                                                                class="col-lg-4 control-label">Pinterest Ad</label>
                                                        <div class="col-lg-5">
                                                            <?php if(!empty($contact_social_media)){echo $contact_social_media[0]->social_pinterest_ad;} ?>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-12 margin-t-5">
                                                        <label
                                                                class="col-lg-4 control-label">YouTube Organic</label>
                                                        <div class="col-lg-5">
                                                            <?php if(!empty($contact_social_media)){echo $contact_social_media[0]->social_youtube_or;} ?>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-12 margin-t-5">
                                                        <label
                                                                class="col-lg-4 control-label">YouTube Ad</label>
                                                        <div class="col-lg-5">
                                                            <?php if(!empty($contact_social_media)){echo $contact_social_media[0]->social_youtube_ad;} ?>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-12 margin-t-5">
                                                        <label
                                                                class="col-lg-4 control-label">LinkedIn Oranic</label>
                                                        <div class="col-lg-5">
                                                           <?php if(!empty($contact_social_media)){echo $contact_social_media[0]->social_linkedin_or;} ?>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-12 margin-t-5">
                                                        <label
                                                                class="col-lg-4 control-label">LinkedIn Ad</label>
                                                        <div class="col-lg-5">
                                                            <?php if(!empty($contact_social_media)){echo $contact_social_media[0]->social_linkedin_ad;} ?>
                                                        </div>
                                                    </div>
                                                </div>
                                                                                                        
                                                </div>
                                                <?php }?>
 
                                                <?php 
                                                            if(!empty($client_info->networking)){?>
                                                <div class="form-group">
                                                    <div class="col-lg-9 pull-right">
                                                        <div class="checkbox c-checkbox">
                                                            <label class="needsclick col-lg-4">
                                                            <large><?php 
                                                            if(!empty($client_info->networking)){echo 'Networking';} ?>
                                                                </large>
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div class="form-group child-settings <?php 
                                            if(empty($client_info->networking)){echo 'display-none';}?>">
                                                    <div class="col-lg-12 margin-t-5">
                                                        <label
                                                                class="col-lg-4 control-label">Event or Group</label>
                                                        <div class="col-lg-5">
                                                                <?php if(!empty($contact_networking)){echo $contact_networking[0]->n_event_group;} ?>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-12 margin-t-5">
                                                        <label
                                                                class="col-lg-4 control-label">Referred By</label>
                                                        <div class="col-lg-5">
                                                                <?php if(!empty($contact_networking)){echo $contact_networking[0]->n_referred_by;} ?>
                                                        </div>
                                                    </div>
                                                </div>                                              
                                                </div>
                                                            <?php }?>


                                                <?php 
                                                            if(!empty($client_info->cold_call)){?>
                                                <div class="form-group">
                                                    <div class="col-lg-9 pull-right">
                                                        <div class="checkbox c-checkbox">
                                                            <label class="needsclick col-lg-4">
                                                            <large><?php 
                                                            if(!empty($client_info->cold_call)){echo 'Cold Call';} ?>
                                                                </large>
                                                            </label>
                                                        </div>
                                                    </div>  
                                                    <div class="form-group child-settings <?php 
                                            if(empty($client_info->cold_call)){echo 'display-none';}?>">
                                                    <div class="col-lg-12 margin-t-5">
                                                        <label
                                                                class="col-lg-4 control-label">Cold Call Type</label>
                                                        <div class="col-lg-5">
                                                                <?php if(!empty($contact_cold_call)){echo $contact_cold_call[0]->c_call_type;} ?>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-12 margin-t-5">
                                                        <label
                                                                class="col-lg-4 control-label">Referred By</label>
                                                        <div class="col-lg-5">
                                                           <?php if(!empty($contact_cold_call)){echo $contact_cold_call[0]->c_referred_by;} ?>
                                                        </div>
                                                    </div>
                                                   
                                                </div>                                                  
                                                </div>
                                                            <?php }?>
                                                            
                                                            <?php 
                                                            if(!empty($client_info->referral_note)){?>
                                                <div class="form-group">
                                                    <label
                                                        class="col-lg-4 control-label">Referral Notes</label>
                                                    <div class="col-lg-5">
                                                    <label class="control-label">
                                                        <?php 
                                                            if(!empty($client_info->referral_note)){echo $client_info->referral_note;} ?>
                                                            </label>
                                                    </div>
                                                </div>
                                                            <?php }?>

                                                <div class="form-group">
                                                    <label
                                                        class="col-lg-4 control-label">Date</label>
                                                    <div class="col-lg-5">
                                                        <div class="input-group">
                                                        <label class="control-label">
                                                           <?php
                                                                if (!empty($client_info->contact_date)) {
                                                                    echo $client_info->contact_date;
                                                                } else {
                                                                    echo date('Y-m-d');
                                                                }
                                                                ?>
                                                                </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div><!-- ************** Contact *************-->
                                            <div class="chart tab-pane" id="relationship_info">
                                                <?php 
                                                if(!empty($client_info))
                                                { 
                                                    $personal_info = $this->db->where('contact_id',$client_info->id)->get('tbl_personal_social')->result();
                                                    $child_info = $this->db->where('contact_id',$client_info->id)->get('tbl_family_child')->result();
                                                    $pet_info = $this->db->where('contact_id',$client_info->id)->get('tbl_family_pet')->result_array();
                                                }
                                                ?>
                                                <?php
                                                        if (!empty($client_info->homeaddress)) {?>
                                                <div class="form-group">
                                                    <label
                                                        class="col-lg-4 control-label">Home Address</label>
                                                    <div class="col-lg-5">
                                                    <label class="control-label">
                                                       <?php
                                                        if (!empty($client_info)) {
                                                            echo $client_info->homeaddress;
                                                        }
                                                        ?>
                                                        </label>
                                                    </div>
                                                </div>
                                                    <?php }?>
                                                <div class="form-group">
                                                    <label
                                                        class="col-lg-4 control-label">Birthday</label>
                                                    <div class="col-lg-5">
                                                        <div class="input-group">
                                                        <label class="control-label">
                                                            <?php
                                                                if (!empty($client_info->birthday)) {
                                                                    echo $client_info->birthday;
                                                                } else {
                                                                    echo date('Y-m-d');
                                                                }
                                                                ?>
                                                        </label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <?php
                                                        if (!empty($client_info->anniversary)) {?>
                                                <div class="form-group">
                                                    <label
                                                        class="col-lg-4 control-label">Anniversary</label>
                                                    <div class="col-lg-5">
                                                    <label class="control-label">
                                                        <?php
                                                        if (!empty($client_info->anniversary)) {
                                                            echo $client_info->anniversary;
                                                        }
                                                        ?>
                                                        </label>
                                                    </div>
                                                </div>
                                                    <?php }?>

                                                <div class="form-group">
                                                    <label
                                                        class="col-lg-4 control-label">Family</label>
                                                    <div class="col-lg-5">
                                                        <div class="col-lg-12 margin-t-5">
                                                            <label class="col-lg-4 control-label">Spouse</label>
                                                            <div class="col-lg-8">
                                                            <label class="control-label">
                                                                <?php
                                                                if (!empty($client_info->spouse)) {
                                                                    echo $client_info->spouse;
                                                                }
                                                                ?>
                                                                </label>
                                                            </div>
                                                        </div>
                                                        <?php if(!empty($child_info)){?>
                                                        <div class="col-lg-12 margin-t-5 additional_icon">
                                                            <label class="col-lg-4 control-label">Child</label>
                                                            <div class="col-lg-8">
                                                            <?php if(!empty($child_info)){$m=0;foreach($child_info as $item){?>
                                                                <div class="col-lg-12 margin-t-5 family-child d-flex nopaddingleft nopaddingright">
                                                                
                                                                    <?php echo $item->child;?>
                                                                    <?php echo '-'.$item->type;?>
                                                                </div>
                                                            <?php }}?>
                                                            </div>
                                                        </div>
                                                            <?php }?>
                                                            <?php if(!empty($pet_info)){?>
                                                        <div class="col-lg-12 margin-t-5 additional_icon">
                                                            <label class="col-lg-4 control-label">Pet</label>
                                                            <div class="col-lg-8">
                                                            <?php if(!empty($pet_info)){$i=0;foreach($pet_info as $item){?>
                                                                <div class="col-lg-12 margin-t-5 family-pet d-flex nopaddingleft nopaddingright">
                                                                    <?php echo $item['pet'];?> 
                                                                    <?php echo '-'.$item['type'];?>
                                                                </div>
                                                                <?php }}?>
                                                            </div>
                                                        </div>
                                                            <?php }?>
                                                            <?php
                                                                if (!empty($client_info->other)) {?>
                                                        <div class="col-lg-12 margin-t-5">
                                                            <label class="col-lg-4 control-label">Other</label>
                                                            <div class="col-lg-8">
                                                            <label class="control-label">
                                                                <?php
                                                                if (!empty($client_info->other)) {
                                                                    echo $client_info->other;
                                                                }
                                                                ?>
                                                                </label>
                                                            </div>
                                                        </div>
                                                            <?php }?>
                                                    </div>
                                                    
                                                </div>
                                                <div class="form-group">
                                                    <label
                                                        class="col-lg-4 control-label">Likes & Interests </label>
                                                    <div class="col-lg-5">
                                                    <label class="control-label">
                                                      <?php
                                                        if (!empty($client_info->likes_interest)) {
                                                            echo $client_info->likes_interest;
                                                        }
                                                        ?>
                                                        </label>
                                                    </div>
                                                </div>
                                                <?php
                                                        if (!empty($client_info->dislikes)) {?>
                                                <div class="form-group">
                                                    <label
                                                        class="col-lg-4 control-label">Dislike</label>
                                                    <div class="col-lg-5">
                                                    <label class="control-label">
                                                       <?php
                                                        if (!empty($client_info->dislikes)) {
                                                            echo $client_info->dislikes;
                                                        }
                                                        ?>
                                                        </label>
                                                    </div>
                                                </div>
                                                    <?php }?>
                                                <?php
                                                        if (!empty($client_info->biography)) {?>
                                                <div class="form-group">
                                                    <label
                                                        class="col-lg-4 control-label">Biography/Notes</label>
                                                    <div class="col-lg-5">
                                                    <label class="control-label">
                                                       <?php
                                                        if (!empty($client_info->biography)) {
                                                            echo $client_info->biography;
                                                        }
                                                        ?>
                                                        </label>
                                                    </div>
                                                </div>
                                                    <?php }?>
                                                <div class="form-group">
                                                    <label
                                                        class="col-lg-4 control-label">Personal Social Media Links</label>
                                                    <div class="col-lg-5">
                                                    <?php
                                                                if (!empty($personal_info[0]->personal_facebook)) {?>
                                                    <div class="col-lg-12 margin-t-5 nopaddingright">
                                                            <label
                                                            class="col-lg-4 control-label">Facebook</label>
                                                            <div class="col-lg-8 nopaddingright">
                                                            <label class="control-label">
                                                                <?php
                                                                if (!empty($personal_info[0]->personal_facebook)) {
                                                                    echo $personal_info[0]->personal_facebook;
                                                                }
                                                                ?>
                                                                </label>
                                                            </div>
                                                        </div>
                                                    <?php }
                                                                ?>
                                                        <?php
                                                                if (!empty($personal_info[0]->personal_instagram)) {?>
                                                        <div class="col-lg-12 margin-t-5 nopaddingright">
                                                            <label
                                                            class="col-lg-4 control-label">Instagram</label>
                                                            <div class="col-lg-8 nopaddingright">
                                                            <label class="control-label">
                                                                <?php
                                                                if (!empty($personal_info[0]->personal_instagram)) {
                                                                    echo $personal_info[0]->personal_instagram;
                                                                }
                                                                ?>
                                                                </label>
                                                            </div>
                                                        </div>
                                                            <?php }?>
                                                            <?php
                                                                if (!empty($personal_info[0]->personal_linkedin)) {?>
                                                        <div class="col-lg-12 margin-t-5 nopaddingright">
                                                            <label
                                                            class="col-lg-4 control-label">Linkedin</label>
                                                            <div class="col-lg-8 nopaddingright">
                                                            <label class="control-label">
                                                                <?php
                                                                if (!empty($personal_info[0]->personal_linkedin)) {
                                                                    echo $personal_info[0]->personal_linkedin;
                                                                }
                                                                ?>
                                                                </label>
                                                            </div>
                                                        </div>
                                                        <?php }?>
                                                        <?php
                                                                if (!empty($personal_info[0]->personal_pinterest)) {?>
                                                        <div class="col-lg-12 margin-t-5 nopaddingright">
                                                            <label
                                                            class="col-lg-4 control-label">Pinterest</label>
                                                            <div class="col-lg-8 nopaddingright">
                                                            <label class="control-label">
                                                                <?php
                                                                if (!empty($personal_info[0]->personal_pinterest)) {
                                                                    echo $personal_info[0]->personal_pinterest;
                                                                }
                                                                ?>
                                                                </label>
                                                            </div>
                                                        </div>
                                                            <?php }?>
                                                        <?php
                                                                if (!empty($personal_info[0]->personal_youtube)) {?>
                                                        <div class="col-lg-12 margin-t-5 nopaddingright">
                                                            <label
                                                            class="col-lg-4 control-label">Youtube</label>
                                                            <div class="col-lg-8 nopaddingright">
                                                            <label class="control-label">
                                                                <?php
                                                                if (!empty($personal_info[0]->personal_youtube)) {
                                                                    echo $personal_info[0]->personal_youtube;
                                                                }
                                                                ?>
                                                                </label>
                                                            </div>
                                                        </div>
                                                            <?php }?>
                                                            <?php
                                                                if (!empty($personal_info[0]->personal_other)) {?>
                                                        <div class="col-lg-12 margin-t-5 nopaddingright">
                                                            <label
                                                            class="col-lg-4 control-label">Other</label>
                                                            <div class="col-lg-8 nopaddingright">
                                                            <label class="control-label">
                                                                <?php
                                                                if (!empty($personal_info[0]->personal_other)) {
                                                                    echo $personal_info[0]->personal_other;
                                                                }
                                                                ?>
                                                                </label>
                                                            </div>
                                                        </div>
                                                        <?php }?>
                                                    </div>
                                                </div><!-- ************** Web *************-->
                                            <div class="chart tab-pane" id="services_info">
                            <?php 
                                if(!empty($client_info)){
                                    $relationship_develop = $this->db->where('contact_id',$client_info->id)->get('tbl_relationship_develop')->result();
                                }
                            ?>
                            <?php if(!empty($client_info)){?>
                                                <div class="form-group">
                                                        <label
                                                            class="col-lg-4 control-label">Client Code</label>
                                                        <div class="col-lg-5">
                                                        <label class="control-label">
                                                            <?php if(!empty($client_info)){echo $client_info->client_code;}?>
                                                            </label>
                                                        </div>
                                                </div>
                            <?php }?>
                            <?php
                                                        if (!empty($client_info->company_color)) {?>
                                                <div class="form-group">
                                                    <label
                                                        class="col-lg-4 control-label">Company Color</label>
                                                    <div class="col-lg-5">
                                                    <label class="control-label">
                                                        <?php
                                                        if (!empty($client_info->company_color)) {
                                                            echo $client_info->company_color;
                                                        }
                                                        ?>
                                                        </label>
                                                    </div>
                                                </div>
                                                    <?php }?>
                                                    <?php
                                                        if (!empty($client_info->seo_keyword)) {?>
                                                <div class="form-group">
                                                    <label
                                                        class="col-lg-4 control-label">SEO Keywords</label>
                                                    <div class="col-lg-5">
                                                    <label class="control-label">
                                                       <?php
                                                        if (!empty($client_info->seo_keyword)) {
                                                            echo $client_info->seo_keyword;
                                                        }
                                                        ?>
                                                        </label>
                                                    </div>
                                                </div>
                                                    <?php }?>

                                                <div class="form-group">
                                                    <label
                                                        class="col-lg-4 control-label">Adwords</label>
                                                    <div class="col-lg-5">
                                                    <?php
                                                        if (!empty($client_info->account_number)) {?>
                                                    <div class="col-lg-12 nopaddingright margin-t-5">
                                                        <label class="col-lg-6 control-label">Account Number</label>
                                                        <div class="col-lg-6 nopaddingright">
                                                        <label class="control-label">
                                                            <?php
                                                        if (!empty($client_info->account_number)) {
                                                            echo $client_info->account_number;
                                                        }
                                                        ?>
                                                        </label>
                                                        </div>
                                                    </div>
                                                    <?php }?>
                                                    <?php
                                                        if (!empty($client_info->monthly_budget)) {?>
                                                    <div class="col-lg-12 nopaddingright margin-t-5">
                                                        <label
                                                            class="col-lg-6 control-label">Monthly budget</label>
                                                        <div class="col-lg-6 nopaddingright">
                                                        <label class="control-label">
                                                            <?php
                                                        if (!empty($client_info->monthly_budget)) {
                                                            echo $client_info->monthly_budget;
                                                        }
                                                        ?>
                                                        </label>
                                                        </div>
                                                    </div>
                                                    <?php }?>
                                                    
                                                    <div class="col-lg-12 nopaddingright margin-t-5">
                                                        <label class="col-lg-6 control-label">Default schedule</label>
                                                        
                                                        <div class="col-lg-12 nopaddingright margin-t-5">
                                                        <?php
                                                        if (!empty($client_info->schedule_money)) {?>
                                                            <div class="col-lg-12 nopaddingright margin-t-5">
                                                                <label
                                                                class="col-lg-6 control-label">Monday</label>
                                                                <div class="col-lg-6 nopaddingright">
                                                                    <label class="control-label">
                                                                        <?php
                                                                            if (!empty($client_info->schedule_money)) {
                                                                                echo $client_info->schedule_money;
                                                                            }
                                                                            ?>
                                                                    </label>
                                                                </div>
                                                            </div>
                                                            <?php }?>
                                                            <?php
                                                        if (!empty($client_info->schedule_tuesday)) {?>
                                                            <div class="col-lg-12 nopaddingright margin-t-5">
                                                            <label class="col-lg-6 control-label">Tuesday</label>
                                                            <div class="col-lg-6 nopaddingright ">
                                                            <label class="control-label">
                                                               <?php
                                                                if (!empty($client_info->schedule_tuesday)) {
                                                                    echo $client_info->schedule_tuesday;
                                                                }
                                                                ?>
                                                        </label>
                                                            </div>
                                                        </div>
                                                            <?php }?>
                                                            <?php
                                                        if (!empty($client_info->schedule_wednesday)) {?>
                                                        <div class="col-lg-12 nopaddingright margin-t-5">
                                                            <label class="col-lg-6 control-label">Wednesday</label>
                                                            <div class="col-lg-6 nopaddingright">
                                                            <label class="control-label">
                                                               <?php
                                                        if (!empty($client_info->schedule_wednesday)) {
                                                            echo $client_info->schedule_wednesday;
                                                        }
                                                        ?>
                                                        </label>
                                                            </div>
                                                        </div>
                                                    <?php }?>
                                                    <?php
                                                        if (!empty($client_info->schedule_thursday)) {?>
                                                        <div class="col-lg-12 nopaddingright margin-t-5">
                                                            <label class="col-lg-6 control-label">Thursday</label>
                                                            <div class="col-lg-6 nopaddingright">
                                                            <label class="control-label">
                                                                <?php
                                                        if (!empty($client_info->schedule_thursday)) {
                                                            echo $client_info->schedule_thursday;
                                                        }
                                                        ?>
                                                        </label>
                                                            </div>
                                                        </div>
                                                    <?php }?>
                                                    <?php
                                                        if (!empty($client_info->schedule_friday)) {?>
                                                        <div class="col-lg-12 nopaddingright margin-t-5">
                                                            <label class="col-lg-6 control-label">Friday</label>
                                                            <div class="col-lg-6 nopaddingright">
                                                            <label class="control-label">
                                                                <?php
                                                        if (!empty($client_info->schedule_friday)) {
                                                            echo $client_info->schedule_friday;
                                                        }
                                                        ?>
                                                        </label>
                                                            </div>
                                                        </div>
                                                    <?php }?>
                                                    <?php
                                                        if (!empty($client_info->schedule_saturday)) {?>
                                                        <div class="col-lg-12 nopaddingright margin-t-5">
                                                            <label class="col-lg-6 control-label">Saturday</label>
                                                            <div class="col-lg-6 nopaddingright">
                                                            <label class="control-label">
                                                              <?php
                                                        if (!empty($client_info->schedule_saturday)) {
                                                            echo $client_info->schedule_saturday;
                                                        }
                                                        ?>
                                                        </label>
                                                            </div>
                                                        </div>
                                                    <?php }?>
                                                    <?php
                                                        if (!empty($client_info->schedule_sunday)) {?>
                                                        <div class="col-lg-12 nopaddingright margin-t-5">
                                                            <label class="col-lg-6 control-label">Sunday</label>
                                                            <div class="col-lg-6 nopaddingright">
                                                                <label class="control-label">
                                                                <?php
                                                                    if (!empty($client_info->schedule_sunday)) {
                                                                        echo $client_info->schedule_sunday;
                                                                    }
                                                                    ?>
                                                                </label>
                                                            </div>
                                                        </div>
                                                                <?php }?>
                                                        </div>
                                                        
                                                    </div>
                                                    <div class="col-lg-12 nopaddingright margin-t-5">
                                                        <label class="col-lg-6 control-label">Target areas</label>
                                                        <div class="col-lg-6 nopaddingright">
                                                        <label class="control-label">
                                                            <?php
                                                        if (!empty($client_info->target_areas)) {
                                                            echo $client_info->target_areas;
                                                        }
                                                        ?> 
                                                        </label>
                                                        </div>
                                                    </div>
                                                    <?php if (!empty($client_info->call_tracking_phone)) {?>
                                                    <div class="col-lg-12 nopaddingright margin-t-5">
                                                        <label class="col-lg-6 control-label">Call tracking phone number</label>
                                                        <div class="col-lg-6 nopaddingright">
                                                            <label class="control-label">
                                                            <?php
                                                            if (!empty($client_info->call_tracking_phone)) {
                                                                echo $client_info->call_tracking_phone;
                                                            }
                                                            ?> 
                                                            </label>
                                                        </div>
                                                    </div>
                                                        <?php }?>
                                                        <?php
                                                        if (!empty($client_info->nagative_keyword)) {?>
                                                    <div class="col-lg-12 nopaddingright margin-t-5">
                                                        <label class="col-lg-6 control-label">Nagative Keywords</label>
                                                        <div class="col-lg-6 nopaddingright">
                                                        <label class="control-label">
                                                            <?php
                                                        if (!empty($client_info->nagative_keyword)) {
                                                            echo $client_info->nagative_keyword;
                                                        }
                                                        ?> 
                                                        </label>
                                                        </div>
                                                    </div>
                                                    <?php }?>
                                                    <?php
                                                        if (!empty($client_info->adwords_notes)) {?>
                                                    <div class="col-lg-12 nopaddingright margin-t-5">
                                                        <label
                                                            class="col-lg-6 control-label">Adwords Notes</label>
                                                        <div class="col-lg-6 nopaddingright">
                                                            <label class="control-label">
                                                            <?php
                                                            if (!empty($client_info->adwords_notes)) {
                                                                echo $client_info->adwords_notes;
                                                            }
                                                            ?> 
                                                            </label>
                                                        </div>
                                                    </div>
                                                        <?php }?>
                                                    </div>
                                                </div>
                                                
                                                <div class="form-group">
                                                    <label class="col-lg-4 control-label">Content Marketing</label>
                                                    <div class="col-lg-5">
                                                    <?php
                                                                    if (!empty($client_info->articles_per_month)) {?>
                                                        <div class="col-lg-12 nopaddingright margin-t-5">
                                                            <label class="col-lg-6 control-label">Articles per month</label>
                                                            <div class="col-lg-6 nopaddingright">
                                                                <label class="control-label">
                                                                    <?php
                                                                    if (!empty($client_info->articles_per_month)) {
                                                                        echo $client_info->articles_per_month;
                                                                    }
                                                                    ?>
                                                                </label>
                                                            </div>
                                                        </div>
                                                                <?php }?>
                                                                <?php
                                                                    if (!empty($client_info->content_notes)) {?>
                                                        <div class="col-lg-12 nopaddingright margin-t-5">
                                                            <label
                                                                class="col-lg-6 control-label">Content Notes</label>
                                                            <div class="col-lg-6 nopaddingright">
                                                            <label class="control-label">
                                                                <?php
                                                            if (!empty($client_info->content_notes)) {
                                                                echo $client_info->content_notes;
                                                            }
                                                            ?> 
                                                            </label>
                                                            </div>
                                                        </div>
                                                        <?php }?>
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                        <label class="col-lg-4 control-label">Social Media</label>
                                                    <div class="col-lg-5">
                                                    <?php if (!empty($client_info->strategy)) {?>
                                                        <div class='col-lg-12 margin-t-5 nopaddingright'>
                                                            <label class="col-lg-6 control-label">Strategy</label>
                                                            <div class="col-lg-6 nopaddingright">
                                                                <label class="control-label">
                                                                    <?php
                                                                    if (!empty($client_info->strategy)) {
                                                                        echo $client_info->strategy;
                                                                    }
                                                                    ?>
                                                                </label>
                                                            </div>
                                                        </div>
                                                    <?php }?>
                                                       <?php 
                                                    if(!empty($relationship_develop))
                                                    {
                                                       foreach($relationship_develop as $item)
                                                       {
                                                        ?>
                                                        <div class="relationship-develop">
                                                            <?php if($item->name == ''){?>
                                                            <div class="col-lg-12 margin-t-5 nopaddingright">
                                                                <label class="col-lg-4 control-label"></label>
                                                                <div class="col-lg-8 nopaddingright">
                                                                    <label class="col-lg-4 control-label">Name</label>
                                                                    <div class="col-lg-8 nopaddingright">
                                                                    <label class="control-label">
                                                                       <?php echo $item->name;?>
                                                                       </label>
                                                                    </div>
                                                                </div>
                                                            <?php }?>
                                                            </div>

                                                                <?php if($item->profile_link == ''){?>
                                                            <div class="col-lg-12 margin-t-5 nopaddingright">
                                                                <label class="col-lg-4 control-label"></label>
                                                                <div class="col-lg-8 nopaddingright ">
                                                                    <label class="col-lg-4 control-label">Profile Link</label>
                                                                    <div class="col-lg-8 nopaddingright">
                                                                        <label class="control-label">
                                                                        <?php echo $item->profile_link;?>
                                                                        </label>
                                                                    </div>
                                                                </div>

                                                            </div>
                                                                <?php }?>
                                                                <?php if($item->notes == ''){?>
                                                            <div class="col-lg-12 margin-t-5 nopaddingright">
                                                                <label class="col-lg-4 control-label"></label>
                                                                <div class="col-lg-8 nopaddingright">
                                                                    <label class="col-lg-4 control-label">Notes</label>
                                                                    <div class="col-lg-8 nopaddingright">
                                                                       <?php echo $item->notes;?>
                                                                    </div>
                                                                </div>

                                                            </div>
                                                                <?php }?>
                                                        </div>
                                                       <?php }
                                                       }
                                                       ?>
                                                        </div>
                                                    </div>
                                                
                                                
                                            </div><!-- ************** Hosting *************-->
                                            <div class="chart tab-pane" id="access_control">

                                <?php if (!empty($client_info))
                                {
                                    $access_info = $this->db->where('contact_id',$client_info->id)->get('tbl_access_info')->result();
                                }?>
                                    <div class="form-group">
                                        <label class="col-lg-4 control-label">Name
                                            <span class="text-danger"> *</span></label>
                                        <div class="col-lg-5">
                                        <label class="control-label">
                                            <?php
                                                if (!empty($client_info->access_name)) {
                                                    echo $client_info->access_name;
                                                }
                                                ?>
                                                </label>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-lg-4 control-label">Login URL
                                            <span class="text-danger"> *</span></label>
                                        <div class="col-lg-5">
                                        <label class="control-label">
                                            <?php
                                                if (!empty($client_info->access_login_url)) {
                                                    echo $client_info->access_login_url;
                                                }
                                                ?>
                                                </label>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-lg-4 control-label">User
                                            <span class="text-danger"> +</span></label>
                                        <?php if(!empty($access_info)){
                                            foreach ($access_info as $item)
                                            {
                                        ?>
                                            
                                        <div>
                                            <div class="col-lg-5">
                                                <div class="col-lg-12 margin-t-5 nopaddingright">
                                                    <label class="col-lg-4 control-label">Username</label>
                                                    <div class="col-lg-8 nopaddingright">
                                                    <label class="control-label">
                                                        <?php
                                                            if (!empty($item->access_username)) {
                                                                echo $item->access_username;
                                                            }
                                                            ?>
                                                            </label>
                                                    </div>
                                                </div>
                                                <div class="col-lg-12 margin-t-5 nopaddingright">
                                                    <label class="col-lg-4 control-label">Associated Email Address</label>
                                                    <div class="col-lg-8 nopaddingright">
                                                    <label class="control-label">
                                                        <?php
                                                            if (!empty($item->access_email_address)) {
                                                                echo $item->access_email_address;
                                                            }
                                                            ?>
                                                            </label>
                                                    </div>
                                                </div>
                                                <div class="col-lg-12 margin-t-5 nopaddingright">
                                                    <label class="col-lg-4 control-label">Password</label>
                                                    <div class="col-lg-8 nopaddingright">
                                                    <label class="control-label">
                                                      <?php
                                                            if (!empty($item->access_password)) {
                                                                echo $item->access_password;
                                                            }
                                                            ?>
                                                            </label>
                                                    </div>
                                                </div>
                                                <div class="col-lg-12 margin-t-5 nopaddingright">
                                                    <label class="col-lg-4 control-label">Who can view this</label>
                                                    <div class="col-lg-8 nopaddingright">
                                                        <?php
                                                            if (!empty($item->access_view)) {
                                                                echo $item->access_view;
                                                            }
                                                            ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <?php 
                                            }
                                        }
                                       
                                        ?>
                                    
                                    </div>
                                </div>

                                        </div>
                                    </div><!-- /.nav-tabs-custom -->
                                   
                                </div>

                            
                        </form>
                    <?php } else { ?>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    function fetch_lat_long_from_google_cprofile() {
        var data = {};
        data.address = $('textarea[name="address"]').val();
        data.city = $('input[name="city"]').val();
        data.country = $('select[name="country"] option:selected').text();
        console.log(data);
        $('#gmaps-search-icon').removeClass('fa-google').addClass('fa-spinner fa-spin');
        $.post('<?= base_url()?>admin/global_controller/fetch_address_info_gmaps', data).done(function (data) {
            data = JSON.parse(data);
            $('#gmaps-search-icon').removeClass('fa-spinner fa-spin').addClass('fa-google');
            if (data.response.status == 'OK') {
                $('input[name="latitude"]').val(data.lat);
                $('input[name="longitude"]').val(data.lng);
            } else {
                if (data.response.status == 'ZERO_RESULTS') {
                    toastr.warning("<?php echo lang('g_search_address_not_found'); ?>");
                } else {
                    toastr.warning(data.response.status);
                }
            }
        });
    }
    $(document).ready(function() {
            $('.new-company-btn').click(function(){
                console.log($(this).parents('.form-group').find('select'));
                if($('.new-company').css('display') == 'none'){
                    $('.new-company').show();
                    $(this).parents('.form-group').find('select').attr('disabled','disabled');
                    $('.new-company-btn').show();
                    $(this).hide();
                    $('.add-company-flag').val('2');
                }
                else{
                    $('.new-company').hide();
                    $(this).parents('.form-group').find('select').removeAttr('disabled');
                    $('.new-company-btn').show();
                    $(this).hide();
                    $('.add-company-flag').val('1');
                }
            });
            // create MultiSelect from select HTML element
            var optional = $("#optional").kendoMultiSelect({
                autoClose: false
            }).data("kendoMultiSelect");

            $("#get").click(function() {
                alert("Attendees:\n\nRequired: " + required.value() + "\nOptional: " + optional.value());
            });
            $('.needsclick input').change(function(){
                var child = $(this).parents('.form-group').find('.child-settings');
                if(child.css('display')=='none')
                {
                    $(this).parents('.form-group').find('.child-settings').show();
                }
                else{
                    $(this).parents('.form-group').find('.child-settings').hide();
                }
                
            });
        });

        function preview_image() 
        {
            var total_file=document.getElementById("upload_file").files.length;
            for(var i=0;i<total_file;i++)
            {
                $('#image_preview').append("<img class='col-lg-3' src='"+URL.createObjectURL(event.target.files[i])+"'><br>");
            }
        }
</script>
