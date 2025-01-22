<?php
/**
 * SapientPro
 *
 * @category    SapientPro
 * @package     SapientPro_Core
 * @author      SapientPro Team <info@sapient.pro >
 * @copyright   Copyright © 2009-2022 SapientPro (https://sapient.pro)
 */
namespace SapientPro\Core\Controller\Adminhtml\Index;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Backend\Model\View\Result\Page;

class Index extends \Magento\Marketplace\Controller\Adminhtml\Index
{
    /**
     * @var PageFactory
     */
    private PageFactory $resultPageFactory;

    public function __construct(
        Context $context,
        PageFactory $resultPageFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    /**
     * Action for Partners page
     *
     * @return Page
     */
    public function execute(): Page
    {
        /** @var Page $resultPage */
        $resultPage = $this->getResultPageFactory()->create();
        $resultPage->setActiveMenu('Magento_Marketplace::partners');
        $resultPage->addBreadcrumb(__('Partners'), __('Partners'));
        $resultPage->getConfig()->getTitle()->prepend(__('About Us'));

        return $resultPage;
    }

    public function getResultPageFactory(): PageFactory
    {
        return $this->resultPageFactory;
    }
}
