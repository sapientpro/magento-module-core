<?php

namespace SapientPro\Core\Service\Report;

use SapientPro\Core\Api\Report\Data\PackersReportInterface;
use Magento\Framework\Data\Collection;
use Magento\Framework\DataObject;

class PackersReport implements PackersReportInterface
{
    /**
     * @var Collection
     */
    private Collection $packersCollection;

    /**
     * CashiersReport constructor.
     *
     * @param Collection $collection
     */
    public function __construct(Collection $collection)
    {
        $this->packersCollection = $collection;
    }

    /**
     * @inheirtDoc
     */
    public function addPacker(DataObject $packer): void
    {
        if (!$this->packersCollection->getItemById($packer->getId())) {
            $this->packersCollection->addItem($packer);
        }
    }

    /**
     * @inheirtDoc
     */
    public function getPackers(): array
    {
        return $this->packersCollection->getItems();
    }
}
