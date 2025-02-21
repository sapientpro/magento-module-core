<?php

namespace SapientPro\Core\Model\Report;

use DateTime;
use SapientPro\Core\Api\Report\Data\SalesReportInterface;
use Magento\Framework\Model\AbstractModel;

class SalesReport extends AbstractModel implements SalesReportInterface
{
    /**
     * @inherit
     */
    public function getTitle(): string
    {
        return $this->getData(SalesReportInterface::TITLE) ?? '';
    }

    /**
     * @inherit
     */
    public function setTitle(string $title): void
    {
        $this->setData(SalesReportInterface::TITLE, $title);
    }

    /**
     * @inherit
     */
    public function getTotal(): float
    {
        return $this->getData(SalesReportInterface::TOTAL) ?? 0;
    }

    /**
     * @inherit
     */
    public function setTotal(float $value): void
    {
        $this->setData(SalesReportInterface::TOTAL, $value);
    }

    /**
     * @inherit
     */
    public function increaseTotal(float $value): void
    {
        $value = $this->getData(SalesReportInterface::TOTAL) + $value;
        $this->setData(SalesReportInterface::TOTAL, $value);
    }

    /**
     * @inherit
     */
    public function decreaseTotal(float $value): void
    {
        $value = $this->getData(SalesReportInterface::TOTAL) - $value;
        $this->setData(SalesReportInterface::TOTAL, $value);
    }

    /**
     * @inherit
     */
    public function getSources(): array
    {
        return $this->getData(SalesReportInterface::SOURCES);
    }

    /**
     * @inherit
     */
    public function setSources(array $sources): void
    {
        $this->setData(SalesReportInterface::SOURCES, $sources);
    }

    /**
     * @inherit
     */
    public function getDateFrom(): \DateTime
    {
        return $this->getData(SalesReportInterface::DATE_FROM);
    }

    /**
     * @inherit
     */
    public function setDateFrom(DateTime $dateFrom): void
    {
        $this->setData(SalesReportInterface::DATE_FROM, $dateFrom);
    }

    /**
     * @inherit
     */
    public function getDateTo(): DateTime
    {
        return $this->getData(SalesReportInterface::DATE_TO);
    }

    /**
     * @inherit
     */
    public function setDateTo(DateTime $dateTo): void
    {
        $this->setData(SalesReportInterface::DATE_TO, $dateTo);
    }

    /**
     * @inherit
     */
    public function getDebit(): float
    {
        return $this->getData(SalesReportInterface::DEBIT) ?? 0.0;
    }

    /**
     * @inherit
     */
    public function setDebit(float $value): void
    {
        $this->setData(SalesReportInterface::DEBIT, $value);
    }

    /**
     * @inherit
     */
    public function increaseDebit(float $value): void
    {
        $this->setData(SalesReportInterface::DEBIT, $this->getData(SalesReportInterface::DEBIT) + $value);
    }

    /**
     * @inherit
     */
    public function decreaseDebit(float $value): void
    {
        $this->setData(SalesReportInterface::DEBIT, $this->getData(SalesReportInterface::DEBIT) - $value);
    }

    /**
     * @inherit
     */
    public function getCredit(): float
    {
        return $this->getData(SalesReportInterface::CREDIT) ?? 0;
    }

    /**
     * @inherit
     */
    public function setCredit(float $value): void
    {
        $this->setData(SalesReportInterface::CREDIT, $value);
    }

    /**
     * @inherit
     */
    public function increaseCredit(float $value): void
    {
        $this->setData(SalesReportInterface::CREDIT, $this->getData(SalesReportInterface::CREDIT) + $value);
    }

    /**
     * @inherit
     */
    public function decreaseCredit(float $value): void
    {
        $this->setData(SalesReportInterface::CREDIT, $this->getData(SalesReportInterface::CREDIT) - $value);
    }

    /**
     * @inherit
     */
    public function increaseDiscount(float $value): void
    {
        $this->setData(SalesReportInterface::DISCOUNT, $this->getData(SalesReportInterface::DISCOUNT) + $value);
    }

    /**
     * @inherit
     */
    public function decreaseDiscount(float $value): void
    {
        $this->setData(SalesReportInterface::DISCOUNT, $this->getData(SalesReportInterface::DISCOUNT) - $value);
    }
}
