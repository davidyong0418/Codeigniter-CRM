<?php echo message_box('success'); ?>
<?php echo message_box('error'); ?>

<style>

</style>
<div class="row">
    <div class="col-sm-12">
        <div class="nav-tabs-custom">
            <!-- Tabs within a box -->
            <ul class="nav nav-tabs">
                <li class="<?= $active == 1 ? 'active' : '' ?>"><a href="#task_list"
                                                                   data-toggle="tab"><?= lang('all_bugs') ?></a></li>
                <li class="<?= $active == 2 ? 'active' : '' ?>"><a href="#assign_task"
                                                                   data-toggle="tab"><?= lang('new_bugs') ?></a></li>
            </ul>
            <div class="tab-content bg-white">
                <!-- Stock Category List tab Starts -->
                <div class="tab-pane <?= $active == 1 ? 'active' : '' ?>" id="task_list" style="position: relative;">
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

                                </tr>
                                </thead>
                                <tbody>
                                <?php

                                if (!empty($all_bugs_info)):foreach ($all_bugs_info as $key => $v_bugs):
                                    $reporter = $this->db->where('user_id', $v_bugs->reporter)->get('tbl_users')->row();

                                    if ($reporter->role_id == '1') {
                                        $badge = 'danger';
                                    } elseif ($reporter->role_id == '2') {
                                        $badge = 'info';
                                    } else {
                                        $badge = 'primary';
                                    }
                                    ?>
                                    <tr>
                                        <td><a class="text-info" style="<?php
                                            if ($v_bugs->bug_status == 'resolve') {
                                                echo 'text-decoration: line-through;';
                                            }
                                            ?>"
                                               href="<?= base_url() ?>client/bugs/view_bug_details/<?= $v_bugs->bug_id ?>"><?php echo $v_bugs->bug_title; ?></a>
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
                                                <span class="badge btn-<?= $badge ?> "><?= $reporter->username ?></span>
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


                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Add Stock Category tab Starts -->
                <div class="tab-pane <?= $active == 2 ? 'active' : '' ?>" id="assign_task" style="position: relative;">
                    <div class="box" style="border: none; padding-top: 15px;" data-collapsed="0">
                        <div class="panel-body">
                            <form data-parsley-validate="" novalidate=""
                                  action="<?php echo base_url() ?>client/bugs/save_bug/<?php if (!empty($bug_info->bug_id)) echo $bug_info->bug_id; ?>"
                                  method="post" class="form-horizontal">


                                <div class="form-group">
                                    <label class="col-sm-3 control-label"><?= lang('bug_title') ?><span
                                            class="required">*</span></label>
                                    <div class="col-sm-5">
                                        <input type="text" name="bug_title" required class="form-control"
                                               value="<?php if (!empty($bug_info->bug_title)) echo $bug_info->bug_title; ?>"/>
                                    </div>
                                </div>

                                <div class="form-group ">
                                    <label for="field-1"
                                           class="col-sm-3 control-label"><?= lang('select') . ' ' . lang('project') ?>
                                        <span
                                            class="required">*</span></label>
                                    <div class="col-sm-5">
                                        <select name="project_id" style="width: 100%" class="select_box"
                                                required onchange="get_milestone_by_id(this.value)">
                                            <?php
                                            $client_id = $this->session->userdata('client_id');
                                            $all_project = $this->db->where('client_id', $client_id)->get('tbl_project')->result();
                                            if (!empty($all_project)) {
                                                foreach ($all_project as $v_project) {
                                                    ?>
                                                    <option value="<?= $v_project->project_id ?>" <?php
                                                    if (!empty($bug_info->project_id)) {
                                                        echo $v_project->project_id == $bug_info->project_id ? 'selected' : '';
                                                    }
                                                    ?>><?= $v_project->project_name ?></option>
                                                    <?php
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
                                    <label for="field-1" class="col-sm-3 control-label"><?= lang('description') ?> <span
                                            class="required">*</span></label>
                                    <div class="col-sm-8">
                                        <textarea class="form-control " name="bug_description" id="ck_editor"
                                                  required><?php if (!empty($bug_info->bug_description)) echo $bug_info->bug_description; ?></textarea>
                                        <?php echo display_ckeditor($editor['ckeditor']); ?>
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
            </div>
        </div>
    </div>
</div>

