<?php

namespace SapientPro\Core\Api\Report;

use Magento\Framework\Data\Collection;
use DateTime;

interface SalesReportGeneratorInterface
{
    public const DATE_FORMAT = 'Y-m-d';

    public function generate(DateTime $dateFrom = null, DateTime $dateTo = null): Collection;
}
