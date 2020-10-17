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

/**
 * Product breadcrumbs view model.
 */
class CurrentCustomer extends DataObject implements ArgumentInterface
{
    /**
     * @var CurrentCustomerHelper
     */
    private $currentCustomer;

    /**
     * @var CustomerViewHelper
     */
    private $helperView;

    /**
     * @param CurrentCustomerHelper $currentCustomer
     * @param CustomerViewHelper $helperView
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __construct(
        CurrentCustomerHelper $currentCustomer,
        CustomerViewHelper $helperView
    ) {
        $this->currentCustomer = $currentCustomer;
        $this->helperView = $helperView;
    }

    /**
     * Returns the Magento Customer Model for this block
     *
     * @return \Magento\Customer\Api\Data\CustomerInterface|null
     */
    public function getCustomer()
    {
        return $this->currentCustomer->getCustomer();
    }

    /**
     * Get the full name of a customer
     *
     * @return string full name
     */
    public function getName()
    {
        return $this->helperView->getCustomerName($this->getCustomer());
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->getCustomer()->getEmail();
    }

    /**
     * @return string
     */
    public function getChangePasswordUrl()
    {
        return $this->urlBuilder->getUrl('customer/account/edit/changepass/1');
    }

}
