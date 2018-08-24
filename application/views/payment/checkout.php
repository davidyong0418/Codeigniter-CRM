<script src="//code.jquery.com/jquery-2.1.0.js"></script>
<?php if (config_item('two_checkout_live') == 'TRUE') { ?>
    <script type="text/javascript" src="https://2checkout.com/checkout/api/2co.min.js"></script>
    <script type="text/javascript" src="https://2checkout.com/checkout/api/script/publickey/"></script>
<?php } else { ?>
    <script type="text/javascript" src="https://sandbox.2checkout.com/checkout/api/2co.min.js"></script>
    <script type="text/javascript" src="https://sandbox.2checkout.com/checkout/api/script/publickey/"></script>
<?php } ?>
<?php
$cur = $this->invoice_model->check_by(array('code' => $invoice_info['currency']), 'tbl_currencies');
$allow_customer_edit_amount = config_item('allow_customer_edit_amount');
?>
<div class="panel panel-custom">
    <div class="panel-heading">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span
                class="sr-only">Close</span></button>
        <h4 class="modal-title">Paying <strong>
                <?= display_money($invoice_info['amount'], $cur->symbol); ?>
            </strong> for Invoice # <?= $invoice_info['item_name'] ?> via 2Checkout</h4>
    </div>
    <div class="modal-body">
        <?php
        $attributes = array('id' => 'tcoPay', 'onsubmit' => 'return false', 'name' => '2checkout', 'data-parsley-validate' => "", 'novalidate' => "", 'class' => 'bs-example form-horizontal');
        echo form_open('payment/checkout/process', $attributes);
        ?>

        <?php
        // Show PHP errors, if they exist:
        if (isset($errors) && !empty($errors) && is_array($errors)) {
            echo '<div class="alert alert-error"><h4>Error!</h4>The following error(s) occurred:<ul>';
            foreach ($errors as $e) {
                echo "<li>$e</li>";
            }
            echo '</ul></div>';
        }
        ?>

        <div id="payment-errors"></div>
        <input type="hidden" name="invoice_id" value="<?= $invoice_info['item_number'] ?>">
        <input id="sellerId" type="hidden" value="<?= config_item('2checkout_seller_id') ?>"/>
        <input id="publishableKey" type="hidden" value="<?= config_item('2checkout_publishable_key') ?>"/>
        <input id="token" name="token" type="hidden" value="">
        <?php if (!empty($allow_customer_edit_amount) && $allow_customer_edit_amount == 'No') { ?>
            <input name="amount" value="<?= display_money($invoice_info['amount']) ?>" type="hidden">
        <?php } ?>
        <div class="form-group">
            <label class="col-lg-4 control-label"><?= lang('amount') ?> ( <?= $cur->symbol ?>) </label>
            <div class="col-lg-4">
                <?php if (!empty($allow_customer_edit_amount) && $allow_customer_edit_amount == 'Yes') { ?>
                    <input type="text" required name="amount" data-parsley-type="number"
                           data-parsley-max="<?= $invoice_info['amount'] ?>" class="form-control"
                           value="<?= ($invoice_info['amount']) ?>">
                <?php } else { ?>
                    <input type="text" class="form-control" value="<?= display_money($invoice_info['amount']) ?>"
                           readonly>
                <?php } ?>
            </div>
        </div>
        <div class="form-group">
            <label class="col-lg-4 control-label">Card Number</label>
            <div class="col-lg-5">
                <input type="text" id="ccNo" size="20" class="form-control card-number input-medium"
                       autocomplete="off" placeholder="5555555555554444" required>
            </div>
        </div>

        <div class="form-group">
            <label class="col-lg-4 control-label">CVC</label>
            <div class="col-lg-2">
                <input type="text" id="cvv" size="4" class="form-control card-cvc input-mini" autocomplete="off"
                       placeholder="123" required>
            </div>
        </div>

        <div class="form-group">
            <label class="col-lg-4 control-label">Expiration (MM/YYYY)</label>
            <div class="col-lg-2">
                <input type="text" size="2" id="expMonth" class="form-control input-mini" autocomplete="off"
                       placeholder="MM" required>

            </div>
            <div class="col-lg-2">
                <input type="text" size="4" id="expYear" class="form-control input-mini" placeholder="YYYY"
                       required>
            </div>
        </div>

        <div class="modal-footer">
            <a href="#" class="btn btn-default" data-dismiss="modal"><?= lang('close') ?></a>
            <input type="submit" value="Submit Payment" class="btn btn-success"/>
        </div>
        <?php echo form_close(); ?>
    </div>
</div>
<script>
    // Called when token created successfully.
    function successCallback(data) {
        var myForm = document.getElementById('tcoPay');
        myForm.token.value = data.response.token.token;
        myForm.submit();
    }

    // Called when token creation fails.
    function errorCallback(data) {
        if (data.errorCode === 200) {
            TCO.requestToken(successCallback,
                errorCallback, 'tcoPay');
        } else {
            alert(data.errorMsg);
        }
    }

    $(function () {
        $("#tcoPay").submit(function (e) {
            e.preventDefault();
            TCO.requestToken(successCallback, errorCallback, 'tcoPay');
        });
    });
</script>