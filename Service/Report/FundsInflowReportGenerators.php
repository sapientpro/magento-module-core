<?php

namespace SapientPro\Core\Service\Report;

use SapientPro\Core\Api\Report\Data\SalesReportInterface;
use SapientPro\Core\Api\Report\FundsInflowReportGeneratorsInterface;
use Magento\Framework\Data\Collection;
use Magento\Framework\Data\CollectionFactory;
use Magento\Framework\Data\Collection\ModelFactory;
use DateTime;
use Exception;
use SapientPro\Core\Api\Report\ReportProvider\ReportProviderInterface;

class FundsInflowReportGenerators implements FundsInflowReportGeneratorsInterface
{
    /**
     * @var array
     */
    private array $providers;

    /**
     * @var CollectionFactory
     */
    private CollectionFactory $collectionFactory;

    /**
     * @var ModelFactory
     */
    private ModelFactory $modelFactory;

    public function __construct(
        CollectionFactory $collectionFactory,
        ModelFactory $modelFactory,
        array $providers
    ) {
        $this->providers = $providers;
        $this->collectionFactory = $collectionFactory;
        $this->modelFactory = $modelFactory;
    }

    protected array $orderFilters = [
        'customer_id' => null,
        'cashier_id' => null,
        'packer_id' => null,
        'pos_source' => null,
    ];

    /**
     * @inheirtdoc
     */
    public function addCustomerFilter(int $customerId): void
    {
        $this->orderFilters['customer_id'] = $customerId;
    }

    /**
     * @inheirtdoc
     */
    public function addCashierFilter(int $cashierId): void
    {
        $this->orderFilters['cashier_id'] = $cashierId;
    }

    /**
     * @inheirtdoc
     */
    public function addPackerFilter(int $packerId): void
    {
        $this->orderFilters['packer_id'] = $packerId;
    }

    /**
     * @inheirtdoc
     */
    public function addSourceFilter(string $sourceId): void
    {
        $this->orderFilters['pos_source'] = $sourceId;
    }

    /**
     * @inheirtdoc
     */
    public function execute(DateTime $dateFrom = null, DateTime $dateTo = null): Collection
    {
        $collection = $this->collectionFactory->create();
        /** @var ReportProviderInterface $provider */
        foreach ($this->providers as $provider) {
            $provider->setFilters($this->orderFilters);
            $result = $provider->execute($dateFrom, $dateTo);

            /** @var SalesReportInterface $item */
            foreach ($result->getItems() as $item) {
                /** @var SalesReportInterface $report */
                $report = $collection->getItemById($item->getId());
                if ($report) {
                    $report->increaseTotal($item->getTotal());
                    $report->increaseDebit($item->getDebit());
                } else {
                    $collection->addItem($item);
                }
            }
        }

        return $collection;
    }
}
