<div id="printReport">
    <div class="show_print">
        <div style="width: 100%; border-bottom: 2px solid black;">
            <table style="width: 100%; vertical-align: middle;">
                <tr>
                    <td style="width: 50px; border: 0px;">
                        <img style="width: 50px;height: 50px;margin-bottom: 5px;"
                             src="<?= base_url() . config_item('company_logo') ?>" alt="" class="img-circle"/>
                    </td>

                    <td style="border: 0px;">
                        <p style="margin-left: 10px; font: 14px lighter;"><?= config_item('company_name') ?></p>
                    </td>

                </tr>
            </table>
        </div>
        <br/>
    </div>
    <div class="panel panel-custom">
        <!-- Default panel contents -->
        <div class="panel-heading">
            <div class="panel-title">
                <strong><?= lang('transactions_report') ?></strong>

                <div class="pull-right hidden-print">
                    <div class="btn-group">
                        <button class="btn btn-xs btn-primary dropdown-toggle" data-toggle="dropdown"
                                aria-expanded="false">
                            <?= lang('search_by') ?><span class="caret"></span></button>
                        <ul class="dropdown-menu animated zoomIn">
                            <?php
                            $all_account = $this->db->get('tbl_accounts')->result();
                            if (!empty($all_account)):
                                foreach ($all_account as $v_account):
                                    ?>
                                    <li>
                                        <a href="<?= base_url() ?>admin/transactions/transactions_report/<?= $v_account->account_id ?>"><?= $v_account->account_name ?></a>
                                    </li>
                                    <?php
                                endforeach;
                            endif;
                            ?>
                        </ul>
                    </div>
                    <a href="<?php echo base_url() ?>admin/transactions/transactions_report_pdf/"
                       class="btn btn-xs btn-success" data-toggle="tooltip" data-placement="top"
                       title="<?= lang('pdf') ?>"><?= lang('pdf') ?></a>
                    <a onclick="print_sales_report('printReport')" class="btn btn-xs btn-danger"
                       data-toggle="tooltip" data-placement="top"
                       title="<?= lang('print') ?>"><?= lang('print') ?></a>
                </div>
            </div>
        </div>
        <div class="panel-body">
            <div class="table-responsive">
                <table class="table table-striped DataTables " id="DataTables" cellspacing="0" width="100%">
                    <thead>
                    <tr>
                        <th style="width: 15%"><?= lang('date') ?></th>
                        <th style="width: 15%"><?= lang('account') ?></th>
                        <th><?= lang('type') ?></th>
                        <th><?= lang('name') . '/' . lang('title') ?></th>
                        <th><?= lang('amount') ?></th>
                        <th><?= lang('credit') ?></th>
                        <th><?= lang('debit') ?></th>
                        <th><?= lang('balance') ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $total_amount = 0;
                    $total_debit = 0;
                    $total_credit = 0;
                    $total_balance = 0;
                    $curency = $this->transactions_model->check_by(array('code' => config_item('default_currency')), 'tbl_currencies');

                    if (!empty($all_transaction_info)): foreach ($all_transaction_info as $v_transaction) :
                        $account_info = $this->transactions_model->check_by(array('account_id' => $v_transaction->account_id), 'tbl_accounts');
                        ?>
                        <tr class="custom-tr custom-font-print">
                            <td><?= strftime(config_item('date_format'), strtotime($v_transaction->date)); ?></td>
                            <td class="vertical-td"><?php
                                if (!empty($account_info->account_name)) {
                                    echo $account_info->account_name;
                                } else {
                                    echo '-';
                                }
                                ?></td>
                            <td class="vertical-td"><?= lang($v_transaction->type) ?> </td>
                            <td><?= ($v_transaction->name ? $v_transaction->name : '-'); ?></td>
                            <td><?= display_money($v_transaction->amount, $curency->symbol) ?></td>
                            <td><?= display_money($v_transaction->credit, $curency->symbol) ?></td>
                            <td><?= display_money($v_transaction->debit, $curency->symbol) ?></td>
                            <td><?= display_money($v_transaction->total_balance, $curency->symbol) ?></td>
                        </tr>
                        <?php
                        $total_amount += $v_transaction->amount;
                        $total_credit += $v_transaction->credit;
                        $total_debit += $v_transaction->debit;
                        ?>
                    <?php endforeach; ?>

                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="panel-footer">
            <strong style="width: 25%"><?= lang('total_amount') ?>:<span
                    class="label label-success"><?= display_money($total_amount, $curency->symbol) ?></span></span>
            </strong>
            <strong class="col-sm-3"><?= lang('credit') ?>:<span
                    class="label label-primary"><?= display_money($total_credit, $curency->symbol) ?></span></span>
            </strong>
            <strong class="col-sm-3"><?= lang('debit') ?>:<span
                    class="label label-danger"><?= display_money($total_debit, $curency->symbol) ?></span></span>
            </strong>
            <strong class="col-sm-3"><?= lang('balance') ?>:<span
                    class="label label-info"><?= display_money($total_credit - $total_debit, $curency->symbol) ?></span></span>
            </strong>
        </div>
    </div>
</div>

<div class="panel panel-custom ">
    <div class="panel-heading">
        <div class="panel-title">
            <strong><?= lang('transactions_report') . ' ' . lang('graph') . ' ' . date('F-Y') ?></strong>
        </div>
    </div>
    <div class="panel-body">
        <div id="morris-line"></div>
    </div>
</div>
<script type="text/javascript">
    $(function () {

        if (typeof Morris === 'undefined') return;

        var chartdata = [
            <?php foreach ($transactions_report as $days => $v_report){
            $total_expense = 0;
            $total_income = 0;
            $total_transfer = 0;
            foreach ($v_report as $Expense) {
                if ($Expense->type == 'Expense') {
                    $total_expense += $Expense->amount;
                }
                if ($Expense->type == 'Income') {
                    $total_income += $Expense->amount;
                }
                if ($Expense->type == 'Transfer') {
                    $total_transfer += $Expense->amount / 2;
                }
            }
            ?>
            {
                y: "<?= $days ?>",
                income: <?= $total_income?>,
                expense: <?= $total_expense?>,
                transfer: <?= $total_transfer?>},
            <?php }?>


        ];
        // Line Chart
        // -----------------------------------

        new Morris.Line({
            element: 'morris-line',
            data: chartdata,
            xkey: 'y',
            ykeys: ["income", "expense", "transfer"],
            labels: ["<?= lang('Income')?>", "<?= lang('expense')?>", "<?= lang('transfer')?>"],
            lineColors: ["#27c24c", "#f05050", "#5d9cec"],
            parseTime: false,
            resize: true
        });

    });
    function print_sales_report(printReport) {
        var printContents = document.getElementById(printReport).innerHTML;
        var originalContents = document.body.innerHTML;
        document.body.innerHTML = printContents;
        window.print();
        document.body.innerHTML = originalContents;
    }

</script>
