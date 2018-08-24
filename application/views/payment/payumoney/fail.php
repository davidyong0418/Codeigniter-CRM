<?php echo message_box('success'); ?>
<?php echo message_box('error'); ?>
<div class="row">
    <div class="col-md-2"></div>
    <div class="col-md-8 text-center">
        <?php
        echo "<h3>Your order status is " . $status . ".</h3>";
        echo "<h4>Your transaction id for this transaction is " . $txnid . ".</h4> <h4> Contact our customer support.</h4>";
        ?>
    </div>
    <div class="col-md-2"></div>
</div>