<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title><?php echo $title; ?></title>
    <?php if (config_item('favicon') != '') : ?>
        <link rel="icon" href="<?php echo base_url() . config_item('favicon'); ?>" type="image/png">
    <?php else: ?>
        <link rel="icon" href="<?php echo base_url('assets/img/favicon.ico'); ?>" type="image/png">
    <?php endif; ?>
    <!-- =============== VENDOR STYLES ===============-->
    <!-- FONT AWESOME-->
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/plugins/fontawesome/css/font-awesome.min.css">
    <!-- Toastr-->
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/toastr.min.css">
    <!-- =============== BOOTSTRAP STYLES ===============-->
    <link href="<?php echo base_url(); ?>dist/css/pages/login-register-lock.css" rel="stylesheet">
    <link href="<?php echo base_url(); ?>dist/css/style.min.css" rel="stylesheet">
    <style>
        body{
            font-family: "Poppins", sans-serif;
            font-size: 0.875rem;
            font-weight: 400;
            line-height: 1.5;
            color: #212529;
            text-align: left;
        }
        .login-register{
            padding-top:5%;
            padding-bottom:10%;
        }
        .parsley-required{
            color:#ff0000;
            list-style: none;
        }
    </style>

    <!-- JQUERY-->
    <script src="<?php echo base_url(); ?>assets/plugins/jquery/dist/jquery.min.js"></script>

    <?php if (config_item('recaptcha_secret_key') != '' && config_item('recaptcha_site_key') != '') { ?>
        <script src='https://www.google.com/recaptcha/api.js'></script>
    <?php } ?>
    <?php

    $this->load->helper('file');
    $lbg = config_item('login_background');
    if (!empty($lbg)) {
        $login_background = _mime_content_type($lbg);
        $login_background = explode('/', $login_background);
    }
    ?>
   
</head>
<?php
$login_position = config_item('login_position');
if (!empty($login_background[0]) && $login_background[0] == 'image') {
    $login_background = config_item('login_background');
    if (!empty($login_background)) {
        $back_img = base_url() . '/' . config_item('login_background');
    }
} ?>

<?php

if (!empty($login_position) && $login_position == 'center') {
    if (!empty($back_img)) {
        $body_style = 'style="background: url(' . $back_img . ') no-repeat center center fixed;
 -webkit-background-size: cover;
 -moz-background-size: cover;
 -o-background-size: cover;
 background-size: cover;min-height: 100%;width:100%"';
    } else {
        $body_style = '';
    }
} else {
    $body_style = '';
}
$type = $this->session->userdata('c_message');
?>
<body <?= $body_style ?>>
<?php if (!empty($login_position) && $login_position == 'left') {
    $lcol = 'col-lg-4 col-sm-6 left-login';
} else if (!empty($login_position) && $login_position == 'right') {
    $lcol = 'col-lg-4 col-sm-6 left-login pull-right';
} else {
    $lcol = 'login-center';
} ?>
<div class="">
<section id="wrapper">
    <!-- <div class="wrapper" style="margin: 20% 0 0 auto"> -->
        <div class="login-register">
            
            <?= message_box('success'); ?>
            <?= message_box('error'); ?>
            <div class="error_login">
                <?php
                $validation_errors = validation_errors();
                if (!empty($validation_errors)) { ?>
                    <div class="alert alert-danger"><?php echo $validation_errors; ?></div>
                    <?php
                }
                $error = $this->session->flashdata('error');
                $success = $this->session->flashdata('success');
                if (!empty($error)) {
                    ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php } ?>
                <?php if (!empty($success)) { ?>
                    <div class="alert alert-success"><?php echo $success; ?></div>
                <?php } ?>
            </div>
            <!-- START panel-->
            <div class="login-box card">
                <div class="panel-heading text-center">
                    <a href="#" style="color: #ffffff">
                   <span style="font-size: 15px;"><?= config_item('company_name') ?>
                    </a>
                </div>
                <?php if (!empty($type)) {
                    ?>
                    <script>
                        $(document).ready(function () {
                            // show when page load
                            toastr.success('<?= lang($type)?>');
                        });
                    </script>
                    <?php
                    $this->session->unset_userdata('c_message');
                } ?>

                <div class="card-body">
                    <div class="text-center" style="margin-bottom: 20px">
                        <img style="width: 100%;"
                            src="<?= base_url() . config_item('company_logo') ?>" class="m-r-sm">
                    </div>
                    <?= $subview; ?>


                    <?php if (config_item('logo_or_icon') == 'logo_title') { ?>
                </div>
            </div>
        <?php } ?>
            <!-- END panel-->
            
        </div>
    </div>
</div>

<?php
if (!empty($login_position) && $login_position == 'left') {
    $col = 'col-lg-8 col-sm-6';
    if (!empty($back_img)) {
        $leftstyle = 'style="background: url(' . $back_img . ') no-repeat center center fixed;
 -webkit-background-size: cover;
 -moz-background-size: cover;
 -o-background-size: cover;
 background-size: cover;min-height: 100%;"';
    } else {
        $leftstyle = '';
    }
} else if (!empty($login_position) && $login_position == 'right') {
    $col = 'col-lg-8 col-sm-6 left-login pull-right';
    if (!empty($back_img)) {
        $leftstyle = 'style="background: url(' . $back_img . ') no-repeat center center fixed;
 -webkit-background-size: cover;
 -moz-background-size: cover;
 -o-background-size: cover;
 background-size: cover;min-height: 100%;"';
    } else {
        $leftstyle = '';
    }
} else {
    $col = '';
    $leftstyle = '';
}
?>

<div class="<?= $col ?> hidden-xs" <?= $leftstyle ?>>
    <?php if (!empty($login_background[0]) && $login_background[0] == 'video') { ?>
        <video class="bgvid inner" autoplay="autoplay" muted="muted" preload="auto" loop>
            <source
                src="<?php echo base_url() . config_item('login_background'); ?>"
                type="video/webm">
        </video>
    <?php } ?>
</div>

<!-- =============== VENDOR SCRIPTS ===============-->

<!-- =============== Toastr ===============-->
<script src="<?= base_url() ?>assets/js/toastr.min.js"></script>
<!-- BOOTSTRAP-->
<script src="<?php echo base_url(); ?>assets/plugins/bootstrap/dist/js/bootstrap.min.js"></script>
<!-- STORAGE API-->
<script src="<?php echo base_url(); ?>assets/plugins/jQuery-Storage-API/jquery.storageapi.min.js"></script>
<script src="<?php echo base_url() ?>assets/plugins/parsleyjs/parsley.min.js"></script>

</body>

</html>
