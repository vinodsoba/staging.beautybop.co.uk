<?php

namespace BeautyFort\BeautyfortProductImport\Controller\Adminhtml\Search;

use Magento\Backend\App\Action;
use Magento\Framework\Controller\ResultFactory;
use BeautyFort\BeautyfortProductImport\Helper\Api;

class Result extends Action
{
    protected $api;

    public function __construct(
        Action\Context $context,
        Api $api
    ) {
        parent::__construct($context);
        $this->api = $api;
    }

    public function execute()
    {
        $sku = trim((string)$this->getRequest()->getParam('sku'));

        if ($sku === '') {
            $this->messageManager->addErrorMessage(__('Please enter a SKU.'));
            return $this->_redirect('*/*/index');
        }

        try {
            $items = $this->api->fetchProductBySku($sku);

            if (empty($items)) {
                $this->messageManager->addErrorMessage(
                    __('No Beautyfort product found for SKU "%1".', $sku)
                );
                return $this->_redirect('*/*/index');
            }

            // ✅ Safe to access
            $product = $items[0];

            /** @var \Magento\Framework\Controller\Result\Page $resultPage */
            $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
            $resultPage->getConfig()->getTitle()->prepend(__('Beautyfort Product Result'));

            // Optional: pass data to block
            // $resultPage->getLayout()
            //     ->getBlock('beautyfort.search.result')
            //     ->setProduct($product);

            return $resultPage;

        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(
                __('Beautyfort error: %1', $e->getMessage())
            );
            return $this->_redirect('*/*/index');
        }
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('BeautyFort_BeautyfortProductImport::search');
    }
}
