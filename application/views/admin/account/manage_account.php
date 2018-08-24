<?= message_box('success'); ?>
<?= message_box('error');
$created = can_action('36', 'created');
$edited = can_action('36', 'edited');
$deleted = can_action('36', 'deleted');
if (!empty($created) || !empty($edited)){
?>
<div class="nav-tabs-custom">
    <!-- Tabs within a box -->
    <ul class="nav nav-tabs">
        <li class="<?= $active == 1 ? 'active' : ''; ?>"><a href="#manage"
                                                            data-toggle="tab"><?= lang('manage_account') ?></a></li>
        <li class="<?= $active == 2 ? 'active' : ''; ?>"><a href="#create"
                                                            data-toggle="tab"><?= lang('new_account') ?></a></li>
    </ul>
    <div class="tab-content bg-white">
        <!-- ************** general *************-->
        <div class="tab-pane <?= $active == 1 ? 'active' : ''; ?>" id="manage">
            <?php } else { ?>
            <div class="panel panel-custom">
                <header class="panel-heading ">
                    <div class="panel-title"><strong><?= lang('manage_account') ?></strong></div>
                </header>
                <?php } ?>
                <div class="table-responsive">
                    <table class="table table-striped DataTables " id="">
                        <thead>
                        <tr>
                            <th><?= lang('account') ?></th>
                            <th><?= lang('description') ?></th>
                            <?php if (!empty($edited) || !empty($deleted)) { ?>
                                <th class="col-options no-sort"><?= lang('action') ?></th>
                            <?php } ?>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $currency = $this->db->where('code', config_item('default_currency'))->get('tbl_currencies')->row();
                        $total_balance = 0;
                        if (!empty($all_account)) {
                            foreach ($all_account as $v_account) {
                                $can_edit = $this->account_model->can_action('tbl_accounts', 'edit', array('account_id' => $v_account->account_id));
                                $can_delete = $this->account_model->can_action('tbl_accounts', 'delete', array('account_id' => $v_account->account_id));
                                $total_balance += $v_account->balance;
                                ?>
                                <tr id="table_account_<?= $v_account->account_id ?>">
                                    <td><?= $v_account->account_name ?></td>
                                    <td><?= $v_account->description ?></td>
                                    <td><?= display_money($v_account->balance, $currency->symbol); ?></td>
                                    <td>
                                        <?php if (!empty($can_edit) && !empty($edited)) { ?>
                                            <?= btn_edit('admin/account/manage_account/' . $v_account->account_id) ?>
                                        <?php }
                                        if (!empty($can_delete) && !empty($deleted)) {
                                            ?>
                                            <?php echo ajax_anchor(base_url("admin/account/delete_account/$v_account->account_id"), "<i class='btn btn-xs btn-danger fa fa-trash-o'></i>", array("class" => "", "title" => lang('delete'), "data-fade-out-on-success" => "#table_account_" . $v_account->account_id)); ?>
                                        <?php } ?>
                                    </td>
                                </tr>
                                <?php
                            };
                        }
                        ?>
                        <tr class="total_amount">
                            <td colspan="2" style="text-align: right;"><strong><?= lang('total_amount') ?> : </strong>
                            </td>
                            <td colspan="2" style="padding-left: 8px;">
                                <strong><?= display_money($total_balance, $currency->symbol); ?></strong></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php if (!empty($created) || !empty($edited)){ ?>
                <div class="tab-pane <?= $active == 2 ? 'active' : ''; ?>" id="create">
                    <form role="form" data-parsley-validate="" novalidate="" enctype="multipart/form-data" id="form"
                          action="<?php echo base_url(); ?>admin/account/save_account/<?php
                          if (!empty($account_info)) {
                              echo $account_info->account_id;
                          }
                          ?>" method="post" class="form-horizontal  ">
                        <div class="form-group">
                            <label class="col-lg-3 control-label"><?= lang('account_name') ?> <span
                                    class="text-danger">*</span></label>
                            <div class="col-lg-5">
                                <input type="text" class="form-control" value="<?php
                                if (!empty($account_info)) {
                                    echo $account_info->account_name;
                                }
                                ?>" name="account_name" required="">
                            </div>

                        </div>
                        <!-- End discount Fields -->
                        <div class="form-group terms">
                            <label class="col-lg-3 control-label"><?= lang('description') ?> </label>
                            <div class="col-lg-5">
                        <textarea name="description" class="form-control"><?php
                            if (!empty($account_info)) {
                                echo $account_info->description;
                            }
                            ?></textarea>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-3 control-label"><?= lang('initial_balance') ?> <span
                                    class="text-danger">*</span></label>
                            <div class="col-lg-5">
                                <input type="text" data-parsley-type="number" class="form-control" value="<?php
                                if (!empty($account_info)) {
                                    echo $account_info->balance;
                                }
                                ?>" name="balance" required="">
                            </div>

                        </div>
                        <div class="form-group" id="border-none">
                            <label for="field-1" class="col-sm-3 control-label"><?= lang('permission') ?> <span
                                    class="required">*</span></label>
                            <div class="col-sm-9">
                                <div class="checkbox c-radio needsclick">
                                    <label class="needsclick">
                                        <input id="" <?php
                                        if (!empty($account_info->permission) && $account_info->permission == 'all') {
                                            echo 'checked';
                                        } elseif (empty($account_info)) {
                                            echo 'checked';
                                        }
                                        ?> type="radio" name="permission" value="everyone">
                                        <span class="fa fa-circle"></span><?= lang('everyone') ?>
                                        <i title="<?= lang('permission_for_all') ?>"
                                           class="fa fa-question-circle" data-toggle="tooltip"
                                           data-placement="top"></i>
                                    </label>
                                </div>
                                <div class="checkbox c-radio needsclick">
                                    <label class="needsclick">
                                        <input id="" <?php
                                        if (!empty($account_info->permission) && $account_info->permission != 'all') {
                                            echo 'checked';
                                        }
                                        ?> type="radio" name="permission" value="custom_permission"
                                        >
                                        <span class="fa fa-circle"></span><?= lang('custom_permission') ?> <i
                                            title="<?= lang('permission_for_customization') ?>"
                                            class="fa fa-question-circle" data-toggle="tooltip"
                                            data-placement="top"></i>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group <?php
                        if (!empty($account_info->permission) && $account_info->permission != 'all') {
                            echo 'show';
                        }
                        ?>" id="permission_user_1">
                            <label for="field-1"
                                   class="col-sm-3 control-label"><?= lang('select') . ' ' . lang('users') ?>
                                <span
                                    class="required">*</span></label>
                            <div class="col-sm-9">
                                <?php
                                if (!empty($permission_user)) {
                                    foreach ($permission_user as $key => $v_user) {

                                        if ($v_user->role_id == 1) {
                                            $role = '<strong class="badge btn-danger">' . lang('admin') . '</strong>';
                                        } else {
                                            $role = '<strong class="badge btn-primary">' . lang('staff') . '</strong>';
                                        }

                                        ?>
                                        <div class="checkbox c-checkbox needsclick">
                                            <label class="needsclick">
                                                <input type="checkbox"
                                                    <?php
                                                    if (!empty($account_info->permission) && $account_info->permission != 'all') {
                                                        $get_permission = json_decode($account_info->permission);
                                                        foreach ($get_permission as $user_id => $v_permission) {
                                                            if ($user_id == $v_user->user_id) {
                                                                echo 'checked';
                                                            }
                                                        }

                                                    }
                                                    ?>
                                                       value="<?= $v_user->user_id ?>"
                                                       name="assigned_to[]"
                                                       class="needsclick">
                                                        <span
                                                            class="fa fa-check"></span><?= $v_user->username . ' ' . $role ?>
                                            </label>

                                        </div>
                                        <div class="action_1 p
                                                <?php

                                        if (!empty($account_info->permission) && $account_info->permission != 'all') {
                                            $get_permission = json_decode($account_info->permission);

                                            foreach ($get_permission as $user_id => $v_permission) {
                                                if ($user_id == $v_user->user_id) {
                                                    echo 'show';
                                                }
                                            }

                                        }
                                        ?>
                                                " id="action_1<?= $v_user->user_id ?>">
                                            <label class="checkbox-inline c-checkbox">
                                                <input id="<?= $v_user->user_id ?>" checked type="checkbox"
                                                       name="action_1<?= $v_user->user_id ?>[]"
                                                       disabled
                                                       value="view">
                                                        <span
                                                            class="fa fa-check"></span><?= lang('can') . ' ' . lang('view') ?>
                                            </label>
                                            <label class="checkbox-inline c-checkbox">
                                                <input id="<?= $v_user->user_id ?>"
                                                    <?php

                                                    if (!empty($account_info->permission) && $account_info->permission != 'all') {
                                                        $get_permission = json_decode($account_info->permission);

                                                        foreach ($get_permission as $user_id => $v_permission) {
                                                            if ($user_id == $v_user->user_id) {
                                                                if (in_array('edit', $v_permission)) {
                                                                    echo 'checked';
                                                                };

                                                            }
                                                        }

                                                    }
                                                    ?>
                                                       type="checkbox"
                                                       value="edit" name="action_<?= $v_user->user_id ?>[]">
                                                        <span
                                                            class="fa fa-check"></span><?= lang('can') . ' ' . lang('edit') ?>
                                            </label>
                                            <label class="checkbox-inline c-checkbox">
                                                <input id="<?= $v_user->user_id ?>"
                                                    <?php

                                                    if (!empty($account_info->permission) && $account_info->permission != 'all') {
                                                        $get_permission = json_decode($account_info->permission);
                                                        foreach ($get_permission as $user_id => $v_permission) {
                                                            if ($user_id == $v_user->user_id) {
                                                                if (in_array('delete', $v_permission)) {
                                                                    echo 'checked';
                                                                };
                                                            }
                                                        }

                                                    }
                                                    ?>
                                                       name="action_<?= $v_user->user_id ?>[]"
                                                       type="checkbox"
                                                       value="delete">
                                                        <span
                                                            class="fa fa-check"></span><?= lang('can') . ' ' . lang('delete') ?>
                                            </label>
                                            <input id="<?= $v_user->user_id ?>" type="hidden"
                                                   name="action_<?= $v_user->user_id ?>[]" value="view">

                                        </div>


                                        <?php
                                    }
                                }
                                ?>


                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-3 control-label"></label>
                            <div class="col-lg-5">
                                <button type="submit"
                                        class="btn btn-sm btn-primary"><?= lang('create_acount') ?></button>
                            </div>
                        </div>
                    </form>
                </div>
            <?php } else { ?>
        </div>
        <?php } ?>

    </div>
</div>