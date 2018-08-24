<?php
$allow_customer_edit_amount = config_item('allow_customer_edit_amount');
?>
<div class="panel panel-custom" id="replace">
    <div class="panel-heading">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span
                class="sr-only">Close</span></button>
        <h4 class="modal-title">Paying <strong>
                <?php if (!empty($allow_customer_edit_amount) && $allow_customer_edit_amount == 'No') { ?>
                    <?= display_money($amount, $cur->symbol); ?>
                <?php } ?>
            </strong> for Invoice # <?= $reference_no ?> via PayUmoney</h4>
    </div>
    <div class="modal-body">
        <?php

        echo form_open($action, $attributes);
        ?>
        <?php if (!empty($allow_customer_edit_amount) && $allow_customer_edit_amount == 'No') { ?>
            <input name="amount" value="<?= ($amount) ?>" type="hidden">
        <?php } ?>

        <div class="form-group">
            <label class="col-lg-3 control-label"><?= lang('amount') ?> ( <?= $cur->symbol ?>) </label>
            <div class="col-lg-7">
                <?php if (!empty($allow_customer_edit_amount) && $allow_customer_edit_amount == 'Yes') { ?>
                    <input type="text" required name="amount" data-parsley-type="number"
                           max="<?= $amount ?>" class="form-control"
                           value="<?= ($amount) ?>">
                <?php } else { ?>
                    <input type="text" class="form-control" value="<?= display_money($amount) ?>"
                           readonly>
                <?php } ?>
            </div>
        </div>

        <input type="hidden" name="key" value="<?= $mkey ?>"/>
        <input type="hidden" name="hash" value="<?= $hash ?>"/>
        <input type="hidden" name="txnid" value="<?= $tid ?>"/>

        <input type="hidden" name="productinfo" value="Payment for Invoice # <?= $reference_no ?>"/>
        <input name="surl" value="<?= $sucess ?>" size="64" type="hidden"/>
        <input name="furl" value="<?= $failure ?>" size="64" type="hidden"/>
        <input type="hidden" name="service_provider" value="" size="64"/>
        <input name="curl" value="<?= $cancel ?> " type="hidden"/>

        <div class="form-group">
            <label class="col-lg-3 control-label"><?= lang('name') ?> </label>
            <div class="col-lg-7">
                <input type="text" required name="firstname" class="form-control"
                       value="<?= $firstname ?>">
            </div>
        </div>
        <div class="form-group">
            <label class="col-lg-3 control-label"><?= lang('email') ?> </label>
            <div class="col-lg-7">
                <input type="text" required name="email" class="form-control"
                       value="<?= $mailid ?>">
            </div>
        </div>
        <div class="form-group">
            <label class="col-lg-3 control-label"><?= lang('phone') ?> </label>
            <div class="col-lg-7">
                <input type="text" required name="phone" class="form-control"
                       value="<?= $phoneno ?>">
            </div>
        </div>
        <div class="form-group">
            <label class="col-lg-3 control-label"><?= lang('address') ?> </label>
            <div class="col-lg-7">
                <textarea class="form-control" name="address"><?= $address ?></textarea>
            </div>
        </div>

        <div class="modal-footer"><a href="#" class="btn btn-default" data-dismiss="modal"><?= lang('close') ?></a>
            <button type="submit" id="proceed" class="btn btn-success">Proceed Payment</button>
        </div>
        <?php echo form_close(); ?>
    </div>
</div>
</div>
<script type="text/javascript">
    $(function () {
        $("#proceed").click(function () {
            $('form#my_id').attr("id", "payuForm");
        });
        $('form#my_id').submit(function (event) {
            event.preventDefault(); // Prevent the form from submitting via the browser
            var form = $(event.target);
            $.ajax({
                type: form.attr('method'),
                url: form.attr('action'),
                data: form.serialize()
            }).done(function (data) {
                $('#my_id').empty();
                $('#replace').html(data);
                $('form#my_id').attr("id", "payuForm");
                $("input,textarea").prop("readonly", true);
                $("#proceed").html('Pay Now');
                // Optionally alert the user of success here...
            }).fail(function (data) {
                // Optionally alert the user of an error here...
            });
        });
    });
</script>