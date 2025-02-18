<?php

namespace SapientPro\Core\Api\Report;

use Magento\Framework\Data\Collection;
use DateTime;

interface FundsInflowReportGeneratorsInterface
{
    /**
     * Add filter by customer id
     *
     * @param int $customerId
     * @return void
     */
    public function addCustomerFilter(int $customerId): void;

    /**
     * Add filter by cashier id
     *
     * @param int $cashierId
     * @return void
     */
    public function addCashierFilter(int $cashierId): void;

    /**
     * Add filter by packer id
     *
     * @param int $packerId
     * @return void
     */
    public function addPackerFilter(int $packerId): void;

    /**
     * Add filter by source id
     *
     * @param string $sourceId
     * @return void
     */
    public function addSourceFilter(string $sourceId): void;

    /**
     * Prepare report data
     *
     * @param DateTime|null $dateFrom
     * @param DateTime|null $dateTo
     * @return Collection
     */
    public function execute(DateTime $dateFrom = null, DateTime $dateTo = null): Collection;
}
