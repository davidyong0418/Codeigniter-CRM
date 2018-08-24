<?php echo message_box('success'); ?>
<?php echo message_box('error');
$created = can_action('142', 'created');
$edited = can_action('142', 'edited');
$deleted = can_action('142', 'deleted');
?>
<div class="panel panel-custom" style="border: none;" data-collapsed="0">
    <div class="panel-heading">
        <div class="panel-title">
            <?= lang('category') ?>
            <?php if (!empty($created)) { ?>
                <div class="pull-right hidden-print" style="padding-top: 0px;padding-bottom: 8px">
                    <a href="<?= base_url() ?>admin/knowledgebase/new_categories" class="btn btn-xs btn-info"
                       data-toggle="modal"
                       data-placement="top" data-target="#myModal">
                        <i class="fa fa-plus "></i> <?= ' ' . lang('new') . ' ' . lang('categories') ?></a>
                </div>
            <?php } ?>
        </div>
    </div>
    <!-- Table -->
    <div class="panel-body">
        <table class="table table-striped DataTables " id="DataTables" cellspacing="0" width="100%">
            <thead>
            <tr>
                <th><?= lang('categories') ?></th>
                <th><?= lang('description') ?></th>
                <th class="col-sm-1"><?= lang('active') ?></th>
                <th class="col-sm-1"><?= lang('order') ?></th>
                <th><?= lang('action') ?></th>
            </tr>
            </thead>
            <tbody>
            <?php if (!empty($all_kb_category_info)):foreach ($all_kb_category_info as $key => $v_kb_category):
                ?>
                <tr>
                    <td><?php echo $v_kb_category->category; ?></td>
                    <td><?php echo $v_kb_category->description; ?></td>
                    <td>
                        <div class="change_kb_category">
                            <input data-id="<?= $v_kb_category->kb_category_id ?>" data-toggle="toggle"
                                   name="active" value="1" <?php
                            if (!empty($v_kb_category->status) && $v_kb_category->status == '1') {
                                echo 'checked';
                            }
                            ?> data-on="<?= lang('yes') ?>" data-off="<?= lang('no') ?>"
                                   data-onstyle="success btn-xs"
                                   data-offstyle="danger btn-xs" type="checkbox">
                        </div>
                    </td>
                    <td><?php echo $v_kb_category->sort; ?></td>
                    <td>
                        <?php if (!empty($edited)) { ?>
                            <span data-toggle="tooltip" data-placement="top" title="<?= lang('edit') ?>">
                        <a href="<?= base_url() ?>admin/knowledgebase/new_categories/<?= $v_kb_category->kb_category_id ?>"
                           class="btn btn-primary btn-xs"
                           data-toggle="modal"
                           data-placement="top" data-target="#myModal">
                            <i class="fa fa-pencil-square-o"></i> </a>
                            </span>
                        <?php }
                        if (!empty($deleted)) { ?>
                            <?php echo btn_delete('admin/knowledgebase/delete_categories/' . $v_kb_category->kb_category_id) ?>
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
        $('.change_kb_category input[type="checkbox"]').change(function () {
            var kb_category_id = $(this).data().id;
            var status = $(this).is(":checked");
            if (status == true) {
                status = 1;
            } else {
                status = 2;
            }
            $.ajax({
                type: 'POST', // define the type of HTTP verb we want to use (POST for our form)
                url: '<?= base_url()?>admin/knowledgebase/change_status/' + status + '/' + kb_category_id, // the url where we want to POST
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