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
if ( class_exists( 'zfaddn_woocommerce_front' ) ) {
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
class zfaddn_woocommerce_front extends Uiform_Base_Module {

	const VERSION = '0.1';

	private $pagination = '';
	var $per_page       = 5;
	private $wpdb       = '';
	protected $modules;
	private $model_addon_details;
	private $model_formrecords;
	private $model_fields;
	private $model_form;
	private $model_addon_woo;
	// adding libs
	public $local_controllers = array();
	// adding actions
	public $local_actions = array();
	// adding js actions
	public $js_actions = array();

	/**
	 * Constructor
	 *
	 * @mvc Controller
	 */
	protected function __construct() {
		global $wpdb;
		$this->wpdb                = $wpdb;
		$this->model_addon_details = self::$_models['addon']['addon_details'];
		$this->model_form          = self::$_models['formbuilder']['form'];
		$this->model_formrecords   = self::$_models['formbuilder']['form_records'];
		$this->model_fields        = self::$_models['formbuilder']['fields'];

		//check if woocommerce constant is active
		if ( ! defined( 'WC_VERSION' ) ) {
			return;
		}

		// actions and filters
		add_action( 'woocommerce_before_calculate_totals', array( &$this, 'wc_before_calculate_totals' ), 99 );

		add_action( 'woocommerce_add_cart_item_data', array( &$this, 'wc_add_cart_item_data' ), 10, 2 );
		add_filter( 'woocommerce_get_item_data', array( &$this, 'wc_get_item_data' ), 10, 2 );

		if ( version_compare( WC_VERSION, '3.2.6' ) >= 0 ) {
			add_action( 'woocommerce_checkout_create_order_line_item', array( &$this, 'wc_checkout_create_order_line_item' ), 10, 4 );
			add_action( 'woocommerce_new_order_item', array( &$this, 'wc_add_order_item_meta' ), 10, 3 );
		} else {
			add_action( 'woocommerce_add_order_item_meta', array( &$this, 'wc_add_order_item_meta' ), 10, 3 );
		}
		add_action( 'woocommerce_order_item_get_formatted_meta_data', array( &$this, 'wc_order_item_get_formatted_meta_data' ), 10, 2 );

		add_action( 'woocommerce_checkout_order_processed', array( &$this, 'wc_checkout_order_processed' ), 10, 2 );
		add_action( 'woocommerce_before_cart_item_quantity_zero', array( &$this, 'wc_before_cart_item_quantity_zero' ), 10, 1 );

		// woocommerce custom queries
		require_once UIFORM_FORMS_DIR . '/modules/addon_woocommerce/models/model-woocommerce.php';
		$this->model_addon_woo = new zfaddn_woocommerce_model();

		//save on submit form
		add_action( 'zgfm_onSubmitForm_pos', array( &$this, 'submit_data' ), 10, 2 );

	}

	/*
	 * wc_add_cart_item_data
	 */

	public function wc_add_cart_item_data( $cart_item_data, $product_id ) {
		if ( isset( $_POST['_rockfm_form_id'] ) ) {
			$cart_item_data['zgfm_custom_data'] = '';
			/* below statement make sure every add to cart action as unique line item */
			$cart_item_data['unique_key'] = md5( microtime() . rand() );
		}
		return $cart_item_data;
	}

	/*
	 * wc before cart item
	 */

	public function wc_before_cart_item_quantity_zero( $cart_item_key ) {
		return;
		$cart = WC()->cart->get_cart();
		foreach ( $cart as $key => $values ) {
			/*
			 if ($values['sasdf'] == $cart_item_key)
			  unset($woocommerce->cart->cart_contents[$key]); */
		}
	}

	/**
	 * make actions after checkout
	 */
	public function wc_checkout_order_processed( $order_id ) {
		$order = new WC_Order( $order_id );
		if ( empty( $order ) ) {
			return;
		}
		$items = $order->get_items();
	}

	/*
	 * wc order item get formatted meta data
	 */

	public function wc_order_item_get_formatted_meta_data( $formatted_meta, $order ) {
		$keys = array( 'zgfm_cart_prod_key', 'zgfm_form_id', 'zgfm_total', 'zgfm_summ_status', 'zgfm_rec_id', 'zgfm_summ_title', 'zgfm_summ_content' );

		foreach ( $formatted_meta as $id => $meta ) {
			if ( in_array( $meta->key, $keys ) ) {
				unset( $formatted_meta[ $id ] );
			}
		}

		return $formatted_meta;
	}

	/*
	 * add order item meta
	 */

	public function wc_add_order_item_meta( $item_id, $values, $cart_item_key ) {
		if ( ! empty( $values['zgfm_summ_content'] ) ) {
			wc_add_order_item_meta( $item_id, 'zgfm_form_id', $values['zgfm_form_id'] );
			wc_add_order_item_meta( $item_id, $values['zgfm_summ_title'], $values['zgfm_summ_content'] );
		}
	}

	/**
	 * wc create order line
	 */
	public function wc_checkout_create_order_line_item( $item, $cart_item_key, $values, $order ) {
		if ( ! empty( $values['zgfm_form_id'] ) ) {
			$item->add_meta_data( 'zgfm_cart_prod_key', $values['zgfm_cart_prod_key'], true );
			$item->add_meta_data( 'zgfm_form_id', $values['zgfm_form_id'], true );
			$item->add_meta_data( 'zgfm_rec_id', $values['zgfm_rec_id'], true );
			$item->add_meta_data( 'zgfm_total', $values['zgfm_total'], true );
			$item->add_meta_data( 'zgfm_summ_status', $values['zgfm_summ_status'], true );
			if ( isset( $values['zgfm_summ_content'] ) ) {
				$item->add_meta_data( 'zgfm_summ_content', $values['zgfm_summ_content'], true );
			}
			if ( isset( $values['zgfm_summ_title'] ) ) {
				$item->add_meta_data( 'zgfm_summ_title', $values['zgfm_summ_title'], true );
			}
		}
	}

	/**
	 * wc get item data
	 */
	public function wc_get_item_data( $cart_data, $cart_item ) {
		$custom_items = array();

		if ( ! empty( $cart_data ) ) {
			$custom_items = $cart_data;
		}

		if ( isset( $cart_item['zgfm_summ_status'] ) && intval( $cart_item['zgfm_summ_status'] ) === 1 ) {
			$custom_items[] = array(
				'name'  => $cart_item['zgfm_summ_title'],
				'value' => $cart_item['zgfm_summ_content'],
			);
		}
		return $custom_items;
	}

	/**
	 * woocommerce get totals
	 */
	public function wc_before_calculate_totals( $cart_obj ) {
		if ( ! WC()->session->__isset( 'reload_checkout' ) ) {
			foreach ( $cart_obj->cart_contents as $key => $value ) {

				if ( isset( $value['zgfm_total'] ) ) {
					$value['data']->set_price( $value['zgfm_total'] );
					/*
					  if ($value["zgfm_custom_title"] != '') {
					  $value['data']->set_name($value["zgfm_custom_title"]);
					  } */
				}
			}
		}
	}

	public function loadStyleOnFront() {

	}

	/*
	 * sending info
	 */

	public function submit_data( $form_id, $record_id ) {

		try {

			$form_id = self::$_form_data['form_id'];
			$rec_id  = self::$_form_data['record_id'];
			$form_data = $this->model_addon_woo->getData( $form_id, $rec_id );

			$send_data              = array();
			$send_data['form_id']   = $form_id;
			$send_data['form_name'] = $form_data->fmb_name;

			$addon_data = $form_data->adet_data;

			// return if data is null
			if ( empty( $addon_data ) ) {
				return;
			}

			$addon_data_tmp = json_decode( $addon_data, true );

			// return if status is zero
			if ( isset( $addon_data_tmp['status'] ) && intval( $addon_data_tmp['status'] ) != 1 ) {
				return;
			}

			// product id is empty
			if ( empty( $addon_data_tmp['prod_id'] ) ) {
				return;
			}

			$quantity = 1;
			$total    = $form_data->pgr_payment_amount;
			if ( ! empty( $addon_data_tmp['wc_quantity'] ) && intval( $addon_data_tmp['wc_quantity'] ) != 1 ) {
				$quantity_field = $this->model_fields->getFieldNameByUniqueId( $addon_data_tmp['wc_quantity'], $form_id );

				$quantity = $this->model_formrecords->getFieldOptRecord( $rec_id, $quantity_field->type, $addon_data_tmp['wc_quantity'], 'input', 'value' );

				$quantity = ( intval( $quantity ) >= 1 ) ? $quantity : 1;
				if ( intval( $quantity ) >= 1 ) {
					$total = floatval( $form_data->pgr_payment_amount ) / floatval( $quantity );
				}
			}

			if ( ! function_exists( 'WC' ) ) {
				return;
			}
			// check active session
			if ( ! WC()->session->has_session() ) {
				WC()->session->set_customer_session_cookie( true );

				$this->debug_issue( 'session issue' );

			}

			// get product id
			$prod_id = $addon_data_tmp['prod_id'];

			$woo_arr                       = array();
			$woo_arr['zgfm_cart_prod_key'] = md5( microtime( true ) );
			$woo_arr['zgfm_form_id']       = $form_id;
			$woo_arr['zgfm_rec_id']        = self::$_form_data['record_id'];
			$woo_arr['zgfm_total']         = $total;
			$woo_arr['zgfm_summ_status']   = $addon_data_tmp['summ_status'];

			if ( isset( $addon_data_tmp['summ_status'] ) && intval( $addon_data_tmp['summ_status'] ) === 1 ) {
				$woo_arr['zgfm_summ_content'] = do_shortcode( $addon_data_tmp['summ_content'] );
				$woo_arr['zgfm_summ_title']   = $addon_data_tmp['summ_title'];
			}

			//get product price
			$product_data = wc_get_product( $prod_id );
			$product_price = $product_data->get_price();
			if ( strval( $product_price ) == '' ) {
				 throw new Exception( __( 'Error! Produce price should be at least zero and not empty ', 'FRocket_admin' ) );
			}

			//adding to cart
			$wc_item_key = WC()->instance()->cart->add_to_cart( $prod_id, $quantity, 0, array(), $woo_arr );

			if ( ! $wc_item_key ) {
				throw new Exception( __( 'Error! form was not added to cart ', 'FRocket_admin' ) );
			}
		} catch ( Exception $e ) {
			echo $e->getMessage();
			die();
		}
	}


	public function debug_issue( $output ) {
		if ( get_option( 'zgfm_debug_status', 0 ) == 0 ) {
			return;
		}

		// insert debug to database
	}

	/*
	 * get value of field
	 */

	public function get_value_fields( $field ) {
		$output     = '';
		$record_id  = self::$_form_data['record_id'];
		$form_id    = self::$_form_data['form_id'];
		$field_id   = $field['field'];
		$field_type = $field['type'];
		$output     = $this->model_formrecords->getFieldOptRecord( $record_id, $field_type, $field_id, 'input' );
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


