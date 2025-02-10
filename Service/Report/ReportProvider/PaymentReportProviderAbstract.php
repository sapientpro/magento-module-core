<?php

namespace SapientPro\Core\Service\Report\ReportProvider;

use SapientPro\Core\Api\Report\ReportProvider\ReportProviderInterface;
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

abstract class PaymentReportProviderAbstract
{
    /**
     * @var array
     */
    protected array $orderFilters = [
        'customer_id' => null,
        'cashier_id' => null,
        'packer_id' => null,
        'pos_source' => null,
    ];

    /**
     * Set filters
     *
     * @param array $filters
     * @return void
     */
    public function setFilters(array $filters): void
    {
        foreach ($filters as $filterName => $filterValue) {
            if (array_key_exists($filterName, $this->orderFilters)) {
                $this->orderFilters[$filterName] = $filterValue;
            }
        }
    }

    /**
     * Check all filters by order
     *
     * @param OrderInterface $order
     * @return bool
     */
    protected function checkOrderFilters(OrderInterface $order): bool
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
}
