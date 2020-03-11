var WishListCouponAdminApp = (function($) {

	Backbone.emulateHTTP = true;
	Backbone.emulateJSON = true;

	var self = {};

	var Promotion = Backbone.Model.extend({
		urlRoot: function () {
			return ajaxurl + "?action=wishlist_coupon_backbone_promotions&id=" + encodeURIComponent(this.id) + "&";
		}
	});

	var Coupon = Backbone.Model.extend({
		idAttribute: 'coupon_id',
		urlRoot: function () {
			return ajaxurl + "?action=wishlist_coupon_backbone_promotion_coupons&id=" + encodeURIComponent(this.id) + "&";
		}
	});

	var PromotionCollection = Backbone.Collection.extend({
		model: Promotion
	});

	var CouponCollection = Backbone.Collection.extend({
		model: Coupon,
		url: function () {
			return ajaxurl + "?action=wishlist_coupon_backbone_coupon_list";
		}
	});



	var CanvasView = Backbone.View.extend({
		el: '#canvas',
		initialize: function(options) {
			this.options = this.options? this.options : options || {};
			this.data = this.options.data;
			this.collection.on('add', function(m) {
				var pview = new PromotionView({model: m});
				this.$el.find('div.container').append(pview.$el);
				pview.show_coupons();
			}, this);
		},
		events: {
			'click .add': 'add'
		},
		add: function() {
			this.collection.create({}, {wait: true});
		},
		render: function() {
			var promotions_view = new PromotionListView({collection: this.collection});
			this.$el.find('div.container').html(promotions_view.$el);
		}
	});

	var PromotionListView = Backbone.View.extend({
		initialize: function() {
			this.render();
		},
		render: function() {
			this.collection.each(function(p) {
				var pview = new PromotionView({model: p});
				this.$el.append(pview.$el);
			}, this);
		}
	});


	var PromotionView = Backbone.View.extend({
		className: "item",
		couponList: null,
		carousel: null,
		initialize: function() {
			this.render();
			this.model.on('sync', function() {
				this.$el.find('.spinner').hide();
			}, this);
		},
		events: {
			'click a.show_coupons': 'show_coupons',
			'click .add_coupon': 'add_coupon',
			'click a.delete_promotion': 'delete_promotion',
			'submit form': 'save_settings'
		},
		delete_promotion: function() {
			if(confirm("Are you sure you want to remove this promotion?")) {
				this.model.destroy();
				this.remove();
			}
		},
		save_settings: function() {
			this.$el.find('.spinner').eq(0).show();
			this.model.set('name', this.$el.find('.name').val());
			this.model.set('default_payment_link', this.$el.find('.default_payment_link').val());
			this.model.set('valid_text', this.$el.find('.valid_text').val());
			this.model.set('invalid_text', this.$el.find('.invalid_text').val());
			this.model.set('apply_button_text', this.$el.find('.apply_button_text').val());
			this.model.set('pay_button_text', this.$el.find('.pay_button_text').val());
			this.model.set('style', this.$el.find('.style').val());
			this.model.set('label', this.$el.find('.label').val());
			this.model.save();
			return false;
		},
		add_coupon: function() {
			this.couponList.add_coupon();
		},
		toggle: function() {


			this.$el.find('.item-body').toggle();
			this.$el.find('.item-settings').toggle();

			if(this.$el.find('.item-body').is(':visible')) {
				this.$el.find('a.show_coupons i').removeClass('icon-chevron-down').addClass('icon-chevron-up');
			} else {
				this.$el.find('a.show_coupons i').removeClass('icon-chevron-up').addClass('icon-chevron-down');
			}

			initialize_tooltip($);

		},
		show_coupons: function() {
			var collection = new CouponCollection();
			var p = this;
			var selected_index = p.get_carousel_selected_index();

			if(p.couponList != null) {
				p.toggle();
				return;
			}
			collection.fetch(
			{
				data: {promotion_id: p.model.get('id')},
				success: function() {
					var cpn_list_view = new CouponListView({collection: collection, promotion_id: p.model.get('id')});
					p.$el.find('.coupon-list').html(cpn_list_view.$el);
					p.toggle();
					p.carousel.jcarousel('reload');
					p.carousel.jcarousel('scroll', selected_index, false, function(status) {
						console.log(selected_index);
					});
					p.couponList = cpn_list_view;
				}
			});
		},
		get_carousel_selected_index: function() {
			var list = this.$el.find('.jcarousel li');
			return list.index(list.filter('.carousel-item-active'));
		},
		render: function() {
			var template = _.template($("#promotion_template").html(), {variable: 'promotion'})(this.model);
			this.$el.attr('id', "promotion-" + this.model.get('id'));
			this.$el.html(template);

			var list = this.$el.find('.jcarousel li');

			this.carousel = this.$el.find('.jcarousel').jcarousel();
			//console.log(this.$el.find('.jcarousel'));


			this.$el.find('.jcarousel-control-prev')
				.on('jcarouselcontrol:active', function() {
					$(this).removeClass('inactive');
				})
				.on('jcarouselcontrol:inactive', function() {
					$(this).addClass('inactive');
				})
				.jcarouselControl({
					target: '-=4'
			});

			this.$el.find('.jcarousel-control-next')
				.on('jcarouselcontrol:active', function() {
					$(this).removeClass('inactive');
				})
				.on('jcarouselcontrol:inactive', function() {
					$(this).addClass('inactive');
				})
				.jcarouselControl({
					target: '+=4'
			});

			//scroll to the active item




			var p = this;
			this.$el.find('.carousel-item').click(function() {
				list.removeClass('carousel-item-active');
				$(this).addClass('carousel-item-active');
				p.$el.find('.style').val($(this).attr('data-value'));
			});
		}

	});

	var CouponListView = Backbone.View.extend({
		initialize: function(options) {
			this.options = this.options? this.options : options || {};
			this.promotion_id = this.options.promotion_id;
			this.render();

			this.collection.on('add', function() {
				this.render();
			}, this);
		},
		add_coupon: function() {
			var m = new Coupon({promotion_id: this.promotion_id});
			m.save();
			this.collection.add(m, {merge: true});
		},
		render: function() {
			this.$el.html('');
			this.collection.each(function(c) {
				var cview = new CouponView({model: c});
				this.$el.append(cview.$el);
			}, this);
		}
	});

	var CouponView = Backbone.View.extend({
		initialize: function() {
			this.render();

			this.model.on('sync', function() {
				this.render();
			}, this);
		},
		events: {
			'click input.save': 'save',
			'submit form': 'save',
			'click input.delete': 'delete_coupon'
		},
		delete_coupon: function(e) {
			e.stopImmediatePropagation();
			if(confirm("Are you sure you want to delete this coupon?")) {
				this.model.destroy();
				this.remove();
			}
			return false;
		},
		save: function(e) {
			e.stopImmediatePropagation();
			this.$el.find('.spinner').show();
			this.model.set('coupon_code', this.$el.find('.coupon_code').val());
			this.model.set('payment_link', this.$el.find('.payment_link').val());

			this.model.set('valid_date_from', this.$el.find('.valid_date_from').val())
			this.model.set('valid_date_to', this.$el.find('.valid_date_to').val())
			this.model.set('valid_num_tries', this.$el.find('.valid_num_tries').val())
			this.model.set('valid_num_days_after_reg', this.$el.find('.valid_num_days_after_reg').val())
			this.model.set('valid_num_days_after_reg_level', this.$el.find('.valid_num_days_after_reg_level').val())
			this.model.save();
			return false;
		},
		render: function() {
			var variables = {coupon: this.model};
			var template = _.template($("#coupon_template").html(), {variable:'coupon'})(this.model);
			this.$el.html(template);
			this.$el.find('.wishlist_coupon_el_datepicker').datepicker({
				dateFormat:"mm-dd-yy",
				showOn: "button",
				buttonImage: wpcpn_calendar_img,
				buttonImageOnly: true});
			initialize_tooltip($);

		}
	});


	self.start = function(data) {
		var promotions = new PromotionCollection();
		_.each(data, function(d) {
			var p = new Promotion(d);
			promotions.add(p);
		});

		var canvas = new CanvasView({collection: promotions});
		canvas.render()
	}
	return self;
})(jQuery);