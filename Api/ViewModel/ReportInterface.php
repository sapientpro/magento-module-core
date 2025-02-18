<?php

namespace SapientPro\Core\Api\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;

interface ReportInterface extends ArgumentInterface
{
    public function addDateFromFilter();

    public function addDateToFilter();

    public function addSourceFilter(string $sourceId);

    public function addCashierFilter();

    public function addPackerFilter();
}
