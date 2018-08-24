<?php
$client_id = $this->session->userdata('client_id');
$client_outstanding = $this->invoice_model->client_outstanding($client_id);
$currency = $this->db->where(array('code' => config_item('default_currency')))->get('tbl_currencies')->row();
?>
<div class="row">
    <div class="col-lg-3">
        <!-- START widget-->
        <div class="panel widget">
            <div class="panel-body pl-sm pr-sm pt-sm pb0 text-center">
                <h3 class="mt0 mb0"><?php
                    if ($client_outstanding > 0) {
                        echo display_money($client_outstanding, $currency->symbol);
                    } else {
                        echo '0.00';
                    }
                    ?></h3>
                <p class="text-warning m0"><?= lang('total') . ' ' . lang('outstanding') . ' ' . lang('invoice') ?></p>
            </div>
        </div>
    </div>
    <!-- END widget-->
    <?php
    $past_overdue = 0;
    $all_paid_amount = 0;
    $not_paid = 0;
    $fully_paid = 0;
    $draft = 0;
    $partially_paid = 0;
    $overdue = 0;
    $all_invoices = $this->db->where(array('client_id' => $client_id))->get('tbl_invoices')->result();

    if (!empty($all_invoices)) {
        $all_invoices = array_reverse($all_invoices);
        foreach ($all_invoices as $v_invoice) {
            $payment_status = $this->invoice_model->get_payment_status($v_invoice->invoices_id);
            if (strtotime($v_invoice->due_date) < time() AND $payment_status != lang('fully_paid')) {
                $past_overdue += $this->invoice_model->calculate_to('invoice_due', $v_invoice->invoices_id);
            }
            $all_paid_amount += $this->invoice_model->calculate_to('paid_amount', $v_invoice->invoices_id);

            if ($this->invoice_model->get_payment_status($v_invoice->invoices_id) == lang('not_paid')) {
                $not_paid += count($v_invoice->invoices_id);
            }
            if ($this->invoice_model->get_payment_status($v_invoice->invoices_id) == lang('fully_paid')) {
                $fully_paid += count($v_invoice->invoices_id);
            }
            if ($this->invoice_model->get_payment_status($v_invoice->invoices_id) == lang('draft')) {
                $draft += count($v_invoice->invoices_id);
            }
            if ($this->invoice_model->get_payment_status($v_invoice->invoices_id) == lang('partially_paid')) {
                $partially_paid += count($v_invoice->invoices_id);
            }
            if (strtotime($v_invoice->due_date) < time() AND $payment_status != lang('fully_paid')) {
                $overdue += count($v_invoice->invoices_id);
            }
        }
    }
    ?>
    <div class="col-lg-3">
        <!-- START widget-->
        <div class="panel widget">
            <div class="panel-body pl-sm pr-sm pt-sm pb0 text-center">
                <h3 class="mt0 mb0 "><?= display_money($all_paid_amount + $client_outstanding, $currency->symbol) ?></h3>
                <p class="text-primary m0"><?= lang('total') . ' ' . lang('invoice_amount') ?></p>
            </div>
        </div>
        <!-- END widget-->
    </div>
    <div class="col-lg-3">
        <!-- START widget-->
        <div class="panel widget">
            <div class="panel-body pl-sm pr-sm pt-sm pb0 text-center">
                <h3 class="mt0 mb0"><?= display_money($past_overdue, $currency->symbol) ?></h3>
                <p class="text-danger m0"><?= lang('past') . ' ' . lang('overdue') . ' ' . lang('invoice') ?></p>
            </div>
        </div>
        <!-- END widget-->
    </div>
    <div class="col-lg-3">
        <!-- START widget-->
        <div class="panel widget">
            <div class="panel-body pl-sm pr-sm pt-sm pb0 text-center">
                <h3 class="mt0 mb0 "><?= display_money($all_paid_amount, $currency->symbol) ?></h3>
                <p class="text-success m0"><?= lang('paid') . ' ' . lang('invoice') ?></p>
            </div>
        </div>
        <!-- END widget-->
    </div>
</div>
<?php if (!empty($all_invoices)) { ?>
    <div class="row">
        <div class="col-lg-5ths pl-lg">
            <!-- START widget-->
            <div class="panel widget">
                <div class="pl-sm pr-sm pb-sm">
                    <strong><a style="font-size: 15px"
                               href="<?= base_url() ?>client/invoice/manage_invoice/filter_by/not_paid"><?= lang('unpaid') ?></a>
                        <small class="pull-right " style="padding-top: 2px"> <?= $not_paid ?>
                            / <?= count($all_invoices) ?></small>
                    </strong>
                    <div class="progress progress-striped progress-xs mb-sm">
                        <div class="progress-bar progress-bar-danger " data-toggle="tooltip"
                             data-original-title="<?= round(($not_paid / count($all_invoices)) * 100) ?>%"
                             style="width: <?= ($not_paid / count($all_invoices)) * 100 ?>%"></div>
                    </div>
                </div>
            </div>
            <!-- END widget-->
        </div>

        <div class="col-lg-5ths">
            <!-- START widget-->
            <div class="panel widget">
                <div class="pl-sm pr-sm pb-sm">
                    <strong><a style="font-size: 15px"
                               href="<?= base_url() ?>client/invoice/manage_invoice/filter_by/fully_paid"><?= lang('paid') ?></a>
                        <small class="pull-right " style="padding-top: 2px"> <?= $fully_paid ?>
                            / <?= count($all_invoices) ?></small>
                    </strong>
                    <div class="progress progress-striped progress-xs mb-sm">
                        <div class="progress-bar progress-bar-success " data-toggle="tooltip"
                             data-original-title="<?= round(($fully_paid / count($all_invoices)) * 100) ?>%"
                             style="width: <?= ($fully_paid / count($all_invoices)) * 100 ?>%"></div>
                    </div>
                </div>
            </div>
            <!-- END widget-->
        </div>
        <div class="col-lg-5ths">
            <!-- START widget-->
            <div class="panel widget">
                <div class="pl-sm pr-sm pb-sm">
                    <strong><a style="font-size: 15px"
                               href="<?= base_url() ?>client/invoice/manage_invoice/filter_by/partially_paid"><?= lang('partially_paid') ?></a>
                        <small class="pull-right " style="padding-top: 2px"> <?= $partially_paid ?>
                            / <?= count($all_invoices) ?></small>
                    </strong>
                    <div class="progress progress-striped progress-xs mb-sm">
                        <div class="progress-bar progress-bar-primary " data-toggle="tooltip"
                             data-original-title="<?= round(($partially_paid / count($all_invoices)) * 100) ?>%"
                             style="width: <?= ($partially_paid / count($all_invoices)) * 100 ?>%"></div>
                    </div>
                </div>
            </div>
            <!-- END widget-->
        </div>
        <div class="col-lg-5ths">
            <!-- START widget-->
            <div class="panel widget">
                <div class="pl-sm pr-sm pb-sm">
                    <strong><a style="font-size: 15px"
                               href="<?= base_url() ?>client/invoice/manage_invoice/filter_by/overdue"><?= lang('overdue') ?></a>
                        <small class="pull-right " style="padding-top: 2px"> <?= $overdue ?>
                            / <?= count($all_invoices) ?></small>
                    </strong>
                    <div class="progress progress-striped progress-xs mb-sm">
                        <div class="progress-bar progress-bar-warning " data-toggle="tooltip"
                             data-original-title="<?= round(($overdue / count($all_invoices)) * 100) ?>%"
                             style="width: <?= round(($overdue / count($all_invoices)) * 100) ?>%"></div>
                    </div>
                </div>
            </div>
            <!-- END widget-->
        </div>
        <div class="col-lg-5ths pr-lg">
            <!-- START widget-->
            <div class="panel widget">
                <div class="pl-sm pr-sm pb-sm">
                    <strong><a style="font-size: 15px"
                               href="<?= base_url() ?>client/invoice/manage_invoice/filter_by/draft"><?= lang('draft') ?></a>
                        <small class="pull-right " style="padding-top: 2px"> <?= $draft ?>
                            / <?= count($all_invoices) ?></small>
                    </strong>
                    <div class="progress progress-striped progress-xs mb-sm">
                        <div class="progress-bar progress-bar-aqua " data-toggle="tooltip"
                             data-original-title="<?= round(($draft / count($all_invoices)) * 100) ?>%"
                             style="width: <?= ($draft / count($all_invoices)) * 100 ?>%"></div>
                    </div>
                </div>
            </div>
            <!-- END widget-->
        </div>
    </div>
<?php } ?>
<?= message_box('success'); ?>
<div class="panel panel-custom">
    <div class="panel-heading">
        <h3 class="panel-title"><?= lang('all_invoices') ?></h3>
    </div>
    <div class="table-responsive">
        <table class="table table-striped DataTables " id="DataTables" cellspacing="0" width="100%">
            <thead>
            <tr>
                <th><?= lang('invoice') ?></th>
                <th class="col-date"><?= lang('due_date') ?></th>
                <th class="col-currency"><?= lang('amount') ?></th>
                <th class="col-currency"><?= lang('due_amount') ?></th>
                <th><?= lang('status') ?></th>
            </tr>
            </thead>
            <tbody>
            <?php
            $type = $this->uri->segment(5);
            if (!empty($type) && !is_numeric($type)) {
                $filterBy = $type;
            } else {
                $filterBy = null;
            }

            if (!empty($all_invoices_info)) {
                foreach ($all_invoices_info as $invoices) {
                    if (!empty($filterBy) && $filterBy != 'overdue') {
                        if ($this->invoice_model->get_payment_status($invoices->invoices_id) == lang($filterBy)) {
                            $v_invoices = $invoices;
                        } else {
                            $v_invoices = null;
                        }
                    } elseif (!empty($filterBy) && $filterBy == 'overdue') {
                        if (strtotime($invoices->due_date) < time() AND $payment_status != lang('fully_paid')) {
                            $v_invoices = $invoices;
                        } else {
                            $v_invoices = null;
                        }
                    } else {
                        $v_invoices = $invoices;
                    }
                    if (!empty($v_invoices)) {
                        if ($this->invoice_model->get_payment_status($v_invoices->invoices_id) == lang('fully_paid')) {
                            $invoice_status = lang('fully_paid');
                            $label = "success";
                        } elseif ($this->invoice_model->get_payment_status($v_invoices->invoices_id) == lang('draft')) {
                            $invoice_status = lang('draft');
                            $label = "default";
                        } elseif ($this->invoice_model->get_payment_status($v_invoices->invoices_id) == lang('partially_paid')) {
                            $invoice_status = lang('partially_paid');
                            $label = "warning";
                        } elseif ($v_invoices->emailed == 'Yes') {
                            $invoice_status = lang('sent');
                            $label = "info";
                        } else {
                            $invoice_status = $this->invoice_model->get_payment_status($v_invoices->invoices_id);
                            $label = "danger";
                        }
                        ?>
                        <tr>


                            <td><a class="text-info"
                                   href="<?= base_url() ?>client/invoice/manage_invoice/invoice_details/<?= $v_invoices->invoices_id ?>"><?= $v_invoices->reference_no ?></a>
                            </td>
                            <td><?= strftime(config_item('date_format'), strtotime($v_invoices->due_date)) ?></td>
                            <?php $currency = $this->invoice_model->client_currency_sambol($v_invoices->client_id); ?>
                            <td><?= display_money($this->invoice_model->calculate_to('invoice_cost', $v_invoices->invoices_id), $currency->symbol) ?></td>
                            <td><?= display_money($this->invoice_model->calculate_to('invoice_due', $v_invoices->invoices_id), $currency->symbol) ?></td>
                            <td><span class="label label-<?= $label ?>"><?= $invoice_status ?></span>
                                <?php if ($v_invoices->recurring == 'Yes') { ?>
                                    <span class="label label-primary"><i class="fa fa-retweet"></i></span>
                                <?php } ?>

                            </td>


                        </tr>
                        <?php
                    }
                }
            }
            ?>
            </tbody>
        </table>
    </div>
</div>