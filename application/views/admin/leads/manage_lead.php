

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

// 30 days before

$curency = $this->client_model->check_by(array('code' => config_item('default_currency')), 'tbl_currencies');
    ?>
   
<?php 
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
                        href="<?= base_url() ?>admin/leads/manage_lead"><?php echo lang('all'); ?></a>
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
                                    <a href="<?= base_url() ?>admin/leads/manage_lead/group/<?php echo $group->customer_group_id; ?>"><?php echo $group->customer_group; ?></a>
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
                <li class="<?= $active == 1 ? 'active' : '' ?>"><a href="<?= base_url()?>/admin/leads/manage_lead"
                                                                   >Lead list</a></li>
                <li class="<?= $active == 2 ? 'active' : '' ?>"><a href="<?= base_url()?>/admin/leads/manage_lead/new"
                                                                 >New Lead</a></li>
                
                <li><a style="background-color: #03a9f3;color: #ffffff"
                       href="<?= base_url() ?>admin/leads/import">Import Lead</a>
                </li>
            </ul>
            <div class="tab-content bg-white">
                <!-- Stock Category List tab Starts -->
                <div class="tab-pane <?= $active == 1 ? 'active' : '' ?>" id="client_list" style="position: relative;">
                    <?php } else { ?>
                    <div class="panel panel-custom">
                        <header class="panel-heading ">
                            <div class="panel-title"><strong><?= lang('client_list') ?></strong></div>
                        </header>
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
                                            <?php
                                                               if (!empty($client_details->contact_type) && $client_details->contact_type == 1) {
                                                                   echo 'Lead';
                                                               }
                                                               ?>
                                            <?php
                                                               if (!empty($client_details->contact_type) && $client_details->contact_type == 2) {
                                                                   echo 'Contact';
                                                               }
                                                               ?>
                                            <?php
                                                               if (!empty($client_details->contact_type) && $client_details->contact_type == 3) {
                                                                   echo 'Client';
                                                               }
                                                               ?>
                                            <?php
                                                               if (!empty($client_details->contact_type) && $client_details->contact_type == 4) {
                                                                   echo 'Ex-Client';
                                                               }
                                                               ?>
                                            <?php
                                                               if (!empty($client_details->contact_type) && $client_details->contact_type == 5) {
                                                                   echo 'Reffral Partner';
                                                               }
                                                               ?>
                                            <?php
                                                               if (!empty($client_details->contact_type) && $client_details->contact_type == 6) {
                                                                   echo 'Scope';
                                                               }
                                                               ?>
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
                                                    <?php echo btn_edit('admin/leads/manage_lead/' . $client_details->id) ?>
                                                <?php }
                                                if (!empty($deleted)) {
                                                    ?>
                                                    <?php echo btn_delete('admin/leads/delete_leads/' . $client_details->id) ?>
                                                <?php } ?>
                                                <?php echo btn_view('admin/leads/lead_details/' . $client_details->id) ?>
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
                              action="<?php echo base_url(); ?>admin/leads/save_client/<?php
                              if (!empty($client_info)) {
                                  echo $client_info->id;
                              }
                              ?>" method="post" class="form-horizontal contacts-form" >
                            <div class="panel-body">
                                <label class="control-label col-sm-3"></label>
                                <div class="col-sm-12">
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
                                                
                                                <input type="hidden" class="form-control" name="contact_type" value="1">
                                                    
                                                        

                                                <div class="form-group">
                                                    <label class="col-lg-4 control-label">First Name
                                                        <span
                                                            class="text-danger"> *</span></label>
                                                    <div class="col-lg-5">
                                                        <input type="text" class="form-control" required
                                                               value="<?php
                                                               if (!empty($client_info->first_name)) {
                                                                   echo $client_info->first_name;
                                                               }
                                                               ?>" name="first_name" >
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="col-lg-4 control-label">Last Name
                                                        <span
                                                            class="text-danger"> *</span></label>
                                                    <div class="col-lg-5">
                                                        <input type="text" class="form-control" required
                                                               value="<?php
                                                               if (!empty($client_info->last_name)) {
                                                                   echo $client_info->last_name;
                                                               }
                                                               ?>" name="last_name">
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="col-lg-4 control-label">Alias/Nickname
                                                        <span
                                                            class="text-danger"> *</span></label>
                                                    <div class="col-lg-5">
                                                        <input type="text" class="form-control" required
                                                               value="<?php
                                                               if (!empty($client_info->nick_name)) {
                                                                   echo $client_info->nick_name;
                                                               }
                                                               ?>" name="nick_name">
                                                    </div>
                                                </div>
                                                <?php
                                                               if (!empty($client_info->password)) {?>
                                                <div class="form-group">
                                                    <label class="col-sm-4 control-label"><?= lang('password') ?><span
                                                            class="text-danger">*</span></label>
                                                    <div class="col-sm-5">
                                                        <input type="text" id="new_password" placeholder="<?= lang('password') ?>"
                                                            name="password" class="input-sm form-control" value="<?php
                                                               if (!empty($client_info->password)) {
                                                                   echo $client_info->password;
                                                               }
                                                               ?>">
                                                    </div>
                                                </div>
                                                <?php }?>
                                                <?php
                                                    if (!empty($client_info->password)) {
                                                ?>
                                                    <div class="form-group">
                                                        <label
                                                            class="col-sm-4 control-label"><?= lang('confirm_password') ?><span
                                                                class="text-danger">*</span></label>
                                                        <div class="col-sm-5">
                                                            <input type="password" data-parsley-equalto="#new_password"
                                                                placeholder="<?= lang('confirm_password') ?>"
                                                                name="confirm_password" class="input-sm form-control"  value="<?php
                                                                if (!empty($client_info->password)) {
                                                                    echo $client_info->password;
                                                                }
                                                                ?>">
                                                        </div>
                                                    </div>
                                                <?php }?>
                                                <div class="form-group">
                                                    <label class="col-lg-4 control-label">Email
                                                        <span
                                                            class="text-danger"> *</span></label>
                                                    <div class="col-lg-5">
                                                        <input type="email" class="form-control" required=""
                                                               value="<?php
                                                               if (!empty($client_info->email)) {
                                                                   echo $client_info->email;
                                                               }
                                                               ?>" name="email">
                                                    </div>
                                                </div>
                                                <?php $label_info = $this->db->get('tbl_additional_label')->result();?>
                                                <div class="form-group additional-email additional_icon additional_label_group">
                                                    <label class="col-lg-4 control-label">Additional Email</label>
                                                    
                                                    <?php if(!empty($additional_emails)){
                                                        $j= 0 ;
                                                        foreach ($additional_emails as $item)
                                                        {
                                                    ?>
                                                    <div class="col-lg-5 d-flex">
                                                        <input type="email" class="form-control"
                                                               value="<?php
                                                               if (!empty($item->additional_email)) {
                                                                   echo $item->additional_email;
                                                               }
                                                               ?>" name="additional_email[]" />
                                                         <select class="form-control margin-l-5" name="additional_email_label[]">
                                                            <option>--Label--</option>
                                                            <?php foreach ($label_info as $label_item){?>
                                                                <option value="<?php echo $label_item->id;?>" <?php if($item->type == $label_item->id){echo 'selected';} ?>><?php echo $label_item->label;?></option>
                                                            <?php }?>
                                                           

                                                        </select>
                                                            <?php if($j == 0){?>
                                                                <div class="input-group-addon">
                                                                    <a data-toggle="modal" data-target="#myModal" href="<?= base_url() ?>admin/leads/add_additional_label_modal" class="add-additional-label-modal"><i class="fa fa-pencil"></i></a>
                                                                </div>
                                                                <div class="input-group-addon">
                                                                    <a class="add email-add-btn"><i class="fa fa-plus"></i></a>
                                                                </div>
                                                            <?php $j++;}else{?>
                                                                <div class="input-group-addon">
                                                                    <a class="remove"><i class="fas fa-times"></i></a>
                                                                </div>
                                                            <?php }?>
                                                    </div>
                                                    <?php 
                                                        }
                                                        }else{
                                                    ?>
                                                    <div class="col-lg-5 d-flex">
                                                        <input type="email" class="form-control"
                                                               value="" name="additional_email[]" />
                                                         <select class="form-control margin-l-5" name="additional_email_label[]">
                                                         <option>--Label--</option>
                                                         <?php foreach ($label_info as $label_item){?>
                                                                <option value="<?php echo $label_item->id;?>"><?php echo $label_item->label;?></option>
                                                            <?php }?>
                                                        </select>
                                                        <div class="input-group-addon">
                                                            <a data-toggle="modal" data-target="#myModal" href="<?= base_url() ?>admin/leads/add_additional_label_modal" class="add-additional-label-modal"><i class="fa fa-pencil"></i></a>
                                                        </div>
                                                        <div class="input-group-addon">
                                                            <a class="add email-add-btn"><i class="fa fa-plus"></i></a>
                                                        </div>
                                                    </div>
                                                    <?php }?>
                                                </div>
                                                
                                                <script type="text/javascript">
                                                    $(document).ready(function() {
                                                       
                                                    //here first get the contents of the div with name class copy-fields and add it to after "after-add-more" div class.
                                                    $(".email-add-btn").click(function(){ 
                                                        var options = $(this).parents('.additional_icon').find('select').html();
                                                        console.log(options);
                                                        var append_html = `<div class="form-group additional_icon additional_label_group">
                                                        <label class="col-lg-4 control-label"></label>
                                                        <div class="col-lg-5 d-flex">
                                                            <input type="email" class="form-control" value="" name="additional_email[]"><select class="form-control margin-l-5" name="additional_email_label[]">` + options+`</select><div class="input-group-addon">
                                                        </div><div class="input-group-addon">
                                                                <a class="remove"><i class="fas fa-times"></i></a>
                                                            </div>
                                                        </div>
                                                    </div>`;
                                                        $(".additional-email").after(append_html);
                                                    });
                                                //here it will remove the current value of the remove button which has been pressed
                                                    $("body").on("click",".remove",function(){ 
                                                        $(this).parents(".form-group").remove();
                                                    });
                                                
                                                    });
                                                </script>
                                                <div class="form-group">
                                                    <label
                                                        class="col-lg-4 control-label">Mobile</label>
                                                    <div class="col-lg-5">
                                                        <input type="text" class="form-control" id="mobile_phone"
                                                               value="<?php
                                                               if (!empty($client_info->phone)) {
                                                                   echo $client_info->phone;
                                                               }
                                                               ?>" name="phone" data-parsley-validate="number" required>
                                                    </div>
                                                </div>
                                                <?php if(!empty($additional_phones)){
                                                        $i = 0;
                                                        foreach ($additional_phones as $item)
                                                        {
                                                    ?>
                                                <div class="form-group additional-phone additional_icon additional_label_group">
                                                    <label class="col-lg-4 control-label"><?php if($i == 0){echo 'Additional Phone';}?></label>

                                                    <div class="col-lg-5 d-flex">
                                                        <input type="text" class="form-control" value="<?php
                                                        if (!empty($item->additional_phone)) {
                                                            echo $item->additional_phone;
                                                        }
                                                        ?>" name="additional_phone[]"  data-parsley-validate="number">
                                                        <select class="form-control margin-l-5" name="additional_phone_label[]">
                                                            <option>--Label--</option>
                                                            <?php foreach ($label_info as $label_item){?>
                                                                <option value="<?php echo $label_item->id;?>" <?php if($item->type == $label_item->id){echo 'selected';} ?>><?php echo $label_item->label;?></option>
                                                            <?php }?>
                                                        </select>
                                                            <?php if($i == 0){?>
                                                                <div class="input-group-addon">
                                                                    <a data-toggle="modal" data-target="#myModal" href="<?= base_url() ?>admin/leads/add_additional_label_modal" class="add-additional-label-modal"><i class="fa fa-pencil"></i></a>
                                                                </div>
                                                                <div class="input-group-addon">
                                                                    <a class="add phone-add-more"><i class="fa fa-plus"></i></a>
                                                                </div>
                                                            <?php $i++;}else{?>
                                                                <div class="input-group-addon">
                                                                    <a class="remove"><i class="fas fa-times"></i></a>
                                                                </div>
                                                            <?php }?>
                                                        
                                                        </div>
                                                    </div>

                                                    <?php }
                                                        }else{
                                                    ?>
                                                     <div class="form-group additional-phone additional_icon additional_label_group">
                                                    <label class="col-lg-4 control-label">Additional Phone</label>
                                                    <div class="col-lg-5 d-flex">
                                                        <input type="text" class="form-control" value="" name="additional_phone[]" data-parsley-validate="number">
                                                        <select class="form-control margin-l-5" name="additional_phone_label[]">
                                                            <option>--Label--</option>
                                                         <?php foreach ($label_info as $label_item){?>
                                                                
                                                                <option value="<?php echo $label_item->id;?>"><?php echo $label_item->label;?></option>
                                                            <?php }?>
                                                        </select>
                                                        <div class="input-group-addon">
                                                            <a data-toggle="modal" data-target="#myModal" href="<?= base_url() ?>admin/leads/add_additional_label_modal" class="add-additional-label-modal"><i class="fa fa-pencil"></i></a>
                                                        </div>
                                                        <div class="input-group-addon">
                                                            <a class="add phone-add-more"><i class="fa fa-plus"></i></a>
                                                        </div>
                                                        </div>
                                                    </div>
                                                    <?php }?>
                                                <div class="additional-phone-copy hide">
                                                    
                                                </div>
                                                <script type="text/javascript">
                                                    $(document).ready(function() {
                                                    //here first get the contents of the div with name class copy-fields and add it to after "after-add-more" div class.
                                                    $(".phone-add-more").click(function(){ 
                                                        var options = $(this).parents('.additional_icon').find('select').html();
                                                        var append_html = `<div class="form-group additional_icon additional_label_group">
                                                        <label
                                                                class="col-lg-4 control-label"></label>
                                                        <div class="col-lg-5 d-flex">
                                                            <input type="text" class="form-control" name="additional_phone[]" data-parsley-validate="number">
                                                            <select class="form-control margin-l-5" name="additional_phone_label[]">` + options+`</select><div class="input-group-addon">
                                                        </div><div class="input-group-addon">
                                                                <a class="remove"><i class="fas fa-times"></i></a>
                                                            </div>
                                                        </div>
                                                    </div>`;
                                                        $(".additional-phone").after(append_html);
                                                    });
                                                //here it will remove the current value of the remove button which has been pressed
                                                    $("body").on("click",".remove",function(){ 
                                                        $(this).parents(".form-group").remove();
                                                    });
                                                
                                                    });
                                                
                                                </script>


                                                <?php if(!empty($website_info)){
                                                        $k = 0;
                                                        foreach ($website_info as $item)
                                                        {
                                                ?>
                                                <div class="form-group additional_icon  <?php if($k == 0){echo 'website-url';}?>">
                                                    <label class="col-lg-4 control-label"><?php if($k == 0){echo 'Website URL';}?></label>
                                                    <div class="col-lg-5 d-flex">
                                                        <input type="url" class="form-control" value="<?php
                                                        if (!empty($item->website_url)) {
                                                            echo $item->website_url;
                                                        }
                                                        ?>" name="website_url[]">
                                                        <input type="text" name="website_url_label[]" class="form-control margin-l-5" value="<?php
                                                        if (!empty($item->label)) {
                                                            echo $item->label;
                                                        }
                                                        ?>">
                                                        <div class="input-group-addon"> 
                                                        <?php if($k == 0){?>
                                                            <a class="website-add-more"><i class="fa fa-plus"></i></a>
                                                            <?php $k++;}else{?>
                                                                <a class="remove"><i class="fas fa-times"></i></a>
                                                            <?php }?>
                                                        </div>
                                                    </div>
                                                    
                                                </div>
                                                <?php }
                                                    }else{
                                                    ?>
                                                    <div class="form-group website-url additional_icon">
                                                        <label class="col-lg-4 control-label">Website URL</label>
                                                        <div class="col-lg-5 d-flex">
                                                            <input type="url" class="form-control" value="" name="website_url[]" >
                                                            <input type="text" name="website_url_label[]" class="form-control margin-l-5" value="" >
                                                            <div class="input-group-addon"> 
                                                                    <a class="website-add-more"><i class="fa fa-plus"></i></a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php }?>
                                                <script type="text/javascript">
                                                    $(document).ready(function() {
                                                        var html = `<div class="form-group additional_icon">
                                                        <label class="col-lg-4 control-label"></label>
                                                        <div class="col-lg-5 d-flex">
                                                            <input type="text" class="form-control" value="" name="website_url[]" required>
                                                            <input type="text" name="website_url_label[]" class="form-control margin-l-5" required>
                                                            <div class="input-group-addon"> 
                                                                <a class="remove"><i class="fas fa-times"></i></a>
                                                            </div>
                                                        </div>
                                                    </div>`;
                                                
                                                    //here first get the contents of the div with name class copy-fields and add it to after "after-add-more" div class.
                                                    $(".website-add-more").click(function(){ 
                                                        $(".website-url").after(html);
                                                    });
                                                //here it will remove the current value of the remove button which has been pressed
                                                    $("body").on("click",".remove",function(){ 
                                                        $(this).parents(".form-group").remove();
                                                    });
                                                
                                                    });
                                                </script>
                                                <?php if(!empty($client_info))
                                                    {
                                                        $industry_array = explode(',',$client_info->industries);
                                                    }
                                                ?>

                                                <div class="form-group">
                                                    <label
                                                        class="col-lg-4 control-label">Industries</label>
                                                    <div class="col-lg-5">
                                                    <select name="industries[]" id="optional" multiple="multiple" data-placeholder="Select Industry..." class="form-control" required="">
                                                        <?php if(!empty($industries)){
                                                            foreach ($industries as $industry)
                                                            {
                                                        ?>    
                                                            <option value="<?php echo $industry->id; ?>" <?php if(!empty($client_info) && in_array($industry->id,$industry_array)){echo 'selected';}?>><?php echo $industry->industry ?></option>
                                                        <?php 
                                                            }
                                                            }
                                                            else{
                                                        ?>
                                                            <option>No industry</option>
                                                        <?php }?>
                                                    </select>
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
                                                        Geographical Area Served
                                                    </label>
                                                    <div class="col-lg-5">
                                                        <input type="text" class="form-control" value="<?php
                                                        if (!empty($client_info->latitude)) {
                                                            echo $client_info->latitude;
                                                        }
                                                        ?>" name="g_areas_served">
                                                    </div>
                                                </div>
                                                
                                                <input type="hidden" class="add-company-flag" name="add-company-flag" value="1">
                                                <div class="form-group additional_icon">
                                                    <label
                                                        class="col-lg-4 control-label">Company Name</label>
                                                    <div class="col-lg-5 d-flex">
                                                        <select class="form-control select_box" name="company_id">
                                                            <?php foreach ($companies as $company){?>
                                                                <option value="<?php echo $company->id;?>" <?php if(!empty($client_info->company_id) && $client_info->company_id == $company->id){echo 'selected';}?> ><?php echo $company->company_name;?></option>
                                                            <?php }?>
                                                        </select>
                                                        <div class="input-group-addon">
                                                            <a data-toggle="modal" data-target="#myModal" href="<?= base_url() ?>admin/leads/add_company_modal" class="add-additional-label-modal"><i class="fa fa-plus"></i></a>
                                                            <!-- <a class="new-company-btn"><i class="fa fa-plus"></i></a> -->
                                                            <!-- <a class="new-company-btn display-none"><i class="fas fa-times"></i></a> -->
                                                        </div>
                                                    </div>
                                                
                                                
                                                   
                                                <script>
                                                    $(document).ready(function(){
                                                        $before_construction = '';
                                                        $('#social-media-select').change(function(){
                                                            // ajax
                                                            var data ={};
                                                            data.social = $(this).val();
                                                            $.post('<?= base_url()?>admin/leads/get_company_social_links',data).done(function(data){
                                                                console.log(data);
                                                                console.log('44444444');
                                                            });
                                                        
                                                            before_construction = $(this).val();
                                                        });
                                                    });
                                                
                                                </script>
                                                </div>
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
                                            <select name="white_brand" class="form-control select_box"
                                                    style="width: 100%" required>
                                                    <?php if (!empty($whitebrand)): foreach ($whitebrand as $item): ?>
                                                        <option
                                                            value="<?= $item->id ?>" <?= (!empty($client_info->white_brand) && $client_info->white_brand == $item->white_brand ? 'selected' : NULL) ?>><?= $item->white_brand ?>
                                                        </option>
                                                    <?php 
                                                        endforeach;
                                                    endif;
                                                    ?>
                                                        
                                            </select>
                                        </div>
                                    </div>
                                                   
                                                    <!-- intereste Level -->
                                    <div class="form-group">
                                        <label
                                            class="col-lg-4 control-label">Interest Level</label>
                                        <div class="col-lg-5">
                                            <select name="interest_level" class="form-control select_box" style="width: 100%" required>
                                                <option value="1" <?= (!empty($client_info->interest_level) && $client_info->interest_level == '1' ? 'selected' : NULL) ?>>Not Applicable</option>
                                                <option value="2" <?= (!empty($client_info->interest_level) && $client_info->interest_level == '2' || empty($client_info->interest_level)? 'selected' : NULL) ?>>To Be Determined</option>
                                                <option value="3" <?= (!empty($client_info->interest_level) && $client_info->interest_level == '3' ? 'selected' : NULL) ?>>No Interest</option>
                                                <option value="4" <?= (!empty($client_info->interest_level) && $client_info->interest_level == '4' ? 'selected' : NULL) ?>>Low Interest</option>
                                                <option value="5" <?= (!empty($client_info->interest_level) && $client_info->interest_level == '5' ? 'selected' : NULL) ?>>Medium Interest</option>
                                                <option value="6" <?= (!empty($client_info->interest_level) && $client_info->interest_level == '6' ? 'selected' : NULL) ?>>high Interest</option>
                                            </select>
                                        </div>
                                    </div>
                                    <!-- sales rep -->
                                    <div class="form-group">
                                        <label
                                            class="col-lg-4 control-label">Sales Rep</label>
                                        <div class="col-lg-5">
                                        <select name="sales_rep" class="form-control select_box" style="width: 100%">
                                                    
                                                    <?php if (!empty($all_staffes)): foreach ($all_staffes as $staff): ?>
                                                        <option value="<?= $staff->user_id ?>" <?= (!empty($client_info->sales_rep) && $client_info->sales_rep == $staff->username ? 'selected' : NULL) ?>><?= $staff->username ?>
                                                        </option>
                                                        <?php
                                                    endforeach;
                                                    endif;
                                                    ?>
                                            </select>
                                        </div>
                                    </div>


                                    <!-- Referral Type and Source -->
                                    <div class="form-group">
                                        <label class="col-lg-4 control-label">Referral Type & Source</label>
                                        <div class="col-lg-5">
                                            <div class="form-group referral-type-source">
                                                <div class="checkbox c-checkbox">
                                                    <div class="col-lg-12">
                                                    <label class="needsclick">
                                                        <input type="checkbox" class="check_web" name="web" <?php 
                                                        if(!empty($client_info->web)){echo 'checked';} ?>>
                                                        <span class="fa fa-check"></span>
                                                        <large>Web</large>
                                                    </label>
                                                    </div>
                                                </div>
                                                <div class="form-group child-settings <?php 
                                                        if(empty($client_info->web)){echo 'display-none';}?>">
                                                    <div class="col-lg-12 margin-t-5">
                                                        <label class="col-lg-4 control-label">Website</label>
                                                        <div class="col-lg-8">
                                                        
                                                            <input type="url" class="form-control" value="<?php if(!empty($contact_web)){echo $contact_web[0]->w_wbsite;} ?>" name="w_wbsite">
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-12 margin-t-5">
                                                        <label class="col-lg-4 control-label">Google Organic</label>
                                                        <div class="col-lg-8">
                                                            <input type="url" class="form-control" value="<?php if(!empty($contact_web)){echo $contact_web[0]->w_google_organic;} ?>" name="w_google_organic">
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-12 margin-t-5">
                                                        <label
                                                                class="col-lg-4 control-label">Bing Organic</label>
                                                        <div class="col-lg-8">
                                                                <input type="url" class="form-control" value="<?php if(!empty($contact_web)){echo $contact_web[0]->w_bing_organic;} ?>" name="w_bing_organic">
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-12 margin-t-5">
                                                        <label
                                                                class="col-lg-4 control-label">Google Ads</label>
                                                        <div class="col-lg-8">
                                                                <input type="url" class="form-control" value="<?php if(!empty($contact_web)){echo $contact_web[0]->w_google_ads;} ?>" name="w_google_ads">
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-12 margin-t-5">
                                                        <label
                                                                class="col-lg-4 control-label">Bing Ads</label>
                                                        <div class="col-lg-8">
                                                                <input type="url" class="form-control" value="<?php if(!empty($contact_web)){echo $contact_web[0]->w_bing_ads;} ?>" name="w_bing_ads">
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-12 margin-t-5">
                                                        <label class="col-lg-4 control-label">Other</label>
                                                        <div class="col-lg-8">
                                                                <input type="url" class="form-control" value="<?php if(!empty($contact_web)){echo $contact_web[0]->w_other;} ?>" name="w_other">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group referral-type-source">
                                                    <div class="col-lg-12">
                                                        <div class="checkbox c-checkbox">
                                                            <label class="needsclick">
                                                                <input type="checkbox" class="check_social_media" name="social_media" <?php 
                                                            if(!empty($client_info->social_media)){echo 'checked';} ?>>
                                                                <span class="fa fa-check"></span>
                                                                <large>Social Media</large>
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div class="form-group child-settings <?php 
                                            if(empty($client_info->social_media)){echo 'display-none';}?>">
                                                    <div class="col-lg-12 margin-t-5">
                                                        <label
                                                                class="col-lg-4 control-label">Facebook Organic</label>
                                                        <div class="col-lg-8">
                                                                <input type="text" class="form-control" value="<?php if(!empty($contact_social_media)){echo $contact_social_media[0]->s_facebook_or;} ?>" name="social_facebook_or">
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-12 margin-t-5">
                                                        <label
                                                                class="col-lg-4 control-label">Facebook Ad</label>
                                                        <div class="col-lg-8">
                                                                <input type="text" class="form-control" value="<?php if(!empty($contact_social_media)){echo $contact_social_media[0]->s_facebook_ad;} ?>" name="social_facebook_ad">
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-12 margin-t-5">
                                                        <label
                                                                class="col-lg-4 control-label">Instagram Oragnic</label>
                                                        <div class="col-lg-8">
                                                                <input type="text" class="form-control" value="<?php if(!empty($contact_social_media)){echo $contact_social_media[0]->social_instagram_or;} ?>" name="social_instagram_or">
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-12 margin-t-5">
                                                        <label
                                                                class="col-lg-4 control-label">Instagram Ad</label>
                                                        <div class="col-lg-8">
                                                                <input type="text" class="form-control" value="<?php if(!empty($contact_social_media)){echo $contact_social_media[0]->social_instagram_ad;} ?>" name="social_instagram_ad">
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-12 margin-t-5">
                                                        <label
                                                                class="col-lg-4 control-label">Pinterest Organic</label>
                                                        <div class="col-lg-8">
                                                                <input type="text" class="form-control" value="<?php if(!empty($contact_social_media)){echo $contact_social_media[0]->social_pinterest_or;} ?>" name="social_pinterest_or">
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-12 margin-t-5">
                                                        <label
                                                                class="col-lg-4 control-label">Pinterest Ad</label>
                                                        <div class="col-lg-8">
                                                                <input type="text" class="form-control" value="<?php if(!empty($contact_social_media)){echo $contact_social_media[0]->social_pinterest_ad;} ?>" name="social_pinterest_ad">
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-12 margin-t-5">
                                                        <label
                                                                class="col-lg-4 control-label">YouTube Organic</label>
                                                        <div class="col-lg-8">
                                                                <input type="text" class="form-control" value="<?php if(!empty($contact_social_media)){echo $contact_social_media[0]->social_youtube_or;} ?>" name="social_youtube_or">
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-12 margin-t-5">
                                                        <label
                                                                class="col-lg-4 control-label">YouTube Ad</label>
                                                        <div class="col-lg-8">
                                                                <input type="text" class="form-control" value="<?php if(!empty($contact_social_media)){echo $contact_social_media[0]->social_youtube_ad;} ?>" name="social_youtube_ad">
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-12 margin-t-5">
                                                        <label
                                                                class="col-lg-4 control-label">LinkedIn Oranic</label>
                                                        <div class="col-lg-8">
                                                                <input type="text" class="form-control" value="<?php if(!empty($contact_social_media)){echo $contact_social_media[0]->social_linkedin_or;} ?>" name="social_linkedin_or">
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-12 margin-t-5">
                                                        <label
                                                                class="col-lg-4 control-label">LinkedIn Ad</label>
                                                        <div class="col-lg-8">
                                                                <input type="text" class="form-control" value="<?php if(!empty($contact_social_media)){echo $contact_social_media[0]->social_linkedin_ad;} ?>" name="social_linkedin_ad">
                                                        </div>
                                                    </div>
                                                </div>
                                                                                                        
                                                </div>
                                                
 

                                                <div class="form-group referral-type-source">
                                                    <div class="col-lg-12">
                                                        <div class="checkbox c-checkbox">
                                                            <label class="needsclick">
                                                                <input type="checkbox" class="check_network" id="web" name="networking" <?php 
                                                            if(!empty($client_info->networking)){echo 'checked';} ?>>
                                                                <span class="fa fa-check"></span>
                                                                <large>Networking</large>
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div class="form-group child-settings <?php 
                                            if(empty($client_info->networking)){echo 'display-none';}?>">
                                                    <div class="col-lg-12 margin-t-5">
                                                        <label
                                                                class="col-lg-4 control-label">Event or Group</label>
                                                        <div class="col-lg-8">
                                                                <input type="text" class="form-control" value="<?php if(!empty($contact_networking)){echo $contact_networking[0]->n_event_group;} ?>" name="n_event_group">
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-12 margin-t-5">
                                                        <label class="col-lg-4 control-label">Referred By</label>
                                                        <div class="col-lg-8">

                                                            <div class="input-group">
                                                                <select name="n_referred_by" class="form-control select_box" style="width: 100%">
                                                                    <?php if (!empty($contact_info)): foreach ($contact_info as $contact): ?>
                                                                        <option value="<?= $contact->id ?>" <?= (!empty($contact_networking) && $contact_networking[0]->n_referred_by == $contact->id ? 'selected' : NULL) ?>><?php echo  $contact->nick_name;?>
                                                                        </option>
                                                                        <?php
                                                                    endforeach;
                                                                    endif;
                                                                    ?>
                                                                </select>
                                                                <div class="input-group-addon">
                                                                        <a href="<?= base_url() ?>admin/leads/manage_client/new" class="add-additional-label-modal"><i class="fa fa-plus"></i></a>
                                                                </div>
                                                            </div>
                                                            
                                                            
                                                        </div>
                                                    </div>
                                                </div>                                              
                                                </div>


                                                <?php $cold_type = $this->db->get('tbl_contact_cold_call_type_label')->result();?>
                                                <div class="form-group referral-type-source">
                                                    <div class="col-lg-12">
                                                        <div class="checkbox c-checkbox">
                                                            <label class="needsclick">
                                                                <input type="checkbox" class="check_cold_call" name="cold_call" <?php 
                                                            if(!empty($client_info->cold_call)){echo 'checked';} ?>>
                                                                <span class="fa fa-check"></span>
                                                                <large>Cold Call</large>
                                                            </label>
                                                        </div>
                                                    </div>  
                                                    <div class="form-group child-settings <?php 
                                            if(empty($client_info->cold_call)){echo 'display-none';}?>">
                                                    <div class="col-lg-12 margin-t-5">
                                                        <label class="col-lg-4 control-label">Cold Call Type</label>
                                                        <div class="col-lg-8">
                                                            <div class="input-group">
                                                                <select class="form-control select_box" name="c_call_type" style="width:100%">
                                                                    <?php foreach ($cold_type as $type_item){?>
                                                                        <option value="<?php echo $type_item->id;?>" <?php if(!empty($contact_cold_call) && $contact_cold_call[0]->c_call_type == $type_item->id){echo 'selected';} ?>><?php echo $type_item->type;?></option>
                                                                    <?php }?>
                                                                </select>
                                                                <div class="input-group-addon">
                                                                        <a data-toggle="modal" data-target="#myModal" href="<?= base_url() ?>admin/leads/add_cold_call_type_modal" class="add-additional-label-modal"><i class="fa fa-plus"></i></a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-12 margin-t-5">
                                                        <label class="col-lg-4 control-label">Cold Caller</label>
                                                        <div class="col-lg-8">
                                                            <div class="input-group">
                                                                <select name="c_caller" class="form-control select_box" style="width: 100%">
                                                                    <?php if (!empty($all_staffes)): foreach ($all_staffes as $staff): ?>
                                                                        <option value="<?= $staff->user_id ?>" <?= (!empty($contact_cold_call) && $contact_cold_call[0]->c_caller == $staff->user_id ? 'selected' : NULL) ?>><?= $staff->username ?>
                                                                        </option>
                                                                        <?php
                                                                    endforeach;
                                                                    endif;
                                                                    ?>
                                                                </select>
                                                                <div class="input-group-addon">
                                                                        <a href="<?= base_url() ?>admin/user/user_list" class="add-additional-label-modal"><i class="fa fa-plus"></i></a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                   
                                                </div>                                                  
                                                </div>
                                                
                                                
                                                <div class="form-group referral-type-source">
                                                    <div class="col-lg-12">
                                                        <div class="checkbox c-checkbox">
                                                            <label class="needsclick col-lg-4">
                                                                <input type="checkbox" class="check_web" name="sales_rep_check" <?php 
                                                            if(!empty($client_info->sales_rep_check)){echo 'checked';} ?>>
                                                                <span class="fa fa-check"></span>
                                                                <large>Sales Rep</large>
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div class="form-group child-settings <?php 
                                            if(empty($client_info->sales_rep_check)){echo 'display-none';}?>">
                                                        <div class="col-lg-12">    
                                                            <label class="col-lg-4 control-label">Sales Rep Name</label>
                                                            <div class="col-lg-8">
                                                                    <div class="checkbox c-checkbox">
                                                                        <label class="col-lg-12">
                                                                        <input type="checkbox" name="sales_rep_name" <?php if(!empty($contact_sales_rep)){if(!empty($contact_sales_rep[0]->sales_rep_name)){echo 'checked';}} ?>>
                                                                        <span class="fa fa-check"></span>
                                                                        <small>Networking</small>
                                                                        </label>
                                                                    </div>
                                                            </div>
                                                        </div>
                                                        </div>
                                                </div>


                                        </div>
                                    </div>
                                   
                                
                                                

                                                <div class="form-group">
                                                    <label
                                                        class="col-lg-4 control-label">Referral Notes</label>
                                                    <div class="col-lg-5">
                                                        <textarea name="referral_note" class="form-control textarea"><?php 
                                                            if(!empty($client_info->referral_note)){echo $client_info->referral_note;} ?></textarea>
                                                    </div>
                                                </div>


                                                <div class="form-group">
                                                    <label
                                                        class="col-lg-4 control-label">Date</label>
                                                    <div class="col-lg-5">
                                                        <div class="input-group">
                                                            <input type="text" name="contact_date"
                                                                class="form-control datepicker"
                                                                value="<?php
                                                                if (!empty($client_info->contact_date)) {
                                                                    echo $client_info->contact_date;
                                                                } else {
                                                                    echo date('Y-m-d');
                                                                }
                                                                ?>"
                                                                data-date-format="<?= config_item('date_picker_format'); ?>">
                                                            <div class="input-group-addon">
                                                                <a href="#"><i class="fa fa-calendar"></i></a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>


                                                <div class="form-group referral-type-source">
                                                    <div class="col-lg-9 pull-right">
                                                        <div class="checkbox c-checkbox">
                                                            <label class="needsclick col-lg-4">
                                                                <input type="checkbox" class="select_one" name="request_proposal" <?php 
                                                            if(!empty($client_info->request_proposal)){echo 'checked';} ?>>
                                                                <span class="fa fa-check"></span>
                                                                <large>Request Proposal</large>
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div class="form-group child-settings <?php 
                                                if(empty($client_info->request_proposal)){echo 'display-none';}?>">
                                                    <div class="col-lg-12 margin-t-5">
                                                        <label
                                                            class="col-lg-4 control-label">Select Proposal Sections</label>
                                                        <div class="col-lg-7">
                                                            <div class="col-lg-12">
                                                                <div class="checkbox c-checkbox">
                                                                    <label class="col-lg-5">
                                                                    <input type="checkbox" name="branding" <?php if(!empty($contact_request_proposal)){if(!empty($contact_request_proposal[0]->branding)){echo 'checked';}}?>>
                                                                    <span class="fa fa-check"></span>
                                                                    <small>Branding</small>
                                                                    </label>
                                                                </div>
                                                            </div>

                                                            <div class="col-lg-12">
                                                                
                                                                <div class="checkbox c-checkbox">
                                                                    <label class="col-lg-5">
                                                                    <input type="checkbox" name="website_analysis" <?php if(!empty($contact_request_proposal)){if(!empty($contact_request_proposal[0]->website_analysis)){echo 'checked';}}?>>
                                                                    <span class="fa fa-check"></span>
                                                                    <small>Website Analysis</small>
                                                                    </label>
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-12">
                                                              
                                                                <div class="checkbox c-checkbox">
                                                                    <label class="col-lg-5">
                                                                    <input type="checkbox" name="website_proposal" <?php if(!empty($contact_request_proposal)){if(!empty($contact_request_proposal[0]->website_proposal)){echo 'checked';}}?>>
                                                                    <span class="fa fa-check"></span>
                                                                    <small>Website proposal</small>
                                                                    </label>
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-12">
                                                                
                                                                <div class="checkbox c-checkbox">
                                                                    <label class="col-lg-5">
                                                                    <input type="checkbox" name="seo" <?php if(!empty($contact_request_proposal)){if(!empty($contact_request_proposal[0]->seo)){echo 'checked';}}?>>
                                                                    <span class="fa fa-check"></span>
                                                                    <small>Search Engine Optimization</small>
                                                                    </label>
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-12">
                                                               
                                                                <div class="checkbox c-checkbox">
                                                                    <label class="col-lg-5">
                                                                    <input type="checkbox" name="sea" <?php if(!empty($contact_request_proposal)){if(!empty($contact_request_proposal[0]->sea)){echo 'checked';}}?>>
                                                                    <span class="fa fa-check"></span>
                                                                    <small>Search Engine Advertising</small>
                                                                    </label>
                                                                </div>
                                                            </div>

                                                            <div class="col-lg-12">
                                                                <div class="checkbox c-checkbox">
                                                                    <label class="col-lg-5">
                                                                    <input type="checkbox" name="smm" <?php if(!empty($contact_request_proposal)){if(!empty($contact_request_proposal[0]->smm)){echo 'checked';}}?>>
                                                                    <span class="fa fa-check"></span>
                                                                    <small>Social Media Management</small>
                                                                    </label>
                                                                </div>
                                                            </div>

                                                            <div class="col-lg-12">
                                                                <div class="checkbox c-checkbox">
                                                                    <label class="col-lg-5">
                                                                    <input type="checkbox" name="sma" <?php if(!empty($contact_request_proposal)){if(!empty($contact_request_proposal[0]->sma)){echo 'checked';}}?>>
                                                                    <span class="fa fa-check"></span>
                                                                    <small>Social Media Advertising</small>
                                                                    </label>
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-12">
                                                                <div class="checkbox c-checkbox">
                                                                    <label class="col-lg-5">
                                                                    <input type="checkbox" name="content_marketing" <?php if(!empty($contact_request_proposal)){if(!empty($contact_request_proposal[0]->content_marketing)){echo 'checked';}}?>>
                                                                    <span class="fa fa-check"></span>
                                                                    <small>Content Marketing</small>
                                                                    </label>
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-12">
                                                                <div class="checkbox c-checkbox">
                                                                    <label class="col-lg-5">
                                                                    <input type="checkbox" name="marketing_analysis" <?php if(!empty($contact_request_proposal)){if(!empty($contact_request_proposal[0]->marketing_analysis)){echo 'checked';}}?>>
                                                                    <span class="fa fa-check"></span>
                                                                    <small>Marketing Analysis</small>
                                                                    </label>
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-12">
                                                                <div class="checkbox c-checkbox">
                                                                    <label class="col-lg-5">
                                                                    <input type="checkbox" name="recommendations" <?php if(!empty($contact_request_proposal)){if(!empty($contact_request_proposal[0]->recommendations)){echo 'checked';}}?>>
                                                                    <span class="fa fa-check"></span>
                                                                    <small>Recommendations</small>
                                                                    </label>
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-12">
                                                                <div class="checkbox c-checkbox">
                                                                    <label class="col-lg-5">
                                                                    <input type="checkbox" name="why_us_page" <?php if(!empty($contact_request_proposal)){if(!empty($contact_request_proposal[0]->why_us_page)){echo 'checked';}}?>>
                                                                    <span class="fa fa-check"></span>
                                                                    <small>Why Us Page</small>
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-12 margin-t-5">
                                                        <label
                                                            class="col-lg-4 control-label">Price Category</label>
                                                        <div class="col-lg-7">
                                                            <div class="input-group">
                                                                <select name="price_category" class="form-control select_box" style="width: 100%">
                                                                            <?php if (!empty($prices)): foreach ($prices as $price): ?>
                                                                                <option
                                                                                    value="<?= $price->id ?>" <?= (!empty($contact_request_proposal) && !empty($contact_request_proposal[0]->price_category) && $contact_request_proposal[0]->price_category == $price->price ? 'selected' : NULL) ?>><?= $price->price ?>
                                                                                </option>
                                                                                <?php
                                                                            endforeach;
                                                                            endif;
                                                                            ?>
                                                                    </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-12 margin-t-5">
                                                        <label class="col-lg-4 control-label">Due NLT</label>
                                                        <div class="col-lg-7">
                                                            <div class="input-group">
                                                                <input type="text" name="due_nlt"
                                                                    class="form-control datetimepicker"
                                                                    value="<?php
                                                                    if (!empty($contact_request_proposal) && !empty($contact_request_proposal[0]->due_nlt)) {
                                                                        echo $contact_request_proposal[0]->due_nlt;
                                                                    } else {
                                                                        $day = date('d') + 3;
                                                                        if($day < 10)
                                                                        {
                                                                            $day = '0'. $day;
                                                                        }
                                                                        echo (date('Y-m').'-'.$day.' '.date('H:i'));
                                                                    }
                                                                    ?>"
                                                                    >
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-12 margin-t-5">
                                                        <label
                                                            class="col-lg-4 control-label">Assigned to</label>
                                                        <div class="col-lg-7">
                                                            <div class="input-group">
                                                                <input type="text" disabled class="form-control">
                                                            </div>
                                                        </div>
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
                                                <div class="form-group">
                                                    <label
                                                        class="col-lg-4 control-label">Home Address</label>
                                                    <div class="col-lg-5">
                                                        <input type="text" class="form-control" value="<?php
                                                        if (!empty($client_info)) {
                                                            echo $client_info->homeaddress;
                                                        }
                                                        ?>" name="homeaddress">
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label
                                                        class="col-lg-4 control-label">Birthday</label>
                                                    <div class="col-lg-5">
                                                        <div class="input-group">
                                                            <input type="text" name="birthday"
                                                                class="form-control datepicker"
                                                                value="<?php
                                                                if (!empty($client_info->birthday)) {
                                                                    echo $client_info->birthday;
                                                                } else {
                                                                    echo date('Y-m-d');
                                                                }
                                                                ?>"
                                                                data-date-format="<?= config_item('date_picker_format'); ?>">
                                                            <div class="input-group-addon">
                                                                <a href="#"><i class="fa fa-calendar"></i></a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="col-lg-4 control-label">Anniversary</label>
                                                    <div class="col-lg-5">
                                                        <div class="input-group">
                                                            <input type="text" class="form-control datepicker" value="<?php
                                                            if (!empty($client_info->anniversary)) {
                                                                echo $client_info->anniversary;
                                                            }else{
                                                                echo date('Y-m-d');
                                                            }
                                                            ?>" name="anniversary">
                                                            <div class="input-group-addon">
                                                                <a href="#"><i class="fa fa-calendar"></i></a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label
                                                        class="col-lg-4 control-label">Family</label>
                                                    <div class="col-lg-5">
                                                        <div class="col-lg-12 margin-t-5">
                                                            <label class="col-lg-4 control-label">Spouse<span class="text-danger"> *</span></label>
                                                            <div class="col-lg-8">
                                                                <input type="text" class="form-control" value="<?php
                                                                if (!empty($client_info->spouse)) {
                                                                    echo $client_info->spouse;
                                                                }
                                                                ?>" name="spouse">
                                                            </div>
                                                        </div>
                                                        
                                                        <div class="col-lg-12 margin-t-5 additional_icon">
                                                            <label class="col-lg-4 control-label">Child</label>
                                                            <div class="col-lg-8">
                                                            <?php if(!empty($child_info)){$m=0;foreach($child_info as $item){?>
                                                                <div class="col-lg-12 margin-t-5 family-child d-flex nopaddingleft nopaddingright">
                                                                    <input type="text" class="form-control" value="<?php echo $item->child;?>" name="family_child[]">
                                                                    <input type="text" class="form-control margin-l-5" value="<?php echo $item->type;?>" name="family_child_label[]">
                                                                    <div class="input-group-addon">
                                                                        <?php if($m == 0){$i++;?>
                                                                            <a class="add child-add-btn"><i class="fa fa-plus"></i></a>
                                                                        <?php }else{?>
                                                                            <a class="remove"><i class="fas fa-times"></i></a>
                                                                        <?php }?>
                                                                    </div>
                                                                </div>
                                                            <?php }}else{?>
                                                                <div class="col-lg-12 margin-t-5 family-child d-flex nopaddingleft nopaddingright">
                                                                    <input type="text" class="form-control" value="" name="family_child[]">
                                                                    <input type="text" class="form-control margin-l-5" value="" name="family_child_label[]">
                                                                    <div class="input-group-addon">
                                                                        <a class="add child-add-btn"><i class="fa fa-plus"></i></a>
                                                                    </div>
                                                                </div>
                                                            <?php }?>
                                                            </div>
                                                        </div>
                                                    
                                                        
                                                        <script type="text/javascript">
                                                            $(document).ready(function() {
                                                                var html = `<div class="col-lg-12 margin-t-5 nopaddingleft nopaddingright d-flex family-child-copy">
                                                                    <input type="text" class="form-control" value="" name="family_child[]">
                                                                    <input type="text" class="form-control margin-l-5" value="" name="family_child_label[]">
                                                                    <div class="input-group-btn"> 
                                                                        <a class="remove"><i class="fas fa-times"></i></a>
                                                                    </div>
                                                                </div>`;
                                                            //here first get the contents of the div with name class copy-fields and add it to after "after-add-more" div class.
                                                                $(".child-add-btn").click(function(){ 
                                                                    $(".family-child").after(html);
                                                                });
                                                            //here it will remove the current value of the remove button which has been pressed
                                                                $("body").on("click",".remove",function(){ 
                                                                    $(this).parents(".family-child-copy").remove();
                                                                });
                                                
                                                            });
                                                        </script>

                                                        <div class="col-lg-12 margin-t-5 additional_icon">
                                                            <label class="col-lg-4 control-label">Pet</label>
                                                            <div class="col-lg-8">
                                                            <?php if(!empty($pet_info)){$i=0;foreach($pet_info as $item){?>
                                                                <div class="col-lg-12 margin-t-5 family-pet d-flex nopaddingleft nopaddingright">
                                                                    <input type="text" class="form-control" value="<?php echo $item['pet'];?>" name="family_pet[]">
                                                                    <input type="text" class="form-control margin-l-5" value="<?php echo $item['type'];?>" name="family_pet_label[]">
                                                                    <div class="input-group-addon"> 
                                                                    <?php if($i == 0){$i++;?>
                                                                            <a class="add pet-add-btn"><i class="fa fa-plus"></i></a>
                                                                        <?php }else{?>
                                                                            <a class="remove"><i class="fas fa-times"></i></a>
                                                                        <?php }?>
                                                                    </div>
                                                                </div>
                                                                <?php }}else{?>
                                                                <div class="col-lg-12 margin-t-5 family-pet d-flex nopaddingleft nopaddingright">
                                                                    <input type="text" class="form-control" value="" name="family_pet[]">
                                                                    <input type="text" class="form-control margin-l-5" value="" name="family_pet_label[]">
                                                                    <div class="input-group-addon">
                                                                        <a class="add pet-add-btn"><i class="fa fa-plus"></i></a>
                                                                    </div>
                                                                </div>
                                                                <?php }?>
                                                            </div>
                                                        </div>
                                                       
                                                        <script type="text/javascript">
                                                            $(document).ready(function() {
                                                                var html = ` <div class="col-lg-12 margin-t-5 nopaddingleft nopaddingright d-flex family-pet-copy">
                                                                    <input type="text" class="form-control" value="" name="family_pet[]">
                                                                    <input type="text" class="form-control margin-l-5" value="" name="family_pet_label[]">
                                                                    <div class="input-group-addon"> 
                                                                        <a class="remove"><i class="fas fa-times"></i></a>
                                                                    </div></div>`;
                                                            //here first get the contents of the div with name class copy-fields and add it to after "after-add-more" div class.
                                                                $(".pet-add-btn").click(function(){ 
                                                                    $(".family-pet").after(html);
                                                                });
                                                            //here it will remove the current value of the remove button which has been pressed
                                                                $("body").on("click",".remove",function(){ 
                                                                    $(this).parents(".family-pet-copy").remove();
                                                                });
                                                
                                                            });
                                                        </script>



                                                        <div class="col-lg-12 margin-t-5">
                                                            <label class="col-lg-4 control-label">Other</label>
                                                            <div class="col-lg-8">
                                                                <input type="text" class="form-control" value="<?php
                                                                if (!empty($client_info->anniversary)) {
                                                                    echo $client_info->anniversary;
                                                                }
                                                                ?>" name="other">
                                                            </div>
                                                        </div>
                                                        
                                                    </div>
                                                    
                                                </div>
                                                <div class="form-group">
                                                    <label
                                                        class="col-lg-4 control-label">Likes & Interests <span class="text-danger"> *</span></label>
                                                    <div class="col-lg-5">
                                                        <textarea class="form-control" name="likes_interest" ><?php
                                                        if (!empty($client_info->likes_interest)) {
                                                            echo $client_info->likes_interest;
                                                        }
                                                        ?></textarea>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label
                                                        class="col-lg-4 control-label">Dislike<span class="text-danger"> *</span></label>
                                                    <div class="col-lg-5">
                                                        <textarea class="form-control" name="dislikes"><?php
                                                        if (!empty($client_info->dislikes)) {
                                                            echo $client_info->dislikes;
                                                        }
                                                        ?></textarea>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label
                                                        class="col-lg-4 control-label">Biography/Notes<span class="text-danger"> *</span></label>
                                                    <div class="col-lg-5">
                                                        <textarea class="form-control" name="biography"><?php
                                                        if (!empty($client_info->biography)) {
                                                            echo $client_info->biography;
                                                        }
                                                        ?></textarea>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label
                                                        class="col-lg-4 control-label">Personal Social Media Links</label>
                                                    <div class="col-lg-5">
                                                    <div class="col-lg-12 margin-t-5 nopaddingright">
                                                            <label
                                                            class="col-lg-4 control-label">Facebook</label>
                                                            <div class="col-lg-8 nopaddingright">
                                                                <input type="text" class="form-control" value="<?php
                                                                if (!empty($personal_info[0]->personal_facebook)) {
                                                                    echo $personal_info[0]->personal_facebook;
                                                                }
                                                                ?>" name="personal_facebook">
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-12 margin-t-5 nopaddingright">
                                                            <label
                                                            class="col-lg-4 control-label">Instagram</label>
                                                            <div class="col-lg-8 nopaddingright">
                                                                <input type="text" class="form-control" value="<?php
                                                                if (!empty($personal_info[0]->personal_instagram)) {
                                                                    echo $personal_info[0]->personal_instagram;
                                                                }
                                                                ?>" name="personal_instagram">
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-12 margin-t-5 nopaddingright">
                                                            <label
                                                            class="col-lg-4 control-label">Linkedin</label>
                                                            <div class="col-lg-8 nopaddingright">
                                                                <input type="text" class="form-control" value="<?php
                                                                if (!empty($personal_info[0]->personal_linkedin)) {
                                                                    echo $personal_info[0]->personal_linkedin;
                                                                }
                                                                ?>" name="personal_linkedin">
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-12 margin-t-5 nopaddingright">
                                                            <label
                                                            class="col-lg-4 control-label">Pinterest</label>
                                                            <div class="col-lg-8 nopaddingright">
                                                                <input type="text" class="form-control" value="<?php
                                                                if (!empty($personal_info[0]->personal_pinterest)) {
                                                                    echo $personal_info[0]->personal_pinterest;
                                                                }
                                                                ?>" name="personal_pinterest">
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-12 margin-t-5 nopaddingright">
                                                            <label
                                                            class="col-lg-4 control-label">Youtube</label>
                                                            <div class="col-lg-8 nopaddingright">
                                                                <input type="text" class="form-control" value="<?php
                                                                if (!empty($personal_info[0]->personal_youtube)) {
                                                                    echo $personal_info[0]->personal_youtube;
                                                                }
                                                                ?>" name="personal_youtube">
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-12 margin-t-5 nopaddingright">
                                                            <label
                                                            class="col-lg-4 control-label">Other</label>
                                                            <div class="col-lg-8 nopaddingright">
                                                                <input type="text" class="form-control" value="<?php
                                                                if (!empty($personal_info[0]->personal_other)) {
                                                                    echo $personal_info[0]->personal_other;
                                                                }
                                                                ?>" name="personal_other">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div><!-- ************** Web *************-->
                                            <div class="chart tab-pane" id="services_info">
                            <?php 
                                if(!empty($client_info)){
                                    $relationship_develop = $this->db->where('contact_id',$client_info->id)->get('tbl_relationship_develop')->result();
                                }
                            ?>
                                                <div class="form-group">
                                                        <label
                                                            class="col-lg-4 control-label">Client Code</label>
                                                        <div class="col-lg-5">
                                                            <input class="form-control" type="input" name="client_code" value="<?php if(!empty($client_info)){echo $client_info->client_code;}?>">
                                                        </div>
                                                </div>
                                                <div class="form-group">
                                                    <label
                                                        class="col-lg-4 control-label">Company Color</label>
                                                    <div class="col-lg-5">
                                                        <input type="text" class="form-control" value="<?php
                                                        if (!empty($client_info->company_color)) {
                                                            echo $client_info->company_color;
                                                        }
                                                        ?>" name="company_color" >
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label
                                                        class="col-lg-4 control-label">SEO Keywords</label>
                                                    <div class="col-lg-5">
                                                        <input type="text" class="form-control" value="<?php
                                                        if (!empty($client_info->seo_keyword)) {
                                                            echo $client_info->seo_keyword;
                                                        }
                                                        ?>" name="seo_keyword" >
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label
                                                        class="col-lg-4 control-label">Adwords</label>
                                                    <div class="col-lg-5">
                                                    <div class="col-lg-12 nopaddingright margin-t-5">
                                                        <label class="col-lg-6 control-label">Account Number</label>
                                                        <div class="col-lg-6 nopaddingright">
                                                            <input type="text" data-parse-type="number" class="form-control" name="account_number" value="<?php
                                                        if (!empty($client_info->account_number)) {
                                                            echo $client_info->account_number;
                                                        }
                                                        ?>"> 
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="col-lg-12 nopaddingright margin-t-5">
                                                        <label
                                                            class="col-lg-6 control-label">Monthly budget</label>
                                                        <div class="col-lg-6 nopaddingright">
                                                            <input type="text" class="form-control" name="monthly_budget" value="<?php
                                                        if (!empty($client_info->monthly_budget)) {
                                                            echo $client_info->monthly_budget;
                                                        }
                                                        ?>"> 
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-12 nopaddingright margin-t-5">
                                                            <div class="col-lg-12">
                                                                <label class="col-lg-6 control-label">Default schedule</label>
                                                            </div>
                                                            
                                                                <label
                                                                class="col-lg-6 control-label margin-t-5">Monday</label>
                                                                <div class="col-lg-6 nopaddingright margin-t-5">
                                                                    <input type="text" class="form-control" name="schedule_monday" value="<?php
                                                        if (!empty($client_info->schedule_money)) {
                                                            echo $client_info->schedule_money;
                                                        }
                                                        ?>" > 
                                                            </div>
                                                            
                                                            <label
                                                            class="col-lg-6 control-label margin-t-5">Tuesday</label>
                                                            <div class="col-lg-6 nopaddingright margin-t-5">
                                                                <input type="text" class="form-control"  name="schedule_tuesday" value="<?php
                                                        if (!empty($client_info->schedule_tuesday)) {
                                                            echo $client_info->schedule_tuesday;
                                                        }
                                                        ?>"> 
                                                            </div>
                                                            <label
                                                            class="col-lg-6 control-label margin-t-5">Wednesday</label>
                                                            <div class="col-lg-6 nopaddingright margin-t-5">
                                                                <input type="text" class="form-control" name="schedule_wednesday" value="<?php
                                                        if (!empty($client_info->schedule_wednesday)) {
                                                            echo $client_info->schedule_wednesday;
                                                        }
                                                        ?>"> 
                                                            </div>
                                                            <label
                                                            class="col-lg-6 control-label margin-t-5">Thursday</label>
                                                            <div class="col-lg-6 nopaddingright margin-t-5">
                                                                <input type="text" class="form-control" name="schedule_thursday" value="<?php
                                                        if (!empty($client_info->schedule_thursday)) {
                                                            echo $client_info->schedule_thursday;
                                                        }
                                                        ?>"> 
                                                            </div>
                                                            <label
                                                            class="col-lg-6 control-label margin-t-5">Friday</label>
                                                            <div class="col-lg-6 nopaddingright margin-t-5">
                                                                <input type="text" class="form-control" name="schedule_friday" value="<?php
                                                        if (!empty($client_info->schedule_friday)) {
                                                            echo $client_info->schedule_friday;
                                                        }
                                                        ?>"> 
                                                            </div>
                                                            <label
                                                            class="col-lg-6 control-label margin-t-5">Saturday</label>
                                                            <div class="col-lg-6 nopaddingright margin-t-5">
                                                                <input type="text" class="form-control" name="schedule_saturday" value="<?php
                                                        if (!empty($client_info->schedule_saturday)) {
                                                            echo $client_info->schedule_saturday;
                                                        }
                                                        ?>"> 
                                                            </div>
                                                            <label
                                                            class="col-lg-6 control-label margin-t-5">Sunday</label>
                                                            <div class="col-lg-6 nopaddingright margin-t-5">
                                                                <input type="text" class="form-control" name="schedule_sunday" value="<?php
                                                        if (!empty($client_info->schedule_sunday)) {
                                                            echo $client_info->schedule_sunday;
                                                        }
                                                        ?>"> 
                                                            </div>
                                                        
                                                    </div>
                                                    <div class="col-lg-12 nopaddingright margin-t-5">
                                                        <label
                                                            class="col-lg-6 control-label">Target areas</label>
                                                        <div class="col-lg-6 nopaddingright">
                                                            <input type="text" class="form-control" name="target_areas" value="<?php
                                                        if (!empty($client_info->target_areas)) {
                                                            echo $client_info->target_areas;
                                                        }
                                                        ?>"> 
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-12 nopaddingright margin-t-5">
                                                        <label
                                                            class="col-lg-6 control-label">Call tracking phone number</label>
                                                        <div class="col-lg-6 nopaddingright">
                                                            <input type="text" class="form-control" name="call_tracking_phone" value="<?php
                                                        if (!empty($client_info->call_tracking_phone)) {
                                                            echo $client_info->call_tracking_phone;
                                                        }
                                                        ?>"> 
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-12 nopaddingright margin-t-5">
                                                        <label
                                                            class="col-lg-6 control-label">Nagative Keywords</label>
                                                        <div class="col-lg-6 nopaddingright">
                                                            <input type="text" class="form-control" name="nagative_keyword" value="<?php
                                                        if (!empty($client_info->nagative_keyword)) {
                                                            echo $client_info->nagative_keyword;
                                                        }
                                                        ?>"> 
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-12 nopaddingright margin-t-5">
                                                        <label
                                                            class="col-lg-6 control-label">Adwords Notes</label>
                                                        <div class="col-lg-6 nopaddingright">
                                                            <input type="text" class="form-control" name="adwords_notes" value="<?php
                                                        if (!empty($client_info->adwords_notes)) {
                                                            echo $client_info->adwords_notes;
                                                        }
                                                        ?>"> 
                                                        </div>
                                                    </div>
                                                    </div>
                                                </div>
                                                
                                                <div class="form-group">
                                                    <label class="col-lg-4 control-label">Content Marketing</label>
                                                    <div class="col-lg-5">
                                                        <div class="col-lg-12 nopaddingright margin-t-5">
                                                            <label class="col-lg-6 control-label">Articles per month</label>
                                                            <div class="col-lg-6 nopaddingright">
                                                                <input type="text" class="form-control" name="articles_per_month" value="<?php
                                                        if (!empty($client_info->articles_per_month)) {
                                                            echo $client_info->articles_per_month;
                                                        }
                                                        ?>"> 
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-12 nopaddingright margin-t-5">
                                                            <label
                                                                class="col-lg-6 control-label">Content Notes</label>
                                                            <div class="col-lg-6 nopaddingright">
                                                                <input type="text" class="form-control" name="content_notes" value="<?php
                                                        if (!empty($client_info->content_notes)) {
                                                            echo $client_info->content_notes;
                                                        }
                                                        ?>"> 
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                        <label class="col-lg-4 control-label">Social Media</label>
                                                    <div class="col-lg-5">
                                                        <div class='col-lg-12 margin-t-5 nopaddingright'>
                                                            <label class="col-lg-6 control-label">Strategy</label>
                                                            <div class="col-lg-6 nopaddingright">
                                                                <textare class="form-control" name="strategy"><?php
                                                        if (!empty($client_info->strategy)) {
                                                            echo $client_info->strategy;
                                                        }
                                                        ?></textarea>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-12 margin-t-5 nopaddingright">
                                                            <label class="col-lg-6 control-label margin-t-5 ">Relationships to Develop</label>
                                                            <div class="col-lg-6 nopaddingright">
                                                            </div>
                                                        </div>
                                                        
                                                       <?php 
                                                    if(!empty($relationship_develop))
                                                    {
                                                       foreach($relationship_develop as $item)
                                                       {
                                                        ?>
                                                        <div class="relationship-develop">
                                                                <div class="col-lg-12 nopaddingright margin-t-5">
                                                                    <label class="col-lg-6 control-label">Name</label>
                                                                    <div class="col-lg-6 nopaddingright">
                                                                        <input type="text" class="form-control" name="relationship_name[]" value="<?php echo $item->name;?>"> 
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-12 nopaddingright margin-t-5">
                                                                    <label class="col-lg-6 control-label">Profile Link</label>
                                                                    <div class="col-lg-6 nopaddingright">
                                                                        <input type="text" class="form-control" name="relationship_profile_link[]" value="<?php echo $item->profile_link;?>"> 
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-12 nopaddingright margin-t-5">
                                                                    <label class="col-lg-6 control-label">Notes</label>
                                                                    <div class="col-lg-6 nopaddingright">
                                                                        <input type="text" class="form-control" name="relationship_notes[]" value="<?php echo $item->notes;?>"> 
                                                                    </div>
                                                                </div>

                                                        </div>
                                                       <?php }
                                                       }else{
                                                       ?>
                                                        <div class="relationship-develop">
                                                           
                                                                <div class="col-lg-12 nopaddingright margin-t-5">
                                                                    <label class="col-lg-6 control-label">Name</label>
                                                                    <div class="col-lg-6 nopaddingright">
                                                                        <input type="text" class="form-control" name="relationship_name[]" value=""> 
                                                                    </div>
                                                                </div>

                                                                <div class="col-lg-12 nopaddingright margin-t-5">
                                                                    <label class="col-lg-6 control-label">Profile Link</label>
                                                                    <div class="col-lg-6 nopaddingright">
                                                                        <input type="text" class="form-control" name="relationship_profile_link[]" value=""> 
                                                                    </div>
                                                            </div>
                                                                <div class="col-lg-12 nopaddingright margin-t-5">
                                                                    <label class="col-lg-6 control-label">Notes</label>
                                                                    <div class="col-lg-6 nopaddingright">
                                                                        <input type="text" class="form-control" name="relationship_notes[]" value=""> 
                                                                    </div>
                                                            </div>
                                                        </div>

                                                        <?php }?>
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
                                            <input type="text" class="form-control" 
                                                value="<?php
                                                if (!empty($client_info->access_name)) {
                                                    echo $client_info->access_name;
                                                }
                                                ?>" name="access_name" >
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-lg-4 control-label">Login URL
                                            <span class="text-danger"> *</span></label>
                                        <div class="col-lg-5">
                                            <input type="text" class="form-control" 
                                                value="<?php
                                                if (!empty($client_info->access_login_url)) {
                                                    echo $client_info->access_login_url;
                                                }
                                                ?>" name="access_login_url" >
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
                                                        <input type="text" class="form-control" 
                                                            value="<?php
                                                            if (!empty($item->access_username)) {
                                                                echo $item->access_username;
                                                            }
                                                            ?>" name="access_username[]">
                                                    </div>
                                                </div>
                                                <div class="col-lg-12 margin-t-5 nopaddingright">
                                                    <label class="col-lg-4 control-label">Associated Email Address</label>
                                                    <div class="col-lg-8 nopaddingright">
                                                        <input type="text" class="form-control" 
                                                            value="<?php
                                                            if (!empty($item->access_email_address)) {
                                                                echo $item->access_email_address;
                                                            }
                                                            ?>" name="access_email_address[]">
                                                    </div>
                                                </div>
                                                <div class="col-lg-12 margin-t-5 nopaddingright">
                                                    <label class="col-lg-4 control-label">Password</label>
                                                    <div class="col-lg-8 nopaddingright">
                                                        <input type="text" class="form-control" 
                                                            value="<?php
                                                            if (!empty($item->access_password)) {
                                                                echo $item->access_password;
                                                            }
                                                            ?>" name="access_password[]">
                                                    </div>
                                                </div>
                                                <div class="col-lg-12 margin-t-5 nopaddingright">
                                                    <label class="col-lg-4 control-label">Who can view this</label>
                                                    <div class="col-lg-8 nopaddingright">
                                                        <input type="text" class="form-control"
                                                            value="<?php
                                                            if (!empty($item->access_view)) {
                                                                echo $item->access_view;
                                                            }
                                                            ?>" name="access_view[]">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <?php 
                                            }
                                        }
                                        else
                                        {
                                        ?>
                                        <div>
                                            <div class="col-lg-5">
                                                <div class="col-lg-12 margin-t-5 nopaddingright">
                                                    <label class="col-lg-4 control-label">Username</label>
                                                    <div class="col-lg-8 nopaddingright">
                                                        <input type="text" class="form-control" 
                                                            value="" name="access_username[]">
                                                    </div>
                                                </div>
                                                <div class="col-lg-12 margin-t-5 nopaddingright">
                                                    <label class="col-lg-4 control-label">Associated Email Address</label>
                                                    <div class="col-lg-8 nopaddingright">
                                                        <input type="text" class="form-control" 
                                                            value="" name="access_email_address[]">
                                                    </div>
                                                </div>
                                                <div class="col-lg-12 margin-t-5 nopaddingright">
                                                    <label class="col-lg-4 control-label">Password</label>
                                                    <div class="col-lg-8 nopaddingright">
                                                        <input type="text" class="form-control" 
                                                            value="" name="access_password[]">
                                                    </div>
                                                </div>
                                                <div class="col-lg-12 margin-t-5 nopaddingright">
                                                    <label class="col-lg-4 control-label">Who can view this</label>
                                                    <div class="col-lg-8 nopaddingright">
                                                        <input type="text" class="form-control" 
                                                            value="" name="access_view[]">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <?php }?>
                                    
                                    </div>
                                </div>

                                        </div>
                                    </div><!-- /.nav-tabs-custom -->
                                    <div class="form-group mt">
                                        <label class="col-lg-3"></label>
                                        <div class="col-lg-1">
                                            <button type="submit"
                                                    class="btn btn-sm btn-primary"><?= lang('save') ?></button>
                                        </div>
                                        

                                    </div>
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
<link rel="stylesheet" href="<?= base_url() ?>assets/plugins/datetimepicker/jquery.datetimepicker.min.css">
<?php include_once 'assets/plugins/datetimepicker/jquery.datetimepicker.full.php'; ?>
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
        var opt_time = {
                lazyInit: true,
                scrollInput: false,
                format: 'Y-m-d H:i',
            };
            $('.datetimepicker').datetimepicker(opt_time);
        $("#mobile_phone").inputmask({"mask": "(999) 999-9999"});
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
                var child = $(this).parents('.referral-type-source').find('.child-settings');
                if(child.css('display')=='none')
                {
                    $(this).parents('.referral-type-source').find('.child-settings').show();
                }
                else{
                    $(this).parents('.referral-type-source').find('.child-settings').hide();
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
