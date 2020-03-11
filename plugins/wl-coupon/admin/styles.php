<?php if ($show_page_menu) : ?>
<?php return; endif; ?>
<?php
$styles  = $this->wldb->get_available_styles();
$redirect_to = admin_url('admin.php') ."?page={$this->class_name}&wl=styles";
$messages = array(
	'removed' => __('The style has been deleted', 'wishlist-coupon'),
	'updated' => __('The style has been updated', 'wishlist-coupon'),
	'created' => __('A new style has been created', 'wishlist-coupon'),
	'cloned' => __('A new style has been created', 'wishlist-coupon'),
	'createfail' => __('Cannot create style, name already exists', 'wishlist-coupon'),
	'updatefail' => __('Cannot update style, name is in conflict with another style', 'wishlist-coupon'),

	//''
);
?>
<h2>Styles</h2>
<?php if(isset($_GET['status']) && $_GET['status'] == 'ok'): ?>

	<div id="message" class="updated below-h2 fade"><p><?php echo $messages[$_GET['act']]?></p></div>
<?php elseif ($_GET['status'] == 'fail'): ?>
	<div id="message" class="updated below-h2 error fade"><p><?php echo $messages[$_GET['act']]?></p></div>
<?php endif; ?>

<h3>Available Styles</h3>
<div class="jcarousel-wrapper">
					<div class="jcarousel">
						<ul>
							<?php foreach($styles as $i => $s): ?>
								<li>
									<div style="display:none" class="<?php echo preg_replace('/\W/', '_', $i)?>">
<?php echo $s['content']?>
								</div>
									<div class="style-name"><?php echo $s['name']?></div>
									<img width="185" height="150" src="<?php echo $s['screenshot']?>"></img>
									<br/>
									<div style="padding: 2px 2px 2px 2px;">
										<div>
											<a href="<?php echo add_query_arg(array($this->class_name.'Action' => 'clone_style', 'id' => $i, 'redirect_to' => urlencode($redirect_to)));?>" class="clone-style">clone</a>
											<?php if(!$s['default']): ?>
											| <a id="<?php echo $i?>" href="<?php echo preg_replace('/\W/', '_', $i)?>" class="edit-style">edit</a>
											| <a href="<?php echo add_query_arg(array($this->class_name.'Action' => 'remove_style', 'id' => $i, 'redirect_to' => urlencode($redirect_to)));?>" class="remove-style">delete</a>
											<?php endif; ?>
										</div>
									</div>
								</li>
							<?php endforeach;?>
						</ul>
					</div>

				<a href="#" class="jcarousel-control-prev">&lsaquo;</a>
				<a href="#" class="jcarousel-control-next">&rsaquo;</a>
				</div>


<hr/>



<form method="post" enctype="multipart/form-data">
<button class="button button-primary create-style">Create New Style</button>

<div id="update-styles">
<input type="hidden" class="id" name="id" value=""/>

<table class="form-table" style="margin-left: 30px;">
	<tr>
		<th><label>Style Name: </label></th>
		<td><input class="name" type="text" name="name" value="" /> <?php echo $this->tooltip('tooltip_name')?></td>
	</tr>
	<tr>
		<th><label>Screenshot: <em>Optional<em></label></th>
		<td><input class="screenshot" type="file" name="screenshot" value="" /> <?php echo $this->tooltip('tooltip_file')?> </td>
	</tr>
	<tr>
		<th><label>Style Content: </label></th>
		<td>
			<textarea class="content" name="content" rows="10" cols="50"></textarea>
			<?php echo $this->tooltip('tooltip_content')?>
		</td>
	</tr>
	<tr>
		<th></th>
		<td>
			<input type="hidden" name="<?php echo $this->class_name?>Action" value="create_style"/>
			<input type="hidden" name="redirect_to" value="<?php echo $redirect_to;?>"/>
			<input type="submit" value="Save Style" class="button-primary"/>
			<input type="submit" value="Cancel" class="button-secondary cancel"/>
		</td>
	</tr>

</table>
</div>



</form>


<div id="default-content" style="display:none;">
	<!-- this is a sample style -->
	<strong>[wishlist_coupon_label]</strong><br/>
	<p class="wishlist-coupon-alert">
		[wishlist_coupon_success]
		[wishlist_coupon_alert]
	</p>
	[wishlist_coupon_code] [wishlist_coupon_apply] [wishlist_coupon_pay]


	<style type="text/css">
	.wishlist-coupon-success{
		color: green;
	}

	.wishlist-coupon-alert {
		color: red;
	}

	</style>

</div>
<style type="text/css">
textarea {
		vertical-align: top;
	}
.sample {
	display: none;
}
.style-name {
	text-align: center;
}

.jcarousel {
	height: 202px;
}

#update-styles label.error {
	margin-top: 3px;
	color: red;
	display: block;
}
</style>

<script type="text/javascript">
jQuery(function($) {

	var carousel = $('.jcarousel').jcarousel();
	$('.jcarousel-control-prev')
		.on('jcarouselcontrol:active', function() {
			$(this).removeClass('inactive');
		})
		.on('jcarouselcontrol:inactive', function() {
			$(this).addClass('inactive');
		})
		.jcarouselControl({
			target: '-=4'
	});

	$('.jcarousel-control-next')
		.on('jcarouselcontrol:active', function() {
			$(this).removeClass('inactive');
		})
		.on('jcarouselcontrol:inactive', function() {
			$(this).addClass('inactive');
		})
		.jcarouselControl({
			target: '+=4'
	});


	var show_style_editor = function() {
		$('#update-styles').show('slide', function() {
			$("html, body").animate({ scrollTop: $(document).height() }, 1000);
		});
	}

	var edit_style = function (id) {
		var el = $('a[href='+id+']');
		var idx = '.' + id;

		if($('#update-styles').is(':visible')) {
			$('#update-styles').hide('slide', function() {
				show_style_editor();
				$('.id').val( el.prop('id') );
				$('.name').val( el.prop('id') )
				$('.content').val( $(idx).html() )
			});
		} else {
			show_style_editor();
			$('.id').val( el.prop('id') );
			$('.name').val( el.prop('id') )
			$('.content').val( $(idx).html() )

		}
	}
	$('.edit-style').on('click', function(ev) {
		ev.preventDefault();
		edit_style($(this).attr('href'));
		return false;
	});


	$('.remove-style').on('click', function(ev) {
		return confirm("Are you sure you want to delete this style?");
	});

	$('#update-styles').validate( {
		rules: {
			'name': {required: true},
			'content': {required: true}
		}
	});

	$('.create-style').on('click', function(ev) {
		ev.preventDefault();

		$('.id').val('');
		$('.name').val("<?php _e("A New Style", "wishlist-coupon")?>");
		$('.content').val($('#default-content').html());

		if($('#update-styles').is(':visible')) {
			$('#update-styles').hide('slide', function() {
				show_style_editor();
			});
		} else {
			show_style_editor();
		}
	});

	$('.cancel').on('click', function(ev) {
		ev.preventDefault();
		$('#update-styles').hide('slide');
	});

	<?php if(isset($_GET['id']) && !empty($_GET['id'])): ?>
	edit_style('<?php echo $_GET['id']?>');
	<?php endif; ?>
});

</script>

<style type="text/css">
.form-table th {
	width: 100px;
}
#update-styles {
	display: none;
}
textarea {
	width: 80% !important;
	height: 320px;
}
</style>
<?php include $this->plugin_dir .'/admin/tooltips/styles.tooltips.php'?>
