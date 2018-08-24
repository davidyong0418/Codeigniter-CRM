<?php 
    $price_tier = $request_proposal_info[0]->price_category;
    if(!empty($price_tier))
    {
        $query = "select a.".$price_tier." as value from `tbl_pricekeyword` as a where a.keyword='SMM 05/20 Hours' or a.keyword='SMM 10/40 Hours' or a.keyword='SMM 15/60 Hours' or a.keyword='SMM 20/80 Hours' or a.keyword='SMM Campaign Fee'";
        $price_values = $this->db->query($query)->result_array();
        if(!empty($price_values)){
            $keyword_5 = $price_values[0]['value'];
            $keyword_10 = $price_values[1]['value'];
            $keyword_15 = $price_values[2]['value'];
            $keyword_20 = $price_values[3]['value'];
            $campaign_fee = $price_values[4]['value'];
        }
    }
?>
<div class="smm card">
        <div class="card-header"><h3>ORGANIC SOCIAL MEDIA MANAGEMENT (SMM)</h3></div>
        <?php 
            $subcontent= $this->db->where('template','smm')->get('tbl_proposal_template')->result();
            print_r($subcontent[0]->content);
        ?>
            <div class="assessment">
                <p>
                    <strong class="custom-color">ASSESSMENT</strong>
                    <span class="custom-color">-</span>
                    Overall, the social media platforms are outdated and inconsistent.  They are not appealing to the desired audience and lack content, followers, and reviews.  

                </p>
            </div>
           
            <div class="smm-footer card-body">
                        <h4 class="text-center">PAYMENT OPTIONS</h4>
                        <div class="total text-center">
                            <ul class="custom-list-none">
                                <li>
                                    <code class="text-dark">20 Hours Per Month (5 per week): $<?php if(!empty($keyword_5)){echo $keyword_5;}else{echo 'no value';}?>   |   40 Hours Per Month (10 per week): $<?php if(!empty($keyword_10)){echo $keyword_10;}else{echo 'no value';}?></code>
                                </li>
                                <li>
                                    <code class="text-dark">60 Hours Per Month (15 per week): $<?php if(!empty($keyword_15)){echo $keyword_15;}else{echo 'no value';}?>   |   80 Hours Per Month (20 per week): $<?php if(!empty($keyword_20)){echo $keyword_20;}else{echo 'no value';}?></code>
                                </li>
                                <li>
                                    <code class="text-dark">All SMM options require a one-time $<?php if(!empty($campaign_fee)){echo $campaign_fee;}else{echo 'no value';}?> campaign design fee </code>
                                </li>
                            </ul>
                        </div>
            </div>
    </div>