<?= message_box('success') ?>
<?= message_box('error') ?>
<?php
$mdate = date('Y-m-d');
$last_7_days = date('Y-m-d', strtotime('today - 7 days'));
$all_goal_tracking = $this->invoice_model->get_permission('tbl_goal_tracking');

$all_goal = 0;
$bank_goal = 0;
$complete_achivement = 0;
if (!empty($all_goal_tracking)) {
    foreach ($all_goal_tracking as $v_goal_track) {
        $goal_achieve = $this->invoice_model->get_progress($v_goal_track, true);
        if ($v_goal_track->goal_type_id == 7) {
            if ($v_goal_track->end_date <= $mdate) { // check today is last date or not

                if ($v_goal_track->email_send == 'no') {// check mail are send or not
                    if ($v_goal_track->achievement <= $goal_achieve['achievement']) {
                        if ($v_goal_track->notify_goal_achive == 'on') {// check is notify is checked or not check
                            $this->invoice_model->send_goal_mail('goal_achieve', $v_goal_track);
                        }
                    } else {
                        if ($v_goal_track->notify_goal_not_achive == 'on') {// check is notify is checked or not check
                            $this->invoice_model->send_goal_mail('goal_not_achieve', $v_goal_track);
                        }
                    }
                }
            }
            $all_goal += $v_goal_track->achievement;
            $complete_achivement += $goal_achieve['achievement'];
        }
    }
}
// 30 days before

for ($iDay = 7; $iDay >= 0; $iDay--) {
    $date = date('Y-m-d', strtotime('today - ' . $iDay . 'days'));
    $where = array('payment_date >=' => $date, 'payment_date <=' => $date);
    $invoice_result[$date] = $this->db->select_sum('amount')->where($where)->get('tbl_payments')->result();
}

$terget_achievement = $this->db->where(array('goal_type_id' => 7, 'start_date >=' => $last_7_days, 'end_date <=' => $mdate))->get('tbl_goal_tracking')->result();

$total_terget = 0;
if (!empty($terget_achievement)) {
    foreach ($terget_achievement as $v_terget) {
        $total_terget += $v_terget->achievement;
    }
}
$tolal_goal = $all_goal + $bank_goal;
$curency = $this->invoice_model->check_by(array('code' => config_item('default_currency')), 'tbl_currencies');

if ($this->session->userdata('user_type') == 1) {
    $margin = 'margin-bottom:30px';
    ?>
    <div class="">
        <div class="row bg-white p0" style="<?= $margin ?>;margin-left: 0px;margin-right: 0px">
            <div class="col-md-4">
                <div class="row row-table pv-lg">
                    <div class="col-xs-6">
                        <p class="m0 lead"><?= display_money($tolal_goal, $curency->symbol) ?></p>
                        <p class="m0">
                            <small><?= lang('achievement') ?></small>
                        </p>
                    </div>
                    <div class="col-xs-6 ">
                        <p class="m0 lead"><?= display_money($total_terget, $curency->symbol); ?></p>
                        <p class="m0">
                            <small><?= lang('last_weeks') . ' ' . lang('created') ?></small>
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="row row-table ">
                    <div class="col-xs-6">
                        <p class="m0 lead"><?= display_money($complete_achivement, $curency->symbol); ?></p>
                        <p class="m0">
                            <small><?= lang('completed') . ' ' . lang('achievements') ?></small>
                        </p>
                    </div>
                    <div class="col-xs-6 pt">
                        <div data-sparkline="" data-bar-color="#23b7e5" data-height="60" data-bar-width="8"
                             data-bar-spacing="6" data-chart-range-min="0" values="<?php
                        if (!empty($invoice_result)) {
                            foreach ($invoice_result as $v_invoice_result) {
                                echo $v_invoice_result[0]->amount . ',';
                            }
                        }
                        ?>">
                        </div>
                        <p class="m0">
                            <small>
                                <?php
                                if (!empty($invoice_result)) {
                                    foreach ($invoice_result as $date => $v_invoice_result) {
                                        echo date('d', strtotime($date)) . ' ';
                                    }
                                }
                                ?>
                            </small>
                        </p>

                    </div>
                </div>

            </div>
            <div class="col-md-4">
                <div class="row row-table ">
                    <div class="col-xs-6">
                        <p class="m0 lead">
                            <?php
                            if ($tolal_goal < $complete_achivement) {
                                $pending_goal = 0;
                            } else {
                                $pending_goal = $tolal_goal - $complete_achivement;
                            } ?>
                            <?= display_money($pending_goal, $curency->symbol); ?>
                        </p>
                        <p class="m0">
                            <small><?= lang('pending') . ' ' . lang('achievements') ?></small>
                        </p>
                    </div>
                    <?php
                    if (!empty($tolal_goal)) {
                        if ($tolal_goal <= $complete_achivement) {
                            $total_progress = 100;
                        } else {
                            $progress = ($complete_achivement / $tolal_goal) * 100;
                            $total_progress = round($progress);
                        }
                    } else {
                        $total_progress = 0;
                    }
                    ?>
                    <div class="col-xs-6 text-center pt">
                        <div class="inline ">
                            <div class="easypiechart text-success"
                                 data-percent="<?= $total_progress ?>"
                                 data-line-width="5" data-track-Color="#f0f0f0"
                                 data-bar-color="#<?php
                                 if ($total_progress == 100) {
                                     echo '8ec165';
                                 } elseif ($total_progress >= 40 && $total_progress <= 50) {
                                     echo '5d9cec';
                                 } elseif ($total_progress >= 51 && $total_progress <= 99) {
                                     echo '7266ba';
                                 } else {
                                     echo 'fb6b5b';
                                 }
                                 ?>" data-rotate="270" data-scale-Color="false"
                                 data-size="50"
                                 data-animate="2000">
                                                        <span class="small "><?= $total_progress ?>
                                                            %</span>
                                <span class="easypie-text"><strong><?= lang('done') ?></strong></span>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
<?php }
$edited = can_action('13', 'edited');
$deleted = can_action('13', 'deleted');
?>

<section class="panel panel-custom ">
    <header class="panel-heading"><?= lang('all_payments') ?>
        <div class="pull-right">
            <a data-toggle="modal" data-target="#myModal"
               href="<?= base_url() ?>admin/invoice/zipped/payment"
               class="btn btn-success btn-xs ml-lg"><?= lang('zip_payment') ?></a>
            <a data-toggle="modal" data-target="#myModal"
               href="<?= base_url() ?>admin/invoice/make_payment"
               class="btn btn-danger btn-xs ml-lg"><?= lang('make_payment') ?></a>
        </div>
    </header>

    <div class="panel-body">
        <table class="table table-striped DataTables " id="DataTables" cellspacing="0" width="100%">
            <thead>
            <tr>
                <th><?= lang('payment_date') ?></th>
                <th><?= lang('invoice_date') ?></th>
                <th><?= lang('invoice') ?></th>
                <th><?= lang('client') ?></th>
                <th><?= lang('amount') ?></th>
                <th><?= lang('payment_method') ?></th>
                <?php if (!empty($edited) || !empty($deleted)) { ?>
                    <th class="hidden-print"><?= lang('action') ?></th>
                <?php } ?>
            </tr>
            </thead>
            <tbody>
            <?php
            if (!empty($all_invoice_info)) {
                foreach ($all_invoice_info as $v_invoice) {
                    if (!empty($v_invoice)) {

                        $can_edit = $this->invoice_model->can_action('tbl_invoices', 'edit', array('invoices_id' => $v_invoice->invoices_id));
                        $can_delete = $this->invoice_model->can_action('tbl_invoices', 'delete', array('invoices_id' => $v_invoice->invoices_id));

                        $all_payment_info = $this->db->where('invoices_id', $v_invoice->invoices_id)->get('tbl_payments')->result();
                        if (!empty($all_payment_info)):foreach ($all_payment_info as $v_payments_info):
                            ?>
                            <tr id="table_payments_<?= $v_payments_info->payments_id ?>">
                                <?php
                                $client_info = $this->invoice_model->check_by(array('client_id' => $v_invoice->client_id), 'tbl_client');
                                if (!empty($client_info)) {
                                    $c_name = $client_info->name;
                                    $currency = $this->invoice_model->client_currency_sambol($v_invoice->client_id);
                                } else {
                                    $c_name = '-';
                                    $currency = $this->invoice_model->check_by(array('code' => config_item('default_currency')), 'tbl_currencies');
                                }
                                $payment_methods = $this->invoice_model->check_by(array('payment_methods_id' => $v_payments_info->payment_method), 'tbl_payment_methods');

                                ?>

                                <td>
                                    <a href="<?= base_url() ?>admin/invoice/manage_invoice/payments_details/<?= $v_payments_info->payments_id ?>"> <?= strftime(config_item('date_format'), strtotime($v_payments_info->payment_date)); ?></a>
                                </td>
                                <td><?= strftime(config_item('date_format'), strtotime($v_invoice->date_saved)) ?></td>
                                <td><a class="text-info"
                                       href="<?= base_url() ?>admin/invoice/manage_invoice/invoice_details/<?= $v_payments_info->invoices_id ?>"><?= $v_invoice->reference_no; ?></a>
                                </td>

                                <td><?= $c_name; ?></td>
                                <td><?= display_money($v_payments_info->amount, $currency->symbol); ?> </td>
                                <td><?= !empty($payment_methods->method_name) ? $payment_methods->method_name : '-' ?></td>
                                <?php if (!empty($edited) || !empty($deleted)) { ?>
                                    <td>
                                        <?php if (!empty($can_edit) && !empty($edited)) { ?>
                                            <?= btn_edit('admin/invoice/all_payments/' . $v_payments_info->payments_id) ?>
                                        <?php }
                                        if (!empty($can_delete) && !empty($deleted)) {
                                            ?>
                                            <?php echo ajax_anchor(base_url("admin/invoice/delete/delete_payment/" . $v_payments_info->payments_id), "<i class='btn btn-xs btn-danger fa fa-trash-o'></i>", array("class" => "", "title" => lang('delete'), "data-fade-out-on-success" => "#table_payments_" . $v_payments_info->payments_id)); ?>
                                        <?php } ?>
                                        <?= btn_view('admin/invoice/manage_invoice/payments_details/' . $v_payments_info->payments_id) ?>
                                        <?php if (!empty($can_edit) && !empty($edited)) { ?>
                                            <a data-toggle="tooltip" data-placement="top"
                                               href="<?= base_url() ?>admin/invoice/send_payment/<?= $v_payments_info->payments_id . '/' . $v_payments_info->amount ?>"
                                               title="<?= lang('send_email') ?>"
                                               class="btn btn-xs btn-success">
                                                <i class="fa fa-envelope"></i> </a>
                                        <?php } ?>
                                    </td>
                                <?php } ?>
                            </tr>
                            <?php
                        endforeach;
                        endif;
                    }

                }
            }
            ?>
            </tbody>
        </table>
    </div>
</section>