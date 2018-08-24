<div class="row">
    <div class="col-md-12">
        <form method="post" action="<?php echo base_url() ?>admin/mailbox/delete_mail/draft">
            <div class="panel panel-custom">
                <div class="panel-heading card-body">
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
                        <table class="table table-striped DataTables " id="DataTables">
                            <tbody style="font-size: 13px">
                            <?php if (!empty($draft_message)):foreach ($draft_message as $v_draft_msg): ?>
                                <tr>
                                    <td><input class="child_present" type="checkbox" name="selected_id[]"
                                               value="<?php echo $v_draft_msg->draft_id; ?>"/></td>
                                    <td>
                                        <a href="<?php echo base_url() ?>admin/mailbox/index/compose/<?php echo $v_draft_msg->draft_id ?>"><?php
                                            $email_address = unserialize($v_draft_msg->to);
                                            $total_email = count($email_address);
                                            if ($total_email > 1) {
                                                $deduct = "$total_email" - 1;
                                                echo $email_address[0] . ' , (' . $deduct . ')';
                                            } else {
                                                echo $email_address[0];
                                            }
                                            ?></a></td>
                                    <td><b class="pull-left"> <?php
                                            $subject = (strlen($v_draft_msg->subject) > 20) ? strip_tags(mb_substr($v_draft_msg->subject, 0, 20)) . '...' : $v_draft_msg->subject;
                                            echo $subject;
                                            ?> -&nbsp; </b> <span class="pull-left "> <?php
                                            $body = (strlen($v_draft_msg->message_body) > 40) ? strip_tags(mb_substr($v_draft_msg->message_body, 0, 40)) . '...' : $v_draft_msg->message_body;
                                            echo $body;
                                            ?></span></td>
                                    <td>
                                        <?= time_ago($v_draft_msg->message_time); ?>
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
