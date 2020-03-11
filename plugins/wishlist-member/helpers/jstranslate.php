<?php

// internal use only
// file generates helpers/jslang.php

$jsfiles = [];
function findjs($path = '') {
	global $jsfiles;
	$jsfiles = array_merge($jsfiles, glob($path . '*.js'));
	$folders = glob($path . '*', GLOB_ONLYDIR | GLOB_MARK);
	foreach($folders AS $folder) {
		findjs($folder);
	}
}

findjs();

$strings = array();

while($file=array_shift($jsfiles)) {
	$content = file_get_contents($file);
	preg_match_all("#wlm.translate\s*\(\s*('.+?(?<!\\\\)')#", $content, $matches);
	if($matches[1]) {
		foreach($matches[1] AS $match) {
			$strings[] = substr($match, 1, -1);
		}
	}
	preg_match_all('#wlm.translate\s*\(\s*(".+?(?<!\\\\)")#', $content, $matches);
	if($matches[1]) {
		foreach($matches[1] AS $match) {
			$strings[] = substr($match, 1, -1);
		}
	}
}

$strings = array_unique($strings);

$output = '';

$output .= "<?php\nwp_localize_script( 'wishlistmember3-combined-scripts', 'wlm3l10n', array(\n";
while($string = array_shift($strings)) {
	$output .= sprintf("\t'%s' => esc_html__( '%s', 'wishlist-member' ),\n", $string, $string);
}
$output .= ") );";

file_put_contents(__DIR__ . '/jslang.php', $output);