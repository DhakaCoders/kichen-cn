<?php

namespace WLMStripe\Terminal;

/**
 * Class Reader
 *
 * @property string $id
 * @property string $object
 * @property bool $deleted
 * @property string $device_sw_version
 * @property string $device_type
 * @property string $ip_address
 * @property string $label
 * @property string $location
 * @property string $serial_number
 * @property string $status
 *
 * @package Stripe\Terminal
 */
class Reader extends \WLMStripe\ApiResource
{
    const OBJECT_NAME = "terminal.reader";

    use \WLMStripe\ApiOperations\All;
    use \WLMStripe\ApiOperations\Create;
    use \WLMStripe\ApiOperations\Delete;
    use \WLMStripe\ApiOperations\Retrieve;
    use \WLMStripe\ApiOperations\Update;
}
