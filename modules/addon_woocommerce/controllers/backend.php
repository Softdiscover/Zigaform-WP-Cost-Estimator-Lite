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
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'No direct script access allowed' );
}
if ( class_exists( 'zfaddn_woocommerce_back' ) ) {
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
 * @link      http://wordpress-cost-estimator.zigaform.com
 */
class zfaddn_woocommerce_back extends Uiform_Base_Module {

	const VERSION       = '0.1';
	private $pagination = '';
	var $per_page       = 5;
	private $wpdb       = '';
	protected $modules;
	public $model_addon_details;

	// adding libs
	public $local_controllers = array();

	// adding routes
	public $local_back_actions = array();


		// adding js actions
	public $js_back_actions = array();

	/**
	 * Constructor
	 *
	 * @mvc Controller
	 */
	protected function __construct() {
		global $wpdb;
		$this->wpdb = $wpdb;
		$this->model_addon_details = self::$_models['addon']['addon_details'];
		// admin resources
		add_action( 'admin_enqueue_scripts', array( &$this, 'load_dependencies' ), 20, 1 );

		add_filter( 'zgfm_saveForm_store', array( &$this, 'saveData' ), 10, 2 );

		//load options
		add_action( 'wp_ajax_zgfm_back_woocommerce_load_settings', array( &$this, 'ajax_load_settings' ) );
		// add content to extension tab
		add_filter( 'zgfm_back_exttab_block', array( &$this, 'back_exttab_block' ) );
	}

	/**
	   * Add data to filter
	   */
	public function back_exttab_block( $filter_data ) {
		// load form variables
		$filter_data[] = $this->get_content();
		return $filter_data;
	}

	public function ajax_load_settings() {
		check_ajax_referer( 'zgfm_ajax_nonce', 'zgfm_security' );

		$json      = array();
		$tmp_addon = array();
		$form_id   = ( isset( $_POST['form_id'] ) ) ? Uiform_Form_Helper::sanitizeInput( trim( $_POST['form_id'] ) ) : '';

		$tmp_data = $this->model_addon_details->getAddonDataByForm( 'woocommerce', $form_id );
		if ( ! empty( $tmp_data ) ) {
			$tmp_addon = json_decode( $tmp_data->adet_data, true );
		} else {
			$tmp_addon = array();
		}
		$json['data'] = $tmp_addon;
		  //return data to ajax callback
		  header( 'Content-Type: application/json' );
		  echo json_encode( $json );
		  wp_die();
	}

	public function saveData( $fmb_data, $form_id ) {
		if ( ! isset( $fmb_data['addons']['woocommerce'] ) ) {
			return $fmb_data;
		}
		$data_addon = $fmb_data['addons']['woocommerce'];

		$data_addon_store = json_encode( $data_addon );

		$newdata = array();

		if ( self::$_models['addon']['addon_details']->existRecord( 'woocommerce', $form_id ) ) {

			$where    = array(
				'add_name' => 'woocommerce',
				'fmb_id'   => $form_id,
			);
				$data = array(
					'adet_data' => $data_addon_store,
				);
				$this->wpdb->update( self::$_models['addon']['addon_details']->table, $data, $where );

		} else {

			$newdata['add_name']  = 'woocommerce';
			$newdata['fmb_id']    = $form_id;
			$newdata['adet_data'] = $data_addon_store;

			$this->wpdb->insert( self::$_models['addon']['addon_details']->table, $newdata );
		}
		return $fmb_data;
	}


	/*
	* load css, and javascript files
	*/
	public function load_dependencies() {
		// css
		wp_enqueue_style( 'zgfm-woocommerce-style', UIFORM_FORMS_URL . '/modules/addon_woocommerce/views/backend/assets/style.css' );
		// load
		 wp_enqueue_script( 'zgfm_back_woocommerce_js', UIFORM_FORMS_URL . '/modules/addon_woocommerce/views/backend/assets/back.js', array( 'wp-i18n', 'wp-hooks' ), ( UIFORM_DEBUG ) ? date( 'YmdHis' ) : 1, true );
	}

	public function get_content() {
		  $data = array();

		$output                = array();
		$output['tab_link']    = array( 'name' => 'woocommerce settings' );
		$output['tab_content'] = self::render_template( 'addon_woocommerce/views/backend/get_content.php', $data );

		return $output;
	}

	/**
	 * Adding new controllers
	 *
	 * @mvc Controller
	 */
	public function add_controllers() {

		$tmp_flag = array();

		return $tmp_flag;
	}



	/**
	 * Register callbacks for actions and filters
	 *
	 * @mvc Controller
	 */
	public function register_hook_callbacks() {

	}

	/**
	 * Initializes variables
	 *
	 * @mvc Controller
	 */
	public function init() {

		try {
			// $instance_example = new WPPS_Instance_Class( 'Instance example', '42' );
			// add_notice('ba');
		} catch ( Exception $exception ) {
			add_notice( __METHOD__ . ' error: ' . $exception->getMessage(), 'error' );
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
	public function activate( $network_wide ) {

		return true;
	}

	/**
	 * Rolls back activation procedures when de-activating the plugin
	 *
	 * @mvc Controller
	 */
	public function deactivate() {
		return true;
	}

	/**
	 * Checks if the plugin was recently updated and upgrades if necessary
	 *
	 * @mvc Controller
	 *
	 * @param string $db_version
	 */
	public function upgrade( $db_version = 0 ) {
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
	protected function is_valid( $property = 'all' ) {
		return true;
	}

}


