<?php
foreach($wpm_levels AS $level_id => $level) :
?>
<div
	data-process="modal"
	id="sendlane-tags-<?php echo $level_id; ?>-template" 
	data-id="sendlane-tags-<?php echo $level_id; ?>"
	data-label="sendlane-tags-<?php echo $level_id; ?>"
	data-title="Editing <?php echo $config['name']; ?> Actions for <?php echo $level['name']; ?>"
	data-show-default-footer="1"
	data-classes="modal-lg modal-sendlane-actions"
	style="display:none">
	<div class="body">
		<div class="row">
			<div class="col-md-12">
				<ul class="nav nav-tabs">
					<li class="active nav-item"><a class="nav-link" data-toggle="tab" href="#sendlane-when-added-<?php echo $level_id; ?>">When Added</a></li>
					<li class="nav-item"><a class="nav-link" data-toggle="tab" href="#sendlane-when-cancelled-<?php echo $level_id; ?>">When Cancelled</a></li>
					<li class="nav-item"><a class="nav-link" data-toggle="tab" href="#sendlane-when-reregistered-<?php echo $level_id; ?>">When Re-Registered</a></li>
					<li class="nav-item"><a class="nav-link" data-toggle="tab" href="#sendlane-when-removed-<?php echo $level_id; ?>">When Removed</a></li>
				</ul>
			</div>
		</div>
		<div class="tab-content">
			<div class="row tab-pane active in" id="sendlane-when-added-<?php echo $level_id; ?>">
				<div class="row col-md-12">
					<div class="col-md-6">
						<div class="form-group">
							<label>Add to List</label>
							<select class="wlm-select sendlane-list-select" data-placeholder="Select list..." data-allow-clear="true" style="width:100%" name="<?php echo $level_id; ?>[add][list_add]"></select>
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label>Remove from List</label>
							<select class="wlm-select sendlane-list-select" data-placeholder="Select list..." data-allow-clear="true" style="width:100%" name="<?php echo $level_id; ?>[add][list_remove]"></select>
						</div>
					</div>
				</div>
				<div class="col-md-12">
					<div class="form-group">
						<label><?php _e( 'Apply Tags', 'wishlist-member' ); ?></label>
						<select class="wlm-select sendlane-tags-select" multiple="multiple" data-placeholder="Select tags..." style="width:100%" name="<?php echo $level_id; ?>[add][apply_tag][]"></select>
					</div>
				</div>
				<div class="col-md-12">
					<div class="form-group">
						<label><?php _e( 'Remove Tags', 'wishlist-member' ); ?></label>
						<select class="wlm-select sendlane-tags-select" multiple="multiple" data-placeholder="Select tags..." style="width:100%" name="<?php echo $level_id; ?>[add][remove_tag][]"></select>
					</div>
				</div>
			</div>
			<div class="row tab-pane" id="sendlane-when-cancelled-<?php echo $level_id; ?>">
				<div class="row col-md-12">
					<div class="col-md-6">
						<div class="form-group">
							<label>Add to List</label>
							<select class="wlm-select sendlane-list-select" data-placeholder="Select list..." data-allow-clear="true" style="width:100%" name="<?php echo $level_id; ?>[cancel][list_add]"></select>
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label>Remove from List</label>
							<select class="wlm-select sendlane-list-select" data-placeholder="Select list..." data-allow-clear="true" style="width:100%" name="<?php echo $level_id; ?>[cancel][list_remove]"></select>
						</div>
					</div>
				</div>
				<div class="col-md-12">
					<div class="form-group">
						<label><?php _e( 'Apply Tags', 'wishlist-member' ); ?></label>
						<select class="wlm-select sendlane-tags-select" multiple="multiple" data-placeholder="Select tags..." style="width:100%" name="<?php echo $level_id; ?>[cancel][apply_tag][]"></select>
					</div>
				</div>
				<div class="col-md-12">
					<div class="form-group">
						<label><?php _e( 'Remove Tags', 'wishlist-member' ); ?></label>
						<select class="wlm-select sendlane-tags-select" multiple="multiple" data-placeholder="Select tags..." style="width:100%" name="<?php echo $level_id; ?>[cancel][remove_tag][]"></select>
					</div>
				</div>
			</div>
			<div class="row tab-pane" id="sendlane-when-reregistered-<?php echo $level_id; ?>">
				<div class="row col-md-12">
					<div class="col-md-6">
						<div class="form-group">
							<label>Add to List</label>
							<select class="wlm-select sendlane-list-select" data-placeholder="Select list..." data-allow-clear="true" style="width:100%" name="<?php echo $level_id; ?>[rereg][list_add]"></select>
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label>Remove from List</label>
							<select class="wlm-select sendlane-list-select" data-placeholder="Select list..." data-allow-clear="true" style="width:100%" name="<?php echo $level_id; ?>[rereg][list_remove]"></select>
						</div>
					</div>
				</div>
				<div class="col-md-12">
					<div class="form-group">
						<label><?php _e( 'Apply Tags', 'wishlist-member' ); ?></label>
						<select class="wlm-select sendlane-tags-select" multiple="multiple" data-placeholder="Select tags..." style="width:100%" name="<?php echo $level_id; ?>[rereg][apply_tag][]"></select>
					</div>
				</div>
				<div class="col-md-12">
					<div class="form-group">
						<label><?php _e( 'Remove Tags', 'wishlist-member' ); ?></label>
						<select class="wlm-select sendlane-tags-select" multiple="multiple" data-placeholder="Select tags..." style="width:100%" name="<?php echo $level_id; ?>[rereg][remove_tag][]"></select>
					</div>
				</div>
			</div>
			<div class="row tab-pane" id="sendlane-when-removed-<?php echo $level_id; ?>">
				<div class="row col-md-12">
					<div class="col-md-6">
						<div class="form-group">
							<label>Add to List</label>
							<select class="wlm-select sendlane-list-select" data-placeholder="Select list..." data-allow-clear="true" style="width:100%" name="<?php echo $level_id; ?>[remove][list_add]"></select>
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label>Remove from List</label>
							<select class="wlm-select sendlane-list-select" data-placeholder="Select list..." data-allow-clear="true" style="width:100%" name="<?php echo $level_id; ?>[remove][list_remove]"></select>
						</div>
					</div>
				</div>
				<div class="col-md-12">
					<div class="form-group">
						<label><?php _e( 'Apply Tags', 'wishlist-member' ); ?></label>
						<select class="wlm-select sendlane-tags-select" multiple="multiple" data-placeholder="Select tags..." style="width:100%" name="<?php echo $level_id; ?>[remove][apply_tag][]"></select>
					</div>
				</div>
				<div class="col-md-12">
					<div class="form-group">
						<label><?php _e( 'Remove Tags', 'wishlist-member' ); ?></label>
						<select class="wlm-select sendlane-tags-select" multiple="multiple" data-placeholder="Select tags..." style="width:100%" name="<?php echo $level_id; ?>[remove][remove_tag][]"></select>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php
endforeach;
?>
