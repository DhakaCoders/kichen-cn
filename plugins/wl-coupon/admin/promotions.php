<?php if ($show_page_menu) : ?>
<?php return; endif; ?>
<h2>Promotions</h2>
<?php
	global $WishListMemberInstance;
	$levels = $WishListMemberInstance->GetOption('wpm_levels');
	$coupons = $this->wldb->get_coupons();
	$styles  = $this->wldb->get_available_styles();

	$promotions = array();

	foreach($coupons as $c) {
		$promotions[$c->promotion_id]['id'] = $c->promotion_id;
		$promotions[$c->promotion_id]['name'] = $c->name;
		$promotions[$c->promotion_id]['valid_text'] = $c->valid_text;
		$promotions[$c->promotion_id]['invalid_text'] = $c->invalid_text;
		$promotions[$c->promotion_id]['apply_button_text'] = $c->apply_button_text;
		$promotions[$c->promotion_id]['pay_button_text'] = $c->pay_button_text;
		$promotions[$c->promotion_id]['default_payment_link'] = $c->default_payment_link;
		$promotions[$c->promotion_id]['coupons'][] = $c;
	}

	$p = $this->wldb->get_promotions();
	$p = json_encode($p);
?>

<div id="canvas">
	<div class="container"></div>

	<br/>
	<input type="submit" name="" class="button-primary add" value="Create a Promotion"/>
</div>


<script type="text/template" id="promotion_template">
		<form method="post">
		<div class="item-header">
			<input type="text" class="name" name="name" size="50" value="<%=promotion.get('name') %>"/>
			<div class="item-header-right">
				<a href="#/show_coupons/<%=promotion.get('id') %>" class="show_coupons" title="collapse"><i class="icon-chevron-down icon-2x"></i></a>
				&nbsp;&nbsp;&nbsp;
				<a href="#/delete_promotion/<%=promotion.get('id') %>" class="delete_promotion" title="Delete"><i class="icon-remove icon-2x"></i></a>
			</div>
		</div>

		<div class="item-settings" style="display:none;">
			<input type="hidden" name="id" value="<%=promotion.get('id')%>"/>

				<div class="form-control"><label>Default Payment Link: </label> <input size="88" type="text" class="default_payment_link" name="default_payment_link" value="<%=promotion.get('default_payment_link')%>" placeholder="ex. http://yourdomain.com/defaultpaymentlink"/><?php echo $this->tooltip('tooltip_default_payment_link')?></div>

				<hr/>
				<div class="item-left">
					<h4>Alerts</h4>
					<div class="form-control"><label>Valid Text: </label> <input type="text" class="valid_text" name="valid_text" value="<%=promotion.get('valid_text')%>"/> <?php echo $this->tooltip('tooltip_valid_text')?></div>
					<div class="form-control"><label>Invalid Text: </label> <input type="text" class="invalid_text" name="invalid_text" value="<%=promotion.get('invalid_text')%>"/> <?php echo $this->tooltip('tooltip_invalid_text')?></div>
				</div>

				<div class="item-right">
					<h4>Labels</h4>
					<div class="form-control"><label>Form Label: </label> <input type="text" class="label" name="label" value="<%=promotion.get('label')%>"/><?php echo $this->tooltip('tooltip_label')?></div>
					<div class="form-control"><label>Apply Button Text: </label> <input type="text" class="apply_button_text" name="apply_button_text" value="<%=promotion.get('apply_button_text')%>"/><?php echo $this->tooltip('tooltip_apply_button_text')?></div>
					<div class="form-control"><label>Buy Button Text: </label> <input type="text" class="pay_button_text" name="pay_button_text" value="<%=promotion.get('pay_button_text')%>"/><?php echo $this->tooltip('tooltip_pay_button_text')?></div>
				</div>

				<hr/>
				<h4>Style</h4>

				<div class="form-control"><label>Select Style: <?php echo $this->tooltip('tooltip_style')?></label>
					<select class="style" name="style" style="display:none;">
						<option value=""></option>
						<?php foreach($styles as $i => $s): ?>
						<option <% if(promotion.get('style') == '<?php echo $i?>')print('selected="selected"')%> value="<?php echo $i?>"><?php echo $s['name']?></option>
						<?php endforeach; ?>
					</select>
				</div>

				<div class="jcarousel-wrapper">
					<div class="jcarousel">
						<ul>
							<?php foreach($styles as $i => $s): ?>
								<li class="carousel-item <% if(promotion.get('style') == '<?php echo $i?>')print('carousel-item-active')%>" data-value="<?php echo $i?>">
								<img width="185px" height="150px" src="<?php echo $s['screenshot']?>"></img></a><div><?php echo $s['name']?>
								<a class="check" href="#" title="Deselect"><div class="media-modal-icon"></div></a>
								</li>
							<?php endforeach; ?>
						</ul>
					</div>

				<a href="#" class="jcarousel-control-prev">&lsaquo;</a>
				<a href="#" class="jcarousel-control-next">&rsaquo;</a>
				</div>

				<div class="form-control"><br/><input type="submit" name="save" value="Save Promotion Settings" class="button-primary"/>
				<div class="spinner-container"><span class="spinner"></span></div></div>
		</div>
		</form>

		<div class="item-body" style="display:none;">
			<div class="coupon-list"></div>

			<div class="coupon-item">
				<input type="submit" class="add_coupon button-primary" value="Create New Coupon">
			</div>
		</div>
</script>


<script type="text/template" id="coupon_template">
<div class="coupon-item">
	<form>
		<input type="hidden" name="id" value="<%=coupon.get('coupon_id')%>"/>
			<div class="item-left">
			<div class="form-control"><label>Coupon Code: </label> <br/><input type="text" class="coupon_code" name="coupon_code" value="<%=coupon.get('coupon_code')%>"/><?php echo $this->tooltip('tooltip_coupon_code')?></div>
			<div class="form-control"><label>Payment Link: </label> <br/> <input type="text" size="50" class="payment_link" name="payment_link" value="<%=coupon.get('payment_link')%>"/ placeholder="ex. http://yourdomain.com/discountedpaymentlink"><?php echo $this->tooltip('tooltip_payment_link')?></div>
		</div>

		<div class="item-right">
			<div class="form-control"><label>Date Range: </label><input size="10" type="text" class="valid_date_from wishlist_coupon_el_datepicker" name="valid_date_from" value="<%=coupon.get('valid_date_from')%>"/>&nbsp; to
																<input size="10"  type="text" class="valid_date_to wishlist_coupon_el_datepicker" name="valid_date_to" value="<%=coupon.get('valid_date_to')%>" /><?php echo $this->tooltip('tooltip_date_range')?></div>
			<div class="form-control"><label>Quantity: </label><input size="2" type="text" class="valid_num_tries" name="valid_num_tries" value="<%=coupon.get('valid_num_tries')%>"/> <% if(coupon.get('valid_num_tries') > 0) { %> <em>( <%=coupon.get('valid_num_tries_remaining')%>  remaining ) </em><?php echo $this->tooltip('tooltip_valid_num_tries_remaining')?>  <% } %></div>
			<div class="form-control"><label>Days After Registration: </label><input size="2" type="text" class="valid_num_days_after_reg" name="valid_num_days_after_reg" value="<%=coupon.get('valid_num_days_after_reg')%>"/>
				<select name="valid_num_days_after_reg_level" class="valid_num_days_after_reg_level">
					<option value=""></option>
					<?php foreach($levels as $lid => $l): ?>
					<option <% if(coupon.get('valid_num_days_after_reg_level') == '<?php echo $lid?>') print('selected="selected"'); %>value="<?php echo $lid?>"><?php echo $l['name']?></option>
					<?php endforeach; ?>
				</select><?php echo $this->tooltip('tooltip_valid_num_days_after_reg_level')?>
			</div>
		</div>
		<hr/>
		<div class="form-control" style="text-align: right;"><br/>
			<input type="submit" name="save" value="Save Coupon" class="button-primary save"/>
			<input type="submit" name="save" value="Delete Coupon" class="button-secondary delete"/>
			<div class="spinner-container">
				<span class="spinner"></span>
			</div>
		</div>
	</form>
</div>
</script>

<script type="text/javascript">
jQuery(function($) {
	var app =  WishListCouponAdminApp;
	var data = JSON.parse(<?php echo json_encode($p)?>);
	app.start(data);
});

</script>


<style type="text/css">
#canvas .item, #canvas .coupon-item, #canvas .item-settings {
	background: #f7f7f7;
	border: 1px solid #ccc;
	margin-right: 10px;
	margin-bottom: 0px;
	margin-top: -2px;
	-webkit-border-radius: 3px;
	-webkit-border-radius: 3px;
	border-radius: 3px;
	border-radius: 3px;
}

#canvas .item-header {
	padding: 10px 10px 10px 10px;
	border-bottom: 1px solid #ccc;
	background: #E4E4E4;
}

#canvas .item-header .item-header-right {
	float: right;
}

.item-header-right a:link {
	text-decoration: none;
}
#canvas .coupon-item, #canvas .item-settings {
	margin-top: 8px;
	margin-left: 10px;
	margin-bottom: 10px;
	padding: 10px 10px 10px 10px;
	background: white;
}

#canvas .item-settings {
	margin-left: 10px;
}

#canvas .coupon-item label, #canvas .item-settings label {
	display: inline-block;
	width: 180px;
	padding-top: 10px;
	padding-bottom: 5px;
}
#canvas ul li div {
	text-align: center;
}
#canvas ul li a.check{
	position: absolute;
	display: none;
	background-color: #04a4cc;
  	box-shadow: 0 0 0 1px white, 0 0 0 2px #04a4cc;
	right: 6px;
	top: -6px;
	color: #21759b;
}
#canvas ul li a.check div {
	background-position: -21px 0;
	height: 15px;
	width: 15px;
	margin: 5px;
}

#canvas ul li.carousel-item-active a.check {
	display: block;
}

.item-left, .item-right {
	float: left;
}

.item-right {
	margin-left: 25px;
}

.spinner-container {
	display: inline-block;
	width: 30px;
}
.spinner {
	float: left;
}
.wp-admin select {
	vertical-align: top;
}
</style>
<?php include $this->plugin_dir .'/admin/tooltips/promotions.tooltips.php'?>