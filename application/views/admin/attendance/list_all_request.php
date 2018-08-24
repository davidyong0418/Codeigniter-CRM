<?php echo message_box('success'); ?>
<?php echo message_box('error'); ?>

<div class="panel panel-custom">
    <div class="panel-heading">
        <div class="panel-title">
            <strong><?= lang('all_timechange_request') ?></strong>

            <div class="pull-right hidden-print" style="padding-top: 0px;padding-bottom: 8px">
                <a href="<?= base_url() ?>admin/attendance/add_time_manually" class="btn btn-xs btn-info"
                   data-toggle="modal"
                   data-placement="top" data-target="#myModal">
                    <i class="fa fa-plus "></i> <?= ' ' . lang('add_time_manually') ?></a>
            </div>
        </div>
    </div>
    <!-- Table -->

    <table class="table table-bordered table-hover" id="dataTables-example">
        <thead>
        <tr>
            <th><?= lang('emp_id') ?></th>
            <th><?= lang('name') ?></th>
            <th><?= lang('time_in') ?></th>
            <th><?= lang('time_out') ?></th>
            <th><?= lang('status') ?></th>
            <th><?= lang('action') ?></th>
        </tr>
        </thead>
        <tbody>
        <?php

        if (!empty($all_clock_history)):foreach ($all_clock_history as $key => $v_clock_history):
            ?>
            <tr id="table_clock_history_<?= $v_clock_history->clock_history_id ?>">
                <td><?php echo $v_clock_history->employment_id; ?></td>
                <td><?php echo $v_clock_history->fullname; ?></td>
                <td><?php
                    if ($v_clock_history->clockin_edit != "00:00:00") {
                        echo display_time($v_clock_history->clockin_edit);
                    }
                    ?></td>
                <td><?php
                    if ($v_clock_history->clockout_edit != "00:00:00") {
                        echo display_time($v_clock_history->clockout_edit);
                    }
                    ?></td>
                <td><?php
                    if ($v_clock_history->status == 1) {
                        $label = 'warning';
                        $text = lang('pending');
                    } elseif ($v_clock_history->status == 2) {
                        $label = 'success';
                        $text = lang('accepted');
                    } elseif ($v_clock_history->status == 3) {
                        $label = 'danger';
                        $text = lang('rejected');
                    } ?>
                    <span class="label label-<?= $label ?>"><?= $text ?></span>
                </td>
                <td>
                    <a href="<?= base_url() ?>admin/attendance/view_timerequest/<?= $v_clock_history->clock_history_id ?>"
                       class="btn btn-primary btn-xs"
                       title="<?= lang('view') ?>" data-toggle="modal" data-placement="top" data-target="#myModal"><span
                            class="fa fa-list-alt"></span></a>
                    <?php if ($this->session->userdata('user_type') == 1) { ?>
                        <?php echo ajax_anchor(base_url("admin/attendance/delete_request/" . $v_clock_history->clock_history_id), "<i class='btn btn-xs btn-danger fa fa-trash-o'></i>", array("class" => "", "title" => lang('delete'), "data-fade-out-on-success" => "#table_clock_history_" . $v_clock_history->clock_history_id)); ?>
                    <?php }
                    ?>
                </td>
            </tr>
        <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>
</div>
