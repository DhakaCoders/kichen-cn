<?php

namespace WLMStripe\Radar;

/**
 * Class ValueListItem
 *
 * @property string $id
 * @property string $object
 * @property int $created
 * @property string $created_by
 * @property string $list
 * @property bool $livemode
 * @property string $value
 *
 * @package Stripe\Radar
 */
class ValueListItem extends \WLMStripe\ApiResource
{
    const OBJECT_NAME = "radar.value_list_item";

    use \WLMStripe\ApiOperations\All;
    use \WLMStripe\ApiOperations\Create;
    use \WLMStripe\ApiOperations\Delete;
    use \WLMStripe\ApiOperations\Retrieve;
}
