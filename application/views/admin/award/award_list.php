<?php echo message_box('success'); ?>
<?php echo message_box('error');
$created = can_action('99', 'created');
$edited = can_action('99', 'edited');
$deleted = can_action('99', 'deleted');
?>

<div class="panel panel-custom" style="border: none;" data-collapsed="0">
    <div class="panel-heading">
        <div class="panel-title">
            <?= lang('award_list') ?>
            <div class="pull-right hidden-print" style="padding-top: 0px;padding-bottom: 8px">

                <span><?php echo btn_pdf('admin/award/employee_award_pdf'); ?></span>
                <?php if (!empty($created)) { ?>
                    <a href="<?= base_url() ?>admin/award/give_award" class="btn btn-xs btn-info" data-toggle="modal"
                       data-placement="top" data-target="#myModal">
                        <i class="fa fa-plus "></i> <?= ' ' . lang('give_award') ?></a>
                <?php } ?>
            </div>
        </div>
    </div>
    <!-- Table -->
    <div class="panel-body">
        <table class="table table-striped DataTables " id="DataTables" cellspacing="0" width="100%">
            <thead>
            <tr>
                <th class="col-sm-1"><?= lang('employee_id') ?></th>
                <th><?= lang('name') ?></th>
                <th><?= lang('award_name') ?></th>
                <th><?= lang('gift') ?></th>
                <th><?= lang('amount') ?></th>
                <th><?= lang('month') ?></th>
                <th><?= lang('award_date') ?></th>
                <?php if (!empty($deleted) || !empty($edited)) { ?>
                    <th><?= lang('action') ?></th>
                <?php } ?>
            </tr>
            </thead>
            <tbody>
            <?php
            $curency = $this->db->where('code', config_item('default_currency'))->get('tbl_currencies')->row();
            if (!empty($all_employee_award_info)):foreach ($all_employee_award_info as $v_award_info): ?>
                <tr>
                    <td><?php echo $v_award_info->employment_id ?></td>
                    <td><?php echo $v_award_info->fullname; ?></td>
                    <td><?php echo $v_award_info->award_name; ?></td>
                    <td><?php echo $v_award_info->gift_item; ?></td>
                    <td><?php echo display_money($v_award_info->award_amount, $curency->symbol); ?></td>
                    <td><?= strftime(date('M Y'), strtotime($v_award_info->award_date)) ?></td>
                    <td><?= strftime(config_item('date_format'), strtotime($v_award_info->given_date)) ?></td>
                    <?php if (!empty($deleted) || !empty($edited)) { ?>
                        <td>
                            <?php if (!empty($edited)) { ?>
                                <span data-toggle="tooltip" data-placement="top" title="<?= lang('edit') ?>">
                        <a href="<?= base_url() ?>admin/award/give_award/<?= $v_award_info->employee_award_id ?>"
                           class="btn btn-xs btn-primary"
                           data-toggle="modal"
                           data-placement="top" data-target="#myModal">
                            <i class="fa fa-pencil-square-o "></i>
                        </a>
                            </span>
                            <?php }
                            if (!empty($deleted)) { ?>
                                <?php echo btn_delete('admin/award/delete_employee_award/' . $v_award_info->employee_award_id); ?>
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
