<link href="<?php echo base_url() ?>assets/plugins/bootstrap-slider/bootstrap-slider.min.css" rel="stylesheet">
<?php echo message_box('success'); ?>
<?php echo message_box('error'); ?>
<?php include_once 'assets/admin-ajax.php'; ?>
<?php
$mdate = date('Y-m-d');
$last_7_days = date('Y-m-d', strtotime('today - 7 days'));
$all_goal_tracking = $this->bugs_model->get_permission('tbl_goal_tracking');

$all_goal = 0;
$bank_goal = 0;
$complete_achivement = 0;
if (!empty($all_goal_tracking)) {
    foreach ($all_goal_tracking as $v_goal_track) {
        $goal_achieve = $this->bugs_model->get_progress($v_goal_track, true);

        if ($v_goal_track->goal_type_id == 9) {

            if ($v_goal_track->end_date <= $mdate) { // check today is last date or not

                if ($v_goal_track->email_send == 'no') {// check mail are send or not
                    if ($v_goal_track->achievement <= $goal_achieve['achievement']) {
                        if ($v_goal_track->notify_goal_achive == 'on') {// check is notify is checked or not check
                            $this->bugs_model->send_goal_mail('goal_achieve', $v_goal_track);
                        }
                    } else {
                        if ($v_goal_track->notify_goal_not_achive == 'on') {// check is notify is checked or not check
                            $this->bugs_model->send_goal_mail('goal_not_achieve', $v_goal_track);
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

for ($iDay = 7; $iDay >= 0; $iDay--) {
    $date = date('Y-m-d', strtotime('today - ' . $iDay . 'days'));
    $where = array('update_time >=' => $date . " 00:00:00", 'update_time <=' => $date . " 23:59:59", 'bug_status' => 'resolved');

    $invoice_result[$date] = count($this->db->where($where)->get('tbl_bug')->result());
}

$terget_achievement = $this->db->where(array('goal_type_id' => 9, 'start_date >=' => $last_7_days, 'end_date <=' => $mdate))->get('tbl_goal_tracking')->result();

$total_terget = 0;
if (!empty($terget_achievement)) {
    foreach ($terget_achievement as $v_terget) {
        $total_terget += $v_terget->achievement;
    }
}
$tolal_goal = $all_goal + $bank_goal;
$curency = $this->bugs_model->check_by(array('code' => config_item('default_currency')), 'tbl_currencies');

if ($this->session->userdata('user_type') == 1) {
    $margin = 'margin-bottom:30px';
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
                    <p class="m0 lead"><?= ($total_terget) ?></p>
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
<?php }
$created = can_action('58', 'created');
$edited = can_action('58', 'edited');
$deleted = can_action('58', 'deleted');
if (!empty($created) || !empty($edited)){
?>
<div class="row">
    <div class="col-sm-12">
        <div class="nav-tabs-custom">
            <!-- Tabs within a box -->
            <ul class="nav nav-tabs customtab">
                <li class="nav-item <?= $active == 1 ? 'active' : '' ?>"><a href="#task_list"  class="nav-link" 
                                                                   data-toggle="tab"><?= lang('all_bugs') ?></a></li>
                <li class="<?= $active == 2 ? 'active' : '' ?> nav-item"><a href="#assign_task" class="nav-link"
                                                                   data-toggle="tab"><?= lang('new_bugs') ?></a></li>
            </ul>
            <div class="tab-content bg-white">
                <!-- Stock Category List tab Starts -->
                <div class="tab-pane <?= $active == 1 ? 'active' : '' ?>" id="task_list" style="position: relative;">
                    <?php } else { ?>
                    <div class="panel panel-custom">
                        <header class="panel-heading ">
                            <div class="panel-title"><strong><?= lang('all_bugs') ?></strong></div>
                        </header>
                        <?php } ?>
                        <div class="box" style="border: none; padding-top: 15px;" data-collapsed="0">
                            <div class="box-body">
                                <!-- Table -->
                                <table class="table table-striped DataTables " id="DataTables" cellspacing="0" width="100%">
                                    <thead>
                                    <tr>
                                        <th><?= lang('bug_title') ?></th>
                                        <th><?= lang('status') ?></th>
                                        <th><?= lang('priority') ?></th>
                                        <?php if ($this->session->userdata('user_type') == '1') { ?>
                                            <th><?= lang('reporter') ?></th>
                                        <?php } ?>
                                        <th><?= lang('assigned_to') ?></th>
                                        <?php $show_custom_fields = custom_form_table(6, null);
                                        if (!empty($show_custom_fields)) {
                                            foreach ($show_custom_fields as $c_label => $v_fields) {
                                                if (!empty($c_label)) {
                                                    ?>
                                                    <th><?= $c_label ?> </th>
                                                <?php }
                                            }
                                        }
                                        ?>
                                        <?php if (!empty($edited) || !empty($deleted)) { ?>
                                            <th><?= lang('action') ?></th>
                                        <?php } ?>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php if (!empty($all_bugs_info)):foreach ($all_bugs_info as $key => $v_bugs):
                                        $can_edit = $this->bugs_model->can_action('tbl_bug', 'edit', array('bug_id' => $v_bugs->bug_id));
                                        $can_delete = $this->bugs_model->can_action('tbl_bug', 'delete', array('bug_id' => $v_bugs->bug_id));
                                        $reporter = $this->db->where('user_id', $v_bugs->reporter)->get('tbl_users')->row();
                                        $user_info = $this->db->where('user_id', $v_bugs->reporter)->get('tbl_account_details')->row();

                                        if ($reporter->role_id == '1') {
                                            $badge = 'danger';
                                        } elseif ($reporter->role_id == '2') {
                                            $badge = 'info';
                                        } else {
                                            $badge = 'primary';
                                        }
                                        ?>
                                        <tr id="table-bugs-<?= $v_bugs->bug_id?>">
                                            <td><a class="text-info" style="<?php
                                                if ($v_bugs->bug_status == 'resolve') {
                                                    echo 'text-decoration: line-through;';
                                                }
                                                ?>"
                                                   href="<?= base_url() ?>admin/bugs/view_bug_details/<?= $v_bugs->bug_id ?>"><?php echo $v_bugs->bug_title; ?></a>
                                            </td>
                                            </td>
                                            <td><?php
                                                if ($v_bugs->bug_status == 'unconfirmed') {
                                                    $label = 'warning';
                                                } elseif ($v_bugs->bug_status == 'confirmed') {
                                                    $label = 'info';
                                                } elseif ($v_bugs->bug_status == 'in_progress') {
                                                    $label = 'primary';
                                                } else {
                                                    $label = 'success';
                                                }
                                                ?>
                                                <span
                                                    class="label label-<?= $label ?>"><?= lang("$v_bugs->bug_status") ?></span>
                                            </td>
                                            <td>
                                                <?php
                                                if ($v_bugs->priority == 'High') {
                                                    $plabel = 'danger';
                                                } elseif ($v_bugs->priority == 'Medium') {
                                                    $plabel = 'info';
                                                } else {
                                                    $plabel = 'primary';
                                                }
                                                ?>
                                                <span
                                                    class="badge btn-<?= $plabel ?>"><?= ucfirst($v_bugs->priority) ?></span>
                                            </td>
                                            <?php if ($this->session->userdata('user_type') == '1') { ?>
                                                <td>
                                                    <a href="<?= base_url() ?>admin/user/user_details/<?= $reporter->user_id ?>"> <span
                                                            class="badge btn-<?= $badge ?> "><?= $user_info->fullname ?></span></a>
                                                </td>
                                            <?php } ?>
                                            <td>
                                                <?php

                                                if ($v_bugs->permission != 'all') {
                                                    $get_permission = json_decode($v_bugs->permission);

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
                                               href="<?= base_url() ?>admin/bugs/update_users/<?= $v_bugs->bug_id ?>"
                                               class="text-default ml"><i class="fa fa-plus"></i></a>
                                                </span>
                                                <?php } ?>
                                            </td>
                                            <?php $show_custom_fields = custom_form_table(6, $v_bugs->bug_id);
                                            if (!empty($show_custom_fields)) {
                                                foreach ($show_custom_fields as $c_label => $v_fields) {
                                                    if (!empty($c_label)) {
                                                        ?>
                                                        <td><?= $v_fields ?> </td>
                                                    <?php }
                                                }
                                            }
                                            ?>
                                            <?php if (!empty($edited) || !empty($deleted)) { ?>
                                                <td>
                                                    <?php if (!empty($can_edit) && !empty($edited)) { ?>
                                                        <?php echo btn_edit('admin/bugs/index/' . $v_bugs->bug_id) ?>
                                                    <?php }
                                                    if (!empty($can_delete) && !empty($deleted)) { ?>
                                                        <?php echo ajax_anchor(base_url("admin/bugs/delete_bug/" . $v_bugs->bug_id), "<i class='btn btn-danger btn-xs fa fa-trash-o'></i>", array("class" => "", "title" => lang('delete'), "data-fade-out-on-success" => "#table-bugs-" . $v_bugs->bug_id)); ?>
                                                    <?php } ?>
                                                    <?php if (!empty($can_edit) && !empty($edited)) { ?>
                                                        <div class="btn-group">
                                                            <button class="btn btn-xs btn-success dropdown-toggle"
                                                                    data-toggle="dropdown">
                                                                <?= lang('change_status') ?>
                                                                <span class="caret"></span>
                                                                </button>
                                                            <ul class="dropdown-menu animated zoomIn">

                                                                <li>
                                                                    <a href="<?= base_url() ?>admin/bugs/change_status/<?= $v_bugs->bug_id ?>/unconfirmed"><?= lang('unconfirmed') ?></a>
                                                                </li>
                                                                <li>
                                                                    <a href="<?= base_url() ?>admin/bugs/change_status/<?= $v_bugs->bug_id ?>/confirmed"><?= lang('confirmed') ?></a>
                                                                </li>
                                                                <li>
                                                                    <a href="<?= base_url() ?>admin/bugs/change_status/<?= $v_bugs->bug_id ?>/in_progress"><?= lang('in_progress') ?></a>
                                                                </li>
                                                                <li>
                                                                    <a href="<?= base_url() ?>admin/bugs/change_status/<?= $v_bugs->bug_id ?>/resolved"><?= lang('resolved') ?></a>
                                                                </li>
                                                                <li>
                                                                    <a href="<?= base_url() ?>admin/bugs/change_status/<?= $v_bugs->bug_id ?>/verified"><?= lang('verified') ?></a>
                                                                </li>

                                                            </ul>
                                                        </div>
                                                    <?php } ?>
                                                </td>
                                            <?php } ?>
                                        </tr>
                                    <?php endforeach; ?>
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
                                    <form  data-parsley-validate="" novalidate=""
                                          action="<?php echo base_url() ?>admin/bugs/save_bug/<?php if (!empty($bug_info->bug_id)) echo $bug_info->bug_id; ?>"
                                          method="post" class="form-horizontal">


                                        <div class="form-group">
                                            <label class="col-sm-3 control-label"><?= lang('bug_title') ?><span
                                                    class="required">*</span></label>
                                            <div class="col-sm-5">
                                                <input type="text" name="bug_title" required class="form-control"
                                                       value="<?php if (!empty($bug_info->bug_title)) echo $bug_info->bug_title; ?>"/>
                                            </div>
                                        </div>
                                        <?php
                                        if (!empty($bug_info->project_id)) {
                                            $project_id = $bug_info->project_id;
                                        } elseif (!empty($project_id)) {
                                            $project_id = $project_id; ?>
                                            <input type="hidden" name="un_project_id" required class="form-control"
                                                   value="<?php echo $project_id ?>"/>
                                        <?php }
                                        if (!empty($bug_info->opportunities_id)) {
                                            $opportunities_id = $bug_info->opportunities_id;
                                        } elseif (!empty($opportunities_id)) {
                                            $opportunities_id = $opportunities_id; ?>
                                            <input type="hidden" name="un_opportunities_id" required
                                                   class="form-control"
                                                   value="<?php echo $opportunities_id ?>"/>
                                        <?php }
                                        ?>
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
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group" id="related_to">
                                        </div>
                                        <?php
                                        if (!empty($project_id)):?>
                                            <div class="form-group <?= !empty($project_id) ? '' : 'company' ?>">
                                                <label for="field-1"
                                                       class="col-sm-3 control-label"><?= lang('project') ?>
                                                    <span
                                                        class="required">*</span></label>
                                                <div class="col-sm-5">
                                                    <select name="project_id" style="width: 100%"
                                                            class="select_box <?= !empty($project_id) ? '' : 'company' ?>"
                                                            required="1">
                                                        <?php
                                                        $all_project = $this->bugs_model->get_permission('tbl_project');
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
                                                <div id="milestone"></div>
                                            </div>
                                        <?php endif ?>
                                        <?php if (!empty($opportunities_id)): ?>
                                            <div class="form-group <?= !empty($opportunities_id) ? '' : 'company' ?>">
                                                <label for="field-1"
                                                       class="col-sm-3 control-label"><?= lang('opportunities') ?>
                                                    <span class="required">*</span></label>
                                                <div class="col-sm-5">
                                                    <select name="opportunities_id" style="width: 100%"
                                                            class="select_box <?= !empty($opportunities_id) ? '' : 'company' ?>"
                                                            required="1">
                                                        <?php
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
                                        <div class="form-group" id="border-none">
                                            <label for="field-1" class="col-sm-3 control-label"><?= lang('reporter') ?>
                                                <span
                                                    class="required">*</span></label>
                                            <div class="col-sm-5">
                                                <select name="reporter" style="width: 100%" class="select_box"
                                                        required="">
                                                    <?php
                                                    $type = $this->uri->segment(4);
                                                    if (!empty($type) && !is_numeric($type)) {
                                                        $ex = explode('_', $type);
                                                        if ($ex[0] == 'c') {
                                                            $primary_contact = $ex[1];
                                                        }
                                                    }
                                                    $reporter_info = $this->db->get('tbl_users')->result();
                                                    if (!empty($reporter_info)) {
                                                        foreach ($reporter_info as $key => $v_reporter) {
                                                            $users_info = $this->db->where(array("user_id" => $v_reporter->user_id))->get('tbl_account_details')->row();
                                                            if (!empty($users_info)) {
                                                                if ($v_reporter->role_id == 1) {
                                                                    $role = lang('admin');
                                                                } elseif ($v_reporter->role_id == 2) {
                                                                    $role = lang('client');
                                                                } else {
                                                                    $role = lang('staff');
                                                                }
                                                                ?>
                                                                <option value="<?= $users_info->user_id ?>" <?php
                                                                if (!empty($bug_info->reporter)) {
                                                                    echo $v_reporter->user_id == $bug_info->reporter ? 'selected' : '';
                                                                } else if (!empty($primary_contact) && $primary_contact == $users_info->user_id) {
                                                                    echo 'selected';
                                                                }
                                                                ?>><?= $users_info->fullname . ' (' . $role . ')'; ?></option>
                                                                <?php
                                                            }
                                                        }
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-lg-3 control-label"><?= lang('priority') ?> <span
                                                    class="text-danger">*</span> </label>
                                            <div class="col-lg-5">
                                                <div class=" ">
                                                    <select name="priority" class="form-control">
                                                        <?php
                                                        $priorities = $this->db->get('tbl_priorities')->result();
                                                        if (!empty($priorities)) {
                                                            foreach ($priorities as $v_priorities):
                                                                ?>
                                                                <option value="<?= $v_priorities->priority ?>" <?php
                                                                if (!empty($bug_info) && $bug_info->priority == $bug_info->priority) {
                                                                    echo 'selected';
                                                                }
                                                                ?>><?= lang(strtolower($v_priorities->priority)) ?></option>
                                                                <?php
                                                            endforeach;
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="field-1"
                                                   class="col-sm-3 control-label"><?= lang('description') ?> </label>
                                            <div class="col-sm-8">
                                        <textarea class="form-control " name="bug_description" id="ck_editor"
                                                  required ><?php if (!empty($bug_info->bug_description)) echo $bug_info->bug_description; ?></textarea>
                                                <?php echo display_ckeditor($editor['ckeditor']); ?>
                                            </div>
                                        </div>

                                        <div class="form-group" id="border-none">
                                            <label for="field-1"
                                                   class="col-sm-3 control-label"><?= lang('bug_status') ?>
                                                <span
                                                    class="required">*</span></label>
                                            <div class="col-sm-5">

                                                <select name="bug_status" class="form-control" required>
                                                    <option
                                                        value="unconfirmed" <?php if (!empty($bug_info->bug_status)) echo $bug_info->bug_status == 'unconfirmed' ? 'selected' : '' ?>> <?= lang('unconfirmed') ?> </option>
                                                    <option
                                                        value="confirmed" <?php if (!empty($bug_info->bug_status)) echo $bug_info->bug_status == 'confirmed' ? 'selected' : '' ?>> <?= lang('confirmed') ?> </option>
                                                    <option
                                                        value="in_progress" <?php if (!empty($bug_info->bug_status)) echo $bug_info->bug_status == 'in_progress' ? 'selected' : '' ?>> <?= lang('in_progress') ?> </option>
                                                    <option
                                                        value="resolved" <?php if (!empty($bug_info->bug_status)) echo $bug_info->bug_status == 'resolved' ? 'selected' : '' ?>> <?= lang('resolved') ?> </option>
                                                    <option
                                                        value="verified" <?php if (!empty($bug_info->bug_status)) echo $bug_info->bug_status == 'verified' ? 'selected' : '' ?>> <?= lang('verified') ?> </option>
                                                </select>
                                            </div>
                                        </div>
                                        <?php if (!empty($project_id)): ?>
                                            <div class="form-group">
                                                <label for="field-1"
                                                       class="col-sm-3 control-label"><?= lang('visible_to_client') ?>
                                                    <span class="required">*</span></label>
                                                <div class="col-sm-8">
                                                    <input data-toggle="toggle" name="client_visible" value="Yes" <?php
                                                    if (!empty($bug_info) && $bug_info->client_visible == 'Yes') {
                                                        echo 'checked';
                                                    }
                                                    ?> data-on="<?= lang('yes') ?>" data-off="<?= lang('no') ?>"
                                                           data-onstyle="success" data-offstyle="danger"
                                                           type="checkbox">
                                                </div>
                                            </div>
                                        <?php endif ?>
                                        <?php
                                        if (!empty($bug_info)) {
                                            $bug_id = $bug_info->bug_id;
                                        } else {
                                            $bug_id = null;
                                        }
                                        ?>
                                        <?= custom_form_Fields(6, $bug_id); ?>
                                        <div class="form-group" id="border-none">
                                            <label for="field-1"
                                                   class="col-sm-3 control-label"><?= lang('assined_to') ?>
                                                <span
                                                    class="required">*</span></label>
                                            <div class="col-sm-9">
                                                <div class="checkbox c-radio needsclick">
                                                    <label class="needsclick">
                                                        <input id="" <?php
                                                        if (!empty($bug_info->permission) && $bug_info->permission == 'all') {
                                                            echo 'checked';
                                                        } elseif (empty($bug_info)) {
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
                                                        if (!empty($bug_info->permission) && $bug_info->permission != 'all') {
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
                                        if (!empty($bug_info->permission) && $bug_info->permission != 'all') {
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
                                                                    if (!empty($bug_info->permission) && $bug_info->permission != 'all') {
                                                                        $get_permission = json_decode($bug_info->permission);
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

                                                        if (!empty($bug_info->permission) && $bug_info->permission != 'all') {
                                                            $get_permission = json_decode($bug_info->permission);

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

                                                                    if (!empty($bug_info->permission) && $bug_info->permission != 'all') {
                                                                        $get_permission = json_decode($bug_info->permission);

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

                                                                    if (!empty($bug_info->permission) && $bug_info->permission != 'all') {
                                                                        $get_permission = json_decode($bug_info->permission);
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


                                        <div class="">
                                            <div class="col-sm-offset-3 col-sm-5">
                                                <button type="submit" id="sbtn"
                                                        class="btn btn-primary"><?= lang('save') ?></button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php }else{ ?>
                </div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>

