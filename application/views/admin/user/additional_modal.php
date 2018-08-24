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
        Add additional label
    </header>
    <?php if (!empty($created) || !empty($edited)) { ?>
        <?php echo form_open(base_url('admin/user/update_additional_label'), array('id' => 'group_modal', 'class' => 'form-horizontal')); ?>
        <div class="form-group">
            <label
                class="col-sm-3 control-label">New label</label>
            <div class="col-sm-5">
                <input type="text" name="new_label" class="form-control"
                       placeholder="e.g Enter new label">
            </div>
        </div>
        <div class="form-group mt">
            <label class="col-lg-3"></label>
            <div class="col-lg-6">
                <button type="submit" class="btn btn-success waves-effect waves-light"><?= lang('save') ?></button>
                <button type="button" class="btn btn-default waves-effect waves-light" data-dismiss="modal"><?= lang('close') ?></button>
            </div>
        </div>
        <?php echo form_close(); ?>
    <?php } ?>
</div>

<script type="text/javascript">
    $(document).on("submit", "form", function (event) {
        var form = $(event.target);
        if (form.attr('action') == '<?= base_url('admin/user/update_additional_label')?>') {
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
                    str += `<option value="`+response.data[i].id+`">`+response.data[i].label+`</option>`;
                }
                var groups_phone = $('select[name="additional_phone_label[]"');
                groups_phone.html('');
                groups_phone.prepend(str);
                var groups_email = $('select[name="additional_email_label[]"');
                groups_email.html('');
                groups_email.prepend(str);
            }
            toastr[response.status](response.message);
            $('#myModal').modal('hide');
        }).fail(function () {
            alert('There was a problem with AJAX');
        });
    });
</script>