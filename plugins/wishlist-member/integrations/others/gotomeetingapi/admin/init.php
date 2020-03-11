<?php
$data = array('webinar' => array($config['id'] => $webinar_data[$config['id']]));
thirdparty_integration_data($config['id'], $data);

include_once $this->legacy_wlm_dir . '/lib/integration.webinar.gotomeetingapi.php';

$obj = new WLM_GTMAPI_OAuth_En();
$oauth = new WLM_GTMAPI_OAuth($obj);
