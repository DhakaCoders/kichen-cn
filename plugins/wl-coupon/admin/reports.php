<?php if ($show_page_menu) : ?>

<?php return; endif; ?>

<?php
	$num_per_page = 20;

	$default_filters = array(
		'date_from' => null,
		'date_to'   => null,
		'status'    => null,
		'offset'    => 0,
		'limit'     => $num_per_page,
		'promotion' => null,
		'coupon'    => null,
	);

	$default_filters = array_merge($default_filters,
		array(
			'date_from' => $_GET['date_from'],
			'date_to'   => $_GET['date_to'],
			'status'    => $_GET['status'],
			'offset'    => empty($_GET['offset'])? 1: $_GET['offset'],
			'limit'     => $num_per_page,
			'promotion' => $_GET['promotion'],
			'coupon'    => $_GET['coupon'],
		)
	);

	$report = $this->get_wldb()->get_report($default_filters);
	$promotions = $this->get_wldb()->get_promotions();
	$count = $report['count'];
	$report = $report['result'];


	$subs = array();
	foreach($report as $r) {
		$subs[$r->status] = $subs[$r->status] + 1;
	}


	$paginate_args = array(
				'base'         => add_query_arg('offset', '%#%'),
				'format'       => '?page=%#%',
				'total'        => ceil($count/$num_per_page),
				'current'      => $default_filters['offset'],
				'prev_next'    => True,
				'type'         => 'plain',
				'prev_text'    => __('«'),
				'next_text'    => __('»'),

	);
?>
<h2>WishList Coupon &raquo; Reports</h2>


<div class="tablenav">
<ul class="subsubsub">

	<li>
	<select style="vertical-align: top;" name="promotion" class="promotion">
		<option value="">Select Promotion</option>
		<?php foreach($promotions as $p): ?>
		<?php $selected = $default_filters['promotion'] == $p->id? 'selected="selected"' : null ?>
		<option <?php echo $selected?> value="<?php echo $p->id?>"><?php echo $p->name?></option>
		<?php endforeach; ?>
	</select>
	<input size="10" type="text"  class="date_from datepicker" placeholder="" value="<?php echo $default_filters['date_from']?>"> To
	<input size="10" type="text"  class="date_to datepicker" placeholder="" value="<?php echo $default_filters['date_to']?>">


	<select style="vertical-align: top;" name="coupon" class="coupon">
		<option value="">Select Coupon</option>
	</select>
	<select style="vertical-align: top;" name="status" class="status">
		<option value="">Select Status</option>
		<option <?php if($default_filters['status'] == TRCK_STATUS_VRFYD) echo 'selected="selected"'?> value="<?php echo TRCK_STATUS_APPLY?>">Applied</option>
		<option <?php if($default_filters['status'] == TRCK_STATUS_COMPL) echo 'selected="selected"'?> value="<?php echo TRCK_STATUS_COMPL?>">Clicked Buy</option>
		<option <?php if($default_filters['status'] == TRCK_STATUS_FINSH) echo 'selected="selected"'?> value="<?php echo TRCK_STATUS_FINSH?>">Completed</option>
	</select>
	<button type="submit" class="button-secondary apply-filters">Apply Filters</button>
	</li>
</ul>

	<div class="tablenav-pages">
		<span class="displaying-num"><?php echo $count?> items</span>
		<span class="pagination-links">
			<?php
			echo paginate_links($paginate_args);
			?>
		</span>
	</div>
</div>



<table class="post widefat">
	<thead>
	<tr>
		<th>USER <?php echo $this->tooltip('tooltip_user')?></th>
		<th>PROMOTION <?php echo $this->tooltip('tooltip_promotion')?></th>
		<th>COUPON CODE <?php echo $this->tooltip('tooltip_coupon_code')?></th>
		<th>STATUS <?php echo $this->tooltip('tooltip_status')?></th>
		<th>DATE <?php echo $this->tooltip('tooltip_date')?></th>

	</tr>
	</thead>

	<?php foreach($report as $i => $r): ?>
	<tr class="<?php if($i%2 == 0) echo 'alternate'?>">
		<td>
			<?php if(empty($r->ID)): ?>
				GUEST
			<?php else: ?>
				<a href=""><?php echo $r->user_email;?> / <?php echo $r->display_name?></a>
			<?php endif; ?>
		</td>
		<td><?php echo $r->promotion?></td>
		<td><?php echo $r->coupon_code?></td>
		<td>
			<?php
			switch ($r->status) {
				case TRCK_STATUS_APPLY:
					echo 'APPLIED';
					break;
				case TRCK_STATUS_COMPL:
					echo 'CLICKED BUY';
					break;
				case TRCK_STATUS_FINSH:
					echo 'COMPLETED';
					break;
				default:
					# code...
					break;
			}
			?>
		</td>
		<td>&nbsp;&nbsp;&nbsp;<?php echo date('M d, Y h:i a', strtotime($r->created))?></td>

	</tr>
	<?php endforeach; ?>
</table>
<div class="tablenav">
	<div class="tablenav-pages">
		<span class="displaying-num"><?php echo $count?> items</span>
		<span class="pagination-links">
			<?php
			echo paginate_links($paginate_args);
			?>
		</span>
	</div>
</div>

<script type="text/javascript">

jQuery(function($) {
	$('.datepicker').datepicker({
		showOn: "button",
      	buttonImage: "<?php echo $this->plugin_url?>assets/images/calendar.gif",
      	buttonImageOnly: true

	});
	function update_coupon_dropdown() {
		$('.coupon').find('option').each(function(i, e) {
			if(i > 0) {
				$(this).remove();
			}
		});


		var current_coupon = '<?php echo $default_filters['coupon']?>';
		var promotion = $('.promotion').val();

		$.get(ajaxurl, { action: 'wishlist_coupon_backbone_coupon_list', promotion_id: promotion}, function(res) {
			var coupons = JSON.parse(res);
			for(i in coupons) {
				$('.coupon').append('<option value="'+coupons[i].coupon_id+'">'+coupons[i].coupon_code+'</option>');
			}
			$('.coupon').val(current_coupon);
		});

	}
	$('.promotion').on('change', function() {
		update_coupon_dropdown();
	});
	$('.apply-filters').on('click', function(ev) {
		ev.preventDefault();
		var url = '<?php echo admin_url('admin.php')?>';
		var data = {
			'page'		: '<?php echo $this->class_name?>',
			'wl'		: 'reports',
			'status'    : $('.status').val(),
			'date_from' : $('.date_from').val(),
			'date_to'   : $('.date_to').val(),
			'promotion' : $('.promotion').val(),
			'coupon'    : $('.coupon').val()
		};

		query_str = "";
		for(i in data) {
			query_str += '&'+i+'='+data[i];
		}
		url += '?' + query_str;
		$(location).prop('href', url);
		return false;
	});
	update_coupon_dropdown();
});
</script>
<?php include $this->plugin_dir .'/admin/tooltips/reports.tooltips.php'?>