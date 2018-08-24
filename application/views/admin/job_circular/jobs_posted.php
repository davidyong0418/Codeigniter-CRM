<?php echo message_box('success'); ?>
<?php echo message_box('error'); ?>
<?php
$created = can_action('103', 'created');
$edited = can_action('103', 'edited');
$deleted = can_action('103', 'deleted');
?>
<div class="panel panel-custom">
    <div class="panel-heading">
        <div class="panel-title">
            <strong><?= lang('job_posted_list') ?></strong>
            <?php if (!empty($created)) { ?>
                <div class="pull-right hidden-print" style="padding-top: 0px;padding-bottom: 8px">
                    <a href="<?= base_url() ?>admin/job_circular/new_jobs_posted" class="btn btn-xs btn-info"
                       data-toggle="modal"
                       data-placement="top" data-target="#myModal_lg">
                        <i class="fa fa-plus "></i> <?= ' ' . lang('new') . ' ' . lang('jobs_posted') ?></a>
                </div>
            <?php } ?>
        </div>
    </div>
    <!-- Table -->
    <div class="panel-body">
        <table class="table table-striped DataTables " id="DataTables" cellspacing="0" width="100%">
            <thead>
            <tr>
                <th><?= lang('job_title') ?></th>
                <th><?= lang('designation') ?></th>
                <th><?= lang('vacancy_no') ?></th>
                <th><?= lang('last_date') ?></th>
                <?php $show_custom_fields = custom_form_table(14, null);
                if (!empty($show_custom_fields)) {
                    foreach ($show_custom_fields as $c_label => $v_fields) {
                        if (!empty($c_label)) {
                            ?>
                            <th><?= $c_label ?> </th>
                        <?php }
                    }
                }
                ?>
                <th><?= lang('status') ?></th>
                <th><?= lang('action') ?></th>
            </tr>
            </thead>
            <tbody>
            <?php if (!empty($job_post_info)): foreach ($job_post_info as $v_job_post):
                if (!empty($v_job_post->designations_id)) {
                    $design_info = $this->db->where('designations_id', $v_job_post->designations_id)->get('tbl_designations')->row();
                    if (!empty($design_info)) {
                        $designation = $design_info->designations;
                    } else {
                        $designation = '-';
                    }
                } else {
                    $designation = '-';
                }
                $can_edit = $this->job_circular_model->can_action('tbl_job_circular', 'edit', array('job_circular_id' => $v_job_post->job_circular_id));
                $can_delete = $this->job_circular_model->can_action('tbl_job_circular', 'delete', array('job_circular_id' => $v_job_post->job_circular_id));
                ?>
                <tr>
                    <td>
                        <a data-toggle="modal" data-target="#myModal_lg"
                           href="<?= base_url() ?>admin/job_circular/view_circular_details/<?= $v_job_post->job_circular_id ?>"> <?php echo $v_job_post->job_title; ?></a>
                    </td>
                    <td><?php echo $designation ?></td>
                    <td><?php echo $v_job_post->vacancy_no; ?></td>
                    <td><?= strftime(config_item('date_format'), strtotime($v_job_post->last_date)) ?></td>
                    <?php $show_custom_fields = custom_form_table(14, $v_job_post->job_circular_id);
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

                        <?php

                        if ($v_job_post->status == 'unpublished') : ?>
                            <span class="label label-danger"><?= lang('unpublished') ?></span>
                        <?php else : ?>
                            <span class="label label-success"><?= lang('published') ?></span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="<?= base_url() ?>admin/job_circular/jobs_applications/<?= $v_job_post->job_circular_id ?>"
                           class="btn btn-purple btn-xs" data-toggle="tooltip"
                           data-placement="top" title="<?= lang('all_application_for') ?>">
                            <i class="fa fa-list"></i> </a>
                        <?php if (!empty($can_edit) && !empty($edited)) { ?>
                            <?php
                            if ($v_job_post->status == 'unpublished') {
                                echo btn_publish('admin/job_circular/change_status/published/' . $v_job_post->job_circular_id);
                            } else {
                                echo btn_unpublish('admin/job_circular/change_status/unpublished/' . $v_job_post->job_circular_id);
                            }
                            ?>
                            <span data-toggle="tooltip" data-placement="top" title="<?= lang('edit') ?>">
                        <a href="<?= base_url() ?>admin/job_circular/new_jobs_posted/<?= $v_job_post->job_circular_id ?>"
                           class="btn btn-primary btn-xs"
                           data-toggle="modal"
                           data-placement="top" data-target="#myModal_lg">
                            <i class="fa fa-pencil-square-o"></i> </a>
                            </span>
                        <?php }
                        if (!empty($can_delete) && !empty($deleted)) { ?>
                            <?php echo btn_delete('admin/job_circular/delete_jobs_posted/' . $v_job_post->job_circular_id); ?>
                        <?php } ?>
                        <a target="_blank"
                           href="<?= base_url() ?>frontend/circular_details/<?= $v_job_post->job_circular_id ?>"
                           class="btn btn-primary btn-xs"
                           data-toggle="tooltip"
                           data-placement="top"
                           title="<?= lang('view_circular_details') . ' & ' . lang('apply_now') ?>">
                            <i class="fa fa-paper-plane"></i> </a>
                    </td>
                </tr>
                <?php

            endforeach;
                ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>