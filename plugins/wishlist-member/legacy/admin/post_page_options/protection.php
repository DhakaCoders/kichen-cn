
<div class="wlm-inside wlm-inside01">
	<!-- Content Protection Toggle -->
	<div class="form-group">
		<div class="switch-toggle switch-toggle-wlm -semi-compressed">
			<input id="protection-settings-unprotected" name="protection_settings" type="radio" value="0" <?php if($protection_settings == '0') echo 'checked="checked"'; ?>>
			<label for="protection-settings-unprotected" onclick=""><?php _e('Unprotected', 'wishlist-member'); ?></label>
			<input id="protection-settings-protected" name="protection_settings" type="radio" value="1" <?php if($protection_settings == '1') echo 'checked="checked"'; ?>>
			<label for="protection-settings-protected" onclick=""><?php _e('Protected', 'wishlist-member'); ?></label>
			<input id="protection-settings-inherited" name="protection_settings" type="radio" value="2" <?php if($protection_settings == '2') echo 'checked="checked"'; ?>>
			<label for="protection-settings-inherited" onclick=""><?php _e('Inherited', 'wishlist-member'); ?></label>
			<a href="" class="btn btn-primary"></a>
		</div>
	</div>
	<input type="hidden" name="wpm_protect" value="">
	<input type="hidden" name="wlm_inherit_protection" value="">
	<div id="wpm-access-options">
		<hr>
		<!-- Membership Levels -->
		<?php if ( count( $wpm_levels ) ): ?>
		<h2 class="wlm-h2"><?php _e( 'Access', 'wishlist-member' ); ?></h2>
		<?php
		$all_access = array();
		$options = '';
		foreach ( ( array ) $wpm_levels AS $id => $level ) {
			if($level[$allindex]) {
				$all_access[] = $level['name'];
				continue;
			}
			$options .= sprintf('<option value="%s" %s>%s</option>', $id, in_array($id, $wpm_access) ? 'selected="selected"' : '', $level['name']);
		}
		if($all_access) {
			printf('<p>%s <strong><em>%s</em></strong></p><br>', sprintf(_n('Level with access to all %s:', 'Levels with access to all %s:', count($all_access), 'wishlist-member'), $allindex == 'allposts' ? 'posts' : 'pages'), implode(', ', $all_access));
		}
		?>
		<div class="form-group" id="wpm-access-form">
			<p class="float-left" for=""><?php _e('Select the membership level(s) that can access this content:', 'wishlist-member'); ?></p>
			<a class="float-right -text-light" id="select-all-levels" href="#"><?php _e('Select All', 'wishlist-member'); ?></a>
			<a class="float-right -text-light" id="clear-all-levels" href="#" style="display: none;"><?php _e('Clear All', 'wishlist-member'); ?></a>
			<div style="clear: both">
				<select name="wpm_access[]" id="" class="form-control wlm-select" style="width: 100%" multiple="multiple"><?php echo $options; ?></select>				
			</div>
		</div>
		<div id="wpm-access-inherited">
			<?php if($protected_taxonomies || $ancestor) : ?>
				<p>
					<?php
					_e('Inherited From:', 'wishlist-member');
					$titles = array();
					if($protected_taxonomies) {
						foreach($protected_taxonomies AS $id) {
							$t = get_term( $id );
							$titles[] = $t->name;
						}
					} else {
						foreach($ancestor AS $id) {
							$titles[] = get_the_title( $id );
						}
					}
					echo ' ' . implode(', ', $titles);
					?>
				</p>
				<p><?php _e('Inherited Status:', 'wishlist-member'); ?> <?php echo $parent_protect ? __('Protected', 'wishlist-member') : __('Unprotected', 'wishlist-member'); ?></p>
				<?php if ($parent_protect) : ?>
				<p>
					<?php
					$inherited_levels = array();
					foreach ( ( array ) $parent_levels AS $id ) {
						if($wpm_levels[$id][$allindex]) continue;
						if(empty($wpm_levels[$id])) continue;
						$inherited_levels[] = $wpm_levels[$id]['name'];
					}
					$inherited_levels = array_unique( $inherited_levels );
					echo _n('Inherited Level:', 'Inherited Levels:', count($inherited_levels), 'wishlist-member');
					echo ' ';
					echo count($inherited_levels) ? implode(', ', $inherited_levels) : __('None', 'wishlist-member');
					?>
				</p>
				<?php endif; ?>
			<?php else : ?>
				<p><?php _e('No parent to inherit protection from.', 'wishlist-member'); ?></p>
			<?php endif; ?>
		</div>
		<?php else : ?>
		<p><?php _e('No membership levels found', 'wishlist-member'); ?></p>
		<?php endif; ?>
	</div>
	<br>
	<hr>
	<div style="text-align: right;">
		<div class="wlm-saved" style="display: none"><?php _e('Saved', 'wishlist-member'); ?></div>
		<div class="wlm-saving" style="display: none"><?php _e('Saving...', 'wishlist-member'); ?></div>
		<a href="#" class="wlm-btn -with-icons -success -centered-span wlm-postpage-apply">
			<i class="wlm-icons"><img src="<?php echo $this->pluginURL3; ?>/ui/images/baseline-save-24px.svg" alt=""></i>
			<span><?php _e('Apply Settings', 'wishlist-member'); ?></span>
		</a>
	</div>

</div>