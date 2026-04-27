<?php
declare(strict_types=1);

namespace BeautyFort\BeautyfortProductImport\Controller\Adminhtml\Import;

use Magento\Backend\App\Action;
use Magento\Framework\Controller\Result\Redirect;
use BeautyFort\BeautyfortProductImport\Model\Importer\BulkImporter;
use BeautyFort\BeautyfortProductImport\Logger\Logger;

class Product extends Action
{
    const ADMIN_RESOURCE = 'BeautyFort_BeautyfortProductImport::import';

    /** @var BulkImporter */
    private  $bulkImporter;

    /** @var Logger */
    private $logger;

    public function __construct(
        Action\Context $context,
        BulkImporter $bulkImporter,
        Logger $logger
    ) {
        parent::__construct($context);
        $this->bulkImporter = $bulkImporter;
        $this->logger = $logger;
    }

    public function execute()
    {
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();

        $skusParam = (string)$this->getRequest()->getParam('sku');

        if ($skusParam === '') {
            $this->messageManager->addErrorMessage(__('No SKU provided'));
            return $resultRedirect->setPath('beautyfort/productimport/index');
        }

        $skus = array_filter(array_map('trim', explode(',', $skusParam)));

        $this->logger->info('🟢 IMPORT REQUEST', ['skus' => $skus]);

        $result = $this->bulkImporter->import($skus);

        $this->messageManager->addSuccessMessage(
            __('Imported %1 products (%2 failed)', $result['success'], $result['failed'])
        );

        return $resultRedirect->setPath('beautyfort/productimport/index');
    }
}
