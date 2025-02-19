<?php

namespace SapientPro\Core\Api\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use DateTime;

interface ReportInterface extends ArgumentInterface
{
    /**
     * Is supervisor
     *
     * @param bool $isSupervisor
     * @return bool
     */
    public function isSupervisor(bool $isSupervisor): bool;

    /**
     * Add date from filter
     *
     * @return mixed
     */
    public function addDateFromFilter(DateTime $dateTime);

    /**
     * Add date to filter
     *
     * @return mixed
     */
    public function addDateToFilter(DateTime $dateTime);

    /**
     * Add source filter
     *
     * @param string $sourceId
     * @return mixed
     */
    public function addSourceFilter(string $sourceId);

    /**
     * Add cashier filter
     *
     * @return mixed
     */
    public function addCashierFilter(int $cashierId);

    /**
     * Add packer filter
     *
     * @return mixed
     */
    public function addPackerFilter(int $packerId);
}
