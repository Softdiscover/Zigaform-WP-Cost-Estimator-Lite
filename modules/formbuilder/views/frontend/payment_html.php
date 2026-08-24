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
?>
<div class="uiform-pg-main-page">
    <div class="">
        <div class="sfdc-row">
            <div class="sfdc-col-md-12">
                <h1 ><?php echo esc_html__('Choose Payment Method', 'frocket_front'); ?></h1>
            </div>
        </div>
        <div class="sfdc-row">
            <div class="sfdc-col-sm-7 col-md-8 uiform-pg-section-1">
                <div class="uiform-pg-table-header-box">
                    <div class="sfdc-col-md-5 pull-left">
                        <h4><?php echo esc_html__('Payment Option', 'frocket_front'); ?></h4>
                    </div>
                    <div class="sfdc-clearfix"></div>
                </div>
                <div class="uiform-pg-content">
                   <!-- content here -->
                   <?php
                    if ( ! empty($gateways)) {
                        foreach ( $gateways as $key => $value) {
                            if ( isset($value->html_view)) {
                                echo $value->html_view;
                            }
                        }
                    }
                    ?>
                   <!--\ content here -->

                    <div class="sfdc-clearfix"></div>
                </div>

            </div>

            <div class="uiform-pg-section-2 col-sm-5 col-md-4">
                <div class="row uiform-pg-summary-head">
                    <div class="sfdc-col-sm-12 ">
                        <h4><?php echo esc_html__('Order Summary', 'frocket_front'); ?></h4>
                    </div>
                </div>
                <div class="row uiform-pg-summary-body">
                    <div class="col-xs-10 col-xs-offset-1">


                        <p class="uiform-pg-summbox-p text-right">
                            <a
                                onclick="javascript:rocketfm.payment_seeSummary(this);return false;"
                                href="javascript:void(0);">
                                   <i class="fa fa-list-ul"></i> <?php echo esc_html__('See summary', 'frocket_front'); ?>
                                </a>
                        </p>


                          <?php if ( ZIGAFORM_C_LITE !== 1) { ?>
                            <p class="uiform-pg-summbox-p text-right">
                                <a
                                    onclick="javascript:rocketfm.payment_seeInvoice(this);return false;"
                                    href="javascript:void(0);">
                                       <i class="fa fa-file-text-o"></i> <?php echo esc_html__('See invoice', 'frocket_front'); ?>
                                    </a>

                            </p>
                          <?php } ?>


                        <p class="uiform-pg-summbox-p text-right"> <?php echo esc_html__('Total', 'frocket_front'); ?>:&nbsp;&nbsp;
                            <span class="uiform-pg-summbox-total "><?php echo esc_html(urldecode($currency['symbol'])); ?> <span class="uiform-pg-summbox-amount"> <?php echo esc_html(round($amount, 2)); ?></span> <?php echo esc_html($currency['cur']); ?></span>
                        </p>
                        <div style="display:none;" class="uiform-pg-summbox-agreement text-right">
                            <label class="">
                                <input type="checkbox"> <small>
                                    <?php echo esc_html__('Agree To The', 'frocket_front'); ?> <a target="_blank" href=""><?php echo esc_html__('Terms & Conditions', 'frocket_front'); ?></a></small>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="row uiform-pg-complete-box">

                            <a onclick="javascript:rocketfm.payment_completebtn(this);return false;"
                               href="javascript:void(0);"
                               class="sfdc-btn sfdc-btn-success sfdc-btn-lg">
                                <i class="fa fa-shopping-cart"></i> <?php echo esc_html__('Complete Order', 'frocket_front'); ?></a>
                </div>
            </div>
        </div>
    </div>
    <div style="display:none">
        <input type="hidden" class="uifm_pg_msg_selectpay" value="<?php echo esc_attr__('Select Payment', 'frocket_front'); ?>" >
    </div>
    <input type="hidden" class="_uifm_pg_record_id" value="<?php echo esc_attr($fbh_id); ?>">
    <input type="hidden" class="_uifm_record_nonce" value="<?php echo esc_attr($record_nonce); ?>">
</div>
