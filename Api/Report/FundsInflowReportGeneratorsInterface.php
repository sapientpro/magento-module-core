<?php

namespace SapientPro\Core\Api\Report;

use Magento\Framework\Data\Collection;
use DateTime;

interface FundsInflowReportGeneratorsInterface
{
    public function execute(DateTime $dateFrom = null, DateTime $dateTo = null): Collection;
}
