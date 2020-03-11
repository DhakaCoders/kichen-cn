var custom_regform_edits = {
	text : {
		label : '',
		name : '',
		name_editable : false,
		default : '',
		width : 20,
		description: '',
		has_required : true,
	},
	textarea : {
		label : '',
		name : '',
		name_editable : false,
		default : '',
		width : 30,
		height : 4,
		description: '',
		has_required : true		
	},
	select : {
		label : '',
		name : '',
		name_editable : false,
		default : '',
		items : [],
		description: '',
		has_required : true
	},
	hidden : {
		labelh : wlm.translate( 'Hidden' ),
		name : '',
		default : ''
	},
	submit : {
		default : ''
	}
};

var custom_regform_markup = {
	text : $.extend({}, custom_regform_edits.text, {label : wlm.translate( 'Text' ), name : 'text_field', width : 25, name_editable : true}),
	textarea : $.extend({}, custom_regform_edits.textarea, {label : wlm.translate( 'Text Box' ), name : 'textbox_field', name_editable : true}),
	select : $.extend({}, custom_regform_edits.select, {label : wlm.translate( 'Dropdown List' ), name : 'dropdown_field', items : [wlm.translate( 'Option 1' ), wlm.translate( 'Option 2' )], default : wlm.translate( 'Option 1' ), name_editable : true}),
	radio : $.extend({}, custom_regform_edits.select, {label : wlm.translate( 'Radio Buttons' ), name : 'radio_field', items : [wlm.translate( 'Radio 1' ), wlm.translate( 'Radio 2' )], default : wlm.translate( 'Radio 1' ), name_editable : true}),
	checkbox : $.extend({}, custom_regform_edits.select, {label : wlm.translate( 'Checkboxes' ), name : 'checkbox_field[]', items : [wlm.translate( 'Checkbox' )], name_editable : true}),
	hidden : $.extend({}, custom_regform_edits.hidden, {name : 'hidden_field'}),

	username : $.extend({}, custom_regform_edits.text, {label : wlm.translate( 'Username' ), name : 'username', width: 25, has_required : false}),
	email : $.extend({}, custom_regform_edits.text, {label : wlm.translate( 'Email' ), name : 'email', width: 25, has_required : false}),
	password1 : $.extend({}, custom_regform_edits.text, {label : wlm.translate( 'Password (twice)' ), name : 'password1', width: 25, description : wlm.translate( 'Enter your desired password twice. Must be at least [wlm_min_passlength] characters long.' ), default: null, has_required : false}),
	firstname : $.extend({}, custom_regform_edits.text, {label : wlm.translate( 'First Name' ), name : 'firstname'}),
	lastname : $.extend({}, custom_regform_edits.text, {label : wlm.translate( 'Last Name' ), name : 'lastname'}),
	nickname : $.extend({}, custom_regform_edits.text, {label : wlm.translate( 'Nickname' ), name : 'nickname'}),
	website : $.extend({}, custom_regform_edits.text, {label : wlm.translate( 'Website' ), name : 'website'}),
	aim : $.extend({}, custom_regform_edits.text, {label : 'AIM:', name : 'aim'}),
	yim : $.extend({}, custom_regform_edits.text, {label : wlm.translate( 'Yahoo IM' ), name : 'yim'}),
	jabber : $.extend({}, custom_regform_edits.text, {label : wlm.translate( 'Jabber / Google Talk' ), name : 'jabber'}),
	biography : $.extend({}, custom_regform_edits.textarea, {label : wlm.translate( 'Biographical Info' ), name : 'biography'}),

	company : $.extend({}, custom_regform_edits.text, {label : wlm.translate( 'Company' ), name : 'company', width : 30}),
	address1 : $.extend({}, custom_regform_edits.text, {label : wlm.translate( 'Address (Line 1)' ), name : 'address1', width : 40}),
	address2 : $.extend({}, custom_regform_edits.text, {label : wlm.translate( 'Address (Line 2)' ), name : 'address2', width : 40}),
	city : $.extend({}, custom_regform_edits.text, {label : wlm.translate( 'City' ), name : 'city'}),
	state : $.extend({}, custom_regform_edits.text, {label : wlm.translate( 'State' ), name : 'state', width : 10}),
	zip : $.extend({}, custom_regform_edits.text, {label : wlm.translate( 'Zip Code' ), name : 'zip', width : 5}),
	country : $.extend({}, custom_regform_edits.text, {label : wlm.translate( 'Country' ), name : 'country', width : 40}),

	submit : $.extend({}, custom_regform_edits.submit, {default : 'submit_registration'}),

	field_special_header : {'label' : wlm.translate( 'Section Header' ), helper : wlm.translate( 'Use the section header to divide your custom registration form into sections.' )},
	field_special_paragraph : {'description' : '<p>Text</p>'},
	field_tos : {name: 'terms_of_service', description : WLM3VARS.level_defaults.tos.replace(/\n/g, '<br>\n'), default : wlm.translate( 'I agree to the Terms of Service' )},
}

function wlm3_screen_regforms() {
	this.init();
}
wlm3_screen_regforms.prototype = {
	apply_do_confirm: function() {
		$( '#regforms-create .-delete-field' ).do_confirm( { confirm_message : wlm.translate( 'Remove this item?' ), yes_button : wlm.translate( 'Remove' ) } ).on( 'yes.do_confirm', function() {
			$( this ).closest( '.panel' ).fadeOut( 300, function() {
				$( this ).remove();
			} );
		} );
	},
	list_regforms: function() {
		$('#regforms-list').show();
		$('#custom-registration-forms-list tbody').replaceWith('<tbody/>');
		var data = {
			forms : wpm_regforms
		}
		var tmpl = _.template($('script#custom-registration-forms-list-template').html(), {variable: 'data'});
		var html = tmpl(data);
		$('#custom-registration-forms-list tbody').replaceWith('<tbody>'+html.trim()+'</tbody>');
		$('a.-no-delete').tooltip({trigger : 'click'}).on('mouseout', function() {
			$(this).tooltip('hide');
		});
		$( '#regforms-list .-del-btn' ).do_confirm( { confirm_message : wlm.translate( 'Delete this Form?' ), yes_button : wlm.translate( 'Delete' ) } ).on( 'yes.do_confirm', { that : this }, this._delete );
		$('#custom-registration-forms-list tbody').sort('tr', 'data-sort', true, true);
		$('#custom-registration-forms-list').removeClass('d-none');
	},
	generate_form_field_object(input, label, description, container) {
		if(!container) container = $('<div/>');

		var field = {};
		input = $(input);

		// ids
		var id = Date.now();
		id += (Math.floor(Math.random() * (318 - 3)) + 3);
		field.group_id = '_group_' + id;
		field.collapse_id = '_collapse_' + id;
		field.heading_id = '_heading_' + id;

		// label
		field.label = label;

		// description
		field.description = description;

		// type
		if(container.hasClass('field_special_header')) {
			field.type = 'field_special_header';
			delete field.description;
		} else if(container.hasClass('field_special_paragraph')) {
			field.type = 'field_special_paragraph';
			delete field.label;
		} else if(container.hasClass('field_tos')) {
			field.type = 'field_tos';
			field.name = input.attr('name');
			field.required = field.name;
			field.lightbox = container.hasClass('lightbox_tos');
			delete field.label;
		} else {
			field.type = input[0].type.toLowerCase();

			// get name
			field.name = input.attr('name');

			// set element dimensions
			if(field.type == 'textarea') {
				field.width = input.attr('cols');
				field.height = input.attr('rows');
			} else {
				field.width = input.attr('size');
			}
		}

		if(['select-one', 'select-multiple', 'select'].indexOf(field.type) > -1) {
			field.type = 'select';
		}

		// make sure that element markup is correct
		// also set default value
		field.default = '';
		switch(field.type) {
			case 'checkbox':
			case 'radio':
				field.items = [];
				input.each(function(i) {
					if($(this).is(':checked')) {
						field.default = $(this).val();
					}

					var l = $(this).closest('label');
					var t = l.text().trim();
					var v = $(this).attr('value');
					if(v == t) {
						field.items.push(v);
					} else {
						field.items.push([v,t].join(':'));
					}
					if(!l.length) {
						l = $('<label>').text($(this).val()).prepend($(this));
					}
					input[i] = $('<div/>').addClass(field.type).append(l[0].outerHTML);
				});
				break;
			case 'select':
				input = input.first().addClass('form-control');
				field.items = [];
				field.default = input.val();
				input.find('option').each(function(i, option) {
					option = $(option);
					var l = option.text();
					var v = option.attr('value');
					if(v == l) {
						field.items.push(v);
					} else {
						field.items.push([v,l].join(':'));
					}
				})
				break;
			case 'submit':
				input.addClass('btn');
				input.addClass('submit');
				delete field.name;
				delete field.label;
				delete field.description;
				field.default = input.val();
				break;
			case 'field_special_header':
			case 'field_special_paragraph':
				delete field.default;
				input = null;
				break;
			case 'field_tos':
				input = input.first().clone();
				field.default = input.val();
				var l = $('<label />').text(field.default);
				var d = $('<div />').addClass('checkbox');
				l.append(input);
				d.append(l);
				input = d;
				delete field.label;
				break;
			case 'hidden':
				input = input.first();
				field.default = input.val();
				field.inputh = field.name+' (' + field.default + ')';
				delete field.width;
				delete field.label;
				break;
			default:
				input = input.first().addClass('form-control');
				field.default = input.val();
		}

		// generate html for input
		field.input = '';
		if(input) {
			input.each(function() {
				field.input += $(this)[0].outerHTML;
			});
		}

		if(field.type in custom_regform_markup && !(field.name in custom_regform_markup) && custom_regform_markup[field.type].name_editable) {
			field.name_editable = true;
		}

		return field;
	},
	generate_form_field_edit_markup(field, obj) {
		var edit_html = '<div>No options</div>';
		var edit_panel = '';
		if(field.name in custom_regform_markup) {
			edit_panel = $.extend({}, custom_regform_markup[field.name], field);
		}else if (field.type in custom_regform_markup) {
			edit_panel = $.extend({}, custom_regform_markup[field.type], field);
		}
		if(edit_panel) {
			var edit_tmpl = _.template($('script#regform-edit-item-fields-template').html(), {variable: 'data'});
			edit_html = edit_tmpl(edit_panel);
		}
		obj.find('.panel-body').html(edit_html);
	},
	form_transform(regform) {
		var f = $(regform.form).filter('table');
		var required = [];
		if("required" in regform && (!! regform.required)) {
			required = regform.required.split(',');
		}
		var fields = [];
		var field = {};
		var that = this;
		f.find('tr.li_fld,tr.li_submit').each(function(i, obj) {
			obj = $(obj);
			field = that.generate_form_field_object(
				obj.find('td.fld_div :input.fld'),
				obj.find('td.label').text(),
				obj.find('td.fld_div div.desc').html(),
				obj
			);
			if('required' in field && field.required == field.name) {
				required.push(field.required);
			}
			fields.push(field);
		});
		var data = {
			fields : fields,
			required : _.uniq(required),
		}
		var tmpl = _.template($('script#regform-edit-item-template').html(), {variable: 'data'});
		var html = tmpl(data);
		return html;
	},
	form_reverse_transform(regform) {
		var tbody = $('<tbody/>');
		var table = $('<table/>').addClass('wpm_regform_table wpm_registration').append(tbody);
		table.attr({cellpadding: 0, cellspacing: 0});
		regform.find('.panel').each(function(i, panel) {
			panel = $(panel);
			var tr = $('<tr/>');
			if(panel.hasClass('field_submit')) {
				var input = panel.find('.form-element-container :input').first().clone();
				tr.addClass('li_submit');
				input.removeClass('btn');
				tr.append($('<td/>').addClass('label'));
				tr.append($('<td/>').addClass('fld_div form_button').append(input));
				tbody.append(tr);
				return true;
			}
			var req = panel.attr('data-required');
			if(panel.hasClass('field_tos')) {
				req = true;
			}

			var input = $(panel.find('.form-element-container'));
			if(input.find('[name=username],[name=password1],[name=email]').length) {
				req = true;
				tr.addClass('systemFld');
			}

			input = input.html();

			var label = panel.find('.the-label').text();
			var desc = panel.find('.desc').html();

			tr.addClass('li_fld').addClass('field_' + panel.data('field-type').replace('field_', ''));
			tr.removeClass('field_submit');
			if(req) tr.addClass('required');

			if(panel.hasClass('lightbox_tos')) {
				tr.addClass('lightbox_tos')
			}

			tr.append($('<td/>').addClass('label').text(label));
			tr.append($('<td/>').addClass('fld_div').append(input).append($('<div/>').addClass('desc').html(desc)));
			
			tbody.append(tr);
		});
		table.find(':input').removeClass('form-control');
		table.find(':input[type=password]').val('');

		return table[0].outerHTML;
	},
	show_form: function(regform) {
		var that = this;

		$('#regforms-list').hide();

		var obj = $('#regforms-create');

		obj.find('.page-header input[name=form_name]').first().val(regform.form_name).focus();
		obj.find('.page-header input[name=form_id]').first().val(regform.id);
		obj.find('.chosen-fields').empty().append(this.form_transform(regform));

		obj.find('#regform-edit-accordion').first().sortable({
			items : '> div:not(.submit)',
			placeholder : 'regform-edit-accordion-placeholder',
			stop: function(e, ui) {
				var markup = ui.item.data('markup-name');
				if(!markup) return;
				var html = '<div>Not working yet</div>';
				var id = 0;
				var field = {};
				var input = '';
				var container = $('<div/>');
				if(markup in custom_regform_markup) {
					field = custom_regform_markup[markup];
					delete field.type;
					switch(markup) {
						case 'hidden':
							field.type = 'hidden';
							input = $('<input type="hidden" />');
							input.attr('name', field.name);
							input.addClass('fld');
							input.val(field.default);
							break;
						case 'textarea':
						case 'biography':
							field.type = 'textarea';
							input = $('<textarea/>');
							input.attr('cols', field.width);
							input.attr('rows', field.height);
							input.attr('name', field.name);
							input.addClass('form-control').addClass('fld');
							input.val(field.default);
							break;
						case 'checkbox':
						case 'radio':
							field.type = markup;
							input = $('<div/>');
							$.each(field.items, function(i, item) {
								var x = item.split(':');
								var v = x.shift();
								var l = x.length ? x.join(':') : v;
								var c = v == field.default ? ' checked="checked"' : '';
								input.append($('<div class="'+field.type+'"><label><input class="fld" name="'+field.name+'" type="'+field.type+'" value="'+v+'"'+c+'>'+l+'</label></div>'));
							});
							input = input.find(':input.fld');
							break;
						case 'select':
							input = $('<select/>').attr('name', field.name).addClass('fld');
							$.each(field.items, function(i, item) {
								var x = item.split(':');
								var v = x.shift();
								var l = x.length ? x.join(':') : v;
								var o = $('<option/>').text(l).attr('value',v);
								if(v == field.default) o.prop('selected', true);
								input.append(o);
							});
							break;
						case 'field_special_header':
						case 'field_special_paragraph':
						case 'field_tos':
							field.type = markup;
							container.addClass(markup);
							input = ' ';
							break;
						case 'password1':
							field.type = 'password';
						case 'text':
							field.type = 'text';
						default:
							input = $('<input/>');
							input.attr('width', field.width);
							input.attr('name', field.name);
							input.addClass('form-control').addClass('fld');
							input.val(field.default);
							break;
					}
					if(input) {
						field = $.extend({}, that.generate_form_field_object(input, field.label, field.description, container), field);

						var tmpl = _.template($('script#regform-edit-item-template').html(), {variable: 'data'});
						var html = tmpl({fields : [field]});
					}
				}
				ui.item.replaceWith($(html).html());
				that.apply_do_confirm();
			}
		}).on('show.bs.collapse', function(e) {
			var obj = $(e.target).closest('.panel');
			obj.addClass('panel-open')
			var field = that.generate_form_field_object(
				obj.find(':input.fld'),
				obj.find('.the-label').text(),
				obj.find('.desc').html(),
				obj
			);
			field.required = obj.attr('data-required') == obj.find(':input.fld').attr('name');
			that.generate_form_field_edit_markup(field, obj);
		}).on('hide.bs.collapse', function(e) {
			$(e.target).closest('.panel').removeClass('panel-open');
		});
		obj.find('ul.custom-fields-draggable > li').draggable({
			helper: 'clone',
			connectToSortable : '#regform-edit-accordion',
		});

		if(!regform.form_name) {
			$('#all-form-data').addClass('d-none');
		}

		$( '#regforms-create [name=form_name]' ).apply_cancel( { show_feedback : false } )
		.on('apply.apply_cancel', {'that' : that}, function(e) {
			e.data.that._save_and_continue(e);
		});

		obj.show();

		this.apply_do_confirm();

		return false;
	},
	_close_and_list: function(e) {
		window.parent.location.hash = '#';
		return false;
	},
	_save_and_close: function(e) {
		e.data.that._save(e, true);
		return false;
	},
	_save_and_continue: function(e) {
		e.data.that._save(e, false);
		return false;
	},
	_save: function(e, close) {
		var form = e.data.that.form_reverse_transform($('#regforms-create .chosen-fields'));
		$('#regforms-create .page-header textarea[name=rfdata]').val(form);

		var required = [];
		$('#regforms-create .panel[data-required]').each(function() {
			required.push($(this).data('required'));
		});
		$('#regforms-create .page-header input[name=form_required]').val(required.join(','));

		var fields = [];
		$('#regforms-create .chosen-fields :input.fld').each(function() {
			if(typeof $(this).attr('name') != 'undefined') {
				if($(this).attr('name') == 'username') return true;
				if($(this).attr('name') == 'email') return true;
				if($(this).attr('name') == 'password') return true;
				if($(this).attr('name') == 'password1') return true;
				if($(this).attr('name') == 'password2') return true;

				fields.push($(this).attr('name').split('[')[0]);
			}
		});
		$('#regforms-create .page-header input[name=form_fields]').val(fields.join(','));

		// return;

		var f = $('#regforms-create .page-header').save_settings({
			on_init: function($me, $data) {
				$me.find(".save-button").disable_button({
					disable: true,
					icon: "update"
				});
			},
			on_done: function($me, $data) {
				$me.find(".save-button").disable_button({
					disable: false,
					icon: "save"
				});
			},
			on_error: function($me, $error_fields) {
				$.each($error_fields, function(key, obj) {
					obj.parent().addClass('has-error');
				});
				$me.find(".save-button").disable_button({
					disable: false,
					icon: "save"
				});
			},
			on_success: function($me, $result) {
				$('#all-form-data').removeClass('d-none');
				wpm_regforms = $result.regforms;
				$('.wlm-message-holder').show_message({
					message: $result.msg,
					type: $result.msg_type,
					icon: $result.msg_type
				});
				if(close) {
					e.data.that._close_and_list(e);
				}
			}
		});

		return false;
	},
	_edit: function(regformid) {
		var regform = $.extend({}, wlm.regform_defaults);
		$.extend(regform, wpm_regforms[regformid].option_value);
		regform.id = regformid;
		return this.show_form(regform);

	},
	_new: function() {
		var regform = $.extend({}, wlm.regform_defaults);
		var n = Date.now();
		regform.id = 'CUSTOMREGFORM-' + n;
		// regform.form_name = wlm.translate( 'Form ' ) + moment().format().replace('T', ' ').replace(/\+.+/, '');
		regform.form_name = '';
		regform.form = wpm_regform_default;
		return this.show_form(regform);
	},
	_delete: function(e) {
		tr = $(this).closest('tr');
		var regformid = $(this).closest('tr').attr('data-id');
		var f = $('<form><input type=hidden name=action value=admin_actions><input type=hidden name=WishListMemberAction value=delete_custom_registration_form><input type=hidden name=id value=' + regformid + '></form>');
		f.save_settings({
			on_success: function($me, $result) {
				wpm_regforms = $result.wpm_regforms;
				$('.wlm-message-holder').show_message({
					message: $result.msg,
					type: $result.msg_type,
					icon: $result.msg_type
				});
				tr.remove();
			}
		});
		return false;
	},
	_clone: function(e) {
		tr = $(this).closest('tr');
		var regformid = $(this).closest('tr').attr('data-id');
		var f = $('<form><input type=hidden name=action value=admin_actions><input type=hidden name=WishListMemberAction value=clone_custom_registration_form><input type=hidden name=id value=' + regformid + '></form>');
		f.save_settings({
			on_success: function($me, $result) {
				wpm_regforms = $result.regforms;
				$('.wlm-message-holder').show_message({
					message: $result.msg,
					type: $result.msg_type,
					icon: $result.msg_type
				});
				e.data.that.list_regforms();
			}
		});
		return false;
	},
	init: function() {
		var that = this;
		$('#regforms-create').off('.custom_reg_form');
		$('#regforms-list').off('.custom_reg_form');

		$('#regforms-list')
			// clone button handler
			.on('click.custom_reg_form', '.-clone-btn', {
				that: this
			}, this._clone);

		$('#regforms-create')
			// save button handler
			.on('click.custom_reg_form', 'a.save-and-continue', {
				that: this
			}, this._save_and_continue)
			// save button handler
			.on('click.custom_reg_form', 'a.save-and-close', {
				that: this
			}, this._save_and_close)
			// cancel button handler
			.on('click.custom_reg_form', 'a.cancel', {
				that: this
			}, this._close_and_list)
			.on('click.custom_reg_form', '.fld.btn', function() {
				return false;
			});

		// editing of fields
		$('#regforms-create')
			.on('change.custom_reg_form', '.-edit-label', function() {
				$(this).closest('.panel').find('.the-label').text($(this).val());
			})
			.on('change.custom_reg_form', '.-edit-name', function() {
				var i = $(this).closest('.panel').find(':input.fld');
				i.attr('name', $(this).val());
				if(i[0].type == 'hidden') {
					$(this).closest('.panel').find('label.inputh').text(i.attr('name') + ' ('+i.val()+')');
				}
			})
			.on('change.custom_reg_form', '.-edit-default', function() {
				var i = $(this).closest('.panel').find(':input.fld');
				i.attr('value', $(this).val());
				i.val([$(this).val()]);
				if(i[0].type == 'hidden') {
					$(this).closest('.panel').find('label.inputh').text(i.attr('name') + ' ('+i.val()+')');
				}
				if($(this).closest('.panel').hasClass('field_tos')) {
					var l = i.closest('label');
					i.attr('value', $(this).val());
					l.text($(this).val());
					l.prepend(i);
				}
			})
			.on('change.custom_reg_form', '.-edit-width', function() {
				var i = $(this).closest('.panel').find(':input.fld');
				if(i[0].type == 'textarea') {
					i.attr('cols', $(this).val());
				} else {
					i.attr('size', $(this).val());
				}
			})
			.on('change.custom_reg_form', '.-edit-height', function() {
				$(this).closest('.panel').find(':input.fld').attr('rows', $(this).val());
			})
			.on('change.custom_reg_form', '.-edit-description', function() {
				$(this).closest('.panel').find('.desc').html($(this).val());
			})
			.on('change.custom_reg_form', '.-edit-required', function() {
				var panel = $(this).closest('.panel');
				if($(this).is(':checked')) {
					panel.attr('data-required', $(this).val());
				} else {
					panel.removeAttr('data-required');
				}
			})
			.on('change.custom_reg_form', '.-edit-lightbox', function() {
				var panel = $(this).closest('.panel');
				if($(this).is(':checked')) {
					panel.addClass('lightbox_tos', $(this).val());
				} else {
					panel.removeClass('lightbox_tos');
				}
			})
			.on('change.custom_reg_form', '.-edit-items', function() {
				var items = $(this).val().split("\n");
				var panel = $(this).closest('.panel');
				var input = panel.find(':input.fld').first();
				var val = panel.find(':input.-edit-default').val();
				var name = input.attr('name');
				var type = input[0].type;
				var container;

				if(['radio', 'checkbox'].indexOf(type) > -1){
					container = panel.find('div.form-element-container').first();
				} else {
					container = panel.find('select.fld').first();
				}
				container.empty();

				$.each(items, function(i, item) {
					item = item.split(':');
					var v = item.shift();
					var l = item.length ? item.join(':') : v;
					var input, element, label;

					switch(type) {
						case 'radio':
						case 'checkbox':
							input = $('<div/>').addClass(type);
							element = $('<input/>').addClass('fld').attr('name',name).attr('type',type).attr('value', v);
							label = $('<label/>').text(l);
							container.append(input.append(label.prepend(element)));
							break;
						default:
							input = $('<option/>');
							input.text(l);
							input.attr('value', v);
							if(v == val) input.prop('selected', true);
							container.append(input);
							break;
					}

				});
			})
			.on('keydown', ':input.fld', function() {
				return false;
			})
			.on('focus', ':input.fld', function() {
				$(this).blur();
				return false;
			})
			.on('click.custom_reg_form', '.-clone-field', {that:this}, function(e) {
				var obj = $(this).closest('.panel');
				var field = e.data.that.generate_form_field_object(
					obj.find(':input.fld'),
					obj.find('.the-label').text(),
					obj.find('.desc').html(),
					obj
				);
				field.required = obj.attr('data-required') == obj.find(':input.fld').attr('name');
				field.name_editable = true;

				var tmpl = _.template($('script#regform-edit-item-template').html(), {variable: 'data'});
				var html = tmpl({fields : [field]});
				obj.after($(html).html());
				that.apply_do_confirm();
			});

		if(window.parent.location.hash) {
			var hash = window.parent.location.hash.split('-');
			var action = hash.shift();
			if(action == '#editform') {
				var form = hash.join('-');
				if(form == 'new') {
					this._new();
				} else {
					this._edit(form);
				}
			}
		} else {
			this.list_regforms();
		}

	}
};
$(function() {
	new wlm3_screen_regforms();
});
