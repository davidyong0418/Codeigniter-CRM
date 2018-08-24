<?php 
    $price_tier = $request_proposal_info[0]->price_category;
    if(!empty($price_tier))
    {
        $query = "select a.".$price_tier." from `tbl_pricekeyword` as a where a.keyword='Branding Complete Package' or a.keyword='Branding Logo Only' or a.keyword='Branding Identity Development' or a.keyword='Branding Tagline Development'";
        $price_values = $this->db->query($query)->result_array();
        if(!empty($price_values)){
            $complete = $price_values[0][1];
            $logo = $price_values[1][1];
            $identity = $price_values[2][1];
            $tagline = $price_values[3][1];
        }
    }
?>


<div class="brand-development card">
    <div class="card-header"><h3>BRAND DEVELOPMENT</h3></div>
        <?php 
            $subcontent = $this->db->where('template','branding')->get('tbl_proposal_template')->result();
            print_r($subcontent[0]->content);
        ?>
    <div class="card-body">
        <div class="seo-footer">
            <h4 class="text-center"><code class="text-dark">PAYMENT OPTIONS</code></h4>
            <div class="total text-center">
                <code class="text-dark">Complete Package: $<?php if(!empty($complete)){echo $complete;}else{echo 'no value';}?>   |   Logo Only: $<?php if(!empty($logo)){echo $logo;}else{echo 'no value';}?>   |   Identity Development: $<?php if(!empty($identity)){echo $identity;}else{echo 'no value';}?>   |   Tagline Development: $<?php if(!empty($tagline)){echo $tagline;}else{echo 'no value';}?></code>
            </div>
        </div>
    </div>
</div>