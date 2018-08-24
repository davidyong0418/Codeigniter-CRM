<?php
?>
<div class="proposals">
    <?php //$this->load->view('admin/proposals/htmlheader');?>
</div>
<style>
#top-menu{
    padding-left: 10px;
}
#top-menu a{
    background-color:#f0f0f0;
}
#top-menu a.active{
    border-left:3px solid #6ec613 !important;
    font-weight:bold;
    border-color: #f0f0f0;
    color: #555;
    background-color:#E7E7E7;
    
}
#top-menu a:hover{
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
.main-proposal-pages{
    background-color:#E7E7E7;
    padding:50px 100px 10px 100px;
}
</style>
<script src="<?= base_url() ?>/assets/node_modules/sticky-kit-master/dist/sticky-kit.min.js"></script>
<div class="row" id="proposal-pages">
                    <div class="col-lg-3 col-xlg-2 col-md-4 left-navbar">
                    <div class="white-brand-log">
                        <?php if (!empty($white_brand_logo)){?>
                            <img width="240" id="Picture 1" src="<?php echo $white_brand_logo;?>">
                        <?php }?>
                    </div>
                    <div class = "salesrep-contact-info">
                        
                    </div>
                   
                        <div class="stickyside">
                            <div class="list-group" id="top-menu">
                                <?php if(!empty($request_proposal_info[0]->cover_page)){?>
                                    <a href="#cover_page" class="list-group-item">Cover Page</a>
                                <?php }?>
                                <?php if(!empty($request_proposal_info[0]->branding)){?>
                                    <a href="#branding" class="list-group-item">Branding</a>
                                <?php }?>
                                <?php if(!empty($request_proposal_info[0]->website_analysis)){?>
                                    <a href="#website_analysis" class="list-group-item">Website Analysis</a>
                                <?php }?>
                                <?php if(!empty($request_proposal_info[0]->website_proposal)){?>
                                    <a href="#website_proposal" class="list-group-item">Website Proposal</a>
                                <?php }?>
                                <?php if(!empty($request_proposal_info[0]->seo)){?>
                                    <a href="#seo" class="list-group-item">Search Engine Optimization</a>
                                <?php }?>
                                <?php if(!empty($request_proposal_info[0]->sea)){?>
                                    <a href="#sea" class="list-group-item">Search Engine Advertising</a>
                                <?php }?>
                                <?php if(!empty($request_proposal_info[0]->smm)){?>
                                    <a href="#smm" class="list-group-item">Social Media Management</a>
                                <?php }?>
                                <?php if(!empty($request_proposal_info[0]->sma)){?>
                                    <a href="#sma" class="list-group-item">Social Media Advertising</a>
                                <?php }?>
                                <?php if(!empty($request_proposal_info[0]->content_marketing)){?>
                                    <a href="#content_marketing" class="list-group-item">Content Marketing</a>
                                <?php }?>
                                <?php if(!empty($request_proposal_info[0]->marketing_analysis)){?>
                                    <a href="#marketing_analysis" class="list-group-item">Marketing Analysis</a>
                                <?php }?>

                                <?php if(!empty($request_proposal_info[0]->recommendations)){?>
                                    <a href="#recommendations" class="list-group-item">Recommendations</a>
                                <?php }?>
                                <?php if(!empty($request_proposal_info[0]->why_us_page)){?>
                                    <a href="#why_us_page" class="list-group-item">Why Us</a>
                                <?php }?>
                                   
                            </div>
                            
                        </div>
                    </div>
                    <div class="col-lg-9 col-xlg-10 col-md-8">
                    <a data-toggle="modal" data-target="#myModal" href="<?= base_url()?>admin/proposals/discuss_proposal">Discuss</a>
                        <div class="card">
                            <div class="card-body main-proposal-pages">
                                <?php
                                    if(!empty($request_proposal_info[0]->cover_page)){?>
                                    <div id="cover_page" class="proposal-page">
                                    <?php
                                        $this->load->view('admin/proposals/template/cover_page');
                                    ?>
                                </div>
                                <?php }?>
                                
                                <?php
                                 if(!empty($request_proposal_info[0]->branding)){?> 
                                 <div id="branding" class="proposal-page">                                 
                                 <?php
                                    $this->load->view('admin/proposals/template/branding');
                                ?>
                                </div>
                                <?php }?>
                                
                                <?php
                                 if(!empty($request_proposal_info[0]->website_analysis)){?>  
                                 <div id="website_analysis" class="proposal-page">                                
                                 <?php
                                    $this->load->view('admin/proposals/template/website_analysis');
                                ?>
                                </div>
                                <?php }?>
                                
                                <?php
                                 if(!empty($request_proposal_info[0]->website_proposal)){?>   
                                 <div id="website_proposal" class="proposal-page">                               
                                 <?php
                                    $this->load->view('admin/proposals/template/website_proposal');
                                ?>
                                </div>
                                <?php }?>
                                <?php
                                 if(!empty($request_proposal_info[0]->seo)){?>
                                 <div id="seo" class="proposal-page">                                  
                                 <?php
                                    $this->load->view('admin/proposals/template/seo');
                                 
                                ?>
                                </div>
                                 <?php }?>
                                
                                <?php
                                 if(!empty($request_proposal_info[0]->sea)){?>
                                 <div id="sea" class="proposal-page">                                  
                                 <?php
                                    $this->load->view('admin/proposals/template/sea');
                                ?>
                                </div>
                                 <?php }?>
                                
                                <?php
                                 if(!empty($request_proposal_info[0]->smm)){?>
                                 <div id="smm" class="proposal-page">                                  
                                 <?php
                                    $this->load->view('admin/proposals/template/smm');
                                ?>
                                </div>
                                <?php }?>
                                
                                <?php
                                 if(!empty($request_proposal_info[0]->sma)){?> 
                                 <div id="sma" class="proposal-page">                                 
                                 <?php
                                    $this->load->view('admin/proposals/template/sma');
                                ?>
                                </div>
                                 <?php }?>
                                
                                <?php
                                 if(!empty($request_proposal_info[0]->content_marketing)){?>
                                 <div id="content_marketing" class="proposal-page">                                  
                                 <?php
                                    $this->load->view('admin/proposals/template/content_marketing');
                                ?>
                                </div>
                                 <?php }?>
                                
                                <?php
                                 if(!empty($request_proposal_info[0]->marketing_analysis)){?>
                                    <div id="marketing_analysis" class="proposal-page">                                  
                                 <?php
                                    $this->load->view('admin/proposals/template/marketing_analysis');
                                ?>
                                </div>
                                <?php  }?>
                                
                                <?php
                                 if(!empty($request_proposal_info[0]->recommendations)){?>
                                 <div id="recommendations" class="proposal-page">                                  
                                    <?php
                                        $this->load->view('admin/proposals/template/recommendations');
                                    ?>
                                </div>
                                 <?php }?>
                                
                                <?php
                                 if(!empty($request_proposal_info[0]->why_us_page)){?>
                                    <div id="why_us_page" class="proposal-page">
                                 <?php
                                    $this->load->view('admin/proposals/template/why_us_page');
                                ?>
                                </div>
                                 <?php }?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php 
                    if ($user_type == 4)
                    {
                        $this->db->set('view_state',1);
                        $this->db->where('contact_id',$contact_id);
                        $this->db->update('tbl_proposal');
                    }
                ?>

                <script>
                   $(".stickyside").stick_in_parent({
                    offset_top: 100,
                    
                });
    $('.stickyside a').click(function() {
        $('html, body').animate({
            scrollTop: $($(this).attr('href')).offset().top - 100
        }, 500);
        return false;
    });
    // This is auto select left sidebar
    // Cache selectors
    // Cache selectors
    var lastId,
        topMenu = $(".stickyside"),
        topMenuHeight = topMenu.outerHeight(),
        // All list items
        menuItems = topMenu.find("a"),
        // Anchors corresponding to menu items
        scrollItems = menuItems.map(function() {
            var item = $($(this).attr("href"));
            if (item.length) {
                return item;
            }
        });

    // Bind click handler to menu items
        var nav_hegiht =  $('#proposal-pages').height();
        $('.left-navbar').height(nav_hegiht);
    // Bind to scroll
    $(window).scroll(function() {
        // Get container scroll position
        var fromTop = $(this).scrollTop() + topMenuHeight - 250;

        // Get id of current scroll item
        var cur = scrollItems.map(function() {
            if ($(this).offset().top < fromTop)
                return this;
        });
        // Get the id of the current element
        cur = cur[cur.length - 1];
        var id = cur && cur.length ? cur[0].id : "";

        if (lastId !== id) {
            lastId = id;
            // Set/remove active class
            menuItems
                .removeClass("active")
                .filter("[href='#" + id + "']").addClass("active");
        }
    });
    </script>