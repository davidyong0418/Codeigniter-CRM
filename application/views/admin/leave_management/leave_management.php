<?php include_once 'asset/admin-ajax.php';
$created = can_action('72', 'created');
$edited = can_action('72', 'edited');
$deleted = can_action('72', 'deleted');
$office_hours = config_item('office_hours');

?>
<?= message_box('success'); ?>
<?= message_box('error'); ?>
    <div class=" mt-lg">
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
                <li class="<?= $active == 1 ? 'active' : '' ?>"><a href="#pending_approval"
                                                                   data-toggle="tab"><?= lang('pending') . ' ' . lang('approval') ?></a>
                </li>

                <li class="<?= $active == 2 ? 'active' : '' ?>"><a href="#my_leave"
                                                                   data-toggle="tab"><?= lang('my_leave') ?></a></li>

                <?php if ($this->session->userdata('user_type') == 1) { ?>
                    <li class="<?= $active == 3 ? 'active' : '' ?>"><a href="#all_leave"
                                                                       data-toggle="tab"><?= lang('all_leave') ?></a>
                    </li>
                <?php } ?>
                <li class="<?= $active == 4 ? 'active' : '' ?>"><a href="#leave_report"
                                                                   data-toggle="tab"><?= lang('leave_report') ?></a>
                </li>
                <li class="pull-right">
                    <a href="<?= base_url() ?>admin/leave_management/apply_leave"
                       class="bg-info"
                       data-toggle="modal" data-placement="top" data-target="#myModal_extra_lg">
                        <i class="fa fa-plus "></i> <?= lang('apply') . ' ' . lang('leave') ?>
                    </a>
                </li>
            </ul>
            <div class="tab-content" style="border: 0;padding:0;">
                <div class="tab-pane <?= $active == 1 ? 'active' : '' ?>" id="pending_approval"
                     style="position: relative;">
                    <div class="panel panel-custom">
                        <div class="panel-body">
                            <div class="table-responsive">
                                <table class="table table-striped DataTables " id="DataTables" cellspacing="0"
                                       width="100%">
                                    <thead>
                                    <tr>
                                        <th><?= lang('name') ?></th>
                                        <th><?= lang('leave_category') ?></th>
                                        <th><?= lang('date') ?></th>
                                        <th><?= lang('duration') ?></th>
                                        <th><?= lang('status') ?></th>
                                        <?php $show_custom_fields = custom_form_table(17, null);
                                        if (!empty($show_custom_fields)) {
                                            foreach ($show_custom_fields as $c_label => $v_fields) {
                                                if (!empty($c_label)) {
                                                    ?>
                                                    <th><?= $c_label ?> </th>
                                                <?php }
                                            }
                                        }
                                        ?>
                                        <?php if ($this->session->userdata('user_type') == 1) { ?>
                                            <th class="col-sm-2"><?= lang('action') ?></th>
                                        <?php } ?>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    $my_details = MyDetails();

                                    $designation_info = $this->application_model->check_by(array('designations_id' => $my_details->designations_id), 'tbl_designations');
                                    if (!empty($designation_info)) {
                                        $dept_head = $this->application_model->check_by(array('departments_id' => $designation_info->departments_id), 'tbl_departments');
                                    }

                                    if ($this->session->userdata('user_type') == 1 || !empty($dept_head) && $dept_head->department_head_id == $my_details->user_id) {
                                        $all_pending_leave = $this->db->where('application_status', 1)->get('tbl_leave_application')->result();
                                    } else {
                                        $all_pending_leave = $this->db->where(array('application_status' => 1, 'user_id' => $this->session->userdata('user_id')))->get('tbl_leave_application')->result();
                                    }

                                    if (!empty($all_pending_leave)) {
                                        foreach ($all_pending_leave as $v_pending):
                                            if ($this->session->userdata('user_type') != 1 && !empty($dept_head) && $dept_head->department_head_id == $my_details->user_id) {
                                                $staff_details = MyDetails($v_pending->user_id);
                                                if ($staff_details->departments_id == $dept_head->departments_id) {
                                                    $v_pending = $v_pending;
                                                } else {
                                                    $v_pending = null;
                                                }
                                            }
                                            if (!empty($v_pending)) {
                                                $p_profile = $this->db->where('user_id', $v_pending->user_id)->get('tbl_account_details')->row();
                                                $p_leave_category = $this->db->where('leave_category_id', $v_pending->leave_category_id)->get('tbl_leave_category')->row();
                                                ?>
                                                <tr id="table_leave_m_<?= $v_pending->leave_application_id ?>">
                                                    <td><?= $p_profile->fullname ?></td>
                                                    <td><?= $p_leave_category->leave_category ?></td>
                                                    <td><?= strftime(config_item('date_format'), strtotime($v_pending->leave_start_date)) ?>
                                                        <?php
                                                        if ($v_pending->leave_type == 'multiple_days') {
                                                            if (!empty($v_pending->leave_end_date)) {
                                                                echo lang('TO') . ' ' . strftime(config_item('date_format'), strtotime($v_pending->leave_end_date));
                                                            }
                                                        } ?>
                                                    </td>
                                                    <td><?php
                                                        if ($v_pending->leave_type == 'single_day') {
                                                            echo ' 1 ' . lang('day') . ' (<span class="text-danger">' . $office_hours . '.00' . lang('hours') . '</span>)';
                                                        }
                                                        if ($v_pending->leave_type == 'multiple_days') {
                                                            $ge_days = 0;
                                                            $m_days = 0;

                                                            $month = cal_days_in_month(CAL_GREGORIAN, date('m', strtotime($v_pending->leave_start_date)), date('Y', strtotime($v_pending->leave_start_date)));
                                                            $datetime1 = new DateTime($v_pending->leave_start_date);
                                                            if (empty($v_pending->leave_end_date)) {
                                                                $v_pending->leave_end_date = $v_pending->leave_start_date;
                                                            }
                                                            $datetime2 = new DateTime($v_pending->leave_end_date);
                                                            $difference = $datetime1->diff($datetime2);
                                                            if ($difference->m != 0) {
                                                                $m_days += $month;
                                                            } else {
                                                                $m_days = 0;
                                                            }
                                                            $ge_days += $difference->d + 1;
                                                            $total_token = $m_days + $ge_days;
                                                            echo $total_token . ' ' . lang('days') . ' (<span class="text-danger">' . $total_token * $office_hours . '.00' . lang('hours') . '</span>)';
                                                        }
                                                        if ($v_pending->leave_type == 'hours') {
                                                            $total_hours = ($v_pending->hours / $office_hours);
                                                            echo number_format($total_hours, 2) . ' ' . lang('days') . ' (<span class="text-danger">' . $v_pending->hours . '.00' . lang('hours') . '</span>)';
                                                        }
                                                        ?>
                                                    </td>
                                                    <td><?php
                                                        if ($v_pending->application_status == '1') {
                                                            echo '<span class="label label-warning">' . lang('pending') . '</span>';
                                                        } elseif ($v_pending->application_status == '2') {
                                                            echo '<span class="label label-success">' . lang('accepted') . '</span>';
                                                        } else {
                                                            echo '<span class="label label-danger">' . lang('rejected') . '</span>';
                                                        }
                                                        ?></td>
                                                    <?php $show_custom_fields = custom_form_table(17, $v_pending->leave_application_id);
                                                    if (!empty($show_custom_fields)) {
                                                        foreach ($show_custom_fields as $c_label => $v_fields) {
                                                            if (!empty($c_label)) {
                                                                ?>
                                                                <td><?= $v_fields ?> </td>
                                                            <?php }
                                                        }
                                                    }
                                                    ?>
                                                    <td>
                                                        <?php echo btn_view_modal('admin/leave_management/view_details/' . $v_pending->leave_application_id) ?>
                                                        <?php if ($v_pending->application_status != '2') { ?>
                                                            <?php echo ajax_anchor(base_url("admin/leave_management/delete_application/" . $v_pending->leave_application_id), "<i class='btn btn-xs btn-danger fa fa-trash-o'></i>", array("class" => "", "title" => lang('delete'), "data-fade-out-on-success" => "#table_leave_m_" . $v_pending->leave_application_id)); ?>
                                                        <?php } ?>
                                                    </td>
                                                </tr>
                                                <?php
                                            }
                                        endforeach;
                                    }
                                    ?>

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane <?= $active == 2 ? 'active' : '' ?>" id="my_leave" style="position: relative;">
                    <div class="panel panel-custom">
                        <div class="panel-body">
                            <div class="table-responsive">
                                <table class="table table-striped DataTables " id="DataTables" cellspacing="0"
                                       width="100%">
                                    <thead>
                                    <tr>
                                        <th><?= lang('name') ?></th>
                                        <th><?= lang('leave_category') ?></th>
                                        <th><?= lang('date') ?></th>
                                        <th><?= lang('duration') ?></th>
                                        <th><?= lang('status') ?></th>
                                        <?php $show_custom_fields = custom_form_table(17, null);
                                        if (!empty($show_custom_fields)) {
                                            foreach ($show_custom_fields as $c_label => $v_fields) {
                                                if (!empty($c_label)) {
                                                    ?>
                                                    <th><?= $c_label ?> </th>
                                                <?php }
                                            }
                                        }
                                        ?>
                                        <?php if ($this->session->userdata('user_type') == 1) { ?>
                                            <th class="col-sm-2"><?= lang('action') ?></th>
                                        <?php } ?>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    $my_leave_application = $this->db->where('user_id', $this->session->userdata('user_id'))->get('tbl_leave_application')->result();
                                    if (!empty($my_leave_application)) {
                                        foreach ($my_leave_application as $v_my_leave):
                                            $my_profile = $this->db->where('user_id', $v_my_leave->user_id)->get('tbl_account_details')->row();
                                            $my_leave_category = $this->db->where('leave_category_id', $v_my_leave->leave_category_id)->get('tbl_leave_category')->row();
                                            ?>
                                            <tr id="table_leave_my_<?= $v_my_leave->leave_application_id ?>">
                                                <td><?= $my_profile->fullname ?></td>
                                                <td><?= $my_leave_category->leave_category ?></td>
                                                <td><?= strftime(config_item('date_format'), strtotime($v_my_leave->leave_start_date)) ?>
                                                    <?php
                                                    if ($v_my_leave->leave_type == 'multiple_days') {
                                                        if (!empty($v_my_leave->leave_end_date)) {
                                                            echo lang('TO') . ' ' . strftime(config_item('date_format'), strtotime($v_my_leave->leave_end_date));
                                                        }
                                                    } ?>
                                                </td>
                                                <td><?php
                                                    if ($v_my_leave->leave_type == 'single_day') {
                                                        echo ' 1 ' . lang('day') . ' (<span class="text-danger">' . $office_hours . '.00' . lang('hours') . '</span>)';
                                                    }
                                                    if ($v_my_leave->leave_type == 'multiple_days') {
                                                        $ge_days = 0;
                                                        $m_days = 0;

                                                        $month = cal_days_in_month(CAL_GREGORIAN, date('m', strtotime($v_my_leave->leave_start_date)), date('Y', strtotime($v_my_leave->leave_start_date)));
                                                        $datetime1 = new DateTime($v_my_leave->leave_start_date);
                                                        if (empty($v_my_leave->leave_end_date)) {
                                                            $v_my_leave->leave_end_date = $v_my_leave->leave_start_date;
                                                        }
                                                        $datetime2 = new DateTime($v_my_leave->leave_end_date);
                                                        $difference = $datetime1->diff($datetime2);
                                                        if ($difference->m != 0) {
                                                            $m_days += $month;
                                                        } else {
                                                            $m_days = 0;
                                                        }
                                                        $ge_days += $difference->d + 1;
                                                        $total_token = $m_days + $ge_days;
                                                        echo $total_token . ' ' . lang('days') . ' (<span class="text-danger">' . $total_token * $office_hours . '.00' . lang('hours') . '</span>)';
                                                    }
                                                    if ($v_my_leave->leave_type == 'hours') {
                                                        $total_hours = ($v_my_leave->hours / $office_hours);
                                                        echo number_format($total_hours, 2) . ' ' . lang('days') . ' (<span class="text-danger">' . $v_my_leave->hours . '.00' . lang('hours') . '</span>)';
                                                    }
                                                    ?>
                                                </td>
                                                <td><?php
                                                    if ($v_my_leave->application_status == '1') {
                                                        echo '<span class="label label-warning">' . lang('pending') . '</span>';
                                                    } elseif ($v_my_leave->application_status == '2') {
                                                        echo '<span class="label label-success">' . lang('accepted') . '</span>';
                                                    } else {
                                                        echo '<span class="label label-danger">' . lang('rejected') . '</span>';
                                                    }
                                                    ?></td>
                                                <?php $show_custom_fields = custom_form_table(17, $v_my_leave->leave_application_id);
                                                if (!empty($show_custom_fields)) {
                                                    foreach ($show_custom_fields as $c_label => $v_fields) {
                                                        if (!empty($c_label)) {
                                                            ?>
                                                            <td><?= $v_fields ?> </td>
                                                        <?php }
                                                    }
                                                }
                                                ?>
                                                <?php if ($this->session->userdata('user_type') == 1) { ?>
                                                    <td>
                                                        <?php echo btn_view_modal('admin/leave_management/view_details/' . $v_my_leave->leave_application_id) ?>
                                                        <?php if ($v_my_leave->application_status != '2') { ?>
                                                            <?php echo ajax_anchor(base_url("admin/leave_management/delete_application/" . $v_my_leave->leave_application_id), "<i class='btn btn-xs btn-danger fa fa-trash-o'></i>", array("class" => "", "title" => lang('delete'), "data-fade-out-on-success" => "#table_leave_my_" . $v_my_leave->leave_application_id)); ?>
                                                        <?php } ?>
                                                    </td>
                                                <?php } ?>
                                            </tr>
                                            <?php
                                        endforeach;
                                    }
                                    ?>

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                </div>
                <?php if ($this->session->userdata('user_type') == 1) { ?>
                    <div class="tab-pane <?= $active == 2 ? 'active' : '' ?>" id="all_leave"
                         style="position: relative;">
                        <div class="panel panel-custom">
                            <div class="panel-body">
                                <div class="table-responsive">
                                    <table class="table table-striped DataTables " id="DataTables" cellspacing="0"
                                           width="100%">
                                        <thead>
                                        <tr>
                                            <th><?= lang('name') ?></th>
                                            <th><?= lang('leave_category') ?></th>
                                            <th><?= lang('date') ?></th>
                                            <th><?= lang('duration') ?></th>
                                            <th><?= lang('status') ?></th>
                                            <?php $show_custom_fields = custom_form_table(17, null);
                                            if (!empty($show_custom_fields)) {
                                                foreach ($show_custom_fields as $c_label => $v_fields) {
                                                    if (!empty($c_label)) {
                                                        ?>
                                                        <th><?= $c_label ?> </th>
                                                    <?php }
                                                }
                                            }
                                            ?>
                                            <?php if ($this->session->userdata('user_type') == 1) { ?>
                                                <th class="col-sm-2"><?= lang('action') ?></th>
                                            <?php } ?>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php
                                        $all_leave_application = $this->db->get('tbl_leave_application')->result();
                                        if (!empty($all_leave_application)) {
                                            foreach ($all_leave_application as $v_all_leave):
                                                $my_profile = $this->db->where('user_id', $v_all_leave->user_id)->get('tbl_account_details')->row();
                                                $a_leave_category = $this->db->where('leave_category_id', $v_all_leave->leave_category_id)->get('tbl_leave_category')->row();
                                                ?>
                                                <tr id="table_leave_all_<?= $v_all_leave->leave_application_id ?>">
                                                    <td><?= $my_profile->fullname ?></td>
                                                    <td><?= $a_leave_category->leave_category ?></td>
                                                    <td><?= strftime(config_item('date_format'), strtotime($v_all_leave->leave_start_date)) ?>
                                                        <?php
                                                        if ($v_all_leave->leave_type == 'multiple_days') {
                                                            if (!empty($v_all_leave->leave_end_date)) {
                                                                echo lang('TO') . ' ' . strftime(config_item('date_format'), strtotime($v_all_leave->leave_end_date));
                                                            }
                                                        } ?>
                                                    </td>
                                                    <td><?php
                                                        if ($v_all_leave->leave_type == 'single_day') {
                                                            echo ' 1 ' . lang('day') . ' (<span class="text-danger">' . $office_hours . '.00' . lang('hours') . '</span>)';
                                                        }
                                                        if ($v_all_leave->leave_type == 'multiple_days') {
                                                            $ge_days = 0;
                                                            $m_days = 0;

                                                            $month = cal_days_in_month(CAL_GREGORIAN, date('m', strtotime($v_all_leave->leave_start_date)), date('Y', strtotime($v_all_leave->leave_start_date)));
                                                            $datetime1 = new DateTime($v_all_leave->leave_start_date);
                                                            if (empty($v_all_leave->leave_end_date)) {
                                                                $v_all_leave->leave_end_date = $v_all_leave->leave_start_date;
                                                            }
                                                            $datetime2 = new DateTime($v_all_leave->leave_end_date);
                                                            $difference = $datetime1->diff($datetime2);
                                                            if ($difference->m != 0) {
                                                                $m_days += $month;
                                                            } else {
                                                                $m_days = 0;
                                                            }
                                                            $ge_days += $difference->d + 1;
                                                            $total_token = $m_days + $ge_days;
                                                            echo $total_token . ' ' . lang('days') . ' (<span class="text-danger">' . $total_token * $office_hours . '.00' . lang('hours') . '</span>)';
                                                        }
                                                        if ($v_all_leave->leave_type == 'hours') {
                                                            $total_hours = ($v_all_leave->hours / $office_hours);
                                                            echo number_format($total_hours, 2) . ' ' . lang('days') . ' (<span class="text-danger">' . $v_all_leave->hours . '.00' . lang('hours') . '</span>)';
                                                        }
                                                        ?>
                                                    </td>
                                                    <td><?php
                                                        if ($v_all_leave->application_status == '1') {
                                                            echo '<span class="label label-warning">' . lang('pending') . '</span>';
                                                        } elseif ($v_all_leave->application_status == '2') {
                                                            echo '<span class="label label-success">' . lang('accepted') . '</span>';
                                                        } else {
                                                            echo '<span class="label label-danger">' . lang('rejected') . '</span>';
                                                        }
                                                        ?></td>
                                                    <?php $show_custom_fields = custom_form_table(17, $v_all_leave->leave_application_id);
                                                    if (!empty($show_custom_fields)) {
                                                        foreach ($show_custom_fields as $c_label => $v_fields) {
                                                            if (!empty($c_label)) {
                                                                ?>
                                                                <td><?= $v_fields ?> </td>
                                                            <?php }
                                                        }
                                                    }
                                                    ?>
                                                    <?php if ($this->session->userdata('user_type') == 1) { ?>
                                                        <td>
                                                            <?php echo btn_view_modal('admin/leave_management/view_details/' . $v_all_leave->leave_application_id) ?>
                                                            <?php if ($v_all_leave->application_status != '2') { ?>
                                                                <?php echo ajax_anchor(base_url("admin/leave_management/delete_application/" . $v_all_leave->leave_application_id), "<i class='btn btn-xs btn-danger fa fa-trash-o'></i>", array("class" => "", "title" => lang('delete'), "data-fade-out-on-success" => "#table_leave_all_" . $v_all_leave->leave_application_id)); ?>
                                                            <?php } ?>
                                                        </td>
                                                    <?php } ?>
                                                </tr>
                                                <?php
                                            endforeach;
                                        }
                                        ?>

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } ?>
                <div class="tab-pane <?= $active == 4 ? 'active' : '' ?>" id="leave_report" style="position: relative;">
                    <div class="panel panel-custom">
                        <div class="panel-body">
                            <?php if ($this->session->userdata('user_type') == 1) { ?>
                                <div id="panelChart5">
                                    <div class="row panel-title pl-lg pb-sm"
                                         style="border-bottom: 1px solid #a0a6ad"><?= lang('all') . ' ' . lang('leave_report') ?></div>
                                    <div class="chart-pie flot-chart"></div>
                                </div>
                            <?php } ?>

                            <div id="panelChart5">
                                <div class="row panel-title pl-lg pb-sm"
                                     style="border-bottom: 1px solid #a0a6ad"><?= lang('my_leave') . ' ' . lang('report') ?></div>
                                <div class="chart-pie-my flot-chart"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php
$all_category = $this->db->get('tbl_leave_category')->result();
$color = array('37bc9b', '7266ba', 'f05050', 'ff902b', '7266ba', 'f532e5', '5d9cec', '7cd600', '91ca00', 'ff7400', '1cc200', 'bb9000', '40c400');
if (!empty($all_category)) {

    ?>
    <script src="<?= base_url() ?>assets/plugins/Flot/jquery.flot.js"></script>
    <script src="<?= base_url() ?>assets/plugins/Flot/jquery.flot.tooltip.min.js"></script>
    <script src="<?= base_url() ?>assets/plugins/Flot/jquery.flot.resize.js"></script>
    <script src="<?= base_url() ?>assets/plugins/Flot/jquery.flot.pie.js"></script>
    <script src="<?= base_url() ?>assets/plugins/Flot/jquery.flot.time.js"></script>
    <script src="<?= base_url() ?>assets/plugins/Flot/jquery.flot.categories.js"></script>
    <script src="<?= base_url() ?>assets/plugins/Flot/jquery.flot.spline.min.js"></script>
    <script type="text/javascript">

        // CHART PIE
        // -----------------------------------
        <?php if(!empty($leave_report)){?>

        (function (window, document, $, undefined) {
            $(function () {
                var data = [
                    <?php
                    if(!empty($all_category)){
                    foreach ($all_category as $key => $v_category) {
                    if (!empty($leave_report['leave_taken'][$key])) {
                    $all_report = $leave_report['leave_taken'][$key];
                    ?>
                    {
                        "label": "<?= $v_category->leave_category . ' ( <small>' . lang('quota') . ': ' . $leave_report['leave_quota'][$key] . ' ' . lang('taken') . ': ' . $all_report . '</small>)'?>",
                        "color": "#<?=$color[$key] ?>",
                        "data": <?= $all_report ?>
                    },
                    <?php }
                    }
                    }?>
                ];
                var options = {
                    series: {
                        pie: {
                            show: true,
                            innerRadius: 0,
                            label: {
                                show: true,
                                radius: 0.8,
                                formatter: function (label, series) {
                                    return '<div class="flot-pie-label">' +
                                            //label + ' : ' +
                                        Math.round(series.percent) +
                                        '%</div>';
                                },
                                background: {
                                    opacity: 0.8,
                                    color: '#222'
                                }
                            }
                        }
                    }
                };

                var chart = $('.chart-pie');
                if (chart.length)
                    $.plot(chart, data, options);

            });

        })(window, document, window.jQuery);
        <?php }?>

        <?php

        if(!empty($my_leave_report)){?>
        // CHART PIE
        // -----------------------------------
        (function (window, document, $, undefined) {

            $(function () {
                var data = [
                    <?php
                    if(!empty($all_category)){
                    foreach ($all_category as $key => $v_category) {
                    if (!empty($my_leave_report['leave_taken'][$key])) {
                    $result = $my_leave_report['leave_taken'][$key];
                    ?>
                    {
                        "label": "<?= $v_category->leave_category . ' ( <small>' . lang('quota') . ': ' . $my_leave_report['leave_quota'][$key] . ' ' . lang('taken') . ': ' . $result . '</small>)'?>",
                        "color": "#<?=$color[$key] ?>",
                        "data": <?= $result?>
                    },
                    <?php }
                    }
                    }?>
                ];

                var options = {
                    series: {
                        pie: {
                            show: true,
                            innerRadius: 0,
                            label: {
                                show: true,
                                radius: 0.8,
                                formatter: function (label, series) {
                                    return '<div class="flot-pie-label">' +
                                            //label + ' : ' +
                                        Math.round(series.percent) +
                                        '%</div>';
                                },
                                background: {
                                    opacity: 0.8,
                                    color: '#222'
                                }
                            }
                        }
                    }
                };
                var chart = $('.chart-pie-my');
                if (chart.length)
                    $.plot(chart, data, options);
            });
        })(window, document, window.jQuery);

        <?php }?>
    </script>
<?php } ?>