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
if ( class_exists('zgfm_mod_addon_controller_back')) {
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
 * @link      https://wordpress-cost-estimator.zigaform.com
 */
class zgfm_mod_addon_controller_back extends Uiform_Base_Module
{

    const VERSION       = '0.1';
    private $pagination = '';
    private $per_page       = 100;
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

        // admin resources
        add_action('admin_enqueue_scripts', array( &$this, 'loadStyle' ), 20, 1);

        //add_filter( 'zgfm_back_filter_globalvars', array( &$this, 'filter_add_globalvariable' ) );

        // ajax for saving form
        add_action('wp_ajax_rocket_fbuilder_addon_status', array( &$this, 'listaddon_updateStatus' ));
    }

    /*
     * update addon status
     */
    public function listaddon_updateStatus()
    {
        check_ajax_referer('zgfm_ajax_nonce', 'zgfm_security');

        $data       = array();
        $add_name   = ( $_POST['add_name'] ) ? Uiform_Form_Helper::sanitizeInput(trim($_POST['add_name'])) : '';
        $add_status = ( $_POST['add_status'] ) ? Uiform_Form_Helper::sanitizeInput(trim($_POST['add_status'])) : '0';

        $data['flag_status'] = $add_status;
        $json                = array();
        if ( $this->model_addon->existAddon($add_name)) {
            $where = array(
                'add_name' => $add_name,
            );
            $this->wpdb->update($this->model_addon->table, $data, $where);
        }

        $json['status'] = 'updated';

        // return data to ajax callback
        header('Content-Type: application/json');
        echo json_encode($json);
        wp_die();
    }

    public function filter_add_globalvariable($value)
    {
        return $value;
    }

    /*
    * Show extensions
    */
    public function list_extensions()
    {
        global $wp_version;
        $data          = array();
        $data['query'] = $this->model_addon->getListAddons(100, 0);
        $data['wp_version'] = $wp_version;
        echo self::loadPartial('layout.php', 'addon/views/backend/list_extensions.php', $data);
    }


    public function loadStyle()
    {
        // load
        wp_enqueue_script('zgfm_back_addon_js', UIFORM_FORMS_URL . '/modules/addon/views/backend/assets/back-addon.js', array(), ( UIFORM_DEBUG ) ? date('YmdHis') : 1, true);

        wp_enqueue_style('zgfm_back_addon_css', UIFORM_FORMS_URL . '/modules/addon/views/backend/assets/back-addon.css');
    }

    public function load_addonsbyBack()
    {

        // get addons
        $tmp_addons = $this->model_addon->getListAddonsByBack();

        // flag variables
        $tmp_addons_arr  = array();
        $tmp_modules_arr = self::$_addons;

        // storing lib objects
        foreach ( $tmp_addons as $key => $value) {
            // load addons
            require_once UIFORM_FORMS_DIR . '/modules/addon_' . $value->add_name . '/controllers/backend.php';
            call_user_func(array( 'zfaddn_' . $value->add_name . '_back', 'get_instance' ));
        }
    }

    public function load_addActions()
    {
    }



    public function addons_doActions($section = '', $return_array = false)
    {
    }

    public function get_addon_content($addon_name)
    {

        // return 'here loading content of addon '.$addon_name;
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
            ////add_notice('ba');
        } catch ( Exception $exception) {
           //add_notice(__METHOD__ . ' error: ' . $exception->getMessage(), 'error');
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
