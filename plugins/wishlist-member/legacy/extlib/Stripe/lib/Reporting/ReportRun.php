<?php

namespace WLMStripe\Reporting;

/**
 * Class ReportRun
 *
 * @property string $id
 * @property string $object
 * @property int $created
 * @property string $error
 * @property bool $livemode
 * @property mixed $parameters
 * @property string $report_type
 * @property mixed $result
 * @property string $status
 * @property int $succeeded_at
 *
 * @package Stripe\Reporting
 */
class ReportRun extends \WLMStripe\ApiResource
{
    const OBJECT_NAME = "reporting.report_run";

    use \WLMStripe\ApiOperations\All;
    use \WLMStripe\ApiOperations\Create;
    use \WLMStripe\ApiOperations\Retrieve;
}
