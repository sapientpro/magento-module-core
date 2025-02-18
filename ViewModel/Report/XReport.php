<?php

declare(strict_types=1);

namespace SapientPro\Core\ViewModel\Report;

use SapientPro\Core\Api\ViewModel\ReportInterface;
use Magento\Payment\Model\Config as PaymentConfig;
use SapientPro\Core\Api\Report\FundsInflowReportGeneratorsInterface;
use SapientPro\PointOfSale\Model\Payment\PosCashPayment;

class XReport implements ReportInterface
{
    /**
     * @var PaymentConfig
     */
    private PaymentConfig $paymentConfig;

    /**
     * @var FundsInflowReportGeneratorsInterface
     */
    private FundsInflowReportGeneratorsInterface $fundsInflowReportGenerators;

    /**
     * PaymentViewModal constructor.
     *
     * @param PaymentConfig $paymentConfig
     * @param FundsInflowReportGeneratorsInterface $fundsInflowReportGenerators
     */
    public function __construct(
        PaymentConfig $paymentConfig,
        FundsInflowReportGeneratorsInterface $fundsInflowReportGenerators
    ) {
        $this->paymentConfig = $paymentConfig;
        $this->fundsInflowReportGenerators = $fundsInflowReportGenerators;
    }

    public function addDateFromFilter()
    {
        // TODO: Implement setDateFromFilter() method.
    }

    public function addDateToFilter()
    {
        // TODO: Implement setDateToFilter() method.
    }

    public function addSourceFilter(string $sourceId)
    {
        $this->fundsInflowReportGenerators->addSourceFilter($sourceId);
    }

    public function addCashierFilter()
    {
        // TODO: Implement setCashierFilter() method.
    }

    public function addPackerFilter()
    {
        // TODO: Implement setPackerFilter() method.
    }
}
