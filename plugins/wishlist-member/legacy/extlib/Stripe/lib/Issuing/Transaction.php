<?php

namespace WLMStripe\Issuing;

/**
 * Class Transaction
 *
 * @property string $id
 * @property string $object
 * @property int $amount
 * @property string $authorization
 * @property string $balance_transaction
 * @property string $card
 * @property string $cardholder
 * @property int $created
 * @property string $currency
 * @property string $dispute
 * @property bool $livemode
 * @property mixed $merchant_data
 * @property int $merchant_amount
 * @property string $merchant_currency
 * @property \WLMStripe\WLM_StripeObject $metadata
 * @property string $type
 *
 * @package Stripe\Issuing
 */
class Transaction extends \WLMStripe\ApiResource
{
    const OBJECT_NAME = "issuing.transaction";

    use \WLMStripe\ApiOperations\All;
    use \WLMStripe\ApiOperations\Create;
    use \WLMStripe\ApiOperations\Retrieve;
    use \WLMStripe\ApiOperations\Update;
}
