<?php
/* Payment Integration : PayPal Payments Standard */
include dirname(__DIR__) . '/paypal/assets/common.php';
include_once 'admin/init.php';

$tabs = array(
	'settings' => 'Settings',
	'products' => 'Products',
	'cancellations' => 'Cancellations',
	'tutorial' => 'Tutorial',
);
$active_tab = 'settings';

printf('<div class="form-text text-danger help-block"><p class="mb-0">%s</p></div>', 'The PayPal Payments Standard integration has been deprecated. Any previously set up PayPal Payments Standard integrations with Levels will still continue to function. But it is strongly recommended to use the PayPal Checkout integration for any additional PayPal integrations moving forward. <a href="?page=WishListMember&wl=setup/integrations/payment_provider/paypalec">Click here for more information on the PayPal Checkout integration</a>.');

echo '<ul class="nav nav-tabs">';
foreach($tabs AS $k => $v) {
	$active = $active_tab == $k ? 'active' : '';
	printf('<li class="%s nav-item"><a class="nav-link" data-toggle="tab" href="#%s_%s">%s</a></li>', $active, $config['id'], $k, $v);
}
echo '</ul>';
echo '<div class="tab-content">';
foreach($tabs AS $k => $v) {
	$active = $active_tab == $k ? 'active in' : '';
	printf('<div id="%s_%s" class="tab-pane %s">', $config['id'], $k, $active);
	include_once 'admin/tabs/' . $k . '.php';
	echo '</div>';
}
echo '</div>';

printf('<div data-script="%s"></div>', plugin_dir_url(__FILE__) . 'assets/admin.js');
printf('<div data-style="%s"></div>', plugin_dir_url(__FILE__) . 'assets/admin.css');
