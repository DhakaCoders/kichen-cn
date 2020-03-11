<?php
$wpm_levels = $this->GetOption('wpm_levels');
$total_cnt = count( $wpm_levels );
$this->SortLevels( $wpm_levels, 'a', 'levelOrder' );
$this->set_timezone_to_wp();

$howmany = $this->GetOption('wpmlevels_pagination');
if (is_numeric(wlm_arrval($_GET, 'howmany')) || !$howmany) {
	if (wlm_arrval($_GET, 'howmany')) {
		$howmany = (int) $_GET['howmany'];
	}
	if ( !$howmany ) $howmany = $this->pagination_items[1];
	if ( !in_array( $howmany, $this->pagination_items ) ) $howmany = $this->pagination_items[1];
	$this->SaveOption('wpmlevels_pagination', $howmany);
}

$offset = $_GET['offset'] - 1;
if ( $offset < 0 ) $offset = 0;
$offset = $offset * $howmany;
$membership_levels = array_slice( $wpm_levels, $offset, $howmany, true );
$current_page = $offset / $howmany + 1;
$offset += 1;
$total_pages = ceil( $total_cnt / $howmany);

$form_action = "?page={$this->MenuID}&wl=" .( isset( $_GET['wl'] ) ? $_GET['wl'] : "members/sequential" );
?>
	<div class="page-header">
		<div class="row">
			<div class="col-md-9 col-sm-9 col-xs-8">
				<h2 class="page-title"><?php _e( 'Sequential Upgrade', 'wishlist-member' ); ?></h2>
			</div>
			<div class="col-md-3 col-sm-3 col-xs-4">
				<?php include $this->pluginDir3 . '/helpers/header-icons.php'; ?>
			</div>
		</div>
	</div>
<?php if ( $total_cnt && $total_cnt > $this->pagination_items[0] ) : ?>
	<div class="row">
		<div class="col-md-12">
			<div class="pagination -minimal pull-right">
				<div class="count pull-left">
					<div role="presentation" class="dropdown page-rows">
						<a href="#" class="dropdown-toggle" id="drop-page" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
							<?php echo $offset; ?> - <?php echo ($howmany * $current_page) > $total_cnt ? $total_cnt : $howmany * $current_page; ?>
						</a> of <?php echo $total_cnt; ?>
						<ul class="dropdown-menu" id="menu1" aria-labelledby="drop-page">
							<?php foreach ( $this->pagination_items as $key => $value) : ?>
								<a class="dropdown-item" target="_parent" href="<?php echo $form_action ."&howmany=" .$value; ?>"><?php echo $value; ?></a>
							<?php endforeach; ?>
						</ul>
					</div>
				</div>
				<?php if ( $howmany <= $total_cnt ) : ?>
					<div class="arrows pull-right">
						<?php
						if ( $current_page <= 1 ) $previous_link = $form_action ."&offset=" .$total_pages;
						else $previous_link = $form_action ."&offset=" .($current_page-1);
						?>
						<a target="_parent" href="<?php echo $previous_link; ?>" >
							<i class="wlm-icons md-26">keyboard_arrow_left</i>
						</a>
						<?php
						if ( $current_page < $total_pages ) $next_link = $form_action ."&offset=" .($current_page+1);
						else $next_link = $form_action ."&offset=1";
						?>
						<a target="_parent" href="<?php echo $next_link; ?>">
							<i class="wlm-icons md-26">keyboard_arrow_right</i>
						</a>
					</div>
				<?php endif; ?>
			</div>
			<br class="d-none d-sm-block d-md-none">
			<br class="d-none d-sm-block d-md-none">
			<br class="d-none d-sm-block d-md-none">
		</div>
	</div>
<?php endif; ?>
	<div class="row">
		<div class="col-md-12">
			<p><em>WordPress Time: 
				<?php printf('%s %s %s', date( get_option( 'date_format' ) ), date( get_option( 'time_format' ) ), $this->get_wp_tzstring( true ) ); ?>
			</em></p>
			<div class="table-wrapper table-responsive">
				<table class="table table-striped table-condensed">
					<colgroup>
						<col>
						<col width="100">
						<col width="260">
						<col width="300">
					</colgroup>
					<thead>
						<tr>
							<th><?php _e('Membership Level', 'wishlist-member'); ?></th>
							<th><?php _e('Method', 'wishlist-member'); ?> <?php $this->tooltip(__('The Move, Add or Remove method must be selected if a Sequential Upgrade is set up. <br><br>
							Note: If a Member is ADDED, they will retain access to the previous Membership Level and will belong to both Membership Levels after the Sequential Upgrade.<br><br>
							If a Member is MOVED, they will be removed from the previous Membership Level and will only belong to the new Membership Level after the Sequential Upgrade.<br><br>
							If a Member is REMOVED, their access will be removed from the specified Membership Level.', 'wishlist-member'), 'lg'); ?></th>
							<th><?php _e('Upgrade To', 'wishlist-member'); ?></th>
							<th><?php _e('Schedule', 'wishlist-member'); ?> <?php $this->tooltip(__('There are two options for scheduling a Sequential Upgrade.<br><br>
							When using the "On" option, select a date using the date picker. All members will be upgraded on the specified date.<br><br>
							When using the "After" option, select the number of Days/Weeks/Months/Years between the Sequential Upgrade.<br><br>
							Each scheduled increment of time should be based on the desired amount of time that should pass before the Sequential Upgrade occurs.<br><br>
							Each set of time increments will be calculated on a per member basis.', 'wishlist-member'), 'lg'); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ( $membership_levels as $lvlid => $lvl ) : ?>
							<?php
								$inactive = false;
								if ( ( !$lvl['upgradeTo'] && $lvl['upgradeMethod'] != 'REMOVE' ) || ! $lvl['upgradeMethod'] || ($lvl['upgradeSchedule'] == 'ondate' && $lvl['upgradeOnDate'] < 1) || ($lvl['upgradeMethod'] == 'MOVE' && ! (( int ) $lvl['upgradeAfter']) && empty( $lvl['upgradeSchedule'] )) ) {
									$inactive = true;
								}

								$method_class = 'method-inactive';
								if($lvl['upgradeMethod']) {
									$method_class = 'method-' . strtolower( $lvl['upgradeMethod'] );
								}

								$schedule_class = 'schedule-after';
								if($lvl['upgradeSchedule'] == 'ondate') {
									$schedule_class = 'schedule-ondate';
								}
							?>
							<tr class="tr tr-<?php echo $lvlid; ?> <?php echo $method_class . ' ' . $schedule_class; ?>">
								<td>
									<span class="text"><?php echo $lvl['name']; ?></span>
								</td>
								<td>
									<select class="form-control wlm-select upgrade-method" name="upgradeMethod[<?php echo $lvlid; ?>]">
										<option value="inactive" <?php $this->Selected( 'inactive', $lvl['upgradeMethod'] ); ?>><?php _e('None', 'wishlist-member'); ?></option>
										<option value="MOVE" <?php $this->Selected( 'MOVE', $lvl['upgradeMethod'] ); ?>><?php _e('Move', 'wishlist-member'); ?></option>
										<option value="ADD" <?php $this->Selected( 'ADD', $lvl['upgradeMethod'] ); ?>><?php _e('Add', 'wishlist-member'); ?></option>
										<option value="REMOVE" <?php $this->Selected( 'REMOVE', $lvl['upgradeMethod'] ); ?>><?php _e('Remove', 'wishlist-member'); ?></option>
									</select>
								</td>
								<td class="upgrade-to-col">
									<div class="upgrade-to-holder">
										<select class="form-control wlm-select upgrade-to-level" name="upgradeTo[<?php echo $lvlid; ?>]">
											<option value=""><?php _e( '- Select a Level -', 'wishlist-member' ); ?></option>
											<?php foreach ( $wpm_levels as $key => $value ) : ?>
												<?php if ( $lvlid != $key ): ?>
													<option value="<?php echo $key; ?>" <?php $this->Selected( $key, $lvl['upgradeTo'] ); ?>><?php echo $value['name']; ?></option>
												<?php endif; ?>
											<?php endforeach; ?>
										</select>
									</div>
									<div class="remove-from-holder">
										<?php printf(__('Remove from %s', 'wishlist-member'), $lvl['name']); ?>
									</div>
								</td>
								<td>
									<div class="row no-gutters">
										<div class="col-4 schedule-type-holder">
											<div class="switch-toggle switch-toggle-wlm -compressed" style="margin-top: 3px;">
												<input skip-save="1" class="toggle-radio toggle-radio-sched  sched-after" id="after<?php echo $lvlid; ?>" name="sched_toggle_<?php echo $lvlid; ?>" type="radio" value="after" <?php echo $lvl['upgradeSchedule'] != "ondate" ? "checked" : "" ?>>
												<label for="after<?php echo $lvlid; ?>"><?php _e('After', 'wishlist-member'); ?></label>
												<input skip-save="1" class="toggle-radio toggle-radio-sched sched-ondate" id="on<?php echo $lvlid; ?>" name="sched_toggle_<?php echo $lvlid; ?>" type="radio" value="ondate" <?php echo $lvl['upgradeSchedule'] == "ondate" ? "checked" : "" ?> >
												<label for="on<?php echo $lvlid; ?>"><?php _e('On', 'wishlist-member'); ?></label>
												<a href="" class="btn btn-primary"></a>
											</div>
											<input type="hidden" class="sched-hidden" name="upgradeSchedule[<?php echo $lvlid; ?>]" value="<?php echo $lvl['upgradeSchedule']; ?>" >
										</div>
										<div class="col">
											<div class="form-group date-ranger schedule-ondate-holder">
												<label class="sr-only" for=""><?php _e( 'Specific Date', 'wishlist-member' ); ?></label>
												<div class="date-ranger-container">
													<input type="text" name="upgradeOnDate[<?php echo $lvlid; ?>]" class="form-control wlm-datetimepicker" value="<?php echo $lvl['upgradeOnDate'] ? date( "m/d/Y h:i:s a", $lvl['upgradeOnDate'] ) : ''; ?>">
													<i class="wlm-icons">date_range</i>
												</div>
											</div>
											<!--v4: start  -->
											<div class="form-inline -combo-form input-group schedule-after-holder">
												<label class="sr-only" for=""><?php _e( 'Fixed Term', 'wishlist-member' ); ?></label>
												<input type="number" min="0" name="upgradeAfter[<?php echo $lvlid; ?>]" class="form-control text-center" placeholder="0" value="<?php echo ( int ) $lvl['upgradeAfter']; ?>">
													<select class="form-control wlm-select" name="upgradeAfterPeriod[<?php echo $lvlid; ?>]" style="width: 120px;">
														<option value=""><?php _e( 'Day(s)', 'wishlist-member' ); ?></option>
														<option value="weeks" <?php $this->Selected( 'weeks', $lvl['upgradeAfterPeriod'] ); ?>><?php _e( 'Week(s)', 'wishlist-member' ); ?></option>
														<option value="months" <?php $this->Selected( 'months', $lvl['upgradeAfterPeriod'] ); ?>><?php _e( 'Month(s)', 'wishlist-member' ); ?></option>
														<option value="years" <?php $this->Selected( 'years', $lvl['upgradeAfterPeriod'] ); ?>><?php _e( 'Year(s)', 'wishlist-member' ); ?></option>
													</select>
											</div>
											<!--v4: end  -->
										</div>
									</div>
								</td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			</div>
		</div>
		<div class="col-md-12 text-right">
			<input type="hidden" name="action" value="admin_actions" />
			<input type="hidden" name="WishListMemberAction" value="save_sequential" />
			<a href="#" class="btn -primary save-settings">
				<i class="wlm-icons">save</i>
				<span><?php _e( 'Save', 'wishlist-member' ); ?></span>
			</a>
		</div>
	</div>
	<div class="content-wrapper -no-background">
		<h4><?php _e( 'Advanced Settings', 'wishlist-member' ); ?></h4>
		<div class="row">
			<div class="col-md-12">
				<p><?php _e( 'Sequential Upgrades are automatically triggered when a member signs in to their account. If you would like to set your system to trigger upgrades without requiring a member to sign in, you must create a Cron Job on your server.', 'wishlist-member' ); ?></p>
				<p><a href="?page=WishListMember&wl=advanced_settings/cron_jobs" target="_blank"><?php _e( 'Click Here', 'wishlist-member' ); ?></a> for instructions on how to set-up a Cron Job for WishList Member.</p>
			</div>
		</div>
	</div>
	<p>&nbsp;</p><p>&nbsp;</p>
<style type="text/css">
	tr.schedule-ondate .schedule-after-holder,
	tr.schedule-after .schedule-ondate-holder,
	tr.method-inactive .schedule-type-holder,
	tr.method-inactive .schedule-ondate-holder,
	tr.method-inactive .schedule-after-holder,
	tr.method-inactive .remove-from-holder,
	tr.method-add .remove-from-holder,
	tr.method-move .remove-from-holder {
		display: none;
	}
	tr.method-inactive .upgrade-to-holder,
	tr.method-remove .upgrade-to-holder {
		visibility: hidden;
		height: 0;
	}
</style>