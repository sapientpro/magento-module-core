<?php

namespace SapientPro\Core\Model\Report;

use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use SapientPro\Core\Api\Report\Data\SalesReportInterface;
use Magento\Framework\DataObject;

class SalesReport extends DataObject implements SalesReportInterface
{
    private string $title = '';

    private float $total = 0.0;

    private array $sources = [];

    private TimezoneInterface $dateFrom;

    private TimezoneInterface $dateTo;

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * @inherit
     */
    public function getTotal(): float
    {
        return $this->total;
    }

    /**
     * @inherit
     */
    public function setTotal(float $total): void
    {
        $this->total = $total;
    }

    /**
     * @inherit
     */
    public function getSources(): array
    {
        return $this->sources;
    }

    /**
     * @inherit
     */
    public function setSources(array $sources): void
    {
        $this->sources = $sources;
    }

    /**
     * @inherit
     */
    public function getDateFrom(): TimezoneInterface
    {
        return $this->dateFrom;
    }

    /**
     * @inherit
     */
    public function setDateFrom(TimezoneInterface $dateFrom): void
    {
        $this->dateFrom = $dateFrom;
    }

    /**
     * @inherit
     */
    public function getDateTo(): TimezoneInterface
    {
        return $this->dateTo;
    }

    /**
     * @inherit
     */
    public function setDateTo(TimezoneInterface $dateTo): void
    {
        $this->dateTo = $dateTo;
    }
}
