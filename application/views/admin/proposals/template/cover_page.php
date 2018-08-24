<?php 
    $sales_rep = $contact[0]->sales_rep;
    if(!empty($sales_rep))
    {
        $query = "SELECT avatar FROM `tbl_users` AS a JOIN `tbl_account_details` AS b ON a.`user_id` = b.`user_id` WHERE a.`user_id` = ".$sales_rep;
        $sales_logo = $this->db->query($query)->result_array();
    }
    $company = $contact[0]->company_id;
    if(!empty($company))
    {
        $company = $this->db->where('id',$company)->get('tbl_company')->result();
        $company_name = $company[0]->company_name;
    }
    $created_date = $this->db->where('contact_id',$contact_id)->get('tbl_proposal')->result();
    if(!empty($created_date))
    {
        $date = substr($created_date[0]->created_at, 0, 7);
    }
    // print_r($company_name);
    
?>


<div class="cover-page card">
        <div class="card-body">
            <div class="text-center col-sm-12">
            <?php if (!empty($white_brand_logo)){?>
                <img width="240" id="Picture 1" src="<?php echo $white_brand_logo;?>">
            <?php }?>
            </div>
            <div class="text-center col-sm-12" style="margin-top: 40px;font-size:50px; font-family: 'Times New Roman',serif "><strong>ANALYSIS & PROPOSAL FOR</strong></div>
            
            <div class="text-center col-sm-12"><strong style="font-size:50px; font-family: 'Times New Roman',serif "><?php if(!empty($company_name)){echo $company_name;}?></strong></div>
            <div class="text-center col-sm-12">
                <?php if(!empty($date))
                {
                ?>
                    <span style="font-size:16.0pt"><?php echo $date;?></span>
                <?php 
                }else{ ?>
                    <span style="font-size:16.0pt">EFFECTIVE (DATE)</span>
                <?php }?>
            </div>
            <div class="text-center col-sm-12" style="margin-top: 250px;">
            <?php if(!empty($sales_logo[0]['avatar'])){
            ?>
             <img width="280px" id="Picture 1" src="<?php echo base_url().$sales_logo[0]['avatar'];?>"></div>
            <?php 
            }
            ?>
           
        </div>
    </div>