
<?php 
    $industry = $contact[0]->industries;
    $area = $contact[0]->g_areas_served;
    if(!empty($industry)){
        $industries_id = explode(',',$industry);
        $this->db->select('industry');
        $this->db->where('id', $industries_id[0]);
        $query = $this->db->get('tbl_industry');
        $industries = $query->result_array();
        $industry_str = '';
        foreach ($industries as $key => $item)
        {
            if($key==0)
            {
                $industry_str = $item['industry'];
            }
            else{
                $industry_str = $industry_str.','.$item['industry'];
            }
        }
        $total_vol_query = "SELECT SUM(a.".$industries_id[0].") as total FROM `tbl_industryroi` AS a WHERE id >3";
        $total_vol = $this->db->query($total_vol_query)->result_array();
        $divid_total = intval($total_vol[0]['total']/12);
        $query = "select a.".$industries_id[0]." from `tbl_industryroi` as a where a.keyword='Avg Client Value' or a.keyword='Market Share Low' or a.keyword='Market Share High'";
        $industryroi_info = $this->db->query($query)->result_array();
        // print_r($industryroi_info);exit;
        $avg_client_val = $industryroi_info[0][$industries_id[0]];
        $market_share_low = $industryroi_info[1][$industries_id[0]];
        $market_share_high = $industryroi_info[2][$industries_id[0]];
        $data = $this->db->where('id >','3')->get('tbl_industryroi')->result_array();
        foreach ($data as $key => $chart_item)
        {
            $chart_data[] = $chart_item[$industries_id[0]];
        }
        
    }
 ?>


<div class="marketing-analysis card">
    <div class="card-header">
        <h3>MARKET SHARE OPPORTUNITY & ROI ANALYSIS</h3></div>
    <?php 
        $subcontent= $this->db->where('template','marketing_analysis')->get('tbl_proposal_template')->result();
        print_r($subcontent[0]->content);
    ?>
        
    </div>
    <script>
        var industry = "<?php if(!empty($industry_str)){echo $industry_str;}else{echo 'no industry';}?>";
        var area = "<?php if(!empty($area)){echo $area;}else{echo 'no value';}?>";
        var divid_total = "<?php if(!empty($divid_total)){echo $divid_total;}else{echo 'no value';}?>";
        var total = "<?php if(!empty($total_vol[0]['total'])){echo $total_vol[0]['total'];}else{echo 'no value';}?>";
        var avg_client_val = "<?php if(!empty($avg_client_val)){echo $avg_client_val;}else{echo '(no value)';}?>";
        var marketpportunity = "<?php if(!empty($avg_client_val) && !empty($total_vol[0]['total'])){echo $avg_client_val * $total_vol[0]['total'];}else{echo 'no value';}?>";
        var market_share_low = "<?php if(!empty($market_share_low)){echo $market_share_low;}else{echo '(no value)';}?>";
        var market_share_high = "<?php if(!empty($market_share_high)){echo $market_share_high;}else{echo '(no value)';}?>";
        $(document).ready(function(){
            $('.marketing-analysis .industry').html(industry);
            $('.marketing-analysis .target-area').html(area);
            $('.marketing-analysis .servicearea').html(area);
            $('.marketing-analysis .times').html(divid_total);
            $('.marketing-analysis .value0').html(total);
            $('.marketing-analysis .avgclientvalue').html(avg_client_val);
            $('.marketing-analysis .totalsearch').html(marketpportunity);
            // clientvalue
            $('.marketing-analysis .high-market').html(market_share_high);
            $('.marketing-analysis .low-market').html(market_share_low);
            $('.marketing-analysis .value1').html(marketpportunity*market_share_low);
            $('.marketing-analysis .value2').html(marketpportunity*market_share_high);
        });
       
    </script>
    <script>
        var data = <?php echo json_encode($chart_data);?>;
        $(function() {
$('#marketing_chart').show();
new Chart(document.getElementById("marketing_chart"),
    {
        "type":"bar",
        "data":{"labels":["January","February","March","April","May","June","July","Aug","Sep", "Oct", "Nov","Dec"],
        "datasets":[{
                        "label":"Average Searches Per Month",
                        "data":data,
                        "fill":false,
                        "backgroundColor":["rgba(255, 99, 132, 0.2)","rgba(255, 159, 64, 0.2)","rgba(255, 205, 86, 0.2)","rgba(75, 192, 192, 0.2)","rgba(54, 162, 235, 0.2)","rgba(153, 102, 255, 0.2)","rgba(201, 203, 207, 0.2)","rgb(255, 159, 64,0.2)","rgb(255, 205, 86,0.2)","rgb(75, 192, 192,0.2)","rgba(54, 162, 235, 0.2)","rgba(153, 102, 255, 0.2)"],
                        "borderColor":["rgb(255, 99, 132)","rgb(255, 159, 64)","rgb(255, 205, 86)","rgb(75, 192, 192)","rgb(54, 162, 235)","rgb(153, 102, 255)","rgb(201, 203, 207)","rgb(255, 159, 64)","rgb(255, 205, 86)","rgb(75, 192, 192)","rgb(54, 162, 235)","rgb(153, 102, 255)"],
                        "borderWidth":1}
                    ]},
        "options":{
            "scales":{"yAxes":[{"ticks":{"beginAtZero":true}}]}
        }
    });
});
</script>
    <script src="<?= base_url()?>assets/node_modules/Chart.js/Chart.min.js"></script>

