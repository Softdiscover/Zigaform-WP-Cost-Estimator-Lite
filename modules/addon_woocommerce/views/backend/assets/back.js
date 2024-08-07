if (typeof $uifm === 'undefined') {
	$uifm = jQuery;
}
var zgfm_back_addon_woocomm = zgfm_back_addon_woocomm || null;
if (!$uifm.isFunction(zgfm_back_addon_woocomm)) {
	(function($, window) {
		'use strict';

		var zgfm_fn_woocomm = function() {
			var variable = [];
			variable.innerVars = {};
			variable.externalVars = {};

			var defaults = {
				data: {
					status: '0',
					prod_id: '',
					wc_quantity: '',
					summ_status: '0',
					summ_title: 'summary',
					summ_content:
						'<div style="overflow-x:auto;background:#eee;clear:both;"> <table cellpadding="0" cellspacing="0" style="margin:0px;"> <tr class="details"> <td> Check </td><td> 0 </td></tr><tr class="heading"> <td> <b>Item</b> </td><td> <b>Price</b> </td></tr><tr class="item"> <td> Product 1 </td><td> $0 </td></tr><tr class="item"> <td> Service 1 </td><td> $0 </td></tr><tr class="item last"> <td> Product 2 </td><td> $0 </td></tr><tr class="total"> <td></td><td> Total: $0 </td></tr></table></div>',
				},
			};

			var settings = $.extend(true, {}, defaults);

			this.initialize = function() {};

			this.dump_data = function() {
				console.log(this.dumpvar3(settings));
			};

			this.refresh_options = function() {
				//show options
				let tmp_wc_quantity = settings['data']['wc_quantity'];

				//load all fields
				var tmp_options = this.dataFields_load();

				//fill select list of form values
				$('#woocmc_quantity').html('');
				$('#woocmc_quantity').append(
					$('<option></option>')
						.attr('value', '1')
						.text('None')
				);
				$.each(tmp_options, function(key2, value2) {
					$('#woocmc_quantity').append(
						$('<option></option>')
							.attr('value', value2['id'])
							.attr('data-type', value2['type'])
							.text(value2['name'])
					);
				});

				$('#woocmc_quantity').val(tmp_wc_quantity);

				//load events
				this.load_events();
			};

			this.load_settings = function() {
				var idform = $('#uifm_frm_main_id').val();
				$.ajax({
					type: 'POST',
					url: ajaxurl,
					data: {
						action: 'zgfm_back_woocommerce_load_settings',
						page: 'zgfm_form_builder',
						zgfm_security: uiform_vars.ajax_nonce,
						form_id: parseInt(idform),
					},
					success: function(msg) {
						//load data
						if (msg.data.status) {
							settings = $.extend(true, {}, defaults, { data: msg.data });
						} else {
							settings = $.extend(true, {}, defaults);
						}

						//show options
						zgfm_back_addon_woocomm.show_options();

						//load events
						zgfm_back_addon_woocomm.load_events_once();
					},
				});
			};

			this.load_events_once = function() {
				$('.woocomm-input').on('change', function(e) {
					if (e) {
						e.stopPropagation();
						e.preventDefault();
					}

					var f_store = $(e.target).data('options');
					var f_val = $(e.target).val();

					zgfm_back_addon_woocomm.update_settings(f_store, f_val);
				});

				/* tinymce text*/
				$('#woocmc_summ_content').on('change keyup paste', function(e) {
					// your code here
					var tab_opt = $(e.target).attr('id');
					var tmp_content = $(e.target).val();
					var tmp_id = $('#uifm-field-selected-id').val();

					var f_store = 'summ_content';
					var f_val = tmp_content;

					zgfm_back_addon_woocomm.update_settings(f_store, f_val);
				});
			};

			this.load_events = function() {
				$('.woocomm-input').on('change', function(e) {
					if (e) {
						e.stopPropagation();
						e.preventDefault();
					}

					var f_store = $(e.target).data('options');
					var f_val = $(e.target).val();

					zgfm_back_addon_woocomm.update_settings(f_store, f_val);
				});
			};

			/*
			 * receeive tinymce data
			 */
			this.tinyMCE_onChange = function(args) {
				if (args['textarea_id'] === 'woocmc_summ_content') {
					var f_store = 'summ_content';
					var f_val = args['textarea_content'];

					zgfm_back_addon_woocomm.update_settings(f_store, f_val);
				}
			};

			this.update_settings = function(f_store, value) {
				var opt1, opt2, opt3, tmp_store;
				var f_id = $('#uifm-field-selected-id').val();
				var f_step = $('#' + f_id)
					.closest('.uiform-step-pane')
					.data('uifm-step');

				tmp_store = f_store.split('-');
				opt1 = tmp_store[0];
				opt2 = tmp_store[1];
				opt3 = tmp_store[2];

				switch (String(opt1)) {
					case 'status':
						settings['data']['status'] = value;
						break;
					case 'prod_id':
						settings['data']['prod_id'] = value;
						break;

					case 'wc_quantity':
						settings['data']['wc_quantity'] = value;
						break;
					case 'summ_status':
						settings['data']['summ_status'] = value;
						break;
					case 'summ_title':
						settings['data']['summ_title'] = value;
						break;
					case 'summ_content':
						settings['data']['summ_content'] = value;
						break;
				}
			};

			this.show_options = function() {
				//load settings on tab

				let tmp_status = settings['data']['status'];

				if (parseInt(tmp_status) === 1) {
					$('#woocmc_status_2').prop('checked', true);
				} else {
					$('#woocmc_status_1').prop('checked', true);
				}

				let tmp_url = settings['data']['prod_id'];
				$('#woocmc_prod_id').val(tmp_url);

				let tmp_wc_quantity = settings['data']['wc_quantity'];

				//load all fields
				var tmp_options = this.dataFields_load();

				//fill select list of form values
				$('#woocmc_quantity').html('');
				$('#woocmc_quantity').append(
					$('<option></option>')
						.attr('value', '1')
						.text('None')
				);
				$.each(tmp_options, function(key2, value2) {
					$('#woocmc_quantity').append(
						$('<option></option>')
							.attr('value', value2['id'])
							.attr('data-type', value2['type'])
							.text(value2['name'])
					);
				});

				$('#woocmc_quantity').val(tmp_wc_quantity);

				let tmp_status_2 = settings['data']['summ_status'];

				if (parseInt(tmp_status_2) === 1) {
					$('#woocmc_summstatus_2').prop('checked', true);
				} else {
					$('#woocmc_summstatus_1').prop('checked', true);
				}

				let tmp_summ_title = settings['data']['summ_title'];
				$('#woocmc_summ_title').val(tmp_summ_title);

				let tmp_summ_content = settings['data']['summ_content'] || '';
				if (typeof tinymce != 'undefined') {
					var editor = tinymce.get('woocmc_summ_content');

					if (editor && editor instanceof tinymce.Editor) {
						var content = tmp_summ_content;
						editor.setContent(content, { format: 'html' });
					} else {
						$('textarea#woocmc_summ_content').val(tmp_summ_content);
					}
				} else {
				}

				$('#woocmc_summ_content').val(tmp_summ_content);
			};

			/*
			 * execute action after creating field
			 */
			this.onFieldCreation_post = function() {
				//load fields
				zgfm_back_addon_woocomm.refresh_options();
			};

			this.get_currentDataToSave = function(result) {
				result['woocommerce'] = settings['data'];
				return result;
			};

			this.dataFields_load = function() {
				var tmp_fields = rocketform.get_coreData();
				var tmp_options = [];
				var tmp_inneropts = {};
				if (
					parseInt(
						$.map(tmp_fields['steps_src'], function(n, i) {
							return i;
						}).length
					) != 0
				) {
					$.each(tmp_fields['steps_src'], function(index3, value3) {
						$.each(value3, function(index4, value4) {
							if (parseInt($('#' + index4).length) != 0) {
								switch (parseInt(value4['type'])) {
									case 6:
									case 7:
									case 16:
									case 18:
										tmp_inneropts = {};
										tmp_inneropts['id'] = value4['id'];
										tmp_inneropts['name'] = value4['field_name'];
										tmp_inneropts['type'] = value4['type'];
										tmp_options.push(tmp_inneropts);
										break;
								}
							}
						});
					});
				}

				return tmp_options;
			};

			this.dev_show_vars = function() {
				console.log(this.dumpvar3(settings));
			};

			this.setExternalVars = function() {};
			this.getExternalVars = function(name) {
				if (variable.externalVars[name]) {
					return variable.externalVars[name];
				} else {
					return '';
				}
			};
			this.setInnerVariable = function(name, value) {
				variable.innerVars[name] = value;
			};

			this.getInnerVariable = function(name) {
				if (variable.innerVars[name]) {
					return variable.innerVars[name];
				} else {
					return '';
				}
			};

			this.dumpvar3 = function(object) {
				return JSON.stringify(object, null, 2);
			};
			this.dumpvar2 = function(object) {
				return JSON.stringify(object);
			};

			this.dumpvar = function(object) {
				var seen = [];
				var json = JSON.stringify(object, function(key, val) {
					if (val != null && typeof val == 'object') {
						if (seen.indexOf(val) >= 0) return;
						seen.push(val);
					}
					return val;
				});
				return seen;
			};
		};
		window.zgfm_back_addon_woocomm = zgfm_back_addon_woocomm = $.zgfm_back_addon_woocomm = new zgfm_fn_woocomm();

		//adding hook
		const { addFilter, addAction } = wp.hooks;
		//before submit form
		addAction('zgfm.onLoadForm_loadAddon', 'zgfm_back_addon_woocomm/load_settings', zgfm_back_addon_woocomm.load_settings);
		addAction('zgfm.onLoadForm_loadAddon', 'zgfm_back_addon_woocomm/onFieldCreation_post', zgfm_back_addon_woocomm.onFieldCreation_post);
		addFilter('zgfm.getData_beforeSubmitForm', 'zgfm_back_addon_woocomm/get_currentDataToSave', zgfm_back_addon_woocomm.get_currentDataToSave);
		addAction('zgfm.tinyMCE_onChange', 'zgfm_back_addon_woocomm/tinyMCE_onChange', zgfm_back_addon_woocomm.tinyMCE_onChange);
	})($uifm, window);
}
