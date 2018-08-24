<?= message_box('success'); ?>
<?= message_box('error');
$created = can_action('158', 'created');
$edited = can_action('158', 'edited');
$deleted = can_action('158', 'deleted');
// var_dump($created);
    ?>
    <form role="form" data-parsley-validate="" novalidate="" id="userform"
                            enctype="multipart/form-data"
                            action="<?php echo base_url(); ?>admin/settings/save_industrycm/<?php if(!empty($id)){echo $id;}?>" method="post"
                            class="form-horizontal form-groups-bordered">
    <?php if ($id == 'new'){
    ?>
    <div class="row">
        <div class="col-sm-12 new-industry-group">
            <div class="control-label float-left">
                <label class="form-lable">Industry Name</label>
            </div>
            <div class="col-sm-10">
                <input type="text" class="form-control new-industry" name="new_industry" required/>
            </div>
        </div>
    </div>
    <?php }?>
    <div class="nav-tabs-custom row">
        <!-- Tabs within a box -->
        
        <ul class="nav nav-tabs">
            <li class="<?= $active == 1 ? 'active' : ''; ?>">
                <a href="#industry_cm" data-toggle="tab">
                   Industry CM</a></li>
            <li class="<?= $active == 2 ? 'active' : ''; ?>">
                <a href="#industry_roi" data-toggle="tab">Industry ROI</a>
            </li>
            <li class="<?= $active == 3 ? 'active' : ''; ?>">
                <a href="#industry_seo" data-toggle="tab">Industry SEO</a>
            </li>
        </ul>
        
        <div class="tab-content bg-white">
            <!-- ************** general *************-->
                <div class="tab-pane <?= $active == 1 ? 'active' : ''; ?>" id="industry_cm">
                    <!-- tab 1 -->
                    
                        <input type="hidden" name="new_data" value="new_industrycm"/>
               
                        <div class="industry_cm">
                           
                            <?php
                                $i = 0; 
                                foreach ($industry_cm as $key=>$item){
                            ?>
                                <div class="form-group">
                                    <div class="col-sm-3 control-label">
                                        <?php echo $item['keyword']; ?>
                                    </div>
                                    <div class="col-sm-4 control-label text-center">
                                    <?php if ($action != 'view'){ ?>
                                        <input type='text' name = "industry_cm[<?php echo $item['id'];?>]"  class="form-control" value="<?php if (!empty($item[$id])){
                                                echo $item[$id];
                                                $i++;
                                            }
                                        ?>" required/>
                                    <?php }else{
                                        echo $item[$id];
                                    }?>
                                    </div>
                                </div>
                            <?php 
                            }
                            ?>
                        </div>
                    <div class="form-group">
                        <label for="field-1" class="col-sm-3 control-label"></label>
                        <div class="col-sm-8">
                            <a href="<?= base_url() ?>admin/settings/industries" class='btn btn-primary'>Back</a> 
                            <?php if ($action != 'view'){ ?>
                                <input type="button" class="btn btn-primary add-new-keyword" onClick="" value="New Article Title">
                                <button type="submit" class="btn btn-primary new-industry-save-btn" onClick=""><?= lang('save')?></button>
                                <!-- <button type="submit" id="sbtn" class="btn btn-primary" onClick="">Save and continue</button> -->
                            <?php }?>
                        </div>
                    </div>
   </div>
    
        <div class="tab-pane <?= $active == 2 ? 'active' : ''; ?>" id="industry_roi">
                            <input type="hidden" name="new_data" value="new_industrycm"/>
                            <div class="industry_cm">
                                <div class="form-group">
                                    <div class="col-sm-3 control-label">
                                        <h5 class="custom-color">keyword</h5>
                                    </div>
                                    <div class="col-sm-4 control-label text-center">
                                        <h5 class="custom-color">value</h5>
                                    </div>
                                </div>

                                <?php
                                    $i = 0;
                                    $total = 0;
                                    $j = 0;
                                    foreach ($industry_roi as $key=>$item){
                                ?>
                                    <div class="form-group">
                                        <div class="col-sm-3 control-label">
                                            <?php echo $item['keyword']; ?>
                                        </div>
                                        <div class="col-sm-4 control-label text-center">
                                        <?php if ($action != 'view'){ ?>
                                            <input type='text' name = "industry_roi[<?php echo $item['id'];?>]" required class="form-control industry-roi-value" value="<?php 
                                                if (!empty($item[$id])){
                                                    echo $item[$id];
                                                }
                                            ?>" data-parsley-type="number"/>

                                            
                                        <?php } else{?>
                                           <?php echo $item[$id];?></p>
                                        <?php }?>
                                        </div>
                                    </div>


                                        <?php 
                                        if($action == 'view'){                                           
                                                if( $i !=1 && $i !=2 && $i !=0 ){
                                                    $total = $total + $industry_roi[$i][$id];
                                                }
                                                $i++;
                                        if ( $item == end($industry_roi) )
                                        {
                                            
                                            ?>
                                        <div class="form-group">
                                            <div class="col-sm-3 control-label">
                                            <strong class="float-right">Total Vol (Auto Calc) :</strong>
                                            </div>
                                            <div class="col-sm-4 control-label text-center">
                                                <strong>
                                                    <?php 
                                                        echo $total;
                                                    ?>
                                                </strong>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-sm-3 control-label">
                                            <strong class="float-right">Market Share Low (Auto-Calc) :</strong>
                                            </div>
                                            <div class="col-sm-4 control-label text-center">
                                                <strong>$
                                                    <?php 
                                                        $low = intval(($industry_roi[0][$id] * $total * $industry_roi[1][$id])/1000);
                                                        echo $low;
                                                    ?>
                                                </strong>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <div class="col-sm-3 control-label">
                                            <strong class="float-right">Market Share High (Auto-Calc) :</strong>
                                            </div>
                                            <div class="col-sm-4 control-label text-center">
                                                <strong>
                                                $ <?php 
                                                    $high = intval(($industry_roi[2][$id] * $total * $industry_roi[0][$id])/1000);
                                                echo $high;?>
                                                </strong>
                                            </div>
                                        </div>
                                        <?php
                                        }
                                    }
                                    else{
                                        ?>

                                        <?php 
                                        
                                        if( $i !=1 && $i !=2 && $i !=0 ){
                                            if($id != 'new')
                                            {
                                                $total = $total + $industry_roi[$i][$id];
                                            }
                                            else{
                                                $total = '';
                                            }
                                        }
                                        $i++;
                                        if ($item == end($industry_roi))
                                        {
                                            
                                            ?>
                                        <div class="form-group">
                                            <div class="col-sm-3 control-label">
                                                <strong class="float-right">Total Vol (Auto Calc) :</strong>
                                            </div>
                                            <div class="col-sm-4 control-label text-center">
                                                <input type='text' class="form-control" value="<?php echo $total;?>" disabled/>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-sm-3 control-label">
                                                <strong class="float-right">Market Share Low (Auto-Calc) :</strong>
                                            </div>
                                            <div class="col-sm-4 control-label text-center">
                                            <input type='text' class="form-control" value="<?php
                                                if($id != 'new'){ 
                                                        $low = intval(($industry_roi[0][$id] * $total * $industry_roi[1][$id])/1000);
                                                        echo $low;
                                                    }else{echo '';}
                                                    ?>" disabled/>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-sm-3 control-label">
                                            <strong class="float-right">Market Share High (Auto-Calc) :</strong>
                                            </div>
                                            <div class="col-sm-4 control-label text-center">
                                            <input type='text' class="form-control" value="<?php 
                                                if($id != 'new'){
                                                    $high = intval(($industry_roi[2][$id] * $total * $industry_roi[0][$id])/1000);
                                                echo $high;
                                                }else{echo '';}
                                                ?>" disabled/>
                                            </div>
                                        </div>


                                        <?php }?>
                                        <?php }?>
                                <?php 
                                }
                                ?>
                                
                            </div>

                        <div class="form-group">
                            <label for="field-1" class="col-sm-3 control-label"></label>
                            <div class="col-sm-8">
                                <a href="<?= base_url() ?>admin/settings/industries" class='btn btn-primary'>Back</a> 
                                <?php if ($action != 'view'){ ?>
                                    <button type="submit" class="btn btn-primary new-industry-save-btn" onClick=""><?= lang('save')?></button>
                                <?php }?>
                            </div>
                        </div>
        </div>

    <div class="tab-pane <?= $active == 3 ? 'active' : ''; ?>" id="industry_seo">
            
                            
                                <input type="hidden" name="new_data" value="new_industrycm"/>
                                <div class="industry_cm">
                                <div class="form-group">
                                    <div class="col-sm-3 control-label">
                                        <h5 class="custom-color"></h5>
                                    </div>
                                    <div class="col-sm-6 control-label">
                                        <div class="col-sm-4 text-center"><h5 class="custom-color">Keyword</h5></div>
                                        <div class="col-sm-4 text-center"><h5 class="custom-color">Volume</h5></div>
                                        <div class="col-sm-4 text-center"><h5 class="custom-color">Competition</h5></div>
                                    </div>
                                </div>

                                   <?php
                                   $i = 0;
                                        foreach ($industry_seo as $key=>$item){
                                        
                                    ?>
                                        <div class="form-group">
                                            <div class="col-sm-3 control-label">
                                                <?php echo $item['keyword']; ?>
                                            </div>
                                            <div class="col-sm-6 control-label">
                                                <div class="col-sm-4 text-center">
                                                <?php if ($action != 'view'){ ?>
                                                    <input type='text' name = "seo_keyword[<?php echo $item['id'];?>]"  class="form-control" value="<?php 
                                                        if(!empty($item[$id.'_keyword']))
                                                        {
                                                            echo $item[$id.'_keyword'];
                                                        } 
                                                    ?>" />
                                                <?php }else{echo $item[$id.'_keyword'];}?>
                                                </div>
                                                <div class="col-sm-4 text-center">
                                                    <?php if ($action != 'view'){ ?>
                                                        <input type='text' name = "seo_value[<?php echo $item['id'];?>]" class="form-control" value="<?php 
                                                            if(!empty($item[$id.'_value']))
                                                            {
                                                                echo $item[$id.'_value'];
                                                            } 
                                                        
                                                        ?>" data-parsley-type="number" />
                                                    <?php }else{echo $item[$id.'_value'];}?>
                                                </div>
                                                <div class="col-sm-4 text-center">
                                                    <?php if ($action != 'view'){ ?>
                                                        <input type='text' name = "seo_competition[<?php echo $item['id'];?>]"  class="form-control" value="<?php 
                                                            if(!empty($item[$id.'_competition']))
                                                            {
                                                                echo $item[$id.'_competition'];
                                                            } 
                                                        ?>" />
                                                    <?php }else{
                                                        echo $item[$id.'_competition'];
                                                        }?>
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
                                    <a href="<?= base_url() ?>admin/settings/industries" class='btn btn-primary'>Back</a> 
                                    <?php if ($action != 'view'){ ?>
                                        <input type="button" class="btn btn-primary add-new-seokeyword" onClick="" value="New Article Title">
                                        <button type="submit" id="sbtn" class="btn btn-primary" onClick=""><?= lang('save')?></button>
                                    <?php }?>
                                </div>
                            </div>
                    </div>
    </div>
    
        


</form>
<script>
$(document).ready(function(){
    var i = 0;
    $('.add-new-keyword').click(function(){
        var template = `<div class="form-group added_key">
            <div class="col-sm-3 control-label">
                <div class="col-sm-6 float-right new-keywork-parent">
                <input type="text" placeholder="New field name" name="new_cmkeyword[`+i+`]" required class="form-control new-keyword">
                </div>
            </div>
            <div class="col-sm-4 control-label display-flex">
                <input type='text' placeholder="value" name = "new_cmkeyword_value[`+i+`]" required class="form-control new-keyword-value"/>
                <i class="fas fa-times additional_times float-right" style="margin-left:5px"></i>
            </div>
        </div>`;
        $('.tab-pane').has($(this)).find('.industry_cm').append(template);
        i++;
    });
    var j = 0;
    $('.add-new-seokeyword').click(function(){
        var template = `<div class="form-group added_key">
                <div class="col-sm-3 control-label">
                    <div class="col-sm-6 float-right new-keywork-parent">
                        <input type="text" placeholder="New field name" name="new_seokeyword[`+j+`]" required class="form-control new-keyword">
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
        $('.tab-pane').has($(this)).find('.industry_cm').append(template);
        j++;
    });
    $(document).on('click','.additional_times', function(){
        $('.added_key').has($(this)).remove();
    });
    $(document).on('change','.industry-roi-value',function(){
        var total = 0;
        $('.industry-roi-value').each(function(index){
            if(index !=0 && index !=1 && index !=2)
            {
                total = parseInt(total) + parseInt($(this).val());
            }

        });
        var val_0 =  $('.industry-roi-value').eq(0);
        var val_1 = $('.industry-roi-value').eq(1);
        var val_2 = $('.industry-roi-value').eq(2);
        var val_3 = $('.industry-roi-value').eq(3);
        var low_value = parseFloat(val_1)*parseFloat(val_2)*parseInt(total);
        var high_value = parseFloat(val_3)*parseFloat(val_1)*parseInt(total);
        

        
    });
    
    // $(document).on('click','.new-industry-save-btn',function(){
    //     console.log($('.new-industry-group').length);
    //     alert();
    //     if($('.new-industry-group').length){
    //        $('form').has($(this)).find('.new-industry-input').val($('.new-industry').val());
    //        alert();
    //     }
    // });
});
</script>