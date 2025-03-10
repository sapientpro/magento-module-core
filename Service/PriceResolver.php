<?php

declare(strict_types=1);

namespace SapientPro\Core\Service;

use Magento\Catalog\Model\Product;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\GroupRepositoryInterface;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Pricing\Helper\Data as PricingHelper;

class PriceResolver
{
    /**
     * @var CustomerSession
     */
    private CustomerSession $customerSession;

    /**
     * @var GroupRepositoryInterface
     */
    private GroupRepositoryInterface $groupRepository;

    /**
     * @var CustomerRepositoryInterface
     */
    private CustomerRepositoryInterface $customerRepository;

    /**
     * @var PricingHelper
     */
    private PricingHelper $pricingHelper;

    /**
     * PriceResolver constructor.
     *
     * @param CustomerSession $customerSession
     * @param GroupRepositoryInterface $groupRepository
     * @param CustomerRepositoryInterface $customerRepository
     * @param PricingHelper $pricingHelper
     */
    public function __construct(
        CustomerSession             $customerSession,
        GroupRepositoryInterface    $groupRepository,
        CustomerRepositoryInterface $customerRepository,
        PricingHelper $pricingHelper
    ) {
        $this->pricingHelper = $pricingHelper;
        $this->customerRepository = $customerRepository;
        $this->groupRepository = $groupRepository;
        $this->customerSession = $customerSession;
    }

    /**
     * Resolve price for product
     *
     * @param Product $product
     * @return string
     */
    public function resolve(Product $product): string
    {
        $price = $this->getPrice($product);
        return $this->pricingHelper->currency($price, true, false);
    }

    /**
     * Resolve price for product without currency
     *
     * @param Product $product
     * @return string
     */
    public function getPrice(Product $product): string
    {
        $buyerId = $this->customerSession->getBuyerId();
        $price = $product->getSpecialPrice() ?: $product->getPrice();

        if (!$buyerId) {
            return (string)$price;
        }

        try {
            $customer = $this->customerRepository->getById($buyerId);
            $group = $this->groupRepository->getById($customer->getGroupId());

            foreach ($product->getTierPrices() as $tierPrice) {
                if ($tierPrice->getCustomerGroupId() == $group->getId()) {
                    return (string)$tierPrice->getValue();
                }
            }

            return (string)$price;
        } catch (NoSuchEntityException|LocalizedException $exception) {
            return (string)$price;
        }
    }
}
