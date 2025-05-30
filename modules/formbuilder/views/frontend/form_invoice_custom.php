<?php
/**
 * Intranet
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   Rocket_form
 * @author    Softdiscover <info@softdiscover.com>
 * @copyright 2015 Softdiscover
 * @license   http://www.php.net/license/3_01.txt  PHP License 3.01
 * @link      https://wordpress-cost-estimator.zigaform.com
 */
if ( ! defined('ABSPATH')) {
    exit('No direct script access allowed');
}
ob_start();
?>
 <div class="sfdc-row">
        <div class="sfdc-col-md-12">
            <div class="uifm-inforecord-box-info">
                <div id="uifm_frm_modal_html_loader"><img src="<?php echo $base_url . '/assets/backend/image/ajax-loader-black.gif'; ?>"></div>
          <iframe src="<?php echo $url_form; ?>" 
        scrolling="no" 
        id="zgfm-iframe-<?php echo $form_id; ?>-inv-showsumm"
        frameborder="0" 
        style="border:none;width:100%;" 
        allowTransparency="true"></iframe>
                 <script type="text/javascript">
                    
                    document.getElementById("zgfm-iframe-<?php echo $form_id; ?>-inv-showsumm").onload = function() {
        document.getElementById("uifm_frm_modal_html_loader").style.display = 'none';
        setTimeout(function() {
            iFrameResize({
                                                    log                     : false,
                                                    onScroll: function (coords) {
                                                        /*console.log("[OVERRIDE] overrode scrollCallback x: " + coords.x + " y: " + coords.y);*/
                                                    }
                                            },'#zgfm-iframe-<?php echo $form_id; ?>-inv-showsumm');
}, 500);
    };
          </script> 
            </div>
        </div>
        
</div>
<?php
$cntACmp = ob_get_contents();
$cntACmp = Uiform_Form_Helper::sanitize_output($cntACmp);
ob_end_clean();
echo $cntACmp;
?>
