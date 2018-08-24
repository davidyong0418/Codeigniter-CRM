<?= message_box('success'); ?>
<?= message_box('error');
$created = can_action('69', 'created');
$edited = can_action('69', 'edited');
$deleted = can_action('69', 'deleted');
if (!empty($created) || !empty($edited)){
?>
<div class="nav-tabs-custom">
    <!-- Tabs within a box -->
    <ul class="nav nav-tabs">
        <li class="<?= $active == 1 ? 'active' : ''; ?>"><a href="#manage"
                                                            data-toggle="tab"><?= lang('goal_tracking') ?></a></li>
        <li class="<?= $active == 2 ? 'active' : ''; ?>"><a href="#create"
                                                            data-toggle="tab"><?= lang('new_goal') ?></a></li>
    </ul>
    <div class="tab-content bg-white">
        <!-- ************** general *************-->
        <div class="tab-pane <?= $active == 1 ? 'active' : ''; ?>" id="manage">
            <?php } else { ?>
            <div class="panel panel-custom">
                <header class="panel-heading ">
                    <div class="panel-title"><strong><?= lang('goal_tracking') ?></strong></div>
                </header>
                <?php } ?>
                <div class="table-responsive">
                    <table class="table table-striped DataTables " id="DataTables" cellspacing="0" width="100%">
                        <thead>
                        <tr>
                            <th><?= lang('subject') ?></th>
                            <th><?= lang('type') ?></th>
                            <th><?= lang('achievement') ?></th>
                            <th><?= lang('start_date') ?></th>
                            <th><?= lang('end_date') ?></th>
                            <th><?= lang('progress') ?></th>
                            <th class="col-options no-sort"><?= lang('action') ?></th>
                        </tr>
                        </thead>
                        <tbody>

                        <?php
                        $all_goal_tracking = $this->items_model->get_permission('tbl_goal_tracking');
                        if (!empty($all_goal_tracking)):foreach ($all_goal_tracking as $v_goal_tracking):

                            $goal_type_info = $this->db->where('goal_type_id', $v_goal_tracking->goal_type_id)->get('tbl_goal_type')->row();

                            $progress = $this->items_model->get_progress($v_goal_tracking);

                            $can_edit = $this->items_model->can_action('tbl_goal_tracking', 'edit', array('goal_tracking_id' => $v_goal_tracking->goal_tracking_id));
                            $can_delete = $this->items_model->can_action('tbl_goal_tracking', 'delete', array('goal_tracking_id' => $v_goal_tracking->goal_tracking_id)); ?>

                            <tr>
                                <td><a class="text-info"
                                       href="<?= base_url() ?>admin/goal_tracking/goal_details/<?= $v_goal_tracking->goal_tracking_id ?>"><?php echo $v_goal_tracking->subject; ?></a>
                                </td>
                                <td>
                            <span data-toggle="tooltip" data-placement="top"
                                  title="<?= $goal_type_info->description ?>"><?= lang($goal_type_info->type_name) ?></span>
                                </td>
                                <td><?= $v_goal_tracking->achievement ?></td>
                                <td><?= strftime(config_item('date_format'), strtotime($v_goal_tracking->start_date)); ?></td>
                                <td><?= strftime(config_item('date_format'), strtotime($v_goal_tracking->end_date)); ?></td>
                                <td class="col-sm-1" style="padding-bottom: 0px;padding-top: 3px">

                                    <div class="inline ">
                                        <div class="easypiechart text-success" style="margin: 0px;"
                                             data-percent="<?= $progress['progress'] ?>"
                                             data-line-width="5" data-track-Color="#f0f0f0"
                                             data-bar-color="#<?php
                                             if ($progress['progress'] == 100) {
                                                 echo '8ec165';
                                             } elseif ($progress['progress'] >= 40 && $progress['progress'] <= 50) {
                                                 echo '5d9cec';
                                             } elseif ($progress['progress'] >= 51 && $progress['progress'] <= 99) {
                                                 echo '7266ba';
                                             } else {
                                                 echo 'fb6b5b';
                                             }
                                             ?>" data-rotate="270" data-scale-Color="false"
                                             data-size="50"
                                             data-animate="2000">
                                                        <span class="small "><?= $progress['progress'] ?>
                                                            %</span>
                                        </div>
                                    </div>

                                </td>

                                <td>
                                    <?= btn_view('admin/goal_tracking/goal_details/' . $v_goal_tracking->goal_tracking_id) ?>
                                    <?php if (!empty($can_edit) && !empty($edited)) { ?>
                                        <?= btn_edit('admin/goal_tracking/index/' . $v_goal_tracking->goal_tracking_id) ?>
                                    <?php }
                                    if (!empty($can_delete) && !empty($deleted)) {
                                        ?>
                                        <?= btn_delete('admin/goal_tracking/delete_goal/' . $v_goal_tracking->goal_tracking_id) ?>
                                    <?php } ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php if (!empty($created) || !empty($edited)){ ?>
                <div class="tab-pane <?= $active == 2 ? 'active' : ''; ?>" id="create">
                    <form role="form" enctype="multipart/form-data" id="form"  data-parsley-validate="" novalidate=""
                          action="<?php echo base_url(); ?>admin/goal_tracking/save_goal_tracking/<?php
                          if (!empty($goal_info)) {
                              echo $goal_info->goal_tracking_id;
                          }
                          ?>" method="post" class="form-horizontal  ">
                        <div class="form-group">
                            <label class="col-lg-3 control-label"><?= lang('subject') ?> <span
                                    class="text-danger">*</span></label>
                            <div class="col-lg-5">
                                <input type="text" class="form-control" value="<?php
                                if (!empty($goal_info)) {
                                    echo $goal_info->subject;
                                }
                                ?>" name="subject" required="">
                            </div>

                        </div>
                        <div class="form-group">
                            <label class="col-lg-3 control-label"><?= lang('goal') . ' ' . lang('type') ?> <span
                                    class="text-danger">*</span></label>
                            <div class="col-lg-5">
                                <select name="goal_type_id" class="form-control select_box" style="width: 100%"
                                        id="goal_type_id"
                                        required="">
                                    <?php
                                    $goal_type = $this->db->get('tbl_goal_type')->result();
                                    if (!empty($goal_type)) {
                                        foreach ($goal_type as $v_goal_type) {
                                            ?>
                                            <option
                                                value="<?= $v_goal_type->goal_type_id ?>" <?php
                                            if (!empty($goal_info->goal_type_id)) {
                                                echo $v_goal_type->goal_type_id == $goal_info->goal_type_id ? 'selected' : '';
                                            }
                                            ?>><?= lang($v_goal_type->type_name) ?>
                                            </option>
                                            <?php
                                        }
                                    }
                                    ?>
                                </select>
                            </div>

                        </div>
                        <div class="form-group " id="account"
                             style="<?php if (!empty($goal_info->goal_type_id) && $goal_info->goal_type_id == 2 || !empty($goal_info->goal_type_id) && $goal_info->goal_type_id == 4) {
                                 echo 'display:block';
                             } else {
                                 echo 'display:none';
                             }; ?>">
                            <label class="col-lg-3 control-label mt-lg"><?= lang('account') ?> <span
                                    class="text-danger">*</span> </label>
                            <div class="col-lg-5 mt-lg">
                                <select class="form-control select_box" style="width: 100%" name="account_id"
                                        id="account_id"
                                    <?php if (empty($goal_info->account_id)) {
                                        echo 'disabled';
                                    }; ?>
                                        required>
                                    <?php
                                    $account_info = $this->items_model->get_permission('tbl_accounts');

                                    if (!empty($account_info)) {
                                        foreach ($account_info as $v_account) {
                                            ?>
                                            <option value="<?= $v_account->account_id ?>"
                                                <?php
                                                if (!empty($goal_info->account_id)) {
                                                    echo $goal_info->account_id == $v_account->account_id ? 'selected' : '';
                                                }
                                                ?>
                                            ><?= $v_account->account_name ?></option>
                                            <?php
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-3 control-label"><?= lang('achievement') ?> <span
                                    class="text-danger">*</span></label>
                            <div class="col-lg-5">
                                <input type="number" class="form-control" value="<?php
                                if (!empty($goal_info)) {
                                    echo $goal_info->achievement;
                                }
                                ?>" name="achievement" required="">
                            </div>

                        </div>
                        <div class="form-group">
                            <label class="col-lg-3 control-label"><?= lang('start_date') ?> <span
                                    class="text-danger">*</span></label>
                            <div class="col-lg-5">
                                <div class="input-group">
                                    <input type="text" name="start_date" class="form-control datepicker"
                                           value="<?php
                                           if (!empty($goal_info->start_date)) {
                                               echo $goal_info->start_date;
                                           } else {
                                               echo date('Y-m-d');
                                           }
                                           ?>" data-date-format="<?= config_item('date_picker_format'); ?>">
                                    <div class="input-group-addon">
                                        <a href="#"><i class="fa fa-calendar"></i></a>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="form-group">
                            <label class="col-lg-3 control-label"><?= lang('end_date') ?> <span
                                    class="text-danger">*</span></label>
                            <div class="col-lg-5">
                                <div class="input-group">
                                    <input type="text" name="end_date" class="form-control datepicker"
                                           value="<?php
                                           if (!empty($goal_info->end_date)) {
                                               echo $goal_info->end_date;
                                           } else {
                                               echo date('Y-m-d');
                                           }
                                           ?>" data-date-format="<?= config_item('date_picker_format'); ?>">
                                    <div class="input-group-addon">
                                        <a href="#"><i class="fa fa-calendar"></i></a>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <!-- End discount Fields -->
                        <div class="form-group terms">
                            <label class="col-lg-3 control-label"><?= lang('description') ?> </label>
                            <div class="col-lg-5">
                        <textarea name="description" class="form-control"><?php
                            if (!empty($goal_info)) {
                                echo $goal_info->description;
                            }
                            ?></textarea>
                            </div>
                        </div>

                        <div class="form-group" id="border-none">
                            <label for="field-1" class="col-sm-3 control-label"><?= lang('permission') ?> <span
                                    class="required">*</span></label>
                            <div class="col-sm-9">
                                <div class="checkbox c-radio needsclick">
                                    <label class="needsclick">
                                        <input id="" <?php
                                        if (!empty($goal_info->permission) && $goal_info->permission == 'all') {
                                            echo 'checked';
                                        } elseif (empty($goal_info)) {
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
                                        if (!empty($goal_info->permission) && $goal_info->permission != 'all') {
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
                        if (!empty($goal_info->permission) && $goal_info->permission != 'all') {
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
                                                    if (!empty($goal_info->permission) && $goal_info->permission != 'all') {
                                                        $get_permission = json_decode($goal_info->permission);
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

                                        if (!empty($goal_info->permission) && $goal_info->permission != 'all') {
                                            $get_permission = json_decode($goal_info->permission);

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

                                                    if (!empty($goal_info->permission) && $goal_info->permission != 'all') {
                                                        $get_permission = json_decode($goal_info->permission);

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

                                                    if (!empty($goal_info->permission) && $goal_info->permission != 'all') {
                                                        $get_permission = json_decode($goal_info->permission);
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
                            <div class="col-lg-6">
                                <div class="checkbox c-checkbox">
                                    <label class="needsclick">
                                        <input type="checkbox" <?php
                                        if (!empty($goal_info->notify_goal_achive) && $goal_info->notify_goal_achive == 'on') {
                                            echo "checked=\"checked\"";
                                        }
                                        ?> name="notify_goal_achive">
                                        <span class="fa fa-check"></span>
                                    </label>
                                    <?= lang('notify_goal_achive') ?>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-3 control-label"></label>
                            <div class="col-lg-6">
                                <div class="checkbox c-checkbox">
                                    <label class="needsclick">
                                        <input type="checkbox" <?php
                                        if (!empty($goal_info->notify_goal_not_achive) && $goal_info->notify_goal_not_achive == 'on') {
                                            echo "checked=\"checked\"";
                                        }
                                        ?> name="notify_goal_not_achive">
                                        <span class="fa fa-check"></span>
                                    </label>
                                    <?= lang('notify_goal_not_achive') ?>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-3 control-label"></label>
                            <div class="col-lg-5">
                                <button type="submit" class="btn btn-sm btn-primary"><?= lang('update') ?></button>
                            </div>
                        </div>
                    </form>
                </div>
            <?php } else { ?>
        </div>
        <?php } ?>
    </div>
</div>

<!-- end -->