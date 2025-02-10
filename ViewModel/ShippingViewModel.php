<?php

declare(strict_types=1);

namespace SapientPro\Core\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Shipping\Model\Config as ShippingConfig;
use SapientPro\PointOfSale\Model\Carrier\PosShipping;

class ShippingViewModel implements ArgumentInterface
{
    /**
     * @var ShippingConfig
     */
    private ShippingConfig $shippingConfig;

    /**
     * ShippingViewModal constructor.
     *
     * @param ShippingConfig $shippingConfig
     */
    public function __construct(
        ShippingConfig $shippingConfig
    ) {
        $this->shippingConfig = $shippingConfig;
    }

    /**
     * Get POS shipping methods
     *
     * @return array
     */
    public function getPosShippingMethods()
    {
        $methods = [];
        $pickupMethod = null;
        foreach ($this->shippingConfig->getActiveCarriers() as $code => $carrier) {
            $carrierConfig = $carrier->getConfigData('is_allow_in_pos');
            if ($carrierConfig) {
                if ($code === PosShipping::CODE) {
                    $pickupMethod = [$code => $carrier->getConfigData('title')];
                } else {
                    $methods[$code] = $carrier->getConfigData('title');
                }
            }
        }

        if ($pickupMethod) {
            $methods = $pickupMethod + $methods;
        }

        return $methods;
    }
}
