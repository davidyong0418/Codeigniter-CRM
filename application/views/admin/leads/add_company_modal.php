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
        Add New Company
    </header>
    <?php if (!empty($created) || !empty($edited)) { ?>
        <?php echo form_open(base_url('admin/leads/add_company'), array('id' => 'group_modal', 'class' => 'form-horizontal')); ?>
        
        <div class="col-sm-12">
        <div class="new-company">
                    <div class="form-group">
                        <label
                            class="col-lg-4 control-label">Company</label>
                        <div class="col-lg-8">
                            <input type="text" class="form-control" value="" name="company_name">
                        </div>
                    </div>
                

                    <div class="form-group">
                        <label
                            class="col-lg-4 control-label">Company address</label>
                        <div class="col-lg-8">
                            <input type="text" class="form-control" value="" name="company_address">
                        </div>
                    </div>
                    <div class="form-group">
                        <label
                            class="col-lg-4 control-label">Company Phone Numbers</label>
                        <div class="col-lg-8">
                            <input type="text" class="form-control" value="" name="company_phone" data-parsley-validate="number">
                        </div>
                    </div>
                    <div class="form-group">
                        <label
                            class="col-lg-4 control-label">Company Logo</label>
                        <div class="col-lg-8">
                            <input class="form-control" type="file" id="upload_file" name="upload_file[]" onchange="preview_image();" multiple value="company_logo"/>
                        </div>
                    </div>
                    <div class="form-group">
                    <label
                            class="col-lg-4 control-label">Company Social Media Links</label>
                    <div class="col-lg-8">
                        <div class="col-lg-6 nopaddingleft">
                            <select class="form-control" id="social-media-select" value="company_social">
                                <option value="1">Facebook</option>
                                <option value="2">Instagram</option>
                                <option value="3">Linkedin</option>
                                <option value="4">Pinterest</option>
                                <option value="5">YouTube</option>
                                <option value="6">Other</option>
                            </select>
                        </div>
                        <div class="col-lg-6 nopaddingright">
                            <input type="form-control" id="social-media-value" class="form-control" placeholder="URL" name="company_social_url">
                        </div>
                    </div>
                    </div>
            </div>
        </div>
       
        <div class="form-group mt">
            <label class="col-lg-4"></label>
            <div class="col-lg-8">
                <button type="submit" class="btn btn-success waves-effect waves-light">Save</button>
                <button type="button" class="btn waves-effect waves-light btn-default"  data-dismiss="modal">Cancel</button>
            </div>
        </div>
        <?php echo form_close(); ?>
    <?php } ?>
</div>

<script type="text/javascript">
    $(document).on("submit", "form", function (event) {
        var form = $(event.target);
        if (form.attr('action') == '<?= base_url('admin/leads/add_company')?>') {
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
                    str += `<option value="`+response.data[i].id+`">`+ response.data[i].company_name +`</option>`;
                }
                var groups = $('select[name="company_id"');
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