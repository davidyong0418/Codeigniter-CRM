<?= message_box('success'); ?>
<?= message_box('error'); ?>

<?php
$curency = $this->proposal_model->check_by(array('code' => config_item('default_currency')), 'tbl_currencies'); ?>
<div class="panel panel-custom">
    <header class="panel-heading ">
        <div class="panel-title"><strong><?= lang('all_proposals') ?></strong></div>
    </header>

    <div class="table-responsive">
        <table class="table table-striped DataTables " id="DataTables" cellspacing="0"
               width="100%">
            <thead>
            <tr>
                <th><?= lang('proposal') ?> #</th>
                <th><?= lang('proposal_date') ?></th>
                <th><?= lang('expire_date') ?></th>
                <th><?= strtoupper(lang('TO')) ?></th>
                <th><?= lang('amount') ?></th>
                <th><?= lang('status') ?></th>
            </tr>
            </thead>
            <tbody>
            <?php

            if (!empty($all_proposals_info)) {
                foreach ($all_proposals_info as $v_proposals) {
                    if ($v_proposals->status == 'revised') {
                        $label = "info";
                    } elseif ($v_proposals->status == 'accepted') {
                        $label = "success";
                    } else {
                        $label = "danger";
                    }
                    ?>
                    <tr>
                        <td>
                            <a class="text-info"
                               href="<?= base_url() ?>client/proposals/index/proposals_details/<?= $v_proposals->proposals_id ?>"><?= $v_proposals->reference_no ?></a>
                            <?php if ($v_proposals->convert == 'Yes') {
                                if ($v_proposals->convert_module == 'invoice') {
                                    $c_url = base_url() . 'client/invoice/manage_invoice/invoice_details/' . $v_proposals->convert_module_id;
                                    $text = lang('invoiced');
                                } else {
                                    $text = lang('estimated');
                                    $c_url = base_url() . 'client/estimates/index/estimates_details/' . $v_proposals->convert_module_id;
                                }
                                if (!empty($c_url)) { ?>
                                    <p class="text-sm m0 p0">
                                        <a class="text-success"
                                           href="<?= $c_url ?>">
                                            <?= $text ?>
                                        </a>
                                    </p>
                                <?php }
                            } ?>
                        </td>
                        <td><?= strftime(config_item('date_format'), strtotime($v_proposals->proposal_date)) ?></td>
                        <td><?= strftime(config_item('date_format'), strtotime($v_proposals->due_date)) ?>
                            <?php
                            if (strtotime($v_proposals->due_date) < time() AND $v_proposals->status == 'pending' || strtotime($v_proposals->due_date) < time() AND $v_proposals->status == ('draft')) { ?>
                                <span class="label label-danger "><?= lang('expired') ?></span>
                            <?php }
                            ?>
                        </td>
                        <?php
                        if ($v_proposals->module == 'client') {
                            $client_info = $this->proposal_model->check_by(array('client_id' => $v_proposals->module_id), 'tbl_client');
                            $client_name = $client_info->name;
                            $currency = $this->proposal_model->client_currency_sambol($v_proposals->module_id);
                        } else if ($v_proposals->module == 'leads') {
                            $client_info = $this->proposal_model->check_by(array('leads_id' => $v_proposals->module_id), 'tbl_leads');
                            $client_name = $client_info->lead_name;
                            $currency = $this->proposal_model->check_by(array('code' => config_item('default_currency')), 'tbl_currencies');
                        } else {
                            $client_name = '-';
                            $currency = $this->proposal_model->check_by(array('code' => config_item('default_currency')), 'tbl_currencies');
                        }

                        ?>
                        <td><?= $client_name ?></td>
                        <?php ?>
                        <td>
                            <?= display_money($this->proposal_model->proposal_calculation('total', $v_proposals->proposals_id), $currency->symbol); ?>
                        </td>
                        <td><span
                                class="label label-<?= $label ?>"><?= lang($v_proposals->status) ?></span>
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

