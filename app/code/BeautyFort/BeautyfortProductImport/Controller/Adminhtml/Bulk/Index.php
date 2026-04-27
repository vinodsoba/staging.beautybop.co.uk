<?php
namespace BeautyFort\BeautyfortProductImport\Controller\Adminhtml\Bulk;

use Magento\Backend\App\Action;
use Magento\Framework\View\Result\PageFactory;

class Index extends Action
{
    const ADMIN_RESOURCE = 'BeautyFort_BeautyfortProductImport::bulk';

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
        return $this->resultPageFactory->create();
    }
}