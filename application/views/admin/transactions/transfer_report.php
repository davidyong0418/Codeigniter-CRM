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
                <strong><?= lang('transfer_report') ?></strong>

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
                                        <a href="<?= base_url() ?>admin/transactions/transfer_report/<?= $v_account->account_id ?>"><?= $v_account->account_name ?></a>
                                    </li>
                                    <?php
                                endforeach;
                            endif;
                            ?>
                        </ul>
                    </div>
                    <a href="<?php echo base_url() ?>admin/transactions/transfer_report_pdf"
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
                        <th style="width: 15%"><?= lang('from_account') ?></th>
                        <th style="width: 15%"><?= lang('to_account') ?></th>
                        <th><?= lang('type') ?></th>
                        <th><?= lang('amount') ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $curency = $this->transactions_model->check_by(array('code' => config_item('default_currency')), 'tbl_currencies');
                    $total_amount = 0;
                    if (!empty($all_transfer_info)):
                    foreach ($all_transfer_info as $v_transfer) :
                        $from_account_info = $this->transactions_model->check_by(array('account_id' => $v_transfer->from_account_id), 'tbl_accounts');
                        $to_account_info = $this->transactions_model->check_by(array('account_id' => $v_transfer->to_account_id), 'tbl_accounts');
                        ?>
                        <tr class="custom-tr custom-font-print">
                            <td><?= strftime(config_item('date_format'), strtotime($v_transfer->date)); ?></td>
                            <td class="vertical-td"><?= $from_account_info->account_name ?></td>
                            <td class="vertical-td"><?= $to_account_info->account_name ?></td>
                            <td class="vertical-td"><?= lang($v_transfer->type) ?> </td>
                            <td><?= display_money($v_transfer->amount, $curency->symbol) ?></td>
                        </tr>
                        <?php
                        $total_amount += $v_transfer->amount;
                        ?>
                    <?php endforeach; ?>
                    </tbody>
                    <?php endif; ?>
                </table>
            </div>
        </div>
        <div class="panel-footer">
            <strong class=""><?= lang('total') . ' ' . lang('transfer') ?>:<span
                    class="label label-primary">
                    <?= display_money($total_amount, $curency->symbol) ?>
                    </span></span>
            </strong>
        </div>
    </div>
</div>
<script type="text/javascript">

    function print_sales_report(printReport) {
        var printContents = document.getElementById(printReport).innerHTML;
        var originalContents = document.body.innerHTML;
        document.body.innerHTML = printContents;
        window.print();
        document.body.innerHTML = originalContents;
    }

</script>
