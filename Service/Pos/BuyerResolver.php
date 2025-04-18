<?php

declare(strict_types=1);

namespace SapientPro\Core\Service\Pos;


use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

class BuyerResolver
{
    /**
     * @var CustomerSession
     */
    private CustomerSession $customerSession;

    /**
     * @var CustomerRepositoryInterface
     */
    private CustomerRepositoryInterface $customerRepository;


    /**
     * Constructor for the BuyerResolver class.
     *
     * @param CustomerSession $customerSession
     * @param CustomerRepositoryInterface $customerRepository
     */
    public function __construct(
        CustomerSession                        $customerSession,
        CustomerRepositoryInterface            $customerRepository
    ) {
        $this->customerRepository = $customerRepository;
        $this->customerSession = $customerSession;
    }

    /**
     * Get the current buyer from the session.
     *
     * @return CustomerInterface|null
     */
    public function getCurrentBuyer(): ?CustomerInterface
    {
        $buyer = $this->customerSession->getBuyerId();
        $cashierId = $this->customerSession->getCashierId();

        if ($buyer === $cashierId) {
            return null;
        }

        try {
            return $this->customerRepository->getById($buyer);
        } catch (NoSuchEntityException|LocalizedException $e) {
            return null;
        }
    }
}
