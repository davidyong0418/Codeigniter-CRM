<?php echo message_box('success'); ?>
<?php echo message_box('error'); ?>
<div class="row">
    <div class="col-md-2"></div>
    <div class="col-md-8 text-center">
        <?php
        echo "<h3>Thank You. Your order status is " . $status . ".</h3>";
        echo "<h4>Your Transaction ID for this transaction is " . $txnid . ".</h4>";
        echo "<h4>We have received a payment of Rs. " . $amount . ". Your order  will dispatch soon.</h4>";
        ?>
    </div>
    <div class="col-md-2"></div>
</div>