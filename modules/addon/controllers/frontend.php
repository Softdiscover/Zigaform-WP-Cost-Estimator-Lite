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
 * @link      https://softdiscover.com/zigaform/wordpress-cost-estimator
 */
if ( ! defined('ABSPATH')) {
    exit('No direct script access allowed');
}
if ( class_exists('zgfm_mod_addon_controller_front')) {
    return;
}

/**
 * Controller Settings class
 *
 * @category  PHP
 * @package   Rocket_form
 * @author    Softdiscover <info@softdiscover.com>
 * @copyright 2013 Softdiscover
 * @license   http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version   Release: 1.00
 * @link      https://softdiscover.com/zigaform/wordpress-cost-estimator
 */
class zgfm_mod_addon_controller_front extends Uiform_Base_Module
{

    const VERSION       = '0.1';
    private $pagination = '';
    private $per_page       = 5;
    private $wpdb       = '';
    protected $modules;
    private $model_addon = '';

    /**
     * Constructor
     *
     * @mvc Controller
     */
    protected function __construct()
    {
        global $wpdb;
        $this->wpdb        = $wpdb;
        $this->model_addon = self::$_models['addon']['addon'];
    }



    public function load_addonsByFront()
    {

        global $wpdb;
        $table_name = $wpdb->prefix . 'cest_addon';
        if ( $wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
            // table not in database
            return;
        }

        // get addons
        $tmp_addons = $this->model_addon->getListAddonsByFront();

        // flag variables
        $tmp_addons_arr  = array();
        $tmp_modules_arr = self::$_addons;

        foreach ( $tmp_addons as $key => $value) {
            // $tmp_addons_arr[$value->add_section][$value->add_name]=$tmp_addons[$key];

            // load addons
            require_once UIFORM_FORMS_DIR . '/modules/addon_' . $value->add_name . '/controllers/frontend.php';
            call_user_func(array( 'zfaddn_' . $value->add_name . '_front', 'get_instance' ));
        }
    }

    public function load_addActions()
    {

        $tmp_addons = self::$_addons;

        $tmp_addons_actions = array();

        /*
        pending to add cache*/
        // loop addons
        foreach ( $tmp_addons as $key => $value) {
            // loop controllers
            foreach ( $value as $key2 => $value2) {
                $tmp_flag = array();
                $tmp_flag = $value2->local_actions;

                if ( ! empty($tmp_flag)) {
                    foreach ( $tmp_flag as $key3 => $value3) {
                        $tmp_addons_actions[ $value3['action'] ][ $value3['priority'] ][ $key ] = array(
                            'function'      => $value3['function'],
                            'accepted_args' => $value3['accepted_args'],
                            'controller'    => $key2,

                        );
                    }
                }
            }
        }

        self::$_addons_actions = $tmp_addons_actions;

        // add js actions
        $tmp_addons_actions = array();

        /*
        pending to add cache*/
        // loop addons
        foreach ( $tmp_addons as $key => $value) {
            // loop controllers
            foreach ( $value as $key2 => $value2) {
                $tmp_flag = array();
                $tmp_flag = $value2->js_actions;

                if ( ! empty($tmp_flag)) {
                    foreach ( $tmp_flag as $key3 => $value3) {
                        $tmp_addons_actions[ $value3['action'] ][ $value3['priority'] ][ $key ] = array(
                            'function'      => $value3['function'],
                            'accepted_args' => $value3['accepted_args'],
                            'controller'    => $value3['controller'],

                        );
                    }
                }
            }
        }

        //self::$_addons_jsactions = $tmp_addons_actions;
    }

    public function addons_doActions($section = '')
    {

        if ( empty(self::$_addons_actions[ $section ])) {
            return '';
        }

        $tmp_addons = self::$_addons_actions[ $section ];

        $tmp_str = '';

        if ( ! empty($tmp_addons)) {
            foreach ( $tmp_addons as $key => $value) {
                foreach ( $value as $key2 => $value2) {
                    $tmp_str .= call_user_func(array( self::$_addons[ $key2 ][ $value2['controller'] ], $value2['function'] ));
                }
            }
        }

        return $tmp_str;
    }


    /**
     * Register callbacks for actions and filters
     *
     * @mvc Controller
     */
    public function register_hook_callbacks()
    {
    }

    /**
     * Initializes variables
     *
     * @mvc Controller
     */
    public function init()
    {

        try {
            // $instance_example = new WPPS_Instance_Class( 'Instance example', '42' );
            // add_notice('ba');
        } catch ( Exception $exception) {
            add_notice(__METHOD__ . ' error: ' . $exception->getMessage(), 'error');
        }
    }

    /*
     * Instance methods
     */

    /**
     * Prepares sites to use the plugin during single or network-wide activation
     *
     * @mvc Controller
     *
     * @param bool $network_wide
     */
    public function activate($network_wide)
    {

        return true;
    }

    /**
     * Rolls back activation procedures when de-activating the plugin
     *
     * @mvc Controller
     */
    public function deactivate()
    {
        return true;
    }

    /**
     * Checks if the plugin was recently updated and upgrades if necessary
     *
     * @mvc Controller
     *
     * @param string $db_version
     */
    public function upgrade($db_version = 0)
    {
        return true;
    }

    /**
     * Checks that the object is in a correct state
     *
     * @mvc Model
     *
     * @param string $property An individual property to check, or 'all' to check all of them
     * @return bool
     */
    protected function is_valid($property = 'all')
    {
        return true;
    }
}
