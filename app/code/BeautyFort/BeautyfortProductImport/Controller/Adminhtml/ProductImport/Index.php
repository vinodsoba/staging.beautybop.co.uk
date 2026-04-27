<?php

namespace BeautyFort\BeautyfortProductImport\Controller\Adminhtml\ProductImport;

use Magento\Backend\App\Action;
use Magento\Framework\View\Result\PageFactory;

class Index extends Action
{
    const ADMIN_RESOURCE = 'BeautyFort_BeautyfortProductImport::search';

    /**
     * @var PageFactory
     */
    private $resultPageFactory;

    public function __construct(
        Action\Context $context,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }

    public function execute()
    {
        /** @var \Magento\Framework\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();

        $resultPage->setActiveMenu(
            'BeautyFort_BeautyfortProductImport::search'
        );

        $resultPage->getConfig()->getTitle()->prepend(
            __('Beautyfort Product Import')
        );

        return $resultPage;
    }
}
