jQuery(document).ready(function($) {

	var stripe_profile_objs = {
		container			: jQuery('#wlmtnmce-stripe-profile-lightbox'),
		shortcode_text 	    : jQuery('#wlmtnmce-stripe-profile-lightbox').find('.wlmtnmcelbox-preview-text'),
		insertcode_button 	: jQuery('#wlmtnmce-stripe-profile-lightbox').find('.wlmtnmcelbox-insertcode'),
		content_levels 	    : jQuery('#wlmtnmce-stripe-profile-lightbox').find('.wlmtnmcelbox-levels'),
		lightbox_close 	    : jQuery('#wlmtnmce-stripe-profile-lightbox').find('.media-modal-close'),

		showlevels 	    	: jQuery('#wlmtnmce-stripe-profile-lightbox').find('.wlmtnmcelbox-showlevels'),
		includepost 	    	: jQuery('#wlmtnmce-stripe-profile-lightbox').find('.wlmtnmcelbox-includepost'),

		init	: function() {
			stripe_profile_objs.content_levels.chosen( { width:'100%', display_disabled_options:false, } );
			stripe_profile_objs.content_levels.chosen().change( stripe_profile_objs.chosen_change );
			stripe_profile_objs.insertcode_button.on('click', stripe_profile_objs.insertcode );
			stripe_profile_objs.lightbox_close.on('click', stripe_profile_objs.close );

			stripe_profile_objs.showlevels.on('click', stripe_profile_objs.showlevels_click );
			stripe_profile_objs.includepost.on('click', stripe_profile_objs.includepost_click );

			//assign the show in this function so that we can call it using our wlmtnmcelbox_vars global object
			wlmtnmcelbox_vars.stripe_profile_objs = stripe_profile_objs.show_lightbox;
		},

		show_lightbox       : function() {
			stripe_profile_objs.container.show();
			stripe_profile_objs.container.find('.media-modal').show();
			stripe_profile_objs.container.find('.media-modal-backdrop').show();

			stripe_profile_objs.showlevels.prop('checked', true);
			stripe_profile_objs.includepost.prop('checked', false);
			stripe_profile_objs.update_preview();
		},

		update_preview		: function() {
			var selected_levels = "";
			var include_posts = "";
			if ( stripe_profile_objs.showlevels.is(':checked') ) {
				jQuery(".wlmtnmcelbox-levels :selected").each(function(){
					selected_levels = selected_levels + (selected_levels == "" ? "":",") + jQuery.trim(jQuery(this).val());
				});
				if ( selected_levels == "") {
					selected_levels = "all";
				}
			} else {
				selected_levels = "no";
			}

			if ( stripe_profile_objs.includepost.is(':checked') ) {
				include_posts = "yes"
			} else {
				include_posts = "no"
			}

			if( selected_levels == "no" && include_posts == "no" ) {
				stripe_profile_objs.shortcode_text.val('');
				return;
			}
			var text = "[wlm_stripe_profile levels='" +selected_levels +"' include_posts='" +include_posts +"']";
			stripe_profile_objs.shortcode_text.val(text);
		},

		chosen_change		: function() {
			var str_selected = jQuery(this).val();
			if( str_selected != null ){
				pos = str_selected.lastIndexOf("all");
				if ( pos >= 0 ) {
					jQuery(this).find('option').each(function() {
						if(jQuery(this).val() == "all"){
							jQuery(this).prop("selected",false);
						}else{
							jQuery(this).prop("selected","selected");
						}
						jQuery(this).trigger("chosen:updated");
					});
				}
			}
			stripe_profile_objs.update_preview();
		},

		showlevels_click 		: function() {
			if ( jQuery(this).is(':checked') ) {
				stripe_profile_objs.content_levels.find('option').each(function() {
					jQuery(this).prop("disabled",false);
				});
				stripe_profile_objs.content_levels.prop('disabled', false).trigger("chosen:updated");
			} else {
				stripe_profile_objs.content_levels.find('option').each(function() {
					jQuery(this).prop("selected",false);
					jQuery(this).prop("disabled",true);
				});
				stripe_profile_objs.content_levels.prop('disabled', true).trigger("chosen:updated");
			}

			stripe_profile_objs.update_preview();
		},

		includepost_click 		: function() {
			stripe_profile_objs.update_preview();
		},

		insertcode		: function() {
			var text = stripe_profile_objs.shortcode_text.val().replace(/\r\n|\r|\n/g,"<br/>");
		    if (tinyMCE && tinyMCE.activeEditor && text != '') {
		   		tinyMCE.activeEditor.execCommand('mceInsertContent', false, text);
		   		stripe_profile_objs.shortcode_text.val('');
		    }
		    stripe_profile_objs.reset();
		    stripe_profile_objs.close();
		},

		reset	: function() {
			stripe_profile_objs.content_levels.find('option').each(function() {
				jQuery(this).prop("selected",false);
			});
			stripe_profile_objs.showlevels.prop('checked', false);
			stripe_profile_objs.includepost.prop('checked', false);
			stripe_profile_objs.shortcode_text.val('');
		},

		close	: function() {
			stripe_profile_objs.container.hide();
			stripe_profile_objs.container.find('.media-modal').hide();
			stripe_profile_objs.container.find('.media-modal-backdrop').hide();
		}
	};
	stripe_profile_objs.init();

	var paypal_inserter = {
		container           : jQuery('#wlmtnmce-paypal-lightbox'),
		shortcode_text      : jQuery('#wlmtnmce-paypal-lightbox .wlmtnmcelbox-preview-text'),
		insertcode_button   : jQuery('#wlmtnmce-paypal-lightbox .wlmtnmcelbox-insertcode'),
		content_text        : jQuery('#wlmtnmce-paypal-lightbox .wlmtnmcelbox-content-text'),
		lightbox_title      : jQuery('#wlmtnmce-paypal-lightbox .media-frame-title'),
		lightbox_close      : jQuery('#wlmtnmce-paypal-lightbox .media-modal-close'),
		shortcode           : '',
		products            : '',

		init                : function () {
			//assign the show in this function so that we can call it using our wlmtnmcelbox_vars global object
			wlmtnmcelbox_vars.show_paypalps_inserter_lightbox = paypal_inserter.show_ps_lightbox;
			wlmtnmcelbox_vars.show_paypalec_inserter_lightbox = paypal_inserter.show_ec_lightbox;
			wlmtnmcelbox_vars.show_paypalpro_inserter_lightbox = paypal_inserter.show_pro_lightbox;
			wlmtnmcelbox_vars.show_paypalpayflow_inserter_lightbox = paypal_inserter.show_payflow_lightbox;

			paypal_inserter.lightbox_close.on('click', paypal_inserter.close );
			paypal_inserter.insertcode_button.on('click', paypal_inserter.insertcode );
			paypal_inserter.container.find('.shortcode-fields').on('change', function() {
				paypal_inserter.update_preview();
			});
			paypal_inserter.container.find('.wlmtnmcelbox-buttons').on('change',paypal_inserter.button_select);
			paypal_inserter.container.find('.wlmtnmcelbox-button-options').on('change',paypal_inserter.button_preview);
			paypal_inserter.container.find('.spb_options').on('change',paypal_inserter.button_preview);
		},

		button_select       : function() {
			paypal_inserter.container.find('.wlmtnmcelbox-button-options').hide();
			var val = paypal_inserter.container.find('select.wlmtnmcelbox-buttons').val();
			if(!val) return;
			paypal_inserter.container.find('.wlmtnmcelbox-button-options.'+val).show();
			paypal_inserter.container.find('.wlmtnmcelbox-button-preview.'+val+'.s').show();

			paypal_inserter.update_button_preview(val);
		},

		button_preview      : function() {
			paypal_inserter.update_button_preview(paypal_inserter.container.find('select.wlmtnmcelbox-buttons').val());
			paypal_inserter.update_preview();
		},

		update_button_preview      : function (chosen_button) {
			paypal_inserter.container.find('.wlmtnmcelbox-button-preview *').hide();
			$('#paypalec-spb-preview').empty();
			if('enable' in paypalec_spb && parseInt(paypalec_spb.enable) && paypal_inserter.shortcode == 'wlm_paypalec_btn') {
				// spb
				var settings = {
					env : 'sandbox',
					style : {
						layout : $('select[name="layout"]').val(),
						size : $('select[name="size"]').val(),
						shape : $('select[name="shape"]').val(),
						color : $('select[name="color"]').val(),
					},
					funding : {
						allowed : $('[name="funding"]:checked').map(function() {return paypal.FUNDING[this.value]}).get(),
						disallowed : $('[name="funding"]:not(:checked)').map(function() {return paypal.FUNDING[this.value]}).get(),
					},
					payment : function() {},
					onAuthorize : function() {},
				}
				paypal.Button.render(settings, '#paypalec-spb-preview');
			} else {
				// none spb
				switch(chosen_button) {
					case 'plain_text':
						paypal_inserter.container.find('.wlmtnmcelbox-button-preview input.'+chosen_button).val(paypal_inserter.container.find(':input.wlmtnmcelbox-button-options.'+chosen_button).val());
						paypal_inserter.container.find('.wlmtnmcelbox-button-preview .'+chosen_button+'.s').show();
					break;
					case 'custom_image':
						paypal_inserter.container.find('.wlmtnmcelbox-button-preview .'+chosen_button).prop('src', paypal_inserter.container.find(':input.wlmtnmcelbox-button-options.'+chosen_button).val());
						paypal_inserter.container.find('.wlmtnmcelbox-button-preview .'+chosen_button+'.s').show();
					break;
					default:
						// paypal_inserter.container.find(':input.wlmtnmcelbox-button-options.'+chosen_button).prop('selectedIndex', 0);
						paypal_inserter.container.find('.wlmtnmcelbox-button-preview .'+chosen_button+'.'+paypal_inserter.container.find(':input.wlmtnmcelbox-button-options.'+chosen_button).val()).show();
				}
			}
		},

		show_ps_lightbox    : function () {
			jQuery('#wlmtnmce-paypal-lightbox').attr('class', 'wlmtnmcelbox paypalps');

			paypal_inserter.lightbox_title.find('h1').text('PayPal Payments Standard Shortcode Generator');
			paypal_inserter.shortcode = 'wlm_paypalps_btn';
			paypal_inserter.products = 'paypalpsproducts';
			paypal_inserter.show_lightbox();
		},
		show_ec_lightbox    : function () {
			jQuery('#wlmtnmce-paypal-lightbox').attr('class', 'wlmtnmcelbox paypalec');

			paypal_inserter.lightbox_title.find('h1').text('PayPal Checkout Shortcode Generator');
			paypal_inserter.shortcode = 'wlm_paypalec_btn';
			paypal_inserter.products = 'paypalecproducts';
			if(!paypalec_spb_default_set) {
				jQuery('select[name="layout"]').val(paypalec_spb.layout);
				jQuery('select[name="size"]').val(paypalec_spb.size);
				jQuery('select[name="shape"]').val(paypalec_spb.shape);
				jQuery('select[name="color"]').val(paypalec_spb.color);
				jQuery('[name="funding"]').val(paypalec_spb.funding);
				paypalec_spb_default_set = true;
			}
			if(parseInt(paypalec_spb.enable)) {
				jQuery('#wlmtnmce-paypal-lightbox').addClass('spb');
			}

			paypal_inserter.show_lightbox();
		},
		show_pro_lightbox   : function () {
			jQuery('#wlmtnmce-paypal-lightbox').attr('class', 'wlmtnmcelbox paypalpro');

			paypal_inserter.lightbox_title.find('h1').text('PayPal Pro Shortcode Generator');
			paypal_inserter.shortcode = 'wlm_paypalpro_btn';
			paypal_inserter.products = 'paypalproproducts';
			paypal_inserter.show_lightbox();
		},

		show_payflow_lightbox   : function () {
			jQuery('#wlmtnmce-paypal-lightbox').attr('class', 'wlmtnmcelbox paypalpayflow');

			paypal_inserter.lightbox_title.find('h1').text('PayPal Payflow Shortcode Generator');
			paypal_inserter.shortcode = 'wlm_payflow_btn';
			paypal_inserter.products = 'paypalpayflowproducts';
			paypal_inserter.show_lightbox();
		},

		show_lightbox       : function () {
			paypal_inserter.prep_options();
			paypal_inserter.container.show();
			paypal_inserter.container.find('.media-modal').show();
			paypal_inserter.container.find('.media-modal-backdrop').show();
			paypal_inserter.button_preview();
			paypal_inserter.update_preview();
		},

		prep_options        : function () {
			var select = paypal_inserter.container.find('select.wlmtnmcelbox-products');
			select.find('.pp-product').remove();
			jQuery.each(wlm_paypal_products[paypal_inserter.products],function(index, product) {
				select.append(jQuery('<option>', {value : index, class : 'pp-product'}).text(product.name));
			});
		},

		reset               : function () {
			paypal_inserter.container.find('.wlmtnmcelbox-products').val('');
		},

		close               : function () {
			paypal_inserter.container.hide();
			paypal_inserter.container.find('.media-modal').hide();
			paypal_inserter.container.find('.media-modal-backdrop').hide();
		},

		insertcode          : function () {
			var text = paypal_inserter.shortcode_text.val().trim();
			var product=paypal_inserter.container.find('.wlmtnmcelbox-products');

			if(product.val()!='' && text != '') {
			    if (tinyMCE && tinyMCE.activeEditor) {
			   		tinyMCE.activeEditor.execCommand('mceInsertContent', false, text);
			   		paypal_inserter.shortcode_text.val('');
			    }
			}

		    paypal_inserter.reset();
		    paypal_inserter.close();
		},

		update_preview      : function () {
			var preview_box = jQuery(paypal_inserter.shortcode_text);
			var product=paypal_inserter.container.find('.wlmtnmcelbox-products');

			if(product.val() == '') {
				preview_box.val('Please select a product.');
				return;
			}

			if('enable' in paypalec_spb && parseInt(paypalec_spb.enable) && paypal_inserter.shortcode == 'wlm_paypalec_btn') {
				// spb
				var options = {};
				jQuery(':input.spb_options').each(function() {
					if(!(this.name in options)) {
						options[this.name] = [];
					}
					if(this.type == 'checkbox' && !this.checked) return true;
					options[this.name].push(this.value);
				});

				preview_box.val(
					'['
					+paypal_inserter.shortcode
					+' name="'
					+paypal_inserter.container.find('.wlmtnmcelbox-products option:selected').text()
					+'" sku="'
					+product.val()
					+'" layout="'
					+options.layout[0]
					+'" size="'
					+options.size[0]
					+'" shape="'
					+options.shape[0]
					+'" color="'
					+options.color[0]
					+'" funding="'
					+options.funding.join(',')
					+'"]'
				);

			} else {
				// none spb

				var chosen_button = paypal_inserter.container.find('select.wlmtnmcelbox-buttons').val();

				var btn_value = '';

				switch(chosen_button) {
					case 'plain_text':
					case 'custom_image':
						btn_value = paypal_inserter.container.find(':input.wlmtnmcelbox-button-options.'+chosen_button).val();
					break;
					default:
						btn_value = chosen_button+':'+paypal_inserter.container.find(':input.wlmtnmcelbox-button-options.'+chosen_button).val();
				}

				preview_box.val(
					'['
					+paypal_inserter.shortcode
					+' name="'
					+paypal_inserter.container.find('.wlmtnmcelbox-products option:selected').text()
					+'" sku="'
					+product.val()
					+'" btn="'
					+btn_value
					+'"]'
				);
			}

		}
	}
	paypal_inserter.init();

	var private_tags_objs = {
		container			: jQuery('#wlmtnmce-private-tags-lightbox'),
		shortcode_text 	    : jQuery('#wlmtnmce-private-tags-lightbox').find('.wlmtnmcelbox-preview-text'),
		insertcode_button 	: jQuery('#wlmtnmce-private-tags-lightbox').find('.wlmtnmcelbox-insertcode'),
		reverse 			: jQuery('#wlmtnmce-private-tags-lightbox').find('.wlmtnmcelbox-reverse'),
		content_text 		: jQuery('#wlmtnmce-private-tags-lightbox').find('.wlmtnmcelbox-content-text'),
		content_levels 	    : jQuery('#wlmtnmce-private-tags-lightbox').find('.wlmtnmcelbox-levels'),
		lightbox_close 	    : jQuery('#wlmtnmce-private-tags-lightbox').find('.media-modal-close'),

		show_lightbox       : function() {
			private_tags_objs.container.show();
			private_tags_objs.container.find('.media-modal').show();
			private_tags_objs.container.find('.media-modal-backdrop').show();
			var t = "";
			if ( typeof tinyMCE == 'object' ) {
				t = tinyMCE.activeEditor.selection.getContent();
			}
			private_tags_objs.content_text.val(t);
			private_tags_objs.shortcode_text.val('');
		},

		update_preview		: function() {
			var selected = "";
			jQuery(".wlmtnmcelbox-levels :selected").each(function(){
				selected = selected + (selected == "" ? "":"|") + jQuery.trim(jQuery(this).html());
			});
			if ( selected == "") {
				private_tags_objs.shortcode_text.val('');
			 	return;
			}
			var reverse = private_tags_objs.reverse.attr('checked') ? '!' : '';
			var text = "[" +reverse +'wlm_private "' +selected +'"]' +private_tags_objs.content_text.val() +"[/" +reverse +"wlm_private]";
			private_tags_objs.shortcode_text.val(text);
		},

		chosen_change		: function() {
			var str_selected = jQuery(this).val();
			if( str_selected != null ){
				pos = str_selected.lastIndexOf("all");
				if ( pos >= 0 ) {
					jQuery(this).find('option').each(function() {
						if(jQuery(this).val() == "all"){
							jQuery(this).prop("selected",false);
						}else{
							jQuery(this).prop("selected","selected");
						}
						jQuery(this).trigger("chosen:updated");
					});
				}
			}
			private_tags_objs.update_preview();
		},

		insertcode		: function() {
			var text = private_tags_objs.shortcode_text.val().replace(/\r\n|\r|\n/g,"<br/>");
		    if (tinyMCE && tinyMCE.activeEditor && text != '') {
		   		tinyMCE.activeEditor.execCommand('mceInsertContent', false, text);
		   		private_tags_objs.shortcode_text.val('');
		    }
		    private_tags_objs.reset();
		    private_tags_objs.close();
		},

		reset	: function() {
			private_tags_objs.shortcode_text.val('');
			private_tags_objs.content_text.val('')
		},

		close	: function() {
			private_tags_objs.container.hide();
			private_tags_objs.container.find('.media-modal').hide();
			private_tags_objs.container.find('.media-modal-backdrop').hide();
		},

		init	: function() {
			private_tags_objs.content_levels.chosen( { width:'100%', display_disabled_options:false, } );
			private_tags_objs.content_levels.chosen().change( private_tags_objs.chosen_change );
			private_tags_objs.reverse.on('click', private_tags_objs.update_preview );
			private_tags_objs.content_text.on('keyup', private_tags_objs.update_preview );
			private_tags_objs.insertcode_button.on('click', private_tags_objs.insertcode );
			private_tags_objs.lightbox_close.on('click', private_tags_objs.close );

			//assign the show in this function so that we can call it using our wlmtnmcelbox_vars global object
			wlmtnmcelbox_vars.show_private_tags_lightbox = private_tags_objs.show_lightbox;
		}
	};
	private_tags_objs.init();

	var reg_form_objs = {
		container			: jQuery('#wlmtnmce-reg-form-lightbox'),
		shortcode_text 	    : jQuery('#wlmtnmce-reg-form-lightbox').find('.wlmtnmcelbox-preview-text'),
		insertcode_button 	: jQuery('#wlmtnmce-reg-form-lightbox').find('.wlmtnmcelbox-insertcode'),
		reverse 			: jQuery('#wlmtnmce-reg-form-lightbox').find('.wlmtnmcelbox-reverse'),
		content_text 		: jQuery('#wlmtnmce-reg-form-lightbox').find('.wlmtnmcelbox-content-text'),
		content_levels 	    : jQuery('#wlmtnmce-reg-form-lightbox').find('.reg-form-wlmtnmcelbox-levels'),
		lightbox_close 	    : jQuery('#wlmtnmce-reg-form-lightbox').find('.media-modal-close'),

		show_lightbox       : function() {
			reg_form_objs.container.show();
			reg_form_objs.container.find('.media-modal').show();
			reg_form_objs.container.find('.media-modal-backdrop').show();
			var t = "";
			if ( typeof tinyMCE == 'object' ) {
				t = tinyMCE.activeEditor.selection.getContent();
			}
			reg_form_objs.content_text.val(t);
			reg_form_objs.shortcode_text.val('');
			reg_form_objs.update_preview();
		},

		update_preview		: function() {
			var selected = "";
			jQuery(".reg-form-wlmtnmcelbox-levels :selected").each(function(){
				selected = jQuery.trim(jQuery(this).html());
			});
			if ( selected == "") {
				reg_form_objs.shortcode_text.val('');
			 	return;
			}
			var text = "[" +'wlm_register "' +selected +'"]';
			reg_form_objs.shortcode_text.val(text);
		},

		chosen_change		: function() {
			var str_selected = jQuery(this).val();
			if( str_selected != null ){
				pos = str_selected.lastIndexOf("all");
				if ( pos >= 0 ) {
					jQuery(this).find('option').each(function() {
						if(jQuery(this).val() == "all"){
							jQuery(this).prop("selected",false);
						}else{
							jQuery(this).prop("selected","selected");
						}
						jQuery(this).trigger("chosen:updated");
					});
				}
			}
			reg_form_objs.update_preview();
		},

		insertcode		: function() {
			var text = reg_form_objs.shortcode_text.val().replace(/\r\n|\r|\n/g,"<br/>");
		    if (tinyMCE && tinyMCE.activeEditor && text != '') {
		   		tinyMCE.activeEditor.execCommand('mceInsertContent', false, text);
		   		reg_form_objs.shortcode_text.val('');
		    }
		    reg_form_objs.reset();
		    reg_form_objs.close();
		},

		reset	: function() {
			reg_form_objs.shortcode_text.val('');
			reg_form_objs.content_text.val('')
		},

		close	: function() {
			reg_form_objs.container.hide();
			reg_form_objs.container.find('.media-modal').hide();
			reg_form_objs.container.find('.media-modal-backdrop').hide();
		},

		init	: function() {
			reg_form_objs.content_levels.chosen( { width:'100%', display_disabled_options:false, } );
			reg_form_objs.content_levels.chosen().change( reg_form_objs.chosen_change );
			reg_form_objs.reverse.on('click', reg_form_objs.update_preview );
			reg_form_objs.content_text.on('keyup', reg_form_objs.update_preview );
			reg_form_objs.insertcode_button.on('click', reg_form_objs.insertcode );
			reg_form_objs.lightbox_close.on('click', reg_form_objs.close );

			//assign the show in this function so that we can call it using our wlmtnmcelbox_vars global object
			wlmtnmcelbox_vars.show_reg_form_lightbox = reg_form_objs.show_lightbox;
		}
	};
	reg_form_objs.init();

	var scheduler_form_objs = {
		container			: jQuery('#wlmtnmce-scheduler-lightbox'),
		shortcode_text 	    : jQuery('#wlmtnmce-scheduler-lightbox').find('.wlmtnmcelbox-preview-text'),
		insertcode_button 	: jQuery('#wlmtnmce-scheduler-lightbox').find('.wlmtnmcelbox-insertcode'),

		content_title 		: jQuery('#wlmtnmce-scheduler-lightbox').find('.scheduler-wlmtnmcelbox-title'),
		content_ptype 	    : jQuery('#wlmtnmce-scheduler-lightbox').find('.scheduler-wlmtnmcelbox-ptype'),
		content_showpost 	    : jQuery('#wlmtnmce-scheduler-lightbox').find('.scheduler-wlmtnmcelbox-showpost'),
		content_px 	    : jQuery('#wlmtnmce-scheduler-lightbox').find('.scheduler-wlmtnmcelbox-px'),
		content_separator 	    : jQuery('#wlmtnmce-scheduler-lightbox').find('.scheduler-wlmtnmcelbox-separator'),
		content_sort 	    : jQuery('#wlmtnmce-scheduler-lightbox').find('.scheduler-wlmtnmcelbox-sort'),
		content_showdate 	    : jQuery('#wlmtnmce-scheduler-lightbox').find('.scheduler-wlmtnmcelbox-showdate'),
		content_showtime 	    : jQuery('#wlmtnmce-scheduler-lightbox').find('.scheduler-wlmtnmcelbox-showtime'),
		lightbox_close 	    : jQuery('#wlmtnmce-scheduler-lightbox').find('.media-modal-close'),

		show_lightbox       : function() {
			scheduler_form_objs.container.show();
			scheduler_form_objs.container.find('.media-modal').show();
			scheduler_form_objs.container.find('.media-modal-backdrop').show();

			scheduler_form_objs.reset();
			scheduler_form_objs.update_preview();
		},

		update_preview		: function() {
			var params = "";
			var ptype = "";
			scheduler_form_objs.content_ptype.find(":selected").each(function(){
				ptype = ptype + (ptype == "" ? "":",") + jQuery.trim(jQuery(this).val());
			});
			if ( ptype != "") params = params + ' ptype="' +ptype +'"';

			var title = scheduler_form_objs.content_title.val();
			if ( title != "") params = params + ' title="' +title +'"';

			var showpost = scheduler_form_objs.content_showpost.val();
			if ( showpost != "") params = params + ' showpost="' +showpost +'"';

			var px = scheduler_form_objs.content_px.val();
			if ( px != "") params = params + ' px="' +px +'"';

			var separator = scheduler_form_objs.content_separator.val();
			if ( separator != "") params = params + ' separator="' +separator +'"';

			var sort = scheduler_form_objs.content_sort.val();
			if ( sort != "") params = params + ' sort="' +sort +'"';

			if ( scheduler_form_objs.content_showdate.attr('checked') ) params = params + ' showdate="yes"';
			if ( scheduler_form_objs.content_showtime.attr('checked') ) params = params + ' showtime="yes"';

			var text = "[" +'scheduled-contents ' +params +']';
			scheduler_form_objs.shortcode_text.val(text);
		},

		chosen_change		: function() {
			var str_selected = jQuery(this).val();
			// if( str_selected != null ){
			// 	pos = str_selected.lastIndexOf("all");
			// 	if ( pos >= 0 ) {
			// 		jQuery(this).find('option').each(function() {
			// 			// if(jQuery(this).val() == "all"){
			// 			// 	jQuery(this).prop("selected",false);
			// 			// }else{
			// 				jQuery(this).prop("selected","selected");
			// 			// }
			// 			jQuery(this).trigger("chosen:updated");
			// 		});
			// 	}
			// }
			scheduler_form_objs.update_preview();
		},

		insertcode		: function() {
			var text = scheduler_form_objs.shortcode_text.val().replace(/\r\n|\r|\n/g,"<br/>");
		    if (tinyMCE && tinyMCE.activeEditor && text != '') {
		   		tinyMCE.activeEditor.execCommand('mceInsertContent', false, text);
		   		scheduler_form_objs.shortcode_text.val('');
		    }
		    scheduler_form_objs.reset();
		    scheduler_form_objs.close();
		},

		reset	: function() {
			scheduler_form_objs.shortcode_text.val('');

			scheduler_form_objs.content_title.val("My Upcoming Posts");
			scheduler_form_objs.content_ptype.val("");
			scheduler_form_objs.content_showpost.val("10");
			scheduler_form_objs.content_px.val("4");
			scheduler_form_objs.content_separator.val("@");
			scheduler_form_objs.content_sort.val("title");
			scheduler_form_objs.content_showdate.prop('checked', true);
			scheduler_form_objs.content_showtime.prop('checked', true);
		},

		close	: function() {
			scheduler_form_objs.container.hide();
			scheduler_form_objs.container.find('.media-modal').hide();
			scheduler_form_objs.container.find('.media-modal-backdrop').hide();
		},

		init	: function() {
			scheduler_form_objs.content_ptype.chosen( { width:'100%', display_disabled_options:false, } );
			scheduler_form_objs.content_ptype.chosen().change( scheduler_form_objs.chosen_change );
			scheduler_form_objs.content_title.on('keyup', scheduler_form_objs.update_preview );
			scheduler_form_objs.content_showpost.on('keyup', scheduler_form_objs.update_preview );
			scheduler_form_objs.content_px.on('keyup', scheduler_form_objs.update_preview );
			scheduler_form_objs.content_separator.on('keyup', scheduler_form_objs.update_preview );
			scheduler_form_objs.content_sort.change( scheduler_form_objs.update_preview );
			scheduler_form_objs.content_showdate.on('click', scheduler_form_objs.update_preview );
			scheduler_form_objs.content_showtime.on('click', scheduler_form_objs.update_preview );

			scheduler_form_objs.insertcode_button.on('click', scheduler_form_objs.insertcode );
			scheduler_form_objs.lightbox_close.on('click', scheduler_form_objs.close );

			//assign the show in this function so that we can call it using our wlmtnmcelbox_vars global object
			wlmtnmcelbox_vars.show_scheduler_form_lightbox = scheduler_form_objs.show_lightbox;
		}
	};
	scheduler_form_objs.init();

	//When User press escape
	jQuery('body').on('keydown', function(event) {
		if ( 27 === event.which ) {
			jQuery('.wlmtnmcelbox').hide();
			jQuery('.wlmtnmcelbox').find('.media-modal').hide();
			jQuery('.wlmtnmcelbox').find('.media-modal-backdrop').hide();

			//add other functions that you want to execute when closing tru escape
			private_tags_objs.reset();
		}
	});
});


