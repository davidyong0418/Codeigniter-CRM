<div class="panel panel-custom">
    <div class="panel-heading">
        <h3 class="panel-title"><?= lang('all_expense') ?></h3>
    </div>
    <div class="panel-body">
        <div class="table-responsive">
            <table class="table table-striped DataTables " id="DataTables" cellspacing="0" width="100%">
                <thead>
                <tr>
                    <th><?= lang('date') ?></th>
                    <th><?= lang('account_name') ?></th>
                    <th class="col-date"><?= lang('notes') ?></th>
                    <th class="col-currency"><?= lang('amount') ?></th>
                    <th class="col-currency"><?= lang('credit') ?></th>
                    <th class="col-currency"><?= lang('debit') ?></th>
                    <th class="col-currency"><?= lang('balance') ?></th>
                    <th class="col-options no-sort"><?= lang('action') ?></th>
                </tr>
                </thead>
                <tbody>
                <?php
                $total_amount = 0;
                $total_credit = 0;
                $total_debit = 0;
                $total_balance = 0;
                $curency = $this->report_model->check_by(array('code' => config_item('default_currency')), 'tbl_currencies');
                $all_expense_info = $this->db->where(array('type' => 'Expense'))->order_by('transactions_id', 'DESC')->get('tbl_transactions')->result();
                foreach ($all_expense_info as $v_expense) :
                    $account_info = $this->report_model->check_by(array('account_id' => $v_expense->account_id), 'tbl_accounts');
                    ?>
                    <tr>
                        <td><?= strftime(config_item('date_format'), strtotime($v_expense->date)); ?></td>
                        <td><?= !empty($account_info->account_name) ? $account_info->account_name : '-' ?></td>
                        <td><?= $v_expense->notes ?></td>
                        <td><?= display_money($v_expense->amount, $curency->symbol) ?></td>
                        <td><?= display_money($v_expense->credit, $curency->symbol) ?></td>
                        <td><?= display_money($v_expense->debit, $curency->symbol) ?></td>
                        <td><?= display_money($v_expense->total_balance, $curency->symbol) ?></td>

                        <td><?= btn_edit('admin/transactions/expense/' . $v_expense->transactions_id) ?>
                            <?= btn_delete('admin/transactions/delete_expense/' . $v_expense->transactions_id) ?></td>
                    </tr>
                    <?php
                    $total_amount += $v_expense->amount;
                    $total_credit += $v_expense->credit;
                    $total_debit += $v_expense->debit;
                    $total_balance += $v_expense->total_balance;
                    ?>
                    <?php
                endforeach;
                ?>

                </tbody>
            </table>
        </div>
    </div>
    <div class="panel-footer">
        <strong style="width: 25%"><?= lang('balance') ?>:<span
                class="label label-info"><?= display_money($total_credit - $total_debit, $curency->symbol) ?></span></span>
        </strong>
        <strong class="col-sm-3"><?= lang('total_amount') ?>:<span
                class="label label-success">
                <?= display_money($total_amount, $curency->symbol) ?>
            </span></span>
        </strong>
        <strong class="col-sm-3"><?= lang('credit') ?>:<span
                class="label label-primary">
                <?= display_money($total_credit, $curency->symbol) ?>
            </span></span>
        </strong>
        <strong class="col-sm-3"><?= lang('debit') ?>:<span
                class="label label-danger">
                <?= display_money($total_debit, $curency->symbol) ?>
                </span></span>
        </strong>

    </div>
</div>