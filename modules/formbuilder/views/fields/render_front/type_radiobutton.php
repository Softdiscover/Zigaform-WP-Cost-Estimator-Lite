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
<?php



$opt_class = 'sfdc-radio';
if ( isset($input2['block_align']) && intval($input2['block_align']) === 1) {
    $opt_class = 'sfdc-radio-inline';
}
?>
<?php
$defaul_class = 'rockfm-inp2-rdo';
if ( intval($input2['style_type']) === 1) {
    $defaul_class .= ' rockfm-input2-chk-styl1';
}
?>
<div data-uifm-tabnum="<?php echo $tab_num; ?>"
     data-theme-type="<?php echo $input2['style_type']; ?>"
     class="rockfm-input2-wrap">    
<?php
usort($input2['options'], ['Uiform_Form_Helper', 'compareByOrder']);
foreach ( $input2['options'] as $key => $value) {
    $checked = '';
    if ( isset($value['checked']) && intval($value['checked']) === 1) {
        $checked = 'checked="checked"';
    }
    ?>
    <div 
        data-opt-index="<?php echo $value['id']; ?>" 
        class="<?php echo $opt_class; ?>">
        <label>
            <span class="rockfm-inp2-opt-inp">
            <input type="radio"
                   data-chk-icon="<?php echo ( ! empty($input2['stl1']['icon_mark']) && strpos($input2['stl1']['icon_mark'], 'fa') !== false ) ? 'fa ' . $input2['stl1']['icon_mark'] : 'fa fa-check'; ?>"
                   <?php echo $checked; ?>
                   value="<?php echo $value['id']; ?>"
                   data-uifm-inp-val="<?php
                    if ( isset($value['value'])) {
                        echo esc_attr(Uiform_Form_Helper::sanitizeInput($value['value']));
                    }
                    ?>"
                   data-uifm-inp-label="<?php
                    if ( isset($value['label'])) {
                        echo esc_attr(Uiform_Form_Helper::sanitizeInput($value['label']));
                    }
                    ?>"
                   data-uifm-inp-price="<?php
                    if ( isset($value['price'])) {
                        echo $value['price'];
                    }
                    ?>"
                   name="<?php echo $nameField; ?>"
                   class="<?php echo $defaul_class; ?>">
            </span>
            <span class="rockfm-inp2-label rockfm-inp2-opt-label">
            <?php
            if ( isset($value['label'])) {
                echo $value['label'];
            }
            ?>
            </span>
            <?php
            if ( isset($price['lbl_show_st']) && intval($price['lbl_show_st']) === 1 && isset($value['price'])) {
                $tmp_price_label = urldecode($price['lbl_show_format']);
                $tmp_price_label = str_replace('[uifm_price]', '<span class="uiform-stickybox-inp-price">' . $value['price'] . '</span>', $tmp_price_label);
                if ( ! empty($tmp_price_label)) {
                    ?>
            <span class="rockfm-inp2-opt-price-lbl"><?php echo $tmp_price_label; ?></span>
                    <?php
                }
            }
            ?>
        </label>
     </div>
    <?php
}


?>
</div>
<?php
$cntACmp = ob_get_contents();
$cntACmp = Uiform_Form_Helper::sanitize_output($cntACmp);
ob_end_clean();

echo $cntACmp;
?>
