<?php

namespace WLMStripe\Radar;

/**
 * Class ValueList
 *
 * @property string $id
 * @property string $object
 * @property string $alias
 * @property int $created
 * @property string $created_by
 * @property string $item_type
 * @property Collection $list_items
 * @property bool $livemode
 * @property StripeObject $metadata
 * @property mixed $name
 * @property int $updated
 * @property string $updated_by
 *
 * @package Stripe\Radar
 */
class ValueList extends \WLMStripe\ApiResource
{
    const OBJECT_NAME = "radar.value_list";

    use \WLMStripe\ApiOperations\All;
    use \WLMStripe\ApiOperations\Create;
    use \WLMStripe\ApiOperations\Delete;
    use \WLMStripe\ApiOperations\Retrieve;
    use \WLMStripe\ApiOperations\Update;
}
