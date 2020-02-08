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
 * @link      http://wordpress-cost-estimator.zigaform.com
 */
if (!defined('ABSPATH')) {
    exit('No direct script access allowed');
}
if (class_exists('zfaddn_woocommerce_model')) {
    return;
}

/**
 * Model Setting class
 *
 * @category  PHP
 * @package   Rocket_form
 * @author    Softdiscover <info@softdiscover.com>
 * @copyright 2013 Softdiscover
 * @license   http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version   Release: 1.00
 * @link      http://wordpress-cost-estimator.zigaform.com
 */
class zfaddn_woocommerce_model {

    private $wpdb = "";
    public $table = "";

    function __construct() {
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->form = $wpdb->prefix . "cest_uiform_form";
        $this->form_records = $wpdb->prefix . "cest_uiform_form_records";
        $this->form_pay_records = $wpdb->prefix . "cest_uiform_pay_records";
        $this->form_addon = $wpdb->prefix . "cest_addon";
        $this->form_addon_details = $wpdb->prefix . "cest_addon_details";
    }

    function getData($form_id,$rec_id) {
        $query = sprintf('SELECT wc.adet_data, wf.fmb_name,wp.pgr_payment_amount
FROM %s as wc 
JOIN %s as wf
JOIN %s as wr
JOIN %s as wp
WHERE wc.fmb_id=%s and wc.add_name="woocommerce" and wf.fmb_id=wr.form_fmb_id and wr.fbh_id=wp.fbh_id and wr.fbh_id=%s', $this->form_addon_details,$this->form,$this->form_records,$this->form_pay_records,$form_id,$rec_id);

        return $this->wpdb->get_row($query);
    }

    

}

?>
