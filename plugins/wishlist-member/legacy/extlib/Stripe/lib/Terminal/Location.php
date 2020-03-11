<?php

namespace WLMStripe\Terminal;

/**
 * Class Location
 *
 * @property string $id
 * @property string $object
 * @property mixed $address
 * @property bool $deleted
 * @property string $display_name
 *
 * @package Stripe\Terminal
 */
class Location extends \WLMStripe\ApiResource
{
    const OBJECT_NAME = "terminal.location";

    use \WLMStripe\ApiOperations\All;
    use \WLMStripe\ApiOperations\Create;
    use \WLMStripe\ApiOperations\Delete;
    use \WLMStripe\ApiOperations\Retrieve;
    use \WLMStripe\ApiOperations\Update;
}
