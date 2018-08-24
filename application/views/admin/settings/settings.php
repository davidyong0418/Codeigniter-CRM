<div class="row">
    <div class="col-lg-12">
        <section class="col-sm-12">
            <div class="col-sm-12">
                <?php if ($load_setting == 'email') { ?>
                    <div style="margin-bottom: 10px;margin-left: -15px" class="<?php
                    if ($load_setting != 'email') {
                        echo 'hidden';
                    }
                    ?>">
                        <a href="<?= base_url() ?>admin/settings/email&view=alerts" class="btn btn-info"><i
                                class="fa fa fa-inbox text"></i>
                            <span class="text"><?php echo lang('alert_settings') ?></span>
                        </a>
                    </div>
                <?php } ?>

            </div>
            <section>
                <!-- Load the settings form in views -->
                <?php $this->load->view('admin/settings/' . $load_setting) ?>
                <!-- End of settings Form -->
            </section>
        </section>
    </div>
</div>
