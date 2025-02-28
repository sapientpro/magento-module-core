<?php

namespace SapientPro\Core\Service\Report\ReportProvider;

use Magento\Framework\DataObject;
use SapientPro\Core\Api\Report\ReportProvider\ReportProviderInterface;
use Magento\Framework\Data\Collection;
use Magento\Framework\Data\CollectionFactory;
use Magento\Framework\Data\Collection\ModelFactory;
use Magento\Framework\Exception\LocalizedException;
use SapientPro\Core\Api\Report\Data\SalesReportInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\FilterBuilder;
use Magento\Sales\Api\InvoiceRepositoryInterfaceFactory;
use Magento\Sales\Api\CreditmemoRepositoryInterfaceFactory;
use Magento\Framework\Api\SearchCriteria;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use DateMalformedStringException;
use DateTime;
use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory as CustomerCollectionFactory;
use SapientPro\Core\Api\Report\Data\CashiersReportInterface;
use SapientPro\Core\Api\Report\Data\PackersReportInterface;

class PaymentInvoiceReportProvider extends PaymentReportProviderAbstract implements ReportProviderInterface
{
    /**
     * @var array
     */
    private array $customerCache = [];

    /**
     * @var CollectionFactory
     */
    private CollectionFactory $collectionFactory;

    /**
     * @var InvoiceRepositoryInterfaceFactory
     */
    protected InvoiceRepositoryInterfaceFactory $invoiceCollectionFactory;

    /**
     * @var CreditmemoRepositoryInterfaceFactory
     */
    protected CreditmemoRepositoryInterfaceFactory $creditmemoCollectionFactory;

    /**
     * @var SearchCriteriaBuilder
     */
    private SearchCriteriaBuilder $searchCriteriaBuilder;

    /**
     * @var FilterBuilder
     */
    private FilterBuilder $filterBuilder;

    /**
     * @var ModelFactory
     */
    private ModelFactory $modelFactory;

    /**
     * @var TimezoneInterface
     */
    private TimezoneInterface $timezone;

    /**
     * @var CustomerCollectionFactory
     */
    private CustomerCollectionFactory $customerCollectionFactory;
    /**
     * @var CashiersReportInterface
     */
    private CashiersReportInterface $cashiersReport;
    /**
     * @var PackersReportInterface
     */
    private PackersReportInterface $packersReport;

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
     * @param CustomerCollectionFactory $customerCollectionFactory
     * @param CashiersReportInterface $cashiersReport
     * @param PackersReportInterface $packersReport
     */
    public function __construct(
        CollectionFactory $collectionFactory,
        InvoiceRepositoryInterfaceFactory $invoiceCollectionFactory,
        CreditmemoRepositoryInterfaceFactory $creditmemoCollectionFactory,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        ModelFactory $modelFactory,
        FilterBuilder $filterBuilder,
        TimezoneInterface $timezone,
        CustomerCollectionFactory $customerCollectionFactory,
        CashiersReportInterface $cashiersReport,
        PackersReportInterface $packersReport
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->invoiceCollectionFactory = $invoiceCollectionFactory;
        $this->creditmemoCollectionFactory = $creditmemoCollectionFactory;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->modelFactory = $modelFactory;
        $this->filterBuilder = $filterBuilder;
        $this->timezone = $timezone;
        $this->cashiersCollection = $this->collectionFactory->create();
        $this->packersCollection = $this->collectionFactory->create();
        $this->sourcesCollection = $this->collectionFactory->create();
        $this->customerCollectionFactory = $customerCollectionFactory;
        $this->cashiersReport = $cashiersReport;
        $this->packersReport = $packersReport;
    }

    /**
     * @throws DateMalformedStringException
     * @throws LocalizedException
     */
    public function execute(DateTime $dateFrom = null, DateTime $dateTo = null): Collection
    {
        $collection = $this->collectionFactory->create();
        $invoiceCollection = $this->invoiceCollectionFactory->create();

        $searchCriteriaBuilder = $this->prepareDateRangeSearchCriteria($dateFrom, $dateTo);

        $invoices = $invoiceCollection->getList($searchCriteriaBuilder);

        foreach ($invoices->getItems() as $invoice) {
            $order = $invoice->getOrder();

            if (!$this->checkOrderFilters($order)) {
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
            } else {
                $reportItem = $collection->getItemById($paymentCode);
            }

            $cashier = $this->getCustomerById($order->getCashierId());
            $packer = $this->getCustomerById($order->getPackerId());

            if ($cashier->getId()) {
                $this->cashiersReport->addCashier($cashier);
            }

            if ($packer->getId()) {
                $this->packersReport->addPacker($packer);
            }

            $reportItem->increaseDebit($invoice->getGrandTotal());
            $reportItem->increaseTotal($order->getGrandTotal());
            $reportItem->increaseDiscount($order->getDiscountAmount());
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

    /**
     * Get customer by id
     *
     * @param int $customerId
     * @return DataObject
     * @throws LocalizedException
     */
    protected function getCustomerById(int $customerId): DataObject
    {
        if (!isset($this->customerCache[$customerId])) {
            $customerCollection = $this->customerCollectionFactory->create();
            $customerCollection->addAttributeToSelect('*');
            $customerCollection->addAttributeToFilter('entity_id', $customerId);

            $this->customerCache[$customerId] = $customerCollection->getFirstItem();
        }

        return $this->customerCache[$customerId];
    }
}
