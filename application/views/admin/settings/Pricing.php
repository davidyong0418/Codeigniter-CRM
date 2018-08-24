<?= message_box('success'); ?>
<?= message_box('error');
$created = can_action('24', 'created');
$edited = can_action('24', 'edited');
$deleted = can_action('24', 'deleted');
// var_dump($created);

    ?>
     

    <div class="nav-tabs-custom">
    <ul class="nav nav-tabs">
                <li class=""><a href="#manage" data-toggle="tab"></a>
                </li>
            </ul>
        <!-- Tabs within a box -->
        <div class="tab-content bg-white">
            <!-- ************** general *************-->
            <?php if ( $action == ''){?>
        <a href="<?= base_url() ?>admin/settings/pricing_action/new" class="btn btn-success float-right"><i class="fas fa-plus"></i>Add New Price</a> 
                <div class="tab-pane active" id="manage">
                    <table class="table table-striped DataTables " id="DataTables" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th>
                                    No
                                </th>
                                <th>
                                   Pricing
                                </th>
                                <th>
                                    Action
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $i =1;
                                    foreach ($whitelables as $key=>$item)
                                    {
                                ?>
                            <tr>
                                <td><?php echo $i;$i++;?></td>
                                <td>
                                <?php echo $item['price']; ?>
                                </td>
                                <td>
                                    <?php echo btn_edit('admin/settings/pricing/edit/' . $item['id']); ?>
                                    <?php echo btn_view('admin/settings/pricing/view/' . $item['id']); ?>
                                    <?php echo btn_delete('admin/settings/pricing/delete/' . $item['id']); ?>
                                </td>
                            </tr>
                            <?php 
                                    }
                            ?>
                        </tbody>
                    </table>
            </div>
             <?php }?>
             <?php if ($action != ''){?>
             <form role="form" data-parsley-validate="" novalidate="" id="userform"
                            enctype="multipart/form-data"
                            action="<?php echo base_url(); ?>admin/settings/save_pricing/<?php if(!empty($id)){echo $id;}?>" method="post"
                            class="form-horizontal form-groups-bordered">
                            <?php if ($id == 'new'){
                                ?>
                                <div class="row">
                                    <div class="col-sm-12 new-industry-group">
                                        <div class="control-label float-left">
                                            <label class="form-lable">Brand Name</label>
                                        </div>
                                        <div class="col-sm-10">
                                            <input type="text" class="form-control new-industry" name="new_industry" required/>
                                        </div>
                                    </div>
                                </div>
                            <?php }?>
            <?php if ($action != ''){?>
            
                <div class="tab-pane active" id="industry_cm">
                    <!-- tab 1 -->
                        <input type="hidden" name="new_data" value="new_industrycm"/>
                        <div class="industry_cm">
                            <?php
                                $i = 0; 
                                foreach ($whitebrandkeywords as $key=>$item){
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
                            <a href="<?= base_url() ?>admin/settings/pricing" class='btn btn-primary'>Back</a> 
                            <?php if ($action != 'view'){ ?>
                                <input type="button" class="btn btn-primary add-new-keyword" onClick="" value="New Price">
                                <button type="submit" class="btn btn-primary new-industry-save-btn" onClick=""><?= lang('save')?></button>
                            <?php }?>
                        </div>
                    </div>
   </div>
                        
            <?php }?>

            </form>
        <?php }?>
    </div>
    </div>

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
    $(document).on('click','.additional_times', function(){
        $('.added_key').has($(this)).remove();
    });
})
</script>