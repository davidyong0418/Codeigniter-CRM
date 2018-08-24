<form data-parsley-validate="" novalidate="" action="<?php echo base_url() ?>login" method="post" class="form-horizontal form-material" id="loginform">
    <div class="form-group has-feedback">
        <div class="col-xs-12">
                <input type="text" name="user_name" required="true" class="form-control" placeholder="<?= lang('username') ?>"/>
        </div>
    </div>
    <div class="form-group has-feedback">
        <div class="col-xs-12">
            <input type="password" name="password" required="true" class="form-control"
                placeholder="<?= lang('password') ?>"/>
        </div>
    </div>
    <div class="form-group row">
        <div class="col-md-12">
            <div class="d-flex no-block align-items-center">
            <div class="checkbox c-checkbox custom-control custom-checkbox">
                    <input type="checkbox" value="" name="remember" class="custom-control-input" id="customCheck1">
                    <label class="custom-control-label" for="customCheck1"><?= lang('remember_me') ?></label>
            </div>
            <div class="ml-auto"><a href="<?= base_url() ?>login/forgot_password"
                                    class="text-muted"><?= lang('forgot_password') ?></a>
            </div>
                </div>
            </div>
    </div>
    <div class="form-group text-center">
    <?php if (config_item('recaptcha_secret_key') != '' && config_item('recaptcha_site_key') != '') { ?>
        <div class="col-xs-12 p-b-20" data-sitekey="<?php echo config_item('recaptcha_site_key'); ?>"></div>
    <?php }
    $mark_attendance_from_login = config_item('mark_attendance_from_login');
    if (!empty($mark_attendance_from_login) && $mark_attendance_from_login == 'Yes') {
        $class = null;
    } else {
        $class = 'btn-block';
    }
    ?>
    <button type="submit" class="btn btn-block btn-lg btn-info btn-rounded <?= $class ?> btn-flat"><?= lang('sign_in') ?> <i
            class="fa fa-arrow-right"></i></button>
</div>
</div>
    
</form>
<?php if (config_item('allow_client_registration') == 'TRUE') { ?>
    <p class="pt-lg text-center"><?= lang('do_not_have_an_account') ?></p><a href="<?= base_url() ?>login/register"
                                                            class="btn btn-block btn-default">
     <?= lang('get_your_account') ?></a>
<?php } ?>