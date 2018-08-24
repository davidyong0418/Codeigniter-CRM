<p class="text-center pv"><?= lang('sing_up_to_get_access') ?></p>
<form method="post" data-parsley-validate="" novalidate="" action="<?= base_url() ?>login/registered_user" class="form-horizontal form-material">

    <div class="form-group has-feedback">
        <div class="col-xs-12">
            <input type="text" name="name" required="true" class="form-control"
                placeholder="<?= lang('company_name') ?>">
        </div>
    </div>
    <div class="form-group has-feedback">
        <input type="email" name="email" required="true" class="form-control"
               placeholder="<?= lang('company_email') ?>">
    </div>
    <div class="form-group has-feedback">
        <select name="language" class="form-control"
                style="width: 100%">
            <?php
            $languages = $this->db->where('active', 1)->order_by('name', 'ASC')->get('tbl_languages')->result();
            if (!empty($languages)) {
                foreach ($languages as $lang) : ?>
                    <option
                        value="<?= $lang->name ?>"<?php
                    if (!empty($client_info->language) && $client_info->language == $lang->name) {
                        echo 'selected';
                    } elseif (empty($client_info->language) && $this->config->item('language') == $lang->name) {
                        echo 'selected';
                    } ?>
                    ><?= ucfirst($lang->name) ?></option>
                <?php endforeach;
            } else {
                ?>
                <option
                    value="<?= $this->config->item('language') ?>"><?= ucfirst($this->config->item('language')) ?></option>
                <?php
            }
            ?>
        </select>
    </div>
    <div class="form-group has-feedback">
        <input type="text" name="username" required="true" class="form-control"
               placeholder="<?= lang('username') ?>">
    </div>
    <div class="form-group has-feedback">
        <input type="password" id="password" placeholder="<?= lang('password') ?>" required="true" class="form-control"
               name="password">
    </div>
    <div class="form-group has-feedback">
        <input id="signupInputRePassword1" data-parsley-equalto="#password" type="password" placeholder="<?= lang('confirm_password') ?>"
               required="true" class="form-control" value="" name="confirm_password">
    </div>
    <div class="form-group has-feedback">
    <button type="submit" class="btn btn-block btn-primary btn-lg btn-rounded"><?= lang('sign_up') ?></button>
    </div>
</form>
<div class="form-group text-center">
<p class="pt-lg text-center"><?= lang('already_have_an_account') ?></p><a href="<?= base_url() ?>login"
                                                                          class="btn btn-block btn-default"><?= lang('sign_in') ?></a>
        </div>
