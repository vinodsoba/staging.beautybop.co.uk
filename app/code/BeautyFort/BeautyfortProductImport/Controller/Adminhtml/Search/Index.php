<?php
namespace BeautyFort\BeautyfortProductImport\Controller\Adminhtml\Search;

use Magento\Backend\App\Action;
use Magento\Framework\View\Result\PageFactory;

class Index extends Action
{
    const ADMIN_RESOURCE = 'BeautyFort_BeautyfortProductImport::search';
    
    protected $resultPageFactory;

    public function __construct(
        Action\Context $context,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }

    public function execute()
    {
        $page = $this->resultPageFactory->create();
        $page->setActiveMenu('BeautyFort_BeautyfortProductImport::search');
        $page->getConfig()->getTitle()->prepend(__('Beautyfort Product Search'));
        return $page;
    }
}
