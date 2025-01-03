<?php

namespace SapientPro\Core\Service\Report;

use Magento\Framework\Data\Collection;
use Magento\Framework\Data\CollectionFactory;
use SapientPro\Core\Api\Report\Data\SalesReportInterface;
use SapientPro\Core\Api\Report\Data\SalesReportInterfaceFactory;
use SapientPro\Core\Api\Report\SalesReportGeneratorInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\FilterBuilder;
use Magento\Sales\Api\InvoiceRepositoryInterfaceFactory;
use Magento\Sales\Api\CreditmemoRepositoryInterfaceFactory;
use DateTime;
use Exception;

class SalesReportGenerator implements SalesReportGeneratorInterface
{
    private SalesReportInterfaceFactory $salesReportFactory;

    private CollectionFactory $collectionFactory;

    protected InvoiceRepositoryInterfaceFactory $invoiceCollectionFactory;

    protected CreditmemoRepositoryInterfaceFactory $creditmemoCollectionFactory;

    private SearchCriteriaBuilder $searchCriteriaBuilder;

    private FilterBuilder $filterBuilder;

    public function __construct(
        SalesReportInterfaceFactory $salesReportFactory,
        CollectionFactory $collectionFactory,
        InvoiceRepositoryInterfaceFactory $invoiceCollectionFactory,
        CreditmemoRepositoryInterfaceFactory $creditmemoCollectionFactory,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        FilterBuilder $filterBuilder
    ) {
        $this->salesReportFactory = $salesReportFactory;
        $this->collectionFactory = $collectionFactory;
        $this->invoiceCollectionFactory = $invoiceCollectionFactory;
        $this->creditmemoCollectionFactory = $creditmemoCollectionFactory;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->filterBuilder = $filterBuilder;
    }

    /**
     * @throws Exception
     */
    public function generate(DateTime $dateFrom = null, DateTime $dateTo = null): Collection
    {
        $collectTotals = [];
        $collection = $this->collectionFactory->create();
        $invoiceCollection = $this->invoiceCollectionFactory->create();
        $creditmemoCollection = $this->creditmemoCollectionFactory->create();

        $filters = [];
        if ($dateFrom) {
            $filters[] = $this->filterBuilder
                ->setField('created_at')
                ->setConditionType('gteq')
                ->setValue($dateFrom->format('Y-m-d H:i:s'))
                ->create();
        }

        if ($dateTo) {
            $filters[] = $this->filterBuilder
                ->setField('created_at')
                ->setConditionType('lteq')
                ->setValue($dateFrom->format('Y-m-d H:i:s'))
                ->create();
        }

        $searchCriteriaBuilder = $this->searchCriteriaBuilder
            ->addFilters($filters)
            ->create();

        $invoices = $invoiceCollection->getList($searchCriteriaBuilder);

        foreach ($invoices->getItems() as $invoice) {
            $order = $invoice->getOrder();
            $method = $order->getPayment()->getMethod();
            if (!isset($collectTotals[$method])) {
                $collectTotals[$method] = 0;
            }
            $collectTotals[$method] += $invoice->getGrandTotal();
        }

        $searchCriteriaBuilder = $this->searchCriteriaBuilder->create();
        $creditMemos = $creditmemoCollection->getList($searchCriteriaBuilder);

        foreach ($creditMemos->getItems() as $creditMemo) {
            $order = $creditMemo->getOrder();
            $method = $order->getPayment()->getMethod();
            if (!isset($collectTotals[$method])) {
                $collectTotals[$method] = 0;
            }
            $collectTotals[$method] -= $creditMemo->getGrandTotal();
        }

        foreach ($collectTotals as $method => $total) {
            /** @var SalesReportInterface $salesReport */
            $salesReport = $this->salesReportFactory->create();
            $salesReport->setTitle($method);
            $salesReport->setTotal($total);
            $collection->addItem($salesReport);
        }

        return $collection;
    }
}
