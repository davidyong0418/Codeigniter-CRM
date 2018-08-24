<?php 
    $price_tier = $request_proposal_info[0]->price_category;
    if(!empty($price_tier))
    {
        $query = "select a.".$price_tier." as value from `tbl_pricekeyword` as a where a.keyword='MDA <1250 Budget Management Fee' or a.keyword='MDA <1250 Budget Setup Fee' or a.keyword='MDA <5000 Budget Management Fee' or a.keyword='MDA <5000 Budget Setup Fee' or a.keyword='MDA <10000 Budget Management Fee' or a.keyword='MDA <10000 Budget Setup Fee' or a.keyword='MDA >10000 Budget Management Fee' or a.keyword='MDA >10000 Budget Setup Fee'";
        $price_values = $this->db->query($query)->result_array();
        if(!empty($price_values)){
            $management_1250 = $price_values[0]['value'];
            $fee_1250 = $price_values[1]['value'];
            $management_5000 = $price_values[2]['value'];
            $fee_5000 = $price_values[3]['value'];
            $management_10000 = $price_values[4]['value'];
            $fee_10000 = $price_values[5]['value'];
            $management_m_10000 = $price_values[6]['value'];
            $fee_m_10000 = $price_values[7]['value'];
        }
    }

?>
<div class="sma card">
    <div class="card-header">
        <h3>SOCIAL MEDIA ADVERTISING (SMA)</h3>
    </div>
    <?php 
        $subcontent = $this->db->where('template','sma')->get('tbl_proposal_template')->result();
        print_r($subcontent[0]->content);
    ?>
</div>

<script>
    var table = `<div class="card-body sma-table-content">
            <div class="sea-table">
                <table class="table table-bordered color-bordered-table info-bordered-table">
                        <thead>
                            <tr>
                                <th class="text-center">MONTHLY ADVERTISING SPEND</th>
                                <th class="text-center">MONTHLY MERCHANRSIDE FEES</th>
                                <th class="text-center">ONE TIME CAMPAIGN FEE</th>
                            </tr>
                        </thead>
                        <tbody class="text-center">
                            <tr style="background-color:#FFFF00;">
                                <td>$300 - $500</td>
                                <td>NO FEE IF STACKED WITH AN</td>
                                <td>ORGANIC (SEO) PLAN</td>
                                
                            </tr>
                            <tr>
                                <td>$300 - $1,250</td>
                                <td><?php if(!empty($management_1250)){echo $management_1250;}else{echo "no value";}?></td>
                                <td>$<?php if(!empty($fee_1250)){echo $fee_1250;}else{echo "no value";}?></td>
                                
                            </tr>
                            <tr>
                                <td>$1,251 - $4,999</td>
                                <td><?php if(!empty($management_5000)){echo $management_5000;}else{echo "no value";}?></td>
                                <td>$<?php if(!empty($fee_5000)){echo $fee_5000;}else{echo "no value";}?></td>
                                
                            </tr>
                            <tr>
                                <td>$5,000 - $9,999</td>
                                <td><?php if(!empty($management_10000)){echo $management_10000;}else{echo "no value";}?></td>
                                <td>$<?php if(!empty($fee_10000)){echo $fee_10000;}else{echo "no value";}?></td>
                            </tr>
                            <tr>
                                <td>More than $10000</td>
                                <td><?php if(!empty($management_m_10000)){echo $management_m_10000;}else{echo "no value";}?></td>
                                <td>$<?php if(!empty($fee_m_10000)){echo $fee_m_10000;}else{echo "no value";}?></td>
                            </tr>
                        </tbody>
                    </table>
            </div>
        </div>`;
        $(document).ready(function(){
        $('.sma .proposal-content').html(table);

});
</script>