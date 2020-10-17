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
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var ArrayManager
     */
    private $arrayManager;

    /**
     * CheckoutConfigProvider constructor.
     * @param ScopeConfigInterface $scopeConfig
     * @param ArrayManager $arrayManager
     */
    public function __construct(ScopeConfigInterface $scopeConfig, ArrayManager $arrayManager)
    {
        $this->scopeConfig = $scopeConfig;
        $this->arrayManager = $arrayManager;
    }

    /**
     * @param array $jsLayout
     * @return array
     */
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
