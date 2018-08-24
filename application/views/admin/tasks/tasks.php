<?php include_once 'assets/admin-ajax.php'; ?>
<?php echo message_box('success'); ?>
<?php echo message_box('error'); ?>
<style>
    .note-editor .note-editable {
        height: 150px;
    }

    a:hover {
        text-decoration: none;
    }
</style>
<?php
$mdate = date('Y-m-d');
$last_7_days = date('Y-m-d', strtotime('today - 7 days'));
$all_goal_tracking = $this->tasks_model->get_permission('tbl_goal_tracking');

$all_goal = 0;
$bank_goal = 0;
$complete_achivement = 0;
if (!empty($all_goal_tracking)) {
    foreach ($all_goal_tracking as $v_goal_track) {
        $goal_achieve = $this->tasks_model->get_progress($v_goal_track, true);

        if ($v_goal_track->goal_type_id == 8) {

            if ($v_goal_track->end_date <= $mdate) { // check today is last date or not

                if ($v_goal_track->email_send == 'no') {// check mail are send or not
                    if ($v_goal_track->achievement <= $goal_achieve['achievement']) {
                        if ($v_goal_track->notify_goal_achive == 'on') {// check is notify is checked or not check
                            $this->tasks_model->send_goal_mail('goal_achieve', $v_goal_track);
                        }
                    } else {
                        if ($v_goal_track->notify_goal_not_achive == 'on') {// check is notify is checked or not check
                            $this->tasks_model->send_goal_mail('goal_not_achieve', $v_goal_track);
                        }
                    }
                }
            }
            $all_goal += $v_goal_track->achievement;
            $complete_achivement += $goal_achieve['achievement'];
        }
    }
}
// 30 days before
$last_weeks = 0;
for ($iDay = 7; $iDay >= 0; $iDay--) {
    $date = date('Y-m-d', strtotime('today - ' . $iDay . 'days'));
    $where = array('task_created_date >=' => $date . " 00:00:00", 'task_created_date <=' => $date . " 23:59:59", 'task_status' => 'completed');

    $invoice_result[$date] = count($this->db->where($where)->get('tbl_task')->result());
    $last_weeks += count($this->db->where($where)->get('tbl_task')->result());
}

$terget_achievement = $this->db->where(array('goal_type_id' => 8, 'start_date >=' => $last_7_days, 'end_date <=' => $mdate))->get('tbl_goal_tracking')->result();

$total_terget = 0;
if (!empty($terget_achievement)) {
    foreach ($terget_achievement as $v_terget) {
        $total_terget += $v_terget->achievement;
    }
}
$tolal_goal = $all_goal + $bank_goal;
$curency = $this->tasks_model->check_by(array('code' => config_item('default_currency')), 'tbl_currencies');

if ($this->session->userdata('user_type') == 1) {
    $margin = 'margin-bottom:20px';
    ?>
    <div class="col-sm-12 bg-white p0" style="<?= $margin ?>">
        <div class="col-md-4">
            <div class="row row-table pv-lg">
                <div class="col-xs-6">
                    <p class="m0 lead"><?= ($tolal_goal) ?></p>
                    <p class="m0">
                        <small><?= lang('achievement') ?></small>
                    </p>
                </div>
                <div class="col-xs-6 ">
                    <p class="m0 lead"><?= ($last_weeks) ?></p>
                    <p class="m0">
                        <small><?= lang('last_weeks') . ' ' . lang('created') ?></small>
                    </p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="row row-table ">
                <div class="col-xs-6">
                    <p class="m0 lead"><?= ($complete_achivement) ?></p>
                    <p class="m0">
                        <small><?= lang('completed') . ' ' . lang('achievements') ?></small>
                    </p>
                </div>
                <div class="col-xs-6 pt">
                    <div data-sparkline="" data-bar-color="#23b7e5" data-height="60" data-bar-width="8"
                         data-bar-spacing="6" data-chart-range-min="0" values="<?php
                    if (!empty($invoice_result)) {
                        foreach ($invoice_result as $v_invoice_result) {
                            echo $v_invoice_result . ',';
                        }
                    }
                    ?>">
                    </div>
                    <p class="m0">
                        <small>
                            <?php
                            if (!empty($invoice_result)) {
                                foreach ($invoice_result as $date => $v_invoice_result) {
                                    echo date('d', strtotime($date)) . ' ';
                                }
                            }
                            ?>
                        </small>
                    </p>

                </div>
            </div>

        </div>
        <div class="col-md-4">
            <div class="row row-table ">
                <div class="col-xs-6">
                    <p class="m0 lead">
                        <?php
                        if ($tolal_goal < $complete_achivement) {
                            $pending_goal = 0;
                        } else {
                            $pending_goal = $tolal_goal - $complete_achivement;
                        } ?>
                        <?= $pending_goal ?></p>
                    <p class="m0">
                        <small><?= lang('pending') . ' ' . lang('achievements') ?></small>
                    </p>
                </div>
                <?php
                if (!empty($tolal_goal)) {
                    if ($tolal_goal <= $complete_achivement) {
                        $total_progress = 100;
                    } else {
                        $progress = ($complete_achivement / $tolal_goal) * 100;
                        $total_progress = round($progress);
                    }
                } else {
                    $total_progress = 0;
                }
                ?>
                <div class="col-xs-6 text-center pt">
                    <div class="inline ">
                        <div class="easypiechart text-success"
                             data-percent="<?= $total_progress ?>"
                             data-line-width="5" data-track-Color="#f0f0f0"
                             data-bar-color="#<?php
                             if ($total_progress == 100) {
                                 echo '8ec165';
                             } elseif ($total_progress >= 40 && $total_progress <= 50) {
                                 echo '5d9cec';
                             } elseif ($total_progress >= 51 && $total_progress <= 99) {
                                 echo '7266ba';
                             } else {
                                 echo 'fb6b5b';
                             }
                             ?>" data-rotate="270" data-scale-Color="false"
                             data-size="50"
                             data-animate="2000">
                                                        <span class="small "><?= $total_progress ?>
                                                            %</span>
                            <span class="easypie-text"><strong><?= lang('done') ?></strong></span>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
<?php } ?>


<?php $complete = 0;
$not_started = 0;
if (!empty($all_task_info)):foreach ($all_task_info as $v_task):
    if ($v_task->task_status == 'completed') {
        $complete += count($v_task->task_id);
    }
    if ($v_task->task_status == 'not_started') {
        $not_started += count($v_task->task_id);
    }
endforeach;
endif;
$created = can_action('54', 'created');

$edited = can_action('54', 'edited');
$deleted = can_action('54', 'deleted');

$kanban = $this->session->userdata('task_kanban');
$uri_segment = $this->uri->segment(4);
if (!empty($kanban)) {
    $tasks = 'kanban';
} elseif ($uri_segment == 'kanban') {
    $tasks = 'kanban';
} else {
    $tasks = 'list';
}

if ($tasks == 'kanban') {
    $text = 'list';
    $btn = 'purple';
} else {
    $text = 'kanban';
    $btn = 'danger';
}

?>
<div class="mb-lg pull-left">
    <div class="pull-left pr-lg">
        <a href="<?= base_url() ?>admin/tasks/all_task/<?= $text ?>"
           class="btn btn-xs btn-<?= $btn ?> pull-right"
           data-toggle="tooltip"
           data-placement="top" title="<?= lang('switch_to_' . $text) ?>">
            <i class="fa fa-undo"> </i><?= ' ' . lang('switch_to_' . $text) ?>
        </a>
    </div>
</div>
<div class="row">
    <div class="col-sm-12">
        <?php if ($tasks == 'kanban') { ?>
            <link rel="stylesheet" href="<?= base_url() ?>assets/plugins/kanban/kan-app.css"/>
            <div class="app-wrapper">
                <p class="total-card-counter" id="totalCards"></p>
                <div class="board" id="board"></div>
            </div>
            <?php include_once 'assets/plugins/kanban/tasks_kan-app.php'; ?>
        <?php } else { ?>
            <div class="nav-tabs-custom">
                <!-- Tabs within a box -->
                <ul class="nav nav-tabs customtab">
                    <li class="nav-item <?= $active == 1 ? 'active' : '' ?>"><a href="#task_list" class="nav-link"
                                                                       data-toggle="tab"><?= lang('all_task') ?></a>
                    </li>
                    <?php if (!empty($created) || !empty($edited)) { ?>
                        <li class="nav-item <?= $active == 2 ? 'active' : '' ?>"><a href="#assign_task"  class="nav-link " 
                                                                           data-toggle="tab"><?= lang('assign_task') ?></a>
                        </li>
                        <li class="margin-right-auto nav-item"><a style="background-color: #03a9f3;color: #ffffff" class="nav-link "  
                               href="<?= base_url() ?>admin/tasks/import"><?= lang('import') . ' ' . lang('tasks') ?></a>
                        </li>
                    <?php }
                    $tasks_status = array_reverse($this->tasks_model->get_statuses());
                    foreach ($tasks_status as $v_status) {
                        $total_status = count($this->tasks_model->get_tasks($v_status['value']));
                        ?>
                        <li class="pull-right <?= $active == $v_status['value'] ? 'active' : ''; ?>"><a
                                href="<?= base_url() ?>admin/tasks/all_task/<?= $v_status['value'] ?>"
                            ><?= lang($v_status['value']) ?>
                                <small class="label label-danger"
                                       style="top: 11%;position: absolute;right: 5%;}"><?php if ($total_status != 0) {
                                        echo $total_status;
                                    } ?></small>
                            </a>
                        </li>
                    <?php }
                    ?>
                </ul>
                <div class="tab-content bg-white">
                    <!-- Stock Category List tab Starts -->
                    <div
                        class="tab-pane <?= $active == 1 || $active == 'not_started' || $active == 'in_progress' || $active == 'completed' || $active == 'deferred' || $active == 'waiting_for_someone' ? 'active' : '' ?>"
                        id="task_list">
                        <div class="box" style="border: none; padding-top: 15px;" data-collapsed="0">
                            <div class="box-body">
                                <!-- Table -->
                                <table class="table table-striped DataTables " id="DataTables" cellspacing="0"
                                       width="100%">
                                    <thead>
                                    <tr>
                                        <?php if (!empty($created) || !empty($edited)) { ?>
                                            <th data-check-all>

                                            </th>
                                        <?php } ?>
                                        <th class="col-sm-3"><?= lang('task_name') ?></th>
                                        <th class="col-sm-2"><?= lang('due_date') ?></th>
                                        <th class="col-sm-1"><?= lang('status') ?></th>
                                        <th class="col-sm-1"><?= lang('progress') ?></th>
                                        <th class="col-sm-2"><?= lang('assigned_to') ?></th>
                                        <?php $show_custom_fields = custom_form_table(3, null);
                                        if (!empty($show_custom_fields)) {
                                            foreach ($show_custom_fields as $c_label => $v_fields) {
                                                if (!empty($c_label)) {
                                                    ?>
                                                    <th><?= $c_label ?> </th>
                                                <?php }
                                            }
                                        }
                                        ?>
                                        <th class="col-sm-3"><?= lang('changes/view') ?></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    if (!empty($all_task_info)):foreach ($all_task_info as $key => $v_task):
                                        if ($v_task->task_status != 'completed' || !empty($completed)) {
                                            $can_edit = $this->tasks_model->can_action('tbl_task', 'edit', array('task_id' => $v_task->task_id));
                                            $can_delete = $this->tasks_model->can_action('tbl_task', 'delete', array('task_id' => $v_task->task_id));
                                            ?>
                                            <tr id="table-tasks-<?= $v_task->task_id ?>">
                                                <?php if (!empty($created) || !empty($edited)) { ?>
                                                    <td class="col-sm-1">
                                                        <div class="complete checkbox c-checkbox">
                                                            <label>
                                                                <input type="checkbox"
                                                                       data-id="<?= $v_task->task_id ?>"
                                                                       style="position: absolute;" <?php
                                                                if ($v_task->task_progress >= 100) {
                                                                    echo 'checked';
                                                                }
                                                                ?>>
                                                                <span class="fa fa-check"></span>
                                                            </label>
                                                        </div>
                                                    </td>
                                                <?php } ?>
                                                <td>
                                                    <a style="<?php
                                                    if ($v_task->task_progress >= 100) {
                                                        echo 'text-decoration: line-through;';
                                                    }
                                                    ?>"
                                                       href="<?= base_url() ?>admin/tasks/view_task_details/<?= $v_task->task_id ?>"><?php echo $v_task->task_name; ?></a>
                                                </td>
                                                <td><?php
                                                    $due_date = $v_task->due_date;
                                                    $due_time = strtotime($due_date);
                                                    $current_time = time();
                                                    if ($v_task->task_progress == 100) {
                                                        $c_progress = 100;
                                                    } elseif ($v_task->task_status == 'completed') {
                                                        $c_progress = 100;
                                                    } else {
                                                        $c_progress = 0;
                                                    }

                                                    ?>
                                                    <?= strftime(config_item('date_format'), strtotime($due_date)) ?>
                                                    <?php if ($current_time > $due_time && $c_progress < 100) { ?>
                                                        <span
                                                            class="label label-danger"><?= lang('overdue') ?></span>
                                                    <?php } ?></td>
                                                <td>
                                                    <?php
                                                    if ($v_task->task_status == 'completed') {
                                                        $label = 'success';
                                                    } elseif ($v_task->task_status == 'not_started') {
                                                        $label = 'info';
                                                    } elseif ($v_task->task_status == 'deferred') {
                                                        $label = 'danger';
                                                    } else {
                                                        $label = 'warning';
                                                    }
                                                    ?>
                                                    <span
                                                        class="label label-<?= $label ?>"><?= lang($v_task->task_status) ?> </span>
                                                </td>
                                                <td class="col-sm-1" style="padding-bottom: 0px;padding-top: 3px">

                                                    <div class="inline ">
                                                        <div class="easypiechart text-success" style="margin: 0px;"
                                                             data-percent="<?= $v_task->task_progress ?>"
                                                             data-line-width="5" data-track-Color="#f0f0f0"
                                                             data-bar-color="#<?php
                                                             if ($v_task->task_progress == 100) {
                                                                 echo '8ec165';
                                                             } else {
                                                                 echo 'fb6b5b';
                                                             }
                                                             ?>" data-rotate="270" data-scale-Color="false"
                                                             data-size="50"
                                                             data-animate="2000">
                                                        <span class="small "><?= $v_task->task_progress ?>
                                                            %</span>
                                                        </div>
                                                    </div>
                                                </td>

                                                <td>
                                                    <?php
                                                    if ($v_task->permission != 'all') {
                                                        $get_permission = json_decode($v_task->permission);
                                                        if (!empty($get_permission)) :
                                                            foreach ($get_permission as $permission => $v_permission) :
                                                                $user_info = $this->db->where(array('user_id' => $permission))->get('tbl_users')->row();
                                                                if ($user_info->role_id == 1) {
                                                                    $label = 'circle-danger';
                                                                } else {
                                                                    $label = 'circle-success';
                                                                }
                                                                $profile_info = $this->db->where(array('user_id' => $permission))->get('tbl_account_details')->row();
                                                                ?>

                                                                <a href="#" data-toggle="tooltip"
                                                                   data-placement="top"
                                                                   title="<?= $profile_info->fullname ?>"><img
                                                                        src="<?= base_url() . $profile_info->avatar ?>"
                                                                        class="img-circle img-xs" alt="">
                                                <span style="margin: 0px 0 8px -10px;"
                                                      class="circle <?= $label ?>  circle-lg"></span>
                                                                </a>

                                                                <?php
                                                            endforeach;
                                                        endif;
                                                    } else { ?>
                                                        <strong><?= lang('everyone') ?></strong>
                                                        <i
                                                            title="<?= lang('permission_for_all') ?>"
                                                            class="fa fa-question-circle" data-toggle="tooltip"
                                                            data-placement="top"></i>
                                                        <?php
                                                    }
                                                    ?>
                                                    <?php if (!empty($can_edit) && !empty($edited)) { ?>
                                                        <span data-placement="top" data-toggle="tooltip"
                                                              title="<?= lang('add_more') ?>">
                                            <a data-toggle="modal" data-target="#myModal"
                                               href="<?= base_url() ?>admin/tasks/update_users/<?= $v_task->task_id ?>"
                                               class="text-default ml"><i class="fa fa-plus"></i></a>
                                                </span>
                                                    <?php } ?>
                                                </td>
                                                <?php $show_custom_fields = custom_form_table(3, $v_task->task_id);
                                                if (!empty($show_custom_fields)) {
                                                    foreach ($show_custom_fields as $c_label => $v_fields) {
                                                        if (!empty($c_label)) {
                                                            ?>
                                                            <td><?= $v_fields ?> </td>
                                                        <?php }
                                                    }
                                                }
                                                ?>

                                                <td>
                                                    <?php if (!empty($can_edit) && !empty($edited)) {
                                                        echo btn_edit('admin/tasks/all_task/' . $v_task->task_id) . ' ';
                                                    } ?>
                                                    <?php if (!empty($can_delete) && !empty($deleted)) {
                                                        echo ajax_anchor(base_url("admin/tasks/delete_task/" . $v_task->task_id), "<i class='btn btn-danger btn-xs fa fa-trash-o'></i>", array("class" => "", "title" => lang('delete'), "data-fade-out-on-success" => "#table-tasks-" . $v_task->task_id));
                                                    } ?>
                                                    <?php

                                                    if ($v_task->timer_status == 'on') { ?>
                                                        <a class="btn btn-xs btn-danger"
                                                           href="<?= base_url() ?>admin/tasks/tasks_timer/off/<?= $v_task->task_id ?>"><?= lang('stop_timer') ?> </a>

                                                    <?php } else { ?>
                                                        <a class="btn btn-xs btn-success"
                                                           href="<?= base_url() ?>admin/tasks/tasks_timer/on/<?= $v_task->task_id ?>"><?= lang('start_timer') ?> </a>
                                                    <?php } ?>
                                                </td>
                                            </tr>
                                            <?php
                                        }endforeach; ?>
                                    <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <?php if (!empty($created) || !empty($edited)) { ?>
                        <!-- Add Stock Category tab Starts -->
                        <div class="tab-pane <?= $active == 2 ? 'active' : '' ?>" id="assign_task"
                             style="position: relative;">
                            <div class="box" style="border: none; padding-top: 15px;" data-collapsed="0">
                                <div class="panel-body">
                                    <form data-parsley-validate="" novalidate=""
                                          action="<?php echo base_url() ?>admin/tasks/save_task/<?php if (!empty($task_info->task_id)) echo $task_info->task_id; ?>"
                                          method="post" class="form-horizontal">
                                        <div class="form-group">
                                            <label class="col-sm-3 control-label"><?= lang('task_name') ?><span
                                                    class="required">*</span></label>
                                            <div class="col-sm-5">
                                                <input type="text" name="task_name" required class="form-control"
                                                       value="<?php if (!empty($task_info->task_name)) echo $task_info->task_name; ?>"/>
                                            </div>
                                        </div>
                                        <?php
                                        if (!empty($task_info->project_id)) {
                                            $project_id = $task_info->project_id;
                                        } elseif (!empty($project_id)) {
                                            $project_id = $project_id; ?>
                                            <input type="hidden" name="un_project_id" required class="form-control"
                                                   value="<?php echo $project_id ?>"/>
                                        <?php }
                                        if (!empty($task_info->opportunities_id)) {
                                            $opportunities_id = $task_info->opportunities_id;
                                        } elseif (!empty($opportunities_id)) {
                                            $opportunities_id = $opportunities_id; ?>
                                            <input type="hidden" name="un_opportunities_id" required
                                                   class="form-control"
                                                   value="<?php echo $opportunities_id ?>"/>
                                        <?php }
                                        if (!empty($task_info->leads_id)) {
                                            $leads_id = $task_info->leads_id;
                                        } elseif (!empty($leads_id)) {
                                            $leads_id = $leads_id; ?>
                                            <input type="hidden" name="un_leads_id" required class="form-control"
                                                   value="<?php echo $leads_id ?>"/>
                                        <?php }
                                        if (!empty($task_info->bug_id)) {
                                            $bug_id = $task_info->bug_id;
                                        } elseif (!empty($bug_id)) {
                                            $bug_id = $bug_id; ?>
                                            <input type="hidden" name="un_bug_id" required class="form-control"
                                                   value="<?php echo $bug_id ?>"/>
                                        <?php }
                                        if (!empty($task_info->goal_tracking_id)) {
                                            $goal_tracking_id = $task_info->goal_tracking_id;
                                        } elseif (!empty($goal_tracking_id)) {
                                            $goal_tracking_id = $goal_tracking_id; ?>
                                            <input type="hidden" name="un_goal_tracking_id" required
                                                   class="form-control"
                                                   value="<?php echo $goal_tracking_id ?>"/>
                                        <?php } ?>
                                        <?php
                                        if (!empty($task_info->sub_task_id)) {
                                            $sub_task_id = $task_info->sub_task_id;
                                        } elseif (!empty($sub_task_id)) {
                                            $sub_task_id = $sub_task_id; ?>
                                            <input type="hidden" name="un_sub_task_id" required
                                                   class="form-control"
                                                   value="<?php echo $sub_task_id ?>"/>
                                        <?php } ?>
                                        <div class="form-group" id="border-none">
                                            <label for="field-1"
                                                   class="col-sm-3 control-label"><?= lang('related_to') ?> </label>
                                            <div class="col-sm-5">
                                                <select name="related_to" class="form-control" id="check_related"
                                                        onchange="get_related_moduleName(this.value)">
                                                    <option
                                                        value="0"> <?= lang('none') ?> </option>
                                                    <option
                                                        value="project" <?= (!empty($project_id) ? 'selected' : '') ?>> <?= lang('project') ?> </option>
                                                    <option
                                                        value="opportunities" <?= (!empty($opportunities_id) ? 'selected' : '') ?>> <?= lang('opportunities') ?> </option>
                                                    <option
                                                        value="leads" <?= (!empty($leads_id) ? 'selected' : '') ?>> <?= lang('leads') ?> </option>
                                                    <option
                                                        value="bug" <?= (!empty($bug_id) ? 'selected' : '') ?>> <?= lang('bugs') ?> </option>
                                                    <option
                                                        value="goal" <?= (!empty($goal_tracking_id) ? 'selected' : '') ?>> <?= lang('goal_tracking') ?> </option>
                                                    <option
                                                        value="sub_task" <?= (!empty($sub_task_id) ? 'selected' : '') ?>> <?= lang('tasks') ?> </option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group" id="related_to">

                                        </div>
                                        <?php if (empty($project_id)) { ?>
                                            <div class="form-group company"
                                                 id="milestone_show">
                                                <label for="field-1"
                                                       class="col-sm-3 control-label"><?= lang('milestones') ?>
                                                    <span class="required">*</span></label>
                                                <div class="col-sm-5">
                                                    <select name="milestones_id" id="milestone"
                                                            class="form-control company">
                                                        <?php
                                                        if (!empty($project_id)) {
                                                            $all_milestones_info = $this->db->where('project_id', $project_id)->get('tbl_milestones')->result();
                                                        } else {
                                                            $project_milestone = $this->db->get('tbl_project')->row();
                                                            $all_milestones_info = $this->db->where('project_id', $project_milestone->project_id)->get('tbl_milestones')->result();
                                                        }
                                                        if (!empty($all_milestones_info)) {
                                                            foreach ($all_milestones_info as $v_milestones) {
                                                                ?>
                                                                <option
                                                                    value="<?= $v_milestones->milestones_id ?>" <?php
                                                                if (!empty($task_info->milestones_id)) {
                                                                    echo $v_milestones->milestones_id == $task_info->milestones_id ? 'selected' : '';
                                                                }
                                                                ?>><?= $v_milestones->milestone_name ?></option>
                                                                <?php
                                                            }
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                        <?php } ?>
                                        <?php

                                        if (!empty($project_id)):
                                            $project_info = $this->db->where('project_id', $project_id)->get('tbl_project')->row();
                                            ?>
                                            <div class="form-group <?= $project_id ? 'project_module' : 'company' ?>">
                                                <label for="field-1"
                                                       class="col-sm-3 control-label"><?= lang('project') ?> <span
                                                        class="required">*</span></label>
                                                <div class="col-sm-5">
                                                    <select name="project_id" style="width: 100%"
                                                            class="select_box <?= $project_id ? 'project_module' : 'company' ?>"
                                                            required="1" onchange="get_milestone_by_id(this.value)">
                                                        <?php
                                                        $all_project = $this->tasks_model->get_permission('tbl_project');
                                                        if (!empty($all_project)) {
                                                            foreach ($all_project as $v_project) {
                                                                ?>
                                                                <option value="<?= $v_project->project_id ?>" <?php
                                                                if (!empty($project_id)) {
                                                                    echo $v_project->project_id == $project_id ? 'selected' : '';
                                                                }
                                                                ?>><?= $v_project->project_name ?></option>
                                                                <?php
                                                            }
                                                        }
                                                        ?>
                                                    </select>

                                                </div>
                                            </div>
                                            <div class="form-group <?= $project_id ? 'milestone_module' : 'company' ?>"
                                                 id="milestone_show">
                                                <label for="field-1"
                                                       class="col-sm-3 control-label"><?= lang('milestones') ?>
                                                    <span class="required">*</span></label>
                                                <div class="col-sm-5">
                                                    <select name="milestones_id" id="milestone"
                                                            class="form-control <?= $project_id ? 'milestone_module' : 'company' ?>">
                                                        <option><?= lang('none') ?></option>
                                                        <?php
                                                        $all_milestones_info = $this->db->where('project_id', $project_id)->get('tbl_milestones')->result();
                                                        if (!empty($all_milestones_info)) {
                                                            foreach ($all_milestones_info as $v_milestones) {
                                                                ?>
                                                                <option
                                                                    value="<?= $v_milestones->milestones_id ?>" <?php
                                                                if (!empty($task_info->milestones_id)) {
                                                                    echo $v_milestones->milestones_id == $task_info->milestones_id ? 'selected' : '';
                                                                }
                                                                ?>><?= $v_milestones->milestone_name ?></option>
                                                                <?php
                                                            }
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                        <?php endif ?>
                                        <?php if (!empty($opportunities_id)): ?>
                                            <div
                                                class="form-group <?= $opportunities_id ? 'opportunities_module' : 'company' ?>"
                                                id="border-none">
                                                <label for="field-1"
                                                       class="col-sm-3 control-label"><?= lang('select') . ' ' . lang('opportunities') ?>
                                                    <span class="required">*</span></label>
                                                <div class="col-sm-5">
                                                    <select name="opportunities_id" style="width: 100%"
                                                            class="select_box <?= $opportunities_id ? 'opportunities_module' : 'company' ?>"
                                                            required="1">
                                                        <?php
                                                        $all_opportunities_info = $this->tasks_model->get_permission('tbl_opportunities');
                                                        if (!empty($all_opportunities_info)) {
                                                            foreach ($all_opportunities_info as $v_opportunities) {
                                                                ?>
                                                                <option
                                                                    value="<?= $v_opportunities->opportunities_id ?>" <?php
                                                                if (!empty($opportunities_id)) {
                                                                    echo $v_opportunities->opportunities_id == $opportunities_id ? 'selected' : '';
                                                                }
                                                                ?>><?= $v_opportunities->opportunity_name ?></option>
                                                                <?php
                                                            }
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                        <?php endif ?>
                                        <?php if (!empty($leads_id)): ?>
                                            <div class="form-group <?= $leads_id ? 'leads_module' : 'company' ?>"
                                                 id="border-none">
                                                <label for="field-1"
                                                       class="col-sm-3 control-label"><?= lang('select') . ' ' . lang('leads') ?>
                                                    <span class="required">*</span></label>
                                                <div class="col-sm-5">
                                                    <select name="leads_id" style="width: 100%"
                                                            class="select_box <?= $leads_id ? 'leads_module' : 'company' ?>"
                                                            required="1">
                                                        <?php
                                                        $all_leads_info = $this->tasks_model->get_permission('tbl_leads');
                                                        if (!empty($all_leads_info)) {
                                                            foreach ($all_leads_info as $v_leads) {
                                                                ?>
                                                                <option value="<?= $v_leads->leads_id ?>" <?php
                                                                if (!empty($leads_id)) {
                                                                    echo $v_leads->leads_id == $leads_id ? 'selected' : '';
                                                                }
                                                                ?>><?= $v_leads->lead_name ?></option>
                                                                <?php
                                                            }
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                        <?php endif ?>

                                        <?php if (!empty($bug_id)): ?>
                                            <div class="form-group <?= $bug_id ? 'bugs_module' : 'company' ?>"
                                                 id="border-none">
                                                <label for="field-1"
                                                       class="col-sm-3 control-label"><?= lang('select') . ' ' . lang('bugs') ?>
                                                    <span class="required">*</span></label>
                                                <div class="col-sm-5">
                                                    <select name="bug_id" style="width: 100%"
                                                            class="select_box <?= $bug_id ? 'bugs_module' : 'company' ?>"
                                                            required="1">
                                                        <?php
                                                        $all_bugs_info = $this->tasks_model->get_permission('tbl_bug');
                                                        if (!empty($all_bugs_info)) {
                                                            foreach ($all_bugs_info as $v_bugs) {
                                                                ?>
                                                                <option value="<?= $v_bugs->bug_id ?>" <?php
                                                                if (!empty($bug_id)) {
                                                                    echo $v_bugs->bug_id == $bug_id ? 'selected' : '';
                                                                }
                                                                ?>><?= $v_bugs->bug_title ?></option>
                                                                <?php
                                                            }
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                        <?php endif ?>
                                        <?php if (!empty($goal_tracking_id)): ?>
                                            <div
                                                class="form-group <?= $goal_tracking_id ? 'goal_tracking' : 'company' ?>"
                                                id="border-none">
                                                <label for="field-1"
                                                       class="col-sm-3 control-label"><?= lang('select') . ' ' . lang('goal_tracking') ?>
                                                    <span class="required">*</span></label>
                                                <div class="col-sm-5">
                                                    <select name="goal_tracking_id" style="width: 100%"
                                                            class="select_box <?= $goal_tracking_id ? 'goal_tracking' : 'company' ?>"
                                                            required="1">
                                                        <?php
                                                        $all_goal_info = $this->tasks_model->get_permission('tbl_goal_tracking');
                                                        if (!empty($all_goal_info)) {
                                                            foreach ($all_goal_info as $v_goal) {
                                                                ?>
                                                                <option value="<?= $v_goal->goal_tracking_id ?>" <?php
                                                                if (!empty($goal_tracking_id)) {
                                                                    echo $v_goal->goal_tracking_id == $goal_tracking_id ? 'selected' : '';
                                                                }
                                                                ?>><?= $v_goal->subject ?></option>
                                                                <?php
                                                            }
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                        <?php endif ?>
                                        <?php if (!empty($sub_task_id)): ?>
                                            <div
                                                class="form-group <?= $sub_task_id ? 'sub_tasks' : 'company' ?>"
                                                id="border-none">
                                                <label for="field-1"
                                                       class="col-sm-3 control-label"><?= lang('select') . ' ' . lang('tasks') ?>
                                                    <span class="required">*</span></label>
                                                <div class="col-sm-5">
                                                    <select name="sub_task_id" style="width: 100%"
                                                            class="select_box <?= $sub_task_id ? 'sub_tasks' : 'company' ?>"
                                                            required="1">
                                                        <?php
                                                        $all_sub_tasks = $this->tasks_model->get_permission('tbl_task');
                                                        if (!empty($all_sub_tasks)) {
                                                            foreach ($all_sub_tasks as $v_s_tasks) {
                                                                ?>
                                                                <option value="<?= $v_s_tasks->task_id ?>" <?php
                                                                if (!empty($sub_task_id)) {
                                                                    echo $v_s_tasks->task_id == $sub_task_id ? 'selected' : '';
                                                                }
                                                                ?>><?= $v_s_tasks->task_name ?></option>
                                                                <?php
                                                            }
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                        <?php endif ?>
                                        <div class="form-group">
                                            <label class="col-lg-3 control-label"><?= lang('start_date') ?></label>
                                            <div class="col-lg-5">
                                                <div class="input-group">
                                                    <input type="text" name="task_start_date"
                                                           class="form-control datepicker"
                                                           value="<?php
                                                           if (!empty($task_info->task_start_date)) {
                                                               echo $task_info->task_start_date;
                                                           } else {
                                                               echo date('Y-m-d');
                                                           }
                                                           ?>"
                                                           data-date-format="<?= config_item('date_picker_format'); ?>">
                                                    <div class="input-group-addon">
                                                        <a href="#"><i class="fa fa-calendar"></i></a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-lg-3 control-label"><?= lang('due_date') ?><span
                                                    class="required">*</span></label>
                                            <div class="col-lg-5">
                                                <div class="input-group">
                                                    <input type="text" name="due_date" required="" value="<?php
                                                    if (!empty($task_info->due_date)) {
                                                        echo $task_info->due_date;
                                                    }
                                                    ?>" class="form-control datepicker" data-format="yyyy-mm-dd">
                                                    <div class="input-group-addon">
                                                        <a href="#"><i class="fa fa-calendar"></i></a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-3 control-label"><?= lang('project_hourly_rate') ?></label>
                                            <div class="col-sm-5">
                                                <input type="text" data-parsley-type="number" name="hourly_rate"
                                                       class="form-control"
                                                       value="<?php if (!empty($task_info->hourly_rate)) echo $task_info->hourly_rate; ?>"/>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-3 control-label"><?= lang('estimated_hour') ?></label>
                                            <div class="col-sm-5">
                                                <input type="number" step="0.01" data-parsley-type="number"
                                                       name="task_hour"
                                                       class="form-control"
                                                       value="<?php
                                                       if (!empty($task_info->task_hour)) {
                                                           $result = explode(':', $task_info->task_hour);
                                                           if (empty($result[1])) {
                                                               $result1 = 0;
                                                           } else {
                                                               $result1 = $result[1];
                                                           }
                                                           echo $result[0] . '.' . $result1;
                                                       }
                                                       ?>"/>
                                            </div>

                                        </div>
                                        <script src="<?= base_url() ?>assets/js/jquery-ui.js"></script>
                                        <?php $direction = $this->session->userdata('direction');
                                        if (!empty($direction) && $direction == 'rtl') {
                                            $RTL = 'on';
                                        } else {
                                            $RTL = config_item('RTL');
                                        }
                                        ?>
                                        <?php
                                        if (!empty($RTL)) { ?>
                                            <!-- bootstrap-editable -->
                                            <script type="text/javascript"
                                                    src="<?= base_url() ?>assets/plugins/jquery-ui/jquery.ui.slider-rtl.js"></script>
                                        <?php }
                                        ?>
                                        <style>

                                            .ui-widget.ui-widget-content {
                                                border: 1px solid #dde6e9;
                                            }

                                            .ui-corner-all, .ui-corner-bottom, .ui-corner-left, .ui-corner-bl {
                                                border: 7px solid #28a9f1;
                                            }

                                            .ui-widget-content {
                                                border: 1px solid #dddddd;
                                                /*background: #E1E4E9;*/
                                                color: #333333;
                                            }

                                            .ui-slider {
                                                position: relative;
                                                text-align: left;
                                            }

                                            .ui-slider-horizontal {
                                                height: 1em;
                                            }

                                            .ui-state-default, .ui-widget-content .ui-state-default, .ui-widget-header .ui-state-default, .ui-button, html .ui-button.ui-state-disabled:hover, html .ui-button.ui-state-disabled:active {
                                                border: 1px solid #1797be;
                                                background: #1797be;
                                                font-weight: normal;
                                                color: #454545;
                                            }

                                            .ui-slider-horizontal .ui-slider-handle {
                                                top: -.3em;
                                                margin-left: -.1em;;
                                                margin-right: -.1em;;
                                            }

                                            .ui-slider .ui-slider-handle:hover {
                                                background: #1797be;
                                            }

                                            .ui-slider .ui-slider-handle {
                                                position: absolute;
                                                z-index: 2;
                                                width: 1.2em;;
                                                height: 1.5em;
                                                cursor: default;
                                                -ms-touch-action: none;
                                                touch-action: none;

                                            }

                                            .ui-state-disabled, .ui-widget-content .ui-state-disabled, .ui-widget-header .ui-state-disabled {
                                                opacity: .35;
                                                filter: Alpha(Opacity=35);
                                                background-image: none;
                                            }

                                            .ui-state-disabled {
                                                cursor: default !important;
                                                pointer-events: none;
                                            }

                                            .ui-slider.ui-state-disabled .ui-slider-handle, .ui-slider.ui-state-disabled .ui-slider-range {
                                                filter: inherit;
                                            }

                                            .ui-slider-range, .ui-widget-header, .ui-slider-handle:before, .list-group-item.active, .list-group-item.active:hover, .list-group-item.active:focus, .icon-frame {
                                                background-image: none;
                                                background: #28a9f1;
                                            }

                                        </style>
                                        <?php
                                        if (!empty($task_info)) {
                                            $value = $this->tasks_model->get_task_progress($task_info->task_id);
                                        } else {
                                            $value = 0;
                                        }
                                        ?>
                                        <div class="form-group">
                                            <label
                                                class="col-lg-3 control-label"><?php echo lang('progress'); ?> </label>
                                            <div class="col-lg-5">
                                                <?php echo form_hidden('task_progress', $value); ?>
                                                <div
                                                    class="project_progress_slider project_progress_slider_horizontal mbot15"></div>

                                                <div class="input-group">
                                <span class="input-group-addon">
                                     <div class="">
                                         <div class="pull-left mt">
                                             <?php echo lang('progress'); ?>
                                             <span class="label_progress "><?php echo $value; ?>%</span>
                                         </div>
                                         <div class="checkbox c-checkbox pull-right" data-toggle="tooltip"
                                              data-placement="top"
                                              title="<?php echo lang('calculate_progress_through_sub_tasks'); ?>">
                                             <label class="needsclick">
                                                 <input class="select_one"
                                                        type="checkbox" <?php if ((!empty($task_info) && $task_info->calculate_progress == 'through_sub_tasks')) {
                                                     echo 'checked';
                                                 } ?> name="calculate_progress" value="through_sub_tasks"
                                                        id="through_sub_tasks">
                                                 <span class="fa fa-check"></span>
                                                 <small><?php echo lang('through_sub_tasks'); ?></small>
                                             </label>
                                         </div>
                                         <div class="checkbox c-checkbox pull-right" data-toggle="tooltip"
                                              data-placement="top"
                                              title="<?php echo lang('calculate_progress_through_task_hours'); ?>">
                                             <label class="needsclick">
                                                 <input class="select_one"
                                                        type="checkbox" <?php if ((!empty($task_info) && $task_info->calculate_progress == 'through_tasks_hours')) {
                                                     echo 'checked';
                                                 } ?> name="calculate_progress" value="through_tasks_hours"
                                                        id="through_tasks_hours">
                                                 <span class="fa fa-check"></span>
                                                 <small><?php echo lang('through_tasks_hours'); ?></small>
                                             </label>
                                         </div>
                                     </div>
                                </span>
                                                </div>
                                            </div>
                                        </div>
                                        <script>
                                            $(document).ready(function () {
                                                var progress_input = $('input[name="task_progress"]');
                                                <?php if ((!empty($project_info) && $project_info->calculate_progress == 'through_tasks_hours')) {?>
                                                var progress_from_tasks = $('#through_tasks_hours');
                                                <?php }elseif ((!empty($project_info) && $project_info->calculate_progress == 'through_sub_tasks')){?>
                                                var progress_from_tasks = $('#through_sub_tasks');
                                                <?php }else{?>
                                                var progress_from_tasks = $('.select_one');
                                                <?php } ?>

                                                var progress = progress_input.val();
                                                $('.project_progress_slider').slider({
                                                    range: "min",
                                                    <?php
                                                    if (!empty($RTL)) { ?>
                                                    isRTL: true,
                                                    <?php }
                                                    ?>
                                                    min: 0,
                                                    max: 100,
                                                    value: progress,
                                                    disabled: progress_from_tasks.prop('checked'),
                                                    slide: function (event, ui) {
                                                        progress_input.val(ui.value);
                                                        $('.label_progress').html(ui.value + '%');
                                                    }
                                                });
                                                progress_from_tasks.on('change', function () {
                                                    var _checked = $(this).prop('checked');
                                                    $('.project_progress_slider').slider({
                                                        disabled: _checked,
                                                    });
                                                });
                                            })
                                            ;
                                        </script>
                                        <div class="form-group" id="border-none">
                                            <label for="field-1"
                                                   class="col-sm-3 control-label"><?= lang('task_status') ?> <span
                                                    class="required">*</span></label>
                                            <div class="col-sm-5">
                                                <select name="task_status" class="form-control" required>
                                                    <option
                                                        value="not_started" <?= (!empty($task_info->task_status) && $task_info->task_status == 'not_started' ? 'selected' : '') ?>> <?= lang('not_started') ?> </option>
                                                    <option
                                                        value="in_progress" <?= (!empty($task_info->task_status) && $task_info->task_status == 'in_progress' ? 'selected' : '') ?>> <?= lang('in_progress') ?> </option>
                                                    <option
                                                        value="completed" <?= (!empty($task_info->task_status) && $task_info->task_status == 'completed' ? 'selected' : '') ?>> <?= lang('completed') ?> </option>
                                                    <option
                                                        value="deferred" <?= (!empty($task_info->task_status) && $task_info->task_status == 'deferred' ? 'selected' : '') ?>> <?= lang('deferred') ?> </option>
                                                    <option
                                                        value="waiting_for_someone" <?= (!empty($task_info->task_status) && $task_info->task_status == 'waiting_for_someone' ? 'selected' : '') ?>> <?= lang('waiting_for_someone') ?> </option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="field-1"
                                                   class="col-sm-3 control-label"><?= lang('task_description') ?>
                                            </label>
                                            <div class="col-sm-8">
                                        <textarea class="form-control textarea"
                                                  name="task_description"><?php if (!empty($task_info->task_description)) echo $task_info->task_description; ?></textarea>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="field-1"
                                                   class="col-sm-3 control-label"><?= lang('billable') ?>
                                                <span class="required">*</span></label>
                                            <div class="col-sm-8">
                                                <input data-toggle="toggle" name="billable" value="Yes" <?php
                                                if (!empty($task_info) && $task_info->billable == 'Yes') {
                                                    echo 'checked';
                                                }
                                                ?> data-on="<?= lang('yes') ?>" data-off="<?= lang('no') ?>"
                                                       data-onstyle="success" data-offstyle="danger" type="checkbox">
                                            </div>
                                        </div>
                                        <?php if (!empty($project_id)): ?>
                                            <div class="form-group">
                                                <label for="field-1"
                                                       class="col-sm-3 control-label"><?= lang('visible_to_client') ?>
                                                    <span class="required">*</span></label>
                                                <div class="col-sm-8">
                                                    <input data-toggle="toggle" name="client_visible" value="Yes" <?php
                                                    if (!empty($task_info) && $task_info->client_visible == 'Yes') {
                                                        echo 'checked';
                                                    }
                                                    ?> data-on="<?= lang('yes') ?>" data-off="<?= lang('no') ?>"
                                                           data-onstyle="success" data-offstyle="danger"
                                                           type="checkbox">
                                                </div>
                                            </div>
                                        <?php endif ?>

                                        <?php
                                        if (!empty($task_info)) {
                                            $task_id = $task_info->task_id;
                                        } else {
                                            $task_id = null;
                                        }
                                        ?>
                                        <?= custom_form_Fields(3, $task_id); ?>

                                        <div class="form-group" id="border-none">
                                            <label for="field-1"
                                                   class="col-sm-3 control-label"><?= lang('assined_to') ?> <span
                                                    class="required">*</span></label>
                                            <div class="col-sm-9">
                                                <div class="checkbox c-radio needsclick">
                                                    <label class="needsclick">
                                                        <input id="" <?php
                                                        if (!empty($task_info->permission) && $task_info->permission == 'all') {
                                                            echo 'checked';
                                                        } elseif (empty($task_info)) {
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
                                                        if (!empty($task_info->permission) && $task_info->permission != 'all') {
                                                            echo 'checked';
                                                        }
                                                        ?> type="radio" name="permission" value="custom_permission"
                                                        >
                                                        <span
                                                            class="fa fa-circle"></span><?= lang('custom_permission') ?>
                                                        <i
                                                            title="<?= lang('permission_for_customization') ?>"
                                                            class="fa fa-question-circle" data-toggle="tooltip"
                                                            data-placement="top"></i>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group <?php
                                        if (!empty($task_info->permission) && $task_info->permission != 'all') {
                                            echo 'show';
                                        }
                                        ?>" id="permission_user_1">
                                            <label for="field-1"
                                                   class="col-sm-3 control-label"><?= lang('select') . ' ' . lang('users') ?>
                                                <span
                                                    class="required">*</span></label>
                                            <div class="col-sm-9">
                                                <?php
                                                if (!empty($assign_user)) {
                                                    foreach ($assign_user as $key => $v_user) {

                                                        if ($v_user->role_id == 1) {
                                                            $disable = true;
                                                            $role = '<strong class="badge btn-danger">' . lang('admin') . '</strong>';
                                                        } else {
                                                            $disable = false;
                                                            $role = '<strong class="badge btn-primary">' . lang('staff') . '</strong>';
                                                        }

                                                        ?>
                                                        <div class="checkbox c-checkbox needsclick">
                                                            <label class="needsclick">
                                                                <input type="checkbox"
                                                                    <?php
                                                                    if (!empty($task_info->permission) && $task_info->permission != 'all') {
                                                                        $get_permission = json_decode($task_info->permission);
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

                                                        if (!empty($task_info->permission) && $task_info->permission != 'all') {
                                                            $get_permission = json_decode($task_info->permission);

                                                            foreach ($get_permission as $user_id => $v_permission) {
                                                                if ($user_id == $v_user->user_id) {
                                                                    echo 'show';
                                                                }
                                                            }

                                                        }
                                                        ?>
                                                " id="action_1<?= $v_user->user_id ?>">
                                                            <label class="checkbox-inline c-checkbox">
                                                                <input id="<?= $v_user->user_id ?>" checked
                                                                       type="checkbox"
                                                                       name="action_1<?= $v_user->user_id ?>[]"
                                                                       disabled
                                                                       value="view">
                                                        <span
                                                            class="fa fa-check"></span><?= lang('can') . ' ' . lang('view') ?>
                                                            </label>
                                                            <label class="checkbox-inline c-checkbox">
                                                                <input <?php if (!empty($disable)) {
                                                                    echo 'disabled' . ' ' . 'checked';
                                                                } ?> id="<?= $v_user->user_id ?>"
                                                                    <?php

                                                                    if (!empty($task_info->permission) && $task_info->permission != 'all') {
                                                                        $get_permission = json_decode($task_info->permission);

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
                                                                     value="edit"
                                                                     name="action_<?= $v_user->user_id ?>[]">
                                                        <span
                                                            class="fa fa-check"></span><?= lang('can') . ' ' . lang('edit') ?>
                                                            </label>
                                                            <label class="checkbox-inline c-checkbox">
                                                                <input <?php if (!empty($disable)) {
                                                                    echo 'disabled' . ' ' . 'checked';
                                                                } ?> id="<?= $v_user->user_id ?>"
                                                                    <?php

                                                                    if (!empty($task_info->permission) && $task_info->permission != 'all') {
                                                                        $get_permission = json_decode($task_info->permission);
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
                                            <label for="field-1" class="col-sm-3 control-label"></label>
                                            <div class="col-sm-8">
                                                <button type="submit" id="sbtn"
                                                        class="btn btn-primary"><?= lang('save') ?></button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                    <div class="tab-pane <?= $active == 4 ? 'active' : ''; ?>" id="not_started">

                        <div class="table-responsive">
                            <table class="table table-striped DataTables " id="DataTables" cellspacing="0" width="100%">
                                <thead>
                                <tr>
                                    <?php if (!empty($created) || !empty($edited)) { ?>
                                        <th data-check-all>

                                        </th>
                                    <?php } ?>
                                    <th class="col-sm-3"><?= lang('task_name') ?></th>
                                    <th class="col-sm-2"><?= lang('due_date') ?></th>
                                    <th class="col-sm-1"><?= lang('status') ?></th>
                                    <th class="col-sm-1"><?= lang('progress') ?></th>
                                    <th class="col-sm-2"><?= lang('assigned_to') ?></th>
                                    <th class="col-sm-3"><?= lang('changes/view') ?></th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                if (!empty($all_task_info)):foreach ($all_task_info as $key => $v_task):
                                    if ($v_task->task_status == 'not_started') {
                                        $can_edit = $this->tasks_model->can_action('tbl_task', 'edit', array('task_id' => $v_task->task_id));
                                        $can_delete = $this->tasks_model->can_action('tbl_task', 'delete', array('task_id' => $v_task->task_id));
                                        ?>
                                        <tr>
                                            <td class="col-sm-1">
                                                <div class="complete checkbox c-checkbox">
                                                    <label>
                                                        <input type="checkbox" data-id="<?= $v_task->task_id ?>"
                                                               style="position: absolute;" <?php
                                                        if ($v_task->task_progress >= 100) {
                                                            echo 'checked';
                                                        }
                                                        ?>>
                                                        <span class="fa fa-check"></span>
                                                    </label>
                                                </div>
                                            </td>
                                            <td>
                                                <a style="<?php
                                                if ($v_task->task_progress >= 100) {
                                                    echo 'text-decoration: line-through;';
                                                }
                                                ?>"
                                                   href="<?= base_url() ?>admin/tasks/view_task_details/<?= $v_task->task_id ?>"><?php echo $v_task->task_name; ?></a>
                                            </td>
                                            <td><?php
                                                $due_date = $v_task->due_date;
                                                $due_time = strtotime($due_date);
                                                $current_time = time();
                                                if ($v_task->task_progress == 100) {
                                                    $c_progress = 100;
                                                } elseif ($v_task->task_status == 'completed') {
                                                    $c_progress = 100;
                                                } else {
                                                    $c_progress = 0;
                                                }
                                                ?>
                                                <?= strftime(config_item('date_format'), strtotime($due_date)) ?>
                                                <?php if ($current_time > $due_time && $c_progress < 100) { ?>
                                                    <span class="label label-danger"><?= lang('overdue') ?></span>
                                                <?php } ?></td>
                                            <td>
                                                <?php
                                                if ($v_task->task_status == 'completed') {
                                                    $label = 'success';
                                                } elseif ($v_task->task_status == 'not_started') {
                                                    $label = 'info';
                                                } elseif ($v_task->task_status == 'deferred') {
                                                    $label = 'danger';
                                                } else {
                                                    $label = 'warning';
                                                }
                                                ?>
                                                <span
                                                    class="label label-<?= $label ?>"><?= lang($v_task->task_status) ?> </span>
                                            </td>
                                            <td class="col-sm-1" style="padding-bottom: 0px;padding-top: 3px">

                                                <div class="inline ">
                                                    <div class="easypiechart text-success" style="margin: 0px;"
                                                         data-percent="<?= $v_task->task_progress ?>"
                                                         data-line-width="5" data-track-Color="#f0f0f0"
                                                         data-bar-color="#<?php
                                                         if ($v_task->task_progress == 100) {
                                                             echo '8ec165';
                                                         } else {
                                                             echo 'fb6b5b';
                                                         }
                                                         ?>" data-rotate="270" data-scale-Color="false"
                                                         data-size="50"
                                                         data-animate="2000">
                                                        <span class="small "><?= $v_task->task_progress ?>
                                                            %</span>
                                                    </div>
                                                </div>
                                            </td>

                                            <td>
                                                <?php
                                                if ($v_task->permission != 'all') {
                                                    $get_permission = json_decode($v_task->permission);
                                                    if (!empty($get_permission)) :
                                                        foreach ($get_permission as $permission => $v_permission) :
                                                            $user_info = $this->db->where(array('user_id' => $permission))->get('tbl_users')->row();
                                                            if ($user_info->role_id == 1) {
                                                                $label = 'circle-danger';
                                                            } else {
                                                                $label = 'circle-success';
                                                            }
                                                            $profile_info = $this->db->where(array('user_id' => $permission))->get('tbl_account_details')->row();
                                                            ?>

                                                            <a href="#" data-toggle="tooltip" data-placement="top"
                                                               title="<?= $profile_info->fullname ?>"><img
                                                                    src="<?= base_url() . $profile_info->avatar ?>"
                                                                    class="img-circle img-xs" alt="">
                                                <span style="margin: 0px 0 8px -10px;"
                                                      class="circle <?= $label ?>  circle-lg"></span>
                                                            </a>

                                                            <?php
                                                        endforeach;
                                                    endif;
                                                } else { ?>
                                                    <strong><?= lang('everyone') ?></strong>
                                                    <i
                                                        title="<?= lang('permission_for_all') ?>"
                                                        class="fa fa-question-circle" data-toggle="tooltip"
                                                        data-placement="top"></i>
                                                    <?php
                                                }
                                                ?>
                                                <?php if (!empty($can_edit) && !empty($edited)) { ?>
                                                    <span data-placement="top" data-toggle="tooltip"
                                                          title="<?= lang('add_more') ?>">
                                            <a data-toggle="modal" data-target="#myModal"
                                               href="<?= base_url() ?>admin/tasks/update_users/<?= $v_task->task_id ?>"
                                               class="text-default ml"><i class="fa fa-plus"></i></a>
                                                </span>
                                                <?php } ?>
                                            </td>

                                            <td>
                                                <?php if (!empty($can_edit) && !empty($edited)) {
                                                    echo btn_edit('admin/tasks/all_task/' . $v_task->task_id) . ' ';
                                                } ?>
                                                <?php if (!empty($can_delete) && !empty($deleted)) {
                                                    echo ajax_anchor(base_url("admin/tasks/delete_task/" . $v_task->task_id), "<i class='btn btn-danger btn-xs fa fa-trash-o'></i>", array("class" => "", "title" => lang('delete'), "data-fade-out-on-success" => "#table-tasks-" . $v_task->task_id)) . ' ';
                                                } ?>
                                                <?php

                                                if ($v_task->timer_status == 'on') { ?>
                                                    <a class="btn btn-xs btn-danger"
                                                       href="<?= base_url() ?>admin/tasks/tasks_timer/off/<?= $v_task->task_id ?>"><?= lang('stop_timer') ?> </a>

                                                <?php } else { ?>
                                                    <a class="btn btn-xs btn-success"
                                                       href="<?= base_url() ?>admin/tasks/tasks_timer/on/<?= $v_task->task_id ?>"><?= lang('start_timer') ?> </a>
                                                <?php } ?>
                                            </td>
                                        </tr>
                                        <?php
                                    }
                                endforeach; ?>
                                <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="tab-pane <?= $active == 3 ? 'active' : ''; ?>" id="archived">

                        <div class="table-responsive">
                            <table class="table table-striped DataTables " id="DataTables" cellspacing="0" width="100%">
                                <thead>
                                <tr>
                                    <?php if (!empty($created) || !empty($edited)) { ?>
                                        <th data-check-all>

                                        </th>
                                    <?php } ?>
                                    <th class="col-sm-3"><?= lang('task_name') ?></th>
                                    <th class="col-sm-2"><?= lang('due_date') ?></th>
                                    <th class="col-sm-1"><?= lang('status') ?></th>
                                    <th class="col-sm-1"><?= lang('progress') ?></th>
                                    <th class="col-sm-2"><?= lang('assigned_to') ?></th>
                                    <th class="col-sm-3"><?= lang('changes/view') ?></th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                if (!empty($all_task_info)):foreach ($all_task_info as $key => $v_task):
                                    if ($v_task->task_status == 'completed') {
                                        $can_edit = $this->tasks_model->can_action('tbl_task', 'edit', array('task_id' => $v_task->task_id));
                                        $can_delete = $this->tasks_model->can_action('tbl_task', 'delete', array('task_id' => $v_task->task_id));
                                        ?>
                                        <tr>
                                            <td class="col-sm-1">
                                                <div class="complete checkbox c-checkbox">
                                                    <label>
                                                        <input type="checkbox" data-id="<?= $v_task->task_id ?>"
                                                               style="position: absolute;" <?php
                                                        if ($v_task->task_progress >= 100) {
                                                            echo 'checked';
                                                        }
                                                        ?>>
                                                        <span class="fa fa-check"></span>
                                                    </label>
                                                </div>
                                            </td>
                                            <td>
                                                <a style="<?php
                                                if ($v_task->task_progress >= 100) {
                                                    echo 'text-decoration: line-through;';
                                                }
                                                ?>"
                                                   href="<?= base_url() ?>admin/tasks/view_task_details/<?= $v_task->task_id ?>"><?php echo $v_task->task_name; ?></a>
                                            </td>
                                            <td><?php
                                                $due_date = $v_task->due_date;
                                                $due_time = strtotime($due_date);
                                                $current_time = time();
                                                if ($v_task->task_progress == 100) {
                                                    $c_progress = 100;
                                                } elseif ($v_task->task_status == 'completed') {
                                                    $c_progress = 100;
                                                } else {
                                                    $c_progress = 0;
                                                }
                                                ?>
                                                <?= strftime(config_item('date_format'), strtotime($due_date)) ?>
                                                <?php if ($current_time > $due_time && $c_progress < 100) { ?>
                                                    <span class="label label-danger"><?= lang('overdue') ?></span>
                                                <?php } ?></td>
                                            <td>
                                                <?php
                                                if ($v_task->task_status == 'completed') {
                                                    $label = 'success';
                                                } elseif ($v_task->task_status == 'not_started') {
                                                    $label = 'info';
                                                } elseif ($v_task->task_status == 'deferred') {
                                                    $label = 'danger';
                                                } else {
                                                    $label = 'warning';
                                                }
                                                ?>
                                                <span
                                                    class="label label-<?= $label ?>"><?= lang($v_task->task_status) ?> </span>
                                            </td>
                                            <td class="col-sm-1" style="padding-bottom: 0px;padding-top: 3px">

                                                <div class="inline ">
                                                    <div class="easypiechart text-success" style="margin: 0px;"
                                                         data-percent="<?= $v_task->task_progress ?>"
                                                         data-line-width="5" data-track-Color="#f0f0f0"
                                                         data-bar-color="#<?php
                                                         if ($v_task->task_progress == 100) {
                                                             echo '8ec165';
                                                         } else {
                                                             echo 'fb6b5b';
                                                         }
                                                         ?>" data-rotate="270" data-scale-Color="false"
                                                         data-size="50"
                                                         data-animate="2000">
                                                        <span class="small "><?= $v_task->task_progress ?>
                                                            %</span>
                                                    </div>
                                                </div>
                                            </td>

                                            <td>
                                                <?php
                                                if ($v_task->permission != 'all') {
                                                    $get_permission = json_decode($v_task->permission);
                                                    if (!empty($get_permission)) :
                                                        foreach ($get_permission as $permission => $v_permission) :
                                                            $user_info = $this->db->where(array('user_id' => $permission))->get('tbl_users')->row();
                                                            if ($user_info->role_id == 1) {
                                                                $label = 'circle-danger';
                                                            } else {
                                                                $label = 'circle-success';
                                                            }
                                                            $profile_info = $this->db->where(array('user_id' => $permission))->get('tbl_account_details')->row();
                                                            ?>

                                                            <a href="#" data-toggle="tooltip" data-placement="top"
                                                               title="<?= $profile_info->fullname ?>"><img
                                                                    src="<?= base_url() . $profile_info->avatar ?>"
                                                                    class="img-circle img-xs" alt="">
                                                <span style="margin: 0px 0 8px -10px;"
                                                      class="circle <?= $label ?>  circle-lg"></span>
                                                            </a>

                                                            <?php
                                                        endforeach;
                                                    endif;
                                                } else { ?>
                                                    <strong><?= lang('everyone') ?></strong>
                                                    <i
                                                        title="<?= lang('permission_for_all') ?>"
                                                        class="fa fa-question-circle" data-toggle="tooltip"
                                                        data-placement="top"></i>
                                                    <?php
                                                }
                                                ?>
                                                <?php if (!empty($can_edit) && !empty($edited)) { ?>
                                                    <span data-placement="top" data-toggle="tooltip"
                                                          title="<?= lang('add_more') ?>">
                                            <a data-toggle="modal" data-target="#myModal"
                                               href="<?= base_url() ?>admin/tasks/update_users/<?= $v_task->task_id ?>"
                                               class="text-default ml"><i class="fa fa-plus"></i></a>
                                                </span>
                                                <?php } ?>
                                            </td>

                                            <td>
                                                <?php if (!empty($can_edit) && !empty($edited)) {
                                                    echo btn_edit('admin/tasks/all_task/' . $v_task->task_id) . ' ';
                                                } ?>
                                                <?php if (!empty($can_delete) && !empty($deleted)) {
                                                    echo ajax_anchor(base_url("admin/tasks/delete_task/" . $v_task->task_id), "<i class='btn btn-danger btn-xs fa fa-trash-o'></i>", array("class" => "", "title" => lang('delete'), "data-fade-out-on-success" => "#table-tasks-" . $v_task->task_id)) . ' ';
                                                } ?>
                                                <?php

                                                if ($v_task->timer_status == 'on') { ?>
                                                    <a class="btn btn-xs btn-danger"
                                                       href="<?= base_url() ?>admin/tasks/tasks_timer/off/<?= $v_task->task_id ?>"><?= lang('stop_timer') ?> </a>

                                                <?php } else { ?>
                                                    <a class="btn btn-xs btn-success"
                                                       href="<?= base_url() ?>admin/tasks/tasks_timer/on/<?= $v_task->task_id ?>"><?= lang('start_timer') ?> </a>
                                                <?php } ?>
                                            </td>
                                        </tr>
                                        <?php
                                    }
                                endforeach; ?>
                                <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>
</div>
