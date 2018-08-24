<?php include_once 'assets/admin-ajax.php'; ?>
<style>
.msg{
margin-top: 0;
}
#image_preview1{
margin-bottom:5px;
}
.uinfo{
width:410px;
}
#div2{
    border: 1px solid #e4eaec;
}

</style>


<?= message_box('success'); ?>
<?= message_box('error');
$created = can_action('24', 'created');
$edited = can_action('24', 'edited');
$deleted = can_action('24', 'deleted');
if (!empty($created) || !empty($edited)){
    ?>
    <style>
        .note-editor .note-editable {
            height: 220px !important;
        }
    </style>
    
    <div class="nav-tabs-custom">
        <!-- Tabs within a box -->
        <ul class="nav nav-tabs">
            <li class="<?= $active == 1 ? 'active' : ''; ?>"><a href="#manage"
                                                                data-toggle="tab"><?= lang('all_staffs') ?></a></li>
            <li class="<?= $active == 2 ? 'active' : ''; ?>"><a href="#new"
                                                                data-toggle="tab"><?= lang('new_staff') ?></a>
            </li>
        </ul>
        <div class="tab-content bg-white">
            <!-- ************** general *************-->
            <div class="tab-pane <?= $active == 1 ? 'active' : ''; ?>" id="manage">
                <?php } else { ?>
                <div class="panel panel-custom">
                    <header class="panel-heading ">
                        <div class="panel-title"><strong><?= lang('all_users') ?></strong></div>
                    </header>
                    <?php } ?>
                    <table class="table table-striped DataTables " id="DataTables" cellspacing="0" width="100%">
                        <thead>
                        <tr>
                            <th> No </th>
                            <th class="col-sm-1"><?= lang('photo') ?></th>
                            <th><?= lang('name') ?></th>
                            <th class="col-sm-2"><?= lang('username') ?></th>
                            <th class="col-sm-2">Email</th>
                            <th class="col-sm-1"><?= lang('active') ?></th>
                     
                            <?php $show_custom_fields = custom_form_table(13, null);
                            if (!empty($show_custom_fields)) {
                                foreach ($show_custom_fields as $c_label => $v_fields) {
                                    if (!empty($c_label)) {
                                        ?>
                                        <th><?= $c_label ?> </th>
                                    <?php }
                                }
                            }
                            ?>
                            <th class="col-sm-2"><?= lang('action') ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $num = 1;
                        if (!empty($all_user_info)): foreach ($all_user_info as $v_user) :
                            $account_info = $this->user_model->check_by(array('user_id' => $v_user->user_id), 'tbl_account_details');
                            if (!empty($account_info)) {
                                $can_edit = $this->user_model->can_action('tbl_users', 'edit', array('user_id' => $v_user->user_id));
                                $can_delete = $this->user_model->can_action('tbl_users', 'delete', array('user_id' => $v_user->user_id));
                                ?>

                                <tr>
                                    <td><?php echo $num; $num++;?></td>
                                    <td><img style="width: 36px;margin-right: 10px;"
                                             src="<?= base_url() ?><?= $account_info->avatar ?>" class="img-circle">
                                    </td>
                                    <td>
                                        <?php if ($v_user->role_id != 2) { ?>
                                            <a href="<?= base_url() ?>admin/user/user_list/edit_user/<?= $v_user->user_id ?>"><?= $account_info->fullname ?></a>
                                        <?php } else { ?>
                                            <?= $account_info->fullname ?>
                                        <?php } ?>

                                    </td>
                                    <td><?= ($v_user->username) ?></td>
                                    <td><?= ($v_user->email) ?></td>
                                    <td><?php if ($v_user->user_id != $this->session->userdata('user_id')): ?>
                                            <?php if (!empty($can_edit) && !empty($edited)) { ?>
                                                <div class="change_user_status">
                                                    <input data-id="<?= $v_user->user_id ?>" data-toggle="toggle"
                                                           name="active" value="1" <?php
                                                    if (!empty($v_user->activated) && $v_user->activated == '1') {
                                                        echo 'checked';
                                                    }
                                                    ?> data-on="<?= lang('yes') ?>" data-off="<?= lang('no') ?>"
                                                           data-onstyle="success btn-xs"
                                                           data-offstyle="danger btn-xs" type="checkbox">
                                                </div>
                                            <?php } else { ?>
                                                <?php if ($v_user->activated == 1): ?>
                                                    <span class="label label-success"><?= lang('active') ?></span>
                                                <?php else: ?>
                                                    <span class="label label-danger"><?= lang('deactive') ?></span>
                                                <?php endif; ?>
                                            <?php } ?>
                                        <?php else: ?>
                                            <?php if ($v_user->activated == 1): ?>
                                                <span class="label label-success"><?= lang('active') ?></span>
                                            <?php else: ?>
                                                <span class="label label-danger"><?= lang('deactive') ?></span>
                                            <?php endif; ?>
                                        <?php endif ?>
                                        <?php
                                        if ($v_user->banned == 1) {
                                            ?>
                                            <span class="label label-danger" data-toggle='tooltip' data-placement='top'
                                                  title="<?= $v_user->ban_reason ?>"><?= lang('banned') ?></span>
                                        <?php }
                                        ?></td>
                                   
                                    <?php $show_custom_fields = custom_form_table(13, $v_user->user_id);
                                    if (!empty($show_custom_fields)) {
                                        foreach ($show_custom_fields as $c_label => $v_fields) {
                                            if (!empty($c_label)) {
                                                ?>
                                                <td><?= $v_fields ?> </td>
                                            <?php }
                                        }
                                    }
                                    ?>
                                    <td><?php if ($v_user->user_id != $this->session->userdata('user_id')): ?>
                                            <?php if (!empty($can_edit) && !empty($edited)) { ?>
                                                <?php if ($v_user->banned == 1): ?>
                                                    <a data-toggle="tooltip" data-placement="top"
                                                       class="btn btn-success btn-xs"
                                                       title="Click to <?= lang('unbanned') ?> "
                                                       href="<?php echo base_url() ?>admin/user/set_banned/0/<?php echo $v_user->user_id; ?>"><span
                                                            class="fa fa-check"></span></a>
                                                <?php else: ?>
                                                    <span data-toggle="tooltip" data-placement="top"
                                                          title="Click to <?= lang('banned') ?> ">
                                                <?php echo btn_banned_modal('admin/user/change_banned/' . $v_user->user_id); ?>
                                                    </span>
                                                <?php endif; ?>
                                            <?php } ?>

                                            <a data-toggle="tooltip" data-placement="top" class="btn btn-info btn-xs"
                                               title="<?= lang('send') . ' ' . lang('wellcome_email') ?> "
                                               href="<?php echo base_url() ?>admin/user/send_welcome_email/<?php echo $v_user->user_id; ?>"><span
                                                    class="fa fa-envelope-o"></span></a>

                                            <?php if (!empty($can_edit) && !empty($edited)) { ?>
                                                <?php echo btn_edit('admin/user/user_list/edit_user/' . $v_user->user_id); ?>
                                            <?php }
                                            if (!empty($can_delete) && !empty($deleted)) {
                                                ?>
                                                <?php echo btn_delete('admin/user/delete_user/' . $v_user->user_id); ?>
                                            <?php } ?>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php
                            };
                        endforeach;
                            ?>
                        <?php else : ?>
                            <td colspan="3">
                                <strong><?= lang('nothing_to_display') ?></strong>
                            </td>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <?php if (!empty($created) || !empty($edited)){ ?>
                    <div class="tab-pane <?= $active == 2 ? 'active' : ''; ?>" id="new">
                        <form role="form" data-parsley-validate="" novalidate="" id="userform"
                              enctype="multipart/form-data"
                              action="<?php echo base_url(); ?>admin/user/save_user" method="post"
                              class="form-horizontal form-groups-bordered custom-form">

                            <?php
                            if (!empty($login_info->user_id)) {
                                $profile_info = $this->user_model->check_by(array('user_id' => $login_info->user_id), 'tbl_account_details');
                                $permission_role = $this->db->where('user_id', $login_info->user_id)->get('tbl_staff_role')->result_array();
                            }
                            ?>
                            <input type="hidden" id="username_flag" value="">
                            <input type="hidden" id="user_id" name="user_id" value="<?php
                            if (!empty($login_info)) {
                                echo $login_info->user_id;
                            }
                            ?>">
                            <input type="hidden" name="account_details_id" value="<?php
                            if (!empty($profile_info)) {
                                echo $profile_info->account_details_id;
                            }
                            ?>">
                            <div class="form-group">
                                <label class="col-sm-3 control-label"><?= lang('first_name') ?><span
                                        class="text-danger">*</span></label>
                                <div class="col-sm-5">
                                    <input type="text" class="input-sm form-control" value="<?php
                                    if (!empty($profile_info)) {
                                        echo $profile_info->first_name;
                                    }
                                    ?>"
                                           placeholder="<?= lang('eg') ?> <?= lang('enter_your') . ' ' . lang('first_name') ?>"
                                           name="first_name"
                                           required onkeyUp="document.getElementById('Uname').innerHTML = this.value">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label"><?= lang('last_name') ?> <span
                                        class="text-danger">*</span></label>
                                <div class="col-sm-5">
                                    <input type="text" id="last_name" class="input-sm form-control" value="<?php
                                    if (!empty($profile_info)) {
                                        echo $profile_info->last_name;
                                    }
                                    ?>"
                                           placeholder="<?= lang('eg') ?> <?= lang('enter_your') . ' ' . lang('last_name') ?>"
                                           name="last_name"
                                           required onKeyup="addjobtitle()">
                                </div>
                            </div>

                                <div class="form-group">
                                    <label
                                        class="col-sm-3 control-label"> <?= lang('nick_name'); ?><span
                                            class="text-danger">*</span></label>
                                    <div class="col-sm-5">
                                        <input type="text" name="username" id="check_username"
                                               placeholder="<?= lang('eg') ?> <?= lang('enter_your') . ' ' . lang('username') ?>"
                                               value="<?php
                                               if (!empty($login_info)) {
                                                   echo $login_info->username;
                                               }
                                               ?>" class="input-sm form-control" required >
                                        <span class="required" id="check_username_error"></span>
                                    </div>
                                </div>


                            <div class="form-group">
                                <label class="col-sm-3 control-label"><?= lang('email') ?><span
                                        class="text-danger">*</span></label>
                                <div class="col-sm-5">
                                    <input type="email" id="check_email_addrees"
                                           placeholder="<?= lang('eg') ?> <?= lang('user_placeholder_email') ?>"
                                           name="email" value="<?php
                                    if (!empty($login_info)) {
                                        echo $login_info->email;
                                    }
                                    ?>" class="input-sm form-control" required onKeyup="addemailid()">
                                    <span class="required" id="email_addrees_error"></span>
                                </div>
                            </div>
                            <?php 
                                $additional_phone_label = $this->db->get('tbl_additional_label')->result();
                                $email_num = 0;
                            ?>
                                <?php if (!empty($additional_emails))
                                {
                                    foreach ($additional_emails as $key=> $additional_email)
                                    {
                                    ?>

                            <div class="form-group">
                           
                                <label class="col-sm-3 control-label"><?= lang('additional_email') ?></label>
                                <div class="col-sm-5 additional_email_group additional_icon">
                                    <div class="additional_email_item d-flex">
                                            <input type="email" id="check_additional_email_addrees"
                                                placeholder="<?= lang('eg') ?> <?= lang('additional_email') ?>"
                                                name="additional_email[<?php echo $additional_email['id']; ?>]" value="<?php
                                                echo $additional_email['additional_email'];
                                            ?>" class="input-sm form-control">
                                                                    
                                            <select class="additional_email_label form-control margin-l-5" required name="additional_email_label[<?php echo $additional_email['id'] ?>]">
                                                    <?php foreach ($additional_phone_label as $item_label)
                                                        {
                                                    ?>
                                                        <option value="<?php echo $item_label->id;?>" <?php if($item_label->id == $additional_email['type']){echo 'selected';}?>><?php echo $item_label->label;?></option>
                                                    <?php }?>
                                                
                                            </select>
                                            <?php if($email_num == 0){?>
                                                <div class="input-group-addon">
                                                    <a data-toggle="modal" data-target="#myModal" href="<?= base_url() ?>admin/user/add_additional_label_modal" class="add-additional-label-modal"><i class="fa fa-pencil"></i></a>
                                                </div>
                                                <div class="input-group-addon">
                                                    <a class="add email-add-more"><i class="fa fa-plus"></i></a>
                                                </div>
                                            <?php $email_num++;}else{?>
                                                <div class="input-group-addon">
                                                    <a class="remove"><i class="fas fa-times"></i></a>
                                                </div>
                                            <?php }?>
                                    </div>
                                </div>
                            </div>
                            <?php }
                                }?>


                            <?php if (empty($additional_emails))
                            {
                            ?>

                            <div class="form-group">
                                <label class="col-sm-3 control-label"><?= lang('additional_email') ?></label>
                                <div class="col-sm-5 additional_email_group additional_icon">
                                    <div class="additional-email-item d-flex">
                                        <input type="email" id="check_additional_email_addrees"
                                            placeholder="<?= lang('eg') ?> <?= lang('additional_email') ?>"
                                            name="additional_email[]" value="" class="input-sm form-control">
                                
                                        <select class="additional_email_label form-control margin-l-5" required name="additional_email_label[]">
                                            <?php foreach ($additional_phone_label as $item_label)
                                                {
                                                ?>
                                                <option value="<?php echo $item_label->id;?>"><?php echo $item_label->label;?></option>
                                            <?php }?>
                                        </select>
                                        <div class="input-group-addon">
                                            <a data-toggle="modal" data-target="#myModal" href="<?= base_url() ?>admin/user/add_additional_label_modal" class="add-additional-label-modal"><i class="fa fa-pencil"></i></a>
                                        </div>
                                        <div class="input-group-addon">
                                            <a class="add email-add-more"><i class="fa fa-plus"></i></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php 
                                }
                                ?>
                            <script type="text/javascript">
                                $(document).ready(function() {
                                //here first get the contents of the div with name class copy-fields and add it to after "after-add-more" div class.
                                $(".email-add-more").click(function(){ 
                                    var html = $(this).parents('.additional-email-item').find('.additional_phone_select').html();
                                    html = `<div class="d-flex staff-margin-top margin-l-5"><input type="text" class="input-sm form-control" value="" name="additional_email[]"
                                                placeholder="e.g Enter Your Additional Email"><select class="form-control additional_phone_select margin-l-5" name="additional_email_label[]" style="width: 100%">`+ html + `</select><div class="input-group-addon"> 
                                                               
                                                            </div><div class="input-group-addon"> 
                                                                <a class="remove"><i class="fas fa-times"></i></a>
                                                            </div></div>`;
                                    $(".additional_email_group").append(html);
                                });
                            //here it will remove the current value of the remove button which has been pressed
                                $("body").on("click",".remove",function(){ 
                                    $(this).parents(".d-flex").remove();
                                });
                            
                                });
                                                
                                </script>
                            <div class="form-group">
                                <label class="col-sm-3 control-label"><?= lang('phone') ?> </label>
                                <div class="col-sm-5">
                                    <input type="text" class="input-sm form-control" id="phone"  value="<?php
                                    if (!empty($profile_info)) {
                                        echo $profile_info->phone;
                                    }
                                    ?>" name="phone"
                                           placeholder="<?= lang('eg') ?> <?= lang('user_placeholder_phone') ?>" required  onKeyup="addmobileno()"> 
                                </div>
                            </div>

                           <?php 
                                if(!empty($additional_phones))
                                {
                                    $m = 0;
                                    foreach($additional_phones as $key=>$additional_phone)
                                    {
                           ?>
                            <div class="form-group">
                                <label class="col-sm-3 control-label"><?= lang('additional_phone') ?></label>
                                <div class="col-sm-5 additional-phone-group additional_icon">
                                    <div class="additional-phone-item d-flex">
                                        <input type="text" class="input-sm form-control" value="<?php
                                                echo $additional_phone['additional_phone'];
                                            ?>" name="additional_phone[<?php echo $additional_phone['id'];?>]"
                                                placeholder="<?= lang('eg') ?> ">
                                        
                                                <select class="form-control margin-l-5 additional_phone_select" name="additional_phone_label[<?php echo $additional_phone['id'];?>]">
                                                        <?php foreach ($additional_phone_label as $item_label)
                                                        {
                                                        ?>
                                                            <option value="<?php echo $item_label->id;?>"><?php echo $item_label->label;?></option>
                                                        <?php }?>
                                                </select>
                                        
                                            <?php if($m == 0){?>
                                                <div class="input-group-addon">
                                                    <a data-toggle="modal" data-target="#myModal" href="<?= base_url() ?>admin/user/add_additional_label_modal" class="add-additional-label-modal"><i class="fa fa-pencil"></i></a>
                                                </div>
                                                <div class="input-group-addon">
                                                    <a class="add phone-add-more"><i class="fa fa-plus"></i></a>
                                                </div>
                                            <?php $m++;}else{?>
                                                <div class="input-group-addon">
                                                    <a class="remove"><i class="fas fa-times"></i></a>
                                                </div>
                                            <?php }?>
                                        

                                    </div>
                                        
                                </div>
                            </div>

                            <?php 
                                 }
                                }
                            ?>
                            <?php 
                                if(empty($additional_phones))
                                {
                           ?>
                            <div class="form-group">
                                <label class="col-sm-3 control-label"><?= lang('additional_phone') ?></label>
                                <div class="col-sm-5 additional-phone-group additional_icon">
                                    <div class="additional-phone-item d-flex">
                                        <input type="text" class="input-sm form-control" value="" name="additional_phone[]"
                                                placeholder="e.g Enter Your Additional Phone Number">
                                        <?php 
                                            $additional_phone_label = $this->db->get('tbl_additional_label')->result();
                                        ?>
                                        <select class="form-control additional_phone_select margin-l-5" name="additional_phone_label[]" style="width: 100%">
                                                <?php foreach ($additional_phone_label as $item_label)
                                                {
                                                ?>
                                                <option value="<?php echo $item_label->id;?>"><?php echo $item_label->label;?></option>
                                                <?php }?>
                                        </select>
                                        <div class="input-group-addon">
                                            <a data-toggle="modal" data-target="#myModal" href="<?= base_url() ?>admin/user/add_additional_label_modal" class="add-additional-label-modal"><i class="fa fa-pencil"></i></a>
                                        </div>
                                        <div class="input-group-addon">
                                            <a class="add phone-add-more"><i class="fa fa-plus"></i></a>
                                        </div>
                                    </div>
                                        
                                </div>
                            </div>

                            <?php 
                                }
                            ?>
                            

                            <script type="text/javascript">
                                $(document).ready(function() {
                                //here first get the contents of the div with name class copy-fields and add it to after "after-add-more" div class.
                                $(".phone-add-more").click(function(){ 
                                    var html = $(this).parents('.additional-phone-item').find('.additional_phone_select').html();
                                    html = `<div class="d-flex staff-margin-top margin-l-5"><input type="text" class="input-sm form-control" value="" name="additional_phone[]"
                                                placeholder="e.g Enter Your Additional Phone Number"><select class="form-control additional_phone_select margin-l-5" name="additional_phone_label[]" style="width: 100%">`+ html + `</select><div class="input-group-addon"> 
                                                               
                                                            </div><div class="input-group-addon"> 
                                                                <a class="remove"><i class="fas fa-times"></i></a>
                                                            </div></div>`;
                                    $(".additional-phone-group").append(html);
                                });
                            //here it will remove the current value of the remove button which has been pressed
                                $("body").on("click",".remove",function(){ 
                                    $(this).parents(".d-flex").remove();
                                });
                            
                                });
                                                
                                </script>


                            
                             <!--white_label_brand -->
                             <div class="form-group">
                                <label class="col-sm-3 control-label"><?= lang('white_label_brand') ?></label>
                                <div class="col-sm-5">
                                    <select name="white_label_brand" class="form-control select_box"
                                                    style="width: 100%" required>
                                        <?php if (!empty($whitebrand)): foreach ($whitebrand as $item): ?>
                                            <option value="<?= $item->id ?>" <?= (!empty($profile_info->white_label_brand) && $profile_info->white_label_brand == $item->white_brand ? 'selected' : NULL) ?>><?= $item->white_brand ?>
                                            </option>
                                        <?php 
                                            endforeach;
                                        endif;
                                        ?>
                                    </select>
                                </div>
                            </div>   
                            <input type="hidden" name="role_id" value="3"/>
                            <!-- role -->

                            <?php 
                                if(!empty($profile_info->role_ids)){
                                    $roles = explode(',',$profile_info->role_ids);
                                }
                            ?>
                            <div class="form-group">
                                <label for="field-1"
                                       class="col-sm-3 control-label">Roles</label>
                                <div class="col-sm-5 multi-select-role">
                                    <select class="selectpicker form-control" id="user_type" name="role_ids[]" multiple="multiple" required>
                                        <option value='1' <?php if(!empty($permission_role)){ 
                                            if($permission_role[0]['super_admin'] == 1){
                                                echo 'selected';
                                            }
                                    }
                                             ?>>Super Admin</option>
                                        <option value='2' <?php if(!empty($permission_role)){ 
                                            if($permission_role[0]['sales_rep'] == 1){
                                                echo 'selected';
                                            }
                                    }
                                             ?>>Sales Rep</option>
                                        <option value='3' <?php if(!empty($permission_role)){ 
                                            if($permission_role[0]['graphic_designer'] == 1){
                                                echo 'selected';
                                            }
                                    }
                                             ?>>Graphic Designer</option>
                                        <option value='4' <?php if(!empty($permission_role)){ 
                                            if($permission_role[0]['web_developer'] == 1){
                                                echo 'selected';
                                            }
                                    }
                                             ?>>Web developer</option>
                                        <option value='5' <?php if(!empty($permission_role)){ 
                                            if($permission_role[0]['content_writer'] == 1 ){
                                                echo 'selected';
                                            }
                                    }
                                             ?>>Content Writer</option>
                                        <option value='6' <?php if(!empty($permission_role)){ 
                                            if($permission_role[0]['content_admin'] == 1){
                                                echo 'selected';
                                            }
                                    }
                                             ?>>Content Admin</option>
                                        <option value='7' <?php if(!empty($permission_role)){ 
                                            if($permission_role[0]['seo'] == 1){
                                                echo 'selected';
                                            }
                                    }
                                             ?>>SEO</option>
                                        <option value='8' <?php if(!empty($permission_role)){ 
                                            if($permission_role[0]['seo_admin'] == 1){
                                                echo 'selected';
                                            }
                                    }
                                             ?>>SEO Admin</option>
                                        <option value='9' <?php if(!empty($permission_role)){ 
                                            if($permission_role[0]['sem'] == 1){
                                                echo 'selected';
                                            }
                                    }
                                             ?>>Social Media Manager</option>
                                        <option value='10' <?php if(!empty($permission_role)){ 
                                            if($permission_role[0]['sem_admin'] == 1){
                                                echo 'selected';
                                            }
                                    }
                                             ?>>Social Media Admin</option>
                                        <option value="11" <?php if(!empty($permission_role)){ 
                                            if($permission_role[0]['social_media_manager'] == 1){
                                                echo 'selected';
                                            }
                                    }
                                             ?>>Social Media Manager</option>
                                        <option value="12" <?php if(!empty($permission_role)){ 
                                            if($permission_role[0]['social_media_admin'] == 1){
                                                echo 'selected';
                                            }
                                    }
                                             ?>>Social Media Admin</option>
                                        <option value='13' <?php if(!empty($permission_role)){ 
                                            if($permission_role[0]['accounting'] == 1){
                                                echo 'selected';
                                            }
                                    }
                                             ?>>Accounting</option>
                                        <option value='14' <?php if(!empty($permission_role)){ 
                                            if($permission_role[0]['proposal_writer'] == 1){
                                                echo 'selected';
                                            }
                                    }
                                             ?>>Proposal Writer</option>
                                        <option value="15" <?php if(!empty($permission_role)){ 
                                            if($permission_role[0]['proposal_admin'] == 1){
                                                echo 'selected';
                                            }
                                    }
                                             ?>>Proposal Admin</option>
                                        <option value="16" <?php if(!empty($permission_role)){ 
                                            if($permission_role[0]['human_resources']==1){
                                                echo 'selected';
                                            }
                                    }
                                             ?>>Human Resources</option>
                                        <option value="17" <?php if(!empty($permission_role)){ 
                                            if($permission_role[0]['sales_admin']==1){
                                                echo 'selected';
                                            }
                                    }
                                             ?>>Sales Admin</option>
                                    </select>
                                </div>
                              
                            </div>
                            <div class="form-group" style="margin-bottom: 15px">
                                    <label for="field-1"
                                           class="col-sm-3 control-label">Staff Files</label>

                                    <div class="col-sm-5">
                                        <div id="comments_file-dropzone" class="dropzone mb15">

                                        </div>
                                        <div id="comments_file-dropzone-scrollbar">
                                            <div id="comments_file-previews">
                                                <div id="file-upload-row" class="mt pull-left">
                                                    <div class="preview box-content pr-lg" style="width:100px;">
                                                    <span data-dz-remove class="pull-right" style="cursor: pointer">
                                    <i class="fa fa-times"></i>
                                </span>
                                                        <img data-dz-thumbnail class="upload-thumbnail-sm"/>
                                                        <input class="file-count-field" type="hidden" name="files[]"
                                                               value=""/>
                                                        <div
                                                            class="mb progress progress-striped upload-progress-sm active mt-sm"
                                                            role="progressbar" aria-valuemin="0" aria-valuemax="100"
                                                            aria-valuenow="0">
                                                            <div class="progress-bar progress-bar-success"
                                                                 style="width:0%;"
                                                                 data-dz-uploadprogress></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <?php
                                        if (!empty($tickets_info->upload_file)) {
                                            $uploaded_file = json_decode($tickets_info->upload_file);
                                        }
                                        if (!empty($uploaded_file)) {
                                            foreach ($uploaded_file as $v_files_image) { ?>
                                                <div class="pull-left mt pr-lg mb" style="width:100px;">
                                                        <span data-dz-remove class="pull-right existing_image"
                                                              style="cursor: pointer"><i
                                                                class="fa fa-times"></i></span>
                                                    <?php if ($v_files_image->is_image == 1) { ?>
                                                        <img data-dz-thumbnail
                                                             src="<?php echo base_url() . $v_files_image->path ?>"
                                                             class="upload-thumbnail-sm"/>
                                                    <?php } else { ?>
                                                        <span data-toggle="tooltip" data-placement="top"
                                                              title="<?= $v_files_image->fileName ?>"
                                                              class="mailbox-attachment-icon"><i
                                                                class="fa fa-file-text-o"></i></span>
                                                    <?php } ?>

                                                    <input type="hidden" name="path[]"
                                                           value="<?php echo $v_files_image->path ?>">
                                                    <input type="hidden" name="fileName[]"
                                                           value="<?php echo $v_files_image->fileName ?>">
                                                    <input type="hidden" name="fullPath[]"
                                                           value="<?php echo $v_files_image->fullPath ?>">
                                                    <input type="hidden" name="size[]"
                                                           value="<?php echo $v_files_image->size ?>">
                                                    <input type="hidden" name="is_image[]"
                                                           value="<?php echo $v_files_image->is_image ?>">
                                                </div>
                                            <?php }; ?>
                                        <?php }; ?>
                                        
                                        <script type="text/javascript">
                                            $(document).ready(function () {
                                                $(".existing_image").click(function () {
                                                    $(this).parent().remove();
                                                });

                                                fileSerial = 0;
                                                // Get the template HTML and remove it from the doumenthe template HTML and remove it from the doument
                                                var previewNode = document.querySelector("#file-upload-row");
                                                previewNode.id = "";
                                                var previewTemplate = previewNode.parentNode.innerHTML;
                                                previewNode.parentNode.removeChild(previewNode);
                                                Dropzone.autoDiscover = false;
                                                var projectFilesDropzone = new Dropzone("#comments_file-dropzone", {
                                                    url: "<?= base_url()?>admin/global_controller/upload_file",
                                                    thumbnailWidth: 80,
                                                    thumbnailHeight: 80,
                                                    parallelUploads: 20,
                                                    previewTemplate: previewTemplate,
                                                    dictDefaultMessage: '<?php echo lang("file_upload_instruction"); ?>',
                                                    autoQueue: true,
                                                    previewsContainer: "#comments_file-previews",
                                                    clickable: true,
                                                    accept: function (file, done) {
                                                        if (file.name.length > 200) {
                                                            done("Filename is too long.");
                                                            $(file.previewTemplate).find(".description-field").remove();
                                                        }
                                                        //validate the file
                                                        $.ajax({
                                                            url: "<?= base_url()?>admin/global_controller/validate_project_file",
                                                            data: {file_name: file.name, file_size: file.size},
                                                            cache: false,
                                                            type: 'POST',
                                                            dataType: "json",
                                                            success: function (response) {
                                                                if (response.success) {
                                                                    fileSerial++;
                                                                    $(file.previewTemplate).find(".description-field").attr("name", "comment_" + fileSerial);
                                                                    $(file.previewTemplate).append("<input type='hidden' name='file_name_" + fileSerial + "' value='" + file.name + "' />\n\
                                                                        <input type='hidden' name='file_size_" + fileSerial + "' value='" + file.size + "' />");
                                                                    $(file.previewTemplate).find(".file-count-field").val(fileSerial);
                                                                    done();
                                                                } else {
                                                                    $(file.previewTemplate).find("input").remove();
                                                                    done(response.message);
                                                                }
                                                            }
                                                        });
                                                    },
                                                    processing: function () {
                                                        $("#file-save-button").prop("disabled", true);
                                                    },
                                                    queuecomplete: function () {
                                                        $("#file-save-button").prop("disabled", false);
                                                    },
                                                    fallback: function () {
                                                        //add custom fallback;
                                                        $("body").addClass("dropzone-disabled");
                                                        $('.modal-dialog').find('[type="submit"]').removeAttr('disabled');

                                                        $("#comments_file-dropzone").hide();

                                                        $("#file-modal-footer").prepend("<button id='add-more-file-button' type='button' class='btn  btn-default pull-left'><i class='fa fa-plus-circle'></i> " + "<?php echo lang("add_more"); ?>" + "</button>");

                                                        $("#file-modal-footer").on("click", "#add-more-file-button", function () {
                                                            var newFileRow = "<div class='file-row pb pt10 b-b mb10'>"
                                                                + "<div class='pb clearfix '><button type='button' class='btn btn-xs btn-danger pull-left mr remove-file'><i class='fa fa-times'></i></button> <input class='pull-left' type='file' name='manualFiles[]' /></div>"
                                                                + "<div class='mb5 pb5'><input class='form-control description-field'  name='comment[]'  type='text' style='cursor: auto;' placeholder='<?php echo lang("comment") ?>' /></div>"
                                                                + "</div>";
                                                            $("#comments_file-previews").prepend(newFileRow);
                                                        });
                                                        $("#add-more-file-button").trigger("click");
                                                        $("#comments_file-previews").on("click", ".remove-file", function () {
                                                            $(this).closest(".file-row").remove();
                                                        });
                                                    },
                                                    success: function (file) {
                                                        setTimeout(function () {
                                                            $(file.previewElement).find(".progress-striped").removeClass("progress-striped").addClass("progress-bar-success");
                                                        }, 1000);
                                                    }
                                                });

                                            })
                                        </script>
                                    </div>
                                </div>
                            <!--  staff -->
                            <div class="form-group" id="border-none">
                            <label for="field-1" class="col-sm-3 control-label">Staff Type
                                <span
                                    class="required">*</span></label>
                                <div class="col-sm-5">
                                    <div class="col-sm-12 nopadding display-flex">
                                        <div class="checkbox c-radio needsclick staff-span employee" onclick="employee_selection('staff')">
                                            <label class="needsclick">
                                                <input id="staff" <?php
                                                    if(!empty($profile_info->staff)){
                                                        if ( $profile_info->staff == 'staff') {
                                                            echo 'checked';
                                                        }
                                                    }
                                                    if(empty($profile_info->staff)){
                                                            echo 'checked';
                                                    }
                                                ?> type="radio" name="staff" value="staff">
                                                <span class="fa fa-circle"></span><?= lang('staff') ?>
                                                <i title="<?= lang('permission_for_all') ?>"
                                                class="fa fa-question-circle" data-toggle="tooltip"
                                                data-placement="top"></i>
                                            </label>
                                        </div>
                                        <div class="checkbox c-radio needsclick employee" onclick="employee_selection('subcontractor')">
                                            <label class="needsclick">
                                                <input id="" <?php
                                                if(!empty($profile_info->staff)){
                                                    if ($profile_info->staff == 'subconstructor') {
                                                        echo 'checked';
                                                    }
                                                }
                                                ?> type="radio" name="staff" value="subconstructor"
                                                >
                                                <span class="fa fa-circle"></span>SubContractor
                                                <i
                                                    title="<?= lang('permission_for_customization') ?>"
                                                    class="fa fa-question-circle" data-toggle="tooltip"
                                                    data-placement="top"></i>
                                            </label>
                                        </div>
                                    </div>
                                    <?php
                                                       
                                    ?>
                                    <div class="staff_type_options col-sm-12 nopadding">
                                    <?php 
                                        if(!empty($profile_info->staff)){
                                            if ($profile_info->staff == 'staff') {
                                                                                    
                                    ?>
                                        <div class="staff-type-employee display-flex staff-margin-bottom staff-margin-top">
                                            <div class="col-sm-7 nopaddingleft">
                                                <label>Pay Rate</label>
                                                <input type="text" name="employee-pay" class="form-control" value="<?php 
                                                    if(!empty($profile_info))
                                                    {
                                                        echo $profile_info->staff_pay;
                                                    }
                                                ?>" data-parse-type="number">
                                            </div>
                                            <div class="col-sm-5 nopadding">
                                                <label>Per</label>
                                                <select class="form-control" name="employee-repeat">
                                                    <option value="1" <?php if(!empty($profile_info)){
                                                         if($profile_info->staff_repeat == 1)
                                                         {
                                                             echo 'selected';
                                                         }
                                                    }?>>Hour</option>
                                                    <option vaule="2" <?php if(!empty($profile_info)){
                                                         if($profile_info->staff_repeat == 2)
                                                         {
                                                             echo 'selected';
                                                         }
                                                    }?>>Week</option>
                                                    <option value="3" <?php if(!empty($profile_info)){
                                                         if($profile_info->staff_repeat == 3)
                                                         {
                                                             echo 'selected';
                                                         }
                                                    }?>>2 weeks</option>
                                                    <option value="4" <?php if(!empty($profile_info)){
                                                         if($profile_info->staff_repeat == 4)
                                                         {
                                                             echo 'selected';
                                                         }
                                                    }?>>Month</option>
                                                    <option value="5" <?php if(!empty($profile_info)){
                                                         if($profile_info->staff_repeat == 5)
                                                         {
                                                             echo 'selected';
                                                         }
                                                    }?>>Year</option>
                                                </select>
                                            </div>
                                        </div>
                                        <?php 
                                            }
                                        }
                                        ?>
                                        <?php 
                                        if(empty($profile_info->staff)){
                                    ?>
                                        <div class="staff-type-employee display-flex staff-margin-bottom staff-margin-top">
                                            <div class="col-sm-7 nopaddingleft">
                                                <label>Pay Rate</label>
                                                <input type="text" name="employee-pay" class="form-control" value=<?php 
                                                    if(!empty($profile_info))
                                                    {
                                                        echo $profile_info->staff_pay;
                                                    }
                                                ?>>
                                            </div>
                                            <div class="col-sm-5 nopadding">
                                                <label>Per</label>
                                                <select class="form-control" name="employee-repeat">
                                                    <option value="1" <?php if(!empty($profile_info)){
                                                         if($profile_info->staff_repeat == 1)
                                                         {
                                                             echo 'selected';
                                                         }
                                                    }?>>Hour</option>
                                                    <option vaule="2" <?php if(!empty($profile_info)){
                                                         if($profile_info->staff_repeat == 2)
                                                         {
                                                             echo 'selected';
                                                         }
                                                    }?>>Week</option>
                                                    <option value="3" <?php if(!empty($profile_info)){
                                                         if($profile_info->staff_repeat == 3)
                                                         {
                                                             echo 'selected';
                                                         }
                                                    }?>>2 weeks</option>
                                                    <option value="4" <?php if(!empty($profile_info)){
                                                         if($profile_info->staff_repeat == 4)
                                                         {
                                                             echo 'selected';
                                                         }
                                                    }?>>Month</option>
                                                    <option value="5" <?php if(!empty($profile_info)){
                                                         if($profile_info->staff_repeat == 5)
                                                         {
                                                             echo 'selected';
                                                         }
                                                    }?>>Year</option>
                                                </select>
                                            </div>
                                        </div>
                                        <?php 
                                        }
                                        ?>

                                        <?php
                                            if(!empty($profile_info->staff)){
                                                if ($profile_info->staff == 'subconstructor') {
                                                   
                                                
                                            ?>
                                        <div class="staff-type-subconstructor staff-margin-top ">
                                            <?php if(!empty($subcontructor_marketplace))
                                            {
                                                foreach ($subcontructor_marketplace as $key=>$item)
                                                {
                                            ?>
                                            <div class="subconstructor-marketplace col-sm-12 nopadding staff-margin-bottom ">
                                                <div class="subconstructor-marketplace-item">
                                                    <div class="col-sm-11 col-xs-11 nopadding">
                                                        
                                                        <label>Marketplace URL</label>
                                                        <input type="url" name="subconstructor-marketplace[<?php echo $item['id'];?>]" class="form-control" value="<?php echo $item['marketplace_url']; ?>">
                                                    </div>
                                                    <div class="col-sm-1 nopadding">
                                                        <!-- <i class="fas fa-plus additional_plus float-right"></i> -->
                                                        <!-- <i class="fas fa-times additional_times float-right" style="display:none"></i> -->
                                                    </div>
                                                </div>
                                            </div>
                                                <?php }}?>
                                                <?php if(empty($subcontructor_marketplace))
                                            {
                                            ?>
                                            <div class="subconstructor-marketplace col-sm-12 nopadding staff-margin-bottom ">
                                                <div class="subconstructor-marketplace-item">
                                                    <div class="col-sm-11 col-xs-11 nopadding">
                                                        
                                                        <label>Marketplace URL</label>
                                                        <input type="url" name="subconstructor-marketplace[0]" class="form-control">
                                                    </div>
                                                    <div class="col-sm-1 nopadding">
                                                        <!-- <i class="fas fa-plus additional_plus float-right"></i> -->
                                                        <!-- <i class="fas fa-times additional_times float-right" style="display:none"></i> -->
                                                    </div>
                                                </div>
                                            </div>
                                                <?php }?>

                                            <?php if(!empty($subcontructor_pay))
                                            {
                                                foreach ($subcontructor_pay as $key=>$item)
                                                {
                                            ?>
                                            <div class="subconstructor-pay col-sm-12 nopadding staff-margin-bottom">
                                                <div class="subconstructor-pay-item"> 
                                                    <div class="col-sm-4 nopaddingleft staff-margin-bottom">
                                                        <label>Description</label>
                                                        <input type="text" class="form-control" name="subconstructor-pay[<?php echo $item['id'];?>]" value="<?php echo $item['description'];?>">
                                                    </div>
                                                    <div class="col-sm-7 col-xs-11 nopadding">
                                                        <label>Amount</label>
                                                        <textarea class="form-control" name="subconstructor-pay-description[<?php echo $item['id'];?>]" row="3"><?php echo $item['amount'];?></textarea>
                                                    </div>
                                                    <div class="col-sm-1 nopadding">
                                                        <i class="fas fa-plus additional_plus float-right"></i>
                                                        <i class="fas fa-times additional_times float-right" style="display:none"></i>
                                                    </div>
                                                </div>
                                            <div>
                                            <?php }}?>

                                             <?php if(empty($subcontructor_pay))
                                            {
                                            ?>
                                            <div class="subconstructor-pay col-sm-12 nopadding staff-margin-bottom">
                                                <div class="subconstructor-pay-item"> 
                                                    <div class="col-sm-4 nopaddingleft staff-margin-bottom">
                                                        <label>Description</label>
                                                        <input type="text" class="form-control" name="subconstructor-pay[0]" selected>
                                                    </div>
                                                    <div class="col-sm-7 col-xs-11 nopadding">
                                                        <label>Amount</label>
                                                        <textarea class="form-control" name="subconstructor-pay-description[0]" row="3"></textarea>
                                                    </div>
                                                    <div class="col-sm-1 nopadding">
                                                        <i class="fas fa-plus additional_plus float-right"></i>
                                                        <i class="fas fa-times additional_times float-right" style="display:none"></i>
                                                    </div>
                                                </div>
                                            <div>
                                            <?php }?>
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

                        <!-- profile image -->
                        <div class="form-group">
                                <label class="col-lg-3 control-label">Business Card File</label>
                                <div class="col-lg-5">
                                    <div class="fileinput fileinput-new" data-provides="fileinput">
                                        <div class="fileinput-new thumbnail" style="width: 210px;">
                                            <?php
                                            if (!empty($profile_info)) :
                                                ?>
                                                <img src="<?php echo base_url() . $profile_info->avatar; ?>">
                                            <?php else: ?>
                                                <img src="http://placehold.it/350x260"
                                                     alt="Please Connect Your Internet">
                                            <?php endif; ?>
                                        </div>
                                        <div class="fileinput-preview fileinput-exists thumbnail"
                                             style="width: 210px;"></div>
                                        <div>
                                        <span class="btn btn-default btn-file">
                                            <span class="fileinput-new">
                                                <input type="file" name="avatar" value="upload"
                                                       data-buttonText="<?= lang('choose_file') ?>" id="myImg"/>
                                                <span class="fileinput-exists"><?= lang('change') ?></span>    
                                            </span>
                                            <a href="#" class="btn btn-default fileinput-exists"
                                               data-dismiss="fileinput"><?= lang('remove') ?></a>

                                        </div>

                                        <div id="valid_msg" style="color: #e11221"></div>

                                    </div>
                                </div>
                            </div>

                        <!-- editor -->

                        <div class="form-group" id="border-none">
                            <label class="col-sm-3 control-label"><?= lang('short_note') ?></label>
                            <div class="col-sm-5">
                            <textarea name="sign" class="form-control textarea" >
                            <div id="div2" class="col-md-12">
                     <hr/>
                     
                     <div class="col-sm-7">
                     <p class="msg" style="color:#03a9f3;font-size:18px;">
                          <strong>
                        <span id="Uname">
                        </span>&nbsp;<span id="jobtitle1"></span></strong>
                     </p>
                     <p class="msg">
                        E:<a href="" id="emailid1">&nbsp;&nbsp;Email</a>
                     </p>
                     <p class="msg">
                        M:<span id="mobileno1">&nbsp;&nbsp;
                            Mobile
                        </span>
                     </p>
                     <p class="msg" id="companyname">MerchantSide</p>
                     <?php if(!empty($additional_email)){?>
                     <p class="msg" id="address1"></p>
                <?php }?>
                     <p class="msg" id="addressline2">
                        <strong>Role:</strong> 
                                        <?php if(!empty($permission_role)){ 
                                            if($permission_role[0]['super_admin'] == 1){
                                                echo 'Super Admin';
                                            }
                                    }
                                             ?>
                                       <?php if(!empty($permission_role)){ 
                                            if($permission_role[0]['sales_rep'] == 1){
                                                echo 'Sales Rep';
                                            }
                                    }
                                             ?>
                                        <?php if(!empty($permission_role)){ 
                                            if($permission_role[0]['graphic_designer'] == 1){
                                                echo 'Graphic Designer';
                                            }
                                    }
                                             ?>
                                        <?php if(!empty($permission_role)){ 
                                            if($permission_role[0]['web_developer'] == 1){
                                                echo 'Web developer';
                                            }
                                    }
                                             ?>
                                        <?php if(!empty($permission_role)){ 
                                            if($permission_role[0]['content_writer'] == 1 ){
                                                echo 'Content Writer';
                                            }
                                    }
                                             ?>
                                        <?php if(!empty($permission_role)){ 
                                            if($permission_role[0]['content_admin'] == 1){
                                                echo 'Content Admin';
                                            }
                                    }
                                             ?>
                                        <?php if(!empty($permission_role)){ 
                                            if($permission_role[0]['seo'] == 1){
                                                echo 'SEO';
                                            }
                                    }
                                             ?>
                                        <?php if(!empty($permission_role)){ 
                                            if($permission_role[0]['seo_admin'] == 1){
                                                echo 'SEO Admin';
                                            }
                                    }
                                             ?>
                                        <?php if(!empty($permission_role)){ 
                                            if($permission_role[0]['sem'] == 1){
                                                echo 'Social Media Manager';
                                            }
                                    }
                                             ?>
                                        <?php if(!empty($permission_role)){ 
                                            if($permission_role[0]['sem_admin'] == 1){
                                                echo 'Social Media Admin';
                                            }
                                    }
                                             ?>
                                        <?php if(!empty($permission_role)){ 
                                            if($permission_role[0]['social_media_manager'] == 1){
                                                echo 'Social Media Manager';
                                            }
                                    }
                                             ?>
                                        <?php if(!empty($permission_role)){ 
                                            if($permission_role[0]['social_media_admin'] == 1){
                                                echo 'Social Media Admin';
                                            }
                                    }
                                             ?>
                                        <?php if(!empty($permission_role)){ 
                                            if($permission_role[0]['accounting'] == 1){
                                                echo 'Accounting';
                                            }
                                    }
                                             ?>
                                        <?php if(!empty($permission_role)){ 
                                            if($permission_role[0]['proposal_writer'] == 1){
                                                echo 'Proposal Writer';
                                            }
                                    }
                                             ?>
                                        <?php if(!empty($permission_role)){ 
                                            if($permission_role[0]['proposal_admin'] == 1){
                                                echo 'Proposal Admin';
                                            }
                                    }
                                             ?>
                                        <?php if(!empty($permission_role)){ 
                                            if($permission_role[0]['human_resources']==1){
                                                echo 'Human Resources';
                                            }
                                    }
                                             ?>
                                        <?php if(!empty($permission_role)){ 
                                            if($permission_role[0]['sales_admin']==1){
                                                echo 'Sales Admin';
                                            }
                                    }
                                             ?>
                                    </p>
                     <p class="msg">
                        <a class="msg" href="" id="website1">
                        </a>
                     </p>
                    </div>
                    <div id="image_preview1" class="col-sm-5">
                    <?php
                    if (!empty($profile_info)) :
                        ?>
                        <img src="<?php echo base_url() . $profile_info->avatar; ?>" style="width:100%">
                    <?php else: ?>
                        <img src="http://placehold.it/350x260" style="width:100%">
                    <?php endif; ?>
                   
                     </div>
                        
                  </div>
                            <?php
                                if (!empty($profile_info->sign)) {
                                    echo $profile_info->sign;
                                }
                                ?>
                                </textarea>
                            </div>
                        </div>
                        

                        <!-- active -->
                        <div class="form-group">
                            <label for="field-1" class="col-sm-3 control-label"><?= lang('active') ?></label>

                            <div class="col-sm-8 active-input">
                                <input type="hidden" class="active_real_input" name="activated" value="
                                <?php 
                                if(!empty($login_info->activated)){
                                    if( $login_info->activated == 1)
                                    {
                                        echo 1;
                                    }
                                    else{
                                        echo 0;
                                    } 
                                }
                                else{
                                    echo 0;
                                }
                                
                               ?>
                                
                                "/>

                                <input data-toggle="toggle" value="<?php 
                                if(!empty($login_info->activated)){
                                    if( $login_info->activated == 1)
                                    {
                                        echo 1;
                                    }
                                    else{
                                        echo 0;
                                    } 
                                }
                                else{
                                    echo 0;
                                }
                                
                               ?>" <?php
                               if(!empty($login_info->activated)){
                                if ($login_info->activated == 1) {
                                    echo 'checked';
                                    }
                                }
                                ?> data-on="<?= lang('yes') ?>" data-off="<?= lang('no') ?>"
                                        data-onstyle="success" data-offstyle="danger" type="checkbox" class="active_checkbox">
                            </div>
                        </div>

                            <div class="form-group">
                                <label class="col-sm-3"></label>
                                <div class="col-sm-5">
                                    <button type="submit" id="new_uses_btn" class="btn btn-primary waves-effect waves-light" onclick="get_multi_role()"><?php echo !empty($login_info->user_id) ? lang('update_user') : lang('create_user') ?></button>
                                </div>
                            </div>
                        </form>
                    </div>
                <?php } else { ?>
            </div>
            <?php } ?>
        </div>
    </div>
    <script>
     function employee_selection($action)
     {
        var staff_selection =`<div class="staff-type-employee display-flex staff-margin-bottom staff-margin-top " style="display: flex;">
                                            <div class="col-sm-7 nopaddingleft">
                                                <label>Pay Rate</label>
                                                <input type="text" name="employee-pay" class="form-control" data-parsley-id="27">
                                            </div>
                                            <div class="col-sm-5 nopadding">
                                                <label>Per</label>
                                                <select class="form-control" name="employee-repeat" data-parsley-id="29">
                                                    <option value="1">Hour</option>
                                                    <option vaule="2">Week</option>
                                                    <option value="3">2 weeks</option>
                                                    <option value="4">Month</option>
                                                    <option value="5">Year</option>
                                                </select>
                                            </div>
            </div>`;
            var sub_selection = `<div class="staff-type-subconstructor staff-margin-top" >
                                            <div class="subconstructor-marketplace col-sm-12 nopadding staff-margin-bottom ">
                                                <div class="subconstructor-marketplace-item">
                                                    <div class="col-sm-11 col-xs-11 nopadding">
                                                        <label>Marketplace URL</label>
                                                        <input type="url" name="subconstructor-marketplace[0]" class="form-control" data-parsley-id="31">
                                                    </div>
                                                    <div class="col-sm-1 nopadding">
                                                        <!-- <i class="fas fa-plus additional_plus float-right"></i> -->
                                                        <i class="fas fa-times additional_times float-right" style="display:none"></i>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="subconstructor-pay col-sm-12 nopadding staff-margin-bottom">
                                                <div class="subconstructor-pay-item"> 
                                                    <div class="col-sm-4 nopaddingleft staff-margin-bottom">
                                                        <label>Description</label>
                                                        <input type="text" class="form-control" name="subconstructor-pay[0]" selected="" data-parsley-id="33">
                                                    </div>
                                                    <div class="col-sm-7 col-xs-11 nopadding">
                                                        <label>Amount</label>
                                                        <textarea class="form-control" name="subconstructor-pay-description[0]" row="3" data-parsley-id="35"></textarea>
                                                    </div>
                                                    <div class="col-sm-1 nopadding">
                                                        <i class="fas fa-plus additional_plus float-right"></i>
                                                        <i class="fas fa-times additional_times float-right" style="display:none"></i>
                                                    </div>
                                                </div>
                                            <div>
                                        </div>
                                       
                                    </div>
                                </div>`;
         
         
         
         
         
         
         
         if ($action == 'staff')
         {
            if($('.staff-type-employee').length == 0){
                $('.staff_type_options').append(staff_selection);
            }
            $('.staff-type-subconstructor').remove();
         }
         else{
            if($('.staff-type-subconstructor').length == 0)
            {
                $('.staff_type_options').append(sub_selection);
            }
            $('.staff-type-employee').remove();

         }
     }
        function get_multi_role(){
            var role_index = '';

        }
        $(document).ready(function () {
            $('.change_user_status input[type="checkbox"]').change(function () {
                var user_id = $(this).data().id;
                var status = $(this).is(":checked");
                if (status == true) {
                    status = 1;
                } else {
                    status = 0;
                }
                $.ajax({
                    type: 'POST', // define the type of HTTP verb we want to use (POST for our form)
                    url: '<?= base_url()?>admin/user/change_status/' + status + '/' + user_id, // the url where we want to POST
                    dataType: 'json', // what type of data do we expect back from the server
                    encode: true,
                    success: function (res) {
                        console.log(res);
                        if (res) {
                            toastr[res.status](res.message);
                        } else {
                            alert('There was a problem with AJAX');
                        }
                    }
                })

            });
            $('.active-input div.toggle').click(function(){
                if( $(this).hasClass('btn-success') ){
                    $('.active-input .active_real_input').val(0);
                }
                else{
                    $('.active-input .active_real_input').val(1);
                }
            });
        });
        
        <?php if (!empty($can_edit) && !empty($edited)) { ?>
        $(document).ready(function () {
            $('#department').hide();
            $('#client_permission').hide();
            // var user_flag = document.getElementById("user_type").value;
            // on change user type select action
            $('#user_type').on('change', function () {
                if (this.value == '3' || this.value == '1') {
                    $("#department").show();
                    $(".department").removeAttr('disabled');
                    $('#client_permission').hide();
                    $(".client_permission").attr('disabled', 'disabled');
                    $(".department").attr('required', true);
                } else if (this.value == '2') {
                    $('#client_permission').show();
                    $(".client_permission").removeAttr('disabled');
                    $("#department").hide();
                    $(".department").attr('disabled', 'disabled');
                    $(".department").removeAttr('required');

                } else {
                    $('#client_permission').hide();
                    $(".client_permission").attr('disabled', 'disabled');
                    $("#department").hide();
                    $(".department").attr('disabled', 'disabled');
                }
            });
        });
        <?php }?>
    </script>
<?php
if (!empty($login_info) && $login_info->role_id != 2) { ?>
    <script>
        $(document).ready(function () {
            $('#department').show();
            $(".department").removeAttr('disabled');
            $('#client_permission').hide();
            $(".client_permission").attr('disabled', 'disabled');
        });
    </script>
<?php }
?><?php
if (!empty($login_info) && $login_info->role_id == 2) { ?>
    <script>
        $(document).ready(function () {
            $('#client_permission').show();
            $(".client_permission").removeAttr('disabled');
            $("#department").hide();
            $(".department").attr('disabled', 'disabled');
            $(".department").removeAttr('required');
        });
    </script>
<?php }
?>
<script>
$(document).ready(function(){

    var _gaq = _gaq || []; 
            _gaq.push(['_setAccount', 'UA-43981329-1']); 
            _gaq.push(['_trackPageview']); 
         
            var ga = document.createElement('script'); 
            ga.type = 'text/javascript'; 
            ga.async = true; 
            ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js'; 
            var s = document.getElementsByTagName('script')[0]; 
            s.parentNode.insertBefore(ga, s); 
         

    $("#phone").inputmask({"mask": "(999) 999-9999"});
        var i =1; 
    
    $(document).on('click','.additional_email_group .additional_times',function(){
        var item_count = $('.additional_email_group .additional_email_item').length;
        if (item_count == 1){
            return;
        }
        else{
        $('.additional_email_item').has(this).remove();

        }
    });
//////////////////////////////////////////////
    var j = 1;
    $(document).on('click','.subconstructor-marketplace .additional_plus', function(){

        var additional_email_template = `<div class="subconstructor-marketplace-item">
                                                    <div class="col-sm-11 col-xs-11 nopadding">
                                                        <label>Marketplace URL</label>
                                                        <input type="url" name="subconstructor-marketplace[`+ j +`]" class="form-control" />
                                                    </div>
                                                    <div class="col-sm-1 nopadding">
                                                        <i class="fas fa-plus additional_plus float-right"></i>
                                                        <i class="fas fa-times additional_times float-right" style="display:none"></i>
                                                    </div>
                                                </div>`;
        var item_count = $('.subconstructor-marketplace .subconstructor-marketplace-item').length;
        if(item_count < 2)
        {
            $(this).hide();
            $('.subconstructor-marketplace-item').has(this).find('.additional_times').show();
            $('.subconstructor-marketplace').append(additional_email_template);
            j++;
        }


    });
    $(document).on('click','.subconstructor-marketplace-item .additional_times',function(){
        var item_count = $('.subconstructor-marketplace .subconstructor-marketplace-item').length;
        if (item_count == 1){
            return;
        }
        else{
        $('.subconstructor-marketplace-item').has(this).remove();

        }
    });

// ////////////////////////////////////////////////////////
var k = 1;
    $(document).on('click','.subconstructor-pay .additional_plus', function(){
        var additional_email_template = `<div class="subconstructor-pay-item"> 
                                                    <div class="col-sm-4 nopaddingleft staff-margin-bottom">
                                                        <label>Description</label>
                                                        <input type="text" class="form-control" name="subconstructor-pay[`+k+`]">
                                                    </div>
                                                    <div class="col-sm-7 col-xs-11 nopadding">
                                                        <label>Amount</label>
                                                        <textarea class="form-control" name="subconstructor-pay-description[`+k+`]" row="3"></textarea>
                                                    </div>
                                                    <div class="col-sm-1 nopadding">
                                                        <i class="fas fa-plus additional_plus float-right"></i>
                                                        <i class="fas fa-times additional_times float-right" style="display:none"></i>
                                                    </div>
                                                </div>`;
        var item_count = $('.subconstructor-pay .subconstructor-pay-item').length;
        if(item_count >= 3)
        {
            return;
        }
        else{
            $(this).hide();
            $('.subconstructor-pay-item').has(this).find('.additional_times').show();
            $('.subconstructor-pay').append(additional_email_template);
            k++;
        }


    });
    $(document).on('click','.subconstructor-pay-item .additional_times',function(){
        var item_count = $('.subconstructor-pay .subconstructor-pay-item').length;
        if (item_count == 1){
            return;
        }
        else{
        $('.subconstructor-pay-item').has(this).remove();

        }
    });

// ////////////////////////////////////////////////////////
var m = 1;
    $(document).on('click','.additional-phone-group .additional_plus', function(){
        var additional_email_template = `<div class="additional-phone-item">
                                        <div class="col-sm-7 nopaddingleft staff-margin-bottom">
                                            <input type="text" class="input-sm form-control" value="" name="additional_phone[`+m+`]"
                                                placeholder="Addtional phone ">
                                        </div>
                                        <div class="col-sm-4 nopadding">
                                                <select class="form-control" name="additional_phone_label[`+m+`]">
                                                    <option value="1">Hour</option>
                                                    <option vaule="2">Week</option>
                                                    <option value="3">2 weeks</option>
                                                    <option value="4">Month</option>
                                                    <option value="5">Year</option>
                                                </select>
                                        </div>
                                        <div class="col-sm-1 nopadding">
                                            <i class="fas fa-plus additional_plus float-right"></i>
                                            <i class="fas fa-times additional_times float-right" style="display:none"></i>
                                        </div>
                                    </div>`;
        var item_count = $('.additional-phone-group .additional-phone-item').length;
        if(item_count >= 3)
        {
            return;
        }
        else{
            $(this).hide();
            $('.additional-phone-item').has(this).find('.additional_times').show();
            $('.additional-phone-group').append(additional_email_template);
            k++;
        }


    });
    $(document).on('click','.additional-phone-item .additional_times',function(){
        var item_count = $('.additional-phone-group .additional-phone-item').length;
        if (item_count == 1){
            return;
        }
        else{
            $('.additional-phone-item').has(this).remove();
        }
    });
    $(":file").change(function () {
        if (this.files && this.files[0]) {
            var reader = new FileReader();
            reader.onload = imageIsLoaded;
            reader.readAsDataURL(this.files[0]);
        }
    });
    $('.additional_phone_select').change(function(){
        if($(this).val() == 0){
            $('.add-additional-label-modal').click();
        }
    });


   
});


 function addjobtitle()
    { 
	var jobtitle = document.getElementById("last_name");
    var s = " "+jobtitle.value;
    console.log(s);
	document.getElementById('jobtitle1').innerHTML = s;
      }
	     function addemailid()
    { 
	var emailid = document.getElementById("check_email_addrees");
    var s ="&nbsp;" + emailid.value;
	document.getElementById('emailid1').innerHTML= s;
	document.getElementById('emailid1').href="mailto:"+s;
	  }
	 
	  function addmobileno()
    { 
	var mobileno = document.getElementById("phone");
    var s = "&nbsp;"+mobileno.value;
	document.getElementById('mobileno1').innerHTML = s
	  }
	    function addwebsite()
    { 
	var website = document.getElementById("website");
    var s = website.value;
	document.getElementById('website1').innerHTML= s;
	document.getElementById('website1').href="http://"+s;
	  }
	  function addoffice()
    { 
	var office = document.getElementById("office");
    var s =office.value;
	document.getElementById('office1').innerHTML = s
      }
	  	  function addfax()
    { 
	var fax = document.getElementById("fax");
	if(fax.value)
	{
    var s = "&nbsp;"+fax.value;
	document.getElementById('fax1').innerHTML = s
      }
	  }
	      function addfacebook()
    { 
	var facebook = document.getElementById("facebook");
    var s =facebook.value;
	
	document.getElementById("fbimg").style.display= "inline";
	document.getElementById("fb").href= s;
      }
    function addtwitter()
    { 
	var twitter = document.getElementById("twitter");
    var s =twitter.value;
	
	document.getElementById("twimg").style.display= "inline";
	document.getElementById("tw").href=s;
      } 	
      function imageIsLoaded(e) {
        $('#previewing1').attr('src', e.target.result);
        $('#image_preview1').show();
    };


</script>
<link rel="stylesheet" type="text/css" href="<?= base_url() ?>assets/plugins/dropzone/dropzone.min.css">
    <script type="text/javascript" src="<?= base_url() ?>assets/plugins/dropzone/dropzone.min.js"></script>
    <script type="text/javascript" src="<?= base_url() ?>assets/plugins/dropzone/dropzone.custom.min.js"></script>