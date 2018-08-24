<?php echo message_box('success'); ?>
<?php echo message_box('error'); ?>

<div class="panel panel-custom">
    <div class="panel-heading">
        <div class="panel-title">
            <strong><?= lang('employee_salary_details') ?></strong>
        </div>
    </div>

    <!-- Table -->
    <table class="table table-striped DataTables " id="DataTables" cellspacing="0" width="100%">
        <thead>
        <tr>
            <th class="col-sm-1"><?= lang('emp_id') ?></th>
            <th><?= lang('name') ?></th>
            <th><?= lang('salary_type') ?></th>
            <th><?= lang('basic_salary') ?></th>
            <th><?= lang('overtime') ?>
                <small>(<?= lang('per_hour') ?>)</small>
            </th>
            <th class="col-sm-1"><?= lang('details') ?></th>
            <th><?= lang('action') ?></th>

        </tr>
        </thead>
        <tbody>
        <?php

        if (!empty($emp_salary_info)):foreach ($emp_salary_info as $v_emp_salary):
            ?>
            <tr>
                <td><?php echo $v_emp_salary->employment_id; ?></td>
                <td>
                    <?php
                    if (!empty($v_emp_salary->salary_grade)) {
                        ?>
                        <a href="<?= base_url() ?>admin/payroll/view_salary_details/<?= $v_emp_salary->salary_template_id ?>/<?= $v_emp_salary->user_id ?>"
                           title="<?= lang('view') ?>" data-toggle="modal" data-target="#myModal_lg">
                            <?php echo $v_emp_salary->fullname ?>
                        </a>
                    <?php } else {
                        echo $v_emp_salary->fullname;
                    } ?>
                </td>
                <td><?php
                    if (!empty($v_emp_salary->salary_grade)) {
                        echo $v_emp_salary->salary_grade . ' <small>(' . lang("monthly") . ')</small>';
                    } else {
                        echo $v_emp_salary->hourly_grade . ' <small>(' . lang("hourly") . ')</small>';
                    }
                    ?></td>
                <td><?php
                    $curency = $this->db->where('code', config_item('default_currency'))->get('tbl_currencies')->row();
                    if (!empty($v_emp_salary->basic_salary)) {
                        echo display_money($v_emp_salary->basic_salary, $curency->symbol);
                    } else {
                        echo display_money($v_emp_salary->hourly_rate, $curency->symbol) . ' <small>(' . lang("per_hour") . ')</small>';
                    }
                    ?></td>
                <td><?php
                    if (!empty($v_emp_salary->overtime_salary)) {
                        echo display_money($v_emp_salary->overtime_salary, $curency->symbol);
                    }
                    ?></td>


                <td>
                    <?php
                    if (!empty($v_emp_salary->salary_grade)) {
                    ?>
                    <?php echo btn_view_modal('admin/payroll/view_salary_details/' . $v_emp_salary->salary_template_id . '/' . $v_emp_salary->user_id); ?></td>
                <?php } ?>
                <td>
                    <?php echo btn_edit('admin/payroll/manage_salary_details/' . $v_emp_salary->departments_id); ?>
                    <?php if ($this->session->userdata('user_type') == '1') { ?>
                        <?php echo btn_delete('admin/payroll/delete_salary/' . $v_emp_salary->payroll_id); ?>
                    <?php } ?>
                </td>
            </tr>
        <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>
</div>
