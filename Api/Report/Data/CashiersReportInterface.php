<?php

namespace SapientPro\Core\Api\Report\Data;

use Magento\Framework\DataObject;

interface CashiersReportInterface
{
    /**
     * Add a cashier to the report
     *
     * @param DataObject $cashier
     * @return void
     */
    public function addCashier(DataObject $cashier): void;

    /**
     * Get the list of cashiers
     *
     * @return array
     */
    public function getCashiers(): array;
}
