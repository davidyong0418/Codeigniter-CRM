<form role="form" id="form" enctype="multipart/form-data" action="<?php echo base_url() ?>client/mailbox/send_mail/<?php
if (!empty($get_draft_info->draft_id)) {
    echo $get_draft_info->draft_id;
}
?>" method="post" class="form-horizontal form-groups-bordered">
    <!-- Content Header (Page header) -->
    <div class="col-md-12">              
        <div class="box box-primary">                        
            <div class="box-body">
                <div class="form-group col-md-12">                                
                    <select multiple="multiple" required="" placeholder="To" name="to[]" style="width: 100%" class="select_2_to" >  
                        <option value=""></option>
                        <?php
                        if (!empty($get_user_info)): foreach ($get_user_info as $v_user_info) :
                                $user = $this->mailbox_model->check_by(array('user_id' => $v_user_info->user_id), 'tbl_account_details');
                                if (!empty($user)) {
                                    if ($v_user_info->role_id == 1) {
                                        $role = lang('admin');
                                    } elseif ($v_user_info->role_id == 3) {
                                        $role = lang('staff');
                                    } else {
                                        $role = lang('client');
                                    }
                                    ?>                                 
                                    <option value="<?php echo $v_user_info->email ?>"><?php echo $user->fullname . ' (<small>' . $role . '</small> )' ?></option>                                
                                    <?php
                                }
                            endforeach;
                            ?>
                        <?php endif; ?>
                        <?php
                        if (!empty($get_draft_info->to)) {
                            $saved_email = unserialize($get_draft_info->to);
                            foreach ($saved_email as $v_email) {
                                ?>
                                <option value="<?= $v_email ?>" selected=""><?= $v_email ?></option>
                                <?php
                            }
                        }if (!empty($inbox_info)) {
                            ?>
                            <option value="<?= $inbox_info->from ?>" selected=""><?= $inbox_info->from ?></option>
                        <?php }
                        ?>
                    </select>
                </div>
                <div class="form-group col-md-12">
                    <input class="form-control" value="<?php
                    if (!empty($get_draft_info->subject)) {
                        echo $get_draft_info->subject;
                    }
                    ?>" type="text" required="" name="subject" placeholder="Subject:"/>
                </div>
                <div class="form-group col-md-12">
                    <textarea class="form-control text-justify"  id="ck_editor" name="message_body" style="height: 350px"><?php
                        if (!empty($get_draft_info->message_body)) {
                            echo $get_draft_info->message_body;
                        }
                        ?></textarea>
                    <?php echo display_ckeditor($editor['ckeditor']); ?>
                </div>
                <div class="form-group col-md-12">                                
                    <div class="fileinput fileinput-new" data-provides="fileinput">
                        <input type="hidden" name="attach_file_path" value="<?php
                        if (!empty($get_draft_info->attach_file_path)) {
                            echo $get_draft_info->attach_file_path;
                        }
                        ?>">
                               <?php if (!empty($get_draft_info->attach_file)): ?>
                            <span class="btn btn-default btn-file"><span class="fileinput-new" style="display: none" ><?= lang('select_file') ?></span>
                                <span class="fileinput-exists" style="display: block">Change</span>
                                <input type="hidden" name="attach_file" value="<?php echo $get_draft_info->attach_file ?>" >
                                <input type="file" name="attach_file" >
                            </span>                                    
                            <span class="fileinput-filename"> <?php echo $get_draft_info->attach_filename ?></span>                                          
                        <?php else: ?>
                            <span class="btn btn-default btn-file"><span class="fileinput-new" ><i class="fa fa-paperclip"></i> <?= lang('attachment') ?></span>
                                <span class="fileinput-exists" ><?= lang('change') ?></span>                                            
                                <input type="file" name="attach_file" >
                            </span> 
                            <span class="fileinput-filename"></span>                                        
                            <a href="#" class="close fileinput-exists" data-dismiss="fileinput" style="float: none;">&times;</a>                                                                                                            
                        <?php endif; ?>
                        <p class="help-block">Max. 15 MB</p>
                    </div>    
                    <div id="msg_pdf" style="color: #e11221"></div>                                
                </div>
            </div><!-- /.box-body -->
            <div class="box-footer">
                <div class="pull-right">
                    <button  name="draf" value="1" class="btn btn-default"><i class="fa fa-pencil"></i> <?= lang('draft') ?></button>
                    <button type="submit" class="btn btn-primary"><i class="fa fa-envelope-o"></i> <?= lang('send') ?></button>
                </div>
                <button onclick="return confirm('<?= lang('discard_msg') ?>')" class="btn btn-default" name="discard" value="2" data-toggle="tooltip" data-placement="top" title="Close"><i class="fa fa-times"></i> Discard</button>                                
            </div>            
        </div><!-- /. box -->                
    </div><!-- /.col -->
</form>
<link href="<?php echo base_url() ?>asset/css/select2.css" rel="stylesheet"/>
<script src="<?php echo base_url() ?>asset/js/select2.js"></script>