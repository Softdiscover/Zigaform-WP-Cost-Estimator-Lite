<?php

/**
 * Frontend
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
if (!defined('ABSPATH')) {
    exit('No direct script access allowed');
}
if (class_exists('Uiform_Fb_Controller_Frontend')) {
    return;
}

/**
 * Controller Frontend class
 *
 * @category  PHP
 * @package   Rocket_form
 * @author    Softdiscover <info@softdiscover.com>
 * @copyright 2013 Softdiscover
 * @license   http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version   Release: 1.00
 * @link      https://wordpress-cost-estimator.zigaform.com
 */
class Uiform_Fb_Controller_Frontend extends Uiform_Base_Module
{

    const VERSION = '1.2';

    private $formsmodel         = '';
    private $model_formrecords  = '';
    private $model_fields       = '';
    private $model_visitor      = '';
    private $model_vis_error    = '';
    private $model_gateways     = '';
    private $model_gateways_rec = '';
    private $wpdb               = '';
    private $flag_submitted     = 0;
    private $form_response      = array();
    private $current_cost       = array();
    private $current_form_id    = '';
    private $form_rec_msg_summ  = '';

    private $format_price_conf = array();

    protected $modules;

    const PREFIX = 'wprofmr_';

    /**
     * Constructor
     *
     * @mvc Controller
     */
    protected function __construct()
    {

        $this->formsmodel         = self::$_models['formbuilder']['form'];
        $this->model_formrecords  = self::$_models['formbuilder']['form_records'];
        $this->model_fields       = self::$_models['formbuilder']['fields'];
        $this->model_visitor      = self::$_models['visitor']['visitor'];
        $this->model_vis_error    = self::$_models['visitor']['error'];
        $this->model_gateways     = self::$_models['gateways']['gateways'];
        $this->model_gateways_rec = self::$_models['gateways']['records'];

        global $wpdb;
        $this->wpdb = $wpdb;
        // Shortcodes
        add_shortcode('uiform-estimator', array(&$this, 'get_form_shortcode'));
        add_shortcode('zigaform-estimator', array(&$this, 'get_form_shortcode'));
        // ajax for verify recaptcha
        add_action('wp_ajax_rocket_front_checkrecaptcha', array(&$this, 'ajax_check_recaptcha'));
        add_action('wp_ajax_nopriv_rocket_front_checkrecaptcha', array(&$this, 'ajax_check_recaptcha'));
        // ajax for verify recaptchav3
        add_action('wp_ajax_rocket_front_checkrecaptchav3', array(&$this, 'ajax_check_recaptchav3'));
        add_action('wp_ajax_nopriv_rocket_front_checkrecaptchav3', array(&$this, 'ajax_check_recaptchav3'));
        // ajax refresh captcha
        add_action('wp_ajax_rocket_front_refreshcaptcha', array(&$this, 'ajax_refresh_captcha'));
        add_action('wp_ajax_nopriv_rocket_front_refreshcaptcha', array(&$this, 'ajax_refresh_captcha'));
        // ajax refresh captcha
        add_action('wp_ajax_rocket_front_valcaptcha', array(&$this, 'ajax_validate_captcha'));
        add_action('wp_ajax_nopriv_rocket_front_valcaptcha', array(&$this, 'ajax_validate_captcha'));
        // submit ajax mode
        add_action('wp_ajax_rocket_front_submitajaxmode', array(&$this, 'ajax_submit_ajaxmode'));
        add_action('wp_ajax_nopriv_rocket_front_submitajaxmode', array(&$this, 'ajax_submit_ajaxmode'));
        // submit ajax mode for multi-step
        add_action('wp_ajax_rocket_ms_front_submitajaxmode', array(&$this, 'ajax_ms_submit_ajaxmode'));
        add_action('wp_ajax_nopriv_rocket_ms_front_submitajaxmode', array(&$this, 'ajax_ms_submit_ajaxmode'));

        // ajax for save offline mode
        add_action('wp_ajax_rocket_front_saveofflinemode', array(&$this, 'ajax_save_offlinepayment'));
        add_action('wp_ajax_nopriv_rocket_front_saveofflinemode', array(&$this, 'ajax_save_offlinepayment'));

        // ajax for showing summary
        add_action('wp_ajax_rocket_front_payment_seesummary', array(&$this, 'ajax_payment_seesummary'));
        add_action('wp_ajax_nopriv_rocket_front_payment_seesummary', array(&$this, 'ajax_payment_seesummary'));

        // ajax for showing invoice
        add_action('wp_ajax_rocket_front_payment_seeinvoice', array(&$this, 'ajax_payment_seeinvoice'));
        add_action('wp_ajax_nopriv_rocket_front_payment_seeinvoice', array(&$this, 'ajax_payment_seeinvoice'));

        // shortcodes
        // add_shortcode('uifm_cost_total', array(&$this, 'shortcode_uifm_cost_total') );
        add_shortcode('uifm_wrap', array(&$this, 'shortcode_uifm_recvar_wrap'));
        add_shortcode('uifm_recvar', array(&$this, 'shortcode_uifm_recvar'));
        add_shortcode('zgfm_rfvar', array(&$this, 'shortcode_uifm_recfvar'));
        add_shortcode('uifm_var', array(&$this, 'shortcode_uifm_form_var'));
        // shortcode show version info
        add_action('wp_head', array(&$this, 'shortcode_show_version'));
        /*shortcodes*/
        add_shortcode('uifm_symbol', array(&$this, 'shortcode_uifm_symbol'));
        add_shortcode('uifm_total', array(&$this, 'shortcode_uifm_total'));
        add_shortcode('uifm_currency', array(&$this, 'shortcode_uifm_currency'));
        add_shortcode('uifm_summary', array(&$this, 'shortcode_uifm_summary'));
        add_shortcode('uifm_summary_link', array(&$this, 'shortcode_uifm_summary_link'));
        add_shortcode('uifm_price', array(&$this, 'shortcode_uifm_price'));

        add_shortcode('uifm_tax', array(&$this, 'shortcode_uifm_tax'));
        add_shortcode('uifm_subtotal', array(&$this, 'shortcode_uifm_subtotal'));

        // set cookie
        add_action('init', array(&$this, 'uifm_set_newuser_cookie'));

        /*shortcode calc*/
        add_shortcode('zgfm_fvar', array(&$this, 'shortcode_uifm_form_fvar'));

        // adding script for whole site
        add_action('wp_enqueue_scripts', array(&$this, 'global_scripts'));

        $modalmode = get_option('zgfm_c_modalmode', 0);
        if (intval($modalmode) === 1) {
            // load resources
            add_action('wp_enqueue_scripts', array(&$this, 'load_form_resources'), 50, 1);
        }

        // add variables
        add_filter('zgfm_front_initvar_load', array(&$this, 'front_initvar_load'));

        // ajax get children
        add_action('wp_ajax_rocket_front_mm_get_child', array(&$this, 'ajax_mm_get_child'));
        add_action('wp_ajax_nopriv_rocket_front_mm_get_child', array(&$this, 'ajax_mm_get_child'));
    }

    /**
     * add script to whole site
     */
    public function global_scripts()
    {
        // prev jquery
        wp_register_script('rockfm-prev-jquery', UIFORM_FORMS_URL . '/assets/common/js/init.js', array('jquery'), null, false);
        wp_enqueue_script('rockfm-prev-jquery');
        // for summmary and invoices
        wp_register_script(self::PREFIX . 'rockefform-iframe', UIFORM_FORMS_URL . '/assets/frontend/js/iframe/4.3.1/iframeResizer.min.js', array(), UIFORM_VERSION, false);
        wp_enqueue_script(self::PREFIX . 'rockefform-iframe');
    }

    /**
     * create frontend init variables
     */
    public function front_initvar_load($form_variables)
    {
        // load form variables

        $form_variables['url_site']                    = site_url();
        $form_variables['ajax_nonce']                  = wp_create_nonce('zgfm_ajax_nonce');
        $form_variables['ajaxurl']                     = site_url('wp-admin/admin-ajax.php');
        $form_variables['url_plugin']                  = UIFORM_FORMS_URL;
        $form_variables['imagesurl']                   = UIFORM_FORMS_URL . '/assets/frontend/images';
        $form_variables['_uifmvar']['fm_loadmode']     = '';
        $form_variables['_uifmvar']['fm_modalmode_st'] = get_option('zgfm_c_modalmode', 0);
        return apply_filters('zgfm_front_extra_variables', $form_variables);
    }
    public function shortcode_uifm_tax($atts)
    {

        ob_start();

        ?>
        <span class="uiform-stickybox-tax">0</span>
        <?php

        $str_output = ob_get_contents();
        ob_end_clean();
        return $str_output;
    }

    public function shortcode_uifm_subtotal($atts)
    {
        ob_start();

        ?>
        <span class="uiform-stickybox-subtotal">0</span>
        <?php

        $str_output = ob_get_contents();
        ob_end_clean();
        return $str_output;
    }

    public function shortcode_uifm_symbol($atts)
    {

        ob_start();
        ?>
        <span class="uiform-stickybox-symbol"></span>
        <?php
        $str_output = ob_get_contents();
        ob_end_clean();
        return $str_output;
    }

    public function shortcode_uifm_total($atts)
    {
        return '<span class="uiform-stickybox-total">0</span>';
    }

    public function shortcode_uifm_currency($atts)
    {

        ob_start();
        ?>
        <span class="uiform-stickybox-currency"></span>
        <?php
        $str_output = ob_get_contents();
        ob_end_clean();
        return $str_output;
    }

    public function shortcode_uifm_summary($atts)
    {
        $vars = shortcode_atts(
            array(
                'rows'            => '5',
                'heading'         => '',
                'hide_cur_code'   => '0',
                'hide_cur_symbol' => '0',
            ),
            $atts
        );

        ob_start();
        ?>
        <span class="uiform-stickybox-summary">
            <?php if (!empty($vars['heading'])) { ?>
                <span class="uiform-stickybox-summary-heading"><?php echo $vars['heading']; ?></span>
            <?php } ?>
            <span class="uiform-stickybox-summary-list">

            </span>

            <input type="hidden" class="_rockfm_shortcode_summ_data" data-zgfm-rows="<?php echo $vars['rows']; ?>" data-zgfm-hidecurcode="<?php echo $vars['hide_cur_code']; ?>" data-zgfm-hidecursymbol="<?php echo $vars['hide_cur_symbol']; ?>" value="">

            <input type="hidden" class="_rockfm_stickybox_summ_rows" value="">
        </span>
        <?php
        $str_output = ob_get_contents();
        ob_end_clean();

        return $str_output;
    }

    public function shortcode_uifm_summary_link($atts)
    {
        $vars = shortcode_atts(
            array(
                'value' => 'Show summary',
            ),
            $atts
        );

        ob_start();
        ?>
        <span class="uiform-stickybox-summary-link"><a href="javascript:void(0);" onclick="javascript:zgfm_front_cost.costest_summbox_linkPopUp(this);"><?php echo $vars['value']; ?></a></span>
        <?php
        $str_output = ob_get_contents();
        ob_end_clean();

        return $str_output;
    }

    public function shortcode_uifm_price($atts)
    {
        return '<span class="uiform-stickybox-inp-price">0</span>';
    }

    public function ajax_payment_seeinvoice()
    {

        $nonceCheck = apply_filters('zgfm_front_nonce_check', true);
        if ($nonceCheck) {
            check_ajax_referer('zgfm_ajax_nonce', 'zgfm_security');
        }

        $id_rec = (isset($_POST['form_r_id'])) ? Uiform_Form_Helper::sanitizeInput($_POST['form_r_id']) : '';

        $temp = $this->model_formrecords->getFormDataById($id_rec);

        $form_id          = $temp->form_fmb_id;
        $form_data        = $this->formsmodel->getFormById_2($form_id);
        $form_data_onsubm = json_decode($form_data->fmb_data2, true);
        $pdf_show_onpage  = (isset($form_data_onsubm['main']['pdf_show_onpage'])) ? $form_data_onsubm['main']['pdf_show_onpage'] : '0';

        $resp = array();

        $resp['show_summary_title'] = __('Invoice', 'frocket_front');
        if (intval($pdf_show_onpage) === 1) {
            $resp['show_summary_title'] = '<a class="sfdc-btn sfdc-btn-warning pull-right" onclick="javascript:rocketfm.genpdf_infoinvoice(' . $id_rec . ');" href="javascript:void(0);"><i class="fa fa-file-pdf-o"></i> ' . __('Export to PDF', 'frocket_front') . '</a>';
        }

        $data            = array();
        $data['base_url'] = UIFORM_FORMS_URL . '/';
        $data['form_id']  = $form_id;
        $data['url_form']  = site_url() . '/?uifm_costestimator_api_handler&zgfm_action=uifm_est_api_handler&uifm_action=show_invoice&uifm_mode=pdf&is_html=1&id=' . $id_rec;

        $resp['show_summary'] = Uiform_Form_Helper::encodeHex(self::render_template('formbuilder/views/frontend/form_invoice_custom.php', $data));
        // return data to ajax callback
        header('Content-Type: text/html; charset=UTF-8');
        echo json_encode($resp);
        wp_die();
    }

    public function ajax_payment_seesummary()
    {

        $nonceCheck = apply_filters('zgfm_front_nonce_check', true);
        if ($nonceCheck) {
            check_ajax_referer('zgfm_ajax_nonce', 'zgfm_security');
        }

        $id_rec = (isset($_POST['form_r_id'])) ? Uiform_Form_Helper::sanitizeInput($_POST['form_r_id']) : '';

        $temp             = $this->model_formrecords->getFormDataById($id_rec);
        $form_id          = $temp->form_fmb_id;
        $form_data        = $this->formsmodel->getFormById_2($form_id);
        $form_data_onsubm = json_decode($form_data->fmb_data2, true);
        $pdf_show_onpage  = (isset($form_data_onsubm['main']['pdf_show_onpage'])) ? $form_data_onsubm['main']['pdf_show_onpage'] : '0';

        $resp = array();

        $resp['show_summary_title'] = __('Order summary', 'frocket_front');
        if (intval($pdf_show_onpage) === 1) {
            if (ZIGAFORM_F_LITE === 1) {
                $resp['show_summary_title'] .= '';
            } else {
                $resp['show_summary_title'] .= ' <a class="sfdc-btn sfdc-btn-warning pull-right" onclick="javascript:rocketfm.genpdf_inforecord(' . $id_rec . ');" href="javascript:void(0);"><i class="fa fa-file-pdf-o"></i> ' . __('Export to PDF', 'frocket_front') . '</a>';
            }
        }

        if (isset($temp->fmb_rec_tpl_st) && intval($temp->fmb_rec_tpl_st) === 1) {
            $data             = array();
            $data['base_url'] = UIFORM_FORMS_URL . '/';
            $data['form_id']  = $form_id;
            $data['url_form'] = site_url() . '/?uifm_costestimator_api_handler&zgfm_action=uifm_est_api_handler&uifm_action=show_record&uifm_mode=pdf&is_html=1&id=' . $id_rec;

            $resp['show_summary'] = Uiform_Form_Helper::encodeHex(self::render_template('formbuilder/views/frontend/form_summary_custom.php', $data));
        } else {
            $resp['show_summary'] = Uiform_Form_Helper::encodeHex($this->get_summaryRecord($id_rec));
        }

        // return data to ajax callback
        header('Content-Type: text/html; charset=UTF-8');
        echo json_encode($resp);
        wp_die();
    }

    public function get_summaryInvoice()
    {
        $id_rec  = isset($_GET['id']) ? Uiform_Form_Helper::sanitizeInput($_GET['id']) : '';
        $form_id = (isset($_POST['form_id'])) ? Uiform_Form_Helper::sanitizeInput($_POST['form_id']) : '';

        $name_fields   = $this->model_formrecords->getNameInvoiceField($id_rec);
        $form_rec_data = $this->model_gateways_rec->getInvoiceDataByFormRecId($id_rec);
        if (empty($form_id)) {
            $form_id = $form_rec_data->fmb_id;
        }

        $form_data          = json_decode($form_rec_data->fmb_data, true);
        $form_data_currency = (isset($form_data['main']['price_currency'])) ? $form_data['main']['price_currency'] : '';
        $form_data_invoice  = (isset($form_data['invoice'])) ? $form_data['invoice'] : '';

        // price numeric format
        $format_price_conf                        = array();
        $format_price_conf['price_format_st']     = (isset($form_data['main']['price_format_st'])) ? $form_data['main']['price_format_st'] : '0';
        $format_price_conf['price_sep_decimal']   = (isset($form_data['main']['price_sep_decimal'])) ? $form_data['main']['price_sep_decimal'] : '.';
        $format_price_conf['price_sep_thousand']  = (isset($form_data['main']['price_sep_thousand'])) ? $form_data['main']['price_sep_thousand'] : ',';
        $format_price_conf['price_sep_precision'] = (isset($form_data['main']['price_sep_precision'])) ? $form_data['main']['price_sep_precision'] : '2';

        $name_fields_check = array();
        foreach ($name_fields as $value) {
            $name_fields_check[$value->fmf_uniqueid]['fieldname'] = $value->fieldname;
            $name_fields_check[$value->fmf_uniqueid]['id']        = $value->fmf_id;
        }

        $data_record     = $this->model_formrecords->getRecordById($id_rec);
        $record_user     = json_decode($data_record->fbh_data, true);
        $new_record_user = array();
        foreach ($record_user as $key => $value) {
            if (isset($name_fields_check[$key]) && isset($value['price_st']) && intval($value['price_st']) === 1) {
                $field_name      = '';
                $field_id        = '';
                $tmp_invoice_row = array();

                $field_name = $name_fields_check[$key]['fieldname'];
                $field_id   = $name_fields_check[$key]['id'];

                $tmp_invoice_row['item_uniqueid'] = $key;
                $tmp_invoice_row['item_id']       = $field_id;
                // $tmp_invoice_row['item_desc']=$value['label'];

                if (is_array($value['input'])) {
                    foreach ($value['input'] as $key2 => $value2) {
                        $tmp_invoice_row['item_qty']  = 1;
                        $tmp_invoice_row['item_desc'] = '';
                        if (isset($value2['qty'])) {
                            $tmp_invoice_row['item_qty'] = $value2['qty'];
                        } else {
                        }

                        if (isset($value2['amount'])) {
                            $tmp_invoice_row['item_amount'] = $value2['amount'];
                        } else {
                            $tmp_invoice_row['item_amount'] = isset($value2['cost']) ? $value2['cost'] : 0;
                        }

                        $tmp_inp_label = $value['label'];
                        if (!empty($value2['label'])) {
                            $tmp_inp_label .= ' - ' . $value2['label'];
                        }
                        $tmp_invoice_row['item_desc'] = $tmp_inp_label;

                        $new_record_user[] = $tmp_invoice_row;
                    }
                } else {
                    $tmp_invoice_row['item_qty']    = 1;
                    $tmp_invoice_row['item_desc']  .= ' ' . $value['input'];
                    $tmp_invoice_row['item_amount'] = '';
                    $new_record_user[]              = $tmp_invoice_row;
                }
            }
        }
        $data = array();

        // processs tax
        $form_data_tax_st  = (isset($form_data['main']['price_tax_st'])) ? $form_data['main']['price_tax_st'] : '0';
        $form_data_tax_val = (isset($form_data['main']['price_tax_val'])) ? $form_data['main']['price_tax_val'] : '';

        $tmp_amount_total = floatval($form_rec_data->fbh_total_amount);
        if (isset($form_data_tax_st) && intval($form_data_tax_st) === 1) {
            $tmp_tax                       = (floatval($form_data_tax_val) / 100);
            $tmp_sub_total                 = ($tmp_amount_total) * (100 / (100 + (100 * $tmp_tax)));
            $data['form_subtotal_amount'] = $tmp_sub_total;
            $data['form_tax']             = $tmp_amount_total - $tmp_sub_total;
        }

        $data['form_tax_enable']   = $form_data_tax_st;
        $data['form_total_amount'] = $tmp_amount_total;
        $data['form_mathcalc_st']  = (isset($form_data['calculation']['enable_st'])) ? $form_data['calculation']['enable_st'] : '0';
        $data['form_currency']     = $form_data_currency;
        $data['record_info']       = $new_record_user;
        $data['price_format']      = $format_price_conf;
        $data['invoice_id']        = $form_rec_data->pgr_id;
        $data['invoice_date']      = date('F j, Y, g:i a', strtotime($form_rec_data->created_date));

        $data['invoice_from_info1'] = isset($form_data_invoice['from_text1']) ? urldecode($form_data_invoice['from_text1']) : '';
        $data['invoice_from_info2'] = isset($form_data_invoice['from_text2']) ? urldecode($form_data_invoice['from_text2']) : '';
        $data['invoice_from_info3'] = isset($form_data_invoice['from_text3']) ? urldecode($form_data_invoice['from_text3']) : '';
        $data['invoice_from_info4'] = isset($form_data_invoice['from_text4']) ? urldecode($form_data_invoice['from_text4']) : '';
        $data['invoice_from_info5'] = isset($form_data_invoice['from_text5']) ? urldecode($form_data_invoice['from_text5']) : '';

        $data['invoice_to_info1'] = isset($form_data_invoice['to_text1']) ? urldecode($this->model_formrecords->getFieldOptRecord($id_rec, '', $form_data_invoice['to_text1'], 'input')) : '';
        $data['invoice_to_info2'] = isset($form_data_invoice['to_text2']) ? urldecode($this->model_formrecords->getFieldOptRecord($id_rec, '', $form_data_invoice['to_text2'], 'input')) : '';
        $data['invoice_to_info3'] = isset($form_data_invoice['to_text3']) ? urldecode($this->model_formrecords->getFieldOptRecord($id_rec, '', $form_data_invoice['to_text3'], 'input')) : '';
        $data['invoice_to_info4'] = isset($form_data_invoice['to_text4']) ? urldecode($this->model_formrecords->getFieldOptRecord($id_rec, '', $form_data_invoice['to_text4'], 'input')) : '';
        $data['invoice_to_info5'] = isset($form_data_invoice['to_text5']) ? urldecode($form_data_invoice['to_text5']) : '';
        $form_summary             = self::render_template('formbuilder/views/frontend/form_invoice.php', $data);

        return $form_summary;
    }

    public function get_summaryRecord($id_rec)
    {
        $form_id = (isset($_POST['form_id'])) ? Uiform_Form_Helper::sanitizeInput($_POST['form_id']) : '';

        $name_fields   = $this->model_formrecords->getNameField($id_rec);
        $form_rec_data = $this->model_formrecords->getFormDataById($id_rec);

        $form_data          = json_decode($form_rec_data->fmb_data, true);
        $form_data_currency = (isset($form_data['main']['price_currency'])) ? $form_data['main']['price_currency'] : '';

        // price numeric format
        $format_price_conf                        = array();
        $format_price_conf['price_format_st']     = (isset($form_data['main']['price_format_st'])) ? $form_data['main']['price_format_st'] : '0';
        $format_price_conf['price_sep_decimal']   = (isset($form_data['main']['price_sep_decimal'])) ? $form_data['main']['price_sep_decimal'] : '.';
        $format_price_conf['price_sep_thousand']  = (isset($form_data['main']['price_sep_thousand'])) ? $form_data['main']['price_sep_thousand'] : ',';
        $format_price_conf['price_sep_precision'] = (isset($form_data['main']['price_sep_precision'])) ? $form_data['main']['price_sep_precision'] : '2';

        $name_fields_check = array();

        $field_data_stored = array();

        foreach ($name_fields as $value) {
            $name_fields_check[$value->fmf_uniqueid] = $value->fieldname;
            $field_data_stored[$value->fmf_uniqueid] = $value->fmf_data;
        }
        $data_record     = $this->model_formrecords->getRecordById($id_rec);
        $record_user     = json_decode($data_record->fbh_data, true);
        $new_record_user = array();

        foreach ($record_user as $key => $value) {
            $field_name = '';
            if (isset($name_fields_check[$key])) {
                $field_name = $name_fields_check[$key];
            }

            $field_data = array();
            if (isset($field_data_stored[$key])) {
                $field_data = $field_data_stored[$key];
                $field_data = json_decode($field_data, true);
            }

            $value['label'] = (isset($value['label'])) ? $value['label'] : 'not assigned';

            switch (intval($value['type'])) {
                case 9:
                case 11:
                    $new_record_user[] = array(
                        'field' => $value['label'],
                        'value' => $value['input_value'],
                    );
                    break;
                case 12:
                case 13:
                    $value_new = $value['input'];
                    // checking if image exists
                    if (!empty($value_new) && @is_array(getimagesize($value_new))) {
                        $value_new = '<img width="100px" src="' . $value_new . '"/>';
                    }

                    $new_record_user[] = array(
                        'field'             => $value['label'],
                        'field_name'        => $field_name,
                        'type'              => $value['type'],
                        'price_lbl_show_st' => isset($field_data['price']['lbl_show_st']) ? $field_data['price']['lbl_show_st'] : '0',
                        'value'             => $value_new,
                    );
                    break;
                default:
                    $new_record_user[] = array(
                        'field'             => $value['label'],
                        'field_name'        => $field_name,
                        'type'              => $value['type'],
                        'price_lbl_show_st' => isset($field_data['price']['lbl_show_st']) ? $field_data['price']['lbl_show_st'] : '0',
                        'value'             => $value['input'],
                    );
                    break;
            }
        }
        $data = array();

        // processs tax
        $form_data_tax_st  = (isset($form_data['main']['price_tax_st'])) ? $form_data['main']['price_tax_st'] : '0';
        $form_data_tax_val = (isset($form_data['main']['price_tax_val'])) ? $form_data['main']['price_tax_val'] : '';

        $tmp_amount_total = floatval($form_rec_data->fbh_total_amount);
        if (isset($form_data_tax_st) && intval($form_data_tax_st) === 1) {
            $tmp_tax                       = (floatval($form_data_tax_val) / 100);
            $tmp_sub_total                 = ($tmp_amount_total) * (100 / (100 + (100 * $tmp_tax)));
            $data['form_subtotal_amount'] = $tmp_sub_total;
            $data['form_tax']             = $tmp_amount_total - $tmp_sub_total;
        }

        $data['form_total_amount'] = $tmp_amount_total;
        $data['form_currency']     = $form_data_currency;
        $data['record_info']       = $new_record_user;
        $data['price_format']      = $format_price_conf;
        $form_summary              = self::render_template('formbuilder/views/frontend/form_summary.php', $data);
        return $form_summary;
    }



    public function ajax_save_offlinepayment()
    {

        $nonceCheck = apply_filters('zgfm_front_nonce_check', true);
        if ($nonceCheck) {
            check_ajax_referer('zgfm_ajax_nonce', 'zgfm_security');
        }

        $offline_return_url = (isset($_POST['offline_return_url'])) ? Uiform_Form_Helper::sanitizeInput($_POST['offline_return_url']) : '';
        $item_number        = (isset($_POST['item_number'])) ? Uiform_Form_Helper::sanitizeInput($_POST['item_number']) : '';
        $form_id            = (isset($_POST['form_id'])) ? Uiform_Form_Helper::sanitizeInput($_POST['form_id']) : '';

        $data                               = array();
        $data['type_pg_id']         = 1;
        $data['pgr_payment_status'] = 'completed';
        $data['pgr_data']           = json_encode($_POST);
        $where                      = array(
            'pgr_id' => $item_number,
        );
        $this->wpdb->update($this->model_gateways_rec->table, $data, $where);

        $resp               = array();
        $resp['success']    = 1;
        $resp['return_url'] = $offline_return_url;

        $gt_data              = $this->model_gateways_rec->getRecordById($item_number);
        $this->flag_submitted = $gt_data->fbh_id;
        if (empty($offline_return_url)) {
            // get data from form
            $form_data        = $this->formsmodel->getFormById_2($form_id);
            $form_data_onsubm = json_decode($form_data->fmb_data2, true);
            // prepare message
            $tmp_template_msg         = (isset($form_data_onsubm['onsubm']['sm_successtext'])) ? urldecode($form_data_onsubm['onsubm']['sm_successtext']) : '';
            $tmp_template_msg = do_shortcode($tmp_template_msg);

            $resp['show_message'] = $tmp_template_msg;
        }

        // return data to ajax callback
        header('Content-Type: text/html; charset=UTF-8');
        echo json_encode($resp);
        wp_die();
    }

    public function uifm_set_newuser_cookie()
    {
        $ip         = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
        $user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
        $hash       = hash('crc32', md5($ip . $user_agent));
        setcookie(UIFORM_FOLDER, $hash, time() + (60 * 60 * 24 * 30), '/');
    }

    public function uifm_get_newuser_hash()
    {
        $ip         = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
        $user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
        $hash       = hash('crc32', md5($ip . $user_agent));

        return $hash;
    }

    public function shortcode_uifm_cost_total($atts)
    {

        $vars = shortcode_atts(
            array(
                'heading' => __('Total cost', 'frocket_front'),
            ),
            $atts
        );

        ob_start();
        ?>
        <table cellspacing="0" cellpadding="0">
            <tr>
                <td align="center" valign="top"><?php echo $vars['heading']; ?></td>
                <td width="20" align="center" valign="top">:</td>
                <td width="200" align="left" valign="top"><?php echo $this->current_cost['symbol'] . $this->current_cost['total'] . ' ' . $this->current_cost['cur']; ?></td>
            </tr>
        </table>
        <?php
        $str_output = ob_get_contents();
        ob_end_clean();

        if (isset($this->current_cost['st']) && intval($this->current_cost['st']) === 1) {
            return $str_output;
        } else {
            return '';
        }
    }

    public function shortcode_uifm_recvar_wrap($atts, $content = null)
    {

        $vars = shortcode_atts(
            array(
                'id'   => '',
                'atr1' => 'input',
                'opt'  => '', // quick option
            ),
            $atts
        );

        $result = '';
        $output = '';

        switch (strval($vars['opt'])) {
            case 'calc':
                $form_rec_data = $this->model_formrecords->getVarOptRecord('calc_' . $vars['atr1'], $this->flag_submitted);
                $resultCalc        = $form_rec_data;

                if ($resultCalc != '' && intval($resultCalc) > 0) {
                    $output = '1';
                }

                break;

            default:
                if (strpos($vars['id'], '_') !== false) {
                    $tmpResult = explode('_', $vars['id']);
                    $f_data = $this->model_formrecords->getFieldDataByIdOnMultistep($this->flag_submitted, $tmpResult[0], $tmpResult[1]);
                } else {
                    $f_data = $this->model_formrecords->getFieldDataById($this->flag_submitted, $vars['id']);
                }
                switch (intval($f_data->type)) {
                    case 16:
                    case 17:
                    case 18:
                        $output = $this->model_formrecords->getFieldOptRecord($this->flag_submitted, $f_data->type, $vars['id'], 'input', 'value');
                        break;

                    default:
                        $output = $this->model_formrecords->getFieldOptRecord($this->flag_submitted, $f_data->type, $vars['id'], $vars['atr1']);
                        break;
                }



                break;
        }

        if ($output != '' && $output != '0') {
            $result = do_shortcode($content);
        } else {
            $result = '';
        }

        return $result;
    }


    public function shortcode_uifm_recvar($atts)
    {

        try {
            $vars = shortcode_atts(
                array(
                    'id'   => '',
                    'atr1' => 'input',
                    'atr2' => '',
                    'atr3' => '',
                    'atr4' => '',
                ),
                $atts
            );

            if (strpos($vars['id'], '_') !== false) {
                $tmpResult = explode('_', $vars['id']);
                $f_data = $this->model_formrecords->getFieldDataByIdOnMultistep($this->flag_submitted, $tmpResult[0], $tmpResult[1]);
            } else {
                $f_data = $this->model_formrecords->getFieldDataById($this->flag_submitted, $vars['id']);
            }
            if (!isset($f_data->type)) {
                throw new Exception(__('field data is not cound. field:' . $vars['id']));
            }
            $output = $this->model_formrecords->getFieldOptRecord($this->flag_submitted, $f_data->type, $vars['id'], $vars['atr1'], $vars['atr2']);

            // apply price format
            switch (strval($vars['atr3'])) {
                case 'price_format':
                    /*
                     * check if format price configuration is stored
                     */
                    if (empty($this->format_price_conf)) {
                        $form_rec_data = $this->model_formrecords->getFormDataById($this->flag_submitted);
                        $form_data      = json_decode($form_rec_data->fmb_data, true);
                        // price numeric format
                        $format_price_conf                        = array();
                        $format_price_conf['price_format_st']     = (isset($form_data['main']['price_format_st'])) ? $form_data['main']['price_format_st'] : '0';
                        $format_price_conf['price_sep_decimal']   = (isset($form_data['main']['price_sep_decimal'])) ? $form_data['main']['price_sep_decimal'] : '.';
                        $format_price_conf['price_sep_thousand']  = (isset($form_data['main']['price_sep_thousand'])) ? $form_data['main']['price_sep_thousand'] : ',';
                        $format_price_conf['price_sep_precision'] = (isset($form_data['main']['price_sep_precision'])) ? $form_data['main']['price_sep_precision'] : '2';
                        $this->format_price_conf                  = $format_price_conf;
                    }

                    $output = Uiform_Form_Helper::cformat_numeric($this->format_price_conf, $output);

                    break;
                case 'format':
                    switch (strval($vars['atr4'])) {
                        case 'list':
                            //format to field with multiple options
                            switch (strval($f_data->type)) {
                                case '9':
                                case '11':
                                    $tmpArr = explode('^,^', $output);
                                    if (is_array($tmpArr)) {
                                        $newString = '<ul>';
                                        foreach ($tmpArr as $key => $value) {
                                            $newString .= '<li>' . $value . '</li>';
                                        }
                                        $newString .= '</ul>';
                                        $output = $newString;
                                    }

                                    break;

                                default:
                                    # code...
                                    break;
                            }
                            break;
                        case 'comma':
                            //format to field with multiple options
                            switch (strval($f_data->type)) {
                                case '9':
                                case '11':
                                    $tmpArr = explode('^,^', $output);
                                    if (is_array($tmpArr)) {
                                        $output = str_replace('^,^', ', ', $output);
                                    }

                                    break;

                                default:
                                    # code...
                                    break;
                            }
                            break;
                        default:
                            break;
                    }

                    break;
            }

            if ($output != '') {
                return $output;
            } else {
                return '';
            }
        } catch (Exception $exception) {
            $data                = array();
            $data['vars']        = $vars;
            $data['error_debug'] = __METHOD__ . ' error: ' . $exception->getMessage();
            return '';
        }
    }


    public function shortcode_uifm_recfvar($atts)
    {

        $vars = shortcode_atts(
            array(
                'id'   => '',
                'atr1' => 'input',
            ),
            $atts
        );

        switch (strval($vars['atr1'])) {
            case 'label':
                ob_start();
                ?>
                <span data-zgfm-id="<?php echo $vars['id']; ?>" data-zgfm-type="0" data-zgfm-atr="0" class="zgfm-recfvar-obj"></span>
                <?php
                $output = ob_get_contents();
                ob_end_clean();
                break;
            case 'input':
                ob_start();
                ?>
                <span data-zgfm-id="<?php echo $vars['id']; ?>" data-zgfm-atr="1" class="zgfm-recfvar-obj"></span>
                <?php
                $output = ob_get_contents();
                ob_end_clean();
                break;
            case 'amount':
                ob_start();
                ?>
                <span data-zgfm-id="<?php echo $vars['id']; ?>" data-zgfm-atr="2" class="zgfm-recfvar-obj"></span>
                <?php
                $output = ob_get_contents();
                ob_end_clean();
                break;
            case 'qty':
                ob_start();
                ?>
                <span data-zgfm-id="<?php echo $vars['id']; ?>" data-zgfm-atr="3" class="zgfm-recfvar-obj"></span>
                <?php
                $output = ob_get_contents();
                ob_end_clean();
                break;
        }

        if ($output != '') {
            return $output;
        } else {
            return '';
        }
    }

    public function shortcode_uifm_form_fvar($atts)
    {
        $vars   = shortcode_atts(
            array(
                'atr1' => '',
                'atr2' => '',
                'atr3' => '',
                'opt'  => '', // quick option
            ),
            $atts
        );
        $output = '';

        if (!empty($vars['opt'])) {
            switch ((string) $vars['opt']) {
                case 'calc':
                    ob_start();
                    if (isset($vars['atr1']) && intval($vars['atr1']) >= 0) {
                        ?>
                        <div class="zgfm-f-calc-var-lbl zgfm-f-calc-var<?php echo $vars['atr1']; ?>-lbl"></div>
                        <?php
                    }
                    $output = ob_get_contents();
                    ob_end_clean();

                    break;
                case 'form_cur_symbol':
                    ob_start();
                    ?>
                    <span class="uiform-stickybox-symbol"></span>
                    <?php
                    $output = ob_get_contents();
                    ob_end_clean();
                    break;
                case 'form_cur_code':
                    ob_start();
                    ?>
                    <span class="uiform-stickybox-currency"></span>
                    <?php
                    $output = ob_get_contents();
                    ob_end_clean();
                    break;
                case 'form_subtotal_amount':
                    ob_start();
                    ?>
                    <span class="uiform-stickybox-subtotal">0</span>
                    <?php

                    $output = ob_get_contents();
                    ob_end_clean();
                    break;
                case 'form_tax_amount':
                    ob_start();

                    ?>
                    <span class="uiform-stickybox-tax">0</span>
                    <?php

                    $output = ob_get_contents();
                    ob_end_clean();
                    break;
                case 'form_total_amount':
                    $output = '<span class="uiform-stickybox-total">0</span>';
                    break;
            }
        }

        if ($output != '') {
            return $output;
        } else {
            return '';
        }
    }

    public function shortcode_uifm_form_var($atts)
    {

        $vars   = shortcode_atts(
            array(
                'atr1' => '0', // source 0=>fmb_data2; 1=>fmb_data
                'atr2' => '',
                'atr3' => '',
                'atr4' => '',
                'opt'  => '', // quick option
            ),
            $atts
        );
        $output = '';

        $rec_id = $this->flag_submitted;

        if (!empty($vars['opt'])) {
            switch ((string) $vars['opt']) {
                case 'calc':
                    $form_rec_data = $this->model_formrecords->getVarOptRecord('calc_' . $vars['atr1'], $this->flag_submitted);
                    $output        = $form_rec_data;
                    break;
                case 'form_currency_symbol':
                case 'form_cur_symbol':
                    $form_data        = $this->formsmodel->getFormById_2($this->current_form_id);
                    $form_data_onsubm = json_decode($form_data->fmb_data2, true);
                    $output           = $form_data_onsubm['main']['price_currency_symbol'];
                    break;
                case 'form_currency_code':
                case 'form_cur_code':
                    $form_data        = $this->formsmodel->getFormById_2($this->current_form_id);
                    $form_data_onsubm = json_decode($form_data->fmb_data2, true);
                    $output           = $form_data_onsubm['main']['price_currency'];
                    break;
                case 'form_total_amount':
                case 'form_subtotal_amount':
                case 'form_tax_amount':
                    $form_rec_data = $this->model_formrecords->getFormDataById($rec_id);
                    if (!isset($form_rec_data->fmb_data)) {
                        break 1;
                    }
                    $form_data = json_decode($form_rec_data->fmb_data, true);

                    // price numeric format
                    $format_price_conf                        = array();
                    $format_price_conf['price_format_st']     = (isset($form_data['main']['price_format_st'])) ? $form_data['main']['price_format_st'] : '0';
                    $format_price_conf['price_sep_decimal']   = (isset($form_data['main']['price_sep_decimal'])) ? $form_data['main']['price_sep_decimal'] : '.';
                    $format_price_conf['price_sep_thousand']  = (isset($form_data['main']['price_sep_thousand'])) ? $form_data['main']['price_sep_thousand'] : ',';
                    $format_price_conf['price_sep_precision'] = (isset($form_data['main']['price_sep_precision'])) ? $form_data['main']['price_sep_precision'] : '2';

                    $tmp_amount_total    = 0;
                    $tmp_amount_subtotal = 0;
                    $tmp_amount_tax      = 0;

                    $tmp_output = $this->model_formrecords->getOptRecordById('fbh_total_amount', $rec_id);
                    if (!empty($tmp_output) && isset($tmp_output->fbh_total_amount)) {
                        $tmp_amount_total = floatval($tmp_output->fbh_total_amount);
                    }

                    // processs tax
                    $form_data_tax_st  = (isset($form_data['main']['price_tax_st'])) ? $form_data['main']['price_tax_st'] : '0';
                    $form_data_tax_val = (isset($form_data['main']['price_tax_val'])) ? $form_data['main']['price_tax_val'] : '';

                    if (isset($form_data_tax_st) && intval($form_data_tax_st) === 1) {
                        $tmp_tax             = (floatval($form_data_tax_val) / 100);
                        $tmp_sub_total       = ($tmp_amount_total) * (100 / (100 + (100 * $tmp_tax)));
                        $tmp_amount_subtotal = $tmp_sub_total;
                        $tmp_amount_tax      = $tmp_amount_total - $tmp_sub_total;
                    }

                    switch ((string) $vars['opt']) {
                        case 'form_total_amount':
                            $output = Uiform_Form_Helper::cformat_numeric($format_price_conf, $tmp_amount_total);
                            break;
                        case 'form_subtotal_amount':
                            $output = Uiform_Form_Helper::cformat_numeric($format_price_conf, $tmp_amount_subtotal);
                            break;
                        case 'form_tax_amount':
                            $output = Uiform_Form_Helper::cformat_numeric($format_price_conf, $tmp_amount_tax);
                            break;
                    }

                    break;
                case 'rec_summ':
                    $data             = $this->model_formrecords->getFormDataById($rec_id);
                    $tmp_data         = json_decode($data->fbh_data, true);
                    $form_data_onsubm = json_decode($data->fmb_data2, true);

                    // price numeric format
                    $format_price_conf                        = array();
                    $format_price_conf['price_format_st']     = (isset($form_data_onsubm['main']['price_format_st'])) ? $form_data_onsubm['main']['price_format_st'] : '0';
                    $format_price_conf['price_sep_decimal']   = (isset($form_data_onsubm['main']['price_sep_decimal'])) ? $form_data_onsubm['main']['price_sep_decimal'] : '.';
                    $format_price_conf['price_sep_thousand']  = (isset($form_data_onsubm['main']['price_sep_thousand'])) ? $form_data_onsubm['main']['price_sep_thousand'] : ',';
                    $format_price_conf['price_sep_precision'] = (isset($form_data_onsubm['main']['price_sep_precision'])) ? $form_data_onsubm['main']['price_sep_precision'] : '2';

                    $data2                        = array();
                    $data2['data']                = $tmp_data;
                    $data2['format_price_conf']   = $format_price_conf;
                    $data2['form_cost_total']     = $data->fbh_total_amount;
                    $data2['current_cost_st']     = (isset($form_data_onsubm['main']['price_st'])) ? $form_data_onsubm['main']['price_st'] : 'USD';
                    $data2['current_cost_symbol'] = (isset($form_data_onsubm['main']['price_currency_symbol'])) ? $form_data_onsubm['main']['price_currency_symbol'] : '$';
                    $data2['current_cost_cur']    = (isset($form_data_onsubm['main']['price_currency'])) ? $form_data_onsubm['main']['price_currency'] : 'USD';
                    $data2['show_only_value'] = ($vars['atr2'] === 'show_only_value') ? 'yes' : 'no';
                    $data2['hide_total'] = ($vars['atr3'] === 'hide_total') ? 'yes' : 'no';

                    $output                       = self::render_template('formbuilder/views/frontend/mail_generate_fields.php', $data2, 'always');
                    break;
                case 'rec_url_fm':
                    $output = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
                    break;
                case 'form_name':
                    $data   = $this->model_formrecords->getFormDataById($rec_id);
                    $output = $data->fmb_name;
                    break;
                case 'rec_id':
                    $output = $rec_id;
                    break;
                case 'form_inv_number':
                    $data   = $this->model_gateways_rec->getInvoiceDataByFormRecId($rec_id);
                    $output = $data->pgr_id;
                    break;
                case 'form_inv_date':
                    $data = $this->model_gateways_rec->getInvoiceDataByFormRecId($rec_id);
                    if (!empty($vars['atr2'])) {
                        $temp_date = date($vars['atr2'], strtotime($data->created_date));
                    } else {
                        $temp_date = date('F j, Y', strtotime($data->created_date));
                    }
                    $output = $temp_date;
                    break;
                case 'user_ip':
                    $data   = $this->model_formrecords->getFormDataById($rec_id);
                    $output = $data->created_ip;
                    break;
                case 'logged_username':
                    $user = wp_get_current_user();
                    $output = $user->user_login;
                    break;
                case 'logged_email':
                    $user = wp_get_current_user();
                    $output = $user->user_email;
                    break;

                default:
            }

            // apply price format
            switch (strval($vars['atr3'])) {
                case 'price_format':
                    /*
                     * check if format price configuration is stored
                     */
                    if (empty($this->format_price_conf)) {
                        $form_rec_data = $this->model_formrecords->getFormDataById($rec_id);
                        $form_data      = json_decode($form_rec_data->fmb_data, true);
                        // price numeric format
                        $format_price_conf                        = array();
                        $format_price_conf['price_format_st']     = (isset($form_data['main']['price_format_st'])) ? $form_data['main']['price_format_st'] : '0';
                        $format_price_conf['price_sep_decimal']   = (isset($form_data['main']['price_sep_decimal'])) ? $form_data['main']['price_sep_decimal'] : '.';
                        $format_price_conf['price_sep_thousand']  = (isset($form_data['main']['price_sep_thousand'])) ? $form_data['main']['price_sep_thousand'] : ',';
                        $format_price_conf['price_sep_precision'] = (isset($form_data['main']['price_sep_precision'])) ? $form_data['main']['price_sep_precision'] : '2';
                        $this->format_price_conf                  = $format_price_conf;
                    }

                    $output = Uiform_Form_Helper::cformat_numeric($this->format_price_conf, $output);

                    break;
            }
        } else {
        }

        // get data from form
        if ($output != '') {
            return $output;
        } else {
            return '';
        }
    }
    public function ajax_ms_submit_ajaxmode()
    {
        $nonceCheck = apply_filters('zgfm_front_nonce_check', true);
        if ($nonceCheck) {
            check_ajax_referer('zgfm_ajax_nonce', 'zgfm_security');
        }

        $resp = array();
        $resp = $this->process_form(true);

        if (isset($this->flag_submitted) && intval($this->flag_submitted) > 0) {
            $resp['success']      = (isset($resp['success'])) ? $resp['success'] : 0;
            $resp['show_message'] = (isset($resp['show_message'])) ? Uiform_Form_Helper::encodeHex($resp['show_message']) :
                '<div class="rockfm-alert rockfm-alert-danger"><i class="fa fa-exclamation-triangle"></i> ' . __('Success! your form was submitted', 'frocket_front') . '</div>';
        } else {
            $resp['success']      = 0;
            $resp['show_message'] = '<div class="rockfm-alert rockfm-alert-danger"><i class="fa fa-exclamation-triangle"></i> ' . __('warning! Form was not submitted', 'frocket_front') . '</div>';
        }

        // return data to ajax callback
        header('Content-Type: text/html; charset=UTF-8');
        echo json_encode($resp);
        wp_die();
    }
    public function ajax_submit_ajaxmode()
    {
        $nonceCheck = apply_filters('zgfm_front_nonce_check', true);
        if ($nonceCheck) {
            check_ajax_referer('zgfm_ajax_nonce', 'zgfm_security');
        }
        $resp = array();
        $resp = $this->process_form();

        if (isset($this->flag_submitted) && intval($this->flag_submitted) > 0) {
            $resp['success']    = (isset($resp['success'])) ? $resp['success'] : 0;
            $resp['payment_st'] = (isset($resp['payment_st'])) ? $resp['payment_st'] : 0;
            if (intval($resp['payment_st']) === 1) {
                $resp['show_message'] = (isset($resp['payment_html'])) ? Uiform_Form_Helper::encodeHex(do_shortcode($resp['payment_html'])) :
                    '<div class="rockfm-alert rockfm-alert-danger"><i class="fa fa-exclamation-triangle"></i> ' . __('Error! something went wrong.', 'frocket_front') . '</div>';
            } else {
                $resp['show_message'] = (isset($resp['show_message'])) ? Uiform_Form_Helper::encodeHex(do_shortcode($resp['show_message'])) :
                    '<div class="rockfm-alert rockfm-alert-danger"><i class="fa fa-exclamation-triangle"></i> ' . __('Success! your form was submitted', 'frocket_front') . '</div>';
            }
        } else {
            $resp['success']      = 0;
            $resp['show_message'] = '<div class="rockfm-alert rockfm-alert-danger"><i class="fa fa-exclamation-triangle"></i> ' . __('warning! Form was not submitted', 'frocket_front') . '</div>';
        }

        // return data to ajax callback
        header('Content-Type: text/html; charset=UTF-8');
        echo json_encode($resp);
        wp_die();
    }

    public function ajax_validate_captcha()
    {

        $nonceCheck = apply_filters('zgfm_front_nonce_check', true);
        if ($nonceCheck) {
            check_ajax_referer('zgfm_ajax_nonce', 'zgfm_security');
        }

        $rockfm_code     = (isset($_POST['rockfm-code'])) ? Uiform_Form_Helper::sanitizeInput($_POST['rockfm-code']) : '';
        $rockfm_inpcode  = (isset($_POST['rockfm-inpcode'])) ? Uiform_Form_Helper::sanitizeInput($_POST['rockfm-inpcode']) : '';
        $resp            = array();
        $resp['code']    = $rockfm_code;
        $resp['inpcode'] = $rockfm_inpcode;

        if (!empty($rockfm_code)) {
            $captcha_key  = 'Rocketform-' . $_SERVER['HTTP_HOST'];
            $captcha_resp = Uiform_Form_Helper::data_decrypt($rockfm_code, $captcha_key);
            $resp['resp'] = $captcha_resp;
            if ((string) $captcha_resp === (string) ($rockfm_inpcode)) {
                $resp['success'] = true;
            } else {
                $resp['success'] = false;
            }
        } else {
            $resp['success'] = false;
        }

        // return data to ajax callback
        header('Content-Type: text/html; charset=UTF-8');
        echo json_encode($resp);
        wp_die();
    }
    public function ajax_mm_get_child()
    {
        $nonceCheck = apply_filters('zgfm_front_nonce_check', true);
        if ($nonceCheck) {
            check_ajax_referer('zgfm_ajax_nonce', 'zgfm_security');
        }

        $form_parent_id     = (isset($_POST['form_parent_id'])) ? Uiform_Form_Helper::sanitizeInput($_POST['form_parent_id']) : '';
        $form_child_id  = (isset($_POST['form_child_id'])) ? Uiform_Form_Helper::sanitizeInput($_POST['form_child_id']) : '';
        $resp            = array();

        $data_form = $this->formsmodel->getFormById($form_child_id);

        $resp['html'] = $data_form->fmb_html;

        // return data to ajax callback
        header('Content-Type: text/html; charset=UTF-8');
        echo json_encode($resp);
        wp_die();
    }
    public function ajax_refresh_captcha()
    {

        $nonceCheck = apply_filters('zgfm_front_nonce_check', true);
        if ($nonceCheck) {
            check_ajax_referer('zgfm_ajax_nonce', 'zgfm_security');
        }

        $rkver = (isset($_POST['rkver'])) ? Uiform_Form_Helper::sanitizeInput(trim($_POST['rkver'])) : 0;
        if ($rkver) {
            $rkver     = Uiform_Form_Helper::base64url_decode($rkver);
            $rkver_arr = json_decode($rkver, true);

            $length  = 5;
            $charset = 'abcdefghijklmnpqrstuvwxyz123456789';
            $phrase  = '';
            $chars   = str_split($charset);

            for ($i = 0; $i < $length; $i++) {
                $phrase .= $chars[array_rand($chars)];
            }
            $captcha_data = array();

            if (isset($rkver_arr['ca_txt_gen'])) {
                $rkver_arr['ca_txt_gen'] = $phrase;
                $captcha_data            = $rkver_arr;
            } else {
                $captcha_data['ca_txt_gen'] = $phrase;
            }
            $captcha_options = Uiform_Form_Helper::base64url_encode(json_encode($captcha_data));
            $resp            = array();
            $resp['rkver']   = $captcha_options;

            /* generate check code */
            $captcha_key  = 'Rocketform-' . $_SERVER['HTTP_HOST'];
            $resp['code'] = Uiform_Form_Helper::data_encrypt($phrase, $captcha_key);

            // return data to ajax callback
            header('Content-Type: text/html; charset=UTF-8');
            echo json_encode($resp);
            wp_die();
        }
    }

    public function ajax_check_recaptcha()
    {

        $nonceCheck = apply_filters('zgfm_front_nonce_check', true);
        if ($nonceCheck) {
            check_ajax_referer('zgfm_ajax_nonce', 'zgfm_security');
        }

        require_once UIFORM_FORMS_DIR . '/modules/formbuilder/controllers/uiform-fb-controller-recaptcha.php';
        $inst_re = new Uiform_Fb_Controller_Recaptcha();
        $inst_re->front_verify_recaptcha();
    }

    public function ajax_check_recaptchav3()
    {

        $nonceCheck = apply_filters('zgfm_front_nonce_check', true);
        if ($nonceCheck) {
            check_ajax_referer('zgfm_ajax_nonce', 'zgfm_security');
        }

        require_once UIFORM_FORMS_DIR . '/modules/formbuilder/controllers/uiform-fb-controller-recaptcha.php';
        $inst_re = new Uiform_Fb_Controller_Recaptcha();
        $inst_re->front_verify_recaptchav3();
    }
    public function process_form_fields($form_fields, $form_id)
    {
        $form_f_tmp            = array();
        $form_f_rec_tmp        = array();
        $form_errors    = array();
        $attachment_status     = 0;
        $attachments           = array();  // initialize attachment array
        $form_cost_total       = 0;
        if (!empty($form_fields)) {
            foreach ($form_fields as $key => $value) {
                $tmp_field_name = $this->model_fields->getFieldNameByUniqueId($key, $form_id);

                if (!isset($tmp_field_name->type)) {
                    $err_output = 'error $key:' . $key . ' - $form_id:' . $form_id;
                    if (UIFORM_DEBUG === 1) {
                        $err_output .= ' - Last query: ' . htmlentities($this->wpdb->last_query, ENT_NOQUOTES, 'UTF-8');
                    }
                    throw new Exception($err_output);
                }

                switch (intval($tmp_field_name->type)) {
                    case 6:
                        /*textbox*/
                    case 28:
                    case 29:
                    case 30:
                        $tmp_fdata = json_decode($tmp_field_name->data, true);
                        if (isset($tmp_fdata['validate']) && isset($tmp_fdata['validate']['typ_val']) && intval($tmp_fdata['validate']['typ_val']) === 4) {
                            // $mail_replyto=$value;
                        }
                        break;
                }

                /*storing to main array*/

                switch (intval($tmp_field_name->type)) {
                    case 9:
                        /*checkbox*/
                    case 11:
                        /*multiselect*/
                        $tmp_fdata                         = json_decode($tmp_field_name->data, true);
                                $tmp_field_cost_total              = 0;
                                $tmp_options                       = array();
                                $tmp_field_label                   = ( ! empty($tmp_fdata['label']['text']) ) ? $tmp_fdata['label']['text'] : $tmp_field_name->fieldname;
                                $form_f_tmp[ $key ]['type']        = $tmp_field_name->type;
                                $form_f_tmp[ $key ]['fieldname']   = $tmp_field_name->fieldname;
                                $form_f_tmp[ $key ]['label']       = $tmp_field_label;
                                $form_f_tmp[ $key ]['price_st']    = isset($tmp_fdata['price']['enable_st']) ? $tmp_fdata['price']['enable_st'] : 0;
                                $form_f_tmp[ $key ]['lbl_show_st'] = isset($tmp_fdata['price']['lbl_show_st']) ? $tmp_fdata['price']['lbl_show_st'] : 0;

                                $tmp_f_values = array();

                                $tmp_inp_label = array();
                                $tmp_inp_value = array();

                                if ( is_array($value)) {
                                    // for records
                                    $tmp_options_rec = array();
                                    foreach ( $value as $key2 => $value2) {
                                        $tmp_options_row          = array();
                                        $tmp_options_row['label'] = isset($tmp_fdata['input2']['options'][ $value2 ]['label']) ? $tmp_fdata['input2']['options'][ $value2 ]['label'] : '';
                                        $tmp_options_row['value'] = isset($tmp_fdata['input2']['options'][ $value2 ]['value']) ? $tmp_fdata['input2']['options'][ $value2 ]['value'] : '';
                                        $tmp_options_rec[]        = $tmp_options_row['value'];
                                        $tmp_f_values[]           = $value2;
                                    }
                                    $form_f_rec_tmp[ $key ] = implode('^,^', $tmp_options_rec);
                                    // end for records

                                    foreach ( $value as $key2 => $value2) {
                                        $tmp_options_row          = array();
                                        $tmp_options_row['label'] = isset($tmp_fdata['input2']['options'][ $value2 ]['label']) ? $tmp_fdata['input2']['options'][ $value2 ]['label'] : '';

                                        $tmp_options_row['value'] = isset($tmp_fdata['input2']['options'][ $value2 ]['value']) ? $tmp_fdata['input2']['options'][ $value2 ]['value'] : '';

                                        // store label
                                        $tmp_inp_label[] = $tmp_options_row['label'];
                                        $tmp_inp_value[] = $tmp_options_row['value'];

                                        if ( isset($tmp_fdata['input2']['options'][ $value2 ]) && $tmp_fdata['input2']['options'][ $value2 ]) {
                                            $tmp_options_row['cost']   = floatval($tmp_fdata['input2']['options'][ $value2 ]['price']?? 0);
                                            $tmp_options_row['amount'] = $tmp_options_row['cost'];

                                            if ( isset($tmp_fdata['price']['enable_st'])
                                                    && intval($this->current_cost['st']) === 1
                                                    && intval($tmp_fdata['price']['enable_st']) === 1) {
                                                /*cost estimate*/
                                                $form_cost_total += $tmp_options_row['cost'];
                                            }

                                            $tmp_field_cost_total                 = $tmp_field_cost_total + $tmp_options_row['cost'];
                                            $form_f_tmp[ $key ]['input_cost_amt'] = floatval($tmp_field_cost_total);
                                        }

                                        if ( isset($tmp_fdata['input2']['options'][ $value2 ]) && $tmp_fdata['input2']['options'][ $value2 ]) {
                                            $tmp_options[ $value2 ] = $tmp_options_row;
                                        }
                                    }
                                }

                                $form_f_tmp[ $key ]['input_label'] = implode('^,^', $tmp_inp_label);
                                $form_f_tmp[ $key ]['input_value'] = implode('^,^', $tmp_inp_value);

                                $form_f_tmp[ $key ]['chosen'] = implode(',', $tmp_f_values);
                                /*saving data to field array*/
                                $form_f_tmp[ $key ]['input'] = $tmp_options;

                                break;
                    case 8:
                        /*radiobutton*/
                    case 10:
                        /*select*/

                        $tmp_fdata                       = json_decode($tmp_field_name->data, true);
                                 $tmp_field_cost_total            = 0;
                                 $tmp_options                     = array();
                                 $tmp_field_label                 = ( ! empty($tmp_fdata['label']['text']) ) ? $tmp_fdata['label']['text'] : $tmp_field_name->fieldname;
                                 $form_f_tmp[ $key ]['type']      = $tmp_field_name->type;
                                 $form_f_tmp[ $key ]['fieldname'] = $tmp_field_name->fieldname;
                                 $form_f_tmp[ $key ]['label']     = $tmp_field_label;

                                 $form_f_tmp[ $key ]['chosen']      = implode(',', array( $value ));
                                 $form_f_tmp[ $key ]['price_st']    = isset($tmp_fdata['price']['enable_st']) ? $tmp_fdata['price']['enable_st'] : 0;
                                 $form_f_tmp[ $key ]['lbl_show_st'] = isset($tmp_fdata['price']['lbl_show_st']) ? $tmp_fdata['price']['lbl_show_st'] : 0;

                                 // foreach ($value as $key2=>$value2) {
                                     $tmp_options_row          = array();
                                     $tmp_options_row['label'] = isset($tmp_fdata['input2']['options'][ $value ]['label']) ? $tmp_fdata['input2']['options'][ $value ]['label'] : '';
                                     $tmp_options_row['value'] = isset($tmp_fdata['input2']['options'][ $value ]['value']) ? $tmp_fdata['input2']['options'][ $value ]['value'] : '';

                                     // for records
                                     $form_f_rec_tmp[ $key ] = $tmp_options_row['label'];

                                if ( isset($tmp_fdata['input2']['options'][ $value ])) {
                                    $tmp_options_row['cost']   = floatval($tmp_fdata['input2']['options'][ $value ]['price']??0);
                                    $tmp_options_row['amount'] = $tmp_options_row['cost'];

                                    if ( isset($tmp_fdata['price']['enable_st'])
                                          && intval($this->current_cost['st']) === 1
                                          && intval($tmp_fdata['price']['enable_st']) === 1) {
                                             /*cost estimate*/
                                             $form_cost_total += $tmp_options_row['amount'];
                                    }

                                         $tmp_field_cost_total                 = $tmp_field_cost_total + $tmp_options_row['cost'];
                                         $form_f_tmp[ $key ]['input_cost_amt'] = floatval($tmp_field_cost_total);
                                }

                                if ( isset($tmp_fdata['input2']['options'][ $value ])) {
                                    $tmp_options[ $value ] = $tmp_options_row;
                                }
                                      // }

                                      $form_f_tmp[ $key ]['input_label'] = $tmp_options_row['label'];
                                      $form_f_tmp[ $key ]['input_value'] = $tmp_options_row['value'];
                                      /*saving data to field array*/
                                      $form_f_tmp[ $key ]['input'] = $tmp_options;

                                break;

                    case 12:
                        /*file input field*/
                    case 13:
                        /*image upload*/
                        $tmp_fdata = json_decode($tmp_field_name->data, true);

                        $tmp_options                     = array();
                        $tmp_field_label                 = (!empty($tmp_fdata['label']['text'])) ? $tmp_fdata['label']['text'] : $tmp_field_name->fieldname;
                        $form_f_tmp[$key]['type']      = $tmp_field_name->type;
                        $form_f_tmp[$key]['fieldname'] = $tmp_field_name->fieldname;
                        $form_f_tmp[$key]['label']     = $tmp_field_label;

                        $allowedext_default = array('aaaa', 'png', 'doc', 'docx', 'xls', 'xlsx', 'csv', 'txt', 'rtf', 'zip', 'mp3', 'wma', 'wmv', 'mpg', 'flv', 'avi', 'jpg', 'jpeg', 'png', 'gif', 'ods', 'rar', 'ppt', 'tif', 'wav', 'mov', 'psd', 'eps', 'sit', 'sitx', 'cdr', 'ai', 'mp4', 'm4a', 'bmp', 'pps', 'aif', 'pdf');
                        $custom_allowedext  = (!empty($tmp_fdata['input16']['extallowed'])) ? array_map('trim', explode(',', $tmp_fdata['input16']['extallowed'])) : $allowedext_default;
                        $custom_maxsize     = (!empty($tmp_fdata['input16']['maxsize'])) ? floatval($tmp_fdata['input16']['maxsize']) : 5;
                        $custom_attach_st   = (isset($tmp_fdata['input16']['attach_st'])) ? intval($tmp_fdata['input16']['attach_st']) : 0;

                        if (
                            isset($_FILES['uiform_fields']['name'][$key])
                            && !empty($_FILES['uiform_fields']['name'][$key])
                        ) {
                            $fileSize = $_FILES['uiform_fields']['size'][$key];
                            if (floatval($fileSize) > $custom_maxsize * 1024 * 1024) {
                                $form_errors[] = __('Error! The file exceeds the allowed size of', 'frocket_front') . ' ' . $custom_maxsize . ' MB';
                            }
                            /*find attachment max size found*/
                            $attachment_status = ($attachment_status < $custom_attach_st) ? $custom_attach_st : $attachment_status;

                            $ext = strtolower(substr($_FILES['uiform_fields']['name'][$key], strrpos($_FILES['uiform_fields']['name'][$key], '.') + 1));
                            if (!in_array($ext, $custom_allowedext)) {
                                $form_errors[] = __('Error! Type of file is not allowed to upload', 'frocket_front');
                            }
                            if (empty($form_errors)) {
                                $upload_data   = wp_upload_dir();  // look for this function in WordPress documentation at codex
                                $upload_dir    = $upload_data['path'];
                                $upload_dirurl = $upload_data['baseurl'];
                                $upload_subdir = $upload_data['subdir'];
                                $rename        = 'file_' . md5(uniqid($_FILES['uiform_fields']['name'][$key], true));

                                $_FILES['uiform_fields']['name'][$key] = $rename . '.' . strtolower($ext);

                                $form_f_tmp[$key]['input'] = $upload_dirurl . $upload_subdir . '/' . $_FILES['uiform_fields']['name'][$key];
                                $form_f_rec_tmp[$key]      = $upload_dirurl . $upload_subdir . '/' . $_FILES['uiform_fields']['name'][$key];
                                $form_fields[$key]         = $upload_dirurl . $upload_subdir . '/' . $_FILES['uiform_fields']['name'][$key];

                                // attachment

                                if ($_FILES['uiform_fields']['error'][$key] == UPLOAD_ERR_OK) {
                                    $tmp_name = $_FILES['uiform_fields']['tmp_name'][$key]; // Get temp name of uploaded file
                                    $name     = $_FILES['uiform_fields']['name'][$key];  // rename it to whatever
                                    move_uploaded_file($tmp_name, "$upload_dir/$name"); // move file to new location
                                    if (intval($custom_attach_st) === 1) {
                                        $attachments[] = "$upload_dir/$name";  // push the new uploaded file in attachment array
                                    }
                                }
                            }
                        } else {
                            unset($form_fields[$key]);
                            $form_f_tmp[$key]['input'] = '';
                            $form_f_rec_tmp[$key]      = '';
                        }

                        break;
                    case 16:
                        /*slider*/
                    case 18:
                        /*spinner*/
                        $tmp_fdata = json_decode($tmp_field_name->data, true);

                                $tmp_field_label                   = ( ! empty($tmp_fdata['label']['text']) ) ? $tmp_fdata['label']['text'] : $tmp_field_name->fieldname;
                                $form_f_tmp[ $key ]['type']        = $tmp_field_name->type;
                                $form_f_tmp[ $key ]['fieldname']   = $tmp_field_name->fieldname;
                                $form_f_tmp[ $key ]['label']       = $tmp_field_label;
                                $form_f_tmp[ $key ]['price_st']    = isset($tmp_fdata['price']['enable_st']) ? $tmp_fdata['price']['enable_st'] : 0;
                                $form_f_tmp[ $key ]['lbl_show_st'] = isset($tmp_fdata['price']['lbl_show_st']) ? $tmp_fdata['price']['lbl_show_st'] : 0;

                                // foreach ($value as $key2=>$value2) {
                                    $tmp_options_row = array();

                                       $tmp_options_row['cost'] = floatval($tmp_fdata['price']['unit_price']);

                                       $tmp_options_row['qty']   = floatval($value);
                                       $tmp_options_row['value'] = floatval($value);
                                       // for records
                                       $form_f_rec_tmp[ $key ] = $value;

                                if ( isset($tmp_fdata['price']['enable_st'])
                                                && intval($this->current_cost['st']) === 1
                                                && intval($tmp_fdata['price']['enable_st']) === 1) {
                                    /*cost estimate*/
                                    $form_cost_total += floatval($value) * floatval($tmp_fdata['price']['unit_price']);
                                }

                                    $tmp_options_row['amount'] = floatval($value) * floatval($tmp_fdata['price']['unit_price']);

                                // }
                                /*saving data to field array*/
                                $form_f_tmp[ $key ]['input']          = $tmp_options_row;
                                $form_f_tmp[ $key ]['input_cost_amt'] = floatval($value) * floatval($tmp_fdata['price']['unit_price']);
                                break;

                    case 40:
                        /*switch*/
                        $tmp_fdata = json_decode($tmp_field_name->data, true);

                                $tmp_options                       = array();
                                $tmp_field_label                   = ( ! empty($tmp_fdata['label']['text']) ) ? $tmp_fdata['label']['text'] : $tmp_field_name->fieldname;
                                $form_f_tmp[ $key ]['type']        = $tmp_field_name->type;
                                $form_f_tmp[ $key ]['fieldname']   = $tmp_field_name->fieldname;
                                $form_f_tmp[ $key ]['label']       = $tmp_field_label;
                                $form_f_tmp[ $key ]['price_st']    = isset($tmp_fdata['price']['enable_st']) ? $tmp_fdata['price']['enable_st'] : 0;
                                $form_f_tmp[ $key ]['lbl_show_st'] = isset($tmp_fdata['price']['lbl_show_st']) ? $tmp_fdata['price']['lbl_show_st'] : 0;

                                // foreach ($value as $key2=>$value2) {

                                if ( $value === 'on') {
                                    $tmp_options_row['label'] = ( ! empty($tmp_fdata['input15']['txt_yes']) ) ? $tmp_fdata['input15']['txt_yes'] : $value;
                                    $form_f_rec_tmp[ $key ]   = 1;
                                } else {
                                    $tmp_options_row['label'] = ( ! empty($tmp_fdata['input15']['txt_no']) ) ? $tmp_fdata['input15']['txt_no'] : $value;
                                    $form_f_rec_tmp[ $key ]   = 0;
                                }

                                if ( isset($tmp_fdata['price']['unit_price'])) {
                                    $tmp_options_row['cost']   = floatval($tmp_fdata['price']['unit_price']);
                                    $tmp_options_row['amount'] = $tmp_options_row['cost'];

                                    if ( isset($tmp_fdata['price']['enable_st'])
                                            && intval($this->current_cost['st']) === 1
                                            && intval($tmp_fdata['price']['enable_st']) === 1) {
                                        /*cost estimate*/
                                        $form_cost_total += $tmp_options_row['amount'];
                                    }
                                }

                                // }
                                /*saving data to field array*/
                                $form_f_tmp[ $key ]['input']          = $tmp_options_row;
                                $form_f_tmp[ $key ]['input_cost_amt'] = floatval($tmp_fdata['price']['unit_price']);
                                break;

                    case 41:
                        /*dyn checkbox*/
                    case 42:
                        /*dyn radiobtn*/
                        $tmp_fdata                         = json_decode($tmp_field_name->data, true);
                        $tmp_field_cost_total              = 0;
                        $tmp_options                       = array();
                        $tmp_field_label                   = ( ! empty($tmp_fdata['label']['text']) ) ? $tmp_fdata['label']['text'] : $tmp_field_name->fieldname;
                        $form_f_tmp[ $key ]['type']        = $tmp_field_name->type;
                        $form_f_tmp[ $key ]['fieldname']   = $tmp_field_name->fieldname;
                        $form_f_tmp[ $key ]['label']       = $tmp_field_label;
                        $form_f_tmp[ $key ]['price_st']    = isset($tmp_fdata['price']['enable_st']) ? $tmp_fdata['price']['enable_st'] : 0;
                        $form_f_tmp[ $key ]['lbl_show_st'] = isset($tmp_fdata['price']['lbl_show_st']) ? $tmp_fdata['price']['lbl_show_st'] : 0;

                        // for records
                        $tmp_summary = array();

                    foreach ( $value as $key2 => $value2) {
                        $tmp_summary_inner = '';

                        if ( isset($tmp_fdata['input17']['options'][ $key2 ]['label'])) {
                            $tmp_summary_inner .= $tmp_fdata['input17']['options'][ $key2 ]['label'];
                        }

                        if ( intval($value2) > 1) {
                            $tmp_summary_inner .= ' - qty: ' . $value2;
                        }
                        $tmp_summary[] = $tmp_summary_inner;
                    }

                    $form_f_rec_tmp[ $key ] = implode('^,^', $tmp_summary);
                        // end for records

                    foreach ( $value as $key2 => $value2) {
                        $tmp_options_row          = array();
                        $tmp_options_row['label'] = $tmp_fdata['input17']['options'][ $key2 ]['label'];

                        if ( $tmp_fdata['input17']['options'][ $key2 ]) {
                            $tmp_options_row['cost']   = floatval($tmp_fdata['input17']['options'][ $key2 ]['price']);
                            $tmp_options_row['qty']    = $value2;
                            $tmp_options_row['amount'] = floatval($value2) * floatval($tmp_fdata['input17']['options'][ $key2 ]['price']);

                            if ( isset($tmp_fdata['price']['enable_st'])
                                    && intval($this->current_cost['st']) === 1
                                    && intval($tmp_fdata['price']['enable_st']) === 1) {
                                /*cost estimate*/
                                $form_cost_total      += $tmp_options_row['amount'];
                                $tmp_field_cost_total += $tmp_options_row['amount'];
                            }
                        }

                        $tmp_options[] = $tmp_options_row;
                    }
                        /*saving data to field array*/
                        $form_f_tmp[ $key ]['input']          = $tmp_options;
                        $form_f_tmp[ $key ]['input_cost_amt'] = $tmp_field_cost_total;
                    break;

                    default:
                        $tmp_fdata                       = json_decode($tmp_field_name->data, true);
                        $form_f_tmp[$key]['type']      = $tmp_field_name->type;
                        $form_f_tmp[$key]['fieldname'] = $tmp_field_name->fieldname;
                        $tmp_field_label                 = (!empty($tmp_fdata['label']['text'])) ? $tmp_fdata['label']['text'] : $tmp_field_name->fieldname;
                        $form_f_tmp[$key]['label']     = $tmp_field_label;
                        if (is_array($value)) {
                            $tmp_options = array();
                            foreach ($value as $value2) {
                                $tmp_options[] = $value2;
                            }
                            $form_f_tmp[$key]['input'] = implode('^,^', $tmp_options);
                            // for records
                            $form_f_rec_tmp[$key] = implode('^,^', $tmp_options);
                        } else {
                            $form_f_tmp[$key]['input'] = $value;
                            // for records
                            $form_f_rec_tmp[$key] = $value;
                        }

                        break;
                }
            }
        }


        return [$form_f_tmp, $form_f_rec_tmp, $form_errors, $attachments, $attachment_status];
    }
    public function process_form($isMultiStep = false)
    {
        try {
            if ($isMultiStep) {
                $form_id         = ($_POST['_rockfm_form_parent_id']) ? Uiform_Form_Helper::sanitizeInput(trim($_POST['_rockfm_form_parent_id'])) : 0;
            } else {
                $form_id         = ($_POST['_rockfm_form_id']) ? Uiform_Form_Helper::sanitizeInput(trim($_POST['_rockfm_form_id'])) : 0;
            }
            $is_demo               = ($_POST['zgfm_is_demo']) ? intval(Uiform_Form_Helper::sanitizeInput(trim($_POST['zgfm_is_demo']))) : 0;
            $this->current_form_id = $form_id;
            $form_fields           = (isset($_POST['uiform_fields']) && $_POST['uiform_fields']) ? array_map(array('Uiform_Form_Helper', 'sanitizeRecursive_html'), $_POST['uiform_fields']) : array();
            $form_avars            = (isset($_POST['zgfm_avars']) && $_POST['zgfm_avars']) ? array_map(array('Uiform_Form_Helper', 'sanitizeRecursive_html'), $_POST['zgfm_avars']) : array();
            $form_f_tmp            = array();
            $form_f_rec_tmp        = array();
            $form_cost_total       = 0;
            $attachment_status     = 0;
            $attachments           = array();  // initialize attachment array
            // get data from form
            $form_data        = $this->formsmodel->getFormById_2($form_id);
            $form_data_onsubm = json_decode($form_data->fmb_data2, true);
            $form_data_name   = $form_data->fmb_name;

            // math formula result
            $zgfm_calc_math        = ($_POST['zgfm_calc_math']) ? Uiform_Form_Helper::sanitizeInput(trim($_POST['zgfm_calc_math'])) : 0;
            $form_data_calc_enable = (isset($form_data_onsubm['calculation']['enable_st'])) ? $form_data_onsubm['calculation']['enable_st'] : '0';

            // process fields
            $message_fields = '';
            $form_errors    = array();
            $mail_errors    = false;

            $this->current_cost['symbol'] = (isset($form_data_onsubm['main']['price_currency_symbol'])) ? $form_data_onsubm['main']['price_currency_symbol'] : '$';
            $this->current_cost['cur']    = (isset($form_data_onsubm['main']['price_currency'])) ? $form_data_onsubm['main']['price_currency'] : 'USD';
            $this->current_cost['st']     = (isset($form_data_onsubm['main']['price_st'])) ? $form_data_onsubm['main']['price_st'] : 'USD';

            // price numeric format
            $format_price_conf                        = array();
            $format_price_conf['price_format_st']     = (isset($form_data_onsubm['main']['price_format_st'])) ? $form_data_onsubm['main']['price_format_st'] : '0';
            $format_price_conf['price_sep_decimal']   = (isset($form_data_onsubm['main']['price_sep_decimal'])) ? $form_data_onsubm['main']['price_sep_decimal'] : '.';
            $format_price_conf['price_sep_thousand']  = (isset($form_data_onsubm['main']['price_sep_thousand'])) ? $form_data_onsubm['main']['price_sep_thousand'] : ',';
            $format_price_conf['price_sep_precision'] = (isset($form_data_onsubm['main']['price_sep_precision'])) ? $form_data_onsubm['main']['price_sep_precision'] : '2';

            // other variables
            $form_f_avar = array();

            if (!empty($form_avars)) {
                foreach ($form_avars as $key => $value) {
                    switch (strval($key)) {
                        case 'calc':
                            foreach ($value as $key2 => $value2) {
                                $form_f_avar['calc'][$key2] = $value2;
                            }
                            break;
                        default:
                            break;
                    }
                }
            }

            // fields
            if ($isMultiStep) {
                list($form_f_tmp, $form_f_rec_tmp, $form_errors, $attachments, $attachment_status) = [[], [], [], [], false];

                if (!empty($form_fields)) {
                    foreach ($form_fields as $key2 => $value2) {
                        list($form_f_tmp2, $form_f_rec_tmp2, $form_errors2, $attachments2, $attachment_status2) = $this->process_form_fields($value2, $key2);
                        
                        $newArray = [];
                        foreach ($form_f_tmp2 as $key3 => $value3) {
                            $newIndex = $key3.'_'.$key2;
                            $newArray[$newIndex] = $value3;
                        }
                        
                        $newArray2 = [];
                        foreach ($form_f_rec_tmp2 as $key3 => $value3) {
                            $newIndex = $key3.'_'.$key2;
                            $newArray2[$newIndex] = $value3;
                        }
                        
                        $form_f_tmp = array_merge($form_f_tmp, $newArray);
                        $form_f_rec_tmp = array_merge($form_f_rec_tmp, $newArray2);
                        $form_errors = array_merge($form_errors, $form_errors2);
                        $attachments = array_merge($attachments, $attachments2);
                        if ($attachment_status2 === true) {
                            $attachment_status =  true;
                        }
                    }
                }
            } else {
                // fields
                list($form_f_tmp, $form_f_rec_tmp, $form_errors, $attachments, $attachment_status) = $this->process_form_fields($form_fields, $form_id);
            }

            // process tax
            $tmp_price_tax_st  = (isset($form_data_onsubm['main']['price_tax_st'])) ? $form_data_onsubm['main']['price_tax_st'] : '0';
            $tmp_price_tax_val = (isset($form_data_onsubm['main']['price_tax_val'])) ? $form_data_onsubm['main']['price_tax_val'] : '0';
            $tmp_payment_st = (isset($form_data_onsubm['main']['price_st'])) ? $form_data_onsubm['main']['price_st'] : '0';

            // check if math calc is enabled

            if (intval($form_data_calc_enable) === 1  || intval($tmp_payment_st)===1) {
                $form_cost_total = isset($zgfm_calc_math) ? $zgfm_calc_math : 0;
            } 
            // check if tax is enabled
            if (intval($tmp_price_tax_st) === 1) {
                $form_cost_subtotal = floatval($form_cost_total);
                $form_cost_tax      = (floatval($tmp_price_tax_val) / 100) * floatval($form_cost_subtotal);
                $form_cost_total    = floatval($form_cost_subtotal) + $form_cost_tax;
            }

            // storing total cost
            $this->current_cost['total'] = $form_cost_total;

            if (count($form_errors) > 0) {
                $data                = array();
                $data['success']     = 0;
                $data['form_errors'] = count($form_errors);
                $tmp_err_msg         = '<ul>';
                foreach ($form_errors as $value_er) {
                    $tmp_err_msg .= '<li>' . $value_er . '</li>';
                }
                $tmp_err_msg           .= '</ul>';
                $tmp_err_msg            = Uiform_Form_Helper::assign_alert_container($tmp_err_msg, 4);
                $data['form_error_msg'] = $tmp_err_msg;
                $this->form_response    = $data;
                $data['form_error_msg'] = Uiform_Form_Helper::encodeHex($data['form_error_msg']);
                return $data;
            }

            // generate mail html part
            $tmp_data1                 = array();
            $tmp_data1['data']         = $form_f_tmp;
            $tmp_data1['price_tax_st'] = $tmp_price_tax_st;
            if (intval($tmp_price_tax_st) === 1) {
                $tmp_data1['form_cost_subtotal'] = $form_cost_subtotal;
                $tmp_data1['form_cost_tax']      = $form_cost_tax;
            }

            $tmp_data1['form_cost_total']       = $form_cost_total;
            $tmp_data1['current_cost_st']       = $this->current_cost['st'];
            $tmp_data1['current_cost_symbol']   = $this->current_cost['symbol'];
            $tmp_data1['current_cost_cur']      = $this->current_cost['cur'];
            $tmp_data1['format_price_conf']     = $format_price_conf;
            $tmp_data1['form_data_calc_enable'] = $form_data_calc_enable;
            if (intval($tmp_price_tax_st) === 1) {
                $tmp_data1['sub_total'] = Uiform_Form_Helper::cformat_numeric($format_price_conf, $form_cost_subtotal);
                $tmp_data1['tax']       = Uiform_Form_Helper::cformat_numeric($format_price_conf, $form_cost_tax);
            }
            $tmp_data1['total'] = Uiform_Form_Helper::cformat_numeric($format_price_conf, $form_cost_total) . ' ' . $this->current_cost['cur'];

            //$this->form_rec_msg_summ = self::render_template( 'formbuilder/views/frontend/mail_generate_fields.php', $tmp_data1, 'always' );

            // ending form process

            // save to form records
            $agent   = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
            $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';

            $form_f_rec_tmp  = $this->process_DataRecord($form_f_tmp, $form_f_rec_tmp);
            $form_f_avar_tmp = $this->process_DataRecord2($form_f_avar);

            $data                  = array();
            $data['fbh_data']      = json_encode($form_f_tmp);
            $data['fbh_data_rec']  = json_encode($form_f_rec_tmp);
            $data['fbh_data2']     = json_encode($form_f_avar);
            $data['fbh_data_rec2'] = json_encode($form_f_avar_tmp);

            $data['fbh_total_amount']  = $form_cost_total;
            $data['created_ip']        = $_SERVER['REMOTE_ADDR'];
            $data['form_fmb_id']       = $form_id;
            $data['fbh_data_rec_xml']  = Uiform_Form_Helper::array2xml($form_f_rec_tmp);
            $data['fbh_data_rec2_xml'] = Uiform_Form_Helper::array2xml($form_f_avar_tmp);
            $data['fbh_user_agent']    = $agent;
            $data['fbh_page']          = $_SERVER['REQUEST_URI'];
            $data['fbh_referer']       = $referer;

            // generate uniqueid
            if (isset($_COOKIE[UIFORM_FOLDER])) {
                $hash = $_COOKIE[UIFORM_FOLDER];
            } else {
                $hash = $this->uifm_get_newuser_hash();
            }

            $data['vis_uniqueid'] = $hash;

            $this->wpdb->insert($this->model_formrecords->table, $data);
            $idActivate     = $this->wpdb->insert_id;
            $json           = array();
            $json['status'] = 'created';
            $json['id']     = $idActivate;

            $this->flag_submitted          = $idActivate;
            self::$_form_data['form_id']   = $form_id;
            self::$_form_data['record_id'] = $idActivate;

            // update to payment records
            $data3                       = array();
            $data3['fbh_id']             = $idActivate;
            $data3['pgr_payment_amount'] = $form_cost_total;
            $data3['pgr_currency']       = $this->current_cost['cur'];
            $data3['flag_status']        = 1;
            $data3['created_ip']         = $_SERVER['REMOTE_ADDR'];
            $data3['created_by']         = 1;
            $data3['created_date']       = date('Y-m-d h:i:s');
            $data3['type_pg_id']         = 1;

            $this->wpdb->insert($this->model_gateways_rec->table, $data3);

            // is demo
            if ($is_demo === 0) {
                // preparing mail
                $mail_from_email = (isset($form_data_onsubm['onsubm']['mail_from_email'])) ? $form_data_onsubm['onsubm']['mail_from_email'] : '';
                $mail_from_name  = (isset($form_data_onsubm['onsubm']['mail_from_name'])) ? $form_data_onsubm['onsubm']['mail_from_name'] : '';

                $mail_html_wholecont = isset($form_data_onsubm['main']['email_html_fullpage']) ? $form_data_onsubm['main']['email_html_fullpage'] : '0';
                $mail_pdf_wholecont  = isset($form_data_onsubm['main']['pdf_html_fullpage']) ? $form_data_onsubm['main']['pdf_html_fullpage'] : '0';

                // admin
                // mail template
                $mail_template_msg = (isset($form_data_onsubm['onsubm']['mail_template_msg'])) ? urldecode($form_data_onsubm['onsubm']['mail_template_msg']) : '';
                $mail_template_msg = do_shortcode($mail_template_msg);
                $mail_template_msg = html_entity_decode($mail_template_msg, ENT_QUOTES, 'UTF-8');
                $mail_template_msg = self::render_template(
                    'formbuilder/views/frontend/mail_global_template.php',
                    array(
                        'content'        => $mail_template_msg,
                        'html_wholecont' => $mail_html_wholecont,
                    ),
                    'always'
                );

                $email_recipient = (isset($form_data_onsubm['onsubm']['mail_recipient'])) ? $form_data_onsubm['onsubm']['mail_recipient'] : get_option('admin_email');
                $email_cc        = (isset($form_data_onsubm['onsubm']['mail_cc'])) ? $form_data_onsubm['onsubm']['mail_cc'] : '';
                $email_bcc       = (isset($form_data_onsubm['onsubm']['mail_bcc'])) ? $form_data_onsubm['onsubm']['mail_bcc'] : '';
                $mail_subject    = (isset($form_data_onsubm['onsubm']['mail_subject'])) ? do_shortcode($form_data_onsubm['onsubm']['mail_subject']) : __('New form request', 'frocket_front');

                $mail_usr_recipient = (isset($form_data_onsubm['onsubm']['mail_usr_recipient'])) ? $form_data_onsubm['onsubm']['mail_usr_recipient'] : '';
                $mail_replyto       = (isset($form_data_onsubm['onsubm']['mail_replyto'])) ? $form_data_onsubm['onsubm']['mail_replyto'] : '';
                
                
                
                $data_mail                = array();
                $data_mail['from_mail']   = html_entity_decode(do_shortcode($mail_from_email));
                $data_mail['from_name']   = html_entity_decode(do_shortcode($mail_from_name));
                $data_mail['message']     = $mail_template_msg;
                $data_mail['subject']     = html_entity_decode($mail_subject);
                $data_mail['attachments'] = $attachments;
                $data_mail['to']          = trim($email_recipient);
                $data_mail['cc']          = array_map('trim', explode(',', $email_cc));
                $data_mail['bcc']         = array_map('trim', explode(',', $email_bcc));
                
                $tmp_replyto = $this->model_formrecords->getFieldOptRecord($idActivate, '', $mail_replyto, 'input');
                 
                if (!empty($tmp_replyto)) {
                    $data_mail['mail_replyto'] = $tmp_replyto;
                }

                if (isset($form_data_onsubm['main']['email_dissubm']) && intval($form_data_onsubm['main']['email_dissubm']) === 1) {
                    $mail_errors = false;
                } else {
                    $mail_errors = $this->process_mail($data_mail);
                }

                // customer
                // mail template
                $mail_usr_st = (isset($form_data_onsubm['onsubm']['mail_usr_st'])) ? $form_data_onsubm['onsubm']['mail_usr_st'] : '0';
                if (intval($mail_usr_st) === 1) {
                    $mail_template_msg = (isset($form_data_onsubm['onsubm']['mail_usr_template_msg'])) ? urldecode($form_data_onsubm['onsubm']['mail_usr_template_msg']) : '';
                    $mail_template_msg = do_shortcode($mail_template_msg);
                    $mail_template_msg = html_entity_decode($mail_template_msg, ENT_QUOTES, 'UTF-8');
                    $mail_template_msg = self::render_template(
                        'formbuilder/views/frontend/mail_global_template.php',
                        array(
                            'content'        => $mail_template_msg,
                            'html_wholecont' => $mail_html_wholecont,
                        ),
                        'always'
                    );

                    $mail_usr_cc      = (isset($form_data_onsubm['onsubm']['mail_usr_cc'])) ? $form_data_onsubm['onsubm']['mail_usr_cc'] : '';
                    $mail_usr_bcc     = (isset($form_data_onsubm['onsubm']['mail_usr_bcc'])) ? $form_data_onsubm['onsubm']['mail_usr_bcc'] : '';
                    $mail_usr_replyto = (isset($form_data_onsubm['onsubm']['mail_usr_replyto'])) ? $form_data_onsubm['onsubm']['mail_usr_replyto'] : '';
                    $mail_usr_subject = (isset($form_data_onsubm['onsubm']['mail_usr_subject'])) ? do_shortcode($form_data_onsubm['onsubm']['mail_usr_subject']) : __('New form request', 'frocket_front');

                    $mail_usr_pdf_st = (isset($form_data_onsubm['onsubm']['mail_usr_pdf_st'])) ? $form_data_onsubm['onsubm']['mail_usr_pdf_st'] : '0';
                    if (intval($mail_usr_pdf_st) === 1) {
                        $data_mail                              = array();
                        $mail_template_msg_pdf                  = (isset($form_data_onsubm['onsubm']['mail_usr_pdf_template_msg'])) ? urldecode($form_data_onsubm['onsubm']['mail_usr_pdf_template_msg']) : '';
                        $mail_template_msg_pdf                  = do_shortcode($mail_template_msg_pdf);
                        $data_mail['mail_usr_pdf_template_msg'] = $mail_template_msg_pdf;
                        $mail_pdf_fn                            = (isset($form_data_onsubm['onsubm']['mail_usr_pdf_fn'])) ? urldecode($form_data_onsubm['onsubm']['mail_usr_pdf_fn']) : '';
                        $mail_pdf_fn                            = do_shortcode($mail_pdf_fn);
                        $data_mail['mail_usr_pdf_fn']           = $mail_pdf_fn;
                        $data_mail['html_wholecont']            = $mail_pdf_wholecont;
                        $data_mail['rec_id']                    = $idActivate;
                        $data_mail['is_html']                   = 0;

                        $data_mail['charset']        = (isset($form_data_onsubm['main']['pdf_charset'])) ? $form_data_onsubm['main']['pdf_charset'] : '';
                        $data_mail['font']           = (isset($form_data_onsubm['main']['pdf_font'])) ? urldecode($form_data_onsubm['main']['pdf_font']) : '';
                        $data_mail['pdf_paper_size'] = (isset($form_data_onsubm['main']['pdf_paper_size'])) ? $form_data_onsubm['main']['pdf_paper_size'] : 'a4';
                        $data_mail['pdf_paper_orie'] = (isset($form_data_onsubm['main']['pdf_paper_orie'])) ? $form_data_onsubm['main']['pdf_paper_orie'] : 'landscape';

                        // $mail_pdf_font = (isset($form_data_onsubm['onsubm']['mail_usr_pdf_font'])) ? urldecode($form_data_onsubm['onsubm']['mail_usr_pdf_font']) : '';
                        // $data_mail['mail_usr_pdf_font']=$mail_pdf_font;
                        // $data_mail['mail_usr_pdf_charset']=(isset($form_data_onsubm['onsubm']['mail_usr_pdf_charset'])) ? $form_data_onsubm['onsubm']['mail_usr_pdf_charset'] : '';
                        if (ZIGAFORM_F_LITE != 1) {
                            $attachments[] = $this->process_custom_pdf($data_mail);
                        }
                    }

                    $data_mail                       = array();
                    $data_mail['from_mail']          = html_entity_decode(do_shortcode($mail_from_email));
                    $data_mail['from_name']          = html_entity_decode(do_shortcode($mail_from_name));
                    $data_mail['message']            = $mail_template_msg;
                    $data_mail['subject']            = html_entity_decode(do_shortcode($mail_usr_subject));
                    $data_mail['attachments']        = $attachments;
                    $data_mail['attachement_status'] = $attachment_status;
                    
                    $data_mail['to']  = $this->model_formrecords->getFieldOptRecord($idActivate, '', $mail_usr_recipient, 'input');
                     
                    $data_mail['cc']  = array_map('trim', explode(',', $mail_usr_cc));
                    $data_mail['bcc'] = array_map('trim', explode(',', $mail_usr_bcc));
                    if (!empty($mail_usr_replyto)) {
                        $data_mail['mail_replyto'] = $mail_usr_replyto;
                    }

                    if (isset($form_data_onsubm['main']['email_dissubm']) && intval($form_data_onsubm['main']['email_dissubm']) === 1) {
                        $mail_errors = false;
                    } else {
                        $mail_errors = $this->process_mail($data_mail);
                    }
                }
            }

            // success message

            $tmp_sm_successtext = (isset($form_data_onsubm['onsubm']['sm_successtext'])) ? urldecode($form_data_onsubm['onsubm']['sm_successtext']) : '';
            $tmp_sm_successtext = do_shortcode($tmp_sm_successtext);

            // url redirection
            $tmp_sm_redirect_st  = (isset($form_data_onsubm['onsubm']['sm_redirect_st'])) ? $form_data_onsubm['onsubm']['sm_redirect_st'] : '0';
            $tmp_sm_redirect_url = (isset($form_data_onsubm['onsubm']['sm_redirect_url'])) ? urldecode($form_data_onsubm['onsubm']['sm_redirect_url']) : '';

            $data                    = array();
            $data['success']         = 1;
            $data['show_message']    = $tmp_sm_successtext;
            $data['sm_redirect_st']  = $tmp_sm_redirect_st;
            $data['sm_redirect_url'] = $tmp_sm_redirect_url;
            $data['amount']          = $form_cost_total;
            $data['payment_st']      = (isset($form_data_onsubm['main']['payment_st'])) ? $form_data_onsubm['main']['payment_st'] : 0;
            $data['vis_uniqueid']    = $hash;
            $data['form_errors']     = 0;
            $data['form_id']         = $form_id;
            $data['mail_error']      = ($mail_errors) ? 1 : 0;
            $data['fbh_id']          = $idActivate;
            $data['currency']        = $this->current_cost;

            //self::$_modules['addon']['frontend']->addons_doActions( 'onSubmitForm_pos' );
            do_action('zgfm_onSubmitForm_pos', self::$_form_data['form_id'], self::$_form_data['record_id']);

            if (intval($data['payment_st']) === 1) {
                $id_payrec            = $this->wpdb->insert_id;
                $data['id_payrec']    = $id_payrec;
                $this->form_response  = $data;
                $data['payment_html'] = $this->get_payment_html();
                // generate new invoice records
            } else {
                $this->form_response = $data;
            }
            return $data;
        } catch (Exception $exception) {
            $data                = array();
            $data['success']     = 0;
            $data['form_errors'] = count($form_errors);
            $data['error_debug'] = __METHOD__ . ' error: ' . $exception->getMessage();
            $data['mail_error']  = ($mail_errors) ? 1 : 0;
            $this->form_response = $data;
            return $data;
        }
    }

    private function process_custom_pdf($data)
    {

        $output                  = '';
        $data2                   = array();
        $data2['rec_id']         = $data['rec_id'];
        $data2['content']        = $data['mail_usr_pdf_template_msg'];
        $data2['html_wholecont'] = $data['html_wholecont'];
        // $tmp_html = self::$_modules['formbuilder']['frontend']->pdf_global_template($data2);
        $output = uifm_generate_pdf($data2['content'], $data['mail_usr_pdf_fn'], $data['pdf_paper_size'], $data['pdf_paper_orie'], false);

        return $output;
    }

    public function pdf_show_record()
    {
        $rec_id  = isset($_GET['id']) ? Uiform_Form_Helper::sanitizeInput($_GET['id']) : '';
        $is_html = isset($_GET['is_html']) ? Uiform_Form_Helper::sanitizeInput($_GET['is_html']) : 0;

        $form_data = $this->model_formrecords->getFormDataById($rec_id);
        $this->current_form_id = $form_data->form_fmb_id;

        if (intval($rec_id) > 0) {
            ob_start();
            ?>

            <!-- if p tag is removed, title will dissapear, idk -->
            <h1><?php echo $form_data->fmb_name; ?></h1>
            <h4><?php echo __('Order summary', 'FRocket_admin'); ?></h4>

            <?php
            echo self::$_modules['formbuilder']['frontend']->get_summaryRecord($rec_id);
            ?>

            <?php
            $content = ob_get_contents();
            ob_end_clean();

            // update form id
            $this->flag_submitted = $rec_id;

            // custom template
            if (intval($form_data->fmb_rec_tpl_st) === 1) {
                $template_msg = do_shortcode($form_data->fmb_rec_tpl_html);
                $template_msg = html_entity_decode($template_msg, ENT_QUOTES, 'UTF-8');
                $content      = $template_msg;
            }

            $pos  = strpos($content, '</body>');
            $pos2 = strpos($content, '</html>');

            if ($pos === false && $pos2 === false) {
                $full_page = 0;
            } else {
                $full_page = 1;
                if (intval($is_html) === 1) {
                    $content = str_replace('</head>', '<script type="text/javascript" src="' . UIFORM_FORMS_URL . '/assets/frontend/js/iframe/4.1.1/iframeResizer.contentWindow.min.js"></script></head>', $content);
                }
            }

            $output                  = '';
            $data2                   = array();
            $data2['rec_id']         = $rec_id;
            $data2['html_wholecont'] = $full_page;
            $data2['content']        = $content;
            $data2['is_html']        = $is_html;
            $tmp_res                 = self::$_modules['formbuilder']['frontend']->pdf_global_template($data2);

            if (intval($is_html) === 1) {
                header('Content-type: text/html');

                echo $tmp_res['content'];
            } else {
                uifm_generate_pdf($tmp_res['content'], 'record_' . $rec_id, $tmp_res['pdf_paper_size'], $tmp_res['pdf_paper_orie'], true);
            }

            die();
        }
    }

    public function pdf_show_invoice()
    {

        $rec_id    = isset($_GET['id']) ? Uiform_Form_Helper::sanitizeInput($_GET['id']) : '';
        $form_data = $this->model_gateways_rec->getInvoiceDataByFormRecId($rec_id);
        $is_html   = isset($_GET['is_html']) ? Uiform_Form_Helper::sanitizeInput($_GET['is_html']) : 0;

        ob_start();
        ?>
        <link href="<?php echo UIFORM_FORMS_URL; ?>/assets/common/bootstrap/2.3.2/css/bootstrap.css" rel="stylesheet" type="text/css" media="all">

        <style type="text/css">
            .uifm_invoice_container h3 {
                margin-left: -20px;
            }

            .uifm_invoice_container .invoice_date {
                margin-left: -20px;
                margin-bottom: 20px;
            }

            .uifm_invoice_container {
                margin: 10px 20px 20px;
            }

            html,
            body {
                /*background:#eee;*/
            }

            table {
                background: #fff;
            }
        </style>

        <?php
        $head_extra = ob_get_contents();
        ob_end_clean();

        ob_start();
        ?>

        <!-- if p tag is removed, title will dissapear, idk -->
        <p>&nbsp;</p>
        <?php
        echo self::$_modules['formbuilder']['frontend']->get_summaryInvoice($rec_id);
        ?>

        <?php
        $content = ob_get_contents();
        ob_end_clean();

        // update form id
        $this->flag_submitted = $rec_id;

        // custom template
        if (intval($form_data->fmb_inv_tpl_st) === 1) {
            $template_msg = do_shortcode($form_data->fmb_inv_tpl_html);
            $template_msg = html_entity_decode($template_msg, ENT_QUOTES, 'UTF-8');
            $content      = $template_msg;
        }

        $pos  = strpos($content, '</body>');
        $pos2 = strpos($content, '</html>');

        if ($pos === false && $pos2 === false) {
            $full_page = 0;
        } else {
            $full_page = 1;
            if (intval($is_html) === 1) {
                $content = str_replace('</body>', '<script type="text/javascript" src="' . UIFORM_FORMS_URL . '/assets/frontend/js/iframe/4.1.1/iframeResizer.contentWindow.min.js"></script></body>', $content);
            }
        }

        $output              = '';
        $data2               = array();
        $data2['rec_id']     = $rec_id;
        $data2['head_extra'] = $head_extra;
        $data2['content']    = $content;
        $data2['is_html']    = $is_html;
        // $tmp_html = self::$_modules['formbuilder']['frontend']->pdf_global_template($data2);
        $tmp_res = self::$_modules['formbuilder']['frontend']->pdf_global_template($data2);
        if (intval($is_html) === 1) {
            header('Content-type: text/html');

            echo $tmp_res['content'];
        } else {
            uifm_generate_pdf($tmp_res['content'], 'invoice_' . $rec_id, $tmp_res['pdf_paper_size'], $tmp_res['pdf_paper_orie'], true);
        }

        die();
    }

    public function pdf_global_template($data)
    {

        $rec_id  = $data['rec_id'];
        $temp    = $this->model_formrecords->getFormDataById($rec_id);
        $form_id = $temp->form_fmb_id;

        $form_data        = $this->formsmodel->getFormById_2($form_id);
        $form_data_onsubm = json_decode($form_data->fmb_data2, true);
        $pdf_charset      = (isset($form_data_onsubm['main']['pdf_charset'])) ? $form_data_onsubm['main']['pdf_charset'] : '';
        $pdf_font         = (isset($form_data_onsubm['main']['pdf_font'])) ? urldecode($form_data_onsubm['main']['pdf_font']) : '';
        $pdf_paper_size   = (isset($form_data_onsubm['main']['pdf_paper_size'])) ? $form_data_onsubm['main']['pdf_paper_size'] : 'a4';
        $pdf_paper_orie   = (isset($form_data_onsubm['main']['pdf_paper_orie'])) ? $form_data_onsubm['main']['pdf_paper_orie'] : 'landscape';

        $data2                   = array();
        $data2['font']           = $pdf_font;
        $data2['charset']        = $pdf_charset;
        $data2['pdf_paper_size'] = $pdf_paper_size;
        $data2['pdf_paper_orie'] = $pdf_paper_orie;
        $data2['head_extra']     = isset($data['head_extra']) ? $data['head_extra'] : '';
        $data2['content']        = $data['content'];
        $data2['html_wholecont'] = isset($data['html_wholecont']) ? $data['html_wholecont'] : '0';
        $data2['is_html']        = isset($data['is_html']) ? $data['is_html'] : '0';
        $data2['content']        = self::render_template('formbuilder/views/forms/pdf_global_template.php', $data2);
        return $data2;
    }

    public function process_mail($data)
    {
        $mail_errors = false;
        // disable mail function
        if (defined('ZF_DISABLE_EMAIL') && ZF_DISABLE_EMAIL === true) {
            return $mail_errors;
        }
        /*getting admin mail*/
        $data['from_name'] = !empty($data['from_name']) ? $data['from_name'] : wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);

        $headers        = array();
        $message_format = 'html';
        $content_type   = $message_format == 'html' ? 'text/html' : 'text/plain';
        $headers[]      = 'MIME-Version: 1.0';
        $headers[]      = "Content-type: {$content_type}";
        $headers[]      = 'charset=' . get_option('blog_charset');
        $headers[]      = "From: \"{$data['from_name']}\" <{$data['from_mail']}>";
        if (
            !empty($data['mail_replyto'])
            && preg_match('/^[a-zA-Z0-9._+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/', $data['mail_replyto'])
        ) {
            $mail_replyto_name = substr($data['mail_replyto'], 0, strrpos($data['mail_replyto'], '@'));
            $headers[]         = "Reply-To: \"{$mail_replyto_name}\" <{$data['mail_replyto']}>";
            // $data['subject'].=" - ".$data['mail_replyto'];
        }
        // cc
        if (!empty($data['cc'])) {
            if (is_array($data['cc'])) {
                foreach ($data['cc'] as $value) {
                    if (preg_match('/^[a-zA-Z0-9._+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/', $value)) {
                        $headers[] = "Cc: {$value}";
                    }
                }
            }
        }

        // bcc
        if (!empty($data['bcc'])) {
            if (is_array($data['bcc'])) {
                foreach ($data['bcc'] as $value) {
                    if (preg_match('/^[a-zA-Z0-9._+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/', $value)) {
                        $headers[] = "Bcc: {$value}";
                    }
                }
            }
        }

        $to = trim($data['to']);

        if (preg_match('/^[a-zA-Z0-9._+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/', $to)) {
            if (!empty($data['attachments'])) {
                $mail_errors = wp_mail($to, $data['subject'], $data['message'], $headers, $data['attachments']);
            } else {
                $mail_errors = wp_mail($to, $data['subject'], $data['message'], $headers);
            }

            // pending option to delete attachment
            if (false && !empty($data['attachments'])) {
                foreach ($data['attachments'] as $attachment) {
                    @unlink($attachment); // delete files after sending them
                }
            }
        } else {
            $mail_errors = true;
        }

        return $mail_errors;
    }



    private function process_DataRecord($data1, $data2)
    {

        $data3 = array();

        if (!empty($data1)) {
            foreach ($data1 as $key => $value) {
                if (!empty($value) && is_array($value)) {
                    foreach ($value as $key2 => $value2) {
                        if (is_array($value2)) {
                            // index
                            $temp_input = array();
                            $temp_cost  = array();
                            $temp_qty   = array();
                            $temp_amt   = array();

                            if (is_array($value2)) {
                                foreach ($value2 as $key3 => $value3) {
                                    // values

                                    if (is_array($value3)) {
                                        foreach ($value3 as $key4 => $value4) {
                                            switch ($key4) {
                                                case 'label':
                                                    $temp_input[] = $value4;
                                                    break;
                                                case 'cost':
                                                    $temp_cost[] = $value4;
                                                    break;
                                                case 'qty':
                                                    $temp_qty[] = $value4;
                                                    break;
                                                case 'amount':
                                                    $temp_amt[] = $value4;
                                                    break;
                                                default:
                                            }
                                            $data3[$key . '_' . $key2 . '_' . $key3 . '_' . $key4] = $value4;
                                        }
                                    } else {
                                        $data3[$key . '_' . $key2 . '_' . $key3] = $value3;
                                    }
                                }
                            }

                            if (!empty($temp_input)) {
                                $data3[$key . '_input'] = implode('^,^', $temp_input);
                            }
                            if (!empty($temp_cost)) {
                                $data3[$key . '_cost'] = implode('^,^', $temp_cost);
                            }
                            if (!empty($temp_qty)) {
                                $data3[$key . '_qty'] = implode('^,^', $temp_qty);
                            }
                            if (!empty($temp_amt)) {
                                $data3[$key . '_amount'] = implode('^,^', $temp_amt);
                            }
                        } else {
                            $data3[$key . '_' . $key2] = $value2;
                        }
                    }
                }
            }
        }
        $data3 = array_merge($data3, $data2);

        return $data3;
    }

    private function process_DataRecord2($data1)
    {

        $data3 = array();
        if (!empty($data1)) {
            foreach ($data1 as $key => $value) {
                switch (strval($key)) {
                    case 'calc':
                        if (!empty($value) && is_array($value)) {
                            foreach ($value as $key2 => $value2) {
                                $data3[$key . '_' . $key2] = $value2;
                            }
                        }
                        break;
                    default:
                }
            }
        }

        return $data3;
    }

    private function get_payment_html()
    {

        $data             = array();
        $data['amount']   = (isset($this->form_response['amount'])) ? $this->form_response['amount'] : 0;
        $data['fbh_id']   = (isset($this->form_response['fbh_id'])) ? $this->form_response['fbh_id'] : '';
        $data['currency'] = (isset($this->form_response['currency'])) ? $this->form_response['currency'] : array();
        $gateways         = $this->model_gateways->getAvailableGateways();

        foreach ($gateways as $key => $value) {
            switch (intval($value->pg_id)) {
                case 1:
                    // offline
                    $pg_data                     = json_decode($value->pg_data, true);
                    $data2                       = array();
                    $data2['pg_name']            = (isset($value->pg_name)) ? $value->pg_name : '';
                    $data2['pg_description']     = (isset($value->pg_description)) ? $value->pg_description : '';
                    $data2['form_id']            = (isset($this->form_response['form_id'])) ? $this->form_response['form_id'] : '';
                    $data2['item_number']        = (isset($this->form_response['id_payrec'])) ? $this->form_response['id_payrec'] : '';
                    $data2['offline_return_url'] = isset($pg_data['offline_return_url']) ? $pg_data['offline_return_url'] : '';
                    $gateways[$key]->html_view = self::render_template('gateways/views/frontend/offline.php', $data2);

                    break;
                case 2:
                    // paypal
                    if (ZIGAFORM_F_LITE === 1) {
                        break 1;
                    }
                    $pg_data                    = json_decode($value->pg_data, true);
                    $data2                      = array();
                    $data2['amount']            = (isset($this->form_response['amount'])) ? $this->form_response['amount'] : 0;
                    $data2['amount']            = number_format(round($data2['amount'], 2, PHP_ROUND_HALF_EVEN), 2, '.', '');
                    $data2['vis_uniqueid']      = (isset($this->form_response['vis_uniqueid'])) ? $this->form_response['vis_uniqueid'] : '';
                    $data2['pg_name']           = (isset($value->pg_name)) ? $value->pg_name : '';
                    $data2['mod_test']          = (isset($value->pg_modtest)) ? $value->pg_modtest : 0;
                    $data2['pg_description']    = (isset($value->pg_description)) ? $value->pg_description : '';
                    $data2['item_number']       = (isset($this->form_response['id_payrec'])) ? $this->form_response['id_payrec'] : '';
                    $data2['paypal_email']      = (isset($pg_data['paypal_email'])) ? $pg_data['paypal_email'] : '';
                    $data2['paypal_return_url'] = isset($pg_data['paypal_return_url']) ? $pg_data['paypal_return_url'] : '';
                    $data2['paypal_cancel_url'] = isset($pg_data['paypal_cancel_url']) ? $pg_data['paypal_cancel_url'] : '';
                    $data2['paypal_currency']   = isset($pg_data['paypal_currency']) ? $pg_data['paypal_currency'] : '';
                    $data2['paypal_method']     = isset($pg_data['paypal_method']) ? $pg_data['paypal_method'] : 0;

                    if (intval($data2['paypal_method']) === 0) {
                        // get data from invoice
                        $form_rec_data = $this->model_gateways_rec->getInvoiceDataByFormRecId($data['fbh_id']);
                        $form_data     = json_decode($form_rec_data->fmb_data, true);

                        $form_data_calc_st = (isset($form_data['calculation']['enable_st'])) ? $form_data['calculation']['enable_st'] : '0';

                        if (intval($form_data_calc_st) === 1) {
                            // math calculation

                            $tmp_invoice_row                  = array();
                            $tmp_invoice_row['item_uniqueid'] = 0;
                            $tmp_invoice_row['item_id']       = 0;
                            $tmp_invoice_row['item_qty']      = 1;
                            $tmp_invoice_row['item_num']      = 1;
                            $tmp_invoice_row['item_name']     = (isset($form_data['calculation']['variables'][0]['tab_title'])) ? $form_data['calculation']['variables'][0]['tab_title'] : 'Main Calc Variable';
                            $tmp_invoice_row['item_amount']   = $form_rec_data->fbh_total_amount;
                            $new_record_user[]                = $tmp_invoice_row;
                        } else {
                            // processs tax
                            $form_data_tax_st  = (isset($form_data['main']['price_tax_st'])) ? $form_data['main']['price_tax_st'] : '0';
                            $form_data_tax_val = (isset($form_data['main']['price_tax_val'])) ? $form_data['main']['price_tax_val'] : '';

                            $tmp_amount_total = floatval($this->form_response['amount']);
                            if (isset($form_data_tax_st) && intval($form_data_tax_st) === 1) {
                                $tmp_tax                       = (floatval($form_data_tax_val) / 100);
                                $tmp_sub_total                 = ($tmp_amount_total) * (100 / (100 + (100 * $tmp_tax)));
                                $data['form_subtotal_amount'] = $tmp_sub_total;
                                $data['form_tax']             = $tmp_amount_total - $tmp_sub_total;
                            }

                            // process individuals
                            $name_fields      = $this->model_formrecords->getNameInvoiceField($data['fbh_id']);
                            $name_fields_check = array();
                            foreach ($name_fields as $value) {
                                $name_fields_check[$value->fmf_uniqueid]['fieldname'] = $value->fieldname;
                                $name_fields_check[$value->fmf_uniqueid]['id']        = $value->fmf_id;
                            }

                            $data_record     = $this->model_formrecords->getRecordById($data['fbh_id']);
                            $record_user     = json_decode($data_record->fbh_data, true);
                            $new_record_user = array();
                            $item_count      = 1;
                            foreach ($record_user as $key2 => $value) {
                                if (isset($name_fields_check[$key2]) && isset($value['price_st']) && intval($value['price_st']) === 1) {
                                    $field_name      = '';
                                    $field_id        = '';
                                    $tmp_invoice_row = array();

                                    $field_name = $name_fields_check[$key2]['fieldname'];
                                    $field_id   = $name_fields_check[$key2]['id'];

                                    $tmp_invoice_row['item_uniqueid'] = $key2;
                                    $tmp_invoice_row['item_id']       = $field_id;

                                    if (isset($value['price_st']) && intval($value['price_st']) === 1) {
                                        if (is_array($value['input'])) {
                                            if (isset($value['input']['amount'])) {
                                                $tmp_invoice_row['item_qty']    = 1;
                                                $tmp_invoice_row['item_num']    = $item_count;
                                                $tmp_invoice_row['item_name']   = $value['label'];
                                                $tmp_invoice_row['item_amount'] = $value['input']['amount'];
                                                $new_record_user[]              = $tmp_invoice_row;
                                                $item_count++;
                                            } else {
                                                foreach ($value['input'] as $key3 => $value2) {
                                                    $tmp_invoice_row['item_qty']  = 1;
                                                    $tmp_invoice_row['item_name'] = '';
                                                    $tmp_invoice_row['item_num']  = $item_count;
                                                    if (isset($value2['cost'])) {
                                                        if (isset($value2['qty'])) {
                                                            $tmp_invoice_row['item_qty']    = $value2['qty'];
                                                            $tmp_invoice_row['item_amount'] = $value2['cost'];
                                                        } else {
                                                            $tmp_invoice_row['item_amount'] = $value2['cost'];
                                                        }
                                                    }

                                                    $tmp_inp_label = $value['label'];
                                                    if (!empty($value2['label'])) {
                                                        $tmp_inp_label .= ' - ' . $value2['label'];
                                                    }
                                                    $tmp_invoice_row['item_name'] = $tmp_inp_label;

                                                    $new_record_user[] = $tmp_invoice_row;
                                                    $item_count++;
                                                }
                                            }
                                        } else {
                                            $tmp_invoice_row['item_qty']    = 0;
                                            $tmp_invoice_row['item_num']    = $item_count;
                                            $tmp_invoice_row['item_name']  .= ' ' . $value['input'];
                                            $tmp_invoice_row['item_amount'] = 0;
                                            $new_record_user[]              = $tmp_invoice_row;
                                            $item_count++;
                                        }
                                    }
                                }
                            }

                            // add tax
                            if (isset($form_data_tax_st) && intval($form_data_tax_st) === 1) {
                                $tmp_invoice_row                = array();
                                $tmp_invoice_row['item_qty']    = 1;
                                $tmp_invoice_row['item_num']    = $item_count;
                                $tmp_invoice_row['item_name']   = 'TAX';
                                $tmp_invoice_row['item_amount'] = $data['form_tax'];
                                $new_record_user[]              = $tmp_invoice_row;
                            }
                        }

                        $data2['paypal_individuals'] = $new_record_user;
                    }
                    $gateways[$key]->html_view = self::render_template('gateways/views/frontend/paypal.php', $data2);
                    break;
                default:
                    break;
            }
        }
        $data['gateways'] = $gateways;

        $output = self::render_template('formbuilder/views/frontend/payment_html.php', $data);
        return $output;
    }

    private function get_enqueue_files($id_form)
    {

        wp_register_script('rockfm-prev-jquery', UIFORM_FORMS_URL . '/assets/common/js/init.js', array('jquery'));
        wp_enqueue_script('rockfm-prev-jquery');

        wp_register_script('rockfm-wp-hooks', site_url() . '/wp-includes/js/dist/hooks.min.js', array());
        wp_enqueue_script('rockfm-wp-hooks');

        wp_register_script('rockfm-wp-i18n', site_url() . '/wp-includes/js/dist/i18n.min.js', array());
        wp_enqueue_script('rockfm-wp-i18n');

        // load resources
        $this->load_form_resources();

        // load rocket form
        wp_enqueue_script('rockfm-js_global');
        do_action('wp_enqueue_scripts');

        $result            = array();
        $result['scripts'] = array();
        $result['styles']  = array();

        // Print all loaded Scripts
        global $wp_scripts;
        $result['scripts']['base_url']    = $wp_scripts->base_url;
        $result['scripts']['content_url'] = $wp_scripts->content_url;

        $result['files'][] = '<script type="text/javascript" src="' . $result['scripts']['base_url'] . $wp_scripts->registered['jquery-core']->src . '"></script>';
        $result['files'][] = '<script type="text/javascript" src="' . $result['scripts']['base_url'] . $wp_scripts->registered['jquery-ui-core']->src . '"></script>';
        $temp = array();
        foreach ($wp_scripts->queue as $script) :
            if (Uiform_Form_Helper::isValidUrl_structure($wp_scripts->registered[$script]->src)) {
                if (
                    strpos($wp_scripts->registered[$script]->handle, 'rockfm-') !== false ||
                    strpos($wp_scripts->registered[$script]->handle, 'jquery-') !== false
                ) {
                    $result['files'][] = '<script type="text/javascript" src="' . $wp_scripts->registered[$script]->src . '"></script>';
                }
            }
        endforeach;

        global $wp_styles;
        $result['styles']['base_url']    = $wp_styles->base_url;
        $result['styles']['content_url'] = $wp_styles->content_url;
        foreach ($wp_styles->queue as $style) :
            //excepting block-library
            if (
                strpos($wp_styles->registered[$style]->src, 'dist/block-library') === false &&
                strpos($wp_styles->registered[$style]->src, get_template_directory_uri()) === false
            ) {
                $result['files'][] = '<link href="' . $wp_styles->registered[$style]->src . '" rel="stylesheet">';
            }
        endforeach;
        return $result;
    }

    public function get_form_iframe($id_form)
    {

        $rdata = $this->formsmodel->getAvailableFormById($id_form);
        if (empty($rdata)) {
            return '';
        }

        $shortcode_string = $rdata->fmb_html;

        $data = array();

        // load form variables
        $form_variables                      = array();
        $form_variables['url_site']          = site_url();
        $form_variables['ajax_nonce']        = wp_create_nonce('zgfm_ajax_nonce');
        $form_variables['ajaxurl']           = admin_url('admin-ajax.php');
        $form_variables['imagesurl']         = UIFORM_FORMS_URL . '/assets/frontend/images';

        $form_data_onsubm = json_decode($rdata->fmb_data2, true);
        $onload_scroll   = (isset($form_data_onsubm['main']['onload_scroll'])) ? $form_data_onsubm['main']['onload_scroll'] : '0';
        if (intval($onload_scroll) === 1) {
            $form_variables['_uifmvar']['fm_onload_scroll'] = 1;
        } else {
            $form_variables['_uifmvar']['fm_onload_scroll'] = 0;
        }

        $preload_noconflict = (isset($form_data_onsubm['main']['preload_noconflict'])) ? $form_data_onsubm['main']['preload_noconflict'] : '0';
        if (intval($preload_noconflict) === 1) {
            $form_variables['_uifmvar']['fm_preload_noconflict'] = 1;
        } else {
            $form_variables['_uifmvar']['fm_preload_noconflict'] = 0;
        }
        $form_variables['_uifmvar']['fm_loadmode']     = 'iframe';
        $form_variables['_uifmvar']['fm_modalmode_st'] = get_option('zgfm_b_modalmode', 0);

        $data['rockfm_vars_arr'] = $form_variables;

        $data['form_id'] = $id_form;
        // get enqueue files
        $data['head_files'] = $this->get_enqueue_files($id_form);
        $data['form_html']  = do_shortcode($shortcode_string);
        $data['imagesurl']  = UIFORM_FORMS_URL . '/assets/frontend/images';
        $output             = '';
        $output             = self::render_template('formbuilder/views/frontend/get_form_iframe.php', $data, 'always');

        return $output;
    }



    public function get_form_shortcode($attributes, $content = null)
    {
        try {
            // buffer 1
            ob_start();

            if (is_admin()) {
                return;
            }

            extract(
                shortcode_atts(
                    array(
                        'id'      => 1,
                        'ajax'    => false,
                        'lmode'   => 0,
                        'is_demo' => '0'
                    ),
                    $attributes
                )
            );

            switch (intval($lmode)) {
                case 1:
                    /*
                            iframe*/
                    // script

                    // load form variables
                    $form_variables                        = array();
                    $form_variables['_uifmvar']['id']      = $id;
                    $form_variables['_uifmvar']['addon']   = '';
                    $form_variables['_uifmvar']['is_demo'] = '';
                    $form_variables['_uifmvar']['is_mocking_submit'] = $is_mocking_submit;
                    if (UIFORM_DEBUG === 1) {
                        wp_enqueue_script('rockefform-iframe-script', UIFORM_FORMS_URL . '/assets/frontend/js/front.iframe.debug.js', array('jquery', self::PREFIX . 'rockefform-iframe'), '1', false);
                    } else {
                        wp_enqueue_script('rockefform-iframe-script', UIFORM_FORMS_URL . '/assets/frontend/js/front.iframe.min.js', array('jquery', self::PREFIX . 'rockefform-iframe'), '1', false);
                    }

                    wp_localize_script('rockefform-iframe-script', 'rockfm_vars', $form_variables);

                    $tmp_vars             = array();
                    $tmp_vars['base_url'] = UIFORM_FORMS_URL . '/';
                    $tmp_vars['form_id']  = $id;
                    $tmp_vars['url_form'] = site_url() . '/?uifm_costestimator_api_handler&zgfm_action=uifm_est_api_handler&uifm_action=1&uifm_mode=lmode&id=' . $id;
                    $output               = self::render_template('formbuilder/views/frontend/get_code_iframe.php', $tmp_vars, 'always');
                    break;
                case 2:
                    /*modal mode*/
                    $modalmode = get_option('zgfm_c_modalmode', 0);
                    if (intval($modalmode) === 0) {
                        echo __('<b>Alert!</b> Modal mode is not enabled on settings menu option', 'FRocket_admin');
                        return;
                    }
                    $shortcode_string = '';

                    $data_form = $this->formsmodel->getAvailableFormById($id);
                    if (empty($data_form)) {
                        return;
                    }
                    $shortcode_string = $data_form->fmb_html;
                    // load resources
                    $this->load_form_resources_alt($id, $is_demo);

                    // buffer 2
                    ob_start();
                    // check for external shortcodes
                    $shortcode_string = do_shortcode($shortcode_string);

                    echo $shortcode_string;
                    // buffer 4
                    ob_start();
                    ?>
                    <script type="text/javascript">
                        jQuery("#rockfm_form_<?php echo $id; ?>").ready(function() {
                            zgfm_front_helper.load_cssfiles(<?php echo $id; ?>);
                            rocketfm();
                            rocketfm.initialize();
                            rocketfm.setExternalVars();
                            rocketfm.loadform_init();
                        });
                    </script>
                    <?php
                    $js_string = ob_get_clean();
                    // end buffer 4

                    echo $js_string;
                    // end buffer 2
                    $output = ob_get_clean();
                    wp_enqueue_script('rockfm-extra-1', UIFORM_FORMS_URL . '/assets/frontend/js/extra-default.js', array('jquery'), UIFORM_VERSION, true);
                    break;
                default:
                    /*normal shortcode*/
                    $shortcode_string = '';

                    $data_form = $this->formsmodel->getAvailableFormById($id);

                    if (empty($data_form)) {
                        return;
                    }
                    $shortcode_string = $data_form->fmb_html;

                    $modalmode = get_option('zgfm_c_modalmode', 0);
                    if (intval($modalmode) === 0) {
                        // load resources
                        $this->load_form_resources();
                    }

                    // load resources
                    $this->load_form_resources_alt($id, $is_demo);

                    // buffer 2
                    ob_start();
                    // check for external shortcodes
                    $shortcode_string = do_shortcode($shortcode_string);
                    // adding alert message
                    if (
                        isset($_POST['_rockfm_type_submit'])
                        && absint($_POST['_rockfm_type_submit']) === 0
                        && absint($_POST['_rockfm_form_id']) === intval($id)
                    ) {
                        if (
                            isset($this->form_response['success'])
                            && intval($this->form_response['success']) === 1
                            && isset($this->flag_submitted) && intval($this->flag_submitted) > 0
                        ) {
                            echo (isset($_POST['_rockfm_onsubm_smsg'])) ? Uiform_Form_Helper::base64url_decode(Uiform_Form_Helper::sanitizeInput_html($_POST['_rockfm_onsubm_smsg'])) : __('Success! your form was submitted', 'frocket_front');
                        } else {
                            if (isset($this->form_response['form_errors']) && intval($this->form_response['form_errors']) > 0) {
                                echo '<div class="rockfm-alert-container uiform-wrap"><div class="rockfm-alert-inner" >' . $this->form_response['form_error_msg'] . '</div></div>';
                            } else {
                                echo Uiform_Form_Helper::assign_alert_container(__('warning! Form was not submitted', 'frocket_front'), 3);
                            }
                        }
                    }

                    echo $shortcode_string;

                    wp_enqueue_script('rockfm-extra-1', UIFORM_FORMS_URL . '/assets/frontend/js/extra-default.js', array('jquery'), UIFORM_VERSION, true);
                    // end buffer 2
                    $output = ob_get_clean();
                    if (ob_get_length() > 0) {
                        ob_end_clean();
                    }

                    if (!empty($shortcode_string)) {
                        $ip         = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
                        $user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
                        if (isset($_COOKIE[UIFORM_FOLDER])) {
                            $hash = $_COOKIE[UIFORM_FOLDER];
                        } else {
                            $hash = $this->uifm_get_newuser_hash();
                        }

                        $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
                        // there is not feature to manage log visit client
                        if (false) {
                            $data                   = array();
                            $data['fmb_id']         = $id;
                            $data['vis_uniqueid']   = $hash;
                            $data['vis_user_agent'] = $user_agent;
                            $data['vis_page']       = $_SERVER['REQUEST_URI'];
                            $data['vis_referer']    = $referer;
                            $data['vis_ip']         = $ip;
                            $data['vis_last_date']  = date('Y-m-d H:i:s');
                            $this->wpdb->insert($this->model_visitor->table, $data);
                        }
                    }
            }

            // checking errors
            // end buffer 1
            $output_error = ob_get_contents();
            if (ob_get_length() > 0) {
                ob_end_clean();
            }

            if (!empty($output_error)) {
                throw new Exception($output_error);
            }

            return $output;
        } catch (Exception $e) {
            $data             = array();
            $error            = array();
            $error['Message'] = $e->getMessage();
            $error['Trace']   = $e->getTrace();
            $ip               = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
            $user_agent       = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
            if (!isset($_COOKIE[UIFORM_FOLDER])) {
                $ip         = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
                $user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
                $hash       = hash('crc32', md5($ip . $user_agent));
                setcookie(UIFORM_FOLDER, $hash, time() + (60 * 60 * 24 * 30), '/');
            } else {
                $hash = $_COOKIE[UIFORM_FOLDER];
            }

            $referer                = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
            $data['vis_uniqueid']   = $hash;
            $data['vis_user_agent'] = $user_agent;
            $data['vis_page']       = $_SERVER['REQUEST_URI'];
            $data['vis_referer']    = $referer;
            $data['vis_error']      = Uiform_Form_Helper::array2xml($error);
            $data['vis_ip']         = $ip;
            $data['vis_last_date']  = date('Y-m-d H:i:s');
            $this->wpdb->insert($this->model_vis_error->table, $data);

            return '';
        }
    }

    public function load_form_resources()
    {

        if (is_admin()) {
            return;
        }

        /*
         load css */
        // loas ui
        wp_enqueue_style('jquery-ui');


        // bootstrap
        wp_enqueue_style('rockefform-bootstrap', UIFORM_FORMS_URL . '/assets/common/bootstrap/3.3.7/css/bootstrap-wrapper.css');
        wp_enqueue_style('rockefform-bootstrap-theme', UIFORM_FORMS_URL . '/assets/common/bootstrap/3.3.7/css/bootstrap-theme-wrapper.css');



        wp_enqueue_style('rockfm-fontawesome', UIFORM_FORMS_URL . '/assets/common/css/fontawesome/4.7.0/css/font-awesome.min-sfdc.css');

        // jasny bootstrap
        wp_enqueue_style('rockfm-jasny-bootstrap', UIFORM_FORMS_URL . '/assets/common/js/bjasny/jasny-bootstrap.css');
        // bootstrap slider
        wp_enqueue_style('rockfm-bootstrap-slider', UIFORM_FORMS_URL . '/assets/backend/js/bslider/4.12.1/bootstrap-slider.css');
        // bootstrap touchspin
        wp_enqueue_style('rockfm-bootstrap-touchspin', UIFORM_FORMS_URL . '/assets/backend/js/btouchspin/jquery.bootstrap-touchspin.css');
        // bootstrap datetimepicker
        wp_enqueue_style('rockfm-bootstrap-datetimepicker', UIFORM_FORMS_URL . '/assets/backend/js/bdatetime/4.17.45/bootstrap-datetimepicker.css');

        // bootstrap datetimepicker2
        wp_enqueue_style('rockfm-bootstrap-datetimepicker2', UIFORM_FORMS_URL . '/assets/common/js/flatpickr/4.6.2/flatpickr.min.css');

        // color picker
        wp_enqueue_style('rockfm-bootstrap-colorpicker', UIFORM_FORMS_URL . '/assets/backend/js/colorpicker/2.5/css/bootstrap-colorpicker.css');
        // bootstrap select
        wp_enqueue_style('rockfm-bootstrap-select', UIFORM_FORMS_URL . '/assets/common/js/bselect/1.12.4/css/bootstrap-select-mod.css');

        // bootstrap select 2
        wp_enqueue_style('rockfm-bootstrap-select2', UIFORM_FORMS_URL . '/assets/common/js/select2/4.0.13/css/select2.min.css');

        // star rating
        wp_enqueue_style('rockfm-star-rating', UIFORM_FORMS_URL . '/assets/backend/js/bratestar/star-rating.css');

        // bootstrap switch
        wp_enqueue_style('rockfm-bootstrap-switch', UIFORM_FORMS_URL . '/assets/backend/js/bswitch/bootstrap-switch.css');
        // blueimp
        wp_enqueue_style('rockfm-blueimp', UIFORM_FORMS_URL . '/assets/common/js/blueimp/2.16.0/css/blueimp-gallery.min.css');
        // bootstrap gallery
        wp_enqueue_style('rockfm-bootstrap-gal', UIFORM_FORMS_URL . '/assets/common/js/bgallery/3.1.3/css/bootstrap-image-gallery.css');
        // checkradio
        wp_enqueue_style('rockfm-checkradio', UIFORM_FORMS_URL . '/assets/common/js/checkradio/2.2.2/css/jquery.checkradios.css');

        // global
        if (UIFORM_DEBUG === 1) {
            wp_enqueue_style(self::PREFIX . 'rockfm_global', UIFORM_FORMS_URL . '/assets/frontend/css/front.debug.css?v=' . date('Ymdgis'), array(), UIFORM_VERSION, 'all');
        } else {
            wp_enqueue_style(self::PREFIX . 'rockfm_global', UIFORM_FORMS_URL . '/assets/frontend/css/front.min.css', array(), UIFORM_VERSION, 'all');
        }

        // load jquery
        wp_enqueue_script('jquery');

        // load jquery ui
        wp_enqueue_script('jquery-ui-core');
        wp_enqueue_script('jquery-ui-widget');
        wp_enqueue_script('jquery-ui-mouse');
        wp_enqueue_script('jquery-ui-resizable');
        wp_enqueue_script('jquery-ui-position');
        wp_enqueue_script('jquery-ui-sortable');
        wp_enqueue_script('jquery-ui-draggable');
        wp_enqueue_script('jquery-ui-droppable');
        wp_enqueue_script('jquery-ui-accordion');
        wp_enqueue_script('jquery-ui-autocomplete');
        wp_enqueue_script('jquery-ui-menu');
        wp_enqueue_script('jquery-ui-datepicker');
        wp_enqueue_script('jquery-ui-slider');
        wp_enqueue_script('jquery-ui-spinner');
        wp_enqueue_script('jquery-ui-button');
        // wp_enqueue_script('jquery-ui-tooltip');

        // bootstrap
        wp_enqueue_script('rockfm-bootstrap', UIFORM_FORMS_URL . '/assets/common/bootstrap/3.3.7/js/bootstrap-sfdc.js', array('jquery', 'rockfm-prev-jquery'), UIFORM_VERSION);

        // jasny bootstrap
        wp_enqueue_script('rockfm-jasny-bootstrap', UIFORM_FORMS_URL . '/assets/common/js/bjasny/jasny-bootstrap.js', array('jquery', 'rockfm-bootstrap'), '1.0', true);
        // bootstrap slider
        wp_enqueue_script('rockfm-bootstrap-slider', UIFORM_FORMS_URL . '/assets/backend/js/bslider/4.12.1/bootstrap-slider.js', array('jquery', 'rockfm-bootstrap'), '1.0', true);
        // bootstrap touchspin
        wp_enqueue_script('rockfm-bootstrap-touchspin', UIFORM_FORMS_URL . '/assets/backend/js/btouchspin/jquery.bootstrap-touchspin.js', array('jquery', 'rockfm-bootstrap'), '1.0', true);
        // bootstrap datetimepicker
        wp_enqueue_script('rockfm-bootstrap-dtpicker-locales', UIFORM_FORMS_URL . '/assets/backend/js/bdatetime/4.17.45/moment-with-locales.js', array('jquery', 'rockfm-bootstrap'), '1.0', true);
        wp_enqueue_script('rockfm-bootstrap-datetimepicker', UIFORM_FORMS_URL . '/assets/backend/js/bdatetime/4.17.45/bootstrap-datetimepicker.js', array('jquery', 'rockfm-bootstrap'), '1.0', true);

        // bootstrap datetimepicker
        wp_enqueue_script('rockfm-bootstrap-dtpicker-locales2', UIFORM_FORMS_URL . '/assets/common/js/flatpickr/4.6.2/flatpickr.js', array('jquery', 'rockfm-bootstrap'), '1.0', true);
        wp_enqueue_script('rockfm-bootstrap-datetimepicker2', UIFORM_FORMS_URL . '/assets/common/js/flatpickr/4.6.2/l10n/all-lang.js', array('jquery', 'rockfm-bootstrap'), '1.0', true);

        // star rating
        wp_enqueue_script('rockfm-star-rating', UIFORM_FORMS_URL . '/assets/backend/js/bratestar/star-rating.js', array('jquery', 'rockfm-bootstrap'), '1.0', true);
        // color picker
        wp_enqueue_script('rockfm-bootstrap-colorpicker', UIFORM_FORMS_URL . '/assets/backend/js/colorpicker/2.5/js/bootstrap-colorpicker.js', array('jquery', 'rockfm-bootstrap'), '1.0', true);
        // bootstrap select
        wp_enqueue_script('rockfm-bootstrap-select', UIFORM_FORMS_URL . '/assets/common/js/bselect/1.12.4/js/bootstrap-select-mod.js', array('jquery', 'rockfm-bootstrap'), '1.12.4', true);

        // select2
        wp_enqueue_script('rockfm-bootstrap-select2', UIFORM_FORMS_URL . '/assets/common/js/select2/4.0.13/js/select2.full.min.js', array('jquery', 'rockfm-bootstrap'), '4.0.13', true);

        // bootstrap switch
        wp_enqueue_script('rockfm-bootstrap-switch', UIFORM_FORMS_URL . '/assets/backend/js/bswitch/bootstrap-switch.js', array('jquery', 'rockfm-bootstrap'), '1.0', true);
        // form
        wp_enqueue_script('rockfm-jform', UIFORM_FORMS_URL . '/assets/frontend/js/jquery.form.js');
        // blueimp
        wp_enqueue_script('rockfm-blueimp', UIFORM_FORMS_URL . '/assets/common/js/blueimp/2.16.0/js/blueimp-gallery.min.js', array('jquery', 'rockfm-bootstrap'), '2.16.0', true);
        // bootstrap gallery
        wp_enqueue_script('rockfm-bootstrap-gal', UIFORM_FORMS_URL . '/assets/common/js/bgallery/3.1.3/js/bootstrap-image-gallery.js', array('jquery', 'rockfm-bootstrap', 'rockfm-blueimp'), '3.1.0', true);
        // accounting
        wp_enqueue_script('rockfm-accounting', UIFORM_FORMS_URL . '/assets/common/js/accounting/accounting.min.js', array('jquery', 'rockfm-bootstrap'), '1.0', true);
        // checkradio
        wp_enqueue_script('rockfm-checkradio', UIFORM_FORMS_URL . '/assets/common/js/checkradio/2.2.2/js/jquery.checkradios.js', array('jquery'), '2.2.2', true);

        /* load js */
        if (UIFORM_DEBUG === 1) {
            wp_enqueue_script('rockfm-js_global', UIFORM_FORMS_URL . '/assets/frontend/js/front.debug.js?v=' . date('Ymdgis'), array('rockfm-bootstrap', 'wp-i18n', 'wp-hooks'), UIFORM_VERSION, true);
        } else {
            wp_enqueue_script('rockfm-js_global', UIFORM_FORMS_URL . '/assets/frontend/js/front.min.js', array('rockfm-bootstrap', 'wp-i18n', 'wp-hooks'), UIFORM_VERSION, true);
        }
    }


    public function load_form_resources_alt($id, $is_demo = 0)
    {
        if (file_exists(WP_CONTENT_DIR . '/uploads/softdiscover/' . UIFORM_SLUG . '/css/rockfm_form' . $id . '.css')) {
            wp_register_style(self::PREFIX . 'rockfm_form' . $id, site_url() . '/wp-content/uploads/softdiscover/' . UIFORM_SLUG . '/css/rockfm_form' . $id . '.css?' . date('Ymdgis'), array(), UIFORM_VERSION, 'all');
            wp_enqueue_style(self::PREFIX . 'rockfm_form' . $id);
        } elseif (file_exists(WP_CONTENT_DIR . '/uploads/softdiscover/' . UIFORM_SLUG . '/rockfm_form' . $id . '.css')) {
            wp_register_style(self::PREFIX . 'rockfm_form' . $id, site_url() . '/wp-content/uploads/softdiscover/' . UIFORM_SLUG . '/rockfm_form' . $id . '.css?' . date('Ymdgis'), array(), UIFORM_VERSION, 'all');
            wp_enqueue_style(self::PREFIX . 'rockfm_form' . $id);
        } elseif (file_exists(UIFORM_FORMS_DIR . '/assets/frontend/css/rockfm_form' . $id . '.css')) {
            wp_register_style(self::PREFIX . 'rockfm_form' . $id, UIFORM_FORMS_URL . '/assets/frontend/css/rockfm_form' . $id . '.css?' . date('Ymdgis'), array(), UIFORM_VERSION, 'all');
            wp_enqueue_style(self::PREFIX . 'rockfm_form' . $id);
        }

        // load form variables
        $form_variables                        = array();
        $form_variables['_uifmvar']['is_demo'] = $is_demo;
        $form_variables['_uifmvar']['is_dev']  = UIFORM_DEBUG;

        wp_localize_script('rockfm-js_global', 'rockfm_vars', apply_filters('zgfm_front_initvar_load', $form_variables));
    }


    /**
     * show version.
     *
     * @author  Unknown
     * @since   v0.0.1
     * @version v1.0.0  Sunday, January 28th, 2024.
     * @access  public
     * @return  void
     */
    public function shortcode_show_version()
    {
        if (ZIGAFORM_F_LITE === 0) {
            $hideversion = get_option('zgfm_b_hideversion', 0);
            if (intval($hideversion) === 1) {
                return;
            }
        }

        $output  = '<noscript>';
        $output .= '<a href="https://softdiscover.com/zigaform/?uifmc_v=' . UIFORM_VERSION . '" title="WordPress Calculator & Cost Estimation" >ZigaForm </a> version ' . UIFORM_VERSION;
        $output .= '</noscript>';
        echo $output;
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
        } catch (Exception $exception) {
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
?>