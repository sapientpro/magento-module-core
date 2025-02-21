<?php

namespace SapientPro\Core\Service\Report;

use SapientPro\Core\Api\Report\Data\CashiersReportInterface;
use Magento\Framework\Data\Collection;
use Magento\Framework\DataObject;

class CashiersReport implements CashiersReportInterface
{
    /**
     * @var Collection
     */
    private Collection $cashiersCollection;

    /**
     * CashiersReport constructor.
     *
     * @param Collection $collection
     */
    public function __construct(Collection $collection)
    {
        $this->cashiersCollection = $collection;
    }

    /**
     * @inheirtDoc
     */
    public function addCashier(DataObject $cashier): void
    {
        if (!$this->cashiersCollection->getItemById($cashier->getId())) {
            $this->cashiersCollection->addItem($cashier);
        }
    }

    /**
     * @inheirtDoc
     */
    public function getCashiers(): array
    {
        return $this->cashiersCollection->getItems();
    }
}
