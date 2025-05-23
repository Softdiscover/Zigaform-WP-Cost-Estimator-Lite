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
$nameField =  apply_filters('uifm_ms_render_field_front', "uiform_fields[".$id."]", $id, $type);
?>
 <div class="sfdc-input-group">
        <div class="sfdc-input-group-addon rockfm-inp-preptxt"><?php echo urldecode($input['prepe_txt']); ?></div>
        <input placeholder="<?php echo htmlentities($input['placeholder'], ENT_QUOTES, 'UTF-8'); ?>"
            class="rockfm-txtbox-inp-val sfdc-form-control"
            type="text" 
            data-uifm-tabnum="<?php echo $tab_num; ?>"
            name="<?php echo $nameField; ?>"
            value="<?php echo $input['value']; ?>">
        <div class="sfdc-input-group-addon rockfm-inp-apptxt"><?php echo urldecode($input['append_txt']); ?></div>
    </div>
<?php
$cntACmp = ob_get_contents();
$cntACmp = Uiform_Form_Helper::sanitize_output($cntACmp);
ob_end_clean();

echo $cntACmp;
?>
