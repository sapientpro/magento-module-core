<?php

namespace SapientPro\Core\Service\Report;

use Magento\Framework\Data\Collection;
use Magento\Framework\Data\CollectionFactory;
use Magento\Framework\Data\Collection\ModelFactory;
use Magento\Framework\Exception\LocalizedException;
use SapientPro\Core\Api\Report\Data\SalesReportInterface;
use SapientPro\Core\Api\Report\SalesReportProviderInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\FilterBuilder;
use Magento\Sales\Api\InvoiceRepositoryInterfaceFactory;
use Magento\Sales\Api\CreditmemoRepositoryInterfaceFactory;
use Magento\Framework\Api\SearchCriteria;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use DateMalformedStringException;
use DateTime;
use Exception;

class SalesReportProvider implements SalesReportProviderInterface
{
    private CollectionFactory $collectionFactory;

    protected InvoiceRepositoryInterfaceFactory $invoiceCollectionFactory;

    protected CreditmemoRepositoryInterfaceFactory $creditmemoCollectionFactory;

    private SearchCriteriaBuilder $searchCriteriaBuilder;

    private FilterBuilder $filterBuilder;

    private ModelFactory $modelFactory;

    private TimezoneInterface $timezone;

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
            $dateTo->modify('-1 year');
        }

        if ($dateFrom) {
            return $this->searchCriteriaBuilder
                ->addFilter('created_at', $dateFrom->format('Y-m-d H:i:s'), 'gt')
                ->addFilter('created_at', $dateTo->format('Y-m-d H:i:s'), 'lt')
                ->create();
        }

        return $this->searchCriteriaBuilder
            ->addFilter('created_at', $dateTo->format('Y-m-d H:i:s'), 'gt')
            ->create();
    }
}
