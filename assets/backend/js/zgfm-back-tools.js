if (typeof $uifm === 'undefined') {
	$uifm = jQuery;
}
var zgfm_back_tools = zgfm_back_tools || null;
if (!$uifm.isFunction(zgfm_back_tools)) {
	(function ($, window) {
		'use strict';

		var zgfm_back_tools = function () {
			var zgfm_variable = [];
			zgfm_variable.innerVars = {};
			zgfm_variable.externalVars = {};

			this.initialize = function () {};

			this.setInnerVariable = function (name, value) {
				zgfm_variable.innerVars[name] = value;
			};
			this.getInnerVariable = function (name) {
				if (zgfm_variable.innerVars[name]) {
					return zgfm_variable.innerVars[name];
				} else {
					return '';
				}
			};

			/**
			 * generate pdf for email pdf attachement
			 */

			this.pdf_showsample = function (type) {
				var editor, email_template_pdf_msg;
				var pdf_fullpage = $('#uifm_frm_main_pdf_htmlfullpage').bootstrapSwitchZgpb('state') ? 1 : 0;
				var form_id = $('#uifm_frm_main_id').val() ? $('#uifm_frm_main_id').val() : 0;

				zgfm_back_tools.setInnerVariable('form_id', form_id);

				switch (String(type)) {
					case 'pdf_email_attach':
						if (typeof tinymce != 'undefined') {
							editor = tinymce.get('uifm_frm_email_usr_tmpl_pdf');
							if (editor && editor instanceof tinymce.Editor) {
								email_template_pdf_msg = tinymce.get('uifm_frm_email_usr_tmpl_pdf').getContent();
							} else {
								email_template_pdf_msg = $('#uifm_frm_email_usr_tmpl_pdf').val() ? $('#uifm_frm_email_usr_tmpl_pdf').val() : '';
							}
						}
						break;
					case 'pdf_invoice_gen':
						if (typeof tinymce != 'undefined') {
							editor = tinymce.get('uifm_frm_invoice_tpl_content');
							if (editor && editor instanceof tinymce.Editor) {
								email_template_pdf_msg = tinymce.get('uifm_frm_invoice_tpl_content').getContent();
							} else {
								email_template_pdf_msg = $('#uifm_frm_invoice_tpl_content').val() ? $('#uifm_frm_invoice_tpl_content').val() : '';
							}
						}
						break;
					case 'pdf_record_gen':
						if (typeof tinymce != 'undefined') {
							editor = tinymce.get('uifm_frm_record_tpl_content');
							if (editor && editor instanceof tinymce.Editor) {
								email_template_pdf_msg = tinymce.get('uifm_frm_record_tpl_content').getContent();
							} else {
								email_template_pdf_msg = $('#uifm_frm_record_tpl_content').val() ? $('#uifm_frm_record_tpl_content').val() : '';
							}
						}
						break;
				}
				this.pdf_processSample(email_template_pdf_msg, pdf_fullpage);
			};

			this.pdf_processSample = function (message, whole_control) {
				$.ajax({
					type: 'POST',
					url: ajaxurl,
					data: {
						action: 'rocket_fbuilder_pdf_showsample',
						page: 'zgfm_cost_estimate',
						zgfm_security: uiform_vars.ajax_nonce,
						full_page: whole_control,
						message: encodeURIComponent(message),
						form_id: zgfm_back_tools.getInnerVariable('form_id')
					},
					success: function (msg) {
						try {
							if (parseInt(msg.status) === 1) {
								//$("body").append("<iframe src='"+ msg.pdf_url+"' style='display: none;' ></iframe>");
								if (msg.pdf_name) {
									// use HTML5 a[download] attribute to specify filename
									var a = document.createElement('a');

									// safari doesn't support this yet
									if (typeof a.download === 'undefined') {
										window.location = msg.pdf_url;
									} else {
										a.href = msg.pdf_url;
										a.download = msg.pdf_name;
										document.body.appendChild(a);
										a.target = '_blank';
										a.click();
									}
								} else {
									window.location = msg.pdf_url;
								}
							} else {
								alert('Error! PDf was not generated');
							}
						} catch (ex) {
							alert('Error! PDf was not generated');
						}
					}
				});
			};

			this.email_sendsample = function (type) {
				var editor, email_template_msg;
				var html_wholecont = $('#uifm_frm_main_email_htmlfullpage').bootstrapSwitchZgpb('state') ? 1 : 0;
				var tmp_type;

				var email = $('#uifm_frm_email_recipient').val() || '';

				var filter = /^[\w\-\.\+]+\@[a-zA-Z0-9\.\-]+\.[a-zA-z0-9]{2,8}$/;
				if (filter.test(email)) {
				} else {
					alert('Email address needed. Fill "From mail" option.');
					return false;
				}

				switch (String(type)) {
					case 'email_admin_tmpl':
						if (typeof tinymce != 'undefined') {
							editor = tinymce.get('uifm_frm_email_tmpl');
							if (editor && editor instanceof tinymce.Editor) {
								email_template_msg = tinymce.get('uifm_frm_email_tmpl').getContent();
							} else {
								email_template_msg = $('#uifm_frm_email_tmpl').val() ? $('#uifm_frm_email_tmpl').val() : '';
							}
						}
						tmp_type = 1;

						break;
					case 'email_client_tmpl':
						if (typeof tinymce != 'undefined') {
							editor = tinymce.get('uifm_frm_email_usr_tmpl');
							if (editor && editor instanceof tinymce.Editor) {
								email_template_msg = tinymce.get('uifm_frm_email_usr_tmpl').getContent();
							} else {
								email_template_msg = $('#uifm_frm_email_usr_tmpl').val() ? $('#uifm_frm_email_usr_tmpl').val() : '';
							}
						}

						tmp_type = 2;
						break;
				}

				this.email_processSample(email_template_msg, html_wholecont, email);
			};

			this.email_processSample = function (message, whole_control, email_to) {
				$.ajax({
					type: 'POST',
					url: ajaxurl,
					data: {
						action: 'rocket_fbuilder_email_sendsample',
						page: 'zgfm_cost_estimate',
						zgfm_security: uiform_vars.ajax_nonce,
						full_page: whole_control,
						message: encodeURIComponent(message),
						email_to: email_to
					},
					success: function (msg) {
						if (parseInt(msg.status) === 1) {
							alert('Email test was sent to : ' + email_to);
						} else {
							alert('Error! Email test was not sent because of an error.');
						}
					}
				});
			};
		};
		window.zgfm_back_tools = zgfm_back_tools = $.zgfm_back_tools = new zgfm_back_tools();
	})($uifm, window);
}
