<?php

namespace SapientPro\Core\Service;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Payment\Model\Config as PaymentConfig;

/**
 * Class PaymentMethodProvider
 */
class PaymentMethodProvider
{
    /**
     * @var PaymentConfig
     */
    private PaymentConfig $paymentConfig;

    /**
     * @var ScopeConfigInterface
     */
    private ScopeConfigInterface $scopeConfig;

    /**
     * Constructor PaymentMethodProvider
     *
     * @param PaymentConfig $paymentConfig
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        PaymentConfig $paymentConfig,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->paymentConfig = $paymentConfig;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Get payment method sort order
     *
     * @param string $code
     * @return int
     */
    public function getPaymentMethodSortOrder(string $code): int
    {
        $paymentMethods = $this->paymentConfig->getActiveMethods();
        $paymentMethod = $paymentMethods[$code] ?? null;

        if ($paymentMethod) {
            return (int)$this->scopeConfig->getValue(
                'payment/' . $code . '/sort_order',
                ScopeInterface::SCOPE_STORE
            );
        }

        return 0;
    }

    /**
     * Is payment method allowed in POS
     *
     * @param string $code
     * @return bool
     */
    public function isPaymentMethodAllowedInPos(string $code): bool
    {
        $paymentMethods = $this->paymentConfig->getActiveMethods();
        $paymentMethod = $paymentMethods[$code] ?? null;

        if ($paymentMethod) {
            return $this->scopeConfig->isSetFlag(
                'payment/' . $code . '/is_allowed_in_pos',
                ScopeInterface::SCOPE_STORE
            );
        }

        return false;
    }

    /**
     * Is payment method included in report
     *
     * @param string $code
     * @return bool
     */
    public function isPaymentMethodIncludedInReport(string $code): bool
    {
        $paymentMethods = $this->paymentConfig->getActiveMethods();
        $paymentMethod = $paymentMethods[$code] ?? null;

        if ($paymentMethod) {
            return $this->scopeConfig->isSetFlag(
                'payment/' . $code . '/is_included_in_report',
                ScopeInterface::SCOPE_STORE
            );
        }

        return false;
    }

    /**
     * @return array
     */
    public function getActiveMethods(): array
    {
        return $this->paymentConfig->getActiveMethods();
    }
}
