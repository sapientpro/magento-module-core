<?php

namespace SapientPro\Core\Api\Report\ReportProvider;

use Magento\Framework\Data\Collection;
use DateTime;

interface ReportProviderInterface
{
    public function setFilters(array $filters): void;

    public function getCashiers(): Collection;

    public function getPackers(): Collection;

    public function getSources(): Collection;

    public function execute(DateTime $dateFrom = null, DateTime $dateTo = null): Collection;
}
