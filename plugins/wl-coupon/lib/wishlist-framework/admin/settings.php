<?php if ($show_page_menu) : ?>
	<ul class="wlm-sub-menu">
		<li<?php echo (!$_GET['mode']) ? ' class="current"' : '' ?>><a href="?<?php echo $base_url; ?>"><?php echo $this->lang('tab1'); ?></a></li>
		<li<?php echo ($_GET['mode'] == 'tab2') ? ' class="current"' : '' ?>><a href="?<?php echo $base_url; ?>&mode=tab2"><?php echo $this->lang('tab2') ?></a></li>
	</ul>
<?php return; endif; ?>
<h2><?php printf($this->lang('plugin_settings'), $this->name) ?></h2>
<form method="post">
<!-- your form goes here -->
<p class="submit">
	<?php $this->options(); $this->required_options(); ?>
	<input type="hidden" name="<?php echo $this->plugin_action?>" value="Save" />
	<input type="submit" value="<?php echo $this->lang('save_settings')?> "  class="button-primary"/>
</p>
</form>


