<link href="<?= base_url()?>dist/css/pages/inbox.css" rel="stylesheet">
<div class="row card-body">
    <div class="col-md-3">
        <div class="panel panel-custom">
            <div class="panel-heading">
                <h3 class="panel-title"><?= lang('all_messages') ?>

                </h3>
            </div>

            <div class="panel-body inbox-panel">
                <ul class="list-group list-group-full">
                    <li class="<?php echo ($menu_active == 'inbox') ? 'active' : ''; ?> list-group-item">
                        <a href="<?= base_url() ?>admin/mailbox/index/inbox"> <i class="mdi mdi-gmail"></i>
                            <?= lang('inbox') ?>
                            <span class="label label-primary pull-right"><?php
                                if (!empty($unread_mail)) {
                                    echo $unread_mail;
                                } else {
                                    echo '0';
                                }
                                ?></span>
                        </a>
                    </li>
                    <li class="<?php echo ($menu_active == 'sent') ? 'active' : ''; ?> list-group-item">
                        <a href="<?= base_url() ?>admin/mailbox/index/sent"> <i class="mdi mdi-file-document-box"></i>
                            <?= lang('sent') ?>
                        </a>
                    </li>
                    <li class="<?php echo ($menu_active == 'draft') ? 'active' : ''; ?> list-group-item"><a
                            href="<?= base_url() ?>admin/mailbox/index/draft"><i class="mdi mdi-send"></i>
                            Drafts</a></li>
                    <li class="<?php echo ($menu_active == 'favourites') ? 'active' : ''; ?> list-group-item">
                        <a href="<?= base_url() ?>admin/mailbox/index/favourites"> <i class="mdi mdi-star"></i>
                            <?= lang('favourites') ?>
                        </a>
                    </li>
                    <li class="<?php echo ($menu_active == 'trash') ? 'active' : ''; ?> list-group-item">
                        <a href="<?= base_url() ?>admin/mailbox/index/trash"> <i class="mdi mdi-delete"></i>
                            <?= lang('trash') ?><span class="label label-warning pull-right"><?php
                                $inbox_query = $this->db->where(array('to' => $this->session->userdata('email'), 'deleted' => 'Yes'))->get('tbl_inbox');
                                $totat_inbox = $inbox_query->num_rows();
                                $sent_query = $this->db->where(array('user_id' => $this->session->userdata('user_id'), 'deleted' => 'Yes'))->get('tbl_sent');
                                $totat_sent = $sent_query->num_rows();
                                $draft_query = $this->db->where(array('user_id' => $this->session->userdata('user_id'), 'deleted' => 'Yes'))->get('tbl_draft');
                                $tatal_draft = $draft_query->num_rows();
                                echo $totat_inbox + $totat_sent + $tatal_draft;
                                ?></span></a></li>

                </ul>
            </div><!-- /.box-body -->
        </div><!-- /. box -->
    </div><!-- /.col -->
    <div class="col-md-9 bg-light border-left">
        <?php echo message_box('success'); ?>
        <?php echo message_box('error'); ?>
        <?php $this->load->view('admin/mailbox/' . $view) ?>
    </div><!-- /.col -->
