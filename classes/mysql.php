<?php

if ( ! defined('ABSPATH')) {
    exit('No direct script access allowed');
}

// forms
        $sql = "CREATE  TABLE IF NOT EXISTS $this->form (
            `fmb_id` INT(10) NOT NULL AUTO_INCREMENT ,
            `fmb_data` longtext ,
            `fmb_name` VARCHAR(255) NULL ,
            `fmb_html` longtext NULL ,
            `fmb_html_backend` longtext NULL ,
            `flag_status` SMALLINT(5) DEFAULT '1',
            `created_date` TIMESTAMP NULL ,
            `updated_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,
            `created_ip` VARCHAR(100) NULL ,
            `updated_ip` VARCHAR(100) NULL ,
            `created_by` VARCHAR(100) NULL ,
            `updated_by` VARCHAR(100) NULL ,
            `fmb_html_css` longtext NULL ,
            `fmb_default` TINYINT(1) NULL DEFAULT 0 ,
            `fmb_skin_status` TINYINT(1) NULL DEFAULT 0 ,
            `fmb_skin_data` longtext NULL ,
            `fmb_skin_type` SMALLINT(5) NULL DEFAULT 1 ,
            `fmb_data2` longtext NULL ,
            `fmb_rec_tpl_html` longtext NULL ,
            `fmb_inv_tpl_html` longtext NULL ,
            `fmb_rec_tpl_st` TINYINT(1) NULL DEFAULT 0 ,
            `fmb_inv_tpl_st` TINYINT(1) NULL DEFAULT 0 ,
            `fmb_type` TINYINT(1) NULL DEFAULT 0 ,
			`fmb_parent` BIGINT DEFAULT 0 ,
            PRIMARY KEY (`fmb_id`) ) " . $charset . ';';
        $wpdb->query($sql);
        // form request statitistics

        $sql = "CREATE  TABLE IF NOT EXISTS $this->form_history (
                `fbh_id` int(10) NOT NULL AUTO_INCREMENT,
                `fbh_data` longtext,
                `fbh_data_rec` longtext,
                `fbh_data2` longtext,
                `fbh_data_rec2` longtext,
                `fbh_data_rec2_xml` longtext,
                `fbh_total_amount` varchar(45) DEFAULT NULL,
                `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `created_ip` varchar(100) DEFAULT NULL,
                `created_by` VARCHAR(100) DEFAULT NULL,
                `flag_status` smallint(5) DEFAULT '1',
                `fbh_data_user` longtext,
                `form_fmb_id` int(10) NOT NULL,
                `fbh_data_rec_xml` longtext,
                `fbh_user_agent` text,
                `fbh_page` longtext,
                `fbh_referer` longtext,
                `fbh_params` longtext,
                `vis_uniqueid` varchar(10) NOT NULL,
                `fbh_error` longtext,    
            PRIMARY KEY (`fbh_id`) ) " . $charset . ';';
        $wpdb->query($sql);
        // fields type
        $sql = "CREATE  TABLE IF NOT EXISTS $this->form_fields_type (
        `fby_id` INT(6) NOT NULL AUTO_INCREMENT ,
        `fby_name` VARCHAR(100) NULL ,
        `flag_status` SMALLINT(5) NULL ,
        `created_date` TIMESTAMP NULL ,
        `updated_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,
        `created_ip` VARCHAR(100) NULL ,
        `updated_ip` VARCHAR(100) NULL ,
        `created_by` VARCHAR(100) NULL ,
        `updated_by` VARCHAR(100) NULL ,
        PRIMARY KEY (`fby_id`) )" . $charset . ';';
        $wpdb->query($sql);
        // insert types
        $uifm_check_total = $wpdb->get_row('SELECT COUNT(*) AS total FROM ' . $this->form_fields_type, ARRAY_A);
if ( isset($uifm_check_total['total']) && intval($uifm_check_total['total']) === 0) {
    $sql = "INSERT INTO $this->form_fields_type VALUES 
        ('1', '1 Col', '1', '1980-01-01 00:00:01', '2014-05-24 01:10:27', null, null, null, null),
        ('2', '2 Cols', '1', '1980-01-01 00:00:01', '2014-05-24 01:10:44', null, null, null, null),
        ('3', '3 Cols', '1', '1980-01-01 00:00:01', '2014-05-24 01:10:57', null, null, null, null),
        ('4', '4 Cols', '1', '1980-01-01 00:00:01', '2014-05-24 01:11:36', null, null, null, null),
        ('5', '6 Cols', '1', '1980-01-01 00:00:01', '2014-05-24 01:11:45', null, null, null, null),
        ('6', 'Textbox', '1', '1980-01-01 00:00:01', '2014-05-24 01:11:58', null, null, null, null),
        ('7', 'Textarea', '1', '1980-01-01 00:00:01', '2014-05-24 01:12:12', null, null, null, null),
        ('8', 'Radio Button', '1', '1980-01-01 00:00:01', '2014-05-24 01:13:21', null, null, null, null),
        ('9', 'Checkbox', '1', '1980-01-01 00:00:01', '2014-05-24 01:13:33', null, null, null, null),
        ('10', 'Select', '1', '1980-01-01 00:00:01', '2014-05-24 01:13:44', null, null, null, null),
        ('11', 'Multiple Select', '1', '1980-01-01 00:00:01', '2014-05-24 01:13:57', null, null, null, null),
        ('12', 'File Upload', '1', '1980-01-01 00:00:01', '2014-05-24 01:28:55', null, null, null, null),
        ('13', 'Image Upload', '1', '1980-01-01 00:00:01', '2014-05-24 01:29:06', null, null, null, null),
        ('14', 'Custom HTML', '1', '1980-01-01 00:00:01', '2014-05-24 01:29:31', null, null, null, null),
        ('15', 'Password', '1', '1980-01-01 00:00:01', '2014-05-24 01:30:39', null, null, null, null),
        ('16', 'Slider', '1', '1980-01-01 00:00:01', '2014-05-24 01:30:53', null, null, null, null),
        ('17', 'Range', '1', '1980-01-01 00:00:01', '2014-05-24 01:35:41', null, null, null, null),
        ('18', 'Spinner', '1', '1980-01-01 00:00:01', '2014-05-24 01:37:09', null, null, null, null),
        ('19', 'Captcha', '1', '1980-01-01 00:00:01', '2014-05-24 01:37:19', null, null, null, null),
        ('20', 'Submit button', '1', '1980-01-01 00:00:01', '2014-05-24 01:39:59', null, null, null, null),
        ('21', 'Hidden field', '1', '1980-01-01 00:00:01', '2014-05-24 01:40:13', null, null, null, null),
        ('22', 'Star rating', '1', '1980-01-01 00:00:01', '2014-05-24 01:40:24', null, null, null, null),
        ('23', 'Color Picker', '1', '1980-01-01 00:00:01', '2014-05-24 01:40:37', null, null, null, null),
        ('24', 'Date Picker', '1', '1980-01-01 00:00:01', '2014-05-24 01:41:19', null, null, null, null),
        ('25', 'Time Picker', '1', '1980-01-01 00:00:01', '2014-05-24 01:41:46', null, null, null, null),
        ('26', 'Date and Time', '1', '1980-01-01 00:00:01', '2014-05-24 01:50:36', null, null, null, null),
        ('27', 'ReCaptcha', '1', '1980-01-01 00:00:01', '2014-05-24 01:50:53', null, null, null, null),
        ('28', 'Prepended text', '1', '1980-01-01 00:00:01', '2014-05-24 01:51:16', null, null, null, null),
        ('29', 'Appended text', '1', '1980-01-01 00:00:01', '2014-05-24 01:51:38', null, null, null, null),
        ('30', 'Append and prepend', '1', '1980-01-01 00:00:01', '2014-05-24 01:51:55', null, null, null, null),
        ('31', 'Panel', '1', '1980-01-01 00:00:01', '2014-05-24 01:55:32', null, null, null, null),
        ('32', 'Divider', '1', '1980-01-01 00:00:01', '2014-05-24 01:58:58', null, null, null, null),
        ('33', 'Heading 1', '1', '1980-01-01 00:00:01', '2014-05-24 02:25:51', null, null, null, null),
        ('34', 'Heading 2', '1', '1980-01-01 00:00:01', '2014-05-24 02:25:51', null, null, null, null),
        ('35', 'Heading 3', '1', '1980-01-01 00:00:01', '2014-05-24 02:25:51', null, null, null, null),
        ('36', 'Heading 4', '1', '1980-01-01 00:00:01', '2014-05-24 02:25:51', null, null, null, null),
        ('37', 'Heading 5', '1', '1980-01-01 00:00:01', '2014-05-24 02:25:51', null, null, null, null),
        ('38', 'Heading 6', '1', '1980-01-01 00:00:01', '2014-05-24 02:25:51', null, null, null, null),
        ('39', 'Wizard buttons', '1', '1980-01-01 00:00:01', '2014-05-24 02:25:51', null, null, null, null),
        ('40', 'Switch', '1', '1980-01-01 00:00:01', '2014-05-24 02:25:51', null, null, null, null),
        ('41', 'Dinamic Checkbox', '1', '1980-01-01 00:00:01', '2014-05-24 02:25:51', null, null, null, null),
        ('42', 'Dinamic RadioButton', '1', '1980-01-01 00:00:01', '2014-05-24 02:25:51', null, null, null, null),
        ('43', 'Date 2', '1', '1980-01-01 00:00:01', '2014-05-24 02:25:51', null, null, null, null);";
    $wpdb->query($sql);
}

        // fields
        $sql = "CREATE  TABLE IF NOT EXISTS $this->form_fields (
        `fmf_id` int(10) NOT NULL AUTO_INCREMENT,
        `fmf_uniqueid` varchar(255) DEFAULT NULL,
        `fmf_data` longtext NULL ,
        `fmf_fieldname` varchar(255) DEFAULT NULL,
        `flag_status` smallint(5) DEFAULT NULL,
        `created_date` timestamp NULL,
        `updated_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `created_ip` varchar(100) DEFAULT NULL,
        `updated_ip` varchar(100) DEFAULT NULL,
        `created_by` VARCHAR(100) NULL ,
        `updated_by` VARCHAR(100) NULL ,
        `fmf_status_qu` smallint(5) NOT NULL DEFAULT '0',
        `type_fby_id` int(6) NOT NULL,
        `form_fmb_id` int(10) NOT NULL,
        `order_frm` smallint(5) DEFAULT NULL,
        `order_rec` smallint(5) DEFAULT NULL, 
        PRIMARY KEY (`fmf_id`,`form_fmb_id`) ) " . $charset . ';';
        $wpdb->query($sql);

        // settings
        $sql = "CREATE  TABLE IF NOT EXISTS $this->settings (
        `version` varchar(10) DEFAULT NULL,
        `type_email` SMALLINT(1) NULL ,
        `smtp_host` VARCHAR(255) NULL ,
        `smtp_port` SMALLINT(6) NULL ,
        `smtp_user` VARCHAR(100) NULL ,
        `smtp_pass` VARCHAR(100) NULL ,
        `sendmail_path` VARCHAR(255) NULL ,
        `language` VARCHAR(45) NULL,
         `id` INT(1) NOT NULL AUTO_INCREMENT ,
         PRIMARY KEY (`id`)
        ) " . $charset . ';';

        $wpdb->query($sql);
        // insert data
        $uifm_check_total = $wpdb->get_row('SELECT COUNT(*) AS total FROM ' . $this->settings, ARRAY_A);
if ( isset($uifm_check_total['total']) && intval($uifm_check_total['total']) === 0) {
    $sql = "INSERT INTO $this->settings VALUES ('7.2.9', null, null, null, null, null, null, '', '1');";
    $wpdb->query($sql);
}

        // payment gateways
        $sql = "CREATE  TABLE IF NOT EXISTS $this->pay_gateways (
        `pg_id` int(6) NOT NULL AUTO_INCREMENT,
        `pg_name` varchar(255) DEFAULT NULL,
        `pg_modtest` smallint(5) DEFAULT NULL,
        `pg_data` text,
        `flag_status` smallint(5) DEFAULT NULL,
        `pg_order` smallint(5) DEFAULT '0',
        `pg_description` longtext,
        PRIMARY KEY (`pg_id`)
        ) " . $charset . ';';

        $wpdb->query($sql);
        // insert data
        $uifm_check_total = $wpdb->get_row('SELECT COUNT(*) AS total FROM ' . $this->pay_gateways, ARRAY_A);
if ( isset($uifm_check_total['total']) && intval($uifm_check_total['total']) === 0) {
    if ( ZIGAFORM_F_LITE === 1) {
        $sql = "INSERT INTO $this->pay_gateways VALUES 
                ('1', 'Offline', '0', '', '1', '3', 'Offline payment description'),
                ('2', 'Paypal', '0', '', '0', '0', 'paypal payment');";
        $wpdb->query($sql);
    } else {
        $sql = "INSERT INTO $this->pay_gateways VALUES 
                ('1', 'Offline', '0', '', '1', '3', 'Offline payment description'),
                ('2', 'Paypal', '0', '', '1', '0', 'paypal payment');";
        $wpdb->query($sql);
    }
}

        // payment records
        $sql = "CREATE  TABLE IF NOT EXISTS $this->pay_records (
        `pgr_id` int(10) NOT NULL AUTO_INCREMENT,
        `type_pg_id` int(6) NOT NULL,
        `pgr_payment_status` varchar(100) DEFAULT NULL,
        `pgr_payment_amount` varchar(100) DEFAULT NULL,
        `pgr_currency` varchar(45) DEFAULT NULL,
        `pgr_data` longtext,
        `flag_status` smallint(5) DEFAULT NULL,
        `created_date` timestamp NULL,
        `updated_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `created_ip` varchar(100) DEFAULT NULL,
        `updated_ip` varchar(100) DEFAULT NULL,
        `created_by` VARCHAR(100) NULL ,
        `updated_by` VARCHAR(100) NULL ,
        `fbh_id` int(10) NOT NULL ,
        PRIMARY KEY (`pgr_id`)
        ) " . $charset . ';';

        $wpdb->query($sql);

        // payment logs
        $sql = "CREATE  TABLE IF NOT EXISTS $this->pay_logs (
        `pgl_id` bigint(20) NOT NULL AUTO_INCREMENT,
        `type_pg_id` int(6) NOT NULL,
        `pgl_data` longtext,
        `pgl_data2` longtext,
        `pgl_error` longtext,
        `pgl_message` longtext,
        `pgr_id` int(10) NOT NULL ,
        `vis_last_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`pgl_id`)
        ) " . $charset . ';';

        $wpdb->query($sql);

        // visitor
        $sql = "CREATE  TABLE IF NOT EXISTS $this->visitor (
        `vis_id` bigint(20) NOT NULL AUTO_INCREMENT,
        `fmb_id` INT(6) NOT NULL ,
        `vis_uniqueid` varchar(10) DEFAULT NULL,
        `vis_user_agent` varchar(200) DEFAULT NULL,
        `vis_page` longtext,
        `vis_referer` longtext,
        `vis_ip` longtext,
        `vis_last_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
        `vis_params` longtext,
        PRIMARY KEY (`vis_id`)
        ) " . $charset . ';';

        $wpdb->query($sql);

        // visitor error
        $sql = "CREATE  TABLE IF NOT EXISTS $this->visitor_error (
        `vis_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        `vis_uniqueid` varchar(10) NOT NULL,
        `vis_user_agent` varchar(250) DEFAULT NULL,
        `vis_page` longtext,
        `vis_referer` longtext,
        `vis_error` longtext CHARACTER SET latin1,
        `vis_ip` varchar(100) DEFAULT NULL,
        `vis_last_date` timestamp NULL ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`vis_id`)
        ) " . $charset . ';';
        $wpdb->query($sql);

        update_option('uifmcostest_version', UIFORM_VERSION);

        // form log
        $sql = "CREATE  TABLE IF NOT EXISTS $this->form_log (
            `log_id` bigint(20) NOT NULL AUTO_INCREMENT,
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
            `created_by` VARCHAR(100) NULL ,
            `updated_by` VARCHAR(100) NULL ,
            `log_frm_parent` BIGINT DEFAULT 0,
            PRIMARY KEY (`log_id`)
        ) " . $charset . ';';

         $wpdb->query($sql);

         // addon
        $sql = "CREATE  TABLE IF NOT EXISTS $this->core_addon (
            `add_name` varchar(100) NOT NULL DEFAULT '',
            `add_title` text ,
            `add_info` text ,
            `add_system` smallint(5) DEFAULT NULL,
            `add_hasconfig` smallint(5) DEFAULT NULL,
            `add_version` varchar(45)  DEFAULT NULL,
            `add_icon` text ,
            `add_installed` smallint(5) DEFAULT NULL,
            `add_order` int(5) DEFAULT NULL,
            `add_params` longtext,
            `add_log` longtext,
            `addonscol` varchar(45) DEFAULT NULL,
            `flag_status` smallint(5)  DEFAULT 1,
            `created_date` timestamp NULL,
            `updated_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `created_ip` varchar(100)  DEFAULT NULL,
            `updated_ip` varchar(100)  DEFAULT NULL,
            `created_by` VARCHAR(100) NULL ,
            `updated_by` VARCHAR(100) NULL ,
            `add_xml` longtext,
            `add_load_back` smallint(5) DEFAULT NULL,
            `add_load_front` smallint(5) DEFAULT NULL,
            `is_field` smallint(5) DEFAULT NULL,
            PRIMARY KEY (`add_name`) 
        ) " . $charset . ';';

         $wpdb->query($sql);

              // insert data
            $uifm_check_total = $wpdb->get_row('SELECT COUNT(*) AS total FROM ' . $this->core_addon . " where add_name='func_anim'", ARRAY_A);
if ( isset($uifm_check_total['total']) && intval($uifm_check_total['total']) === 0) {
    $sql = "INSERT INTO $this->core_addon VALUES ('func_anim', 'Animation effect', 'You can animate your fields adding many animation effects. Also you can set up the delay and other options.', 1, 1, NULL, NULL, 1, 1, NULL, NULL, NULL, 0, '1980-01-01 00:00:01', '2018-01-31 10:35:14', NULL, NULL, NULL, NULL, NULL, 1, 1, 1);";
     $wpdb->query($sql);
}
              // insert data
            $uifm_check_total = $wpdb->get_row('SELECT COUNT(*) AS total FROM ' . $this->core_addon . " where add_name='webhook'", ARRAY_A);
if ( isset($uifm_check_total['total']) && intval($uifm_check_total['total']) === 0) {
    $sql = "INSERT INTO $this->core_addon VALUES ('webhook', 'WebHooks Add-On', 'You can use the WebHooks Add-On to send data from your forms to any custom page or script you like. This page can perform integration tasks to transform, parse, manipulate and send your submission data to wherever you choose. If you are developing an application that needs to be updated every time a form is submitted, WebHooks is for you. The advantage of WebHooks is that the passing of data is immediate and you can pass all submitted form data at once. e.g. you can connect with Webhook of Zapier - https%3A%2F%2Fzapier.com%2Fpage%2Fwebhooks%2F', 1, 1, NULL, NULL, 1, 2, NULL, NULL, NULL, 0, '2019-12-30 01:36:23', '2019-12-30 01:34:27', NULL, NULL, NULL, NULL, NULL, 1, 1, 0);";
     $wpdb->query($sql);
}

            // insert data
            $uifm_check_total = $wpdb->get_row('SELECT COUNT(*) AS total FROM ' . $this->core_addon . " where add_name='woocommerce'", ARRAY_A);
if ( isset($uifm_check_total['total']) && intval($uifm_check_total['total']) === 0) {
    $sql = "INSERT INTO $this->core_addon VALUES ('woocommerce', 'Woocommerce Add-On', 'Integrate your estimation form into woocommerce.  Add custom summary to a product form and collect more data when it is added to the cart.', 1, 1, '1.0', NULL, 1, 3, NULL, NULL, NULL, 0, '2020-01-29 23:46:55', '2020-01-29 23:42:54', NULL, NULL, NULL, NULL, NULL, 1, 1, 0);";
    $wpdb->query($sql);
}

         // insert data
            $uifm_check_total = $wpdb->get_row('SELECT COUNT(*) AS total FROM ' . $this->core_addon . " where add_name='mgtranslate'", ARRAY_A);
if ( isset($uifm_check_total['total']) && intval($uifm_check_total['total']) === 0) {
    $sql = "INSERT INTO $this->core_addon VALUES ('mgtranslate', 'Translation Manager Add-on', 'Translate any text on zigaform, and add new language', 1, 1, '1.0', NULL, 0, 4, '{\"required_wp\":5.0,\"required_php\":7.2}', NULL, NULL, 0, '2020-09-26 12:13:06', '2020-09-26 12:12:40', NULL, NULL, NULL, NULL, '<?xml version=\"1.0\"?> <params><required_wp>5.0</required_wp><required_php>7.2</required_php></params>', 1, 0, 0);";
     $wpdb->query($sql);
}

           // addon detail
        $sql = "CREATE  TABLE IF NOT EXISTS $this->core_addon_detail (
            `add_name` varchar(45)  NOT NULL,
            `fmb_id` int(10) NOT NULL,
            `adet_data` longtext ,
            `flag_status` smallint(5) DEFAULT 1,
            `created_date` timestamp NULL,
            `updated_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `created_ip` varchar(100) DEFAULT NULL,
            `updated_ip` varchar(100) DEFAULT NULL,
            `created_by` VARCHAR(100) NULL ,
            `updated_by` VARCHAR(100) NULL ,
            PRIMARY KEY (`add_name`, `fmb_id`) 
        ) " . $charset . ';';

         $wpdb->query($sql);

        // addon log
        $sql = "CREATE  TABLE IF NOT EXISTS $this->core_addon_log (
            `add_log_id` bigint(20) NOT NULL AUTO_INCREMENT,
            `add_name` varchar(45)  NOT NULL,
            `fmb_id` int(5) NOT NULL,
            `adet_data` longtext  NULL,
            `flag_status` smallint(5) DEFAULT 1,
            `created_date` timestamp NULL,
            `updated_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `created_ip` varchar(100) DEFAULT NULL,
            `updated_ip` varchar(100) DEFAULT NULL,
            `created_by` VARCHAR(100) NULL ,
            `updated_by` VARCHAR(100) NULL ,
            `log_id` bigint(20) NOT NULL,
            PRIMARY KEY (`add_log_id`) 
        ) " . $charset . ';';

         $wpdb->query($sql);
