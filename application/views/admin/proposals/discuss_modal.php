<?php
echo message_box('success');
echo message_box('error');
$created = can_action('125', 'created');
$edited = can_action('125', 'edited');
?>
<div class="panel panel-custom discuss-proposal">
    <header class="panel-heading ">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        Discuss
    </header>
    <?php if (!empty($created) || !empty($edited)) { ?>
        <?php echo form_open(base_url('admin/proposals/add_comment'), array('id' => 'group_modal', 'class' => 'form-horizontal')); ?>
        <div class="label-group">
            <ul class="custom-list-none list-group comment-group">
                <?php if (!empty($comment_data))
                {
                    foreach ($comment_data as $comment_item)
                    {
                        $user_info = $this->db->where('user_id',$user_id)->get('tbl_account_details')->result();
                ?>
                <li class="border-bottom custom-margin-bottom">
                    <div class="col-sm-3 text-center">
                        <div class="col-sm-8">
                            <img src="<?= base_url()?><?php if(!empty($user_info)){echo $user_info[0]->avatar;}else{echo 'upload/business_card.jpg';}?>" style="width:100%;" class="float-right">
                        </div>
                        
                    </div>
                    <div class="col-sm-6">
                        <h5 style="margin-top:0px;"><?php if(!empty($user_info)){echo $user_info[0]->fullname;}?></h5>
                        <p><?php echo $comment_item->comment;?></p>
                    </div>
                    <div class="col-sm-3 text-center">
                        <?php echo $comment_item->created_at;?>
                    </div>
                </li>
            <?php 
                    }
                }
            ?>
            </ul>
        </div>
        <div class="comment-textarea">
            <label class="col-sm-3">Your Comment</label>
            <textarea class="form-control col-sm-8 proposal-comment-textarea" name="proposal_comment" row="8"></textarea>
        <div class="comment-send col-sm-12 padding-top-10">
            <label class="col-sm-3"></label>
            <div class="col-sm-9">
                <button type="submit" class="btn btn-success waves-effect waves-light">Save</button>
                <button type="button" class="btn waves-effect waves-light btn-default" data-dismiss="modal">Cancel</button>
            </div>
        </div>
        <?php echo form_close(); ?>
    <?php } ?>
</div>

<script type="text/javascript">
    var base_url = "<?php echo base_url();?>";
    $(document).on("submit", "form", function (event) {
        var form = $(event.target);
        if (form.attr('action') == '<?= base_url('admin/proposals/add_comment')?>') {
            event.preventDefault();
        }
        $.ajax({
            type: form.attr('method'),
            url: form.attr('action'),
            data: form.serialize()
        }).done(function (response) {
        
            response = JSON.parse(response);
            if (response.status == 'success') {
                var img_url, user_name, comment, created_at;
                img_url=base_url + response.data.img_url;
                user_id = response.data.user_id;
                comment = response.data.comment;
                user_name = response.data.username;
                created_at = response.created_at;
                var append_str = `<li class="border-bottom custom-margin-bottom"><div class="col-sm-3"><img src="`+img_url+`" style="width:60%;height: 50%" class="float-right"></div><div class="col-sm-6"><h5 style="margin-top:0px;">`+user_name+`</h5><p>`+comment+`</p></div><div class="col-sm-3 text-center">`+created_at+`</div></li>;`
                $('.comment-group').append(append_str);
                $('.proposal-comment-textarea').text('');

            }
            toastr[response.status](response.message);
            // $('#myModal').modal('hide');
        }).fail(function () {
            alert('There was a problem with AJAX');
        });
    });
</script>