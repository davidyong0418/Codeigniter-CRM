<?php 
     $price_tier = $request_proposal_info[0]->price_category;
     if(!empty($price_tier))
     {
         $query = "select a.".$price_tier." as value from `tbl_pricekeyword` as a where a.keyword='SEO 10 Keywords' or a.keyword='Content Marketing Per Article' or a.keyword ='SEO 20 Keywords' or a.keyword = 'SMM 05/20 Hours'";
         $price_values = $this->db->query($query)->result_array();
         if(!empty($price_values)){
             $keyword_10 = $price_values[0]['value'];
             $content_marketing = $price_values[1]['value'];
             $keyword_20 = $price_values[2]['value'];
             $smm_5 = $price_values[3]['value'];
         }
     }



?>


<div class="recommendations card-body">
        <h3>RECOMMENDATIONS</h3>
        <div>
            <p>
                <span class="font-weight-bold">ANY SINGLE SERVICE CAN BE PURCHASED BY ITSELF. </span>
                However, in order to yield the highest ROI for every dollar spent, we recommend a composite marketing strategy comprised of website conversion optimization, search engine optimization (SEO), managed digital advertising (MDA), and content marketing (CM).  Budget permitting, the simultaneous combination of the strategies greatly amplifies the efficiency of each individual part while creating exponential results as a whole.  The recommendation that follows is exactly what we believe you need in order to get the highest ROI on a mid-level budget:

            </p>
        </div>
        <div class="as-recommended table-responsive padding-top-10">
            <table class="table table-bordered color-bordered-table color-table success-bordered-table full-success-table hover-table">
                <thead>
                    <tr>
                        <th colspan="3" class="text-center">AS RECOMMENDED</th>
                    </tr>
                </thead>
                <tbody class="text-center">
                    <tr>
                        <td>SEO</td>
                        <td>
                            Search Engine Optimization - 10 Keywords
                        </td>
                        <td class="text-nowrap">
                            $<?php if(!empty($keyword_10)){echo $keyword_10;}else{echo '692';}?>
                        </td>
                    </tr>
                    <tr>
                        <td>CM</td>
                        <td>
                            Content Marketing - 4 Articles Per Month
                        </td>
                        <td class="text-nowrap">
                            $<?php if(!empty($content_marketing)){echo $content_marketing*4;}else{echo '192';}?>
                        </td>
                    </tr>
                    <tr>
                        <td>MDA</td>
                        <td>
                            Managed Digital Advertising
                        </td>
                        <td class="text-nowrap">
                            $300
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3">$450 ONE-TIME CAMPAIGN FEE + $<?php if(!empty($content_marketing)){echo intval(($content_marketing*4)+$keyword_10);}else{echo '1,184';};?> RECURRING MONTH-TO-MONTH</td>
                    </tr>
                    <tr style="background-color:#FFFF00;">
                        <td colspan="3">THIS PACKAGE COMES WITH A 100% MONEY BACK GUARANTEE ON SEO, IN WRITING OR A NEW WEBSITE AT NO ADDITIONAL COST WITH A 6-MONTH MINIMUM COMMITMENT</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="restricted-budget-seo table-responsive">
            <p class="text-center">If you can only do one thing, that thing should be SEO or MDA.</p>
            <table class="table table-bordered color-bordered-table color-table danger-bordered-table hover-table">
                <thead>
                    <tr>
                        <th colspan="3" class="text-center">RESTRICTED BUDGET (SEO)</th>
                    </tr>
                </thead>
                <tbody class="text-center">
                    <tr>
                        <td>SEO</td>
                        <td>
                            Search Engine Optimization - 10 Keywords
                        </td>
                        <td class="text-nowrap">
                            $<?php if(!empty($keyword_10)){echo $keyword_10;}else{echo '692';}?>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3">$450 ONE-TIME CAMPAIGN FEE + $<?php if(!empty($keyword_10)){echo $keyword_10;}else{echo '700';}?> RECURRING MONTH-TO-MONTH</td>
                    </tr>
                    <tr class="bg-secondary">
                        <td colspan="3">THIS PACKAGE COMES  NEW WEBSITE AT 50% OFF WITH A 6-MONTH MINIMUM COMMITMENT</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="restricted-budget-mda table-responsive">
            
            <table class="table table-bordered color-bordered-table danger-bordered-table hover-table color-table">
                <thead>
                    <tr>
                        <th colspan="3" class="text-center">RESTRICTED BUDGET (MDA)</th>
                    </tr>
                </thead>
                <tbody class="text-center">
                    <tr>
                        <td>MDA</td>
                        <td>
                            Managed Digital Advertising $450 Budget + $250 Fee
                        </td>
                        <td>
                            $700
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3">$450 ONE-TIME CAMPAIGN FEE + $700 RECURRING MONTH-TO-MONTH</td>
                    </tr>
                    <tr class="bg-secondary">
                        <td colspan="3">THIS PACKAGE COMES  NEW WEBSITE AT 50% OFF WITH A 6-MONTH MINIMUM COMMITMENT</td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <div class="as-recommended table-responsive">
            <div class="col-sm-12"><p class="text-center">If you are operating with a more permissive budget and want to be more aggressive, your best return would be found in increasing your keywords to 20, increasing your Managed Digital Advertising (MDA) spend to $600, and adding Social Media Management (SMM) 10 hours per week.</p></div>
            <table class="table table-bordered color-bordered-table info-bordered-table hover-table color-table">
                <thead>
                    <tr>
                        <th colspan="3" class="text-center">PERMISSIVE BUDGET</th>
                    </tr>
                </thead>
                <tbody class="text-center">
                    <tr>
                        <td>SEO</td>
                        <td>
                            Search Engine Optimization - 20 Keywords
                        </td>
                        <td class="text-nowrap">
                            $<?php if(!empty($keyword_20)){echo $keyword_20;}else{echo '1,134';}?>
                        </td>
                    </tr>
                    <tr>
                        <td>CM</td>
                        <td>
                            Content Marketing - 4 Articles Per Month
                        </td>
                        <td class="text-nowrap">
                            $<?php if(!empty($content_marketing)){echo $content_marketing*4;}else{echo '427';}?>
                        </td>
                    </tr>
                    <tr>
                        <td>MDA</td>
                        <td>
                            Managed Digital Advertising
                        </td>
                        <td class="text-nowrap">
                            $600
                        </td>
                    </tr>
                    <tr>
                        <td>SMM</td>
                        <td>
                            Social Media Management - 20hrs/Month (5hrs/Week)
                        </td>
                        <td class="text-nowrap">
                            $<?php if(!empty($smm_5)){echo $smm_5;}else{echo 'no value';}?>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3">$450 ONE-TIME CAMPAIGN FEE + $<?php if(!empty($content_marketing)){echo intval(($content_marketing*4)+$keyword_20+$smm_5+600);}else{echo '2,353';}?> RECURRING MONTH-TO-MONTH</td>
                    </tr>
                    <tr style="background-color:#FFFF00;">
                        <td colspan="3">THIS PACKAGE COMES WITH A 100% MONEY BACK GUARANTEE ON SEO, IN WRITING OR A NEW WEBSITE AT NO ADDITIONAL COST WITH A 6-MONTH MINIMUM COMMITMENT</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>