<?php

namespace SapientPro\Core\Service\Report\ReportProvider;

use SapientPro\Core\Api\Report\ReportProvider\ReportProviderInterface;
use Magento\Framework\Data\Collection;
use Magento\Framework\Data\CollectionFactory;
use Magento\Framework\Data\Collection\ModelFactory;
use SapientPro\Core\Api\Report\Data\SalesReportInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\FilterBuilder;
use Magento\Sales\Api\InvoiceRepositoryInterfaceFactory;
use Magento\Sales\Api\CreditmemoRepositoryInterfaceFactory;
use Magento\Framework\Api\SearchCriteria;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use DateMalformedStringException;
use DateTime;

class PaymentRefundProvider extends PaymentReportProviderAbstract implements ReportProviderInterface
{
    private CollectionFactory $collectionFactory;

    protected InvoiceRepositoryInterfaceFactory $invoiceCollectionFactory;

    protected CreditmemoRepositoryInterfaceFactory $creditmemoCollectionFactory;

    private SearchCriteriaBuilder $searchCriteriaBuilder;

    private FilterBuilder $filterBuilder;

    private ModelFactory $modelFactory;

    private TimezoneInterface $timezone;

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

    public function execute(DateTime $dateFrom = null, DateTime $dateTo = null): Collection
    {
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
            if (!$collection->getItemById($paymentCode)) {
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
