<?php
echo message_box('success');
echo message_box('error');
$created = can_action('125', 'created');
$edited = can_action('125', 'edited');
$label_info = $this->db->get('tbl_additional_label')->result();
?>
<div class="panel panel-custom">
    <header class="panel-heading ">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span
                class="sr-only">Close</span></button>
        Add additional label
    </header>
    <?php if (!empty($created) || !empty($edited)) { ?>
        <?php echo form_open(base_url('admin/client/update_additional_label'), array('id' => 'group_modal', 'class' => 'form-horizontal')); ?>
        
        <div class="form-group label-group">
        <label class="col-sm-2 control-label">Edit label</label>
            <div class="col-sm-5">
                <select class="form-control" name="select_label">
                    <?php foreach ($label_info as $label_item) {?>
                        <option value="<?php echo $label_item->id;?>"><?php echo $label_item->label;?></option>
                    <?php }?>
                </select>

            </div>
            <div class="col-sm-4 d-flex">
                <input type="text" name="edit_label" class="form-control" placeholder="">
                <!-- <button type="button" class="btn btn-danger margin-l-5">
                                        <i class="ti-trash"></i>
                </button> -->
            </div>
        </div>
       
        <div class="form-group mt">
            <label class="col-lg-3"></label>
            <div class="col-lg-9">
                <button type="button" class="btn waves-effect waves-light btn-info new-additional-label">Add New</button>
                <button type="button" class="btn waves-effect waves-light btn-danger" data-dismiss="modal">Delete</button>
                <button type="submit" class="btn btn-success waves-effect waves-light">Save</button>
                <button type="button" class="btn waves-effect waves-light btn-default" data-dismiss="modal">Cancel</button>
            </div>
        </div>
        <?php echo form_close(); ?>
    <?php } ?>
</div>

<script type="text/javascript">
    $(document).on('click','.new-additional-label',function(){
        var html = `<div class="form-group label-group">
            <label class="col-sm-3 control-label">New label</label>
            <div class="col-sm-5">
                <input type="text" name="new_label[]" class="form-control"
                       placeholder="e.g Enter new label">
            </div>
        </div>`;
        $('.label-group').last().after(html);
    });
    $(document).on("submit", "form", function (event) {
        var form = $(event.target);
        if (form.attr('action') == '<?= base_url('admin/client/update_additional_label')?>') {
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
                var groups = $('.additional_label_group select');
                groups.html('');
                groups.html(str);
            }
            toastr[response.status](response.message);
            $('#myModal').modal('hide');
        }).fail(function () {
            alert('There was a problem with AJAX');
        });
    });
</script>