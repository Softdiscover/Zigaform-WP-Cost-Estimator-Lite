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
if (class_exists('Uiform_Pg_Controller_Records')) {
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
class Uiform_Pg_Controller_Records extends Uiform_Base_Module {

    const VERSION = '0.1';
    private $pagination = "";
    var $per_page = 5;
    private $wpdb = "";
    protected $modules;
    private $model_gateways_records = "";
    
    /**
     * Constructor
     *
     * @mvc Controller
     */
    protected function __construct() {
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->model_gateways_records = self::$_models['gateways']['records'];
       // delete record
       add_action('wp_ajax_rocket_fbuilder_invoice_delete_records', array(&$this, 'ajax_delete_records'));
    }
    
    public function ajax_delete_records() {
        
        check_ajax_referer( 'zgfm_ajax_nonce', 'zgfm_security' );
        
        $pgr_id = (isset($_POST['pgr_id']) && $_POST['pgr_id']) ? Uiform_Form_Helper::sanitizeInput($_POST['pgr_id']) : 0;
        $where = array(
            'pgr_id' => $pgr_id
        );
        $data = array(
            'flag_status' => 0
        );
        $this->wpdb->update($this->model_gateways_records->table, $data, $where);
    }
    
    public function info_record() {
        $id_rec = (isset($_GET['id_rec']) && $_GET['id_rec']) ? Uiform_Form_Helper::sanitizeInput($_GET['id_rec']) : 0;
        $data = array();
        $data['record_id']=$id_rec;
        $data['show_summary']=self::$_modules['formbuilder']['frontend']->get_summaryInvoice($id_rec);
        echo self::loadPartial('layout.php', 'gateways/views/records/info_record.php', $data);
    }
    
    public function list_records() {

        require_once( UIFORM_FORMS_DIR . '/classes/Pagination.php');
        $this->pagination = new CI_Pagination();
        $offset = (isset($_GET['offset']) && $_GET['offset']) ? Uiform_Form_Helper::sanitizeInput($_GET['offset']) : 0;
        //list all forms
        $data = $config = array();
        $config['base_url'] = admin_url() . '?page=zgfm_cost_estimate&zgfm_mod=gateways&zgfm_contr=records&zgfm_action=list_records';
        $config['total_rows'] = $this->model_gateways_records->CountRecords();
        $config['per_page'] = $this->per_page;
        $config['first_link'] = 'First';
        $config['last_link'] = 'Last';
        $config['full_tag_open'] = '<ul class="pagination pagination-sm">';
        $config['full_tag_close'] = '</ul>';
        $config['first_tag_open'] = '<li>';
        $config['first_tag_close'] = '</li>';
        $config['last_tag_open'] = '<li>';
        $config['last_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li><span>';
        $config['cur_tag_close'] = '</span></li>';
        $config['next_tag_open'] = '<li>';
        $config['next_tag_close'] = '</li>';
        $config['prev_tag_open'] = '<li>';
        $config['prev_tag_close'] = '</li>';
        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';
        $config['page_query_string'] = true;
        $config['query_string_segment'] = 'offset';

        $this->pagination->initialize($config);
        // If the pagination library doesn't recognize the current page add:
        $this->pagination->cur_page = $offset;
        $data['query'] = $this->model_gateways_records->getListRecords($this->per_page, $offset);
        $data['pagination'] = $this->pagination->create_links();

        echo self::loadPartial('layout.php', 'gateways/views/records/list_records.php', $data);
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
            //$instance_example = new WPPS_Instance_Class( 'Instance example', '42' );
            //add_notice('ba');
        } catch (Exception $exception) {
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
    public function activate($network_wide) {

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
    public function upgrade($db_version = 0) {
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
    protected function is_valid($property = 'all') {
        return true;
    }

}

?>
