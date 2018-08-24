
<div class="row">
    <div class="col-md-12">
        <form method="post" action="<?php echo base_url() ?>client/mailbox/delete_inbox_mail" >
            <!-- Main content -->
            <div class="panel panel-custom">
                <div class="panel-heading">
                    <div class="mailbox-controls">

                        <!-- Check all button -->
                        <div class="mail_checkbox mr-sm">
                            <input type="checkbox" id="parent_present">
                        </div>
                        <div class="btn-group">
                            <button class="btn btn-default btn-xs mr-sm"><i class="fa fa-trash-o"></i></button>
                        </div><!-- /.btn-group -->
                        <a href="#" onClick="history.go(0)" class="btn btn-default btn-xs mr-sm"><i
                                class="fa fa-refresh"></i></a>
                        <a href="<?php echo base_url() ?>client/mailbox/index/compose"
                           class="btn btn-danger btn-xs mr-sm">Compose
                            +</a>
                    </div>
                </div>

                <div class="panel-body">
                    <div class="table-responsive mailbox-messages">
                        <table class="table table-striped DataTables " id="DataTables">
                            <tbody>
                                <?php if (!empty($favourites_mail)):foreach ($favourites_mail as $v_favourites_msg): ?>
                                        <tr>                                                                                                                                
                                            <td><input class="child_present" type="checkbox" name="selected_inbox_id[]" value="<?php echo $v_favourites_msg->inbox_id; ?>"/></td>

                                            <td class="mailbox-star">                                                
                                                <a href="<?php echo base_url() ?>client/mailbox/index/added_favourites/<?php echo $v_favourites_msg->inbox_id ?>/0"><i class="fa fa-star text-yellow"></i></a>
                                            </td> 
                                            <td class="mailbox-name"><a href="<?php echo base_url() ?>client/mailbox/index/read_inbox_mail/<?php echo $v_favourites_msg->inbox_id ?>"><?php
                                                    $string = (strlen($v_favourites_msg->to) > 13) ? strip_tags(mb_substr($v_favourites_msg->to, 0, 13)) . '...' : $v_favourites_msg->to;
                                                    if ($v_favourites_msg->view_status == 1) {
                                                        echo '<span style="color:#000">' . $string . '</span>';
                                                    } else {
                                                        echo '<b style="color:#000;font-size:13px;">' . $string . '</b>';
                                                    }
                                                    ?></a></td>
                                            <td class="mailbox-subject" style="font-size:13px"><b class="pull-left"><?php
                                                    $subject = (strlen($v_favourites_msg->subject) > 20) ? strip_tags(mb_substr($v_favourites_msg->subject, 0, 20)) . '...' : $v_favourites_msg->subject;
                                                    echo $subject;
                                                    ?> - &nbsp;</b> <span class="pull-left "> <?php
                                                    $body = (strlen($v_favourites_msg->message_body) > 40) ? strip_tags(mb_substr($v_favourites_msg->message_body, 0, 40)) . '...' : $v_favourites_msg->message_body;
                                                    echo $body;
                                                    ?></span></td>
                                            <td style="font-size:13px">
                                                <?= time_ago($v_favourites_msg->message_time); ?>
                                            </td>
                                        </tr>                  
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td><strong>There is no email to display</strong></td>
                                    </tr> 
                                <?php endif; ?>
                            </tbody>
                        </table><!-- /.table -->
                    </div><!-- /.mail-box-messages -->
                </div><!-- /.box-body -->                        
            </div><!-- /. box -->                        
        </form>
    </div><!-- /.content-wrapper -->
</div><!-- /.content-wrapper -->
