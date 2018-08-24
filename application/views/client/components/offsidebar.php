<?php

$user_id = $this->session->userdata('user_id');
?>
<style type="text/css">
    .offsidebar {
        background-color: #3a3f51
    }
</style>
<aside class="offsidebar hide">
    <!-- START Off Sidebar (right)-->
    <div class="tab-content">
        <!-- Home tab content -->
        <div class="tab-pane active" style="background:none;" id="control-sidebar-home-tab">
            <h2 style="color: #EFF3F4;font-weight: 100;text-align: center;">
                <?php echo date("l"); ?>
                <br/>
                <?php echo date("jS F, Y"); ?>
            </h2>
            <form action="<?= base_url() ?>client/user/todo/add" method="post" class="form-horizontal form-groups"
                  style="margin-top: 40px">
                <div class="form-group col-sm-12">
                    <div class="col-sm-10 ">
                        <textarea class="form-control" type="text" name="title" placeholder="+<?= lang('add_todo') ?>"
                                  style="background-color: #364559;border: 1px solid #4F595E;color: rgba(170,170,170 ,1);"
                                  data-validate="required"></textarea>
                    </div>
                    <input type="submit" value="<?= lang('add') ?>" class="btn btn-success btn-xs col-sm-2"/>
                </div>
            </form>
            <table style="width: 83%;margin-left: 22px;">
                <?php
                $this->db->where('user_id', $user_id);
                $this->db->order_by('order', 'asc');
                $todos = $this->db->get('tbl_todo')->result_array();
                foreach ($todos as $row):
                    ?>
                    <tr>
                        <td>
                            <li id="todo_1"
                                style="<?php if ($row['status'] == 1): ?>text-decoration: line-through;<?php endif; ?>font-size: 13px;
                                    color: #B4BCBE;">
                                <?php echo $row['title']; ?>
                            </li>
                        </td>
                        <td style="text-align:right;">
                            <div class="btn-group">
                                <button type="button" class="btn btn-default btn-sm dropdown-toggle "
                                        data-toggle="dropdown"
                                        style="padding:0px;background: none;border: 0px;-ms-transform: rotate(90deg); /* IE 9 */
                                    -webkit-transform: rotate(90deg); /* Chrome, Safari, Opera */
                                    transform: rotate(90deg);">
                                    <i class="fa fa-ellipsis-h" style="color:#B4BCBE;"></i>
                                    <span class="" style="visibility:hidden; width:0px;"></span>
                                </button>
                                <ul class="dropdown-menu dropdown-default pull-right" role="menu"
                                    style="text-align:left;">
                                    <li>
                                        <?php if ($row['status'] == 0): ?>
                                            <a href="<?= base_url() ?>client/user/todo/mark_as_done/<?php echo $row['todo_id']; ?>">
                                                <i class="entypo-check"></i>
                                                <?php echo lang('mark_completed'); ?>
                                            </a>
                                        <?php endif; ?>
                                        <?php if ($row['status'] == 1): ?>
                                            <a href="<?= base_url() ?>client/user/todo/mark_as_undone/<?php echo $row['todo_id']; ?>">
                                                <i class="entypo-cancel"></i>
                                                <?php echo lang('mark_incomplete'); ?>
                                            </a>
                                        <?php endif; ?>
                                    </li>


                                    <li>
                                        <a href="<?= base_url() ?>client/user/todo/swap/<?php echo $row['todo_id']; ?>/up">
                                            <i class="entypo-up"></i>
                                            <?php echo lang('move_up'); ?>
                                        </a>
                                        <a href="<?= base_url() ?>client/user/todo/swap/<?php echo $row['todo_id']; ?>/down">
                                            <i class="entypo-down"></i>
                                            <?php echo lang('move_down'); ?>
                                        </a>
                                    </li>
                                    <li class="divider"></li>


                                    <li>
                                        <a href="<?= base_url() ?>client/user/todo/delete/<?php echo $row['todo_id']; ?>">
                                            <i class="entypo-trash"></i>
                                            <?= lang('delete'); ?>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>

            <div id="idCalculadora"></div>

        </div><!-- /.tab-pane -->
    </div>
    <!-- END Off Sidebar (right)-->
</aside>
<link rel="stylesheet" href="<?= base_url() ?>asset/js/plugins/calculator/SimpleCalculadorajQuery.css">
<script src="<?= base_url() ?>asset/js/plugins/calculator/SimpleCalculadorajQuery.js"></script>
<script>
    $("#idCalculadora").Calculadora({'EtiquetaBorrar': 'Clear', TituloHTML: ''});
</script>