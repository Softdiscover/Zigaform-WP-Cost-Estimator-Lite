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
if ( class_exists( 'Uiform_Model_Gateways_Logs' ) ) {
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
class Uiform_Model_Gateways_Logs {

	private $wpdb = '';
	public $table = '';

	function __construct() {
		global $wpdb;
		$this->wpdb  = $wpdb;
		$this->table = $wpdb->prefix . 'cest_uiform_pay_logs';
		$this->tbpay_record  = $wpdb->prefix . 'cest_uiform_pay_records';
		$this->tbform_record = $wpdb->prefix . 'cest_uiform_form_records';
		$this->tbform        = $wpdb->prefix . 'cest_uiform_form';
	}

	/**
	 * delete payment records by form id
	 *
	 * @param [type] $form_id
	 * @return void
	 */
	function deleteRecordbyFormId( $form_id ) {

		$query = sprintf(
			'
            DELETE from %s where pgr_id IN (
				select pgr_id from %s where pgr_id IN (
				select fbh_id from %s where form_fmb_id=%s
				)
				);
            ',
			$this->table,
			$this->tbpay_record,
			$this->tbform_record,
			$form_id
		);

		$this->wpdb->query( $query );
	}


}


