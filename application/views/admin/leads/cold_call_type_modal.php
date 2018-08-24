<?php
echo message_box('success');
echo message_box('error');
$created = can_action('125', 'created');
$edited = can_action('125', 'edited');
?>
<div class="panel panel-custom">
    <header class="panel-heading ">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span
                class="sr-only">Close</span></button>
        Add Cold Call Type
    </header>
    <?php if (!empty($created) || !empty($edited)) { ?>
        <?php echo form_open(base_url('admin/leads/update_cold_call_type'), array('id' => 'group_modal', 'class' => 'form-horizontal')); ?>
        <div class="form-group">
            <label class="col-sm-4 control-label">New Cold Call Type</label>
            <div class="col-sm-5">
                <input type="text" name="new_type" class="form-control" placeholder="e.g Enter new type" required>
            </div>
        </div>
        <div class="form-group mt">
            <label class="col-lg-3"></label>
            <div class="col-lg-3">
                <button type="submit" class="btn btn-sm btn-primary"><?= lang('save') ?></button>
                <button type="button" class="btn btn-default" data-dismiss="modal"><?= lang('close') ?></button>
            </div>
        </div>
        <?php echo form_close(); ?>
    <?php } ?>
</div>

<script type="text/javascript">
    $(document).on("submit", "form", function (event) {
        var form = $(event.target);
        if (form.attr('action') == '<?= base_url('admin/leads/update_cold_call_type')?>') {
            event.preventDefault();
        }
        $.ajax({
            type: form.attr('method'),
            url: form.attr('action'),
            data: form.serialize()
        }).done(function (response) {
            response = JSON.parse(response);
            if (response.status == 'success') {
                var str = '';
                for (var i = 0; i<response.data.length; i++)
                {
                    str += `<option value="`+response.data[i].id+`">`+response.data[i].type+`</option>`;
                }
                var groups = $('select[name="c_call_type"');
                groups.html('');
                groups.prepend(str);
                var select2Instance = groups.data('select2');
                var resetOptions = select2Instance.options.options;
                groups.select2('destroy').select2(resetOptions);
            }
            toastr[response.status](response.message);
            $('#myModal').modal('hide');
        }).fail(function () {
            alert('There was a problem with AJAX');
        });
    });
</script>