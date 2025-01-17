<?php

namespace SapientPro\Core\Service\Pos;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\Exception\LocalizedException;

class CustomerPosAllowChecker
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
     * CustomerPosAllowChecker constructor.
     *
     * @param CustomerSession $customerSession
     * @param CustomerRepositoryInterface $customerRepository
     */
    public function __construct(
        CustomerSession                      $customerSession,
        CustomerRepositoryInterface $customerRepository
    ) {
        $this->customerRepository = $customerRepository;
        $this->customerSession = $customerSession;
    }

    /**
     * Check if customer is allowed to use POS terminal
     *
     * @return bool
     */
    public function isAllowed(): bool
    {
        if (!$this->customerSession->isLoggedIn()) {
            return false;
        }

        try {
            $customerData = $this->customerRepository->getById($this->customerSession->getCustomerId());
            $allowPosTerminal = $customerData->getCustomAttribute('allow_pos_terminal');

            return $allowPosTerminal && (int)$allowPosTerminal->getValue();
        } catch (LocalizedException $exception) {
            return false;
        }
    }

    /**
     * @return int
     */
    public function getCustomerId(): int
    {
        return $this->customerSession->getCustomerId();
    }
}
