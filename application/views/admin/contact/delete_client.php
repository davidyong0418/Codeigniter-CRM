<div class="panel panel-custom">
    <div class="panel-heading">
        <div class="panel-title"><strong><?= lang('delete') ?> <?= $client_info->first_name.' '.$client_info->last_name ?> </strong>

        </div>
    </div>
    <div class="panel-body ">
        <form role="form" enctype="multipart/form-data" id="form"
              action="<?php echo base_url(); ?>admin/contact/delete_client/<?= $client_info->id ?>/yes"
              method="post"
              class="form-horizontal  ">
            <p style="font-weight: bold; color: Red"><?= lang('delete_note_1') . ' <span style="color:#000"> ' . $client_info->first_name.' '.$client_info->last_name . ' </span> ' . lang('delete_note_2') ?></p>
            <p>
                <strong> <?= lang('delete_note_3') . ' <span class="text-danger">' . $client_info->first_name.' '.$client_info->last_name . '</span> ' . lang('delete_note_4') ?></strong>
            </p>

            <div class="col-sm-12">
                <label class="pull-left"><i class="fa fa-check-circle-o fa-2x"></i></label>
                <div class="col-sm-11">
                    <p class=""
                       style="font-size: 18px"><?= '<span class="text-success">' . 'General' . '</span> ' . lang('wil_be_deleted') ?></p>
                </div>
            </div>
            <div class="col-sm-12">
                <label class="pull-left"><i class="fa fa-check-circle-o fa-2x"></i></label>
                <div class="col-sm-11">
                    <p class=""
                       style="font-size: 18px"><?= '<span class="text-success">' . 'Sales' . '</span> ' . lang('wil_be_deleted') ?></p>
                </div>
            </div>
            <div class="col-sm-12">
                <label class="pull-left"><i class="fa fa-check-circle-o fa-2x"></i></label>
                <div class="col-sm-11">
                    <p class=""
                       style="font-size: 18px"><?= '<span class="text-success">' . 'Relationship Information' . '</span> ' . lang('wil_be_deleted') ?></p>
                </div>
            </div>
            <div class="col-sm-12">
                <label class="pull-left"><i class="fa fa-check-circle-o fa-2x"></i></label>
                <div class="col-sm-11">
                    <p class=""
                       style="font-size: 18px"><?= '<span class="text-success">' . 'Services Info' . '</span> ' . lang('wil_be_deleted') ?></p>
                </div>
            </div>
            <div class="col-sm-12">
                <label class="pull-left"><i class="fa fa-check-circle-o fa-2x"></i></label>
                <div class="col-sm-11">
                    <p class=""
                       style="font-size: 18px"><?= '<span class="text-success">' . 'Access Control' . '</span> ' . lang('wil_be_deleted') ?></p>
                </div>
            </div>
           
            <div class="form-group">
                <label for="inputEmail3" class=""> </label>
                <div class="col-md-4 ml-lg">
                    <button type="submit" name="submit" value="1"
                            class="btn-block btn btn-danger"><?= lang('proceed_anyway') ?></button>
                </div>
                <div class="col-md-1">
                    <a href="<?= base_url() ?>admin/client/manage_client"
                       class="btn btn-primary"><?= lang('cancel') ?></a>
                </div>
            </div>
        </form>

    </div>
</div>