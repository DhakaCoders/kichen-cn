<?php

namespace WLMStripe\Terminal;

/**
 * Class ConnectionToken
 *
 * @property string $secret
 *
 * @package Stripe\Terminal
 */
class ConnectionToken extends \WLMStripe\ApiResource
{
    const OBJECT_NAME = "terminal.connection_token";

    use \WLMStripe\ApiOperations\Create;
}
