<div class="col-md-3"></div>
<div class="col-md-6">
    <form action="<?= $action; ?>/_payment" method="post" id="payuForm" name="payuForm">
        <input type="hidden" name="key" value="<?= $mkey ?>"/>
        <input type="hidden" name="hash" value="<?= $hash ?>"/>
        <input type="hidden" name="txnid" value="<?= $tid ?>"/>

        <div class="form-group">
            <label class="control-label">Total Payable Amount</label>
            <input class="form-control" name="amount" value="<?= $amount ?>" readonly/>
        </div>
        <div class="form-group">
            <label class="control-label">Your Name</label>
            <input class="form-control" name="firstname" id="firstname" value="<?= $name ?>" readonly/>
        </div>
        <div class="form-group">
            <label class="control-label">Email</label>
            <input class="form-control" name="email" id="email" value="<?= $mailid ?>" readonly/>
        </div>
        <div class="form-group">
            <label class="control-label">Phone</label>
            <input class="form-control" name="phone" value="<?= $phoneno ?>" readonly/>
        </div>
        <div class="form-group">
            <label class="control-label"> Booking Info</label>
            <textarea class="form-control" name="productinfo" readonly><?= $productinfo ?></textarea>
        </div>
        <div class="form-group">
            <label class="control-label">Address</label>
            <input class="form-control" name="address1" value="<?= $address ?>" readonly/>
        </div>
        <div class="form-group">
            <input name="surl" value="<?= $sucess ?>" size="64" type="hidden"/>
            <input name="furl" value="<?= $failure ?>" size="64" type="hidden"/>
            <input type="hidden" name="service_provider" value="" size="64"/>
            <input name="curl" value="<?= $cancel ?> " type="hidden"/>
        </div>
        <div class="form-group text-center">
            <input type="submit" value="Pay Now" class="btn btn-success btn-block"/></td>
        </div>
    </form>
</div>
<div class="col-md-3"></div>
