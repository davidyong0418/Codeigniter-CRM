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
                <a href="#industry_cm" data-toggle="tab">
                    <?= lang('industry_views') ?></a></li>
            <li class="<?= $active == 2 ? 'active' : ''; ?>">
                <a href="#industry_seo" data-toggle="tab"><?= lang('new_industry') ?></a>
            </li>
            <li class="<?= $active == 3 ? 'active' : ''; ?>">
                <a href="#industry_roi" data-toggle="tab"><?= lang('new_industry_keyword') ?></a>
            </li>
        </ul>
        <div class="tab-content bg-white">
            <!-- ************** general *************-->
            <div class="tab-pane <?= $active == 1 ? 'active' : ''; ?>" id="industry_cm">
                <?php } else { ?>
                    <div class="panel panel-custom">
                        <header class="panel-heading ">
                            <div class="panel-title"><strong><?= lang('all_leads') ?></strong></div>
                        </header>
                <?php }?>




                
                <div class="table-responsive">
                    <table class="table table-striped DataTables " id="DataTables" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <?php 
                                    foreach ($comments as $key=>$comment)
                                    {
                                ?>
                                <th>
                                    <?php echo $comment['Comment']; ?>
                                </th>
                                <?php 
                                    }
                                ?>
                                <th>
                                    Action
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                                if (!empty($pricing_data)) {
                                    foreach ($pricing_data as $pricing_item) {
                                        ?>
                                        <tr>
                                            <?php
                                                foreach( $pricing_item as $value)
                                                {
                                            ?>
                                            <td>
                                               
                                                    <?php echo $value;?>
                                            
                                            </td>
                                            <?php
                                                }
                                            ?>
                                            <td>
                                                <?php echo btn_edit('admin/settings/industry_cm/' . $pricing_item->id); ?>
                                            </td>
                                        </tr>
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
                </div>
            
            <div class="tab-pane <?= $active == 2 && empty($new) ? 'active' : ''; ?>" id="industry">
                <form role="form" data-parsley-validate="" novalidate="" id="userform"
                      enctype="multipart/form-data"
                      action="<?php echo base_url(); ?>admin/settings/save_industrycm/<?php echo $edit_id;?>" method="post"
                      class="form-horizontal form-groups-bordered">
                    <div class="form-group overflow-part">
                        
                    <table class="table table-striped DataTables " cellspacing="0" width="100%">
                                    <thead>
                                    <tr>
                                        <?php
                                            $i = 0;
                                            foreach ($comments as $comment)
                                            {
                                                if( $i != 0){
                                                ?>
                                                <th>
                                                    <?php echo $comment['Comment']; ?>
                                                </th>
                                                <?php
                                                }
                                                else{
                                                    $i++;
                                                }
                                            }
                                        ?>
                                    </tr>
                                    </thead>
                                    <tbody class="row">
                                    <?php
                                    if (!empty($pricing_info)) {
                                        ?>
                                        <tr>
                                            <!-- <td><span class="label label-default">
                                           dd
                                        </span></td> -->
                                            <?php
                                            $count = count(json_encode($pricing_info));
                                            $count = 0;
                                            foreach ($pricing_info as $key=>$pricing_item) {
                                                $count++;
                                            }
                                            $i = 0;
                                            foreach ($pricing_info as $key=>$pricing_item) {
                                                if( $i!= 0){
                                                ?>
                                                <td style="width:<?php echo 100/($count-1);?>%">
                                                    <input type="text" value="<?php echo $pricing_item;?>" name="<?php echo $key;?>" class="form-control" required/>
                                                </td>
                                                <?php
                                                }
                                                else{
                                                    $i++;
                                                }
                                            }
                                                    
                                            ?>
                                        </tr>
                                        <?php
                                    }
                                    ?>
                                    </tbody>
                                </table>
                                
                    </div>
                    <div class="form-group mt">
                                        <label class="col-lg-3"></label>
                                        <div class="col-lg-1">
                                            <button type="submit"
                                                    class="btn btn-sm btn-primary"><?= lang('save') ?></button>
                                        </div>
                                       

                                    </div>
                </form>
            </div>
                        <div class="tab-pane <?= $active == 2 && !empty($new)? 'active' : ''; ?>" id="new">
                            <form role="form" data-parsley-validate="" novalidate="" id="userform"
                                enctype="multipart/form-data"
                                action="<?php echo base_url(); ?>admin/settings/save_industrycm/new_industry" method="post"
                                class="form-horizontal form-groups-bordered">
                                <input type="hidden" name="new_data" value="new_industrycm"/>
                                <div class="form-group">
                            
                                <table class="table table-striped DataTables " cellspacing="0" width="100%">
                                    <thead>
                                    <tr> 
                                            <th>
                                                <label>Price Tiers</label>
                                            </th>
                                            <th>
                                                <label>Add New Tier<span class="text-danger">*</span></label>
                                            </th>
                                    </tr>
                                    </thead>
                                    <tbody>

                                    <?php
                                            foreach ($tiers as $key=>$tiers_item) {
                                    ?>
                                        <tr>
                                            <!-- <td><span class="label label-default">
                                           dd
                                        </span></td> -->
                                                <td>
                                                    <?php echo $tiers_item['industry']; ?>
                                                </td>
                                                <td class="form-group">
                                                    <input type='text' name = "price[<?php echo $tiers_item['id'];?>]" required class="form-control"/>
                                                </td>
                                        </tr>
                                        <?php
                                            }
                                            ?>
                                    </tbody>
                                </table>
                                        <div class="form-group">
                                            <label class="col-sm-3 control-label">Please insert new industry name<span class="text-danger">*</span></label>
                                            <div class="col-sm-6">
                                                <input type="text" name="new_price" class="form-control" required pattern="[A-Za-z]"/>
                                            </div>

                                        </div>
                                        <div class="form-group">
                                            <label for="field-1" class="col-sm-3 control-label">Industry Description<span class="text-danger">*</span></label>
                                            <div class="col-sm-6">
                                            <textarea class="form-control textarea" name="pricing_description" ></textarea>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="field-1" class="col-sm-3 control-label"></label>
                                            <div class="col-sm-8">
                                                <button type="submit" id="sbtn" class="btn btn-primary" onClick=""><?= lang('save')?></button>
                                            </div>
                                        </div>



                    </div>
                </form>
            </div>
            

                <div class="tab-pane <?= $active == 3 ? 'active' : ''; ?>" id="industry_roi">
                <form role="form" data-parsley-validate="" novalidate="" id="userform"
                      enctype="multipart/form-data"
                      action="<?php echo base_url(); ?>admin/settings/save_industrycm/new_keyword" method="post"
                      class="form-horizontal form-groups-bordered">
                    <div class="form-group">
                    <input type="hidden" name="new_data" value="new_keyword"/>
                    <table class="table table-striped DataTables " cellspacing="0" width="100%">
                                    <thead>
                                    <tr>
                                    
                                        <?php
                                            $i = 0;
                                            $k = 0;
                                            foreach ($comments as $comment)
                                            {
                                               
                                                if( $i != 0){
                                                    $k++;
                                                ?>
                                                <th>
                                                    <?php 
                                                        if( $comment['Comment']== 'Industry'){
                                                            echo 'New keyword';
                                                        }
                                                        else{
                                                            echo $comment['Comment']; 
                                                        }
                                                    ?>
                                                </th>
                                                <?php
                                                }
                                                else{
                                                    $i++;
                                                }
                                            }
                                            
                                        ?>
                                    </tr>
                                    </thead>
                                    <tbody class="row">
                                        <tr>
                                        
                                            <?php
                                            $i = 0;
                                            foreach ($comments as $comment)
                                            {
                                               
                                                if( $i != 0){
                                                ?>
                                                <td style="width:<?php echo 100/($k+1);?>%">
                                                    <input type="text" value="" name="keyword_field[<?php echo $comment['Field'];?>]" class="form-control" required/>
                                                </td>
                                                <?php
                                                }
                                                else{
                                                    $i++;
                                                }
                                            }
                                            ?>
                                        </tr>
                                       
                                    </tbody>
                                </table>
                                <div class="form-group mt">
                                        <label class="col-lg-3"></label>
                                        <div class="col-lg-1">
                                            <button type="submit"
                                                    class="btn btn-sm btn-primary"><?= lang('save') ?></button>
                                        </div>
                                       

                                    </div>
                    </div>
                </form>
            </div>
    
        
    </div>

