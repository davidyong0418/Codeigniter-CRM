<?php
$cur = $this->invoice_model->check_by(array('code' => $invoice_info['currency']), 'tbl_currencies');
?>
<div class="panel panel-custom">
    <div class="panel-heading">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span
                class="sr-only">Close</span></button>
        <h4 class="modal-title">Paying <strong><?= display_money($invoice_info['amount'], $cur->symbol); ?> </strong>
            for Invoice
            # <?= $invoice_info['item_name'] ?> via Authorize.net</h4>
    </div>
    <div class="modal-body">

        <?php
        $attributes = array('id' => '2checkout', 'class' => 'form-horizontal');
        echo form_open('payment/authorize/process', $attributes);
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
        <input type="hidden" name="ref" value="<?= $invoice_info['item_name'] ?>">
        <input type="hidden" name="amount" value="<?= ($invoice_info['amount']) ?>">
        <input id="token" name="token" type="hidden" value="">

        <div class="form-group">
            <label class="col-lg-4 control-label">Card Number</label>
            <div class="col-lg-5">
                <input type="text" name="ccn" id="ccNo" size="20" class="form-control card-number input-medium"
                       autocomplete="off" placeholder="10111042254564" required>
            </div>
        </div>

        <div class="form-group">
            <label class="col-lg-4 control-label">CVC</label>
            <div class="col-lg-2">
                <input type="text" id="cvv" name="CSV" size="4" class="form-control card-cvc input-mini"
                       autocomplete="off" placeholder="325" required>
            </div>
        </div>

        <div class="form-group">
            <label class="col-lg-4 control-label">Expiration (MM/YYYY)</label>
            <div class="col-lg-2">
                <input type="text" size="2" name="crMonth" id="expMonth" class="form-control input-mini"
                       autocomplete="off" placeholder="MM" required>

            </div>
            <div class="col-lg-2">
                <input type="text" size="4" name="crYear" id="expYear" class="form-control input-mini"
                       placeholder="YYYY" required>
            </div>
        </div>

        <div class="modal-footer">
            <a href="#" class="btn btn-default" data-dismiss="modal"><?= lang('close') ?></a>
            <button type="submit" class="btn btn-success" id="submitBtn">Process Payment</button>
        </div>
        </form>
    </div>
</div>
<!-- /.modal-content -->