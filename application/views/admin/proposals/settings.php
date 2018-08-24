<style>
    #setting-menu{
    padding-left: 10px;
}
#setting-menu a{
    background-color:#f0f0f0;
}
#setting-menu a.active{
    border-left:3px solid #6ec613 !important;
    font-weight:bold;
    border-color: #f0f0f0;
    color: #555;
    background-color:#E7E7E7;
    
}
#setting-menu a:hover{
    border-left:3px solid #67e063 !important;
    background-color:#E7E7E7;
    font-weight:bold;
}
.proposal-page{
    padding: 75px;
    margin-bottom:120px;
    background: white;
}
#proposal-pages{
    /* background-color:#E7E7E7; */
}
.edit-proposal-page{
    padding: 20px 50px 10px 100px;
    /* background-color:#E7E7E7; */
}
</style>
<div class="row">
        <div class="col-md-3 hidden-print"><!-- ************ Expense Report Month Start ************-->
            <ul class="nav nav-pills nav-stacked navbar-custom-nav" id="setting-menu">
                    <li class="">
                        <a href="<?= base_url()?>admin/proposals/settings/website_analysis" class="<?php if($template == 'website_analysis'){echo 'active';};?>">ANALYSIS OF YOUR WEBSITE</a>
                    </li>
                    
                    <li class="">
                        <a href="<?= base_url()?>admin/proposals/settings/website_proposal" class="<?php if($template == 'website_proposal'){echo 'active';};?>">WEBSITE PROPOSAL</a>
                    </li>

                    <li class="">
                        <a href="<?= base_url()?>admin/proposals/settings/seo" class="<?php if($template == 'seo'){echo 'active';};?>">SEARCH ENGINE OPTIMIZATION (SEO)</a>
                    </li>

                    <li class="">
                        <a href="<?= base_url()?>admin/proposals/settings/sea" class="<?php if($template == 'sea'){echo 'active';};?>">SEARCH ENGINE ADVERTISING (SEA)</a>
                    </li>

                    <li class="">
                        <a href="<?= base_url()?>admin/proposals/settings/smm" class="<?php if($template == 'smm'){echo 'active';};?>">ORGANIC SOCIAL MEDIA MANAGEMENT (SMM)</a>
                    </li>

                    <li class="">
                        <a href="<?= base_url()?>admin/proposals/settings/sma" class="<?php if($template == 'sma'){echo 'active';};?>">SOCIAL MEDIA ADVERTISING (SMA)</a>
                    </li>

                    <li class="">
                        <a href="<?= base_url()?>admin/proposals/settings/content_marketing" class="<?php if($template == 'content_marketing'){echo 'active';};?>">CONTENT MARKETING</a>
                    </li>

                    <li class="">
                        <a href="<?= base_url()?>admin/proposals/settings/marketing_analysis" class="<?php if($template == 'marketing_analysis'){echo 'active';};?>">MARKET SHARE OPPORTUNITY & ROI ANALYSIS</a>
                    </li>
                    
                    <li class="">
                        <a href="<?= base_url()?>admin/proposals/settings/why_us_page" class="<?php if($template == 'why_us_page'){echo 'active';};?>">WHY CHOOSE US</a>
                    </li>

            </ul>
        </div><!-- ************ Expense Report Month End ************-->
        <div class="col-md-9 edit-proposal-page"><!-- ************ Expense Report Content Start ************-->
                <div class="m-b-40 default-content card padding-top-10">
                         <?php 
                            $subcontent= $this->db->where('template',$template)->get('tbl_proposal_template')->result();
                            print_r($subcontent[0]->content);
                            
                        ?>
                </div>
                <div class="card">
                    <div class="card-body">
                        <button id="edit" class="btn btn-info btn-rounded" onclick="edit()" type="button">Edit</button>
                        <button id="qwe" class="btn btn-success btn-rounded" onclick="wwww()" type="button">Save</button>
                    </div>
                </div>
            
        </div><!-- ************ Expense Report Content Start ************-->
    </div><!-- ************ Expense Report List End ************-->

    <script>
        window.edit = function() {
            $(".default-content").summernote()
        };
        window.qwe = function() {
            
        };
    </script>
    <script>
        window.wwww = function() {
            // $(".default-content").summernote("destroy");
            var summernote_text = $(".default-content").summernote("code");
    <?php echo '
            var send_data = {
                content: summernote_text,
                template: "'.$template.'"
            };
            $.ajax({
                method: "post",
                url: "'.base_url().'admin/proposals/save_content",
                data: send_data,
                datatype: "json",
                success: function(){
                    $(".default-content").summernote("destroy");
                },
                error: function(){
                  
                }
            });
        }
    </script>';
    ?>
