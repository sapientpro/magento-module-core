<?php
/**
 * SapientPro
 *
 * @category    SapientPro
 * @package     SapientPro_Core
 * @author      SapientPro Team <info@sapient.pro >
 * @copyright   Copyright © 2009-2019 SapientPro (https://sapient.pro)
 */

namespace SapientPro\Core\ViewModel\Customer\Account;

use Magento\Framework\DataObject;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Customer\Helper\Session\CurrentCustomer as CurrentCustomerHelper;
use Magento\Customer\Helper\View as CustomerViewHelper;
use Magento\Customer\Api\Data\CustomerInterface;

class CurrentCustomer extends DataObject implements ArgumentInterface
{
    public function __construct(
        private readonly CurrentCustomerHelper $currentCustomer,
        private readonly CustomerViewHelper $helperView
    ) {
    }

    public function getCustomer(): CustomerInterface
    {
        return $this->currentCustomer->getCustomer();
    }

    public function getName(): array|string
    {
        return $this->helperView->getCustomerName($this->getCustomer());
    }

    public function getEmail(): string
    {
        return $this->getCustomer()->getEmail();
    }
}
