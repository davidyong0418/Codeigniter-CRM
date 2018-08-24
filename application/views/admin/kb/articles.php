<?php echo message_box('success'); ?>
<?php echo message_box('error');
$created = can_action('143', 'created');
$edited = can_action('143', 'edited');
$deleted = can_action('143', 'deleted');
?>
<div class="panel panel-custom" style="border: none;" data-collapsed="0">
    <div class="panel-heading">
        <div class="panel-title">
            <?= lang('all') . ' ' . lang('articles') ?>
            <?php if (!empty($created)) { ?>
                <div class="pull-right hidden-print" style="padding-top: 0px;padding-bottom: 8px">
                    <a href="<?= base_url() ?>admin/knowledgebase/new_articles" class="btn btn-xs btn-info">
                        <i class="fa fa-plus "></i> <?= ' ' . lang('new') . ' ' . lang('articles') ?>
                    </a>
                </div>
            <?php } ?>
        </div>
    </div>
    <!-- Table -->
    <div class="panel-body">
        <table class="table table-striped DataTables " id="DataTables" cellspacing="0" width="100%">
            <thead>
            <tr>
                <th><?= lang('title') ?></th>
                <th><?= lang('categories') ?></th>
                <th class="col-sm-1"><?= lang('total') . ' ' . lang('view') ?></th>
                <th class="col-sm-1"><?= lang('active') ?></th>
                <th><?= lang('action') ?></th>
            </tr>
            </thead>
            <tbody>
            <?php if (!empty($all_kb_info)):foreach ($all_kb_info as $key => $v_kb):
                $category_info = get_row('tbl_kb_category', array('kb_category_id' => $v_kb->kb_category_id));
                ?>
                <tr id="table-articles-<?= $v_kb->kb_id?>">
                    <td><?php echo $v_kb->title; ?></td>
                    <td><?php echo(!empty($category_info->category) ? $category_info->category : '-'); ?></td>
                    <td><?php echo $v_kb->total_view; ?></td>
                    <td>
                        <div class="change_kb">
                            <input data-id="<?= $v_kb->kb_id ?>" data-toggle="toggle"
                                   name="active" value="1" <?php
                            if (!empty($v_kb->status) && $v_kb->status == '1') {
                                echo 'checked';
                            }
                            ?> data-on="<?= lang('yes') ?>" data-off="<?= lang('no') ?>"
                                   data-onstyle="success btn-xs"
                                   data-offstyle="danger btn-xs" type="checkbox">
                        </div>
                    </td>
                    <td>
                        <?= btn_view('admin/knowledgebase/details/articles/' . $v_kb->kb_id) ?>
                        <?php if (!empty($edited)) { ?>
                            <span data-toggle="tooltip" data-placement="top" title="<?= lang('edit') ?>">
                        <a href="<?= base_url() ?>admin/knowledgebase/new_articles/<?= $v_kb->kb_id ?>"
                           class="btn btn-primary btn-xs">
                            <i class="fa fa-pencil-square-o"></i> </a>
                            </span>
                        <?php }
                        if (!empty($deleted)) { ?>
                            <?php echo ajax_anchor(base_url("admin/knowledgebase/delete_articles/" . $v_kb->kb_id), "<i class='btn btn-danger btn-xs fa fa-trash-o'></i>", array("class" => "", "title" => lang('delete'), "data-fade-out-on-success" => "#table-articles-" . $v_kb->kb_id)); ?>
                        <?php } ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<script>
    $(document).ready(function () {
        $('.change_kb input[type="checkbox"]').change(function () {
            var kb_id = $(this).data().id;
            var status = $(this).is(":checked");
            if (status == true) {
                status = 1;
            } else {
                status = 2;
            }
            $.ajax({
                type: 'POST', // define the type of HTTP verb we want to use (POST for our form)
                url: '<?= base_url()?>admin/knowledgebase/change_kb_status/' + status + '/' + kb_id, // the url where we want to POST
                dataType: 'json', // what type of data do we expect back from the server
                encode: true,
                success: function (res) {
                    if (res) {
                        toastr[res.status](res.message);
                    } else {
                        alert('There was a problem with AJAX');
                    }
                }
            })

        });
    })
</script>