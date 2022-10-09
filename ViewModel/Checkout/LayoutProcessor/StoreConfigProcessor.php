<?php
/**
 * SapientPro
 *
 * @category    SapientPro
 * @package     SapientPro_Core
 * @author      SapientPro Team <info@sapient.pro >
 * @copyright   Copyright © 2009-2019 SapientPro (https://sapient.pro)
 */

namespace SapientPro\Core\ViewModel\Checkout\LayoutProcessor;

use Magento\Checkout\Block\Checkout\LayoutProcessorInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Stdlib\ArrayManager;

class StoreConfigProcessor implements LayoutProcessorInterface
{
    public function __construct(
        private readonly ScopeConfigInterface $scopeConfig,
        private readonly ArrayManager $arrayManager
    ) {
    }

    public function process($jsLayout)
    {
        foreach ($this->arrayManager->findPaths('storeConfigData', $jsLayout) as $arrayPath) {
            foreach ($this->arrayManager->get($arrayPath, $jsLayout) as $path => $storeConfig)  {
                $jsLayout = $this->arrayManager->replace(
                    $arrayPath . '/'. $path,
                    $jsLayout,
                    $this->scopeConfig->getValue($storeConfig));
            }
        }

        return $jsLayout;
    }
}
