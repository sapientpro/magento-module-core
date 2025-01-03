<?php

namespace SapientPro\Core\Api\Report\Data;

use Magento\Framework\Stdlib\DateTime\TimezoneInterface;

interface SalesReportInterface
{
    /**
     * @return string
     */
    public function getTitle(): string;

    /**
     * @param string $title
     * @return string
     */
    public function setTitle(string $title): void;

    /**
     * @return float
     */
    public function getTotal(): float;

    /**
     * @param float $total
     * @return void
     */
    public function setTotal(float $total): void;

    /**
     * @return array
     */
    public function getSources(): array;

    /**
     * @param array $sources
     * @return void
     */
    public function setSources(array $sources): void;

    /**
     * @return TimezoneInterface
     */
    public function getDateFrom(): TimezoneInterface;

    /**
     * @param TimezoneInterface $dateFrom
     * @return void
     */
    public function setDateFrom(TimezoneInterface $dateFrom): void;

    /**
     * @return TimezoneInterface
     */
    public function getDateTo(): TimezoneInterface;

    /**
     * @param TimezoneInterface $dateTo
     * @return void
     */
    public function setDateTo(TimezoneInterface $dateTo): void;
}
