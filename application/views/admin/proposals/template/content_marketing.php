<?php
    $price_tier = $request_proposal_info[0]->price_category;
    if(!empty($price_tier))
    {
        $query = "select a.".$price_tier." as value from `tbl_pricekeyword` as a where a.keyword='Content Marketing Per Article'";
        $price_values = $this->db->query($query)->result_array();
        if(!empty($price_values)){
            $per_article = $price_values[0]['value'];
        }
    }
    $industry = $contact[0]->industries;
    if(!empty($industry)){
        $industries = explode(',',$industry);
        $query = "select a.* from `tbl_industrycm` as a limit 10";
        $cm_values = $this->db->query($query)->result_array();
        $buyer_search = '';
        $explorer_search = '';
        foreach ($industries as $key => $item)
        {
            if($key == 0)
            {
                $buyer_search = $cm_values[0][$item];
                $explorer_search = $cm_values[1][$item];
            }
            else{
                $buyer_search = $buyer_search .', '.$cm_values[0][$item];
                $explorer_search = $explorer_search .', '.$cm_values[1][$item];
            }
            
        }
        $count = count($cm_values) - 2; 
    }
   


?>

<div class="content-marketing card">
        <div class="card-header">
        <h3>CONTENT MARKETING</h3>
        </div>
        <?php 
            $subcontent = $this->db->where('template','content_marketing')->get('tbl_proposal_template')->result();
            print_r($subcontent[0]->content);
        ?>
        <div class="card-body">
            <ol>
            <?php if(!empty($cm_values)){
            foreach($cm_values as $key => $cm_item)
            {
                if ($key > 1){
            ?>
                <li>
                    <p> <?php echo $cm_item[$industries[0]]; ?></p>
                </li>
            <?php
                }
            }
        }
            ?>
            </ol>
            <div class="smm-footer">
                <h4 class="text-center">PAYMENT OPTIONS</h4>
                <div class="total text-center">
                    <ul class="custom-style-none">
                        <li>
                            <code class="text-dark">1 Article Per Month: $<?php if(!empty($per_article)){echo $per_article;}else{echo 'no value';}?>   |   2 Articles Per Month: $<?php if(!empty($per_article)){echo $per_article*2;}else{echo 'no value';}?>   |   4 Articles Per Month: $<?php if(!empty($per_article)){echo $per_article*4;}else{echo 'no value';}?></code>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
<script>
    var buyer_search = "<?php if(!empty($buyer_search)){echo $buyer_search;}else{echo 'No value';}?>";
    var explorer_search = "<?php if(!empty($explorer_search)){echo $explorer_search;}else{echo 'No value';}?>";
    var count ="<?php if(!empty($count)){echo $count;}else{echo 'No value';}?>";
    console.log(buyer_search);
    $(document).ready(function(){
        $('.content-marketing .buyer-search').html(buyer_search);
        $('.content-marketing .explorer-search').html(explorer_search);
        $('.content-marketing .numerical-input').html(count);
    });
</script>