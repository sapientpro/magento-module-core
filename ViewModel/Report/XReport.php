<?php

declare(strict_types=1);

namespace SapientPro\Core\ViewModel\Report;

use Magento\Framework\Api\SearchCriteriaBuilder;
use SapientPro\Core\Api\ViewModel\ReportInterface;
use Magento\Payment\Model\Config as PaymentConfig;
use SapientPro\Core\Api\Report\FundsInflowReportGeneratorsInterfaceFactory;
use Magento\Framework\Data\Collection as CustomerReportCollection;
use Magento\Framework\Data\CollectionFactory;
use DateTime;
use SapientPro\Core\Api\Report\Data\CashiersReportInterface;
use SapientPro\Core\Model\Report\ReportStatus;

class XReport implements ReportInterface
{
    /**
     * @var PaymentConfig
     */
    private PaymentConfig $paymentConfig;

    /**
     * @var FundsInflowReportGeneratorsInterfaceFactory
     */
    private FundsInflowReportGeneratorsInterfaceFactory $fundsInflowReportGeneratorsFactory;

    /**
     * @var SearchCriteriaBuilder
     */
    private SearchCriteriaBuilder $searchCriteriaBuilder;

    /**
     * @var bool
     */
    private bool $isSupervisor = false;

    /**
     * @var int
     */
    private int $filterCashierId;

    /**
     * @var int
     */
    private int $filterPackerId;

    /**
     * @var DateTime
     */
    private DateTime $filterDateFrom;

    /**
     * @var DateTime
     */
    private DateTime $filterDateTo;

    /**
     * @var string
     */
    private string $filterSourceId;

    /**
     * @var Collection
     */
    private Collection $cashiersInReport;
    /**
     * @var CustomerReportCollection
     */
    private CustomerReportCollection $customerReportCollection;
    /**
     * @var CashiersReportInterface
     */
    private CashiersReportInterface $cashiersReport;

    /**
     * @var int
     */
    private int $requestedUserId;

    /**
     * PaymentViewModal constructor.
     *
     * @param PaymentConfig $paymentConfig
     * @param FundsInflowReportGeneratorsInterfaceFactory $fundsInflowReportGeneratorsFactory
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        PaymentConfig                               $paymentConfig,
        FundsInflowReportGeneratorsInterfaceFactory $fundsInflowReportGeneratorsFactory,
        SearchCriteriaBuilder                       $searchCriteriaBuilder,
        CustomerReportCollection                    $customerReportCollection,
        CollectionFactory                           $collectionFactory,
        CashiersReportInterface                     $cashiersReport
    )
    {
        $this->paymentConfig = $paymentConfig;
        $this->fundsInflowReportGeneratorsFactory = $fundsInflowReportGeneratorsFactory;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->customerReportCollection = $customerReportCollection;
        $this->cashiersReport = $cashiersReport;
    }

    /**
     * @inheirtdoc
     */
    public function isSupervisor(?bool $isSupervisor = null): bool
    {
        if ($isSupervisor) {
            $this->isSupervisor = $isSupervisor;
        }

        return $this->isSupervisor;
    }

    /**
     * @inheirtdoc
     */
    public function addDateFromFilter(DateTime $dateTime)
    {
        $this->filterDateFrom = $dateTime;
    }

    /**
     * @inheirtdoc
     */
    public function addDateToFilter(DateTime $dateTime)
    {
        $this->filterDateTo = $dateTime;
    }

    /**
     * @inheirtdoc
     */
    public function addSourceFilter(string $sourceId)
    {
        $this->filterSourceId = $sourceId;
    }

    /**
     * @inheirtdoc
     */
    public function addCashierFilter(int $cashierId)
    {
        $this->filterCashierId = $cashierId;
    }

    /**
     * @inheirtdoc
     */
    public function addPackerFilter(int $packerId)
    {
        $this->filterPackerId = $packerId;
    }

    /**
     * @return CustomerReportCollection
     */
    public function getFullReportDataBySource(): CustomerReportCollection
    {
        $reportGenerator = $this->fundsInflowReportGeneratorsFactory->create();
        $reportGenerator->addSourceFilter($this->filterSourceId);

        return $reportGenerator->execute($this->filterDateFrom, $this->filterDateTo, ReportStatus::X_REPORT);
    }

    /**
     * @param int $cashierId
     * @return CustomerReportCollection
     */
    public function getFullReportDataByCashier(int $cashierId): CustomerReportCollection
    {
        $reportGenerator = $this->fundsInflowReportGeneratorsFactory->create();
        $reportGenerator->addSourceFilter($this->filterSourceId);
        $reportGenerator->addCashierFilter($cashierId);

        return $reportGenerator->execute($this->filterDateFrom, $this->filterDateTo, ReportStatus::X_REPORT);
    }


    /**
     * @return array
     */
    public function getCashiersInReport()
    {
        return $this->cashiersReport->getCashiers();
    }

    /**
     * @param int|null $userId
     * @return int
     */
    public function requestedUserReport(?int $userId = null)
    {
        if ($userId) {
            $this->requestedUserId = $userId;
        }

        return $this->requestedUserId;
    }
}
