<?php echo message_box('success') ?>

<div class="row" xmlns="http://www.w3.org/1999/html">
    <!-- Start Form -->
    <div class="col-lg-12">
        <form role="form" id="form" action="<?php echo base_url(); ?>admin/settings/save_email_integration"
              method="post"
              class="form-horizontal  ">
            <section class="panel panel-custom">
                <header class="panel-heading  "><?= lang('email_integration') ?></header>
                <div class="panel-body">

                    <?php
                    $trace_msg = $this->session->userdata('trace');
                    if (!empty($trace_msg)) {
                        ?>
                        <style type="text/css">
                            .panel-custom {
                                box-shadow: 3px 1px 5px 3px rgba(0, 0, 0, 0.4);
                            }
                        </style>
                        <div class="panel panel-custom copyright-wrap" id="copyright-wrap">
                            <div class="panel-heading">
                                <?= $this->session->userdata('header'); ?>

                                <button type="button" class="close" data-target="#copyright-wrap"
                                        data-dismiss="alert"><span aria-hidden="true">&times;</span><span
                                        class="sr-only">Close</span>

                                </button>
                            </div>
                            <div class="panel-body">
                                <?php
                                echo '<pre>';
                                print_r($trace_msg);

                                ?>
                            </div>
                        </div>
                    <?php }

                    $this->session->unset_userdata('trace');
                    $this->session->unset_userdata('header');
                    ?>
                    <?php echo validation_errors(); ?>
                    <input type="hidden" name="settings" value="<?= $load_setting ?>">

                    <div class="form-group">
                        <label class="col-lg-3 control-label"><?= lang('encryption') ?></label>
                        <div class="col-lg-9">
                            <label class="checkbox-inline c-checkbox">
                                <input class="select_one " type="checkbox" value="tls" name="encryption" <?php
                                if (config_item('encryption') == 'tls') {
                                    echo "checked=\"checked\"";
                                }
                                ?>>
                                <span class="fa fa-check"></span><?= lang('tls') ?>
                            </label>

                            <label class="checkbox-inline c-checkbox">
                                <input class="select_one " type="checkbox" value="ssl" name="encryption" <?php
                                if (config_item('encryption') == 'ssl') {
                                    echo "checked=\"checked\"";
                                }
                                ?>>
                                <span class="fa fa-check"></span><?= lang('ssl') ?>
                            </label>
                            <label class="checkbox-inline c-checkbox">
                                <input class="select_one " type="checkbox" name="encryption" <?php
                                if (config_item('encryption') == null) {
                                    echo "checked=\"checked\"";
                                }
                                ?>>
                                <span class="fa fa-check"></span><?= lang('no') . ' ' . lang('encryption') ?>
                            </label>

                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-3 control-label"><?= lang('host') ?></label>
                        <div class="col-lg-6">
                            <input type="text" name="config_host" value="<?= config_item('config_host') ?>"
                                   class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-3 control-label"><?= lang('username') ?></label>
                        <div class="col-lg-6">
                            <input type="text" name="config_username" value="<?= config_item('config_username') ?>"
                                   class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-3 control-label"><?= lang('password') ?></label>
                        <div class="col-lg-6">
                            <?php $password = strlen(decrypt(config_item('config_password'))); ?>
                            <input type="password" name="config_password" value="" placeholder="<?php
                            if (!empty($password)) {
                                for ($p = 1; $p <= $password; $p++) {
                                    echo '*';
                                }
                            } ?>" class="form-control">
                            <strong id="show_password" class="required"></strong>
                        </div>
                        <div class="col-lg-3">
                            <a data-toggle="modal" data-target="#myModal"
                               href="<?= base_url('admin/client/see_password/emin') ?>"
                               id="see_password"><?= lang('see_password') ?></a>
                            <strong id="hosting_password" class="required"></strong>
                        </div>
                    </div>
                    <?php
                    $mailbox = config_item('config_mailbox');
                    $unread_email = config_item('unread_email');
                    ?>
                    <div class="form-group">
                        <label class="col-lg-3 control-label"><?= lang('mailbox') ?></label>
                        <div class="col-lg-6">
                            <input type="text" name="config_mailbox"
                                   value="<?= (!empty($mailbox) ? $mailbox : 'INBOX') ?>"
                                   class="form-control">
                            <span class="help-block">e.g Gmail: INBOX</span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-lg-3 control-label"></label>
                        <div class="col-lg-3">
                            <label class="checkbox-inline c-checkbox">
                                <input type="checkbox" name="unread_email" <?php
                                if ($unread_email == 'on') {
                                    echo "checked=\"checked\"";
                                }
                                ?> >
                                <span class="fa fa-check"></span><?= lang('unread_email') ?>
                            </label>


                        </div>
                        <div class="col-sm-6">
                            <label class="checkbox-inline c-checkbox">
                                <input type="checkbox" name="delete_mail_after_import" <?php
                                if (config_item('delete_mail_after_import') == 'on') {
                                    echo "checked=\"checked\"";
                                }
                                ?> >
                                <span class="fa fa-check"></span><?= lang('delete_mail_after_import') ?>
                            </label>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-lg-3 control-label"><?= lang('for_leads') ?></label>
                        <div class="col-lg-6">
                            <div class="checkbox c-checkbox">
                                <label class="needsclick">
                                    <input type="checkbox" <?php
                                    if (config_item('for_leads') == 'on') {
                                        echo "checked=\"checked\"";
                                    }
                                    ?> name="for_leads" id="for_leads">
                                    <span class="fa fa-check"></span>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div
                        id="imap_search_for_leads" <?php echo (config_item('for_leads') != 'on') ? 'style="display:none"' : '' ?>>
                        <div class="form-group">
                            <label class="col-lg-3 control-label"><?= lang('imap_search') ?></label>
                            <div class="col-lg-6">
                                <input type="text" name="imap_search_for_leads" class="form-control"
                                       value="<?= config_item('imap_search_for_leads') ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-3 control-label"><?= lang('search_keyword') ?></label>
                            <div class="col-lg-6">
                                <input type="text" name="leads_keyword" class="form-control"
                                       value="<?= config_item('leads_keyword') ?>">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-3 control-label"><?= lang('for_tickets') ?></label>
                        <div class="col-lg-6">
                            <div class="checkbox c-checkbox">
                                <label class="needsclick">
                                    <input type="checkbox" <?php
                                    if (config_item('for_tickets') == 'on') {
                                        echo "checked=\"checked\"";
                                    }
                                    ?> name="for_tickets" id="for_tickets">
                                    <span class="fa fa-check"></span>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div
                        id="imap_search_for_tickets" <?php echo (config_item('for_tickets') != 'on') ? 'style="display:none"' : '' ?>>
                        <div class="form-group">
                            <label class="col-lg-3 control-label"><?= lang('imap_search') ?></label>
                            <div class="col-lg-6">
                                <input type="text" name="imap_search_for_tickets" class="form-control"
                                       value="<?= config_item('imap_search_for_tickets') ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-3 control-label"><?= lang('search_keyword') ?></label>
                            <div class="col-lg-6">
                                <input type="text" name="tickets_keyword" class="form-control"
                                       value="<?= config_item('tickets_keyword') ?>">
                            </div>
                        </div>
                    </div>
                    <?php
                    $all_user_info = get_result('tbl_users', array('role_id !=' => 2, 'activated' => 1))
                    ?>
                    <div class="form-group">
                        <label class="col-lg-3 control-label"><?= lang('notified_user') ?></label>
                        <div class="col-lg-6">
                            <select name="notified_user[]" style="width: 100%" multiple
                                    class="form-control select_multi">
                                <?php
                                $user_id = json_decode(config_item('notified_user'));
                                if (!empty($all_user_info)) {
                                    foreach ($all_user_info as $v_user) :
                                        $profile_info = $this->db->where('user_id', $v_user->user_id)->get('tbl_account_details')->row();
                                        if (!empty($profile_info)) {
                                            ?>
                                            <option value="<?= $v_user->user_id ?>"
                                                <?php if (!empty($user_id)) {
                                                    foreach ($user_id as $v_id) {
                                                        if ($v_id == $v_user->user_id) {
                                                            echo 'selected';
                                                        }
                                                    }
                                                } ?>
                                            ><?= $profile_info->fullname ?></option>
                                            <?php
                                        }
                                    endforeach;
                                }
                                ?>
                            </select>
                        </div>
                    </div>


                    <div class="form-group">
                        <label class="col-lg-3 control-label"><?= lang('postmaster_link') ?></label>
                        <div class="col-lg-9">
                            <p class="form-control-static">
                                <strong>wget <?= base_url() ?>postmaster -O /dev/null</strong>
                            </p>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-lg-3 control-label"><?= lang('last_postmaster_run') ?></label>
                        <div class="col-lg-6">
                            <p class="form-control-static">
                                <strong>
                                    <?php
                                    $last_postmaster_run = config_item('last_postmaster_run');
                                    if (!empty($last_postmaster_run)) {
                                        echo date("Y-m-d H:i", config_item('last_postmaster_run'));
                                    } else {
                                        echo "-";
                                    } ?>
                                </strong>
                            </p>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-lg-3 control-label"></label>
                        <div class="col-lg-9">
                            <div class="pull-left">
                                <button type="submit"
                                        class="btn btn-sm btn-primary"><?= lang('save_changes') ?></button>
                            </div>

                            <div class="pull-right">
                                <p data-toggle="tooltip" data-placement="top"
                                   title="<?= lang('save_email_then_test') ?>">
                                    <a href="<?= base_url() ?>admin/settings/test_email"
                                       class="btn btn-success pull-right"><?= lang('test_email_settings') ?></a>
                                </p>
                            </div>

                        </div>
                    </div>
                </div>
            </section>
        </form>
    </div>
    <!-- End Form -->
</div>