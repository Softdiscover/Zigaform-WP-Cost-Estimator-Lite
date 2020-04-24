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
	exit( 'No direct script access allowed' );}
?>

<div class="sfdc-row">
	<div class="sfdc-col-md-12">
		<div class="uifm_frm_calc_options_container">
			<div class="sfdc-row ">
				<div class="sfdc-col-md-6">
					<div class="sfdc-form-group">
						<div class="sfdc-col-md-4">
							<label for=""><?php echo __( 'Temporal variable title', 'FRocket_admin' ); ?></label>
						</div>
						<div class="sfdc-col-md-8">
							<input type="text" 
								   onkeyup="javascript:zgfm_back_calc.calc_tab_changeTitle(this);"
								   onfocus="javascript:zgfm_back_calc.calc_tab_changeTitle(this);"
								   onchange="javascript:zgfm_back_calc.calc_tab_changeTitle(this);"
								   class="sfdc-form-control uifm_frm_calc_tabtitle" 
								   placeholder="" readonly="readonly" value="Main">
						</div>    

					</div>
				</div>
				<div class="sfdc-col-md-6">
					<a class="sfdc-btn sfdc-btn-danger sfdc-pull-right" onclick="javascript:zgfm_back_calc.calc_delete_tab();" href="javascript:void(0);">
						  <?php echo __( 'Delete', 'FRocket_admin' ); ?> </a>
				</div>
			</div>
			<div class="space10"></div>
			
		</div>
	</div>
	<div class="sfdc-col-md-12">
		<textarea class="uifm_frm_calc_content autogrow" 
				  data-num="0"
		 style="width: 100%; height: 534px;" 
		 name="uifm_frm_calc_content0" id="uifm_frm_calc_content0"></textarea>
	</div>
	<div class="sfdc-col-md-12">
		<div class="space10"></div>
		<div class="sfdc-alert sfdc-alert-info">
			
			<div id="uifm_frm_calc_content0_showvars" class="uifm_frm_calc_showvars">
				<h3><?php echo __( 'Form Variables', 'FRocket_admin' ); ?></h3>
				<ul>
					
				</ul>
			</div>
		</div>
		
	</div>
</div>
