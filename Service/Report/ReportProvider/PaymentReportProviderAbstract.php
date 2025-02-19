<?php

namespace SapientPro\Core\Service\Report\ReportProvider;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Data\Collection;
use Magento\Framework\Data\CollectionFactory;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\InvoiceRepositoryInterfaceFactory;
use Magento\Sales\Api\CreditmemoRepositoryInterfaceFactory;

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
     * @var Collection
     */
    protected Collection $cashiersCollection;

    /**
     * @var Collection
     */
    protected Collection $packersCollection;

    /**
     * @var Collection
     */
    protected Collection $sourcesCollection;

    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerRepository;

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
     * @return Collection
     */
    public function getCashiers(): Collection
    {
        return $this->cashiersCollection;
    }

    /**
     * @return Collection
     */
    public function getPackers(): Collection
    {
        return $this->packersCollection;
    }

    /**
     * @return Collection
     */
    public function getSources(): Collection
    {
        return $this->sourcesCollection;
    }

    /**
     * Check all filters by order
     *
     * @param OrderInterface $order
     * @return bool
     */
    protected function checkOrderFilters(OrderInterface $order): bool
    {
        $filterStatuses = [];
        foreach ($this->orderFilters as $property => $value) {
            if ($value !== null) {
                $filterStatuses[$property] = ($order->getData($property) == $value);
            }
        }

        foreach ($filterStatuses as $property => $value) {
            if ($value) {
                $filterStatuses[$property] = ($order->getData($property) == $value);
            }
        }

        if (count($filterStatuses) === 0) {
            return true;
        }

        return $this->allTrue($filterStatuses);
    }

    /**
     * Check all filters by order
     *
     * @param array $arr
     * @return bool
     */
    protected function allTrue(array $arr): bool {
        return array_reduce($arr, function($carry, $item) {
            return $carry && $item;
        }, true);
    }

}
