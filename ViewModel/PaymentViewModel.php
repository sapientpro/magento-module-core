<?php

declare(strict_types=1);

namespace SapientPro\Core\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Payment\Model\Config as PaymentConfig;
use SapientPro\PointOfSale\Model\Payment\PosCashPayment;

class PaymentViewModel implements ArgumentInterface
{
    /**
     * @var PaymentConfig
     */
    private PaymentConfig $paymentConfig;

    /**
     * PaymentViewModal constructor.
     *
     * @param PaymentConfig $paymentConfig
     */
    public function __construct(
        PaymentConfig $paymentConfig
    ) {
        $this->paymentConfig = $paymentConfig;
    }

    /**
     * Get POS payment methods
     *
     * @return array
     */
    public function getPosPaymentMethods()
    {
        $methods = [];
        $cashPaymentMethod = null;

        $paymentMethods = $this->paymentConfig->getActiveMethods();
        foreach ($paymentMethods as $code => $data) {
            $carrierConfig = $data->getConfigData('is_allow_in_pos');
            if ($carrierConfig) {
                if ($code === PosCashPayment::PAYMENT_METHOD_CASH_CODE) {
                    $cashPaymentMethod = [$code => $data->getConfigData('title')];
                } else {
                    $methods[$code] = $data->getConfigData('title');
                }
            }
        }

        if ($cashPaymentMethod) {
            $methods = $cashPaymentMethod + $methods;
        }

        return $methods;
    }
}
