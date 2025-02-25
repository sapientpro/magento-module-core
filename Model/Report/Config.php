<?php

namespace SapientPro\Core\Model\Report;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class Config
{
    /**
     * @var ScopeConfigInterface
     */
    private ScopeConfigInterface $scopeConfig;

    /**
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @param string $reportType
     * @return array
     */
    public function getExcludedPayments(string $reportType): array
    {
        $configPath = "pos/report_excluded_payments/{$reportType}";
        $payments = $this->scopeConfig->getValue($configPath, ScopeInterface::SCOPE_STORE);

        return $payments ? explode(',', $payments) : [];
    }
}
