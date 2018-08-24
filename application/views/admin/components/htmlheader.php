<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- Tell the browser to be responsive to screen width -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <!-- Favicon icon -->
<link href="<?= base_url() ?>dist/css/style.min.css" rel="stylesheet">

    <link rel="icon" type="image/png" sizes="16x16" href="<?= base_url() ?>assets/images/favicon.png">
    <title>Elite Admin Template - The Ultimate Multipurpose admin template</title>
    <!-- This page CSS -->
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/plugins/fontawesome/css/font-awesome.min.css">
    <!-- SIMPLE LINE ICONS-->
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/plugins/simple-line-icons/css/simple-line-icons.css">
    <!-- chartist CSS -->


    <!-- <link href="assets/plugins/fullcalendar/fullcalendar.min.css" rel="stylesheet" type="text/css"> -->
    
    <?php $direction = $this->session->userdata('direction');
    if (!empty($direction) && $direction == 'rtl') {
        $RTL = 'on';
    } else {
        $RTL = config_item('RTL');
    }

    ?>
    
    
    

    <?php
    if (!empty($RTL)) {
        ?>
        <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/bootstrap-rtl.min.css" id="bscss">
        <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/app-rtl.min.css" id="maincss">
    <?php } else {
        ?>
        <!-- =============== BOOTSTRAP STYLES ===============-->
        <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/bootstrap.min.css" id="bscss">
        <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/app.min.css" id="maincss">
    <?php }
    $custom_color = config_item('active_custom_color');
    if (!empty($custom_color) && $custom_color == 1) {
        include_once 'assets/css/bg-custom.php';
    } else {
        ?>
        
    <?php }
    ?>
    <!-- <link rel="stylesheet" href="<?php echo base_url(); ?>assets/plugins/select2/dist/css/select2.min.css"> -->
    <!-- <link rel="stylesheet"
          href="<?php echo base_url(); ?>assets/plugins/select2/dist/css/select2-bootstrap.min.css"> -->

    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/toastr.min.css">

        <!-- Data Table  CSS -->
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/plugins/dataTables/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/plugins/dataTables/css/dataTables.colVis.min.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/plugins/dataTables/css/dataTables.bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/plugins/dataTables/css/responsive.dataTables.min.css">




    <link href="<?php echo base_url(); ?>assets/plugins/summernote/summernote.min.css" rel="stylesheet">
    <link href="<?php echo base_url() ?>assets/plugins/bootstrap-slider/bootstrap-slider.min.css" rel="stylesheet">
    


        <!--- bootstrap-select ---!>
    <link href="<?php echo base_url() ?>assets/plugins/bootstrap-select/bootstrap-select.min.css" rel="stylesheet">
    <link href="<?php echo base_url() ?>assets/plugins/chat/chat.min.css" rel="stylesheet">

   <!-- custom -->
    <link href="<?= base_url() ?>assets/css/custom.css" rel="stylesheet">

    <!-- Dashboard 1 Page CSS -->
    <link href="<?= base_url() ?>dist/css/pages/dashboard1.css" rel="stylesheet">






    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
<![endif]-->
 <!-- JQUERY-->
    <script src="<?= base_url() ?>assets/js/jquery.min.js"></script>

    <link href="<?php echo base_url() ?>asset/css/bootstrap-toggle.min.css" rel="stylesheet">
    <script src="<?php echo base_url() ?>asset/js/bootstrap-toggle.min.js"></script>
    <script src="<?php echo base_url() ?>assets/plugins/raphael/raphael.min.js"></script>
<script src="<?php echo base_url() ?>assets/plugins/morris/morris.min.js"></script>

    <script>
        var total_unread_notifications = <?php echo $unread_notifications; ?>,
            autocheck_notifications_timer_id = 0,
            base_url = "<?php echo base_url(); ?>",
            new_notification = "<?php lang('new_notification'); ?>",
            auto_check_for_new_notifications = <?php echo config_item('auto_check_for_new_notifications'); ?>,
            file_upload_instruction = "<?php echo lang('file_upload_instruction_js'); ?>",
            filename_too_long = "<?php echo lang('filename_too_long'); ?>";
        desktop_notifications = "<?php echo config_item('desktop_notifications'); ?>";
        lsetting = "<?php echo lang('settings'); ?>";
        lfull_conversation = "<?php echo lang('full_conversation'); ?>";
        ledit_name = "<?php echo lang('edit') . ' ' . lang('name') ?>";
        ldelete_conversation = "<?php echo lang('delete_conversation') ?>";
        lminimize = "<?php echo lang('minimize') ?>";
        lclose = "<?php echo lang('close') ?>";
        lnew = "<?php echo lang('new') ?>";
    </script>

</head>
