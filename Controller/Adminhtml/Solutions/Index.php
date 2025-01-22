<?php
/**
 * SapientPro
 *
 * @category    SapientPro
 * @package     SapientPro_Core
 * @author      SapientPro Team <info@sapient.pro >
 * @copyright   Copyright © 2009-2019 SapientPro (https://sapient.pro)
 */
namespace SapientPro\Core\Controller\Adminhtml\Solutions;

use Magento\Framework\App\Action\HttpGetActionInterface as HttpGetActionInterface;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Backend\Model\View\Result\Page;
use Magento\Marketplace\Controller\Adminhtml\Index as ControllerIndex;

class Index extends ControllerIndex
{
    /**
     * @var PageFactory
     */
    private PageFactory $resultPageFactory;

    /**
     * Constructor Partners Page
     *
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
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
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Magento_Marketplace::partners');
        $resultPage->addBreadcrumb(__('Partners'), __('Partners'));
        $resultPage->getConfig()->getTitle()->prepend(__('E-commerce Solutions'));

        return $resultPage;
    }

}
