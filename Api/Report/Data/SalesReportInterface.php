<?php

namespace SapientPro\Core\Api\Report\Data;

use DateTime;

interface SalesReportInterface
{
    public const TITLE = 'title';

    public const TOTAL = 'total';

    public const DEBIT = 'debit';

    public const CREDIT = 'credit';
    public const DISCOUNT = 'discount';

    public const SOURCES = 'sources';

    public const DATE_FROM = 'date_from';

    public const DATE_TO = 'date_to';

    public function getId();

    public function setId($id);

    /**
     * @return string
     */
    public function getTitle(): string;

    /**
     * @param string $title
     */
    public function setTitle(string $title): void;

    /**
     * @return float
     */
    public function getTotal(): float;

    /**
     * @param float $value
     * @return void
     */
    public function setTotal(float $value): void;

    /**
     * @param float $value
     * @return void
     */
    public function increaseTotal(float $value): void;

    /**
     * @param float $value
     * @return void
     */
    public function decreaseTotal(float $value): void;

    /**
     * @return float
     */
    public function getDebit(): float;

    /**
     * @param float $value
     * @return void
     */
    public function setDebit(float $value): void;

    /**
     * @param float $value
     * @return void
     */
    public function increaseDebit(float $value): void;

    /**
     * @param float $value
     * @return void
     */
    public function decreaseDebit(float $value): void;

    /**
     * @return float
     */
    public function getDiscount(): float;

    /**
     * @param float $value
     * @return void
     */
    public function increaseDiscount(float $value): void;

    /**
     * @param float $value
     * @return void
     */
    public function decreaseDiscount(float $value): void;

    /**
     * @return float
     */
    public function getCredit(): float;

    /**
     * @param float $value
     * @return void
     */
    public function setCredit(float $value): void;

    /**
     * @param float $value
     * @return void
     */
    public function increaseCredit(float $value): void;

    /**
     * @param float $value
     * @return void
     */
    public function decreaseCredit(float $value): void;

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
     * @return DateTime
     */
    public function getDateFrom(): DateTime;

    /**
     * @param DateTime $dateFrom
     * @return void
     */
    public function setDateFrom(DateTime $dateFrom): void;

    /**
     * @return DateTime
     */
    public function getDateTo(): DateTime;

    /**
     * @param DateTime $dateTo
     * @return void
     */
    public function setDateTo(DateTime $dateTo): void;
}
