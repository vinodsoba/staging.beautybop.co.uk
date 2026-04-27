<?php
namespace BeautyFort\BeautyfortProductImport\Controller\Adminhtml\Bulk;

use Magento\Backend\App\Action;
use Magento\Framework\Controller\Result\Redirect;

class Import extends Action
{
    const ADMIN_RESOURCE = 'BeautyFort_BeautyfortProductImport::bulk';

    protected $bulkImporter;

    public function __construct(
        Action\Context $context,
        \BeautyFort\BeautyfortProductImport\Model\BulkImporter $bulkImporter
    ) {
        parent::__construct($context);
        $this->bulkImporter = $bulkImporter;
    }

    public function execute()
    {
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();

        $data = $this->getRequest()->getPostValue();

        $skus = preg_split('/\r\n|\r|\n/', $data['skus'] ?? '');
        $skus = array_unique(array_filter(array_map('trim', $skus)));

        $categoryIds = $data['category_ids'] ?? [];
        $brand = trim($data['brand'] ?? '');

        if (empty($skus) && empty($brand)) {
            $this->messageManager->addErrorMessage(__('Please enter at least one SKU or a brand.'));
            return $resultRedirect->setPath('*/*/index');
        }

        try {

            if (!empty($brand)) {

                $results = $this->bulkImporter->importByBrand(
                    $brand,
                    $categoryIds
                );

            } else {

                $results = $this->bulkImporter->import(
                    $skus,
                    $categoryIds
                );
            }

            $imported = $results['imported'] ?? 0;

            $skipped = ($results['skipped_existing'] ?? 0)
                     + ($results['skipped_non_fragrance'] ?? 0);

            $items = $results['items'] ?? [];

            $successCount = 0;
            $failCount = 0;

            foreach ($items as $sku => $status) {

                if ($status === 'Imported') {

                    $successCount++;

                } elseif (strpos($status, 'Skipped') !== false) {

                    // already counted

                } else {

                    $failCount++;

                    $this->messageManager->addErrorMessage(
                        __('SKU %1 failed: %2', $sku, $status)
                    );
                }
            }

            $this->messageManager->addNoticeMessage(
                __('Test mode active: only 5 products imported.')
            );

            $this->messageManager->addSuccessMessage(
                __('Import complete: %1 imported, %2 skipped.', $imported, $skipped)
            );

            if ($failCount > 0) {
                $this->messageManager->addErrorMessage(
                    __('%1 product(s) failed to import.', $failCount)
                );
            }

        } catch (\Exception $e) {

            $this->messageManager->addErrorMessage(
                __('Import error: %1', $e->getMessage())
            );
        }

        return $resultRedirect->setPath('*/*/index');
    }
}