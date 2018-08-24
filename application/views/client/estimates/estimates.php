<?= message_box('success'); ?>
<div class="panel panel-custom">
    <div class="panel-heading">
        <h3 class="panel-title"><?= lang('all_estimates') ?></h3>
    </div>

    <div class="table-responsive">
        <table class="table table-striped DataTables " id="DataTables" cellspacing="0" width="100%">
            <thead>
            <tr>
                <th><?= lang('estimate') ?></th>
                <th><?= lang('created') ?></th>
                <th><?= lang('due_date') ?></th>
                <th><?= lang('client_name') ?></th>
                <th><?= lang('amount') ?></th>
                <th><?= lang('status') ?></th>
                <th><?= lang('action') ?></th>
            </tr>
            </thead>
            <tbody>
            <?php
            if (!empty($all_estimates_info)) {
                foreach ($all_estimates_info as $v_estimates) {
                    if ($v_estimates->status == 'pending') {
                        $label = "info";
                    } elseif ($v_estimates->status == 'accepted') {
                        $label = "success";
                    } else {
                        $label = "danger";
                    }
                    ?>
                    <tr>
                        <td>
                            <a class="text-info"
                               href="<?= base_url() ?>client/estimates/index/estimates_details/<?= $v_estimates->estimates_id ?>"><?= $v_estimates->reference_no ?></a>
                        </td>
                        <td><?= strftime(config_item('date_format'), strtotime($v_estimates->date_saved)) ?></td>
                        <td><?= strftime(config_item('date_format'), strtotime($v_estimates->due_date)) ?></td>
                        <?php
                        $client_info = $this->estimates_model->check_by(array('client_id' => $v_estimates->client_id), 'tbl_client');
                        ?>
                        <td><?= $client_info->name; ?></td>
                        <?php $currency = $this->estimates_model->client_currency_sambol($v_estimates->client_id); ?>
                        <td>
                            <?= display_money($this->estimates_model->estimate_calculation('total', $v_estimates->estimates_id), $currency->symbol); ?>
                        </td>
                        <td><span class="label label-<?= $label ?>"><?= lang(strtolower($v_estimates->status)) ?></span>
                        </td>
                        <td>
                            <div class="btn-group">
                                <button class="btn btn-xs btn-default dropdown-toggle" data-toggle="dropdown">
                                    <?= lang('change_status') ?>
                                    <span class="caret"></span></button>
                                <ul class="dropdown-menu animated zoomIn">
                                    <li>
                                        <a href="<?= base_url() ?>client/estimates/change_status/declined/<?= $v_estimates->estimates_id ?>"><?= lang('declined') ?></a>
                                    </li>
                                    <li>
                                        <a href="<?= base_url() ?>client/estimates/change_status/accepted/<?= $v_estimates->estimates_id ?>"><?= lang('accepted') ?></a>
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                    <?php
                }
            }
            ?>
            </tbody>
        </table>
    </div>
</div>