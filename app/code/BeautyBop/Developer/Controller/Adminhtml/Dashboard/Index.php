<?php

namespace BeautyBop\Developer\Controller\Adminhtml\Dashboard;

use Magento\Backend\App\Action;
use Magento\Framework\View\Result\PageFactory;

class Index extends Action
{
    public const ADMIN_RESOURCE = 'BeautyBop_Developer::developer';

    public function __construct(
        Action\Context $context,
        private PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
    }

    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();

        $resultPage->setActiveMenu('BeautyBop_Developer::developer');
        $resultPage->getConfig()->getTitle()->prepend(__('BeautyBop Developer'));

        return $resultPage;
    }
}