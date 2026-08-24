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
 * @link      https://softdiscover.com/zigaform/wordpress-form-builder/
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'No direct script access allowed' );
}

if ( ! function_exists( 'zgfm_cost_estimation_ensure_required_addon' ) ) {
	function zgfm_cost_estimation_ensure_required_addon( $wpdb ) {
		$tbname = $wpdb->prefix . 'cest_addon';

		if ( (string) $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $tbname ) ) !== $tbname ) {
			return;
		}

		$sql = "INSERT INTO $tbname (
			`add_name`,
			`add_title`,
			`add_info`,
			`add_system`,
			`add_hasconfig`,
			`add_version`,
			`add_icon`,
			`add_installed`,
			`add_order`,
			`add_params`,
			`add_log`,
			`addonscol`,
			`flag_status`,
			`created_date`,
			`updated_date`,
			`created_ip`,
			`updated_ip`,
			`created_by`,
			`updated_by`,
			`add_xml`,
			`add_load_back`,
			`add_load_front`,
			`is_field`
		) VALUES (
			'cost_estimation',
			'Cost Estimation',
			'Required calculation engine for the cost estimation form builder.',
			1,
			0,
			'1.0',
			NULL,
			1,
			99,
			'{\"locked\":true}',
			NULL,
			NULL,
			1,
			'2026-06-15 00:00:00',
			'2026-06-15 00:00:00',
			NULL,
			NULL,
			NULL,
			NULL,
			NULL,
			1,
			1,
			0
		) ON DUPLICATE KEY UPDATE
			`add_title` = VALUES(`add_title`),
			`add_info` = VALUES(`add_info`),
			`add_system` = 1,
			`add_hasconfig` = 0,
			`add_installed` = 1,
			`add_order` = 99,
			`add_params` = VALUES(`add_params`),
			`flag_status` = 1,
			`updated_date` = VALUES(`updated_date`),
			`add_load_back` = 1,
			`add_load_front` = 1,
			`is_field` = 0;";

		$wpdb->query( $sql );
	}
}

zgfm_cost_estimation_ensure_required_addon( $wpdb );

if (   version_compare( $version, $install_ver, '>' ) ) {

	if ( version_compare( $install_ver, '1.6', '<' ) ) {
		$tbname = $wpdb->prefix . 'cest_uiform_fields';

		if ( (string) $wpdb->get_var( "SHOW TABLES LIKE '$tbname'" ) === $tbname ) {

			$row = $wpdb->get_var( 'SHOW COLUMNS FROM ' . $tbname . " LIKE 'order_frm'" );


			if ( empty( $row ) ) {
				$sql = 'ALTER TABLE ' . $tbname . ' ADD  order_frm smallint(5) DEFAULT NULL;';
				$wpdb->query( $sql );
			}

			$row = $wpdb->get_var( 'SHOW COLUMNS FROM ' . $tbname . " LIKE 'order_rec'" );

			if ( empty( $row ) ) {
				$sql = 'ALTER TABLE ' . $tbname . ' ADD  order_rec smallint(5) DEFAULT NULL;';
				$wpdb->query( $sql );
			}
		}
	}

	if ( version_compare( $install_ver, '3', '<' ) ) {

		$tbname = $wpdb->prefix . 'cest_uiform_form_log';

		if ( (string) $wpdb->get_var( "SHOW TABLES LIKE '$tbname'" ) != $tbname ) {
				$charset = '';
				//form log
				$sql = "CREATE  TABLE IF NOT EXISTS $tbname (
                                `log_id` int(6) NOT NULL AUTO_INCREMENT,
                                `log_frm_data` longtext,
                                `log_frm_name` varchar(255) DEFAULT NULL,
                                `log_frm_html` longtext,
                                `log_frm_html_backend` longtext,
                                `log_frm_html_css` longtext,
                                `log_frm_id` int(6) NOT NULL,
                                `log_frm_hash` varchar(255) NOT NULL,
                                `flag_status` smallint(5) DEFAULT '1',
                                `created_date` timestamp NULL,
                                `updated_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                                `created_ip` varchar(100) DEFAULT NULL,
                                `updated_ip` varchar(100) DEFAULT NULL,
                                `created_by` varchar(100) DEFAULT NULL,
                                `updated_by` varchar(100) DEFAULT NULL,
                                PRIMARY KEY (`log_id`)
                            ) " . $charset . ';';


				$wpdb->query( $sql );

		}
	}

				//below 3.3.2
	if ( version_compare( $install_ver, '3.4', '<' ) ) {
		$tbname = $wpdb->prefix . 'cest_uiform_form_records';

		if ( (string) $wpdb->get_var( "SHOW TABLES LIKE '$tbname'" ) === $tbname ) {

			$row = $wpdb->get_var( 'SHOW COLUMNS FROM ' . $tbname . " LIKE 'fbh_data2'" );


			if ( empty( $row ) ) {
				$sql = 'ALTER TABLE ' . $tbname . ' ADD  fbh_data2 longtext;';
				$wpdb->query( $sql );
			}

			$row = $wpdb->get_var( 'SHOW COLUMNS FROM ' . $tbname . " LIKE 'fbh_data_rec2'" );

			if ( empty( $row ) ) {
				$sql = 'ALTER TABLE ' . $tbname . ' ADD  fbh_data_rec2 longtext;';
				$wpdb->query( $sql );
			}


			$row = $wpdb->get_var( 'SHOW COLUMNS FROM ' . $tbname . " LIKE 'fbh_data_rec2_xml'" );

			if ( empty( $row ) ) {
				$sql = 'ALTER TABLE ' . $tbname . ' ADD  fbh_data_rec2_xml longtext;';
				$wpdb->query( $sql );
			}
		}
	}


				//below 3.4.3
	if ( version_compare( $install_ver, '3.4.3', '<' ) ) {

		$tbname = $wpdb->prefix . 'cest_uiform_form';
		if ( (string) $wpdb->get_var( "SHOW TABLES LIKE '$tbname'" ) === $tbname ) {

			$row = $wpdb->get_var( 'SHOW COLUMNS FROM ' . $tbname . " LIKE 'fmb_data2'" );

			if ( ! empty( $row ) ) {
				$sql = 'ALTER TABLE ' . $tbname . ' MODIFY  fmb_data2 longtext;';
				$wpdb->query( $sql );
			}
		}

		$tbname = $wpdb->prefix . 'cest_uiform_fields';
		if ( (string) $wpdb->get_var( "SHOW TABLES LIKE '$tbname'" ) === $tbname ) {

			$row = $wpdb->get_var( 'SHOW COLUMNS FROM ' . $tbname . " LIKE 'fmf_data'" );

			if ( ! empty( $row ) ) {
				$sql = 'ALTER TABLE ' . $tbname . ' MODIFY  fmf_data longtext;';
				$wpdb->query( $sql );
			}
		}

		$tbname = $wpdb->prefix . 'cest_uiform_form_records';
		if ( (string) $wpdb->get_var( "SHOW TABLES LIKE '$tbname'" ) === $tbname ) {

			$row = $wpdb->get_var( 'SHOW COLUMNS FROM ' . $tbname . " LIKE 'fbh_error'" );

			if ( ! empty( $row ) ) {
				$sql = 'ALTER TABLE ' . $tbname . ' MODIFY  fbh_error longtext;';
				$wpdb->query( $sql );
			}
		}

		$tbname = $wpdb->prefix . 'cest_uiform_pay_logs';
		if ( (string) $wpdb->get_var( "SHOW TABLES LIKE '$tbname'" ) === $tbname ) {

			$row = $wpdb->get_var( 'SHOW COLUMNS FROM ' . $tbname . " LIKE 'pgl_message'" );

			if ( ! empty( $row ) ) {
				$sql = 'ALTER TABLE ' . $tbname . ' MODIFY  pgl_message longtext;';
				$wpdb->query( $sql );
			}
		}
	}

				 //below 3.7
	if ( version_compare( $install_ver, '3.7', '<' ) ) {

		$charset = '';
		if ( $wpdb->has_cap( 'collation' ) ) {
			if ( ! empty( $wpdb->charset ) ) {
				$charset = "DEFAULT CHARACTER SET $wpdb->charset";
			}
			if ( ! empty( $wpdb->collate ) ) {
				$charset .= " COLLATE $wpdb->collate";
			}
		}


		$tbname = $wpdb->prefix . 'cest_addon';

		if ( (string) $wpdb->get_var( "SHOW TABLES LIKE '$tbname'" ) != $tbname ) {

			 //addon
			$sql = "CREATE  TABLE IF NOT EXISTS $tbname (
                            `add_name` varchar(45) NOT NULL DEFAULT '',
                            `add_title` text ,
                            `add_info` text ,
                            `add_system` smallint(5) DEFAULT NULL,
                            `add_hasconfig` smallint(5) DEFAULT NULL,
                            `add_version` varchar(45)  DEFAULT NULL,
                            `add_icon` text ,
                            `add_installed` smallint(5) DEFAULT NULL,
                            `add_order` int(5) DEFAULT NULL,
                            `add_params` text ,
                            `add_log` text ,
                            `addonscol` varchar(45) DEFAULT NULL,
                            `flag_status` smallint(5)  DEFAULT 1,
                            `created_date` timestamp NULL,
                            `updated_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                            `created_ip` varchar(100)  DEFAULT NULL,
                            `updated_ip` varchar(100)  DEFAULT NULL,
                            `created_by` varchar(100) DEFAULT NULL,
                            `updated_by` varchar(100) DEFAULT NULL,
                            `add_xml` text ,
                            `add_load_back` smallint(5) DEFAULT NULL,
                            `add_load_front` smallint(5) DEFAULT NULL,
                            `is_field` smallint(5) DEFAULT NULL,
                            PRIMARY KEY (`add_name`) 
                        ) " . $charset . ';';

			 $wpdb->query( $sql );

			if ( ZIGAFORM_C_LITE != 1 ) {
					 $sql = "INSERT INTO $tbname VALUES ('func_anim', 'Animation effect', 'Animation effects to fields', 1, 1, NULL, NULL, NULL, 1, NULL, NULL, NULL, 1, '1980-01-01 00:00:01', '2018-01-31 10:35:14', NULL, NULL, NULL, NULL, NULL, 1, 1, 1);";
					 $wpdb->query( $sql );
			}
		}

		$tbname = $wpdb->prefix . 'cest_addon_details';

		if ( (string) $wpdb->get_var( "SHOW TABLES LIKE '$tbname'" ) != $tbname ) {
			  //addon detail
				$sql = "CREATE  TABLE IF NOT EXISTS $tbname (
                                `add_name` varchar(45)  NOT NULL,
                                `fmb_id` int(5) NOT NULL,
                                `adet_data` longtext ,
                                `flag_status` smallint(5) DEFAULT 1,
                                `created_date` timestamp NULL,
                                `updated_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                                `created_ip` varchar(100) DEFAULT NULL,
                                `updated_ip` varchar(100) DEFAULT NULL,
                                `created_by` varchar(100) DEFAULT NULL,
                                `updated_by` varchar(100) DEFAULT NULL,
                                PRIMARY KEY (`add_name`, `fmb_id`) 
                            ) " . $charset . ';';

				 $wpdb->query( $sql );

		}

		$tbname = $wpdb->prefix . 'cest_addon_details_log';

		if ( (string) $wpdb->get_var( "SHOW TABLES LIKE '$tbname'" ) != $tbname ) {

			//addon log
			$sql = "CREATE  TABLE IF NOT EXISTS $tbname (
                            `add_log_id` int(5) NOT NULL AUTO_INCREMENT,
                            `add_name` varchar(45)  NOT NULL,
                            `fmb_id` int(5) NOT NULL,
                            `adet_data` longtext  NULL,
                            `flag_status` smallint(5) DEFAULT 1,
                            `created_date` timestamp NULL,
                            `updated_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                            `created_ip` varchar(100) DEFAULT NULL,
                            `updated_ip` varchar(100) DEFAULT NULL,
                            `created_by` varchar(100) DEFAULT NULL,
                            `updated_by` varchar(100) DEFAULT NULL,
                            `log_id` int(5) NOT NULL,
                            PRIMARY KEY (`add_log_id`) 
                        ) " . $charset . ';';

			 $wpdb->query( $sql );
		}
	}

				//below 3.7.6.3
	if ( version_compare( $install_ver, '3.7.6.3', '<' ) ) {

		 $tbname = $wpdb->prefix . 'cest_addon';

		if ( (string) $wpdb->get_var( "SHOW TABLES LIKE '$tbname'" ) === $tbname ) {

			try {
				$row = $wpdb->get_var( 'SHOW COLUMNS FROM ' . $tbname . " LIKE 'add_id'" );
			} catch ( Exception $e ) {
				$row = array();
			}

			if ( ! empty( $row ) ) {
				$sql = 'ALTER TABLE ' . $tbname . " DROP COLUMN 'add_id';";
				$wpdb->query( $sql );
			}
		}
	}

						//below 3.7
	if ( version_compare( $install_ver, '3.9.5', '<' ) ) {


		$tbname = $wpdb->prefix . 'cest_uiform_fields_type';

		if ( (string) $wpdb->get_var( "SHOW TABLES LIKE '$tbname'" ) == $tbname ) {

			 $sql = "INSERT INTO $tbname VALUES ('43', 'Date 2', '1', '1980-01-01 00:00:01', '2018-10-11 14:10:35', NULL, NULL, NULL, NULL) ON DUPLICATE KEY UPDATE flag_status = 1;";
			 $wpdb->query( $sql );

		}
	}

						  //below 3.9.9.6.1
	if ( version_compare( $install_ver, '3.9.9.6.1', '<' ) ) {
		$tbname = $wpdb->prefix . 'cest_uiform_form';

		if ( (string) $wpdb->get_var( "SHOW TABLES LIKE '$tbname'" ) === $tbname ) {

						$row = $wpdb->get_var( 'SHOW COLUMNS FROM ' . $tbname . " LIKE 'fmb_rec_tpl_html'" );
			if ( empty( $row ) ) {
				$sql = 'ALTER TABLE ' . $tbname . ' ADD  fmb_rec_tpl_html longtext NULL;';
				$wpdb->query( $sql );
			}

						$row = $wpdb->get_var( 'SHOW COLUMNS FROM ' . $tbname . " LIKE 'fmb_inv_tpl_html'" );
			if ( empty( $row ) ) {
				$sql = 'ALTER TABLE ' . $tbname . ' ADD  fmb_inv_tpl_html longtext NULL;';
				$wpdb->query( $sql );
			}

						$row = $wpdb->get_var( 'SHOW COLUMNS FROM ' . $tbname . " LIKE 'fmb_rec_tpl_st'" );
			if ( empty( $row ) ) {
				$sql = 'ALTER TABLE ' . $tbname . ' ADD  fmb_rec_tpl_st TINYINT(1) NULL DEFAULT 0;';
				$wpdb->query( $sql );
			}

						$row = $wpdb->get_var( 'SHOW COLUMNS FROM ' . $tbname . " LIKE 'fmb_inv_tpl_st'" );
			if ( empty( $row ) ) {
				$sql = 'ALTER TABLE ' . $tbname . ' ADD  fmb_inv_tpl_st TINYINT(1) NULL DEFAULT 0;';
				$wpdb->query( $sql );
			}
		}
	}
	
	if ( version_compare( $install_ver, '5.0.5', '<' ) ) {
		  // insert data
		  $tbname = $wpdb->prefix . 'cest_addon';
		  $uifm_check_total = $wpdb->get_row( 'SELECT COUNT(*) AS total FROM ' . $tbname . " where add_name='mgtranslate'", ARRAY_A );
		  if ( isset( $uifm_check_total['total'] ) && intval( $uifm_check_total['total'] ) === 0 ) {
			  $sql = "INSERT INTO $tbname VALUES ('mgtranslate', 'Translation Manager Add-on', 'Translate any text on zigaform, and add new language', 1, 1, '1.0', NULL, 0, 4, '{\"required_wp\":5.0,\"required_php\":7.2}', NULL, NULL, 0, '2020-09-26 12:13:06', '2020-09-26 12:12:40', NULL, NULL, NULL, NULL, '<?xml version=\"1.0\"?> <params><required_wp>5.0</required_wp><required_php>7.2</required_php></params>', 1, 0, 0);";
			   $wpdb->query( $sql );
		  }
	}
	
	if (   version_compare( $install_ver, '7.0.0', '<' ) ) {
		$tbname = $wpdb->prefix . 'cest_uiform_form';

		if ( (string) $wpdb->get_var( "SHOW TABLES LIKE '$tbname'" ) === $tbname ) {
 

			$row = $wpdb->get_var( 'SHOW COLUMNS FROM ' . $tbname . " LIKE 'fmb_type'" );
			if ( empty( $row ) ) {
				  $sql = 'ALTER TABLE ' . $tbname . ' ADD  fmb_type TINYINT(1) NULL DEFAULT 0;';
				  $wpdb->query( $sql );
				  $sql = 'ALTER TABLE ' . $tbname . ' ADD  fmb_parent BIGINT DEFAULT 0;';
				  $wpdb->query( $sql );
			}
		}
		
		
		$tbname = $wpdb->prefix . 'cest_uiform_form_log';

		if ( (string) $wpdb->get_var( "SHOW TABLES LIKE '$tbname'" ) === $tbname ) {
 

			$row = $wpdb->get_var( 'SHOW COLUMNS FROM ' . $tbname . " LIKE 'log_frm_parent'" );
			if ( empty( $row ) ) {
				  $sql = 'ALTER TABLE ' . $tbname . ' ADD  log_frm_parent BIGINT DEFAULT 0;';
				  $wpdb->query( $sql );
			}
		}
  }
	zgfm_cost_estimation_ensure_required_addon( $wpdb );
				 update_option( 'uifmcostest_version', $version );
}
