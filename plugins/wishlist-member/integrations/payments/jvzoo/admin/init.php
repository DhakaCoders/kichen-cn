<?php
$data = new stdClass();

$data->jvzoothankyou = $this->GetOption( 'jvzoothankyou' );
if ( ! $data->jvzoothankyou ) {
	$this->SaveOption( 'jvzoothankyou', $data->jvzoothankyou = $this->MakeRegURL() );
}
$data->jvzoosecret = $this->GetOption( 'jvzoosecret' );
if ( ! $data->jvzoosecret ) {
	$this->SaveOption( 'jvzoosecret', $data->jvzoosecret = $this->PassGen() . $this->PassGen() );
}

$data->jvzoothankyou_url = $wpm_scregister . $data->jvzoothankyou;

thirdparty_integration_data($config['id'], $data);
