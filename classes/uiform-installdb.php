<?php
/**
 * Frontend
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
if ( class_exists('Uiform_InstallDB')) {
    return;
}

class Uiform_InstallDB
{

    public $form;
    public $form_history;
    public $form_fields;
    public $form_log;
    public $form_fields_type;
    public $settings;
    public $core_addon;
    public $core_addon_detail;
    public $core_addon_log;
    public $pay_gateways;
    public $pay_records;
    public $pay_logs;
    public $visitor;
    public $visitor_error;

    public function __construct()
    {
        global $wpdb;
        $this->form              = $wpdb->prefix . 'cest_uiform_form';
        $this->form_history      = $wpdb->prefix . 'cest_uiform_form_records';
        $this->form_fields       = $wpdb->prefix . 'cest_uiform_fields';
        $this->form_log          = $wpdb->prefix . 'cest_uiform_form_log';
        $this->form_fields_type  = $wpdb->prefix . 'cest_uiform_fields_type';
        $this->settings          = $wpdb->prefix . 'cest_uiform_settings';
        $this->pay_gateways      = $wpdb->prefix . 'cest_uiform_pay_gateways';
        $this->pay_records       = $wpdb->prefix . 'cest_uiform_pay_records';
        $this->pay_logs          = $wpdb->prefix . 'cest_uiform_pay_logs';
        $this->visitor           = $wpdb->prefix . 'cest_uiform_visitor';
        $this->visitor_error     = $wpdb->prefix . 'cest_uiform_visitor_error';
        $this->core_addon        = $wpdb->prefix . 'cest_addon';
        $this->core_addon_detail = $wpdb->prefix . 'cest_addon_details';
        $this->core_addon_log    = $wpdb->prefix . 'cest_addon_details_log';
    }

    public function install($networkwide = false)
    {
        if ( $networkwide) {
            deactivate_plugins(plugin_basename(UIFORM_ABSFILE));
            wp_die(__('The plugin can not be network activated. You need to activate the plugin per site.', 'FRocket_admin'));
        }
        global $wpdb;
        $charset = '';
        if ( $wpdb->has_cap('collation')) {
            if ( ! empty($wpdb->charset)) {
                $charset = "DEFAULT CHARACTER SET $wpdb->charset";
            }
            if ( ! empty($wpdb->collate)) {
                $charset .= " COLLATE $wpdb->collate";
            }
        }
        
        if (  version_compare($wpdb->db_version(), '8.0', '>=')) {
             require_once(__DIR__ . '/mysql8.php');
        }else {
            require_once(__DIR__ . '/mysql.php');
        }


          // Store the date when the initial activation was performed
        $type      = class_exists('UiformCostEstLite') ? 'pro' : 'lite';
        $activated = get_option('zgfm_c_activated', array());
        if ( empty($activated[ $type ])) {
            $activated[ $type ] = time();
            update_option('zgfm_c_activated', $activated);
        }

        // ajax mode by default
        update_option('zgfm_c_modalmode', 0);
    }

    public function uninstall()
    {

                    global $wpdb;
        $wpdb->query('DROP TABLE IF EXISTS ' . $this->form_history);
        $wpdb->query('DROP TABLE IF EXISTS ' . $this->form_fields);
        $wpdb->query('DROP TABLE IF EXISTS ' . $this->form_log);
        $wpdb->query('DROP TABLE IF EXISTS ' . $this->form_fields_type);
        $wpdb->query('DROP TABLE IF EXISTS ' . $this->form);
        $wpdb->query('DROP TABLE IF EXISTS ' . $this->settings);
        $wpdb->query('DROP TABLE IF EXISTS ' . $this->pay_gateways);
        $wpdb->query('DROP TABLE IF EXISTS ' . $this->pay_records);
        $wpdb->query('DROP TABLE IF EXISTS ' . $this->pay_logs);
        $wpdb->query('DROP TABLE IF EXISTS ' . $this->visitor);
        $wpdb->query('DROP TABLE IF EXISTS ' . $this->visitor_error);
        $wpdb->query('DROP TABLE IF EXISTS ' . $this->core_addon);
        $wpdb->query('DROP TABLE IF EXISTS ' . $this->core_addon_detail);
        $wpdb->query('DROP TABLE IF EXISTS ' . $this->core_addon_log);

         // removing options
        delete_option('uifmcostest_version');
    }
}
