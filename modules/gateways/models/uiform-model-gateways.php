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
if ( class_exists( 'Uiform_Model_Gateways' ) ) {
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
class Uiform_Model_Gateways {

	private $wpdb = '';
	public $table = '';

	function __construct() {
		global $wpdb;
		$this->wpdb  = $wpdb;
		$this->table = $wpdb->prefix . 'cest_uiform_pay_gateways';
	}

	/**
	 * formsmodel::getListGateways()
	 * List Gateways
	 *
	 * @param int $per_page max number of form estimators
	 * @param int $segment  Number of pagination
	 *
	 * @return array
	 */
	function getListGateways() {
		$query = sprintf(
			'
            select c.pg_id,c.pg_name,c.pg_modtest,c.pg_data,c.flag_status,c.pg_order,c.pg_description
            from %s c
            where c.flag_status>=0 
            ',
			$this->table
		);

		return $this->wpdb->get_results( $query );
	}

	function getAvailableGateways() {
		$query = sprintf(
			'
            select c.pg_id,c.pg_name,c.pg_modtest,c.pg_data,c.flag_status,c.pg_order,c.pg_description
            from %s c
            where c.flag_status=1
            ORDER BY c.pg_order asc
            ',
			$this->table
		);

		return $this->wpdb->get_results( $query );
	}

	function getGatewayById( $id ) {
		$query = sprintf(
			'
            select c.pg_id,c.pg_name,c.pg_modtest,c.pg_data,c.flag_status,c.pg_order,c.pg_description
            from %s c
            where c.pg_id=%s
            ',
			$this->table,
			$id
		);

		return $this->wpdb->get_row( $query );
	}

}


