<?php

namespace BeautyFort\BeautyfortProductImport\Logger;

use Magento\Framework\Logger\Handler\Base;
use Monolog\Logger;

class Handler extends Base
{
    protected $fileName = '/var/log/beautyfort_product_import.log';
    protected $loggerType = Logger::DEBUG;
}
