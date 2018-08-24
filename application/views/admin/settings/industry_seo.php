<?= message_box('success'); ?>
<?= message_box('error');
$created = can_action('24', 'created');
$edited = can_action('24', 'edited');
$deleted = can_action('24', 'deleted');
// var_dump($created);
if (!empty($created) || !empty($edited)){
    ?>
    <div class="nav-tabs-custom">
        <!-- Tabs within a box -->
        <ul class="nav nav-tabs">
            <li class="<?= $active == 1 ? 'active' : ''; ?>">
                <a href="<?php echo base_url(); ?>admin/settings/industry_seo">
                    <?= lang('pricing_views') ?></a></li>
            <li class="<?= $active == 2 ? 'active' : ''; ?>"><a href="<?php echo base_url(); ?>admin/settings/industry_seo/new_industry"
                                                                ><?= lang('new_industry') ?></a>
            </li>
            <li class="<?= $active == 3 ? 'active' : ''; ?>"><a href="<?php echo base_url(); ?>admin/settings/industry_seo/new_keyword"
                                                                ><?= lang('new_industry_keyword') ?></a>
            </li>
        </ul>
        <div class="tab-content bg-white">
            <!-- ************** general *************-->
            <div class="tab-pane <?= $active == 1 ? 'active' : ''; ?>" id="industry-seo">
                <?php }?>
                    <table class="table table-striped DataTables " id="DataTables" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th rowspan="2">
                                Industry
                                </th>
                                <?php 
                                $i = 0;
                                    foreach ($categories as $key=>$category)
                                    {
                                        $i++;
                                ?>
                                <th colspan="3">
                                    <?php echo $category['category']; ?>
                                </th>
                                <?php 
                                    }
                                ?>
                                <th>
                                Action
                                </th>
                            </tr>
                            <tr>
                               
                                <?php
                                    for ($j=0;$j<$i;$j++)
                                    {
                                ?>
                                    <th>Keyword</th>
                                    <th>Value</th>
                                    <th>Competition</th>
                                <?php
                                    }
                                ?>
                                
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                                if (!empty($pricing_data)) {
                                    foreach ($pricing_data as $pricing_item) {
                                        ?>
                                        <tr>
                                            <?php
                                            $i = 0;

                                                foreach( $pricing_item as $value)
                                                {
                                                if( $i != 0){

                                            ?>
                                            <td>
                                               
                                                    <?php echo $value;?>
                                            
                                            </td>
                                            <?php
                                                }
                                                else{
                                                    $i++;
                                                }
                                            }
                                            ?>
                                            <td>
                                                <?php echo btn_edit('admin/settings/industry_seo/' . $pricing_item->id); ?>
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
            <?php if ($active == 2){?>
            
            <div class="tab-pane <?= empty($new)? 'active' : ''; ?>" id="new">
                <form role="form" data-parsley-validate="" novalidate="" id="userform"
                      enctype="multipart/form-data"
                      action="<?php echo base_url(); ?>admin/settings/save_industryseo/<?php echo $edit_id;?>" method="post"
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
                                
                        <div class="tab-pane <?= !empty($new)? 'active' : ''; ?>" id="new">
                            <form role="form" data-parsley-validate="" novalidate="" id="userform"
                                enctype="multipart/form-data"
                                action="<?php echo base_url(); ?>admin/settings/save_industryseo/new" method="post"
                                class="form-horizontal form-groups-bordered">
                                <input type="hidden" name="new_data" value="new_data"/>
                                <div class="form-group overflow-part">
                            
                                <table class="table table-striped DataTables " cellspacing="0" width="100%">
                                    <thead>
                                    <tr>
                                            <th>
                                                <label>Industry</label>
                                            </th>
                                            <th>
                                                <label>Keyword</label>
                                            </th>
                                            <th>
                                                <label>Value</label>
                                            </th>
                                            <th>
                                                <label>Competition</label>
                                            </th>
                                    </tr>
                                    </thead>
                                    <tbody>

                                    <?php
                                            foreach ($tiers as $key=>$tiers_item) {
                                    ?>
                                        <tr>
                                            <td>
                                                <?php echo $tiers_item['industry'];?>
                                            </td>
                                                
                                                <td class="form-group">
                                                <input type='text' name = "keyword[<?php echo $tiers_item['id'];?>]"  class="form-control"/>
                                                </td>
                                                
                                                <td class="form-group">
                                                <input type='text' name = "value[<?php echo $tiers_item['id'];?>]" class="form-control"/>
                                                </td>
                                                
                                                <td class="form-group">
                                                <input type='text' name = "competition[<?php echo $tiers_item['id'];?>]"  class="form-control"/>
                                                </td>
                                        </tr>
                                        <?php
                                            }
                                            ?>
                                    </tbody>
                                </table>
                                        
                    </div>
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
                </form>
            </div>
            <?php }?>
            <?php if ($active == 3){?>
                <div class="tab-pane active" id="new">
                <form role="form" data-parsley-validate="" novalidate="" id="userform"
                      enctype="multipart/form-data"
                      action="<?php echo base_url(); ?>admin/settings/save_industryseo/new_keyword" method="post"
                      class="form-horizontal form-groups-bordered">
                    <div class="form-group overflow-part">
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
                                                    <input type="text" value="" name="keyword_field[<?php echo $comment['Field'];?>]" class="form-control"/>
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
            <?php }?>
    </div>
