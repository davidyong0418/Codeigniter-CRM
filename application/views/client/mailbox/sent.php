<div class="row">
    <div class="col-md-12">
        <form method="post" action="<?php echo base_url() ?>client/mailbox/delete_mail/sent">
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
                        <table class="table DataTables " >
                            <tbody style="font-size: 13px">
                            <?php if (!empty($get_sent_message)):foreach ($get_sent_message as $v_sent_msg): ?>
                                <tr>
                                    <td><input class="child_present" type="checkbox" name="selected_id[]"
                                               value="<?php echo $v_sent_msg->sent_id; ?>"/></td>
                                    <td>
                                        <a href="<?php echo base_url() ?>client/mailbox/index/read_send_mail/<?php echo $v_sent_msg->sent_id ?>"><?php
                                            $string = (strlen($v_sent_msg->to) > 13) ? strip_tags(mb_substr($v_sent_msg->to, 0, 13)) . '...' : $v_sent_msg->to;
                                            echo $string;
                                            ?></a></td>
                                    <td><b class="pull-left"> <?php
                                            $subject = (strlen($v_sent_msg->subject) > 20) ? strip_tags(mb_substr($v_sent_msg->subject, 0, 20)) . '...' : $v_sent_msg->subject;
                                            echo $subject;
                                            ?> -&nbsp; </b> <span class="pull-left "> <?php
                                            $body = (strlen($v_sent_msg->message_body) > 40) ? strip_tags(mb_substr($v_sent_msg->message_body, 0, 40)) . '...' : $v_sent_msg->message_body;
                                            echo $body;
                                            ?></span></td>
                                    <td style="font-size:13px">
                                        <?= time_ago($v_sent_msg->message_time); ?>
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
    </div><!-- /.col -->
</div><!-- /.row -->
