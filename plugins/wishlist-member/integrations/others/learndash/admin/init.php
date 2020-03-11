<?php
$data = (array) $this->GetOption( 'learndash_settings' );

thirdparty_integration_data(
	$config['id'], array(
		'learndash_settings' => $data,
	)
);

