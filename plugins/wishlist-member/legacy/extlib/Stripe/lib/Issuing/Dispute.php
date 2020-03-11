<?php

namespace WLMStripe\Issuing;

/**
 * Class Dispute
 *
 * @property string $id
 * @property string $object
 * @property int $amount
 * @property int $created
 * @property string $currency
 * @property mixed $evidence
 * @property bool $livemode
 * @property \WLMStripe\WLM_StripeObject $metadata
 * @property string $reason
 * @property string $status
 * @property Transaction $transaction
 *
 * @package Stripe\Issuing
 */
class Dispute extends \WLMStripe\ApiResource
{
    const OBJECT_NAME = "issuing.dispute";

    use \WLMStripe\ApiOperations\All;
    use \WLMStripe\ApiOperations\Create;
    use \WLMStripe\ApiOperations\Retrieve;
    use \WLMStripe\ApiOperations\Update;
}
