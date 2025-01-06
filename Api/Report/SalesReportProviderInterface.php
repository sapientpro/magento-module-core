<?php

namespace SapientPro\Core\Api\Report;

use Magento\Framework\Data\Collection;
use DateTime;

interface SalesReportProviderInterface
{
    public const DATE_FORMAT = 'Y-m-d';

    public function preparePaymentRefundReportData(DateTime $dateFrom = null, DateTime $dateTo = null): Collection;

    public function prepareSalesReportData(DateTime $dateFrom = null, DateTime $dateTo = null): Collection;

    public function prepareRefundReportData(DateTime $dateFrom = null, DateTime $dateTo = null): Collection;
}
