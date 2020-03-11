<!--
name: Basic Horizontal
screenshot: basic-horizontal.png
default: yes
-->
<div id="wl-coupons" class="wishlist-coupon-promotion rounded drop-shadow">
	<div class="wc-body">
		<p class="wc-alert">
			[wishlist_coupon_success]
			[wishlist_coupon_alert]
		</p>
		<div class="wc-form clearfix">
			<label class="wc-label">[wishlist_coupon_label]</label>
			[wishlist_coupon_code]
			[wishlist_coupon_apply]
			[wishlist_coupon_pay]
		</div>
	</div>
</div>

<style type="text/css">

/** Reset **/
#wl-coupons {
	border: none;
	padding: 0;
}
#wl-coupons * {
	margin: 0;
	padding: 0;
	background: none;
	border: 0;
	color: #333;
	font-size: 16px;
	font: inherit;
	font-family: Arial, Helvetica, sans-serif;
	text-transform: none;
	vertical-align: baseline;
	border-radius: 0;
	-moz-border-radius: 0;
	box-shadow: none;
	-moz-box-shadow: none;
	text-shadow:none;
	line-height: 1;
	-moz-box-sizing: border-box;
	box-sizing: border-box;
}

#wl-coupons .clearfix:before,
#wl-coupons .clearfix:after {
  content: " ";
  display: table;
}
#wl-coupons .clearfix:after {
  clear: both;
}

/*Alert Message*/
#wl-coupons .wc-alert { margin: 0; padding:0; }
#wl-coupons .wishlist-coupon-alert { color: #d94d07; }
#wl-coupons .wishlist-coupon-success { color: #2ECC71; }

/*Wrapper*/
#wl-coupons.wishlist-coupon-promotion {
	background: #fff;
	border: 1px solid #ddd;
	border-top: 2px solid #1068A3;
	display: inline-block;
	display: table;
}
/*Head*/
#wl-coupons .wc-label {
	color: #1068a3;
	float: left;
	font-size: 18px;
	padding: 9px 9px 9px 0;
}
/*Body*/
#wl-coupons .wc-body {
	padding: 20px;
	border-bottom: 1px solid #ddd;
}
#wl-coupons .wishlist-coupon-code {
	background: #ddd;
	float: left;
	font-size: 14px;
	padding: 10px;
	height: 38px;
	-moz-box-sizing: border-box;
	box-sizing: border-box;
	width: auto;
}

#wl-coupons .wishlist-coupon-btn-apply {
	background: #c5c5c5;
	color: #fff;
	float: left;
	font-size: 14px;
	padding: 10px;
	height: 38px;
}
#wl-coupons .wishlist-coupon-btn-apply:hover {
	background: #9e9d9d;
}
/*Foot*/
#wl-coupons .wc-footer {
	padding: 20px;
	text-align: center;
}

#wl-coupons .wishlist-coupon-btn-pay {
	background: #1068a3;
	color: #fff;
	float: right;
	font-size: 16px;
	padding: 8px;
	height: 38px;
	margin-left: 12px;
}

#wl-coupons .wishlist-coupon-btn-pay:hover {
	background: #2698dd;
}

/*** Edit Here ***/

</style>













