<?php

namespace SapientPro\Core\Service\Report;

use Magento\Framework\Data\Collection;
use Magento\Framework\Data\CollectionFactory;
use Magento\Framework\Data\Collection\ModelFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Api\Data\OrderInterface;
use SapientPro\Core\Api\Report\Data\SalesReportInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\FilterBuilder;
use Magento\Sales\Api\InvoiceRepositoryInterfaceFactory;
use Magento\Sales\Api\CreditmemoRepositoryInterfaceFactory;
use Magento\Framework\Api\SearchCriteria;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use DateMalformedStringException;
use DateTime;
use Exception;

class SalesReportProvider
{
    private CollectionFactory $collectionFactory;

    protected InvoiceRepositoryInterfaceFactory $invoiceCollectionFactory;

    protected CreditmemoRepositoryInterfaceFactory $creditmemoCollectionFactory;

    private SearchCriteriaBuilder $searchCriteriaBuilder;

    private FilterBuilder $filterBuilder;

    private ModelFactory $modelFactory;

    private TimezoneInterface $timezone;

    private array $orderFilters = [
        'customer_id' => null,
        'cashier_id' => null,
        'packer_id' => null,
        'pos_source' => null,
    ];

    /**
     * Sales Report Provider Constructor
     *
     * @param CollectionFactory $collectionFactory
     * @param InvoiceRepositoryInterfaceFactory $invoiceCollectionFactory
     * @param CreditmemoRepositoryInterfaceFactory $creditmemoCollectionFactory
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param ModelFactory $modelFactory
     * @param FilterBuilder $filterBuilder
     * @param TimezoneInterface $timezone
     */
    public function __construct(
        CollectionFactory $collectionFactory,
        InvoiceRepositoryInterfaceFactory $invoiceCollectionFactory,
        CreditmemoRepositoryInterfaceFactory $creditmemoCollectionFactory,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        ModelFactory $modelFactory,
        FilterBuilder $filterBuilder,
        TimezoneInterface $timezone
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->invoiceCollectionFactory = $invoiceCollectionFactory;
        $this->creditmemoCollectionFactory = $creditmemoCollectionFactory;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->modelFactory = $modelFactory;
        $this->filterBuilder = $filterBuilder;
        $this->timezone = $timezone;
    }

    /**
     * Add filter by customer id
     *
     * @param int $customerId
     * @return void
     */
    public function addCustomerFilter(int $customerId): void
    {
        $this->orderFilters['customer_id'] = $customerId;
    }

    /**
     * Add filter by cashier
     *
     * @param int $cashierId
     * @return void
     */
    public function addCashierFilter(int $cashierId): void
    {
        $this->orderFilters['cashier_id'] = $cashierId;
    }

    /**
     * Add filter by packer
     *
     * @param int $packerId
     * @return void
     */
    public function addPackerFilter(int $packerId): void
    {
        $this->orderFilters['packer_id'] = $packerId;
    }

    /**
     * Add filter by source
     *
     * @param string $sourceId
     * @return void
     */
    public function addSourceFilter(string $sourceId): void
    {
        $this->orderFilters['pos_source'] = $sourceId;
    }

    /**
     * @throws LocalizedException
     */
    public function preparePaymentRefundOrders(DateTime $dateFrom = null, DateTime $dateTo = null): Collection
    {
        $collectOrders = [];
        /** @var Collection $collection */
        $collection = $this->collectionFactory->create();


        $invoiceCollection = $this->invoiceCollectionFactory->create();
        $refundCollection = $this->creditmemoCollectionFactory->create();

        $searchCriteriaBuilder = $this->prepareDateRangeSearchCriteria($dateFrom, $dateTo);
        $result = $invoiceCollection->getList($searchCriteriaBuilder);

        /** @var SalesReportInterface $item */
        foreach ($result->getItems() as $item) {
            if (!isset($collectOrders[$item->getOrder()->getId()])) {
                $collectOrders[$item->getOrder()->getId()] = true;
                $collection->addItem($item->getOrder());
            }
        }

        $result = $refundCollection->getList($searchCriteriaBuilder);
        /** @var SalesReportInterface $item */
        foreach ($result->getItems() as $item) {
            if (!isset($collectOrders[$item->getOrder()->getId()])) {
                $collectOrders[$item->getOrder()->getId()] = true;
                $collection->addItem($item->getOrder());
            }
        }

        return $collection;
    }

    /**
     * Preparation of data on orders for which payment has been made or funds have been refunded
     *
     * @throws Exception
     */
    public function preparePaymentRefundReportData(DateTime $dateFrom = null, DateTime $dateTo = null): Collection
    {
        $collectByPaymentCode = [];
        /** @var Collection $collection */
        $collection = $this->collectionFactory->create();
        $invoiceCollection = $this->prepareSalesReportData($dateFrom, $dateTo);
        $refundCollection = $this->prepareRefundReportData($dateFrom, $dateTo);

        /** @var SalesReportInterface $item */
        foreach ($invoiceCollection->getItems() as $item) {
            /** @var SalesReportInterface $reportItem */
            $reportItem = $this->modelFactory->create(SalesReportInterface::class);

            $reportItem->setId($item->getId());
            $reportItem->setTitle($item->getTitle());
            $reportItem->increaseDebit($item->getTotal());
            $reportItem->increaseTotal($item->getTotal());

            $collectByPaymentCode[$item->getId()] = $reportItem;
            $collection->addItem($reportItem);
        }

        /** @var SalesReportInterface $item */
        foreach ($refundCollection->getItems() as $item) {
            /** @var SalesReportInterface $reportItem */
            if (isset($collectByPaymentCode[$item->getId()])) {
                $reportItem = $collectByPaymentCode[$item->getId()];
            } else {
                $reportItem = $this->modelFactory->create(SalesReportInterface::class);
                $collection->addItem($reportItem);
            }

            $reportItem->setTitle($item->getTitle());
            $reportItem->increaseCredit($item->getTotal());
            // => increaseTotal Because $item->getTotal() returns a negative value
            $reportItem->increaseTotal($item->getTotal());
        }

        return $collection;
    }

    /**
     * Prepare sales report data
     *
     * @param DateTime|null $dateFrom
     * @param DateTime|null $dateTo
     * @return Collection
     * @throws LocalizedException
     */
    public function prepareSalesReportData(DateTime $dateFrom = null, DateTime $dateTo = null): Collection
    {
        $collectByPaymentTypes = [];
        $collection = $this->collectionFactory->create();
        $invoiceCollection = $this->invoiceCollectionFactory->create();

        $searchCriteriaBuilder = $this->prepareDateRangeSearchCriteria($dateFrom, $dateTo);

        $invoices = $invoiceCollection->getList($searchCriteriaBuilder);

        foreach ($invoices->getItems() as $invoice) {
            $order = $invoice->getOrder();

            if ($this->checkOrderFilters($order)) {
                continue;
            }

            $payment = $order->getPayment();
            $paymentCode = $payment->getMethodInstance()->getCode();
            $paymentTitle = $payment->getMethodInstance()->getTitle();

            /** @var SalesReportInterface $reportItem */
            if (isset($collectByPaymentTypes[$paymentCode])) {
                $reportItem = $collectByPaymentTypes[$paymentCode];
            } else {
                $reportItem = $this->modelFactory->create(SalesReportInterface::class);
                $reportItem->setId($paymentCode);
                $reportItem->setTitle($paymentTitle);
                $collectByPaymentTypes[$paymentCode] = $reportItem;
                $collection->addItem($reportItem);
            }

            $reportItem->increaseDebit($invoice->getGrandTotal());
            $reportItem->increaseTotal($invoice->getGrandTotal());
        }

        return $collection;
    }

    /**
     * Prepare refund report data
     *
     * @param DateTime|null $dateFrom
     * @param DateTime|null $dateTo
     * @return Collection
     * @throws LocalizedException
     */
    public function prepareRefundReportData(DateTime $dateFrom = null, DateTime $dateTo = null): Collection
    {
        $collectByPaymentTypes = [];
        $collection = $this->collectionFactory->create();
        $creditMemoCollection = $this->creditmemoCollectionFactory->create();

        $searchCriteriaBuilder = $this->prepareDateRangeSearchCriteria($dateFrom, $dateTo);

        $invoices = $creditMemoCollection->getList($searchCriteriaBuilder);

        foreach ($invoices->getItems() as $creditMemo) {
            $order = $creditMemo->getOrder();

            if ($this->checkOrderFilters($order)) {
                continue;
            }

            $payment = $order->getPayment();
            $paymentCode = $payment->getMethodInstance()->getCode();
            $paymentTitle = $payment->getMethodInstance()->getTitle();

            /** @var SalesReportInterface $reportItem */
            if (isset($collectByPaymentTypes[$paymentCode])) {
                $reportItem = $collectByPaymentTypes[$paymentCode];
            } else {
                $reportItem = $this->modelFactory->create(SalesReportInterface::class);
                $reportItem->setId($paymentCode);
                $reportItem->setTitle($paymentTitle);
                $collectByPaymentTypes[$paymentCode] = $reportItem;
                $collection->addItem($reportItem);
            }

            $reportItem->increaseCredit($creditMemo->getGrandTotal());
            $reportItem->decreaseTotal($creditMemo->getGrandTotal());
        }

        return $collection;
    }


    /**
     * Check all filters by order
     *
     * @param OrderInterface $order
     * @return bool
     */
    private function checkOrderFilters(OrderInterface $order): bool
    {
        foreach ($this->orderFilters as $property => $value) {
            if ($value !== null) {
                if ($order->getData($property) == $value) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Prepare search criteria for date range
     *
     * @param DateTime|null $dateFrom
     * @param DateTime|null $dateTo
     * @return SearchCriteria
     * @throws DateMalformedStringException
     */
    private function prepareDateRangeSearchCriteria(DateTime $dateFrom = null, DateTime $dateTo = null): SearchCriteria
    {
        if (!$dateTo) {
            $dateTo = $this->timezone->date();
        }

        if (!$dateFrom) {
            $dateFrom = clone $dateTo;
            $dateFrom->modify('-1 year');
        }

        $this->searchCriteriaBuilder
            ->addFilter('created_at', $dateFrom->format('Y-m-d H:i:s'), 'gteq')
            ->addFilter('created_at', $dateTo->format('Y-m-d H:i:s'), 'lteq');

        return $this->searchCriteriaBuilder->create();
    }
}
