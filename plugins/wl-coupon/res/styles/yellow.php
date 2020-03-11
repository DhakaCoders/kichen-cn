<!--
name: Yellow
screenshot: yellow.png
default: yes
-->

<div id="wl-coupons" class="wishlist-coupon-promotion rounded drop-shadow wc-gradient wc-yellow">
	<label for="" class="wc-label">[wishlist_coupon_label]</label>
	<div class="wc-body">
		<p class="wc-alert">
			[wishlist_coupon_success]
			[wishlist_coupon_alert]
		</p>
		<div class="wc-form clearfix">
			[wishlist_coupon_code]
			[wishlist_coupon_apply]
		</div>
	</div>
		<div class="wc-footer">
			[wishlist_coupon_pay]
		</div>
</div>

<style type="text/css">

/**
Reset
*/
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
	background: #f8f8f8;
	border: 1px solid #ddd;
	display: table-cell;
}
/*Head*/
#wl-coupons .wc-label {
	background: #1068a3;
	color: #fff;
	display: block;
	font-size: 18px;
	padding: 10px 20px;
	line-height: 24px;
}
/*Body*/
#wl-coupons .wc-body {
	padding: 15px 20px 20px;
	border-bottom: 1px solid #ddd;
}

#wl-coupons .wc-form {
	padding-top: 5px;
}
#wl-coupons .wishlist-coupon-code {
	background: #fff;
	float: left;
	font-size: 14px;
	padding: 10px;
	height: 38px;
	-webkit-box-shadow:0px 1px 2px 0 #D3D3D3 inset;
	box-shadow:0px 1px 2px 0 #D3D3D3 inset;
	-moz-box-sizing: border-box;
	box-sizing: border-box;
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
	border-top: 1px solid #fff;
	padding: 20px;
	text-align: center;
}

#wl-coupons .wishlist-coupon-btn-pay {
	background: #1068a3;
	color: #fff;
	font-size: 16px;
	font-weight: bold;
	padding: 8px;
	height: 38px;
}

#wl-coupons .wishlist-coupon-btn-pay:hover {
	background: #2698dd;
}

/*Rounded Box version*/
#wl-coupons.wishlist-coupon-promotion.rounded {
	border-radius: 3px;
}

#wl-coupons.wishlist-coupon-promotion.rounded .wc-label {
	border-radius: 3px 3px 0 0;
}

#wl-coupons.wishlist-coupon-promotion.rounded .wishlist-coupon-code {
	border-radius: 3px 0 0 3px;
}

#wl-coupons.wishlist-coupon-promotion.rounded .wishlist-coupon-btn-apply {
	border-radius: 0 3px 3px 0;
}
#wl-coupons.wishlist-coupon-promotion.rounded .wishlist-coupon-btn-pay {
	border-radius: 3px;
}

/*Drop Shadow Box*/
#wl-coupons.wishlist-coupon-promotion.drop-shadow {
	-webkit-box-shadow: 0 3px 2px -2px #999999;
	box-shadow: 0 3px 2px -2px #999999;
}

/******** Yellow ********/

#wl-coupons.wc-yellow .wc-label {
	background: #f1c40f;
}

#wl-coupons.wc-yellow .wc-body {}

#wl-coupons.wc-yellow .wc-alert {}

#wl-coupons.wc-yellow .wc-form {}

#wl-coupons.wc-yellow .wishlist-coupon-code {}

#wl-coupons.wc-yellow .wishlist-coupon-btn-apply {
	background: #f1c40f;
}

#wl-coupons.wc-yellow .wc-footer {}

#wl-coupons.wc-yellow .wishlist-coupon-btn-pay {
	background: #f1c40f;
}


/*CSS3 GRADIENTS*/
#wl-coupons.wc-gradient .wishlist-coupon-btn-pay {
	box-shadow: 0 1px 1px 0 #FFFFFF inset;
	-webkit-box-shadow: 0 1px 1px 0 #FFFFFF inset;
	text-shadow:0 -1px 0 #777;
}

/*YELLOW*/
#wl-coupons.wc-yellow.wc-gradient .wc-label,
#wl-coupons.wc-yellow.wc-gradient .wishlist-coupon-btn-pay,
#wl-coupons.wc-yellow.wc-gradient .wishlist-coupon-btn-apply {
	background: rgb(241,196,15); /* Old browsers */
	/* IE9 SVG, needs conditional override of 'filter' to 'none' */
	background: url(data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiA/Pgo8c3ZnIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgd2lkdGg9IjEwMCUiIGhlaWdodD0iMTAwJSIgdmlld0JveD0iMCAwIDEgMSIgcHJlc2VydmVBc3BlY3RSYXRpbz0ibm9uZSI+CiAgPGxpbmVhckdyYWRpZW50IGlkPSJncmFkLXVjZ2ctZ2VuZXJhdGVkIiBncmFkaWVudFVuaXRzPSJ1c2VyU3BhY2VPblVzZSIgeDE9IjAlIiB5MT0iMCUiIHgyPSIwJSIgeTI9IjEwMCUiPgogICAgPHN0b3Agb2Zmc2V0PSIwJSIgc3RvcC1jb2xvcj0iI2YxYzQwZiIgc3RvcC1vcGFjaXR5PSIxIi8+CiAgICA8c3RvcCBvZmZzZXQ9IjEwMCUiIHN0b3AtY29sb3I9IiNmMzljMTIiIHN0b3Atb3BhY2l0eT0iMSIvPgogIDwvbGluZWFyR3JhZGllbnQ+CiAgPHJlY3QgeD0iMCIgeT0iMCIgd2lkdGg9IjEiIGhlaWdodD0iMSIgZmlsbD0idXJsKCNncmFkLXVjZ2ctZ2VuZXJhdGVkKSIgLz4KPC9zdmc+);
	background: -moz-linear-gradient(top,  rgba(241,196,15,1) 0%, rgba(243,156,18,1) 100%); /* FF3.6+ */
	background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,rgba(241,196,15,1)), color-stop(100%,rgba(243,156,18,1))); /* Chrome,Safari4+ */
	background: -webkit-linear-gradient(top,  rgba(241,196,15,1) 0%,rgba(243,156,18,1) 100%); /* Chrome10+,Safari5.1+ */
	background: -o-linear-gradient(top,  rgba(241,196,15,1) 0%,rgba(243,156,18,1) 100%); /* Opera 11.10+ */
	background: -ms-linear-gradient(top,  rgba(241,196,15,1) 0%,rgba(243,156,18,1) 100%); /* IE10+ */
	background: linear-gradient(to bottom,  rgba(241,196,15,1) 0%,rgba(243,156,18,1) 100%); /* W3C */
	filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#f1c40f', endColorstr='#f39c12',GradientType=0 ); /* IE6-8 */

}
#wl-coupons.wc-yellow.wc-gradient .wishlist-coupon-btn-pay {
	border: 1px solid #f39c12;
}

#wl-coupons.wc-yellow.wc-gradient .wishlist-coupon-btn-pay:hover,
#wl-coupons.wc-yellow.wc-gradient .wishlist-coupon-btn-apply:hover {
	background: #f39c12;
}

</style>













