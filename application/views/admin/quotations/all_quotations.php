<div class="row">
    <div class="col-sm-12" data-spy="scroll" data-offset="0">
        <div class="panel panel-custom">
            <!-- Default panel contents -->
            <div class="panel-heading">
                <div class="panel-title">
                    <strong><?= lang('quotations') ?></strong>
                </div>
            </div>
            <br/>
            <div class="panel-body">
                <div class="">
                    <table class="table table-striped DataTables " id="DataTables" cellspacing="0" width="100%">
                        <thead>
                        <tr>
                            <th><?= lang('title') ?></th>
                            <th><?= lang('client') ?></th>
                            <th><?= lang('date') ?></th>
                            <th><?= lang('amount') ?></th>
                            <th><?= lang('status') ?></th>
                            <th><?= lang('generated_by') ?></th>
                            <th><?= lang('action') ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        if (!empty($all_quatations)) {

                            foreach ($all_quatations as $v_quatations) {
                                ?>
                                <tr>
                                    <?php
                                    $client_info = $this->quotations_model->check_by(array('client_id' => $v_quatations->client_id), 'tbl_client');

                                    $user_info = $this->quotations_model->check_by(array('user_id' => $v_quatations->user_id), 'tbl_users');
                                    if(!empty($user_info)){
                                    if ($user_info->role_id == 1) {
                                        $user = '(admin)';
                                    } elseif ($user_info->role_id == 3) {
                                        $user = '(Staff)';
                                    } else {
                                        $user = '(client)';
                                    }
                                    }else{
                                        $user = ' ';                                    
                                    }
                                    $currency = $this->quotations_model->client_currency_sambol($v_quatations->client_id);
                                     if (empty($currency)) {
                                    $currency = $this->quotations_model->check_by(array('code' => config_item('default_currency')), 'tbl_currencies');
	                                }
                                    if (!empty($client_info)) {
                                        if ($client_info->client_status == 1) {
                                            $client_status = '(' . lang('person') . ')';
                                        } else {
                                            $client_status = '(' . lang('company') . ')';
                                        }
                                    } else {
                                        $client_status = '';
                                    }
                                    ?>
                                    <td>
                                        <a href="<?= base_url() ?>admin/quotations/quotations_details/<?= $v_quatations->quotations_id ?>"><?= $v_quatations->quotations_form_title; ?></a>
                                    </td>
                                    <td><?= $v_quatations->name . ' ' . $client_status; ?></td>
                                    <td><?= strftime(config_item('date_format'), strtotime($v_quatations->quotations_date)) ?></td>
                                    <td>
                                        <?php
                                        if (!empty($v_quatations->quotations_amount)) {
                                            echo display_money($v_quatations->quotations_amount, $currency->symbol);
                                        }
                                        ?>
                                        </td>
                                    <td><?php
                                        if ($v_quatations->quotations_status == 'completed') {
                                            echo '<span class="label label-success">' . lang('completed') . '</span>';
                                        } else {
                                            echo '<span class="label label-danger">' . lang('pending') . '</span>';
                                        };
                                        ?></td>
                                    <td><?= (!empty($user_info->username) ? $user_info->username : '-') . ' ' . $user; ?> </td>
                                    <td>
                                        <?= btn_view('admin/quotations/quotations_details/' . $v_quatations->quotations_id) ?>
                                        <?= btn_delete('admin/quotations/index/delete_quotations/' . $v_quatations->quotations_id) ?>
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
        </div>
    </div>
</div>