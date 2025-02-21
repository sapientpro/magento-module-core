<?php

namespace SapientPro\Core\Block\Report;

use Magento\Framework\View\Element\Template;
use Magento\Store\Model\ScopeInterface;

class Footer extends Template
{
    /**
     * Get address from configuration
     *
     * @return string
     */
    public function getAddress()
    {
        return $this->_scopeConfig->getValue('general/store_information/street_line1', ScopeInterface::SCOPE_STORE) .
            ' ' . $this->_scopeConfig->getValue('general/store_information/street_line2', ScopeInterface::SCOPE_STORE);
    }

    /**
     * Get phone from configuration
     *
     * @return string
     */
    public function getPhone()
    {
        return $this->_scopeConfig->getValue('general/store_information/phone', ScopeInterface::SCOPE_STORE);
    }

    /**
     * Get email from configuration
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->_scopeConfig->getValue('trans_email/ident_general/email', ScopeInterface::SCOPE_STORE);
    }
}
