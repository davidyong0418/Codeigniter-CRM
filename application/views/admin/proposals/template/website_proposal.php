   <?php 
    $price_tier = $request_proposal_info[0]->price_category;
    if(!empty($price_tier))
    {
        $query = "select a.".$price_tier." as value from `tbl_pricekeyword` as a where a.keyword='Website Regular'";
        $price_values = $this->db->query($query)->result_array();
        if(!empty($price_values)){
            $website_regular = $price_values[0]['value'];
        }
    }
   ?>
   
   <div class="website-proposal card">
   <div class="card-header"><h3>WEBSITE PROPOSAL</h3></div>
        <?php 
            $subcontent = $this->db->where('template','website_proposal')->get('tbl_proposal_template')->result();
            print_r($subcontent[0]->content);

        ?>
    <div class="card-footer">
        <h4 class="text-center">PAYMENT OPTIONS</h4>
        <div class="total text-center">
            <code class="text-dark">Cost $<?php if(!empty($website_regular)){echo $website_regular;}else{echo 'no value';}?> (50% upfront & 50% upon completion)  OR   6 payments of $601   OR   12 payments of $328</code>
        </div>
    </div> 
</div>
