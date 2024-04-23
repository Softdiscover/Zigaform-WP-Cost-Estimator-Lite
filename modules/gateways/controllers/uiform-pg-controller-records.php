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
if ( class_exists('Uiform_Pg_Controller_Records')) {
    return;
}

use \Zigaform\Admin\List_data;

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
class Uiform_Pg_Controller_Records extends Uiform_Base_Module
{

    const VERSION       = '0.1';
    private $pagination = '';
    private $per_page       = 50;
    private $wpdb       = '';
    protected $modules;
    private $model_gateways_records = '';
    private $model_record           = '';

    /**
     * Constructor
     *
     * @mvc Controller
     */
    protected function __construct()
    {
        global $wpdb;
        $this->wpdb                   = $wpdb;
        $this->model_gateways_records = self::$_models['gateways']['records'];
        $this->model_record           = self::$_models['formbuilder']['form_records'];
        // delete record
        //add_action( 'wp_ajax_rocket_fbuilder_invoice_delete_records', array( &$this, 'ajax_delete_records' ) );

        //ajax_recordlist_sendfilter
        add_action('wp_ajax_zgfm_fbuilder_invoicelist_sendfilter', array( &$this, 'ajax_invoicelist_sendfilter' ));

        // list form update status
        add_action('wp_ajax_zgfm_fbuilder_list_invoice_updatest', array( &$this, 'ajax_list_invoice_updatest' ));

        // delete record
        add_action('wp_ajax_rocket_fbuilder_delete_invoice', array( &$this, 'ajax_delete_invoice' ));
    }

    public function ajax_list_invoice_updatest()
    {

        check_ajax_referer('zgfm_ajax_nonce', 'zgfm_security');
        $list_ids = ( isset($_POST['id']) && $_POST['id'] ) ? array_map(array( 'Uiform_Form_Helper', 'sanitizeRecursive' ), $_POST['id']) : array();
        $form_st  = ( isset($_POST['form_st']) && $_POST['form_st'] ) ? Uiform_Form_Helper::sanitizeInput($_POST['form_st']) : '';
        $is_trash  = ( isset($_POST['is_trash']) && $_POST['is_trash'] ) ? Uiform_Form_Helper::sanitizeInput($_POST['is_trash']) : '';
        if ( $list_ids) {
            if ( intval($is_trash) === 0) {
                switch ( intval($form_st)) {
                    case 1:
                    case 2:
                    case 0:
                        foreach ( $list_ids as $value) {
                            $where = array(
                                'pgr_id' => $value,
                            );
                            $data  = array(
                                'flag_status' => intval($form_st),
                            );
                            $this->wpdb->update($this->model_gateways_records->table, $data, $where);
                        }
                        break;
                    default:
                        break;
                }
            } else {
                switch ( intval($form_st)) {
                    case 1:
                    case 2:
                        foreach ( $list_ids as $value) {
                            $where = array(
                                'pgr_id' => $value,
                            );
                            $data  = array(
                                'flag_status' => intval($form_st),
                            );
                            $this->wpdb->update($this->model_gateways_records->table, $data, $where);
                        }
                        break;
                    case 0:
                        foreach ( $list_ids as $value) {
                            $this->delete_form_process($value);
                        }

                        break;
                    default:
                        # code...
                        break;
                }
            }
        }
    }

    private function delete_form_process($value)
    {

        //remove from records
        $where = array(
            'pgr_id' => $value,
        );
        $this->wpdb->delete(self::$_models['gateways']['logs']->table, $where);

        //remove from records
        $where = array(
            'pgr_id' => $value,
        );
        $this->wpdb->delete($this->model_gateways_records->table, $where);
    }

    public function ajax_delete_invoice()
    {

        check_ajax_referer('zgfm_ajax_nonce', 'zgfm_security');

        $pgr_id = ( isset($_POST['pgr_id']) && $_POST['pgr_id'] ) ? Uiform_Form_Helper::sanitizeInput($_POST['pgr_id']) : 0;
        $is_trash = ( isset($_POST['is_trash']) && $_POST['is_trash'] ) ? Uiform_Form_Helper::sanitizeInput($_POST['is_trash']) : 0;

        if ( intval($is_trash) === 0) {
            $where  = array(
                'pgr_id' => $pgr_id,
            );
            $data   = array(
                'flag_status' => 0,
            );
            $this->wpdb->update($this->model_gateways_records->table, $data, $where);
        } else {
            $this->delete_form_process($pgr_id);
        }
    }

    /**
     * List trash forms
     *
     * @return void
     */
    public function ajax_invoicelist_sendfilter()
    {
        check_ajax_referer('zgfm_ajax_nonce', 'zgfm_security');

        $data_filter = ( isset($_POST['data_filter']) && $_POST['data_filter'] ) ? $_POST['data_filter'] : '';

        $opt_save   = ( isset($_POST['opt_save']) && $_POST['opt_save'] ) ? Uiform_Form_Helper::sanitizeInput($_POST['opt_save']) : 0;
        $opt_offset = ( isset($_POST['opt_offset']) && $_POST['opt_offset'] ) ? Uiform_Form_Helper::sanitizeInput($_POST['opt_offset']) : 0;
        $is_trash = ( isset($_POST['op_is_trash']) && $_POST['op_is_trash'] ) ? Uiform_Form_Helper::sanitizeInput($_POST['op_is_trash']) : 0;

        parse_str($data_filter, $data_filter_arr);

        $per_page   = $data_filter_arr['zgfm-listform-pref-perpage'];
        $orderby    = $data_filter_arr['zgfm-listform-pref-orderby'];

        $data               = array();
        $data['per_page']   = $per_page;
        $data['orderby']    = $orderby;
        $data['is_trash']    = $is_trash;

        update_option('zgfm_listinvoices_searchfilter', $data);

        $data['segment'] = 0;
        $data['offset']  = $opt_offset;

        $result = $this->ajax_invoiceslist_refresh($data);

        $json            = array();
        $json['content'] = $result;

        header('Content-Type: application/json');
        echo json_encode($json);
        wp_die();
    }

    /**
     * get forms in trash
     *
     * @param [type] $data
     * @return void
     */
    public function ajax_invoiceslist_refresh($data)
    {

        require_once UIFORM_FORMS_DIR . '/classes/Pagination.php';
        $this->pagination = new CI_Pagination();

        $offset = $data['offset'];

        // list all forms
        $config                         = array();

        $tmp = $this->model_gateways_records->ListTotals();
        if ( intval($data['is_trash']) === 0) {
            $config['base_url']             = admin_url() . '?page=zgfm_form_builder&zgfm_mod=gateways&zgfm_contr=records&zgfm_action=list_records';
            $config['total_rows']           = $tmp->r_all;
        } else {
            $config['base_url']             = admin_url() . '?page=zgfm_form_builder&zgfm_mod=formbuilder&zgfm_contr=records&zgfm_action=list_trash_records';
            $config['total_rows']           = $tmp->r_trash;
        }

        $config['per_page']             = $data['per_page'];
        $config['first_link']           = 'First';
        $config['last_link']            = 'Last';
        $config['full_tag_open']        = '<ul class="pagination pagination-sm">';
        $config['full_tag_close']       = '</ul>';
        $config['first_tag_open']       = '<li>';
        $config['first_tag_close']      = '</li>';
        $config['last_tag_open']        = '<li>';
        $config['last_tag_close']       = '</li>';
        $config['cur_tag_open']         = '<li class="zgfm-pagination-active"><span>';
        $config['cur_tag_close']        = '</span></li>';
        $config['next_tag_open']        = '<li>';
        $config['next_tag_close']       = '</li>';
        $config['prev_tag_open']        = '<li>';
        $config['prev_tag_close']       = '</li>';
        $config['num_tag_open']         = '<li>';
        $config['num_tag_close']        = '</li>';
        $config['page_query_string']    = true;
        $config['query_string_segment'] = 'offset';

        $this->pagination->initialize($config);
        // If the pagination library doesn't recognize the current page add:
        $this->pagination->cur_page = $offset;

        $data2               = array();
        $data2['per_page']   = $data['per_page'];
        $data2['segment']    = $offset;
        $data2['orderby']    = $data['orderby'];
        $data2['is_trash']  = $data['is_trash'];

        if ( intval($data2['is_trash']) === 0) {
            $data2['query'] = $this->model_gateways_records->getListAllInvoicesFiltered($data2);
        } else {
            $data2['query'] = $this->model_gateways_records->getListTrashInvoicesFiltered($data2);
        }

        $data2['pagination'] = $this->pagination->create_links();
        $data2['obj_list_data'] = List_data::get();

        if ( intval($data2['is_trash']) === 0) {
            return List_data::get()->list_detail_invoices($data2);
        } else {
            return List_data::get()->list_detail_invoicestrash($data2);
        }
    }

    public function info_record()
    {
        $id_rec            = ( isset($_GET['id_rec']) && $_GET['id_rec'] ) ? Uiform_Form_Helper::sanitizeInput($_GET['id_rec']) : 0;
        $data              = array();
        $data['record_id'] = $id_rec;

        $form_rec_data = $this->model_record->getFormDataById($id_rec);

        $data['fmb_inv_tpl_st'] = $form_rec_data->fmb_inv_tpl_st;
        $data['base_url']       = UIFORM_FORMS_URL . '/';
        $data['form_id']        = $form_rec_data->form_fmb_id;
        $data['url_form']       = site_url() . '/?uifm_costestimator_api_handler&zgfm_action=uifm_est_api_handler&uifm_action=show_invoice&uifm_mode=pdf&is_html=1&id=' . $id_rec;
        $data['show_summary']   = self::render_template('formbuilder/views/frontend/form_summary_custom.php', $data);

        echo self::loadPartial('layout.php', 'gateways/views/records/info_record.php', $data);
    }

    public function list_records()
    {
        $filter_data = get_option('zgfm_listinvoices_searchfilter', true);
        $data2       = array();
        if ( empty($filter_data)) {
            $data2['per_page']   = intval($this->per_page);
            $data2['orderby']    = 'asc';
        } else {
            $data2['per_page']   = intval($filter_data['per_page']??'');
            $data2['orderby']    = $filter_data['orderby']??'';
        }

        $offset          = ( isset($_GET['offset']) ) ? Uiform_Form_Helper::sanitizeInput($_GET['offset']) : 0;
        $data2['offset'] = $offset;

        $form_data = $this->model_gateways_records->ListTotals();
        $data2['title'] = __('Invoices list', 'FRocket_admin');
        $data2['all'] = $form_data->r_all;
        $data2['trash'] = $form_data->r_trash;
        $data2['header_buttons'] = List_data::get()->list_detail_invoice_headerbuttons();
        $data2['script_trigger'] = 'zgfm_back_general.invoiceslist_search_process();';
        $data2['subcurrent'] = 1;
        $data2['subsubsub'] = List_data::get()->subsubsub_invoices($data2);
        $data2['is_trash'] = 0;

        $content = List_data::get()->show_list($data2);
        echo self::loadPartial2('layout.php', $content);
    }

    /**
     * list trash records
     *
     * @return void
     */
    public function list_trash_records()
    {
        $filter_data = get_option('zgfm_listinvoices_searchfilter', true);
        $data2       = array();
        if ( empty($filter_data)) {
            $data2['per_page']   = intval($this->per_page);
            $data2['orderby']    = 'asc';
        } else {
            $data2['per_page']   = intval($filter_data['per_page']??'');
            $data2['orderby']    = $filter_data['orderby']??'';
        }

        $offset          = ( isset($_GET['offset']) ) ? Uiform_Form_Helper::sanitizeInput($_GET['offset']) : 0;
        $data2['offset'] = $offset;

        $form_data = $this->model_gateways_records->ListTotals();
        $data2['title'] = __('Invoices in trash', 'FRocket_admin');
        $data2['all'] = $form_data->r_all;
        $data2['trash'] = $form_data->r_trash;
        $data2['header_buttons'] = List_data::get()->list_detail_invoicetrash_headerbuttons();
        $data2['script_trigger'] = 'zgfm_back_general.invoiceslist_search_process();';
        $data2['subcurrent'] = 2;
        $data2['subsubsub'] = List_data::get()->subsubsub_invoices($data2);
        $data2['is_trash'] = 1;

        $content = List_data::get()->show_list($data2);
        echo self::loadPartial2('layout.php', $content);
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
