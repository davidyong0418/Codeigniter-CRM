<?= message_box('success'); ?>
<?= message_box('error');
$created = can_action('158', 'created');
$edited = can_action('158', 'edited');
$deleted = can_action('158', 'deleted');
// var_dump($created);
// var_dump($created);
if (!empty($created) || !empty($edited)){
    ?>
    <div class="col-sm-12 bg-white p0" style="margin-bottom:30px">
        <div class="col-md-4">
            <div class="row row-table pv-lg">
                <div class="col-xs-6">
                    <p class="m0 lead text-center">0</p>
                    <p class="m0 text-center">
                        <small class="custom-color">Industry
                        </small>
                    </p>
                </div>
                <div class="col-xs-6">
                    <p class="m0 lead text-center">0</p>
                    <p class="m0 text-center">
                        <small class="custom-color">Industry CM
                        </small>
                    </p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="row row-table pv-lg">
                <div class="col-xs-6">
                    <p class="m0 lead text-center">0</p>
                    <p class="m0 text-center">
                        <small class="custom-color">Industry ROI
                        </small>
                    </p>
                </div>
                <div class="col-xs-6">
                    <p class="m0 lead text-center">0</p>
                    <p class="m0 text-center">
                        <small class="custom-color">Industry SEO
                        </small>
                    </p>
                </div>
            </div>
        </div>
    </div>
    <div class="nav-tabs-custom">
        <!-- Tabs within a box -->
        <ul class="nav nav-tabs">
                <li class=""><a href="#manage" data-toggle="tab"></a>
                </li>
            </ul>
        <div class="tab-content bg-white">
        <a href="<?= base_url() ?>admin/settings/industry_cm/new" class="btn btn-success float-right"><i class="fas fa-plus"></i>Add New Industry</a> 
            <!-- ************** general *************-->
            <div class="tab-pane active" id="manage">
                <?php }?>
                    <table class="table table-striped DataTables " id="DataTables" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Industry</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody> 
                        <?php 
                            if (!empty($industries)){
                            $i = 1;
                            foreach ($industries as $industry){
                        ?>
                                        <tr>
                                            <td><?php echo $i++;?></td>
                                            <td> <?php echo $industry['industry']; ?></td>
                                            <td>
                                                <?php echo btn_edit('admin/settings/industries/edit/' . $industry['id']); ?>
                                                <?php echo btn_view('admin/settings/industries/view/' . $industry['id']); ?>
                                                <?php echo btn_delete('admin/settings/industries/delete/' . $industry['id']); ?>
                                            </td>
                                        </tr>
                            <?php 
                            }
                                } else{ ?>
                                    <tr>
                                        <td colspan="9">
                                            <?php lang('no_data') ?>
                                        </td>
                                    </tr>
                            <?php  }?>
                        </tbody>
                    </table>
            </div>
    </div>

