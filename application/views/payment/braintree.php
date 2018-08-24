<script src="https://js.braintreegateway.com/js/braintree-2.21.0.min.js"></script>
<script>
    // We generated a client token for you so you can test out this code
    // immediately. In a production-ready integration, you will need to
    // generate a client token on your server (see section below).
    var clientToken = "<?=$invoice_info['token']?>";

    braintree.setup(clientToken, "dropin", {
        container: "payment-form"
    });
</script>
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
            </strong> for Invoice # <?= $invoice_info['item_name'] ?> via Braintree</h4>
    </div>
    <div class="modal-body">
        <?php
        $attributes = array('class' => 'bs-example form-horizontal', 'id' => 'braintree-form');
        echo form_open(base_url() . 'payment/braintree/process', $attributes); ?>

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
        <input type="hidden" name="ref" value="<?= $invoice_info['item_name'] ?>">
        <input type="hidden" name="item_name" value="<?= $invoice_info['item_name'] ?>">
        <input type="hidden" name="item_number" value="<?= $invoice_info['item_number'] ?>">
        <?php if (!empty($allow_customer_edit_amount) && $allow_customer_edit_amount == 'No') { ?>
            <input name="amount" value="<?= display_money($invoice_info['amount']) ?>" type="hidden">
        <?php } ?>
        <div class="form-group">
            <label class="col-lg-4 control-label"><?= lang('amount') ?> ( <?= $invoice_info['currency'] ?>) </label>
            <div class="col-lg-4">
                <?php if (!empty($allow_customer_edit_amount) && $allow_customer_edit_amount == 'Yes') { ?>
                    <input type="text" id="amount" required name="amount" data-parsley-type="number"
                           data-parsley-max="<?= $invoice_info['amount'] ?>" class="form-control"
                           value="<?= ($invoice_info['amount']) ?>">
                <?php } else { ?>
                    <input type="text" class="form-control" value="<?= display_money($invoice_info['amount']) ?>"
                           readonly>
                <?php } ?>
            </div>
        </div>

        <div class="modal-footer">
            <a href="#" class="btn btn-default" data-dismiss="modal"><?= lang('close') ?></a>
            <button type="submit" class="btn btn-success" id="submitBtn">Process Payment</button>
        </div>
        </form>


    </div>


</div>
