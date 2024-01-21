<?php
/**
 * Intranet
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   Zigapage_wp
 * @author    Softdiscover <info@softdiscover.com>
 * @copyright 2015 Softdiscover
 * @license   http://www.php.net/license/3_01.txt  PHP License 3.01
 * @link      http://zigapage.softdiscover.com
 */
if ( ! defined('ABSPATH')) {
    exit('No direct script access allowed');
}
$default_template = '';
ob_start();
?>
<div style="overflow-x:auto;background:#eee;clear:both;">
   <table cellpadding="0" cellspacing="0" style="margin:0px;">
           
        
            <tr class="details">
                <td>
                    Check
                </td>
                
                <td>
                    0
                </td>
            </tr>
            
            <tr class="heading">
                <td>
                    <b>Item</b>
                </td>
                
                <td>
                    <b>Price</b>
                </td>
            </tr>
            
            <tr class="item">
                <td>
                    Product 1
                </td>
                
                <td>
                    $0
                </td>
            </tr>
            
            <tr class="item">
                <td>
                    Service 1
                </td>
                
                <td>
                    $0
                </td>
            </tr>
            
            <tr class="item last">
                <td>
                    Product 2
                </td>
                
                <td>
                    $0
                </td>
            </tr>
            
            <tr class="total">
                <td></td>
                
                <td>
                   Total: $0
                </td>
            </tr>
        </table>
</div>
<?php
$default_template = ob_get_clean();

ob_start();
?>

<div class='sfdclauncher'>

</div>
<div class="sfdclauncher zgfm-block1-container sfdc-clearfix" >
    <div class="space20"></div>
    <div class="">
        <div class="col-lg-12">
            <div class="widget widget-padding span12">
                <div class="widget-header">
                    <i class="fa fa-list-alt"></i>
                    <h5>
                        <?php echo __('Woocommerce settings', 'FRocket_admin'); ?>
                    </h5>

                </div>  
                <div class="widget-body">
 
                    <?php if ( UIFORM_DEBUG === 1) { ?>
                    <a href="javascript:void(0);" onclick="javascript:zgfm_back_addon_woocomm.dev_show_vars();" class="sfdc-btn sfdc-btn-primary">
                    <span class="fa fa-desktop"></span> show data</a>
                    <?php } ?>
                    
                        <div class="form-group row">
                            <label  class="col-sm-2 col-form-label"><?php echo __('Integrate with woocommerce', 'FRocket_admin'); ?></label>
                            <div class="col-sm-10">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input woocomm-input" data-options="status" type="radio" id="woocmc_status_1" name="woocmc_status" value="0">
                                    <label for="woocmc_status_1" class="form-check-label" ><?php echo __('no', 'FRocket_admin'); ?></label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input woocomm-input" data-options="status"  type="radio" id="woocmc_status_2" name="woocmc_status"   value="1">
                                    <label for="woocmc_status_2" class="form-check-label" ><?php echo __('yes', 'FRocket_admin'); ?></label>
                                </div>
                            </div>
                        </div>
                         <div class="form-group row">
                            <label  class="col-sm-2 col-form-label"><?php echo __('WooCommerce Product ID', 'FRocket_admin'); ?></label>
                            <div class="col-sm-10">
                                <input class="form-control woocomm-input" data-options="prod_id"  id="woocmc_prod_id" type="text"   placeholder="">
                               <p><i><?php echo __('WooCommerce product ID for this form', 'FRocket_admin'); ?></i></p>
                            </div>
                        </div>
                  
                    
                    
                     <div class="form-group row">
                            <label for="" class="col-sm-2 col-form-label"><?php echo __('woocommerce quantity ', 'FRocket_admin'); ?></label>
                            <div class="col-sm-10">
                                <select class="custom-select woocomm-input" data-options="wc_quantity"  id="woocmc_quantity"  >
                                    <option value="1"><?php echo __('None', 'FRocket_admin'); ?></option>
                                    
                                </select>
                                <p><i><?php echo __('just choose a field that will be the product quantity in Woocommerce', 'FRocket_admin'); ?></i></p>
                            </div>
                        </div>
  
                       <hr>
                        <div class="card">
                            <div class="card-header">
                                <?php echo __('summary content on checkout', 'FRocket_admin'); ?>
                            </div>
                            <div class="card-body">
                                
                                <p class="card-text"><?php echo __('show custom summary content on checkout', 'FRocket_admin'); ?></p>
                                  <div class="form-group row">
                                        <label  class="col-sm-2 col-form-label"><?php echo __('Enable summary', 'FRocket_admin'); ?></label>
                                        <div class="col-sm-10">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input woocomm-input" data-options="summ_status" type="radio" id="woocmc_summstatus_1" name="woocmc_summstatus"  value="0">
                                                <label for="woocmc_summstatus_1" class="form-check-label" ><?php echo __('no', 'FRocket_admin'); ?></label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input woocomm-input" data-options="summ_status"  type="radio" id="woocmc_summstatus_2" name="woocmc_summstatus"   value="1">
                                                <label for="woocmc_summstatus_2" class="form-check-label" ><?php echo __('yes', 'FRocket_admin'); ?></label>
                                            </div>
                                        </div>
                                  </div>
                                 <div class="form-group row">
                                    <label  class="col-sm-2 col-form-label"><?php echo __('summary title', 'FRocket_admin'); ?></label>
                                    <div class="col-sm-10">
                                        <input class="form-control woocomm-input" data-options="summ_title"  id="woocmc_summ_title" type="text"   placeholder="">
                                       <p><i><?php echo __('the summary title will be shown above summary content', 'FRocket_admin'); ?></i></p>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label  class="col-sm-2 col-form-label"><?php echo __('summary content', 'FRocket_admin'); ?></label>
                                    <div class="col-sm-10">
                                        
                                        
                                        <?php
                                                        /* pending add this tinymce */
                                                        $settings = array(
                                                            'media_buttons' => true,
                                                            'editor_height' => 325,
                                                            'textarea_rows' => 20,
                                                        );
                                                        wp_editor($default_template, 'woocmc_summ_content', $settings);
                                                        ?>
                                       <p><i><?php echo __('summary content support HTML. you can add the backend shortcodes as same as it is done on email notification ', 'FRocket_admin'); ?></i></p>
                                    </div>
                                </div>
                            
                            </div>
                        </div>






                </div>    
            </div>
        </div>
    </div>
</div>    
 
 
<?php
$cntACmp = ob_get_contents();

$cntACmp = preg_replace('/\s+/', ' ', $cntACmp);
ob_end_clean();
echo $cntACmp;
?>
