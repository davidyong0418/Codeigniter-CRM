<div class="row">
    <div class="col-md-12">
        <form method="post" action="<?php echo base_url() ?>admin/mailbox/delete_mail/inbox">
            <!-- Main content -->
            <div class="panel panel-custom">
                <div class="card-body panel-heading">
                    <div class="mailbox-controls btn-group m-b-10 m-r-10">

                        <!-- Check all button -->
                        <div class="mail_checkbox btn btn-secondary font-18">
                            <input type="checkbox" id="parent_present">
                        </div>
                        <div class="btn-group">
                            <button class="btn btn-secondary font-18"><i class="mdi mdi-delete"></i></button>
                        </div><!-- /.btn-group -->
                        <a href="#" onClick="history.go(0)" class="btn btn-secondary font-18"><i class="mdi mdi-reload font-18"></i></a>
                        <a href="<?php echo base_url() ?>admin/mailbox/index/compose"
                           class="btn btn-danger font-18">Compose
                            +</a>
                    </div>
                </div>

                <div class="card-body p-t-0 panel-body">
                    <div class="mailbox-messages inbox-center table-responsive">
                        <table class="table" id="">
                            <tbody>
                            <?php if (!empty($get_inbox_message)):foreach ($get_inbox_message as $v_inbox_msg): ?>
                                <tr>
                                    <td><input class="child_present" type="checkbox" name="selected_id[]"
                                               value="<?php echo $v_inbox_msg->inbox_id; ?>"/></td>

                                    <td class="mailbox-star">
                                        <?php if ($v_inbox_msg->favourites == 1) { ?>
                                            <a href="<?php echo base_url() ?>admin/mailbox/index/added_favourites/<?php echo $v_inbox_msg->inbox_id ?>/0"><i
                                                    class="fa fa-star text-yellow"></i></a>
                                        <?php } else { ?>
                                            <a href="<?php echo base_url() ?>admin/mailbox/index/added_favourites/<?php echo $v_inbox_msg->inbox_id ?>/1"><i
                                                    class="fa fa-star-o text-yellow"></i></a>
                                        <?php } ?>
                                    </td>
                                    <td class="mailbox-name"><a
                                            href="<?php echo base_url() ?>admin/mailbox/index/read_inbox_mail/<?php echo $v_inbox_msg->inbox_id ?>"><?php
                                            $string = (strlen($v_inbox_msg->to) > 13) ?  strip_tags(mb_substr($v_inbox_msg->to, 0, 13)) . '...' : $v_inbox_msg->to;
                                            if ($v_inbox_msg->view_status == 1) {
                                                echo '<span style="color:#000">' . $string . '</span>';
                                            } else {
                                                echo '<b style="color:#000;font-size:13px;">' . $string . '</b>';
                                            }
                                            ?></a></td>
                                    <td class="mailbox-subject" style="font-size:13px"><b class="pull-left"><?php
                                            $subject = (strlen($v_inbox_msg->subject) > 20) ? strip_tags(mb_substr($v_inbox_msg->subject, 0, 20)) . '...' : $v_inbox_msg->subject;
                                            echo $subject;
                                            ?> - &nbsp;</b> <span class="pull-left "> <?php
                                            $body = (strlen($v_inbox_msg->message_body) > 40) ? strip_tags(mb_substr($v_inbox_msg->message_body, 0, 40)) . '...' : $v_inbox_msg->message_body;
                                            echo $body;
                                            ?></span></td>
                                    <td>
                                        <?= time_ago($v_inbox_msg->message_time); ?>
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
                </div>
            </div><!-- /.box-body -->
    </div><!-- /. box -->
    </form>
</div><!-- /.content-wrapper -->
</div><!-- /.content-wrapper -->
