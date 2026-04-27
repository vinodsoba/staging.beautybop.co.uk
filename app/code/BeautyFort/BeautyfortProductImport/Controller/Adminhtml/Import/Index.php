<?php

namespace BeautyFort\BeautyfortProductImport\Controller\Adminhtml\Import;

use Magento\Backend\App\Action;
use Magento\Framework\Controller\Result\Redirect;

class Index extends Action
{
    const ADMIN_RESOURCE = 'BeautyFort_BeautyfortProductImport::import';

    public function execute()
    {
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();

        $sku = (string) $this->getRequest()->getParam('sku');

        // 🔥 PROOF CONTROLLER IS HIT
        $this->_objectManager
            ->get(\Psr\Log\LoggerInterface::class)
            ->info('IMPORT CONTROLLER HIT', ['sku' => $sku]);

        if ($sku === '') {
            $this->messageManager->addErrorMessage(__('Missing SKU'));
            return $resultRedirect->setPath('beautyfort/productimport/index');
        }

        $this->messageManager->addSuccessMessage(
            __('Import controller reached for SKU %1', $sku)
        );

        return $resultRedirect->setPath('beautyfort/productimport/index');
    }
}
