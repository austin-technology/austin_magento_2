<?php

namespace Magenest\StripePayment\Helper;

use Magento\Framework\Logger\Handler\Base;

class Handler extends Base
{
    protected $fileName = '/var/log/stripe/debug.log';
    protected $loggerType = \Monolog\Logger::DEBUG;
}
