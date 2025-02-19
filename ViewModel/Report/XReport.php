<?php

declare(strict_types=1);

namespace SapientPro\Core\ViewModel\Report;

use Magento\Framework\Api\SearchCriteriaBuilder;
use SapientPro\Core\Api\ViewModel\ReportInterface;
use Magento\Payment\Model\Config as PaymentConfig;
use SapientPro\Core\Api\Report\FundsInflowReportGeneratorsInterfaceFactory;
use Magento\Framework\Data\Collection;
use Magento\Framework\Data\CollectionFactory;
use DateTime;

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
    private bool $isSupervisor= false;

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
     * PaymentViewModal constructor.
     *
     * @param PaymentConfig $paymentConfig
     * @param FundsInflowReportGeneratorsInterfaceFactory $fundsInflowReportGeneratorsFactory
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        PaymentConfig $paymentConfig,
        FundsInflowReportGeneratorsInterfaceFactory $fundsInflowReportGeneratorsFactory,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        CollectionFactory $collectionFactory
    ) {
        $this->paymentConfig = $paymentConfig;
        $this->fundsInflowReportGeneratorsFactory = $fundsInflowReportGeneratorsFactory;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->cashiersInReport = $collectionFactory->create();
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

    public function getFullReportDataBySource(): Collection
    {
        $reportGenerator = $this->fundsInflowReportGeneratorsFactory->create();
        $reportGenerator->addSourceFilter($this->filterSourceId);

        return $reportGenerator->execute($this->filterDateFrom, $this->filterDateTo);
    }

    /**
     * @return Collection
     */
    public function getCashiersInReport(): Collection
    {
        return $this->cashiersInReport;
    }
}
