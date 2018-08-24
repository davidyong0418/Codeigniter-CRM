<?php
$cur = $this->invoice_model->check_by(array('code' => $invoice_info['currency']), 'tbl_currencies');
$allow_customer_edit_amount = config_item('allow_customer_edit_amount');
$client_info = $this->db->where('client_id', $invoice_info['client_id'])->get('tbl_client')->row();
?>
<div class="panel panel-custom">
    <div class="panel-heading">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span
                class="sr-only">Close</span></button>
        <h4 class="modal-title">Paying <strong><?= display_money($invoice_info['amount'], $cur->symbol); ?></strong> for
            Invoice # <?= $invoice_info['item_name'] ?> via CCAvenue</h4>
    </div>
    <div class="modal-body">

        <?php
        $attributes = array('id' => 'ccavenue', 'class' => 'form-horizontal');
        echo form_open('https://www.ccavenue.com/shopzone/cc_details.jsp', $attributes);
        ?>
        <p><strong>Are you sure to paid by CCAvenue </strong></p>

        <input type="hidden" name="invoice_id" value="<?= $invoice_info['item_number'] ?>">
        <?php if (!empty($allow_customer_edit_amount) && $allow_customer_edit_amount == 'No') { ?>
            <input name="Amount" value="<?= display_money($invoice_info['amount']) ?>" type="hidden">
        <?php } ?>
        <div class="form-group">
            <label class="col-lg-4 control-label"><?= lang('amount') ?> ( <?= $invoice_info['currency'] ?>) </label>
            <div class="col-lg-4">
                <?php if (!empty($allow_customer_edit_amount) && $allow_customer_edit_amount == 'Yes') { ?>
                    <input type="text" id="amount" required name="Amount" data-parsley-type="number"
                           data-parsley-max="<?= $invoice_info['amount'] ?>" class="form-control"
                           value="<?= ($invoice_info['amount']) ?>">
                <?php } else { ?>
                    <input type="text" class="form-control" value="<?= display_money($invoice_info['amount']) ?>"
                           readonly>
                <?php } ?>
            </div>
        </div>
        <input type=hidden name="Merchant_Id" value="<?= $this->config->item('ccavenue_merchant_id') ?>">
        <input type="hidden" name="Currency" value="<?= $invoice_info['currency'] ?>">
        <input type="hidden" name="Amount" value="<?= ($invoice_info['amount']) ?>">
        <input type="hidden" name="Order_Id" value="<?= $invoice_info['item_name'] ?>">

        <input type="hidden" name="Redirect_Url" value="<?php echo base_url().'payment/payumoney/check_status/'.$invoice_info['invoices_id']; ?>">
        <input type="hidden" name="Checksum" value="<?php echo $Checksum; ?>">
        <input type="hidden" name="billing_cust_name" value="<?php echo $client_info->name; ?>">
        <input type="hidden" name="billing_cust_address" value="<?php echo $client_info->short_note; ?>">
        <input type="hidden" name="billing_cust_country" value="<?php echo $client_info->country; ?>">
        <input type="hidden" name="billing_cust_state" value="<?php echo $client_info->zipcode; ?>">
        <input type="hidden" name="billing_zip" value="<?php echo  $client_info->zipcode; ?>; ?>">
        <input type="hidden" name="billing_cust_tel" value="<?php echo  $client_info->phone; ?>">
        <input type="hidden" name="billing_cust_email" value="<?php echo  $client_info->email; ?>">
        <input type="hidden" name="delivery_cust_name" value="<?php echo  $client_info->name; ?>">
        <input type="hidden" name="delivery_cust_address" value="<?php echo  $client_info->short_note; ?>">
        <input type="hidden" name="delivery_cust_country" value="<?php echo  $client_info->country; ?>">
        <input type="hidden" name="delivery_cust_state" value="<?php echo  $client_info->zipcode; ?>">
        <input type="hidden" name="delivery_cust_tel" value="<?php echo  $client_info->mobile; ?>">
        <input type="hidden" name="delivery_cust_notes" value="<?php echo  $client_info->short_note; ?>">
        <input type="hidden" name="Merchant_Param" value="<?php echo ''; ?>">
        <input type="hidden" name="billing_cust_city" value="<?php echo  $client_info->city; ?>">
        <input type="hidden" name="billing_zip_code" value="<?php echo  $client_info->zipcode; ?>">
        <input type="hidden" name="delivery_cust_city" value="<?php echo  $client_info->city; ?>">
        <input type="hidden" name="delivery_zip_code" value="<?php echo  $client_info->zipcode; ?>">
        <div class="modal-footer">
            <a href="#" class="btn btn-default" data-dismiss="modal"><?= lang('close') ?></a>
            <button type="submit" class="btn btn-success" id="submitBtn">Process Payment</button>
        </div>
        </form>

    </div>
</div>