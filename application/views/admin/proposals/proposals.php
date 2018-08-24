<form name="myform" role="form" data-parsley-validate="" novalidate=""
      enctype="multipart/form-data"
      id="form"
      action="<?php echo base_url(); ?>admin/proposals/save_proposals/<?php
      if (!empty($action)) {
          echo $action;
      }
      ?>" method="post" class="form-horizontal contacts-form">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.16.0/jquery.validate.js"></script>
    <?php include_once 'assets/admin-ajax.php'; ?>
    <?php include_once 'assets/js/sales.php'; ?>
    <?= message_box('success'); ?>
    <?= message_box('error'); ?>

    <?php
    $created = can_action('140', 'created');
    $edited = can_action('140', 'edited');
    $deleted = can_action('140', 'deleted');
    if (!empty($created) || !empty($edited)){
    ?>
   
    <div class="row">
        <div class="col-sm-12">
            <div class="nav-tabs-custom">
                <!-- Tabs within a box -->
                <ul class="nav nav-tabs">
                    <li class="<?= $active == 1 ? 'active' : ''; ?>"><a href="<?= base_url()?>admin/proposals/manage_proposal"
                                                                      ><?= lang('all_proposals') ?></a>
                    </li>
                    <li class="<?= $active == 2 ? 'active' : ''; ?>"><a href="<?= base_url()?>admin/proposals/manage_proposal/new">New Proposal</a>
                    </li>
                    <li class="float-right">
                        <a href="<?= base_url()?>admin/proposals/settings"><i class="ti-settings"></i></a>
                    </li>
                </ul>
                <div class="tab-content bg-white">
                    <!-- ************** general *************-->
                    <div class="tab-pane <?= $active == 1 ? 'active' : ''; ?>" id="manage">
                        <?php } else { ?>
                        <div class="panel panel-custom">
                            <header class="panel-heading ">
                                <div class="panel-title"><strong><?= lang('all_proposals') ?></strong></div>
                            </header>
                            <?php } ?>
                            <div class="table-responsive">
                                <table class="table table-striped DataTables " id="DataTables" cellspacing="0"
                                       width="100%">
                                    <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Sales Rep</th>
                                        <th>Contact Name</th>
                                        <th>Company Name</th>
                                        <th>Industries</th>
                                        <th>Website Url</th>
                                        <th>Date Created</th>
                                        <th>Date Updated</th>
                                        <?php if (!empty($edited) || !empty($deleted)) { ?>
                                            <th class="hidden-print"><?= lang('action') ?></th>
                                        <?php } ?>
                                    </tr>
                                    
                                    </thead>
                                    <tbody>
                                    <?php 
                                        if(!empty($all_proposals)){
                                            $no = 1;
                                            foreach ($all_proposals as $key => $proposal){
                                                $contact = $this->db->where('id', $proposal->contact_id)->get('tbl_contact')->result();
                                                $contact = $contact[0];
                                                $sales_ref = $contact->sales_rep;
                                                $company_id = $contact->company_id;
                                                // $where = array();
                                                $user_info = $this->db->where('user_id',$sales_ref)->get('tbl_users')->result_array();
                                                if(!empty($user_info)){
                                                    $username = $user_info[0]['username'];
                                                }
                                                else{
                                                    $username = '';
                                                }
                                                $contact_name = $contact->nick_name;
                                                $industries = $contact->industries;
                                                $view_industry = '';
                                                $explode_i = explode(',', $industries);
                                                foreach ($explode_i as $key => $item){
                                                    $industry_index = $this->db->where('id',$item)->get('tbl_industry')->result_array();
                                                    if ($key == 0)
                                                    {
                                                        $view_industry = $industry_index[0]['industry'];
                                                    }
                                                    else{
                                                        $view_industry = $view_industry.','.$industry_index[0]['industry'];
                                                    }
                                                }
                                                // company name
                                                $company_info = $this->db->where('id',$company_id)->get('tbl_company')->result_array();
                                               if(!empty($company_info)){
                                                    $company_name = $company_info[0]['company_name'];
                                               }
                                               else{
                                                    $company_name = '';
                                               }
                                                // website url
                                                $website_view_info = $this->db->where('contact_id',$contact->id)->get('tbl_website_url')->result_array();
                                                $website_url ='';
                                                foreach ($website_view_info as $key => $item)
                                                {
                                                    if ($key == 0)
                                                    {
                                                        $website_url = $item['website_url'];
                                                    }
                                                    else{
                                                        $website_url = $website_url.','.$item['website_url'];
                                                    }
                                                }
                                                ?>
                                        <tr>
                                            <td><?php echo $no;$no++;?></td>
                                            <td><?php echo $username?></td>
                                            <td><?php echo $contact_name;?></td>
                                            <td><?php echo $company_name;?></td>
                                            <td><?php echo $view_industry?></td>
                                            <td><?php echo $website_url;?></td>
                                            <td><?php echo $proposal->created_at;?></td>
                                            <td><?php echo $proposal->updated_at;?></td>
                                            <td>
                                                <?php 
                                                    if($user_type == 2 || $user_type == 4 )
                                                    {
                                                        echo btn_view('admin/proposals/view_proposals/'.$proposal->link);
                                                    }
                                                    else
                                                    {
                                                        echo btn_edit('admin/proposals/manage_proposal/' .$proposal->link);
                                                        echo btn_delete('admin/proposals/delete_proposal/'.$proposal->id);
                                                        echo btn_view('admin/proposals/view_proposals/'.$proposal->link);
                                                    }
                                                 
                                                  ?>
                                            </td>
                                        </tr>
                                        <?php 
                                            }
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <?php if (!empty($created) || !empty($edited)) { ?>

                        <div class="tab-pane <?= $active == 2 ? 'active' : ''; ?>" id="proposal_new">
                        <!-- <input type="hidden" name="action" value="<?php //echo $breadcrumb_f;?>" /> -->
                            <div class="hidden">
                            <input type="hidden" name="generate_link" value="<?php if(!empty($generate_link)){echo $generate_link;}?>">
                            </div>
                            <div class="row mb-lg invoice proposal-template">
                                <div class="col-xs-12 ">
                                    <div class="row">
                                        <!-- <div class="form-group">
                                            <label class="col-lg-3 control-label"><?= lang('reference_no') ?> <span class="text-danger">*</span></label>
                                            <div class="col-lg-7">
                                                <?php $this->load->helper('string'); ?>
                                            </div>
                                        </div> -->
                                        <div class="form-group" id="border-none">
                                            <label for="field-1" class="col-sm-3 control-label"><?= lang('related_to') ?> </label>
                                            <div class="col-sm-7 custom-flex-columm">
                                                <select name="contact_id" class="form-control select_box" required="" onchange="select_contact(this.value)"
                                                        style="width: 100%">
                                                        <option value="">- Select Contact -</option>
                                                        <?php 
                                                        if(!empty($all_contacts)){
                                                        foreach($all_contacts as $contact){?>
                                                            <option value="<?php echo $contact->id;?>" <?php if(!empty($contact_id)){
                                                                if($contact->id == $contact_id){
                                                                    echo "selected";
                                                                }
                                                            }?>><?php echo $contact->nick_name;?></option>
                                                        <?php }
                                                        }?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="proposal-content">
                                        </div>
                                        <div class="form-group" id="border-none">
                                            <label for="field-1" class="col-sm-3 control-label">Sales Rep</label>
                                            <div class="col-sm-7">
                                                <select class="form-control sales-rep" name="sales_rep" id="field-1" required="">
                                                <option value="">- Select Sales Rep -</option>
                                                <?php if (!empty($all_staffes)){
                                                    foreach ($all_staffes as $staff){
                                                ?>
                                                    <option value="<?php echo $staff->user_id?>" <?php if(!empty($contact_info)){
                                                    if( $staff->user_id == $contact_info['sales_rep']){
                                                        echo 'selected';
                                                    }
                                                    }?>><?= $staff->username?></option>

                                                <?php 
                                                    }
                                                    }
                                                ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group" id="border-none">
                                            <label for="field-2" class="col-sm-3 control-label">White label brand</label>
                                            <div class="col-sm-7">
                                                <select class="form-control white-label-brand" name="white_brand" id="field-2" required="">
                                                    <option value="">- Select White label Brand -</option>
                                                    <?php if (!empty($whitebrand)){
                                                        foreach ($whitebrand as $item){
                                                    ?>
                                                        <option value="<?= $item->id?>" <?php 
                                                        if(!empty($contact_info)){
                                                            if($item->id == $contact_info['white_brand'])
                                                            {
                                                                echo 'selected';
                                                            }

                                                        }
                                                        ?>><?= $item->white_brand?></option>
                                                    <?php }
                                                    }?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group" id="border-none">
                                            <label for="field-3" class="col-sm-3 control-label">Company name</label>
                                            <div class="col-sm-7">
                                                <select class="form-control company-name" name="company_id" id="field-3" required="">
                                                    <option value="">- Select Company -</option>
                                                    <?php 
                                                    if(!empty($companies)){
                                                    foreach ($companies as $company){
                                                    ?>
                                                        <option value="<?php echo $company->id;?>" <?php 
                                                        if(!empty($contact_info)){
                                                            if($company->id == $contact_info['company_id'])
                                                            {
                                                                echo 'selected';
                                                            }

                                                        }
                                                        ?>><?php echo $company->company_name;?></option>
                                                    <?php }
                                                    }?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group" id="border-none">
                                            <label for="field-4" class="col-sm-3 control-label">Industry</label>
                                            <div class="col-sm-7">
                                                <select class="form-control industry" name="industries" id="field-4" required="">
                                                    <option value="">- Select Industry -</option>
                                                    <?php 
                                                    if(!empty($industries_info)){
                                                    foreach ($industries_info as $item){?>
                                                        <option value="<?php echo $item->id;?>" <?php 
                                                        if(!empty($contact_info)){
                                                            if($item->id == $contact_info['industries'])
                                                            {
                                                                echo 'selected';
                                                            }

                                                        }
                                                        ?>><?php echo $item->industry;?></option>
                                                    <?php }
                                                    }?>
                                                </select>
                                            </div>
                                        </div>

                                            <div id="website_url_form">
                                                <?php if(!empty($website_info)){
                                                    foreach ($website_info as $key => $website_item){
                                                ?>
                                                <?php if($key ==0 ){?>
                                                    <div class="form-group website-url additional_icon">
                                                        <label class="col-sm-3 control-label">Website URL</label>
                                                        <div class="col-sm-7 d-flex">
                                                            <input type="url" class="form-control" value="<?php echo $website_item->website_url;?>" name="website_url[]" required placeholder="Enter Website Url">
                                                            <input type="text" name="website_url_label[]" class="form-control margin-l-5" value="<?php echo $website_item->label;?>" required placeholder="Enter Website Type">
                                                            <div class="input-group-addon"> 
                                                                    <a class="website-add-more"><i class="fa fa-plus"></i></a>
                                                            </div>
                                                        </div>
                                                    </div>

                                                <?php }else{?>
                                                    <div class="form-group website-url additional_icon">
                                                        <label class="col-sm-3 control-label"></label>
                                                        <div class="col-sm-7 d-flex">
                                                            <input type="url" class="form-control" value="<?php echo $website_item->website_url;?>" name="website_url[]" required placeholder="Enter Website Url">
                                                            <input type="text" name="website_url_label[]" class="form-control margin-l-5" value="<?php echo $website_item->label;?>" required placeholder="Enter Website Type">
                                                            <div class="input-group-addon"> 
                                                                    <a class="remove"><i class="fas fa-times"></i></a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php 
                                                        }
                                                    }
                                                }else{
                                                ?>
                                                    <div class="form-group website-url additional_icon">
                                                        <label class="col-sm-3 control-label">Website URL</label>
                                                        <div class="col-sm-7 d-flex">
                                                            <input type="url" class="form-control" value="" name="website_url[]" required placeholder="Enter Website Url">
                                                            <input type="text" name="website_url_label[]" class="form-control margin-l-5" value="" required placeholder="Enter Website Type">
                                                            <div class="input-group-addon"> 
                                                                    <a class="website-add-more"><i class="fa fa-plus"></i></a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php }?>
                                                </div>
                                                
                                                <script type="text/javascript">
                                                    $(document).ready(function() {
                                                        var html = `<div class="form-group additional_icon">
                                                        <label class="col-sm-3 control-label"></label>
                                                        <div class="col-sm-7 d-flex">
                                                            <input type="text" class="form-control" value="" name="website_url[]" required placeholder="Enter Website Url">
                                                            <input type="text" name="website_url_label[]" class="form-control margin-l-5" required placeholder="Enter Website Type">
                                                            <div class="input-group-addon"> 
                                                                <a class="remove"><i class="fas fa-times"></i></a>
                                                            </div>
                                                        </div>
                                                    </div>`;
                                                
                                                    //here first get the contents of the div with name class copy-fields and add it to after "after-add-more" div class.
                                                    $(".website-add-more").click(function(){ 
                                                        $(".website-url").after(html);
                                                    });
                                                //here it will remove the current value of the remove button which has been pressed
                                                    $("body").on("click",".remove",function(){ 
                                                        $(this).parents(".form-group").remove();
                                                    });
                                                
                                                    });
                                                </script>


                                        <div class="form-group" id="border-none">
                                            <label for="field-6" class="col-sm-3 control-label">Selected Price Tier</label>
                                            <div class="col-sm-7">
                                                <select name="price_category" class="form-control price-tier" required="">
                                                    <option value="">- Select Price Tier -</option>
                                                        <?php if (!empty($prices)): foreach ($prices as $price): ?>
                                                            <option value="<?= $price->id ?>" <?php if(!empty($resquest_proposal_info)){
                                                            if($resquest_proposal_info['price_category'] == $price->id)
                                                            {
                                                                echo 'selected';
                                                            }
                                                        } ?>><?= $price->price ?>
                                                            </option>
                                                            <?php
                                                        endforeach;
                                                        endif;
                                                        ?>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-group" id="border-none">
                                            <label for="field-1" class="col-sm-3 control-label">Geographical Areas Served</label>
                                            <div class="col-sm-7">
                                                <input type="text" class="form-control geographical-area" placeholder="Geographical Areas Served" name="g_areas_served" value="<?php 
                                                        if(!empty($contact_info)){
                                                            echo $contact_info['g_areas_served'];
                                                        }
                                                        ?>" >
                                            </div>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label class="col-sm-3 control-label">Request proposal</label>
                                            <div class="col-sm-7">
                                            <div class="col-lg-12">
                                                    
                                                    <div class="checkbox c-checkbox">
                                                        <label class="col-lg-5">
                                                        <input type="checkbox" class="proposal-cover-page" name="cover_page" <?php if(!empty($resquest_proposal_info)){
                                                            if($resquest_proposal_info['cover_page'] == 'on')
                                                            {
                                                                echo 'checked';
                                                            }
                                                        } ?>>
                                                        <span class="fa fa-check"></span>
                                                        <small>Cover page</small>
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col-lg-12">
                                                    <div class="checkbox c-checkbox">
                                                        <label class="col-lg-5">
                                                        <input type="checkbox" class="proposal-branding" name="branding" <?php if(!empty($resquest_proposal_info)){
                                                            if($resquest_proposal_info['branding'] == 'on')
                                                            {
                                                                echo 'checked';
                                                            }
                                                        } ?>>
                                                        <span class="fa fa-check"></span>
                                                        <small>Branding</small>
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col-lg-12">
                                                    <div class="checkbox c-checkbox">
                                                        <label class="col-lg-5">
                                                        <input type="checkbox" class="proposal-website-analysis" name="website_analysis" <?php if(!empty($resquest_proposal_info)){
                                                            if($resquest_proposal_info['website_analysis'] == 'on')
                                                            {
                                                                echo 'checked';
                                                            }
                                                        } ?>>
                                                        <span class="fa fa-check"></span>
                                                        <small>Website Analysis</small>
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col-lg-12">
                                                    
                                                    <div class="checkbox c-checkbox">
                                                        <label class="col-lg-5">
                                                        <input type="checkbox" class="proposal-website-proposal" name="website_proposal" <?php if(!empty($resquest_proposal_info)){
                                                            if($resquest_proposal_info['website_proposal'] == 'on')
                                                            {
                                                                echo 'checked';
                                                            }
                                                        } ?>>
                                                        <span class="fa fa-check"></span>
                                                        <small>Website proposal</small>
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col-lg-12">
                                                    
                                                    <div class="checkbox c-checkbox">
                                                        <label class="col-lg-5">
                                                        <input type="checkbox" class="proposal-seo" name="seo" <?php if(!empty($resquest_proposal_info)){
                                                            if($resquest_proposal_info['seo'] == 'on')
                                                            {
                                                                echo 'checked';
                                                            }
                                                        } ?>>
                                                        <span class="fa fa-check"></span>
                                                        <small>Search Engine Optimization</small>
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col-lg-12">
                                                    
                                                    <div class="checkbox c-checkbox">
                                                        <label class="col-lg-5">
                                                        <input type="checkbox" class="proposal-sea" name="sea" <?php if(!empty($resquest_proposal_info)){
                                                            if($resquest_proposal_info['sea'] == 'on')
                                                            {
                                                                echo 'checked';
                                                            }
                                                        } ?>>
                                                        <span class="fa fa-check"></span>
                                                        <small>Search Engine Advertising</small>
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col-lg-12">
                                                    <div class="checkbox c-checkbox">
                                                        <label class="col-lg-5">
                                                        <input type="checkbox" class="proposal-smm" name="smm" <?php if(!empty($resquest_proposal_info)){
                                                            if($resquest_proposal_info['smm'] == 'on')
                                                            {
                                                                echo 'checked';
                                                            }
                                                        } ?>>
                                                        <span class="fa fa-check"></span>
                                                        <small>Social Media Management</small>
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col-lg-12">
                                                    <div class="checkbox c-checkbox">
                                                        <label class="col-lg-5">
                                                        <input type="checkbox" class="proposal-sma" name="sma" <?php if(!empty($resquest_proposal_info)){
                                                            if($resquest_proposal_info['sma'] == 'on')
                                                            {
                                                                echo 'checked';
                                                            }
                                                        } ?>>
                                                        <span class="fa fa-check"></span>
                                                        <small>Social Media Advertising</small>
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col-lg-12">
                                                    <div class="checkbox c-checkbox">
                                                        <label class="col-lg-5">
                                                        <input type="checkbox" class="proposal-content-marketing" name="content_marketing" <?php if(!empty($resquest_proposal_info)){
                                                            if($resquest_proposal_info['content_marketing'] == 'on')
                                                            {
                                                                echo 'checked';
                                                            }
                                                        } ?>>
                                                        <span class="fa fa-check"></span>
                                                        <small>Content Marketing</small>
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col-lg-12">
                                                    <div class="checkbox c-checkbox">
                                                        <label class="col-lg-5">
                                                        <input type="checkbox" class="proposal-marking-analysis" name="marketing_analysis" <?php if(!empty($resquest_proposal_info)){
                                                            if($resquest_proposal_info['marketing_analysis'] == 'on')
                                                            {
                                                                echo 'checked';
                                                            }
                                                        } ?>>
                                                        <span class="fa fa-check"></span>
                                                        <small>Marketing Analysis</small>
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col-lg-12">
                                                    <div class="checkbox c-checkbox">
                                                        <label class="col-lg-5">
                                                        <input type="checkbox" class="proposal-recommendations" name="recommendations" <?php if(!empty($resquest_proposal_info)){
                                                            if($resquest_proposal_info['recommendations'] == 'on')
                                                            {
                                                                echo 'checked';
                                                            }
                                                        } ?>>
                                                        <span class="fa fa-check"></span>
                                                        <small>Recommendations</small>
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col-lg-12">
                                                    <div class="checkbox c-checkbox">
                                                        <label class="col-lg-5">
                                                        <input type="checkbox" class="proposal-why-us" name="why_us_page" <?php if(!empty($resquest_proposal_info)){
                                                            if($resquest_proposal_info['why_us_page'] == 'on')
                                                            {
                                                                echo 'checked';
                                                            }
                                                        } ?>>
                                                        <span class="fa fa-check"></span>
                                                        <small>Why Us Page</small>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <!-- <div id="removed-items"></div> -->
                                <div>
                                    <div class="col-sm-3"></div>
                                    <div class="col-sm-5">
                                        <input type="submit" value="submit" name="Create a Proposal"
                                               class="btn btn-primary btn-block">
                                    </div>
                                </div>
                            </div>

</form>
<?php } else { ?>
    </div>
<?php } ?>
</div>
<script type="text/javascript">
    function select_contact($value)
    {
        // array_push($result,(array)$contact_info[0], (array)$website_info, (array)$resquest_proposal_info[0]);
        // var link = '<?php //echo base_url(); ?>' + 'admin/proposals/save_proposals/';
        // $("form").attr("action", link + $value);
        if($value=='')
        {
            $('#proposal_new select').val('');
            $("#proposal_new input[type=checkbox]").prop('checked', false);
            var replace_template = `<div class="form-group website-url additional_icon">
                                                        <label class="col-sm-3 control-label">Website URL</label>
                                                        <div class="col-sm-7 d-flex">
                                                            <input type="url" class="form-control" value="" name="website_url[]" required="" placeholder="Enter Website Url" data-parsley-id="14">
                                                            <input type="text" name="website_url_label[]" class="form-control margin-l-5" value="" required="" placeholder="Enter Website Type" data-parsley-id="16">
                                                            <div class="input-group-addon"> 
                                                                    <a class="website-add-more"><i class="fa fa-plus"></i></a>
                                                            </div>
                                                        </div>
                                                    </div>`;
            $('#website_url_form').html(replace_template);
            $('#proposal_new .geographical-area').val('');
        }
        else{
            $.ajax({
                url:"<?= base_url()?>admin/proposals/get_contact_info",
                type:"POST",
                data: 'select_contact='+$value,
                dataType: 'json',
                success:function(response){
                    $('.sales-rep').val(response[0]['sales_rep']);
                    $('.white-label-brand').val(response[0]['white_brand']);
                    $('.industry').val(response[0]['industries']);
                    $('.company-name').val(response[0]['company_id']);
                    $('.geographical-area').val(response[0]['g_areas_served']);
                    $('.price-tier').val(response[2]['price_category']);
                    // $('.website-url').val(response[1]['']);
                    $("input[type=checkbox]").prop('checked', false);
                    if( response[2]['cover_page'] == 'on')
                    {
                        $('.proposal-cover-page').trigger('click');
                    }
                    if( response[2]['branding']  == 'on')
                    {
                        $('.proposal-branding').trigger('click');
                    }
                    if( response[2]['website_analysis'] == 'on')
                    {
                        $('.proposal-website-analysis').trigger('click');
                    }
                    if( response[2]['website_proposal'] == 'on')
                    {
                        $('.proposal-website-proposal').trigger('click');
                    }
                    if( response[2]['seo'] == 'on')
                    {
                        $('.proposal-seo').trigger('click');
                    }
                    if( response[2]['sea'] == 'on')
                    {
                        $('.proposal-sea').trigger('click');
                    }
                    if( response[2]['smm'] == 'on')
                    {
                        $('.proposal-smm').trigger('click');
                    }
                    if( response[2]['sma'] == 'on')
                    {
                        $('.proposal-sma').trigger('click');
                    }
                    if( response[2]['content_marketing'] == 'on')
                    {
                        $('.proposal-content-marketing').trigger('click');
                    }
                    if(response[2]['marketing_analysis'] == 'on')
                    {
                        $('.proposal-marking-analysis').trigger('click');
                    }

                    if(response[2]['recommendations'] == 'on')
                    {
                        $('.proposal-recommendations').trigger('click');
                    }
                    if(response[2]['why_us_page'] == 'on')
                    {
                        $('.proposal-why-us').trigger('click');
                    }
                    var website_info = response[1];
                    // $('.price-tier').val(response.);
                    var total = '';
                    for(var k in website_info) {

                        var string1 = `<div class="form-group additional_icon`;
                        var _string1 = '';
                        var _string2 = '';
                        var _string3 = '<a class="remove"><i class="fas fa-times"></i></a>';
                        if(k==0){
                            _string1 = 'website-url';
                            _string2 = 'Website URL';
                            _string3 = '<a class="website-add-more"><i class="fa fa-plus"></i></a>';
                        }
                        var string2 = _string1+`">`;
                        var string3 =`<label for="field-5" class="col-sm-3 control-label">` + _string2 + `</label>`;
                        var string4 = `<div class="col-sm-7 d-flex">
                                    <input type="url" class="form-control" value="`+ website_info[k]['website_url'] + `" name="website_url[]" required placeholder="Enter Website Url">
                                    <input type="text" name="website_url_label[]" class="form-control margin-l-5" value="` + website_info[k]['label']+`" required placeholder="Enter Website Type">
                                    <div class="input-group-addon">` + _string3 + `</div></div></div>`;
                        var c_str = string1 + string2 + string3 + string4;
                        total = total + c_str;
                    }
                    $('#website_url_form').html(total);
                    console.log(total);
                    // $('.geographical-area').val(response.latitude);
                    
                }
            });
        }
        // 
    }
    function slideToggle($id) {
        $('#quick_state').attr('data-original-title', '<?= lang('view_quick_state') ?>');
        $($id).slideToggle("slow");
    }
    $(document).ready(function () {
        init_items_sortable();
    });
</script>
