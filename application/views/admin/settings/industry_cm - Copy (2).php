<?= message_box('success'); ?>
<?= message_box('error');
$created = can_action('158', 'created');
$edited = can_action('158', 'edited');
$deleted = can_action('158', 'deleted');
// var_dump($created);
if (!empty($created) || !empty($edited)){
    ?>
    <div class="nav-tabs-custom">
        <!-- Tabs within a box -->
        <ul class="nav nav-tabs">
            <li class="<?= $active == 1 ? 'active' : ''; ?>">
                <a href="<?php echo base_url();?>admin/settings/industry_cm">
                   Industry CM</a></li>
            <li class="<?= $active == 2 ? 'active' : ''; ?>">
                <a href="<?php echo base_url();?>admin/settings/industry_roi">Industry ROI</a>
            </li>
            <li class="<?= $active == 3 ? 'active' : ''; ?>">
                <a href="<?php echo base_url();?>admin/settings/industry_seo">Industry SEO</a>
            </li>
        </ul>
        <div class="tab-content bg-white">
            <!-- ************** general *************-->
            <?php if ($active == 1){?>
                <div class="tab-pane <?= $active == 1 ? 'active' : ''; ?>" id="industry_cm">
            <?php } ?>
            <?php if ($active == 2){?>
                <div class="tab-pane <?= $active == 2 ? 'active' : ''; ?>" id="industry_roi">
            <?php } ?>
            <?php if ($active == 3){?>
                <div class="tab-pane <?= $active == 3 ? 'active' : ''; ?>" id="industry_cm">
            <?php } ?>
                
                <?php } else { ?>
                    <div class="panel panel-custom">
                        <header class="panel-heading ">
                            <div class="panel-title"><strong><?= lang('all_leads') ?></strong></div>
                        </header>
                <?php }?>
    <?php if($active == 1){ ?>
                <div class="table-responsive">
                <?php if($action == 'view_list'){ ?>
                    <table class="table table-striped DataTables " id="DataTables" cellspacing="0" width="100%">
                        <a href="<?= base_url() ?>admin/settings/industry_cm/new_industry" class="btn btn-success float-right">New industry</a>
                    <thead>
                            <tr>
                                <th>No</th>
                                <th>Industry</th>
                                <th>
                                    Action
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                                if (!empty($comments)) {
                                    $i = 0;
                                    $k = 1;
                                    foreach ($comments as $comment_item) {
                                        ?>
                                         <?php 
                                            if($i != 0 && $i != 1 )
                                            {
                                                
                                                ?>
                                        <tr>
                                       
                                            <td>
                                                <?php 
                                                echo $k;
                                                $k++;
                                                ?>    
                                            </td>
                                            <td>
                                                   <?php
                                                            echo $comment_item['Field'];
                                                        
                                                    ?>
                                            </td>
                                            <td>
                                                <?php echo btn_edit('admin/settings/industry_cm/edit/' . $comment_item['Field']); ?>
                                                <?php echo btn_view('admin/settings/industry_cm/view/' . $comment_item['Field']); ?>
                                                <?php echo btn_delete('admin/settings/industry_cm/delete/' . $comment_item['Field']); ?>
                                            </td>
                                           
                                        </tr>
                                        <?php }
                                            $i++;
                                        ?>
                                        <?php
                                        }
                                    } 
                                    else
                                    {
                                    ?>
                                    <tr>
                                        <td colspan="9">
                                            <?php lang('no_data') ?>
                                        </td>
                                    </tr>
                                <?php } ?>
                        </tbody>
                    </table>
                <?php }?>
                </div>
            <?php 
     
            if ( $action =='new_edit'){ ?>
                        <div class="tab-pane">
                            <form role="form" data-parsley-validate="" novalidate="" id="userform"
                                enctype="multipart/form-data"
                                action="<?php echo base_url(); ?>admin/settings/save_industrycm/<?php echo $uri?>" method="post"
                                class="form-horizontal form-groups-bordered">
                                <input type="hidden" name="new_data" value="new_industrycm"/>
                                <?php if (empty($pricing_info)){ ?>
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">New industry name<span class="text-danger">*</span></label>
                                        <div class="col-sm-4">
                                            <input type="text" name="new_price" class="form-control" required pattern="[A-Za-z]"/>
                                        </div>

                                    </div>
                                <!-- <div class="form-group">
                                    <label for="field-1" class="col-sm-3 control-label">Industry Description<span class="text-danger">*</span></label>
                                    <div class="col-sm-6">
                                    <textarea class="form-control textarea" name="pricing_description" ></textarea>
                                    </div>
                                </div> -->
                                <?php }?>
                                <div class="industry_cm">
                                    <div class="form-group">
                                        <div class="col-sm-3 control-label">
                                            <h5 class="custom-color">keyword</h5>
                                        </div>
                                        <div class="col-sm-4 control-label text-center">
                                            <h5 class="custom-color">Value</h5>
                                        </div>
                                    </div>
                                   <?php
                                        $i = 0;
                                        foreach ($tiers as $key=>$tiers_item){
                                    ?>
                                        <div class="form-group">
                                            <div class="col-sm-3 control-label">
                                                <?php echo $tiers_item['industry']; ?>
                                            </div>
                                            <div class="col-sm-4 control-label">
                                                <input type='text' name = "price[<?php echo $tiers_item['id'];?>]"  class="form-control" value="<?php if (!empty($pricing_info)){
                                                        echo $pricing_info[$i][$edit_id];
                                                        $i++;
                                                    }
                                                ?>"/>
                                            </div>
                                        </div>
                                    <?php 
                                    }
                                    ?>
                                </div>

                            <div class="form-group">
                                <label for="field-1" class="col-sm-3 control-label"></label>
                                <div class="col-sm-8">
                                    <a href="<?= base_url() ?>admin/settings/industry_cm" class='btn btn-primary'>Back</a> 
                                    <input type="button" class="btn btn-primary add-new-keyword" onClick="" value="Add new keyword">
                                    <button type="submit" id="sbtn" class="btn btn-primary" onClick=""><?= lang('save')?></button>
                                </div>
                            </div>

                        </form>
                    </div>
                <?php 
             }
            ?>
         <?php 
        if ( $action =='view'){ ?>
            <div class="row mt-lg">
    
                <div class="col-sm-9">

                    <div class="tab-content" style="border: 0;padding:0;">
                        <!-- Task Details tab Starts -->
                        <div class="tab-pane active" id="task_details" style="position: relative;">
                            <div class="panel panel-custom">
                                <div class="panel-heading">
                                    <h3 class="panel-title custom-color"><?php echo $uri; ?>                        
                                    <div class="pull-right ml-sm " style="margin-top: -6px">
                                            <a data-toggle="tooltip" data-placement="top" title="" href="#" class="btn-xs btn btn-warning" data-original-title="Added into Pinned"><i class="fa fa-thumb-tack"></i></a>
                                        </div>
                                        <span class="btn-xs pull-right">
                                                <a href="<?= base_url() ?>admin/settings/industry_cm/edit/<?php echo $uri;?>">Edit industry</a>
                                    </span>
                                    </h3>
                                </div>
                                <div class="panel-body row form-horizontal task_details">
                                <div class="form-group col-sm-12">
                                    <div class="col-sm-5">
                                        <h5 class="custom-color float-right">Keyword</h5>
                                    </div>
                                    <div class="col-sm-1">
                                        <h5 class="custom-color float-right">Value</h5>
                                    </div>
                                </div>
                                <?php
                                        $i = 0;
                                        foreach ($tiers as $key=>$tiers_item){
                                            if(!empty($pricing_info[$i][$edit_id])){
                                    ?>
                                    
                                    <div class="form-group col-sm-12">
                                            <div class="control-label col-sm-5"><strong><?php echo $tiers_item['industry']; ?> &nbsp;&nbsp;:&nbsp;&nbsp&nbsp  </strong>
                                            </div>
                                            <div class="col-sm-1">
                                                <p class="form-control-static float-right"><?php echo $pricing_info[$i][$edit_id]; $i++;?></p>
                                            </div>

                                    </div>
                                        <?php 
                                            }
                                    }?>
                                </div>

                            </div>
                        </div>
                        
                        
                    </div>
                </div>
            </div>
        
        
        
        
        <?php 
            }
        }
        ?>
    <?php if($active == 2){ ?>
                <div class="table-responsive">
                <?php if($action == 'view_list'){ ?>
                    <table class="table table-striped DataTables " id="DataTables" cellspacing="0" width="100%">
                        <a href="<?= base_url() ?>admin/settings/industry_roi/new_industry" class="btn btn-success float-right">New industry</a>
                    <thead>
                            <tr>
                                <th>No</th>
                                <th>Industry</th>
                                <th>
                                    Action
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                                if (!empty($comments)) {
                                    $i = 0;
                                    $k = 1;
                                    foreach ($comments as $comment_item) {
                                        ?>
                                         <?php 
                                            if($i != 0 && $i != 1 )
                                            {
                                                
                                                ?>
                                        <tr>
                                            <td>
                                                <?php 
                                                echo $k;
                                                $k++;
                                                ?>    
                                            </td>
                                            <td>
                                                   <?php
                                                            echo $comment_item['Field'];
                                                    ?>
                                            </td>
                                            <td>
                                                <?php echo btn_edit('admin/settings/industry_roi/edit/' . $comment_item['Field']); ?>
                                                <?php echo btn_view('admin/settings/industry_roi/view/' . $comment_item['Field']); ?>
                                                <?php echo btn_delete('admin/settings/industry_roi/delete/' . $comment_item['Field']); ?>
                                            </td>
                                           
                                        </tr>
                                        <?php }
                                            $i++;
                                        ?>
                                        <?php
                                        }
                                    } 
                                    else
                                    {
                                    ?>
                                    <tr>
                                        <td colspan="9">
                                            <?php lang('no_data') ?>
                                        </td>
                                    </tr>
                                <?php } ?>
                        </tbody>
                    </table>
                <?php }?>
                </div>
            
            <?php 
     
            if ( $action =='new_edit'){ ?>
                        <div class="tab-pane">
                            <form role="form" data-parsley-validate="" novalidate="" id="userform"
                                enctype="multipart/form-data"
                                action="<?php echo base_url(); ?>admin/settings/save_industryroi/<?php echo $uri?>" method="post"
                                class="form-horizontal form-groups-bordered">
                                <input type="hidden" name="new_data" value="new_industrycm"/>
                                <?php if (empty($pricing_info)){ ?>
                                <div class="form-group">
                                    <label class="col-sm-3 control-label">New industry name<span class="text-danger">*</span></label>
                                    <div class="col-sm-4">
                                        <input type="text" name="new_price" class="form-control" required pattern="[A-Za-z]"/>
                                    </div>

                                </div>
                                <!-- <div class="form-group">
                                    <label for="field-1" class="col-sm-3 control-label">Industry Description<span class="text-danger">*</span></label>
                                    <div class="col-sm-6">
                                    <textarea class="form-control textarea" name="pricing_description" ></textarea>
                                    </div>
                                </div> -->
                                <?php }?>

                                <div class="industry_cm">
                                <div class="form-group">
                                        <div class="col-sm-3 control-label">
                                            <h5 class="custom-color">keyword</h5>
                                        </div>
                                        <div class="col-sm-4 control-label text-center">
                                            <h5 class="custom-color">Value</h5>
                                        </div>
                                </div>
                                   <?php
                                        $i = 0;
                                        foreach ($tiers as $key=>$tiers_item){
                                    ?>
                                        <div class="form-group">
                                            <div class="col-sm-3 control-label">
                                                <?php echo $tiers_item['industry']; ?>
                                            </div>
                                            <div class="col-sm-4 control-label">
                                                <input type='text' name = "price[<?php echo $tiers_item['id'];?>]" required class="form-control" value="<?php 
                                                    if (!empty($pricing_info)){
                                                        echo $pricing_info[$i][$edit_id];
                                                        $i++;
                                                    }
                                                ?>"/>
                                            </div>
                                        </div>
                                    <?php 
                                    }
                                    ?>
                                </div>

                            <div class="form-group">
                                <label for="field-1" class="col-sm-3 control-label"></label>
                                <div class="col-sm-8">
                                    <a href="<?= base_url() ?>admin/settings/industry_roi" class='btn btn-primary'>Back</a> 
                                    <button type="submit" id="sbtn" class="btn btn-primary" onClick=""><?= lang('save')?></button>
                                </div>
                            </div>

                        </form>
                    </div>
                <?php 
             }
            ?>
         <?php 
        if ( $action =='view'){ ?>
            <div class="row mt-lg">
                <div class="col-sm-9">
                    <div class="tab-content" style="border: 0;padding:0;">
                        <!-- Task Details tab Starts -->
                        <div class="tab-pane active" id="task_details" style="position: relative;">
                            <div class="panel panel-custom">
                                <div class="panel-heading">
                                    <h3 class="panel-title custom-color"><?php echo $uri; ?>                        
                                    <div class="pull-right ml-sm " style="margin-top: -6px">
                                            <a data-toggle="tooltip" data-placement="top" title="" href="#" class="btn-xs btn btn-warning" data-original-title="Added into Pinned"><i class="fa fa-thumb-tack"></i></a>
                                        </div>
                                        <span class="btn-xs pull-right">
                                                <a href="<?= base_url() ?>admin/settings/industry_roi/edit/<?php echo $uri;?>">Edit industry</a>
                                    </span>
                                    </h3>
                                </div>
                                <div class="panel-body row form-horizontal task_details">
                                    <div class="form-group">
                                        <div class="col-sm-4">
                                            <h5 class="custom-color float-right">Keyword</h5>
                                        </div>
                                        <div class="col-sm-2 text-center text-center">
                                            <h5 class="custom-color">Value</h5>
                                        </div>
                                    </div>
                                <?php
                                        $i = 0;
                                        $total = 0;
                                        foreach ($tiers as $key=>$tiers_item){
                                    ?>
                                    
                                    <div class="form-group">
                                            <div class="control-label col-sm-4"><strong class=" float-right"><?php echo $tiers_item['industry']; ?> :</strong>
                                        </div>
                                            <div class="col-sm-2 text-center">
                                            <p class="form-control-static"><?php echo $pricing_info[$i][$edit_id]; ?></p>
                                            </div>

                                    </div>
                                    <?php 
                                        if( $i !=1 && $i !=2 && $i !=0 ){
                                            $total = $total + $pricing_info[$i][$edit_id];
                                        }
                                        $i++;
                                        if($tiers_item == end($tiers)){
                                    ?>
                                        <div class="form-group">
                                            <div class="control-label col-sm-4">
                                                <strong class=" float-right">Total Vol (Auto Calc) :</strong>
                                            </div>
                                            <div class="control-label col-sm-2 text-center">
                                            <strong><?php echo $total;?></strong>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="control-label col-sm-4">
                                                <strong class=" float-right">Market Share Low (Auto-Calc) :</strong>
                                            </div>
                                            <div class="control-label col-sm-2 text-center">
                                            <strong>$ <?php 
                                                    $low = $pricing_info[2][$edit_id] * $total * $pricing_info[1][$edit_id];
                                                
                                                echo $low;?></strong>
                                                </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="control-label col-sm-4">
                                                <strong class=" float-right" >Market Share High (Auto-Calc) :</strong>
                                            </div>
                                            <div class="control-label col-sm-2 text-center">
                                            <strong>
                                                $ <?php 
                                                    $high = $pricing_info[3][$edit_id] * $total * $pricing_info[0][$edit_id];
                                                echo $high;?></strong>
                                            </div>
                                        </div>
                                    <?php
                                        }
                                    ?>
                                        <?php }?>
                                </div>

                            </div>
                        </div>
                        
                        
                    </div>
                </div>
            </div>
        
        <?php 
            }
        }
        ?>
    <?php if($active == 3){ ?>
        <div class="table-responsive">
            <?php if($action == 'view_list'){ ?>
                <table class="table table-striped DataTables " id="DataTables" cellspacing="0" width="100%">
                    <a href="<?= base_url() ?>admin/settings/industry_seo/new_industry" class="btn btn-success float-right">New industry</a>
                <thead>
                <tr>
                    <th>No</th>
                    <th>Industry</th>
                    <th>
                        Action
                    </th>
                </tr>
            </thead>
            <tbody>
            <?php
            if (!empty($categories)) {
                $i = 0;
                $k = 1;
                foreach ($categories as $category) {
                    ?>
                   
                    <tr>
                        <td>
                            <?php 
                            echo $k;
                            $k++;
                            ?>    
                        </td>
                        <td>
                                <?php
                                        echo $category['category'];
                                ?>
                        </td>
                        <td>
                            <?php echo btn_edit('admin/settings/industry_seo/edit/' . $category['category']); ?>
                            <?php echo btn_view('admin/settings/industry_seo/view/' . $category['category']); ?>
                            <?php echo btn_delete('admin/settings/industry_seo/delete/' . $category['category']); ?>
                        </td>
                    </tr>
                    <?php
                    ?>
                    <?php
                    }
                } 
                else
                {
                ?>
                <tr>
                    <td colspan="9">
                        <?php lang('no_data') ?>
                    </td>
                </tr>
                    <?php } ?>
                    </tbody>
                </table>
            <?php }?>
            </div>
            
            <?php 
     
            if ( $action =='new_edit'){ ?>
                        <div class="tab-pane">
                            <form role="form" data-parsley-validate="" novalidate="" id="userform"
                                enctype="multipart/form-data"
                                action="<?php echo base_url(); ?>admin/settings/save_industryseo/<?php echo $uri?>" method="post"
                                class="form-horizontal form-groups-bordered">
                                <input type="hidden" name="new_data" value="new_industrycm"/>
                                <?php if (empty($pricing_info)){ ?>
                                <div class="form-group">
                                    <label class="col-sm-3 control-label">New industry name<span class="text-danger">*</span></label>
                                    <div class="col-sm-4">
                                        <div class="col-sm-12">
                                            <input type="text" name="new_price" class="form-control" required pattern="[A-Za-z]"/>
                                        </div>
                                    </div>

                                </div>
                                <!-- <div class="form-group">
                                    <label for="field-1" class="col-sm-3 control-label">Industry Description<span class="text-danger">*</span></label>
                                    <div class="col-sm-6">
                                    <textarea class="form-control textarea" name="pricing_description" ></textarea>
                                    </div>
                                </div> -->
                                <?php }?>
                                <div class="industry_cm">
                                <div class="form-group">
                                    <div class="col-sm-3 control-label"></div>
                                    <div class="col-sm-6 control-label">
                                        <div class="col-sm-4 text-center"><strong class="custom-color">Keyword</strong></div>
                                        <div class="col-sm-4 text-center"><strong class="custom-color">Value</strong></div>
                                        <div class="col-sm-4 text-center"><strong class="custom-color">Competition</strong></div>
                                    </div>
                                </div>
                                   <?php
                                   $i = 0;
                                        foreach ($tiers as $key=>$tiers_item){
                                        
                                    ?>
                                        <div class="form-group">
                                            <div class="col-sm-3 control-label">
                                                <?php echo $tiers_item['industry']; ?>
                                            </div>
                                            <div class="col-sm-6 control-label">

                                                <div class="col-sm-4">
                                                <input type='text' name = "keyword[<?php echo $tiers_item['id'];?>]"  class="form-control" value="<?php 
                                                    if(!empty($pricing_info))
                                                    {
                                                        echo $pricing_info[$key][$uri.'_keyword'];
                                                    } 
                                                
                                                ?>"/>
                                                </div>
                                                
                                                <div class="col-sm-4">
                                                <input type='text' name = "value[<?php echo $tiers_item['id'];?>]" class="form-control" value="<?php 
                                                    if(!empty($pricing_info))
                                                    {
                                                        echo $pricing_info[$key][$uri.'_value'];
                                                    } 
                                                
                                                ?>"/>
                                                </div>
                                                
                                                <div class="col-sm-4">
                                                <input type='text' name = "competition[<?php echo $tiers_item['id'];?>]"  class="form-control" value="<?php 
                                                    if(!empty($pricing_info))
                                                    {
                                                        echo $pricing_info[$key][$uri.'_competition'];
                                                    } 
                                                
                                                ?>"/>
                                                </div>
                                            </div>
                                        </div>
                                    <?php 
                                    }
                                    ?>
                                </div>

                            <div class="form-group">
                                <label for="field-1" class="col-sm-3 control-label"></label>
                                <div class="col-sm-8">
                                    <a href="<?= base_url() ?>admin/settings/industry_seo" class='btn btn-primary'>Back</a> 
                                    <input type="button" class="btn btn-primary add-new-seokeyword" onClick="" value="Add new keyword">
                                    <button type="submit" id="sbtn" class="btn btn-primary" onClick=""><?= lang('save')?></button>
                                </div>
                            </div>

                        </form>
                    </div>
                <?php 
             }
            ?>
         <?php 
        if ( $action =='view'){ ?>
            <div class="row mt-lg">
    
                <div class="col-sm-9">

                    <div class="tab-content" style="border: 0;padding:0;">
                        <!-- Task Details tab Starts -->
                                
                        <div class="tab-pane active" id="task_details" style="position: relative;">
                            <div class="panel panel-custom">
                                <div class="panel-heading">
                                    <h3 class="panel-title custom-color"><?php echo $uri; ?>                        
                                    <div class="pull-right ml-sm " style="margin-top: -6px">
                                            <a data-toggle="tooltip" data-placement="top" title="" href="#" class="btn-xs btn btn-warning" data-original-title="Added into Pinned"><i class="fa fa-thumb-tack"></i></a>
                                        </div>
                                        <span class="btn-xs pull-right">
                                                <a href="<?= base_url() ?>admin/settings/industry_seo/edit/<?php echo $uri;?>">Edit industry</a>
                                    </span>
                                    </h3>
                                </div>
                                <div class="panel-body row form-horizontal task_details">
                                    <div class="form-group col-sm-12">
                                            <div class="col-sm-3 text-right">
                                                <h5 class="custom-color">industry</h5>
                                            </div>
                                            <div class="col-sm-3 text-center">
                                            <h5 class="custom-color">Keyword</h5>
                                            </div>
                                            <div class="col-sm-3 text-center">
                                            <h5 class="custom-color">Value</h5>
                                            </div>
                                            <div class="col-sm-3 text-center">
                                            <h5 class="custom-color">Competition</h5>
                                            </div>
                                            

                                    </div>
                                <?php
                                        foreach ($tiers as $key=>$tiers_item){
                                    ?>
                                    
                                    <div class="form-group col-sm-12">
                                            <div class="col-sm-3 text-right">
                                                <strong class="form-control-static"><?php echo $tiers_item['industry']; ?> :</strong>
                                            </div>
                                            <div class="col-sm-3 text-center">
                                                <p class="form-control-static"><?php if(!empty($pricing_info[$key][$uri.'_keyword'])){echo $pricing_info[$key][$uri.'_keyword'];}else{echo "No value";}?></p>
                                            </div>
                                            <div class="col-sm-3 text-center">
                                                <p class="form-control-static"><?php if(!empty($pricing_info[$key][$uri.'_value'])){echo $pricing_info[$key][$uri.'_value'];}else{echo "No value";}?></p>
                                            </div>
                                            <div class="col-sm-3 text-center">
                                                <p class="form-control-static"><?php if(!empty($pricing_info[$key][$uri.'_competition'])){echo $pricing_info[$key][$uri.'_competition'];}else{echo "No value";}?></p>
                                            </div>
                                            

                                    </div>
                                        <?php }?>
                                </div>

                            </div>
                        </div>
                        
                        
                    </div>
                </div>
            </div>
        
        
        
        
        <?php 
            }
        }
        ?>
    </div>

<script>

$(document).ready(function(){
    var i = 0;
    $('.add-new-keyword').click(function(){
        var template = `<div class="form-group added_key">
            <div class="col-sm-3 control-label">
                <div class="col-sm-6 float-right">
                <input type="text" placeholder="New field name" name="new_keyword[`+i+`]" required class="form-control new-keyword">
                </div>
            </div>
            <div class="col-sm-4 control-label display-flex">
                <input type='text' placeholder="value" name = "new_keyword_value[`+i+`]" required class="form-control new-keyword-value"/>
                <i class="fas fa-times additional_times float-right" style="margin-left:5px"></i>
            </div>
        </div>`;
        $('form').has($(this)).find('.industry_cm').append(template);
        i++;
      
    });
    var j = 0;
    $('.add-new-seokeyword').click(function(){
        var template = `<div class="form-group added_key">
                <div class="col-sm-3 control-label">
                    <div class="col-sm-6 float-right">
                        <input type="text" placeholder="New field name" name="new_keyword[`+j+`]" required class="form-control new-keyword">
                    </div>
                </div>
                <div class="col-sm-6 control-label">
                    <div class="col-sm-4 text-center">
                        <input type='text' placeholder="new keyword" name = "new_keyword_item[`+j+`]" required class="form-control"/>
                    </div>

                    <div class="col-sm-4 text-center">
                        <input type='text' placeholder="new value" name = "new_value[`+j+`]" required class="form-control"/>
                    </div>
                    <div class="col-sm-4 text-center display-flex">
                        <input type='text' placeholder="new competition" name = "new_competition[`+j+`]" required class="form-control"/>
                        <i class="fas fa-times additional_times float-right" style="margin-left:5px"></i>
                    </div>
                </div>
        </div>`;
        $('form').has($(this)).find('.industry_cm').append(template);
        j++;
      
    });
    $(document).on('click','.additional_times', function(){
        $('.added_key').has($(this)).remove();
    });
});
</script>