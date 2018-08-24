<?php 
    $industry = $contact[0]->industries;
    if(!empty($industry)){
        $industries = explode(',',$industry);
        $industry = $industries[0];
        $query = "select a.".$industry."_keyword as keyword, a.".$industry."_value as value, a.".$industry."_competition as competition from `tbl_industryseo` as a limit 20";
        $seo_values = $this->db->query($query)->result_array();
    }
    $price_tier = $request_proposal_info[0]->price_category;
    if(!empty($price_tier))
    {
        $query = "select a.".$price_tier." as value from `tbl_pricekeyword` as a where a.keyword='SEO 5 Keywords' or a.keyword='SEO 10 Keywords' or a.keyword='SEO 15 Keywords' or a.keyword='SEO 20 Keywords' or a.keyword='SEO Campaign Fee'";
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

<div class="seo card">
    <div class="card-header"><h3>SEARCH ENGINE OPTIMIZATION (SEO)</h3></div>
    <?php 
        $subcontent= $this->db->where('template','seo')->get('tbl_proposal_template')->result();
        print_r($subcontent[0]->content);
    ?>
    <div class="card-body">
            
        
            <div class="seo-table table-responsive">
            <?php if(!empty($seo_values)){
                $count = count($seo_values);
                foreach ($seo_values as $key => $seo_item)
                {    
            ?>
                <?php if ($key < $count/2 ){?>
                <?php if ($key == 0){?>
                <div class="col-sm-6 padding-top-10">
                    <table class="table table-bordered color-bordered-table info-bordered-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>KEYWORDS</th>
                                <th>VOLUME</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                <?php }?>
                            <tr>
                                <td><?php echo $key + 1;?></td>
                                <td><?php echo $seo_item['keyword'];?></td>
                                <td><?php echo $seo_item['value'];?></td>
                                <td><?php echo $seo_item['competition'];?></td>
                            </tr>
                <?php if ($key == ($count/2) -1 || $key == $count - 1 ){?>
                        </tbody>
                    </table>
                </div>
                <?php }?>
                <?php }else{?>
                    <?php if ($key == $count/2){?>
                            <div class="col-sm-6 padding-top-10">
                                <table class="table table-bordered color-bordered-table info-bordered-table">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>KEYWORDS</th>
                                            <th>VOLUME</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                            <?php }?>
                                        <tr>
                                            <td><?php echo $key + 1;?></td>
                                            <td><?php echo $seo_item['keyword'];?></td>
                                            <td><?php echo $seo_item['value'];?></td>
                                            <td><?php echo $seo_item['competition'];?></td>
                                        </tr>
                            <?php if ($key == ($count/2) -1 || $key == $count -1 ){?>
                                    </tbody>
                                </table>
                            </div>
                    <?php }?>
                <?php }?>
            <?php 
                }
            }
            ?>
            </div>
            <div class="seo-footer">
                <h4 class="text-center">MONTHLY PAYMENT OPTIONS</h4>
                <div class="total text-center">
                    <code class="text-dark">
                        5 Keywords: $<?php if(!empty($keyword_5)){echo $keyword_5;}else{echo 'no value';}?>   |   10 Keywords: $<?php if(!empty($keyword_10)){echo $keyword_10;}else{echo 'no value';}?>   |   15 Keywords: $<?php if(!empty($keyword_15)){echo $keyword_15;}else{echo 'no value';}?>   |   20 Keywords: $<?php if(!empty($keyword_20)){echo $keyword_20;}else{echo 'no value';}?>
                        <br/>
                        All SEO options require $<?php if(!empty($campaign_fee)){echo $campaign_fee;}else{echo 'no value';}?> campaign setup fee 
                    </code>
                </div>
            </div>
    </div>
    </div>