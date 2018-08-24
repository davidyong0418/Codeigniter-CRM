<?php
    $websites = $this->db->where('contact_id',$contact_id)->get('tbl_website_url')->result_array();
    $whitebrand = $contact[0]->white_brand;
    $query = "select a.".$whitebrand." as value from `tbl_whitelabelkeyword` as a where a.keyword='Award Image 1' or a.keyword='Award Image 2' or a.keyword='Award Image 3' or a.keyword='Award Image 4' or a.keyword='Award Image 5'";
    $img_urls = $this->db->query($query)->result_array();
?>
<div class="why-choose-us card">
    <div class="card-header">
        <h3>YOU HAVE MANY CHOICES,WHY CHOOSE US?</h3>
    </div>
    <div class="card-body display-flex">
        <div class="col-sm-8">
            <?php 
                $subcontent= $this->db->where('template','why_us_page')->get('tbl_proposal_template')->result();
                print_r($subcontent[0]->content);
            ?>
            <div>
                <strong class="custom-color">HERE ARE A FEW OF THE WEBSITES WE COMPLETED RECENTLY:</strong>
                <ul>
                    <?php if(!empty($websites))
                    {
                        foreach($websites as $website)
                        {
                    ?>
                        <li><a href="<?php echo $website['website_url'];?>"><?php echo $website['label'];?></a></li>
                    <?php
                        }
                    }
                    ?>
                </ul>
            </div>
        </div>
        <div class="col-sm-4">
            <?php 
            if(!empty($img_urls))
            {
                foreach($img_urls as $img_item){?>
                <img width="100%" src="http://<?php echo $img_item['value'];?>" align="right" hspace="12">
            <?php 
                }
            }?>
        </div>
        </div>
    </div>